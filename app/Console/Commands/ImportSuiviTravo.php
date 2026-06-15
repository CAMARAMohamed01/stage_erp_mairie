<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ImportSuiviTravo extends Command
{
    /**
     * Le nom et la signature de la commande.
     */
    protected $signature = 'import:suivitravo {filepath : Le chemin absolu vers le fichier CSV}';

    /**
     * La description de la commande.
     */
    protected $description = 'Importe et dédoublonne l\'historique des interventions bâtiments avec gestion stricte de l\'intégrité (PostgreSQL)';

    /**
     * Exécution de la commande.
     */
    public function handle()
    {
        $filepath = $this->argument('filepath');

        if (!file_exists($filepath)) {
            $this->error("Le fichier est introuvable : {$filepath}");
            return Command::FAILURE;
        }

        $this->info("Début de l'analyse du fichier : {$filepath}...");

        // Lecture du CSV
        $file = fopen($filepath, 'r');
        $headers = fgetcsv($file, 0, ';');

        // 1. Nettoyage du BOM UTF-8 (Caractère invisible qu'Excel met au tout début du fichier)
        $headers[0] = preg_replace('/[\xef\xbb\xbf]/', '', $headers[0]);

        // 2. Nettoyage et conversion d'encodage (pour gérer les "é" de l'export Excel Windows)
        $headers = array_map(function ($col) {
            return mb_convert_encoding(trim($col), 'UTF-8', 'Windows-1252');
        }, $headers);

        DB::beginTransaction();

        try {
            $compteurInserts = 0;
            $batimentsDB = DB::table('batiment')->get(['id_batiment', 'nom_bat']);
            $batimentsDico = [];
            foreach ($batimentsDB as $bat) {
                // Transforme "Bâtiment Jeunesse" en "batiment-jeunesse"
                $clefPropre = Str::slug($bat->nom_bat);
                $batimentsDico[$clefPropre] = $bat->id_batiment;
            }

            while (($data = fgetcsv($file, 0, ';')) !== false) {
                // Ignore les lignes vides ou mal formatées en fin de fichier
                if (count($headers) !== count($data)) {
                    continue;
                }

                // NOUVEAU : Conversion de CHAQUE cellule de la ligne en UTF-8
                $data = array_map(function ($cell) {
                    return mb_convert_encoding($cell, 'UTF-8', 'Windows-1252');
                }, $data);

                $row = array_combine($headers, $data);

                // --- 1. NETTOYAGE DES DONNÉES (DATA CLEANSING) ---
                $nomLieuDit = trim($row['nom_lieu_dit'] ?? '');
                $nomBatiment = trim($row['nom_bat'] ?? '');
                $initialesUser = trim($row['initiales'] ?? '');
                $categorieLibelle = trim($row['Categorie'] ?? '');

                // Parsing des dates (Supporte 'd/m/Y' et 'd/m/y')
                $dateOuverture = $this->parseDate($row['date_ouverture'] ?? '');
                $dateCloture = $this->parseDate($row['date_cloture'] ?? '');

                // Parsing du coût (sécurisé contre les variations d'encodage Excel)
                $valeurCout = $row['couts associés'] ?? $row['couts associes'] ?? $row['couts associs'] ?? $row['Couts associes'] ?? null;
                $coutAssocie = $this->parseCost($valeurCout);

                // --- 2. RÉSOLUTION DES DÉPENDANCES (UPSERT) ---

                // 2.A - Utilisateur (Émetteur/Demandeur)
                $idUser = DB::table('utilisateur')->where('initiales', $initialesUser)->value('id_user');
                if (!$idUser) {
                    $idUser = DB::table('utilisateur')->insertGetId([
                        'initiales' => substr($initialesUser, 0, 5),
                        'nom_user' => 'Inconnu (' . $initialesUser . ')',
                        'prenom_user' => 'Import',
                        'role_appli' => 'Agent technique',
                    ], 'id_user');
                }

                // 2.B - Catégorie
                $idCat = DB::table('categorie')->whereRaw('LOWER(libelle) = ?', [strtolower($categorieLibelle)])->value('id_cat');
                if (!$idCat && !empty($categorieLibelle)) {
                    $idCat = DB::table('categorie')->insertGetId(['libelle' => $categorieLibelle], 'id_cat');
                } elseif (!$idCat) {
                    $idCat = DB::table('categorie')->insertGetId(['libelle' => 'Non classé'], 'id_cat');
                }

                // 2.C - Lieu-dit
                $idLieuDit = DB::table('lieu_dit')->where('nom_lieu_dit', $nomLieuDit)->value('id_lieu_dit');
                if (!$idLieuDit) {
                    $idLieuDit = DB::table('lieu_dit')->insertGetId(['nom_lieu_dit' => $nomLieuDit ?: 'Inconnu'], 'id_lieu_dit');
                }

                // 2.D - Bâtiment (Création sécurisée avec coquilles vides pour contraintes NOT NULL)
                // 2.D - Bâtiment (Recherche par Slug sans accent ni espace)
                $idBatiment = null;
                if (!empty($nomBatiment)) {
                    // Nettoyage absolu du nom Excel
                    $clefRecherche = Str::slug($nomBatiment);

                    // On cherche dans notre dictionnaire en mémoire
                    $idBatiment = $batimentsDico[$clefRecherche] ?? null;

                    // S'il n'existe vraiment pas, on le crée
                    if (!$idBatiment) {
                        $idAdresse = DB::table('Adresse')->insertGetId([
                            'nom_voie' => 'Adresse à préciser',
                            'code_postal' => '74230',
                            'ville' => 'Dingy-Saint-Clair',
                            'id_lieu_dit' => $idLieuDit
                        ], 'id_adresse');

                        $idParcelle = DB::table('parcelle')->insertGetId([
                            'num_parcelle' => 'XXX',
                            'section_cadastrale' => 'X',
                            'id_lieu_dit' => $idLieuDit
                        ], 'id_parcelle');

                        $idTypeErp = DB::table('type_erp')->where('type_erp', 'NR')->value('id_type_erp');
                        if (!$idTypeErp) {
                            $idTypeErp = DB::table('type_erp')->insertGetId([
                                'reglementation_applicable' => 'Non renseignée',
                                'type_erp' => 'NR'
                            ], 'id_type_erp');
                        }

                        $idBatiment = DB::table('batiment')->insertGetId([
                            'nom_bat' => $nomBatiment, // On garde l'orthographe d'origine pour l'insertion
                            'id_parcelle' => $idParcelle,
                            'id_type_erp' => $idTypeErp,
                            'id_adresse' => $idAdresse
                        ], 'id_batiment');

                        // On met à jour notre dictionnaire en direct pour les prochaines lignes !
                        $batimentsDico[$clefRecherche] = $idBatiment;
                    }
                }

                // --- 3. CRÉATION DE L'ACTION (Le signalement parent) ---
                $description = trim($row['action'] ?? 'Action sans description');

                $actionExists = DB::table('action')
                    ->where('description', $description)
                    ->where('id_batiment', $idBatiment)
                    ->whereDate('date_creation', $dateOuverture ?: now())
                    ->first();

                $idAction = $actionExists ? $actionExists->id_action : DB::table('action')->insertGetId([
                    'date_creation' => $dateOuverture ?: now(),
                    'emetteur_nom' => 'Historique Excel',
                    'description' => $description,
                    'mode_reception' => 'Import SUIVI TRAVO',
                    'priorite' => !empty($row['priorite']) ? $row['priorite'] : 'Normale',
                    'statut_action' => trim($row['statut_global'] ?? 'En attente'),
                    'id_batiment' => $idBatiment,
                    'id_user' => $idUser,
                    'id_cat' => $idCat
                ], 'id_action');

                // --- 4. CRÉATION DE L'INTERVENTION ---
                $prochaineEtapeCSV = trim(($row['Prochaine étape/délai'] ?? '') . ' ' . ($row['prochaine étape'] ?? ''));

                DB::table('intervention')->updateOrInsert(
                    [
                        'description' => $description,
                        'id_batiment' => $idBatiment,
                        'date_ouverture' => $dateOuverture ?: now(),
                    ],
                    [
                        'code_budget' => trim($row['code_budget'] ?? ''),
                        'date_cloture' => $dateCloture,
                        'type_intervention' => $categorieLibelle ?: 'Maintenance générale',
                        'statut_global' => trim($row['statut_global'] ?? 'En attente'),
                        'Autre' => null, // La donnée ne va plus ici
                        'id_cat' => $idCat,
                        'id_user_demandeur' => $idUser,
                        'id_action' => $idAction
                    ]
                );
                // --- 5. LIAISON DU COÛT FINANCIER ET SUIVI ---
                $prochaineEtapeCSV = trim(($row['Prochaine étape/délai'] ?? '') . ' ' . ($row['prochaine étape'] ?? ''));
                $descriptionEtape = !empty($prochaineEtapeCSV) ? $prochaineEtapeCSV : 'Suivi importé depuis Excel';

                if ($coutAssocie > 0 || !empty($prochaineEtapeCSV)) {
                    $idIntervention = DB::table('intervention')->where('id_action', $idAction)->value('id_int');

                    if ($idIntervention) {
                        DB::table('suivi_action')->updateOrInsert(
                            [
                                'id_int' => $idIntervention,
                                'description_etape' => $descriptionEtape // Inséré en toute sécurité ici
                            ],
                            [
                                'date_action_suivi' => $dateCloture ?: ($dateOuverture ?: now()),
                                'cout_associe' => $coutAssocie,
                                'statut_apres_action' => trim($row['statut_global'] ?? 'En attente'),
                                'id_user' => $idUser
                            ]
                        );
                    }
                }

                $compteurInserts++;
            }

            DB::commit();
            fclose($file);

            $this->info("✅ Succès : {$compteurInserts} lignes historiques importées et liées sans corrompre la base de données.");
            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($file) && is_resource($file)) {
                fclose($file);
            }
            Log::error("Erreur ETL Bâtiments : " . $e->getMessage());
            $this->error("🛑 L'import a échoué. Annulation complète (Rollback). Erreur : " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Convertit une date format FR en format SQL Y-m-d.
     */
    private function parseDate($dateStr)
    {
        $dateStr = trim($dateStr ?? '');
        if (empty($dateStr))
            return null;

        try {
            return Carbon::createFromFormat('d/m/Y', str_replace('-', '/', $dateStr))->format('Y-m-d');
        } catch (\Exception $e) {
            try {
                return Carbon::createFromFormat('d/m/y', str_replace('-', '/', $dateStr))->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }
    }

    /**
     * Nettoie une chaîne de coût pour la base de données (ex: "3 700,00 €" -> 3700.00).
     */
    private function parseCost($costStr)
    {
        $costStr = trim($costStr ?? '');
        if (empty($costStr) || $costStr === '-' || str_contains($costStr, '-   €') || str_contains($costStr, '-   €')) {
            return 0.00;
        }

        $costStr = str_replace(',', '.', $costStr);
        $cleanCost = preg_replace('/[^0-9.]/', '', $costStr);

        return is_numeric($cleanCost) ? (float) $cleanCost : 0.00;
    }
}
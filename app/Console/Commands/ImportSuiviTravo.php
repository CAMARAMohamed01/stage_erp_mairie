<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ImportSuiviTravo extends Command
{
    protected $signature = 'import:suivitravo {filepath : Le chemin absolu vers le fichier CSV}';
    protected $description = 'Importe et dédoublonne l\'historique des interventions avec séparation stricte des étapes de suivi.';

    public function handle()
    {
        $filepath = $this->argument('filepath');

        if (!file_exists($filepath)) {
            $this->error("Le fichier est introuvable : {$filepath}");
            return Command::FAILURE;
        }

        $this->info("🚀 Début de l'analyse du fichier : {$filepath}...");

        $file = fopen($filepath, 'r');
        $rawHeaders = fgetcsv($file, 0, ';');

        // 1. Nettoyage extrême des en-têtes (BOM + Espaces + Minuscules + Accents)
        $headers = array_map(function ($col) {
            $col = preg_replace('/[\xef\xbb\xbf]/', '', $col);
            $col = mb_convert_encoding(trim($col), 'UTF-8', 'Windows-1252');
            return Str::slug($col, '_'); // Transforme " couts associés " en "couts_associes"
        }, $rawHeaders);

        DB::beginTransaction();

        try {
            $compteurInserts = 0;

            // Dictionnaire des bâtiments pour recherche rapide
            $batimentsDB = DB::table('batiment')->get(['id_batiment', 'nom_bat']);
            $batimentsDico = [];
            foreach ($batimentsDB as $bat) {
                $batimentsDico[Str::slug($bat->nom_bat)] = $bat->id_batiment;
            }

            while (($data = fgetcsv($file, 0, ';')) !== false) {
                if (count($headers) !== count($data))
                    continue;

                $data = array_map(function ($cell) {
                    return mb_convert_encoding(trim($cell), 'UTF-8', 'Windows-1252');
                }, $data);

                $row = array_combine($headers, $data);

                // --- 1. LECTURE & NETTOYAGE DES DONNÉES ---
                $nomLieuDit = $row['quartier'] ?? '';
                $nomBatiment = $row['nom_bat'] ?? '';
                $categorieLibelle = $row['categorie'] ?? '';
                $actionDesc = $row['action'] ?? 'Intervention sans description';

                $dateOuverture = $this->parseDate($row['date_ouverture'] ?? '');
                $dateCloture = $this->parseDate($row['date_cloture'] ?? '');
                $coutAssocie = $this->parseCost($row['couts_associes'] ?? null);
                $statutGlobal = $row['statut_global'] ?? 'En attente';

                // Gestion stricte des initiales par défaut
                $initialesUser = $row['initiales'] ?? '';
                if (empty($initialesUser)) {
                    $initialesUser = 'PYP';
                }

                // --- 2. RÉSOLUTION DES DÉPENDANCES ---

                // A. Utilisateur
                $idUser = DB::table('utilisateur')->where('initiales', $initialesUser)->value('id_user');
                if (!$idUser) {
                    $idUser = DB::table('utilisateur')->insertGetId([
                        'initiales' => substr($initialesUser, 0, 5),
                        'nom_user' => 'Agent (' . $initialesUser . ')',
                        'prenom_user' => 'Import',
                        'role_appli' => 'Agent technique',
                    ], 'id_user');
                }

                // B. Catégorie
                $idCat = DB::table('categorie')->whereRaw('LOWER(libelle) = ?', [strtolower($categorieLibelle)])->value('id_cat');
                if (!$idCat && !empty($categorieLibelle)) {
                    $idCat = DB::table('categorie')->insertGetId(['libelle' => $categorieLibelle], 'id_cat');
                } elseif (!$idCat) {
                    $idCat = DB::table('categorie')->insertGetId(['libelle' => 'Non classé'], 'id_cat');
                }

                // C. Bâtiment (Recherche intelligente)
                $idBatiment = null;
                if (!empty($nomBatiment)) {
                    $idBatiment = $batimentsDico[Str::slug($nomBatiment)] ?? null;

                    if (!$idBatiment) {
                        $match = DB::table('batiment')->where('nom_bat', 'ILIKE', '%' . $nomBatiment . '%')->first();
                        if ($match) {
                            $idBatiment = $match->id_batiment;
                        } else {
                            $actionDesc = "[Lieu CSV: {$nomBatiment}] " . $actionDesc;
                        }
                    }
                }

                // --- 3. CRÉATION DE L'ACTION ---
                $actionExists = DB::table('action')
                    ->where('description', $actionDesc)
                    ->where('id_batiment', $idBatiment)
                    ->whereDate('date_creation', $dateOuverture ?: now())
                    ->first();

                $idAction = $actionExists ? $actionExists->id_action : DB::table('action')->insertGetId([
                    'date_creation' => $dateOuverture ?: now(),
                    'emetteur_nom' => 'Historique Excel',
                    'description' => $actionDesc,
                    'mode_reception' => 'Import SUIVI TRAVO',
                    'priorite' => !empty($row['priorite']) ? $row['priorite'] : 'Normale',
                    'statut_action' => $statutGlobal,
                    'id_batiment' => $idBatiment,
                    'id_user' => $idUser,
                    'id_cat' => $idCat
                ], 'id_action');

                // --- 4. CRÉATION DE L'INTERVENTION ---
                $idIntervention = DB::table('intervention')->where('id_action', $idAction)->value('id_int');

                if (!$idIntervention) {
                    $idIntervention = DB::table('intervention')->insertGetId([
                        'description' => $actionDesc,
                        'id_batiment' => $idBatiment,
                        'date_ouverture' => $dateOuverture ?: now(),
                        'code_budget' => $row['code_budget'] ?? '',
                        'date_cloture' => $dateCloture,
                        'type_intervention' => $categorieLibelle ?: 'Maintenance générale',
                        'statut_global' => $statutGlobal,
                        'id_cat' => $idCat,
                        'id_user_demandeur' => $idUser,
                        'id_action' => $idAction
                    ], 'id_int');
                }

                // --- 5. SUIVI ET FINANCES EN DEUX ÉTAPES DISTINCTES ---

                $etape1 = trim($row['prochaine_etape_delai'] ?? '');
                $etape2 = trim($row['prochaine_etape'] ?? '');

                // ÉTAPE 1 : Le premier passage (porte le coût financier)
                if ($coutAssocie > 0 || !empty($etape1) || $statutGlobal === 'Terminé') {
                    DB::table('suivi_action')->updateOrInsert(
                        [
                            'id_int' => $idIntervention,
                            'description_etape' => !empty($etape1) ? $etape1 : 'Clôture / Premier suivi importé'
                        ],
                        [
                            'date_action_suivi' => $dateCloture ?: ($dateOuverture ?: now()),
                            'cout_associe' => $coutAssocie,
                            'statut_apres_action' => !empty($etape2) ? 'En cours' : $statutGlobal, // Si étape 2 existe, l'étape 1 n'est probablement pas la fin
                            'id_user' => $idUser
                        ]
                    );
                }

                // ÉTAPE 2 : Le second passage (ne porte aucun coût pour éviter les doublons comptables)
                if (!empty($etape2)) {
                    DB::table('suivi_action')->updateOrInsert(
                        [
                            'id_int' => $idIntervention,
                            'description_etape' => $etape2
                        ],
                        [
                            'date_action_suivi' => $dateCloture ?: ($dateOuverture ?: now()), // On réutilise la date connue
                            'cout_associe' => 0.00, // Sécurité financière
                            'statut_apres_action' => $statutGlobal, // Cette fois, c'est bien le statut final
                            'id_user' => $idUser
                        ]
                    );
                }

                $compteurInserts++;
            }

            DB::commit();
            fclose($file);

            $this->info("✅ Succès : {$compteurInserts} lignes importées. Les étapes multiples ont été séparées en interventions distinctes.");
            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($file) && is_resource($file))
                fclose($file);
            Log::error("Erreur ETL Suivi Travo : " . $e->getMessage());
            $this->error("🛑 L'import a échoué. Annulation complète. Erreur : " . $e->getMessage());
            return Command::FAILURE;
        }
    }

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

    private function parseCost($costStr)
    {
        $costStr = trim($costStr ?? '');
        if (empty($costStr) || $costStr === '-')
            return 0.00;

        $costStr = str_replace([',', ' ', '€'], ['.', '', ''], $costStr);
        $cleanCost = preg_replace('/[^0-9.\-]/', '', $costStr);

        return is_numeric($cleanCost) ? (float) $cleanCost : 0.00;
    }
}
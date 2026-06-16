<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ImportLocaux extends Command
{
    protected $signature = 'import:locaux {filepath : Le chemin absolu vers le fichier CSV}';
    protected $description = 'Importe l\'historique des interventions sur les Locaux (Pièces) (V1)';

    public function handle()
    {
        $filepath = $this->argument('filepath');

        if (!file_exists($filepath)) {
            $this->error("Le fichier est introuvable : {$filepath}");
            return Command::FAILURE;
        }

        $this->info("Début de l'analyse du fichier Locaux : {$filepath}...");

        $file = fopen($filepath, 'r');
        $headers = fgetcsv($file, 0, ';');

        $headers[0] = preg_replace('/[\xef\xbb\xbf]/', '', $headers[0]);
        $headers = array_map(function ($col) {
            return mb_convert_encoding(trim($col), 'UTF-8', 'Windows-1252');
        }, $headers);

        DB::beginTransaction();

        try {
            $compteurInserts = 0;

            // CHARGEMENT DES DICTIONNAIRES (Bâtiments et Lieux Publics)
            $batimentsDico = [];
            foreach (DB::table('batiment')->get(['id_batiment', 'nom_bat']) as $bat) {
                $batimentsDico[Str::slug($bat->nom_bat)] = $bat->id_batiment;
            }

            $lieuxDico = [];
            foreach (DB::table('lieux_publics')->get(['id_lieu', 'nom_lieu']) as $lieu) {
                $lieuxDico[Str::slug($lieu->nom_lieu)] = $lieu->id_lieu;
            }

            while (($data = fgetcsv($file, 0, ';')) !== false) {
                if (count($headers) !== count($data))
                    continue;

                $data = array_map(function ($cell) {
                    return mb_convert_encoding($cell, 'UTF-8', 'Windows-1252');
                }, $data);

                $row = array_combine($headers, $data);

                // --- 1. LECTURE INTELLIGENTE DES COLONNES ---
                $nomParent = null;
                $nomLocal = null;

                // On fouille dynamiquement pour contrer les caprices d'Excel sur "nom_bat/lieu public"
                foreach ($row as $key => $val) {
                    $slugKey = Str::slug($key);
                    if (str_contains($slugKey, 'bat') || str_contains($slugKey, 'lieu-public')) {
                        $nomParent = trim($val);
                    }
                    if (str_contains($slugKey, 'local')) {
                        $nomLocal = trim($val);
                    }
                }

                $initialesUser = trim($row['initiales'] ?? '');
                $categorieLibelle = trim($row['Categorie'] ?? '');
                $dateOuverture = $this->parseDate($row['date_ouverture'] ?? '');
                $dateCloture = $this->parseDate($row['date_cloture'] ?? '');
                $valeurCout = $row['couts associés'] ?? $row['couts associes'] ?? null;
                $coutAssocie = $this->parseCost($valeurCout);

                // --- 2. RÉSOLUTION DES DÉPENDANCES DE BASE ---
                $idUser = DB::table('utilisateur')->where('initiales', $initialesUser)->value('id_user');
                if (!$idUser) {
                    $idUser = DB::table('utilisateur')->insertGetId([
                        'initiales' => substr($initialesUser, 0, 5),
                        'nom_user' => 'Inconnu (' . $initialesUser . ')',
                        'prenom_user' => 'Import',
                        'role_appli' => 'Agent technique',
                    ], 'id_user');
                }

                $idCat = DB::table('categorie')->whereRaw('LOWER(libelle) = ?', [strtolower($categorieLibelle)])->value('id_cat');
                if (!$idCat && !empty($categorieLibelle)) {
                    $idCat = DB::table('categorie')->insertGetId(['libelle' => $categorieLibelle], 'id_cat');
                } elseif (!$idCat) {
                    $idCat = DB::table('categorie')->insertGetId(['libelle' => 'Non classé'], 'id_cat');
                }

                // --- 3. RECHERCHE DU PARENT (Bâtiment ou Espace) ---
                $idBatiment = null;
                $idLieuPublic = null;
                if (!empty($nomParent)) {
                    $clefParent = Str::slug($nomParent);
                    // On vérifie d'abord si c'est un bâtiment
                    if (isset($batimentsDico[$clefParent])) {
                        $idBatiment = $batimentsDico[$clefParent];
                    }
                    // Sinon, est-ce un lieu public ?
                    elseif (isset($lieuxDico[$clefParent])) {
                        $idLieuPublic = $lieuxDico[$clefParent];
                    }
                }

                // --- 4. GESTION DU LOCAL ---
                $idLocal = null;
                if (!empty($nomLocal)) {
                    // On cherche si ce local existe déjà (en filtrant par parent si connu)
                    $queryLocal = DB::table('local_')->whereRaw('LOWER(nom_local) = ?', [strtolower($nomLocal)]);
                    if ($idBatiment)
                        $queryLocal->where('id_batiment', $idBatiment);
                    elseif ($idLieuPublic)
                        $queryLocal->where('id_lieu', $idLieuPublic);

                    $localTrouve = $queryLocal->first();

                    if ($localTrouve) {
                        $idLocal = $localTrouve->id_local;
                    } else {
                        // S'il n'existe pas, c'est super simple, on le crée !
                        $idLocal = DB::table('local_')->insertGetId([
                            'nom_local' => $nomLocal,
                            'id_batiment' => $idBatiment,
                            'id_lieu' => $idLieuPublic
                        ], 'id_local');
                    }
                }

                // --- 5. CRÉATION DE L'ACTION ---
                $description = trim($row['action'] ?? 'Action sans description');

                $actionExists = DB::table('action')
                    ->where('description', $description)
                    ->where('id_local', $idLocal)
                    ->whereDate('date_creation', $dateOuverture ?: now())
                    ->first();

                $idAction = $actionExists ? $actionExists->id_action : DB::table('action')->insertGetId([
                    'date_creation' => $dateOuverture ?: now(),
                    'emetteur_nom' => 'Historique Excel',
                    'description' => $description,
                    'mode_reception' => 'Import_Locaux_V1', // 🏷️ TAG DE LOT UNIQUE
                    'priorite' => !empty($row['priorite']) ? $row['priorite'] : 'Normale',
                    'statut_action' => trim($row['statut_global'] ?? 'En attente'),
                    'id_local' => $idLocal,
                    'id_batiment' => $idBatiment, // On remonte l'info parent si existante
                    'id_lieu' => $idLieuPublic,   // On remonte l'info parent si existante
                    'id_user' => $idUser,
                    'id_cat' => $idCat
                ], 'id_action');

                // --- 6. CRÉATION DE L'INTERVENTION ---
                DB::table('intervention')->updateOrInsert(
                    [
                        'description' => $description,
                        'date_ouverture' => $dateOuverture ?: now(),
                        'id_action' => $idAction
                    ],
                    [
                        'code_budget' => trim($row['code_budget'] ?? ''),
                        'date_cloture' => $dateCloture,
                        'type_intervention' => $categorieLibelle ?: 'Maintenance locale',
                        'statut_global' => trim($row['statut_global'] ?? 'En attente'),
                        'id_cat' => $idCat,
                        'id_user_demandeur' => $idUser,
                        'id_local' => $idLocal,
                        'id_batiment' => $idBatiment,
                        'id_lieu' => $idLieuPublic
                    ]
                );

                $idIntervention = DB::table('intervention')->where('id_action', $idAction)->value('id_int');

                // --- 7. SUIVI ---
                if ($idIntervention) {
                    $etape1 = trim($row['Prochaine étape/délai'] ?? '');
                    $etape2 = trim($row['prochaine étape'] ?? '');

                    // Enregistrement 1 : Toujours créé s'il y a du texte dans la 1ère colonne OU s'il y a un coût
                    if (!empty($etape1) || $coutAssocie > 0) {
                        $descriptionEtape1 = !empty($etape1) ? $etape1 : 'Suivi importé depuis Excel';

                        DB::table('suivi_action')->updateOrInsert(
                            [
                                'id_int' => $idIntervention,
                                'description_etape' => $descriptionEtape1
                            ],
                            [
                                'date_action_suivi' => $dateCloture ?: ($dateOuverture ?: now()),
                                'cout_associe' => $coutAssocie, // Le coût est imputé sur l'étape principale
                                'statut_apres_action' => trim($row['statut_global'] ?? 'En attente'),
                                'id_user' => $idUser
                            ]
                        );
                    }

                    // Enregistrement 2 : Créé UNIQUEMENT si la 2ème colonne "prochaine étape" n'est pas vide
                    if (!empty($etape2)) {
                        DB::table('suivi_action')->updateOrInsert(
                            [
                                'id_int' => $idIntervention,
                                'description_etape' => $etape2 // Devient une nouvelle ligne d'historique propre
                            ],
                            [
                                'date_action_suivi' => $dateCloture ?: ($dateOuverture ?: now()),
                                'cout_associe' => 0.00, // Coût déjà enregistré sur la première ligne
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

            $this->info("✅ Succès complet : {$compteurInserts} lignes de Locaux importées et liées.");
            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($file) && is_resource($file))
                fclose($file);
            Log::error("Erreur ETL Locaux : " . $e->getMessage());
            $this->error("🛑 L'import a échoué. Erreur : " . $e->getMessage());
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
        if (empty($costStr) || $costStr === '-' || str_contains($costStr, '-   €'))
            return 0.00;
        $costStr = str_replace(',', '.', $costStr);
        $cleanCost = preg_replace('/[^0-9.]/', '', $costStr);
        return is_numeric($cleanCost) ? (float) $cleanCost : 0.00;
    }
}
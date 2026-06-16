<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ImportEspacesPublics extends Command
{
    protected $signature = 'import:espacespublics {filepath : Le chemin absolu vers le fichier CSV}';
    protected $description = 'Importe l\'historique des Espaces Publics en liant uniquement aux lieux existants (V4)';

    public function handle()
    {
        $filepath = $this->argument('filepath');

        if (!file_exists($filepath)) {
            $this->error("Le fichier est introuvable : {$filepath}");
            return Command::FAILURE;
        }

        $this->info("Début de l'analyse du fichier Espaces Publics : {$filepath}...");

        $file = fopen($filepath, 'r');
        $headers = fgetcsv($file, 0, ';');

        $headers[0] = preg_replace('/[\xef\xbb\xbf]/', '', $headers[0]);
        $headers = array_map(function ($col) {
            return mb_convert_encoding(trim($col), 'UTF-8', 'Windows-1252');
        }, $headers);

        DB::beginTransaction();

        try {
            $compteurInserts = 0;

            // CHARGEMENT DES LIEUX OFFICIELS DE TA BASE
            $lieuxDB = DB::table('lieux_publics')->get(['id_lieu', 'nom_lieu']);
            $lieuxDico = [];
            foreach ($lieuxDB as $lieu) {
                $lieuxDico[Str::slug($lieu->nom_lieu)] = $lieu->id_lieu;
            }

            while (($data = fgetcsv($file, 0, ';')) !== false) {
                if (count($headers) !== count($data))
                    continue;

                $data = array_map(function ($cell) {
                    return mb_convert_encoding($cell, 'UTF-8', 'Windows-1252');
                }, $data);

                $row = array_combine($headers, $data);

                // LECTURE
                $nomLieuDit = trim($row['nom_lieu_dit'] ?? '');
                $nomEspacePublic = trim($row['espaces public'] ?? $row['espaces publics'] ?? '');
                $initialesUser = trim($row['initiales'] ?? '');
                $categorieLibelle = trim($row['Categorie'] ?? '');
                $dateOuverture = $this->parseDate($row['date_ouverture'] ?? '');
                $dateCloture = $this->parseDate($row['date_cloture'] ?? '');
                $valeurCout = $row['couts associés'] ?? $row['couts associes'] ?? null;
                $coutAssocie = $this->parseCost($valeurCout);

                // RÉSOLUTION DÉPENDANCES
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

                $idLieuDit = DB::table('lieu_dit')->where('nom_lieu_dit', $nomLieuDit)->value('id_lieu_dit');
                if (!$idLieuDit && !empty($nomLieuDit)) {
                    $idLieuDit = DB::table('lieu_dit')->insertGetId(['nom_lieu_dit' => $nomLieuDit], 'id_lieu_dit');
                }

                // 🌟 LIAISON ESPACE PUBLIC STRICTE
                $idLieuPublic = null;
                if (!empty($nomEspacePublic)) {
                    $clefRecherche = Str::slug($nomEspacePublic);
                    $idLieuPublic = $lieuxDico[$clefRecherche] ?? null;
                }

                // CRÉATION ACTION
                $description = trim($row['action'] ?? 'Action sans description');
                $actionExists = DB::table('action')
                    ->where('description', $description)
                    ->where('id_lieu', $idLieuPublic)
                    ->whereDate('date_creation', $dateOuverture ?: now())
                    ->first();

                $idAction = $actionExists ? $actionExists->id_action : DB::table('action')->insertGetId([
                    'date_creation' => $dateOuverture ?: now(),
                    'emetteur_nom' => 'Historique Excel',
                    'description' => $description,
                    'mode_reception' => 'Import_Espaces_Publics_V4',
                    'priorite' => !empty($row['priorite']) ? $row['priorite'] : 'Normale',
                    'statut_action' => trim($row['statut_global'] ?? 'En attente'),
                    'id_lieu' => $idLieuPublic,
                    'id_user' => $idUser,
                    'id_cat' => $idCat
                ], 'id_action');

                // CRÉATION INTERVENTION
                DB::table('intervention')->updateOrInsert(
                    [
                        'description' => $description,
                        'date_ouverture' => $dateOuverture ?: now(),
                        'id_action' => $idAction
                    ],
                    [
                        'code_budget' => trim($row['code_budget'] ?? ''),
                        'date_cloture' => $dateCloture,
                        'type_intervention' => $categorieLibelle ?: 'Entretien extérieur',
                        'statut_global' => trim($row['statut_global'] ?? 'En attente'),
                        'Autre' => null,
                        'id_cat' => $idCat,
                        'id_user_demandeur' => $idUser,
                        'id_lieu' => $idLieuPublic
                    ]
                );

                $idIntervention = DB::table('intervention')->where('id_action', $idAction)->value('id_int');

                // SUIVI
                $prochaineEtapeCSV = trim(($row['Prochaine étape/délai'] ?? '') . ' ' . ($row['prochaine étape'] ?? ''));
                $descriptionEtape = !empty($prochaineEtapeCSV) ? $prochaineEtapeCSV : 'Suivi importé depuis Excel';

                if ($coutAssocie > 0 || !empty($prochaineEtapeCSV)) {
                    if ($idIntervention) {
                        DB::table('suivi_action')->updateOrInsert(
                            ['id_int' => $idIntervention, 'description_etape' => $descriptionEtape],
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

            $this->info("✅ Succès : {$compteurInserts} lignes importées SANS créer de faux lieux publics.");
            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($file) && is_resource($file))
                fclose($file);
            Log::error("Erreur ETL : " . $e->getMessage());
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
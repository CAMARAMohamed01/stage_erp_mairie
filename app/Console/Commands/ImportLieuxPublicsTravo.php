<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ImportLieuxPublicsTravo extends Command
{
    protected $signature = 'import:lieuxpublics-travo {filepath : Le chemin absolu vers le fichier CSV}';
    protected $description = 'Importe l\'historique des interventions liées aux Lieux Publics avec liaison des Lieux-Dits.';

    public function handle()
    {
        $filepath = $this->argument('filepath');

        if (!file_exists($filepath)) {
            $this->error("Le fichier est introuvable : {$filepath}");
            return Command::FAILURE;
        }

        $this->info("🚀 Début de l'analyse du fichier des Lieux Publics : {$filepath}...");

        $file = fopen($filepath, 'r');
        $rawHeaders = fgetcsv($file, 0, ';');

        // 1. Nettoyage extrême des en-têtes (BOM + Espaces + Minuscules + Accents)
        $headers = array_map(function ($col) {
            $col = preg_replace('/[\xef\xbb\xbf]/', '', $col);
            $col = mb_convert_encoding(trim($col), 'UTF-8', 'Windows-1252');
            return Str::slug($col, '_'); // Ex: "lieu public" devient "lieu_public"
        }, $rawHeaders);

        DB::beginTransaction();

        try {
            $compteurInserts = 0;

            // Dictionnaire des Lieux Publics pour recherche rapide
            $lieuxPublicsDB = DB::table('lieux_publics')->get(['id_lieu', 'nom_lieu']);
            $lieuxPublicsDico = [];
            foreach ($lieuxPublicsDB as $lp) {
                $lieuxPublicsDico[Str::slug($lp->nom_lieu)] = $lp->id_lieu;
            }

            while (($data = fgetcsv($file, 0, ';')) !== false) {
                if (count($headers) !== count($data))
                    continue;

                $data = array_map(function ($cell) {
                    return mb_convert_encoding(trim($cell), 'UTF-8', 'Windows-1252');
                }, $data);

                $row = array_combine($headers, $data);

                // --- 1. LECTURE & NETTOYAGE DES DONNÉES ---
                $nomQuartier = $row['quartier'] ?? '';
                $nomLieuPublic = $row['lieu_public'] ?? '';
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

                // C. Lieu-Dit (Le Quartier) - Très important pour la table action !
                $idLieuDit = null;
                if (!empty($nomQuartier)) {
                    $idLieuDit = DB::table('lieu_dit')->where('nom_lieu_dit', 'ILIKE', trim($nomQuartier))->value('id_lieu_dit');
                    if (!$idLieuDit) {
                        // S'il n'existe pas encore en base, on le crée proprement
                        $idLieuDit = DB::table('lieu_dit')->insertGetId(['nom_lieu_dit' => trim($nomQuartier)], 'id_lieu_dit');
                    }
                }

                // D. Lieu Public (Recherche intelligente)
                $idLieu = null;
                if (!empty($nomLieuPublic)) {
                    $idLieu = $lieuxPublicsDico[Str::slug($nomLieuPublic)] ?? null;

                    if (!$idLieu) {
                        $match = DB::table('lieux_publics')->where('nom_lieu', 'ILIKE', '%' . trim($nomLieuPublic) . '%')->first();
                        if ($match) {
                            $idLieu = $match->id_lieu;
                        } else {
                            $actionDesc = "[Lieu Public CSV: {$nomLieuPublic}] " . $actionDesc;
                        }
                    }
                }

                // --- 3. CRÉATION DE L'ACTION ---
                $actionExists = DB::table('action')
                    ->where('description', $actionDesc)
                    ->where('id_lieu', $idLieu) // On cible id_lieu ici
                    ->whereDate('date_creation', $dateOuverture ?: now())
                    ->first();

                $idAction = $actionExists ? $actionExists->id_action : DB::table('action')->insertGetId([
                    'date_creation' => $dateOuverture ?: now(),
                    'emetteur_nom' => 'Historique Excel',
                    'description' => $actionDesc,
                    'mode_reception' => 'Import SUIVI TRAVO',
                    'priorite' => !empty($row['priorite']) ? $row['priorite'] : 'Normale',
                    'statut_action' => $statutGlobal,
                    'id_lieu' => $idLieu, // Cible la table lieux_publics
                    'id_lieu_dit' => $idLieuDit, // Rattachement géographique du quartier
                    'id_user' => $idUser,
                    'id_cat' => $idCat
                ], 'id_action');

                // --- 4. CRÉATION DE L'INTERVENTION ---
                $idIntervention = DB::table('intervention')->where('id_action', $idAction)->value('id_int');

                if (!$idIntervention) {
                    $idIntervention = DB::table('intervention')->insertGetId([
                        'description' => $actionDesc,
                        'id_lieu' => $idLieu, // On cible id_lieu ici aussi
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
                // --- 5. SUIVI ET FINANCES EN DEUX ÉTAPES DISTINCTES ---

                $etape1 = '';
                $etape2 = '';

                // Recherche dynamique des clés (immunise contre les espaces invisibles d'Excel ou le slash)
                foreach ($row as $key => $value) {
                    if (str_contains($key, 'delai')) {
                        $etape1 = trim($value);
                    } elseif (str_contains($key, 'etape') && !str_contains($key, 'delai')) {
                        $etape2 = trim($value);
                    }
                }

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

            $this->info("✅ Succès : {$compteurInserts} lignes importées pour les lieux publics.");
            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($file) && is_resource($file))
                fclose($file);
            Log::error("Erreur ETL Lieux Publics : " . $e->getMessage());
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
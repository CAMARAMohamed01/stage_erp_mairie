<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ImportInterventionsProjetTravo extends Command
{
    protected $signature = 'import:interventions-projet-travo {filepath : Le chemin absolu vers le fichier CSV}';
    protected $description = 'Importe l\'historique des interventions et les relie aux projets, bâtiments et lieux publics.';

    public function handle()
    {
        $filepath = $this->argument('filepath');

        if (!file_exists($filepath)) {
            $this->error("Le fichier est introuvable : {$filepath}");
            return Command::FAILURE;
        }

        $this->info("🚀 Analyse du fichier des Interventions : {$filepath}...");

        $file = fopen($filepath, 'r');
        $rawHeaders = fgetcsv($file, 0, ';');

        // Nettoyage des en-têtes
        $headers = array_map(function ($col) {
            $col = preg_replace('/[\xef\xbb\xbf]/', '', $col);
            $col = mb_convert_encoding(trim($col), 'UTF-8', 'Windows-1252');
            return Str::slug($col, '_');
        }, $rawHeaders);

        DB::beginTransaction();

        try {
            $compteurInterventions = 0;
            $categoriesCrees = 0;

            // Dictionnaires pour éviter les requêtes répétitives
            $utilisateurs = DB::table('utilisateur')->pluck('id_user', 'initiales')->toArray();

            while (($data = fgetcsv($file, 0, ';')) !== false) {
                if (count($headers) !== count($data))
                    continue;

                $data = array_map(function ($cell) {
                    return mb_convert_encoding(trim($cell), 'UTF-8', 'Windows-1252');
                }, $data);

                $row = array_combine($headers, $data);

                // --- 1. RECHERCHE DES CLÉS ÉTRANGÈRES ---

                // Projet
                $nomProjet = trim($row['nom_projet'] ?? '');
                $idProjet = null;
                if (!empty($nomProjet)) {
                    $idProjet = DB::table('projet')->where('nom_projet', 'ILIKE', '%' . $nomProjet . '%')->value('id_projet');
                }

                // Bâtiment (Avec dictionnaire d'alias)
                $nomBatiment = '';
                foreach ($row as $key => $value) {
                    if (str_contains($key, 'batiment')) {
                        $nomBatiment = trim($value);
                        break;
                    }
                }
                $idBatiment = null;
                if (!empty($nomBatiment)) {
                    $aliases = [
                        'espace sportif et associatif' => 'EAS',
                        'ecole maurice anjot' => 'École Maurice Anjot',
                    ];
                    $nomRecherche = array_key_exists(strtolower($nomBatiment), $aliases) ? $aliases[strtolower($nomBatiment)] : $nomBatiment;
                    $idBatiment = DB::table('batiment')->where('nom_bat', 'ILIKE', '%' . $nomRecherche . '%')->value('id_batiment');
                }

                // Lieu Public
                $nomLieuPublic = trim($row['lieu_public'] ?? '');
                $idLieu = null;
                if (!empty($nomLieuPublic)) {
                    $idLieu = DB::table('lieux_publics')->where('nom_lieu', 'ILIKE', '%' . $nomLieuPublic . '%')->value('id_lieu');
                }

                // Catégorie (Création à la volée car NOT NULL dans ta BDD)
                $nomCategorie = trim($row['categorie'] ?? '');
                if (empty($nomCategorie))
                    $nomCategorie = 'Non définie';

                $idCat = DB::table('categorie')->where('libelle', 'ILIKE', $nomCategorie)->value('id_cat');
                if (!$idCat) {
                    $idCat = DB::table('categorie')->insertGetId(['libelle' => $nomCategorie], 'id_cat');
                    $categoriesCrees++;
                }

                // Utilisateur Demandeur
                $initiales = trim($row['initiales'] ?? '');
                $idUser = $utilisateurs[$initiales] ?? null;


                // --- 2. FORMATAGE DES DONNÉES DE L'INTERVENTION ---

                // Dates
                $dateOuverture = $this->parseDate($row['date'] ?? '') ?? Carbon::now()->format('Y-m-d');
                $dateCloture = $this->parseDate($row['date_cloture'] ?? '');

                // Coût estimé (utilisé dans la description)
                $montant = trim($row['montant_ttc'] ?? '');
                $coutStr = (!empty($montant) && $montant !== '-') ? "\n[Coût estimé / devis : " . $montant . "]" : "";

                // NOUVEAU : Capture du quartier et de la voie (ajoutés à la description car pas de FK directe dans 'intervention')
                $nomQuartier = trim($row['quartier'] ?? '');
                $nomVoie = trim($row['nom_voie'] ?? '');
                $contexteGeo = [];
                if (!empty($nomQuartier))
                    $contexteGeo[] = "Quartier : " . $nomQuartier;
                if (!empty($nomVoie))
                    $contexteGeo[] = "Voie : " . $nomVoie;
                $geoStr = !empty($contexteGeo) ? "\n📍 [" . implode(' | ', $contexteGeo) . "]" : "";

                // Statut
                $statutGlobal = trim($row['statut_global'] ?? '');
                if (empty($statutGlobal))
                    $statutGlobal = 'Enregistré';

                // Collecte des interventions distinctes (colonnes 1 à 4)
                $interventionsToCreate = [];
                $int1 = trim($row['intervention_1_liee'] ?? '');
                $int2 = trim($row['intervention_2_liee'] ?? '');
                $int3 = trim($row['intervention_3_liee'] ?? '');
                $int4 = trim($row['intervention_4_liee'] ?? '');

                if (!empty($int1))
                    $interventionsToCreate[] = $int1;
                if (!empty($int2))
                    $interventionsToCreate[] = $int2;
                if (!empty($int3))
                    $interventionsToCreate[] = $int3;
                if (!empty($int4))
                    $interventionsToCreate[] = $int4;

                // Fallback de sécurité : s'il n'y a rien dans les colonnes 1 à 4, on utilise le nom du projet
                if (empty($interventionsToCreate)) {
                    $interventionsToCreate[] = !empty($nomProjet) ? $nomProjet : 'Intervention diverse';
                }

                // --- 3. INSERTION EN BASE (Boucle sur les interventions) ---
                foreach ($interventionsToCreate as $intText) {
                    $typeIntervention = Str::limit($intText, 145);
                    $description = $intText . $coutStr . $geoStr . "\n[TAG: PROJET]";

                    DB::table('intervention')->insert([
                        'date_ouverture' => $dateOuverture,
                        'date_cloture' => $dateCloture,
                        'type_intervention' => $typeIntervention,
                        'statut_global' => Str::limit($statutGlobal, 50),
                        'description' => $description,
                        'id_projet' => $idProjet,
                        'id_batiment' => $idBatiment,
                        'id_lieu' => $idLieu,
                        'id_cat' => $idCat,
                        'id_user_demandeur' => $idUser,
                    ]);

                    $compteurInterventions++;
                }
            }

            fclose($file);
            DB::commit();

            $this->info("✅ Succès : {$compteurInterventions} interventions ont été créées !");
            if ($categoriesCrees > 0) {
                $this->info("💡 {$categoriesCrees} nouvelles catégories ont été générées automatiquement.");
            }
            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($file) && is_resource($file))
                fclose($file);
            Log::error("Erreur ETL Interventions : " . $e->getMessage());
            $this->error("🛑 L'import a échoué. Erreur : " . $e->getMessage() . " à la ligne " . $e->getLine());
            return Command::FAILURE;
        }
    }

    private function parseDate($dateStr)
    {
        $dateStr = trim($dateStr);
        if (empty($dateStr))
            return null;

        try {
            $dateStr = str_replace('-', '/', $dateStr);
            return Carbon::createFromFormat('d/m/Y', $dateStr)->format('Y-m-d');
        } catch (\Exception $e) {
            try {
                return Carbon::createFromFormat('d/m/y', $dateStr)->format('Y-m-d');
            } catch (\Exception $e2) {
                return null;
            }
        }
    }
}
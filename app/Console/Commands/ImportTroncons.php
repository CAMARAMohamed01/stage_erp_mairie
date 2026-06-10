<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportTroncons extends Command
{
    protected $signature = 'erp:import-troncons';
    protected $description = 'Importe les tronçons et génère la hiérarchie Secteurs/Zones à la volée.';

    public function handle()
    {
        $filePath = storage_path('app/troncons_data.csv');

        if (!file_exists($filePath)) {
            $this->error("Le fichier $filePath est introuvable.");
            return Command::FAILURE;
        }

        $this->info("Début de l'ingestion avec création des Secteurs et Zones...");

        $file = fopen($filePath, 'r');
        $header = fgetcsv($file, 0, ';');

        $currentZoneId = null;
        $countSecteurs = 0;
        $countZones = 0;
        $countTroncons = 0;

        DB::beginTransaction();

        try {
            while (($rawRow = fgetcsv($file, 0, ';')) !== false) {
                $row = array_map(function ($cell) {
                    // mb_convert_encoding force la traduction de Windows vers UTF-8
                    return mb_convert_encoding($cell, 'UTF-8', 'Windows-1252');
                }, $rawRow);
                $col0 = isset($row[0]) ? trim($row[0]) : '';
                $col1 = isset($row[1]) ? trim($row[1]) : '';

                // 1. DÉTECTION D'UN TITRE DE ZONE
                if (!empty($col0) && empty($col1)) {
                    $titreLigne = $col0;
                    $nomSecteur = explode(' ', $titreLigne)[0];

                    // --- Gestion du Secteur (minuscule car "CREATE TABLE secteur") ---
                    $secteur = DB::table('secteur')->where('nom_secteur', $nomSecteur)->first();
                    if (!$secteur) {
                        $secteurId = DB::table('secteur')->insertGetId([
                            'nom_secteur' => $nomSecteur,
                            'code_secteur' => $nomSecteur
                        ], 'id_secteur');
                        $countSecteurs++;
                    } else {
                        $secteurId = $secteur->id_secteur;
                    }

                    // --- Gestion de la Zone (Z majuscule car "CREATE TABLE Zone") ---
                    $zone = DB::table('Zone')
                        ->where('nom_zone', $titreLigne)
                        ->where('id_secteur', $secteurId)
                        ->first();

                    if (!$zone) {
                        $currentZoneId = DB::table('Zone')->insertGetId([
                            'nom_zone' => $titreLigne,
                            'id_secteur' => $secteurId
                        ], 'id_zone');
                        $countZones++;
                    } else {
                        $currentZoneId = $zone->id_zone;
                    }

                    continue;
                }

                // 2. DÉTECTION D'UN TRONÇON
                if (empty($col1)) {
                    continue;
                }

                $numeroTroncon = $col1;
                $nomPortion = !empty($row[2]) ? trim($row[2]) : null;
                $etat = !empty($row[5]) ? trim($row[5]) : null;
                $revetement = !empty($row[8]) ? trim($row[8]) : null;
                $gabarit = !empty($row[9]) ? trim($row[9]) : null;
                $paysage = !empty($row[10]) ? trim($row[10]) : null;
                $remarques = !empty($row[14]) ? trim($row[14]) : null;

                // minuscules car "CREATE TABLE troncon"
                DB::table('troncon')->updateOrInsert(
                    ['numero_troncon' => $numeroTroncon],
                    [
                        'nom_portion' => $nomPortion,
                        'etat_physique' => $etat,
                        'type_revetement' => $revetement,
                        'gabarit_accessibilite' => $gabarit,
                        'paysage_environnement' => $paysage,
                        //'observations_statut' => $remarques,
                        'id_zone' => $currentZoneId,
                    ]
                );

                $countTroncons++;
            }

            DB::commit();
            fclose($file);

            $this->info("Ingestion terminée et hiérarchisée avec succès !");
            $this->line("- Nouveaux Secteurs créés : <fg=blue>$countSecteurs</>");
            $this->line("- Nouvelles Zones créées : <fg=magenta>$countZones</>");
            $this->line("- Tronçons traités (Insert/Update) : <fg=green>$countTroncons</>");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($file) && is_resource($file)) {
                fclose($file);
            }
            $this->error("Erreur critique lors de l'importation. Rollback effectué.");
            $this->error($e->getMessage());

            return Command::FAILURE;
        }
    }
}
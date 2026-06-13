<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportAdressesCommune extends Command
{
    protected $signature = 'erp:import-adresses';
    protected $description = 'ETL : Importation de l\'intégralité des adresses de Dingy-Saint-Clair depuis le fichier BAN gouv (Séparateur ;)';

    public function handle()
    {
        $path = storage_path('app/adresses_dingy.csv');

        if (!file_exists($path)) {
            $this->error("Le fichier adresses_dingy.csv est introuvable dans storage/app/.");
            return 1;
        }

        $this->info("🚀 Début de l'analyse et de l'importation de la Base Adresse Nationale...");

        if (($handle = fopen($path, 'r')) !== FALSE) {

            // 1. On lit la ligne des entêtes avec le séparateur ";"
            $headers = fgetcsv($handle, 1000, ";");

            $countLieuxDits = 0;
            $countAdresses = 0;

            DB::connection()->disableQueryLog();

            // 2. Boucle de lecture avec le séparateur ";"
            while (($row = fgetcsv($handle, 1000, ";")) !== FALSE) {

                // Si la ligne est vide, on passe
                if (count($row) < 5)
                    continue;

                // Extraction selon les index exacts validés par ton diagnostic :
                // index 2 = numero | index 3 = rep | index 4 = nom_voie | index 5 = code_postal
                // index 7 = nom_commune | index 12 = lon | index 13 = lat
                $numero = !empty($row[2]) ? intval($row[2]) : null;
                $rep = !empty($row[3]) ? trim($row[3]) : null;
                $nomVoie = !empty($row[4]) ? trim($row[4]) : 'Inconnu';
                $codePostal = !empty($row[5]) ? trim($row[5]) : '74230';
                $ville = !empty($row[7]) ? trim($row[7]) : 'Dingy-Saint-Clair';
                $longitude = !empty($row[12]) ? floatval($row[12]) : null;
                $latitude = !empty($row[13]) ? floatval($row[13]) : null;
                // --- STRATÉGIE ETL POUR LE LIEU-DIT ---
                $nomLieuDit = 'Centre Village';
                if (Str::contains(Str::lower($nomVoie), ['blonnière', 'blonniere']))
                    $nomLieuDit = 'La Blonnière';
                elseif (Str::contains(Str::lower($nomVoie), 'glandon'))
                    $nomLieuDit = 'Le Glandon';
                elseif (Str::contains(Str::lower($nomVoie), 'tappes'))
                    $nomLieuDit = 'Les Tappes';
                elseif (Str::contains(Str::lower($nomVoie), 'nanoir'))
                    $nomLieuDit = 'Nanoir';
                elseif (Str::contains(Str::lower($nomVoie), 'chessenay'))
                    $nomLieuDit = 'Chessenay';
                elseif (Str::contains(Str::lower($nomVoie), 'parmelan'))
                    $nomLieuDit = 'Le Parmelan';
                elseif (Str::contains(Str::lower($nomVoie), 'fier'))
                    $nomLieuDit = 'La Plaine du Fier';
                //Maillage ou création du Lieu-dit
                $lieuDitRow = DB::table('lieu_dit')->where('nom_lieu_dit', $nomLieuDit)->first();
                if (!$lieuDitRow) {
                    $idLieuDit = DB::table('lieu_dit')->insertGetId(['nom_lieu_dit' => $nomLieuDit], 'id_lieu_dit');
                    $countLieuxDits++;
                } else {
                    $idLieuDit = $lieuDitRow->id_lieu_dit;
                }
                // Insertion de l'adresse si elle n'existe pas déjà (numéro + nom de voie + rep)
                DB::table('Adresse')->updateOrInsert(
                    [
                        'num_rue' => $numero,
                        'nom_voie' => $nomVoie,
                        'rep' => $rep
                    ],
                    [
                        'code_postal' => $codePostal,
                        'ville' => $ville,
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                        'id_lieu_dit' => $idLieuDit
                    ]
                );

                $countAdresses++;
            }

            fclose($handle);

            $this->info("📊 BILAN DE L'IMPORTATION :");
            $this->info("✨ {$countLieuxDits} nouveaux lieux-dits créés.");
            $this->info("✨ {$countAdresses} adresses distinctes importées avec succès dans PostgreSQL !");
        }

        return 0;
    }
}

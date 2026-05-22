<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportRoutesGeoJSON extends Command
{
    // Le nom de la commande à taper dans le terminal
    protected $signature = 'import:routes';

    // La description
    protected $description = 'Importe les routes depuis un fichier GeoJSON vers les tables voie et troncon';

    public function handle()
    {
        $path = storage_path('app/routes_dingy.geojson');

        if (!File::exists($path)) {
            $this->error("Le fichier routes_dingy.geojson est introuvable dans storage/app/");
            return;
        }

        $this->info("Lecture du fichier GeoJSON...");
        $json = File::get($path);
        $data = json_decode($json, true);

        if (!isset($data['features'])) {
            $this->error("Le format du GeoJSON est invalide.");
            return;
        }

        $count = 0;

        foreach ($data['features'] as $index => $feature) {
            // On ne traite que les lignes (LineString)
            if ($feature['geometry']['type'] !== 'LineString') {
                continue;
            }

            // Récupération du nom de la rue (s'il existe, sinon on met un nom par défaut)
            $nomVoie = $feature['properties']['name'] ?? 'Chemin rural / Voie sans nom';

            // On convertit le tableau de géométrie en chaîne JSON pour PostGIS
            $geometryJson = json_encode($feature['geometry']);

            // 1. Création ou récupération de la Voie
            $voie = DB::table('voie')->where('nom_voie', $nomVoie)->first();

            if (!$voie) {
                $idVoie = DB::table('voie')->insertGetId([
                    'nom_voie' => $nomVoie,
                    // On génère un numéro fictif pour respecter d'éventuelles contraintes
                    'numero_voie' => 'V-' . rand(1000, 9999)
                ], 'id_voie');
            } else {
                $idVoie = $voie->id_voie;
            }

            // 2. Création du Tronçon avec la fonction PostGIS (ST_GeomFromGeoJSON)
            // On s'assure d'utiliser le SRID 4326 (WGS 84, coordonnées GPS standard)
            DB::table('troncon')->insert([
                'id_voie' => $idVoie,
                'numero_troncon' => 'TR-' . uniqid(),
                'type_revetement' => $feature['properties']['surface'] ?? 'Inconnu',
                'trace_geo' => DB::raw("ST_SetSRID(ST_GeomFromGeoJSON('$geometryJson'), 4326)")
            ]);

            $count++;
        }

        $this->info("✅ Importation terminée ! $count tronçons ont été créés avec succès.");
    }
}
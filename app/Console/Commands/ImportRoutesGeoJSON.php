<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
/**
 * Class ImportRoutesGeoJSON
 * * Flux ETL (Extract, Transform, Load) dédié à l'intégration des données géospatiales.
 * Traite les tracés de voirie issus d'OpenStreetMap ou de SIG (format GeoJSON) 
 * pour alimenter le référentiel de la commune de Dingy-Saint-Clair.
 * * @package App\Console\Commands
 */
class ImportRoutesGeoJSON extends Command
{
    // Le nom de la commande à taper dans le terminal
    protected $signature = 'import:routes';

    // La description
    protected $description = 'Importe les routes depuis un fichier GeoJSON vers les tables voie et troncon';
    /**
     * Exécute le traitement du flux géospatial.
     * * Logique technique & métier :
     * 1. Extraction et parsing du fichier d'entrée GeoJSON.
     * 2. Filtrage des primitives pour n'isoler que les vecteurs linéaires (LineString).
     * 3. Normalisation et dédoublonnage de l'entité parente "voie".
     * 4. Injection du tronçon avec typage fort PostGIS (Conversion JSON -> Géométrie brute).
     * * @return void
     */
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
            /**
             * Contrainte topologique : On écarte les points (noeuds) et les polygones
             * pour ne conserver que les arcs (LineString) représentant les axes routiers.
             */
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
                    'numero_voie' => 'V-' . rand(1000, 9999) // Injection d'un code d'inventaire fictif
                ], 'id_voie');
            } else {
                $idVoie = $voie->id_voie;
            }

            // 2. INJECTION SPATIALE DANS POSTGRESQL (Table: troncon)
            /**
             * Sécurisation PostGIS : 
             * - ST_GeomFromGeoJSON parse la structure de coordonnées JSON.
             * - ST_SetSRID force le système de référence spatial sur le SRID 4326.
             * (Système WGS 84 utilisé par défaut par les puces GPS et OpenStreetMap).
             */
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
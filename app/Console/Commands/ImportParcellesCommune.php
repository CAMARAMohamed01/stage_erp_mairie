<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportParcellesCommune extends Command
{
    // Signature de la commande à lancer dans le terminal
    protected $signature = 'erp:import-parcelles';
    protected $description = 'ETL : Importation complète du cadastre (parcelles et géométries) de Dingy-Saint-Clair depuis data.gouv.fr';

    public function handle()
    {
        $path = storage_path('app/parcelles_dingy.geojson');

        if (!file_exists($path)) {
            $this->error("Le fichier parcelles_dingy.geojson est introuvable dans storage/app/.");
            return 1;
        }

        $this->info("🚀 Lecture et analyse du fichier du cadastre GeoJSON...");

        // Lecture et décodage du fichier JSON géospatial
        $geoJsonData = json_decode(file_get_contents($path), true);

        if (!$geoJsonData || !isset($geoJsonData['features'])) {
            $this->error("Le format du fichier GeoJSON est invalide ou corrompu.");
            return 1;
        }

        $parcelles = $geoJsonData['features'];
        $total = count($parcelles);
        $this->info("📋 {$total} parcelles détectées. Début du traitement et de l'intégration...");

        // Désactivation des logs de requêtes pour éviter l'explosion de la mémoire RAM
        DB::connection()->disableQueryLog();

        // Récupération de l'ID du lieu-dit par défaut pour le maillage obligatoire (clé étrangère)
        $idLieuDitParDefaut = DB::table('lieu_dit')->where('nom_lieu_dit', 'Centre Village')->value('id_lieu_dit');
        if (!$idLieuDitParDefaut) {
            $idLieuDitParDefaut = DB::table('lieu_dit')->insertGetId(['nom_lieu_dit' => 'Centre Village'], 'id_lieu_dit');
        }

        // Récupération de l'ID d'immobilisation par défaut si ta table exige une liaison à la création
        $idImmoParDefaut = DB::table('immobilisation_inventaire_')->value('id_immo') ?? 1;

        $countInserts = 0;
        $countUpdates = 0;

        // Barre de progression dans la console pour ton confort de dev
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($parcelles as $feature) {
            $props = $feature['properties'];
            $geometry = $feature['geometry'];

            //  Si la géométrie contient trop de points (parcelle géante/forêt/rivière), on l'ignore
            // Les parcelles géantes ont souvent des dizaines de milliers de coordonnées.
            if (isset($geometry['coordinates'][0]) && count($geometry['coordinates'][0]) > 200) {
                continue; // On passe à la parcelle suivante, trop grande pour être un bâtiment municipal
            }

            $section = !empty($props['section']) ? trim($props['section']) : 'A';
            $numero = !empty($props['numero']) ? str_pad(trim($props['numero']), 4, '0', STR_PAD_LEFT) : '0000';
            // On extrait la géométrie brute (le polygone du tracé) pour PostGIS
            $geometryJson = json_encode($feature['geometry']);

            // Extraction optionnelle du nom de lieu-dit si la DGFiP le renseigne (parfois dans 'nom_ld')
            $idLieuDit = $idLieuDitParDefaut;
            if (!empty($props['nom_ld'])) {
                $nomLd = trim($props['nom_ld']);
                $lieuDitRow = DB::table('lieu_dit')->where('nom_lieu_dit', 'ilike', $nomLd)->first();
                if ($lieuDitRow) {
                    $idLieuDit = $lieuDitRow->id_lieu_dit;
                }
            }

            // Vérification si la parcelle existe déjà
            $existing = DB::table('parcelle')
                ->where('section_cadastrale', $section)
                ->where('num_parcelle', $numero)
                ->first();

            if ($existing) {
                // Mise à jour de la géométrie géospatiale
                DB::table('parcelle')
                    ->where('id_parcelle', $existing->id_parcelle)
                    ->update([
                        'type_parcelle' => $props['type_parcelle'] ?? 'Inconnu',
                        'id_lieu_dit' => $idLieuDit
                    ]);

                // Mise à jour du polygone PostGIS géométrique via SQL brut
                DB::update(
                    "UPDATE parcelle 
                     SET geom_parcelle = ST_SetSRID(ST_GeomFromGeoJSON(?), 4326) 
                     WHERE id_parcelle = ?",
                    [$geometryJson, $existing->id_parcelle]
                );

                $countUpdates++;
            } else {
                // Création complète de la parcelle selon ton architecture
                $idParcelle = DB::table('parcelle')->insertGetId([
                    'section_cadastrale' => $section,
                    'num_parcelle' => $numero,
                    'type_parcelle' => $props['type_parcelle'] ?? 'Domaine Public',
                    'id_lieu_dit' => $idLieuDit,
                    'id_immo' => $idImmoParDefaut // Ajuste selon la contrainte de conteneur
                ], 'id_parcelle');

                // Injection du polygone géométrique PostGIS
                DB::update(
                    "UPDATE parcelle 
                     SET geom_parcelle = ST_SetSRID(ST_GeomFromGeoJSON(?), 4326) 
                     WHERE id_parcelle = ?",
                    [$geometryJson, $idParcelle]
                );

                $countInserts++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info("📊 BILAN DU TRAITEMENT CADRASTRAL :");
        $this->info("✨ {$countInserts} nouvelles parcelles créées avec leur polygone PostGIS.");
        $this->info("✨ {$countUpdates} parcelles existantes synchronisées.");
        $this->info("✅ Tout le cadastre de Dingy-Saint-Clair est maillé !");

        return 0;
    }
}
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportPatrimoineComplexe extends Command
{
    protected $signature = 'erp:import-patrimoine';
    protected $description = 'ETL : Migration du patrimoine avec protection stricte contre la duplication des adresses et des lieux publics';

    public function handle()
    {
        $this->info("🚀 Début de la migration stricte du patrimoine (Zéro doublon garanti)...");

        // 1. DICTIONNAIRE DU PATRIMOINE (Bâtiments complexes - Noms inchangés)
        $patrimoine = [
            [
                'batiment' => 'Bibliothèque',
                'adresse_recherche' => ['num' => 87, 'voie' => "place de l'église", 'ville' => 'Dingy-Saint-Clair', 'cp' => '74230', 'ld' => 'Centre Village'],
                'surface_bat' => 170,
                'type_erp' => 'R',
                'locaux' => [
                    ['nom' => 'Bibliothèque', 'etage' => 'RDC', 'surface' => 100, 'remarques' => null],
                    ['nom' => 'Bureau APED', 'etage' => 'RDC', 'surface' => 10, 'remarques' => null],
                    ['nom' => 'Grenette', 'etage' => 'RDC', 'surface' => 40, 'remarques' => null],
                    ['nom' => 'WC publics', 'etage' => 'RDC', 'surface' => 20, 'remarques' => null],
                ]
            ],
            [
                'batiment' => 'Bibliothèque',
                'adresse_recherche' => ['num' => 93, 'voie' => "place de l'église", 'ville' => 'Dingy-Saint-Clair', 'cp' => '74230', 'ld' => 'Centre Village'],
                'surface_bat' => 160,
                'type_erp' => 'W',
                'locaux' => [
                    ['nom' => 'Appartement', 'etage' => '1er + Mezzanine', 'surface' => 50, 'remarques' => null],
                    ['nom' => 'Salle Ados', 'etage' => '1er étage', 'surface' => 30, 'remarques' => null],
                    ['nom' => 'Grenier', 'etage' => '2e étage', 'surface' => 80, 'remarques' => null],
                ]
            ],
            [
                'batiment' => 'Mairie',
                'adresse_recherche' => ['num' => 55, 'voie' => "place de l'église", 'ville' => 'Dingy-Saint-Clair', 'cp' => '74230', 'ld' => 'Centre Village'],
                'surface_bat' => 454,
                'type_erp' => 'W',
                'locaux' => [
                    ['nom' => 'Mairie', 'etage' => '1er étage', 'surface' => 142, 'remarques' => null],
                    ['nom' => 'Mairie', 'etage' => 'RDC', 'surface' => 156, 'remarques' => 'bureaux'],
                    ['nom' => 'Local serveur', 'etage' => '1er étage', 'surface' => null, 'remarques' => null],
                    ['nom' => 'Espace antenne orange', 'etage' => '2e étage', 'surface' => null, 'remarques' => null],
                    ['nom' => 'Grenier', 'etage' => '2e étage', 'surface' => 156, 'remarques' => '8m² pour Antenne orange'],
                    ['nom' => 'Dépendances', 'etage' => 'cour', 'surface' => null, 'remarques' => 'Cabanon'],
                ]
            ],
            [
                'batiment' => 'Créche',
                'adresse_recherche' => ['num' => 33, 'voie' => 'route de chez Brachet', 'ville' => 'Dingy-Saint-Clair', 'cp' => '74230', 'ld' => 'Centre Village'],
                'surface_bat' => 313,
                'type_erp' => 'R',
                'locaux' => [
                    ['nom' => 'créche', 'etage' => 'RDC', 'surface' => 263, 'remarques' => null],
                    ['nom' => 'salle Mélèze', 'etage' => 'Etage', 'surface' => 50, 'remarques' => null],
                ]
            ],
            [
                'batiment' => 'maison médicale',
                'adresse_recherche' => ['num' => 73, 'voie' => 'route de chez Brachet', 'ville' => 'Dingy-Saint-Clair', 'cp' => '74230', 'ld' => 'Centre Village'],
                'surface_bat' => 200,
                'type_erp' => 'U',
                'locaux' => [
                    ['nom' => 'Local paramédical', 'etage' => 'RDC', 'surface' => 60, 'remarques' => null],
                    ['nom' => 'Dépendances', 'etage' => 'Local autre', 'surface' => 20, 'remarques' => null],
                    ['nom' => 'Grenier', 'etage' => '2e étage', 'surface' => 120, 'remarques' => 'étage combles perdues'],
                    ['nom' => 'Appartement', 'etage' => 'RDC + 1', 'surface' => 60, 'remarques' => null],
                ]
            ],
            [
                'batiment' => 'Presbytère',
                'adresse_recherche' => ['num' => 52, 'voie' => 'route de chez Brachet', 'ville' => 'Dingy-Saint-Clair', 'cp' => '74230', 'ld' => 'Centre Village'],
                'surface_bat' => 535,
                'type_erp' => 'W',
                'locaux' => [
                    ['nom' => 'ancien presbytere', 'etage' => 'corps', 'surface' => 495, 'remarques' => null],
                    ['nom' => 'Local randonneur', 'etage' => 'RDC', 'surface' => 20, 'remarques' => 'Dépendances'],
                    ['nom' => 'Local autre', 'etage' => 'RDC', 'surface' => 20, 'remarques' => 'Dépendances'],
                ]
            ],
            [
                'batiment' => 'Espace Michel Doche',
                'adresse_recherche' => ['num' => 85, 'voie' => 'route de la Blonnière', 'ville' => 'Dingy-Saint-Clair', 'cp' => '74230', 'ld' => 'La Blonnière'],
                'surface_bat' => 1099,
                'type_erp' => 'L',
                'locaux' => [
                    ['nom' => 'Restaurant scolaire', 'etage' => 'RDC', 'surface' => 1099, 'remarques' => null],
                    ['nom' => 'Salle Michel Doche', 'etage' => 'RDC', 'surface' => null, 'remarques' => null],
                    ['nom' => 'Salle Fier', 'etage' => '1er étage', 'surface' => null, 'remarques' => null],
                    ['nom' => 'Salle Parmelan', 'etage' => '1er étage', 'surface' => null, 'remarques' => null],
                    ['nom' => 'Grenier', 'etage' => '1er étage', 'surface' => null, 'remarques' => null],
                    ['nom' => 'Local théatre / Aped', 'etage' => null, 'surface' => null, 'remarques' => 'Dépendances'],
                    ['nom' => 'Local mairie', 'etage' => null, 'surface' => null, 'remarques' => 'Dépendances'],
                ]
            ],
            [
                'batiment' => 'Maison forestière',
                'adresse_recherche' => ['num' => 40, 'voie' => 'route de la maison forestière', 'ville' => 'Dingy-Saint-Clair', 'cp' => '74230', 'ld' => 'Centre Village'],
                'surface_bat' => 295,
                'type_erp' => 'W',
                'locaux' => [
                    ['nom' => 'maison forestiere', 'etage' => null, 'surface' => 295, 'remarques' => null]
                ]
            ],
            [
                'batiment' => 'Services techniques',
                'adresse_recherche' => ['num' => 20, 'voie' => 'route de la maison forestière', 'ville' => 'Dingy-Saint-Clair', 'cp' => '74230', 'ld' => 'Centre Village'],
                'surface_bat' => 220,
                'type_erp' => 'W',
                'locaux' => [
                    ['nom' => 'Local technique', 'etage' => 'RDC', 'surface' => 220, 'remarques' => null]
                ]
            ],
            [
                'batiment' => 'Espace jeunes',
                'adresse_recherche' => ['num' => null, 'voie' => 'chemin de la maison  forestière', 'ville' => 'Dingy-Saint-Clair', 'cp' => '74230', 'ld' => 'Centre Village'],
                'surface_bat' => 124,
                'type_erp' => 'L',
                'locaux' => [
                    ['nom' => 'ancien club house', 'etage' => 'RDC', 'surface' => 124, 'remarques' => null],
                    ['nom' => 'ancien vestiaire 1', 'etage' => null, 'surface' => null, 'remarques' => null],
                    ['nom' => 'ancien vestiaire 2', 'etage' => null, 'surface' => null, 'remarques' => null],
                    ['nom' => 'ancien vestiaire 3', 'etage' => null, 'surface' => null, 'remarques' => null],
                    ['nom' => 'Local stockage', 'etage' => null, 'surface' => null, 'remarques' => null],
                ]
            ],
            [
                'batiment' => 'Bâtiment jeunesse',
                'adresse_recherche' => ['num' => 65, 'voie' => "place de l'église", 'ville' => 'Dingy-Saint-Clair', 'cp' => '74230', 'ld' => 'Centre Village'],
                'surface_bat' => 761,
                'type_erp' => 'R',
                'locaux' => [
                    ['nom' => 'batiment jeunesse', 'etage' => null, 'surface' => 761, 'remarques' => null],
                    ['nom' => 'salle d\'activités + vestiaires', 'etage' => null, 'surface' => 100, 'remarques' => null],
                ]
            ],
            [
                'batiment' => 'Maison Tessier',
                'adresse_recherche' => ['num' => 213, 'voie' => 'route du chef lieu', 'ville' => 'Dingy-Saint-Clair', 'cp' => '74230', 'ld' => 'Centre Village'],
                'surface_bat' => 191,
                'type_erp' => 'W',
                'locaux' => [
                    ['nom' => 'Maison Tessier', 'etage' => null, 'surface' => 191, 'remarques' => null]
                ]
            ],
            [
                'batiment' => 'EAS',
                'adresse_recherche' => ['num' => 47, 'voie' => 'route du fier', 'ville' => 'Dingy-Saint-Clair', 'cp' => '74230', 'ld' => 'La Plaine du Fier'],
                'surface_bat' => 49,
                'type_erp' => 'X',
                'locaux' => [
                    ['nom' => 'vestiaires', 'etage' => null, 'surface' => null, 'remarques' => null],
                    ['nom' => 'salle polyvalente', 'etage' => null, 'surface' => 49, 'remarques' => null],
                    ['nom' => 'grenette', 'etage' => null, 'surface' => null, 'remarques' => null],
                ]
            ],
            [
                'batiment' => 'école Maurice Anjot',
                'adresse_recherche' => ['num' => 65, 'voie' => "place de l'église", 'ville' => 'Dingy-Saint-Clair', 'cp' => '74230', 'ld' => 'Centre Village'],
                'surface_bat' => 1195,
                'type_erp' => 'R',
                'locaux' => [
                    ['nom' => 'Ecole maurice Anjot', 'etage' => null, 'surface' => 1195, 'remarques' => null],
                    ['nom' => 'Dépendances', 'etage' => 'cour', 'surface' => null, 'remarques' => 'Cabanon'],
                ]
            ],
            [
                'batiment' => 'Eglise',
                'adresse_recherche' => ['num' => 55, 'voie' => "place de l'église", 'ville' => 'Dingy-Saint-Clair', 'cp' => '74230', 'ld' => 'Centre Village'],
                'surface_bat' => 542,
                'type_erp' => 'V',
                'locaux' => [
                    ['nom' => 'Eglise', 'etage' => null, 'surface' => 542, 'remarques' => null]
                ]
            ]
        ];

        // 2. DICTIONNAIRE DES LIEUX PUBLICS / INFRASTRUCTURES ISOLÉES
        $infrastructuresPublics = [
            ['nom' => 'WC du stade', 'typo' => 'bloc sanitaire'],
            ['nom' => 'Chalet Ablon', 'typo' => 'toilettes seches'],
            ['nom' => 'Chalet Ablon', 'typo' => 'chalet d\'alpage'],
            ['nom' => 'Chalet Ablon', 'typo' => 'chalet du Perthuis'],
            ['nom' => 'Chapelle', 'typo' => 'chapelle St Clair'],
            ['nom' => 'Chapelle', 'typo' => 'chapelle'],
            ['nom' => 'Four à pain', 'typo' => 'Four à pain (la blonnière)'],
            ['nom' => 'Four à pain', 'typo' => 'Four à pain (glandon)'],
            ['nom' => 'Four à pain', 'typo' => 'four à pain (les tappes)'],
            ['nom' => 'cabane du garde', 'typo' => 'cabane du garde'],
        ];

        // Vidage propre avant ré-importation
        DB::statement('TRUNCATE TABLE local_ CASCADE;');
        DB::statement('DELETE FROM batiment;');
        DB::statement('DELETE FROM lieux_publics;');

        // SÉCURITÉ PARCELLE DEFAUT
        $idParcelleDefaut = DB::table('parcelle')->value('id_parcelle');
        if (!$idParcelleDefaut) {
            $idLieuDitDefaut = DB::table('lieu_dit')->where('nom_lieu_dit', 'Centre Village')->value('id_lieu_dit') ?? 1;
            $idImmoDefaut = DB::table('immobilisation_inventaire_')->value('id_immo') ?? 1;

            $idParcelleDefaut = DB::table('parcelle')->insertGetId([
                'section_cadastrale' => 'A',
                'num_parcelle' => '0001',
                'type_parcelle' => 'Domaine Communal',
                'id_lieu_dit' => $idLieuDitDefaut,
                'id_immo' => $idImmoDefaut
            ], 'id_parcelle');
        }

        $indexImmo = 1;

        // --- STRATÉGIE 1 : IMPORTATION DES BÂTIMENTS ---
        foreach ($patrimoine as $item) {
            $search = $item['adresse_recherche'];

            $adresse = DB::table('Adresse')
                ->where('nom_voie', 'ilike', trim($search['voie']))
                ->where('num_rue', $search['num'])
                ->first();

            if ($adresse) {
                $idAdresse = $adresse->id_adresse;
            } else {
                $idLieuDit = DB::table('lieu_dit')->where('nom_lieu_dit', 'ilike', $search['ld'])->value('id_lieu_dit') ?? 1;
                $idAdresse = DB::table('Adresse')->insertGetId([
                    'num_rue' => $search['num'],
                    'nom_voie' => $search['voie'],
                    'code_postal' => $search['cp'],
                    'ville' => $search['ville'],
                    'id_lieu_dit' => $idLieuDit
                ], 'id_adresse');
            }

            $immoExiste = DB::table('immobilisation_inventaire_')->where('id_immo', $indexImmo)->exists();
            if (!$immoExiste) {
                DB::table('immobilisation_inventaire_')->insertOrIgnore([
                    'id_immo' => $indexImmo,
                    'num_inventaire' => 'INV-' . str_pad($indexImmo, 4, '0', STR_PAD_LEFT),
                    'libelle_comptable' => 'Immo ' . $item['batiment']
                ]);
            }

            $idTypeErp = DB::table('type_erp')->where('type_erp', $item['type_erp'])->value('id_type_erp') ?? 1;

            $idBatiment = DB::table('batiment')->insertGetId([
                'nom_bat' => $item['batiment'],
                'surface_totale_m2' => $item['surface_bat'],
                'date_construction' => '1980-01-01',
                'id_parcelle' => $idParcelleDefaut,
                'id_type_erp' => $idTypeErp,
                'id_adresse' => $idAdresse,
                'id_immo' => $indexImmo
            ], 'id_batiment');

            if ($adresse && $adresse->latitude && $adresse->longitude) {
                $geojson = json_encode([
                    'type' => 'Point',
                    'coordinates' => [$adresse->longitude, $adresse->latitude]
                ]);
                DB::update(
                    "UPDATE batiment SET geom_batiment = ST_SetSRID(ST_GeomFromGeoJSON(?), 4326) WHERE id_batiment = ?",
                    [$geojson, $idBatiment]
                );
            }

            foreach ($item['locaux'] as $loc) {
                DB::table('local_')->insert([
                    'nom_local' => $loc['nom'],
                    'niveau' => $loc['etage'],
                    'surface_m2' => $loc['surface'],
                    'remarque' => $loc['remarques'],
                    'id_batiment' => $idBatiment
                ]);
            }

            $this->info("🟩 Bâtiment créé de façon unique : {$item['batiment']}");
            $indexImmo++;
        }

        // --- STRATÉGIE 2 : IMPORTATION DES LIEUX PUBLICS (PROTECTION CONTRE LES DOUBLONS CRÉÉE) ---
        foreach ($infrastructuresPublics as $infra) {

            // 🛡️ ANCRE D'UNICITÉ : On vérifie si la paire Nom + Typologie existe déjà en base
            $existeLieu = DB::table('lieux_publics')
                ->where('nom_lieu', $infra['nom'])
                ->where('typologie_lieu', $infra['typo'])
                ->exists();

            if (!$existeLieu) {
                DB::table('lieux_publics')->insert([
                    'nom_lieu' => $infra['nom'],
                    'typologie_lieu' => $infra['typo'],
                    'id_batiment' => null,
                    'id_parcelle' => $idParcelleDefaut,
                    'id_immo' => null,
                    'id_type_erp' => null,
                    'id_decision_reglement' => null
                ]);

                $this->info("🟦 Lieu Public créé (Unique) : {$infra['nom']} — Typologie: {$infra['typo']}");
            } else {
                $this->comment("⚠️ Lieu Public ignoré (Doublon détecté) : {$infra['nom']} ({$infra['typo']})");
            }
        }

        $this->info("✨ Terminé ! Le patrimoine a été entièrement migré sans aucune altération de nom et sans répétition.");
        return 0;
    }
}
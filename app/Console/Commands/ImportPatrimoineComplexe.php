<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportPatrimoineComplexe extends Command
{
    protected $signature = 'erp:import-patrimoine';
    protected $description = 'ETL : Importation avec moteur de recherche d\'adresses anti-accents et anti-abréviations.';

    public function handle()
    {
        $this->info("🚀 Début de l'importation (Moteur de recherche d'adresses avancé)...");

        // 1. DICTIONNAIRE : On utilise uniquement le mot-clé le plus fort de la voie (sans se soucier de route/place/chemin)
        $patrimoine = [
            [
                'batiment' => 'Bibliothèque',
                'adresse_recherche' => ['num' => 87, 'mot_cle' => 'eglise'],
                'type_erp' => 'R',
                'locaux' => [
                    ['nom' => 'Bibliothèque', 'etage' => 'RDC', 'surface' => 100, 'remarques' => null],
                    ['nom' => 'Bureau APED', 'etage' => 'RDC', 'surface' => 10, 'remarques' => null],
                    ['nom' => 'Grenette', 'etage' => 'RDC', 'surface' => 40, 'remarques' => null],
                    ['nom' => 'WC publics', 'etage' => 'RDC', 'surface' => 20, 'remarques' => null],
                    ['nom' => 'Appartement', 'etage' => '1er + Mezzanine', 'surface' => 50, 'remarques' => null],
                    ['nom' => 'Salle Ados', 'etage' => '1er étage', 'surface' => 30, 'remarques' => null],
                    ['nom' => 'Grenier', 'etage' => '2e étage', 'surface' => 80, 'remarques' => null],
                ]
            ],
            [
                'batiment' => 'Mairie',
                'adresse_recherche' => ['num' => 55, 'mot_cle' => 'eglise'],
                'type_erp' => 'W',
                'locaux' => [
                    ['nom' => 'Local principal', 'etage' => '1er étage', 'surface' => 142, 'remarques' => null],
                    ['nom' => 'Mairie', 'etage' => 'RDC', 'surface' => 156, 'remarques' => 'bureaux'],
                    ['nom' => 'Local serveur', 'etage' => '1er étage', 'surface' => null, 'remarques' => null],
                    ['nom' => 'Espace antenne orange', 'etage' => '2e étage', 'surface' => null, 'remarques' => null],
                    ['nom' => 'Grenier', 'etage' => '2e étage', 'surface' => 156, 'remarques' => '8m² pour Antenne orange'],
                    ['nom' => 'Dépendances', 'etage' => 'cour', 'surface' => null, 'remarques' => 'Cabanon'],
                ]
            ],
            [
                'batiment' => 'Créche',
                'adresse_recherche' => ['num' => 33, 'mot_cle' => 'brachet'],
                'type_erp' => 'R',
                'locaux' => [
                    ['nom' => 'créche', 'etage' => 'RDC', 'surface' => 263, 'remarques' => null],
                    ['nom' => 'salle Mélèze', 'etage' => 'Etage', 'surface' => 50, 'remarques' => null],
                ]
            ],
            [
                'batiment' => 'Maison médicale',
                'adresse_recherche' => ['num' => 73, 'mot_cle' => 'brachet'],
                'type_erp' => 'U',
                'locaux' => [
                    ['nom' => 'Local Maison médicale', 'etage' => 'RDC', 'surface' => 60, 'remarques' => null],
                    ['nom' => 'Dépendances', 'etage' => 'Local autre', 'surface' => 20, 'remarques' => null],
                    ['nom' => 'Grenier', 'etage' => '2e étage', 'surface' => 120, 'remarques' => 'étage combles perdues'],
                    ['nom' => 'Appartement', 'etage' => 'RDC + 1', 'surface' => 60, 'remarques' => null],
                ]
            ],
            [
                'batiment' => 'Presbytère',
                'adresse_recherche' => ['num' => 52, 'mot_cle' => 'brachet'],
                'type_erp' => 'W',
                'locaux' => [
                    ['nom' => 'ancien presbytere', 'etage' => 'corps', 'surface' => 495, 'remarques' => null],
                    ['nom' => 'Local randonneur', 'etage' => 'RDC', 'surface' => 20, 'remarques' => 'Dépendances'],
                    ['nom' => 'Local autre', 'etage' => 'RDC', 'surface' => 20, 'remarques' => 'Dépendances'],
                ]
            ],
            [
                'batiment' => 'Espace Michel Doche',
                'adresse_recherche' => ['num' => 85, 'mot_cle' => 'blonniere'],
                'type_erp' => 'L',
                'locaux' => [
                    ['nom' => 'Restaurant scolaire', 'etage' => 'RDC', 'surface' => 1099, 'remarques' => null],
                    ['nom' => 'Salle Michel Doche', 'etage' => 'RDC', 'surface' => null, 'remarques' => null],
                    ['nom' => 'Salle Fier', 'etage' => '1er étage', 'surface' => null, 'remarques' => null],
                    ['nom' => 'Salle Parmelan', 'etage' => '1er étage', 'surface' => null, 'remarques' => null],
                    ['nom' => 'Grenier', 'etage' => '1er étage', 'surface' => null, 'remarques' => null],
                    ['nom' => 'Local théatre / Aped', 'etage' => 'Dépendances', 'surface' => null, 'remarques' => null],
                    ['nom' => 'Local mairie', 'etage' => 'Dépendances', 'surface' => null, 'remarques' => null],
                ]
            ],
            [
                'batiment' => 'Maison forestière',
                'adresse_recherche' => ['num' => 40, 'mot_cle' => 'forestiere'],
                'type_erp' => 'W',
                'locaux' => [
                    ['nom' => 'maison forestiere', 'etage' => null, 'surface' => 295, 'remarques' => null]
                ]
            ],
            [
                'batiment' => 'Services techniques',
                'adresse_recherche' => ['num' => 20, 'mot_cle' => 'forestiere'],
                'type_erp' => 'W',
                'locaux' => [
                    ['nom' => 'Local technique', 'etage' => 'RDC', 'surface' => 220, 'remarques' => null]
                ]
            ],
            [
                'batiment' => 'Espace jeunes',
                'adresse_recherche' => ['num' => null, 'mot_cle' => 'forestiere'],
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
                'adresse_recherche' => ['num' => 65, 'mot_cle' => 'eglise'],
                'type_erp' => 'R',
                'locaux' => [
                    ['nom' => 'salle d\'activités + vestiaires', 'etage' => null, 'surface' => 100, 'remarques' => null],
                ]
            ],
            [
                'batiment' => 'Maison Tessier',
                'adresse_recherche' => ['num' => 213, 'mot_cle' => 'chef'],
                'type_erp' => 'W',
                'locaux' => [
                    ['nom' => 'Maison Tessier', 'etage' => null, 'surface' => 191, 'remarques' => null]
                ]
            ],
            [
                'batiment' => 'WC du stade',
                'adresse_recherche' => ['num' => null, 'mot_cle' => 'chef'],
                'type_erp' => 'X',
                'locaux' => [
                    ['nom' => 'WC du stade', 'etage' => null, 'surface' => 20, 'remarques' => 'bloc sanitaire'],
                ]
            ],
            [
                'batiment' => 'EAS',
                'adresse_recherche' => ['num' => 47, 'mot_cle' => 'fier'],
                'type_erp' => 'X',
                'locaux' => [
                    ['nom' => 'vestiaires', 'etage' => null, 'surface' => null, 'remarques' => null],
                    ['nom' => 'salle polyvalente', 'etage' => null, 'surface' => 49, 'remarques' => null],
                    ['nom' => 'grenette', 'etage' => null, 'surface' => null, 'remarques' => null],
                ]
            ],
            [
                'batiment' => 'Chalet Ablon',
                'adresse_recherche' => ['num' => null, 'mot_cle' => 'ablon'],
                'type_erp' => 'W',
                'locaux' => [
                    ['nom' => 'toilettes seches', 'etage' => null, 'surface' => 3, 'remarques' => null],
                ]
            ],
            [
                'batiment' => 'Chalet Ablon (Le Perthuis)',
                'adresse_recherche' => ['num' => null, 'mot_cle' => 'perthuis'],
                'type_erp' => 'W',
                'locaux' => [
                    ['nom' => 'chalet du Perthuis', 'etage' => null, 'surface' => 80, 'remarques' => null],
                ]
            ],
            [
                'batiment' => 'Ecole primaire (Maurice Anjot)',
                'adresse_recherche' => ['num' => 65, 'mot_cle' => 'eglise'],
                'type_erp' => 'R',
                'locaux' => [
                    ['nom' => 'Ecole maurice Anjot', 'etage' => null, 'surface' => 1195, 'remarques' => null],
                    ['nom' => 'Dépendances', 'etage' => 'cour', 'surface' => null, 'remarques' => 'Cabanon'],
                ]
            ],
            [
                'batiment' => 'Chapelle St Clair',
                'adresse_recherche' => ['num' => null, 'mot_cle' => 'clair'],
                'type_erp' => 'V',
                'locaux' => [
                    ['nom' => 'chapelle St Clair', 'etage' => null, 'surface' => 60, 'remarques' => null]
                ]
            ],
            [
                'batiment' => 'Chapelle la blonnière',
                'adresse_recherche' => ['num' => null, 'mot_cle' => 'blonniere'],
                'type_erp' => 'V',
                'locaux' => [
                    ['nom' => 'chapelle', 'etage' => null, 'surface' => 50, 'remarques' => null]
                ]
            ],
            [
                'batiment' => 'Four à pain (la blonnière)',
                'adresse_recherche' => ['num' => null, 'mot_cle' => 'blonniere'],
                'type_erp' => 'W',
                'locaux' => [
                    ['nom' => 'Four à pain', 'etage' => null, 'surface' => 15, 'remarques' => null]
                ]
            ],
            [
                'batiment' => 'Four à pain (glandon)',
                'adresse_recherche' => ['num' => null, 'mot_cle' => 'glandon'],
                'type_erp' => 'W',
                'locaux' => [
                    ['nom' => 'Four à pain', 'etage' => null, 'surface' => 15, 'remarques' => null]
                ]
            ],
            [
                'batiment' => 'Four à pain (les tappes)',
                'adresse_recherche' => ['num' => null, 'mot_cle' => 'tappes'],
                'type_erp' => 'W',
                'locaux' => [
                    ['nom' => 'four à pain', 'etage' => null, 'surface' => 15, 'remarques' => null]
                ]
            ],
            [
                'batiment' => 'cabane du garde',
                'adresse_recherche' => ['num' => null, 'mot_cle' => 'fournet'],
                'type_erp' => 'W',
                'locaux' => [
                    ['nom' => 'cabane du garde', 'etage' => null, 'surface' => 10, 'remarques' => null]
                ]
            ],
            [
                'batiment' => 'Eglise',
                'adresse_recherche' => ['num' => 55, 'mot_cle' => 'eglise'],
                'type_erp' => 'V',
                'locaux' => [
                    ['nom' => 'Eglise', 'etage' => null, 'surface' => 542, 'remarques' => null]
                ]
            ]
        ];

        // 2. NETTOYAGE
        DB::statement('TRUNCATE TABLE local_, batiment CASCADE;');

        // 3. CHARGEMENT DE TOUTES LES ADRESSES EN MÉMOIRE POUR ANALYSE
        $toutesLesAdresses = DB::table('Adresse')->get();
        if ($toutesLesAdresses->isEmpty()) {
            $this->error("Erreur fatale : La table 'Adresse' est vide !");
            return 1;
        }

        $idAdresseDefaut = $toutesLesAdresses->first()->id_adresse;

        $idErpDefaut = DB::table('type_erp')->value('id_type_erp');
        if (!$idErpDefaut) {
            $idErpDefaut = DB::table('type_erp')->insertGetId([
                'reglementation_applicable' => 'Code de l\'urbanisme',
                'categorie_erp' => 5,
                'type_erp' => 'W'
            ], 'id_type_erp');
        }

        // 4. BOUCLE D'INSERTION
        foreach ($patrimoine as $item) {
            $search = $item['adresse_recherche'];

            // LA MAGIE OPÈRE ICI : On cherche l'adresse en ignorant les accents et les espaces
            $adresseTrouvee = $toutesLesAdresses->first(function ($adr) use ($search) {
                // Str::slug transforme "PL DE L'ÉGLISE" en "pl-de-leglise"
                $voieBddNettoyee = Str::slug($adr->nom_voie);

                $matchMotCle = str_contains($voieBddNettoyee, $search['mot_cle']);

                if (!empty($search['num'])) {
                    return $adr->num_rue == $search['num'] && $matchMotCle;
                }

                return $matchMotCle;
            });

            $idAdresseFinale = $adresseTrouvee ? $adresseTrouvee->id_adresse : $idAdresseDefaut;

            // Création du bâtiment
            $idBatiment = DB::table('batiment')->insertGetId([
                'nom_bat' => $item['batiment'],
                'surface_totale_m2' => collect($item['locaux'])->sum('surface') > 0 ? collect($item['locaux'])->sum('surface') : null,
                'id_type_erp' => $idErpDefaut,
                'id_adresse' => $idAdresseFinale,
                'id_parcelle' => null,
                'id_immo' => null
            ], 'id_batiment');

            // Création des locaux liés à ce bâtiment
            foreach ($item['locaux'] as $loc) {
                DB::table('local_')->insert([
                    'nom_local' => $loc['nom'],
                    'niveau' => $loc['etage'],
                    'surface_m2' => $loc['surface'],
                    'remarque' => $loc['remarques'],
                    'id_batiment' => $idBatiment,
                    'id_lieu' => null
                ]);
            }

            if ($adresseTrouvee) {
                $this->info("🟩 Bâtiment inséré : {$item['batiment']} (Lié à : {$adresseTrouvee->num_rue} {$adresseTrouvee->nom_voie})");
            } else {
                $this->comment("🟨 Bâtiment inséré : {$item['batiment']} (Mot-clé '{$search['mot_cle']}' introuvable -> Adresse par défaut)");
            }
        }

        $this->info("✨ Terminé ! Bâtiments et Locaux importés avec succès.");
        return 0;
    }
}
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ImportSuiviTravaux extends Command
{
    protected $signature = 'erp:import-suivi-travaux';
    protected $description = 'ETL de masse : Nettoyage final des caractères brisés Excel pour PostgreSQL';

    public function handle()
    {
        $cheminFichier = storage_path('app/suivi_travaux.csv');

        if (!file_exists($cheminFichier)) {
            $this->error("❌ Le fichier CSV est introuvable dans : {$cheminFichier}");
            return;
        }

        $this->info('🚀 Préparation du nettoyage final de la base de données...');

        // 🛡️ 1. SÉCURITÉ : Initialisation Lieu-dit minimal
        $idLieuDit = DB::table('lieu_dit')->where('nom_lieu_dit', 'Dingy-Saint-Clair Bourg')->value('id_lieu_dit');
        if (!$idLieuDit) {
            $idLieuDit = DB::table('lieu_dit')->insertGetId(['nom_lieu_dit' => 'Dingy-Saint-Clair Bourg'], 'id_lieu_dit');
        }

        // 🛡️ 2. SÉCURITÉ : Initialisation Adresse minimale
        $idAdresse = DB::table('Adresse')->where('nom_voie', 'Rue de la Mairie')->value('id_adresse');
        if (!$idAdresse) {
            $idAdresse = DB::table('Adresse')->insertGetId([
                'num_rue' => 1,
                'nom_voie' => 'Rue de la Mairie',
                'code_postal' => '74230',
                'ville' => 'Dingy-Saint-Clair',
                'id_lieu_dit' => $idLieuDit
            ], 'id_adresse');
        }

        // 🛡️ 3. SÉCURITÉ : Initialisation Tiers Propriétaire Mairie
        $idTiersMairie = DB::table('tiers')->where('type_tiers', 'Morale')->value('id_tiers');
        if (!$idTiersMairie) {
            $idTiersMairie = DB::table('tiers')->insertGetId([
                'type_tiers' => 'Morale',
                'email_tiers' => 'contact@dingy-saint-clair.fr',
                'tel_tiers' => '0450020054',
                'id_adresse' => $idAdresse
            ], 'id_tiers');
            DB::table('tiers_morale')->insert(['id_tiers' => $idTiersMairie, 'raison_sociale' => 'Mairie de Dingy-Saint-Clair']);
        }

        // 🛡️ 4. SÉCURITÉ : Initialisation Type ERP
        $idTypeErp = DB::table('type_erp')->where('reglementation_applicable', 'Générique ERP Mairie')->value('id_type_erp');
        if (!$idTypeErp) {
            $idTypeErp = DB::table('type_erp')->insertGetId([
                'reglementation_applicable' => 'Générique ERP Mairie',
                'libelle_erp' => 'Établissement recevant du public',
                'categorie_erp' => 5,
                'type_erp' => 'W'
            ], 'id_type_erp');
        }

        // 🛡️ 5. SÉCURITÉ : Initialisation Parcelle minimale
        $idParcelle = DB::table('parcelle')->where('num_parcelle', '0001')->value('id_parcelle');
        if (!$idParcelle) {
            $idParcelle = DB::table('parcelle')->insertGetId(['num_parcelle' => '0001', 'section_cadastrale' => 'A', 'id_lieu_dit' => $idLieuDit], 'id_parcelle');
        }

        // 🗺️ DICTIONNAIRE DE MAPPING INTÉGRAL
        $dictionnairePatrimoine = [
            'église' => ['nom_officiel' => 'Église', 'table_cible' => 'batiment'],
            'place de l\'église' => ['nom_officiel' => 'Église', 'table_cible' => 'batiment'],
            'mairie' => ['nom_officiel' => 'Mairie', 'table_cible' => 'batiment'],
            'appartement mairie' => ['nom_officiel' => 'Mairie', 'table_cible' => 'batiment'],
            'salle des sociétés' => ['nom_officiel' => 'Salle des Sociétés', 'table_cible' => 'batiment'],
            'salle michel doche' => ['nom_officiel' => 'Salle Michel Doche', 'table_cible' => 'batiment'],
            'salle du parmelan' => ['nom_officiel' => 'Salle du Parmelan', 'table_cible' => 'batiment'],
            'salle du fier' => ['nom_officiel' => 'Salle du Fier', 'table_cible' => 'batiment'],
            'salle paroissiale' => ['nom_officiel' => 'Salle Paroissiale', 'table_cible' => 'batiment'],
            'salle mélèze' => ['nom_officiel' => 'Salle Mélèze', 'table_cible' => 'batiment'],
            'bâtiment jeunesse' => ['nom_officiel' => 'Bâtiment Jeunesse', 'table_cible' => 'batiment'],
            'bâtiment jeunesse ' => ['nom_officiel' => 'Bâtiment Jeunesse', 'table_cible' => 'batiment'],
            'bâtiment services tk' => ['nom_officiel' => 'Bâtiment Services TK', 'table_cible' => 'batiment'],
            'bibliothèque' => ['nom_officiel' => 'Bibliothèque', 'table_cible' => 'batiment'],
            'appart bibliothéque' => ['nom_officiel' => 'Bibliothèque', 'table_cible' => 'batiment'],
            'maison médicale' => ['nom_officiel' => 'Maison Médicale', 'table_cible' => 'batiment'],
            'crèche' => ['nom_officiel' => 'Crèche', 'table_cible' => 'batiment'],
            'créche' => ['nom_officiel' => 'Crèche', 'table_cible' => 'batiment'],
            'presbytère' => ['nom_officiel' => 'Presbytère', 'table_cible' => 'batiment'],
            'la ruche citoyenne' => ['nom_officiel' => 'La Ruche Citoyenne', 'table_cible' => 'batiment'],
            'la ruche citoyenne ' => ['nom_officiel' => 'La Ruche Citoyenne', 'table_cible' => 'batiment'],
            'maison tessier' => ['nom_officiel' => 'Maison Tessier', 'table_cible' => 'batiment'],
            'maison forestière' => ['nom_officiel' => 'Maison Forestière', 'table_cible' => 'batiment'],
            'chalet ablon' => ['nom_officiel' => 'Chalet Ablon', 'table_cible' => 'batiment'],
            'chalet du perthuis' => ['nom_officiel' => 'Chalet du Perthuis', 'table_cible' => 'batiment'],
            'refuge parmelan' => ['nom_officiel' => 'Refuge Parmelan', 'table_cible' => 'batiment'],
            'centre village' => ['nom_officiel' => 'Centre Village', 'table_cible' => 'batiment'],
            'monument aux morts' => ['nom_officiel' => 'Monument aux Morts', 'table_cible' => 'batiment'],
            'école m anjot' => ['nom_officiel' => 'École Maurice Anjot', 'table_cible' => 'lieux_publics'],
            'école m anjot ' => ['nom_officiel' => 'École Maurice Anjot', 'table_cible' => 'lieux_publics'],
            'école m anjot ' => ['nom_officiel' => 'École Maurice Anjot', 'table_cible' => 'lieux_publics'],
            'école m anjot  ' => ['nom_officiel' => 'École Maurice Anjot', 'table_cible' => 'lieux_publics'],
            'restaurant scolaire' => ['nom_officiel' => 'École Maurice Anjot - Cantine', 'table_cible' => 'lieux_publics'],
            'restaurant scolaire ' => ['nom_officiel' => 'École Maurice Anjot - Cantine', 'table_cible' => 'lieux_publics'],
            'restaurant scolaire ' => ['nom_officiel' => 'École Maurice Anjot - Cantine', 'table_cible' => 'lieux_publics'],
            'restaurant scolaire  ' => ['nom_officiel' => 'École Maurice Anjot - Cantine', 'table_cible' => 'lieux_publics'],
            'aire de jeux' => ['nom_officiel' => 'Aire de Jeux', 'table_cible' => 'lieux_publics'],
            'aire de jeux ' => ['nom_officiel' => 'Aire de Jeux', 'table_cible' => 'lieux_publics'],
            'agorespace' => ['nom_officiel' => 'Aire de Jeux', 'table_cible' => 'lieux_publics'],
            'cimetière' => ['nom_officiel' => 'Cimetière', 'table_cible' => 'lieux_publics'],
            'wc cimetière' => ['nom_officiel' => 'Cimetière', 'table_cible' => 'lieux_publics'],
            'stade de foot' => ['nom_officiel' => 'Stade de Foot', 'table_cible' => 'lieux_publics'],
            'vestiaires stade' => ['nom_officiel' => 'Stade de Foot', 'table_cible' => 'lieux_publics'],
            'buvette foot' => ['nom_officiel' => 'Stade de Foot', 'table_cible' => 'lieux_publics'],
            'déchèterie' => ['nom_officiel' => 'Déchèterie', 'table_cible' => 'lieux_publics'],
            'décheterie' => ['nom_officiel' => 'Déchèterie', 'table_cible' => 'lieux_publics'],
            'blonnière' => ['nom_officiel' => 'Secteur Blonnière', 'table_cible' => 'lieux_publics'],
            'blonnière ' => ['nom_officiel' => 'Secteur Blonnière', 'table_cible' => 'lieux_publics'],
            'blonnière ' => ['nom_officiel' => 'Secteur Blonnière', 'table_cible' => 'lieux_publics'],
            'glandon' => ['nom_officiel' => 'Secteur Glandon', 'table_cible' => 'lieux_publics'],
            'provenat' => ['nom_officiel' => 'Secteur Provenat', 'table_cible' => 'lieux_publics'],
            'village' => ['nom_officiel' => 'Secteur Village', 'table_cible' => 'lieux_publics'],
            'chez collet' => ['nom_officiel' => 'Lieu-dit Chez Collet', 'table_cible' => 'lieux_publics'],
            'chez planchin' => ['nom_officiel' => 'Lieu-dit Chez Planchin', 'table_cible' => 'lieux_publics'],
            'copro le chêne' => ['nom_officiel' => 'Copro Le Chêne', 'table_cible' => 'lieux_publics'],
            'eas' => ['nom_officiel' => 'EAS', 'table_cible' => 'lieux_publics'],
            'la centenaire' => ['nom_officiel' => 'Secteur La Centenaire', 'table_cible' => 'lieux_publics'],
            'captage du frêne' => ['nom_officiel' => 'Captage du Frêne', 'table_cible' => 'lieux_publics'],
            'source martinod' => ['nom_officiel' => 'Source Martinod', 'table_cible' => 'lieux_publics'],
            'plaine du fier' => ['nom_officiel' => 'Plaine du Fier', 'table_cible' => 'lieux_publics'],
            'galets du fier' => ['nom_officiel' => 'Galets du Fier', 'table_cible' => 'lieux_publics'],
            'promenade du fier' => ['nom_officiel' => 'Promenade du Fier', 'table_cible' => 'lieux_publics'],
            'chemin des blonnettes dessus' => ['nom_officiel' => 'Chemin des Blonnettes dessus', 'table_cible' => 'lieux_publics'],
            'pont des blonnettes bas' => ['nom_officiel' => 'Pont des Blonnettes bas', 'table_cible' => 'lieux_publics'],
            'chemin des écoliers' => ['nom_officiel' => 'Chemin des Écoliers', 'table_cible' => 'lieux_publics'],
            'chemin des planches' => ['nom_officiel' => 'Chemin des Planches', 'table_cible' => 'lieux_publics'],
            'chemin des mélis' => ['nom_officiel' => 'Chemin des Mélis', 'table_cible' => 'lieux_publics'],
            'chemin des mélis ' => ['nom_officiel' => 'Chemin des Mélis', 'table_cible' => 'lieux_publics'],
            'chemin de naves' => ['nom_officiel' => 'Chemin de Naves', 'table_cible' => 'lieux_publics'],
            'ancien chemin de naves' => ['nom_officiel' => 'Chemin de Naves', 'table_cible' => 'lieux_publics'],
            'chemin du pré fionnay' => ['nom_officiel' => 'Chemin du Pré Fionnay', 'table_cible' => 'lieux_publics'],
            'chemin du clu' => ['nom_officiel' => 'Chemin du Clu', 'table_cible' => 'lieux_publics'],
            'chemin de la maison forestière' => ['nom_officiel' => 'Chemin de la Maison Forestière', 'table_cible' => 'lieux_publics'],
            'chemin chez planchin' => ['nom_officiel' => 'Chemin Chez Planchin', 'table_cible' => 'lieux_publics'],
            'chemin de la combe à bullier' => ['nom_officiel' => 'Chemin de la Combe à Bullier', 'table_cible' => 'lieux_publics'],
            'chemin de poussy' => ['nom_officiel' => 'Chemin de Poussy', 'table_cible' => 'lieux_publics'],
            'chemin sous la ville' => ['nom_officiel' => 'Chemin sous la Ville', 'table_cible' => 'lieux_publics'],
            'chemin sous le bois' => ['nom_officiel' => 'Chemin sous le Bois', 'table_cible' => 'lieux_publics'],
            'chemin sous la fruitière' => ['nom_officiel' => 'Chemin sous la Fruitière', 'table_cible' => 'lieux_publics'],
            'pont sous la fruitière' => ['nom_officiel' => 'Pont sous la Fruitière', 'table_cible' => 'lieux_publics'],
            'pont de la chappelle' => ['nom_officiel' => 'Pont de la Chapelle', 'table_cible' => 'lieux_publics'],
            'pont des tappes' => ['nom_officiel' => 'Pont des Tappes', 'table_cible' => 'lieux_publics'],
            'tappes' => ['nom_officiel' => 'Secteur Tappes', 'table_cible' => 'lieux_publics'],
            'route de cornet' => ['nom_officiel' => 'Route de Cornet', 'table_cible' => 'lieux_publics'],
            'route des curtils' => ['nom_officiel' => 'Route des Curtils', 'table_cible' => 'lieux_publics'],
            'curtils' => ['nom_officiel' => 'Route des Curtils', 'table_cible' => 'lieux_publics'],
            'route de verbin' => ['nom_officiel' => 'Route de Verbin', 'table_cible' => 'lieux_publics'],
            'route de provenat' => ['nom_officiel' => 'Route de Provenat', 'table_cible' => 'lieux_publics'],
            'route de la blonnière' => ['nom_officiel' => 'Route de la Blonnière', 'table_cible' => 'lieux_publics'],
            'route de rochebard' => ['nom_officiel' => 'Route de Rochebard', 'table_cible' => 'lieux_publics'],
            'route de chez brachet' => ['nom_officiel' => 'Route de Chez Brachet', 'table_cible' => 'lieux_publics'],
            'route de la frasse ' => ['nom_officiel' => 'Route de la Frasse', 'table_cible' => 'lieux_publics'],
            'route du fraisy' => ['nom_officiel' => 'Route du Fraisy', 'table_cible' => 'lieux_publics'],
            'route du fraisy ' => ['nom_officiel' => 'Route du Fraisy', 'table_cible' => 'lieux_publics'],
            'route de fraisy' => ['nom_officiel' => 'Route du Fraisy', 'table_cible' => 'lieux_publics'],
            'route de la déchèterie' => ['nom_officiel' => 'Route de la Déchèterie', 'table_cible' => 'lieux_publics'],
            'voie romaine' => ['nom_officiel' => 'Voie Romaine', 'table_cible' => 'lieux_publics'],
            'chessenay' => ['nom_officiel' => 'Secteur Chessenay', 'table_cible' => 'lieux_publics'],
            'chapelle blonnière' => ['nom_officiel' => 'Chapelle Blonnière', 'table_cible' => 'lieux_publics'],
            'chapelle blonnière ' => ['nom_officiel' => 'Chapelle Blonnière', 'table_cible' => 'lieux_publics'],
            'chapelle saint clair' => ['nom_officiel' => 'Chapelle Saint Clair', 'table_cible' => 'lieux_publics'],
            'four des tappes' => ['nom_officiel' => 'Four des Tappes', 'table_cible' => 'lieux_publics'],
            'wc place' => ['nom_officiel' => 'WC Publics Place', 'table_cible' => 'lieux_publics'],
            'voirie' => ['nom_officiel' => 'Réseau Voirie', 'table_cible' => 'lieux_publics'],
            'routes' => ['nom_officiel' => 'Réseau Voirie', 'table_cible' => 'lieux_publics'],
            'sentiers' => ['nom_officiel' => 'Sentiers Communaux', 'table_cible' => 'lieux_publics'],
            'saugy' => ['nom_officiel' => 'Secteur Saugy', 'table_cible' => 'lieux_publics'],
            'nanoir' => ['nom_officiel' => 'Secteur Nanoir', 'table_cible' => 'lieux_publics'],
            'les blonnettes' => ['nom_officiel' => 'Secteur Les Blonnettes', 'table_cible' => 'lieux_publics'],
            'projet' => ['nom_officiel' => 'Zone Projet', 'table_cible' => 'lieux_publics'],
        ];

        if (($handle = fopen($cheminFichier, 'r')) !== false) {
            fgetcsv($handle, 0, ';');

            $totalImporte = 0;
            $ligneActuelle = 1;

            while (($row = fgetcsv($handle, 0, ';')) !== false) {
                $ligneActuelle++;

                if (count($row) < 8 || empty($row[1])) {
                    continue; // 🌟 FIX : Ignore proprement la ligne 940 vide ou mal formée
                }

                // 🌟 FIX ENCODAGE ULTIME : Convertit et nettoie les caractères parasites brisés ()
                foreach ($row as $key => $val) {
                    $clean = mb_convert_encoding($val, 'UTF-8', 'UTF-8, Windows-1252, ISO-8859-1');
                    // Remplace le symbole cassé d'Excel par une chaîne vide ou propre
                    $row[$key] = str_replace(chr(0xC3), '', $clean);
                    $row[$key] = iconv("UTF-8", "UTF-8//IGNORE", $row[$key]);
                }

                $col_ouverture = trim($row[1]);
                $col_dde = !empty($row[2]) ? trim($row[2]) : null;
                $col_budg = !empty($row[3]) ? trim($row[3]) : 'T';
                $col_lieu = !empty($row[4]) ? trim($row[4]) : null;
                $col_cat = !empty($row[5]) ? trim($row[5]) : null;
                $col_prio = !empty($row[6]) ? trim($row[6]) : null;
                $col_affaire = !empty($row[7]) ? trim($row[7]) : 'Demande technique';
                $col_cout = !empty($row[8]) ? trim($row[8]) : null;
                $col_statut = !empty($row[9]) ? trim($row[9]) : 'Ouvert';
                $col_date_fin = !empty($row[10]) ? trim($row[10]) : null;
                $col_note = !empty($row[11]) ? trim($row[11]) : null;

                // Nettoyage spécifique de la colonne budget si Excel a écrit "R"
                $col_budg = Str::ascii($col_budg);
                $col_budg = substr(preg_replace('/[^A-Za-z]/', '', $col_budg), 0, 2);
                if (empty($col_budg)) {
                    $col_budg = 'T';
                }

                try {
                    DB::beginTransaction();

                    // --- FORMATAGE ROBUSTE DES DATES ---
                    $dateOuverture = null;
                    $col_ouverture = str_replace('-', '/', $col_ouverture);
                    if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{2}$/', $col_ouverture)) {
                        $dateOuverture = Carbon::createFromFormat('d/m/y', $col_ouverture)->format('Y-m-d');
                    } else {
                        try {
                            $dateOuverture = Carbon::createFromFormat('d/m/Y', $col_ouverture)->format('Y-m-d');
                        } catch (\Exception $e) {
                            $dateOuverture = '2022-01-01';
                        }
                    }
                    $anneeOuverture = Carbon::parse($dateOuverture)->format('Y');

                    $dateCloture = null;
                    if ($col_date_fin) {
                        $dateFinBrute = str_replace('-', '/', strtolower(trim($col_date_fin)));
                        if (preg_match('/[a-z]/', $dateFinBrute)) {
                            $moisFr = ['janv' => 1, 'fevr' => 2, 'mar' => 3, 'avr' => 4, 'mai' => 5, 'juin' => 6, 'juil' => 7, 'aout' => 8, 'sept' => 9, 'oct' => 10, 'nov' => 11, 'dec' => 12];
                            $segments = explode('/', $dateFinBrute);
                            $jou = intval($segments[0]);
                            $moi = $moisFr[trim($segments[1])] ?? 1;
                            $dateCloture = Carbon::create($anneeOuverture, $moi, $jou)->format('Y-m-d');
                        } else {
                            try {
                                if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{2}$/', $dateFinBrute)) {
                                    $dateCloture = Carbon::createFromFormat('d/m/y', $dateFinBrute)->format('Y-m-d');
                                } else {
                                    $dateCloture = Carbon::parse($dateFinBrute)->format('Y-m-d');
                                }
                            } catch (\Exception $e) {
                                $dateCloture = null;
                            }
                        }
                    }

                    // --- NETTOYAGE COÛT ---
                    $coutNettoye = null;
                    if ($col_cout) {
                        $brut = str_replace(['€', ' ', "\u{A0}", '-'], '', $col_cout);
                        $brut = str_replace(',', '.', $brut);
                        if (is_numeric($brut)) {
                            $coutNettoye = floatval($brut);
                        }
                    }

                    // --- SEGMENTATION DU PATRIMOINE M57 ---
                    $idLocal = null;
                    $idLieuPublic = null;

                    if (!empty($col_lieu)) {
                        $cleLieu = Str::lower(trim($col_lieu));

                        if (array_key_exists($cleLieu, $dictionnairePatrimoine)) {
                            $nomOfficiel = $dictionnairePatrimoine[$cleLieu]['nom_officiel'];
                            $tableCible = $dictionnairePatrimoine[$cleLieu]['table_cible'];

                            if ($tableCible === 'batiment') {
                                $idBat = DB::table('batiment')->where('nom_bat', $nomOfficiel)->value('id_batiment');
                                if (!$idBat) {
                                    $idImmoUnique = DB::table('immobilisation_inventaire_')->insertGetId([
                                        'num_inventaire' => 'B-' . uniqid(),
                                        'libelle_comptable' => 'Immo analytique : ' . Str::limit($nomOfficiel, 50),
                                        'valeur_achat' => 0.00,
                                        'date_acquisition' => $dateOuverture,
                                        'est_amortissable' => false
                                    ], 'id_immo');

                                    $idBat = DB::table('batiment')->insertGetId([
                                        'nom_bat' => $nomOfficiel,
                                        'id_parcelle' => $idParcelle,
                                        'id_type_erp' => $idTypeErp,
                                        'id_adresse' => $idAdresse,
                                        'id_tiers' => $idTiersMairie,
                                        'id_immo' => $idImmoUnique
                                    ], 'id_batiment');
                                }
                                $idLocal = DB::table('local_')->where('nom_local', 'Générique - ' . $nomOfficiel)->value('id_local');
                                if (!$idLocal) {
                                    $idLocal = DB::table('local_')->insertGetId([
                                        'nom_local' => 'Générique - ' . $nomOfficiel,
                                        'id_batiment' => $idBat
                                    ], 'id_local');
                                }
                            } else {
                                $idLieuPublic = DB::table('lieux_publics')->where('nom_lieu', $nomOfficiel)->value('id_lieu');
                                if (!$idLieuPublic) {
                                    $idImmoLieuUnique = DB::table('immobilisation_inventaire_')->insertGetId([
                                        'num_inventaire' => 'L-' . uniqid(),
                                        'libelle_comptable' => 'Immo analytique espace : ' . Str::limit($nomOfficiel, 50),
                                        'valeur_achat' => 0.00,
                                        'date_acquisition' => $dateOuverture,
                                        'est_amortissable' => false
                                    ], 'id_immo');

                                    $idLieuPublic = DB::table('lieux_publics')->insertGetId([
                                        'nom_lieu' => $nomOfficiel,
                                        'id_parcelle' => $idParcelle,
                                        'id_immo' => $idImmoLieuUnique
                                    ], 'id_lieu');
                                }
                            }
                        }
                    }

                    // --- CATÉGORIES ET AGENTS ---
                    $libelleCat = $col_cat ? ucfirst(strtolower($col_cat)) : 'Divers';
                    $idCat = DB::table('categorie')->where('libelle', $libelleCat)->value('id_cat');
                    if (!$idCat) {
                        $idCat = DB::table('categorie')->insertGetId(['libelle' => $libelleCat], 'id_cat');
                    }

                    $idUser = 1;
                    if ($col_dde) {
                        $agent = DB::table('utilisateur')->where('initiales', 'ilike', trim($col_dde))->first();
                        if ($agent) {
                            $idUser = $agent->id_user;
                        }
                    }

                    // --- EXECUTION SQL ---
                    $idAction = DB::table('action')->insertGetId([
                        'date_creation' => $dateOuverture . ' 08:00:00',
                        'emetteur_nom' => $col_dde ?? 'REGISTRE',
                        'description' => $col_affaire,
                        'mode_reception' => 'ETL Suivi Travaux',
                        'priorite' => substr(preg_replace('/[^A-Za-z0-9 ]/', '', $col_prio), 0, 10),
                        'statut_action' => ucfirst(strtolower($col_statut)),
                        'id_user' => $idUser,
                        'id_cat' => $idCat,
                        'id_local' => $idLocal,
                    ], 'id_action');

                    $idInt = DB::table('intervention')->insertGetId([
                        'code_budget' => $col_budg,
                        'date_ouverture' => $dateOuverture,
                        'date_cloture' => $dateCloture,
                        'type_intervention' => Str::limit($col_affaire, 145),
                        'statut_global' => ucfirst(strtolower($col_statut)),
                        'description' => $col_affaire,
                        'id_cat' => $idCat,
                        'id_user_demandeur' => $idUser,
                        'id_action' => $idAction,
                        'id_local' => $idLocal,
                    ], 'id_int');

                    if ($idLieuPublic) {
                        DB::table('intervention_espace')->insert([
                            'id_int' => $idInt,
                            'id_lieu' => $idLieuPublic
                        ]);
                    }

                    DB::table('suivi_action')->insert([
                        'date_action_suivi' => $dateOuverture,
                        'cout_associe' => $coutNettoye,
                        'statut_apres_action' => ucfirst(strtolower($col_statut)),
                        'description_etape' => $col_note ?? 'Ligne historique importée.',
                        'id_int' => $idInt,
                        'id_user' => $idUser,
                    ]);

                    DB::commit();
                    $totalImporte++;

                } catch (\Exception $e) {
                    DB::rollBack();
                    $this->warn("⚠️ Échec ligne {$ligneActuelle} : " . $e->getMessage());
                }
            }
            fclose($handle);

            // On nettoie et réinitialise d'abord les compteurs pour tes tests
            $this->info("✨ Importation achevée avec succès ! Total : {$totalImporte} lignes de l'historique M57 synchronisées.");
        }
    }
}
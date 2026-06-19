<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CompteurEtlSeeder extends Seeder
{
    public function run(): void
    {
        // Ingestion exhaustive et structurée de tes 19 lignes métiers
        $rows = [
            ['pds' => '7410200041', 'intra' => '00041', 'bat' => 'Réservoir', 'equip' => 'Général', 'num' => 'I14BA090917', 'pose' => '24/08/2021', 'arret' => null, 'sub' => null, 'num_rue' => null, 'voie' => 'CHEMIN DU GRAND BASSIN', 'actions' => ['pas de consommation depuis 2 ans']],
            ['pds' => '7410200079', 'intra' => '00079', 'bat' => 'EAS', 'equip' => 'Vestiaire Foot', 'num' => 'I23BD098115', 'pose' => '22/02/2024', 'arret' => null, 'sub' => null, 'num_rue' => 27, 'voie' => 'ROUTE DU FIER', 'actions' => ['changer adresse INTRANET', 'créer descriptif']],
            ['pds' => '7410200080', 'intra' => '00080', 'bat' => 'Fontaine Déchèterie', 'equip' => 'Général', 'num' => 'I23IA015979', 'pose' => '22/05/2024', 'arret' => null, 'sub' => null, 'num_rue' => null, 'voie' => 'ROUTE DE PROVENAT', 'actions' => ['changer adresse INTRANET', 'créer descriptif']],
            ['pds' => '7410211750', 'intra' => '11750', 'bat' => 'WC Public', 'equip' => 'Sanitaires', 'num' => 'I20IA044788', 'pose' => '08/01/2021', 'arret' => null, 'sub' => null, 'num_rue' => 87, 'voie' => "PLACE DE L'EGLISE", 'actions' => ['31m3 depuis le 03/10 Beber tp', 'comment estce télérelevé ?', 'supprimer']],
            ['pds' => '7410211762', 'intra' => '11762', 'bat' => 'maison des sœurs', 'equip' => 'Général', 'num' => 'I20IA044839', 'pose' => '01/07/2021', 'arret' => null, 'sub' => null, 'num_rue' => 75, 'voie' => 'ROUTE DE CHEZ BRACHET', 'actions' => ['vérifier N° compteur dans batiment', 'changer adresse INTRANET', 'créer descriptif']],
            ['pds' => '7410211894', 'intra' => '11894', 'bat' => 'Mairie', 'equip' => 'Général', 'num' => 'I21JB023945', 'pose' => '20/04/2024', 'arret' => '20/04/2024', 'sub' => '741021339001', 'num_rue' => 480, 'voie' => 'ROUTE DU CHEF LIEU', 'actions' => ['vérifier N° compteur dans batiment', 'changer adresse INTRANET', 'créer descriptif']],
            ['pds' => '7410211895', 'intra' => '11895', 'bat' => 'Cimetière', 'equip' => 'Général', 'num' => 'I20IA044806', 'pose' => '08/07/2021', 'arret' => null, 'sub' => null, 'num_rue' => 500, 'voie' => 'ROUTE DU CHEF LIEU', 'actions' => ['alimente quoi ?', 'changer adresse INTRANET', 'créer descriptif']],
            ['pds' => '7410211896', 'intra' => '11896', 'bat' => 'Mairie', 'equip' => 'Général Mairie', 'num' => 'I21IA580880', 'pose' => '19/10/2021', 'arret' => null, 'sub' => null, 'num_rue' => 55, 'voie' => "PLACE DE L'EGLISE", 'actions' => ['changer adresse INTRANET', 'créer descriptif']],
            ['pds' => '7410211897', 'intra' => '11897', 'bat' => 'Crèche', 'equip' => 'Général', 'num' => 'I21IA535414', 'pose' => '19/10/2021', 'arret' => null, 'sub' => null, 'num_rue' => 33, 'voie' => 'ROUTE DE CHEZ BRACHET', 'actions' => ['changer adresse INTRANET', 'créer descriptif']],
            ['pds' => '7410211898', 'intra' => '11898', 'bat' => 'Presbytére', 'equip' => 'Général', 'num' => 'I21IA580883', 'pose' => '19/10/2021', 'arret' => null, 'sub' => null, 'num_rue' => 52, 'voie' => 'ROUTE DE CHEZ BRACHET', 'actions' => ['changer adresse INTRANET', 'créer descriptif', 'supprimer']],
            ['pds' => '7410211900', 'intra' => '11900', 'bat' => 'école Maurice Anjot', 'equip' => 'Général', 'num' => 'I19BC056583', 'pose' => '24/07/2020', 'arret' => null, 'sub' => null, 'num_rue' => 65, 'voie' => "PLACE DE L'EGLISE", 'actions' => ['vérifier N° compteur dans batiment', 'créer descriptif']],
            ['pds' => '7410211901', 'intra' => '11901', 'bat' => 'la ruche citoyenne', 'equip' => 'Général', 'num' => 'I21JB033358', 'pose' => '07/10/2021', 'arret' => null, 'sub' => null, 'num_rue' => 30, 'voie' => 'CHEMIN DE LA MAISON FORESTIERE', 'actions' => ['vérifier N° compteur dans batiment', 'créer descriptif']],
            ['pds' => '7410211991', 'intra' => '11991', 'bat' => 'Bibliothéque', 'equip' => 'Général', 'num' => 'I20IA018423', 'pose' => '08/07/2021', 'arret' => null, 'sub' => 'WC publics', 'num_rue' => 87, 'voie' => "PLACE DE L'EGLISE", 'actions' => ['27m3 depuis l\'ouverture le 03/10/24 Beber tp', 'créer descriptif']],
            ['pds' => '7410212102', 'intra' => '12102', 'bat' => 'Services techniques', 'equip' => 'Général', 'num' => 'I21JB023965', 'pose' => '08/07/2021', 'arret' => null, 'sub' => null, 'num_rue' => 20, 'voie' => 'CHEMIN DE LA MAISON FORESTIERE', 'actions' => ['vérifier N° compteur dans batiment', 'changer adresse INTRANET', 'créer descriptif']],
            ['pds' => '741020039001', 'intra' => '0039001', 'bat' => 'Mairie', 'equip' => 'appart nord', 'num' => 'I21IA535415', 'pose' => null, 'arret' => null, 'sub' => null, 'num_rue' => 59, 'voie' => "PLACE DE L'EGLISE", 'actions' => ['vérifier N° compteur dans batiment', 'changer adresse INTRANET', 'créer descriptif', 'supprimer']],
            ['pds' => '741021341901', 'intra' => '1341901', 'bat' => 'Espace Michel Doche', 'equip' => 'Cantine', 'num' => 'I21JB033357', 'pose' => null, 'arret' => null, 'sub' => null, 'num_rue' => null, 'voie' => null, 'actions' => ['vérifier N° compteur dans batiment', 'créer adresse', 'créer descriptif']],
            ['pds' => '7410212100001', 'intra' => '12100001', 'bat' => 'Mairie', 'equip' => 'appart sud', 'num' => 'I21IA580882', 'pose' => null, 'arret' => null, 'sub' => null, 'num_rue' => 49, 'voie' => "PLACE DE L'EGLISE", 'actions' => ['vérifier N° compteur dans batiment', 'changer adresse INTRANET', 'créer descriptif', 'supprimer']],
            ['pds' => '7410212100002', 'intra' => '12100002', 'bat' => 'Cimetière', 'equip' => 'WC PUBLICS CIMETIERE', 'num' => 'I20IA044790', 'pose' => null, 'arret' => null, 'sub' => null, 'num_rue' => 500, 'voie' => 'ROUTE DU CHEF LIEU', 'actions' => []],
            ['pds' => '7410212100003', 'intra' => '12100003', 'bat' => 'Espace Michel Doche', 'equip' => 'Salle Doche', 'num' => 'I21IA580879', 'pose' => null, 'arret' => null, 'sub' => null, 'num_rue' => 85, 'voie' => 'RTE DE LA BLONNIERE', 'actions' => ['vérifier N° compteur dans batiment', 'changer adresse INTRANET', 'créer descriptif']],
        ];

        $idUser = DB::table('utilisateur')->value('id_user') ?? 1;
        $idCat = DB::table('categorie')->value('id_cat') ?? 1;
        $idLieuDit = DB::table('lieu_dit')->value('id_lieu_dit') ?? 1;

        $idTypeErpDefault = DB::table('type_erp')->value('id_type_erp') ?? DB::table('type_erp')->insertGetId([
            'reglementation_applicable' => 'Règlement sécurité incendie standard',
            'categorie_erp' => 5,
            'type_erp' => 'W'
        ], 'id_type_erp');

        foreach ($rows as $row) {
            DB::transaction(function () use ($row, $idUser, $idCat, $idLieuDit, $idTypeErpDefault) {

                // =========================================================================
                // 1. SYNC INTÉLLIGENTE ADRESSE (DÉDUPLICATION BAN VIA LE NOM DE LA VOIE)
                // =========================================================================
                $idAdresseFinal = null;

                if (!empty($row['voie'])) {
                    $voieNettoyee = str_replace(['RTE', 'PL ', 'PL.'], ['ROUTE', 'PLACE ', 'PLACE'], strtoupper($row['voie']));

                    $adresseExistante = DB::table('Adresse')
                        ->where('nom_voie', 'ilike', '%' . trim($voieNettoyee) . '%')
                        ->first();

                    if ($adresseExistante) {
                        $idAdresseFinal = $adresseExistante->id_adresse;
                    } else {
                        $idAdresseFinal = DB::table('Adresse')->insertGetId([
                            'num_rue' => $row['num_rue'],
                            'nom_voie' => trim($row['voie']),
                            'code_postal' => '74230',
                            'ville' => 'Dingy-Saint-Clair',
                            'id_lieu_dit' => $idLieuDit
                        ], 'id_adresse');
                    }
                }

                if (!$idAdresseFinal) {
                    $idAdresseFinal = DB::table('Adresse')->value('id_adresse') ?? $idLieuDit;
                }

                // =========================================================================
                // 2. MAILLAGE DES STRUCTURES PARENTES (DÉCOUPAGE STRICT BÂTIMENT VS LIEU PUBLIC)
                // =========================================================================
                $idBatimentParent = null;
                $idLieuParent = null;

                if (!empty($row['bat'])) {
                    $nomStructure = trim($row['bat']);

                    // 🎯 Filtrage strict selon tes directives
                    $estUnLieuPublic = in_array(strtolower($nomStructure), [
                        'cimetière',
                        'fontaine déchèterie',
                        'fontaine decheterie',
                        'réservoir'
                    ]);

                    if ($estUnLieuPublic) {
                        // A. Table lieux_publics (Espaces communaux extérieurs)
                        $lieuExistant = DB::table('lieux_publics')->where('nom_lieu', 'ilike', $nomStructure)->first();
                        if ($lieuExistant) {
                            $idLieuParent = $lieuExistant->id_lieu;
                        } else {
                            $idLieuParent = DB::table('lieux_publics')->insertGetId([
                                'nom_lieu' => $nomStructure,
                                'typologie_lieu' => 'Espace Communal Ouvert',
                                'id_type_erp' => $idTypeErpDefault
                            ], 'id_lieu');
                        }
                    } else {
                        // B. Table batiment (Espaces communaux bâtis/couverts)
                        $batimentExistant = DB::table('batiment')->where('nom_bat', 'ilike', $nomStructure)->first();
                        if ($batimentExistant) {
                            $idBatimentParent = $batimentExistant->id_batiment;
                        } else {
                            // LIGNE CORRIGÉE ICI : 'id_parcelle' retiré
                            $idBatimentParent = DB::table('batiment')->insertGetId([
                                'nom_bat' => $nomStructure,
                                'id_type_erp' => $idTypeErpDefault,
                                'id_adresse' => $idAdresseFinal
                            ], 'id_batiment');
                        }
                    }
                }

                // =========================================================================
                // 3. RECHERCHE, CLAUSE DE NON-REDUNDANCE ET VERIFICATION DU LOCAL INTERNE
                // =========================================================================
                $idLocalFinal = null;
                $nomDuLocalCible = !empty($row['equip']) ? trim($row['equip']) : 'Général';

                if ($idBatimentParent) {
                    $localExistant = DB::table('local_')
                        ->where('id_batiment', $idBatimentParent)
                        ->where('nom_local', 'ilike', $nomDuLocalCible)
                        ->first();
                    if ($localExistant)
                        $idLocalFinal = $localExistant->id_local;
                } elseif ($idLieuParent) {
                    $localExistant = DB::table('local_')
                        ->where('id_lieu', $idLieuParent)
                        ->where('nom_local', 'ilike', $nomDuLocalCible)
                        ->first();
                    if ($localExistant)
                        $idLocalFinal = $localExistant->id_local;
                } else {
                    $localExistant = DB::table('local_')
                        ->whereNull('id_batiment')
                        ->whereNull('id_lieu')
                        ->where('nom_local', 'ilike', $nomDuLocalCible)
                        ->first();
                    if ($localExistant)
                        $idLocalFinal = $localExistant->id_local;
                }

                // Si le local n'existe pas encore sous la structure parente rattachée, on le crée
                if (!$idLocalFinal) {
                    $idLocalFinal = DB::table('local_')->insertGetId([
                        'nom_local' => substr($nomDuLocalCible, 0, 80),
                        'statut_occupation' => 'Occupé',
                        'id_batiment' => $idBatimentParent,
                        'id_lieu' => $idLieuParent
                    ], 'id_local');
                }

                // =========================================================================
                // 4. INGESTION SÉCURISÉE DES COMPTEURS D'EAU POTABLE
                // =========================================================================
                if (!empty($row['pds'])) {
                    $datePose = $row['pose'] ? Carbon::createFromFormat('d/m/Y', $row['pose'])->format('Y-m-d') : now();
                    $dateArret = $row['arret'] ? Carbon::createFromFormat('d/m/Y', $row['arret'])->format('Y-m-d') : null;

                    $compteurExiste = DB::table('compteur')->where('point_comptage', $row['pds'])->exists();

                    if (!$compteurExiste) {
                        DB::table('compteur')->insert([
                            'point_comptage' => $row['pds'],
                            'numero_compteur' => $row['num'] ?: ($row['sub'] ?: 'SANS_NUM_' . $row['intra']),
                            'type_reseau' => 'EAU POTABLE',
                            'dessert_tout_le_batiment' => ($nomDuLocalCible === 'Général' || $nomDuLocalCible === 'Général Mairie'),
                            'unite_mesure' => 'm3',
                            'date_pose' => $datePose,
                            'date_arret' => $dateArret,
                            'observations' => 'Réf Intranet: ' . $row['intra'] . ($row['sub'] ? ' - Lien : ' . $row['sub'] : ''),
                            'id_local' => $idLocalFinal
                        ]);
                    }
                }

                // =========================================================================
                // 5. INGESTION DES TICKETS ET SIGNALEMENTS EMIS
                // =========================================================================
                foreach ($row['actions'] as $actionTxt) {
                    if (empty($actionTxt))
                        continue;

                    DB::table('action')->insert([
                        'date_creation' => now(),
                        'emetteur_nom' => 'Service Eau (Automatique)',
                        'description' => "Vérification Compteur PDS " . $row['pds'] . " : " . $actionTxt,
                        'mode_reception' => 'Interne',
                        'priorite' => 'Normale',
                        'statut_action' => 'Nouveau',
                        'id_user' => $idUser,
                        'id_local' => $idLocalFinal,
                        'id_adresse' => $idAdresseFinal,
                        'id_cat' => $idCat
                    ]);
                }
            });
        }

        $this->command->info('✨ ETL Exécuté avec succès ! Architecture du patrimoine et déduplication BAN validées.');
    }
}
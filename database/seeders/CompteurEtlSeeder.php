<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CompteurEtlSeeder extends Seeder
{
    public function run(): void
    {
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

        // On récupère les valeurs par défaut
        $idUser = DB::table('utilisateur')->value('id_user') ?? 1;
        $idCat = DB::table('categorie')->where('libelle', 'ilike', 'Plomberie')->value('id_cat') ?? (DB::table('categorie')->value('id_cat') ?? 1);

        $nbActionsInserees = 0;

        foreach ($rows as $row) {

            // Si la ligne n'a aucune action, on passe à la suivante directement
            if (empty($row['actions'])) {
                continue;
            }

            // 1. RECHERCHE DE L'ADRESSE (Mode Lecture Seule)
            $idAdresseFinal = null;
            if (!empty($row['voie'])) {
                $voieNettoyee = str_replace(['RTE', 'PL ', 'PL.'], ['ROUTE', 'PLACE ', 'PLACE'], strtoupper($row['voie']));
                $idAdresseFinal = DB::table('Adresse')
                    ->where('nom_voie', 'ilike', '%' . trim($voieNettoyee) . '%')
                    ->value('id_adresse'); // value() renvoie directement l'ID ou null
            }

            // 2. RECHERCHE BÂTIMENT OU LIEU PUBLIC (Mode Lecture Seule)
            $idBatimentParent = null;
            $idLieuParent = null;

            if (!empty($row['bat'])) {
                $nomStructure = trim($row['bat']);

                // On cherche d'abord dans les bâtiments
                $idBatimentParent = DB::table('batiment')
                    ->where('nom_bat', 'ilike', $nomStructure)
                    ->value('id_batiment');

                // Si ce n'est pas un bâtiment, on cherche dans les lieux publics
                if (!$idBatimentParent) {
                    $idLieuParent = DB::table('lieux_publics')
                        ->where('nom_lieu', 'ilike', $nomStructure)
                        ->value('id_lieu');
                }
            }

            // 3. RECHERCHE DU LOCAL (Mode Lecture Seule)
            $idLocalFinal = null;
            $nomDuLocalCible = !empty($row['equip']) ? trim($row['equip']) : 'Général';

            if ($idBatimentParent) {
                $idLocalFinal = DB::table('local_')
                    ->where('id_batiment', $idBatimentParent)
                    ->where('nom_local', 'ilike', $nomDuLocalCible)
                    ->value('id_local');
            } elseif ($idLieuParent) {
                $idLocalFinal = DB::table('local_')
                    ->where('id_lieu', $idLieuParent)
                    ->where('nom_local', 'ilike', $nomDuLocalCible)
                    ->value('id_local');
            } else {
                // Si on a ni batiment ni lieu, on cherche juste par le nom du local
                $idLocalFinal = DB::table('local_')
                    ->whereNull('id_batiment')
                    ->whereNull('id_lieu')
                    ->where('nom_local', 'ilike', $nomDuLocalCible)
                    ->value('id_local');
            }

            // 4. INSERTION DES ACTIONS (Mode Écriture)
            foreach ($row['actions'] as $actionTxt) {
                if (empty(trim($actionTxt)))
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
                    'id_batiment' => $idBatimentParent,
                    'id_lieu' => $idLieuParent,
                    'id_adresse' => $idAdresseFinal,
                    'id_cat' => $idCat
                ]);

                $nbActionsInserees++;
            }
        }

        $this->command->info("✨ Succès : {$nbActionsInserees} actions de compteurs ont été recréées et parfaitement reliées à l'infrastructure existante !");
    }
}
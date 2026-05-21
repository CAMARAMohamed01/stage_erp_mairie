<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VrdTestSeeder extends Seeder
{
    public function run()
    {
        // ---------------------------------------------------
        // ÉTAPE 1 : Création ou récupération des dépendances
        // ---------------------------------------------------

        $secteur = DB::table('secteur')->where('code_secteur', 'SN-01')->first();
        $idSecteur = $secteur ? $secteur->id_secteur : DB::table('secteur')->insertGetId([
            'nom_secteur' => 'Secteur Nord',
            'code_secteur' => 'SN-01'
        ], 'id_secteur');

        $zone = DB::table('Zone')->where('code_zone', 'ZP-A')->first();
        $idZone = $zone ? $zone->id_zone : DB::table('Zone')->insertGetId([
            'nom_zone' => 'Zone Périurbaine',
            'code_zone' => 'ZP-A',
            'id_secteur' => $idSecteur
        ], 'id_zone');

        $famille = DB::table('famille_equipement')->where('libelle_famille', 'Signalisation routière')->first();
        $idFamille = $famille ? $famille->id_famille : DB::table('famille_equipement')->insertGetId([
            'libelle_famille' => 'Signalisation routière'
        ], 'id_famille');

        $service = DB::table('service_mairie')->where('nom_service', 'Services Techniques')->first();
        $idService = $service ? $service->id_service : DB::table('service_mairie')->insertGetId([
            'nom_service' => 'Services Techniques'
        ], 'id_service');

        $user = DB::table('utilisateur')->where('nom_user', 'Martin')->where('prenom_user', 'Paul')->first();
        $idUser = $user ? $user->id_user : DB::table('utilisateur')->insertGetId([
            'nom_user' => 'Martin',
            'prenom_user' => 'Paul',
            'role_appli' => 'Agent Technique',
            'id_service' => $idService
        ], 'id_user');

        $cat = DB::table('categorie')->where('libelle', 'Voirie & Réseaux')->first();
        $idCat = $cat ? $cat->id_cat : DB::table('categorie')->insertGetId([
            'libelle' => 'Voirie & Réseaux'
        ], 'id_cat');

        // ---------------------------------------------------
        // ÉTAPE 2 : Infrastructure (Voie, Ouvrage, Tronçon)
        // ---------------------------------------------------

        $voie = DB::table('voie')->where('numero_voie', 'VC01')->first();
        $idVoie = $voie ? $voie->id_voie : DB::table('voie')->insertGetId([
            'nom_voie' => 'Route d\'Annecy',
            'numero_voie' => 'VC01',
            'categorie_voie' => 'Voie Communale',
            'longueur_reelle_ml' => 1250,
            'largeur_moyenne_m' => 6.50,
            'statut_juridique' => 'Domaine public',
            'est_pdipr' => false
        ], 'id_voie');

        $ouvrage = DB::table('ouvrage')->where('nom_ouvrage', 'Pont de la rivière')->first();
        $idOuvrage = $ouvrage ? $ouvrage->id_ouvrage : DB::table('ouvrage')->insertGetId([
            'nom_ouvrage' => 'Pont de la rivière',
            'type_ouvrage' => 'Pont',
            'franchissement' => 'Cours d\'eau',
            'id_voie' => $idVoie
        ], 'id_ouvrage');

        $troncon = DB::table('troncon')->where('numero_troncon', 'TR-VC01-A')->first();
        $idTroncon = $troncon ? $troncon->id_troncon : DB::table('troncon')->insertGetId([
            'numero_troncon' => 'TR-VC01-A',
            'nom_portion' => 'Entrée agglomération',
            'pk_debut' => 0.00,
            'pk_fin' => 450.50,
            'type_revetement' => 'Enrobé à chaud',
            'etat_physique' => 'Moyen',
            'date_dernier_goudronnage' => Carbon::now()->subYears(4)->toDateString(),
            'id_voie' => $idVoie,
            'id_zone' => $idZone,
            'id_ouvrage_lie' => $idOuvrage
        ], 'id_troncon');

        // ---------------------------------------------------
        // ÉTAPE 3 : Vie du tronçon (Équipement & Intervention)
        // ---------------------------------------------------

        $equipementExists = DB::table('equipement')->where('nom_equipement', 'Panneau Limitation 50 km/h')->where('id_troncon', $idTroncon)->exists();
        if (!$equipementExists) {
            DB::table('equipement')->insert([
                'nom_equipement' => 'Panneau Limitation 50 km/h',
                'etat_fonctionnement' => 'Opérationnel',
                'id_troncon' => $idTroncon,
                'id_famille' => $idFamille
            ]);
        }

        $interventionExists = DB::table('intervention')->where('type_intervention', 'Rebouchage nid de poule')->where('id_troncon', $idTroncon)->exists();
        if (!$interventionExists) {
            DB::table('intervention')->insert([
                'type_intervention' => 'Rebouchage nid de poule',
                'statut_global' => 'En cours',
                'description' => 'Formation d\'un nid de poule dangereux au PK 150 suite aux dernières intempéries.',
                'date_ouverture' => Carbon::now()->subDays(2)->toDateString(),
                'id_troncon' => $idTroncon,
                'id_cat' => $idCat,
                // 'id_user' => $idUser,
                'id_user_demandeur' => $idUser
            ]);
        }

        $this->command->info('Jeu de données VRD (Voies, Tronçons, Ouvrages) vérifié et injecté avec succès ! 🚀');
    }
}
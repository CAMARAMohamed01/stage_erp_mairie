<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EquipementSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Cartographie précise des équipements avec leur famille correspondante
        $equipementsNomenclature = [
            ['nom' => 'Defibrillateurs', 'famille' => 'securité'],
            ['nom' => 'Agorespace', 'famille' => 'espaces verts'],
            ['nom' => 'ADOUCISSEUR', 'famille' => 'Plomberie'],
            ['nom' => 'tonte foot', 'famille' => 'espaces verts'],
            ['nom' => 'deratisation', 'famille' => 'nettoyage'],
            ['nom' => 'photocopieurs', 'famille' => 'Autre'],
            ['nom' => 'bacteriologie', 'famille' => 'securité'],
            ['nom' => 'Paratonnerre', 'famille' => 'électricité'],
            ['nom' => 'fibre', 'famille' => 'VRD'],
            ['nom' => 'INCENDIE', 'famille' => 'incendie'],
            ['nom' => 'panneau numérique', 'famille' => 'électricité'],
            ['nom' => 'telephonie hebergee', 'famille' => 'Autre'],
            ['nom' => 'installations electriques', 'famille' => 'électricité'],
            ['nom' => 'installation GAZ', 'famille' => 'chauffage'],
            ['nom' => 'carte SIM panneau entree village', 'famille' => 'Autre'],
            ['nom' => 'cloche eglise', 'famille' => 'Autre'],
            ['nom' => 'telesurveillance', 'famille' => 'securité'],
            ['nom' => 'CTA', 'famille' => 'chauffage'], // Centrale de Traitement d'Air
            ['nom' => 'Chauffage', 'famille' => 'chauffage'],
            ['nom' => 'équipements cuisine', 'famille' => 'Autre'],
            ['nom' => 'éclairage de sécurité', 'famille' => 'électricité'],
            ['nom' => 'appareils de cuisson et remise en température', 'famille' => 'Autre'],
            ['nom' => 'Désenfumage', 'famille' => 'incendie'],
            ['nom' => 'Extincteurs', 'famille' => 'incendie'],
            ['nom' => 'Ascenceurs', 'famille' => 'manutention'],
            ['nom' => 'Alarme Incendie', 'famille' => 'incendie'],
            ['nom' => 'Système détection automatique incendie', 'famille' => 'incendie'],
            ['nom' => 'Chauffage école', 'famille' => 'chauffage'],
            ['nom' => 'Jeux du stade', 'famille' => 'espaces verts'],
            ['nom' => 'agrées du fier', 'famille' => 'espaces verts'],
            ['nom' => 'cages de foot', 'famille' => 'espaces verts'],
            ['nom' => 'bloc escalade', 'famille' => 'Autre'],
            ['nom' => 'porte coulissante', 'famille' => 'serrurerie'],
            ['nom' => 'cages de foot école', 'famille' => 'espaces verts'],
            ['nom' => 'parcours sportif', 'famille' => 'espaces verts'],
            ['nom' => 'echafaudage', 'famille' => 'manutention'],
            ['nom' => 'lignes de vie ( foot + photovoltaique )', 'famille' => 'toiture'],
            ['nom' => 'treuil / palan', 'famille' => 'manutention'],
            ['nom' => 'ampliroll', 'famille' => 'manutention'],
            ['nom' => 'camion', 'famille' => 'manutention'],
            ['nom' => 'légionelle', 'famille' => 'securité']
        ];

        // Désactivation temporaire pour vider proprement la table si besoin
        DB::statement('TRUNCATE TABLE equipement RESTART IDENTITY CASCADE;');

        // Récupération des IDs d'immobilisation et de locaux par défaut pour l'intégrité référentielle nullable
        $idImmoDefaut = DB::table('immobilisation_inventaire_')->value('id_immo');
        $idLocalDefaut = DB::table('local_')->value('id_local');

        foreach ($equipementsNomenclature as $item) {

            //Récupération dynamique de la famille d'équipement par son nom exact
            $idFamille = DB::table('famille_equipement')
                ->where('libelle_famille', 'ilike', $item['famille'])
                ->value('id_famille');

            // Sécurité si une famille venait à manquer
            if (!$idFamille) {
                $idFamille = DB::table('famille_equipement')->where('libelle_famille', 'Autre')->value('id_famille') ?? 1;
            }

            // Insertion de l'équipement en base
            DB::table('equipement')->insert([
                'nom_equipement' => $item['nom'],
                'reference_serie' => 'SN-' . strtoupper(bin2hex(random_bytes(4))),
                'date_achat' => now()->subMonths(rand(6, 48))->format('Y-m-d'), // Date aléatoire réaliste
                'duree_garantie_mois' => 24.00,
                'marque' => 'Standard Communal',
                'etat_fonctionnement' => 'Opérationnel',
                'couleur' => null,
                'remarque' => 'Équipement technique recensé pour la maintenance',
                'id_immo' => $idImmoDefaut,
                'id_troncon' => null,
                'id_service' => null,
                'id_lieu' => null,
                'id_parent' => null,
                'id_local' => $idLocalDefaut, // Lié au premier local disponible (ex: Mairie ou Bibliothèque)
                'id_famille' => $idFamille
            ]);
        }
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EquipementSeeder extends Seeder
{
    public function run(): void
    {
        // 🚀 Ingestion de tes lignes d'équipements avec leurs familles respectives
        $dataset = [
            ['famille' => 'Equipement GAZ', 'equipement' => 'citerne'],
            ['famille' => 'équipement voirie', 'equipement' => 'Passage piéton'],
            ['famille' => 'équipement voirie', 'equipement' => 'plaque égout'],
            ['famille' => 'Equipements cuisine', 'equipement' => 'Four'],
            ['famille' => 'Équipements de chauffage et climatisation', 'equipement' => 'CTA'],
            ['famille' => 'Équipements de chauffage et climatisation', 'equipement' => 'PAC'],
            ['famille' => 'Équipements de sécurité', 'equipement' => 'Alarme Incendie'],
            ['famille' => 'Équipements de sécurité', 'equipement' => 'centrale Incendie'],
            ['famille' => 'Équipements de sécurité', 'equipement' => 'Defibrillateurs'],
            ['famille' => 'Équipements de sécurité', 'equipement' => 'Extincteurs'],
            ['famille' => 'Équipements de sécurité', 'equipement' => 'Système détection automatique incendie'],
            ['famille' => 'Équipements de sécurité', 'equipement' => 'trappe de désenfumage'],
            ['famille' => 'Equipements informatiques et numériques', 'equipement' => 'Logiciels'],
            ['famille' => 'Equipements informatiques et numériques', 'equipement' => 'Ordinateurs'],
            ['famille' => 'Equipements informatiques et numériques', 'equipement' => 'Photocopieurs'],
            ['famille' => 'Equipements informatiques et numériques', 'equipement' => 'ROUTEUR'],
            ['famille' => 'Equipements informatiques et numériques', 'equipement' => 'Serveurs'],
            ['famille' => 'Equipements informatiques et numériques', 'equipement' => 'telephonie hebergee'],
            ['famille' => 'Équipements techniques et d\'infrastructure', 'equipement' => 'ADOUCISSEUR'],
            ['famille' => 'Équipements techniques et d\'infrastructure', 'equipement' => 'ascenceur'],
            ['famille' => 'Équipements techniques et d\'infrastructure', 'equipement' => 'echafaudage'],
            ['famille' => 'Équipements techniques et d\'infrastructure', 'equipement' => 'groupe electrogéne'],
            ['famille' => 'Équipements techniques et d\'infrastructure', 'equipement' => 'Lignes De Vie'],
            ['famille' => 'Équipements techniques et d\'infrastructure', 'equipement' => 'treuil / palan'],
            ['famille' => 'jeux extérieurs', 'equipement' => 'city stade'],
            ['famille' => 'jeux extérieurs', 'equipement' => 'Agrées'],
            ['famille' => 'jeux extérieurs', 'equipement' => 'bloc escalade'],
            ['famille' => 'jeux extérieurs', 'equipement' => 'cages de foot'],
            ['famille' => 'jeux extérieurs', 'equipement' => 'cages de foot'], // Le doublon sera géré
            ['famille' => 'jeux extérieurs', 'equipement' => 'Skate Park'],
            ['famille' => 'materiel électroportatif et espaces verts', 'equipement' => 'debrousailleuse'],
            ['famille' => 'Matériel logistique', 'equipement' => 'barrières'],
            ['famille' => 'meubles et mobilier', 'equipement' => 'chaises'],
            ['famille' => 'MOBILIER URBAIN', 'equipement' => 'abribus'],
            ['famille' => 'MOBILIER URBAIN', 'equipement' => 'panneau numérique'],
            ['famille' => 'transport et véhicules', 'equipement' => 'camion'],
            ['famille' => 'transport et véhicules', 'equipement' => 'kubota'],
            ['famille' => 'Équipements techniques et d\'infrastructure', 'equipement' => 'Cloche'],
            ['famille' => 'Équipements techniques et d\'infrastructure', 'equipement' => 'Paratonnerre'],
            ['famille' => 'Équipements techniques et d\'infrastructure', 'equipement' => 'porte coulissante'],
            ['famille' => 'materiel électroportatif et espaces verts', 'equipement' => 'ROBOT tonte foot'],
            ['famille' => 'Équipements de chauffage et climatisation', 'equipement' => 'installation plomberie'],
            ['famille' => 'Équipements d\'électricité', 'equipement' => 'installation électrique'],
            ['famille' => 'Équipements de sécurité', 'equipement' => 'installation électrique'], // Rattaché à sécurité selon ta ligne
            ['famille' => 'Espaces verts', 'equipement' => 'Agorespace'],
        ];

        $insertions = 0;

        foreach ($dataset as $row) {
            // 1. Uniformisation du nom de la famille pour correspondre à ce qui a été inséré
            $familleFormatee = mb_convert_case(trim($row['famille']), MB_CASE_TITLE, "UTF-8");

            // 2. Uniformisation du nom de l'équipement (TitleCase comme demandé pour chaque début de mot)
            $equipementFormate = mb_convert_case(trim($row['equipement']), MB_CASE_TITLE, "UTF-8");

            // 3. Récupération dynamique de l'ID de la famille parente
            $idFamille = DB::table('famille_equipement')
                ->where('libelle_famille', 'ilike', $familleFormatee)
                ->value('id_famille');

            // Sécurité : Si la famille n'existe pas (ex: 'Espaces Verts' qui n'était pas dans ta première liste), on la crée à la volée !
            if (!$idFamille) {
                $idFamille = DB::table('famille_equipement')->insertGetId([
                    'libelle_famille' => $familleFormatee
                ], 'id_famille');
            }

            // 4. 🛡️ SÉCURITÉ ANTI-DOUBLON COMPLÈTE
            // On vérifie si cet équipement exact n'est pas déjà enregistré pour cette même famille
            $existeEquipement = DB::table('equipement')
                ->where('nom_equipement', 'ilike', $equipementFormate)
                ->where('id_famille', $idFamille)
                ->exists();

            if (!$existeEquipement) {
                DB::table('equipement')->insert([
                    'nom_equipement' => $equipementFormate,
                    'id_famille' => $idFamille,
                    'etat_fonctionnement' => 'En service' // Statut initial par défaut
                ]);
                $insertions++;
            }
        }

        $this->command->info("🛡️ Référentiel des équipements initialisé avec succès ! {$insertions} nouveaux composants uniques importés.");
    }
}
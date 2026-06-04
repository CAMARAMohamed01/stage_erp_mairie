<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FamilleEquipementSeeder extends Seeder
{
    public function run(): void
    {
        // Ta liste brute issue du fichier
        $famillesBrutes = [
            'Equipement GAZ',
            'Espaces verts',
            'équipement voirie',
            'équipement voirie',
            'Equipements cuisine',
            'Équipements de chauffage et climatisation',
            'Équipements de chauffage et climatisation',
            'Équipements de sécurité',
            'Équipements de sécurité',
            'Équipements de sécurité',
            'Équipements de sécurité',
            'Équipements de sécurité',
            'Équipements de sécurité',
            'Equipements informatiques et numériques',
            'Equipements informatiques et numériques',
            'Equipements informatiques et numériques',
            'Equipements informatiques et numériques',
            'Equipements informatiques et numériques',
            'Equipements informatiques et numériques',
            'Équipements techniques et d\'infrastructure',
            'Équipements techniques et d\'infrastructure',
            'Équipements techniques et d\'infrastructure',
            'Équipements techniques et d\'infrastructure',
            'Équipements techniques et d\'infrastructure',
            'Équipements techniques et d\'infrastructure',
            'jeux extérieurs',
            'jeux extérieurs',
            'jeux extérieurs',
            'jeux extérieurs',
            'jeux extérieurs',
            'jeux extérieurs',
            'materiel électroportatif et espaces verts',
            'Matériel logistique',
            'meubles et mobilier',
            'MOBILIER URBAIN',
            'MOBILIER URBAIN',
            'transport et véhicules',
            'transport et véhicules',
            'Équipements techniques et d\'infrastructure',
            'Équipements techniques et d\'infrastructure',
            'Équipements techniques et d\'infrastructure',
            'materiel électroportatif et espaces verts',
            'Équipements de chauffage et climatisation',
            'Équipements d\'électricité',
            'Équipements de sécurité'
        ];

        $insertions = 0;

        foreach ($famillesBrutes as $libelle) {
            // 👑 Nettoyage et Capitalisation de CHAQUE mot (ex: mobilier urbain -> Mobilier Urbain)
            $libelleFormate = mb_convert_case(trim($libelle), MB_CASE_TITLE, "UTF-8");

            // 🛡️ Sécurité anti-doublon (recherche insensible à la casse)
            $existeFamille = DB::table('famille_equipement')
                ->where('libelle_famille', 'ilike', $libelleFormate)
                ->exists();

            if (!$existeFamille) {
                DB::table('famille_equipement')->insert([
                    'libelle_famille' => $libelleFormate
                ]);
                $insertions++;
            }
        }

        $this->command->info("⚙️ Référentiel des familles d'équipement initialisé. {$insertions} nouvelles familles au format TitleCase insérées.");
    }
}
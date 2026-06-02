<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorieSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            'Bricolage',
            'Équipement',
            'Électricité',
            'Entretien',
            'Voirie',
            'Divers',
            'Toiture',
            'Peinture',
            'Plomberie',
            'Espaces verts',
            'Ménage',
            'Carrelage',
            'VRD',
            'Serrurerie',
            'Manutention',
            'Nettoyage',
            'Maçonnerie',
            'Menuiserie',
            'Chauffage',
            'Enrobés',
            'Portail',
            'Réseaux',
            'Grands Projets'
        ];

        foreach ($categories as $libelle) {
            DB::table('categorie')->updateOrInsert(
                ['libelle' => $libelle], // Condition d'unicité
                ['libelle' => $libelle]
            );
        }

        $this->command->info(' Référentiel des Catégories d\'interventions inséré !');
    }
}
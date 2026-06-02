<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LieuDitSeeder extends Seeder
{
    public function run()
    {
        $lieuxDits = [
            'Centre Village',
            'La Blonnière',
            'Le Glandon',
            'Les Tappes',
            'Nanoir',
            'Chessenay',
            'Chez Collet',
            'Chez Planchin',
            'La Centenaire',
            'Le Parmelan',
            'La Plaine du Fier',
            'Les Galets du Fier',
            'La Promenade du Fier',
            'Le Captage du Frêne',
            'La Source Martinod'
        ];

        foreach ($lieuxDits as $nom) {
            DB::table('lieu_dit')->updateOrInsert(
                ['nom_lieu_dit' => $nom], // Condition d'unicité sur le nom
                ['nom_lieu_dit' => $nom]
            );
        }

        $this->command->info('✨ Référentiel des Lieux-dits de Dingy-Saint-Clair initialisé !');
    }
}
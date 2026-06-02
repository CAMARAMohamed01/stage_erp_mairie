<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleLogicielSeeder extends Seeder
{
    public function run()
    {
        $modules = [
            'actions',
            'Interventions',
            'Patrimoine & Travaux',
            'Patrimoine & Équipements',
            'Finances & Achats',
            'Urbanisme',
            'Administration',
            'Conseil & Commissions',
            'État Civil & Cimetières',
            'Voirie'
        ];

        foreach ($modules as $nom) {
            DB::table('module_logiciel')->updateOrInsert(
                ['nom_module' => $nom], // Condition d'unicité
                ['nom_module' => $nom]
            );
        }

        $this->command->info('✨ Les 10 modules officiels de la matrice d\'habilitations ont été initialisés !');
    }
}
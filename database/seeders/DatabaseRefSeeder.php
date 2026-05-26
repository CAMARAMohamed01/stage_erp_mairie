<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseRefSeeder extends Seeder
{
    public function run(): void
    {
        // On ne garde STRICTEMENT que les modules applicatifs réels
        $modules = [
            ['nom_module' => 'actions'],
            ['nom_module' => 'Interventions'],
            ['nom_module' => 'Patrimoine & Equipements'],
            ['nom_module' => 'Utilisateurs & Droits'],
        ];

        foreach ($modules as $module) {
            DB::table('module_logiciel')->updateOrInsert(
                ['nom_module' => $module['nom_module']],
                $module
            );
        }
    }
}
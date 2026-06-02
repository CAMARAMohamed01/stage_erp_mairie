<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceMairieSeeder extends Seeder
{
    public function run()
    {
        $services = [
            'Services Techniques',
            'Urbanisme',
            'Accueil',
            'Comptabilité',
            'DGS',
            'Périscolaire'
        ];

        foreach ($services as $nom) {
            DB::table('service_mairie')->updateOrInsert(
                ['nom_service' => $nom], // Évite les doublons si relancé
                ['nom_service' => $nom, 'id_service_parent' => null]
            );
        }

        $this->command->info('✨ Les 6 services réels de la mairie ont été initialisés.');
    }
}
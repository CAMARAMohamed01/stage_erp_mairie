<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypeErpSeeder extends Seeder
{
    public function run()
    {
        // 🏛️ Liste officielle de la réglementation de sécurité incendie communale
        $typesErp = [
            ['type_erp' => 'W', 'categorie_erp' => 5, 'reglementation' => 'Administrations, banques, bureaux (Mairie)'],
            ['type_erp' => 'L', 'categorie_erp' => 5, 'reglementation' => 'Salles d\'auditions, conférences, réunions (Salles des fêtes)'],
            ['type_erp' => 'L', 'categorie_erp' => 3, 'reglementation' => 'Grandes salles de réunions / spectacles'],
            ['type_erp' => 'N', 'categorie_erp' => 5, 'reglementation' => 'Restaurants et débits de boissons (Cantine scolaire)'],
            ['type_erp' => 'R', 'categorie_erp' => 5, 'reglementation' => 'Établissements d\'éveil et d\'enseignement (Écoles, Crèches)'],
            ['type_erp' => 'R', 'categorie_erp' => 4, 'reglementation' => 'Centres de vacances ou d\'envergure'],
            ['type_erp' => 'V', 'categorie_erp' => 3, 'reglementation' => 'Établissements du culte (Église)'],
            ['type_erp' => 'M', 'categorie_erp' => 5, 'reglementation' => 'Magasins de vente, centres commerciaux'],
            ['type_erp' => 'X', 'categorie_erp' => 5, 'reglementation' => 'Établissements sportifs couverts (Vestiaires, complexes)'],
            ['type_erp' => 'REF', 'categorie_erp' => 4, 'reglementation' => 'Refuges de montagne / Chalets d\'altitude'],
        ];

        foreach ($typesErp as $erp) {
            DB::table('type_erp')->updateOrInsert(
                // Condition d'unicité sur le couple Type et Catégorie
                ['type_erp' => $erp['type_erp'], 'categorie_erp' => $erp['categorie_erp']],
                [
                    'reglementation_applicable' => $erp['reglementation'],
                    'public_cible' => 'Tout public ou Agents communaux'
                ]
            );
        }

        $this->command->info('✨ Référentiel des Types ERP inséré avec succès !');
    }
}
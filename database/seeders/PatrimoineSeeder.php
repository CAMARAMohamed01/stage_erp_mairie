<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PatrimoineSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Lieu-dit (On cherche d'abord, sinon on crée)
        $lieu = DB::table('lieu_dit')->where('nom_lieu_dit', 'Chef-Lieu')->first();
        $id_lieu_dit = $lieu->id_lieu_dit ?? DB::table('lieu_dit')->insertGetId(['nom_lieu_dit' => 'Chef-Lieu'], 'id_lieu_dit');

        // 2. Adresse
        $adresse = DB::table('Adresse')->where('nom_voie', 'Place de la Mairie')->first();
        $id_adresse = $adresse->id_adresse ?? DB::table('Adresse')->insertGetId([
            'num_rue' => 1,
            'nom_voie' => 'Place de la Mairie',
            'code_postal' => '74230',
            'ville' => 'Dingy-Saint-Clair',
            'id_lieu_dit' => $id_lieu_dit
        ], 'id_adresse');

        // 3. Tiers (L'email qui bloquait)
        $tiers = DB::table('tiers')->where('email_tiers', 'contact@dingysaintclair.fr')->first();
        if ($tiers) {
            $id_tiers = $tiers->id_tiers; // Si trouvé, on récupère juste l'ID
        } else {
            // Sinon, on le crée
            $id_tiers = DB::table('tiers')->insertGetId([
                'type_tiers' => 'Personne Morale',
                'email_tiers' => 'contact@dingysaintclair.fr',
                'id_adresse' => $id_adresse
            ], 'id_tiers');

            DB::table('tiers_morale')->insert([
                'id_tiers' => $id_tiers,
                'raison_sociale' => 'Mairie de Dingy-Saint-Clair',
                'siret' => '21740102500010'
            ]);
        }

        // 4. Immobilisation (Avec sa propre contrainte UNIQUE)
        $immo = DB::table('immobilisation_inventaire_')->where('num_inventaire', 'INV-BAT-001')->first();
        $id_immo = $immo->id_immo ?? DB::table('immobilisation_inventaire_')->insertGetId([
            'num_inventaire' => 'INV-BAT-001',
            'libelle_comptable' => 'Hôtel de Ville Principal',
            'est_amortissable' => true
        ], 'id_immo');

        // 5. Parcelle
        $parcelle = DB::table('parcelle')->where('num_parcelle', '0245')->first();
        $id_parcelle = $parcelle->id_parcelle ?? DB::table('parcelle')->insertGetId([
            'num_parcelle' => '0245',
            'section_cadastrale' => 'A',
            'type_parcelle' => 'Domaine Public',
            'id_lieu_dit' => $id_lieu_dit,
            'id_immo' => $id_immo
        ], 'id_parcelle');

        // 6. Types d'ERP
        $erp_w = DB::table('type_erp')->where('type_erp', 'W')->first();
        $id_type_erp_w = $erp_w->id_type_erp ?? DB::table('type_erp')->insertGetId([
            'reglementation_applicable' => 'Code de la Construction',
            'categorie_erp' => 5,
            'type_erp' => 'W',
            'public_cible' => 'Tout public'
        ], 'id_type_erp');

        $erp_r = DB::table('type_erp')->where('type_erp', 'R')->first();
        if (!$erp_r) {
            DB::table('type_erp')->insert([
                'reglementation_applicable' => 'Code de la Construction',
                'categorie_erp' => 4,
                'type_erp' => 'R',
                'public_cible' => 'Enfants et Personnel'
            ]);
        }

        // 7. Bâtiment (On vérifie s'il existe avant de l'insérer)
        $batiment = DB::table('batiment')->where('nom_bat', 'Hôtel de Ville de Dingy-Saint-Clair')->first();
        if (!$batiment) {
            DB::table('batiment')->insert([
                'nom_bat' => 'Hôtel de Ville de Dingy-Saint-Clair',
                'surface_totale_m2' => 450.50,
                'date_construction' => Carbon::create('1980', '01', '01'),
                'id_tiers' => $id_tiers,
                'id_parcelle' => $id_parcelle,
                'id_type_erp' => $id_type_erp_w,
                'id_adresse' => $id_adresse,
                'id_immo' => $id_immo
            ]);
        }
    }
}
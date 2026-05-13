<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Le Technicien
        Utilisateur::updateOrCreate(
            ['emailpro' => 'pierre@mairie.fr'], // La condition de recherche
            [
                'nom_user' => 'Camara', // J'ai vu dans ton erreur que tu avais testé avec ton nom !
                'prenom_user' => 'Pierre',
                'role_appli' => 'Technicien',
                'password' => Hash::make('password'),
            ]
        );

        // 2. L'Agent d'accueil
        Utilisateur::updateOrCreate(
            ['emailpro' => 'sophie@mairie.fr'],
            [
                'nom_user' => 'Durand',
                'prenom_user' => 'Sophie',
                'role_appli' => 'Accueil',
                'password' => Hash::make('password'),
            ]
        );

        // 3. L'Administrateur du système
        Utilisateur::updateOrCreate(
            ['emailpro' => 'm.camara@mairie.fr'],
            [
                'nom_user' => 'Camara',
                'prenom_user' => 'Mohamed',
                'role_appli' => 'Administrateur',
                'password' => Hash::make('password'),
            ]
        );
    }
}
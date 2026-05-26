<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
// On importe les Models proprement
use App\Models\Categorie;
use App\Models\Utilisateur;
use App\Models\FamilleEquipement;
use App\Models\action;
use App\Models\Intervention;
use App\Models\Equipement;
use App\Models\ControleReglementaire;

class DemoSeeder extends Seeder
{
    public function run()
    {
        // 1. On crée les dépendances. 
        // La méthode create() retourne l'objet complet.
        $categorie = Categorie::create(['libelle' => 'Voirie & Réseaux']);

        $user = Utilisateur::create([
            'nom_user' => 'Dupont',
            'prenom_user' => 'Pierre',
            'role_appli' => 'Technicien'
        ]);

        $famille = FamilleEquipement::create(['libelle_famille' => 'Véhicules & Engins']);

        // 2. On insère les actions
        // L'avantage : la fonction getKey() va automatiquement chercher la bonne clé primaire (id_cat, id_user...) 
        // selon ce que vous avez configuré dans vos Models !
        action::create([
            'date_creation' => Carbon::now()->subDays(2),
            'emetteur_nom' => 'M. Bernard',
            'description' => 'Gros nid de poule dangereux',
            'mode_reception' => 'Téléphone',
            'priorite' => 'Haute',
            'statut_action' => 'Nouveau',
            'id_user' => $user->getKey(),
            'id_cat' => $categorie->getKey(),
        ]);

        action::create([
            'date_creation' => Carbon::now()->subHours(5),
            'emetteur_nom' => 'Mme Martin',
            'description' => 'Lampadaire clignotant devant l\'école',
            'mode_reception' => 'Email',
            'priorite' => 'Normale',
            'statut_action' => 'Nouveau',
            'id_user' => $user->getKey(),
            'id_cat' => $categorie->getKey(),
        ]);

        // 3. Les interventions
        Intervention::create([
            'date_ouverture' => Carbon::now()->subDays(1),
            'type_intervention' => 'Rebouchage asphalte',
            'statut_global' => 'En cours',
            'description' => 'Intervention suite à action',
            'id_cat' => $categorie->getKey(),
        ]);

        Intervention::create([
            'date_ouverture' => Carbon::now()->subDays(4),
            'type_intervention' => 'Réparation fuite fontaine',
            'statut_global' => 'En cours',
            'description' => 'Changement de la pompe',
            'id_cat' => $categorie->getKey(),
        ]);

        // 4. Les équipements
        Equipement::create([
            'nom_equipement' => 'Tractopelle JCB',
            'marque' => 'JCB',
            'etat_fonctionnement' => 'En panne',
            'id_famille' => $famille->getKey()
        ]);

        Equipement::create([
            'nom_equipement' => 'Tondeuse Autoportée',
            'marque' => 'John Deere',
            'etat_fonctionnement' => 'Défectueux',
            'id_famille' => $famille->getKey()
        ]);

        // 5. Les contrôles
        ControleReglementaire::create(['designation' => 'Vérification annuelle extincteurs Mairie', 'frequence_mois' => 12]);
        ControleReglementaire::create(['designation' => 'Contrôle technique nacelle élévatrice', 'frequence_mois' => 6]);
        ControleReglementaire::create(['designation' => 'Test des alarmes incendie École', 'frequence_mois' => 6]);
    }
}
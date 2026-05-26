<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\action;
use App\Models\Intervention;
use App\Models\Equipement;
use App\Models\ControleReglementaire;

class DashboardTechniqueController extends Controller
{
    public function index()
    {
        // 1. actions à traiter
        $nouveauxactions = action::with('categorie')
            ->where('statut_action', 'Nouveau')
            ->orderBy('date_creation', 'desc')
            ->take(5)
            ->get();

        // 2. Interventions en cours
        $interventionsEnCours = Intervention::with('categorie')
            ->where('statut_global', 'En cours')
            ->take(5)
            ->get();

        // 3. Équipements en panne (On se base sur votre colonne etat_fonctionnement)
        $equipementsEnPanne = Equipement::whereIn('etat_fonctionnement', ['En panne', 'Défectueux'])
            ->take(5)
            ->get();

        // 4. Contrôles réglementaires à venir (Pour l'instant on liste les 5 premiers à titre d'exemple)
        $controles = ControleReglementaire::take(5)->get();

        // On envoie tout à la vue grâce à la fonction compact() qui est plus propre
        return view('technique.dashboard', compact(
            'nouveauxactions',
            'interventionsEnCours',
            'equipementsEnPanne',
            'controles'
        ));
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Compteur;
use App\Models\Local;
use App\Models\Contrat;
use Illuminate\Support\Facades\DB;

class CompteurController extends Controller
{
    public function index()
    {
        $compteurs = Compteur::with(['local.batiment', 'contrat'])->orderBy('numero_compteur')->get();
        return view('compteurs.index', compact('compteurs'));
    }

    public function create()
    {
        $locaux = Local::with('batiment')->orderBy('nom_local')->get();

        // On récupère les contrats (idéalement ceux de type fourniture)
        $contrats = Contrat::with('tiers')
            ->where('type_contrat', 'ILIKE', '%eau%')
            ->orWhere('type_contrat', 'ILIKE', '%électricité%')
            ->orWhere('type_contrat', 'ILIKE', '%gaz%')
            ->orWhere('type_contrat', 'ILIKE', '%énergie%')
            ->orWhere('type_contrat', 'ILIKE', '%fourniture%')
            ->orderBy('numero_contrat')
            ->get();

        // Si la liste est vide, on récupère tous les contrats
        if ($contrats->isEmpty()) {
            $contrats = Contrat::with('tiers')->orderBy('numero_contrat')->get();
        }

        $compteursPrincipaux = Compteur::whereNull('id_compteur_principal')->orderBy('numero_compteur')->get();

        return view('compteurs.create', compact('locaux', 'contrats', 'compteursPrincipaux'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'point_comptage' => 'required|string|max:30',
            'numero_compteur' => 'nullable|string|max:30|unique:compteur,numero_compteur',
            'type_reseau' => 'required|string|max:80', // Eau, Électricité, Gaz, Chauffage urbain
            'dessert_tout_le_batiment' => 'nullable|boolean',
            'unite_mesure' => 'nullable|string|max:10', // m3, kWh
            'date_pose' => 'nullable|date',
            'date_arret' => 'nullable|date|after_or_equal:date_pose',
            'observations' => 'nullable|string|max:255',
            'localisation_vanne_arret' => 'nullable|string|max:255',
            'id_contrat' => 'nullable|exists:contrat,id_contrat',
            'id_compteur_principal' => 'nullable|exists:compteur,id_compteur',
            'id_local' => 'required|exists:local_,id_local',
        ]);

        $validated['dessert_tout_le_batiment'] = $request->has('dessert_tout_le_batiment');

        Compteur::create($validated);

        return redirect()->route('compteurs.index')->with('success', 'Le compteur a été ajouté au patrimoine.');
    }

    public function edit($id)
    {
        $compteur = Compteur::findOrFail($id);
        $locaux = Local::with('batiment')->orderBy('nom_local')->get();

        $contrats = Contrat::with('tiers')
            ->where('type_contrat', 'ILIKE', '%eau%')
            ->orWhere('type_contrat', 'ILIKE', '%électricité%')
            ->orWhere('type_contrat', 'ILIKE', '%gaz%')
            ->orWhere('type_contrat', 'ILIKE', '%énergie%')
            ->orWhere('type_contrat', 'ILIKE', '%fourniture%')
            ->orderBy('numero_contrat')
            ->get();

        if ($contrats->isEmpty()) {
            $contrats = Contrat::with('tiers')->orderBy('numero_contrat')->get();
        }

        // On exclut le compteur lui-même pour ne pas qu'il soit son propre parent
        $compteursPrincipaux = Compteur::whereNull('id_compteur_principal')
            ->where('id_compteur', '!=', $id)
            ->orderBy('numero_compteur')
            ->get();

        return view('compteurs.edit', compact('compteur', 'locaux', 'contrats', 'compteursPrincipaux'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'point_comptage' => 'required|string|max:30',
            'numero_compteur' => 'nullable|string|max:30|unique:compteur,numero_compteur,' . $id . ',id_compteur',
            'type_reseau' => 'required|string|max:80',
            'dessert_tout_le_batiment' => 'nullable|boolean',
            'unite_mesure' => 'nullable|string|max:10',
            'date_pose' => 'nullable|date',
            'date_arret' => 'nullable|date|after_or_equal:date_pose',
            'observations' => 'nullable|string|max:255',
            'localisation_vanne_arret' => 'nullable|string|max:255',
            'id_contrat' => 'nullable|exists:contrat,id_contrat',
            'id_compteur_principal' => 'nullable|exists:compteur,id_compteur',
            'id_local' => 'required|exists:local_,id_local',
        ]);

        $validated['dessert_tout_le_batiment'] = $request->has('dessert_tout_le_batiment');

        $compteur = Compteur::findOrFail($id);
        $compteur->update($validated);

        return redirect()->route('compteurs.index')->with('success', 'Les informations du compteur ont été mises à jour.');
    }

    public function destroy($id)
    {
        $compteur = Compteur::findOrFail($id);
        $compteur->delete();

        return redirect()->route('compteurs.index')->with('success', 'Le compteur a été supprimé.');
    }
    // --- CONSULTER LA FICHE DÉTAILLÉE ---
    public function show($id)
    {
        $compteur = Compteur::with([
            'local.batiment',
            'contrat.tiers',
            'compteurPrincipal',
            'sousCompteurs'
        ])->findOrFail($id);

        return view('compteurs.show', compact('compteur'));
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReleveCompteur;
use App\Models\Compteur;

class ReleveCompteurController extends Controller
{
    // On affiche les relevés spécifiques à un compteur
    public function index($idCompteur)
    {
        $compteur = Compteur::findOrFail($idCompteur);
        $releves = ReleveCompteur::where('id_compteur', $idCompteur)->orderByDesc('date_releve')->get();

        return view('releves.index', compact('compteur', 'releves'));
    }

    public function store(Request $request, $idCompteur)
    {
        $validated = $request->validate([
            'date_releve' => 'required|date',
            'valeur_index' => 'required|numeric',
            'commentaire_releve' => 'nullable|string|max:150',
        ]);

        $validated['id_compteur'] = $idCompteur;

        ReleveCompteur::create($validated);

        return redirect()->route('compteurs.releves.index', $idCompteur)
            ->with('success', 'Relevé enregistré avec succès.');
    }
}
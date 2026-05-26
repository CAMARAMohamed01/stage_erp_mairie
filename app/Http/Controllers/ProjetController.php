<?php

namespace App\Http\Controllers;

use App\Models\Projet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjetController extends Controller
{
    public function index(Request $request)
    {
        $query = Projet::with(['chefProjet']);
        if ($request->filled('search')) {
            $query->where('nom_projet', 'ilike', '%' . $request->search . '%');
        }

        // Filtre par type
        if ($request->filled('type')) {
            $query->where('type_projet', $request->type);
        }

        $projets = $query->orderBy('annee_mandat', 'desc')->get();
        return view('projets.index', compact('projets'));
    }

    public function create()
    {
        $utilisateurs = DB::table('utilisateur')->get();
        return view('projets.create', compact('utilisateurs'));
    }
    public function show($id)
    {
        // On charge les relation 'chefProjet' définies dans ton modèle
        $projet = Projet::with(['interventions', 'chefProjet'])->findOrFail($id);

        return view('projets.show', compact('projet'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom_projet' => 'required|string|max:80',
            'type_projet' => 'required|string|max:50',
            'budget_global_alloue' => 'nullable|numeric|min:0',
            'annee_mandat' => 'required|string|max:5',
            'avis' => 'nullable|string|max:100',
            'id_user' => 'required|exists:utilisateur,id_user',
        ]);

        Projet::create($validated);

        return redirect()->route('projets.index')->with('success', 'Projet créé avec succès.');
    }

    public function edit($id)
    {
        $projet = Projet::findOrFail($id);
        $utilisateurs = DB::table('utilisateur')->get();
        return view('projets.edit', compact('projet', 'utilisateurs'));
    }

    public function update(Request $request, $id)
    {
        $projet = Projet::findOrFail($id);

        $validated = $request->validate([
            'nom_projet' => 'required|string|max:80',
            'type_projet' => 'required|string|max:50',
            'budget_global_alloue' => 'nullable|numeric|min:0',
            'annee_mandat' => 'required|string|max:5',
            'avis' => 'nullable|string|max:100',
            'id_user' => 'required|exists:utilisateur,id_user',
        ]);

        $projet->update($validated);

        return redirect()->route('projets.index')->with('success', 'Projet mis à jour.');
    }

    public function destroy($id)
    {
        Projet::findOrFail($id)->delete();
        return redirect()->route('projets.index')->with('success', 'Projet supprimé.');
    }
}
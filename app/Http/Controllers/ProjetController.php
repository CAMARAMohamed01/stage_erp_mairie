<?php

namespace App\Http\Controllers;

use App\Models\Projet;
use App\Models\Batiment;
use App\Models\LieuPublic;
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

        if ($request->filled('type')) {
            $query->where('type_projet', $request->type);
        }

        $projets = $query->orderBy('annee_mandat', 'desc')->get();
        return view('projets.index', compact('projets'));
    }

    public function create()
    {
        $utilisateurs = DB::table('utilisateur')->get();
        // On récupère les périmètres possibles
        $batiments = DB::table('batiment')->orderBy('nom_bat')->get();
        $lieux = DB::table('lieux_publics')->orderBy('nom_lieu')->get();
        $quartiers = DB::table('lieu_dit')->orderBy('nom_lieu_dit')->get();

        return view('projets.create', compact('utilisateurs', 'batiments', 'lieux', 'quartiers'));
    }

    public function show($id)
    {
        // On charge les interventions, le chef de projet, et le périmètre (bâtiments + lieux)
        $projet = Projet::with(['interventions', 'chefProjet', 'batiments', 'lieuxPublics'])->findOrFail($id);
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
            // Validation des tableaux de périmètre
            'batiments' => 'nullable|array',
            'batiments.*' => 'exists:batiment,id_batiment',
            'lieux' => 'nullable|array',
            'lieux.*' => 'exists:lieux_publics,id_lieu',
            'quartiers' => 'nullable|array',
            'quartiers.*' => 'exists:lieu_dit,id_lieu_dit',
        ]);

        $projet = Projet::create($validated);

        // Attachement des relations (Pivot tables)
        if ($request->has('batiments')) {
            $projet->batiments()->attach($request->batiments);
        }
        if ($request->has('lieux')) {
            $projet->lieuxPublics()->attach($request->lieux);
        }
        if ($request->has('quartiers')) {
            $projet->quartiers()->attach($request->quartiers);
        }
        return redirect()->route('projets.index')->with('success', 'Projet et son périmètre créés avec succès.');
    }

    public function edit($id)
    {
        $projet = Projet::with(['batiments', 'lieuxPublics', 'quartiers'])->findOrFail($id);
        $utilisateurs = DB::table('utilisateur')->get();
        $batiments = DB::table('batiment')->orderBy('nom_bat')->get();
        $lieux = DB::table('lieux_publics')->orderBy('nom_lieu')->get();
        $quartiers = DB::table('lieu_dit')->orderBy('nom_lieu_dit')->get();
        return view('projets.edit', compact('projet', 'utilisateurs', 'batiments', 'lieux', 'quartiers'));
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
            'batiments' => 'nullable|array',
            'batiments.*' => 'exists:batiment,id_batiment',
            'lieux' => 'nullable|array',
            'lieux.*' => 'exists:lieux_publics,id_lieu',
            'quartiers' => 'nullable|array',
            'quartiers.*' => 'exists:lieu_dit,id_lieu_dit',
        ]);

        $projet->update($validated);

        // La méthode sync() met à jour la table pivot (ajoute les nouveaux, supprime les décochés)
        $projet->batiments()->sync($request->input('batiments', []));
        $projet->lieuxPublics()->sync($request->input('lieux', []));
        $projet->quartiers()->sync($request->input('quartiers', []));

        return redirect()->route('projets.index')->with('success', 'Projet mis à jour.');
    }

    public function destroy($id)
    {
        $projet = Projet::findOrFail($id);
        // On détache d'abord les relations pour éviter les erreurs de clés étrangères
        $projet->batiments()->detach();
        $projet->lieuxPublics()->detach();
        $projet->lieuxDits()->detach();
        $projet->delete();


        return redirect()->route('projets.index')->with('success', 'Projet supprimé.');
    }
}
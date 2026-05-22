<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VoieController extends Controller
{

    public function index(Request $request)
    {
        $query = DB::table('voie');

        // Recherche par nom de voie
        if ($request->has('search')) {
            $query->where('nom_voie', 'ilike', '%' . $request->search . '%');
        }

        $voies = $query->orderBy('nom_voie', 'asc')->paginate(20);

        // Calculs pour le bandeau d'en-tête (patrimoine)
        $totalVoies = DB::table('voie')->count();
        $longueurTotale = DB::table('voie')->sum('longueur_reelle_ml');

        return view('voies.index', compact('voies', 'totalVoies', 'longueurTotale'));
    }
    public function show($id)
    {
        // 1. Informations complètes de la Voie
        $voie = DB::table('voie')->where('id_voie', $id)->first();

        if (!$voie) {
            abort(404, 'Voie de circulation introuvable.');
        }

        // 2. Les tronçons qui composent cette voie (triés par Point Kilométrique de début)
        $troncons = DB::table('troncon')
            ->leftJoin('Zone', 'troncon.id_zone', '=', 'Zone.id_zone')
            ->where('id_voie', $id)
            ->select('troncon.*', 'Zone.nom_zone', 'Zone.code_zone')
            ->orderBy('pk_debut', 'asc')
            ->get();

        // 3. Les ouvrages d'art rattachés à cette voie (Ponts, Murs de soutènement...)
        $ouvrages = DB::table('ouvrage')
            ->where('id_voie', $id)
            ->orderBy('nom_ouvrage')
            ->get();

        // 4. Les zones globales traversées par cette voie (via la table pivot voie_zone)
        $zones = DB::table('Zone')
            ->join('voie_zone', 'Zone.id_zone', '=', 'voie_zone.id_zone')
            ->where('voie_zone.id_voie', $id)
            ->select('Zone.*')
            ->get();

        return view('voies.show', compact('voie', 'troncons', 'ouvrages', 'zones'));
    }
    public function create()
    {
        return view('voies.create');
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom_voie' => 'required|string|max:100',
            'numero_voie' => 'nullable|string|max:10',
            'ancien_numero' => 'nullable|string|max:20',
            'num_provisoire' => 'nullable|string|max:20',
            'categorie_voie' => 'nullable|string|max:80',
            'statut_juridique' => 'nullable|string|max:50',

            'longueur_reelle_ml' => 'nullable|integer|min:0',
            'longueur_classee_ml' => 'nullable|integer|min:0',
            'largeur_moyenne_m' => 'nullable|numeric|min:0|max:999.99',
            'point_origine' => 'nullable|string|max:50',
            'point_extremite' => 'nullable|string|max:100',

            'conformite_cadastrale' => 'nullable|string|max:100',
            'definition_trace' => 'nullable|string',
            'historique_incorporation' => 'nullable|string',
            'observations_statut' => 'nullable|string',
            'interet_touristique' => 'nullable|string',
        ]);

        // Forçage du booléen pour Postgres
        $validated['est_pdipr'] = $request->has('est_pdipr');

        $id = DB::table('voie')->insertGetId($validated, 'id_voie');

        return redirect()->route('voies.show', $id)
            ->with('success', 'La voie a été créée avec succès.');
    }
    public function edit($id)
    {
        $voie = DB::table('voie')->where('id_voie', $id)->first();

        if (!$voie)
            abort(404);

        return view('voies.edit', compact('voie'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nom_voie' => 'required|string|max:100',
            'numero_voie' => 'nullable|string|max:10',
            'ancien_numero' => 'nullable|string|max:20',
            'num_provisoire' => 'nullable|string|max:20',
            'categorie_voie' => 'nullable|string|max:80',
            'statut_juridique' => 'nullable|string|max:50',

            'longueur_reelle_ml' => 'nullable|integer|min:0',
            'longueur_classee_ml' => 'nullable|integer|min:0',
            'largeur_moyenne_m' => 'nullable|numeric|min:0|max:999.99',
            'point_origine' => 'nullable|string|max:50',
            'point_extremite' => 'nullable|string|max:100',

            'conformite_cadastrale' => 'nullable|string|max:100',
            'definition_trace' => 'nullable|string',
            'historique_incorporation' => 'nullable|string',
            'observations_statut' => 'nullable|string',
            'interet_touristique' => 'nullable|string',
        ]);

        $validated['est_pdipr'] = $request->has('est_pdipr');

        DB::table('voie')->where('id_voie', $id)->update($validated);

        return redirect()->route('voies.show', $id)
            ->with('success', 'Les informations de la voie ont été mises à jour.');
    }
    public function destroy($id)
    {
        // Vérification d'existence
        $voie = DB::table('voie')->where('id_voie', $id)->first();
        if (!$voie)
            abort(404, 'Voie introuvable.');

        // Suppression de la voie (les tronçons liés seront automatiquement supprimés grâce à la contrainte ON DELETE CASCADE)
        DB::table('voie')->where('id_voie', $id)->delete();

        return redirect()->route('voies.index')
            ->with('success', 'La voie a été supprimée avec succès.');
    }
}
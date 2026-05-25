<?php

namespace App\Http\Controllers;

use App\Models\Secteur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SecteurController extends Controller
{
    /**
     * Affiche la liste des secteurs.
     */
    public function index()
    {
        // On récupère tous les secteurs. Laravel saura vérifier si geom_secteur est null ou non pour le badge.
        $secteurs = Secteur::all();

        return view('secteurs.index', compact('secteurs'));
    }

    /**
     * Affiche le formulaire de création.
     */
    public function create()
    {
        return view('secteurs.create');
    }

    /**
     * Enregistre un nouveau secteur en base de données.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom_secteur' => 'required|string|max:80',
            'code_secteur' => 'nullable|string|max:10',
            'geojson_data' => 'nullable|json'
        ]);

        // 1. On crée l'entité avec Eloquent pour générer son ID
        $secteur = new Secteur();
        $secteur->nom_secteur = $request->nom_secteur;
        $secteur->code_secteur = $request->code_secteur;
        $secteur->save();

        // 2. Si un dessin a été fait, on met à jour la géométrie en SQL brut
        if ($request->filled('geojson_data')) {
            DB::update(
                "UPDATE secteur 
                 SET geom_secteur = ST_Multi(ST_SetSRID(ST_GeomFromGeoJSON(?), 4326)) 
                 WHERE id_secteur = ?",
                [$request->geojson_data, $secteur->id_secteur]
            );
        }

        return redirect()->route('secteurs.index')->with('success', 'Le secteur a été créé avec succès.');
    }

    /**
     * Affiche la fiche détaillée d'un secteur (avec la carte).
     */
    public function show($id)
    {
        // On utilise DB::table pour pouvoir exécuter la fonction PostGIS ST_AsGeoJSON
        $secteur = DB::table('secteur')
            ->select('id_secteur', 'nom_secteur', 'code_secteur', DB::raw('ST_AsGeoJSON(geom_secteur) as geojson'))
            ->where('id_secteur', $id)
            ->first();

        if (!$secteur) {
            abort(404, 'Secteur introuvable');
        }

        return view('secteurs.show', compact('secteur'));
    }

    /**
     * Affiche le formulaire d'édition.
     */
    public function edit($id)
    {
        // Comme pour le show, on a besoin du GeoJSON pour redessiner le polygone existant sur la carte
        $secteur = DB::table('secteur')
            ->select('id_secteur', 'nom_secteur', 'code_secteur', DB::raw('ST_AsGeoJSON(geom_secteur) as geojson'))
            ->where('id_secteur', $id)
            ->first();

        if (!$secteur) {
            abort(404, 'Secteur introuvable');
        }

        return view('secteurs.edit', compact('secteur'));
    }

    /**
     * Met à jour un secteur existant.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nom_secteur' => 'required|string|max:80',
            'code_secteur' => 'nullable|string|max:10',
            'geojson_data' => 'nullable|json'
        ]);

        $secteur = Secteur::findOrFail($id);
        $secteur->nom_secteur = $request->nom_secteur;
        $secteur->code_secteur = $request->code_secteur;
        $secteur->save();

        // Mise à jour de la géométrie
        if ($request->filled('geojson_data')) {
            DB::update(
                "UPDATE secteur 
                 SET geom_secteur = ST_Multi(ST_SetSRID(ST_GeomFromGeoJSON(?), 4326)) 
                 WHERE id_secteur = ?",
                [$request->geojson_data, $id]
            );
        } else {
            // Optionnel : si on veut permettre d'effacer la géométrie
            // DB::update("UPDATE secteur SET geom_secteur = NULL WHERE id_secteur = ?", [$id]);
        }

        return redirect()->route('secteurs.show', $id)->with('success', 'Le secteur a été mis à jour.');
    }

    /**
     * Supprime un secteur.
     */
    public function destroy($id)
    {
        $secteur = Secteur::findOrFail($id);

        // Attention : S'il y a des zones liées à ce secteur, il faudra d'abord gérer la contrainte de clé étrangère
        $secteur->delete();

        return redirect()->route('secteurs.index')->with('success', 'Secteur supprimé.');
    }
}
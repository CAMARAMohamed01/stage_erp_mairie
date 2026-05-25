<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use App\Models\Secteur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ZoneController extends Controller
{
    public function index()
    {
        // On charge les zones avec leur secteur associé pour l'affichage dans le tableau
        $zones = Zone::with('secteur')->get();
        return view('zones.index', compact('zones'));
    }

    public function create()
    {
        // On a besoin de la liste des secteurs pour le menu déroulant du formulaire
        $secteurs = Secteur::all();
        return view('zones.create', compact('secteurs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom_zone' => 'required|string|max:80',
            'code_zone' => 'nullable|string|max:10',
            'id_secteur' => 'required|integer|exists:secteur,id_secteur',
            'geojson_data' => 'nullable|json'
        ]);

        $zone = new Zone();
        $zone->nom_zone = $request->nom_zone;
        $zone->code_zone = $request->code_zone;
        $zone->id_secteur = $request->id_secteur;
        $zone->save();

        if ($request->filled('geojson_data')) {
            // Attention : "Zone" doit être entre guillemets si la table a été créée avec une majuscule
            DB::update(
                'UPDATE "Zone" 
                 SET geom_zone = ST_Multi(ST_SetSRID(ST_GeomFromGeoJSON(?), 4326)) 
                 WHERE id_zone = ?',
                [$request->geojson_data, $zone->id_zone]
            );
        }

        return redirect()->route('zones.index')->with('success', 'Zone créée avec succès.');
    }

    public function show($id)
    {
        $zone = DB::table('Zone')
            ->select('id_zone', 'nom_zone', 'code_zone', 'id_secteur', DB::raw('ST_AsGeoJSON(geom_zone) as geojson'))
            ->where('id_zone', $id)
            ->first();

        if (!$zone)
            abort(404);

        // On récupère le secteur associé pour l'afficher sur la fiche
        $secteur = Secteur::find($zone->id_secteur);

        return view('zones.show', compact('zone', 'secteur'));
    }

    public function edit($id)
    {
        $zone = DB::table('Zone')
            ->select('id_zone', 'nom_zone', 'code_zone', 'id_secteur', DB::raw('ST_AsGeoJSON(geom_zone) as geojson'))
            ->where('id_zone', $id)
            ->first();

        if (!$zone)
            abort(404);

        $secteurs = Secteur::all();
        return view('zones.edit', compact('zone', 'secteurs'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nom_zone' => 'required|string|max:80',
            'code_zone' => 'nullable|string|max:10',
            'id_secteur' => 'required|integer|exists:secteur,id_secteur',
            'geojson_data' => 'nullable|json'
        ]);

        $zone = Zone::findOrFail($id);
        $zone->nom_zone = $request->nom_zone;
        $zone->code_zone = $request->code_zone;
        $zone->id_secteur = $request->id_secteur;
        $zone->save();

        if ($request->filled('geojson_data')) {
            DB::update(
                'UPDATE "Zone" 
                 SET geom_zone = ST_Multi(ST_SetSRID(ST_GeomFromGeoJSON(?), 4326)) 
                 WHERE id_zone = ?',
                [$request->geojson_data, $id]
            );
        }

        return redirect()->route('zones.show', $id)->with('success', 'Zone mise à jour.');
    }

    public function destroy($id)
    {
        $zone = Zone::findOrFail($id);
        $zone->delete();
        return redirect()->route('zones.index')->with('success', 'Zone supprimée.');
    }
}
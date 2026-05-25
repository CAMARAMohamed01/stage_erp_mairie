<?php

namespace App\Http\Controllers;

use App\Models\Parcelle;
use App\Models\LieuDit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParcelleController extends Controller
{
    public function index()
    {
        $parcelles = Parcelle::with('lieuDit')->get();
        return view('parcelles.index', compact('parcelles'));
    }

    public function create()
    {
        $lieuxDits = LieuDit::orderBy('nom_lieu_dit')->get();
        return view('parcelles.create', compact('lieuxDits'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'num_parcelle' => 'required|string|max:5',
            'section_cadastrale' => 'required|string|max:1',
            'type_parcelle' => 'nullable|string|max:50',
            'surface_cadastrale' => 'nullable|numeric',
            'id_lieu_dit' => 'required|integer|exists:lieu_dit,id_lieu_dit',
            'geojson_data' => 'nullable|json'
        ]);

        $parcelle = new Parcelle();
        $parcelle->num_parcelle = $request->num_parcelle;
        $parcelle->section_cadastrale = strtoupper($request->section_cadastrale);
        $parcelle->type_parcelle = $request->type_parcelle;
        $parcelle->surface_cadastrale = $request->surface_cadastrale;
        $parcelle->id_lieu_dit = $request->id_lieu_dit;
        $parcelle->save();

        if ($request->filled('geojson_data')) {
            DB::update(
                "UPDATE parcelle 
                 SET geom_parcelle = ST_Multi(ST_SetSRID(ST_GeomFromGeoJSON(?), 4326)) 
                 WHERE id_parcelle = ?",
                [$request->geojson_data, $parcelle->id_parcelle]
            );
        }

        return redirect()->route('parcelles.index')->with('success', 'Parcelle cadastrale créée.');
    }

    public function show($id)
    {
        $parcelle = DB::table('parcelle')
            ->select('id_parcelle', 'num_parcelle', 'section_cadastrale', 'surface_cadastrale', 'type_parcelle', 'id_lieu_dit', DB::raw('ST_AsGeoJSON(geom_parcelle) as geojson'))
            ->where('id_parcelle', $id)
            ->first();

        if (!$parcelle)
            abort(404);

        $lieuDit = LieuDit::find($parcelle->id_lieu_dit);

        return view('parcelles.show', compact('parcelle', 'lieuDit'));
    }

    public function edit($id)
    {
        $parcelle = DB::table('parcelle')
            ->select('id_parcelle', 'num_parcelle', 'section_cadastrale', 'surface_cadastrale', 'type_parcelle', 'id_lieu_dit', DB::raw('ST_AsGeoJSON(geom_parcelle) as geojson'))
            ->where('id_parcelle', $id)
            ->first();

        if (!$parcelle)
            abort(404);

        $lieuxDits = LieuDit::orderBy('nom_lieu_dit')->get();
        return view('parcelles.edit', compact('parcelle', 'lieuxDits'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'num_parcelle' => 'required|string|max:5',
            'section_cadastrale' => 'required|string|max:1',
            'type_parcelle' => 'nullable|string|max:50',
            'surface_cadastrale' => 'nullable|numeric',
            'id_lieu_dit' => 'required|integer|exists:lieu_dit,id_lieu_dit',
            'geojson_data' => 'nullable|json'
        ]);

        $parcelle = Parcelle::findOrFail($id);
        $parcelle->num_parcelle = $request->num_parcelle;
        $parcelle->section_cadastrale = strtoupper($request->section_cadastrale);
        $parcelle->type_parcelle = $request->type_parcelle;
        $parcelle->surface_cadastrale = $request->surface_cadastrale;
        $parcelle->id_lieu_dit = $request->id_lieu_dit;
        $parcelle->save();

        if ($request->filled('geojson_data')) {
            DB::update(
                "UPDATE parcelle 
                 SET geom_parcelle = ST_Multi(ST_SetSRID(ST_GeomFromGeoJSON(?), 4326)) 
                 WHERE id_parcelle = ?",
                [$request->geojson_data, $id]
            );
        }

        return redirect()->route('parcelles.show', $id)->with('success', 'Parcelle mise à jour.');
    }

    public function destroy($id)
    {
        $parcelle = Parcelle::findOrFail($id);
        $parcelle->delete();
        return redirect()->route('parcelles.index')->with('success', 'Parcelle supprimée.');
    }
}
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
            'section_cadastrale' => 'required|string|min:1|max:2',
            'type_parcelle' => 'nullable|string|max:50',
            'surface_cadastrale' => 'nullable|numeric',
            // id_lieu_dit est maintenant nullable
            'id_lieu_dit' => 'nullable|integer|exists:lieu_dit,id_lieu_dit',
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
        $parcelle = Parcelle::with(['lieuDit', 'batiments', 'lieuxPublics', 'immobilisation'])
            ->select('*', DB::raw('ST_AsGeoJSON(geom_parcelle) as geojson'))
            ->findOrFail($id);

        $proprietaires = DB::table('proprio_parcelle')
            ->join('tiers', 'proprio_parcelle.id_tiers', '=', 'tiers.id_tiers')
            ->leftJoin('tiers_physique', 'tiers.id_tiers', '=', 'tiers_physique.id_tiers')
            ->leftJoin('tiers_morale', 'tiers.id_tiers', '=', 'tiers_morale.id_tiers')
            ->where('proprio_parcelle.id_parcelle', $id)
            ->select(
                'proprio_parcelle.*',
                'tiers.type_tiers',
                'tiers_physique.nom_tiers',
                'tiers_physique.prenom_tiers',
                'tiers_morale.raison_sociale'
            )->get();

        $dossiersUrba = DB::table('dossier_parcelle')
            ->join('dossier_urba', 'dossier_parcelle.id_dossier', '=', 'dossier_urba.id_dossier')
            ->where('dossier_parcelle.id_parcelle', $id)->get();

        $documents = DB::table('document')->where('id_parcelle', $id)->orderByDesc('date_upload')->get();

        $tousLesTiers = DB::table('tiers')
            ->leftJoin('tiers_physique', 'tiers.id_tiers', '=', 'tiers_physique.id_tiers')
            ->leftJoin('tiers_morale', 'tiers.id_tiers', '=', 'tiers_morale.id_tiers')
            ->select('tiers.id_tiers', 'tiers.type_tiers', 'tiers_physique.nom_tiers', 'tiers_physique.prenom_tiers', 'tiers_morale.raison_sociale')
            ->get();

        return view('parcelles.show', compact('parcelle', 'proprietaires', 'dossiersUrba', 'documents', 'tousLesTiers'));
    }

    public function ajouterProprietaire(Request $request, $id)
    {
        $request->validate([
            'id_tiers' => 'required|integer|exists:tiers,id_tiers',
            'date_acquisition' => 'required|date',
            'pourcentage_part' => 'required|numeric|min:0|max:100',
            'prix_parcelle' => 'nullable|numeric|min:0'
        ]);

        $existe = DB::table('proprio_parcelle')
            ->where('id_parcelle', $id)
            ->where('id_tiers', $request->id_tiers)
            ->exists();

        if ($existe) {
            return redirect()->back()->with('error', '🛑 Ce tiers est déjà associé à cette parcelle.');
        }

        DB::table('proprio_parcelle')->insert([
            'id_parcelle' => $id,
            'id_tiers' => $request->id_tiers,
            'date_acquisition' => $request->date_acquisition,
            'pourcentage_part' => $request->pourcentage_part,
            'prix_parcelle' => $request->prix_parcelle
        ]);

        return redirect()->back()->with('success', '👥 Propriétaire ajouté avec succès à la parcelle.');
    }

    public function retirerProprietaire($id, $idTiers)
    {
        DB::table('proprio_parcelle')
            ->where('id_parcelle', $id)
            ->where('id_tiers', $idTiers)
            ->delete();

        return redirect()->back()->with('success', '🗑️ Le propriétaire a été dissocié de la parcelle.');
    }

    public function destroy($id)
    {
        //dd("Début de la méthode destroy pour l'ID : " . $id);
        $parcelle = Parcelle::findOrFail($id);

        $batimentsCount = DB::table('batiment')->where('id_parcelle', $id)->count();
        // On vérifie la table pivot espace_parcelle
        $lieuxCount = DB::table('espace_parcelle')->where('id_parcelle', $id)->count();

        if ($batimentsCount > 0 || $lieuxCount > 0) {
            return redirect()->back()->with('error', "🛑 Suppression impossible : cette parcelle cadastrale contient actuellement {$batimentsCount} bâtiment(s) et {$lieuxCount} lieu(x) public(s). Veuillez d'abord modifier ces éléments.");
        }

        try {
            DB::beginTransaction();

            DB::table('proprio_parcelle')->where('id_parcelle', $id)->delete();
            DB::table('dossier_parcelle')->where('id_parcelle', $id)->delete();
            DB::table('gestionnaire_parcelle')->where('id_parcelle', $id)->delete();

            DB::table('document')->where('id_parcelle', $id)->update(['id_parcelle' => null]);

            $parcelle->delete();

            DB::commit();
            return redirect()->route('parcelles.index')->with('success', '✅ La parcelle cadastrale a été supprimée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', '🛑 Une erreur est survenue lors de la suppression : ' . $e->getMessage());
        }
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
            'section_cadastrale' => 'required|string|min:1|max:2',
            'type_parcelle' => 'nullable|string|max:50',
            'surface_cadastrale' => 'nullable|numeric',
            'id_lieu_dit' => 'nullable|integer|exists:lieu_dit,id_lieu_dit',
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
}
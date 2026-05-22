<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
// shema et stockage pour la gestion des documents liés aux ouvrages (si applicable)
//Shema
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
class OuvrageController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('ouvrage')
            ->leftJoin('voie', 'ouvrage.id_voie', '=', 'voie.id_voie')
            ->select('ouvrage.*', 'voie.nom_voie');

        // Filtrage / Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('ouvrage.nom_ouvrage', 'ilike', "%{$search}%")
                ->orWhere('ouvrage.type_ouvrage', 'ilike', "%{$search}%")
                ->orWhere('ouvrage.franchissement', 'ilike', "%{$search}%");
        }

        $ouvrages = $query->orderBy('nom_ouvrage')->paginate(15);

        return view('ouvrages.index', compact('ouvrages'));
    }
    public function create()
    {
        $voies = DB::table('voie')->orderBy('nom_voie')->get();
        return view('ouvrages.create', compact('voies'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom_ouvrage' => 'required|string|max:100',
            'type_ouvrage' => 'nullable|string|max:50',
            'domaine' => 'nullable|string|max:50',
            'voie_portee' => 'nullable|string|max:100',
            'franchissement' => 'nullable|string|max:50',
            'classe_longueur_mur' => 'nullable|string|max:50',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'date_transmission_etat' => 'nullable|date',
            'commentaire' => 'nullable|string',
            'id_voie' => 'nullable|exists:voie,id_voie',
            'geojson_data' => 'nullable|string', // On autorise le champ de la carte
        ]);
        $geojson = $validated['geojson_data'] ?? null;
        unset($validated['geojson_data']);

        if ($geojson) {
            $safeGeojson = str_replace("'", "''", $geojson);
            $validated['trace_geometrique'] = DB::raw("ST_SetSRID(ST_GeomFromGeoJSON('" . $safeGeojson . "'), 4326)");
        }
        // Gestion des booléens pour PostgreSQL
        $validated['sous_loi_didier'] = $request->has('sous_loi_didier');
        $validated['est_programme_national'] = $request->has('est_programme_national');
        $validated['dimension_sup_2m'] = $request->has('dimension_sup_2m');

        $id = DB::table('ouvrage')->insertGetId($validated, 'id_ouvrage');

        return redirect()->route('ouvrages.show', $id)->with('success', 'Ouvrage créé avec succès.');
    }

    public function edit($id)
    {
        $ouvrage = DB::table('ouvrage')
            ->select('ouvrage.*', DB::raw('ST_AsGeoJSON(trace_geometrique) as geojson'))
            ->where('id_ouvrage', $id)
            ->first();

        if (!$ouvrage)
            abort(404);

        $voies = DB::table('voie')->orderBy('nom_voie')->get();

        return view('ouvrages.edit', compact('ouvrage', 'voies'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nom_ouvrage' => 'required|string|max:100',
            'type_ouvrage' => 'nullable|string|max:50',
            'domaine' => 'nullable|string|max:50',
            'voie_portee' => 'nullable|string|max:100',
            'franchissement' => 'nullable|string|max:50',
            'classe_longueur_mur' => 'nullable|string|max:50',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'date_transmission_etat' => 'nullable|date',
            'commentaire' => 'nullable|string',
            'id_voie' => 'nullable|exists:voie,id_voie',
            'geojson_data' => 'nullable|string', // On autorise le champ de la carte
        ]);
        $geojson = $validated['geojson_data'] ?? null;
        unset($validated['geojson_data']);

        if ($geojson) {
            $safeGeojson = str_replace("'", "''", $geojson);
            $validated['trace_geometrique'] = DB::raw("ST_SetSRID(ST_GeomFromGeoJSON('" . $safeGeojson . "'), 4326)");
        } else {
            $validated['trace_geometrique'] = null; // Si l'utilisateur efface la carte
        }
        $validated['sous_loi_didier'] = $request->has('sous_loi_didier');
        $validated['est_programme_national'] = $request->has('est_programme_national');
        $validated['dimension_sup_2m'] = $request->has('dimension_sup_2m');

        DB::table('ouvrage')->where('id_ouvrage', $id)->update($validated);

        return redirect()->route('ouvrages.show', $id)->with('success', 'Ouvrage mis à jour.');
    }

    public function destroy($id)
    {
        $ouvrage = DB::table('ouvrage')->where('id_ouvrage', $id)->first();
        if (!$ouvrage)
            abort(404);

        //On supprime les liaisons avec les communes avant de détruire l'ouvrage
        DB::table('ouvrage_partage')->where('id_ouvrage', $id)->delete();

        // On détache des tronçons
        DB::table('troncon')->where('id_ouvrage_lie', $id)->update(['id_ouvrage_lie' => null]);
        DB::table('troncon')->where('id_ouvrage_debut', $id)->update(['id_ouvrage_debut' => null]);
        DB::table('troncon')->where('id_ouvrage_fin', $id)->update(['id_ouvrage_fin' => null]);

        // Suppression des documents (si applicable)
        if (Schema::hasColumn('document', 'id_ouvrage')) {
            $documents = DB::table('document')->where('id_ouvrage', $id)->get();
            foreach ($documents as $doc) {
                if (Storage::disk('public')->exists($doc->chemin_stockage)) {
                    Storage::disk('public')->delete($doc->chemin_stockage);
                }
            }
            DB::table('document')->where('id_ouvrage', $id)->delete();
        }

        // Suppression de l'ouvrage
        DB::table('ouvrage')->where('id_ouvrage', $id)->delete();

        return redirect()->route('ouvrages.index')->with('success', 'Ouvrage et ses partages supprimés.');
    }

    public function show($id)
    {
        // 1. Récupération de l'ouvrage AVEC son tracé (trace_geometrique)
        $ouvrage = DB::table('ouvrage')
            ->leftJoin('voie', 'ouvrage.id_voie', '=', 'voie.id_voie')
            ->select(
                'ouvrage.*',
                'voie.nom_voie',
                'voie.numero_voie',
                DB::raw('ST_AsGeoJSON(ouvrage.trace_geometrique) as geojson') // C'est ICI que ça doit être !
            )
            ->where('ouvrage.id_ouvrage', $id)
            ->first();

        if (!$ouvrage)
            abort(404);

        // 2. Récupération des communes DÉJÀ liées (sans chercher de géométrie ici)
        $communesLiees = DB::table('commune_partenaire')
            ->join('ouvrage_partage', 'commune_partenaire.id_commune', '=', 'ouvrage_partage.id_commune')
            ->where('ouvrage_partage.id_ouvrage', $id)
            ->select('commune_partenaire.*') // Juste l'étoile ici, rien d'autre !
            ->orderBy('nom_commune')
            ->get();

        // 3. Récupération des communes DISPONIBLES (pas encore liées)
        $idsLies = $communesLiees->pluck('id_commune')->toArray();
        $communesDisponibles = DB::table('commune_partenaire')
            ->whereNotIn('id_commune', $idsLies)
            ->orderBy('nom_commune')
            ->get();

        return view('ouvrages.show', compact('ouvrage', 'communesLiees', 'communesDisponibles'));
    }

    // --- GESTION DES COMMUNES PARTENAIRES (AJOUT / SUPPRESSION) ---

    public function addCommune(Request $request, $id)
    {
        $request->validate([
            'id_commune' => 'required|exists:commune_partenaire,id_commune'
        ]);

        // Vérification de sécurité pour éviter les doublons SQL
        $existe = DB::table('ouvrage_partage')
            ->where('id_ouvrage', $id)
            ->where('id_commune', $request->id_commune)
            ->exists();

        if (!$existe) {
            DB::table('ouvrage_partage')->insert([
                'id_ouvrage' => $id,
                'id_commune' => $request->id_commune
            ]);
        }

        return redirect()->back()->with('success', 'Commune partenaire ajoutée avec succès.');
    }

    public function removeCommune($idOuvrage, $idCommune)
    {
        DB::table('ouvrage_partage')
            ->where('id_ouvrage', $idOuvrage)
            ->where('id_commune', $idCommune)
            ->delete();

        return redirect()->back()->with('success', 'La commune a été retirée du partage de cet ouvrage.');
    }



}
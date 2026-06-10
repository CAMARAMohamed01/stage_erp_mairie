<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // ➕ AJOUT pour la gestion du disque
use App\Models\Voie;
use App\Models\Troncon;
use App\Models\Zone;
use App\Models\Ouvrage;

class TronconController extends Controller
{
    public function create(Request $request)
    {
        $selectedVoieId = $request->query('id_voie');
        $voies = Voie::all();
        $zones = Zone::all();
        $ouvrages = Ouvrage::all();

        return view('troncons.create', compact('selectedVoieId', 'voies', 'zones', 'ouvrages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_voie' => 'nullable|exists:voie,id_voie',
            'numero_troncon' => 'required|string|max:150|unique:troncon,numero_troncon',
            'nom_portion' => 'nullable|string|max:100',
            'pk_debut' => 'nullable|numeric',
            'pk_fin' => 'nullable|numeric',
            'repere_physique_debut' => 'nullable|string|max:100',
            'repere_physique_fin' => 'nullable|string|max:100',
            'type_revetement' => 'nullable|string|max:50',
            'date_dernier_goudronnage' => 'nullable|date',
            'etat_physique' => 'nullable|string|max:50',
            'gabarit_accessibilite' => 'nullable|string|max:50',
            'paysage_environnement' => 'nullable|string|max:50',
            'geojson_data' => 'nullable|string',
            'id_zone' => 'nullable|exists:Zone,id_zone',
            'id_ouvrage_lie' => 'nullable|exists:ouvrage,id_ouvrage',
            'id_ouvrage_debut' => 'nullable|exists:ouvrage,id_ouvrage',
            'id_ouvrage_fin' => 'nullable|exists:ouvrage,id_ouvrage',
        ], [
            'numero_troncon.unique' => '⚠️ Ce numéro de tronçon existe déjà dans la base de données.'
        ]);

        $geojson = $validated['geojson_data'] ?? null;
        unset($validated['geojson_data']);

        if ($geojson) {
            $safeGeojson = str_replace("'", "''", $geojson);
            $validated['trace_geo'] = DB::raw("ST_SetSRID(ST_GeomFromGeoJSON('" . $safeGeojson . "'), 4326)");
        }

        $id = DB::table('troncon')->insertGetId($validated, 'id_troncon');

        return redirect()->route('troncons.show', $id)->with('success', 'Le tronçon a été créé avec succès.');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'id_voie' => 'nullable|exists:voie,id_voie',
            'numero_troncon' => 'required|string|max:150|unique:troncon,numero_troncon,' . $id . ',id_troncon',
            'nom_portion' => 'nullable|string|max:100',
            'pk_debut' => 'nullable|numeric',
            'pk_fin' => 'nullable|numeric',
            'repere_physique_debut' => 'nullable|string|max:100',
            'repere_physique_fin' => 'nullable|string|max:100',
            'type_revetement' => 'nullable|string|max:50',
            'date_dernier_goudronnage' => 'nullable|date',
            'etat_physique' => 'nullable|string|max:50',
            'gabarit_accessibilite' => 'nullable|string|max:50',
            'paysage_environnement' => 'nullable|string|max:50',
            'id_zone' => 'nullable|exists:Zone,id_zone',
            'id_ouvrage_lie' => 'nullable|exists:ouvrage,id_ouvrage',
            'id_ouvrage_debut' => 'nullable|exists:ouvrage,id_ouvrage',
            'id_ouvrage_fin' => 'nullable|exists:ouvrage,id_ouvrage',
            'geojson_data' => 'nullable|string',
        ]);

        $geojson = $validated['geojson_data'] ?? null;
        unset($validated['geojson_data']);

        if ($geojson) {
            $safeGeojson = str_replace("'", "''", $geojson);
            $validated['trace_geo'] = DB::raw("ST_SetSRID(ST_GeomFromGeoJSON('" . $safeGeojson . "'), 4326)");
        } else {
            $validated['trace_geo'] = null;
        }

        DB::table('troncon')->where('id_troncon', $id)->update($validated);

        return redirect()->route('troncons.show', $id)->with('success', 'Le tronçon a été mis à jour.');
    }

    public function show($id)
    {
        $troncon = DB::table('troncon')
            ->leftJoin('voie', 'troncon.id_voie', '=', 'voie.id_voie')
            ->leftJoin('Zone', 'troncon.id_zone', '=', 'Zone.id_zone')
            ->leftJoin('ouvrage', 'troncon.id_ouvrage_lie', '=', 'ouvrage.id_ouvrage')
            ->leftJoin('ouvrage as od', 'troncon.id_ouvrage_debut', '=', 'od.id_ouvrage')
            ->leftJoin('ouvrage as of', 'troncon.id_ouvrage_fin', '=', 'of.id_ouvrage')
            ->select(
                'troncon.*',
                'voie.nom_voie',
                'Zone.nom_zone',
                'ouvrage.nom_ouvrage as nom_ouvrage_lie',
                'od.nom_ouvrage as nom_ouvrage_debut',
                'of.nom_ouvrage as nom_ouvrage_fin',
                DB::raw('ST_AsGeoJSON(trace_geo) as geojson')
            )
            ->where('id_troncon', $id)
            ->first();

        if (!$troncon)
            abort(404, 'Tronçon introuvable.');

        $interventions = DB::table('intervention')->where('id_troncon', $id)->orderByDesc('date_ouverture')->get();
        $documents = DB::table('document')->where('id_troncon', $id)->orderByDesc('date_upload')->get();
        $equipements = DB::table('equipement')->where('id_troncon', $id)->orderBy('nom_equipement')->get();

        return view('troncons.show', compact('troncon', 'interventions', 'documents', 'equipements'));
    }

    public function edit($id)
    {
        $troncon = DB::table('troncon')->select('troncon.*', DB::raw('ST_AsGeoJSON(trace_geo) as geojson'))->where('id_troncon', $id)->first();
        if (!$troncon)
            abort(404, 'Tronçon introuvable.');

        $voies = DB::table('voie')->orderBy('nom_voie')->get();
        $zones = DB::table('Zone')->orderBy('nom_zone')->get();
        $ouvrages = DB::table('ouvrage')->orderBy('nom_ouvrage')->get();

        return view('troncons.edit', compact('troncon', 'voies', 'zones', 'ouvrages'));
    }

    public function destroy($id)
    {
        $troncon = DB::table('troncon')->where('id_troncon', $id)->first();
        if (!$troncon)
            abort(404, 'Tronçon introuvable.');

        $idVoieParente = $troncon->id_voie;

        // Cascade manuelle sécurisée
        $documents = DB::table('document')->where('id_troncon', $id)->get();
        foreach ($documents as $doc) {
            if (Storage::disk('public')->exists($doc->chemin_stockage)) {
                Storage::disk('public')->delete($doc->chemin_stockage);
            }
        }
        DB::table('document')->where('id_troncon', $id)->delete();
        DB::table('equipement')->where('id_troncon', $id)->delete();
        DB::table('intervention')->where('id_troncon', $id)->delete();
        DB::table('troncon')->where('id_troncon', $id)->delete();

        return redirect()->route('voies.show', $idVoieParente)->with('success', 'Le tronçon et toutes ses dépendances ont été supprimés.');
    }

    public function uploadDocument(Request $request, $idTroncon)
    {
        $request->validate([
            'fichier' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ]);

        $file = $request->file('fichier');
        $path = $file->store('documents/troncons', 'public');

        DB::table('document')->insert([
            'nom_fichier' => $file->getClientOriginalName(),
            'type_doc' => $file->getClientOriginalExtension(),
            'chemin_stockage' => $path,
            'taille_ko' => $file->getSize() / 1024,
            'date_upload' => now(),
            'id_troncon' => $idTroncon,
        ]);

        return back()->with('success', 'Le document a été rattaché au tronçon avec succès.');
    }

    // ➕ AJOUT : Méthode de suppression physique du document
    public function destroyDocument($id)
    {
        $document = DB::table('document')->where('id_document', $id)->first();

        if ($document) {
            if ($document->chemin_stockage && Storage::disk('public')->exists($document->chemin_stockage)) {
                Storage::disk('public')->delete($document->chemin_stockage);
            }
            DB::table('document')->where('id_document', $id)->delete();
            return redirect()->back()->with('success', '✅ Pièce jointe supprimée définitivement.');
        }

        return redirect()->back()->with('error', 'Fichier introuvable.');
    }
}
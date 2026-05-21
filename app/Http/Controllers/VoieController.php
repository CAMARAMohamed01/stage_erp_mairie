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
}
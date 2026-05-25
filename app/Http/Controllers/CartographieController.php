<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CartographieController extends Controller
{
    public function index()
    {
        // 1. Récupération des Secteurs (On ajoute code_secteur)
        $secteurs = DB::table('secteur')
            ->select('id_secteur', 'nom_secteur', 'code_secteur', DB::raw('ST_AsGeoJSON(geom_secteur) as geojson'))
            ->whereNotNull('geom_secteur')
            ->get();

        // 2. Récupération des Zones (On ajoute code_zone)
        $zones = DB::table('Zone')
            ->select('id_zone', 'nom_zone', 'code_zone', DB::raw('ST_AsGeoJSON(geom_zone) as geojson'))
            ->whereNotNull('geom_zone')
            ->get();

        // 3. Récupération des Parcelles (Déjà complet)
        $parcelles = DB::table('parcelle')
            ->select('id_parcelle', 'num_parcelle', 'section_cadastrale', DB::raw('ST_AsGeoJSON(geom_parcelle) as geojson'))
            ->whereNotNull('geom_parcelle')
            ->get();

        // 4. Récupération des Tronçons
        $troncons = DB::table('troncon')
            ->select('id_troncon', 'nom_portion', 'etat_physique', DB::raw('ST_AsGeoJSON(trace_geo) as geojson'))
            ->whereNotNull('trace_geo')
            ->get();

        return view('cartographie.index', compact('secteurs', 'zones', 'parcelles', 'troncons'));
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Secteur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class SecteurController extends Controller
{
    //

    public function store(Request $request)
    {
        // 1. Validation classique des champs texte
        $request->validate([
            'nom_secteur' => 'required|string|max:80',
            'code_secteur' => 'nullable|string|max:10',
            'geojson_data' => 'nullable|json' // C'est ici que Leaflet enverra le dessin
        ]);

        // 2. Création de l'enregistrement de base
        $secteur = new Secteur();
        $secteur->nom_secteur = $request->nom_secteur;
        $secteur->code_secteur = $request->code_secteur;
        $secteur->save(); // On sauvegarde d'abord pour générer l'ID (id_secteur)

        // 3. Traitement de la géométrie avec PostGIS
        if ($request->filled('geojson_data')) {
            // On met à jour la ligne qu'on vient de créer avec une requête SQL brute sécurisée
            // ST_Multi() force la conversion en MultiPolygon
            // ST_SetSRID(..., 4326) applique le système de coordonnées GPS
            DB::update(
                "UPDATE secteur 
             SET geom_secteur = ST_Multi(ST_SetSRID(ST_GeomFromGeoJSON(?), 4326)) 
             WHERE id_secteur = ?",
                [$request->geojson_data, $secteur->id_secteur]
            );
        }

        return redirect()->route('secteurs.index')->with('success', 'Secteur créé avec succès !');
    }
}
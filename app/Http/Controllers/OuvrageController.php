<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OuvrageController extends Controller
{
    public function index()
    {
        // On récupère les ouvrages avec le nom de la voie associée
        $ouvrages = DB::table('ouvrage')
            ->leftJoin('voie', 'ouvrage.id_voie', '=', 'voie.id_voie')
            ->select('ouvrage.*', 'voie.nom_voie')
            ->orderBy('nom_ouvrage')
            ->get();

        return view('ouvrages.index', compact('ouvrages'));
    }

    public function show($id)
    {
        $ouvrage = DB::table('ouvrage')
            ->leftJoin('voie', 'ouvrage.id_voie', '=', 'voie.id_voie')
            ->select('ouvrage.*', 'voie.nom_voie')
            ->where('id_ouvrage', $id)
            ->first();

        if (!$ouvrage)
            abort(404);

        return view('ouvrages.show', compact('ouvrage'));
    }

    public function create()
    {
        $voies = DB::table('voie')->orderBy('nom_voie')->get();
        return view('ouvrages.create', compact('voies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom_ouvrage' => 'required|max:100',
            'type_ouvrage' => 'nullable',
            'id_voie' => 'required|exists:voie,id_voie',
            // Ajoute ici d'autres validations selon tes champs obligatoires
        ]);

        DB::table('ouvrage')->insert($validated);

        return redirect()->route('ouvrages.index')->with('success', 'Ouvrage créé.');
    }
}
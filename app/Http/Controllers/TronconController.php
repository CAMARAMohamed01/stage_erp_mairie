<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Voie;
use App\Models\Troncon;
use App\Models\Zone;
use App\Models\Ouvrage;

class TronconController extends Controller
{
    public function create(Request $request)
    {
        // Permet de pré-sélectionner la voie si on vient de la page "show" d'une voie
        $selectedVoieId = $request->query('id_voie');

        $voies = Voie::all();
        $zones = Zone::all();
        $ouvrages = Ouvrage::all();

        return view('troncons.create', compact('selectedVoieId', 'voies', 'zones', 'ouvrages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_voie' => 'required|exists:voie,id_voie',
            'numero_troncon' => 'required|unique:troncon,numero_troncon',
            'pk_debut' => 'required|numeric',
            'pk_fin' => 'required|numeric|gt:pk_debut', // PK Fin doit être > PK Début
            'type_revetement' => 'nullable',
            'etat_physique' => 'nullable',
        ]);

        Troncon::create($validated);

        return redirect()->route('voies.show', $request->id_voie)
            ->with('success', 'Tronçon créé avec succès.');
    }
    public function show($id)
    {
        // 1. Récupération du tronçon avec ses dépendances (Voie, Zone, Ouvrage lié)
        $troncon = DB::table('troncon')
            ->leftJoin('voie', 'troncon.id_voie', '=', 'voie.id_voie')
            ->leftJoin('Zone', 'troncon.id_zone', '=', 'Zone.id_zone')
            ->leftJoin('ouvrage', 'troncon.id_ouvrage_lie', '=', 'ouvrage.id_ouvrage')
            ->select(
                'troncon.*',
                'voie.nom_voie',
                'Zone.nom_zone',
                'ouvrage.nom_ouvrage as nom_ouvrage_lie'
            )
            ->where('id_troncon', $id)
            ->first();

        if (!$troncon)
            abort(404, 'Tronçon introuvable.');

        // 2. Les interventions liées à ce tronçon
        $interventions = DB::table('intervention')
            ->where('id_troncon', $id)
            ->orderByDesc('date_ouverture')
            ->get();
        $documents = DB::table('document')
            ->where('id_troncon', $id)
            ->orderByDesc('date_upload')
            ->get();
        // 3. Les équipements installés sur ce tronçon
        $equipements = DB::table('equipement')
            ->where('id_troncon', $id)
            ->get();

        return view('troncons.show', compact('troncon', 'interventions', 'documents', 'equipements'));
    }

    public function uploadDocument(\Illuminate\Http\Request $request, $idTroncon)
    {
        $request->validate([
            'fichier' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120', // Max 5 Mo
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
}
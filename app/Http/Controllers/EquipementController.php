<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Equipement;
use App\Models\FamilleEquipement;
use App\Models\Local;
use App\Models\LieuPublic;
use App\Models\ControleReglementaire;
use Illuminate\Support\Facades\DB;
class EquipementController extends Controller
{
    //
    public function index()
    {
        // On récupère tous les équipements
        $equipements = Equipement::all();

        // On retourne la vue avec les données
        return view('equipements.index', compact('equipements'));
    }

    // N'oublie pas d'importer tes modèles en haut si ce n'est pas fait !

    public function create()
    {
        $familles = FamilleEquipement::orderBy('libelle_famille', 'asc')->get();

        // On charge les emplacements possibles
        // (Assure-toi d'avoir créé ces modèles avec leurs noms de table respectifs)
        $locaux = Local::orderBy('nom_local', 'asc')->get();
        $lieux = LieuPublic::orderBy('nom_lieu', 'asc')->get();

        // On charge les contrôles réglementaires existants
        $controles = ControleReglementaire::orderBy('designation', 'asc')->get();

        return view('equipements.create', compact('familles', 'locaux', 'lieux', 'controles'));
    }

    public function store(Request $request)
    {
        // 1. Validation complète
        $validated = $request->validate([
            'nom_equipement' => 'required|max:80',
            'id_famille' => 'required|exists:famille_equipement,id_famille',
            'marque' => 'nullable|max:50',
            'couleur' => 'nullable|max:50',
            'reference_serie' => 'nullable|max:80',
            'date_achat' => 'nullable|date',
            'duree_garantie_mois' => 'nullable|max:50',
            'etat_fonctionnement' => 'nullable|max:50',
            // 'id_local' => 'nullable|exists:local,id_local',
            'id_local' => 'nullable|exists:local_,id_local',
            'id_lieu' => 'nullable|exists:lieux_publics,id_lieu',
            'dates_controles' => 'nullable|array',
        ]);

        // 2. Création de l'équipement avec les champs directs
        $equipementData = $validated;
        unset($equipementData['dates_controles']);

        // 2. Création de l'équipement UNIQUEMENT avec ses propres colonnes
        $equipement = Equipement::create($equipementData);

        // 3. Liaison avec la table pivot (soumis_a_controle)
        if ($request->has('controles')) {
            $pivotData = [];

            foreach ($request->controles as $id_controle) {
                // On récupère la date correspondante à cet ID de contrôle
                $date = $request->dates_controles[$id_controle] ?? null;

                // On prépare le tableau pour la table pivot
                $pivotData[$id_controle] = ['date_controle' => $date];
            }

            // On attache les contrôles avec leurs dates respectives
            $equipement->controles()->attach($pivotData);
        }

        return redirect()->route('equipements.index')->with('success', 'Équipement complet enregistré !');
    }

    public function show($id)
    {
        // On récupère l'équipement par sa clé primaire en "chargeant" ses relations (Eager Loading)
        $equipement = Equipement::with(['famille', 'local', 'lieuPublic', 'controles'])->findOrFail($id);

        return view('equipements.show', compact('equipement'));
    }
}
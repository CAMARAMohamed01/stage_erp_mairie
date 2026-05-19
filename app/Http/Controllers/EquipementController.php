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
    public function index(Request $request)
    {
        // 1. Initialisation de la requête 
        $query = Equipement::query()->with('famille');

        // 2. Application des filtres
        if ($request->filled('search')) {
            $query->where('nom_equipement', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('famille')) {
            $query->where('id_famille', $request->famille);
        }

        if ($request->filled('etat')) {
            $query->where('etat_fonctionnement', $request->etat);
        }

        // 3. Pagination
        $equipements = $query->latest()->paginate(15);

        // 4. RÉCUPÉRATION DES FAMILLES 
        $familles = FamilleEquipement::orderBy('libelle_famille', 'asc')->get();

        // 5. Retour de la vue avec les DEUX variables
        return view('equipements.index', compact('equipements', 'familles'));
    }


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
        $equipement = Equipement::with([
            'famille',
            'local',
            'lieuPublic',
            'controles',
            'interventions' => function ($query) {
                $query->orderBy('date_ouverture', 'desc'); // Historique trié
            },
            'service',         // Ajout relation service
            'immobilisation'   // Ajout relation immo (si définie dans ton modèle)
        ])->findOrFail($id);

        return view('equipements.show', compact('equipement'));
    }

    public function edit($id)
    {
        // On récupère l'équipement avec ses relations pivot
        $equipement = Equipement::with('controles')->findOrFail($id);

        // On récupère toutes les listes pour les menus déroulants
        $familles = FamilleEquipement::orderBy('libelle_famille')->get();
        $locaux = Local::orderBy('nom_local')->get();
        $lieux = LieuPublic::orderBy('nom_lieu')->get();
        $controles = ControleReglementaire::orderBy('designation')->get();

        return view('equipements.edit', compact('equipement', 'familles', 'locaux', 'lieux', 'controles'));
    }

    public function update(Request $request, $id)
    {
        $equipement = Equipement::findOrFail($id);

        // 1. Validation (identique au store)
        $validated = $request->validate([
            'nom_equipement' => 'required|max:80',
            'id_famille' => 'required|exists:famille_equipement,id_famille',
            'marque' => 'nullable|max:50',
            'couleur' => 'nullable|max:50',
            'reference_serie' => 'nullable|max:80',
            'date_achat' => 'nullable|date',
            'duree_garantie_mois' => 'nullable|max:50',
            'etat_fonctionnement' => 'nullable|max:50',
            'id_local' => 'nullable|exists:local_,id_local',
            'id_lieu' => 'nullable|exists:lieux_publics,id_lieu',
            'dates_controles' => 'nullable|array',
        ]);

        // 2. Mise à jour de l'équipement (sans les dates_controles)
        $equipementData = $validated;
        unset($equipementData['dates_controles']);
        $equipement->update($equipementData);

        // 3. Synchronisation des contrôles (Table Pivot)
        $pivotData = [];
        if ($request->has('controles')) {
            foreach ($request->controles as $id_controle) {
                $date = $request->dates_controles[$id_controle] ?? null;
                $pivotData[$id_controle] = ['date_controle' => $date];
            }
        }

        // sync() va supprimer les anciens liens et créer les nouveaux/mis à jour
        $equipement->controles()->sync($pivotData);

        return redirect()->route('equipements.show', $equipement->id_equipement)
            ->with('success', 'Équipement mis à jour avec succès !');
    }
    public function destroy($id)
    {
        // 1. On récupère l'équipement
        $equipement = Equipement::findOrFail($id);

        // 2. IMPORTANT : On détache les contrôles de la table pivot 
        // pour ne pas laisser de données fantômes ou bloquer la suppression
        $equipement->controles()->detach();

        // 3. On supprime l'équipement de la base de données
        $equipement->delete();

        // 4. On redirige vers l'inventaire avec un message
        return redirect()->route('equipements.index')
            ->with('success', 'L\'équipement a été supprimé définitivement.');
    }
}
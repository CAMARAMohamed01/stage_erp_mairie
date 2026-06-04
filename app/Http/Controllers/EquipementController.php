<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Equipement;
use App\Models\FamilleEquipement;
use App\Models\Local;
use App\Models\LieuPublic;
use App\Models\ControleReglementaire;
use Illuminate\Support\Facades\DB;
use App\Models\Contrat;
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
        $contrats = Contrat::orderBy('numero_contrat')->get();
        $troncons = DB::table('troncon')->orderBy('numero_troncon')->get();
        // On charge les contrôles réglementaires existants
        $controles = ControleReglementaire::orderBy('designation', 'asc')->get();

        return view('equipements.create', compact('familles', 'locaux', 'lieux', 'controles', 'contrats', 'troncons'));
    }
    public function store(Request $request)
    {
        // dd($request->all(), $request->query('id_parent'));

        $validated = $request->validate([
            'nom_equipement' => 'required|max:80',
            'id_famille' => 'required|exists:famille_equipement,id_famille',
            'id_parent' => 'nullable|exists:equipement,id_equipement',
            'marque' => 'nullable|max:50',
            'couleur' => 'nullable|max:50',
            'reference_serie' => 'nullable|max:80',
            'date_achat' => 'nullable|date',
            'duree_garantie_mois' => 'nullable|max:50',
            'etat_fonctionnement' => 'nullable|max:50',
            'id_local' => 'nullable|exists:local_,id_local',
            'id_lieu' => 'nullable|exists:lieux_publics,id_lieu',
            'dates_controles' => 'nullable|array',
            'id_contrats' => 'nullable|array',
            'id_contrats.*' => 'exists:contrat,id_contrat',
            'id_troncon' => 'nullable|exists:troncon,id_troncon',
        ]);

        // Nettoyage des tableaux de pivots pour ne garder que les colonnes directes
        $equipementData = $validated;
        unset($equipementData['dates_controles']);
        unset($equipementData['id_contrats']);
        unset($equipementData['controles']);

        //  Si id_parent n'est pas dans le formulaire caché, on le récupère depuis l'URL
        if (!isset($equipementData['id_parent']) || is_null($equipementData['id_parent'])) {
            $equipementData['id_parent'] = $request->query('id_parent') ?: null;
        }

        //  Exécution sécurisée via une transaction de base de données
        $equipement = DB::transaction(function () use ($equipementData, $request) {

            // Création de l'équipement avec son id_parent maillé
            $newEquipement = Equipement::create($equipementData);

            // Liaison avec la table pivot des contrats
            if ($request->has('id_contrats')) {
                $newEquipement->contratsAdministratifs()->attach($request->id_contrats);
            }

            if ($request->has('controles')) {
                $pivotData = [];

                foreach ($request->controles as $id_controle) {
                    $date = $request->dates_controles[$id_controle] ?? null;
                    $pivotData[$id_controle] = ['date_controle' => $date];
                }

                $newEquipement->controles()->attach($pivotData);
            }

            return $newEquipement;
        });

        // Redirection intelligente
        if ($equipement->id_parent) {
            return redirect()->route('equipements.show', $equipement->id_parent)
                ->with('success', 'Le sous-composant technique a bien été rattaché et enregistré !');
        }

        return redirect()->route('equipements.index')
            ->with('success', 'Équipement principal enregistré avec succès !');
    }

    public function show($id)
    {
        $equipement = Equipement::with([
            'famille',
            'local',
            'lieuPublic',
            'controles',
            'equipementParent',
            'sousEquipements',
            'immobilisation',
            'service',
            'interventions' => function ($query) {
                $query->orderBy('date_ouverture', 'desc'); // Historique trié
            },
            'service',         // Ajout relation service
            'immobilisation'   // Ajout relation immo (si définie dans ton modèle)
        ])->findOrFail($id);

        $documents = DB::table('document')
            ->where('id_equipement', $id)
            ->orderByDesc('date_upload')
            ->get();

        return view('equipements.show', compact('equipement', 'documents'));
    }

    public function edit($id)
    {
        // On récupère l'équipement avec ses relations pivot
        $equipement = Equipement::with('controles', 'contratsAdministratifs')->findOrFail($id);

        // On récupère toutes les listes pour les menus déroulants
        $familles = FamilleEquipement::orderBy('libelle_famille')->get();
        $locaux = Local::orderBy('nom_local')->get();
        $lieux = LieuPublic::orderBy('nom_lieu')->get();
        $controles = ControleReglementaire::orderBy('designation')->get();
        $contrats = Contrat::orderBy('numero_contrat')->get();
        $troncons = DB::table('troncon')->orderBy('numero_troncon')->get();

        return view('equipements.edit', compact('equipement', 'familles', 'locaux', 'lieux', 'controles', 'contrats', 'troncons'));
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
            'id_contrats' => 'nullable|array',
            'id_contrats.*' => 'exists:contrat,id_contrat',
            'id_troncon' => 'nullable|exists:troncon,id_troncon',
        ]);

        // 2. Mise à jour de l'équipement (sans les dates_controles)
        $equipementData = $validated;
        unset($equipementData['dates_controles']);
        unset($equipementData['id_contrats']);
        unset($equipementData['controles']);
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

        $equipement->contratsAdministratifs()->sync($request->id_contrats ?? []);

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
        // Détache les contrats administratifs


        // 3. On supprime l'équipement de la base de données
        $equipement->delete();

        // 4. On redirige vers l'inventaire avec un message
        return redirect()->route('equipements.index')
            ->with('success', 'L\'équipement a été supprimé définitivement.');
    }
    public function uploadDocument(Request $request, $idEquipement)
    {
        $request->validate([
            'fichier' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120', // Max 5 Mo
        ]);

        $file = $request->file('fichier');
        $path = $file->store('documents/equipements', 'public');

        \App\Models\Document::create([
            'nom_fichier' => $file->getClientOriginalName(),
            'type_doc' => $file->getClientOriginalExtension(),
            'chemin_stockage' => $path,
            'taille_ko' => $file->getSize() / 1024,
            'date_upload' => now(),
            'id_equipement' => $idEquipement, // La clé étrangère cible l'équipement
        ]);

        return back()->with('success', 'Le document a été ajouté à l\'équipement avec succès.');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Contrat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContratController extends Controller
{
    // Lancer la liste des contrats
    public function index(Request $request)
    {
        $query = Contrat::with('tiers'); // On charge le tiers prestataire

        // Petit filtre rapide par type de contrat si besoin
        if ($request->filled('type_contrat')) {
            $query->where('type_contrat', $request->type_contrat);
        }

        $contrats = $query->orderBy('date_debut_contrat', 'desc')->paginate(15);

        // On récupère les types uniques pour le filtre
        $typesContrat = Contrat::select('type_contrat')->distinct()->pluck('type_contrat');

        return view('contrats.index', compact('contrats', 'typesContrat'));
    }

    // Afficher le formulaire de création
    public function create()
    {
        // On récupère les Tiers (comme on l'a fait pour les interventions)
        $tiers = DB::table('tiers')
            ->leftJoin('tiers_physique', 'tiers.id_tiers', '=', 'tiers_physique.id_tiers')
            ->leftJoin('tiers_morale', 'tiers.id_tiers', '=', 'tiers_morale.id_tiers')
            ->select(
                'tiers.id_tiers',
                'tiers.type_tiers',
                'tiers_physique.nom_tiers',
                'tiers_physique.prenom_tiers',
                'tiers_morale.raison_sociale'
            )
            ->orderByRaw('COALESCE(tiers_morale.raison_sociale, tiers_physique.nom_tiers) ASC')
            ->get();

        return view('contrats.create', compact('tiers'));
    }
    public function store(Request $request)
    {
        // 1. Validation stricte basée sur ton schéma SQL
        $validated = $request->validate([
            'numero_contrat' => 'nullable|max:30|unique:contrat,numero_contrat',
            'type_contrat' => 'required|max:50',
            'objet_contrat' => 'nullable|max:255',
            'date_signature_contrat' => 'nullable|date',
            'date_debut_contrat' => 'required|date',
            'date_fin_contrat' => 'nullable|date|after_or_equal:date_debut_contrat',
            'prix_mois' => 'nullable|numeric|min:0',
            'prix_annuel' => 'nullable|numeric|min:0',
            'duree_mois' => 'nullable|integer|min:0',
            'modalite_renouvellement' => 'nullable|max:255',
            'preavis_resiliation_mois' => 'nullable|integer|min:0',
            'code_imputation' => 'nullable|max:20',
            'lot' => 'nullable|max:20',
            'code_analytique' => 'nullable|max:100',
            'frequence_facturation' => 'nullable|max:100',
            'mode_reglement' => 'nullable|max:50',
            'date_echeance' => 'nullable|date',
            'id_tiers' => 'required|exists:tiers,id_tiers',
        ]);

        // Gestion du booléen (si la checkbox n'est pas cochée, elle n'est pas envoyée)
        $validated['revision_prix_prevue'] = $request->has('revision_prix_prevue');

        // 2. Création de l'enregistrement
        Contrat::create($validated);

        return redirect()->route('contrats.index')
            ->with('success', 'Le contrat a été enregistré avec succès !');
    }

    public function show($id)
    {
        // On charge le contrat avec son prestataire (tiers) et les éléments liés
        $contrat = Contrat::with([
            'tiers',
            // Si tu as défini ces relations dans ton modèle Contrat (sinon on fera autrement)
            // 'equipements', 
            // 'locaux'
        ])->findOrFail($id);

        // Pour l'instant, on récupère manuellement les éléments liés au contrat
        $equipementsLies = DB::table('equipement')
            ->where('id_contrat', $id)
            ->get();

        $locauxLies = DB::table('local_')
            ->where('id_contrat_assurance', $id)
            ->get();

        return view('contrats.show', compact('contrat', 'equipementsLies', 'locauxLies'));
    }
}
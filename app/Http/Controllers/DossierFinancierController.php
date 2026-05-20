<?php

namespace App\Http\Controllers;

use App\Models\DossierFinancier;
use App\Models\Contrat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DossierFinancierController extends Controller
{
    // Liste des dossiers comptables
    public function index(Request $request)
    {
        $query = DossierFinancier::with(['tiers', 'contrat']);

        if ($request->filled('statut')) {
            $query->where('statut_actuel', $request->statut);
        }

        $dossiers = $query->orderBy('id_dossier', 'desc')->paginate(15);
        $statuts = DossierFinancier::select('statut_actuel')->distinct()->pluck('statut_actuel');

        return view('finances.dossiers.index', compact('dossiers', 'statuts'));
    }

    // Formulaire de création
    public function create()
    {
        $contrats = Contrat::orderBy('numero_contrat')->get();

        // Récupération des tiers avec l'héritage moral/physique
        $tiers = DB::table('tiers')
            ->leftJoin('tiers_physique', 'tiers.id_tiers', '=', 'tiers_physique.id_tiers')
            ->leftJoin('tiers_morale', 'tiers.id_tiers', '=', 'tiers_morale.id_tiers')
            ->select('tiers.id_tiers', 'tiers_physique.nom_tiers', 'tiers_physique.prenom_tiers', 'tiers_morale.raison_sociale')
            ->orderByRaw('COALESCE(tiers_morale.raison_sociale, tiers_physique.nom_tiers) ASC')
            ->get();

        return view('finances.dossiers.create', compact('contrats', 'tiers'));
    }

    // Enregistrement en base
    public function store(Request $request)
    {
        $validated = $request->validate([
            'objet_dossier' => 'nullable|string|max:255',
            'numero_titre_recette' => 'nullable|string|max:50',
            'numero_devis' => 'nullable|string|max:50',
            'numero_engagement' => 'nullable|string|max:50',
            'numero_bon_commande' => 'nullable|string|max:50',
            'numero_bon_livraison' => 'nullable|string|max:50',
            'numero_facture' => 'nullable|string|max:50',
            'statut_actuel' => 'required|string|max:50',
            'date_constatation_recette' => 'nullable|date',
            'date_emission_titre' => 'nullable|date',
            'date_encaissement' => 'nullable|date',
            'date_reception_devis' => 'nullable|date',
            'date_signature_engagement' => 'nullable|date',
            'date_bon_livraison' => 'nullable|date',
            'date_service_fait' => 'nullable|date',
            'date_reception_facture' => 'nullable|date',
            'date_transmission_compta' => 'nullable|date',
            'id_contrat' => 'nullable|exists:contrat,id_contrat',
            'id_tiers' => 'nullable|exists:tiers,id_tiers',
        ]);

        DossierFinancier::create($validated);

        return redirect()->route('dossiers-financiers.index')
            ->with('success', 'Le dossier financier a été initialisé avec succès.');
    }

    // AFFICHER LES DÉTAILS D'UN DOSSIER
    public function show($id)
    {
        $dossier = DossierFinancier::with(['tiers', 'contrat'])->findOrFail($id);

        // Récupération des lignes financières associées
        $lignes = DB::table('ligne_financiere_facture_')
            ->where('id_dossier', $id)
            ->get();

        // Récupération des budgets disponibles pour le formulaire d'ajout
        $budgets = DB::table('enveloppe_budgetaire')
            ->orderBy('annee_exercice', 'desc')
            ->get();

        // Récupération des opérations comptables
        $operations = DB::table('operation_comptable')
            ->orderBy('numero_operation')
            ->get();

        return view('finances.dossiers.show', compact('dossier', 'lignes', 'budgets', 'operations'));
    }

    // AJOUTER UNE LIGNE FINANCIÈRE DE FACTURE
    public function ajouterLigne(Request $request, $id)
    {
        $dossier = DossierFinancier::findOrFail($id);

        $validated = $request->validate([
            'designation_ligne' => 'required|string|max:255',
            'montant_ht' => 'required|numeric|min:0',
            'montant_tva' => 'required|numeric|min:0',
            'montant_ttc' => 'required|numeric|min:0',
            'nature_charge' => 'nullable|string|max:30',
            'id_budget' => 'required',
            'id_operation' => 'nullable',
        ]);

        DB::table('ligne_financiere_facture_')->insert([
            'date_comptable' => now()->format('Y-m-d'),
            'designation_ligne' => $validated['designation_ligne'],
            'montant_ht' => $validated['montant_ht'],
            'montant_tva' => $validated['montant_tva'],
            'montant_ttc' => $validated['montant_ttc'],
            'nature_charge' => $validated['nature_charge'],
            'id_dossier' => $dossier->id_dossier,
            'id_budget' => $validated['id_budget'],
            'id_operation' => $validated['id_operation'] ?? $dossier->id_operation,
        ]);

        return redirect()->route('dossiers-financiers.show', $id)
            ->with('success', 'Ligne financière ajoutée avec succès.');
    }
}
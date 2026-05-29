<?php

namespace App\Http\Controllers;

use App\Models\DossierFinancier;
use App\Models\Contrat;
use App\Models\LigneFinanciereFacture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DossierFinancierController extends Controller
{
    // Liste des dossiers comptables
    public function index(Request $request)
    {
        // Correction de la clé : id_dossier_f
        $query = DossierFinancier::with(['tiers', 'contrat']);

        if ($request->filled('statut')) {
            $query->where('statut_actuel', $request->statut);
        }

        if ($request->filled('search')) {
            $query->where('objet_dossier', 'ilike', '%' . $request->search . '%');
        }

        $dossiers = $query->orderBy('id_dossier_f', 'desc')->paginate(15);
        $statuts = DossierFinancier::select('statut_actuel')->distinct()->pluck('statut_actuel');

        return view('finances.dossiers.index', compact('dossiers', 'statuts'));
    }

    // Formulaire de création
    // Formulaire de création (Avec aiguillage Dépense / Recette)
    public function create(Request $request)
    {
        $type = $request->query('type');

        // Si le type n'est pas bon ou absent, on affiche la page de choix intermédiaire
        if (!$type || !in_array($type, ['depense', 'recette'])) {
            return view('finances.dossiers.choice');
        }

        $contrats = Contrat::orderBy('numero_contrat')->get();

        $tiers = DB::table('tiers')
            ->leftJoin('tiers_physique', 'tiers.id_tiers', '=', 'tiers_physique.id_tiers')
            ->leftJoin('tiers_morale', 'tiers.id_tiers', '=', 'tiers_morale.id_tiers')
            ->select('tiers.id_tiers', 'tiers_physique.nom_tiers', 'tiers_physique.prenom_tiers', 'tiers_morale.raison_sociale')
            ->orderByRaw('COALESCE(tiers_morale.raison_sociale, tiers_physique.nom_tiers) ASC')
            ->get();

        return view('finances.dossiers.create', compact('contrats', 'tiers', 'type'));
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

    // Afficher les détails d'un dossier
    public function show($id)
    {
        // On charge le dossier et ses lignes ventilées en une seule fois
        $dossier = DossierFinancier::with(['tiers', 'contrat', 'lignes.operationComptable', 'lignes.enveloppeBudgetaire'])->findOrFail($id);

        $budgets = DB::table('enveloppe_budgetaire')->orderBy('annee_exercice', 'desc')->get();
        $operations = DB::table('operation_comptable')->orderBy('numero_operation')->get();

        return view('finances.dossiers.show', compact('dossier', 'budgets', 'operations'));
    }

    // Ajouter une ligne financière de facture via le modèle
    public function ajouterLigne(Request $request, $id)
    {
        $request->validate([
            'designation_ligne' => 'required|string|max:255',
            'montant_ht' => 'required|numeric|min:0',
            'montant_tva' => 'required|numeric|min:0',
            'montant_ttc' => 'required|numeric|min:0',
            'nature_charge' => 'nullable|string|max:30',
            'id_budget' => 'required|exists:enveloppe_budgetaire,id_budget',
            'id_operation' => 'nullable|exists:operation_comptable,id_operation',
        ]);

        LigneFinanciereFacture::create([
            'date_comptable' => now()->format('Y-m-d'),
            'designation_ligne' => $request->designation_ligne,
            'montant_ht' => $request->montant_ht,
            'montant_tva' => $request->montant_tva,
            'montant_ttc' => $request->montant_ttc,
            'nature_charge' => $request->nature_charge,
            'id_dossier_f' => $id, // 🌟 Correction de la colonne de liaison
            'id_budget' => $request->id_budget,
            'id_operation' => $request->id_operation,
        ]);

        return redirect()->route('dossiers-financiers.show', $id)
            ->with('success', 'La ligne financière a été imputée avec succès.');
    }
    // Formulaire d'édition du dossier
    public function edit($id)
    {
        $dossier = DossierFinancier::findOrFail($id);
        $contrats = \App\Models\Contrat::orderBy('numero_contrat')->get();

        $tiers = DB::table('tiers')
            ->leftJoin('tiers_physique', 'tiers.id_tiers', '=', 'tiers_physique.id_tiers')
            ->leftJoin('tiers_morale', 'tiers.id_tiers', '=', 'tiers_morale.id_tiers')
            ->select('tiers.id_tiers', 'tiers_physique.nom_tiers', 'tiers_physique.prenom_tiers', 'tiers_morale.raison_sociale')
            ->orderByRaw('COALESCE(tiers_morale.raison_sociale, tiers_physique.nom_tiers) ASC')
            ->get();

        return view('finances.dossiers.edit', compact('dossier', 'contrats', 'tiers'));
    }

    // Sauvegarde de la modification
    public function update(Request $request, $id)
    {
        $dossier = DossierFinancier::findOrFail($id);

        $validated = $request->validate([
            'objet_dossier' => 'nullable|string|max:255',
            'numero_titre_recette' => 'nullable|string|max:50',
            'numero_devis' => 'nullable|string|max:50',
            'numero_engagement' => 'nullable|string|max:50',
            'numero_bon_commande' => 'nullable|string|max:50',
            'numero_bon_livraison' => 'nullable|string|max:50',
            'numero_facture' => 'nullable|string|max:50',
            'statut_actuel' => 'required|string|max:50',
            'id_contrat' => 'nullable|exists:contrat,id_contrat',
            'id_tiers' => 'nullable|exists:tiers,id_tiers',
        ]);

        $dossier->update($validated);

        return redirect()->route('dossiers-financiers.show', $id)
            ->with('success', '✏️ Le dossier financier a été mis à jour.');
    }

    // Suppression sécurisée en cascade
    public function destroy($id)
    {
        $dossier = DossierFinancier::findOrFail($id);

        try {
            DB::beginTransaction();

            // 1. Suppression forcée de toutes les lignes de ventilation comptable
            $dossier->lignes()->delete();

            // 2. Dissociation des documents liés au dossier financier pour éviter les blocages
            DB::table('document')->where('id_dossier_f', $id)->update(['id_dossier_f' => null]);

            // 3. Suppression de la fiche principale
            $dossier->delete();

            DB::commit();

            return redirect()->route('dossiers-financiers.index')
                ->with('success', '🗑️ Le dossier financier et son historique d\'imputations ont été définitivement supprimés.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur lors du nettoyage de la pièce : ' . $e->getMessage());
        }
    }

    // ➕ PERTINENT : Supprimer une ligne de facture en direct
    public function supprimerLigne($idDossier, $idLigne)
    {
        LigneFinanciereFacture::where('id_dossier_f', $idDossier)->findOrFail($idLigne)->delete();
        return redirect()->back()->with('success', 'Ligne comptable retirée.');
    }

    // ➕ PERTINENT : Changer le statut budgétaire (Visé, Payé, Bloqué...) depuis la fiche
    public function updateStatut(Request $request, $id)
    {
        $request->validate(['statut_actuel' => 'required|string|max:50']);
        DossierFinancier::findOrFail($id)->update(['statut_actuel' => $request->statut_actuel]);
        return redirect()->back()->with('success', 'Statut comptable mis à jour.');
    }
}
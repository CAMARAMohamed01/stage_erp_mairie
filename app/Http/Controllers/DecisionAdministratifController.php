<?php

namespace App\Http\Controllers;

use App\Models\DecisionAdministratif;
use App\Models\OperationComptable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DecisionAdministratifController extends Controller
{
    public function index(Request $request)
    {
        $query = DecisionAdministratif::with('redacteur');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('numero_decision', 'ilike', '%' . $search . '%')
                    ->orWhere('intitule_decision', 'ilike', '%' . $search . '%');
            });
        }

        $decisions = $query->orderBy('date_decision', 'desc')->get();
        return view('decisions_admin.index', compact('decisions'));
    }

    public function create()
    {
        $agents = DB::table('utilisateur')->orderBy('nom_user')->get();
        return view('decisions_admin.create', compact('agents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero_decision' => 'required|string|max:50|unique:decision_administratif,numero_decision',
            'date_decision' => 'required|date',
            'intitule_decision' => 'required|string',
            'objet_decision' => 'nullable|string',
            'type_decision' => 'nullable|string|max:80',
            'id_user_redacteur' => 'nullable|exists:utilisateur,id_user',
        ]);

        $validated['teletransmission_prefecture'] = $request->has('teletransmission_prefecture');

        DecisionAdministratif::create($validated);
        return redirect()->route('decisions-admin.index')->with('success', 'Acte administratif enregistré.');
    }

    public function show($id)
    {
        $decision = DecisionAdministratif::with(['redacteur', 'operationsComptables'])->findOrFail($id);

        // On récupère toutes les opérations comptables pour pouvoir les lier via le menu déroulant
        $toutesLesOperations = OperationComptable::orderBy('numero_operation')->get();

        return view('decisions_admin.show', compact('decision', 'toutesLesOperations'));
    }
    public function edit($id)
    {
        $decision = DecisionAdministratif::findOrFail($id);
        $agents = DB::table('utilisateur')->orderBy('nom_user')->get();

        return view('decisions_admin.edit', compact('decision', 'agents'));
    }
    // --- MISE À JOUR D'UN ACTE ---
    public function update(Request $request, $id)
    {
        $decision = DecisionAdministratif::findOrFail($id);

        $validated = $request->validate([
            // On ignore l'ID de la décision actuelle pour la règle d'unicité
            'numero_decision' => 'required|string|max:50|unique:decision_administratif,numero_decision,' . $id . ',id_decision',
            'date_decision' => 'required|date',
            'intitule_decision' => 'required|string',
            'objet_decision' => 'nullable|string',
            'type_decision' => 'nullable|string|max:80',
            'id_user_redacteur' => 'nullable|exists:utilisateur,id_user',
        ]);

        // Les cases à cocher HTML ne sont pas envoyées si elles sont décochées. 
        // Le has() renvoie true si cochée, false si décochée.
        $validated['teletransmission_prefecture'] = $request->has('teletransmission_prefecture');

        // Sauvegarde des modifications
        $decision->update($validated);

        return redirect()->route('decisions-admin.show', $id)
            ->with('success', '✏️ L\'acte administratif a été mis à jour avec succès.');
    }
    // --- INTERACTION PIVOT : LIER UNE OPÉRATION COMPTABLE À UN ACTE ---
    public function lierOperation(Request $request, $id)
    {
        $request->validate([
            'id_operation' => 'required|exists:operation_comptable,id_operation'
        ]);

        $decision = DecisionAdministratif::findOrFail($id);

        // syncWithoutDetaching évite les doublons si l'opération est déjà liée
        $decision->operationsComptables()->syncWithoutDetaching([$request->id_operation]);

        return redirect()->back()->with('success', '🔗 L\'opération comptable a été rattachée à cet acte officiel.');
    }

    // --- INTERACTION PIVOT : DÉLIER ---
    public function delierOperation($id, $idOp)
    {
        $decision = DecisionAdministratif::findOrFail($id);
        $decision->operationsComptables()->detach($idOp);

        return redirect()->back()->with('success', '🔓 L\'opération comptable a été dissociée de l\'acte.');
    }
    // --- SUPPRESSION D'UN ACTE ---
    public function destroy($id)
    {
        $decision = DecisionAdministratif::findOrFail($id);

        try {
            DB::beginTransaction();

            // 1. On détache les opérations comptables liées (Nettoyage table pivot acte_operation)
            $decision->operationsComptables()->detach();

            // 2. Si des documents y étaient rattachés, on passe leur clé étrangère à NULL
            DB::table('document')->where('id_decision', $id)->update(['id_decision' => null]);

            // 3. Suppression finale de la décision
            $decision->delete();

            DB::commit();

            return redirect()->route('decisions-admin.index')
                ->with('success', '🗑️ L\'acte administratif a été supprimé définitivement.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }
    }
}
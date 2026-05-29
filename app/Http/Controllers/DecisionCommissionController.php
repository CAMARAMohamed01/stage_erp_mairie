<?php

namespace App\Http\Controllers;

use App\Models\DecisionCommission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DecisionCommissionController extends Controller
{
    public function index(Request $request)
    {

        $query = DecisionCommission::with(['projet', 'enregistreur', 'intervention', 'operationComptable']);


        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('commentaire_elus', 'ilike', '%' . $search . '%')
                    ->orWhereHas('projet', function ($qp) use ($search) {
                        $qp->where('nom_projet', 'ilike', '%' . $search . '%');
                    })
                    ->orWhereHas('operationComptable', function ($qo) use ($search) {
                        $qo->where('numero_operation', 'ilike', '%' . $search . '%');
                    });
            });
        }


        if ($request->filled('statut')) {
            $query->where('statut_decision', $request->input('statut'));
        }

        // FILTRE PAR ANNÉE DE COMMISSION
        if ($request->filled('annee')) {
            $query->whereYear('date_commission', $request->input('annee'));
        }

        // Récupération des résultats paginés en conservant les filtres dans l'URL
        $decisions = $query->orderByDesc('date_commission')
            ->paginate(15)
            ->withQueryString();

        // Récupération de la liste des statuts uniques pour alimenter dynamiquement le filtre
        $statutsDisponibles = ['Validé', 'En attente', 'Ajourné', 'Refusé'];

        return view('commissions.index', compact('decisions', 'statutsDisponibles'));
    }

    public function create()
    {
        $projets = DB::table('projet')->orderBy('nom_projet')->get();
        $operations = DB::table('operation_comptable')->orderBy('numero_operation')->get();

        // On récupère les interventions ouvertes ou récentes
        $interventions = DB::table('intervention')
            ->select('id_int', 'type_intervention', 'statut_global', 'date_ouverture')
            ->orderByDesc('date_ouverture')->take(50)->get();

        $agents = DB::table('utilisateur')->orderBy('nom_user')->get();

        return view('commissions.create', compact('projets', 'operations', 'interventions', 'agents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date_commission' => 'required|date',
            'statut_decision' => 'required|string|max:50',
            'commentaire_elus' => 'nullable|string',
            'id_projet' => 'nullable|exists:projet,id_projet',
            'id_enregistreur_decision' => 'nullable|exists:utilisateur,id_user',
            'id_int' => 'nullable|exists:intervention,id_int',
            'id_operation' => 'nullable|exists:operation_comptable,id_operation',
        ]);

        DecisionCommission::create($validated);

        return redirect()->route('decisions-commission.index')
            ->with('success', '⚖️ Délibération de la commission enregistrée avec succès.');
    }

    public function edit($id)
    {
        $decision = DecisionCommission::findOrFail($id);
        $projets = DB::table('projet')->orderBy('nom_projet')->get();
        $operations = DB::table('operation_comptable')->orderBy('numero_operation')->get();
        $interventions = DB::table('intervention')->orderByDesc('date_ouverture')->take(50)->get();
        $agents = DB::table('utilisateur')->orderBy('nom_user')->get();

        return view('commissions.edit', compact('decision', 'projets', 'operations', 'interventions', 'agents'));
    }

    public function update(Request $request, $id)
    {
        $decision = DecisionCommission::findOrFail($id);

        $validated = $request->validate([
            'date_commission' => 'required|date',
            'statut_decision' => 'required|string|max:50',
            'commentaire_elus' => 'nullable|string',
            'id_projet' => 'nullable|exists:projet,id_projet',
            'id_enregistreur_decision' => 'nullable|exists:utilisateur,id_user',
            'id_int' => 'nullable|exists:intervention,id_int',
            'id_operation' => 'nullable|exists:operation_comptable,id_operation',
        ]);

        $decision->update($validated);

        return redirect()->route('decisions-commission.index')
            ->with('success', '✏️ Arbitrage de commission mis à jour.');
    }

    public function destroy($id)
    {
        $decision = DecisionCommission::findOrFail($id);
        $decision->delete();

        return redirect()->route('decisions-commission.index')
            ->with('success', '🗑️ Délibération supprimée du registre.');
    }
}
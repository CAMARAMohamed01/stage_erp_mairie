<?php

namespace App\Http\Controllers;

use App\Models\EnveloppeBudgetaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnveloppeBudgetaireController extends Controller
{
    public function index()
    {
        $enveloppes = EnveloppeBudgetaire::leftJoin('service_mairie', 'enveloppe_budgetaire.id_service', '=', 'service_mairie.id_service')
            ->select('enveloppe_budgetaire.*', 'service_mairie.nom_service')
            ->orderByDesc('annee_exercice')->paginate(15);

        return view('finances.enveloppes.index', compact('enveloppes'));
    }

    public function create()
    {
        $services = DB::table('service_mairie')->orderBy('nom_service')->get();
        return view('finances.enveloppes.create', compact('services'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'annee_exercice' => 'required|integer|min:2020|max:2050',
            'montant_vote_ttc' => 'required|numeric|min:0',
            'id_service' => 'nullable|exists:service_mairie,id_service',
        ]);
        EnveloppeBudgetaire::create($validated);
        return redirect()->route('enveloppes-budgetaires.index')->with('success', 'Crédit budgétaire annuel alloué.');
    }

    public function edit($id)
    {
        $enveloppe = EnveloppeBudgetaire::findOrFail($id);
        $services = DB::table('service_mairie')->orderBy('nom_service')->get();
        return view('finances.enveloppes.edit', compact('enveloppe', 'services'));
    }

    public function update(Request $request, $id)
    {
        $enveloppe = EnveloppeBudgetaire::findOrFail($id);
        $validated = $request->validate([
            'annee_exercice' => 'required|integer|min:2020|max:2050',
            'montant_vote_ttc' => 'required|numeric|min:0',
            'id_service' => 'nullable|exists:service_mairie,id_service',
        ]);
        $enveloppe->update($validated);
        return redirect()->route('enveloppes-budgetaires.index')->with('success', 'Crédit mis à jour.');
    }

    public function destroy($id)
    {
        EnveloppeBudgetaire::findOrFail($id)->delete();
        return redirect()->route('enveloppes-budgetaires.index')->with('success', 'Ligne budgétaire retirée.');
    }
}
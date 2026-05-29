<?php

namespace App\Http\Controllers;

use App\Models\EnveloppeBudgetaire;
use App\Models\ArticleCompta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnveloppeBudgetaireController extends Controller
{
    public function index()
    {
        // On charge la relation avec les articles pour l'affichage du registre
        $enveloppes = EnveloppeBudgetaire::with('articles')
            ->leftJoin('service_mairie', 'enveloppe_budgetaire.id_service', '=', 'service_mairie.id_service')
            ->select('enveloppe_budgetaire.*', 'service_mairie.nom_service')
            ->orderByDesc('annee_exercice')->paginate(15);

        return view('finances.enveloppes.index', compact('enveloppes'));
    }

    public function create()
    {
        $services = DB::table('service_mairie')->orderBy('nom_service')->get();
        $articles = ArticleCompta::orderBy('numero_article')->get(); // ➕ Récupération des articles
        return view('finances.enveloppes.create', compact('services', 'articles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'annee_exercice' => 'required|integer|min:2020|max:2050',
            'montant_vote_ttc' => 'required|numeric|min:0',
            'id_service' => 'nullable|exists:service_mairie,id_service',
            'articles' => 'nullable|array', // ➕ Validation du tableau pivot
            'articles.*' => 'exists:article_compta,id_article'
        ]);

        // Nettoyage pour empêcher Eloquent de chercher une colonne physique "articles"
        $dataBudget = $validated;
        unset($dataBudget['articles']);

        $enveloppe = EnveloppeBudgetaire::create($dataBudget);

        // Enregistrement dans la table pivot `article_budget`
        if ($request->has('articles')) {
            $enveloppe->articles()->sync($request->articles);
        }

        return redirect()->route('enveloppes-budgetaires.index')
            ->with('success', ' Crédit budgétaire annuel alloué et lié aux articles.');
    }

    public function edit($id)
    {
        $enveloppe = EnveloppeBudgetaire::with('articles')->findOrFail($id);
        $services = DB::table('service_mairie')->orderBy('nom_service')->get();
        $articles = ArticleCompta::orderBy('numero_article')->get(); // ➕ Récupération des articles

        return view('finances.enveloppes.edit', compact('enveloppe', 'services', 'articles'));
    }

    public function update(Request $request, $id)
    {
        $enveloppe = EnveloppeBudgetaire::findOrFail($id);
        $validated = $request->validate([
            'annee_exercice' => 'required|integer|min:2020|max:2050',
            'montant_vote_ttc' => 'required|numeric|min:0',
            'id_service' => 'nullable|exists:service_mairie,id_service',
            'articles' => 'nullable|array',
            'articles.*' => 'exists:article_compta,id_article'
        ]);

        // Nettoyage avant modification
        $dataBudget = $validated;
        unset($dataBudget['articles']);

        $enveloppe->update($dataBudget);
        $enveloppe->articles()->sync($request->input('articles', []));

        return redirect()->route('enveloppes-budgetaires.index')->with('success', 'Crédit mis à jour.');
    }

    public function destroy($id)
    {
        $enveloppe = EnveloppeBudgetaire::findOrFail($id);

        // on détache d'abord les lignes pivots article_budget
        $enveloppe->articles()->detach();
        $enveloppe->delete();

        return redirect()->route('enveloppes-budgetaires.index')->with('success', 'Ligne budgétaire retirée.');
    }
}
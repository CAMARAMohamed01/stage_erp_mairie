<?php

namespace App\Http\Controllers;

use App\Models\ArticleCompta;
use App\Models\Chapitre;
use App\Models\EnveloppeBudgetaire;
use Illuminate\Http\Request;

class ArticleComptaController extends Controller
{
    public function index(Request $request)
    {
        $query = ArticleCompta::with('chapitres');
        if ($request->filled('search')) {
            $query->where('numero_article', 'ilike', '%' . $request->search . '%')
                ->orWhere('libelle_article', 'ilike', '%' . $request->search . '%');
        }
        $articles = ArticleCompta::with(['chapitres', 'enveloppes'])->orderBy('numero_article')->paginate(15);
        return view('finances.articles.index', compact('articles'));
    }

    public function create()
    {
        $chapitres = Chapitre::orderBy('numero_chapitre')->get();
        $enveloppes = EnveloppeBudgetaire::leftJoin('service_mairie', 'enveloppe_budgetaire.id_service', '=', 'service_mairie.id_service')
            ->select('enveloppe_budgetaire.*', 'service_mairie.nom_service')->orderByDesc('annee_exercice')->get();
        return view('finances.articles.create', compact('chapitres', 'enveloppes'));

    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero_article' => 'required|string|max:20|unique:article_compta,numero_article',
            'libelle_article' => 'required|string|max:150',
            'chapitres' => 'nullable|array',
            'chapitres.*' => 'exists:chapitre,id_chapitre'
        ]);

        $dataAccount = $validated;
        unset($dataAccount['chapitres'], $dataAccount['enveloppes']);

        $article = ArticleCompta::create($dataAccount);
        $article->update($dataAccount);
        $article->chapitres()->sync($request->input('chapitres', []));
        $article->enveloppes()->sync($request->input('enveloppes', []));
        if ($request->has('chapitres')) {
            $article->chapitres()->sync($request->chapitres);
        }

        return redirect()->route('articles-compta.index')->with('success', '🏷️ Article comptable ajouté.');
    }

    public function update(Request $request, $id)
    {
        $article = ArticleCompta::findOrFail($id);
        $validated = $request->validate([
            'numero_article' => 'required|string|max:20|unique:article_compta,numero_article,' . $id . ',id_article',
            'libelle_article' => 'required|string|max:150',
            'chapitres' => 'nullable|array',
            'chapitres.*' => 'exists:chapitre,id_chapitre'
        ]);

        $dataAccount = $validated;
        unset($dataAccount['chapitres'], $dataAccount['enveloppes']);

        $article->update($dataAccount);
        $article->chapitres()->sync($request->input('chapitres', []));
        $article->enveloppes()->sync($request->input('enveloppes', []));

        return redirect()->route('articles-compta.index')->with('success', '✏️ Article comptable mis à jour.');
    }

    public function edit($id)
    {
        $article = ArticleCompta::with('chapitres')->findOrFail($id);
        $chapitres = Chapitre::orderBy('numero_chapitre')->get();
        $enveloppes = EnveloppeBudgetaire::leftJoin('service_mairie', 'enveloppe_budgetaire.id_service', '=', 'service_mairie.id_service')
            ->select('enveloppe_budgetaire.*', 'service_mairie.nom_service')->orderByDesc('annee_exercice')->get();
        return view('finances.articles.edit', compact('article', 'chapitres', 'enveloppes'));
    }
    public function destroy($id)
    {
        $article = ArticleCompta::findOrFail($id);
        $article->chapitres()->detach(); // Déconnexion relationnelle pivot
        $article->delete();

        return redirect()->route('articles-compta.index')->with('success', '🗑️ Article supprimé de la nomenclature.');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Chapitre;
use App\Models\ArticleCompta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChapitreController extends Controller
{
    public function index(Request $request)
    {
        $query = Chapitre::with('articles');
        if ($request->filled('search')) {
            $query->where('numero_chapitre', 'ilike', '%' . $request->search . '%')
                ->orWhere('libelle_chapitre', 'ilike', '%' . $request->search . '%');
        }
        $chapitres = $query->orderBy('numero_chapitre')->paginate(15);
        return view('finances.chapitres.index', compact('chapitres'));
    }

    public function create()
    {
        $articles = ArticleCompta::orderBy('numero_article')->get();
        return view('finances.chapitres.create', compact('articles'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero_chapitre' => 'required|string|max:50|unique:chapitre,numero_chapitre',
            'libelle_chapitre' => 'required|string|max:150',
            'sens_financier' => 'required|string|max:50', // ➕ Ajouté
            'section_budgetaire' => 'required|string|max:50', // ➕ Ajouté (NOT NULL)
            'articles' => 'nullable|array',
            'articles.*' => 'exists:article_compta,id_article'
        ]);

        $chapitre = Chapitre::create($validated);

        if ($request->has('articles')) {
            $chapitre->articles()->sync($request->articles);
        }

        return redirect()->route('chapitres.index')->with('success', '📖 Chapitre budgétaire créé avec succès.');
    }

    public function update(Request $request, $id)
    {
        $chapitre = Chapitre::findOrFail($id);
        $validated = $request->validate([
            'numero_chapitre' => 'required|string|max:50|unique:chapitre,numero_chapitre,' . $id . ',id_chapitre',
            'libelle_chapitre' => 'required|string|max:150',
            'sens_financier' => 'required|string|max:50', // ➕ Ajouté
            'section_budgetaire' => 'required|string|max:50', // ➕ Ajouté
            'articles' => 'nullable|array',
            'articles.*' => 'exists:article_compta,id_article'
        ]);

        $chapitre->update($validated);
        $chapitre->articles()->sync($request->input('articles', []));

        return redirect()->route('chapitres.index')->with('success', '✏️ Chapitre mis à jour.');
    }

    public function edit($id)
    {
        $chapitre = Chapitre::with('articles')->findOrFail($id);
        $articles = ArticleCompta::orderBy('numero_article')->get();
        return view('finances.chapitres.edit', compact('chapitre', 'articles'));
    }

    public function destroy($id)
    {
        $chapitre = Chapitre::findOrFail($id);
        $chapitre->articles()->detach(); // Nettoyage table pivot
        $chapitre->delete();

        return redirect()->route('chapitres.index')->with('success', '🗑️ Chapitre retiré de la nomenclature.');
    }
}
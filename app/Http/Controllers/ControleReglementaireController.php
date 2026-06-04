<?php

namespace App\Http\Controllers;

use App\Models\ControleReglementaire;
use App\Models\TypeErp;
use Illuminate\Http\Request;

class ControleReglementaireController extends Controller
{
    public function index(Request $request)
    {
        // On charge la relation pivot avec le champ supplémentaire date_controle
        $query = ControleReglementaire::with([
            'typesErp' => function ($query) {
                $query->withPivot('date_controle');
            }
        ]);

        if ($request->filled('search')) {
            $query->where('designation', 'ilike', '%' . $request->search . '%')
                ->orWhere('domaine_technique', 'ilike', '%' . $request->search . '%');
        }

        $controles = $query->orderBy('designation')->get();
        return view('controles.index', compact('controles'));
    }

    public function create()
    {
        $typesErp = TypeErp::orderBy('categorie_erp')->orderBy('type_erp')->get();
        return view('controles.create', compact('typesErp'));
    }

    public function show($id)
    {
        // Eager loading des équipements et des types ERP avec la nouvelle colonne pivot
        $controle = ControleReglementaire::with([
            'typesErp' => function ($query) {
                $query->withPivot('date_controle');
            },
            'equipements'
        ])->findOrFail($id);

        return view('controles.show', compact('controle'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'designation' => 'required|string|max:160',
            'domaine_technique' => 'nullable|string|max:100',
            'est_legalement_obligatoire' => 'boolean',
            'frequence_mois' => 'nullable|integer|min:1',
            'type_controle' => 'nullable|string|max:80',
            'type_document_attendu' => 'nullable|string|max:100',
            'intervenant_prevu' => 'nullable|string|max:100',
            'types_erp' => 'nullable|array',
            'types_erp.*' => 'exists:type_erp,id_type_erp'
        ]);

        $validated['est_legalement_obligatoire'] = $request->has('est_legalement_obligatoire');

        $controle = ControleReglementaire::create($validated);

        if ($request->has('types_erp')) {
            $controle->typesErp()->attach($request->types_erp);
        }

        return redirect()->route('controles.index')->with('success', 'Contrôle réglementaire créé avec succès.');
    }

    public function edit($id)
    {
        $controle = ControleReglementaire::with('typesErp')->findOrFail($id);
        $typesErp = TypeErp::orderBy('categorie_erp')->orderBy('type_erp')->get();
        $erp_lies = $controle->typesErp->pluck('id_type_erp')->toArray();

        return view('controles.edit', compact('controle', 'typesErp', 'erp_lies'));
    }

    public function update(Request $request, $id)
    {
        $controle = ControleReglementaire::findOrFail($id);

        $validated = $request->validate([
            'designation' => 'required|string|max:160',
            'domaine_technique' => 'nullable|string|max:100',
            'est_legalement_obligatoire' => 'boolean',
            'frequence_mois' => 'nullable|integer|min:1',
            'type_controle' => 'nullable|string|max:80',
            'type_document_attendu' => 'nullable|string|max:100',
            'intervenant_prevu' => 'nullable|string|max:100',
            'types_erp' => 'nullable|array',
            'types_erp.*' => 'exists:type_erp,id_type_erp'
        ]);

        $validated['est_legalement_obligatoire'] = $request->has('est_legalement_obligatoire');

        $controle->update($validated);

        $controle->typesErp()->sync($request->types_erp ?? []);

        return redirect()->route('controles.show', $id)->with('success', 'Contrôle mis à jour avec succès.');
    }

    public function destroy($id)
    {
        $controle = ControleReglementaire::findOrFail($id);
        $controle->typesErp()->detach();
        $controle->delete();

        return redirect()->route('controles.index')->with('success', 'Contrôle supprimé.');
    }
}
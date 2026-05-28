<?php

namespace App\Http\Controllers;

use App\Models\ControleReglementaire;
use App\Models\TypeErp;
use Illuminate\Http\Request;

class ControleReglementaireController extends Controller
{
    public function index(Request $request)
    {
        // On charge les contrôles avec leurs ERP liés pour éviter le problème N+1
        $query = ControleReglementaire::with('typesErp');

        if ($request->filled('search')) {
            $query->where('designation', 'ilike', '%' . $request->search . '%')
                ->orWhere('domaine_technique', 'ilike', '%' . $request->search . '%');
        }

        $controles = $query->orderBy('designation')->get();
        return view('controles.index', compact('controles'));
    }

    public function create()
    {
        // On récupère tous les types ERP pour les afficher sous forme de cases à cocher
        $typesErp = TypeErp::orderBy('categorie_erp')->orderBy('type_erp')->get();
        return view('controles.create', compact('typesErp'));
    }
    public function show($id)
    {
        // On charge les ERP et les Équipements liés pour éviter les requêtes N+1
        $controle = ControleReglementaire::with(['typesErp', 'equipements'])->findOrFail($id);

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
            'types_erp' => 'nullable|array', // Le tableau des IDs cochés
            'types_erp.*' => 'exists:type_erp,id_type_erp'
        ]);

        // Assurer que le booléen est bien défini si la case n'est pas cochée
        $validated['est_legalement_obligatoire'] = $request->has('est_legalement_obligatoire');

        // 1. Création du contrôle
        $controle = ControleReglementaire::create($validated);

        // 2. Attachement aux types ERP dans la table pivot
        if ($request->has('types_erp')) {
            $controle->typesErp()->attach($request->types_erp);
        }

        return redirect()->route('controles.index')->with('success', 'Contrôle réglementaire créé avec succès.');
    }

    public function edit($id)
    {
        $controle = ControleReglementaire::with('typesErp')->findOrFail($id);
        $typesErp = TypeErp::orderBy('categorie_erp')->orderBy('type_erp')->get();

        // On récupère un tableau simple contenant uniquement les IDs des ERP liés à ce contrôle
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

        // 1. Mise à jour des informations
        $controle->update($validated);

        // 2. Synchronisation de la table pivot (supprime les décochés, ajoute les nouveaux)
        $controle->typesErp()->sync($request->types_erp ?? []);

        return redirect()->route('controles.show', $id)->with('success', 'Contrôle mis à jour avec succès.');
    }

    public function destroy($id)
    {
        $controle = ControleReglementaire::findOrFail($id);

        // On détache d'abord toutes les relations de la table pivot
        $controle->typesErp()->detach();

        // Ensuite on peut supprimer
        $controle->delete();

        return redirect()->route('controles.index')->with('success', 'Contrôle supprimé.');
    }
}
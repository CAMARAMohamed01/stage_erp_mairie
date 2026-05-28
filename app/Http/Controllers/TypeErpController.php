<?php

namespace App\Http\Controllers;

use App\Models\TypeErp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TypeErpController extends Controller
{
    // --- LISTE DES TYPES D'ERP ---
    public function index(Request $request)
    {
        $query = TypeErp::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('reglementation_applicable', 'ilike', '%' . $search . '%')
                ->orWhere('type_erp', 'ilike', '%' . $search . '%')
                ->orWhere('public_cible', 'ilike', '%' . $search . '%');
        }

        // Tri par catégorie (1 à 5) puis par type (L, M, R...)
        $types_erp = $query->orderBy('categorie_erp', 'asc')->orderBy('type_erp', 'asc')->get();

        return view('types_erp.index', compact('types_erp'));
    }

    // --- FORMULAIRE DE CRÉATION ---
    public function create()
    {
        return view('types_erp.create');
    }

    // --- SAUVEGARDE EN BASE ---
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reglementation_applicable' => 'required|string|max:80',
            'public_cible' => 'nullable|string|max:80',
            'categorie_erp' => 'nullable|integer|min:1|max:5', // Les catégories ERP vont généralement de 1 à 5
            'type_erp' => 'nullable|string|max:2', // Ex: L, M, N, O...
        ]);

        TypeErp::create($validated);

        return redirect()->route('types-erp.index')->with('success', 'La nouvelle catégorie ERP a été ajoutée.');
    }

    // --- FICHE DÉTAILLÉE ---
    public function show($id)
    {
        // 1. On récupère le type ERP avec ses contrôles réglementaires (via la table pivot)
        $type_erp = TypeErp::with('controles')->findOrFail($id);

        // 2. On cherche quels bâtiments de la commune sont classés avec cet ERP
        $batiments = DB::table('batiment')->where('id_type_erp', $id)->orderBy('nom_bat')->get();

        // 3. Pareil pour les lieux publics (s'ils ont un ERP)
        $lieux = DB::table('lieux_publics')->where('id_type_erp', $id)->orderBy('nom_lieu')->get();
        $batiments = DB::table('batiment')->where('id_type_erp', $id)->orderBy('nom_bat')->get();

        return view('types_erp.show', compact('type_erp', 'batiments', 'lieux'));
    }

    // --- FORMULAIRE DE MODIFICATION ---
    public function edit($id)
    {
        $type_erp = TypeErp::findOrFail($id);
        return view('types_erp.edit', compact('type_erp'));
    }

    // --- MISE À JOUR ---
    public function update(Request $request, $id)
    {
        $type_erp = TypeErp::findOrFail($id);

        $validated = $request->validate([
            'reglementation_applicable' => 'required|string|max:80',
            'public_cible' => 'nullable|string|max:80',
            'categorie_erp' => 'nullable|integer|min:1|max:5',
            'type_erp' => 'nullable|string|max:2',
        ]);

        $type_erp->update($validated);

        return redirect()->route('types-erp.show', $id)->with('success', 'Les informations du type ERP ont été mises à jour.');
    }

    // --- SUPPRESSION ---
    public function destroy($id)
    {
        $type_erp = TypeErp::findOrFail($id);

        // 1. On vérifie s'il y a des BÂTIMENTS liés (car id_type_erp est NOT NULL dans la table batiment)
        $batimentsCount = DB::table('batiment')->where('id_type_erp', $id)->count();

        if ($batimentsCount > 0) {
            // On bloque obligatoirement pour les bâtiments
            return redirect()->back()->with('error', "🛑 Impossible de supprimer : ce type d'ERP est encore utilisé par {$batimentsCount} bâtiment(s) (contrainte stricte). Modifiez-les d'abord.");
        }

        try {
            DB::beginTransaction();

            // 2. AUTOMATISATION : Pour les lieux publics, on passe leur id_type_erp à NULL
            DB::table('lieux_publics')->where('id_type_erp', $id)->update(['id_type_erp' => null]);

            // 3. On détache les contrôles de la table pivot
            $type_erp->controles()->detach();

            // 4. On supprime définitivement l'ERP
            $type_erp->delete();

            DB::commit();

            return redirect()->route('types-erp.index')->with('success', '✅ Type ERP supprimé avec succès. Les lieux publics associés ont été mis à jour.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', "🛑 Une erreur est survenue : " . $e->getMessage());
        }
    }
}
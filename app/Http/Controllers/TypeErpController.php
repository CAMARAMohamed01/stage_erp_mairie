<?php

namespace App\Http\Controllers;

use App\Models\TypeErp;
use App\Models\ControleReglementaire;
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

        $types_erp = $query->orderBy('categorie_erp', 'asc')->orderBy('type_erp', 'asc')->get();

        return view('types_erp.index', compact('types_erp'));
    }

    // --- FORMULAIRE DE CRÉATION ---
    public function create()
    {
        // On récupère la liste des contrôles pour les proposer à la création
        $controles = ControleReglementaire::orderBy('designation')->get();
        return view('types_erp.create', compact('controles'));
    }

    // --- SAUVEGARDE EN BASE ---
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reglementation_applicable' => 'required|string|max:80',
            'public_cible' => 'nullable|string|max:80',
            'categorie_erp' => 'nullable|integer|min:1|max:5',
            'type_erp' => 'nullable|string|max:2',
            'controles' => 'nullable|array',
            'controles.*' => 'exists:controle_reglementaire,id_controle',
            'date_controle' => 'nullable|array',
            'date_controle.*' => 'nullable|date'
        ]);

        DB::transaction(function () use ($validated, $request) {
            // 1. Création de l'ERP
            $type_erp = TypeErp::create($validated);

            // 2. Traitement et attachement des contrôles avec leurs dates pivots respectives
            if ($request->has('controles')) {
                $pivotData = [];
                foreach ($request->controles as $idControle) {
                    $pivotData[$idControle] = [
                        'date_controle' => $request->input("date_controle.{$idControle}") ?: null
                    ];
                }
                $type_erp->controles()->attach($pivotData);
            }
        });

        return redirect()->route('types-erp.index')->with('success', 'La nouvelle catégorie ERP a été ajoutée avec ses obligations.');
    }

    // --- FICHE DÉTAILLÉE ---
    public function show($id)
    {
        $type_erp = TypeErp::with([
            'controles' => function ($q) {
                $q->withPivot('date_controle');
            }
        ])->findOrFail($id);

        $batiments = DB::table('batiment')->where('id_type_erp', $id)->orderBy('nom_bat')->get();
        $lieux = DB::table('lieux_publics')->where('id_type_erp', $id)->orderBy('nom_lieu')->get();

        return view('types_erp.show', compact('type_erp', 'batiments', 'lieux'));
    }

    // --- FORMULAIRE DE MODIFICATION ---
    public function edit($id)
    {
        $type_erp = TypeErp::with([
            'controles' => function ($q) {
                $q->withPivot('date_controle');
            }
        ])->findOrFail($id);

        $controles = ControleReglementaire::orderBy('designation')->get();

        // Crée un tableau associatif [id_controle => date_controle] pour pré-remplir les inputs de la vue
        $controles_lies = $type_erp->controles->pluck('pivot.date_controle', 'id_controle')->toArray();

        return view('types_erp.edit', compact('type_erp', 'controles', 'controles_lies'));
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
            'controles' => 'nullable|array',
            'controles.*' => 'exists:controle_reglementaire,id_controle',
            'date_controle' => 'nullable|array',
            'date_controle.*' => 'nullable|date'
        ]);

        DB::transaction(function () use ($type_erp, $validated, $request) {
            // 1. Mise à jour des caractéristiques
            $type_erp->update($validated);

            // 2. Traitement et synchronisation de la table pivot
            $pivotData = [];
            foreach ($request->input('controles', []) as $idControle) {
                $pivotData[$idControle] = [
                    'date_controle' => $request->input("date_controle.{$idControle}") ?: null
                ];
            }
            $type_erp->controles()->sync($pivotData);
        });

        return redirect()->route('types-erp.show', $id)->with('success', 'Les informations du type ERP et les contrôles ont été mis à jour.');
    }

    // --- SUPPRESSION ---
    public function destroy($id)
    {
        $type_erp = TypeErp::findOrFail($id);
        $batimentsCount = DB::table('batiment')->where('id_type_erp', $id)->count();

        if ($batimentsCount > 0) {
            return redirect()->back()->with('error', "🛑 Impossible de supprimer : ce type d'ERP est encore utilisé par {$batimentsCount} bâtiment(s). Modifiez-les d'abord.");
        }

        try {
            DB::beginTransaction();

            DB::table('lieux_publics')->where('id_type_erp', $id)->update(['id_type_erp' => null]);
            $type_erp->controles()->detach();
            $type_erp->delete();

            DB::commit();
            return redirect()->route('types-erp.index')->with('success', '✅ Type ERP supprimé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', "🛑 Une erreur est survenue : " . $e->getMessage());
        }
    }
}
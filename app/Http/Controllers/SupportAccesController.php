<?php

namespace App\Http\Controllers;

use App\Models\SupportAcces;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupportAccesController extends Controller
{
    // --- LISTE AVEC RECHERCHE ET FILTRES ---
    public function index(Request $request)
    {
        // On charge la relation utilisateurs pour afficher directement qui a la clé dans le tableau
        $query = SupportAcces::with('utilisateurs');

        // 1. Recherche textuelle (N° de série ou observations)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('numero_serie', 'ilike', '%' . $search . '%')
                    ->orWhere('observations', 'ilike', '%' . $search . '%');
            });
        }

        // 2. Filtre par type de support (Clé, Badge...)
        if ($request->filled('type_support')) {
            $query->where('type_support', $request->type_support);
        }

        // 3. Filtre par statut d'activité
        if ($request->filled('statut')) {
            $statut = $request->statut === 'actif' ? true : false;
            $query->where('est_active', $statut); // Note : adaptez selon le nom exact de votre colonne ('est_actif' ou 'est_active')
        }

        $supports = $query->orderBy('numero_serie')->get();

        return view('supports_acces.index', compact('supports'));
    }

    // --- FORMULAIRE CRÉATION ---
    public function create()
    {
        return view('supports_acces.create');
    }

    // --- ENREGISTREMENT ---
    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero_serie' => 'required|string|max:50|unique:support_acces,numero_serie',
            'type_support' => 'nullable|string|max:50',
            'observations' => 'nullable|string|max:250',
        ]);

        // Par défaut, un nouveau support est actif
        $validated['est_actif'] = $request->has('est_actif') ? true : false;

        SupportAcces::create($validated);

        return redirect()->route('supports-acces.index')->with('success', 'Le nouveau support d\'accès a été enregistré.');
    }

    // --- FICHE DÉTAILLÉE ---
    public function show($id)
    {
        // On récupère le support avec tout son historique d'affectation
        $support = SupportAcces::with('utilisateurs')->findOrFail($id);

        // On isole l'affectation en cours (où date_restitution IS NULL)
        $affectationActuelle = $support->utilisateurs()->wherePivotNull('date_restitution')->first();

        // On récupère aussi les accès de ce support (les bâtiments/locaux qu'il peut ouvrir)
        $batimentsAutorises = DB::table('ouverture_batiment')
            ->join('batiment', 'ouverture_batiment.id_batiment', '=', 'batiment.id_batiment')
            ->where('id_support', $id)
            ->get();

        return view('supports_acces.show', compact('support', 'affectationActuelle', 'batimentsAutorises'));
    }

    // --- FORMULAIRE MODIFICATION ---
    public function edit($id)
    {
        $support = SupportAcces::findOrFail($id);
        return view('supports_acces.edit', compact('support'));
    }

    // --- MISE À JOUR ---
    public function update(Request $request, $id)
    {
        $support = SupportAcces::findOrFail($id);

        $validated = $request->validate([
            'numero_serie' => 'required|string|max:50|unique:support_acces,numero_serie,' . $id . ',id_support',
            'type_support' => 'nullable|string|max:50',
            'observations' => 'nullable|string|max:250',
        ]);

        $validated['est_actif'] = $request->has('est_actif');

        $support->update($validated);

        return redirect()->route('supports-acces.show', $id)->with('success', 'Support d\'accès mis à jour.');
    }

    // --- SUPPRESSION ---
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            // Nettoyage des liaisons d'ouverture et d'affectation avant suppression
            DB::table('affectation')->where('id_support', $id)->delete();
            DB::table('ouverture_local')->where('id_support', $id)->delete();
            DB::table('ouverture_batiment')->where('id_support', $id)->delete();
            DB::table('ouverture_equipement')->where('id_support', $id)->delete();
            DB::table('ouverture_lieu')->where('id_support', $id)->delete();

            DB::table('support_acces')->where('id_support', $id)->delete();

            DB::commit();
            return redirect()->route('supports-acces.index')->with('success', 'Support supprimé définitivement.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }
    }
}
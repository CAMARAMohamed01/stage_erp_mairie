<?php

namespace App\Http\Controllers;

use App\Models\DossierUrba;
use App\Models\Parcelle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Document;
class DossierUrbaController extends Controller
{
    // --- LISTE DES DOSSIERS AVEC FILTRES ---
    public function index(Request $request)
    {
        // On charge le demandeur ET son héritage (physique/morale) en cascade
        $query = DossierUrba::with(['demandeur.physique', 'demandeur.morale', 'instructeur']);

        // 1. Recherche par numéro de dossier ou objet des travaux
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('numero_dossier', 'ilike', '%' . $search . '%')
                    ->orWhere('objet_travaux', 'ilike', '%' . $search . '%');
            });
        }

        // 2. Filtre par type d'acte (PC, DP, CU)
        if ($request->filled('type_dossier')) {
            $query->where('type_dossier_CU_DP_', $request->type_dossier);
        }

        // 3. Filtre par statut de la décision
        if ($request->filled('statut')) {
            $query->where('nature_decision', $request->statut);
        }

        $dossiers = $query->orderBy('date_depot', 'desc')->get();

        return view('dossiers_urba.index', compact('dossiers'));
    }

    // --- FORMULAIRE DE CRÉATION ---
    public function create()
    {
        // Chargement des référentiels indispensables pour les menus déroulants
        $agents = DB::table('utilisateur')->orderBy('nom_user')->get();
        $decisions = DB::table('decision_administratif')->orderByDesc('date_decision')->get();

        $parcelles = Parcelle::orderBy('section_cadastrale')->orderBy('num_parcelle')->get();

        $tiers = DB::table('tiers')
            ->leftJoin('tiers_physique', 'tiers.id_tiers', '=', 'tiers_physique.id_tiers')
            ->leftJoin('tiers_morale', 'tiers.id_tiers', '=', 'tiers_morale.id_tiers')
            ->select('tiers.id_tiers', 'tiers.type_tiers', 'tiers_physique.nom_tiers', 'tiers_physique.prenom_tiers', 'tiers_morale.raison_sociale')
            ->orderBy('nom_tiers')->orderBy('raison_sociale')->get();

        return view('dossiers_urba.create', compact('agents', 'decisions', 'parcelles', 'tiers'));
    }

    // --- SAUVEGARDE ---
    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero_dossier' => 'required|string|max:50|unique:dossier_urba,numero_dossier',
            'type_dossier_CU_DP_' => 'required|string|max:10',
            'date_depot' => 'required|date',
            'date_decision' => 'nullable|date',
            'nature_decision' => 'required|string|max:50',
            'objet_travaux' => 'nullable|string',
            'surface_plancher_m2' => 'nullable|numeric|min:0',
            'hauteur_construction' => 'nullable|numeric|min:0',
            'prix_m2_ia' => 'nullable|numeric|min:0',
            'date_limite_instruction' => 'nullable|date',
            'avis_maire' => 'nullable|string',
            'observations' => 'nullable|string',
            'id_tiers' => 'nullable|integer|exists:tiers,id_tiers',
            'id_acte_decision' => 'nullable|integer|exists:decision_administratif,id_decision',
            'id_user_instructeur' => 'nullable|integer|exists:utilisateur,id_user',

            // Validation du tableau des parcelles sélectionnées
            'parcelles' => 'nullable|array',
            'parcelles.*' => 'exists:parcelle,id_parcelle',
        ]);

        try {
            DB::beginTransaction();

            // 1. Création du dossier d'urbanisme
            $dossier = DossierUrba::create($validated);

            // 2. Liaison des parcelles dans la table pivot dossier_parcelle
            $dossier->parcelles()->attach($request->parcelles);

            DB::commit();
            return redirect()->route('dossiers-urba.index')->with('success', 'Le dossier d\'urbanisme a été enregistré et ouvert.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Erreur lors de la création : ' . $e->getMessage());
        }
    }

    // --- FICHE DÉTAILLÉE ---
    public function show($id)
    {
        $dossier = DossierUrba::with([
            'demandeur.physique',
            'demandeur.morale',
            'instructeur',
            'acteDecision',
            'parcelles',
            'documents'
        ])
            ->findOrFail($id);

        return view('dossiers_urba.show', compact('dossier'));
    }

    // --- FORMULAIRE DE MODIFICATION ---
    public function edit($id)
    {
        $dossier = DossierUrba::with('parcelles')->findOrFail($id);

        $agents = DB::table('utilisateur')->orderBy('nom_user')->get();
        $decisions = DB::table('decision_administratif')->orderByDesc('date_decision')->get();
        $parcelles = Parcelle::orderBy('section_cadastrale')->orderBy('num_parcelle')->get();

        $parcelles_liees = $dossier->parcelles->pluck('id_parcelle')->toArray();

        $tiers = DB::table('tiers')
            ->leftJoin('tiers_physique', 'tiers.id_tiers', '=', 'tiers_physique.id_tiers')
            ->leftJoin('tiers_morale', 'tiers.id_tiers', '=', 'tiers_morale.id_tiers')
            ->select('tiers.id_tiers', 'tiers.type_tiers', 'tiers_physique.nom_tiers', 'tiers_physique.prenom_tiers', 'tiers_morale.raison_sociale')
            ->get();

        return view('dossiers_urba.edit', compact('dossier', 'agents', 'decisions', 'parcelles', 'parcelles_liees', 'tiers'));
    }

    // --- MISE À JOUR ---
    public function update(Request $request, $id)
    {
        $dossier = DossierUrba::findOrFail($id);

        $validated = $request->validate([
            'numero_dossier' => 'required|string|max:50|unique:dossier_urba,numero_dossier,' . $id . ',id_dossier',
            'type_dossier_CU_DP_' => 'required|string|max:10',
            'date_depot' => 'required|date',
            'date_decision' => 'nullable|date',
            'nature_decision' => 'required|string|max:50',
            'objet_travaux' => 'nullable|string',
            'surface_plancher_m2' => 'nullable|numeric|min:0',
            'hauteur_construction' => 'nullable|numeric|min:0',
            'id_tiers' => 'nullable|integer|exists:tiers,id_tiers',
            'id_acte_decision' => 'nullable|integer|exists:decision_administratif,id_decision',
            'id_user_instructeur' => 'nullable|integer|exists:utilisateur,id_user',
            'parcelles' => 'nullable|array',
            'parcelles.*' => 'exists:parcelle,id_parcelle',
        ]);

        try {
            DB::beginTransaction();

            $dossier->update($validated);

            // Synchronisation de la table pivot dossier_parcelle
            $dossier->parcelles()->sync($request->parcelles);

            DB::commit();
            return redirect()->route('dossiers-urba.show', $id)->with('success', 'Dossier d\'urbanisme mis à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
        }
    }

    // --- SUPPRESSION ---
    public function destroy($id)
    {
        $dossier = DossierUrba::findOrFail($id);

        try {
            DB::beginTransaction();

            // 1. On détache d'abord les parcelles de la table pivot
            $dossier->parcelles()->detach();

            // 2. On passe la clé étrangère des documents liés à NULL
            DB::table('document')->where('id_dossier', $id)->update(['id_dossier' => null]);

            // 3. Suppression finale
            $dossier->delete();

            DB::commit();
            return redirect()->route('dossiers-urba.index')->with('success', '✅ Le dossier d\'urbanisme a été supprimé.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }
    }

    // --- IMPORTATION DE PLANS / DOCUMENTS NUMÉRISÉS ---
    public function uploadDocument(Request $request, $id)
    {
        $request->validate([
            'fichier' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // Max 10 Mo par plan
        ]);

        $file = $request->file('fichier');
        $path = $file->store('documents/urbanisme', 'public');

        Document::create([
            'nom_fichier' => $file->getClientOriginalName(),
            'type_doc' => $file->getClientOriginalExtension(),
            'chemin_stockage' => $path,
            'taille_ko' => $file->getSize() / 1024,
            'date_upload' => now(),
            'id_dossier' => $id,
        ]);

        return back()->with('success', 'Le plan/document a été téléversé et rattaché au dossier d\'urbanisme.');
    }
    // --- SUPPRIMER UN DOCUMENT NUMÉRISÉ ---
    public function destroyDocument($id)
    {
        $document = Document::findOrFail($id);

        // 1. Suppression du fichier physique sur le disque 'public' (storage/app/public/...)
        if ($document->chemin_stockage && Storage::disk('public')->exists($document->chemin_stockage)) {
            Storage::disk('public')->delete($document->chemin_stockage);
        }

        // 2. Suppression de la ligne en base de données
        $document->delete();

        return redirect()->back()->with('success', '🗑️ Le plan/document a bien été supprimé définitivement.');
    }
}
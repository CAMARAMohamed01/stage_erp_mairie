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
        // On récupère le support avec tout son historique d'affectation (trié du plus récent au plus ancien)
        $support = SupportAcces::with([
            'utilisateurs' => function ($q) {
                $q->orderByPivot('date_remise', 'desc');
            }
        ])->findOrFail($id);

        // On isole l'affectation en cours
        $affectationActuelle = $support->utilisateurs()->wherePivotNull('date_restitution')->first();

        // Récupération de TOUTES les ouvertures associées via les tables pivots
        $batimentsAutorises = DB::table('ouverture_batiment')
            ->join('batiment', 'ouverture_batiment.id_batiment', '=', 'batiment.id_batiment')
            ->where('id_support', $id)->get();

        $locauxAutorises = DB::table('ouverture_local')
            ->join('local_', 'ouverture_local.id_local', '=', 'local_.id_local')
            ->where('id_support', $id)->get();

        $equipementsAutorises = DB::table('ouverture_equipement')
            ->join('equipement', 'ouverture_equipement.id_equipement', '=', 'equipement.id_equipement')
            ->where('id_support', $id)->get();

        // Référentiels pour peupler les listes déroulantes d'ajout (uniquement si l'utilisateur a les droits d'écriture)
        $tousLesAgents = DB::table('utilisateur')->orderBy('nom_user')->get();
        $tousLesBatiments = DB::table('batiment')->orderBy('nom_bat')->get();
        $tousLesLocaux = DB::table('local_')->orderBy('nom_local')->get();
        $tousLesEquipements = DB::table('equipement')->orderBy('nom_equipement')->get();

        return view('supports_acces.show', compact(
            'support',
            'affectationActuelle',
            'batimentsAutorises',
            'locauxAutorises',
            'equipementsAutorises',
            'tousLesAgents',
            'tousLesBatiments',
            'tousLesLocaux',
            'tousLesEquipements'
        ));
    }

    // --- EFFECTUER UNE NOUVELLE AFFECTATION ---
    public function affecter(Request $request, $id)
    {
        $request->validate([
            'id_user' => 'required|integer|exists:utilisateur,id_user',
            'date_remise' => 'required|date',
            'commentaire' => 'nullable|string|max:250'
        ]);

        $support = SupportAcces::findOrFail($id);

        // 1. Sécurité : On vérifie que la clé n'est pas déjà détenue par quelqu'un EN CE MOMENT
        if ($support->utilisateurs()->wherePivotNull('date_restitution')->exists()) {
            return redirect()->back()->with('error', '🛑 Ce support est déjà affecté à un agent actuellement.');
        }

        // syncWithoutDetaching au lieu de attach()
        // Si l'agent a déjà eu cette clé dans le passé, Laravel fera un UPDATE. Sinon, un INSERT.
        $support->utilisateurs()->syncWithoutDetaching([
            $request->id_user => [
                'date_remise' => $request->date_remise,
                'date_restitution' => null, // TRÈS IMPORTANT : on repasse à null pour réactiver l'affectation !
                'attestation_signee' => $request->has('attestation_signee'),
                'commentaire' => $request->commentaire
            ]
        ]);

        return redirect()->back()->with('success', '🔑 Le support d\'accès a été confié à l\'agent avec succès.');
    }
    // --- ENREGISTRER LA RESTITUTION (RETOUR AU COFFRE) ---
    public function restituer(Request $request, $id, $userId)
    {
        $support = SupportAcces::findOrFail($id);

        // On met à jour la ligne d'affectation active en y mettant la date de restitution à aujourd'hui
        $support->utilisateurs()
            ->wherePivot('id_user', $userId)
            ->wherePivotNull('date_restitution')
            ->updateExistingPivot($userId, ['date_restitution' => now()]);

        return redirect()->back()->with('success', '🔒 Le support a été restitué et est de retour au coffre de la mairie.');
    }

    // --- AJOUTER UN DROIT D'OUVERTURE (PIVOTS) ---
    public function ajouterOuverture(Request $request, $id)
    {
        $request->validate([
            'type_cible' => 'required|in:batiment,local,equipement',
            'target_id' => 'required|integer'
        ]);

        $type = $request->type_cible;
        $targetId = $request->target_id;

        // Insertion dans la bonne table pivot en mode "Insert or Ignore" pour éviter les doublons
        if ($type === 'batiment') {
            DB::table('ouverture_batiment')->insertOrIgnore(['id_support' => $id, 'id_batiment' => $targetId]);
        } elseif ($type === 'local') {
            DB::table('ouverture_local')->insertOrIgnore(['id_support' => $id, 'id_local' => $targetId]);
        } elseif ($type === 'equipement') {
            DB::table('ouverture_equipement')->insertOrIgnore(['id_support' => $id, 'id_equipement' => $targetId]);
        }

        return redirect()->back()->with('success', '🚪 Droit d\'ouverture ajouté.');
    }

    // --- SUPPRIMER UN DROIT D'OUVERTURE (PIVOTS) ---
    public function supprimerOuverture($id, $type, $targetId)
    {
        if ($type === 'batiment') {
            DB::table('ouverture_batiment')->where('id_support', $id)->where('id_batiment', $targetId)->delete();
        } elseif ($type === 'local') {
            DB::table('ouverture_local')->where('id_support', $id)->where('id_local', $targetId)->delete();
        } elseif ($type === 'equipement') {
            DB::table('ouverture_equipement')->where('id_support', $id)->where('id_equipement', $targetId)->delete();
        }

        return redirect()->back()->with('success', '🗑️ Droit d\'ouverture retiré avec succès.');
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
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Action;
use App\Models\Intervention;
use App\Models\Utilisateur;
use App\Models\Categorie;
use App\Models\TiersPhysique;
use App\Models\Local;
use App\Models\Adresse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Tiers;

class ActionController extends Controller
{
    public function index(Request $request)
    {
        $query = Action::query()->with(['categorie', 'adresse', 'local', 'tiers', 'createur']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'ilike', '%' . $search . '%')
                    ->orWhere('emetteur_nom', 'ilike', '%' . $search . '%');
            });
        }

        if ($request->filled('statut')) {
            $query->where('statut_action', $request->statut);
        }

        if ($request->filled('categorie_id')) {
            $query->where('id_cat', $request->categorie_id);
        }

        $actions = $query->orderBy('date_creation', 'desc')->get();
        $categories = Categorie::orderBy('libelle')->get();

        return view('actions.index', compact('actions', 'categories'));
    }

    public function show($id)
    {
        $action = Action::with(['categorie', 'adresse', 'local', 'tiers', 'createur'])->findOrFail($id);
        return view('actions.show', compact('action'));
    }

    public function create()
    {
        $categories = Categorie::orderBy('libelle')->get();
        $tiers = TiersPhysique::orderBy('nom_tiers')->get();

        // Récupération des adresses BAN et des locaux pour la localisation
        $adresses = DB::table('Adresse')->orderBy('nom_voie')->orderBy('num_rue')->get();
        $locaux = DB::table('local_')->orderBy('nom_local')->get();

        return view('actions.create', compact('categories', 'tiers', 'adresses', 'locaux'));
    }

    public function store(Request $request)
    {
        try {
            // Validation stricte des données issues du formulaire
            $validated = $request->validate([
                'description' => 'required|string|max:500',
                'id_cat' => 'required|exists:categorie,id_cat',
                'mode_reception' => 'required|string|max:100',
                'priorite' => 'required|string|max:50',
                'id_tiers' => 'nullable|exists:tiers,id_tiers',
                'emetteur_nom' => 'required_without:id_tiers',
                'emetteur_prenom' => 'required_if:creer_nouveau_tiers,1',
                'emetteur_contact' => 'nullable|string|max:50',
                'creer_nouveau_tiers' => 'nullable',
                'id_adresse' => 'nullable|exists:Adresse,id_adresse',
                'id_local' => 'nullable|exists:local_,id_local'
            ]);

            $id_tiers_final = $request->id_tiers;
            $emetteur_contact_final = $request->emetteur_contact;
            $emetteur_nom_final = '';

            // CAS A : L'agent coche "Créer un nouveau citoyen"
            if ($request->has('creer_nouveau_tiers') && $request->creer_nouveau_tiers == '1' && empty($id_tiers_final)) {
                $isEmail = str_contains($request->emetteur_contact, '@');
                $email = $isEmail ? $request->emetteur_contact : null;

                $tel = null;
                if (!$isEmail && !empty($request->emetteur_contact)) {
                    $telNettoye = preg_replace('/[^0-9+]/', '', $request->emetteur_contact);
                    // On ne garde que les 12 premiers caractères au cas où (ex: +33612345678)
                    $tel = substr($telNettoye, 0, 12);
                }

                $nouveauTiers = Tiers::create([
                    'type_tiers' => 'Physique',
                    'email_tiers' => $email,
                    'tel_tiers' => $tel
                ]);

                TiersPhysique::create([
                    'id_tiers' => $nouveauTiers->id_tiers,
                    'nom_tiers' => $request->emetteur_nom,
                    'prenom_tiers' => $request->emetteur_prenom
                ]);

                $id_tiers_final = $nouveauTiers->id_tiers;
                $emetteur_nom_final = trim($request->emetteur_prenom . ' ' . $request->emetteur_nom);
            } else if (!empty($id_tiers_final)) {
                $tiersPhysique = TiersPhysique::where('id_tiers', $id_tiers_final)->first();
                $emetteur_nom_final = $tiersPhysique ? trim($tiersPhysique->prenom_tiers . ' ' . $tiersPhysique->nom_tiers) : 'Citoyen inconnu';
            } else {
                $emetteur_nom_final = trim($request->emetteur_prenom . ' ' . $request->emetteur_nom);
            }

            // Sauvegarde directe via le Query Builder
            DB::table('action')->insert([
                'date_creation' => now(),
                'description' => $request->description,
                'id_cat' => $request->id_cat,
                'mode_reception' => $request->mode_reception,
                'priorite' => $request->priorite,
                'statut_action' => 'Nouveau',
                'id_user' => Auth::id() ?? 1,
                'id_tiers' => $id_tiers_final ?: null,
                'emetteur_nom' => $emetteur_nom_final ?: 'Anonyme',
                'emetteur_contact' => $emetteur_contact_final ?: null,
                'id_adresse' => $request->id_adresse ? (int) $request->id_adresse : null,
                'id_local' => $request->id_local ? (int) $request->id_local : null,
                'id_user_assigne' => null
            ]);

            return redirect()->route('actions.index')->with('success', 'Action enregistrée avec succès.');

        } catch (\Exception $e) {
            dd("Erreur SQL lors de l'insertion : " . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $action = Action::findOrFail($id);
        $categories = Categorie::orderBy('libelle')->get();
        $tiers = TiersPhysique::orderBy('nom_tiers')->get();

        // Récupération pour la vue de modification
        $adresses = DB::table('Adresse')->orderBy('nom_voie')->orderBy('num_rue')->get();
        $locaux = DB::table('local_')->orderBy('nom_local')->get();

        return view('actions.edit', compact('action', 'categories', 'tiers', 'adresses', 'locaux'));
    }

    public function update(Request $request, $id)
    {
        $action = Action::findOrFail($id);

        $validated = $request->validate([
            'description' => 'required|string|max:500',
            'id_cat' => 'required|exists:categorie,id_cat',
            'mode_reception' => 'required|string|max:100',
            'priorite' => 'required|string|max:50',
            'id_tiers' => 'nullable|exists:tiers,id_tiers',
            'emetteur_nom' => 'required_without:id_tiers',
            'emetteur_contact' => 'nullable|string|max:50',

            // Validation des modifications géographiques
            'id_adresse' => 'nullable|exists:Adresse,id_adresse',
            'id_local' => 'nullable|exists:local_,id_local'
        ]);

        $id_tiers_final = $request->id_tiers;
        $emetteur_nom_final = '';

        if (!empty($id_tiers_final)) {
            $tiersPhysique = TiersPhysique::where('id_tiers', $id_tiers_final)->first();
            $emetteur_nom_final = $tiersPhysique ? trim($tiersPhysique->prenom_tiers . ' ' . $tiersPhysique->nom_tiers) : 'Citoyen inconnu';
        } else {
            $emetteur_nom_final = $request->emetteur_nom;
        }

        $action->update([
            'description' => $request->description,
            'id_cat' => $request->id_cat,
            'mode_reception' => $request->mode_reception,
            'priorite' => $request->priorite,
            'id_tiers' => $id_tiers_final,
            'emetteur_nom' => $emetteur_nom_final,
            'emetteur_contact' => $request->emetteur_contact,
            'id_adresse' => $request->id_adresse ?: null, //  Mise à jour de l'adresse
            'id_local' => $request->id_local ?: null       //  Mise à jour du local
        ]);

        return redirect()->route('actions.show', $action->id_action)->with('success', 'Action mise à jour avec succès.');
    }

    public function destroy($id)
    {
        $action = Action::findOrFail($id);
        Intervention::where('id_action', $id)->update(['id_action' => null]);
        $action->delete();

        return redirect()->route('actions.index')->with('success', 'Action supprimée définitivement.');
    }


    public function prendreEnCharge($id)
    {
        // Récupérer l'action
        $action = Action::findOrFail($id);

        //  Mettre à jour le statut dans la table action
        $action->update([
            'statut_action' => 'En cours'
        ]);

        // 3. Rediriger avec un message de succès
        return redirect()->back()->with('success', 'Le signalement est désormais en cours de traitement.');
    }


    public function creerIntervention($id)
    {
        $action = Action::findOrFail($id);

        // Créer l'intervention technique via le Query Builder
        // On respecte scrupuleusement les contraintes NOT NULL de DDL (description, id_cat, etc.)
        $idIntervention = DB::table('intervention')->insertGetId([
            'date_ouverture' => now(),
            'type_intervention' => 'Réparation : ' . substr($action->description, 0, 130),
            'statut_global' => 'En cours',
            'description' => 'Intervention générée suite au signalement citoyen #' . $action->id_action,
            'id_cat' => $action->id_cat,
            'id_action' => $action->id_action,
            'id_local' => $action->id_local ?: null, // On hérite automatiquement du local s'il est saisi
            'code_budget' => null,
            'date_cloture' => null,
            'id_compteur' => null,
            'id_troncon' => null,
            'id_tiers' => $action->id_tiers ?: null, // On hérite du citoyen émetteur
            'id_contrat' => null,
            'id_user_demandeur' => Auth::id() ?? 1,
            'id_service' => null,
            'Autre' => null
        ], 'id_int');

        // Faire évoluer le statut du signalement d'origine en "Transmis"
        $action->update([
            'statut_action' => 'Transmis'
        ]);

        // Redirection vers la fiche de l'intervention fraîchement créée pour que l'agent puisse la planifier
        return redirect()->route('interventions.show', $idIntervention)
            ->with('success', 'Intervention technique générée avec succès depuis le signalement !');
    }

    public function imprimer($id)
    {
        // On charge l'action avec sa catégorie, son adresse BAN, son local et son créateur
        $action = Action::with(['categorie', 'adresse', 'local', 'createur'])->findOrFail($id);

        return view('actions.print', compact('action'));
    }
}
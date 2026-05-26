<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\action;
use App\Models\Intervention;
use App\Models\Utilisateur;
use App\Models\Categorie;
// tiers physique
use App\Models\TiersPhysique;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
class actionController extends Controller
{
    public function index()
    {
        // On récupère tous les actions avec leur catégorie
        $actions = action::with('categorie')->orderBy('date_creation', 'desc')->get();

        return view('actions.index', compact('actions'));
    }
    // La méthode pour afficher UN dossier complet
    public function show($id)
    {
        // On cherche le action avec sa catégorie et l'utilisateur associé (s'il y en a un)
        // failOrFail permet d'afficher une belle page 404 si l'ID n'existe pas dans la base
        $action = action::with(['categorie'])->findOrFail($id);

        return view('actions.show', compact('action'));
    }
    public function prendreEnCharge($id)
    {
        // 1. Récupérer le action
        $action = action::findOrFail($id);

        // 2. Mettre à jour le statut
        $action->update([
            'statut_action' => 'En cours'
        ]);

        // 3. Rediriger avec un message de succès
        return redirect()->back()->with('success', 'Le action est désormais en cours de traitement.');
    }

    public function creerIntervention($id)
    {
        $action = action::findOrFail($id);

        // 1. Créer l'intervention technique
        // On récupère les infos du action pour pré-remplir l'intervention
        $intervention = Intervention::create([
            'date_ouverture' => now(),
            'type_intervention' => 'Réparation : ' . $action->description,
            'statut_global' => 'En cours',
            'id_cat' => $action->id_cat,
            'id_action' => $action->id_action, // C'est ici que le lien se fait dans votre BDD !
            'description' => 'Suite au action #' . $action->id_action,
        ]);

        // 2. On lie le action à l'intervention (si vous avez une colonne id_int dans votre table action)
        // Et on change le statut en "Transmis"
        $action->update([
            'statut_action' => 'Transmis'
        ]);

        return redirect()->route('technique.dashboard')->with('success', 'Intervention générée avec succès !');
    }

    public function exportExcel()
    {
        $actions = action::with('categorie')->get();
        $fileName = 'registre_actions_' . date('d_m_Y') . '.csv';

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($actions) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8

            // En-têtes adaptés à la table action
            fputcsv($file, ['ID', 'Date', 'Émetteur', 'Catégorie', 'Priorité', 'Statut', 'Description']);

            foreach ($actions as $sig) {
                fputcsv($file, [
                    $sig->id_action,
                    $sig->date_creation,
                    $sig->emetteur_nom,
                    $sig->categorie->libelle ?? 'N/A',
                    $sig->priorite,
                    $sig->statut_action,
                    $sig->description
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function imprimer($id)
    {
        $action = action::with('categorie')->findOrFail($id);
        return view('actions.print', compact('action'));
    }
    public function create()
    {
        $categories = Categorie::all();
        // On récupère les tiers physiques existants pour la liaison
        $tiers = TiersPhysique::all();
        return view('actions.create', compact('categories', 'tiers'));
    }

    public function store(Request $request)
    {
        try {
            // 1. Validation des données
            $validated = $request->validate([
                'description' => 'required',
                'id_cat' => 'required',
                'mode_reception' => 'required',
                'priorite' => 'required',
                'id_tiers' => 'nullable|exists:tiers,id_tiers',
                'emetteur_nom' => 'required_without:id_tiers',
                'emetteur_prenom' => 'required_if:creer_nouveau_tiers,1',
                'emetteur_contact' => 'nullable|string|max:50',
                'creer_nouveau_tiers' => 'nullable'
            ]);

            $id_tiers_final = $request->id_tiers;
            $emetteur_contact_final = $request->emetteur_contact;
            $emetteur_nom_final = '';

            // CAS A : L'agent a coché "Créer un nouveau citoyen"
            if ($request->has('creer_nouveau_tiers') && $request->creer_nouveau_tiers == '1' && empty($id_tiers_final)) {

                $isEmail = str_contains($request->emetteur_contact, '@');
                $email = $isEmail ? $request->emetteur_contact : null;
                $tel = !$isEmail ? substr($request->emetteur_contact, 0, 12) : null;

                $nouveauTiers = \App\Models\Tiers::create([
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

                // On garde le nom pour le action
                $emetteur_nom_final = trim($request->emetteur_prenom . ' ' . $request->emetteur_nom);

            }
            // CAS B : Un citoyen existant a été sélectionné dans la liste
            else if (!empty($id_tiers_final)) {

                // On va chercher son vrai nom dans la base de données
                $tiersPhysique = TiersPhysique::where('id_tiers', $id_tiers_final)->first();

                if ($tiersPhysique) {
                    $emetteur_nom_final = trim($tiersPhysique->prenom_tiers . ' ' . $tiersPhysique->nom_tiers);
                } else {
                    $emetteur_nom_final = 'Citoyen inconnu';
                }
            }
            // CAS C : Personne de passage (ni existante, ni à créer)
            else {
                $emetteur_nom_final = trim($request->emetteur_prenom . ' ' . $request->emetteur_nom);
            }

            // 3. Création du action avec le bon nom !
            $action = action::create([
                'date_creation' => now(),
                'description' => $request->description,
                'id_cat' => $request->id_cat,
                'mode_reception' => $request->mode_reception,
                'priorite' => $request->priorite,
                'statut_action' => 'Nouveau',
                'id_user' => Auth::id(),
                'id_tiers' => $id_tiers_final,
                'emetteur_nom' => $emetteur_nom_final,
                'emetteur_contact' => $emetteur_contact_final
            ]);

            return redirect()->route('actions.index')->with('success', 'action enregistré avec succès.');

        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    // AFFICHER LE FORMULAIRE DE MODIFICATION
    public function edit($id)
    {
        $action = action::findOrFail($id);

        // On charge les listes pour les menus déroulants
        $categories = \App\Models\Categorie::orderBy('libelle')->get();
        // Assure-toi que la requête correspond à ce que tu as dans create()
        $tiers = \App\Models\TiersPhysique::orderBy('nom_tiers')->get();

        return view('actions.edit', compact('action', 'categories', 'tiers'));
    }

    // TRAITER LA MODIFICATION
    public function update(Request $request, $id)
    {
        $action = action::findOrFail($id);

        $validated = $request->validate([
            'description' => 'required',
            'id_cat' => 'required',
            'mode_reception' => 'required',
            'priorite' => 'required',
            'id_tiers' => 'nullable|exists:tiers,id_tiers',
            'emetteur_nom' => 'required_without:id_tiers',
            'emetteur_contact' => 'nullable|string|max:50',
        ]);

        $id_tiers_final = $request->id_tiers;
        $emetteur_nom_final = '';

        // Si on a sélectionné un tiers existant, on va chercher son nom
        if (!empty($id_tiers_final)) {
            $tiersPhysique = TiersPhysique::where('id_tiers', $id_tiers_final)->first();
            if ($tiersPhysique) {
                $emetteur_nom_final = trim($tiersPhysique->prenom_tiers . ' ' . $tiersPhysique->nom_tiers);
            } else {
                $emetteur_nom_final = 'Citoyen inconnu';
            }
        } else {
            // Sinon on prend ce qui a été tapé dans la case "Nom"
            $emetteur_nom_final = $request->emetteur_nom;
        }

        // Mise à jour
        $action->update([
            'description' => $request->description,
            'id_cat' => $request->id_cat,
            'mode_reception' => $request->mode_reception,
            'priorite' => $request->priorite,
            'id_tiers' => $id_tiers_final,
            'emetteur_nom' => $emetteur_nom_final,
            'emetteur_contact' => $request->emetteur_contact
        ]);

        return redirect()->route('actions.show', $action->id_action)
            ->with('success', 'action mis à jour avec succès.');
    }

    // SUPPRIMER LE action
    public function destroy($id)
    {
        $action = action::findOrFail($id);

        // IMPORTANT : S'il y a des interventions liées à ce action,
        // on vide le champ 'id_action' pour ne pas bloquer la suppression
        Intervention::where('id_action', $id)->update(['id_action' => null]);

        $action->delete();

        return redirect()->route('actions.index')
            ->with('success', 'action supprimé définitivement.');
    }
}
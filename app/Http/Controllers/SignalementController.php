<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Signalement;
use App\Models\Intervention;
use App\Models\Utilisateur;
use App\Models\Categorie;
// tiers physique
use App\Models\TiersPhysique;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
class SignalementController extends Controller
{
    public function index()
    {
        // On récupère tous les signalements avec leur catégorie
        $signalements = Signalement::with('categorie')->orderBy('date_creation', 'desc')->get();

        return view('signalements.index', compact('signalements'));
    }
    // La méthode pour afficher UN dossier complet
    public function show($id)
    {
        // On cherche le signalement avec sa catégorie et l'utilisateur associé (s'il y en a un)
        // failOrFail permet d'afficher une belle page 404 si l'ID n'existe pas dans la base
        $signalement = Signalement::with(['categorie'])->findOrFail($id);

        return view('signalements.show', compact('signalement'));
    }
    public function prendreEnCharge($id)
    {
        // 1. Récupérer le signalement
        $signalement = Signalement::findOrFail($id);

        // 2. Mettre à jour le statut
        $signalement->update([
            'statut_signalement' => 'En cours'
        ]);

        // 3. Rediriger avec un message de succès
        return redirect()->back()->with('success', 'Le signalement est désormais en cours de traitement.');
    }

    public function creerIntervention($id)
    {
        $signalement = Signalement::findOrFail($id);

        // 1. Créer l'intervention technique
        // On récupère les infos du signalement pour pré-remplir l'intervention
        $intervention = Intervention::create([
            'date_ouverture' => now(),
            'type_intervention' => 'Réparation : ' . $signalement->description,
            'statut_global' => 'En cours',
            'id_cat' => $signalement->id_cat,
            'id_sig' => $signalement->id_sig, // C'est ici que le lien se fait dans votre BDD !
            'description' => 'Suite au signalement #' . $signalement->id_sig,
        ]);

        // 2. On lie le signalement à l'intervention (si vous avez une colonne id_int dans votre table signalement)
        // Et on change le statut en "Transmis"
        $signalement->update([
            'statut_signalement' => 'Transmis'
        ]);

        return redirect()->route('technique.dashboard')->with('success', 'Intervention générée avec succès !');
    }

    public function exportExcel()
    {
        $signalements = Signalement::with('categorie')->get();
        $fileName = 'registre_signalements_' . date('d_m_Y') . '.csv';

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($signalements) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8

            // En-têtes adaptés à la table signalement
            fputcsv($file, ['ID', 'Date', 'Émetteur', 'Catégorie', 'Priorité', 'Statut', 'Description']);

            foreach ($signalements as $sig) {
                fputcsv($file, [
                    $sig->id_sig,
                    $sig->date_creation,
                    $sig->emetteur_nom,
                    $sig->categorie->libelle ?? 'N/A',
                    $sig->priorite,
                    $sig->statut_signalement,
                    $sig->description
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function imprimer($id)
    {
        $signalement = Signalement::with('categorie')->findOrFail($id);
        return view('signalements.print', compact('signalement'));
    }
    public function create()
    {
        $categories = Categorie::all();
        // On récupère les tiers physiques existants pour la liaison
        $tiers = TiersPhysique::all();
        return view('signalements.create', compact('categories', 'tiers'));
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

                // On garde le nom pour le signalement
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

            // 3. Création du signalement avec le bon nom !
            $signalement = Signalement::create([
                'date_creation' => now(),
                'description' => $request->description,
                'id_cat' => $request->id_cat,
                'mode_reception' => $request->mode_reception,
                'priorite' => $request->priorite,
                'statut_signalement' => 'Nouveau',
                'id_user' => Auth::id(),
                'id_tiers' => $id_tiers_final,
                'emetteur_nom' => $emetteur_nom_final,
                'emetteur_contact' => $emetteur_contact_final
            ]);

            return redirect()->route('signalements.index')->with('success', 'Signalement enregistré avec succès.');

        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    // AFFICHER LE FORMULAIRE DE MODIFICATION
    public function edit($id)
    {
        $signalement = Signalement::findOrFail($id);

        // On charge les listes pour les menus déroulants
        $categories = \App\Models\Categorie::orderBy('libelle')->get();
        // Assure-toi que la requête correspond à ce que tu as dans create()
        $tiers = \App\Models\TiersPhysique::orderBy('nom_tiers')->get();

        return view('signalements.edit', compact('signalement', 'categories', 'tiers'));
    }

    // TRAITER LA MODIFICATION
    public function update(Request $request, $id)
    {
        $signalement = Signalement::findOrFail($id);

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
        $signalement->update([
            'description' => $request->description,
            'id_cat' => $request->id_cat,
            'mode_reception' => $request->mode_reception,
            'priorite' => $request->priorite,
            'id_tiers' => $id_tiers_final,
            'emetteur_nom' => $emetteur_nom_final,
            'emetteur_contact' => $request->emetteur_contact
        ]);

        return redirect()->route('signalements.show', $signalement->id_sig)
            ->with('success', 'Signalement mis à jour avec succès.');
    }

    // SUPPRIMER LE SIGNALEMENT
    public function destroy($id)
    {
        $signalement = Signalement::findOrFail($id);

        // IMPORTANT : S'il y a des interventions liées à ce signalement,
        // on vide le champ 'id_sig' pour ne pas bloquer la suppression
        Intervention::where('id_sig', $id)->update(['id_sig' => null]);

        $signalement->delete();

        return redirect()->route('signalements.index')
            ->with('success', 'Signalement supprimé définitivement.');
    }
}
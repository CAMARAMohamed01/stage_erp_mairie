<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Signalement;
use App\Models\Intervention;
use App\Models\Utilisateur;
use App\Models\Categorie;

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
        $categories = \App\Models\Categorie::all();
        // On récupère les tiers physiques existants pour la liaison
        $tiers = \App\Models\TiersPhysique::all();
        return view('signalements.create', compact('categories', 'tiers'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'description' => 'required',
                'id_cat' => 'required',
                'mode_reception' => 'required',
                'priorite' => 'required',
                // Soit on choisit un tiers existant, soit on saisit un nouveau nom
                'id_tiers' => 'nullable|exists:tiers,id_tiers',
                'emetteur_nom' => 'required_without:id_tiers',
                'emetteur_contact' => 'nullable|string|max:50'
            ]);

            $signalement = Signalement::create([
                'date_creation' => now(),
                'description' => $request->description,
                'id_cat' => $request->id_cat,
                'mode_reception' => $request->mode_reception,
                'priorite' => $request->priorite,
                'statut_signalement' => 'Nouveau',
                'id_user' => 1,
                // Logique de liaison
                'id_tiers' => $request->id_tiers,
                'emetteur_nom' => $request->id_tiers ? 'Citoyen Répertorié' : $request->emetteur_nom,
                'emetteur_contact' => $request->emetteur_contact
            ]);

            return redirect()->route('signalements.index');
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
}
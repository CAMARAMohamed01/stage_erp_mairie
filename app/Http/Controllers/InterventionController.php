<?php

namespace App\Http\Controllers;

use App\Models\Intervention;
use Illuminate\Http\Request;
use App\Models\SuiviAction;
use App\Models\Equipement;
use App\Models\Categorie;
class InterventionController extends Controller
{
    public function index(Request $request)
    {
        // 1. On commence la requête
        $query = Intervention::with('categorie');

        // 2. Si un statut est sélectionné dans le filtre, on ajoute une condition
        if ($request->has('statut') && $request->statut !== 'Tous') {
            $query->where('statut_global', $request->statut);
        }

        // 3. On récupère les résultats triés
        $interventions = $query->orderBy('date_ouverture', 'desc')->get();

        return view('interventions.index', compact('interventions'));
    }

    public function show($id)
    {

        // On récupère l'intervention avec ses relations
        $intervention = Intervention::with(['equipements', 'categorie', 'signalement', 'suiviActions'])->findOrFail($id);
        return view('interventions.show', compact('intervention'));
    }

    public function cloturer($id)
    {
        $intervention = Intervention::findOrFail($id);

        $intervention->update([
            'statut_global' => 'Terminé',
            'date_cloture' => now()
        ]);

        return redirect()->back()->with('success', 'L\'intervention a été clôturée avec succès.');
    }

    public function exportExcel()
    {
        $interventions = Intervention::all();
        $fileName = 'registre_interventions.csv';

        $headers = array(
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $callback = function () use ($interventions) {
            $file = fopen('php://output', 'w');
            // Ajout du BOM pour qu'Excel reconnaisse l'UTF-8 (les accents)
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // En-têtes
            fputcsv($file, ['ID', 'Type', 'Statut', 'Date Ouverture', 'Description']);

            foreach ($interventions as $int) {
                fputcsv($file, [
                    $int->id_int,
                    $int->type_intervention,
                    $int->statut_global,
                    $int->date_ouverture,
                    $int->description
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function imprimer($id)
    {
        $intervention = Intervention::with(['categorie', 'signalement', 'suiviActions'])->findOrFail($id);
        // On renvoie simplement une vue propre, et on déclenchera l'impression en JS
        return view('interventions.print', compact('intervention'));
    }

    public function sauvegarderCloture(Request $request, $id)
    {
        try {
            // 1. Validation rigoureuse
            $request->validate([
                'compte_rendu' => 'required|string|min:10',
                'date_cloture' => 'required|date',
                'temps_passe' => 'nullable|numeric|min:0',
                'statut_final' => 'required|string'
            ]);

            // 2. Création du compte-rendu dans la table suivi_action
            SuiviAction::create([
                'date_action_suivi' => $request->date_cloture,
                'description_etape' => $request->compte_rendu,
                'temps_passe_heures' => $request->temps_passe ?? 0,
                'statut_apres_action' => $request->statut_final,
                'id_int' => $id,
                'id_user' => 1, // À remplacer par auth()->id() plus tard
                'cout_associe' => 0
            ]);

            // 3. Mise à jour de l'intervention parente
            $intervention = Intervention::findOrFail($id);
            $intervention->update([
                'statut_global' => $request->statut_final,
                'date_cloture' => ($request->statut_final == 'Terminé') ? $request->date_cloture : null
            ]);

            return redirect()->route('interventions.show', $id)
                ->with('success', 'Le compte-rendu a été enregistré et le statut mis à jour.');
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
    public function create(Request $request)
    {
        // On récupère l'ID passé dans l'URL par le bouton de la fiche équipement
        $equipement_preselectionne = $request->query('equipement_id');

        $equipements = Equipement::orderBy('nom_equipement')->get();

        $categories = Categorie::orderBy('libelle', 'asc')->get();


        return view('interventions.create', compact('equipements', 'equipement_preselectionne', 'categories'));
    }
    public function store(Request $request)
    {
        // 1. Validation stricte basée sur ton schéma SQL (NOT NULL)
        $validated = $request->validate([
            'date_ouverture' => 'required|date',
            'type_intervention' => 'required|string|max:150',
            'statut_global' => 'required|string|max:50',
            'description' => 'required|string',
            'id_cat' => 'required|exists:categorie,id_cat',
            'equipement_id' => 'nullable|exists:equipement,id_equipement',
            'code_budget' => 'nullable|string|max:2',
        ]);

        // 2. On isole les données de la table intervention
        $interventionData = $validated;
        unset($interventionData['equipement_id']); // On retire ce champ car il n'existe pas dans la table

        // 3. Création de l'intervention
        $intervention = Intervention::create($interventionData);

        // 4. On la relie à l'équipement via la table pivot
        if ($request->filled('equipement_id')) {
            $intervention->equipements()->attach($request->equipement_id);
        }

        // Redirection vers la fiche de l'équipement si on venait de là
        if ($request->filled('equipement_id')) {
            return redirect()->route('equipements.show', $request->equipement_id)
                ->with('success', 'Intervention créée et liée à l\'équipement !');
        }

        return redirect()->route('interventions.index')->with('success', 'Intervention enregistrée.');
    }

    // AFFICHER LE FORMULAIRE DE MODIFICATION
    public function edit($id)
    {
        $intervention = Intervention::with('equipements')->findOrFail($id);
        $equipements = Equipement::orderBy('nom_equipement')->get();
        $categories = Categorie::orderBy('libelle', 'asc')->get();

        // On récupère l'ID de l'équipement lié (s'il y en a un) pour pré-sélectionner la liste
        $equipement_preselectionne = $intervention->equipements->first()->id_equipement ?? null;

        return view('interventions.edit', compact('intervention', 'equipements', 'categories', 'equipement_preselectionne'));
    }

    // TRAITER LA MODIFICATION
    public function update(Request $request, $id)
    {
        $intervention = Intervention::findOrFail($id);

        $validated = $request->validate([
            'date_ouverture' => 'required|date',
            'type_intervention' => 'required|string|max:150',
            'statut_global' => 'required|string|max:50',
            'description' => 'required|string',
            'id_cat' => 'required|exists:categorie,id_cat',
            'equipement_id' => 'nullable|exists:equipement,id_equipement',
            'code_budget' => 'nullable|string|max:2',
        ]);

        $interventionData = $validated;
        unset($interventionData['equipement_id']);

        // Mise à jour des données
        $intervention->update($interventionData);

        // Mise à jour de la liaison avec l'équipement (Table pivot)
        if ($request->filled('equipement_id')) {
            // sync() remplace les anciens liens par le nouveau
            $intervention->equipements()->sync([$request->equipement_id]);
        } else {
            // S'il n'y a pas d'équipement sélectionné, on détache tout
            $intervention->equipements()->detach();
        }

        return redirect()->route('interventions.show', $intervention->id_int)
            ->with('success', 'Intervention mise à jour avec succès.');
    }

    // SUPPRIMER L'INTERVENTION
    public function destroy($id)
    {
        $intervention = Intervention::findOrFail($id);

        // On détache la table pivot avant de supprimer !
        $intervention->equipements()->detach();

        $intervention->delete();

        return redirect()->route('interventions.index')
            ->with('success', 'Intervention supprimée définitivement.');
    }
    // 1. Affiche le formulaire
    public function formulaireCloture($id)
    {
        $intervention = Intervention::findOrFail($id);
        return view('interventions.cloture_form', compact('intervention'));
    }


}
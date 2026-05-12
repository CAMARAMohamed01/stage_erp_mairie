<?php

namespace App\Http\Controllers;

use App\Models\Intervention;
use Illuminate\Http\Request;

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
        $intervention = Intervention::with(['categorie', 'signalement'])->findOrFail($id);
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
        $intervention = Intervention::with(['categorie', 'signalement'])->findOrFail($id);
        // On renvoie simplement une vue propre, et on déclenchera l'impression en JS
        return view('interventions.print', compact('intervention'));
    }
}
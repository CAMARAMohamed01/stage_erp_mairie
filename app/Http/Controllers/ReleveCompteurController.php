<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReleveCompteur;
use App\Models\Compteur;
use Barryvdh\DomPDF\Facade\Pdf;
// Pour la génération de noms de fichiers propres
use Illuminate\Support\Str;
class ReleveCompteurController extends Controller
{
    // On affiche les relevés spécifiques à un compteur
    public function index($idCompteur)
    {
        $compteur = Compteur::findOrFail($idCompteur);
        $releves = ReleveCompteur::where('id_compteur', $idCompteur)->orderByDesc('date_releve')->get();

        return view('releves.index', compact('compteur', 'releves'));
    }

    public function store(Request $request, $idCompteur)
    {
        $validated = $request->validate([
            'date_releve' => 'required|date',
            'valeur_index' => 'required|numeric',
            'commentaire_releve' => 'nullable|string|max:150',
        ]);

        $validated['id_compteur'] = $idCompteur;

        ReleveCompteur::create($validated);

        return redirect()->route('compteurs.releves.index', $idCompteur)
            ->with('success', 'Relevé enregistré avec succès.');
    }

    public function exportPdf($idCompteur)
    {
        $compteur = Compteur::with('local.batiment')->findOrFail($idCompteur);
        $releves = ReleveCompteur::where('id_compteur', $idCompteur)
            ->orderByDesc('date_releve')
            ->get();

        // On charge une vue Blade spécialement conçue pour l'impression
        $pdf = Pdf::loadView('releves.pdf', compact('compteur', 'releves'));

        // Le nom du fichier sera dynamique en fonction du PDL/Point de comptage
        $nomFichier = 'releves_compteur_' . Str::slug($compteur->point_comptage) . '_' . date('Y-m-d') . '.pdf';

        // return $pdf->stream(); // Utilise stream() si tu veux juste l'afficher dans le navigateur
        return $pdf->download($nomFichier); // Utilise download() pour forcer le téléchargement
    }
}
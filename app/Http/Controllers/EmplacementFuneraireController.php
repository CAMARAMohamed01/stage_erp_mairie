<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmplacementFuneraire;
use App\Models\LieuPublic;
use Illuminate\Support\Facades\DB;

class EmplacementFuneraireController extends Controller
{
    public function index()
    {
        $emplacements = EmplacementFuneraire::with('lieu')->orderBy('reference_emplacement')->get();
        return view('emplacements.index', compact('emplacements'));
    }

    public function create()
    {
        // On récupère idéalement que les lieux qui sont des cimetières
        // Adapte le 'where' selon les termes exacts utilisés dans ta base
        $cimetieres = LieuPublic::where('typologie_lieu', 'ILIKE', '%cimetière%')
            ->orderBy('nom_lieu')
            ->get();

        // Si la liste est vide (pas encore de typologie renseignée), on prend tout
        if ($cimetieres->isEmpty()) {
            $cimetieres = LieuPublic::orderBy('nom_lieu')->get();
        }

        return view('emplacements.create', compact('cimetieres'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'reference_emplacement' => 'nullable|string|max:50',
            'type_emplacement' => 'nullable|string|max:50', // Ex: Caveau, Pleine terre, Case columbarium
            'capacite_max' => 'nullable|integer|min:1',
            'statut_occupation' => 'nullable|string|max:50', // Ex: Libre, Occupé, Réservé, Repris
            'id_lieu' => 'nullable|integer|exists:lieux_publics,id_lieu',
        ]);

        EmplacementFuneraire::create($validated);

        return redirect()->route('emplacements.index')
            ->with('success', 'L\'emplacement funéraire a été créé avec succès.');
    }

    public function edit($id)
    {
        $emplacement = EmplacementFuneraire::findOrFail($id);

        $cimetieres = LieuPublic::where('typologie_lieu', 'ILIKE', '%cimetière%')
            ->orderBy('nom_lieu')
            ->get();

        if ($cimetieres->isEmpty()) {
            $cimetieres = LieuPublic::orderBy('nom_lieu')->get();
        }

        return view('emplacements.edit', compact('emplacement', 'cimetieres'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'reference_emplacement' => 'nullable|string|max:50',
            'type_emplacement' => 'nullable|string|max:50',
            'capacite_max' => 'nullable|integer|min:1',
            'statut_occupation' => 'nullable|string|max:50',
            'id_lieu' => 'nullable|integer|exists:lieux_publics,id_lieu',
        ]);

        $emplacement = EmplacementFuneraire::findOrFail($id);
        $emplacement->update($validated);

        return redirect()->route('emplacements.index')
            ->with('success', 'L\'emplacement funéraire a été mis à jour.');
    }

    public function destroy($id)
    {
        $emplacement = EmplacementFuneraire::findOrFail($id);
        $emplacement->delete();

        return redirect()->route('emplacements.index')
            ->with('success', 'L\'emplacement funéraire a été supprimé.');
    }
}
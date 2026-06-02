<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LieuDitController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('lieu_dit');

        // Système de recherche
        if ($request->filled('search')) {
            $query->where('nom_lieu_dit', 'ilike', '%' . $request->search . '%');
        }

        // On compte aussi le nombre d'adresses rattachées à chaque lieu-dit pour l'affichage
        $lieuxDits = $query->orderBy('nom_lieu_dit', 'asc')->get();

        return view('lieux_dits.index', compact('lieuxDits'));
    }

    public function create()
    {
        return view('lieux_dits.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom_lieu_dit' => 'required|string|max:80|unique:lieu_dit,nom_lieu_dit',
        ]);

        DB::table('lieu_dit')->insert([
            'nom_lieu_dit' => trim($request->nom_lieu_dit)
        ]);

        return redirect()->route('lieux-dits.index')->with('success', 'Le lieu-dit a été ajouté avec succès.');
    }

    public function edit($id)
    {
        $lieuDit = DB::table('lieu_dit')->where('id_lieu_dit', $id)->first();

        if (!$lieuDit) {
            return redirect()->route('lieux-dits.index')->with('error', 'Lieu-dit introuvable.');
        }

        return view('lieux_dits.edit', compact('lieuDit'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nom_lieu_dit' => 'required|string|max:80|unique:lieu_dit,nom_lieu_dit,' . $id . ',id_lieu_dit',
        ]);

        DB::table('lieu_dit')->where('id_lieu_dit', $id)->update([
            'nom_lieu_dit' => trim($request->nom_lieu_dit)
        ]);

        return redirect()->route('lieux-dits.index')->with('success', 'Le lieu-dit a été mis à jour.');
    }

    public function destroy($id)
    {
        // Vérifier si des adresses ou parcelles utilisent ce lieu-dit
        $adressesLiees = DB::table('Adresse')->where('id_lieu_dit', $id)->count();
        $parcellesLiees = DB::table('parcelle')->where('id_lieu_dit', $id)->count();

        if ($adressesLiees > 0 || $parcellesLiees > 0) {
            return redirect()->route('lieux-dits.index')->with('error', "Impossible de supprimer ce lieu-dit : il est utilisé par {$adressesLiees} adresse(s) et {$parcellesLiees} parcelle(s) cadastrale(s).");
        }

        DB::table('lieu_dit')->where('id_lieu_dit', $id)->delete();

        return redirect()->route('lieux-dits.index')->with('success', 'Le lieu-dit a été supprimé du référentiel.');
    }
}
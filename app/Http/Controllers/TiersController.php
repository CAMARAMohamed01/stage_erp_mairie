<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tiers;

class TiersController extends Controller
{
    // AFFICHER LA LISTE DE TOUS LES CITOYENS
    public function index(Request $request)
    {
        // On récupère uniquement les "Physiques", avec leurs infos et le compte de leurs actions
        $citoyens = Tiers::with('physique')
            ->withCount('actions')
            ->where('type_tiers', 'Physique')
            ->get();

        return view('tiers.index', compact('citoyens'));
    }

    // AFFICHER LE PROFIL D'UN CITOYEN ET SON HISTORIQUE
    public function show($id)
    {
        // On charge le citoyen avec ses infos physiques et tous ses actions (triés par date)
        $citoyen = Tiers::with([
            'physique',
            'actions' => function ($query) {
                $query->orderBy('date_creation', 'desc');
            }
        ])->findOrFail($id);

        return view('tiers.show', compact('citoyen'));
    }
}
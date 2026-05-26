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

        $query = Tiers::with('physique')->where('type_tiers', 'Physique');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                // Recherche sur les champs de la table principale
                $q->where('email_tiers', 'ilike', '%' . $search . '%')
                    ->orWhere('tel_tiers', 'ilike', '%' . $search . '%')
                    // Recherche sur la table liée (tiers_physique)
                    ->orWhereHas('physique', function ($subQuery) use ($search) {
                        $subQuery->where('nom_tiers', 'ilike', '%' . $search . '%')
                            ->orWhere('prenom_tiers', 'ilike', '%' . $search . '%');
                    });
            });
        }
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
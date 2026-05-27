<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tiers;
use Illuminate\Support\Facades\DB;

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
        $citoyen = Tiers::with([
            'physique',
            'comptesBancaires.documents', // Pour les RIB liés au compte
            'documents',                  // Pour les documents généraux liés au citoyen
            'actions' => function ($query) {
                $query->orderBy('date_creation', 'desc');
            }
        ])->findOrFail($id);
    }
    // --- CRÉATION D'UN CITOYEN ---
    public function create()
    {
        return view('tiers.create');
    }
    // --- MODIFICATION D'UN CITOYEN ---
    public function edit($id)
    {
        $citoyen = Tiers::with('physique')->where('type_tiers', 'Physique')->findOrFail($id);
        return view('tiers.edit', compact('citoyen'));
    }

    // --- CRÉATION D'UN CITOYEN ---
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email_tiers' => 'nullable|email|max:100|unique:tiers,email_tiers',
            'tel_tiers' => 'nullable|string|max:12',
            'civilite' => 'nullable|string|max:50',
            'nom_tiers' => 'required|string|max:50',
            'prenom_tiers' => 'required|string|max:50',
            'date_naissance' => 'nullable|date',
        ]);

        DB::transaction(function () use ($validated) {
            $tiers = Tiers::create([
                'type_tiers' => 'Physique',
                'email_tiers' => $validated['email_tiers'],
                'tel_tiers' => $validated['tel_tiers'],
            ]);

            DB::table('tiers_physique')->insert([
                'id_tiers' => $tiers->id_tiers,
                'civilite' => $validated['civilite'],
                'nom_tiers' => $validated['nom_tiers'],
                'prenom_tiers' => $validated['prenom_tiers'],
                'date_naissance' => $validated['date_naissance'],
            ]);
        });

        return redirect()->route('tiers.index')->with('success', 'Citoyen ajouté avec succès.');
    }

    // --- MODIFICATION D'UN CITOYEN ---
    public function update(Request $request, $id)
    {
        $citoyen = Tiers::findOrFail($id);

        $validated = $request->validate([
            'email_tiers' => 'nullable|email|max:100|unique:tiers,email_tiers,' . $id . ',id_tiers',
            'tel_tiers' => 'nullable|string|max:12',
            'civilite' => 'nullable|string|max:50',
            'nom_tiers' => 'required|string|max:50',
            'prenom_tiers' => 'required|string|max:50',
            'date_naissance' => 'nullable|date',
        ]);

        DB::transaction(function () use ($validated, $citoyen) {
            $citoyen->update([
                'email_tiers' => $validated['email_tiers'],
                'tel_tiers' => $validated['tel_tiers'],
            ]);

            DB::table('tiers_physique')
                ->where('id_tiers', $citoyen->id_tiers)
                ->update([
                    'civilite' => $validated['civilite'],
                    'nom_tiers' => $validated['nom_tiers'],
                    'prenom_tiers' => $validated['prenom_tiers'],
                    'date_naissance' => $validated['date_naissance'],
                ]);
        });

        return redirect()->route('tiers.show', $citoyen->id_tiers)->with('success', 'Les informations du citoyen ont été mises à jour.');
    }

    // --- CRÉATION ---
    public function createEntreprise()
    {
        return view('tiers.create_entreprise');
    }
    // AFFICHER LE PROFIL D'UNE ENTREPRISE (Tiers Morale)
    public function showEntreprise($id)
    {
        // On charge l'entreprise avec ses infos morales et ses comptes bancaires (incluant les documents liés)

        $entreprise = Tiers::with(['morale', 'comptesBancaires'])->findOrFail($id);

        return view('tiers.show_entreprise', compact('entreprise'));
    }
    public function storeEntreprise(Request $request)
    {
        $validated = $request->validate([
            'email_tiers' => 'nullable|email|max:100|unique:tiers,email_tiers',
            'tel_tiers' => 'nullable|string|max:12',
            'raison_sociale' => 'required|string|max:150',
            'siret' => 'nullable|string|max:14',
            'numero_tva_intra' => 'nullable|string|max:30',
            'alias_tiers' => 'nullable|string|max:10',
            'nom_contact' => 'nullable|string|max:100',
            'num_compte_client' => 'nullable|string|max:50',
        ]);

        DB::transaction(function () use ($validated) {
            // 1. Insertion dans la table parente
            $tiers = Tiers::create([
                'type_tiers' => 'Morale',
                'email_tiers' => $validated['email_tiers'],
                'tel_tiers' => $validated['tel_tiers'],
            ]);

            // 2. Insertion dans la table enfant
            DB::table('tiers_morale')->insert([
                'id_tiers' => $tiers->id_tiers,
                'raison_sociale' => $validated['raison_sociale'],
                'siret' => $validated['siret'],
                'numero_tva_intra' => $validated['numero_tva_intra'],
                'alias_tiers' => $validated['alias_tiers'],
                'nom_contact' => $validated['nom_contact'],
                'num_compte_client' => $validated['num_compte_client'],
            ]);
        });

        return redirect()->route('tiers.entreprises')->with('success', 'Entreprise ajoutée avec succès.');
    }

    // --- MODIFICATION D'UNE ENTREPRISE ---
    public function editEntreprise($id)
    {
        $entreprise = Tiers::with('morale')->findOrFail($id);

        return view('tiers.edit_entreprise', compact('entreprise'));
    }

    public function updateEntreprise(Request $request, $id)
    {
        $entreprise = Tiers::findOrFail($id);

        $validated = $request->validate([
            // On ignore l'email actuel pour la règle "unique"
            'email_tiers' => 'nullable|email|max:100|unique:tiers,email_tiers,' . $id . ',id_tiers',
            'tel_tiers' => 'nullable|string|max:12',
            'raison_sociale' => 'required|string|max:150',
            'siret' => 'nullable|string|max:14',
            'numero_tva_intra' => 'nullable|string|max:30',
            'alias_tiers' => 'nullable|string|max:10',
            'nom_contact' => 'nullable|string|max:100',
            'num_compte_client' => 'nullable|string|max:50',
        ]);

        DB::transaction(function () use ($validated, $entreprise) {
            $entreprise->update([
                'email_tiers' => $validated['email_tiers'],
                'tel_tiers' => $validated['tel_tiers'],
            ]);

            DB::table('tiers_morale')
                ->where('id_tiers', $entreprise->id_tiers)
                ->update([
                    'raison_sociale' => $validated['raison_sociale'],
                    'siret' => $validated['siret'],
                    'numero_tva_intra' => $validated['numero_tva_intra'],
                    'alias_tiers' => $validated['alias_tiers'],
                    'nom_contact' => $validated['nom_contact'],
                    'num_compte_client' => $validated['num_compte_client'],
                ]);
        });

        return redirect()->route('tiers.entreprises')->with('success', 'Les informations ont été mises à jour.');
    }
    // AFFICHER L'ANNUAIRE DES ENTREPRISES (Tiers Morale)
    public function entreprises(Request $request)
    {
        // On charge la relation morale et les comptes bancaires (avec leurs documents)
        $query = Tiers::with(['morale', 'comptesBancaires.documents'])
            ->where('type_tiers', 'Morale')
            ->orWhere('type_tiers', 'Personne Morale');
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                // Recherche sur les contacts
                $q->where('email_tiers', 'ilike', '%' . $search . '%')
                    ->orWhere('tel_tiers', 'ilike', '%' . $search . '%')
                    // Recherche sur les infos de l'entreprise
                    ->orWhereHas('morale', function ($subQuery) use ($search) {
                        $subQuery->where('raison_sociale', 'ilike', '%' . $search . '%')
                            ->orWhere('siret', 'ilike', '%' . $search . '%')
                            ->orWhere('nom_contact', 'ilike', '%' . $search . '%');
                    });
            });
        }

        // On trie par ordre alphabétique de la raison sociale
        $entreprises = $query->get()->sortBy(function ($tiers) {
            return $tiers->morale->raison_sociale ?? 'ZZZ';
        });

        return view('tiers.entreprises', compact('entreprises'));
    }


    // --- SUPPRESSION GÉNÉRIQUE ---
    public function destroy($id)
    {
        $tiers = Tiers::findOrFail($id);
        $type = $tiers->type_tiers;

        DB::transaction(function () use ($tiers) {
            // Supprimer d'abord la dépendance dans la table enfant
            if ($tiers->type_tiers === 'Morale') {
                DB::table('tiers_morale')->where('id_tiers', $tiers->id_tiers)->delete();
            } else {
                DB::table('tiers_physique')->where('id_tiers', $tiers->id_tiers)->delete();
            }

            // Ensuite on supprime le parent
            $tiers->delete();
        });

        if ($type === 'Morale') {
            return redirect()->route('tiers.entreprises')->with('success', 'Entreprise supprimée définitivement.');
        }
        return redirect()->route('tiers.index')->with('success', 'Citoyen supprimé.');
    }
}
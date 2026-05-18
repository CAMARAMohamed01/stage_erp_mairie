<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BatimentController extends Controller
{
    // Liste de tous les bâtiments de la mairie
    public function index()
    {
        // Récupération de tous les bâtiments avec leur classification ERP s'ils en ont une
        $batiments = DB::table('batiment')
            ->leftJoin('type_erp', 'batiment.id_type_erp', '=', 'type_erp.id_type_erp')
            ->select('batiment.*', 'type_erp.categorie_erp')
            ->orderBy('nom_bat')
            ->get();

        return view('batiments.index', compact('batiments'));
    }

    // Fiche détaillée d'un bâtiment (Le point central de convergence de tes données)
    public function show($id)
    {
        // 1. Infos du bâtiment et son type ERP
        $batiment = DB::table('batiment')
            ->leftJoin('type_erp', 'batiment.id_type_erp', '=', 'type_erp.id_type_erp')
            ->select('batiment.*', 'type_erp.categorie_erp')
            ->where('id_batiment', $id)
            ->first();

        if (!$batiment) {
            abort(404, 'Bâtiment introuvable');
        }

        // 2. Les équipements installés dans ce bâtiment
        $equipements = DB::table('equipement')
            ->where('id_batiment', $id)
            ->orderBy('nom_equipement')
            ->get();

        // 3. Les contrôles réglementaires prévus pour ce bâtiment
        $controles = DB::table('controle_reglementaire')
            ->where('id_batiment', $id)
            ->orderBy('date_prochain_controle')
            ->get();

        // 4. Les interventions récentes ou en cours dans ce bâtiment
        $interventions = DB::table('intervention')
            ->where('id_batiment', $id)
            ->orderByDesc('date_creation')
            ->limit(5) // On prend les 5 plus récentes
            ->get();

        // 5. Les signalements citoyens non résolus dans ce bâtiment
        $signalements = DB::table('signalement')
            ->where('id_batiment', $id)
            ->where('statut', '!=', 'Résolu')
            ->get();

        return view('batiments.show', compact('batiment', 'equipements', 'controles', 'interventions', 'signalements'));
    }

    // Afficher le formulaire de création
    public function create()
    {
        $tiers = DB::table('tiers')
            ->leftJoin('tiers_morale', 'tiers.id_tiers', '=', 'tiers_morale.id_tiers')
            ->leftJoin('tiers_physique', 'tiers.id_tiers', '=', 'tiers_physique.id_tiers')
            ->select('tiers.id_tiers', 'tiers.type_tiers', 'tiers_morale.raison_sociale', 'tiers_physique.nom_tiers', 'tiers_physique.prenom_tiers')
            ->get();

        $adresses = DB::table('Adresse')->get();
        $parcelles = DB::table('parcelle')->get();
        $types_erp = DB::table('type_erp')->orderBy('categorie_erp')->get();
        $lieu_dits = DB::table('lieu_dit')->orderBy('nom_lieu_dit')->get();

        $immos_disponibles = DB::table('immobilisation_inventaire_')
            ->whereNotIn('id_immo', function ($query) {
                $query->select('id_immo')->from('batiment');
            })->get();

        return view('batiments.create', compact('tiers', 'adresses', 'parcelles', 'types_erp', 'immos_disponibles', 'lieu_dits'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom_bat' => 'required|string|max:100',
            'surface_totale_m2' => 'nullable|numeric',
            'date_construction' => 'nullable|date',
            'id_tiers' => 'required|integer|exists:tiers,id_tiers',
            'id_parcelle' => 'required|integer|exists:parcelle,id_parcelle',
            'id_type_erp' => 'required|integer|exists:type_erp,id_type_erp',
            'id_adresse' => 'required|integer|exists:Adresse,id_adresse',
            'id_immo' => 'required|integer|exists:immobilisation_inventaire_,id_immo|unique:batiment,id_immo',
        ]);

        DB::table('batiment')->insert([
            'nom_bat' => $request->nom_bat,
            'surface_totale_m2' => $request->surface_totale_m2,
            'date_construction' => $request->date_construction,
            'id_tiers' => $request->id_tiers,
            'id_parcelle' => $request->id_parcelle,
            'id_type_erp' => $request->id_type_erp,
            'id_adresse' => $request->id_adresse,
            'id_immo' => $request->id_immo,
        ]);

        return redirect()->route('batiments.index')->with('success', 'Le bâtiment a été intégré avec succès.');
    }

    // --- ENREGISTREMENTS RAPIDES AJAX ---

    public function quickStoreAdresse(Request $request)
    {
        $id = DB::table('Adresse')->insertGetId([
            'num_rue' => $request->num_rue,
            'nom_voie' => $request->nom_voie,
            'code_postal' => $request->code_postal,
            'ville' => $request->ville,
            'id_lieu_dit' => $request->id_lieu_dit,
        ], 'id_adresse');

        return response()->json(['id' => $id, 'label' => "{$request->num_rue} {$request->nom_voie}, {$request->ville}"]);
    }

    public function quickStoreParcelle(Request $request)
    {
        $id = DB::table('parcelle')->insertGetId([
            'num_parcelle' => $request->num_parcelle,
            'section_cadastrale' => $request->section_cadastrale,
            'type_parcelle' => $request->type_parcelle,
            'id_lieu_dit' => $request->id_lieu_dit,
            'id_immo' => $request->id_immo,
        ], 'id_parcelle');

        return response()->json(['id' => $id, 'label' => "Section {$request->section_cadastrale} - N° {$request->num_parcelle}"]);
    }

    public function quickStoreTiers(Request $request)
    {
        $id_tiers = DB::table('tiers')->insertGetId([
            'type_tiers' => $request->type_tiers,
            'email_tiers' => $request->email_tiers,
            'id_adresse' => $request->id_adresse ?: null,
        ], 'id_tiers');

        if ($request->type_tiers === 'Personne Morale') {
            DB::table('tiers_morale')->insert([
                'id_tiers' => $id_tiers,
                'raison_sociale' => $request->raison_sociale,
                'siret' => $request->siret
            ]);
            $label = $request->raison_sociale;
        } else {
            DB::table('tiers_physique')->insert([
                'id_tiers' => $id_tiers,
                'civilite' => $request->civilite,
                'nom_tiers' => $request->nom_tiers,
                'prenom_tiers' => $request->prenom_tiers,
            ]);
            $label = $request->nom_tiers . ' ' . $request->prenom_tiers;
        }

        return response()->json(['id' => $id_tiers, 'label' => $label]);
    }

    // Sauvegarder le bâtiment en base
    // public function store(Request $request)
    // {
    //     // Validation stricte des données selon tes contraintes PostgreSQL
    //     $request->validate([
    //         'nom_bat' => 'required|string|max:100',
    //         'surface_totale_m2' => 'nullable|numeric',
    //         'date_construction' => 'nullable|date',
    //         'id_tiers' => 'required|integer|exists:tiers,id_tiers',
    //         'id_parcelle' => 'required|integer|exists:parcelle,id_parcelle',
    //         'id_type_erp' => 'required|integer|exists:type_erp,id_type_erp',
    //         'id_adresse' => 'required|integer|exists:Adresse,id_adresse',
    //         'id_immo' => 'required|integer|exists:immobilisation_inventaire_,id_immo|unique:batiment,id_immo',
    //     ]);

    //     DB::table('batiment')->insert([
    //         'nom_bat' => $request->nom_bat,
    //         'surface_totale_m2' => $request->surface_totale_m2,
    //         'date_construction' => $request->date_construction,
    //         'id_tiers' => $request->id_tiers,
    //         'id_parcelle' => $request->id_parcelle,
    //         'id_type_erp' => $request->id_type_erp,
    //         'id_adresse' => $request->id_adresse,
    //         'id_immo' => $request->id_immo,
    //     ]);

    //     return redirect()->route('batiments.index')
    //         ->with('success', 'Le nouveau bâtiment a été intégré au patrimoine de la commune.');
    // }
}
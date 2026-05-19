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
        // 1. Infos du bâtiment, son type ERP ET son Adresse
        $batiment = DB::table('batiment')
            ->leftJoin('type_erp', 'batiment.id_type_erp', '=', 'type_erp.id_type_erp')
            ->leftJoin('Adresse', 'batiment.id_adresse', '=', 'Adresse.id_adresse')
            ->select(
                'batiment.*',
                'type_erp.categorie_erp',
                'Adresse.num_rue',
                'Adresse.nom_voie',
                'Adresse.code_postal',
                'Adresse.ville'
            )
            ->where('id_batiment', $id)
            ->first();

        if (!$batiment) {
            abort(404, 'Bâtiment introuvable');
        }

        // 2. NOUVEAU : Récupérer les locaux/pièces rattachés à ce bâtiment
        $locaux = DB::table('local_')
            ->leftJoin('type_usage', 'local_.id_usage', '=', 'type_usage.id_usage')
            ->select('local_.*', 'type_usage.libelle_usage')
            ->where('id_batiment', $id)
            ->orderBy('local_.niveau')
            ->orderBy('local_.nom_local')
            ->get();

        // 3. Les équipements : via l'immobilisation commune ou via les locaux du bâtiment
        $equipements = DB::table('equipement')
            ->leftJoin('local_', 'equipement.id_local', '=', 'local_.id_local')
            ->where('equipement.id_immo', $batiment->id_immo)
            ->orWhere('local_.id_batiment', $id)
            ->select('equipement.*')
            ->distinct()
            ->orderBy('nom_equipement')
            ->get();

        // 4. Les contrôles réglementaires (via les lieux publics rattachés)
        $controles = DB::table('controle_reglementaire')
            ->join('lieux_publics', 'controle_reglementaire.id_lieu', '=', 'lieux_publics.id_lieu')
            ->where('lieux_publics.id_batiment', $id)
            ->select('controle_reglementaire.*')
            ->orderBy('designation')
            ->get();

        // 5. Les interventions récentes : liées à l'adresse du bâtiment
        $interventions = DB::table('intervention')
            ->where('id_adresse', $batiment->id_adresse)
            ->orderByDesc('date_ouverture')
            ->limit(5)
            ->get();

        // 6. Les signalements citoyens : liés à l'adresse du bâtiment
        $signalements = DB::table('signalement')
            ->where('id_adresse', $batiment->id_adresse)
            ->where('statut_signalement', '!=', 'Clôturé')
            ->get();

        // On ajoute 'locaux' dans le compact
        return view('batiments.show', compact('batiment', 'locaux', 'equipements', 'controles', 'interventions', 'signalements'));
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

    // --- MODIFICATION D'UN BÂTIMENT ---

    public function edit($id)
    {
        $batiment = DB::table('batiment')->where('id_batiment', $id)->first();

        if (!$batiment) {
            abort(404, 'Bâtiment introuvable');
        }

        // Récupération des référentiels pour les listes déroulantes
        $tiers = DB::table('tiers')
            ->leftJoin('tiers_morale', 'tiers.id_tiers', '=', 'tiers_morale.id_tiers')
            ->leftJoin('tiers_physique', 'tiers.id_tiers', '=', 'tiers_physique.id_tiers')
            ->select('tiers.id_tiers', 'tiers.type_tiers', 'tiers_morale.raison_sociale', 'tiers_physique.nom_tiers', 'tiers_physique.prenom_tiers')
            ->get();

        $adresses = DB::table('Adresse')->get();
        $parcelles = DB::table('parcelle')->get();
        $types_erp = DB::table('type_erp')->orderBy('categorie_erp')->get();
        $lieu_dits = DB::table('lieu_dit')->orderBy('nom_lieu_dit')->get();

        // 💡 ASTUCE DATA : On récupère les immos libres + celle actuellement liée à ce bâtiment
        $immos = DB::table('immobilisation_inventaire_')
            ->whereNotIn('id_immo', function ($query) use ($id) {
                // On exclut les immos utilisées par TOUS les autres bâtiments SAUF celui-ci
                $query->select('id_immo')->from('batiment')->where('id_batiment', '!=', $id);
            })->get();

        return view('batiments.edit', compact('batiment', 'tiers', 'adresses', 'parcelles', 'types_erp', 'immos', 'lieu_dits'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nom_bat' => 'required|string|max:100',
            'surface_totale_m2' => 'nullable|numeric',
            'date_construction' => 'nullable|date',
            'id_tiers' => 'required|integer|exists:tiers,id_tiers',
            'id_parcelle' => 'required|integer|exists:parcelle,id_parcelle',
            'id_type_erp' => 'required|integer|exists:type_erp,id_type_erp',
            'id_adresse' => 'required|integer|exists:Adresse,id_adresse',
            // On vérifie l'unicité de l'immo, en ignorant l'ID du bâtiment en cours de modification
            'id_immo' => 'required|integer|exists:immobilisation_inventaire_,id_immo|unique:batiment,id_immo,' . $id . ',id_batiment',
        ]);

        DB::table('batiment')
            ->where('id_batiment', $id)
            ->update([
                'nom_bat' => $request->nom_bat,
                'surface_totale_m2' => $request->surface_totale_m2,
                'date_construction' => $request->date_construction,
                'id_tiers' => $request->id_tiers,
                'id_parcelle' => $request->id_parcelle,
                'id_type_erp' => $request->id_type_erp,
                'id_adresse' => $request->id_adresse,
                'id_immo' => $request->id_immo,
            ]);

        return redirect()->route('batiments.show', $id)
            ->with('success', 'Les informations du bâtiment ont été mises à jour avec succès.');
    }

    // --- SUPPRESSION D'UN BÂTIMENT ---

    // --- SUPPRESSION D'UN BÂTIMENT ---

    public function destroy($id)
    {
        // 1. On récupère le bâtiment pour obtenir son id_immo
        $batiment = DB::table('batiment')->where('id_batiment', $id)->first();

        if (!$batiment) {
            return redirect()->route('batiments.index')->with('error', 'Bâtiment introuvable.');
        }

        // 2. Vérification des équipements liés (via l'immobilisation ou via un local)
        $equipementsLies = DB::table('equipement')
            ->leftJoin('local_', 'equipement.id_local', '=', 'local_.id_local')
            ->where('equipement.id_immo', $batiment->id_immo)
            ->orWhere('local_.id_batiment', $id)
            ->count();

        if ($equipementsLies > 0) {
            return redirect()->back()->with('error', "🛑 Impossible de supprimer : $equipementsLies équipement(s) sont encore rattachés à ce bâtiment.");
        }

        // 3. Vérification des dépendances structurelles (Locaux et Lieux publics)
        $locauxLies = DB::table('local_')->where('id_batiment', $id)->count();
        $lieuxLies = DB::table('lieux_publics')->where('id_batiment', $id)->count();

        if ($locauxLies > 0 || $lieuxLies > 0) {
            return redirect()->back()->with('error', "🛑 Impossible de supprimer : Ce bâtiment contient encore $locauxLies local/locaux et $lieuxLies lieu(x) public(s). Vous devez les supprimer ou les réaffecter d'abord.");
        }

        // 4. Si toutes les vérifications sont bonnes, on supprime !
        DB::table('batiment')->where('id_batiment', $id)->delete();

        return redirect()->route('batiments.index')
            ->with('success', '✅ Le bâtiment a été retiré du patrimoine communal avec succès.');
    }
}
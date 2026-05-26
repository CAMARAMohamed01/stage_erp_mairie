<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Contrat;
use App\Models\Batiment;

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
        // 1. Infos du bâtiment, son type ERP, son Adresse ET sa Parcelle
        $batiment = DB::table('batiment')
            ->leftJoin('type_erp', 'batiment.id_type_erp', '=', 'type_erp.id_type_erp')
            ->leftJoin('Adresse', 'batiment.id_adresse', '=', 'Adresse.id_adresse')
            ->leftJoin('parcelle', 'batiment.id_parcelle', '=', 'parcelle.id_parcelle')
            ->leftJoin('lieu_dit', 'parcelle.id_lieu_dit', '=', 'lieu_dit.id_lieu_dit') // ← manquant
            ->select(
                'batiment.*',
                'type_erp.categorie_erp',
                'Adresse.num_rue',
                'Adresse.nom_voie',
                'Adresse.code_postal',
                'Adresse.ville',
                'parcelle.section_cadastrale',
                'parcelle.num_parcelle',
                'lieu_dit.nom_lieu_dit',
                DB::raw('ST_AsGeoJSON(batiment.geom_batiment) as geojson_batiment'),
                DB::raw('ST_AsGeoJSON(parcelle.geom_parcelle) as geojson_parcelle')
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

        $compteurs_generaux = DB::table('compteur')
            ->join('local_', 'compteur.id_local', '=', 'local_.id_local')
            ->where('local_.id_batiment', $id)
            ->where('compteur.dessert_tout_le_batiment', true)
            ->select('compteur.*')
            ->get();

        //  Les contrats liés directement à ce bâtiment (via la table pivot contrat_batiment)
        $contrats = DB::table('contrat')
            ->join('contrat_batiment', 'contrat.id_contrat', '=', 'contrat_batiment.id_contrat')
            ->leftJoin('tiers', 'contrat.id_tiers', '=', 'tiers.id_tiers')
            ->leftJoin('tiers_morale', 'tiers.id_tiers', '=', 'tiers_morale.id_tiers')
            ->leftJoin('tiers_physique', 'tiers.id_tiers', '=', 'tiers_physique.id_tiers')
            ->where('contrat_batiment.id_batiment', $id)
            ->select('contrat.*', 'tiers_morale.raison_sociale', 'tiers_physique.nom_tiers', 'tiers_physique.prenom_tiers')
            ->get();
        //documents liés à ce bâtiment
        $documents = DB::table('document')
            ->where('id_batiment', $id)
            ->orderByDesc('date_upload')
            ->get();

        // 6. Les actions citoyens : liés à l'adresse du bâtiment
        $actions = DB::table('action')
            ->where('id_adresse', $batiment->id_adresse)
            ->where('statut_action', '!=', 'Clôturé')
            ->get();

        // On ajoute 'locaux' dans le compact
        return view('batiments.show', compact('batiment', 'locaux', 'equipements', 'controles', 'interventions', 'actions', 'compteurs_generaux', 'contrats', 'documents'));
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

        // NOUVEAU : Récupération des contrats
        $contrats = Contrat::orderBy('numero_contrat')->get();

        return view('batiments.create', compact('tiers', 'adresses', 'parcelles', 'types_erp', 'immos_disponibles', 'lieu_dits', 'contrats'));
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
            'geojson_data' => 'nullable|json',
            // Validation des contrats
            'id_contrats' => 'nullable|array',
            'id_contrats.*' => 'exists:contrat,id_contrat',
        ]);

        // On utilise Eloquent (Batiment::create) au lieu de DB::table pour pouvoir chaîner la relation
        $batiment = Batiment::create([
            'nom_bat' => $request->nom_bat,
            'surface_totale_m2' => $request->surface_totale_m2,
            'date_construction' => $request->date_construction,
            'id_tiers' => $request->id_tiers,
            'id_parcelle' => $request->id_parcelle,
            'id_type_erp' => $request->id_type_erp,
            'id_adresse' => $request->id_adresse,
            'id_immo' => $request->id_immo,
        ]);

        // NOUVEAU : On attache les contrats dans la table pivot
        if ($request->has('id_contrats')) {
            $batiment->contratsAdministratifs()->attach($request->id_contrats);
        }
        // Enregistrement du Point GPS
        if ($request->filled('geojson_data')) {
            DB::update(
                "UPDATE batiment 
             SET geom_batiment = ST_SetSRID(ST_GeomFromGeoJSON(?), 4326) 
             WHERE id_batiment = ?",
                [$request->geojson_data, $batiment->id_batiment]
            );
        }

        return redirect()->route('batiments.index')->with('success', 'Le bâtiment a été intégré avec succès.');
    }

    public function edit($id)
    {
        // On remplace DB::table par Eloquent pour charger les contrats liés
        $batiment = Batiment::with('contratsAdministratifs')
            ->select('*', DB::raw('ST_AsGeoJSON(geom_batiment) as geojson'))
            ->findOrFail($id);

        $tiers = DB::table('tiers')
            ->leftJoin('tiers_morale', 'tiers.id_tiers', '=', 'tiers_morale.id_tiers')
            ->leftJoin('tiers_physique', 'tiers.id_tiers', '=', 'tiers_physique.id_tiers')
            ->select('tiers.id_tiers', 'tiers.type_tiers', 'tiers_morale.raison_sociale', 'tiers_physique.nom_tiers', 'tiers_physique.prenom_tiers')
            ->get();

        $adresses = DB::table('Adresse')->get();
        $parcelles = DB::table('parcelle')->get();
        $types_erp = DB::table('type_erp')->orderBy('categorie_erp')->get();
        $lieu_dits = DB::table('lieu_dit')->orderBy('nom_lieu_dit')->get();

        $immos = DB::table('immobilisation_inventaire_')
            ->whereNotIn('id_immo', function ($query) use ($id) {
                $query->select('id_immo')->from('batiment')->where('id_batiment', '!=', $id);
            })->get();

        // NOUVEAU : Récupération des contrats
        $contrats = Contrat::orderBy('numero_contrat')->get();

        return view('batiments.edit', compact('batiment', 'tiers', 'adresses', 'parcelles', 'types_erp', 'immos', 'lieu_dits', 'contrats'));
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
            'id_immo' => 'required|integer|exists:immobilisation_inventaire_,id_immo|unique:batiment,id_immo,' . $id . ',id_batiment',

            // Validation des contrats
            'id_contrats' => 'nullable|array',
            'id_contrats.*' => 'exists:contrat,id_contrat',
        ]);

        $batiment = Batiment::findOrFail($id);

        $batiment->update([
            'nom_bat' => $request->nom_bat,
            'surface_totale_m2' => $request->surface_totale_m2,
            'date_construction' => $request->date_construction,
            'id_tiers' => $request->id_tiers,
            'id_parcelle' => $request->id_parcelle,
            'id_type_erp' => $request->id_type_erp,
            'id_adresse' => $request->id_adresse,
            'id_immo' => $request->id_immo,
        ]);
        // Enregistrement du Point GPS
        if ($request->filled('geojson_data')) {
            DB::update(
                "UPDATE batiment 
             SET geom_batiment = ST_SetSRID(ST_GeomFromGeoJSON(?), 4326) 
             WHERE id_batiment = ?",
                [$request->geojson_data, $batiment->id_batiment]
            );
        }

        $batiment->contratsAdministratifs()->sync($request->id_contrats ?? []);

        return redirect()->route('batiments.show', $id)
            ->with('success', 'Les informations du bâtiment ont été mises à jour avec succès.');
    }

    public function uploadDocument(Request $request, $idBatiment)
    {
        $request->validate(['fichier' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120']);

        $file = $request->file('fichier');
        $path = $file->store('documents/batiments', 'public'); // Adapte le nom du dossier

        \App\Models\Document::create([
            'nom_fichier' => $file->getClientOriginalName(),
            'type_doc' => $file->getClientOriginalExtension(),
            'chemin_stockage' => $path,
            'taille_ko' => $file->getSize() / 1024,
            'date_upload' => now(),
            'id_batiment' => $idBatiment, // <-- C'est LA seule chose qui change selon le contrôleur
        ]);

        return back()->with('success', 'Document ajouté au bâtiment.');
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



    public function destroy($id)
    {
        // 1. On récupère le bâtiment pour obtenir son id_immo
        $batiment = DB::table('batiment')->where('id_batiment', $id)->first();

        if (!$batiment) {
            return redirect()->route('batiments.index')->with('error', 'Bâtiment introuvable.');
        }

        //  Vérification des équipements liés (via l'immobilisation ou via un local)
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
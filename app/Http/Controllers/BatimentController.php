<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Contrat;
use App\Models\Batiment;

class BatimentController extends Controller
{
    // Liste de tous les bâtiments de la mairie
    public function index(Request $request)
    {
        // 1. Initialisation de la requête avec les jointures nécessaires
        $query = DB::table('batiment')
            ->leftJoin('type_erp', 'batiment.id_type_erp', '=', 'type_erp.id_type_erp')
            ->leftJoin('Adresse', 'batiment.id_adresse', '=', 'Adresse.id_adresse') // Jointure avec la table Adresse
            ->select('batiment.*', 'type_erp.categorie_erp', 'Adresse.nom_voie', 'Adresse.ville');

        // 2. Application du filtre
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('batiment.nom_bat', 'ilike', '%' . $search . '%')
                    ->orWhere('Adresse.nom_voie', 'ilike', '%' . $search . '%') // Recherche sur la voie
                    ->orWhere('Adresse.ville', 'ilike', '%' . $search . '%');    // Recherche sur la ville
            });
        }

        $batiments = $query->orderBy('nom_bat')->get();

        return view('batiments.index', compact('batiments'));
    }

    // Fiche détaillée d'un bâtiment (Le point central de convergence de tes données)
    public function show($id)
    {
        // 1. Infos du bâtiment, son type ERP, son Adresse ET sa Parcelle
        $batiment = DB::table('batiment')
            ->leftJoin('type_erp', 'batiment.id_type_erp', '=', 'type_erp.id_type_erp')
            ->leftJoin('Adresse', 'batiment.id_adresse', '=', 'Adresse.id_adresse')
            ->leftJoin('parcelle', 'batiment.id_parcelle', '=', 'parcelle.id_parcelle')
            ->leftJoin('lieu_dit', 'parcelle.id_lieu_dit', '=', 'lieu_dit.id_lieu_dit')
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

        // Récupérer les locaux/pièces rattachés à ce bâtiment (trié proprement sur le nom)
        $locaux = DB::table('local_')
            ->select('local_.*')
            ->where('id_batiment', $id)
            ->orderBy('local_.nom_local')
            ->get();

        // Équipements rattachés via l'immobilisation commune ou via les locaux du bâtiment
        $equipements = DB::table('equipement')
            ->leftJoin('local_', 'equipement.id_local', '=', 'local_.id_local')
            ->where('equipement.id_immo', $batiment->id_immo)
            ->orWhere('local_.id_batiment', $id)
            ->select('equipement.*')
            ->distinct()
            ->orderBy('nom_equipement')
            ->get();

        // Les contrôles réglementaires (via la table de correspondance ERP)
        $controles = collect();

        if (isset($batiment->id_type_erp) && $batiment->id_type_erp) {
            $controles = DB::table('controle_reglementaire')
                ->join('type_erp_controle', 'controle_reglementaire.id_controle', '=', 'type_erp_controle.id_controle')
                ->where('type_erp_controle.id_type_erp', $batiment->id_type_erp)
                ->select('controle_reglementaire.*')
                ->orderBy('controle_reglementaire.designation')
                ->get();
        }

        // 5. Les interventions récentes : liées à l'adresse du bâtiment
        $interventions = DB::table('intervention')
            ->where('id_adresse', $batiment->id_adresse)
            ->orderByDesc('date_ouverture')
            ->limit(5)
            ->get();

        // Compteurs généraux rattachés au bâtiment
        $compteurs_generaux = DB::table('compteur')
            ->join('local_', 'compteur.id_local', '=', 'local_.id_local')
            ->where('local_.id_batiment', $id)
            ->where('compteur.dessert_tout_le_batiment', true)
            ->select('compteur.*')
            ->get();

        // Les contrats liés directement à ce bâtiment (via la table pivot contrat_batiment)
        $contrats = DB::table('contrat')
            ->join('contrat_batiment', 'contrat.id_contrat', '=', 'contrat_batiment.id_contrat')
            ->leftJoin('tiers', 'contrat.id_tiers', '=', 'tiers.id_tiers')
            ->leftJoin('tiers_morale', 'tiers.id_tiers', '=', 'tiers_morale.id_tiers')
            ->leftJoin('tiers_physique', 'tiers.id_tiers', '=', 'tiers_physique.id_tiers')
            ->where('contrat_batiment.id_batiment', $id)
            ->select('contrat.*', 'tiers_morale.raison_sociale', 'tiers_physique.nom_tiers', 'tiers_physique.prenom_tiers')
            ->get();

        // Documents liés à ce bâtiment
        $documents = DB::table('document')
            ->where('id_batiment', $id)
            ->orderByDesc('date_upload')
            ->get();

        // 6. Les actions citoyens : liées à l'adresse du bâtiment
        $actions = DB::table('action')
            ->where('id_adresse', $batiment->id_adresse)
            ->where('statut_action', '!=', 'Clôturé')
            ->get();

        return view('batiments.show', compact('batiment', 'locaux', 'equipements', 'controles', 'interventions', 'actions', 'compteurs_generaux', 'contrats', 'documents'));
    }

    // Afficher le formulaire de création
    public function create()
    {
        $adresses = DB::table('Adresse')
            ->select('id_adresse', 'num_rue', 'nom_voie', 'ville', 'latitude', 'longitude')
            ->orderBy('nom_voie')
            ->get();
        $parcelles = DB::table('parcelle')->get();
        $types_erp = DB::table('type_erp')->orderBy('categorie_erp')->get();
        $lieu_dits = DB::table('lieu_dit')->orderBy('nom_lieu_dit')->get();

        $immos_disponibles = DB::table('immobilisation_inventaire_')
            ->whereNotIn('id_immo', function ($query) {
                $query->select('id_immo')->from('batiment');
            })->get();

        // Récupération des contrats
        $contrats = Contrat::orderBy('numero_contrat')->get();

        return view('batiments.create', compact('adresses', 'parcelles', 'types_erp', 'immos_disponibles', 'lieu_dits', 'contrats'));
    }

    public function store(Request $request)
    {
        // ✅ MISE À JOUR : Suppression de la validation requise sur 'id_tiers'
        $request->validate([
            'nom_bat' => 'required|string|max:100',
            'surface_totale_m2' => 'nullable|numeric',
            'date_construction' => 'nullable|date',
            'id_parcelle' => 'required|integer|exists:parcelle,id_parcelle',
            'id_type_erp' => 'required|integer|exists:type_erp,id_type_erp',
            'id_adresse' => 'required|integer|exists:Adresse,id_adresse',
            'id_immo' => 'required|integer|exists:immobilisation_inventaire_,id_immo|unique:batiment,id_immo',
            'geojson_data' => 'nullable|json',
            'id_contrats' => 'nullable|array',
            'id_contrats.*' => 'exists:contrat,id_contrat',
        ]);

        // Utilisation d'Eloquent pour chaîner les contrats
        $batiment = Batiment::create([
            'nom_bat' => $request->nom_bat,
            'surface_totale_m2' => $request->surface_totale_m2,
            'date_construction' => $request->date_construction,
            'id_parcelle' => $request->id_parcelle,
            'id_type_erp' => $request->id_type_erp,
            'id_adresse' => $request->id_adresse,
            'id_immo' => $request->id_immo,
        ]);

        // Liaison dans la table pivot des contrats
        if ($request->has('id_contrats')) {
            $batiment->contratsAdministratifs()->attach($request->id_contrats);
        }

        // Enregistrement du Point GPS PostGIS
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
        $batiment = Batiment::with('contratsAdministratifs')
            ->select('*', DB::raw('ST_AsGeoJSON(geom_batiment) as geojson'))
            ->findOrFail($id);

        $adresses = DB::table('Adresse')->get();
        $parcelles = DB::table('parcelle')->get();
        $types_erp = DB::table('type_erp')->orderBy('categorie_erp')->get();
        $lieu_dits = DB::table('lieu_dit')->orderBy('nom_lieu_dit')->get();

        $immos = DB::table('immobilisation_inventaire_')
            ->whereNotIn('id_immo', function ($query) use ($id) {
                $query->select('id_immo')->from('batiment')->where('id_batiment', '!=', $id);
            })->get();

        $contrats = Contrat::orderBy('numero_contrat')->get();

        return view('batiments.edit', compact('batiment', 'adresses', 'parcelles', 'types_erp', 'immos', 'lieu_dits', 'contrats'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nom_bat' => 'required|string|max:100',
            'surface_totale_m2' => 'nullable|numeric',
            'date_construction' => 'nullable|date',
            'id_parcelle' => 'required|integer|exists:parcelle,id_parcelle',
            'id_type_erp' => 'required|integer|exists:type_erp,id_type_erp',
            'id_adresse' => 'required|integer|exists:Adresse,id_adresse',
            'id_immo' => 'required|integer|exists:immobilisation_inventaire_,id_immo|unique:batiment,id_immo,' . $id . ',id_batiment',
            'id_contrats' => 'nullable|array',
            'id_contrats.*' => 'exists:contrat,id_contrat',
        ]);

        $batiment = Batiment::findOrFail($id);

        $batiment->update([
            'nom_bat' => $request->nom_bat,
            'surface_totale_m2' => $request->surface_totale_m2,
            'date_construction' => $request->date_construction,
            'id_parcelle' => $request->id_parcelle,
            'id_type_erp' => $request->id_type_erp,
            'id_adresse' => $request->id_adresse,
            'id_immo' => $request->id_immo,
        ]);

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
        $path = $file->store('documents/batiments', 'public');

        \App\Models\Document::create([
            'nom_fichier' => $file->getClientOriginalName(),
            'type_doc' => $file->getClientOriginalExtension(),
            'chemin_stockage' => $path,
            'taille_ko' => $file->getSize() / 1024,
            'date_upload' => now(),
            'id_batiment' => $idBatiment,
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

    public function destroy($id)
    {
        $batiment = DB::table('batiment')->where('id_batiment', $id)->first();

        if (!$batiment) {
            return redirect()->route('batiments.index')->with('error', 'Bâtiment introuvable.');
        }

        // Vérification des équipements liés (via l'immo commune ou via un local)
        $equipementsLies = DB::table('equipement')
            ->leftJoin('local_', 'equipement.id_local', '=', 'local_.id_local')
            ->where('equipement.id_immo', $batiment->id_immo)
            ->orWhere('local_.id_batiment', $id)
            ->count();

        if ($equipementsLies > 0) {
            return redirect()->back()->with('error', "🛑 Impossible de supprimer : $equipementsLies équipement(s) sont encore rattachés à ce bâtiment.");
        }

        // Structure : Locaux et Lieux publics rattachés
        $locauxLies = DB::table('local_')->where('id_batiment', $id)->count();
        $lieuxLies = DB::table('lieux_publics')->where('id_batiment', $id)->count();

        if ($locauxLies > 0 || $lieuxLies > 0) {
            return redirect()->back()->with('error', "🛑 Impossible de supprimer : Ce bâtiment contient encore $locauxLies local/locaux et $lieuxLies lieu(x) public(s). Vous devez les supprimer ou les réaffecter d'abord.");
        }

        DB::table('batiment')->where('id_batiment', $id)->delete();

        return redirect()->route('batiments.index')
            ->with('success', '✅ Le bâtiment a été retiré du patrimoine communal avec succès.');
    }
}
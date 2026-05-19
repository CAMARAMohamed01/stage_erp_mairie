<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LieuController extends Controller
{
    // --- LISTE DES LIEUX PUBLICS ---
    public function index()
    {
        $lieux = DB::table('lieux_publics')
            // Jointures obligatoires pour la localisation cadastrale
            ->join('parcelle', 'lieux_publics.id_parcelle', '=', 'parcelle.id_parcelle')
            ->join('lieu_dit', 'parcelle.id_lieu_dit', '=', 'lieu_dit.id_lieu_dit')
            // Jointures optionnelles pour le bâtiment et son adresse
            ->leftJoin('batiment', 'lieux_publics.id_batiment', '=', 'batiment.id_batiment')
            ->leftJoin('Adresse', 'batiment.id_adresse', '=', 'Adresse.id_adresse')
            ->select(
                'lieux_publics.*',
                'batiment.nom_bat',
                'parcelle.section_cadastrale',
                'parcelle.num_parcelle',
                'lieu_dit.nom_lieu_dit',
                'Adresse.num_rue',
                'Adresse.nom_voie',
                'Adresse.ville'
            )
            ->orderBy('lieux_publics.nom_lieu')
            ->get();

        return view('lieux.index', compact('lieux'));
    }
    // --- FORMULAIRE D'AJOUT ---
    public function create()
    {
        // Référentiels pour les listes déroulantes
        $immos = DB::table('immobilisation_inventaire_')->orderBy('num_inventaire')->get();
        $decisions = DB::table('decision_administratif')->orderByDesc('date_decision')->get();
        $tiers = DB::table('tiers')->get(); // Pour simplifier, tu pourrais faire un leftJoin comme dans BatimentController
        $batiments = DB::table('batiment')->orderBy('nom_bat')->get();
        $types_erp = DB::table('type_erp')->get();

        // Pour les lieux publics, on veut afficher les parcelles avec leur adresse pour que ce soit lisible
        $parcelles = DB::table('parcelle')
            ->join('lieu_dit', 'parcelle.id_lieu_dit', '=', 'lieu_dit.id_lieu_dit')
            ->select('parcelle.*', 'lieu_dit.nom_lieu_dit')
            ->orderBy('parcelle.section_cadastrale')
            ->get();

        return view('lieux.create', compact('immos', 'decisions', 'tiers', 'batiments', 'types_erp', 'parcelles'));
    }

    // --- SAUVEGARDE EN BASE ---
    public function store(Request $request)
    {
        $request->validate([
            'nom_lieu' => 'required|string|max:80',
            'typologie_lieu' => 'nullable|string|max:80',
            'surface_m2' => 'nullable|numeric',
            'horaire_ouverture' => 'nullable|date_format:H:i',
            'horaire_fermeture' => 'nullable|date_format:H:i',

            // Clés étrangères
            'id_immo' => 'nullable|integer|exists:immobilisation_inventaire_,id_immo',
            'id_decision_reglement' => 'nullable|integer|exists:decision_administratif,id_decision',
            'id_tiers' => 'nullable|integer|exists:tiers,id_tiers',
            'id_batiment' => 'nullable|integer|exists:batiment,id_batiment',
            'id_type_erp' => 'nullable|integer|exists:type_erp,id_type_erp',
            'id_parcelle' => 'required|integer|exists:parcelle,id_parcelle', // Le DDL dit NOT NULL !
        ]);

        DB::table('lieux_publics')->insert([
            'nom_lieu' => $request->nom_lieu,
            'typologie_lieu' => $request->typologie_lieu,
            'surface_m2' => $request->surface_m2,
            // Formatage des heures pour PostgreSQL (on rajoute les secondes :00)
            'horaire_ouverture' => $request->horaire_ouverture ? $request->horaire_ouverture . ':00' : null,
            'horaire_fermeture' => $request->horaire_fermeture ? $request->horaire_fermeture . ':00' : null,

            'id_immo' => $request->id_immo,
            'id_decision_reglement' => $request->id_decision_reglement,
            'id_tiers' => $request->id_tiers,
            'id_batiment' => $request->id_batiment,
            'id_type_erp' => $request->id_type_erp,
            'id_parcelle' => $request->id_parcelle,
        ]);

        return redirect()->route('lieux.index')
            ->with('success', 'Le nouvel espace public a été enregistré dans le patrimoine.');
    }

    // --- FICHE DÉTAILLÉE DU LIEU ---
    public function show($id)
    {
        $lieu = DB::table('lieux_publics')
            ->join('parcelle', 'lieux_publics.id_parcelle', '=', 'parcelle.id_parcelle')
            ->join('lieu_dit', 'parcelle.id_lieu_dit', '=', 'lieu_dit.id_lieu_dit')
            ->leftJoin('batiment', 'lieux_publics.id_batiment', '=', 'batiment.id_batiment')
            ->leftJoin('type_erp', 'lieux_publics.id_type_erp', '=', 'type_erp.id_type_erp')
            ->leftJoin('immobilisation_inventaire_', 'lieux_publics.id_immo', '=', 'immobilisation_inventaire_.id_immo')
            ->leftJoin('decision_administratif', 'lieux_publics.id_decision_reglement', '=', 'decision_administratif.id_decision')
            ->select(
                'lieux_publics.*',
                'batiment.nom_bat',
                'parcelle.section_cadastrale',
                'parcelle.num_parcelle',
                'lieu_dit.nom_lieu_dit',
                'type_erp.categorie_erp',
                'type_erp.type_erp as code_erp',
                'immobilisation_inventaire_.num_inventaire',
                'immobilisation_inventaire_.libelle_comptable',
                'decision_administratif.numero_decision',
                'decision_administratif.date_decision',
            )
            ->where('lieux_publics.id_lieu', $id)
            ->first();

        if (!$lieu)
            abort(404, 'Espace public introuvable');

        // 1. Les locaux rattachés directement à cet espace (ex: buvette de parc)
        $locaux = DB::table('local_')
            ->leftJoin('type_usage', 'local_.id_usage', '=', 'type_usage.id_usage')
            ->select('local_.*', 'type_usage.libelle_usage')
            ->where('id_lieu', $id)
            ->get();

        // 2. Les équipements en extérieur (bancs, jeux, poubelles...)
        $equipements = DB::table('equipement')->where('id_lieu', $id)->get();

        // 3. Le patrimoine végétal (arbres, massifs)
        $vegetaux = DB::table('patrimoine_vegetal')->where('id_lieu', $id)->get();

        // 4. Les plans d'entretien paysager
        $plans_entretien = DB::table('plan_entretien_vert')
            ->join('type_tache_verte', 'plan_entretien_vert.id_tache_verte', '=', 'type_tache_verte.id_tache_verte')
            ->where('id_lieu', $id)
            ->get();

        // 5. Emplacements funéraires (si c'est un cimetière)
        $emplacements = DB::table('emplacement_funeraire')->where('id_lieu', $id)->get();

        $controles = DB::table('controle_reglementaire')
            ->where('id_lieu', $id)
            ->orderBy('designation')
            ->get();

        // Ajout de 'controles' dans le compact :
        return view('lieux.show', compact('lieu', 'locaux', 'equipements', 'vegetaux', 'plans_entretien', 'emplacements', 'controles'));
    }

    // --- FORMULAIRE DE MODIFICATION ---
    public function edit($id)
    {
        $lieu = DB::table('lieux_publics')->where('id_lieu', $id)->first();
        if (!$lieu)
            abort(404);

        $immos = DB::table('immobilisation_inventaire_')->orderBy('num_inventaire')->get();
        $decisions = DB::table('decision_administratif')->orderByDesc('date_decision')->get();
        $batiments = DB::table('batiment')->orderBy('nom_bat')->get();
        $types_erp = DB::table('type_erp')->get();
        $parcelles = DB::table('parcelle')
            ->join('lieu_dit', 'parcelle.id_lieu_dit', '=', 'lieu_dit.id_lieu_dit')
            ->select('parcelle.*', 'lieu_dit.nom_lieu_dit')
            ->orderBy('parcelle.section_cadastrale')
            ->get();

        return view('lieux.edit', compact('lieu', 'immos', 'decisions', 'batiments', 'types_erp', 'parcelles'));
    }

    // --- MISE À JOUR ---
    public function update(Request $request, $id)
    {
        $request->validate([
            'nom_lieu' => 'required|string|max:80',
            'typologie_lieu' => 'nullable|string|max:80',
            'surface_m2' => 'nullable|numeric',
            'horaire_ouverture' => 'nullable',
            'horaire_fermeture' => 'nullable',
            'id_immo' => 'nullable|integer|exists:immobilisation_inventaire_,id_immo',
            'id_decision_reglement' => 'nullable|integer|exists:decision_administratif,id_decision',
            'id_batiment' => 'nullable|integer|exists:batiment,id_batiment',
            'id_type_erp' => 'nullable|integer|exists:type_erp,id_type_erp',
            'id_parcelle' => 'required|integer|exists:parcelle,id_parcelle',
        ]);

        DB::table('lieux_publics')->where('id_lieu', $id)->update([
            'nom_lieu' => $request->nom_lieu,
            'typologie_lieu' => $request->typologie_lieu,
            'surface_m2' => $request->surface_m2,
            // Nettoyage de la date si seulement HH:MM est envoyé
            'horaire_ouverture' => $request->horaire_ouverture ? (strlen($request->horaire_ouverture) == 5 ? $request->horaire_ouverture . ':00' : $request->horaire_ouverture) : null,
            'horaire_fermeture' => $request->horaire_fermeture ? (strlen($request->horaire_fermeture) == 5 ? $request->horaire_fermeture . ':00' : $request->horaire_fermeture) : null,
            'id_immo' => $request->id_immo,
            'id_decision_reglement' => $request->id_decision_reglement,
            'id_batiment' => $request->id_batiment,
            'id_type_erp' => $request->id_type_erp,
            'id_parcelle' => $request->id_parcelle,
        ]);

        return redirect()->route('lieux.show', $id)->with('success', 'Les informations du lieu ont été mises à jour.');
    }

    // --- SUPPRESSION SÉCURISÉE ---
    public function destroy($id)
    {
        // Vérification des dépendances multiples
        $locaux = DB::table('local_')->where('id_lieu', $id)->count();
        $equipements = DB::table('equipement')->where('id_lieu', $id)->count();
        $vegetaux = DB::table('patrimoine_vegetal')->where('id_lieu', $id)->count();
        $emplacements = DB::table('emplacement_funeraire')->where('id_lieu', $id)->count();
        $plans = DB::table('plan_entretien_vert')->where('id_lieu', $id)->count();
        $interventions = DB::table('intervention_espace')->where('id_lieu', $id)->count();

        if ($locaux > 0 || $equipements > 0 || $vegetaux > 0 || $emplacements > 0 || $plans > 0 || $interventions > 0) {
            return redirect()->back()->with('error', "🛑 Impossible de supprimer ce lieu. Il contient encore des éléments rattachés (Locaux: $locaux, Équipements: $equipements, Végétaux: $vegetaux, Emplacements: $emplacements, Entretien: $plans, Interventions: $interventions).");
        }

        DB::table('lieux_publics')->where('id_lieu', $id)->delete();
        return redirect()->route('lieux.index')->with('success', 'Le lieu a été retiré du référentiel.');
    }
}
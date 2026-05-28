<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Contrat;
use App\Models\LieuPublic;
class LieuController extends Controller
{
    // --- LISTE DES LIEUX PUBLICS ---
    public function index(Request $request)
    {
        // 1. Initialisation de la requête avec les jointures
        $query = DB::table('lieux_publics')
            ->join('parcelle', 'lieux_publics.id_parcelle', '=', 'parcelle.id_parcelle')
            ->join('lieu_dit', 'parcelle.id_lieu_dit', '=', 'lieu_dit.id_lieu_dit')
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
            );

        // 2. Application du filtre si présent
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('lieux_publics.nom_lieu', 'ilike', '%' . $search . '%')
                    ->orWhere('Adresse.nom_voie', 'ilike', '%' . $search . '%')
                    ->orWhere('lieu_dit.nom_lieu_dit', 'ilike', '%' . $search . '%');
            });
        }

        // 3. Exécution
        $lieux = $query->orderBy('lieux_publics.nom_lieu')->get();

        return view('lieux.index', compact('lieux'));
    }
    // --- FORMULAIRE D'AJOUT ---
    public function create()
    {
        // Référentiels pour les listes déroulantes
        $immos = DB::table('immobilisation_inventaire_')->orderBy('num_inventaire')->get();
        $decisions = DB::table('decision_administratif')->orderByDesc('date_decision')->get();
        $tiers = DB::table('tiers')->get();
        $batiments = DB::table('batiment')->orderBy('nom_bat')->get();
        $types_erp = DB::table('type_erp')->get();

        $contrats = Contrat::orderBy('numero_contrat')->get();

        // Pour les lieux publics, on veut afficher les parcelles avec leur adresse pour que ce soit lisible
        $parcelles = DB::table('parcelle')
            ->join('lieu_dit', 'parcelle.id_lieu_dit', '=', 'lieu_dit.id_lieu_dit')
            ->select('parcelle.*', 'lieu_dit.nom_lieu_dit')
            ->orderBy('parcelle.section_cadastrale')
            ->get();

        return view('lieux.create', compact('immos', 'decisions', 'tiers', 'batiments', 'types_erp', 'parcelles', 'contrats'));
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
            'id_parcelle' => 'required|integer|exists:parcelle,id_parcelle',

            // Validation du tableau des contrats
            'id_contrats' => 'nullable|array',
            'id_contrats.*' => 'exists:contrat,id_contrat',
            'geojson_data' => 'nullable|json'
        ]);

        // 1. On utilise le modèle Eloquent pour créer l'enregistrement principal
        $lieu = LieuPublic::create([
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

        // 2. On attache les contrats via la relation Many-to-Many
        if ($request->has('id_contrats')) {
            $lieu->contratsAdministratifs()->attach($request->id_contrats);
        }
        if ($request->filled('geojson_data')) {
            DB::update(
                "UPDATE lieux_publics 
             SET geom_lieu = ST_SetSRID(ST_GeomFromGeoJSON(?), 4326) 
             WHERE id_lieu = ?",
                [$request->geojson_data, $lieu->id_lieu]
            );
        }

        return redirect()->route('lieux.index')
            ->with('success', 'Le nouvel espace public a été enregistré dans le patrimoine.');
    }

    // --- FORMULAIRE DE MODIFICATION ---
    public function edit($id)
    {
        //  Ajout du selectRaw pour récupérer le GeoJSON du point GPS
        $lieu = LieuPublic::with('contratsAdministratifs')
            ->select('*', DB::raw('ST_AsGeoJSON(geom_lieu) as geojson_lieu'))
            ->findOrFail($id);

        $immos = DB::table('immobilisation_inventaire_')->orderBy('num_inventaire')->get();
        $decisions = DB::table('decision_administratif')->orderByDesc('date_decision')->get();
        $batiments = DB::table('batiment')->orderBy('nom_bat')->get();
        $types_erp = DB::table('type_erp')->get();

        $contrats = Contrat::orderBy('numero_contrat')->get();

        $parcelles = DB::table('parcelle')
            ->join('lieu_dit', 'parcelle.id_lieu_dit', '=', 'lieu_dit.id_lieu_dit')
            ->select('parcelle.*', 'lieu_dit.nom_lieu_dit')
            ->orderBy('parcelle.section_cadastrale')
            ->get();

        return view('lieux.edit', compact('lieu', 'immos', 'decisions', 'batiments', 'types_erp', 'parcelles', 'contrats'));
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

            // Validation du tableau des contrats
            'id_contrats' => 'nullable|array',
            'id_contrats.*' => 'exists:contrat,id_contrat',
            'geojson_data' => 'nullable|json',
        ]);


        $lieu = LieuPublic::findOrFail($id);

        // 1. Mise à jour des colonnes de la table lieux_publics
        $lieu->update([
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


        // 2. Synchronisation des contrats dans la table pivot
        $lieu->contratsAdministratifs()->sync($request->id_contrats ?? []);
        if ($request->filled('geojson_data')) {
            DB::update(
                "UPDATE lieux_publics 
                 SET geom_lieu = ST_SetSRID(ST_GeomFromGeoJSON(?), 4326) 
                 WHERE id_lieu = ?",
                [$request->geojson_data, $lieu->id_lieu]
            );
        }

        return redirect()->route('lieux.show', $id)
            ->with('success', 'Les informations du lieu ont été mises à jour.');
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
                DB::raw('ST_AsGeoJSON(lieux_publics.geom_lieu) as geojson_lieu'), //  Point GPS du lieu
                DB::raw('ST_AsGeoJSON(parcelle.geom_parcelle) as geojson_parcelle') // Polygone parcelle
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

        $compteurs = DB::table('compteur')
            ->join('local_', 'compteur.id_local', '=', 'local_.id_local')
            ->where('local_.id_lieu', $id)
            ->select('compteur.*', 'local_.nom_local')
            ->get();

        $documents = DB::table('document')
            ->where('id_lieu', $id)
            ->orderByDesc('date_upload')
            ->get();
        //Les équipements en extérieur (bancs, jeux, poubelles...)
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

        $controles = collect(); // Par défaut une collection vide
        if ($lieu->id_type_erp) {
            $controles = DB::table('controle_reglementaire')
                ->join('type_erp_controle', 'controle_reglementaire.id_controle', '=', 'type_erp_controle.id_controle')
                ->where('type_erp_controle.id_type_erp', $lieu->id_type_erp)
                ->orderBy('controle_reglementaire.designation')
                ->select('controle_reglementaire.*')
                ->get();
        }

        // Ajout de 'controles' dans le compact :
        return view('lieux.show', compact('lieu', 'locaux', 'equipements', 'vegetaux', 'plans_entretien', 'emplacements', 'controles', 'compteurs', 'documents'));
    }


    public function destroy($id)
    {
        try {
            // On lance une transaction : si une seule suppression échoue, on annule tout pour ne pas corrompre la base
            DB::beginTransaction();

            $lieu = LieuPublic::findOrFail($id);

            // 1. Détacher les contrats de la table pivot
            $lieu->contratsAdministratifs()->detach();

            // 2. Gestion des sous-dépendances (ex: Compteurs liés aux locaux de ce lieu)
            $locauxIds = DB::table('local_')->where('id_lieu', $id)->pluck('id_local');
            if ($locauxIds->isNotEmpty()) {
                // On supprime d'abord les compteurs rattachés aux locaux de ce lieu
                DB::table('compteur')->whereIn('id_local', $locauxIds)->delete();
                // S'il y avait d'autres choses rattachées au local (ex: equipements de local), il faudrait les ajouter ici
            }

            // Nettoyage de toutes les tables liées directement à id_lieu
            //DB::table('controle_reglementaire')->where('id_lieu', $id)->delete(); 
            DB::table('document')->where('id_lieu', $id)->delete();
            DB::table('equipement')->where('id_lieu', $id)->update(['id_lieu' => null]);
            DB::table('patrimoine_vegetal')->where('id_lieu', $id)->delete();
            DB::table('emplacement_funeraire')->where('id_lieu', $id)->delete();
            DB::table('plan_entretien_vert')->where('id_lieu', $id)->delete();
            DB::table('intervention_espace')->where('id_lieu', $id)->delete();

            // 4. Maintenant on peut supprimer les locaux
            DB::table('local_')->where('id_lieu', $id)->delete();

            // 5. Et enfin, on supprime le lieu lui-même
            $lieu->delete();

            // On valide toutes les requêtes
            DB::commit();

            return redirect()->route('lieux.index')
                ->with('success', '✅ Le lieu public et l\'intégralité de ses éléments rattachés ont été supprimés avec succès.');

        } catch (\Illuminate\Database\QueryException $e) {
            // Si PostgreSQL bloque la suppression, on annule et on affiche l'erreur exacte
            DB::rollBack();

            // On extrait le message d'erreur SQL pour qu'il soit lisible
            return redirect()->back()->with('error', "🛑 Échec de la suppression à cause d'une contrainte de base de données. Détail technique : " . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', "🛑 Erreur inattendue : " . $e->getMessage());
        }
    }

    public function uploadDocument(Request $request, $idLieu)
    {
        $request->validate([
            'fichier' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120', // Max 5 Mo
        ]);

        $file = $request->file('fichier');
        $path = $file->store('documents/lieux', 'public');

        \App\Models\Document::create([
            'nom_fichier' => $file->getClientOriginalName(),
            'type_doc' => $file->getClientOriginalExtension(),
            'chemin_stockage' => $path,
            'taille_ko' => $file->getSize() / 1024,
            'date_upload' => now(),
            'id_lieu' => $idLieu, // La clé étrangère cible l'espace public
        ]);

        return back()->with('success', 'Le document a été ajouté à l\'espace public avec succès.');
    }
}
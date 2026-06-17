<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Contrat;
use App\Models\LieuPublic;

class LieuController extends Controller
{
    public function index(Request $request)
    {
        // 1. On utilise Eloquent pour gérer la relation N:N proprement sans doublons
        $query = LieuPublic::with(['parcelles.lieuDit', 'batiment', 'adresse', 'typeErp', 'contratsAdministratifs']);

        // 2. Application du filtre si présent
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nom_lieu', 'ilike', '%' . $search . '%')
                    ->orWhereHas('adresse', function ($qAdr) use ($search) {
                        $qAdr->where('nom_voie', 'ilike', '%' . $search . '%');
                    })
                    ->orWhereHas('parcelles.lieuDit', function ($qLieu) use ($search) {
                        $qLieu->where('nom_lieu_dit', 'ilike', '%' . $search . '%');
                    });
            });
        }

        $lieux = $query->orderBy('nom_lieu')->get();

        return view('lieux.index', compact('lieux'));
    }

    public function create()
    {
        $immos = DB::table('immobilisation_inventaire_')->orderBy('num_inventaire')->get();
        $decisions = DB::table('decision_administratif')->orderByDesc('date_decision')->get();
        $tiers = DB::table('tiers')->get();
        $batiments = DB::table('batiment')->orderBy('nom_bat')->get();
        $types_erp = DB::table('type_erp')->get();
        $contrats = Contrat::orderBy('numero_contrat')->get();
        $adresses = DB::table('Adresse')->orderBy('nom_voie')->get(); // NOUVEAU

        $parcelles = DB::table('parcelle')
            ->leftJoin('lieu_dit', 'parcelle.id_lieu_dit', '=', 'lieu_dit.id_lieu_dit')
            ->select('parcelle.*', 'lieu_dit.nom_lieu_dit')
            ->orderBy('parcelle.section_cadastrale')
            ->get();

        return view('lieux.create', compact('immos', 'decisions', 'tiers', 'batiments', 'types_erp', 'parcelles', 'contrats', 'adresses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom_lieu' => 'required|string|max:80',
            'typologie_lieu' => 'nullable|string|max:80',
            'surface_m2' => 'nullable|numeric',
            'horaire_ouverture' => 'nullable|date_format:H:i',
            'horaire_fermeture' => 'nullable|date_format:H:i',

            'id_immo' => 'nullable|integer|exists:immobilisation_inventaire_,id_immo',
            'id_decision_reglement' => 'nullable|integer|exists:decision_administratif,id_decision',
            'id_tiers' => 'nullable|integer|exists:tiers,id_tiers',
            'id_batiment' => 'nullable|integer|exists:batiment,id_batiment',
            'id_type_erp' => 'nullable|integer|exists:type_erp,id_type_erp',
            'id_adresse' => 'nullable|integer|exists:Adresse,id_adresse', // NOUVEAU

            'id_contrats' => 'nullable|array',
            'id_contrats.*' => 'exists:contrat,id_contrat',
            // NOUVEAU : Tableau des parcelles
            'parcelles' => 'nullable|array',
            'parcelles.*' => 'exists:parcelle,id_parcelle',

            'geojson_data' => 'nullable|json'
        ]);

        $lieu = LieuPublic::create([
            'nom_lieu' => $request->nom_lieu,
            'typologie_lieu' => $request->typologie_lieu,
            'surface_m2' => $request->surface_m2,
            'horaire_ouverture' => $request->horaire_ouverture ? $request->horaire_ouverture . ':00' : null,
            'horaire_fermeture' => $request->horaire_fermeture ? $request->horaire_fermeture . ':00' : null,
            'id_immo' => $request->id_immo,
            'id_decision_reglement' => $request->id_decision_reglement,
            'id_tiers' => $request->id_tiers,
            'id_batiment' => $request->id_batiment,
            'id_type_erp' => $request->id_type_erp,
            'id_adresse' => $request->id_adresse,
        ]);

        if ($request->has('id_contrats')) {
            $lieu->contratsAdministratifs()->attach($request->id_contrats);
        }
        if ($request->has('parcelles')) {
            $lieu->parcelles()->attach($request->parcelles);
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

    public function edit($id)
    {
        $lieu = LieuPublic::with(['contratsAdministratifs', 'parcelles'])
            ->select('*', DB::raw('ST_AsGeoJSON(geom_lieu) as geojson_lieu'))
            ->findOrFail($id);

        $immos = DB::table('immobilisation_inventaire_')->orderBy('num_inventaire')->get();
        $decisions = DB::table('decision_administratif')->orderByDesc('date_decision')->get();
        $batiments = DB::table('batiment')->orderBy('nom_bat')->get();
        $types_erp = DB::table('type_erp')->get();
        $contrats = Contrat::orderBy('numero_contrat')->get();
        $adresses = DB::table('Adresse')->orderBy('nom_voie')->get();

        $parcelles = DB::table('parcelle')
            ->leftJoin('lieu_dit', 'parcelle.id_lieu_dit', '=', 'lieu_dit.id_lieu_dit')
            ->select('parcelle.*', 'lieu_dit.nom_lieu_dit')
            ->orderBy('parcelle.section_cadastrale')
            ->get();

        return view('lieux.edit', compact('lieu', 'immos', 'decisions', 'batiments', 'types_erp', 'parcelles', 'contrats', 'adresses'));
    }

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
            'id_adresse' => 'nullable|integer|exists:Adresse,id_adresse',

            'id_contrats' => 'nullable|array',
            'id_contrats.*' => 'exists:contrat,id_contrat',
            'parcelles' => 'nullable|array',
            'parcelles.*' => 'exists:parcelle,id_parcelle',
            'geojson_data' => 'nullable|json',
        ]);

        $lieu = LieuPublic::findOrFail($id);

        $lieu->update([
            'nom_lieu' => $request->nom_lieu,
            'typologie_lieu' => $request->typologie_lieu,
            'surface_m2' => $request->surface_m2,
            'horaire_ouverture' => $request->horaire_ouverture ? (strlen($request->horaire_ouverture) == 5 ? $request->horaire_ouverture . ':00' : $request->horaire_ouverture) : null,
            'horaire_fermeture' => $request->horaire_fermeture ? (strlen($request->horaire_fermeture) == 5 ? $request->horaire_fermeture . ':00' : $request->horaire_fermeture) : null,
            'id_immo' => $request->id_immo,
            'id_decision_reglement' => $request->id_decision_reglement,
            'id_batiment' => $request->id_batiment,
            'id_type_erp' => $request->id_type_erp,
            'id_adresse' => $request->id_adresse,
        ]);

        $lieu->contratsAdministratifs()->sync($request->id_contrats ?? []);
        $lieu->parcelles()->sync($request->parcelles ?? []); // Synchronisation N:N

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

    public function show($id)
    {
        // 1. Récupération optimisée du lieu via DB Builder (avec son adresse)
        $lieu = DB::table('lieux_publics')
            ->leftJoin('batiment', 'lieux_publics.id_batiment', '=', 'batiment.id_batiment')
            ->leftJoin('type_erp', 'lieux_publics.id_type_erp', '=', 'type_erp.id_type_erp')
            ->leftJoin('immobilisation_inventaire_', 'lieux_publics.id_immo', '=', 'immobilisation_inventaire_.id_immo')
            ->leftJoin('decision_administratif', 'lieux_publics.id_decision_reglement', '=', 'decision_administratif.id_decision')
            ->leftJoin('Adresse', 'lieux_publics.id_adresse', '=', 'Adresse.id_adresse') // Jointure ajoutée
            ->select(
                'lieux_publics.*',
                'batiment.nom_bat',
                'type_erp.categorie_erp',
                'type_erp.type_erp as code_erp',
                'immobilisation_inventaire_.num_inventaire',
                'immobilisation_inventaire_.libelle_comptable',
                'decision_administratif.numero_decision',
                'decision_administratif.date_decision',
                'Adresse.num_rue',
                'Adresse.nom_voie',
                'Adresse.code_postal',
                'Adresse.ville',
                DB::raw('ST_AsGeoJSON(lieux_publics.geom_lieu) as geojson_lieu')
            )
            ->where('lieux_publics.id_lieu', $id)
            ->first();

        if (!$lieu)
            abort(404, 'Espace public introuvable');

        // Récupération des parcelles associées
        $parcelles = DB::table('espace_parcelle')
            ->join('parcelle', 'espace_parcelle.id_parcelle', '=', 'parcelle.id_parcelle')
            ->leftJoin('lieu_dit', 'parcelle.id_lieu_dit', '=', 'lieu_dit.id_lieu_dit')
            ->where('espace_parcelle.id_lieu', $id)
            ->select('parcelle.*', 'lieu_dit.nom_lieu_dit', DB::raw('ST_AsGeoJSON(parcelle.geom_parcelle) as geojson_parcelle'))
            ->get();

        $locaux = DB::table('local_')->where('id_lieu', $id)->get();

        $compteurs = DB::table('compteur')
            ->join('local_', 'compteur.id_local', '=', 'local_.id_local')
            ->where('local_.id_lieu', $id)
            ->select('compteur.*', 'local_.nom_local')
            ->get();

        $documents = DB::table('document')->where('id_lieu', $id)->orderByDesc('date_upload')->get();
        $equipements = DB::table('equipement')->where('id_lieu', $id)->get();
        $vegetaux = DB::table('patrimoine_vegetal')->where('id_lieu', $id)->get();

        $plans_entretien = DB::table('plan_entretien_vert')
            ->join('type_tache_verte', 'plan_entretien_vert.id_tache_verte', '=', 'type_tache_verte.id_tache_verte')
            ->where('id_lieu', $id)
            ->get();

        $emplacements = DB::table('emplacement_funeraire')->where('id_lieu', $id)->get();

        $controles = collect();
        if ($lieu->id_type_erp) {
            $controles = DB::table('controle_reglementaire')
                ->join('type_erp_controle', 'controle_reglementaire.id_controle', '=', 'type_erp_controle.id_controle')
                ->where('type_erp_controle.id_type_erp', $lieu->id_type_erp)
                ->orderBy('controle_reglementaire.designation')
                ->select('controle_reglementaire.*')
                ->get();
        }

        // On passe les parcelles multiples à la vue
        return view('lieux.show', compact('lieu', 'parcelles', 'locaux', 'equipements', 'vegetaux', 'plans_entretien', 'emplacements', 'controles', 'compteurs', 'documents'));
    }

    public function destroy($id)
    {
        //dd($id);
        try {
            DB::beginTransaction();
            $lieu = LieuPublic::findOrFail($id);

            // 1. Détacher les relations Many-to-Many
            $lieu->contratsAdministratifs()->detach();
            $lieu->parcelles()->detach();

            DB::table('projet_lieu')->where('id_lieu', $id)->delete();
            DB::table('ouverture_lieu')->where('id_lieu', $id)->delete();
            DB::table('intervention_espace')->where('id_lieu', $id)->delete();

            // 2. Gestion des LOCAUX et de TOUS LEURS ENFANTS
            $locauxIds = DB::table('local_')->where('id_lieu', $id)->pluck('id_local');
            if ($locauxIds->isNotEmpty()) {
                // Suppressions dures
                DB::table('compteur')->whereIn('id_local', $locauxIds)->delete();
                DB::table('ouverture_local')->whereIn('id_local', $locauxIds)->delete();
                DB::table('contrat_local')->whereIn('id_local', $locauxIds)->delete();
                DB::table('document')->whereIn('id_local', $locauxIds)->delete();

                // Conservations (détachement)
                DB::table('action')->whereIn('id_local', $locauxIds)->update(['id_local' => null]);
                DB::table('intervention')->whereIn('id_local', $locauxIds)->update(['id_local' => null]);
                DB::table('equipement')->whereIn('id_local', $locauxIds)->update(['id_local' => null]);

                // Suppression des locaux
                DB::table('local_')->whereIn('id_local', $locauxIds)->delete();
            }

            // 3. CONSERVATION DE L'HISTORIQUE DU LIEU (Détachement)
            DB::table('action')->where('id_lieu', $id)->update(['id_lieu' => null]);
            DB::table('intervention')->where('id_lieu', $id)->update(['id_lieu' => null]);
            DB::table('equipement')->where('id_lieu', $id)->update(['id_lieu' => null]);

            // 4. SUPPRESSION DES DÉPENDANCES STRICTES DU LIEU
            DB::table('document')->where('id_lieu', $id)->delete();
            DB::table('patrimoine_vegetal')->where('id_lieu', $id)->delete();
            DB::table('emplacement_funeraire')->where('id_lieu', $id)->delete();
            DB::table('plan_entretien_vert')->where('id_lieu', $id)->delete();

            // 5. Supprimer le lieu lui-même (et son geom_lieu avec)
            $lieu->delete();

            DB::commit();

            return redirect()->route('lieux.index')->with('success', '✅ Le lieu public et ses dépendances directes ont été supprimés avec succès.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();

            // ANALYSE DE L'ERREUR POSTGRESQL
            $message = $e->getMessage();
            $tableBloquante = 'inconnue';

            // PostgreSQL renvoie souvent "violates foreign key constraint ... on table "nom_de_la_table""
            if (preg_match('/on table "([^"]+)"/', $message, $matches)) {
                $tableBloquante = $matches[1];
            }

            return redirect()->back()->with('error', "🛑 Suppression bloquée : Le lieu (ou l'un de ses locaux) est encore utilisé dans la table '{$tableBloquante}'. Il faut d'abord nettoyer cette table.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', "🛑 Erreur inattendue : " . $e->getMessage());
        }
    }

    public function uploadDocument(Request $request, $idLieu)
    {
        $request->validate([
            'fichier' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ]);

        $file = $request->file('fichier');
        $path = $file->store('documents/lieux', 'public');

        \App\Models\Document::create([
            'nom_fichier' => $file->getClientOriginalName(),
            'type_doc' => $file->getClientOriginalExtension(),
            'chemin_stockage' => $path,
            'taille_ko' => $file->getSize() / 1024,
            'date_upload' => now(),
            'id_lieu' => $idLieu,
        ]);

        return back()->with('success', 'Le document a été ajouté à l\'espace public avec succès.');
    }
}
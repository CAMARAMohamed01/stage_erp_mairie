<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Voie;
use App\Models\Troncon;
use App\Models\Zone;
use App\Models\Ouvrage;

class TronconController extends Controller
{
    public function create(Request $request)
    {
        // Permet de pré-sélectionner la voie si on vient de la page "show" d'une voie
        $selectedVoieId = $request->query('id_voie');

        $voies = Voie::all();
        $zones = Zone::all();
        $ouvrages = Ouvrage::all();

        return view('troncons.create', compact('selectedVoieId', 'voies', 'zones', 'ouvrages'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_voie' => 'required|exists:voie,id_voie',
            'numero_troncon' => 'required|string|max:150|unique:troncon,numero_troncon',
            'nom_portion' => 'nullable|string|max:100',

            'pk_debut' => 'nullable|numeric',
            'pk_fin' => 'nullable|numeric',
            'repere_physique_debut' => 'nullable|string|max:100',
            'repere_physique_fin' => 'nullable|string|max:100',

            'type_revetement' => 'nullable|string|max:50',
            'date_dernier_goudronnage' => 'nullable|date',
            'etat_physique' => 'nullable|string|max:50',
            'gabarit_accessibilite' => 'nullable|string|max:50',
            'paysage_environnement' => 'nullable|string|max:50',
            'geojson_data' => 'nullable|string',

            'id_zone' => 'nullable|exists:Zone,id_zone',
            'id_ouvrage_lie' => 'nullable|exists:ouvrage,id_ouvrage',
            'id_ouvrage_debut' => 'nullable|exists:ouvrage,id_ouvrage',
            'id_ouvrage_fin' => 'nullable|exists:ouvrage,id_ouvrage',
        ], [

            'numero_troncon.unique' => '⚠️ Ce numéro de tronçon existe déjà dans la base de données. Veuillez en choisir un autre.'
        ]);
        // Extraction et traitement de la géométrie
        $geojson = $validated['geojson_data'] ?? null;
        unset($validated['geojson_data']); // On le retire du tableau car la colonne n'existe pas sous ce nom

        if ($geojson) {
            // On sécurise les apostrophes pour le SQL, sans casser les guillemets du JSON
            $safeGeojson = str_replace("'", "''", $geojson);
            $validated['trace_geo'] = DB::raw("ST_SetSRID(ST_GeomFromGeoJSON('" . $safeGeojson . "'), 4326)");
        }
        $id = DB::table('troncon')->insertGetId($validated, 'id_troncon');

        return redirect()->route('troncons.show', $id)
            ->with('success', 'Le tronçon a été créé avec succès.');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'id_voie' => 'required|exists:voie,id_voie',
            // On ignore l'ID actuel pour la règle d'unicité lors de la modification
            'numero_troncon' => 'required|string|max:150|unique:troncon,numero_troncon,' . $id . ',id_troncon',
            'nom_portion' => 'nullable|string|max:100',

            'pk_debut' => 'nullable|numeric',
            'pk_fin' => 'nullable|numeric',
            'repere_physique_debut' => 'nullable|string|max:100',
            'repere_physique_fin' => 'nullable|string|max:100',

            'type_revetement' => 'nullable|string|max:50',
            'date_dernier_goudronnage' => 'nullable|date',
            'etat_physique' => 'nullable|string|max:50',
            'gabarit_accessibilite' => 'nullable|string|max:50',
            'paysage_environnement' => 'nullable|string|max:50',

            'id_zone' => 'nullable|exists:Zone,id_zone',
            'id_ouvrage_lie' => 'nullable|exists:ouvrage,id_ouvrage',
            'id_ouvrage_debut' => 'nullable|exists:ouvrage,id_ouvrage',
            'id_ouvrage_fin' => 'nullable|exists:ouvrage,id_ouvrage',
            'geojson_data' => 'nullable|string',
        ]);
        // Traitement de la géométrie
        $geojson = $validated['geojson_data'] ?? null;
        unset($validated['geojson_data']);
        if ($geojson) {
            // On sécurise les apostrophes pour le SQL, sans casser les guillemets du JSON
            $safeGeojson = str_replace("'", "''", $geojson);
            $validated['trace_geo'] = DB::raw("ST_SetSRID(ST_GeomFromGeoJSON('" . $safeGeojson . "'), 4326)");
        } else {
            $validated['trace_geo'] = null; // Si l'utilisateur efface le tracé sur la carte
        }

        DB::table('troncon')->where('id_troncon', $id)->update($validated);

        return redirect()->route('troncons.show', $id)
            ->with('success', 'Le tronçon a été mis à jour.');
    }
    public function show($id)
    {
        // 1. Récupération du tronçon avec ses dépendances (Voie, Zone, Ouvrage lié)
        $troncon = DB::table('troncon')
            ->leftJoin('voie', 'troncon.id_voie', '=', 'voie.id_voie')
            ->leftJoin('Zone', 'troncon.id_zone', '=', 'Zone.id_zone')
            ->leftJoin('ouvrage', 'troncon.id_ouvrage_lie', '=', 'ouvrage.id_ouvrage')
            ->select(
                'troncon.*',
                'voie.nom_voie',
                'Zone.nom_zone',
                'ouvrage.nom_ouvrage as nom_ouvrage_lie',
                DB::raw('ST_AsGeoJSON(trace_geo) as geojson')
            )
            ->where('id_troncon', $id)
            ->first();

        if (!$troncon)
            abort(404, 'Tronçon introuvable.');

        // 2. Les interventions liées à ce tronçon
        $interventions = DB::table('intervention')
            ->where('id_troncon', $id)
            ->orderByDesc('date_ouverture')
            ->get();
        $documents = DB::table('document')
            ->where('id_troncon', $id)
            ->orderByDesc('date_upload')
            ->get();
        // 3. Les équipements installés sur ce tronçon
        $equipements = DB::table('equipement')
            ->where('id_troncon', $id)
            ->orderBy('nom_equipement')
            ->get();

        return view('troncons.show', compact('troncon', 'interventions', 'documents', 'equipements'));
    }
    public function edit($id)
    {

        // 1. On récupère le tronçon à modifier
        $troncon = DB::table('troncon')->where('id_troncon', $id)->first();

        // Il faut extraire le tracé existant pour pouvoir le modifier sur la carte
        $troncon = DB::table('troncon')
            ->select('troncon.*', DB::raw('ST_AsGeoJSON(trace_geo) as geojson'))
            ->where('id_troncon', $id)
            ->first();

        if (!$troncon)
            abort(404, 'Tronçon introuvable.');

        // 2. On charge les listes pour les menus déroulants (select) du formulaire
        $voies = DB::table('voie')->orderBy('nom_voie')->get();
        $zones = DB::table('Zone')->orderBy('nom_zone')->get();
        $ouvrages = DB::table('ouvrage')->orderBy('nom_ouvrage')->get();

        // 3. On retourne la vue avec toutes les données
        return view('troncons.edit', compact('troncon', 'voies', 'zones', 'ouvrages'));
    }
    public function destroy($id)
    {
        // 1. On récupère le tronçon pour vérifier qu'il existe et garder l'ID de la voie pour la redirection
        $troncon = DB::table('troncon')->where('id_troncon', $id)->first();

        if (!$troncon)
            abort(404, 'Tronçon introuvable.');

        $idVoieParente = $troncon->id_voie;

        // --- DEBUT DE LA GESTION MANUELLE EN CASCADE ---

        //  Gérer les documents (Suppression physique des fichiers + base de données)
        $documents = DB::table('document')->where('id_troncon', $id)->get();
        foreach ($documents as $doc) {
            // On vérifie si le fichier existe sur le disque public et on le supprime
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($doc->chemin_stockage)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($doc->chemin_stockage);
            }
        }
        // Ensuite on supprime les lignes dans la table 'document'
        DB::table('document')->where('id_troncon', $id)->delete();

        // Gérer les équipements (Suppression des équipements liés à ce tronçon)
        DB::table('equipement')->where('id_troncon', $id)->delete();

        // Gérer les interventions (Suppression des interventions liées)
        DB::table('intervention')->where('id_troncon', $id)->delete();

        // --- FIN DE LA CASCADE ---


        DB::table('troncon')->where('id_troncon', $id)->delete();

        // 4. Redirection vers la fiche de la voie parente
        return redirect()->route('voies.show', $idVoieParente)
            ->with('success', 'Le tronçon ainsi que ses équipements, interventions et documents associés ont été définitivement supprimés.');
    }
    public function uploadDocument(Request $request, $idTroncon)
    {
        $request->validate([
            'fichier' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120', // Max 5 Mo
        ]);

        $file = $request->file('fichier');
        $path = $file->store('documents/troncons', 'public');

        DB::table('document')->insert([
            'nom_fichier' => $file->getClientOriginalName(),
            'type_doc' => $file->getClientOriginalExtension(),
            'chemin_stockage' => $path,
            'taille_ko' => $file->getSize() / 1024,
            'date_upload' => now(),
            'id_troncon' => $idTroncon,
        ]);

        return back()->with('success', 'Le document a été rattaché au tronçon avec succès.');
    }
}
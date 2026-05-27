<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Local;
use App\Models\Contrat;
use Illuminate\Support\Facades\DB;
class LocalController extends Controller
{
    // Afficher la liste de tous les locaux
    public function index(Request $request)
    {
        // 1. Initialisation de la requête avec les jointures
        $query = DB::table('local_')
            ->leftJoin('batiment', 'local_.id_batiment', '=', 'batiment.id_batiment')
            ->leftJoin('type_usage', 'local_.id_usage', '=', 'type_usage.id_usage')
            ->select('local_.*', 'batiment.nom_bat', 'type_usage.libelle_usage');

        // 2. Application du filtre si une recherche est effectuée
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('local_.nom_local', 'ilike', '%' . $search . '%')
                    ->orWhere('batiment.nom_bat', 'ilike', '%' . $search . '%')
                    ->orWhere('type_usage.libelle_usage', 'ilike', '%' . $search . '%');
            });
        }

        // 3. Tri final
        $locaux = $query->orderBy('batiment.nom_bat')
            ->orderBy('local_.niveau')
            ->orderBy('local_.nom_local')
            ->get();

        return view('locaux.index', compact('locaux'));
    }
    // Préparer le formulaire d'ajout
    public function create()
    {
        // On a besoin de la liste des bâtiments, des usages et des contrats d'assurance
        $batiments = DB::table('batiment')->orderBy('nom_bat')->get();
        $usages = DB::table('type_usage')->orderBy('libelle_usage')->get();

        // Optionnel: on récupère les lieux publics si un local dépend d'un lieu (ex: Cabanon dans un parc)
        $lieux = DB::table('lieux_publics')->orderBy('nom_lieu')->get();
        $contrats = Contrat::orderBy('numero_contrat')->get();


        return view('locaux.create', compact('batiments', 'usages', 'lieux', 'contrats'));
    }

    // --- SAUVEGARDER LE LOCAL EN BASE ---
    public function store(Request $request)
    {
        // 1. Validation stricte selon le schéma DDL
        $validated = $request->validate([
            'nom_local' => 'required|string|max:80',
            'largeur' => 'nullable|numeric',
            'longueur' => 'nullable|numeric',
            'surface_m2' => 'nullable|numeric',
            'niveau' => 'nullable|string|max:50',
            'statut_occupation' => 'nullable|string|max:50',
            'ref_article_assurance' => 'nullable|string|max:50',
            'prime_assurance_ttc' => 'nullable|numeric',
            'remarque' => 'nullable|string|max:255',
            'id_batiment' => 'nullable|integer|exists:batiment,id_batiment',
            'id_lieu' => 'nullable|integer|exists:lieux_publics,id_lieu',
            'id_usage' => 'nullable|integer|exists:type_usage,id_usage',
            'id_contrats' => 'nullable|array',
            'id_contrats.*' => 'exists:contrat,id_contrat',
        ]);
        $data = $validated;
        unset($data['id_contrats']);

        $local = Local::create($data);

        // 2. Règle métier : Un local doit appartenir SOIT à un bâtiment, SOIT à un lieu public (pas aucun des deux)
        if (empty($request->id_batiment) && empty($request->id_lieu)) {
            return back()->withInput()->with('error', 'Le local doit obligatoirement être rattaché à un Bâtiment ou à un Lieu public.');
        }
        if ($request->has('id_contrats')) {
            $local->contratsAdministratifs()->attach($request->id_contrats);
        }

        // // 3. Insertion dans la base
        // DB::table('local_')->insert([
        //     'nom_local' => $request->nom_local,
        //     'largeur' => $request->largeur,
        //     'longueur' => $request->longueur,
        //     'surface_m2' => $request->surface_m2,
        //     'niveau' => $request->niveau,
        //     'statut_occupation' => $request->statut_occupation,
        //     'ref_article_assurance' => $request->ref_article_assurance,
        //     'prime_assurance_ttc' => $request->prime_assurance_ttc,
        //     'remarque' => $request->remarque,
        //     'id_batiment' => $request->id_batiment,
        //     'id_lieu' => $request->id_lieu,
        //     'id_usage' => $request->id_usage,
        // ]);

        return redirect()->route('locaux.index')
            ->with('success', 'Le nouveau local a été ajouté au référentiel.');
    }
    // --- FICHE DÉTAILLÉE D'UN LOCAL ---
    public function show($id)
    {
        // 1. Infos du local, son usage, et son parent (Bâtiment ou Lieu)
        $local = DB::table('local_')
            ->leftJoin('batiment', 'local_.id_batiment', '=', 'batiment.id_batiment')
            ->leftJoin('lieux_publics', 'local_.id_lieu', '=', 'lieux_publics.id_lieu')
            ->leftJoin('type_usage', 'local_.id_usage', '=', 'type_usage.id_usage')
            ->select(
                'local_.*',
                'batiment.nom_bat',
                'lieux_publics.nom_lieu',
                'type_usage.libelle_usage'
            )
            ->where('id_local', $id)
            ->first();

        if (!$local) {
            abort(404, 'Local introuvable');
        }

        // 2. Équipements installés dans cette pièce précise
        $equipements = DB::table('equipement')
            ->where('id_local', $id)
            ->orderBy('nom_equipement')
            ->get();

        $documents = DB::table('document')
            ->where('id_local', $id)
            ->orderByDesc('date_upload')
            ->get();
        // 3. Compteurs (eau, gaz, électricité) liés à ce local
        $compteurs = DB::table('compteur')
            ->where('id_local', $id)
            ->orderBy('type_reseau')
            ->get();

        // 4. actions ouverts pour cette pièce
        $actions = DB::table('action')
            ->where('id_local', $id)
            ->where('statut_action', '!=', 'Clôturé')
            ->orderByDesc('date_creation')
            ->get();

        return view('locaux.show', compact('local', 'equipements', 'compteurs', 'actions', 'documents'));
    }

    // --- FORMULAIRE DE MODIFICATION ---
    public function edit($id)
    {
        $local = Local::with('contratsAdministratifs')->findOrFail($id);

        if (!$local) {
            abort(404, 'Local introuvable');
        }

        // Récupération des référentiels pour les listes déroulantes
        $batiments = DB::table('batiment')->orderBy('nom_bat')->get();
        $usages = DB::table('type_usage')->orderBy('libelle_usage')->get();
        $lieux = DB::table('lieux_publics')->orderBy('nom_lieu')->get();
        $contrats = Contrat::orderBy('numero_contrat')->get();

        return view('locaux.edit', compact('local', 'batiments', 'usages', 'lieux', 'contrats'));
    }

    // --- MISE À JOUR EN BASE ---
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nom_local' => 'required|string|max:80',
            'largeur' => 'nullable|numeric',
            'longueur' => 'nullable|numeric',
            'surface_m2' => 'nullable|numeric',
            'niveau' => 'nullable|string|max:50',
            'statut_occupation' => 'nullable|string|max:50',
            'ref_article_assurance' => 'nullable|string|max:50',
            'prime_assurance_ttc' => 'nullable|numeric',
            'remarque' => 'nullable|string|max:255',
            'id_batiment' => 'nullable|integer|exists:batiment,id_batiment',
            'id_lieu' => 'nullable|integer|exists:lieux_publics,id_lieu',
            'id_usage' => 'nullable|integer|exists:type_usage,id_usage',
            'id_contrats' => 'nullable|array',
            'id_contrats.*' => 'exists:contrat,id_contrat',
        ]);
        $local = Local::findOrFail($id);
        $data = $validated;
        unset($data['id_contrats']);
        if (empty($request->id_batiment) && empty($request->id_lieu)) {
            return back()->withInput()->with('error', 'Le local doit obligatoirement être rattaché à un Bâtiment ou à un Lieu public.');
        }
        $local->update($data);


        $local->contratsAdministratifs()->sync($request->id_contrats ?? []);
        return redirect()->route('locaux.show', $id)
            ->with('success', 'Les informations du local ont été mises à jour.');
    }

    // --- SUPPRESSION SÉCURISÉE (DATA INTEGRITY) ---
    public function destroy($id)
    {
        // 1. Vérifier la présence d'équipements dans ce local
        $equipements = DB::table('equipement')->where('id_local', $id)->count();
        if ($equipements > 0) {
            return redirect()->back()->with('error', "🛑 Suppression impossible : $equipements équipement(s) sont localisés dans cette pièce.");
        }

        // 2. Vérifier si des compteurs y sont installés
        $compteurs = DB::table('compteur')->where('id_local', $id)->count();
        if ($compteurs > 0) {
            return redirect()->back()->with('error', "🛑 Suppression impossible : Ce local abrite $compteurs compteur(s) de réseaux.");
        }

        // 3. Vérifier s'il y a des actions ou interventions liés à cette pièce
        $actions = DB::table('action')->where('id_local', $id)->count();
        $interventions = DB::table('intervention')->where('id_local', $id)->count();
        if ($actions > 0 || $interventions > 0) {
            return redirect()->back()->with('error', "🛑 Suppression impossible : Ce local est référencé dans $actions action(s) ou $interventions intervention(s).");
        }

        // 4. Si aucun blocage, exécution de la suppression
        DB::table('local_')->where('id_local', $id)->delete();

        return redirect()->route('locaux.index')
            ->with('success', '✅ Le local a été retiré de l\'inventaire communal.');
    }
    public function uploadDocument(Request $request, $idLocal)
    {
        $request->validate([
            'fichier' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120', // Max 5 Mo
        ]);

        $file = $request->file('fichier');
        $path = $file->store('documents/locaux', 'public');

        \App\Models\Document::create([
            'nom_fichier' => $file->getClientOriginalName(),
            'type_doc' => $file->getClientOriginalExtension(),
            'chemin_stockage' => $path,
            'taille_ko' => $file->getSize() / 1024,
            'date_upload' => now(),
            'id_local' => $idLocal, // La clé étrangère cible le local
        ]);

        return back()->with('success', 'Le document a été ajouté au local avec succès.');
    }
}
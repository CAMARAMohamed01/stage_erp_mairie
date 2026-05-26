<?php

namespace App\Http\Controllers;

use App\Models\Contrat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContratController extends Controller
{
    // Lancer la liste des contrats
    public function index(Request $request)
    {
        $query = Contrat::with('tiers'); // On charge le tiers prestataire

        // Petit filtre rapide par type de contrat si besoin
        if ($request->filled('type_contrat')) {
            $query->where('type_contrat', $request->type_contrat);
        }

        $contrats = $query->orderBy('date_debut_contrat', 'desc')->paginate(15);

        // On récupère les types uniques pour le filtre
        $typesContrat = Contrat::select('type_contrat')->distinct()->pluck('type_contrat');

        return view('contrats.index', compact('contrats', 'typesContrat'));
    }

    // Afficher le formulaire de création
    public function create()
    {
        // On récupère les Tiers (comme on l'a fait pour les interventions)
        $tiers = DB::table('tiers')
            ->leftJoin('tiers_physique', 'tiers.id_tiers', '=', 'tiers_physique.id_tiers')
            ->leftJoin('tiers_morale', 'tiers.id_tiers', '=', 'tiers_morale.id_tiers')
            ->select(
                'tiers.id_tiers',
                'tiers.type_tiers',
                'tiers_physique.nom_tiers',
                'tiers_physique.prenom_tiers',
                'tiers_morale.raison_sociale'
            )
            ->orderByRaw('COALESCE(tiers_morale.raison_sociale, tiers_physique.nom_tiers) ASC')
            ->get();

        return view('contrats.create', compact('tiers'));
    }
    public function store(Request $request)
    {
        // 1. Validation stricte basée sur ton schéma SQL
        $validated = $request->validate([
            'numero_contrat' => 'nullable|max:30|unique:contrat,numero_contrat',
            'type_contrat' => 'required|max:50',
            'objet_contrat' => 'nullable|max:255',
            'date_signature_contrat' => 'nullable|date',
            'date_debut_contrat' => 'required|date',
            'date_fin_contrat' => 'nullable|date|after_or_equal:date_debut_contrat',
            'prix_mois' => 'nullable|numeric|min:0',
            'prix_annuel' => 'nullable|numeric|min:0',
            'duree_mois' => 'nullable|integer|min:0',
            'modalite_renouvellement' => 'nullable|max:255',
            'preavis_resiliation_mois' => 'nullable|integer|min:0',
            'code_imputation' => 'nullable|max:20',
            'lot' => 'nullable|max:20',
            'code_analytique' => 'nullable|max:100',
            'frequence_facturation' => 'nullable|max:100',
            'mode_reglement' => 'nullable|max:50',
            'date_echeance' => 'nullable|date',
            'id_tiers' => 'required|exists:tiers,id_tiers',
        ]);

        // Gestion du booléen (si la checkbox n'est pas cochée, elle n'est pas envoyée)
        $validated['revision_prix_prevue'] = $request->has('revision_prix_prevue');

        // 2. Création de l'enregistrement
        Contrat::create($validated);

        return redirect()->route('contrats.index')
            ->with('success', 'Le contrat a été enregistré avec succès !');
    }

    public function show($id)
    {
        // 1. Infos de base + Jointures pour le Tiers (Prestataire)
        $contrat = Contrat::leftJoin('tiers', 'contrat.id_tiers', '=', 'tiers.id_tiers')
            ->leftJoin('tiers_physique', 'tiers.id_tiers', '=', 'tiers_physique.id_tiers')
            ->leftJoin('tiers_morale', 'tiers.id_tiers', '=', 'tiers_morale.id_tiers')
            ->select('contrat.*', 'tiers_physique.nom_tiers', 'tiers_physique.prenom_tiers', 'tiers_morale.raison_sociale')
            ->findOrFail($id);

        // 2. Périmètre d'application (Les liaisons N:M via Eloquent avec les infos "Location")
        // Grâce à ton modèle, ça remplace les 3 grosses requêtes manuelles !
        $equipementsLies = $contrat->equipementsCouverts;
        $locauxLies = $contrat->locauxCouverts;
        $lieuxLies = $contrat->lieuxCouverts;

        // 3. Spécifique aux formulaires d'ajout (pour les listes déroulantes)
        $equipementsDisponibles = DB::table('equipement')->orderBy('nom_equipement')->get();
        $decisions = DB::table('decision_administratif')->orderBy('numero_decision', 'desc')->get();

        // 4. On supprime $locations qui est maintenant obsolète (intégré dans $equipementsLies)

        return view('contrats.show', compact(
            'contrat',
            'equipementsLies',
            'locauxLies',
            'lieuxLies',
            'equipementsDisponibles',
            'decisions'
        ));
    }
    // ENREGISTRER UNE LIGNE DE LOCATION DE MATÉRIEL
    public function ajouterLocation(Request $request, $id_contrat)
    {
        $request->validate([
            'id_equipement' => 'required|exists:equipement,id_equipement',
            'id_decision' => 'required|exists:decision_administratif,id_decision',
            'quantite_louee' => 'required|integer|min:1',
            'etat_depart' => 'nullable|string|max:100',
            'date_debut_utilisation' => 'nullable|date',
            'date_fin_utilisation' => 'nullable|date|after_or_equal:date_debut_utilisation',
        ]);

        // Insertion via DB pour éviter les blocages de clés primaires composites de l'ORM
        DB::table('contrat_equipement')->insert([
            'id_contrat' => $id_contrat,
            'id_equipement' => $request->id_equipement,
            'id_decision' => $request->id_decision,
            'quantite_louee' => $request->quantite_louee,
            'etat_depart' => $request->etat_depart,
            'date_debut_utilisation' => $request->date_debut_utilisation,
            'date_fin_utilisation' => $request->date_fin_utilisation,
            'statut_ligne' => 'En cours'
        ]);
        return redirect()->back()->with('success', 'Équipement ajouté à la feuille de location.');
    }

    // AFFICHER LE FORMULAIRE DE MODIFICATION
    public function edit($id)
    {
        $contrat = Contrat::findOrFail($id);

        // Récupération des tiers pour la liste déroulante (identique au create)
        $tiers = DB::table('tiers')
            ->leftJoin('tiers_physique', 'tiers.id_tiers', '=', 'tiers_physique.id_tiers')
            ->leftJoin('tiers_morale', 'tiers.id_tiers', '=', 'tiers_morale.id_tiers')
            ->select('tiers.id_tiers', 'tiers_physique.nom_tiers', 'tiers_physique.prenom_tiers', 'tiers_morale.raison_sociale')
            ->orderByRaw('COALESCE(tiers_morale.raison_sociale, tiers_physique.nom_tiers) ASC')
            ->get();

        return view('contrats.edit', compact('contrat', 'tiers'));
    }

    // ENREGISTRER LES MODIFICATIONS
    public function update(Request $request, $id)
    {
        $contrat = Contrat::findOrFail($id);

        $validated = $request->validate([
            'numero_contrat' => 'nullable|max:30|unique:contrat,numero_contrat,' . $id . ',id_contrat',
            'type_contrat' => 'required|max:50',
            'objet_contrat' => 'nullable|max:255',
            'id_tiers' => 'required|exists:tiers,id_tiers',
            'date_signature_contrat' => 'nullable|date',
            'date_debut_contrat' => 'required|date',
            'date_fin_contrat' => 'nullable|date|after_or_equal:date_debut_contrat',
            'date_echeance' => 'nullable|date',
            'prix_mois' => 'nullable|numeric|min:0',
            'prix_annuel' => 'nullable|numeric|min:0',
            'frequence_facturation' => 'nullable|max:100',
            'mode_reglement' => 'nullable|max:50',
            'duree_mois' => 'nullable|integer|min:0',
            'preavis_resiliation_mois' => 'nullable|integer|min:0',
            'modalite_renouvellement' => 'nullable|max:255',
            'code_imputation' => 'nullable|max:20',
            'lot' => 'nullable|max:20',
            'code_analytique' => 'nullable|max:100',
        ]);

        $validated['revision_prix_prevue'] = $request->has('revision_prix_prevue');

        $contrat->update($validated);

        return redirect()->route('contrats.show', $contrat->id_contrat)
            ->with('success', 'Le contrat a été mis à jour avec succès.');
    }

    // SUPPRIMER LE CONTRAT
    public function destroy($id)
    {
        $contrat = Contrat::findOrFail($id);

        // Grâce au "ON DELETE CASCADE" dans ta base de données, 
        // les lignes dans contrat_equipement seront supprimées automatiquement.
        $contrat->delete();

        return redirect()->route('contrats.index')
            ->with('success', 'Le contrat a été définitivement supprimé.');
    }
}
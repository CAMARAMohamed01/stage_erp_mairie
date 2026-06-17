<?php

namespace App\Http\Controllers;
// Authentification controleur
use Illuminate\Support\Facades\Auth;
use App\Models\Intervention;
use Illuminate\Http\Request;
use App\Models\SuiviAction;
use App\Models\Equipement;
use App\Models\Categorie;
use App\Models\Local;
use App\Models\LieuPublic;
use App\Models\Batiment;
use App\Models\Utilisateur;
use App\Models\Tiers;
use Illuminate\Support\Facades\DB;
use App\Models\Contrat;
use App\Models\OperationComptable;
use App\Models\Projet;

class InterventionController extends Controller
{
    public function index(Request $request)
    {
        // dd(auth()->user()->can('check-permission', ['Interventions', 'lecture'])); // Debug pour vérifier les permissions
        // 1. On commence la requête
        $query = Intervention::with('categorie');


        // Filtre par statut existant
        if ($request->filled('statut') && $request->statut !== 'Tous') {
            // Attention au nom exact de ton champ, ici 'statut_global' selon tes vues
            $query->where('statut_global', $request->statut);
        }

        // Filtre textuel (ID ou description ou type)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'ilike', '%' . $search . '%')
                    ->orWhere('type_intervention', 'ilike', '%' . $search . '%');

                // Si la recherche est un nombre, on cherche aussi par ID
                if (is_numeric($search)) {
                    $q->orWhere('id_int', $search);
                }
            });
        }
        // 2. Si un statut est sélectionné dans le filtre, on ajoute une condition
        if ($request->has('statut') && $request->statut !== 'Tous') {
            $query->where('statut_global', $request->statut);
        }

        // 3. On récupère les résultats triés
        $interventions = $query->orderBy('date_ouverture', 'desc')->get();

        return view('interventions.index', compact('interventions'));
    }
    public function uploadDocument(Request $request, $idInt)
    {
        $request->validate([
            'fichier' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120', // Max 5 Mo
        ]);

        $file = $request->file('fichier');
        // On range ça dans un dossier spécifique aux interventions
        $path = $file->store('documents/interventions', 'public');

        \App\Models\Document::create([
            'nom_fichier' => $file->getClientOriginalName(),
            'type_doc' => $file->getClientOriginalExtension(),
            'chemin_stockage' => $path,
            'taille_ko' => $file->getSize() / 1024,
            'date_upload' => now(),
            'id_int' => $idInt, // La clé étrangère pointe vers l'intervention
        ]);

        return back()->with('success', 'Le document a été joint à l\'intervention avec succès.');
    }

    /**
     * Affiche le détail d'une intervention et consolide son coût financier.
     * * Règle métier : Le coût total d'une intervention est la somme exacte 
     * des achats de matériels (quantité * prix unitaire HT) et du coût de la main d'œuvre 
     * ou des prestataires issus de l'historique de suivi.
     *
     * @param int $id Identifiant de l'intervention
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $documents = DB::table('document')
            ->where('id_int', $id)
            ->orderByDesc('date_upload')
            ->get();

        // On récupère l'intervention avec ses relations
        $intervention = Intervention::with([
            'equipements',
            'categorie',
            'action',
            'suiviActions',
            'agents',
            'tiers',
            'contrat',
            'achatsMateriels'
        ])->findOrFail($id);

        $coutMateriels = $intervention->achatsMateriels->reduce(function ($carry, $m) {
            return $carry + ($m->quantite * $m->prix_unitaire_ht);
        }, 0);

        $coutSuivi = $intervention->suiviActions->sum('cout_associe');

        // Coût total consolidé
        $coutTotalIntervention = $coutMateriels + $coutSuivi;
        return view('interventions.show', compact('intervention', 'documents', 'coutMateriels', 'coutSuivi', 'coutTotalIntervention'));
    }

    public function cloturer($id)
    {
        $intervention = Intervention::findOrFail($id);

        $intervention->update([
            'statut_global' => 'Terminé',
            'date_cloture' => now()
        ]);

        return redirect()->back()->with('success', 'L\'intervention a été clôturée avec succès.');
    }
    /**
     * Exporte le registre des interventions au format CSV.
     * * Technique : Injection du BOM (Byte Order Mark) UTF-8 au début du fichier
     * pour forcer Microsoft Excel à interpréter correctement les caractères accentués.
     * Utilisation d'un stream HTTP pour ne pas saturer la RAM du serveur lors de gros exports.
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportExcel()
    {
        $interventions = Intervention::all();
        $fileName = 'registre_interventions.csv';

        $headers = array(
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $callback = function () use ($interventions) {
            $file = fopen('php://output', 'w');
            // Ajout du BOM pour qu'Excel reconnaisse l'UTF-8 (les accents)
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // En-têtes
            fputcsv($file, ['ID', 'Type', 'Statut', 'Date Ouverture', 'Description']);

            foreach ($interventions as $int) {
                fputcsv($file, [
                    $int->id_int,
                    $int->type_intervention,
                    $int->statut_global,
                    $int->date_ouverture,
                    $int->description
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function imprimer($id)
    {
        $intervention = Intervention::with(['categorie', 'action', 'suiviActions', 'agents', 'tiers', 'contrat', 'achatsMateriels'])->findOrFail($id);
        // On renvoie simplement une vue propre, et on déclenchera l'impression en JS
        return view('interventions.print', compact('intervention'));
    }

    public function sauvegarderCloture(Request $request, $id)
    {
        try {
            // 1. Validation rigoureuse
            $request->validate([
                'compte_rendu' => 'required|string|min:10',
                'date_cloture' => 'required|date',
                'temps_passe' => 'nullable|numeric|min:0',
                'cout_associe' => 'nullable|numeric|min:0',
                'statut_final' => 'required|string'
            ]);

            // 2. Création du compte-rendu dans la table suivi_action
            SuiviAction::create([
                'date_action_suivi' => $request->date_cloture,
                'description_etape' => $request->compte_rendu,
                'temps_passe_heures' => $request->temps_passe ?? 0,
                'cout_associe' => $request->cout_associe ?? 0,
                'statut_apres_action' => $request->statut_final,
                'id_int' => $id,
                'id_user' => Auth::id(),
            ]);

            // 3. Mise à jour de l'intervention parente
            $intervention = Intervention::findOrFail($id);
            $intervention->update([
                'statut_global' => $request->statut_final,
                'date_cloture' => ($request->statut_final == 'Terminé') ? $request->date_cloture : null
            ]);

            return redirect()->route('interventions.show', $id)
                ->with('success', 'Le compte-rendu a été enregistré et le statut mis à jour.');
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
    public function create(Request $request)
    {
        $equipement_preselectionne = $request->query('equipement_id');

        // Chargement de toutes les dimensions de ciblage de ton DDL
        $equipements = Equipement::orderBy('nom_equipement', 'asc')->get();
        $categories = Categorie::orderBy('libelle', 'asc')->get();
        $locaux = Local::orderBy('nom_local', 'asc')->get();
        $batiments = Batiment::orderBy('nom_bat', 'asc')->get();
        $lieux_publics = LieuPublic::orderBy('nom_lieu', 'asc')->get();
        $contrats = Contrat::orderBy('numero_contrat', 'asc')->get();
        $operations = OperationComptable::orderBy('numero_operation', 'asc')->get();
        $projets = Projet::orderBy('nom_projet', 'asc')->get();
        $projet_id = $request->query('projet_id');
        $agents = Utilisateur::orderBy('nom_user')->get();
        $tiers = DB::table('tiers')
            ->leftJoin('tiers_physique', 'tiers.id_tiers', '=', 'tiers_physique.id_tiers')
            ->leftJoin('tiers_morale', 'tiers.id_tiers', '=', 'tiers_morale.id_tiers')
            ->select(
                'tiers.id_tiers',
                'tiers.type_tiers',
                'tiers.tel_tiers',
                'tiers.email_tiers',
                'tiers_physique.nom_tiers',
                'tiers_physique.prenom_tiers',
                'tiers_morale.raison_sociale'
            )
            // On trie par ordre alphabétique en prenant soit la raison sociale, soit le nom
            ->orderByRaw('COALESCE(tiers_morale.raison_sociale, tiers_physique.nom_tiers) ASC')
            ->get();

        return view('interventions.create', compact(
            'equipements',
            'equipement_preselectionne',
            'categories',
            'locaux',
            'batiments',
            'lieux_publics',
            'agents',
            'tiers',
            'contrats',
            'operations',
            'projets',
        ));
    }
    /**
     * Enregistre une nouvelle intervention et ses dépendances patrimoniales.
     * * Sécurité : Utilisation d'une transaction SQL pour garantir que l'intervention 
     * et ses liaisons dans les tables pivots (intervention_equipement, intervention_espace) 
     * soient insérées simultanément. En cas d'erreur, un Rollback automatique est effectué.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // 1. Validation stricte basée sur ton schéma SQL
        $validated = $request->validate([
            'date_ouverture' => 'required|date',
            'type_intervention' => 'required|string|max:150',
            'statut_global' => 'required|string|max:50',
            'description' => 'required|string',
            'id_cat' => 'required|exists:categorie,id_cat',
            'code_budget' => 'nullable|string|max:2',

            // Les différentes cibles possibles de ton schéma
            'equipement_id' => 'nullable|exists:equipement,id_equipement',
            'id_local' => 'nullable|exists:local_,id_local',
            'id_batiment' => 'nullable|exists:batiment,id_batiment',
            'id_lieu' => 'nullable|exists:lieux_publics,id_lieu',
            'id_tiers' => 'nullable|exists:tiers,id_tiers',
            'id_contrat' => 'nullable|exists:contrat,id_contrat',
            'id_operation' => 'nullable|exists:operation_comptable,id_operation',
            'id_projet' => 'nullable|exists:projet,id_projet',
        ]);

        // 2. Préparation des données pour la table 'intervention'
        $interventionData = [
            'date_ouverture' => $validated['date_ouverture'],
            'type_intervention' => $validated['type_intervention'],
            'statut_global' => $validated['statut_global'],
            'description' => $validated['description'],
            'id_cat' => $validated['id_cat'],
            'code_budget' => $validated['code_budget'],
            'id_local' => $validated['id_local'] ?? null,
            'id_batiment' => $validated['id_batiment'] ?? null, // Si tu as ajouté la colonne, sinon géré par projet_batiment ou description
            'id_contrat' => $validated['id_contrat'] ?? null,
            'id_operation' => $validated['id_operation'] ?? null,
            'id_projet' => $validated['id_projet'] ?? null,
        ];

        // 3. Transaction pour garantir la cohérence des tables pivots
        DB::transaction(function () use ($interventionData, $validated, $request) {

            // Création de l'intervention principale
            $intervention = Intervention::create($interventionData);
            if ($request->has('agents')) {
                $intervention->agents()->attach($request->agents);
            }

            // A. Si c'est un ÉQUIPEMENT -> Table de liaison 'intervention_equipement'
            if ($request->filled('equipement_id')) {
                DB::table('intervention_equipement')->insert([
                    'id_int' => $intervention->id_int,
                    'id_equipement' => $validated['equipement_id']
                ]);
            }

            // B. Si c'est un LIEU PUBLIC -> Table de liaison 'intervention_espace' (Présente dans ton DDL !)
            if ($request->filled('id_lieu')) {
                DB::table('intervention_espace')->insert([
                    'id_int' => $intervention->id_int,
                    'id_lieu' => $validated['id_lieu']
                ]);
            }
        });

        // Redirection avec flag d'origine
        if ($request->has('from_equipement') && $request->filled('equipement_id')) {
            return redirect()->route('equipements.show', $validated['equipement_id'])
                ->with('success', 'Intervention créée et associée à l’équipement !');
        }

        return redirect()->route('interventions.index')->with('success', 'L’intervention a bien été enregistrée.');
    }

    // AFFICHER LE FORMULAIRE DE MODIFICATION
    public function edit($id)
    {
        $intervention = Intervention::with('equipements')->findOrFail($id);
        $equipements = Equipement::orderBy('nom_equipement')->get();
        $categories = Categorie::orderBy('libelle', 'asc')->get();
        $agents = Utilisateur::orderBy('nom_user')->get();
        $contrats = Contrat::orderBy('numero_contrat', 'asc')->get();
        $operations = OperationComptable::orderBy('numero_operation', 'asc')->get();
        $projets = Projet::orderBy('nom_projet', 'asc')->get();
        $tiers = DB::table('tiers')
            ->leftJoin('tiers_physique', 'tiers.id_tiers', '=', 'tiers_physique.id_tiers')
            ->leftJoin('tiers_morale', 'tiers.id_tiers', '=', 'tiers_morale.id_tiers')
            ->select(
                'tiers.id_tiers',
                'tiers.type_tiers',
                'tiers.tel_tiers',
                'tiers.email_tiers',
                'tiers_physique.nom_tiers',
                'tiers_physique.prenom_tiers',
                'tiers_morale.raison_sociale'
            )
            // On trie par ordre alphabétique en prenant soit la raison sociale, soit le nom
            ->orderByRaw('COALESCE(tiers_morale.raison_sociale, tiers_physique.nom_tiers) ASC')
            ->get();
        // On récupère l'ID de l'équipement lié (s'il y en a un) pour pré-sélectionner la liste
        $equipement_preselectionne = $intervention->equipements->first()->id_equipement ?? null;

        return view('interventions.edit', compact('intervention', 'equipements', 'categories', 'agents', 'tiers', 'equipement_preselectionne', 'contrats', 'operations', 'projets'));
    }

    // TRAITER LA MODIFICATION
    public function update(Request $request, $id)
    {
        $intervention = Intervention::findOrFail($id);

        $validated = $request->validate([
            'date_ouverture' => 'required|date',
            'type_intervention' => 'required|string|max:150',
            'statut_global' => 'required|string|max:50',
            'description' => 'required|string',
            'id_cat' => 'required|exists:categorie,id_cat',
            'equipement_id' => 'nullable|exists:equipement,id_equipement',
            'code_budget' => 'nullable|string|max:2',
            'id_tiers' => 'nullable|exists:tiers,id_tiers',
            'id_contrat' => 'nullable|exists:contrat,id_contrat',
            'id_operation' => 'nullable|exists:operation_comptable,id_operation',
            'id_projet' => 'nullable|exists:projet,id_projet',
        ]);

        $interventionData = $validated;
        unset($interventionData['equipement_id']);

        // Mise à jour des données
        $intervention->update($interventionData);

        if ($request->has('agents')) {
            $intervention->agents()->sync($request->agents);
        } else {
            $intervention->agents()->detach();
        }
        // Mise à jour de la liaison avec l'équipement (Table pivot)
        if ($request->filled('equipement_id')) {
            // sync() remplace les anciens liens par le nouveau
            $intervention->equipements()->sync([$request->equipement_id]);
        } else {
            // S'il n'y a pas d'équipement sélectionné, on détache tout
            $intervention->equipements()->detach();
        }

        return redirect()->route('interventions.show', $intervention->id_int)
            ->with('success', 'Intervention mise à jour avec succès.');
    }

    // SUPPRIMER L'INTERVENTION
    public function destroy($id)
    {
        $intervention = Intervention::findOrFail($id);

        // 1. On détache les équipements (Table pivot)

        $intervention->equipements()->detach();
        $intervention->agents()->detach();
        // 2. NOUVEAU : On supprime les comptes-rendus / l'historique liés
        $intervention->suiviActions()->delete();

        // 3. On peut enfin supprimer l'intervention en toute sécurité
        $intervention->delete();

        return redirect()->route('interventions.index')
            ->with('success', 'Intervention et son historique supprimés définitivement.');
    }
    // 1. Affiche le formulaire
    public function formulaireCloture($id)
    {
        $intervention = Intervention::findOrFail($id);
        return view('interventions.cloture_form', compact('intervention'));
    }
    // ENREGISTRER UN ACHAT DE MATÉRIEL / CONSOMMABLE
    public function ajouterMateriel(Request $request, $id)
    {
        $intervention = Intervention::findOrFail($id);

        $validated = $request->validate([
            'nom_materiel' => 'required|string|max:150',
            'quantite' => 'required|numeric|min:0.01',
            'unite_mesure' => 'nullable|string|max:50',
            'prix_unitaire_ht' => 'required|numeric|min:0',
            'date_achat' => 'required|date',
        ]);

        // On y injecte l'id de l'intervention et des statuts par défaut conformes à ton DDL
        DB::table('achat_materiel_consommable')->insert([
            'nom_materiel' => $validated['nom_materiel'],
            'quantite' => $validated['quantite'],
            'unite_mesure' => $validated['unite_mesure'] ?? 'Unité',
            'prix_unitaire_ht' => $validated['prix_unitaire_ht'],
            'date_achat' => $validated['date_achat'],
            'statut_achat' => 'Utilisé',
            'id_int' => $intervention->id_int,
        ]);

        return redirect()->route('interventions.show', $id)
            ->with('success', 'Matériel ajouté avec succès au bon de travaux.');
    }

    public function destroyMateriel($id)
    {
        // On récupère l'achat du matériel (ajuste le nom du modèle si besoin)
        $materiel = DB::table('achat_materiel_consommable')->where('id_achat', $id)->first();

        if (!$materiel) {
            return back()->with('error', 'Élément introuvable.');
        }

        DB::table('achat_materiel_consommable')->where('id_achat', $id)->delete();

        return back()->with('success', '✅ Ligne de matériel supprimée avec succès.');
    }

}
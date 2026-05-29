<?php

namespace App\Http\Controllers;

use App\Models\Contrat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContratController extends Controller
{
    public function index(Request $request)
    {
        $query = Contrat::with('tiers');

        if ($request->filled('type_contrat')) {
            $query->where('type_contrat', $request->type_contrat);
        }

        $contrats = $query->orderBy('date_debut_contrat', 'desc')->paginate(15);
        $typesContrat = Contrat::select('type_contrat')->distinct()->pluck('type_contrat');

        return view('contrats.index', compact('contrats', 'typesContrat'));
    }

    public function create()
    {
        $tiers = DB::table('tiers')
            ->leftJoin('tiers_physique', 'tiers.id_tiers', '=', 'tiers_physique.id_tiers')
            ->leftJoin('tiers_morale', 'tiers.id_tiers', '=', 'tiers_morale.id_tiers')
            ->select('tiers.id_tiers', 'tiers.type_tiers', 'tiers_physique.nom_tiers', 'tiers_physique.prenom_tiers', 'tiers_morale.raison_sociale')
            ->orderByRaw('COALESCE(tiers_morale.raison_sociale, tiers_physique.nom_tiers) ASC')
            ->get();

        return view('contrats.create', compact('tiers'));
    }

    public function store(Request $request)
    {
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

        $validated['revision_prix_prevue'] = $request->has('revision_prix_prevue');
        Contrat::create($validated);

        return redirect()->route('contrats.index')->with('success', 'Le contrat a été enregistré avec succès !');
    }

    public function show($id)
    {
        $contrat = Contrat::leftJoin('tiers', 'contrat.id_tiers', '=', 'tiers.id_tiers')
            ->leftJoin('tiers_physique', 'tiers.id_tiers', '=', 'tiers_physique.id_tiers')
            ->leftJoin('tiers_morale', 'tiers.id_tiers', '=', 'tiers_morale.id_tiers')
            ->select('contrat.*', 'tiers_physique.nom_tiers', 'tiers_physique.prenom_tiers', 'tiers_morale.raison_sociale')
            ->findOrFail($id);

        // Récupération des liaisons N:M
        $equipementsLies = $contrat->equipementsCouverts;
        $locauxLies = $contrat->locauxCouverts;
        $lieuxLies = $contrat->lieuxCouverts;

        // 🏢 Chargement manuel du pivot bâtiment pour parer au manque de withPivot de l'ORM
        $batimentsLies = DB::table('contrat_batiment')
            ->join('batiment', 'contrat_batiment.id_batiment', '=', 'batiment.id_batiment')
            ->where('contrat_batiment.id_contrat', $id)
            ->get();

        $interventionsTriggered = $contrat->interventions()->orderByDesc('date_ouverture')->get();

        // Catalogues d'actifs disponibles
        $equipementsDisponibles = DB::table('equipement')->orderBy('nom_equipement')->get();
        $batimentsDisponibles = DB::table('batiment')->orderBy('nom_bat')->get();
        $locauxDisponibles = DB::table('local_')->orderBy('nom_local')->get();
        $lieuxDisponibles = DB::table('lieux_publics')->orderBy('nom_lieu')->get();

        $decisions = DB::table('decision_administratif')->orderBy('numero_decision', 'desc')->get();

        return view('contrats.show', compact(
            'contrat',
            'equipementsLies',
            'batimentsLies',
            'locauxLies',
            'lieuxLies',
            'interventionsTriggered',
            'equipementsDisponibles',
            'batimentsDisponibles',
            'locauxDisponibles',
            'lieuxDisponibles',
            'decisions'
        ));
    }

    // 🚚 AJOUTER LOCATION MATÉRIEL (Avec observations)
    public function ajouterLocation(Request $request, $id_contrat)
    {
        $request->validate([
            'id_equipement' => 'required|exists:equipement,id_equipement',
            'id_decision' => 'nullable|exists:decision_administratif,id_decision',
            'quantite_louee' => 'required|integer|min:1',
            'etat_depart' => 'nullable|string|max:100',
            'date_debut_utilisation' => 'required|date',
            'date_fin_utilisation' => 'nullable|date|after_or_equal:date_debut_utilisation',
            'observations' => 'nullable|string',
        ]);

        DB::table('contrat_equipement')->insert([
            'id_contrat' => $id_contrat,
            'id_equipement' => $request->id_equipement,
            'id_decision' => $request->id_decision,
            'quantite_louee' => $request->quantite_louee,
            'etat_depart' => $request->etat_depart,
            'date_debut_utilisation' => $request->date_debut_utilisation,
            'date_fin_utilisation' => $request->date_fin_utilisation,
            'observations' => $request->observations,
            'statut_ligne' => 'En cours'
        ]);

        return redirect()->back()->with('success', '🚚 Matériel ajouté avec vos observations.');
    }

    // 🏢 NEW METHOD : LOUER UN BÂTIMENT ENTIER
    public function ajouterBatiment(Request $request, $id_contrat)
    {
        $request->validate([
            'id_batiment' => 'required|exists:batiment,id_batiment',
            'id_decision' => 'nullable|exists:decision_administratif,id_decision',
            'date_debut_utilisation' => 'required|date',
            'date_fin_utilisation' => 'nullable|date|after_or_equal:date_debut_utilisation',
            'etat_lieux_entree' => 'nullable|string',
            'caution_retenue' => 'nullable|numeric|min:0',
            'observations' => 'nullable|string',
        ]);

        DB::table('contrat_batiment')->insert([
            'id_contrat' => $id_contrat,
            'id_batiment' => $request->id_batiment,
            'id_decision' => $request->id_decision,
            'date_debut_utilisation' => $request->date_debut_utilisation,
            'date_fin_utilisation' => $request->date_fin_utilisation,
            'etat_lieux_entree' => $request->etat_lieux_entree,
            'caution_retenue' => $request->caution_retenue,
            'observations' => $request->observations,
            'statut_ligne' => 'En cours',
            'date_modification' => now()
        ]);

        return redirect()->back()->with('success', '🏢 Bâtiment complet affecté et conventionné au contrat.');
    }

    public function ajouterLocal(Request $request, $id_contrat)
    {
        $request->validate([
            'id_local' => 'required|exists:local_,id_local',
            'id_decision' => 'nullable|exists:decision_administratif,id_decision',
            'date_debut_utilisation' => 'required|date',
            'date_fin_utilisation' => 'nullable|date|after_or_equal:date_debut_utilisation',
            'etat_lieux_entree' => 'nullable|string',
            'caution_retenue' => 'nullable|numeric|min:0',
        ]);

        DB::table('contrat_local')->insert([
            'id_contrat' => $id_contrat,
            'id_local' => $request->id_local,
            'id_decision' => $request->id_decision,
            'date_debut_utilisation' => $request->date_debut_utilisation,
            'date_fin_utilisation' => $request->date_fin_utilisation,
            'etat_lieux_entree' => $request->etat_lieux_entree,
            'caution_retenue' => $request->caution_retenue,
            'statut_ligne' => 'En cours'
        ]);

        return redirect()->back()->with('success', '🏢 Salle/Local affecté au contrat de mise à disposition.');
    }

    public function ajouterLieu(Request $request, $id_contrat)
    {
        $request->validate([
            'id_lieu' => 'required|exists:lieux_publics,id_lieu',
            'id_decision' => 'nullable|exists:decision_administratif,id_decision',
            'date_debut_occupation' => 'required|date',
            'date_fin_occupation' => 'nullable|date|after_or_equal:date_debut_occupation',
            'surface_occupee_m2' => 'nullable|numeric|min:0',
            'usage_specifique' => 'nullable|string|max:255',
        ]);

        DB::table('contrat_lieu')->insert([
            'id_contrat' => $id_contrat,
            'id_lieu' => $request->id_lieu,
            'id_decision' => $request->id_decision,
            'date_debut_occupation' => $request->date_debut_occupation,
            'date_fin_occupation' => $request->date_fin_occupation,
            'surface_occupee_m2' => $request->surface_occupee_m2,
            'usage_specifique' => $request->usage_specifique,
            'statut_ligne' => 'En cours'
        ]);

        return redirect()->back()->with('success', '🌿 Domaine/Espace public conventionné avec succès.');
    }

    public function edit($id)
    {
        $contrat = Contrat::findOrFail($id);
        $tiers = DB::table('tiers')
            ->leftJoin('tiers_physique', 'tiers.id_tiers', '=', 'tiers_physique.id_tiers')
            ->leftJoin('tiers_morale', 'tiers.id_tiers', '=', 'tiers_morale.id_tiers')
            ->select('tiers.id_tiers', 'tiers_physique.nom_tiers', 'tiers_physique.prenom_tiers', 'tiers_morale.raison_sociale')
            ->orderByRaw('COALESCE(tiers_morale.raison_sociale, tiers_physique.nom_tiers) ASC')
            ->get();

        return view('contrats.edit', compact('contrat', 'tiers'));
    }

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

        return redirect()->route('contrats.show', $contrat->id_contrat)->with('success', 'Le contrat a été mis à jour.');
    }

    public function destroy($id)
    {
        $contrat = Contrat::findOrFail($id);

        try {
            // Utilisation d'une transaction pour s'assurer que TOUT est supprimé ou RIEN (sécurité Postgres)
            DB::transaction(function () use ($id, $contrat) {

                // 1. Nettoyage complet des tables pivots (Liaisons N:M)
                DB::table('contrat_equipement')->where('id_contrat', $id)->delete();
                DB::table('contrat_batiment')->where('id_contrat', $id)->delete();
                DB::table('contrat_local')->where('id_contrat', $id)->delete();
                DB::table('contrat_lieu')->where('id_contrat', $id)->delete();
                DB::table('operation_contrat')->where('id_contrat', $id)->delete();
                DB::table('convention_voirie')->where('id_contrat', $id)->delete();

                // 2. Rupture des liens sur les tables enfants (Passage à NULL pour conserver l'historique métier)
                DB::table('dossier_financier')->where('id_contrat', $id)->update(['id_contrat' => null]);
                DB::table('intervention')->where('id_contrat', $id)->update(['id_contrat' => null]);
                DB::table('compteur')->where('id_contrat', $id)->update(['id_contrat' => null]);

                // 3. Cas de sécurité critique : Les concessions de cimetière (id_contrat unique et NOT NULL)
                $hasConcession = DB::table('concession_cimetiere')->where('id_contrat', $id)->exists();
                if ($hasConcession) {
                    throw new \Exception("Ce contrat est lié à une concession de cimetière active. Supprimez d'abord la concession.");
                }

                // 4. Suppression définitive du contrat
                $contrat->delete();
            });

            return redirect()->route('contrats.index')
                ->with('success', '🗑️ Le contrat et l\'ensemble de ses affectations patrimoniales ont été supprimés du registre.');

        } catch (\Exception $e) {
            // En cas de problème ou si la concession bloque, on revient en arrière proprement
            return redirect()->back()
                ->with('error', '❌ Impossible de supprimer ce contrat : ' . $e->getMessage());
        }
    }
    // SUPPRIMER MATÉRIEL AFFECTÉ
    public function retirerMateriel($id_contrat, $id_equipement, $id_decision = null)
    {
        $query = DB::table('contrat_equipement')
            ->where('id_contrat', $id_contrat)
            ->where('id_equipement', $id_equipement);

        if (!$id_decision || $id_decision == 0 || $id_decision === 'null') {
            $query->whereNull('id_decision');
        } else {
            $query->where('id_decision', $id_decision);
        }

        $query->delete();
        return redirect()->back()->with('success', '🗑️ L\'équipement a été retiré du contrat.');
    }

    // SUPPRIMER BÂTIMENT AFFECTÉ
    public function retirerBatiment($id_contrat, $id_batiment)
    {
        DB::table('contrat_batiment')
            ->where('id_contrat', $id_contrat)
            ->where('id_batiment', $id_batiment)
            ->delete();

        return redirect()->back()->with('success', '🗑️ Le bâtiment a été désaffecté du contrat.');
    }

    // SUPPRIMER LOCAL AFFECTÉ
    public function retirerLocal($id_contrat, $id_local, $id_decision = null)
    {
        $query = DB::table('contrat_local')
            ->where('id_contrat', $id_contrat)
            ->where('id_local', $id_local);

        if (!$id_decision || $id_decision == 0 || $id_decision === 'null') {
            $query->whereNull('id_decision');
        } else {
            $query->where('id_decision', $id_decision);
        }

        $query->delete();
        return redirect()->back()->with('success', '🗑️ La salle/local a été retirée du contrat.');
    }

    // SUPPRIMER LIEU PUBLIC AFFECTÉ
    public function retirerLieu($id_contrat, $id_lieu, $id_decision = null)
    {
        $query = DB::table('contrat_lieu')
            ->where('id_contrat', $id_contrat)
            ->where('id_lieu', $id_lieu);

        if (!$id_decision || $id_decision == 0 || $id_decision === 'null') {
            $query->whereNull('id_decision');
        } else {
            $query->where('id_decision', $id_decision);
        }

        $query->delete();
        return redirect()->back()->with('success', '🗑️ Le domaine public a été retiré du périmètre.');
    }
}
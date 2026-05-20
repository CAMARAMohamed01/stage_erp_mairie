<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConcessionCimetiere;
use App\Models\EmplacementFuneraire;
use App\Models\Contrat;
use App\Models\Tiers;
use Illuminate\Support\Facades\DB;

class ConcessionCimetiereController extends Controller
{
    public function index()
    {
        $concessions = ConcessionCimetiere::with(['emplacement', 'contrat.tiers', 'defunts'])->get();
        return view('concessions.index', compact('concessions'));
    }

    public function create()
    {
        // 1. On ne prend que les emplacements libres
        $emplacementsLibres = EmplacementFuneraire::where('statut_occupation', 'Libre')->get();

        // 2. On prend les contrats de type Concession qui ne sont pas encore liés
        $contratsDispos = Contrat::where('type_contrat', 'ILIKE', '%concession%')
            ->whereNotIn('id_contrat', function ($query) {
                $query->select('id_contrat')->from('concession_cimetiere');
            })->get();

        // 3. Liste des personnes physiques pour les défunts
        $personnes = DB::table('tiers')
            ->join('tiers_physique', 'tiers.id_tiers', '=', 'tiers_physique.id_tiers')
            ->select('tiers.id_tiers', 'tiers_physique.nom_tiers', 'tiers_physique.prenom_tiers')
            ->get();

        return view('concessions.create', compact('emplacementsLibres', 'contratsDispos', 'personnes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_emplacement' => 'required|exists:emplacement_funeraire,id_emplacement',
            'id_contrat' => 'required|exists:contrat,id_contrat|unique:concession_cimetiere,id_contrat',
            'beneficiaires_autorises' => 'nullable|string',
            'commentaire_concession' => 'nullable|string',
            'id_defunts' => 'nullable|array',
            'id_defunts.*' => 'exists:tiers_physique,id_tiers',
        ]);

        // 1. Création de la concession
        $concession = ConcessionCimetiere::create($validated);

        // 2. Liaison avec les défunts
        if ($request->has('id_defunts')) {
            $concession->defunts()->attach($request->id_defunts);
        }

        // 3. Mise à jour automatique du statut de l'emplacement
        EmplacementFuneraire::where('id_emplacement', $request->id_emplacement)
            ->update(['statut_occupation' => 'Occupé']);

        return redirect()->route('concessions.index')->with('success', 'La concession a été actée.');
    }
    // --- AFFICHER LE FORMULAIRE DE MODIFICATION ---
    public function edit($id)
    {
        $concession = ConcessionCimetiere::with('defunts')->findOrFail($id);

        // 1. Emplacements libres + celui actuellement lié à cette concession
        $emplacementsLibres = EmplacementFuneraire::where('statut_occupation', 'Libre')
            ->orWhere('id_emplacement', $concession->id_emplacement)
            ->get();

        // 2. Contrats dispos + le contrat actuel
        $contratsDispos = Contrat::where('type_contrat', 'ILIKE', '%concession%')
            ->where(function ($query) use ($concession) {
                $query->whereNotIn('id_contrat', function ($sub) {
                    $sub->select('id_contrat')->from('concession_cimetiere');
                })->orWhere('id_contrat', $concession->id_contrat);
            })->get();

        // 3. Liste des personnes physiques
        $personnes = DB::table('tiers')
            ->join('tiers_physique', 'tiers.id_tiers', '=', 'tiers_physique.id_tiers')
            ->select('tiers.id_tiers', 'tiers_physique.nom_tiers', 'tiers_physique.prenom_tiers')
            ->get();

        return view('concessions.edit', compact('concession', 'emplacementsLibres', 'contratsDispos', 'personnes'));
    }

    // --- ENREGISTRER LES MODIFICATIONS ---
    public function update(Request $request, $id)
    {
        $concession = ConcessionCimetiere::findOrFail($id);
        $ancienEmplacementId = $concession->id_emplacement;

        $validated = $request->validate([
            'id_emplacement' => 'required|exists:emplacement_funeraire,id_emplacement',
            'id_contrat' => 'required|exists:contrat,id_contrat|unique:concession_cimetiere,id_contrat,' . $id . ',id_concession',
            'beneficiaires_autorises' => 'nullable|string',
            'commentaire_concession' => 'nullable|string',
            'id_defunts' => 'nullable|array',
            'id_defunts.*' => 'exists:tiers_physique,id_tiers',
        ]);

        // 1. Mise à jour des données de l'acte
        $concession->update([
            'id_emplacement' => $request->id_emplacement,
            'id_contrat' => $request->id_contrat,
            'beneficiaires_autorises' => $request->beneficiaires_autorises,
            'commentaire_concession' => $request->commentaire_concession,
        ]);

        // 2. Synchronisation des défunts (ajoute les nouveaux, supprime les retirés)
        $concession->defunts()->sync($request->id_defunts ?? []);

        // 3. Gestion intelligente des emplacements si la concession change de tombe
        if ($ancienEmplacementId != $request->id_emplacement) {
            EmplacementFuneraire::where('id_emplacement', $ancienEmplacementId)->update(['statut_occupation' => 'Libre']);
            EmplacementFuneraire::where('id_emplacement', $request->id_emplacement)->update(['statut_occupation' => 'Occupé']);
        }

        return redirect()->route('concessions.index')->with('success', 'L\'acte de concession a été mis à jour.');
    }

    // --- SUPPRIMER LA CONCESSION ---
    public function destroy($id)
    {
        $concession = ConcessionCimetiere::findOrFail($id);
        $idEmplacementLie = $concession->id_emplacement;

        // On détache d'abord les défunts de la table pivot
        $concession->defunts()->detach();

        // On supprime l'acte de concession
        $concession->delete();

        // On libère la tombe pour qu'elle redevienne disponible
        EmplacementFuneraire::where('id_emplacement', $idEmplacementLie)->update(['statut_occupation' => 'Libre']);

        return redirect()->route('concessions.index')->with('success', 'La concession a été supprimée et l\'emplacement a été libéré.');
    }
    // --- CONSULTER LA FICHE DÉTAILLÉE ---
    public function show($id)
    {
        $concession = ConcessionCimetiere::with(['emplacement.lieu', 'contrat.tiers', 'defunts'])->findOrFail($id);

        return view('concessions.show', compact('concession'));
    }
}
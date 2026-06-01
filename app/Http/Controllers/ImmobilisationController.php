<?php

namespace App\Http\Controllers;

use App\Models\ImmobilisationInventaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\LigneFinanciereFacture;

class ImmobilisationController extends Controller
{
    public function index(Request $request)
    {
        $query = ImmobilisationInventaire::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('num_inventaire', 'ilike', '%' . $search . '%')
                    ->orWhere('libelle_comptable', 'ilike', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'amortissable')
                $query->where('est_amortissable', true);
            if ($request->status === 'sorti')
                $query->whereNotNull('date_sortie');
            if ($request->status === 'actif')
                $query->whereNull('date_sortie');
        }

        $immobilisations = $query->orderBy('num_inventaire', 'asc')->paginate(15)->withQueryString();

        return view('finances.immobilisations.index', compact('immobilisations'));
    }

    public function create()
    {
        $lignesDisponibles = DB::table('ligne_financiere_facture_')
            ->orderByDesc('date_comptable')
            ->take(50)
            ->get();
        $articlesComptables = \App\Models\ArticleCompta::orderBy('numero_article')->get();
        return view('finances.immobilisations.create', compact('lignesDisponibles', 'articlesComptables'));
    }
    public function store(Request $request)
    {
        // 1. Validation stricte des données du formulaire
        $validated = $request->validate([
            'num_inventaire' => 'required|string|max:50|unique:immobilisation_inventaire_,num_inventaire',
            'libelle_comptable' => 'required|string|max:255',
            'valeur_achat' => 'nullable|numeric|min:0',
            'date_acquisition' => 'nullable|date',
            'id_ligne_achat' => 'nullable|exists:ligne_financiere_facture_,id_ligne',
        ]);

        // 2. Traitement du booléen pour la case à cocher
        $validated['est_amortissable'] = $request->has('est_amortissable');

        // 3. 🛡️ Sécurité Comptable : Si la valeur d'achat est vide mais qu'une facture est liée
        if (empty($validated['valeur_achat']) && !empty($validated['id_ligne_achat'])) {
            $ligneFacture = DB::table('ligne_financiere_facture_')
                ->where('id_ligne', $validated['id_ligne_achat'])
                ->first();

            if ($ligneFacture) {
                $validated['valeur_achat'] = $ligneFacture->montant_ttc;
            }
        }

        // 4. Insertion en base via le modèle Eloquent
        ImmobilisationInventaire::create($validated);

        // 5. Redirection avec message de confirmation
        return redirect()->route('immobilisations.index')
            ->with('success', '📦 Le bien a été immobilisé et inscrit avec succès au grand registre de la commune.');
    }

    public function show($id)
    {
        $immo = ImmobilisationInventaire::with([
            'ligneAchat.dossierFinancier',
            'ligneVente.dossierFinancier',
            'batiments',
            'parcelles',
            'lieuxPublics',
            'equipements'
        ])->findOrFail($id);

        // On charge TOUS les biens physiques de la commune qui sont encore totalement libres (id_immo à NULL)
        $batimentsDisponibles = DB::table('batiment')->whereNull('id_immo')->orderBy('nom_bat')->get();
        $parcellesDisponibles = DB::table('parcelle')->whereNull('id_immo')->orderBy('num_parcelle')->get();
        $equipementsDisponibles = DB::table('equipement')->whereNull('id_immo')->orderBy('nom_equipement')->get();
        $lieuxDisponibles = DB::table('lieux_publics')->whereNull('id_immo')->orderBy('nom_lieu')->get();

        return view('finances.immobilisations.show', compact(
            'immo',
            'batimentsDisponibles',
            'parcellesDisponibles',
            'equipementsDisponibles',
            'lieuxDisponibles'
        ));
    }

    public function rattacherBien(Request $request, $id)
    {
        $request->validate([
            'type_bien' => 'required|in:batiment,parcelle,equipement,lieu',
            'id_bien' => 'required|integer'
        ]);

        $type = $request->type_bien;
        $idBien = $request->id_bien;

        try {
            // Tentative de mise à jour sécurisée par un bloc try/catch
            if ($type === 'batiment') {
                DB::table('batiment')->where('id_batiment', $idBien)->update(['id_immo' => $id]);
            } elseif ($type === 'parcelle') {
                DB::table('parcelle')->where('id_parcelle', $idBien)->update(['id_immo' => $id]);
            } elseif ($type === 'equipement') {
                DB::table('equipement')->where('id_equipement', $idBien)->update(['id_immo' => $id]);
            } elseif ($type === 'lieu') {
                DB::table('lieux_publics')->where('id_lieu', $idBien)->update(['id_immo' => $id]);
            }

            return redirect()->back()->with('success', '🔗 Le bien physique a été rattaché avec succès.');

        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            // Si PostgreSQL lève la contrainte unique (Code 23505), on attrape l'erreur et on informe poliment l'utilisateur
            return redirect()->back()->withErrors([
                'liaison_error' => '⚠️ Impossible d\'affecter ce bien : cette immobilisation possède déjà un élément de cette nature unique (Contrainte SQL Unique).'
            ]);
        }
    }
    public function edit($id)
    {
        $immo = ImmobilisationInventaire::findOrFail($id);
        return view('finances.immobilisations.edit', compact('immo'));
    }

    public function update(Request $request, $id)
    {
        $immo = ImmobilisationInventaire::findOrFail($id);

        $validated = $request->validate([
            'num_inventaire' => 'required|string|max:50|unique:immobilisation_inventaire_,num_inventaire,' . $id . ',id_immo',
            'libelle_comptable' => 'required|string|max:255',
            'valeur_achat' => 'nullable|numeric|min:0',
            'date_acquisition' => 'nullable|date',
            'date_sortie' => 'nullable|date',
            'motif_sortie' => 'nullable|string|max:100',
            'valeur_revente' => 'nullable|numeric|min:0',
        ]);

        $validated['est_amortissable'] = $request->has('est_amortissable');
        $immo->update($validated);

        return redirect()->route('immobilisations.show', $id)
            ->with('success', '✏️ Fiche d\'inventaire mise à jour.');
    }

    public function destroy($id)
    {
        $immo = ImmobilisationInventaire::findOrFail($id);

        // Rompre le maillage sur les biens liés avant suppression pour éviter les blocages de clés étrangères
        DB::table('batiment')->where('id_immo', $id)->update(['id_immo' => null]);
        DB::table('parcelle')->where('id_immo', $id)->update(['id_immo' => null]);
        DB::table('equipement')->where('id_immo', $id)->update(['id_immo' => null]);
        DB::table('lieux_publics')->where('id_immo', $id)->update(['id_immo' => null]);

        $immo->delete();
        return redirect()->route('immobilisations.index')
            ->with('success', '🗑️ L\'immobilisation a été retirée définitivement du registre.');
    }
}
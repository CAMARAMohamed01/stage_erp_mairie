<?php

use App\Http\Controllers\ProjetController;
use App\Http\Controllers\ReleveCompteurController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardTechniqueController;
use App\Http\Controllers\ActionController;
use App\Http\Controllers\InterventionController;
use App\Http\Controllers\EquipementController;
use App\Http\Controllers\TiersController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HabilitationController;
use App\Http\Controllers\LocalController;
use App\Http\Controllers\BatimentController;
use App\Http\Controllers\LieuController;
use App\Http\Controllers\ContratController;
use App\Http\Controllers\DossierFinancierController;
use App\Http\Controllers\EmplacementFuneraireController;
use App\Http\Controllers\ConcessionCimetiereController;
use App\Http\Controllers\CompteurController;
use App\Http\Controllers\TronconController;
use App\Http\Controllers\VoieController;
use App\Http\Controllers\OuvrageController;
use App\Http\Controllers\secteurController;
use App\Http\Controllers\ZoneController;
use App\Http\Controllers\ParcelleController;
use App\Http\Controllers\CartographieController;
use App\Http\Controllers\ControleReglementaireController;
use App\Http\Controllers\TypeErpController;
use App\Http\Controllers\SupportAccesController;
use App\Http\Controllers\DossierUrbaController;
use App\Http\Controllers\OperationComptableController;
use App\Http\Controllers\EnveloppeBudgetaireController;
use App\Http\Controllers\DecisionAdministratifController;
use App\Http\Controllers\ChapitreController;
use App\Http\Controllers\ArticleComptaController;
use App\Http\Controllers\DecisionCommissionController;
use App\Http\Controllers\ImmobilisationController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\UtilisateurController;
use App\Http\Controllers\LieuDitController;

Route::get('/', function () {
    return view('welcome');
});

// --- ROUTES PUBLIQUES ---
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// --- ROUTES PRIVÉES (Nécessitent d'être connecté) ---
Route::middleware('auth')->group(function () {

    Route::get('/profil', [ProfilController::class, 'show'])->name('profil.show');
    Route::put('/profil', [ProfilController::class, 'update'])->name('profil.update');

    Route::get('/admin/utilisateurs', [UtilisateurController::class, 'index'])->name('utilisateurs.index');
    Route::get('/admin/utilisateurs/creer', [UtilisateurController::class, 'create'])->name('utilisateurs.create');
    Route::post('/admin/utilisateurs', [UtilisateurController::class, 'store'])->name('utilisateurs.store');
    Route::get('/admin/utilisateurs/{id}/modifier', [UtilisateurController::class, 'edit'])->name('utilisateurs.edit');
    Route::put('/admin/utilisateurs/{id}', [UtilisateurController::class, 'update'])->name('utilisateurs.update');
    Route::delete('/admin/utilisateurs/{id}', [UtilisateurController::class, 'destroy'])->name('utilisateurs.destroy');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard global
    Route::get('/technique/dashboard', [DashboardTechniqueController::class, 'index'])->name('technique.dashboard');

    // --- MODULE CITOYENS / TIERS ---
    Route::get('/citoyens', [TiersController::class, 'index'])->name('tiers.index');
    Route::get('/tiers/create', [TiersController::class, 'create'])->name('tiers.create');
    Route::post('/tiers', [TiersController::class, 'store'])->name('tiers.store');
    Route::get('/citoyens/{id}', [TiersController::class, 'show'])->name('tiers.show');
    Route::get('/tiers/{id}/edit', [TiersController::class, 'edit'])->name('tiers.edit');
    Route::put('/tiers/{id}', [TiersController::class, 'update'])->name('tiers.update');

    // ---MODULE ENTREPRISE---//
    // CRUD Tiers Morale (Entreprises)
    Route::get('/entreprises', [TiersController::class, 'entreprises'])->name('tiers.entreprises');
    Route::get('/entreprises/create', [TiersController::class, 'createEntreprise'])->name('tiers.create_entreprise');
    Route::post('/entreprises', [TiersController::class, 'storeEntreprise'])->name('tiers.store_entreprise');
    Route::get('/entreprises/{id}', [TiersController::class, 'showEntreprise'])->name('tiers.show_entreprise');
    Route::get('/entreprises/{id}/edit', [TiersController::class, 'editEntreprise'])->name('tiers.edit_entreprise');

    Route::put('/entreprises/{id}', [TiersController::class, 'updateEntreprise'])->name('tiers.update_entreprise');
    // Route de suppression générique pour les Tiers (Physique ou Morale)
    Route::delete('/tiers/{id}', [TiersController::class, 'destroy'])->name('tiers.destroy');


    //COMPTES BANCAIRES
    // Gestion des Comptes Bancaires
    Route::get('/tiers/{id_tiers}/comptes/create', [TiersController::class, 'createCompte'])->name('tiers.comptes.create');
    Route::post('/tiers/{id_tiers}/comptes', [TiersController::class, 'storeCompte'])->name('tiers.comptes.store');
    Route::delete('/comptes/{id}', [TiersController::class, 'destroyCompte'])->name('tiers.comptes.destroy');
    // Routes pour l'État Civil
    Route::post('/tiers/{id}/union', [TiersController::class, 'storeUnion'])->name('tiers.union.store');
    Route::post('/tiers/{id}/union/dissoudre/{p1}/{p2}', [TiersController::class, 'dissoudreUnion'])->name('tiers.union.dissoudre');

    Route::post('/tiers/{id}/filiation', [TiersController::class, 'storeFiliation'])->name('tiers.filiation.store');
    Route::delete('/tiers/filiation/retirer/{enfant}/{parent}', [TiersController::class, 'supprimerFiliation'])->name('tiers.filiation.destroy');
    // ======================================================== suppression de la route globale de suppression de document (sécurisée via la matrice de droits)
    Route::delete('/documents/global/{id}', function ($id) {
        $document = \App\Models\Document::findOrFail($id);
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($document->chemin_stockage)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($document->chemin_stockage);
        }
        $document->delete();
        return back()->with('success', 'Document supprimé définitivement.');
    })->middleware('can:check-permission,"Patrimoine & Equipements","suppression"')->name('documents.global.destroy');
    // ========================================================
    // 🔏 SECURISE : MODULE actionS VIA LA MATRICE DE DROITS
    // ========================================================

    // Lecture (Index, Fiche Détails, Export)
    Route::get('/actions', [ActionController::class, 'index'])->middleware('can:check-permission,"actions","lecture"')->name('actions.index');
    Route::get('/actions/export/excel', [ActionController::class, 'exportExcel'])->middleware('can:check-permission,"actions","lecture"')->name('actions.excel');
    Route::get('/actions/{id}', [ActionController::class, 'show'])->middleware('can:check-permission,"actions","lecture"')->name('actions.show');
    Route::get('/action/{id}/pdf', [ActionController::class, 'imprimer'])->middleware('can:check-permission,"actions","lecture"')->name('action.pdf');

    // Écriture / Création
    Route::get('/action/nouveau', [ActionController::class, 'create'])->middleware('can:check-permission,"actions","ecriture"')->name('actions.create');
    Route::post('/action/nouveau', [ActionController::class, 'store'])->middleware('can:check-permission,"actions","ecriture"')->name('actions.store');

    // Écriture / Modification & Prise en charge
    Route::get('/actions/{id}/edit', [ActionController::class, 'edit'])->middleware('can:check-permission,"actions","ecriture"')->name('actions.edit');
    Route::put('/actions/{id}', [ActionController::class, 'update'])->middleware('can:check-permission,"actions","ecriture"')->name('actions.update');
    Route::patch('/action/{id}/prendre-en-charge', [ActionController::class, 'prendreEnCharge'])->middleware('can:check-permission,"actions","ecriture"')->name('action.prendre-en-charge');
    Route::post('/action/{id}/creer-intervention', [ActionController::class, 'creerIntervention'])->middleware('can:check-permission,"actions","ecriture"')->name('action.creer-intervention');

    // Suppression
    Route::delete('/actions/{id}', [ActionController::class, 'destroy'])->middleware('can:check-permission,"actions","suppression"')->name('actions.destroy');

    //   // ========================================================
// 🔏 SECURISE : MODULE PROJET VIA LA MATRICE DE DROITS
// ========================================================
// --- Module : Patrimoine & Travaux ---
    Route::get('/projets', [ProjetController::class, 'index'])->middleware('can:check-permission,"Patrimoine & Travaux","lecture"')->name('projets.index');
    Route::get('/projets/create', [ProjetController::class, 'create'])->middleware('can:check-permission,"Patrimoine & Travaux","ecriture"')->name('projets.create');
    Route::post('/projets', [ProjetController::class, 'store'])->middleware('can:check-permission,"Patrimoine & Travaux","ecriture"')->name('projets.store');

    Route::get('/projets/{id}', [ProjetController::class, 'show'])->middleware('can:check-permission,"Patrimoine & Travaux","lecture"')->name('projets.show');
    Route::get('/projets/{id}/edit', [ProjetController::class, 'edit'])->middleware('can:check-permission,"Patrimoine & Travaux","ecriture"')->name('projets.edit');
    Route::put('/projets/{id}', [ProjetController::class, 'update'])->middleware('can:check-permission,"Patrimoine & Travaux","ecriture"')->name('projets.update');
    Route::delete('/projets/{id}', [ProjetController::class, 'destroy'])->middleware('can:check-permission,"Patrimoine & Travaux","suppression"')->name('projets.destroy');


    // ========================================================
// 🔏 SECURISE : MODULE INTERVENTIONS VIA LA MATRICE DE DROITS
// ========================================================

    Route::get('/interventions', [InterventionController::class, 'index'])->middleware('can:check-permission,"Interventions","lecture"')->name('interventions.index');
    Route::get('/interventions/export/excel', [InterventionController::class, 'exportExcel'])->middleware('can:check-permission,"Interventions","lecture"')->name('interventions.excel');
    Route::get('/interventions/create', [InterventionController::class, 'create'])->middleware('can:check-permission,"Interventions","ecriture"')->name('interventions.create');
    Route::post('/interventions', [InterventionController::class, 'store'])->middleware('can:check-permission,"Interventions","ecriture"')->name('interventions.store');

    // ROUTES AVEC VARIABLES / DYNAMIQUES
    Route::get('/interventions/{id}', [InterventionController::class, 'show'])->middleware('can:check-permission,"Interventions","lecture"')->name('interventions.show');
    Route::get('/interventions/{id}/pdf', [InterventionController::class, 'imprimer'])->middleware('can:check-permission,"Interventions","lecture"')->name('interventions.pdf');
    Route::get('/interventions/{id}/edit', [InterventionController::class, 'edit'])->middleware('can:check-permission,"Interventions","ecriture"')->name('interventions.edit');
    Route::put('/interventions/{id}', [InterventionController::class, 'update'])->middleware('can:check-permission,"Interventions","ecriture"')->name('interventions.update');
    Route::patch('/interventions/{id}/cloturer', [InterventionController::class, 'cloturer'])->middleware('can:check-permission,"Interventions","ecriture"')->name('interventions.cloturer');
    Route::get('/interventions/{id}/cloturer', [InterventionController::class, 'formulaireCloture'])->middleware('can:check-permission,"Interventions","ecriture"')->name('interventions.cloturer.form');
    Route::patch('/interventions/{id}/cloturer/save', [InterventionController::class, 'sauvegarderCloture'])->middleware('can:check-permission,"Interventions","ecriture"')->name('interventions.cloturer.save');

    // Suppression
    Route::delete('/interventions/{id}', [InterventionController::class, 'destroy'])->middleware('can:check-permission,"Interventions","suppression"')->name('interventions.destroy');

    Route::post('/interventions/{id}/materiel', [InterventionController::class, 'ajouterMateriel'])->middleware('can:check-permission,"Interventions","ecriture"')->name('interventions.materiel.store');
    Route::post('/interventions/{idInt}/documents', [InterventionController::class, 'uploadDocument'])
        ->middleware('can:check-permission,"Patrimoine & Equipements","ecriture"')
        ->name('interventions.documents.store');
    // ========================================================
    // 🔏 SECURISE : MODULE BATIMENTS & LIEUX PUBLICS (MATRICE)
    // ========================================================
// Routes de création rapide (AJAX) pour le formulaire bâtiment
    Route::post('/api/quick-adresse', [BatimentController::class, 'quickStoreAdresse'])->name('api.adresse.store');
    Route::post('/api/quick-parcelle', [BatimentController::class, 'quickStoreParcelle'])->name('api.parcelle.store');
    Route::post('/api/quick-tiers', [BatimentController::class, 'quickStoreTiers'])->name('api.tiers.store');
    // 1. ROUTES FIXES
    Route::get('/batiments', [BatimentController::class, 'index'])->middleware('can:check-permission,"Patrimoine & Equipements","lecture"')->name('batiments.index');
    Route::get('/batiments/create', [BatimentController::class, 'create'])->middleware('can:check-permission,"Patrimoine & Equipements","ecriture"')->name('batiments.create');
    Route::post('/batiments', [BatimentController::class, 'store'])->middleware('can:check-permission,"Patrimoine & Equipements","ecriture"')->name('batiments.store');

    // 2. ROUTES DYNAMIQUES
    Route::get('/batiments/{id}', [BatimentController::class, 'show'])->middleware('can:check-permission,"Patrimoine & Equipements","lecture"')->name('batiments.show');
    Route::get('/batiments/{id}/edit', [BatimentController::class, 'edit'])->middleware('can:check-permission,"Patrimoine & Equipements","ecriture"')->name('batiments.edit');
    Route::put('/batiments/{id}', [BatimentController::class, 'update'])->middleware('can:check-permission,"Patrimoine & Equipements","ecriture"')->name('batiments.update');
    Route::delete('/batiments/{id}', [BatimentController::class, 'destroy'])->middleware('can:check-permission,"Patrimoine & Equipements","suppression"')->name('batiments.destroy');
    // documents des bâtiments
    Route::get('/batiments/{id}/documents', [BatimentController::class, 'documents'])->middleware('can:check-permission,"Patrimoine & Equipements","lecture"')->name('batiments.documents');
    Route::post('/batiments/{idBatiment}/documents', [BatimentController::class, 'uploadDocument'])
        ->middleware('can:check-permission,"Patrimoine & Equipements","ecriture"')
        ->name('batiments.documents.store');
    // ========================================================
    // 🌳 SECURISE : MODULE LIEUX PUBLICS (Espaces extérieurs)
    // ========================================================

    Route::get('/lieux', [LieuController::class, 'index'])->middleware('can:check-permission,"Patrimoine & Equipements","lecture"')->name('lieux.index');
    Route::get('/lieux/create', [LieuController::class, 'create'])->middleware('can:check-permission,"Patrimoine & Equipements","ecriture"')->name('lieux.create');
    Route::post('/lieux', [LieuController::class, 'store'])->middleware('can:check-permission,"Patrimoine & Equipements","ecriture"')->name('lieux.store');

    Route::get('/lieux/{id}', [LieuController::class, 'show'])->middleware('can:check-permission,"Patrimoine & Equipements","lecture"')->name('lieux.show');
    Route::get('/lieux/{id}/edit', [LieuController::class, 'edit'])->middleware('can:check-permission,"Patrimoine & Equipements","ecriture"')->name('lieux.edit');
    Route::put('/lieux/{id}', [LieuController::class, 'update'])->middleware('can:check-permission,"Patrimoine & Equipements","ecriture"')->name('lieux.update');
    Route::delete('/lieux/{id}', [LieuController::class, 'destroy'])->middleware('can:check-permission,"Patrimoine & Equipements","suppression"')->name('lieux.destroy');

    Route::post('/lieux/{idLieu}/documents', [LieuController::class, 'uploadDocument'])
        ->middleware('can:check-permission,"Patrimoine & Equipements","ecriture"')
        ->name('lieux.documents.store');
    // ========================================================
    // 🔏 SECURISE : MODULE LOCAUX (PIÈCES & SALLES)
    // ========================================================

    Route::get('/locaux', [LocalController::class, 'index'])->middleware('can:check-permission,"Patrimoine & Equipements","lecture"')->name('locaux.index');
    Route::get('/locaux/create', [LocalController::class, 'create'])->middleware('can:check-permission,"Patrimoine & Equipements","ecriture"')->name('locaux.create');
    Route::post('/locaux', [LocalController::class, 'store'])->middleware('can:check-permission,"Patrimoine & Equipements","ecriture"')->name('locaux.store');

    Route::get('/locaux/{id}', [LocalController::class, 'show'])->middleware('can:check-permission,"Patrimoine & Equipements","lecture"')->name('locaux.show');
    Route::get('/locaux/{id}/edit', [LocalController::class, 'edit'])->middleware('can:check-permission,"Patrimoine & Equipements","ecriture"')->name('locaux.edit');
    Route::put('/locaux/{id}', [LocalController::class, 'update'])->middleware('can:check-permission,"Patrimoine & Equipements","ecriture"')->name('locaux.update');
    Route::delete('/locaux/{id}', [LocalController::class, 'destroy'])->middleware('can:check-permission,"Patrimoine & Equipements","suppression"')->name('locaux.destroy');
    Route::post('/locaux/{idLocal}/documents', [LocalController::class, 'uploadDocument'])
        ->middleware('can:check-permission,"Patrimoine & Equipements","ecriture"')
        ->name('locaux.documents.store');
    // ========================================================
    // 🔏 SECURISE : MODULE EQUIPEMENTS VIA LA MATRICE DE DROITS
    // ========================================================

    Route::get('/equipements', [EquipementController::class, 'index'])->middleware('can:check-permission,"Patrimoine & Equipements","lecture"')->name('equipements.index');
    Route::get('/equipements/create', [EquipementController::class, 'create'])->middleware('can:check-permission,"Patrimoine & Equipements","ecriture"')->name('equipements.create');
    Route::post('/equipements', [EquipementController::class, 'store'])->middleware('can:check-permission,"Patrimoine & Equipements","ecriture"')->name('equipements.store');

    // ROUTES DYNAMIQUES AVEC VARIABLES (Toujours après !)
    Route::get('/equipements/{id}', [EquipementController::class, 'show'])->middleware('can:check-permission,"Patrimoine & Equipements","lecture"')->name('equipements.show');
    Route::get('/equipements/{id}/edit', [EquipementController::class, 'edit'])->middleware('can:check-permission,"Patrimoine & Equipements","ecriture"')->name('equipements.edit');
    Route::put('/equipements/{id}', [EquipementController::class, 'update'])->middleware('can:check-permission,"Patrimoine & Equipements","ecriture"')->name('equipements.update');
    Route::delete('/equipements/{id}', [EquipementController::class, 'destroy'])->middleware('can:check-permission,"Patrimoine & Equipements","suppression"')->name('equipements.destroy');
    Route::post('/equipements/{idEquipement}/documents', [EquipementController::class, 'uploadDocument'])
        ->middleware('can:check-permission,"Patrimoine & Equipements","ecriture"')
        ->name('equipements.documents.store');

    // --- GESTION DES SUPPORTS D'ACCÈS / CLÉS ---
    Route::get('/supports-acces', [SupportAccesController::class, 'index'])
        ->middleware('can:check-permission,"Patrimoine & Equipements","lecture"')->name('supports-acces.index');

    Route::get('/supports-acces/create', [SupportAccesController::class, 'create'])
        ->middleware('can:check-permission,"Patrimoine & Equipements","ecriture"')->name('supports-acces.create');

    Route::post('/supports-acces', [SupportAccesController::class, 'store'])
        ->middleware('can:check-permission,"Patrimoine & Equipements","ecriture"')->name('supports-acces.store');

    Route::get('/supports-acces/{id}', [SupportAccesController::class, 'show'])
        ->middleware('can:check-permission,"Patrimoine & Equipements","lecture"')->name('supports-acces.show');

    Route::get('/supports-acces/{id}/edit', [SupportAccesController::class, 'edit'])
        ->middleware('can:check-permission,"Patrimoine & Equipements","ecriture"')->name('supports-acces.edit');

    Route::put('/supports-acces/{id}', [SupportAccesController::class, 'update'])
        ->middleware('can:check-permission,"Patrimoine & Equipements","ecriture"')->name('supports-acces.update');

    Route::delete('/supports-acces/{id}', [SupportAccesController::class, 'destroy'])
        ->middleware('can:check-permission,"Patrimoine & Equipements","suppression"')->name('supports-acces.destroy');

    // --- ACTIONS SUR LES SUPPORTS D'ACCÈS (AFFECTATIONS & OUVERTURES) ---
    Route::post('/supports-acces/{id}/affecter', [SupportAccesController::class, 'affecter'])
        ->middleware('can:check-permission,"Patrimoine & Equipements","ecriture"')->name('supports-acces.affecter');

    Route::put('/supports-acces/{id}/restituer/{userId}', [SupportAccesController::class, 'restituer'])
        ->middleware('can:check-permission,"Patrimoine & Equipements","ecriture"')->name('supports-acces.restituer');

    Route::post('/supports-acces/{id}/ouvertures', [SupportAccesController::class, 'ajouterOuverture'])
        ->middleware('can:check-permission,"Patrimoine & Equipements","ecriture"')->name('supports-acces.ouvertures.store');

    Route::delete('/supports-acces/{id}/ouvertures/{type}/{targetId}', [SupportAccesController::class, 'supprimerOuverture'])
        ->middleware('can:check-permission,"Patrimoine & Equipements","suppression"')->name('supports-acces.ouvertures.destroy');
    // =========================================================
    // MODULE : TYPES ERP
    // =========================================================

    Route::get('/types-erp', [TypeErpController::class, 'index'])
        ->middleware('can:check-permission,"Patrimoine & Equipements","lecture"')->name('types-erp.index');

    Route::get('/types-erp/create', [TypeErpController::class, 'create'])
        ->middleware('can:check-permission,"Patrimoine & Equipements","ecriture"')->name('types-erp.create');

    Route::post('/types-erp', [TypeErpController::class, 'store'])
        ->middleware('can:check-permission,"Patrimoine & Equipements","ecriture"')->name('types-erp.store');

    Route::get('/types-erp/{id}', [TypeErpController::class, 'show'])
        ->middleware('can:check-permission,"Patrimoine & Equipements","lecture"')->name('types-erp.show');

    Route::get('/types-erp/{id}/edit', [TypeErpController::class, 'edit'])
        ->middleware('can:check-permission,"Patrimoine & Equipements","ecriture"')->name('types-erp.edit');

    Route::put('/types-erp/{id}', [TypeErpController::class, 'update'])
        ->middleware('can:check-permission,"Patrimoine & Equipements","ecriture"')->name('types-erp.update');

    Route::delete('/types-erp/{id}', [TypeErpController::class, 'destroy'])
        ->middleware('can:check-permission,"Patrimoine & Equipements","suppression"')->name('types-erp.destroy');

    // --- CONTRÔLES RÉGLEMENTAIRES ---
    Route::get('/controles', [ControleReglementaireController::class, 'index'])
        ->middleware('can:check-permission,"Patrimoine & Equipements","lecture"')->name('controles.index');

    Route::get('/controles/create', [ControleReglementaireController::class, 'create'])
        ->middleware('can:check-permission,"Patrimoine & Equipements","ecriture"')->name('controles.create');

    Route::post('/controles', [ControleReglementaireController::class, 'store'])
        ->middleware('can:check-permission,"Patrimoine & Equipements","ecriture"')->name('controles.store');

    Route::get('/controles/{id}', [ControleReglementaireController::class, 'show'])
        ->middleware('can:check-permission,"Patrimoine & Equipements","lecture"')->name('controles.show');

    Route::get('/controles/{id}/edit', [ControleReglementaireController::class, 'edit'])
        ->middleware('can:check-permission,"Patrimoine & Equipements","ecriture"')->name('controles.edit');

    Route::put('/controles/{id}', [ControleReglementaireController::class, 'update'])
        ->middleware('can:check-permission,"Patrimoine & Equipements","ecriture"')->name('controles.update');

    Route::delete('/controles/{id}', [ControleReglementaireController::class, 'destroy'])
        ->middleware('can:check-permission,"Patrimoine & Equipements","suppression"')->name('controles.destroy');
    // ==========================================
// MODULE : GESTION DES CIMETIÈRES
// ==========================================
    Route::prefix('emplacements')->name('emplacements.')->group(function () {

        // LECTURE (Accès à la liste et aux détails)
        Route::get('/', [EmplacementFuneraireController::class, 'index'])
            ->middleware('can:check-permission,"État Civil & Cimetières","lecture"')
            ->name('index');

        // ÉCRITURE (Création)
        Route::get('/create', [EmplacementFuneraireController::class, 'create'])
            ->middleware('can:check-permission,"État Civil & Cimetières","ecriture"')
            ->name('create');

        Route::post('/', [EmplacementFuneraireController::class, 'store'])
            ->middleware('can:check-permission,"État Civil & Cimetières","ecriture"')
            ->name('store');

        // ÉCRITURE (Modification)
        Route::get('/{id}/edit', [EmplacementFuneraireController::class, 'edit'])
            ->middleware('can:check-permission,"État Civil & Cimetières","ecriture"')
            ->name('edit');

        Route::put('/{id}', [EmplacementFuneraireController::class, 'update'])
            ->middleware('can:check-permission,"État Civil & Cimetières","ecriture"')
            ->name('update');

        // SUPPRESSION
        Route::delete('/{id}', [EmplacementFuneraireController::class, 'destroy'])
            ->middleware('can:check-permission,"État Civil & Cimetières","suppression"')
            ->name('destroy');
    });
    Route::prefix('concessions')->name('concessions.')->group(function () {
        Route::get('/', [ConcessionCimetiereController::class, 'index'])
            ->middleware('can:check-permission,"État Civil & Cimetières","lecture"')->name('index');

        Route::get('/create', [ConcessionCimetiereController::class, 'create'])
            ->middleware('can:check-permission,"État Civil & Cimetières","ecriture"')->name('create');

        // --- NOUVELLE ROUTE POUR LA FICHE DÉTAILLÉE ---
        Route::get('/{id}', [ConcessionCimetiereController::class, 'show'])
            ->middleware('can:check-permission,"État Civil & Cimetières","lecture"')->name('show');

        Route::post('/', [ConcessionCimetiereController::class, 'store'])
            ->middleware('can:check-permission,"État Civil & Cimetières","ecriture"')->name('store');

        // --- LES NOUVELLES ROUTES ---
        Route::get('/{id}/edit', [ConcessionCimetiereController::class, 'edit'])
            ->middleware('can:check-permission,"État Civil & Cimetières","ecriture"')->name('edit');

        Route::put('/{id}', [ConcessionCimetiereController::class, 'update'])
            ->middleware('can:check-permission,"État Civil & Cimetières","ecriture"')->name('update');

        Route::delete('/{id}', [ConcessionCimetiereController::class, 'destroy'])
            ->middleware('can:check-permission,"État Civil & Cimetières","suppression"')->name('destroy');
    });


    // ==========================================
// MODULE : GESTION DES FLUIDES & COMPTEURS
// ==========================================
    Route::prefix('compteurs')->name('compteurs.')->group(function () {

        Route::get('/', [CompteurController::class, 'index'])
            ->middleware('can:check-permission,"Patrimoine & Equipements","lecture"')->name('index');

        Route::get('/create', [CompteurController::class, 'create'])
            ->middleware('can:check-permission,"Patrimoine & Equipements","ecriture"')->name('create');
        Route::get('/{id}', [CompteurController::class, 'show'])->middleware('can:check-permission,"Patrimoine & Equipements","lecture"')->name('show');

        Route::post('/', [CompteurController::class, 'store'])
            ->middleware('can:check-permission,"Patrimoine & Equipements","ecriture"')->name('store');

        Route::get('/{id}/edit', [CompteurController::class, 'edit'])
            ->middleware('can:check-permission,"Patrimoine & Equipements","ecriture"')->name('edit');

        Route::put('/{id}', [CompteurController::class, 'update'])
            ->middleware('can:check-permission,"Patrimoine & Equipements","ecriture"')->name('update');

        Route::delete('/{id}', [CompteurController::class, 'destroy'])
            ->middleware('can:check-permission,"Patrimoine & Equipements","suppression"')->name('destroy');
        Route::post('/{idCompteur}/documents', [CompteurController::class, 'uploadDocument'])
            ->middleware('can:check-permission,"Patrimoine & Equipements","ecriture"')->name('documents.store');
        Route::delete('/documents/{idDocument}', [CompteurController::class, 'deleteDocument'])
            ->middleware('can:check-permission,"Patrimoine & Equipements","suppression"')->name('documents.destroy');
    });
    // Relevés des compteurs
    Route::prefix('compteurs/{idCompteur}/releves')->name('compteurs.releves.')->group(function () {
        Route::get('/', [ReleveCompteurController::class, 'index'])->name('index');
        Route::post('/', [ReleveCompteurController::class, 'store'])->name('store');
        Route::get('/export-pdf', [ReleveCompteurController::class, 'exportPdf'])->name('export.pdf');
        // Route pour l'upload de documents sur un compteur

    });


    // GESTION DES DOCUMENTS GÉNÉRAUX DES TIERS
    Route::prefix('tiers/{id_tiers}/documents')->name('tiers.documents.')->group(function () {
        Route::get('/create', [TiersController::class, 'createDocument'])->name('create');
        Route::post('/', [TiersController::class, 'storeDocument'])->name('store');
    });
    // ========================================================
    // 💼 SECURISE : MODULE CONTRATS & ENGAGEMENTS FINANCIERS
    // ========================================================

    // Lecture (Index, Fiche Détails, Exports potentiels)
    Route::get('/contrats', [ContratController::class, 'index'])->middleware('can:check-permission,"Finances & Achats","lecture"')->name('contrats.index');

    // Écriture / Création
    Route::get('/contrats/create', [ContratController::class, 'create'])->middleware('can:check-permission,"Finances & Achats","ecriture"')->name('contrats.create');
    Route::post('/contrats', [ContratController::class, 'store'])->middleware('can:check-permission,"Finances & Achats","ecriture"')->name('contrats.store');

    // ROUTES DYNAMIQUES AVEC VARIABLES 
    // Écriture / Modification
    Route::get('/contrats/{id}', [ContratController::class, 'show'])->middleware('can:check-permission,"Finances & Achats","lecture"')->name('contrats.show');
    Route::get('/contrats/{id}/edit', [ContratController::class, 'edit'])->middleware('can:check-permission,"Finances & Achats","ecriture"')->name('contrats.edit');
    Route::put('/contrats/{id}', [ContratController::class, 'update'])->middleware('can:check-permission,"Finances & Achats","ecriture"')->name('contrats.update');

    // Suppression
    Route::delete('/contrats/{id}', [ContratController::class, 'destroy'])->middleware('can:check-permission,"Finances & Achats","suppression"')->name('contrats.destroy');
    Route::post('/contrats/{id}/ajouter-location', [ContratController::class, 'ajouterLocation'])->middleware('can:check-permission,"Finances & Achats","ecriture"')->name('contrats.location.store');

    // Enregistrement des occupations de patrimoine
    Route::post('/contrats/{id}/ajouter-local', [ContratController::class, 'ajouterLocal'])->name('contrats.local.store');
    Route::post('/contrats/{id}/ajouter-lieu', [ContratController::class, 'ajouterLieu'])->name('contrats.lieu.store');
    Route::post('/contrats/{id}/ajouter-batiment', [ContratController::class, 'ajouterBatiment'])->name('contrats.batiment.store');
    // --- DÉSAFFECTATION DES BIENS D'UN CONTRAT ---
    Route::delete('/contrats/{id_contrat}/materiel/{id_equipement}/{id_decision?}', [ContratController::class, 'retirerMateriel'])->name('contrats.materiel.destroy');
    Route::delete('/contrats/{id_contrat}/batiment/{id_batiment}', [ContratController::class, 'retirerBatiment'])->name('contrats.batiment.destroy');
    Route::delete('/contrats/{id_contrat}/local/{id_local}/{id_decision?}', [ContratController::class, 'retirerLocal'])->name('contrats.local.destroy');
    Route::delete('/contrats/{id_contrat}/lieu/{id_lieu}/{id_decision?}', [ContratController::class, 'retirerLieu'])->name('contrats.lieu.destroy');
    // ========================================================
    // 💳 SECURISE : MODULE DOSSIERS FINANCIERS (COMPTABILITE)
    // ========================================================

    // Route::get('/finances/dossiers', [DossierFinancierController::class, 'index'])->middleware('can:check-permission,"Finances & Achats","lecture"')->name('dossiers-financiers.index');
    // Route::get('/finances/dossiers/create', [DossierFinancierController::class, 'create'])->middleware('can:check-permission,"Finances & Achats","ecriture"')->name('dossiers-financiers.create');
    // Route::post('/finances/dossiers', [DossierFinancierController::class, 'store'])->middleware('can:check-permission,"Finances & Achats","ecriture"')->name('dossiers-financiers.store');
    // Route::get('/finances/dossiers/{id}', [DossierFinancierController::class, 'show'])->middleware('can:check-permission,"Finances & Achats","lecture"')->name('dossiers-financiers.show');
    // Route::post('/finances/dossiers/{id}/ligne', [DossierFinancierController::class, 'ajouterLigne'])->middleware('can:check-permission,"Finances & Achats","ecriture"')->name('dossiers-financiers.ligne.store');
    Route::resource('dossiers-financiers', DossierFinancierController::class)
        ->middleware('can:check-permission,"Finances & Achats","lecture"');

    // Enregistrer une imputation de charge
    Route::post('/dossiers-financiers/{id}/lignes', [DossierFinancierController::class, 'ajouterLigne'])
        ->middleware('can:check-permission,"Finances & Achats","ecriture"')->name('dossiers-financiers.lignes.store');

    // Supprimer une imputation
    Route::delete('/dossiers-financiers/{id}/lignes/{idLigne}', [DossierFinancierController::class, 'supprimerLigne'])
        ->middleware('can:check-permission,"Finances & Achats","suppression"')->name('dossiers-financiers.lignes.destroy');

    // Changer le statut budgétaire à la volée
    Route::patch('/dossiers-financiers/{id}/statut', [DossierFinancierController::class, 'updateStatut'])
        ->middleware('can:check-permission,"Finances & Achats","ecriture"')->name('dossiers-financiers.statut.update');
    // --- GESTION DES DOCUMENTS DU DOSSIER FINANCIER ---
    Route::post('/dossiers-financiers/{id}/documents', [DossierFinancierController::class, 'uploadDocument'])
        ->middleware('can:check-permission,"Finances & Achats","ecriture"')
        ->name('dossiers-financiers.documents.store');

    Route::delete('/dossiers-financiers/{id}/documents/{documentId}', [DossierFinancierController::class, 'destroyDocument'])
        ->middleware('can:check-permission,"Finances & Achats","suppression"')
        ->name('dossiers-financiers.documents.destroy');

    // --- GESTION DES OPÉRATIONS COMPTABLES ---
    Route::get('/operations-comptables', [OperationComptableController::class, 'index'])->middleware('can:check-permission,"Finances & Achats","lecture"')->name('operations-comptables.index');
    Route::get('/operations-comptables/create', [OperationComptableController::class, 'create'])->middleware('can:check-permission,"Finances & Achats","ecriture"')->name('operations-comptables.create');
    Route::post('/operations-comptables', [OperationComptableController::class, 'store'])->middleware('can:check-permission,"Finances & Achats","ecriture"')->name('operations-comptables.store');
    Route::get('/operations-comptables/{id}', [OperationComptableController::class, 'show'])->middleware('can:check-permission,"Finances & Achats","lecture"')->name('operations-comptables.show');
    Route::get('/operations-comptables/{id}/edit', [OperationComptableController::class, 'edit'])->middleware('can:check-permission,"Finances & Achats","ecriture"')->name('operations-comptables.edit');
    Route::put('/operations-comptables/{id}', [OperationComptableController::class, 'update'])->middleware('can:check-permission,"Finances & Achats","ecriture"')->name('operations-comptables.update');
    Route::delete('/operations-comptables/{id}', [OperationComptableController::class, 'destroy'])->middleware('can:check-permission,"Finances & Achats","suppression"')->name('operations-comptables.destroy');

    // --- GESTION DES ENVELOPPES BUDGÉTAIRES ---
    Route::get('/enveloppes-budgetaires', [EnveloppeBudgetaireController::class, 'index'])->middleware('can:check-permission,"Finances & Achats","lecture"')->name('enveloppes-budgetaires.index');
    Route::get('/enveloppes-budgetaires/create', [EnveloppeBudgetaireController::class, 'create'])->middleware('can:check-permission,"Finances & Achats","ecriture"')->name('enveloppes-budgetaires.create');
    Route::post('/enveloppes-budgetaires', [EnveloppeBudgetaireController::class, 'store'])->middleware('can:check-permission,"Finances & Achats","ecriture"')->name('enveloppes-budgetaires.store');
    Route::get('/enveloppes-budgetaires/{id}', [EnveloppeBudgetaireController::class, 'show'])->middleware('can:check-permission,"Finances & Achats","lecture"')->name('enveloppes-budgetaires.show');
    Route::get('/enveloppes-budgetaires/{id}/edit', [EnveloppeBudgetaireController::class, 'edit'])->middleware('can:check-permission,"Finances & Achats","ecriture"')->name('enveloppes-budgetaires.edit');
    Route::put('/enveloppes-budgetaires/{id}', [EnveloppeBudgetaireController::class, 'update'])->middleware('can:check-permission,"Finances & Achats","ecriture"')->name('enveloppes-budgetaires.update');
    Route::delete('/enveloppes-budgetaires/{id}', [EnveloppeBudgetaireController::class, 'destroy'])->middleware('can:check-permission,"Finances & Achats","suppression"')->name('enveloppes-budgetaires.destroy');

    // --- GESTION DES CHAPITRES ---
    Route::resource('chapitres', ChapitreController::class)
        ->middleware('can:check-permission,"Finances & Achats","lecture"');

    // --- GESTION DES ARTICLES COMPTABLES ---
    Route::resource('articles-compta', ArticleComptaController::class)
        ->middleware('can:check-permission,"Finances & Achats","lecture"');
    // --- GESTION DES DÉCISIONS ADMINISTRATIVES ---
    Route::resource('decisions-admin', DecisionAdministratifController::class)
        ->middleware('can:check-permission,"Administration","lecture"');

    // --- LIAISON ACTE ➔ OPÉRATION COMPTABLE (PIVOT) ---
    Route::post('/decisions-admin/{id}/operations', [DecisionAdministratifController::class, 'lierOperation'])
        ->middleware('can:check-permission,"Administration","ecriture"')->name('decisions-admin.operations.store');

    Route::delete('/decisions-admin/{id}/operations/{idOp}', [DecisionAdministratifController::class, 'delierOperation'])
        ->middleware('can:check-permission,"Administration","suppression"')->name('decisions-admin.operations.destroy');

    Route::resource('decisions-commission', DecisionCommissionController::class)
        ->middleware('can:check-permission,"Conseil & Commissions","lecture"');

    Route::resource('immobilisations', ImmobilisationController::class)
        ->middleware('can:check-permission,"Finances & Achats","lecture"');
    // --- MAILLAGE DIRECT DE L'INVENTAIRE COMPTABLE ---
    Route::post('/immobilisations/{id}/rattacher-bien', [ImmobilisationController::class, 'rattacherBien'])->name('immobilisations.rattacher');
    // ========================================================
    // 🚧 MODULE VOIES & RÉSEAUX DIVERS (VRD) / TRONÇONS / OUVRAGES
    // ========================================================
// --- MODULE LIEUX-DITS (TERRITOIRE) ---
    Route::get('/lieux-dits', [LieuDitController::class, 'index'])->middleware('can:check-permission,"Voirie","lecture"')->name('lieux-dits.index');
    Route::get('/lieux-dits/creer', [LieuDitController::class, 'create'])->middleware('can:check-permission,"Voirie","ecriture"')->name('lieux-dits.create');
    Route::post('/lieux-dits', [LieuDitController::class, 'store'])->middleware('can:check-permission,"Voirie","ecriture"')->name('lieux-dits.store');
    Route::get('/lieux-dits/{id}/modifier', [LieuDitController::class, 'edit'])->middleware('can:check-permission,"Voirie","ecriture"')->name('lieux-dits.edit');
    Route::put('/lieux-dits/{id}', [LieuDitController::class, 'update'])->middleware('can:check-permission,"Voirie","ecriture"')->name('lieux-dits.update');
    Route::delete('/lieux-dits/{id}', [LieuDitController::class, 'destroy'])->middleware('can:check-permission,"Voirie","suppression"')->name('lieux-dits.destroy');
    // --- GESTION DES VOIES ---
    Route::get('/voies', [VoieController::class, 'index'])
        ->middleware('can:check-permission,"Voirie","lecture"')->name('voies.index');
    Route::get('/voies/create', [VoieController::class, 'create'])
        ->middleware('can:check-permission,"Voirie","ecriture"')->name('voies.create');
    Route::post('/voies', [VoieController::class, 'store'])
        ->middleware('can:check-permission,"Voirie","ecriture"')->name('voies.store');
    Route::get('/voies/{id}', [VoieController::class, 'show'])
        ->middleware('can:check-permission,"Voirie","lecture"')->name('voies.show');
    Route::get('/voies/{id}/edit', [VoieController::class, 'edit'])
        ->middleware('can:check-permission,"Voirie","ecriture"')->name('voies.edit');
    Route::put('/voies/{id}', [VoieController::class, 'update'])
        ->middleware('can:check-permission,"Voirie","ecriture"')->name('voies.update');
    Route::delete('/voies/{id}', [VoieController::class, 'destroy'])
        ->middleware('can:check-permission,"Voirie","suppression"')->name('voies.destroy');

    // --- GESTION DES TRONÇONS DE VOIE ---
    Route::get('/troncons/create', [TronconController::class, 'create'])
        ->middleware('can:check-permission,"Voirie","ecriture"')->name('troncons.create');
    Route::post('/troncons', [TronconController::class, 'store'])
        ->middleware('can:check-permission,"Voirie","ecriture"')->name('troncons.store');
    Route::get('/troncons/{id}', [TronconController::class, 'show'])
        ->middleware('can:check-permission,"Voirie","lecture"')->name('troncons.show');
    Route::get('/troncons/{id}/edit', [TronconController::class, 'edit'])
        ->middleware('can:check-permission,"Voirie","ecriture"')->name('troncons.edit');
    Route::put('/troncons/{id}', [TronconController::class, 'update'])
        ->middleware('can:check-permission,"Voirie","ecriture"')->name('troncons.update');
    Route::delete('/troncons/{id}', [TronconController::class, 'destroy'])
        ->middleware('can:check-permission,"Voirie","suppression"')->name('troncons.destroy');

    // Pièces jointes rattachées aux Tronçons
    Route::post('/troncons/{id}/documents', [TronconController::class, 'uploadDocument'])
        ->middleware('can:check-permission,"Voirie","ecriture"')->name('troncons.documents.store');
    Route::delete('/troncons/documents/{id}', [TronconController::class, 'destroyDocument'])
        ->middleware('can:check-permission,"Voirie","suppression"')->name('troncons.documents.destroy');

    // --- GESTION DES OUVRAGES D'ART ---
    Route::get('/ouvrages', [OuvrageController::class, 'index'])
        ->middleware('can:check-permission,"Voirie","lecture"')->name('ouvrages.index');
    Route::get('/ouvrages/create', [OuvrageController::class, 'create'])
        ->middleware('can:check-permission,"Voirie","ecriture"')->name('ouvrages.create');
    Route::post('/ouvrages', [OuvrageController::class, 'store'])
        ->middleware('can:check-permission,"Voirie","ecriture"')->name('ouvrages.store');
    Route::get('/ouvrages/{id}', [OuvrageController::class, 'show'])
        ->middleware('can:check-permission,"Voirie","lecture"')->name('ouvrages.show');
    Route::get('/ouvrages/{id}/edit', [OuvrageController::class, 'edit'])
        ->middleware('can:check-permission,"Voirie","ecriture"')->name('ouvrages.edit');
    Route::put('/ouvrages/{id}', [OuvrageController::class, 'update'])
        ->middleware('can:check-permission,"Voirie","ecriture"')->name('ouvrages.update');
    Route::delete('/ouvrages/{id}', [OuvrageController::class, 'destroy'])
        ->middleware('can:check-permission,"Voirie","suppression"')->name('ouvrages.destroy');

    // Partage intercommunal des ouvrages d'art
    Route::post('/ouvrages/{ouvrage}/communes', [OuvrageController::class, 'addCommune'])
        ->middleware('can:check-permission,"Voirie","ecriture"')->name('ouvrages.communes.store');
    Route::delete('/ouvrages/{ouvrage}/communes/{commune}', [OuvrageController::class, 'removeCommune'])
        ->middleware('can:check-permission,"Voirie","suppression"')->name('ouvrages.communes.destroy');

    // --- RÉFÉRENTIEL DES COMMUNES PARTENAIRES ---
    Route::get('/communes', [\App\Http\Controllers\CommunePartenaireController::class, 'index'])
        ->middleware('can:check-permission,"Voirie","lecture"')->name('communes.index');
    Route::get('/communes/create', [\App\Http\Controllers\CommunePartenaireController::class, 'create'])
        ->middleware('can:check-permission,"Voirie","ecriture"')->name('communes.create');
    Route::post('/communes', [\App\Http\Controllers\CommunePartenaireController::class, 'store'])
        ->middleware('can:check-permission,"Voirie","ecriture"')->name('communes.store');
    // Secteurs
    Route::get('/secteurs', [SecteurController::class, 'index'])->name('secteurs.index');
    Route::get('/secteurs/create', [SecteurController::class, 'create'])->name('secteurs.create');
    Route::post('/secteurs', [SecteurController::class, 'store'])->name('secteurs.store');
    Route::get('/secteurs/{id}', [SecteurController::class, 'show'])->name('secteurs.show');
    Route::get('/secteurs/{id}/edit', [SecteurController::class, 'edit'])->name('secteurs.edit');
    Route::put('/secteurs/{id}', [SecteurController::class, 'update'])->name('secteurs.update');
    Route::delete('/secteurs/{id}', [SecteurController::class, 'destroy'])->name('secteurs.destroy');

    // Zones
    Route::get('/zones', [ZoneController::class, 'index'])->name('zones.index');
    Route::get('/zones/create', [ZoneController::class, 'create'])->name('zones.create');
    Route::post('/zones', [ZoneController::class, 'store'])->name('zones.store');
    Route::get('/zones/{id}', [ZoneController::class, 'show'])->name('zones.show');
    Route::get('/zones/{id}/edit', [ZoneController::class, 'edit'])->name('zones.edit');
    Route::put('/zones/{id}', [ZoneController::class, 'update'])->name('zones.update');
    Route::delete('/zones/{id}', [ZoneController::class, 'destroy'])->name('zones.destroy');

    //Parcelles
    Route::get('/parcelles', [ParcelleController::class, 'index'])->name('parcelles.index');
    Route::get('/parcelles/create', [ParcelleController::class, 'create'])->name('parcelles.create');
    Route::post('/parcelles', [ParcelleController::class, 'store'])->name('parcelles.store');
    Route::get('/parcelles/{id}', [ParcelleController::class, 'show'])->name('parcelles.show');
    Route::get('/parcelles/{id}/edit', [ParcelleController::class, 'edit'])->name('parcelles.edit');
    Route::put('/parcelles/{id}', [ParcelleController::class, 'update'])->name('parcelles.update');
    Route::delete('/parcelles/{id}', [ParcelleController::class, 'destroy'])->name('parcelles.destroy');
    // Liaison d'un propriétaire à une parcelle
    Route::post('/parcelles/{id}/proprietaires', [ParcelleController::class, 'ajouterProprietaire'])
        ->middleware('can:check-permission,"Patrimoine & Equipements","ecriture"')->name('parcelles.proprietaires.store');
    // Dissociation d'un propriétaire d'une parcelle
    Route::delete('/parcelles/{id}/proprietaires/{idTiers}', [ParcelleController::class, 'retirerProprietaire'])
        ->middleware('can:check-permission,"Patrimoine & Equipements","suppression"')->name('parcelles.proprietaires.destroy');

    // La cartographie globale
    Route::get('/cartographie', [CartographieController::class, 'index'])->name('cartographie.index');


    // --- DOSSIERS D'URBANISME ---
    Route::get('/dossiers-urba', [DossierUrbaController::class, 'index'])
        ->middleware('can:check-permission,"Urbanisme","lecture"')->name('dossiers-urba.index');

    Route::get('/dossiers-urba/create', [DossierUrbaController::class, 'create'])
        ->middleware('can:check-permission,"Urbanisme","ecriture"')->name('dossiers-urba.create');

    Route::post('/dossiers-urba', [DossierUrbaController::class, 'store'])
        ->middleware('can:check-permission,"Urbanisme","ecriture"')->name('dossiers-urba.store');

    Route::get('/dossiers-urba/{id}', [DossierUrbaController::class, 'show'])
        ->middleware('can:check-permission,"Urbanisme","lecture"')->name('dossiers-urba.show');

    Route::get('/dossiers-urba/{id}/edit', [DossierUrbaController::class, 'edit'])
        ->middleware('can:check-permission,"Urbanisme","ecriture"')->name('dossiers-urba.edit');

    Route::put('/dossiers-urba/{id}', [DossierUrbaController::class, 'update'])
        ->middleware('can:check-permission,"Urbanisme","ecriture"')->name('dossiers-urba.update');

    Route::delete('/dossiers-urba/{id}', [DossierUrbaController::class, 'destroy'])
        ->middleware('can:check-permission,"Urbanisme","suppression"')->name('dossiers-urba.destroy');

    // Route optionnelle pour charger des documents spécifiques d'urbanisme
    Route::post('/dossiers-urba/{id}/documents', [DossierUrbaController::class, 'uploadDocument'])
        ->middleware('can:check-permission,"Urbanisme","ecriture"')->name('dossiers-urba.documents.store');
    Route::delete('/dossiers-urba/documents/{id}', [DossierUrbaController::class, 'destroyDocument'])
        ->middleware('can:check-permission,"Urbanisme","suppression"')->name('dossiers-urba.documents.destroy');

    // ========================================================
    // RESTRICTION CRITIQUE : GESTION DES HABILITATIONS (ADMIN SYSTEME ONLY)
    // ========================================================
    Route::middleware(['role:Administrateur,Responsable technique'])->group(function () {
        Route::get('/admin/habilitations', [HabilitationController::class, 'index'])->name('admin.habilitations.index');
        Route::post('/admin/habilitations/update', [HabilitationController::class, 'update'])->name('admin.habilitations.update');
        Route::get('/admin/preventif/generer', function () {
            // Déclenche l'exécution de la commande de maintenance directement depuis le code PHP
            Illuminate\Support\Facades\Artisan::call('app:generer-preventif');

            return redirect()->route('interventions.index')
                ->with('success', 'Le moteur de maintenance préventive a scanné le patrimoine. Les ordres de travail urgents ont été générés !');
        })->name('admin.preventif.generer');
    });

});
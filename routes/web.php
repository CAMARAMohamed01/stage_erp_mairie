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

Route::get('/', function () {
    return view('welcome');
});

// --- ROUTES PUBLIQUES ---
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// --- ROUTES PRIVÉES (Nécessitent d'être connecté) ---
Route::middleware('auth')->group(function () {

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

    // Remplace ton ancienne ligne par celle-ci :
    Route::post('/equipements/{idEquipement}/documents', [EquipementController::class, 'uploadDocument'])
        ->middleware('can:check-permission,"Patrimoine & Equipements","ecriture"')
        ->name('equipements.documents.store');
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
    // ========================================================
    // 💳 SECURISE : MODULE DOSSIERS FINANCIERS (COMPTABILITE)
    // ========================================================

    Route::get('/finances/dossiers', [DossierFinancierController::class, 'index'])->middleware('can:check-permission,"Finances & Achats","lecture"')->name('dossiers-financiers.index');
    Route::get('/finances/dossiers/create', [DossierFinancierController::class, 'create'])->middleware('can:check-permission,"Finances & Achats","ecriture"')->name('dossiers-financiers.create');
    Route::post('/finances/dossiers', [DossierFinancierController::class, 'store'])->middleware('can:check-permission,"Finances & Achats","ecriture"')->name('dossiers-financiers.store');
    Route::get('/finances/dossiers/{id}', [DossierFinancierController::class, 'show'])->middleware('can:check-permission,"Finances & Achats","lecture"')->name('dossiers-financiers.show');
    Route::post('/finances/dossiers/{id}/ligne', [DossierFinancierController::class, 'ajouterLigne'])->middleware('can:check-permission,"Finances & Achats","ecriture"')->name('dossiers-financiers.ligne.store');

    // ========================================================
    // 🚧 MODULE VOIES & RÉSEAUX DIVERS (VRD) -
    // ========================================================
    // Les routes statiques d'abord
    Route::get('/voies', [VoieController::class, 'index'])->name('voies.index');
    Route::get('/voies/create', [VoieController::class, 'create'])->name('voies.create');
    Route::post('/voies', [VoieController::class, 'store'])->name('voies.store');

    // Les routes dynamiques avec {id} ensuite
    Route::get('/voies/{id}', [VoieController::class, 'show'])->name('voies.show');
    Route::get('/voies/{id}/edit', [VoieController::class, 'edit'])->name('voies.edit');
    Route::put('/voies/{id}', [VoieController::class, 'update'])->name('voies.update');
    Route::delete('/voies/{id}', [VoieController::class, 'destroy'])->name('voies.destroy');
    // ========================================================
    // 🚧 MODULE TRONÇONS (PARTIE GESTION DES VOIES)

    Route::get('/troncons/create', [TronconController::class, 'create'])->name('troncons.create');
    Route::post('/troncons', [TronconController::class, 'store'])->name('troncons.store');
    Route::post('/troncons/{idTroncon}/documents', [TronconController::class, 'uploadDocument'])
        ->name('troncons.documents.store');
    Route::get('/troncons/{id}', [TronconController::class, 'show'])->name('troncons.show');
    Route::get('/troncons/{id}/edit', [TronconController::class, 'edit'])->name('troncons.edit');
    Route::put('/troncons/{id}', [TronconController::class, 'update'])->name('troncons.update');
    Route::delete('/troncons/{id}', [TronconController::class, 'destroy'])->name('troncons.destroy');

    //Ouvrages d'art
    Route::get('/ouvrages', [OuvrageController::class, 'index'])->name('ouvrages.index');
    Route::get('/ouvrages/create', [OuvrageController::class, 'create'])->name('ouvrages.create');
    Route::post('/ouvrages', [OuvrageController::class, 'store'])->name('ouvrages.store');
    Route::get('/ouvrages/{id}', [OuvrageController::class, 'show'])->name('ouvrages.show');
    Route::get('/ouvrages/{id}/edit', [OuvrageController::class, 'edit'])->name('ouvrages.edit');
    Route::put('/ouvrages/{id}', [OuvrageController::class, 'update'])->name('ouvrages.update');
    Route::delete('/ouvrages/{id}', [OuvrageController::class, 'destroy'])->name('ouvrages.destroy');

    // Gestion des communes partenaires pour un ouvrage
    Route::post('/ouvrages/{ouvrage}/communes', [OuvrageController::class, 'addCommune'])->name('ouvrages.communes.store');
    Route::delete('/ouvrages/{ouvrage}/communes/{commune}', [OuvrageController::class, 'removeCommune'])->name('ouvrages.communes.destroy');

    // Référentiel des Communes Partenaires
    Route::get('/communes', [\App\Http\Controllers\CommunePartenaireController::class, 'index'])->name('communes.index');
    Route::get('/communes/create', [\App\Http\Controllers\CommunePartenaireController::class, 'create'])->name('communes.create');
    Route::post('/communes', [\App\Http\Controllers\CommunePartenaireController::class, 'store'])->name('communes.store');

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

    // La cartographie globale
    Route::get('/cartographie', [CartographieController::class, 'index'])->name('cartographie.index');
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
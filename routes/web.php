<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardTechniqueController;
use App\Http\Controllers\SignalementController;
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
    Route::get('/citoyens/{id}', [TiersController::class, 'show'])->name('tiers.show');


    // ========================================================
    // 🔏 SECURISE : MODULE SIGNALEMENTS VIA LA MATRICE DE DROITS
    // ========================================================

    // Lecture (Index, Fiche Détails, Export)
    Route::get('/signalements', [SignalementController::class, 'index'])->middleware('can:check-permission,"Signalements","lecture"')->name('signalements.index');
    Route::get('/signalements/export/excel', [SignalementController::class, 'exportExcel'])->middleware('can:check-permission,"Signalements","lecture"')->name('signalements.excel');
    Route::get('/signalements/{id}', [SignalementController::class, 'show'])->middleware('can:check-permission,"Signalements","lecture"')->name('signalements.show');
    Route::get('/signalement/{id}/pdf', [SignalementController::class, 'imprimer'])->middleware('can:check-permission,"Signalements","lecture"')->name('signalement.pdf');

    // Écriture / Création
    Route::get('/signalement/nouveau', [SignalementController::class, 'create'])->middleware('can:check-permission,"Signalements","ecriture"')->name('signalements.create');
    Route::post('/signalement/nouveau', [SignalementController::class, 'store'])->middleware('can:check-permission,"Signalements","ecriture"')->name('signalements.store');

    // Écriture / Modification & Prise en charge
    Route::get('/signalements/{id}/edit', [SignalementController::class, 'edit'])->middleware('can:check-permission,"Signalements","ecriture"')->name('signalements.edit');
    Route::put('/signalements/{id}', [SignalementController::class, 'update'])->middleware('can:check-permission,"Signalements","ecriture"')->name('signalements.update');
    Route::patch('/signalement/{id}/prendre-en-charge', [SignalementController::class, 'prendreEnCharge'])->middleware('can:check-permission,"Signalements","ecriture"')->name('signalement.prendre-en-charge');
    Route::post('/signalement/{id}/creer-intervention', [SignalementController::class, 'creerIntervention'])->middleware('can:check-permission,"Signalements","ecriture"')->name('signalement.creer-intervention');

    // Suppression
    Route::delete('/signalements/{id}', [SignalementController::class, 'destroy'])->middleware('can:check-permission,"Signalements","suppression"')->name('signalements.destroy');


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
    });
    // Relevés des compteurs
    Route::prefix('compteurs/{idCompteur}/releves')->name('compteurs.releves.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ReleveCompteurController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\ReleveCompteurController::class, 'store'])->name('store');
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
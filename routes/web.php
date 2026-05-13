<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardTechniqueController;
use App\Http\Controllers\SignalementController;
use App\Http\Controllers\InterventionController;
use App\Http\Controllers\EquipementController;
use App\Http\Controllers\AuthController;


Route::get('/', function () {
    return view('welcome');
});



// --- ROUTES PUBLIQUES ---
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// --- ROUTES PRIVÉES (Nécessitent d'être connecté) ---
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


    // Avant :
    Route::get('/technique/dashboard', [DashboardTechniqueController::class, 'index']);

    // Après (on ajoute ->name) :
    Route::get('/technique/dashboard', [DashboardTechniqueController::class, 'index'])->name('technique.dashboard');

    // Routes de création de signalement (formulaire)
    Route::get('/signalement/nouveau', [SignalementController::class, 'create'])->name('signalements.create');
    Route::post('/signalement/nouveau', [SignalementController::class, 'store'])->name('signalements.store');

    // Route pour voir le détail d'un signalement
    Route::get('/signalements/{id}', [SignalementController::class, 'show'])->name('signalements.show');

    // Route pour la liste globale des signalements
    Route::get('/signalements', [SignalementController::class, 'index'])->name('signalements.index');

    Route::patch('/signalement/{id}/prendre-en-charge', [SignalementController::class, 'prendreEnCharge'])->name('signalement.prendre-en-charge');

    Route::post('/signalement/{id}/creer-intervention', [SignalementController::class, 'creerIntervention'])->name('signalement.creer-intervention');

    Route::get('/signalements/export/excel', [SignalementController::class, 'exportExcel'])->name('signalements.excel');
    Route::get('/signalement/{id}/pdf', [SignalementController::class, 'imprimer'])->name('signalement.pdf');

    Route::get('/signalements/{id}/edit', [SignalementController::class, 'edit'])->name('signalements.edit');
    Route::put('/signalements/{id}', [SignalementController::class, 'update'])->name('signalements.update');
    Route::delete('/signalements/{id}', [SignalementController::class, 'destroy'])->name('signalements.destroy');

    // Routes pour les interventions
    Route::get('/interventions/create', [InterventionController::class, 'create'])->name('interventions.create');
    // Enregistrer la nouvelle intervention
    Route::post('/interventions', [InterventionController::class, 'store'])->name('interventions.store');


    Route::get('/interventions', [InterventionController::class, 'index'])->name('interventions.index');
    Route::get('/interventions/{id}', [InterventionController::class, 'show'])->name('interventions.show');
    Route::patch('/interventions/{id}/cloturer', [InterventionController::class, 'cloturer'])->name('interventions.cloturer');

    Route::get('/interventions/{id}/pdf', [InterventionController::class, 'imprimer'])->name('interventions.pdf');
    Route::get('/interventions/export/excel', [InterventionController::class, 'exportExcel'])->name('interventions.excel');

    Route::middleware(['role:Technicien, Responsable technique, Administrateur'])->group(function () {
        // Afficher le formulaire de compte-rendu
        Route::get('/interventions/{id}/cloturer', [InterventionController::class, 'formulaireCloture'])->name('interventions.cloturer.form');
        // Traiter la clôture (On utilise PATCH ou POST)
        Route::patch('/interventions/{id}/cloturer/save', [InterventionController::class, 'sauvegarderCloture'])->name('interventions.cloturer.save');
    });
    // Supprimer une intervention
    Route::middleware(['role:Administrateur, Responsable technique'])->group(function () {
        Route::delete('/interventions/{id}', [InterventionController::class, 'destroy'])->name('interventions.destroy');
        // Modifier une intervention (formulaire)
        Route::get('/interventions/{id}/edit', [InterventionController::class, 'edit'])->name('interventions.edit');
        // Enregistrer les modifications
        Route::put('/interventions/{id}', [InterventionController::class, 'update'])->name('interventions.update');

        // Des memes pour les signalements et les équipements

    });




    // Route pour les équipements
    Route::get('/equipements', [EquipementController::class, 'index'])->name('equipements.index');

    // Afficher le formulaire de création
    Route::get('/equipements/create', [EquipementController::class, 'create'])->name('equipements.create');

    // Enregistrer les données en base
    Route::post('/equipements', [EquipementController::class, 'store'])->name('equipements.store');
    // Afficher la fiche détaillée d'un équipement
    Route::get('/equipements/{id}', [EquipementController::class, 'show'])->name('equipements.show');

    // Afficher le formulaire de modification
    Route::get('/equipements/{id}/edit', [EquipementController::class, 'edit'])->name('equipements.edit');
    // Mettre à jour les données
    Route::put('/equipements/{id}', [EquipementController::class, 'update'])->name('equipements.update');
    // Supprimer un équipement
    Route::delete('/equipements/{id}', [EquipementController::class, 'destroy'])->name('equipements.destroy');


    Route::get('/citoyens', [App\Http\Controllers\TiersController::class, 'index'])->name('tiers.index');
    Route::get('/citoyens/{id}', [App\Http\Controllers\TiersController::class, 'show'])->name('tiers.show');
});
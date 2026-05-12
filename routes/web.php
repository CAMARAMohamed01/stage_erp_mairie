<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardTechniqueController;
use App\Http\Controllers\SignalementController;
use App\Http\Controllers\InterventionController;
use App\Http\Controllers\EquipementController;



Route::get('/', function () {
    return view('welcome');
});


// L'URL sera : http://localhost:8000/technique/dashboard
// Avant :
Route::get('/technique/dashboard', [DashboardTechniqueController::class, 'index']);

// Après (on ajoute ->name) :
Route::get('/technique/dashboard', [DashboardTechniqueController::class, 'index'])->name('technique.dashboard');

// Routes de création de signalement (formulaire)
Route::get('/signalement/nouveau', [SignalementController::class, 'create'])->name('signalements.create');
Route::post('/signalement/nouveau', [SignalementController::class, 'store'])->name('signalements.store');

// Route pour voir le détail d'un signalement
Route::get('/signalement/{id}', [SignalementController::class, 'show'])->name('signalement.show');

// Route pour la liste globale des signalements
Route::get('/signalements', [SignalementController::class, 'index'])->name('signalements.index');

Route::patch('/signalement/{id}/prendre-en-charge', [SignalementController::class, 'prendreEnCharge'])->name('signalement.prendre-en-charge');

Route::post('/signalement/{id}/creer-intervention', [SignalementController::class, 'creerIntervention'])->name('signalement.creer-intervention');

Route::get('/signalements/export/excel', [SignalementController::class, 'exportExcel'])->name('signalements.excel');
Route::get('/signalement/{id}/pdf', [SignalementController::class, 'imprimer'])->name('signalement.pdf');


// Routes pour les interventions
Route::get('/interventions', [InterventionController::class, 'index'])->name('interventions.index');
Route::get('/interventions/{id}', [InterventionController::class, 'show'])->name('interventions.show');
Route::patch('/interventions/{id}/cloturer', [InterventionController::class, 'cloturer'])->name('interventions.cloturer');

Route::get('/interventions/{id}/pdf', [InterventionController::class, 'imprimer'])->name('interventions.pdf');
Route::get('/interventions/export/excel', [InterventionController::class, 'exportExcel'])->name('interventions.excel');

// Afficher le formulaire de compte-rendu
Route::get('/interventions/{id}/cloturer', [InterventionController::class, 'formulaireCloture'])->name('interventions.cloturer.form');
// Traiter la clôture (On utilise PATCH ou POST)
Route::patch('/interventions/{id}/cloturer/save', [InterventionController::class, 'sauvegarderCloture'])->name('interventions.cloturer.save');

// Route pour les équipements
Route::get('/equipements', [EquipementController::class, 'index'])->name('equipements.index');

// Afficher le formulaire de création
Route::get('/equipements/create', [EquipementController::class, 'create'])->name('equipements.create');

// Enregistrer les données en base
Route::post('/equipements', [EquipementController::class, 'store'])->name('equipements.store');
// Afficher la fiche détaillée d'un équipement
Route::get('/equipements/{id}', [EquipementController::class, 'show'])->name('equipements.show');
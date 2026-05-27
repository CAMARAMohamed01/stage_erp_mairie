<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjetController;
use App\Http\Controllers\ReleveCompteurController;
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
use App\Http\Controllers\SecteurController;
use App\Http\Controllers\ZoneController;
use App\Http\Controllers\ParcelleController;
use App\Http\Controllers\CartographieController;
use App\Http\Controllers\CommunePartenaireController;

Route::get('/', function () {
    return view('welcome');
});

// --- ROUTES PUBLIQUES ---
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// --- ROUTES PRIVÉES (Nécessitent d'être connecté) ---
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/technique/dashboard', [DashboardTechniqueController::class, 'index'])->name('technique.dashboard');
    Route::get('/cartographie', [CartographieController::class, 'index'])->name('cartographie.index');

    // ==========================================
    // 👥 MODULE : CITOYENS & ENTREPRISES (TIERS)
    // ==========================================

    // Citoyens
    Route::get('/citoyens', [TiersController::class, 'index'])->name('tiers.index');
    Route::get('/citoyens/{id}', [TiersController::class, 'show'])->name('tiers.show');
    Route::resource('tiers', TiersController::class)->except(['index', 'show']);

    // Entreprises
    Route::prefix('entreprises')->name('tiers.')->group(function () {
        Route::get('/', [TiersController::class, 'entreprises'])->name('entreprises');
        Route::get('/create', [TiersController::class, 'createEntreprise'])->name('create_entreprise');
        Route::post('/', [TiersController::class, 'storeEntreprise'])->name('store_entreprise');
        Route::get('/{id}', [TiersController::class, 'showEntreprise'])->name('show_entreprise');
        Route::get('/{id}/edit', [TiersController::class, 'editEntreprise'])->name('edit_entreprise');
        Route::put('/{id}', [TiersController::class, 'updateEntreprise'])->name('update_entreprise');
    });

    // Comptes Bancaires
    Route::prefix('tiers/{id_tiers}/comptes')->name('tiers.comptes.')->group(function () {
        Route::get('/create', [TiersController::class, 'createCompte'])->name('create');
        Route::post('/', [TiersController::class, 'storeCompte'])->name('store');
    });
    Route::delete('/comptes/{id}', [TiersController::class, 'destroyCompte'])->name('tiers.comptes.destroy');

    // GESTION DES DOCUMENTS GÉNÉRAUX DES TIERS
    Route::prefix('tiers/{id_tiers}/documents')->name('tiers.documents.')->group(function () {
        Route::get('/create', [TiersController::class, 'createDocument'])->name('create');
        Route::post('/', [TiersController::class, 'storeDocument'])->name('store');
    });
    // ==========================================
    // 🔧 MODULE : ACTIONS
    // ==========================================
    Route::middleware('can:check-permission,"actions","lecture"')->group(function () {
        Route::get('/actions', [ActionController::class, 'index'])->name('actions.index');
        Route::get('/actions/export/excel', [ActionController::class, 'exportExcel'])->name('actions.excel');
        Route::get('/actions/{id}', [ActionController::class, 'show'])->name('actions.show');
        Route::get('/action/{id}/pdf', [ActionController::class, 'imprimer'])->name('action.pdf');
    });

    Route::middleware('can:check-permission,"actions","ecriture"')->group(function () {
        Route::get('/action/nouveau', [ActionController::class, 'create'])->name('actions.create');
        Route::post('/action/nouveau', [ActionController::class, 'store'])->name('actions.store');
        Route::get('/actions/{id}/edit', [ActionController::class, 'edit'])->name('actions.edit');
        Route::put('/actions/{id}', [ActionController::class, 'update'])->name('actions.update');
        Route::patch('/action/{id}/prendre-en-charge', [ActionController::class, 'prendreEnCharge'])->name('action.prendre-en-charge');
        Route::post('/action/{id}/creer-intervention', [ActionController::class, 'creerIntervention'])->name('action.creer-intervention');
    });

    Route::middleware('can:check-permission,"actions","suppression"')->group(function () {
        Route::delete('/actions/{id}', [ActionController::class, 'destroy'])->name('actions.destroy');
    });

    // ==========================================
    // 🏗️ MODULE : PATRIMOINE & TRAVAUX (PROJETS)
    // ==========================================
    Route::middleware('can:check-permission,"Patrimoine & Travaux","lecture"')->group(function () {
        Route::resource('projets', ProjetController::class)->only(['index', 'show']);
    });
    Route::middleware('can:check-permission,"Patrimoine & Travaux","ecriture"')->group(function () {
        Route::resource('projets', ProjetController::class)->only(['create', 'store', 'edit', 'update']);
    });
    Route::middleware('can:check-permission,"Patrimoine & Travaux","suppression"')->group(function () {
        Route::resource('projets', ProjetController::class)->only(['destroy']);
    });

    // ==========================================
    // 🛠️ MODULE : INTERVENTIONS
    // ==========================================
    Route::middleware('can:check-permission,"Interventions","lecture"')->group(function () {

        Route::get('/interventions/export/excel', [InterventionController::class, 'exportExcel'])->name('interventions.excel');
        Route::get('/interventions/{id}/pdf', [InterventionController::class, 'imprimer'])->name('interventions.pdf');
    });

    Route::middleware('can:check-permission,"Interventions","ecriture"')->group(function () {
        Route::resource('interventions', InterventionController::class)->only(['create', 'store', 'edit', 'update']);
        Route::resource('interventions', InterventionController::class)->only(['index', 'show']);
        Route::patch('/interventions/{id}/cloturer', [InterventionController::class, 'cloturer'])->name('interventions.cloturer');
        Route::get('/interventions/{id}/cloturer', [InterventionController::class, 'formulaireCloture'])->name('interventions.cloturer.form');
        Route::patch('/interventions/{id}/cloturer/save', [InterventionController::class, 'sauvegarderCloture'])->name('interventions.cloturer.save');
        Route::post('/interventions/{id}/materiel', [InterventionController::class, 'ajouterMateriel'])->name('interventions.materiel.store');
    });

    Route::middleware('can:check-permission,"Interventions","suppression"')->group(function () {
        Route::resource('interventions', InterventionController::class)->only(['destroy']);
    });

    // ==========================================
    // 🏛️ MODULE : PATRIMOINE & EQUIPEMENTS
    // ==========================================
    Route::middleware('can:check-permission,"Patrimoine & Equipements","lecture"')->group(function () {
        Route::resource('batiments', BatimentController::class)->only(['index', 'show']);
        Route::get('/batiments/{id}/documents', [BatimentController::class, 'documents'])->name('batiments.documents');
        Route::resource('lieux', LieuController::class)->only(['index', 'show']);
        Route::resource('locaux', LocalController::class)->only(['index', 'show']);
        Route::resource('equipements', EquipementController::class)->only(['index', 'show']);
        Route::resource('compteurs', CompteurController::class)->only(['index', 'show']);

        Route::prefix('compteurs/{idCompteur}/releves')->name('compteurs.releves.')->group(function () {
            Route::get('/', [ReleveCompteurController::class, 'index'])->name('index');
            Route::get('/export-pdf', [ReleveCompteurController::class, 'exportPdf'])->name('export.pdf');
        });
    });

    Route::middleware('can:check-permission,"Patrimoine & Equipements","ecriture"')->group(function () {
        Route::resource('batiments', BatimentController::class)->only(['create', 'store', 'edit', 'update']);
        Route::resource('lieux', LieuController::class)->only(['create', 'store', 'edit', 'update']);
        Route::resource('locaux', LocalController::class)->only(['create', 'store', 'edit', 'update']);
        Route::resource('equipements', EquipementController::class)->only(['create', 'store', 'edit', 'update']);
        Route::resource('compteurs', CompteurController::class)->only(['create', 'store', 'edit', 'update']);

        // Création rapide (AJAX)
        Route::post('/api/quick-adresse', [BatimentController::class, 'quickStoreAdresse'])->name('api.adresse.store');
        Route::post('/api/quick-parcelle', [BatimentController::class, 'quickStoreParcelle'])->name('api.parcelle.store');
        Route::post('/api/quick-tiers', [BatimentController::class, 'quickStoreTiers'])->name('api.tiers.store');

        // Uploads de documents (Centralisés)
        Route::post('/interventions/{idInt}/documents', [InterventionController::class, 'uploadDocument'])->name('interventions.documents.store');
        Route::post('/batiments/{idBatiment}/documents', [BatimentController::class, 'uploadDocument'])->name('batiments.documents.store');
        Route::post('/lieux/{idLieu}/documents', [LieuController::class, 'uploadDocument'])->name('lieux.documents.store');
        Route::post('/locaux/{idLocal}/documents', [LocalController::class, 'uploadDocument'])->name('locaux.documents.store');
        Route::post('/equipements/{idEquipement}/documents', [EquipementController::class, 'uploadDocument'])->name('equipements.documents.store');
        Route::post('/compteurs/{idCompteur}/documents', [CompteurController::class, 'uploadDocument'])->name('compteurs.documents.store');

        Route::post('compteurs/{idCompteur}/releves', [ReleveCompteurController::class, 'store'])->name('compteurs.releves.store');
    });

    Route::middleware('can:check-permission,"Patrimoine & Equipements","suppression"')->group(function () {
        Route::resource('batiments', BatimentController::class)->only(['destroy']);
        Route::resource('lieux', LieuController::class)->only(['destroy']);
        Route::resource('locaux', LocalController::class)->only(['destroy']);
        Route::resource('equipements', EquipementController::class)->only(['destroy']);
        Route::resource('compteurs', CompteurController::class)->only(['destroy']);

        Route::delete('/compteurs/documents/{idDocument}', [CompteurController::class, 'deleteDocument'])->name('compteurs.documents.destroy');

        // Document Global
        Route::delete('/documents/global/{id}', function ($id) {
            $document = \App\Models\Document::findOrFail($id);
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($document->chemin_stockage)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($document->chemin_stockage);
            }
            $document->delete();
            return back()->with('success', 'Document supprimé définitivement.');
        })->name('documents.global.destroy');
    });

    // ==========================================
    // 🕊️ MODULE : ÉTAT CIVIL & CIMETIÈRES
    // ==========================================
    Route::middleware('can:check-permission,"État Civil & Cimetières","lecture"')->group(function () {
        Route::resource('emplacements', EmplacementFuneraireController::class)->only(['index']);
        Route::resource('concessions', ConcessionCimetiereController::class)->only(['index', 'show']);
    });

    Route::middleware('can:check-permission,"État Civil & Cimetières","ecriture"')->group(function () {
        Route::resource('emplacements', EmplacementFuneraireController::class)->only(['create', 'store', 'edit', 'update']);
        Route::resource('concessions', ConcessionCimetiereController::class)->only(['create', 'store', 'edit', 'update']);
    });

    Route::middleware('can:check-permission,"État Civil & Cimetières","suppression"')->group(function () {
        Route::resource('emplacements', EmplacementFuneraireController::class)->only(['destroy']);
        Route::resource('concessions', ConcessionCimetiereController::class)->only(['destroy']);
    });

    // ==========================================
    // 💰 MODULE : FINANCES & ACHATS
    // ==========================================
    Route::middleware('can:check-permission,"Finances & Achats","lecture"')->group(function () {
        Route::resource('contrats', ContratController::class)->only(['index', 'show']);
        Route::get('/finances/dossiers', [DossierFinancierController::class, 'index'])->name('dossiers-financiers.index');
        Route::get('/finances/dossiers/{id}', [DossierFinancierController::class, 'show'])->name('dossiers-financiers.show');
    });

    Route::middleware('can:check-permission,"Finances & Achats","ecriture"')->group(function () {
        Route::resource('contrats', ContratController::class)->only(['create', 'store', 'edit', 'update']);
        Route::post('/contrats/{id}/ajouter-location', [ContratController::class, 'ajouterLocation'])->name('contrats.location.store');

        Route::get('/finances/dossiers/create', [DossierFinancierController::class, 'create'])->name('dossiers-financiers.create');
        Route::post('/finances/dossiers', [DossierFinancierController::class, 'store'])->name('dossiers-financiers.store');
        Route::post('/finances/dossiers/{id}/ligne', [DossierFinancierController::class, 'ajouterLigne'])->name('dossiers-financiers.ligne.store');
    });

    Route::middleware('can:check-permission,"Finances & Achats","suppression"')->group(function () {
        Route::resource('contrats', ContratController::class)->only(['destroy']);
    });

    // ==========================================
    // 🚧 MODULE : VOIES & RÉSEAUX DIVERS (VRD)
    // ==========================================
    Route::resource('voies', VoieController::class);
    Route::resource('ouvrages', OuvrageController::class);
    Route::resource('secteurs', SecteurController::class);
    Route::resource('zones', ZoneController::class);
    Route::resource('parcelles', ParcelleController::class);
    Route::resource('communes', CommunePartenaireController::class)->only(['index', 'create', 'store']);
    Route::resource('troncons', TronconController::class)->except(['index']);

    // Relations & Documents VRD
    Route::post('/ouvrages/{ouvrage}/communes', [OuvrageController::class, 'addCommune'])->name('ouvrages.communes.store');
    Route::delete('/ouvrages/{ouvrage}/communes/{commune}', [OuvrageController::class, 'removeCommune'])->name('ouvrages.communes.destroy');
    Route::post('/troncons/{idTroncon}/documents', [TronconController::class, 'uploadDocument'])->name('troncons.documents.store');

    // ==========================================
    // ⚙️ MODULE : ADMINISTRATION (ADMIN ONLY)
    // ==========================================
    Route::middleware(['role:Administrateur,Responsable technique'])->group(function () {
        Route::get('/admin/habilitations', [HabilitationController::class, 'index'])->name('admin.habilitations.index');
        Route::post('/admin/habilitations/update', [HabilitationController::class, 'update'])->name('admin.habilitations.update');

        Route::get('/admin/preventif/generer', function () {
            Illuminate\Support\Facades\Artisan::call('app:generer-preventif');
            return redirect()->route('interventions.index')
                ->with('success', 'Le moteur de maintenance préventive a scanné le patrimoine. Les ordres de travail urgents ont été générés !');
        })->name('admin.preventif.generer');
    });

});
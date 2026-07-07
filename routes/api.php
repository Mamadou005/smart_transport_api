<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VoyageController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\BagageController;
use App\Http\Controllers\SignalementController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\BagagisteController;
use App\Http\Controllers\RapportController;

// ════════════════════════════════════════════
// ROUTES PUBLIQUES
// ════════════════════════════════════════════
Route::post('/login',    [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// ════════════════════════════════════════════
// ROUTES PROTÉGÉES
// ════════════════════════════════════════════
Route::middleware('auth:sanctum')->group(function () {

    // ── Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // ── Profil
    Route::get('/profile',           [ProfileController::class, 'show']);
    Route::put('/profile',           [ProfileController::class, 'update']);
    Route::post('/profile/password', [ProfileController::class, 'changerMotDePasse']);

    // ── Voyages (lecture seule)
    Route::resource('voyages', VoyageController::class)
        ->only(['index','show'])
        ->names([
            'index' => 'voyages.public.index',
            'show'  => 'voyages.public.show',
        ]);

    // ── Notifications
    Route::get('/notifications',             [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/non-lues',    [NotificationController::class, 'nonLues'])->name('notifications.nonlues');
    Route::put('/notifications/toutes-lues', [NotificationController::class, 'marquerToutesLues'])->name('notifications.lues');
    Route::put('/notifications/{id}/lue',    [NotificationController::class, 'marquerLue'])->name('notifications.lue');

    // ════════════════════════════════════════════
    // ESPACE PASSAGER
    // ════════════════════════════════════════════
    Route::middleware('role:passager,admin')->group(function () {
        Route::resource('reservations', ReservationController::class)
            ->except(['create','edit'])
            ->names([
                'index'   => 'passager.reservations.index',
                'store'   => 'passager.reservations.store',
                'show'    => 'passager.reservations.show',
                'update'  => 'passager.reservations.update',
                'destroy' => 'passager.reservations.destroy',
            ]);

        Route::post('/paiements/initier',               [PaiementController::class, 'initier'])->name('passager.paiements.initier');
        Route::post('/paiements/{reference}/confirmer', [PaiementController::class, 'confirmer'])->name('passager.paiements.confirmer');
        Route::get('/paiements/historique',             [PaiementController::class, 'historique'])->name('passager.paiements.historique');

        Route::resource('bagages', BagageController::class)
            ->except(['create','edit'])
            ->names([
                'index'   => 'passager.bagages.index',
                'store'   => 'passager.bagages.store',
                'show'    => 'passager.bagages.show',
                'update'  => 'passager.bagages.update',
                'destroy' => 'passager.bagages.destroy',
            ]);

        Route::post('/bagages/{id}/localisation',[BagageController::class, 'addLocalisation'])->name('passager.bagages.localisation');

        Route::resource('signalements', SignalementController::class)
            ->except(['create','edit'])
            ->names([
                'index'   => 'passager.signalements.index',
                'store'   => 'passager.signalements.store',
                'show'    => 'passager.signalements.show',
                'update'  => 'passager.signalements.update',
                'destroy' => 'passager.signalements.destroy',
            ]);
    });

    // ════════════════════════════════════════════
    // ESPACE AGENT TERMINAL
    // ════════════════════════════════════════════
    Route::middleware('role:agent,admin')->prefix('agent')->group(function () {
        Route::post('/scanner',             [AgentController::class, 'scanner'])->name('agent.scanner');
        Route::get('/reservations-du-jour', [AgentController::class, 'reservationsDuJour'])->name('agent.reservations.jour');
        Route::get('/stats',                [AgentController::class, 'statsDuJour'])->name('agent.stats');
    });

    // ════════════════════════════════════════════
    // ESPACE AGENT BAGAGISTE
    // ════════════════════════════════════════════
    Route::middleware('role:bagagiste,admin')->prefix('bagagiste')->group(function () {
        Route::post('/bagages',             [BagagisteController::class, 'enregistrerBagage'])->name('bagagiste.bagages.store');
        Route::get('/bagages',              [BagagisteController::class, 'listeBagages'])->name('bagagiste.bagages.index');
        Route::get('/bagages/{id}',         [BagagisteController::class, 'show'])->name('bagagiste.bagages.show');
        Route::put('/bagages/{id}/statut',  [BagagisteController::class, 'updateStatut'])->name('bagagiste.bagages.updateStatut');
        Route::post('/chercher-reservation',[BagagisteController::class, 'chercherReservation'])->name('bagagiste.reservations.search');
        Route::get('/stats',                [BagagisteController::class, 'stats'])->name('bagagiste.stats');
        Route::get('/signalements',         [BagagisteController::class, 'listeSignalements'])->name('bagagiste.signalements.index');
        Route::put('/signalements/{id}/statut', [BagagisteController::class, 'updateSignalement'])->name('bagagiste.signalements.update');
        Route::put('/signalements/{id}/confirmer-perdu', [BagagisteController::class, 'confirmerPerteDefinitive'])->name('bagagiste.signalements.perte');
    });

    // ════════════════════════════════════════════
    // ESPACE ADMIN
    // ════════════════════════════════════════════
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/stats', [AdminController::class, 'stats'])->name('admin.stats');

        Route::resource('utilisateurs', AdminController::class)
            ->except(['create','edit'])
            ->names([
                'index'   => 'admin.utilisateurs.index',
                'store'   => 'admin.utilisateurs.store',
                'show'    => 'admin.utilisateurs.show',
                'update'  => 'admin.utilisateurs.update',
                'destroy' => 'admin.utilisateurs.destroy',
            ]);

        Route::resource('voyages', AdminController::class)
            ->except(['create','edit'])
            ->names([
                'index'   => 'admin.voyages.index',
                'store'   => 'admin.voyages.store',
                'show'    => 'admin.voyages.show',
                'update'  => 'admin.voyages.update',
                'destroy' => 'admin.voyages.destroy',
            ]);

        Route::resource('bagages', AdminController::class)
            ->except(['create','edit'])
            ->names([
                'index'   => 'admin.bagages.index',
                'store'   => 'admin.bagages.store',
                'show'    => 'admin.bagages.show',
                'update'  => 'admin.bagages.update',
                'destroy' => 'admin.bagages.destroy',
            ]);

        Route::resource('signalements', AdminController::class)
            ->except(['create','edit'])
            ->names([
                'index'   => 'admin.signalements.index',
                'store'   => 'admin.signalements.store',
                'show'    => 'admin.signalements.show',
                'update'  => 'admin.signalements.update',
                'destroy' => 'admin.signalements.destroy',
            ]);

        Route::get('/rapports/stats', [RapportController::class, 'stats'])->name('admin.rapports.stats');
    });
});

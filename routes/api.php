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
// ROUTES PUBLIQUES (sans authentification)
// ════════════════════════════════════════════
Route::post('/login',    [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// ════════════════════════════════════════════
// ROUTES PROTÉGÉES (token JWT requis)
// ════════════════════════════════════════════
Route::middleware('auth:sanctum')->group(function () {

    // ── Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // ── Profil
    Route::get('/profile',           [ProfileController::class, 'show']);
    Route::put('/profile',           [ProfileController::class, 'update']);
    Route::post('/profile/password', [ProfileController::class, 'changerMotDePasse']);
    Route::delete('/profile',        [ProfileController::class, 'supprimer']);

    // ── Voyages (lecture pour tous les rôles connectés)
    Route::get('/voyages',      [VoyageController::class, 'index']);
    Route::get('/voyages/{id}', [VoyageController::class, 'show']);

    // ── Notifications (tous rôles)
    Route::get('/notifications',                     [NotificationController::class, 'index']);
    Route::get('/notifications/non-lues',            [NotificationController::class, 'nonLues']);
    Route::put('/notifications/toutes-lues',         [NotificationController::class, 'marquerToutesLues']);
    Route::put('/notifications/{id}/lue',            [NotificationController::class, 'marquerLue']);

    // ════════════════════════════════════════════
    // ESPACE PASSAGER
    // ════════════════════════════════════════════
    Route::middleware('role:passager,admin')->group(function () {
        // Réservations
        Route::get('/reservations',      [ReservationController::class, 'index']);
        Route::post('/reservations',     [ReservationController::class, 'store']);
        Route::get('/reservations/{id}', [ReservationController::class, 'show']);
        Route::delete('/reservations/{id}', [ReservationController::class, 'destroy']);

        // Paiements
        Route::post('/paiements/initier',              [PaiementController::class, 'initier']);
        Route::post('/paiements/{reference}/confirmer',[PaiementController::class, 'confirmer']);
        Route::get('/paiements/historique',            [PaiementController::class, 'historique']);

        // Bagages (le passager enregistre lui-même depuis l'app)
        Route::get('/bagages',                  [BagageController::class, 'index']);
        Route::post('/bagages',                 [BagageController::class, 'store']);
        Route::get('/bagages/{id}',             [BagageController::class, 'show']);
        Route::post('/bagages/{id}/localisation',[BagageController::class, 'addLocalisation']);

        // Signalements
        Route::get('/signalements',          [SignalementController::class, 'index']);
        Route::post('/signalements',         [SignalementController::class, 'store']);
        Route::get('/signalements/{id}',     [SignalementController::class, 'show']);
    });

    // ════════════════════════════════════════════
    // ESPACE AGENT TERMINAL (embarquement)
    // Rôles autorisés : agent, admin
    // ════════════════════════════════════════════
    Route::middleware('role:agent,admin')->prefix('agent')->group(function () {
        Route::post('/scanner',             [AgentController::class, 'scanner']);
        Route::get('/reservations-du-jour', [AgentController::class, 'reservationsDuJour']);
        Route::get('/stats',                [AgentController::class, 'statsDuJour']);
    });

    // ════════════════════════════════════════════
    // ESPACE AGENT BAGAGISTE (bagages)
    // Rôles autorisés : bagagiste, admin
    // ════════════════════════════════════════════
    Route::middleware('role:bagagiste,admin')->prefix('bagagiste')->group(function () {
        Route::post('/bagages',                  [BagagisteController::class, 'enregistrerBagage']);
        Route::get('/bagages',                   [BagagisteController::class, 'listeBagages']);
        Route::get('/bagages/{id}',              [BagagisteController::class, 'show']);
        Route::put('/bagages/{id}/statut',       [BagagisteController::class, 'updateStatut']);
        Route::post('/chercher-reservation',     [BagagisteController::class, 'chercherReservation']);
        Route::get('/stats',                     [BagagisteController::class, 'stats']);
    });

    // ════════════════════════════════════════════
    // ESPACE ADMIN
    // Rôles autorisés : admin uniquement
    // ════════════════════════════════════════════
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        // Stats dashboard
        Route::get('/stats', [AdminController::class, 'stats']);

        // Utilisateurs
        Route::get('/utilisateurs',          [AdminController::class, 'getUtilisateurs']);
        Route::post('/utilisateurs',         [AdminController::class, 'creerUtilisateur']);
        Route::put('/utilisateurs/{id}',     [AdminController::class, 'updateUtilisateur']);
        Route::delete('/utilisateurs/{id}',  [AdminController::class, 'supprimerUtilisateur']);

        // Voyages
        Route::get('/voyages',               [AdminController::class, 'getVoyages']);
        Route::post('/voyages',              [AdminController::class, 'creerVoyage']);
        Route::put('/voyages/{id}',          [AdminController::class, 'updateVoyage']);
        Route::put('/voyages/{id}/statut',   [AdminController::class, 'updateStatutVoyage']);
        Route::delete('/voyages/{id}',       [AdminController::class, 'supprimerVoyage']);

        // Bagages (vue admin)
        Route::get('/bagages',               [AdminController::class, 'getBagages']);

        // Signalements
        Route::get('/signalements',                          [AdminController::class, 'getSignalements']);
        Route::put('/signalements/{id}/statut',              [AdminController::class, 'updateSignalement']);
        Route::put('/signalements/{id}/confirmer-perdu',     [AdminController::class, 'confirmerBagagePerdu']);

        // Rapports
        Route::get('/rapports/stats', [RapportController::class, 'stats']);
    });
});

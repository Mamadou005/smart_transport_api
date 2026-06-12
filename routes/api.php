<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\VoyageController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\BagageController;
use App\Http\Controllers\SignalementController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaiementController;
use Illuminate\Support\Facades\Route;

// ── Routes Publiques (Authentification)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// ── Routes Sécurisées (Utilisateurs connectés)
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // ── Voyages (Passagers)
    Route::get('/voyages',      [VoyageController::class, 'index']);
    Route::post('/voyages',     [VoyageController::class, 'store']);
    Route::get('/voyages/{id}', [VoyageController::class, 'show']);

    // ── Réservations
    Route::get('/reservations',         [ReservationController::class, 'index']);
    Route::post('/reservations',        [ReservationController::class, 'store']);
    Route::get('/reservations/{id}',    [ReservationController::class, 'show']);
    Route::delete('/reservations/{id}', [ReservationController::class, 'destroy']);

    // ── Bagages
    Route::get('/bagages',                    [BagageController::class, 'index']);
    Route::post('/bagages',                   [BagageController::class, 'store']);
    Route::get('/bagages/{id}',               [BagageController::class, 'show']);
    Route::put('/bagages/{id}/statut',        [BagageController::class, 'updateStatut']);
    Route::post('/bagages/{id}/localisation', [BagageController::class, 'addLocalisation']);

    // ── Signalements
    Route::get('/signalements',             [SignalementController::class, 'index']);
    Route::post('/signalements',            [SignalementController::class, 'store']);
    Route::get('/signalements/{id}',        [SignalementController::class, 'show']);
    Route::put('/signalements/{id}/statut', [SignalementController::class, 'updateStatut']);

    // ── Profil
    Route::get('/profile',           [ProfileController::class, 'show']);
    Route::put('/profile',           [ProfileController::class, 'update']);
    Route::post('/profile/password', [ProfileController::class, 'changerMotDePasse']);
    Route::delete('/profile',        [ProfileController::class, 'supprimer']);

    // ── Notifications
    Route::get('/notifications',             [NotificationController::class, 'index']);
    Route::get('/notifications/non-lues',    [NotificationController::class, 'nonLues']);
    Route::put('/notifications/{id}/lue',    [NotificationController::class, 'marquerLue']);
    Route::put('/notifications/toutes-lues', [NotificationController::class, 'marquerToutesLues']);

    // ── Paiements
    Route::post('/paiements/initier',          [PaiementController::class, 'initier']);
    Route::post('/paiements/{reference}/confirmer', [PaiementController::class, 'confirmer']);
    Route::get('/paiements/historique',        [PaiementController::class, 'historique']);

    // ══════════════════════════════════
    //  ESPACE ADMIN (admin seulement)
    // ══════════════════════════════════
    Route::prefix('admin')->middleware('role:admin')->group(function () {
        Route::get('/stats',                             [AdminController::class, 'stats']);
        Route::put('/voyages/{id}/statut',               [AdminController::class, 'updateStatutVoyage']);
        Route::put('/signalements/{id}/confirmer-perdu', [AdminController::class, 'confirmerBagagePerdu']);
        Route::get('/rapports/stats',                    [RapportController::class, 'stats']);

        // Gestion des Utilisateurs
        Route::get('/utilisateurs',         [AdminController::class, 'getUtilisateurs']);
        Route::post('/utilisateurs',        [AdminController::class, 'creerUtilisateur']);
        Route::put('/utilisateurs/{id}',    [AdminController::class, 'updateUtilisateur']);
        Route::delete('/utilisateurs/{id}', [AdminController::class, 'supprimerUtilisateur']);

        // Gestion des Voyages
        Route::get('/voyages',         [AdminController::class, 'getVoyages']);
        Route::post('/voyages',        [AdminController::class, 'creerVoyage']);
        Route::put('/voyages/{id}',    [AdminController::class, 'updateVoyage']);
        Route::delete('/voyages/{id}', [AdminController::class, 'supprimerVoyage']);

        // Gestion des Signalements & Bagages
        Route::get('/signalements',             [AdminController::class, 'getSignalements']);
        Route::put('/signalements/{id}/statut', [AdminController::class, 'updateSignalement']);
        Route::get('/bagages',                  [AdminController::class, 'getBagages']);
    });

    // ══════════════════════════════════
    //  ESPACE AGENT (admin + agent)
    // ══════════════════════════════════
    Route::prefix('agent')->middleware('role:admin,agent')->group(function () {
        Route::post('/scanner',             [AgentController::class, 'scanner']);
        Route::post('/bagages',             [AgentController::class, 'enregistrerBagage']);
        Route::put('/bagages/{id}/statut',  [AgentController::class, 'updateStatutBagage']);
        Route::get('/reservations-du-jour', [AgentController::class, 'reservationsDuJour']);
        Route::get('/stats',                [AgentController::class, 'statsDuJour']);
    });

});

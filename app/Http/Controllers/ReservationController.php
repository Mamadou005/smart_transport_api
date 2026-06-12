<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Voyage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReservationController extends Controller {

    public function index(Request $request) {
        $reservations = Reservation::with('voyage')
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($reservations);
    }

    public function store(Request $request) {
        $request->validate([
            'voyage_id' => 'required|exists:voyages,id',
        ]);

        // Vérifie la capacité disponible
        $voyage = Voyage::findOrFail($request->voyage_id);
        $nbReservations = Reservation::where('voyage_id', $voyage->id)
            ->whereNotIn('statut', ['annulee'])
            ->count();

        if ($nbReservations >= $voyage->capacite) {
            return response()->json([
                'message' => 'Voyage complet, aucune place disponible.'
            ], 422);
        }

        // Création de la réservation
        $reservation = Reservation::create([
            'user_id'   => $request->user()->id,
            'voyage_id' => $request->voyage_id,
            'code_qr'   => 'RES-' . strtoupper(Str::random(8)),
            'statut'    => 'confirmee',
        ]);

        // --- Notification avec détails du voyage et date formatée ---
        $voyage = $reservation->voyage;
        \App\Http\Controllers\NotificationController::creer(
            $request->user()->id,
            "✅ Réservation confirmée ! Code : {$reservation->code_qr} | {$voyage->origine} → {$voyage->destination} le " . \Carbon\Carbon::parse($voyage->date_depart)->format('d/m/Y à H:i'),
            'confirmation'
        );
        // ------------------------------------------------------------

        return response()->json(
            $reservation->load('voyage'), 201
        );
    }

    public function show($id, Request $request) {
        $reservation = Reservation::with('voyage', 'bagages')
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);
        return response()->json($reservation);
    }

    public function destroy($id, Request $request) {
        $reservation = Reservation::where('user_id', $request->user()->id)
            ->findOrFail($id);
        $reservation->update(['statut' => 'annulee']);
        return response()->json(['message' => 'Réservation annulée']);
    }
}

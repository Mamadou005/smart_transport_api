<?php
namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Notification;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    // ── Scanner QR et valider embarquement
    public function scanner(Request $request)
    {
        $request->validate(['code_qr' => 'required|string']);

        $reservation = Reservation::with('user', 'voyage')
            ->where('code_qr', $request->code_qr)
            ->first();

        if (!$reservation) {
            return response()->json(['message' => 'QR Code invalide.'], 404);
        }

        if ($reservation->statut === 'annulee') {
            return response()->json(['message' => 'Réservation annulée.'], 422);
        }

        // Marque comme embarquée
        $reservation->update(['statut' => 'embarquee']);

        Notification::create([
            'user_id' => $reservation->user_id,
            'message' => "✈️ Embarquement validé ! Bon voyage de {$reservation->voyage->origine} vers {$reservation->voyage->destination}.",
            'type'    => 'confirmation',
            'lue'     => false,
        ]);

        return response()->json([
            'message'     => 'Embarquement validé.',
            'reservation' => $reservation,
        ]);
    }

    // ── Réservations du jour
    public function reservationsDuJour(Request $request)
    {
        $date = $request->get('date', today()->toDateString());

        $reservations = Reservation::with('user', 'voyage')
            ->whereHas('voyage', fn($q) => $q->whereDate('date_depart', $date))
            ->orderByDesc('created_at')
            ->get();

        return response()->json($reservations);
    }

    // ── Stats du jour
    public function statsDuJour()
    {
        return response()->json([
            'reservations_aujourd_hui' => Reservation::whereDate('created_at', today())->count(),
            'embarques'                => Reservation::whereDate('updated_at', today())->where('statut', 'embarquee')->count(),
        ]);
    }
}

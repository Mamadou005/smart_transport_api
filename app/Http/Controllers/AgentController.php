<?php
namespace App\Http\Controllers;

use App\Models\Bagage;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AgentController extends Controller {

    // ── Scanner un code QR/RFID
    public function scanner(Request $request) {
        $request->validate([
            'code' => 'required|string',
        ]);

        $code = strtoupper(trim($request->code)); // ← normalise le code

        // Chercher dans les réservations
        $reservation = Reservation::with(['user', 'voyage'])
            ->where('code_qr', $code)
            ->first();

        if ($reservation) {
            if ($reservation->statut === 'confirmee') {
                $reservation->update(['statut' => 'embarquee']);

                // ✅ Notifie le passager du bon déroulement de l'embarquement
                \App\Http\Controllers\NotificationController::creer(
                    $reservation->user_id,
                    "🚪 Embarquement validé ! Bon voyage de {$reservation->voyage->origine} vers {$reservation->voyage->destination}.",
                    'confirmation'
                );
            }

            return response()->json([
                'type'        => 'reservation',
                'valide'      => true,
                'code'        => $code,
                'reservation' => $reservation,
                'message'     => 'Réservation validée — Accès autorisé',
            ]);
        }

        // Chercher dans les bagages
        $bagage = Bagage::with(['reservation.user', 'reservation.voyage'])
            ->where('code_qr', $code)
            ->first();

        if ($bagage) {
            return response()->json([
                'type'    => 'bagage',
                'valide'  => true,
                'code'    => $code,
                'bagage'  => $bagage,
                'message' => 'Bagage identifié — ' . ucfirst($bagage->statut),
            ]);
        }

        return response()->json([
            'type'    => 'inconnu',
            'valide'  => false,
            'code'    => $code,
            'message' => 'Code non reconnu — Vérifiez le code saisi',
        ], 404);
    }

    // ── Enregistrer un bagage (agent)
    public function enregistrerBagage(Request $request) {
        $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'description'    => 'nullable|string',
            'poids'          => 'required|numeric|min:0',
        ]);

        $bagage = Bagage::create([
            'reservation_id' => $request->reservation_id,
            'description'    => $request->description ?? 'Bagage',
            'poids'          => $request->poids,
            'code_qr'        => 'BAG-' . strtoupper(Str::random(8)),
            'statut'         => 'enregistre',
        ]);

        return response()->json(
            $bagage->load('reservation.voyage'), 201
        );
    }

    // ── Mettre à jour statut bagage
    public function updateStatutBagage(Request $request, $id) {
        $request->validate([
            'statut' => 'required|in:enregistre,en_transit,arrive,perdu,recupere',
        ]);

        $bagage = Bagage::findOrFail($id);
        $bagage->update(['statut' => $request->statut]);

        return response()->json($bagage);
    }

    // ── Liste des réservations du jour
    public function reservationsDuJour(Request $request) {
        $date = $request->get('date', today()->toDateString());

        $reservations = Reservation::with(['user', 'voyage'])
            ->whereHas('voyage', function($q) use ($date) {
                $q->whereDate('date_depart', $date);
            })
            ->whereIn('statut', ['confirmee', 'embarquee', 'en_attente'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($reservations);
    }

    // ── Stats agent du jour
    public function statsDuJour(Request $request) {
        return response()->json([
            'reservations_today' => Reservation::whereHas('voyage', function($q) {
                $q->whereDate('date_depart', today());
            })->count(),
            'bagages_enregistres_today' => Bagage::whereDate('created_at', today())->count(),
            'bagages_en_transit' => Bagage::where('statut', 'en_transit')->count(),
            'bagages_perdus'     => Bagage::where('statut', 'perdu')->count(),
        ]);
    }
}

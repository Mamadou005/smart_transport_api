<?php
namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Notification as NotificationModel; // ✅ alias obligatoire
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AgentController extends Controller
{
    // ══════════════════════════════════════════
    // SCANNER QR — Valider embarquement
    // POST /api/agent/scanner
    // ══════════════════════════════════════════
    public function scanner(Request $request): JsonResponse
    {
        $request->validate([
            'code_qr' => 'required|string|max:50',
        ]);

        $reservation = Reservation::with(['user', 'voyage'])
            ->where('code_qr', $request->code_qr)
            ->first();

        if (!$reservation) {
            return response()->json([
                'valide'  => false,
                'message' => '❌ QR Code invalide. Aucune réservation trouvée.',
            ], 404);
        }

        if ($reservation->statut === 'annulee') {
            return response()->json([
                'valide'  => false,
                'message' => '❌ Réservation annulée. Accès refusé.',
            ], 422);
        }

        if ($reservation->statut === 'embarquee') {
            return response()->json([
                'valide'      => true,
                'message'     => '⚠️ Ce passager est déjà embarqué.',
                'reservation' => $reservation,
            ]);
        }

        // ✅ Marque comme embarquée
        $reservation->update(['statut' => 'embarquee']);

        // ✅ Notification — utilise l'alias pour éviter le conflit
        NotificationModel::create([
            'user_id' => $reservation->user_id,
            'message' => "✈️ Embarquement validé ! Bon voyage de {$reservation->voyage->origine} vers {$reservation->voyage->destination}.",
            'type'    => 'confirmation',
            'lue'     => false,
        ]);

        return response()->json([
            'valide'      => true,
            'message'     => '✅ Embarquement validé avec succès.',
            'reservation' => $reservation,
        ]);
    }

    // ══════════════════════════════════════════
    // RÉSERVATIONS DU JOUR
    // GET /api/agent/reservations-du-jour?date=2026-07-01
    // ══════════════════════════════════════════
    public function reservationsDuJour(Request $request): JsonResponse
    {
        $date = $request->get('date', today()->toDateString());

        $reservations = Reservation::with(['user', 'voyage'])
            ->whereHas('voyage', function ($q) use ($date) {
                $q->whereDate('date_depart', $date);
            })
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($r) {
                return [
                    'id'         => $r->id,
                    'code_qr'    => $r->code_qr,
                    'statut'     => $r->statut,
                    'passager'   => $r->user
                        ? $r->user->prenom . ' ' . $r->user->nom
                        : '—',
                    'voyage'     => $r->voyage
                        ? $r->voyage->origine . ' → ' . $r->voyage->destination
                        : '—',
                    'user'       => $r->user,
                    'voyage_obj' => $r->voyage,
                    'created_at' => $r->created_at,
                ];
            });

        return response()->json($reservations);
    }

    // ══════════════════════════════════════════
    // STATS DU JOUR
    // GET /api/agent/stats
    // ══════════════════════════════════════════
    public function statsDuJour(): JsonResponse
    {
        $voyage = \App\Models\Voyage::where('statut', 'en_cours')
            ->orWhere('statut', 'embarquement')
            ->first();

        return response()->json([
            'reservations_aujourd_hui' => Reservation::whereDate('created_at', today())->count(),
            'embarques_aujourd_hui'    => Reservation::whereDate('updated_at', today())
                ->where('statut', 'embarquee')->count(),
            'en_attente'               => Reservation::where('statut', 'confirmee')->count(),
            'confirmees'               => Reservation::where('statut', 'confirmee')->count(),
            'voyage_actif'             => $voyage,
        ]);
    }
}

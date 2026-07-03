<?php
namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Voyage;
use App\Models\Notification as NotificationModel;
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
                'reservation' => $this->_formatReservation($reservation),
            ]);
        }

        $reservation->update(['statut' => 'embarquee']);

        NotificationModel::create([
            'user_id' => $reservation->user_id,
            'message' => "✈️ Embarquement validé ! Bon voyage de {$reservation->voyage->origine} vers {$reservation->voyage->destination}.",
            'type'    => 'confirmation',
            'lue'     => false,
        ]);

        return response()->json([
            'valide'      => true,
            'message'     => '✅ Embarquement validé avec succès.',
            'reservation' => $this->_formatReservation($reservation),
        ]);
    }

    // ══════════════════════════════════════════
    // RÉSERVATIONS DU JOUR
    // GET /api/agent/reservations-du-jour?date=2026-07-01
    // ✅ Fix : structure voyage correcte (String, pas int)
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
            ->map(fn($r) => $this->_formatReservation($r));

        return response()->json($reservations);
    }

    // ══════════════════════════════════════════
    // STATS DU JOUR
    // GET /api/agent/stats
    // ══════════════════════════════════════════
    public function statsDuJour(): JsonResponse
    {
        $voyageActif = Voyage::whereIn('statut', ['embarquement', 'en_cours'])->first();

        return response()->json([
            'reservations_aujourd_hui' => Reservation::whereDate('created_at', today())->count(),
            'embarques_aujourd_hui'    => Reservation::whereDate('updated_at', today())
                ->where('statut', 'embarquee')->count(),
            'en_attente'               => Reservation::where('statut', 'confirmee')->count(),
            'confirmees'               => Reservation::where('statut', 'confirmee')->count(),
            'voyage_actif'             => $voyageActif ? [
                'id'          => $voyageActif->id,
                'origine'     => (string) $voyageActif->origine,
                'destination' => (string) $voyageActif->destination,
                'date_depart' => $voyageActif->date_depart,
                'statut'      => $voyageActif->statut,
            ] : null,
        ]);
    }

    // ══════════════════════════════════════════
    // HELPER — Formate une réservation proprement
    // ✅ Garantit que tous les champs String sont bien des String
    //    pour éviter le TypeError Flutter "String is not a subtype of int"
    // ══════════════════════════════════════════
    private function _formatReservation(Reservation $r): array
    {
        return [
            'id'         => (int)    $r->id,
            'code_qr'    => (string) $r->code_qr,
            'statut'     => (string) $r->statut,
            'created_at' => $r->created_at,
            'user'       => $r->user ? [
                'id'        => (int)    $r->user->id,
                'nom'       => (string) $r->user->nom,
                'prenom'    => (string) $r->user->prenom,
                'email'     => (string) $r->user->email,
                'telephone' => (string) ($r->user->telephone ?? ''),
            ] : null,
            'voyage' => $r->voyage ? [
                'id'           => (int)    $r->voyage->id,
                'origine'      => (string) $r->voyage->origine,      // ✅ forcé String
                'destination'  => (string) $r->voyage->destination,  // ✅ forcé String
                'date_depart'  => (string) $r->voyage->date_depart,
                'date_arrivee' => (string) ($r->voyage->date_arrivee ?? ''),
                'statut'       => (string) $r->voyage->statut,
                'capacite'     => (int)    ($r->voyage->capacite ?? 0),
                'prix'         => (float)  ($r->voyage->prix ?? 0),
                'type_transport' => (string) ($r->voyage->type_transport ?? 'routier'),
            ] : null,
        ];
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Bagage;
use App\Models\Reservation;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class BagagisteController extends Controller
{
    // ══════════════════════════════════════════
    // ENREGISTRER UN BAGAGE
    // POST /api/bagagiste/bagages
    // Body: { reservation_id, poids, description? }
    // ══════════════════════════════════════════
    public function enregistrerBagage(Request $request): JsonResponse
    {
        $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'poids'          => 'required|numeric|min:0.1|max:500',
            'description'    => 'nullable|string|max:255',
        ]);

        $reservation = Reservation::with(['user', 'voyage'])
            ->findOrFail($request->reservation_id);

        // Vérification que la réservation est confirmée
        if (!in_array($reservation->statut, ['confirmee', 'embarquee'])) {
            return response()->json([
                'message' => 'Impossible d\'enregistrer un bagage : réservation non confirmée.',
            ], 422);
        }

        $bagage = Bagage::create([
            'reservation_id' => $reservation->id,
            'description'    => $request->description ?? 'Bagage',
            'poids'          => $request->poids,
            'code_qr'        => 'BAG-' . strtoupper(Str::random(8)),
            'statut'         => 'enregistre',
        ]);

        // Notification au passager
        Notification::create([
            'user_id' => $reservation->user_id,
            'message' => "🧳 Votre bagage {$bagage->code_qr} ({$request->poids} kg) a été enregistré avec succès.",
            'type'    => 'confirmation',
            'lue'     => false,
        ]);

        return response()->json([
            'message' => 'Bagage enregistré avec succès.',
            'bagage'  => $bagage->load('reservation.user'),
        ], 201);
    }

    // ══════════════════════════════════════════
    // LISTE DES BAGAGES DU JOUR
    // GET /api/bagagiste/bagages
    // ══════════════════════════════════════════
    public function listeBagages(Request $request): JsonResponse
    {
        $statut = $request->get('statut');
        $date   = $request->get('date', today()->toDateString());

        $query = Bagage::with(['reservation.user', 'reservation.voyage'])
            ->whereDate('created_at', $date);

        if ($statut) {
            $query->where('statut', $statut);
        }

        $bagages = $query->orderByDesc('created_at')->get();

        return response()->json($bagages);
    }

    // ══════════════════════════════════════════
    // METTRE À JOUR LE STATUT D'UN BAGAGE
    // PUT /api/bagagiste/bagages/{id}/statut
    // Body: { statut: 'en_transit'|'arrive'|'recupere'|'perdu' }
    // ══════════════════════════════════════════
    public function updateStatut(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'statut' => 'required|in:enregistre,en_transit,arrive,perdu,recupere',
        ]);

        $bagage = Bagage::with(['reservation.user', 'reservation.voyage'])
            ->findOrFail($id);

        $ancienStatut = $bagage->statut;
        $bagage->update(['statut' => $request->statut]);

        // Messages de notification selon le statut
        $messages = [
            'en_transit' => "🚌 Votre bagage {$bagage->code_qr} est en transit vers {$bagage->reservation->voyage?->destination}.",
            'arrive'     => "✅ Votre bagage {$bagage->code_qr} est arrivé à destination. Présentez-vous au comptoir.",
            'recupere'   => "🎉 Votre bagage {$bagage->code_qr} a été récupéré. Bon retour !",
            'perdu'      => "⚠️ Votre bagage {$bagage->code_qr} est signalé perdu. Un agent vous contactera.",
        ];

        $types = [
            'en_transit' => 'arrivee',
            'arrive'     => 'arrivee',
            'recupere'   => 'confirmation',
            'perdu'      => 'perte',
        ];

        if (isset($messages[$request->statut]) && $bagage->reservation) {
            Notification::create([
                'user_id' => $bagage->reservation->user_id,
                'message' => $messages[$request->statut],
                'type'    => $types[$request->statut],
                'lue'     => false,
            ]);
        }

        return response()->json([
            'message'       => 'Statut mis à jour.',
            'bagage'        => $bagage,
            'ancien_statut' => $ancienStatut,
            'nouveau_statut'=> $request->statut,
        ]);
    }

    // ══════════════════════════════════════════
    // RECHERCHER UNE RÉSERVATION PAR CODE QR
    // POST /api/bagagiste/chercher-reservation
    // Body: { code_qr: 'RES-XXXX' }
    // ══════════════════════════════════════════
    public function chercherReservation(Request $request): JsonResponse
    {
        $request->validate([
            'code_qr' => 'required|string|max:50',
        ]);

        $reservation = Reservation::with(['user', 'voyage', 'bagages'])
            ->where('code_qr', $request->code_qr)
            ->first();

        if (!$reservation) {
            return response()->json([
                'message' => 'Aucune réservation trouvée avec ce code.',
                'trouve'  => false,
            ], 404);
        }

        return response()->json([
            'trouve'      => true,
            'reservation' => $reservation,
        ]);
    }

    // ══════════════════════════════════════════
    // STATS DU BAGAGISTE
    // GET /api/bagagiste/stats
    // ══════════════════════════════════════════
    public function stats(): JsonResponse
    {
        return response()->json([
            'enregistres_aujourd_hui' => Bagage::whereDate('created_at', today())
                ->where('statut', 'enregistre')->count(),
            'en_transit'              => Bagage::where('statut', 'en_transit')->count(),
            'arrives_aujourd_hui'     => Bagage::whereDate('updated_at', today())
                ->where('statut', 'arrive')->count(),
            'recuperes_aujourd_hui'   => Bagage::whereDate('updated_at', today())
                ->where('statut', 'recupere')->count(),
            'perdus'                  => Bagage::where('statut', 'perdu')->count(),
            'total_aujourd_hui'       => Bagage::whereDate('created_at', today())->count(),
        ]);
    }

    // ══════════════════════════════════════════
    // DETAIL D'UN BAGAGE
    // GET /api/bagagiste/bagages/{id}
    // ══════════════════════════════════════════
    public function show(int $id): JsonResponse
    {
        $bagage = Bagage::with(['reservation.user', 'reservation.voyage', 'localisations'])
            ->findOrFail($id);

        return response()->json($bagage);
    }
}

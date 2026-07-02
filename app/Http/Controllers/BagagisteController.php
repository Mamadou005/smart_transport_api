<?php
namespace App\Http\Controllers;

use App\Models\Bagage;
use App\Models\Reservation;
use App\Models\Signalement;
use App\Models\Notification as NotificationModel; // ✅ alias obligatoire
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class BagagisteController extends Controller
{
    // ══════════════════════════════════════════
    // ENREGISTRER UN BAGAGE
    // POST /api/bagagiste/bagages
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

        if (!in_array($reservation->statut, ['confirmee', 'embarquee'])) {
            return response()->json([
                'message' => 'Impossible d\'enregistrer : réservation non confirmée.',
            ], 422);
        }

        $bagage = Bagage::create([
            'reservation_id' => $reservation->id,
            'description'    => $request->description ?? 'Bagage',
            'poids'          => $request->poids,
            'code_qr'        => 'BAG-' . strtoupper(Str::random(8)),
            'statut'         => 'enregistre',
        ]);

        // ✅ Notification avec alias
        NotificationModel::create([
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
    // GET /api/bagagiste/bagages?statut=en_transit
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

        return response()->json($query->orderByDesc('created_at')->get());
    }

    // ══════════════════════════════════════════
    // DETAIL D'UN BAGAGE
    // GET /api/bagagiste/bagages/{id}
    // ══════════════════════════════════════════
    public function show(int $id): JsonResponse
    {
        $bagage = Bagage::with(['reservation.user', 'reservation.voyage'])
            ->findOrFail($id);

        return response()->json($bagage);
    }

    // ══════════════════════════════════════════
    // METTRE À JOUR LE STATUT D'UN BAGAGE
    // PUT /api/bagagiste/bagages/{id}/statut
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

        // ✅ Messages de notification avec alias
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
            NotificationModel::create([
                'user_id' => $bagage->reservation->user_id,
                'message' => $messages[$request->statut],
                'type'    => $types[$request->statut],
                'lue'     => false,
            ]);
        }

        return response()->json([
            'message'        => 'Statut mis à jour.',
            'bagage'         => $bagage,
            'ancien_statut'  => $ancienStatut,
            'nouveau_statut' => $request->statut,
        ]);
    }

    // ══════════════════════════════════════════
    // RECHERCHER UNE RÉSERVATION PAR CODE QR
    // POST /api/bagagiste/chercher-reservation
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
                'trouve'  => false,
                'message' => 'Aucune réservation trouvée avec ce code.',
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
    // LISTE DES SIGNALEMENTS
    // GET /api/bagagiste/signalements
    // ══════════════════════════════════════════
    public function listeSignalements(Request $request): JsonResponse
    {
        $statut = $request->get('statut');

        $query = Signalement::with(['user', 'bagage.reservation.voyage'])
            ->orderByDesc('created_at');

        if ($statut) {
            $query->where('statut', $statut);
        }

        return response()->json($query->get());
    }

    // ══════════════════════════════════════════
    // METTRE À JOUR STATUT SIGNALEMENT
    // PUT /api/bagagiste/signalements/{id}/statut
    // ══════════════════════════════════════════
    public function updateSignalement(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'statut' => 'required|in:ouvert,en_cours,resolu',
        ]);

        $signalement = Signalement::with(['user', 'bagage'])->findOrFail($id);
        $signalement->update(['statut' => $request->statut]);

        // ✅ Notification avec alias
        if ($request->statut === 'en_cours' && $signalement->user_id) {
            NotificationModel::create([
                'user_id' => $signalement->user_id,
                'message' => "🔍 Votre signalement pour le bagage {$signalement->bagage?->code_qr} est pris en charge. Nous recherchons activement votre bagage.",
                'type'    => 'prise_en_charge',
                'lue'     => false,
            ]);
        }

        if ($request->statut === 'resolu' && $signalement->user_id) {
            // Met aussi à jour le statut du bagage
            if ($signalement->bagage) {
                $signalement->bagage->update(['statut' => 'recupere']);
            }

            NotificationModel::create([
                'user_id' => $signalement->user_id,
                'message' => "🎉 Bonne nouvelle ! Votre bagage {$signalement->bagage?->code_qr} a été retrouvé et est disponible à la récupération.",
                'type'    => 'retrouve',
                'lue'     => false,
            ]);
        }

        return response()->json([
            'message'     => 'Signalement mis à jour.',
            'signalement' => $signalement,
        ]);
    }

    // ══════════════════════════════════════════
    // CONFIRMER PERTE DÉFINITIVE
    // PUT /api/bagagiste/signalements/{id}/confirmer-perdu
    // ══════════════════════════════════════════
    public function confirmerPerteDefinitive(int $id): JsonResponse
    {
        $signalement = Signalement::with(['user', 'bagage'])->findOrFail($id);

        $signalement->update(['statut' => 'resolu']);

        if ($signalement->bagage) {
            $signalement->bagage->update(['statut' => 'perdu']);
        }

        // ✅ Notification avec alias
        if ($signalement->user_id) {
            NotificationModel::create([
                'user_id' => $signalement->user_id,
                'message' => "❌ Votre bagage {$signalement->bagage?->code_qr} a été confirmé perdu définitivement. Contactez l'agence pour plus d'informations.",
                'type'    => 'perte',
                'lue'     => false,
            ]);
        }

        return response()->json([
            'message' => 'Bagage confirmé perdu définitivement.',
        ]);
    }
}

<?php
namespace App\Http\Controllers;

use App\Models\Bagage;
use App\Models\Localisation;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BagageController extends Controller {

    // Liste des bagages du passager connecté
    public function index(Request $request) {
        $bagages = Bagage::with([
            'reservation.voyage',
            'derniereLocalisation'
        ])
            ->whereHas('reservation', function ($q) use ($request) {
                $q->where('user_id', $request->user()->id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($bagages);
    }

    // Enregistrer un bagage
    public function store(Request $request) {
        $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'description'    => 'nullable|string',
            'poids'          => 'required|numeric|min:0',
        ]);

        // Vérifie que la réservation appartient au user
        $reservation = Reservation::where('id', $request->reservation_id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $bagage = Bagage::create([
            'reservation_id' => $reservation->id,
            'description'    => $request->description,
            'poids'          => $request->poids,
            'code_qr'        => 'BAG-' . strtoupper(Str::random(8)),
            'statut'         => 'enregistre',
        ]);

        // --- Notification de confirmation du bagage ---
        \App\Http\Controllers\NotificationController::creer(
            $request->user()->id,
            "🧳 Bagage enregistré ! Code : {$bagage->code_qr} | Poids : {$bagage->poids} kg",
            'confirmation'
        );
        // -----------------------------------------------

        return response()->json(
            $bagage->load('reservation.voyage'), 201
        );
    }

    // Détail d'un bagage + historique GPS
    public function show($id, Request $request) {
        $bagage = Bagage::with([
            'reservation.voyage',
            'localisations' => fn($q) => $q->orderBy('created_at', 'desc')
        ])
            ->whereHas('reservation', function ($q) use ($request) {
                $q->where('user_id', $request->user()->id);
            })
            ->findOrFail($id);

        return response()->json($bagage);
    }

    // Mettre à jour le statut (pour agent/admin)
    public function updateStatut(Request $request, $id) {
        $request->validate([
            'statut' => 'required|in:enregistre,en_transit,arrive,perdu,recupere',
        ]);

        $bagage = Bagage::findOrFail($id);
        $bagage->update(['statut' => $request->statut]);

        return response()->json($bagage);
    }

    // Ajouter une localisation GPS
    public function addLocalisation(Request $request, $id) {
        $request->validate([
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $bagage = Bagage::findOrFail($id);

        $loc = Localisation::create([
            'bagage_id'   => $bagage->id,
            'latitude'    => $request->latitude,
            'longitude'   => $request->longitude,
            'horodatage'  => now(),
        ]);

        return response()->json($loc, 201);
    }
}

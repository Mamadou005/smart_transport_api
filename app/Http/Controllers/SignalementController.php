<?php
namespace App\Http\Controllers;

use App\Models\Signalement;
use App\Models\Bagage;
use Illuminate\Http\Request;

class SignalementController extends Controller {

    public function index(Request $request) {
        $signalements = Signalement::with(['bagage'])
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($signalements);
    }

    public function indexAll() {
        $signalements = Signalement::with(['bagage', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($signalements);
    }

    public function store(Request $request) {
        $request->validate([
            'bagage_id'      => 'required|exists:bagages,id',
            'description'    => 'required|string',
            'lieu_dernier_vu'=> 'nullable|string',
        ]);

        // On récupère le bagage en vérifiant qu'il appartient bien à l'utilisateur
        $bagage = Bagage::whereHas('reservation', function ($q) use ($request) {
            $q->where('user_id', $request->user()->id);
        })->findOrFail($request->bagage_id);

        // Mise à jour automatique du statut du bagage
        $bagage->update(['statut' => 'perdu']);

        // Création du signalement
        $signalement = Signalement::create([
            'bagage_id'       => $bagage->id,
            'user_id'         => $request->user()->id,
            'description'     => $request->description,
            'lieu_dernier_vu' => $request->lieu_dernier_vu,
            'statut'          => 'ouvert',
        ]);

        // --- Notification de signalement de perte ---
        \App\Http\Controllers\NotificationController::creer(
            $request->user()->id,
            "🚨 Signalement enregistré pour le bagage {$bagage->code_qr}. Notre équipe est alertée et recherche votre bagage.",
            'perte'
        );
        // --------------------------------------------

        return response()->json(
            $signalement->load('bagage'), 201
        );
    }

    public function show($id, Request $request) {
        $signalement = Signalement::with(['bagage', 'user'])
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);
        return response()->json($signalement);
    }

    public function updateStatut(Request $request, $id) {
        $request->validate([
            'statut' => 'required|in:ouvert,en_cours,resolu',
        ]);

        $signalement = Signalement::findOrFail($id);
        $signalement->update(['statut' => $request->statut]);

        // Si le signalement est résolu, on marque le bagage comme récupéré
        if ($request->statut === 'resolu') {
            $signalement->bagage->update(['statut' => 'recupere']);
        }

        return response()->json($signalement);
    }
}

<?php
namespace App\Http\Controllers;

use App\Models\Paiement;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaiementController extends Controller {

    // Initier un paiement
    public function initier(Request $request) {
        $request->validate([
            'reservation_id'      => 'required|exists:reservations,id',
            'methode'             => 'required|in:wave,orange_money,cash',
            'telephone_paiement'  => 'required_unless:methode,cash|string',
        ]);

        $reservation = Reservation::with('voyage')
            ->findOrFail($request->reservation_id);

        // Vérifie que la réservation appartient à l'utilisateur
        if ($reservation->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Accès refusé'
            ], 403);
        }

        // Vérifie si paiement déjà effectué
        if ($reservation->paiement &&
            $reservation->paiement->statut === 'confirme') {
            return response()->json([
                'message' => 'Cette réservation est déjà payée'
            ], 400);
        }

        // Génère une référence unique
        $reference = strtoupper('PAY-' . Str::random(10));

        $paiement = Paiement::updateOrCreate(
            ['reservation_id' => $reservation->id],
            [
                'user_id'             => $request->user()->id,
                'montant'             => $reservation->voyage->prix,
                'devise'              => $reservation->voyage->devise ?? 'XOF',
                'methode'             => $request->methode,
                'statut'              => 'en_attente',
                'reference'           => $reference,
                'telephone_paiement'  => $request->telephone_paiement,
            ]
        );

        // Simulation paiement mobile money
        // En production : intégrer l'API Wave/Orange Money ici
        return response()->json([
            'paiement'  => $paiement,
            'message'   => $this->_getInstructions($request->methode,
                $request->telephone_paiement, $reservation->voyage->prix),
            'reference' => $reference,
        ]);
    }

    // Confirmer un paiement (simulé — en prod : webhook)
    public function confirmer(Request $request, $reference) {
        $paiement = Paiement::where('reference', $reference)
            ->firstOrFail();

        if ($paiement->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $paiement->update([
            'statut'      => 'confirme',
            'confirme_at' => now(),
        ]);

        // Confirme automatiquement la réservation
        $paiement->reservation->update(['statut' => 'confirmee']);

        // Notification
        \App\Http\Controllers\NotificationController::creer(
            $paiement->user_id,
            "💳 Paiement de {$paiement->montant} {$paiement->devise} confirmé ! "
            . "Référence : {$reference}",
            'confirmation'
        );

        return response()->json([
            'message'  => 'Paiement confirmé avec succès',
            'paiement' => $paiement->load('reservation.voyage'),
        ]);
    }

    // Historique des paiements
    public function historique(Request $request) {
        $paiements = Paiement::with(['reservation.voyage'])
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($paiements);
    }

    private function _getInstructions(
        string $methode, ?string $tel, float $montant): string {
        $montantFmt = number_format($montant, 0, ',', ' ') . ' XOF';
        return match($methode) {
            'wave' =>
                "Envoyez {$montantFmt} au numéro Wave indiqué. "
                . "Votre paiement sera confirmé automatiquement.",
            'orange_money' =>
                "Composez #144# sur votre téléphone Orange "
                . "et envoyez {$montantFmt}. "
                . "Votre paiement sera confirmé automatiquement.",
            default =>
            "Payez {$montantFmt} en espèces à l'agent."
        };
    }
}

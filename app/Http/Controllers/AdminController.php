<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Voyage;
use App\Models\Reservation;
use App\Models\Bagage;
use App\Models\Signalement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller {

    // ── Dashboard stats
    public function stats() {
        return response()->json([
            'total_passagers'    => User::where('role', 'passager')->count(),
            'total_agents'       => User::where('role', 'agent')->count(),
            'total_voyages'      => Voyage::count(),
            'voyages_planifies'  => Voyage::where('statut', 'planifie')->count(),
            'voyages_en_cours'   => Voyage::where('statut', 'en_cours')->count(),
            'total_reservations' => Reservation::count(),
            'total_bagages'      => Bagage::count(),
            'bagages_perdus'     => Bagage::where('statut', 'perdu')->count(),
            'signalements_ouverts' => Signalement::where('statut', 'ouvert')->count(),
            'signalements_en_cours'=> Signalement::where('statut', 'en_cours')->count(),
        ]);
    }

    // ── Gestion utilisateurs
    public function getUtilisateurs(Request $request) {
        $query = User::query();
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nom', 'like', '%'.$request->search.'%')
                    ->orWhere('prenom', 'like', '%'.$request->search.'%')
                    ->orWhere('email', 'like', '%'.$request->search.'%');
            });
        }
        return response()->json(
            $query->orderBy('created_at', 'desc')->get()
        );
    }

    public function creerUtilisateur(Request $request) {
        $request->validate([
            'nom'       => 'required|string',
            'prenom'    => 'required|string',
            'email'     => 'required|email|unique:users',
            'telephone' => 'nullable|string',
            'password'  => 'required|min:6',
            'role'      => 'required|in:passager,agent,admin',
        ]);

        $user = User::create([
            'nom'       => $request->nom,
            'prenom'    => $request->prenom,
            'email'     => $request->email,
            'telephone' => $request->telephone,
            'password'  => Hash::make($request->password),
            'role'      => $request->role,
        ]);

        return response()->json($user, 201);
    }

    public function updateUtilisateur(Request $request, $id) {
        $user = User::findOrFail($id);
        $request->validate([
            'nom'       => 'string',
            'prenom'    => 'string',
            'email'     => 'email|unique:users,email,'.$id,
            'telephone' => 'nullable|string',
            'role'      => 'in:passager,agent,admin',
        ]);
        $user->update($request->only([
            'nom','prenom','email','telephone','role'
        ]));
        return response()->json($user);
    }

    public function supprimerUtilisateur($id) {
        User::findOrFail($id)->delete();
        return response()->json(['message' => 'Utilisateur supprimé']);
    }

    // ── Gestion voyages
    public function getVoyages() {
        return response()->json(
            Voyage::withCount('reservations')
                ->orderBy('date_depart', 'desc')
                ->get()
        );
    }

    public function creerVoyage(Request $request) {
        $request->validate([
            'origine'        => 'required|string',
            'destination'    => 'required|string',
            'date_depart'    => 'required|date',
            'date_arrivee'   => 'required|date',
            'type_transport' => 'required|in:routier,ferroviaire,aerien',
            'capacite'       => 'required|integer|min:1',
            'prix'           => 'required|numeric|min:0',
        ]);

        $voyage = Voyage::create($request->all());
        return response()->json($voyage, 201);
    }

    public function updateVoyage(Request $request, $id) {
        $voyage = Voyage::findOrFail($id);
        $voyage->update($request->all());
        return response()->json($voyage);
    }

    public function supprimerVoyage($id) {
        Voyage::findOrFail($id)->delete();
        return response()->json(['message' => 'Voyage supprimé']);
    }

    // ── Signalements admin
    public function getSignalements() {
        return response()->json(
            Signalement::with(['bagage', 'user'])
                ->orderBy('created_at', 'desc')
                ->get()
        );
    }

    public function updateSignalement(Request $request, $id) {
        $request->validate([
            'statut' => 'required|in:ouvert,en_cours,resolu',
        ]);
        $signalement = Signalement::with('bagage')->findOrFail($id);
        $signalement->update(['statut' => $request->statut]);

        if ($request->statut === 'resolu') {
            $signalement->bagage->update(['statut' => 'recupere']);
            \App\Http\Controllers\NotificationController::creer(
                $signalement->user_id,
                "🎉 Bonne nouvelle ! Votre bagage {$signalement->bagage->code_qr} a été retrouvé et est disponible à la récupération.",
                'arrivee'
            );
        }

        if ($request->statut === 'en_cours') {
            \App\Http\Controllers\NotificationController::creer(
                $signalement->user_id,
                "🔍 Votre signalement pour le bagage {$signalement->bagage->code_qr} est pris en charge. Nous recherchons activement votre bagage.",
                'alerte'
            );
        }

        return response()->json($signalement);
    }
    public function getBagages() {
        $bagages = \App\Models\Bagage::with([
            'reservation.user',
            'reservation.voyage',
        ])
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($bagages);
    }

    public function updateStatutVoyage(Request $request, $id) {
        $request->validate([
            'statut' => 'required|in:planifie,embarquement,en_cours,arrive,annule',
        ]);
        $voyage = Voyage::findOrFail($id);
        $voyage->update(['statut' => $request->statut]);
        return response()->json($voyage);
    }

    public function confirmerBagagePerdu(Request $request, $id) {
        $signalement = \App\Models\Signalement::with('bagage')
            ->findOrFail($id);

        // ✅ Marque le bagage comme définitivement perdu
        $signalement->bagage->update(['statut' => 'perdu']);

        // ✅ Met le signalement en cours
        $signalement->update(['statut' => 'en_cours']);

        // ✅ Notifie le passager
        \App\Http\Controllers\NotificationController::creer(
            $signalement->user_id,
            "❌ Après recherche, votre bagage {$signalement->bagage->code_qr} est confirmé perdu. Contactez notre service client.",
            'perte'
        );

        return response()->json([
            'message'     => 'Bagage confirmé perdu',
            'signalement' => $signalement->load('bagage'),
        ]);
    }
}

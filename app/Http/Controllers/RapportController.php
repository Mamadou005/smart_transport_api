<?php
namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Bagage;
use App\Models\Voyage;
use App\Models\User;
use App\Models\Signalement;
use Illuminate\Http\Request;

class RapportController extends Controller {

    public function stats(Request $request) {
        $debut = $request->get('debut', now()->startOfMonth()->toDateString());
        $fin   = $request->get('fin',   now()->toDateString());

        return response()->json([
            'periode' => ['debut' => $debut, 'fin' => $fin],

            // Passagers
            'total_passagers'     => User::where('role','passager')->count(),
            'nouveaux_passagers'  => User::where('role','passager')
                ->whereBetween('created_at', [$debut, $fin])->count(),

            // Voyages
            'total_voyages'       => Voyage::count(),
            'voyages_periode'     => Voyage::whereBetween('date_depart',
                [$debut, $fin])->count(),
            'voyages_par_statut'  => Voyage::selectRaw('statut, count(*) as total')
                ->groupBy('statut')->get(),
            'voyages_par_type'    => Voyage::selectRaw(
                'type_transport, count(*) as total')
                ->groupBy('type_transport')->get(),

            // Réservations
            'total_reservations'  => Reservation::count(),
            'reservations_periode'=> Reservation::whereBetween('created_at',
                [$debut, $fin])->count(),
            'reservations_par_statut' => Reservation::selectRaw(
                'statut, count(*) as total')
                ->groupBy('statut')->get(),

            // Bagages
            'total_bagages'       => Bagage::count(),
            'bagages_periode'     => Bagage::whereBetween('created_at',
                [$debut, $fin])->count(),
            'bagages_perdus'      => Bagage::where('statut','perdu')->count(),
            'bagages_recuperes'   => Bagage::where('statut','recupere')->count(),
            'taux_perte'          => Bagage::count() > 0
                ? round(Bagage::where('statut','perdu')->count()
                    / Bagage::count() * 100, 1)
                : 0,

            // Signalements
            'total_signalements'  => Signalement::count(),
            'signalements_ouverts'=> Signalement::where('statut','ouvert')->count(),
            'signalements_resolus'=> Signalement::where('statut','resolu')->count(),

            // Top destinations
            'top_destinations'    => Voyage::selectRaw(
                'destination, count(*) as total')
                ->groupBy('destination')
                ->orderByDesc('total')
                ->limit(5)->get(),

            // Voyages par mois (derniers 6 mois)
            'voyages_par_mois'    => Voyage::selectRaw(
                'DATE_FORMAT(date_depart, "%Y-%m") as mois, count(*) as total')
                ->where('date_depart', '>=', now()->subMonths(6))
                ->groupBy('mois')
                ->orderBy('mois')
                ->get(),
        ]);
    }
}

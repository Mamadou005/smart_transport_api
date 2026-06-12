<?php
namespace App\Http\Controllers;

use App\Models\Voyage;
use Illuminate\Http\Request;

class VoyageController extends Controller {

    public function index(Request $request) {
        $query = Voyage::query();

        if ($request->has('origine')) {
            $query->where('origine', 'like', '%'.$request->origine.'%');
        }
        if ($request->has('destination')) {
            $query->where('destination', 'like', '%'.$request->destination.'%');
        }
        if ($request->has('date_depart')) {
            $query->whereDate('date_depart', $request->date_depart);
        }

        $voyages = $query->where('statut', 'planifie')
            ->orderBy('date_depart')
            ->get();

        return response()->json($voyages);
    }

    public function store(Request $request) {
        $request->validate([
            'origine'        => 'required|string',
            'destination'    => 'required|string',
            'date_depart'    => 'required|date',
            'date_arrivee'   => 'required|date',
            'type_transport' => 'required|in:routier,ferroviaire,aerien',
            'capacite'       => 'integer',
        ]);

        $voyage = Voyage::create($request->all());
        return response()->json($voyage, 201);
    }

    public function show($id) {
        $voyage = Voyage::findOrFail($id);
        return response()->json($voyage);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    /**
     * Vraća sve timove zajedno sa sezonom kojoj pripadaju.
     */
    public function index()
    {
        $teams = Team::with('season')->get();
        return response()->json($teams, 200);
    }

    /**
     * Registruje novi tim uz validaciju unetih podataka.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'season_id' => 'required|exists:seasons,id',
            'name' => 'required|string|max:255',
            'contact_email' => 'required|email',
        ]);

        $team = Team::create($validated);

        return response()->json([
            'message' => 'Tim je uspesno registrovan.',
            'data' => $team
        ], 201);
    }

    /**
     * Prikazuje pojedinačni tim i učitava njegove rezultate sa događajima.
     */
    public function show(Team $team)
    {
        $team->load(['season', 'results.event']);
        return response()->json($team, 200);
    }

    /**
     * Ažurira podatke o timu (polja su opcionalna za slanje uz 'sometimes').
     */
    public function update(Request $request, Team $team)
    {
        $validated = $request->validate([
            'season_id' => 'sometimes|required|exists:seasons,id',
            'name' => 'sometimes|required|string|max:255',
            'contact_email' => 'sometimes|required|email',
        ]);

        $team->update($validated);

        return response()->json([
            'message' => 'Tim je uspesno izmenjen.',
            'data' => $team
        ], 200);
    }

    /**
     * Briše tim iz baze podataka.
     */
    public function destroy(Team $team)
    {
        $team->delete();

        return response()->json([
            'message' => 'Tim je uspesno obrisan.'
        ], 200);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    /**
     * Vraća sve timove zajedno sa sezonom kojoj pripadaju.
     */
       public function index(Request $request)
    {
        $upit = Team::with('season');

        if ($request->filled('pretraga')) {
            $tekst = $request->pretraga;

            $upit->where(function ($q) use ($tekst) {
                $q->where('name', 'like', '%' . $tekst . '%')
                  ->orWhere('contact_email', 'like', '%' . $tekst . '%');
            });
        }

        if ($request->filled('season_id')) {
            $upit->where('season_id', $request->season_id);
        }

        $kolona = $request->input('sortiraj_po', 'name');
        $smer = $request->input('smer', 'asc');

        $dozvoljeneKolone = ['name', 'contact_email', 'created_at'];
        $dozvoljeniSmerovi = ['asc', 'desc'];

        if (!in_array($kolona, $dozvoljeneKolone)) {
            $kolona = 'name';
        }

        if (!in_array($smer, $dozvoljeniSmerovi)) {
            $smer = 'asc';
        }

        $teams = $upit->orderBy($kolona, $smer)->paginate(10);

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
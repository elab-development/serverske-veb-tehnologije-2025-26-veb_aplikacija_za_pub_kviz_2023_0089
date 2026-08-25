<?php

namespace App\Http\Controllers;

use App\Models\Season;
use Illuminate\Http\Request;

class SeasonController extends Controller
{
    public function index()
    {
        $seasons = Season::withCount(['teams', 'events'])
            ->orderBy('start_date', 'desc')
            ->get();

        return response()->json($seasons, 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:seasons,name',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $season = Season::create($data);

        return response()->json([
            'message' => 'Sezona je uspesno kreirana.',
            'season' => $season,
        ], 201);
    }

    public function show(Season $season)
    {
        $season->load(['teams', 'events']);

        return response()->json($season, 200);
    }

    public function update(Request $request, Season $season)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255|unique:seasons,name,' . $season->id,
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date|after:start_date',
        ]);

        $season->update($data);

        return response()->json([
            'message' => 'Sezona je uspesno izmenjena.',
            'season' => $season,
        ], 200);
    }

    public function destroy(Season $season)
    {
        $season->delete();

        return response()->json([
            'message' => 'Sezona je uspesno obrisana.',
        ], 200);
    }
        public function scoreboard(Season $season)
    {
        $teams = $season->teams()
            ->withSum('results as ukupno_poena', 'points')
            ->withCount('results as odigrano_dogadjaja')
            ->orderByDesc('ukupno_poena')
            ->get()
            ->map(function ($team, $index) {
                return [
                    'pozicija' => $index + 1,
                    'tim' => $team->name,
                    'ukupno_poena' => $team->ukupno_poena ?? 0,
                    'odigrano_dogadjaja' => $team->odigrano_dogadjaja,
                ];
            });

        return response()->json([
            'sezona' => $season->name,
            'broj_timova' => $teams->count(),
            'tabela' => $teams,
        ], 200);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Result;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function index(Request $request)
    {
        $query = Result::with(['team', 'event']);

        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        if ($request->filled('team_id')) {
            $query->where('team_id', $request->team_id);
        }

        $results = $query->orderBy('points', 'desc')->paginate(15);

        return response()->json($results, 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'event_id' => 'required|exists:events,id',
            'team_id' => 'required|exists:teams,id',
            'points' => 'required|numeric|min:0',
        ]);

        $postoji = Result::where('event_id', $data['event_id'])
            ->where('team_id', $data['team_id'])
            ->exists();

        if ($postoji) {
            return response()->json([
                'message' => 'Rezultat za ovaj tim na ovom dogadjaju vec postoji.',
            ], 409);
        }

        $result = Result::create($data);

        return response()->json([
            'message' => 'Rezultat je uspesno upisan.',
            'result' => $result->load(['team', 'event']),
        ], 201);
    }

    public function show(Result $result)
    {
        $result->load(['team', 'event']);

        return response()->json($result, 200);
    }

    public function update(Request $request, Result $result)
    {
        $data = $request->validate([
            'points' => 'required|numeric|min:0',
        ]);

        $result->update($data);

        return response()->json([
            'message' => 'Rezultat je uspesno izmenjen.',
            'result' => $result->fresh(),
        ], 200);
    }

    public function destroy(Result $result)
    {
        $result->delete();

        return response()->json([
            'message' => 'Rezultat je uspesno obrisan.',
        ], 200);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with(['season', 'winner']);

        if ($request->filled('season_id')) {
            $query->where('season_id', $request->season_id);
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        $events = $query->orderBy('event_date', 'desc')->paginate(10);

        return response()->json($events, 200);
    }

    public function upcoming()
    {
        $events = Event::with('season')
            ->where('event_date', '>=', now())
            ->orderBy('event_date', 'asc')
            ->get();

        return response()->json([
            'message' => 'Aktuelni i predstojeci dogadjaji.',
            'count' => $events->count(),
            'events' => $events,
        ], 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'season_id' => 'required|exists:seasons,id',
            'name' => 'required|string|max:255',
            'event_date' => 'required|date',
            'location' => 'nullable|string|max:255',
        ]);

        $event = Event::create($data);

        return response()->json([
            'message' => 'Dogadjaj je uspesno kreiran.',
            'event' => $event,
        ], 201);
    }

    public function show(Event $event)
    {
        $event->load(['season', 'winner', 'results.team']);

        return response()->json($event, 200);
    }

    public function update(Request $request, Event $event)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'event_date' => 'sometimes|required|date',
            'location' => 'nullable|string|max:255',
            'winner_team_id' => 'nullable|exists:teams,id',
        ]);

        $event->update($data);

        return response()->json([
            'message' => 'Dogadjaj je uspesno izmenjen.',
            'event' => $event->fresh(),
        ], 200);
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return response()->json([
            'message' => 'Dogadjaj je uspesno obrisan.',
        ], 200);
    }
}
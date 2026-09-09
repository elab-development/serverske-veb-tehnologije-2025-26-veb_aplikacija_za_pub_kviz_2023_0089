<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'amount' => 'nullable|integer|min:1|max:20',
            'difficulty' => 'nullable|in:easy,medium,hard',
        ]);

        $amount = $data['amount'] ?? 10;
        $difficulty = $data['difficulty'] ?? null;

        $parametri = ['amount' => $amount, 'type' => 'multiple'];

        if ($difficulty) {
            $parametri['difficulty'] = $difficulty;
        }

        $odgovor = Http::timeout(10)->get('https://opentdb.com/api.php', $parametri);

        if ($odgovor->failed()) {
            return response()->json([
                'message' => 'Javni servis trenutno nije dostupan.',
            ], 503);
        }

        $telo = $odgovor->json();

        if (($telo['response_code'] ?? 1) !== 0) {
            return response()->json([
                'message' => 'Javni servis nije vratio pitanja za zadate kriterijume.',
            ], 404);
        }

        $pitanja = collect($telo['results'])->map(function ($p) {
            return [
                'kategorija' => html_entity_decode($p['category']),
                'tezina' => $p['difficulty'],
                'pitanje' => html_entity_decode($p['question']),
                'tacan_odgovor' => html_entity_decode($p['correct_answer']),
                'ponudjeni_odgovori' => collect($p['incorrect_answers'])
                    ->push($p['correct_answer'])
                    ->map(fn ($o) => html_entity_decode($o))
                    ->shuffle()
                    ->values(),
            ];
        });

        return response()->json([
            'message' => 'Pitanja preuzeta sa javnog servisa Open Trivia Database.',
            'izvor' => 'https://opentdb.com',
            'broj_pitanja' => $pitanja->count(),
            'pitanja' => $pitanja,
        ], 200);
    }
}
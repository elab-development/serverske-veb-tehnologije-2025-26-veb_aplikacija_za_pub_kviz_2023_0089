<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SeasonController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ResultController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/events/upcoming', [EventController::class, 'upcoming']);
Route::get('/seasons/{season}/scoreboard', [SeasonController::class, 'scoreboard']);

Route::apiResource('seasons', SeasonController::class)->only(['index', 'show']);
Route::apiResource('events', EventController::class)->only(['index', 'show']);
Route::apiResource('results', ResultController::class)->only(['index', 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    });

    Route::apiResource('seasons', SeasonController::class)->except(['index', 'show']);
    Route::apiResource('events', EventController::class)->except(['index', 'show']);
    Route::apiResource('results', ResultController::class)->except(['index', 'show']);
});
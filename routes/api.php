<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\EventoParticipacionController;

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // EVENTOS ONG + EXTERNO
    Route::prefix('events')->group(function () {
        Route::get('/ong/{id}', [EventController::class, 'indexByOng']);
        Route::get('/', [EventController::class, 'indexAll']);
        Route::post('/', [EventController::class, 'store']);
        Route::get('/detalle/{id}', [EventController::class, 'show']);
        Route::put('/{id}', [EventController::class, 'update']);
        Route::delete('/{id}', [EventController::class, 'destroy']);
    });

    // PARTICIPACIÓN
    Route::post('/eventos/participar', [EventoParticipacionController::class, 'inscribir']);
    Route::post('/eventos/cancelar', [EventoParticipacionController::class, 'cancelar']);
    Route::get('/eventos/mis-eventos', [EventoParticipacionController::class, 'misEventos']);
});

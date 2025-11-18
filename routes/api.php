<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\EventoParticipacionController;
use App\Http\Controllers\Api\VoluntarioController;

// ----------- AUTH -----------
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login',    [AuthController::class, 'login']);

// Rutas protegidas con SANCTUM
Route::middleware('auth:sanctum')->group(function () {

    // LOGOUT (agregar método en AuthController)
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // ----------- EVENTOS -----------
    Route::prefix('eventos')->group(function () {

        // ONG
        Route::get('/ong/{ongId}',       [EventController::class, 'indexByOng']);
        Route::post('/',                 [EventController::class, 'store']);
        Route::put('/{id}',              [EventController::class, 'update']);
        Route::delete('/{id}',           [EventController::class, 'destroy']);

        // TODOS LOS PUBLICADOS
        Route::get('/',                  [EventController::class, 'indexAll']);

        // DETALLE
        Route::get('/detalle/{id}',      [EventController::class, 'show']);

        // EMPRESAS E INVITADOS
        Route::get('/empresas/disponibles', [EventController::class, 'empresasDisponibles']);
        Route::get('/invitados',         [EventController::class, 'invitadosDisponibles']);
        
        // PATROCINADORES
        Route::post('/{id}/patrocinar', [EventController::class, 'agregarPatrocinador']);
    });

    // ----------- PARTICIPACIÓN -----------
    Route::post('/participaciones/inscribir', [EventoParticipacionController::class, 'inscribir']);
    Route::post('/participaciones/cancelar',  [EventoParticipacionController::class, 'cancelar']);
    Route::get('/participaciones/mis-eventos', [EventoParticipacionController::class, 'misEventos']);

    // ----------- VOLUNTARIOS -----------
    Route::get('/voluntarios/ong/{ongId}', [VoluntarioController::class, 'indexByOng']);
});

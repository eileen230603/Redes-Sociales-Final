<?php

use Illuminate\Support\Facades\Route;

// LOGIN / REGISTROS
Route::view('/', 'auth.login')->name('inicio');
Route::view('/login', 'auth.login')->name('login');

Route::view('/register-ong', 'auth.register-ong')->name('register.ong');
Route::view('/register-empresa', 'auth.register-empresa')->name('register.empresa');
Route::view('/register-externo', 'auth.register-externo')->name('register.externo');


// =======================
// HOME SEGÚN PERFIL
// =======================
Route::view('/home-ong', 'home-ong')->name('home.ong');
Route::view('/home-publica', 'home-publica')->name('home.publica');
Route::view('/home-empresa', 'home-empresa')->name('home.empresa');
Route::view('/home-externo', 'externo.home')->name('home.externo');   // ← CORRECTO


// =======================
// EVENTOS EXTERNO
// =======================
Route::prefix('externo/eventos')->group(function () {

    Route::view('/', 'externo.eventos.index')->name('externo.eventos.index');

    Route::view('/{id}/detalle', 'externo.eventos.show')->name('externo.eventos.show');

});


// =======================
// EVENTOS ONG
// =======================
Route::prefix('ong/eventos')->name('ong.eventos.')->group(function () {

    Route::view('/', 'ong.eventos.index')->name('index');

    Route::view('/crear', 'ong.eventos.create')->name('create');

    Route::view('/{id}/editar', 'ong.eventos.edit')->name('edit');

    Route::view('/{id}/detalle', 'ong.eventos.show')->name('show');

});


// REDIRECCIONES
Route::redirect('/eventos', '/ong/eventos');
Route::redirect('/home', '/home-publica');


// ERROR 404
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});

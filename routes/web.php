<?php

use Illuminate\Support\Facades\Route;

// ---------------- AUTH ----------------
Route::view('/', 'auth.login')->name('inicio');
Route::view('/login', 'auth.login')->name('login');

Route::view('/register-ong', 'auth.register-ong')->name('register.ong');
Route::view('/register-empresa', 'auth.register-empresa')->name('register.empresa');
Route::view('/register-externo', 'auth.register-externo')->name('register.externo');

// ---------------- HOME ----------------
Route::view('/home-publica', 'home-publica')->name('home.publica');
Route::view('/home-ong', 'home-ong')->name('home.ong');
Route::view('/home-empresa', 'home-empresa')->name('home.empresa');
Route::view('/home-externo', 'externo.home')->name('home.externo');

// ---------------- EXTERNO: EVENTOS ----------------
Route::prefix('externo/eventos')->group(function () {
    Route::view('/', 'externo.eventos.index')->name('externo.eventos.index');
    Route::view('/{id}/detalle', 'externo.eventos.show')->name('externo.eventos.show');
});

// ---------------- ONG: EVENTOS ----------------
Route::prefix('ong/eventos')->name('ong.eventos.')->group(function () {
    Route::view('/', 'ong.eventos.index')->name('index');
    Route::view('/crear', 'ong.eventos.create')->name('create');
    Route::view('/{id}/editar', 'ong.eventos.edit')->name('edit');
    Route::view('/{id}/detalle', 'ong.eventos.show')->name('show');
});

// ---------------- ONG: VOLUNTARIOS ----------------
Route::prefix('ong/voluntarios')->name('ong.voluntarios.')->group(function () {
    Route::view('/', 'ong.voluntarios.index')->name('index');
});

// ---------------- ONG: REPORTES ----------------
Route::prefix('ong/reportes')->name('ong.reportes.')->group(function () {
    Route::view('/', 'ong.reportes.index')->name('index');
});

// ---------------- EMPRESA: EVENTOS ----------------
Route::prefix('empresa/eventos')->name('empresa.eventos.')->group(function () {
    Route::view('/', 'empresa.eventos.index')->name('index');
    Route::view('/disponibles', 'empresa.eventos.disponibles')->name('disponibles');
});

// ---------------- EMPRESA: REPORTES ----------------
Route::prefix('empresa/reportes')->name('empresa.reportes.')->group(function () {
    Route::view('/', 'empresa.reportes.index')->name('index');
});

// ---------------- LOGOUT ----------------
Route::get('/logout', function () {
    // Limpiar localStorage y redirigir al login
    return redirect('/login')->with('logout', true);
})->name('logout');

// ---------------- REDIRECTS ----------------
Route::redirect('/eventos', '/ong/eventos');
Route::redirect('/voluntarios', '/ong/voluntarios');
Route::redirect('/reportes', '/ong/reportes');
Route::redirect('/home', '/home-publica');

// ---------------- 404 ----------------
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});

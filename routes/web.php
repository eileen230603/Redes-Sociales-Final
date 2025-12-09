<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

// ---------------- PROXY DE IMÁGENES EXTERNAS (PARA EVITAR CORS) ----------------
Route::options('/api/image-proxy', [App\Http\Controllers\ImageProxyController::class, 'options']);
Route::get('/api/image-proxy', [App\Http\Controllers\ImageProxyController::class, 'proxy']);

// ---------------- SERVIR ARCHIVOS DE STORAGE CON CORS (MÁXIMA PRIORIDAD) ----------------
// Esta ruta DEBE estar al principio para tener máxima prioridad
Route::options('/storage/{path}', [App\Http\Controllers\StorageController::class, 'options'])
    ->where('path', '.*');

Route::get('/storage/{path}', [App\Http\Controllers\StorageController::class, 'serve'])
    ->where('path', '.*');

// ---------------- AUTH ----------------
Route::view('/', 'welcome')->name('inicio');
Route::view('/login', 'auth.login')->name('login');

Route::view('/register-ong', 'auth.register-ong')->name('register.ong');
Route::view('/register-empresa', 'auth.register-empresa')->name('register.empresa');
Route::view('/register-externo', 'auth.register-externo')->name('register.externo');

// ---------------- HOME ----------------
Route::view('/home-publica', 'home-publica')->name('home.publica');
Route::view('/home-ong', 'home-ong')->name('home.ong');
Route::view('/home-empresa', 'home-empresa')->name('home.empresa');
Route::view('/home-externo', 'home-externo')->name('home.externo');

// ---------------- ACCESO PÚBLICO A EVENTOS (QR) ----------------
Route::get('/evento/{id}/qr', [App\Http\Controllers\EventoPublicoController::class, 'show'])->name('evento.publico.qr');

// ---------------- ACCESO PÚBLICO A MEGA EVENTOS (QR) ----------------
Route::get('/mega-evento/{id}/qr', [App\Http\Controllers\MegaEventoPublicoController::class, 'show'])->name('mega-evento.publico.qr');

// ---------------- EXTERNO: EVENTOS ----------------
Route::prefix('externo/eventos')->group(function () {
    Route::view('/', 'externo.eventos.index')->name('externo.eventos.index');
    Route::view('/{id}/detalle', 'externo.eventos.show')->name('externo.eventos.show');
});

// ---------------- EXTERNO: MIS PARTICIPACIONES ----------------
Route::view('/externo/mis-participaciones', 'externo.mis-participaciones')->name('externo.mis-participaciones');

// ---------------- EXTERNO: REPORTES ----------------
Route::view('/externo/reportes', 'externo.reportes.index')->name('externo.reportes.index');

// ---------------- EXTERNO: MEGA EVENTOS ----------------
Route::prefix('externo/mega-eventos')->name('externo.mega-eventos.')->group(function () {
    Route::view('/', 'externo.mega-eventos.index')->name('index');
    Route::view('/{id}/detalle', 'externo.mega-eventos.show')->name('show');
});

// ---------------- VOLUNTARIO: MEGA EVENTOS ----------------
Route::prefix('voluntario/mega-eventos')->name('voluntario.mega-eventos.')->group(function () {
    Route::view('/', 'externo.mega-eventos.index')->name('index');
    Route::view('/{id}/detalle', 'externo.mega-eventos.show')->name('show');
});

// ---------------- ONG: EVENTOS ----------------
Route::prefix('ong/eventos')->name('ong.eventos.')->group(function () {
    Route::view('/', 'ong.eventos.index')->name('index');
    Route::view('/en-curso', 'ong.eventos.en-curso')->name('en-curso');
    Route::view('/historial', 'ong.eventos.historial')->name('historial');
    Route::view('/crear', 'ong.eventos.create')->name('create');
    Route::view('/{id}/editar', 'ong.eventos.edit')->name('edit');
    Route::view('/{id}/detalle', 'ong.eventos.show')->name('show');
    Route::view('/{id}/dashboard', 'ong.eventos.dashboard-evento')->name('dashboard-evento');
});

// ---------------- ONG: VOLUNTARIOS ----------------
Route::prefix('ong/voluntarios')->name('ong.voluntarios.')->group(function () {
    Route::view('/', 'ong.voluntarios.index')->name('index');
});

// ---------------- ONG: REPORTES ----------------
Route::prefix('ong/reportes')->name('ong.reportes.')->group(function () {
    Route::view('/', 'ong.reportes.index')->name('index');
});

// ---------------- ONG: NOTIFICACIONES ----------------
Route::prefix('ong/notificaciones')->name('ong.notificaciones.')->group(function () {
    Route::view('/', 'ong.notificaciones.index')->name('index');
});

// ---------------- ONG: DASHBOARD ----------------
Route::prefix('ong/dashboard')->name('ong.dashboard.')->group(function () {
    Route::view('/', 'ong.dashboard.index')->name('index');
});

// ---------------- ONG: DASHBOARD DE EVENTOS ----------------
Route::prefix('ong/eventos-dashboard')->name('ong.eventos-dashboard.')->group(function () {
    Route::view('/', 'ong.eventos.dashboard')->name('index');
});

// ---------------- ONG: MEGA EVENTOS ----------------
Route::prefix('ong/mega-eventos')->name('ong.mega-eventos.')->group(function () {
    Route::view('/', 'ong.mega-eventos.index')->name('index');
    Route::view('/en-curso', 'ong.mega-eventos.en-curso')->name('en-curso');
    Route::view('/historial', 'ong.mega-eventos.historial')->name('historial');
    Route::view('/crear', 'ong.mega-eventos.create')->name('create');
    Route::view('/{id}/editar', 'ong.mega-eventos.edit')->name('edit');
    Route::view('/{id}/detalle', 'ong.mega-eventos.show')->name('show');
    Route::view('/{id}/seguimiento', 'ong.mega-eventos.seguimiento')->name('seguimiento');
});

// ---------------- EMPRESA: MEGA EVENTOS ----------------
Route::prefix('empresa/mega-eventos')->name('empresa.mega-eventos.')->group(function () {
    Route::view('/', 'empresa.mega-eventos.index')->name('index');
    Route::view('/{id}/detalle', 'empresa.mega-eventos.show')->name('show');
});

// ---------------- EMPRESA: NOTIFICACIONES ----------------
Route::prefix('empresa/notificaciones')->name('empresa.notificaciones.')->group(function () {
    Route::view('/', 'empresa.notificaciones.index')->name('index');
});

// ---------------- EMPRESA: EVENTOS ----------------
Route::prefix('empresa/eventos')->name('empresa.eventos.')->group(function () {
    Route::view('/', 'empresa.eventos.index')->name('index');
    Route::view('/disponibles', 'empresa.eventos.disponibles')->name('disponibles');
    Route::view('/{id}/detalle', 'empresa.eventos.show')->name('show');
});

// ---------------- EMPRESA: REPORTES ----------------
Route::prefix('empresa/reportes')->name('empresa.reportes.')->group(function () {
    Route::view('/', 'empresa.reportes.index')->name('index');
});

// ---------------- PERFIL ----------------
Route::prefix('perfil')->name('perfil.')->group(function () {
    Route::view('/ong', 'ong.perfil')->name('ong');
    Route::view('/empresa', 'empresa.perfil')->name('empresa');
    Route::view('/externo', 'externo.perfil')->name('externo');
});

// ---------------- CONFIGURACIÓN / PARÁMETROS ----------------
Route::prefix('configuracion')->name('configuracion.')->group(function () {
    Route::get('/', [App\Http\Controllers\ConfiguracionController::class, 'index'])->name('index');
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

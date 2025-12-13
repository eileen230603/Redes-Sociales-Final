<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\EventoParticipacionController;
use App\Http\Controllers\Api\EventoEmpresaParticipacionController;
use App\Http\Controllers\Api\EventoReaccionController;
use App\Http\Controllers\Api\EventoCompartidoController;
use App\Http\Controllers\Api\MegaEventoReaccionController;
use App\Http\Controllers\Api\MegaEventoCompartidoController;
use App\Http\Controllers\Api\NotificacionController;
use App\Http\Controllers\Api\DashboardOngController;
use App\Http\Controllers\Api\DashboardExternoController;
use App\Http\Controllers\Api\VoluntarioController;
use App\Http\Controllers\Api\ConfiguracionController;
use App\Http\Controllers\Api\ParametrizacionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MegaEventoController;
use App\Http\Controllers\StorageController;

// ----------- CORS PREFLIGHT (debe estar al inicio) -----------
Route::options('{any}', function () {
    return response('', 200)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS, PATCH')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, Origin')
        ->header('Access-Control-Max-Age', '86400');
})->where('any', '.*');

// ----------- STORAGE (CORS para Flutter) -----------
Route::options('/storage/{path}', [StorageController::class, 'options'])
    ->where('path', '.*');
Route::get('/storage/{path}', [StorageController::class, 'serve'])
    ->where('path', '.*');

// ----------- AUTH -----------
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login',    [AuthController::class, 'login']);

// ----------- MEGA EVENTOS PÚBLICOS (SIN AUTENTICACIÓN) -----------
Route::get('/mega-eventos/publicos', [MegaEventoController::class, 'publicos']);

// Rutas protegidas con SANCTUM
Route::middleware('auth:sanctum')->group(function () {

    // LOGOUT (agregar método en AuthController)
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // ----------- EVENTOS -----------
    Route::prefix('eventos')->group(function () {

        // ONG
        Route::get('/ong/{ongId}',       [EventController::class, 'indexByOng']);
        Route::get('/ong/{ongId}/dashboard', [EventController::class, 'dashboardPorEstado']);
        Route::post('/',                 [EventController::class, 'store']);
        Route::put('/{id}',              [EventController::class, 'update']);
        Route::delete('/{id}',           [EventController::class, 'destroy']);

        // TODOS LOS PUBLICADOS
        Route::get('/',                  [EventController::class, 'indexAll']);

        // DETALLE
        Route::get('/detalle/{id}',      [EventController::class, 'show']);

        // DASHBOARD DEL EVENTO (ruta específica con restricción numérica)
        Route::get('/{id}/dashboard', [EventController::class, 'dashboard'])->where('id', '[0-9]+');
        Route::get('/{id}/dashboard/pdf', [EventController::class, 'dashboardPdf'])->where('id', '[0-9]+');

        // EMPRESAS E INVITADOS
        Route::get('/empresas/disponibles', [EventController::class, 'empresasDisponibles']);
        Route::get('/invitados',         [EventController::class, 'invitadosDisponibles']);
        
        // PATROCINADORES
        Route::post('/{id}/patrocinar', [EventController::class, 'agregarPatrocinador'])->where('id', '[0-9]+');
    });

    // ----------- PARTICIPACIÓN -----------
    Route::post('/participaciones/inscribir', [EventoParticipacionController::class, 'inscribir']);
    Route::post('/participaciones/cancelar',  [EventoParticipacionController::class, 'cancelar']);
    Route::get('/participaciones/mis-eventos', [EventoParticipacionController::class, 'misEventos']);
    Route::get('/participaciones/evento/{eventoId}', [EventoParticipacionController::class, 'participantesEvento']);
    Route::put('/participaciones/{participacionId}/aprobar', [EventoParticipacionController::class, 'aprobar']);
    Route::put('/participaciones/{participacionId}/rechazar', [EventoParticipacionController::class, 'rechazar']);

    // ----------- REACCIONES (Favoritos) -----------
    Route::post('/reacciones/toggle', [EventoReaccionController::class, 'toggle']);
    Route::get('/reacciones/verificar/{eventoId}', [EventoReaccionController::class, 'verificar']);
    Route::get('/reacciones/evento/{eventoId}', [EventoReaccionController::class, 'usuariosQueReaccionaron']);

    // ----------- COMPARTIDOS EVENTOS -----------
    Route::post('/eventos/{eventoId}/compartir', [EventoCompartidoController::class, 'compartir']);

    // ----------- NOTIFICACIONES -----------
    Route::prefix('notificaciones')->group(function () {
        Route::get('/', [NotificacionController::class, 'index']);
        Route::get('/contador', [NotificacionController::class, 'contador']);
        Route::put('/{id}/leida', [NotificacionController::class, 'marcarLeida']);
        Route::put('/marcar-todas', [NotificacionController::class, 'marcarTodasLeidas']);
    });

    // ----------- NOTIFICACIONES (EMPRESA) -----------
    Route::prefix('empresas/notificaciones')->group(function () {
        Route::get('/', [NotificacionController::class, 'indexEmpresa']);
        Route::get('/contador', [NotificacionController::class, 'contadorEmpresa']);
        Route::put('/{id}/leida', [NotificacionController::class, 'marcarLeidaEmpresa']);
        Route::put('/marcar-todas', [NotificacionController::class, 'marcarTodasLeidasEmpresa']);
    });

    // ----------- DASHBOARD ONG -----------
    Route::prefix('dashboard-ong')->group(function () {
        Route::get('/estadisticas-generales', [DashboardOngController::class, 'estadisticasGenerales']);
        Route::get('/participantes/estadisticas', [DashboardOngController::class, 'estadisticasParticipantes']);
        Route::get('/participantes/lista', [DashboardOngController::class, 'listaParticipantes']);
        Route::get('/reacciones/estadisticas', [DashboardOngController::class, 'estadisticasReacciones']);
        Route::get('/reacciones/lista', [DashboardOngController::class, 'listaReacciones']);
    });

    // ----------- DASHBOARD EXTERNO -----------
    Route::prefix('dashboard-externo')->group(function () {
        Route::get('/estadisticas-generales', [DashboardExternoController::class, 'estadisticasGenerales']);
        Route::get('/datos-detallados', [DashboardExternoController::class, 'datosDetallados']);
        Route::get('/eventos-disponibles', [DashboardExternoController::class, 'eventosDisponibles']);
        Route::get('/descargar-pdf-completo', [DashboardExternoController::class, 'descargarPdfCompleto']);
    });

    // ----------- VOLUNTARIOS -----------
    Route::get('/voluntarios/ong/{ongId}', [VoluntarioController::class, 'indexByOng']);

    // ----------- PERFIL -----------
    Route::prefix('perfil')->group(function () {
        Route::get('/', [ProfileController::class, 'show']);
        Route::put('/', [ProfileController::class, 'update']);
        Route::post('/', [ProfileController::class, 'update']); // POST para mejor compatibilidad con FormData
    });

    // ----------- MEGA EVENTOS -----------
    Route::prefix('mega-eventos')->group(function () {
        Route::get('/', [MegaEventoController::class, 'index']);
        Route::get('/mis-participaciones', [MegaEventoController::class, 'misParticipaciones']);
        Route::get('/seguimiento/general', [MegaEventoController::class, 'seguimientoGeneral']);
        Route::post('/', [MegaEventoController::class, 'store']);
        Route::get('/{id}', [MegaEventoController::class, 'show']);
        // Compartidos mega eventos
        Route::post('/{megaEventoId}/compartir', [MegaEventoCompartidoController::class, 'compartir']);
        Route::get('/{megaEventoId}/compartidos/total', [MegaEventoCompartidoController::class, 'totalCompartidos']);
        // Reacciones mega eventos
        Route::post('/reacciones/toggle', [MegaEventoReaccionController::class, 'toggle']);
        Route::get('/reacciones/verificar/{megaEventoId}', [MegaEventoReaccionController::class, 'verificar']);
        Route::get('/reacciones/{megaEventoId}', [MegaEventoReaccionController::class, 'usuariosQueReaccionaron']);
        // Participación y seguimiento
        Route::post('/{id}/participar', [MegaEventoController::class, 'participar']);
        Route::get('/{id}/verificar-participacion', [MegaEventoController::class, 'verificarParticipacion']);
        Route::get('/{id}/seguimiento', [MegaEventoController::class, 'seguimiento']);
        Route::get('/{id}/participantes', [MegaEventoController::class, 'participantes']);
        Route::get('/{id}/historial', [MegaEventoController::class, 'historial']);
        // CRUD
        Route::put('/{id}', [MegaEventoController::class, 'update']);
        Route::delete('/{id}', [MegaEventoController::class, 'destroy']);
        Route::delete('/{id}/imagen', [MegaEventoController::class, 'deleteImage']);
        Route::get('/{id}/exportar-excel', [MegaEventoController::class, 'exportarExcel'])->where('id', '[0-9]+');
    });

    // ----------- EMPRESAS PARTICIPANTES (COLABORADORAS) -----------
    Route::prefix('eventos/{eventoId}/empresas')->group(function () {
        Route::post('/asignar', [EventoEmpresaParticipacionController::class, 'asignarEmpresas']);
        Route::post('/remover', [EventoEmpresaParticipacionController::class, 'removerEmpresas']);
        Route::get('/', [EventoEmpresaParticipacionController::class, 'empresasParticipantes']);
        Route::get('/verificar', [EventoEmpresaParticipacionController::class, 'verificarParticipacion']);
    });

    // ----------- EMPRESAS: MIS EVENTOS -----------
    Route::prefix('empresas')->group(function () {
        Route::post('/eventos/{eventoId}/confirmar', [EventoEmpresaParticipacionController::class, 'confirmarParticipacion']);
        Route::get('/mis-eventos', [EventoEmpresaParticipacionController::class, 'misEventos']);
    });

    // ----------- PARTICIPACIONES NO REGISTRADAS -----------
    Route::prefix('participaciones-no-registradas')->group(function () {
        Route::put('/{participacionId}/aprobar', [EventoParticipacionController::class, 'aprobarNoRegistrado']);
        Route::put('/{participacionId}/rechazar', [EventoParticipacionController::class, 'rechazarNoRegistrado']);
    });

    // ----------- CONFIGURACIÓN / PARÁMETROS -----------
    Route::prefix('configuracion')->group(function () {
        Route::get('/', [ConfiguracionController::class, 'index']);
        Route::get('/categorias', [ConfiguracionController::class, 'categorias']);
        Route::get('/grupos', [ConfiguracionController::class, 'grupos']);
        Route::get('/codigo/{codigo}', [ConfiguracionController::class, 'porCodigo']);
        Route::post('/', [ConfiguracionController::class, 'store']);
        Route::get('/{id}', [ConfiguracionController::class, 'show']);
        Route::put('/{id}', [ConfiguracionController::class, 'update']);
        Route::put('/{id}/valor', [ConfiguracionController::class, 'actualizarValor']);
        Route::delete('/{id}', [ConfiguracionController::class, 'destroy']);
    });

    // ----------- PARAMETRIZACIONES -----------
    Route::prefix('parametrizaciones')->group(function () {
        // Tipos de Evento
        Route::get('/tipos-evento', [ParametrizacionController::class, 'tiposEvento']);
        Route::post('/tipos-evento', [ParametrizacionController::class, 'crearTipoEvento']);
        Route::put('/tipos-evento/{id}', [ParametrizacionController::class, 'actualizarTipoEvento']);
        Route::delete('/tipos-evento/{id}', [ParametrizacionController::class, 'eliminarTipoEvento']);

        // Categorías de Mega Eventos
        Route::get('/categorias-mega-evento', [ParametrizacionController::class, 'categoriasMegaEvento']);
        Route::post('/categorias-mega-evento', [ParametrizacionController::class, 'crearCategoriaMegaEvento']);
        Route::put('/categorias-mega-evento/{id}', [ParametrizacionController::class, 'actualizarCategoriaMegaEvento']);
        Route::delete('/categorias-mega-evento/{id}', [ParametrizacionController::class, 'eliminarCategoriaMegaEvento']);

        // Ciudades
        Route::get('/ciudades', [ParametrizacionController::class, 'ciudades']);
        Route::post('/ciudades', [ParametrizacionController::class, 'crearCiudad']);
        Route::put('/ciudades/{id}', [ParametrizacionController::class, 'actualizarCiudad']);
        Route::delete('/ciudades/{id}', [ParametrizacionController::class, 'eliminarCiudad']);

        // Lugares
        Route::get('/lugares', [ParametrizacionController::class, 'lugares']);
        Route::post('/lugares', [ParametrizacionController::class, 'crearLugar']);
        Route::put('/lugares/{id}', [ParametrizacionController::class, 'actualizarLugar']);
        Route::delete('/lugares/{id}', [ParametrizacionController::class, 'eliminarLugar']);

        // Estados de Participación
        Route::get('/estados-participacion', [ParametrizacionController::class, 'estadosParticipacion']);
        Route::post('/estados-participacion', [ParametrizacionController::class, 'crearEstadoParticipacion']);
        Route::put('/estados-participacion/{id}', [ParametrizacionController::class, 'actualizarEstadoParticipacion']);
        Route::delete('/estados-participacion/{id}', [ParametrizacionController::class, 'eliminarEstadoParticipacion']);

        // Tipos de Notificación
        Route::get('/tipos-notificacion', [ParametrizacionController::class, 'tiposNotificacion']);
        Route::post('/tipos-notificacion', [ParametrizacionController::class, 'crearTipoNotificacion']);
        Route::put('/tipos-notificacion/{id}', [ParametrizacionController::class, 'actualizarTipoNotificacion']);
        Route::delete('/tipos-notificacion/{id}', [ParametrizacionController::class, 'eliminarTipoNotificacion']);

        // Estados de Evento
        Route::get('/estados-evento', [ParametrizacionController::class, 'estadosEvento']);
        Route::post('/estados-evento', [ParametrizacionController::class, 'crearEstadoEvento']);
        Route::put('/estados-evento/{id}', [ParametrizacionController::class, 'actualizarEstadoEvento']);
        Route::delete('/estados-evento/{id}', [ParametrizacionController::class, 'eliminarEstadoEvento']);

        // Tipos de Usuario
        Route::get('/tipos-usuario', [ParametrizacionController::class, 'tiposUsuario']);
        Route::post('/tipos-usuario', [ParametrizacionController::class, 'crearTipoUsuario']);
        Route::put('/tipos-usuario/{id}', [ParametrizacionController::class, 'actualizarTipoUsuario']);
        Route::delete('/tipos-usuario/{id}', [ParametrizacionController::class, 'eliminarTipoUsuario']);
    });
});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\EventoParticipacionController;
use App\Http\Controllers\Api\EventoReaccionController;
use App\Http\Controllers\Api\MegaEventoReaccionController;
use App\Http\Controllers\Api\EventoCompartidoController;
use App\Http\Controllers\Api\MegaEventoCompartidoController;
use App\Http\Controllers\Api\NotificacionController;
use App\Http\Controllers\Api\DashboardOngController;
use App\Http\Controllers\Api\DashboardExternoController;
use App\Http\Controllers\Api\VoluntarioController;
use App\Http\Controllers\Api\ConfiguracionController;
use App\Http\Controllers\Api\ParametrizacionController;
use App\Http\Controllers\Api\EventoEmpresaParticipacionController;
use App\Http\Controllers\Api\EventoMetricaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MegaEventoController;
use App\Http\Controllers\StorageController;

// ----------- CORS PREFLIGHT CATCH-ALL (DEBE ESTAR PRIMERO) -----------
Route::options('{any}', function () {
    return response('', 200)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS, PATCH')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, Origin')
        ->header('Access-Control-Max-Age', '86400');
})->where('any', '.*');

// ----------- STORAGE (con CORS para Flutter) -----------
// Esta ruta debe estar antes de las protegidas para que funcione sin autenticación
Route::options('/storage/{path}', [StorageController::class, 'options'])
    ->where('path', '.*');
Route::get('/storage/{path}', [StorageController::class, 'serve'])
    ->where('path', '.*');

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
        Route::get('/ong/{ongId}/dashboard', [EventController::class, 'dashboardPorEstado']);
        
        // EMPRESAS E INVITADOS (rutas específicas antes de las genéricas)
        Route::get('/empresas/disponibles', [EventController::class, 'empresasDisponibles']);
        Route::get('/invitados',         [EventController::class, 'invitadosDisponibles']);

        // TODOS LOS PUBLICADOS (debe estar antes de rutas con parámetros)
        Route::get('/',                  [EventController::class, 'indexAll']);

        // DETALLE (ruta específica)
        Route::get('/detalle/{id}',      [EventController::class, 'show']);

        // DASHBOARD DEL EVENTO (ruta específica con restricción numérica)
        Route::get('/{id}/dashboard', [EventController::class, 'dashboard'])->where('id', '[0-9]+');
        Route::get('/{id}/dashboard/pdf', [EventController::class, 'dashboardPdf'])->where('id', '[0-9]+');
        
        // PATROCINADORES
        Route::post('/{id}/patrocinar', [EventController::class, 'agregarPatrocinador'])->where('id', '[0-9]+');
        
        // CRUD (al final, con restricción numérica)
        Route::post('/',                 [EventController::class, 'store']);
        Route::post('/{id}',             [EventController::class, 'update'])->where('id', '[0-9]+'); // POST para compatibilidad con FormData
        Route::put('/{id}',              [EventController::class, 'update'])->where('id', '[0-9]+'); // Mantener PUT para compatibilidad
        Route::delete('/{id}',           [EventController::class, 'destroy'])->where('id', '[0-9]+');
    });

    // ----------- EMPRESAS PARTICIPANTES (COLABORADORAS) -----------
    Route::prefix('eventos/{eventoId}/empresas')->group(function () {
        // Asignar empresas (ONG)
        Route::post('/asignar', [EventoEmpresaParticipacionController::class, 'asignarEmpresas']);
        // Remover empresas (ONG)
        Route::post('/remover', [EventoEmpresaParticipacionController::class, 'removerEmpresas']);
        // Ver empresas participantes
        Route::get('/', [EventoEmpresaParticipacionController::class, 'empresasParticipantes']);
        // Verificar participación
        Route::get('/verificar', [EventoEmpresaParticipacionController::class, 'verificarParticipacion']);
    });

    // ----------- EMPRESAS: MIS EVENTOS -----------
    Route::prefix('empresas')->group(function () {
        // Confirmar participación
        Route::post('/eventos/{eventoId}/confirmar', [EventoEmpresaParticipacionController::class, 'confirmarParticipacion']);
        // Mis eventos como empresa colaboradora
        Route::get('/mis-eventos', [EventoEmpresaParticipacionController::class, 'misEventos']);
    });

    // ----------- PARTICIPACIÓN -----------
    Route::post('/participaciones/inscribir', [EventoParticipacionController::class, 'inscribir']);
    Route::post('/participaciones/cancelar',  [EventoParticipacionController::class, 'cancelar']);
    Route::get('/participaciones/mis-eventos', [EventoParticipacionController::class, 'misEventos']);
    Route::get('/participaciones/evento/{eventoId}', [EventoParticipacionController::class, 'participantesEvento']);
    Route::put('/participaciones/{participacionId}/aprobar', [EventoParticipacionController::class, 'aprobar']);
    Route::put('/participaciones/{participacionId}/rechazar', [EventoParticipacionController::class, 'rechazar']);
    Route::put('/participaciones-no-registradas/{participacionId}/aprobar', [EventoParticipacionController::class, 'aprobarNoRegistrado']);
    Route::put('/participaciones-no-registradas/{participacionId}/rechazar', [EventoParticipacionController::class, 'rechazarNoRegistrado']);
    
    // ----------- CONTROL DE ASISTENCIA -----------
    // Usuario externo: marcar su propia asistencia
    Route::post('/eventos/{eventoId}/marcar-asistencia', [EventoParticipacionController::class, 'marcarAsistenciaUsuario']);
    Route::get('/eventos/activos-para-marcar', [EventoParticipacionController::class, 'eventosActivosParaMarcar']);
    // Validación desde welcome.php
    Route::get('/eventos/en-curso-usuario', [EventoParticipacionController::class, 'eventosEnCursoUsuario']);
    Route::get('/eventos/alertas-5-minutos', [EventoParticipacionController::class, 'alertas5Minutos']);
    Route::post('/verificar-ticket-welcome', [EventoParticipacionController::class, 'verificarTicketWelcome']);
    Route::post('/validar-asistencia-welcome', [EventoParticipacionController::class, 'validarAsistenciaWelcome']);
    // Validación para usuarios no registrados (sin autenticación)
    Route::post('/verificar-ticket-no-registrado-welcome', [EventoParticipacionController::class, 'verificarTicketNoRegistradoWelcome']);
    Route::post('/validar-asistencia-no-registrado-welcome', [EventoParticipacionController::class, 'validarAsistenciaNoRegistradoWelcome']);
    // Registrar descarga de QR (solo una vez)
    Route::post('/registrar-descarga-qr', [EventoParticipacionController::class, 'registrarDescargaQR']);
    // ONG: control de asistencia
    Route::get('/eventos/{eventoId}/control-asistencia', [EventoParticipacionController::class, 'controlAsistencia']);
    Route::put('/participaciones/{participacionId}/modificar-asistencia', [EventoParticipacionController::class, 'modificarAsistencia']);
    Route::get('/eventos/{eventoId}/exportar-asistencia-pdf', [EventoParticipacionController::class, 'exportarAsistenciaPDF']);
    Route::get('/eventos/{eventoId}/exportar-asistencia-excel', [EventoParticipacionController::class, 'exportarAsistenciaExcel']);
    Route::get('/eventos/{eventoId}/participantes-completo', [EventoParticipacionController::class, 'obtenerParticipantesCompleto']);
    Route::get('/eventos/{eventoId}/exportar-participantes-completo', [EventoParticipacionController::class, 'exportarParticipantesCompleto']);

    // ----------- REACCIONES (Favoritos) -----------
    Route::post('/reacciones/toggle', [EventoReaccionController::class, 'toggle']);
    Route::get('/reacciones/verificar/{eventoId}', [EventoReaccionController::class, 'verificar']);
    Route::get('/reacciones/evento/{eventoId}', [EventoReaccionController::class, 'usuariosQueReaccionaron']);

    // ----------- COMPARTIDOS -----------
    Route::post('/eventos/{eventoId}/compartir', [EventoCompartidoController::class, 'compartir']);

    // ----------- NOTIFICACIONES (ONG) -----------
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

    // ----------- MÉTRICAS Y KPIs DE EVENTOS -----------
    Route::prefix('metricas-eventos')->group(function () {
        // Métricas de un evento específico
        Route::get('/evento/{eventoId}', [EventoMetricaController::class, 'metricasEvento']);
        // Métricas agregadas de la ONG
        Route::get('/ong', [EventoMetricaController::class, 'metricasOng']);
        // Ciclo de vida completo de un evento
        Route::get('/evento/{eventoId}/ciclo-vida', [EventoMetricaController::class, 'cicloVidaEvento']);
        // Generar reporte PDF completo
        Route::get('/evento/{eventoId}/reporte-pdf', [EventoMetricaController::class, 'generarReportePdf']);
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
        Route::post('/', [ProfileController::class, 'update']); // Cambiado a POST para mejor compatibilidad con FormData
        Route::put('/', [ProfileController::class, 'update']); // Mantener PUT para compatibilidad
    });

    // ----------- MEGA EVENTOS -----------
    Route::prefix('mega-eventos')->group(function () {
        // Ruta OPTIONS para CORS preflight
        Route::options('/', function () {
            return response('', 200)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
        });
        Route::get('/', [MegaEventoController::class, 'index']);
        Route::get('/en-curso', [MegaEventoController::class, 'enCurso']);
        Route::get('/finalizados', [MegaEventoController::class, 'finalizados']);
        Route::get('/mis-participaciones', [MegaEventoController::class, 'misParticipaciones']);
        Route::post('/', [MegaEventoController::class, 'store']);
        // IMPORTANTE: Rutas con parámetros dinámicos deben ir al final, después de rutas específicas
        Route::get('/{id}', [MegaEventoController::class, 'show'])->where('id', '[0-9]+');
        // Compartidos
        Route::post('/{megaEventoId}/compartir', [MegaEventoCompartidoController::class, 'compartir'])->where('megaEventoId', '[0-9]+');
        Route::get('/{megaEventoId}/compartidos/total', [MegaEventoCompartidoController::class, 'totalCompartidos'])->where('megaEventoId', '[0-9]+');
        // Reacciones (usuarios registrados)
        Route::post('/reacciones/toggle', [MegaEventoReaccionController::class, 'toggle']);
        Route::get('/reacciones/verificar/{megaEventoId}', [MegaEventoReaccionController::class, 'verificar'])->where('megaEventoId', '[0-9]+');
        Route::get('/reacciones/{megaEventoId}', [MegaEventoReaccionController::class, 'usuariosQueReaccionaron'])->where('megaEventoId', '[0-9]+');
        Route::put('/{id}', [MegaEventoController::class, 'update'])->where('id', '[0-9]+');
        Route::delete('/{id}', [MegaEventoController::class, 'destroy'])->where('id', '[0-9]+');
        Route::delete('/{id}/imagen', [MegaEventoController::class, 'deleteImage'])->where('id', '[0-9]+');
        Route::post('/{id}/participar', [MegaEventoController::class, 'participar'])->where('id', '[0-9]+');
        Route::post('/{id}/cancelar-participacion', [MegaEventoController::class, 'cancelarParticipacion'])->where('id', '[0-9]+');
        Route::get('/{id}/verificar-participacion', [MegaEventoController::class, 'verificarParticipacion'])->where('id', '[0-9]+');
        // Registrar descarga de QR (solo una vez)
        Route::post('/registrar-descarga-qr', [MegaEventoController::class, 'registrarDescargaQR']);
        Route::get('/{id}/participantes', [MegaEventoController::class, 'participantes'])->where('id', '[0-9]+');
        // Control de asistencias
        Route::get('/{id}/control-asistencia', [MegaEventoController::class, 'controlAsistencia'])->where('id', '[0-9]+');
        Route::post('/{id}/registrar-asistencia', [MegaEventoController::class, 'registrarAsistencia'])->where('id', '[0-9]+');
        Route::post('/{id}/marcar-asistencia', [MegaEventoController::class, 'marcarAsistenciaUsuario'])->where('id', '[0-9]+'); // Usuario externo se auto-registra
        Route::put('/asistencias/{participacionId}/{tipo}', [MegaEventoController::class, 'modificarAsistencia'])->where('participacionId', '[0-9A-Za-z-]+');
        Route::get('/alertas-5-minutos', [MegaEventoController::class, 'alertas5Minutos']);
        // Rutas de seguimiento
        Route::get('/{id}/seguimiento', [MegaEventoController::class, 'seguimiento'])->where('id', '[0-9]+');
        Route::get('/{id}/historial', [MegaEventoController::class, 'historial'])->where('id', '[0-9]+');
        Route::get('/{id}/exportar-excel', [MegaEventoController::class, 'exportarExcel'])->where('id', '[0-9]+');
        Route::get('/seguimiento/general', [MegaEventoController::class, 'seguimientoGeneral']);
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

// ----------- REACCIONES PÚBLICAS (SIN AUTENTICACIÓN) -----------
Route::get('/reacciones/evento/{eventoId}/total', [EventoReaccionController::class, 'totalReacciones']);
Route::post('/reacciones/evento/{eventoId}/reaccionar-publico', [EventoReaccionController::class, 'reaccionarPublico']);

// ----------- REACCIONES PÚBLICAS MEGA EVENTOS (SIN AUTENTICACIÓN) -----------
Route::get('/reacciones/mega-evento/{megaEventoId}/total', [MegaEventoReaccionController::class, 'totalReacciones']);
Route::post('/reacciones/mega-evento/{megaEventoId}/reaccionar-publico', [MegaEventoReaccionController::class, 'reaccionarPublico']);

// ----------- COMPARTIDOS PÚBLICOS (SIN AUTENTICACIÓN) -----------
Route::post('/eventos/{eventoId}/compartir-publico', [EventoCompartidoController::class, 'compartir']);
Route::get('/eventos/{eventoId}/compartidos/total', [EventoCompartidoController::class, 'totalCompartidos']);

// ----------- COMPARTIDOS PÚBLICOS MEGA EVENTOS (SIN AUTENTICACIÓN) -----------
Route::post('/mega-eventos/{megaEventoId}/compartir-publico', [MegaEventoCompartidoController::class, 'compartir'])->where('megaEventoId', '[0-9]+');
Route::get('/mega-eventos/{megaEventoId}/compartidos/total', [MegaEventoCompartidoController::class, 'totalCompartidos'])->where('megaEventoId', '[0-9]+');

// ----------- PARTICIPACIÓN PÚBLICA (SIN AUTENTICACIÓN) -----------
// Ruta eliminada - Ya no se permite participación pública sin registro
// Route::post('/eventos/{eventoId}/participar-publico', [EventoParticipacionController::class, 'participarPublico']);
Route::post('/eventos/{eventoId}/verificar-participacion-publica', [EventoParticipacionController::class, 'verificarParticipacionPublica']);

// ----------- MEGA EVENTOS PÚBLICOS (SIN AUTENTICACIÓN) - DEBE ESTAR ANTES DE LAS RUTAS PROTEGIDAS -----------
Route::options('/mega-eventos/publicos', function () {
    return response('', 200)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, Origin')
        ->header('Access-Control-Max-Age', '86400');
});
Route::get('/mega-eventos/publicos', [MegaEventoController::class, 'publicos']);

// ----------- PARTICIPACIÓN PÚBLICA MEGA EVENTOS (SIN AUTENTICACIÓN) -----------
// Ruta eliminada - Ya no se permite participación pública sin registro
// Route::post('/mega-eventos/{megaEventoId}/participar-publico', [MegaEventoController::class, 'participarPublico'])->where('megaEventoId', '[0-9]+');
Route::post('/mega-eventos/{megaEventoId}/verificar-participacion-publica', [MegaEventoController::class, 'verificarParticipacionPublica'])->where('megaEventoId', '[0-9]+');

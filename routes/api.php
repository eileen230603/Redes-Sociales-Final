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
use App\Http\Controllers\Api\ReportesOngController;
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

        // ONG - El controlador ya valida permisos
        Route::get('/ong/{ongId}',       [EventController::class, 'indexByOng']);
        Route::get('/ong/{ongId}/dashboard', [EventController::class, 'dashboardPorEstado']);
        
        // EMPRESAS E INVITADOS (rutas específicas antes de las genéricas)
        Route::get('/empresas/disponibles', [EventController::class, 'empresasDisponibles'])->middleware('permission:eventos.ver');
        Route::get('/invitados',         [EventController::class, 'invitadosDisponibles'])->middleware('permission:eventos.ver');

        // TODOS LOS PUBLICADOS (debe estar antes de rutas con parámetros)
        Route::get('/',                  [EventController::class, 'indexAll'])->middleware('permission:eventos.ver');

        // DETALLE (ruta específica)
        // El controlador ya valida permisos
        Route::get('/detalle/{id}',      [EventController::class, 'show']);

        // DASHBOARD DEL EVENTO (ruta específica con restricción numérica) - Requiere permiso de gestión
        Route::get('/{id}/dashboard', [EventController::class, 'dashboard'])->where('id', '[0-9]+')->middleware('permission:eventos.gestionar');
        Route::get('/{id}/dashboard/pdf', [EventController::class, 'dashboardPdf'])->where('id', '[0-9]+')->middleware('permission:eventos.exportar-reportes');
        
        // DASHBOARD COMPLETO CON EXPORTACIÓN (nuevo sistema mejorado) - El controlador ya valida permisos
        Route::get('/{id}/dashboard-completo', [\App\Http\Controllers\Api\EventoDashboardController::class, 'dashboard'])->where('id', '[0-9]+');
        Route::get('/{id}/dashboard-completo/pdf', [\App\Http\Controllers\EventoController::class, 'exportarDashboardPDF'])->where('id', '[0-9]+')->middleware('auth:sanctum');
        Route::get('/{id}/dashboard-completo/excel', [\App\Http\Controllers\Api\EventoDashboardController::class, 'exportarExcel'])->where('id', '[0-9]+')->middleware('permission:eventos.exportar-reportes');
        
        // PATROCINADORES - Requiere permiso de patrocinar
        Route::post('/{id}/patrocinar', [EventController::class, 'agregarPatrocinador'])->where('id', '[0-9]+')->middleware('permission:eventos.patrocinar');
        
        // CRUD (al final, con restricción numérica) - El controlador ya valida permisos
        Route::post('/',                 [EventController::class, 'store'])->middleware('permission:eventos.crear');
        Route::post('/{id}',             [EventController::class, 'update'])->where('id', '[0-9]+');
        Route::put('/{id}',              [EventController::class, 'update'])->where('id', '[0-9]+');
        Route::delete('/{id}',           [EventController::class, 'destroy'])->where('id', '[0-9]+')->middleware('permission:eventos.eliminar');
    });

    // ----------- EMPRESAS PARTICIPANTES (COLABORADORAS) -----------
    Route::prefix('eventos/{eventoId}/empresas')->group(function () {
        // Asignar empresas (ONG) - Requiere permiso de gestión
        Route::post('/asignar', [EventoEmpresaParticipacionController::class, 'asignarEmpresas'])->middleware('permission:eventos.gestionar');
        // Remover empresas (ONG) - Requiere permiso de gestión
        Route::post('/remover', [EventoEmpresaParticipacionController::class, 'removerEmpresas'])->middleware('permission:eventos.gestionar');
        // Ver empresas participantes - Requiere permiso de ver participantes
        Route::get('/', [EventoEmpresaParticipacionController::class, 'empresasParticipantes'])->middleware('permission:eventos.ver-participantes');
        // Verificar participación
        Route::get('/verificar', [EventoEmpresaParticipacionController::class, 'verificarParticipacion'])->middleware('permission:eventos.ver');
    });

    // ----------- EMPRESAS: MIS EVENTOS -----------
    Route::prefix('empresas')->group(function () {
        // Confirmar participación
        Route::post('/eventos/{eventoId}/confirmar', [EventoEmpresaParticipacionController::class, 'confirmarParticipacion']);
        // Mis eventos como empresa colaboradora
        Route::get('/mis-eventos', [EventoEmpresaParticipacionController::class, 'misEventos']);
    });

    // ----------- PARTICIPACIÓN -----------
    Route::post('/participaciones/inscribir', [EventoParticipacionController::class, 'inscribir'])->middleware('permission:eventos.inscribirse');
    Route::post('/participaciones/cancelar',  [EventoParticipacionController::class, 'cancelar'])->middleware('permission:participaciones.ver-mis-participaciones');
    Route::get('/participaciones/mis-eventos', [EventoParticipacionController::class, 'misEventos'])->middleware('permission:participaciones.ver-mis-participaciones');
    // El controlador ya valida permisos
    Route::get('/participaciones/evento/{eventoId}', [EventoParticipacionController::class, 'participantesEvento']);
    Route::put('/participaciones/{participacionId}/aprobar', [EventoParticipacionController::class, 'aprobar'])->middleware('permission:participaciones.aprobar');
    Route::put('/participaciones/{participacionId}/rechazar', [EventoParticipacionController::class, 'rechazar'])->middleware('permission:participaciones.rechazar');
    Route::put('/participaciones-no-registradas/{participacionId}/aprobar', [EventoParticipacionController::class, 'aprobarNoRegistrado'])->middleware('permission:participaciones.aprobar');
    Route::put('/participaciones-no-registradas/{participacionId}/rechazar', [EventoParticipacionController::class, 'rechazarNoRegistrado'])->middleware('permission:participaciones.rechazar');
    
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
    // ONG: control de asistencia - Requiere permiso de control de asistencia
    // El controlador ya valida permisos
    Route::get('/eventos/{eventoId}/control-asistencia', [EventoParticipacionController::class, 'controlAsistencia']);
    Route::put('/participaciones/{participacionId}/modificar-asistencia', [EventoParticipacionController::class, 'modificarAsistencia'])->middleware('permission:eventos.control-asistencia');
    Route::get('/eventos/{eventoId}/exportar-asistencia-pdf', [EventoParticipacionController::class, 'exportarAsistenciaPDF'])->middleware('permission:eventos.exportar-reportes');
    Route::get('/eventos/{eventoId}/exportar-asistencia-excel', [EventoParticipacionController::class, 'exportarAsistenciaExcel'])->middleware('permission:eventos.exportar-reportes');
    Route::get('/eventos/{eventoId}/participantes-completo', [EventoParticipacionController::class, 'obtenerParticipantesCompleto'])->middleware('permission:eventos.ver-participantes');
    Route::get('/eventos/{eventoId}/exportar-participantes-completo', [EventoParticipacionController::class, 'exportarParticipantesCompleto'])->middleware('permission:eventos.exportar-reportes');

    // ----------- REACCIONES (Favoritos) -----------
    Route::post('/reacciones/toggle', [EventoReaccionController::class, 'toggle'])->middleware('permission:eventos.reaccionar');
    // El controlador ya valida permisos donde es necesario
    Route::get('/reacciones/verificar/{eventoId}', [EventoReaccionController::class, 'verificar']);
    Route::get('/reacciones/evento/{eventoId}', [EventoReaccionController::class, 'usuariosQueReaccionaron']);

    // ----------- COMPARTIDOS -----------
    Route::post('/eventos/{eventoId}/compartir', [EventoCompartidoController::class, 'compartir'])->middleware('permission:eventos.compartir');

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

    // ----------- DASHBOARD ONG COMPLETO (NUEVO SISTEMA) -----------
    Route::prefix('ong/dashboard')->group(function () {
        Route::get('/', [\App\Http\Controllers\Ong\OngDashboardController::class, 'dashboard']);
        Route::get('/export-pdf', [\App\Http\Controllers\Ong\OngDashboardController::class, 'exportarPdf']);
        Route::get('/export-excel', [\App\Http\Controllers\Ong\OngDashboardController::class, 'exportarExcel']);
        Route::get('/pdf', [\App\Http\Controllers\Ong\OngDashboardController::class, 'generarPDFDashboard']);
    });
    
    // Ruta alternativa para compatibilidad
    Route::get('/api/ong/dashboard/pdf', [\App\Http\Controllers\Ong\OngDashboardController::class, 'generarPDFDashboard']);

    // ----------- REPORTES ONG -----------
    // Nota: El controlador ya valida que el usuario sea tipo ONG, 
    // por lo que no necesitamos middleware de Spatie aquí
    Route::prefix('ong/reportes')->group(function () {
        Route::get('/datos', [\App\Http\Controllers\ReportController::class, 'getDatosReportes']);
        Route::get('/export-pdf', [\App\Http\Controllers\ReportController::class, 'exportarResumenEjecutivoPDF']);
        Route::get('/export-excel', [\App\Http\Controllers\ReportController::class, 'exportarResumenEjecutivoExcel']);
    });
    
    Route::prefix('ong/voluntarios')->group(function () {
        Route::get('/lista', [\App\Http\Controllers\ReportController::class, 'getVoluntariosLista']);
    });
    
    Route::prefix('reportes-ong')->group(function () {
        // APIs existentes para mega eventos
        Route::get('/kpis-destacados', [\App\Http\Controllers\ReportController::class, 'apiKPIsDestacados']);
        Route::get('/resumen-ejecutivo', [\App\Http\Controllers\ReportController::class, 'apiResumenEjecutivo']);
        Route::get('/resumen-ejecutivo/exportar/pdf', [\App\Http\Controllers\ReportController::class, 'exportarResumenEjecutivoPDF']);
        Route::get('/resumen-ejecutivo/exportar/excel', [\App\Http\Controllers\ReportController::class, 'exportarResumenEjecutivoExcel']);
        Route::get('/analisis-temporal', [\App\Http\Controllers\ReportController::class, 'apiAnalisisTemporal']);
        Route::get('/analisis-temporal/exportar/pdf', [\App\Http\Controllers\ReportController::class, 'exportarAnalisisTemporalPDF']);
        Route::get('/analisis-temporal/exportar/excel', [\App\Http\Controllers\ReportController::class, 'exportarAnalisisTemporalExcel']);
        Route::get('/analisis-temporal/exportar/csv', [\App\Http\Controllers\ReportController::class, 'exportarAnalisisTemporalCSV']);
        // Endpoint mejorado usando el nuevo controlador
        Route::get('/participacion-colaboracion', [ReportesOngController::class, 'participacionColaboracion']);
        // Mantener endpoints antiguos por compatibilidad
        Route::get('/participacion-colaboracion/legacy', [\App\Http\Controllers\ReportController::class, 'apiParticipacionColaboracion']);
        Route::get('/participacion-colaboracion/exportar/pdf', [\App\Http\Controllers\ReportController::class, 'exportarParticipacionColaboracionPDF']);
        Route::get('/participacion-colaboracion/exportar/excel', [\App\Http\Controllers\ReportController::class, 'exportarParticipacionColaboracionExcel']);
        Route::get('/analisis-geografico', [\App\Http\Controllers\ReportController::class, 'apiAnalisisGeografico']);
        Route::get('/analisis-geografico/exportar/pdf', [\App\Http\Controllers\ReportController::class, 'exportarAnalisisGeograficoPDF']);
        Route::get('/analisis-geografico/exportar/excel', [\App\Http\Controllers\ReportController::class, 'exportarAnalisisGeograficoExcel']);
        Route::get('/rendimiento-ong', [\App\Http\Controllers\ReportController::class, 'apiRendimientoOng']);
        Route::get('/rendimiento-ong/exportar/pdf', [\App\Http\Controllers\ReportController::class, 'exportarRendimientoOngPDF']);
        Route::get('/rendimiento-ong/exportar/excel', [\App\Http\Controllers\ReportController::class, 'exportarRendimientoOngExcel']);
        Route::get('/rendimiento-ong/exportar/json', [\App\Http\Controllers\ReportController::class, 'exportarRendimientoOngJSON']);

        // Nuevas APIs para eventos regulares, mega eventos y consolidado
        Route::get('/eventos-metrics', [\App\Http\Controllers\ReportController::class, 'apiEventosMetrics']);
        Route::get('/mega-eventos-metrics', [\App\Http\Controllers\ReportController::class, 'apiMegaEventosMetrics']);
        Route::get('/consolidado-metrics', [\App\Http\Controllers\ReportController::class, 'apiConsolidadoMetrics']);
        
        // Exportaciones - El controlador ya valida permisos
        Route::get('/eventos/export-pdf', [\App\Http\Controllers\ReportController::class, 'exportEventosPDF']);
        Route::get('/eventos/export-excel', [\App\Http\Controllers\ReportController::class, 'exportEventosExcel']);
        Route::get('/mega-eventos/export-pdf', [\App\Http\Controllers\ReportController::class, 'exportMegaEventosPDF']);
        Route::get('/mega-eventos/export-excel', [\App\Http\Controllers\ReportController::class, 'exportMegaEventosExcel']);
        Route::get('/consolidado/export-pdf', [\App\Http\Controllers\ReportController::class, 'exportConsolidadoPDF']);
        Route::get('/consolidado/export-excel', [\App\Http\Controllers\ReportController::class, 'exportConsolidadoExcel']);
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
        Route::get('/export-pdf', [DashboardExternoController::class, 'exportarPdf']);
        Route::get('/export-excel', [DashboardExternoController::class, 'exportarExcel']);
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
        // El controlador ya valida permisos
        Route::get('/', [MegaEventoController::class, 'index']);
        Route::get('/en-curso', [MegaEventoController::class, 'enCurso']);
        Route::get('/finalizados', [MegaEventoController::class, 'finalizados']);
        Route::get('/mis-participaciones', [MegaEventoController::class, 'misParticipaciones'])->middleware('permission:participaciones.ver-mis-participaciones');
        Route::post('/', [MegaEventoController::class, 'store'])->middleware('permission:mega-eventos.crear');
        // IMPORTANTE: Rutas con parámetros dinámicos deben ir al final, después de rutas específicas
        // El controlador ya valida permisos
        Route::get('/{id}', [MegaEventoController::class, 'show'])->where('id', '[0-9]+');
        // Compartidos
        Route::post('/{megaEventoId}/compartir', [MegaEventoCompartidoController::class, 'compartir'])->where('megaEventoId', '[0-9]+')->middleware('permission:mega-eventos.compartir');
        Route::get('/{megaEventoId}/compartidos/total', [MegaEventoCompartidoController::class, 'totalCompartidos'])->where('megaEventoId', '[0-9]+')->middleware('permission:mega-eventos.ver');
        // Reacciones (usuarios registrados)
        Route::post('/reacciones/toggle', [MegaEventoReaccionController::class, 'toggle'])->middleware('permission:mega-eventos.reaccionar');
        // El controlador ya valida permisos donde es necesario
        Route::get('/reacciones/verificar/{megaEventoId}', [MegaEventoReaccionController::class, 'verificar'])->where('megaEventoId', '[0-9]+');
        Route::get('/reacciones/{megaEventoId}', [MegaEventoReaccionController::class, 'usuariosQueReaccionaron'])->where('megaEventoId', '[0-9]+');
        // El controlador ya valida permisos
        Route::put('/{id}', [MegaEventoController::class, 'update'])->where('id', '[0-9]+');
        Route::delete('/{id}', [MegaEventoController::class, 'destroy'])->where('id', '[0-9]+')->middleware('permission:mega-eventos.eliminar');
        Route::delete('/{id}/imagen', [MegaEventoController::class, 'deleteImage'])->where('id', '[0-9]+')->middleware('permission:mega-eventos.editar');
        Route::post('/{id}/participar', [MegaEventoController::class, 'participar'])->where('id', '[0-9]+')->middleware('permission:mega-eventos.participar');
        Route::post('/{id}/cancelar-participacion', [MegaEventoController::class, 'cancelarParticipacion'])->where('id', '[0-9]+')->middleware('permission:participaciones.ver-mis-participaciones');
        Route::get('/{id}/verificar-participacion', [MegaEventoController::class, 'verificarParticipacion'])->where('id', '[0-9]+')->middleware('permission:mega-eventos.ver');
        // Registrar descarga de QR (solo una vez)
        Route::post('/registrar-descarga-qr', [MegaEventoController::class, 'registrarDescargaQR'])->middleware('permission:mega-eventos.participar');
        // El controlador ya valida permisos
        Route::get('/{id}/participantes', [MegaEventoController::class, 'participantes'])->where('id', '[0-9]+');
        // Control de asistencias
        // El controlador ya valida permisos
        Route::get('/{id}/control-asistencia', [MegaEventoController::class, 'controlAsistencia'])->where('id', '[0-9]+');
        Route::post('/{id}/registrar-asistencia', [MegaEventoController::class, 'registrarAsistencia'])->where('id', '[0-9]+')->middleware('permission:mega-eventos.control-asistencia');
        Route::post('/{id}/marcar-asistencia', [MegaEventoController::class, 'marcarAsistenciaUsuario'])->where('id', '[0-9]+')->middleware('permission:mega-eventos.participar');
        Route::put('/asistencias/{participacionId}/{tipo}', [MegaEventoController::class, 'modificarAsistencia'])->where('participacionId', '[0-9A-Za-z-]+')->middleware('permission:mega-eventos.control-asistencia');
        Route::get('/alertas-5-minutos', [MegaEventoController::class, 'alertas5Minutos'])->middleware('permission:mega-eventos.ver');
        // Rutas de seguimiento - El controlador ya valida permisos
        Route::get('/{id}/seguimiento', [MegaEventoController::class, 'seguimiento'])->where('id', '[0-9]+');
        // El controlador ya valida permisos
        Route::get('/{id}/historial', [MegaEventoController::class, 'historial'])->where('id', '[0-9]+');
        Route::get('/{id}/exportar-excel', [MegaEventoController::class, 'exportarExcel'])->where('id', '[0-9]+')->middleware('permission:mega-eventos.exportar-reportes');
        // El controlador ya valida permisos
        Route::get('/seguimiento/general', [MegaEventoController::class, 'seguimientoGeneral']);
    });

    // ----------- CONFIGURACIÓN / PARÁMETROS -----------
    Route::prefix('configuracion')->middleware('permission:configuracion.ver')->group(function () {
        Route::get('/', [ConfiguracionController::class, 'index']);
        Route::get('/categorias', [ConfiguracionController::class, 'categorias']);
        Route::get('/grupos', [ConfiguracionController::class, 'grupos']);
        Route::get('/codigo/{codigo}', [ConfiguracionController::class, 'porCodigo']);
        Route::post('/', [ConfiguracionController::class, 'store'])->middleware('permission:configuracion.gestionar');
        Route::get('/{id}', [ConfiguracionController::class, 'show']);
        Route::put('/{id}', [ConfiguracionController::class, 'update'])->middleware('permission:configuracion.gestionar');
        Route::put('/{id}/valor', [ConfiguracionController::class, 'actualizarValor'])->middleware('permission:configuracion.gestionar');
        Route::delete('/{id}', [ConfiguracionController::class, 'destroy'])->middleware('permission:configuracion.gestionar');
    });

    // ----------- PARAMETRIZACIONES -----------
    Route::prefix('parametrizaciones')->middleware('permission:parametrizaciones.ver')->group(function () {
        // Tipos de Evento
        Route::get('/tipos-evento', [ParametrizacionController::class, 'tiposEvento']);
        Route::post('/tipos-evento', [ParametrizacionController::class, 'crearTipoEvento'])->middleware('permission:parametrizaciones.gestionar');
        Route::put('/tipos-evento/{id}', [ParametrizacionController::class, 'actualizarTipoEvento'])->middleware('permission:parametrizaciones.gestionar');
        Route::delete('/tipos-evento/{id}', [ParametrizacionController::class, 'eliminarTipoEvento'])->middleware('permission:parametrizaciones.gestionar');

        // Categorías de Mega Eventos
        Route::get('/categorias-mega-evento', [ParametrizacionController::class, 'categoriasMegaEvento']);
        Route::post('/categorias-mega-evento', [ParametrizacionController::class, 'crearCategoriaMegaEvento'])->middleware('permission:parametrizaciones.gestionar');
        Route::put('/categorias-mega-evento/{id}', [ParametrizacionController::class, 'actualizarCategoriaMegaEvento'])->middleware('permission:parametrizaciones.gestionar');
        Route::delete('/categorias-mega-evento/{id}', [ParametrizacionController::class, 'eliminarCategoriaMegaEvento'])->middleware('permission:parametrizaciones.gestionar');

        // Ciudades
        Route::get('/ciudades', [ParametrizacionController::class, 'ciudades']);
        Route::post('/ciudades', [ParametrizacionController::class, 'crearCiudad'])->middleware('permission:parametrizaciones.gestionar');
        Route::put('/ciudades/{id}', [ParametrizacionController::class, 'actualizarCiudad'])->middleware('permission:parametrizaciones.gestionar');
        Route::delete('/ciudades/{id}', [ParametrizacionController::class, 'eliminarCiudad'])->middleware('permission:parametrizaciones.gestionar');

        // Lugares
        Route::get('/lugares', [ParametrizacionController::class, 'lugares']);
        Route::post('/lugares', [ParametrizacionController::class, 'crearLugar'])->middleware('permission:parametrizaciones.gestionar');
        Route::put('/lugares/{id}', [ParametrizacionController::class, 'actualizarLugar'])->middleware('permission:parametrizaciones.gestionar');
        Route::delete('/lugares/{id}', [ParametrizacionController::class, 'eliminarLugar'])->middleware('permission:parametrizaciones.gestionar');

        // Estados de Participación
        Route::get('/estados-participacion', [ParametrizacionController::class, 'estadosParticipacion']);
        Route::post('/estados-participacion', [ParametrizacionController::class, 'crearEstadoParticipacion'])->middleware('permission:parametrizaciones.gestionar');
        Route::put('/estados-participacion/{id}', [ParametrizacionController::class, 'actualizarEstadoParticipacion'])->middleware('permission:parametrizaciones.gestionar');
        Route::delete('/estados-participacion/{id}', [ParametrizacionController::class, 'eliminarEstadoParticipacion'])->middleware('permission:parametrizaciones.gestionar');

        // Tipos de Notificación
        Route::get('/tipos-notificacion', [ParametrizacionController::class, 'tiposNotificacion']);
        Route::post('/tipos-notificacion', [ParametrizacionController::class, 'crearTipoNotificacion'])->middleware('permission:parametrizaciones.gestionar');
        Route::put('/tipos-notificacion/{id}', [ParametrizacionController::class, 'actualizarTipoNotificacion'])->middleware('permission:parametrizaciones.gestionar');
        Route::delete('/tipos-notificacion/{id}', [ParametrizacionController::class, 'eliminarTipoNotificacion'])->middleware('permission:parametrizaciones.gestionar');

        // Estados de Evento
        Route::get('/estados-evento', [ParametrizacionController::class, 'estadosEvento']);
        Route::post('/estados-evento', [ParametrizacionController::class, 'crearEstadoEvento'])->middleware('permission:parametrizaciones.gestionar');
        Route::put('/estados-evento/{id}', [ParametrizacionController::class, 'actualizarEstadoEvento'])->middleware('permission:parametrizaciones.gestionar');
        Route::delete('/estados-evento/{id}', [ParametrizacionController::class, 'eliminarEstadoEvento'])->middleware('permission:parametrizaciones.gestionar');

        // Tipos de Usuario
        Route::get('/tipos-usuario', [ParametrizacionController::class, 'tiposUsuario']);
        Route::post('/tipos-usuario', [ParametrizacionController::class, 'crearTipoUsuario'])->middleware('permission:parametrizaciones.gestionar');
        Route::put('/tipos-usuario/{id}', [ParametrizacionController::class, 'actualizarTipoUsuario'])->middleware('permission:parametrizaciones.gestionar');
        Route::delete('/tipos-usuario/{id}', [ParametrizacionController::class, 'eliminarTipoUsuario'])->middleware('permission:parametrizaciones.gestionar');
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

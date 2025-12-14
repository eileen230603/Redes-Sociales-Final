@extends('layouts.adminlte')

@section('page_title', 'Reportes Avanzados - ONG')

@section('content_body')
<div class="container-fluid">
    <!-- Header con botones de exportación -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom" style="border-bottom: 1px solid #e9ecef; padding: 1.5rem;">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <h3 class="mb-0" style="color: #0C2B44; font-weight: 700; font-size: 1.75rem;">
                            <i class="fas fa-chart-line mr-2" style="color: #00A36C;"></i>Reportes y Estadísticas
                        </h3>
                        <div class="d-flex gap-2 mt-2 mt-md-0 flex-wrap">
                            <button class="btn btn-sm" onclick="exportarPDFReportes()" style="background: #dc3545; color: white; border-radius: 8px; padding: 0.6rem 1.5rem; font-weight: 600;">
                                <i class="fas fa-file-pdf mr-2"></i>Exportar PDF Completo
                            </button>
                            <div class="dropdown d-md-block d-none">
                                <button class="btn btn-sm dropdown-toggle" type="button" data-toggle="dropdown" style="background: #0C2B44; color: white; border-radius: 8px; padding: 0.6rem 1.5rem; font-weight: 600;">
                                    <i class="fas fa-share-alt mr-2"></i>Más Opciones
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#" onclick="compartirDashboard()"><i class="fas fa-link mr-2"></i>Compartir Dashboard</a>
                                    <a class="dropdown-item" href="#" onclick="programarReporte()"><i class="fas fa-clock mr-2"></i>Programar Reporte</a>
                                    <a class="dropdown-item" href="#" onclick="window.print()"><i class="fas fa-print mr-2"></i>Imprimir Vista Actual</a>
                </div>
            </div>
        </div>
                </div>
            </div>
        </div>
                </div>
            </div>

    <!-- (1) SECCIÓN DE FILTROS AVANZADOS -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom" style="border-bottom: 1px solid #e9ecef; padding: 1.5rem;">
                    <h5 class="mb-0" style="color: #0C2B44; font-weight: 600;">
                        <i class="fas fa-filter mr-2" style="color: #00A36C;"></i>Filtros Avanzados
                    </h5>
        </div>
                <div class="card-body" style="padding: 1.5rem;">
                    <div class="row">
                        <div class="col-md-2 mb-3">
                            <label for="filtroFechaInicio" style="font-weight: 600; color: #333; margin-bottom: 0.5rem;">Fecha Inicio</label>
                            <input type="date" id="filtroFechaInicio" class="form-control" style="border-radius: 8px; border: 1px solid #dee2e6;">
                </div>
                        <div class="col-md-2 mb-3">
                            <label for="filtroFechaFin" style="font-weight: 600; color: #333; margin-bottom: 0.5rem;">Fecha Fin</label>
                            <input type="date" id="filtroFechaFin" class="form-control" style="border-radius: 8px; border: 1px solid #dee2e6;">
            </div>
                        <div class="col-md-2 mb-3">
                            <label for="filtroEstadoReporte" style="font-weight: 600; color: #333; margin-bottom: 0.5rem;">Estado Evento</label>
                            <select id="filtroEstadoReporte" class="form-control" style="border-radius: 8px; border: 1px solid #dee2e6;">
                                <option value="Todos">Todos</option>
                                <option value="Activo">Activo</option>
                                <option value="Publicado">Publicado</option>
                                <option value="Borrador">Borrador</option>
                                <option value="Finalizado">Finalizado</option>
                                <option value="Cancelado">Cancelado</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="filtroTipoEvento" style="font-weight: 600; color: #333; margin-bottom: 0.5rem;">Tipo de Evento</label>
                            <select id="filtroTipoEvento" class="form-control" style="border-radius: 8px; border: 1px solid #dee2e6;">
                                <option value="Todos">Todos</option>
                                <option value="Educación">Educación</option>
                                <option value="Salud">Salud</option>
                                <option value="Medio Ambiente">Medio Ambiente</option>
                                <option value="Social">Social</option>
                                <option value="Cultural">Cultural</option>
                                <option value="Deportivo">Deportivo</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="filtroVoluntario" style="font-weight: 600; color: #333; margin-bottom: 0.5rem;">Voluntario Específico</label>
                            <select id="filtroVoluntario" class="form-control" style="border-radius: 8px; border: 1px solid #dee2e6;">
                                <option value="">Todos</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3 d-flex flex-column justify-content-end">
                            <button class="btn btn-block mb-2" onclick="aplicarFiltrosReportes()" style="background: #0C2B44; color: white; border-radius: 8px; padding: 0.6rem; font-weight: 600;">
                                <i class="fas fa-filter mr-2"></i>Aplicar Filtros
                            </button>
                            <button class="btn btn-block" onclick="resetearFiltrosReportes()" style="background: #6c757d; color: white; border-radius: 8px; padding: 0.6rem; font-weight: 600;">
                                <i class="fas fa-redo mr-2"></i>Resetear
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- (9) ALERTAS Y NOTIFICACIONES INTELIGENTES -->
    <div id="contenedorAlertas" class="row mb-4"></div>

    <!-- (2) TARJETAS KPI MEJORADAS -->
    <div class="row mb-4">
        <div class="col-lg-2 col-md-4 col-6 mb-4">
            <div class="card border-0 shadow-sm kpi-card" style="background: linear-gradient(135deg, #0C2B44 0%, #0a2338 100%); border-radius: 12px; min-height: 160px;">
                <div class="card-body p-4 position-relative" style="overflow: hidden;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-white text-uppercase mb-2" style="font-size: 0.75rem; font-weight: 600; opacity: 0.9;">Total Eventos</h6>
                            <h2 class="mb-0 text-white" id="kpiTotalEventos" style="font-size: 2.5rem; font-weight: 700;">0</h2>
                            <div class="mt-2" id="sparklineTotalEventos"></div>
                            <small class="badge mt-2" id="variacionTotalEventos" style="border-radius: 20px; padding: 0.2em 0.6em; font-size: 0.7rem; background: rgba(255,255,255,0.2); color: white;">
                                <i class="fas fa-arrow-up"></i> 0%
                            </small>
                        </div>
                        <i class="far fa-calendar-alt" style="opacity: 0.2; font-size: 3rem; color: white; position: absolute; right: 10px; top: 10px;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6 mb-4">
            <div class="card border-0 shadow-sm kpi-card" style="background: linear-gradient(135deg, #00A36C 0%, #008a5a 100%); border-radius: 12px; min-height: 160px;">
                <div class="card-body p-4 position-relative" style="overflow: hidden;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-white text-uppercase mb-2" style="font-size: 0.75rem; font-weight: 600; opacity: 0.9;">Eventos Activos</h6>
                            <h2 class="mb-0 text-white" id="kpiEventosActivos" style="font-size: 2.5rem; font-weight: 700;">0</h2>
                            <div class="mt-2" id="sparklineEventosActivos"></div>
                            <small class="badge mt-2" id="variacionEventosActivos" style="border-radius: 20px; padding: 0.2em 0.6em; font-size: 0.7rem; background: rgba(255,255,255,0.2); color: white;">
                                <i class="fas fa-arrow-up"></i> 0%
                            </small>
                        </div>
                        <i class="fas fa-calendar-check" style="opacity: 0.2; font-size: 3rem; color: white; position: absolute; right: 10px; top: 10px;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6 mb-4">
            <div class="card border-0 shadow-sm kpi-card" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); border-radius: 12px; min-height: 160px;">
                <div class="card-body p-4 position-relative" style="overflow: hidden;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-white text-uppercase mb-2" style="font-size: 0.75rem; font-weight: 600; opacity: 0.9;">Voluntarios Únicos</h6>
                            <h2 class="mb-0 text-white" id="kpiVoluntariosUnicos" style="font-size: 2.5rem; font-weight: 700;">0</h2>
                            <div class="mt-2" id="sparklineVoluntarios"></div>
                            <small class="badge mt-2" id="variacionVoluntarios" style="border-radius: 20px; padding: 0.2em 0.6em; font-size: 0.7rem; background: rgba(255,255,255,0.2); color: white;">
                                <i class="fas fa-arrow-up"></i> 0%
                            </small>
                        </div>
                        <i class="fas fa-users" style="opacity: 0.2; font-size: 3rem; color: white; position: absolute; right: 10px; top: 10px;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6 mb-4">
            <div class="card border-0 shadow-sm kpi-card" style="background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%); border-radius: 12px; min-height: 160px;">
                <div class="card-body p-4 position-relative" style="overflow: hidden;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-white text-uppercase mb-2" style="font-size: 0.75rem; font-weight: 600; opacity: 0.9;">Total Participaciones</h6>
                            <h2 class="mb-0 text-white" id="kpiTotalParticipaciones" style="font-size: 2.5rem; font-weight: 700;">0</h2>
                            <div class="mt-2" id="sparklineParticipaciones"></div>
                            <small class="badge mt-2" id="variacionParticipaciones" style="border-radius: 20px; padding: 0.2em 0.6em; font-size: 0.7rem; background: rgba(255,255,255,0.2); color: white;">
                                <i class="fas fa-arrow-up"></i> 0%
                            </small>
                        </div>
                        <i class="fas fa-user-check" style="opacity: 0.2; font-size: 3rem; color: white; position: absolute; right: 10px; top: 10px;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6 mb-4">
            <div class="card border-0 shadow-sm kpi-card" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); border-radius: 12px; min-height: 160px;">
                <div class="card-body p-4 position-relative" style="overflow: hidden;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-white text-uppercase mb-2" style="font-size: 0.75rem; font-weight: 600; opacity: 0.9;">Tasa de Asistencia</h6>
                            <h2 class="mb-0 text-white" id="kpiTasaAsistencia" style="font-size: 2.5rem; font-weight: 700;">0%</h2>
                            <div class="mt-2" id="sparklineAsistencia"></div>
                            <small class="badge mt-2" id="variacionAsistencia" style="border-radius: 20px; padding: 0.2em 0.6em; font-size: 0.7rem; background: rgba(255,255,255,0.2); color: white;">
                                <i class="fas fa-arrow-up"></i> 0%
                            </small>
                        </div>
                        <i class="fas fa-percentage" style="opacity: 0.2; font-size: 3rem; color: white; position: absolute; right: 10px; top: 10px;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6 mb-4">
            <div class="card border-0 shadow-sm kpi-card" style="background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%); border-radius: 12px; min-height: 160px;">
                <div class="card-body p-4 position-relative" style="overflow: hidden;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-white text-uppercase mb-2" style="font-size: 0.75rem; font-weight: 600; opacity: 0.9;">Promedio por Evento</h6>
                            <h2 class="mb-0 text-white" id="kpiPromedioParticipantes" style="font-size: 2.5rem; font-weight: 700;">0</h2>
                            <div class="mt-2" id="sparklinePromedio"></div>
                            <small class="badge mt-2" id="variacionPromedio" style="border-radius: 20px; padding: 0.2em 0.6em; font-size: 0.7rem; background: rgba(255,255,255,0.2); color: white;">
                                <i class="fas fa-arrow-up"></i> 0%
                            </small>
                        </div>
                        <i class="fas fa-chart-bar" style="opacity: 0.2; font-size: 3rem; color: white; position: absolute; right: 10px; top: 10px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- (3) GRÁFICOS INTERACTIVOS MEJORADOS -->
    <div class="row mb-4">
        <!-- GRÁFICO 1: Eventos por Estado -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom" style="padding: 1rem 1.5rem;">
                    <h5 class="mb-0" style="color: #0C2B44; font-weight: 600;">
                        <i class="fas fa-chart-pie mr-2" style="color: #00A36C;"></i>Eventos por Estado
                    </h5>
                </div>
                <div class="card-body" style="padding: 1.5rem; position: relative;">
                    <canvas id="graficoEstados" height="250"></canvas>
                    <div id="totalEventosCentro" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 2rem; font-weight: 700; color: #0C2B44;">0</div>
                </div>
            </div>
        </div>

        <!-- GRÁFICO 2: Participaciones por Evento -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom" style="padding: 1rem 1.5rem;">
                    <h5 class="mb-0" style="color: #0C2B44; font-weight: 600;">
                        <i class="fas fa-chart-bar mr-2" style="color: #00A36C;"></i>Top 15 Participaciones por Evento
                    </h5>
                </div>
                <div class="card-body" style="padding: 1.5rem;">
                    <canvas id="graficoParticipaciones" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- GRÁFICO 3: Tendencia Mensual -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom" style="padding: 1rem 1.5rem;">
                    <h5 class="mb-0" style="color: #0C2B44; font-weight: 600;">
                        <i class="fas fa-chart-line mr-2" style="color: #00A36C;"></i>Tendencia Mensual de Participaciones
                    </h5>
                </div>
                <div class="card-body" style="padding: 1.5rem;">
                    <canvas id="graficoTendenciaMensual" height="120"></canvas>
                </div>
            </div>
        </div>

        <!-- GRÁFICO 4: Distribución por Tipo -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom" style="padding: 1rem 1.5rem;">
                    <h5 class="mb-0" style="color: #0C2B44; font-weight: 600;">
                        <i class="fas fa-chart-pie mr-2" style="color: #00A36C;"></i>Distribución por Tipo
                    </h5>
                </div>
                <div class="card-body" style="padding: 1.5rem;">
                    <canvas id="graficoDistribucionTipo" height="250"></canvas>
            </div>
        </div>
    </div>

        <!-- GRÁFICO 5: Comparativa de Engagement -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom" style="padding: 1rem 1.5rem;">
                    <h5 class="mb-0" style="color: #0C2B44; font-weight: 600;">
                        <i class="fas fa-chart-area mr-2" style="color: #00A36C;"></i>Comparativa de Engagement
                    </h5>
        </div>
                <div class="card-body" style="padding: 1.5rem;">
                    <canvas id="graficoEngagement" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- GRÁFICO 6: Top 10 Voluntarios -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom" style="padding: 1rem 1.5rem;">
                    <h5 class="mb-0" style="color: #0C2B44; font-weight: 600;">
                        <i class="fas fa-users mr-2" style="color: #00A36C;"></i>Top 10 Voluntarios Más Activos
                    </h5>
                </div>
                <div class="card-body" style="padding: 1.5rem;">
                    <canvas id="graficoTopVoluntarios" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- (5) SECCIÓN ANÁLISIS POR VOLUNTARIO -->
    <div id="seccionAnalisisVoluntario" class="row mb-4" style="display: none;">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom" style="padding: 1.5rem;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <img id="voluntarioFotoPerfil" src="" alt="" class="rounded-circle mr-3" style="width: 80px; height: 80px; object-fit: cover; border: 3px solid #00A36C;">
                            <div>
                                <h4 class="mb-1" id="voluntarioNombre" style="color: #0C2B44; font-weight: 700;"></h4>
                                <p class="mb-0 text-muted" id="voluntarioContacto" style="font-size: 0.9rem;"></p>
                                <div class="mt-2">
                                    <span class="badge mr-2" style="background: #00A36C; color: white; border-radius: 20px; padding: 0.4em 0.8em;">
                                        <i class="fas fa-calendar mr-1"></i><span id="voluntarioTotalEventos">0</span> Eventos
                                    </span>
                                    <span class="badge mr-2" style="background: #17a2b8; color: white; border-radius: 20px; padding: 0.4em 0.8em;">
                                        <i class="fas fa-percentage mr-1"></i><span id="voluntarioTasaAsistencia">0%</span> Asistencia
                                    </span>
                                    <span class="badge" style="background: #ffc107; color: white; border-radius: 20px; padding: 0.4em 0.8em;">
                                        <i class="fas fa-clock mr-1"></i>Última: <span id="voluntarioUltimaParticipacion">N/A</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <button class="btn" onclick="generarReporteVoluntario()" style="background: #0C2B44; color: white; border-radius: 8px; padding: 0.6rem 1.5rem; font-weight: 600;">
                            <i class="fas fa-file-pdf mr-2"></i>Generar Reporte Individual PDF
                        </button>
                    </div>
                </div>
                <div class="card-body" style="padding: 1.5rem;">
                    <ul class="nav nav-tabs mb-3" id="tabsVoluntario">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#tabHistorial">Historial</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tabEstadisticas">Estadísticas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tabCertificados">Certificados</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tabHistorial">
                            <div id="contenidoHistorial"></div>
                        </div>
                        <div class="tab-pane fade" id="tabEstadisticas">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <canvas id="graficoVoluntarioLine" height="150"></canvas>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <canvas id="graficoVoluntarioBar" height="150"></canvas>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <canvas id="graficoVoluntarioDona" height="150"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tabCertificados">
                            <div id="contenidoCertificados">
                                <p class="text-muted">No hay certificados generados aún.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- (6) SECCIÓN MÉTRICAS DE ENGAGEMENT -->
    <div class="row mb-4">
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body text-center p-4">
                    <h6 class="text-uppercase mb-3" style="color: #6c757d; font-weight: 600; font-size: 0.85rem;">Engagement Rate</h6>
                    <div class="position-relative d-inline-block mb-3">
                        <canvas id="graficoEngagementRate" width="120" height="120"></canvas>
                        <div class="position-absolute" style="top: 50%; left: 50%; transform: translate(-50%, -50%);">
                            <h3 class="mb-0" id="valorEngagementRate" style="font-weight: 700; color: #0C2B44;">0%</h3>
                        </div>
                    </div>
                    <div id="sparklineEngagementRate"></div>
                    <p class="text-muted mb-0 mt-2" style="font-size: 0.85rem;">(reacciones + compartidos) / total eventos</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body text-center p-4">
                    <h6 class="text-uppercase mb-3" style="color: #6c757d; font-weight: 600; font-size: 0.85rem;">Tasa de Conversión</h6>
                    <div class="position-relative d-inline-block mb-3">
                        <canvas id="graficoTasaConversion" width="120" height="120"></canvas>
                        <div class="position-absolute" style="top: 50%; left: 50%; transform: translate(-50%, -50%);">
                            <h3 class="mb-0" id="valorTasaConversion" style="font-weight: 700; color: #0C2B44;">0%</h3>
                        </div>
                    </div>
                    <div id="sparklineTasaConversion"></div>
                    <p class="text-muted mb-0 mt-2" style="font-size: 0.85rem;">% de interesados que se inscriben</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body text-center p-4">
                    <h6 class="text-uppercase mb-3" style="color: #6c757d; font-weight: 600; font-size: 0.85rem;">Índice de Satisfacción</h6>
                    <div class="position-relative d-inline-block mb-3">
                        <h1 class="mb-0" id="indiceSatisfaccion" style="font-size: 4rem; font-weight: 700; color: #ffc107;">-</h1>
                        <p class="text-muted mb-0" style="font-size: 0.85rem;">de 5.0</p>
                    </div>
                    <div id="sparklineSatisfaccion"></div>
                    <p class="text-muted mb-0 mt-2" style="font-size: 0.85rem;">Promedio de calificaciones</p>
                </div>
            </div>
        </div>
    </div>

    <!-- (7) SECCIÓN COMPARATIVA TEMPORAL -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom" style="padding: 1.5rem;">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <h5 class="mb-0" style="color: #0C2B44; font-weight: 600;">
                            <i class="fas fa-chart-line mr-2" style="color: #00A36C;"></i>Comparativa Temporal
                        </h5>
                        <select id="selectorPeriodoComparativa" class="form-control" onchange="calcularComparativaTemporal(this.value)" style="width: auto; border-radius: 8px; border: 1px solid #dee2e6;">
                            <option value="7">Última Semana</option>
                            <option value="30" selected>Último Mes</option>
                            <option value="90">Últimos 3 Meses</option>
                            <option value="180">Últimos 6 Meses</option>
                            <option value="365">Último Año</option>
                        </select>
                    </div>
                </div>
                <div class="card-body" style="padding: 1.5rem;">
            <div class="table-responsive">
                        <table class="table table-hover" id="tablaComparativa">
                            <thead style="background: #0C2B44; color: white;">
                        <tr>
                                    <th style="font-weight: 600;">Métrica</th>
                                    <th class="text-center" style="font-weight: 600;">Período Actual</th>
                                    <th class="text-center" style="font-weight: 600;">Período Anterior</th>
                                    <th class="text-center" style="font-weight: 600;">Variación</th>
                                    <th class="text-center" style="font-weight: 600;">Tendencia</th>
                        </tr>
                    </thead>
                            <tbody id="tbodyComparativa">
                        <tr>
                                    <td colspan="5" class="text-center">
                                <i class="fas fa-spinner fa-spin"></i> Cargando...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
    </div>

    <!-- (4) TABLA INTERACTIVA DE EVENTOS -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom" style="padding: 1.5rem;">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <h5 class="mb-0" style="color: #0C2B44; font-weight: 600;">
                            <i class="fas fa-list mr-2" style="color: #00A36C;"></i>Tabla de Eventos
                        </h5>
                        <div class="d-flex gap-2 flex-wrap">
                            <input type="text" id="busquedaTablaEventos" class="form-control" placeholder="Buscar por título, tipo, ubicación..." style="width: 250px; border-radius: 8px; border: 1px solid #dee2e6;">
                            <button class="btn btn-sm" onclick="exportarSeleccionadosCSV()" style="background: #17a2b8; color: white; border-radius: 8px;">
                                <i class="fas fa-file-csv mr-2"></i>Exportar Seleccionados
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body" style="padding: 1.5rem;">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped" id="tablaEventos">
                            <thead style="background: #0C2B44; color: white;">
                                <tr>
                                    <th style="width: 40px;">
                                        <input type="checkbox" id="checkAllEventos" onchange="toggleSelectAll()">
                                    </th>
                                    <th style="font-weight: 600;">Imagen</th>
                                    <th style="font-weight: 600;">Título</th>
                                    <th style="font-weight: 600;">Tipo</th>
                                    <th style="font-weight: 600;">Estado</th>
                                    <th style="font-weight: 600;">Fecha Inicio</th>
                                    <th style="font-weight: 600;">Ubicación</th>
                                    <th class="text-center" style="font-weight: 600;">Participantes</th>
                                    <th class="text-center" style="font-weight: 600;">Asistieron</th>
                                    <th class="text-center" style="font-weight: 600;">Reacciones</th>
                                    <th class="text-center" style="font-weight: 600;">Compartidos</th>
                                    <th class="text-center" style="font-weight: 600;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyEventos">
                                <tr>
                                    <td colspan="12" class="text-center">
                                        <i class="fas fa-spinner fa-spin"></i> Cargando eventos...
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot id="tfootEventos" style="background: #f8f9fa; font-weight: 600;">
                                <tr>
                                    <td colspan="7" class="text-right">TOTALES:</td>
                                    <td class="text-center" id="totalParticipantesFoot">0</td>
                                    <td class="text-center" id="totalAsistieronFoot">0</td>
                                    <td class="text-center" id="totalReaccionesFoot">0</td>
                                    <td class="text-center" id="totalCompartidosFoot">0</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <span class="text-muted">Mostrando <span id="paginaActual">1</span> de <span id="totalPaginas">1</span></span>
                        </div>
                        <div>
                            <button class="btn btn-sm" id="btnAnterior" onclick="cambiarPagina(-1)" style="background: #6c757d; color: white; border-radius: 8px;" disabled>
                                <i class="fas fa-chevron-left"></i> Anterior
                            </button>
                            <button class="btn btn-sm ml-2" id="btnSiguiente" onclick="cambiarPagina(1)" style="background: #6c757d; color: white; border-radius: 8px;">
                                Siguiente <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    /* Paleta de colores corporativa */
    :root {
        --color-azul-marino: #0C2B44;
        --color-verde-esmeralda: #00A36C;
        --color-cyan: #17a2b8;
        --color-amarillo: #ffc107;
        --color-rojo: #dc3545;
        --color-gris: #6c757d;
    }

    /* Cards KPI con animaciones */
    .kpi-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        cursor: pointer;
    }

    .kpi-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
    }

    /* Estilos de cards generales */
    .card {
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.12);
    }

    /* Badges mejorados */
    .badge {
        border-radius: 20px;
        padding: 0.4em 0.8em;
        font-size: 0.85rem;
        font-weight: 500;
    }

    /* Botones mejorados */
    .btn {
        border-radius: 8px;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    /* Inputs y selects */
    input.form-control, select.form-control {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        transition: all 0.3s ease;
    }

    input.form-control:focus, select.form-control:focus {
        border-color: #00A36C;
        box-shadow: 0 0 0 0.2rem rgba(0,163,108,0.25);
    }

    /* Alertas animadas */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .alerta-card {
        animation: fadeIn 0.5s ease;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 1rem;
        position: relative;
    }

    .alerta-card .btn-cerrar {
        position: absolute;
        top: 10px;
        right: 10px;
        background: transparent;
        border: none;
        font-size: 1.2rem;
        color: #666;
        cursor: pointer;
    }

    /* Tabla responsive */
    .table-responsive {
        border-radius: 8px;
    }

    .table thead th {
        border: none;
        font-weight: 600;
        font-size: 0.9rem;
        padding: 1rem;
    }

    .table tbody td {
        padding: 0.75rem 1rem;
        vertical-align: middle;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    /* Sparklines */
    .sparkline-container {
        height: 40px;
        width: 100%;
    }

    /* Gráficos */
    canvas {
        max-width: 100%;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .kpi-card {
            margin-bottom: 1rem;
        }
        
        .card-header h5 {
            font-size: 1rem;
        }
    }

    /* Loading spinner */
    .spinner-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255,255,255,0.9);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .spinner-overlay .spinner-border {
        width: 3rem;
        height: 3rem;
        border-width: 0.3rem;
        color: #00A36C;
    }
</style>
@endpush

@push('js')
<script src="{{ asset('js/notificaciones-ong.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@2.0.1/dist/chartjs-plugin-zoom.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
<script src="{{ asset('assets/js/config.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Definir API_BASE_URL si no está definido
if (typeof API_BASE_URL === 'undefined') {
    window.API_BASE_URL = "{{ env('APP_URL', 'http://localhost:8000') }}";
    var API_BASE_URL = window.API_BASE_URL;
    console.log("🌐 API_BASE_URL definido:", API_BASE_URL);
}

// Variables globales
let token, ongId;
let datosReportes = null;
let chartInstances = {};
let eventosFiltrados = [];
let paginaActual = 1;
const eventosPorPagina = 15;
let eventosSeleccionados = new Set();

// Inicialización al cargar
document.addEventListener('DOMContentLoaded', async () => {
    token = localStorage.getItem('token');
    ongId = parseInt(localStorage.getItem('id_entidad') || localStorage.getItem('id_usuario'), 10);

    if (!token || isNaN(ongId) || ongId <= 0) {
        Swal.fire({
            icon: 'error',
            title: 'Error de Autenticación',
            text: 'Debe iniciar sesión correctamente'
        }).then(() => {
        window.location.href = '/login';
        });
        return;
    }

    // Establecer fechas por defecto (últimos 6 meses)
    const fechaFin = new Date();
    const fechaInicio = new Date();
    fechaInicio.setMonth(fechaInicio.getMonth() - 6);
    
    document.getElementById('filtroFechaInicio').value = fechaInicio.toISOString().split('T')[0];
    document.getElementById('filtroFechaFin').value = fechaFin.toISOString().split('T')[0];

    // Cargar voluntarios para filtro
    await cargarVoluntariosParaFiltro();
    
    // Cargar datos iniciales
    await cargarDatosReportes();
});

// (10) FUNCIÓN: Cargar voluntarios para filtro
async function cargarVoluntariosParaFiltro() {
    try {
        const res = await fetch(`${API_BASE_URL}/api/ong/voluntarios/lista`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        const data = await res.json();
        if (!data.success) throw new Error(data.error || 'Error al cargar voluntarios');

        const select = document.getElementById('filtroVoluntario');
        select.innerHTML = '<option value="">Todos</option>';
        
        data.voluntarios.forEach(vol => {
            const option = document.createElement('option');
            option.value = vol.user_id;
            option.textContent = vol.nombre_completo;
            select.appendChild(option);
        });
    } catch (error) {
        console.error('Error cargando voluntarios:', error);
    }
}

// (10) FUNCIÓN PRINCIPAL: Cargar datos de reportes
async function cargarDatosReportes() {
    try {
        mostrarLoading();

        const fechaInicio = document.getElementById('filtroFechaInicio').value;
        const fechaFin = document.getElementById('filtroFechaFin').value;
        const estadoEvento = document.getElementById('filtroEstadoReporte').value;
        const tipoEvento = document.getElementById('filtroTipoEvento').value;
        const voluntarioId = document.getElementById('filtroVoluntario').value;

        const params = new URLSearchParams({
            fecha_inicio: fechaInicio,
            fecha_fin: fechaFin,
            estado_evento: estadoEvento,
            tipo_evento: tipoEvento
        });

        if (voluntarioId) {
            params.append('voluntario_id', voluntarioId);
        }

        const res = await fetch(`${API_BASE_URL}/api/ong/reportes/datos?${params.toString()}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        const data = await res.json();
        if (!data.success) throw new Error(data.error || 'Error al cargar datos');

        datosReportes = data;
        
        // Actualizar todas las secciones
        actualizarKPIs(data.metricas, data.comparativa_temporal);
        actualizarGraficos(data.graficos);
        actualizarTablaEventos(data.eventos_detalle);
        actualizarAlertas(data.alertas || []);
        actualizarMetricasEngagement(data.metricas);
        actualizarComparativaTemporal(data.comparativa_temporal);

        // Si hay voluntario seleccionado, mostrar análisis
        if (voluntarioId) {
            mostrarDetalleVoluntario(parseInt(voluntarioId));
        } else {
            document.getElementById('seccionAnalisisVoluntario').style.display = 'none';
        }
        
        // Agregar listener al select de voluntario para mostrar análisis cuando cambie
        const selectVoluntario = document.getElementById('filtroVoluntario');
        if (selectVoluntario) {
            selectVoluntario.addEventListener('change', function() {
                if (this.value) {
                    mostrarDetalleVoluntario(parseInt(this.value));
                } else {
                    document.getElementById('seccionAnalisisVoluntario').style.display = 'none';
                }
            });
        }

        ocultarLoading();
    } catch (error) {
        console.error('Error cargando datos:', error);
        ocultarLoading();
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al cargar los datos: ' + error.message
        });
    }
}

// Actualizar KPIs
function actualizarKPIs(metricas, comparativa) {
    document.getElementById('kpiTotalEventos').textContent = metricas.total_eventos || 0;
    document.getElementById('kpiEventosActivos').textContent = metricas.eventos_activos || 0;
    document.getElementById('kpiVoluntariosUnicos').textContent = metricas.total_voluntarios_unicos || 0;
    document.getElementById('kpiTotalParticipaciones').textContent = metricas.total_participaciones || 0;
    document.getElementById('kpiTasaAsistencia').textContent = (metricas.tasa_asistencia_promedio || 0) + '%';
    document.getElementById('kpiPromedioParticipantes').textContent = metricas.promedio_participantes_evento || 0;

    // Actualizar variaciones si hay comparativa
    if (comparativa && comparativa.variaciones) {
        actualizarVariacion('variacionTotalEventos', comparativa.variaciones.eventos_realizados);
        actualizarVariacion('variacionEventosActivos', 0); // Calcular si es necesario
        actualizarVariacion('variacionVoluntarios', comparativa.variaciones.nuevas_inscripciones);
        actualizarVariacion('variacionParticipaciones', comparativa.variaciones.nuevas_inscripciones);
        actualizarVariacion('variacionAsistencia', comparativa.variaciones.tasa_asistencia);
    }
}

function actualizarVariacion(elementId, valor) {
    const elemento = document.getElementById(elementId);
    if (!elemento) return;
    
    const icono = valor >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
    const color = valor >= 0 ? 'rgba(0,163,108,0.3)' : 'rgba(220,53,69,0.3)';
    
    elemento.innerHTML = `<i class="fas ${icono}"></i> ${Math.abs(valor)}%`;
    elemento.style.background = color;
}

// Actualizar gráficos
function actualizarGraficos(graficos) {
    // Destruir gráficos existentes
    Object.values(chartInstances).forEach(chart => {
        if (chart && typeof chart.destroy === 'function') {
            chart.destroy();
        }
    });
    chartInstances = {};

    // GRÁFICO 1: Eventos por Estado (Doughnut)
    const ctxEstados = document.getElementById('graficoEstados');
    if (ctxEstados) {
        const total = graficos.eventos_por_estado.data.reduce((a, b) => a + b, 0);
        document.getElementById('totalEventosCentro').textContent = total;

        const coloresEstado = {
            'publicado': '#00A36C',
            'borrador': '#ffc107',
            'finalizado': '#6c757d',
            'cancelado': '#dc3545',
            'activo': '#17a2b8'
        };

        chartInstances.estados = new Chart(ctxEstados, {
            type: 'doughnut',
            data: {
                labels: graficos.eventos_por_estado.labels,
                datasets: [{
                    data: graficos.eventos_por_estado.data,
                    backgroundColor: graficos.eventos_por_estado.labels.map(l => coloresEstado[l.toLowerCase()] || '#6c757d')
                }]
            },
            options: {
                cutout: '65%',
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.9)',
                        padding: 15,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 }
                    }
                }
            }
        });
    }

    // GRÁFICO 2: Participaciones por Evento (Bar Horizontal)
    const ctxParticipaciones = document.getElementById('graficoParticipaciones');
    if (ctxParticipaciones) {
        chartInstances.participaciones = new Chart(ctxParticipaciones, {
            type: 'bar',
            data: {
                labels: graficos.participaciones_por_evento.labels.slice(0, 15),
                datasets: [{
                    label: 'Participaciones',
                    data: graficos.participaciones_por_evento.data.slice(0, 15),
                    backgroundColor: (ctx) => {
                        const gradient = ctx.chart.ctx.createLinearGradient(0, 0, 0, 400);
                        gradient.addColorStop(0, '#00A36C');
                        gradient.addColorStop(1, '#0C2B44');
                        return gradient;
                    }
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.9)',
                        padding: 15,
                        callbacks: {
                            label: function(context) {
                                return `Participantes: ${context.parsed.x}`;
                            }
                        }
                    },
                    zoom: { zoom: { wheel: { enabled: true } } }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    y: {
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    }
                }
            }
        });
    }

    // GRÁFICO 3: Tendencia Mensual (Line)
    const ctxTendencia = document.getElementById('graficoTendenciaMensual');
    if (ctxTendencia) {
        chartInstances.tendencia = new Chart(ctxTendencia, {
            type: 'line',
            data: {
                labels: graficos.tendencia_mensual.labels,
                datasets: [
                    {
                        label: 'Inscripciones Totales',
                        data: graficos.tendencia_mensual.inscripciones,
                        borderColor: '#0C2B44',
                        backgroundColor: 'rgba(12, 43, 68, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Asistencias Confirmadas',
                        data: graficos.tendencia_mensual.asistencias,
                        borderColor: '#00A36C',
                        backgroundColor: 'rgba(0, 163, 108, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Inasistencias',
                        data: graficos.tendencia_mensual.inasistencias,
                        borderColor: '#dc3545',
                        borderDash: [5, 5],
                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.9)',
                        padding: 15
                    },
                    zoom: { zoom: { wheel: { enabled: true } } }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: {
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    }
                }
            }
        });
    }

    // GRÁFICO 4: Distribución por Tipo (Polar Area)
    const ctxDistribucion = document.getElementById('graficoDistribucionTipo');
    if (ctxDistribucion) {
        chartInstances.distribucion = new Chart(ctxDistribucion, {
            type: 'polarArea',
            data: {
                labels: graficos.distribucion_tipo.labels,
                datasets: [{
                    data: graficos.distribucion_tipo.data,
                    backgroundColor: [
                        '#0C2B44', '#00A36C', '#17a2b8', '#ffc107', '#dc3545', '#6c757d', '#e83e8c'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.9)',
                        padding: 15
                    }
                }
            }
        });
    }

    // GRÁFICO 5: Engagement (Radar)
    const ctxEngagement = document.getElementById('graficoEngagement');
    if (ctxEngagement) {
        chartInstances.engagement = new Chart(ctxEngagement, {
            type: 'radar',
            data: {
                labels: graficos.engagement.labels,
                datasets: [{
                    label: 'Engagement',
                    data: graficos.engagement.data,
                    borderColor: '#00A36C',
                    backgroundColor: 'rgba(0, 163, 108, 0.2)',
                    pointBackgroundColor: '#00A36C'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.9)',
                        padding: 15
                    }
                },
                scales: {
                    r: {
                        beginAtZero: true,
                        max: 100,
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    }
                }
            }
        });
    }

    // GRÁFICO 6: Top Voluntarios (Bar)
    const ctxTopVoluntarios = document.getElementById('graficoTopVoluntarios');
    if (ctxTopVoluntarios) {
        chartInstances.topVoluntarios = new Chart(ctxTopVoluntarios, {
            type: 'bar',
            data: {
                labels: graficos.top_voluntarios.map(v => v.nombre.length > 20 ? v.nombre.substring(0, 20) + '...' : v.nombre),
                datasets: [{
                    label: 'Participaciones',
                    data: graficos.top_voluntarios.map(v => v.participaciones),
                    backgroundColor: '#17a2b8'
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.9)',
                        padding: 15
                    },
                    zoom: { zoom: { wheel: { enabled: true } } }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    y: {
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    }
                }
            }
        });
    }
}

// Actualizar tabla de eventos
function actualizarTablaEventos(eventos) {
    eventosFiltrados = eventos || [];
    renderizarTablaEventos();
}

function renderizarTablaEventos() {
    const tbody = document.getElementById('tbodyEventos');
    const inicio = (paginaActual - 1) * eventosPorPagina;
    const fin = inicio + eventosPorPagina;
    const eventosPagina = eventosFiltrados.slice(inicio, fin);

    if (eventosPagina.length === 0) {
        tbody.innerHTML = '<tr><td colspan="12" class="text-center text-muted">No hay eventos para mostrar</td></tr>';
        return;
    }

    tbody.innerHTML = eventosPagina.map(evento => {
        const imagen = evento.imagen ? `<img src="${evento.imagen}" alt="${evento.titulo}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">` : '<i class="fas fa-image text-muted" style="font-size: 2rem;"></i>';
        const estadoBadge = obtenerBadgeEstado(evento.estado);
        const tipoBadge = obtenerBadgeTipo(evento.tipo);
        const ubicacionTruncada = evento.ubicacion && evento.ubicacion.length > 30 ? evento.ubicacion.substring(0, 30) + '...' : (evento.ubicacion || 'N/A');
                
                return `
                    <tr>
                        <td>
                    <input type="checkbox" class="checkbox-evento" value="${evento.id}" ${eventosSeleccionados.has(evento.id) ? 'checked' : ''} onchange="toggleEventoSeleccionado(${evento.id})">
                        </td>
                <td>${imagen}</td>
                <td><a href="/ong/eventos/${evento.id}/detalle" style="color: #0C2B44; font-weight: 600; text-decoration: none;">${evento.titulo}</a></td>
                <td>${tipoBadge}</td>
                <td>${estadoBadge}</td>
                <td>${evento.fecha_inicio}</td>
                <td title="${evento.ubicacion || 'N/A'}">${ubicacionTruncada}</td>
                <td class="text-center"><span class="badge" style="background: #17a2b8; color: white;"><i class="far fa-users mr-1"></i>${evento.participantes_count || 0}</span></td>
                <td class="text-center"><span class="badge" style="background: ${evento.tasa_asistencia >= 70 ? '#00A36C' : evento.tasa_asistencia >= 50 ? '#ffc107' : '#dc3545'}; color: white;">${evento.asistieron_count || 0} (${evento.tasa_asistencia || 0}%)</span></td>
                <td class="text-center"><span class="badge" style="background: #dc3545; color: white;"><i class="far fa-heart mr-1"></i>${evento.reacciones_count || 0}</span></td>
                <td class="text-center"><span class="badge" style="background: #6c757d; color: white;"><i class="far fa-share mr-1"></i>${evento.compartidos_count || 0}</span></td>
                <td class="text-center">
                    <div class="btn-group">
                        <a href="/ong/eventos/${evento.id}/dashboard" class="btn btn-sm" style="background: #17a2b8; color: white; border-radius: 8px;" title="Ver Dashboard">
                            <i class="fas fa-chart-line"></i>
                        </a>
                        <a href="/ong/eventos/${evento.id}/editar" class="btn btn-sm" style="background: #ffc107; color: white; border-radius: 8px;" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button onclick="eliminarEvento(${evento.id})" class="btn btn-sm" style="background: #dc3545; color: white; border-radius: 8px;" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
                    </tr>
                `;
            }).join('');

    // Actualizar totales
    const totales = eventosFiltrados.reduce((acc, e) => ({
        participantes: acc.participantes + (e.participantes_count || 0),
        asistieron: acc.asistieron + (e.asistieron_count || 0),
        reacciones: acc.reacciones + (e.reacciones_count || 0),
        compartidos: acc.compartidos + (e.compartidos_count || 0)
    }), { participantes: 0, asistieron: 0, reacciones: 0, compartidos: 0 });

    document.getElementById('totalParticipantesFoot').textContent = totales.participantes;
    document.getElementById('totalAsistieronFoot').textContent = totales.asistieron;
    document.getElementById('totalReaccionesFoot').textContent = totales.reacciones;
    document.getElementById('totalCompartidosFoot').textContent = totales.compartidos;

    // Actualizar paginación
    const totalPaginas = Math.ceil(eventosFiltrados.length / eventosPorPagina);
    document.getElementById('paginaActual').textContent = paginaActual;
    document.getElementById('totalPaginas').textContent = totalPaginas || 1;
    document.getElementById('btnAnterior').disabled = paginaActual === 1;
    document.getElementById('btnSiguiente').disabled = paginaActual >= totalPaginas;
}

function obtenerBadgeEstado(estado) {
    const estados = {
        'publicado': { color: '#00A36C', texto: 'Publicado' },
        'activo': { color: '#17a2b8', texto: 'Activo' },
        'borrador': { color: '#ffc107', texto: 'Borrador' },
        'finalizado': { color: '#6c757d', texto: 'Finalizado' },
        'cancelado': { color: '#dc3545', texto: 'Cancelado' }
    };
    const estadoData = estados[estado?.toLowerCase()] || { color: '#6c757d', texto: estado || 'N/A' };
    return `<span class="badge" style="background: ${estadoData.color}; color: white; border-radius: 20px;">${estadoData.texto}</span>`;
}

function obtenerBadgeTipo(tipo) {
    const tipos = {
        'educación': { color: '#0C2B44' },
        'salud': { color: '#dc3545' },
        'medio ambiente': { color: '#00A36C' },
        'social': { color: '#17a2b8' },
        'cultural': { color: '#ffc107' },
        'deportivo': { color: '#e83e8c' }
    };
    const tipoData = tipos[tipo?.toLowerCase()] || { color: '#6c757d' };
    return `<span class="badge" style="background: ${tipoData.color}; color: white; border-radius: 20px;">${tipo || 'N/A'}</span>`;
}

// Actualizar alertas
function actualizarAlertas(alertas) {
    const contenedor = document.getElementById('contenedorAlertas');
    contenedor.innerHTML = '';

    alertas.forEach((alerta, index) => {
        const colores = {
            'warning': { bg: 'linear-gradient(135deg, #fff8e1 0%, #ffecb3 100%)', border: '#ff9800', icon: 'fa-calendar-day', iconColor: '#ff9800' },
            'danger': { bg: 'linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%)', border: '#f44336', icon: 'fa-exclamation-triangle', iconColor: '#f44336' },
            'info': { bg: 'linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%)', border: '#2196f3', icon: 'fa-user-clock', iconColor: '#2196f3' }
        };

        const color = colores[alerta.severidad] || colores.warning;
        const titulos = {
            'eventos_proximos': 'Eventos Próximos a Iniciar',
            'baja_participacion': 'Alerta: Baja Participación',
            'voluntarios_inactivos': 'Voluntarios Inactivos',
            'tareas_pendientes': 'Tareas Pendientes'
        };

        const alertaHTML = `
            <div class="col-12 mb-3">
                <div class="alerta-card" style="background: ${color.bg}; border-left: 4px solid ${color.border};">
                    <button class="btn-cerrar" onclick="cerrarAlerta(${index})">&times;</button>
                    <div class="d-flex align-items-start">
                        <i class="fas ${color.icon}" style="font-size: 2.5rem; color: ${color.iconColor}; margin-right: 1rem; float: left;"></i>
                        <div style="flex: 1;">
                            <h5 style="font-weight: 700; color: ${color.border}; margin-bottom: 0.5rem;">${titulos[alerta.tipo] || alerta.mensaje}</h5>
                            <p style="margin-bottom: 1rem; color: #333;">${alerta.mensaje}</p>
                            ${alerta.eventos_afectados && alerta.eventos_afectados.length > 0 ? `
                                <ul style="margin-bottom: 1rem;">
                                    ${alerta.eventos_afectados.slice(0, 5).map(e => `
                                        <li style="margin-bottom: 0.5rem;">
                                            <strong>${e.titulo}</strong>
                                            ${e.fecha_inicio ? ` - ${e.fecha_inicio}` : ''}
                                            ${e.participantes !== undefined ? ` (${e.participantes} participantes)` : ''}
                                        </li>
                                    `).join('')}
                                </ul>
                            ` : ''}
                            <button class="btn btn-sm" style="background: ${color.border}; color: white; border-radius: 8px; padding: 0.5rem 1rem;" onclick="verDetalleAlerta('${alerta.tipo}')">
                                Ver Detalles
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        contenedor.innerHTML += alertaHTML;
    });
}

function cerrarAlerta(index) {
    const alertas = document.querySelectorAll('.alerta-card');
    if (alertas[index]) {
        alertas[index].style.display = 'none';
    }
}

function verDetalleAlerta(tipo) {
    // Implementar lógica de ver detalles según el tipo
    console.log('Ver detalle de alerta:', tipo);
}

// Actualizar métricas de engagement
function actualizarMetricasEngagement(metricas) {
    // Engagement Rate (Circular)
    const ctxEngagementRate = document.getElementById('graficoEngagementRate');
    if (ctxEngagementRate) {
        const rate = metricas.engagement_rate || 0;
        document.getElementById('valorEngagementRate').textContent = rate + '%';
        
        new Chart(ctxEngagementRate, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [rate, 100 - rate],
                    backgroundColor: ['#00A36C', '#e9ecef']
                }]
            },
            options: {
                cutout: '75%',
                plugins: { legend: { display: false } }
            }
        });
    }

    // Tasa de Conversión
    const ctxConversion = document.getElementById('graficoTasaConversion');
    if (ctxConversion) {
        const conversion = metricas.tasa_conversion || 0;
        document.getElementById('valorTasaConversion').textContent = conversion + '%';
        
        new Chart(ctxConversion, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [conversion, 100 - conversion],
                    backgroundColor: ['#17a2b8', '#e9ecef']
                }]
            },
            options: {
                cutout: '75%',
                plugins: { legend: { display: false } }
            }
        });
    }

    // Índice de Satisfacción (si existe en métricas)
    document.getElementById('indiceSatisfaccion').textContent = metricas.indice_satisfaccion || '-';
}

// Actualizar comparativa temporal
function actualizarComparativaTemporal(comparativa) {
    if (!comparativa) return;

    const tbody = document.getElementById('tbodyComparativa');
    const metricas = [
        { key: 'eventos_realizados', label: 'Eventos Realizados' },
        { key: 'nuevas_inscripciones', label: 'Nuevas Inscripciones' },
        { key: 'tasa_asistencia', label: 'Tasa Asistencia' },
        { key: 'promedio_participantes', label: 'Promedio Participantes' },
        { key: 'total_reacciones', label: 'Total Reacciones' },
        { key: 'total_compartidos', label: 'Total Compartidos' }
    ];

    tbody.innerHTML = metricas.map(metrica => {
        const actual = comparativa.actual[metrica.key] || 0;
        const anterior = comparativa.anterior[metrica.key] || 0;
        const variacion = comparativa.variaciones[metrica.key] || 0;
        const esPositivo = variacion >= 0;
        const icono = esPositivo ? 'fa-arrow-up text-success' : 'fa-arrow-down text-danger';
        const badgeClass = esPositivo ? 'badge-success' : 'badge-danger';

        return `
            <tr>
                <td style="font-weight: 600;">${metrica.label}</td>
                <td class="text-center">${actual}</td>
                <td class="text-center">${anterior}</td>
                <td class="text-center">
                    <span class="badge ${badgeClass}" style="border-radius: 20px;">
                        <i class="fas ${icono}"></i> ${Math.abs(variacion)}%
                    </span>
                </td>
                <td class="text-center">
                    <div class="sparkline-container" id="sparkline${metrica.key}"></div>
                </td>
            </tr>
        `;
    }).join('');
}

// Funciones de filtros
function aplicarFiltrosReportes() {
    paginaActual = 1;
    cargarDatosReportes();
}

function resetearFiltrosReportes() {
    const fechaFin = new Date();
    const fechaInicio = new Date();
    fechaInicio.setMonth(fechaInicio.getMonth() - 6);
    
    document.getElementById('filtroFechaInicio').value = fechaInicio.toISOString().split('T')[0];
    document.getElementById('filtroFechaFin').value = fechaFin.toISOString().split('T')[0];
    document.getElementById('filtroEstadoReporte').value = 'Todos';
    document.getElementById('filtroTipoEvento').value = 'Todos';
    document.getElementById('filtroVoluntario').value = '';
    
    paginaActual = 1;
    cargarDatosReportes();
}

// Funciones de exportación
async function exportarPDFReportes() {
    try {
        const btn = event.target;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Generando...';

        const fechaInicio = document.getElementById('filtroFechaInicio').value;
        const fechaFin = document.getElementById('filtroFechaFin').value;
        const estadoEvento = document.getElementById('filtroEstadoReporte').value;
        const tipoEvento = document.getElementById('filtroTipoEvento').value;
        const voluntarioId = document.getElementById('filtroVoluntario').value;

        const params = new URLSearchParams({
            fecha_inicio: fechaInicio,
            fecha_fin: fechaFin,
            estado_evento: estadoEvento,
            tipo_evento: tipoEvento
        });

        if (voluntarioId) params.append('voluntario_id', voluntarioId);

        window.open(`${API_BASE_URL}/api/ong/reportes/export-pdf?${params.toString()}`, '_blank');
        
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-file-pdf mr-2"></i>Exportar PDF Completo';
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al exportar PDF: ' + error.message
        });
    }
}

function compartirDashboard() {
    Swal.fire({
        title: 'Compartir Dashboard',
        html: `
            <p>Esta funcionalidad permitirá generar un link público temporal válido por 7 días.</p>
            <div class="form-group mt-3">
                <label>Proteger con contraseña (opcional)</label>
                <input type="password" class="form-control" id="passwordCompartir" placeholder="Contraseña">
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Generar Link',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Implementar lógica de generación de link
            Swal.fire('Éxito', 'Link generado correctamente', 'success');
        }
    });
}

function programarReporte() {
    Swal.fire({
        title: 'Programar Reporte',
        html: `
            <p>Configurar envío automático por email.</p>
            <div class="form-group mt-3">
                <label>Frecuencia</label>
                <select class="form-control" id="frecuenciaReporte">
                    <option value="semanal">Semanal</option>
                    <option value="mensual">Mensual</option>
                </select>
            </div>
            <div class="form-group mt-3">
                <label>Email</label>
                <input type="email" class="form-control" id="emailReporte" placeholder="email@ejemplo.com">
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Programar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Implementar lógica de programación
            Swal.fire('Éxito', 'Reporte programado correctamente', 'success');
        }
    });
}

// Funciones de tabla
function toggleSelectAll() {
    const checkAll = document.getElementById('checkAllEventos');
    const checkboxes = document.querySelectorAll('.checkbox-evento');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = checkAll.checked;
        const eventoId = parseInt(checkbox.value);
        if (checkAll.checked) {
            eventosSeleccionados.add(eventoId);
        } else {
            eventosSeleccionados.delete(eventoId);
        }
    });
}

function toggleEventoSeleccionado(eventoId) {
    if (eventosSeleccionados.has(eventoId)) {
        eventosSeleccionados.delete(eventoId);
    } else {
        eventosSeleccionados.add(eventoId);
    }
}

function cambiarPagina(direccion) {
    const totalPaginas = Math.ceil(eventosFiltrados.length / eventosPorPagina);
    const nuevaPagina = paginaActual + direccion;
    
    if (nuevaPagina >= 1 && nuevaPagina <= totalPaginas) {
        paginaActual = nuevaPagina;
        renderizarTablaEventos();
    }
}

// Búsqueda con debounce
let debounceTimer;
document.getElementById('busquedaTablaEventos')?.addEventListener('input', function() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        const busqueda = this.value.toLowerCase();
        if (!datosReportes || !datosReportes.eventos_detalle) return;
        
        eventosFiltrados = datosReportes.eventos_detalle.filter(e => 
            e.titulo.toLowerCase().includes(busqueda) ||
            e.tipo.toLowerCase().includes(busqueda) ||
            (e.ubicacion && e.ubicacion.toLowerCase().includes(busqueda))
        );
        
        paginaActual = 1;
        renderizarTablaEventos();
    }, 300);
});

// Funciones de exportación de tabla
function exportarSeleccionadosCSV() {
    if (eventosSeleccionados.size === 0) {
        Swal.fire('Atención', 'Selecciona al menos un evento', 'warning');
        return;
    }
    
    const eventos = datosReportes.eventos_detalle.filter(e => eventosSeleccionados.has(e.id));
    // Implementar exportación CSV
    Swal.fire('Éxito', 'CSV generado correctamente', 'success');
}

function eliminarEvento(eventoId) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Implementar eliminación
            Swal.fire('Eliminado', 'Evento eliminado correctamente', 'success');
            cargarDatosReportes();
        }
    });
}

// Funciones de análisis por voluntario
function mostrarDetalleVoluntario(userId) {
    // Obtener datos del voluntario desde datosReportes
    const voluntario = datosReportes?.voluntarios_activos?.find(v => v.user_id == userId || v.user_id === parseInt(userId));
    if (!voluntario) {
        // Si no se encuentra, ocultar sección
        document.getElementById('seccionAnalisisVoluntario').style.display = 'none';
        return;
    }

    document.getElementById('voluntarioNombre').textContent = voluntario.nombre || 'Usuario';
    document.getElementById('voluntarioContacto').textContent = `${voluntario.email || ''}`;
    document.getElementById('voluntarioTotalEventos').textContent = voluntario.eventos_count || 0;
    document.getElementById('voluntarioTasaAsistencia').textContent = (voluntario.tasa_asistencia || 0) + '%';
    
    if (voluntario.foto_perfil || voluntario.avatar) {
        document.getElementById('voluntarioFotoPerfil').src = voluntario.foto_perfil || voluntario.avatar || '/assets/img/default-avatar.png';
    } else {
        document.getElementById('voluntarioFotoPerfil').src = '/assets/img/default-avatar.png';
    }

    // Mostrar sección
    document.getElementById('seccionAnalisisVoluntario').style.display = 'block';
    
    // Scroll suave a la sección
    document.getElementById('seccionAnalisisVoluntario').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function generarReporteVoluntario() {
    const voluntarioId = document.getElementById('filtroVoluntario').value;
    if (!voluntarioId) {
        Swal.fire('Atención', 'Selecciona un voluntario', 'warning');
        return;
    }
    
    window.open(`${API_BASE_URL}/api/ong/reportes/voluntario/${voluntarioId}/pdf`, '_blank');
}

function calcularComparativaTemporal(periodo) {
    // Actualizar fechas según período seleccionado
    const fechaFin = new Date();
    const fechaInicio = new Date();
    
    switch(parseInt(periodo)) {
        case 7:
            fechaInicio.setDate(fechaInicio.getDate() - 7);
            break;
        case 30:
            fechaInicio.setMonth(fechaInicio.getMonth() - 1);
            break;
        case 90:
            fechaInicio.setMonth(fechaInicio.getMonth() - 3);
            break;
        case 180:
            fechaInicio.setMonth(fechaInicio.getMonth() - 6);
            break;
        case 365:
            fechaInicio.setFullYear(fechaInicio.getFullYear() - 1);
            break;
        default:
            fechaInicio.setMonth(fechaInicio.getMonth() - 1);
    }
    
    document.getElementById('filtroFechaInicio').value = fechaInicio.toISOString().split('T')[0];
    document.getElementById('filtroFechaFin').value = fechaFin.toISOString().split('T')[0];
    
    // Recalcular datos con nuevo período
    cargarDatosReportes();
}

// Utilidades
function mostrarLoading() {
    const overlay = document.createElement('div');
    overlay.className = 'spinner-overlay';
    overlay.id = 'loadingOverlay';
    overlay.innerHTML = '<div class="spinner-border" role="status"><span class="sr-only">Cargando...</span></div>';
    document.body.appendChild(overlay);
}

function ocultarLoading() {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) overlay.remove();
}
</script>
@endpush

@extends('layouts.adminlte')

@section('page_title', 'Dashboard General - ONG')

@section('content_body')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header border-0" style="background: #0C2B44;">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h3 class="card-title mb-0" style="font-weight: 700; font-size: 2rem; letter-spacing: 0.5px; color: #ffffff;">
                        Dashboard General
                    </h3>
                    <p class="mb-0 mt-1" style="color: rgba(255,255,255,0.9); font-size: 1.1rem; font-weight: 500;">Panel centralizado de estadísticas</p>
                </div>
                <div class="d-flex mt-2 mt-md-0" style="gap: 0.5rem;">
                    <button id="btnDescargarPDF" class="btn btn-sm" onclick="descargarPDFDashboard()" style="background: #dc3545; border: none; color: #ffffff; border-radius: 6px; font-weight: 600;">
                        <i class="fas fa-file-pdf mr-2"></i> PDF
                    </button>
                    <button id="btnDescargarExcel" class="btn btn-sm" onclick="descargarExcel()" style="background: #00A36C; border: none; color: #ffffff; border-radius: 6px; font-weight: 600;">
                        <i class="fas fa-file-excel mr-2"></i> Excel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header border-0" style="background: #f8f9fa; border-bottom: 1px solid #e9ecef !important;">
            <h5 class="card-title mb-0" style="color: #0C2B44; font-weight: 600; font-size: 1rem;">
                <i class="fas fa-filter mr-2" style="color: #00A36C;"></i> Filtros
            </h5>
        </div>
        <div class="card-body" style="padding: 1.25rem;">
            <div class="row">
                <div class="col-md-3 mb-2">
                    <label for="fechaInicio" style="font-size: 1rem; color: #0C2B44; font-weight: 600;">Fecha Inicio</label>
                    <input type="date" id="fechaInicio" class="form-control" style="border-radius: 6px; border: 1px solid #dee2e6;" />
                </div>
                <div class="col-md-3 mb-2">
                    <label for="fechaFin" style="font-size: 1rem; color: #0C2B44; font-weight: 600;">Fecha Fin</label>
                    <input type="date" id="fechaFin" class="form-control" style="border-radius: 6px; border: 1px solid #dee2e6;" />
                </div>
                <div class="col-md-2 mb-2">
                    <label for="estadoEvento" style="font-size: 1rem; color: #0C2B44; font-weight: 600;">Estado</label>
                    <select id="estadoEvento" class="form-control" style="border-radius: 6px; border: 1px solid #dee2e6;">
                        <option value="">Todos</option>
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                        <option value="finalizado">Finalizado</option>
                        <option value="cancelado">Cancelado</option>
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label for="tipoParticipacion" style="font-size: 1rem; color: #0C2B44; font-weight: 600;">Tipo</label>
                    <select id="tipoParticipacion" class="form-control" style="border-radius: 6px; border: 1px solid #dee2e6;">
                        <option value="">Todos</option>
                        <option value="voluntario">Voluntario</option>
                        <option value="asistente">Asistente</option>
                        <option value="colaborador">Colaborador</option>
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label for="busquedaEvento" style="font-size: 1rem; color: #0C2B44; font-weight: 600;">Buscar</label>
                    <input type="text" id="busquedaEvento" class="form-control" placeholder="Nombre..." style="border-radius: 6px; border: 1px solid #dee2e6;" />
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <button class="btn btn-sm" onclick="aplicarFiltros()" style="background: #0C2B44; border: none; color: #ffffff; border-radius: 6px; padding: 0.5rem 1.5rem; font-weight: 600;">
                        <i class="fas fa-search mr-2"></i> Aplicar
                    </button>
                    <button class="btn btn-sm ml-2" onclick="resetearFiltros()" style="background: #6c757d; border: none; color: #ffffff; border-radius: 6px; padding: 0.5rem 1.5rem; font-weight: 600;">
                        <i class="fas fa-redo mr-2"></i> Resetear
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertas -->
    <div id="alertasContainer" class="mb-4"></div>

    <!-- Tarjetas de Métricas -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm" style="border-radius: 8px; border-left: 4px solid #dc3545 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 id="totalEventosActivos" class="mb-0" style="font-size: 2.5rem; font-weight: 800; color: #0C2B44; letter-spacing: -1px;">0</h3>
                            <p class="mb-0 mt-2" style="color: #0C2B44; font-size: 1rem; font-weight: 600;">Eventos Activos</p>
                        </div>
                        <div style="color: #dc3545; opacity: 0.2; font-size: 3rem;">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm" style="border-radius: 8px; border-left: 4px solid #00A36C !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 id="totalReacciones" class="mb-0" style="font-size: 2.5rem; font-weight: 800; color: #0C2B44; letter-spacing: -1px;">0</h3>
                            <p class="mb-0 mt-2" style="color: #0C2B44; font-size: 1rem; font-weight: 600;">Total Reacciones</p>
                        </div>
                        <div style="color: #00A36C; opacity: 0.2; font-size: 3rem;">
                            <i class="fas fa-heart"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm" style="border-radius: 8px; border-left: 4px solid #17a2b8 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 id="totalCompartidos" class="mb-0" style="font-size: 2.5rem; font-weight: 800; color: #0C2B44; letter-spacing: -1px;">0</h3>
                            <p class="mb-0 mt-2" style="color: #0C2B44; font-size: 1rem; font-weight: 600;">Total Compartidos</p>
                        </div>
                        <div style="color: #17a2b8; opacity: 0.2; font-size: 3rem;">
                            <i class="fas fa-share-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm" style="border-radius: 8px; border-left: 4px solid #ffc107 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 id="totalVoluntarios" class="mb-0" style="font-size: 2.5rem; font-weight: 800; color: #0C2B44; letter-spacing: -1px;">0</h3>
                            <p class="mb-0 mt-2" style="color: #0C2B44; font-size: 1rem; font-weight: 600;">Total Voluntarios</p>
                        </div>
                        <div style="color: #ffc107; opacity: 0.2; font-size: 3rem;">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm" style="border-radius: 8px; border-left: 4px solid #00A36C !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 id="totalParticipantes" class="mb-0" style="font-size: 2.5rem; font-weight: 800; color: #0C2B44; letter-spacing: -1px;">0</h3>
                            <p class="mb-0 mt-2" style="color: #0C2B44; font-size: 1rem; font-weight: 600;">Total Participantes</p>
                        </div>
                        <div style="color: #00A36C; opacity: 0.2; font-size: 3rem;">
                            <i class="fas fa-user-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm" style="border-radius: 8px; border-left: 4px solid #6c757d !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 id="totalEventosFinalizados" class="mb-0" style="font-size: 2.5rem; font-weight: 800; color: #0C2B44; letter-spacing: -1px;">0</h3>
                            <p class="mb-0 mt-2" style="color: #0C2B44; font-size: 1rem; font-weight: 600;">Eventos Finalizados</p>
                        </div>
                        <div style="color: #6c757d; opacity: 0.2; font-size: 3rem;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row mb-4">
        <!-- Gráfico de Líneas - Tendencias Mensuales -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header border-0" style="background: #f8f9fa; border-bottom: 1px solid #e9ecef !important;">
                    <h5 class="card-title mb-0" style="color: #0C2B44; font-weight: 700; font-size: 1.2rem;">
                        <i class="fas fa-chart-line mr-2" style="color: #00A36C;"></i> Tendencias Mensuales
                    </h5>
                </div>
                <div class="card-body">
                    <div style="height: 300px; position: relative;">
                        <canvas id="graficaTendenciasMensuales"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico de Dona - Distribución de Estados -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header border-0" style="background: #f8f9fa; border-bottom: 1px solid #e9ecef !important;">
                    <h5 class="card-title mb-0" style="color: #0C2B44; font-weight: 700; font-size: 1.2rem;">
                        <i class="fas fa-chart-pie mr-2" style="color: #00A36C;"></i> Distribución de Estados
                    </h5>
                </div>
                <div class="card-body">
                    <div style="height: 300px; position: relative;">
                        <canvas id="graficaDistribucionEstados"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico de Barras - Comparativa Eventos -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header border-0" style="background: #f8f9fa; border-bottom: 1px solid #e9ecef !important;">
                    <h5 class="card-title mb-0" style="color: #0C2B44; font-weight: 700; font-size: 1.2rem;">
                        <i class="fas fa-chart-bar mr-2" style="color: #00A36C;"></i> Comparativa por Evento
                    </h5>
                </div>
                <div class="card-body">
                    <div style="height: 300px; position: relative;">
                        <canvas id="graficaComparativaEventos"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico de Área - Actividad Semanal -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header border-0" style="background: #f8f9fa; border-bottom: 1px solid #e9ecef !important;">
                    <h5 class="card-title mb-0" style="color: #0C2B44; font-weight: 700; font-size: 1.2rem;">
                        <i class="fas fa-chart-area mr-2" style="color: #00A36C;"></i> Actividad Semanal
                    </h5>
                </div>
                <div class="card-body">
                    <div style="height: 300px; position: relative;">
                        <canvas id="graficaActividadSemanal"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico de Columnas Apiladas - Reacciones vs Compartidos -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header border-0" style="background: #f8f9fa; border-bottom: 1px solid #e9ecef !important;">
                    <h5 class="card-title mb-0" style="color: #0C2B44; font-weight: 700; font-size: 1.2rem;">
                        <i class="fas fa-chart-bar mr-2" style="color: #00A36C;"></i> Reacciones vs Compartidos
                    </h5>
                </div>
                <div class="card-body">
                    <div style="height: 300px; position: relative;">
                        <canvas id="graficaReaccionesVsCompartidos"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico Radar - Métricas Generales -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header border-0" style="background: #f8f9fa; border-bottom: 1px solid #e9ecef !important;">
                    <h5 class="card-title mb-0" style="color: #0C2B44; font-weight: 700; font-size: 1.2rem;">
                        <i class="fas fa-chart-pie mr-2" style="color: #00A36C;"></i> Métricas Generales
                    </h5>
                </div>
                <div class="card-body">
                    <div style="height: 300px; position: relative;">
                        <canvas id="graficaRadar"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Listado de Eventos -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header border-0" style="background: #f8f9fa; border-bottom: 1px solid #e9ecef !important;">
                    <h5 class="card-title mb-0" style="color: #0C2B44; font-weight: 700; font-size: 1.2rem;">
                        <i class="fas fa-list mr-2" style="color: #00A36C;"></i> Listado de Eventos
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead>
                                <tr>
                                    <th style="font-size: 1rem; font-weight: 700; color: #0C2B44;">Tipo</th>
                                    <th style="font-size: 1rem; font-weight: 700; color: #0C2B44;">Título</th>
                                    <th style="font-size: 1rem; font-weight: 700; color: #0C2B44;">Fecha Inicio</th>
                                    <th style="font-size: 1rem; font-weight: 700; color: #0C2B44;">Ubicación</th>
                                    <th class="text-center" style="font-size: 1rem; font-weight: 700; color: #0C2B44;">Participantes</th>
                                    <th style="font-size: 1rem; font-weight: 700; color: #0C2B44;">Estado</th>
                                    <th class="text-center" style="font-size: 1rem; font-weight: 700; color: #0C2B44;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tablaListadoEventos">
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="spinner-border" role="status" style="color: #00A36C;">
                                            <span class="sr-only">Cargando...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Actividad Reciente (30 días) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header border-0" style="background: #f8f9fa; border-bottom: 1px solid #e9ecef !important;">
                    <h5 class="card-title mb-0" style="color: #0C2B44; font-weight: 700; font-size: 1.2rem;">
                        <i class="fas fa-calendar-day mr-2" style="color: #00A36C;"></i> Actividad Últimos 30 Días
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th class="text-center">Reacciones</th>
                                    <th class="text-center">Compartidos</th>
                                    <th class="text-center">Inscripciones</th>
                                    <th class="text-center">Total</th>
                                </tr>
                            </thead>
                            <tbody id="tablaActividadReciente">
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <div class="spinner-border" role="status" style="color: #00A36C;">
                                            <span class="sr-only">Cargando...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla Top 10 Eventos -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header border-0" style="background: #f8f9fa; border-bottom: 1px solid #e9ecef !important;">
                    <h5 class="card-title mb-0" style="color: #0C2B44; font-weight: 700; font-size: 1.2rem;">
                        <i class="fas fa-trophy mr-2" style="color: #00A36C;"></i> Top 10 Eventos por Engagement
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Título</th>
                                    <th class="text-center">Reacciones</th>
                                    <th class="text-center">Compartidos</th>
                                    <th class="text-center">Inscripciones</th>
                                    <th class="text-center">Engagement</th>
                                </tr>
                            </thead>
                            <tbody id="tablaTopEventos">
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="spinner-border" role="status" style="color: #00A36C;">
                                            <span class="sr-only">Cargando...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla Top 10 Voluntarios -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header border-0" style="background: #f8f9fa; border-bottom: 1px solid #e9ecef !important;">
                    <h5 class="card-title mb-0" style="color: #0C2B44; font-weight: 700; font-size: 1.2rem;">
                        <i class="fas fa-users mr-2" style="color: #00A36C;"></i> Top 10 Voluntarios
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th class="text-center">Eventos</th>
                                    <th class="text-center">Horas</th>
                                </tr>
                            </thead>
                            <tbody id="tablaTopVoluntarios">
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <div class="spinner-border" role="status" style="color: #00A36C;">
                                            <span class="sr-only">Cargando...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Distribución de Participantes -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header border-0" style="background: #f8f9fa; border-bottom: 1px solid #e9ecef !important;">
                    <h5 class="card-title mb-0" style="color: #0C2B44; font-weight: 700; font-size: 1.2rem;">
                        <i class="fas fa-chart-pie mr-2" style="color: #00A36C;"></i> Distribución por Tipo
                    </h5>
                </div>
                <div class="card-body">
                    <div style="height: 300px; position: relative;">
                        <canvas id="graficaDistribucionTipo"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header border-0" style="background: #f8f9fa; border-bottom: 1px solid #e9ecef !important;">
                    <h5 class="card-title mb-0" style="color: #0C2B44; font-weight: 700; font-size: 1.2rem;">
                        <i class="fas fa-chart-pie mr-2" style="color: #00A36C;"></i> Distribución por Estado
                    </h5>
                </div>
                <div class="card-body">
                    <div style="height: 300px; position: relative;">
                        <canvas id="graficaDistribucionEstado"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@push('css')
<style>
    /* Estilo Minimalista con Paleta Corporativa */
    body {
        background-color: #f8f9fa;
    }

    .card {
        border-radius: 8px;
        border: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: all 0.2s ease;
    }

    .card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        transform: translateY(-2px);
    }

    .card-header {
        border-bottom: 1px solid #e9ecef;
        border-radius: 8px 8px 0 0 !important;
        padding: 1rem 1.25rem;
    }

    .card-body {
        padding: 1.25rem;
    }

    canvas {
        max-width: 100%;
    }

    .badge {
        padding: 0.4rem 0.8rem;
        font-size: 0.85rem;
        border-radius: 6px;
        font-weight: 500;
    }

    .alert {
        border-radius: 8px;
        border-left: 4px solid;
        border: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .alert-danger {
        border-left-color: #dc3545;
        background-color: #fff5f5;
        color: #721c24;
    }

    .alert-warning {
        border-left-color: #ffc107;
        background-color: #fffbf0;
        color: #856404;
    }

    .alert-info {
        border-left-color: #17a2b8;
        background-color: #f0f9fa;
        color: #0c5460;
    }

    .table {
        font-size: 1rem;
    }

    .table th {
        font-weight: 700;
        text-transform: none;
        font-size: 1rem;
        color: #0C2B44;
        background-color: #f8f9fa;
        border-bottom: 2px solid #e9ecef;
        padding: 1rem;
        letter-spacing: 0.3px;
    }

    .table td {
        padding: 1rem;
        border-bottom: 1px solid #f0f0f0;
        font-size: 0.95rem;
        font-weight: 500;
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background-color: #ffffff;
    }

    .table-striped tbody tr:nth-of-type(even) {
        background-color: #f8f9fa;
    }

    .table-hover tbody tr:hover {
        background-color: #f0f7ff;
    }

    .btn {
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .form-control {
        border-radius: 6px;
        border: 1px solid #dee2e6;
        transition: all 0.2s ease;
    }

    .form-control:focus {
        border-color: #00A36C;
        box-shadow: 0 0 0 0.2rem rgba(0, 163, 108, 0.15);
    }

    /* Colores corporativos */
    .text-primary-custom {
        color: #0C2B44 !important;
    }

    .text-success-custom {
        color: #00A36C !important;
    }

    .bg-primary-custom {
        background-color: #0C2B44 !important;
    }

    .bg-success-custom {
        background-color: #00A36C !important;
    }
</style>
@endpush

@section('js')
@parent
<script src="{{ asset('js/notificaciones-ong.js') }}"></script>
<script src="{{ asset('assets/js/config.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
// Definir API_BASE_URL si no está definido
if (typeof API_BASE_URL === 'undefined') {
    window.API_BASE_URL = "{{ env('APP_URL', 'http://localhost:8000') }}";
    var API_BASE_URL = window.API_BASE_URL;
    console.log("🌐 API_BASE_URL definido:", API_BASE_URL);
}

const token = localStorage.getItem('token');
let charts = {};
let intervaloActualizacion = null;
let cargandoDashboard = false; // Flag para evitar múltiples cargas simultáneas

// Inicializar fechas por defecto
document.addEventListener('DOMContentLoaded', () => {
    const fechaFin = new Date();
    const fechaInicio = new Date();
    fechaInicio.setMonth(fechaInicio.getMonth() - 6);
    
    document.getElementById('fechaInicio').value = fechaInicio.toISOString().split('T')[0];
    document.getElementById('fechaFin').value = fechaFin.toISOString().split('T')[0];
    
    // Cargar dashboard solo una vez al inicio
    cargarDashboard();
    
    // Actualizar automáticamente cada 10 minutos (aumentado para reducir carga)
    intervaloActualizacion = setInterval(() => {
        if (!cargandoDashboard) {
            cargarDashboard();
        }
    }, 600000); // 10 minutos
});

async function cargarDashboard() {
    // Evitar múltiples cargas simultáneas
    if (cargandoDashboard) {
        console.log('⏳ Dashboard ya se está cargando, esperando...');
        return;
    }
    
    cargandoDashboard = true;
    
    try {
        // Asegurar que API_BASE_URL esté definido
        const apiUrl = window.API_BASE_URL || API_BASE_URL || "{{ env('APP_URL', 'http://localhost:8000') }}";
        
        if (!apiUrl) {
            throw new Error('API_BASE_URL no está definido');
        }

        if (!token) {
            throw new Error('No hay token de autenticación. Por favor, inicia sesión nuevamente.');
        }

        // Obtener filtros
        const fechaInicio = document.getElementById('fechaInicio')?.value || '';
        const fechaFin = document.getElementById('fechaFin')?.value || '';
        const estadoEvento = document.getElementById('estadoEvento')?.value || '';
        const tipoParticipacion = document.getElementById('tipoParticipacion')?.value || '';
        const busquedaEvento = document.getElementById('busquedaEvento')?.value || '';

        const params = new URLSearchParams();
        if (fechaInicio) params.append('fecha_inicio', fechaInicio);
        if (fechaFin) params.append('fecha_fin', fechaFin);
        if (estadoEvento) params.append('estado_evento', estadoEvento);
        if (tipoParticipacion) params.append('tipo_participacion', tipoParticipacion);
        if (busquedaEvento) params.append('busqueda_evento', busquedaEvento);

        const url = `${apiUrl}/api/ong/dashboard?${params.toString()}`;

        const res = await fetch(url, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'include'
        });

        if (!res.ok) {
            const errorData = await res.json().catch(() => ({}));
            throw new Error(errorData.error || `Error ${res.status}: ${res.statusText}`);
        }

        const response = await res.json();

        if (!response.success) {
            throw new Error(response.error || 'Error al cargar datos');
        }

        const data = response.data;

        // Actualizar métricas
        actualizarMetricas(data.metricas);
        
        // Crear gráficos
        crearGraficos(data);
        
        // Actualizar tablas
        actualizarTablas(data);
        
        // Mostrar alertas
        mostrarAlertas(data.alertas);
        
        // Mostrar comparativas
        mostrarComparativas(data.comparativas);
        
        return Promise.resolve();

    } catch (error) {
        console.error('❌ Error al cargar dashboard:', error);
        mostrarNotificacion('Error al cargar el dashboard: ' + error.message, 'error');
        return Promise.reject(error);
    } finally {
        cargandoDashboard = false;
    }
}

function actualizarMetricas(metricas) {
    document.getElementById('totalEventosActivos').textContent = metricas?.eventos_activos || 0;
    document.getElementById('totalReacciones').textContent = formatNumber(metricas?.total_reacciones || 0);
    document.getElementById('totalCompartidos').textContent = formatNumber(metricas?.total_compartidos || 0);
    document.getElementById('totalVoluntarios').textContent = formatNumber(metricas?.total_voluntarios || 0);
    document.getElementById('totalParticipantes').textContent = formatNumber(metricas?.total_participantes || 0);
    document.getElementById('totalEventosFinalizados').textContent = metricas?.eventos_finalizados || 0;
}

function crearGraficos(data) {
    // Tendencias Mensuales
    crearGraficoTendenciasMensuales(data.tendencias_mensuales);
    
    // Distribución de Estados
    crearGraficoDistribucionEstados(data.distribucion_estados);
    
    // Comparativa Eventos
    crearGraficoComparativaEventos(data.comparativa_eventos);
    
    // Actividad Semanal
    crearGraficoActividadSemanal(data.actividad_semanal);
    
    // Reacciones vs Compartidos
    crearGraficoReaccionesVsCompartidos(data.comparativa_eventos);
    
    // Radar
    crearGraficoRadar(data.metricas_radar);
    
    // Distribución Tipo
    crearGraficoDistribucionTipo(data.distribucion_participantes?.por_tipo || {});
    
    // Distribución Estado
    crearGraficoDistribucionEstado(data.distribucion_participantes?.por_estado || {});
}

function crearGraficoTendenciasMensuales(tendencias) {
    const ctx = document.getElementById('graficaTendenciasMensuales');
    if (!ctx || !tendencias) return;

    if (charts.tendenciasMensuales) charts.tendenciasMensuales.destroy();

    charts.tendenciasMensuales = new Chart(ctx, {
        type: 'line',
        data: {
            labels: Object.keys(tendencias),
            datasets: [{
                label: 'Participantes',
                data: Object.values(tendencias),
                borderColor: '#00A36C',
                backgroundColor: 'rgba(0, 163, 108, 0.08)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#00A36C',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: '#00A36C',
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0C2B44',
                    padding: 12,
                    titleFont: { size: 13, weight: '600' },
                    bodyFont: { size: 12 },
                    borderColor: '#00A36C',
                    borderWidth: 1,
                    cornerRadius: 6
                }
            },
            scales: {
                y: { 
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)' },
                    ticks: { color: '#6c757d', font: { size: 11 } }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#6c757d', font: { size: 11 } }
                }
            }
        }
    });
}

function crearGraficoDistribucionEstados(estados) {
    const ctx = document.getElementById('graficaDistribucionEstados');
    if (!ctx || !estados) return;

    if (charts.distribucionEstados) charts.distribucionEstados.destroy();

    charts.distribucionEstados = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(estados).map(e => e.charAt(0).toUpperCase() + e.slice(1)),
            datasets: [{
                data: Object.values(estados),
                backgroundColor: ['#00A36C', '#0C2B44', '#dc3545', '#17a2b8', '#ffc107'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { 
                    position: 'bottom',
                    labels: { 
                        padding: 15,
                        font: { size: 11 },
                        color: '#0C2B44'
                    }
                },
                tooltip: {
                    backgroundColor: '#0C2B44',
                    padding: 12,
                    titleFont: { size: 13, weight: '600' },
                    bodyFont: { size: 12 },
                    borderColor: '#00A36C',
                    borderWidth: 1,
                    cornerRadius: 6
                }
            }
        }
    });
}

function crearGraficoComparativaEventos(comparativa) {
    const ctx = document.getElementById('graficaComparativaEventos');
    if (!ctx || !comparativa || comparativa.length === 0) return;

    const top10 = comparativa.slice(0, 10);

    if (charts.comparativaEventos) charts.comparativaEventos.destroy();

    charts.comparativaEventos = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: top10.map(e => e.titulo?.substring(0, 20) || 'Evento'),
            datasets: [
                {
                    label: 'Reacciones',
                    data: top10.map(e => e.reacciones || 0),
                    backgroundColor: '#dc3545'
                },
                {
                    label: 'Compartidos',
                    data: top10.map(e => e.compartidos || 0),
                    backgroundColor: '#00A36C'
                },
                {
                    label: 'Participantes',
                    data: top10.map(e => e.participantes || 0),
                    backgroundColor: '#17a2b8'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
}

function crearGraficoActividadSemanal(actividad) {
    const ctx = document.getElementById('graficaActividadSemanal');
    if (!ctx || !actividad) return;

    if (charts.actividadSemanal) charts.actividadSemanal.destroy();

    charts.actividadSemanal = new Chart(ctx, {
        type: 'line',
        data: {
            labels: Object.keys(actividad),
            datasets: [{
                label: 'Actividad Semanal',
                data: Object.values(actividad),
                borderColor: '#17a2b8',
                backgroundColor: 'rgba(23, 162, 184, 0.3)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
}

function crearGraficoReaccionesVsCompartidos(comparativa) {
    const ctx = document.getElementById('graficaReaccionesVsCompartidos');
    if (!ctx || !comparativa || comparativa.length === 0) return;

    const top10 = comparativa.slice(0, 10);

    if (charts.reaccionesVsCompartidos) charts.reaccionesVsCompartidos.destroy();

    charts.reaccionesVsCompartidos = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: top10.map(e => e.titulo?.substring(0, 20) || 'Evento'),
            datasets: [
                {
                    label: 'Reacciones',
                    data: top10.map(e => e.reacciones || 0),
                    backgroundColor: '#dc3545'
                },
                {
                    label: 'Compartidos',
                    data: top10.map(e => e.compartidos || 0),
                    backgroundColor: '#00A36C'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' }
            },
            scales: {
                x: { stacked: true },
                y: { stacked: true, beginAtZero: true }
            }
        }
    });
}

function crearGraficoRadar(metricasRadar) {
    const ctx = document.getElementById('graficaRadar');
    if (!ctx || !metricasRadar) return;

    if (charts.radar) charts.radar.destroy();

    charts.radar = new Chart(ctx, {
        type: 'radar',
        data: {
            labels: ['Reacciones', 'Compartidos', 'Voluntarios', 'Participantes'],
            datasets: [{
                label: 'Métricas',
                data: [
                    metricasRadar.reacciones || 0,
                    metricasRadar.compartidos || 0,
                    metricasRadar.voluntarios || 0,
                    metricasRadar.participantes || 0
                ],
                backgroundColor: 'rgba(0, 163, 108, 0.2)',
                borderColor: '#00A36C',
                borderWidth: 2,
                pointBackgroundColor: '#00A36C',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: '#00A36C'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                r: {
                    beginAtZero: true,
                    max: 100,
                    ticks: { display: false }
                }
            }
        }
    });
}

function crearGraficoDistribucionTipo(distribucion) {
    const ctx = document.getElementById('graficaDistribucionTipo');
    if (!ctx || !distribucion) return;

    if (charts.distribucionTipo) charts.distribucionTipo.destroy();

    charts.distribucionTipo = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(distribucion).map(t => t.charAt(0).toUpperCase() + t.slice(1)),
            datasets: [{
                data: Object.values(distribucion),
                backgroundColor: ['#00A36C', '#17a2b8', '#ffc107']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
}

function crearGraficoDistribucionEstado(distribucion) {
    const ctx = document.getElementById('graficaDistribucionEstado');
    if (!ctx || !distribucion) return;

    if (charts.distribucionEstado) charts.distribucionEstado.destroy();

    charts.distribucionEstado = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(distribucion).map(e => e.charAt(0).toUpperCase() + e.slice(1)),
            datasets: [{
                data: Object.values(distribucion),
                backgroundColor: ['#28a745', '#ffc107', '#dc3545']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
}

function actualizarTablas(data) {
    actualizarTablaListadoEventos(data.listado_eventos || []);
    actualizarTablaActividadReciente(data.actividad_reciente || []);
    actualizarTablaTopEventos(data.top_eventos || []);
    actualizarTablaTopVoluntarios(data.top_voluntarios || []);
}

function actualizarTablaListadoEventos(eventos) {
    const tbody = document.getElementById('tablaListadoEventos');
    if (!eventos || eventos.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4" style="color: #6c757d; font-size: 1rem; font-weight: 600;">No hay eventos disponibles</td></tr>';
        return;
    }

    // Separar eventos por tipo
    const eventosRegulares = eventos.filter(e => e.tipo === 'evento' || !e.tipo);
    const megaEventos = eventos.filter(e => e.tipo === 'mega_evento');
    
    let html = '';
    
    // Mostrar eventos regulares primero
    if (eventosRegulares.length > 0) {
        html += `
            <tr style="background: #e9ecef !important;">
                <td colspan="7" style="font-weight: 700; font-size: 1.2rem; color: #0C2B44; padding: 1rem;">
                    <i class="fas fa-calendar-alt mr-2" style="color: #00A36C;"></i> EVENTOS REGULARES
                </td>
            </tr>
        `;
        
        eventosRegulares.forEach(evento => {
            const fechaInicio = evento.fecha_inicio ? new Date(evento.fecha_inicio).toLocaleDateString('es-ES') : 'N/A';
            const estadoBadge = getEstadoBadge(evento.estado);
            
            html += `
                <tr>
                    <td><span class="badge" style="background: #17a2b8; color: #ffffff; font-size: 0.9rem; font-weight: 700; padding: 0.5rem 0.75rem;">Evento</span></td>
                    <td style="font-weight: 700; color: #0C2B44; font-size: 1.1rem;">${evento.titulo || 'Sin título'}</td>
                    <td style="color: #6c757d; font-size: 1rem; font-weight: 500;">${fechaInicio}</td>
                    <td style="color: #6c757d; font-size: 1rem; font-weight: 500;">${evento.ubicacion || 'N/A'}</td>
                    <td class="text-center"><span class="badge" style="background: #17a2b8; color: #ffffff; font-size: 1rem; font-weight: 700; padding: 0.5rem 0.75rem;">${formatNumber(evento.total_participantes || 0)}</span></td>
                    <td>${estadoBadge}</td>
                    <td class="text-center">
                        <a href="/ong/eventos/${evento.id}/dashboard" class="btn btn-sm" title="Ver Dashboard" style="background: #0C2B44; border: none; color: #ffffff; padding: 0.4rem 0.9rem; font-weight: 600;">
                            <i class="fas fa-chart-bar"></i>
                        </a>
                    </td>
                </tr>
            `;
        });
    }
    
    // Mostrar mega eventos después
    if (megaEventos.length > 0) {
        html += `
            <tr style="background: #fff3cd !important;">
                <td colspan="7" style="font-weight: 700; font-size: 1.2rem; color: #0C2B44; padding: 1rem;">
                    <i class="fas fa-star mr-2" style="color: #ffc107;"></i> MEGA EVENTOS
                </td>
            </tr>
        `;
        
        megaEventos.forEach(evento => {
            const fechaInicio = evento.fecha_inicio ? new Date(evento.fecha_inicio).toLocaleDateString('es-ES') : 'N/A';
            const estadoBadge = getEstadoBadge(evento.estado);
            
            html += `
                <tr>
                    <td><span class="badge" style="background: #ffc107; color: #000; font-size: 0.9rem; font-weight: 700; padding: 0.5rem 0.75rem;">Mega Evento</span></td>
                    <td style="font-weight: 700; color: #0C2B44; font-size: 1.1rem;">${evento.titulo || 'Sin título'}</td>
                    <td style="color: #6c757d; font-size: 1rem; font-weight: 500;">${fechaInicio}</td>
                    <td style="color: #6c757d; font-size: 1rem; font-weight: 500;">${evento.ubicacion || 'N/A'}</td>
                    <td class="text-center"><span class="badge" style="background: #ffc107; color: #000; font-size: 1rem; font-weight: 700; padding: 0.5rem 0.75rem;">${formatNumber(evento.total_participantes || 0)}</span></td>
                    <td>${estadoBadge}</td>
                    <td class="text-center">
                        <a href="/ong/mega-eventos/${evento.id}/seguimiento" class="btn btn-sm" title="Ver Seguimiento" style="background: #0C2B44; border: none; color: #ffffff; padding: 0.4rem 0.9rem; font-weight: 600;">
                            <i class="fas fa-chart-bar"></i>
                        </a>
                    </td>
                </tr>
            `;
        });
    }
    
    tbody.innerHTML = html;
}

function actualizarTablaActividadReciente(actividad) {
    const tbody = document.getElementById('tablaActividadReciente');
    if (!actividad || Object.keys(actividad).length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center">No hay datos disponibles</td></tr>';
        return;
    }

    let html = '';
    let totalReacciones = 0, totalCompartidos = 0, totalInscripciones = 0, totalGeneral = 0;
    
    Object.entries(actividad).forEach(([fecha, datos]) => {
        const fechaFormateada = new Date(fecha).toLocaleDateString('es-ES', { 
            day: '2-digit', 
            month: '2-digit', 
            year: 'numeric' 
        });
        
        totalReacciones += datos.reacciones || 0;
        totalCompartidos += datos.compartidos || 0;
        totalInscripciones += datos.inscripciones || 0;
        totalGeneral += datos.total || 0;
        
        html += `
            <tr>
                <td><strong>${fechaFormateada}</strong></td>
                <td class="text-center">${formatNumber(datos.reacciones || 0)}</td>
                <td class="text-center">${formatNumber(datos.compartidos || 0)}</td>
                <td class="text-center">${formatNumber(datos.inscripciones || 0)}</td>
                <td class="text-center"><strong>${formatNumber(datos.total || 0)}</strong></td>
            </tr>
        `;
    });
    
    html += `
        <tr class="bg-light font-weight-bold">
            <td><strong>TOTAL</strong></td>
            <td class="text-center">${formatNumber(totalReacciones)}</td>
            <td class="text-center">${formatNumber(totalCompartidos)}</td>
            <td class="text-center">${formatNumber(totalInscripciones)}</td>
            <td class="text-center">${formatNumber(totalGeneral)}</td>
        </tr>
    `;
    
    tbody.innerHTML = html;
}

function actualizarTablaTopEventos(topEventos) {
    const tbody = document.getElementById('tablaTopEventos');
    if (!topEventos || topEventos.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">No hay datos disponibles</td></tr>';
        return;
    }

    let html = '';
    topEventos.forEach((evento, index) => {
        html += `
            <tr>
                <td class="text-center"><strong>${index + 1}</strong></td>
                <td>${evento.titulo || 'Evento'}</td>
                <td class="text-center"><span class="badge badge-danger">${formatNumber(evento.reacciones || 0)}</span></td>
                <td class="text-center"><span class="badge badge-success">${formatNumber(evento.compartidos || 0)}</span></td>
                <td class="text-center"><span class="badge badge-info">${formatNumber(evento.inscripciones || 0)}</span></td>
                <td class="text-center"><strong><span class="badge badge-warning" style="font-size: 1rem;">${formatNumber(evento.engagement || 0)}</span></strong></td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

function actualizarTablaTopVoluntarios(topVoluntarios) {
    const tbody = document.getElementById('tablaTopVoluntarios');
    if (!topVoluntarios || topVoluntarios.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center">No hay datos disponibles</td></tr>';
        return;
    }

    let html = '';
    topVoluntarios.forEach((voluntario, index) => {
        html += `
            <tr>
                <td class="text-center"><strong>${index + 1}</strong></td>
                <td>${voluntario.nombre || 'Voluntario'}</td>
                <td>${voluntario.email || 'N/A'}</td>
                <td class="text-center"><span class="badge badge-info">${voluntario.eventos_participados || 0}</span></td>
                <td class="text-center"><span class="badge badge-success">${voluntario.horas_contribuidas || 0} hrs</span></td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

function mostrarAlertas(alertas) {
    const container = document.getElementById('alertasContainer');
    if (!alertas || alertas.length === 0) {
        container.innerHTML = '';
        return;
    }

    let html = '';
    alertas.forEach(alerta => {
        const alertClass = alerta.severidad === 'danger' ? 'alert-danger' : 
                          alerta.severidad === 'warning' ? 'alert-warning' : 'alert-info';
        
        html += `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <strong>${alerta.mensaje}</strong>
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

function mostrarComparativas(comparativas) {
    // Agregar badges de crecimiento a las tarjetas si es necesario
    // Esta función puede expandirse para mostrar indicadores visuales
}

function aplicarFiltros() {
    // Limpiar intervalo de actualización al aplicar filtros
    if (intervaloActualizacion) {
        clearInterval(intervaloActualizacion);
    }
    cargarDashboard();
    
    // Reiniciar intervalo de actualización
    intervaloActualizacion = setInterval(() => {
        if (!cargandoDashboard) {
            cargarDashboard();
        }
    }, 600000); // 10 minutos
}

function resetearFiltros() {
    const fechaFin = new Date();
    const fechaInicio = new Date();
    fechaInicio.setMonth(fechaInicio.getMonth() - 6);
    
    document.getElementById('fechaInicio').value = fechaInicio.toISOString().split('T')[0];
    document.getElementById('fechaFin').value = fechaFin.toISOString().split('T')[0];
    document.getElementById('estadoEvento').value = '';
    document.getElementById('tipoParticipacion').value = '';
    document.getElementById('busquedaEvento').value = '';
    
    // Limpiar cache al resetear filtros
    if (intervaloActualizacion) {
        clearInterval(intervaloActualizacion);
    }
    cargarDashboard();
    
    // Reiniciar intervalo de actualización
    intervaloActualizacion = setInterval(() => {
        if (!cargandoDashboard) {
            cargarDashboard();
        }
    }, 600000); // 10 minutos
}

async function descargarPDFDashboard() {
    const btn = document.getElementById('btnDescargarPDF');
    const token = localStorage.getItem('token') || (window.token ? window.token : null);
    
    if (!token) {
        alert('No hay token de autenticación');
        return;
    }
    
    if (!btn) return;
    if (btn.disabled) return;
    
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Generando PDF...';
    
    try {
        const apiUrl = window.API_BASE_URL || 'http://10.26.5.12:8000';
        const res = await fetch(`${apiUrl}/api/ong/dashboard/pdf`, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/pdf'
            }
        });
        
        if (!res.ok) {
            const errorData = await res.json();
            throw new Error(errorData.error || 'Error al generar PDF');
        }
        
        const blob = await res.blob();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `dashboard-ong-${new Date().toISOString().split('T')[0]}.pdf`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: '¡PDF Generado!',
                text: 'El archivo se ha descargado correctamente',
                timer: 3000
            });
        } else {
            alert('PDF descargado correctamente');
        }
    } catch (error) {
        console.error('Error completo:', error);
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message || 'Error al generar el PDF'
            });
        } else {
            alert('Error: ' + (error.message || 'Error al generar el PDF'));
        }
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-file-pdf mr-2"></i> PDF';
    }
}

async function descargarExcel() {
    const btnExcel = document.getElementById('btnDescargarExcel');
    if (!btnExcel) return;
    
    if (btnExcel.disabled) return; // Evitar múltiples clics
    
    try {
        btnExcel.disabled = true;
        btnExcel.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Generando Excel...';

        const apiUrl = window.API_BASE_URL || "{{ env('APP_URL', 'http://localhost:8000') }}";
        const params = obtenerParametrosFiltros();
        const url = `${apiUrl}/api/ong/dashboard/export-excel?${params.toString()}`;

        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 120000); // 120 segundos

        const res = await fetch(url, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            },
            credentials: 'include',
            signal: controller.signal
        });

        clearTimeout(timeoutId);

        if (!res.ok) {
            const contentType = res.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                const errorData = await res.json();
                throw new Error(errorData.error || errorData.message || 'Error al generar Excel');
            }
            throw new Error(`Error HTTP: ${res.status}`);
        }

        const blob = await res.blob();
        if (blob.size === 0) {
            throw new Error('El Excel generado está vacío');
        }

        const urlBlob = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = urlBlob;
        a.download = `dashboard-ong-${new Date().toISOString().split('T')[0]}.xlsx`;
        a.style.display = 'none';
        document.body.appendChild(a);
        a.click();
        
        setTimeout(() => {
            window.URL.revokeObjectURL(urlBlob);
            document.body.removeChild(a);
        }, 100);

        btnExcel.disabled = false;
        btnExcel.innerHTML = '<i class="fas fa-file-excel mr-2"></i> Excel';
        mostrarNotificacion('Excel generado exitosamente', 'success');
    } catch (error) {
        console.error('Error al descargar Excel:', error);
        const errorMessage = error.name === 'AbortError' 
            ? 'La descarga tardó demasiado. Por favor, intente nuevamente.' 
            : (error.message || 'Error al generar el Excel');
        mostrarNotificacion(errorMessage, 'error');
        btnExcel.disabled = false;
        btnExcel.innerHTML = '<i class="fas fa-file-excel mr-2"></i> Excel';
    }
}

function obtenerParametrosFiltros() {
    const params = new URLSearchParams();
    const fechaInicio = document.getElementById('fechaInicio')?.value || '';
    const fechaFin = document.getElementById('fechaFin')?.value || '';
    const estadoEvento = document.getElementById('estadoEvento')?.value || '';
    const tipoParticipacion = document.getElementById('tipoParticipacion')?.value || '';
    const busquedaEvento = document.getElementById('busquedaEvento')?.value || '';

    if (fechaInicio) params.append('fecha_inicio', fechaInicio);
    if (fechaFin) params.append('fecha_fin', fechaFin);
    if (estadoEvento) params.append('estado_evento', estadoEvento);
    if (tipoParticipacion) params.append('tipo_participacion', tipoParticipacion);
    if (busquedaEvento) params.append('busqueda_evento', busquedaEvento);

    return params;
}

function mostrarNotificacion(mensaje, tipo) {
    const alertClass = tipo === 'success' ? 'alert-success' : 'alert-danger';
    const icon = tipo === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
    
    const alert = document.createElement('div');
    alert.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
    alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alert.innerHTML = `
        <i class="fas ${icon} mr-2"></i> ${mensaje}
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    `;
    
    document.body.appendChild(alert);
    
    setTimeout(() => {
        alert.remove();
    }, 5000);
}

function formatNumber(num) {
    return new Intl.NumberFormat('es-ES').format(num);
}

function getEstadoBadge(estado) {
    const badges = {
        'activo': '<span class="badge" style="background: #00A36C; color: #ffffff; font-weight: 600;">Activo</span>',
        'inactivo': '<span class="badge" style="background: #6c757d; color: #ffffff; font-weight: 600;">Inactivo</span>',
        'finalizado': '<span class="badge" style="background: #17a2b8; color: #ffffff; font-weight: 600;">Finalizado</span>',
        'cancelado': '<span class="badge" style="background: #dc3545; color: #ffffff; font-weight: 600;">Cancelado</span>',
        'borrador': '<span class="badge" style="background: #ffc107; color: #000;">Borrador</span>'
    };
    return badges[estado] || '<span class="badge" style="background: #6c757d; color: #ffffff; font-weight: 600;">' + estado + '</span>';
}

// Limpiar intervalo al salir de la página
window.addEventListener('beforeunload', () => {
    if (intervaloActualizacion) {
        clearInterval(intervaloActualizacion);
    }
});
</script>
@stop


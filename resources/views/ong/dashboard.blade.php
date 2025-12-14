@extends('layouts.adminlte')

@section('page_title', 'Dashboard General - ONG')

@section('content_body')
<div class="container-fluid">
    <!-- Header Minimalista -->
    <div class="dashboard-header mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div class="header-content">
                <h1 class="dashboard-title">
                    <i class="fas fa-chart-line mr-3"></i>
                        Dashboard General
                </h1>
                <p class="dashboard-subtitle">Panel centralizado de estadísticas y métricas</p>
                </div>
            <div class="header-actions">
                <button id="btnDescargarPDF" class="btn-pdf" onclick="descargarPDFDashboard()">
                    <i class="fas fa-file-pdf mr-2"></i>
                    <span>Exportar PDF</span>
                    </button>
            </div>
        </div>
    </div>

    <!-- Filtros Minimalistas -->
    <div class="filters-card mb-4">
        <div class="filters-header">
            <div class="d-flex align-items-center">
                <div class="filter-icon-wrapper">
                    <i class="fas fa-filter"></i>
        </div>
                <h5 class="filter-title mb-0">Filtros</h5>
                </div>
            <button class="filter-toggle" onclick="toggleFilters()">
                <i class="fas fa-chevron-up" id="filterToggleIcon"></i>
            </button>
                </div>
        <div class="filters-body" id="filtersBody">
            <div class="row g-3">
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <label class="filter-label">Fecha Inicio</label>
                    <input type="date" id="fechaInicio" class="form-control-modern" />
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <label class="filter-label">Fecha Fin</label>
                    <input type="date" id="fechaFin" class="form-control-modern" />
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <label class="filter-label">Estado</label>
                    <select id="estadoEvento" class="form-control-modern">
                        <option value="">Todos</option>
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                        <option value="finalizado">Finalizado</option>
                        <option value="cancelado">Cancelado</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <label class="filter-label">Tipo</label>
                    <select id="tipoParticipacion" class="form-control-modern">
                        <option value="">Todos</option>
                        <option value="voluntario">Voluntario</option>
                        <option value="asistente">Asistente</option>
                        <option value="colaborador">Colaborador</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <label class="filter-label">Buscar</label>
                    <input type="text" id="busquedaEvento" class="form-control-modern" placeholder="Nombre..." />
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6 d-flex align-items-end">
                    <div class="filter-buttons">
                        <button class="btn-filter-primary" onclick="aplicarFiltros()">
                            <i class="fas fa-search mr-2"></i>Aplicar
                    </button>
                        <button class="btn-filter-secondary" onclick="resetearFiltros()">
                            <i class="fas fa-redo mr-2"></i>Resetear
                    </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertas -->
    <div id="alertasContainer" class="mb-4"></div>

    <!-- Tarjetas de Métricas -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="info-box metric-card" data-animation="fadeInUp" data-delay="0.1s">
                <span class="info-box-icon bg-danger elevation-1">
                            <i class="fas fa-calendar-alt"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Eventos Activos</span>
                    <span class="info-box-number" id="totalEventosActivos">0</span>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="info-box metric-card" data-animation="fadeInUp" data-delay="0.2s">
                <span class="info-box-icon bg-success elevation-1">
                            <i class="fas fa-heart"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Reacciones</span>
                    <span class="info-box-number" id="totalReacciones">0</span>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="info-box metric-card" data-animation="fadeInUp" data-delay="0.3s">
                <span class="info-box-icon bg-info elevation-1">
                            <i class="fas fa-share-alt"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Compartidos</span>
                    <span class="info-box-number" id="totalCompartidos">0</span>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="info-box metric-card" data-animation="fadeInUp" data-delay="0.4s">
                <span class="info-box-icon bg-warning elevation-1">
                            <i class="fas fa-users"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Voluntarios</span>
                    <span class="info-box-number" id="totalVoluntarios">0</span>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="info-box metric-card" data-animation="fadeInUp" data-delay="0.5s">
                <span class="info-box-icon bg-success elevation-1">
                            <i class="fas fa-user-check"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Participantes</span>
                    <span class="info-box-number" id="totalParticipantes">0</span>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="info-box metric-card" data-animation="fadeInUp" data-delay="0.6s">
                <span class="info-box-icon bg-secondary elevation-1">
                            <i class="fas fa-check-circle"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Eventos Finalizados</span>
                    <span class="info-box-number" id="totalEventosFinalizados">0</span>
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
    /* Variables CSS */
    :root {
        --color-primary: #0C2B44;
        --color-success: #00A36C;
        --color-info: #17a2b8;
        --color-warning: #ffc107;
        --color-danger: #dc3545;
        --color-secondary: #6c757d;
        --color-proximos: #6366f1;
        --color-bg: #f8f9fa;
        --color-border: #e9ecef;
        --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.06);
        --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.08);
        --shadow-lg: 0 8px 24px rgba(0, 0, 0, 0.12);
        --radius: 12px;
        --radius-sm: 8px;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Header Minimalista */
    .dashboard-header {
        background: linear-gradient(135deg, var(--color-primary) 0%, #0a2338 100%);
        border-radius: var(--radius);
        padding: 2rem 2.5rem;
        box-shadow: var(--shadow-md);
        margin-bottom: 2rem;
        animation: fadeInDown 0.6s ease-out;
    }

    .dashboard-title {
        font-size: 2rem;
        font-weight: 700;
        color: #ffffff;
        margin: 0;
        letter-spacing: -0.5px;
        display: flex;
        align-items: center;
    }

    .dashboard-title i {
        color: var(--color-success);
        font-size: 1.8rem;
    }

    .dashboard-subtitle {
        color: rgba(255, 255, 255, 0.85);
        font-size: 0.95rem;
        margin: 0.5rem 0 0 3rem;
        font-weight: 400;
    }

    .btn-pdf {
        background: var(--color-danger);
        color: #ffffff;
        border: none;
        border-radius: var(--radius-sm);
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        font-size: 0.95rem;
        transition: var(--transition);
        display: flex;
        align-items: center;
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
    }

    .btn-pdf:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
        background: #c82333;
    }

    /* Filtros Minimalistas */
    .filters-card {
        background: #ffffff;
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--color-border);
        overflow: hidden;
        transition: var(--transition);
        animation: fadeInUp 0.6s ease-out 0.1s both;
    }

    .filters-card:hover {
        box-shadow: var(--shadow-md);
    }

    .filters-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        padding: 1.25rem 2rem;
        border-bottom: 1px solid var(--color-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .filter-icon-wrapper {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, var(--color-success) 0%, #008a5a 100%);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        color: #ffffff;
        font-size: 1.1rem;
        box-shadow: 0 4px 12px rgba(0, 163, 108, 0.2);
    }

    .filter-title {
        color: var(--color-primary);
        font-weight: 700;
        font-size: 1.1rem;
        letter-spacing: -0.3px;
    }

    .filter-toggle {
        background: transparent;
        border: 2px solid var(--color-border);
        border-radius: 8px;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--color-primary);
        transition: var(--transition);
        cursor: pointer;
    }

    .filter-toggle:hover {
        border-color: var(--color-success);
        color: var(--color-success);
        transform: scale(1.1);
    }

    .filters-body {
        padding: 2rem;
        transition: all 0.4s ease;
    }

    .filter-label {
        display: block;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--color-primary);
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-control-modern {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid var(--color-border);
        border-radius: var(--radius-sm);
        font-size: 0.95rem;
        transition: var(--transition);
        background: #ffffff;
        color: var(--color-primary);
    }

    .form-control-modern:focus {
        outline: none;
        border-color: var(--color-success);
        box-shadow: 0 0 0 4px rgba(0, 163, 108, 0.1);
        transform: translateY(-1px);
    }

    .filter-buttons {
        display: flex;
        gap: 0.75rem;
        width: 100%;
    }

    .btn-filter-primary {
        flex: 1;
        background: var(--color-primary);
        color: #ffffff;
        border: none;
        border-radius: var(--radius-sm);
        padding: 0.75rem 1.25rem;
        font-weight: 600;
        font-size: 0.9rem;
        transition: var(--transition);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-filter-primary:hover {
        background: #0a2338;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(12, 43, 68, 0.3);
    }

    .btn-filter-secondary {
        flex: 1;
        background: var(--color-secondary);
        color: #ffffff;
        border: none;
        border-radius: var(--radius-sm);
        padding: 0.75rem 1.25rem;
        font-weight: 600;
        font-size: 0.9rem;
        transition: var(--transition);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-filter-secondary:hover {
        background: #5a6268;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
    }
</style>
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

    /* Animaciones Mejoradas */
    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(40px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes slideInLeft {
        from {
            opacity: 0;
            transform: translateX(-40px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
            opacity: 1;
        }
        50% {
            transform: scale(1.05);
            opacity: 0.9;
        }
    }

    @keyframes shimmer {
        0% {
            background-position: -1000px 0;
        }
        100% {
            background-position: 1000px 0;
        }
    }

    @keyframes bounceIn {
        0% {
            opacity: 0;
            transform: scale(0.3);
        }
        50% {
            opacity: 1;
            transform: scale(1.05);
        }
        70% {
            transform: scale(0.9);
        }
        100% {
            transform: scale(1);
        }
    }

    @keyframes float {
        0%, 100% {
            transform: translateY(0px);
        }
        50% {
            transform: translateY(-10px);
        }
    }

    /* Tarjetas de métricas mejoradas */
    .metric-card {
        opacity: 0;
        animation-fill-mode: forwards;
        transition: var(--transition);
        transform-style: preserve-3d;
    }

    .metric-card[data-animation="fadeInUp"] {
        animation: fadeInUp 0.7s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    .metric-card[data-delay="0.1s"] { animation-delay: 0.1s; }
    .metric-card[data-delay="0.2s"] { animation-delay: 0.2s; }
    .metric-card[data-delay="0.3s"] { animation-delay: 0.3s; }
    .metric-card[data-delay="0.4s"] { animation-delay: 0.4s; }
    .metric-card[data-delay="0.5s"] { animation-delay: 0.5s; }
    .metric-card[data-delay="0.6s"] { animation-delay: 0.6s; }

    .metric-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: var(--shadow-lg) !important;
    }

    /* Info Box estilo AdminLTE mejorado */
    .info-box {
        display: flex;
        min-height: 110px;
        background: #ffffff;
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        transition: var(--transition);
        border: 1px solid var(--color-border);
        overflow: hidden;
        position: relative;
    }

    .info-box::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: var(--color-success);
        transform: scaleY(0);
        transition: transform 0.3s ease;
    }

    .info-box:hover::before {
        transform: scaleY(1);
    }

    .info-box:hover {
        box-shadow: var(--shadow-md);
        border-color: var(--color-success);
    }

    .info-box-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100px;
        font-size: 2.2rem;
        color: #ffffff;
        transition: var(--transition);
        position: relative;
    }

    .info-box-icon::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }

    .info-box:hover .info-box-icon::after {
        width: 200px;
        height: 200px;
    }

    .info-box:hover .info-box-icon {
        transform: scale(1.15) rotate(5deg);
    }

    .info-box-content {
        flex: 1;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
        position: relative;
        z-index: 1;
    }

    .info-box-text {
        display: block;
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--color-secondary);
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 0.75rem;
        opacity: 0.8;
    }

    .info-box-number {
        display: block;
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--color-primary);
        line-height: 1;
        transition: var(--transition);
        letter-spacing: -1px;
    }

    .info-box:hover .info-box-number {
        color: var(--color-success);
        transform: scale(1.05);
    }

    /* Cards de gráficos mejoradas */
    .card {
        opacity: 0;
        animation: fadeIn 1s ease-out 0.4s forwards;
        transition: var(--transition);
        border-radius: var(--radius);
        border: 1px solid var(--color-border);
        overflow: hidden;
        background: #ffffff;
        box-shadow: var(--shadow-sm);
        position: relative;
    }

    .card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--color-success), var(--color-info));
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .card:hover::before {
        transform: scaleX(1);
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg) !important;
        border-color: var(--color-success);
    }

    /* Alertas mejoradas con colores específicos */
    .alert {
        border-radius: 12px;
        border-left: 5px solid;
        border: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        padding: 1.25rem 1.5rem;
        margin-bottom: 1rem;
        animation: slideInRight 0.5s ease-out;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .alert::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        width: 5px;
        height: 100%;
        background: inherit;
        border-left-color: inherit;
    }

    .alert:hover {
        transform: translateX(5px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
    }

    .alert-danger {
        border-left-color: #dc3545;
        background: linear-gradient(135deg, #fff5f5 0%, #ffe0e0 100%);
        color: #721c24;
    }

    .alert-warning {
        border-left-color: #ffc107;
        background: linear-gradient(135deg, #fffbf0 0%, #fff3cd 100%);
        color: #856404;
    }

    /* Color especial para eventos próximos - Índigo/Violeta */
    .alert-proximos {
        border-left-color: var(--color-proximos);
        background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
        color: #4338ca;
        animation: slideInRight 0.6s ease-out, pulse 3s ease-in-out infinite 1.5s;
        box-shadow: 0 4px 16px rgba(99, 102, 241, 0.2);
    }

    .alert-proximos:hover {
        box-shadow: 0 6px 24px rgba(99, 102, 241, 0.3);
        transform: translateX(8px);
    }

    .alert-proximos i {
        color: var(--color-proximos);
        animation: float 3s ease-in-out infinite;
    }

    .alert-proximos::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.1) 0%, transparent 70%);
        border-radius: 50%;
        transform: translate(30%, -30%);
        pointer-events: none;
    }

    .alert-info {
        border-left-color: #17a2b8;
        background: linear-gradient(135deg, #f0f9fa 0%, #d1ecf1 100%);
        color: #0c5460;
    }

    .alert-success {
        border-left-color: #00A36C;
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        color: #166534;
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

    .table-hover tbody tr {
        transition: all 0.2s ease;
    }

    .table-hover tbody tr:hover {
        background-color: #f0f7ff;
        transform: scale(1.01);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    /* Animación para números que cambian */
    @keyframes numberUpdate {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.1);
            color: #00A36C;
        }
        100% {
            transform: scale(1);
        }
    }

    .info-box-number.updating {
        animation: numberUpdate 0.6s ease-out;
    }

    /* Botones mejorados */
    .btn {
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        padding: 0.5rem 1.25rem;
        position: relative;
        overflow: hidden;
    }

    .btn::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }

    .btn:hover::before {
        width: 300px;
        height: 300px;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
    }

    .btn:active {
        transform: translateY(0);
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

    /* Mejoras adicionales AdminLTE */
    .card-header {
        font-weight: 600;
        letter-spacing: 0.3px;
    }

    /* Efecto de carga suave */
    @keyframes shimmer {
        0% {
            background-position: -1000px 0;
        }
        100% {
            background-position: 1000px 0;
        }
    }

    .loading-shimmer {
        animation: shimmer 2s infinite linear;
        background: linear-gradient(
            to right,
            #f6f7f8 0%,
            #edeef1 20%,
            #f6f7f8 40%,
            #f6f7f8 100%
        );
        background-size: 1000px 100%;
    }

    /* Header mejorado */
    .card-header.bg-primary {
        background: linear-gradient(135deg, #0C2B44 0%, #0a2338 100%) !important;
    }

    /* Mejoras en filtros */
    .card-header[style*="background: #f8f9fa"] {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%) !important;
    }

    /* Scrollbar personalizado */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb {
        background: #00A36C;
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #008a5a;
    }

    /* Transiciones globales */
    * {
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Loading spinner mejorado */
    .spinner-border {
        width: 3rem;
        height: 3rem;
        border-width: 0.3em;
        color: #00A36C;
    }

    /* Mejoras en badges */
    .badge {
        padding: 0.4rem 0.8rem;
        font-weight: 600;
        letter-spacing: 0.3px;
        transition: all 0.3s ease;
    }

    .badge:hover {
        transform: scale(1.05);
    }

    /* Responsive mejorado */
    @media (max-width: 768px) {
        .dashboard-header {
            padding: 1.5rem;
        }

        .dashboard-title {
            font-size: 1.5rem;
        }

        .dashboard-subtitle {
            font-size: 0.85rem;
            margin-left: 0;
        }

        .info-box {
            min-height: 100px;
        }

        .info-box-icon {
            width: 80px;
            font-size: 1.8rem;
        }

        .info-box-number {
            font-size: 2rem;
        }

        .filters-body {
            padding: 1.5rem;
        }

        .filter-buttons {
            flex-direction: column;
        }
    }

    /* Mejoras adicionales de accesibilidad */
    .btn:focus,
    .form-control-modern:focus,
    .filter-toggle:focus {
        outline: 3px solid rgba(0, 163, 108, 0.3);
        outline-offset: 2px;
    }

    /* Smooth scrolling */
    html {
        scroll-behavior: smooth;
    }

    /* Loading state mejorado */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.95);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        animation: fadeIn 0.3s ease;
    }

    .loading-spinner {
        width: 50px;
        height: 50px;
        border: 4px solid var(--color-border);
        border-top-color: var(--color-success);
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
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
    const elementos = [
        { id: 'totalEventosActivos', valor: metricas?.eventos_activos || 0 },
        { id: 'totalReacciones', valor: formatNumber(metricas?.total_reacciones || 0) },
        { id: 'totalCompartidos', valor: formatNumber(metricas?.total_compartidos || 0) },
        { id: 'totalVoluntarios', valor: formatNumber(metricas?.total_voluntarios || 0) },
        { id: 'totalParticipantes', valor: formatNumber(metricas?.total_participantes || 0) },
        { id: 'totalEventosFinalizados', valor: metricas?.eventos_finalizados || 0 }
    ];

    elementos.forEach(({ id, valor }) => {
        const elemento = document.getElementById(id);
        if (elemento) {
            // Agregar clase de animación
            elemento.classList.add('updating');
            elemento.textContent = valor;
            
            // Remover clase después de la animación
            setTimeout(() => {
                elemento.classList.remove('updating');
            }, 600);
        }
    });
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

    console.log('📊 Actualizando tabla de eventos. Total:', eventos.length);
    if (eventos.length > 0) {
        console.log('📊 Primer evento (ejemplo) - Estructura completa:', eventos[0]);
        console.log('📊 Campos disponibles en primer evento:', Object.keys(eventos[0]));
    }
    
    // ERROR #4 CORREGIDO: Agregar manejo de errores con fallback visual
    try {

    // Separar eventos por tipo
    const eventosRegulares = eventos.filter(e => e.tipo === 'evento' || !e.tipo);
    const megaEventos = eventos.filter(e => e.tipo === 'mega_evento');
    
    console.log('📊 Eventos regulares:', eventosRegulares.length);
    console.log('📊 Mega eventos:', megaEventos.length);
    
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
        
        eventosRegulares.forEach((evento, index) => {
            // ERROR #1 CORREGIDO: Intentar obtener el ID de múltiples campos posibles
            let eventoId = null;
            
            // Intentar obtener el ID de diferentes campos posibles (prioridad: id > evento_id > id_evento)
            if (evento.id !== undefined && evento.id !== null && evento.id !== '') {
                eventoId = parseInt(evento.id, 10);
            } else if (evento.evento_id !== undefined && evento.evento_id !== null && evento.evento_id !== '') {
                eventoId = parseInt(evento.evento_id, 10);
            } else if (evento.id_evento !== undefined && evento.id_evento !== null && evento.id_evento !== '') {
                eventoId = parseInt(evento.id_evento, 10);
            }
            
            // ERROR #2 CORREGIDO: No hacer return, mostrar evento con mensaje de error si no hay ID
            if (!eventoId || isNaN(eventoId) || eventoId <= 0) {
                console.error(`❌ ERROR #1: Evento ${index} con ID inválido o faltante:`, {
                    evento: evento,
                    id: evento.id,
                    evento_id: evento.evento_id,
                    id_evento: evento.id_evento,
                    titulo: evento.titulo
                });
                
                // ERROR #4 CORREGIDO: Mostrar fallback visual en lugar de omitir
                html += `
                    <tr style="background: #fff3cd !important;">
                        <td><span class="badge" style="background: #dc3545; color: #ffffff;">Error</span></td>
                        <td style="font-weight: 700; color: #dc3545;" title="ID de evento inválido o faltante">${evento.titulo || 'Sin título'} <small>(ID inválido)</small></td>
                        <td colspan="4" style="color: #dc3545; font-style: italic;">⚠️ No se pudo cargar la información del evento</td>
                        <td class="text-center">
                            <span class="badge badge-warning" style="padding: 0.4rem 0.9rem;">N/A</span>
                        </td>
                    </tr>
                `;
                return; // Continuar con el siguiente evento
            }
            
            console.log(`✅ Evento ${index} procesado correctamente - ID: ${eventoId}, Título: ${evento.titulo}`);
            
            const fechaInicio = evento.fecha_inicio ? new Date(evento.fecha_inicio).toLocaleDateString('es-ES') : 'N/A';
            const estadoBadge = getEstadoBadge(evento.estado || evento.estado_dinamico || 'activo');
            
            // ERROR #3 CORREGIDO: Ruta correcta verificada contra routes/web.php (línea 70: /ong/eventos/{id}/dashboard)
            const rutaDashboard = `/ong/eventos/${eventoId}/dashboard`;
            
            html += `
                <tr>
                    <td><span class="badge" style="background: #17a2b8; color: #ffffff; font-size: 0.9rem; font-weight: 700; padding: 0.5rem 0.75rem;">Evento</span></td>
                    <td style="font-weight: 700; color: #0C2B44; font-size: 1.1rem;">${evento.titulo || 'Sin título'}</td>
                    <td style="color: #6c757d; font-size: 1rem; font-weight: 500;">${fechaInicio}</td>
                    <td style="color: #6c757d; font-size: 1rem; font-weight: 500;">${evento.ubicacion || 'N/A'}</td>
                    <td class="text-center"><span class="badge" style="background: #17a2b8; color: #ffffff; font-size: 1rem; font-weight: 700; padding: 0.5rem 0.75rem;">${formatNumber(evento.total_participantes || 0)}</span></td>
                    <td>${estadoBadge}</td>
                    <td class="text-center">
                        <a href="/ong/eventos/${eventoId}/dashboard" 
                           class="btn btn-sm btn-dashboard-event" 
                           title="Ver Estadísticas del Evento: ${(evento.titulo || 'Sin título').substring(0, 50)}" 
                           data-event-id="${eventoId}"
                           data-evento-titulo="${(evento.titulo || '').replace(/"/g, '&quot;')}"
                           onclick="event.preventDefault(); event.stopPropagation(); const eventId = parseInt('${eventoId}', 10); console.log('🔗🔗🔗 NAVEGANDO al dashboard del evento #' + eventId + ' desde botón de acciones'); if (!isNaN(eventId) && eventId > 0) { const ruta = '/ong/eventos/' + eventId + '/dashboard'; console.log('📍 Ruta final:', ruta); window.location.href = ruta; } else { console.error('❌ ID inválido:', eventId); alert('Error: ID de evento inválido: ' + eventId); } return false;"
                           style="background: #0C2B44; border: none; color: #ffffff; padding: 0.4rem 0.9rem; font-weight: 600; border-radius: 8px; transition: all 0.3s ease; cursor: pointer; text-decoration: none; display: inline-block;">
                            <i class="fas fa-chart-bar mr-1"></i> Estadísticas
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
    
    // ERROR #5 CORREGIDO: Prevenir listeners duplicados usando una bandera global
    if (window.dashboardListenersConfigurados) {
        // Limpiar listeners anteriores si ya fueron configurados
        const botonesAntiguos = document.querySelectorAll('.btn-dashboard-event');
        botonesAntiguos.forEach(btn => {
            const nuevoBtn = btn.cloneNode(true);
            btn.parentNode.replaceChild(nuevoBtn, btn);
        });
    }
    
    // Agregar listeners a los botones de dashboard después de renderizar
    setTimeout(() => {
        const botones = document.querySelectorAll('.btn-dashboard-event');
        console.log(`🔧 Configurando ${botones.length} botones de dashboard...`);
        
        botones.forEach((btn, index) => {
            // ERROR #5 CORREGIDO: Verificar si el botón ya tiene listener antes de agregar uno nuevo
            if (btn.hasAttribute('data-listener-added')) {
                return; // Ya tiene listener, saltar
            }
            
            // Marcar que este botón ya tiene listener
            btn.setAttribute('data-listener-added', 'true');
            
            // Agregar listener al botón
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const eventId = this.getAttribute('data-event-id');
                const href = this.getAttribute('href');
                const eventoTitulo = this.getAttribute('data-evento-titulo') || 'Evento';
                
                console.log(`🔍 Clic en botón Dashboard #${index + 1}:`, {
                    eventId: eventId,
                    href: href,
                    eventoTitulo: eventoTitulo,
                    eventIdType: typeof eventId
                });
                
                if (eventId) {
                    // Verificar que el ID sea válido antes de navegar
                    const idNum = parseInt(eventId, 10);
                    if (!isNaN(idNum) && idNum > 0) {
                        // Construir la ruta correcta
                        const ruta = `/ong/eventos/${idNum}/dashboard`;
                        console.log('✅ ID válido:', idNum);
                        console.log('✅ Ruta construida:', ruta);
                        console.log(`✅ Navegando al dashboard del evento: "${eventoTitulo}" (ID: ${idNum})`);
                        
                        // Navegar a la ruta
                        window.location.href = ruta;
                    } else {
                        console.error('❌ ID de evento inválido:', eventId);
                        const mensaje = `ID de evento inválido: ${eventId}`;
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: mensaje
                            });
                        } else {
                            alert('Error: ' + mensaje);
                        }
                    }
                } else {
                    console.error('❌ No se encontró data-event-id en el botón');
                    console.error('❌ Elemento HTML:', this);
                    console.error('❌ HREF disponible:', href);
                    
                    // Intentar extraer el ID del href si está disponible
                    if (href) {
                        const match = href.match(/\/eventos\/(\d+)\/dashboard/);
                        if (match && match[1]) {
                            const idExtraido = parseInt(match[1], 10);
                            console.log('✅ ID extraído del href:', idExtraido);
                            window.location.href = `/ong/eventos/${idExtraido}/dashboard`;
                            return;
                        }
                    }
                    
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudo identificar el evento. Por favor, intenta de nuevo.'
                        });
                    } else {
                        alert('Error: No se pudo identificar el evento.');
                    }
                }
            });
            
            console.log(`✅ Botón #${index + 1} configurado - Evento ID: ${btn.getAttribute('data-event-id')}`);
        });
        
        console.log(`✅ ${document.querySelectorAll('.btn-dashboard-event').length} botones de dashboard configurados correctamente`);
        
        // Marcar que los listeners ya fueron configurados
        window.dashboardListenersConfigurados = true;
    }, 300);
    
    } catch (error) {
        // ERROR #4 CORREGIDO: Mostrar error visual en la tabla si algo falla
        console.error('❌ ERROR al actualizar tabla de eventos:', error);
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-4" style="background: #f8d7da; color: #721c24;">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>Error al cargar eventos:</strong> ${error.message || 'Error desconocido'}
                    <br><small>Por favor, recarga la página o contacta al soporte.</small>
                </td>
            </tr>
        `;
        
        // Mostrar notificación con SweetAlert si está disponible
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error al Cargar Eventos',
                text: error.message || 'Ocurrió un error al cargar la tabla de eventos. Por favor, intenta recargar la página.',
                confirmButtonText: 'Reintentar',
                showCancelButton: true,
                cancelButtonText: 'Cerrar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Reintentar cargar el dashboard
                    if (typeof cargarDashboard === 'function') {
                        cargarDashboard();
                    } else {
                        location.reload();
                    }
                }
            });
        }
    }
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
    alertas.forEach((alerta, index) => {
        // Detectar si es alerta de eventos próximos (mejorado)
        const mensajeLower = (alerta.mensaje || '').toLowerCase();
        const esEventosProximos = mensajeLower.includes('próximo') ||
                                  mensajeLower.includes('proximo') ||
                                  mensajeLower.includes('inicia pronto') ||
                                  mensajeLower.includes('iniciar') ||
                                  mensajeLower.includes('menos de') ||
                                  alerta.tipo === 'eventos_proximos' ||
                                  alerta.tipo === 'eventos_proximo';

        let alertClass = '';
        let icon = 'fa-info-circle';
        
        if (esEventosProximos) {
            // Color especial índigo/violeta para eventos próximos
            alertClass = 'alert-proximos';
            icon = 'fa-calendar-day';
        } else {
            alertClass = alerta.severidad === 'danger' ? 'alert-danger' : 
                        alerta.severidad === 'warning' ? 'alert-warning' : 
                        alerta.severidad === 'success' ? 'alert-success' : 'alert-info';
            icon = alerta.severidad === 'danger' ? 'fa-exclamation-circle' :
                   alerta.severidad === 'warning' ? 'fa-exclamation-triangle' :
                   alerta.severidad === 'success' ? 'fa-check-circle' : 'fa-info-circle';
        }
        
        html += `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert" style="animation-delay: ${index * 0.15}s;">
                <div class="d-flex align-items-center">
                    <i class="fas ${icon} mr-3" style="font-size: 1.5rem; flex-shrink: 0;"></i>
                    <div class="flex-grow-1">
                        <strong style="font-size: 1rem; line-height: 1.5;">${alerta.mensaje}</strong>
                    </div>
                    <button type="button" class="close ml-3" data-dismiss="alert" aria-label="Close" style="flex-shrink: 0; opacity: 0.7; transition: opacity 0.3s;">
                        <span aria-hidden="true" style="font-size: 1.5rem;">&times;</span>
                </button>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

// Función para toggle de filtros
function toggleFilters() {
    const filtersBody = document.getElementById('filtersBody');
    const toggleIcon = document.getElementById('filterToggleIcon');
    
    if (filtersBody && toggleIcon) {
        const isVisible = filtersBody.style.display !== 'none';
        
        if (isVisible) {
            filtersBody.style.display = 'none';
            toggleIcon.classList.remove('fa-chevron-up');
            toggleIcon.classList.add('fa-chevron-down');
        } else {
            filtersBody.style.display = 'block';
            toggleIcon.classList.remove('fa-chevron-down');
            toggleIcon.classList.add('fa-chevron-up');
        }
    }
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
        const apiUrl = window.API_BASE_URL || 'http://192.168.0.7:8000';
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


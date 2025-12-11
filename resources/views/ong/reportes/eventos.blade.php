@extends('layouts.adminlte')

@section('page_title', 'Reporte: Eventos Regulares')

@section('content_body')
<link rel="stylesheet" href="{{ asset('css/reportes-ong.css') }}">
<div class="reportes-container">
    <!-- Header -->
    <div class="reportes-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="reportes-title">Reporte de Eventos Regulares</h1>
                <p class="reportes-subtitle">Análisis detallado de eventos regulares con métricas y visualizaciones</p>
            </div>
            <a href="{{ route('ong.reportes.index') }}" class="btn btn-outline">
                <i data-feather="arrow-left" style="width: 18px; height: 18px; margin-right: 8px;"></i>
                Volver
            </a>
        </div>
    </div>

    <!-- Filtros -->
    @include('ong.reportes.components.filter-form', [
        'tipoReporte' => 'eventos',
        'route' => route('ong.reportes.eventos')
    ])

    <!-- Botones de Exportación -->
    @include('ong.reportes.components.export-buttons', [
        'tipoReporte' => 'eventos'
    ])

    <!-- KPIs Principales -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            @include('ong.reportes.components.metric-card', [
                'titulo' => 'Total Eventos',
                'valor' => '<span id="totalEventos">0</span>',
                'icono' => 'calendar',
                'color' => 'primary'
            ])
        </div>
        <div class="col-md-3 mb-3">
            @include('ong.reportes.components.metric-card', [
                'titulo' => 'Total Participantes',
                'valor' => '<span id="totalParticipantes">0</span>',
                'icono' => 'users',
                'color' => 'success'
            ])
        </div>
        <div class="col-md-3 mb-3">
            @include('ong.reportes.components.metric-card', [
                'titulo' => 'Eventos Activos',
                'valor' => '<span id="eventosActivos">0</span>',
                'icono' => 'activity',
                'color' => 'info'
            ])
        </div>
        <div class="col-md-3 mb-3">
            @include('ong.reportes.components.metric-card', [
                'titulo' => 'Promedio Participantes',
                'valor' => '<span id="promedioParticipantes">0</span>',
                'icono' => 'trending-up',
                'color' => 'warning'
            ])
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            @include('ong.reportes.components.chart-container', [
                'titulo' => 'Distribución por Categoría',
                'id' => 'graficoCategoria',
                'tipo' => 'bar'
            ])
        </div>
        <div class="col-md-6 mb-3">
            @include('ong.reportes.components.chart-container', [
                'titulo' => 'Distribución por Estado',
                'id' => 'graficoEstado',
                'tipo' => 'doughnut'
            ])
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12 mb-3">
            @include('ong.reportes.components.chart-container', [
                'titulo' => 'Tendencias Temporales',
                'id' => 'graficoTendencias',
                'tipo' => 'line'
            ])
        </div>
    </div>

    <!-- Top 5 Eventos -->
    <div class="card metric-card mb-4">
        <div class="card-header" style="background: var(--color-secondary); color: white; padding: 15px 20px;">
            <h5 class="mb-0" style="font-weight: 600;">
                <i data-feather="award" style="width: 20px; height: 20px; margin-right: 10px;"></i>
                Top 5 Eventos por Participantes
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="data-table" id="tablaTopEventos">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th class="text-center">Participantes</th>
                            <th class="text-center">Capacidad</th>
                            <th class="text-center">Tasa Ocupación</th>
                            <th class="text-center">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i data-feather="loader" class="spinning" style="width: 20px; height: 20px; margin-right: 10px;"></i>
                                Cargando datos...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="{{ asset('assets/js/config.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="https://unpkg.com/feather-icons"></script>
<script src="{{ asset('js/reportes-ong.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
    initReportesEventos();
});
</script>
@endpush


@extends('layouts.adminlte')

@section('page_title', 'Reporte: Mega Eventos')

@section('content_body')
<link rel="stylesheet" href="{{ asset('css/reportes-ong.css') }}">
<div class="reportes-container">
    <div class="reportes-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="reportes-title">Reporte de Mega Eventos</h1>
                <p class="reportes-subtitle">Análisis detallado de mega eventos con métricas y visualizaciones</p>
            </div>
            <a href="{{ route('ong.reportes.index') }}" class="btn btn-outline">
                <i data-feather="arrow-left" style="width: 18px; height: 18px; margin-right: 8px;"></i>
                Volver
            </a>
        </div>
    </div>

    @include('ong.reportes.components.filter-form', [
        'tipoReporte' => 'mega-eventos',
        'route' => route('ong.reportes.mega-eventos')
    ])

    @include('ong.reportes.components.export-buttons', [
        'tipoReporte' => 'mega-eventos'
    ])

    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            @include('ong.reportes.components.metric-card', [
                'titulo' => 'Total Mega Eventos',
                'valor' => '<span id="totalMegaEventos">0</span>',
                'icono' => 'star',
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
                'titulo' => 'Mega Eventos Activos',
                'valor' => '<span id="megaEventosActivos">0</span>',
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
    initReportesMegaEventos();
});

function initReportesMegaEventos() {
    applyFilters('mega-eventos');
}
</script>
@endpush


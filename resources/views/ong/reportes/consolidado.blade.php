@extends('layouts.adminlte')

@section('page_title', 'Reporte: Consolidado')

@section('content_body')
<link rel="stylesheet" href="{{ asset('css/reportes-ong.css') }}">
<div class="reportes-container">
    <div class="reportes-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="reportes-title">Reporte Consolidado</h1>
                <p class="reportes-subtitle">Análisis combinado de eventos regulares y mega eventos</p>
            </div>
            <a href="{{ route('ong.reportes.index') }}" class="btn btn-outline">
                <i data-feather="arrow-left" style="width: 18px; height: 18px; margin-right: 8px;"></i>
                Volver
            </a>
        </div>
    </div>

    @include('ong.reportes.components.filter-form', [
        'tipoReporte' => 'consolidado',
        'route' => route('ong.reportes.consolidado')
    ])

    @include('ong.reportes.components.export-buttons', [
        'tipoReporte' => 'consolidado'
    ])

    <!-- KPIs Consolidados -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            @include('ong.reportes.components.metric-card', [
                'titulo' => 'Total Participantes General',
                'valor' => '<span id="totalParticipantesGeneral">0</span>',
                'icono' => 'users',
                'color' => 'primary'
            ])
        </div>
        <div class="col-md-4 mb-3">
            @include('ong.reportes.components.metric-card', [
                'titulo' => 'Total Eventos General',
                'valor' => '<span id="totalEventosGeneral">0</span>',
                'icono' => 'calendar',
                'color' => 'success'
            ])
        </div>
        <div class="col-md-4 mb-3">
            @include('ong.reportes.components.metric-card', [
                'titulo' => 'Mejor Rendimiento',
                'valor' => '<span id="mejorRendimiento">-</span>',
                'icono' => 'award',
                'color' => 'warning'
            ])
        </div>
    </div>

    <!-- Comparativa -->
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            @include('ong.reportes.components.chart-container', [
                'titulo' => 'Comparativa: Eventos vs Mega Eventos',
                'id' => 'graficoComparativa',
                'tipo' => 'bar'
            ])
        </div>
        <div class="col-md-6 mb-3">
            @include('ong.reportes.components.chart-container', [
                'titulo' => 'Distribución Porcentual',
                'id' => 'graficoDistribucion',
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
    initReportesConsolidado();
});

function initReportesConsolidado() {
    applyFilters('consolidado');
}
</script>
@endpush


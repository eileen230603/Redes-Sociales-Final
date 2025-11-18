@extends('layouts.adminlte-empresa')

@section('page_title', 'Reportes')

@section('content_body')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-primary"><i class="fas fa-chart-bar"></i> Reportes</h4>
</div>

<div class="card">
    <div class="card-header bg-primary">
        <h5 class="card-title mb-0">Reportes de Empresa</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <h5><i class="fas fa-info-circle"></i> Próximamente</h5>
            <p class="mb-0">Los reportes de eventos patrocinados, impacto y estadísticas estarán disponibles próximamente.</p>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-body">
                        <h6><i class="fas fa-calendar-check"></i> Eventos Patrocinados</h6>
                        <p class="text-muted">Visualiza estadísticas de los eventos que patrocinas.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-body">
                        <h6><i class="fas fa-chart-line"></i> Impacto Social</h6>
                        <p class="text-muted">Mide el impacto de tus patrocinios en la comunidad.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@stop


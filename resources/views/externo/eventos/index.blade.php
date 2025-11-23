@extends('layouts.adminlte-externo')

@section('page_title', 'Eventos Disponibles')

@section('content_body')

<!-- Header con diseño mejorado - Colores AdminLTE -->
<div class="card mb-4 shadow-sm" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); border: none; border-radius: 15px; overflow: hidden;">
    <div class="card-body py-4 px-4">
        <div class="row align-items-center">
            <div class="col-md-10">
                <div class="d-flex align-items-center">
                    <div class="bg-white rounded-circle p-3 mr-3 shadow-sm" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-calendar-check" style="font-size: 1.8rem; color: #17a2b8;"></i>
                    </div>
                    <div>
                        <h3 class="text-white mb-1" style="font-weight: 700; font-size: 1.75rem;">
                            Eventos Disponibles
                        </h3>
                        <p class="text-white mb-0" style="opacity: 0.95; font-size: 1rem;">
                            Descubre oportunidades para participar y colaborar
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-2 text-right d-none d-md-block">
                <i class="fas fa-calendar-alt" style="font-size: 4.5rem; color: rgba(255,255,255,0.15);"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filtros y Búsqueda con diseño mejorado -->
<div class="card mb-4 shadow-sm" style="border-radius: 10px; border: none;">
    <div class="card-header bg-white border-0" style="border-radius: 10px 10px 0 0;">
        <h5 class="mb-0" style="color: #495057; font-weight: 600;">
            <i class="fas fa-sliders-h mr-2" style="color: #17a2b8;"></i>Filtros de Búsqueda
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 mb-3 mb-md-0">
                <label for="filtroTipo" class="form-label font-weight-bold text-secondary" style="font-size: 0.875rem;">
                    <i class="fas fa-filter mr-1" style="color: #17a2b8;"></i>Tipo de Evento
                </label>
                <select id="filtroTipo" class="form-control" style="border-radius: 8px; border: 2px solid #e9ecef;">
                    <option value="todos">Todos los tipos</option>
                    <option value="cultural">Cultural</option>
                    <option value="deportivo">Deportivo</option>
                    <option value="educativo">Educativo</option>
                    <option value="social">Social</option>
                    <option value="benefico">Benéfico</option>
                    <option value="otro">Otro</option>
                </select>
            </div>
            <div class="col-md-8">
                <label for="buscador" class="form-label font-weight-bold text-secondary" style="font-size: 0.875rem;">
                    <i class="fas fa-search mr-1" style="color: #17a2b8;"></i>Buscar Eventos
                </label>
                <div class="input-group">
                    <input type="text" id="buscador" class="form-control" placeholder="Buscar por título o descripción..." 
                           style="border-radius: 8px 0 0 8px; border: 2px solid #e9ecef; border-right: none;">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" id="btnLimpiar" 
                                style="border-radius: 0 8px 8px 0; border: 2px solid #e9ecef; border-left: none;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Listado de Eventos -->
<div class="row" id="listaEventos">
    <div class="col-12 text-center py-5">
        <div class="spinner-border text-info" role="status" style="width: 3rem; height: 3rem;">
            <span class="sr-only">Cargando...</span>
        </div>
        <p class="text-muted mt-3">Cargando eventos disponibles...</p>
    </div>
</div>

@stop

@section('css')
<style>
    /* Estilos mejorados para eventos en los que el usuario está inscrito */
    .evento-inscrito {
        position: relative;
        border-radius: 10px;
        overflow: hidden;
    }
    
    .evento-inscrito::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(90deg, #28a745 0%, #20c997 100%);
        z-index: 1;
    }
    
    .evento-inscrito .card-body {
        background: linear-gradient(to bottom, rgba(40, 167, 69, 0.05) 0%, rgba(248, 249, 250, 1) 15%);
    }
    
    .evento-inscrito:hover {
        box-shadow: 0 10px 25px rgba(40, 167, 69, 0.25) !important;
        transform: translateY(-3px);
        transition: all 0.3s ease;
    }

    /* Mejoras para las tarjetas de eventos */
    .card {
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
    }

    /* Estilos para los inputs */
    .form-control:focus {
        border-color: #17a2b8;
        box-shadow: 0 0 0 0.2rem rgba(23, 162, 184, 0.15);
    }

    select.form-control:focus {
        border-color: #17a2b8;
        box-shadow: 0 0 0 0.2rem rgba(23, 162, 184, 0.15);
    }

    /* Badge para tipos de eventos */
    .badge-evento {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Colores AdminLTE para badges */
    .badge-cultural { background: #17a2b8; color: white; }
    .badge-deportivo { background: #28a745; color: white; }
    .badge-educativo { background: #007bff; color: white; }
    .badge-social { background: #ffc107; color: #212529; }
    .badge-benefico { background: #dc3545; color: white; }
    .badge-otro { background: #6c757d; color: white; }
</style>
@endsection

@section('js')
<script src="{{ asset('assets/js/config.js') }}"></script>
<script src="{{ asset('assets/js/externo/eventos-index.js') }}"></script>
@stop
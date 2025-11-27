@extends('layouts.adminlte-externo')

@section('page_title', 'Eventos Disponibles')

@section('content_body')

<!-- Header con diseño mejorado - Paleta de colores -->
<div class="card mb-4 shadow-sm" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); border: none; border-radius: 15px; overflow: hidden;">
    <div class="card-body py-4 px-4">
        <div class="row align-items-center">
            <div class="col-md-10">
                <div class="d-flex align-items-center">
                    <div class="bg-white rounded-circle p-3 mr-3 shadow-sm" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <i class="far fa-calendar-check" style="font-size: 1.8rem; color: #00A36C;"></i>
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
                <i class="far fa-calendar-alt" style="font-size: 4.5rem; color: rgba(255,255,255,0.15);"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filtros y Búsqueda con diseño mejorado -->
<div class="card mb-4 shadow-sm" style="border-radius: 12px; border: 1px solid #F5F5F5;">
    <div class="card-header bg-white border-0" style="border-radius: 12px 12px 0 0;">
        <h5 class="mb-0" style="color: #0C2B44; font-weight: 600;">
            <i class="far fa-sliders-h mr-2" style="color: #00A36C;"></i>Filtros de Búsqueda
        </h5>
    </div>
    <div class="card-body" style="padding: 1.5rem;">
        <div class="row">
            <div class="col-md-4 mb-3 mb-md-0">
                <label for="filtroTipo" class="form-label font-weight-bold" style="color: #0C2B44; font-weight: 600; margin-bottom: 0.75rem;">
                    <i class="far fa-filter mr-2" style="color: #00A36C;"></i>Tipo de Evento
                </label>
                <select id="filtroTipo" class="form-control" style="border-radius: 8px; padding: 0.75rem; border: 1px solid #e9ecef;">
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
                <label for="buscador" class="form-label font-weight-bold" style="color: #0C2B44; font-weight: 600; margin-bottom: 0.75rem;">
                    <i class="far fa-search mr-2" style="color: #00A36C;"></i>Buscar Eventos
                </label>
                <div class="input-group">
                    <input type="text" id="buscador" class="form-control" placeholder="Buscar por título o descripción..." 
                           style="border-radius: 8px 0 0 8px; padding: 0.75rem; border: 1px solid #e9ecef; border-right: none;">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" id="btnLimpiar" 
                                style="border-radius: 0 8px 8px 0; border: 1px solid #e9ecef; border-left: none; padding: 0.75rem 1rem;">
                            <i class="far fa-times-circle"></i>
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
        <div class="spinner-border" role="status" style="width: 3rem; height: 3rem; color: #00A36C;">
            <span class="sr-only">Cargando...</span>
        </div>
        <p class="text-muted mt-3">Cargando eventos disponibles...</p>
    </div>
</div>

@stop

@push('css')
<style>
    /* Estilos mejorados para eventos en los que el usuario está inscrito */
    .evento-inscrito {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
    }
    
    .evento-inscrito::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(90deg, #00A36C 0%, #008a5a 100%);
        z-index: 1;
    }
    
    .evento-inscrito .card-body {
        background: linear-gradient(to bottom, rgba(0, 163, 108, 0.05) 0%, rgba(248, 249, 250, 1) 15%);
    }
    
    .evento-inscrito:hover {
        box-shadow: 0 10px 25px rgba(0, 163, 108, 0.25) !important;
        transform: translateY(-3px);
        transition: all 0.3s ease;
    }

    /* Mejoras para las tarjetas de eventos */
    .card {
        transition: all 0.3s ease;
        border-radius: 12px;
        border: 1px solid #F5F5F5;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(12, 43, 68, 0.15) !important;
    }

    /* Estilos para los inputs */
    .form-control:focus {
        border-color: #00A36C;
        box-shadow: 0 0 0 0.2rem rgba(0, 163, 108, 0.15);
    }

    select.form-control:focus {
        border-color: #00A36C;
        box-shadow: 0 0 0 0.2rem rgba(0, 163, 108, 0.15);
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

    /* Colores con paleta de colores */
    .badge-cultural { background: #0C2B44; color: white; }
    .badge-deportivo { background: #00A36C; color: white; }
    .badge-educativo { background: #0C2B44; color: white; }
    .badge-social { background: #00A36C; color: white; }
    .badge-benefico { background: #dc3545; color: white; }
    .badge-otro { background: #6c757d; color: white; }
</style>
@endpush

@push('js')
<script src="{{ asset('assets/js/config.js') }}"></script>
<script src="{{ asset('assets/js/externo/eventos-index.js') }}"></script>
@endpush
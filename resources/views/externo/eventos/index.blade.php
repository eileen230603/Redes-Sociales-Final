@extends('layouts.adminlte-externo')

@section('page_title', 'Eventos Disponibles')

@section('content_body')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-primary"><i class="fas fa-calendar-alt"></i> Eventos Disponibles</h4>
</div>

<!-- Filtros y Búsqueda -->
<div class="card mb-4 shadow-sm">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 mb-3 mb-md-0">
                <label for="filtroTipo" class="form-label"><i class="fas fa-filter mr-2"></i>Tipo de Evento</label>
                <select id="filtroTipo" class="form-control">
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
                <label for="buscador" class="form-label"><i class="fas fa-search mr-2"></i>Buscar</label>
                <div class="input-group">
                    <input type="text" id="buscador" class="form-control" placeholder="Buscar por título o descripción...">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" id="btnLimpiar">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row" id="listaEventos">
    <p class="text-muted px-3">Cargando eventos...</p>
</div>

@stop

@section('css')
<style>
    /* Estilos para eventos en los que el usuario está inscrito */
    .evento-inscrito {
        position: relative;
    }
    
    .evento-inscrito::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #28a745 0%, #20c997 100%);
        z-index: 1;
    }
    
    .evento-inscrito .card-body {
        background: linear-gradient(to bottom, rgba(40, 167, 69, 0.05) 0%, rgba(248, 249, 250, 1) 10%);
    }
    
    .evento-inscrito:hover {
        box-shadow: 0 8px 16px rgba(40, 167, 69, 0.2) !important;
    }
</style>
@endsection

@section('js')
<script src="{{ asset('assets/js/config.js') }}"></script>
<script src="{{ asset('assets/js/externo/eventos-index.js') }}"></script>
@stop

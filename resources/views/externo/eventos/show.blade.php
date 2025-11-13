@extends('layouts.adminlte-externo')

@section('page_title', 'Detalle del Evento')

@section('content_body')

<input type="hidden" id="eventoId" value="{{ request()->id }}">

<div class="card">
    <div class="card-body">

        <h2 id="titulo"></h2>
        <p><strong>Descripción:</strong> <span id="descripcion"></span></p>

        <p><strong>Fecha inicio:</strong> <span id="fecha_inicio"></span></p>
        <p><strong>Ciudad:</strong> <span id="ciudad"></span></p>
        <p><strong>Dirección:</strong> <span id="direccion"></span></p>
        <p><strong>Capacidad:</strong> <span id="capacidad_maxima"></span></p>

        <hr>

        <button class="btn btn-success" id="btnParticipar">
            <i class="fas fa-check-circle"></i> Participar
        </button>

        <button class="btn btn-danger d-none" id="btnCancelar">
            <i class="fas fa-times-circle"></i> Cancelar inscripción
        </button>

    </div>
</div>

@stop

@section('js')
<script src="{{ asset('assets/js/config.js') }}"></script>
<script src="{{ asset('assets/js/externo/show-event.js') }}"></script>
@stop

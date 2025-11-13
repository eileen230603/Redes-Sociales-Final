@extends('adminlte::page')

@section('title', 'Detalle del Evento')

@section('content_header')
<h1 class="text-primary">Detalle del Evento</h1>
@stop

@section('content')
<div class="container">

    <div class="card shadow-sm">
        <div class="card-body">

            <h3 id="titulo" class="text-primary"></h3>

            <p><strong>Descripción:</strong> <span id="descripcion"></span></p>
            <p><strong>Fecha inicio:</strong> <span id="fecha_inicio"></span></p>
            <p><strong>Fecha fin:</strong> <span id="fecha_fin"></span></p>
            <p><strong>Fecha límite inscripción:</strong> <span id="fecha_limite_inscripcion"></span></p>
            <p><strong>Tipo evento:</strong> <span id="tipo_evento"></span></p>
            <p><strong>Capacidad máxima:</strong> <span id="capacidad_maxima"></span></p>
            <p><strong>Estado:</strong> <span id="estado"></span></p>
            <p><strong>Ciudad:</strong> <span id="ciudad"></span></p>
            <p><strong>Dirección:</strong> <span id="direccion"></span></p>

            <p><strong>Patrocinadores:</strong> <span id="patrocinadores"></span></p>
            <p><strong>Invitados:</strong> <span id="invitados"></span></p>

            <hr>

            <h4>Imágenes</h4>
            <div id="imagenes" class="row"></div>

            <a href="/ong/eventos" class="btn btn-secondary mt-4">Volver</a>

        </div>
    </div>

</div>
@stop

@section('js')
<script src="{{ asset('assets/js/config.js') }}"></script>
<script src="{{ asset('assets/js/ong/show-event.js') }}"></script>
@stop
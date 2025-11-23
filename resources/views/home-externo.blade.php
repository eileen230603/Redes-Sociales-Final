@extends('layouts.adminlte-externo')

@section('page_title', 'Panel del Integrante Externo')

@section('content_body')

<div class="alert alert-info">
    <h5>Bienvenido, <span id="nombreUsuario"></span></h5>
    <p>Explora eventos, participa y revisa tu historial.</p>
</div>

@include('externo.partials.resumen')
@include('externo.partials.estadisticas')
@include('externo.partials.eventos-disponibles')

@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('assets/js/config.js') }}"></script>
<script src="{{ asset('assets/js/externo/home.js') }}"></script>
@stop

@extends('layouts.adminlte-externo')

@section('page_title', 'Eventos Disponibles')

@section('content_body')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-primary"><i class="fas fa-calendar-alt"></i> Eventos Disponibles</h4>
</div>

<div class="row" id="listaEventos">
    <p class="text-muted px-3">Cargando eventos...</p>
</div>

@stop

@section('js')
<script src="{{ asset('assets/js/config.js') }}"></script>
<script src="{{ asset('assets/js/externo/eventos-index.js') }}"></script>
@stop

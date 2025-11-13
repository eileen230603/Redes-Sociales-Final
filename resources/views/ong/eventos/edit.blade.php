@extends('adminlte::page')

@section('title', 'Editar Evento')

@section('content_header')
<h1 class="text-primary">Editar Evento</h1>
@stop

@section('content')
<div class="container">

    <div class="card shadow-sm">
        <div class="card-body">

            <form id="editEventForm">

                <div class="form-group mb-3">
                    <label>Título</label>
                    <input type="text" id="titulo" class="form-control">
                </div>

                <div class="form-group mb-3">
                    <label>Descripción</label>
                    <textarea id="descripcion" class="form-control" rows="3"></textarea>
                </div>

                <div class="form-group mb-3">
                    <label>Tipo de evento</label>
                    <select id="tipo_evento" class="form-control">
                        <option value="conferencia">Conferencia</option>
                        <option value="taller">Taller</option>
                        <option value="seminario">Seminario</option>
                        <option value="voluntariado">Voluntariado</option>
                        <option value="cultural">Cultural</option>
                        <option value="deportivo">Deportivo</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label>Fecha inicio</label>
                    <input type="datetime-local" id="fecha_inicio" class="form-control">
                </div>

                <div class="form-group mb-3">
                    <label>Fecha fin</label>
                    <input type="datetime-local" id="fecha_fin" class="form-control">
                </div>

                <div class="form-group mb-3">
                    <label>Fecha límite inscripción</label>
                    <input type="datetime-local" id="fecha_limite_inscripcion" class="form-control">
                </div>

                <div class="form-group mb-3">
                    <label>Capacidad máxima</label>
                    <input type="number" id="capacidad_maxima" class="form-control">
                </div>

                <div class="form-group mb-3">
                    <label>Estado</label>
                    <select id="estado" class="form-control">
                        <option value="borrador">Borrador</option>
                        <option value="publicado">Publicado</option>
                        <option value="cancelado">Cancelado</option>
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label>Ciudad</label>
                    <input type="text" id="ciudad" class="form-control">
                </div>

                <div class="form-group mb-3">
                    <label>Dirección</label>
                    <input type="text" id="direccion" class="form-control">
                </div>

                <button type="submit" class="btn btn-primary mt-3">Guardar cambios</button>

            </form>

        </div>
    </div>

</div>
@stop

@section('js')
<script src="{{ asset('assets/js/config.js') }}"></script>
<script src="{{ asset('assets/js/ong/edit-event.js') }}"></script>
@stop

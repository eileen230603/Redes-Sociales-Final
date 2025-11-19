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

                <!-- IMÁGENES -->
                <hr class="mt-4 mb-3">
                <h5 class="mb-3"><i class="fas fa-images"></i> Imágenes promocionales</h5>
                
                <!-- Imágenes existentes -->
                <div id="imagenesExistentes" class="mb-3">
                    <p class="text-muted">Cargando imágenes...</p>
                </div>

                <!-- Subir nuevas imágenes -->
                <div class="form-group mb-3">
                    <label>Agregar nuevas imágenes</label>
                    <input type="file" id="nuevasImagenes" multiple accept="image/*" class="form-control-file">
                    <small class="text-muted">Puedes seleccionar múltiples imágenes</small>
                </div>

                <!-- Preview de nuevas imágenes -->
                <div id="previewNuevasImagenes" class="d-flex flex-wrap gap-2 mb-3"></div>

                <button type="submit" class="btn btn-primary mt-3">Guardar cambios</button>

            </form>

        </div>
    </div>

</div>
@stop

@section('css')
<style>
    #imagenesExistentes .imagen-item {
        position: relative;
        display: inline-block;
        margin: 8px;
    }
    #imagenesExistentes .imagen-item img {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #ddd;
        cursor: pointer;
        transition: transform 0.2s;
    }
    #imagenesExistentes .imagen-item img:hover {
        transform: scale(1.05);
    }
    #imagenesExistentes .imagen-item .btn-eliminar {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(220, 53, 69, 0.9);
        color: white;
        border: none;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }
    #previewNuevasImagenes img {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #28a745;
        margin: 8px;
    }
</style>
@stop

@section('js')
<script src="{{ asset('assets/js/config.js') }}"></script>
<script src="{{ asset('assets/js/ong/edit-event.js') }}"></script>
@stop

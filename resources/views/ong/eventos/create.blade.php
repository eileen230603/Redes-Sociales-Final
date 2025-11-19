@extends('adminlte::page')

@section('title', 'Crear Evento | UNI2')

@section('content_header')
    <h1><i class="fas fa-calendar-plus text-success"></i> Crear nuevo evento</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-body">

            <form id="createEventJsonForm" enctype="multipart/form-data">

                <!-- Información básica -->
                <div class="mb-4 border-bottom pb-3">
                    <h4 class="text-primary"><i class="fas fa-info-circle"></i> Información básica</h4>
                </div>

                <div class="form-group mb-3">
                    <label for="titulo">Título del evento *</label>
                    <input id="titulo" name="titulo" class="form-control" required>
                </div>

                <div class="form-group mb-3">
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" name="descripcion" rows="3" class="form-control"></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="fechaInicio">Fecha de inicio *</label>
                        <input type="datetime-local" id="fechaInicio" class="form-control" required>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="fechaFinal">Fecha de finalización</label>
                        <input type="datetime-local" id="fechaFinal" class="form-control">
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="fechaLimiteInscripcion">Límite para inscribirse</label>
                    <input type="datetime-local" id="fechaLimiteInscripcion" class="form-control">
                </div>

                <div class="form-group mb-3">
                    <label for="tipoEvento">Tipo de evento *</label>
                    <select id="tipoEvento" class="form-control" required>
                        <option disabled selected>Selecciona una categoría</option>
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
                    <label for="capacidadMaxima">Capacidad máxima</label>
                    <input type="text" id="capacidadMaxima" class="form-control" pattern="[0-9]*" inputmode="numeric" placeholder="Solo números">
                    <small class="form-text text-muted">Ingrese solo números (sin letras, símbolos ni espacios)</small>
                </div>

                <div class="form-group mb-3">
                    <label for="estado">Estado del evento *</label>
                    <select id="estado" class="form-control" required>
                        <option value="" disabled selected>Selecciona un estado</option>
                        <option value="borrador">📝 Borrador</option>
                        <option value="publicado">✅ Publicado</option>
                        <option value="cancelado">❌ Cancelado</option>
                    </select>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" id="inscripcionAbierta" checked>
                    <label class="form-check-label" for="inscripcionAbierta">
                        Inscripción abierta
                    </label>
                </div>

                <!-- MAPA -->
                <hr>
                <h4 class="text-success mt-4"><i class="fas fa-map-marker-alt"></i> Ubicación del evento</h4>

                <div id="map" class="rounded mb-3" style="height: 300px; border: 1px solid #ced4da;"></div>

                <div class="form-group">
                    <label for="locacion">Dirección seleccionada</label>
                    <input id="locacion" readonly class="form-control bg-light">
                    <small id="ciudadInfo" class="text-muted"></small>
                </div>

                <input type="hidden" id="lat">
                <input type="hidden" id="lng">

                <!-- IMÁGENES -->
                <hr>
                <h4 class="text-warning mt-4"><i class="fas fa-images"></i> Imágenes promocionales</h4>

                <!-- Subir imágenes desde dispositivo -->
                <div class="form-group mb-3">
                    <label for="imagenesPromocionales">Subir imágenes desde dispositivo</label>
                    <input type="file" id="imagenesPromocionales" multiple accept="image/*" class="form-control-file">
                    <small class="form-text text-muted">Formatos permitidos: JPG, PNG, GIF, WEBP. Tamaño máximo: 5MB por imagen.</small>
                </div>

                <div id="previewContainer" class="d-flex flex-wrap gap-2 mb-4"></div>

                <!-- Agregar imagen por URL -->
                <div class="form-group mb-3">
                    <label for="imagen_url">Agregar imagen por URL</label>
                    <div class="input-group">
                        <input type="url" id="imagen_url" class="form-control" placeholder="https://ejemplo.com/imagen.jpg">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-success" id="btnAgregarUrl">
                                <i class="fas fa-plus mr-2"></i>Agregar
                            </button>
                        </div>
                    </div>
                    <small class="form-text text-muted">Ingresa una URL válida de una imagen (JPG, PNG, GIF, WEBP).</small>
                </div>

                <div id="urlImagesContainer" class="d-flex flex-wrap gap-2 mb-3"></div>

                <!-- EMPRESAS -->
                <hr>
                <h4 class="text-info mt-4"><i class="fas fa-handshake"></i> Empresas colaboradoras</h4>

                <div class="row" id="patrocinadoresBox">
                    <p class="text-muted px-3">Cargando empresas...</p>
                </div>

                <!-- INVITADOS -->
                <hr>
                <h4 class="text-info mt-4"><i class="fas fa-user-friends"></i> Invitados</h4>

                <div class="row" id="invitadosBox">
                    <p class="text-muted px-3">Cargando invitados...</p>
                </div>

                <!-- BOTONES -->
                <hr class="mt-4 mb-3">

                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-secondary mr-2" id="saveDraftBtn">
                        <i class="fas fa-save"></i> Guardar borrador
                    </button>

                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check-circle"></i> Publicar evento
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

<style>
    #previewContainer img,
    #urlImagesContainer img {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #ddd;
        margin: 5px;
        cursor: pointer;
        transition: transform 0.2s;
    }
    #previewContainer img:hover,
    #urlImagesContainer img:hover {
        transform: scale(1.05);
    }
    .image-preview-wrapper {
        position: relative;
        display: inline-block;
        margin: 5px;
    }
    .image-preview-wrapper .remove-image {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #dc3545;
        color: white;
        border-radius: 50%;
        width: 25px;
        height: 25px;
        border: none;
        cursor: pointer;
        font-size: 12px;
    }
    #urlImagesContainer .image-preview-wrapper {
        border: 2px solid #28a745;
    }
    #urlImagesContainer img {
        width: 100%;
        height: 150px;
        object-fit: cover;
        border-radius: 6px;
    }
</style>
@stop

@section('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="{{ asset('assets/js/config.js') }}"></script>

<!-- TU ARCHIVO JS FINAL -->
<script src="{{ asset('assets/js/ong/create-event.js') }}"></script>
@stop

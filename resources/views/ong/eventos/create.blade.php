@extends('layouts.adminlte')

@section('page_title', 'Crear Evento')

@section('content_body')
<div class="container-fluid">
    <div class="card shadow-sm" style="border-radius: 12px; border: 1px solid #F5F5F5;">
        <div class="card-header" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); color: #FFFFFF; border-radius: 12px 12px 0 0;">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div>
                    <h3 class="mb-1" style="font-weight: 700; font-size: 1.4rem;">
                        <i class="far fa-calendar-plus mr-2"></i> Crear nuevo evento
                    </h3>
                    <p class="mb-0" style="font-size: 0.9rem; opacity: 0.9;">
                        Define la información principal, ubicación, imágenes y empresas participantes de tu evento.
                    </p>
                </div>
                <div class="mt-3 mt-md-0">
                    <a href="{{ route('ong.eventos.index') }}" class="btn btn-sm" style="background: rgba(255,255,255,0.15); color: #FFFFFF; border-radius: 999px; border: none; padding: 0.5rem 1.25rem;">
                        <i class="far fa-arrow-left mr-1"></i> Volver a eventos
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body p-4">

            <form id="createEventJsonForm" enctype="multipart/form-data">

                <!-- Información básica -->
                <div class="mb-4 pb-3" style="border-bottom: 1px solid #F5F5F5;">
                    <h4 class="mb-1" style="color: #0C2B44; font-weight: 700;">
                        <i class="far fa-info-circle mr-2" style="color: #00A36C;"></i> Información básica
                    </h4>
                    <p class="mb-0 text-muted" style="font-size: 0.9rem;">Completa los datos generales del evento.</p>
                </div>

                <div class="form-group mb-3">
                    <label for="titulo">Título del evento *</label>
                    <input id="titulo" name="titulo" class="form-control" required>
                </div>

                <div class="form-group mb-3">
                    <label for="descripcion">Descripción *</label>
                    <textarea id="descripcion" name="descripcion" rows="3" class="form-control" required></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="fechaInicio">Fecha de inicio *</label>
                        <input type="datetime-local" id="fechaInicio" class="form-control" required>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="fechaFinal">Fecha de finalización *</label>
                        <input type="datetime-local" id="fechaFinal" class="form-control" required>
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
                <div class="mt-4 mb-3" style="border-top: 1px solid #F5F5F5; padding-top: 1.5rem;">
                    <h4 class="mb-1" style="color: #0C2B44; font-weight: 700;">
                        <i class="far fa-map mr-2" style="color: #00A36C;"></i> Ubicación del evento
                    </h4>
                    <p class="mb-0 text-muted" style="font-size: 0.9rem;">Selecciona en el mapa el lugar donde se realizará el evento.</p>
                </div>

                <div id="map" class="rounded mb-3" style="height: 300px; border: 1px solid #ced4da;"></div>

                <div class="form-group">
                    <label for="locacion">Dirección seleccionada *</label>
                    <input id="locacion" readonly class="form-control bg-light" required>
                    <small id="ciudadInfo" class="text-muted"></small>
                    <small class="form-text text-muted">Haz clic en el mapa para seleccionar la ubicación del evento</small>
                </div>

                <input type="hidden" id="lat">
                <input type="hidden" id="lng">

                <!-- IMÁGENES -->
                <div class="mt-4 mb-3" style="border-top: 1px solid #F5F5F5; padding-top: 1.5rem;">
                    <h4 class="mb-1" style="color: #0C2B44; font-weight: 700;">
                        <i class="far fa-images mr-2" style="color: #00A36C;"></i> Imágenes promocionales
                    </h4>
                    <p class="mb-0 text-muted" style="font-size: 0.9rem;">Sube imágenes que representen tu evento para destacarlo en la plataforma.</p>
                </div>

                <!-- Subir imágenes desde dispositivo -->
                <div class="form-group mb-3">
                    <label for="imagenesPromocionales">Subir imágenes desde dispositivo *</label>
                    <input type="file" id="imagenesPromocionales" multiple accept="image/*" class="form-control-file">
                    <small class="form-text text-muted">Formatos permitidos: JPG, PNG, GIF, WEBP. Tamaño máximo: 5MB por imagen. Debes agregar al menos una imagen (archivo o URL).</small>
                </div>

                <div id="previewContainer" class="d-flex flex-wrap gap-2 mb-4"></div>

                <!-- Agregar imagen por URL -->
                <div class="form-group mb-3">
                    <label for="imagen_url">Agregar imagen por URL *</label>
                    <div class="input-group">
                        <input type="url" id="imagen_url" class="form-control" placeholder="https://ejemplo.com/imagen.jpg">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-success" id="btnAgregarUrl">
                                <i class="fas fa-plus mr-2"></i>Agregar
                            </button>
                        </div>
                    </div>
                    <small class="form-text text-muted">Ingresa una URL válida de una imagen (JPG, PNG, GIF, WEBP). Debes agregar al menos una imagen (archivo o URL).</small>
                </div>

                <div id="urlImagesContainer" class="d-flex flex-wrap gap-2 mb-3"></div>

                <!-- PATROCINADORES -->
                <div class="mt-4 mb-2" style="border-top: 1px solid #F5F5F5; padding-top: 1.5rem;">
                    <h4 class="mb-1" style="color: #0C2B44; font-weight: 700;">
                        <i class="far fa-handshake mr-2" style="color: #00A36C;"></i> Patrocinadores
                    </h4>
                </div>
                <p class="text-muted"><small>Selecciona las empresas que patrocinarán este evento. Los patrocinadores aparecerán en los detalles del evento.</small></p>
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row" id="patrocinadoresBox">
                            <div class="col-12 text-center py-3">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Cargando...</span>
                                </div>
                                <p class="mt-2 text-muted">Cargando empresas disponibles...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- INVITADOS -->
                <div class="mt-4 mb-2" style="border-top: 1px solid #F5F5F5; padding-top: 1.5rem;">
                    <h4 class="mb-1" style="color: #0C2B44; font-weight: 700;">
                        <i class="far fa-user-friends mr-2" style="color: #00A36C;"></i> Invitados
                    </h4>
                </div>

                <div class="row" id="invitadosBox">
                    <p class="text-muted px-3">Cargando invitados...</p>
                </div>

                <!-- BOTONES -->
                <div class="mt-4 pt-3 d-flex justify-content-end" style="border-top: 1px solid #F5F5F5;">
                    <button type="button" class="btn btn-outline-secondary mr-2" id="saveDraftBtn" style="border-radius: 8px;">
                        <i class="far fa-save mr-1"></i> Guardar borrador
                    </button>

                    <button type="submit" class="btn btn-success" style="border-radius: 8px; min-width: 190px; font-weight: 600;">
                        <i class="far fa-check-circle mr-1"></i> Publicar evento
                    </button>
                </div>

            </form>

        </div>
    </div>
    </div>
</div>
@endsection

@push('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

<style>
    /* Layout general del formulario de creación de eventos */
    #createEventJsonForm .form-group label,
    #createEventJsonForm .form-label {
        font-weight: 600;
        color: #0C2B44;
        font-size: 0.9rem;
    }

    #createEventJsonForm .form-control {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        padding: 0.6rem 0.9rem;
        transition: all 0.2s ease;
    }

    #createEventJsonForm .form-control:focus {
        border-color: #00A36C;
        box-shadow: 0 0 0 0.15rem rgba(0, 163, 108, 0.2);
    }

    #createEventJsonForm small.form-text {
        font-size: 0.8rem;
    }

    /* Tarjeta de mapa */
    #map {
        border-radius: 12px;
        border: 1px solid #F5F5F5;
        box-shadow: 0 2px 8px rgba(12, 43, 68, 0.08);
    }

    #previewContainer img,
    #urlImagesContainer img {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #F5F5F5;
        margin: 5px;
        cursor: pointer;
        transition: transform 0.2s;
    }
    #previewContainer img:hover,
    #urlImagesContainer img:hover {
        transform: scale(1.05);
    }
    .is-invalid {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }
    .is-invalid:focus {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
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
        border: 2px solid #00A36C;
    }
    #urlImagesContainer img {
        width: 100%;
        height: 150px;
        object-fit: cover;
        border-radius: 6px;
    }
    .is-invalid {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }
    .is-invalid:focus {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }
</style>
@endpush

@push('js')
{{-- Script global para icono de notificaciones --}}
<script src="{{ asset('js/notificaciones-ong.js') }}"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="{{ asset('assets/js/config.js') }}"></script>
<script src="{{ asset('assets/js/ong/create-event.js') }}"></script>
@endpush

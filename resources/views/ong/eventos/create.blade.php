@extends('layouts.adminlte')

@section('page_title', 'Crear Evento')

@section('content_body')
<div class="container-fluid">
    <!-- Header Mejorado -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%);">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div class="d-flex align-items-center">
                    <div class="mr-3" style="width: 64px; height: 64px; background: rgba(255,255,255,0.15); border-radius: 16px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px); box-shadow: 0 4px 16px rgba(0,0,0,0.2);">
                        <i class="fas fa-calendar-plus text-white" style="font-size: 2rem;"></i>
                    </div>
                <div>
                        <h3 class="mb-1 text-white" style="font-weight: 700; font-size: 1.75rem;">
                            Crear nuevo evento
                    </h3>
                        <p class="mb-0 text-white" style="font-size: 0.95rem; opacity: 0.95;">
                        Define la información principal, ubicación, imágenes y empresas participantes de tu evento.
                    </p>
                    </div>
                </div>
                <div class="mt-3 mt-md-0">
                    <a href="{{ route('ong.eventos.index') }}" class="btn" style="background: rgba(255,255,255,0.2); color: #FFFFFF; border-radius: 10px; border: none; padding: 0.6rem 1.5rem; font-weight: 500; backdrop-filter: blur(10px); transition: all 0.3s ease;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                        <i class="fas fa-arrow-left mr-2"></i> Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario Principal -->
    <div class="card border-0 shadow-sm" style="border-radius: 16px; max-width: 1200px; margin: 0 auto;">
        <div class="card-body p-5">
            <form id="createEventJsonForm" enctype="multipart/form-data">

                <!-- Información básica -->
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="section-icon mr-3">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="font-weight: 700; color: #0C2B44; font-size: 1.1rem;">
                                Información básica
                            </h5>
                            <p class="mb-0 text-muted" style="font-size: 0.85rem; margin-top: 0.25rem;">
                                Completa los datos generales del evento
                            </p>
                        </div>
                </div>

                <div class="form-group mb-4">
                    <label for="titulo" class="form-label-custom">
                        <i class="fas fa-heading mr-2"></i> Título del evento *
                    </label>
                    <input id="titulo" name="titulo" class="form-control form-control-custom" placeholder="Ej: Conferencia de Tecnología 2025" required value="">
                </div>

                <div class="form-group mb-4">
                    <label for="descripcion" class="form-label-custom">
                        <i class="fas fa-align-left mr-2"></i> Descripción *
                    </label>
                    <textarea id="descripcion" name="descripcion" rows="5" class="form-control form-control-custom" placeholder="Describe tu evento de manera detallada. Incluye información sobre el objetivo, público objetivo, actividades principales y cualquier detalle relevante que los participantes deban conocer..." required></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6 mb-4">
                        <label for="fechaInicio" class="form-label-custom">
                            <i class="fas fa-calendar-check mr-2"></i> Fecha de inicio *
                        </label>
                        <input type="datetime-local" id="fechaInicio" class="form-control form-control-custom" required>
                    </div>

                    <div class="form-group col-md-6 mb-4">
                        <label for="fechaFinal" class="form-label-custom">
                            <i class="fas fa-calendar-times mr-2"></i> Fecha de finalización *
                        </label>
                        <input type="datetime-local" id="fechaFinal" class="form-control form-control-custom" required>
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label for="fechaLimiteInscripcion" class="form-label-custom">
                        <i class="fas fa-clock mr-2"></i> Límite para inscribirse
                    </label>
                    <input type="datetime-local" id="fechaLimiteInscripcion" class="form-control form-control-custom">
                    <small class="form-text text-muted">Opcional: establece una fecha límite para las inscripciones</small>
                </div>

                <div class="form-group mb-4">
                    <label for="tipoEvento" class="form-label-custom">
                        <i class="fas fa-tag mr-2"></i> Tipo de evento *
                    </label>
                    <select id="tipoEvento" class="form-control form-control-custom" required>
                        <option value="" disabled>Selecciona una categoría</option>
                        <option value="conferencia" selected>📢 Conferencia</option>
                        <option value="taller">🔧 Taller</option>
                        <option value="seminario">🎓 Seminario</option>
                        <option value="voluntariado">🤝 Voluntariado</option>
                        <option value="cultural">🎭 Cultural</option>
                        <option value="deportivo">⚽ Deportivo</option>
                        <option value="otro">📌 Otro</option>
                    </select>
                </div>

                <div class="form-group mb-4">
                    <label for="capacidadMaxima" class="form-label-custom">
                        <i class="fas fa-users mr-2"></i> Capacidad máxima
                    </label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <button type="button" class="btn btn-outline-secondary" id="btnDecrementarCapacidad" style="border-radius: 10px 0 0 10px; border-right: none;">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                        <input type="number" id="capacidadMaxima" class="form-control form-control-custom text-center" min="1" value="0" style="border-radius: 0; border-left: none; border-right: none;">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary" id="btnIncrementarCapacidad" style="border-radius: 0 10px 10px 0; border-left: none;">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <small class="form-text text-muted">Use los botones +/- o ingrese un número directamente. Deje en 0 para capacidad ilimitada.</small>
                </div>

                <div class="form-group mb-4">
                    <label for="estado" class="form-label-custom">
                        <i class="fas fa-flag mr-2"></i> Estado del evento *
                    </label>
                    <select id="estado" class="form-control form-control-custom" required>
                        <option value="borrador" selected>📝 Borrador</option>
                        <option value="publicado">✅ Publicado</option>
                        <option value="cancelado">❌ Cancelado</option>
                    </select>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" id="inscripcionAbierta" checked style="width: 20px; height: 20px; cursor: pointer;">
                    <label class="form-check-label ml-2" for="inscripcionAbierta" style="font-weight: 500; color: #2c3e50; cursor: pointer;">
                        <i class="fas fa-user-check mr-2" style="color: #00A36C;"></i> Inscripción abierta
                    </label>
                </div>

                <!-- MAPA -->
                <div class="mb-4" style="border-top: 2px solid #f0f0f0; padding-top: 2rem; margin-top: 2rem;">
                    <div class="d-flex align-items-center mb-3">
                        <div class="section-icon mr-3">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="font-weight: 700; color: #0C2B44; font-size: 1.1rem;">
                                Ubicación del evento
                            </h5>
                            <p class="mb-0 text-muted" style="font-size: 0.85rem; margin-top: 0.25rem;">
                                Selecciona en el mapa el lugar donde se realizará el evento
                            </p>
                        </div>
                </div>

                    <div id="map" class="rounded mb-4" style="height: 350px; border: 2px solid #f0f0f0; box-shadow: 0 2px 12px rgba(0,0,0,0.05);"></div>

                <div class="form-group">
                        <label for="locacion" class="form-label-custom">
                            <i class="fas fa-road mr-2"></i> Dirección seleccionada *
                        </label>
                        <input id="locacion" readonly class="form-control form-control-custom bg-light" required placeholder="Haz clic en el mapa para seleccionar la ubicación">
                        <small id="ciudadInfo" class="text-muted d-block mt-1"></small>
                        <small class="form-text text-muted"><i class="fas fa-info-circle mr-1"></i> Haz clic en el mapa para seleccionar la ubicación del evento</small>
                    </div>
                </div>

                <input type="hidden" id="lat">
                <input type="hidden" id="lng">

                <!-- IMÁGENES -->
                <div class="mb-4" style="border-top: 2px solid #f0f0f0; padding-top: 2rem; margin-top: 2rem;">
                    <div class="d-flex align-items-center mb-3">
                        <div class="section-icon mr-3">
                            <i class="fas fa-images"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="font-weight: 700; color: #0C2B44; font-size: 1.1rem;">
                                Imágenes promocionales
                            </h5>
                            <p class="mb-0 text-muted" style="font-size: 0.85rem; margin-top: 0.25rem;">
                                Sube imágenes que representen tu evento
                            </p>
                        </div>
                </div>

                <!-- Subir imágenes desde dispositivo -->
                    <div class="form-group mb-4">
                        <label for="imagenesPromocionales" class="form-label-custom">
                            <i class="fas fa-upload mr-2"></i> Subir imágenes desde dispositivo *
                        </label>
                        <div class="custom-file">
                            <input type="file" id="imagenesPromocionales" multiple accept="image/*" class="custom-file-input form-control-custom">
                            <label class="custom-file-label form-control-custom" for="imagenesPromocionales" style="border-radius: 10px; padding: 0.6rem 0.9rem;">
                                <i class="fas fa-folder-open mr-2"></i> Seleccionar archivos
                            </label>
                        </div>
                    <small class="form-text text-muted">Formatos permitidos: JPG, PNG, GIF, WEBP. Tamaño máximo: 5MB por imagen. Debes agregar al menos una imagen (archivo o URL).</small>
                </div>

                    <div id="previewContainer" class="d-flex flex-wrap gap-3 mb-4"></div>

                <!-- Agregar imagen por URL -->
                    <div class="form-group mb-4">
                        <label for="imagen_url" class="form-label-custom">
                            <i class="fas fa-link mr-2"></i> Agregar imagen por URL *
                        </label>
                    <div class="input-group">
                            <input type="url" id="imagen_url" class="form-control form-control-custom" placeholder="https://ejemplo.com/imagen.jpg" style="border-radius: 10px 0 0 10px;">
                        <div class="input-group-append">
                                <button type="button" class="btn btn-success-custom" id="btnAgregarUrl" style="border-radius: 0 10px 10px 0; border-left: none;">
                                    <i class="fas fa-plus mr-2"></i> Agregar
                            </button>
                        </div>
                    </div>
                    <small class="form-text text-muted">Ingresa una URL válida de una imagen (JPG, PNG, GIF, WEBP). Debes agregar al menos una imagen (archivo o URL).</small>
                </div>

                    <div id="urlImagesContainer" class="d-flex flex-wrap gap-3 mb-3"></div>
                </div>

                <!-- PATROCINADORES -->
                <div class="mb-4" style="border-top: 2px solid #f0f0f0; padding-top: 2rem; margin-top: 2rem;">
                    <div class="d-flex align-items-center mb-3">
                        <div class="section-icon mr-3">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="font-weight: 700; color: #0C2B44; font-size: 1.1rem;">
                                Patrocinadores
                            </h5>
                            <p class="mb-0 text-muted" style="font-size: 0.85rem; margin-top: 0.25rem;">
                                Selecciona las empresas que patrocinarán este evento
                            </p>
                        </div>
                </div>
                    <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px; background: #f8f9fa;">
                        <div class="card-body p-4">
                        <div class="row" id="patrocinadoresBox">
                                <div class="col-12 text-center py-4">
                                    <div class="spinner-border" role="status" style="color: #00A36C; width: 3rem; height: 3rem;">
                                    <span class="sr-only">Cargando...</span>
                                    </div>
                                    <p class="mt-3 text-muted">Cargando empresas disponibles...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- INVITADOS -->
                <div class="mb-4" style="border-top: 2px solid #f0f0f0; padding-top: 2rem; margin-top: 2rem;">
                    <div class="d-flex align-items-center mb-3">
                        <div class="section-icon mr-3">
                            <i class="fas fa-user-friends"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="font-weight: 700; color: #0C2B44; font-size: 1.1rem;">
                                Invitados especiales
                            </h5>
                            <p class="mb-0 text-muted" style="font-size: 0.85rem; margin-top: 0.25rem;">
                                Selecciona invitados especiales para este evento
                            </p>
                        </div>
                </div>

                <div class="row" id="invitadosBox">
                        <div class="col-12 text-center py-4">
                            <div class="spinner-border" role="status" style="color: #00A36C; width: 3rem; height: 3rem;">
                                <span class="sr-only">Cargando...</span>
                            </div>
                            <p class="mt-3 text-muted">Cargando invitados...</p>
                        </div>
                    </div>
                </div>

                <!-- BOTONES -->
                <div class="mt-4 pt-4 d-flex justify-content-end flex-wrap" style="border-top: 2px solid #f0f0f0; gap: 0.75rem;">
                    <button type="button" class="btn btn-outline-secondary" id="saveDraftBtn" style="border-radius: 10px; padding: 0.6rem 1.5rem; font-weight: 500; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                        <i class="fas fa-save mr-2"></i> Guardar borrador
                    </button>

                    <button type="submit" class="btn btn-success-custom" style="border-radius: 10px; padding: 0.6rem 2rem; font-weight: 600; min-width: 190px; box-shadow: 0 4px 12px rgba(0, 163, 108, 0.3);">
                        <i class="fas fa-check-circle mr-2"></i> Publicar evento
                    </button>
                </div>

            </form>
    </div>
    </div>
</div>
@endsection

@push('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

<style>
    /* Variables de color */
    :root {
        --primary-color: #00A36C;
        --primary-dark: #008a5a;
        --dark-color: #0C2B44;
        --border-color: #e9ecef;
        --bg-light: #f8f9fa;
        --shadow-sm: 0 2px 8px rgba(12, 43, 68, 0.08);
        --shadow-md: 0 4px 16px rgba(12, 43, 68, 0.12);
    }

    body {
        background-color: #f5f7fa;
    }

    /* Iconos de sección - Con container y icono adentro */
    .section-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
        box-shadow: 0 4px 12px rgba(0, 163, 108, 0.2);
        flex-shrink: 0;
    }

    /* Labels personalizados */
    .form-label-custom {
        font-weight: 600;
        color: #0C2B44;
        font-size: 0.95rem;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
    }

    /* Controles de formulario personalizados */
    .form-control-custom {
        border-radius: 10px;
        border: 2px solid #e9ecef;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
        font-size: 0.95rem;
        background-color: #ffffff;
        color: #2c3e50;
    }

    .form-control-custom:not(:placeholder-shown) {
        border-color: #00A36C;
        background-color: #f8fff9;
    }

    .form-control-custom:focus {
        border-color: #00A36C;
        box-shadow: 0 0 0 4px rgba(0, 163, 108, 0.1);
        outline: none;
        background-color: #ffffff;
    }

    .form-control-custom::placeholder {
        color: #adb5bd;
        font-style: italic;
    }

    .form-control-custom:empty::before {
        content: attr(placeholder);
        color: #adb5bd;
    }

    /* Select personalizado */
    select.form-control-custom {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2300A36C' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        padding-right: 2.5rem;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
    }

    select.form-control-custom:not([value=""]) {
        border-color: #00A36C;
        background-color: #f8fff9;
    }

    /* Botones personalizados */
    .btn-success-custom {
        background: linear-gradient(135deg, #00A36C 0%, #008a5a 100%);
        border: none;
        color: white;
        transition: all 0.3s ease;
    }

    .btn-success-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 163, 108, 0.4) !important;
    }

    .btn-outline-secondary:hover {
        background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
        border-color: #6c757d;
        color: white;
        transform: translateY(-2px);
    }

    /* Tarjeta de mapa mejorada */
    #map {
        border-radius: 12px;
        border: 2px solid #f0f0f0;
        box-shadow: 0 2px 12px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }

    #map:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    }

    /* Previews de imágenes mejoradas */
    #previewContainer img,
    #urlImagesContainer img {
        width: 180px;
        height: 180px;
        object-fit: cover;
        border-radius: 12px;
        border: 3px solid #00A36C;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 163, 108, 0.2);
    }
    
    #previewContainer img:hover,
    #urlImagesContainer img:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 16px rgba(0, 163, 108, 0.3);
        border-color: #008a5a;
    }
    
    /* Estilos para patrocinadores mejorados */
    .patrocinador-card {
        transition: all 0.3s ease;
        border-radius: 12px;
        border: 1px solid #e9ecef;
        padding: 1rem;
        background: white;
    }
    .patrocinador-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 163, 108, 0.15);
        border-color: #00A36C;
    }
    .patrocinador-card .form-check-input:checked {
        background-color: #00A36C;
        border-color: #00A36C;
    }
    
    /* Estilos para botones de capacidad mejorados */
    #btnIncrementarCapacidad,
    #btnDecrementarCapacidad {
        min-width: 45px;
        font-weight: 600;
        transition: all 0.3s ease;
        border-color: #e9ecef;
    }
    #btnIncrementarCapacidad:hover,
    #btnDecrementarCapacidad:hover {
        background: linear-gradient(135deg, #00A36C 0%, #008a5a 100%);
        border-color: #00A36C;
        color: white;
        transform: scale(1.05);
    }
    #capacidadMaxima {
        font-weight: 600;
        font-size: 1.1rem;
        color: #0C2B44;
    }

    /* Cards mejoradas */
    .card {
        transition: all 0.3s ease;
        border: none !important;
        background: #ffffff;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md) !important;
    }

    /* Mejora visual para campos con valores - usando JavaScript para compatibilidad */
    .form-group.has-value label {
        color: #00A36C;
    }

    .form-group.has-value .form-control-custom {
        border-color: #00A36C;
        background-color: #f8fff9;
    }

    /* Mejora para textarea */
    textarea.form-control-custom {
        min-height: 120px;
        resize: vertical;
    }

    textarea.form-control-custom:not(:placeholder-shown) {
        border-color: #00A36C;
        background-color: #f8fff9;
    }

    /* Custom file input mejorado */
    .custom-file-label {
        cursor: pointer;
        transition: all 0.3s ease;
        background-color: #ffffff;
        border: 2px solid #e9ecef;
    }

    .custom-file-label:hover {
        border-color: #00A36C;
        background: #f8fff9;
    }

    .custom-file-input:focus ~ .custom-file-label {
        border-color: #00A36C;
        box-shadow: 0 0 0 4px rgba(0, 163, 108, 0.1);
    }

    .custom-file-input:valid ~ .custom-file-label {
        border-color: #00A36C;
        background-color: #f8fff9;
    }

    /* Input readonly con mejor estilo */
    input[readonly].form-control-custom {
        background-color: #f8f9fa;
        cursor: not-allowed;
        border-color: #dee2e6;
    }

    input[readonly].form-control-custom:not(:placeholder-shown) {
        background-color: #e9ecef;
        border-color: #00A36C;
    }

    /* Responsive */
    @media (max-width: 991px) {
        .section-icon {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }

        h5 {
            font-size: 1rem !important;
        }

        #previewContainer img,
        #urlImagesContainer img {
            width: 150px;
            height: 150px;
        }
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

<script>
    // Mejorar la detección de campos con valores
    document.addEventListener('DOMContentLoaded', function() {
        const formControls = document.querySelectorAll('.form-control-custom, select.form-control-custom');
        
        function checkFieldValue(field) {
            const formGroup = field.closest('.form-group');
            if (!formGroup) return;
            
            let hasValue = false;
            
            if (field.tagName === 'SELECT') {
                hasValue = field.value !== '' && field.value !== null;
            } else if (field.type === 'checkbox' || field.type === 'radio') {
                hasValue = field.checked;
            } else {
                hasValue = field.value.trim() !== '' || field.value !== '';
            }
            
            if (hasValue) {
                formGroup.classList.add('has-value');
            } else {
                formGroup.classList.remove('has-value');
            }
        }
        
        // Verificar valores iniciales
        formControls.forEach(field => {
            checkFieldValue(field);
            
            // Agregar listeners para cambios
            field.addEventListener('input', function() {
                checkFieldValue(this);
            });
            
            field.addEventListener('change', function() {
                checkFieldValue(this);
            });
        });
    });
</script>
@endpush

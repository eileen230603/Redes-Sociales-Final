@extends('layouts.adminlte')

@section('page_title', 'Editar Evento')

@section('content_body')
<div class="container-fluid py-4">
    <!-- Header mejorado -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; overflow: hidden; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%);">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div class="mb-3 mb-md-0">
                    <div class="d-flex align-items-center mb-2">
                        <div class="mr-3" style="width: 56px; height: 56px; background: rgba(255,255,255,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px);">
                            <i class="fas fa-edit" style="font-size: 1.5rem; color: #FFFFFF;"></i>
                        </div>
                <div>
                            <h2 class="mb-0" style="font-weight: 700; font-size: 1.75rem; color: #FFFFFF; letter-spacing: -0.5px;">
                                Editar Evento
                            </h2>
                            <p class="mb-0 mt-1" style="font-size: 0.95rem; color: rgba(255,255,255,0.9);">
                                Modifica la información de tu evento
                    </p>
                        </div>
                    </div>
                </div>
                <div>
                    <a href="{{ route('ong.eventos.index') }}" class="btn btn-light btn-sm" style="border-radius: 10px; padding: 0.6rem 1.5rem; font-weight: 500; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                        <i class="fas fa-arrow-left mr-2"></i> Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

            <form id="editEventForm">
        <div class="row">
            <!-- Columna principal -->
            <div class="col-lg-8">
                <!-- Información básica -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="section-icon mr-3">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <div>
                                <h5 class="mb-0" style="font-weight: 700; color: #0C2B44; font-size: 1.1rem;">
                                    Información Básica
                                </h5>
                                <p class="mb-0 text-muted" style="font-size: 0.85rem; margin-top: 0.25rem;">
                                    Datos principales del evento
                                </p>
                            </div>
                </div>

                        <div class="form-group mb-4">
                            <label for="titulo" class="form-label-custom">
                                <i class="fas fa-heading mr-2"></i> Título del evento <span class="text-danger">*</span>
                    </label>
                            <input type="text" id="titulo" class="form-control-custom" required placeholder="Ej: Conferencia de Tecnología 2025">
                </div>

                        <div class="form-group mb-4">
                            <label for="descripcion" class="form-label-custom">
                                <i class="fas fa-align-left mr-2"></i> Descripción <span class="text-danger">*</span>
                    </label>
                            <textarea id="descripcion" class="form-control-custom" rows="4" required placeholder="Describe tu evento de manera clara y atractiva..."></textarea>
                </div>

                        <div class="form-group mb-0">
                            <label for="tipo_evento" class="form-label-custom">
                                <i class="fas fa-tag mr-2"></i> Tipo de evento <span class="text-danger">*</span>
                    </label>
                            <select id="tipo_evento" class="form-control-custom" required>
                                <option value="">Selecciona un tipo</option>
                                <option value="conferencia">📢 Conferencia</option>
                                <option value="taller">🔧 Taller</option>
                                <option value="seminario">🎓 Seminario</option>
                                <option value="voluntariado">🤝 Voluntariado</option>
                                <option value="cultural">🎭 Cultural</option>
                                <option value="deportivo">⚽ Deportivo</option>
                                <option value="otro">📌 Otro</option>
                    </select>
                        </div>
                    </div>
                </div>

                <!-- Fechas -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="section-icon mr-3">
                                <i class="fas fa-calendar-alt"></i>
                </div>
                            <div>
                                <h5 class="mb-0" style="font-weight: 700; color: #0C2B44; font-size: 1.1rem;">
                                    Fechas del Evento
                                </h5>
                                <p class="mb-0 text-muted" style="font-size: 0.85rem; margin-top: 0.25rem;">
                                    Define cuándo se realizará el evento
                                </p>
                    </div>
                </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="fecha_inicio" class="form-label-custom">
                                    <i class="fas fa-calendar-check mr-2"></i> Inicio <span class="text-danger">*</span>
                                </label>
                                <input type="datetime-local" id="fecha_inicio" class="form-control-custom" required>
                </div>

                            <div class="col-md-4 mb-3">
                                <label for="fecha_fin" class="form-label-custom">
                                    <i class="fas fa-calendar-times mr-2"></i> Finalización <span class="text-danger">*</span>
                        </label>
                                <input type="datetime-local" id="fecha_fin" class="form-control-custom" required>
                </div>

                            <div class="col-md-4 mb-3">
                                <label for="fecha_limite_inscripcion" class="form-label-custom">
                                    <i class="fas fa-clock mr-2"></i> Límite inscripción
                        </label>
                                <input type="datetime-local" id="fecha_limite_inscripcion" class="form-control-custom">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ubicación -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="section-icon mr-3">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <h5 class="mb-0" style="font-weight: 700; color: #0C2B44; font-size: 1.1rem;">
                                    Ubicación
                                </h5>
                                <p class="mb-0 text-muted" style="font-size: 0.85rem; margin-top: 0.25rem;">
                                    Selecciona la ubicación en el mapa
                                </p>
                            </div>
                </div>

                        <div id="map" class="rounded mb-4" style="height: 350px; border: 2px solid #f0f0f0; box-shadow: 0 2px 12px rgba(0,0,0,0.05);"></div>

                <div class="form-group mb-3">
                            <label for="locacion" class="form-label-custom">
                                <i class="fas fa-map-pin mr-2"></i> Dirección seleccionada
                    </label>
                            <input id="locacion" readonly class="form-control-custom bg-light">
                            <small id="ciudadInfo" class="text-muted d-block mt-1"></small>
                            <small class="form-text text-muted" style="font-size: 0.8rem; margin-top: 0.5rem;">
                                <i class="fas fa-info-circle mr-1"></i> Haz clic en el mapa para seleccionar la ubicación
                            </small>
                </div>

                <input type="hidden" id="lat">
                <input type="hidden" id="lng">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="ciudad" class="form-label-custom">
                                    <i class="fas fa-city mr-2"></i> Ciudad
                        </label>
                                <input type="text" id="ciudad" class="form-control-custom" placeholder="Ej: La Paz">
                    </div>

                            <div class="col-md-6 mb-0">
                                <label for="direccion" class="form-label-custom">
                                    <i class="fas fa-road mr-2"></i> Dirección
                        </label>
                                <input type="text" id="direccion" class="form-control-custom" placeholder="Ej: Av. 16 de Julio">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Imágenes -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="section-icon mr-3">
                                <i class="fas fa-images"></i>
                            </div>
                            <div>
                                <h5 class="mb-0" style="font-weight: 700; color: #0C2B44; font-size: 1.1rem;">
                                    Imágenes Promocionales
                                </h5>
                                <p class="mb-0 text-muted" style="font-size: 0.85rem; margin-top: 0.25rem;">
                                    Agrega imágenes que representen tu evento
                                </p>
                            </div>
                </div>
                
                <!-- Imágenes existentes -->
                <div id="imagenesExistentes" class="mb-4">
                            <p class="text-muted text-center py-3">
                                <i class="fas fa-spinner fa-spin mr-2"></i> Cargando imágenes...
                            </p>
                </div>

                        <!-- Subir nuevas imágenes -->
                        <div class="form-group mb-4">
                            <label for="nuevasImagenes" class="form-label-custom">
                                <i class="fas fa-upload mr-2"></i> Subir imágenes desde dispositivo
                    </label>
                            <div class="custom-file-upload">
                                <input type="file" id="nuevasImagenes" multiple accept="image/*" class="form-control-custom">
                            </div>
                            <small class="form-text text-muted mt-2">
                                <i class="fas fa-info-circle mr-1"></i> Formatos: JPG, PNG, GIF, WEBP. Máximo 5MB por imagen.
                            </small>
                </div>

                <!-- Preview de nuevas imágenes -->
                <div id="previewNuevasImagenes" class="d-flex flex-wrap gap-2 mb-4"></div>

                <!-- Agregar imagen por URL -->
                        <div class="form-group mb-0">
                            <label for="imagen_url_edit" class="form-label-custom">
                                <i class="fas fa-link mr-2"></i> Agregar imagen por URL
                    </label>
                            <div class="input-group-custom">
                                <input type="url" id="imagen_url_edit" class="form-control-custom" placeholder="https://ejemplo.com/imagen.jpg">
                                <button type="button" class="btn btn-primary-custom" id="btnAgregarUrlEdit">
                                    <i class="fas fa-plus mr-2"></i> Agregar
                            </button>
                            </div>
                            <small class="form-text text-muted mt-2">
                                <i class="fas fa-info-circle mr-1"></i> Ingresa una URL válida de una imagen
                            </small>
                        </div>

                        <div id="urlImagesContainerEdit" class="d-flex flex-wrap gap-2 mt-4"></div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Configuración -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; position: sticky; top: 20px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="section-icon mr-3">
                                <i class="fas fa-cog"></i>
                            </div>
                            <div>
                                <h5 class="mb-0" style="font-weight: 700; color: #0C2B44; font-size: 1.1rem;">
                                    Configuración
                                </h5>
                                <p class="mb-0 text-muted" style="font-size: 0.85rem; margin-top: 0.25rem;">
                                    Ajustes del evento
                                </p>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label for="capacidad_maxima" class="form-label-custom">
                                <i class="fas fa-users mr-2"></i> Capacidad máxima
                            </label>
                            <input type="text" id="capacidad_maxima" class="form-control-custom" pattern="[0-9]*" inputmode="numeric" placeholder="Ej: 100">
                            <small class="form-text text-muted mt-2">
                                <i class="fas fa-info-circle mr-1"></i> Solo números
                            </small>
                        </div>

                        <div class="form-group mb-0">
                            <label for="estado" class="form-label-custom">
                                <i class="fas fa-flag mr-2"></i> Estado <span class="text-danger">*</span>
                            </label>
                            <select id="estado" class="form-control-custom" required>
                                <option value="borrador">📝 Borrador</option>
                                <option value="publicado">✅ Publicado</option>
                                <option value="cancelado">❌ Cancelado</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="card border-0 shadow-sm" style="border-radius: 16px; position: sticky; top: 400px;">
                    <div class="card-body p-4">
                        <button type="button" class="btn btn-outline-secondary btn-block mb-3" onclick="window.location.href='{{ route('ong.eventos.index') }}'" style="border-radius: 10px; padding: 0.75rem; font-weight: 500;">
                            <i class="fas fa-times mr-2"></i> Cancelar
                    </button>
                        <button type="submit" class="btn btn-success-custom btn-block" style="border-radius: 10px; padding: 0.75rem; font-weight: 600; box-shadow: 0 4px 12px rgba(0, 163, 108, 0.3);">
                            <i class="fas fa-save mr-2"></i> Guardar cambios
                    </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
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

    /* Iconos de sección */
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
    }

    /* Labels personalizados */
    .form-label-custom {
        display: block;
        font-weight: 600;
        color: #0C2B44;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
        letter-spacing: -0.2px;
    }

    .form-label-custom i {
        color: #00A36C;
        width: 18px;
    }

    /* Inputs personalizados */
    .form-control-custom {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background-color: #fff;
    }

    .form-control-custom:focus {
        outline: none;
        border-color: #00A36C;
        box-shadow: 0 0 0 4px rgba(0, 163, 108, 0.1);
        background-color: #fff;
    }

    .form-control-custom::placeholder {
        color: #adb5bd;
    }

    /* Select personalizado */
    select.form-control-custom {
        cursor: pointer;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2300A36C' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        padding-right: 2.5rem;
        appearance: none;
    }

    /* Botones personalizados */
    .btn-primary-custom {
        background: linear-gradient(135deg, #00A36C 0%, #008a5a 100%);
        color: white;
        border: none;
        border-radius: 10px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 163, 108, 0.2);
    }

    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 163, 108, 0.3);
        color: white;
    }

    .btn-success-custom {
        background: linear-gradient(135deg, #00A36C 0%, #008a5a 100%);
        color: white;
        border: none;
        transition: all 0.3s ease;
    }

    .btn-success-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 163, 108, 0.4) !important;
        color: white;
    }

    /* Input group personalizado */
    .input-group-custom {
        display: flex;
        gap: 0.5rem;
    }

    .input-group-custom .form-control-custom {
        flex: 1;
        border-radius: 10px 0 0 10px;
    }

    .input-group-custom .btn-primary-custom {
        border-radius: 0 10px 10px 0;
        white-space: nowrap;
    }

    /* Mapa mejorado */
    #map {
        transition: all 0.3s ease;
    }

    #map:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.1) !important;
    }

    /* Imágenes existentes */
    #imagenesExistentes .imagen-item {
        position: relative;
        display: inline-block;
        margin: 8px;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }

    #imagenesExistentes .imagen-item:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }

    #imagenesExistentes .imagen-item img {
        width: 160px;
        height: 160px;
        object-fit: cover;
        display: block;
    }

    #imagenesExistentes .imagen-item .btn-eliminar {
        position: absolute;
        top: 8px;
        right: 8px;
        background: rgba(220, 53, 69, 0.95);
        color: white;
        border: none;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
    }

    #imagenesExistentes .imagen-item .btn-eliminar:hover {
        background: #dc3545;
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
    }

    /* Preview de nuevas imágenes */
    #previewNuevasImagenes img,
    #urlImagesContainerEdit img {
        width: 160px;
        height: 160px;
        object-fit: cover;
        border-radius: 12px;
        border: 3px solid #00A36C;
        margin: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 163, 108, 0.2);
    }

    #previewNuevasImagenes img:hover,
    #urlImagesContainerEdit img:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 16px rgba(0, 163, 108, 0.3);
    }

    .image-preview-wrapper-url {
        position: relative;
        display: inline-block;
        margin: 8px;
        border: 3px solid #00A36C;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 163, 108, 0.2);
    }

    .image-preview-wrapper-url img {
        width: 160px;
        height: 160px;
        object-fit: cover;
        display: block;
    }

    .image-preview-wrapper-url .remove-image {
        position: absolute;
        top: 8px;
        right: 8px;
        background: rgba(220, 53, 69, 0.95);
        color: white;
        border-radius: 50%;
        width: 28px;
        height: 28px;
        border: none;
        cursor: pointer;
        font-size: 12px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
    }

    .image-preview-wrapper-url .remove-image:hover {
        background: #dc3545;
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
    }

    /* Cards mejoradas */
    .card {
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: var(--shadow-md) !important;
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
    }

    /* Animaciones suaves */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .card {
        animation: fadeIn 0.4s ease-out;
    }
</style>
@endpush

@push('js')
{{-- Script global para icono de notificaciones --}}
<script src="{{ asset('js/notificaciones-ong.js') }}"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="{{ asset('assets/js/config.js') }}"></script>
<script src="{{ asset('assets/js/ong/edit-event.js') }}"></script>
@endpush

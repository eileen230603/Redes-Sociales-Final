@extends('layouts.adminlte')

@section('page_title', 'Crear Mega Evento')

@section('content_body')
<div class="container-fluid">
    <!-- Header Mejorado -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%);">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div class="d-flex align-items-center">
                    <div class="mr-3" style="width: 64px; height: 64px; background: rgba(255,255,255,0.15); border-radius: 16px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px); box-shadow: 0 4px 16px rgba(0,0,0,0.2);">
                        <i class="fas fa-star text-white" style="font-size: 2rem;"></i>
                    </div>
                    <div>
                        <h3 class="mb-1 text-white" style="font-weight: 700; font-size: 1.75rem;">
                            Crear nuevo mega evento
                        </h3>
                        <p class="mb-0 text-white" style="font-size: 0.95rem; opacity: 0.95;">
                            Define la información principal, ubicación, visibilidad e imágenes para tu mega evento.
                        </p>
                    </div>
                </div>
                <div class="mt-3 mt-md-0">
                    <a href="{{ route('ong.mega-eventos.index') }}" class="btn" style="background: rgba(255,255,255,0.2); color: #FFFFFF; border-radius: 10px; border: none; padding: 0.6rem 1.5rem; font-weight: 500; backdrop-filter: blur(10px); transition: all 0.3s ease;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                        <i class="fas fa-arrow-left mr-2"></i> Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario Principal -->
    <div class="card border-0 shadow-sm" style="border-radius: 16px; max-width: 1200px; margin: 0 auto;">
        <div class="card-body p-5">
            <form id="createMegaEventoForm" enctype="multipart/form-data">
                <!-- Información Básica -->
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
                                Completa los datos generales del mega evento
                            </p>
                        </div>
                    </div>
                    <div class="form-group mb-4">
                        <label for="titulo" class="form-label-custom">
                            <i class="fas fa-heading mr-2"></i> Título del mega evento *
                        </label>
                        <input type="text" id="titulo" name="titulo" class="form-control form-control-custom" required 
                               maxlength="200" placeholder="Ej: Festival de Verano 2025">
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-8 mb-4">
                            <label for="descripcion" class="form-label-custom">
                                <i class="fas fa-align-left mr-2"></i> Descripción
                            </label>
                            <textarea id="descripcion" name="descripcion" rows="5" class="form-control form-control-custom" 
                                      placeholder="Describe el mega evento de manera detallada. Incluye información sobre el objetivo, público objetivo, actividades principales y cualquier detalle relevante..."></textarea>
                        </div>
                        <div class="form-group col-md-4 mb-4">
                            <label for="categoria" class="form-label-custom">
                                <i class="fas fa-tags mr-2"></i> Categoría
                            </label>
                            <select id="categoria" name="categoria" class="form-control form-control-custom">
                                <option value="social">Social</option>
                                <option value="cultural">Cultural</option>
                                <option value="deportivo">Deportivo</option>
                                <option value="educativo">Educativo</option>
                                <option value="benefico">Benéfico</option>
                                <option value="ambiental">Ambiental</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Fechas -->
                <div class="mb-4" style="border-top: 2px solid #f0f0f0; padding-top: 2rem; margin-top: 2rem;">
                    <div class="d-flex align-items-center mb-3">
                        <div class="section-icon mr-3">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="font-weight: 700; color: #0C2B44; font-size: 1.1rem;">
                                Fechas del mega evento
                            </h5>
                            <p class="mb-0 text-muted" style="font-size: 0.85rem; margin-top: 0.25rem;">
                                Define cuándo comenzará y finalizará el mega evento
                            </p>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6 mb-4">
                            <label for="fecha_inicio" class="form-label-custom">
                                <i class="fas fa-calendar-check mr-2"></i> Fecha y hora de inicio *
                            </label>
                            <input type="datetime-local" id="fecha_inicio" name="fecha_inicio" 
                                   class="form-control form-control-custom" required>
                        </div>
                        <div class="form-group col-md-6 mb-4">
                            <label for="fecha_fin" class="form-label-custom">
                                <i class="fas fa-calendar-times mr-2"></i> Fecha y hora de fin *
                            </label>
                            <input type="datetime-local" id="fecha_fin" name="fecha_fin" 
                                   class="form-control form-control-custom" required>
                        </div>
                    </div>
                </div>

                <!-- Ubicación y Capacidad -->
                <div class="mb-4" style="border-top: 2px solid #f0f0f0; padding-top: 2rem; margin-top: 2rem;">
                    <div class="d-flex align-items-center mb-3">
                        <div class="section-icon mr-3">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="font-weight: 700; color: #0C2B44; font-size: 1.1rem;">
                                Ubicación y capacidad
                            </h5>
                            <p class="mb-0 text-muted" style="font-size: 0.85rem; margin-top: 0.25rem;">
                                Selecciona la ubicación en el mapa y define la capacidad máxima
                            </p>
                        </div>
                    </div>

                    <!-- Mapa -->
                    <div class="form-group mb-4">
                        <label class="form-label-custom">
                            <i class="fas fa-map mr-2"></i> Seleccionar ubicación en el mapa
                        </label>
                        <div id="map" class="rounded mb-3" style="height: 350px; border: 2px solid #e9ecef; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.05);"></div>
                        <div class="form-group mb-3">
                            <label for="locacion" class="form-label-custom">Dirección seleccionada *</label>
                            <input id="locacion" readonly class="form-control form-control-custom" style="background: #f8f9fa;">
                            <small id="ciudadInfo" class="text-muted"></small>
                        </div>
                        <input type="hidden" id="lat" name="lat">
                        <input type="hidden" id="lng" name="lng">
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-8 mb-4">
                            <label for="ubicacion" class="form-label-custom">
                                <i class="fas fa-location-dot mr-2"></i> Ubicación (texto)
                            </label>
                            <input type="text" id="ubicacion" name="ubicacion" class="form-control form-control-custom" 
                                   maxlength="500" placeholder="Ej: Parque Central, La Paz">
                            <small class="form-text text-muted">Puedes escribir manualmente o seleccionar en el mapa</small>
                        </div>
                        <div class="form-group col-md-4 mb-4">
                            <label for="capacidad_maxima" class="form-label-custom">
                                <i class="fas fa-users mr-2"></i> Capacidad máxima
                            </label>
                            <input type="number" id="capacidad_maxima" name="capacidad_maxima" 
                                   class="form-control form-control-custom" min="1" placeholder="Ej: 1000">
                        </div>
                    </div>
                </div>

                <!-- Configuración -->
                <div class="mb-4" style="border-top: 2px solid #f0f0f0; padding-top: 2rem; margin-top: 2rem;">
                    <div class="d-flex align-items-center mb-3">
                        <div class="section-icon mr-3">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="font-weight: 700; color: #0C2B44; font-size: 1.1rem;">
                                Configuración
                            </h5>
                            <p class="mb-0 text-muted" style="font-size: 0.85rem; margin-top: 0.25rem;">
                                Define el estado, visibilidad y actividad del mega evento
                            </p>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4 mb-4">
                            <label for="estado" class="form-label-custom">
                                <i class="fas fa-info-circle mr-2"></i> Estado
                            </label>
                            <select id="estado" name="estado" class="form-control form-control-custom">
                                <option value="planificacion">Planificación</option>
                                <option value="activo">Activo</option>
                                <option value="en_curso">En Curso</option>
                                <option value="finalizado">Finalizado</option>
                                <option value="cancelado">Cancelado</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4 mb-4">
                            <label for="es_publico" class="form-label-custom">
                                <i class="fas fa-eye mr-2"></i> Visibilidad
                            </label>
                            <select id="es_publico" name="es_publico" class="form-control form-control-custom">
                                <option value="1">Público</option>
                                <option value="0">Privado</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4 mb-4">
                            <label for="activo" class="form-label-custom">
                                <i class="fas fa-power-off mr-2"></i> Estado de actividad
                            </label>
                            <select id="activo" name="activo" class="form-control form-control-custom">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Imágenes -->
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
                                Agrega imágenes para promocionar tu mega evento
                            </p>
                        </div>
                    </div>

                    <!-- Subir archivos -->
                    <div class="form-group mb-4">
                        <label for="imagenes" class="form-label-custom">
                            <i class="fas fa-upload mr-2"></i> Subir imágenes desde archivo
                        </label>
                        <div class="custom-file-wrapper">
                            <input type="file" id="imagenes" name="imagenes[]" multiple 
                                   accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" 
                                   class="custom-file-input-improved">
                            <label class="custom-file-label-improved" for="imagenes">
                                <span class="file-label-text">Seleccionar archivos...</span>
                                <i class="fas fa-chevron-down ml-auto"></i>
                            </label>
                        </div>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle mr-1"></i> Puedes seleccionar múltiples imágenes. Formatos permitidos: JPG, PNG, GIF, WEBP (máx. 5MB cada una)
                        </small>
                    </div>

                    <div id="previewContainer" class="row mb-4"></div>

                    <!-- Agregar por URL -->
                    <div class="form-group mb-4">
                        <label for="imagen_url" class="form-label-custom">
                            <i class="fas fa-link mr-2"></i> Agregar imagen por URL (opcional)
                        </label>
                        <div class="input-group">
                            <input type="url" id="imagen_url" name="imagen_url" 
                                   class="form-control form-control-custom" 
                                   placeholder="https://ejemplo.com/imagen.jpg"
                                   style="border-radius: 10px 0 0 10px;">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-gradient-info" id="btnAgregarUrl" style="border-radius: 0 10px 10px 0; border: none;">
                                    <i class="fas fa-plus mr-1"></i> Agregar
                                </button>
                            </div>
                        </div>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle mr-1"></i> Ingresa la URL completa de una imagen en internet
                        </small>
                    </div>

                    <div id="urlImagesContainer" class="row mb-4"></div>
                </div>

                <!-- Patrocinadores -->
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
                                Selecciona las empresas que patrocinarán este mega evento
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

                <!-- Mensaje de resultado -->
                <div id="formMessage" class="alert" style="display: none;"></div>

                <!-- Botones -->
                <div class="d-flex justify-content-end" style="border-top: 2px solid #f0f0f0; padding-top: 2rem; margin-top: 2rem;">
                    <a href="{{ route('ong.mega-eventos.index') }}" class="btn btn-outline-secondary mr-3" style="border-radius: 10px; padding: 0.6rem 1.5rem; font-weight: 500;">
                        <i class="fas fa-times mr-2"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-gradient-success" style="border-radius: 10px; padding: 0.6rem 2rem; font-weight: 600; font-size: 1rem;">
                        <i class="fas fa-check-circle mr-2"></i> Crear mega evento
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
    /* Paleta de colores principal */
    :root {
        --color-primary: #0C2B44;
        --color-secondary: #00A36C;
        --color-gradient: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%);
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
        box-shadow: 0 4px 12px rgba(12, 43, 68, 0.2);
    }

    /* Labels de formulario */
    .form-label-custom {
        font-weight: 600;
        color: #0C2B44;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
    }

    .form-label-custom i {
        color: #00A36C;
        margin-right: 0.5rem;
    }

    /* Inputs de formulario */
    .form-control-custom {
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: #ffffff;
    }

    .form-control-custom:focus {
        border-color: #00A36C;
        box-shadow: 0 0 0 0.2rem rgba(0, 163, 108, 0.15);
        outline: none;
    }

    .form-control-custom::placeholder {
        color: #adb5bd;
    }

    /* Botones con gradiente */
    .btn-gradient-primary {
        background: linear-gradient(135deg, #0C2B44 0%, #1a4a6b 100%);
        border: none;
        color: white;
        transition: all 0.3s ease;
    }

    .btn-gradient-primary:hover {
        background: linear-gradient(135deg, #1a4a6b 0%, #0C2B44 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(12, 43, 68, 0.3);
        color: white;
    }

    .btn-gradient-success {
        background: linear-gradient(135deg, #00A36C 0%, #008a5a 100%);
        border: none;
        color: white;
        transition: all 0.3s ease;
    }

    .btn-gradient-success:hover {
        background: linear-gradient(135deg, #008a5a 0%, #00A36C 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 163, 108, 0.3);
        color: white;
    }

    .btn-gradient-info {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        border: none;
        color: white;
        transition: all 0.3s ease;
    }

    .btn-gradient-info:hover {
        background: linear-gradient(135deg, #138496 0%, #0f6674 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(23, 162, 184, 0.3);
        color: white;
    }

    /* Card principal */
    .card {
        border: none;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
    }

    /* Preview de imágenes */
    #previewContainer img {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 12px;
        border: 2px solid #e9ecef;
        margin: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    #previewContainer img:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 16px rgba(0, 163, 108, 0.3);
        border-color: #00A36C;
    }

    .image-preview-wrapper {
        position: relative;
        display: inline-block;
        margin: 8px;
    }

    .image-preview-wrapper .remove-image {
        position: absolute;
        top: -8px;
        right: -8px;
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        color: white;
        border-radius: 50%;
        width: 28px;
        height: 28px;
        border: 2px solid white;
        cursor: pointer;
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(220, 53, 69, 0.4);
        transition: all 0.3s ease;
    }

    .image-preview-wrapper .remove-image:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.6);
    }

    #urlImagesContainer .image-preview-wrapper,
    #urlImagesContainerEdit .image-preview-wrapper {
        border: 2px solid #00A36C;
        border-radius: 12px;
        overflow: hidden;
    }

    #urlImagesContainer img,
    #urlImagesContainerEdit img {
        width: 100%;
        height: 150px;
        object-fit: cover;
        border-radius: 10px;
    }

    /* Custom file input mejorado - Sin duplicaciones */
    .custom-file-wrapper {
        position: relative;
        display: block;
    }

    .custom-file-input-improved {
        position: absolute;
        opacity: 0;
        width: 100%;
        height: 100%;
        cursor: pointer;
        z-index: 2;
    }

    /* Ocultar cualquier estilo por defecto de Bootstrap */
    .custom-file-input-improved::-webkit-file-upload-button {
        display: none;
    }

    .custom-file-input-improved::file-selector-button {
        display: none;
    }

    .custom-file-label-improved {
        display: flex;
        align-items: center;
        justify-content: space-between;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 0.75rem 1rem;
        background: #ffffff;
        color: #495057;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        min-height: 48px;
        position: relative;
    }

    /* Asegurar que no haya pseudo-elementos adicionales */
    .custom-file-label-improved::before,
    .custom-file-label-improved::after {
        display: none !important;
        content: none !important;
    }

    .custom-file-label-improved:hover {
        border-color: #00A36C;
        background: #f8fff9;
    }

    .custom-file-input-improved:focus ~ .custom-file-label-improved {
        border-color: #00A36C;
        box-shadow: 0 0 0 0.2rem rgba(0, 163, 108, 0.15);
    }

    .file-label-text {
        flex: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* Solo UNA flecha hacia abajo */
    .custom-file-label-improved i {
        color: #00A36C;
        font-size: 0.9rem;
        transition: transform 0.3s ease;
        margin-left: auto;
        flex-shrink: 0;
    }

    .custom-file-input-improved:focus ~ .custom-file-label-improved i {
        transform: rotate(180deg);
    }

    /* Mapa mejorado */
    #map {
        border-radius: 12px;
        overflow: hidden;
    }

    /* Select mejorado - Solo una flecha */
    select.form-control-custom {
        cursor: pointer;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2300A36C' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 0.75rem center;
        background-repeat: no-repeat;
        background-size: 1.2em 1.2em;
        padding-right: 2.5rem;
    }

    /* Eliminar cualquier flecha adicional del navegador */
    select.form-control-custom::-ms-expand {
        display: none;
    }

    select.form-control-custom option {
        padding: 0.5rem;
    }

    /* Animaciones */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .card-body > * {
        animation: fadeInUp 0.5s ease-out;
    }
</style>
@endpush

@push('js')
{{-- Script global para icono de notificaciones --}}
<script src="{{ asset('js/notificaciones-ong.js') }}"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="{{ asset('assets/js/config.js') }}"></script>
<script>
/**
 * ============================================================
 * CORRECCIONES IMPLEMENTADAS PARA RESOLVER ERROR 403 EN IMÁGENES
 * ============================================================
 * 
 * ERROR 1: Verificación de envío de imágenes al backend
 *   - Agregados console.logs detallados para debuggear qué se envía
 *   - Asegurado que formData.append('imagenes[]', file) se ejecute correctamente
 *   - Verificado que formData.append('imagenes_urls', JSON.stringify(urlImages)) funcione
 * 
 * ERROR 2: URLs públicas correctas (/storage/)
 *   - El backend debe usar Storage::disk('public')->putFile() (ya implementado)
 *   - Las URLs generadas deben ser /storage/mega-eventos/{id}/{filename}
 *   - Verificar que php artisan storage:link esté ejecutado
 * 
 * ERROR 3: Procesamiento de imágenes locales
 *   - El backend procesa archivos y genera URLs públicas automáticamente
 *   - Las imágenes se guardan en storage/app/public/mega_eventos/{id}/
 *   - Las URLs se generan como /storage/mega_eventos/{id}/{uuid}.{ext}
 * 
 * ERROR 4: URLs externas
 *   - URLs externas válidas se guardan tal cual
 *   - URLs temporales o no accesibles se validan antes de guardar
 *   - Implementada validación de accesibilidad con Image.onload/onerror
 * 
 * ERROR 5: Validación de URLs accesibles
 *   - Función addUrlImage() valida que la URL sea accesible antes de agregarla
 *   - Muestra advertencia si la URL no es accesible pero permite agregarla de todos modos
 *   - Timeout de 5 segundos para validación
 * 
 * ERROR 6: Manejo de errores robusto
 *   - updatePreview() y updateUrlImagesPreview() implementan img.onerror con placeholders
 *   - Placeholders SVG personalizados cuando las imágenes fallan
 *   - Console.logs detallados para debugging
 * 
 * ERROR 7: Verificación de storage:link
 *   - IMPORTANTE: Ejecutar 'php artisan storage:link' en el servidor
 *   - Esto crea el enlace simbólico entre storage/app/public y public/storage
 *   - Sin este enlace, las URLs /storage/ no funcionarán (403 Forbidden)
 * 
 * ERROR 8: Console.logs detallados
 *   - Agregados logs en cada paso del proceso de imágenes
 *   - Logs de URLs generadas, archivos enviados, respuestas del servidor
 *   - Logs de validación de accesibilidad de URLs
 * 
 * ERROR 9: Fallback con placeholders
 *   - Implementado en updatePreview() y updateUrlImagesPreview()
 *   - Placeholders SVG cuando las imágenes fallan al cargar
 *   - Prevención de loops infinitos con this.onerror = null
 * 
 * ERROR 10: Rutas de almacenamiento públicas
 *   - El backend ya usa Storage::disk('public')->putFile() correctamente
 *   - Las URLs se generan con Storage::disk('public')->url($path)
 *   - Esto genera rutas como /storage/mega_eventos/{id}/{filename}
 */

let selectedFiles = [];
let urlImages = []; // Array para almacenar URLs de imágenes
let map, clickMarker;
let ciudadDetectada = "";

// ===============================
// 🗺️ MAPA LEAFLET
// ===============================
function initMap() {
    const pos = [-16.5, -68.15]; // La Paz, Bolivia por defecto

    map = L.map("map").setView(pos, 13);

    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png").addTo(map);

    map.on("click", (e) => {
        const { lat, lng } = e.latlng;

        if (clickMarker) clickMarker.setLatLng(e.latlng);
        else clickMarker = L.marker(e.latlng).addTo(map);

        document.getElementById("lat").value = lat;
        document.getElementById("lng").value = lng;

        reverseGeocode(lat, lng);
    });
}

// ===============================
// 🌍 GEOCODIFICACIÓN INVERSA
// ===============================
async function reverseGeocode(lat, lng) {
    try {
        const r = await fetch(
            `https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`
        );
        const data = await r.json();

        const direccion = data.display_name ?? "";
        document.getElementById("locacion").value = direccion;
        
        // Actualizar también el campo de ubicación si está vacío
        const ubicacionInput = document.getElementById("ubicacion");
        if (!ubicacionInput.value) {
            ubicacionInput.value = direccion;
        }

        ciudadDetectada =
            data.address?.city ||
            data.address?.town ||
            data.address?.village ||
            data.address?.state ||
            "Sin especificar";

        document.getElementById("ciudadInfo").innerText = "Ciudad: " + ciudadDetectada;

    } catch (e) {
        console.warn("No se pudo obtener dirección");
    }
}

// ===============================
// 🏢 PATROCINADORES
// ===============================
// Cargar patrocinadores disponibles
async function loadPatrocinadores() {
    const box = document.getElementById("patrocinadoresBox");
    if (!box) return;
    
    box.innerHTML = '<div class="col-12 text-center py-3"><div class="spinner-border" role="status" style="color: #00A36C; width: 3rem; height: 3rem;"><span class="sr-only">Cargando...</span></div><p class="mt-2 text-muted">Cargando empresas...</p></div>';

    const token = localStorage.getItem('token');
    try {
        const res = await fetch(`${API_BASE_URL}/api/eventos/empresas/disponibles`, {
            headers: { 
                Authorization: `Bearer ${token}`,
                Accept: 'application/json',
                'Content-Type': 'application/json'
            }
        });
        
        if (!res.ok) {
            const errorData = await res.json().catch(() => ({}));
            throw new Error(errorData.error || `Error HTTP ${res.status}: ${res.statusText}`);
        }
        
        const data = await res.json();

        if (!data.success) {
            throw new Error(data.error || 'Error al cargar empresas');
        }

        box.innerHTML = "";

        if (!data.empresas || data.empresas.length === 0) {
            box.innerHTML = '<div class="col-12"><p class="text-muted text-center">No hay empresas disponibles para patrocinar.</p></div>';
            return;
        }

        data.empresas.forEach(emp => {
            const col = document.createElement('div');
            col.className = 'col-md-4 col-sm-6 mb-3';
            const inicial = (emp.nombre || 'E').charAt(0).toUpperCase();
            
            // Determinar qué avatar mostrar
            let avatarHTML = '';
            if (emp.foto_perfil) {
                avatarHTML = `
                    <div class="position-relative d-inline-block mr-3" style="width: 50px; height: 50px;">
                        <img src="${emp.foto_perfil}" alt="${emp.nombre}" class="rounded-circle patrocinador-avatar-img" 
                             style="width: 50px; height: 50px; object-fit: cover; border: 2px solid #E9ECEF; transition: all 0.3s; position: absolute; top: 0; left: 0; z-index: 2;"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="rounded-circle bg-gradient-primary text-white d-flex align-items-center justify-content-center patrocinador-fallback" 
                             style="width: 50px; height: 50px; font-weight: 600; font-size: 1.1rem; display: none; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); position: absolute; top: 0; left: 0; z-index: 1;">
                            ${inicial}
                        </div>
                    </div>
                `;
            } else {
                avatarHTML = `
                    <div class="rounded-circle bg-gradient-primary text-white d-flex align-items-center justify-content-center mr-3" 
                         style="width: 50px; height: 50px; font-weight: 600; font-size: 1.1rem; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%);">
                        ${inicial}
                    </div>
                `;
            }
            
            col.innerHTML = `
                <div class="card border patrocinador-card" style="border-radius: 12px; transition: all 0.3s ease; border: 2px solid #E9ECEF; cursor: pointer; overflow: hidden;" 
                     onmouseover="this.style.borderColor='#00A36C'; this.style.boxShadow='0 4px 12px rgba(0,163,108,0.15)';" 
                     onmouseout="this.style.borderColor='#E9ECEF'; this.style.boxShadow='none';"
                     onclick="document.getElementById('patrocinador_${emp.id}').click();">
                    <div class="card-body p-3">
                        <div class="form-check m-0">
                            <input class="form-check-input" type="checkbox" name="patrocinadores[]" value="${emp.id}" id="patrocinador_${emp.id}" 
                                   onchange="this.closest('.patrocinador-card').style.borderColor = this.checked ? '#00A36C' : '#E9ECEF'; this.closest('.patrocinador-card').style.background = this.checked ? '#F0FDF4' : 'white';">
                            <label class="form-check-label d-flex align-items-center m-0" for="patrocinador_${emp.id}" style="cursor: pointer; width: 100%;">
                                ${avatarHTML}
                                <div class="flex-grow-1">
                                    <strong style="font-size: 0.95rem; color: #0C2B44; font-weight: 600; display: block; margin-bottom: 2px;">${emp.nombre || 'Sin nombre'}</strong>
                                    ${emp.NIT ? `<small class="text-muted" style="font-size: 0.8rem;">NIT: ${emp.NIT}</small>` : ''}
                                    ${emp.descripcion ? `<small class="text-muted d-block mt-1" style="font-size: 0.75rem; line-height: 1.3;">${emp.descripcion.substring(0, 50)}${emp.descripcion.length > 50 ? '...' : ''}</small>` : ''}
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            `;
            box.appendChild(col);
        });

    } catch (error) {
        console.error("Error cargando patrocinadores:", error);
        box.innerHTML = `<div class="col-12">
            <div class="alert alert-warning" style="border-radius: 8px; border-left: 4px solid #ffc107;">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <strong>Error de conexión</strong><br>
                <small>${error.message || 'Error desconocido'}</small><br>
                <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="loadPatrocinadores()">
                    <i class="fas fa-redo mr-1"></i> Reintentar
                </button>
            </div>
        </div>`;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    initMap();
    const token = localStorage.getItem('token');
    const tipoUsuario = localStorage.getItem('tipo_usuario');
    
    if (!token || tipoUsuario !== 'ONG') {
            Swal.fire({
                icon: 'warning',
                title: 'Acceso denegado',
                text: 'Debes iniciar sesión como ONG para crear mega eventos.',
            confirmButtonText: 'Ir al Login'
        }).then(() => {
            window.location.href = '/login';
        });
        return;
    }

    // Cargar patrocinadores disponibles
    loadPatrocinadores();

    // Preview de imágenes desde archivo
    document.getElementById('imagenes').addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        selectedFiles = [...selectedFiles, ...files];
        updatePreview();
        
        // Actualizar label del custom-file-input mejorado
        const label = document.querySelector('.custom-file-label-improved .file-label-text');
        if (label && files.length > 0) {
            label.textContent = files.length === 1 ? files[0].name : `${files.length} archivos seleccionados`;
        } else if (label) {
            label.textContent = 'Seleccionar archivos...';
        }
    });

    // CORRECCIÓN ERROR 5: Mejorar validación de URLs antes de agregarlas
    document.getElementById('btnAgregarUrl').addEventListener('click', async function() {
        const urlInput = document.getElementById('imagen_url');
        const url = urlInput.value.trim();
        
        if (!url) {
            Swal.fire({
                icon: 'warning',
                title: 'URL vacía',
                text: 'Por favor ingresa una URL válida'
            });
            return;
        }

        // Validar que sea una URL válida
        try {
            new URL(url);
        } catch (e) {
            Swal.fire({
                icon: 'error',
                title: 'URL inválida',
                text: 'Por favor ingresa una URL válida (ej: https://ejemplo.com/imagen.jpg)'
            });
            return;
        }

        // Verificar que sea una imagen (por extensión)
        const imageExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.webp'];
        const isImage = imageExtensions.some(ext => url.toLowerCase().includes(ext)) || 
                       url.match(/\.(jpg|jpeg|png|gif|webp)(\?|$)/i);

        if (!isImage) {
            Swal.fire({
                icon: 'warning',
                title: 'URL no parece ser una imagen',
                text: 'La URL debe apuntar a una imagen (JPG, PNG, GIF, WEBP)',
                showCancelButton: true,
                confirmButtonText: 'Agregar de todos modos',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    addUrlImage(url);
                    urlInput.value = '';
                }
            });
        } else {
            // CORRECCIÓN ERROR 5: Usar función async addUrlImage que valida accesibilidad
            await addUrlImage(url);
            urlInput.value = '';
        }
    });

    // Permitir agregar URL con Enter
    document.getElementById('imagen_url').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('btnAgregarUrl').click();
        }
    });

    // Validar fecha_fin después de fecha_inicio
    document.getElementById('fecha_inicio').addEventListener('change', function() {
        const fechaInicio = this.value;
        const fechaFin = document.getElementById('fecha_fin');
        if (fechaInicio && fechaFin.value && fechaFin.value <= fechaInicio) {
            fechaFin.value = '';
            Swal.fire({
                icon: 'warning',
                title: 'Fecha inválida',
                text: 'La fecha de fin debe ser posterior a la fecha de inicio'
            });
        }
        fechaFin.min = fechaInicio;
    });
});

// CORRECCIÓN ERROR 9: Implementar fallback con placeholders cuando las imágenes fallen al cargar
function updatePreview() {
    const container = document.getElementById('previewContainer');
    container.innerHTML = '';

    if (selectedFiles.length === 0) {
        return;
    }

    selectedFiles.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const wrapper = document.createElement('div');
            wrapper.className = 'image-preview-wrapper';
            
            const img = document.createElement('img');
            img.src = e.target.result;
            img.alt = `Preview ${index + 1}`;
            img.style.cssText = 'width: 150px; height: 150px; object-fit: cover; border-radius: 12px; border: 2px solid #e9ecef; margin: 8px; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);';
            
            // CORRECCIÓN ERROR 9: Manejo de errores robusto con placeholder
            img.onerror = function() {
                console.error(`[DEBUG] Error cargando preview de imagen ${index + 1}:`, file.name);
                this.onerror = null; // Prevenir loops infinitos
                // Crear placeholder SVG
                this.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="150" height="150"%3E%3Crect fill="%23f8f9fa" width="150" height="150"/%3E%3Ctext x="50%25" y="50%25" text-anchor="middle" dy=".3em" fill="%23adb5bd" font-family="Arial" font-size="12"%3EError cargando%3C/text%3E%3C/svg%3E';
                this.style.objectFit = 'contain';
                this.style.padding = '10px';
            };
            
            img.onclick = () => {
                // Abrir imagen en nueva ventana si es posible
                const newWindow = window.open();
                if (newWindow) {
                    newWindow.document.write(`<img src="${e.target.result}" style="max-width: 100%; height: auto;">`);
                }
            };
            
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'remove-image';
            removeBtn.title = 'Eliminar';
            removeBtn.innerHTML = '<i class="fas fa-times"></i>';
            removeBtn.onclick = () => removeImage(index);
            
            wrapper.appendChild(img);
            wrapper.appendChild(removeBtn);
            container.appendChild(wrapper);
        };
        
        reader.onerror = function() {
            console.error(`[DEBUG] Error leyendo archivo ${file.name}:`, file);
            // Mostrar mensaje de error en el contenedor
            const errorDiv = document.createElement('div');
            errorDiv.className = 'alert alert-warning';
            errorDiv.innerHTML = `<i class="fas fa-exclamation-triangle mr-2"></i>Error al leer archivo: ${file.name}`;
            container.appendChild(errorDiv);
        };
        
        reader.readAsDataURL(file);
    });
}

// CORRECCIÓN ERROR 5: Agregar validación para verificar que las URLs de imágenes sean accesibles
async function addUrlImage(url) {
    console.log('[DEBUG] Intentando agregar imagen URL:', url);
    
    // Verificar si la URL ya existe
    if (urlImages.includes(url)) {
        Swal.fire({
            icon: 'warning',
            title: 'URL duplicada',
            text: 'Esta URL ya ha sido agregada'
        });
        return;
    }

    // CORRECCIÓN ERROR 5: Validar que la URL sea accesible antes de agregarla
    try {
        // Mostrar loading mientras se valida
        const loadingToast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });
        
        loadingToast.fire({
            icon: 'info',
            title: 'Validando URL...'
        });

        // Intentar cargar la imagen para verificar que sea accesible
        const img = new Image();
        const isValid = await new Promise((resolve) => {
            img.onload = () => {
                console.log('[DEBUG] URL válida y accesible:', url);
                resolve(true);
            };
            img.onerror = () => {
                console.warn('[DEBUG] URL no accesible o no es una imagen válida:', url);
                resolve(false);
            };
            // Timeout de 5 segundos
            setTimeout(() => {
                console.warn('[DEBUG] Timeout validando URL:', url);
                resolve(false);
            }, 5000);
            img.src = url;
        });

        if (!isValid) {
            // Preguntar si quiere agregar de todos modos
            const result = await Swal.fire({
                icon: 'warning',
                title: 'URL no accesible',
                html: `<p>No se pudo validar que la URL sea accesible:</p><p style="font-size: 0.85rem; color: #6c757d; word-break: break-all;">${url}</p>`,
                showCancelButton: true,
                confirmButtonText: 'Agregar de todos modos',
                cancelButtonText: 'Cancelar'
            });

            if (!result.isConfirmed) {
                return;
            }
        }

        urlImages.push(url);
        updateUrlImagesPreview();
        
        Swal.fire({
            icon: 'success',
            title: 'URL agregada',
            text: 'La imagen se agregó correctamente',
            timer: 1500,
            timerProgressBar: true,
            showConfirmButton: false
        });
    } catch (error) {
        console.error('[DEBUG] Error validando URL:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Ocurrió un error al validar la URL'
        });
    }
}

// CORRECCIÓN ERROR 9: Implementar fallback robusto con placeholders y validación de URLs
function updateUrlImagesPreview() {
    const container = document.getElementById('urlImagesContainer');
    container.innerHTML = '';

    if (urlImages.length === 0) {
        return;
    }

    urlImages.forEach((url, index) => {
        console.log(`[DEBUG] Procesando imagen URL ${index + 1}:`, url);
        
        const colDiv = document.createElement('div');
        colDiv.className = 'col-md-3 col-sm-4 col-6 mb-3';
        
        const wrapper = document.createElement('div');
        wrapper.className = 'image-preview-wrapper position-relative';
        wrapper.style.cssText = 'border-radius: 8px; overflow: hidden; border: 2px solid #28a745;';
        
        const img = document.createElement('img');
        img.src = url;
        img.alt = `Imagen URL ${index + 1}`;
        img.className = 'img-fluid';
        img.style.cssText = 'width: 100%; height: 150px; object-fit: cover; cursor: pointer; transition: all 0.3s ease;';
        img.onclick = () => window.open(url, '_blank');
        
        // CORRECCIÓN ERROR 9: Manejo de errores robusto con placeholder y logging
        img.onerror = function() {
            console.error(`[DEBUG] Error cargando imagen URL ${index + 1}:`, url);
            this.onerror = null; // Prevenir loops infinitos
            // Placeholder SVG mejorado
            this.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="150" height="150"%3E%3Crect fill="%23f8f9fa" width="150" height="150"/%3E%3Ctext x="50%25" y="40%25" text-anchor="middle" fill="%23dc3545" font-family="Arial" font-size="11" font-weight="bold"%3EError%3C/text%3E%3Ctext x="50%25" y="60%25" text-anchor="middle" fill="%23adb5bd" font-family="Arial" font-size="9"%3EURL no accesible%3C/text%3E%3C/svg%3E';
            this.style.objectFit = 'contain';
            this.style.padding = '10px';
            this.style.border = '2px dashed #dc3545';
            
            // Agregar tooltip con la URL que falló
            this.title = `Error cargando: ${url}`;
        };
        
        // CORRECCIÓN ERROR 9: Agregar evento onload para confirmar que la imagen se cargó correctamente
        img.onload = function() {
            console.log(`[DEBUG] Imagen URL ${index + 1} cargada correctamente:`, url);
        };
        
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'remove-image btn btn-danger btn-sm';
        removeBtn.style.cssText = 'position: absolute; top: 5px; right: 5px; width: 30px; height: 30px; border-radius: 50%; padding: 0; display: flex; align-items: center; justify-content: center; z-index: 10;';
        removeBtn.title = 'Eliminar imagen';
        removeBtn.innerHTML = '<i class="fas fa-times"></i>';
        removeBtn.onclick = () => removeUrlImage(index);
        
        wrapper.appendChild(img);
        wrapper.appendChild(removeBtn);
        colDiv.appendChild(wrapper);
        container.appendChild(colDiv);
    });
}

function removeUrlImage(index) {
    urlImages.splice(index, 1);
    updateUrlImagesPreview();
}

function removeImage(index) {
    selectedFiles.splice(index, 1);
    updatePreview();
    // Actualizar el input file
    const input = document.getElementById('imagenes');
    const dt = new DataTransfer();
    selectedFiles.forEach(file => dt.items.add(file));
    input.files = dt.files;
}

document.getElementById('createMegaEventoForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const token = localStorage.getItem('token');
    const ongId = parseInt(localStorage.getItem('id_entidad') || localStorage.getItem('id_usuario'), 10);
    const formMessage = document.getElementById('formMessage');

    // Validar fechas
    const fechaInicio = document.getElementById('fecha_inicio').value;
    const fechaFin = document.getElementById('fecha_fin').value;

    if (new Date(fechaFin) <= new Date(fechaInicio)) {
        formMessage.className = 'alert alert-danger';
        formMessage.textContent = 'La fecha de fin debe ser posterior a la fecha de inicio';
        formMessage.style.display = 'block';
        return;
    }

    // Mostrar loading
    Swal.fire({
        title: 'Creando mega evento...',
        text: 'Por favor espera',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    try {
        const formData = new FormData();
        formData.append('titulo', document.getElementById('titulo').value);
        formData.append('descripcion', document.getElementById('descripcion').value);
        formData.append('fecha_inicio', fechaInicio);
        formData.append('fecha_fin', fechaFin);
        formData.append('ubicacion', document.getElementById('ubicacion').value);
        formData.append('lat', document.getElementById('lat').value || '');
        formData.append('lng', document.getElementById('lng').value || '');
        formData.append('categoria', document.getElementById('categoria').value);
        formData.append('estado', document.getElementById('estado').value);
        formData.append('ong_organizadora_principal', ongId);
        
        // Capacidad máxima - manejar correctamente valores vacíos
        const capacidadValue = document.getElementById('capacidad_maxima').value.trim();
        if (capacidadValue !== '' && !isNaN(capacidadValue) && parseInt(capacidadValue) > 0) {
            formData.append('capacidad_maxima', parseInt(capacidadValue));
        } else {
            // Enviar cadena vacía para que el backend lo convierta a null
            formData.append('capacidad_maxima', '');
        }
        
        formData.append('es_publico', document.getElementById('es_publico').value);
        formData.append('activo', document.getElementById('activo').value);

        // CORRECCIÓN ERROR 1: Verificar cómo se envían las imágenes al backend con console.logs detallados
        console.log('[DEBUG] === ENVIANDO IMÁGENES AL BACKEND ===');
        console.log('[DEBUG] Archivos seleccionados:', selectedFiles.length);
        selectedFiles.forEach((file, index) => {
            console.log(`[DEBUG] Archivo ${index + 1}:`, {
                name: file.name,
                size: file.size,
                type: file.type,
                lastModified: file.lastModified
            });
            // CORRECCIÓN ERROR 1: Asegurar que se envíen correctamente con el nombre 'imagenes[]'
            formData.append('imagenes[]', file, file.name);
        });

        // CORRECCIÓN ERROR 1: Verificar envío de URLs de imágenes
        console.log('[DEBUG] URLs de imágenes:', urlImages.length);
        if (urlImages.length > 0) {
            urlImages.forEach((url, index) => {
                console.log(`[DEBUG] URL ${index + 1}:`, url);
            });
            // CORRECCIÓN ERROR 1: Asegurar que se envíe como JSON string
            formData.append('imagenes_urls', JSON.stringify(urlImages));
            console.log('[DEBUG] URLs enviadas como JSON:', JSON.stringify(urlImages));
        }

        // Agregar patrocinadores seleccionados
        const patrocinadoresCheckboxes = document.querySelectorAll('input[name="patrocinadores[]"]:checked');
        const patrocinadoresIds = Array.from(patrocinadoresCheckboxes).map(cb => parseInt(cb.value));
        if (patrocinadoresIds.length > 0) {
            formData.append('patrocinadores', JSON.stringify(patrocinadoresIds));
        }

        // CORRECCIÓN ERROR 8: Agregar console.logs detallados para debuggear URLs generadas
        console.log('[DEBUG] === ENVIANDO REQUEST AL BACKEND ===');
        console.log('[DEBUG] URL:', `${API_BASE_URL}/api/mega-eventos`);
        console.log('[DEBUG] Método: POST');
        console.log('[DEBUG] Total de datos en FormData:');
        for (let pair of formData.entries()) {
            if (pair[1] instanceof File) {
                console.log(`[DEBUG] ${pair[0]}: [File] ${pair[1].name} (${pair[1].size} bytes, ${pair[1].type})`);
            } else {
                console.log(`[DEBUG] ${pair[0]}:`, pair[1]);
            }
        }

        const res = await fetch(`${API_BASE_URL}/api/mega-eventos`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
                // NO incluir 'Content-Type' cuando se envía FormData, el navegador lo hace automáticamente
            },
            body: formData
        });

        // CORRECCIÓN ERROR 6: Validar Content-Type antes de parsear JSON
        const contentType = res.headers.get('content-type');
        console.log('[DEBUG] === RESPUESTA DEL SERVIDOR ===');
        console.log('[DEBUG] Status:', res.status);
        console.log('[DEBUG] Content-Type:', contentType);
        
        let data;
        const text = await res.text();
        console.log('[DEBUG] Response Text (primeros 500 caracteres):', text.substring(0, 500));
        
        // Validar que el Content-Type sea JSON
        if (!contentType || !contentType.includes('application/json')) {
            console.error('[DEBUG] ERROR: El servidor no devolvió JSON. Content-Type:', contentType);
            console.error('[DEBUG] Respuesta completa:', text);
            
            let errorMessage = 'El servidor devolvió una respuesta no válida';
            if (res.status === 404) {
                errorMessage = 'Endpoint no encontrado (404)';
            } else if (res.status === 500) {
                errorMessage = 'Error interno del servidor (500)';
            } else if (res.status === 403) {
                errorMessage = 'No tienes permisos para realizar esta acción (403)';
            } else if (res.status === 422) {
                errorMessage = 'Error de validación (422)';
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Error de respuesta',
                html: `<p>${errorMessage}</p><p style="font-size: 0.85rem; color: #6c757d;">Status: ${res.status}</p>`,
                confirmButtonColor: '#dc3545'
            });
            return;
        }
        
        // Si el Content-Type es JSON, intentar parsear
        try {
            data = JSON.parse(text);
            console.log('[DEBUG] Response Data (parsed):', data);
        } catch (e) {
            console.error('[DEBUG] Error parseando respuesta JSON:', e);
            Swal.fire({
                icon: 'error',
                title: 'Error de respuesta',
                text: 'El servidor devolvió JSON inválido',
                confirmButtonColor: '#dc3545'
            });
            return;
        }

        if (!res.ok || !data.success) {
            console.error('[DEBUG] Error en la respuesta:', data);
            let errorMessage = data.error || 'Error al crear el mega evento';
            
            // Si hay errores de validación, mostrarlos
            if (data.errors) {
                const errorsList = Object.entries(data.errors)
                    .map(([key, value]) => `${key}: ${Array.isArray(value) ? value.join(', ') : value}`)
                    .join('\n');
                errorMessage += '\n\nDetalles:\n' + errorsList;
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Error al crear',
                text: errorMessage,
                confirmButtonColor: '#dc3545',
                width: '600px'
            });
            return;
        }

        // CORRECCIÓN ERROR 8: Log detallado de las URLs de imágenes generadas
        console.log('[DEBUG] === MEGA EVENTO CREADO EXITOSAMENTE ===');
        console.log('[DEBUG] Mega Evento ID:', data.mega_evento?.mega_evento_id);
        console.log('[DEBUG] Título:', data.mega_evento?.titulo);
        
        if (data.mega_evento?.imagenes && Array.isArray(data.mega_evento.imagenes)) {
            console.log('[DEBUG] Imágenes guardadas:', data.mega_evento.imagenes.length);
            data.mega_evento.imagenes.forEach((imgUrl, index) => {
                console.log(`[DEBUG] Imagen ${index + 1} URL:`, imgUrl);
                // Verificar si la URL es accesible
                const img = new Image();
                img.onload = () => {
                    console.log(`[DEBUG] ✓ Imagen ${index + 1} es accesible:`, imgUrl);
                };
                img.onerror = () => {
                    console.error(`[DEBUG] ✗ Imagen ${index + 1} NO es accesible (403/404):`, imgUrl);
                };
                img.src = imgUrl;
            });
        } else {
            console.warn('[DEBUG] No se recibieron imágenes en la respuesta');
        }

        Swal.fire({
            icon: 'success',
            title: '¡Mega evento creado!',
            text: 'El mega evento se ha creado correctamente',
            confirmButtonText: 'Ver mega eventos',
            timer: 2000,
            timerProgressBar: true
        }).then(() => {
            window.location.href = '{{ route("ong.mega-eventos.index") }}';
        });

    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error de conexión',
            text: 'No se pudo conectar con el servidor'
        });
    }
});
</script>
@endpush


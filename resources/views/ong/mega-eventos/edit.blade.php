@extends('layouts.adminlte')

@section('page_title', 'Editar Mega Evento')

@section('content_body')
<div class="container-fluid px-0">
    <!-- Header Minimalista -->
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 16px; overflow: hidden; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%);">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div class="mb-3 mb-md-0">
                    <h2 class="mb-2 text-white" style="font-weight: 600; font-size: 1.75rem;">
                        <i class="fas fa-edit mr-2"></i>Editar Mega Evento
                    </h2>
                    <p class="mb-0 text-white-50" style="font-size: 0.95rem;">Modifica la información de tu evento y actualiza los cambios</p>
                </div>
                <div>
                    <a href="{{ route('ong.mega-eventos.show', request()->segment(3)) }}" class="btn btn-light btn-sm px-3 mb-2 mb-md-0 d-block d-md-inline-block" style="border-radius: 8px;">
                        <i class="fas fa-eye mr-2"></i>Ver Detalles
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Botón Volver separado -->
    <div class="mb-3">
        <a href="{{ route('ong.mega-eventos.index') }}" class="btn btn-outline-secondary btn-sm" style="border-radius: 8px;">
            <i class="fas fa-arrow-left mr-2"></i>Volver al listado
        </a>
    </div>

    <!-- Loading State -->
    <div id="loadingMessage" class="text-center py-5">
        <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem; border-width: 4px;">
            <span class="sr-only">Cargando...</span>
        </div>
        <p class="text-muted font-weight-medium">Cargando información del mega evento...</p>
    </div>

    <!-- Formulario -->
    <form id="editMegaEventoForm" enctype="multipart/form-data" style="display: none;">
        <div class="row">
            <!-- Columna Principal -->
            <div class="col-lg-8">
                <!-- Información Básica -->
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="icon-box mr-3" style="width: 48px; height: 48px; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-info-circle text-white"></i>
                            </div>
                            <h5 class="mb-0 font-weight-bold" style="color: #2c3e50; font-size: 1.1rem;">Información Básica</h5>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="titulo" class="form-label font-weight-medium" style="color: #495057; font-size: 0.9rem;">
                                <i class="fas fa-heading mr-2 text-primary"></i>Título del Mega Evento <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="titulo" name="titulo" class="form-control form-control-lg" required maxlength="200" 
                                   style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 0.75rem 1rem;">
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group mb-3">
                                    <label for="categoria" class="form-label font-weight-medium" style="color: #495057; font-size: 0.9rem;">
                                        <i class="fas fa-tag mr-2 text-info"></i>Categoría
                                    </label>
                                    <select id="categoria" name="categoria" class="form-control" style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 0.75rem 1rem;">
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

                        <div class="form-group mb-0">
                            <label for="descripcion" class="form-label font-weight-medium" style="color: #495057; font-size: 0.9rem;">
                                <i class="fas fa-align-left mr-2 text-secondary"></i>Descripción
                            </label>
                            <textarea id="descripcion" name="descripcion" rows="5" class="form-control" 
                                      style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 0.75rem 1rem; resize: vertical;"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Fechas del Evento -->
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="icon-box mr-3" style="width: 48px; height: 48px; background: linear-gradient(135deg, #00A36C 0%, #0C2B44 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-calendar-alt text-white"></i>
                            </div>
                            <h5 class="mb-0 font-weight-bold" style="color: #2c3e50; font-size: 1.1rem;">Fechas del Evento</h5>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="fecha_inicio" class="form-label font-weight-medium" style="color: #495057; font-size: 0.9rem;">
                                        <i class="fas fa-play-circle mr-2 text-success"></i>Fecha y Hora de Inicio <span class="text-danger">*</span>
                                    </label>
                                    <input type="datetime-local" id="fecha_inicio" name="fecha_inicio" class="form-control" required 
                                           style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 0.75rem 1rem;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label for="fecha_fin" class="form-label font-weight-medium" style="color: #495057; font-size: 0.9rem;">
                                        <i class="fas fa-stop-circle mr-2 text-danger"></i>Fecha y Hora de Fin <span class="text-danger">*</span>
                                    </label>
                                    <input type="datetime-local" id="fecha_fin" name="fecha_fin" class="form-control" required 
                                           style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 0.75rem 1rem;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ubicación y Mapa -->
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="icon-box mr-3" style="width: 48px; height: 48px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-map-marker-alt text-white"></i>
                            </div>
                            <h5 class="mb-0 font-weight-bold" style="color: #2c3e50; font-size: 1.1rem;">Ubicación y Mapa</h5>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label font-weight-medium" style="color: #495057; font-size: 0.9rem;">
                                <i class="fas fa-map mr-2 text-primary"></i>Seleccionar Ubicación en el Mapa
                            </label>
                            <div id="map" class="rounded" style="height: 350px; border: 2px solid #e0e0e0; border-radius: 12px; overflow: hidden;"></div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="locacion" class="form-label font-weight-medium" style="color: #495057; font-size: 0.9rem;">
                                <i class="fas fa-location-arrow mr-2 text-info"></i>Dirección Detectada
                            </label>
                            <input id="locacion" readonly class="form-control bg-light" 
                                   style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 0.75rem 1rem; font-style: italic;">
                            <small id="ciudadInfo" class="form-text text-muted mt-1">
                                <i class="fas fa-city mr-1"></i><span>Ciudad: Sin especificar</span>
                            </small>
                        </div>

                        <div class="form-group mb-0">
                            <label for="ubicacion" class="form-label font-weight-medium" style="color: #495057; font-size: 0.9rem;">
                                <i class="fas fa-map-pin mr-2 text-warning"></i>Ubicación (Texto)
                            </label>
                            <input type="text" id="ubicacion" name="ubicacion" class="form-control" maxlength="500" 
                                   style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 0.75rem 1rem;"
                                   placeholder="Escribe la ubicación o selecciona en el mapa">
                            <small class="form-text text-muted mt-1">
                                <i class="fas fa-info-circle mr-1"></i>Puedes escribir manualmente o seleccionar en el mapa
                            </small>
                        </div>

                        <input type="hidden" id="lat" name="lat">
                        <input type="hidden" id="lng" name="lng">
                    </div>
                </div>

                <!-- Imágenes Existentes -->
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="icon-box mr-3" style="width: 48px; height: 48px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-images text-white"></i>
                            </div>
                            <h5 class="mb-0 font-weight-bold" style="color: #2c3e50; font-size: 1.1rem;">Imágenes Actuales</h5>
                        </div>
                        <div id="existingImagesContainer" class="row"></div>
                    </div>
                </div>

                <!-- Agregar Nuevas Imágenes -->
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="icon-box mr-3" style="width: 48px; height: 48px; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-plus-circle text-white"></i>
                            </div>
                            <h5 class="mb-0 font-weight-bold" style="color: #2c3e50; font-size: 1.1rem;">Agregar Nuevas Imágenes</h5>
                        </div>

                        <div class="form-group mb-4">
                            <label for="nuevas_imagenes" class="form-label font-weight-medium" style="color: #495057; font-size: 0.9rem;">
                                <i class="fas fa-upload mr-2 text-primary"></i>Subir Imágenes desde Archivo
                            </label>
                            <div class="custom-file">
                                <input type="file" id="nuevas_imagenes" name="nuevas_imagenes[]" multiple 
                                       accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" 
                                       class="custom-file-input" style="border-radius: 8px;">
                                <label class="custom-file-label" for="nuevas_imagenes" style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 0.75rem 1rem;">
                                    <i class="fas fa-folder-open mr-2"></i>Seleccionar archivos
                                </label>
                            </div>
                            <small class="form-text text-muted mt-2">
                                <i class="fas fa-info-circle mr-1"></i>Formatos permitidos: JPG, PNG, GIF, WEBP (máx. 5MB cada una)
                            </small>
                        </div>

                        <div id="previewContainer" class="row mb-4"></div>

                        <div class="form-group mb-0">
                            <label for="imagen_url_edit" class="form-label font-weight-medium" style="color: #495057; font-size: 0.9rem;">
                                <i class="fas fa-link mr-2 text-success"></i>Agregar Imagen por URL
                            </label>
                            <div class="input-group">
                                <input type="url" id="imagen_url_edit" name="imagen_url_edit" class="form-control" 
                                       placeholder="https://ejemplo.com/imagen.jpg"
                                       style="border-radius: 8px 0 0 8px; border: 1px solid #e0e0e0; padding: 0.75rem 1rem;">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-primary" id="btnAgregarUrlEdit" style="border-radius: 0 8px 8px 0;">
                                        <i class="fas fa-plus mr-2"></i>Agregar
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div id="urlImagesContainerEdit" class="row mt-3"></div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Configuración -->
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; position: sticky; top: 20px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="icon-box mr-3" style="width: 48px; height: 48px; background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-cog text-white"></i>
                            </div>
                            <h5 class="mb-0 font-weight-bold" style="color: #2c3e50; font-size: 1.1rem;">Configuración</h5>
                        </div>

                        <div class="form-group mb-3">
                            <label for="estado" class="form-label font-weight-medium" style="color: #495057; font-size: 0.9rem;">
                                <i class="fas fa-toggle-on mr-2 text-info"></i>Estado
                            </label>
                            <select id="estado" name="estado" class="form-control" style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 0.75rem 1rem;">
                                <option value="planificacion">Planificación</option>
                                <option value="activo">Activo</option>
                                <option value="en_curso">En Curso</option>
                                <option value="finalizado">Finalizado</option>
                                <option value="cancelado">Cancelado</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="capacidad_maxima" class="form-label font-weight-medium" style="color: #495057; font-size: 0.9rem;">
                                <i class="fas fa-users mr-2 text-warning"></i>Capacidad Máxima
                            </label>
                            <input type="number" id="capacidad_maxima" name="capacidad_maxima" class="form-control" min="1" 
                                   style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 0.75rem 1rem;"
                                   placeholder="Ej: 100">
                        </div>

                        <div class="form-group mb-3">
                            <label for="es_publico" class="form-label font-weight-medium" style="color: #495057; font-size: 0.9rem;">
                                <i class="fas fa-globe mr-2 text-success"></i>Visibilidad
                            </label>
                            <select id="es_publico" name="es_publico" class="form-control" style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 0.75rem 1rem;">
                                <option value="1">Público</option>
                                <option value="0">Privado</option>
                            </select>
                        </div>

                        <div class="form-group mb-0">
                            <label for="activo" class="form-label font-weight-medium" style="color: #495057; font-size: 0.9rem;">
                                <i class="fas fa-power-off mr-2 text-danger"></i>Estado de Actividad
                            </label>
                            <select id="activo" name="activo" class="form-control" style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 0.75rem 1rem;">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Acciones -->
                <div class="card shadow-sm border-0" style="border-radius: 12px; position: sticky; top: 400px;">
                    <div class="card-body p-4">
                        <div id="formMessage" class="alert mb-3" style="display: none; border-radius: 8px;"></div>
                        
                        <button type="submit" class="btn btn-lg btn-block font-weight-bold" 
                                style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); color: white; border: none; border-radius: 10px; padding: 0.875rem; box-shadow: 0 4px 15px rgba(12, 43, 68, 0.3);">
                            <i class="fas fa-save mr-2"></i>Guardar Cambios
                        </button>
                        
                        <a href="{{ route('ong.mega-eventos.index') }}" class="btn btn-outline-secondary btn-block mt-2" 
                           style="border-radius: 10px; padding: 0.875rem; border-width: 2px;">
                            <i class="fas fa-times mr-2"></i>Cancelar
                        </a>
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
    body {
        background-color: #f8f9fa;
    }

    .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
    }

    .form-control:focus, .custom-file-input:focus ~ .custom-file-label {
        border-color: #00A36C;
        box-shadow: 0 0 0 0.2rem rgba(0, 163, 108, 0.15);
    }

    .custom-file-label::after {
        background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%);
        color: white;
        border-left: 1px solid #00A36C;
    }

    #previewContainer img, #existingImagesContainer img, #urlImagesContainerEdit img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 12px;
        border: 2px solid #e0e0e0;
        transition: all 0.3s ease;
    }

    #previewContainer img:hover, #existingImagesContainer img:hover, #urlImagesContainerEdit img:hover {
        transform: scale(1.05);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        border-color: #00A36C;
    }

    .image-preview-wrapper {
        position: relative;
        margin-bottom: 1rem;
        border-radius: 12px;
        overflow: hidden;
    }

    .remove-image {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: rgba(220, 53, 69, 0.9);
        color: white;
        border: 2px solid white;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        transition: all 0.2s ease;
        z-index: 10;
    }

    .remove-image:hover {
        background: #dc3545;
        transform: scale(1.1);
    }

    .icon-box {
        transition: transform 0.3s ease;
    }

    .icon-box:hover {
        transform: rotate(5deg) scale(1.05);
    }

    #map {
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .btn-primary, .btn-success {
        transition: all 0.3s ease;
    }

    .btn-primary:hover, .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }

    @media (max-width: 991px) {
        .card[style*="sticky"] {
            position: relative !important;
            top: 0 !important;
        }
    }
</style>
@endpush

@push('js')
<script src="{{ asset('js/notificaciones-ong.js') }}"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="{{ asset('assets/js/config.js') }}"></script>
<script>
let megaEventoId = null;
let existingImages = [];
let selectedFiles = [];
let urlImages = [];
let map, clickMarker;
let ciudadDetectada = "";
let ongOrganizadoraPrincipal = null;

// ===============================
// 🗺️ MAPA LEAFLET
// ===============================
function initMap(lat = null, lng = null) {
    if (map) {
        map.remove();
        map = null;
    }

    const pos = lat && lng ? [parseFloat(lat), parseFloat(lng)] : [-16.5, -68.15];
    map = L.map("map").setView(pos, lat && lng ? 15 : 13);

    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    if (lat && lng) {
        const latNum = parseFloat(lat);
        const lngNum = parseFloat(lng);
        if (!isNaN(latNum) && !isNaN(lngNum)) {
            clickMarker = L.marker([latNum, lngNum]).addTo(map);
            document.getElementById("lat").value = latNum;
            document.getElementById("lng").value = lngNum;
            reverseGeocode(latNum, lngNum);
        }
    }

    map.on("click", (e) => {
        const { lat: clickedLat, lng: clickedLng } = e.latlng;
        if (clickMarker) {
            clickMarker.setLatLng(e.latlng);
        } else {
            clickMarker = L.marker(e.latlng).addTo(map);
        }
        document.getElementById("lat").value = clickedLat;
        document.getElementById("lng").value = clickedLng;
        reverseGeocode(clickedLat, clickedLng);
    });
}

// ===============================
// 🌍 GEOCODIFICACIÓN INVERSA
// ===============================
async function reverseGeocode(lat, lng) {
    try {
        const r = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json&zoom=18&addressdetails=1`);
        const data = await r.json();
        const direccion = data.display_name ?? "";
        document.getElementById("locacion").value = direccion;
        
        const ubicacionInput = document.getElementById("ubicacion");
        if (!ubicacionInput.value || ubicacionInput.value.trim() === '') {
            ubicacionInput.value = direccion;
        }

        ciudadDetectada = data.address?.city || data.address?.town || data.address?.village || data.address?.state || "Sin especificar";
        document.getElementById("ciudadInfo").innerHTML = `<i class="fas fa-city mr-1"></i><span>Ciudad: ${ciudadDetectada}</span>`;
    } catch (e) {
        console.warn("No se pudo obtener dirección:", e);
    }
}

// ===============================
// 📋 CARGAR MEGA EVENTO
// ===============================
async function loadMegaEvento() {
    const token = localStorage.getItem('token');
    const loadingMessage = document.getElementById('loadingMessage');
    const form = document.getElementById('editMegaEventoForm');

    console.log('=== CARGANDO MEGA EVENTO ===');
    console.log('Mega Evento ID:', megaEventoId);
    console.log('API URL:', `${API_BASE_URL}/api/mega-eventos/${megaEventoId}`);

    try {
        const res = await fetch(`${API_BASE_URL}/api/mega-eventos/${megaEventoId}`, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        console.log('Response status:', res.status);
        const data = await res.json();
        console.log('Response data:', data);

        if (!res.ok || !data.success) {
            loadingMessage.innerHTML = `
                <div class="alert alert-danger" style="border-radius: 12px; padding: 1.5rem;">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>Error:</strong> ${data.error || 'Error al cargar el mega evento'}
                </div>
            `;
            return;
        }

        const mega = data.mega_evento;
        
        // Guardar ong_organizadora_principal
        ongOrganizadoraPrincipal = mega.ong_organizadora_principal || localStorage.getItem('id_usuario');
        
        // Llenar formulario
        document.getElementById('titulo').value = mega.titulo || '';
        document.getElementById('descripcion').value = mega.descripcion || '';
        document.getElementById('categoria').value = mega.categoria || 'social';
        document.getElementById('estado').value = mega.estado || 'planificacion';
        document.getElementById('ubicacion').value = mega.ubicacion || '';
        
        // Capacidad máxima - manejar null correctamente
        const capacidadInput = document.getElementById('capacidad_maxima');
        if (mega.capacidad_maxima !== null && mega.capacidad_maxima !== undefined && mega.capacidad_maxima !== '' && !isNaN(mega.capacidad_maxima)) {
            const capacidadValue = parseInt(mega.capacidad_maxima);
            if (capacidadValue > 0) {
                capacidadInput.value = capacidadValue;
            } else {
                capacidadInput.value = '';
            }
        } else {
            capacidadInput.value = '';
        }
        
        document.getElementById('es_publico').value = mega.es_publico ? '1' : '0';
        document.getElementById('activo').value = mega.activo ? '1' : '0';
        
        // Log para depuración
        console.log('Capacidad máxima cargada:', {
            original: mega.capacidad_maxima,
            type: typeof mega.capacidad_maxima,
            parsed: capacidadInput.value,
            raw: mega.capacidad_maxima
        });

        // Fechas - formatear correctamente para datetime-local
        if (mega.fecha_inicio) {
            const fechaInicio = new Date(mega.fecha_inicio);
            // Ajustar a zona horaria local
            const fechaInicioLocal = new Date(fechaInicio.getTime() - fechaInicio.getTimezoneOffset() * 60000);
            document.getElementById('fecha_inicio').value = fechaInicioLocal.toISOString().slice(0, 16);
        }
        if (mega.fecha_fin) {
            const fechaFin = new Date(mega.fecha_fin);
            // Ajustar a zona horaria local
            const fechaFinLocal = new Date(fechaFin.getTime() - fechaFin.getTimezoneOffset() * 60000);
            document.getElementById('fecha_fin').value = fechaFinLocal.toISOString().slice(0, 16);
        }
        
        console.log('Fechas cargadas:', {
            inicio: document.getElementById('fecha_inicio').value,
            fin: document.getElementById('fecha_fin').value
        });

        existingImages = Array.isArray(mega.imagenes) ? mega.imagenes.filter(img => img && img.trim() !== '') : [];
        displayExistingImages();

        const lat = mega.lat ? parseFloat(mega.lat) : null;
        const lng = mega.lng ? parseFloat(mega.lng) : null;
        
        if (lat && lng && !isNaN(lat) && !isNaN(lng)) {
            document.getElementById("lat").value = lat;
            document.getElementById("lng").value = lng;
            if (!mega.ubicacion || mega.ubicacion.trim() === '') {
                reverseGeocode(lat, lng);
            }
        }
        
        initMap(lat, lng);
        loadingMessage.style.display = 'none';
        form.style.display = 'block';
        
        console.log('=== DATOS CARGADOS COMPLETAMENTE ===');
        console.log('Título:', document.getElementById('titulo').value);
        console.log('Descripción:', document.getElementById('descripcion').value);
        console.log('Categoría:', document.getElementById('categoria').value);
        console.log('Estado:', document.getElementById('estado').value);
        console.log('Capacidad máxima:', document.getElementById('capacidad_maxima').value);
        console.log('Ubicación:', document.getElementById('ubicacion').value);
        console.log('Lat:', document.getElementById('lat').value);
        console.log('Lng:', document.getElementById('lng').value);
        console.log('Es público:', document.getElementById('es_publico').value);
        console.log('Activo:', document.getElementById('activo').value);
        console.log('Fechas:', {
            inicio: document.getElementById('fecha_inicio').value,
            fin: document.getElementById('fecha_fin').value
        });
        console.log('Imágenes existentes:', existingImages.length);

    } catch (error) {
        console.error('Error:', error);
        loadingMessage.innerHTML = `
            <div class="alert alert-danger" style="border-radius: 12px; padding: 1.5rem;">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <strong>Error de conexión</strong> al cargar el mega evento.
            </div>
        `;
    }
}

// ===============================
// 🖼️ MOSTRAR IMÁGENES EXISTENTES
// ===============================
function displayExistingImages() {
    const container = document.getElementById('existingImagesContainer');
    container.innerHTML = '';

    if (existingImages.length === 0) {
        container.innerHTML = `
            <div class="col-12">
                <div class="alert alert-info text-center" style="border-radius: 12px;">
                    <i class="fas fa-image mr-2"></i>No hay imágenes cargadas
                </div>
            </div>
        `;
        return;
    }

    existingImages.forEach((imgUrl, index) => {
        if (!imgUrl || imgUrl.trim() === '') return;
        
        const wrapper = document.createElement('div');
        wrapper.className = 'col-md-6 col-sm-6 mb-3';
        
        const imageWrapper = document.createElement('div');
        imageWrapper.className = 'image-preview-wrapper';
        
        const img = document.createElement('img');
        img.src = imgUrl;
        img.alt = `Imagen ${index + 1}`;
        img.onclick = () => window.open(imgUrl, '_blank');
        img.onerror = function() {
            this.onerror = null;
            this.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="200" height="200"%3E%3Crect fill="%23f8f9fa" width="200" height="200"/%3E%3Ctext x="50%25" y="50%25" text-anchor="middle" dy=".3em" fill="%23adb5bd"%3EError%3C/text%3E%3C/svg%3E';
        };

        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'remove-image';
        removeBtn.innerHTML = '<i class="fas fa-times"></i>';
        removeBtn.onclick = () => removeExistingImage(imgUrl);

        imageWrapper.appendChild(img);
        imageWrapper.appendChild(removeBtn);
        wrapper.appendChild(imageWrapper);
        container.appendChild(wrapper);
    });
}

// ===============================
// ❌ ELIMINAR IMAGEN EXISTENTE
// ===============================
async function removeExistingImage(imgUrl) {
    const result = await Swal.fire({
        title: '¿Eliminar imagen?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        buttonsStyling: false,
        customClass: {
            confirmButton: 'btn btn-danger px-4 py-2 mr-2',
            cancelButton: 'btn btn-secondary px-4 py-2'
        }
    });

    if (!result.isConfirmed) return;

    const token = localStorage.getItem('token');
    try {
        const res = await fetch(`${API_BASE_URL}/api/mega-eventos/${megaEventoId}/imagen`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ imagen_url: imgUrl })
        });

        const data = await res.json();
        if (!res.ok || !data.success) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.error || 'Error al eliminar la imagen'
            });
            return;
        }

        existingImages = existingImages.filter(img => img !== imgUrl);
        displayExistingImages();

        Swal.fire({
            icon: 'success',
            title: '¡Eliminada!',
            text: 'La imagen ha sido eliminada',
            timer: 1500,
            timerProgressBar: true,
            showConfirmButton: false
        });
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error de Conexión',
            text: 'No se pudo conectar con el servidor'
        });
    }
}

// ===============================
// 📤 PREVIEW DE NUEVAS IMÁGENES
// ===============================
function updatePreview() {
    const container = document.getElementById('previewContainer');
    container.innerHTML = '';

    selectedFiles.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const wrapper = document.createElement('div');
            wrapper.className = 'col-md-4 col-sm-6 mb-3';
            
            const imageWrapper = document.createElement('div');
            imageWrapper.className = 'image-preview-wrapper';
            imageWrapper.innerHTML = `
                <img src="${e.target.result}" alt="Preview ${index + 1}">
                <button type="button" class="remove-image" onclick="removeNewImage(${index})" title="Eliminar">
                    <i class="fas fa-times"></i>
                </button>
            `;
            wrapper.appendChild(imageWrapper);
            container.appendChild(wrapper);
        };
        reader.readAsDataURL(file);
    });
}

// ===============================
// 🔗 AGREGAR IMAGEN POR URL
// ===============================
function addUrlImageEdit(url) {
    if (urlImages.includes(url)) {
        Swal.fire({
            icon: 'warning',
            title: 'URL duplicada',
            text: 'Esta URL ya ha sido agregada'
        });
        return;
    }
    urlImages.push(url);
    updateUrlImagesPreviewEdit();
}

function updateUrlImagesPreviewEdit() {
    const container = document.getElementById('urlImagesContainerEdit');
    container.innerHTML = '';

    if (urlImages.length === 0) return;

    urlImages.forEach((url, index) => {
        const wrapper = document.createElement('div');
        wrapper.className = 'col-md-4 col-sm-6 mb-3';
        
        const imageWrapper = document.createElement('div');
        imageWrapper.className = 'image-preview-wrapper';
        
        const img = document.createElement('img');
        img.src = url;
        img.alt = `Imagen URL ${index + 1}`;
        img.onclick = () => window.open(url, '_blank');
        img.onerror = function() {
            this.onerror = null;
            this.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="200" height="200"%3E%3Crect fill="%23f8f9fa" width="200" height="200"/%3E%3Ctext x="50%25" y="50%25" text-anchor="middle" dy=".3em" fill="%23adb5bd"%3EError%3C/text%3E%3C/svg%3E';
        };

        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'remove-image';
        removeBtn.innerHTML = '<i class="fas fa-times"></i>';
        removeBtn.onclick = () => removeUrlImageEdit(index);

        imageWrapper.appendChild(img);
        imageWrapper.appendChild(removeBtn);
        wrapper.appendChild(imageWrapper);
        container.appendChild(wrapper);
    });
}

function removeUrlImageEdit(index) {
    urlImages.splice(index, 1);
    updateUrlImagesPreviewEdit();
}

function removeNewImage(index) {
    selectedFiles.splice(index, 1);
    updatePreview();
    const input = document.getElementById('nuevas_imagenes');
    const dt = new DataTransfer();
    selectedFiles.forEach(file => dt.items.add(file));
    input.files = dt.files;
    
    // Actualizar label del custom file input
    const label = input.nextElementSibling;
    if (selectedFiles.length > 0) {
        label.innerHTML = `<i class="fas fa-folder-open mr-2"></i>${selectedFiles.length} archivo(s) seleccionado(s)`;
    } else {
        label.innerHTML = `<i class="fas fa-folder-open mr-2"></i>Seleccionar archivos`;
    }
}

// ===============================
// 🚀 INICIALIZACIÓN
// ===============================
document.addEventListener('DOMContentLoaded', async () => {
    const token = localStorage.getItem('token');
    const tipoUsuario = localStorage.getItem('tipo_usuario');
    
    if (!token || tipoUsuario !== 'ONG') {
        Swal.fire({
            icon: 'warning',
            title: 'Acceso denegado',
            text: 'Debes iniciar sesión como ONG para editar mega eventos.',
            confirmButtonText: 'Ir al Login'
        }).then(() => {
            window.location.href = '/login';
        });
        return;
    }

    // CORRECCIÓN ERROR 5: Extracción robusta del ID desde la URL
    // La ruta es: /ong/mega-eventos/{id}/editar
    // Buscar el índice de 'mega-eventos' y parsear el siguiente segmento como entero
    const segments = window.location.pathname.split('/').filter(seg => seg !== '');
    const megaEventosIndex = segments.indexOf('mega-eventos');
    
    if (megaEventosIndex !== -1 && segments[megaEventosIndex + 1]) {
        const idCandidate = segments[megaEventosIndex + 1];
        const parsedId = parseInt(idCandidate, 10);
        if (!isNaN(parsedId) && parsedId > 0) {
            megaEventoId = parsedId;
        }
    }
    
    console.log('Mega Evento ID extraído:', megaEventoId);
    console.log('Path completo:', window.location.pathname);
    console.log('Segmentos:', segments);
    
    // CORRECCIÓN ERROR 6: Validar el ID al INICIO antes de continuar
    if (!megaEventoId || isNaN(megaEventoId)) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo obtener el ID del mega evento de la URL',
            confirmButtonText: 'Volver al listado'
        }).then(() => {
            window.location.href = '/ong/mega-eventos';
        });
        return;
    }
    
    await loadMegaEvento();

    // Event listeners
    document.getElementById('nuevas_imagenes').addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        selectedFiles = [...selectedFiles, ...files];
        updatePreview();
        
        const label = this.nextElementSibling;
        label.innerHTML = `<i class="fas fa-folder-open mr-2"></i>${selectedFiles.length} archivo(s) seleccionado(s)`;
    });

    document.getElementById('btnAgregarUrlEdit').addEventListener('click', function() {
        const urlInput = document.getElementById('imagen_url_edit');
        const url = urlInput.value.trim();
        
        if (!url) {
            Swal.fire({
                icon: 'warning',
                title: 'URL vacía',
                text: 'Por favor ingresa una URL válida'
            });
            return;
        }

        try {
            new URL(url);
        } catch (e) {
            Swal.fire({
                icon: 'error',
                title: 'URL inválida',
                text: 'Por favor ingresa una URL válida'
            });
            return;
        }

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
                    addUrlImageEdit(url);
                    urlInput.value = '';
                }
            });
        } else {
            addUrlImageEdit(url);
            urlInput.value = '';
        }
    });

    document.getElementById('imagen_url_edit').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('btnAgregarUrlEdit').click();
        }
    });

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

// ===============================
// 💾 ENVIAR FORMULARIO
// ===============================
document.getElementById('editMegaEventoForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const token = localStorage.getItem('token');
    const formMessage = document.getElementById('formMessage');

    const fechaInicio = document.getElementById('fecha_inicio').value;
    const fechaFin = document.getElementById('fecha_fin').value;

    if (new Date(fechaFin) <= new Date(fechaInicio)) {
        formMessage.className = 'alert alert-danger';
        formMessage.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i>La fecha de fin debe ser posterior a la fecha de inicio';
        formMessage.style.display = 'block';
        return;
    }

    Swal.fire({
        title: 'Guardando cambios...',
        html: '<div class="spinner-border text-primary" role="status"><span class="sr-only">Cargando...</span></div><p class="mt-3">Por favor espera</p>',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // CORRECCIÓN ERROR 6: Validar ID al INICIO antes de construir FormData
    if (!megaEventoId || isNaN(megaEventoId)) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'ID de mega evento inválido',
            confirmButtonColor: '#dc3545'
        });
        return;
    }

    try {
        const formData = new FormData();
        
        // CORRECCIÓN ERROR 1 y ERROR 2: Agregar _method PUT al inicio para simular PUT con POST
        // Laravel requiere esto cuando se usa FormData con POST para simular PUT
        formData.append('_method', 'PUT');
        
        // Información básica
        formData.append('titulo', document.getElementById('titulo').value || '');
        formData.append('descripcion', document.getElementById('descripcion').value || '');
        formData.append('categoria', document.getElementById('categoria').value || 'social');
        
        // Fechas
        formData.append('fecha_inicio', fechaInicio);
        formData.append('fecha_fin', fechaFin);
        
        // Ubicación y coordenadas
        const ubicacionText = document.getElementById('ubicacion').value || '';
        const latValue = document.getElementById('lat').value || '';
        const lngValue = document.getElementById('lng').value || '';
        
        formData.append('ubicacion', ubicacionText);
        formData.append('lat', latValue);
        formData.append('lng', lngValue);
        
        // Configuración
        formData.append('estado', document.getElementById('estado').value || 'planificacion');
        
        // Capacidad máxima - manejar correctamente valores vacíos
        const capacidadInput = document.getElementById('capacidad_maxima');
        const capacidadValue = capacidadInput.value.trim();
        if (capacidadValue !== '' && !isNaN(capacidadValue) && parseInt(capacidadValue) > 0) {
            formData.append('capacidad_maxima', parseInt(capacidadValue));
        } else {
            // Enviar cadena vacía explícitamente para que el backend lo convierta a null
            formData.append('capacidad_maxima', '');
        }
        
        // Log para depuración
        console.log('Capacidad máxima enviada:', {
            value: capacidadValue,
            parsed: capacidadValue !== '' ? parseInt(capacidadValue) : null,
            sending: capacidadValue !== '' && !isNaN(capacidadValue) && parseInt(capacidadValue) > 0 ? parseInt(capacidadValue) : ''
        });
        
        formData.append('es_publico', document.getElementById('es_publico').value || '0');
        formData.append('activo', document.getElementById('activo').value || '1');
        
        // ONG Organizadora Principal (requerido)
        const ongId = ongOrganizadoraPrincipal || localStorage.getItem('id_usuario');
        if (ongId) {
            formData.append('ong_organizadora_principal', ongId);
        }
        
        // Imágenes
        selectedFiles.forEach(file => {
            formData.append('imagenes[]', file);
        });

        if (urlImages.length > 0) {
            formData.append('imagenes_urls', JSON.stringify(urlImages));
        }

        // Agregar patrocinadores seleccionados
        const patrocinadoresCheckboxes = document.querySelectorAll('input[name="patrocinadores[]"]:checked');
        const patrocinadoresIds = Array.from(patrocinadoresCheckboxes).map(cb => parseInt(cb.value));
        if (patrocinadoresIds.length > 0) {
            formData.append('patrocinadores', JSON.stringify(patrocinadoresIds));
        }

        // CORRECCIÓN ERROR 4: Simplificar lógica de procesamiento de imágenes existentes
        // Solo distinguir entre URLs externas (completas) y rutas locales
        const imagenesParaGuardar = existingImages
            .filter(imgUrl => imgUrl && imgUrl.trim() !== '') // Filtrar vacíos
            .map(imgUrl => {
                const trimmed = imgUrl.trim();
                
                // Si es URL completa externa (http/https), mantenerla tal cual
                if (trimmed.startsWith('http://') || trimmed.startsWith('https://')) {
                    try {
                        const urlObj = new URL(trimmed);
                        const host = urlObj.hostname;
                        const currentHost = window.location.hostname;
                        
                        // Si es un dominio externo (no localhost, 192.168.0.7, ni 10.26.x.x), mantener URL completa
                        if (host !== 'localhost' && 
                            host !== '192.168.0.7' && 
                            !host.startsWith('10.26.') && 
                            host !== currentHost) {
                            return trimmed; // URL externa completa
                        }
                    } catch (e) {
                        // Si no se puede parsear como URL, tratarlo como ruta local
                    }
                }
                
                // Para rutas locales, asegurar que empiecen con /storage/
                return trimmed.startsWith('/storage/') 
                    ? trimmed 
                    : '/storage/' + trimmed.replace(/^\/storage\/?/, '').replace(/^\//, '');
            })
            .filter(img => img !== null && img !== undefined);
        
        // CORRECCIÓN ERROR 3: Usar 'imagenes_existentes' en lugar de 'imagenes_json'
        // El backend espera 'imagenes_existentes' para las imágenes que ya existen en el mega evento
        formData.append('imagenes_existentes', JSON.stringify(imagenesParaGuardar));

        // Log para depuración
        console.log('Enviando datos:', {
            titulo: document.getElementById('titulo').value,
            ubicacion: ubicacionText,
            lat: latValue,
            lng: lngValue,
            ong_organizadora_principal: ongId
        });

        // Log completo de datos antes de enviar
        console.log('=== DATOS A ENVIAR ===');
        console.log('Mega Evento ID:', megaEventoId);
        console.log('Título:', document.getElementById('titulo').value);
        console.log('Descripción:', document.getElementById('descripcion').value);
        console.log('Categoría:', document.getElementById('categoria').value);
        console.log('Estado:', document.getElementById('estado').value);
        console.log('Capacidad máxima:', capacidadValue, '→ Enviando:', capacidadValue !== '' && !isNaN(capacidadValue) && parseInt(capacidadValue) > 0 ? parseInt(capacidadValue) : '');
        console.log('Ubicación:', ubicacionText);
        console.log('Lat:', latValue);
        console.log('Lng:', lngValue);
        console.log('Es público:', document.getElementById('es_publico').value);
        console.log('Activo:', document.getElementById('activo').value);
        console.log('ONG ID:', ongId);
        console.log('Fechas:', { inicio: fechaInicio, fin: fechaFin });
        console.log('Imágenes existentes:', existingImages.length);
        console.log('Archivos nuevos:', selectedFiles.length);
        console.log('URLs nuevas:', urlImages.length);
        
        // Mostrar todos los datos del FormData
        console.log('=== FORMDATA ===');
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + (pair[1] instanceof File ? `[File: ${pair[1].name}]` : pair[1]));
        }

        console.log('=== ENVIANDO REQUEST ===');
        console.log('URL:', `${API_BASE_URL}/api/mega-eventos/${megaEventoId}`);
        // CORRECCIÓN ERROR 1: Cambiar método a POST (Laravel procesa FormData mejor con POST)
        console.log('Method: POST (con _method=PUT)');
        console.log('Mega Evento ID:', megaEventoId);

        // CORRECCIÓN ERROR 1: Usar POST en lugar de PUT para FormData
        // Laravel no procesa bien FormData con PUT, por eso usamos POST con _method=PUT
        const res = await fetch(`${API_BASE_URL}/api/mega-eventos/${megaEventoId}`, {
            method: 'POST', // Cambiado de PUT a POST
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
                // NO incluir 'Content-Type' cuando se envía FormData, el navegador lo hace automáticamente
            },
            body: formData
        });

        console.log('=== RESPUESTA DEL SERVIDOR ===');
        console.log('Status:', res.status);
        console.log('Status Text:', res.statusText);
        console.log('Headers:', res.headers);

        // CORRECCIÓN ERROR 7: Validar Content-Type antes de intentar parsear como JSON
        // Esto previene errores cuando el servidor devuelve HTML (error 500, 404, etc.)
        const contentType = res.headers.get('content-type');
        console.log('Content-Type:', contentType);
        
        let data;
        const text = await res.text();
        console.log('Response Text (raw):', text.substring(0, 500)); // Limitar log a primeros 500 caracteres
        
        // Validar que el Content-Type sea JSON antes de parsear
        if (!contentType || !contentType.includes('application/json')) {
            console.error('ERROR: El servidor no devolvió JSON. Content-Type:', contentType);
            console.error('Respuesta completa:', text);
            
            // Si es un error HTML, intentar extraer información útil
            let errorMessage = 'El servidor devolvió una respuesta no válida';
            if (res.status === 404) {
                errorMessage = 'El mega evento no fue encontrado (404)';
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
        } catch (e) {
            console.error('Error parseando respuesta JSON:', e);
            Swal.fire({
                icon: 'error',
                title: 'Error de respuesta',
                text: 'El servidor devolvió JSON inválido',
                confirmButtonColor: '#dc3545'
            });
            return;
        }
        
        console.log('Response Data (parsed):', data);

        if (!res.ok || !data.success) {
            console.error('Error en la respuesta:', data);
            let errorMessage = data.error || 'Error al actualizar el mega evento';
            
            // Si hay errores de validación, mostrarlos
            if (data.errors) {
                const errorsList = Object.entries(data.errors)
                    .map(([key, value]) => `${key}: ${Array.isArray(value) ? value.join(', ') : value}`)
                    .join('\n');
                errorMessage += '\n\nDetalles:\n' + errorsList;
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Error al actualizar',
                text: errorMessage,
                confirmButtonColor: '#dc3545',
                width: '600px'
            });
            return;
        }

        // Verificar que los datos se hayan actualizado correctamente
        console.log('=== DATOS ACTUALIZADOS ===');
        console.log('Mega evento actualizado:', data.mega_evento);
        console.log('Mega Evento ID:', megaEventoId);
        
        Swal.fire({
            icon: 'success',
            title: '¡Actualizado!',
            text: 'El mega evento se ha actualizado correctamente',
            confirmButtonText: 'Ver detalles',
            confirmButtonColor: '#00A36C',
            timer: 2000,
            timerProgressBar: true
        }).then(() => {
            // Redirigir correctamente a la vista de detalles
            // Ruta: /ong/mega-eventos/{id}/detalle
            const detalleUrl = `/ong/mega-eventos/${megaEventoId}/detalle?t=${Date.now()}`;
            console.log('Redirigiendo a:', detalleUrl);
            window.location.href = detalleUrl;
        });

    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error de conexión',
            text: 'No se pudo conectar con el servidor',
            confirmButtonColor: '#dc3545'
        });
    }
});
</script>
@endpush
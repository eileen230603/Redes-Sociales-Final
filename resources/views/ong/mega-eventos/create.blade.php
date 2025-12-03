@extends('layouts.adminlte')

@section('page_title', 'Crear Mega Evento')

@section('content_body')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-4">
        <div class="card-header bg-white">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div>
                    <h3 class="card-title mb-1 text-dark">
                        <i class="fas fa-calendar-plus mr-2 text-primary"></i> Crear nuevo mega evento
                    </h3>
                    <p class="mb-0 text-muted">
                        Define la información principal, ubicación, visibilidad e imágenes para tu mega evento.
                    </p>
                </div>
                <div class="mt-3 mt-md-0">
                    <a href="{{ route('ong.mega-eventos.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form id="createMegaEventoForm" enctype="multipart/form-data">
        <!-- Información Básica -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0 text-dark">
                    <i class="fas fa-info-circle mr-2 text-info"></i> Información básica
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="titulo" class="font-weight-bold text-dark">
                                <i class="fas fa-heading mr-2 text-primary"></i>Título del mega evento <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="titulo" name="titulo" class="form-control" required 
                                   maxlength="200" placeholder="Ej: Festival de Verano 2025">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="categoria" class="font-weight-bold text-dark">
                                <i class="fas fa-tags mr-2 text-success"></i>Categoría
                            </label>
                            <select id="categoria" name="categoria" class="form-control">
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

                <div class="form-group">
                    <label for="descripcion" class="font-weight-bold text-dark">
                        <i class="fas fa-align-left mr-2 text-info"></i>Descripción
                    </label>
                    <textarea id="descripcion" name="descripcion" rows="4" class="form-control" 
                              placeholder="Describe el mega evento..."></textarea>
                </div>
            </div>
        </div>

        <!-- Fechas -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0 text-dark">
                    <i class="fas fa-calendar-alt mr-2 text-warning"></i> Fechas del evento
                </h5>
            </div>
            <div class="card-body">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fecha_inicio" class="font-weight-bold text-dark">
                                <i class="fas fa-play-circle mr-2 text-success"></i>Fecha y Hora de Inicio <span class="text-danger">*</span>
                            </label>
                            <input type="datetime-local" id="fecha_inicio" name="fecha_inicio" 
                                   class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fecha_fin" class="font-weight-bold text-dark">
                                <i class="fas fa-stop-circle mr-2 text-danger"></i>Fecha y Hora de Fin <span class="text-danger">*</span>
                            </label>
                            <input type="datetime-local" id="fecha_fin" name="fecha_fin" 
                                   class="form-control" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ubicación y Capacidad -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0 text-dark">
                    <i class="fas fa-map-marker-alt mr-2 text-danger"></i> Ubicación y capacidad
                </h5>
            </div>
            <div class="card-body">

                <!-- Mapa -->
                <div class="form-group mb-3">
                    <label class="font-weight-bold text-dark">
                        <i class="fas fa-map mr-2 text-primary"></i>Seleccionar ubicación en el mapa
                    </label>
                    <div id="map" class="rounded mb-3" style="height: 300px; border: 1px solid #ced4da;"></div>
                    <div class="form-group">
                        <label for="locacion" class="font-weight-bold text-dark">Dirección seleccionada <span class="text-danger">*</span></label>
                        <input id="locacion" readonly class="form-control bg-light">
                        <small id="ciudadInfo" class="text-muted"></small>
                    </div>
                    <input type="hidden" id="lat" name="lat">
                    <input type="hidden" id="lng" name="lng">
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="ubicacion" class="font-weight-bold text-dark">
                                <i class="fas fa-location-dot mr-2 text-info"></i>Ubicación (texto)
                            </label>
                            <input type="text" id="ubicacion" name="ubicacion" class="form-control" 
                                   maxlength="500" placeholder="Ej: Parque Central, La Paz">
                            <small class="form-text text-muted">Puedes escribir manualmente o seleccionar en el mapa</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="capacidad_maxima" class="font-weight-bold text-dark">
                                <i class="fas fa-users mr-2 text-success"></i>Capacidad máxima
                            </label>
                            <input type="number" id="capacidad_maxima" name="capacidad_maxima" 
                                   class="form-control" min="1" placeholder="Ej: 1000">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Configuración -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0 text-dark">
                    <i class="fas fa-cogs mr-2 text-secondary"></i> Configuración
                </h5>
            </div>
            <div class="card-body">

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="estado" class="font-weight-bold text-dark">
                                <i class="fas fa-info-circle mr-2 text-info"></i>Estado
                            </label>
                            <select id="estado" name="estado" class="form-control">
                                <option value="planificacion">Planificación</option>
                                <option value="activo">Activo</option>
                                <option value="en_curso">En Curso</option>
                                <option value="finalizado">Finalizado</option>
                                <option value="cancelado">Cancelado</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="es_publico" class="font-weight-bold text-dark">
                                <i class="fas fa-eye mr-2 text-primary"></i>Visibilidad
                            </label>
                            <select id="es_publico" name="es_publico" class="form-control">
                                <option value="1">Público</option>
                                <option value="0">Privado</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="activo" class="font-weight-bold text-dark">
                                <i class="fas fa-power-off mr-2 text-success"></i>Estado de Actividad
                            </label>
                            <select id="activo" name="activo" class="form-control">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Imágenes -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0 text-dark">
                    <i class="fas fa-images mr-2 text-warning"></i> Imágenes promocionales
                </h5>
            </div>
            <div class="card-body">

                <!-- Subir archivos -->
                <div class="form-group">
                    <label for="imagenes" class="font-weight-bold text-dark">
                        <i class="fas fa-upload mr-2 text-primary"></i>Subir imágenes desde archivo
                    </label>
                    <div class="custom-file">
                        <input type="file" id="imagenes" name="imagenes[]" multiple 
                               accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" 
                               class="custom-file-input">
                        <label class="custom-file-label" for="imagenes">Seleccionar archivos...</label>
                    </div>
                    <small class="form-text text-muted">
                        <i class="fas fa-info-circle mr-1"></i>Puedes seleccionar múltiples imágenes. Formatos permitidos: JPG, PNG, GIF, WEBP (máx. 5MB cada una)
                    </small>
                </div>

                <div id="previewContainer" class="row mb-3"></div>

                <!-- Agregar por URL -->
                <div class="form-group mt-4">
                    <label for="imagen_url" class="font-weight-bold text-dark">
                        <i class="fas fa-link mr-2 text-info"></i>Agregar imagen por URL (opcional)
                    </label>
                    <div class="input-group">
                        <input type="url" id="imagen_url" name="imagen_url" 
                               class="form-control" 
                               placeholder="https://ejemplo.com/imagen.jpg">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary" id="btnAgregarUrl">
                                <i class="fas fa-plus mr-1"></i> Agregar
                            </button>
                        </div>
                    </div>
                    <small class="form-text text-muted">
                        <i class="fas fa-info-circle mr-1"></i>Ingresa la URL completa de una imagen en internet
                    </small>
                </div>

                <div id="urlImagesContainer" class="row mb-3"></div>
            </div>
        </div>

        <!-- Mensaje de resultado -->
        <div id="formMessage" class="alert" style="display: none;"></div>

        <!-- Botones -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-end">
                    <a href="{{ route('ong.mega-eventos.index') }}" class="btn btn-secondary mr-2">
                        <i class="fas fa-times mr-1"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-check-circle mr-1"></i> Crear mega evento
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    .card {
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12) !important;
    }

    .card-header {
        border-bottom: none;
    }

    .form-group label {
        margin-bottom: 0.5rem;
    }

    #previewContainer img {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #F5F5F5;
        margin: 5px;
        cursor: pointer;
        transition: transform 0.2s;
    }
    #previewContainer img:hover {
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
        display: flex;
        align-items: center;
        justify-content: center;
    }
    #urlImagesContainer .image-preview-wrapper,
    #urlImagesContainerEdit .image-preview-wrapper {
        border: 2px solid #00A36C;
    }
    #urlImagesContainer img,
    #urlImagesContainerEdit img {
        width: 100%;
        height: 150px;
        object-fit: cover;
        border-radius: 6px;
    }

    .custom-file-label::after {
        content: "Buscar";
    }
</style>
@endpush

@push('js')
{{-- Script global para icono de notificaciones --}}
<script src="{{ asset('js/notificaciones-ong.js') }}"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="{{ asset('assets/js/config.js') }}"></script>
<script>
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

    // Preview de imágenes desde archivo
    document.getElementById('imagenes').addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        selectedFiles = [...selectedFiles, ...files];
        updatePreview();
        
        // Actualizar label del custom-file-input
        const label = this.nextElementSibling;
        if (files.length > 0) {
            label.textContent = files.length === 1 ? files[0].name : `${files.length} archivos seleccionados`;
        } else {
            label.textContent = 'Seleccionar archivos...';
        }
    });

    // Agregar imagen por URL
    document.getElementById('btnAgregarUrl').addEventListener('click', function() {
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
            addUrlImage(url);
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

function updatePreview() {
    const container = document.getElementById('previewContainer');
    container.innerHTML = '';

    selectedFiles.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const wrapper = document.createElement('div');
            wrapper.className = 'image-preview-wrapper';
            wrapper.innerHTML = `
                <img src="${e.target.result}" alt="Preview ${index + 1}">
                <button type="button" class="remove-image" onclick="removeImage(${index})" title="Eliminar">
                    <i class="fas fa-times"></i>
                </button>
            `;
            container.appendChild(wrapper);
        };
        reader.readAsDataURL(file);
    });
}

function addUrlImage(url) {
    // Verificar si la URL ya existe
    if (urlImages.includes(url)) {
            Swal.fire({
                icon: 'warning',
                title: 'URL duplicada',
                text: 'Esta URL ya ha sido agregada'
        });
        return;
    }

    urlImages.push(url);
    updateUrlImagesPreview();
}

function updateUrlImagesPreview() {
    const container = document.getElementById('urlImagesContainer');
    container.innerHTML = '';

    if (urlImages.length === 0) {
        return;
    }

    urlImages.forEach((url, index) => {
        const colDiv = document.createElement('div');
        colDiv.className = 'col-md-3 col-sm-4 col-6 mb-3';
        
        const wrapper = document.createElement('div');
        wrapper.className = 'image-preview-wrapper position-relative';
        wrapper.style.cssText = 'border-radius: 8px; overflow: hidden; border: 2px solid #28a745;';
        
        const img = document.createElement('img');
        img.src = url;
        img.alt = `Imagen URL ${index + 1}`;
        img.className = 'img-fluid';
        img.style.cssText = 'width: 100%; height: 150px; object-fit: cover; cursor: pointer;';
        img.onclick = () => window.open(url, '_blank');
        img.onerror = function() {
            this.onerror = null;
            this.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="150" height="150"%3E%3Crect fill="%23f8f9fa" width="150" height="150"/%3E%3Ctext x="50%25" y="50%25" text-anchor="middle" dy=".3em" fill="%23adb5bd" font-family="Arial" font-size="12"%3EError cargando%3C/text%3E%3C/svg%3E';
            this.style.objectFit = 'contain';
            this.style.padding = '10px';
        };
        
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'remove-image btn btn-danger btn-sm';
        removeBtn.style.cssText = 'position: absolute; top: 5px; right: 5px; width: 30px; height: 30px; border-radius: 50%; padding: 0; display: flex; align-items: center; justify-content: center;';
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
        formData.append('capacidad_maxima', document.getElementById('capacidad_maxima').value);
        formData.append('es_publico', document.getElementById('es_publico').value);
        formData.append('activo', document.getElementById('activo').value);

        // Agregar imágenes desde archivos
        selectedFiles.forEach(file => {
            formData.append('imagenes[]', file);
        });

        // Agregar imágenes desde URLs
        if (urlImages.length > 0) {
            formData.append('imagenes_urls', JSON.stringify(urlImages));
        }

        const res = await fetch(`${API_BASE_URL}/api/mega-eventos`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            },
            body: formData
        });

        const data = await res.json();

        if (!res.ok || !data.success) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.error || 'Error al crear el mega evento'
            });
            return;
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


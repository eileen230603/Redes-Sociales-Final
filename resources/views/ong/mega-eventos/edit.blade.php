@extends('layouts.adminlte')

@section('page_title', 'Editar Mega Evento')

@section('content_body')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-warning text-dark">
            <h3 class="card-title mb-0">
                <i class="fas fa-edit mr-2"></i> Editar Mega Evento
            </h3>
        </div>
        <div class="card-body">
            <div id="loadingMessage" class="text-center py-5">
                <div class="spinner-border text-warning" role="status">
                    <span class="sr-only">Cargando...</span>
                </div>
                <p class="mt-3 text-muted">Cargando información del mega evento...</p>
            </div>

            <form id="editMegaEventoForm" enctype="multipart/form-data" style="display: none;">
                <!-- Información Básica -->
                <div class="mb-4 border-bottom pb-3">
                    <h4 class="text-primary">
                        <i class="fas fa-info-circle mr-2"></i> Información Básica
                    </h4>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="titulo">Título del Mega Evento *</label>
                            <input type="text" id="titulo" name="titulo" class="form-control" required 
                                   maxlength="200">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="categoria">Categoría</label>
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
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" name="descripcion" rows="4" class="form-control"></textarea>
                </div>

                <!-- Fechas -->
                <div class="mb-4 border-bottom pb-3 mt-4">
                    <h4 class="text-info">
                        <i class="fas fa-calendar-alt mr-2"></i> Fechas del Evento
                    </h4>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fecha_inicio">Fecha y Hora de Inicio *</label>
                            <input type="datetime-local" id="fecha_inicio" name="fecha_inicio" 
                                   class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fecha_fin">Fecha y Hora de Fin *</label>
                            <input type="datetime-local" id="fecha_fin" name="fecha_fin" 
                                   class="form-control" required>
                        </div>
                    </div>
                </div>

                <!-- Ubicación y Capacidad -->
                <div class="mb-4 border-bottom pb-3 mt-4">
                    <h4 class="text-warning">
                        <i class="fas fa-map-marker-alt mr-2"></i> Ubicación y Capacidad
                    </h4>
                </div>

                <!-- Mapa -->
                <div class="form-group mb-3">
                    <label>Seleccionar Ubicación en el Mapa</label>
                    <div id="map" class="rounded mb-3" style="height: 300px; border: 1px solid #ced4da;"></div>
                    <div class="form-group">
                        <label for="locacion">Dirección seleccionada</label>
                        <input id="locacion" readonly class="form-control bg-light">
                        <small id="ciudadInfo" class="text-muted"></small>
                    </div>
                    <input type="hidden" id="lat" name="lat">
                    <input type="hidden" id="lng" name="lng">
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="ubicacion">Ubicación (Texto)</label>
                            <input type="text" id="ubicacion" name="ubicacion" class="form-control" 
                                   maxlength="500">
                            <small class="form-text text-muted">Puedes escribir manualmente o seleccionar en el mapa</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="capacidad_maxima">Capacidad Máxima</label>
                            <input type="number" id="capacidad_maxima" name="capacidad_maxima" 
                                   class="form-control" min="1">
                        </div>
                    </div>
                </div>

                <!-- Configuración -->
                <div class="mb-4 border-bottom pb-3 mt-4">
                    <h4 class="text-secondary">
                        <i class="fas fa-cogs mr-2"></i> Configuración
                    </h4>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="estado">Estado</label>
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
                            <label for="es_publico">Visibilidad</label>
                            <select id="es_publico" name="es_publico" class="form-control">
                                <option value="1">Público</option>
                                <option value="0">Privado</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="activo">Estado de Actividad</label>
                            <select id="activo" name="activo" class="form-control">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Imágenes Existentes -->
                <div class="mb-4 border-bottom pb-3 mt-4">
                    <h4 class="text-danger">
                        <i class="fas fa-images mr-2"></i> Imágenes Actuales
                    </h4>
                </div>

                <div id="existingImagesContainer" class="row mb-3"></div>

                <!-- Agregar Nuevas Imágenes -->
                <div class="mb-4 border-bottom pb-3 mt-4">
                    <h4 class="text-danger">
                        <i class="fas fa-plus-circle mr-2"></i> Agregar Nuevas Imágenes
                    </h4>
                </div>

                <div class="form-group">
                    <label for="nuevas_imagenes">Subir Imágenes desde Archivo</label>
                    <input type="file" id="nuevas_imagenes" name="nuevas_imagenes[]" multiple 
                           accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" 
                           class="form-control-file">
                    <small class="form-text text-muted">
                        Puedes seleccionar múltiples imágenes. Formatos permitidos: JPG, PNG, GIF, WEBP (máx. 5MB cada una)
                    </small>
                </div>

                <div id="previewContainer" class="row mb-3"></div>

                <!-- Agregar por URL -->
                <div class="form-group mt-4">
                    <label for="imagen_url_edit">Agregar Imagen por URL (Opcional)</label>
                    <div class="input-group">
                        <input type="url" id="imagen_url_edit" name="imagen_url_edit" 
                               class="form-control" 
                               placeholder="https://ejemplo.com/imagen.jpg">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary" id="btnAgregarUrlEdit">
                                <i class="fas fa-plus"></i> Agregar
                            </button>
                        </div>
                    </div>
                    <small class="form-text text-muted">
                        Ingresa la URL completa de una imagen en internet
                    </small>
                </div>

                <div id="urlImagesContainerEdit" class="row mb-3"></div>

                <!-- Mensaje de resultado -->
                <div id="formMessage" class="alert" style="display: none;"></div>

                <!-- Botones -->
                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('ong.mega-eventos.index') }}" class="btn btn-secondary mr-2">
                        <i class="fas fa-times mr-2"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-warning btn-lg">
                        <i class="fas fa-save mr-2"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    #previewContainer img {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #ddd;
        margin: 5px;
        cursor: pointer;
        transition: transform 0.2s;
    }
    #previewContainer img:hover {
        transform: scale(1.05);
    }
    #existingImagesContainer .image-preview-wrapper {
        position: relative;
        transition: transform 0.2s;
    }
    #existingImagesContainer .image-preview-wrapper:hover {
        transform: translateY(-5px);
    }
    #existingImagesContainer .image-preview-wrapper img {
        cursor: pointer;
        transition: all 0.3s ease;
    }
    #existingImagesContainer .image-preview-wrapper img:hover {
        opacity: 0.8;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    .image-preview-wrapper .remove-image {
        position: absolute;
        top: 5px;
        right: 5px;
        background: #dc3545;
        color: white;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        border: 2px solid white;
        cursor: pointer;
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.3);
        transition: all 0.2s;
        z-index: 10;
    }
    .image-preview-wrapper .remove-image:hover {
        background: #c82333;
        transform: scale(1.1);
    }
</style>
@endsection

@section('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="{{ asset('assets/js/config.js') }}"></script>
<script>
let megaEventoId = null;
let existingImages = [];
let selectedFiles = [];
let urlImages = []; // Array para almacenar URLs de imágenes
let map, clickMarker;
let ciudadDetectada = "";

// ===============================
// 🗺️ MAPA LEAFLET
// ===============================
function initMap(lat = null, lng = null) {
    const pos = lat && lng ? [lat, lng] : [-16.5, -68.15]; // La Paz, Bolivia por defecto

    map = L.map("map").setView(pos, lat && lng ? 15 : 13);

    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png").addTo(map);

    // Si hay coordenadas existentes, colocar marcador
    if (lat && lng) {
        clickMarker = L.marker([lat, lng]).addTo(map);
        document.getElementById("lat").value = lat;
        document.getElementById("lng").value = lng;
        reverseGeocode(lat, lng);
    }

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

document.addEventListener('DOMContentLoaded', async () => {
    const token = localStorage.getItem('token');
    const tipoUsuario = localStorage.getItem('tipo_usuario');
    
    if (!token || tipoUsuario !== 'ONG') {
        Swal.fire({
            icon: 'warning',
            title: 'Acceso Denegado',
            text: 'Debes iniciar sesión como ONG para editar mega eventos.',
            confirmButtonText: 'Ir al Login'
        }).then(() => {
            window.location.href = '/login';
        });
        return;
    }

    // Obtener ID de la URL
    const pathParts = window.location.pathname.split('/');
    megaEventoId = pathParts[pathParts.length - 2]; // /ong/mega-eventos/{id}/editar

    await loadMegaEvento();

    // Preview de nuevas imágenes desde archivo
    document.getElementById('nuevas_imagenes').addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        selectedFiles = [...selectedFiles, ...files];
        updatePreview();
    });

    // Agregar imagen por URL
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
                    addUrlImageEdit(url);
                    urlInput.value = '';
                }
            });
        } else {
            addUrlImageEdit(url);
            urlInput.value = '';
        }
    });

    // Permitir agregar URL con Enter
    document.getElementById('imagen_url_edit').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('btnAgregarUrlEdit').click();
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
                title: 'Fecha Inválida',
                text: 'La fecha de fin debe ser posterior a la fecha de inicio'
            });
        }
        fechaFin.min = fechaInicio;
    });
});

async function loadMegaEvento() {
    const token = localStorage.getItem('token');
    const loadingMessage = document.getElementById('loadingMessage');
    const form = document.getElementById('editMegaEventoForm');

    try {
        const res = await fetch(`${API_BASE_URL}/api/mega-eventos/${megaEventoId}`, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        const data = await res.json();

        if (!res.ok || !data.success) {
            loadingMessage.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Error: ${data.error || 'Error al cargar el mega evento'}
                </div>
            `;
            return;
        }

        const mega = data.mega_evento;
        
        // Llenar formulario
        document.getElementById('titulo').value = mega.titulo || '';
        document.getElementById('descripcion').value = mega.descripcion || '';
        document.getElementById('categoria').value = mega.categoria || 'social';
        document.getElementById('estado').value = mega.estado || 'planificacion';
        document.getElementById('ubicacion').value = mega.ubicacion || '';
        document.getElementById('capacidad_maxima').value = mega.capacidad_maxima || '';
        document.getElementById('es_publico').value = mega.es_publico ? '1' : '0';
        document.getElementById('activo').value = mega.activo ? '1' : '0';

        // Formatear fechas para datetime-local
        if (mega.fecha_inicio) {
            const fechaInicio = new Date(mega.fecha_inicio);
            document.getElementById('fecha_inicio').value = fechaInicio.toISOString().slice(0, 16);
        }
        if (mega.fecha_fin) {
            const fechaFin = new Date(mega.fecha_fin);
            document.getElementById('fecha_fin').value = fechaFin.toISOString().slice(0, 16);
        }

        // Cargar imágenes existentes (el modelo ya devuelve URLs completas)
        existingImages = Array.isArray(mega.imagenes) ? mega.imagenes.filter(img => img && img.trim() !== '') : [];
        displayExistingImages();

        // Inicializar mapa con coordenadas existentes si las hay
        const lat = mega.lat ? parseFloat(mega.lat) : null;
        const lng = mega.lng ? parseFloat(mega.lng) : null;
        initMap(lat, lng);

        loadingMessage.style.display = 'none';
        form.style.display = 'block';

    } catch (error) {
        console.error('Error:', error);
        loadingMessage.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Error de conexión al cargar el mega evento.
            </div>
        `;
    }
}

function displayExistingImages() {
    const container = document.getElementById('existingImagesContainer');
    container.innerHTML = '';

    if (existingImages.length === 0) {
        container.innerHTML = '<div class="col-12"><p class="text-muted"><i class="fas fa-image mr-2"></i>No hay imágenes cargadas</p></div>';
        return;
    }

    existingImages.forEach((imgUrl, index) => {
        // El modelo ya devuelve URLs completas, usar directamente
        if (!imgUrl || imgUrl.trim() === '') return;
        
        const fullUrl = imgUrl; // Ya viene como URL completa del modelo

        const wrapper = document.createElement('div');
        wrapper.className = 'col-md-3 col-sm-4 col-6 mb-3';
        
        const img = document.createElement('img');
        img.src = fullUrl;
        img.alt = `Imagen ${index + 1}`;
        img.className = 'img-fluid rounded shadow-sm';
        img.style.cssText = 'width: 100%; height: 200px; object-fit: cover; border: 2px solid #ddd; cursor: pointer;';
        img.onclick = () => window.open(fullUrl, '_blank');
        img.onerror = function() {
            this.onerror = null;
            this.src = 'https://via.placeholder.com/200x200?text=Error+cargando+imagen';
            this.style.objectFit = 'contain';
        };

        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'remove-image btn btn-danger btn-sm';
        removeBtn.title = 'Eliminar imagen';
        removeBtn.innerHTML = '<i class="fas fa-times"></i>';
        removeBtn.onclick = () => removeExistingImage(imgUrl);

        const imageWrapper = document.createElement('div');
        imageWrapper.className = 'image-preview-wrapper position-relative';
        imageWrapper.appendChild(img);
        imageWrapper.appendChild(removeBtn);
        
        wrapper.appendChild(imageWrapper);
        container.appendChild(wrapper);
    });
}

async function removeExistingImage(imgUrl) {
    const result = await Swal.fire({
        title: '¿Eliminar imagen?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
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

        // Remover de la lista local
        existingImages = existingImages.filter(img => img !== imgUrl);
        displayExistingImages();

        Swal.fire({
            icon: 'success',
            title: '¡Eliminada!',
            text: 'La imagen ha sido eliminada',
            timer: 1500,
            timerProgressBar: true
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
                <button type="button" class="remove-image" onclick="removeNewImage(${index})" title="Eliminar">
                    <i class="fas fa-times"></i>
                </button>
            `;
            container.appendChild(wrapper);
        };
        reader.readAsDataURL(file);
    });
}

function addUrlImageEdit(url) {
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
    updateUrlImagesPreviewEdit();
}

function updateUrlImagesPreviewEdit() {
    const container = document.getElementById('urlImagesContainerEdit');
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
        removeBtn.onclick = () => removeUrlImageEdit(index);
        
        wrapper.appendChild(img);
        wrapper.appendChild(removeBtn);
        colDiv.appendChild(wrapper);
        container.appendChild(colDiv);
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
}

document.getElementById('editMegaEventoForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const token = localStorage.getItem('token');
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
        title: 'Guardando cambios...',
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
        formData.append('capacidad_maxima', document.getElementById('capacidad_maxima').value);
        formData.append('es_publico', document.getElementById('es_publico').value);
        formData.append('activo', document.getElementById('activo').value);

        // Agregar nuevas imágenes desde archivos
        selectedFiles.forEach(file => {
            formData.append('imagenes[]', file);
        });

        // Agregar imágenes desde URLs
        if (urlImages.length > 0) {
            formData.append('imagenes_urls', JSON.stringify(urlImages));
        }

        // Agregar imágenes existentes que se mantendrán (como JSON)
        // Convertir URLs locales a rutas relativas, pero mantener URLs de internet
        const imagenesParaGuardar = existingImages.map(imgUrl => {
            if (!imgUrl || imgUrl.trim() === '') return null;
            
            // Si es URL completa
            if (imgUrl.startsWith('http://') || imgUrl.startsWith('https://')) {
                try {
                    const urlObj = new URL(imgUrl);
                    const host = urlObj.host;
                    const currentHost = window.location.host;
                    const currentOrigin = window.location.origin;
                    
                    // Si es URL de internet (no es localhost ni el dominio actual), mantenerla
                    if (host !== 'localhost' && host !== '127.0.0.1' && 
                        !urlObj.origin.includes(currentHost) &&
                        !currentOrigin.includes(host) &&
                        !host.includes(currentHost) && 
                        !currentHost.includes(host)) {
                        return imgUrl; // Mantener URL de internet completa
                    }
                    
                    // Si es URL local, extraer la ruta relativa
                    const pathname = urlObj.pathname;
                    return pathname.startsWith('/storage/') 
                        ? pathname 
                        : '/storage/' + pathname.replace(/^\/storage\/?/, '').replace(/^\//, '');
                } catch (e) {
                    console.warn('Error parseando URL:', imgUrl, e);
                    // Si no se puede parsear, asumir que es ruta relativa
                    return imgUrl.startsWith('/storage/') ? imgUrl : '/storage/' + imgUrl.replace(/^\/storage\/?/, '');
                }
            }
            // Si ya es ruta relativa, retornarla
            return imgUrl.startsWith('/storage/') ? imgUrl : '/storage/' + imgUrl.replace(/^\/storage\/?/, '');
        }).filter(img => img !== null);
        
        // Siempre enviar imagenes_json, incluso si está vacío, para mantener las existentes
        formData.append('imagenes_json', JSON.stringify(imagenesParaGuardar));
        
        console.log('Imágenes existentes a guardar:', imagenesParaGuardar);
        console.log('URLs nuevas a agregar:', urlImages);

        const res = await fetch(`${API_BASE_URL}/api/mega-eventos/${megaEventoId}`, {
            method: 'PUT',
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
                text: data.error || 'Error al actualizar el mega evento'
            });
            return;
        }

        Swal.fire({
            icon: 'success',
            title: '¡Actualizado!',
            text: 'El mega evento se ha actualizado correctamente',
            confirmButtonText: 'Ver Mega Eventos',
            timer: 2000,
            timerProgressBar: true
        }).then(() => {
            window.location.href = '{{ route("ong.mega-eventos.index") }}';
        });

    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error de Conexión',
            text: 'No se pudo conectar con el servidor'
        });
    }
});
</script>
@endsection


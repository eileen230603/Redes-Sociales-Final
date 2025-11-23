@extends('layouts.adminlte-empresa')

@section('page_title', 'Detalle del Mega Evento')

@section('content_body')
<input type="hidden" id="megaEventoId" value="{{ request()->id }}">
<div class="container-fluid px-0">
    <div id="loadingMessage" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Cargando...</span>
        </div>
        <p class="mt-3 text-muted">Cargando información del mega evento...</p>
    </div>

    <div id="megaEventoContent" style="display: none;">
        <!-- Banner Superior con Imagen Principal -->
        <div id="eventBanner" class="position-relative" style="height: 400px; background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); overflow: hidden;">
            <div id="bannerImage" class="w-100 h-100" style="background-size: cover; background-position: center; opacity: 0.3;"></div>
            <div class="position-absolute" style="top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(to bottom, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0.6) 100%);"></div>
            <div class="position-absolute" style="bottom: 0; left: 0; right: 0; padding: 2rem; color: white;">
                <div class="container">
                    <h1 id="titulo" class="mb-2" style="font-size: 2.5rem; font-weight: 700; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);"></h1>
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <span id="categoriaBadge" class="badge badge-info" style="font-size: 0.9rem; padding: 0.5em 1em;"></span>
                        <span id="estadoBadge" class="badge" style="font-size: 0.9rem; padding: 0.5em 1em;"></span>
                        <span id="publicoBadge" class="badge badge-info" style="font-size: 0.9rem; padding: 0.5em 1em;">Público</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mt-4">
            <!-- Botones de Acción -->
            <div class="d-flex justify-content-end mb-4 gap-2 flex-wrap">
                <a href="/empresa/mega-eventos" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left mr-2"></i> Volver
                </a>
                <button class="btn btn-info" id="btnAuspiciar">
                    <i class="fas fa-hand-holding-usd mr-2"></i> Auspiciar / Ser patrocinador
                </button>
            </div>

            <div class="row">
                <!-- Columna Principal -->
                <div class="col-lg-8">
                    <!-- Descripción -->
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                        <div class="card-body p-4">
                            <h4 class="mb-3" style="color: #2c3e50; font-weight: 600;">
                                <i class="fas fa-align-left mr-2 text-info"></i> Descripción
                            </h4>
                            <p id="descripcion" class="mb-0" style="color: #6c757d; line-height: 1.8; font-size: 1rem;"></p>
                        </div>
                    </div>

                    <!-- Información del Mega Evento -->
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                        <div class="card-body p-4">
                            <h4 class="mb-4" style="color: #2c3e50; font-weight: 600;">
                                <i class="fas fa-info-circle mr-2 text-info"></i> Información del Mega Evento
                            </h4>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-calendar-alt text-info mr-3 mt-1" style="font-size: 1.2rem;"></i>
                                        <div>
                                            <h6 class="mb-1" style="color: #495057; font-weight: 600;">Fecha de Inicio</h6>
                                            <p id="fecha_inicio" class="mb-0 text-muted"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-calendar-check text-info mr-3 mt-1" style="font-size: 1.2rem;"></i>
                                        <div>
                                            <h6 class="mb-1" style="color: #495057; font-weight: 600;">Fecha de Fin</h6>
                                            <p id="fecha_fin" class="mb-0 text-muted"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-users text-info mr-3 mt-1" style="font-size: 1.2rem;"></i>
                                        <div>
                                            <h6 class="mb-1" style="color: #495057; font-weight: 600;">Capacidad Máxima</h6>
                                            <p id="capacidad_maxima" class="mb-0 text-muted"></p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Ubicación destacada -->
                                <div class="col-md-12 mb-4">
                                    <div class="card border-0" style="background: #d1ecf1; border-radius: 12px; padding: 1.5rem; border-left: 4px solid #17a2b8;">
                                        <div class="d-flex align-items-start">
                                            <div class="mr-3" style="width: 50px; height: 50px; background: #fff; border-radius: 10px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                                <i class="fas fa-map-marker-alt" style="color: #17a2b8; font-size: 1.5rem;"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h5 class="mb-3" style="color: #0c5460; font-weight: 600;">
                                                    <i class="fas fa-map-marked-alt mr-2"></i> Ubicación del Evento
                                                </h5>
                                                <div id="ubicacionContainer">
                                                    <p id="ubicacion" class="mb-0" style="font-size: 1.1rem; font-weight: 500; color: #2c3e50; line-height: 1.8;">-</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Mapa -->
                                <div class="col-md-12 mb-3">
                                    <h5 class="mb-3" style="color: #2c3e50; font-weight: 600;">
                                        <i class="fas fa-map mr-2 text-info"></i> Mapa de Ubicación
                                    </h5>
                                    <div id="map" style="height: 350px; border-radius: 12px; border: 1px solid #e9ecef; overflow: hidden;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Galería de Imágenes -->
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                        <div class="card-body p-4">
                            <h4 class="mb-4" style="color: #2c3e50; font-weight: 600;">
                                <i class="fas fa-images mr-2 text-info"></i> Imágenes Promocionales
                                <span id="imagenesCount" class="badge badge-info ml-2">0</span>
                            </h4>
                            <div id="imagenesContainer" class="row">
                                <div class="col-12 text-center py-3">
                                    <div class="spinner-border text-info" role="status"></div>
                                    <p class="mt-2 text-muted">Cargando imágenes...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- ONG Organizadora -->
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                        <div class="card-body p-4">
                            <h5 class="mb-3" style="color: #2c3e50; font-weight: 600;">
                                <i class="fas fa-building mr-2 text-info"></i> ONG Organizadora
                            </h5>
                            <p id="ong_organizadora" class="mb-0" style="color: #495057; font-size: 1rem;"></p>
                        </div>
                    </div>

                    <!-- Información Adicional -->
                    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                        <div class="card-body p-4">
                            <h5 class="mb-3" style="color: #2c3e50; font-weight: 600;">
                                <i class="fas fa-info-circle mr-2 text-info"></i> Información Adicional
                            </h5>
                            <div class="mb-3 pb-3 border-bottom">
                                <small class="text-muted d-block mb-1">Estado del Evento</small>
                                <div id="estado" class="mb-0"></div>
                            </div>
                            <div>
                                <small class="text-muted d-block mb-1">Visibilidad</small>
                                <div id="es_publico" class="mb-0"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    #megaEventoContent {
        animation: fadeIn 0.3s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection

@section('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="{{ asset('assets/js/config.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function buildImageUrl(imgUrl) {
    if (!imgUrl || imgUrl.trim() === '') return null;
    if (imgUrl.startsWith('http://') || imgUrl.startsWith('https://')) return imgUrl;
    if (imgUrl.startsWith('/storage/')) return `${window.location.origin}${imgUrl}`;
    if (imgUrl.startsWith('storage/')) return `${window.location.origin}/${imgUrl}`;
    return `${window.location.origin}/storage/${imgUrl}`;
}

let megaEventoId = null;
let map = null;

document.addEventListener('DOMContentLoaded', async () => {
    const token = localStorage.getItem('token');
    
    if (!token) {
        Swal.fire({
            icon: 'warning',
            title: 'Sesión Expirada',
            text: 'Tu sesión ha expirado. Por favor, inicia sesión nuevamente.',
            confirmButtonText: 'Ir al Login'
        }).then(() => {
            window.location.href = '/login';
        });
        return;
    }

    megaEventoId = document.getElementById('megaEventoId')?.value || window.location.pathname.split('/')[3];
    
    await loadMegaEvento();
});

async function loadMegaEvento() {
    const token = localStorage.getItem('token');
    const loadingMessage = document.getElementById('loadingMessage');
    const content = document.getElementById('megaEventoContent');

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
        displayMegaEvento(mega);
        loadingMessage.style.display = 'none';
        content.style.display = 'block';

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

function parsearUbicacion(ubicacionStr) {
    if (!ubicacionStr || ubicacionStr === 'No especificada') {
        return null;
    }
    
    // Intentar parsear formato: "Dirección, Ciudad, Departamento" o variaciones
    const partes = ubicacionStr.split(',').map(p => p.trim()).filter(p => p);
    
    if (partes.length >= 3) {
        // Formato: Dirección, Ciudad, Departamento
        return {
            direccion: partes.slice(0, -2).join(', '),
            ciudad: partes[partes.length - 2],
            departamento: partes[partes.length - 1]
        };
    } else if (partes.length === 2) {
        // Formato: Dirección, Ciudad o Ciudad, Departamento
        const segundaParte = partes[1].toLowerCase();
        const esDepartamento = segundaParte.includes('departamento') || 
                               segundaParte.includes('depto') ||
                               segundaParte.length < 15;
        
        if (esDepartamento) {
            return {
                direccion: partes[0],
                ciudad: null,
                departamento: partes[1]
            };
        } else {
            return {
                direccion: partes[0],
                ciudad: partes[1],
                departamento: null
            };
        }
    } else if (partes.length === 1) {
        return {
            direccion: partes[0],
            ciudad: null,
            departamento: null
        };
    }
    
    return null;
}

function displayMegaEvento(mega) {
    document.getElementById('titulo').textContent = mega.titulo || '-';
    document.getElementById('descripcion').textContent = mega.descripcion || 'Sin descripción disponible.';

    // Fechas
    if (mega.fecha_inicio) {
        const fechaInicio = new Date(mega.fecha_inicio);
        document.getElementById('fecha_inicio').textContent = fechaInicio.toLocaleString('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    if (mega.fecha_fin) {
        const fechaFin = new Date(mega.fecha_fin);
        document.getElementById('fecha_fin').textContent = fechaFin.toLocaleString('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    // Capacidad
    document.getElementById('capacidad_maxima').textContent = mega.capacidad_maxima 
        ? `${mega.capacidad_maxima} personas` 
        : 'Sin límite';

    // Ubicación - Mostrar de forma visible y organizada con dirección, ciudad y departamento
    const ubicacionContainer = document.getElementById('ubicacionContainer');
    const ubicacion = mega.ubicacion || 'No especificada';
    const ubicacionParsed = parsearUbicacion(ubicacion);
    
    if (ubicacionParsed) {
        let html = '';
        
        if (ubicacionParsed.direccion) {
            html += `
                <div class="mb-3">
                    <strong style="color: #0c5460; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        <i class="fas fa-road mr-2"></i> Dirección:
                    </strong>
                    <p class="mb-0 mt-2" style="font-size: 1.1rem; color: #2c3e50; font-weight: 500;">${ubicacionParsed.direccion}</p>
                </div>
            `;
        }
        
        if (ubicacionParsed.ciudad) {
            html += `
                <div class="mb-3">
                    <strong style="color: #0c5460; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        <i class="fas fa-city mr-2"></i> Ciudad:
                    </strong>
                    <p class="mb-0 mt-2" style="font-size: 1.1rem; color: #2c3e50; font-weight: 500;">${ubicacionParsed.ciudad}</p>
                </div>
            `;
        }
        
        if (ubicacionParsed.departamento) {
            html += `
                <div>
                    <strong style="color: #0c5460; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        <i class="fas fa-map-marked-alt mr-2"></i> Departamento:
                    </strong>
                    <p class="mb-0 mt-2" style="font-size: 1.1rem; color: #2c3e50; font-weight: 500;">${ubicacionParsed.departamento}</p>
                </div>
            `;
        }
        
        ubicacionContainer.innerHTML = html || `
            <p class="mb-0" style="font-size: 1.1rem; font-weight: 500; color: #2c3e50; line-height: 1.8;">
                <i class="fas fa-map-marker-alt mr-2" style="color: #17a2b8;"></i>${ubicacion}
            </p>
        `;
    } else {
        ubicacionContainer.innerHTML = `
            <p class="mb-0 text-muted" style="font-size: 1rem;">
                <i class="fas fa-exclamation-circle mr-2"></i>Ubicación no especificada
            </p>
        `;
    }

    // ONG Organizadora
    if (mega.ong_principal) {
        document.getElementById('ong_organizadora').textContent = mega.ong_principal.nombre_ong || '-';
    } else {
        document.getElementById('ong_organizadora').textContent = '-';
    }

    // Estados
    const estadoBadges = {
        'planificacion': '<span class="badge badge-secondary">Planificación</span>',
        'activo': '<span class="badge badge-success">Activo</span>',
        'en_curso': '<span class="badge badge-info">En Curso</span>',
        'finalizado': '<span class="badge badge-primary">Finalizado</span>',
        'cancelado': '<span class="badge badge-danger">Cancelado</span>'
    };
    document.getElementById('estadoBadge').innerHTML = estadoBadges[mega.estado] || '<span class="badge badge-secondary">' + (mega.estado || 'N/A') + '</span>';
    document.getElementById('estado').innerHTML = estadoBadges[mega.estado] || '<span class="badge badge-secondary">' + (mega.estado || 'N/A') + '</span>';

    document.getElementById('publicoBadge').innerHTML = mega.es_publico 
        ? '<span class="badge badge-info">Público</span>' 
        : '<span class="badge badge-secondary">Privado</span>';
    document.getElementById('es_publico').innerHTML = mega.es_publico 
        ? '<span class="badge badge-info">Público</span>' 
        : '<span class="badge badge-secondary">Privado</span>';

    document.getElementById('categoriaBadge').innerHTML = mega.categoria 
        ? '<span class="badge badge-info">' + mega.categoria.charAt(0).toUpperCase() + mega.categoria.slice(1) + '</span>' 
        : '';

    // Imágenes
    const imagenes = Array.isArray(mega.imagenes) ? mega.imagenes.filter(img => img && img.trim() !== '') : [];
    const imagenesContainer = document.getElementById('imagenesContainer');
    const imagenesCount = document.getElementById('imagenesCount');
    
    imagenesCount.textContent = imagenes.length;
    
    if (imagenes.length === 0) {
        imagenesContainer.innerHTML = `
            <div class="col-12">
                <div class="text-center py-5" style="background: #f8f9fa; border-radius: 8px;">
                    <i class="fas fa-image fa-3x mb-3" style="color: #dee2e6;"></i>
                    <p class="mb-0 text-muted">No hay imágenes disponibles</p>
                </div>
            </div>
        `;
    } else {
        imagenesContainer.innerHTML = '';
        imagenes.forEach((imgUrl, index) => {
            if (!imgUrl || imgUrl.trim() === '') return;
            
            const fullUrl = buildImageUrl(imgUrl);
            const colDiv = document.createElement('div');
            colDiv.className = 'col-md-4 col-sm-6 mb-4';
            
            const imgWrapper = document.createElement('div');
            imgWrapper.className = 'position-relative overflow-hidden';
            imgWrapper.style.cssText = 'height: 220px; background: #f8f9fa; border-radius: 12px; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.05);';
            imgWrapper.onclick = () => {
                Swal.fire({
                    imageUrl: fullUrl,
                    imageAlt: 'Imagen ' + (index + 1) + ' del mega evento',
                    showCloseButton: true,
                    showConfirmButton: false,
                    width: '90%',
                    padding: '1rem',
                    background: 'rgba(0,0,0,0.9)'
                });
            };
            
            const img = document.createElement('img');
            img.src = fullUrl;
            img.className = 'w-100 h-100';
            img.style.cssText = 'object-fit: cover; display: block;';
            img.alt = `Imagen ${index + 1}`;
            img.onerror = function() {
                this.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="400" height="220"%3E%3Crect fill="%23f8f9fa" width="400" height="220"/%3E%3Ctext x="50%25" y="50%25" text-anchor="middle" dy=".3em" fill="%23adb5bd" font-family="Arial" font-size="14"%3EImagen no disponible%3C/text%3E%3C/svg%3E';
                this.style.objectFit = 'contain';
                this.style.padding = '20px';
            };
            
            imgWrapper.appendChild(img);
            colDiv.appendChild(imgWrapper);
            imagenesContainer.appendChild(colDiv);
        });
    }

    // Imagen principal en banner
    if (imagenes.length > 0 && imagenes[0]) {
        const headerImage = document.getElementById('bannerImage');
        const fullUrl = buildImageUrl(imagenes[0]);
        const testImg = new Image();
        testImg.onload = function() {
            headerImage.style.backgroundImage = `url(${fullUrl})`;
        };
        testImg.onerror = function() {
            headerImage.style.background = 'linear-gradient(135deg, #17a2b8 0%, #138496 100%)';
        };
        testImg.src = fullUrl;
    }

    // Mapa
    const mapContainer = document.getElementById('map');
    if (mega.lat && mega.lng) {
        const lat = parseFloat(mega.lat);
        const lng = parseFloat(mega.lng);
        
        map = L.map("map", {
            zoomControl: true,
            scrollWheelZoom: true
        }).setView([lat, lng], 13);
        
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);
        
        const marker = L.marker([lat, lng], {
            icon: L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            })
        }).addTo(map);
        
        marker.bindPopup(`
            <div style="font-size: 0.9rem;">
                <strong style="color: #2c3e50;">${mega.titulo}</strong><br>
                <span style="color: #6c757d;">${mega.ubicacion || 'Ubicación del mega evento'}</span>
            </div>
        `).openPopup();
    } else {
        mapContainer.innerHTML = `
            <div class="d-flex align-items-center justify-content-center h-100" style="background: #f8f9fa; border-radius: 8px;">
                <div class="text-center">
                    <i class="fas fa-map-marker-alt fa-2x mb-2" style="color: #dee2e6;"></i>
                    <p class="text-muted mb-0" style="font-size: 0.9rem;">No hay coordenadas disponibles</p>
                </div>
            </div>
        `;
    }
}

// Auspiciar / Ser patrocinador
document.getElementById('btnAuspiciar').addEventListener('click', async function() {
    const token = localStorage.getItem('token');
    if (!token) {
        Swal.fire({
            icon: 'warning',
            title: 'Sesión Expirada',
            text: 'Debes iniciar sesión para auspiciar',
            confirmButtonText: 'Ir al Login'
        }).then(() => {
            window.location.href = '/login';
        });
        return;
    }

    Swal.fire({
        title: '¿Auspiciar este mega evento?',
        text: 'Tu empresa será registrada como auspiciadora/patrocinadora de este mega evento',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#17a2b8',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, auspiciar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                icon: 'info',
                title: 'Funcionalidad en desarrollo',
                text: 'La funcionalidad de auspicio/patrocinio estará disponible próximamente',
                confirmButtonText: 'Entendido'
            });
        }
    });
});
</script>
@endsection


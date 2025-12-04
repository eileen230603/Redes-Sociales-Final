@extends('layouts.adminlte')

@section('page_title', 'Detalle del Mega Evento')

@section('content_body')
<div class="container-fluid">
    <div id="loadingMessage" class="text-center py-5">
        <div class="spinner-border" role="status" style="color: #00A36C; width: 3rem; height: 3rem;">
            <span class="sr-only">Cargando...</span>
        </div>
        <p class="mt-3" style="color: #333333; font-weight: 500;">Cargando información del mega evento...</p>
    </div>

    <div id="megaEventoContent" style="display: none;">
        <!-- Banner Superior con Imagen Principal (igual que eventos) -->
        <div id="eventBanner" class="position-relative" style="height: 400px; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); overflow: hidden;">
            <div id="bannerImage" class="w-100 h-100" style="background-size: cover; background-position: center; opacity: 0.3;"></div>
            <div class="position-absolute" style="top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(to bottom, rgba(12, 43, 68, 0.3) 0%, rgba(0, 163, 108, 0.6) 100%);"></div>
            <div class="position-absolute" style="bottom: 0; left: 0; right: 0; padding: 2rem; color: white;">
                <div class="container">
                    <h1 id="titulo" class="mb-2" style="font-size: 2.5rem; font-weight: 700; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">-</h1>
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <span id="categoriaBadge" class="badge badge-light" style="font-size: 0.9rem; padding: 0.5em 1em;">-</span>
                        <span id="estadoBadge" class="badge" style="font-size: 0.9rem; padding: 0.5em 1em;">-</span>
                        <span id="publicoBadge" class="badge" style="font-size: 0.9rem; padding: 0.5em 1em;">-</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mt-4">
            <!-- Botones de Acción -->
            <div class="d-flex justify-content-end mb-4 flex-wrap" style="gap: 0.5rem;">
                <a href="{{ route('ong.mega-eventos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i> Volver
                </a>
                <button class="btn btn-outline-danger" id="btnReaccionar">
                    <i class="fas fa-heart mr-2" id="iconoCorazon"></i>
                    <span id="textoReaccion">Me gusta</span>
                    <span id="contadorReacciones" class="badge badge-light ml-2">0</span>
                </button>
                <button class="btn btn-primary" id="btnCompartir">
                    <i class="fas fa-share-alt mr-2"></i> Compartir <span id="contadorCompartidos" class="badge badge-light ml-2">0</span>
                </button>
                <a href="#" id="seguimientoLink" class="btn btn-info">
                    <i class="fas fa-chart-line mr-2"></i> Seguimiento
                </a>
                <a href="#" id="editLink" class="btn btn-success">
                    <i class="fas fa-edit mr-2"></i> Editar Mega Evento
                </a>
            </div>

            <div class="row">
                <!-- Columna Principal -->
                <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0 text-dark">
                            <i class="fas fa-info-circle mr-2 text-info"></i> Información del Mega Evento
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <label class="font-weight-bold text-dark mb-2">
                                <i class="fas fa-align-left mr-2 text-primary"></i>Descripción
                            </label>
                            <p id="descripcion" class="mb-0 text-muted" style="line-height: 1.8;">-</p>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-calendar mr-3 mt-1 text-info"></i>
                                    <div>
                                        <h6 class="mb-1 font-weight-bold text-dark">Fecha de Inicio</h6>
                                        <p id="fecha_inicio" class="mb-0 text-muted">-</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-calendar-check mr-3 mt-1 text-success"></i>
                                    <div>
                                        <h6 class="mb-1 font-weight-bold text-dark">Fecha de Fin</h6>
                                        <p id="fecha_fin" class="mb-0 text-muted">-</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start">
                                            <i class="fas fa-map-marker-alt mr-3 mt-1 text-danger fa-2x"></i>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-2 font-weight-bold text-dark">Ubicación del Evento</h6>
                                                <div id="ubicacionContainer">
                                                    <p id="ubicacion" class="mb-0 text-muted">-</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-users mr-3 mt-1 text-success"></i>
                                    <div>
                                        <h6 class="mb-1 font-weight-bold text-dark">Capacidad Máxima</h6>
                                        <p id="capacidad_maxima" class="mb-0 text-muted">-</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="font-weight-bold text-dark mb-2">
                                    <i class="fas fa-map mr-2 text-danger"></i>Mapa de Ubicación
                                </label>
                                <div id="map" class="rounded" style="height: 350px; border: 1px solid #ced4da; overflow: hidden;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Imágenes -->
                <div class="card mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 text-dark">
                            <i class="fas fa-images mr-2 text-warning"></i> Imágenes Promocionales
                        </h5>
                        <span id="imagenesCount" class="badge badge-primary">0</span>
                    </div>
                    <div class="card-body">
                        <div id="imagenesContainer" class="row">
                            <div class="col-12 text-center py-3">
                                <div class="spinner-border text-primary" role="status" style="width: 2rem; height: 2rem;">
                                    <span class="sr-only">Cargando...</span>
                                </div>
                                <p class="mt-2 text-muted" style="font-size: 0.9rem;">Cargando imágenes...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reacciones (Favoritos) -->
                <div class="card mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 text-dark">
                            <i class="fas fa-heart mr-2 text-danger"></i> Reacciones y Favoritos
                        </h5>
                        <button class="btn btn-sm btn-secondary btn-actualizar-reacciones" onclick="cargarReaccionesMegaEvento()">
                            <i class="fas fa-sync mr-1"></i> Actualizar
                        </button>
                    </div>
                    <div class="card-body">
                        <p class="mb-3 text-muted">
                            Usuarios que han marcado este mega evento como favorito con un corazón.
                        </p>
                        <div id="reaccionesContainer">
                            <div class="text-center py-3">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Cargando...</span>
                                </div>
                                <p class="mt-2 text-muted">Cargando reacciones...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información Adicional -->
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header bg-success">
                        <h6 class="card-title mb-0 text-white">
                            <i class="fas fa-building mr-2"></i> ONG Organizadora
                        </h6>
                    </div>
                    <div class="card-body">
                        <p id="ong_organizadora" class="mb-0 text-muted">-</p>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-info">
                        <h6 class="card-title mb-0 text-white">
                            <i class="fas fa-calendar mr-2"></i> Fechas del Sistema
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3 pb-3 border-bottom">
                            <small class="d-block mb-1 font-weight-bold text-dark">Fecha de Creación</small>
                            <p id="fecha_creacion" class="mb-0 text-muted">-</p>
                        </div>
                        <div>
                            <small class="d-block mb-1 font-weight-bold text-dark">Última Actualización</small>
                            <p id="fecha_actualizacion" class="mb-0 text-muted">-</p>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-primary">
                        <h6 class="card-title mb-0 text-white">
                            <i class="fas fa-cog mr-2"></i> Estado
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3 pb-3 border-bottom">
                            <small class="d-block mb-1 font-weight-bold text-dark">
                                <i class="fas fa-flag mr-1 text-info"></i> Estado del Evento
                            </small>
                            <div id="estado" class="mb-0">-</div>
                        </div>
                        <div class="mb-3 pb-3 border-bottom">
                            <small class="d-block mb-1 font-weight-bold text-dark">
                                <i class="fas fa-eye mr-1 text-primary"></i> Visibilidad
                            </small>
                            <div id="es_publico" class="mb-0">-</div>
                        </div>
                        <div>
                            <small class="d-block mb-1 font-weight-bold text-dark">
                                <i class="fas fa-power-off mr-1 text-success"></i> Activo
                            </small>
                            <div id="activo" class="mb-0">-</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Compartir -->
<div id="modalCompartir" class="modal fade" tabindex="-1" role="dialog" style="display: none;">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 400px;">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 8px 32px rgba(0,0,0,0.2);">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold">Compartir</h5>
                <button type="button" class="close" onclick="cerrarModalCompartir()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row text-center">
                    <!-- Copiar enlace -->
                    <div class="col-6 mb-4">
                        <button onclick="copiarEnlaceMegaEvento()" class="btn btn-link p-0" style="text-decoration: none; border: none; background: none; width: 100%;">
                            <div class="bg-light rounded-lg d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; transition: all 0.3s ease;" onmouseover="this.style.transform='scale(1.1)'; this.style.background='#E9ECEF';" onmouseout="this.style.transform='scale(1)'; this.style.background='#F5F5F5';">
                                <i class="fas fa-link fa-2x text-primary"></i>
                            </div>
                            <span class="font-weight-bold text-dark">Copiar enlace</span>
                        </button>
                    </div>
                    <!-- QR Code -->
                    <div class="col-6 mb-4">
                        <button onclick="mostrarQRMegaEvento()" class="btn btn-link p-0" style="text-decoration: none; border: none; background: none; width: 100%;">
                            <div class="bg-primary rounded-lg d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; transition: all 0.3s ease;" onmouseover="this.style.transform='scale(1.1)'; this.style.background='#0056b3';" onmouseout="this.style.transform='scale(1)'; this.style.background='#007bff';">
                                <i class="fas fa-qrcode fa-2x text-white"></i>
                            </div>
                            <span class="font-weight-bold text-dark">Código QR</span>
                        </button>
                    </div>
                </div>
                <!-- Contenedor para el QR -->
                <div id="qrContainer" style="display: none; margin-top: 1.5rem;">
                    <div class="text-center">
                        <div id="qrcode" class="d-inline-block p-3 bg-white rounded mb-3"></div>
                        <p class="text-muted mb-0">Escanea este código para acceder al mega evento</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    /* Estilos con nueva paleta de colores */
    #megaEventoContent {
        animation: fadeIn 0.3s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .card {
        transition: box-shadow 0.2s ease;
    }
    
    .card:hover {
        box-shadow: 0 4px 12px rgba(12, 43, 68, 0.12) !important;
    }
    
    /* Mejoras en las imágenes */
    #imagenesContainer img {
        transition: transform 0.3s ease;
    }
    
    #imagenesContainer .position-relative:hover img {
        transform: scale(1.05);
    }
    
    /* Estilo para el header */
    #headerImage {
        transition: all 0.3s ease;
    }
    
    /* Badges con nueva paleta */
    .badge {
        font-weight: 500;
        padding: 0.5em 0.8em;
        border-radius: 20px;
    }
    
    .badge-success {
        background-color: #00A36C !important;
        color: white !important;
    }
    
    .badge-info {
        background-color: #0C2B44 !important;
        color: white !important;
    }
    
    .badge-secondary {
        background-color: #333333 !important;
        color: white !important;
    }
    
    .badge-primary {
        background-color: #0C2B44 !important;
        color: white !important;
    }
    
    .badge-danger {
        background-color: #dc3545 !important;
        color: white !important;
    }
    
    /* Mejoras en el mapa */
    #map {
        transition: box-shadow 0.2s ease;
    }
    
    #map:hover {
        box-shadow: 0 4px 12px rgba(12, 43, 68, 0.15);
    }
</style>
@endpush

@push('js')
{{-- Script global para icono de notificaciones --}}
<script src="{{ asset('js/notificaciones-ong.js') }}"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="{{ asset('assets/js/config.js') }}"></script>
<script>
// Función helper para construir URL de imagen
function buildImageUrl(imgUrl) {
    if (!imgUrl || imgUrl.trim() === '') return null;
    
    if (imgUrl.startsWith('http://') || imgUrl.startsWith('https://')) {
        return imgUrl;
    }
    
    if (imgUrl.startsWith('/storage/')) {
        return `${window.location.origin}${imgUrl}`;
    }
    
    if (imgUrl.startsWith('storage/')) {
        return `${window.location.origin}/${imgUrl}`;
    }
    
    return `${window.location.origin}/storage/${imgUrl}`;
}

let megaEventoId = null;

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

    // Obtener ID de la URL
    // La URL es: /ong/mega-eventos/{id}/detalle
    const pathParts = window.location.pathname.split('/').filter(p => p !== '');
    console.log('Path parts:', pathParts);
    
    // Buscar el índice de 'mega-eventos' y tomar el siguiente elemento como ID
    const megaEventosIndex = pathParts.indexOf('mega-eventos');
    if (megaEventosIndex !== -1 && pathParts[megaEventosIndex + 1]) {
        megaEventoId = pathParts[megaEventosIndex + 1];
    } else {
        // Fallback: intentar obtener el penúltimo elemento
        megaEventoId = pathParts[pathParts.length - 2];
    }

    console.log('Mega Evento ID extraído:', megaEventoId);

    if (!megaEventoId || isNaN(megaEventoId)) {
        const loadingMessage = document.getElementById('loadingMessage');
        loadingMessage.innerHTML = `
            <div class="alert alert-danger" style="background: #f8d7da; border: 1px solid #dc3545; color: #721c24; border-radius: 8px; padding: 1rem;">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Error: ID de mega evento inválido en la URL. URL: ${window.location.pathname}
            </div>
        `;
        return;
    }

    await loadMegaEvento();
});

async function loadMegaEvento() {
    const token = localStorage.getItem('token');
    const loadingMessage = document.getElementById('loadingMessage');
    const content = document.getElementById('megaEventoContent');

    if (!loadingMessage || !content) {
        console.error('Elementos del DOM no encontrados');
        return;
    }

    try {
        console.log('Cargando mega evento ID:', megaEventoId);
        console.log('API URL:', `${API_BASE_URL}/api/mega-eventos/${megaEventoId}`);
        
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
                <div class="alert alert-danger" style="background: #f8d7da; border: 1px solid #dc3545; color: #721c24; border-radius: 8px; padding: 1rem;">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Error: ${data.error || 'Error al cargar el mega evento'}
                </div>
            `;
            return;
        }

        if (!data.mega_evento) {
            loadingMessage.innerHTML = `
                <div class="alert alert-warning" style="background: #fff3cd; border: 1px solid #ffc107; color: #856404; border-radius: 8px; padding: 1rem;">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    No se encontró información del mega evento
                </div>
            `;
            return;
        }

        const mega = data.mega_evento;
        console.log('Mega evento recibido:', mega);
        
        try {
            displayMegaEvento(mega);
            loadingMessage.style.display = 'none';
            content.style.display = 'block';
            
            // Configurar botón de compartir
            configurarBotonesCompartir(megaEventoId, mega);
            // Cargar contador de compartidos
            cargarContadorCompartidosMegaEvento(megaEventoId);
            // Cargar reacciones
            verificarReaccionMegaEvento();
            cargarReaccionesMegaEvento();
            // Iniciar actualización en tiempo real
            iniciarActualizacionTiempoRealMegaEvento();
            console.log('Mega evento mostrado correctamente');
        } catch (displayError) {
            console.error('Error al mostrar el mega evento:', displayError);
            loadingMessage.innerHTML = `
                <div class="alert alert-danger" style="background: #f8d7da; border: 1px solid #dc3545; color: #721c24; border-radius: 8px; padding: 1rem;">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Error al mostrar el mega evento: ${displayError.message}
                </div>
            `;
        }

    } catch (error) {
        console.error('Error en loadMegaEvento:', error);
        loadingMessage.innerHTML = `
            <div class="alert alert-danger" style="background: #f8d7da; border: 1px solid #dc3545; color: #721c24; border-radius: 8px; padding: 1rem;">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Error de conexión al cargar el mega evento: ${error.message}
            </div>
        `;
    }
}

function displayMegaEvento(mega) {
    console.log('Iniciando displayMegaEvento con:', mega);
    
    try {
        // Helper para construir URL de imagen
        function buildImageUrl(imgUrl) {
            if (!imgUrl || imgUrl.trim() === '') return null;
            if (imgUrl.startsWith('http://') || imgUrl.startsWith('https://')) return imgUrl;
            if (imgUrl.startsWith('/storage/')) return `${window.location.origin}${imgUrl}`;
            if (imgUrl.startsWith('storage/')) return `${window.location.origin}/${imgUrl}`;
            return `${window.location.origin}/storage/${imgUrl}`;
        }

        // Banner con imagen principal
        const banner = document.getElementById('eventBanner');
        const bannerImage = document.getElementById('bannerImage');
        if (bannerImage && mega.imagenes && Array.isArray(mega.imagenes) && mega.imagenes.length > 0) {
            const primeraImagen = buildImageUrl(mega.imagenes[0]);
            if (primeraImagen) {
                bannerImage.style.backgroundImage = `url(${primeraImagen})`;
            }
        }

        // Título
        const tituloEl = document.getElementById('titulo');
        if (tituloEl) {
            tituloEl.textContent = mega.titulo || 'Sin título';
        }

    // Categoría badge (en el banner)
    const categoriaBadgeEl = document.getElementById('categoriaBadge');
    if (categoriaBadgeEl) {
        if (mega.categoria) {
            categoriaBadgeEl.innerHTML = '<span class="badge badge-success" style="background: #00A36C !important; color: white !important;">' + mega.categoria.charAt(0).toUpperCase() + mega.categoria.slice(1) + '</span>';
        } else {
            categoriaBadgeEl.style.display = 'none';
        }
    }

        // Estado badge (en el banner)
        const estadoBadges = {
            'planificacion': { class: 'badge-secondary', text: 'Planificación' },
            'activo': { class: 'badge-success', text: 'Activo' },
            'en_curso': { class: 'badge-info', text: 'En Curso' },
            'finalizado': { class: 'badge-primary', text: 'Finalizado' },
            'cancelado': { class: 'badge-danger', text: 'Cancelado' }
        };
        const estadoInfo = estadoBadges[mega.estado] || { class: 'badge-secondary', text: mega.estado || 'N/A' };
        const estadoBadgeEl = document.getElementById('estadoBadge');
        if (estadoBadgeEl) {
            estadoBadgeEl.className = `badge ${estadoInfo.class}`;
            estadoBadgeEl.textContent = estadoInfo.text;
        }

        // Público/Privado badge (en el banner)
        const publicoBadgeEl = document.getElementById('publicoBadge');
        if (publicoBadgeEl) {
            if (mega.es_publico) {
                publicoBadgeEl.className = 'badge badge-info';
                publicoBadgeEl.textContent = 'Público';
            } else {
                publicoBadgeEl.className = 'badge badge-secondary';
                publicoBadgeEl.textContent = 'Privado';
            }
        }

        // Descripción
        const descripcionEl = document.getElementById('descripcion');
        if (descripcionEl) {
            const descripcion = mega.descripcion || 'Sin descripción disponible.';
            descripcionEl.textContent = descripcion;
        }

        // Fechas
        const fechaInicioEl = document.getElementById('fecha_inicio');
        if (fechaInicioEl) {
            if (mega.fecha_inicio) {
                const fechaInicio = new Date(mega.fecha_inicio);
                fechaInicioEl.textContent = fechaInicio.toLocaleString('es-ES', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            } else {
                fechaInicioEl.textContent = '-';
            }
        }

        const fechaFinEl = document.getElementById('fecha_fin');
        if (fechaFinEl) {
            if (mega.fecha_fin) {
                const fechaFin = new Date(mega.fecha_fin);
                fechaFinEl.textContent = fechaFin.toLocaleString('es-ES', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            } else {
                fechaFinEl.textContent = '-';
            }
        }

        // Ubicación - Mostrar de forma simple y directa
        const ubicacionContainer = document.getElementById('ubicacionContainer');
        if (!ubicacionContainer) {
            console.error('No se encontró el elemento ubicacionContainer');
            throw new Error('Elemento ubicacionContainer no encontrado');
        }
        
        const ubicacion = mega.ubicacion || 'No especificada';
        
        // Mostrar la ubicación de forma simple y clara
        if (ubicacion && ubicacion !== 'No especificada' && ubicacion.trim() !== '') {
            ubicacionContainer.innerHTML = `
                <p class="mb-0 text-muted">
                    <i class="fas fa-map-marker-alt mr-2 text-danger"></i>${ubicacion}
                </p>
            `;
        } else {
            ubicacionContainer.innerHTML = `
                <p class="mb-0 text-muted">
                    <i class="fas fa-exclamation-circle mr-2 text-warning"></i>Ubicación no especificada
                </p>
            `;
        }

        // Capacidad
        const capacidadEl = document.getElementById('capacidad_maxima');
        if (capacidadEl) {
            capacidadEl.textContent = mega.capacidad_maxima 
                ? `${mega.capacidad_maxima} personas` 
                : 'Sin límite';
        }

        // ONG Organizadora
        const ongEl = document.getElementById('ong_organizadora');
        if (ongEl) {
            if (mega.ong_principal) {
                ongEl.textContent = mega.ong_principal.nombre_ong || '-';
            } else {
                ongEl.textContent = '-';
            }
        }

        // Fechas del sistema
        const fechaCreacionEl = document.getElementById('fecha_creacion');
        if (fechaCreacionEl) {
            if (mega.fecha_creacion) {
                const fechaCreacion = new Date(mega.fecha_creacion);
                fechaCreacionEl.textContent = fechaCreacion.toLocaleString('es-ES');
            } else {
                fechaCreacionEl.textContent = '-';
            }
        }

        const fechaActualizacionEl = document.getElementById('fecha_actualizacion');
        if (fechaActualizacionEl) {
            if (mega.fecha_actualizacion) {
                const fechaActualizacion = new Date(mega.fecha_actualizacion);
                fechaActualizacionEl.textContent = fechaActualizacion.toLocaleString('es-ES');
            } else {
                fechaActualizacionEl.textContent = '-';
            }
        }

    // Estados en el sidebar (usando innerHTML para badges con estilos)
    const estadoBadgesHTML = {
        'planificacion': '<span class="badge badge-secondary" style="background: #333333 !important; color: white !important;">Planificación</span>',
        'activo': '<span class="badge badge-success" style="background: #00A36C !important; color: white !important;">Activo</span>',
        'en_curso': '<span class="badge badge-info" style="background: #0C2B44 !important; color: white !important;">En Curso</span>',
        'finalizado': '<span class="badge badge-primary" style="background: #0C2B44 !important; color: white !important;">Finalizado</span>',
        'cancelado': '<span class="badge badge-danger" style="background: #dc3545 !important; color: white !important;">Cancelado</span>'
    };
    
    // Solo establecer el estado en el sidebar si el elemento existe
    const estadoElement = document.getElementById('estado');
    if (estadoElement) {
        estadoElement.innerHTML = estadoBadgesHTML[mega.estado] || '<span class="badge badge-secondary" style="background: #333333 !important; color: white !important;">' + (mega.estado || 'N/A') + '</span>';
    }

    const esPublicoElement = document.getElementById('es_publico');
    if (esPublicoElement) {
        esPublicoElement.innerHTML = mega.es_publico 
            ? '<span class="badge badge-info" style="background: #0C2B44 !important; color: white !important;">Público</span>' 
            : '<span class="badge badge-secondary" style="background: #333333 !important; color: white !important;">Privado</span>';
    }

    const activoElement = document.getElementById('activo');
    if (activoElement) {
        activoElement.innerHTML = mega.activo 
            ? '<span class="badge badge-success" style="background: #00A36C !important; color: white !important;">Activo</span>' 
            : '<span class="badge badge-danger" style="background: #dc3545 !important; color: white !important;">Inactivo</span>';
    }

        // Imágenes (el modelo ya devuelve URLs completas)
        const imagenes = Array.isArray(mega.imagenes) ? mega.imagenes.filter(img => img && img.trim() !== '') : [];
        const imagenesContainer = document.getElementById('imagenesContainer');
        const imagenesCount = document.getElementById('imagenesCount');
        
        if (!imagenesContainer || !imagenesCount) {
            console.error('No se encontraron los elementos de imágenes');
            throw new Error('Elementos de imágenes no encontrados');
        }
    
    // Actualizar contador
    imagenesCount.textContent = imagenes.length;
    
    if (imagenes.length === 0) {
        imagenesContainer.innerHTML = `
            <div class="col-12">
                <div class="text-center py-5 bg-light rounded">
                    <div class="bg-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-images fa-3x text-white"></i>
                    </div>
                    <h5 class="font-weight-bold text-dark mb-2">No hay imágenes disponibles</h5>
                    <p class="text-muted mb-0">Las imágenes aparecerán aquí cuando se agreguen al mega evento.</p>
                </div>
            </div>
        `;
    } else {
        imagenesContainer.innerHTML = '';
        imagenes.forEach((imgUrl, index) => {
            if (!imgUrl || imgUrl.trim() === '') return;
            
            // Construir URL correcta usando buildImageUrl
            const fullUrl = buildImageUrl(imgUrl);

            // Crear elementos con diseño minimalista mejorado
            const colDiv = document.createElement('div');
            colDiv.className = 'col-md-4 col-sm-6 mb-4';
            
            const imgWrapper = document.createElement('div');
            imgWrapper.className = 'position-relative overflow-hidden';
            imgWrapper.style.cssText = 'height: 220px; background: #f8f9fa; border-radius: 12px; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.05);';
            imgWrapper.onmouseenter = function() { 
                this.style.transform = 'translateY(-4px)'; 
                this.style.boxShadow = '0 8px 16px rgba(0,0,0,0.1)';
            };
            imgWrapper.onmouseleave = function() { 
                this.style.transform = 'translateY(0)'; 
                this.style.boxShadow = '0 2px 4px rgba(0,0,0,0.05)';
            };
            imgWrapper.onclick = () => abrirImagen(fullUrl, index + 1);
            
            // Overlay para efecto hover
            const overlay = document.createElement('div');
            overlay.className = 'position-absolute w-100 h-100 d-flex align-items-center justify-content-center';
            overlay.style.cssText = 'top: 0; left: 0; background: rgba(0,0,0,0); transition: background 0.3s; pointer-events: none;';
            overlay.onmouseenter = function() { this.style.background = 'rgba(0,0,0,0.1)'; };
            overlay.onmouseleave = function() { this.style.background = 'rgba(0,0,0,0)'; };
            
            const icon = document.createElement('i');
            icon.className = 'fas fa-search-plus fa-lg text-white';
            icon.style.cssText = 'opacity: 0; transition: opacity 0.3s;';
            icon.onmouseenter = function() { this.style.opacity = '1'; };
            icon.onmouseleave = function() { this.style.opacity = '0'; };
            
            overlay.appendChild(icon);
            
            const img = document.createElement('img');
            img.src = fullUrl;
            img.className = 'w-100 h-100';
            img.style.cssText = 'object-fit: cover; display: block;';
            img.alt = `Imagen ${index + 1}`;
            img.loading = 'lazy'; // Lazy loading para mejor rendimiento
            
            // Manejo de errores mejorado con múltiples intentos
            img.onerror = function() {
                console.error('Error cargando imagen:', fullUrl);
                this.onerror = null;
                
                // Intentar con diferentes formatos de URL
                const altUrls = [
                    imgUrl.startsWith('/storage/') ? `${window.location.origin}${imgUrl}` : null,
                    imgUrl.startsWith('storage/') ? `${window.location.origin}/${imgUrl}` : null,
                    `${window.location.origin}/storage/${imgUrl}`,
                    `${API_BASE_URL}${imgUrl.startsWith('/') ? imgUrl : '/' + imgUrl}`
                ].filter(url => url && url !== fullUrl);
                
                let attemptIndex = 0;
                const tryNextUrl = () => {
                    if (attemptIndex < altUrls.length) {
                        this.src = altUrls[attemptIndex];
                        attemptIndex++;
                        this.onerror = tryNextUrl;
                    } else {
                        // Si todas fallan, usar placeholder SVG
                        this.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="400" height="220"%3E%3Crect fill="%23f8f9fa" width="400" height="220"/%3E%3Ctext x="50%25" y="50%25" text-anchor="middle" dy=".3em" fill="%23adb5bd" font-family="Arial" font-size="14"%3EImagen no disponible%3C/text%3E%3C/svg%3E';
                        this.style.objectFit = 'contain';
                        this.style.padding = '20px';
                    }
                };
                tryNextUrl();
            };
            
            imgWrapper.appendChild(img);
            imgWrapper.appendChild(overlay);
            colDiv.appendChild(imgWrapper);
            imagenesContainer.appendChild(colDiv);
        });
    }

        // Link de editar
        const editLinkEl = document.getElementById('editLink');
        if (editLinkEl) {
            editLinkEl.href = `/ong/mega-eventos/${megaEventoId}/editar`;
        }
        
        const seguimientoLinkEl = document.getElementById('seguimientoLink');
        if (seguimientoLinkEl) {
            seguimientoLinkEl.href = `/ong/mega-eventos/${megaEventoId}/seguimiento`;
        }

        // Mapa - Usar geocodificación inversa si no hay coordenadas
        const mapContainer = document.getElementById('map');
        if (mapContainer) {
            const inicializarMapa = async () => {
                let lat = mega.lat;
                let lng = mega.lng;
                let direccionCompleta = mega.ubicacion || '';

                // Si no hay coordenadas pero hay ubicación, hacer geocodificación
                if ((!lat || !lng) && direccionCompleta && direccionCompleta.trim() !== '') {
                    try {
                        // Usar Nominatim (OpenStreetMap) para geocodificación
                        const geocodeUrl = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(direccionCompleta)}&limit=1`;
                        const geocodeRes = await fetch(geocodeUrl, {
                            headers: {
                                'User-Agent': 'MegaEventoApp/1.0'
                            }
                        });
                        const geocodeData = await geocodeRes.json();
                        
                        if (geocodeData && geocodeData.length > 0) {
                            lat = parseFloat(geocodeData[0].lat);
                            lng = parseFloat(geocodeData[0].lon);
                        }
                    } catch (error) {
                        console.warn('Error en geocodificación:', error);
                    }
                }

                // Si tenemos coordenadas, mostrar el mapa
                if (lat && lng) {
                    mapContainer.style.display = 'block';
                    const map = L.map('map').setView([lat, lng], 13);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap contributors'
                    }).addTo(map);
                    L.marker([lat, lng]).addTo(map).bindPopup(direccionCompleta || 'Ubicación del mega evento');
                } else {
                    mapContainer.style.display = 'none';
                }
            };

            inicializarMapa();
        }
        
        // Guardar información del mega evento para compartir
        if (window.megaEventoParaCompartir) {
            window.megaEventoParaCompartir.titulo = mega.titulo || 'Mega Evento';
            window.megaEventoParaCompartir.descripcion = mega.descripcion || '';
        }
        
        console.log('displayMegaEvento completado exitosamente');
    } catch (error) {
        console.error('Error en displayMegaEvento:', error);
        throw error; // Re-lanzar el error para que loadMegaEvento lo capture
    }
}

// Función para abrir imagen en modal o nueva ventana
function abrirImagen(url, index) {
    Swal.fire({
        imageUrl: url,
        imageAlt: 'Imagen ' + index + ' del mega evento',
        showCloseButton: true,
        showConfirmButton: false,
        width: '90%',
        padding: '1rem',
        background: 'rgba(0,0,0,0.9)',
        customClass: {
            popup: 'swal-image-popup'
        }
    });
}

// Configurar botones de compartir
function configurarBotonesCompartir(megaEventoId, mega) {
    const btnCompartir = document.getElementById('btnCompartir');
    
    if (btnCompartir) {
        btnCompartir.onclick = () => {
            mostrarModalCompartirMegaEvento();
        };
    }
    
    // Guardar información del mega evento para compartir
    window.megaEventoParaCompartir = {
        mega_evento_id: megaEventoId,
        titulo: mega.titulo || 'Mega Evento',
        descripcion: mega.descripcion || '',
        url: typeof getPublicUrl !== 'undefined' 
            ? getPublicUrl(`/mega-evento/${megaEventoId}/qr`)
            : `http://192.168.0.6:8000/mega-evento/${megaEventoId}/qr`
    };
}

// Mostrar modal de compartir
function mostrarModalCompartirMegaEvento() {
    const modal = document.getElementById('modalCompartir');
    if (modal) {
        if (typeof $ !== 'undefined') {
            $(modal).modal('show');
        } else {
            modal.style.display = 'block';
            modal.classList.add('show');
            document.body.classList.add('modal-open');
            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            backdrop.id = 'modalBackdropCompartir';
            backdrop.onclick = () => cerrarModalCompartir();
            document.body.appendChild(backdrop);
        }
    }
}

// Cerrar modal de compartir
function cerrarModalCompartir() {
    const modal = document.getElementById('modalCompartir');
    if (modal) {
        if (typeof $ !== 'undefined') {
            $(modal).modal('hide');
        } else {
            modal.style.display = 'none';
            modal.classList.remove('show');
            document.body.classList.remove('modal-open');
            const backdrop = document.getElementById('modalBackdropCompartir');
            if (backdrop) {
                backdrop.remove();
            }
        }
    }
    // Ocultar QR
    const qrContainer = document.getElementById('qrContainer');
    if (qrContainer) {
        qrContainer.style.display = 'none';
    }
}

// Registrar compartido
async function registrarCompartidoMegaEvento(megaEventoId, metodo) {
    try {
        const token = localStorage.getItem('token');
        // Usar la ruta pública que acepta tanto usuarios autenticados como no autenticados
        const url = `${API_BASE_URL}/api/mega-eventos/${megaEventoId}/compartir-publico`;
        
        const headers = {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        };
        
        // Si hay token, incluirlo (para usuarios autenticados: ONG, externos, empresas)
        if (token) {
            headers['Authorization'] = `Bearer ${token}`;
        }
        
        await fetch(url, {
            method: 'POST',
            headers: headers,
            body: JSON.stringify({ metodo: metodo })
        });
        
        // Actualizar contador de compartidos
        await cargarContadorCompartidosMegaEvento(megaEventoId);
    } catch (error) {
        console.warn('Error registrando compartido:', error);
    }
}

// Cargar contador de compartidos
async function cargarContadorCompartidosMegaEvento(megaEventoId) {
    try {
        const res = await fetch(`${API_BASE_URL}/api/mega-eventos/${megaEventoId}/compartidos/total`);
        const data = await res.json();
        
        if (data.success) {
            const contadorCompartidos = document.getElementById('contadorCompartidos');
            if (contadorCompartidos) {
                contadorCompartidos.textContent = data.total_compartidos || 0;
            }
        }
    } catch (error) {
        console.warn('Error cargando contador de compartidos:', error);
    }
}

// Copiar enlace
async function copiarEnlaceMegaEvento() {
    const megaEvento = window.megaEventoParaCompartir;
    if (!megaEvento) return;

    // Registrar compartido en backend
    await registrarCompartidoMegaEvento(megaEvento.mega_evento_id, 'link');

    const url = megaEvento.url;
    
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(url).then(() => {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: '¡Enlace copiado!',
                    text: 'El enlace se ha copiado al portapapeles',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                alert('Enlace copiado al portapapeles');
            }
            cerrarModalCompartir();
        }).catch(err => {
            console.error('Error al copiar:', err);
            fallbackCopiarEnlaceMegaEvento(url);
        });
    } else {
        fallbackCopiarEnlaceMegaEvento(url);
    }
}

function fallbackCopiarEnlaceMegaEvento(url) {
    const textarea = document.createElement('textarea');
    textarea.value = url;
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    textarea.select();
    try {
        document.execCommand('copy');
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: '¡Enlace copiado!',
                text: 'El enlace se ha copiado al portapapeles',
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            alert('Enlace copiado al portapapeles');
        }
        cerrarModalCompartir();
    } catch (err) {
        console.error('Error al copiar:', err);
        alert('Error al copiar el enlace. Por favor, cópialo manualmente: ' + url);
    }
    document.body.removeChild(textarea);
}

// Mostrar QR Code
async function mostrarQRMegaEvento() {
    const megaEvento = window.megaEventoParaCompartir;
    if (!megaEvento) return;

    // Registrar compartido en backend
    await registrarCompartidoMegaEvento(megaEvento.mega_evento_id, 'qr');

    const qrContainer = document.getElementById('qrContainer');
    const qrcodeDiv = document.getElementById('qrcode');
    
    if (!qrContainer || !qrcodeDiv) return;

    const qrUrl = megaEvento.url;
    
    // Limpiar contenido anterior
    qrcodeDiv.innerHTML = '';
    
    // Mostrar contenedor primero
    qrContainer.style.display = 'block';
    
    // Agregar indicador de carga
    qrcodeDiv.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x" style="color: #0C2B44;"></i><p class="mt-2" style="color: #333;">Generando QR...</p></div>';
    
    // Intentar cargar QRCode si no está disponible
    if (typeof QRCode === 'undefined') {
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js';
        script.onload = function() {
            generarQRCodeMegaEvento(qrUrl, qrcodeDiv);
        };
        script.onerror = function() {
            generarQRConAPIMegaEvento(qrUrl, qrcodeDiv);
        };
        document.head.appendChild(script);
    } else {
        generarQRCodeMegaEvento(qrUrl, qrcodeDiv);
    }
}

// Función auxiliar para generar QR con la librería
function generarQRCodeMegaEvento(qrUrl, qrcodeDiv) {
    try {
        QRCode.toCanvas(qrcodeDiv, qrUrl, {
            width: 250,
            margin: 2,
            color: {
                dark: '#0C2B44',
                light: '#FFFFFF'
            },
            errorCorrectionLevel: 'M'
        }, function (error) {
            if (error) {
                console.error('Error generando QR:', error);
                generarQRConAPIMegaEvento(qrUrl, qrcodeDiv);
            } else {
                const canvas = qrcodeDiv.querySelector('canvas');
                if (canvas) {
                    canvas.style.display = 'block';
                    canvas.style.margin = '0 auto';
                }
            }
        });
    } catch (error) {
        console.error('Error en generarQRCode:', error);
        generarQRConAPIMegaEvento(qrUrl, qrcodeDiv);
    }
}

// Función alternativa usando API de QR
function generarQRConAPIMegaEvento(qrUrl, qrcodeDiv) {
    try {
        const apiUrl = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodeURIComponent(qrUrl)}&bgcolor=FFFFFF&color=0C2B44`;
        const img = document.createElement('img');
        img.src = apiUrl;
        img.alt = 'QR Code';
        img.style.cssText = 'display: block; margin: 0 auto; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);';
        img.onerror = function() {
            qrcodeDiv.innerHTML = `
                <div class="text-center p-3">
                    <p class="text-danger mb-2" style="font-size: 0.9rem;">Error cargando generador de QR.</p>
                    <p class="text-muted mb-2" style="font-size: 0.85rem;">Por favor, usa el enlace directo:</p>
                    <a href="${qrUrl}" target="_blank" class="btn btn-sm" style="background: #0C2B44; color: white;">Abrir enlace</a>
                </div>
            `;
        };
        qrcodeDiv.innerHTML = '';
        qrcodeDiv.appendChild(img);
    } catch (error) {
        console.error('Error generando QR con API:', error);
        qrcodeDiv.innerHTML = `
            <div class="text-center p-3">
                <p class="text-danger mb-2" style="font-size: 0.9rem;">Error cargando generador de QR.</p>
                <p class="text-muted mb-2" style="font-size: 0.85rem;">Por favor, usa el enlace directo:</p>
                <a href="${qrUrl}" target="_blank" class="btn btn-sm" style="background: #0C2B44; color: white;">Abrir enlace</a>
            </div>
        `;
    }
}

// Funciones para reacciones de mega eventos
async function verificarReaccionMegaEvento() {
    const token = localStorage.getItem('token');
    const btnReaccionar = document.getElementById('btnReaccionar');
    const iconoCorazon = document.getElementById('iconoCorazon');
    const textoReaccion = document.getElementById('textoReaccion');
    const contadorReacciones = document.getElementById('contadorReacciones');

    if (!btnReaccionar) return;

    try {
        const res = await fetch(`${API_BASE_URL}/api/mega-eventos/reacciones/verificar/${megaEventoId}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        const data = await res.json();
        if (data.success) {
            if (data.reaccionado) {
                iconoCorazon.className = 'fas fa-heart mr-2';
                btnReaccionar.classList.remove('btn-outline-danger');
                btnReaccionar.classList.add('btn-danger');
                textoReaccion.textContent = 'Te gusta';
            } else {
                iconoCorazon.className = 'fas fa-heart mr-2';
                btnReaccionar.classList.remove('btn-danger');
                btnReaccionar.classList.add('btn-outline-danger');
                textoReaccion.textContent = 'Me gusta';
            }
            contadorReacciones.textContent = data.total_reacciones || 0;
        }

        // Agregar evento click al botón
        btnReaccionar.onclick = async () => {
            await toggleReaccionMegaEvento();
        };
    } catch (error) {
        console.warn('Error verificando reacción:', error);
    }
}

async function toggleReaccionMegaEvento() {
    const token = localStorage.getItem('token');
    const btnReaccionar = document.getElementById('btnReaccionar');
    const iconoCorazon = document.getElementById('iconoCorazon');
    const textoReaccion = document.getElementById('textoReaccion');
    const contadorReacciones = document.getElementById('contadorReacciones');

    try {
        const res = await fetch(`${API_BASE_URL}/api/mega-eventos/reacciones/toggle`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ mega_evento_id: megaEventoId })
        });

        const data = await res.json();
        if (data.success) {
            if (data.reaccionado) {
                iconoCorazon.className = 'fas fa-heart mr-2';
                btnReaccionar.classList.remove('btn-outline-danger');
                btnReaccionar.classList.add('btn-danger');
                textoReaccion.textContent = 'Te gusta';
            } else {
                iconoCorazon.className = 'fas fa-heart mr-2';
                btnReaccionar.classList.remove('btn-danger');
                btnReaccionar.classList.add('btn-outline-danger');
                textoReaccion.textContent = 'Me gusta';
            }
            contadorReacciones.textContent = data.total_reacciones || 0;
            // Recargar lista de reacciones
            cargarReaccionesMegaEvento();
        }
    } catch (error) {
        console.error('Error en toggle reacción:', error);
    }
}

// Cargar lista de usuarios que reaccionaron
async function cargarReaccionesMegaEvento() {
    const container = document.getElementById('reaccionesContainer');
    if (!container) return;

    const token = localStorage.getItem('token');

    try {
        container.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border" role="status" style="color: #00A36C; width: 3rem; height: 3rem;">
                    <span class="sr-only">Cargando...</span>
                </div>
                <p class="mt-3" style="color: #333333; font-weight: 500;">Cargando reacciones...</p>
            </div>
        `;

        const res = await fetch(`${API_BASE_URL}/api/mega-eventos/reacciones/${megaEventoId}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        const data = await res.json();

        if (!res.ok || !data.success) {
            container.innerHTML = `
                <div class="alert" style="background: #fff3cd; border: 1px solid #ffc107; color: #856404; border-radius: 8px; padding: 1rem;">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    ${data.error || 'Error al cargar reacciones'}
                </div>
            `;
            return;
        }

        if (!data.reacciones || data.reacciones.length === 0) {
            container.innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-heart fa-3x mb-3 text-danger" style="opacity: 0.3;"></i>
                    <p class="mb-0 text-muted">Aún no hay reacciones en este mega evento</p>
                </div>
            `;
            return;
        }

        let html = '<div class="row">';
        data.reacciones.forEach((reaccion, index) => {
            const fechaReaccion = new Date(reaccion.fecha_reaccion).toLocaleDateString('es-ES', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            const fotoPerfil = reaccion.foto_perfil || null;
            const inicialNombre = reaccion.nombre ? reaccion.nombre.charAt(0).toUpperCase() : '?';
            const tipoBadge = reaccion.tipo === 'registrado' 
                ? '<span class="badge badge-success">Registrado</span>'
                : '<span class="badge badge-warning">No registrado</span>';

            html += `
                <div class="col-md-6 col-lg-4 mb-3 reaccion-card" style="animation-delay: ${index * 0.1}s;">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                ${fotoPerfil ? `
                                    <img src="${fotoPerfil}" alt="${reaccion.nombre}" class="rounded-circle mr-3" style="width: 50px; height: 50px; object-fit: cover; border: 3px solid #00A36C;">
                                ` : `
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mr-3 bg-primary text-white" style="width: 50px; height: 50px; font-weight: 600; font-size: 1.2rem;">
                                        ${inicialNombre}
                                    </div>
                                `}
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-1">
                                        <h6 class="mb-0 font-weight-bold text-dark">${reaccion.nombre || 'N/A'}</h6>
                                        ${tipoBadge}
                                    </div>
                                    <small class="text-muted">
                                        <i class="fas fa-envelope mr-1 text-info"></i> ${reaccion.correo || 'N/A'}
                                    </small>
                                </div>
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                    <i class="fas fa-heart text-danger"></i>
                                </div>
                            </div>
                            <div class="mt-3 pt-3 border-top">
                                <small class="text-muted">
                                    <i class="fas fa-clock mr-1 text-secondary"></i> 
                                    ${fechaReaccion}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        container.innerHTML = html;
    } catch (error) {
        console.error('Error cargando reacciones:', error);
        container.innerHTML = `
            <div class="alert alert-danger" style="background: #f8d7da; border: 1px solid #dc3545; color: #721c24; border-radius: 8px; padding: 1rem;">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Error de conexión al cargar reacciones
            </div>
        `;
    }
}

// Actualizar contadores en tiempo real
let intervaloContadoresMegaEvento = null;
function iniciarActualizacionTiempoRealMegaEvento() {
    // Actualizar cada 5 segundos
    intervaloContadoresMegaEvento = setInterval(() => {
        verificarReaccionMegaEvento();
        cargarContadorCompartidosMegaEvento(megaEventoId);
    }, 5000);
}

// Limpiar intervalo al salir de la página
window.addEventListener('beforeunload', function() {
    if (intervaloContadoresMegaEvento) {
        clearInterval(intervaloContadoresMegaEvento);
    }
});
</script>
<style>
.swal-image-popup {
    max-width: 90vw !important;
}
.swal-image-popup img {
    max-width: 100%;
    max-height: 80vh;
    object-fit: contain;
}

/* Animaciones para reacciones */
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

.reaccion-card {
    animation: fadeInUp 0.5s ease-out;
}

.reaccion-card .fa-heart {
    animation: heartBeat 0.6s ease-in-out;
}

.reaccion-card:hover .fa-heart {
    animation: pulse 1s ease-in-out infinite;
    color: #dc3545 !important;
}

@keyframes heartBeat {
    0%, 100% {
        transform: scale(1);
    }
    25% {
        transform: scale(1.2);
    }
    50% {
        transform: scale(1);
    }
    75% {
        transform: scale(1.1);
    }
}

/* Animación para el contador de reacciones */
#contadorReacciones {
    transition: all 0.3s ease;
}

#contadorReacciones.animate {
    animation: pulse 0.5s ease-in-out;
    color: #dc3545;
    font-weight: 700;
}

/* Animación para el botón de actualizar reacciones */
.btn-actualizar-reacciones:active {
    transform: rotate(360deg);
    transition: transform 0.5s ease;
}
</style>
@endpush


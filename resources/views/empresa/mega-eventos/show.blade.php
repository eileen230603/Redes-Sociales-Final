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
        <!-- Banner Superior con Imagen Principal - Mejorado -->
        <div id="eventBanner" class="position-relative" style="height: 450px; background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); overflow: hidden; border-radius: 0 0 24px 24px;">
            <div id="bannerImage" class="w-100 h-100" style="background-size: cover; background-position: center; opacity: 0.25; transition: transform 0.5s ease;"></div>
            <div class="position-absolute" style="top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(to bottom, rgba(23, 162, 184, 0.4) 0%, rgba(19, 132, 150, 0.7) 100%);"></div>
            <div class="position-absolute" style="bottom: 0; left: 0; right: 0; padding: 3rem 2rem; color: white;">
                <div class="container">
                    <div class="d-flex align-items-center mb-3">
                        <div class="mr-3" style="width: 64px; height: 64px; background: rgba(255,255,255,0.15); border-radius: 16px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px); box-shadow: 0 4px 16px rgba(0,0,0,0.2); animation: pulse 2s ease-in-out infinite;">
                            <i class="fas fa-star" style="font-size: 2rem;"></i>
                        </div>
                        <div style="flex: 1;">
                            <h1 id="titulo" class="mb-2" style="font-size: 2.75rem; font-weight: 700; text-shadow: 2px 2px 8px rgba(0,0,0,0.5); letter-spacing: -0.5px; line-height: 1.2; animation: fadeInUp 0.6s ease-out;">-</h1>
                            <div class="d-flex flex-wrap align-items-center" style="gap: 0.75rem;">
                                <span id="categoriaBadge" class="badge" style="font-size: 0.95rem; padding: 0.6em 1.2em; background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); border-radius: 50px; font-weight: 500; animation: fadeInUp 0.8s ease-out;">-</span>
                                <span id="estadoBadge" class="badge" style="font-size: 0.95rem; padding: 0.6em 1.2em; border-radius: 50px; font-weight: 500; animation: fadeInUp 0.9s ease-out;">-</span>
                                <span id="publicoBadge" class="badge" style="font-size: 0.95rem; padding: 0.6em 1.2em; border-radius: 50px; font-weight: 500; animation: fadeInUp 1s ease-out;">-</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mt-4">
            <!-- Botones de Acción - Mejorados -->
            <div class="d-flex justify-content-end mb-4 flex-wrap" style="gap: 0.75rem;">
                <a href="/empresa/mega-eventos" class="btn btn-outline-secondary" style="border-radius: 12px; padding: 0.6rem 1.5rem; font-weight: 500; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(0,0,0,0.08);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.12)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.08)'">
                    <i class="fas fa-arrow-left mr-2"></i> Volver
                </a>
                <button class="btn btn-info" id="btnAuspiciar" style="border-radius: 12px; padding: 0.6rem 1.5rem; font-weight: 500; transition: all 0.3s ease; background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); border: none; box-shadow: 0 2px 8px rgba(23,162,184,0.2);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(23,162,184,0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(23,162,184,0.2)'">
                    <i class="fas fa-hand-holding-usd mr-2"></i> Auspiciar / Ser patrocinador
                </button>
            </div>

            <div class="row">
                <!-- Columna Principal -->
                <div class="col-lg-8">
                    <!-- Descripción -->
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; animation: fadeInUp 0.5s ease-out;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-4">
                                <div class="section-icon mr-3" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);">
                                    <i class="fas fa-align-left"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0" style="font-weight: 700; color: #2c3e50; font-size: 1.1rem;">
                                        Descripción
                                    </h5>
                                    <p class="mb-0 text-muted" style="font-size: 0.85rem; margin-top: 0.25rem;">
                                        Información detallada del mega evento
                                    </p>
                                </div>
                            </div>
                            <p id="descripcion" class="mb-0" style="color: #495057; line-height: 1.8; font-size: 1rem;">-</p>
                        </div>
                    </div>

                    <!-- Información del Mega Evento -->
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; animation: fadeInUp 0.6s ease-out;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-4">
                                <div class="section-icon mr-3" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0" style="font-weight: 700; color: #2c3e50; font-size: 1.1rem;">
                                        Información del Mega Evento
                                    </h5>
                                    <p class="mb-0 text-muted" style="font-size: 0.85rem; margin-top: 0.25rem;">
                                        Detalles importantes del evento
                                    </p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="info-item">
                                        <div class="info-icon" style="color: #17a2b8;">
                                            <i class="fas fa-calendar-alt"></i>
                                        </div>
                                        <div class="info-content">
                                            <h6 class="info-label">Fecha de Inicio</h6>
                                            <p id="fecha_inicio" class="info-value">-</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="info-item">
                                        <div class="info-icon" style="color: #17a2b8;">
                                            <i class="fas fa-calendar-check"></i>
                                        </div>
                                        <div class="info-content">
                                            <h6 class="info-label">Fecha de Fin</h6>
                                            <p id="fecha_fin" class="info-value">-</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="info-item">
                                        <div class="info-icon" style="color: #17a2b8;">
                                            <i class="fas fa-users"></i>
                                        </div>
                                        <div class="info-content">
                                            <h6 class="info-label">Capacidad Máxima</h6>
                                            <p id="capacidad_maxima" class="info-value">-</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 mb-4">
                                    <div class="info-item" style="background: linear-gradient(135deg, rgba(23, 162, 184, 0.05) 0%, rgba(19, 132, 150, 0.05) 100%); padding: 1.5rem; border-radius: 12px; border-left: 4px solid #17a2b8;">
                                        <div class="info-icon" style="font-size: 1.5rem; color: #17a2b8;">
                                            <i class="fas fa-map-marker-alt"></i>
                                            </div>
                                        <div class="info-content">
                                            <h6 class="info-label" style="color: #2c3e50; font-weight: 600; margin-bottom: 0.5rem;">Ubicación del Evento</h6>
                                                <div id="ubicacionContainer">
                                                <p id="ubicacion" class="info-value" style="font-size: 1rem; font-weight: 500;">-</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="section-icon mr-3" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);">
                                            <i class="fas fa-map"></i>
                                        </div>
                                        <div>
                                            <h5 class="mb-0" style="font-weight: 700; color: #2c3e50; font-size: 1.1rem;">
                                                Mapa de Ubicación
                                    </h5>
                                        </div>
                                    </div>
                                    <div id="map" style="height: 350px; border-radius: 12px; border: 1px solid #e9ecef; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: all 0.3s ease;" onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.12)'" onmouseout="this.style.boxShadow='0 2px 8px rgba(0,0,0,0.08)'"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Galería de Imágenes -->
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; animation: fadeInUp 0.7s ease-out;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="section-icon mr-3" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);">
                                        <i class="fas fa-images"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0" style="font-weight: 700; color: #2c3e50; font-size: 1.1rem;">
                                            Galería de Imágenes
                                        </h5>
                                        <p class="mb-0 text-muted" style="font-size: 0.85rem; margin-top: 0.25rem;">
                                            Imágenes promocionales del mega evento
                                        </p>
                                    </div>
                                </div>
                                <span id="imagenesCount" class="badge" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; font-size: 0.9rem; padding: 0.5em 1em; border-radius: 50px; font-weight: 600;">0</span>
                            </div>
                            <div id="imagenesContainer" class="row">
                                <div class="col-12 text-center py-4">
                                    <div class="spinner-border" role="status" style="color: #17a2b8; width: 3rem; height: 3rem;">
                                        <span class="sr-only">Cargando...</span>
                                    </div>
                                    <p class="mt-3 text-muted">Cargando imágenes...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- ONG Organizadora -->
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; position: sticky; top: 2rem; animation: fadeInUp 0.5s ease-out;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="section-icon mr-3" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);">
                                    <i class="fas fa-building"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0" style="font-weight: 700; color: #2c3e50; font-size: 1.1rem;">
                                        ONG Organizadora
                            </h5>
                                </div>
                            </div>
                            <div class="info-item">
                                <div id="ongAvatarContainer" class="mr-3" style="flex-shrink: 0; position: relative;">
                                    <!-- Avatar se cargará aquí dinámicamente -->
                                </div>
                                <div class="info-content">
                                    <p id="ong_organizadora" class="info-value" style="font-size: 1rem; font-weight: 500;">-</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información Adicional -->
                    <div class="card border-0 shadow-sm" style="border-radius: 16px; animation: fadeInUp 0.6s ease-out;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="section-icon mr-3" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0" style="font-weight: 700; color: #2c3e50; font-size: 1.1rem;">
                                        Información Adicional
                            </h5>
                                </div>
                            </div>
                            <div class="info-item mb-3 pb-3" style="border-bottom: 1px solid #e9ecef;">
                                <div class="info-icon" style="color: #17a2b8;">
                                    <i class="fas fa-flag"></i>
                                </div>
                                <div class="info-content" style="flex: 1;">
                                    <h6 class="info-label" style="font-size: 0.85rem; font-weight: 600; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.5rem;">Estado del Evento</h6>
                                    <div id="estado" class="info-value" style="font-size: 1rem; font-weight: 500;">-</div>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-icon" style="color: #17a2b8;">
                                    <i class="fas fa-eye"></i>
                                </div>
                                <div class="info-content" style="flex: 1;">
                                    <h6 class="info-label" style="font-size: 0.85rem; font-weight: 600; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.5rem;">Visibilidad</h6>
                                    <div id="es_publico" class="info-value" style="font-size: 1rem; font-weight: 500;">-</div>
                                </div>
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
    /* Variables CSS */
    :root {
        --primary-color: #17a2b8;
        --secondary-color: #138496;
        --text-dark: #2c3e50;
        --text-muted: #6c757d;
        --border-radius: 16px;
        --shadow-sm: 0 2px 8px rgba(0,0,0,0.08);
        --shadow-md: 0 4px 12px rgba(0,0,0,0.12);
        --shadow-lg: 0 8px 24px rgba(0,0,0,0.15);
    }
    
    /* Animaciones */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeInUp {
        from { 
            opacity: 0; 
            transform: translateY(30px); 
        }
        to { 
            opacity: 1; 
            transform: translateY(0); 
        }
    }

    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
            opacity: 1;
        }
        50% {
            transform: scale(1.05);
            opacity: 0.9;
        }
    }

    /* Contenido principal */
    #megaEventoContent {
        animation: fadeIn 0.5s ease-in;
    }

    /* Banner con efecto hover */
    #eventBanner:hover #bannerImage {
        transform: scale(1.05);
    }

    /* Section Icon (para títulos principales) */
    .section-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
        box-shadow: 0 4px 12px rgba(23, 162, 184, 0.2);
        flex-shrink: 0;
        transition: all 0.3s ease;
    }

    .section-icon:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(23, 162, 184, 0.3);
    }

    /* Info Icon (para items individuales) */
    .info-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        color: #17a2b8;
        font-size: 1.2rem;
        margin-right: 1rem;
        flex-shrink: 0;
        transition: all 0.3s ease;
    }

    .info-item {
        display: flex;
        align-items: flex-start;
        padding: 1rem;
        border-radius: 12px;
        transition: all 0.3s ease;
        margin-bottom: 0.5rem;
    }

    .info-item:hover {
        background: rgba(23, 162, 184, 0.03);
        transform: translateX(5px);
    }

    .info-item:hover .info-icon {
        color: #138496;
        transform: scale(1.1);
    }

    .info-content {
        flex: 1;
    }

    .info-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-muted);
        margin-bottom: 0.25rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-value {
        font-size: 1rem;
        color: var(--text-dark);
        margin: 0;
        font-weight: 500;
    }

    /* Cards mejoradas */
    .card {
        transition: all 0.3s ease;
        border: none;
    }

    .card:hover {
        box-shadow: var(--shadow-md) !important;
        transform: translateY(-2px);
    }

    /* Imágenes con efecto hover */
    #imagenesContainer img {
        transition: transform 0.3s ease;
    }

    #imagenesContainer .position-relative:hover img {
        transform: scale(1.05);
    }

    /* Badges mejorados */
    .badge {
        font-weight: 500;
        padding: 0.5em 0.8em;
        border-radius: 20px;
        transition: all 0.3s ease;
    }

    .badge:hover {
        transform: scale(1.05);
    }

    /* Mapa mejorado */
    #map {
        transition: all 0.3s ease;
        border-radius: 12px;
    }

    #map:hover {
        box-shadow: var(--shadow-md);
    }

    /* Botones mejorados */
    .btn {
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-2px);
    }

    .btn:active {
        transform: translateY(0);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .section-icon {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }

        .info-icon {
            font-size: 1rem;
        }

        #eventBanner {
            height: 350px !important;
        }

        #titulo {
            font-size: 2rem !important;
        }
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
    // Función para formatear fechas desde PostgreSQL sin conversión de zona horaria
    function formatearFechaPostgreSQL(fechaStr) {
        if (!fechaStr) return 'Fecha no especificada';
        try {
            let fechaObj;
            
            if (typeof fechaStr === 'string') {
                fechaStr = fechaStr.trim();
                
                // Patrones para diferentes formatos de fecha
                const mysqlPattern = /^(\d{4})-(\d{2})-(\d{2})[\sT](\d{2}):(\d{2}):(\d{2})(?:\.\d+)?(?:Z|[+-]\d{2}:\d{2})?$/;
                const isoPattern = /^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})(?:\.\d+)?(?:Z|[+-]\d{2}:\d{2})?$/;
                
                let match = fechaStr.match(mysqlPattern) || fechaStr.match(isoPattern);
                
                if (match) {
                    // Parsear manualmente para evitar conversión UTC
                    const [, year, month, day, hour, minute, second] = match;
                    fechaObj = new Date(
                        parseInt(year, 10),
                        parseInt(month, 10) - 1,
                        parseInt(day, 10),
                        parseInt(hour, 10),
                        parseInt(minute, 10),
                        parseInt(second || 0, 10)
                    );
                } else {
                    fechaObj = new Date(fechaStr);
                }
            } else {
                fechaObj = new Date(fechaStr);
            }
            
            if (isNaN(fechaObj.getTime())) return fechaStr;
            
            const año = fechaObj.getFullYear();
            const mes = fechaObj.getMonth();
            const dia = fechaObj.getDate();
            const horas = fechaObj.getHours();
            const minutos = fechaObj.getMinutes();
            
            const meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 
                          'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
            
            const horaFormateada = String(horas).padStart(2, '0');
            const minutoFormateado = String(minutos).padStart(2, '0');
            
            return `${dia} de ${meses[mes]} de ${año}, ${horaFormateada}:${minutoFormateado}`;
        } catch (error) {
            console.error('Error formateando fecha:', error);
            return fechaStr;
        }
    }

    // Fechas - Corregidas para obtener correctamente desde PostgreSQL
    if (mega.fecha_inicio) {
        document.getElementById('fecha_inicio').textContent = formatearFechaPostgreSQL(mega.fecha_inicio);
    }

    if (mega.fecha_fin) {
        document.getElementById('fecha_fin').textContent = formatearFechaPostgreSQL(mega.fecha_fin);
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

    // ONG Organizadora - Con avatar
    const ongEl = document.getElementById('ong_organizadora');
    const ongAvatarContainer = document.getElementById('ongAvatarContainer');
    if (ongEl && ongAvatarContainer) {
        console.log('Mega evento completo:', mega);
        console.log('Mega evento ONG data:', mega.ong_principal, mega.ongPrincipal);
        
        // Intentar obtener la información de la ONG de diferentes formas
        let ong = null;
    if (mega.ong_principal) {
            ong = mega.ong_principal;
        } else if (mega.ongPrincipal) {
            ong = mega.ongPrincipal;
        }
        
        if (ong) {
            console.log('ONG encontrada:', ong);
            console.log('Propiedades de ONG:', Object.keys(ong));
            const nombreOng = ong.nombre_ong || ong.nombre || '-';
            ongEl.textContent = nombreOng;
            
            // Obtener avatar de la ONG - intentar múltiples propiedades
            let fotoPerfil = ong.foto_perfil_url || ong.foto_perfil || ong.avatar || null;
            console.log('Foto perfil ONG (raw):', fotoPerfil);
            
            // Si la URL viene del backend, puede que ya esté normalizada, pero verificamos
            if (fotoPerfil && typeof fotoPerfil === 'string' && fotoPerfil.trim() !== '') {
                // Normalizar URL de la imagen
                const avatarUrl = buildImageUrl(fotoPerfil);
                console.log('Avatar URL normalizada:', avatarUrl);
                
                // Crear contenedor con posición relativa para el fallback
                ongAvatarContainer.innerHTML = '';
                ongAvatarContainer.style.position = 'relative';
                
                // Crear fallback primero (para que esté detrás)
                const fallback = document.createElement('div');
                fallback.className = 'rounded-circle d-flex align-items-center justify-content-center';
                fallback.style.cssText = 'width: 70px; height: 70px; font-weight: 700; font-size: 1.5rem; background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; border: 3px solid #17a2b8; box-shadow: 0 4px 12px rgba(23, 162, 184, 0.2); position: absolute; top: 0; left: 0; z-index: 1; display: none;';
                const inicial = nombreOng && nombreOng !== '-' ? nombreOng.charAt(0).toUpperCase() : '?';
                fallback.textContent = inicial;
                ongAvatarContainer.appendChild(fallback);
                
                // Crear imagen
                const img = document.createElement('img');
                img.src = avatarUrl;
                img.alt = nombreOng;
                img.className = 'rounded-circle';
                img.style.cssText = 'width: 70px; height: 70px; object-fit: cover; border: 3px solid #17a2b8; box-shadow: 0 4px 12px rgba(23, 162, 184, 0.2); display: block; position: relative; z-index: 2;';
                
                img.onerror = function() {
                    console.error('Error cargando avatar de ONG:', avatarUrl);
                    console.error('URL intentada:', this.src);
                    this.style.display = 'none';
                    fallback.style.display = 'flex';
                };
                
                img.onload = function() {
                    console.log('Avatar de ONG cargado correctamente:', avatarUrl);
                    fallback.style.display = 'none';
                };
                
                ongAvatarContainer.appendChild(img);
            } else {
                console.log('No hay foto_perfil disponible, mostrando inicial');
                const inicial = nombreOng && nombreOng !== '-' ? nombreOng.charAt(0).toUpperCase() : '?';
                ongAvatarContainer.innerHTML = `
                    <div class="rounded-circle d-flex align-items-center justify-content-center" 
                         style="width: 70px; height: 70px; font-weight: 700; font-size: 1.5rem; background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; border: 3px solid #17a2b8; box-shadow: 0 4px 12px rgba(23, 162, 184, 0.2);">
                        ${inicial}
                    </div>
                `;
            }
        } else {
            console.warn('No se encontró información de la ONG organizadora');
            ongEl.textContent = '-';
            ongAvatarContainer.innerHTML = `
                <div class="rounded-circle d-flex align-items-center justify-content-center" 
                     style="width: 70px; height: 70px; font-weight: 700; font-size: 1.5rem; background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; border: 3px solid #17a2b8; box-shadow: 0 4px 12px rgba(23, 162, 184, 0.2);">
                    ?
                </div>
            `;
        }
    } else {
        console.error('Elementos del DOM no encontrados:', { ongEl, ongAvatarContainer });
    }

    // Estados
    const estadoBadgesStyled = {
        'planificacion': '<span class="badge badge-secondary" style="background: #6c757d !important; color: white !important; font-size: 0.9rem; padding: 0.5em 1em; border-radius: 50px; font-weight: 600;">Planificación</span>',
        'activo': '<span class="badge badge-success" style="background: #00A36C !important; color: white !important; font-size: 0.9rem; padding: 0.5em 1em; border-radius: 50px; font-weight: 600;">Activo</span>',
        'en_curso': '<span class="badge badge-info" style="background: #17a2b8 !important; color: white !important; font-size: 0.9rem; padding: 0.5em 1em; border-radius: 50px; font-weight: 600;">En Curso</span>',
        'finalizado': '<span class="badge badge-primary" style="background: #0C2B44 !important; color: white !important; font-size: 0.9rem; padding: 0.5em 1em; border-radius: 50px; font-weight: 600;">Finalizado</span>',
        'cancelado': '<span class="badge badge-danger" style="background: #dc3545 !important; color: white !important; font-size: 0.9rem; padding: 0.5em 1em; border-radius: 50px; font-weight: 600;">Cancelado</span>'
    };
    const estadoBadgeEl = document.getElementById('estadoBadge');
    if (estadoBadgeEl) {
        estadoBadgeEl.innerHTML = estadoBadgesStyled[mega.estado] || '<span class="badge badge-secondary" style="background: #6c757d !important; color: white !important; font-size: 0.9rem; padding: 0.5em 1em; border-radius: 50px; font-weight: 600;">' + (mega.estado || 'N/A') + '</span>';
    }
    const estadoEl = document.getElementById('estado');
    if (estadoEl) {
        estadoEl.innerHTML = estadoBadgesStyled[mega.estado] || '<span class="badge badge-secondary" style="background: #6c757d !important; color: white !important; font-size: 0.9rem; padding: 0.5em 1em; border-radius: 50px; font-weight: 600;">' + (mega.estado || 'N/A') + '</span>';
    }

    const publicoBadgeEl = document.getElementById('publicoBadge');
    if (publicoBadgeEl) {
        publicoBadgeEl.innerHTML = mega.es_publico 
            ? '<span class="badge badge-info" style="background: #17a2b8 !important; color: white !important; font-size: 0.9rem; padding: 0.5em 1em; border-radius: 50px; font-weight: 600;">Público</span>' 
            : '<span class="badge badge-secondary" style="background: #6c757d !important; color: white !important; font-size: 0.9rem; padding: 0.5em 1em; border-radius: 50px; font-weight: 600;">Privado</span>';
    }
    const esPublicoEl = document.getElementById('es_publico');
    if (esPublicoEl) {
        esPublicoEl.innerHTML = mega.es_publico 
            ? '<span class="badge badge-info" style="background: #17a2b8 !important; color: white !important; font-size: 0.9rem; padding: 0.5em 1em; border-radius: 50px; font-weight: 600;">Público</span>' 
            : '<span class="badge badge-secondary" style="background: #6c757d !important; color: white !important; font-size: 0.9rem; padding: 0.5em 1em; border-radius: 50px; font-weight: 600;">Privado</span>';
    }

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

    // Mapa - Implementación mejorada igual a eventos/detalles
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

            // Validar coordenadas antes de mostrar el mapa
            if (lat && lng && !isNaN(parseFloat(lat)) && !isNaN(parseFloat(lng))) {
                // Asegurar que el contenedor esté visible y tenga tamaño
                mapContainer.style.display = 'block';
                mapContainer.style.height = '350px';
                mapContainer.style.width = '100%';
                
                // Limpiar contenido previo
                mapContainer.innerHTML = '';
                
                // Esperar a que el DOM esté completamente listo
                setTimeout(() => {
                    try {
                        // Verificar que Leaflet esté disponible
                        if (typeof L === 'undefined') {
                            console.error('Leaflet no está cargado');
                            mapContainer.innerHTML = '<div class="alert alert-warning p-3 m-0">Error: La librería de mapas no está cargada. Por favor, recarga la página.</div>';
                            return;
                        }
                        
                        // Limpiar cualquier mapa anterior
                        if (window.megaEventoMapa) {
                            try {
                                window.megaEventoMapa.remove();
                            } catch (e) {
                                console.warn('Error al remover mapa anterior:', e);
                            }
                        }
                        
                        const latNum = parseFloat(lat);
                        const lngNum = parseFloat(lng);
                        
                        // Inicializar el mapa con opciones mejoradas
                        const map = L.map('map', {
            zoomControl: true,
                            scrollWheelZoom: true,
                            doubleClickZoom: true,
                            boxZoom: true,
                            keyboard: true,
                            dragging: true,
                            touchZoom: true
                        }).setView([latNum, lngNum], 13);
        
                        // Guardar referencia global
                        window.megaEventoMapa = map;
                        
                        // Agregar capa de tiles con opciones mejoradas
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '© OpenStreetMap contributors',
                            maxZoom: 19,
                            minZoom: 3
        }).addTo(map);
        
                        // Esperar a que los tiles se carguen
                        map.whenReady(() => {
                            // Agregar marcador con popup mejorado
                            const marker = L.marker([latNum, lngNum]).addTo(map);
                            const popupContent = `
                                <div style="padding: 0.5rem; min-width: 200px;">
                                    <strong style="color: #0C2B44; font-size: 1rem; display: block; margin-bottom: 0.25rem;">${direccionCompleta || 'Ubicación del mega evento'}</strong>
                                    ${mega.ciudad ? `<small style="color: #6c757d; display: block;">${mega.ciudad}</small>` : ''}
            </div>
        `;
                            marker.bindPopup(popupContent).openPopup();
                            
                            // Ajustar el mapa múltiples veces para asegurar renderizado completo
                            setTimeout(() => {
                                map.invalidateSize();
                            }, 100);
                            
                            setTimeout(() => {
                                map.invalidateSize();
                            }, 300);
                            
                            setTimeout(() => {
                                map.invalidateSize();
                            }, 500);
                            
                            // Forzar actualización después de un segundo
                            setTimeout(() => {
                                map.invalidateSize();
                                map.setView([latNum, lngNum], map.getZoom());
                            }, 1000);
                        });
                        
                    } catch (error) {
                        console.error('Error inicializando mapa:', error);
                        mapContainer.innerHTML = `<div class="alert alert-danger p-3 m-0">Error al cargar el mapa: ${error.message}</div>`;
                    }
                }, 500);
            } else {
                mapContainer.style.display = 'none';
            }
        };

        // Inicializar el mapa cuando el DOM esté listo
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', inicializarMapa);
        } else {
            inicializarMapa();
        }
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


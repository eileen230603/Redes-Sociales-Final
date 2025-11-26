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
        <!-- Header con imagen -->
        <div class="card border-0 mb-4" style="border-radius: 12px; border: 1px solid #F5F5F5; box-shadow: 0 2px 8px rgba(12, 43, 68, 0.08); overflow: hidden;">
            <div id="headerImage" class="card-img-top" style="height: 300px; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); display: flex; align-items: center; justify-content: center;">
                <i class="far fa-calendar-check fa-4x text-white" style="opacity: 0.9;"></i>
            </div>
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <h2 class="mb-3" id="titulo" style="font-size: 2rem; font-weight: 700; color: #0C2B44; line-height: 1.3;">-</h2>
                        <p id="descripcionHeader" class="mb-3" style="font-size: 1.1rem; color: #333333; line-height: 1.6; font-weight: 400;">-</p>
                        <div class="mb-3">
                            <span id="estadoBadge" class="badge mr-2 mb-2" style="font-size: 0.85rem; padding: 0.5em 0.8em; border-radius: 20px;">-</span>
                            <span id="publicoBadge" class="badge mr-2 mb-2" style="font-size: 0.85rem; padding: 0.5em 0.8em; border-radius: 20px;">-</span>
                            <span id="categoriaBadge" class="badge mb-2" style="font-size: 0.85rem; padding: 0.5em 0.8em; border-radius: 20px;">-</span>
                        </div>
                    </div>
                    <div class="ml-3">
                        <a href="{{ route('ong.mega-eventos.index') }}" class="btn btn-sm mr-2" style="background: #F5F5F5; color: #0C2B44; border: none; border-radius: 8px; padding: 0.5rem 1rem;">
                            <i class="far fa-arrow-left mr-1"></i> Volver
                        </a>
                        <a href="#" id="editLink" class="btn btn-sm" style="background: #00A36C; color: white; border: none; border-radius: 8px; padding: 0.5rem 1rem;">
                            <i class="far fa-edit mr-1"></i> Editar
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Información Principal -->
            <div class="col-md-8">
                <div class="card border-0 mb-4" style="border-radius: 12px; border: 1px solid #F5F5F5; box-shadow: 0 2px 8px rgba(12, 43, 68, 0.08);">
                    <div class="card-body p-4">
                        <h5 class="mb-4" style="font-size: 1.1rem; font-weight: 700; color: #0C2B44;">
                            <i class="far fa-info-circle mr-2" style="color: #00A36C;"></i> Información del Mega Evento
                        </h5>
                        <div class="mb-4">
                            <small class="d-block mb-2" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; color: #0C2B44;">Descripción</small>
                            <p id="descripcion" class="mb-0" style="line-height: 1.8; color: #333333; font-size: 1rem;">-</p>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="d-flex align-items-start">
                                    <div class="mr-3" style="width: 50px; height: 50px; background: rgba(12, 43, 68, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                        <i class="far fa-calendar-alt" style="color: #0C2B44; font-size: 1.25rem;"></i>
                                    </div>
                                    <div>
                                        <small class="d-block mb-1" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; color: #0C2B44;">Fecha de Inicio</small>
                                        <p id="fecha_inicio" class="mb-0" style="font-size: 1rem; font-weight: 500; color: #333333;">-</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="d-flex align-items-start">
                                    <div class="mr-3" style="width: 50px; height: 50px; background: rgba(0, 163, 108, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                        <i class="far fa-calendar-check" style="color: #00A36C; font-size: 1.25rem;"></i>
                                    </div>
                                    <div>
                                        <small class="d-block mb-1" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; color: #0C2B44;">Fecha de Fin</small>
                                        <p id="fecha_fin" class="mb-0" style="font-size: 1rem; font-weight: 500; color: #333333;">-</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-4">
                                <div class="card border-0" style="background: #F5F5F5; border-radius: 12px; padding: 1.5rem; border: 1px solid rgba(12, 43, 68, 0.1);">
                                    <div class="d-flex align-items-start">
                                        <div class="mr-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                            <i class="far fa-map" style="color: #00A36C; font-size: 1.6rem;"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <small class="d-block mb-2" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; color: #0C2B44;">Ubicación del Evento</small>
                                            <div id="ubicacionContainer">
                                                <p id="ubicacion" class="mb-0" style="font-size: 1.05rem; font-weight: 500; color: #333333; line-height: 1.7;">-</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="d-flex align-items-start">
                                    <div class="mr-3" style="width: 50px; height: 50px; background: rgba(0, 163, 108, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                        <i class="far fa-user" style="color: #00A36C; font-size: 1.4rem;"></i>
                                    </div>
                                    <div>
                                        <small class="d-block mb-1" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; color: #0C2B44;">
                                            <i class="far fa-user mr-1" style="color: #00A36C;"></i> Capacidad Máxima
                                        </small>
                                        <p id="capacidad_maxima" class="mb-0" style="font-size: 1rem; font-weight: 500; color: #333333;">-</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="mb-3">
                                    <small class="d-block mb-2" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; color: #0C2B44;">
                                        <i class="far fa-map mr-1" style="color: #00A36C;"></i> Mapa de Ubicación
                                    </small>
                                </div>
                                <div id="map" style="height: 350px; border-radius: 12px; border: 1px solid #F5F5F5; overflow: hidden; box-shadow: 0 2px 8px rgba(12, 43, 68, 0.08);"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Imágenes -->
                <div class="card border-0 mb-4" style="border-radius: 12px; border: 1px solid #F5F5F5; box-shadow: 0 2px 8px rgba(12, 43, 68, 0.08);">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0" style="font-size: 1.1rem; font-weight: 700; color: #0C2B44;">
                                <i class="far fa-images mr-2" style="color: #00A36C;"></i> Imágenes Promocionales
                            </h5>
                            <span id="imagenesCount" class="badge" style="font-size: 0.85rem; background: #0C2B44; color: white; padding: 0.5em 0.8em; border-radius: 20px;">0</span>
                        </div>
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
            </div>

            <!-- Información Adicional -->
            <div class="col-md-4">
                <div class="card border-0 mb-4" style="border-radius: 12px; border: 1px solid #F5F5F5; box-shadow: 0 2px 8px rgba(12, 43, 68, 0.08);">
                    <div class="card-body p-4">
                        <h6 class="mb-3" style="font-size: 0.9rem; font-weight: 700; color: #0C2B44; text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="far fa-building mr-2" style="color: #00A36C;"></i> ONG Organizadora
                        </h6>
                        <p id="ong_organizadora" class="mb-0" style="font-size: 1rem; font-weight: 500; color: #333333;">-</p>
                    </div>
                </div>

                <div class="card border-0 mb-4" style="border-radius: 12px; border: 1px solid #F5F5F5; box-shadow: 0 2px 8px rgba(12, 43, 68, 0.08);">
                    <div class="card-body p-4">
                        <h6 class="mb-3" style="font-size: 0.9rem; font-weight: 700; color: #0C2B44; text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="far fa-calendar mr-2" style="color: #00A36C;"></i> Fechas del Sistema
                        </h6>
                        <div class="mb-3 pb-3 border-bottom" style="border-color: #F5F5F5 !important;">
                            <small class="d-block mb-1" style="font-size: 0.75rem; font-weight: 600; color: #0C2B44;">Fecha de Creación</small>
                            <p id="fecha_creacion" class="mb-0" style="font-size: 0.9rem; color: #333333;">-</p>
                        </div>
                        <div>
                            <small class="d-block mb-1" style="font-size: 0.75rem; font-weight: 600; color: #0C2B44;">Última Actualización</small>
                            <p id="fecha_actualizacion" class="mb-0" style="font-size: 0.9rem; color: #333333;">-</p>
                        </div>
                    </div>
                </div>

                <div class="card border-0" style="border-radius: 12px; border: 1px solid #F5F5F5; box-shadow: 0 2px 8px rgba(12, 43, 68, 0.08);">
                    <div class="card-body p-4">
                        <h6 class="mb-3" style="font-size: 0.9rem; font-weight: 700; color: #0C2B44; text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="far fa-cog mr-2" style="color: #00A36C;"></i> Estado
                        </h6>
                        <div class="mb-3 pb-3 border-bottom" style="border-color: #F5F5F5 !important;">
                            <small class="d-block mb-1" style="font-size: 0.75rem; font-weight: 600; color: #0C2B44;">
                                <i class="far fa-flag mr-1" style="color: #00A36C;"></i> Estado del Evento
                            </small>
                            <div id="estado" class="mb-0">-</div>
                        </div>
                        <div class="mb-3 pb-3 border-bottom" style="border-color: #F5F5F5 !important;">
                            <small class="d-block mb-1" style="font-size: 0.75rem; font-weight: 600; color: #0C2B44;">
                                <i class="far fa-eye mr-1" style="color: #00A36C;"></i> Visibilidad
                            </small>
                            <div id="es_publico" class="mb-0">-</div>
                        </div>
                        <div>
                            <small class="d-block mb-1" style="font-size: 0.75rem; font-weight: 600; color: #0C2B44;">
                                <i class="far fa-toggle-on mr-1" style="color: #00A36C;"></i> Activo
                            </small>
                            <div id="activo" class="mb-0">-</div>
                        </div>
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

    // Obtener ID de la URL
    const pathParts = window.location.pathname.split('/');
    megaEventoId = pathParts[pathParts.length - 2]; // /ong/mega-eventos/{id}/detalle

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
                <div class="alert alert-danger" style="background: #f8d7da; border: 1px solid #dc3545; color: #721c24; border-radius: 8px; padding: 1rem;">
                    <i class="far fa-exclamation-triangle mr-2"></i>
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
            <div class="alert alert-danger" style="background: #f8d7da; border: 1px solid #dc3545; color: #721c24; border-radius: 8px; padding: 1rem;">
                <i class="far fa-exclamation-triangle mr-2"></i>
                Error de conexión al cargar el mega evento.
            </div>
        `;
    }
}

function displayMegaEvento(mega) {
    // Título
    document.getElementById('titulo').textContent = mega.titulo || '-';

    // Descripción en header y en sección
    const descripcion = mega.descripcion || 'Sin descripción disponible.';
    document.getElementById('descripcionHeader').textContent = descripcion;
    document.getElementById('descripcion').textContent = descripcion;

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
    } else {
        document.getElementById('fecha_inicio').textContent = '-';
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
    } else {
        document.getElementById('fecha_fin').textContent = '-';
    }

    // Ubicación - Mostrar de forma más visible y organizada con dirección, ciudad y departamento
    const ubicacionContainer = document.getElementById('ubicacionContainer');
    const ubicacion = mega.ubicacion || 'No especificada';
    
    function parsearUbicacion(ubicacionStr) {
        if (!ubicacionStr || ubicacionStr === 'No especificada' || ubicacionStr.trim() === '') {
            return null;
        }
        
        // Intentar parsear formato: "Dirección, Ciudad, Departamento" o variaciones
        const partes = ubicacionStr.split(',').map(p => p.trim()).filter(p => p);
        
        if (partes.length >= 3) {
            // Formato: Dirección, Ciudad, Departamento
            // Las últimas dos partes son ciudad y departamento, el resto es dirección
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
                                   segundaParte.includes('dep.') ||
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
    
    const ubicacionParsed = parsearUbicacion(ubicacion);
    
    if (ubicacionParsed) {
        let html = '';
        
        if (ubicacionParsed.direccion) {
            html += `
                <div class="mb-2">
                    <strong style="color: #0C2B44; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">
                        <i class="far fa-road mr-1" style="color: #00A36C;"></i> Dirección:
                    </strong>
                    <p class="mb-0 mt-1" style="font-size: 1.05rem; color: #333333; font-weight: 500;">${ubicacionParsed.direccion}</p>
                </div>
            `;
        }
        
        if (ubicacionParsed.ciudad) {
            html += `
                <div class="mb-2">
                    <strong style="color: #0C2B44; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">
                        <i class="far fa-city mr-1" style="color: #00A36C;"></i> Ciudad:
                    </strong>
                    <p class="mb-0 mt-1" style="font-size: 1.05rem; color: #333333; font-weight: 500;">${ubicacionParsed.ciudad}</p>
                </div>
            `;
        }
        
        if (ubicacionParsed.departamento) {
            html += `
                <div>
                    <strong style="color: #0C2B44; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">
                        <i class="far fa-map-marked-alt mr-1" style="color: #00A36C;"></i> Departamento:
                    </strong>
                    <p class="mb-0 mt-1" style="font-size: 1.05rem; color: #333333; font-weight: 500;">${ubicacionParsed.departamento}</p>
                </div>
            `;
        }
        
        ubicacionContainer.innerHTML = html || `
            <p class="mb-0" style="font-size: 1.05rem; font-weight: 500; color: #333333; line-height: 1.7;">
                <i class="far fa-map-marker-alt mr-2" style="color: #00A36C;"></i>${ubicacion}
            </p>
        `;
    } else {
        ubicacionContainer.innerHTML = `
            <p class="mb-0" style="font-size: 1rem; color: #333333; font-weight: 500;">
                <i class="far fa-exclamation-circle mr-2" style="color: #00A36C;"></i>Ubicación no especificada
            </p>
        `;
    }

    // Capacidad
    document.getElementById('capacidad_maxima').textContent = mega.capacidad_maxima 
        ? `${mega.capacidad_maxima} personas` 
        : 'Sin límite';

    // ONG Organizadora
    if (mega.ong_principal) {
        document.getElementById('ong_organizadora').textContent = mega.ong_principal.nombre_ong || '-';
    } else {
        document.getElementById('ong_organizadora').textContent = '-';
    }

    // Fechas del sistema
    if (mega.fecha_creacion) {
        const fechaCreacion = new Date(mega.fecha_creacion);
        document.getElementById('fecha_creacion').textContent = fechaCreacion.toLocaleString('es-ES');
    } else {
        document.getElementById('fecha_creacion').textContent = '-';
    }

    if (mega.fecha_actualizacion) {
        const fechaActualizacion = new Date(mega.fecha_actualizacion);
        document.getElementById('fecha_actualizacion').textContent = fechaActualizacion.toLocaleString('es-ES');
    } else {
        document.getElementById('fecha_actualizacion').textContent = '-';
    }

    // Estados con nueva paleta de colores
    const estadoBadges = {
        'planificacion': '<span class="badge badge-secondary" style="background: #333333 !important; color: white !important;">Planificación</span>',
        'activo': '<span class="badge badge-success" style="background: #00A36C !important; color: white !important;">Activo</span>',
        'en_curso': '<span class="badge badge-info" style="background: #0C2B44 !important; color: white !important;">En Curso</span>',
        'finalizado': '<span class="badge badge-primary" style="background: #0C2B44 !important; color: white !important;">Finalizado</span>',
        'cancelado': '<span class="badge badge-danger" style="background: #dc3545 !important; color: white !important;">Cancelado</span>'
    };
    document.getElementById('estadoBadge').innerHTML = estadoBadges[mega.estado] || '<span class="badge badge-secondary" style="background: #333333 !important; color: white !important;">' + (mega.estado || 'N/A') + '</span>';
    document.getElementById('estado').innerHTML = estadoBadges[mega.estado] || '<span class="badge badge-secondary" style="background: #333333 !important; color: white !important;">' + (mega.estado || 'N/A') + '</span>';

    document.getElementById('publicoBadge').innerHTML = mega.es_publico 
        ? '<span class="badge badge-info" style="background: #0C2B44 !important; color: white !important;">Público</span>' 
        : '<span class="badge badge-secondary" style="background: #333333 !important; color: white !important;">Privado</span>';
    document.getElementById('es_publico').innerHTML = mega.es_publico 
        ? '<span class="badge badge-info" style="background: #0C2B44 !important; color: white !important;">Público</span>' 
        : '<span class="badge badge-secondary" style="background: #333333 !important; color: white !important;">Privado</span>';

    document.getElementById('categoriaBadge').innerHTML = mega.categoria 
        ? '<span class="badge badge-success" style="background: #00A36C !important; color: white !important;">' + mega.categoria.charAt(0).toUpperCase() + mega.categoria.slice(1) + '</span>' 
        : '';

    document.getElementById('activo').innerHTML = mega.activo 
        ? '<span class="badge badge-success" style="background: #00A36C !important; color: white !important;">Activo</span>' 
        : '<span class="badge badge-danger" style="background: #dc3545 !important; color: white !important;">Inactivo</span>';

    // Imágenes (el modelo ya devuelve URLs completas)
    const imagenes = Array.isArray(mega.imagenes) ? mega.imagenes.filter(img => img && img.trim() !== '') : [];
    const imagenesContainer = document.getElementById('imagenesContainer');
    const imagenesCount = document.getElementById('imagenesCount');
    
    // Actualizar contador
    imagenesCount.textContent = imagenes.length;
    
    if (imagenes.length === 0) {
        imagenesContainer.innerHTML = `
            <div class="col-12">
                <div class="text-center py-5" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); border-radius: 12px; padding: 3rem 2rem;">
                    <div style="background: rgba(255, 255, 255, 0.15); width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; backdrop-filter: blur(10px);">
                        <i class="far fa-images fa-3x text-white"></i>
                    </div>
                    <h5 style="color: white; font-weight: 600; margin-bottom: 0.5rem;">No hay imágenes disponibles</h5>
                    <p style="color: rgba(255, 255, 255, 0.9); margin: 0;">Las imágenes aparecerán aquí cuando se agreguen al mega evento.</p>
                </div>
            </div>
        `;
    } else {
        imagenesContainer.innerHTML = '';
        imagenes.forEach((imgUrl, index) => {
            if (!imgUrl || imgUrl.trim() === '') return;
            
            // El modelo ya devuelve URLs completas, usar directamente
            const fullUrl = imgUrl;

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
            icon.className = 'far fa-search-plus fa-lg text-white';
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

    // Imagen principal en header con mejor manejo
    if (imagenes.length > 0 && imagenes[0]) {
        const headerImage = document.getElementById('headerImage');
        const imgUrl = imagenes[0];
        // El modelo ya devuelve URLs completas
        const fullUrl = imgUrl;
        
        if (fullUrl) {
            const testImg = new Image();
            testImg.onload = function() {
                headerImage.style.backgroundImage = `url(${fullUrl})`;
                headerImage.style.backgroundSize = 'cover';
                headerImage.style.backgroundPosition = 'center';
                headerImage.style.backgroundRepeat = 'no-repeat';
                headerImage.innerHTML = '';
            };
            testImg.onerror = function() {
                // Si falla, usar placeholder con nueva paleta
                headerImage.style.background = 'linear-gradient(135deg, #0C2B44 0%, #00A36C 100%)';
                headerImage.innerHTML = '<i class="far fa-calendar-check fa-4x text-white" style="opacity: 0.9;"></i>';
            };
            testImg.src = fullUrl;
        }
    }

    // Link de editar
    document.getElementById('editLink').href = `/ong/mega-eventos/${megaEventoId}/editar`;

    // Mostrar mapa si hay coordenadas
    const mapContainer = document.getElementById('map');
    if (mega.lat && mega.lng) {
        const lat = parseFloat(mega.lat);
        const lng = parseFloat(mega.lng);
        
        // Inicializar mapa con estilo minimalista
        map = L.map("map", {
            zoomControl: true,
            scrollWheelZoom: true
        }).setView([lat, lng], 13);
        
        // Usar tiles más limpios
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);
        
        // Marcador con estilo minimalista
        const marker = L.marker([lat, lng], {
            icon: L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
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
        
        mapContainer.style.borderRadius = '8px';
        mapContainer.style.overflow = 'hidden';
    } else {
        mapContainer.innerHTML = `
            <div class="d-flex align-items-center justify-content-center h-100" style="background: #F5F5F5; border-radius: 12px;">
                <div class="text-center">
                    <i class="far fa-map-marker-alt fa-3x mb-3" style="color: #00A36C;"></i>
                    <p class="mb-0" style="font-size: 1rem; color: #333333; font-weight: 500;">No hay coordenadas disponibles</p>
                </div>
            </div>
        `;
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
</style>
@endpush


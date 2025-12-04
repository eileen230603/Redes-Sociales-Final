@extends('layouts.adminlte-externo')

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
        <div id="eventBanner" class="position-relative" style="height: 400px; background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); overflow: hidden;">
            <div id="bannerImage" class="w-100 h-100" style="background-size: cover; background-position: center; opacity: 0.3;"></div>
            <div class="position-absolute" style="top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(to bottom, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0.6) 100%);"></div>
            <div class="position-absolute" style="bottom: 0; left: 0; right: 0; padding: 2rem; color: white;">
                <div class="container">
                    <h1 id="titulo" class="mb-2" style="font-size: 2.5rem; font-weight: 700; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);"></h1>
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <span id="categoriaBadge" class="badge badge-warning" style="font-size: 0.9rem; padding: 0.5em 1em;"></span>
                        <span id="estadoBadge" class="badge" style="font-size: 0.9rem; padding: 0.5em 1em;"></span>
                        <span id="publicoBadge" class="badge badge-info" style="font-size: 0.9rem; padding: 0.5em 1em;">Público</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mt-4">
            <!-- Botones de Acción -->
            <div class="d-flex justify-content-end mb-4 gap-2 flex-wrap">
                <a href="#" id="btnVolver" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left mr-2"></i> Volver
                </a>
                <button class="btn btn-outline-danger" id="btnReaccionar" style="border-radius: 50px;">
                    <i class="far fa-heart mr-2" id="iconoCorazon"></i>
                    <span id="textoReaccion">Me gusta</span>
                    <span class="badge badge-light ml-2" id="contadorReacciones">0</span>
                </button>
                <button class="btn btn-outline-primary" id="btnCompartir" style="border-radius: 50px;">
                    <i class="far fa-share-square mr-2"></i> Compartir
                    <span class="badge badge-light ml-2" id="contadorCompartidos">0</span>
                </button>
                <button class="btn btn-primary" id="btnParticipar">
                    <i class="fas fa-user-plus mr-2"></i> <span id="btnParticiparTexto">Participar en el mega evento</span>
                </button>
                <button class="btn btn-success d-none" id="btnYaParticipando" disabled>
                    <i class="fas fa-check-circle mr-2"></i> Ya estás participando
                </button>
            </div>

            <div class="row">
                <!-- Columna Principal -->
                <div class="col-lg-8">
                    <!-- Descripción -->
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                        <div class="card-body p-4">
                            <h4 class="mb-3" style="color: #2c3e50; font-weight: 600;">
                                <i class="fas fa-align-left mr-2 text-warning"></i> Descripción
                            </h4>
                            <p id="descripcion" class="mb-0" style="color: #6c757d; line-height: 1.8; font-size: 1rem;"></p>
                        </div>
                    </div>

                    <!-- Información del Mega Evento -->
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                        <div class="card-body p-4">
                            <h4 class="mb-4" style="color: #2c3e50; font-weight: 600;">
                                <i class="fas fa-info-circle mr-2 text-warning"></i> Información del Mega Evento
                            </h4>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-calendar-alt text-warning mr-3 mt-1" style="font-size: 1.2rem;"></i>
                                        <div>
                                            <h6 class="mb-1" style="color: #495057; font-weight: 600;">Fecha de Inicio</h6>
                                            <p id="fecha_inicio" class="mb-0 text-muted"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-calendar-check text-warning mr-3 mt-1" style="font-size: 1.2rem;"></i>
                                        <div>
                                            <h6 class="mb-1" style="color: #495057; font-weight: 600;">Fecha de Fin</h6>
                                            <p id="fecha_fin" class="mb-0 text-muted"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-users text-warning mr-3 mt-1" style="font-size: 1.2rem;"></i>
                                        <div>
                                            <h6 class="mb-1" style="color: #495057; font-weight: 600;">Capacidad Máxima</h6>
                                            <p id="capacidad_maxima" class="mb-0 text-muted"></p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Ubicación destacada -->
                                <div class="col-md-12 mb-4">
                                    <div class="card border-0" style="background: #fff3cd; border-radius: 12px; padding: 1.5rem; border-left: 4px solid #ffc107;">
                                        <div class="d-flex align-items-start">
                                            <div class="mr-3" style="width: 50px; height: 50px; background: #fff; border-radius: 10px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                                <i class="fas fa-map-marker-alt" style="color: #ef4444; font-size: 1.5rem;"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h5 class="mb-3" style="color: #856404; font-weight: 600;">
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
                                        <i class="fas fa-map mr-2 text-warning"></i> Mapa de Ubicación
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
                                <i class="fas fa-images mr-2 text-warning"></i> Imágenes Promocionales
                                <span id="imagenesCount" class="badge badge-warning ml-2">0</span>
                            </h4>
                            <div id="imagenesContainer" class="row">
                                <div class="col-12 text-center py-3">
                                    <div class="spinner-border text-warning" role="status"></div>
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
                                <i class="fas fa-building mr-2 text-warning"></i> ONG Organizadora
                            </h5>
                            <p id="ong_organizadora" class="mb-0" style="color: #495057; font-size: 1rem;"></p>
                        </div>
                    </div>

                    <!-- Información Adicional -->
                    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                        <div class="card-body p-4">
                            <h5 class="mb-3" style="color: #2c3e50; font-weight: 600;">
                                <i class="fas fa-info-circle mr-2 text-warning"></i> Información Adicional
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

<!-- Modal de Compartir -->
<div id="modalCompartir" class="modal fade" tabindex="-1" role="dialog" style="display: none;">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 400px;">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 8px 32px rgba(0,0,0,0.2);">
            <div class="modal-header" style="border-bottom: 1px solid #F5F5F5; padding: 1.5rem;">
                <h5 class="modal-title" style="color: #2c3e50; font-weight: 700; font-size: 1.25rem;">Compartir</h5>
                <button type="button" class="close" onclick="cerrarModalCompartir()" style="border: none; background: none; font-size: 1.5rem; color: #6c757d; cursor: pointer;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 2rem;">
                <div class="row text-center">
                    <!-- Copiar enlace -->
                    <div class="col-6 mb-4">
                        <button onclick="copiarEnlaceMegaEvento()" class="btn btn-link p-0" style="text-decoration: none; border: none; background: none; width: 100%;">
                            <div style="width: 80px; height: 80px; background: #F5F5F5; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(0,0,0,0.1);" onmouseover="this.style.background='#E9ECEF'; this.style.transform='scale(1.1)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)'" onmouseout="this.style.background='#F5F5F5'; this.style.transform='scale(1)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.1)'">
                                <i class="fas fa-link" style="font-size: 2rem; color: #2c3e50;"></i>
                            </div>
                            <span style="color: #333; font-size: 0.9rem; font-weight: 600;">Copiar enlace</span>
                        </button>
                    </div>
                    <!-- QR Code -->
                    <div class="col-6 mb-4">
                        <button onclick="mostrarQRMegaEvento()" class="btn btn-link p-0" style="text-decoration: none; border: none; background: none; width: 100%;">
                            <div style="width: 80px; height: 80px; background: #667eea; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(102,126,234,0.3);" onmouseover="this.style.background='#764ba2'; this.style.transform='scale(1.1)'; this.style.boxShadow='0 4px 12px rgba(118,75,162,0.4)'" onmouseout="this.style.background='#667eea'; this.style.transform='scale(1)'; this.style.boxShadow='0 2px 8px rgba(102,126,234,0.3)'">
                                <i class="fas fa-qrcode" style="font-size: 2rem; color: white;"></i>
                            </div>
                            <span style="color: #333; font-size: 0.9rem; font-weight: 600;">Código QR</span>
                        </button>
                    </div>
                </div>
                <!-- Contenedor para el QR -->
                <div id="qrContainer" style="display: none; margin-top: 1.5rem;">
                    <div class="text-center">
                        <div id="qrcode" style="display: inline-block; padding: 1rem; background: white; border-radius: 12px; margin-bottom: 1rem;"></div>
                        <p style="color: #333; font-size: 0.9rem; margin: 0;">Escanea este código para acceder al mega evento</p>
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
let estaParticipando = false;

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
    
    // Detectar tipo de usuario y actualizar texto del botón y enlace de volver
    const tipoUsuario = localStorage.getItem('tipo_usuario') || '';
    const currentPath = window.location.pathname;
    
    // Configurar botón de volver según la ruta
    const btnVolver = document.getElementById('btnVolver');
    if (btnVolver) {
        if (currentPath.includes('/voluntario/mega-eventos')) {
            btnVolver.href = '/voluntario/mega-eventos';
        } else if (currentPath.includes('/empresa/mega-eventos')) {
            btnVolver.href = '/empresa/mega-eventos';
        } else {
            btnVolver.href = '/externo/mega-eventos';
        }
    }
    
    // Configurar texto del botón según tipo de usuario
    const btnParticiparTexto = document.getElementById('btnParticiparTexto');
    if (tipoUsuario === 'Voluntario' && btnParticiparTexto) {
        btnParticiparTexto.textContent = 'Participar ahora';
    } else if (btnParticiparTexto) {
        btnParticiparTexto.textContent = 'Participar en el mega evento';
    }
    
    await Promise.all([loadMegaEvento(), verificarParticipacion(), cargarContadorCompartidos()]);
    
    // Configurar botón de compartir
    const btnCompartir = document.getElementById('btnCompartir');
    if (btnCompartir) {
        btnCompartir.addEventListener('click', mostrarModalCompartir);
    }
});

async function verificarParticipacion() {
    const token = localStorage.getItem('token');
    try {
        const res = await fetch(`${API_BASE_URL}/api/mega-eventos/${megaEventoId}/verificar-participacion`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });
        const data = await res.json();
        estaParticipando = data.participando || false;
        
        if (estaParticipando) {
            document.getElementById('btnParticipar').classList.add('d-none');
            document.getElementById('btnYaParticipando').classList.remove('d-none');
        }
    } catch (error) {
        console.error('Error verificando participación:', error);
    }
}

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
        
        // Cargar estado de reacción y contador
        await verificarReaccionMegaEvento();
        await cargarContadorReaccionesMegaEvento();
        
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

function displayMegaEvento(mega) {
    // Guardar información del mega evento para compartir
    setMegaEventoParaCompartir(mega);
    
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
            // Intentar detectar si la segunda parte es ciudad o departamento
            const segundaParte = partes[1].toLowerCase();
            const esDepartamento = segundaParte.includes('departamento') || 
                                   segundaParte.includes('depto') ||
                                   segundaParte.includes('dep.') ||
                                   segundaParte.length < 15; // Departamentos suelen ser más cortos
            
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
            // Solo una parte, asumir que es dirección completa
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
                <div class="mb-3">
                    <strong style="color: #856404; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        <i class="fas fa-road mr-2"></i> Dirección:
                    </strong>
                    <p class="mb-0 mt-2" style="font-size: 1.1rem; color: #2c3e50; font-weight: 500;">${ubicacionParsed.direccion}</p>
                </div>
            `;
        }
        
        if (ubicacionParsed.ciudad) {
            html += `
                <div class="mb-3">
                    <strong style="color: #856404; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        <i class="fas fa-city mr-2"></i> Ciudad:
                    </strong>
                    <p class="mb-0 mt-2" style="font-size: 1.1rem; color: #2c3e50; font-weight: 500;">${ubicacionParsed.ciudad}</p>
                </div>
            `;
        }
        
        if (ubicacionParsed.departamento) {
            html += `
                <div>
                    <strong style="color: #856404; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        <i class="fas fa-map-marked-alt mr-2"></i> Departamento:
                    </strong>
                    <p class="mb-0 mt-2" style="font-size: 1.1rem; color: #2c3e50; font-weight: 500;">${ubicacionParsed.departamento}</p>
                </div>
            `;
        }
        
        ubicacionContainer.innerHTML = html || `
            <p class="mb-0" style="font-size: 1.1rem; font-weight: 500; color: #2c3e50; line-height: 1.8;">
                <i class="fas fa-map-marker-alt mr-2" style="color: #ef4444;"></i>${ubicacion}
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
        ? '<span class="badge badge-warning">' + mega.categoria.charAt(0).toUpperCase() + mega.categoria.slice(1) + '</span>' 
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
            headerImage.style.background = 'linear-gradient(135deg, #ffc107 0%, #ff9800 100%)';
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

// Participar en mega evento
document.getElementById('btnParticipar').addEventListener('click', async function() {
    const token = localStorage.getItem('token');
    if (!token) {
        Swal.fire({
            icon: 'warning',
            title: 'Sesión Expirada',
            text: 'Debes iniciar sesión para participar',
            confirmButtonText: 'Ir al Login'
        }).then(() => {
            window.location.href = '/login';
        });
        return;
    }

    const result = await Swal.fire({
        title: '¿Participar en este mega evento?',
        text: 'Tu participación será registrada y aprobada automáticamente',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, participar',
        cancelButtonText: 'Cancelar'
    });
    
    if (!result.isConfirmed) return;

    try {
        const res = await fetch(`${API_BASE_URL}/api/mega-eventos/${megaEventoId}/participar`, {
            method: 'POST',
            headers: {
                "Authorization": `Bearer ${token}`,
                "Accept": "application/json",
                "Content-Type": "application/json"
            }
        });

        const data = await res.json();

        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Participación exitosa!',
                text: 'Tu participación ha sido registrada y aprobada automáticamente',
                timer: 2000,
                showConfirmButton: false
            });
            estaParticipando = true;
            document.getElementById('btnParticipar').classList.add('d-none');
            document.getElementById('btnYaParticipando').classList.remove('d-none');
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.error || 'Error al participar en el mega evento'
            });
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error de conexión',
            text: 'No se pudo registrar la participación'
        });
    }
});

// Funciones para reacciones de mega eventos
async function verificarReaccionMegaEvento() {
    const token = localStorage.getItem('token');
    const btnReaccionar = document.getElementById('btnReaccionar');
    const iconoCorazon = document.getElementById('iconoCorazon');
    const textoReaccion = document.getElementById('textoReaccion');
    const contadorReacciones = document.getElementById('contadorReacciones');

    if (!btnReaccionar) return;

    try {
        if (token) {
            // Usuario registrado - verificar reacción
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
                    iconoCorazon.className = 'far fa-heart mr-2';
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
        } else {
            // Usuario no registrado - solo cargar contador
            await cargarContadorReaccionesMegaEvento();
            
            // Agregar evento click para mostrar modal de reacción pública
            btnReaccionar.onclick = async () => {
                await mostrarModalReaccionPublica();
            };
        }
    } catch (error) {
        console.warn('Error verificando reacción:', error);
        // Si hay error, intentar cargar solo el contador
        await cargarContadorReaccionesMegaEvento();
    }
}

async function cargarContadorReaccionesMegaEvento() {
    try {
        const res = await fetch(`${API_BASE_URL}/api/reacciones/mega-evento/${megaEventoId}/total`, {
            headers: {
                'Accept': 'application/json'
            }
        });
        const data = await res.json();
        
        if (data.success) {
            const contador = document.getElementById('contadorReacciones');
            if (contador) {
                contador.textContent = data.total_reacciones || 0;
            }
        }
    } catch (error) {
        console.warn('Error cargando contador de reacciones:', error);
    }
}

async function toggleReaccionMegaEvento() {
    const token = localStorage.getItem('token');
    if (!token) {
        await mostrarModalReaccionPublica();
        return;
    }

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
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Me gusta agregado!',
                        text: 'Has marcado este mega evento como favorito',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            } else {
                iconoCorazon.className = 'far fa-heart mr-2';
                btnReaccionar.classList.remove('btn-danger');
                btnReaccionar.classList.add('btn-outline-danger');
                textoReaccion.textContent = 'Me gusta';
            }
            contadorReacciones.textContent = data.total_reacciones || 0;
        } else {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.error || 'Error al procesar la reacción'
                });
            }
        }
    } catch (error) {
        console.error('Error en toggle reacción:', error);
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error de conexión',
                text: 'No se pudo procesar la reacción'
            });
        }
    }
}

async function mostrarModalReaccionPublica() {
    const { value: formValues } = await Swal.fire({
        title: 'Reaccionar al Mega Evento',
        html: `
            <div class="text-left">
                <p class="mb-3">Para reaccionar, por favor ingresa tu información:</p>
                <input id="swal-nombres" class="swal2-input" placeholder="Nombres" required>
                <input id="swal-apellidos" class="swal2-input" placeholder="Apellidos" required>
                <input id="swal-email" class="swal2-input" type="email" placeholder="Email (opcional)">
            </div>
        `,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Reaccionar',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            return {
                nombres: document.getElementById('swal-nombres').value,
                apellidos: document.getElementById('swal-apellidos').value,
                email: document.getElementById('swal-email').value
            };
        },
        customClass: {
            popup: 'swal2-popup-custom'
        }
    });

    if (formValues && formValues.nombres && formValues.apellidos) {
        await reaccionarPublicoMegaEvento(formValues.nombres, formValues.apellidos, formValues.email);
    }
}

async function reaccionarPublicoMegaEvento(nombres, apellidos, email) {
    const btnReaccionar = document.getElementById('btnReaccionar');
    const iconoCorazon = document.getElementById('iconoCorazon');
    const textoReaccion = document.getElementById('textoReaccion');
    const contadorReacciones = document.getElementById('contadorReacciones');

    try {
        const res = await fetch(`${API_BASE_URL}/api/reacciones/mega-evento/${megaEventoId}/reaccionar-publico`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                nombres: nombres,
                apellidos: apellidos,
                email: email || null
            })
        });

        const data = await res.json();
        
        if (data.success) {
            if (data.reaccionado) {
                iconoCorazon.className = 'fas fa-heart mr-2';
                btnReaccionar.classList.remove('btn-outline-danger');
                btnReaccionar.classList.add('btn-danger');
                textoReaccion.textContent = 'Te gusta';
            } else {
                iconoCorazon.className = 'far fa-heart mr-2';
                btnReaccionar.classList.remove('btn-danger');
                btnReaccionar.classList.add('btn-outline-danger');
                textoReaccion.textContent = 'Me gusta';
            }
            contadorReacciones.textContent = data.total_reacciones || 0;
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: data.reaccionado ? '¡Me gusta agregado!' : 'Me gusta eliminado',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        } else {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.error || 'Error al procesar la reacción'
                });
            }
        }
    } catch (error) {
        console.error('Error:', error);
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error de conexión',
                text: 'No se pudo procesar la reacción'
            });
        }
    }
}

// Funciones para compartir mega eventos
let megaEventoParaCompartir = null;

// Guardar información del mega evento para compartir
function setMegaEventoParaCompartir(megaEvento) {
    megaEventoParaCompartir = {
        ...megaEvento,
        url: typeof getPublicUrl !== 'undefined' 
            ? getPublicUrl(`/mega-evento/${megaEvento.mega_evento_id}/qr`)
            : `http://192.168.0.6:8000/mega-evento/${megaEvento.mega_evento_id}/qr`
    };
    console.log('Mega evento para compartir configurado:', megaEventoParaCompartir);
}

// Mostrar modal de compartir
function mostrarModalCompartir() {
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
    const token = localStorage.getItem('token');
    try {
        const headers = {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        };
        
        if (token) {
            headers['Authorization'] = `Bearer ${token}`;
        }
        
        // Usar la ruta pública que acepta tanto usuarios autenticados como no autenticados
        await fetch(`${API_BASE_URL}/api/mega-eventos/${megaEventoId}/compartir-publico`, {
            method: 'POST',
            headers: headers,
            body: JSON.stringify({ metodo: metodo })
        });
        
        // Actualizar contador de compartidos
        await cargarContadorCompartidos();
    } catch (error) {
        console.warn('Error registrando compartido:', error);
    }
}

// Cargar contador de compartidos
async function cargarContadorCompartidos() {
    try {
        const res = await fetch(`${API_BASE_URL}/api/mega-eventos/${megaEventoId}/compartidos/total`);
        const data = await res.json();
        
        if (data.success) {
            const contador = document.getElementById('contadorCompartidos');
            if (contador) {
                contador.textContent = data.total_compartidos || 0;
            }
        }
    } catch (error) {
        console.warn('Error cargando contador de compartidos:', error);
    }
}

// Copiar enlace
async function copiarEnlaceMegaEvento() {
    // URL pública para compartir (debe ser accesible desde cualquier dispositivo)
    const url = typeof getPublicUrl !== 'undefined' 
        ? getPublicUrl(`/mega-evento/${megaEventoId}/qr`)
        : `http://192.168.0.6:8000/mega-evento/${megaEventoId}/qr`;

    // Registrar compartido en backend
    await registrarCompartidoMegaEvento(megaEventoId, 'link');
    
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
    // URL pública para compartir (debe ser accesible desde cualquier dispositivo)
    const qrUrl = typeof getPublicUrl !== 'undefined' 
        ? getPublicUrl(`/mega-evento/${megaEventoId}/qr`)
        : `http://192.168.0.6:8000/mega-evento/${megaEventoId}/qr`;

    // Registrar compartido en backend
    await registrarCompartidoMegaEvento(megaEventoId, 'qr');

    const qrContainer = document.getElementById('qrContainer');
    const qrcodeDiv = document.getElementById('qrcode');
    
    if (!qrContainer || !qrcodeDiv) return;
    
    // Limpiar contenido anterior
    qrcodeDiv.innerHTML = '';
    
    // Mostrar contenedor primero
    qrContainer.style.display = 'block';
    
    // Agregar indicador de carga
    qrcodeDiv.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x" style="color: #667eea;"></i><p class="mt-2" style="color: #333;">Generando QR...</p></div>';
    
    // Intentar cargar QRCode si no está disponible
    if (typeof QRCode === 'undefined') {
        // Verificar si ya se está cargando
        if (document.querySelector('script[src*="qrcode"]')) {
            // Esperar a que se cargue
            const checkQRCode = setInterval(() => {
                if (typeof QRCode !== 'undefined') {
                    clearInterval(checkQRCode);
                    generarQRCodeMegaEvento(qrUrl, qrcodeDiv);
                }
            }, 100);
            // Timeout después de 5 segundos
            setTimeout(() => {
                clearInterval(checkQRCode);
                if (typeof QRCode === 'undefined') {
                    generarQRConAPIMegaEvento(qrUrl, qrcodeDiv);
                }
            }, 5000);
        } else {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js';
            script.onload = function() {
                if (typeof QRCode !== 'undefined') {
                    generarQRCodeMegaEvento(qrUrl, qrcodeDiv);
                } else {
                    generarQRConAPIMegaEvento(qrUrl, qrcodeDiv);
                }
            };
            script.onerror = function() {
                generarQRConAPIMegaEvento(qrUrl, qrcodeDiv);
            };
            document.head.appendChild(script);
        }
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
                dark: '#667eea',
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
        const apiUrl = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodeURIComponent(qrUrl)}&bgcolor=FFFFFF&color=667eea`;
        const img = document.createElement('img');
        img.src = apiUrl;
        img.alt = 'QR Code';
        img.style.cssText = 'display: block; margin: 0 auto; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);';
        img.onerror = function() {
            qrcodeDiv.innerHTML = `
                <div class="text-center p-3">
                    <p class="text-danger mb-2" style="font-size: 0.9rem;">Error cargando generador de QR.</p>
                    <p class="text-muted mb-2" style="font-size: 0.85rem;">Por favor, usa el enlace directo:</p>
                    <a href="${qrUrl}" target="_blank" class="btn btn-sm btn-primary">Abrir enlace</a>
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
                <a href="${qrUrl}" target="_blank" class="btn btn-sm btn-primary">Abrir enlace</a>
            </div>
        `;
    }
}
</script>
@endsection



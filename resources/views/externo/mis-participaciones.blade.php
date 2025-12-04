@extends('layouts.adminlte-externo')

@section('page_title', 'Mis Participaciones')

@section('content_body')
<div class="container-fluid">
    <!-- Header con diseño mejorado - Paleta de colores -->
    <div class="card mb-4 shadow-sm" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); border: none; border-radius: 15px; overflow: hidden;">
        <div class="card-body py-4 px-4">
            <div class="row align-items-center">
                <div class="col-md-10">
                    <div class="d-flex align-items-center">
                        <div class="bg-white rounded-circle p-3 mr-3 shadow-sm" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                            <i class="far fa-calendar-check" style="font-size: 1.8rem; color: #00A36C;"></i>
                        </div>
                        <div>
                            <h3 class="text-white mb-1" style="font-weight: 700; font-size: 1.75rem;">
                                Mis Participaciones en Eventos
                            </h3>
                            <p class="text-white mb-0" style="opacity: 0.95; font-size: 1rem;">
                                Revisa todos los eventos en los que estás participando
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 text-right d-none d-md-block">
                    <i class="far fa-calendar-check" style="font-size: 4.5rem; color: rgba(255,255,255,0.15);"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Navbar de pestañas -->
    <div class="card mb-4 shadow-sm" style="border: none; border-radius: 12px;">
        <div class="card-body p-0">
            <ul class="nav nav-tabs nav-justified" id="participacionesTabs" role="tablist" style="border-bottom: 2px solid #E5E7EB;">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="eventos-tab" data-toggle="tab" href="#eventos" role="tab" aria-controls="eventos" aria-selected="true" style="color: #0C2B44; font-weight: 600; padding: 1rem 1.5rem; border: none; border-bottom: 3px solid transparent; transition: all 0.3s;">
                        <i class="far fa-calendar-alt mr-2"></i>Eventos
                        <span id="eventos-count" class="badge badge-primary ml-2" style="background: #00A36C; border-radius: 12px; padding: 0.25em 0.6em;">0</span>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="mega-eventos-tab" data-toggle="tab" href="#mega-eventos" role="tab" aria-controls="mega-eventos" aria-selected="false" style="color: #0C2B44; font-weight: 600; padding: 1rem 1.5rem; border: none; border-bottom: 3px solid transparent; transition: all 0.3s;">
                        <i class="far fa-star mr-2"></i>Mega Eventos
                        <span id="mega-eventos-count" class="badge badge-primary ml-2" style="background: #00A36C; border-radius: 12px; padding: 0.25em 0.6em;">0</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Contenido de las pestañas -->
    <div class="tab-content" id="participacionesTabContent">
        <!-- Pestaña de Eventos -->
        <div class="tab-pane fade show active" id="eventos" role="tabpanel" aria-labelledby="eventos-tab">
            <div id="eventosContainer" class="row">
                <div class="col-12 text-center py-5">
                    <div class="spinner-border" role="status" style="width: 3rem; height: 3rem; color: #00A36C;">
                        <span class="sr-only">Cargando...</span>
                    </div>
                    <p class="mt-3 text-muted">Cargando tus eventos...</p>
                </div>
            </div>
        </div>

        <!-- Pestaña de Mega Eventos -->
        <div class="tab-pane fade" id="mega-eventos" role="tabpanel" aria-labelledby="mega-eventos-tab">
            <div id="megaEventosContainer" class="row">
                <div class="col-12 text-center py-5">
                    <div class="spinner-border" role="status" style="width: 3rem; height: 3rem; color: #00A36C;">
                        <span class="sr-only">Cargando...</span>
                    </div>
                    <p class="mt-3 text-muted">Cargando tus mega eventos...</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    .evento-inscrito {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
    }
    
    .evento-inscrito::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(90deg, #00A36C 0%, #008a5a 100%);
        z-index: 1;
    }
    
    .evento-inscrito .card-body {
        background: linear-gradient(to bottom, rgba(0, 163, 108, 0.05) 0%, rgba(248, 249, 250, 1) 15%);
    }
    
    .evento-inscrito:hover {
        box-shadow: 0 10px 25px rgba(0, 163, 108, 0.25) !important;
        transform: translateY(-3px);
        transition: all 0.3s ease;
    }

    .card {
        transition: all 0.3s ease;
        border-radius: 12px;
        border: 1px solid #F5F5F5;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(12, 43, 68, 0.15) !important;
    }

    .badge-success {
        background-color: #00A36C !important;
        color: white !important;
    }

    .badge-warning {
        background-color: #ffc107 !important;
        color: #212529 !important;
    }

    .badge-danger {
        background-color: #dc3545 !important;
        color: white !important;
    }

    /* Estilos para las pestañas */
    .nav-tabs .nav-link {
        border: none !important;
        border-bottom: 3px solid transparent !important;
    }

    .nav-tabs .nav-link:hover {
        border-color: transparent !important;
        color: #00A36C !important;
    }

    .nav-tabs .nav-link.active {
        color: #00A36C !important;
        background-color: transparent !important;
        border-color: transparent !important;
        border-bottom: 3px solid #00A36C !important;
    }

    .nav-tabs .nav-link:focus {
        outline: none !important;
    }
</style>
@endpush

@push('js')
<script src="{{ asset('assets/js/config.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', async () => {
    await cargarMisParticipaciones();
});

function buildImageUrl(imgUrl) {
    if (!imgUrl || imgUrl.trim() === '') return null;
    if (imgUrl.startsWith('http://') || imgUrl.startsWith('https://')) return imgUrl;
    if (imgUrl.startsWith('/storage/')) return `${window.location.origin}${imgUrl}`;
    if (imgUrl.startsWith('storage/')) return `${window.location.origin}/${imgUrl}`;
    return `${window.location.origin}/storage/${imgUrl}`;
}

function formatearFechaOverlay(fechaFin) {
    if (!fechaFin) return '';
    const fecha = new Date(fechaFin);
    const dia = fecha.getDate();
    const meses = ['ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'];
    const mes = meses[fecha.getMonth()];
    return `
        <div class="position-absolute" style="top: 12px; left: 12px; background: white; border-radius: 8px; padding: 0.5rem 0.75rem; box-shadow: 0 2px 8px rgba(0,0,0,0.15); z-index: 10; pointer-events: none;">
            <div style="font-size: 1.5rem; font-weight: 700; color: #0C2B44; line-height: 1;">${dia}</div>
            <div style="font-size: 0.75rem; font-weight: 600; color: #00A36C; line-height: 1; margin-top: 2px;">${mes}</div>
        </div>
    `;
}

async function cargarMisParticipaciones() {
    const eventosContainer = document.getElementById('eventosContainer');
    const megaEventosContainer = document.getElementById('megaEventosContainer');
    const token = localStorage.getItem('token');

    if (!token) {
        eventosContainer.innerHTML = `
            <div class="col-12">
                <div class="alert alert-warning">
                    <p>Debes iniciar sesión para ver tus participaciones.</p>
                    <a href="/login" class="btn btn-primary">Iniciar sesión</a>
                </div>
            </div>
        `;
        megaEventosContainer.innerHTML = eventosContainer.innerHTML;
        return;
    }

    try {
        // Cargar eventos regulares y mega eventos en paralelo
        const [eventosRes, megaEventosRes] = await Promise.all([
            fetch(`${API_BASE_URL}/api/participaciones/mis-eventos`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            }),
            fetch(`${API_BASE_URL}/api/mega-eventos/mis-participaciones`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            })
        ]);

        const eventosData = await eventosRes.json();
        const megaEventosData = await megaEventosRes.json();

        const tieneEventos = eventosData.success && eventosData.eventos && eventosData.eventos.length > 0;
        const tieneMegaEventos = megaEventosData.success && megaEventosData.mega_eventos && megaEventosData.mega_eventos.length > 0;

        // Actualizar contadores
        document.getElementById('eventos-count').textContent = tieneEventos ? eventosData.eventos.length : 0;
        document.getElementById('mega-eventos-count').textContent = tieneMegaEventos ? megaEventosData.mega_eventos.length : 0;

        // Cargar eventos regulares
        if (tieneEventos) {
            eventosContainer.innerHTML = '';
            eventosData.eventos.forEach(participacion => {
                const evento = participacion.evento;
                const fechaInicio = evento.fecha_inicio ? new Date(evento.fecha_inicio).toLocaleDateString('es-ES', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit'
                }) : 'Fecha no especificada';

                let estadoBadge = '';
                let estadoColor = '';
                let estadoIcon = '';
                if (participacion.estado === 'aprobada') {
                    estadoBadge = 'Aprobada';
                    estadoColor = 'success';
                    estadoIcon = 'fa-check-circle';
                } else if (participacion.estado === 'rechazada') {
                    estadoBadge = 'Rechazada';
                    estadoColor = 'danger';
                    estadoIcon = 'fa-times-circle';
                } else {
                    estadoBadge = 'Pendiente';
                    estadoColor = 'warning';
                    estadoIcon = 'fa-clock';
                }

                // Procesar imágenes
                let imagenes = [];
                if (Array.isArray(evento.imagenes) && evento.imagenes.length > 0) {
                    imagenes = evento.imagenes.filter(img => img && typeof img === 'string' && img.trim().length > 0);
                } else if (typeof evento.imagenes === 'string' && evento.imagenes.trim()) {
                    try {
                        const parsed = JSON.parse(evento.imagenes);
                        if (Array.isArray(parsed)) {
                            imagenes = parsed.filter(img => img && typeof img === 'string' && img.trim().length > 0);
                        }
                    } catch (err) {
                        console.warn('Error parseando imágenes:', err);
                    }
                }

                const imagenPrincipal = imagenes.length > 0 ? buildImageUrl(imagenes[0]) : (evento.imagen_principal ? buildImageUrl(evento.imagen_principal) : null);
                const fechaOverlay = formatearFechaOverlay(evento.fecha_fin);

                // Estado badge del evento
                const estadoBadges = {
                    'borrador': '<span class="badge badge-secondary">Borrador</span>',
                    'publicado': '<span class="badge badge-success">Publicado</span>',
                    'cancelado': '<span class="badge badge-danger">Cancelado</span>'
                };
                const estadoEventoBadge = estadoBadges[evento.estado] || '<span class="badge badge-secondary">' + (evento.estado || 'N/A') + '</span>';

                const colDiv = document.createElement('div');
                colDiv.className = 'col-md-4 mb-4';
                
                colDiv.innerHTML = `
                    <div class="card border-0 shadow-sm h-100 evento-inscrito" style="border-radius: 12px; overflow: hidden; transition: transform 0.2s, box-shadow 0.2s; background: #f8f9fa; border: 2px solid #00A36C !important;">
                        ${imagenPrincipal 
                            ? `<a href="/externo/eventos/${evento.id}/detalle" style="text-decoration: none; display: block;">
                                <div class="position-relative" style="height: 200px; overflow: hidden; background: #f8f9fa; cursor: pointer;">
                                    <img src="${imagenPrincipal}" alt="${evento.titulo}" class="w-100 h-100" style="object-fit: cover; transition: transform 0.3s;" 
                                     onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\\\'http://www.w3.org/2000/svg\\' width=\\\'400\\' height=\\\'200\\'%3E%3Crect fill=\\\'%23f8f9fa\\' width=\\\'400\\' height=\\\'200\\'/%3E%3Ctext x=\\\'50%25\\' y=\\\'50%25\\' text-anchor=\\\'middle\\' dy=\\\'.3em\\' fill=\\\'%23adb5bd\\' font-family=\\\'Arial\\' font-size=\\\'14\\'%3EImagen no disponible%3C/text%3E%3C/svg%3E'; this.style.objectFit='contain'; this.style.padding='20px';">
                                ${fechaOverlay}
                                <div class="position-absolute" style="top: 12px; right: 12px; z-index: 10; pointer-events: none;">
                                    ${estadoEventoBadge}
                                </div>
                                <div class="position-absolute" style="top: 50px; left: 12px; z-index: 10; pointer-events: none;">
                                    <span class="badge badge-${estadoColor}" style="${estadoColor === 'success' ? 'background-color: #00A36C !important;' : estadoColor === 'warning' ? 'background-color: #ffc107 !important;' : 'background-color: #dc3545 !important;'} color: white; font-size: 0.7rem; padding: 0.3em 0.6em; border-radius: 15px;"><i class="fas ${estadoIcon} mr-1"></i>${estadoBadge}</span>
                                </div>
                               </div>
                               </a>`
                            : `<a href="/externo/eventos/${evento.id}/detalle" style="text-decoration: none; display: block;">
                                <div class="position-relative" style="height: 200px; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); display: flex; align-items: center; justify-content: center; cursor: pointer;">
                                <i class="far fa-calendar fa-4x text-white" style="opacity: 0.7;"></i>
                                ${fechaOverlay}
                                <div class="position-absolute" style="top: 12px; right: 12px; z-index: 10; pointer-events: none;">
                                    ${estadoEventoBadge}
                                </div>
                                <div class="position-absolute" style="top: 50px; left: 12px; z-index: 10; pointer-events: none;">
                                    <span class="badge badge-${estadoColor}" style="${estadoColor === 'success' ? 'background-color: #00A36C !important;' : estadoColor === 'warning' ? 'background-color: #ffc107 !important;' : 'background-color: #dc3545 !important;'} color: white; font-size: 0.7rem; padding: 0.3em 0.6em; border-radius: 15px;"><i class="fas ${estadoIcon} mr-1"></i>${estadoBadge}</span>
                                </div>
                               </div>`
                        }
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="mb-0" style="font-size: 1.1rem; font-weight: 600; color: #2c3e50; flex: 1;">${evento.titulo || 'Sin título'}</h5>
                                <i class="fas fa-check-circle text-success ml-2" style="font-size: 1.2rem;" title="Estás inscrito en este evento"></i>
                            </div>
                            <span class="badge badge-${estadoColor} mb-2" style="${estadoColor === 'success' ? 'background-color: #00A36C !important;' : estadoColor === 'warning' ? 'background-color: #ffc107 !important;' : 'background-color: #dc3545 !important;'} color: white; font-size: 0.75rem; display: inline-block;">
                                <i class="fas ${estadoIcon} mr-1"></i>Participas (${estadoBadge})
                            </span>
                            <p class="text-muted mb-3" style="font-size: 0.9rem; line-height: 1.5; color: #6c757d; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                ${evento.descripcion || 'Sin descripción'}
                            </p>
                            ${evento.ciudad ? `<p class="text-muted mb-2" style="font-size: 0.85rem;"><i class="fas fa-map-marker-alt mr-1"></i> ${evento.ciudad}</p>` : ''}
                            <div class="mb-3 d-flex align-items-center" style="color: #6c757d; font-size: 0.85rem;">
                                <i class="far fa-calendar-alt mr-2"></i>
                                <span>${fechaInicio}</span>
                            </div>
                            ${evento.tipo_evento ? `<span class="badge badge-info mb-3" style="font-size: 0.75rem;">${evento.tipo_evento}</span>` : ''}
                            <div class="d-flex flex-column flex-sm-row gap-2 mt-auto">
                                <a href="/externo/eventos/${evento.id}/detalle" class="btn btn-sm btn-block mb-2 mb-sm-0" style="background: linear-gradient(135deg, #00A36C 0%, #008a5a 100%); color: white; border: none; border-radius: 8px; padding: 0.5em 1.2em; font-weight: 500; transition: all 0.2s;">
                                    <i class="far fa-eye mr-1"></i> Ver Detalles
                                </a>
                                ${participacion.ticket_codigo ? `
                                    <button type="button" class="btn btn-sm btn-outline-success btn-block" onclick="mostrarTicketEvento('${participacion.ticket_codigo}', '${evento.titulo || 'Evento'}')" style="border-radius: 8px; padding: 0.5em 1.2em; font-weight: 500;">
                                        <i class="fas fa-ticket-alt mr-1"></i> Ver Ticket
                                    </button>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                `;
                
                // Efecto hover
                const card = colDiv.querySelector('.card');
                card.onmouseenter = function() {
                    this.style.transform = 'translateY(-4px)';
                    this.style.boxShadow = '0 8px 16px rgba(0, 163, 108, 0.3)';
                };
                card.onmouseleave = function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = '0 1px 3px rgba(0,0,0,0.1)';
                };
                
                eventosContainer.appendChild(colDiv);
            });
        } else {
            eventosContainer.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="far fa-calendar-alt fa-3x mb-3" style="color: #00A36C;"></i>
                        <h4>No tienes eventos registrados</h4>
                        <p class="mb-3">Explora eventos disponibles e inscríbete</p>
                        <a href="/externo/eventos" class="btn" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); color: white; border: none; border-radius: 8px; padding: 0.75rem 1.5rem; font-weight: 500;">
                            <i class="far fa-calendar-alt mr-2"></i> Ver Eventos Disponibles
                        </a>
                    </div>
                </div>
            `;
        }

        // Cargar mega eventos
        if (tieneMegaEventos) {
            megaEventosContainer.innerHTML = '';
            megaEventosData.mega_eventos.forEach(mega => {
                const fechaInicio = mega.fecha_inicio ? new Date(mega.fecha_inicio).toLocaleDateString('es-ES', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit'
                }) : 'Fecha no especificada';

                let estadoBadge = 'Aprobada';
                let estadoColor = 'success';
                let estadoIcon = 'fa-check-circle';
                if (mega.estado_participacion === 'rechazada') {
                    estadoBadge = 'Rechazada';
                    estadoColor = 'danger';
                    estadoIcon = 'fa-times-circle';
                } else if (mega.estado_participacion === 'pendiente') {
                    estadoBadge = 'Pendiente';
                    estadoColor = 'warning';
                    estadoIcon = 'fa-clock';
                }

                let imagenes = Array.isArray(mega.imagenes) ? mega.imagenes.filter(img => img && typeof img === 'string' && img.trim().length > 0) : [];
                const imagenPrincipal = imagenes.length > 0 ? buildImageUrl(imagenes[0]) : null;
                const fechaOverlay = formatearFechaOverlay(mega.fecha_fin);

                const colDiv = document.createElement('div');
                colDiv.className = 'col-md-4 mb-4';
                
                colDiv.innerHTML = `
                    <div class="card border-0 shadow-sm h-100 evento-inscrito" style="border-radius: 12px; overflow: hidden; transition: transform 0.2s, box-shadow 0.2s; background: #f8f9fa; border: 2px solid #00A36C !important;">
                        ${imagenPrincipal 
                            ? `<a href="/externo/mega-eventos/${mega.mega_evento_id}/detalle" style="text-decoration: none; display: block;">
                                <div class="position-relative" style="height: 200px; overflow: hidden; background: #f8f9fa; cursor: pointer;">
                                    <img src="${imagenPrincipal}" alt="${mega.titulo}" class="w-100 h-100" style="object-fit: cover; transition: transform 0.3s;" 
                                     onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\\\'http://www.w3.org/2000/svg\\' width=\\\'400\\' height=\\\'200\\'%3E%3Crect fill=\\\'%23f8f9fa\\' width=\\\'400\\' height=\\\'200\\'/%3E%3Ctext x=\\\'50%25\\' y=\\\'50%25\\' text-anchor=\\\'middle\\' dy=\\\'.3em\\' fill=\\\'%23adb5bd\\' font-family=\\\'Arial\\' font-size=\\\'14\\'%3EImagen no disponible%3C/text%3E%3C/svg%3E'; this.style.objectFit='contain'; this.style.padding='20px';">
                                ${fechaOverlay}
                                <div class="position-absolute" style="top: 12px; left: 12px; right: 12px; display: flex; justify-content: space-between; align-items: flex-start;">
                                    <span class="badge" style="background: rgba(12, 43, 68, 0.9); color: white; font-size: 0.75rem; padding: 0.4em 0.8em; border-radius: 20px; font-weight: 500; pointer-events: none;"><i class="far fa-star mr-1"></i>Mega Evento</span>
                                    <span class="badge badge-${estadoColor}" style="${estadoColor === 'success' ? 'background-color: #00A36C !important;' : estadoColor === 'warning' ? 'background-color: #ffc107 !important;' : 'background-color: #dc3545 !important;'} color: white; font-size: 0.7rem; padding: 0.3em 0.6em; border-radius: 15px; pointer-events: none;"><i class="fas ${estadoIcon} mr-1"></i>${estadoBadge}</span>
                                </div>
                               </div>
                               </a>`
                            : `<a href="/externo/mega-eventos/${mega.mega_evento_id}/detalle" style="text-decoration: none; display: block;">
                                <div class="position-relative" style="height: 200px; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); display: flex; align-items: center; justify-content: center; cursor: pointer;">
                                <i class="far fa-star fa-4x text-white" style="opacity: 0.7;"></i>
                                ${fechaOverlay}
                                <div class="position-absolute" style="top: 12px; left: 12px; right: 12px; display: flex; justify-content: space-between; align-items: flex-start;">
                                    <span class="badge" style="background: rgba(12, 43, 68, 0.9); color: white; font-size: 0.75rem; padding: 0.4em 0.8em; border-radius: 20px; font-weight: 500; pointer-events: none;"><i class="far fa-star mr-1"></i>Mega Evento</span>
                                    <span class="badge badge-${estadoColor}" style="${estadoColor === 'success' ? 'background-color: #00A36C !important;' : estadoColor === 'warning' ? 'background-color: #ffc107 !important;' : 'background-color: #dc3545 !important;'} color: white; font-size: 0.7rem; padding: 0.3em 0.6em; border-radius: 15px; pointer-events: none;"><i class="fas ${estadoIcon} mr-1"></i>${estadoBadge}</span>
                                </div>
                               </div>`
                        }
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="mb-0" style="font-size: 1.1rem; font-weight: 600; color: #2c3e50; flex: 1;">${mega.titulo || 'Sin título'}</h5>
                                <i class="fas fa-check-circle text-success ml-2" style="font-size: 1.2rem;" title="Estás participando en este mega evento"></i>
                            </div>
                            <span class="badge badge-${estadoColor} mb-2" style="${estadoColor === 'success' ? 'background-color: #00A36C !important;' : estadoColor === 'warning' ? 'background-color: #ffc107 !important;' : 'background-color: #dc3545 !important;'} color: white; font-size: 0.75rem; display: inline-block;">
                                <i class="fas ${estadoIcon} mr-1"></i>Participas (${estadoBadge})
                            </span>
                            <p class="text-muted mb-3" style="font-size: 0.9rem; line-height: 1.5; color: #6c757d; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                ${mega.descripcion || 'Sin descripción'}
                            </p>
                            ${mega.ubicacion ? `<p class="text-muted mb-2" style="font-size: 0.85rem;"><i class="fas fa-map-marker-alt mr-1"></i> ${mega.ubicacion}</p>` : ''}
                            <div class="mb-3 d-flex align-items-center" style="color: #6c757d; font-size: 0.85rem;">
                                <i class="far fa-calendar-alt mr-2"></i>
                                <span>${fechaInicio}</span>
                            </div>
                            ${mega.categoria ? `<span class="badge badge-warning mb-3" style="font-size: 0.75rem;">${mega.categoria}</span>` : ''}
                            <a href="/externo/mega-eventos/${mega.mega_evento_id}/detalle" class="btn btn-sm btn-block mt-auto" style="background: linear-gradient(135deg, #00A36C 0%, #008a5a 100%); color: white; border: none; border-radius: 8px; padding: 0.5em 1.2em; font-weight: 500; transition: all 0.2s;">
                                <i class="far fa-eye mr-1"></i> Ver Detalles
                            </a>
                        </div>
                    </div>
                `;
                
                // Efecto hover
                const card = colDiv.querySelector('.card');
                card.onmouseenter = function() {
                    this.style.transform = 'translateY(-4px)';
                    this.style.boxShadow = '0 8px 16px rgba(0, 163, 108, 0.3)';
                };
                card.onmouseleave = function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = '0 1px 3px rgba(0,0,0,0.1)';
                };
                
                megaEventosContainer.appendChild(colDiv);
            });
        } else {
            megaEventosContainer.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="far fa-star fa-3x mb-3" style="color: #00A36C;"></i>
                        <h4>No tienes mega eventos registrados</h4>
                        <p class="mb-3">Explora mega eventos disponibles e inscríbete</p>
                        <a href="/externo/mega-eventos" class="btn" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); color: white; border: none; border-radius: 8px; padding: 0.75rem 1.5rem; font-weight: 500;">
                            <i class="far fa-star mr-2"></i> Ver Mega Eventos Disponibles
                        </a>
                    </div>
                </div>
            `;
        }

    } catch (error) {
        console.error('Error cargando participaciones:', error);
        eventosContainer.innerHTML = `
            <div class="col-12">
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Error de conexión al cargar tus participaciones
                </div>
            </div>
        `;
        megaEventosContainer.innerHTML = eventosContainer.innerHTML;
    }
}

/**
 * Mostrar ticket del evento con QR usando el código del ticket.
 */
function mostrarTicketEvento(ticketCodigo, tituloEvento) {
    if (typeof Swal === 'undefined') {
        alert(`Tu código de ticket es: ${ticketCodigo}`);
        return;
    }

    const containerId = 'ticket-qr-container-' + Date.now();

    Swal.fire({
        title: 'Ticket de acceso',
        html: `
            <div style="margin-top: 0.5rem;">
                <p style="font-size: 0.95rem; color: #4b5563; margin-bottom: 0.5rem;">
                    Evento: <strong>${tituloEvento}</strong>
                </p>
                <div id="${containerId}" style="display: flex; flex-direction: column; align-items: center; justify-content: center;">
                    <div class="mb-3" id="${containerId}-qr"></div>
                    <code style="background: #f3f4f6; padding: 0.35rem 0.6rem; border-radius: 0.375rem; font-size: 0.85rem; word-break: break-all;">
                        ${ticketCodigo}
                    </code>
                    <p class="mt-2 mb-0" style="font-size: 0.8rem; color: #6b7280;">Muestra este QR o el código al ingresar al evento.</p>
                </div>
            </div>
        `,
        showConfirmButton: true,
        confirmButtonText: 'Cerrar',
        width: 400,
        didOpen: () => {
            const target = document.getElementById(`${containerId}-qr`);
            if (!target) return;

            const qrText = ticketCodigo;

            // Intentar usar librería QRCode si está disponible
            const renderQr = () => {
                try {
                    target.innerHTML = '';
                    new QRCode(target, {
                        text: qrText,
                        width: 200,
                        height: 200,
                        colorDark : "#0C2B44",
                        colorLight : "#ffffff",
                        correctLevel : QRCode.CorrectLevel.H
                    });
                } catch (e) {
                    console.error('Error generando QR:', e);
                    target.innerHTML = '<p style="color:#dc2626;font-size:0.85rem;">No se pudo generar el QR. Usa el código de texto.</p>';
                }
            };

            if (typeof QRCode === 'undefined') {
                const script = document.createElement('script');
                script.src = 'https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js';
                script.onload = renderQr;
                script.onerror = () => {
                    target.innerHTML = '<p style="color:#dc2626;font-size:0.85rem;">No se pudo cargar la librería de QR. Usa el código de texto.</p>';
                };
                document.head.appendChild(script);
            } else {
                renderQr();
            }
        }
    });
}
</script>
@endpush

@extends('layouts.adminlte-externo')

@section('page_title', 'Mis Participaciones')

@section('content_body')
<div class="container-fluid">
    <!-- Header Minimalista -->
    <div class="mb-5" style="padding-top: 1rem;">
        <div class="d-flex align-items-center mb-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 56px; height: 56px; background: #f0fdf4; color: #00A36C;">
                <i class="far fa-calendar-check" style="font-size: 1.5rem;"></i>
            </div>
            <div>
                <h2 class="mb-1" style="font-weight: 700; font-size: 1.875rem; color: #111827;">
                    Mis Participaciones
                </h2>
                <p class="mb-0" style="color: #6b7280; font-size: 0.95rem;">
                    Revisa todos los eventos en los que estás participando
                </p>
            </div>
        </div>
    </div>

    <!-- Navbar de pestañas - Minimalista -->
    <div class="mb-5">
        <ul class="nav nav-tabs" id="participacionesTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="eventos-tab" data-toggle="tab" href="#eventos" role="tab" aria-controls="eventos" aria-selected="true">
                    <i class="far fa-calendar-alt mr-2"></i>Eventos
                    <span id="eventos-count" class="badge badge-minimalista ml-2" style="background: #e0f2fe; color: #0369a1; font-size: 0.75rem; padding: 0.25em 0.5em;">0</span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="mega-eventos-tab" data-toggle="tab" href="#mega-eventos" role="tab" aria-controls="mega-eventos" aria-selected="false">
                    <i class="far fa-star mr-2"></i>Mega Eventos
                    <span id="mega-eventos-count" class="badge badge-minimalista ml-2" style="background: #e0f2fe; color: #0369a1; font-size: 0.75rem; padding: 0.25em 0.5em;">0</span>
                </a>
            </li>
        </ul>
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
    /* Estilo Minimalista */
    body {
        background-color: #f8f9fa;
    }

    .evento-inscrito {
        position: relative;
        border-radius: 16px;
        overflow: hidden;
        background: #ffffff;
        border: 1px solid #e5e7eb !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .evento-inscrito::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #00A36C 0%, #008a5a 100%);
        z-index: 1;
    }
    
    .evento-inscrito .card-body {
        background: #ffffff;
        padding: 1.5rem;
    }
    
    .evento-inscrito:hover {
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.08) !important;
        transform: translateY(-4px);
        border-color: #d1d5db !important;
    }

    .card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .card:hover {
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.08) !important;
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

    /* Estilos para las pestañas - Minimalista */
    .nav-tabs {
        border-bottom: 1px solid #e5e7eb;
        background: transparent;
    }

    .nav-tabs .nav-link {
        border: none !important;
        border-bottom: 2px solid transparent !important;
        color: #6b7280;
        font-weight: 500;
        padding: 1rem 1.5rem;
        transition: all 0.2s ease;
    }

    .nav-tabs .nav-link:hover {
        border-color: transparent !important;
        color: #00A36C !important;
        background-color: rgba(0, 163, 108, 0.05);
    }

    .nav-tabs .nav-link.active {
        color: #00A36C !important;
        background-color: transparent !important;
        border-color: transparent !important;
        border-bottom: 2px solid #00A36C !important;
        font-weight: 600;
    }

    .nav-tabs .nav-link:focus {
        outline: none !important;
    }

    /* Botones minimalistas */
    .btn-minimalista {
        border-radius: 10px;
        padding: 0.625rem 1.25rem;
        font-weight: 500;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        border: 1px solid transparent;
    }

    .btn-minimalista:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    /* Sección de ticket mejorada */
    .ticket-section {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 1.25rem;
        margin: 1rem 0;
    }

    .ticket-buttons {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        margin-top: 1rem;
    }

    .ticket-button {
        width: 100%;
        border-radius: 10px;
        padding: 0.75rem 1rem;
        font-weight: 500;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        border: 1px solid;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .ticket-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .ticket-button-primary {
        background: #00A36C;
        color: white;
        border-color: #00A36C;
    }

    .ticket-button-primary:hover {
        background: #008a5a;
        border-color: #008a5a;
        color: white;
    }

    .ticket-button-secondary {
        background: white;
        color: #0C2B44;
        border-color: #d1d5db;
    }

    .ticket-button-secondary:hover {
        background: #f9fafb;
        border-color: #00A36C;
        color: #00A36C;
    }

    /* Espaciado mejorado */
    .card-body {
        display: flex;
        flex-direction: column;
    }

    .card-content {
        flex: 1;
    }

    .card-actions {
        margin-top: auto;
        padding-top: 1rem;
    }

    /* Imagen mejorada */
    .evento-image {
        height: 240px;
        object-fit: cover;
        transition: transform 0.4s ease;
    }

    .evento-inscrito:hover .evento-image {
        transform: scale(1.05);
    }

    /* Badges minimalistas */
    .badge-minimalista {
        padding: 0.375rem 0.75rem;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 500;
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

// Función helper para formatear fechas desde PostgreSQL sin conversión de zona horaria
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
                const fechaInicio = formatearFechaPostgreSQL(evento.fecha_inicio);

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
                    <div class="card border-0 shadow-sm h-100 evento-inscrito">
                        ${imagenPrincipal 
                            ? `<a href="/externo/eventos/${evento.id}/detalle" style="text-decoration: none; display: block;">
                                <div class="position-relative" style="height: 240px; overflow: hidden; background: #f9fafb; cursor: pointer;">
                                    <img src="${imagenPrincipal}" alt="${evento.titulo}" class="w-100 h-100 evento-image" 
                                     onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\\\'http://www.w3.org/2000/svg\\' width=\\\'400\\' height=\\\'240\\'%3E%3Crect fill=\\\'%23f9fafb\\' width=\\\'400\\' height=\\\'240\\'/%3E%3Ctext x=\\\'50%25\\' y=\\\'50%25\\' text-anchor=\\\'middle\\' dy=\\\'.3em\\' fill=\\\'%23d1d5db\\' font-family=\\\'Arial\\' font-size=\\\'14\\'%3EImagen no disponible%3C/text%3E%3C/svg%3E'; this.style.objectFit='contain'; this.style.padding='20px';">
                                ${fechaOverlay}
                                <div class="position-absolute" style="top: 16px; right: 16px; z-index: 10; pointer-events: none;">
                                    ${estadoEventoBadge}
                                </div>
                                <div class="position-absolute" style="top: 16px; left: 16px; z-index: 10; pointer-events: none;">
                                    <span class="badge badge-minimalista badge-${estadoColor}" style="${estadoColor === 'success' ? 'background-color: #00A36C !important;' : estadoColor === 'warning' ? 'background-color: #ffc107 !important;' : 'background-color: #dc3545 !important;'} color: white;"><i class="fas ${estadoIcon} mr-1"></i>${estadoBadge}</span>
                                </div>
                               </div>
                               </a>`
                            : `<a href="/externo/eventos/${evento.id}/detalle" style="text-decoration: none; display: block;">
                                <div class="position-relative" style="height: 240px; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); display: flex; align-items: center; justify-content: center; cursor: pointer;">
                                <i class="far fa-calendar fa-4x text-white" style="opacity: 0.6;"></i>
                                ${fechaOverlay}
                                <div class="position-absolute" style="top: 16px; right: 16px; z-index: 10; pointer-events: none;">
                                    ${estadoEventoBadge}
                                </div>
                                <div class="position-absolute" style="top: 16px; left: 16px; z-index: 10; pointer-events: none;">
                                    <span class="badge badge-minimalista badge-${estadoColor}" style="${estadoColor === 'success' ? 'background-color: #00A36C !important;' : estadoColor === 'warning' ? 'background-color: #ffc107 !important;' : 'background-color: #dc3545 !important;'} color: white;"><i class="fas ${estadoIcon} mr-1"></i>${estadoBadge}</span>
                                </div>
                               </div>`
                        }
                        <div class="card-body">
                            <div class="card-content">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h5 class="mb-0" style="font-size: 1.25rem; font-weight: 600; color: #111827; line-height: 1.3;">${evento.titulo || 'Sin título'}</h5>
                                    <i class="fas fa-check-circle text-success ml-2" style="font-size: 1.25rem; opacity: 0.8;" title="Estás inscrito en este evento"></i>
                                </div>
                                
                                <div class="mb-3">
                                    <span class="badge badge-minimalista badge-${estadoColor}" style="${estadoColor === 'success' ? 'background-color: #00A36C !important;' : estadoColor === 'warning' ? 'background-color: #ffc107 !important;' : 'background-color: #dc3545 !important;'} color: white;">
                                        <i class="fas ${estadoIcon} mr-1"></i>${estadoBadge}
                                    </span>
                                    ${evento.tipo_evento ? `<span class="badge badge-minimalista badge-info ml-2" style="background-color: #e0f2fe !important; color: #0369a1 !important; font-size: 0.75rem;">${evento.tipo_evento}</span>` : ''}
                                </div>
                                
                                <p class="text-muted mb-3" style="font-size: 0.9rem; line-height: 1.6; color: #6b7280; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    ${evento.descripcion || 'Sin descripción'}
                                </p>
                                
                                <div class="mb-3" style="display: flex; flex-direction: column; gap: 0.5rem;">
                                    ${evento.ciudad ? `<div class="d-flex align-items-center" style="color: #6b7280; font-size: 0.875rem;"><i class="fas fa-map-marker-alt mr-2" style="width: 16px; color: #9ca3af;"></i><span>${evento.ciudad}</span></div>` : ''}
                                    <div class="d-flex align-items-center" style="color: #6b7280; font-size: 0.875rem;">
                                        <i class="far fa-calendar-alt mr-2" style="width: 16px; color: #9ca3af;"></i>
                                        <span>${fechaInicio}</span>
                                    </div>
                                </div>
                                
                                <!-- Sección de Ticket - Diseño Minimalista Mejorado -->
                                ${participacion.ticket_codigo ? `
                                    <div class="ticket-section">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 40px; height: 40px; background: #f0fdf4; color: #00A36C;">
                                                <i class="fas fa-ticket-alt" style="font-size: 1.1rem;"></i>
                                            </div>
                                            <div>
                                                <strong style="color: #111827; font-size: 0.95rem; font-weight: 600;">Ticket de Acceso</strong>
                                                <p class="mb-0" style="font-size: 0.75rem; color: #6b7280; font-family: 'Courier New', monospace;">${participacion.ticket_codigo}</p>
                                            </div>
                                        </div>
                                        <div class="ticket-buttons">
                                            <button type="button" class="ticket-button ticket-button-primary" onclick="descargarQRTicket('${participacion.ticket_codigo}', '${evento.titulo || 'Evento'}')">
                                                <i class="fas fa-download mr-2"></i> Descargar QR
                                            </button>
                                            <button type="button" class="ticket-button ticket-button-secondary" onclick="copiarCodigoTicket('${participacion.ticket_codigo}')">
                                                <i class="fas fa-copy mr-2"></i> Copiar Código
                                            </button>
                                        </div>
                                    </div>
                                ` : ''}
                                
                                <!-- Separador visual -->
                                <div style="margin: 1.5rem 0; border-top: 1px solid #e5e7eb;"></div>
                                
                                <!-- Botón de Registrar Asistencia (si el evento está en curso y no ha marcado asistencia) -->
                                ${(function() {
                                    const ahora = new Date();
                                    let fechaInicio = null;
                                    let fechaFin = null;
                                    
                                    if (evento.fecha_inicio) {
                                        const match = evento.fecha_inicio.toString().match(/^(\d{4})-(\d{2})-(\d{2})[\sT](\d{2}):(\d{2}):(\d{2})/);
                                        if (match) {
                                            const [, year, month, day, hour, minute, second] = match;
                                            fechaInicio = new Date(parseInt(year, 10), parseInt(month, 10) - 1, parseInt(day, 10), parseInt(hour, 10), parseInt(minute, 10), parseInt(second || 0, 10));
                                        } else {
                                            fechaInicio = new Date(evento.fecha_inicio);
                                        }
                                    }
                                    
                                    if (evento.fecha_fin) {
                                        const match = evento.fecha_fin.toString().match(/^(\d{4})-(\d{2})-(\d{2})[\sT](\d{2}):(\d{2}):(\d{2})/);
                                        if (match) {
                                            const [, year, month, day, hour, minute, second] = match;
                                            fechaFin = new Date(parseInt(year, 10), parseInt(month, 10) - 1, parseInt(day, 10), parseInt(hour, 10), parseInt(minute, 10), parseInt(second || 0, 10));
                                        } else {
                                            fechaFin = new Date(evento.fecha_fin);
                                        }
                                    }
                                    
                                    const eventoEnCurso = fechaInicio && fechaFin && ahora >= fechaInicio && ahora <= fechaFin;
                                    const yaMarcado = participacion.estado_asistencia === 'asistido' || participacion.asistio === true;
                                    
                                    if (yaMarcado) {
                                        return `
                                            <div class="mt-3 pt-3 border-top">
                                                <div class="alert alert-success mb-0" style="border-radius: 8px; padding: 0.75rem;">
                                                    <i class="fas fa-check-circle mr-2"></i>
                                                    <strong>Asistencia registrada</strong>
                                                    <p class="mb-0 mt-1" style="font-size: 0.85rem;">Tu asistencia fue registrada correctamente.</p>
                                                </div>
                                            </div>
                                        `;
                                    } else if (eventoEnCurso && participacion.estado === 'aprobada') {
                                        return `
                                            <div class="mt-3 pt-3 border-top">
                                                <button type="button" class="btn btn-block" onclick="registrarAsistenciaDesdeMisParticipaciones(${evento.id}, '${participacion.ticket_codigo || ''}')" 
                                                        style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; border: none; border-radius: 8px; padding: 0.6rem; font-weight: 600;">
                                                    <i class="fas fa-clipboard-check mr-2"></i> Registrar Mi Asistencia
                                                </button>
                                            </div>
                                        `;
                                    }
                                    return '';
                                })()}
                            </div>
                            
                            <div class="card-actions">
                                <a href="/externo/eventos/${evento.id}/detalle" class="btn btn-minimalista btn-block" style="background: #00A36C; color: white; border-color: #00A36C;">
                                    <i class="far fa-eye mr-2"></i> Ver Detalles
                                </a>
                            </div>
                        </div>
                    </div>
                `;
                
                // Efecto hover mejorado
                const card = colDiv.querySelector('.card');
                card.onmouseenter = function() {
                    this.style.transform = 'translateY(-4px)';
                    this.style.boxShadow = '0 12px 32px rgba(0, 0, 0, 0.08)';
                };
                card.onmouseleave = function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = '0 1px 3px rgba(0, 0, 0, 0.05)';
                };
                
                eventosContainer.appendChild(colDiv);
            });
        } else {
            eventosContainer.innerHTML = `
                <div class="col-12">
                    <div class="text-center py-5" style="background: white; border-radius: 16px; border: 1px solid #e5e7eb; padding: 3rem 2rem;">
                        <div class="mb-4">
                            <i class="far fa-calendar-alt" style="font-size: 4rem; color: #d1d5db;"></i>
                        </div>
                        <h4 class="mb-2" style="color: #111827; font-weight: 600;">No tienes eventos registrados</h4>
                        <p class="mb-4" style="color: #6b7280;">Explora eventos disponibles e inscríbete</p>
                        <a href="/externo/eventos" class="btn btn-minimalista" style="background: #00A36C; color: white; border-color: #00A36C;">
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
                const fechaInicio = formatearFechaPostgreSQL(mega.fecha_inicio);

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
                    <div class="card border-0 shadow-sm h-100 evento-inscrito">
                        ${imagenPrincipal 
                            ? `<a href="/externo/mega-eventos/${mega.mega_evento_id}/detalle" style="text-decoration: none; display: block;">
                                <div class="position-relative" style="height: 240px; overflow: hidden; background: #f9fafb; cursor: pointer;">
                                    <img src="${imagenPrincipal}" alt="${mega.titulo}" class="w-100 h-100 evento-image" 
                                     onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\\\'http://www.w3.org/2000/svg\\' width=\\\'400\\' height=\\\'240\\'%3E%3Crect fill=\\\'%23f9fafb\\' width=\\\'400\\' height=\\\'240\\'/%3E%3Ctext x=\\\'50%25\\' y=\\\'50%25\\' text-anchor=\\\'middle\\' dy=\\\'.3em\\' fill=\\\'%23d1d5db\\' font-family=\\\'Arial\\' font-size=\\\'14\\'%3EImagen no disponible%3C/text%3E%3C/svg%3E'; this.style.objectFit='contain'; this.style.padding='20px';">
                                ${fechaOverlay}
                                <div class="position-absolute" style="top: 16px; left: 16px; right: 16px; display: flex; justify-content: space-between; align-items: flex-start; z-index: 10; pointer-events: none;">
                                    <span class="badge badge-minimalista" style="background: rgba(12, 43, 68, 0.9); color: white;"><i class="far fa-star mr-1"></i>Mega Evento</span>
                                    <span class="badge badge-minimalista badge-${estadoColor}" style="${estadoColor === 'success' ? 'background-color: #00A36C !important;' : estadoColor === 'warning' ? 'background-color: #ffc107 !important;' : 'background-color: #dc3545 !important;'} color: white;"><i class="fas ${estadoIcon} mr-1"></i>${estadoBadge}</span>
                                </div>
                               </div>
                               </a>`
                            : `<a href="/externo/mega-eventos/${mega.mega_evento_id}/detalle" style="text-decoration: none; display: block;">
                                <div class="position-relative" style="height: 240px; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); display: flex; align-items: center; justify-content: center; cursor: pointer;">
                                <i class="far fa-star fa-4x text-white" style="opacity: 0.6;"></i>
                                ${fechaOverlay}
                                <div class="position-absolute" style="top: 16px; left: 16px; right: 16px; display: flex; justify-content: space-between; align-items: flex-start; z-index: 10; pointer-events: none;">
                                    <span class="badge badge-minimalista" style="background: rgba(12, 43, 68, 0.9); color: white;"><i class="far fa-star mr-1"></i>Mega Evento</span>
                                    <span class="badge badge-minimalista badge-${estadoColor}" style="${estadoColor === 'success' ? 'background-color: #00A36C !important;' : estadoColor === 'warning' ? 'background-color: #ffc107 !important;' : 'background-color: #dc3545 !important;'} color: white;"><i class="fas ${estadoIcon} mr-1"></i>${estadoBadge}</span>
                                </div>
                               </div>`
                        }
                        <div class="card-body">
                            <div class="card-content">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h5 class="mb-0" style="font-size: 1.25rem; font-weight: 600; color: #111827; line-height: 1.3;">${mega.titulo || 'Sin título'}</h5>
                                    <i class="fas fa-check-circle text-success ml-2" style="font-size: 1.25rem; opacity: 0.8;" title="Estás participando en este mega evento"></i>
                                </div>
                                
                                <div class="mb-3">
                                    <span class="badge badge-minimalista badge-${estadoColor}" style="${estadoColor === 'success' ? 'background-color: #00A36C !important;' : estadoColor === 'warning' ? 'background-color: #ffc107 !important;' : 'background-color: #dc3545 !important;'} color: white;">
                                        <i class="fas ${estadoIcon} mr-1"></i>${estadoBadge}
                                    </span>
                                    ${mega.categoria ? `<span class="badge badge-minimalista badge-warning ml-2" style="background-color: #fef3c7 !important; color: #92400e !important; font-size: 0.75rem;">${mega.categoria}</span>` : ''}
                                </div>
                                
                                <p class="text-muted mb-3" style="font-size: 0.9rem; line-height: 1.6; color: #6b7280; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    ${mega.descripcion || 'Sin descripción'}
                                </p>
                                
                                <div class="mb-3" style="display: flex; flex-direction: column; gap: 0.5rem;">
                                    ${mega.ubicacion ? `<div class="d-flex align-items-center" style="color: #6b7280; font-size: 0.875rem;"><i class="fas fa-map-marker-alt mr-2" style="width: 16px; color: #9ca3af;"></i><span>${mega.ubicacion}</span></div>` : ''}
                                    <div class="d-flex align-items-center" style="color: #6b7280; font-size: 0.875rem;">
                                        <i class="far fa-calendar-alt mr-2" style="width: 16px; color: #9ca3af;"></i>
                                        <span>${fechaInicio}</span>
                                    </div>
                                </div>
                                
                                <!-- Sección de Ticket - Diseño Minimalista Mejorado para Mega Eventos -->
                                ${mega.ticket_codigo ? `
                                    <div class="ticket-section">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 40px; height: 40px; background: #f0fdf4; color: #00A36C;">
                                                <i class="fas fa-ticket-alt" style="font-size: 1.1rem;"></i>
                                            </div>
                                            <div>
                                                <strong style="color: #111827; font-size: 0.95rem; font-weight: 600;">Ticket de Acceso</strong>
                                                <p class="mb-0" style="font-size: 0.75rem; color: #6b7280; font-family: 'Courier New', monospace;">${mega.ticket_codigo}</p>
                                            </div>
                                        </div>
                                        <div class="ticket-buttons">
                                            <button type="button" class="ticket-button ticket-button-primary" onclick="descargarQRTicketMega('${mega.ticket_codigo}', '${mega.titulo || 'Mega Evento'}')">
                                                <i class="fas fa-download mr-2"></i> Descargar QR
                                            </button>
                                            <button type="button" class="ticket-button ticket-button-secondary" onclick="copiarCodigoTicket('${mega.ticket_codigo}')">
                                                <i class="fas fa-copy mr-2"></i> Copiar Código
                                            </button>
                                        </div>
                                    </div>
                                ` : ''}
                            </div>
                            
                            <!-- Separador visual -->
                            <div style="margin: 1.5rem 0; border-top: 1px solid #e5e7eb;"></div>
                            
                            <!-- Botón de Registrar Asistencia para Mega Eventos (si está en curso y no ha marcado asistencia) -->
                            ${(function() {
                                const ahora = new Date();
                                let fechaInicio = null;
                                let fechaFin = null;
                                
                                if (mega.fecha_inicio) {
                                    const match = mega.fecha_inicio.toString().match(/^(\d{4})-(\d{2})-(\d{2})[\sT](\d{2}):(\d{2}):(\d{2})/);
                                    if (match) {
                                        const [, year, month, day, hour, minute, second] = match;
                                        fechaInicio = new Date(parseInt(year, 10), parseInt(month, 10) - 1, parseInt(day, 10), parseInt(hour, 10), parseInt(minute, 10), parseInt(second || 0, 10));
                                    } else {
                                        fechaInicio = new Date(mega.fecha_inicio);
                                    }
                                }
                                
                                if (mega.fecha_fin) {
                                    const match = mega.fecha_fin.toString().match(/^(\d{4})-(\d{2})-(\d{2})[\sT](\d{2}):(\d{2}):(\d{2})/);
                                    if (match) {
                                        const [, year, month, day, hour, minute, second] = match;
                                        fechaFin = new Date(parseInt(year, 10), parseInt(month, 10) - 1, parseInt(day, 10), parseInt(hour, 10), parseInt(minute, 10), parseInt(second || 0, 10));
                                    } else {
                                        fechaFin = new Date(mega.fecha_fin);
                                    }
                                }
                                
                                const eventoEnCurso = fechaInicio && fechaFin && ahora >= fechaInicio && ahora <= fechaFin;
                                const yaMarcado = mega.estado_asistencia === 'asistido' || mega.asistio === true;
                                
                                if (yaMarcado) {
                                    return `
                                        <div class="mt-3 pt-3 border-top">
                                            <div class="alert alert-success mb-0" style="border-radius: 8px; padding: 0.75rem;">
                                                <i class="fas fa-check-circle mr-2"></i>
                                                <strong>Asistencia registrada</strong>
                                                <p class="mb-0 mt-1" style="font-size: 0.85rem;">Tu asistencia fue registrada correctamente.</p>
                                            </div>
                                        </div>
                                    `;
                                } else if (eventoEnCurso && mega.estado_participacion === 'aprobada') {
                                    return `
                                        <div class="mt-3 pt-3 border-top">
                                            <button type="button" class="btn btn-block" onclick="registrarAsistenciaMegaDesdeMisParticipaciones(${mega.mega_evento_id})" 
                                                    style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; border: none; border-radius: 8px; padding: 0.6rem; font-weight: 600;">
                                                <i class="fas fa-clipboard-check mr-2"></i> Registrar Mi Asistencia
                                            </button>
                                        </div>
                                    `;
                                }
                                return '';
                            })()}
                            
                            <div class="card-actions">
                                <a href="/externo/mega-eventos/${mega.mega_evento_id}/detalle" class="btn btn-minimalista btn-block" style="background: #00A36C; color: white; border-color: #00A36C;">
                                    <i class="far fa-eye mr-2"></i> Ver Detalles
                                </a>
                            </div>
                        </div>
                    </div>
                `;
                
                // Efecto hover mejorado
                const card = colDiv.querySelector('.card');
                card.onmouseenter = function() {
                    this.style.transform = 'translateY(-4px)';
                    this.style.boxShadow = '0 12px 32px rgba(0, 0, 0, 0.08)';
                };
                card.onmouseleave = function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = '0 1px 3px rgba(0, 0, 0, 0.05)';
                };
                
                megaEventosContainer.appendChild(colDiv);
            });
        } else {
            megaEventosContainer.innerHTML = `
                <div class="col-12">
                    <div class="text-center py-5" style="background: white; border-radius: 16px; border: 1px solid #e5e7eb; padding: 3rem 2rem;">
                        <div class="mb-4">
                            <i class="far fa-star" style="font-size: 4rem; color: #d1d5db;"></i>
                        </div>
                        <h4 class="mb-2" style="color: #111827; font-weight: 600;">No tienes mega eventos registrados</h4>
                        <p class="mb-4" style="color: #6b7280;">Explora mega eventos disponibles e inscríbete</p>
                        <a href="/externo/mega-eventos" class="btn btn-minimalista" style="background: #00A36C; color: white; border-color: #00A36C;">
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
 * Descargar QR del ticket como imagen (solo una vez)
 */
async function descargarQRTicket(ticketCodigo, tituloEvento) {
    const token = localStorage.getItem('token');
    if (!token) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Debes iniciar sesión para descargar el QR del ticket'
            });
        } else {
            alert('Debes iniciar sesión para descargar el QR del ticket');
        }
        return;
    }

    // Mostrar indicador de carga
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Verificando...',
            text: 'Validando descarga del QR',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }

    try {
        // Primero registrar la descarga en el backend
        const apiUrl = window.API_BASE_URL || 'http://192.168.0.7:8000';
        const res = await fetch(`${apiUrl}/api/registrar-descarga-qr`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                ticket_codigo: ticketCodigo
            })
        });

        const data = await res.json();

        if (!data.success) {
            // Si ya fue descargado, mostrar mensaje específico
            if (data.ya_descargado) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'QR ya descargado',
                        html: `El QR de este ticket ya fue descargado anteriormente.<br><small>Fecha: ${data.fecha_descarga_anterior || 'No disponible'}</small><br><br><small>Solo se permite una descarga por ticket por seguridad.</small>`,
                        confirmButtonText: 'Entendido'
                    });
                } else {
                    alert(`El QR de este ticket ya fue descargado anteriormente (${data.fecha_descarga_anterior || 'No disponible'}). Solo se permite una descarga por ticket.`);
                }
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.error || 'No se pudo autorizar la descarga del QR'
                    });
                } else {
                    alert(data.error || 'No se pudo autorizar la descarga del QR');
                }
            }
            return;
        }

        // Si la descarga fue autorizada, proceder a generar y descargar el QR
        if (typeof Swal !== 'undefined') {
            Swal.close();
        }

    // Cargar librería QRCode si no está disponible
    const generarYDescargarQR = () => {
        try {
            // Crear contenedor temporal
            const container = document.createElement('div');
            container.style.position = 'absolute';
            container.style.left = '-9999px';
            container.id = 'temp-qr-container-' + Date.now();
            document.body.appendChild(container);

            // Generar QR
            new QRCode(container, {
                text: ticketCodigo,
                width: 400,
                height: 400,
                colorDark: "#0C2B44",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });

            // Esperar a que se genere el QR
            setTimeout(() => {
                const canvas = container.querySelector('canvas');
                if (canvas) {
                    // Convertir canvas a imagen
                    canvas.toBlob((blob) => {
                        const url = URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = `QR-Ticket-${tituloEvento.replace(/[^a-z0-9]/gi, '_')}-${ticketCodigo.substring(0, 8)}.png`;
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                        URL.revokeObjectURL(url);
                            
                            // Mostrar mensaje de éxito
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡QR descargado!',
                                    text: 'El código QR del ticket se ha descargado correctamente',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            }
                        });
                    } else {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error al generar el código QR'
                    });
                } else {
                    alert('Error al generar el código QR');
                        }
                }
                document.body.removeChild(container);
            }, 500);
        } catch (e) {
            console.error('Error generando QR:', e);
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al generar el código QR. Por favor, intenta nuevamente.'
                    });
                } else {
            alert('Error al generar el código QR. Por favor, intenta nuevamente.');
                }
        }
    };

    if (typeof QRCode === 'undefined') {
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js';
        script.onload = generarYDescargarQR;
        script.onerror = () => {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo cargar la librería de QR. Por favor, recarga la página e intenta nuevamente.'
                    });
                } else {
            alert('No se pudo cargar la librería de QR. Por favor, recarga la página e intenta nuevamente.');
                }
        };
        document.head.appendChild(script);
    } else {
        generarYDescargarQR();
        }

    } catch (error) {
        console.error('Error descargando QR:', error);
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al procesar la descarga. Por favor, intenta nuevamente.'
            });
        } else {
            alert('Error al procesar la descarga. Por favor, intenta nuevamente.');
        }
    }
}

/**
 * Descargar QR del ticket de mega evento como imagen (solo una vez)
 */
async function descargarQRTicketMega(ticketCodigo, tituloMegaEvento) {
    const token = localStorage.getItem('token');
    if (!token) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Debes iniciar sesión para descargar el QR del ticket'
            });
        } else {
            alert('Debes iniciar sesión para descargar el QR del ticket');
        }
        return;
    }

    // Mostrar indicador de carga
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Verificando...',
            text: 'Validando descarga del QR',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }

    try {
        // Primero registrar la descarga en el backend (endpoint de mega eventos)
        const apiUrl = window.API_BASE_URL || 'http://192.168.0.7:8000';
        const res = await fetch(`${apiUrl}/api/mega-eventos/registrar-descarga-qr`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                ticket_codigo: ticketCodigo
            })
        });

        const data = await res.json();

        if (!data.success) {
            // Si ya fue descargado, mostrar mensaje específico
            if (data.ya_descargado) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'QR ya descargado',
                        html: `El QR de este ticket ya fue descargado anteriormente.<br><small>Fecha: ${data.fecha_descarga_anterior || 'No disponible'}</small><br><br><small>Solo se permite una descarga por ticket por seguridad.</small>`,
                        confirmButtonText: 'Entendido'
                    });
                } else {
                    alert(`El QR de este ticket ya fue descargado anteriormente (${data.fecha_descarga_anterior || 'No disponible'}). Solo se permite una descarga por ticket.`);
                }
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.error || 'No se pudo autorizar la descarga del QR'
                    });
                } else {
                    alert(data.error || 'No se pudo autorizar la descarga del QR');
                }
            }
            return;
        }

        // Si la descarga fue autorizada, proceder a generar y descargar el QR
        if (typeof Swal !== 'undefined') {
            Swal.close();
        }

    // Cargar librería QRCode si no está disponible
    const generarYDescargarQR = () => {
        try {
            // Crear contenedor temporal
            const container = document.createElement('div');
            container.style.position = 'absolute';
            container.style.left = '-9999px';
            container.id = 'temp-qr-container-' + Date.now();
            document.body.appendChild(container);

            // Generar QR
            new QRCode(container, {
                text: ticketCodigo,
                width: 400,
                height: 400,
                colorDark: "#0C2B44",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });

            // Esperar a que se genere el QR
            setTimeout(() => {
                const canvas = container.querySelector('canvas');
                if (canvas) {
                    // Convertir canvas a imagen
                    canvas.toBlob((blob) => {
                        const url = URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = `QR-Ticket-Mega-${tituloMegaEvento.replace(/[^a-z0-9]/gi, '_')}-${ticketCodigo.substring(0, 8)}.png`;
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                        URL.revokeObjectURL(url);
                            
                            // Mostrar mensaje de éxito
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡QR descargado!',
                                    text: 'El código QR del ticket se ha descargado correctamente',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            }
                        });
                    } else {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error al generar el código QR'
                    });
                } else {
                    alert('Error al generar el código QR');
                        }
                }
                document.body.removeChild(container);
            }, 500);
        } catch (e) {
            console.error('Error generando QR:', e);
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al generar el código QR. Por favor, intenta nuevamente.'
                    });
                } else {
            alert('Error al generar el código QR. Por favor, intenta nuevamente.');
                }
        }
    };

    if (typeof QRCode === 'undefined') {
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js';
        script.onload = generarYDescargarQR;
        script.onerror = () => {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo cargar la librería de QR. Por favor, recarga la página e intenta nuevamente.'
                    });
                } else {
            alert('No se pudo cargar la librería de QR. Por favor, recarga la página e intenta nuevamente.');
                }
        };
        document.head.appendChild(script);
    } else {
        generarYDescargarQR();
        }

    } catch (error) {
        console.error('Error descargando QR:', error);
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al procesar la descarga. Por favor, intenta nuevamente.'
            });
        } else {
            alert('Error al procesar la descarga. Por favor, intenta nuevamente.');
        }
    }
}

/**
 * Copiar código del ticket al portapapeles
 */
function copiarCodigoTicket(ticketCodigo) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(ticketCodigo).then(() => {
            // Mostrar notificación de éxito
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: '¡Código copiado!',
                    text: 'El código del ticket ha sido copiado al portapapeles',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                alert('Código copiado al portapapeles: ' + ticketCodigo);
            }
        }).catch(err => {
            console.error('Error copiando:', err);
            // Fallback: usar método antiguo
            copiarCodigoFallback(ticketCodigo);
        });
    } else {
        // Fallback para navegadores antiguos
        copiarCodigoFallback(ticketCodigo);
    }
}

/**
 * Método fallback para copiar código
 */
function copiarCodigoFallback(ticketCodigo) {
    const textArea = document.createElement('textarea');
    textArea.value = ticketCodigo;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    try {
        document.execCommand('copy');
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: '¡Código copiado!',
                text: 'El código del ticket ha sido copiado al portapapeles',
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            alert('Código copiado al portapapeles: ' + ticketCodigo);
        }
    } catch (err) {
        console.error('Error copiando:', err);
        alert('No se pudo copiar automáticamente. Código: ' + ticketCodigo);
    }
    document.body.removeChild(textArea);
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

/**
 * Registrar asistencia de mega evento desde Mis Participaciones
 */
async function registrarAsistenciaMegaDesdeMisParticipaciones(megaEventoId) {
    const token = localStorage.getItem('token');
    if (!token) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Sesión Expirada',
                text: 'Debes iniciar sesión para registrar asistencia',
                confirmButtonText: 'Ir al Login'
            }).then(() => {
                window.location.href = '/login';
            });
        } else {
            alert('Debes iniciar sesión para registrar asistencia');
            window.location.href = '/login';
        }
        return;
    }

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Registrando asistencia...',
            text: 'Por favor espera',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }

    try {
        const res = await fetch(`${API_BASE_URL}/api/mega-eventos/${megaEventoId}/marcar-asistencia`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        const data = await res.json();

        if (data.success) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: '¡Asistencia registrada!',
                    text: 'Tu asistencia ha sido registrada correctamente.',
                    confirmButtonText: 'Aceptar'
                }).then(() => {
                    location.reload();
                });
            } else {
                alert('¡Asistencia registrada correctamente!');
                location.reload();
            }
        } else {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.error || 'Error al registrar asistencia'
                });
            } else {
                alert(data.error || 'Error al registrar asistencia');
            }
        }
    } catch (error) {
        console.error('Error:', error);
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error de conexión',
                text: 'No se pudo conectar con el servidor. Por favor, intenta nuevamente.'
            });
        } else {
            alert('Error de conexión al registrar asistencia');
        }
    }
}

/**
 * Registrar asistencia desde Mis Participaciones
 */
async function registrarAsistenciaDesdeMisParticipaciones(eventoId, ticketCodigo) {
    const token = localStorage.getItem('token');
    if (!token) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Sesión Expirada',
                text: 'Debes iniciar sesión para registrar asistencia',
                confirmButtonText: 'Ir al Login'
            }).then(() => {
                window.location.href = '/login';
            });
        } else {
            alert('Debes iniciar sesión para registrar asistencia');
            window.location.href = '/login';
        }
        return;
    }

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Registrando asistencia...',
            text: 'Por favor espera',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }

    try {
        const res = await fetch(`${API_BASE_URL}/api/eventos/${eventoId}/marcar-asistencia`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                ticket_codigo: ticketCodigo || null
            })
        });

        const data = await res.json();

        if (data.success) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: '¡Asistencia registrada!',
                    text: 'Tu asistencia ha sido registrada correctamente.',
                    confirmButtonText: 'Aceptar'
                }).then(() => {
                    location.reload();
                });
            } else {
                alert('¡Asistencia registrada correctamente!');
                location.reload();
            }
        } else {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.error || 'Error al registrar asistencia'
                });
            } else {
                alert(data.error || 'Error al registrar asistencia');
            }
        }
    } catch (error) {
        console.error('Error:', error);
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error de conexión',
                text: 'No se pudo conectar con el servidor. Por favor, intenta nuevamente.'
            });
        } else {
            alert('Error de conexión al registrar asistencia');
        }
    }
}
</script>
@endpush

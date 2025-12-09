@extends('layouts.adminlte-empresa')

@section('page_title', 'Mis Eventos')

@section('content_body')

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
                            Mis Eventos (Colaboradores y Patrocinadores)
                        </h3>
                        <p class="text-white mb-0" style="opacity: 0.95; font-size: 1rem;">
                            Eventos asignados por ONGs donde participas como colaboradora o patrocinadora
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-2 text-right d-none d-md-block">
                <i class="far fa-calendar-alt" style="font-size: 4.5rem; color: rgba(255,255,255,0.15);"></i>
            </div>
        </div>
    </div>
</div>

<!-- Botones de acción -->
<div class="d-flex justify-content-end mb-4">
    <a href="/empresa/eventos/disponibles" class="btn mr-2" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); color: white; border: none; border-radius: 8px; padding: 0.6rem 1.5rem; font-weight: 600; transition: all 0.3s;">
        <i class="far fa-plus mr-2"></i> Ver Eventos Disponibles
    </a>
    <button class="btn btn-sm" id="btnRefresh" title="Actualizar lista" style="background: #0C2B44; color: white; border: none; border-radius: 8px; padding: 0.5rem 1rem; font-weight: 500;">
        <i class="far fa-sync-alt mr-2"></i> Actualizar
    </button>
</div>

<!-- Filtros y Búsqueda con diseño mejorado -->
<div class="card mb-4 shadow-sm" style="border-radius: 12px; border: 1px solid #F5F5F5;">
    <div class="card-header bg-white border-0" style="border-radius: 12px 12px 0 0;">
        <h5 class="mb-0" style="color: #0C2B44; font-weight: 600;">
            <i class="far fa-sliders-h mr-2" style="color: #00A36C;"></i>Filtros de Búsqueda
        </h5>
    </div>
    <div class="card-body" style="padding: 1.5rem;">
        <div class="row">
            <div class="col-md-6 mb-3 mb-md-0">
                <label for="filtroEstado" class="form-label font-weight-bold" style="color: #0C2B44; font-weight: 600; margin-bottom: 0.75rem;">
                    <i class="far fa-info-circle mr-2" style="color: #00A36C;"></i>Estado
                </label>
                <select id="filtroEstado" class="form-control" style="border-radius: 8px; padding: 0.75rem; border: 1px solid #e9ecef;">
                    <option value="todos">Todos los estados</option>
                    <option value="borrador">Borrador</option>
                    <option value="publicado">Publicado</option>
                    <option value="cancelado">Cancelado</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="buscador" class="form-label font-weight-bold" style="color: #0C2B44; font-weight: 600; margin-bottom: 0.75rem;">
                    <i class="far fa-search mr-2" style="color: #00A36C;"></i>Buscar
                </label>
                <div class="input-group">
                    <input type="text" id="buscador" class="form-control" placeholder="Buscar por título o descripción..." 
                           style="border-radius: 8px 0 0 8px; padding: 0.75rem; border: 1px solid #e9ecef; border-right: none;">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" id="btnLimpiar" 
                                style="border-radius: 0 8px 8px 0; border: 1px solid #e9ecef; border-left: none; padding: 0.75rem 1rem;">
                            <i class="far fa-times-circle"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row" id="eventosContainer">
    <p class="text-muted px-3">Cargando eventos...</p>
</div>

@stop

@push('js')
<script src="{{ asset('assets/js/config.js') }}"></script>
<script>
let filtrosEmpresa = {
    estado: 'todos',
    buscar: ''
};

let todosLosEventos = [];

async function cargarEventosEmpresa() {
    const token = localStorage.getItem("token");
    const cont = document.getElementById("eventosContainer");

    if (!token) {
        cont.innerHTML = `<div class="alert alert-warning">
            <p>Debes iniciar sesión para ver los eventos.</p>
            <a href="/login" class="btn btn-primary">Iniciar sesión</a>
        </div>`;
        return;
    }

    cont.innerHTML = '<div class="col-12 text-center py-3"><div class="spinner-border" role="status" style="color: #00A36C; width: 3rem; height: 3rem;"><span class="sr-only">Cargando...</span></div><p class="mt-2 text-muted">Cargando eventos...</p></div>';

    try {
        // Usar el nuevo endpoint de empresas participantes
        const url = `${API_BASE_URL}/api/empresas/mis-eventos`;

        const res = await fetch(url, {
            method: 'GET',
            headers: { 
                "Authorization": `Bearer ${token}`,
                "Accept": "application/json",
                "Content-Type": "application/json"
            }
        });

        if (!res.ok) {
            const errorData = await res.json().catch(() => ({}));
            cont.innerHTML = `<div class="alert alert-danger">
                <p>Error al cargar eventos (${res.status})</p>
                <small>${errorData.error || 'Error del servidor'}</small>
            </div>`;
            return;
        }

        const data = await res.json();
        console.log("Datos recibidos:", data);

        if (!data.success) {
            cont.innerHTML = `<div class="alert alert-danger">
                <p>Error: ${data.error || 'Error desconocido'}</p>
            </div>`;
            return;
        }

        // Obtener eventos de las participaciones
        let eventosColaboradores = data.eventos || [];
            
        // Aplicar filtros locales
        if (filtrosEmpresa.estado !== 'todos') {
            eventosColaboradores = eventosColaboradores.filter(item => {
                const evento = item.evento;
                if (!evento) return false;
                return evento.estado === filtrosEmpresa.estado;
        });
        }

        if (filtrosEmpresa.buscar.trim() !== '') {
            const buscarLower = filtrosEmpresa.buscar.toLowerCase();
            eventosColaboradores = eventosColaboradores.filter(item => {
                const evento = item.evento;
                if (!evento) return false;
                const titulo = (evento.titulo || '').toLowerCase();
                const descripcion = (evento.descripcion || '').toLowerCase();
                return titulo.includes(buscarLower) || descripcion.includes(buscarLower);
            });
        }

            if (eventosColaboradores.length === 0) {
                cont.innerHTML = `<div class="alert text-center" style="background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 12px; padding: 3rem;">
                    <i class="far fa-info-circle fa-3x mb-3" style="color: #00A36C;"></i>
                    <h5 style="color: #0C2B44; font-weight: 600;">No tienes eventos asignados</h5>
                    <p class="mb-2" style="color: #6c757d;">Aún no has sido asignada como empresa colaboradora o patrocinadora en ningún evento.</p>
                    <p class="mb-0"><small class="text-muted">Cuando una ONG te asigne como colaboradora o patrocinadora, recibirás una notificación y el evento aparecerá aquí automáticamente.</small></p>
                    <a href="/empresa/eventos/disponibles" class="btn mt-3" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); color: white; border: none; border-radius: 8px; padding: 0.6rem 1.5rem; font-weight: 600;">
                        <i class="far fa-search mr-2"></i> Ver Eventos Disponibles
                    </a>
            </div>`;
            return;
        }

        cont.innerHTML = "";

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

        eventosColaboradores.forEach(item => {
            const e = item.evento;
            const participacion = item;
            
            // Validar que el evento existe
            if (!e) {
                console.warn('Evento no encontrado para participación:', item.id);
                return;
            }
            // Función helper para formatear fechas desde PostgreSQL sin conversión de zona horaria
            const formatearFechaPostgreSQL = (fechaStr) => {
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
            };
            
            const fechaInicio = formatearFechaPostgreSQL(e.fecha_inicio);

            // Formatear fecha límite de inscripción
            let fechaLimiteHTML = '';
            if (e.fecha_limite_inscripcion) {
                const fechaLimite = new Date(e.fecha_limite_inscripcion);
                const fechaFormateada = fechaLimite.toLocaleDateString('es-ES', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
                const ahora = new Date();
                const diasRestantes = Math.ceil((fechaLimite - ahora) / (1000 * 60 * 60 * 24));
                
                fechaLimiteHTML = `
                    <div class="mb-3 p-3" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); border-radius: 10px; color: white;">
                        <div class="d-flex align-items-center mb-2">
                            <i class="far fa-calendar-times mr-2" style="font-size: 1.1rem; opacity: 0.9;"></i>
                            <span style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Cierre de Inscripción</span>
                        </div>
                        <div style="font-size: 1rem; font-weight: 700; margin-bottom: 0.25rem;">${fechaFormateada}</div>
                        ${diasRestantes >= 0 
                            ? `<div style="font-size: 0.8rem; opacity: 0.9;">
                                <i class="far fa-clock mr-1"></i>
                                ${diasRestantes === 0 ? 'Último día' : diasRestantes === 1 ? '1 día restante' : `${diasRestantes} días restantes`}
                               </div>`
                            : `<div style="font-size: 0.8rem; opacity: 0.9; color: #ffc107;">
                                <i class="far fa-exclamation-triangle mr-1"></i>
                                Inscripción cerrada
                               </div>`
                        }
                    </div>
                `;
            }

            // Procesar imágenes
            let imagenes = [];
            if (Array.isArray(e.imagenes) && e.imagenes.length > 0) {
                imagenes = e.imagenes.filter(img => img && typeof img === 'string' && img.trim().length > 0);
            } else if (typeof e.imagenes === 'string' && e.imagenes.trim()) {
                try {
                    const parsed = JSON.parse(e.imagenes);
                    if (Array.isArray(parsed)) {
                        imagenes = parsed.filter(img => img && typeof img === 'string' && img.trim().length > 0);
                    }
                } catch (err) {
                    console.warn('Error parseando imágenes:', err);
                }
            }

            const imagenPrincipal = imagenes.length > 0 ? buildImageUrl(imagenes[0]) : null;

            // Formatear fecha de finalización para el overlay (día y mes)
            let fechaOverlay = '';
            if (e.fecha_fin) {
                const fechaFin = new Date(e.fecha_fin);
                const dia = fechaFin.getDate();
                const meses = ['ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'];
                const mes = meses[fechaFin.getMonth()];
                fechaOverlay = `
                    <div class="position-absolute" style="top: 12px; left: 12px; background: white; border-radius: 8px; padding: 0.5rem 0.75rem; box-shadow: 0 2px 8px rgba(0,0,0,0.15); z-index: 10;">
                        <div style="font-size: 1.5rem; font-weight: 700; color: #0C2B44; line-height: 1;">${dia}</div>
                        <div style="font-size: 0.75rem; font-weight: 600; color: #00A36C; line-height: 1; margin-top: 2px;">${mes}</div>
                    </div>
                `;
            }

            // Estado badge con nueva paleta
            const estadoBadges = {
                'borrador': '<span class="badge" style="background: #6c757d; color: white; font-size: 0.75rem; padding: 0.4em 0.8em; border-radius: 20px; font-weight: 500;">Borrador</span>',
                'publicado': '<span class="badge" style="background: #00A36C; color: white; font-size: 0.75rem; padding: 0.4em 0.8em; border-radius: 20px; font-weight: 500;">Publicado</span>',
                'cancelado': '<span class="badge" style="background: #dc3545; color: white; font-size: 0.75rem; padding: 0.4em 0.8em; border-radius: 20px; font-weight: 500;">Cancelado</span>'
            };
            const estadoBadge = estadoBadges[e.estado] || '<span class="badge" style="background: #6c757d; color: white; font-size: 0.75rem; padding: 0.4em 0.8em; border-radius: 20px; font-weight: 500;">' + (e.estado || 'N/A') + '</span>';

            // Crear card con diseño minimalista
            const cardDiv = document.createElement('div');
            cardDiv.className = 'col-md-4 mb-4';
            
            cardDiv.innerHTML = `
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; overflow: hidden; transition: transform 0.2s, box-shadow 0.2s;">
                    ${imagenPrincipal 
                        ? `<a href="/empresa/eventos/${e.id}/detalle" style="text-decoration: none; display: block;">
                            <div class="position-relative" style="height: 200px; overflow: hidden; background: #f8f9fa; cursor: pointer;">
                                <img src="${imagenPrincipal}" alt="${e.titulo || 'Evento'}" class="w-100 h-100" style="object-fit: cover; transition: transform 0.3s;" 
                                 onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\\'http://www.w3.org/2000/svg\\' width=\\'400\\' height=\\'200\\'%3E%3Crect fill=\\'%23f8f9fa\\' width=\\'400\\' height=\\'200\\'/%3E%3Ctext x=\\'50%25\\' y=\\'50%25\\' text-anchor=\\'middle\\' dy=\\'.3em\\' fill=\\'%23adb5bd\\' font-family=\\'Arial\\' font-size=\\'14\\'%3EImagen no disponible%3C/text%3E%3C/svg%3E'; this.style.objectFit='contain'; this.style.padding='20px';">
                            ${fechaOverlay}
                            <div class="position-absolute" style="top: 12px; right: 12px; z-index: 10; pointer-events: none;">
                                ${estadoBadge}
                            </div>
                           </div>
                           </a>`
                        : `<a href="/empresa/eventos/${e.id}/detalle" style="text-decoration: none; display: block;">
                            <div class="position-relative" style="height: 200px; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); display: flex; align-items: center; justify-content: center; cursor: pointer;">
                            <i class="far fa-calendar fa-4x text-white" style="opacity: 0.7;"></i>
                            ${fechaOverlay}
                            <div class="position-absolute" style="top: 12px; right: 12px; z-index: 10; pointer-events: none;">
                                ${estadoBadge}
                            </div>
                           </div>
                           </a>`
                    }
                    <div class="card-body p-4">
                        <h5 class="mb-2" style="font-size: 1.1rem; font-weight: 600; color: #2c3e50;">${e.titulo || 'Sin título'}</h5>
                        <p class="text-muted mb-3" style="font-size: 0.9rem; line-height: 1.5; color: #6c757d; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            ${e.descripcion || 'Sin descripción'}
                        </p>
                        ${e.ciudad ? `<p class="text-muted mb-2" style="font-size: 0.85rem;"><i class="far fa-map-marker-alt mr-1" style="color: #00A36C;"></i> ${e.ciudad}</p>` : ''}
                        ${e.ong && e.ong.nombre_ong ? `<p class="text-muted mb-2" style="font-size: 0.85rem;"><i class="far fa-building mr-1" style="color: #00A36C;"></i> Organizado por: <strong style="color: #0C2B44;">${e.ong.nombre_ong}</strong></p>` : ''}
                        <div class="mb-3 d-flex align-items-center" style="color: #6c757d; font-size: 0.85rem;">
                            <i class="far fa-calendar-alt mr-2" style="color: #00A36C;"></i>
                            <span>${fechaInicio}</span>
                        </div>
                        ${fechaLimiteHTML}
                        ${e.tipo_evento ? `<span class="badge mb-2" style="font-size: 0.75rem; background: #0C2B44; color: white; padding: 0.4em 0.8em; border-radius: 20px; font-weight: 500;">${e.tipo_evento}</span>` : ''}
                        <div class="mb-2">
                            ${participacion.tipo_relacion === 'patrocinadora' 
                                ? `<span class="badge mr-1" style="font-size: 0.75rem; background-color: #0C2B44; color: white; padding: 0.4em 0.8em; border-radius: 20px; font-weight: 500;"><i class="far fa-handshake"></i> Patrocinadora</span>`
                                : participacion.estado_participacion === 'confirmada' 
                                    ? `<span class="badge mr-1" style="font-size: 0.75rem; background-color: #00A36C; color: white; padding: 0.4em 0.8em; border-radius: 20px; font-weight: 500;"><i class="far fa-check-circle"></i> Confirmada</span>`
                                    : `<span class="badge mr-1" style="font-size: 0.75rem; background-color: #ffc107; color: #333; padding: 0.4em 0.8em; border-radius: 20px; font-weight: 500;"><i class="far fa-clock"></i> Pendiente</span>`
                            }
                            ${participacion.tipo_relacion === 'colaboradora' 
                                ? `<span class="badge" style="font-size: 0.75rem; background-color: #00A36C; color: white; padding: 0.4em 0.8em; border-radius: 20px; font-weight: 500;"><i class="far fa-handshake"></i> Colaboradora</span>`
                                : ''
                            }
                        </div>
                        ${participacion.fecha_asignacion ? `<p class="text-muted mb-2" style="font-size: 0.8rem;"><i class="far fa-calendar-check mr-1" style="color: #00A36C;"></i> Asignado el: ${new Date(participacion.fecha_asignacion).toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' })}</p>` : ''}
                        ${participacion.tipo_colaboracion ? `<p class="text-muted mb-2" style="font-size: 0.85rem;"><i class="far fa-tag mr-1" style="color: #00A36C;"></i> ${participacion.tipo_colaboracion}</p>` : ''}
                        <a href="/empresa/eventos/${e.id}/detalle" class="btn btn-sm btn-block mt-auto" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); color: white; border: none; border-radius: 8px; padding: 0.5em 1.2em; font-weight: 500; transition: all 0.2s;">
                            <i class="far fa-eye mr-2"></i>Ver Detalles
                        </a>
                    </div>
                </div>
            `;
            
            // Agregar efecto hover
            const card = cardDiv.querySelector('.card');
            card.onmouseenter = function() {
                this.style.transform = 'translateY(-4px)';
                this.style.boxShadow = '0 8px 16px rgba(0,0,0,0.1)';
            };
            card.onmouseleave = function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '0 1px 3px rgba(0,0,0,0.1)';
            };
            
            cont.appendChild(cardDiv);
        });

    } catch (error) {
        console.error("Error al cargar eventos:", error);
        cont.innerHTML = `<div class="alert alert-danger">
            <p>Error de conexión al cargar eventos.</p>
            <small>${error.message}</small>
        </div>`;
    }
}

document.addEventListener("DOMContentLoaded", async () => {
    // Cargar eventos iniciales
    await cargarEventosEmpresa();

    // Event listeners para filtros
    document.getElementById('filtroEstado').addEventListener('change', function() {
        filtrosEmpresa.estado = this.value;
        cargarEventosEmpresa();
    });

    // Búsqueda con debounce
    let searchTimeout;
    document.getElementById('buscador').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            filtrosEmpresa.buscar = this.value;
            cargarEventosEmpresa();
        }, 500);
    });

    // Botón limpiar
    document.getElementById('btnLimpiar').addEventListener('click', function() {
        document.getElementById('buscador').value = '';
        document.getElementById('filtroEstado').value = 'todos';
        filtrosEmpresa = {
            estado: 'todos',
            buscar: ''
        };
        cargarEventosEmpresa();
    });

    // Botón actualizar
    const btnRefresh = document.getElementById('btnRefresh');
    if (btnRefresh) {
        btnRefresh.addEventListener('click', async function() {
            const icon = this.querySelector('i');
            icon.classList.add('fa-spin');
            await cargarEventosEmpresa();
            icon.classList.remove('fa-spin');
        });
    }

    // Actualización automática cada 30 segundos
    setInterval(async () => {
        await cargarEventosEmpresa();
    }, 30000); // 30 segundos
});
</script>
@endpush


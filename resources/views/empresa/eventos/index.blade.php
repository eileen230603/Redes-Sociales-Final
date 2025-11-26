@extends('layouts.adminlte-empresa')

@section('page_title', 'Mis Eventos')

@section('content_body')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="text-primary mb-1"><i class="fas fa-calendar-check"></i> Mis Eventos (Colaboradores y Patrocinadores)</h4>
        <p class="text-muted mb-0" style="font-size: 0.9rem;"><i class="fas fa-info-circle"></i> Eventos asignados por ONGs donde participas como colaboradora o patrocinadora</p>
    </div>
    <div>
        <a href="/empresa/eventos/disponibles" class="btn btn-success mr-2">
            <i class="fas fa-plus"></i> Ver Eventos Disponibles
        </a>
        <button class="btn btn-sm btn-outline-primary" id="btnRefresh" title="Actualizar lista">
            <i class="fas fa-sync-alt"></i> Actualizar
        </button>
    </div>
</div>

<!-- Filtros y Búsqueda -->
<div class="card mb-4 shadow-sm">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3 mb-md-0">
                <label for="filtroEstado" class="form-label"><i class="fas fa-info-circle mr-2"></i>Estado</label>
                <select id="filtroEstado" class="form-control">
                    <option value="todos">Todos los estados</option>
                    <option value="borrador">Borrador</option>
                    <option value="publicado">Publicado</option>
                    <option value="cancelado">Cancelado</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="buscador" class="form-label"><i class="fas fa-search mr-2"></i>Buscar</label>
                <div class="input-group">
                    <input type="text" id="buscador" class="form-control" placeholder="Buscar por título o descripción...">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" id="btnLimpiar">
                            <i class="fas fa-times"></i>
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

@section('js')
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

    cont.innerHTML = '<div class="col-12 text-center py-3"><div class="spinner-border text-primary" role="status"><span class="sr-only">Cargando...</span></div><p class="mt-2 text-muted">Cargando eventos...</p></div>';

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
                cont.innerHTML = `<div class="alert alert-info text-center">
                    <i class="fas fa-info-circle fa-3x mb-3 text-primary"></i>
                    <h5>No tienes eventos asignados</h5>
                    <p class="mb-2">Aún no has sido asignada como empresa colaboradora o patrocinadora en ningún evento.</p>
                    <p class="mb-0"><small class="text-muted">Cuando una ONG te asigne como colaboradora o patrocinadora, recibirás una notificación y el evento aparecerá aquí automáticamente.</small></p>
                    <a href="/empresa/eventos/disponibles" class="btn btn-primary mt-3">
                        <i class="fas fa-search"></i> Ver Eventos Disponibles
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
            const fechaInicio = e.fecha_inicio ? new Date(e.fecha_inicio).toLocaleDateString('es-ES', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            }) : 'Fecha no especificada';

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

            // Estado badge
            const estadoBadges = {
                'borrador': '<span class="badge badge-secondary">Borrador</span>',
                'publicado': '<span class="badge badge-success">Publicado</span>',
                'cancelado': '<span class="badge badge-danger">Cancelado</span>'
            };
            const estadoBadge = estadoBadges[e.estado] || '<span class="badge badge-secondary">' + (e.estado || 'N/A') + '</span>';

            // Crear card con diseño minimalista
            const cardDiv = document.createElement('div');
            cardDiv.className = 'col-md-4 mb-4';
            
            cardDiv.innerHTML = `
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; overflow: hidden; transition: transform 0.2s, box-shadow 0.2s;">
                    ${imagenPrincipal 
                        ? `<div class="position-relative" style="height: 200px; overflow: hidden; background: #f8f9fa;">
                            <img src="${imagenPrincipal}" alt="${e.titulo}" class="w-100 h-100" style="object-fit: cover;" 
                                 onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\\'http://www.w3.org/2000/svg\\' width=\\'400\\' height=\\'200\\'%3E%3Crect fill=\\'%23f8f9fa\\' width=\\'400\\' height=\\'200\\'/%3E%3Ctext x=\\'50%25\\' y=\\'50%25\\' text-anchor=\\'middle\\' dy=\\'.3em\\' fill=\\'%23adb5bd\\' font-family=\\'Arial\\' font-size=\\'14\\'%3EImagen no disponible%3C/text%3E%3C/svg%3E'; this.style.objectFit='contain'; this.style.padding='20px';">
                            <div class="position-absolute" style="top: 12px; left: 12px; right: 12px; display: flex; justify-content: space-between; align-items: flex-start;">
                                <span class="badge" style="background: rgba(74, 144, 226, 0.9); color: white; font-size: 0.75rem; padding: 0.4em 0.8em; border-radius: 20px; font-weight: 500;">Evento</span>
                                ${estadoBadge}
                            </div>
                           </div>`
                        : `<div class="position-relative" style="height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-calendar fa-4x text-white" style="opacity: 0.7;"></i>
                            <div class="position-absolute" style="top: 12px; left: 12px; right: 12px; display: flex; justify-content: space-between; align-items: flex-start;">
                                <span class="badge" style="background: rgba(74, 144, 226, 0.9); color: white; font-size: 0.75rem; padding: 0.4em 0.8em; border-radius: 20px; font-weight: 500;">Evento</span>
                                ${estadoBadge}
                            </div>
                           </div>`
                    }
                    <div class="card-body p-4">
                        <h5 class="mb-2" style="font-size: 1.1rem; font-weight: 600; color: #2c3e50;">${e.titulo || 'Sin título'}</h5>
                        <p class="text-muted mb-3" style="font-size: 0.9rem; line-height: 1.5; color: #6c757d; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            ${e.descripcion || 'Sin descripción'}
                        </p>
                        ${e.ciudad ? `<p class="text-muted mb-2" style="font-size: 0.85rem;"><i class="fas fa-map-marker-alt mr-1"></i> ${e.ciudad}</p>` : ''}
                        ${e.ong && e.ong.nombre_ong ? `<p class="text-muted mb-2" style="font-size: 0.85rem;"><i class="fas fa-building mr-1 text-primary"></i> Organizado por: <strong>${e.ong.nombre_ong}</strong></p>` : ''}
                        <div class="mb-3 d-flex align-items-center" style="color: #6c757d; font-size: 0.85rem;">
                            <i class="far fa-calendar-alt mr-2"></i>
                            <span>${fechaInicio}</span>
                        </div>
                        ${e.tipo_evento ? `<span class="badge badge-info mb-2" style="font-size: 0.75rem;">${e.tipo_evento}</span>` : ''}
                        <div class="mb-2">
                            ${participacion.tipo_relacion === 'patrocinadora' 
                                ? `<span class="badge badge-primary mr-1" style="font-size: 0.75rem; background-color: #007bff;"><i class="fas fa-handshake"></i> Patrocinadora</span>`
                                : participacion.estado_participacion === 'confirmada' 
                                    ? `<span class="badge badge-success mr-1" style="font-size: 0.75rem;"><i class="fas fa-check-circle"></i> Confirmada</span>`
                                    : `<span class="badge badge-warning mr-1" style="font-size: 0.75rem;"><i class="fas fa-clock"></i> Pendiente</span>`
                            }
                            ${participacion.tipo_relacion === 'colaboradora' 
                                ? `<span class="badge badge-success" style="font-size: 0.75rem; background-color: #28a745;"><i class="fas fa-handshake"></i> Colaboradora</span>`
                                : ''
                            }
                        </div>
                        ${participacion.fecha_asignacion ? `<p class="text-muted mb-2" style="font-size: 0.8rem;"><i class="fas fa-calendar-check mr-1"></i> Asignado el: ${new Date(participacion.fecha_asignacion).toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' })}</p>` : ''}
                        ${participacion.tipo_colaboracion ? `<p class="text-muted mb-2" style="font-size: 0.85rem;"><i class="fas fa-tag mr-1"></i> ${participacion.tipo_colaboracion}</p>` : ''}
                        <a href="/empresa/eventos/${e.id}/detalle" class="btn btn-sm btn-block mt-auto" style="background: #667eea; color: white; border: none; border-radius: 8px; padding: 0.5em 1.2em; font-weight: 500; transition: all 0.2s;">
                            Ver Detalles
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
@stop


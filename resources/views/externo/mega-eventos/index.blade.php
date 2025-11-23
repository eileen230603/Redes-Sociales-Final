@extends('layouts.adminlte-externo')

@section('page_title', 'Mega Eventos Disponibles')

@section('content_body')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-primary"><i class="fas fa-star"></i> Mega Eventos Disponibles</h4>
</div>

<!-- Filtros y Búsqueda -->
<div class="card mb-4 shadow-sm">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 mb-3 mb-md-0">
                <label for="filtroCategoria" class="form-label"><i class="fas fa-filter mr-2"></i>Categoría</label>
                <select id="filtroCategoria" class="form-control">
                    <option value="todos">Todas las categorías</option>
                    <option value="social">Social</option>
                    <option value="cultural">Cultural</option>
                    <option value="deportivo">Deportivo</option>
                    <option value="educativo">Educativo</option>
                    <option value="benefico">Benéfico</option>
                    <option value="ambiental">Ambiental</option>
                    <option value="otro">Otro</option>
                </select>
            </div>
            <div class="col-md-8">
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

<div class="row" id="listaMegaEventos">
    <p class="text-muted px-3">Cargando mega eventos...</p>
</div>

@stop

@section('css')
<style>
    .mega-evento-inscrito {
        position: relative;
    }
    
    .mega-evento-inscrito::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #ffc107 0%, #ff9800 100%);
        z-index: 1;
    }
    
    .mega-evento-inscrito .card-body {
        background: linear-gradient(to bottom, rgba(255, 193, 7, 0.05) 0%, rgba(248, 249, 250, 1) 10%);
    }
    
    .mega-evento-inscrito:hover {
        box-shadow: 0 8px 16px rgba(255, 193, 7, 0.2) !important;
    }
</style>
@endsection

@section('js')
<script src="{{ asset('assets/js/config.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let filtrosMegaEventos = {
    categoria: 'todos',
    buscar: ''
};

async function cargarMegaEventos() {
    const token = localStorage.getItem("token");
    const cont = document.getElementById("listaMegaEventos");

    if (!token) {
        cont.innerHTML = `<div class="alert alert-warning">
            <p>Debes iniciar sesión para ver los mega eventos.</p>
            <a href="/login" class="btn btn-primary">Iniciar sesión</a>
        </div>`;
        return;
    }

    cont.innerHTML = '<div class="col-12 text-center py-3"><div class="spinner-border text-primary" role="status"><span class="sr-only">Cargando...</span></div><p class="mt-2 text-muted">Cargando mega eventos...</p></div>';

    try {
        const params = new URLSearchParams();
        if (filtrosMegaEventos.categoria !== 'todos') {
            params.append('categoria', filtrosMegaEventos.categoria);
        }
        if (filtrosMegaEventos.buscar.trim() !== '') {
            params.append('buscar', filtrosMegaEventos.buscar.trim());
        }

        const url = `${API_BASE_URL}/api/mega-eventos/publicos${params.toString() ? '?' + params.toString() : ''}`;

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
                <p>Error al cargar mega eventos (${res.status})</p>
                <small>${errorData.error || 'Error del servidor'}</small>
            </div>`;
            return;
        }

        const data = await res.json();

        if (!data.success) {
            cont.innerHTML = `<div class="alert alert-danger">
                <p>Error: ${data.error || 'Error desconocido'}</p>
            </div>`;
            return;
        }

        if (!data.mega_eventos || data.mega_eventos.length === 0) {
            cont.innerHTML = `<div class="alert alert-info">
                <p class="mb-0">No hay mega eventos públicos disponibles en este momento.</p>
            </div>`;
            return;
        }

        cont.innerHTML = "";

        // Cargar participaciones en mega eventos
        const participacionesPromises = data.mega_eventos.map(mega => 
            fetch(`${API_BASE_URL}/api/mega-eventos/${mega.mega_evento_id}/verificar-participacion`, {
                headers: { 
                    "Authorization": `Bearer ${token}`,
                    "Accept": "application/json"
                }
            }).then(r => r.json()).catch(() => ({ success: false, participando: false }))
        );

        const participacionesData = await Promise.all(participacionesPromises);
        const participacionesMap = {};
        data.mega_eventos.forEach((mega, index) => {
            participacionesMap[mega.mega_evento_id] = participacionesData[index]?.participando || false;
        });

        function buildImageUrl(imgUrl) {
            if (!imgUrl || imgUrl.trim() === '') return null;
            if (imgUrl.startsWith('http://') || imgUrl.startsWith('https://')) return imgUrl;
            if (imgUrl.startsWith('/storage/')) return `${window.location.origin}${imgUrl}`;
            if (imgUrl.startsWith('storage/')) return `${window.location.origin}/${imgUrl}`;
            return `${window.location.origin}/storage/${imgUrl}`;
        }

        data.mega_eventos.forEach(mega => {
            const fechaInicio = mega.fecha_inicio ? new Date(mega.fecha_inicio).toLocaleDateString('es-ES', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            }) : 'Fecha no especificada';

            const estaParticipando = participacionesMap[mega.mega_evento_id] || false;

            let imagenes = Array.isArray(mega.imagenes) ? mega.imagenes.filter(img => img && typeof img === 'string' && img.trim().length > 0) : [];
            const imagenPrincipal = imagenes.length > 0 ? buildImageUrl(imagenes[0]) : null;

            const cardDiv = document.createElement('div');
            cardDiv.className = 'col-md-4 mb-4';
            
            cardDiv.innerHTML = `
                <div class="card border-0 shadow-sm h-100 ${estaParticipando ? 'mega-evento-inscrito' : ''}" style="border-radius: 12px; overflow: hidden; transition: transform 0.2s, box-shadow 0.2s; ${estaParticipando ? 'background: #f8f9fa; border: 2px solid #ffc107 !important;' : ''}">
                    ${imagenPrincipal 
                        ? `<div class="position-relative" style="height: 200px; overflow: hidden; background: #f8f9fa;">
                            <img src="${imagenPrincipal}" alt="${mega.titulo}" class="w-100 h-100" style="object-fit: cover;" 
                                 onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\\'http://www.w3.org/2000/svg\\' width=\\'400\\' height=\\'200\\'%3E%3Crect fill=\\'%23f8f9fa\\' width=\\'400\\' height=\\'200\\'/%3E%3Ctext x=\\'50%25\\' y=\\'50%25\\' text-anchor=\\'middle\\' dy=\\'.3em\\' fill=\\'%23adb5bd\\' font-family=\\'Arial\\' font-size=\\'14\\'%3EImagen no disponible%3C/text%3E%3C/svg%3E'; this.style.objectFit='contain'; this.style.padding='20px';">
                            <div class="position-absolute" style="top: 12px; left: 12px; right: 12px; display: flex; justify-content: space-between; align-items: flex-start;">
                                <div>
                                    <span class="badge" style="background: rgba(255, 193, 7, 0.9); color: white; font-size: 0.75rem; padding: 0.4em 0.8em; border-radius: 20px; font-weight: 500;"><i class="fas fa-star mr-1"></i>Mega Evento</span>
                                    ${estaParticipando ? '<span class="badge badge-success ml-2" style="font-size: 0.7rem; padding: 0.3em 0.6em; border-radius: 15px;"><i class="fas fa-check-circle mr-1"></i>Participando</span>' : ''}
                                </div>
                            </div>
                           </div>`
                        : `<div class="position-relative" style="height: 200px; background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-star fa-4x text-white" style="opacity: 0.7;"></i>
                            <div class="position-absolute" style="top: 12px; left: 12px; right: 12px; display: flex; justify-content: space-between; align-items: flex-start;">
                                <div>
                                    <span class="badge" style="background: rgba(255, 193, 7, 0.9); color: white; font-size: 0.75rem; padding: 0.4em 0.8em; border-radius: 20px; font-weight: 500;"><i class="fas fa-star mr-1"></i>Mega Evento</span>
                                    ${estaParticipando ? '<span class="badge badge-success ml-2" style="font-size: 0.7rem; padding: 0.3em 0.6em; border-radius: 15px;"><i class="fas fa-check-circle mr-1"></i>Participando</span>' : ''}
                                </div>
                            </div>
                           </div>`
                    }
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="mb-0" style="font-size: 1.1rem; font-weight: 600; color: #2c3e50; flex: 1;">${mega.titulo || 'Sin título'}</h5>
                            ${estaParticipando ? '<i class="fas fa-check-circle text-success ml-2" style="font-size: 1.2rem;" title="Estás participando en este mega evento"></i>' : ''}
                        </div>
                        ${estaParticipando ? `<span class="badge badge-success mb-2" style="font-size: 0.75rem; display: inline-block;">
                            <i class="fas fa-check-circle mr-1"></i>Participando (Aprobada)
                        </span>` : ''}
                        <p class="text-muted mb-3" style="font-size: 0.9rem; line-height: 1.5; color: #6c757d; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            ${mega.descripcion || 'Sin descripción'}
                        </p>
                        ${mega.ubicacion ? `<p class="text-muted mb-2" style="font-size: 0.85rem;"><i class="fas fa-map-marker-alt mr-1"></i> ${mega.ubicacion}</p>` : ''}
                        <div class="mb-3 d-flex align-items-center" style="color: #6c757d; font-size: 0.85rem;">
                            <i class="far fa-calendar-alt mr-2"></i>
                            <span>${fechaInicio}</span>
                        </div>
                        ${mega.categoria ? `<span class="badge badge-warning mb-3" style="font-size: 0.75rem;">${mega.categoria}</span>` : ''}
                        ${(() => {
                            // Detectar la ruta base según la URL actual
                            const currentPath = window.location.pathname;
                            let basePath = '/externo/mega-eventos';
                            if (currentPath.includes('/voluntario/mega-eventos')) {
                                basePath = '/voluntario/mega-eventos';
                            } else if (currentPath.includes('/empresa/mega-eventos')) {
                                basePath = '/empresa/mega-eventos';
                            }
                            return `<a href="${basePath}/${mega.mega_evento_id}/detalle" class="btn btn-sm btn-block mt-auto" style="background: ${estaParticipando ? '#28a745' : '#ffc107'}; color: white; border: none; border-radius: 8px; padding: 0.5em 1.2em; font-weight: 500; transition: all 0.2s;">
                                ${estaParticipando ? '<i class="fas fa-eye mr-1"></i> Ver Detalles' : '<i class="fas fa-info-circle mr-1"></i> Ver Detalles'}
                            </a>`;
                        })()}
                    </div>
                </div>
            `;
            
            const card = cardDiv.querySelector('.card');
            card.onmouseenter = function() {
                this.style.transform = 'translateY(-4px)';
                this.style.boxShadow = estaParticipando 
                    ? '0 8px 16px rgba(255, 193, 7, 0.3)' 
                    : '0 8px 16px rgba(0,0,0,0.1)';
            };
            card.onmouseleave = function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '0 1px 3px rgba(0,0,0,0.1)';
            };
            
            cont.appendChild(cardDiv);
        });

    } catch (error) {
        console.error("Error al cargar mega eventos:", error);
        cont.innerHTML = `<div class="alert alert-danger">
            <p>Error de conexión al cargar mega eventos.</p>
            <small>${error.message}</small>
        </div>`;
    }
}

document.addEventListener("DOMContentLoaded", async () => {
    await cargarMegaEventos();

    document.getElementById('filtroCategoria').addEventListener('change', function() {
        filtrosMegaEventos.categoria = this.value;
        cargarMegaEventos();
    });

    let searchTimeout;
    document.getElementById('buscador').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            filtrosMegaEventos.buscar = this.value;
            cargarMegaEventos();
        }, 500);
    });

    document.getElementById('btnLimpiar').addEventListener('click', function() {
        document.getElementById('buscador').value = '';
        document.getElementById('filtroCategoria').value = 'todos';
        filtrosMegaEventos = {
            categoria: 'todos',
            buscar: ''
        };
        cargarMegaEventos();
    });
});
</script>
@stop



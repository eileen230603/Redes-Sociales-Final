@extends('layouts.adminlte-empresa')

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

@section('js')
<script src="{{ asset('assets/js/config.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let filtrosMegaEventos = {
    categoria: 'todos',
    buscar: ''
};

function buildImageUrl(imgUrl) {
    if (!imgUrl || imgUrl.trim() === '') return null;
    if (imgUrl.startsWith('http://') || imgUrl.startsWith('https://')) return imgUrl;
    if (imgUrl.startsWith('/storage/')) return `${window.location.origin}${imgUrl}`;
    if (imgUrl.startsWith('storage/')) return `${window.location.origin}/${imgUrl}`;
    return `${window.location.origin}/storage/${imgUrl}`;
}

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

        data.mega_eventos.forEach(mega => {
            const fechaInicio = mega.fecha_inicio ? new Date(mega.fecha_inicio).toLocaleDateString('es-ES', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            }) : 'Fecha no especificada';

            let imagenes = Array.isArray(mega.imagenes) ? mega.imagenes.filter(img => img && typeof img === 'string' && img.trim().length > 0) : [];
            const imagenPrincipal = imagenes.length > 0 ? buildImageUrl(imagenes[0]) : null;

            const cardDiv = document.createElement('div');
            cardDiv.className = 'col-md-4 mb-4';
            
            cardDiv.innerHTML = `
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; overflow: hidden; transition: transform 0.2s, box-shadow 0.2s;">
                    ${imagenPrincipal 
                        ? `<div class="position-relative" style="height: 200px; overflow: hidden; background: #f8f9fa;">
                            <img src="${imagenPrincipal}" alt="${mega.titulo}" class="w-100 h-100" style="object-fit: cover;" 
                                 onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\\'http://www.w3.org/2000/svg\\' width=\\'400\\' height=\\'200\\'%3E%3Crect fill=\\'%23f8f9fa\\' width=\\'400\\' height=\\'200\\'/%3E%3Ctext x=\\'50%25\\' y=\\'50%25\\' text-anchor=\\'middle\\' dy=\\'.3em\\' fill=\\'%23adb5bd\\' font-family=\\'Arial\\' font-size=\\'14\\'%3EImagen no disponible%3C/text%3E%3C/svg%3E'; this.style.objectFit='contain'; this.style.padding='20px';">
                            <div class="position-absolute" style="top: 12px; left: 12px;">
                                <span class="badge" style="background: rgba(255, 193, 7, 0.9); color: white; font-size: 0.75rem; padding: 0.4em 0.8em; border-radius: 20px; font-weight: 500;"><i class="fas fa-star mr-1"></i>Mega Evento</span>
                            </div>
                           </div>`
                        : `<div class="position-relative" style="height: 200px; background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-star fa-4x text-white" style="opacity: 0.7;"></i>
                            <div class="position-absolute" style="top: 12px; left: 12px;">
                                <span class="badge" style="background: rgba(255, 193, 7, 0.9); color: white; font-size: 0.75rem; padding: 0.4em 0.8em; border-radius: 20px; font-weight: 500;"><i class="fas fa-star mr-1"></i>Mega Evento</span>
                            </div>
                           </div>`
                    }
                    <div class="card-body p-4">
                        <h5 class="mb-2" style="font-size: 1.1rem; font-weight: 600; color: #2c3e50;">${mega.titulo || 'Sin título'}</h5>
                        <p class="text-muted mb-3" style="font-size: 0.9rem; line-height: 1.5; color: #6c757d; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            ${mega.descripcion || 'Sin descripción'}
                        </p>
                        ${mega.ubicacion ? `<p class="text-muted mb-2" style="font-size: 0.85rem;"><i class="fas fa-map-marker-alt mr-1"></i> ${mega.ubicacion}</p>` : ''}
                        <div class="mb-3 d-flex align-items-center" style="color: #6c757d; font-size: 0.85rem;">
                            <i class="far fa-calendar-alt mr-2"></i>
                            <span>${fechaInicio}</span>
                        </div>
                        ${mega.categoria ? `<span class="badge badge-warning mb-3" style="font-size: 0.75rem;">${mega.categoria}</span>` : ''}
                        <a href="/empresa/mega-eventos/${mega.mega_evento_id}/detalle" class="btn btn-sm btn-block mt-auto" style="background: #17a2b8; color: white; border: none; border-radius: 8px; padding: 0.5em 1.2em; font-weight: 500; transition: all 0.2s;">
                            <i class="fas fa-hand-holding-usd mr-1"></i> Auspiciar / Ser patrocinador
                        </a>
                    </div>
                </div>
            `;
            
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


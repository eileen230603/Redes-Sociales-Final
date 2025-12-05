@extends('layouts.adminlte')

@section('page_title', 'Historial de Mega Eventos')

@section('content_body')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0" style="color: #0C2B44; font-weight: 700;">
            <i class="fas fa-history mr-2" style="color: #00A36C;"></i>Historial de Mega Eventos Finalizados
        </h3>
        <a href="{{ route('ong.mega-eventos.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left mr-2"></i> Volver a Lista de Mega Eventos
        </a>
    </div>

    <!-- Filtros y Búsqueda -->
    <div class="card mb-4">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0 text-dark">
                <i class="fas fa-filter mr-2 text-info"></i>Filtros y Búsqueda
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3 mb-md-0">
                    <label for="filtroCategoria" class="font-weight-bold text-dark">
                        <i class="fas fa-sliders-h mr-2 text-success"></i>Categoría
                    </label>
                    <select id="filtroCategoria" class="form-control">
                        <option value="todos">Todas las categorías</option>
                        <option value="cultural">Cultural</option>
                        <option value="deportivo">Deportivo</option>
                        <option value="educativo">Educativo</option>
                        <option value="social">Social</option>
                        <option value="benefico">Benéfico</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <label for="filtroFecha" class="font-weight-bold text-dark">
                        <i class="fas fa-calendar mr-2 text-warning"></i>Ordenar por
                    </label>
                    <select id="filtroFecha" class="form-control">
                        <option value="recientes">Más recientes</option>
                        <option value="antiguos">Más antiguos</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="buscador" class="font-weight-bold text-dark">
                        <i class="fas fa-search mr-2 text-primary"></i>Buscar
                    </label>
                    <div class="input-group">
                        <input type="text" id="buscador" class="form-control" placeholder="Buscar por título...">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" id="btnLimpiar">
                                <i class="fas fa-times-circle"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="megaEventosContainer" class="row">
        <p class="text-muted px-3">Cargando mega eventos finalizados...</p>
    </div>

</div>

@stop

@section('js')
{{-- Script global para icono de notificaciones --}}
<script src="{{ asset('js/notificaciones-ong.js') }}"></script>
<script src="{{ asset('assets/js/config.js') }}"></script>

<script>
// Función helper para parsear fechas correctamente
function parsearFecha(fechaStr) {
    if (!fechaStr) return null;
    try {
        if (typeof fechaStr === 'string') {
            fechaStr = fechaStr.trim();
            const mysqlPattern = /^(\d{4})-(\d{2})-(\d{2})[\sT](\d{2}):(\d{2}):(\d{2})(?:\.\d+)?(?:Z|[+-]\d{2}:\d{2})?$/;
            const isoPattern = /^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})(?:\.\d+)?(?:Z|[+-]\d{2}:\d{2})?$/;
            let match = fechaStr.match(mysqlPattern) || fechaStr.match(isoPattern);
            if (match) {
                const [, year, month, day, hour, minute, second] = match;
                return new Date(
                    parseInt(year, 10),
                    parseInt(month, 10) - 1,
                    parseInt(day, 10),
                    parseInt(hour, 10),
                    parseInt(minute, 10),
                    parseInt(second || 0, 10)
                );
            }
        }
        return new Date(fechaStr);
    } catch (error) {
        console.error('Error parseando fecha:', error);
        return null;
    }
}

let filtrosActuales = {
    categoria: 'todos',
    buscar: '',
    orden: 'recientes'
};

async function cargarMegaEventos() {
    const cont = document.getElementById('megaEventosContainer');
    const token = localStorage.getItem('token');

    if (!token) {
        cont.innerHTML = "<p class='text-danger'>Debe iniciar sesión correctamente.</p>";
        return;
    }

    // Verificar que API_BASE_URL esté definido
    if (typeof API_BASE_URL === 'undefined' || !API_BASE_URL) {
        cont.innerHTML = "<div class='col-12'><div class='alert alert-danger'>Error de configuración: API_BASE_URL no está definido. Por favor, recarga la página.</div></div>";
        return;
    }

    cont.innerHTML = '<div class="col-12 text-center py-3"><div class="spinner-border text-primary" role="status"><span class="sr-only">Cargando...</span></div><p class="mt-2 text-muted">Cargando mega eventos finalizados...</p></div>';

    try {
        // Construir URL con parámetros de filtro - SOLO mega eventos finalizados
        const params = new URLSearchParams();
        params.append('estado', 'finalizado'); // Solo mega eventos finalizados
        if (filtrosActuales.categoria !== 'todos') {
            params.append('categoria', filtrosActuales.categoria);
        }
        if (filtrosActuales.buscar) {
            params.append('buscar', filtrosActuales.buscar);
        }

        const url = `${API_BASE_URL}/api/mega-eventos${params.toString() ? '?' + params.toString() : ''}`;
        
        const res = await fetch(url, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        // Verificar si la respuesta es JSON antes de parsear
        const contentType = res.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await res.text();
            console.error('Respuesta no es JSON:', text.substring(0, 200));
            cont.innerHTML = `<div class="col-12"><div class="alert alert-danger">Error: El servidor no devolvió una respuesta válida. Por favor, verifica tu conexión o intenta más tarde.</div></div>`;
            return;
        }

        const data = await res.json();

        if (!res.ok || !data.success) {
            cont.innerHTML = `<div class="col-12"><div class="alert alert-danger">Error: ${data.error || data.message || 'Error al cargar mega eventos'}</div></div>`;
            return;
        }

        let megaEventos = data.mega_eventos || [];

        // Ordenar mega eventos
        if (filtrosActuales.orden === 'recientes') {
            megaEventos.sort((a, b) => {
                const fechaA = parsearFecha(a.fecha_finalizacion || a.fecha_fin) || new Date(0);
                const fechaB = parsearFecha(b.fecha_finalizacion || b.fecha_fin) || new Date(0);
                return fechaB - fechaA; // Más recientes primero
            });
        } else {
            megaEventos.sort((a, b) => {
                const fechaA = new Date(a.fecha_finalizacion || a.fecha_fin || 0);
                const fechaB = new Date(b.fecha_finalizacion || b.fecha_fin || 0);
                return fechaA - fechaB; // Más antiguos primero
            });
        }

        if (megaEventos.length === 0) {
            cont.innerHTML = `
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-calendar-times fa-4x text-muted mb-3" style="opacity: 0.5;"></i>
                            <h5 class="text-muted">No hay mega eventos finalizados</h5>
                            <p class="text-muted">Los mega eventos que finalicen aparecerán aquí.</p>
                        </div>
                    </div>
                </div>
            `;
            return;
        }

        cont.innerHTML = megaEventos.map(e => {
            const imagenUrl = e.imagenes && e.imagenes.length > 0 
                ? (e.imagenes[0].startsWith('http') ? e.imagenes[0] : `${API_BASE_URL}/storage/${e.imagenes[0]}`)
                : null;
            
            const fechaFin = e.fecha_finalizacion || e.fecha_fin;
            const fechaFinDia = fechaFin ? new Date(fechaFin).getDate() : '';
            const fechaFinMes = fechaFin ? new Date(fechaFin).toLocaleString('es-ES', { month: 'short' }) : '';
            
            const estadoBadge = {
                'finalizado': { class: 'badge-info', text: 'Finalizado' },
                'cancelado': { class: 'badge-danger', text: 'Cancelado' }
            }[e.estado] || { class: 'badge-secondary', text: e.estado || 'N/A' };

            return `
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100" style="border-radius: 12px; overflow: hidden; transition: transform 0.3s, box-shadow 0.3s;" 
                         onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.15)'" 
                         onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow=''">
                        <a href="/ong/mega-eventos/${e.mega_evento_id}/detalle" style="text-decoration: none; color: inherit;">
                            <div class="position-relative" style="height: 200px; overflow: hidden; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); cursor: pointer;">
                                ${imagenUrl 
                                    ? `<img src="${imagenUrl}" alt="${e.titulo}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.style.display='none';">`
                                    : `<div class="d-flex align-items-center justify-content-center h-100"><i class="fas fa-calendar-alt fa-4x text-white" style="opacity: 0.3;"></i></div>`
                                }
                                ${fechaFin ? `
                                    <div class="position-absolute" style="top: 10px; left: 10px; background: rgba(255,255,255,0.95); border-radius: 8px; padding: 8px 12px; pointer-events: none;">
                                        <div style="font-size: 1.2rem; font-weight: 700; color: #0C2B44; line-height: 1;">${fechaFinDia}</div>
                                        <div style="font-size: 0.75rem; color: #00A36C; font-weight: 600; text-transform: uppercase;">${fechaFinMes}</div>
                                    </div>
                                ` : ''}
                                <div class="position-absolute" style="top: 10px; right: 10px; pointer-events: none;">
                                    <span class="badge ${estadoBadge.class}" style="font-size: 0.85rem; padding: 0.5em 0.75em;">${estadoBadge.text}</span>
                                </div>
                            </div>
                        </a>
                        <div class="card-body" style="padding: 1.5rem;">
                            <h5 class="card-title mb-2 font-weight-bold text-dark" style="font-size: 1.1rem; line-height: 1.3;">
                                ${e.titulo || 'Sin título'}
                            </h5>
                            <p class="card-text text-muted mb-3" style="font-size: 0.9rem; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                ${e.descripcion || 'Sin descripción'}
                            </p>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar-check mr-1"></i>
                                        ${fechaFin ? new Date(fechaFin).toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' }) : 'Fecha no especificada'}
                                    </small>
                                </div>
                                <span class="badge badge-secondary" style="font-size: 0.75rem;">${e.categoria || 'N/A'}</span>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="/ong/mega-eventos/${e.mega_evento_id}/detalle" class="btn btn-primary btn-sm flex-fill">
                                    <i class="fas fa-eye mr-1"></i> Ver Detalles
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');

    } catch (error) {
        console.error('Error al cargar mega eventos:', error);
        let mensajeError = 'Error al cargar mega eventos. ';
        
        if (error.message && error.message.includes('Unexpected token')) {
            mensajeError += 'El servidor devolvió una respuesta no válida. Por favor, verifica tu conexión o contacta al administrador.';
        } else if (error.message && error.message.includes('Failed to fetch')) {
            mensajeError += 'No se pudo conectar con el servidor. Verifica tu conexión a internet.';
        } else if (error.message) {
            mensajeError += error.message;
        } else {
            mensajeError += 'Ocurrió un error inesperado. Por favor, intenta nuevamente.';
        }
        
        cont.innerHTML = `<div class="col-12"><div class="alert alert-danger">${mensajeError}</div></div>`;
    }
}

// Event listeners
document.getElementById('filtroCategoria').addEventListener('change', (e) => {
    filtrosActuales.categoria = e.target.value;
    cargarMegaEventos();
});

document.getElementById('filtroFecha').addEventListener('change', (e) => {
    filtrosActuales.orden = e.target.value;
    cargarMegaEventos();
});

document.getElementById('buscador').addEventListener('input', debounce((e) => {
    filtrosActuales.buscar = e.target.value;
    cargarMegaEventos();
}, 500));

document.getElementById('btnLimpiar').addEventListener('click', () => {
    document.getElementById('buscador').value = '';
    filtrosActuales.buscar = '';
    cargarMegaEventos();
});

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Cargar mega eventos al iniciar
document.addEventListener('DOMContentLoaded', () => {
    cargarMegaEventos();
});
</script>

@stop


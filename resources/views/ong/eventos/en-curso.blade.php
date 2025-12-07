@extends('layouts.adminlte')

@section('page_title', 'Eventos en Curso')

@section('content_body')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0" style="color: #0C2B44; font-weight: 700;">
            <i class="far fa-play-circle mr-2" style="color: #00A36C;"></i>Eventos en Curso
        </h3>
        <a href="{{ route('ong.eventos.index') }}" class="btn btn-outline-primary">
            <i class="far fa-arrow-left mr-2"></i> Volver a Lista de Eventos
        </a>
    </div>

    <!-- Filtros y Búsqueda -->
    <div class="card mb-4 shadow-sm" style="border-radius: 12px; border: none; margin-top: -1rem;">
        <div class="card-body" style="padding: 2rem 1.5rem;">
            <div class="row">
                <div class="col-md-4 mb-3 mb-md-0">
                    <label for="filtroTipo" class="form-label" style="color: #0C2B44; font-weight: 600; margin-bottom: 0.75rem;">
                        <i class="far fa-sliders-h mr-2" style="color: #00A36C;"></i>Tipo de Evento
                    </label>
                    <select id="filtroTipo" class="form-control" style="border-radius: 8px; padding: 0.75rem;">
                        <option value="todos">Todos los tipos</option>
                        <option value="cultural">Cultural</option>
                        <option value="deportivo">Deportivo</option>
                        <option value="educativo">Educativo</option>
                        <option value="social">Social</option>
                        <option value="benefico">Benéfico</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <label for="filtroFecha" class="form-label" style="color: #0C2B44; font-weight: 600; margin-bottom: 0.75rem;">
                        <i class="far fa-calendar mr-2" style="color: #00A36C;"></i>Ordenar por
                    </label>
                    <select id="filtroFecha" class="form-control" style="border-radius: 8px; padding: 0.75rem;">
                        <option value="proximos">Próximos a iniciar</option>
                        <option value="recientes">Recién iniciados</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="buscador" class="form-label" style="color: #0C2B44; font-weight: 600; margin-bottom: 0.75rem;">
                        <i class="far fa-search mr-2" style="color: #00A36C;"></i>Buscar
                    </label>
                    <div class="input-group">
                        <input type="text" id="buscador" class="form-control" placeholder="Buscar por título..." style="border-radius: 8px 0 0 8px; padding: 0.75rem;">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" id="btnLimpiar" style="border-radius: 0 8px 8px 0; padding: 0.75rem 1rem;">
                                <i class="far fa-times-circle"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="eventosContainer" class="row">
        <p class="text-muted px-3">Cargando eventos en curso...</p>
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

let filtrosActuales = {
    tipo_evento: 'todos',
    buscar: '',
    orden: 'proximos'
};

async function cargarEventos() {
    const cont = document.getElementById('eventosContainer');
    const token = localStorage.getItem('token');
    const ongId = parseInt(localStorage.getItem("id_entidad") || localStorage.getItem("id_usuario"), 10);

    if (!token || isNaN(ongId) || ongId <= 0) {
        cont.innerHTML = "<p class='text-danger'>Debe iniciar sesión correctamente.</p>";
        return;
    }

    if (typeof API_BASE_URL === 'undefined' || !API_BASE_URL) {
        cont.innerHTML = "<div class='col-12'><div class='alert alert-danger'>Error de configuración: API_BASE_URL no está definido. Por favor, recarga la página.</div></div>";
        return;
    }

    cont.innerHTML = '<div class="col-12 text-center py-3"><div class="spinner-border text-primary" role="status"><span class="sr-only">Cargando...</span></div><p class="mt-2 text-muted">Cargando eventos en curso...</p></div>';

    try {
        // Construir URL con parámetros de filtro - SOLO eventos activos (en curso)
        const params = new URLSearchParams();
        params.append('estado', 'activos'); // Solo eventos activos/en curso
        if (filtrosActuales.tipo_evento !== 'todos') {
            params.append('tipo_evento', filtrosActuales.tipo_evento);
        }
        if (filtrosActuales.buscar) {
            params.append('buscar', filtrosActuales.buscar);
        }

        const url = `${API_BASE_URL}/api/eventos/ong/${ongId}/dashboard${params.toString() ? '?' + params.toString() : ''}`;
        
        const res = await fetch(url, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        const contentType = res.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await res.text();
            console.error('Respuesta no es JSON:', text.substring(0, 200));
            cont.innerHTML = `<div class="col-12"><div class="alert alert-danger">Error: El servidor no devolvió una respuesta válida. Por favor, verifica tu conexión o intenta más tarde.</div></div>`;
            return;
        }

        const data = await res.json();

        if (!res.ok || !data.success) {
            cont.innerHTML = `<div class="col-12"><div class="alert alert-danger">Error: ${data.error || data.message || 'Error al cargar eventos'}</div></div>`;
            return;
        }

        let eventos = data.eventos || [];

        // Filtrar solo eventos activos (en curso)
        eventos = eventos.filter(e => {
            const estadoDinamico = e.estado_dinamico || e.estado;
            return estadoDinamico === 'activo';
        });

        // Ordenar eventos
        if (filtrosActuales.orden === 'proximos') {
            eventos.sort((a, b) => {
                const fechaA = parsearFecha(a.fecha_inicio) || new Date(0);
                const fechaB = parsearFecha(b.fecha_inicio) || new Date(0);
                return fechaA - fechaB; // Próximos primero
            });
        } else {
            eventos.sort((a, b) => {
                const fechaA = parsearFecha(a.fecha_inicio) || new Date(0);
                const fechaB = parsearFecha(b.fecha_inicio) || new Date(0);
                return fechaB - fechaA; // Recién iniciados primero
            });
        }

        if (eventos.length === 0) {
            cont.innerHTML = `
                <div class="col-12">
                    <div class="card shadow-sm" style="border-radius: 12px; border: none;">
                        <div class="card-body text-center py-5">
                            <i class="far fa-calendar-check fa-4x text-muted mb-3" style="opacity: 0.5;"></i>
                            <h5 class="text-muted">No hay eventos en curso</h5>
                            <p class="text-muted">Los eventos que estén iniciando aparecerán aquí.</p>
                        </div>
                    </div>
                </div>
            `;
            return;
        }

        cont.innerHTML = eventos.map(e => {
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
            
            const imagenUrl = imagenes.length > 0 ? buildImageUrl(imagenes[0]) : null;
            
            const fechaInicio = e.fecha_inicio ? parsearFecha(e.fecha_inicio) : null;
            const fechaInicioDia = fechaInicio ? fechaInicio.getDate() : '';
            const fechaInicioMes = fechaInicio ? fechaInicio.toLocaleString('es-ES', { month: 'short' }) : '';
            
            // Calcular tiempo restante o transcurrido
            const ahora = new Date();
            let tiempoInfo = '';
            if (fechaInicio) {
                const diffMs = fechaInicio - ahora;
                const diffMins = Math.floor(diffMs / 60000);
                if (diffMins > 0) {
                    if (diffMins < 60) {
                        tiempoInfo = `<span class="badge badge-info">Inicia en ${diffMins} min</span>`;
                    } else if (diffMins < 1440) {
                        const horas = Math.floor(diffMins / 60);
                        tiempoInfo = `<span class="badge badge-info">Inicia en ${horas} hora${horas > 1 ? 's' : ''}</span>`;
                    } else {
                        const dias = Math.floor(diffMins / 1440);
                        tiempoInfo = `<span class="badge badge-info">Inicia en ${dias} día${dias > 1 ? 's' : ''}</span>`;
                    }
                } else {
                    const minsTranscurridos = Math.abs(diffMins);
                    if (minsTranscurridos < 60) {
                        tiempoInfo = `<span class="badge badge-success">En curso (${minsTranscurridos} min)</span>`;
                    } else {
                        const horasTranscurridas = Math.floor(minsTranscurridos / 60);
                        tiempoInfo = `<span class="badge badge-success">En curso (${horasTranscurridas}h)</span>`;
                    }
                }
            }

            return `
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card shadow-sm h-100" style="border-radius: 12px; border: none; overflow: hidden; transition: transform 0.3s, box-shadow 0.3s; border-left: 4px solid #00A36C;" 
                         onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.15)'" 
                         onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.1)'">
                        <a href="/ong/eventos/${e.id}/detalle" style="text-decoration: none; color: inherit;">
                            <div class="position-relative" style="height: 200px; overflow: hidden; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); cursor: pointer;">
                                ${imagenUrl 
                                    ? `<img src="${imagenUrl}" alt="${e.titulo}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.style.display='none';">`
                                    : `<div class="d-flex align-items-center justify-content-center h-100"><i class="far fa-calendar-alt fa-4x text-white" style="opacity: 0.3;"></i></div>`
                                }
                                ${fechaInicio ? `
                                    <div class="position-absolute" style="top: 10px; left: 10px; background: rgba(255,255,255,0.95); border-radius: 8px; padding: 8px 12px; pointer-events: none;">
                                        <div style="font-size: 1.2rem; font-weight: 700; color: #0C2B44; line-height: 1;">${fechaInicioDia}</div>
                                        <div style="font-size: 0.75rem; color: #00A36C; font-weight: 600; text-transform: uppercase;">${fechaInicioMes}</div>
                                    </div>
                                ` : ''}
                                <div class="position-absolute" style="top: 10px; right: 10px; pointer-events: none;">
                                    <span class="badge badge-success" style="font-size: 0.85rem; padding: 0.5em 0.75em; background: #00A36C;">
                                        <i class="far fa-play-circle mr-1"></i>En Curso
                                    </span>
                                </div>
                            </div>
                        </a>
                        <div class="card-body" style="padding: 1.5rem;">
                            <h5 class="card-title mb-2" style="color: #0C2B44; font-weight: 700; font-size: 1.1rem; line-height: 1.3;">
                                ${e.titulo || 'Sin título'}
                            </h5>
                            <p class="card-text text-muted mb-3" style="font-size: 0.9rem; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                ${e.descripcion || 'Sin descripción'}
                            </p>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <small class="text-muted">
                                        <i class="far fa-calendar mr-1"></i>
                                        ${fechaInicio ? fechaInicio.toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' }) : 'Fecha no especificada'}
                                    </small>
                                </div>
                                <span class="badge badge-secondary" style="font-size: 0.75rem;">${e.tipo_evento || 'N/A'}</span>
                            </div>
                            ${tiempoInfo ? `<div class="mb-3">${tiempoInfo}</div>` : ''}
                            <div class="d-flex gap-2">
                                <a href="/ong/eventos/${e.id}/detalle" class="btn btn-primary btn-sm flex-fill" style="border-radius: 8px;">
                                    <i class="far fa-eye mr-1"></i> Ver Detalles
                                </a>
                                <a href="/ong/eventos/${e.id}/dashboard" class="btn btn-success btn-sm" style="border-radius: 8px;">
                                    <i class="far fa-chart-bar mr-1"></i> Dashboard
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');

    } catch (error) {
        console.error('Error al cargar eventos:', error);
        let mensajeError = 'Error al cargar eventos. ';
        
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
document.getElementById('filtroTipo').addEventListener('change', (e) => {
    filtrosActuales.tipo_evento = e.target.value;
    cargarEventos();
});

document.getElementById('filtroFecha').addEventListener('change', (e) => {
    filtrosActuales.orden = e.target.value;
    cargarEventos();
});

document.getElementById('buscador').addEventListener('input', debounce((e) => {
    filtrosActuales.buscar = e.target.value;
    cargarEventos();
}, 500));

document.getElementById('btnLimpiar').addEventListener('click', () => {
    document.getElementById('buscador').value = '';
    filtrosActuales.buscar = '';
    cargarEventos();
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

// Cargar eventos al iniciar
document.addEventListener('DOMContentLoaded', () => {
    cargarEventos();
});
</script>

@stop

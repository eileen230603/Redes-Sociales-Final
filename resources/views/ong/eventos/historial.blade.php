@extends('layouts.adminlte')

@section('page_title', 'Historial de Eventos')

@section('content_body')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0" style="color: #0C2B44; font-weight: 700;">
            <i class="far fa-history mr-2" style="color: #00A36C;"></i>Historial de Eventos Finalizados
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
                        <option value="recientes">Más recientes</option>
                        <option value="antiguos">Más antiguos</option>
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
        <p class="text-muted px-3">Cargando eventos finalizados...</p>
    </div>

</div>

@stop

@section('js')
{{-- Script global para icono de notificaciones --}}
<script src="{{ asset('js/notificaciones-ong.js') }}"></script>
<script src="{{ asset('assets/js/config.js') }}"></script>

<script>
let filtrosActuales = {
    tipo_evento: 'todos',
    buscar: '',
    orden: 'recientes'
};

async function cargarEventos() {
    const cont = document.getElementById('eventosContainer');
    const token = localStorage.getItem('token');
    const ongId = parseInt(localStorage.getItem("id_entidad") || localStorage.getItem("id_usuario"), 10);

    if (!token || isNaN(ongId) || ongId <= 0) {
        cont.innerHTML = "<p class='text-danger'>Debe iniciar sesión correctamente.</p>";
        return;
    }

    // Verificar que API_BASE_URL esté definido
    if (typeof API_BASE_URL === 'undefined' || !API_BASE_URL) {
        cont.innerHTML = "<div class='col-12'><div class='alert alert-danger'>Error de configuración: API_BASE_URL no está definido. Por favor, recarga la página.</div></div>";
        return;
    }

    cont.innerHTML = '<div class="col-12 text-center py-3"><div class="spinner-border text-primary" role="status"><span class="sr-only">Cargando...</span></div><p class="mt-2 text-muted">Cargando eventos finalizados...</p></div>';

    try {
        // Construir URL con parámetros de filtro - SOLO eventos finalizados
        const params = new URLSearchParams();
        params.append('estado', 'finalizado'); // Solo eventos finalizados
        params.append('excluir_finalizados', 'false'); // No excluir finalizados
        if (filtrosActuales.tipo_evento !== 'todos') {
            params.append('tipo_evento', filtrosActuales.tipo_evento);
        }
        if (filtrosActuales.buscar) {
            params.append('buscar', filtrosActuales.buscar);
        }

        const url = `${API_BASE_URL}/api/eventos/ong/${ongId}${params.toString() ? '?' + params.toString() : ''}`;
        
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
            cont.innerHTML = `<div class="col-12"><div class="alert alert-danger">Error: ${data.error || data.message || 'Error al cargar eventos'}</div></div>`;
            return;
        }

        let eventos = data.eventos || [];

        // Ordenar eventos
        if (filtrosActuales.orden === 'recientes') {
            eventos.sort((a, b) => {
                const fechaA = new Date(a.fecha_finalizacion || a.fecha_fin || 0);
                const fechaB = new Date(b.fecha_finalizacion || b.fecha_fin || 0);
                return fechaB - fechaA; // Más recientes primero
            });
        } else {
            eventos.sort((a, b) => {
                const fechaA = new Date(a.fecha_finalizacion || a.fecha_fin || 0);
                const fechaB = new Date(b.fecha_finalizacion || b.fecha_fin || 0);
                return fechaA - fechaB; // Más antiguos primero
            });
        }

        if (eventos.length === 0) {
            cont.innerHTML = `
                <div class="col-12">
                    <div class="card shadow-sm" style="border-radius: 12px; border: none;">
                        <div class="card-body text-center py-5">
                            <i class="far fa-calendar-times fa-4x text-muted mb-3" style="opacity: 0.5;"></i>
                            <h5 class="text-muted">No hay eventos finalizados</h5>
                            <p class="text-muted">Los eventos que finalicen aparecerán aquí.</p>
                        </div>
                    </div>
                </div>
            `;
            return;
        }

        cont.innerHTML = eventos.map(e => {
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
                    <div class="card shadow-sm h-100" style="border-radius: 12px; border: none; overflow: hidden; transition: transform 0.3s, box-shadow 0.3s;" 
                         onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.15)'" 
                         onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.1)'">
                        <a href="/ong/eventos/${e.id}/detalle" style="text-decoration: none; color: inherit;">
                            <div class="position-relative" style="height: 200px; overflow: hidden; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); cursor: pointer;">
                                ${imagenUrl 
                                    ? `<img src="${imagenUrl}" alt="${e.titulo}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.style.display='none';">`
                                    : `<div class="d-flex align-items-center justify-content-center h-100"><i class="far fa-calendar-alt fa-4x text-white" style="opacity: 0.3;"></i></div>`
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
                            <h5 class="card-title mb-2" style="color: #0C2B44; font-weight: 700; font-size: 1.1rem; line-height: 1.3;">
                                ${e.titulo || 'Sin título'}
                            </h5>
                            <p class="card-text text-muted mb-3" style="font-size: 0.9rem; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                ${e.descripcion || 'Sin descripción'}
                            </p>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <small class="text-muted">
                                        <i class="far fa-calendar-check mr-1"></i>
                                        ${e.fecha_finalizacion ? new Date(e.fecha_finalizacion).toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' }) : 'Fecha no especificada'}
                                    </small>
                                </div>
                                <span class="badge badge-secondary" style="font-size: 0.75rem;">${e.tipo_evento || 'N/A'}</span>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="/ong/eventos/${e.id}/detalle" class="btn btn-primary btn-sm flex-fill" style="border-radius: 8px;">
                                    <i class="far fa-eye mr-1"></i> Ver Detalles
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


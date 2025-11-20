@extends('adminlte::page')

@section('title', 'Eventos ONG')

@section('content_header')
    <h1><i class="fas fa-calendar"></i> Eventos</h1>
@stop

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="text-primary">Lista de eventos</h4>

        <a href="{{ route('ong.eventos.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Nuevo evento
        </a>
    </div>

    <!-- Filtros y Búsqueda -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3 mb-md-0">
                    <label for="filtroTipo" class="form-label"><i class="fas fa-filter mr-2"></i>Tipo de Evento</label>
                    <select id="filtroTipo" class="form-control">
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
                    <label for="filtroEstado" class="form-label"><i class="fas fa-info-circle mr-2"></i>Estado</label>
                    <select id="filtroEstado" class="form-control">
                        <option value="todos">Todos los estados</option>
                        <option value="borrador">Borrador</option>
                        <option value="publicado">Publicado</option>
                        <option value="finalizado">Finalizado</option>
                        <option value="cancelado">Cancelado</option>
                    </select>
                </div>
                <div class="col-md-4">
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

    <div id="eventosContainer" class="row">
        <p class="text-muted px-3">Cargando eventos...</p>
    </div>

</div>

@stop

@section('js')
<script src="{{ asset('assets/js/config.js') }}"></script>

<script>
let filtrosActuales = {
    tipo_evento: 'todos',
    estado: 'todos',
    buscar: ''
};

async function cargarEventos() {
    const cont = document.getElementById('eventosContainer');
    const token = localStorage.getItem('token');
    const ongId = parseInt(localStorage.getItem("id_entidad") || localStorage.getItem("id_usuario"), 10);

    if (!token || isNaN(ongId) || ongId <= 0) {
        cont.innerHTML = "<p class='text-danger'>Debe iniciar sesión correctamente.</p>";
        return;
    }

    cont.innerHTML = '<div class="col-12 text-center py-3"><div class="spinner-border text-primary" role="status"><span class="sr-only">Cargando...</span></div><p class="mt-2 text-muted">Cargando eventos...</p></div>';

    try {
        // Construir URL con parámetros de filtro
        const params = new URLSearchParams();
        if (filtrosActuales.tipo_evento !== 'todos') {
            params.append('tipo_evento', filtrosActuales.tipo_evento);
        }
        if (filtrosActuales.estado !== 'todos') {
            params.append('estado', filtrosActuales.estado);
        }
        if (filtrosActuales.buscar.trim() !== '') {
            params.append('buscar', filtrosActuales.buscar.trim());
        }

        const url = `${API_BASE_URL}/api/eventos/ong/${ongId}${params.toString() ? '?' + params.toString() : ''}`;

        const res = await fetch(url, {
            method: 'GET',
            headers: {
                "Authorization": `Bearer ${token}`,
                "Accept": "application/json",
                "Content-Type": "application/json"
            }
        });

        console.log("Response status:", res.status);
        console.log("Response ok:", res.ok);

        if (!res.ok) {
            const errorData = await res.json().catch(() => ({}));
            console.error("Error response:", errorData);
            cont.innerHTML = `<p class='text-danger'>Error cargando eventos (${res.status}): ${errorData.error || 'Error del servidor'}</p>`;
            return;
        }

        const data = await res.json();
        console.log("Eventos recibidos:", data);

        if (!data.success) {
            console.error("Error en respuesta:", data);
            cont.innerHTML = `<p class='text-danger'>Error cargando eventos: ${data.error || 'Error desconocido'}</p>`;
            return;
        }

        if (!data.eventos || data.eventos.length === 0) {
            cont.innerHTML = `
                <div class="alert alert-info">
                    <p class='text-muted mb-2'>No hay eventos registrados para esta ONG.</p>
                    <small class='text-muted'>ONG ID usado: ${data.ong_id || ongId} | Total encontrados: ${data.count || 0}</small>
                </div>
            `;
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

        data.eventos.forEach(ev => {
            // Procesar imágenes
            let imagenes = [];
            if (Array.isArray(ev.imagenes) && ev.imagenes.length > 0) {
                imagenes = ev.imagenes.filter(img => img && typeof img === 'string' && img.trim().length > 0);
            } else if (typeof ev.imagenes === 'string' && ev.imagenes.trim()) {
                try {
                    const parsed = JSON.parse(ev.imagenes);
                    if (Array.isArray(parsed)) {
                        imagenes = parsed.filter(img => img && typeof img === 'string' && img.trim().length > 0);
                    }
                } catch (err) {
                    console.warn('Error parseando imágenes:', err);
                }
            }

            const imagenPrincipal = imagenes.length > 0 ? buildImageUrl(imagenes[0]) : null;
            
            // Formatear fecha
            const fechaInicio = ev.fecha_inicio ? new Date(ev.fecha_inicio).toLocaleDateString('es-ES', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            }) : 'Fecha no especificada';

            // Estado badge - usar estado_dinamico si está disponible
            const estadoParaBadge = ev.estado_dinamico || ev.estado;
            const estadoBadges = {
                'borrador': '<span class="badge badge-warning">Borrador</span>',
                'publicado': '<span class="badge badge-primary">Publicado</span>',
                'cancelado': '<span class="badge badge-danger">Cancelado</span>',
                'finalizado': '<span class="badge badge-secondary">Finalizado</span>',
                'activo': '<span class="badge badge-success">En Curso</span>',
                'proximo': '<span class="badge badge-info">Próximo</span>'
            };
            const estadoBadge = estadoBadges[estadoParaBadge] || '<span class="badge badge-secondary">' + (estadoParaBadge || 'N/A') + '</span>';

            // Crear card con diseño minimalista
            const cardDiv = document.createElement('div');
            cardDiv.className = 'col-md-4 mb-4';
            
            cardDiv.innerHTML = `
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; overflow: hidden; transition: transform 0.2s, box-shadow 0.2s;">
                    ${imagenPrincipal 
                        ? `<div class="position-relative" style="height: 200px; overflow: hidden; background: #f8f9fa;">
                            <img src="${imagenPrincipal}" alt="${ev.titulo}" class="w-100 h-100" style="object-fit: cover;" 
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
                        <h5 class="mb-2" style="font-size: 1.1rem; font-weight: 600; color: #2c3e50;">${ev.titulo || 'Sin título'}</h5>
                        <p class="text-muted mb-3" style="font-size: 0.9rem; line-height: 1.5; color: #6c757d; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            ${ev.descripcion || 'Sin descripción'}
                        </p>
                        <div class="mb-3 d-flex align-items-center" style="color: #6c757d; font-size: 0.85rem;">
                            <i class="far fa-calendar-alt mr-2"></i>
                            <span>${fechaInicio}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mt-3">
                            <div class="d-flex align-items-center" style="color: #6c757d; font-size: 0.85rem;">
                                <i class="fas fa-heart text-danger mr-1"></i>
                                <span id="reacciones-${ev.id}">-</span>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="/ong/eventos/${ev.id}/detalle" class="btn btn-sm" style="background: #e9ecef; color: #495057; border: none; border-radius: 8px; padding: 0.5em 1.2em; font-weight: 500; transition: all 0.2s;">
                                    Detalle
                                </a>
                                <a href="/ong/eventos/${ev.id}/editar" class="btn btn-sm" style="background: #667eea; color: white; border: none; border-radius: 8px; padding: 0.5em 1.2em; font-weight: 500; transition: all 0.2s;">
                                    Editar
                                </a>
                                <button onclick="eliminar(${ev.id})" class="btn btn-sm" style="background: #dc3545; color: white; border: none; border-radius: 8px; padding: 0.5em 1.2em; font-weight: 500; transition: all 0.2s; cursor: pointer;">
                                    Eliminar
                                </button>
                            </div>
                        </div>
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

        // Cargar contadores de reacciones para cada evento
        await cargarContadoresReacciones(data.eventos);

    } catch (err) {
        console.error(err);
        cont.innerHTML = "<p class='text-danger'>Error de conexión.</p>";
    }
}

// Cargar contadores de reacciones para todos los eventos
async function cargarContadoresReacciones(eventos) {
    const token = localStorage.getItem('token');
    
    for (const evento of eventos) {
        try {
            const res = await fetch(`${API_BASE_URL}/api/reacciones/verificar/${evento.id}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            
            const data = await res.json();
            if (data.success) {
                const contadorEl = document.getElementById(`reacciones-${evento.id}`);
                if (contadorEl) {
                    contadorEl.textContent = data.total_reacciones || 0;
                }
            }
        } catch (error) {
            console.warn(`Error cargando reacciones para evento ${evento.id}:`, error);
        }
    }
}

document.addEventListener('DOMContentLoaded', async () => {
    // Cargar eventos iniciales
    await cargarEventos();

    // Event listeners para filtros
    document.getElementById('filtroTipo').addEventListener('change', function() {
        filtrosActuales.tipo_evento = this.value;
        cargarEventos();
    });

    document.getElementById('filtroEstado').addEventListener('change', function() {
        filtrosActuales.estado = this.value;
        cargarEventos();
    });

    // Búsqueda con debounce
    let searchTimeout;
    document.getElementById('buscador').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            filtrosActuales.buscar = this.value;
            cargarEventos();
        }, 500);
    });

    // Botón limpiar
    document.getElementById('btnLimpiar').addEventListener('click', function() {
        document.getElementById('buscador').value = '';
        document.getElementById('filtroTipo').value = 'todos';
        document.getElementById('filtroEstado').value = 'todos';
        filtrosActuales = {
            tipo_evento: 'todos',
            estado: 'todos',
            buscar: ''
        };
        cargarEventos();
    });
});

async function eliminar(id) {
    // Usar SweetAlert2 si está disponible, sino usar confirm nativo
    let confirmar = false;
    
    if (typeof Swal !== 'undefined') {
        const result = await Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        });
        confirmar = result.isConfirmed;
    } else {
        confirmar = confirm("¿Estás seguro de que deseas eliminar este evento? Esta acción no se puede deshacer.");
    }

    if (!confirmar) return;

    const token = localStorage.getItem('token');

    try {
    const res = await fetch(`${API_BASE_URL}/api/eventos/${id}`, {
        method: "DELETE",
        headers: {
            "Authorization": `Bearer ${token}`,
            "Accept": "application/json"
        }
    });

    const data = await res.json();

        if (!res.ok || !data.success) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.error || data.message || 'Error al eliminar el evento'
                });
            } else {
                alert('Error: ' + (data.error || data.message || 'Error al eliminar el evento'));
            }
            return;
        }

        // Mostrar mensaje de éxito
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: '¡Eliminado!',
                text: data.message || 'El evento ha sido eliminado correctamente',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        } else {
            alert(data.message || 'Evento eliminado correctamente');
    location.reload();
        }

    } catch (error) {
        console.error('Error al eliminar:', error);
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error de conexión al eliminar el evento'
            });
        } else {
            alert('Error de conexión al eliminar el evento');
        }
    }
}
</script>
@stop

@extends('layouts.adminlte')

@section('page_title', 'Eventos ONG')

@section('content_body')
<div class="container-fluid">

    <!-- Botón normal (visible al inicio) -->
    <div class="d-flex justify-content-end align-items-center mb-3" id="btnNuevoEventoNormal">
        <a href="{{ route('ong.eventos.create') }}" class="btn btn-success">
            <i class="fas fa-plus-circle mr-2"></i> Nuevo evento
        </a>
    </div>

    <!-- Botón FAB circular (oculto inicialmente) -->
    <a href="{{ route('ong.eventos.create') }}" id="btnNuevoEventoFAB" class="fab-button" style="display: none;">
        <i class="fas fa-plus"></i>
    </a>

    <!-- Filtros y Búsqueda -->
    <div class="card mb-4">
        <div class="card-header bg-primary">
            <h5 class="card-title mb-0 text-white">
                <i class="fas fa-filter mr-2"></i>Filtros y Búsqueda
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3 mb-md-0">
                    <label for="filtroTipo" class="font-weight-bold text-dark">
                        <i class="fas fa-sliders-h mr-2 text-info"></i>Tipo de Evento
                    </label>
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
                    <label for="filtroEstado" class="font-weight-bold text-dark">
                        <i class="fas fa-info-circle mr-2 text-success"></i>Estado
                    </label>
                    <select id="filtroEstado" class="form-control">
                        <option value="todos">Todos los estados</option>
                        <option value="borrador">Borrador</option>
                        <option value="publicado">Publicado</option>
                        <option value="finalizado">Finalizado</option>
                        <option value="cancelado">Cancelado</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="buscador" class="font-weight-bold text-dark">
                        <i class="fas fa-search mr-2 text-warning"></i>Buscar
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

    <div id="eventosContainer" class="row">
        <p class="text-muted px-3">Cargando eventos...</p>
    </div>

</div>

@stop

@push('css')
<style>
    /* Botón FAB (Floating Action Button) */
    .fab-button {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(12, 43, 68, 0.3);
        z-index: 1000;
        transition: all 0.3s ease;
        text-decoration: none;
        border: none;
    }

    .fab-button:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 20px rgba(12, 43, 68, 0.4);
        color: white;
        text-decoration: none;
    }

    .fab-button i {
        font-size: 24px;
    }

    .fab-button.show {
        animation: fabSlideIn 0.3s ease forwards;
    }

    .fab-button.hide {
        animation: fabSlideOut 0.3s ease forwards;
    }

    @keyframes fabSlideIn {
        from {
            opacity: 0;
            transform: translateY(20px) scale(0.8);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    @keyframes fabSlideOut {
        from {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
        to {
            opacity: 0;
            transform: translateY(20px) scale(0.8);
        }
    }

    /* Transición suave para el botón normal */
    #btnNuevoEventoNormal {
        transition: opacity 0.3s ease, transform 0.3s ease;
    }

    #btnNuevoEventoNormal.hide {
        opacity: 0;
        transform: translateY(-10px);
        pointer-events: none;
    }
</style>
@endpush

@section('js')
{{-- Script global para icono de notificaciones --}}
<script src="{{ asset('js/notificaciones-ong.js') }}"></script>
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
        params.append('excluir_finalizados', 'true'); // Excluir eventos finalizados por defecto
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
            let mensajeFiltro = '';
            if (filtrosActuales.estado !== 'todos' || filtrosActuales.tipo_evento !== 'todos' || filtrosActuales.buscar.trim() !== '') {
                mensajeFiltro = '<p class="text-muted mb-2">No se encontraron eventos con los filtros aplicados.</p>';
                mensajeFiltro += '<p class="text-muted mb-2"><small>Intenta cambiar los filtros o la búsqueda.</small></p>';
            } else {
                mensajeFiltro = '<p class="text-muted mb-2">No hay eventos registrados para esta ONG.</p>';
            }
            
            cont.innerHTML = `
                <div class="alert alert-info">
                    ${mensajeFiltro}
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
            
            // Formatear fecha de inicio para mostrar en el card
            const fechaInicio = ev.fecha_inicio ? new Date(ev.fecha_inicio).toLocaleDateString('es-ES', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            }) : 'Fecha no especificada';

            // Formatear fecha de finalización para el overlay (día y mes)
            let fechaOverlay = '';
            if (ev.fecha_fin) {
                const fechaFin = new Date(ev.fecha_fin);
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

            // Estado badge - usar estado_dinamico si está disponible (se actualizará en el HTML)
            const estadoParaBadge = ev.estado_dinamico || ev.estado;

            // Crear card con diseño minimalista - Cada evento en su propio container
            const cardDiv = document.createElement('div');
            cardDiv.className = 'col-md-4 mb-4';
            
            // Actualizar badges con nueva paleta
            const estadoBadgesActualizado = {
                'borrador': '<span class="badge" style="background: #ffc107; color: white; font-size: 0.75rem; padding: 0.4em 0.8em; border-radius: 20px; font-weight: 500;">Borrador</span>',
                'publicado': '<span class="badge" style="background: #0C2B44; color: white; font-size: 0.75rem; padding: 0.4em 0.8em; border-radius: 20px; font-weight: 500;">Publicado</span>',
                'cancelado': '<span class="badge" style="background: #dc3545; color: white; font-size: 0.75rem; padding: 0.4em 0.8em; border-radius: 20px; font-weight: 500;">Cancelado</span>',
                'finalizado': '<span class="badge" style="background: #6c757d; color: white; font-size: 0.75rem; padding: 0.4em 0.8em; border-radius: 20px; font-weight: 500;">Finalizado</span>',
                'activo': '<span class="badge" style="background: #00A36C; color: white; font-size: 0.75rem; padding: 0.4em 0.8em; border-radius: 20px; font-weight: 500;">En Curso</span>',
                'proximo': '<span class="badge" style="background: #0C2B44; color: white; font-size: 0.75rem; padding: 0.4em 0.8em; border-radius: 20px; font-weight: 500;">Próximo</span>'
            };
            const estadoBadgeActualizado = estadoBadgesActualizado[estadoParaBadge] || '<span class="badge" style="background: #6c757d; color: white; font-size: 0.75rem; padding: 0.4em 0.8em; border-radius: 20px; font-weight: 500;">' + (estadoParaBadge || 'N/A') + '</span>';
            
            cardDiv.innerHTML = `
                <div class="card h-100" style="border-radius: 12px; overflow: hidden; transition: transform 0.3s, box-shadow 0.3s;">
                    ${imagenPrincipal 
                        ? `<a href="/ong/eventos/${ev.id}/detalle" style="text-decoration: none; display: block;">
                            <div class="position-relative" style="height: 200px; overflow: hidden; background: #F5F5F5; cursor: pointer;">
                                <img src="${imagenPrincipal}" alt="${ev.titulo}" class="w-100 h-100" style="object-fit: cover; transition: transform 0.3s;" 
                                     onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\\'http://www.w3.org/2000/svg\\' width=\\'400\\' height=\\'200\\'%3E%3Crect fill=\\'%23F5F5F5\\' width=\\'400\\' height=\\'200\\'/%3E%3Ctext x=\\'50%25\\' y=\\'50%25\\' text-anchor=\\'middle\\' dy=\\'.3em\\' fill=\\'%23adb5bd\\' font-family=\\'Arial\\' font-size=\\'14\\'%3EImagen no disponible%3C/text%3E%3C/svg%3E'; this.style.objectFit='contain'; this.style.padding='20px';"
                                     onmouseover="this.style.transform='scale(1.05)'"
                                     onmouseout="this.style.transform='scale(1)'">
                                ${fechaOverlay}
                                <div class="position-absolute" style="top: 12px; right: 12px; pointer-events: none; z-index: 10;">
                                    ${estadoBadgeActualizado}
                                </div>
                            </div>
                           </a>`
                        : `<a href="/ong/eventos/${ev.id}/detalle" style="text-decoration: none; display: block;">
                            <div class="position-relative" style="height: 200px; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); display: flex; align-items: center; justify-content: center; cursor: pointer;">
                                <i class="fas fa-calendar fa-4x text-white" style="opacity: 0.3;"></i>
                                ${fechaOverlay}
                                <div class="position-absolute" style="top: 12px; right: 12px; pointer-events: none; z-index: 10;">
                                    ${estadoBadgeActualizado}
                                </div>
                            </div>
                           </a>`
                    }
                    <div class="card-body p-4">
                        <h5 class="mb-3" style="font-size: 1.15rem; font-weight: 700; color: #0C2B44; line-height: 1.4;">${ev.titulo || 'Sin título'}</h5>
                        <p class="mb-3" style="font-size: 0.9rem; line-height: 1.6; color: #333333; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            ${ev.descripcion || 'Sin descripción'}
                        </p>
                        <div class="mb-3 d-flex align-items-center text-muted" style="font-size: 0.85rem;">
                            <i class="fas fa-calendar mr-2 text-info"></i>
                            <span>${fechaInicio}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mt-4 pt-3 border-top">
                            <div class="d-flex align-items-center text-muted" style="font-size: 0.85rem;">
                                <i class="fas fa-heart mr-1 text-danger"></i>
                                <span id="reacciones-${ev.id}">-</span>
                            </div>
                            <div class="d-flex" style="gap: 0.5rem;">
                                <a href="/ong/eventos/${ev.id}/detalle" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye mr-1"></i>Detalle
                                </a>
                                <a href="/ong/eventos/${ev.id}/editar" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit mr-1"></i>Editar
                                </a>
                                <button onclick="eliminar(${ev.id})" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash-alt mr-1"></i>Eliminar
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
                this.style.boxShadow = '0 8px 16px rgba(0, 0, 0, 0.1)';
            };
            card.onmouseleave = function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '';
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

    // Control del botón FAB al hacer scroll
    const btnNormal = document.getElementById('btnNuevoEventoNormal');
    const btnFAB = document.getElementById('btnNuevoEventoFAB');
    const scrollThreshold = 100; // Píxeles de scroll antes de mostrar FAB

    function handleScroll() {
        const scrollY = window.scrollY || window.pageYOffset;

        if (scrollY > scrollThreshold) {
            // Mostrar FAB y ocultar botón normal
            if (!btnFAB.classList.contains('show')) {
                btnFAB.style.display = 'flex';
                btnFAB.classList.remove('hide');
                btnFAB.classList.add('show');
            }
            if (!btnNormal.classList.contains('hide')) {
                btnNormal.classList.add('hide');
            }
        } else {
            // Ocultar FAB y mostrar botón normal
            if (btnFAB.classList.contains('show')) {
                btnFAB.classList.remove('show');
                btnFAB.classList.add('hide');
                setTimeout(() => {
                    if (btnFAB.classList.contains('hide')) {
                        btnFAB.style.display = 'none';
                    }
                }, 300);
            }
            if (btnNormal.classList.contains('hide')) {
                btnNormal.classList.remove('hide');
            }
        }
    }

    // Escuchar eventos de scroll
    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll(); // Verificar estado inicial
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

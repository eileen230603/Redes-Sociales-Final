@extends('layouts.adminlte')

@section('page_title', 'Mega Eventos')

@section('content_body')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary">
            <i class=></i> 
        </h2>
        <a href="{{ route('ong.mega-eventos.create') }}" class="btn btn-success btn-lg">
            <i class="fas fa-plus mr-2"></i> Nuevo Mega Evento
        </a>
    </div>

    <!-- Filtros y Búsqueda -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3 mb-md-0">
                    <label for="filtroCategoria" class="form-label"><i class="fas fa-filter mr-2"></i>Categoría</label>
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
                    <label for="filtroEstado" class="form-label"><i class="fas fa-info-circle mr-2"></i>Estado</label>
                    <select id="filtroEstado" class="form-control">
                        <option value="todos">Todos los estados</option>
                        <option value="planificacion">Planificación</option>
                        <option value="activo">Activo</option>
                        <option value="en_curso">En Curso</option>
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

    <div id="megaEventosContainer" class="row">
        <div class="col-12 text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Cargando...</span>
            </div>
            <p class="mt-3 text-muted">Cargando mega eventos...</p>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('assets/js/config.js') }}"></script>
<script>
// Función helper para construir URL de imagen
function buildImageUrl(imgUrl) {
    if (!imgUrl || imgUrl.trim() === '') return null;
    
    // Si ya es una URL completa
    if (imgUrl.startsWith('http://') || imgUrl.startsWith('https://')) {
        return imgUrl;
    }
    
    // Si empieza con /storage/, usar directamente
    if (imgUrl.startsWith('/storage/')) {
        return `${window.location.origin}${imgUrl}`;
    }
    
    // Si empieza con storage/, agregar /
    if (imgUrl.startsWith('storage/')) {
        return `${window.location.origin}/${imgUrl}`;
    }
    
    // Si no tiene prefijo, agregar /storage/
    return `${window.location.origin}/storage/${imgUrl}`;
}

let filtrosMegaEventos = {
    categoria: 'todos',
    estado: 'todos',
    buscar: ''
};

async function cargarMegaEventos() {
    const container = document.getElementById('megaEventosContainer');
    const token = localStorage.getItem('token');

    if (!token) {
        Swal.fire({
            icon: 'warning',
            title: 'Sesión Expirada',
            text: 'Tu sesión ha expirado. Por favor, inicia sesión nuevamente.',
            confirmButtonText: 'Ir al Login'
        }).then(() => {
            window.location.href = '/login';
        });
        return;
    }

    container.innerHTML = '<div class="col-12 text-center py-5"><div class="spinner-border text-primary" role="status"><span class="sr-only">Cargando...</span></div><p class="mt-3 text-muted">Cargando mega eventos...</p></div>';

    try {
        // Construir URL con parámetros de filtro
        const params = new URLSearchParams();
        if (filtrosMegaEventos.categoria !== 'todos') {
            params.append('categoria', filtrosMegaEventos.categoria);
        }
        if (filtrosMegaEventos.estado !== 'todos') {
            params.append('estado', filtrosMegaEventos.estado);
        }
        if (filtrosMegaEventos.buscar.trim() !== '') {
            params.append('buscar', filtrosMegaEventos.buscar.trim());
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

        const data = await res.json();

        if (!res.ok || !data.success) {
            container.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Error: ${data.error || 'Error al cargar mega eventos'}
                    </div>
                </div>
            `;
            return;
        }

        if (!data.mega_eventos || data.mega_eventos.length === 0) {
            container.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle fa-3x mb-3"></i>
                        <h4>No hay mega eventos registrados</h4>
                        <p class="mb-3">Comienza creando tu primer mega evento</p>
                        <a href="{{ route('ong.mega-eventos.create') }}" class="btn btn-success">
                            <i class="fas fa-plus mr-2"></i> Crear Mega Evento
                        </a>
                    </div>
                </div>
            `;
            return;
        }

        container.innerHTML = '';

        data.mega_eventos.forEach(mega => {
            const fechaInicio = mega.fecha_inicio ? new Date(mega.fecha_inicio).toLocaleDateString('es-ES', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            }) : '-';

            const fechaFin = mega.fecha_fin ? new Date(mega.fecha_fin).toLocaleDateString('es-ES', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            }) : '-';

            // Obtener primera imagen o usar placeholder
            const imagenes = Array.isArray(mega.imagenes) ? mega.imagenes.filter(img => img && img.trim() !== '') : [];
            const imagenPrincipal = imagenes.length > 0 ? buildImageUrl(imagenes[0]) : null;

            const estadoBadge = {
                'planificacion': '<span class="badge badge-secondary">Planificación</span>',
                'activo': '<span class="badge badge-success">Activo</span>',
                'en_curso': '<span class="badge badge-info">En Curso</span>',
                'finalizado': '<span class="badge badge-primary">Finalizado</span>',
                'cancelado': '<span class="badge badge-danger">Cancelado</span>'
            }[mega.estado] || '<span class="badge badge-secondary">' + (mega.estado || 'N/A') + '</span>';

            // Crear el card usando DOM para evitar problemas con comillas
            const colDiv = document.createElement('div');
            colDiv.className = 'col-md-6 col-lg-4 mb-4';
            
            const cardDiv = document.createElement('div');
            cardDiv.className = 'card h-100 border-0';
            cardDiv.style.cssText = 'box-shadow: 0 1px 3px rgba(0,0,0,0.1); transition: transform 0.2s, box-shadow 0.2s;';
            cardDiv.onmouseenter = function() {
                this.style.transform = 'translateY(-2px)';
                this.style.boxShadow = '0 4px 8px rgba(0,0,0,0.15)';
            };
            cardDiv.onmouseleave = function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '0 1px 3px rgba(0,0,0,0.1)';
            };
            
            // Imagen o placeholder minimalista
            if (imagenPrincipal) {
                const img = document.createElement('img');
                img.src = imagenPrincipal;
                img.className = 'card-img-top';
                img.style.cssText = 'height: 220px; object-fit: cover; cursor: pointer; transition: opacity 0.3s;';
                img.alt = mega.titulo || 'Mega Evento';
                img.onclick = () => window.open(imagenPrincipal, '_blank');
                img.onerror = function() {
                    this.onerror = null;
                    this.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="400" height="200"%3E%3Crect fill="%23f0f0f0" width="400" height="200"/%3E%3Ctext x="50%25" y="50%25" text-anchor="middle" dy=".3em" fill="%23999" font-family="Arial" font-size="14"%3EImagen no disponible%3C/text%3E%3C/svg%3E';
                    this.style.objectFit = 'contain';
                };
                cardDiv.appendChild(img);
            } else {
                const placeholder = document.createElement('div');
                placeholder.className = 'card-img-top d-flex align-items-center justify-content-center';
                placeholder.style.cssText = 'height: 220px; background: #f8f9fa; border-bottom: 1px solid #e9ecef;';
                placeholder.innerHTML = '<i class="fas fa-image fa-3x text-muted" style="opacity: 0.3;"></i>';
                cardDiv.appendChild(placeholder);
            }
            
            // Card body minimalista
            const cardBody = document.createElement('div');
            cardBody.className = 'card-body d-flex flex-column p-3';
            
            const titulo = document.createElement('h5');
            titulo.className = 'card-title mb-2';
            titulo.style.cssText = 'font-size: 1.1rem; font-weight: 600; color: #2c3e50;';
            titulo.textContent = mega.titulo || 'Sin título';
            
            const descripcion = document.createElement('p');
            descripcion.className = 'card-text flex-grow-1 mb-3';
            descripcion.style.cssText = 'font-size: 0.875rem; color: #6c757d; line-height: 1.5;';
            descripcion.textContent = mega.descripcion 
                ? (mega.descripcion.length > 80 ? mega.descripcion.substring(0, 80) + '...' : mega.descripcion)
                : 'Sin descripción';
            
            const infoDiv = document.createElement('div');
            infoDiv.className = 'mb-3';
            infoDiv.style.cssText = 'font-size: 0.8rem; color: #6c757d;';
            infoDiv.innerHTML = `
                <div class="mb-1">
                    <i class="far fa-calendar-alt mr-1"></i> ${fechaInicio.split(',')[0]}
                </div>
                ${mega.ubicacion ? `
                    <div class="mb-2">
                        <i class="fas fa-map-marker-alt mr-1"></i> ${mega.ubicacion.length > 30 ? mega.ubicacion.substring(0, 30) + '...' : mega.ubicacion}
                    </div>
                ` : ''}
                <div>
                    ${estadoBadge}
                    ${mega.es_publico ? '<span class="badge badge-info ml-1" style="font-size: 0.7rem;">Público</span>' : '<span class="badge badge-secondary ml-1" style="font-size: 0.7rem;">Privado</span>'}
                </div>
            `;
            
            const btnGroup = document.createElement('div');
            btnGroup.className = 'mt-auto pt-2 border-top';
            btnGroup.innerHTML = `
                <div class="btn-group w-100" role="group">
                    <a href="/ong/mega-eventos/${mega.mega_evento_id}/detalle" 
                       class="btn btn-sm btn-light border" style="font-size: 0.8rem;">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="/ong/mega-eventos/${mega.mega_evento_id}/editar" 
                       class="btn btn-sm btn-light border" style="font-size: 0.8rem;">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button onclick="eliminarMegaEvento(${mega.mega_evento_id})" 
                            class="btn btn-sm btn-light border text-danger" style="font-size: 0.8rem;">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
            
            cardBody.appendChild(titulo);
            cardBody.appendChild(descripcion);
            cardBody.appendChild(infoDiv);
            cardBody.appendChild(btnGroup);
            
            cardDiv.appendChild(cardBody);
            colDiv.appendChild(cardDiv);
            container.appendChild(colDiv);
        });

    } catch (error) {
        console.error('Error:', error);
        container.innerHTML = `
            <div class="col-12">
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Error de conexión al cargar mega eventos.
                </div>
            </div>
        `;
    }
}

document.addEventListener('DOMContentLoaded', async () => {
    // Cargar mega eventos iniciales
    await cargarMegaEventos();

    // Event listeners para filtros
    document.getElementById('filtroCategoria').addEventListener('change', function() {
        filtrosMegaEventos.categoria = this.value;
        cargarMegaEventos();
    });

    document.getElementById('filtroEstado').addEventListener('change', function() {
        filtrosMegaEventos.estado = this.value;
        cargarMegaEventos();
    });

    // Búsqueda con debounce
    let searchTimeout;
    document.getElementById('buscador').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            filtrosMegaEventos.buscar = this.value;
            cargarMegaEventos();
        }, 500);
    });

    // Botón limpiar
    document.getElementById('btnLimpiar').addEventListener('click', function() {
        document.getElementById('buscador').value = '';
        document.getElementById('filtroCategoria').value = 'todos';
        document.getElementById('filtroEstado').value = 'todos';
        filtrosMegaEventos = {
            categoria: 'todos',
            estado: 'todos',
            buscar: ''
        };
        cargarMegaEventos();
    });
});

async function eliminarMegaEvento(id) {
    const result = await Swal.fire({
        title: '¿Eliminar Mega Evento?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });

    if (!result.isConfirmed) return;

    const token = localStorage.getItem('token');

    try {
        const res = await fetch(`${API_BASE_URL}/api/mega-eventos/${id}`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        const data = await res.json();

        if (!res.ok || !data.success) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.error || 'Error al eliminar el mega evento'
            });
            return;
        }

        Swal.fire({
            icon: 'success',
            title: '¡Eliminado!',
            text: 'El mega evento ha sido eliminado correctamente',
            timer: 1500,
            timerProgressBar: true
        }).then(() => {
            location.reload();
        });

    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error de Conexión',
            text: 'No se pudo conectar con el servidor'
        });
    }
}
</script>
@endsection


@extends('layouts.adminlte')

@section('page_title', 'Mega Eventos')

@section('content_body')
<div class="container-fluid">
    <!-- Botón normal (visible al inicio) -->
    <div class="d-flex justify-content-between align-items-center mb-4" id="btnNuevoMegaEventoNormal">
        <h2 class="text-primary font-weight-bold">
            <i class="fas fa-star mr-2"></i> Mega Eventos
        </h2>
        <div>
            <button id="btnSeguimientoGeneral" class="btn btn-info btn-lg mr-2">
                <i class="fas fa-chart-line mr-2"></i> Seguimiento General
            </button>
            <a href="{{ route('ong.mega-eventos.create') }}" class="btn btn-success btn-lg">
                <i class="fas fa-plus mr-2"></i> Nuevo Mega Evento
            </a>
        </div>
    </div>

    <!-- Botón FAB circular (oculto inicialmente) -->
    <a href="{{ route('ong.mega-eventos.create') }}" id="btnNuevoMegaEventoFAB" class="fab-button" style="display: none;">
        <i class="fas fa-plus"></i>
    </a>

    <!-- Panel de Estadísticas Agregadas -->
    <div id="panelEstadisticasAgregadas" class="card mb-4 shadow-sm" style="border: none; border-radius: 12px; display: none;">
        <div class="card-header bg-white" style="border-bottom: 1px solid #f0f0f0; padding: 1.25rem;">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0" style="font-weight: 700; color: #0C2B44;">
                    <i class="fas fa-chart-bar mr-2" style="color: #00A36C;"></i>Estadísticas Agregadas de Todos los Mega Eventos
                </h5>
                <button class="btn btn-sm btn-outline-secondary" onclick="ocultarEstadisticasAgregadas()">
                    <i class="fas fa-times mr-1"></i> Cerrar
                </button>
            </div>
        </div>
        <div class="card-body p-4">
            <!-- Métricas Principales -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card bg-gradient-primary" style="border: none; border-radius: 12px;">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white text-uppercase mb-3" style="font-size: 0.8rem; opacity: .95; font-weight: 600;">Total Mega Eventos</h6>
                                    <h2 class="text-white mb-0" id="totalMegaEventos" style="font-size: 3rem; font-weight: 700;">0</h2>
                                </div>
                                <i class="fas fa-star fa-3x text-white" style="opacity: .2;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card bg-gradient-success" style="border: none; border-radius: 12px;">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white text-uppercase mb-3" style="font-size: 0.8rem; opacity: .95; font-weight: 600;">Total Participantes</h6>
                                    <h2 class="text-white mb-0" id="totalParticipantesAgregado" style="font-size: 3rem; font-weight: 700;">0</h2>
                                </div>
                                <i class="fas fa-users fa-3x text-white" style="opacity: .2;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card" style="border: none; border-radius: 12px; background: linear-gradient(135deg, #e91e63 0%, #c2185b 100%);">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white text-uppercase mb-3" style="font-size: 0.8rem; opacity: .95; font-weight: 600;">Total Reacciones</h6>
                                    <h2 class="text-white mb-0" id="totalReaccionesAgregado" style="font-size: 3rem; font-weight: 700;">0</h2>
                                </div>
                                <i class="fas fa-heart fa-3x text-white" style="opacity: .2;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card" style="border: none; border-radius: 12px; background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%);">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white text-uppercase mb-3" style="font-size: 0.8rem; opacity: .95; font-weight: 600;">Total Compartidos</h6>
                                    <h2 class="text-white mb-0" id="totalCompartidosAgregado" style="font-size: 3rem; font-weight: 700;">0</h2>
                                </div>
                                <i class="fas fa-share-alt fa-3x text-white" style="opacity: .2;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Métricas Secundarias -->
            <div class="row mb-4">
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="card shadow-sm" style="border: none; border-radius: 12px; border-left: 4px solid #00A36C;">
                        <div class="card-body p-4">
                            <h6 class="text-muted text-uppercase mb-2" style="font-size: 0.75rem; font-weight: 600;">Mega Eventos Activos</h6>
                            <h3 class="mb-0" id="megaEventosActivos" style="font-size: 2rem; font-weight: 700; color: #00A36C;">0</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="card shadow-sm" style="border: none; border-radius: 12px; border-left: 4px solid #6c757d;">
                        <div class="card-body p-4">
                            <h6 class="text-muted text-uppercase mb-2" style="font-size: 0.75rem; font-weight: 600;">Mega Eventos Finalizados</h6>
                            <h3 class="mb-0" id="megaEventosFinalizados" style="font-size: 2rem; font-weight: 700; color: #6c757d;">0</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="card shadow-sm" style="border: none; border-radius: 12px; border-left: 4px solid #17a2b8;">
                        <div class="card-body p-4">
                            <h6 class="text-muted text-uppercase mb-2" style="font-size: 0.75rem; font-weight: 600;">Promedio Participantes/Evento</h6>
                            <h3 class="mb-0" id="promedioParticipantes" style="font-size: 2rem; font-weight: 700; color: #17a2b8;">0</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de Detalle por Mega Evento -->
            <div class="card shadow-sm" style="border: none; border-radius: 12px;">
                <div class="card-header bg-white" style="border-bottom: 1px solid #f0f0f0; padding: 1.25rem;">
                    <h6 class="mb-0" style="font-weight: 600; color: #0C2B44;">
                        <i class="fas fa-list mr-2" style="color: #00A36C;"></i>Detalle por Mega Evento
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead style="background: #F5F5F5;">
                                <tr>
                                    <th style="padding: 1rem; font-weight: 600; color: #0C2B44;">Mega Evento</th>
                                    <th style="padding: 1rem; font-weight: 600; color: #0C2B44;">Estado</th>
                                    <th style="padding: 1rem; font-weight: 600; color: #0C2B44;">Participantes</th>
                                    <th style="padding: 1rem; font-weight: 600; color: #0C2B44;">Reacciones</th>
                                    <th style="padding: 1rem; font-weight: 600; color: #0C2B44;">Compartidos</th>
                                    <th style="padding: 1rem; font-weight: 600; color: #0C2B44;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tablaMegaEventosDetalle">
                                <tr>
                                    <td colspan="6" class="text-center py-3">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="sr-only">Cargando...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                    <label for="filtroCategoria" class="font-weight-bold text-dark">
                        <i class="fas fa-sliders-h mr-2 text-info"></i>Categoría
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
                    <label for="filtroEstado" class="font-weight-bold text-dark">
                        <i class="fas fa-info-circle mr-2 text-success"></i>Estado
                    </label>
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
                    <label for="buscador" class="font-weight-bold text-dark">
                        <i class="fas fa-search mr-2 text-warning"></i>Buscar
                    </label>
                    <div class="input-group">
                        <input type="text" id="buscador" class="form-control" placeholder="Buscar por título o descripción...">
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
        <div class="col-12 text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Cargando...</span>
            </div>
            <p class="mt-3 text-muted">Cargando mega eventos...</p>
        </div>
    </div>
</div>
@endsection

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
        box-shadow: 0 6px 16px rgba(12, 43, 68, 0.4);
        color: white;
        text-decoration: none;
    }

    .fab-button i {
        font-size: 1.5rem;
    }

    .fab-button.show {
        opacity: 1;
        visibility: visible;
        transform: scale(1);
    }

    .fab-button.hide {
        opacity: 0;
        visibility: hidden;
        transform: scale(0.8);
    }
</style>
@endpush

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

        // Función helper para formatear fechas desde PostgreSQL sin conversión de zona horaria
        const formatearFechaPostgreSQL = (fechaStr) => {
            if (!fechaStr) return '-';
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

        data.mega_eventos.forEach(mega => {
            const fechaInicio = formatearFechaPostgreSQL(mega.fecha_inicio);
            const fechaFin = formatearFechaPostgreSQL(mega.fecha_fin);

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
            cardDiv.className = 'card h-100 shadow-sm';
            cardDiv.style.cssText = 'border-radius: 12px; border: 1px solid #e9ecef; transition: transform 0.2s, box-shadow 0.2s; overflow: hidden;';
            cardDiv.onmouseenter = function() {
                this.style.transform = 'translateY(-4px)';
                this.style.boxShadow = '0 8px 16px rgba(0,0,0,0.15)';
            };
            cardDiv.onmouseleave = function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '0 2px 4px rgba(0,0,0,0.1)';
            };
            
            // Imagen o placeholder - clickable para ir a detalles
            if (imagenPrincipal) {
                const imgWrapper = document.createElement('div');
                imgWrapper.className = 'position-relative';
                imgWrapper.style.cssText = 'cursor: pointer; overflow: hidden;';
                imgWrapper.onclick = () => window.location.href = `/ong/mega-eventos/${mega.mega_evento_id}/detalle`;
                
                const img = document.createElement('img');
                img.src = imagenPrincipal;
                img.className = 'card-img-top';
                img.style.cssText = 'height: 220px; object-fit: cover; transition: transform 0.3s ease;';
                img.alt = mega.titulo || 'Mega Evento';
                img.onerror = function() {
                    this.onerror = null;
                    this.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="400" height="200"%3E%3Crect fill="%23f0f0f0" width="400" height="200"/%3E%3Ctext x="50%25" y="50%25" text-anchor="middle" dy=".3em" fill="%23999" font-family="Arial" font-size="14"%3EImagen no disponible%3C/text%3E%3C/svg%3E';
                    this.style.objectFit = 'contain';
                };
                
                imgWrapper.onmouseenter = function() {
                    img.style.transform = 'scale(1.05)';
                };
                imgWrapper.onmouseleave = function() {
                    img.style.transform = 'scale(1)';
                };
                
                imgWrapper.appendChild(img);
                cardDiv.appendChild(imgWrapper);
            } else {
                const placeholder = document.createElement('div');
                placeholder.className = 'card-img-top d-flex align-items-center justify-content-center position-relative';
                placeholder.style.cssText = 'height: 220px; background: #f8f9fa; border-bottom: 1px solid #e9ecef; cursor: pointer;';
                placeholder.onclick = () => window.location.href = `/ong/mega-eventos/${mega.mega_evento_id}/detalle`;
                placeholder.innerHTML = '<i class="fas fa-image fa-3x text-muted" style="opacity: 0.3;"></i>';
                cardDiv.appendChild(placeholder);
            }
            
            // Card body mejorado
            const cardBody = document.createElement('div');
            cardBody.className = 'card-body d-flex flex-column p-4';
            
            const titulo = document.createElement('h5');
            titulo.className = 'card-title mb-2';
            titulo.style.cssText = 'font-size: 1.2rem; font-weight: 700; color: #0C2B44;';
            titulo.textContent = mega.titulo || 'Sin título';
            
            const descripcion = document.createElement('p');
            descripcion.className = 'card-text flex-grow-1 mb-3';
            descripcion.style.cssText = 'font-size: 0.9rem; color: #6c757d; line-height: 1.6;';
            descripcion.textContent = mega.descripcion 
                ? (mega.descripcion.length > 100 ? mega.descripcion.substring(0, 100) + '...' : mega.descripcion)
                : 'Sin descripción';
            
            const infoDiv = document.createElement('div');
            infoDiv.className = 'mb-3';
            infoDiv.style.cssText = 'font-size: 0.85rem; color: #6c757d;';
            
            // Formatear fecha de finalización de forma más corta
            const fechaFinFormateada = mega.fecha_fin ? new Date(mega.fecha_fin).toLocaleDateString('es-ES', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            }) : 'No especificada';
            
            infoDiv.innerHTML = `
                <div class="mb-2">
                    <i class="fas fa-calendar-alt mr-2 text-info"></i> <strong>Inicio:</strong> ${fechaInicio.split(',')[0]}
                </div>
                <div class="mb-2">
                    <i class="fas fa-calendar-check mr-2 text-success"></i> <strong>Fin:</strong> ${fechaFinFormateada}
                </div>
                ${mega.ubicacion ? `
                    <div class="mb-2">
                        <i class="fas fa-map-marker-alt mr-2 text-danger"></i> ${mega.ubicacion.length > 35 ? mega.ubicacion.substring(0, 35) + '...' : mega.ubicacion}
                    </div>
                ` : ''}
                <div class="d-flex flex-wrap gap-2">
                    ${estadoBadge}
                    ${mega.es_publico ? '<span class="badge badge-info">Público</span>' : '<span class="badge badge-secondary">Privado</span>'}
                </div>
            `;
            
            const btnGroup = document.createElement('div');
            btnGroup.className = 'mt-auto pt-3 border-top';
            btnGroup.innerHTML = `
                <div class="d-flex justify-content-between align-items-center" style="gap: 0.5rem;">
                    <a href="/ong/mega-eventos/${mega.mega_evento_id}/detalle" 
                       class="btn btn-sm btn-primary flex-fill" title="Ver detalles">
                        <i class="fas fa-eye mr-1"></i> Detalles
                    </a>
                    <a href="/ong/mega-eventos/${mega.mega_evento_id}/seguimiento" 
                       class="btn btn-sm btn-info flex-fill" title="Seguimiento">
                        <i class="fas fa-chart-line mr-1"></i> Seguimiento
                    </a>
                    <a href="/ong/mega-eventos/${mega.mega_evento_id}/editar" 
                       class="btn btn-sm btn-warning" title="Editar" style="min-width: 40px;">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button onclick="eliminarMegaEvento(${mega.mega_evento_id})" 
                            class="btn btn-sm btn-danger" title="Eliminar" style="min-width: 40px;">
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

    // Control del FAB (Floating Action Button)
    const btnNormal = document.getElementById('btnNuevoMegaEventoNormal');
    const btnFAB = document.getElementById('btnNuevoMegaEventoFAB');
    let lastScrollTop = 0;

    window.addEventListener('scroll', function() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        if (scrollTop > 200) {
            // Ocultar botón normal y mostrar FAB
            if (btnNormal) btnNormal.style.display = 'none';
            if (btnFAB) {
                btnFAB.style.display = 'flex';
                btnFAB.classList.add('show');
                btnFAB.classList.remove('hide');
            }
        } else {
            // Mostrar botón normal y ocultar FAB
            if (btnNormal) btnNormal.style.display = 'flex';
            if (btnFAB) {
                btnFAB.classList.add('hide');
                btnFAB.classList.remove('show');
                setTimeout(() => {
                    if (btnFAB.classList.contains('hide')) {
                        btnFAB.style.display = 'none';
                    }
                }, 300);
            }
        }
        
        lastScrollTop = scrollTop;
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


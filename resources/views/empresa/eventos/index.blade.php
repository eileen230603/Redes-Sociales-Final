@extends('layouts.adminlte-empresa')

@section('page_title', 'Eventos Patrocinados')

@section('content_body')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-primary"><i class="fas fa-calendar-check"></i> Eventos Patrocinados</h4>
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
    const empresaId = parseInt(localStorage.getItem("id_entidad") || localStorage.getItem("id_usuario"), 10);
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
        // Construir URL con parámetros de filtro
        const params = new URLSearchParams();
        if (filtrosEmpresa.estado !== 'todos') {
            params.append('estado', filtrosEmpresa.estado);
        }
        if (filtrosEmpresa.buscar.trim() !== '') {
            params.append('buscar', filtrosEmpresa.buscar.trim());
        }

        const url = `${API_BASE_URL}/api/eventos${params.toString() ? '?' + params.toString() : ''}`;

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

        // Guardar todos los eventos para filtrado local
        todosLosEventos = data.eventos;

        // Filtrar eventos donde esta empresa es patrocinadora
        // Los patrocinadores pueden venir como strings o números, así que comparamos ambos
        let eventosPatrocinados = data.eventos.filter(evento => {
            let patrocinadores = [];
            
            // Procesar patrocinadores
            if (Array.isArray(evento.patrocinadores)) {
                patrocinadores = evento.patrocinadores;
            } else if (typeof evento.patrocinadores === 'string') {
                try {
                    const parsed = JSON.parse(evento.patrocinadores);
                    patrocinadores = Array.isArray(parsed) ? parsed : [];
                } catch (err) {
                    console.warn('Error parseando patrocinadores:', err);
                }
            }
            
            // Convertir empresaId a string y número para comparar
            const empresaIdStr = String(empresaId);
            const empresaIdNum = Number(empresaId);
            
            // Verificar si la empresa está en el array de patrocinadores
            // Comparar tanto como string como número para cubrir todos los casos
            const esPatrocinador = patrocinadores.some(p => {
                // Normalizar ambos valores a string y número para comparar
                const pNormalized = String(p).trim();
                const empresaIdNormalized = String(empresaId).trim();
                
                // Comparar como strings
                if (pNormalized === empresaIdNormalized) return true;
                
                // Comparar como números (por si acaso)
                const pNum = Number(p);
                const empresaIdNum = Number(empresaId);
                if (!isNaN(pNum) && !isNaN(empresaIdNum) && pNum === empresaIdNum) return true;
                
                return false;
            });
            
            console.log(`Evento "${evento.titulo}": patrocinadores=${JSON.stringify(patrocinadores)}, empresaId=${empresaId} (tipo: ${typeof empresaId}), esPatrocinador=${esPatrocinador}`);
            
            return esPatrocinador;
        });

        // Aplicar filtro de búsqueda local si hay texto
        if (filtrosEmpresa.buscar.trim() !== '') {
            const buscarLower = filtrosEmpresa.buscar.toLowerCase();
            eventosPatrocinados = eventosPatrocinados.filter(evento => {
                const titulo = (evento.titulo || '').toLowerCase();
                const descripcion = (evento.descripcion || '').toLowerCase();
                return titulo.includes(buscarLower) || descripcion.includes(buscarLower);
            });
        }

        if (eventosPatrocinados.length === 0) {
            cont.innerHTML = `<div class="alert alert-info">
                <p class="mb-0">No se encontraron eventos patrocinados con los filtros aplicados.</p>
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

        eventosPatrocinados.forEach(e => {
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
                        <div class="mb-3 d-flex align-items-center" style="color: #6c757d; font-size: 0.85rem;">
                            <i class="far fa-calendar-alt mr-2"></i>
                            <span>${fechaInicio}</span>
                        </div>
                        ${e.tipo_evento ? `<span class="badge badge-info mb-2" style="font-size: 0.75rem;">${e.tipo_evento}</span>` : ''}
                        <span class="badge badge-success mb-3" style="font-size: 0.75rem;">Patrocinador</span>
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
});
</script>
@stop


let filtrosExterno = {
    tipo_evento: 'todos',
    buscar: ''
};

async function cargarEventosExterno() {
    const token = localStorage.getItem("token");
    const cont = document.getElementById("listaEventos");

    if (!token) {
        cont.innerHTML = `<div class="alert alert-warning">
            <p>Debes iniciar sesión para ver los eventos.</p>
            <a href="/login" class="btn btn-primary">Iniciar sesión</a>
        </div>`;
        return;
    }

    cont.innerHTML = '<div class="col-12 text-center py-3"><div class="spinner-border text-primary" role="status"><span class="sr-only">Cargando...</span></div><p class="mt-2 text-muted">Cargando eventos...</p></div>';

    try {
        // Cargar eventos y participaciones en paralelo
        const params = new URLSearchParams();
        if (filtrosExterno.tipo_evento !== 'todos') {
            params.append('tipo_evento', filtrosExterno.tipo_evento);
        }
        if (filtrosExterno.buscar.trim() !== '') {
            params.append('buscar', filtrosExterno.buscar.trim());
        }

        const url = `${API_BASE_URL}/api/eventos${params.toString() ? '?' + params.toString() : ''}`;

        const [eventosRes, participacionesRes] = await Promise.all([
            fetch(url, {
                method: 'GET',
                headers: { 
                    "Authorization": `Bearer ${token}`,
                    "Accept": "application/json",
                    "Content-Type": "application/json"
                }
            }),
            fetch(`${API_BASE_URL}/api/participaciones/mis-eventos`, {
                method: 'GET',
                headers: { 
                    "Authorization": `Bearer ${token}`,
                    "Accept": "application/json",
                    "Content-Type": "application/json"
                }
            })
        ]);

        // Procesar respuesta de eventos
        if (!eventosRes.ok) {
            const errorData = await eventosRes.json().catch(() => ({}));
            console.error("Error response:", errorData);
            cont.innerHTML = `<div class="alert alert-danger">
                <p>Error al cargar eventos (${eventosRes.status})</p>
                <small>${errorData.error || 'Error del servidor'}</small>
            </div>`;
            return;
        }

        const data = await eventosRes.json();
        console.log("Datos recibidos:", data);
        console.log("Eventos encontrados:", data.eventos?.length || 0);

        if (!data.success) {
            cont.innerHTML = `<div class="alert alert-danger">
                <p>Error: ${data.error || 'Error desconocido'}</p>
            </div>`;
            return;
        }

        if (!data.eventos || data.eventos.length === 0) {
            cont.innerHTML = `<div class="alert alert-info">
                <p class="mb-0">No hay eventos disponibles en este momento.</p>
                <small>Total encontrados: ${data.count || 0}</small>
            </div>`;
            return;
        }

        // Procesar participaciones para saber en qué eventos está inscrito
        let eventosInscritos = new Set();
        let estadosParticipaciones = {}; // Para guardar el estado de cada participación
        
        if (participacionesRes.ok) {
            try {
                const participacionesData = await participacionesRes.json();
                if (participacionesData.success && participacionesData.eventos) {
                    participacionesData.eventos.forEach(participacion => {
                        if (participacion.evento_id) {
                            eventosInscritos.add(participacion.evento_id);
                            estadosParticipaciones[participacion.evento_id] = participacion.estado || 'pendiente';
                        }
                    });
                }
            } catch (err) {
                console.warn('Error procesando participaciones:', err);
            }
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

        data.eventos.forEach(e => {
            const fechaInicio = e.fecha_inicio ? new Date(e.fecha_inicio).toLocaleDateString('es-ES', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            }) : 'Fecha no especificada';

            // Verificar si el usuario está inscrito en este evento
            const estaInscrito = eventosInscritos.has(e.id);
            const estadoParticipacion = estadosParticipaciones[e.id] || 'pendiente';

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

            // Estado badge del evento
            const estadoBadges = {
                'borrador': '<span class="badge badge-secondary">Borrador</span>',
                'publicado': '<span class="badge badge-success">Publicado</span>',
                'cancelado': '<span class="badge badge-danger">Cancelado</span>'
            };
            const estadoBadge = estadoBadges[e.estado] || '<span class="badge badge-secondary">' + (e.estado || 'N/A') + '</span>';

            // Badge de participación
            let badgeParticipacion = '';
            if (estaInscrito) {
                let estadoColor = '';
                let estadoTexto = '';
                if (estadoParticipacion === 'aprobada') {
                    estadoColor = 'success';
                    estadoTexto = 'Aprobada';
                } else if (estadoParticipacion === 'rechazada') {
                    estadoColor = 'danger';
                    estadoTexto = 'Rechazada';
                } else {
                    estadoColor = 'warning';
                    estadoTexto = 'Pendiente';
                }
                badgeParticipacion = `<span class="badge badge-${estadoColor} mb-2" style="font-size: 0.75rem; display: inline-block;">
                    <i class="fas fa-user-check mr-1"></i>Participas (${estadoTexto})
                </span>`;
            }

            // Estilos para eventos inscritos
            const cardStyleInscrito = estaInscrito 
                ? 'background: #f8f9fa; border: 2px solid #28a745 !important;' 
                : '';
            const cardClassInscrito = estaInscrito ? 'evento-inscrito' : '';

            // Crear card con diseño minimalista
            const cardDiv = document.createElement('div');
            cardDiv.className = 'col-md-4 mb-4';
            
            cardDiv.innerHTML = `
                <div class="card border-0 shadow-sm h-100 ${cardClassInscrito}" style="border-radius: 12px; overflow: hidden; transition: transform 0.2s, box-shadow 0.2s; ${cardStyleInscrito}">
                    ${imagenPrincipal 
                        ? `<div class="position-relative" style="height: 200px; overflow: hidden; background: #f8f9fa;">
                            <img src="${imagenPrincipal}" alt="${e.titulo}" class="w-100 h-100" style="object-fit: cover;" 
                                 onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\\'http://www.w3.org/2000/svg\\' width=\\'400\\' height=\\'200\\'%3E%3Crect fill=\\'%23f8f9fa\\' width=\\'400\\' height=\\'200\\'/%3E%3Ctext x=\\'50%25\\' y=\\'50%25\\' text-anchor=\\'middle\\' dy=\\'.3em\\' fill=\\'%23adb5bd\\' font-family=\\'Arial\\' font-size=\\'14\\'%3EImagen no disponible%3C/text%3E%3C/svg%3E'; this.style.objectFit='contain'; this.style.padding='20px';">
                            <div class="position-absolute" style="top: 12px; left: 12px; right: 12px; display: flex; justify-content: space-between; align-items: flex-start;">
                                <div>
                                    <span class="badge" style="background: rgba(74, 144, 226, 0.9); color: white; font-size: 0.75rem; padding: 0.4em 0.8em; border-radius: 20px; font-weight: 500;">Evento</span>
                                    ${estaInscrito ? '<span class="badge badge-success ml-2" style="font-size: 0.7rem; padding: 0.3em 0.6em; border-radius: 15px;"><i class="fas fa-check-circle mr-1"></i>Inscrito</span>' : ''}
                                </div>
                                ${estadoBadge}
                            </div>
                           </div>`
                        : `<div class="position-relative" style="height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-calendar fa-4x text-white" style="opacity: 0.7;"></i>
                            <div class="position-absolute" style="top: 12px; left: 12px; right: 12px; display: flex; justify-content: space-between; align-items: flex-start;">
                                <div>
                                    <span class="badge" style="background: rgba(74, 144, 226, 0.9); color: white; font-size: 0.75rem; padding: 0.4em 0.8em; border-radius: 20px; font-weight: 500;">Evento</span>
                                    ${estaInscrito ? '<span class="badge badge-success ml-2" style="font-size: 0.7rem; padding: 0.3em 0.6em; border-radius: 15px;"><i class="fas fa-check-circle mr-1"></i>Inscrito</span>' : ''}
                                </div>
                                ${estadoBadge}
                            </div>
                           </div>`
                    }
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="mb-0" style="font-size: 1.1rem; font-weight: 600; color: #2c3e50; flex: 1;">${e.titulo || 'Sin título'}</h5>
                            ${estaInscrito ? '<i class="fas fa-check-circle text-success ml-2" style="font-size: 1.2rem;" title="Estás inscrito en este evento"></i>' : ''}
                        </div>
                        ${badgeParticipacion}
                        <p class="text-muted mb-3" style="font-size: 0.9rem; line-height: 1.5; color: #6c757d; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            ${e.descripcion || 'Sin descripción'}
                        </p>
                        ${e.ciudad ? `<p class="text-muted mb-2" style="font-size: 0.85rem;"><i class="fas fa-map-marker-alt mr-1"></i> ${e.ciudad}</p>` : ''}
                        <div class="mb-3 d-flex align-items-center" style="color: #6c757d; font-size: 0.85rem;">
                            <i class="far fa-calendar-alt mr-2"></i>
                            <span>${fechaInicio}</span>
                        </div>
                        ${e.tipo_evento ? `<span class="badge badge-info mb-3" style="font-size: 0.75rem;">${e.tipo_evento}</span>` : ''}
                        <a href="/externo/eventos/${e.id}/detalle" class="btn btn-sm btn-block mt-auto" style="background: ${estaInscrito ? '#28a745' : '#667eea'}; color: white; border: none; border-radius: 8px; padding: 0.5em 1.2em; font-weight: 500; transition: all 0.2s;">
                            ${estaInscrito ? '<i class="fas fa-eye mr-1"></i> Ver Detalles' : 'Ver Detalles'}
                        </a>
                    </div>
                </div>
            `;
            
            // Agregar efecto hover (mejorado para eventos inscritos)
            const card = cardDiv.querySelector('.card');
            card.onmouseenter = function() {
                this.style.transform = 'translateY(-4px)';
                if (estaInscrito) {
                    this.style.boxShadow = '0 8px 16px rgba(40, 167, 69, 0.3)';
                } else {
                    this.style.boxShadow = '0 8px 16px rgba(0,0,0,0.1)';
                }
            };
            card.onmouseleave = function() {
                this.style.transform = 'translateY(0)';
                if (estaInscrito) {
                    this.style.boxShadow = '0 1px 3px rgba(0,0,0,0.1)';
                } else {
                    this.style.boxShadow = '0 1px 3px rgba(0,0,0,0.1)';
                }
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
    await cargarEventosExterno();

    // Event listeners para filtros
    document.getElementById('filtroTipo').addEventListener('change', function() {
        filtrosExterno.tipo_evento = this.value;
        cargarEventosExterno();
    });

    // Búsqueda con debounce
    let searchTimeout;
    document.getElementById('buscador').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            filtrosExterno.buscar = this.value;
            cargarEventosExterno();
        }, 500);
    });

    // Botón limpiar
    document.getElementById('btnLimpiar').addEventListener('click', function() {
        document.getElementById('buscador').value = '';
        document.getElementById('filtroTipo').value = 'todos';
        filtrosExterno = {
            tipo_evento: 'todos',
            buscar: ''
        };
        cargarEventosExterno();
    });
});

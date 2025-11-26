// =====================================
// show-event.js - Diseño Minimalista
// =====================================

const tokenShow = localStorage.getItem("token");
const eventoIdShow = window.location.pathname.split("/")[3];

document.addEventListener("DOMContentLoaded", async () => {
    try {
        const url = `${API_BASE_URL}/api/eventos/detalle/${eventoIdShow}`;
        const res = await fetch(url, {
            headers: {
                "Authorization": `Bearer ${tokenShow}`,
                "Accept": "application/json"
            }
        });

        const data = await res.json();

        if (!data.success) {
            alert(`Error cargando detalles del evento: ${data.message || data.error || 'Error desconocido'}`);
            return;
        }

        const e = data.evento;

        // Helper para formatear fechas
        const formatFecha = (fecha) => {
            if (!fecha) return 'No especificada';
            try {
                return new Date(fecha).toLocaleString('es-ES', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            } catch (error) {
                return fecha;
            }
        };

        // Helper para construir URL de imagen
        function buildImageUrl(imgUrl) {
            if (!imgUrl || imgUrl.trim() === '') return null;
            if (imgUrl.startsWith('http://') || imgUrl.startsWith('https://')) return imgUrl;
            if (imgUrl.startsWith('/storage/')) return `${window.location.origin}${imgUrl}`;
            if (imgUrl.startsWith('storage/')) return `${window.location.origin}/${imgUrl}`;
            return `${window.location.origin}/storage/${imgUrl}`;
        }

        // Banner con imagen principal
        const banner = document.getElementById('eventBanner');
        const bannerImage = document.getElementById('bannerImage');
        if (e.imagenes && Array.isArray(e.imagenes) && e.imagenes.length > 0) {
            const primeraImagen = buildImageUrl(e.imagenes[0]);
            if (primeraImagen) {
                bannerImage.style.backgroundImage = `url(${primeraImagen})`;
            }
        }

        // Título y badges
        document.getElementById('titulo').textContent = e.titulo || 'Sin título';
        
        // Tipo de evento
        if (e.tipo_evento) {
            document.getElementById('tipoEventoBadge').textContent = e.tipo_evento;
        } else {
            document.getElementById('tipoEventoBadge').style.display = 'none';
        }

        // Estado badge
        const estadoBadges = {
            'borrador': { class: 'badge-secondary', text: 'Borrador' },
            'publicado': { class: 'badge-success', text: 'Publicado' },
            'finalizado': { class: 'badge-info', text: 'Finalizado' },
            'cancelado': { class: 'badge-danger', text: 'Cancelado' }
        };
        const estadoInfo = estadoBadges[e.estado] || { class: 'badge-secondary', text: e.estado || 'N/A' };
        const estadoBadgeEl = document.getElementById('estadoBadge');
        estadoBadgeEl.className = `badge ${estadoInfo.class}`;
        estadoBadgeEl.textContent = estadoInfo.text;

        // Descripción
        document.getElementById('descripcion').textContent = e.descripcion || 'Sin descripción disponible.';

        // Fechas
        document.getElementById('fecha_inicio').textContent = formatFecha(e.fecha_inicio);
        document.getElementById('fecha_fin').textContent = formatFecha(e.fecha_fin);
        document.getElementById('fecha_limite_inscripcion').textContent = formatFecha(e.fecha_limite_inscripcion);
        
        // Fecha de finalización (si existe)
        if (e.fecha_finalizacion) {
            document.getElementById('fechaFinalizacionContainer').style.display = 'block';
            document.getElementById('fecha_finalizacion').textContent = formatFecha(e.fecha_finalizacion);
        } else {
            document.getElementById('fechaFinalizacionContainer').style.display = 'none';
        }

        // Capacidad
        document.getElementById('capacidad_maxima').textContent = e.capacidad_maxima ? `${e.capacidad_maxima} personas` : 'Sin límite';

        // Ubicación
        document.getElementById('ciudad').textContent = e.ciudad || 'No especificada';
        document.getElementById('direccion').textContent = e.direccion || 'No especificada';

        // Mapa (si hay coordenadas)
        if (e.lat && e.lng) {
            const mapContainer = document.getElementById('mapContainer');
            mapContainer.style.display = 'block';
            const map = L.map('mapContainer').setView([e.lat, e.lng], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);
            L.marker([e.lat, e.lng]).addTo(map).bindPopup(e.direccion || e.ciudad || 'Ubicación del evento');
        }

        // Sidebar
        const estadoSidebar = document.getElementById('estadoSidebar');
        estadoSidebar.className = `badge ${estadoInfo.class}`;
        estadoSidebar.textContent = estadoInfo.text;

        document.getElementById('tipoEventoSidebar').textContent = e.tipo_evento || 'N/A';
        document.getElementById('capacidadSidebar').textContent = e.capacidad_maxima ? `${e.capacidad_maxima} personas` : 'Sin límite';

        // Inscripción abierta
        const inscripcionAbierta = document.getElementById('inscripcionAbierta');
        if (e.inscripcion_abierta) {
            inscripcionAbierta.className = 'badge badge-success';
            inscripcionAbierta.textContent = 'Abierta';
        } else {
            inscripcionAbierta.className = 'badge badge-danger';
            inscripcionAbierta.textContent = 'Cerrada';
        }

        // Patrocinadores
        if (e.patrocinadores && Array.isArray(e.patrocinadores) && e.patrocinadores.length > 0) {
            const patrocinadoresCard = document.getElementById('patrocinadoresCard');
            patrocinadoresCard.style.display = 'block';
            const patrocinadoresDiv = document.getElementById('patrocinadores');
            patrocinadoresDiv.innerHTML = '';
            e.patrocinadores.forEach(pat => {
                const nombre = typeof pat === 'object' ? (pat.nombre || 'N/A') : pat;
                const avatar = typeof pat === 'object' ? (pat.avatar || null) : null;
                const inicial = nombre.charAt(0).toUpperCase();
                
                const item = document.createElement('div');
                item.className = 'd-flex align-items-center mb-2 mr-3';
                item.style.cssText = 'background: #f8f9fa; padding: 0.5rem 0.75rem; border-radius: 8px; border-left: 3px solid #007bff;';
                
                if (avatar) {
                    item.innerHTML = `
                        <img src="${avatar}" alt="${nombre}" class="rounded-circle mr-2" style="width: 35px; height: 35px; object-fit: cover; border: 2px solid #007bff;">
                        <span style="font-weight: 500; color: #2c3e50;">${nombre}</span>
                    `;
                } else {
                    item.innerHTML = `
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-2" style="width: 35px; height: 35px; font-weight: 600; font-size: 0.9rem;">
                            ${inicial}
                        </div>
                        <span style="font-weight: 500; color: #2c3e50;">${nombre}</span>
                    `;
                }
                patrocinadoresDiv.appendChild(item);
            });
        }

        // Invitados
        if (e.invitados && Array.isArray(e.invitados) && e.invitados.length > 0) {
            const invitadosCard = document.getElementById('invitadosCard');
            invitadosCard.style.display = 'block';
            const invitadosDiv = document.getElementById('invitados');
            invitadosDiv.innerHTML = '';
            e.invitados.forEach(inv => {
                const nombre = typeof inv === 'object' ? (inv.nombre || 'N/A') : inv;
                const avatar = typeof inv === 'object' ? (inv.avatar || null) : null;
                const inicial = nombre.charAt(0).toUpperCase();
                
                const item = document.createElement('div');
                item.className = 'd-flex align-items-center mb-2 mr-3';
                item.style.cssText = 'background: #f8f9fa; padding: 0.5rem 0.75rem; border-radius: 8px; border-left: 3px solid #6c757d;';
                
                if (avatar) {
                    item.innerHTML = `
                        <img src="${avatar}" alt="${nombre}" class="rounded-circle mr-2" style="width: 35px; height: 35px; object-fit: cover; border: 2px solid #6c757d;">
                        <span style="font-weight: 500; color: #2c3e50;">${nombre}</span>
                    `;
                } else {
                    item.innerHTML = `
                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center mr-2" style="width: 35px; height: 35px; font-weight: 600; font-size: 0.9rem;">
                            ${inicial}
                        </div>
                        <span style="font-weight: 500; color: #2c3e50;">${nombre}</span>
                    `;
                }
                invitadosDiv.appendChild(item);
            });
        }

        // Galería de imágenes
        const imgDiv = document.getElementById("imagenes");
        if (e.imagenes && Array.isArray(e.imagenes) && e.imagenes.length > 0) {
            imgDiv.innerHTML = '';
            e.imagenes.forEach((imgUrl, index) => {
                const fullUrl = buildImageUrl(imgUrl);
                if (!fullUrl) return;
                
                const colDiv = document.createElement('div');
                colDiv.className = 'col-md-4 mb-3';
                colDiv.innerHTML = `
                    <div class="gallery-item" onclick="window.open('${fullUrl}', '_blank')">
                        <img src="${fullUrl}" alt="Imagen ${index + 1}" 
                             onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\\'http://www.w3.org/2000/svg\\' width=\\'400\\' height=\\'200\\'%3E%3Crect fill=\\'%23f8f9fa\\' width=\\'400\\' height=\\'200\\'/%3E%3Ctext x=\\'50%25\\' y=\\'50%25\\' text-anchor=\\'middle\\' dy=\\'.3em\\' fill=\\'%23adb5bd\\' font-family=\\'Arial\\' font-size=\\'14\\'%3EImagen no disponible%3C/text%3E%3C/svg%3E'; this.style.objectFit='contain'; this.style.padding='20px';">
                    </div>
                `;
                imgDiv.appendChild(colDiv);
            });
            } else {
            imgDiv.innerHTML = '<p class="text-muted text-center w-100">No hay imágenes disponibles</p>';
        }

        // Botón editar
        const btnEditar = document.getElementById('btnEditar');
        if (btnEditar) {
            btnEditar.href = `/ong/eventos/${e.id}/editar`;
            }

        // Cargar contador de reacciones
        await cargarContadorReacciones(eventoIdShow);

        // Cargar reacciones
        await cargarReacciones();

        // Cargar participantes (que incluye voluntarios)
        await cargarParticipantes();

    } catch (err) {
        console.error("Error:", err);
        alert(`Error cargando detalles del evento: ${err.message}`);
    }
});

// Cargar contador de reacciones
async function cargarContadorReacciones(eventoId) {
    const token = localStorage.getItem('token');
    const contador = document.getElementById('contadorReaccionesOng');

    if (!contador) return;

    try {
        const res = await fetch(`${API_BASE_URL}/api/reacciones/verificar/${eventoId}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        const data = await res.json();
        if (data.success) {
            contador.textContent = data.total_reacciones || 0;
        }
    } catch (error) {
        console.warn('Error cargando contador de reacciones:', error);
    }
}

// Cargar lista de usuarios que reaccionaron
async function cargarReacciones() {
    const container = document.getElementById('reaccionesContainer');
    if (!container) return;

    const token = localStorage.getItem('token');
    const eventoId = window.location.pathname.split("/")[3];

    try {
        container.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border" role="status" style="color: #00A36C; width: 3rem; height: 3rem;">
                    <span class="sr-only">Cargando...</span>
                </div>
                <p class="mt-3" style="color: #333333; font-weight: 500;">Cargando reacciones...</p>
            </div>
        `;

        const res = await fetch(`${API_BASE_URL}/api/reacciones/evento/${eventoId}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        const data = await res.json();

        if (!res.ok || !data.success) {
            container.innerHTML = `
                <div class="alert" style="background: #fff3cd; border: 1px solid #ffc107; color: #856404; border-radius: 8px; padding: 1rem;">
                    <i class="far fa-exclamation-triangle mr-2"></i>
                    ${data.error || 'Error al cargar reacciones'}
                </div>
            `;
            return;
        }

        if (!data.reacciones || data.reacciones.length === 0) {
            container.innerHTML = `
                <div class="text-center py-5" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); border-radius: 12px; padding: 3rem 2rem;">
                    <div style="background: rgba(255, 255, 255, 0.15); width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; backdrop-filter: blur(10px);">
                        <i class="far fa-heart" style="font-size: 2.5rem; color: white;"></i>
                    </div>
                    <h5 style="color: white; font-weight: 600; margin-bottom: 0.5rem;">Aún no hay reacciones</h5>
                    <p style="color: rgba(255, 255, 255, 0.9); margin: 0; font-size: 0.95rem;">Usuarios que han marcado este evento como favorito con un corazón aparecerán aquí</p>
                </div>
            `;
            return;
        }

        // Crear grid de usuarios que reaccionaron
        let html = '<div class="row">';
        data.reacciones.forEach(reaccion => {
            const fechaReaccion = new Date(reaccion.fecha_reaccion).toLocaleString('es-ES', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            const inicialNombre = (reaccion.nombre || 'U').charAt(0).toUpperCase();
            const fotoPerfil = reaccion.foto_perfil || null;

            html += `
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border: 1px solid #F5F5F5; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 16px rgba(12, 43, 68, 0.15)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.1)';">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                ${fotoPerfil ? `
                                    <img src="${fotoPerfil}" alt="${reaccion.nombre}" class="rounded-circle mr-3" style="width: 50px; height: 50px; object-fit: cover; border: 3px solid #00A36C;">
                                ` : `
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 50px; height: 50px; font-weight: 600; font-size: 1.2rem; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); color: white;">
                                        ${inicialNombre}
                                    </div>
                                `}
                                <div class="flex-grow-1">
                                    <h6 class="mb-1" style="color: #0C2B44; font-weight: 700; font-size: 1rem;">${reaccion.nombre || 'N/A'}</h6>
                                    <small style="color: #333333; font-size: 0.85rem;">
                                        <i class="far fa-envelope mr-1" style="color: #00A36C;"></i> ${reaccion.correo || 'N/A'}
                                    </small>
                                </div>
                                <div style="background: rgba(220, 53, 69, 0.1); width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <i class="far fa-heart" style="font-size: 1.3rem; color: #dc3545;"></i>
                                </div>
                            </div>
                            <div class="mt-3 pt-3" style="border-top: 1px solid #F5F5F5;">
                                <small style="color: #333333; font-size: 0.8rem;">
                                    <i class="far fa-clock mr-1" style="color: #00A36C;"></i> ${fechaReaccion}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        html += `<div class="mt-4 text-center"><span class="badge" style="background: #0C2B44; color: white; padding: 0.5em 1em; border-radius: 20px; font-weight: 500;">Total: ${data.total} reacción(es)</span></div>`;

        container.innerHTML = html;

    } catch (error) {
        console.error('Error cargando reacciones:', error);
        container.innerHTML = `
            <div class="alert" style="background: #f8d7da; border: 1px solid #dc3545; color: #721c24; border-radius: 8px; padding: 1rem;">
                <i class="far fa-exclamation-triangle mr-2"></i>
                Error de conexión al cargar reacciones
            </div>
        `;
    }
}

// Función para cargar participantes
async function cargarParticipantes() {
    const container = document.getElementById('participantesContainer');
    if (!container) return;

    const token = localStorage.getItem('token');
    const eventoId = window.location.pathname.split("/")[3];

    try {
        container.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border" role="status" style="color: #00A36C; width: 3rem; height: 3rem;">
                    <span class="sr-only">Cargando...</span>
                </div>
                <p class="mt-3" style="color: #333333; font-weight: 500;">Cargando participantes...</p>
            </div>
        `;

        const res = await fetch(`${API_BASE_URL}/api/participaciones/evento/${eventoId}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        const data = await res.json();

        if (!res.ok || !data.success) {
            container.innerHTML = `
                <div class="alert" style="background: #fff3cd; border: 1px solid #ffc107; color: #856404; border-radius: 8px; padding: 1rem;">
                    <i class="far fa-exclamation-triangle mr-2"></i>
                    ${data.error || 'Error al cargar participantes'}
                </div>
            `;
            return;
        }

        if (!data.participantes || data.participantes.length === 0) {
            container.innerHTML = `
                <div class="text-center py-5" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); border-radius: 12px; padding: 3rem 2rem;">
                    <div style="background: rgba(255, 255, 255, 0.15); width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; backdrop-filter: blur(10px);">
                        <i class="far fa-users" style="font-size: 2.5rem; color: white;"></i>
                    </div>
                    <h5 style="color: white; font-weight: 600; margin-bottom: 0.5rem;">No hay participantes inscritos aún</h5>
                    <p style="color: rgba(255, 255, 255, 0.9); margin: 0; font-size: 0.95rem;">Las solicitudes de participación aparecerán aquí cuando los usuarios se inscriban</p>
                </div>
            `;
            return;
        }

        // Crear lista de participantes con diseño limpio
        let html = '<div class="row">';
        data.participantes.forEach(participante => {
            const fechaInscripcion = new Date(participante.fecha_inscripcion).toLocaleDateString('es-ES', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            });

            let estadoBadge = '';
            if (participante.estado === 'aprobada') {
                estadoBadge = '<span class="badge" style="background: #00A36C; color: white; padding: 0.4em 0.8em; border-radius: 20px; font-weight: 500;">Aprobada</span>';
            } else if (participante.estado === 'rechazada') {
                estadoBadge = '<span class="badge" style="background: #dc3545; color: white; padding: 0.4em 0.8em; border-radius: 20px; font-weight: 500;">Rechazada</span>';
            } else {
                estadoBadge = '<span class="badge" style="background: #ffc107; color: #333333; padding: 0.4em 0.8em; border-radius: 20px; font-weight: 500;">Pendiente</span>';
            }

            const inicial = (participante.nombre || 'U').charAt(0).toUpperCase();
            const fotoPerfil = participante.foto_perfil || null;

            html += `
                <div class="col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border: 1px solid #F5F5F5; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 16px rgba(12, 43, 68, 0.15)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.1)';">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start mb-3">
                                ${fotoPerfil ? `
                                    <img src="${fotoPerfil}" alt="${participante.nombre}" class="rounded-circle mr-3" style="width: 55px; height: 55px; object-fit: cover; border: 3px solid #00A36C; flex-shrink: 0;">
                                ` : `
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 55px; height: 55px; font-weight: 600; font-size: 1.2rem; flex-shrink: 0; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); color: white;">
                                        ${inicial}
                                    </div>
                                `}
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="mb-0" style="color: #0C2B44; font-weight: 700; font-size: 1.05rem;">${participante.nombre || 'N/A'}</h6>
                                        ${estadoBadge}
                                    </div>
                                    <div class="mb-2">
                                        <p class="mb-1" style="color: #333333; font-size: 0.9rem;">
                                            <i class="far fa-envelope mr-2" style="color: #00A36C;"></i> ${participante.correo || 'N/A'}
                                        </p>
                                        <p class="mb-1" style="color: #333333; font-size: 0.9rem;">
                                            <i class="far fa-phone mr-2" style="color: #00A36C;"></i> ${participante.telefono || 'N/A'}
                                        </p>
                                        <p class="mb-0" style="color: #333333; font-size: 0.9rem;">
                                            <i class="far fa-calendar mr-2" style="color: #00A36C;"></i> ${fechaInscripcion}
                                        </p>
                                    </div>
                                    ${participante.estado === 'pendiente' ? `
                                        <div class="d-flex mt-3" style="gap: 0.5rem;">
                                            <button class="btn btn-sm flex-fill" onclick="aprobarParticipacion(${participante.id})" title="Aprobar" style="background: #00A36C; color: white; border: none; border-radius: 8px; font-weight: 500;">
                                                <i class="far fa-check-circle mr-1"></i> Aprobar
                                            </button>
                                            <button class="btn btn-sm flex-fill" onclick="rechazarParticipacion(${participante.id})" title="Rechazar" style="background: #dc3545; color: white; border: none; border-radius: 8px; font-weight: 500;">
                                                <i class="far fa-times-circle mr-1"></i> Rechazar
                                            </button>
                                        </div>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        html += `<div class="mt-4 text-center"><span class="badge" style="background: #0C2B44; color: white; padding: 0.5em 1em; border-radius: 20px; font-weight: 500;">Total: ${data.count} participante(s)</span></div>`;

        container.innerHTML = html;

    } catch (error) {
        console.error('Error cargando participantes:', error);
        container.innerHTML = `
            <div class="alert" style="background: #f8d7da; border: 1px solid #dc3545; color: #721c24; border-radius: 8px; padding: 1rem;">
                <i class="far fa-exclamation-triangle mr-2"></i>
                Error de conexión al cargar participantes
            </div>
        `;
    }
}

// Función para aprobar participación
async function aprobarParticipacion(participacionId) {
    if (typeof Swal === 'undefined') {
        if (!confirm('¿Estás seguro de aprobar esta participación?')) return;
    } else {
        const result = await Swal.fire({
            title: '¿Aprobar participación?',
            text: 'El participante será notificado de la aprobación',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, aprobar',
            cancelButtonText: 'Cancelar'
        });
        if (!result.isConfirmed) return;
    }

    const token = localStorage.getItem('token');

    try {
        const res = await fetch(`${API_BASE_URL}/api/participaciones/${participacionId}/aprobar`, {
            method: 'PUT',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        const data = await res.json();

        if (!res.ok || !data.success) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.error || 'Error al aprobar participación'
                });
            } else {
                alert('Error: ' + (data.error || 'Error al aprobar participación'));
            }
            return;
        }

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: '¡Aprobado!',
                text: data.message || 'Participación aprobada correctamente',
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            alert(data.message || 'Participación aprobada correctamente');
        }

        await cargarParticipantes();

    } catch (error) {
        console.error('Error aprobando participación:', error);
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error de conexión'
            });
        } else {
            alert('Error de conexión');
        }
    }
}

// Función para rechazar participación
async function rechazarParticipacion(participacionId) {
    if (typeof Swal === 'undefined') {
        if (!confirm('¿Estás seguro de rechazar esta participación?')) return;
    } else {
        const result = await Swal.fire({
            title: '¿Rechazar participación?',
            text: 'El participante será notificado del rechazo',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, rechazar',
            cancelButtonText: 'Cancelar'
        });
        if (!result.isConfirmed) return;
    }

    const token = localStorage.getItem('token');

    try {
        const res = await fetch(`${API_BASE_URL}/api/participaciones/${participacionId}/rechazar`, {
            method: 'PUT',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        const data = await res.json();

        if (!res.ok || !data.success) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.error || 'Error al rechazar participación'
                });
            } else {
                alert('Error: ' + (data.error || 'Error al rechazar participación'));
            }
            return;
        }

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'Rechazado',
                text: data.message || 'Participación rechazada correctamente',
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            alert(data.message || 'Participación rechazada correctamente');
        }

        await cargarParticipantes();

    } catch (error) {
        console.error('Error rechazando participación:', error);
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error de conexión'
            });
        } else {
            alert('Error de conexión');
        }
    }
}

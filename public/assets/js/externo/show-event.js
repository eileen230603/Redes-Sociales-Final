// =====================================
// show-event.js Externo - Diseño Minimalista
// =====================================

document.addEventListener("DOMContentLoaded", async () => {
    const id = document.getElementById("eventoId")?.value || window.location.pathname.split("/")[3];
    const token = localStorage.getItem("token");

    try {
    const res = await fetch(`${API_BASE_URL}/api/eventos/detalle/${id}`, {
            headers: { 
                "Authorization": `Bearer ${token}`,
                "Accept": "application/json"
            }
    });

    const json = await res.json();
        if (!json.success) {
            alert(`Error: ${json.error || json.message || 'Error desconocido'}`);
            return;
        }

    const e = json.evento;

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

        // Verificar si el evento está finalizado
        const eventoFinalizado = e.estado === 'finalizado' || (e.fecha_fin && new Date(e.fecha_fin) < new Date());
        
        // Guardar estado de finalización globalmente para uso en otras funciones
        window.eventoFinalizado = eventoFinalizado;

        if (eventoFinalizado) {
            // Mostrar mensaje de evento finalizado
            const mensajeFinalizado = document.getElementById('mensajeEventoFinalizado');
            const fechaFinalizacionMensaje = document.getElementById('fechaFinalizacionMensaje');
            
            if (mensajeFinalizado) {
                mensajeFinalizado.style.display = 'block';
            }
            
            if (fechaFinalizacionMensaje && e.fecha_fin) {
                fechaFinalizacionMensaje.textContent = formatFecha(e.fecha_fin);
            } else if (fechaFinalizacionMensaje && e.fecha_finalizacion) {
                fechaFinalizacionMensaje.textContent = formatFecha(e.fecha_finalizacion);
            } else if (fechaFinalizacionMensaje) {
                fechaFinalizacionMensaje.textContent = 'No especificada';
            }

            // Deshabilitar botón de compartir
            const btnCompartir = document.getElementById('btnCompartir');
            if (btnCompartir) {
                btnCompartir.disabled = true;
                btnCompartir.style.opacity = '0.5';
                btnCompartir.style.cursor = 'not-allowed';
                btnCompartir.onclick = function(event) {
                    event.preventDefault();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'info',
                            title: 'Evento finalizado',
                            text: 'Este evento fue finalizado. Ya no se puede compartir.'
                        });
                    } else {
                        alert('Este evento fue finalizado. Ya no se puede compartir.');
                    }
                    return false;
                };
            }

            // Deshabilitar botón de reaccionar
            const btnReaccionar = document.getElementById('btnReaccionar');
            if (btnReaccionar) {
                btnReaccionar.disabled = true;
                btnReaccionar.style.opacity = '0.5';
                btnReaccionar.style.cursor = 'not-allowed';
                btnReaccionar.onclick = function(event) {
                    event.preventDefault();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'info',
                            title: 'Evento finalizado',
                            text: 'Este evento fue finalizado. Ya no se puede reaccionar.'
                        });
                    } else {
                        alert('Este evento fue finalizado. Ya no se puede reaccionar.');
                    }
                    return false;
                };
            }

            // Deshabilitar botón de participar
            const btnParticipar = document.getElementById('btnParticipar');
            if (btnParticipar) {
                btnParticipar.disabled = true;
                btnParticipar.style.opacity = '0.5';
                btnParticipar.style.cursor = 'not-allowed';
                btnParticipar.onclick = function(event) {
                    event.preventDefault();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'info',
                            title: 'Evento finalizado',
                            text: 'Este evento fue finalizado. Ya no se puede participar.'
                        });
                    } else {
                        alert('Este evento fue finalizado. Ya no se puede participar.');
                    }
                    return false;
                };
            }
        }

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

        // Información del creador
        const creadorContainer = document.getElementById('creadorContainer');
        const creadorInfo = document.getElementById('creadorInfo');
        
        if (e.creador && e.creador.nombre) {
            creadorContainer.style.display = 'block';
            let creadorHtml = '';
            
            if (e.creador.foto_perfil) {
                creadorHtml = `
                    <img src="${e.creador.foto_perfil}" alt="${e.creador.nombre}" 
                         class="rounded-circle" 
                         style="width: 32px; height: 32px; object-fit: cover; border: 2px solid #667eea;">
                    <span style="color: #495057; font-weight: 500;">${e.creador.nombre}</span>
                    <span class="badge badge-info" style="font-size: 0.75rem; padding: 0.25em 0.5em;">${e.creador.tipo}</span>
                `;
            } else {
                const inicial = e.creador.nombre.charAt(0).toUpperCase();
                creadorHtml = `
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                         style="width: 32px; height: 32px; font-weight: 600; font-size: 0.9rem;">
                        ${inicial}
                    </div>
                    <span style="color: #495057; font-weight: 500;">${e.creador.nombre}</span>
                    <span class="badge badge-info" style="font-size: 0.75rem; padding: 0.25em 0.5em;">${e.creador.tipo}</span>
                `;
            }
            
            creadorInfo.innerHTML = creadorHtml;
        } else {
            creadorContainer.style.display = 'none';
        }

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

        // Galería de imágenes - Carrusel
        const carouselContainer = document.getElementById('carouselImagenes');
        const carouselInner = document.getElementById('carouselInner');
        const carouselIndicators = document.getElementById('carouselIndicators');
        const sinImagenes = document.getElementById('sinImagenes');
        if (e.imagenes && Array.isArray(e.imagenes) && e.imagenes.length > 0) {
            const imagenesValidas = e.imagenes.map(imgUrl => buildImageUrl(imgUrl)).filter(url => url);
            
            if (imagenesValidas.length > 0) {
                if (carouselContainer) carouselContainer.style.display = 'block';
                if (sinImagenes) sinImagenes.style.display = 'none';
                
                // Limpiar contenido previo
                if (carouselInner) carouselInner.innerHTML = '';
                if (carouselIndicators) carouselIndicators.innerHTML = '';
                
                imagenesValidas.forEach((fullUrl, index) => {
                    // Crear item del carrusel
                    const carouselItem = document.createElement('div');
                    carouselItem.className = `carousel-item ${index === 0 ? 'active' : ''}`;
                    carouselItem.innerHTML = `
                        <div class="d-flex justify-content-center align-items-center" style="height: 400px; background: #f8f9fa; border-radius: 12px; overflow: hidden; cursor: pointer;" onclick="mostrarImagenGaleria('${fullUrl}')">
                        <img src="${fullUrl}" alt="Imagen ${index + 1}" 
                                 class="d-block" 
                                 style="max-width: 100%; max-height: 100%; object-fit: contain; border-radius: 12px;"
                                 onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\\%27http://www.w3.org/2000/svg\\%27 width=\\%27400\\%27 height=\\%27200\\%27%3E%3Crect fill=\\%27%23f8f9fa\\%27 width=\\%27400\\%27 height=\\%27200\\%27/%3E%3Ctext x=\\%2750%25\\%27 y=\\%2750%25\\%27 text-anchor=\\%27middle\\%27 dy=\\%27.3em\\%27 fill=\\%27%23adb5bd\\%27 font-family=\\%27Arial\\%27 font-size=\\%2714\\%27%3EImagen no disponible%3C/text%3E%3C/svg%3E'; this.style.objectFit='contain'; this.style.padding='20px';">
                    </div>
                `;
                    if (carouselInner) carouselInner.appendChild(carouselItem);
                    
                    // Crear indicador
                    const indicator = document.createElement('li');
                    indicator.setAttribute('data-target', '#carouselImagenes');
                    indicator.setAttribute('data-slide-to', index);
                    if (index === 0) indicator.classList.add('active');
                    if (carouselIndicators) carouselIndicators.appendChild(indicator);
                });
                
                // Inicializar carrusel de Bootstrap con auto-play (3 segundos)
                if (typeof $ !== 'undefined' && $('#carouselImagenes').length) {
                    $('#carouselImagenes').carousel({
                        interval: 3000,
                        ride: 'carousel'
                    });
                    
                    // Pausar carrusel al pasar el mouse y reanudar al salir
                    $('#carouselImagenes').on('mouseenter', function() {
                        $(this).carousel('pause');
                    }).on('mouseleave', function() {
                        $(this).carousel('cycle');
                    });
                }
            } else {
                if (carouselContainer) carouselContainer.style.display = 'none';
                if (sinImagenes) sinImagenes.style.display = 'block';
            }
        } else {
            if (carouselContainer) carouselContainer.style.display = 'none';
            if (sinImagenes) sinImagenes.style.display = 'block';
        }

        // Verificar si ya está inscrito
        await verificarInscripcion(id);

        // Verificar y cargar estado de reacción (incluye contador de reacciones)
        await verificarReaccion(id);

        // Cargar contador de compartidos (desde backend, para que no se reinicie)
        await cargarContadorCompartidos(id);

        // Configurar botones del banner
        configurarBotonesBanner(id, e);

        // Iniciar auto-refresco de reacciones para usuarios externos
        iniciarAutoRefrescoReacciones(id);

        // Botones de acción
    const btnP = document.getElementById("btnParticipar");
    const btnC = document.getElementById("btnCancelar");

    btnP.onclick = async () => {
            // Verificar si el evento está finalizado
            const eventoFinalizado = window.eventoFinalizado || false;
            if (eventoFinalizado) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Evento finalizado',
                        text: 'Este evento fue finalizado. Ya no se puede participar.'
                    });
                } else {
                    alert('Este evento fue finalizado. Ya no se puede participar.');
                }
                return;
            }

            if (typeof Swal !== 'undefined') {
                const result = await Swal.fire({
                    title: '¿Participar en este evento?',
                    text: 'Tu participación será registrada y aprobada automáticamente',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, participar',
                    cancelButtonText: 'Cancelar'
                });
                if (!result.isConfirmed) return;
            }

        const res = await fetch(`${API_BASE_URL}/api/participaciones/inscribir`, {
            method: "POST",
            headers: {
                "Authorization": `Bearer ${token}`,
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ evento_id: id })
        });

        const data = await res.json();
        if (data.success) {
            btnP.classList.add("d-none");
            btnC.classList.remove("d-none");
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Inscripción exitosa!',
                        text: 'Tu participación ha sido registrada y aprobada automáticamente',
                        timer: 3000,
                        showConfirmButton: false
                    });
                } else {
            alert("Inscripción exitosa");
                }
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.error || "Error al inscribirse"
                    });
        } else {
            alert(data.error || "Error al inscribirse");
                }
        }
    };

    btnC.onclick = async () => {
            if (typeof Swal !== 'undefined') {
                const result = await Swal.fire({
                    title: '¿Cancelar inscripción?',
                    text: 'Esta acción cancelará tu participación en el evento',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, cancelar',
                    cancelButtonText: 'No'
                });
                if (!result.isConfirmed) return;
            }

        const res = await fetch(`${API_BASE_URL}/api/participaciones/cancelar`, {
            method: "POST",
            headers: {
                "Authorization": `Bearer ${token}`,
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ evento_id: id })
        });

        const data = await res.json();
        if (data.success) {
            btnC.classList.add("d-none");
            btnP.classList.remove("d-none");
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Inscripción cancelada',
                        text: 'Tu participación ha sido cancelada',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
            alert("Inscripción cancelada");
                }
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.error || "Error al cancelar"
                    });
        } else {
            alert(data.error || "Error al cancelar");
        }
            }
        };

    } catch (err) {
        console.error("Error:", err);
        alert(`Error cargando detalles del evento: ${err.message}`);
    }
});

// Verificar si el usuario ya está inscrito
async function verificarInscripcion(eventoId) {
    const token = localStorage.getItem("token");
    try {
        const res = await fetch(`${API_BASE_URL}/api/participaciones/mis-eventos`, {
            headers: {
                "Authorization": `Bearer ${token}`,
                "Accept": "application/json"
            }
        });
        const data = await res.json();
        if (data.success && data.eventos) {
            const estaInscrito = data.eventos.some(participacion => participacion.evento_id == eventoId);
            if (estaInscrito) {
                document.getElementById("btnParticipar").classList.add("d-none");
                document.getElementById("btnCancelar").classList.remove("d-none");
            }
        }
    } catch (error) {
        console.warn('Error verificando inscripción:', error);
    }
}

// Verificar y cargar estado de reacción
async function verificarReaccion(eventoId) {
    const token = localStorage.getItem("token");
    const btnReaccionar = document.getElementById('btnReaccionar');
    const iconoCorazon = document.getElementById('iconoCorazon');
    const textoReaccion = document.getElementById('textoReaccion');
    const contadorReacciones = document.getElementById('contadorReacciones');

    if (!btnReaccionar) return;

    try {
        const res = await fetch(`${API_BASE_URL}/api/reacciones/verificar/${eventoId}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        const data = await res.json();
        if (data.success) {
            if (data.reaccionado) {
                iconoCorazon.className = 'fas fa-heart mr-2';
                btnReaccionar.classList.remove('btn-outline-danger');
                btnReaccionar.classList.add('btn-danger');
                textoReaccion.textContent = 'Te gusta';
            } else {
                iconoCorazon.className = 'far fa-heart mr-2';
                btnReaccionar.classList.remove('btn-danger');
                btnReaccionar.classList.add('btn-outline-danger');
                textoReaccion.textContent = 'Me gusta';
            }
            contadorReacciones.textContent = data.total_reacciones || 0;
        }

        // Verificar si el evento está finalizado antes de agregar evento click
        const eventoFinalizado = window.eventoFinalizado || false;
        if (!eventoFinalizado) {
            // Agregar evento click al botón solo si no está finalizado
        btnReaccionar.onclick = async () => {
            await toggleReaccion(eventoId);
        };
        }

    } catch (error) {
        console.warn('Error verificando reacción:', error);
    }
}

// Cargar contador de compartidos (externo)
async function cargarContadorCompartidos(eventoId) {
    const contador = document.getElementById('contadorCompartidos');
    if (!contador) return;

    try {
        const res = await fetch(`${API_BASE_URL}/api/eventos/${eventoId}/compartidos/total`, {
            headers: {
                'Accept': 'application/json'
            }
        });
        const data = await res.json();
        if (data.success) {
            contador.textContent = data.total_compartidos || 0;
        }
    } catch (error) {
        console.warn('Error cargando contador de compartidos:', error);
    }
}

// Toggle reacción (agregar/quitar)
async function toggleReaccion(eventoId) {
    // Verificar si el evento está finalizado
    const eventoFinalizado = window.eventoFinalizado || false;
    if (eventoFinalizado) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'info',
                title: 'Evento finalizado',
                text: 'Este evento fue finalizado. Ya no se puede reaccionar.'
            });
        } else {
            alert('Este evento fue finalizado. Ya no se puede reaccionar.');
        }
        return;
    }

    const token = localStorage.getItem("token");
    const btnReaccionar = document.getElementById('btnReaccionar');
    const iconoCorazon = document.getElementById('iconoCorazon');
    const textoReaccion = document.getElementById('textoReaccion');
    const contadorReacciones = document.getElementById('contadorReacciones');

    try {
        const res = await fetch(`${API_BASE_URL}/api/reacciones/toggle`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ evento_id: eventoId })
        });

        const data = await res.json();
        if (data.success) {
            if (data.reaccionado) {
                iconoCorazon.className = 'fas fa-heart mr-2';
                btnReaccionar.classList.remove('btn-outline-danger');
                btnReaccionar.classList.add('btn-danger');
                textoReaccion.textContent = 'Te gusta';
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Me gusta agregado!',
                        text: 'Has marcado este evento como favorito',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            } else {
                iconoCorazon.className = 'far fa-heart mr-2';
                btnReaccionar.classList.remove('btn-danger');
                btnReaccionar.classList.add('btn-outline-danger');
                textoReaccion.textContent = 'Me gusta';
            }
            contadorReacciones.textContent = data.total_reacciones || 0;
        } else {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.error || 'Error al procesar la reacción'
                });
            }
        }
    } catch (error) {
        console.error('Error en toggle reacción:', error);
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error de conexión',
                text: 'No se pudo procesar la reacción'
            });
        }
    }
}

// Configurar botones del banner (reacción, comentar, compartir)
async function configurarBotonesBanner(eventoId, evento) {
    const btnCompartir = document.getElementById('btnCompartir');

    // Verificar si el evento está finalizado
    const eventoFinalizado = evento.estado === 'finalizado' || (evento.fecha_fin && new Date(evento.fecha_fin) < new Date());

    // Configurar botón de compartir en la barra inferior
    if (btnCompartir) {
        if (eventoFinalizado) {
            btnCompartir.onclick = (event) => {
                event.preventDefault();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Evento finalizado',
                        text: 'Este evento fue finalizado. Ya no se puede compartir.'
                    });
                } else {
                    alert('Este evento fue finalizado. Ya no se puede compartir.');
                }
                return false;
            };
        } else {
            btnCompartir.onclick = () => {
                mostrarModalCompartir();
            };
        }
    }

    // Guardar información del evento para compartir
    window.eventoParaCompartir = {
        id: eventoId,
        titulo: evento.titulo || 'Evento',
        descripcion: evento.descripcion || '',
        url: `http://10.114.190.52:8000/evento/${eventoId}/qr`,
        finalizado: eventoFinalizado
    };
}

// Mostrar modal de compartir
function mostrarModalCompartir() {
    // Verificar si el evento está finalizado
    const evento = window.eventoParaCompartir;
    if (evento && evento.finalizado) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'info',
                title: 'Evento finalizado',
                text: 'Este evento fue finalizado. Ya no se puede compartir.'
            });
        } else {
            alert('Este evento fue finalizado. Ya no se puede compartir.');
        }
        return;
    }

    const modal = document.getElementById('modalCompartir');
    if (modal) {
        if (typeof $ !== 'undefined') {
            $(modal).modal('show');
        } else {
            modal.style.display = 'block';
            modal.classList.add('show');
            document.body.classList.add('modal-open');
            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            backdrop.onclick = () => cerrarModalCompartir();
            document.body.appendChild(backdrop);
        }
    }
}

// Cerrar modal de compartir
function cerrarModalCompartir() {
    const modal = document.getElementById('modalCompartir');
    if (modal) {
        if (typeof $ !== 'undefined') {
            $(modal).modal('hide');
        } else {
            modal.style.display = 'none';
            modal.classList.remove('show');
            document.body.classList.remove('modal-open');
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
        }
    }
}

// Registrar compartido (externo autenticado)
async function registrarCompartido(eventoId, metodo) {
    const token = localStorage.getItem('token');
    if (!token) return;

    try {
        await fetch(`${API_BASE_URL}/api/eventos/${eventoId}/compartir`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ metodo })
        });

        // Actualizar contador desde backend
        await cargarContadorCompartidos(eventoId);
    } catch (error) {
        console.warn('Error registrando compartido:', error);
    }
}

// Funciones de compartir
async function copiarEnlace() {
    const evento = window.eventoParaCompartir;
    if (!evento) return;

    // Registrar compartido en backend
    await registrarCompartido(evento.id, 'link');

    // Usar la URL pública con IP para que cualquier usuario en la misma red pueda acceder
    const url = typeof getPublicUrl !== 'undefined' 
        ? getPublicUrl(`/evento/${evento.id}/qr`)
        : `http://10.114.190.52:8000/evento/${evento.id}/qr`;
    
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(url).then(() => {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: '¡Enlace copiado!',
                    text: 'El enlace se ha copiado al portapapeles',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                alert('Enlace copiado al portapapeles');
            }
            cerrarModalCompartir();
        }).catch(err => {
            console.error('Error al copiar:', err);
            fallbackCopiarEnlace(url);
        });
    } else {
        fallbackCopiarEnlace(url);
    }
}

function fallbackCopiarEnlace(url) {
    const textarea = document.createElement('textarea');
    textarea.value = url;
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    textarea.select();
    try {
        document.execCommand('copy');
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: '¡Enlace copiado!',
                text: 'El enlace se ha copiado al portapapeles',
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            alert('Enlace copiado al portapapeles');
        }
        cerrarModalCompartir();
    } catch (err) {
        console.error('Error al copiar:', err);
        alert('Error al copiar el enlace. Por favor, cópialo manualmente: ' + url);
    }
    document.body.removeChild(textarea);
}

function compartirWhatsApp() {
    const evento = window.eventoParaCompartir;
    if (!evento) return;

    const texto = `*${evento.titulo}*\n\n${evento.descripcion.substring(0, 100)}...\n\n${evento.url}`;
    const url = `https://wa.me/?text=${encodeURIComponent(texto)}`;
    window.open(url, '_blank');
    cerrarModalCompartir();
}

function compartirMessenger() {
    const evento = window.eventoParaCompartir;
    if (!evento) return;

    const url = `https://www.facebook.com/dialog/send?app_id=YOUR_APP_ID&link=${encodeURIComponent(evento.url)}&redirect_uri=${encodeURIComponent(window.location.origin)}`;
    window.open(url, '_blank');
    cerrarModalCompartir();
}

function compartirFacebook() {
    const evento = window.eventoParaCompartir;
    if (!evento) return;

    const url = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(evento.url)}`;
    window.open(url, '_blank', 'width=600,height=400');
    cerrarModalCompartir();
}

function compartirTwitter() {
    const evento = window.eventoParaCompartir;
    if (!evento) return;

    const texto = `${evento.titulo} - ${evento.url}`;
    const url = `https://twitter.com/intent/tweet?text=${encodeURIComponent(texto)}&url=${encodeURIComponent(evento.url)}`;
    window.open(url, '_blank', 'width=600,height=400');
    cerrarModalCompartir();
}

// Mostrar QR Code
async function mostrarQR() {
    const evento = window.eventoParaCompartir;
    if (!evento) return;

    // Registrar compartido en backend
    await registrarCompartido(evento.id, 'qr');

    const qrContainer = document.getElementById('qrContainer');
    const qrcodeDiv = document.getElementById('qrcode');
    
    if (!qrContainer || !qrcodeDiv) return;

    // URL pública con IP para acceso mediante QR (accesible desde otros dispositivos en la misma red)
    const qrUrl = typeof getPublicUrl !== 'undefined' 
        ? getPublicUrl(`/evento/${evento.id}/qr`)
        : `http://10.114.190.52:8000/evento/${evento.id}/qr`;
    
    // Limpiar contenido anterior
    qrcodeDiv.innerHTML = '';
    
    // Mostrar contenedor primero
    qrContainer.style.display = 'block';
    
    // Agregar indicador de carga
    qrcodeDiv.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x" style="color: #667eea;"></i><p class="mt-2" style="color: #333;">Generando QR...</p></div>';
    
    // Intentar cargar QRCode si no está disponible
    if (typeof QRCode === 'undefined') {
        console.log('QRCode no disponible, cargando librería...');
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js';
        script.onload = function() {
            console.log('QRCode cargado exitosamente');
            generarQRCode(qrUrl, qrcodeDiv);
        };
        script.onerror = function() {
            console.error('Error cargando QRCode, usando API alternativa');
            generarQRConAPI(qrUrl, qrcodeDiv);
        };
        document.head.appendChild(script);
    } else {
        generarQRCode(qrUrl, qrcodeDiv);
    }
}

// Función auxiliar para generar QR con la librería
function generarQRCode(qrUrl, qrcodeDiv) {
    try {
        QRCode.toCanvas(qrcodeDiv, qrUrl, {
            width: 250,
            margin: 2,
            color: {
                dark: '#667eea',
                light: '#FFFFFF'
            },
            errorCorrectionLevel: 'M'
        }, function (error) {
            if (error) {
                console.error('Error generando QR:', error);
                generarQRConAPI(qrUrl, qrcodeDiv);
            } else {
                // QR generado exitosamente
                const canvas = qrcodeDiv.querySelector('canvas');
                if (canvas) {
                    canvas.style.display = 'block';
                    canvas.style.margin = '0 auto';
                }
            }
        });
    } catch (error) {
        console.error('Error en generarQRCode:', error);
        generarQRConAPI(qrUrl, qrcodeDiv);
    }
}

// Función alternativa usando API de QR
function generarQRConAPI(qrUrl, qrcodeDiv) {
    // Usar API pública de QR code como alternativa
    const apiUrl = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodeURIComponent(qrUrl)}&bgcolor=FFFFFF&color=667eea`;
    qrcodeDiv.innerHTML = `<img src="${apiUrl}" alt="QR Code" style="display: block; margin: 0 auto; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);" onerror="this.parentElement.innerHTML='<div class=\'alert alert-danger\'><i class=\'fas fa-exclamation-triangle mr-2\'></i>Error al generar QR. Por favor, intenta nuevamente.</div>'">`;
}

// Auto-refresco de reacciones para usuarios externos
let refrescoReaccionesIntervalExterno = null;

function iniciarAutoRefrescoReacciones(eventoId) {
    try {
        if (refrescoReaccionesIntervalExterno) {
            clearInterval(refrescoReaccionesIntervalExterno);
        }

        // Cada 10 segundos actualiza el contador de reacciones
        refrescoReaccionesIntervalExterno = setInterval(async () => {
            try {
                const contador = document.getElementById('contadorReacciones');
                if (!contador) return;

                const res = await fetch(`${API_BASE_URL}/api/reacciones/evento/${eventoId}/total`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                const data = await res.json();
                if (data.success) {
                    const nuevoTotal = data.total_reacciones || 0;
                    const totalAnterior = parseInt(contador.textContent) || 0;
                    
                    if (nuevoTotal !== totalAnterior) {
                        contador.textContent = nuevoTotal;
                        // Animación suave cuando cambia
                        contador.style.transition = 'all 0.3s ease';
                        contador.style.transform = 'scale(1.2)';
                        setTimeout(() => {
                            contador.style.transform = 'scale(1)';
                        }, 300);
                    }
                }
            } catch (err) {
                console.warn('Error en auto-refresco de reacciones:', err);
            }
        }, 10000); // Cada 10 segundos
    } catch (error) {
        console.warn('Error iniciando auto-refresco de reacciones:', error);
    }
}


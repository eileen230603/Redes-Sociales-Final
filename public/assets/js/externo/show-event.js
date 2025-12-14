// =====================================
// show-event.js Externo - Diseño Minimalista
// =====================================

document.addEventListener("DOMContentLoaded", async () => {
    const id = document.getElementById("eventoId")?.value || window.location.pathname.split("/")[3];
    const token = localStorage.getItem("token");
    
    // Guardar el ID del evento en una variable global para uso posterior
    window.eventoIdActual = id;

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
    
    // Guardar evento en variable global para usar en modal de asistencia
    if (typeof window !== 'undefined') {
        window.eventoActualGlobal = e;
    }

        // Helper para formatear fechas
        const formatFecha = (fecha) => {
            if (!fecha) return 'No especificada';
            try {
                let fechaObj;
                
                if (typeof fecha === 'string') {
                    fecha = fecha.trim();
                    
                    // Patrones para diferentes formatos de fecha
                    const mysqlPattern = /^(\d{4})-(\d{2})-(\d{2})[\sT](\d{2}):(\d{2}):(\d{2})(?:\.\d+)?(?:Z|[+-]\d{2}:\d{2})?$/;
                    const isoPattern = /^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})(?:\.\d+)?(?:Z|[+-]\d{2}:\d{2})?$/;
                    
                    let match = fecha.match(mysqlPattern) || fecha.match(isoPattern);
                    
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
                        fechaObj = new Date(fecha);
                    }
                } else {
                    fechaObj = new Date(fecha);
                }
                
                if (isNaN(fechaObj.getTime())) {
                    console.warn('Fecha inválida:', fecha);
                    return fecha;
                }
                
                return fechaObj.toLocaleString('es-ES', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                });
            } catch (error) {
                console.error('Error formateando fecha:', error, fecha);
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
                         style="width: 32px; height: 32px; object-fit: cover; border: 2px solid #00A36C;">
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
            const mapContainer = document.getElementById('mapContainer');
        if (mapContainer) {
            if (e.lat && e.lng && !isNaN(parseFloat(e.lat)) && !isNaN(parseFloat(e.lng))) {
            mapContainer.style.display = 'block';
                
                // Esperar un momento para que el contenedor esté visible antes de inicializar el mapa
                setTimeout(() => {
                    try {
                        // Verificar que Leaflet esté disponible
                        if (typeof L === 'undefined') {
                            console.error('Leaflet no está cargado');
                            mapContainer.innerHTML = '<div class="alert alert-warning p-3 m-0">Error: La librería de mapas no está cargada. Por favor, recarga la página.</div>';
                            return;
                        }
                        
                        // Limpiar cualquier mapa anterior
                        if (window.eventoMapa) {
                            window.eventoMapa.remove();
                        }
                        
                        const lat = parseFloat(e.lat);
                        const lng = parseFloat(e.lng);
                        
                        // Inicializar el mapa
                        const map = L.map('mapContainer', {
                            zoomControl: true,
                            scrollWheelZoom: true
                        }).setView([lat, lng], 13);
                        
                        // Guardar referencia global
                        window.eventoMapa = map;
                        
                        // Agregar capa de tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '© OpenStreetMap contributors',
                            maxZoom: 19
            }).addTo(map);
                        
                        // Agregar marcador
                        const marker = L.marker([lat, lng]).addTo(map);
                        const popupContent = `
                            <div style="padding: 0.5rem; min-width: 200px;">
                                <strong style="color: #0C2B44; font-size: 1rem; display: block; margin-bottom: 0.25rem;">${e.direccion || e.ubicacion || 'Ubicación del evento'}</strong>
                                ${e.ciudad ? `<small style="color: #6c757d; display: block;">${e.ciudad}</small>` : ''}
                            </div>
                        `;
                        marker.bindPopup(popupContent).openPopup();
                        
                        // Ajustar el mapa después de que se renderice
                        setTimeout(() => {
                            map.invalidateSize();
                        }, 200);
                    } catch (error) {
                        console.error('Error inicializando mapa:', error);
                        mapContainer.innerHTML = `<div class="alert alert-danger p-3 m-0">Error al cargar el mapa: ${error.message}</div>`;
                    }
                }, 300);
            } else {
                mapContainer.style.display = 'none';
            }
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

        // Patrocinadores - Diseño mejorado con avatares
        if (e.patrocinadores && Array.isArray(e.patrocinadores) && e.patrocinadores.length > 0) {
            const patrocinadoresCard = document.getElementById('patrocinadoresCard');
            patrocinadoresCard.style.display = 'block';
            const patrocinadoresDiv = document.getElementById('patrocinadores');
            patrocinadoresDiv.innerHTML = '';
            e.patrocinadores.forEach(pat => {
                const nombre = typeof pat === 'object' ? (pat.nombre || 'N/A') : pat;
                const fotoPerfil = typeof pat === 'object' ? (pat.foto_perfil || null) : null;
                const inicial = nombre.charAt(0).toUpperCase();
                
                const item = document.createElement('div');
                item.className = 'd-flex align-items-center mb-3';
                item.style.cssText = 'background: white; padding: 1rem; border-radius: 12px; border: 1px solid #e9ecef; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: all 0.3s ease; width: 100%;';
                item.onmouseover = function() {
                    this.style.transform = 'translateY(-2px)';
                    this.style.boxShadow = '0 4px 12px rgba(0,0,0,0.12)';
                };
                item.onmouseout = function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = '0 2px 8px rgba(0,0,0,0.08)';
                };
                
                if (fotoPerfil) {
                    item.innerHTML = `
                        <img src="${fotoPerfil}" alt="${nombre}" 
                             class="rounded-circle mr-3" 
                             style="width: 60px; height: 60px; object-fit: cover; border: 3px solid #00A36C; box-shadow: 0 4px 12px rgba(0, 163, 108, 0.2); flex-shrink: 0;">
                        <div class="flex-grow-1">
                            <h6 class="mb-0" style="color: #2c3e50; font-weight: 600; font-size: 1rem;">${nombre}</h6>
                            <small style="color: #6c757d; font-size: 0.85rem;">
                                <i class="fas fa-handshake mr-1" style="color: #00A36C;"></i> Patrocinador
                            </small>
                        </div>
                    `;
                } else {
                    item.innerHTML = `
                        <div class="rounded-circle d-flex align-items-center justify-content-center mr-3" 
                             style="width: 60px; height: 60px; font-weight: 700; font-size: 1.5rem; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); color: white; border: 3px solid #00A36C; box-shadow: 0 4px 12px rgba(0, 163, 108, 0.2); flex-shrink: 0;">
                            ${inicial}
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0" style="color: #2c3e50; font-weight: 600; font-size: 1rem;">${nombre}</h6>
                            <small style="color: #6c757d; font-size: 0.85rem;">
                                <i class="fas fa-handshake mr-1" style="color: #00A36C;"></i> Patrocinador
                            </small>
                        </div>
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

        // Verificar si ya está inscrito ANTES de mostrar cualquier botón
        // Esto evita que aparezcan botones incorrectos por un segundo
        await verificarInscripcion(id);

        // Verificar y cargar estado de reacción (incluye contador de reacciones)
        await verificarReaccion(id);

        // Cargar contador de compartidos (desde backend, para que no se reinicie)
        await cargarContadorCompartidos(id);

        // Configurar botones del banner
        configurarBotonesBanner(id, e);

        // Iniciar auto-refresco de reacciones para usuarios externos
        // Auto-refresco deshabilitado - las reacciones se actualizan solo manualmente

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
            // Ocultar botón Participar de forma inmediata
            btnP.classList.add("d-none");
            btnP.style.display = 'none';
            btnP.style.visibility = 'hidden';
            
            // Mostrar botón Cancelar de forma inmediata
            btnC.classList.remove("d-none");
            btnC.style.display = '';
            btnC.style.visibility = 'visible';
            btnC.style.removeProperty('display');
            btnC.style.removeProperty('visibility');
            
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
            // Verificar nuevamente para actualizar estado de asistencia
            await verificarInscripcion(id);
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
    if (!token) {
        console.log('⚠️ No hay token, no se puede verificar inscripción');
        return;
    }
    
    // Verificar que los botones existan en el DOM primero
    const btnParticipar = document.getElementById("btnParticipar");
    const btnCancelar = document.getElementById("btnCancelar");
    
    if (!btnParticipar || !btnCancelar) {
        console.warn('⚠️ Botones no encontrados en el DOM, reintentando en 500ms...');
        setTimeout(() => verificarInscripcion(eventoId), 500);
        return;
    }
    
    try {
        // Obtener información del evento para verificar estado
        const eventoRes = await fetch(`${API_BASE_URL}/api/eventos/detalle/${eventoId}`, {
            headers: {
                "Authorization": `Bearer ${token}`,
                "Accept": "application/json"
            }
        });
        const eventoData = await eventoRes.json();
        const evento = eventoData.evento;

        // Obtener participaciones del usuario
        const res = await fetch(`${API_BASE_URL}/api/participaciones/mis-eventos`, {
            headers: {
                "Authorization": `Bearer ${token}`,
                "Accept": "application/json"
            }
        });
        const data = await res.json();
        
        if (data.success && data.eventos && Array.isArray(data.eventos)) {
            // Convertir ambos a número para comparación estricta
            const eventoIdNum = parseInt(eventoId, 10);
            
            // Buscar participación - verificar tanto evento_id como id
            const participacion = data.eventos.find(p => {
                const pEventoId = parseInt(p.evento_id || p.id || p.evento?.id, 10);
                return !isNaN(pEventoId) && pEventoId === eventoIdNum;
            });
            
            const estaInscrito = !!participacion;
            
            console.log('🔍 Verificando inscripción:', {
                eventoId: eventoId,
                eventoIdNum: eventoIdNum,
                totalEventos: data.eventos.length,
                eventosIds: data.eventos.slice(0, 5).map(e => ({ 
                    evento_id: e.evento_id, 
                    id: e.id,
                    evento: e.evento?.id,
                    tipo_evento_id: typeof e.evento_id,
                    tipo_id: typeof e.id
                })),
                participacionEncontrada: participacion ? {
                    evento_id: participacion.evento_id,
                    id: participacion.id,
                    estado: participacion.estado
                } : null,
                estaInscrito: estaInscrito
            });
            
            if (estaInscrito) {
                // Ocultar botón Participar de forma forzada
                btnParticipar.classList.add("d-none");
                btnParticipar.style.display = 'none';
                btnParticipar.style.visibility = 'hidden';
                
                // Mostrar botón Cancelar de forma inmediata
                btnCancelar.classList.remove("d-none");
                btnCancelar.style.display = '';
                btnCancelar.style.visibility = 'visible';
                btnCancelar.style.removeProperty('display');
                btnCancelar.style.removeProperty('visibility');
                
                console.log('✅ Usuario inscrito - Botón Participar ocultado, botón Cancelar mostrado');
                
                // Verificar si el evento está en curso (activo) para mostrar botón de registrar asistencia
                const btnRegistrarAsistencia = document.getElementById("btnRegistrarAsistencia");
                if (btnRegistrarAsistencia && evento) {
                    // Verificar estado dinámico del evento
                    const ahora = new Date();
                    
                    // Parsear fechas correctamente para evitar problemas de zona horaria
                    let fechaInicio = null;
                    let fechaFin = null;
                    
                    if (evento.fecha_inicio) {
                        if (typeof evento.fecha_inicio === 'string') {
                            const match = evento.fecha_inicio.trim().match(/^(\d{4})-(\d{2})-(\d{2})[\sT](\d{2}):(\d{2}):(\d{2})/);
                            if (match) {
                                const [, year, month, day, hour, minute, second] = match;
                                fechaInicio = new Date(
                                    parseInt(year, 10),
                                    parseInt(month, 10) - 1,
                                    parseInt(day, 10),
                                    parseInt(hour, 10),
                                    parseInt(minute, 10),
                                    parseInt(second || 0, 10)
                                );
                            } else {
                                fechaInicio = new Date(evento.fecha_inicio);
                            }
                        } else {
                            fechaInicio = new Date(evento.fecha_inicio);
                        }
                    }
                    
                    if (evento.fecha_fin) {
                        if (typeof evento.fecha_fin === 'string') {
                            const match = evento.fecha_fin.trim().match(/^(\d{4})-(\d{2})-(\d{2})[\sT](\d{2}):(\d{2}):(\d{2})/);
                            if (match) {
                                const [, year, month, day, hour, minute, second] = match;
                                fechaFin = new Date(
                                    parseInt(year, 10),
                                    parseInt(month, 10) - 1,
                                    parseInt(day, 10),
                                    parseInt(hour, 10),
                                    parseInt(minute, 10),
                                    parseInt(second || 0, 10)
                                );
                            } else {
                                fechaFin = new Date(evento.fecha_fin);
                            }
                        } else {
                            fechaFin = new Date(evento.fecha_fin);
                        }
                    }
                    
                    // Verificar si el evento ya terminó
                    const eventoTerminado = fechaFin && ahora > fechaFin;
                    
                    // Calcular si han pasado menos de 30 minutos desde que terminó el evento
                    let dentroDe30Minutos = false;
                    let minutosDesdeFinalizacion = 0;
                    
                    if (eventoTerminado && fechaFin) {
                        const diferenciaMs = ahora - fechaFin;
                        minutosDesdeFinalizacion = diferenciaMs / (1000 * 60); // Convertir a minutos
                        dentroDe30Minutos = minutosDesdeFinalizacion <= 30;
                    }
                    
                    // El evento permite registro de asistencia si:
                    // 1. No ha terminado, O
                    // 2. Terminó hace menos de 30 minutos
                    const puedeRegistrarAsistencia = !eventoTerminado || dentroDe30Minutos;
                    
                    // También verificar si ya marcó asistencia
                    const yaMarcado = participacion.estado_asistencia === 'asistido' || participacion.asistio === true;
                    
                    console.log('🎟️ Verificando botón de registrar asistencia:', {
                        estaInscrito: true,
                        fechaInicio: fechaInicio,
                        fechaFin: fechaFin,
                        ahora: ahora,
                        eventoTerminado: eventoTerminado,
                        minutosDesdeFinalizacion: minutosDesdeFinalizacion.toFixed(2),
                        dentroDe30Minutos: dentroDe30Minutos,
                        puedeRegistrarAsistencia: puedeRegistrarAsistencia,
                        yaMarcado: yaMarcado,
                        mostrarBoton: puedeRegistrarAsistencia && !yaMarcado
                    });
                    
                    // Mostrar botón si está inscrito, puede registrar asistencia y no ha marcado asistencia
                    if (puedeRegistrarAsistencia && !yaMarcado) {
                        btnRegistrarAsistencia.classList.remove("d-none");
                        
                        // Si el evento ya terminó, mostrar mensaje informativo
                        if (eventoTerminado && dentroDe30Minutos) {
                            const minutosRestantes = (30 - minutosDesdeFinalizacion).toFixed(0);
                            console.log(`✅ Mostrando botón de registrar asistencia (evento terminó, quedan ${minutosRestantes} minutos para registrar)`);
                        } else {
                            console.log('✅ Mostrando botón de registrar asistencia');
                        }
                    } else {
                        btnRegistrarAsistencia.classList.add("d-none");
                        if (eventoTerminado && !dentroDe30Minutos) {
                            console.log('❌ Ocultando botón: han pasado más de 30 minutos desde que terminó el evento');
                        } else if (yaMarcado) {
                            console.log('❌ Ocultando botón: ya marcó asistencia');
                        }
                    }
                }
            } else {
                // Usuario NO está inscrito
                // Ocultar botón Cancelar primero
                btnCancelar.classList.add("d-none");
                btnCancelar.style.display = 'none';
                btnCancelar.style.visibility = 'hidden';
                
                // Mostrar botón Participar de forma inmediata
                btnParticipar.classList.remove("d-none");
                btnParticipar.style.display = '';
                btnParticipar.style.visibility = 'visible';
                btnParticipar.style.removeProperty('display');
                btnParticipar.style.removeProperty('visibility');
                
                const btnRegistrarAsistencia = document.getElementById("btnRegistrarAsistencia");
                if (btnRegistrarAsistencia) {
                    btnRegistrarAsistencia.classList.add("d-none");
                    btnRegistrarAsistencia.style.display = 'none';
                }
                console.log('ℹ️ Usuario no inscrito - Botón Participar visible, botón Cancelar oculto');
            }
        } else {
            // Si no hay eventos o la respuesta no fue exitosa
            console.warn('⚠️ No se pudieron obtener las participaciones o la respuesta no fue exitosa:', {
                success: data.success,
                eventos: data.eventos ? data.eventos.length : 0,
                error: data.error
            });
            // Por defecto, mostrar botón de participar
            btnParticipar.classList.remove("d-none");
            btnParticipar.style.display = '';
            btnCancelar.classList.add("d-none");
            btnCancelar.style.display = 'none';
        }
    } catch (error) {
        console.error('❌ Error verificando inscripción:', error);
        // En caso de error, por defecto mostrar botón de participar
        if (btnParticipar) {
            btnParticipar.classList.remove("d-none");
            btnParticipar.style.display = '';
        }
        if (btnCancelar) {
            btnCancelar.classList.add("d-none");
            btnCancelar.style.display = 'none';
        }
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
        url: `http://192.168.0.7:8000/evento/${eventoId}/qr`,
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
        : `http://192.168.0.7:8000/evento/${evento.id}/qr`;
    
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
        : `http://192.168.0.7:8000/evento/${evento.id}/qr`;
    
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
                dark: '#00A36C',
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
    const apiUrl = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodeURIComponent(qrUrl)}&bgcolor=FFFFFF&color=00A36C`;
    qrcodeDiv.innerHTML = `<img src="${apiUrl}" alt="QR Code" style="display: block; margin: 0 auto; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);" onerror="this.parentElement.innerHTML='<div class=\'alert alert-danger\'><i class=\'fas fa-exclamation-triangle mr-2\'></i>Error al generar QR. Por favor, intenta nuevamente.</div>'">`;
}

// Auto-refresco de reacciones para usuarios externos
// ==============================
// Auto-refresco de reacciones (Externo) - DESHABILITADO
// ==============================
// El auto-refresco ha sido deshabilitado para mejorar el rendimiento
// Las reacciones se actualizan solo cuando el usuario interactúa con el botón


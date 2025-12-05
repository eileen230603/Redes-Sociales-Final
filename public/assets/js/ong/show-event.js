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
                let fechaObj;
                
                if (typeof fecha === 'string') {
                    // Limpiar la fecha (puede venir con espacios extra o formato ISO)
                    fecha = fecha.trim();
                    
                    // Patrones para diferentes formatos de fecha
                    // Formato MySQL: "2025-12-04 14:30:00" o "2025-12-04 14:30:00.000000"
                    const mysqlPattern = /^(\d{4})-(\d{2})-(\d{2})[\sT](\d{2}):(\d{2}):(\d{2})(?:\.\d+)?(?:Z|[+-]\d{2}:\d{2})?$/;
                    // Formato ISO: "2025-12-04T14:30:00" o "2025-12-04T14:30:00Z"
                    const isoPattern = /^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})(?:\.\d+)?(?:Z|[+-]\d{2}:\d{2})?$/;
                    
                    let match = fecha.match(mysqlPattern) || fecha.match(isoPattern);
                    
                    if (match) {
                        // Parsear manualmente para evitar conversión UTC
                        // Las fechas en la BD están en hora local, no UTC
                        const [, year, month, day, hour, minute, second] = match;
                        // Crear fecha en hora local (mes es 0-indexed en JavaScript)
                        fechaObj = new Date(
                            parseInt(year, 10),
                            parseInt(month, 10) - 1,
                            parseInt(day, 10),
                            parseInt(hour, 10),
                            parseInt(minute, 10),
                            parseInt(second || 0, 10)
                        );
                    } else {
                        // Si no coincide con ningún patrón, intentar parsear directamente
                        // pero esto puede causar problemas de zona horaria
                        fechaObj = new Date(fecha);
                    }
                } else {
                    fechaObj = new Date(fecha);
                }
                
                // Verificar que la fecha sea válida
                if (isNaN(fechaObj.getTime())) {
                    console.warn('Fecha inválida:', fecha);
                    return fecha; // Devolver original si no se puede parsear
                }
                
                // Formatear en español
                return fechaObj.toLocaleString('es-ES', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false // Usar formato 24 horas
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

        // Verificar si el evento está finalizado
        const eventoFinalizado = e.estado === 'finalizado' || (e.fecha_fin && new Date(e.fecha_fin) < new Date());
        
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
                btnCompartir.onclick = function(e) {
                    e.preventDefault();
                    alert('Este evento fue finalizado. Ya no se puede compartir.');
                    return false;
                };
            }

            // Deshabilitar botón de reacciones (solo visual, no funcional)
            const btnReacciones = document.getElementById('btnReacciones');
            if (btnReacciones) {
                btnReacciones.style.opacity = '0.5';
                btnReacciones.style.cursor = 'not-allowed';
            }

            // Ocultar sección de reacciones (botón de actualizar)
            const btnActualizarReacciones = document.querySelector('.btn-actualizar-reacciones');
            if (btnActualizarReacciones) {
                btnActualizarReacciones.style.display = 'none';
            }

            // Ocultar sección de participantes (botón de actualizar)
            const btnActualizarParticipantes = document.querySelector('button[onclick="cargarParticipantes()"]');
            if (btnActualizarParticipantes) {
                btnActualizarParticipantes.style.display = 'none';
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
        const creadorNombre = document.getElementById('creadorNombre');
        
        if (e.creador && e.creador.nombre) {
            creadorContainer.style.display = 'block';
            let creadorHtml = '';
            
            if (e.creador.foto_perfil) {
                creadorHtml = `
                    <img src="${e.creador.foto_perfil}" alt="${e.creador.nombre}" 
                         class="rounded-circle" 
                         style="width: 32px; height: 32px; object-fit: cover; border: 2px solid #00A36C;">
                    <span style="color: #333333; font-weight: 500;">${e.creador.nombre}</span>
                    <span class="badge badge-info" style="font-size: 0.75rem; padding: 0.25em 0.5em;">${e.creador.tipo}</span>
                `;
            } else {
                const inicial = e.creador.nombre.charAt(0).toUpperCase();
                creadorHtml = `
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                         style="width: 32px; height: 32px; font-weight: 600; font-size: 0.9rem;">
                        ${inicial}
                    </div>
                    <span style="color: #333333; font-weight: 500;">${e.creador.nombre}</span>
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

        // Verificar si el usuario es ONG
        const tipoUsuario = localStorage.getItem('tipo_usuario');
        const esOng = tipoUsuario === 'ONG';

        // Botón editar (solo para ONG)
        const btnEditar = document.getElementById('btnEditar');
        if (btnEditar) {
            if (esOng) {
            btnEditar.href = `/ong/eventos/${e.id}/editar`;
                btnEditar.style.display = 'inline-block';
            } else {
                btnEditar.style.display = 'none';
            }
        }

        // Configurar botón del dashboard (solo para ONG)
        const btnDashboard = document.getElementById('btnDashboard');
        if (btnDashboard) {
            if (esOng) {
                btnDashboard.href = `/ong/eventos/${e.id}/dashboard`;
                btnDashboard.style.display = 'inline-block';
            } else {
                btnDashboard.style.display = 'none';
            }
            }

        // Cargar contador de reacciones
        await cargarContadorReacciones(eventoIdShow);

        // Cargar reacciones
        await cargarReacciones();

        // Cargar participantes (que incluye voluntarios)
        // Ejecutar inmediatamente, no esperar
        await cargarParticipantes();
        
        // Cargar lista de asistencia si existe el contenedor
        if (document.getElementById('listaAsistenciaContainer')) {
            await cargarListaAsistencia();
        }

        // Cargar contador de compartidos
        await cargarContadorCompartidos(eventoIdShow);

        // Configurar botones del banner
        configurarBotonesBanner(eventoIdShow, e);
        
        // Establecer eventoIdActual para control de asistencia
        if (typeof window.eventoIdActual === 'undefined') {
            window.eventoIdActual = eventoIdShow;
        } else {
            window.eventoIdActual = eventoIdShow;
        }
        
        // Verificar si se debe mostrar el botón de control de asistencia
        if (typeof verificarMostrarBotonControlAsistencia === 'function' && e.estado_dinamico) {
            verificarMostrarBotonControlAsistencia(e.estado_dinamico);
        }

        // Iniciar auto-refresco de reacciones (ONG ve el aumento casi en tiempo real)
        iniciarAutoRefrescoReacciones(eventoIdShow);

        // Iniciar auto-refresco de reacciones (ONG ve el aumento casi en tiempo real)
        iniciarAutoRefrescoReacciones(eventoIdShow);

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
            const nuevoTotal = data.total_reacciones || 0;
            const totalAnterior = parseInt(contador.textContent) || 0;
            
            // Animación si el número cambió
            if (nuevoTotal !== totalAnterior) {
                contador.classList.add('animate');
                setTimeout(() => {
                    contador.classList.remove('animate');
                }, 500);
            }
            
            contador.textContent = nuevoTotal;
        }
    } catch (error) {
        console.warn('Error cargando contador de reacciones:', error);
    }
}

// Cargar lista de usuarios que reaccionaron
async function cargarReacciones() {
    // Verificar si el evento está finalizado
    const estadoBadge = document.getElementById('estadoBadge');
    if (estadoBadge && estadoBadge.textContent.trim() === 'Finalizado') {
        return; // No cargar reacciones si el evento está finalizado
    }
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
        data.reacciones.forEach((reaccion, index) => {
            const fechaReaccion = new Date(reaccion.fecha_reaccion).toLocaleString('es-ES', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            const inicialNombre = (reaccion.nombre || 'U').charAt(0).toUpperCase();
            const fotoPerfil = reaccion.foto_perfil || null;
            const esNoRegistrado = reaccion.tipo === 'no_registrado';
            const tipoBadge = esNoRegistrado 
                ? '<span class="badge badge-info ml-2" style="background: #17a2b8; color: white; padding: 0.25em 0.5em; border-radius: 12px; font-size: 0.75rem; font-weight: 500;"><i class="fas fa-user-clock mr-1"></i> No registrado</span>'
                : '';

            html += `
                <div class="col-md-6 col-lg-4 mb-3 reaccion-card" style="animation-delay: ${index * 0.1}s;">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border: 1px solid #F5F5F5; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 16px rgba(12, 43, 68, 0.15)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.1)';">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                ${fotoPerfil ? `
                                    <img src="${fotoPerfil}" alt="${reaccion.nombre}" class="rounded-circle mr-3" style="width: 50px; height: 50px; object-fit: cover; border: 3px solid #00A36C; animation: fadeInUp 0.5s ease-out;">
                                ` : `
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 50px; height: 50px; font-weight: 600; font-size: 1.2rem; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); color: white; animation: fadeInUp 0.5s ease-out;">
                                        ${inicialNombre}
                                    </div>
                                `}
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-1">
                                        <h6 class="mb-0" style="color: #0C2B44; font-weight: 700; font-size: 1rem;">${reaccion.nombre || 'N/A'}</h6>
                                        ${tipoBadge}
                                    </div>
                                    <small style="color: #333333; font-size: 0.85rem;">
                                        <i class="far fa-envelope mr-1" style="color: #00A36C;"></i> ${reaccion.correo || 'N/A'}
                                    </small>
                                </div>
                                <div style="background: rgba(220, 53, 69, 0.1); width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease;">
                                    <i class="far fa-heart" style="font-size: 1.3rem; color: #dc3545; transition: all 0.3s ease;"></i>
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
        
        // Agregar animación de entrada a las tarjetas de reacciones
        setTimeout(() => {
            const cards = container.querySelectorAll('.reaccion-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease-out';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        }, 100);

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

// ==============================
// Auto-refresco de reacciones (ONG)
// ==============================
let refrescoReaccionesInterval = null;

function iniciarAutoRefrescoReacciones(eventoId) {
    try {
        if (refrescoReaccionesInterval) {
            clearInterval(refrescoReaccionesInterval);
        }

        // Cada 10 segundos vuelve a consultar total y lista de reacciones
        refrescoReaccionesInterval = setInterval(async () => {
            try {
                await cargarContadorReacciones(eventoId);
                await cargarReacciones();
            } catch (err) {
                console.warn('Error en auto-refresco de reacciones:', err);
            }
        }, 10000);
    } catch (error) {
        console.warn('No se pudo iniciar el auto-refresco de reacciones:', error);
    }
}

// Función para cargar participantes (igual que cargarReacciones)
async function cargarParticipantes() {
    const container = document.getElementById('participantesContainer');
    if (!container) {
        console.warn('⚠️ Container participantesContainer no encontrado');
        return;
    }

    const token = localStorage.getItem('token');
    // Obtener eventoId directamente de la URL, igual que cargarReacciones
    const eventoId = window.location.pathname.split("/")[3];
    
    console.log('📋 Iniciando carga de participantes para evento:', eventoId);
    console.log('🔑 Token presente:', !!token);
    console.log('🌐 API_BASE_URL:', API_BASE_URL);

    try {
        container.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border" role="status" style="color: #00A36C; width: 3rem; height: 3rem;">
                    <span class="sr-only">Cargando...</span>
                </div>
                <p class="mt-3" style="color: #333333; font-weight: 500;">Cargando participantes...</p>
            </div>
        `;

        const url = `${API_BASE_URL}/api/participaciones/evento/${eventoId}`;
        console.log('🌐 URL de petición:', url);

        const res = await fetch(url, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        console.log('📡 Status de respuesta:', res.status);

        const data = await res.json();
        console.log('✅ Respuesta del servidor:', data);

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
            console.log('ℹ️ No hay participantes en este evento');
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
        
        console.log('📊 Total participantes recibidos:', data.participantes.length);
        console.log('📊 Participantes registrados:', data.participantes.filter(p => p.tipo === 'registrado').length);
        console.log('📊 Participantes no registrados:', data.participantes.filter(p => p.tipo === 'no_registrado').length);

        // Función helper para parsear fechas correctamente
        function parsearFechaLocal(fechaStr) {
            if (!fechaStr) return null;
            try {
                if (typeof fechaStr === 'string') {
                    fechaStr = fechaStr.trim();
                    const match = fechaStr.match(/^(\d{4})-(\d{2})-(\d{2})[\sT](\d{2}):(\d{2}):(\d{2})/);
                    if (match) {
                        const [, year, month, day, hour, minute, second] = match;
                        return new Date(
                            parseInt(year, 10),
                            parseInt(month, 10) - 1,
                            parseInt(day, 10),
                            parseInt(hour, 10),
                            parseInt(minute, 10),
                            parseInt(second || 0, 10)
                        );
                    }
                }
                return new Date(fechaStr);
            } catch (error) {
                return new Date(fechaStr);
            }
        }
        
        // Crear grid de participantes (igual que reacciones)
        console.log('🎨 Iniciando renderizado de participantes...');
        console.log('🎨 Datos recibidos para renderizar:', data.participantes);
        
        if (!Array.isArray(data.participantes)) {
            console.error('❌ data.participantes no es un array:', typeof data.participantes);
            container.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Error: Los datos recibidos no tienen el formato correcto.
                </div>
            `;
            return;
        }
        
        let html = '<div class="row">';
        data.participantes.forEach((participante, index) => {
            console.log(`🎨 Renderizando participante ${index + 1}:`, {
                nombre: participante.nombre,
                tipo: participante.tipo,
                estado: participante.estado
            });
            
            const fechaObj = parsearFechaLocal(participante.fecha_inscripcion);
            const fechaInscripcion = fechaObj ? fechaObj.toLocaleDateString('es-ES', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            }) : 'N/A';

            let estadoBadge = '';
            if (participante.estado === 'aprobada') {
                const asistioBadge = participante.asistio 
                    ? '<span class="badge ml-2" style="background: #28a745; color: white; padding: 0.3em 0.6em; border-radius: 15px; font-weight: 500; font-size: 0.75rem;"><i class="fas fa-check-circle mr-1"></i>Asistió</span>'
                    : '<span class="badge ml-2" style="background: #ffc107; color: #333; padding: 0.3em 0.6em; border-radius: 15px; font-weight: 500; font-size: 0.75rem;"><i class="fas fa-clock mr-1"></i>Sin asistir</span>';
                estadoBadge = '<span class="badge" style="background: #00A36C; color: white; padding: 0.4em 0.8em; border-radius: 20px; font-weight: 500;">Aprobada</span>' + asistioBadge;
            } else if (participante.estado === 'rechazada') {
                estadoBadge = '<span class="badge" style="background: #dc3545; color: white; padding: 0.4em 0.8em; border-radius: 20px; font-weight: 500;">Rechazada</span>';
            } else {
                estadoBadge = '<span class="badge" style="background: #ffc107; color: #333333; padding: 0.4em 0.8em; border-radius: 20px; font-weight: 500;">Pendiente</span>';
            }

            // Asegurar que tenemos un nombre válido
            const nombreParticipante = participante.nombre || participante.nombres || 'Sin nombre';
            const inicial = nombreParticipante.charAt(0).toUpperCase();
            const fotoPerfil = participante.foto_perfil || null;
            const esNoRegistrado = participante.tipo === 'no_registrado' || participante.tipo_usuario === 'Voluntario';
            const tipoBadge = esNoRegistrado 
                ? '<span class="badge badge-info ml-2" style="background: #17a2b8; color: white; padding: 0.25em 0.5em; border-radius: 12px; font-size: 0.75rem; font-weight: 500;"><i class="fas fa-user-clock mr-1"></i> Voluntario</span>'
                : '<span class="badge badge-primary ml-2" style="background: #007bff; color: white; padding: 0.25em 0.5em; border-radius: 12px; font-size: 0.75rem; font-weight: 500;"><i class="fas fa-user mr-1"></i> Externo</span>';

            html += `
                <div class="col-md-6 col-lg-4 mb-3 participante-card" style="animation-delay: ${index * 0.1}s;">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border: 1px solid #F5F5F5; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 16px rgba(12, 43, 68, 0.15)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.1)';">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                ${fotoPerfil ? `
                                    <img src="${fotoPerfil}" alt="${nombreParticipante}" class="rounded-circle mr-3" style="width: 50px; height: 50px; object-fit: cover; border: 3px solid #00A36C; animation: fadeInUp 0.5s ease-out;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 50px; height: 50px; font-weight: 600; font-size: 1.2rem; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); color: white; animation: fadeInUp 0.5s ease-out; display: none;">
                                        ${inicial}
                                    </div>
                                ` : `
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 50px; height: 50px; font-weight: 600; font-size: 1.2rem; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); color: white; animation: fadeInUp 0.5s ease-out;">
                                        ${inicial}
                                    </div>
                                `}
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-1 flex-wrap">
                                        <h6 class="mb-0" style="color: #0C2B44; font-weight: 700; font-size: 1rem;">${nombreParticipante}</h6>
                                        ${tipoBadge}
                                    </div>
                                    <small style="color: #333333; font-size: 0.85rem; display: block;">
                                        <i class="far fa-envelope mr-1" style="color: #00A36C;"></i> ${participante.correo || participante.email || 'N/A'}
                                    </small>
                                    ${(participante.telefono || participante.phone_number) ? `
                                        <small style="color: #333333; font-size: 0.85rem; display: block;">
                                            <i class="far fa-phone mr-1" style="color: #00A36C;"></i> ${participante.telefono || participante.phone_number}
                                        </small>
                                    ` : ''}
                                </div>
                                <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 0.25rem;">
                                    ${estadoBadge}
                                </div>
                            </div>
                            <div class="mt-3 pt-3" style="border-top: 1px solid #F5F5F5;">
                                <small style="color: #333333; font-size: 0.8rem;">
                                    <i class="far fa-clock mr-1" style="color: #00A36C;"></i> ${fechaInscripcion}
                                </small>
                                ${participante.comentario ? `
                                    <br><small style="color: #495057; font-size: 0.8rem; display: block; margin-top: 0.5rem;">
                                        <i class="fas fa-comment mr-1" style="color: #00A36C;"></i> ${participante.comentario}
                                    </small>
                                ` : ''}
                            </div>
                            ${(!esNoRegistrado && participante.estado === 'pendiente') ? `
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
            `;
        });
        html += '</div>';
        html += `<div class="mt-4 text-center"><span class="badge" style="background: #0C2B44; color: white; padding: 0.5em 1em; border-radius: 20px; font-weight: 500;">Total: ${data.count || data.participantes.length} participante(s)</span></div>`;

        console.log('🎨 HTML generado, longitud:', html.length);
        console.log('🎨 Insertando HTML en container:', container.id);
        container.innerHTML = html;
        console.log('✅ HTML insertado correctamente');
        
        // Agregar animación de entrada a las tarjetas de participantes (igual que reacciones)
        setTimeout(() => {
            const cards = container.querySelectorAll('.participante-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease-out';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        }, 100);

    } catch (error) {
        console.error('❌ Error completo cargando participantes:', error);
        console.error('❌ Stack trace:', error.stack);
        console.error('❌ Mensaje:', error.message);
        
        if (container) {
            container.innerHTML = `
                <div class="alert" style="background: #f8d7da; border: 1px solid #dc3545; color: #721c24; border-radius: 8px; padding: 1rem;">
                    <i class="far fa-exclamation-triangle mr-2"></i>
                    <strong>Error de conexión al cargar participantes</strong>
                    <p class="mb-0 mt-2"><small>${error.message || 'Error desconocido'}</small></p>
                    <button class="btn btn-sm btn-secondary mt-2" onclick="cargarParticipantes()">
                        <i class="fas fa-redo mr-1"></i> Reintentar
                    </button>
                </div>
            `;
        }
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
        // Cargar lista de asistencia si existe el contenedor
        if (document.getElementById('listaAsistenciaContainer')) {
            await cargarListaAsistencia();
        }

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
        // Cargar lista de asistencia si existe el contenedor
        if (document.getElementById('listaAsistenciaContainer')) {
            await cargarListaAsistencia();
        }

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

// Configurar botones del banner (reacción, comentar, compartir)
async function configurarBotonesBanner(eventoId, evento) {
    // Verificar si el evento está finalizado
    const eventoFinalizado = evento.estado === 'finalizado' || (evento.fecha_fin && new Date(evento.fecha_fin) < new Date());
    
    if (eventoFinalizado) {
        // Deshabilitar todos los botones de interacción
        return; // No configurar botones si el evento está finalizado
    }
    const btnCompartir = document.getElementById('btnCompartir');

    // Configurar botón de compartir en la barra inferior
    if (btnCompartir) {
        btnCompartir.onclick = () => {
            mostrarModalCompartir();
        };
    }

    // Guardar información del evento para compartir
    window.eventoParaCompartir = {
        id: eventoId,
        titulo: evento.titulo || 'Evento',
        descripcion: evento.descripcion || '',
        url: `http://10.26.15.110:8000/evento/${eventoId}/qr`
    };
}

// Mostrar modal de compartir
function mostrarModalCompartir() {
    // Verificar si el evento está finalizado
    const estadoBadge = document.getElementById('estadoBadge');
    if (estadoBadge && estadoBadge.textContent.trim() === 'Finalizado') {
        alert('Este evento fue finalizado. Ya no se puede compartir.');
        return;
    }
    
    const modal = document.getElementById('modalCompartir');
    if (modal) {
        // Usar jQuery si está disponible, sino usar vanilla JS
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

// Funciones de compartir
async function copiarEnlace() {
    const evento = window.eventoParaCompartir;
    if (!evento) return;

    // Usar la URL pública con IP para que cualquier usuario en la misma red pueda acceder
    const url = typeof getPublicUrl !== 'undefined' 
        ? getPublicUrl(`/evento/${evento.id}/qr`)
        : `http://10.26.15.110:8000/evento/${evento.id}/qr`;
    
    // Registrar compartido
    await registrarCompartido(evento.id, 'link');
    
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

    const qrContainer = document.getElementById('qrContainer');
    const qrcodeDiv = document.getElementById('qrcode');
    
    if (!qrContainer || !qrcodeDiv) return;

    // Registrar compartido
    await registrarCompartido(evento.id, 'qr');

    // URL pública con IP para acceso mediante QR (accesible desde otros dispositivos en la misma red)
    const qrUrl = typeof getPublicUrl !== 'undefined' 
        ? getPublicUrl(`/evento/${evento.id}/qr`)
        : `http://10.26.15.110:8000/evento/${evento.id}/qr`;
    
    // Limpiar contenido anterior
    qrcodeDiv.innerHTML = '';
    
    // Mostrar contenedor primero
    qrContainer.style.display = 'block';
    
    // Agregar indicador de carga
    qrcodeDiv.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x" style="color: #0C2B44;"></i><p class="mt-2" style="color: #333;">Generando QR...</p></div>';
    
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

// Registrar compartido
async function registrarCompartido(eventoId, metodo) {
    try {
        const token = localStorage.getItem('token');
        const url = token 
            ? `${API_BASE_URL}/api/eventos/${eventoId}/compartir`
            : `${API_BASE_URL}/api/eventos/${eventoId}/compartir-publico`;
        
        const headers = {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        };
        
        if (token) {
            headers['Authorization'] = `Bearer ${token}`;
        }
        
        await fetch(url, {
            method: 'POST',
            headers: headers,
            body: JSON.stringify({ metodo: metodo })
        });
        
        // Actualizar contador de compartidos
        await cargarContadorCompartidos(eventoId);
    } catch (error) {
        console.warn('Error registrando compartido:', error);
        // No mostrar error al usuario, es solo un registro
    }
}

// Cargar contador de compartidos
async function cargarContadorCompartidos(eventoId) {
    try {
        const res = await fetch(`${API_BASE_URL}/api/eventos/${eventoId}/compartidos/total`);
        const data = await res.json();
        
        if (data.success) {
            const contadorCompartidos = document.getElementById('contadorCompartidos');
            if (contadorCompartidos) {
                contadorCompartidos.textContent = data.total_compartidos || 0;
            }
        }
    } catch (error) {
        console.warn('Error cargando contador de compartidos:', error);
    }
}

// Función auxiliar para generar QR con la librería
function generarQRCode(qrUrl, qrcodeDiv) {
    try {
        QRCode.toCanvas(qrcodeDiv, qrUrl, {
            width: 250,
            margin: 2,
            color: {
                dark: '#0C2B44',
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
    const apiUrl = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodeURIComponent(qrUrl)}&bgcolor=FFFFFF&color=0C2B44`;
    qrcodeDiv.innerHTML = `<img src="${apiUrl}" alt="QR Code" style="display: block; margin: 0 auto; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);" onerror="this.parentElement.innerHTML='<div class=\'alert alert-danger\'><i class=\'fas fa-exclamation-triangle mr-2\'></i>Error al generar QR. Por favor, intenta nuevamente.</div>'">`;
}

// Función para aprobar participación no registrada
async function aprobarParticipacionNoRegistrado(participacionId) {
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
        const res = await fetch(`${API_BASE_URL}/api/participaciones-no-registradas/${participacionId}/aprobar`, {
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
        // Cargar lista de asistencia si existe el contenedor
        if (document.getElementById('listaAsistenciaContainer')) {
            await cargarListaAsistencia();
        }
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

// Función para rechazar participación no registrada
async function rechazarParticipacionNoRegistrado(participacionId) {
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
        const res = await fetch(`${API_BASE_URL}/api/participaciones-no-registradas/${participacionId}/rechazar`, {
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
                title: 'Rechazada',
                text: data.message || 'Participación rechazada correctamente',
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            alert(data.message || 'Participación rechazada correctamente');
        }

        await cargarParticipantes();
        // Cargar lista de asistencia si existe el contenedor
        if (document.getElementById('listaAsistenciaContainer')) {
            await cargarListaAsistencia();
        }
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

// =====================================
// FUNCIONES DE CONTROL DE ASISTENCIA
// =====================================

// Cargar lista de asistencia y estadísticas
async function cargarListaAsistencia() {
    const container = document.getElementById('listaAsistenciaContainer');
    const token = localStorage.getItem('token');
    const eventoId = window.location.pathname.split("/")[3];

    if (!container) return;

    try {
        container.innerHTML = `
            <div class="text-center py-3">
                <div class="spinner-border text-primary" role="status" style="color: #00A36C;">
                    <span class="sr-only">Cargando...</span>
                </div>
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
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    ${data.error || 'Error al cargar lista de asistencia'}
                </div>
            `;
            return;
        }

        const participantes = data.participantes || [];
        const aprobados = participantes.filter(p => p.estado === 'aprobada');
        const asistieron = aprobados.filter(p => p.asistio === true);
        const pendientes = aprobados.filter(p => !p.asistio);

        // Actualizar estadísticas
        const totalInscritosEl = document.getElementById('totalInscritos');
        const totalAsistieronEl = document.getElementById('totalAsistieron');
        const totalPendientesEl = document.getElementById('totalPendientes');
        
        if (totalInscritosEl) totalInscritosEl.textContent = aprobados.length;
        if (totalAsistieronEl) totalAsistieronEl.textContent = asistieron.length;
        if (totalPendientesEl) totalPendientesEl.textContent = pendientes.length;

        // Mostrar lista de asistencia
        if (aprobados.length === 0) {
            container.innerHTML = `
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle mr-2"></i>
                    No hay participantes aprobados para este evento
                </div>
            `;
            return;
        }

        let html = `
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead style="background: #f8f9fa;">
                        <tr>
                            <th style="border: none; color: #0C2B44; font-weight: 600;">Participante</th>
                            <th style="border: none; color: #0C2B44; font-weight: 600;">Ticket</th>
                            <th style="border: none; color: #0C2B44; font-weight: 600;" class="text-center">Estado</th>
                            <th style="border: none; color: #0C2B44; font-weight: 600;" class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        aprobados.forEach(participante => {
            const nombre = participante.nombre || 'N/A';
            const ticketCodigo = participante.ticket_codigo || 'N/A';
            const asistio = participante.asistio === true;
            const checkinAt = participante.checkin_at ? new Date(participante.checkin_at).toLocaleString('es-ES') : null;
            
            const estadoBadge = asistio 
                ? `<span class="badge badge-success" style="background: #00A36C; padding: 0.4em 0.8em; border-radius: 20px;">
                    <i class="fas fa-check-circle mr-1"></i>Asistió
                   </span>`
                : `<span class="badge badge-warning" style="background: #ffc107; color: #333; padding: 0.4em 0.8em; border-radius: 20px;">
                    <i class="fas fa-clock mr-1"></i>Pendiente
                   </span>`;

            const botonAccion = asistio
                ? `<span class="text-muted" style="font-size: 0.85rem;">${checkinAt}</span>`
                : `<button class="btn btn-sm btn-success" 
                           onclick="registrarAsistenciaPorId('${ticketCodigo}')"
                           style="background: #00A36C; border: none; border-radius: 8px; padding: 0.3em 0.8em;">
                    <i class="fas fa-check mr-1"></i>Marcar
                  </button>`;

            html += `
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="border: none; padding: 1rem;">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mr-2" 
                                 style="width: 35px; height: 35px; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); color: white; font-weight: 600; font-size: 0.9rem;">
                                ${nombre.charAt(0).toUpperCase()}
                            </div>
                            <div>
                                <div style="font-weight: 600; color: #0C2B44;">${nombre}</div>
                                ${participante.correo ? `<small class="text-muted">${participante.correo}</small>` : ''}
                            </div>
                        </div>
                    </td>
                    <td style="border: none; padding: 1rem;">
                        <code style="background: #f8f9fa; padding: 0.3em 0.6em; border-radius: 6px; font-size: 0.85rem; color: #0C2B44;">
                            ${ticketCodigo.substring(0, 8)}...
                        </code>
                    </td>
                    <td style="border: none; padding: 1rem;" class="text-center">
                        ${estadoBadge}
                    </td>
                    <td style="border: none; padding: 1rem;" class="text-center">
                        ${botonAccion}
                    </td>
                </tr>
            `;
        });

        html += `
                    </tbody>
                </table>
            </div>
        `;

        container.innerHTML = html;

    } catch (error) {
        console.error('Error cargando lista de asistencia:', error);
        container.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Error de conexión al cargar lista de asistencia
            </div>
        `;
    }
}

// Registrar asistencia manualmente ingresando código
async function registrarAsistenciaManual() {
    const ticketCodigo = document.getElementById('ticketCodigoInput').value.trim();
    
    if (!ticketCodigo) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Código requerido',
                text: 'Por favor ingresa o escanea el código del ticket'
            });
        } else {
            alert('Por favor ingresa el código del ticket');
        }
        return;
    }

    await registrarAsistenciaPorId(ticketCodigo);
}

// Registrar asistencia por código de ticket
// Esta función ahora delega a la función en asistencia-functions.js
async function registrarAsistenciaPorId(ticketCodigo, participacionId = null, observaciones = null) {
    // Usar la función de asistencia-functions.js si está disponible
    if (typeof window.registrarAsistenciaPorId === 'function' && window.registrarAsistenciaPorId !== registrarAsistenciaPorId) {
        return await window.registrarAsistenciaPorId(ticketCodigo, participacionId, observaciones);
    }
    
    // Fallback: implementación básica
    const token = localStorage.getItem('token');
    
    // Extraer evento_id de la URL: /ong/eventos/{id}/detalle
    const pathParts = window.location.pathname.split("/").filter(p => p !== '');
    let eventoId = null;
    
    const eventosIndex = pathParts.indexOf('eventos');
    if (eventosIndex !== -1 && pathParts[eventosIndex + 1]) {
        eventoId = pathParts[eventosIndex + 1];
    } else {
        // Fallback: usar índice 3
        const pathArray = window.location.pathname.split("/");
        eventoId = pathArray[3];
    }
    
    if (!eventoId || isNaN(eventoId)) {
        console.error('Evento ID inválido:', eventoId, 'Path:', window.location.pathname);
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo identificar el evento. Por favor, recarga la página.'
            });
        }
        return;
    }

    // Determinar modo de asistencia
    const modoAsistencia = ticketCodigo && ticketCodigo !== 'N/A' ? 'QR' : 'Manual';

    try {
        const body = {
            evento_id: parseInt(eventoId),
            modo_asistencia: modoAsistencia
        };

        if (participacionId) {
            body.participacion_id = participacionId;
        } else if (ticketCodigo && ticketCodigo !== 'N/A') {
            body.ticket_codigo = ticketCodigo.trim();
        } else {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se puede registrar asistencia sin ticket o ID de participación'
                });
            }
            return;
        }

        if (observaciones) {
            body.observaciones = observaciones;
        }

        const res = await fetch(`${API_BASE_URL}/api/participaciones/registrar-asistencia`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(body)
        });

        const data = await res.json();

        if (!res.ok || !data.success) {
            console.error('Error del servidor:', data);
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.error || 'Error al registrar asistencia',
                    footer: data.details ? JSON.stringify(data.details) : ''
                });
            } else {
                alert('Error: ' + (data.error || 'Error al registrar asistencia'));
            }
            return;
        }

        // Limpiar input
        const inputEl = document.getElementById('ticketCodigoInput');
        if (inputEl) inputEl.value = '';
        
        // Mostrar éxito
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: '¡Asistencia registrada!',
                text: data.message || 'La asistencia ha sido registrada correctamente',
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            alert(data.message || 'Asistencia registrada correctamente');
        }

        // Recargar lista
        if (typeof cargarListaAsistencia === 'function') {
            await cargarListaAsistencia();
        }
        if (typeof cargarParticipantes === 'function') {
            await cargarParticipantes();
        }

    } catch (error) {
        console.error('Error registrando asistencia:', error);
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error de conexión al registrar asistencia: ' + error.message
            });
        } else {
            alert('Error de conexión: ' + error.message);
        }
    }
}

// Activar escaneo QR (usando cámara)
function activarEscaneoQR() {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'info',
            title: 'Escaneo QR',
            html: `
                <p>Para escanear el código QR del ticket:</p>
                <ol style="text-align: left;">
                    <li>Abre la cámara de tu dispositivo</li>
                    <li>Apunta hacia el código QR del ticket</li>
                    <li>El código se escaneará automáticamente</li>
                </ol>
                <p class="mt-3">O ingresa el código manualmente en el campo de texto.</p>
            `,
            confirmButtonText: 'Entendido'
        });
    } else {
        alert('Para escanear QR, usa la cámara de tu dispositivo o ingresa el código manualmente');
    }
    
    // Enfocar el input para que el usuario pueda ingresar el código
    const inputEl = document.getElementById('ticketCodigoInput');
    if (inputEl) inputEl.focus();
}

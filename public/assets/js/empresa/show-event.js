// =====================================
// show-event.js Empresa - Diseño Minimalista
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

        // Descripción
        document.getElementById('descripcion').textContent = e.descripcion || 'Sin descripción disponible.';

        // Fechas
        document.getElementById('fecha_inicio').textContent = formatFecha(e.fecha_inicio);
        document.getElementById('fecha_fin').textContent = formatFecha(e.fecha_fin);
        document.getElementById('fecha_limite_inscripcion').textContent = formatFecha(e.fecha_limite_inscripcion);
        
        // Fecha de finalización (si existe)
        if (e.fecha_finalizacion) {
            const container = document.getElementById('fechaFinalizacionContainer');
            if (container) {
                container.style.display = 'block';
                document.getElementById('fecha_finalizacion').textContent = formatFecha(e.fecha_finalizacion);
            }
        } else {
            const container = document.getElementById('fechaFinalizacionContainer');
            if (container) {
                container.style.display = 'none';
            }
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
                         style="width: 32px; height: 32px; object-fit: cover; border: 2px solid #007bff;">
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

        // Empresas Colaboradoras (desde tabla de participaciones)
        if (e.empresas_colaboradoras && Array.isArray(e.empresas_colaboradoras) && e.empresas_colaboradoras.length > 0) {
            const colaboradorasCard = document.getElementById('colaboradorasCard');
            if (colaboradorasCard) {
                colaboradorasCard.style.display = 'block';
                const colaboradorasDiv = document.getElementById('colaboradoras');
                if (colaboradorasDiv) {
                    colaboradorasDiv.innerHTML = '';
                    e.empresas_colaboradoras.forEach(emp => {
                        const nombre = typeof emp === 'object' ? (emp.nombre || 'N/A') : emp;
                        const avatar = typeof emp === 'object' ? (emp.avatar || null) : null;
                        const estado = typeof emp === 'object' ? (emp.estado || 'asignada') : 'asignada';
                        const tipoColab = typeof emp === 'object' ? (emp.tipo_colaboracion || '') : '';
                        const inicial = nombre.charAt(0).toUpperCase();
                        
                        const item = document.createElement('div');
                        item.className = 'd-flex align-items-center mb-2 mr-3';
                        item.style.cssText = 'background: #f8f9fa; padding: 0.5rem 0.75rem; border-radius: 8px; border-left: 3px solid #28a745;';
                        
                        let contenido = '';
                        if (avatar) {
                            contenido = `<img src="${avatar}" alt="${nombre}" class="rounded-circle mr-2" style="width: 35px; height: 35px; object-fit: cover; border: 2px solid #28a745;">`;
                        } else {
                            contenido = `<div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center mr-2" style="width: 35px; height: 35px; font-weight: 600; font-size: 0.9rem;">${inicial}</div>`;
                        }
                        
                        contenido += `<div class="flex-grow-1">
                            <span style="font-weight: 500; color: #2c3e50;">${nombre}</span>
                            ${tipoColab ? `<br><small class="text-muted"><i class="fas fa-tag"></i> ${tipoColab}</small>` : ''}
                            ${estado === 'confirmada' ? '<br><span class="badge badge-success badge-sm">Confirmada</span>' : '<br><span class="badge badge-warning badge-sm">Pendiente</span>'}
                        </div>`;
                        
                        item.innerHTML = contenido;
                        colaboradorasDiv.appendChild(item);
                    });
                }
            }
        }

        // Patrocinadores - Diseño mejorado con avatares
        if (e.patrocinadores && Array.isArray(e.patrocinadores) && e.patrocinadores.length > 0) {
            const patrocinadoresCard = document.getElementById('patrocinadoresCard');
            if (patrocinadoresCard) {
            patrocinadoresCard.style.display = 'block';
            const patrocinadoresDiv = document.getElementById('patrocinadores');
                if (patrocinadoresDiv) {
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
                             style="width: 60px; height: 60px; object-fit: cover; border: 3px solid #667eea; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2); flex-shrink: 0;">
                        <div class="flex-grow-1">
                            <h6 class="mb-0" style="color: #2c3e50; font-weight: 600; font-size: 1rem;">${nombre}</h6>
                            <small style="color: #6c757d; font-size: 0.85rem;">
                                <i class="fas fa-handshake mr-1" style="color: #667eea;"></i> Patrocinador
                            </small>
                        </div>
                    `;
                } else {
                    item.innerHTML = `
                        <div class="rounded-circle d-flex align-items-center justify-content-center mr-3" 
                             style="width: 60px; height: 60px; font-weight: 700; font-size: 1.5rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: 3px solid #667eea; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2); flex-shrink: 0;">
                            ${inicial}
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0" style="color: #2c3e50; font-weight: 600; font-size: 1rem;">${nombre}</h6>
                            <small style="color: #6c757d; font-size: 0.85rem;">
                                <i class="fas fa-handshake mr-1" style="color: #667eea;"></i> Patrocinador
                            </small>
                        </div>
                    `;
                }
                patrocinadoresDiv.appendChild(item);
            });
                }
            }
        }

        // Auspiciadores (del campo JSON)
        if (e.auspiciadores && Array.isArray(e.auspiciadores) && e.auspiciadores.length > 0) {
            const auspiciadoresCard = document.getElementById('auspiciadoresCard');
            if (auspiciadoresCard) {
                auspiciadoresCard.style.display = 'block';
                const auspiciadoresDiv = document.getElementById('auspiciadores');
                if (auspiciadoresDiv) {
                    auspiciadoresDiv.innerHTML = '';
                    e.auspiciadores.forEach(aus => {
                        const nombre = typeof aus === 'object' ? (aus.nombre || 'N/A') : aus;
                        const avatar = typeof aus === 'object' ? (aus.avatar || null) : null;
                        const inicial = nombre.charAt(0).toUpperCase();
                        
                        const item = document.createElement('div');
                        item.className = 'd-flex align-items-center mb-2 mr-3';
                        item.style.cssText = 'background: #f8f9fa; padding: 0.5rem 0.75rem; border-radius: 8px; border-left: 3px solid #17a2b8;';
                        
                        if (avatar) {
                            item.innerHTML = `
                                <img src="${avatar}" alt="${nombre}" class="rounded-circle mr-2" style="width: 35px; height: 35px; object-fit: cover; border: 2px solid #17a2b8;">
                                <span style="font-weight: 500; color: #2c3e50;">${nombre}</span>
                            `;
                        } else {
                            item.innerHTML = `
                                <div class="rounded-circle bg-info text-white d-flex align-items-center justify-content-center mr-2" style="width: 35px; height: 35px; font-weight: 600; font-size: 0.9rem;">
                                    ${inicial}
                                </div>
                                <span style="font-weight: 500; color: #2c3e50;">${nombre}</span>
                            `;
                        }
                        auspiciadoresDiv.appendChild(item);
                    });
                }
            }
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

        // Cargar contador de reacciones
        await cargarContadorReacciones(id);

        // Cargar contador de compartidos
        await cargarContadorCompartidos(id);

        // Configurar botones del banner
        configurarBotonesBanner(id, e);

        // Iniciar auto-refresco de reacciones para empresas
        // Auto-refresco deshabilitado - las reacciones se actualizan solo manualmente

    } catch (err) {
        console.error("Error:", err);
        alert(`Error cargando detalles del evento: ${err.message}`);
    }
});

// Cargar contador de reacciones (solo lectura para empresas)
async function cargarContadorReacciones(eventoId) {
    const token = localStorage.getItem("token");
    const contador = document.getElementById('contadorReaccionesEmpresa');

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

// Cargar contador de compartidos (solo lectura para empresas)
async function cargarContadorCompartidos(eventoId) {
    const contador = document.getElementById('contadorCompartidosEmpresa');
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

// Auto-refresco de reacciones para empresas
// ==============================
// Auto-refresco de reacciones (Empresa) - DESHABILITADO
// ==============================
// El auto-refresco ha sido deshabilitado para mejorar el rendimiento
// Las reacciones se actualizan solo cuando el usuario interactúa con el botón

// Configurar botones del banner (reacción, comentar, compartir)
async function configurarBotonesBanner(eventoId, evento) {
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
        url: `http://10.26.0.215:8000/evento/${eventoId}/qr`
    };
}

// Mostrar modal de compartir
function mostrarModalCompartir() {
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

// Registrar compartido (empresa autenticada)
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

    // Registrar compartido
    await registrarCompartido(evento.id, 'link');

    // Usar la URL pública con IP para que cualquier usuario en la misma red pueda acceder
    const url = typeof getPublicUrl !== 'undefined' 
        ? getPublicUrl(`/evento/${evento.id}/qr`)
        : `http://10.26.0.215:8000/evento/${evento.id}/qr`;
    
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

    // Registrar compartido
    await registrarCompartido(evento.id, 'qr');

    const qrContainer = document.getElementById('qrContainer');
    const qrcodeDiv = document.getElementById('qrcode');
    
    if (!qrContainer || !qrcodeDiv) return;

    // URL pública con IP para acceso mediante QR (accesible desde otros dispositivos en la misma red)
    const qrUrl = typeof getPublicUrl !== 'undefined' 
        ? getPublicUrl(`/evento/${evento.id}/qr`)
        : `http://10.26.0.215:8000/evento/${evento.id}/qr`;
    
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



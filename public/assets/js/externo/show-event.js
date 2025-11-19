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

        // Verificar si ya está inscrito
        await verificarInscripcion(id);

        // Verificar y cargar estado de reacción
        await verificarReaccion(id);

        // Botones de acción
    const btnP = document.getElementById("btnParticipar");
    const btnC = document.getElementById("btnCancelar");

    btnP.onclick = async () => {
            if (typeof Swal !== 'undefined') {
                const result = await Swal.fire({
                    title: '¿Participar en este evento?',
                    text: 'Tu solicitud será enviada para aprobación',
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
                        text: 'Tu solicitud ha sido enviada y está pendiente de aprobación',
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

        // Agregar evento click al botón
        btnReaccionar.onclick = async () => {
            await toggleReaccion(eventoId);
        };

    } catch (error) {
        console.warn('Error verificando reacción:', error);
    }
}

// Toggle reacción (agregar/quitar)
async function toggleReaccion(eventoId) {
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


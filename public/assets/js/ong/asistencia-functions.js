// =====================================
// CONTROL DE ASISTENCIA
// =====================================

let qrStream = null;
let qrScanning = false;

// Procesar imagen QR importada desde archivo
function procesarImagenQR(event) {
    const file = event.target.files[0];
    if (!file) return;

    // Validar que sea una imagen
    if (!file.type.startsWith('image/')) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Por favor selecciona un archivo de imagen válido'
            });
        } else {
            alert('Por favor selecciona un archivo de imagen válido');
        }
        return;
    }

    const reader = new FileReader();
    reader.onload = function(e) {
        const img = new Image();
        img.onload = function() {
            // Crear canvas para procesar la imagen
            const canvas = document.createElement('canvas');
            canvas.width = img.width;
            canvas.height = img.height;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0);

            // Obtener datos de la imagen
            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);

            // Cargar jsQR si no está disponible
            if (typeof jsQR === 'undefined') {
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js';
                script.onload = () => {
                    procesarQRCode(imageData);
                };
                script.onerror = () => {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudo cargar la librería de QR. Por favor, usa el escáner de cámara.'
                        });
                    }
                };
                document.head.appendChild(script);
            } else {
                procesarQRCode(imageData);
            }
        };
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);
}

// Procesar código QR desde imagen
function procesarQRCode(imageData) {
    if (typeof jsQR === 'undefined') {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Librería de QR no disponible'
            });
        }
        return;
    }

    const code = jsQR(imageData.data, imageData.width, imageData.height);
    
    if (code) {
        // QR detectado
        const ticketCodigo = code.data.trim();
        document.getElementById('ticketCodigoInput').value = ticketCodigo;
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'QR Detectado',
                text: 'Código encontrado: ' + ticketCodigo.substring(0, 20) + '...',
                timer: 2000,
                showConfirmButton: false
            });
        }
        
        // Registrar asistencia automáticamente
        registrarAsistenciaPorId(ticketCodigo, null, null, 'QR');
    } else {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'QR no encontrado',
                text: 'No se detectó ningún código QR en la imagen. Por favor, verifica que la imagen contenga un código QR válido.'
            });
        } else {
            alert('No se detectó ningún código QR en la imagen');
        }
    }
    
    // Limpiar el input
    document.getElementById('importarImagenAsistencia').value = '';
}

// Cargar lista de asistencia
async function cargarListaAsistencia() {
    const container = document.getElementById('listaAsistenciaContainer');
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

    if (!container) return;

    try {
        container.innerHTML = `
            <div class="text-center py-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Cargando...</span>
                </div>
                <p class="mt-2 text-muted">Cargando lista de asistencia...</p>
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
                <div class="alert alert-danger" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); border: none; border-radius: 12px; padding: 1.5rem; color: white; box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);">
                    <i class="fas fa-exclamation-triangle mr-2" style="font-size: 1.2rem;"></i>
                    <strong style="font-weight: 700;">Error al cargar lista de asistencia</strong>
                    <p class="mb-0 mt-2" style="opacity: 0.95;">Por favor, intenta actualizar la página o contacta al soporte técnico.</p>
                </div>
            `;
            return;
        }

        // Filtrar solo participantes aprobados
        const aprobados = data.participantes.filter(p => p.estado === 'aprobada');
        
        // Actualizar estadísticas basadas en estado_asistencia
        const totalInscritos = aprobados.length;
        const totalAsistieron = aprobados.filter(p => (p.estado_asistencia === 'asistido' || p.asistio === true)).length;
        const totalPendientes = aprobados.filter(p => (p.estado_asistencia === 'no_asistido' || (!p.asistio && !p.estado_asistencia))).length;

        const totalInscritosEl = document.getElementById('totalInscritos');
        const totalAsistieronEl = document.getElementById('totalAsistieron');
        const totalPendientesEl = document.getElementById('totalPendientes');
        
        if (totalInscritosEl) totalInscritosEl.textContent = totalInscritos;
        if (totalAsistieronEl) totalAsistieronEl.textContent = totalAsistieron;
        if (totalPendientesEl) totalPendientesEl.textContent = totalPendientes;

        // Generar tabla de asistencia
        let html = `
            <div class="table-responsive" style="border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);">
                <table class="table table-hover mb-0" style="margin-bottom: 0;">
                    <thead style="background: linear-gradient(135deg, #0C2B44 0%, #1a4a6b 100%);">
                        <tr>
                            <th style="border: none; color: #ffffff; font-weight: 700; padding: 1.25rem; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.5px;">Participante</th>
                            <th style="border: none; color: #ffffff; font-weight: 700; padding: 1.25rem; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.5px;">Ticket</th>
                            <th style="border: none; color: #ffffff; font-weight: 700; padding: 1.25rem; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.5px;" class="text-center">Estado</th>
                            <th style="border: none; color: #ffffff; font-weight: 700; padding: 1.25rem; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.5px;" class="text-center">Hora Check-in</th>
                            <th style="border: none; color: #ffffff; font-weight: 700; padding: 1.25rem; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.5px;" class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody style="background: #ffffff;">
        `;

        // Ordenar: primero los que asistieron, luego los pendientes
        const asistieron = aprobados.filter(p => p.estado_asistencia === 'asistido' || p.asistio === true);
        const pendientes = aprobados.filter(p => p.estado_asistencia !== 'asistido' && p.asistio !== true);
        const ordenados = [...asistieron, ...pendientes];

        ordenados.forEach(participante => {
            const nombre = participante.nombre || 'N/A';
            const ticketCodigo = participante.ticket_codigo || 'N/A';
            const asistio = participante.asistio === true;
            const tipoUsuario = participante.tipo_usuario || (participante.tipo === 'no_registrado' ? 'Voluntario' : 'Externo');
            const checkinAt = participante.checkin_at ? new Date(participante.checkin_at).toLocaleString('es-ES', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            }) : null;
            
            // Usar estado_asistencia para mostrar el estado correcto
            const estadoAsistencia = participante.estado_asistencia || 'no_asistido';
            let estadoBadge = '';
            
            switch(estadoAsistencia) {
                case 'asistido':
                    estadoBadge = `<span class="badge badge-success" style="background: #00A36C; padding: 0.4em 0.8em; border-radius: 20px;">
                        <i class="fas fa-check-circle mr-1"></i>Asistió
                       </span>`;
                    break;
                case 'en_revision':
                    estadoBadge = `<span class="badge badge-info" style="background: #17a2b8; padding: 0.4em 0.8em; border-radius: 20px;">
                        <i class="fas fa-hourglass-half mr-1"></i>En Revisión
                       </span>`;
                    break;
                case 'no_asistido':
                default:
                    estadoBadge = `<span class="badge badge-warning" style="background: #ffc107; color: #333; padding: 0.4em 0.8em; border-radius: 20px;">
                        <i class="fas fa-times-circle mr-1"></i>No Asistió
                       </span>`;
                    break;
            }

            const horaCheckin = checkinAt 
                ? `<span style="color: #00A36C; font-weight: 700; font-size: 0.95rem;">
                    <i class="fas fa-clock mr-2" style="color: #00A36C;"></i>${checkinAt}
                   </span>`
                : `<span style="color: #ffc107; font-weight: 500; font-size: 0.9rem;">-</span>`;

            const modoAsistencia = participante.modo_asistencia || 'Manual';
            const modoBadge = modoAsistencia === 'QR' 
                ? `<span class="badge badge-info" style="background: #17a2b8; font-size: 0.75rem; margin-left: 0.5rem;">QR</span>`
                : modoAsistencia === 'Online'
                ? `<span class="badge badge-primary" style="background: #007bff; font-size: 0.75rem; margin-left: 0.5rem;">Online</span>`
                : '';

            // Mostrar botón de acción basado en estado_asistencia
            const botonAccion = (estadoAsistencia === 'asistido' || asistio)
                ? `<div>
                    <span style="color: #00A36C; font-weight: 700; font-size: 0.9rem;">
                        <i class="fas fa-check-circle mr-2" style="color: #00A36C;"></i>Registrado
                    </span>
                    ${modoBadge}
                    ${participante.observaciones ? `<br><small style="color: #0C2B44; font-weight: 500; font-size: 0.8rem;" title="${participante.observaciones}"><i class="fas fa-comment mr-1" style="color: #17a2b8;"></i>Con observaciones</small>` : ''}
                  </div>`
                : `<button class="btn btn-sm btn-success" 
                           onclick="mostrarModalRegistrarAsistencia(${participante.id}, '${ticketCodigo}', '${tipoUsuario}')"
                           style="background: linear-gradient(135deg, #00A36C 0%, #008a5a 100%); border: none; border-radius: 10px; padding: 0.5em 1.2em; font-weight: 600; box-shadow: 0 2px 8px rgba(0, 163, 108, 0.3); transition: all 0.3s ease;"
                           onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0, 163, 108, 0.4)'"
                           onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(0, 163, 108, 0.3)'">
                    <i class="fas fa-check mr-2"></i>Marcar
                  </button>`;

            // Resaltar fila si asistió
            const rowStyle = (estadoAsistencia === 'asistido' || asistio)
                ? 'background: linear-gradient(90deg, rgba(0, 163, 108, 0.08) 0%, rgba(0, 163, 108, 0.03) 100%); border-left: 5px solid #00A36C;'
                : 'background: linear-gradient(90deg, rgba(255, 193, 7, 0.05) 0%, rgba(255, 193, 7, 0.02) 100%); border-left: 5px solid #ffc107;';

            // Badge de tipo de usuario
            const tipoBadge = tipoUsuario === 'Voluntario'
                ? `<span class="badge badge-success mr-2" style="background: #00A36C; font-size: 0.7rem; padding: 0.2em 0.5em;">
                    <i class="fas fa-user-check mr-1"></i>Voluntario
                   </span>`
                : `<span class="badge badge-info mr-2" style="background: #17a2b8; font-size: 0.7rem; padding: 0.2em 0.5em;">
                    <i class="fas fa-user mr-1"></i>Externo
                   </span>`;

            html += `
                <tr style="border-bottom: 2px solid rgba(0, 163, 108, 0.1); ${rowStyle}; transition: all 0.3s ease;" 
                    onmouseover="this.style.background='linear-gradient(90deg, rgba(0, 163, 108, 0.12) 0%, rgba(0, 163, 108, 0.05) 100%)'; this.style.transform='scale(1.01)'"
                    onmouseout="this.style.background='${rowStyle.includes('asistido') ? 'linear-gradient(90deg, rgba(0, 163, 108, 0.08) 0%, rgba(0, 163, 108, 0.03) 100%)' : 'linear-gradient(90deg, rgba(255, 193, 7, 0.05) 0%, rgba(255, 193, 7, 0.02) 100%)}'; this.style.transform='scale(1)'">
                    <td style="border: none; padding: 1.25rem;">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mr-3" 
                                 style="width: 45px; height: 45px; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); color: white; font-weight: 700; font-size: 1.1rem; box-shadow: 0 2px 8px rgba(0, 163, 108, 0.3);">
                                ${nombre.charAt(0).toUpperCase()}
                            </div>
                            <div>
                                <div style="font-weight: 700; color: #0C2B44; font-size: 1rem; margin-bottom: 0.25rem;">
                                    ${nombre}
                                    ${tipoBadge}
                                </div>
                                ${participante.correo ? `<small style="color: #0C2B44; font-weight: 500; font-size: 0.85rem; opacity: 0.8;"><i class="fas fa-envelope mr-1" style="color: #17a2b8;"></i>${participante.correo}</small>` : ''}
                            </div>
                        </div>
                    </td>
                    <td style="border: none; padding: 1.25rem;">
                        <code style="background: linear-gradient(135deg, #0C2B44 0%, #1a4a6b 100%); color: #ffffff; padding: 0.5em 0.8em; border-radius: 8px; font-size: 0.9rem; font-weight: 600; letter-spacing: 0.5px; box-shadow: 0 2px 6px rgba(12, 43, 68, 0.2);">
                            ${ticketCodigo !== 'N/A' ? ticketCodigo.substring(0, 8) + '...' : 'N/A'}
                        </code>
                    </td>
                    <td style="border: none; padding: 1.25rem;" class="text-center">
                        ${estadoBadge}
                    </td>
                    <td style="border: none; padding: 1.25rem;" class="text-center">
                        ${horaCheckin}
                    </td>
                    <td style="border: none; padding: 1.25rem;" class="text-center">
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
            <div class="alert alert-danger" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); border: none; border-radius: 12px; padding: 1.5rem; color: white; box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);">
                <i class="fas fa-exclamation-triangle mr-2" style="font-size: 1.2rem;"></i>
                <strong style="font-weight: 700;">Error de conexión</strong>
                <p class="mb-0 mt-2" style="opacity: 0.95;">No se pudo conectar con el servidor. Verifica tu conexión a internet.</p>
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

    await registrarAsistenciaPorId(ticketCodigo, null, null);
}


// Activar escáner QR
async function activarEscannerQR() {
    const container = document.getElementById('qrScannerContainer');
    const video = document.getElementById('qrVideo');
    
    if (!container || !video) {
        alert('Elementos del escáner no encontrados');
        return;
    }

    // Cargar librería jsQR si no está disponible
    if (typeof jsQR === 'undefined') {
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js';
        script.onload = () => iniciarEscanner();
        script.onerror = () => {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo cargar la librería de escáner QR. Por favor, usa la entrada manual.'
                });
            } else {
                alert('Error cargando escáner QR');
            }
        };
        document.head.appendChild(script);
    } else {
        iniciarEscanner();
    }

    function iniciarEscanner() {
        navigator.mediaDevices.getUserMedia({ 
            video: { 
                facingMode: 'environment' // Cámara trasera en móviles
            } 
        })
        .then(stream => {
            qrStream = stream;
            video.srcObject = stream;
            video.setAttribute('playsinline', true);
            video.play();
            container.style.display = 'block';
            qrScanning = true;
            escanearQR();
        })
        .catch(err => {
            console.error('Error accediendo a la cámara:', err);
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de cámara',
                    text: 'No se pudo acceder a la cámara. Por favor, verifica los permisos o usa la entrada manual.'
                });
            } else {
                alert('Error accediendo a la cámara');
            }
        });
    }
}

// Función para escanear QR continuamente
function escanearQR() {
    const video = document.getElementById('qrVideo');
    const canvas = document.getElementById('qrCanvas');
    
    if (!qrScanning || !video || !canvas) return;

    if (video.readyState === video.HAVE_ENOUGH_DATA) {
        canvas.height = video.videoHeight;
        canvas.width = video.videoWidth;
        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
        
        if (typeof jsQR !== 'undefined') {
            const code = jsQR(imageData.data, imageData.width, imageData.height);
            if (code) {
                // QR detectado
                detenerEscannerQR();
                const ticketCodigo = code.data.trim();
                document.getElementById('ticketCodigoInput').value = ticketCodigo;
                // Registrar con modo QR (se detectará en el backend o se puede pasar como parámetro)
                registrarAsistenciaPorId(ticketCodigo, null, null, 'QR');
            }
        }
    }

    if (qrScanning) {
        requestAnimationFrame(escanearQR);
    }
}

// Detener escáner QR
function detenerEscannerQR() {
    qrScanning = false;
    const container = document.getElementById('qrScannerContainer');
    const video = document.getElementById('qrVideo');
    
    if (qrStream) {
        qrStream.getTracks().forEach(track => track.stop());
        qrStream = null;
    }
    
    if (video) {
        video.srcObject = null;
    }
    
    if (container) {
        container.style.display = 'none';
    }
}

// Mostrar modal para registrar asistencia con observaciones
function mostrarModalRegistrarAsistencia(participacionId, ticketCodigo, tipoUsuario = 'Externo') {
    if (typeof Swal === 'undefined') {
        // Si no hay SweetAlert, registrar directamente
        registrarAsistenciaPorId(ticketCodigo, participacionId);
        return;
    }

    Swal.fire({
        title: 'Registrar Asistencia',
        html: `
            <div class="text-left">
                <div class="form-group">
                    <label>Observaciones (opcional)</label>
                    <textarea id="observacionesAsistencia" class="form-control" rows="3" 
                              placeholder="Ej: Llegó tarde, salió antes, documento no válido, etc."></textarea>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Registrar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#00A36C',
        didOpen: () => {
            // Focus en el textarea
            document.getElementById('observacionesAsistencia').focus();
        },
        preConfirm: () => {
            const observaciones = document.getElementById('observacionesAsistencia').value.trim();
            return { observaciones };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const observaciones = result.value.observaciones;
            registrarAsistenciaPorId(ticketCodigo, participacionId, observaciones);
        }
    });
}

// Actualizar función para aceptar observaciones
async function registrarAsistenciaPorId(ticketCodigo, participacionId = null, observaciones = null, modoAsistenciaForzado = null) {
    const token = localStorage.getItem('token');
    
    // Extraer evento_id de la URL: /ong/eventos/{id}/detalle
    const pathParts = window.location.pathname.split("/").filter(p => p !== '');
    let eventoId = null;
    
    // Buscar el índice de 'eventos' y tomar el siguiente elemento como ID
    const eventosIndex = pathParts.indexOf('eventos');
    if (eventosIndex !== -1 && pathParts[eventosIndex + 1]) {
        eventoId = pathParts[eventosIndex + 1];
    } else {
        // Fallback: usar el mismo método que show-event.js (índice 3)
        const pathArray = window.location.pathname.split("/");
        eventoId = pathArray[3];
    }
    
    // Validar que eventoId sea un número
    if (!eventoId || isNaN(eventoId)) {
        console.error('Evento ID inválido:', eventoId, 'Path:', window.location.pathname, 'PathParts:', pathParts);
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo identificar el evento. Por favor, recarga la página.'
            });
        }
        return;
    }

    // Limpiar y validar ticket
    if (ticketCodigo) {
        ticketCodigo = ticketCodigo.trim();
    }

    // Determinar modo de asistencia
    // Si se fuerza un modo (ej: QR desde escáner), usarlo; si no, siempre 'Manual' para registro manual
    const modoAsistencia = modoAsistenciaForzado || 'Manual';

    console.log('Registrando asistencia:', {
        eventoId: parseInt(eventoId),
        ticketCodigo: ticketCodigo ? ticketCodigo.substring(0, 8) + '...' : null,
        participacionId,
        modoAsistencia
    });

    try {
        const body = {
            evento_id: parseInt(eventoId),
            modo_asistencia: modoAsistencia
        };

        // Priorizar participacion_id (para marcar desde lista o voluntarios sin ticket)
        if (participacionId) {
            body.participacion_id = participacionId;
        } else if (ticketCodigo && ticketCodigo !== 'N/A' && ticketCodigo.trim() !== '') {
            // Solo usar ticket_codigo si no hay participacion_id y el ticket es válido
            body.ticket_codigo = ticketCodigo;
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
                    footer: data.debug ? `Debug: ${JSON.stringify(data.debug)}` : ''
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
            alert('Asistencia registrada correctamente');
        }

        // Recargar lista de asistencia y estadísticas
        await cargarListaAsistencia();

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


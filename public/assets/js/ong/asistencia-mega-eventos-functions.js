// =====================================
// CONTROL DE ASISTENCIA - MEGA EVENTOS
// =====================================

let qrStreamMegaEvento = null;
let qrScanningMegaEvento = false;

// Cargar lista de asistencia para mega eventos
async function cargarListaAsistenciaMegaEvento() {
    const container = document.getElementById('listaAsistenciaMegaEventoContainer');
    const token = localStorage.getItem('token');
    
    // Extraer mega_evento_id de la URL: /ong/mega-eventos/{id}/detalle
    const pathParts = window.location.pathname.split("/").filter(p => p !== '');
    let megaEventoId = null;
    
    const megaEventosIndex = pathParts.indexOf('mega-eventos');
    if (megaEventosIndex !== -1 && pathParts[megaEventosIndex + 1]) {
        megaEventoId = pathParts[megaEventosIndex + 1];
    } else {
        // Fallback: usar índice 3
        const pathArray = window.location.pathname.split("/");
        megaEventoId = pathArray[3];
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

        const res = await fetch(`${API_BASE_URL}/api/mega-eventos/${megaEventoId}/control-asistencia`, {
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
        const aprobados = data.participantes || [];
        
        // Actualizar estadísticas basadas en estado_asistencia
        const totalInscritos = aprobados.length;
        const totalAsistieron = aprobados.filter(p => (p.estado_asistencia_raw === 'asistido' || p.asistio === true)).length;
        const totalPendientes = aprobados.filter(p => (p.estado_asistencia_raw === 'no_asistido' || (!p.asistio && !p.estado_asistencia_raw))).length;

        const totalInscritosEl = document.getElementById('totalInscritosMegaEvento');
        const totalAsistieronEl = document.getElementById('totalAsistieronMegaEvento');
        const totalPendientesEl = document.getElementById('totalPendientesMegaEvento');
        
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
                            <th style="border: none; color: #ffffff; font-weight: 700; padding: 1.25rem; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.5px;" class="text-center">Estado de Registro</th>
                        </tr>
                    </thead>
                    <tbody style="background: #ffffff;">
        `;

        if (aprobados.length === 0) {
            html += `
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        <i class="fas fa-users fa-3x mb-3" style="opacity: 0.3;"></i>
                        <p class="mb-0">No hay participantes aprobados en este mega evento</p>
                    </td>
                </tr>
            `;
        } else {
            // Ordenar: primero los que asistieron, luego los pendientes
            const asistieron = aprobados.filter(p => p.estado_asistencia_raw === 'asistido' || p.asistio === true);
            const pendientes = aprobados.filter(p => p.estado_asistencia_raw !== 'asistido' && p.asistio !== true);
            const ordenados = [...asistieron, ...pendientes];

            ordenados.forEach(participante => {
                // Priorizar nombre_completo, luego participante, luego construir desde nombres/apellidos
                let nombre = participante.nombre_completo || participante.participante;
                
                // Si no hay nombre_completo, intentar construir desde nombres y apellidos
                if (!nombre || nombre === 'N/A' || nombre.length <= 2) {
                    const nombres = participante.nombres || '';
                    const apellidos = participante.apellidos || '';
                    const nombreConstruido = (nombres + ' ' + apellidos).trim();
                    nombre = nombreConstruido || participante.nombre_usuario || 'Usuario';
                }
                
                // Si aún es muy corto o solo tiene una letra, usar nombre_usuario
                if (nombre.length <= 2 && participante.nombre_usuario && participante.nombre_usuario.length > 2) {
                    nombre = participante.nombre_usuario;
                }
                
                const nombreUsuario = participante.nombre_usuario || nombre;
                const ticketCodigo = participante.ticket_codigo || 'N/A';
                const asistio = participante.asistio === true;
                const tipoUsuario = participante.tipo_usuario || (participante.tipo === 'no_registrado' || participante.tipo === 'voluntario' ? 'Voluntario' : 'Externo');
                const checkinAt = participante.fecha_registro_asistencia || null;
                const fotoPerfil = participante.foto_perfil || participante.avatar || null;
                
                // Usar estado_asistencia para mostrar el estado correcto
                const estadoAsistencia = participante.estado_asistencia_raw || 'no_asistido';
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

                // Mostrar información de registro (solo visualización, sin botones de acción)
                const infoRegistro = (estadoAsistencia === 'asistido' || asistio)
                    ? `<div>
                        <span style="color: #00A36C; font-weight: 700; font-size: 0.9rem;">
                            <i class="fas fa-check-circle mr-2" style="color: #00A36C;"></i>Registrado
                        </span>
                        ${modoBadge}
                        ${participante.observaciones && participante.observaciones !== '-' ? `<br><small style="color: #0C2B44; font-weight: 500; font-size: 0.8rem;" title="${participante.observaciones}"><i class="fas fa-comment mr-1" style="color: #17a2b8;"></i>Con observaciones</small>` : ''}
                        ${participante.validado_por && participante.validado_por !== '—' ? `<br><small style="color: #6c757d; font-size: 0.75rem;"><i class="fas fa-user mr-1"></i>Registrado por: ${participante.validado_por}</small>` : ''}
                      </div>`
                    : `<div>
                        <span style="color: #ffc107; font-weight: 600; font-size: 0.9rem;">
                            <i class="fas fa-clock mr-2" style="color: #ffc107;"></i>Pendiente
                        </span>
                        <br><small style="color: #6c757d; font-size: 0.75rem;">El participante debe registrar su asistencia</small>
                      </div>`;

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
                        onmouseout="this.style.background='${rowStyle.includes('asistido') ? 'linear-gradient(90deg, rgba(0, 163, 108, 0.08) 0%, rgba(0, 163, 108, 0.03) 100%)' : 'linear-gradient(90deg, rgba(255, 193, 7, 0.05) 0%, rgba(255, 193, 7, 0.02) 100%)'}'; this.style.transform='scale(1)'">
                        <td style="border: none; padding: 1.25rem;">
                            <div class="d-flex align-items-center">
                                ${fotoPerfil ? `
                                    <img src="${fotoPerfil}" 
                                         alt="${nombre}" 
                                         class="rounded-circle mr-3" 
                                         style="width: 45px; height: 45px; object-fit: cover; border: 2px solid #00A36C; box-shadow: 0 2px 8px rgba(0, 163, 108, 0.3);"
                                         onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mr-3" 
                                         style="width: 45px; height: 45px; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); color: white; font-weight: 700; font-size: 1.1rem; box-shadow: 0 2px 8px rgba(0, 163, 108, 0.3); display: none;">
                                        ${nombre.charAt(0).toUpperCase()}
                                    </div>
                                ` : `
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mr-3" 
                                         style="width: 45px; height: 45px; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); color: white; font-weight: 700; font-size: 1.1rem; box-shadow: 0 2px 8px rgba(0, 163, 108, 0.3);">
                                        ${nombre.charAt(0).toUpperCase()}
                                    </div>
                                `}
                                <div>
                                    <div style="font-weight: 700; color: #0C2B44; font-size: 1rem; margin-bottom: 0.25rem;">
                                        ${nombre}
                                        ${tipoBadge}
                                    </div>
                                    ${participante.email && participante.email !== '—' ? `<small style="color: #0C2B44; font-weight: 500; font-size: 0.85rem; opacity: 0.8;"><i class="fas fa-envelope mr-1" style="color: #17a2b8;"></i>${participante.email}</small>` : ''}
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
                            ${infoRegistro}
                        </td>
                    </tr>
                `;
            });
        }

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

// Registrar asistencia por ticket o ID
async function registrarAsistenciaPorIdMegaEvento(ticketCodigo, participacionId = null, observaciones = null, modoAsistencia = 'Manual', tipo = 'registrado') {
    const token = localStorage.getItem('token');
    
    // Extraer mega_evento_id de la URL
    const pathParts = window.location.pathname.split("/").filter(p => p !== '');
    let megaEventoId = null;
    
    const megaEventosIndex = pathParts.indexOf('mega-eventos');
    if (megaEventosIndex !== -1 && pathParts[megaEventosIndex + 1]) {
        megaEventoId = pathParts[megaEventosIndex + 1];
    }

    if (!megaEventoId) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo identificar el mega evento'
            });
        }
        return;
    }

    try {
        const res = await fetch(`${API_BASE_URL}/api/mega-eventos/${megaEventoId}/registrar-asistencia`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                ticket_codigo: ticketCodigo,
                participacion_id: participacionId,
                tipo: tipo,
                observaciones: observaciones,
                modo_asistencia: modoAsistencia
            })
        });

        const data = await res.json();

        if (!res.ok || !data.success) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.error || 'Error al registrar asistencia'
                });
            } else {
                alert(data.error || 'Error al registrar asistencia');
            }
            return;
        }

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: '¡Asistencia registrada!',
                text: 'La asistencia se ha registrado correctamente',
                timer: 2000,
                showConfirmButton: false
            });
        }

        // Recargar lista de asistencia
        await cargarListaAsistenciaMegaEvento();

        // Limpiar input de ticket
        const ticketInput = document.getElementById('ticketCodigoInputMegaEvento');
        if (ticketInput) {
            ticketInput.value = '';
        }

    } catch (error) {
        console.error('Error registrando asistencia:', error);
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error de conexión al registrar asistencia'
            });
        } else {
            alert('Error de conexión');
        }
    }
}

// Registrar asistencia manualmente ingresando código
async function registrarAsistenciaManualMegaEvento() {
    const ticketCodigo = document.getElementById('ticketCodigoInputMegaEvento').value.trim();
    
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

    await registrarAsistenciaPorIdMegaEvento(ticketCodigo, null, null, 'Manual');
}

// Mostrar modal para registrar asistencia con observaciones
function mostrarModalRegistrarAsistenciaMegaEvento(participacionId, ticketCodigo, tipoUsuario = 'Externo', tipo = 'registrado') {
    if (typeof Swal === 'undefined') {
        // Si no hay SweetAlert, registrar directamente
        registrarAsistenciaPorIdMegaEvento(ticketCodigo, participacionId, null, 'Manual', tipo);
        return;
    }

    Swal.fire({
        title: 'Registrar Asistencia',
        html: `
            <div class="text-left">
                <div class="form-group">
                    <label>Observaciones (opcional)</label>
                    <textarea id="observacionesAsistenciaMegaEvento" class="form-control" rows="3" 
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
            document.getElementById('observacionesAsistenciaMegaEvento').focus();
        },
        preConfirm: () => {
            const observaciones = document.getElementById('observacionesAsistenciaMegaEvento').value.trim();
            return registrarAsistenciaPorIdMegaEvento(ticketCodigo, participacionId, observaciones, 'Manual', tipo);
        }
    });
}

// Modificar estado de asistencia
async function modificarAsistenciaMegaEvento(participacionId, tipo, nuevoEstado) {
    const token = localStorage.getItem('token');

    try {
        const res = await fetch(`${API_BASE_URL}/api/mega-eventos/asistencias/${participacionId}/${tipo}`, {
            method: 'PUT',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                estado_asistencia: nuevoEstado,
                observaciones: ''
            })
        });

        const data = await res.json();

        if (!res.ok || !data.success) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.error || 'Error al modificar asistencia'
                });
            }
            return;
        }

        // Recargar lista
        await cargarListaAsistenciaMegaEvento();

    } catch (error) {
        console.error('Error modificando asistencia:', error);
    }
}

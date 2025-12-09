@extends('layouts.adminlte')

@section('page_title', 'Notificaciones')

@section('content_body')
<div class="container-fluid" style="background: #ffffff; min-height: 100vh; padding: 2rem 1rem;">
    <!-- Header Minimalista -->
    <div class="d-flex justify-content-between align-items-center mb-4" style="padding: 0 0.5rem;">
        <div>
            
        </div>
        <button class="btn" onclick="marcarTodasLeidas()" style="background: rgba(12, 43, 68, 0.1); color: #0C2B44; border: 1px solid rgba(12, 43, 68, 0.2); border-radius: 12px; padding: 0.6rem 1.25rem; font-weight: 500; transition: all 0.3s ease;" onmouseover="this.style.background='rgba(12, 43, 68, 0.15)'" onmouseout="this.style.background='rgba(12, 43, 68, 0.1)'">
            <i class="far fa-check-double mr-2"></i> Marcar todas como leídas
        </button>
    </div>
    
    <!-- Contenedor de Notificaciones -->
    <div id="notificacionesContainer">
        <div class="text-center py-5">
            <div class="spinner-border" role="status" style="color: #00A36C; width: 3rem; height: 3rem;">
                <span class="sr-only">Cargando...</span>
            </div>
            <p class="mt-3" style="color: rgba(12, 43, 68, 0.7); font-weight: 500;">Cargando notificaciones...</p>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    body {
        background: #ffffff !important;
    }
    
    .content-wrapper {
        background: #ffffff !important;
    }
    
    .notificacion-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 1.25rem;
        margin-bottom: 1rem;
        border: 1px solid rgba(12, 43, 68, 0.1);
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        box-shadow: 0 2px 8px rgba(12, 43, 68, 0.08);
    }
    
    .notificacion-card:hover {
        background: #f8f9fa;
        border-color: rgba(12, 43, 68, 0.2);
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(12, 43, 68, 0.12);
    }
    
    .notificacion-card.no-leida {
        background: rgba(0, 163, 108, 0.05);
        border-color: rgba(0, 163, 108, 0.2);
        border-left: 4px solid #00A36C;
    }
    
    .notificacion-card.no-leida:hover {
        background: rgba(0, 163, 108, 0.08);
        border-color: rgba(0, 163, 108, 0.3);
    }
    
    .notificacion-card.leida {
        background: #ffffff;
        opacity: 0.9;
        border-left: 4px solid rgba(12, 43, 68, 0.2);
    }
    
    .notificacion-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid rgba(12, 43, 68, 0.1);
        flex-shrink: 0;
    }
    
    .notificacion-avatar-inicial {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 1.1rem;
        flex-shrink: 0;
    }
    
    .notificacion-icono {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(12, 43, 68, 0.05);
        flex-shrink: 0;
    }
    
    .notificacion-prioridad {
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .prioridad-alta {
        background: #dc3545;
        color: white;
    }
    
    .prioridad-media {
        background: #6f42c1;
        color: white;
    }
    
    .prioridad-baja {
        background: #0dcaf0;
        color: white;
    }
    
    .notificacion-navegar {
        color: rgba(12, 43, 68, 0.4);
        font-size: 1.1rem;
        transition: all 0.3s ease;
    }
    
    .notificacion-card:hover .notificacion-navegar {
        color: rgba(12, 43, 68, 0.7);
        transform: translate(2px, -2px);
    }
</style>
@endpush

@push('js')
{{-- Script global para icono de notificaciones --}}
<script src="{{ asset('js/notificaciones-ong.js') }}"></script>
<script src="{{ asset('assets/js/config.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', async () => {
    await cargarNotificaciones();
});

async function cargarNotificaciones() {
    const container = document.getElementById('notificacionesContainer');
    const token = localStorage.getItem('token');

    if (!token) {
        container.innerHTML = '<div class="alert alert-danger" style="background: #f8d7da; border: 1px solid #dc3545; color: #721c24; border-radius: 8px; padding: 1rem;"><i class="far fa-exclamation-triangle mr-2"></i>Debe iniciar sesión</div>';
        return;
    }

    try {
        container.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border" role="status" style="color: #00A36C; width: 3rem; height: 3rem;">
                    <span class="sr-only">Cargando...</span>
                </div>
                <p class="mt-3" style="color: #333333; font-weight: 500;">Cargando notificaciones...</p>
            </div>
        `;

        const res = await fetch(`${API_BASE_URL}/api/notificaciones`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        const data = await res.json();

        if (!res.ok || !data.success) {
            container.innerHTML = `
                <div class="alert alert-danger" style="background: #f8d7da; border: 1px solid #dc3545; color: #721c24; border-radius: 8px; padding: 1rem;">
                    <i class="far fa-exclamation-triangle mr-2"></i>
                    ${data.error || 'Error al cargar notificaciones'}
                </div>
            `;
            return;
        }

        if (!data.notificaciones || data.notificaciones.length === 0) {
            container.innerHTML = `
                <div class="text-center py-5" style="padding: 4rem 2rem;">
                    <div style="width: 80px; height: 80px; border-radius: 50%; background: rgba(12, 43, 68, 0.05); display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; border: 1px solid rgba(12, 43, 68, 0.1);">
                        <i class="far fa-bell-slash fa-3x" style="color: rgba(12, 43, 68, 0.3);"></i>
                    </div>
                    <h5 style="color: #0C2B44; font-weight: 600; margin-bottom: 0.5rem;">No hay notificaciones</h5>
                    <p style="color: rgba(12, 43, 68, 0.6); margin: 0;">Todas tus notificaciones aparecerán aquí cuando las recibas</p>
                </div>
            `;
            return;
        }

        let html = '';
        data.notificaciones.forEach((notif, index) => {
            const fecha = new Date(notif.fecha).toLocaleString('es-ES', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            const claseLeida = notif.leida ? 'leida' : 'no-leida';
            
            // Determinar icono y prioridad según el tipo de notificación
            let iconoTipo, iconoColor, prioridad, prioridadClass, nombreUsuario, tituloUsuario;
            
            if (notif.tipo === 'reaccion') {
                iconoTipo = 'far fa-heart';
                iconoColor = '#00A36C';
                prioridad = 'Media';
                prioridadClass = 'prioridad-media';
                nombreUsuario = notif.externo?.nombre_usuario || 'Usuario';
                tituloUsuario = notif.externo?.titulo || 'Participante';
            } else if (notif.tipo === 'participacion' || notif.tipo === 'inscripcion' || notif.evento_id || notif.evento_titulo) {
                iconoTipo = 'far fa-calendar-alt';
                iconoColor = '#0C2B44';
                prioridad = 'Alta';
                prioridadClass = 'prioridad-alta';
                nombreUsuario = notif.externo?.nombre_usuario || 'Usuario';
                tituloUsuario = notif.externo?.titulo || 'Participante';
            } else if (notif.tipo === 'empresa_asignada' || notif.tipo === 'empresa_confirmada') {
                iconoTipo = 'far fa-building';
                iconoColor = '#00A36C';
                prioridad = 'Alta';
                prioridadClass = 'prioridad-alta';
                nombreUsuario = notif.externo?.nombre_usuario || 'Empresa';
                tituloUsuario = notif.externo?.titulo || 'Empresa';
            } else {
                iconoTipo = 'far fa-bell';
                iconoColor = '#0C2B44';
                prioridad = 'Baja';
                prioridadClass = 'prioridad-baja';
                nombreUsuario = 'Sistema';
                tituloUsuario = 'Notificación';
            }
            
            // Obtener inicial para avatar
            const inicial = nombreUsuario ? nombreUsuario.charAt(0).toUpperCase() : 'N';
            
            // Obtener foto de perfil si existe
            const fotoPerfil = notif.externo?.foto_perfil_url || null;
            
            // Determinar acción al hacer clic
            let urlAccion = '#';
            if (notif.evento_id) {
                urlAccion = `/ong/eventos/${notif.evento_id}/detalle`;
            }

            html += `
                <div class="notificacion-card ${claseLeida}" data-id="${notif.id}" onclick="${urlAccion !== '#' ? `window.location.href='${urlAccion}'` : ''}" style="cursor: ${urlAccion !== '#' ? 'pointer' : 'default'};">
                    <div class="d-flex align-items-start">
                        <!-- Avatar -->
                        <div class="mr-3" style="position: relative;">
                            ${fotoPerfil ? 
                                `<img src="${fotoPerfil}" alt="${nombreUsuario}" class="notificacion-avatar" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                 <div class="notificacion-avatar-inicial" style="display: none;">${inicial}</div>` :
                                `<div class="notificacion-avatar-inicial">${inicial}</div>`
                            }
                        </div>
                        
                        <!-- Contenido Principal -->
                        <div class="flex-grow-1" style="min-width: 0;">
                            <!-- Nombre y Título -->
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div style="flex: 1; min-width: 0;">
                                    <h6 class="mb-0" style="color: #0C2B44; font-weight: 700; font-size: 1rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        ${nombreUsuario}
                                    </h6>
                                    <small style="color: rgba(12, 43, 68, 0.6); font-size: 0.8rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block; margin-top: 2px;">
                                        ${tituloUsuario}${notif.evento_titulo ? ' • ' + notif.evento_titulo : ''}
                                    </small>
                                </div>
                                ${urlAccion !== '#' ? `<i class="fas fa-arrow-up-right notificacion-navegar ml-2" style="flex-shrink: 0;"></i>` : ''}
                            </div>
                            
                            <!-- Descripción con Icono -->
                            <div class="d-flex align-items-start mb-2">
                                <div class="notificacion-icono mr-2" style="margin-top: 2px;">
                                    <i class="${iconoTipo}" style="color: ${iconoColor}; font-size: 1rem;"></i>
                                </div>
                                <p class="mb-0" style="color: rgba(12, 43, 68, 0.8); font-size: 0.9rem; line-height: 1.5; flex: 1;">
                                    ${notif.mensaje}
                                </p>
                            </div>
                            
                            <!-- Fecha y Prioridad -->
                            <div class="d-flex justify-content-between align-items-center">
                                <small style="color: rgba(12, 43, 68, 0.5); font-size: 0.75rem;">
                                    ${fecha}
                                </small>
                                <span class="notificacion-prioridad ${prioridadClass}">
                                    ${prioridad}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Botón de acción (solo para no leídas) -->
                    ${!notif.leida ? `
                        <div class="mt-3 pt-3" style="border-top: 1px solid rgba(12, 43, 68, 0.1);">
                            <button class="btn btn-sm w-100" onclick="event.stopPropagation(); marcarLeida(${notif.id})" style="background: rgba(0, 163, 108, 0.1); color: #00A36C; border: 1px solid rgba(0, 163, 108, 0.2); border-radius: 8px; padding: 0.5rem; font-weight: 500; transition: all 0.3s ease;" onmouseover="this.style.background='rgba(0, 163, 108, 0.15)'" onmouseout="this.style.background='rgba(0, 163, 108, 0.1)'">
                                <i class="far fa-check mr-2"></i> Marcar como leída
                            </button>
                        </div>
                    ` : ''}
                </div>
            `;
        });

        html += `
            <div class="mt-4 pt-3" style="border-top: 1px solid rgba(12, 43, 68, 0.1);">
                <div class="d-flex justify-content-between align-items-center">
                    <small style="color: rgba(12, 43, 68, 0.6); font-weight: 500;">
                        <i class="far fa-info-circle mr-1" style="color: #00A36C;"></i>
                        Total: ${data.notificaciones.length} notificación(es)
                    </small>
                    <small style="color: #00A36C; font-weight: 600;">
                        No leídas: ${data.no_leidas}
                    </small>
                </div>
            </div>
        `;

        container.innerHTML = html;

    } catch (error) {
        console.error('Error cargando notificaciones:', error);
        container.innerHTML = `
            <div class="alert alert-danger" style="background: #f8d7da; border: 1px solid #dc3545; color: #721c24; border-radius: 8px; padding: 1rem;">
                <i class="far fa-exclamation-triangle mr-2"></i>
                Error de conexión al cargar notificaciones
            </div>
        `;
    }
}

async function marcarLeida(id) {
    const token = localStorage.getItem('token');

    try {
        const res = await fetch(`${API_BASE_URL}/api/notificaciones/${id}/leida`, {
            method: 'PUT',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        const data = await res.json();
        if (data.success) {
            // Actualizar contador global después de marcar como leída
            if (typeof actualizarContadorNotificaciones === 'function') {
                actualizarContadorNotificaciones();
            }
            await cargarNotificaciones();
        } else {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.error || 'Error al marcar notificación'
                });
            }
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

async function marcarTodasLeidas() {
    const token = localStorage.getItem('token');

    if (typeof Swal !== 'undefined') {
        const result = await Swal.fire({
            title: '¿Marcar todas como leídas?',
            text: 'Todas las notificaciones se marcarán como leídas',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#00A36C',
            cancelButtonColor: '#333333',
            confirmButtonText: 'Sí, marcar todas',
            cancelButtonText: 'Cancelar'
        });
        if (!result.isConfirmed) return;
    }

    try {
        const res = await fetch(`${API_BASE_URL}/api/notificaciones/marcar-todas`, {
            method: 'PUT',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        const data = await res.json();
        if (data.success) {
            // Actualizar contador global después de marcar todas como leídas
            if (typeof actualizarContadorNotificaciones === 'function') {
                actualizarContadorNotificaciones();
            }
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: '¡Listo!',
                    text: 'Todas las notificaciones han sido marcadas como leídas',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
            await cargarNotificaciones();
        } else {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.error || 'Error al marcar notificaciones'
                });
            }
        }
    } catch (error) {
        console.error('Error:', error);
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error de conexión',
                text: 'No se pudieron marcar las notificaciones'
            });
        }
    }
}
</script>
@endpush


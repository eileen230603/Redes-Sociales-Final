@extends('layouts.adminlte')

@section('page_title', 'Notificaciones')

@section('content_body')
<div class="container-fluid">
    <div class="card border-0 mb-4" style="border-radius: 12px; border: 1px solid #F5F5F5; box-shadow: 0 2px 8px rgba(12, 43, 68, 0.08);">
        <div class="card-header" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); color: white; border-radius: 12px 12px 0 0; padding: 1.25rem 1.5rem;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-1" style="font-weight: 700; font-size: 1.4rem;">
                        <i class="far fa-bell mr-2"></i> Panel de Notificaciones
                    </h3>
                    <p class="mb-0" style="font-size: 0.9rem; opacity: 0.9;">
                        Gestiona todas tus notificaciones y mantente al día con las actividades
                    </p>
                </div>
                <button class="btn btn-sm" onclick="marcarTodasLeidas()" style="background: rgba(255,255,255,0.15); color: white; border: none; border-radius: 8px; padding: 0.5rem 1.25rem; font-weight: 500;">
                    <i class="far fa-check-double mr-1"></i> Marcar todas como leídas
                </button>
            </div>
        </div>
        <div class="card-body p-4">
            <div id="notificacionesContainer">
                <div class="text-center py-5">
                    <div class="spinner-border" role="status" style="color: #00A36C; width: 3rem; height: 3rem;">
                        <span class="sr-only">Cargando...</span>
                    </div>
                    <p class="mt-3" style="color: #333333; font-weight: 500;">Cargando notificaciones...</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    .notificacion-item {
        border-left: 4px solid #0C2B44;
        border-radius: 12px;
        transition: all 0.3s ease;
        margin-bottom: 1rem;
        box-shadow: 0 2px 4px rgba(12, 43, 68, 0.08);
    }

    .notificacion-item:hover {
        transform: translateX(4px);
        box-shadow: 0 4px 12px rgba(12, 43, 68, 0.15);
    }

    .notificacion-item.no-leida {
        background: linear-gradient(135deg, rgba(0, 163, 108, 0.05) 0%, rgba(12, 43, 68, 0.05) 100%);
        border-left-color: #00A36C;
        font-weight: 600;
        border-left-width: 5px;
        border: 1px solid rgba(0, 163, 108, 0.2);
    }

    .notificacion-item.leida {
        background: #F5F5F5;
        border-left-color: #333333;
        opacity: 0.9;
        border-left-width: 4px;
        border: 1px solid #F5F5F5;
    }

    .notificacion-tipo-reaccion {
        border-left-color: #00A36C;
    }

    .notificacion-tipo-reaccion.no-leida {
        border-left-color: #00A36C;
        background: linear-gradient(135deg, rgba(0, 163, 108, 0.08) 0%, rgba(12, 43, 68, 0.05) 100%);
        border: 1px solid rgba(0, 163, 108, 0.2);
    }

    .notificacion-tipo-reaccion.leida {
        border-left-color: #333333;
        background: #F5F5F5;
        border: 1px solid #F5F5F5;
    }

    .notificacion-tipo-participacion {
        border-left-color: #0C2B44;
    }

    .notificacion-tipo-participacion.no-leida {
        border-left-color: #0C2B44;
        background: linear-gradient(135deg, rgba(12, 43, 68, 0.08) 0%, rgba(0, 163, 108, 0.05) 100%);
        border: 1px solid rgba(12, 43, 68, 0.2);
    }

    .notificacion-tipo-participacion.leida {
        border-left-color: #333333;
        background: #F5F5F5;
        border: 1px solid #F5F5F5;
    }

    .notificacion-tipo-inscripcion {
        border-left-color: #0C2B44;
    }

    .notificacion-tipo-inscripcion.no-leida {
        border-left-color: #0C2B44;
        background: linear-gradient(135deg, rgba(12, 43, 68, 0.08) 0%, rgba(0, 163, 108, 0.05) 100%);
        border: 1px solid rgba(12, 43, 68, 0.2);
    }

    .notificacion-tipo-inscripcion.leida {
        border-left-color: #333333;
        background: #F5F5F5;
        border: 1px solid #F5F5F5;
    }

    .card-body {
        padding: 1.25rem 1.5rem;
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
                <div class="text-center py-5" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); border-radius: 12px; padding: 3rem 2rem;">
                    <div style="background: rgba(255, 255, 255, 0.15); width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; backdrop-filter: blur(10px);">
                        <i class="far fa-bell-slash fa-3x text-white"></i>
                    </div>
                    <h5 style="color: white; font-weight: 600; margin-bottom: 0.5rem;">No hay notificaciones</h5>
                    <p style="color: rgba(255, 255, 255, 0.9); margin: 0;">Todas tus notificaciones aparecerán aquí cuando las recibas</p>
                </div>
            `;
            return;
        }

        let html = '';
        data.notificaciones.forEach(notif => {
            const fecha = new Date(notif.fecha).toLocaleString('es-ES', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            const claseLeida = notif.leida ? 'leida' : 'no-leida';
            const claseTipo = `notificacion-tipo-${notif.tipo}`;
            
            // Determinar icono según el tipo de notificación
            let iconoTipo, iconoColor, iconoBackground;
            if (notif.tipo === 'reaccion') {
                iconoTipo = 'far fa-heart';
                iconoColor = '#00A36C';
                iconoBackground = notif.leida ? 'rgba(0, 163, 108, 0.1)' : 'rgba(0, 163, 108, 0.15)';
            } else if (notif.tipo === 'participacion' || notif.tipo === 'inscripcion' || notif.evento_id || notif.evento_titulo) {
                // Notificaciones relacionadas con eventos (inscripciones, participaciones)
                iconoTipo = 'far fa-calendar-alt';
                iconoColor = '#0C2B44';
                iconoBackground = notif.leida ? 'rgba(12, 43, 68, 0.1)' : 'rgba(12, 43, 68, 0.15)';
            } else {
                // Otras notificaciones
                iconoTipo = 'far fa-bell';
                iconoColor = '#0C2B44';
                iconoBackground = notif.leida ? 'rgba(12, 43, 68, 0.1)' : 'rgba(12, 43, 68, 0.15)';
            }

            html += `
                <div class="card notificacion-item ${claseLeida} ${claseTipo}" data-id="${notif.id}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="mr-3" style="width: 55px; height: 55px; background: ${iconoBackground}; border-radius: 12px; display: flex; align-items: center; justify-content: center; position: relative; flex-shrink: 0; box-shadow: 0 2px 4px rgba(12, 43, 68, 0.1);">
                                        <i class="${iconoTipo}" style="font-size: 1.6rem; color: ${iconoColor};"></i>
                                        ${!notif.leida ? '<span class="badge position-absolute" style="top: -5px; right: -8px; font-size: 0.6rem; padding: 3px 6px; border-radius: 50%; min-width: 18px; height: 18px; display: flex; align-items: center; justify-content: center; background: #00A36C !important; color: white !important; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">!</span>' : ''}
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-1">
                                            <h5 class="mb-0" style="font-size: 1.1rem; font-weight: ${notif.leida ? '500' : '700'}; color: ${notif.leida ? '#333333' : '#0C2B44'};">
                                                ${notif.titulo}
                                            </h5>
                                            ${!notif.leida ? '<span class="badge ml-2" style="background: #00A36C !important; color: white !important; font-weight: 600; padding: 0.25em 0.6em; border-radius: 20px; font-size: 0.75rem;"><i class="far fa-circle" style="font-size: 0.4rem; margin-right: 4px;"></i>Nueva</span>' : '<span class="badge ml-2" style="background: #333333 !important; color: white !important; opacity: 0.7; font-weight: 500; padding: 0.25em 0.6em; border-radius: 20px; font-size: 0.75rem;"><i class="far fa-check" style="font-size: 0.7rem; margin-right: 4px;"></i>Leída</span>'}
                                        </div>
                                        <p class="mb-2" style="color: #333333; font-size: 0.95rem; line-height: 1.6;">
                                            ${notif.mensaje}
                                        </p>
                                        ${notif.evento_titulo ? `
                                            <p class="mb-2" style="font-size: 0.9rem;">
                                                <i class="far fa-calendar-alt mr-1" style="color: #00A36C;"></i>
                                                <strong style="color: #0C2B44;">Evento:</strong> 
                                                <a href="/ong/eventos/${notif.evento_id}/detalle" style="color: #0C2B44; font-weight: 600; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='#00A36C'" onmouseout="this.style.color='#0C2B44'">${notif.evento_titulo}</a>
                                            </p>
                                        ` : ''}
                                        <small style="color: #333333; font-size: 0.85rem;">
                                            <i class="far fa-clock mr-1" style="color: #00A36C;"></i> ${fecha}
                                        </small>
                                    </div>
                                </div>
                            </div>
                            ${!notif.leida ? `
                                <button class="btn btn-sm ml-3" onclick="marcarLeida(${notif.id})" title="Marcar como leída" style="background: #00A36C; color: white; border: none; border-radius: 8px; padding: 0.5rem 0.75rem; font-weight: 500; transition: all 0.2s;" onmouseover="this.style.background='#0C2B44'" onmouseout="this.style.background='#00A36C'">
                                    <i class="far fa-check"></i>
                                </button>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;
        });

        html += `<div class="mt-4 pt-3 border-top" style="border-color: #F5F5F5 !important;"><small style="color: #333333; font-weight: 500;"><i class="far fa-info-circle mr-1" style="color: #00A36C;"></i>Total: ${data.notificaciones.length} notificación(es) | <span style="color: #00A36C; font-weight: 600;">No leídas: ${data.no_leidas}</span></small></div>`;

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


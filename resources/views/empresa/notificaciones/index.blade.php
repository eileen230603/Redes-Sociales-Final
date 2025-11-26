@extends('layouts.adminlte-empresa')

@section('title', 'Notificaciones')

@section('page_title', 'Notificaciones')

@section('content_body')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="fas fa-bell mr-2 text-warning"></i> Panel de Notificaciones
        </h4>
        <button class="btn btn-sm btn-outline-primary" onclick="marcarTodasLeidas()">
            <i class="fas fa-check-double mr-1"></i> Marcar todas como leídas
        </button>
    </div>

    <div id="notificacionesContainer">
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Cargando...</span>
            </div>
            <p class="mt-3 text-muted">Cargando notificaciones...</p>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
    .notificacion-item {
        border-left: 4px solid #007bff;
        transition: all 0.2s;
    }

    .notificacion-item:hover {
        transform: translateX(4px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .notificacion-item.no-leida {
        background: #fff5f5;
        border-left-color: #dc3545;
        font-weight: 600;
        border-left-width: 5px;
    }

    .notificacion-item.leida {
        background: #f8f9fa;
        border-left-color: #6c757d;
        opacity: 0.9;
        border-left-width: 4px;
    }

    .notificacion-tipo-empresa_asignada {
        border-left-color: #28a745;
    }

    .notificacion-tipo-empresa_asignada.no-leida {
        border-left-color: #28a745;
        background: #f0fff4;
    }

    .notificacion-tipo-empresa_asignada.leida {
        border-left-color: #adb5bd;
        background: #f8f9fa;
    }

    .notificacion-tipo-empresa_confirmada {
        border-left-color: #17a2b8;
    }

    .notificacion-tipo-empresa_confirmada.no-leida {
        border-left-color: #17a2b8;
        background: #e7f3f5;
    }

    .notificacion-tipo-empresa_confirmada.leida {
        border-left-color: #adb5bd;
        background: #f8f9fa;
    }
</style>
@endsection

@section('js')
<script src="{{ asset('assets/js/config.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', async () => {
    await cargarNotificaciones();
});

async function cargarNotificaciones() {
    const container = document.getElementById('notificacionesContainer');
    const token = localStorage.getItem('token');

    if (!token) {
        container.innerHTML = '<div class="alert alert-danger">Debe iniciar sesión</div>';
        return;
    }

    try {
        container.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Cargando...</span>
                </div>
                <p class="mt-3 text-muted">Cargando notificaciones...</p>
            </div>
        `;

        const res = await fetch(`${API_BASE_URL}/api/empresas/notificaciones`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        const data = await res.json();

        if (!res.ok || !data.success) {
            container.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    ${data.error || 'Error al cargar notificaciones'}
                </div>
            `;
            return;
        }

        if (!data.notificaciones || data.notificaciones.length === 0) {
            container.innerHTML = `
                <div class="alert alert-info text-center">
                    <i class="fas fa-bell-slash fa-3x mb-3"></i>
                    <h5>No hay notificaciones</h5>
                    <p class="mb-0">Todas tus notificaciones aparecerán aquí</p>
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
            const iconoTipo = notif.tipo === 'empresa_asignada' 
                ? 'fa-handshake text-success' 
                : 'fa-check-circle text-info';

            html += `
                <div class="card mb-3 notificacion-item ${claseLeida} ${claseTipo}" data-id="${notif.id}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="mr-2" style="position: relative;">
                                        <i class="fas ${iconoTipo}" style="font-size: 1.2rem;"></i>
                                        ${!notif.leida ? '<span class="badge badge-danger position-absolute" style="top: -5px; right: -8px; font-size: 0.6rem; padding: 2px 4px; border-radius: 50%; min-width: 16px; height: 16px; display: flex; align-items: center; justify-content: center;">!</span>' : ''}
                                    </div>
                                    <h5 class="mb-0" style="font-size: 1rem; font-weight: ${notif.leida ? '400' : '600'}; color: ${notif.leida ? '#6c757d' : '#212529'};">
                                        ${notif.titulo}
                                    </h5>
                                    ${!notif.leida ? '<span class="badge badge-danger ml-2" style="font-weight: bold;"><i class="fas fa-circle" style="font-size: 0.4rem; margin-right: 3px;"></i>Nueva</span>' : '<span class="badge badge-secondary ml-2" style="opacity: 0.7;"><i class="fas fa-check" style="font-size: 0.7rem; margin-right: 3px;"></i>Leída</span>'}
                                </div>
                                <p class="mb-2" style="color: #6c757d; font-size: 0.9rem;">
                                    ${notif.mensaje}
                                </p>
                                ${notif.evento_titulo ? `
                                    <p class="mb-1" style="font-size: 0.85rem;">
                                        <i class="fas fa-calendar-alt mr-1"></i>
                                        <strong>Evento:</strong> 
                                        <a href="/empresa/eventos/${notif.evento_id}/detalle" class="text-primary">${notif.evento_titulo}</a>
                                    </p>
                                ` : ''}
                                <small class="text-muted">
                                    <i class="fas fa-clock mr-1"></i> ${fecha}
                                </small>
                            </div>
                            ${!notif.leida ? `
                                <button class="btn btn-sm btn-outline-primary ml-3" onclick="marcarLeida(${notif.id})" title="Marcar como leída">
                                    <i class="fas fa-check"></i>
                                </button>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;
        });

        html += `<div class="mt-3"><small class="text-muted">Total: ${data.notificaciones.length} notificación(es) | No leídas: ${data.no_leidas}</small></div>`;

        container.innerHTML = html;

    } catch (error) {
        console.error('Error cargando notificaciones:', error);
        container.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Error de conexión al cargar notificaciones
            </div>
        `;
    }
}

async function marcarLeida(id) {
    const token = localStorage.getItem('token');

    try {
        const res = await fetch(`${API_BASE_URL}/api/empresas/notificaciones/${id}/leida`, {
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
            if (typeof actualizarContadorNotificacionesEmpresa === 'function') {
                actualizarContadorNotificacionesEmpresa();
            }
            await cargarNotificaciones();
        } else {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.error || 'Error al marcar notificación'
                });
            } else {
                alert('Error: ' + (data.error || 'Error al marcar notificación'));
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
            confirmButtonColor: '#007bff',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, marcar todas',
            cancelButtonText: 'Cancelar'
        });
        if (!result.isConfirmed) return;
    } else {
        if (!confirm('¿Marcar todas las notificaciones como leídas?')) {
            return;
        }
    }

    try {
        const res = await fetch(`${API_BASE_URL}/api/empresas/notificaciones/marcar-todas`, {
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
            if (typeof actualizarContadorNotificacionesEmpresa === 'function') {
                actualizarContadorNotificacionesEmpresa();
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
            } else {
                alert('Error: ' + (data.error || 'Error al marcar notificaciones'));
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
        } else {
            alert('Error de conexión al marcar notificaciones');
        }
    }
}
</script>
@endsection


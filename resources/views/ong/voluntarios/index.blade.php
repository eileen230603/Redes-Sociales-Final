@extends('layouts.adminlte')

@section('page_title', 'Gestión de Participantes')

@section('content_body')
<div class="container-fluid">
    <div class="card" style="border-radius: 12px; border: 1px solid #F5F5F5;">
        <div class="card-header" style="background: #F5F5F5; border-bottom: 1px solid #F5F5F5; border-radius: 12px 12px 0 0;">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0" style="color: #0C2B44; font-weight: 700;">
                    <i class="far fa-users mr-2" style="color: #00A36C;"></i> Lista de Participantes en Eventos
            </h3>
                <span class="badge badge-lg" id="totalVoluntarios" style="background: #0C2B44; color: white; padding: 0.5em 1em; border-radius: 20px; font-weight: 500;">0 participantes</span>
            </div>
        </div>
        <div class="card-body">
            <!-- Filtros y Búsqueda -->
            <div class="row mb-3" style="padding: 1rem 0;">
                <div class="col-md-6 mb-3 mb-md-0">
                    <label for="buscarVoluntario" class="form-label" style="color: #0C2B44; font-weight: 600; margin-bottom: 0.75rem;">
                        <i class="far fa-search mr-2" style="color: #00A36C;"></i> Buscar
                    </label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" style="background: #F5F5F5; border-color: #dee2e6; border-radius: 8px 0 0 8px;">
                                <i class="far fa-search" style="color: #00A36C;"></i>
                            </span>
                        </div>
                        <input type="text" id="buscarVoluntario" class="form-control" placeholder="Buscar por nombre, email, evento..." style="border-radius: 0 8px 8px 0; padding: 0.75rem;">
                    </div>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <label for="filtroEstado" class="form-label" style="color: #0C2B44; font-weight: 600; margin-bottom: 0.75rem;">
                        <i class="far fa-sliders-h mr-2" style="color: #00A36C;"></i> Estado
                    </label>
                    <select id="filtroEstado" class="form-control" style="border-radius: 8px; padding: 0.75rem;">
                        <option value="">Todos los estados</option>
                        <option value="aprobada">Aprobada</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-block" onclick="filtrarVoluntarios()" style="background: #F5F5F5; color: #0C2B44; border: none; border-radius: 8px; padding: 0.75rem; font-weight: 500;">
                        <i class="far fa-sync mr-2"></i> Actualizar
                    </button>
                </div>
            </div>

            <div id="voluntariosContainer">
                <div class="text-center py-5">
                    <div class="spinner-border" role="status" style="color: #00A36C; width: 3rem; height: 3rem;">
                        <span class="sr-only">Cargando...</span>
                    </div>
                    <p class="mt-3" style="color: #333333; font-weight: 500;">Cargando participantes...</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    .card {
        border-radius: 12px !important;
        border: 1px solid #F5F5F5 !important;
        box-shadow: 0 2px 8px rgba(12, 43, 68, 0.08) !important;
    }

    .card-header {
        background-color: #F5F5F5 !important;
        border-bottom: 1px solid #F5F5F5 !important;
        border-radius: 12px 12px 0 0 !important;
        padding: 1.25rem 1.5rem !important;
    }

    .card-header .card-title {
        font-weight: 700 !important;
        color: #0C2B44 !important;
    }

    .badge-lg {
        font-size: 0.9rem;
        padding: 0.5em 0.8em;
        border-radius: 20px;
    }

    .table {
        font-size: 0.95rem;
        border-radius: 8px;
        overflow: hidden;
    }

    .table thead th {
        background-color: #0C2B44 !important;
        color: white !important;
        border-bottom: 2px solid rgba(255, 255, 255, 0.1) !important;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        vertical-align: middle;
        padding: 1rem !important;
    }

    .table tbody tr {
        transition: background-color 0.2s ease;
        border-bottom: 1px solid #F5F5F5;
    }

    .table tbody tr:hover {
        background-color: rgba(12, 43, 68, 0.03) !important;
    }

    .table td {
        vertical-align: middle;
        padding: 1rem !important;
        color: #333333;
    }

    .avatar-img {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border: 3px solid #00A36C;
        box-shadow: 0 2px 8px rgba(0, 163, 108, 0.2);
    }

    .avatar-placeholder {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 1.1rem;
        box-shadow: 0 2px 8px rgba(12, 43, 68, 0.2);
    }

    .badge {
        padding: 0.4em 0.7em;
        font-weight: 500;
        font-size: 0.85rem;
        border-radius: 20px;
    }

    .badge-primary {
        background-color: #0C2B44 !important;
        color: white !important;
    }

    .badge-info {
        background-color: #00A36C !important;
        color: white !important;
    }

    .badge-success {
        background-color: #00A36C !important;
        color: white !important;
    }

    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .participant-name {
        font-weight: 700;
        color: #0C2B44;
        margin-bottom: 0.25rem;
        font-size: 1rem;
    }

    .participant-info {
        font-size: 0.85rem;
        color: #333333;
    }

    .participant-info i {
        width: 16px;
        text-align: center;
        color: #00A36C;
    }

    .evento-link {
        color: #0C2B44;
        font-weight: 600;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .evento-link:hover {
        color: #00A36C;
        text-decoration: underline;
    }

    .form-label {
        font-weight: 600;
        color: #0C2B44;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }

    .input-group-text {
        background-color: #F5F5F5;
        border-color: #dee2e6;
    }

    .form-control {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #00A36C;
        box-shadow: 0 0 0 0.2rem rgba(0, 163, 108, 0.15);
    }
</style>
@endpush

@push('js')
{{-- Script global para icono de notificaciones --}}
<script src="{{ asset('js/notificaciones-ong.js') }}"></script>
<script src="{{ asset('assets/js/config.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', async () => {
    const container = document.getElementById('voluntariosContainer');
    const token = localStorage.getItem('token');
    const ongId = parseInt(localStorage.getItem('id_entidad') || localStorage.getItem('id_usuario'), 10);

    if (!token || isNaN(ongId) || ongId <= 0) {
        container.innerHTML = '<div class="col-12"><div class="alert alert-danger">Debe iniciar sesión correctamente.</div></div>';
        return;
    }

    let voluntarios = [];

    // Cargar voluntarios
    async function cargarVoluntarios() {
        try {
            container.innerHTML = '<div class="col-12 text-center py-3"><i class="fas fa-spinner fa-spin"></i> Cargando...</div>';

            const res = await fetch(`${API_BASE_URL}/api/voluntarios/ong/${ongId}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const data = await res.json();
            
            if (!data.success) {
                throw new Error(data.error || 'Error al cargar voluntarios');
            }

            voluntarios = data.voluntarios || [];

            if (voluntarios.length === 0) {
                container.innerHTML = `
                    <div class="col-12">
                        <div class="text-center py-5" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); border-radius: 12px; padding: 3rem 2rem;">
                            <div style="background: rgba(255, 255, 255, 0.15); width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; backdrop-filter: blur(10px);">
                                <i class="far fa-users fa-3x text-white"></i>
                            </div>
                            <h5 style="color: white; font-weight: 600; margin-bottom: 0.5rem;">No hay voluntarios aún</h5>
                            <p style="color: rgba(255, 255, 255, 0.9); margin-bottom: 1.5rem;">Los voluntarios aparecerán aquí cuando se inscriban a tus eventos.</p>
                            <a href="/ong/eventos/crear" class="btn mt-2" style="background: white; color: #0C2B44; border: none; border-radius: 8px; padding: 0.75rem 1.5rem; font-weight: 600;">
                                <i class="far fa-calendar-plus mr-2"></i> Crear primer evento
                            </a>
                        </div>
                    </div>
                `;
                document.getElementById('totalVoluntarios').textContent = '0 voluntarios';
                return;
            }

            mostrarVoluntarios(voluntarios);

        } catch (error) {
            console.error('Error cargando voluntarios:', error);
            container.innerHTML = `
                <div class="col-12">
                    <div class="alert" style="background: #f8d7da; border: 1px solid #dc3545; color: #721c24; border-radius: 8px; padding: 1rem;">
                        <i class="far fa-exclamation-circle mr-2"></i> Error al cargar voluntarios: ${error.message}
                    </div>
                </div>
            `;
        }
    }

    function mostrarVoluntarios(vols) {
        if (vols.length === 0) {
            container.innerHTML = `
                <div class="text-center py-5" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); border-radius: 12px; padding: 3rem 2rem;">
                    <div style="background: rgba(255, 255, 255, 0.15); width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; backdrop-filter: blur(10px);">
                        <i class="far fa-users fa-3x text-white"></i>
                    </div>
                    <h5 style="color: white; font-weight: 600; margin-bottom: 0.5rem;">No hay participantes registrados</h5>
                    <p style="color: rgba(255, 255, 255, 0.9); margin: 0;">Los participantes aparecerán aquí cuando se inscriban a tus eventos.</p>
                </div>
            `;
            document.getElementById('totalVoluntarios').textContent = '0 participantes';
            return;
        }

        document.getElementById('totalVoluntarios').textContent = `${vols.length} participante${vols.length !== 1 ? 's' : ''}`;

        // Función para obtener color del badge según estado (solo aprobadas ahora)
        const getEstadoBadge = (estado) => {
            const estados = {
                'aprobada': { class: 'success', icon: 'far fa-check-circle', text: 'Aprobada' }
            };
            return estados[estado] || estados['aprobada'];
        };

        // Función para obtener color del badge según tipo de usuario
        const getTipoUsuarioBadge = (tipo) => {
            return tipo === 'Voluntario' 
                ? { class: 'primary', icon: 'far fa-user-check', text: 'Voluntario', style: 'background: #0C2B44; color: white;' }
                : { class: 'info', icon: 'far fa-user', text: 'Externo', style: 'background: #00A36C; color: white;' };
        };

        container.innerHTML = `
            <div class="table-responsive">
                <table class="table table-hover table-head-fixed text-nowrap">
                    <thead>
                        <tr>
                            <th style="width: 70px; text-align: center;">Avatar</th>
                            <th>Participante</th>
                            <th style="width: 120px;">Tipo de Usuario</th>
                            <th>Evento</th>
                            <th style="width: 130px;">Estado</th>
                            <th style="width: 160px;">Fecha Inscripción</th>
                            <th style="width: 120px; text-align: center;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${vols.map(vol => {
                            const inicial = (vol.nombre || 'U').charAt(0).toUpperCase();
                            const fotoPerfil = vol.foto_perfil || null;
                            const estadoInfo = getEstadoBadge(vol.estado || 'aprobada');
                            const tipoInfo = getTipoUsuarioBadge(vol.tipo_usuario || 'Externo');
                            
                            return `
                            <tr>
                                <td style="text-align: center;">
                                    ${fotoPerfil ? `
                                        <img src="${fotoPerfil}" alt="${vol.nombre || 'Usuario'}" class="avatar-img rounded-circle">
                                    ` : `
                                        <div class="avatar-placeholder mx-auto">
                                            ${inicial}
                                        </div>
                                    `}
                                </td>
                                <td>
                                    <div class="participant-name">${vol.nombre || 'Usuario'}</div>
                                    <div class="participant-info">
                                        <i class="far fa-envelope"></i> ${vol.email || 'Sin email'}
                                    </div>
                                    ${vol.telefono && vol.telefono !== 'No disponible' ? `
                                        <div class="participant-info">
                                            <i class="far fa-phone"></i> ${vol.telefono}
                                        </div>
                                    ` : ''}
                                </td>
                                <td>
                                    <span class="badge" style="${tipoInfo.style || 'background: #0C2B44; color: white;'}">
                                        <i class="${tipoInfo.icon}"></i> ${tipoInfo.text}
                                    </span>
                                </td>
                                <td>
                                    <a href="/ong/eventos/${vol.evento_id}/detalle" class="evento-link">
                                        <i class="far fa-calendar mr-1" style="color: #00A36C;"></i>${vol.evento_titulo || 'N/A'}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge" style="background: #00A36C; color: white;">
                                        <i class="far fa-check-circle mr-1"></i>${estadoInfo.text}
                                    </span>
                                </td>
                                <td>
                                    <small style="color: #333333;">
                                        <i class="far fa-calendar-check mr-1" style="color: #00A36C;"></i>
                                        ${vol.fecha_inscripcion_formateada || (vol.fecha_inscripcion ? new Date(vol.fecha_inscripcion).toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : 'N/A')}
                                    </small>
                                </td>
                                <td style="text-align: center;">
                                    <span class="badge" style="background: #00A36C; color: white;">
                                        <i class="far fa-check-circle"></i> Aprobada automáticamente
                                    </span>
                                </td>
                            </tr>
                            `;
                        }).join('')}
                    </tbody>
                </table>
            </div>
        `;
    }

    // Búsqueda y filtrado
    window.filtrarVoluntarios = function() {
        const termino = document.getElementById('buscarVoluntario').value.toLowerCase();
        const estadoFiltro = document.getElementById('filtroEstado').value;
        
        let filtrados = voluntarios.filter(vol => {
            const coincideBusqueda = 
                (vol.nombre || '').toLowerCase().includes(termino) ||
                (vol.email || '').toLowerCase().includes(termino) ||
                (vol.evento_titulo || '').toLowerCase().includes(termino) ||
                (vol.estado_label || '').toLowerCase().includes(termino) ||
                (vol.tipo_usuario || '').toLowerCase().includes(termino);
            
            const coincideEstado = !estadoFiltro || (vol.estado || 'aprobada') === estadoFiltro;
            
            return coincideBusqueda && coincideEstado;
        });
        
        mostrarVoluntarios(filtrados);
    };

    document.getElementById('buscarVoluntario').addEventListener('input', window.filtrarVoluntarios);
    document.getElementById('filtroEstado').addEventListener('change', window.filtrarVoluntarios);

    // Funciones para aprobar/rechazar participaciones
    window.aprobarParticipacion = async function(participacionId) {
        const token = localStorage.getItem('token');
        if (!token) return;

        if (typeof Swal !== 'undefined') {
            const result = await Swal.fire({
                title: '¿Aprobar participación?',
                text: 'El participante será aprobado para este evento',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, aprobar',
                cancelButtonText: 'Cancelar'
            });
            if (!result.isConfirmed) return;
        }

        try {
            const res = await fetch(`${API_BASE_URL}/api/eventos/participaciones/${participacionId}/aprobar`, {
                method: 'PUT',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            const data = await res.json();
            if (data.success) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Aprobado!',
                        text: 'La participación ha sido aprobada',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
                await cargarVoluntarios();
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.error || 'Error al aprobar participación'
                    });
                }
            }
        } catch (error) {
            console.error('Error:', error);
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo aprobar la participación'
                });
            }
        }
    }

    window.rechazarParticipacion = async function(participacionId) {
        const token = localStorage.getItem('token');
        if (!token) return;

        if (typeof Swal !== 'undefined') {
            const result = await Swal.fire({
                title: '¿Rechazar participación?',
                text: 'El participante será rechazado para este evento',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, rechazar',
                cancelButtonText: 'Cancelar'
            });
            if (!result.isConfirmed) return;
        }

        try {
            const res = await fetch(`${API_BASE_URL}/api/eventos/participaciones/${participacionId}/rechazar`, {
                method: 'PUT',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            const data = await res.json();
            if (data.success) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Rechazado',
                        text: 'La participación ha sido rechazada',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
                await cargarVoluntarios();
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.error || 'Error al rechazar participación'
                    });
                }
            }
        } catch (error) {
            console.error('Error:', error);
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo rechazar la participación'
                });
            }
        }
    }

    // Cargar inicialmente
    cargarVoluntarios();
});
</script>
@endpush


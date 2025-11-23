@extends('adminlte::page')

@section('title', 'Voluntarios | UNI2')

@section('content_header')
    <h1><i class="fas fa-users text-primary"></i> Gestión de Participantes</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-user-friends mr-2"></i> Lista de Participantes en Eventos
            </h3>
            <div class="card-tools">
                <span class="badge badge-primary badge-lg" id="totalVoluntarios">0 participantes</span>
            </div>
        </div>
        <div class="card-body">
            <!-- Filtros y Búsqueda -->
            <div class="row mb-3">
                <div class="col-md-6 mb-3 mb-md-0">
                    <label for="buscarVoluntario" class="form-label">
                        <i class="fas fa-search mr-1"></i> Buscar
                    </label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                        <input type="text" id="buscarVoluntario" class="form-control" placeholder="Buscar por nombre, email, evento o estado...">
                    </div>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <label for="filtroEstado" class="form-label">
                        <i class="fas fa-filter mr-1"></i> Estado
                    </label>
                    <select id="filtroEstado" class="form-control">
                        <option value="">Todos los estados</option>
                        <option value="aprobada">Aprobada</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-default btn-block" onclick="filtrarVoluntarios()">
                        <i class="fas fa-sync-alt"></i> Actualizar
                    </button>
                </div>
            </div>

            <div id="voluntariosContainer">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                    <p class="mt-3 text-muted">Cargando participantes...</p>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .card-primary.card-outline {
        border-top: 3px solid #007bff;
    }

    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }

    .card-header .card-title {
        font-weight: 600;
        color: #495057;
    }

    .badge-lg {
        font-size: 0.9rem;
        padding: 0.5em 0.8em;
    }

    .table {
        font-size: 0.95rem;
    }

    .table thead th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        color: #6c757d;
        vertical-align: middle;
    }

    .table tbody tr {
        transition: background-color 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .table td {
        vertical-align: middle;
    }

    .avatar-img {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border: 2px solid #dee2e6;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .avatar-placeholder {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 1.1rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .badge {
        padding: 0.4em 0.7em;
        font-weight: 500;
        font-size: 0.85rem;
    }

    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .participant-name {
        font-weight: 600;
        color: #212529;
        margin-bottom: 0.25rem;
    }

    .participant-info {
        font-size: 0.85rem;
        color: #6c757d;
    }

    .participant-info i {
        width: 16px;
        text-align: center;
    }

    .evento-link {
        color: #007bff;
        font-weight: 500;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .evento-link:hover {
        color: #0056b3;
        text-decoration: underline;
    }

    .form-label {
        font-weight: 500;
        color: #495057;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }

    .input-group-text {
        background-color: #e9ecef;
        border-color: #ced4da;
    }
</style>
@stop

@section('js')
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
                        <div class="alert alert-info text-center">
                            <i class="fas fa-users fa-2x mb-3"></i>
                            <h5>No hay voluntarios aún</h5>
                            <p>Los voluntarios aparecerán aquí cuando se inscriban a tus eventos.</p>
                            <a href="/ong/eventos/crear" class="btn btn-primary mt-2">
                                <i class="fas fa-calendar-plus"></i> Crear primer evento
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
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> Error al cargar voluntarios: ${error.message}
                    </div>
                </div>
            `;
        }
    }

    function mostrarVoluntarios(vols) {
        if (vols.length === 0) {
            container.innerHTML = `
                <div class="alert alert-info text-center">
                    <i class="fas fa-users fa-2x mb-3"></i>
                    <h5>No hay participantes registrados</h5>
                    <p>Los participantes aparecerán aquí cuando se inscriban a tus eventos.</p>
                </div>
            `;
            document.getElementById('totalVoluntarios').textContent = '0 participantes';
            return;
        }

        document.getElementById('totalVoluntarios').textContent = `${vols.length} participante${vols.length !== 1 ? 's' : ''}`;

        // Función para obtener color del badge según estado (solo aprobadas ahora)
        const getEstadoBadge = (estado) => {
            const estados = {
                'aprobada': { class: 'success', icon: 'fa-check-circle', text: 'Aprobada' }
            };
            return estados[estado] || estados['aprobada'];
        };

        // Función para obtener color del badge según tipo de usuario
        const getTipoUsuarioBadge = (tipo) => {
            return tipo === 'Voluntario' 
                ? { class: 'primary', icon: 'fa-user-check', text: 'Voluntario' }
                : { class: 'info', icon: 'fa-user', text: 'Externo' };
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
                                        <i class="fas fa-envelope"></i> ${vol.email || 'Sin email'}
                                    </div>
                                    ${vol.telefono && vol.telefono !== 'No disponible' ? `
                                        <div class="participant-info">
                                            <i class="fas fa-phone"></i> ${vol.telefono}
                                        </div>
                                    ` : ''}
                                </td>
                                <td>
                                    <span class="badge badge-${tipoInfo.class}">
                                        <i class="fas ${tipoInfo.icon}"></i> ${tipoInfo.text}
                                    </span>
                                </td>
                                <td>
                                    <a href="/ong/eventos/${vol.evento_id}/detalle" class="evento-link">
                                        <i class="fas fa-calendar-alt mr-1"></i>${vol.evento_titulo || 'N/A'}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge badge-${estadoInfo.class}">
                                        <i class="fas ${estadoInfo.icon} mr-1"></i>${estadoInfo.text}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar-check mr-1"></i>
                                        ${vol.fecha_inscripcion_formateada || (vol.fecha_inscripcion ? new Date(vol.fecha_inscripcion).toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : 'N/A')}
                                    </small>
                                </td>
                                <td style="text-align: center;">
                                    <span class="badge badge-success">
                                        <i class="fas fa-check-circle"></i> Aprobada automáticamente
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
@stop


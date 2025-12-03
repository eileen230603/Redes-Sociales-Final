@extends('layouts.adminlte')

@section('page_title', 'Gestión de Participantes')

@section('content_body')
<div class="container-fluid">
    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="small-box bg-primary" style="border-radius: 16px;">
                <div class="inner">
                    <h3 id="statTotalVoluntarios" class="text-white">0</h3>
                    <p class="text-white">Total Participantes</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="small-box bg-success" style="border-radius: 16px;">
                <div class="inner">
                    <h3 id="statVoluntarios" class="text-white">0</h3>
                    <p class="text-white">Voluntarios</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="small-box bg-info" style="border-radius: 16px;">
                <div class="inner">
                    <h3 id="statExternos" class="text-white">0</h3>
                    <p class="text-white">Externos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="small-box bg-warning" style="border-radius: 16px;">
                <div class="inner">
                    <h3 id="statNoRegistrados" class="text-white">0</h3>
                    <p class="text-white">No Registrados</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-clock"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros y Búsqueda -->
    <div class="card mb-4">
        <div class="card-header bg-primary">
            <h5 class="card-title mb-0 text-white">
                <i class="fas fa-filter mr-2"></i>Filtros y Búsqueda
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3 mb-md-0">
                    <label for="buscarVoluntario" class="font-weight-bold text-dark">
                        <i class="fas fa-search mr-2 text-info"></i> Buscar
                    </label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-search text-primary"></i>
                            </span>
                        </div>
                        <input type="text" id="buscarVoluntario" class="form-control" placeholder="Nombre, email, evento...">
                    </div>
                </div>
                <div class="col-md-3 mb-3 mb-md-0">
                    <label for="filtroTipoUsuario" class="font-weight-bold text-dark">
                        <i class="fas fa-user-tag mr-2 text-success"></i> Tipo de Usuario
                    </label>
                    <select id="filtroTipoUsuario" class="form-control">
                        <option value="">Todos</option>
                        <option value="Voluntario">Voluntario</option>
                        <option value="Externo">Externo</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3 mb-md-0">
                    <label for="filtroEvento" class="font-weight-bold text-dark">
                        <i class="fas fa-calendar mr-2 text-warning"></i> Evento
                    </label>
                    <select id="filtroEvento" class="form-control">
                        <option value="">Todos los eventos</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-secondary btn-block" onclick="filtrarVoluntarios()">
                        <i class="fas fa-sync mr-1"></i> Actualizar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Participantes -->
    <div class="card">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 text-dark">
                    <i class="fas fa-users mr-2 text-primary"></i> Lista de Participantes
                </h5>
                <span class="badge badge-primary badge-lg" id="totalVoluntarios">0 participantes</span>
            </div>
        </div>
        <div class="card-body">

            <div id="voluntariosContainer">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="sr-only">Cargando...</span>
                    </div>
                    <p class="mt-3 text-muted font-weight-bold">Cargando participantes...</p>
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
    let eventosUnicos = [];

    // Función para actualizar estadísticas
    function actualizarEstadisticas(vols) {
        const total = vols.length;
        const voluntarios = vols.filter(v => v.tipo_usuario === 'Voluntario').length; // Los no registrados
        const externos = vols.filter(v => v.tipo_usuario === 'Externo').length; // Los registrados
        
        const statTotal = document.getElementById('statTotalVoluntarios');
        const statVoluntarios = document.getElementById('statVoluntarios');
        const statExternos = document.getElementById('statExternos');
        const statNoRegistrados = document.getElementById('statNoRegistrados');
        
        if (statTotal) statTotal.textContent = total;
        if (statVoluntarios) statVoluntarios.textContent = voluntarios;
        if (statExternos) statExternos.textContent = externos;
        if (statNoRegistrados) statNoRegistrados.textContent = voluntarios; // Los voluntarios son los no registrados
    }

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
            
            // Extraer eventos únicos para el filtro
            eventosUnicos = [...new Set(voluntarios.map(v => ({ id: v.evento_id, titulo: v.evento_titulo })))];
            eventosUnicos = eventosUnicos.filter(e => e.id && e.titulo);
            
            // Actualizar select de eventos
            const selectEvento = document.getElementById('filtroEvento');
            if (selectEvento) {
                const opcionesActuales = selectEvento.querySelectorAll('option:not(:first-child)');
                opcionesActuales.forEach(opt => opt.remove());
                
                eventosUnicos.forEach(evento => {
                    const option = document.createElement('option');
                    option.value = evento.id;
                    option.textContent = evento.titulo;
                    selectEvento.appendChild(option);
                });
            }
            
            // Actualizar estadísticas
            actualizarEstadisticas(voluntarios);

            if (voluntarios.length === 0) {
                container.innerHTML = `
                    <div class="col-12">
                        <div class="text-center py-5 bg-light rounded">
                            <div class="bg-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-users fa-3x text-white"></i>
                            </div>
                            <h5 class="font-weight-bold text-dark mb-2">No hay participantes aún</h5>
                            <p class="text-muted mb-3">Los participantes aparecerán aquí cuando se inscriban a tus eventos.</p>
                            <a href="/ong/eventos/crear" class="btn btn-success">
                                <i class="fas fa-calendar-plus mr-2"></i> Crear primer evento
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
                        <i class="fas fa-exclamation-circle mr-2"></i> Error al cargar voluntarios: ${error.message}
                    </div>
                </div>
            `;
        }
    }

    function mostrarVoluntarios(vols) {
        if (vols.length === 0) {
            container.innerHTML = `
                <div class="text-center py-5 bg-light rounded">
                    <div class="bg-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-users fa-3x text-white"></i>
                    </div>
                    <h5 class="font-weight-bold text-dark mb-2">No hay participantes registrados</h5>
                    <p class="text-muted mb-0">Los participantes aparecerán aquí cuando se inscriban a tus eventos.</p>
                </div>
            `;
            document.getElementById('totalVoluntarios').textContent = '0 participantes';
            return;
        }

        document.getElementById('totalVoluntarios').textContent = `${vols.length} participante${vols.length !== 1 ? 's' : ''}`;

        // Función para obtener color del badge según estado
        const getEstadoBadge = (estado) => {
            const estados = {
                'aprobada': { class: 'success', icon: 'fas fa-check-circle', text: 'Aprobada' }
            };
            return estados[estado] || estados['aprobada'];
        };

        // Función para obtener color del badge según tipo de usuario
        const getTipoUsuarioBadge = (tipo) => {
            if (tipo === 'Voluntario') {
                return { class: 'success', icon: 'fas fa-user-check', text: 'Voluntario' };
            } else {
                return { class: 'info', icon: 'fas fa-user', text: 'Externo' };
            }
        };

        // Actualizar estadísticas con los filtrados
        actualizarEstadisticas(vols);
        
        container.innerHTML = `
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="width: 70px; text-align: center;">
                                <i class="fas fa-user-circle"></i>
                            </th>
                            <th>Participante</th>
                            <th style="width: 120px;">Tipo</th>
                            <th>Evento</th>
                            <th style="width: 100px;">Asistencia</th>
                            <th style="width: 130px;">Estado</th>
                            <th style="width: 160px;">Fecha Inscripción</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${vols.map(vol => {
                            const inicial = (vol.nombre || 'U').charAt(0).toUpperCase();
                            const fotoPerfil = vol.foto_perfil || null;
                            const estadoInfo = getEstadoBadge(vol.estado || 'aprobada');
                            const tipoInfo = getTipoUsuarioBadge(vol.tipo_usuario || 'Externo');
                            const puntos = parseInt(vol.puntos) || 0;
                            const asistio = vol.asistio !== undefined ? vol.asistio : true; // Por defecto Sí
                            
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
                                        <i class="fas fa-envelope text-info"></i> ${vol.email || 'Sin email'}
                                    </div>
                                    ${vol.telefono && vol.telefono !== 'No disponible' ? `
                                        <div class="participant-info">
                                            <i class="fas fa-phone text-success"></i> ${vol.telefono}
                                        </div>
                                    ` : ''}
                                </td>
                                <td>
                                    <span class="badge badge-${tipoInfo.class}">
                                        <i class="${tipoInfo.icon} mr-1"></i> ${tipoInfo.text}
                                    </span>
                                </td>
                                <td>
                                    <a href="/ong/eventos/${vol.evento_id}/detalle" class="evento-link">
                                        <i class="fas fa-calendar mr-1 text-warning"></i>${vol.evento_titulo || 'N/A'}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge badge-success">
                                        <i class="fas fa-check-circle mr-1"></i>Sí
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-${estadoInfo.class}">
                                        <i class="${estadoInfo.icon} mr-1"></i>${estadoInfo.text}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar-check mr-1 text-primary"></i>
                                        ${vol.fecha_inscripcion_formateada || (vol.fecha_inscripcion ? new Date(vol.fecha_inscripcion).toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : 'N/A')}
                                    </small>
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
        const tipoFiltro = document.getElementById('filtroTipoUsuario').value;
        const eventoFiltro = document.getElementById('filtroEvento').value;
        
        let filtrados = voluntarios.filter(vol => {
            const coincideBusqueda = 
                (vol.nombre || '').toLowerCase().includes(termino) ||
                (vol.email || '').toLowerCase().includes(termino) ||
                (vol.evento_titulo || '').toLowerCase().includes(termino) ||
                (vol.estado_label || '').toLowerCase().includes(termino) ||
                (vol.tipo_usuario || '').toLowerCase().includes(termino);
            
            const coincideTipo = !tipoFiltro || (vol.tipo_usuario || 'Externo') === tipoFiltro;
            const coincideEvento = !eventoFiltro || vol.evento_id == eventoFiltro;
            
            return coincideBusqueda && coincideTipo && coincideEvento;
        });
        
        mostrarVoluntarios(filtrados);
    };

    document.getElementById('buscarVoluntario').addEventListener('input', window.filtrarVoluntarios);
    document.getElementById('filtroTipoUsuario').addEventListener('change', window.filtrarVoluntarios);
    document.getElementById('filtroEvento').addEventListener('change', window.filtrarVoluntarios);

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



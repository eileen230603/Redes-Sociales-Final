@extends('adminlte::page')

@section('title', 'Voluntarios | UNI2')

@section('content_header')
    <h1><i class="fas fa-users text-primary"></i> Gestión de Voluntarios</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="fas fa-user-friends mr-2"></i> Lista de Voluntarios</h4>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" id="buscarVoluntario" class="form-control" placeholder="Buscar por nombre, email o evento...">
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <span class="badge bg-info" id="totalVoluntarios">0 voluntarios</span>
                </div>
            </div>

            <div id="voluntariosContainer" class="row">
                <div class="col-12 text-center py-5">
                    <i class="fas fa-spinner fa-spin fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Cargando voluntarios...</p>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
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
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="fas fa-users fa-2x mb-3"></i>
                        <h5>No hay voluntarios registrados</h5>
                        <p>Los voluntarios aparecerán aquí cuando se inscriban a tus eventos.</p>
                    </div>
                </div>
            `;
            document.getElementById('totalVoluntarios').textContent = '0 voluntarios';
            return;
        }

        document.getElementById('totalVoluntarios').textContent = `${vols.length} voluntario${vols.length !== 1 ? 's' : ''}`;

        container.innerHTML = vols.map(vol => `
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card shadow-sm h-100 border-left-primary">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar-circle bg-gradient-primary text-white me-3" style="
                                width: 55px;
                                height: 55px;
                                border-radius: 50%;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                font-size: 22px;
                                font-weight: bold;
                                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                            ">
                                ${(vol.nombre || 'V').charAt(0).toUpperCase()}
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <h5 class="mb-0 text-truncate" title="${vol.nombre || 'Voluntario'}">${vol.nombre || 'Voluntario'}</h5>
                                <small class="text-muted d-block text-truncate" title="${vol.email || ''}">
                                    <i class="fas fa-envelope"></i> ${vol.email || 'Sin email'}
                                </small>
                            </div>
                        </div>
                        <div class="mb-3 p-2 bg-light rounded">
                            <small class="text-muted d-block mb-1"><i class="fas fa-calendar-alt text-primary"></i> Evento:</small>
                            <p class="mb-0 fw-bold text-truncate" title="${vol.evento_titulo || 'N/A'}">${vol.evento_titulo || 'N/A'}</p>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge ${vol.asistio ? 'bg-success' : 'bg-secondary'} px-3 py-2">
                                <i class="fas ${vol.asistio ? 'fa-check-circle' : 'fa-clock'}"></i> 
                                ${vol.asistio ? 'Asistió' : 'Pendiente'}
                            </span>
                            <span class="badge bg-info px-3 py-2">
                                <i class="fas fa-star"></i> ${vol.puntos || 0} pts
                            </span>
                        </div>
                        ${vol.fecha_inscripcion ? `
                            <small class="text-muted d-block mt-2">
                                <i class="fas fa-calendar-check"></i> Inscrito: ${new Date(vol.fecha_inscripcion).toLocaleDateString('es-ES')}
                            </small>
                        ` : ''}
                    </div>
                </div>
            </div>
        `).join('');
    }

    // Búsqueda
    document.getElementById('buscarVoluntario').addEventListener('input', (e) => {
        const termino = e.target.value.toLowerCase();
        const filtrados = voluntarios.filter(vol => 
            (vol.nombre || '').toLowerCase().includes(termino) ||
            (vol.email || '').toLowerCase().includes(termino) ||
            (vol.evento_titulo || '').toLowerCase().includes(termino)
        );
        mostrarVoluntarios(filtrados);
    });

    // Cargar inicialmente
    cargarVoluntarios();
});
</script>
@stop


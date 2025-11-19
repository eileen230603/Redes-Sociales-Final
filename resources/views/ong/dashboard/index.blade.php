@extends('layouts.adminlte')

@section('page_title', 'Dashboard Extendido')

@section('content_body')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-primary">
            <i class="fas fa-tachometer-alt mr-2"></i> Dashboard Extendido
        </h1>
        <button class="btn btn-outline-primary" onclick="cargarDatos()">
            <i class="fas fa-sync-alt mr-2"></i> Actualizar
        </button>
    </div>

    <!-- Tabs de Navegación -->
    <ul class="nav nav-tabs mb-4" id="dashboardTabs" role="tablist" style="border-bottom: 2px solid #e9ecef;">
        <li class="nav-item">
            <a class="nav-link active" id="participantes-tab" data-toggle="tab" href="#participantes" role="tab" aria-controls="participantes" aria-selected="true">
                <i class="fas fa-users mr-2"></i> Dashboard de Participantes
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="reacciones-tab" data-toggle="tab" href="#reacciones" role="tab" aria-controls="reacciones" aria-selected="false">
                <i class="fas fa-heart mr-2"></i> Dashboard de Reacciones
            </a>
        </li>
    </ul>

    <!-- Contenido de las Tabs -->
    <div class="tab-content" id="dashboardTabContent">
        <!-- Tab: Participantes -->
        <div class="tab-pane fade show active" id="participantes" role="tabpanel" aria-labelledby="participantes-tab">
            <!-- Estadísticas Generales -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm" style="border-left: 4px solid #007bff;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Total Participantes</h6>
                                    <h3 class="mb-0" id="totalParticipantes">0</h3>
                                </div>
                                <i class="fas fa-users fa-2x text-primary" style="opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm" style="border-left: 4px solid #28a745;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Aprobados</h6>
                                    <h3 class="mb-0 text-success" id="totalAprobados">0</h3>
                                </div>
                                <i class="fas fa-check-circle fa-2x text-success" style="opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm" style="border-left: 4px solid #ffc107;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Pendientes</h6>
                                    <h3 class="mb-0 text-warning" id="totalPendientes">0</h3>
                                </div>
                                <i class="fas fa-clock fa-2x text-warning" style="opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm" style="border-left: 4px solid #dc3545;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Rechazados</h6>
                                    <h3 class="mb-0 text-danger" id="totalRechazados">0</h3>
                                </div>
                                <i class="fas fa-times-circle fa-2x text-danger" style="opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráficos -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-pie mr-2 text-primary"></i> Distribución por Estado
                            </h5>
                        </div>
                        <div class="card-body">
                            <canvas id="graficoEstadoParticipantes" height="250"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-bar mr-2 text-primary"></i> Participantes por Evento
                            </h5>
                        </div>
                        <div class="card-body">
                            <canvas id="graficoParticipantesPorEvento" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista Detallada de Participantes -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-list mr-2 text-primary"></i> Lista Detallada de Participantes
                    </h5>
                    <div class="form-group mb-0" style="max-width: 300px;">
                        <select id="filtroEventoParticipantes" class="form-control form-control-sm">
                            <option value="">Todos los eventos</option>
                        </select>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 50px;">Avatar</th>
                                    <th>Nombre</th>
                                    <th>Correo</th>
                                    <th>Teléfono</th>
                                    <th>Evento</th>
                                    <th>Fecha Inscripción</th>
                                    <th>Estado</th>
                                    <th style="width: 150px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tablaParticipantes">
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="sr-only">Cargando...</span>
                                        </div>
                                        <p class="mt-2 text-muted">Cargando participantes...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab: Reacciones -->
        <div class="tab-pane fade" id="reacciones" role="tabpanel" aria-labelledby="reacciones-tab">
            <!-- Estadísticas Generales -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm" style="border-left: 4px solid #dc3545;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Total de Reacciones</h6>
                                    <h3 class="mb-0 text-danger" id="totalReacciones">0</h3>
                                </div>
                                <i class="fas fa-heart fa-3x text-danger" style="opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráfico de Reacciones -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-bar mr-2 text-danger"></i> Reacciones por Evento
                            </h5>
                        </div>
                        <div class="card-body">
                            <canvas id="graficoReaccionesPorEvento" height="100"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista Detallada de Reacciones -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-list mr-2 text-danger"></i> Lista Detallada de Reacciones
                    </h5>
                    <div class="form-group mb-0" style="max-width: 300px;">
                        <select id="filtroEventoReacciones" class="form-control form-control-sm">
                            <option value="">Todos los eventos</option>
                        </select>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 50px;">Avatar</th>
                                    <th>Nombre</th>
                                    <th>Correo</th>
                                    <th>Evento</th>
                                    <th>Fecha y Hora</th>
                                </tr>
                            </thead>
                            <tbody id="tablaReacciones">
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="sr-only">Cargando...</span>
                                        </div>
                                        <p class="mt-2 text-muted">Cargando reacciones...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
    .card {
        border-radius: 8px;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
    }

    .nav-tabs .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        color: #6c757d;
        font-weight: 500;
    }

    .nav-tabs .nav-link:hover {
        border-bottom-color: #dee2e6;
        color: #495057;
    }

    .nav-tabs .nav-link.active {
        border-bottom-color: #007bff;
        color: #007bff;
        background: transparent;
    }

    .table th {
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        color: #6c757d;
        border-top: none;
    }

    .badge {
        padding: 0.4em 0.8em;
        font-weight: 500;
    }

    .avatar-sm {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }

    .avatar-placeholder {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 0.9rem;
    }
</style>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('assets/js/config.js') }}"></script>
<script>
let chartEstadoParticipantes = null;
let chartParticipantesPorEvento = null;
let chartReaccionesPorEvento = null;

document.addEventListener('DOMContentLoaded', () => {
    cargarDatos();
    
    // Filtros
    document.getElementById('filtroEventoParticipantes').addEventListener('change', function() {
        cargarListaParticipantes(this.value);
    });
    
    document.getElementById('filtroEventoReacciones').addEventListener('change', function() {
        cargarListaReacciones(this.value);
    });
});

async function cargarDatos() {
    await Promise.all([
        cargarEstadisticasParticipantes(),
        cargarListaParticipantes(),
        cargarEstadisticasReacciones(),
        cargarListaReacciones()
    ]);
}

// ========== PARTICIPANTES ==========
async function cargarEstadisticasParticipantes() {
    const token = localStorage.getItem('token');
    
    try {
        const res = await fetch(`${API_BASE_URL}/api/dashboard-ong/participantes/estadisticas`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        const data = await res.json();
        
        if (!res.ok || !data.success) {
            console.error('Error:', data.error);
            return;
        }

        // Actualizar totales
        document.getElementById('totalParticipantes').textContent = data.totales.total || 0;
        document.getElementById('totalAprobados').textContent = data.totales.aprobados || 0;
        document.getElementById('totalPendientes').textContent = data.totales.pendientes || 0;
        document.getElementById('totalRechazados').textContent = data.totales.rechazados || 0;

        // Actualizar filtro de eventos
        const filtro = document.getElementById('filtroEventoParticipantes');
        const eventosUnicos = [...new Set(data.estadisticas_por_evento.map(e => e.evento_id))];
        filtro.innerHTML = '<option value="">Todos los eventos</option>';
        data.estadisticas_por_evento.forEach(est => {
            filtro.innerHTML += `<option value="${est.evento_id}">${est.evento_titulo}</option>`;
        });

        // Gráfico de pastel - Distribución por estado
        const ctx1 = document.getElementById('graficoEstadoParticipantes');
        if (chartEstadoParticipantes) chartEstadoParticipantes.destroy();
        chartEstadoParticipantes = new Chart(ctx1, {
            type: 'pie',
            data: {
                labels: ['Aprobados', 'Pendientes', 'Rechazados'],
                datasets: [{
                    data: [
                        data.totales.aprobados,
                        data.totales.pendientes,
                        data.totales.rechazados
                    ],
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Gráfico de barras - Participantes por evento
        const ctx2 = document.getElementById('graficoParticipantesPorEvento');
        if (chartParticipantesPorEvento) chartParticipantesPorEvento.destroy();
        chartParticipantesPorEvento = new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: data.estadisticas_por_evento.map(e => e.evento_titulo.length > 20 ? e.evento_titulo.substring(0, 20) + '...' : e.evento_titulo),
                datasets: [
                    {
                        label: 'Aprobados',
                        data: data.estadisticas_por_evento.map(e => e.aprobados),
                        backgroundColor: '#28a745'
                    },
                    {
                        label: 'Pendientes',
                        data: data.estadisticas_por_evento.map(e => e.pendientes),
                        backgroundColor: '#ffc107'
                    },
                    {
                        label: 'Rechazados',
                        data: data.estadisticas_por_evento.map(e => e.rechazados),
                        backgroundColor: '#dc3545'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        position: 'top'
                    }
                }
            }
        });

    } catch (error) {
        console.error('Error cargando estadísticas de participantes:', error);
    }
}

async function cargarListaParticipantes(eventoId = '') {
    const token = localStorage.getItem('token');
    const tbody = document.getElementById('tablaParticipantes');
    
    tbody.innerHTML = `
        <tr>
            <td colspan="8" class="text-center py-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Cargando...</span>
                </div>
            </td>
        </tr>
    `;

    try {
        const url = eventoId 
            ? `${API_BASE_URL}/api/dashboard-ong/participantes/lista?evento_id=${eventoId}`
            : `${API_BASE_URL}/api/dashboard-ong/participantes/lista`;
            
        const res = await fetch(url, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        const data = await res.json();
        
        if (!res.ok || !data.success) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-3 text-danger">
                        ${data.error || 'Error al cargar participantes'}
                    </td>
                </tr>
            `;
            return;
        }

        if (!data.participantes || data.participantes.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="fas fa-users fa-3x mb-3"></i>
                        <p>No hay participantes registrados</p>
                    </td>
                </tr>
            `;
            return;
        }

        let html = '';
        data.participantes.forEach(participante => {
            const fecha = new Date(participante.fecha_inscripcion).toLocaleString('es-ES', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            const inicial = (participante.nombre || 'U').charAt(0).toUpperCase();
            const avatar = participante.foto_perfil 
                ? `<img src="${participante.foto_perfil}" alt="${participante.nombre}" class="avatar-sm">`
                : `<div class="avatar-placeholder">${inicial}</div>`;

            const estadoBadge = {
                'aprobada': '<span class="badge badge-success">Aprobada</span>',
                'pendiente': '<span class="badge badge-warning">Pendiente</span>',
                'rechazada': '<span class="badge badge-danger">Rechazada</span>'
            }[participante.estado] || '<span class="badge badge-secondary">' + participante.estado + '</span>';

            const acciones = participante.estado === 'pendiente' 
                ? `
                    <button class="btn btn-sm btn-success mr-1" onclick="aprobarParticipacion(${participante.id})" title="Aprobar">
                        <i class="fas fa-check"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="rechazarParticipacion(${participante.id})" title="Rechazar">
                        <i class="fas fa-times"></i>
                    </button>
                `
                : '<span class="text-muted">-</span>';

            html += `
                <tr>
                    <td>${avatar}</td>
                    <td><strong>${participante.nombre || 'N/A'}</strong></td>
                    <td>${participante.correo || 'N/A'}</td>
                    <td>${participante.telefono || 'N/A'}</td>
                    <td>${participante.evento_titulo || 'N/A'}</td>
                    <td>${fecha}</td>
                    <td>${estadoBadge}</td>
                    <td>${acciones}</td>
                </tr>
            `;
        });

        tbody.innerHTML = html;

    } catch (error) {
        console.error('Error cargando lista de participantes:', error);
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-3 text-danger">
                    Error de conexión al cargar participantes
                </td>
            </tr>
        `;
    }
}

// ========== REACCIONES ==========
async function cargarEstadisticasReacciones() {
    const token = localStorage.getItem('token');
    
    try {
        const res = await fetch(`${API_BASE_URL}/api/dashboard-ong/reacciones/estadisticas`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        const data = await res.json();
        
        if (!res.ok || !data.success) {
            console.error('Error:', data.error);
            return;
        }

        // Actualizar total
        document.getElementById('totalReacciones').textContent = data.total_reacciones || 0;

        // Actualizar filtro de eventos
        const filtro = document.getElementById('filtroEventoReacciones');
        filtro.innerHTML = '<option value="">Todos los eventos</option>';
        data.estadisticas_por_evento.forEach(est => {
            filtro.innerHTML += `<option value="${est.evento_id}">${est.evento_titulo}</option>`;
        });

        // Gráfico de barras - Reacciones por evento
        const ctx = document.getElementById('graficoReaccionesPorEvento');
        if (chartReaccionesPorEvento) chartReaccionesPorEvento.destroy();
        chartReaccionesPorEvento = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.estadisticas_por_evento.map(e => e.evento_titulo.length > 20 ? e.evento_titulo.substring(0, 20) + '...' : e.evento_titulo),
                datasets: [{
                    label: 'Reacciones',
                    data: data.estadisticas_por_evento.map(e => e.total_reacciones),
                    backgroundColor: '#dc3545',
                    borderColor: '#c82333',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

    } catch (error) {
        console.error('Error cargando estadísticas de reacciones:', error);
    }
}

async function cargarListaReacciones(eventoId = '') {
    const token = localStorage.getItem('token');
    const tbody = document.getElementById('tablaReacciones');
    
    tbody.innerHTML = `
        <tr>
            <td colspan="5" class="text-center py-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Cargando...</span>
                </div>
            </td>
        </tr>
    `;

    try {
        const url = eventoId 
            ? `${API_BASE_URL}/api/dashboard-ong/reacciones/lista?evento_id=${eventoId}`
            : `${API_BASE_URL}/api/dashboard-ong/reacciones/lista`;
            
        const res = await fetch(url, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        const data = await res.json();
        
        if (!res.ok || !data.success) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center py-3 text-danger">
                        ${data.error || 'Error al cargar reacciones'}
                    </td>
                </tr>
            `;
            return;
        }

        if (!data.reacciones || data.reacciones.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        <i class="fas fa-heart fa-3x mb-3 text-danger"></i>
                        <p>Aún no hay reacciones en tus eventos</p>
                    </td>
                </tr>
            `;
            return;
        }

        let html = '';
        data.reacciones.forEach(reaccion => {
            const fecha = new Date(reaccion.fecha_reaccion).toLocaleString('es-ES', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            const inicial = (reaccion.nombre || 'U').charAt(0).toUpperCase();
            const avatar = reaccion.foto_perfil 
                ? `<img src="${reaccion.foto_perfil}" alt="${reaccion.nombre}" class="avatar-sm">`
                : `<div class="avatar-placeholder">${inicial}</div>`;

            html += `
                <tr>
                    <td>${avatar}</td>
                    <td><strong>${reaccion.nombre || 'N/A'}</strong></td>
                    <td>${reaccion.correo || 'N/A'}</td>
                    <td>${reaccion.evento_titulo || 'N/A'}</td>
                    <td>${fecha}</td>
                </tr>
            `;
        });

        tbody.innerHTML = html;

    } catch (error) {
        console.error('Error cargando lista de reacciones:', error);
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center py-3 text-danger">
                    Error de conexión al cargar reacciones
                </td>
            </tr>
        `;
    }
}

// ========== ACCIONES ==========
async function aprobarParticipacion(participacionId) {
    if (typeof Swal === 'undefined') {
        if (!confirm('¿Aprobar esta participación?')) return;
    } else {
        const result = await Swal.fire({
            title: '¿Aprobar participación?',
            text: 'El voluntario será notificado de la aprobación',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, aprobar',
            cancelButtonText: 'Cancelar'
        });
        if (!result.isConfirmed) return;
    }

    const token = localStorage.getItem('token');
    
    try {
        const res = await fetch(`${API_BASE_URL}/api/participaciones/${participacionId}/aprobar`, {
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
                    text: 'La participación ha sido aprobada correctamente',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
            await cargarDatos();
        } else {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.error || 'Error al aprobar la participación'
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

async function rechazarParticipacion(participacionId) {
    if (typeof Swal === 'undefined') {
        if (!confirm('¿Rechazar esta participación?')) return;
    } else {
        const result = await Swal.fire({
            title: '¿Rechazar participación?',
            text: 'El voluntario será notificado del rechazo',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, rechazar',
            cancelButtonText: 'Cancelar'
        });
        if (!result.isConfirmed) return;
    }

    const token = localStorage.getItem('token');
    
    try {
        const res = await fetch(`${API_BASE_URL}/api/participaciones/${participacionId}/rechazar`, {
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
            await cargarDatos();
        } else {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.error || 'Error al rechazar la participación'
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
</script>
@endsection


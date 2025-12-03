@extends('layouts.adminlte')

@section('page_title', 'Seguimiento de Mega Evento')

@section('content_body')
<div class="container-fluid">
    <!-- Loading State -->
    <div id="loadingMessage" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Cargando...</span>
        </div>
        <p class="mt-3 text-muted">Cargando información de seguimiento...</p>
    </div>

    <div id="seguimientoContent" style="display: none;">
        <!-- Header con información del mega evento -->
        <div class="card mb-4">
            <div class="card-header bg-primary">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="flex-grow-1">
                        <h3 class="card-title mb-1 text-white" id="tituloMegaEvento">
                            <i class="fas fa-star mr-2"></i>-
                        </h3>
                        <p class="mb-0 text-white-50" id="fechasMegaEvento">-</p>
                    </div>
                    <div>
                        <a href="#" id="volverLink" class="btn btn-sm btn-secondary mr-2">
                            <i class="fas fa-arrow-left mr-1"></i> Volver
                        </a>
                        <button id="btnExportarReporte" class="btn btn-sm btn-success">
                            <i class="fas fa-file-export mr-1"></i> Exportar Reporte
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel de Métricas Principales -->
        <div class="row mb-4">
            <!-- Total Participantes -->
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="small-box bg-primary stat-card-custom">
                    <div class="inner">
                        <h3 id="totalParticipantes" class="text-white">0</h3>
                        <p class="text-white">Total Participantes</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>

            <!-- Participantes Aprobados -->
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="small-box bg-success stat-card-custom">
                    <div class="inner">
                        <h3 id="participantesAprobados" class="text-white">0</h3>
                        <p class="text-white">Aprobados</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>

            <!-- Tasa de Confirmación -->
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="small-box bg-info stat-card-custom">
                    <div class="inner">
                        <h3 id="tasaConfirmacion" class="text-white">0%</h3>
                        <p class="text-white">Tasa Confirmación</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-percentage"></i>
                    </div>
                </div>
            </div>

            <!-- Capacidad -->
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="small-box bg-warning stat-card-custom">
                    <div class="inner">
                        <h3 id="porcentajeCapacidad" class="text-white">-</h3>
                        <p class="text-white">Capacidad</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel de Alertas -->
        <div id="panelAlertas" class="card mb-4" style="display: none;">
            <div class="card-header bg-warning">
                <h5 class="card-title mb-0 text-white">
                    <i class="fas fa-bell mr-2"></i>Alertas y Notificaciones
                </h5>
            </div>
            <div class="card-body p-0">
                <div id="alertasContainer" class="p-3">
                    <!-- Las alertas se cargarán aquí dinámicamente -->
                </div>
            </div>
        </div>

        <!-- Métricas de Interacción: Reacciones, Compartidos, Participaciones -->
        <div class="row mb-4">
            <!-- Total Reacciones -->
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="info-box">
                    <span class="info-box-icon bg-danger elevation-1">
                        <i class="fas fa-heart"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text font-weight-bold">Total Reacciones</span>
                        <span class="info-box-number" id="totalReacciones">0</span>
                        <small class="text-muted">Me gusta recibidos</small>
                    </div>
                </div>
            </div>

            <!-- Total Compartidos -->
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="info-box">
                    <span class="info-box-icon bg-warning elevation-1">
                        <i class="fas fa-share-alt"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text font-weight-bold">Total Compartidos</span>
                        <span class="info-box-number" id="totalCompartidos">0</span>
                        <small class="text-muted">Veces compartido</small>
                    </div>
                </div>
            </div>

            <!-- Total Participaciones -->
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="info-box">
                    <span class="info-box-icon bg-success elevation-1">
                        <i class="fas fa-user-check"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text font-weight-bold">Total Participaciones</span>
                        <span class="info-box-number" id="totalParticipaciones">0</span>
                        <small class="text-muted" id="detalleParticipaciones">0 registrados, 0 no registrados</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Seguimiento por Tipo de Actor -->
        <div class="row mb-4">
            <!-- ONG Organizadora -->
            <div class="col-lg-12 col-md-12 mb-3">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h6 class="card-title mb-0 text-white">
                            <i class="fas fa-flag mr-2"></i>ONG Organizadora
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="font-weight-bold text-dark">Cumplimiento de tareas</span>
                                <strong id="porcentajeCumplimientoOng" class="text-success">0%</strong>
                            </div>
                            <div class="progress">
                                <div id="barraCumplimientoOng" class="progress-bar bg-success" role="progressbar" style="width: 0%;"></div>
                            </div>
                        </div>
                        <div id="tareasOngContainer" class="mt-3">
                            <!-- Tareas se cargarán dinámicamente -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficas -->
        <div class="row mb-4">
            <!-- Gráfica de Inscripciones por Día -->
            <div class="col-lg-6 col-md-12 mb-3">
                <div class="card">
                    <div class="card-header bg-danger">
                        <h6 class="card-title mb-0 text-white">
                            <i class="fas fa-heart mr-2"></i>Reacciones por Día
                        </h6>
                        <small class="text-white-50">Últimos 30 días</small>
                    </div>
                    <div class="card-body">
                        <div style="height: 300px; position: relative;">
                            <canvas id="graficaInscripciones"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráfica de Estado de Participantes -->
            <div class="col-lg-6 col-md-12 mb-3">
                <div class="card">
                    <div class="card-header bg-success">
                        <h6 class="card-title mb-0 text-white">
                            <i class="fas fa-chart-pie mr-2"></i>Estado de Participantes
                        </h6>
                        <small class="text-white-50">Distribución actual</small>
                    </div>
                    <div class="card-body">
                        <div style="height: 300px; position: relative;">
                            <canvas id="graficaEstados"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Participantes -->
        <div class="card mb-4">
            <div class="card-header bg-primary">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <h5 class="card-title mb-0 text-white">
                        <i class="fas fa-users mr-2"></i>Seguimiento de Inscripciones
                    </h5>
                    <div class="d-flex align-items-center mt-2 mt-md-0">
                        <select id="filtroEstadoParticipante" class="form-control form-control-sm mr-2" style="width: auto;">
                            <option value="todos">Todos los estados</option>
                            <option value="aprobada">Aprobados</option>
                            <option value="pendiente">Pendientes</option>
                            <option value="rechazada">Rechazados</option>
                        </select>
                        <input type="text" id="buscadorParticipantes" class="form-control form-control-sm" placeholder="Buscar participante..." style="width: 200px;">
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Participante</th>
                                <th>Email</th>
                                <th>Teléfono</th>
                                <th>Estado</th>
                                <th>Fecha Registro</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaParticipantes">
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="sr-only">Cargando...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Historial de Cambios -->
        <div class="card mb-4">
            <div class="card-header bg-info">
                <h5 class="card-title mb-0 text-white">
                    <i class="fas fa-history mr-2"></i>Bitácora de Cambios
                </h5>
            </div>
            <div class="card-body">
                <div id="historialContainer">
                    <div class="text-center py-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    canvas {
        max-width: 100%;
    }

    .stat-card-custom {
        border-radius: 16px !important;
    }

    .badge-estado {
        padding: 0.4em 0.8em;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .badge-aprobada {
        background: #d4edda;
        color: #155724;
    }

    .badge-pendiente {
        background: #fff3cd;
        color: #856404;
    }

    .badge-rechazada {
        background: #f8d7da;
        color: #721c24;
    }
</style>
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="{{ asset('assets/js/config.js') }}"></script>
<script>
let megaEventoId = null;
let graficaInscripciones = null;
let graficaEstados = null;

// Obtener ID del mega evento de la URL
// La URL es: /ong/mega-eventos/{id}/seguimiento
const pathParts = window.location.pathname.split('/').filter(p => p !== '');
// Buscar el índice de 'mega-eventos' y tomar el siguiente elemento como ID
const megaEventosIndex = pathParts.indexOf('mega-eventos');
if (megaEventosIndex !== -1 && pathParts[megaEventosIndex + 1]) {
    megaEventoId = pathParts[megaEventosIndex + 1];
} else {
    // Fallback: intentar obtener el penúltimo elemento
    megaEventoId = pathParts[pathParts.length - 2];
}

// Cargar datos de seguimiento
async function cargarSeguimiento() {
    const token = localStorage.getItem('token');
    
    if (!token) {
        Swal.fire({
            icon: 'warning',
            title: 'Sesión Expirada',
            text: 'Tu sesión ha expirado. Por favor, inicia sesión nuevamente.',
            confirmButtonText: 'Ir al Login'
        }).then(() => {
            window.location.href = '/login';
        });
        return;
    }

    try {
        // Cargar estadísticas
        const resSeguimiento = await fetch(`${API_BASE_URL}/api/mega-eventos/${megaEventoId}/seguimiento`, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        const dataSeguimiento = await resSeguimiento.json();

        if (!resSeguimiento.ok || !dataSeguimiento.success) {
            throw new Error(dataSeguimiento.error || 'Error al cargar seguimiento');
        }

        // Actualizar información del mega evento
        const megaEvento = dataSeguimiento.mega_evento;
        document.getElementById('tituloMegaEvento').textContent = megaEvento.titulo;
        document.getElementById('volverLink').href = `/ong/mega-eventos/${megaEventoId}/detalle`;
        
        const fechaInicio = new Date(megaEvento.fecha_inicio).toLocaleDateString('es-BO', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        const fechaFin = new Date(megaEvento.fecha_fin).toLocaleDateString('es-BO', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        document.getElementById('fechasMegaEvento').textContent = `${fechaInicio} - ${fechaFin}`;

        // Actualizar métricas principales
        const stats = dataSeguimiento.estadisticas;
        document.getElementById('totalParticipantes').textContent = stats.total_participantes;
        document.getElementById('participantesAprobados').textContent = stats.participantes_aprobados;
        document.getElementById('tasaConfirmacion').textContent = stats.tasa_confirmacion + '%';
        
        // Actualizar métricas de interacción
        if (stats.interaccion_social) {
            document.getElementById('totalReacciones').textContent = stats.interaccion_social.total_reacciones || 0;
            document.getElementById('totalCompartidos').textContent = stats.interaccion_social.total_compartidos || 0;
        }
        
        // Actualizar métricas de participación
        document.getElementById('totalParticipaciones').textContent = stats.total_participantes || 0;
        const participantesRegistrados = stats.participantes_registrados || 0;
        const participantesNoRegistrados = stats.participantes_no_registrados || 0;
        document.getElementById('detalleParticipaciones').textContent = `${participantesRegistrados} registrados, ${participantesNoRegistrados} no registrados`;
        
        if (stats.porcentaje_capacidad !== null) {
            document.getElementById('porcentajeCapacidad').textContent = stats.porcentaje_capacidad + '%';
        } else {
            document.getElementById('porcentajeCapacidad').textContent = 'Sin límite';
        }

        // Actualizar seguimiento por tipo de actor
        document.getElementById('porcentajeCumplimientoOng').textContent = stats.porcentaje_cumplimiento_ong + '%';
        const barraCumplimiento = document.getElementById('barraCumplimientoOng');
        if (barraCumplimiento) {
            barraCumplimiento.style.width = stats.porcentaje_cumplimiento_ong + '%';
        }

        // Mostrar tareas de ONG
        if (stats.tareas_cumplidas_ong) {
            const tareasContainer = document.getElementById('tareasOngContainer');
            const tareas = stats.tareas_cumplidas_ong;
            tareasContainer.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <small><i class="fas ${tareas.evento_publicado ? 'fa-check-circle text-success' : 'fa-times-circle text-danger'} mr-2"></i>Evento publicado</small>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <small><i class="fas ${tareas.imagenes_cargadas ? 'fa-check-circle text-success' : 'fa-times-circle text-danger'} mr-2"></i>Imágenes cargadas</small>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <small><i class="fas ${tareas.fechas_definidas ? 'fa-check-circle text-success' : 'fa-times-circle text-danger'} mr-2"></i>Fechas definidas</small>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <small><i class="fas ${tareas.ubicacion_definida ? 'fa-check-circle text-success' : 'fa-times-circle text-danger'} mr-2"></i>Ubicación definida</small>
                </div>
            `;
        }

        // Mostrar alertas
        if (dataSeguimiento.alertas && dataSeguimiento.alertas.length > 0) {
            mostrarAlertas(dataSeguimiento.alertas);
        }

        // Crear gráficas
        crearGraficaReacciones(dataSeguimiento.reacciones_por_dia || []);
        crearGraficaEstados(stats);

        // Cargar participantes
        cargarParticipantes();

        // Cargar historial
        cargarHistorial();

        // Mostrar contenido
        document.getElementById('loadingMessage').style.display = 'none';
        document.getElementById('seguimientoContent').style.display = 'block';

    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message || 'Error al cargar el seguimiento del mega evento'
        });
    }
}

// Crear gráfica de reacciones por día
function crearGraficaReacciones(datos) {
    const ctx = document.getElementById('graficaInscripciones').getContext('2d');
    
    // Si no hay datos, crear un array vacío con los últimos 30 días
    if (!datos || datos.length === 0) {
        const ultimos30Dias = [];
        for (let i = 29; i >= 0; i--) {
            const fecha = new Date();
            fecha.setDate(fecha.getDate() - i);
            ultimos30Dias.push({
                fecha: fecha.toISOString().split('T')[0],
                cantidad: 0
            });
        }
        datos = ultimos30Dias;
    }
    
    const labels = datos.map(d => {
        const fecha = new Date(d.fecha);
        return fecha.toLocaleDateString('es-BO', { month: 'short', day: 'numeric' });
    });
    const valores = datos.map(d => d.cantidad || 0);

    if (graficaInscripciones) {
        graficaInscripciones.destroy();
    }

    graficaInscripciones = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Reacciones',
                data: valores,
                borderColor: '#e91e63',
                backgroundColor: 'rgba(233, 30, 99, 0.1)',
                tension: 0.4,
                fill: true,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: '#e91e63',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: '#e91e63',
                    borderWidth: 1
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

// Crear gráfica de estados
function crearGraficaEstados(stats) {
    const ctx = document.getElementById('graficaEstados').getContext('2d');

    if (graficaEstados) {
        graficaEstados.destroy();
    }

    const labels = ['Aprobados', 'Pendientes'];
    const data = [stats.participantes_aprobados, stats.participantes_pendientes];
    
    // Agregar cancelados si existen
    if (stats.participantes_cancelados > 0) {
        labels.push('Cancelados');
        data.push(stats.participantes_cancelados);
    }

    graficaEstados = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: ['#00A36C', '#FFC107', '#dc3545'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

// Mostrar alertas
function mostrarAlertas(alertas) {
    const panelAlertas = document.getElementById('panelAlertas');
    const container = document.getElementById('alertasContainer');
    
    if (!alertas || alertas.length === 0) {
        panelAlertas.style.display = 'none';
        return;
    }

    panelAlertas.style.display = 'block';
    
    const nivelColores = {
        'critica': { bg: '#f8d7da', border: '#dc3545', icon: 'fa-exclamation-triangle', text: '#721c24' },
        'advertencia': { bg: '#fff3cd', border: '#ffc107', icon: 'fa-exclamation-circle', text: '#856404' },
        'info': { bg: '#d1ecf1', border: '#17a2b8', icon: 'fa-info-circle', text: '#0c5460' }
    };

    container.innerHTML = alertas.map(alerta => {
        const color = nivelColores[alerta.nivel] || nivelColores.info;
        const fecha = new Date(alerta.fecha).toLocaleString('es-BO', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });

        return `
            <div class="alert mb-2 alert-${alerta.nivel === 'critica' ? 'danger' : alerta.nivel === 'advertencia' ? 'warning' : 'info'}" style="border-left: 4px solid ${color.border}; border-radius: 8px;">
                <div class="d-flex align-items-start">
                    <i class="fas ${color.icon} mr-2 mt-1"></i>
                    <div class="flex-grow-1">
                        <strong>${alerta.mensaje}</strong>
                        <br>
                        <small class="text-muted">${fecha}</small>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

// Cargar participantes
async function cargarParticipantes(estado = 'todos', buscar = '') {
    const token = localStorage.getItem('token');
    const tbody = document.getElementById('tablaParticipantes');

    tbody.innerHTML = '<tr><td colspan="6" class="text-center py-3"><div class="spinner-border text-primary" role="status"><span class="sr-only">Cargando...</span></div></td></tr>';

    try {
        const params = new URLSearchParams();
        if (estado !== 'todos') {
            params.append('estado', estado);
        }
        if (buscar.trim() !== '') {
            params.append('buscar', buscar.trim());
        }

        const url = `${API_BASE_URL}/api/mega-eventos/${megaEventoId}/participantes${params.toString() ? '?' + params.toString() : ''}`;
        
        const res = await fetch(url, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        const data = await res.json();

        if (!res.ok || !data.success) {
            throw new Error(data.error || 'Error al cargar participantes');
        }

        if (data.participantes.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-5 text-muted">No hay participantes registrados</td></tr>';
            return;
        }

        tbody.innerHTML = data.participantes.map(p => {
            const fechaRegistro = new Date(p.fecha_registro).toLocaleDateString('es-BO', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            const nombreCompleto = `${p.nombres || ''} ${p.apellidos || ''}`.trim() || p.nombre_usuario || 'Sin nombre';
            // El estado puede venir como 'estado' o 'estado_participacion' dependiendo del tipo
            const estado = p.estado || p.estado_participacion || 'pendiente';
            const estadoClass = estado === 'aprobada' ? 'badge-aprobada' : 
                               estado === 'rechazada' ? 'badge-rechazada' : 'badge-pendiente';
            const estadoTexto = estado === 'aprobada' ? 'Aprobada' :
                               estado === 'rechazada' ? 'Rechazada' : 'Pendiente';
            
            // Badge de tipo de participante
            const tipoBadge = p.tipo === 'registrado' 
                ? '<span class="badge badge-info mr-2" style="font-size: 0.7rem;">Registrado</span>'
                : '<span class="badge badge-warning mr-2" style="font-size: 0.7rem;">No registrado</span>';

            return `
                <tr>
                    <td style="padding: 1rem;">
                        <div class="d-flex align-items-center">
                            <div class="mr-2" style="width: 40px; height: 40px; border-radius: 50%; background: #F5F5F5; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                ${p.foto_perfil ? `<img src="${p.foto_perfil}" alt="${nombreCompleto}" style="width: 100%; height: 100%; object-fit: cover;">` : `<i class="fas fa-user text-muted"></i>`}
                            </div>
                            <div>
                                <strong class="text-dark">${nombreCompleto}</strong>
                                <br>${tipoBadge}
                            </div>
                        </div>
                    </td>
                    <td>${p.email || '-'}</td>
                    <td>${p.telefono || '-'}</td>
                    <td>
                        <span class="badge badge-estado ${estadoClass}">${estadoTexto}</span>
                    </td>
                    <td>${fechaRegistro}</td>
                    <td>
                        ${p.integrante_externo_id ? `
                            <button class="btn btn-sm btn-outline-primary" onclick="verHistorialParticipante(${p.integrante_externo_id})" title="Ver historial">
                                <i class="fas fa-history"></i>
                            </button>
                        ` : '<span class="text-muted">-</span>'}
                    </td>
                </tr>
            `;
        }).join('');

    } catch (error) {
        console.error('Error:', error);
        tbody.innerHTML = `<tr><td colspan="6" class="text-center py-5 text-danger">Error al cargar participantes: ${error.message}</td></tr>`;
    }
}

// Cargar historial
async function cargarHistorial() {
    const token = localStorage.getItem('token');
    const container = document.getElementById('historialContainer');

    try {
        const res = await fetch(`${API_BASE_URL}/api/mega-eventos/${megaEventoId}/historial`, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        const data = await res.json();

        if (!res.ok || !data.success) {
            throw new Error(data.error || 'Error al cargar historial');
        }

        if (data.historial.length === 0) {
            container.innerHTML = '<p class="text-muted mb-0">No hay historial disponible</p>';
            return;
        }

        if (data.historial.length === 0) {
            container.innerHTML = '<p class="text-muted mb-0">No hay historial disponible</p>';
            return;
        }

        const iconosPorTipo = {
            'creacion': 'fa-check',
            'estado': 'fa-clock',
            'publicacion': 'fa-calendar',
            'imagenes': 'fa-image',
            'actualizacion': 'fa-edit',
            'participacion': 'fa-user-plus'
        };

        const coloresPorTipo = {
            'creacion': '#00A36C',
            'estado': '#17a2b8',
            'publicacion': '#6f42c1',
            'imagenes': '#ffc107',
            'actualizacion': '#0C2B44',
            'participacion': '#00A36C'
        };

        container.innerHTML = data.historial.map(h => {
            const fecha = new Date(h.fecha).toLocaleDateString('es-BO', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            const icono = h.icono || iconosPorTipo[h.tipo] || 'fa-clock';
            const color = coloresPorTipo[h.tipo] || '#00A36C';

            return `
                <div class="d-flex align-items-start mb-3 pb-3 border-bottom">
                    <div class="mr-3" style="width: 48px; height: 48px; background: ${color}20; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="fas ${icono}" style="color: ${color}; font-size: 1.2rem;"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1 font-weight-bold text-dark">${h.accion}</h6>
                        <p class="mb-1 text-muted">${h.detalle}</p>
                        <small class="text-muted">${fecha} - ${h.usuario}</small>
                    </div>
                </div>
            `;
        }).join('');

    } catch (error) {
        console.error('Error:', error);
        container.innerHTML = `<p class="text-danger">Error al cargar historial: ${error.message}</p>`;
    }
}

// Ver historial de participante
function verHistorialParticipante(integranteId) {
    Swal.fire({
        icon: 'info',
        title: 'Historial de Participante',
        text: 'Esta funcionalidad se implementará próximamente',
        confirmButtonText: 'Cerrar'
    });
}

// Exportar reporte a Excel
document.getElementById('btnExportarReporte').addEventListener('click', function() {
    const token = localStorage.getItem('token');
    
    if (!token) {
        Swal.fire({
            icon: 'warning',
            title: 'Sesión Expirada',
            text: 'Tu sesión ha expirado. Por favor, inicia sesión nuevamente.',
            confirmButtonText: 'Ir al Login'
        }).then(() => {
            window.location.href = '/login';
        });
        return;
    }

    Swal.fire({
        title: 'Exportando reporte...',
        text: 'Por favor espera mientras se genera el archivo Excel',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Crear enlace de descarga
    const url = `${API_BASE_URL}/api/mega-eventos/${megaEventoId}/exportar-excel`;
    
    // Agregar token como header usando fetch
    fetch(url, {
        method: 'GET',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/vnd.ms-excel'
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(data.error || 'Error al exportar reporte');
            });
        }
        return response.blob();
    })
    .then(blob => {
        const urlBlob = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = urlBlob;
        link.download = `seguimiento-mega-evento-${megaEventoId}-${new Date().toISOString().split('T')[0]}.xls`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(urlBlob);
        
        Swal.fire({
            icon: 'success',
            title: '¡Exportación exitosa!',
            text: 'El reporte se ha descargado correctamente',
            confirmButtonText: 'Cerrar'
        });
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error al exportar',
            text: error.message || 'Ocurrió un error al generar el archivo Excel',
            confirmButtonText: 'Cerrar'
        });
    });
});

// Filtros
document.getElementById('filtroEstadoParticipante').addEventListener('change', function() {
    const estado = this.value;
    const buscar = document.getElementById('buscadorParticipantes').value;
    cargarParticipantes(estado, buscar);
});

document.getElementById('buscadorParticipantes').addEventListener('input', function() {
    const estado = document.getElementById('filtroEstadoParticipante').value;
    const buscar = this.value;
    cargarParticipantes(estado, buscar);
});

// Inicializar
document.addEventListener('DOMContentLoaded', function() {
    cargarSeguimiento();
});
</script>
@endpush


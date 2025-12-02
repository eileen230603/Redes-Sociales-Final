@extends('layouts.adminlte')

@section('page_title', 'Seguimiento de Mega Evento')

@section('content_body')
<div class="container-fluid">
    <!-- Loading State -->
    <div id="loadingMessage" class="text-center py-5">
        <div class="spinner-border" role="status" style="color: #00A36C; width: 3rem; height: 3rem;">
            <span class="sr-only">Cargando...</span>
        </div>
        <p class="mt-3" style="color: #333333; font-weight: 500;">Cargando información de seguimiento...</p>
    </div>

    <div id="seguimientoContent" style="display: none;">
        <!-- Header con información del mega evento -->
        <div class="card border-0 mb-4" style="border-radius: 12px; border: 1px solid #F5F5F5; box-shadow: 0 2px 8px rgba(12, 43, 68, 0.08);">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <h2 class="mb-2" id="tituloMegaEvento" style="font-size: 1.8rem; font-weight: 700; color: #0C2B44;">-</h2>
                        <p class="mb-0 text-muted" id="fechasMegaEvento">-</p>
                    </div>
                    <div>
                        <a href="#" id="volverLink" class="btn btn-sm mr-2" style="background: #F5F5F5; color: #0C2B44; border: none; border-radius: 8px; padding: 0.5rem 1rem;">
                            <i class="far fa-arrow-left mr-1"></i> Volver
                        </a>
                        <button id="btnExportarReporte" class="btn btn-sm" style="background: #00A36C; color: white; border: none; border-radius: 8px; padding: 0.5rem 1rem;">
                            <i class="far fa-file-export mr-1"></i> Exportar Reporte
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel de Métricas Principales -->
        <div class="row mb-4">
            <!-- Total Participantes -->
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card bg-gradient-primary" style="border: none; border-radius: 12px;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white text-uppercase mb-3" style="font-size: 0.8rem; opacity: .95; font-weight: 600; letter-spacing: 0.5px;">Total Participantes</h6>
                                <h2 class="text-white mb-0" id="totalParticipantes" style="font-size: 3rem; font-weight: 700; line-height: 1;">0</h2>
                            </div>
                            <i class="far fa-users fa-3x text-white" style="opacity: .2;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Participantes Aprobados -->
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card bg-gradient-success" style="border: none; border-radius: 12px;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white text-uppercase mb-3" style="font-size: 0.8rem; opacity: .95; font-weight: 600; letter-spacing: 0.5px;">Aprobados</h6>
                                <h2 class="text-white mb-0" id="participantesAprobados" style="font-size: 3rem; font-weight: 700; line-height: 1;">0</h2>
                            </div>
                            <i class="far fa-check-circle fa-3x text-white" style="opacity: .2;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tasa de Confirmación -->
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card bg-gradient-primary-accent" style="border: none; border-radius: 12px;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white text-uppercase mb-3" style="font-size: 0.8rem; opacity: .95; font-weight: 600; letter-spacing: 0.5px;">Tasa Confirmación</h6>
                                <h2 class="text-white mb-0" id="tasaConfirmacion" style="font-size: 3rem; font-weight: 700; line-height: 1;">0%</h2>
                            </div>
                            <i class="far fa-percentage fa-3x text-white" style="opacity: .2;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Capacidad -->
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card bg-gradient-warning" style="border: none; border-radius: 12px;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white text-uppercase mb-3" style="font-size: 0.8rem; opacity: .95; font-weight: 600; letter-spacing: 0.5px;">Capacidad</h6>
                                <h2 class="text-white mb-0" id="porcentajeCapacidad" style="font-size: 3rem; font-weight: 700; line-height: 1;">-</h2>
                            </div>
                            <i class="far fa-chart-pie fa-3x text-white" style="opacity: .2;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel de Alertas -->
        <div id="panelAlertas" class="card shadow-sm mb-4" style="border: none; border-radius: 12px; display: none;">
            <div class="card-header bg-white" style="border-bottom: 1px solid #f0f0f0; padding: 1.25rem;">
                <h5 class="mb-0" style="font-weight: 700; color: #0C2B44;">
                    <i class="far fa-bell mr-2" style="color: #FFC107;"></i>Alertas y Notificaciones
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
                <div class="card shadow-sm" style="border: none; border-radius: 12px; border-left: 4px solid #e91e63;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted text-uppercase mb-2" style="font-size: 0.75rem; font-weight: 600;">Total Reacciones</h6>
                                <h3 class="mb-0" id="totalReacciones" style="font-size: 2rem; font-weight: 700; color: #e91e63;">0</h3>
                                <small class="text-muted">Me gusta recibidos</small>
                            </div>
                            <i class="far fa-heart fa-2x" style="color: #e91e63; opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Compartidos -->
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="card shadow-sm" style="border: none; border-radius: 12px; border-left: 4px solid #ff9800;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted text-uppercase mb-2" style="font-size: 0.75rem; font-weight: 600;">Total Compartidos</h6>
                                <h3 class="mb-0" id="totalCompartidos" style="font-size: 2rem; font-weight: 700; color: #ff9800;">0</h3>
                                <small class="text-muted">Veces compartido</small>
                            </div>
                            <i class="far fa-share-alt fa-2x" style="color: #ff9800; opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Participaciones -->
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="card shadow-sm" style="border: none; border-radius: 12px; border-left: 4px solid #4caf50;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted text-uppercase mb-2" style="font-size: 0.75rem; font-weight: 600;">Total Participaciones</h6>
                                <h3 class="mb-0" id="totalParticipaciones" style="font-size: 2rem; font-weight: 700; color: #4caf50;">0</h3>
                                <small class="text-muted" id="detalleParticipaciones">0 registrados, 0 no registrados</small>
                            </div>
                            <i class="far fa-user-check fa-2x" style="color: #4caf50; opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Seguimiento por Tipo de Actor -->
        <div class="row mb-4">
            <!-- ONG Organizadora -->
            <div class="col-lg-12 col-md-12 mb-3">
                <div class="card shadow-sm" style="border: none; border-radius: 12px;">
                    <div class="card-header bg-white" style="border-bottom: 1px solid #f0f0f0; padding: 1rem 1.25rem;">
                        <h6 class="mb-0" style="font-weight: 600; color: #333;">
                            <i class="far fa-flag mr-2" style="color: #00A36C;"></i>ONG Organizadora
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Cumplimiento de tareas</span>
                                <strong id="porcentajeCumplimientoOng" style="color: #00A36C;">0%</strong>
                            </div>
                            <div class="progress" style="height: 8px; border-radius: 4px;">
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
                <div class="card shadow-sm" style="border: none; border-radius: 16px; background: #ffffff;">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center" style="border-bottom: 1px solid #f0f0f0; padding: 1rem 1.25rem; border-radius: 16px 16px 0 0;">
                        <div>
                            <h6 class="mb-0" style="font-weight: 600; color: #333; font-size: 0.9rem;">
                                <i class="far fa-heart mr-2" style="color: #e91e63; font-size: 0.85rem;"></i>Reacciones por Día
                            </h6>
                            <small class="text-muted" style="font-size: 0.75rem;">Últimos 30 días</small>
                        </div>
                    </div>
                    <div class="card-body" style="padding: 1.5rem;">
                        <div style="height: 300px; position: relative;">
                            <canvas id="graficaInscripciones"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráfica de Estado de Participantes -->
            <div class="col-lg-6 col-md-12 mb-3">
                <div class="card shadow-sm" style="border: none; border-radius: 16px; background: #ffffff;">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center" style="border-bottom: 1px solid #f0f0f0; padding: 1rem 1.25rem; border-radius: 16px 16px 0 0;">
                        <div>
                            <h6 class="mb-0" style="font-weight: 600; color: #333; font-size: 0.9rem;">
                                <i class="far fa-chart-pie mr-2" style="color: #00A36C; font-size: 0.85rem;"></i>Estado de Participantes
                            </h6>
                            <small class="text-muted" style="font-size: 0.75rem;">Distribución actual</small>
                        </div>
                    </div>
                    <div class="card-body" style="padding: 1.5rem;">
                        <div style="height: 300px; position: relative;">
                            <canvas id="graficaEstados"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Participantes -->
        <div class="card shadow-sm mb-4" style="border: none; border-radius: 12px;">
            <div class="card-header bg-white" style="border-bottom: 1px solid #f0f0f0; padding: 1.25rem;">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <h5 class="mb-0" style="font-weight: 700; color: #0C2B44;">
                        <i class="far fa-users mr-2" style="color: #00A36C;"></i>Seguimiento de Inscripciones
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
                        <thead style="background: #F5F5F5;">
                            <tr>
                                <th style="padding: 1rem; font-weight: 600; color: #0C2B44; border-bottom: 2px solid #e0e0e0;">Participante</th>
                                <th style="padding: 1rem; font-weight: 600; color: #0C2B44; border-bottom: 2px solid #e0e0e0;">Email</th>
                                <th style="padding: 1rem; font-weight: 600; color: #0C2B44; border-bottom: 2px solid #e0e0e0;">Teléfono</th>
                                <th style="padding: 1rem; font-weight: 600; color: #0C2B44; border-bottom: 2px solid #e0e0e0;">Estado</th>
                                <th style="padding: 1rem; font-weight: 600; color: #0C2B44; border-bottom: 2px solid #e0e0e0;">Fecha Registro</th>
                                <th style="padding: 1rem; font-weight: 600; color: #0C2B44; border-bottom: 2px solid #e0e0e0;">Acciones</th>
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
        <div class="card shadow-sm mb-4" style="border: none; border-radius: 12px;">
            <div class="card-header bg-white" style="border-bottom: 1px solid #f0f0f0; padding: 1.25rem;">
                <h5 class="mb-0" style="font-weight: 700; color: #0C2B44;">
                    <i class="far fa-history mr-2" style="color: #00A36C;"></i>Bitácora de Cambios
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
    .card {
        transition: all 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08) !important;
    }

    canvas {
        max-width: 100%;
    }

    .badge-estado {
        padding: 0.4em 0.8em;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .badge-aprobada {
        background: #e8f8f2;
        color: #00A36C;
    }

    .badge-pendiente {
        background: #fff3cd;
        color: #856404;
    }

    .badge-rechazada {
        background: #f8d7da;
        color: #721c24;
    }

    .alerta-critica {
        background: #f8d7da;
        border-left: 4px solid #dc3545;
        color: #721c24;
    }

    .alerta-advertencia {
        background: #fff3cd;
        border-left: 4px solid #ffc107;
        color: #856404;
    }

    .alerta-info {
        background: #d1ecf1;
        border-left: 4px solid #17a2b8;
        color: #0c5460;
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
                    <small><i class="far ${tareas.evento_publicado ? 'fa-check-circle text-success' : 'fa-times-circle text-danger'} mr-2"></i>Evento publicado</small>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <small><i class="far ${tareas.imagenes_cargadas ? 'fa-check-circle text-success' : 'fa-times-circle text-danger'} mr-2"></i>Imágenes cargadas</small>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <small><i class="far ${tareas.fechas_definidas ? 'fa-check-circle text-success' : 'fa-times-circle text-danger'} mr-2"></i>Fechas definidas</small>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <small><i class="far ${tareas.ubicacion_definida ? 'fa-check-circle text-success' : 'fa-times-circle text-danger'} mr-2"></i>Ubicación definida</small>
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
            <div class="alert mb-2" style="background: ${color.bg}; border-left: 4px solid ${color.border}; color: ${color.text}; border-radius: 8px; padding: 1rem;">
                <div class="d-flex align-items-start">
                    <i class="far ${color.icon} mr-2 mt-1" style="font-size: 1.2rem;"></i>
                    <div class="flex-grow-1">
                        <strong>${alerta.mensaje}</strong>
                        <br>
                        <small style="opacity: 0.8;">${fecha}</small>
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
                                ${p.foto_perfil ? `<img src="${p.foto_perfil}" alt="${nombreCompleto}" style="width: 100%; height: 100%; object-fit: cover;">` : `<i class="far fa-user text-muted"></i>`}
                            </div>
                            <div>
                                <strong style="color: #0C2B44;">${nombreCompleto}</strong>
                                <br>${tipoBadge}
                            </div>
                        </div>
                    </td>
                    <td style="padding: 1rem; color: #333;">${p.email || '-'}</td>
                    <td style="padding: 1rem; color: #333;">${p.telefono || '-'}</td>
                    <td style="padding: 1rem;">
                        <span class="badge badge-estado ${estadoClass}">${estadoTexto}</span>
                    </td>
                    <td style="padding: 1rem; color: #333;">${fechaRegistro}</td>
                    <td style="padding: 1rem;">
                        ${p.integrante_externo_id ? `
                            <button class="btn btn-sm btn-outline-primary" onclick="verHistorialParticipante(${p.integrante_externo_id})" title="Ver historial">
                                <i class="far fa-history"></i>
                            </button>
                        ` : '<span class="text-muted" style="font-size: 0.85rem;">-</span>'}
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
                <div class="d-flex align-items-start mb-3 pb-3" style="border-bottom: 1px solid #f0f0f0;">
                    <div class="mr-3" style="width: 48px; height: 48px; background: ${color}20; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="far ${icono}" style="color: ${color}; font-size: 1.2rem;"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1" style="color: #0C2B44; font-weight: 600; font-size: 0.95rem;">${h.accion}</h6>
                        <p class="mb-1 text-muted" style="font-size: 0.85rem; line-height: 1.4;">${h.detalle}</p>
                        <small class="text-muted" style="font-size: 0.75rem;">${fecha} - ${h.usuario}</small>
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


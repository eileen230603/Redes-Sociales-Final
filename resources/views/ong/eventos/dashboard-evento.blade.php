@extends('layouts.adminlte')

@section('page_title', 'Dashboard del Evento')

@section('content_body')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0" style="color: #0C2B44; font-weight: 700;">
                <i class="far fa-chart-bar mr-2" style="color: #00A36C;"></i>
                Dashboard del Evento
            </h2>
            <p class="text-muted mb-0" id="eventoTitulo">Cargando información del evento...</p>
        </div>
        <div class="d-flex gap-2">
            <button id="btnDescargarPDF" class="btn btn-success" onclick="descargarPDF()" style="font-weight: 600;">
                <i class="far fa-file-pdf mr-2"></i> Descargar PDF
            </button>
            <a href="#" id="btnVolver" class="btn btn-outline-primary">
                <i class="far fa-arrow-left mr-2"></i> Volver al Detalle
            </a>
        </div>
    </div>

    <!-- Tarjetas de Estadísticas -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-gradient-primary shadow-sm" style="border: none; border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white text-uppercase mb-2" style="font-size: 0.8rem; opacity: 0.9; font-weight: 600;">Reacciones</h6>
                            <h2 class="text-white mb-0" id="totalReacciones" style="font-size: 2.5rem; font-weight: 700;">0</h2>
                        </div>
                        <i class="far fa-heart fa-3x text-white" style="opacity: 0.2;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-gradient-success shadow-sm" style="border: none; border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white text-uppercase mb-2" style="font-size: 0.8rem; opacity: 0.9; font-weight: 600;">Compartidos</h6>
                            <h2 class="text-white mb-0" id="totalCompartidos" style="font-size: 2.5rem; font-weight: 700;">0</h2>
                        </div>
                        <i class="far fa-share-alt fa-3x text-white" style="opacity: 0.2;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-gradient-info shadow-sm" style="border: none; border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white text-uppercase mb-2" style="font-size: 0.8rem; opacity: 0.9; font-weight: 600;">Voluntarios</h6>
                            <h2 class="text-white mb-0" id="totalVoluntarios" style="font-size: 2.5rem; font-weight: 700;">0</h2>
                        </div>
                        <i class="far fa-users fa-3x text-white" style="opacity: 0.2;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-gradient-warning shadow-sm" style="border: none; border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white text-uppercase mb-2" style="font-size: 0.8rem; opacity: 0.9; font-weight: 600;">Participantes</h6>
                            <h2 class="text-white mb-0" id="totalParticipantes" style="font-size: 2.5rem; font-weight: 700;">0</h2>
                        </div>
                        <i class="far fa-user-check fa-3x text-white" style="opacity: 0.2;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficas -->
    <div class="row mb-4">
        <!-- Gráfica de Reacciones por Día -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm" style="border-radius: 12px; border: none;">
                <div class="card-header" style="background: white; border-bottom: 1px solid #F5F5F5; border-radius: 12px 12px 0 0;">
                    <h5 class="mb-0" style="color: #0C2B44; font-weight: 700;">
                        <i class="far fa-heart mr-2" style="color: #dc3545;"></i> Reacciones por Día
                    </h5>
                </div>
                <div class="card-body" style="padding: 1.5rem;">
                    <div style="height: 300px; position: relative;">
                        <canvas id="graficaReacciones"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfica de Participantes por Estado -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm" style="border-radius: 12px; border: none;">
                <div class="card-header" style="background: white; border-bottom: 1px solid #F5F5F5; border-radius: 12px 12px 0 0;">
                    <h5 class="mb-0" style="color: #0C2B44; font-weight: 700;">
                        <i class="far fa-users mr-2" style="color: #00A36C;"></i> Participantes por Estado
                    </h5>
                </div>
                <div class="card-body" style="padding: 1.5rem;">
                    <div style="height: 300px; position: relative;">
                        <canvas id="graficaParticipantes"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfica de Compartidos por Día -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm" style="border-radius: 12px; border: none;">
                <div class="card-header" style="background: white; border-bottom: 1px solid #F5F5F5; border-radius: 12px 12px 0 0;">
                    <h5 class="mb-0" style="color: #0C2B44; font-weight: 700;">
                        <i class="far fa-share-alt mr-2" style="color: #00A36C;"></i> Compartidos por Día
                    </h5>
                </div>
                <div class="card-body" style="padding: 1.5rem;">
                    <div style="height: 300px; position: relative;">
                        <canvas id="graficaCompartidos"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfica de Inscripciones por Día -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm" style="border-radius: 12px; border: none;">
                <div class="card-header" style="background: white; border-bottom: 1px solid #F5F5F5; border-radius: 12px 12px 0 0;">
                    <h5 class="mb-0" style="color: #0C2B44; font-weight: 700;">
                        <i class="far fa-calendar-check mr-2" style="color: #00A36C;"></i> Inscripciones por Día
                    </h5>
                </div>
                <div class="card-body" style="padding: 1.5rem;">
                    <div style="height: 300px; position: relative;">
                        <canvas id="graficaInscripciones"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfica de Comparación Reacciones vs Compartidos -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm" style="border-radius: 12px; border: none;">
                <div class="card-header" style="background: white; border-bottom: 1px solid #F5F5F5; border-radius: 12px 12px 0 0;">
                    <h5 class="mb-0" style="color: #0C2B44; font-weight: 700;">
                        <i class="far fa-chart-line mr-2" style="color: #00A36C;"></i> Reacciones vs Compartidos
                    </h5>
                </div>
                <div class="card-body" style="padding: 1.5rem;">
                    <div style="height: 300px; position: relative;">
                        <canvas id="graficaComparacion"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfica de Barras Horizontales - Top Participantes -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm" style="border-radius: 12px; border: none;">
                <div class="card-header" style="background: white; border-bottom: 1px solid #F5F5F5; border-radius: 12px 12px 0 0;">
                    <h5 class="mb-0" style="color: #0C2B44; font-weight: 700;">
                        <i class="far fa-chart-bar mr-2" style="color: #00A36C;"></i> Actividad por Semana
                    </h5>
                </div>
                <div class="card-body" style="padding: 1.5rem;">
                    <div style="height: 300px; position: relative;">
                        <canvas id="graficaActividadSemanal"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfica de Área - Tendencias -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm" style="border-radius: 12px; border: none;">
                <div class="card-header" style="background: white; border-bottom: 1px solid #F5F5F5; border-radius: 12px 12px 0 0;">
                    <h5 class="mb-0" style="color: #0C2B44; font-weight: 700;">
                        <i class="far fa-chart-area mr-2" style="color: #00A36C;"></i> Tendencias de Participación
                    </h5>
                </div>
                <div class="card-body" style="padding: 1.5rem;">
                    <div style="height: 300px; position: relative;">
                        <canvas id="graficaTendencias"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfica de Radar - Métricas Generales -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm" style="border-radius: 12px; border: none;">
                <div class="card-header" style="background: white; border-bottom: 1px solid #F5F5F5; border-radius: 12px 12px 0 0;">
                    <h5 class="mb-0" style="color: #0C2B44; font-weight: 700;">
                        <i class="far fa-chart-pie mr-2" style="color: #00A36C;"></i> Métricas Generales
                    </h5>
                </div>
                <div class="card-body" style="padding: 1.5rem;">
                    <div style="height: 300px; position: relative;">
                        <canvas id="graficaRadar"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Resumen -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm" style="border-radius: 12px; border: none;">
                <div class="card-header" style="background: white; border-bottom: 1px solid #F5F5F5; border-radius: 12px 12px 0 0;">
                    <h5 class="mb-0" style="color: #0C2B44; font-weight: 700;">
                        <i class="far fa-list-alt mr-2" style="color: #00A36C;"></i> Resumen Detallado
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead style="background: #F5F5F5;">
                                <tr>
                                    <th style="color: #0C2B44; font-weight: 600;">Métrica</th>
                                    <th style="color: #0C2B44; font-weight: 600;" class="text-right">Valor</th>
                                </tr>
                            </thead>
                            <tbody id="tablaResumen">
                                <tr>
                                    <td colspan="2" class="text-center">
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
        </div>
    </div>
</div>
@stop

@section('js')
@parent
<script src="{{ asset('js/notificaciones-ong.js') }}"></script>
<script src="{{ asset('assets/js/config.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
// Extraer el ID del evento de la URL: /ong/eventos/{id}/dashboard
const pathParts = window.location.pathname.split("/").filter(part => part !== '');
let eventoId = null;

// Buscar el índice de "eventos" y tomar el siguiente elemento como ID
const eventosIndex = pathParts.indexOf('eventos');
if (eventosIndex !== -1 && pathParts[eventosIndex + 1]) {
    eventoId = pathParts[eventosIndex + 1];
}

// Validar que el ID sea numérico
if (!eventoId || isNaN(eventoId)) {
    console.error('No se pudo extraer un ID válido del evento');
    alert('Error: No se pudo identificar el evento. Por favor, vuelve a la lista de eventos.');
    window.location.href = '/ong/eventos';
}

const token = localStorage.getItem('token');

console.log('Evento ID extraído:', eventoId);
console.log('Path completo:', window.location.pathname);

let chartReacciones = null;
let chartParticipantes = null;
let chartCompartidos = null;
let chartInscripciones = null;
let chartComparacion = null;
let chartActividadSemanal = null;
let chartTendencias = null;
let chartRadar = null;

// Configurar botón volver
document.getElementById('btnVolver').href = `/ong/eventos/${eventoId}/detalle`;

async function cargarDashboard() {
    try {
        // Verificar que API_BASE_URL esté definido
        if (typeof API_BASE_URL === 'undefined' || !API_BASE_URL) {
            throw new Error('API_BASE_URL no está definido');
        }

        // Verificar que el token esté disponible
        if (!token) {
            throw new Error('No hay token de autenticación');
        }

        const res = await fetch(`${API_BASE_URL}/api/eventos/${eventoId}/dashboard`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        // Verificar si la respuesta es JSON
        const contentType = res.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await res.text();
            console.error('Respuesta no es JSON:', text.substring(0, 200));
            throw new Error('El servidor no devolvió una respuesta válida');
        }

        const data = await res.json();

        if (!res.ok) {
            throw new Error(data.error || data.message || `Error ${res.status}: ${res.statusText}`);
        }

        if (!data.success) {
            throw new Error(data.error || 'Error al cargar datos');
        }

        // Actualizar título
        document.getElementById('eventoTitulo').textContent = data.evento?.titulo || 'Evento';

        // Actualizar tarjetas
        document.getElementById('totalReacciones').textContent = data.estadisticas?.reacciones || 0;
        document.getElementById('totalCompartidos').textContent = data.estadisticas?.compartidos || 0;
        document.getElementById('totalVoluntarios').textContent = data.estadisticas?.voluntarios || 0;
        document.getElementById('totalParticipantes').textContent = data.estadisticas?.participantes || 0;

        // Crear gráficas
        crearGraficas(data);

        // Actualizar tabla de resumen
        actualizarTablaResumen(data.estadisticas);

    } catch (error) {
        console.error('Error completo:', error);
        console.error('Evento ID:', eventoId);
        console.error('API_BASE_URL:', typeof API_BASE_URL !== 'undefined' ? API_BASE_URL : 'NO DEFINIDO');
        console.error('Token:', token ? 'Presente' : 'Ausente');
        
        let mensajeError = 'Error al cargar el dashboard. ';
        if (error.message) {
            mensajeError += error.message;
        } else {
            mensajeError += 'Error desconocido. Por favor, verifica la consola para más detalles.';
        }
        
        alert(mensajeError);
        
        // Mostrar mensaje en la página también
        document.getElementById('eventoTitulo').textContent = 'Error al cargar datos';
        document.getElementById('eventoTitulo').style.color = '#dc3545';
    }
}

function crearGraficas(data) {
    // Gráfica de Reacciones
    const ctxReacciones = document.getElementById('graficaReacciones');
    if (ctxReacciones && data.graficas?.reacciones_por_dia) {
        if (chartReacciones) chartReacciones.destroy();
        
        const reaccionesData = data.graficas.reacciones_por_dia;
        chartReacciones = new Chart(ctxReacciones, {
            type: 'line',
            data: {
                labels: Object.keys(reaccionesData),
                datasets: [{
                    label: 'Reacciones',
                    data: Object.values(reaccionesData),
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    // Gráfica de Participantes por Estado
    const ctxParticipantes = document.getElementById('graficaParticipantes');
    if (ctxParticipantes && data.graficas?.participantes_por_estado) {
        if (chartParticipantes) chartParticipantes.destroy();
        
        const participantesData = data.graficas.participantes_por_estado;
        chartParticipantes = new Chart(ctxParticipantes, {
            type: 'doughnut',
            data: {
                labels: Object.keys(participantesData),
                datasets: [{
                    data: Object.values(participantesData),
                    backgroundColor: ['#00A36C', '#0C2B44', '#17a2b8', '#ffc107', '#dc3545']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    // Gráfica de Compartidos
    const ctxCompartidos = document.getElementById('graficaCompartidos');
    if (ctxCompartidos && data.graficas?.compartidos_por_dia) {
        if (chartCompartidos) chartCompartidos.destroy();
        
        const compartidosData = data.graficas.compartidos_por_dia;
        chartCompartidos = new Chart(ctxCompartidos, {
            type: 'bar',
            data: {
                labels: Object.keys(compartidosData),
                datasets: [{
                    label: 'Compartidos',
                    data: Object.values(compartidosData),
                    backgroundColor: '#00A36C',
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    // Gráfica de Inscripciones
    const ctxInscripciones = document.getElementById('graficaInscripciones');
    if (ctxInscripciones && data.graficas?.inscripciones_por_dia) {
        if (chartInscripciones) chartInscripciones.destroy();
        
        const inscripcionesData = data.graficas.inscripciones_por_dia;
        chartInscripciones = new Chart(ctxInscripciones, {
            type: 'line',
            data: {
                labels: Object.keys(inscripcionesData),
                datasets: [{
                    label: 'Inscripciones',
                    data: Object.values(inscripcionesData),
                    borderColor: '#17a2b8',
                    backgroundColor: 'rgba(23, 162, 184, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    // Gráfica de Comparación Reacciones vs Compartidos
    const ctxComparacion = document.getElementById('graficaComparacion');
    if (ctxComparacion && data.graficas?.reacciones_por_dia && data.graficas?.compartidos_por_dia) {
        if (chartComparacion) chartComparacion.destroy();
        
        const reaccionesData = data.graficas.reacciones_por_dia;
        const compartidosData = data.graficas.compartidos_por_dia;
        
        // Obtener todas las fechas únicas
        const todasFechas = [...new Set([...Object.keys(reaccionesData), ...Object.keys(compartidosData)])].sort();
        
        chartComparacion = new Chart(ctxComparacion, {
            type: 'bar',
            data: {
                labels: todasFechas,
                datasets: [
                    {
                        label: 'Reacciones',
                        data: todasFechas.map(fecha => reaccionesData[fecha] || 0),
                        backgroundColor: 'rgba(220, 53, 69, 0.7)',
                        borderColor: '#dc3545',
                        borderWidth: 1
                    },
                    {
                        label: 'Compartidos',
                        data: todasFechas.map(fecha => compartidosData[fecha] || 0),
                        backgroundColor: 'rgba(0, 163, 108, 0.7)',
                        borderColor: '#00A36C',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    // Gráfica de Actividad Semanal
    const ctxActividadSemanal = document.getElementById('graficaActividadSemanal');
    if (ctxActividadSemanal && data.graficas?.actividad_semanal) {
        if (chartActividadSemanal) chartActividadSemanal.destroy();
        
        const actividadData = data.graficas.actividad_semanal;
        chartActividadSemanal = new Chart(ctxActividadSemanal, {
            type: 'bar',
            data: {
                labels: Object.keys(actividadData),
                datasets: [{
                    label: 'Actividad',
                    data: Object.values(actividadData),
                    backgroundColor: 'rgba(12, 43, 68, 0.8)',
                    borderColor: '#0C2B44',
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { beginAtZero: true }
                }
            }
        });
    }

    // Gráfica de Tendencias (Área)
    const ctxTendencias = document.getElementById('graficaTendencias');
    if (ctxTendencias && data.graficas?.inscripciones_por_dia) {
        if (chartTendencias) chartTendencias.destroy();
        
        const inscripcionesData = data.graficas.inscripciones_por_dia;
        chartTendencias = new Chart(ctxTendencias, {
            type: 'line',
            data: {
                labels: Object.keys(inscripcionesData),
                datasets: [{
                    label: 'Tendencia de Participación',
                    data: Object.values(inscripcionesData),
                    borderColor: '#00A36C',
                    backgroundColor: 'rgba(0, 163, 108, 0.3)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.5,
                    pointRadius: 5,
                    pointBackgroundColor: '#00A36C'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    // Gráfica Radar - Métricas Generales
    const ctxRadar = document.getElementById('graficaRadar');
    if (ctxRadar && data.estadisticas) {
        if (chartRadar) chartRadar.destroy();
        
        const stats = data.estadisticas;
        // Normalizar valores para el radar (escala 0-100)
        const maxValor = Math.max(
            stats.reacciones || 0,
            stats.compartidos || 0,
            stats.voluntarios || 0,
            stats.participantes || 0
        ) || 1;
        
        chartRadar = new Chart(ctxRadar, {
            type: 'radar',
            data: {
                labels: ['Reacciones', 'Compartidos', 'Voluntarios', 'Participantes'],
                datasets: [{
                    label: 'Métricas',
                    data: [
                        ((stats.reacciones || 0) / maxValor) * 100,
                        ((stats.compartidos || 0) / maxValor) * 100,
                        ((stats.voluntarios || 0) / maxValor) * 100,
                        ((stats.participantes || 0) / maxValor) * 100
                    ],
                    backgroundColor: 'rgba(0, 163, 108, 0.2)',
                    borderColor: '#00A36C',
                    borderWidth: 2,
                    pointBackgroundColor: '#00A36C',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: '#00A36C'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    r: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            display: false
                        }
                    }
                }
            }
        });
    }
}

function actualizarTablaResumen(estadisticas) {
    const tbody = document.getElementById('tablaResumen');
    if (!estadisticas) return;

    tbody.innerHTML = `
        <tr>
            <td><strong>Total de Reacciones</strong></td>
            <td class="text-right"><span class="badge badge-danger" style="font-size: 1rem; padding: 0.5em 1em;">${estadisticas.reacciones || 0}</span></td>
        </tr>
        <tr>
            <td><strong>Total de Compartidos</strong></td>
            <td class="text-right"><span class="badge badge-success" style="font-size: 1rem; padding: 0.5em 1em;">${estadisticas.compartidos || 0}</span></td>
        </tr>
        <tr>
            <td><strong>Total de Voluntarios</strong></td>
            <td class="text-right"><span class="badge badge-info" style="font-size: 1rem; padding: 0.5em 1em;">${estadisticas.voluntarios || 0}</span></td>
        </tr>
        <tr>
            <td><strong>Total de Participantes</strong></td>
            <td class="text-right"><span class="badge badge-warning" style="font-size: 1rem; padding: 0.5em 1em;">${estadisticas.participantes || 0}</span></td>
        </tr>
        <tr>
            <td><strong>Participantes Aprobados</strong></td>
            <td class="text-right"><span class="badge badge-success" style="font-size: 1rem; padding: 0.5em 1em;">${estadisticas.participantes_aprobados || 0}</span></td>
        </tr>
        <tr>
            <td><strong>Participantes Pendientes</strong></td>
            <td class="text-right"><span class="badge badge-warning" style="font-size: 1rem; padding: 0.5em 1em;">${estadisticas.participantes_pendientes || 0}</span></td>
        </tr>
    `;
}

// Función para descargar PDF
async function descargarPDF() {
    try {
        const btnPDF = document.getElementById('btnDescargarPDF');
        if (btnPDF) {
            btnPDF.disabled = true;
            btnPDF.innerHTML = '<i class="far fa-spinner fa-spin mr-2"></i> Generando PDF...';
        }

        const res = await fetch(`${API_BASE_URL}/api/eventos/${eventoId}/dashboard/pdf`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if (!res.ok) {
            throw new Error('Error al generar PDF');
        }

        // Obtener el blob del PDF
        const blob = await res.blob();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `dashboard-evento-${eventoId}-${new Date().toISOString().split('T')[0]}.pdf`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);

        if (btnPDF) {
            btnPDF.disabled = false;
            btnPDF.innerHTML = '<i class="far fa-file-pdf mr-2"></i> Descargar PDF';
        }
    } catch (error) {
        console.error('Error al descargar PDF:', error);
        alert('Error al generar el PDF: ' + error.message);
        
        const btnPDF = document.getElementById('btnDescargarPDF');
        if (btnPDF) {
            btnPDF.disabled = false;
            btnPDF.innerHTML = '<i class="far fa-file-pdf mr-2"></i> Descargar PDF';
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    cargarDashboard();
});
</script>
@stop


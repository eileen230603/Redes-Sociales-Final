@extends('layouts.adminlte')

@section('page_title', 'Dashboard del Evento')

@section('content_body')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-4" style="border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
        <div class="card-header" style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%); border-bottom: 3px solid #00A36C; border-radius: 12px 12px 0 0;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="card-title mb-0" style="color: #0C2B44; font-weight: 700; font-size: 1.75rem;">
                        <i class="fas fa-chart-bar mr-2" style="color: #00A36C;"></i> Dashboard del Evento
                    </h3>
                    <p class="mb-0 mt-2" id="eventoTitulo" style="color: #6c757d; font-size: 0.95rem;">Cargando información del evento...</p>
                </div>
                <div class="d-flex" style="gap: 0.5rem;">
                    <button id="btnDescargarPDF" class="btn btn-sm" onclick="descargarPDF()" style="background: #dc3545; border: none; color: #ffffff; border-radius: 6px; font-weight: 600;">
                        <i class="fas fa-file-pdf mr-2"></i> Descargar PDF
                    </button>
                    <button id="btnDescargarExcel" class="btn btn-sm" onclick="descargarExcel()" style="background: #00A36C; border: none; color: #ffffff; border-radius: 6px; font-weight: 600;">
                        <i class="fas fa-file-excel mr-2"></i> Descargar Excel
                    </button>
                    <a href="#" id="btnVolver" class="btn btn-sm btn-light" style="border: 1px solid #dee2e6; border-radius: 6px; color: #0C2B44; font-weight: 600;">
                        <i class="fas fa-arrow-left mr-2"></i> Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjetas de Estadísticas -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card metric-card" style="border-left: 4px solid #dc3545; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 id="totalReacciones" style="color: #0C2B44; font-weight: 700; font-size: 2.5rem; margin: 0;">0</h3>
                            <p style="color: #6c757d; font-size: 0.95rem; margin: 0.5rem 0 0 0; font-weight: 600;">Reacciones</p>
                        </div>
                        <div style="color: #dc3545; opacity: 0.2; font-size: 3rem;">
                            <i class="fas fa-heart"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card metric-card" style="border-left: 4px solid #00A36C; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 id="totalCompartidos" style="color: #0C2B44; font-weight: 700; font-size: 2.5rem; margin: 0;">0</h3>
                            <p style="color: #6c757d; font-size: 0.95rem; margin: 0.5rem 0 0 0; font-weight: 600;">Compartidos</p>
                        </div>
                        <div style="color: #00A36C; opacity: 0.2; font-size: 3rem;">
                            <i class="fas fa-share-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card metric-card" style="border-left: 4px solid #17a2b8; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 id="totalVoluntarios" style="color: #0C2B44; font-weight: 700; font-size: 2.5rem; margin: 0;">0</h3>
                            <p style="color: #6c757d; font-size: 0.95rem; margin: 0.5rem 0 0 0; font-weight: 600;">Voluntarios</p>
                        </div>
                        <div style="color: #17a2b8; opacity: 0.2; font-size: 3rem;">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card metric-card" style="border-left: 4px solid #ffc107; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 id="totalParticipantes" style="color: #0C2B44; font-weight: 700; font-size: 2.5rem; margin: 0;">0</h3>
                            <p style="color: #6c757d; font-size: 0.95rem; margin: 0.5rem 0 0 0; font-weight: 600;">Participantes</p>
                        </div>
                        <div style="color: #ffc107; opacity: 0.2; font-size: 3rem;">
                            <i class="fas fa-user-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficas -->
    <div class="row mb-4">
        <!-- Gráfica de Reacciones por Día -->
        <div class="col-lg-6 mb-4">
            <div class="card" style="border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-radius: 12px;">
                <div class="card-header" style="background: #ffffff; border-bottom: 2px solid #dc3545; border-radius: 12px 12px 0 0;">
                    <h5 class="card-title mb-0" style="color: #0C2B44; font-weight: 700;">
                        <i class="fas fa-heart mr-2" style="color: #dc3545;"></i> Reacciones por Día
                    </h5>
                </div>
                <div class="card-body">
                    <div style="height: 300px; position: relative;">
                        <canvas id="graficaReacciones"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfica de Participantes por Estado -->
        <div class="col-lg-6 mb-4">
            <div class="card" style="border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-radius: 12px;">
                <div class="card-header" style="background: #ffffff; border-bottom: 2px solid #00A36C; border-radius: 12px 12px 0 0;">
                    <h5 class="card-title mb-0" style="color: #0C2B44; font-weight: 700;">
                        <i class="fas fa-users mr-2" style="color: #00A36C;"></i> Participantes por Estado
                    </h5>
                </div>
                <div class="card-body">
                    <div style="height: 300px; position: relative;">
                        <canvas id="graficaParticipantes"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfica de Compartidos por Día -->
        <div class="col-lg-6 mb-4">
            <div class="card" style="border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-radius: 12px;">
                <div class="card-header" style="background: #ffffff; border-bottom: 2px solid #17a2b8; border-radius: 12px 12px 0 0;">
                    <h5 class="card-title mb-0" style="color: #0C2B44; font-weight: 700;">
                        <i class="fas fa-share-alt mr-2" style="color: #17a2b8;"></i> Compartidos por Día
                    </h5>
                </div>
                <div class="card-body">
                    <div style="height: 300px; position: relative;">
                        <canvas id="graficaCompartidos"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfica de Inscripciones por Día -->
        <div class="col-lg-6 mb-4">
            <div class="card" style="border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-radius: 12px;">
                <div class="card-header" style="background: #ffffff; border-bottom: 2px solid #ffc107; border-radius: 12px 12px 0 0;">
                    <h5 class="card-title mb-0" style="color: #0C2B44; font-weight: 700;">
                        <i class="fas fa-calendar-check mr-2" style="color: #ffc107;"></i> Inscripciones por Día
                    </h5>
                </div>
                <div class="card-body">
                    <div style="height: 300px; position: relative;">
                        <canvas id="graficaInscripciones"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfica de Comparación Reacciones vs Compartidos -->
        <div class="col-lg-6 mb-4">
            <div class="card" style="border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-radius: 12px;">
                <div class="card-header" style="background: #ffffff; border-bottom: 2px solid #0C2B44; border-radius: 12px 12px 0 0;">
                    <h5 class="card-title mb-0" style="color: #0C2B44; font-weight: 700;">
                        <i class="fas fa-chart-line mr-2" style="color: #0C2B44;"></i> Reacciones vs Compartidos
                    </h5>
                </div>
                <div class="card-body">
                    <div style="height: 300px; position: relative;">
                        <canvas id="graficaComparacion"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfica de Actividad por Semana -->
        <div class="col-lg-6 mb-4">
            <div class="card" style="border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-radius: 12px;">
                <div class="card-header" style="background: #ffffff; border-bottom: 2px solid #6c757d; border-radius: 12px 12px 0 0;">
                    <h5 class="card-title mb-0" style="color: #0C2B44; font-weight: 700;">
                        <i class="fas fa-chart-bar mr-2" style="color: #6c757d;"></i> Actividad por Semana
                    </h5>
                </div>
                <div class="card-body">
                    <div style="height: 300px; position: relative;">
                        <canvas id="graficaActividadSemanal"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfica de Tendencias -->
        <div class="col-lg-6 mb-4">
            <div class="card" style="border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-radius: 12px;">
                <div class="card-header" style="background: #ffffff; border-bottom: 2px solid #00A36C; border-radius: 12px 12px 0 0;">
                    <h5 class="card-title mb-0" style="color: #0C2B44; font-weight: 700;">
                        <i class="fas fa-chart-area mr-2" style="color: #00A36C;"></i> Tendencias de Participación
                    </h5>
                </div>
                <div class="card-body">
                    <div style="height: 300px; position: relative;">
                        <canvas id="graficaTendencias"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfica de Radar - Métricas Generales -->
        <div class="col-lg-6 mb-4">
            <div class="card" style="border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-radius: 12px;">
                <div class="card-header" style="background: #ffffff; border-bottom: 2px solid #17a2b8; border-radius: 12px 12px 0 0;">
                    <h5 class="card-title mb-0" style="color: #0C2B44; font-weight: 700;">
                        <i class="fas fa-chart-pie mr-2" style="color: #17a2b8;"></i> Métricas Generales
                    </h5>
                </div>
                <div class="card-body">
                    <div style="height: 300px; position: relative;">
                        <canvas id="graficaRadar"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros de Fecha -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card" style="border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-radius: 12px;">
                <div class="card-header" style="background: #ffffff; border-bottom: 2px solid #17a2b8; border-radius: 12px 12px 0 0;">
                    <h5 class="card-title mb-0" style="color: #0C2B44; font-weight: 700;">
                        <i class="fas fa-filter mr-2" style="color: #17a2b8;"></i> Filtros de Fecha
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="fechaInicio">Fecha Inicio:</label>
                            <input type="date" id="fechaInicio" class="form-control" />
                        </div>
                        <div class="col-md-4">
                            <label for="fechaFin">Fecha Fin:</label>
                            <input type="date" id="fechaFin" class="form-control" />
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button class="btn btn-primary w-100" onclick="aplicarFiltros()">
                                <i class="fas fa-search mr-2"></i> Aplicar Filtros
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Actividad Reciente (Últimos 10 días) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card" style="border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-radius: 12px;">
                <div class="card-header" style="background: #ffffff; border-bottom: 2px solid #0C2B44; border-radius: 12px 12px 0 0;">
                    <h5 class="card-title mb-0" style="color: #0C2B44; font-weight: 700;">
                        <i class="fas fa-calendar-day mr-2" style="color: #0C2B44;"></i> Actividad de los Últimos 10 Días
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="bg-light">
                                <tr>
                                    <th>Fecha</th>
                                    <th class="text-center">Reacciones</th>
                                    <th class="text-center">Compartidos</th>
                                    <th class="text-center">Inscripciones</th>
                                    <th class="text-center">Total</th>
                                </tr>
                            </thead>
                            <tbody id="tablaActividadReciente">
                                <tr>
                                    <td colspan="5" class="text-center">
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

    <!-- Tabla Top 10 Participantes -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card" style="border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-radius: 12px;">
                <div class="card-header" style="background: #ffffff; border-bottom: 2px solid #ffc107; border-radius: 12px 12px 0 0;">
                    <h5 class="card-title mb-0" style="color: #0C2B44; font-weight: 700;">
                        <i class="fas fa-trophy mr-2" style="color: #ffc107;"></i> Top 10 Participantes Más Activos
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="bg-light">
                                <tr>
                                    <th>#</th>
                                    <th>Nombre</th>
                                    <th class="text-center">Total Actividades</th>
                                </tr>
                            </thead>
                            <tbody id="tablaTopParticipantes">
                                <tr>
                                    <td colspan="3" class="text-center">
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

    <!-- Tabla de Resumen -->
    <div class="row">
        <div class="col-12">
            <div class="card" style="border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-radius: 12px;">
                <div class="card-header" style="background: #ffffff; border-bottom: 2px solid #0C2B44; border-radius: 12px 12px 0 0;">
                    <h5 class="card-title mb-0" style="color: #0C2B44; font-weight: 700;">
                        <i class="fas fa-list-alt mr-2" style="color: #0C2B44;"></i> Resumen Detallado
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th class="font-weight-bold text-dark">Métrica</th>
                                    <th class="text-right font-weight-bold text-dark">Valor</th>
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

@push('css')
<style>
    .metric-card {
        transition: all 0.3s ease;
        border: none !important;
    }

    .metric-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12) !important;
    }

    .card {
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1) !important;
    }

    .card-header {
        border-bottom: none;
    }

    .card-body {
        padding: 1.5rem;
    }

    canvas {
        max-width: 100%;
    }

    .table thead th {
        background-color: #f8f9fa;
        color: #0C2B44;
        font-weight: 700;
        border-bottom: 2px solid #dee2e6;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .btn {
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    label {
        color: #0C2B44;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .form-control {
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 0.5rem 0.75rem;
    }

    .form-control:focus {
        border-color: #00A36C;
        box-shadow: 0 0 0 0.2rem rgba(0, 163, 108, 0.25);
    }
</style>
@endpush

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

        // Obtener filtros de fecha
        const fechaInicio = document.getElementById('fechaInicio')?.value || '';
        const fechaFin = document.getElementById('fechaFin')?.value || '';
        
        let url = `${API_BASE_URL}/api/eventos/${eventoId}/dashboard-completo`;
        if (fechaInicio || fechaFin) {
            const params = new URLSearchParams();
            if (fechaInicio) params.append('fecha_inicio', fechaInicio);
            if (fechaFin) params.append('fecha_fin', fechaFin);
            url += '?' + params.toString();
        }

        const res = await fetch(url, {
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
        document.getElementById('totalReacciones').textContent = data.metricas?.reacciones || 0;
        document.getElementById('totalCompartidos').textContent = data.metricas?.compartidos || 0;
        document.getElementById('totalVoluntarios').textContent = data.metricas?.voluntarios || 0;
        document.getElementById('totalParticipantes').textContent = data.metricas?.participantes_total || 0;

        // Crear gráficas con nuevos datos
        crearGraficasMejoradas(data);

        // Actualizar tablas
        actualizarTablaResumen(data.metricas);
        actualizarTablaActividadReciente(data.actividad_reciente);
        actualizarTablaTopParticipantes(data.top_participantes);
        
        // Mostrar comparativas
        mostrarComparativas(data.comparativas);

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
                    backgroundColor: ['#00A36C', '#0C2B44', '#17a2b8', '#ffc107', '#dc3545', '#6c757d']
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
                    borderColor: '#00A36C',
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
                    backgroundColor: 'rgba(108, 117, 125, 0.8)',
                    borderColor: '#6c757d',
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
                    pointBackgroundColor: '#00A36C',
                    pointBorderColor: '#00A36C',
                    pointHoverBackgroundColor: '#0C2B44',
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
                    pointBorderColor: '#00A36C',
                    pointHoverBackgroundColor: '#0C2B44',
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
            btnPDF.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Generando PDF...';
        }

        // Obtener filtros de fecha
        const fechaInicio = document.getElementById('fechaInicio')?.value || '';
        const fechaFin = document.getElementById('fechaFin')?.value || '';
        
        let url = `${API_BASE_URL}/api/eventos/${eventoId}/dashboard-completo/pdf`;
        if (fechaInicio || fechaFin) {
            const params = new URLSearchParams();
            if (fechaInicio) params.append('fecha_inicio', fechaInicio);
            if (fechaFin) params.append('fecha_fin', fechaFin);
            url += '?' + params.toString();
        }

        const res = await fetch(url, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/pdf'
            }
        });

        if (!res.ok) {
            const errorData = await res.json().catch(() => ({}));
            throw new Error(errorData.error || 'Error al generar PDF');
        }

        // Obtener el blob del PDF
        const blob = await res.blob();
        const urlBlob = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = urlBlob;
        a.download = `dashboard-evento-${eventoId}-${new Date().toISOString().split('T')[0]}.pdf`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(urlBlob);
        document.body.removeChild(a);

        if (btnPDF) {
            btnPDF.disabled = false;
            btnPDF.innerHTML = '<i class="fas fa-file-pdf mr-2"></i> Descargar PDF';
        }
        
        // Mostrar notificación de éxito
        mostrarNotificacion('PDF generado exitosamente', 'success');
    } catch (error) {
        console.error('Error al descargar PDF:', error);
        mostrarNotificacion('Error al generar el PDF: ' + error.message, 'error');
        
        const btnPDF = document.getElementById('btnDescargarPDF');
        if (btnPDF) {
            btnPDF.disabled = false;
            btnPDF.innerHTML = '<i class="fas fa-file-pdf mr-2"></i> Descargar PDF';
        }
    }
}

// Función para descargar Excel
async function descargarExcel() {
    try {
        const btnExcel = document.getElementById('btnDescargarExcel');
        if (btnExcel) {
            btnExcel.disabled = true;
            btnExcel.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Generando Excel...';
        }

        // Obtener filtros de fecha
        const fechaInicio = document.getElementById('fechaInicio')?.value || '';
        const fechaFin = document.getElementById('fechaFin')?.value || '';
        
        let url = `${API_BASE_URL}/api/eventos/${eventoId}/dashboard-completo/excel`;
        if (fechaInicio || fechaFin) {
            const params = new URLSearchParams();
            if (fechaInicio) params.append('fecha_inicio', fechaInicio);
            if (fechaFin) params.append('fecha_fin', fechaFin);
            url += '?' + params.toString();
        }

        const res = await fetch(url, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            }
        });

        if (!res.ok) {
            const errorData = await res.json().catch(() => ({}));
            throw new Error(errorData.error || 'Error al generar Excel');
        }

        // Obtener el blob del Excel
        const blob = await res.blob();
        const urlBlob = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = urlBlob;
        a.download = `dashboard-evento-${eventoId}-${new Date().toISOString().split('T')[0]}.xlsx`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(urlBlob);
        document.body.removeChild(a);

        if (btnExcel) {
            btnExcel.disabled = false;
            btnExcel.innerHTML = '<i class="fas fa-file-excel mr-2"></i> Descargar Excel';
        }
        
        // Mostrar notificación de éxito
        mostrarNotificacion('Excel generado exitosamente', 'success');
    } catch (error) {
        console.error('Error al descargar Excel:', error);
        mostrarNotificacion('Error al generar el Excel: ' + error.message, 'error');
        
        const btnExcel = document.getElementById('btnDescargarExcel');
        if (btnExcel) {
            btnExcel.disabled = false;
            btnExcel.innerHTML = '<i class="fas fa-file-excel mr-2"></i> Descargar Excel';
        }
    }
}

// Función para aplicar filtros
function aplicarFiltros() {
    cargarDashboard();
}

// Función para mostrar notificaciones
function mostrarNotificacion(mensaje, tipo) {
    const alertClass = tipo === 'success' ? 'alert-success' : 'alert-danger';
    const icon = tipo === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
    
    const alert = document.createElement('div');
    alert.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
    alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alert.innerHTML = `
        <i class="fas ${icon} mr-2"></i> ${mensaje}
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    `;
    
    document.body.appendChild(alert);
    
    setTimeout(() => {
        alert.remove();
    }, 5000);
}

// Funciones para actualizar tablas
function actualizarTablaActividadReciente(actividad) {
    const tbody = document.getElementById('tablaActividadReciente');
    if (!actividad || Object.keys(actividad).length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center">No hay datos disponibles</td></tr>';
        return;
    }

    let html = '';
    let totalReacciones = 0, totalCompartidos = 0, totalInscripciones = 0, totalGeneral = 0;
    
    Object.entries(actividad).forEach(([fecha, datos]) => {
        const fechaFormateada = new Date(fecha).toLocaleDateString('es-ES', { 
            day: '2-digit', 
            month: '2-digit', 
            year: 'numeric' 
        });
        
        totalReacciones += datos.reacciones || 0;
        totalCompartidos += datos.compartidos || 0;
        totalInscripciones += datos.inscripciones || 0;
        totalGeneral += datos.total || 0;
        
        html += `
            <tr>
                <td><strong>${fechaFormateada}</strong></td>
                <td class="text-center">${datos.reacciones || 0}</td>
                <td class="text-center">${datos.compartidos || 0}</td>
                <td class="text-center">${datos.inscripciones || 0}</td>
                <td class="text-center"><strong>${datos.total || 0}</strong></td>
            </tr>
        `;
    });
    
    html += `
        <tr class="bg-light font-weight-bold">
            <td><strong>TOTAL</strong></td>
            <td class="text-center">${totalReacciones}</td>
            <td class="text-center">${totalCompartidos}</td>
            <td class="text-center">${totalInscripciones}</td>
            <td class="text-center">${totalGeneral}</td>
        </tr>
    `;
    
    tbody.innerHTML = html;
}

function actualizarTablaTopParticipantes(participantes) {
    const tbody = document.getElementById('tablaTopParticipantes');
    if (!participantes || participantes.length === 0) {
        tbody.innerHTML = '<tr><td colspan="3" class="text-center">No hay datos disponibles</td></tr>';
        return;
    }

    let html = '';
    participantes.slice(0, 10).forEach((participante, index) => {
        html += `
            <tr>
                <td class="text-center"><strong>${index + 1}</strong></td>
                <td>${participante.nombre || 'Participante'}</td>
                <td class="text-center"><span class="badge badge-warning" style="font-size: 1rem; padding: 0.5em 1em;">${participante.total_actividades || 0}</span></td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

function mostrarComparativas(comparativas) {
    // Esta función puede mostrar badges de crecimiento en las tarjetas
    if (!comparativas) return;
    
    // Agregar badges de crecimiento a las tarjetas si es necesario
    Object.entries(comparativas).forEach(([metrica, datos]) => {
        const crecimiento = datos.crecimiento || 0;
        const tendencia = datos.tendencia || 'stable';
        
        // Aquí puedes agregar indicadores visuales de crecimiento
        // Por ejemplo, agregar un badge a las tarjetas de métricas
    });
}

// Función mejorada para crear gráficas con nuevos datos
function crearGraficasMejoradas(data) {
    // Reutilizar la función crearGraficas pero adaptada a la nueva estructura
    const datosAdaptados = {
        estadisticas: data.metricas,
        graficas: {
            reacciones_por_dia: data.tendencias?.reacciones_por_dia || {},
            compartidos_por_dia: data.tendencias?.compartidos_por_dia || {},
            participantes_por_estado: data.distribucion_estados || {},
            inscripciones_por_dia: data.tendencias?.inscripciones_por_dia || {},
            actividad_semanal: data.actividad_semanal || {}
        }
    };
    
    crearGraficas(datosAdaptados);
}

document.addEventListener('DOMContentLoaded', () => {
    cargarDashboard();
});
</script>
@stop


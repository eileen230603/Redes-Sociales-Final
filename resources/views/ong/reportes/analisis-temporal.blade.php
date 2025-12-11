@extends('layouts.adminlte')

@section('page_title', 'Reporte: Análisis Temporal de Mega Eventos')

@section('content_body')
<div class="container-fluid">
    <!-- Header -->
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 16px; overflow: hidden; background: linear-gradient(135deg, #00A36C 0%, #008a5a 100%);">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div class="mb-3 mb-md-0">
                    <h2 class="mb-2 text-white" style="font-weight: 600; font-size: 1.75rem;">
                        <i class="fas fa-chart-line mr-2"></i>Análisis Temporal de Mega Eventos
                    </h2>
                    <p class="mb-0 text-white-50" style="font-size: 0.95rem;">Gráfico de líneas de eventos creados por mes con comparativa año anterior</p>
                </div>
                <div>
                    <a href="{{ route('ong.reportes.index') }}" class="btn btn-light btn-sm px-3 mb-2 mb-md-0 d-block d-md-inline-block" style="border-radius: 8px;">
                        <i class="fas fa-arrow-left mr-2"></i>Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
        <div class="card-header bg-light" style="border-radius: 12px 12px 0 0;">
            <h5 class="mb-0" style="color: #0C2B44; font-weight: 600;">
                <i class="fas fa-filter mr-2"></i>Filtros Avanzados
            </h5>
        </div>
        <div class="card-body">
            <form id="filtrosForm" method="GET" action="{{ route('ong.reportes.analisis-temporal') }}">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="fecha_inicio" class="form-label" style="font-weight: 600; color: #495057;">Fecha Inicio</label>
                        <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                               value="{{ request('fecha_inicio') }}" style="border-radius: 8px;">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="fecha_fin" class="form-label" style="font-weight: 600; color: #495057;">Fecha Fin</label>
                        <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" 
                               value="{{ request('fecha_fin') }}" style="border-radius: 8px;">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="categoria" class="form-label" style="font-weight: 600; color: #495057;">Categoría</label>
                        <select class="form-control" id="categoria" name="categoria" style="border-radius: 8px;">
                            <option value="">Todas</option>
                            <option value="social" {{ request('categoria') == 'social' ? 'selected' : '' }}>Social</option>
                            <option value="educativo" {{ request('categoria') == 'educativo' ? 'selected' : '' }}>Educativo</option>
                            <option value="ambiental" {{ request('categoria') == 'ambiental' ? 'selected' : '' }}>Ambiental</option>
                            <option value="salud" {{ request('categoria') == 'salud' ? 'selected' : '' }}>Salud</option>
                            <option value="cultural" {{ request('categoria') == 'cultural' ? 'selected' : '' }}>Cultural</option>
                            <option value="deportivo" {{ request('categoria') == 'deportivo' ? 'selected' : '' }}>Deportivo</option>
                            <option value="benefico" {{ request('categoria') == 'benefico' ? 'selected' : '' }}>Benéfico</option>
                            <option value="otro" {{ request('categoria') == 'otro' ? 'selected' : '' }}>Otro</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="estado" class="form-label" style="font-weight: 600; color: #495057;">Estado</label>
                        <select class="form-control" id="estado" name="estado" style="border-radius: 8px;">
                            <option value="">Todos</option>
                            <option value="planificacion" {{ request('estado') == 'planificacion' ? 'selected' : '' }}>Planificación</option>
                            <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activo</option>
                            <option value="en_curso" {{ request('estado') == 'en_curso' ? 'selected' : '' }}>En Curso</option>
                            <option value="finalizado" {{ request('estado') == 'finalizado' ? 'selected' : '' }}>Finalizado</option>
                            <option value="cancelado" {{ request('estado') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-block" style="border-radius: 8px;">
                            <i class="fas fa-search mr-2"></i>Filtrar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Botones de Exportación -->
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2">
                <button id="btnExportarPDF" class="btn btn-danger" style="border-radius: 8px;">
                    <i class="fas fa-file-pdf mr-2"></i>Exportar PDF
                </button>
                <button id="btnExportarExcel" class="btn btn-success" style="border-radius: 8px;">
                    <i class="fas fa-file-excel mr-2"></i>Exportar Excel
                </button>
                <button id="btnExportarCSV" class="btn btn-info" style="border-radius: 8px;">
                    <i class="fas fa-file-csv mr-2"></i>Exportar CSV
                </button>
            </div>
        </div>
    </div>

    <!-- Métricas Principales -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0" style="border-radius: 12px; background: linear-gradient(135deg, #0C2B44 0%, #1a4a6b 100%);">
                <div class="card-body p-4 text-center">
                    <h3 class="text-white mb-2" style="font-size: 2.5rem; font-weight: 700;" id="totalActual">0</h3>
                    <p class="mb-0 text-white-50" style="font-size: 0.9rem;">Total Período Actual</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0" style="border-radius: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body p-4 text-center">
                    <h3 class="text-white mb-2" style="font-size: 2.5rem; font-weight: 700;" id="totalAnterior">0</h3>
                    <p class="mb-0 text-white-50" style="font-size: 0.9rem;">Total Año Anterior</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0" style="border-radius: 12px; background: linear-gradient(135deg, #00A36C 0%, #008a5a 100%);">
                <div class="card-body p-4 text-center">
                    <h3 class="text-white mb-2" style="font-size: 2.5rem; font-weight: 700;" id="promedioMensual">0</h3>
                    <p class="mb-0 text-white-50" style="font-size: 0.9rem;">Promedio Mensual</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0" style="border-radius: 12px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="card-body p-4 text-center">
                    <h3 class="text-white mb-2" style="font-size: 2.5rem; font-weight: 700;" id="crecimientoTotal">0%</h3>
                    <p class="mb-0 text-white-50" style="font-size: 0.9rem;">Crecimiento Total</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de Líneas -->
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
        <div class="card-header bg-light" style="border-radius: 12px 12px 0 0;">
            <h5 class="mb-0" style="color: #0C2B44; font-weight: 600;">
                <i class="fas fa-chart-line mr-2"></i>Tendencias Temporales - Comparativa Año Actual vs Año Anterior
            </h5>
        </div>
        <div class="card-body p-4">
            <canvas id="graficoLineas" height="80"></canvas>
        </div>
    </div>

    <!-- Tabla de Datos -->
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
        <div class="card-header bg-light" style="border-radius: 12px 12px 0 0;">
            <h5 class="mb-0" style="color: #0C2B44; font-weight: 600;">
                <i class="fas fa-table mr-2"></i>Datos Detallados por Mes
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="tablaTendencias">
                    <thead style="background: #f8f9fa;">
                        <tr>
                            <th style="font-weight: 600; color: #0C2B44; border-top: none;">Mes</th>
                            <th style="font-weight: 600; color: #0C2B44; border-top: none;" class="text-center">Año Actual</th>
                            <th style="font-weight: 600; color: #0C2B44; border-top: none;" class="text-center">Año Anterior</th>
                            <th style="font-weight: 600; color: #0C2B44; border-top: none;" class="text-center">Crecimiento</th>
                        </tr>
                    </thead>
                    <tbody id="tablaTendenciasBody">
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fas fa-spinner fa-spin mr-2"></i>Cargando datos...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="{{ asset('assets/js/config.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let chartLineas = null;

document.addEventListener('DOMContentLoaded', async function() {
    const token = localStorage.getItem('token');
    const tipoUsuario = localStorage.getItem('tipo_usuario');
    const ongId = parseInt(localStorage.getItem('id_entidad') || localStorage.getItem('id_usuario'), 10);

    // Validar autenticación
    if (!token || tipoUsuario !== 'ONG' || isNaN(ongId) || ongId <= 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Acceso denegado',
            text: 'Debes iniciar sesión como ONG para acceder a los reportes.',
            confirmButtonText: 'Ir al Login'
        }).then(() => {
            window.location.href = '/login';
        });
        return;
    }

    // Construir parámetros de filtros desde la URL
    const urlParams = new URLSearchParams(window.location.search);
    const filtrosParams = {};
    if (urlParams.get('fecha_inicio')) filtrosParams.fecha_inicio = urlParams.get('fecha_inicio');
    if (urlParams.get('fecha_fin')) filtrosParams.fecha_fin = urlParams.get('fecha_fin');
    if (urlParams.get('categoria')) filtrosParams.categoria = urlParams.get('categoria');
    if (urlParams.get('estado')) filtrosParams.estado = urlParams.get('estado');

    try {
        // Cargar datos desde la API
        const queryString = new URLSearchParams(filtrosParams).toString();
        const response = await fetch(`${API_BASE_URL}/api/reportes-ong/analisis-temporal?${queryString}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error('Error al cargar datos del reporte');
        }

        const result = await response.json();
        
        if (result.success && result.datos) {
            const datos = result.datos;
            
            // Actualizar métricas
            updateMetricas(datos);
            
            // Crear gráfico
            createChart(datos);
            
            // Actualizar tabla
            updateTabla(datos);
        } else {
            throw new Error(result.error || 'Error al cargar datos');
        }

        // Configurar botones de exportación
        setupExportButtons(ongId, filtrosParams);

    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudieron cargar los datos del reporte. ' + error.message
        });
    }
});

function updateMetricas(datos) {
    document.getElementById('totalActual').textContent = datos.total_actual || 0;
    document.getElementById('totalAnterior').textContent = datos.total_anterior || 0;
    document.getElementById('promedioMensual').textContent = (datos.promedio_mensual || 0).toFixed(2);
    
    const crecimiento = datos.crecimiento_total || 0;
    const crecimientoElement = document.getElementById('crecimientoTotal');
    crecimientoElement.textContent = (crecimiento >= 0 ? '+' : '') + crecimiento + '%';
    crecimientoElement.style.color = crecimiento >= 0 ? '#fff' : '#fff';
}

function createChart(datos) {
    const ctx = document.getElementById('graficoLineas').getContext('2d');
    
    const labels = datos.meses || [];
    const datosActual = datos.tendencias?.map(t => t.cantidad_actual) || [];
    const datosAnterior = datos.tendencias?.map(t => t.cantidad_anterior) || [];

    if (chartLineas) {
        chartLineas.destroy();
    }

    chartLineas = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Año Actual',
                    data: datosActual,
                    borderColor: 'rgb(0, 163, 108)',
                    backgroundColor: 'rgba(0, 163, 108, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: 'rgb(0, 163, 108)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                },
                {
                    label: 'Año Anterior',
                    data: datosAnterior,
                    borderColor: 'rgb(102, 126, 234)',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: 'rgb(102, 126, 234)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    borderDash: [5, 5]
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        font: {
                            size: 14,
                            weight: '600'
                        },
                        padding: 20,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14,
                        weight: '600'
                    },
                    bodyFont: {
                        size: 13
                    },
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        font: {
                            size: 12
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    ticks: {
                        font: {
                            size: 12
                        }
                    },
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

function updateTabla(datos) {
    const tbody = document.getElementById('tablaTendenciasBody');
    tbody.innerHTML = '';

    if (!datos.tendencias || datos.tendencias.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="4" class="text-center py-5 text-muted">
                    No hay datos disponibles para el período seleccionado
                </td>
            </tr>
        `;
        return;
    }

    datos.tendencias.forEach(tendencia => {
        const crecimiento = tendencia.crecimiento_porcentual || 0;
        const crecimientoClass = crecimiento >= 0 ? 'text-success' : 'text-danger';
        const crecimientoIcon = crecimiento >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
        const crecimientoSign = crecimiento >= 0 ? '+' : '';

        const row = document.createElement('tr');
        row.innerHTML = `
            <td style="font-weight: 600; color: #495057;">${tendencia.mes}</td>
            <td class="text-center" style="font-weight: 600; color: #0C2B44;">${tendencia.cantidad_actual}</td>
            <td class="text-center" style="font-weight: 600; color: #667eea;">${tendencia.cantidad_anterior}</td>
            <td class="text-center ${crecimientoClass}" style="font-weight: 600;">
                <i class="fas ${crecimientoIcon} mr-1"></i>${crecimientoSign}${crecimiento}%
            </td>
        `;
        tbody.appendChild(row);
    });
}

function setupExportButtons(ongId, filtros) {
    const token = localStorage.getItem('token');
    
    // Botón exportar PDF
    document.getElementById('btnExportarPDF').addEventListener('click', function() {
        const params = new URLSearchParams(filtros);
        const url = `${API_BASE_URL}/api/reportes-ong/analisis-temporal/exportar/pdf?${params.toString()}&token=${token}`;
        window.open(url, '_blank');
    });

    // Botón exportar Excel
    document.getElementById('btnExportarExcel').addEventListener('click', function() {
        const params = new URLSearchParams(filtros);
        const url = `${API_BASE_URL}/api/reportes-ong/analisis-temporal/exportar/excel?${params.toString()}&token=${token}`;
        window.open(url, '_blank');
    });

    // Botón exportar CSV
    document.getElementById('btnExportarCSV').addEventListener('click', function() {
        const params = new URLSearchParams(filtros);
        const url = `${API_BASE_URL}/api/reportes-ong/analisis-temporal/exportar/csv?${params.toString()}&token=${token}`;
        window.open(url, '_blank');
    });
}
</script>
@endpush

@push('css')
<style>
    body {
        background-color: #f8f9fa;
    }
    
    .card {
        transition: all 0.3s ease;
    }
    
    @media (max-width: 768px) {
        .card-body {
            padding: 1.5rem !important;
        }
    }
</style>
@endpush


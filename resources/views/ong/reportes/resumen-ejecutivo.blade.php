@extends('layouts.adminlte')

@section('page_title', 'Resumen Ejecutivo')

@section('content_body')
<div class="container-fluid" style="max-width: 1400px; margin: 0 auto; padding: 40px 20px;">
    <!-- Header Minimalista -->
    <div class="mb-5">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px;">
            <div>
                <h1 style="font-size: 2rem; font-weight: 600; color: #0C2B44; margin-bottom: 8px; letter-spacing: -0.5px;">
                    Resumen Ejecutivo
                </h1>
                <p style="color: #6C757D; font-size: 1rem; margin: 0;">Análisis consolidado de eventos regulares y mega eventos</p>
            </div>
            <a href="{{ route('ong.reportes.index') }}" style="display: inline-flex; align-items: center; padding: 10px 20px; background: #FFFFFF; border: 1px solid #E9ECEF; border-radius: 12px; color: #0C2B44; text-decoration: none; font-weight: 500; transition: all 0.3s ease;">
                <i class="fas fa-arrow-left mr-2"></i>Volver
            </a>
        </div>
    </div>

    <!-- Filtros Minimalistas -->
    <div style="background: #FFFFFF; border: 1px solid #E9ECEF; border-radius: 16px; padding: 24px; margin-bottom: 24px;">
        <form id="filtrosForm" method="GET" action="{{ route('ong.reportes.resumen-ejecutivo') }}">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #0C2B44; font-weight: 500; font-size: 0.875rem;">Fecha Inicio</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" value="{{ request('fecha_inicio') }}" 
                           style="width: 100%; padding: 10px 14px; border: 1px solid #E9ECEF; border-radius: 8px; font-size: 0.938rem; color: #0C2B44;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #0C2B44; font-weight: 500; font-size: 0.875rem;">Fecha Fin</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" value="{{ request('fecha_fin') }}" 
                           style="width: 100%; padding: 10px 14px; border: 1px solid #E9ECEF; border-radius: 8px; font-size: 0.938rem; color: #0C2B44;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #0C2B44; font-weight: 500; font-size: 0.875rem;">Categoría</label>
                    <select id="categoria" name="categoria" style="width: 100%; padding: 10px 14px; border: 1px solid #E9ECEF; border-radius: 8px; font-size: 0.938rem; color: #0C2B44; background: #FFFFFF;">
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
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #0C2B44; font-weight: 500; font-size: 0.875rem;">Estado</label>
                    <select id="estado" name="estado" style="width: 100%; padding: 10px 14px; border: 1px solid #E9ECEF; border-radius: 8px; font-size: 0.938rem; color: #0C2B44; background: #FFFFFF;">
                        <option value="">Todos</option>
                        <option value="planificacion" {{ request('estado') == 'planificacion' ? 'selected' : '' }}>Planificación</option>
                        <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activo</option>
                        <option value="en_curso" {{ request('estado') == 'en_curso' ? 'selected' : '' }}>En Curso</option>
                        <option value="finalizado" {{ request('estado') == 'finalizado' ? 'selected' : '' }}>Finalizado</option>
                        <option value="cancelado" {{ request('estado') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </div>
                <div style="display: flex; align-items: flex-end;">
                    <button type="submit" style="width: 100%; padding: 10px 20px; background: #00A36C; color: #FFFFFF; border: none; border-radius: 8px; font-weight: 500; cursor: pointer; transition: all 0.3s ease;">
                        <i class="fas fa-search mr-2"></i>Filtrar
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Botones de Exportación -->
    <div style="display: flex; gap: 12px; margin-bottom: 24px; flex-wrap: wrap;">
        <button id="btnExportarPDF" style="display: inline-flex; align-items: center; padding: 12px 24px; background: #0C2B44; color: #FFFFFF; border: none; border-radius: 12px; font-weight: 500; cursor: pointer; transition: all 0.3s ease;">
            <i class="fas fa-file-pdf mr-2"></i>Exportar PDF
        </button>
        <button id="btnExportarExcel" style="display: inline-flex; align-items: center; padding: 12px 24px; background: #00A36C; color: #FFFFFF; border: none; border-radius: 12px; font-weight: 500; cursor: pointer; transition: all 0.3s ease;">
            <i class="fas fa-file-excel mr-2"></i>Exportar Excel
        </button>
    </div>

    <!-- KPIs Principales -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 32px;">
        <div style="background: #FFFFFF; border: 1px solid #E9ECEF; border-radius: 16px; padding: 28px;">
            <div style="display: flex; align-items: center; margin-bottom: 16px;">
                <div style="width: 48px; height: 48px; background: #F0F9F5; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 16px;">
                    <i class="fas fa-calendar-alt" style="color: #0C2B44; font-size: 1.25rem;"></i>
                </div>
                <div style="flex: 1;">
                    <p style="margin: 0; color: #6C757D; font-size: 0.875rem; font-weight: 500;">Total Eventos</p>
                </div>
            </div>
            <h2 style="margin: 0; font-size: 2.5rem; font-weight: 700; color: #0C2B44; line-height: 1.2;" id="totalEventos">0</h2>
            <p style="margin: 8px 0 0 0; color: #6C757D; font-size: 0.813rem;" id="detalleTotalEventos">Eventos regulares + Mega eventos</p>
        </div>

        <div style="background: #FFFFFF; border: 1px solid #E9ECEF; border-radius: 16px; padding: 28px;">
            <div style="display: flex; align-items: center; margin-bottom: 16px;">
                <div style="width: 48px; height: 48px; background: #E8F5E9; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 16px;">
                    <i class="fas fa-check-circle" style="color: #00A36C; font-size: 1.25rem;"></i>
                </div>
                <div style="flex: 1;">
                    <p style="margin: 0; color: #6C757D; font-size: 0.875rem; font-weight: 500;">Finalizados</p>
                </div>
            </div>
            <h2 style="margin: 0; font-size: 2.5rem; font-weight: 700; color: #00A36C; line-height: 1.2;" id="eventosFinalizados">0</h2>
            <p style="margin: 8px 0 0 0; color: #6C757D; font-size: 0.813rem;">Eventos completados</p>
        </div>

        <div style="background: #FFFFFF; border: 1px solid #E9ECEF; border-radius: 16px; padding: 28px;">
            <div style="display: flex; align-items: center; margin-bottom: 16px;">
                <div style="width: 48px; height: 48px; background: #F0F4F8; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 16px;">
                    <i class="fas fa-users" style="color: #0C2B44; font-size: 1.25rem;"></i>
                </div>
                <div style="flex: 1;">
                    <p style="margin: 0; color: #6C757D; font-size: 0.875rem; font-weight: 500;">Participantes</p>
                </div>
            </div>
            <h2 style="margin: 0; font-size: 2.5rem; font-weight: 700; color: #0C2B44; line-height: 1.2;" id="totalParticipantes">0</h2>
            <p style="margin: 8px 0 0 0; color: #6C757D; font-size: 0.813rem;">Total de asistentes</p>
        </div>

        <div style="background: #FFFFFF; border: 1px solid #E9ECEF; border-radius: 16px; padding: 28px;">
            <div style="display: flex; align-items: center; margin-bottom: 16px;">
                <div style="width: 48px; height: 48px; background: #FFF4E6; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 16px;">
                    <i class="fas fa-percentage" style="color: #0C2B44; font-size: 1.25rem;"></i>
                </div>
                <div style="flex: 1;">
                    <p style="margin: 0; color: #6C757D; font-size: 0.875rem; font-weight: 500;">Tasa Finalización</p>
                </div>
            </div>
            <h2 style="margin: 0; font-size: 2.5rem; font-weight: 700; color: #00A36C; line-height: 1.2;" id="tasaFinalizacion">0%</h2>
            <p style="margin: 8px 0 0 0; color: #6C757D; font-size: 0.813rem;">Porcentaje completado</p>
        </div>
    </div>

    <!-- Gráficas -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 24px; margin-bottom: 32px;">
        <!-- Gráfico de Dona: Distribución por Categoría -->
        <div style="background: #FFFFFF; border: 1px solid #E9ECEF; border-radius: 16px; padding: 32px;">
            <h3 style="margin: 0 0 24px 0; font-size: 1.25rem; font-weight: 600; color: #0C2B44;">
                Distribución por Categoría
            </h3>
            <div style="position: relative; height: 300px;">
                <canvas id="chartCategorias"></canvas>
            </div>
        </div>

        <!-- Gráfico de Barras: Distribución por Estado -->
        <div style="background: #FFFFFF; border: 1px solid #E9ECEF; border-radius: 16px; padding: 32px;">
            <h3 style="margin: 0 0 24px 0; font-size: 1.25rem; font-weight: 600; color: #0C2B44;">
                Distribución por Estado
            </h3>
            <div style="position: relative; height: 300px;">
                <canvas id="chartEstados"></canvas>
            </div>
        </div>
    </div>

    <!-- Gráfico de Comparación: Eventos Regulares vs Mega Eventos -->
    <div style="background: #FFFFFF; border: 1px solid #E9ECEF; border-radius: 16px; padding: 32px; margin-bottom: 32px;">
        <h3 style="margin: 0 0 24px 0; font-size: 1.25rem; font-weight: 600; color: #0C2B44;">
            Comparación: Eventos Regulares vs Mega Eventos
        </h3>
        <div style="position: relative; height: 350px;">
            <canvas id="chartComparacion"></canvas>
        </div>
    </div>

    <!-- Tabla de Resumen Detallado -->
    <div style="background: #FFFFFF; border: 1px solid #E9ECEF; border-radius: 16px; padding: 32px;">
        <h3 style="margin: 0 0 24px 0; font-size: 1.25rem; font-weight: 600; color: #0C2B44;">
            Resumen por Categoría
        </h3>
        <div style="overflow-x: auto;">
            <table id="tablaResumen" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #F8F9FA; border-bottom: 2px solid #E9ECEF;">
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #0C2B44; font-size: 0.875rem;">Categoría</th>
                        <th style="padding: 12px 16px; text-align: right; font-weight: 600; color: #0C2B44; font-size: 0.875rem;">Cantidad</th>
                        <th style="padding: 12px 16px; text-align: right; font-weight: 600; color: #0C2B44; font-size: 0.875rem;">Porcentaje</th>
                    </tr>
                </thead>
                <tbody id="tablaResumenBody">
                    <tr>
                        <td colspan="3" style="padding: 24px; text-align: center; color: #6C757D;">Cargando datos...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="{{ asset('assets/js/config.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let chartCategorias = null;
let chartEstados = null;
let chartComparacion = null;

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
        const response = await fetch(`${API_BASE_URL}/api/reportes-ong/resumen-ejecutivo?${queryString}`, {
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
            
            // Actualizar KPIs
            updateKPIs(datos);
            
            // Crear gráficos
            createCharts(datos);
            
            // Actualizar tabla
            updateTable(datos);
        } else {
            throw new Error(result.error || 'Error al cargar datos');
        }

        // Configurar botones de exportación
        setupExportButtons(token, filtrosParams);

    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudieron cargar los datos del reporte. ' + error.message
        });
    }
});

function updateKPIs(datos) {
    const totales = datos.totales || {};
    const kpis = datos.kpis || {};
    
    // Total eventos
    const totalEventos = totales.total_eventos || 0;
    document.getElementById('totalEventos').textContent = totalEventos.toLocaleString();
    document.getElementById('detalleTotalEventos').textContent = 
        `${totales.total_eventos_regulares || 0} regulares + ${totales.total_mega_eventos || 0} mega eventos`;
    
    // Eventos finalizados
    document.getElementById('eventosFinalizados').textContent = (kpis.eventos_finalizados || 0).toLocaleString();
    
    // Total participantes
    document.getElementById('totalParticipantes').textContent = (kpis.total_participantes || 0).toLocaleString();
    
    // Tasa de finalización
    document.getElementById('tasaFinalizacion').textContent = (kpis.tasa_finalizacion || 0).toFixed(2) + '%';
}

function createCharts(datos) {
    if (typeof Chart === 'undefined') {
        console.warn('Chart.js no está disponible');
        return;
    }

    // Configuración global
    Chart.defaults.font.family = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#6C757D';

    // 1. Gráfico de Dona: Categorías
    const ctxCategorias = document.getElementById('chartCategorias');
    if (ctxCategorias) {
        if (chartCategorias) chartCategorias.destroy();
        
        const graficoTorta = datos.grafico_torta || [];
        const categorias = graficoTorta.map(item => item.categoria);
        const valores = graficoTorta.map(item => item.cantidad);
        
        const colores = ['#00A36C', '#0C2B44', '#667eea', '#f5576c', '#f093fb', '#4facfe', '#43e97b', '#fa709a'];
        
        chartCategorias = new Chart(ctxCategorias, {
            type: 'doughnut',
            data: {
                labels: categorias,
                datasets: [{
                    data: valores,
                    backgroundColor: colores.slice(0, categorias.length),
                    borderWidth: 0,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: { size: 12, weight: '500' },
                            color: '#0C2B44'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const porcentaje = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                return `${context.label}: ${context.parsed} (${porcentaje}%)`;
                            }
                        }
                    }
                },
                cutout: '70%'
            }
        });
    }

    // 2. Gráfico de Barras: Estados
    const ctxEstados = document.getElementById('chartEstados');
    if (ctxEstados) {
        if (chartEstados) chartEstados.destroy();
        
        const porEstado = datos.totales?.por_estado || {};
        const estados = Object.keys(porEstado).map(e => e.charAt(0).toUpperCase() + e.slice(1).replace('_', ' '));
        const valores = Object.values(porEstado);
        
        chartEstados = new Chart(ctxEstados, {
            type: 'bar',
            data: {
                labels: estados,
                datasets: [{
                    label: 'Cantidad',
                    data: valores,
                    backgroundColor: '#00A36C',
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0, 0, 0, 0.05)' },
                        ticks: { color: '#6C757D', stepSize: 1 }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#6C757D' }
                    }
                }
            }
        });
    }

    // 3. Gráfico de Comparación: Eventos Regulares vs Mega Eventos
    const ctxComparacion = document.getElementById('chartComparacion');
    if (ctxComparacion) {
        if (chartComparacion) chartComparacion.destroy();
        
        const totales = datos.totales || {};
        
        chartComparacion = new Chart(ctxComparacion, {
            type: 'bar',
            data: {
                labels: ['Total', 'Finalizados', 'Activos'],
                datasets: [
                    {
                        label: 'Eventos Regulares',
                        data: [
                            totales.total_eventos_regulares || 0,
                            Math.round((datos.kpis?.eventos_finalizados || 0) * ((totales.total_eventos_regulares || 0) / (totales.total_eventos || 1))),
                            Math.round((datos.kpis?.eventos_activos || 0) * ((totales.total_eventos_regulares || 0) / (totales.total_eventos || 1)))
                        ],
                        backgroundColor: '#0C2B44',
                        borderRadius: 8
                    },
                    {
                        label: 'Mega Eventos',
                        data: [
                            totales.total_mega_eventos || 0,
                            Math.round((datos.kpis?.eventos_finalizados || 0) * ((totales.total_mega_eventos || 0) / (totales.total_eventos || 1))),
                            Math.round((datos.kpis?.eventos_activos || 0) * ((totales.total_mega_eventos || 0) / (totales.total_eventos || 1)))
                        ],
                        backgroundColor: '#00A36C',
                        borderRadius: 8
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            padding: 15,
                            font: { size: 12, weight: '500' },
                            color: '#0C2B44'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0, 0, 0, 0.05)' },
                        ticks: { color: '#6C757D', stepSize: 1 }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#6C757D' }
                    }
                }
            }
        });
    }
}

function updateTable(datos) {
    const tbody = document.getElementById('tablaResumenBody');
    if (!tbody) return;
    
    const porCategoria = datos.totales?.por_categoria || {};
    const total = datos.totales?.total_eventos || 1;
    
    if (Object.keys(porCategoria).length === 0) {
        tbody.innerHTML = '<tr><td colspan="3" style="padding: 24px; text-align: center; color: #6C757D;">No hay datos disponibles</td></tr>';
        return;
    }
    
    let html = '';
    Object.entries(porCategoria).forEach(([categoria, cantidad]) => {
        const porcentaje = total > 0 ? ((cantidad / total) * 100).toFixed(2) : 0;
        html += `
            <tr style="border-bottom: 1px solid #F8F9FA;">
                <td style="padding: 12px 16px; color: #0C2B44; font-weight: 500;">${categoria.charAt(0).toUpperCase() + categoria.slice(1)}</td>
                <td style="padding: 12px 16px; text-align: right; color: #0C2B44; font-weight: 600;">${cantidad.toLocaleString()}</td>
                <td style="padding: 12px 16px; text-align: right;">
                    <div style="display: flex; align-items: center; justify-content: flex-end; gap: 12px;">
                        <div style="flex: 1; max-width: 150px; height: 8px; background: #F8F9FA; border-radius: 4px; overflow: hidden;">
                            <div style="height: 100%; background: #00A36C; width: ${porcentaje}%; border-radius: 4px;"></div>
                        </div>
                        <span style="color: #0C2B44; font-weight: 600; min-width: 50px; text-align: right;">${porcentaje}%</span>
                    </div>
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

function setupExportButtons(token, filtros) {
    // Botón exportar PDF
    document.getElementById('btnExportarPDF').addEventListener('click', async function() {
        const button = this;
        const originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Generando PDF...';
        
        try {
            // Construir URL con parámetros de filtros
            const params = new URLSearchParams();
            if (filtros.fecha_inicio) params.append('fecha_inicio', filtros.fecha_inicio);
            if (filtros.fecha_fin) params.append('fecha_fin', filtros.fecha_fin);
            if (filtros.categoria) params.append('categoria', filtros.categoria);
            if (filtros.estado) params.append('estado', filtros.estado);
            
            // Obtener token de autenticación
            const token = localStorage.getItem('auth_token');
            const headers = {
                'Accept': 'application/pdf',
                'X-Requested-With': 'XMLHttpRequest'
            };
            
            if (token) {
                headers['Authorization'] = `Bearer ${token}`;
            }
            
            // Usar fetch para poder manejar errores
            const url = `{{ route('ong.reportes.resumen-ejecutivo.exportar.pdf') }}?${params.toString()}`;
            
            const response = await fetch(url, {
                method: 'GET',
                headers: headers,
                credentials: 'include'
            });
            
            // Verificar si la respuesta es exitosa
            if (!response.ok) {
                const errorData = await response.json().catch(() => ({ error: 'Error desconocido' }));
                throw new Error(errorData.error || `Error ${response.status}: ${response.statusText}`);
            }
            
            // Verificar que el contenido sea un PDF
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/pdf')) {
                const errorText = await response.text();
                try {
                    const errorData = JSON.parse(errorText);
                    throw new Error(errorData.error || 'El servidor no devolvió un PDF válido');
                } catch (e) {
                    throw new Error('El servidor no devolvió un PDF válido');
                }
            }
            
            // Obtener el blob del PDF
            const blob = await response.blob();
            
            // Verificar que el blob no esté vacío
            if (blob.size < 100) {
                throw new Error('El PDF generado está vacío o corrupto');
            }
            
            // Crear URL del blob y descargar
            const blobUrl = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = blobUrl;
            link.download = `reporte-resumen-ejecutivo-${new Date().toISOString().split('T')[0]}.pdf`;
            link.style.display = 'none';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            // Limpiar el blob URL después de un delay
            setTimeout(() => {
                window.URL.revokeObjectURL(blobUrl);
            }, 100);
            
            // Restaurar botón
            button.disabled = false;
            button.innerHTML = originalText;
            
            Swal.fire({
                icon: 'success',
                title: 'PDF Generado',
                text: 'El reporte PDF se ha descargado correctamente',
                timer: 2000,
                showConfirmButton: false
            });
        } catch (error) {
            console.error('Error generando PDF:', error);
            button.disabled = false;
            button.innerHTML = originalText;
            Swal.fire({
                icon: 'error',
                title: 'Error al Generar PDF',
                text: error.message || 'No se pudo generar el PDF. Por favor, intente nuevamente.',
                confirmButtonText: 'Aceptar'
            });
        }
    });

    // Botón exportar Excel
    document.getElementById('btnExportarExcel').addEventListener('click', function() {
        const button = this;
        const originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Generando Excel...';
        
        try {
            // Construir URL con parámetros de filtros
            const params = new URLSearchParams();
            if (filtros.fecha_inicio) params.append('fecha_inicio', filtros.fecha_inicio);
            if (filtros.fecha_fin) params.append('fecha_fin', filtros.fecha_fin);
            if (filtros.categoria) params.append('categoria', filtros.categoria);
            if (filtros.estado) params.append('estado', filtros.estado);
            
            // Usar la ruta web de Laravel - window.location.href descarga directamente
            const url = `{{ route('ong.reportes.resumen-ejecutivo.exportar.excel') }}?${params.toString()}`;
            
            // Crear un enlace temporal y hacer click para descargar
            const link = document.createElement('a');
            link.href = url;
            link.download = `reporte-resumen-ejecutivo-${new Date().toISOString().split('T')[0]}.xlsx`;
            link.style.display = 'none';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            // Restaurar botón después de un breve delay
            setTimeout(() => {
                button.disabled = false;
                button.innerHTML = originalText;
                
                Swal.fire({
                    icon: 'success',
                    title: 'Excel Generado',
                    text: 'El reporte Excel se está descargando',
                    timer: 2000,
                    showConfirmButton: false
                });
            }, 1000);
        } catch (error) {
            console.error('Error generando Excel:', error);
            button.disabled = false;
            button.innerHTML = originalText;
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo generar el Excel: ' + error.message
            });
        }
    });
}
</script>
@endpush

@push('css')
<style>
    body {
        background-color: #F8F9FA;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    }
    
    button:hover {
        opacity: 0.85;
        transform: translateY(-2px);
    }
    
    input:focus, select:focus {
        outline: none;
        border-color: #00A36C !important;
        box-shadow: 0 0 0 3px rgba(0, 163, 108, 0.1);
    }
    
    @media (max-width: 768px) {
        .container-fluid {
            padding: 20px 16px !important;
        }
        
        h1 {
            font-size: 1.5rem !important;
        }
    }
</style>
@endpush

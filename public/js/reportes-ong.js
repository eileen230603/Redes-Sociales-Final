// Configuración global de Chart.js
if (typeof Chart !== 'undefined') {
    Chart.defaults.font.family = 'Inter, Roboto, sans-serif';
    Chart.defaults.color = '#6C757D';
    Chart.defaults.plugins.legend.position = 'bottom';
    Chart.defaults.plugins.legend.labels.padding = 20;
}

// Variables globales
let charts = {};

/**
 * Inicializar reporte de eventos regulares
 */
function initReportesEventos() {
    const token = localStorage.getItem('token');
    const tipoUsuario = localStorage.getItem('tipo_usuario');
    const ongId = parseInt(localStorage.getItem('id_entidad') || localStorage.getItem('id_usuario'), 10);

    // Validar autenticación
    if (!token || tipoUsuario !== 'ONG' || isNaN(ongId) || ongId <= 0) {
        window.location.href = '/login';
        return;
    }

    // Cargar datos iniciales
    applyFilters('eventos');
}

/**
 * Aplicar filtros y cargar datos
 */
function applyFilters(tipoReporte) {
    const token = localStorage.getItem('token');
    const form = document.getElementById('filtrosForm');
    const formData = new FormData(form);
    const params = new URLSearchParams();

    // Convertir FormData a URLSearchParams
    for (const [key, value] of formData.entries()) {
        if (value) {
            if (key === 'estados[]') {
                // Manejar checkboxes de estados
                const estados = formData.getAll('estados[]');
                if (estados.length > 0) {
                    params.append('estado', estados.join(','));
                }
            } else {
                params.append(key, value);
            }
        }
    }

    // Determinar endpoint según tipo
    let endpoint = '';
    if (tipoReporte === 'eventos') {
        endpoint = `${API_BASE_URL}/api/reportes-ong/eventos-metrics`;
    } else if (tipoReporte === 'mega-eventos') {
        endpoint = `${API_BASE_URL}/api/reportes-ong/mega-eventos-metrics`;
    } else {
        endpoint = `${API_BASE_URL}/api/reportes-ong/consolidado-metrics`;
    }

    // Mostrar loading
    showLoadingSpinner();

    // Hacer fetch
    fetch(`${endpoint}?${params.toString()}`, {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        hideLoadingSpinner();
        if (data.success && data.datos) {
            updateCharts(data.datos, tipoReporte);
            updateMetrics(data.datos, tipoReporte);
            updateTables(data.datos, tipoReporte);
        } else {
            console.error('Error:', data.error);
        }
    })
    .catch(error => {
        hideLoadingSpinner();
        console.error('Error:', error);
    });
}

/**
 * Actualizar gráficos con nuevos datos
 */
function updateCharts(datos, tipoReporte) {
    // Gráfico de categoría (bar)
    if (datos.distribucion_categoria) {
        updateBarChart('graficoCategoria', datos.distribucion_categoria, 'Distribución por Categoría');
    }

    // Gráfico de estado (doughnut)
    if (datos.distribucion_estado) {
        updateDoughnutChart('graficoEstado', datos.distribucion_estado, 'Distribución por Estado');
    }

    // Gráfico de tendencias (line)
    if (datos.tendencias_mensuales) {
        updateLineChart('graficoTendencias', datos.tendencias_mensuales, 'Tendencias Temporales');
    }
}

/**
 * Actualizar gráfico de barras
 */
function updateBarChart(canvasId, data, title) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return;

    // Destruir gráfico anterior si existe
    if (charts[canvasId]) {
        charts[canvasId].destroy();
    }

    const labels = Object.keys(data);
    const values = Object.values(data);

    charts[canvasId] = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: title,
                data: values,
                backgroundColor: '#00A36C',
                borderColor: '#008a5a',
                borderWidth: 2,
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
}

/**
 * Actualizar gráfico de dona
 */
function updateDoughnutChart(canvasId, data, title) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return;

    if (charts[canvasId]) {
        charts[canvasId].destroy();
    }

    const labels = Object.keys(data);
    const values = Object.values(data);
    const colors = ['#00A36C', '#0C2B44', '#667eea', '#f093fb', '#FF6B35'];

    charts[canvasId] = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: colors.slice(0, labels.length),
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                }
            }
        }
    });
}

/**
 * Actualizar gráfico de líneas
 */
function updateLineChart(canvasId, data, title) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return;

    if (charts[canvasId]) {
        charts[canvasId].destroy();
    }

    const labels = Object.keys(data);
    const values = Object.values(data);

    charts[canvasId] = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: title,
                data: values,
                borderColor: '#00A36C',
                backgroundColor: 'rgba(0, 163, 108, 0.1)',
                borderWidth: 2,
                fill: false,
                tension: 0.4,
                pointRadius: 4,
                pointHoverRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
}

/**
 * Actualizar métricas en la página
 */
function updateMetrics(datos, tipoReporte) {
    if (tipoReporte === 'eventos') {
        document.getElementById('totalEventos').textContent = datos.total_eventos || 0;
        document.getElementById('totalParticipantes').textContent = datos.total_participantes || 0;
        document.getElementById('eventosActivos').textContent = datos.eventos_activos || 0;
        document.getElementById('promedioParticipantes').textContent = (datos.promedio_participantes || 0).toFixed(2);
    }
}

/**
 * Actualizar tablas
 */
function updateTables(datos, tipoReporte) {
    if (tipoReporte === 'eventos' && datos.top_eventos) {
        const tbody = document.querySelector('#tablaTopEventos tbody');
        if (tbody) {
            tbody.innerHTML = '';
            datos.top_eventos.forEach(evento => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${evento.titulo}</td>
                    <td class="text-center">${evento.participantes}</td>
                    <td class="text-center">${evento.capacidad}</td>
                    <td class="text-center">${evento.tasa_ocupacion}%</td>
                    <td class="text-center">${evento.fecha_inicio || 'N/A'}</td>
                `;
                tbody.appendChild(row);
            });
        }
    }
}

/**
 * Mostrar spinner de carga
 */
function showLoadingSpinner() {
    // Implementar según necesidad
}

/**
 * Ocultar spinner de carga
 */
function hideLoadingSpinner() {
    // Implementar según necesidad
}

/**
 * Exportar funciones
 */
function exportarPDF(tipoReporte) {
    const token = localStorage.getItem('token');
    const params = new URLSearchParams(window.location.search);
    params.append('token', token);
    
    let url = '';
    if (tipoReporte === 'eventos') {
        url = `${API_BASE_URL}/api/reportes-ong/eventos/export-pdf?${params.toString()}`;
    } else if (tipoReporte === 'mega-eventos') {
        url = `${API_BASE_URL}/api/reportes-ong/mega-eventos/export-pdf?${params.toString()}`;
    } else {
        url = `${API_BASE_URL}/api/reportes-ong/consolidado/export-pdf?${params.toString()}`;
    }
    
    window.open(url, '_blank');
}

function exportarExcel(tipoReporte) {
    const token = localStorage.getItem('token');
    const params = new URLSearchParams(window.location.search);
    params.append('token', token);
    
    let url = '';
    if (tipoReporte === 'eventos') {
        url = `${API_BASE_URL}/api/reportes-ong/eventos/export-excel?${params.toString()}`;
    } else if (tipoReporte === 'mega-eventos') {
        url = `${API_BASE_URL}/api/reportes-ong/mega-eventos/export-excel?${params.toString()}`;
    } else {
        url = `${API_BASE_URL}/api/reportes-ong/consolidado/export-excel?${params.toString()}`;
    }
    
    window.open(url, '_blank');
}

function exportarCSV(tipoReporte) {
    // Similar a Excel pero con formato CSV
    exportarExcel(tipoReporte);
}

function exportarJSON(tipoReporte) {
    const token = localStorage.getItem('token');
    const params = new URLSearchParams(window.location.search);
    
    let endpoint = '';
    if (tipoReporte === 'eventos') {
        endpoint = `${API_BASE_URL}/api/reportes-ong/eventos-metrics`;
    } else if (tipoReporte === 'mega-eventos') {
        endpoint = `${API_BASE_URL}/api/reportes-ong/mega-eventos-metrics`;
    } else {
        endpoint = `${API_BASE_URL}/api/reportes-ong/consolidado-metrics`;
    }
    
    fetch(`${endpoint}?${params.toString()}`, {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        const jsonStr = JSON.stringify(data, null, 2);
        const blob = new Blob([jsonStr], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `reporte-${tipoReporte}-${new Date().toISOString().split('T')[0]}.json`;
        a.click();
        URL.revokeObjectURL(url);
    });
}


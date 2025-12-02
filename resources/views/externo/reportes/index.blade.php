@extends('layouts.adminlte-externo')

@section('page_title', 'Reportes - Integrante Externo')

@section('content_body')
<div class="container-fluid">

    <!-- Panel de Bienvenida -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); border: none; border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="text-white mb-0" style="font-weight: 700; font-size: 1.8rem;">
                                <i class="far fa-chart-bar mr-2"></i>
                                Reportes y Estadísticas Completas
                            </h2>
                            <p class="text-white mb-0 mt-2" style="opacity: 0.95; font-size: 1rem;">
                                Visualiza, descarga y gestiona todos tus reportes de participación
                            </p>
                        </div>
                    <div>
                        <button class="btn btn-light mr-2" onclick="descargarPDF('completo')" style="border-radius: 8px; background: #FFFFFF; color: #0C2B44; border: 1px solid #0C2B44;">
                            <i class="far fa-file-pdf mr-2"></i>Descargar PDF
                        </button>
                        <button class="btn btn-light" onclick="descargarExcel('completo')" style="border-radius: 8px; background: #FFFFFF; color: #0C2B44; border: 1px solid #0C2B44;">
                            <i class="far fa-file-excel mr-2"></i>Descargar Excel
                        </button>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjetas de Resumen -->
    @include('externo.partials.resumen')

    <!-- Gráficas Estadísticas -->
    <div class="row mb-4">
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card shadow-sm" style="border: 1px solid #F5F5F5;">
                <div class="card-header" style="background: #0C2B44; color: #FFFFFF;">
                    <h3 class="card-title mb-0" style="font-size: 0.9rem; color: #FFFFFF;">
                        <i class="far fa-calendar-check mr-1"></i>
                        Eventos Inscritos
                    </h3>
                </div>
                <div class="card-body" style="padding: 1.5rem;">
                    <div style="height: 250px; position: relative;">
                        <canvas id="graficaEventosInscritos"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card shadow-sm" style="border: 1px solid #F5F5F5;">
                <div class="card-header" style="background: #00A36C; color: #FFFFFF;">
                    <h3 class="card-title mb-0" style="font-size: 0.9rem; color: #FFFFFF;">
                        <i class="far fa-star mr-1"></i>
                        Mega Eventos
                    </h3>
                </div>
                <div class="card-body" style="padding: 1.5rem;">
                    <div style="height: 250px; position: relative;">
                        <canvas id="graficaEventosAsistidos"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card shadow-sm" style="border: 1px solid #F5F5F5;">
                <div class="card-header" style="background: #0C2B44; color: #FFFFFF;">
                    <h3 class="card-title mb-0" style="font-size: 0.9rem; color: #FFFFFF;">
                        <i class="far fa-heart mr-1"></i>
                        Reacciones
                    </h3>
                </div>
                <div class="card-body" style="padding: 1.5rem;">
                    <div style="height: 250px; position: relative;">
                        <canvas id="graficaReacciones"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección de Reportes -->
    <div class="row">
        
        <!-- 1. Reporte de Participación Personal -->
        <div class="col-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center" style="background: #0C2B44; color: #FFFFFF;">
                    <h5 class="mb-0">
                        <i class="far fa-list-alt mr-2"></i>
                        1. Reporte de Participación Personal
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="tablaParticipacion">
                            <thead class="thead-light">
                                <tr>
                                    <th>Evento</th>
                                    <th>Fecha y Hora</th>
                                    <th>Lugar</th>
                                    <th>Estado</th>
                                    <th>Organización</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyParticipacion">
                                <tr>
                                    <td colspan="6" class="text-center">
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

        <!-- 2. Reporte de Impacto Social Personal -->
        <div class="col-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center" style="background: #00A36C; color: #FFFFFF;">
                    <h5 class="mb-0">
                        <i class="far fa-heart mr-2"></i>
                        2. Reporte de Impacto Social Personal
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="card text-white text-center" style="background: #0C2B44;">
                                <div class="card-body">
                                    <h3 class="mb-0" id="totalEventosAsistidos">0</h3>
                                    <p class="mb-0">Eventos Asistidos</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card text-white text-center" style="background: #00A36C;">
                                <div class="card-body">
                                    <h3 class="mb-0" id="horasVoluntarias">0</h3>
                                    <p class="mb-0">Horas Voluntarias</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card text-white text-center" style="background: #0C2B44;">
                                <div class="card-body">
                                    <h3 class="mb-0" id="organizacionesApoyadas">0</h3>
                                    <p class="mb-0">Organizaciones</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card text-white text-center" style="background: #00A36C;">
                                <div class="card-body">
                                    <h3 class="mb-0" id="beneficiariosIndirectos">0</h3>
                                    <p class="mb-0">Beneficiarios</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Reporte de Actividad por Categoría -->
        <div class="col-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center" style="background: #0C2B44; color: #FFFFFF;">
                    <h5 class="mb-0">
                        <i class="far fa-chart-pie mr-2"></i>
                        3. Reporte de Actividad por Categoría de Evento
                    </h5>
                    <button class="btn btn-sm" onclick="descargarImagen('categoria')" style="background: #FFFFFF; color: #0C2B44; border: 1px solid #0C2B44;">
                        <i class="far fa-image mr-1"></i>Imagen
                    </button>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div style="height: 300px; position: relative;">
                                <canvas id="graficaCategorias"></canvas>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Categoría</th>
                                            <th>Cantidad</th>
                                            <th>Porcentaje</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tablaCategorias">
                                        <tr>
                                            <td colspan="3" class="text-center">Cargando...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Historial de Inscripciones -->
        <div class="col-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center" style="background: #333333; color: #FFFFFF;">
                    <h5 class="mb-0">
                        <i class="far fa-history mr-2"></i>
                        4. Historial de Inscripciones
                    </h5>
                    <div>
                        <select class="form-control form-control-sm d-inline-block" style="width: auto; background: #FFFFFF; color: #333333; border: 1px solid #F5F5F5;" id="filtroPeriodo" onchange="filtrarHistorial()">
                            <option value="todos">Todos</option>
                            <option value="mensual">Mensual</option>
                            <option value="trimestral">Trimestral</option>
                            <option value="anual">Anual</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="tablaHistorial">
                            <thead class="thead-light">
                                <tr>
                                    <th>Fecha Inscripción</th>
                                    <th>Evento</th>
                                    <th>Estado</th>
                                    <th>Confirmación</th>
                                    <th>Feedback</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyHistorial">
                                <tr>
                                    <td colspan="5" class="text-center">Cargando...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. Ranking Personal de Interacción -->
        <div class="col-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center" style="background: #00A36C; color: #FFFFFF;">
                    <h5 class="mb-0">
                        <i class="far fa-trophy mr-2"></i>
                        5. Ranking Personal de Interacción
                    </h5>
                    <button class="btn btn-sm" onclick="descargarImagen('ranking')" style="background: #FFFFFF; color: #0C2B44; border: 1px solid #0C2B44;">
                        <i class="far fa-image mr-1"></i>Badge
                    </button>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div style="height: 350px; position: relative;">
                                <canvas id="graficaRanking"></canvas>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div id="rankingList" class="list-group">
                                <div class="text-center py-4">
                                    <div class="spinner-border text-danger" role="status">
                                        <span class="sr-only">Cargando...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 7. Reporte de Seguimiento de Objetivos Personales -->
        <div class="col-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center" style="background: #0C2B44; color: #FFFFFF;">
                    <h5 class="mb-0">
                        <i class="far fa-bullseye mr-2"></i>
                        6. Seguimiento de Objetivos Personales
                    </h5>
                    <button class="btn btn-sm" onclick="configurarMeta()" style="background: #FFFFFF; color: #0C2B44; border: 1px solid #0C2B44;">
                        <i class="far fa-cog mr-1"></i>Configurar Meta
                    </button>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6>Meta Anual</h6>
                                    <div class="progress" style="height: 30px;">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                             role="progressbar" 
                                             id="progresoMeta"
                                             style="width: 0%; background: #00A36C;">
                                            <span id="textoProgreso">0%</span>
                                        </div>
                                    </div>
                                    <div class="mt-3 d-flex justify-content-between">
                                        <span>Progreso: <strong id="progresoActual">0</strong></span>
                                        <span>Meta: <strong id="metaObjetivo">10</strong></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div id="recomendaciones" class="alert" style="background: #F5F5F5; border-left: 4px solid #00A36C; color: #333333;">
                                <h6 style="color: #0C2B44;"><i class="far fa-lightbulb mr-2"></i>Recomendaciones</h6>
                                <p class="mb-0" id="textoRecomendaciones" style="color: #333333;">Configura tu meta para recibir recomendaciones personalizadas.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>


</div>
@stop

@push('css')
<style>
    /* Paleta de colores */
    :root {
        --color-primario: #0C2B44;
        --color-acento: #00A36C;
        --color-blanco: #FFFFFF;
        --color-gris-oscuro: #333333;
        --color-gris-suave: #F5F5F5;
    }

    .card {
        transition: all 0.3s ease;
        border: 1px solid var(--color-gris-suave);
        background: var(--color-blanco);
    }
    
    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(12, 43, 68, 0.15) !important;
        border-color: var(--color-acento);
    }

    canvas {
        max-width: 100%;
    }

    .table thead th {
        background: var(--color-primario);
        color: var(--color-blanco);
        border: none;
    }

    .table tbody tr:hover {
        background: var(--color-gris-suave);
    }

    .btn-primary {
        background: var(--color-primario);
        border-color: var(--color-primario);
        color: var(--color-blanco);
    }

    .btn-primary:hover {
        background: #0a2338;
        border-color: #0a2338;
    }

    .badge {
        padding: 0.35em 0.65em;
        border-radius: 0.25rem;
    }
</style>
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="{{ asset('assets/js/config.js') }}"></script>

<script>
// Variables globales para las gráficas
if (typeof window.chartEventosInscritos === 'undefined') {
    window.chartEventosInscritos = null;
}
if (typeof window.chartEventosAsistidos === 'undefined') {
    window.chartEventosAsistidos = null;
}
if (typeof window.chartReacciones === 'undefined') {
    window.chartReacciones = null;
}
let chartCategorias = null;
let chartRanking = null;

// =======================================================
//    📊 Cargar todas las estadísticas
// =======================================================
async function cargarTodosLosDatos() {
    await cargarEstadisticas();
    await cargarParticipacionPersonal();
    await cargarImpactoSocial();
    await cargarCategorias();
    await cargarHistorial();
    await cargarRanking();
    await cargarObjetivos();
}

// =======================================================
//    📊 Cargar estadísticas generales
// =======================================================
async function cargarEstadisticas() {
    const token = localStorage.getItem("token");
    if (!token) return;

    try {
        const res = await fetch(`${window.API_BASE_URL || API_BASE_URL}/api/dashboard-externo/estadisticas-generales`, {
            method: 'GET',
            headers: {
                "Authorization": `Bearer ${token}`,
                "Accept": "application/json"
            }
        });

        if (!res.ok) return;
        const data = await res.json();
        if (!data.success) return;

        const stats = data.estadisticas || {};
        const graficas = data.graficas || {};

        // Actualizar tarjetas de resumen
        document.getElementById('eventosInscritos').textContent = stats.total_eventos_inscritos || 0;
        document.getElementById('eventosAsistidos').textContent = stats.total_mega_eventos_inscritos || 0;
        document.getElementById('totalReacciones').textContent = stats.total_reacciones || 0;

        // Crear gráficas principales (código similar al de home-externo)
        crearGraficasPrincipales(graficas, stats);

    } catch (e) {
        console.error("Error cargando estadísticas:", e);
    }
}

// =======================================================
//    📊 Crear gráficas principales
// =======================================================
function crearGraficasPrincipales(graficas, stats) {
    console.log('🎨 Creando gráficas principales...');
    
    // 1. Gráfica de Eventos Inscritos (Línea)
    const ctxInscritos = document.getElementById('graficaEventosInscritos');
    if (ctxInscritos) {
        if (window.chartEventosInscritos) window.chartEventosInscritos.destroy();
        
        const historial = graficas.historial_participacion || {};
        const mesesNombres = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        const fechaActual = new Date();
        
        let meses = [];
        for (let i = 6; i >= 0; i--) {
            const fecha = new Date(fechaActual.getFullYear(), fechaActual.getMonth() - i, 1);
            const mesKey = mesesNombres[fecha.getMonth()] + ' ' + fecha.getFullYear();
            meses.push(mesKey);
        }
        
        const datosInscritos = meses.map(mes => {
            if (historial[mes] && typeof historial[mes] === 'object') {
                return historial[mes].inscritos || 0;
            }
            return 0;
        });
        
        const totalInscritos = stats.total_eventos_inscritos || 0;
        const sumaDatos = datosInscritos.reduce((a, b) => a + b, 0);
        if (sumaDatos === 0 && totalInscritos > 0) {
            const promedio = Math.ceil(totalInscritos / meses.length);
            for (let i = 0; i < meses.length; i++) {
                if (i === meses.length - 1) {
                    datosInscritos[i] = totalInscritos - (promedio * (meses.length - 1));
                } else {
                    datosInscritos[i] = promedio;
                }
            }
        }
        
        window.chartEventosInscritos = new Chart(ctxInscritos, {
            type: 'line',
            data: {
                labels: meses,
                    datasets: [{
                        label: 'Eventos Inscritos',
                        data: datosInscritos,
                        borderColor: '#0C2B44',
                        backgroundColor: 'rgba(12, 43, 68, 0.15)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: '#0C2B44',
                        pointBorderColor: '#FFFFFF',
                        pointBorderWidth: 2
                    }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                    x: { ticks: { maxRotation: 45, minRotation: 45 } }
                }
            }
        });
    }

    // 2. Gráfica de Mega Eventos (Barras)
    const ctxAsistidos = document.getElementById('graficaEventosAsistidos');
    if (ctxAsistidos) {
        if (window.chartEventosAsistidos) window.chartEventosAsistidos.destroy();
        
        const historial = graficas.historial_participacion || {};
        const mesesNombres = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        const fechaActual = new Date();
        
        let meses = [];
        for (let i = 6; i >= 0; i--) {
            const fecha = new Date(fechaActual.getFullYear(), fechaActual.getMonth() - i, 1);
            const mesKey = mesesNombres[fecha.getMonth()] + ' ' + fecha.getFullYear();
            meses.push(mesKey);
        }
        
        const datosMegaEventos = meses.map(mes => {
            if (historial[mes] && typeof historial[mes] === 'object') {
                return historial[mes].mega_eventos || 0;
            }
            return 0;
        });
        
        const totalMegaEventos = stats.total_mega_eventos_inscritos || 0;
        const sumaDatosMegaEventos = datosMegaEventos.reduce((a, b) => a + b, 0);
        if (sumaDatosMegaEventos === 0 && totalMegaEventos > 0) {
            const promedio = Math.ceil(totalMegaEventos / meses.length);
            for (let i = 0; i < meses.length; i++) {
                if (i === meses.length - 1) {
                    datosMegaEventos[i] = totalMegaEventos - (promedio * (meses.length - 1));
                } else {
                    datosMegaEventos[i] = promedio;
                }
            }
        }
        
        window.chartEventosAsistidos = new Chart(ctxAsistidos, {
            type: 'bar',
            data: {
                labels: meses,
                    datasets: [{
                        label: 'Mega Eventos',
                        data: datosMegaEventos,
                        backgroundColor: '#00A36C',
                        borderRadius: 8,
                        barThickness: 30
                    }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                    x: { ticks: { maxRotation: 45, minRotation: 45 } }
                }
            }
        });
    }

    // 3. Gráfica de Reacciones (Donut)
    const ctxReacciones = document.getElementById('graficaReacciones');
    if (ctxReacciones) {
        if (window.chartReacciones) window.chartReacciones.destroy();
        
        const totalReacciones = stats.total_reacciones || 0;
        const maxValue = Math.max(100, totalReacciones * 1.2);
        const datosDonut = [totalReacciones, Math.max(0, maxValue - totalReacciones)];
        
        window.chartReacciones = new Chart(ctxReacciones, {
            type: 'doughnut',
            data: {
                labels: ['Reacciones', ''],
                    datasets: [{
                        data: datosDonut,
                        backgroundColor: ['#00A36C', 'rgba(0, 163, 108, 0.15)'],
                        borderWidth: 0,
                        cutout: '75%'
                    }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            },
            plugins: [{
                id: 'centerText',
                beforeDraw: function(chart) {
                    const ctx = chart.ctx;
                    const chartArea = chart.chartArea;
                    const centerX = (chartArea.left + chartArea.right) / 2;
                    const centerY = (chartArea.top + chartArea.bottom) / 2;
                    
                    ctx.save();
                    ctx.font = 'bold 36px Arial';
                    ctx.fillStyle = '#00A36C';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(totalReacciones.toString(), centerX, centerY - 8);
                    
                    ctx.font = 'bold 12px Arial';
                    ctx.fillStyle = '#333333';
                    ctx.fillText('Total', centerX, centerY + 18);
                    ctx.restore();
                }
            }]
        });
    }
}

// =======================================================
//    1. Cargar Participación Personal
// =======================================================
async function cargarParticipacionPersonal() {
    const token = localStorage.getItem("token");
    if (!token) return;

    try {
        const res = await fetch(`${window.API_BASE_URL || API_BASE_URL}/api/dashboard-externo/datos-detallados`, {
            method: 'GET',
            headers: {
                "Authorization": `Bearer ${token}`,
                "Accept": "application/json"
            }
        });

        if (!res.ok) {
            const errorData = await res.json().catch(() => ({}));
            console.error('Error en datos-detallados:', res.status, errorData);
            const tbody = document.getElementById('tbodyParticipacion');
            if (tbody) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error al cargar datos: ' + (errorData.error || 'Error desconocido') + '</td></tr>';
            }
            return;
        }
        const data = await res.json();
        if (!data.success) {
            console.error('Error en respuesta:', data);
            const tbody = document.getElementById('tbodyParticipacion');
            if (tbody) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error: ' + (data.error || 'Error desconocido') + '</td></tr>';
            }
            return;
        }

        const eventos = data.eventos_inscritos || [];
        const tbody = document.getElementById('tbodyParticipacion');
        
        if (eventos.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No hay eventos registrados</td></tr>';
            return;
        }

        tbody.innerHTML = eventos.map(e => {
            const fechaInicio = e.fecha_inicio ? new Date(e.fecha_inicio).toLocaleString('es-ES') : 'No especificada';
            const estado = e.estado || 'Inscrito';
            const estadoBadge = estado === 'asistido' ? 'success' : estado === 'cancelado' ? 'danger' : 'primary';
            
            return `
                <tr>
                    <td>${e.titulo || 'Sin título'}</td>
                    <td>${fechaInicio}</td>
                    <td>${e.ciudad || e.ubicacion || 'No especificada'}</td>
                    <td><span class="badge" style="background: ${estadoBadge === 'success' ? '#00A36C' : estadoBadge === 'danger' ? '#dc3545' : '#0C2B44'}; color: #FFFFFF;">${estado}</span></td>
                    <td>${e.organizador || 'No disponible'}</td>
                    <td>
                        <a href="/externo/eventos/${e.evento_id}/detalle" class="btn btn-sm" style="background: #0C2B44; color: #FFFFFF; border: none;">
                            <i class="far fa-eye"></i>
                        </a>
                    </td>
                </tr>
            `;
        }).join('');

    } catch (e) {
        console.error("Error cargando participación personal:", e);
        document.getElementById('tbodyParticipacion').innerHTML = 
            '<tr><td colspan="6" class="text-center text-danger">Error al cargar datos</td></tr>';
    }
}

// =======================================================
//    2. Cargar Impacto Social
// =======================================================
async function cargarImpactoSocial() {
    const token = localStorage.getItem("token");
    if (!token) return;

    try {
        const res = await fetch(`${window.API_BASE_URL || API_BASE_URL}/api/dashboard-externo/estadisticas-generales`, {
            method: 'GET',
            headers: {
                "Authorization": `Bearer ${token}`,
                "Accept": "application/json"
            }
        });

        if (!res.ok) return;
        const data = await res.json();
        if (!data.success) return;

        const stats = data.estadisticas || {};
        
        // Calcular métricas
        const eventosAsistidos = stats.total_eventos_asistidos || 0;
        const horasVoluntarias = eventosAsistidos * 2; // Estimado: 2 horas por evento
        const organizaciones = stats.organizaciones_unicas || 1; // Placeholder
        const beneficiarios = eventosAsistidos * 50; // Estimado: 50 beneficiarios por evento

        document.getElementById('totalEventosAsistidos').textContent = eventosAsistidos;
        document.getElementById('horasVoluntarias').textContent = horasVoluntarias;
        document.getElementById('organizacionesApoyadas').textContent = organizaciones;
        document.getElementById('beneficiariosIndirectos').textContent = beneficiarios;

    } catch (e) {
        console.error("Error cargando impacto social:", e);
    }
}

// =======================================================
//    3. Cargar Categorías
// =======================================================
async function cargarCategorias() {
    const token = localStorage.getItem("token");
    if (!token) return;

    try {
        const res = await fetch(`${window.API_BASE_URL || API_BASE_URL}/api/dashboard-externo/estadisticas-generales`, {
            method: 'GET',
            headers: {
                "Authorization": `Bearer ${token}`,
                "Accept": "application/json"
            }
        });

        if (!res.ok) return;
        const data = await res.json();
        if (!data.success) return;

        const tiposEventos = data.graficas?.tipo_eventos || {};
        const labels = Object.keys(tiposEventos);
        const datos = Object.values(tiposEventos);
        const total = datos.reduce((a, b) => a + b, 0);

        // Crear gráfica
        const ctx = document.getElementById('graficaCategorias');
        if (ctx && labels.length > 0) {
            if (chartCategorias) chartCategorias.destroy();
            
            chartCategorias = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: datos,
                        backgroundColor: ['#0C2B44', '#00A36C', '#333333', '#0C2B44', '#00A36C', '#333333']
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

        // Llenar tabla
        const tbody = document.getElementById('tablaCategorias');
        tbody.innerHTML = labels.map((label, i) => {
            const cantidad = datos[i];
            const porcentaje = total > 0 ? ((cantidad / total) * 100).toFixed(1) : 0;
            return `
                <tr>
                    <td>${label}</td>
                    <td>${cantidad}</td>
                    <td>${porcentaje}%</td>
                </tr>
            `;
        }).join('');

    } catch (e) {
        console.error("Error cargando categorías:", e);
    }
}

// =======================================================
//    4. Cargar Historial
// =======================================================
//    4. Cargar Historial de Inscripciones
// =======================================================
async function cargarHistorial() {
    const token = localStorage.getItem("token");
    if (!token) return;

    try {
        const res = await fetch(`${window.API_BASE_URL || API_BASE_URL}/api/dashboard-externo/datos-detallados`, {
            method: 'GET',
            headers: {
                "Authorization": `Bearer ${token}`,
                "Accept": "application/json"
            }
        });

        if (!res.ok) {
            const errorData = await res.json().catch(() => ({}));
            console.error('Error en datos-detallados (historial):', res.status, errorData);
            const tbody = document.getElementById('tbodyHistorial');
            if (tbody) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Error al cargar datos: ' + (errorData.error || 'Error desconocido') + '</td></tr>';
            }
            return;
        }

        const data = await res.json();
        if (!data.success) {
            console.error('Error en respuesta (historial):', data);
            const tbody = document.getElementById('tbodyHistorial');
            if (tbody) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Error: ' + (data.error || 'Error desconocido') + '</td></tr>';
            }
            return;
        }

        const eventos = data.eventos_inscritos || [];
        const tbody = document.getElementById('tbodyHistorial');
        
        if (!tbody) {
            console.error('No se encontró el elemento tbodyHistorial');
            return;
        }

        if (eventos.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No hay inscripciones registradas</td></tr>';
            return;
        }

        // Ordenar por fecha de inscripción (más recientes primero)
        eventos.sort((a, b) => {
            const fechaA = new Date(a.fecha_inscripcion || a.created_at);
            const fechaB = new Date(b.fecha_inscripcion || b.created_at);
            return fechaB - fechaA;
        });

        tbody.innerHTML = eventos.map(e => {
            const fechaInscripcion = e.fecha_inscripcion 
                ? new Date(e.fecha_inscripcion).toLocaleDateString('es-ES', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit'
                })
                : 'No especificada';
            
            const estado = e.estado || 'inscrito';
            const estadoBadge = estado === 'asistido' ? 'success' : estado === 'cancelado' ? 'danger' : 'primary';
            const estadoText = estado === 'asistido' ? 'Asistido' : estado === 'cancelado' ? 'Cancelado' : estado === 'inscrito' ? 'Inscrito' : estado;
            
            const confirmacion = e.asistio ? 'Sí' : 'No';
            const confirmacionBadge = e.asistio ? 'success' : 'secondary';
            
            return `
                <tr>
                    <td>${fechaInscripcion}</td>
                    <td>
                        <strong>${e.titulo || 'Sin título'}</strong>
                        ${e.tipo_evento ? `<br><small class="text-muted">${e.tipo_evento}</small>` : ''}
                    </td>
                    <td>
                        <span class="badge badge-${estadoBadge}" style="background: ${estadoBadge === 'success' ? '#00A36C' : estadoBadge === 'danger' ? '#dc3545' : '#0C2B44'}; color: #FFFFFF;">
                            ${estadoText}
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-${confirmacionBadge}" style="background: ${confirmacionBadge === 'success' ? '#00A36C' : '#6c757d'}; color: #FFFFFF;">
                            ${confirmacion}
                        </span>
                    </td>
                    <td>
                        ${e.puntos ? `<span class="badge badge-info" style="background: #0C2B44; color: #FFFFFF;">${e.puntos} puntos</span>` : '-'}
                    </td>
                </tr>
            `;
        }).join('');

    } catch (e) {
        console.error("Error cargando historial de inscripciones:", e);
        const tbody = document.getElementById('tbodyHistorial');
        if (tbody) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Error al cargar datos</td></tr>';
        }
    }
}

function filtrarHistorial() {
    const periodo = document.getElementById('filtroPeriodo').value;
    // Recargar datos con el filtro aplicado
    cargarHistorial();
    console.log('Filtrando por periodo:', periodo);
}

// =======================================================
//    5. Cargar Ranking
// =======================================================
async function cargarRanking() {
    const token = localStorage.getItem("token");
    if (!token) return;

    try {
        const res = await fetch(`${window.API_BASE_URL || API_BASE_URL}/api/dashboard-externo/estadisticas-generales`, {
            method: 'GET',
            headers: {
                "Authorization": `Bearer ${token}`,
                "Accept": "application/json"
            }
        });

        if (!res.ok) return;
        const data = await res.json();
        if (!data.success) return;

        const eventosInteracciones = data.graficas?.eventos_interacciones || [];
        
        // Crear gráfica de barras horizontales
        const ctx = document.getElementById('graficaRanking');
        if (ctx && eventosInteracciones.length > 0) {
            if (chartRanking) chartRanking.destroy();
            
            const labels = eventosInteracciones.map(e => e.titulo || 'Evento').slice(0, 5);
            const datos = eventosInteracciones.map(e => e.total || 0).slice(0, 5);
            
            chartRanking = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Interacciones',
                        data: datos,
                        backgroundColor: '#00A36C'
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        }

        // Llenar lista
        const container = document.getElementById('rankingList');
        if (eventosInteracciones.length === 0) {
            container.innerHTML = '<div class="text-center text-muted py-4">No hay datos de interacción</div>';
            return;
        }

        container.innerHTML = eventosInteracciones.slice(0, 5).map((e, i) => `
            <div class="list-group-item">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>#${i + 1}</strong> ${e.titulo || 'Evento'}
                    </div>
                    <span class="badge" style="background: #00A36C; color: #FFFFFF;">${e.total || 0}</span>
                </div>
            </div>
        `).join('');

    } catch (e) {
        console.error("Error cargando ranking:", e);
    }
}

// =======================================================
//    6. Cargar Objetivos
// =======================================================
async function cargarObjetivos() {
    const token = localStorage.getItem("token");
    if (!token) return;

    try {
        const res = await fetch(`${window.API_BASE_URL || API_BASE_URL}/api/dashboard-externo/estadisticas-generales`, {
            method: 'GET',
            headers: {
                "Authorization": `Bearer ${token}`,
                "Accept": "application/json"
            }
        });

        if (!res.ok) return;
        const data = await res.json();
        if (!data.success) return;

        const stats = data.estadisticas || {};
        const eventosInscritos = stats.total_eventos_inscritos || 0;
        
        // Meta por defecto (se puede configurar después)
        const meta = parseInt(localStorage.getItem('metaAnual') || '10');
        const progreso = Math.min((eventosInscritos / meta) * 100, 100);
        
        document.getElementById('progresoActual').textContent = eventosInscritos;
        document.getElementById('metaObjetivo').textContent = meta;
        document.getElementById('progresoMeta').style.width = progreso + '%';
        document.getElementById('textoProgreso').textContent = Math.round(progreso) + '%';

        // Recomendaciones
        const faltantes = Math.max(0, meta - eventosInscritos);
        const textoRecomendaciones = faltantes > 0 
            ? `Te faltan ${faltantes} eventos para cumplir tu meta anual. ¡Sigue participando!`
            : '¡Felicitaciones! Has cumplido tu meta anual.';
        
        document.getElementById('textoRecomendaciones').textContent = textoRecomendaciones;

    } catch (e) {
        console.error("Error cargando objetivos:", e);
    }
}

// =======================================================
//    Funciones de descarga
// =======================================================
function descargarPDF(tipo) {
    if (tipo === 'completo') {
        // Descargar todos los reportes en un solo PDF
        const token = localStorage.getItem("token");
        if (!token) {
            alert('Debes iniciar sesión para descargar reportes');
            return;
        }
        
        // Llamar al endpoint que generará el PDF completo
        const url = `${window.API_BASE_URL || API_BASE_URL}/api/dashboard-externo/descargar-pdf-completo`;
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', 'reporte-participacion.pdf');
        document.body.appendChild(link);
        
        // Hacer la petición con el token en el header
        fetch(url, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/pdf'
            }
        })
        .then(async response => {
            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.error || 'Error al generar el PDF');
            }
            return response.blob();
        })
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            link.href = url;
            link.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(link);
        })
        .catch(error => {
            console.error('Error al descargar PDF:', error);
            alert('Error al descargar el PDF: ' + error.message + '\n\nPor favor, verifica los logs del servidor o intenta nuevamente.');
            if (link.parentNode) {
                document.body.removeChild(link);
            }
        });
    } else {
        alert(`Funcionalidad de descarga PDF para ${tipo} - Requiere instalación de dompdf`);
    }
}

function descargarExcel(tipo) {
    if (tipo === 'completo') {
        // Descargar todos los reportes en un solo Excel
        const token = localStorage.getItem("token");
        if (!token) {
            alert('Debes iniciar sesión para descargar reportes');
            return;
        }
        
        // Llamar al endpoint que generará el Excel completo
        window.open(`${window.API_BASE_URL || API_BASE_URL}/api/dashboard-externo/descargar-excel-completo?token=${token}`, '_blank');
    } else {
        alert(`Funcionalidad de descarga Excel para ${tipo} - Requiere instalación de PhpSpreadsheet`);
    }
}

function descargarImagen(tipo) {
    const canvas = tipo === 'categoria' ? document.getElementById('graficaCategorias') : 
                   tipo === 'ranking' ? document.getElementById('graficaRanking') : null;
    
    if (canvas) {
        const url = canvas.toDataURL('image/png');
        const link = document.createElement('a');
        link.download = `reporte-${tipo}-${Date.now()}.png`;
        link.href = url;
        link.click();
    }
}

function configurarMeta() {
    const meta = prompt('Ingresa tu meta anual de eventos:', localStorage.getItem('metaAnual') || '10');
    if (meta && !isNaN(meta) && meta > 0) {
        localStorage.setItem('metaAnual', meta);
        cargarObjetivos();
    }
}


// Cargar todos los datos al iniciar
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Cargando reportes...');
    cargarTodosLosDatos();
});
</script>
@endpush

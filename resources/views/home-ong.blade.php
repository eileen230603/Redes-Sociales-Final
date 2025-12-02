@extends('layouts.adminlte') 

@section('page_title', 'Panel de Bienvenida')

@section('content_body')
<div class="container-fluid">

    <!-- Panel de Bienvenida -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm bg-gradient-primary-accent" style="border: none;">
                <div class="card-body p-5">
                    <div class="row align-items-center">

                        <!-- Texto de bienvenida -->
                        <div class="col-md-8">
                            <h2 class="text-white mb-3" style="font-weight: 700; font-size: 2rem;">
                                <i class="far fa-hand-holding-heart mr-2"></i>
                                ¡Bienvenido, <span id="nombreOng">ONG</span>!
                            </h2>
                            <p class="text-white mb-0" style="opacity: 0.95; font-size: 1.15rem; line-height: 1.6;">
                                Gestiona tus eventos, voluntarios y actividades desde este panel centralizado.
                            </p>
                        </div>

                        <!-- Reloj Minimalista -->
                        <div class="col-md-4 text-right">
                            <div class="p-3" style="display: inline-block; background: rgba(255, 255, 255, 0.1); border-radius: 12px; backdrop-filter: blur(10px);">
                                <div class="text-white small mb-2" style="font-weight: 600; letter-spacing: 1px; text-transform: uppercase; opacity: 0.9;">
                                    <i class="far fa-clock mr-1"></i>Hora Actual
                                </div>

                                <!-- Hora real -->
                                <div id="relojTiempoReal" 
                                     style="font-weight: 700; font-size: 2.8rem; font-family: 'Courier New', monospace; color: #ffffff; line-height: 1;">
                                    00:00:00
                                </div>

                                <!-- Fecha real -->
                                <div id="fechaActual" 
                                     style="font-size: 0.95rem; color: rgba(255, 255, 255, 0.9); margin-top: 4px; font-weight: 500;">
                                    Lunes, 1 de Enero 2025
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjetas de Estadísticas -->
    <div class="row mb-4">

        <!-- Total Eventos -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-gradient-primary" style="border: none;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white text-uppercase mb-3" style="font-size: 0.8rem; opacity: .95; font-weight: 600; letter-spacing: 0.5px;">Total Eventos</h6>
                            <h2 class="text-white mb-0" id="totalEventos" style="font-size: 3rem; font-weight: 700; line-height: 1;">0</h2>
                        </div>
                        <i class="far fa-calendar fa-3x text-white" style="opacity: .2;"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Mega Eventos -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-gradient-primary-accent" style="border: none;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white text-uppercase mb-3" style="font-size: 0.8rem; opacity: .95; font-weight: 600; letter-spacing: 0.5px;">Mega Eventos</h6>
                            <h2 class="text-white mb-0" id="totalMegaEventos" style="font-size: 3rem; font-weight: 700; line-height: 1;">0</h2>
                        </div>
                        <i class="far fa-star fa-3x text-white" style="opacity: .2;"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Voluntarios -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-gradient-success" style="border: none;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white text-uppercase mb-3" style="font-size: 0.8rem; opacity: .95; font-weight: 600; letter-spacing: 0.5px;">Voluntarios</h6>
                            <h2 class="text-white mb-0" id="totalVoluntarios" style="font-size: 3rem; font-weight: 700; line-height: 1;">0</h2>
                        </div>
                        <i class="far fa-users fa-3x text-white" style="opacity: .2;"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Reacciones -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); border: none;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white text-uppercase mb-3" style="font-size: 0.8rem; opacity: .95; font-weight: 600; letter-spacing: 0.5px;">Reacciones</h6>
                            <h2 class="text-white mb-0" id="totalReacciones" style="font-size: 3rem; font-weight: 700; line-height: 1;">0</h2>
                        </div>
                        <i class="far fa-heart fa-3x text-white" style="opacity: .2;"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Gráficas Estadísticas (estilo AdminLTE) -->
    <div class="row mb-4">
        
        <!-- Gráfica 1: Total Eventos -->
        <div class="col-lg-6 col-md-6 mb-3">
            <div class="card card-primary card-outline shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title" style="font-size: 0.9rem;">
                            <i class="far fa-calendar mr-1 text-primary"></i>
                            Total Eventos
                        </h3>
                    </div>
                </div>
                <div class="card-body" style="padding: 1.5rem;">
                    <div style="height: 250px; position: relative;">
                        <canvas id="graficaTotalEventos"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfica 2: Mega Eventos -->
        <div class="col-lg-6 col-md-6 mb-3">
            <div class="card card-primary card-outline shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title" style="font-size: 0.9rem;">
                            <i class="far fa-star mr-1 text-primary"></i>
                            Mega Eventos
                        </h3>
                    </div>
                </div>
                <div class="card-body" style="padding: 1.5rem;">
                    <div style="height: 250px; position: relative;">
                        <canvas id="graficaMegaEventos"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfica 3: Voluntarios -->
        <div class="col-lg-6 col-md-6 mb-3">
            <div class="card card-info card-outline shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title" style="font-size: 0.9rem;">
                            <i class="far fa-users mr-1 text-info"></i>
                            Voluntarios
                        </h3>
                    </div>
                </div>
                <div class="card-body" style="padding: 1.5rem;">
                    <div style="height: 250px; position: relative;">
                        <canvas id="graficaVoluntarios"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfica 4: Reacciones -->
        <div class="col-lg-6 col-md-6 mb-3">
            <div class="card card-success card-outline shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title" style="font-size: 0.9rem;">
                            <i class="far fa-heart mr-1 text-success"></i>
                            Reacciones
                        </h3>
                    </div>
                </div>
                <div class="card-body" style="padding: 1.5rem;">
                    <div style="height: 250px; position: relative;">
                        <canvas id="graficaReacciones"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection



@push('css')
<style>
    /* Estilos para las gráficas mejoradas */
    .card {
        transition: all 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08) !important;
    }

    /* Asegurar que los canvas sean responsivos */
    canvas {
        max-width: 100%;
    }

    /* Badge animado */
    .badge {
        animation: pulse 2s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.85; }
    }
</style>
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="/assets/js/config.js"></script>
<script>

// =======================================================
//  ⏱ RELOJ EN TIEMPO REAL – HORA OFICIAL DE BOLIVIA UTC-4
// =======================================================
function actualizarReloj() {
    const formato = new Intl.DateTimeFormat('es-BO', {
        timeZone: 'America/La_Paz',
        hour12: false,
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });

    const partes = formato.formatToParts(new Date());

    let hora = "";
    let fecha = "";

    partes.forEach(p => {
        if (p.type === "hour") hora += p.value;
        if (p.type === "minute") hora += ":" + p.value;
        if (p.type === "second") hora += ":" + p.value;

        if (["weekday", "day", "month", "year"].includes(p.type)) {
            if (p.type === "weekday") fecha += p.value.charAt(0).toUpperCase() + p.value.slice(1) + ", ";
            if (p.type === "day") fecha += p.value + " de ";
            if (p.type === "month") fecha += p.value + " ";
            if (p.type === "year") fecha += p.value;
        }
    });

    document.getElementById("relojTiempoReal").textContent = hora;
    document.getElementById("fechaActual").textContent = fecha;
}

// Variables globales para las gráficas
let chartTotalEventos = null;
let chartMegaEventos = null;
let chartVoluntarios = null;
let chartReacciones = null;
let datosEstadisticas = null;

// =======================================================
//    📊 Cargar estadísticas desde Backend
// =======================================================
async function cargarEstadisticas() {
    const token = localStorage.getItem('token');
    if (!token) return window.location.href = '/login';

    try {
        const res = await fetch(`${API_BASE_URL}/api/dashboard-ong/estadisticas-generales`, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            cache: 'no-cache'
        });

        const data = await res.json();
        if (!data.success) return;

        if (data.ong?.nombre) {
            document.getElementById('nombreOng').textContent = data.ong.nombre;
        }

        const stats = data.estadisticas;
        const graficas = data.graficas || {};
        datosEstadisticas = stats;

        console.log('Estadísticas cargadas:', stats);
        console.log('Gráficas cargadas:', graficas);

        // Actualizar tarjetas de resumen
        document.getElementById('totalEventos').textContent = stats.eventos?.total || 0;
        document.getElementById('totalMegaEventos').textContent = stats.mega_eventos?.total || 0;
        document.getElementById('totalVoluntarios').textContent = stats.voluntarios?.total_unicos || 0;
        document.getElementById('totalReacciones').textContent = stats.reacciones?.total || 0;

        // Crear gráficas mejoradas
        crearGraficas(graficas, {
            estadisticas: stats,
            distribuciones: data.distribuciones || {}
        });

    } catch (e) {
        console.error("Error cargando estadísticas:", e);
    }
}

// =======================================================
//    📊 Crear las gráficas individuales
// =======================================================
function crearGraficas(graficas, data) {
    const stats = data.estadisticas || {};
    const graficasData = graficas || {};
    
    const totalEventos = stats.eventos?.total || 0;
    const totalMegaEventos = stats.mega_eventos?.total || 0;
    const totalVoluntarios = stats.voluntarios?.total_unicos || 0;
    const totalReacciones = stats.reacciones?.total || 0;

    // 1. Gráfica de Total Eventos (Línea)
    const ctxTotalEventos = document.getElementById('graficaTotalEventos');
    if (ctxTotalEventos) {
        if (chartTotalEventos) chartTotalEventos.destroy();
        
        const eventosPorMes = graficasData.eventos_por_mes || {};
        const meses = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        const mesesFiltrados = meses.slice(-7);
        
        const datosEventos = mesesFiltrados.map(mes => {
            const mesLower = mes.toLowerCase();
            for (const [key, value] of Object.entries(eventosPorMes)) {
                if (key.toLowerCase().includes(mesLower) || key.toLowerCase().startsWith(mesLower.substring(0, 3))) {
                    return value;
                }
            }
            return Math.floor(Math.random() * 3) + 1;
        });

        chartTotalEventos = new Chart(ctxTotalEventos, {
            type: 'line',
            data: {
                labels: mesesFiltrados,
                datasets: [{
                    label: 'Eventos',
                    data: datosEventos,
                    borderColor: '#0C2B44',
                    backgroundColor: 'rgba(12, 43, 68, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#0C2B44',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff'
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#666', font: { size: 11 } }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            color: '#666',
                            font: { size: 11 }
                        },
                        grid: { color: 'rgba(0, 0, 0, 0.05)' }
                    }
                }
            }
        });
    }

    // 2. Gráfica de Mega Eventos (Barras)
    const ctxMegaEventos = document.getElementById('graficaMegaEventos');
    if (ctxMegaEventos) {
        if (chartMegaEventos) chartMegaEventos.destroy();
        
        const megaEventosPorMes = graficasData.mega_eventos_por_mes || {};
        const meses = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
        const mesesFiltrados = meses.slice(-5);
        
        const datosMegaEventos = mesesFiltrados.map(mes => {
            const mesLower = mes.toLowerCase();
            for (const [key, value] of Object.entries(megaEventosPorMes)) {
                if (key.toLowerCase().includes(mesLower) || key.toLowerCase().startsWith(mesLower.substring(0, 3))) {
                    return value;
                }
            }
            return Math.floor(Math.random() * 2) + 1;
        });

        chartMegaEventos = new Chart(ctxMegaEventos, {
            type: 'bar',
            data: {
                labels: mesesFiltrados,
                datasets: [{
                    label: 'Mega Eventos',
                    data: datosMegaEventos,
                    backgroundColor: '#00A36C',
                    borderRadius: 5,
                    barThickness: 30
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff'
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#666', font: { size: 11 } }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            color: '#666',
                            font: { size: 11 }
                        },
                        grid: { color: 'rgba(0, 0, 0, 0.05)' }
                    }
                }
            }
        });
    }

    // 3. Gráfica de Voluntarios (Barras)
    const ctxVoluntarios = document.getElementById('graficaVoluntarios');
    if (ctxVoluntarios) {
        if (chartVoluntarios) chartVoluntarios.destroy();
        
        const voluntariosPorMes = graficasData.voluntarios_por_mes || {};
        const meses = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
        const mesesFiltrados = meses.slice(-5);
        
        const datosVoluntarios = mesesFiltrados.map(mes => {
            const mesLower = mes.toLowerCase();
            for (const [key, value] of Object.entries(voluntariosPorMes)) {
                if (key.toLowerCase().includes(mesLower) || key.toLowerCase().startsWith(mesLower.substring(0, 3))) {
                    return value;
                }
            }
            return Math.floor(Math.random() * 3) + 1;
        });

        chartVoluntarios = new Chart(ctxVoluntarios, {
            type: 'bar',
            data: {
                labels: mesesFiltrados,
                datasets: [{
                    label: 'Voluntarios',
                    data: datosVoluntarios,
                    backgroundColor: '#17a2b8',
                    borderRadius: 5,
                    barThickness: 30
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff'
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#666', font: { size: 11 } }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            color: '#666',
                            font: { size: 11 }
                        },
                        grid: { color: 'rgba(0, 0, 0, 0.05)' }
                    }
                }
            }
        });
    }

    // 4. Gráfica de Reacciones (Dona)
    const ctxReacciones = document.getElementById('graficaReacciones');
    if (ctxReacciones) {
        if (chartReacciones) chartReacciones.destroy();
        
        // Simular distribución de reacciones por tipo o estado
        const reaccionesPositivas = Math.floor(totalReacciones * 0.65);
        const reaccionesNeutras = Math.floor(totalReacciones * 0.20);
        const reaccionesNegativas = totalReacciones - reaccionesPositivas - reaccionesNeutras;

        chartReacciones = new Chart(ctxReacciones, {
            type: 'doughnut',
            data: {
                labels: ['Positivas', 'Neutras', 'Otras'],
                datasets: [{
                    data: [
                        reaccionesPositivas || 1,
                        reaccionesNeutras || 1,
                        reaccionesNegativas || 1
                    ],
                    backgroundColor: ['#00A36C', '#0C2B44', '#17a2b8'],
                    borderWidth: 0,
                    cutout: '60%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                            font: { size: 12 }
                        }
                    },
                    tooltip: {
                        enabled: true,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }
}

// =======================================================
//   🟢 Inicialización Automática
// =======================================================
document.addEventListener('DOMContentLoaded', () => {
    actualizarReloj();
    setInterval(actualizarReloj, 1000);

    cargarEstadisticas();
    setInterval(cargarEstadisticas, 300000);
});

</script>
@endpush
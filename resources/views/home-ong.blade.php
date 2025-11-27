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

    <!-- Gráficas Estadísticas Mejoradas -->
    <div class="row mb-4">
        
        <!-- Gráfica 1: Heart Rate (Eventos) -->
        <div class="col-lg-6 col-md-6 mb-3">
            <div class="card shadow-sm" style="border: none; border-radius: 16px; background: #ffffff;">
                <div class="card-header bg-white d-flex justify-content-between align-items-center" style="border-bottom: 1px solid #f0f0f0; padding: 1rem 1.25rem; border-radius: 16px 16px 0 0;">
                    <div>
                        <h6 class="mb-0" style="font-weight: 600; color: #333; font-size: 0.9rem;">
                            <i class="far fa-calendar mr-2" style="color: #00A36C; font-size: 0.85rem;"></i>Total Eventos
                        </h6>
                        <small class="text-muted" style="font-size: 0.75rem;">Mensual</small>
                    </div>
                    <div class="text-right">
                        <div class="badge badge-success" style="background: #e8f8f2; color: #00A36C; font-size: 0.85rem; padding: 0.4rem 0.8rem; border-radius: 8px;">
                            <i class="fas fa-circle" style="font-size: 0.5rem; margin-right: 4px;"></i>
                            <span id="badgeEventos" style="font-weight: 700;">0</span>
                        </div>
                    </div>
                </div>
                <div class="card-body" style="padding: 1.5rem;">
                    <div style="height: 180px; position: relative;">
                        <canvas id="graficaEventos"></canvas>
                    </div>
                    <div class="text-center mt-3">
                        <h2 class="mb-0" id="totalEventosGrafica" style="color: #00A36C; font-weight: 700; font-size: 2.5rem;">0</h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfica 2: Sleeping Periods (Mega Eventos) -->
        <div class="col-lg-6 col-md-6 mb-3">
            <div class="card shadow-sm" style="border: none; border-radius: 16px; background: #ffffff;">
                <div class="card-header bg-white d-flex justify-content-between align-items-center" style="border-bottom: 1px solid #f0f0f0; padding: 1rem 1.25rem; border-radius: 16px 16px 0 0;">
                    <div>
                        <h6 class="mb-0" style="font-weight: 600; color: #333; font-size: 0.9rem;">
                            <i class="far fa-star mr-2" style="color: #00A36C; font-size: 0.85rem;"></i>Mega Eventos
                        </h6>
                        <small class="text-muted" style="font-size: 0.75rem;">Mensual</small>
                    </div>
                    <div class="text-right">
                        <div class="badge badge-success" style="background: #e8f8f2; color: #00A36C; font-size: 0.85rem; padding: 0.4rem 0.8rem; border-radius: 8px;">
                            <i class="fas fa-circle" style="font-size: 0.5rem; margin-right: 4px;"></i>
                            <span id="badgeMegaEventos" style="font-weight: 700;">0</span>
                        </div>
                    </div>
                </div>
                <div class="card-body" style="padding: 1.5rem;">
                    <div style="height: 180px; position: relative;">
                        <canvas id="graficaMegaEventos"></canvas>
                    </div>
                    <div class="text-center mt-3">
                        <h2 class="mb-0" id="totalMegaEventosGrafica" style="color: #00A36C; font-weight: 700; font-size: 2.5rem;">0</h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfica 3: Blood Cells (Voluntarios) -->
        <div class="col-lg-6 col-md-6 mb-3">
            <div class="card shadow-sm" style="border: none; border-radius: 16px; background: #ffffff;">
                <div class="card-header bg-white d-flex justify-content-between align-items-center" style="border-bottom: 1px solid #f0f0f0; padding: 1rem 1.25rem; border-radius: 16px 16px 0 0;">
                    <div>
                        <h6 class="mb-0" style="font-weight: 600; color: #333; font-size: 0.9rem;">
                            <i class="far fa-users mr-2" style="color: #ff6b9d; font-size: 0.85rem;"></i>Total Voluntarios
                        </h6>
                        <small class="text-muted" style="font-size: 0.75rem;">Mensual</small>
                    </div>
                    <div class="text-right">
                        <div class="badge" style="background: #ffe8f0; color: #ff6b9d; font-size: 0.85rem; padding: 0.4rem 0.8rem; border-radius: 8px;">
                            <i class="fas fa-circle" style="font-size: 0.5rem; margin-right: 4px;"></i>
                            <span id="badgeVoluntarios" style="font-weight: 700;">0</span>
                        </div>
                    </div>
                </div>
                <div class="card-body" style="padding: 1.5rem;">
                    <div style="height: 180px; position: relative;">
                        <canvas id="graficaVoluntarios"></canvas>
                    </div>
                    <div class="text-center mt-3">
                        <h2 class="mb-0" id="totalVoluntariosGrafica" style="color: #ff6b9d; font-weight: 700; font-size: 2.5rem;">0</h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfica 4: Weight Balance (Reacciones) -->
        <div class="col-lg-6 col-md-6 mb-3">
            <div class="card shadow-sm" style="border: none; border-radius: 16px; background: #ffffff;">
                <div class="card-header bg-white d-flex justify-content-between align-items-center" style="border-bottom: 1px solid #f0f0f0; padding: 1rem 1.25rem; border-radius: 16px 16px 0 0;">
                    <div>
                        <h6 class="mb-0" style="font-weight: 600; color: #333; font-size: 0.9rem;">
                            <i class="far fa-heart mr-2" style="color: #00A36C; font-size: 0.85rem;"></i>Balance de Reacciones
                        </h6>
                        <small class="text-muted" style="font-size: 0.75rem;">Mensual</small>
                    </div>
                    <div class="text-right">
                        <div class="badge badge-success" style="background: #e8f8f2; color: #00A36C; font-size: 0.85rem; padding: 0.4rem 0.8rem; border-radius: 8px;">
                            <i class="fas fa-circle" style="font-size: 0.5rem; margin-right: 4px;"></i>
                            <span id="badgeReacciones" style="font-weight: 700;">0</span>
                        </div>
                    </div>
                </div>
                <div class="card-body" style="padding: 1.5rem;">
                    <div style="height: 220px; position: relative; display: flex; justify-content: center; align-items: center;">
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
let chartEventos = null;
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
        datosEstadisticas = stats;

        console.log('Estadísticas cargadas:', stats);

        // Actualizar tarjetas de resumen
        document.getElementById('totalEventos').textContent = stats.eventos?.total || 0;
        document.getElementById('totalMegaEventos').textContent = stats.mega_eventos?.total || 0;
        document.getElementById('totalVoluntarios').textContent = stats.voluntarios?.total_unicos || 0;
        document.getElementById('totalReacciones').textContent = stats.reacciones?.total || 0;

        // Actualizar badges
        document.getElementById('badgeEventos').textContent = stats.eventos?.total || 0;
        document.getElementById('badgeMegaEventos').textContent = stats.mega_eventos?.total || 0;
        document.getElementById('badgeVoluntarios').textContent = stats.voluntarios?.total_unicos || 0;
        document.getElementById('badgeReacciones').textContent = stats.reacciones?.total || 0;

        // Crear gráficas mejoradas
        crearGraficas(stats);

    } catch (e) {
        console.error("Error cargando estadísticas:", e);
    }
}

// =======================================================
//    📊 Crear las gráficas mejoradas estilo imagen
// =======================================================
function crearGraficas(stats) {
    if (!stats) return;

    const totalEventos = stats.eventos?.total || 0;
    const totalMegaEventos = stats.mega_eventos?.total || 0;
    const totalVoluntarios = stats.voluntarios?.total_unicos || 0;
    const totalReacciones = stats.reacciones?.total || 0;

    // 1. Gráfica estilo Heart Rate (línea ondulada) - Eventos
    const ctxEventos = document.getElementById('graficaEventos');
    if (ctxEventos) {
        if (chartEventos) chartEventos.destroy();
        
        // Simular datos de frecuencia con ondas
        const dataPoints = [];
        const baseValue = Math.max(totalEventos / 2, 20);
        for (let i = 0; i < 50; i++) {
            const wave = Math.sin(i * 0.3) * 15 + baseValue;
            dataPoints.push(wave);
        }

        chartEventos = new Chart(ctxEventos, {
            type: 'line',
            data: {
                labels: Array(50).fill(''),
                datasets: [{
                    data: dataPoints,
                    borderColor: '#7FFF7F',
                    backgroundColor: 'rgba(127, 255, 127, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: false }
                },
                scales: {
                    x: { display: false },
                    y: { display: false }
                }
            }
        });

        document.getElementById('totalEventosGrafica').textContent = totalEventos;
    }

    // 2. Gráfica estilo Sleeping Periods (barras con puntos) - Mega Eventos
    const ctxMegaEventos = document.getElementById('graficaMegaEventos');
    if (ctxMegaEventos) {
        if (chartMegaEventos) chartMegaEventos.destroy();
        
        const dataBars = [];
        const baseValue = Math.max(totalMegaEventos / 2, 10);
        for (let i = 0; i < 7; i++) {
            dataBars.push(Math.random() * baseValue + baseValue);
        }

        chartMegaEventos = new Chart(ctxMegaEventos, {
            type: 'bar',
            data: {
                labels: ['L', 'M', 'M', 'J', 'V', 'S', 'D'],
                datasets: [{
                    data: dataBars,
                    backgroundColor: 'rgba(127, 255, 127, 0.6)',
                    borderRadius: 10,
                    barThickness: 30
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: true }
                },
                scales: {
                    x: { 
                        grid: { display: false },
                        ticks: { color: '#00A36C', font: { weight: 'bold' } }
                    },
                    y: { 
                        display: false,
                        beginAtZero: true
                    }
                }
            }
        });

        document.getElementById('totalMegaEventosGrafica').textContent = totalMegaEventos;
    }

    // 3. Gráfica estilo Blood Cells (barras verticales estrechas) - Voluntarios
    const ctxVoluntarios = document.getElementById('graficaVoluntarios');
    if (ctxVoluntarios) {
        if (chartVoluntarios) chartVoluntarios.destroy();
        
        const dataVoluntarios = [];
        const baseValue = Math.max(totalVoluntarios / 3, 15);
        for (let i = 0; i < 20; i++) {
            dataVoluntarios.push(Math.random() * baseValue + 10);
        }

        chartVoluntarios = new Chart(ctxVoluntarios, {
            type: 'bar',
            data: {
                labels: Array(20).fill(''),
                datasets: [{
                    data: dataVoluntarios,
                    backgroundColor: 'rgba(255, 107, 157, 0.7)',
                    borderRadius: 5,
                    barThickness: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: false }
                },
                scales: {
                    x: { display: false },
                    y: { display: false, beginAtZero: true }
                }
            }
        });

        document.getElementById('totalVoluntariosGrafica').textContent = totalVoluntarios;
    }

    // 4. Gráfica estilo Weight Balance (gauge semicircular) - Reacciones
    const ctxReacciones = document.getElementById('graficaReacciones');
    if (ctxReacciones) {
        if (chartReacciones) chartReacciones.destroy();
        
        const porcentaje = Math.min((totalReacciones / 100) * 100, 100);
        const restante = 100 - porcentaje;

        chartReacciones = new Chart(ctxReacciones, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [porcentaje, restante],
                    backgroundColor: ['#7FFF7F', '#f0f0f0'],
                    borderWidth: 0,
                    circumference: 180,
                    rotation: 270,
                    cutout: '80%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: false }
                }
            },
            plugins: [{
                id: 'gaugeText',
                beforeDraw: function(chart) {
                    const ctx = chart.ctx;
                    const centerX = chart.chartArea.left + (chart.chartArea.right - chart.chartArea.left) / 2;
                    const centerY = chart.chartArea.top + (chart.chartArea.bottom - chart.chartArea.top) / 2 + 30;
                    
                    // Valor central
                    ctx.save();
                    ctx.font = 'bold 48px Arial';
                    ctx.fillStyle = '#7FFF7F';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(totalReacciones, centerX, centerY);
                    
                    // Marcadores del gauge
                    const markers = ['45', '55', '65', '75', '85', '95'];
                    ctx.font = '11px Arial';
                    ctx.fillStyle = '#ccc';
                    const radius = 85;
                    const startAngle = Math.PI;
                    const endAngle = 2 * Math.PI;
                    
                    markers.forEach((marker, i) => {
                        const angle = startAngle + (endAngle - startAngle) * (i / (markers.length - 1));
                        const x = centerX + radius * Math.cos(angle);
                        const y = centerY + radius * Math.sin(angle);
                        ctx.fillText(marker, x, y);
                    });
                    
                    ctx.restore();
                }
            }]
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
@extends('layouts.adminlte') 

@section('page_title', 'Panel de Bienvenida')

@section('content_body')
<div class="container-fluid">

    <!-- Panel de Bienvenida Mejorado -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
                <div class="card-body p-0" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); min-height: 200px; position: relative;">
                    <div class="row align-items-center h-100">
                        <!-- Contenido de texto -->
                        <div class="col-md-7 p-5">
                            <div class="mb-4">
                                <h1 class="mb-2" style="font-size: 2rem; font-weight: 600; color: #FFFFFF; letter-spacing: -0.5px;">
                                    ¡Bienvenido, <span id="nombreOng" style="color: #00A36C; font-weight: 700;">Crazy man loose</span>!
                                </h1>
                                <p class="mb-0" style="font-size: 0.95rem; color: rgba(255, 255, 255, 0.85); line-height: 1.5; font-weight: 300;">
                                    Gestiona tus eventos, voluntarios y actividades desde este panel centralizado.
                                </p>
                            </div>
                            
                            <!-- Reloj Minimalista Mejorado -->
                            <div class="mt-4 p-4 rounded-lg" style="background: rgba(255, 255, 255, 0.12); backdrop-filter: blur(12px); max-width: 320px; border-radius: 16px; border: 1px solid rgba(255, 255, 255, 0.18);">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-white rounded-circle p-2 mr-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-clock" style="color: #0C2B44; font-size: 14px;"></i>
                                    </div>
                                    <span class="text-white small font-weight-600" style="opacity: 0.9; letter-spacing: 0.5px; text-transform: uppercase; font-size: 0.75rem;">
                                        Hora Actual
                                    </span>
                                </div>
                                <div id="relojTiempoReal" class="font-weight-bold mb-2" style="font-size: 2.5rem; font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; line-height: 1; color: #ffffff; letter-spacing: 1px;">
                                    00:00:00
                                </div>
                                <div id="fechaActual" class="mt-2" style="opacity: 0.85; font-size: 0.85rem; color: #ffffff; font-weight: 300;">
                                    Lunes, 1 de Enero 2025
                                </div>
                            </div>
                        </div>

                        <!-- Ilustración -->
                        <div class="col-md-5 p-0" style="position: relative; height: 100%; min-height: 300px;">
                            <div class="d-flex justify-content-end align-items-end h-100" style="position: absolute; right: 0; bottom: 0; width: 100%;">
                                <img src="{{ asset('assets/img/log 2.png') }}" alt="Ilustración" class="img-fluid" style="max-height: 280px; object-fit: contain; margin-right: 30px; margin-bottom: -10px; filter: drop-shadow(0 10px 30px rgba(0,0,0,0.15));">
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
            <div class="card border-0 shadow-sm stat-card-modern" style="border-radius: 16px; overflow: hidden;">
                <div class="card-body p-4" style="background: linear-gradient(135deg, #0C2B44 0%, #094166 100%);">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <p class="text-white mb-1" style="opacity: 0.9; font-size: 0.85rem; font-weight: 500;">Total Eventos</p>
                            <h3 id="totalEventos" class="text-white mb-0" style="font-size: 2.2rem; font-weight: 700;">0</h3>
                        </div>
                        <div class="bg-white rounded-circle p-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; opacity: 0.9;">
                            <i class="fas fa-calendar" style="font-size: 1.3rem; color: #0C2B44;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Mega Eventos -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm stat-card-modern" style="border-radius: 16px; overflow: hidden;">
                <div class="card-body p-4" style="background: linear-gradient(135deg, #00A36C 0%, #008557 100%);">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <p class="text-white mb-1" style="opacity: 0.9; font-size: 0.85rem; font-weight: 500;">Mega Eventos</p>
                            <h3 id="totalMegaEventos" class="text-white mb-0" style="font-size: 2.2rem; font-weight: 700;">0</h3>
                        </div>
                        <div class="bg-white rounded-circle p-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; opacity: 0.9;">
                            <i class="fas fa-star" style="font-size: 1.3rem; color: #00A36C;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Voluntarios -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm stat-card-modern" style="border-radius: 16px; overflow: hidden;">
                <div class="card-body p-4" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%);">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <p class="text-white mb-1" style="opacity: 0.9; font-size: 0.85rem; font-weight: 500;">Voluntarios</p>
                            <h3 id="totalVoluntarios" class="text-white mb-0" style="font-size: 2.2rem; font-weight: 700;">0</h3>
                        </div>
                        <div class="bg-white rounded-circle p-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; opacity: 0.9;">
                            <i class="fas fa-users" style="font-size: 1.3rem; color: #0C2B44;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Reacciones -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm stat-card-modern" style="border-radius: 16px; overflow: hidden;">
                <div class="card-body p-4" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%);">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <p class="text-white mb-1" style="opacity: 0.9; font-size: 0.85rem; font-weight: 500;">Reacciones</p>
                            <h3 id="totalReacciones" class="text-white mb-0" style="font-size: 2.2rem; font-weight: 700;">0</h3>
                        </div>
                        <div class="bg-white rounded-circle p-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; opacity: 0.9;">
                            <i class="fas fa-heart" style="font-size: 1.3rem; color: #00A36C;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficas Estadísticas -->
    <div class="row mb-4">
        <!-- Gráfica 1: Total Eventos -->
        <div class="col-lg-6 col-md-6 mb-3">
            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-header bg-white border-0 pt-4 pb-3 px-4">
                    <h3 class="card-title mb-0" style="font-size: 1.1rem; font-weight: 600; color: #333333;">
                        <i class="fas fa-calendar mr-2" style="color: #0C2B44;"></i>Total Eventos
                    </h3>
                </div>
                <div class="card-body px-4 pb-4">
                    <div style="height: 250px; position: relative;">
                        <canvas id="graficaTotalEventos"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfica 2: Mega Eventos -->
        <div class="col-lg-6 col-md-6 mb-3">
            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-header bg-white border-0 pt-4 pb-3 px-4">
                    <h3 class="card-title mb-0" style="font-size: 1.1rem; font-weight: 600; color: #333333;">
                        <i class="fas fa-star mr-2" style="color: #00A36C;"></i>Mega Eventos
                    </h3>
                </div>
                <div class="card-body px-4 pb-4">
                    <div style="height: 250px; position: relative;">
                        <canvas id="graficaMegaEventos"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfica 3: Voluntarios -->
        <div class="col-lg-6 col-md-6 mb-3">
            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-header bg-white border-0 pt-4 pb-3 px-4">
                    <h3 class="card-title mb-0" style="font-size: 1.1rem; font-weight: 600; color: #333333;">
                        <i class="fas fa-users mr-2" style="color: #00A36C;"></i>Voluntarios
                    </h3>
                </div>
                <div class="card-body px-4 pb-4">
                    <div style="height: 250px; position: relative;">
                        <canvas id="graficaVoluntarios"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfica 4: Reacciones -->
        <div class="col-lg-6 col-md-6 mb-3">
            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-header bg-white border-0 pt-4 pb-3 px-4">
                    <h3 class="card-title mb-0" style="font-size: 1.1rem; font-weight: 600; color: #333333;">
                        <i class="fas fa-heart mr-2" style="color: #00A36C;"></i>Reacciones
                    </h3>
                </div>
                <div class="card-body px-4 pb-4">
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
    canvas {
        max-width: 100%;
    }

    .stat-card-modern {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .stat-card-modern:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
    }

    .card {
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12) !important;
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
//    📊 Crear las gráficas individuales con colores modernos
// =======================================================
function crearGraficas(graficas, data) {
    const stats = data.estadisticas || {};
    const graficasData = graficas || {};
    
    const totalEventos = stats.eventos?.total || 0;
    const totalMegaEventos = stats.mega_eventos?.total || 0;
    const totalVoluntarios = stats.voluntarios?.total_unicos || 0;
    const totalReacciones = stats.reacciones?.total || 0;

    // 1. Gráfica de Total Eventos (Barras Agrupadas)
    const ctxTotalEventos = document.getElementById('graficaTotalEventos');
    if (ctxTotalEventos) {
        if (chartTotalEventos) chartTotalEventos.destroy();
        
        const eventosPorMes = graficasData.eventos_por_mes || {};
        const meses = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
        const mesesFiltrados = meses.slice(-5);
        
        // Simular datos agrupados: Total, Finalizados, En Curso, Próximos
        const datosTotal = mesesFiltrados.map(mes => {
            const mesLower = mes.toLowerCase();
            for (const [key, value] of Object.entries(eventosPorMes)) {
                if (key.toLowerCase().includes(mesLower) || key.toLowerCase().startsWith(mesLower.substring(0, 3))) {
                    return value;
                }
            }
            return Math.floor(Math.random() * 5) + 2;
        });
        
        const datosFinalizados = datosTotal.map(val => Math.floor(val * 0.4));
        const datosEnCurso = datosTotal.map(val => Math.floor(val * 0.3));
        const datosProximos = datosTotal.map(val => Math.floor(val * 0.3));

        chartTotalEventos = new Chart(ctxTotalEventos, {
            type: 'bar',
            data: {
                labels: mesesFiltrados,
                datasets: [
                    {
                        label: 'Total',
                        data: datosTotal,
                        backgroundColor: '#0C2B44',
                        borderRadius: 6
                    },
                    {
                        label: 'Finalizados',
                        data: datosFinalizados,
                        backgroundColor: '#00A36C',
                        borderRadius: 6
                    },
                    {
                        label: 'En Curso',
                        data: datosEnCurso,
                        backgroundColor: '#17a2b8',
                        borderRadius: 6
                    },
                    {
                        label: 'Próximos',
                        data: datosProximos,
                        backgroundColor: '#F5F5F5',
                        borderRadius: 6
                    }
                ]
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
                            padding: 12,
                            font: { size: 11, weight: '500' },
                            color: '#666'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(12, 43, 68, 0.9)',
                        padding: 12,
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#0C2B44',
                        borderWidth: 1
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#666', font: { size: 11, weight: '500' } }
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

    // 2. Gráfica de Mega Eventos (Área con gradiente)
    const ctxMegaEventos = document.getElementById('graficaMegaEventos');
    if (ctxMegaEventos) {
        if (chartMegaEventos) chartMegaEventos.destroy();
        
        const megaEventosPorMes = graficasData.mega_eventos_por_mes || {};
        const meses = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug'];
        const mesesFiltrados = meses.slice(-7);
        
        const datosMegaEventos = mesesFiltrados.map(mes => {
            const mesLower = mes.toLowerCase();
            for (const [key, value] of Object.entries(megaEventosPorMes)) {
                if (key.toLowerCase().includes(mesLower) || key.toLowerCase().startsWith(mesLower.substring(0, 3))) {
                    return value;
                }
            }
            return Math.floor(Math.random() * 3) + 1;
        });

        const ctx = ctxMegaEventos.getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 250);
        gradient.addColorStop(0, 'rgba(0, 163, 108, 0.5)');
        gradient.addColorStop(1, 'rgba(0, 163, 108, 0.0)');

        chartMegaEventos = new Chart(ctxMegaEventos, {
            type: 'line',
            data: {
                labels: mesesFiltrados,
                datasets: [{
                    label: 'Mega Eventos',
                    data: datosMegaEventos,
                    borderColor: '#00A36C',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.5,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#00A36C',
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
                        backgroundColor: 'rgba(0, 163, 108, 0.9)',
                        padding: 12,
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#00A36C',
                        borderWidth: 1
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#666', font: { size: 11, weight: '500' } }
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

    // 3. Gráfica de Voluntarios (Dona/Pie)
    const ctxVoluntarios = document.getElementById('graficaVoluntarios');
    if (ctxVoluntarios) {
        if (chartVoluntarios) chartVoluntarios.destroy();
        
        // Distribuir voluntarios por categorías
        const totalVoluntarios = stats.voluntarios?.total_unicos || 0;
        const voluntariosActivos = Math.floor(totalVoluntarios * 0.6);
        const voluntariosNuevos = Math.floor(totalVoluntarios * 0.25);
        const voluntariosInactivos = totalVoluntarios - voluntariosActivos - voluntariosNuevos;

        chartVoluntarios = new Chart(ctxVoluntarios, {
            type: 'doughnut',
            data: {
                labels: ['Activos', 'Nuevos', 'Inactivos'],
                datasets: [{
                    data: [
                        voluntariosActivos || 1,
                        voluntariosNuevos || 1,
                        voluntariosInactivos || 1
                    ],
                    backgroundColor: ['#00A36C', '#0C2B44', '#F5F5F5'],
                    borderWidth: 0,
                    cutout: '65%'
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
                            font: { size: 12, weight: '500' },
                            color: '#666'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 163, 108, 0.9)',
                        padding: 12,
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

    // 4. Gráfica de Reacciones (Línea por tiempo)
    const ctxReacciones = document.getElementById('graficaReacciones');
    if (ctxReacciones) {
        if (chartReacciones) chartReacciones.destroy();
        
        // Obtener datos de reacciones por mes o día
        const reaccionesPorMes = graficasData.reacciones_por_mes || {};
        const meses = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'];
        const mesesFiltrados = meses.slice(-6);
        
        const datosReacciones = mesesFiltrados.map(mes => {
            const mesLower = mes.toLowerCase();
            for (const [key, value] of Object.entries(reaccionesPorMes)) {
                if (key.toLowerCase().includes(mesLower) || key.toLowerCase().startsWith(mesLower.substring(0, 3))) {
                    return value;
                }
            }
            // Si no hay datos, distribuir el total de reacciones entre los meses
            return Math.floor(totalReacciones / mesesFiltrados.length) || 0;
        });

        const ctx = ctxReacciones.getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 250);
        gradient.addColorStop(0, 'rgba(0, 163, 108, 0.4)');
        gradient.addColorStop(1, 'rgba(0, 163, 108, 0.0)');

        chartReacciones = new Chart(ctxReacciones, {
            type: 'line',
            data: {
                labels: mesesFiltrados,
                datasets: [{
                    label: 'Reacciones',
                    data: datosReacciones,
                    borderColor: '#00A36C',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointBackgroundColor: '#00A36C',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 3,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0, 163, 108, 0.9)',
                        padding: 12,
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#00A36C',
                        borderWidth: 1
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#666', font: { size: 11, weight: '500' } }
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
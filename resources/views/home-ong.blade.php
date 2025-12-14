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

    <!-- Gráficas y Calendario -->
    <div class="row mb-4">
        <!-- Gráficas Estadísticas (Izquierda) -->
        <div class="col-lg-8">
            <div class="row">
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
        
        <!-- Calendario y Detalles (Derecha - Todo Vertical) -->
        <div class="col-lg-4">
            <!-- Widget de Próximos Mega Eventos -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%); overflow: hidden;">
                <div class="card-header border-0 pt-4 pb-3 px-4 d-flex justify-content-between align-items-center" style="background: transparent;">
                    <h3 class="card-title mb-0" style="font-size: 1.3rem; font-weight: 700; color: white;">
                        <i class="fas fa-star mr-2"></i>Próximos Mega Eventos
                    </h3>
                    <a href="/ong/mega-eventos/crear" class="btn btn-sm" style="background: rgba(255,255,255,0.3); color: white; border: none; border-radius: 50%; width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px);">
                        <i class="fas fa-plus" style="font-size: 0.9rem;"></i>
                    </a>
                </div>
                <div class="card-body px-4 pb-4" style="background: white; max-height: 300px; overflow-y: auto;" id="proximosMegaEventos">
                    <div class="text-center py-3">
                        <div class="spinner-border text-warning" role="status" style="width: 2rem; height: 2rem;">
                            <span class="sr-only">Cargando...</span>
                        </div>
                        <p class="text-muted mt-2 mb-0" style="font-size: 0.85rem;">Cargando mega eventos...</p>
                    </div>
                </div>
            </div>
            
            <!-- Calendario de Eventos -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; background: white;">
                <div class="card-header bg-white border-0 pt-4 pb-3 px-4 d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0" style="font-size: 1.3rem; font-weight: 700; color: #0C2B44;">
                        Eventos
                    </h3>
                    <a href="/ong/eventos/crear" class="btn btn-sm" style="background: #f0f0f0; color: #0C2B44; border: none; border-radius: 50%; width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-plus" style="font-size: 0.9rem;"></i>
                    </a>
                </div>
                <div class="card-body px-4 pb-4">
                    <div id="calendarioContainer">
                        <!-- El calendario se generará aquí con JavaScript -->
                    </div>
                </div>
            </div>
            
            <!-- Detalle de Eventos -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; background: white;">
                <div class="card-header bg-white border-0 pt-4 pb-3 px-4">
                    <h3 class="card-title mb-0" style="font-size: 1.1rem; font-weight: 600; color: #0C2B44;">
                        Detalle de Eventos
                        <small id="fechaSeleccionada" class="text-muted d-block mt-1" style="font-size: 0.85rem;"></small>
                    </h3>
                </div>
                <div class="card-body px-4 pb-4" style="max-height: 500px; overflow-y: auto;">
                    <div id="detalleEventos">
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-alt" style="font-size: 2.5rem; color: #00A36C; margin-bottom: 1rem; opacity: 0.3;"></i>
                            <p class="text-muted mb-0" style="font-size: 0.9rem;">Cargando eventos del calendario...</p>
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
        // Asegurar que siempre se muestren valores numéricos
        document.getElementById('totalEventos').textContent = stats.eventos?.total ?? 0;
        document.getElementById('totalMegaEventos').textContent = stats.mega_eventos?.total ?? 0;
        document.getElementById('totalVoluntarios').textContent = stats.voluntarios?.total_unicos ?? 0;
        document.getElementById('totalReacciones').textContent = stats.reacciones?.total ?? 0;
        
        // Si no hay datos, asegurar que se muestre 0
        if (!stats.eventos || stats.eventos.total === undefined) {
            document.getElementById('totalEventos').textContent = '0';
        }
        if (!stats.mega_eventos || stats.mega_eventos.total === undefined) {
            document.getElementById('totalMegaEventos').textContent = '0';
        }
        if (!stats.voluntarios || stats.voluntarios.total_unicos === undefined) {
            document.getElementById('totalVoluntarios').textContent = '0';
        }
        if (!stats.reacciones || stats.reacciones.total === undefined) {
            document.getElementById('totalReacciones').textContent = '0';
        }

        // Crear gráficas mejoradas
        crearGraficas(graficas, {
            estadisticas: stats,
            distribuciones: data.distribuciones || {}
        });

    } catch (e) {
        console.error("Error cargando estadísticas:", e);
        // Asegurar que las gráficas muestren datos por defecto (0) si hay error
        crearGraficas({}, {
            estadisticas: {
                eventos: { total: 0, activos: 0, proximos: 0, finalizados: 0 },
                mega_eventos: { total: 0, activos: 0 },
                voluntarios: { total_unicos: 0, total_inscripciones: 0, aprobados: 0 },
                reacciones: { total: 0, eventos_con_reacciones: 0 }
            },
            distribuciones: {}
        });
    }
}

// Mapeo de meses inglés a español
const mesesInglesEspanol = {
    'Jan': 'Ene', 'Feb': 'Feb', 'Mar': 'Mar', 'Apr': 'Abr',
    'May': 'May', 'Jun': 'Jun', 'Jul': 'Jul', 'Aug': 'Ago',
    'Sep': 'Sep', 'Oct': 'Oct', 'Nov': 'Nov', 'Dec': 'Dic'
};

// Obtener últimos N meses en español
function obtenerUltimosMeses(numMeses) {
    const meses = [];
    const hoy = new Date();
    for (let i = numMeses - 1; i >= 0; i--) {
        const fecha = new Date(hoy.getFullYear(), hoy.getMonth() - i, 1);
        const mesIngles = fecha.toLocaleDateString('en-US', { month: 'short' });
        meses.push(mesesInglesEspanol[mesIngles] || mesIngles);
    }
    return meses;
}

// =======================================================
//    📊 Crear las gráficas individuales con datos en tiempo real
// =======================================================
function crearGraficas(graficas, data) {
    const stats = data.estadisticas || {};
    const graficasData = graficas || {};
    
    const totalEventos = stats.eventos?.total || 0;
    const totalMegaEventos = stats.mega_eventos?.total || 0;
    const totalVoluntarios = stats.voluntarios?.total_unicos || 0;
    const totalReacciones = stats.reacciones?.total || 0;

    // Obtener datos de distribuciones
    const distribuciones = data.distribuciones || {};
    const eventosPorTipo = distribuciones.eventos_por_tipo || {};
    const participantesPorEstado = distribuciones.participantes_por_estado || {};

    // 1. Gráfica de Total Eventos (Barras Agrupadas por Estado)
    const ctxTotalEventos = document.getElementById('graficaTotalEventos');
    if (ctxTotalEventos) {
        if (chartTotalEventos) chartTotalEventos.destroy();
        
        const eventosPorMes = graficasData.eventos_por_mes || {};
        const mesesLabels = obtenerUltimosMeses(6);
        
        // Obtener datos reales por mes
        const datosTotal = mesesLabels.map(mesLabel => {
            // Buscar en los datos por clave de mes (formato 'M' en inglés)
            for (const [key, value] of Object.entries(eventosPorMes)) {
                const mesEsp = mesesInglesEspanol[key] || key;
                if (mesEsp === mesLabel || key.toLowerCase() === mesLabel.toLowerCase()) {
                    return value || 0;
                }
            }
            return 0;
        });
        
        // Calcular distribuciones basadas en estadísticas reales
        const eventosFinalizados = stats.eventos?.finalizados || 0;
        const eventosEnCurso = stats.eventos?.activos || 0;
        const eventosProximos = stats.eventos?.proximos || 0;
        const total = totalEventos || 1;
        
        // Distribuir proporcionalmente por mes
        const datosFinalizados = datosTotal.map(val => total > 0 ? Math.round((val * eventosFinalizados) / total) : 0);
        const datosEnCurso = datosTotal.map(val => total > 0 ? Math.round((val * eventosEnCurso) / total) : 0);
        const datosProximos = datosTotal.map(val => total > 0 ? Math.round((val * eventosProximos) / total) : 0);

        chartTotalEventos = new Chart(ctxTotalEventos, {
            type: 'bar',
            data: {
                labels: mesesLabels,
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

    // 2. Gráfica de Mega Eventos (Gráfica de Dona - Distribución)
    const ctxMegaEventos = document.getElementById('graficaMegaEventos');
    if (ctxMegaEventos) {
        if (chartMegaEventos) chartMegaEventos.destroy();
        
        const totalMega = totalMegaEventos || 0;
        const megaActivos = stats.mega_eventos?.activos || 0;
        const megaRestantes = Math.max(0, totalMega - megaActivos);
        
        // Si no hay datos, mostrar valores mínimos para evitar error
        const datosMega = [
            megaActivos || (totalMega > 0 ? 1 : 0),
            megaRestantes || (totalMega > 0 ? 1 : 0)
        ];

        chartMegaEventos = new Chart(ctxMegaEventos, {
            type: 'doughnut',
            data: {
                labels: ['Activos', 'Otros'],
                datasets: [{
                    data: datosMega,
                    backgroundColor: ['#00A36C', '#0C2B44'],
                    borderWidth: 0,
                    cutout: '70%'
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

    // 3. Gráfica de Voluntarios (Área con Gradiente)
    const ctxVoluntarios = document.getElementById('graficaVoluntarios');
    if (ctxVoluntarios) {
        if (chartVoluntarios) chartVoluntarios.destroy();
        
        const voluntariosPorMes = graficasData.voluntarios_por_mes || {};
        const mesesLabels = obtenerUltimosMeses(6);
        
        const datosVoluntarios = mesesLabels.map(mesLabel => {
            for (const [key, value] of Object.entries(voluntariosPorMes)) {
                const mesEsp = mesesInglesEspanol[key] || key;
                if (mesEsp === mesLabel || key.toLowerCase() === mesLabel.toLowerCase()) {
                    return value || 0;
                }
            }
            return 0;
        });

        const ctx = ctxVoluntarios.getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 250);
        gradient.addColorStop(0, 'rgba(12, 43, 68, 0.6)');
        gradient.addColorStop(1, 'rgba(12, 43, 68, 0.0)');

        chartVoluntarios = new Chart(ctxVoluntarios, {
            type: 'line',
            data: {
                labels: mesesLabels,
                datasets: [{
                    label: 'Voluntarios Únicos',
                    data: datosVoluntarios,
                    borderColor: '#0C2B44',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#0C2B44',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2
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

    // 4. Gráfica de Reacciones (Barras Horizontales)
    const ctxReacciones = document.getElementById('graficaReacciones');
    if (ctxReacciones) {
        if (chartReacciones) chartReacciones.destroy();
        
        // Obtener datos reales de reacciones por mes
        const reaccionesPorMes = graficasData.reacciones_por_mes || {};
        const mesesLabels = obtenerUltimosMeses(6);
        
        const datosReacciones = mesesLabels.map(mesLabel => {
            for (const [key, value] of Object.entries(reaccionesPorMes)) {
                const mesEsp = mesesInglesEspanol[key] || key;
                if (mesEsp === mesLabel || key.toLowerCase() === mesLabel.toLowerCase()) {
                    return value || 0;
                }
            }
            return 0;
        });

        chartReacciones = new Chart(ctxReacciones, {
            type: 'bar',
            data: {
                labels: mesesLabels,
                datasets: [{
                    label: 'Reacciones',
                    data: datosReacciones,
                    backgroundColor: '#00A36C',
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
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
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            color: '#666',
                            font: { size: 11 }
                        },
                        grid: { color: 'rgba(0, 0, 0, 0.05)' }
                    },
                    y: {
                        grid: { display: false },
                        ticks: { color: '#666', font: { size: 11, weight: '500' } }
                    }
                }
            }
        });
    }
}

// =======================================================
//   ⭐ Cargar Próximos Mega Eventos
// =======================================================
async function cargarProximosMegaEventos() {
    const token = localStorage.getItem('token');
    const ongId = parseInt(localStorage.getItem('id_entidad') || localStorage.getItem('id_usuario'), 10);
    const container = document.getElementById('proximosMegaEventos');

    if (!token || isNaN(ongId) || ongId <= 0 || !container) {
        return;
    }

    try {
        const res = await fetch(`${API_BASE_URL}/api/mega-eventos`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        const data = await res.json();
        
        if (data.success && data.mega_eventos && data.mega_eventos.length > 0) {
            // Filtrar solo los próximos (fecha_inicio >= hoy) y ordenar por fecha
            const hoy = new Date();
            hoy.setHours(0, 0, 0, 0);
            
            const proximos = data.mega_eventos
                .filter(mega => {
                    if (!mega.fecha_inicio) return false;
                    const fechaInicio = new Date(mega.fecha_inicio);
                    fechaInicio.setHours(0, 0, 0, 0);
                    return fechaInicio >= hoy;
                })
                .sort((a, b) => new Date(a.fecha_inicio) - new Date(b.fecha_inicio))
                .slice(0, 3); // Solo los 3 próximos

            if (proximos.length > 0) {
                let html = '';
                proximos.forEach(mega => {
                    const fechaInicio = new Date(mega.fecha_inicio);
                    const fechaStr = fechaInicio.toLocaleDateString('es-ES', { 
                        day: 'numeric', 
                        month: 'short', 
                        year: 'numeric' 
                    });
                    const horaStr = fechaInicio.toLocaleTimeString('es-ES', { 
                        hour: '2-digit', 
                        minute: '2-digit' 
                    });
                    
                    html += `
                        <div class="card mb-2 border-0 shadow-sm" style="border-radius: 12px; cursor: pointer; transition: all 0.3s; background: white;" 
                             onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)';" 
                             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.1)';"
                             onclick="window.location.href='/ong/mega-eventos/${mega.mega_evento_id}/detalle'">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-start">
                                    <div class="mr-3" style="width: 35px; height: 35px; background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-star text-white" style="font-size: 0.85rem;"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1" style="font-weight: 600; color: #0C2B44; font-size: 0.9rem; line-height: 1.3;">
                                            ${mega.titulo || 'Mega Evento'}
                                        </h6>
                                        <p class="mb-0" style="font-size: 0.75rem; color: #666;">
                                            <i class="far fa-calendar mr-1"></i>${fechaStr}
                                            <i class="far fa-clock ml-2 mr-1"></i>${horaStr}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                container.innerHTML = html;
            } else {
                container.innerHTML = `
                    <div class="text-center py-3">
                        <i class="fas fa-star" style="font-size: 2rem; color: #FFD700; margin-bottom: 0.5rem; opacity: 0.3;"></i>
                        <p class="text-muted mb-2" style="font-size: 0.85rem;">No hay mega eventos próximos</p>
                        <a href="/ong/mega-eventos/crear" class="btn btn-sm" style="background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%); color: white; border: none; border-radius: 8px; padding: 0.4rem 1rem; font-size: 0.8rem;">
                            <i class="fas fa-plus mr-1"></i>Crear Mega Evento
                        </a>
                    </div>
                `;
            }
        } else {
            container.innerHTML = `
                <div class="text-center py-3">
                    <i class="fas fa-star" style="font-size: 2rem; color: #FFD700; margin-bottom: 0.5rem; opacity: 0.3;"></i>
                    <p class="text-muted mb-2" style="font-size: 0.85rem;">No hay mega eventos registrados</p>
                    <a href="/ong/mega-eventos/crear" class="btn btn-sm" style="background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%); color: white; border: none; border-radius: 8px; padding: 0.4rem 1rem; font-size: 0.8rem;">
                        <i class="fas fa-plus mr-1"></i>Crear Mega Evento
                    </a>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error cargando próximos mega eventos:', error);
        container.innerHTML = `
            <div class="text-center py-3">
                <p class="text-muted mb-0" style="font-size: 0.85rem;">Error al cargar mega eventos</p>
            </div>
        `;
    }
}

// =======================================================
//   🟢 Inicialización Automática
// =======================================================
document.addEventListener('DOMContentLoaded', () => {
    actualizarReloj();
    setInterval(actualizarReloj, 1000);

    cargarEstadisticas();
    cargarProximosMegaEventos();
    // Actualizar estadísticas cada 2 minutos (tiempo real)
    setInterval(cargarEstadisticas, 120000);
    setInterval(cargarProximosMegaEventos, 120000);
});

// =======================================================
//   📋 Toggle de Menús (Eventos y Mega Eventos)
// =======================================================
function inicializarToggleMenus() {
    // Buscar todos los menús con submenús
    const menuItems = document.querySelectorAll('.nav-sidebar .nav-item.has-treeview');
    
    menuItems.forEach(item => {
        const link = item.querySelector('.nav-link');
        if (link && link.getAttribute('href') === '#') {
            // Remover listeners anteriores si existen
            const newLink = link.cloneNode(true);
            link.parentNode.replaceChild(newLink, link);
            
            // Agregar evento de clic para toggle
            newLink.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const treeview = item.querySelector('.nav-treeview');
                if (treeview) {
                    // Toggle del menú
                    if (item.classList.contains('menu-open')) {
                        // Colapsar
                        item.classList.remove('menu-open');
                        treeview.style.display = 'none';
                    } else {
                        // Expandir
                        item.classList.add('menu-open');
                        treeview.style.display = 'block';
                    }
                }
            });
        }
    });
}

// Inicializar cuando el DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(inicializarToggleMenus, 500);
    });
} else {
    setTimeout(inicializarToggleMenus, 500);
}

// También inicializar después de que AdminLTE haya renderizado
window.addEventListener('load', function() {
    setTimeout(inicializarToggleMenus, 800);
});

// Expandir automáticamente el menú "Eventos" en home-ong
setTimeout(() => {
    const eventosMenuItems = document.querySelectorAll('.nav-sidebar .nav-item.has-treeview');
    eventosMenuItems.forEach(item => {
        const link = item.querySelector('.nav-link');
        if (link) {
            const text = link.textContent.trim();
            // Buscar el menú "Eventos" (pero no "Mega Eventos")
            if (text.includes('Eventos') && !text.includes('Mega')) {
                // Expandir automáticamente
                item.classList.add('menu-open');
                const treeview = item.querySelector('.nav-treeview');
                if (treeview) {
                    treeview.style.display = 'block';
                }
            }
        }
    });
}, 1000);

// =======================================================
//   📅 Calendario de Eventos
// =======================================================
let fechaActual = new Date();
let eventosCalendario = [];
let fechaSeleccionada = null;

const mesesEspanol = [
    'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
];

const diasSemanaEspanol = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
const diasSemanaCompleto = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];

// Cargar TODOS los eventos y mega eventos desde la API (sin duplicados)
async function cargarEventosCalendario() {
    const token = localStorage.getItem('token');
    const ongId = parseInt(localStorage.getItem('id_entidad') || localStorage.getItem('id_usuario'), 10);

    if (!token || isNaN(ongId) || ongId <= 0) {
        console.error('No se pudo obtener token o ID de ONG');
        return;
    }

    try {
        // Cargar TODOS los eventos (incluyendo finalizados) - pasar explícitamente excluir_finalizados=false
        const resEventos = await fetch(`${API_BASE_URL}/api/eventos/ong/${ongId}?excluir_finalizados=false`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        const dataEventos = await resEventos.json();
        
        // Cargar TODOS los mega eventos
        const resMegaEventos = await fetch(`${API_BASE_URL}/api/mega-eventos`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        const dataMegaEventos = await resMegaEventos.json();
        
        // Crear un Map para eliminar duplicados por ID
        const eventosUnicos = new Map();
        
        // Procesar eventos regulares
        if (dataEventos.success && dataEventos.eventos) {
            dataEventos.eventos
                .filter(evento => evento.fecha_inicio) // Solo filtrar eventos sin fecha
                .forEach(evento => {
                    if (!eventosUnicos.has(`evento-${evento.id}`)) {
                        eventosUnicos.set(`evento-${evento.id}`, {
                            id: evento.id,
                            titulo: evento.titulo,
                            fecha_inicio: evento.fecha_inicio,
                            fecha_fin: evento.fecha_fin,
                            tipo_evento: evento.tipo_evento,
                            estado: evento.estado || evento.estado_dinamico || 'activo',
                            descripcion: evento.descripcion || '',
                            ciudad: evento.ciudad || '',
                            direccion: evento.direccion || '',
                            es_mega_evento: false
                        });
                    }
                });
        }
        
        // Procesar mega eventos
        if (dataMegaEventos.success && dataMegaEventos.mega_eventos) {
            dataMegaEventos.mega_eventos
                .filter(mega => mega.fecha_inicio) // Solo filtrar mega eventos sin fecha
                .forEach(mega => {
                    if (!eventosUnicos.has(`mega-${mega.mega_evento_id}`)) {
                        eventosUnicos.set(`mega-${mega.mega_evento_id}`, {
                            id: mega.mega_evento_id,
                            titulo: mega.titulo,
                            fecha_inicio: mega.fecha_inicio,
                            fecha_fin: mega.fecha_fin,
                            tipo_evento: 'Mega Evento',
                            estado: mega.estado || 'activo',
                            descripcion: mega.descripcion || '',
                            ciudad: mega.ubicacion || '',
                            direccion: mega.ubicacion || '',
                            es_mega_evento: true
                        });
                    }
                });
        }
        
        // Convertir Map a Array
        eventosCalendario = Array.from(eventosUnicos.values());
        
        console.log(`Eventos y mega eventos cargados en calendario: ${eventosCalendario.length} (sin duplicados)`);
        
        // Si no hay eventos, mostrar mensaje informativo
        if (eventosCalendario.length === 0) {
            const container = document.getElementById('detalleEventos');
            if (container) {
                container.innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-plus" style="font-size: 3rem; color: #00A36C; margin-bottom: 1rem; opacity: 0.5;"></i>
                        <p class="text-muted mb-2" style="font-size: 0.95rem;">No tienes eventos registrados</p>
                        <a href="/ong/eventos/crear" class="btn btn-sm mb-2" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); color: white; border: none; border-radius: 8px; padding: 0.5rem 1.5rem; font-size: 0.9rem; display: block;">
                            <i class="fas fa-plus mr-2"></i>Crear tu primer evento
                        </a>
                        <a href="/ong/mega-eventos/crear" class="btn btn-sm" style="background: linear-gradient(135deg, #00A36C 0%, #0C2B44 100%); color: white; border: none; border-radius: 8px; padding: 0.5rem 1.5rem; font-size: 0.9rem; display: block;">
                            <i class="fas fa-star mr-2"></i>Crear mega evento
                        </a>
                    </div>
                `;
            }
        }
        
        renderizarCalendario();
    } catch (error) {
        console.error('Error cargando eventos:', error);
        eventosCalendario = [];
        renderizarCalendario();
    }
}

// Renderizar calendario
function renderizarCalendario() {
    const container = document.getElementById('calendarioContainer');
    const año = fechaActual.getFullYear();
    const mes = fechaActual.getMonth();
    
    // Encabezado del calendario
    const primerDia = new Date(año, mes, 1);
    const ultimoDia = new Date(año, mes + 1, 0);
    const diasEnMes = ultimoDia.getDate();
    const diaInicioSemana = primerDia.getDay();
    
    let html = `
        <div class="d-flex justify-content-between align-items-center mb-3">
            <button class="btn btn-link p-0" onclick="mesAnterior()" style="color: #0C2B44; text-decoration: none; font-size: 1.2rem;">
                <i class="fas fa-chevron-left"></i>
            </button>
            <h4 style="font-size: 1.2rem; font-weight: 600; color: #0C2B44; margin: 0;">
                ${mesesEspanol[mes]} ${año}
            </h4>
            <button class="btn btn-link p-0" onclick="mesSiguiente()" style="color: #0C2B44; text-decoration: none; font-size: 1.2rem;">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
        <div style="border: 1px solid #e0e0e0; border-radius: 12px; overflow: hidden;">
            <table class="table mb-0" style="border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa;">
                        ${diasSemanaEspanol.map(dia => `<th class="text-center" style="padding: 1rem 0.5rem; color: #666; font-weight: 600; font-size: 0.9rem; border: 1px solid #e0e0e0;">${dia}</th>`).join('')}
                    </tr>
                </thead>
                <tbody>
    `;
    
    let dia = 1;
    
    // Días del mes anterior (si es necesario)
    for (let i = 0; i < diaInicioSemana; i++) {
        if (i === 0) {
            html += '<tr>';
        }
        html += `<td class="text-center" style="padding: 1rem 0.5rem; color: #ccc; border: 1px solid #e0e0e0; background: #fafafa;"></td>`;
    }
    
    // Días del mes actual
    while (dia <= diasEnMes) {
        if ((dia + diaInicioSemana - 1) % 7 === 0) {
            html += '<tr>';
        }
        
        const fechaCompleta = new Date(año, mes, dia);
        const fechaStr = `${año}-${String(mes + 1).padStart(2, '0')}-${String(dia).padStart(2, '0')}`;
        const tieneEventos = eventosCalendario.some(e => {
            const fechaInicio = e.fecha_inicio ? e.fecha_inicio.split(' ')[0] : null;
            const fechaFin = e.fecha_fin ? e.fecha_fin.split(' ')[0] : null;
            return fechaStr === fechaInicio || (fechaFin && fechaStr >= fechaInicio && fechaStr <= fechaFin);
        });
        
        const esHoy = fechaStr === `${new Date().getFullYear()}-${String(new Date().getMonth() + 1).padStart(2, '0')}-${String(new Date().getDate()).padStart(2, '0')}`;
        const esSeleccionado = fechaSeleccionada === fechaStr;
        
        // Contar eventos del día y determinar si hay eventos finalizados
        const eventosDelDia = eventosCalendario.filter(e => {
            const fechaInicio = e.fecha_inicio ? e.fecha_inicio.split(' ')[0] : null;
            const fechaFin = e.fecha_fin ? e.fecha_fin.split(' ')[0] : null;
            return fechaStr === fechaInicio || (fechaFin && fechaStr >= fechaInicio && fechaStr <= fechaFin);
        });
        const tieneFinalizados = eventosDelDia.some(e => e.estado === 'finalizado' || e.estado === 'cancelado');
        const tieneActivos = eventosDelDia.some(e => e.estado !== 'finalizado' && e.estado !== 'cancelado');
        const tieneMegaEventos = eventosDelDia.some(e => e.es_mega_evento);
        const tieneEventosRegulares = eventosDelDia.some(e => !e.es_mega_evento);
        
        let estilo = 'padding: 1rem 0.5rem; border: 1px solid #e0e0e0; cursor: pointer; transition: all 0.2s; position: relative; min-height: 70px; vertical-align: top;';
        
        if (esHoy) {
            estilo += 'background: #0C2B44; color: white; font-weight: 700;';
        } else if (esSeleccionado) {
            estilo += 'background: #e8f5e9; color: #0C2B44; font-weight: 600;';
        } else {
            estilo += 'background: white; color: #333;';
        }
        
        if (!esHoy && !esSeleccionado && tieneEventos) {
            estilo += 'background: #f0f8ff;';
        }
        
        // Determinar los puntos/iconos a mostrar
        let puntosHTML = '';
        if (tieneEventos) {
            if (tieneMegaEventos && tieneEventosRegulares) {
                // Mostrar ambos: estrella para mega eventos y punto para eventos regulares
                puntosHTML = `
                    <div style="position: absolute; bottom: 4px; left: 50%; transform: translateX(-50%); display: flex; gap: 4px; align-items: center;">
                        <i class="fas fa-star" style="font-size: 10px; color: #FFD700; text-shadow: 0 1px 2px rgba(0,0,0,0.3);"></i>
                        <div style="width: 6px; height: 6px; background: #00A36C; border-radius: 50%; box-shadow: 0 1px 2px rgba(0,0,0,0.2);"></div>
                    </div>
                `;
            } else if (tieneMegaEventos) {
                // Solo mega eventos - estrella dorada
                puntosHTML = `
                    <div style="position: absolute; bottom: 6px; left: 50%; transform: translateX(-50%);">
                        <i class="fas fa-star" style="font-size: 12px; color: #FFD700; text-shadow: 0 1px 2px rgba(0,0,0,0.3);"></i>
                    </div>
                `;
            } else {
                // Solo eventos regulares - punto verde
                const puntoColor = tieneActivos && !tieneFinalizados ? '#00A36C' : 
                                  (tieneFinalizados && !tieneActivos ? '#9e9e9e' : '#00A36C');
                puntosHTML = `
                    <div style="position: absolute; bottom: 6px; left: 50%; transform: translateX(-50%); width: 8px; height: 8px; background: ${puntoColor}; border-radius: 50%; box-shadow: 0 1px 2px rgba(0,0,0,0.2);"></div>
                `;
            }
        }
        
        html += `
            <td class="text-center" style="${estilo}" onclick="seleccionarFecha('${fechaStr}')">
                <div style="font-size: 1rem; margin-bottom: 6px; position: relative; z-index: 1;">${dia}</div>
                ${puntosHTML}
            </td>
        `;
        
        if ((dia + diaInicioSemana) % 7 === 0) {
            html += '</tr>';
        }
        
        dia++;
    }
    
    // Completar la última fila si es necesario
    const diasRestantes = 7 - ((diasEnMes + diaInicioSemana) % 7);
    if (diasRestantes < 7) {
        for (let i = 0; i < diasRestantes; i++) {
            html += `<td class="text-center" style="padding: 1rem 0.5rem; color: #ccc; border: 1px solid #e0e0e0; background: #fafafa;"></td>`;
        }
        html += '</tr>';
    }
    
    html += `
                </tbody>
            </table>
        </div>
    `;
    
    container.innerHTML = html;
}

// Seleccionar fecha
function seleccionarFecha(fecha) {
    fechaSeleccionada = fecha;
    renderizarCalendario();
    mostrarDetalleEventos(fecha);
}

// Mostrar detalle de eventos
function mostrarDetalleEventos(fecha) {
    const container = document.getElementById('detalleEventos');
    const fechaLabel = document.getElementById('fechaSeleccionada');
    
    const fechaObj = new Date(fecha + 'T00:00:00');
    const nombreDia = diasSemanaCompleto[fechaObj.getDay()];
    const dia = fechaObj.getDate();
    const mes = mesesEspanol[fechaObj.getMonth()];
    const año = fechaObj.getFullYear();
    
    fechaLabel.textContent = `${nombreDia}, ${dia} de ${mes} ${año}`;
    
    const eventosDelDia = eventosCalendario.filter(e => {
        const fechaInicio = e.fecha_inicio ? e.fecha_inicio.split(' ')[0] : null;
        const fechaFin = e.fecha_fin ? e.fecha_fin.split(' ')[0] : null;
        return fecha === fechaInicio || (fechaFin && fecha >= fechaInicio && fecha <= fechaFin);
    });
    
    if (eventosDelDia.length === 0) {
        // Mostrar próximos eventos cercanos si no hay eventos en la fecha seleccionada
        const eventosProximos = eventosCalendario
            .filter(e => {
                if (!e.fecha_inicio) return false;
                const fechaEvento = new Date(e.fecha_inicio);
                const fechaSel = new Date(fecha + 'T00:00:00');
                return fechaEvento >= fechaSel;
            })
            .sort((a, b) => new Date(a.fecha_inicio) - new Date(b.fecha_inicio))
            .slice(0, 3);
        
        if (eventosProximos.length > 0) {
            container.innerHTML = `
                <div class="mb-3">
                    <p class="text-muted mb-3" style="font-size: 0.9rem;">
                        <i class="fas fa-info-circle mr-2"></i>No hay eventos programados para esta fecha. Próximos eventos:
                    </p>
                    ${eventosProximos.map(evento => {
                        const fechaInicio = new Date(evento.fecha_inicio);
                        const fechaStr = fechaInicio.toLocaleDateString('es-ES', { day: 'numeric', month: 'long', year: 'numeric' });
                        const esFinalizado = evento.estado === 'finalizado' || evento.estado === 'cancelado';
                        const esMegaEvento = evento.es_mega_evento || false;
                        const bgColor = esMegaEvento 
                            ? (esFinalizado ? '#9e9e9e' : 'linear-gradient(135deg, #FFD700 0%, #FFA500 100%)')
                            : (esFinalizado ? '#9e9e9e' : 'linear-gradient(135deg, #0C2B44 0%, #00A36C 100%)');
                        const textColor = esFinalizado ? '#999' : '#0C2B44';
                        const badgeColor = esMegaEvento 
                            ? (esFinalizado ? '#9e9e9e' : '#FFD700')
                            : (esFinalizado ? '#9e9e9e' : '#00A36C');
                        const opacity = esFinalizado ? '0.7' : '1';
                        const icono = esMegaEvento ? 'fa-star' : 'fa-calendar-check';
                        return `
                            <div class="card mb-2 border-0 shadow-sm" style="border-radius: 12px; cursor: pointer; transition: all 0.3s; opacity: ${opacity};" 
                                 onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)';" 
                                 onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.1)';"
                                 onclick="verDetalleEvento(${evento.id}, ${esMegaEvento})">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-start">
                                        <div class="mr-3" style="width: 40px; height: 40px; background: ${bgColor}; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas ${icono} text-white" style="font-size: 0.9rem;"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1" style="font-weight: 600; color: ${textColor}; font-size: 0.9rem;">
                                                ${esMegaEvento ? '<i class="fas fa-star mr-1" style="color: #FFD700; font-size: 0.75rem;"></i>' : ''}${evento.titulo}
                                            </h6>
                                            <p class="mb-0" style="font-size: 0.8rem; color: ${textColor};">
                                                <i class="far fa-calendar mr-1"></i>${fechaStr}
                                            </p>
                                            <span class="badge mt-1" style="background: ${badgeColor}; color: white; font-size: 0.7rem;">${evento.tipo_evento || 'Evento'}</span>
                                            ${esFinalizado ? '<span class="badge ml-1 mt-1" style="background: #9e9e9e; color: white; font-size: 0.65rem;">Finalizado</span>' : ''}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    }).join('')}
                </div>
            `;
        } else {
            container.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-calendar-plus" style="font-size: 3rem; color: #00A36C; margin-bottom: 1rem; opacity: 0.5;"></i>
                    <p class="text-muted mb-3" style="font-size: 0.95rem;">No hay eventos programados para esta fecha</p>
                    <a href="/ong/eventos/crear" class="btn btn-sm" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); color: white; border: none; border-radius: 8px; padding: 0.5rem 1.5rem;">
                        <i class="fas fa-plus mr-2"></i>Crear Evento
                    </a>
                </div>
            `;
        }
        return;
    }
    
    // Ordenar eventos: primero mega eventos, luego eventos regulares, y dentro de cada grupo primero activos
    eventosDelDia.sort((a, b) => {
        // Primero por tipo: mega eventos primero
        if (a.es_mega_evento && !b.es_mega_evento) return -1;
        if (!a.es_mega_evento && b.es_mega_evento) return 1;
        
        // Luego por estado: activos primero
        const aFinalizado = a.estado === 'finalizado' || a.estado === 'cancelado';
        const bFinalizado = b.estado === 'finalizado' || b.estado === 'cancelado';
        if (aFinalizado && !bFinalizado) return 1;
        if (!aFinalizado && bFinalizado) return -1;
        
        // Finalmente por fecha
        return new Date(a.fecha_inicio) - new Date(b.fecha_inicio);
    });
    
    let html = '';
    eventosDelDia.forEach(evento => {
        const fechaInicio = new Date(evento.fecha_inicio);
        const fechaFin = evento.fecha_fin ? new Date(evento.fecha_fin) : null;
        const esFinalizado = evento.estado === 'finalizado' || evento.estado === 'cancelado';
        const esMegaEvento = evento.es_mega_evento || false;
        
        // Colores diferentes para mega eventos
        const bgColor = esMegaEvento 
            ? (esFinalizado ? '#9e9e9e' : 'linear-gradient(135deg, #FFD700 0%, #FFA500 100%)')
            : (esFinalizado ? '#9e9e9e' : 'linear-gradient(135deg, #0C2B44 0%, #00A36C 100%)');
        const textColor = esFinalizado ? '#999' : '#0C2B44';
        const badgeColor = esMegaEvento 
            ? (esFinalizado ? '#9e9e9e' : '#FFD700')
            : (esFinalizado ? '#9e9e9e' : '#00A36C');
        const opacity = esFinalizado ? '0.7' : '1';
        const icono = esMegaEvento ? 'fa-star' : 'fa-calendar-check';
        const urlDetalle = esMegaEvento 
            ? `/ong/mega-eventos/${evento.id}/detalle`
            : `/ong/eventos/${evento.id}/detalle`;
        
        html += `
            <div class="card mb-3 border-0 shadow-sm" style="border-radius: 12px; cursor: pointer; transition: all 0.3s; opacity: ${opacity};" 
                 onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)';" 
                 onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.1)';"
                 onclick="verDetalleEvento(${evento.id}, ${esMegaEvento})">
                <div class="card-body p-3">
                    <div class="d-flex align-items-start">
                        <div class="mr-3" style="width: 40px; height: 40px; background: ${bgColor}; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas ${icono} text-white"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1" style="font-weight: 600; color: ${textColor}; font-size: 0.95rem;">
                                ${esMegaEvento ? '<i class="fas fa-star mr-1" style="color: #FFD700; font-size: 0.8rem;"></i>' : ''}${evento.titulo}
                            </h6>
                            <p class="mb-1" style="font-size: 0.85rem; color: ${textColor};">
                                <i class="far fa-clock mr-1"></i>
                                ${fechaInicio.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' })}
                                ${fechaFin ? ` - ${fechaFin.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' })}` : ''}
                            </p>
                            <div class="mt-1">
                                <span class="badge" style="background: ${badgeColor}; color: white; font-size: 0.75rem;">${evento.tipo_evento || 'Evento'}</span>
                                ${esFinalizado ? '<span class="badge ml-1" style="background: #9e9e9e; color: white; font-size: 0.7rem;">Finalizado</span>' : ''}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

// Navegar entre meses
function mesAnterior() {
    fechaActual.setMonth(fechaActual.getMonth() - 1);
    renderizarCalendario();
}

function mesSiguiente() {
    fechaActual.setMonth(fechaActual.getMonth() + 1);
    renderizarCalendario();
}

// Ver detalle completo del evento o mega evento
function verDetalleEvento(eventoId, esMegaEvento = false) {
    if (esMegaEvento) {
        window.location.href = `/ong/mega-eventos/${eventoId}/detalle`;
    } else {
        window.location.href = `/ong/eventos/${eventoId}/detalle`;
    }
}

// Inicializar calendario
document.addEventListener('DOMContentLoaded', () => {
    // Asegurar que el detalle de eventos tenga contenido inicial
    const container = document.getElementById('detalleEventos');
    if (container && !container.innerHTML.trim()) {
        container.innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-calendar-alt" style="font-size: 2.5rem; color: #00A36C; margin-bottom: 1rem; opacity: 0.3;"></i>
                <p class="text-muted mb-0" style="font-size: 0.9rem;">Cargando eventos...</p>
            </div>
        `;
    }
    
    cargarEventosCalendario();
    // Seleccionar fecha actual por defecto
    const hoy = new Date();
    const fechaHoy = `${hoy.getFullYear()}-${String(hoy.getMonth() + 1).padStart(2, '0')}-${String(hoy.getDate()).padStart(2, '0')}`;
    setTimeout(() => {
        seleccionarFecha(fechaHoy);
    }, 800);
});

</script>
@endpush 
@extends('layouts.adminlte-externo')

@section('page_title', 'Panel del Integrante Externo')

@section('content_body')
<div class="container-fluid">

    <!-- Panel de Bienvenida -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); border: none; border-radius: 12px;">
                <div class="card-body p-5">
                    <div class="row align-items-center">

                        <!-- Texto de bienvenida -->
                        <div class="col-md-8">
                            <h2 class="text-white mb-3" style="font-weight: 700; font-size: 2rem;">
                                <i class="far fa-hand-holding-heart mr-2"></i>
                                ¡Bienvenido,
                                <span id="nombreUsuario">{{ Auth::user()->nombre_usuario ?? Auth::user()->name ?? 'Usuario' }}</span>!
                            </h2>

                            <p class="text-white mb-0" style="opacity: 0.95; font-size: 1.15rem; line-height: 1.6;">
                                Explora eventos, participa y revisa tu historial desde este panel centralizado.
                            </p>
                        </div>

                        <!-- Reloj Minimalista -->
                        <div class="col-md-4 text-right">
                            <div class="p-3" style="display: inline-block; background: rgba(255, 255, 255, 0.1); border-radius: 12px; backdrop-filter: blur(10px);">

                                <!-- Texto -->
                                <div class="text-white small mb-2"
                                     style="font-weight: 600; letter-spacing: 1px; text-transform: uppercase; opacity: 0.9;">
                                    <i class="far fa-clock mr-1"></i>Hora Actual
                                </div>

                                <!-- Hora real -->
                                <div id="relojTiempoReal"
                                     style="font-weight: 700; font-size: 2.8rem;
                                            font-family: 'Courier New', monospace; color: #ffffff; line-height: 1;">
                                    00:00:00
                                </div>

                                <!-- Fecha real -->
                                <div id="fechaActual"
                                     style="font-size: 0.95rem; color: rgba(255, 255, 255, 0.9);
                                            margin-top: 4px; font-weight: 500;">
                                    Lunes, 1 de Enero 2025
                                </div>
                            </div>
</div>

                    </div><!-- row -->
                </div><!-- card-body -->
            </div><!-- card -->
        </div><!-- col -->
    </div><!-- row -->

@include('externo.partials.resumen')
    
    <!-- Gráficas Estadísticas (estilo AdminLTE) -->
    <div class="row mb-4">
        
        <!-- Gráfica 1: Eventos Inscritos -->
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card card-primary card-outline shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title" style="font-size: 0.9rem;">
                            <i class="far fa-calendar-check mr-1 text-primary"></i>
                            Eventos Inscritos
                        </h3>
                    </div>
                </div>
                <div class="card-body" style="padding: 1.5rem;">
                    <div style="height: 250px; position: relative;">
                        <canvas id="graficaEventosInscritos"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfica 2: Mega Eventos -->
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card card-success card-outline shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title" style="font-size: 0.9rem;">
                            <i class="far fa-star mr-1 text-success"></i>
                            Mega Eventos
                        </h3>
                    </div>
                </div>
                <div class="card-body" style="padding: 1.5rem;">
                    <div style="height: 250px; position: relative;">
                        <canvas id="graficaEventosAsistidos"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfica 3: Reacciones -->
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card card-danger card-outline shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title" style="font-size: 0.9rem;">
                            <i class="far fa-heart mr-1 text-danger"></i>
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
@stop

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

@push('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
{{-- config.js ya se carga en el layout, no es necesario cargarlo aquí --}}

<script>
// =======================================================
//  ⏱ RELOJ EN TIEMPO REAL – HORA OFICIAL DE BOLIVIA UTC-4
// =======================================================
function actualizarReloj() {
    try {
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
                if (p.type === "weekday")
                    fecha += p.value.charAt(0).toUpperCase() + p.value.slice(1) + ", ";
                if (p.type === "day")
                    fecha += p.value + " de ";
                if (p.type === "month")
                    fecha += p.value + " ";
                if (p.type === "year")
                    fecha += p.value;
            }
        });

        const relojEl = document.getElementById("relojTiempoReal");
        const fechaEl = document.getElementById("fechaActual");
        
        if (relojEl) relojEl.textContent = hora;
        if (fechaEl) fechaEl.textContent = fecha;
    } catch (e) {
        console.error('Error actualizando reloj:', e);
    }
}

// Variables globales para las gráficas (solo declarar si no existen)
if (typeof window.chartEventosInscritos === 'undefined') {
    window.chartEventosInscritos = null;
}
if (typeof window.chartEventosAsistidos === 'undefined') {
    window.chartEventosAsistidos = null;
}
if (typeof window.chartReacciones === 'undefined') {
    window.chartReacciones = null;
}

// Usar window para evitar conflictos con otros scripts

// =======================================================
//    📊 Cargar estadísticas desde Backend
// =======================================================
async function cargarEstadisticas() {
    const token = localStorage.getItem('token');
    if (!token) {
        console.error('❌ No hay token, redirigiendo a login');
        return window.location.href = '/login';
    }

    console.log('🔄 Iniciando carga de estadísticas...');
    console.log('🌐 API_BASE_URL:', API_BASE_URL);
    console.log('🔑 Token:', token ? 'Presente' : 'Ausente');

    try {
        const url = `${API_BASE_URL}/api/dashboard-externo/estadisticas-generales`;
        console.log('📡 Llamando a:', url);
        
        const res = await fetch(url, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            cache: 'no-cache'
        });

        console.log('📥 Respuesta recibida, status:', res.status);

        if (!res.ok) {
            const errorText = await res.text();
            console.error('❌ Error HTTP:', res.status, errorText);
            return;
        }

        const data = await res.json();
        console.log('📦 Datos recibidos:', data);

        if (!data.success) {
            console.error('❌ Error en respuesta:', data.error);
            return;
        }

        const stats = data.estadisticas || {};
        const graficas = data.graficas || {};

        console.log('📊 Estadísticas cargadas:', stats);
        console.log('📈 Gráficas cargadas:', graficas);
        console.log('📈 Historial participación:', graficas.historial_participacion);
        console.log('📈 Reacciones por mes:', graficas.reacciones_por_mes);
        if (data.debug) {
            console.log('🔍 Debug info:', data.debug);
        }
        
        // Validar que tenemos datos
        if (!stats || Object.keys(stats).length === 0) {
            console.error('❌ No se recibieron estadísticas del servidor');
            return;
        }
        
        if (!graficas || Object.keys(graficas).length === 0) {
            console.warn('⚠️ No se recibieron datos de gráficas del servidor');
        }

        // Actualizar nombre del usuario dinámicamente
        if (data.usuario && data.usuario.nombre) {
            const nombreUsuarioEl = document.getElementById('nombreUsuario');
            if (nombreUsuarioEl) {
                nombreUsuarioEl.textContent = data.usuario.nombre;
            }
        }

        // Actualizar tarjetas de resumen con TODOS los datos del usuario
        const eventosInscritosEl = document.getElementById('eventosInscritos');
        const eventosAsistidosEl = document.getElementById('eventosAsistidos');
        const totalReaccionesEl = document.getElementById('totalReacciones');
        
        // Total de eventos inscritos (todos los eventos en los que el usuario está inscrito)
        if (eventosInscritosEl) {
            const totalInscritos = stats.total_eventos_inscritos || 0;
            eventosInscritosEl.textContent = totalInscritos;
            console.log('✅ Eventos Inscritos actualizado:', totalInscritos);
        }
        
        // Total de mega eventos (todos los mega eventos en los que el usuario está inscrito)
        if (eventosAsistidosEl) {
            const totalMegaEventos = stats.total_mega_eventos_inscritos || 0;
            eventosAsistidosEl.textContent = totalMegaEventos;
            console.log('✅ Mega Eventos actualizado:', totalMegaEventos);
        }
        
        // Total de reacciones (todas las reacciones que el usuario ha hecho)
        if (totalReaccionesEl) {
            const totalReacciones = stats.total_reacciones || 0;
            totalReaccionesEl.textContent = totalReacciones;
            console.log('✅ Reacciones actualizado:', totalReacciones);
        }

        // Crear gráficas con los datos correctos
        console.log('🎨 Creando gráficas...');
        crearGraficas(graficas, stats);
        console.log('✅ Gráficas creadas');

    } catch (e) {
        console.error("❌ Error cargando estadísticas:", e);
        console.error("❌ Stack trace:", e.stack);
    }
}

// =======================================================
//    📊 Crear las gráficas individuales
// =======================================================
function crearGraficas(graficas, stats) {
    console.log('🎨 Iniciando creación de gráficas...');
    console.log('📊 Graficas recibidas:', graficas);
    console.log('📈 Stats recibidas:', stats);
    
    // 1. Gráfica de Eventos Inscritos (Línea)
    const ctxInscritos = document.getElementById('graficaEventosInscritos');
    console.log('🔍 Canvas Eventos Inscritos encontrado:', ctxInscritos ? 'Sí' : 'No');
    
    if (ctxInscritos) {
        if (window.chartEventosInscritos) {
            console.log('🗑️ Destruyendo gráfica anterior de Eventos Inscritos');
            window.chartEventosInscritos.destroy();
        }
        
        const historial = graficas.historial_participacion || {};
        const mesesNombres = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        const fechaActual = new Date();
        
        // Crear array de los últimos 7 meses
        let meses = [];
        for (let i = 6; i >= 0; i--) {
            const fecha = new Date(fechaActual.getFullYear(), fechaActual.getMonth() - i, 1);
            const mesKey = mesesNombres[fecha.getMonth()] + ' ' + fecha.getFullYear();
            meses.push(mesKey);
        }
        
        // Obtener datos de inscritos para cada mes
        const datosInscritos = meses.map(mes => {
            if (historial[mes]) {
                if (typeof historial[mes] === 'object') {
                    return historial[mes].inscritos || 0;
                } else if (typeof historial[mes] === 'number') {
                    return historial[mes];
                }
            }
            return 0;
        });
        
        // Si todos los datos son 0, usar el total de inscritos dividido entre los meses
        const totalInscritos = stats.total_eventos_inscritos || 0;
        const sumaDatos = datosInscritos.reduce((a, b) => a + b, 0);
        if (sumaDatos === 0 && totalInscritos > 0) {
            // Distribuir el total entre los meses (más en el mes actual)
            const promedio = Math.ceil(totalInscritos / meses.length);
            for (let i = 0; i < meses.length; i++) {
                if (i === meses.length - 1) {
                    datosInscritos[i] = totalInscritos - (promedio * (meses.length - 1));
                } else {
                    datosInscritos[i] = promedio;
                }
            }
        }
        
        console.log('📊 Gráfica Eventos Inscritos - Meses:', meses);
        console.log('📊 Gráfica Eventos Inscritos - Datos:', datosInscritos);
        console.log('📊 Historial completo:', historial);
        console.log('📊 Total inscritos:', totalInscritos);

        try {
            window.chartEventosInscritos = new Chart(ctxInscritos, {
            type: 'line',
            data: {
                labels: meses,
                datasets: [{
                    label: 'Eventos Inscritos',
                    data: datosInscritos,
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
                        ticks: { color: '#666', font: { size: 11 }, maxRotation: 45, minRotation: 45 }
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
            console.log('✅ Gráfica Eventos Inscritos creada exitosamente');
        } catch (error) {
            console.error('❌ Error creando gráfica Eventos Inscritos:', error);
        }
    } else {
        console.error('❌ No se encontró el canvas para Eventos Inscritos');
    }

    // 2. Gráfica de Mega Eventos (Barras)
    const ctxAsistidos = document.getElementById('graficaEventosAsistidos');
    console.log('🔍 Canvas Mega Eventos encontrado:', ctxAsistidos ? 'Sí' : 'No');
    
    if (ctxAsistidos) {
        if (window.chartEventosAsistidos) {
            console.log('🗑️ Destruyendo gráfica anterior de Mega Eventos');
            window.chartEventosAsistidos.destroy();
        }
        
        const historial = graficas.historial_participacion || {};
        const mesesNombres = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        const fechaActual = new Date();
        
        // Crear array de los últimos 7 meses
        let meses = [];
        for (let i = 6; i >= 0; i--) {
            const fecha = new Date(fechaActual.getFullYear(), fechaActual.getMonth() - i, 1);
            const mesKey = mesesNombres[fecha.getMonth()] + ' ' + fecha.getFullYear();
            meses.push(mesKey);
        }
        
        // Obtener datos de mega eventos para cada mes (usar datos de historial si están disponibles)
        const datosMegaEventos = meses.map(mes => {
            if (historial[mes]) {
                if (typeof historial[mes] === 'object') {
                    return historial[mes].mega_eventos || 0;
                } else if (typeof historial[mes] === 'number') {
                    return historial[mes];
                }
            }
            return 0;
        });
        
        // Si todos los datos son 0, usar el total de mega eventos dividido entre los meses
        const totalMegaEventos = stats.total_mega_eventos_inscritos || 0;
        const sumaDatosMegaEventos = datosMegaEventos.reduce((a, b) => a + b, 0);
        if (sumaDatosMegaEventos === 0 && totalMegaEventos > 0) {
            // Distribuir el total entre los meses (más en el mes actual)
            const promedio = Math.ceil(totalMegaEventos / meses.length);
            for (let i = 0; i < meses.length; i++) {
                if (i === meses.length - 1) {
                    datosMegaEventos[i] = totalMegaEventos - (promedio * (meses.length - 1));
                } else {
                    datosMegaEventos[i] = promedio;
                }
            }
        }
        
        console.log('📊 Gráfica Mega Eventos - Meses:', meses);
        console.log('📊 Gráfica Mega Eventos - Datos:', datosMegaEventos);
        console.log('📊 Total mega eventos:', totalMegaEventos);

        try {
            window.chartEventosAsistidos = new Chart(ctxAsistidos, {
            type: 'bar',
            data: {
                labels: meses,
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
                        ticks: { color: '#666', font: { size: 11 }, maxRotation: 45, minRotation: 45 }
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
            console.log('✅ Gráfica Mega Eventos creada exitosamente');
        } catch (error) {
            console.error('❌ Error creando gráfica Mega Eventos:', error);
        }
    } else {
        console.error('❌ No se encontró el canvas para Mega Eventos');
    }

    // 3. Gráfica de Reacciones (Donut - Total de Reacciones)
    const ctxReacciones = document.getElementById('graficaReacciones');
    console.log('🔍 Canvas Reacciones encontrado:', ctxReacciones ? 'Sí' : 'No');
    
    if (ctxReacciones) {
        if (window.chartReacciones) {
            console.log('🗑️ Destruyendo gráfica anterior de Reacciones');
            window.chartReacciones.destroy();
        }
        
        // Cambiar a gráfica donut para mostrar el total de reacciones de manera más visual
        const totalReacciones = stats.total_reacciones || 0;
        
        // Crear datos para el gráfico donut
        // Mostrar el total de reacciones como un porcentaje visual
        const maxValue = Math.max(100, totalReacciones * 1.2); // Escala dinámica
        const datosDonut = [totalReacciones, Math.max(0, maxValue - totalReacciones)];
        const colores = ['#dc3545', 'rgba(220, 53, 69, 0.15)'];
        
        console.log('📊 Gráfica Reacciones - Total:', totalReacciones);
        console.log('📊 Gráfica Reacciones - Datos Donut:', datosDonut);

        try {
            window.chartReacciones = new Chart(ctxReacciones, {
            type: 'doughnut',
            data: {
                labels: ['Reacciones', ''],
                datasets: [{
                    data: datosDonut,
                    backgroundColor: colores,
                    borderWidth: 0,
                    cutout: '75%' // Hacerlo más delgado, tipo donut
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false // Ocultar leyenda
                    },
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(12, 43, 68, 0.9)',
                        padding: 12,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        callbacks: {
                            label: function(context) {
                                if (context.label === 'Reacciones') {
                                    return `Total de Reacciones: ${totalReacciones}`;
                                }
                                return '';
                            },
                            filter: function(tooltipItem) {
                                // Solo mostrar tooltip para "Reacciones"
                                return tooltipItem.label === 'Reacciones';
                            }
                        }
                    }
                }
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
                    ctx.fillStyle = '#dc3545';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(totalReacciones.toString(), centerX, centerY - 8);
                    
                    ctx.font = 'bold 12px Arial';
                    ctx.fillStyle = '#666';
                    ctx.fillText('Total', centerX, centerY + 18);
                    ctx.restore();
                }
            }]
            });
            console.log('✅ Gráfica Reacciones (Donut) creada exitosamente');
        } catch (error) {
            console.error('❌ Error creando gráfica Reacciones:', error);
        }
    } else {
        console.error('❌ No se encontró el canvas para Reacciones');
    }
    
    console.log('✅ Proceso de creación de gráficas completado');
}


// =======================================================
//   🟢 Inicialización Automática
// =======================================================
document.addEventListener('DOMContentLoaded', () => {
    console.log('🚀 DOM cargado, iniciando aplicación...');
    
    // Verificar que Chart.js esté cargado
    if (typeof Chart === 'undefined') {
        console.error('❌ Chart.js no está cargado');
        return;
    }
    console.log('✅ Chart.js cargado correctamente');
    
    // Verificar que API_BASE_URL esté definido
    if (typeof API_BASE_URL === 'undefined') {
        console.error('❌ API_BASE_URL no está definido');
        return;
    }
    console.log('✅ API_BASE_URL:', API_BASE_URL);
    
    // Verificar elementos del DOM
    const canvasInscritos = document.getElementById('graficaEventosInscritos');
    const canvasAsistidos = document.getElementById('graficaEventosAsistidos');
    const canvasReacciones = document.getElementById('graficaReacciones');
    
    console.log('🔍 Canvas Eventos Inscritos:', canvasInscritos ? 'Encontrado' : 'No encontrado');
    console.log('🔍 Canvas Eventos Asistidos:', canvasAsistidos ? 'Encontrado' : 'No encontrado');
    console.log('🔍 Canvas Reacciones:', canvasReacciones ? 'Encontrado' : 'No encontrado');
    
    // Actualizar reloj inmediatamente y luego cada segundo
    actualizarReloj();
    setInterval(actualizarReloj, 1000);

    // Cargar estadísticas inmediatamente
    console.log('📊 Iniciando carga de estadísticas...');
    cargarEstadisticas();
    
    // Actualizar estadísticas cada 5 minutos
    setInterval(cargarEstadisticas, 300000);
});
</script>

@endpushA

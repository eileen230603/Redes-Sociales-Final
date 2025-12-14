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

    <!-- Sección: Eventos Activos - Marca tu Participación -->
    <div class="row mb-4" id="seccionEventosActivos" style="display: none;">
        <div class="col-12">
            <div class="card shadow-sm" style="border: none; border-radius: 12px; background: #fff;">
                <div class="card-header" style="background: linear-gradient(135deg, #00A36C 0%, #0C2B44 100%); border: none; border-radius: 12px 12px 0 0;">
                    <h3 class="card-title mb-0" style="color: white; font-weight: 700; font-size: 1.3rem;">
                        <i class="fas fa-check-circle mr-2"></i>
                        🟢 Eventos Activos - Marca tu Participación
                    </h3>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted mb-4">
                        Los siguientes eventos están en curso. Confirma tu participación marcando tu asistencia.
                    </p>
                    <div id="listaEventosActivos" class="row">
                        <!-- Los eventos se cargarán aquí dinámicamente -->
                    </div>
                    <div id="sinEventosActivos" class="text-center py-5" style="display: none;">
                        <i class="fas fa-calendar-check fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No tienes eventos activos en este momento.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección: Mega Eventos Recomendados -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm" style="border: none; border-radius: 12px; background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%); overflow: hidden;">
                <div class="card-header border-0" style="background: transparent; padding: 1.5rem 1.5rem 1rem;">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0" style="color: white; font-weight: 700; font-size: 1.3rem;">
                            <i class="fas fa-star mr-2"></i>
                            ⭐ Mega Eventos Recomendados para Ti
                        </h3>
                        <a href="/externo/mega-eventos" class="btn btn-sm" style="background: rgba(255,255,255,0.3); color: white; border: none; border-radius: 20px; padding: 0.5rem 1rem; font-weight: 600; backdrop-filter: blur(10px);">
                            Ver Todos <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    <p class="text-white mb-0 mt-2" style="opacity: 0.95; font-size: 0.95rem;">
                        Descubre los mega eventos más importantes y únete a la transformación
                    </p>
                </div>
                <div class="card-body p-4" style="background: white;">
                    <div id="megaEventosRecomendados" class="row">
                        <div class="col-12 text-center py-4">
                            <div class="spinner-border text-warning" role="status" style="width: 2rem; height: 2rem;">
                                <span class="sr-only">Cargando...</span>
                            </div>
                            <p class="text-muted mt-3 mb-0">Cargando mega eventos recomendados...</p>
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
    
    // Cargar eventos activos para marcar asistencia
    cargarEventosActivos();
    
    // Cargar mega eventos recomendados
    cargarMegaEventosRecomendados();
    
    // Actualizar estadísticas cada 5 minutos
    setInterval(cargarEstadisticas, 300000);
    
    // Actualizar eventos activos cada 2 minutos
    setInterval(cargarEventosActivos, 120000);
    
    // Actualizar mega eventos recomendados cada 5 minutos
    setInterval(cargarMegaEventosRecomendados, 300000);
});

// =======================================================
//   🟢 Cargar Eventos Activos para Marcar Asistencia
// =======================================================
async function cargarEventosActivos() {
    const token = localStorage.getItem('token');
    if (!token) return;

    try {
        const url = `${API_BASE_URL}/api/eventos/activos-para-marcar`;
        const res = await fetch(url, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
            },
        });

        if (!res.ok) {
            throw new Error(`HTTP ${res.status}`);
        }

        const data = await res.json();
        
        if (data.success && data.eventos && data.eventos.length > 0) {
            mostrarEventosActivos(data.eventos);
            document.getElementById('seccionEventosActivos').style.display = 'block';
            document.getElementById('sinEventosActivos').style.display = 'none';
        } else {
            document.getElementById('seccionEventosActivos').style.display = 'none';
        }
    } catch (error) {
        console.error('Error cargando eventos activos:', error);
        document.getElementById('seccionEventosActivos').style.display = 'none';
    }
}

// =======================================================
//   📋 Mostrar Eventos Activos en Cards
// =======================================================
function mostrarEventosActivos(eventos) {
    const container = document.getElementById('listaEventosActivos');
    container.innerHTML = '';

    // Función helper para formatear fechas desde PostgreSQL sin conversión de zona horaria
    const formatearFechaPostgreSQL = (fechaStr) => {
        if (!fechaStr) return 'Fecha no especificada';
        try {
            let fechaObj;
            
            if (typeof fechaStr === 'string') {
                fechaStr = fechaStr.trim();
                
                // Patrones para diferentes formatos de fecha
                const mysqlPattern = /^(\d{4})-(\d{2})-(\d{2})[\sT](\d{2}):(\d{2}):(\d{2})(?:\.\d+)?(?:Z|[+-]\d{2}:\d{2})?$/;
                const isoPattern = /^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})(?:\.\d+)?(?:Z|[+-]\d{2}:\d{2})?$/;
                
                let match = fechaStr.match(mysqlPattern) || fechaStr.match(isoPattern);
                
                if (match) {
                    // Parsear manualmente para evitar conversión UTC
                    const [, year, month, day, hour, minute, second] = match;
                    fechaObj = new Date(
                        parseInt(year, 10),
                        parseInt(month, 10) - 1,
                        parseInt(day, 10),
                        parseInt(hour, 10),
                        parseInt(minute, 10),
                        parseInt(second || 0, 10)
                    );
                } else {
                    fechaObj = new Date(fechaStr);
                }
            } else {
                fechaObj = new Date(fechaStr);
            }
            
            if (isNaN(fechaObj.getTime())) return fechaStr;
            
            const año = fechaObj.getFullYear();
            const mes = fechaObj.getMonth();
            const dia = fechaObj.getDate();
            const horas = fechaObj.getHours();
            const minutos = fechaObj.getMinutes();
            
            const meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 
                          'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
            
            const horaFormateada = String(horas).padStart(2, '0');
            const minutoFormateado = String(minutos).padStart(2, '0');
            
            return `${dia} de ${meses[mes]} de ${año}, ${horaFormateada}:${minutoFormateado}`;
        } catch (error) {
            console.error('Error formateando fecha:', error);
            return fechaStr;
        }
    };

    eventos.forEach(evento => {
        const fechaFormateada = formatearFechaPostgreSQL(evento.fecha_inicio);

        const card = document.createElement('div');
        card.className = 'col-md-6 col-lg-4 mb-3';
        card.innerHTML = `
            <div class="card shadow-sm h-100" style="border: none; border-radius: 12px; transition: all 0.3s ease;">
                <div class="card-body">
                    <h5 class="card-title mb-3" style="font-weight: 700; color: #0C2B44;">
                        ${evento.evento_titulo}
                    </h5>
                    <div class="mb-3">
                        <p class="mb-1" style="color: #666;">
                            <i class="far fa-clock mr-2"></i>
                            <strong>Inicio:</strong> ${fechaFormateada}
                        </p>
                        ${evento.ubicacion ? `
                            <p class="mb-0" style="color: #666;">
                                <i class="fas fa-map-marker-alt mr-2"></i>
                                <strong>Lugar:</strong> ${evento.ubicacion}
                                ${evento.ciudad ? `, ${evento.ciudad}` : ''}
                            </p>
                        ` : ''}
                    </div>
                    <button 
                        class="btn btn-success btn-block mt-3" 
                        onclick="marcarAsistencia(${evento.evento_id}, ${evento.participacion_id})"
                        id="btnMarcar_${evento.evento_id}"
                        style="background: linear-gradient(135deg, #00A36C 0%, #0C2B44 100%); border: none; border-radius: 8px; font-weight: 600; padding: 0.75rem;">
                        <i class="fas fa-check-circle mr-2"></i>
                        Marcar Asistencia
                    </button>
                    <div id="mensajeExito_${evento.evento_id}" class="alert alert-success mt-3" style="display: none; border-radius: 8px;">
                        <i class="fas fa-check-circle mr-2"></i>
                        ¡Gracias por participar! Tu asistencia fue registrada correctamente.
                    </div>
                </div>
            </div>
        `;
        container.appendChild(card);
    });
}

// =======================================================
//   ✅ Marcar Asistencia del Usuario
// =======================================================
async function marcarAsistencia(eventoId, participacionId) {
    const token = localStorage.getItem('token');
    if (!token) {
        alert('Debes iniciar sesión para marcar asistencia');
        return;
    }

    const btnMarcar = document.getElementById(`btnMarcar_${eventoId}`);
    const mensajeExito = document.getElementById(`mensajeExito_${eventoId}`);

    // Deshabilitar botón mientras se procesa
    btnMarcar.disabled = true;
    btnMarcar.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Procesando...';

    try {
        const url = `${API_BASE_URL}/api/eventos/${eventoId}/marcar-asistencia`;
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
            },
        });

        const data = await res.json();

        if (data.success) {
            // Ocultar botón y mostrar mensaje de éxito
            btnMarcar.style.display = 'none';
            mensajeExito.style.display = 'block';
            
            // Recargar la lista después de 2 segundos
            setTimeout(() => {
                cargarEventosActivos();
            }, 2000);
        } else {
            alert(data.error || 'Error al marcar asistencia');
            btnMarcar.disabled = false;
            btnMarcar.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Marcar Asistencia';
        }
    } catch (error) {
        console.error('Error marcando asistencia:', error);
        alert('Error al marcar asistencia. Por favor, intenta nuevamente.');
        btnMarcar.disabled = false;
        btnMarcar.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Marcar Asistencia';
    }
}

// =======================================================
//   ⭐ Cargar Mega Eventos Recomendados
// =======================================================
async function cargarMegaEventosRecomendados() {
    const token = localStorage.getItem('token');
    const container = document.getElementById('megaEventosRecomendados');
    
    if (!container) return;

    try {
        const url = `${API_BASE_URL}/api/mega-eventos/publicos`;
        const res = await fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                ...(token ? { 'Authorization': `Bearer ${token}` } : {})
            }
        });

        const data = await res.json();
        
        if (data.success && data.mega_eventos && data.mega_eventos.length > 0) {
            // Filtrar solo los próximos y ordenar por fecha
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
                        month: 'long', 
                        year: 'numeric' 
                    });
                    
                    // Obtener primera imagen
                    let imagenUrl = null;
                    if (mega.imagenes && Array.isArray(mega.imagenes) && mega.imagenes.length > 0) {
                        imagenUrl = mega.imagenes[0];
                    } else if (typeof mega.imagenes === 'string' && mega.imagenes.trim()) {
                        try {
                            const parsed = JSON.parse(mega.imagenes);
                            if (Array.isArray(parsed) && parsed.length > 0) {
                                imagenUrl = parsed[0];
                            }
                        } catch (e) {
                            imagenUrl = mega.imagenes;
                        }
                    }
                    
                    if (imagenUrl && !imagenUrl.startsWith('http')) {
                        const apiUrl = window.API_BASE_URL || 'http://192.168.0.7:8000';
                        if (imagenUrl.startsWith('/storage/')) {
                            imagenUrl = `${apiUrl}${imagenUrl}`;
                        } else {
                            imagenUrl = `${apiUrl}/storage/${imagenUrl}`;
                        }
                    }
                    
                    html += `
                        <div class="col-md-4 mb-3">
                            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; overflow: hidden; transition: all 0.3s; cursor: pointer;" 
                                 onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.15)';" 
                                 onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.1)';"
                                 onclick="window.location.href='/externo/mega-eventos/${mega.mega_evento_id}/detalle'">
                                ${imagenUrl ? `
                                    <div style="height: 180px; overflow: hidden; position: relative;">
                                        <img src="${imagenUrl}" alt="${mega.titulo}" class="w-100 h-100" style="object-fit: cover;"
                                             onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div style="display: none; height: 180px; background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%); align-items: center; justify-content: center;">
                                            <i class="fas fa-star text-white" style="font-size: 3rem; opacity: 0.5;"></i>
                                        </div>
                                        <div class="position-absolute" style="top: 10px; right: 10px;">
                                            <span class="badge" style="background: rgba(255, 215, 0, 0.95); color: #0C2B44; font-weight: 700; padding: 0.4rem 0.7rem; border-radius: 20px;">
                                                <i class="fas fa-star mr-1"></i>Mega
                                            </span>
                                        </div>
                                    </div>
                                ` : `
                                    <div style="height: 180px; background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%); display: flex; align-items: center; justify-content: center; position: relative;">
                                        <i class="fas fa-star text-white" style="font-size: 3rem; opacity: 0.5;"></i>
                                        <div class="position-absolute" style="top: 10px; right: 10px;">
                                            <span class="badge" style="background: rgba(255, 255, 255, 0.95); color: #0C2B44; font-weight: 700; padding: 0.4rem 0.7rem; border-radius: 20px;">
                                                <i class="fas fa-star mr-1"></i>Mega
                                            </span>
                                        </div>
                                    </div>
                                `}
                                <div class="card-body p-4">
                                    <h5 class="card-title mb-3" style="font-weight: 700; color: #0C2B44; font-size: 1.1rem; line-height: 1.4; min-height: 3rem;">
                                        <i class="fas fa-star mr-2" style="color: #FFD700; font-size: 0.9rem;"></i>${mega.titulo || 'Mega Evento'}
                                    </h5>
                                    <p class="text-muted mb-3" style="font-size: 0.9rem; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 2.5rem;">
                                        ${mega.descripcion || 'Descubre este increíble mega evento que está transformando comunidades.'}
                                    </p>
                                    <div class="mb-3">
                                        <p class="mb-1" style="font-size: 0.85rem; color: #666;">
                                            <i class="far fa-calendar mr-2" style="color: #FFD700;"></i>${fechaStr}
                                        </p>
                                        ${mega.ubicacion ? `
                                            <p class="mb-0" style="font-size: 0.85rem; color: #666;">
                                                <i class="far fa-map-marker-alt mr-2" style="color: #FFD700;"></i>${mega.ubicacion}
                                            </p>
                                        ` : ''}
                                    </div>
                                    <a href="/externo/mega-eventos/${mega.mega_evento_id}/detalle" 
                                       class="btn btn-block" 
                                       style="background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%); color: white; border: none; border-radius: 8px; padding: 0.75rem; font-weight: 600;">
                                        <i class="fas fa-star mr-2"></i>Ver Detalle
                                    </a>
                                </div>
                            </div>
                        </div>
                    `;
                });
                container.innerHTML = html;
            } else {
                container.innerHTML = `
                    <div class="col-12">
                        <div class="text-center py-4">
                            <i class="fas fa-star fa-3x mb-3" style="color: #FFD700; opacity: 0.3;"></i>
                            <p class="text-muted mb-0">No hay mega eventos próximos disponibles</p>
                            <a href="/externo/mega-eventos" class="btn btn-sm mt-3" style="background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%); color: white; border: none; border-radius: 8px; padding: 0.5rem 1.5rem;">
                                <i class="fas fa-search mr-2"></i>Explorar Mega Eventos
                            </a>
                        </div>
                    </div>
                `;
            }
        } else {
            container.innerHTML = `
                <div class="col-12">
                    <div class="text-center py-4">
                        <i class="fas fa-star fa-3x mb-3" style="color: #FFD700; opacity: 0.3;"></i>
                        <p class="text-muted mb-0">No hay mega eventos disponibles</p>
                    </div>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error cargando mega eventos recomendados:', error);
        container.innerHTML = `
            <div class="col-12">
                <div class="text-center py-4">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2 text-warning"></i>
                    <p class="text-muted mb-0">Error al cargar mega eventos. Por favor, intenta más tarde.</p>
                </div>
            </div>
        `;
    }
}
</script>

@endpush

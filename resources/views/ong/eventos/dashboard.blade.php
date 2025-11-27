@extends('layouts.adminlte')

@section('page_title', 'Dashboard de Eventos')

@section('content_body')
<div class="container-fluid">
    <!-- Estadísticas de Eventos -->
    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card stat-card" style="background: linear-gradient(135deg, #0C2B44 0%, #0a2338 100%); border: none; border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white text-uppercase mb-2 stat-label" style="font-size: 0.75rem; font-weight: 600; opacity: 0.9; letter-spacing: 0.5px;">Total de Eventos</h6>
                            <h2 class="mb-0 text-white stat-number" id="statTotal" style="font-size: 2.5rem; font-weight: 700; line-height: 1;">0</h2>
                        </div>
                        <div class="text-right stat-icon-container">
                            <i class="far fa-calendar fa-3x text-white stat-icon" style="opacity: 0.2;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card stat-card" style="background: linear-gradient(135deg, #00A36C 0%, #008a5a 100%); border: none; border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white text-uppercase mb-2 stat-label" style="font-size: 0.75rem; font-weight: 600; opacity: 0.9; letter-spacing: 0.5px;">Eventos Finalizados</h6>
                            <h2 class="mb-0 text-white stat-number" id="statFinalizados" style="font-size: 2.5rem; font-weight: 700; line-height: 1;">0</h2>
                        </div>
                        <div class="text-right stat-icon-container">
                            <i class="far fa-check-circle fa-3x text-white stat-icon" style="opacity: 0.2;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card stat-card" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); border: none; border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white text-uppercase mb-2 stat-label" style="font-size: 0.75rem; font-weight: 600; opacity: 0.9; letter-spacing: 0.5px;">Eventos en Curso</h6>
                            <h2 class="mb-0 text-white stat-number" id="statEnCurso" style="font-size: 2.5rem; font-weight: 700; line-height: 1;">0</h2>
                        </div>
                        <div class="text-right stat-icon-container">
                            <i class="far fa-play-circle fa-3x text-white stat-icon" style="opacity: 0.2;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card stat-card" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); border: none; border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white text-uppercase mb-2 stat-label" style="font-size: 0.75rem; font-weight: 600; opacity: 0.9; letter-spacing: 0.5px;">Eventos Próximos</h6>
                            <h2 class="mb-0 text-white stat-number" id="statProximos" style="font-size: 2.5rem; font-weight: 700; line-height: 1;">0</h2>
                        </div>
                        <div class="text-right stat-icon-container">
                            <i class="far fa-clock fa-3x text-white stat-icon" style="opacity: 0.2;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos de Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-6 mb-4">
            <div class="card" style="border-radius: 12px; border: 1px solid #F5F5F5;">
                <div class="card-header" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); color: white; border-radius: 12px 12px 0 0;">
                    <h3 class="card-title" style="color: white; margin: 0; font-weight: 600;">
                        <i class="far fa-chart-pie mr-2"></i>Eventos por Estado
                    </h3>
                </div>
                <div class="card-body p-4">
                    <canvas id="graficoPastelEstados" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card" style="border-radius: 12px; border: 1px solid #F5F5F5;">
                <div class="card-header" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); color: white; border-radius: 12px 12px 0 0;">
                    <h3 class="card-title" style="color: white; margin: 0; font-weight: 600;">
                        <i class="far fa-chart-bar mr-2"></i>Eventos por Tipo
                    </h3>
                </div>
                <div class="card-body p-4">
                    <canvas id="graficoBarrasEstados" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros y Búsqueda -->
    <div class="card mb-4" style="border-radius: 12px; border: 1px solid #F5F5F5;">
        <div class="card-header" style="background: #F5F5F5; border-bottom: 1px solid #F5F5F5; border-radius: 12px 12px 0 0;">
            <h3 class="card-title mb-0" style="color: #0C2B44; font-weight: 700;">
                <i class="far fa-sliders-h mr-2" style="color: #00A36C;"></i>Filtros
            </h3>
        </div>
        <div class="card-body" style="padding: 1.5rem;">
            <div class="row">
                <div class="col-md-4 mb-3 mb-md-0">
                    <label for="filtroEstado" class="form-label" style="color: #0C2B44; font-weight: 600; margin-bottom: 0.75rem;">
                        <i class="far fa-info-circle mr-2" style="color: #00A36C;"></i>Filtrar por Estado
                    </label>
                    <select id="filtroEstado" class="form-control" style="border-radius: 8px; padding: 0.75rem;">
                        <option value="todos">Todos los eventos</option>
                        <option value="finalizados">Finalizados</option>
                        <option value="en_curso">En Curso</option>
                        <option value="activos">Activos</option>
                        <option value="proximos">Próximos</option>
                        <option value="cancelados">Cancelados</option>
                        <option value="borradores">Borradores</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3 mb-md-0">
                    <label for="buscador" class="form-label" style="color: #0C2B44; font-weight: 600; margin-bottom: 0.75rem;">
                        <i class="far fa-search mr-2" style="color: #00A36C;"></i>Buscar
                    </label>
                    <input type="text" id="buscador" class="form-control" placeholder="Buscar por título..." style="border-radius: 8px; padding: 0.75rem;">
                </div>
                <div class="col-md-2">
                    <label class="form-label d-block" style="color: #0C2B44; font-weight: 600; margin-bottom: 0.75rem;">&nbsp;</label>
                    <button id="btnLimpiar" class="btn btn-block" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); color: white; border: none; border-radius: 8px; padding: 0.75rem; font-weight: 500; transition: all 0.3s;">
                        <i class="far fa-times-circle mr-2"></i>Limpiar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Eventos -->
    <div class="card" style="border-radius: 12px; border: 1px solid #F5F5F5;">
        <div class="card-header" style="background: #F5F5F5; border-bottom: 1px solid #F5F5F5; border-radius: 12px 12px 0 0;">
            <h3 class="card-title mb-0" style="color: #0C2B44; font-weight: 700;">
                <i class="far fa-list mr-2" style="color: #00A36C;"></i>Lista de Eventos
            </h3>
        </div>
        <div class="card-body" style="padding: 1.5rem;">
            <div id="eventosContainer" class="row">
                <!-- Los eventos se cargarán aquí -->
            </div>

            <!-- Mensaje cuando no hay eventos -->
            <div id="mensajeVacio" class="text-center py-5 d-none">
                <div style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); width: 100px; height: 100px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                    <i class="far fa-calendar-times fa-3x text-white"></i>
                </div>
                <h4 style="color: #0C2B44; font-weight: 600;">No hay eventos que mostrar</h4>
                <p style="color: #333333;">Intenta cambiar los filtros o crear un nuevo evento.</p>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border-radius: 12px !important;
        border: 1px solid #F5F5F5 !important;
        box-shadow: 0 2px 8px rgba(12, 43, 68, 0.08) !important;
        transition: all 0.3s ease !important;
    }
    
    .card:hover {
        box-shadow: 0 4px 16px rgba(12, 43, 68, 0.15) !important;
        transform: translateY(-2px) !important;
    }
    
    /* Estilos para botones con gradiente */
    button[style*="linear-gradient"], a[style*="linear-gradient"] {
        transition: all 0.3s ease !important;
    }
    
    button[style*="linear-gradient"]:hover, a[style*="linear-gradient"]:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 12px rgba(0, 163, 108, 0.3) !important;
        opacity: 0.9 !important;
    }
    
    .evento-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #F5F5F5;
    }
    
    .evento-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 16px rgba(12, 43, 68, 0.15);
        border-color: #00A36C;
    }
    
    .badge-estado {
        font-size: 0.75rem;
        padding: 0.4em 0.8em;
        border-radius: 20px;
        font-weight: 500;
    }
    
    /* Estilos para los gráficos */
    #graficoPastelEstados,
    #graficoBarrasEstados {
        max-height: 300px;
    }
    
    /* ============================================
       ANIMACIONES PARA TARJETAS DE ESTADÍSTICAS
       ============================================ */
    
    /* Animación de entrada para las tarjetas */
    .stat-card {
        opacity: 0;
        transform: translateY(30px) scale(0.95);
        animation: slideInUp 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        position: relative;
        overflow: hidden;
    }
    
    .stat-card:nth-child(1) {
        animation-delay: 0s;
    }
    
    .stat-card:nth-child(2) {
        animation-delay: 0.1s;
    }
    
    .stat-card:nth-child(3) {
        animation-delay: 0.2s;
    }
    
    .stat-card:nth-child(4) {
        animation-delay: 0.3s;
    }
    
    @keyframes slideInUp {
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
    
    /* Efecto de brillo sutil en hover */
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(
            90deg,
            transparent,
            rgba(255, 255, 255, 0.15),
            transparent
        );
        transition: left 0.6s ease;
        z-index: 1;
    }
    
    .stat-card:hover::before {
        left: 100%;
    }
    
    /* Animación de hover para las tarjetas */
    .stat-card {
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        cursor: pointer;
    }
    
    .stat-card:hover {
        transform: translateY(-8px) scale(1.03) !important;
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.25) !important;
    }
    
    /* Animación de los iconos */
    .stat-icon {
        transition: all 0.4s ease;
        animation: floatIcon 3s ease-in-out infinite;
    }
    
    .stat-card:hover .stat-icon {
        transform: scale(1.2) rotate(5deg) !important;
        opacity: 0.4 !important;
    }
    
    @keyframes floatIcon {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-10px);
        }
    }
    
    /* Animación de entrada para los labels */
    .stat-label {
        opacity: 0;
        animation: fadeInUp 0.8s ease forwards;
    }
    
    .stat-card:nth-child(1) .stat-label {
        animation-delay: 0.2s;
    }
    
    .stat-card:nth-child(2) .stat-label {
        animation-delay: 0.3s;
    }
    
    .stat-card:nth-child(3) .stat-label {
        animation-delay: 0.4s;
    }
    
    .stat-card:nth-child(4) .stat-label {
        animation-delay: 0.5s;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 0.9;
            transform: translateY(0);
        }
    }
    
    /* Animación de pulso sutil para los números */
    .stat-number {
        position: relative;
        display: inline-block;
        animation: numberGlow 2s ease-in-out infinite;
    }
    
    @keyframes numberGlow {
        0%, 100% {
            text-shadow: 0 0 5px rgba(255, 255, 255, 0.3);
        }
        50% {
            text-shadow: 0 0 15px rgba(255, 255, 255, 0.6), 0 0 25px rgba(255, 255, 255, 0.4);
    }
    }
    
    /* Animación de contador para los números */
    .stat-number.counting {
        animation: numberPop 0.5s ease, numberGlow 2s ease-in-out infinite;
    }
    
    @keyframes numberPop {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.15);
        }
        100% {
            transform: scale(1);
        }
    }
    
    /* Efecto de gradiente animado para la última tarjeta */
    .stat-card:last-child {
        background-size: 200% 200%;
        animation: gradientShift 5s ease infinite, slideInUp 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
    }
    
    @keyframes gradientShift {
        0% {
            background-position: 0% 50%;
        }
        50% {
            background-position: 100% 50%;
        }
        100% {
            background-position: 0% 50%;
        }
    }
    
    /* Animación de entrada para el contenedor de iconos */
    .stat-icon-container {
        animation: iconSlideIn 0.8s ease forwards;
        opacity: 0;
        transform: translateX(20px);
    }
    
    .stat-card:nth-child(1) .stat-icon-container {
        animation-delay: 0.4s;
    }
    
    .stat-card:nth-child(2) .stat-icon-container {
        animation-delay: 0.5s;
    }
    
    .stat-card:nth-child(3) .stat-icon-container {
        animation-delay: 0.6s;
    }
    
    .stat-card:nth-child(4) .stat-icon-container {
        animation-delay: 0.7s;
    }
    
    @keyframes iconSlideIn {
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    /* Animación de ondas para el fondo de las tarjetas */
    .stat-card::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        transform: translate(-50%, -50%);
        animation: ripple 3s ease-out infinite;
        z-index: 0;
    }
    
    @keyframes ripple {
        0% {
            width: 0;
            height: 0;
            opacity: 0.8;
        }
        100% {
            width: 300px;
            height: 300px;
            opacity: 0;
        }
    }
    
    /* Asegurar que el contenido esté por encima de las animaciones */
    .stat-card .card-body {
        position: relative;
        z-index: 2;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="{{ asset('assets/js/config.js') }}"></script>
<script>
let chartPastelEstados = null;
let chartBarrasEstados = null;

document.addEventListener('DOMContentLoaded', function() {
    const token = localStorage.getItem('token');
    const tipoUsuario = localStorage.getItem('tipo_usuario');
    // Para ONG, id_entidad es igual a id_usuario (ambos son el user_id)
    const ongId = localStorage.getItem('id_entidad') || localStorage.getItem('id_usuario');
    
    // Validar que el usuario sea ONG y tenga ID válido
    if (!token || tipoUsuario !== 'ONG' || !ongId) {
        Swal.fire('Error', 'Debes iniciar sesión como ONG para acceder a este dashboard.', 'error').then(() => {
            window.location.href = '/login';
        });
        return;
    }
    
    let filtroEstado = 'todos';
    let buscar = '';

    // Cargar eventos
    function cargarEventos() {
        const params = new URLSearchParams();
        if (filtroEstado !== 'todos') {
            params.append('estado', filtroEstado);
        }
        if (buscar) {
            params.append('buscar', buscar);
        }

        fetch(`${API_BASE_URL}/api/eventos/ong/${ongId}/dashboard?${params.toString()}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        })
        .then(res => {
            if (!res.ok) {
                return res.json().then(data => {
                    throw new Error(data.error || `Error ${res.status}: ${res.statusText}`);
                });
            }
            return res.json();
        })
        .then(data => {
            if (data.success) {
                actualizarEstadisticas(data.estadisticas);
                mostrarEventos(data.eventos);
            } else {
                console.error('Error del servidor:', data.error);
                Swal.fire('Error', data.error || 'No se pudieron cargar los eventos', 'error');
                // Si es error de autenticación, redirigir al login
                if (data.error && (data.error.includes('autenticado') || data.error.includes('permiso'))) {
                    setTimeout(() => {
                        window.location.href = '/login';
                    }, 2000);
                }
            }
        })
        .catch(err => {
            console.error('Error:', err);
            Swal.fire('Error', err.message || 'No se pudieron cargar los eventos. Verifica tu conexión.', 'error');
        });
    }

    function actualizarEstadisticas(stats) {
        // Animación de contador para los números
        animarContador('statTotal', stats.total || 0);
        animarContador('statFinalizados', stats.finalizados || 0);
        animarContador('statEnCurso', stats.en_curso || 0);
        animarContador('statProximos', stats.proximos || 0);
        
        // Actualizar gráficos
        actualizarGraficos(stats);
    }
    
    // Función para animar el contador
    function animarContador(elementId, valorFinal) {
        const elemento = document.getElementById(elementId);
        if (!elemento) return;
        
        const valorInicial = parseInt(elemento.textContent) || 0;
        const duracion = 1500; // 1.5 segundos
        const incremento = valorFinal / (duracion / 16); // 60 FPS
        let valorActual = valorInicial;
        
        // Agregar clase de animación
        elemento.classList.add('counting');
        
        const intervalo = setInterval(() => {
            valorActual += incremento;
            
            if ((incremento > 0 && valorActual >= valorFinal) || 
                (incremento < 0 && valorActual <= valorFinal)) {
                elemento.textContent = valorFinal;
                clearInterval(intervalo);
                // Remover clase después de la animación
                setTimeout(() => {
                    elemento.classList.remove('counting');
                }, 500);
            } else {
                elemento.textContent = Math.floor(valorActual);
            }
        }, 16);
    }

    function actualizarGraficos(stats) {
        // Nueva Paleta de Colores
        const coloresPaleta = {
            finalizados: '#00A36C',     // verde esmeralda
            en_curso: '#0C2B44',        // azul marino
            proximos: '#00A36C',        // verde esmeralda
            cancelados: '#dc3545',      // rojo
            borradores: '#ffc107'       // amarillo
        };

        // Gráfico de Pastel - Distribución de Estados
        const ctxPastel = document.getElementById('graficoPastelEstados');
        if (chartPastelEstados) {
            chartPastelEstados.destroy();
        }
        
        const datosPastel = {
            finalizados: stats.finalizados || 0,
            en_curso: stats.en_curso || 0,
            proximos: stats.proximos || 0,
            cancelados: stats.cancelados || 0,
            borradores: stats.borradores || 0
        };

        // Filtrar estados con valor 0 para un gráfico más limpio
        const labelsPastel = [];
        const dataPastel = [];
        const coloresPastel = [];
        
        if (datosPastel.finalizados > 0) {
            labelsPastel.push('Finalizados');
            dataPastel.push(datosPastel.finalizados);
            coloresPastel.push(coloresPaleta.finalizados);
        }
        if (datosPastel.en_curso > 0) {
            labelsPastel.push('En Curso');
            dataPastel.push(datosPastel.en_curso);
            coloresPastel.push(coloresPaleta.en_curso);
        }
        if (datosPastel.proximos > 0) {
            labelsPastel.push('Próximos');
            dataPastel.push(datosPastel.proximos);
            coloresPastel.push(coloresPaleta.proximos);
        }
        if (datosPastel.cancelados > 0) {
            labelsPastel.push('Cancelados');
            dataPastel.push(datosPastel.cancelados);
            coloresPastel.push(coloresPaleta.cancelados);
        }
        if (datosPastel.borradores > 0) {
            labelsPastel.push('Borradores');
            dataPastel.push(datosPastel.borradores);
            coloresPastel.push(coloresPaleta.borradores);
        }

        if (dataPastel.length > 0) {
            chartPastelEstados = new Chart(ctxPastel, {
                type: 'doughnut',
                data: {
                    labels: labelsPastel,
                    datasets: [{
                        data: dataPastel,
                        backgroundColor: coloresPastel,
                        borderColor: '#ffffff',
                        borderWidth: 3,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    cutout: '60%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                usePointStyle: true,
                                font: {
                                    size: 12,
                                    family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 13
                            },
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        } else {
            // Mostrar mensaje si no hay datos
            ctxPastel.getContext('2d').clearRect(0, 0, ctxPastel.width, ctxPastel.height);
            const ctx = ctxPastel.getContext('2d');
            ctx.font = '16px Arial';
            ctx.fillStyle = '#6c757d';
            ctx.textAlign = 'center';
            ctx.fillText('No hay datos para mostrar', ctxPastel.width / 2, ctxPastel.height / 2);
        }

        // Gráfico de Barras - Comparación por Estado
        const ctxBarras = document.getElementById('graficoBarrasEstados');
        if (chartBarrasEstados) {
            chartBarrasEstados.destroy();
        }

        const labelsBarras = ['Finalizados', 'En Curso', 'Próximos', 'Cancelados', 'Borradores'];
        const dataBarras = [
            datosPastel.finalizados,
            datosPastel.en_curso,
            datosPastel.proximos,
            datosPastel.cancelados,
            datosPastel.borradores
        ];
        const coloresBarras = [
            coloresPaleta.finalizados,
            coloresPaleta.en_curso,
            coloresPaleta.proximos,
            coloresPaleta.cancelados,
            coloresPaleta.borradores
        ];

        chartBarrasEstados = new Chart(ctxBarras, {
            type: 'bar',
            data: {
                labels: labelsBarras,
                datasets: [{
                    label: 'Cantidad de Eventos',
                    data: dataBarras,
                    backgroundColor: coloresBarras,
                    borderColor: coloresBarras.map(c => c),
                    borderWidth: 1,
                    borderRadius: 4,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        callbacks: {
                            label: function(context) {
                                return `Eventos: ${context.parsed.y}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            font: {
                                size: 11
                            },
                            color: '#6c757d'
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 11
                            },
                            color: '#6c757d'
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    function mostrarEventos(eventos) {
        const container = document.getElementById('eventosContainer');
        const mensajeVacio = document.getElementById('mensajeVacio');

        if (eventos.length === 0) {
            container.innerHTML = '';
            mensajeVacio.classList.remove('d-none');
            return;
        }

        mensajeVacio.classList.add('d-none');
        container.innerHTML = eventos.map(e => crearCardEvento(e)).join('');
    }

    function crearCardEvento(e) {
        const imagen = e.imagenes && e.imagenes.length > 0 
            ? e.imagenes[0] 
            : null;
        
        // Usar estado_dinamico si está disponible
        const estadoParaBadge = e.estado_dinamico || e.estado;
        const estadoBadge = obtenerBadgeEstado(estadoParaBadge);
        const fechaInicio = e.fecha_inicio ? new Date(e.fecha_inicio).toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        }) : 'No especificada';
        const fechaFin = e.fecha_fin ? new Date(e.fecha_fin).toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        }) : 'No especificada';

        return `
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card evento-card">
                    ${imagen ? `
                        <div style="height: 200px; overflow: hidden; background: #f8f9fa;">
                            <img src="${imagen}" class="w-100 h-100" style="object-fit: cover;" onerror="this.style.display='none'">
                        </div>
                    ` : `
                        <div style="height: 200px; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); display: flex; align-items: center; justify-content: center;">
                            <i class="far fa-calendar fa-4x text-white" style="opacity: 0.3;"></i>
                        </div>
                    `}
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title mb-0" style="font-size: 1.1rem; font-weight: 700; color: #0C2B44;">${e.titulo || 'Sin título'}</h5>
                            ${estadoBadge}
                        </div>
                        <p class="card-text" style="font-size: 0.9rem; line-height: 1.6; color: #333333; margin-bottom: 1rem;">
                            ${(e.descripcion || 'Sin descripción').substring(0, 120)}${e.descripcion && e.descripcion.length > 120 ? '...' : ''}
                        </p>
                        <div class="mt-3 pt-3" style="border-top: 1px solid #F5F5F5;">
                            <small class="d-block mb-2" style="color: #333333;">
                                <i class="far fa-calendar mr-2" style="color: #00A36C;"></i><strong>Inicio:</strong> ${fechaInicio}
                            </small>
                            <small class="d-block" style="color: #333333;">
                                <i class="far fa-calendar-check mr-2" style="color: #00A36C;"></i><strong>Fin:</strong> ${fechaFin}
                            </small>
                        </div>
                        <div class="mt-3">
                            <a href="/ong/eventos/${e.id}/detalle" class="btn btn-sm btn-block" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); color: white; border: none; border-radius: 8px; font-weight: 500; transition: all 0.3s;">
                                <i class="far fa-eye mr-2"></i>Ver Detalles
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function obtenerBadgeEstado(estadoDinamico) {
        // Usar el estado dinámico que viene del backend con nueva paleta
        switch(estadoDinamico) {
            case 'finalizado':
                return '<span class="badge badge-estado" style="background: #6c757d; color: white;">Finalizado</span>';
            case 'activo':
                return '<span class="badge badge-estado" style="background: #00A36C; color: white;">En Curso</span>';
            case 'proximo':
                return '<span class="badge badge-estado" style="background: #0C2B44; color: white;">Próximo</span>';
            case 'cancelado':
                return '<span class="badge badge-estado" style="background: #dc3545; color: white;">Cancelado</span>';
            case 'borrador':
                return '<span class="badge badge-estado" style="background: #ffc107; color: #333333;">Borrador</span>';
            default:
                return '<span class="badge badge-estado" style="background: #6c757d; color: white;">' + estadoDinamico + '</span>';
        }
    }

    // Event listeners
    document.getElementById('filtroEstado').addEventListener('change', function() {
        filtroEstado = this.value;
        cargarEventos();
    });

    let debounceTimer;
    document.getElementById('buscador').addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            buscar = this.value;
            cargarEventos();
        }, 500);
    });

    document.getElementById('btnLimpiar').addEventListener('click', function() {
        filtroEstado = 'todos';
        buscar = '';
        document.getElementById('filtroEstado').value = 'todos';
        document.getElementById('buscador').value = '';
        cargarEventos();
    });

    // Cargar inicial
    cargarEventos();
});
</script>
{{-- Script global para icono de notificaciones --}}
<script src="{{ asset('js/notificaciones-ong.js') }}"></script>
@stop

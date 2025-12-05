@extends('layouts.adminlte')

@section('page_title', 'Dashboard de Eventos')

@section('content_body')
<div class="container-fluid">
    <!-- Estadísticas de Eventos -->
    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="small-box bg-primary stat-card-custom">
                <div class="inner">
                    <h3 id="statTotal" class="text-white">0</h3>
                    <p class="text-white">Total de Eventos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="small-box bg-success stat-card-custom">
                <div class="inner">
                    <h3 id="statFinalizados" class="text-white">0</h3>
                    <p class="text-white">Eventos Finalizados</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="small-box bg-info stat-card-custom">
                <div class="inner">
                    <h3 id="statEnCurso" class="text-white">0</h3>
                    <p class="text-white">Eventos en Curso</p>
                </div>
                <div class="icon">
                    <i class="fas fa-play-circle"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="small-box bg-warning stat-card-custom">
                <div class="inner">
                    <h3 id="statProximos" class="text-white">0</h3>
                    <p class="text-white">Eventos Próximos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Métricas Agregadas -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="small-box bg-info stat-card-custom">
                <div class="inner">
                    <h3 id="statTotalParticipantes" class="text-white">0</h3>
                    <p class="text-white">Total Participantes</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="small-box bg-danger stat-card-custom">
                <div class="inner">
                    <h3 id="statTotalReacciones" class="text-white">0</h3>
                    <p class="text-white">Total Reacciones</p>
                </div>
                <div class="icon">
                    <i class="fas fa-heart"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="small-box bg-warning stat-card-custom">
                <div class="inner">
                    <h3 id="statTotalCompartidos" class="text-white">0</h3>
                    <p class="text-white">Total Compartidos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-share-alt"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="small-box bg-secondary stat-card-custom">
                <div class="inner">
                    <h3 id="statVoluntariosUnicos" class="text-white">0</h3>
                    <p class="text-white">Voluntarios Únicos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-friends"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- KPIs y Métricas de Rendimiento -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-chart-line fa-2x mb-2 text-info"></i>
                    <h6 class="mb-2 font-weight-bold text-dark">Promedio Participantes</h6>
                    <h3 class="mb-0 font-weight-bold text-info" id="statPromedioParticipantes">0</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-percentage fa-2x mb-2 text-success"></i>
                    <h6 class="mb-2 font-weight-bold text-dark">Tasa de Asistencia</h6>
                    <h3 class="mb-0 font-weight-bold text-success" id="statTasaAsistencia">0%</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-fire fa-2x mb-2 text-warning"></i>
                    <h6 class="mb-2 font-weight-bold text-dark">Engagement Rate</h6>
                    <h3 class="mb-0 font-weight-bold text-warning" id="statEngagementRate">0%</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fa-2x mb-2 text-primary"></i>
                    <h6 class="mb-2 font-weight-bold text-dark">Tasa Finalización</h6>
                    <h3 class="mb-0 font-weight-bold text-primary" id="statTasaFinalizacion">0%</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Eventos Destacados -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary">
                    <h3 class="card-title mb-0 text-white">
                        <i class="fas fa-star mr-2"></i>Eventos Destacados
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row" id="eventosDestacadosContainer">
                        <!-- Se llenará con JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos de Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-info">
                    <h3 class="card-title mb-0 text-white">
                        <i class="fas fa-chart-pie mr-2"></i>Eventos por Estado
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="graficoPastelEstados" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-success">
                    <h3 class="card-title mb-0 text-white">
                        <i class="fas fa-chart-bar mr-2"></i>Eventos por Tipo
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="graficoBarrasEstados" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos Adicionales -->
    <div class="row mb-4">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-warning">
                    <h3 class="card-title mb-0 text-white">
                        <i class="fas fa-calendar-alt mr-2"></i>Eventos Creados por Mes
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="graficoEventosPorMes" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-danger">
                    <h3 class="card-title mb-0 text-white">
                        <i class="fas fa-trophy mr-2"></i>Top 5 por Participación
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="graficoTop5Participacion" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla Resumen de Eventos -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary">
                    <h3 class="card-title mb-0 text-white">
                        <i class="fas fa-table mr-2"></i>Resumen de Eventos con Métricas
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="tablaResumenEventos">
                            <thead>
                                <tr>
                                    <th>Evento</th>
                                    <th>Estado</th>
                                    <th class="text-center">Participantes</th>
                                    <th class="text-center">Reacciones</th>
                                    <th class="text-center">Compartidos</th>
                                    <th class="text-center">Tasa Asistencia</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tablaResumenBody">
                                <!-- Se llenará con JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros y Búsqueda -->
    <div class="card mb-4">
        <div class="card-header bg-secondary">
            <h3 class="card-title mb-0 text-white">
                <i class="fas fa-filter mr-2"></i>Filtros
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3 mb-md-0">
                    <label for="filtroEstado" class="font-weight-bold text-dark">
                        <i class="fas fa-info-circle mr-2 text-info"></i>Filtrar por Estado
                    </label>
                    <select id="filtroEstado" class="form-control">
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
                    <label for="buscador" class="font-weight-bold text-dark">
                        <i class="fas fa-search mr-2 text-success"></i>Buscar
                    </label>
                    <input type="text" id="buscador" class="form-control" placeholder="Buscar por título...">
                </div>
                <div class="col-md-2">
                    <label class="d-block font-weight-bold text-dark">&nbsp;</label>
                    <button id="btnLimpiar" class="btn btn-block btn-secondary">
                        <i class="fas fa-times-circle mr-2"></i>Limpiar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Eventos -->
    <div class="card">
        <div class="card-header bg-primary">
            <h3 class="card-title mb-0 text-white">
                <i class="fas fa-list mr-2"></i>Lista de Eventos
            </h3>
        </div>
        <div class="card-body">
            <div id="eventosContainer" class="row">
                <!-- Los eventos se cargarán aquí -->
            </div>

            <!-- Mensaje cuando no hay eventos -->
            <div id="mensajeVacio" class="text-center py-5 d-none">
                <div class="bg-primary d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 100px; height: 100px;">
                    <i class="fas fa-calendar-times fa-3x text-white"></i>
                </div>
                <h4 class="font-weight-bold text-dark">No hay eventos que mostrar</h4>
                <p class="text-muted">Intenta cambiar los filtros o crear un nuevo evento.</p>
            </div>
        </div>
    </div>
</div>

<style>
    /* Estilos minimalistas */
    .card {
        transition: all 0.3s ease;
    }
    
    .card:hover {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .evento-card {
        transition: transform 0.2s;
    }
    
    .evento-card:hover {
        transform: translateY(-2px);
    }
    
    /* Tarjetas personalizadas con paleta de colores */
    .stat-card-custom {
        border-radius: 16px !important;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(12, 43, 68, 0.1);
        transition: all 0.3s ease;
    }
    
    .stat-card-custom:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 12px rgba(12, 43, 68, 0.2);
    }
    
    .stat-card-custom .icon {
        color: rgba(255, 255, 255, 0.3);
    }
    
    /* Gráficos */
    #graficoPastelEstados,
    #graficoBarrasEstados,
    #graficoEventosPorMes,
    #graficoTop5Participacion {
        max-height: 300px;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="{{ asset('assets/js/config.js') }}"></script>
<script>
let chartPastelEstados = null;
let chartBarrasEstados = null;
let chartEventosPorMes = null;
let chartTop5Participacion = null;

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
                actualizarMetricasAgregadas(data.metricas_agregadas || {});
                mostrarEventosDestacados(data.eventos_destacados || {});
                actualizarGraficosAdicionales(data.graficos_adicionales || {});
                mostrarTablaResumen(data.tabla_resumen || []);
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

    function actualizarMetricasAgregadas(metricas) {
        if (!metricas) return;
        
        animarContador('statTotalParticipantes', metricas.total_participantes || 0);
        animarContador('statTotalReacciones', metricas.total_reacciones || 0);
        animarContador('statTotalCompartidos', metricas.total_compartidos || 0);
        animarContador('statVoluntariosUnicos', metricas.total_voluntarios_unicos || 0);
        
        // KPIs
        const promPart = document.getElementById('statPromedioParticipantes');
        const tasaAsist = document.getElementById('statTasaAsistencia');
        const engRate = document.getElementById('statEngagementRate');
        const tasaFin = document.getElementById('statTasaFinalizacion');
        
        if (promPart) promPart.textContent = metricas.promedio_participantes || 0;
        if (tasaAsist) tasaAsist.textContent = (metricas.tasa_asistencia || 0) + '%';
        if (engRate) engRate.textContent = (metricas.engagement_rate || 0) + '%';
        if (tasaFin) tasaFin.textContent = (metricas.tasa_finalizacion || 0) + '%';
    }

    function mostrarEventosDestacados(destacados) {
        const container = document.getElementById('eventosDestacadosContainer');
        if (!container || !destacados) return;
        
        let html = '';
        
        if (destacados.mas_participantes) {
            html += `
                <div class="col-md-3 mb-3">
                    <div class="card border-left-info">
                        <div class="card-body">
                            <h6 class="mb-2 font-weight-bold text-dark">Más Participantes</h6>
                            <h6 class="mb-1 font-weight-bold text-info">${destacados.mas_participantes.titulo || 'N/A'}</h6>
                            <p class="mb-0 text-muted">${destacados.mas_participantes.participantes || 0} participantes</p>
                        </div>
                    </div>
                </div>
            `;
        }
        
        if (destacados.mas_engagement) {
            html += `
                <div class="col-md-3 mb-3">
                    <div class="card border-left-success">
                        <div class="card-body">
                            <h6 class="mb-2 font-weight-bold text-dark">Mayor Engagement</h6>
                            <h6 class="mb-1 font-weight-bold text-success">${destacados.mas_engagement.titulo || 'N/A'}</h6>
                            <p class="mb-0 text-muted">${destacados.mas_engagement.engagement || 0} puntos</p>
                        </div>
                    </div>
                </div>
            `;
        }
        
        if (destacados.proximo_importante) {
            html += `
                <div class="col-md-3 mb-3">
                    <div class="card border-left-warning">
                        <div class="card-body">
                            <h6 class="mb-2 font-weight-bold text-dark">Próximo Importante</h6>
                            <h6 class="mb-1 font-weight-bold text-warning">${destacados.proximo_importante.titulo || 'N/A'}</h6>
                            <p class="mb-0 text-muted">
                                ${destacados.proximo_importante.fecha_inicio ? new Date(destacados.proximo_importante.fecha_inicio).toLocaleDateString('es-ES') : 'N/A'}
                            </p>
                        </div>
                    </div>
                </div>
            `;
        }
        
        if (destacados.mas_reciente) {
            html += `
                <div class="col-md-3 mb-3">
                    <div class="card border-left-primary">
                        <div class="card-body">
                            <h6 class="mb-2 font-weight-bold text-dark">Más Reciente</h6>
                            <h6 class="mb-1 font-weight-bold text-primary">${destacados.mas_reciente.titulo || 'N/A'}</h6>
                            <p class="mb-0 text-muted">
                                ${destacados.mas_reciente.fecha_creacion ? new Date(destacados.mas_reciente.fecha_creacion).toLocaleDateString('es-ES') : 'N/A'}
                            </p>
                        </div>
                    </div>
                </div>
            `;
        }
        
        container.innerHTML = html || '<div class="col-12 text-center text-muted">No hay eventos destacados</div>';
    }

    function actualizarGraficosAdicionales(graficos) {
        if (!graficos) return;
        
        // Gráfico de eventos por mes
        const ctxMes = document.getElementById('graficoEventosPorMes');
        if (ctxMes && graficos.eventos_por_mes && Object.keys(graficos.eventos_por_mes).length > 0) {
            if (chartEventosPorMes) {
                chartEventosPorMes.destroy();
            }
            
            const labels = Object.keys(graficos.eventos_por_mes).map(mes => {
                const [year, month] = mes.split('-');
                return new Date(year, month - 1).toLocaleDateString('es-ES', { month: 'short', year: 'numeric' });
            });
            const data = Object.values(graficos.eventos_por_mes);
            
            chartEventosPorMes = new Chart(ctxMes, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Eventos Creados',
                        data: data,
                        borderColor: '#00A36C',
                        backgroundColor: 'rgba(0, 163, 108, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        }
        
        // Gráfico Top 5 por participación
        const ctxTop5 = document.getElementById('graficoTop5Participacion');
        if (ctxTop5 && graficos.top5_participacion && graficos.top5_participacion.length > 0) {
            if (chartTop5Participacion) {
                chartTop5Participacion.destroy();
            }
            
            const labels = graficos.top5_participacion.map(e => e.titulo.length > 20 ? e.titulo.substring(0, 20) + '...' : e.titulo);
            const data = graficos.top5_participacion.map(e => e.participantes);
            
            chartTop5Participacion = new Chart(ctxTop5, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Participantes',
                        data: data,
                        backgroundColor: '#3B82F6',
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    indexAxis: 'y',
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        }
    }

    function mostrarTablaResumen(tabla) {
        const tbody = document.getElementById('tablaResumenBody');
        if (!tbody) return;
        
        if (tabla.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">No hay eventos para mostrar</td></tr>';
            return;
        }
        
        tbody.innerHTML = tabla.map(e => {
            const estadoBadge = obtenerBadgeEstado(e.estado);
            const colorTasa = e.tasa_asistencia >= 80 ? 'text-success' : e.tasa_asistencia >= 50 ? 'text-warning' : 'text-danger';
            return `
                <tr>
                    <td class="font-weight-bold text-dark">${e.titulo || 'Sin título'}</td>
                    <td>${estadoBadge}</td>
                    <td class="text-center font-weight-bold">${e.participantes || 0}</td>
                    <td class="text-center font-weight-bold">${e.reacciones || 0}</td>
                    <td class="text-center font-weight-bold">${e.compartidos || 0}</td>
                    <td class="text-center font-weight-bold ${colorTasa}">${e.tasa_asistencia || 0}%</td>
                    <td>
                        <a href="/ong/eventos/${e.id}/detalle" class="btn btn-sm btn-primary">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
            `;
        }).join('');
    }
    
    // Función para animar el contador
    function animarContador(elementId, valorFinal) {
        const elemento = document.getElementById(elementId);
        if (!elemento) return;
        
        const valorInicial = parseInt(elemento.textContent) || 0;
        const duracion = 1000;
        const incremento = valorFinal / (duracion / 16);
        let valorActual = valorInicial;
        
        elemento.classList.add('counting');
        
        const intervalo = setInterval(() => {
            valorActual += incremento;
            
            if ((incremento > 0 && valorActual >= valorFinal) || 
                (incremento < 0 && valorActual <= valorFinal)) {
                elemento.textContent = valorFinal;
                clearInterval(intervalo);
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
        
        // Función helper para formatear fechas desde PostgreSQL sin conversión de zona horaria
        const formatearFechaPostgreSQL = (fechaStr) => {
            if (!fechaStr) return 'No especificada';
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

        // Usar estado_dinamico si está disponible
        const estadoParaBadge = e.estado_dinamico || e.estado;
        const estadoBadge = obtenerBadgeEstado(estadoParaBadge);
        const fechaInicio = formatearFechaPostgreSQL(e.fecha_inicio);
        const fechaFin = formatearFechaPostgreSQL(e.fecha_fin);

        return `
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card evento-card">
                    ${imagen ? `
                        <div class="card-img-top" style="height: 200px; overflow: hidden; background: #f8f9fa;">
                            <img src="${imagen}" class="w-100 h-100" style="object-fit: cover;" onerror="this.style.display='none'">
                        </div>
                    ` : `
                        <div class="card-img-top bg-primary d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="fas fa-calendar fa-4x text-white" style="opacity: 0.3;"></i>
                        </div>
                    `}
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title mb-0 font-weight-bold text-dark">${e.titulo || 'Sin título'}</h5>
                            ${estadoBadge}
                        </div>
                        <p class="card-text text-muted mb-3">
                            ${(e.descripcion || 'Sin descripción').substring(0, 120)}${e.descripcion && e.descripcion.length > 120 ? '...' : ''}
                        </p>
                        <div class="mt-3 pt-3 border-top">
                            <small class="d-block mb-2">
                                <i class="fas fa-calendar mr-2 text-info"></i><strong class="text-dark">Inicio:</strong> <span class="text-muted">${fechaInicio}</span>
                            </small>
                            <small class="d-block">
                                <i class="fas fa-calendar-check mr-2 text-success"></i><strong class="text-dark">Fin:</strong> <span class="text-muted">${fechaFin}</span>
                            </small>
                        </div>
                        <div class="mt-3">
                            <a href="/ong/eventos/${e.id}/detalle" class="btn btn-sm btn-block btn-primary">
                                <i class="fas fa-eye mr-2"></i>Ver Detalles
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function obtenerBadgeEstado(estadoDinamico) {
        // Usar clases de AdminLTE
        switch(estadoDinamico) {
            case 'finalizado':
                return '<span class="badge badge-secondary">Finalizado</span>';
            case 'activo':
                return '<span class="badge badge-success">En Curso</span>';
            case 'proximo':
                return '<span class="badge badge-info">Próximo</span>';
            case 'cancelado':
                return '<span class="badge badge-danger">Cancelado</span>';
            case 'borrador':
                return '<span class="badge badge-warning">Borrador</span>';
            default:
                return '<span class="badge badge-secondary">' + estadoDinamico + '</span>';
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

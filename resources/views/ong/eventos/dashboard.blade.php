@extends('adminlte::page')

@section('title', 'Dashboard de Eventos')

@section('content_header')
    <h1><i class="fas fa-chart-line"></i> Dashboard de Eventos</h1>
@stop

@section('content')
<div class="container-fluid">
    <!-- Estadísticas de Eventos -->
    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); border: none;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white text-uppercase mb-2" style="font-size: 0.75rem; font-weight: 600; opacity: 0.9;">Total de Eventos</h6>
                            <h2 class="mb-0 text-white" id="statTotal" style="font-size: 2.5rem; font-weight: 700;">0</h2>
                        </div>
                        <div class="text-right">
                            <i class="fas fa-calendar-alt fa-3x text-white" style="opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card" style="background: linear-gradient(135deg, #28a745 0%, #218838 100%); border: none;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white text-uppercase mb-2" style="font-size: 0.75rem; font-weight: 600; opacity: 0.9;">Eventos Finalizados</h6>
                            <h2 class="mb-0 text-white" id="statFinalizados" style="font-size: 2.5rem; font-weight: 700;">0</h2>
                        </div>
                        <div class="text-right">
                            <i class="fas fa-check-circle fa-3x text-white" style="opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card" style="background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%); border: none;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white text-uppercase mb-2" style="font-size: 0.75rem; font-weight: 600; opacity: 0.9;">Eventos en Curso</h6>
                            <h2 class="mb-0 text-white" id="statEnCurso" style="font-size: 2.5rem; font-weight: 700;">0</h2>
                        </div>
                        <div class="text-right">
                            <i class="fas fa-play-circle fa-3x text-white" style="opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); border: none;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white text-uppercase mb-2" style="font-size: 0.75rem; font-weight: 600; opacity: 0.9;">Eventos Próximos</h6>
                            <h2 class="mb-0 text-white" id="statProximos" style="font-size: 2.5rem; font-weight: 700;">0</h2>
                        </div>
                        <div class="text-right">
                            <i class="fas fa-clock fa-3x text-white" style="opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos de Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header" style="background-color: #17a2b8; color: white;">
                    <h3 class="card-title" style="color: white; margin: 0;">
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
                <div class="card-header" style="background-color: #28a745; color: white;">
                    <h3 class="card-title" style="color: white; margin: 0;">
                        <i class="fas fa-chart-bar mr-2"></i>Eventos por Tipo
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="graficoBarrasEstados" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros y Búsqueda -->
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-filter mr-2"></i>Filtros
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3 mb-md-0">
                    <label for="filtroEstado" class="form-label">
                        <i class="fas fa-info-circle mr-2"></i>Filtrar por Estado
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
                    <label for="buscador" class="form-label">
                        <i class="fas fa-search mr-2"></i>Buscar
                    </label>
                    <input type="text" id="buscador" class="form-control" placeholder="Buscar por título o descripción...">
                </div>
                <div class="col-md-2">
                    <label class="form-label d-block">&nbsp;</label>
                    <button id="btnLimpiar" class="btn btn-secondary btn-block">
                        <i class="fas fa-times mr-2"></i>Limpiar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Eventos -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-list mr-2"></i>Lista de Eventos
            </h3>
        </div>
        <div class="card-body">
            <div id="eventosContainer" class="row">
                <!-- Los eventos se cargarán aquí -->
            </div>

            <!-- Mensaje cuando no hay eventos -->
            <div id="mensajeVacio" class="text-center py-5 d-none">
                <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">No hay eventos que mostrar</h4>
                <p class="text-muted">Intenta cambiar los filtros o crear un nuevo evento.</p>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
        transition: all 0.3s cubic-bezier(.25,.8,.25,1);
    }
    
    .card:hover {
        box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23);
    }
    
    .evento-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .evento-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    .badge-estado {
        font-size: 0.75rem;
        padding: 0.4em 0.8em;
        border-radius: 4px;
        font-weight: 500;
    }
    
    /* Estilos para los gráficos */
    #graficoPastelEstados,
    #graficoBarrasEstados {
        max-height: 300px;
    }
    
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
    
    .card-title {
        margin-bottom: 0;
        font-size: 1rem;
        font-weight: 600;
        color: #495057;
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
        document.getElementById('statTotal').textContent = stats.total || 0;
        document.getElementById('statFinalizados').textContent = stats.finalizados || 0;
        document.getElementById('statEnCurso').textContent = stats.en_curso || 0;
        document.getElementById('statProximos').textContent = stats.proximos || 0;
        
        // Actualizar gráficos
        actualizarGraficos(stats);
    }

    function actualizarGraficos(stats) {
        // Colores AdminLTE - Ajustados para coincidir con el diseño
        const coloresAdminLTE = {
            finalizados: '#6c757d',    // gray
            en_curso: '#17a2b8',        // info (cyan/teal)
            proximos: '#28a745',        // success (green)
            cancelados: '#dc3545',       // danger (red)
            borradores: '#ffc107'       // warning (yellow)
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
            coloresPastel.push(coloresAdminLTE.finalizados);
        }
        if (datosPastel.en_curso > 0) {
            labelsPastel.push('En Curso');
            dataPastel.push(datosPastel.en_curso);
            coloresPastel.push(coloresAdminLTE.en_curso);
        }
        if (datosPastel.proximos > 0) {
            labelsPastel.push('Próximos');
            dataPastel.push(datosPastel.proximos);
            coloresPastel.push(coloresAdminLTE.proximos);
        }
        if (datosPastel.cancelados > 0) {
            labelsPastel.push('Cancelados');
            dataPastel.push(datosPastel.cancelados);
            coloresPastel.push(coloresAdminLTE.cancelados);
        }
        if (datosPastel.borradores > 0) {
            labelsPastel.push('Borradores');
            dataPastel.push(datosPastel.borradores);
            coloresPastel.push(coloresAdminLTE.borradores);
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
            coloresAdminLTE.finalizados,
            coloresAdminLTE.en_curso,
            coloresAdminLTE.proximos,
            coloresAdminLTE.cancelados,
            coloresAdminLTE.borradores
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
                        <div style="height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-calendar fa-4x text-white" style="opacity: 0.5;"></i>
                        </div>
                    `}
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title mb-0" style="font-size: 1.1rem; font-weight: 600; color: #495057;">${e.titulo || 'Sin título'}</h5>
                            ${estadoBadge}
                        </div>
                        <p class="card-text text-muted" style="font-size: 0.9rem; line-height: 1.6;">
                            ${(e.descripcion || 'Sin descripción').substring(0, 120)}${e.descripcion && e.descripcion.length > 120 ? '...' : ''}
                        </p>
                        <div class="mt-3 pt-3 border-top">
                            <small class="text-muted d-block mb-2">
                                <i class="fas fa-calendar-alt mr-2"></i><strong>Inicio:</strong> ${fechaInicio}
                            </small>
                            <small class="text-muted d-block">
                                <i class="fas fa-calendar-check mr-2"></i><strong>Fin:</strong> ${fechaFin}
                            </small>
                        </div>
                        <div class="mt-3">
                            <a href="/ong/eventos/${e.id}/detalle" class="btn btn-primary btn-sm btn-block">
                                <i class="fas fa-eye mr-2"></i>Ver Detalles
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function obtenerBadgeEstado(estadoDinamico) {
        // Usar el estado dinámico que viene del backend
        switch(estadoDinamico) {
            case 'finalizado':
                return '<span class="badge badge-secondary badge-estado">Finalizado</span>';
            case 'activo':
                return '<span class="badge badge-info badge-estado">En Curso</span>';
            case 'proximo':
                return '<span class="badge badge-success badge-estado">Próximo</span>';
            case 'cancelado':
                return '<span class="badge badge-danger badge-estado">Cancelado</span>';
            case 'borrador':
                return '<span class="badge badge-warning badge-estado">Borrador</span>';
            default:
                return '<span class="badge badge-secondary badge-estado">' + estadoDinamico + '</span>';
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

@extends('adminlte::page')

@section('title', 'Reportes | UNI2')

@section('content_header')
    <h1><i class="fas fa-chart-bar text-primary"></i> Reportes y Estadísticas</h1>
@stop

@section('content')
<div class="container-fluid">
    <!-- Estadísticas principales -->
    <div class="row mb-4">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3 id="totalEventos">0</h3>
                    <p>Total de Eventos</p>
                </div>
                <div class="icon"><i class="fas fa-calendar-alt"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3 id="eventosPublicados">0</h3>
                    <p>Eventos Publicados</p>
                </div>
                <div class="icon"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3 id="totalVoluntarios">0</h3>
                    <p>Total Voluntarios</p>
                </div>
                <div class="icon"><i class="fas fa-users"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3 id="totalParticipaciones">0</h3>
                    <p>Total Participaciones</p>
                </div>
                <div class="icon"><i class="fas fa-user-check"></i></div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row">
        <!-- Gráfico de eventos por estado -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-chart-pie mr-2"></i> Eventos por Estado</h4>
                </div>
                <div class="card-body">
                    <canvas id="graficoEstados" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfico de eventos por tipo -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="fas fa-chart-bar mr-2"></i> Eventos por Tipo</h4>
                </div>
                <div class="card-body">
                    <canvas id="graficoTipos" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfico de participación mensual -->
        <div class="col-lg-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0"><i class="fas fa-chart-line mr-2"></i> Participaciones por Mes</h4>
                </div>
                <div class="card-body">
                    <canvas id="graficoMensual" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de eventos recientes -->
    <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white">
            <h4 class="mb-0"><i class="fas fa-list mr-2"></i> Eventos Recientes</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Evento</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Participantes</th>
                            <th>Asistencia</th>
                        </tr>
                    </thead>
                    <tbody id="tablaEventos">
                        <tr>
                            <td colspan="6" class="text-center">
                                <i class="fas fa-spinner fa-spin"></i> Cargando...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('assets/js/config.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', async () => {
    const token = localStorage.getItem('token');
    const ongId = parseInt(localStorage.getItem('id_entidad') || localStorage.getItem('id_usuario'), 10);

    if (!token || isNaN(ongId) || ongId <= 0) {
        alert('Debe iniciar sesión correctamente');
        window.location.href = '/login';
        return;
    }

    try {
        // Cargar eventos
        const resEventos = await fetch(`${API_BASE_URL}/api/eventos/ong/${ongId}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        const dataEventos = await resEventos.json();
        
        if (!dataEventos.success) {
            throw new Error('Error al cargar eventos');
        }

        const eventos = dataEventos.eventos || [];

        // Cargar voluntarios
        const resVoluntarios = await fetch(`${API_BASE_URL}/api/voluntarios/ong/${ongId}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        const dataVoluntarios = await resVoluntarios.json();
        const voluntarios = dataVoluntarios.success ? (dataVoluntarios.voluntarios || []) : [];

        // Calcular estadísticas
        const totalEventos = eventos.length;
        const eventosPublicados = eventos.filter(e => e.estado === 'publicado').length;
        const totalVoluntarios = new Set(voluntarios.map(v => v.user_id)).size;
        const totalParticipaciones = voluntarios.length;

        // Actualizar estadísticas
        document.getElementById('totalEventos').textContent = totalEventos;
        document.getElementById('eventosPublicados').textContent = eventosPublicados;
        document.getElementById('totalVoluntarios').textContent = totalVoluntarios;
        document.getElementById('totalParticipaciones').textContent = totalParticipaciones;

        // Gráfico de estados
        const estados = eventos.reduce((acc, e) => {
            acc[e.estado] = (acc[e.estado] || 0) + 1;
            return acc;
        }, {});

        new Chart(document.getElementById('graficoEstados'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(estados),
                datasets: [{
                    data: Object.values(estados),
                    backgroundColor: [
                        '#6c757d', // borrador
                        '#28a745', // publicado
                        '#dc3545'  // cancelado
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Gráfico de tipos
        const tipos = eventos.reduce((acc, e) => {
            acc[e.tipo_evento] = (acc[e.tipo_evento] || 0) + 1;
            return acc;
        }, {});

        new Chart(document.getElementById('graficoTipos'), {
            type: 'bar',
            data: {
                labels: Object.keys(tipos),
                datasets: [{
                    label: 'Cantidad',
                    data: Object.values(tipos),
                    backgroundColor: '#17a2b8'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Gráfico mensual
        const participacionesPorMes = voluntarios.reduce((acc, v) => {
            if (v.fecha_inscripcion) {
                const mes = new Date(v.fecha_inscripcion).toLocaleDateString('es-ES', { month: 'short', year: 'numeric' });
                acc[mes] = (acc[mes] || 0) + 1;
            }
            return acc;
        }, {});

        const meses = Object.keys(participacionesPorMes).sort();
        const valores = meses.map(m => participacionesPorMes[m]);

        new Chart(document.getElementById('graficoMensual'), {
            type: 'line',
            data: {
                labels: meses,
                datasets: [{
                    label: 'Participaciones',
                    data: valores,
                    borderColor: '#17a2b8',
                    backgroundColor: 'rgba(23, 162, 184, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Tabla de eventos
        const tabla = document.getElementById('tablaEventos');
        if (eventos.length === 0) {
            tabla.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No hay eventos registrados</td></tr>';
        } else {
            tabla.innerHTML = eventos.slice(0, 10).map(e => {
                const participantes = voluntarios.filter(v => v.evento_id === e.id).length;
                const asistieron = voluntarios.filter(v => v.evento_id === e.id && v.asistio).length;
                const fecha = e.fecha_inicio ? new Date(e.fecha_inicio).toLocaleDateString('es-ES') : 'N/A';
                
                return `
                    <tr>
                        <td><strong>${e.titulo || 'Sin título'}</strong></td>
                        <td><span class="badge bg-secondary">${e.tipo_evento || 'N/A'}</span></td>
                        <td>
                            <span class="badge ${e.estado === 'publicado' ? 'bg-success' : e.estado === 'cancelado' ? 'bg-danger' : 'bg-secondary'}">
                                ${e.estado || 'N/A'}
                            </span>
                        </td>
                        <td>${fecha}</td>
                        <td><span class="badge bg-info">${participantes}</span></td>
                        <td><span class="badge bg-success">${asistieron}</span></td>
                    </tr>
                `;
            }).join('');
        }

    } catch (error) {
        console.error('Error cargando reportes:', error);
        document.getElementById('tablaEventos').innerHTML = `
            <tr>
                <td colspan="6" class="text-center text-danger">
                    <i class="fas fa-exclamation-circle"></i> Error al cargar datos: ${error.message}
                </td>
            </tr>
        `;
    }
});
</script>
@stop


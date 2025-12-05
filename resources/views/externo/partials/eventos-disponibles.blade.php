<div class="row mb-4">
    <!-- Gráficas de Estadísticas -->
    <div class="col-12 mb-4">
        <div class="row">
            <!-- Gráfica 1: Eventos Inscritos por Mes -->
            <div class="col-lg-6 col-md-12 mb-3">
                <div class="card shadow-sm" style="border-radius: 12px;">
                    <div class="card-header" style="background: #f8f9fa; border-bottom: 2px solid #00A36C;">
                        <h5 class="mb-0" style="color: #0C2B44; font-weight: 600;">
                            <i class="far fa-calendar-check mr-2 text-primary"></i>
                            Eventos Inscritos por Mes
                        </h5>
                    </div>
                    <div class="card-body" style="padding: 1.5rem;">
                        <div style="height: 300px; position: relative;">
                            <canvas id="graficaEventosPorMes"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráfica 2: Comparación Mega Eventos vs Eventos -->
            <div class="col-lg-6 col-md-12 mb-3">
                <div class="card shadow-sm" style="border-radius: 12px;">
                    <div class="card-header" style="background: #f8f9fa; border-bottom: 2px solid #00A36C;">
                        <h5 class="mb-0" style="color: #0C2B44; font-weight: 600;">
                            <i class="far fa-chart-bar mr-2 text-success"></i>
                            Eventos
                        </h5>
                    </div>
                    <div class="card-body" style="padding: 1.5rem;">
                        <div class="row">
                            <!-- Gráfica de Barras -->
                            <div class="col-6">
                                <div style="height: 250px; position: relative;">
                                    <canvas id="graficaBarrasEventos"></canvas>
                                </div>
                            </div>
                            <!-- Gráfica Donut -->
                            <div class="col-6">
                                <div style="height: 250px; position: relative;">
                                    <canvas id="graficaDonutTipos"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Eventos Disponibles -->
    <div class="col-12">
        <div class="card shadow-sm" style="border-radius: 12px; border: 1px solid #F5F5F5;">
            <div class="card-header" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); color: white; border-radius: 12px 12px 0 0;">
                <h3 class="card-title mb-0" style="color: white; margin: 0; font-weight: 600;">
                    <i class="far fa-calendar mr-2"></i> Eventos Disponibles
                </h3>
            </div>
            <div class="card-body p-4" id="contenedorEventos">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                    <p class="mt-2 text-muted">Cargando eventos disponibles...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let chartEventosPorMes = null;
let chartBarrasEventos = null;
let chartDonutTipos = null;

async function cargarEventosDisponibles() {
    const token = localStorage.getItem("token");
    const cont = document.getElementById("contenedorEventos");

    if (!token) {
        cont.innerHTML = `<div class="alert alert-warning">
            <p>Debes iniciar sesión para ver los eventos.</p>
            <a href="/login" class="btn btn-primary">Iniciar sesión</a>
        </div>`;
        return;
    }

    try {
        const res = await fetch(`${window.API_BASE_URL || API_BASE_URL}/api/dashboard-externo/eventos-disponibles`, {
            method: 'GET',
            headers: { 
                "Authorization": `Bearer ${token}`,
                "Accept": "application/json",
                "Content-Type": "application/json"
            }
        });

        if (!res.ok) {
            throw new Error('Error al cargar eventos');
        }

        const data = await res.json();
        
        if (!data.success) {
            throw new Error(data.error || 'Error al obtener eventos');
        }

        const eventos = data.eventos || [];
        const estadisticas = data.estadisticas || {};

        // Crear gráficas
        crearGraficasEventos(estadisticas, eventos);

        // Mostrar eventos
        if (eventos.length === 0) {
            cont.innerHTML = `
                <div class="alert alert-info text-center">
                    <i class="far fa-info-circle fa-2x mb-3"></i>
                    <p class="mb-0">No hay eventos disponibles en este momento.</p>
                </div>
            `;
            return;
        }

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

        let html = '<div class="row">';
        eventos.forEach(e => {
            const fechaInicio = formatearFechaPostgreSQL(e.fecha_inicio);
            
            const imagenUrl = e.imagen 
                ? (e.imagen.startsWith('http') ? e.imagen : `${window.API_BASE_URL || API_BASE_URL}/storage/${e.imagen}`)
                : null;

            html += `
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 shadow-sm" style="border-radius: 12px; border: 1px solid #F5F5F5; transition: all 0.3s;">
                        ${imagenUrl ? `
                        <img src="${imagenUrl}" class="card-img-top" alt="${e.titulo}" 
                             style="height: 200px; object-fit: cover; border-radius: 12px 12px 0 0;"
                             onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        ` : ''}
                        <div class="card-img-top d-flex align-items-center justify-content-center" 
                             style="height: 200px; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); border-radius: 12px 12px 0 0; ${imagenUrl ? 'display: none;' : ''}">
                            <i class="far fa-calendar fa-4x text-white" style="opacity: 0.5;"></i>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title mb-2" style="color: #0C2B44; font-weight: 700; font-size: 1.1rem;">
                                ${e.titulo || 'Sin título'}
                            </h5>
                            <p class="card-text text-muted mb-3" style="font-size: 0.9rem; line-height: 1.5;">
                                ${(e.descripcion || 'Sin descripción').substring(0, 100)}${(e.descripcion || '').length > 100 ? '...' : ''}
                            </p>
                            <div class="mb-3">
                                <p class="mb-2" style="color: #666; font-size: 0.85rem;">
                                    <i class="far fa-calendar mr-2" style="color: #00A36C;"></i>
                                    <strong>Fecha:</strong> ${fechaInicio}
                                </p>
                                <p class="mb-2" style="color: #666; font-size: 0.85rem;">
                                    <i class="far fa-map-marker-alt mr-2" style="color: #00A36C;"></i>
                                    <strong>Ubicación:</strong> ${e.ciudad || 'No especificada'}
                                </p>
                                <p class="mb-0" style="color: #666; font-size: 0.85rem;">
                                    <i class="far fa-tag mr-2" style="color: #00A36C;"></i>
                                    <strong>Tipo:</strong> ${e.tipo_evento || 'No especificado'}
                                </p>
                            </div>
                            <a href="/externo/eventos/${e.id}/detalle" 
                               class="btn btn-block" 
                               style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); color: white; border: none; border-radius: 8px; padding: 0.6rem 1.5rem; font-weight: 500; transition: all 0.3s;">
                                <i class="far fa-eye mr-2"></i>Ver Detalles
                            </a>
                        </div>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        
        cont.innerHTML = html;

        // Agregar efecto hover a las tarjetas
        const cards = cont.querySelectorAll('.card');
        cards.forEach(card => {
            card.onmouseenter = function() {
                this.style.transform = 'translateY(-4px)';
                this.style.boxShadow = '0 8px 16px rgba(12, 43, 68, 0.15)';
                this.style.borderColor = '#00A36C';
            };
            card.onmouseleave = function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '0 2px 8px rgba(0, 0, 0, 0.08)';
                this.style.borderColor = '#F5F5F5';
            };
        });

    } catch (error) {
        console.error("Error al cargar eventos disponibles:", error);
        cont.innerHTML = `
            <div class="alert alert-danger">
                <i class="far fa-exclamation-triangle mr-2"></i>
                Error al cargar eventos: ${error.message}
            </div>
        `;
    }
}

function crearGraficasEventos(estadisticas, eventos) {
    // 1. Gráfica de Eventos Inscritos por Mes (Línea)
    const ctxPorMes = document.getElementById('graficaEventosPorMes');
    if (ctxPorMes) {
        if (chartEventosPorMes) chartEventosPorMes.destroy();
        
        const mesesNombres = ['ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'];
        const fechaActual = new Date();
        
        // Últimos 5 meses con datos
        let meses = [];
        let datos = [];
        for (let i = 4; i >= 0; i--) {
            const fecha = new Date(fechaActual.getFullYear(), fechaActual.getMonth() - i, 1);
            const mesKey = mesesNombres[fecha.getMonth()];
            meses.push(mesKey);
            
            // Contar eventos por mes
            const eventosMes = eventos.filter(e => {
                if (!e.fecha_inicio) return false;
                const fechaEvento = new Date(e.fecha_inicio);
                return fechaEvento.getMonth() === fecha.getMonth() && 
                       fechaEvento.getFullYear() === fecha.getFullYear();
            }).length;
            datos.push(eventosMes);
        }
        
        chartEventosPorMes = new Chart(ctxPorMes, {
            type: 'line',
            data: {
                labels: meses,
                datasets: [{
                    label: 'Eventos',
                    data: datos,
                    borderColor: '#00A36C',
                    backgroundColor: 'rgba(0, 163, 108, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
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
                        backgroundColor: 'rgba(12, 43, 68, 0.9)',
                        padding: 12,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            color: '#666',
                            font: { size: 11 }
                        },
                        grid: { color: 'rgba(0, 0, 0, 0.05)' }
                    },
                    x: {
                        ticks: {
                            color: '#666',
                            font: { size: 11, weight: 'bold' }
                        },
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // 2. Gráfica de Barras (Mega Eventos vs Eventos)
    const ctxBarras = document.getElementById('graficaBarrasEventos');
    if (ctxBarras) {
        if (chartBarrasEventos) chartBarrasEventos.destroy();
        
        const totalEventos = estadisticas.total_eventos || 0;
        const totalMegaEventos = estadisticas.total_mega_eventos || 0;
        
        chartBarrasEventos = new Chart(ctxBarras, {
            type: 'bar',
            data: {
                labels: ['Mega', 'Eventos'],
                datasets: [{
                    label: 'Cantidad',
                    data: [totalMegaEventos, totalEventos],
                    backgroundColor: ['#0C2B44', '#00A36C'],
                    borderRadius: 8,
                    barThickness: 40
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(12, 43, 68, 0.9)',
                        padding: 12
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            color: '#666',
                            font: { size: 11 }
                        },
                        grid: { color: 'rgba(0, 0, 0, 0.05)' }
                    },
                    x: {
                        ticks: {
                            color: '#666',
                            font: { size: 11, weight: 'bold' }
                        },
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // 3. Gráfica Donut (Tipos de Eventos)
    const ctxDonut = document.getElementById('graficaDonutTipos');
    if (ctxDonut) {
        if (chartDonutTipos) chartDonutTipos.destroy();
        
        const tiposEventos = estadisticas.tipos_eventos || {};
        const labels = Object.keys(tiposEventos);
        const datos = Object.values(tiposEventos);
        
        // Colores para los segmentos
        const colores = ['#00A36C', '#0C2B44', '#008a5a', '#0a2338', '#7FFF7F'];
        
        chartDonutTipos = new Chart(ctxDonut, {
            type: 'doughnut',
            data: {
                labels: labels.length > 0 ? labels : ['Sin datos'],
                datasets: [{
                    data: datos.length > 0 ? datos : [1],
                    backgroundColor: colores.slice(0, Math.max(labels.length, 1)),
                    borderWidth: 0
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
                            padding: 15,
                            font: { size: 11 },
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(12, 43, 68, 0.9)',
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const porcentaje = ((context.parsed / total) * 100).toFixed(1);
                                return `${context.label}: ${context.parsed} (${porcentaje}%)`;
                            }
                        }
                    }
                }
            }
        });
    }
}

// Cargar eventos cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('contenedorEventos')) {
        cargarEventosDisponibles();
    }
});
</script>

@extends('layouts.adminlte-empresa')

@section('page_title', 'Panel de Empresa')

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
                                <i class="far fa-building mr-2"></i>
                                ¡Bienvenido,
                                <span id="nombreUsuario">
                                    {{ Auth::user()->nombre ?? Auth::user()->name ?? 'Empresa' }}
                                </span>!
                            </h2>

                            <p class="text-white mb-0" style="opacity: 0.95; font-size: 1.15rem; line-height: 1.6;">
                                Administra tus eventos, publicaciones y alianzas empresariales desde este panel centralizado.
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

    <!-- 🔹 Estadísticas -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card shadow-sm" style="background: linear-gradient(135deg, #0C2B44 0%, #0a2338 100%); border: none; border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white text-uppercase mb-3" style="font-size: 0.8rem; opacity: .95; font-weight: 600; letter-spacing: 0.5px;">Eventos Activos</h6>
                            <h2 class="text-white mb-0" id="eventosActivos" style="font-size: 3rem; font-weight: 700; line-height: 1;">0</h2>
                        </div>
                        <i class="far fa-calendar-check fa-3x text-white" style="opacity: .2;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card shadow-sm" style="background: linear-gradient(135deg, #00A36C 0%, #008a5a 100%); border: none; border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white text-uppercase mb-3" style="font-size: 0.8rem; opacity: .95; font-weight: 600; letter-spacing: 0.5px;">Alianzas ONG</h6>
                            <h2 class="text-white mb-0" id="alianzas" style="font-size: 3rem; font-weight: 700; line-height: 1;">0</h2>
                        </div>
                        <i class="far fa-handshake fa-3x text-white" style="opacity: .2;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card shadow-sm" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); border: none; border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white text-uppercase mb-3" style="font-size: 0.8rem; opacity: .95; font-weight: 600; letter-spacing: 0.5px;">Voluntarios</h6>
                            <h2 class="text-white mb-0" id="voluntarios" style="font-size: 3rem; font-weight: 700; line-height: 1;">0</h2>
                        </div>
                        <i class="far fa-users fa-3x text-white" style="opacity: .2;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card shadow-sm" style="background: linear-gradient(135deg, #00A36C 0%, #0C2B44 100%); border: none; border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white text-uppercase mb-3" style="font-size: 0.8rem; opacity: .95; font-weight: 600; letter-spacing: 0.5px;">Proyectos Activos</h6>
                            <h2 class="text-white mb-0" id="proyectos" style="font-size: 3rem; font-weight: 700; line-height: 1;">0</h2>
                        </div>
                        <i class="far fa-briefcase fa-3x text-white" style="opacity: .2;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 🔹 Gráfico de impacto -->
    <div class="card shadow-sm" style="border: none; border-radius: 12px;">
        <div class="card-header" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); border-radius: 12px 12px 0 0; border: none;">
            <h3 class="card-title text-white mb-0" style="font-weight: 600;">
                <i class="far fa-chart-bar mr-2"></i> Impacto por Categoría
            </h3>
        </div>
        <div class="card-body" style="padding: 1.5rem;">
            <canvas id="graficoImpacto" height="120"></canvas>
        </div>
    </div>

</div>
@stop

@push('css')
<style>
    .card {
        transition: all 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08) !important;
    }
</style>
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

    document.getElementById("relojTiempoReal").textContent = hora;
    document.getElementById("fechaActual").textContent = fecha;
}

document.addEventListener('DOMContentLoaded', () => {
    const token = localStorage.getItem('token');
    const nombre = localStorage.getItem('nombre_usuario') ?? 'Empresa';
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    
    // Actualizar nombre del usuario
    const nombreUsuarioEl = document.getElementById('nombreUsuario');
    if (nombreUsuarioEl) {
        nombreUsuarioEl.textContent = user.nombre || user.name || nombre || 'Empresa';
    }

    if (!token) {
        alert('⚠️ Tu sesión ha expirado.');
        window.location.href = '/login';
        return;
    }

    // Iniciar reloj
    actualizarReloj();
    setInterval(actualizarReloj, 1000);

    // 🔹 Gráfico de impacto
    const ctx = document.getElementById('graficoImpacto');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Educación', 'Salud', 'Ambiente', 'Cultura'],
                datasets: [{
                    label: 'Proyectos',
                    data: [5, 3, 2, 4],
                    backgroundColor: [
                        'rgba(12, 43, 68, 0.8)',
                        'rgba(0, 163, 108, 0.8)',
                        'rgba(12, 43, 68, 0.6)',
                        'rgba(0, 163, 108, 0.6)'
                    ],
                    borderColor: [
                        '#0C2B44',
                        '#00A36C',
                        '#0C2B44',
                        '#00A36C'
                    ],
                    borderWidth: 2,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(12, 43, 68, 0.9)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#00A36C',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#666',
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        ticks: {
                            color: '#666',
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush

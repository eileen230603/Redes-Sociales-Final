@extends('layouts.adminlte') 

@section('page_title', 'Panel de Bienvenida')

@section('content_body')
<div class="container-fluid">

    <!-- Panel de Bienvenida -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm bg-gradient-primary-accent" style="border: none;">
                <div class="card-body p-5">
                    <div class="row align-items-center">

                        <!-- Texto de bienvenida -->
                        <div class="col-md-8">
                            <h2 class="text-white mb-3" style="font-weight: 700; font-size: 2rem;">
                                <i class="far fa-hand-holding-heart mr-2"></i>
                                ¡Bienvenido, <span id="nombreOng">ONG</span>!
                            </h2>
                            <p class="text-white mb-0" style="opacity: 0.95; font-size: 1.15rem; line-height: 1.6;">
                                Gestiona tus eventos, voluntarios y actividades desde este panel centralizado.
                            </p>
                        </div>

                        <!-- Reloj Minimalista -->
                        <div class="col-md-4 text-right">
                            <div class="p-3" style="display: inline-block; background: rgba(255, 255, 255, 0.1); border-radius: 12px; backdrop-filter: blur(10px);">
                                <div class="text-white small mb-2" style="font-weight: 600; letter-spacing: 1px; text-transform: uppercase; opacity: 0.9;">
                                    <i class="far fa-clock mr-1"></i>Hora Actual
                                </div>

                                <!-- Hora real -->
                                <div id="relojTiempoReal" 
                                     style="font-weight: 700; font-size: 2.8rem; font-family: 'Courier New', monospace; color: #ffffff; line-height: 1;">
                                    00:00:00
                                </div>

                                <!-- Fecha real -->
                                <div id="fechaActual" 
                                     style="font-size: 0.95rem; color: rgba(255, 255, 255, 0.9); margin-top: 4px; font-weight: 500;">
                                    Lunes, 1 de Enero 2025
                                </div>
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
            <div class="card bg-gradient-primary" style="border: none;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white text-uppercase mb-3" style="font-size: 0.8rem; opacity: .95; font-weight: 600; letter-spacing: 0.5px;">Total Eventos</h6>
                            <h2 class="text-white mb-0" id="totalEventos" style="font-size: 3rem; font-weight: 700; line-height: 1;">0</h2>
                        </div>
                        <i class="far fa-calendar fa-3x text-white" style="opacity: .2;"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Mega Eventos -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-gradient-primary-accent" style="border: none;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white text-uppercase mb-3" style="font-size: 0.8rem; opacity: .95; font-weight: 600; letter-spacing: 0.5px;">Mega Eventos</h6>
                            <h2 class="text-white mb-0" id="totalMegaEventos" style="font-size: 3rem; font-weight: 700; line-height: 1;">0</h2>
                        </div>
                        <i class="far fa-star fa-3x text-white" style="opacity: .2;"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Voluntarios -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-gradient-success" style="border: none;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white text-uppercase mb-3" style="font-size: 0.8rem; opacity: .95; font-weight: 600; letter-spacing: 0.5px;">Voluntarios</h6>
                            <h2 class="text-white mb-0" id="totalVoluntarios" style="font-size: 3rem; font-weight: 700; line-height: 1;">0</h2>
                        </div>
                        <i class="far fa-users fa-3x text-white" style="opacity: .2;"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Reacciones -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); border: none;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white text-uppercase mb-3" style="font-size: 0.8rem; opacity: .95; font-weight: 600; letter-spacing: 0.5px;">Reacciones</h6>
                            <h2 class="text-white mb-0" id="totalReacciones" style="font-size: 3rem; font-weight: 700; line-height: 1;">0</h2>
                        </div>
                        <i class="far fa-heart fa-3x text-white" style="opacity: .2;"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection



@section('js')
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

    // Construcción segura y compatible
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

        document.getElementById('totalEventos').textContent = stats.eventos.total || 0;
        document.getElementById('totalMegaEventos').textContent = stats.mega_eventos.total || 0;
        document.getElementById('totalVoluntarios').textContent = stats.voluntarios.total_unicos || 0;
        document.getElementById('totalReacciones').textContent = stats.reacciones.total || 0;

    } catch (e) {
        console.error("Error cargando estadísticas:", e);
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
@endsection

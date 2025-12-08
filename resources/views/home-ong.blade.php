@extends('layouts.adminlte') 

@section('page_title', 'Panel de Bienvenida')

@section('content_body')
<div class="container-fluid">

    <!-- Panel de Bienvenida -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm" style="background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); border: none;">
                <div class="card-body p-4">
                    <div class="row align-items-center">

                        <!-- Texto de bienvenida -->
                        <div class="col-md-8">
                            <h2 class="text-white mb-2" style="font-weight: 600;">
                                <i class="fas fa-hand-holding-heart mr-2"></i>
                                ¡Bienvenido, <span id="nombreOng">ONG</span>!
                            </h2>
                            <p class="text-white mb-0" style="opacity: 0.9; font-size: 1.1rem;">
                                Gestiona tus eventos, voluntarios y actividades desde este panel centralizado.
                            </p>
                        </div>

                        <!-- Reloj Minimalista -->
                        <div class="col-md-4 text-right">
                            <div class="p-2" style="display: inline-block;">
                                <div class="text-white small mb-1" style="font-weight: 600; letter-spacing: 1px; text-transform: uppercase;">
                                    <i class="far fa-clock mr-1"></i>Hora Actual
                                </div>

                                <!-- Hora real -->
                                <div id="relojTiempoReal" 
                                     style="font-weight: 700; font-size: 2.8rem; font-family: 'Courier New', monospace; color: #ffffff;">
                                    00:00:00
                                </div>

                                <!-- Fecha real -->
                                <div id="fechaActual" 
                                     style="font-size: 0.9rem; color: #e4e4e4; margin-top: 2px;">
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
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card" style="background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); border: none;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white text-uppercase mb-2" style="font-size: 0.75rem; opacity: .9;">Total Eventos</h6>
                            <h2 class="text-white" id="totalEventos" style="font-size: 2.5rem;">0</h2>
                        </div>
                        <i class="fas fa-calendar-alt fa-3x text-white" style="opacity: .3;"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Mega Eventos -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card" style="background: linear-gradient(135deg, #6c757d 0%, #495057 100%); border: none;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white text-uppercase mb-2" style="font-size: 0.75rem; opacity: .9;">Mega Eventos</h6>
                            <h2 class="text-white" id="totalMegaEventos" style="font-size: 2.5rem;">0</h2>
                        </div>
                        <i class="fas fa-star fa-3x text-white" style="opacity: .3;"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Voluntarios -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card" style="background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%); border: none;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white text-uppercase mb-2" style="font-size: 0.75rem; opacity: .9;">Voluntarios</h6>
                            <h2 class="text-white" id="totalVoluntarios" style="font-size: 2.5rem;">0</h2>
                        </div>
                        <i class="fas fa-users fa-3x text-white" style="opacity: .3;"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Reacciones -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card" style="background: linear-gradient(135deg, #dc3545 0%, #bd2130 100%); border: none;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white text-uppercase mb-2" style="font-size: 0.75rem; opacity: .9;">Reacciones</h6>
                            <h2 class="text-white" id="totalReacciones" style="font-size: 2.5rem;">0</h2>
                        </div>
                        <i class="fas fa-heart fa-3x text-white" style="opacity: .3;"></i>
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

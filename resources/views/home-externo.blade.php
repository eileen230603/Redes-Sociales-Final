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
                                <span id="nombreUsuario">
                                    {{ Auth::user()->nombre ?? Auth::user()->name ?? 'Usuario' }}
                                </span>!
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
    @include('externo.partials.estadisticas')
    @include('externo.partials.eventos-disponibles')

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

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('assets/js/config.js') }}"></script>
<script src="{{ asset('assets/js/externo/home.js') }}"></script>

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

// =======================================================
//   🟢 Inicialización Automática
// =======================================================
document.addEventListener('DOMContentLoaded', () => {

    // Inicia el reloj
    actualizarReloj();
    setInterval(actualizarReloj, 1000);
});
</script>

@endpush

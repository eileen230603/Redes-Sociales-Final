@extends('layouts.adminlte-empresa')

@section('page_title', 'Ayuda a Eventos')

@section('content_body')

<!-- Espacio superior -->
<div style="height: 40px;"></div>

<!-- Header con diseño minimalista mejorado -->
<div class="card mb-4 border-0 shadow-sm" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); border-radius: 16px; overflow: hidden;">
    <div class="card-body py-4 px-4">
        <div class="row align-items-center">
            <div class="col-md-10">
                <div class="d-flex align-items-center">
                    <div class="bg-white rounded-circle p-3 mr-3 shadow-lg" style="width: 64px; height: 64px; display: flex; align-items: center; justify-content: center; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px);">
                        <i class="far fa-hand-holding-heart" style="font-size: 1.9rem; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;"></i>
                    </div>
                    <div>
                        <h3 class="text-white mb-1" style="font-weight: 700; font-size: 1.85rem; letter-spacing: -0.5px;">
                            Eventos Disponibles para Patrocinar
                        </h3>
                        <p class="text-white mb-0" style="opacity: 0.9; font-size: 1rem; font-weight: 400;">
                            Descubre oportunidades para patrocinar y colaborar con eventos
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-2 text-right d-none d-md-block">
                <i class="far fa-heart" style="font-size: 5rem; color: rgba(255,255,255,0.1);"></i>
            </div>
        </div>
    </div>
</div>

<div class="row" id="eventosContainer">
    <div class="col-12 text-center py-5">
        <div class="spinner-border" role="status" style="width: 3rem; height: 3rem; border: 4px solid rgba(0, 163, 108, 0.2); border-top-color: #00A36C; border-radius: 50%;">
            <span class="sr-only">Cargando...</span>
        </div>
        <p class="text-muted mt-3" style="color: #6b7280; font-size: 0.95rem;">Cargando eventos disponibles...</p>
    </div>
</div>

@stop

@push('css')
<style>
    /* Estilo minimalista para las tarjetas de eventos */
    .evento-card-minimalista {
        border: 1px solid rgba(0, 0, 0, 0.05);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .evento-card-minimalista:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12) !important;
    }

    /* Efecto de hover en botón */
    .evento-card-minimalista button:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 163, 108, 0.4) !important;
    }

    .evento-card-minimalista button:active {
        transform: translateY(0);
    }

    /* Estilos para los inputs */
    .form-control:focus {
        border-color: #00A36C;
        box-shadow: 0 0 0 0.2rem rgba(0, 163, 108, 0.15);
    }

    /* Animación suave para las tarjetas */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .col-md-4 {
        animation: fadeInUp 0.4s ease-out;
    }

    /* Mejoras en el header */
    .card-header-gradient {
        background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%);
        border: none;
    }

    /* Estilos para notificaciones toast */
    .toast-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 350px;
        max-width: 450px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        padding: 20px;
        display: flex;
        align-items: flex-start;
        gap: 15px;
        animation: slideInRight 0.3s ease-out;
        border-left: 4px solid;
    }

    .toast-notification.success {
        border-left-color: #00A36C;
    }

    .toast-notification.error {
        border-left-color: #ef4444;
    }

    .toast-notification.info {
        border-left-color: #0C2B44;
    }

    .toast-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.2rem;
    }

    .toast-notification.success .toast-icon {
        background: rgba(0, 163, 108, 0.1);
        color: #00A36C;
    }

    .toast-notification.error .toast-icon {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
    }

    .toast-notification.info .toast-icon {
        background: rgba(12, 43, 68, 0.1);
        color: #0C2B44;
    }

    .toast-content {
        flex: 1;
    }

    .toast-title {
        font-weight: 700;
        font-size: 1rem;
        color: #1a1a1a;
        margin-bottom: 4px;
    }

    .toast-message {
        font-size: 0.875rem;
        color: #6b7280;
        line-height: 1.5;
    }

    .toast-close {
        background: none;
        border: none;
        color: #9ca3af;
        font-size: 1.2rem;
        cursor: pointer;
        padding: 0;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        transition: all 0.2s;
        flex-shrink: 0;
    }

    .toast-close:hover {
        background: rgba(0, 0, 0, 0.05);
        color: #1a1a1a;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }

    .toast-notification.hiding {
        animation: slideOutRight 0.3s ease-out forwards;
    }

    /* Modal de confirmación personalizado */
    .custom-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: fadeIn 0.2s ease-out;
    }

    .custom-modal {
        background: white;
        border-radius: 16px;
        padding: 0;
        max-width: 450px;
        width: 90%;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        animation: scaleIn 0.2s ease-out;
        overflow: hidden;
    }

    .custom-modal-header {
        background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%);
        padding: 24px;
        color: white;
    }

    .custom-modal-header h4 {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .custom-modal-header .modal-icon {
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    .custom-modal-body {
        padding: 24px;
        color: #1a1a1a;
    }

    .custom-modal-body p {
        margin: 0;
        font-size: 1rem;
        line-height: 1.6;
        color: #4b5563;
    }

    .custom-modal-footer {
        padding: 16px 24px;
        background: #f9fafb;
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }

    .modal-btn {
        padding: 10px 24px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.95rem;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .modal-btn-primary {
        background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%);
        color: white;
    }

    .modal-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 163, 108, 0.4);
    }

    .modal-btn-secondary {
        background: white;
        color: #6b7280;
        border: 1px solid #e5e7eb;
    }

    .modal-btn-secondary:hover {
        background: #f3f4f6;
        color: #1a1a1a;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    @keyframes scaleIn {
        from {
            transform: scale(0.9);
            opacity: 0;
        }
        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    .custom-modal-overlay.hiding {
        animation: fadeOut 0.2s ease-out forwards;
    }

    .custom-modal-overlay.hiding .custom-modal {
        animation: scaleOut 0.2s ease-out forwards;
    }

    @keyframes fadeOut {
        from {
            opacity: 1;
        }
        to {
            opacity: 0;
        }
    }

    @keyframes scaleOut {
        from {
            transform: scale(1);
            opacity: 1;
        }
        to {
            transform: scale(0.9);
            opacity: 0;
        }
    }
</style>
@endpush

@push('js')
<script src="{{ asset('assets/js/config.js') }}"></script>
<script>
// Función para mostrar modal de confirmación personalizado
function mostrarConfirmacionPersonalizada(titulo, mensaje) {
    return new Promise((resolve) => {
        const overlay = document.createElement('div');
        overlay.className = 'custom-modal-overlay';
        overlay.onclick = (e) => {
            if (e.target === overlay) {
                cerrarModal();
            }
        };

        overlay.innerHTML = `
            <div class="custom-modal">
                <div class="custom-modal-header">
                    <h4>
                        <div class="modal-icon">
                            <i class="far fa-question-circle"></i>
                        </div>
                        ${titulo}
                    </h4>
                </div>
                <div class="custom-modal-body">
                    <p>${mensaje}</p>
                </div>
                <div class="custom-modal-footer">
                    <button class="modal-btn modal-btn-secondary" onclick="cerrarModal(false)">
                        <i class="far fa-times mr-1"></i> Cancelar
                    </button>
                    <button class="modal-btn modal-btn-primary" onclick="cerrarModal(true)">
                        <i class="far fa-check mr-1"></i> Aceptar
                    </button>
                </div>
            </div>
        `;

        document.body.appendChild(overlay);

        // Guardar referencia para poder cerrarlo
        window.modalConfirmacion = {
            overlay: overlay,
            resolve: resolve
        };
    });
}

function cerrarModal(resultado = false) {
    if (window.modalConfirmacion) {
        window.modalConfirmacion.overlay.classList.add('hiding');
        setTimeout(() => {
            window.modalConfirmacion.overlay.remove();
            window.modalConfirmacion.resolve(resultado);
            window.modalConfirmacion = null;
        }, 200);
    }
}

// Función para mostrar notificaciones toast
function mostrarNotificacion(tipo, titulo, mensaje, duracion = 5000) {
    // Eliminar notificaciones existentes si hay muchas
    const existingToasts = document.querySelectorAll('.toast-notification');
    if (existingToasts.length >= 3) {
        existingToasts[0].remove();
    }

    const toast = document.createElement('div');
    toast.className = `toast-notification ${tipo}`;
    
    let icono = '';
    if (tipo === 'success') {
        icono = '<i class="far fa-check-circle"></i>';
    } else if (tipo === 'error') {
        icono = '<i class="far fa-exclamation-circle"></i>';
    } else {
        icono = '<i class="far fa-info-circle"></i>';
    }

    toast.innerHTML = `
        <div class="toast-icon">${icono}</div>
        <div class="toast-content">
            <div class="toast-title">${titulo}</div>
            <div class="toast-message">${mensaje}</div>
        </div>
        <button class="toast-close" onclick="cerrarNotificacion(this)">&times;</button>
    `;

    document.body.appendChild(toast);

    // Auto-cerrar después de la duración especificada
    setTimeout(() => {
        cerrarNotificacion(toast.querySelector('.toast-close'));
    }, duracion);
}

function cerrarNotificacion(btn) {
    const toast = btn.closest('.toast-notification');
    if (toast) {
        toast.classList.add('hiding');
        setTimeout(() => {
            toast.remove();
        }, 300);
    }
}

document.addEventListener("DOMContentLoaded", async () => {
    const token = localStorage.getItem("token");
    const empresaId = parseInt(localStorage.getItem("id_entidad") || localStorage.getItem("id_usuario"), 10);
    const cont = document.getElementById("eventosContainer");

    console.log("🔍 Cargando eventos disponibles para patrocinar...");
    console.log("Token:", token ? "Presente" : "Ausente");
    console.log("Empresa ID:", empresaId);

    if (!token) {
        cont.innerHTML = `<div class="alert text-center border-0" style="background: white; border-left: 4px solid #f59e0b; border-radius: 12px; padding: 3rem 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <div class="mb-4" style="width: 80px; height: 80px; margin: 0 auto; background: rgba(245, 158, 11, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="far fa-exclamation-triangle" style="font-size: 2.5rem; color: #f59e0b;"></i>
            </div>
            <h5 style="color: #1a1a1a; font-weight: 700; font-size: 1.25rem; margin-bottom: 0.5rem;">Debes iniciar sesión</h5>
            <p class="mb-4" style="color: #6b7280; font-size: 0.95rem;">Para ver los eventos disponibles necesitas iniciar sesión.</p>
            <a href="/login" class="btn border-0 shadow-sm" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); color: white; border-radius: 10px; padding: 0.7rem 2rem; font-weight: 600; transition: all 0.3s;">
                <i class="far fa-sign-in-alt mr-2"></i> Iniciar sesión
            </a>
        </div>`;
        return;
    }

    try {
        // Obtener todos los eventos publicados
        const res = await fetch(`${API_BASE_URL}/api/eventos`, {
            method: 'GET',
            headers: { 
                "Authorization": `Bearer ${token}`,
                "Accept": "application/json",
                "Content-Type": "application/json"
            }
        });

        if (!res.ok) {
            const errorData = await res.json().catch(() => ({}));
            cont.innerHTML = `<div class="alert text-center border-0" style="background: white; border-left: 4px solid #ef4444; border-radius: 12px; padding: 3rem 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                <div class="mb-4" style="width: 80px; height: 80px; margin: 0 auto; background: rgba(239, 68, 68, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <i class="far fa-exclamation-circle" style="font-size: 2.5rem; color: #ef4444;"></i>
                </div>
                <h5 style="color: #1a1a1a; font-weight: 700; font-size: 1.25rem; margin-bottom: 0.5rem;">Error al cargar eventos (${res.status})</h5>
                <p class="mb-0" style="color: #6b7280; font-size: 0.95rem;">${errorData.error || 'Error del servidor'}</p>
            </div>`;
            return;
        }

        const data = await res.json();
        console.log("Datos recibidos:", data);

        if (!data.success) {
            cont.innerHTML = `<div class="alert text-center border-0" style="background: white; border-left: 4px solid #ef4444; border-radius: 12px; padding: 3rem 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                <div class="mb-4" style="width: 80px; height: 80px; margin: 0 auto; background: rgba(239, 68, 68, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <i class="far fa-exclamation-circle" style="font-size: 2.5rem; color: #ef4444;"></i>
                </div>
                <h5 style="color: #1a1a1a; font-weight: 700; font-size: 1.25rem; margin-bottom: 0.5rem;">Error</h5>
                <p class="mb-0" style="color: #6b7280; font-size: 0.95rem;">${data.error || 'Error desconocido'}</p>
            </div>`;
            return;
        }

        // Filtrar eventos donde esta empresa NO es patrocinadora o colaboradora
        const eventosDisponibles = data.eventos.filter(evento => {
            // Convertir empresaId a string y número para comparar
            const empresaIdStr = String(empresaId);
            const empresaIdNum = Number(empresaId);
            
            // Verificar si es patrocinador desde el campo JSON patrocinadores
            // Los patrocinadores pueden venir como array de números, strings, u objetos enriquecidos {id, nombre, ...}
            let esPatrocinadorJSON = false;
            if (Array.isArray(evento.patrocinadores)) {
                esPatrocinadorJSON = evento.patrocinadores.some(p => {
                    // Si es un objeto enriquecido con id
                    if (typeof p === 'object' && p !== null && p.id !== undefined) {
                        return p.id === empresaIdNum || String(p.id) === empresaIdStr;
                    }
                    // Si es un número o string
                    const pStr = String(p).trim();
                    const pNum = Number(p);
                    return pStr === empresaIdStr || pNum === empresaIdNum || 
                           String(pNum).trim() === empresaIdStr || 
                           String(pStr) === String(empresaIdNum);
                });
            } else if (typeof evento.patrocinadores === 'string') {
                try {
                    const parsed = JSON.parse(evento.patrocinadores);
                    if (Array.isArray(parsed)) {
                        esPatrocinadorJSON = parsed.some(p => {
                            if (typeof p === 'object' && p !== null && p.id !== undefined) {
                                return p.id === empresaIdNum || String(p.id) === empresaIdStr;
                            }
                            const pStr = String(p).trim();
                            const pNum = Number(p);
                            return pStr === empresaIdStr || pNum === empresaIdNum;
                        });
                    }
                } catch (err) {
                    console.warn('Error parseando patrocinadores:', err);
                }
            }
            
            // Verificar si es colaboradora desde empresas_colaboradoras
            let esColaboradora = false;
            if (Array.isArray(evento.empresas_colaboradoras) && evento.empresas_colaboradoras.length > 0) {
                esColaboradora = evento.empresas_colaboradoras.some(emp => {
                    const empId = emp.id || emp.empresa_id || emp.user_id;
                    return empId === empresaIdNum || String(empId) === empresaIdStr;
            });
            }
            
            // Excluir si es patrocinador O colaborador
            const debeExcluir = esPatrocinadorJSON || esColaboradora;
            
            if (debeExcluir) {
                console.log(`Evento "${evento.titulo}" excluido - esPatrocinadorJSON: ${esPatrocinadorJSON}, esColaboradora: ${esColaboradora}`);
            }
            
            return !debeExcluir;
        });

        console.log("Total eventos recibidos:", data.eventos.length);
        console.log("Eventos disponibles para patrocinar:", eventosDisponibles.length);
        console.log("Empresa ID:", empresaId);

        if (eventosDisponibles.length === 0) {
            cont.innerHTML = `<div class="alert text-center border-0" style="background: white; border-radius: 16px; padding: 4rem 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                <div class="mb-4" style="width: 80px; height: 80px; margin: 0 auto; background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <i class="far fa-info-circle" style="font-size: 2.5rem; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;"></i>
                </div>
                <h5 style="color: #1a1a1a; font-weight: 700; font-size: 1.25rem; margin-bottom: 0.5rem;">No hay eventos disponibles para patrocinar</h5>
                <p class="mb-0" style="color: #6b7280; font-size: 0.95rem;">Todos los eventos publicados ya tienen tu patrocinio.</p>
            </div>`;
            return;
        }

        cont.innerHTML = "";

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
                const mesesAbr = ['ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 
                                 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'];
                
                const horaFormateada = String(horas).padStart(2, '0');
                const minutoFormateado = String(minutos).padStart(2, '0');
                
                return {
                    completa: `${dia} de ${meses[mes]} de ${año}, ${horaFormateada}:${minutoFormateado}`,
                    dia: dia,
                    mes: mesesAbr[mes],
                    año: año,
                    hora: `${horaFormateada}:${minutoFormateado}`
                };
            } catch (error) {
                console.error('Error formateando fecha:', error);
                return { completa: fechaStr, dia: '', mes: '', año: '', hora: '' };
            }
        };

        eventosDisponibles.forEach(e => {
            const fechaInicioObj = formatearFechaPostgreSQL(e.fecha_inicio);
            const fechaFinObj = e.fecha_fin ? formatearFechaPostgreSQL(e.fecha_fin) : null;

            // Formatear fecha límite de inscripción
            let fechaLimiteHTML = '';
            if (e.fecha_limite_inscripcion) {
                const fechaLimiteObj = formatearFechaPostgreSQL(e.fecha_limite_inscripcion);
                const fechaLimite = new Date(e.fecha_limite_inscripcion);
                const ahora = new Date();
                const diasRestantes = Math.ceil((fechaLimite - ahora) / (1000 * 60 * 60 * 24));
                
                fechaLimiteHTML = `
                    <div class="mb-3 p-3" style="background: rgba(12, 43, 68, 0.05); border-left: 3px solid #00A36C; border-radius: 8px;">
                        <div class="d-flex align-items-center mb-1">
                            <i class="far fa-hourglass-half mr-2" style="font-size: 0.9rem; color: #00A36C;"></i>
                            <span style="font-weight: 600; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; color: #6c757d;">Cierre de Inscripción</span>
                        </div>
                        <div style="font-size: 0.9rem; font-weight: 600; color: #0C2B44; margin-bottom: 0.25rem;">${fechaLimiteObj.completa}</div>
                        ${diasRestantes >= 0 
                            ? `<div style="font-size: 0.75rem; color: #00A36C;">
                                <i class="far fa-clock mr-1"></i>
                                ${diasRestantes === 0 ? 'Último día' : diasRestantes === 1 ? '1 día restante' : `${diasRestantes} días restantes`}
                               </div>`
                            : `<div style="font-size: 0.75rem; color: #ff9800;">
                                <i class="far fa-exclamation-triangle mr-1"></i>
                                Inscripción cerrada
                               </div>`
                        }
                    </div>
                `;
            }

            // Procesar imágenes
            let imagenes = [];
            if (Array.isArray(e.imagenes) && e.imagenes.length > 0) {
                imagenes = e.imagenes.filter(img => img && typeof img === 'string' && img.trim().length > 0);
            }

            const getImageUrl = (imgPath) => {
                if (!imgPath || typeof imgPath !== 'string') return null;
                imgPath = imgPath.trim();
                if (!imgPath) return null;
                if (imgPath.startsWith('http://') || imgPath.startsWith('https://')) return imgPath;
                if (imgPath.startsWith('/')) return `${API_BASE_URL}${imgPath}`;
                if (imgPath.startsWith('storage/')) return `${API_BASE_URL}/${imgPath}`;
                return `${API_BASE_URL}/${imgPath}`;
            };

            const imagenPrincipal = imagenes.length > 0 ? getImageUrl(imagenes[0]) : null;
            
            // Crear componente de fecha de finalización (similar a la imagen)
            let fechaFinalizacionHTML = '';
            if (fechaFinObj && fechaFinObj.dia && fechaFinObj.mes) {
                fechaFinalizacionHTML = `
                    <div class="position-absolute" style="top: 15px; left: 15px; z-index: 10;">
                        <div style="background: white; border-radius: 12px; padding: 12px 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); min-width: 70px; text-align: center;">
                            <div style="font-size: 2rem; font-weight: 700; line-height: 1; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                                ${fechaFinObj.dia}
                            </div>
                            <div style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin-top: 4px;">
                                ${fechaFinObj.mes}
                            </div>
                        </div>
                    </div>
                `;
            }
            
            const imagenHTML = imagenPrincipal 
                ? `<div class="position-relative" style="height: 240px; overflow: hidden; background: #f8f9fa; border-radius: 12px 12px 0 0;">
                    <img src="${imagenPrincipal}" alt="${e.titulo}" class="w-100 h-100" style="object-fit: cover; filter: brightness(0.95);" 
                         onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\\'http://www.w3.org/2000/svg\\' width=\\'400\\' height=\\'240\\'%3E%3Crect fill=\\'%23f8f9fa\\' width=\\'400\\' height=\\'240\\'/%3E%3Ctext x=\\'50%25\\' y=\\'50%25\\' text-anchor=\\'middle\\' dy=\\'.3em\\' fill=\\'%23adb5bd\\' font-family=\\'Arial\\' font-size=\\'14\\'%3EImagen no disponible%3C/text%3E%3C/svg%3E'; this.style.objectFit='contain'; this.style.padding='20px';">
                    ${fechaFinalizacionHTML}
                   </div>`
                : `<div class="position-relative" style="height: 240px; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); display: flex; align-items: center; justify-content: center; border-radius: 12px 12px 0 0;">
                    <i class="far fa-calendar-alt fa-4x text-white" style="opacity: 0.3;"></i>
                    ${fechaFinalizacionHTML}
                   </div>`;

            cont.innerHTML += `
            <div class="col-md-4 mb-4">
                <div class="card border-0 h-100 evento-card-minimalista" style="border-radius: 16px; overflow: hidden; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); background: white;">
                    ${imagenHTML}
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="mb-2 d-flex align-items-start justify-content-between">
                            ${e.tipo_evento ? `<span class="badge" style="font-size: 0.7rem; background: rgba(0, 163, 108, 0.1); color: #00A36C; padding: 0.35em 0.8em; border-radius: 20px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">${e.tipo_evento}</span>` : ''}
                        </div>
                        
                        <h5 class="mb-2" style="font-size: 1.15rem; font-weight: 700; color: #1a1a1a; line-height: 1.4; margin-top: 8px;">${e.titulo || 'Sin título'}</h5>
                        
                        <p class="text-muted mb-3 flex-grow-1" style="font-size: 0.875rem; line-height: 1.6; color: #6b7280; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            ${e.descripcion || 'Sin descripción'}
                        </p>
                        
                        <div class="mb-3" style="border-top: 1px solid #f3f4f6; padding-top: 12px;">
                            <div class="d-flex align-items-center mb-2" style="color: #4b5563; font-size: 0.85rem;">
                                <i class="far fa-calendar-check mr-2" style="color: #00A36C; font-size: 0.9rem;"></i>
                                <span style="font-weight: 500;">${fechaInicioObj.completa}</span>
                            </div>
                            ${e.ciudad ? `<div class="d-flex align-items-center mb-2" style="color: #4b5563; font-size: 0.85rem;">
                                <i class="far fa-map-marker-alt mr-2" style="color: #00A36C; font-size: 0.9rem;"></i>
                                <span style="font-weight: 500;">${e.ciudad}</span>
                            </div>` : ''}
                            ${e.ong && e.ong.nombre_ong ? `<div class="d-flex align-items-center" style="color: #4b5563; font-size: 0.85rem;">
                                <i class="far fa-users mr-2" style="color: #00A36C; font-size: 0.9rem;"></i>
                                <span style="font-weight: 500;">${e.ong.nombre_ong}</span>
                            </div>` : ''}
                        </div>
                        
                        ${fechaLimiteHTML}
                        
                        <button onclick="patrocinarEvento(${e.id}, ${empresaId}, this)" class="btn btn-block mt-auto" id="btn-patrocinar-${e.id}" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); color: white; border: none; border-radius: 10px; padding: 0.65em 1.2em; font-weight: 600; font-size: 0.9rem; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(0, 163, 108, 0.3);">
                            <i class="far fa-hand-holding-heart mr-2"></i> Patrocinar Evento
                        </button>
                    </div>
                </div>
            </div>`;
        });

    } catch (error) {
        console.error("Error al cargar eventos:", error);
        cont.innerHTML = `<div class="alert text-center border-0" style="background: white; border-left: 4px solid #ef4444; border-radius: 12px; padding: 3rem 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <div class="mb-4" style="width: 80px; height: 80px; margin: 0 auto; background: rgba(239, 68, 68, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="far fa-exclamation-circle" style="font-size: 2.5rem; color: #ef4444;"></i>
            </div>
            <h5 style="color: #1a1a1a; font-weight: 700; font-size: 1.25rem; margin-bottom: 0.5rem;">Error de conexión</h5>
            <p class="mb-2" style="color: #6b7280; font-size: 0.95rem;">Error al cargar eventos.</p>
            <small class="text-muted" style="color: #9ca3af;">${error.message}</small>
        </div>`;
    }
});

async function patrocinarEvento(eventoId, empresaId, boton) {
    // Mostrar modal de confirmación personalizado
    const confirmar = await mostrarConfirmacionPersonalizada(
        'Confirmar Patrocinio',
        '¿Estás seguro de que deseas patrocinar este evento? Una vez confirmado, la ONG organizadora será notificada.'
    );
    
    if (!confirmar) {
        return;
    }

    const token = localStorage.getItem('token');
    const btnOriginal = boton.innerHTML;
    
    // Deshabilitar botón y mostrar loading
    boton.disabled = true;
    boton.innerHTML = '<i class="far fa-spinner fa-spin mr-2"></i> Procesando...';

    try {
        const res = await fetch(`${API_BASE_URL}/api/eventos/${eventoId}/patrocinar`, {
            method: 'POST',
            headers: {
                "Authorization": `Bearer ${token}`,
                "Accept": "application/json",
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                empresa_id: empresaId
            })
        });

        const data = await res.json();
        console.log("Respuesta de patrocinar:", data);

        if (!res.ok || !data.success) {
            mostrarNotificacion(
                'error',
                'Error al patrocinar',
                data.error || data.message || 'No se pudo procesar tu solicitud de patrocinio. Por favor, intenta nuevamente.'
            );
            boton.disabled = false;
            boton.innerHTML = btnOriginal;
            return;
        }

        // Mostrar éxito y actualizar botón
        boton.innerHTML = '<i class="far fa-check-circle mr-2"></i> Patrocinando';
        boton.style.background = 'linear-gradient(135deg, #00A36C 0%, #059669 100%)';
        boton.style.color = 'white';
        boton.disabled = true;

        // Agregar badge de confirmación
        const cardBody = boton.closest('.card-body');
        if (cardBody) {
            const badge = document.createElement('span');
            badge.className = 'badge mb-2';
            badge.style.cssText = 'background: rgba(0, 163, 108, 0.1); color: #00A36C; font-size: 0.7rem; padding: 0.35em 0.8em; border-radius: 20px; font-weight: 600; border: 1px solid rgba(0, 163, 108, 0.2);';
            badge.innerHTML = '<i class="far fa-check mr-1"></i> Patrocinador';
            cardBody.insertBefore(badge, boton);
        }

        // Mostrar notificación de éxito elegante
        mostrarNotificacion(
            'success',
            '¡Patrocinio exitoso!',
            'Has patrocinado este evento correctamente. La ONG organizadora ha sido notificada. El evento aparecerá en "Eventos Patrocinados".',
            6000
        );

        // Remover la tarjeta de la lista después de un momento
        setTimeout(() => {
            const card = boton.closest('.col-md-4');
            if (card) {
                card.style.transition = 'opacity 0.5s, transform 0.5s';
                card.style.opacity = '0';
                card.style.transform = 'scale(0.9)';
                setTimeout(() => {
                    card.remove();
                    // Si no quedan eventos, mostrar mensaje
                    const cont = document.getElementById('eventosContainer');
                    if (cont && cont.querySelectorAll('.col-md-4').length === 0) {
                        cont.innerHTML = `<div class="alert text-center border-0" style="background: white; border-radius: 16px; padding: 4rem 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                            <div class="mb-4" style="width: 80px; height: 80px; margin: 0 auto; background: linear-gradient(135deg, rgba(0, 163, 108, 0.1) 0%, rgba(5, 150, 105, 0.1) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="far fa-check-circle" style="font-size: 2.5rem; color: #00A36C;"></i>
                            </div>
                            <h5 style="color: #1a1a1a; font-weight: 700; font-size: 1.25rem; margin-bottom: 0.5rem;">¡Excelente!</h5>
                            <p class="mb-2" style="color: #6b7280; font-size: 0.95rem;">Has patrocinado todos los eventos disponibles.</p>
                            <small class="text-muted" style="color: #9ca3af;">Puedes ver tus eventos patrocinados en la sección "Eventos Patrocinados".</small>
                        </div>`;
                    }
                }, 500);
            }
        }, 3000);

    } catch (error) {
        console.error("Error al patrocinar evento:", error);
        mostrarNotificacion(
            'error',
            'Error de conexión',
            'No se pudo conectar con el servidor. Por favor, verifica tu conexión a internet e intenta nuevamente.'
        );
        boton.disabled = false;
        boton.innerHTML = btnOriginal;
    }
}
</script>
@endpush


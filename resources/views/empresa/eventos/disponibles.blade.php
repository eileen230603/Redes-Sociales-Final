@extends('layouts.adminlte-empresa')

@section('page_title', 'Ayuda a Eventos')

@section('content_body')

<!-- Header con diseño mejorado - Paleta de colores -->
<div class="card mb-4 shadow-sm" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); border: none; border-radius: 15px; overflow: hidden;">
    <div class="card-body py-4 px-4">
        <div class="row align-items-center">
            <div class="col-md-10">
                <div class="d-flex align-items-center">
                    <div class="bg-white rounded-circle p-3 mr-3 shadow-sm" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <i class="far fa-hand-holding-heart" style="font-size: 1.8rem; color: #00A36C;"></i>
                    </div>
                    <div>
                        <h3 class="text-white mb-1" style="font-weight: 700; font-size: 1.75rem;">
                            Eventos Disponibles para Patrocinar
                        </h3>
                        <p class="text-white mb-0" style="opacity: 0.95; font-size: 1rem;">
                            Descubre oportunidades para patrocinar y colaborar con eventos
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-2 text-right d-none d-md-block">
                <i class="far fa-heart" style="font-size: 4.5rem; color: rgba(255,255,255,0.15);"></i>
            </div>
        </div>
    </div>
</div>

<div class="row" id="eventosContainer">
    <div class="col-12 text-center py-5">
        <div class="spinner-border" role="status" style="width: 3rem; height: 3rem; color: #00A36C;">
            <span class="sr-only">Cargando...</span>
        </div>
        <p class="text-muted mt-3">Cargando eventos disponibles...</p>
    </div>
</div>

@stop

@push('css')
<style>
    /* Mejoras para las tarjetas de eventos */
    .card {
        transition: all 0.3s ease;
        border-radius: 12px;
        border: 1px solid #F5F5F5;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(12, 43, 68, 0.15) !important;
    }

    /* Estilos para los inputs */
    .form-control:focus {
        border-color: #00A36C;
        box-shadow: 0 0 0 0.2rem rgba(0, 163, 108, 0.15);
    }
</style>
@endpush

@push('js')
<script src="{{ asset('assets/js/config.js') }}"></script>
<script>
document.addEventListener("DOMContentLoaded", async () => {
    const token = localStorage.getItem("token");
    const empresaId = parseInt(localStorage.getItem("id_entidad") || localStorage.getItem("id_usuario"), 10);
    const cont = document.getElementById("eventosContainer");

    console.log("🔍 Cargando eventos disponibles para patrocinar...");
    console.log("Token:", token ? "Presente" : "Ausente");
    console.log("Empresa ID:", empresaId);

    if (!token) {
        cont.innerHTML = `<div class="alert text-center" style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 12px; padding: 2rem;">
            <i class="far fa-exclamation-triangle fa-3x mb-3" style="color: #ffc107;"></i>
            <h5 style="color: #856404; font-weight: 600;">Debes iniciar sesión</h5>
            <p class="mb-3" style="color: #856404;">Para ver los eventos disponibles necesitas iniciar sesión.</p>
            <a href="/login" class="btn" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); color: white; border: none; border-radius: 8px; padding: 0.6rem 1.5rem; font-weight: 600;">
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
            cont.innerHTML = `<div class="alert text-center" style="background: #f8d7da; border: 1px solid #dc3545; border-radius: 12px; padding: 2rem;">
                <i class="far fa-exclamation-circle fa-3x mb-3" style="color: #dc3545;"></i>
                <h5 style="color: #721c24; font-weight: 600;">Error al cargar eventos (${res.status})</h5>
                <p class="mb-0" style="color: #721c24;">${errorData.error || 'Error del servidor'}</p>
            </div>`;
            return;
        }

        const data = await res.json();
        console.log("Datos recibidos:", data);

        if (!data.success) {
            cont.innerHTML = `<div class="alert text-center" style="background: #f8d7da; border: 1px solid #dc3545; border-radius: 12px; padding: 2rem;">
                <i class="far fa-exclamation-circle fa-3x mb-3" style="color: #dc3545;"></i>
                <h5 style="color: #721c24; font-weight: 600;">Error</h5>
                <p class="mb-0" style="color: #721c24;">${data.error || 'Error desconocido'}</p>
            </div>`;
            return;
        }

        // Filtrar eventos donde esta empresa NO es patrocinadora
        const eventosDisponibles = data.eventos.filter(evento => {
            let patrocinadores = [];
            
            if (Array.isArray(evento.patrocinadores)) {
                patrocinadores = evento.patrocinadores;
            } else if (typeof evento.patrocinadores === 'string') {
                try {
                    const parsed = JSON.parse(evento.patrocinadores);
                    patrocinadores = Array.isArray(parsed) ? parsed : [];
                } catch (err) {
                    console.warn('Error parseando patrocinadores:', err);
                }
            }
            
            // Convertir empresaId a string para comparar
            const empresaIdStr = String(empresaId);
            
            // Verificar si la empresa NO está en el array de patrocinadores
            const esPatrocinador = patrocinadores.some(p => {
                return String(p).trim() === empresaIdStr || Number(p) === empresaId;
            });
            
            return !esPatrocinador; // Retornar eventos donde NO es patrocinador
        });

        console.log("Total eventos recibidos:", data.eventos.length);
        console.log("Eventos disponibles para patrocinar:", eventosDisponibles.length);
        console.log("Empresa ID:", empresaId);

        if (eventosDisponibles.length === 0) {
            cont.innerHTML = `<div class="alert text-center" style="background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 12px; padding: 3rem;">
                <i class="far fa-info-circle fa-3x mb-3" style="color: #00A36C;"></i>
                <h5 style="color: #0C2B44; font-weight: 600;">No hay eventos disponibles para patrocinar</h5>
                <p class="mb-0" style="color: #6c757d;">Todos los eventos publicados ya tienen tu patrocinio.</p>
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
                
                const horaFormateada = String(horas).padStart(2, '0');
                const minutoFormateado = String(minutos).padStart(2, '0');
                
                return `${dia} de ${meses[mes]} de ${año}, ${horaFormateada}:${minutoFormateado}`;
            } catch (error) {
                console.error('Error formateando fecha:', error);
                return fechaStr;
            }
        };

        eventosDisponibles.forEach(e => {
            const fechaInicio = formatearFechaPostgreSQL(e.fecha_inicio);

            // Formatear fecha límite de inscripción
            let fechaLimiteHTML = '';
            if (e.fecha_limite_inscripcion) {
                const fechaFormateada = formatearFechaPostgreSQL(e.fecha_limite_inscripcion);
                const ahora = new Date();
                const diasRestantes = Math.ceil((fechaLimite - ahora) / (1000 * 60 * 60 * 24));
                
                fechaLimiteHTML = `
                    <div class="mb-3 p-3" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); border-radius: 10px; color: white;">
                        <div class="d-flex align-items-center mb-2">
                            <i class="far fa-calendar-times mr-2" style="font-size: 1.1rem; opacity: 0.9;"></i>
                            <span style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Cierre de Inscripción</span>
                        </div>
                        <div style="font-size: 1rem; font-weight: 700; margin-bottom: 0.25rem;">${fechaFormateada}</div>
                        ${diasRestantes >= 0 
                            ? `<div style="font-size: 0.8rem; opacity: 0.9;">
                                <i class="far fa-clock mr-1"></i>
                                ${diasRestantes === 0 ? 'Último día' : diasRestantes === 1 ? '1 día restante' : `${diasRestantes} días restantes`}
                               </div>`
                            : `<div style="font-size: 0.8rem; opacity: 0.9; color: #ffc107;">
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
            const imagenHTML = imagenPrincipal 
                ? `<div class="position-relative" style="height: 200px; overflow: hidden; background: #f8f9fa;">
                    <img src="${imagenPrincipal}" alt="${e.titulo}" class="w-100 h-100" style="object-fit: cover;" 
                         onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\\'http://www.w3.org/2000/svg\\' width=\\'400\\' height=\\'200\\'%3E%3Crect fill=\\'%23f8f9fa\\' width=\\'400\\' height=\\'200\\'/%3E%3Ctext x=\\'50%25\\' y=\\'50%25\\' text-anchor=\\'middle\\' dy=\\'.3em\\' fill=\\'%23adb5bd\\' font-family=\\'Arial\\' font-size=\\'14\\'%3EImagen no disponible%3C/text%3E%3C/svg%3E'; this.style.objectFit='contain'; this.style.padding='20px';">
                    <div class="position-absolute" style="top: 12px; left: 12px;">
                        <span class="badge" style="background: rgba(12, 43, 68, 0.9); color: white; font-size: 0.75rem; padding: 0.4em 0.8em; border-radius: 20px; font-weight: 500;">Evento</span>
                    </div>
                   </div>`
                : `<div class="position-relative" style="height: 200px; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); display: flex; align-items: center; justify-content: center;">
                    <i class="far fa-calendar fa-4x text-white" style="opacity: 0.7;"></i>
                    <div class="position-absolute" style="top: 12px; left: 12px;">
                        <span class="badge" style="background: rgba(12, 43, 68, 0.9); color: white; font-size: 0.75rem; padding: 0.4em 0.8em; border-radius: 20px; font-weight: 500;">Evento</span>
                    </div>
                   </div>`;

            cont.innerHTML += `
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; overflow: hidden; transition: transform 0.2s, box-shadow 0.2s;">
                    ${imagenHTML}
                    <div class="card-body p-4 d-flex flex-column">
                        <h5 class="mb-2" style="font-size: 1.1rem; font-weight: 600; color: #0C2B44;">${e.titulo || 'Sin título'}</h5>
                        <p class="text-muted mb-3 flex-grow-1" style="font-size: 0.9rem; line-height: 1.5; color: #6c757d; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            ${e.descripcion || 'Sin descripción'}
                        </p>
                        ${e.ciudad ? `<p class="text-muted mb-2" style="font-size: 0.85rem;"><i class="far fa-map-marker-alt mr-1" style="color: #00A36C;"></i> ${e.ciudad}</p>` : ''}
                        <div class="mb-3 d-flex align-items-center" style="color: #6c757d; font-size: 0.85rem;">
                            <i class="far fa-calendar-alt mr-2" style="color: #00A36C;"></i>
                            <span>${fechaInicio}</span>
                        </div>
                        ${fechaLimiteHTML}
                        ${e.tipo_evento ? `<span class="badge mb-2" style="font-size: 0.75rem; background: #0C2B44; color: white; padding: 0.4em 0.8em; border-radius: 20px; font-weight: 500;">${e.tipo_evento}</span>` : ''}
                        ${e.ong && e.ong.nombre_ong ? `<p class="text-muted mb-2" style="font-size: 0.85rem;"><i class="far fa-building mr-1" style="color: #00A36C;"></i> Organizado por: <strong style="color: #0C2B44;">${e.ong.nombre_ong}</strong></p>` : ''}
                        <button onclick="patrocinarEvento(${e.id}, ${empresaId}, this)" class="btn btn-block mt-auto" id="btn-patrocinar-${e.id}" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); color: white; border: none; border-radius: 8px; padding: 0.5em 1.2em; font-weight: 500; transition: all 0.2s;">
                            <i class="far fa-hand-holding-heart mr-2"></i> Patrocinar
                        </button>
                    </div>
                </div>
            </div>`;
        });

    } catch (error) {
        console.error("Error al cargar eventos:", error);
        cont.innerHTML = `<div class="alert text-center" style="background: #f8d7da; border: 1px solid #dc3545; border-radius: 12px; padding: 2rem;">
            <i class="far fa-exclamation-circle fa-3x mb-3" style="color: #dc3545;"></i>
            <h5 style="color: #721c24; font-weight: 600;">Error de conexión</h5>
            <p class="mb-0" style="color: #721c24;">Error al cargar eventos.</p>
            <small class="text-muted">${error.message}</small>
        </div>`;
    }
});

async function patrocinarEvento(eventoId, empresaId, boton) {
    if (!confirm('¿Deseas patrocinar este evento?')) {
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
            alert(data.error || data.message || 'Error al patrocinar el evento');
            boton.disabled = false;
            boton.innerHTML = btnOriginal;
            return;
        }

        // Mostrar éxito y actualizar botón
        boton.innerHTML = '<i class="far fa-check-circle mr-2"></i> Patrocinando';
        boton.style.background = '#00A36C';
        boton.style.color = 'white';
        boton.disabled = true;

        // Agregar badge de confirmación
        const cardBody = boton.closest('.card-body');
        if (cardBody) {
            const badge = document.createElement('span');
            badge.className = 'badge mb-2';
            badge.style.cssText = 'background: #00A36C; color: white; font-size: 0.75rem; padding: 0.4em 0.8em; border-radius: 20px; font-weight: 500;';
            badge.innerHTML = '<i class="far fa-check mr-1"></i> Patrocinador';
            cardBody.insertBefore(badge, boton);
        }

        // Mostrar mensaje de éxito
        alert('¡Has patrocinado este evento exitosamente! Ahora aparecerá en "Eventos Patrocinados".');

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
                        cont.innerHTML = `<div class="alert text-center" style="background: #d4edda; border: 1px solid #00A36C; border-radius: 12px; padding: 3rem;">
                            <i class="far fa-check-circle fa-3x mb-3" style="color: #00A36C;"></i>
                            <h5 style="color: #155724; font-weight: 600;">¡Excelente!</h5>
                            <p class="mb-0" style="color: #155724;">Has patrocinado todos los eventos disponibles.</p>
                            <small class="text-muted">Puedes ver tus eventos patrocinados en la sección "Eventos Patrocinados".</small>
                        </div>`;
                    }
                }, 500);
            }
        }, 2000);

    } catch (error) {
        console.error("Error al patrocinar evento:", error);
        alert('Error de conexión al patrocinar el evento');
        boton.disabled = false;
        boton.innerHTML = btnOriginal;
    }
}
</script>
@endpush


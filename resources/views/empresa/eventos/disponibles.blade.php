@extends('layouts.adminlte-empresa')

@section('page_title', 'Ayuda a Eventos')

@section('content_body')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-primary"><i class="fas fa-hand-holding-heart"></i> Eventos Disponibles para Patrocinar</h4>
</div>

<div class="row" id="eventosContainer">
    <p class="text-muted px-3">Cargando eventos...</p>
</div>

@stop

@section('js')
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
        cont.innerHTML = `<div class="alert alert-warning">
            <p>Debes iniciar sesión para ver los eventos.</p>
            <a href="/login" class="btn btn-primary">Iniciar sesión</a>
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
            cont.innerHTML = `<div class="alert alert-danger">
                <p>Error al cargar eventos (${res.status})</p>
                <small>${errorData.error || 'Error del servidor'}</small>
            </div>`;
            return;
        }

        const data = await res.json();
        console.log("Datos recibidos:", data);

        if (!data.success) {
            cont.innerHTML = `<div class="alert alert-danger">
                <p>Error: ${data.error || 'Error desconocido'}</p>
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
            cont.innerHTML = `<div class="alert alert-info">
                <p class="mb-0">No hay eventos disponibles para patrocinar en este momento.</p>
                <small>Todos los eventos publicados ya tienen tu patrocinio.</small>
            </div>`;
            return;
        }

        cont.innerHTML = "";

        eventosDisponibles.forEach(e => {
            const fechaInicio = e.fecha_inicio ? new Date(e.fecha_inicio).toLocaleDateString('es-ES', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            }) : 'Fecha no especificada';

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
                ? `<img src="${imagenPrincipal}" class="card-img-top" alt="${e.titulo}" style="height: 200px; object-fit: cover;" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\\'card-img-top bg-light d-flex align-items-center justify-content-center\\' style=\\'height: 200px;\\'><i class=\\'fas fa-image fa-3x text-muted\\'></i></div>';">`
                : `<div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;"><i class="fas fa-image fa-3x text-muted"></i></div>`;

            cont.innerHTML += `
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm h-100">
                    ${imagenHTML}
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-primary">${e.titulo}</h5>
                        <p class="card-text flex-grow-1">${e.descripcion || 'Sin descripción'}</p>
                        ${e.ciudad ? `<p class="text-muted"><i class="fas fa-map-marker-alt"></i> ${e.ciudad}</p>` : ''}
                        <p class="text-muted small"><i class="far fa-calendar"></i> ${fechaInicio}</p>
                        ${e.tipo_evento ? `<span class="badge badge-info mb-2">${e.tipo_evento}</span>` : ''}
                        <button onclick="patrocinarEvento(${e.id}, ${empresaId}, this)" class="btn btn-success btn-block mt-auto" id="btn-patrocinar-${e.id}">
                            <i class="fas fa-hand-holding-heart"></i> Patrocinar
                        </button>
                    </div>
                </div>
            </div>`;
        });

    } catch (error) {
        console.error("Error al cargar eventos:", error);
        cont.innerHTML = `<div class="alert alert-danger">
            <p>Error de conexión al cargar eventos.</p>
            <small>${error.message}</small>
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
    boton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';

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
        boton.innerHTML = '<i class="fas fa-check-circle"></i> Patrocinando';
        boton.classList.remove('btn-success');
        boton.classList.add('btn-outline-success');
        boton.disabled = true;

        // Agregar badge de confirmación
        const cardBody = boton.closest('.card-body');
        if (cardBody) {
            const badge = document.createElement('span');
            badge.className = 'badge badge-success mb-2';
            badge.innerHTML = '<i class="fas fa-check"></i> Patrocinador';
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
                        cont.innerHTML = `<div class="alert alert-success">
                            <p class="mb-0">¡Excelente! Has patrocinado todos los eventos disponibles.</p>
                            <small>Puedes ver tus eventos patrocinados en la sección "Eventos Patrocinados".</small>
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
@stop


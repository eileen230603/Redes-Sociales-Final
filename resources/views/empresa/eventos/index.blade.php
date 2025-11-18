@extends('layouts.adminlte-empresa')

@section('page_title', 'Eventos Patrocinados')

@section('content_body')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-primary"><i class="fas fa-calendar-check"></i> Eventos Patrocinados</h4>
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

    console.log("🔍 Cargando eventos patrocinados para empresa...");
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
        // Obtener todos los eventos publicados y filtrar los que tienen esta empresa como patrocinadora
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

        // Filtrar eventos donde esta empresa es patrocinadora
        // Los patrocinadores pueden venir como strings o números, así que comparamos ambos
        const eventosPatrocinados = data.eventos.filter(evento => {
            let patrocinadores = [];
            
            // Procesar patrocinadores
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
            
            // Convertir empresaId a string y número para comparar
            const empresaIdStr = String(empresaId);
            const empresaIdNum = Number(empresaId);
            
            // Verificar si la empresa está en el array de patrocinadores
            // Comparar tanto como string como número para cubrir todos los casos
            const esPatrocinador = patrocinadores.some(p => {
                // Normalizar ambos valores a string y número para comparar
                const pNormalized = String(p).trim();
                const empresaIdNormalized = String(empresaId).trim();
                
                // Comparar como strings
                if (pNormalized === empresaIdNormalized) return true;
                
                // Comparar como números (por si acaso)
                const pNum = Number(p);
                const empresaIdNum = Number(empresaId);
                if (!isNaN(pNum) && !isNaN(empresaIdNum) && pNum === empresaIdNum) return true;
                
                return false;
            });
            
            console.log(`Evento "${evento.titulo}": patrocinadores=${JSON.stringify(patrocinadores)}, empresaId=${empresaId} (tipo: ${typeof empresaId}), esPatrocinador=${esPatrocinador}`);
            
            return esPatrocinador;
        });

        console.log("Total eventos recibidos:", data.eventos.length);
        console.log("Eventos patrocinados encontrados:", eventosPatrocinados.length);
        console.log("Empresa ID buscado:", empresaId);

        if (eventosPatrocinados.length === 0) {
            cont.innerHTML = `<div class="alert alert-info">
                <p class="mb-0">No tienes eventos patrocinados en este momento.</p>
            </div>`;
            return;
        }

        cont.innerHTML = "";

        eventosPatrocinados.forEach(e => {
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
                        <span class="badge badge-success mb-2">Patrocinador</span>
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
</script>
@stop


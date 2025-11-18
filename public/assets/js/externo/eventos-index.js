document.addEventListener("DOMContentLoaded", async () => {
    const token = localStorage.getItem("token");
    const cont = document.getElementById("listaEventos");

    console.log("🔍 Cargando eventos para externo...");
    console.log("Token:", token ? "Presente" : "Ausente");
    console.log("API URL:", `${API_BASE_URL}/api/eventos`);

    if (!token) {
        cont.innerHTML = `<div class="alert alert-warning">
            <p>Debes iniciar sesión para ver los eventos.</p>
            <a href="/login" class="btn btn-primary">Iniciar sesión</a>
        </div>`;
        return;
    }

    try {
        const res = await fetch(`${API_BASE_URL}/api/eventos`, {
            method: 'GET',
            headers: { 
                "Authorization": `Bearer ${token}`,
                "Accept": "application/json",
                "Content-Type": "application/json"
            }
        });

        console.log("Response status:", res.status);
        console.log("Response ok:", res.ok);

        if (!res.ok) {
            const errorData = await res.json().catch(() => ({}));
            console.error("Error response:", errorData);
            cont.innerHTML = `<div class="alert alert-danger">
                <p>Error al cargar eventos (${res.status})</p>
                <small>${errorData.error || 'Error del servidor'}</small>
            </div>`;
            return;
        }

        const data = await res.json();
        console.log("Datos recibidos:", data);
        console.log("Eventos encontrados:", data.eventos?.length || 0);

        if (!data.success) {
            cont.innerHTML = `<div class="alert alert-danger">
                <p>Error: ${data.error || 'Error desconocido'}</p>
            </div>`;
            return;
        }

        if (!data.eventos || data.eventos.length === 0) {
            cont.innerHTML = `<div class="alert alert-info">
                <p class="mb-0">No hay eventos disponibles en este momento.</p>
                <small>Total encontrados: ${data.count || 0}</small>
            </div>`;
            return;
        }

        cont.innerHTML = "";

        data.eventos.forEach(e => {
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
                // Filtrar solo strings válidos
                imagenes = e.imagenes.filter(img => img && typeof img === 'string' && img.trim().length > 0);
            } else if (typeof e.imagenes === 'string') {
                try {
                    const parsed = JSON.parse(e.imagenes);
                    if (Array.isArray(parsed)) {
                        imagenes = parsed.filter(img => img && typeof img === 'string' && img.trim().length > 0);
                    }
                } catch (err) {
                    console.warn('Error parseando imágenes:', err);
                }
            }
            
            console.log('Imágenes procesadas para evento', e.titulo, ':', imagenes);

            // Función para obtener la URL completa de la imagen
            const getImageUrl = (imgPath) => {
                // Validar que imgPath sea una cadena válida
                if (!imgPath || typeof imgPath !== 'string') {
                    return null;
                }
                
                // Limpiar espacios en blanco
                imgPath = imgPath.trim();
                
                if (!imgPath) return null;
                
                // Si ya es una URL completa, retornarla
                if (imgPath.startsWith('http://') || imgPath.startsWith('https://')) {
                    return imgPath;
                }
                // Si es una ruta relativa, construir la URL completa
                if (imgPath.startsWith('/')) {
                    return `${API_BASE_URL}${imgPath}`;
                }
                // Si es una ruta de storage, construir la URL
                if (imgPath.startsWith('storage/')) {
                    return `${API_BASE_URL}/${imgPath}`;
                }
                // Por defecto, asumir que es relativa a la raíz
                return `${API_BASE_URL}/${imgPath}`;
            };

            // Obtener la primera imagen o usar una imagen por defecto
            const imagenPrincipal = imagenes.length > 0 ? getImageUrl(imagenes[0]) : null;
            const imagenHTML = imagenPrincipal 
                ? `<img src="${imagenPrincipal}" class="card-img-top" alt="${e.titulo}" style="height: 200px; object-fit: cover; cursor: pointer;" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\\'card-img-top bg-light d-flex align-items-center justify-content-center\\' style=\\'height: 200px;\\'><i class=\\'fas fa-image fa-3x text-muted\\'></i></div>';" onclick="window.open('${imagenPrincipal}', '_blank');">`
                : `<div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                    <i class="fas fa-image fa-3x text-muted"></i>
                   </div>`;

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
                        <a href="/externo/eventos/${e.id}/detalle" class="btn btn-info btn-block mt-auto">
                            Ver Detalles
                        </a>
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

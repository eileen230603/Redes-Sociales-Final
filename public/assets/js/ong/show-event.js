// =====================================
// show-event.js CON DEBUGGING
// =====================================

const tokenShow = localStorage.getItem("token");
const eventoIdShow = window.location.pathname.split("/")[3];

console.log("🔍 Token:", tokenShow);
console.log("🔍 ID del evento:", eventoIdShow);
console.log("🔍 URL completa:", `${API_BASE_URL}/api/events/detalle/${eventoIdShow}`);

document.addEventListener("DOMContentLoaded", async () => {
    try {
        const url = `${API_BASE_URL}/api/events/detalle/${eventoIdShow}`;
        console.log("📡 Haciendo petición a:", url);

        const res = await fetch(url, {
            headers: {
                "Authorization": `Bearer ${tokenShow}`,
                "Accept": "application/json"
            }
        });

        console.log("📥 Status de respuesta:", res.status);

        const data = await res.json();
        console.log("📦 Datos recibidos:", data);

        if (!data.success) {
            console.error("❌ Error en la respuesta:", data);
            alert(`Error cargando detalles del evento: ${data.message || data.error || 'Error desconocido'}`);
            return;
        }

        const e = data.evento;
        console.log("✅ Evento cargado:", e);

        // Helper para actualizar elementos de forma segura
        const setText = (id, value) => {
            const el = document.getElementById(id);
            if (el) {
                el.textContent = value || 'N/A';
                console.log(`✏️ Actualizado ${id}:`, value);
            } else {
                console.warn(`⚠️ Elemento no encontrado: ${id}`);
            }
        };

        // Helper para formatear fechas
        const formatFecha = (fecha) => {
            if (!fecha) return 'N/A';
            try {
                return new Date(fecha).toLocaleString('es-BO', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            } catch (error) {
                console.error("Error formateando fecha:", fecha, error);
                return fecha;
            }
        };

        // Actualizar todos los campos
        setText("titulo", e.titulo);
        setText("descripcion", e.descripcion);
        setText("fecha_inicio", formatFecha(e.fecha_inicio));
        setText("fecha_fin", formatFecha(e.fecha_fin));
        setText("fecha_limite_inscripcion", formatFecha(e.fecha_limite_inscripcion));
        setText("tipo_evento", e.tipo_evento);
        setText("capacidad_maxima", e.capacidad_maxima);
        setText("estado", e.estado);
        setText("ciudad", e.ciudad);
        setText("direccion", e.direccion);

        // Arrays con validación
        console.log("🔍 Patrocinadores raw:", e.patrocinadores, typeof e.patrocinadores);
        console.log("🔍 Invitados raw:", e.invitados, typeof e.invitados);

        setText("patrocinadores", Array.isArray(e.patrocinadores) && e.patrocinadores.length 
            ? e.patrocinadores.join(", ") 
            : "Sin patrocinadores");
        
        setText("invitados", Array.isArray(e.invitados) && e.invitados.length 
            ? e.invitados.join(", ") 
            : "Sin invitados");

        // Imágenes
        const imgDiv = document.getElementById("imagenes");
        if (imgDiv) {
            console.log("🖼️ Imágenes raw:", e.imagenes, typeof e.imagenes);
            
            if (Array.isArray(e.imagenes) && e.imagenes.length) {
                imgDiv.innerHTML = e.imagenes.map(img => `
                    <div class="col-md-4 mb-3">
                        <img src="${img}" class="img-fluid rounded shadow" alt="Imagen del evento" onerror="console.error('Error cargando imagen:', this.src)">
                    </div>
                `).join('');
                console.log("✅ Imágenes cargadas:", e.imagenes.length);
            } else {
                imgDiv.innerHTML = "<p class='text-muted'>Sin imágenes</p>";
                console.log("ℹ️ No hay imágenes para mostrar");
            }
        }

        console.log("✅ Todos los datos cargados exitosamente");

    } catch (err) {
        console.error("❌ Error completo:", err);
        console.error("Stack trace:", err.stack);
        alert(`Error cargando detalles del evento: ${err.message}`);
    }
});
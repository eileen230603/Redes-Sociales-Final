// =====================================================
// 🛠️ edit-event.js — COMPLETO Y FUNCIONAL
// =====================================================

const token = localStorage.getItem("token");
const sqlUserId = localStorage.getItem("id_usuario");

if (!token) {
    alert("Debe iniciar sesión");
    window.location.href = "/login";
}

const eventoId = window.location.pathname.split("/")[3];

// Función helper para construir URL de imagen
function buildImageUrl(imgUrl) {
    if (!imgUrl || imgUrl.trim() === '') return null;
    
    if (imgUrl.startsWith('http://') || imgUrl.startsWith('https://')) {
        return imgUrl;
    }
    
    if (imgUrl.startsWith('/storage/')) {
        return `${window.location.origin}${imgUrl}`;
    }
    
    if (imgUrl.startsWith('storage/')) {
        return `${window.location.origin}/${imgUrl}`;
    }
    
    return `${window.location.origin}/storage/${imgUrl}`;
}

// Array para almacenar imágenes existentes que se mantendrán
let imagenesExistentes = [];
let imagenesAEliminar = [];

// =====================================================
// 1. Cargar datos del evento
// =====================================================
document.addEventListener("DOMContentLoaded", async () => {
    try {
        const res = await fetch(`${API_BASE_URL}/api/eventos/detalle/${eventoId}`, {
            headers: {
                "Authorization": `Bearer ${token}`,
                "Accept": "application/json"
            }
        });

        const data = await res.json();

        if (!data.success) {
            alert("Error cargando datos del evento");
            return;
        }

        const e = data.evento;

        // rellenar
        document.getElementById("titulo").value = e.titulo;
        document.getElementById("descripcion").value = e.descripcion;
        document.getElementById("tipo_evento").value = e.tipo_evento;

        document.getElementById("fecha_inicio").value =
            e.fecha_inicio ? e.fecha_inicio.replace(" ", "T") : "";

        document.getElementById("fecha_fin").value =
            e.fecha_fin ? e.fecha_fin.replace(" ", "T") : "";

        document.getElementById("fecha_limite_inscripcion").value =
            e.fecha_limite_inscripcion ? e.fecha_limite_inscripcion.replace(" ", "T") : "";

        document.getElementById("capacidad_maxima").value = e.capacidad_maxima ?? "";
        document.getElementById("estado").value = e.estado ?? "borrador";

        document.getElementById("ciudad").value = e.ciudad ?? "";
        document.getElementById("direccion").value = e.direccion ?? "";

        // Cargar imágenes existentes
        if (e.imagenes && Array.isArray(e.imagenes) && e.imagenes.length > 0) {
            imagenesExistentes = e.imagenes.filter(img => img && img.trim() !== '');
            mostrarImagenesExistentes(imagenesExistentes);
        } else {
            document.getElementById("imagenesExistentes").innerHTML = '<p class="text-muted">No hay imágenes disponibles</p>';
        }

    } catch (e) {
        console.error(e);
        alert("Error obteniendo los datos");
    }
});

// Función para mostrar imágenes existentes
function mostrarImagenesExistentes(imagenes) {
    const container = document.getElementById("imagenesExistentes");
    
    if (!imagenes || imagenes.length === 0) {
        container.innerHTML = '<p class="text-muted">No hay imágenes disponibles</p>';
        return;
    }

    container.innerHTML = '';
    imagenes.forEach((imgUrl, index) => {
        const fullUrl = buildImageUrl(imgUrl);
        if (!fullUrl) return;

        const div = document.createElement('div');
        div.className = 'imagen-item';
        div.innerHTML = `
            <img src="${fullUrl}" alt="Imagen ${index + 1}" 
                 onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\\'http://www.w3.org/2000/svg\\' width=\\'150\\' height=\\'150\\'%3E%3Crect fill=\\'%23f8f9fa\\' width=\\'150\\' height=\\'150\\'/%3E%3Ctext x=\\'50%25\\' y=\\'50%25\\' text-anchor=\\'middle\\' dy=\\'.3em\\' fill=\\'%23adb5bd\\' font-family=\\'Arial\\' font-size=\\'12\\'%3EError%3C/text%3E%3C/svg%3E';">
            <button type="button" class="btn-eliminar" onclick="eliminarImagenExistente('${imgUrl}', ${index})" title="Eliminar imagen">
                <i class="fas fa-times"></i>
            </button>
        `;
        container.appendChild(div);
    });
}

// Función para eliminar imagen existente
function eliminarImagenExistente(imgUrl, index) {
    if (confirm('¿Estás seguro de que deseas eliminar esta imagen?')) {
        imagenesExistentes = imagenesExistentes.filter((img, i) => i !== index);
        imagenesAEliminar.push(imgUrl);
        mostrarImagenesExistentes(imagenesExistentes);
    }
}

// Preview de nuevas imágenes
document.getElementById("nuevasImagenes").addEventListener("change", function(e) {
    const previewContainer = document.getElementById("previewNuevasImagenes");
    previewContainer.innerHTML = '';
    
    const files = Array.from(e.target.files);
    files.forEach((file, index) => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.alt = `Preview ${index + 1}`;
                previewContainer.appendChild(img);
            };
            reader.readAsDataURL(file);
        }
    });
});

// =====================================================
// 2. Enviar actualización
// =====================================================
document.getElementById("editEventForm").addEventListener("submit", async (e) => {
    e.preventDefault();

    // Preparar FormData para enviar archivos
    const formData = new FormData();
    
    // Datos básicos
    formData.append("titulo", document.getElementById("titulo").value);
    formData.append("descripcion", document.getElementById("descripcion").value);
    formData.append("tipo_evento", document.getElementById("tipo_evento").value);
    formData.append("fecha_inicio", document.getElementById("fecha_inicio").value);
    formData.append("fecha_fin", document.getElementById("fecha_fin").value);
    formData.append("fecha_limite_inscripcion", document.getElementById("fecha_limite_inscripcion").value);
    formData.append("capacidad_maxima", document.getElementById("capacidad_maxima").value);
    formData.append("estado", document.getElementById("estado").value);
    formData.append("ciudad", document.getElementById("ciudad").value);
    formData.append("direccion", document.getElementById("direccion").value);

    // Agregar nuevas imágenes (archivos)
    const nuevasImagenesInput = document.getElementById("nuevasImagenes");
    if (nuevasImagenesInput.files.length > 0) {
        Array.from(nuevasImagenesInput.files).forEach((file) => {
            formData.append("imagenes[]", file);
        });
    }

    // Agregar imágenes existentes que se mantendrán (como JSON)
    formData.append("imagenes_json", JSON.stringify(imagenesExistentes));

    try {
        const res = await fetch(`${API_BASE_URL}/api/eventos/${eventoId}`, {
            method: "PUT",
            headers: {
                "Authorization": `Bearer ${token}`,
                "Accept": "application/json"
                // NO incluir Content-Type, el navegador lo establecerá automáticamente con el boundary para FormData
            },
            body: formData
        });

        const data = await res.json();

        if (!data.success) {
            alert("Error al actualizar: " + (data.error || "Error desconocido"));
            return;
        }

        alert("Evento actualizado correctamente");
        window.location.href = "/ong/eventos";

    } catch (err) {
        console.error(err);
        alert("Error al actualizar el evento");
    }
});

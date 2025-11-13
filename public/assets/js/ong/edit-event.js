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

// =====================================================
// 1. Cargar datos del evento
// =====================================================
document.addEventListener("DOMContentLoaded", async () => {
    try {
        const res = await fetch(`${API_BASE_URL}/api/events/detalle/${eventoId}`, {
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

    } catch (e) {
        console.error(e);
        alert("Error obteniendo los datos");
    }
});

// =====================================================
// 2. Enviar actualización
// =====================================================
document.getElementById("editEventForm").addEventListener("submit", async (e) => {
    e.preventDefault();

    const payload = {
        titulo: document.getElementById("titulo").value,
        descripcion: document.getElementById("descripcion").value,
        tipo_evento: document.getElementById("tipo_evento").value,
        fecha_inicio: document.getElementById("fecha_inicio").value,
        fecha_fin: document.getElementById("fecha_fin").value,
        fecha_limite_inscripcion: document.getElementById("fecha_limite_inscripcion").value,
        capacidad_maxima: document.getElementById("capacidad_maxima").value,
        estado: document.getElementById("estado").value,
        ciudad: document.getElementById("ciudad").value,
        direccion: document.getElementById("direccion").value
    };

    try {
        const res = await fetch(`${API_BASE_URL}/api/events/${eventoId}`, {
            method: "PUT",
            headers: {
                "Authorization": `Bearer ${token}`,
                "Content-Type": "application/json"
            },
            body: JSON.stringify(payload)
        });

        const data = await res.json();

        if (!data.success) {
            alert("Error al actualizar: " + data.error);
            return;
        }

        alert("Evento actualizado correctamente");
        window.location.href = "/ong/eventos";

    } catch (err) {
        console.error(err);
        alert("Error al actualizar el evento");
    }
});

document.addEventListener("DOMContentLoaded", async () => {
    const token = localStorage.getItem("token");
    if (!token) {
        window.location.href = '/login';
        return;
    }

    const cont = document.getElementById("misEventos");
    cont.innerHTML = `<p class="text-muted">Cargando registros...</p>`;

    try {
        const res = await fetch(`${API_BASE_URL}/api/participaciones/mis-eventos`, {
            headers: { "Authorization": `Bearer ${token}`, "Accept": "application/json" }
    });

    const data = await res.json();
        if (!res.ok || !data.success) throw new Error(data.error || 'No se pudo obtener el historial');

        const eventos = data.eventos ?? [];
        if (!eventos.length) {
            cont.innerHTML = `<p class="text-muted">Aún no tienes participaciones registradas.</p>`;
            return;
        }

    cont.innerHTML = "";
        eventos.forEach(registro => {
            const evento = registro.evento || {};
            const fecha = evento.fecha_inicio
                ? new Date(evento.fecha_inicio).toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' })
                : 'Sin fecha registrada';

        cont.innerHTML += `
                <div class="card mb-3 shadow-sm" style="border-radius: 12px; border: 1px solid #F5F5F5;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="mb-0" style="color: #0C2B44;">${evento.titulo || 'Evento sin título'}</h5>
                            <span class="badge ${registro.asistio ? 'badge-success' : 'badge-warning'}">${registro.estado || 'pendiente'}</span>
                        </div>
                        <p class="mb-2" style="color: #555;">${evento.descripcion || 'Sin descripción disponible'}</p>
                        <p class="mb-0" style="color: #777; font-size: 0.9rem;"><strong>Fecha:</strong> ${fecha}</p>
                    </div>
            </div>
        `;
    });

    } catch (error) {
        console.error("Error al cargar el historial de eventos:", error);
        cont.innerHTML = `<p class="text-danger">No se pudo cargar tu historial de eventos.</p>`;
    }
});

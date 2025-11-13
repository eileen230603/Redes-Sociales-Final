document.addEventListener("DOMContentLoaded", async () => {
    const token = localStorage.getItem("token");
    const usuario = JSON.parse(localStorage.getItem("usuario"));

    if (usuario) {
        document.getElementById("nombreUsuario").textContent = usuario.nombre;
    }

    // 1. Obtener estadísticas
    const resStats = await fetch(`${API_BASE_URL}/api/eventos/mis-eventos`, {
        headers: { "Authorization": `Bearer ${token}` }
    });

    const dataStats = await resStats.json();

    document.getElementById("eventosInscritos").textContent = dataStats.total ?? 0;

    // Gráfico
    const ctx = document.getElementById("graficoEventos");
    new Chart(ctx, {
        type: "pie",
        data: {
            labels: ["Cultural", "Deportivo", "Voluntariado"],
            datasets: [{
                data: [10, 5, 8],
                backgroundColor: ["#17a2b8", "#28a745", "#ffc107"]
            }]
        }
    });

    // 2. Eventos disponibles
    cargarEventos();
});


async function cargarEventos() {
    const token = localStorage.getItem("token");
    const cont = document.getElementById("contenedorEventos");

    try {
        const res = await fetch(`${API_BASE_URL}/api/events`, {
            headers: { "Authorization": `Bearer ${token}` }
        });

        const json = await res.json();

        cont.innerHTML = "";

        json.eventos.forEach(e => {
            cont.innerHTML += `
            <div class="card mb-2">
                <div class="card-body">
                    <h4>${e.titulo}</h4>
                    <p>${e.descripcion ?? ''}</p>
                    <p><i class="fas fa-map-marker-alt"></i> ${e.ciudad}</p>

                    <a class="btn btn-info" href="/externo/eventos/${e.id}/detalle">
                        Ver Detalles
                    </a>
                </div>
            </div>`;
        });

    } catch {
        cont.innerHTML = `<p class="text-danger">Error al cargar eventos.</p>`;
    }
}

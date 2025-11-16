document.addEventListener("DOMContentLoaded", async () => {
    const token = localStorage.getItem("token");
    const cont = document.getElementById("listaEventos");

    try {
        const res = await fetch(`${API_BASE_URL}/api/eventos`, {
            headers: { "Authorization": `Bearer ${token}` }
        });

        const data = await res.json();
        cont.innerHTML = "";

        data.eventos.forEach(e => {
            cont.innerHTML += `
            <div class="col-md-4">
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <h4>${e.titulo}</h4>
                        <p>${e.descripcion ?? ''}</p>
                        <p><i class="fas fa-map-marker-alt"></i> ${e.ciudad}</p>

                        <a href="/externo/eventos/${e.id}/detalle" class="btn btn-info btn-block">
                            Ver Detalles
                        </a>
                    </div>
                </div>
            </div>`;
        });

    } catch {
        cont.innerHTML = `<p class="text-danger">Error al cargar eventos.</p>`;
    }
});

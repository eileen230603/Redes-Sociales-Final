document.addEventListener("DOMContentLoaded", async () => {
    const token = localStorage.getItem("token");

    const res = await fetch(`${API_BASE_URL}/api/eventos/mis-eventos`, {
        headers: { "Authorization": `Bearer ${token}` }
    });

    const data = await res.json();
    const cont = document.getElementById("misEventos");

    cont.innerHTML = "";

    data.eventos.forEach(r => {
        const e = r.evento;
        cont.innerHTML += `
            <div class="card mb-2 p-2">
                <h5>${e.titulo}</h5>
                <p>${e.descripcion ?? ''}</p>
                <p><strong>Fecha:</strong> ${e.fecha_inicio}</p>
            </div>
        `;
    });
});

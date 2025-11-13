document.addEventListener('DOMContentLoaded', async () => {

    const cont = document.getElementById('eventosContainer');

    const token = localStorage.getItem('token');
    const ongId = localStorage.getItem('id_entidad');

    if (!token || !ongId) {
        cont.innerHTML = "<p class='text-danger'>Debe iniciar sesión.</p>";
        return;
    }

    try {

        const res = await fetch(`${API_BASE_URL}/api/events/ong/${ongId}`, {
            headers: {
                "Authorization": `Bearer ${token}`,
                "Accept": "application/json"
            }
        });

        const data = await res.json();

        if (!data.success) {
            cont.innerHTML = "<p class='text-danger'>Error cargando eventos.</p>";
            return;
        }

        if (data.eventos.length === 0) {
            cont.innerHTML = "<p class='text-muted'>No hay eventos registrados.</p>";
            return;
        }

        cont.innerHTML = "";
        data.eventos.forEach(ev => {

            cont.innerHTML += `
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm">

                        <div class="card-body">

                            <h5 class="text-primary">${ev.titulo}</h5>
                            <p>${ev.descripcion ?? ""}</p>
                            <p class="small text-muted">
                                <i class="far fa-calendar"></i> ${ev.fecha_inicio}
                            </p>

                            <div class="d-flex justify-content-between mt-3">
                                <a href="/ong/eventos/${ev.id}/detalle" class="btn btn-outline-primary btn-sm">Ver</a>
                                <a href="/ong/eventos/${ev.id}/editar" class="btn btn-outline-warning btn-sm">Editar</a>
                                <button onclick="eliminar(${ev.id})" class="btn btn-outline-danger btn-sm">Eliminar</button>
                            </div>

                        </div>

                    </div>
                </div>
            `;
        });

    } catch (err) {
        console.error(err);
        cont.innerHTML = "<p class='text-danger'>Error de conexión.</p>";
    }
});

async function eliminar(id) {
    if (!confirm("¿Eliminar evento?")) return;

    const token = localStorage.getItem('token');

    const res = await fetch(`${API_BASE_URL}/api/events/${id}`, {
        method: "DELETE",
        headers: {
            "Authorization": `Bearer ${token}`,
            "Accept": "application/json"
        }
    });

    const data = await res.json();
    alert(data.mensaje ?? data.error);
    location.reload();
}

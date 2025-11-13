document.addEventListener("DOMContentLoaded", async () => {
    const id = document.getElementById("eventoId").value;
    const token = localStorage.getItem("token");

    const res = await fetch(`${API_BASE_URL}/api/events/detalle/${id}`, {
        headers: { "Authorization": `Bearer ${token}` }
    });

    const json = await res.json();
    const e = json.evento;

    document.getElementById("titulo").textContent = e.titulo;
    document.getElementById("descripcion").textContent = e.descripcion ?? "";
    document.getElementById("fecha_inicio").textContent = e.fecha_inicio;
    document.getElementById("ciudad").textContent = e.ciudad;
    document.getElementById("direccion").textContent = e.direccion;
    document.getElementById("capacidad_maxima").textContent = e.capacidad_maxima;

    const btnP = document.getElementById("btnParticipar");
    const btnC = document.getElementById("btnCancelar");

    btnP.onclick = async () => {
        await fetch(`${API_BASE_URL}/api/eventos/participar`, {
            method: "POST",
            headers: {
                "Authorization": `Bearer ${token}`,
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ evento_id: id })
        });

        btnP.classList.add("d-none");
        btnC.classList.remove("d-none");
    };

    btnC.onclick = async () => {
        await fetch(`${API_BASE_URL}/api/eventos/cancelar`, {
            method: "POST",
            headers: {
                "Authorization": `Bearer ${token}`,
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ evento_id: id })
        });

        btnC.classList.add("d-none");
        btnP.classList.remove("d-none");
    };
});

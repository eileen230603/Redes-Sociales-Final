document.addEventListener("DOMContentLoaded", async () => {
    const id = document.getElementById("eventoId").value;
    const token = localStorage.getItem("token");

    const res = await fetch(`${API_BASE_URL}/api/eventos/detalle/${id}`, {
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
        const res = await fetch(`${API_BASE_URL}/api/participaciones/inscribir`, {
            method: "POST",
            headers: {
                "Authorization": `Bearer ${token}`,
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ evento_id: id })
        });

        const data = await res.json();
        if (data.success) {
            btnP.classList.add("d-none");
            btnC.classList.remove("d-none");
            alert("Inscripción exitosa");
        } else {
            alert(data.error || "Error al inscribirse");
        }
    };

    btnC.onclick = async () => {
        const res = await fetch(`${API_BASE_URL}/api/participaciones/cancelar`, {
            method: "POST",
            headers: {
                "Authorization": `Bearer ${token}`,
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ evento_id: id })
        });

        const data = await res.json();
        if (data.success) {
            btnC.classList.add("d-none");
            btnP.classList.remove("d-none");
            alert("Inscripción cancelada");
        } else {
            alert(data.error || "Error al cancelar");
        }
    };
});

// ==========================================
// 🌟 create-event.js (VERSIÓN FINAL DEFINITIVA)
// ==========================================

// ===============================
// 🔐 VALIDACIÓN DE SESIÓN
// ===============================
const token = localStorage.getItem("token");
const tipoUsuario = localStorage.getItem("tipo_usuario");
const ongId = parseInt(localStorage.getItem("id_entidad"), 10); // ✔ ID REAL DE LA ONG

if (!token || tipoUsuario !== "ONG" || isNaN(ongId)) {
    alert("Debes iniciar sesión como ONG.");
    window.location.href = "/login";
}

const allFiles = [];
let ciudadDetectada = "";

// ===============================
// 🗺️ MAPA LEAFLET
// ===============================
let map, clickMarker;

function initMap() {
    const pos = [-16.5, -68.15];

    map = L.map("map").setView(pos, 13);

    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png").addTo(map);

    map.on("click", (e) => {
        const { lat, lng } = e.latlng;

        if (clickMarker) clickMarker.setLatLng(e.latlng);
        else clickMarker = L.marker(e.latlng).addTo(map);

        document.getElementById("lat").value = lat;
        document.getElementById("lng").value = lng;

        reverseGeocode(lat, lng);
    });
}

document.addEventListener("DOMContentLoaded", initMap);

// ===============================
// 🌍 GEOCODIFICACIÓN INVERSA
// ===============================
async function reverseGeocode(lat, lng) {
    try {
        const r = await fetch(
            `https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`
        );
        const data = await r.json();

        document.getElementById("locacion").value = data.display_name ?? "";

        ciudadDetectada =
            data.address?.city ||
            data.address?.town ||
            data.address?.village ||
            data.address?.state ||
            "Sin especificar";

        document.getElementById("ciudadInfo").innerText =
            "Ciudad: " + ciudadDetectada;

    } catch (e) {
        console.warn("No se pudo obtener dirección");
    }
}

// ===============================
// 🏢 EMPRESAS / INVITADOS
// ===============================
async function loadEmpresas() {
    const box = document.getElementById("patrocinadoresBox");
    box.innerHTML = "Cargando...";

    try {
        const res = await fetch(`${API_BASE_URL}/api/events/empresas/disponibles`, {
            headers: { Authorization: `Bearer ${token}` }
        });
        const data = await res.json();

        box.innerHTML = "";

        data.empresas.forEach(e => {
            box.innerHTML += `
                <label class="col-md-4 p-2 border rounded">
                    <input type="checkbox" name="patro" value="${e.id}">
                    ${e.nombre}
                </label>`;
        });

    } catch {
        box.innerHTML = "Error cargando empresas";
    }
}

async function loadInvitados() {
    const box = document.getElementById("invitadosBox");
    box.innerHTML = "Cargando...";

    try {
        const res = await fetch(`${API_BASE_URL}/api/events/invitados`, {
            headers: { Authorization: `Bearer ${token}` }
        });
        const data = await res.json();

        box.innerHTML = "";

        data.invitados.forEach(i => {
            box.innerHTML += `
                <label class="col-md-4 p-2 border rounded">
                    <input type="checkbox" name="invitados" value="${i.id}">
                    ${i.nombre}
                </label>`;
        });

    } catch {
        box.innerHTML = "Error cargando invitados";
    }
}

document.addEventListener("DOMContentLoaded", () => {
    loadEmpresas();
    loadInvitados();
});

// ===============================
// 🖼️ IMÁGENES
// ===============================
const inputImgs = document.getElementById("imagenesPromocionales");
const previewContainer = document.getElementById("previewContainer");

inputImgs.addEventListener("change", () => {
    for (const f of inputImgs.files) allFiles.push(f);
    renderPreviews();
});

function renderPreviews() {
    previewContainer.innerHTML = "";

    allFiles.forEach((f, i) => {
        const url = URL.createObjectURL(f);
        previewContainer.innerHTML += `
        <div class="position-relative m-1">
            <img src="${url}" class="rounded" width="100" height="100">
            <button class="btn btn-danger btn-sm position-absolute top-0 end-0"
                onclick="removeImage(${i})">X</button>
        </div>`;
    });
}

function removeImage(i) {
    allFiles.splice(i, 1);
    renderPreviews();
}

// ===============================
// 🚀 ENVÍO FINAL
// ===============================
document.getElementById("createEventJsonForm")
    .addEventListener("submit", submitEventForm);

async function submitEventForm(e) {
    e.preventDefault();

    const fd = new FormData();

    // ✔ ID REAL DE LA ONG
    fd.append("ong_id", ongId);

    fd.append("titulo", document.getElementById("titulo").value);
    fd.append("descripcion", document.getElementById("descripcion").value);
    fd.append("tipo_evento", document.getElementById("tipoEvento").value);

    fd.append("fecha_inicio", document.getElementById("fechaInicio").value);
    fd.append("fecha_fin", document.getElementById("fechaFinal").value);
    fd.append("fecha_limite_inscripcion", document.getElementById("fechaLimiteInscripcion").value);

    fd.append("capacidad_maxima", document.getElementById("capacidadMaxima").value);
    fd.append("estado", document.getElementById("estado").value);
    fd.append("ciudad", ciudadDetectada);
    fd.append("direccion", document.getElementById("locacion").value);

    fd.append(
        "patrocinadores",
        JSON.stringify([...document.querySelectorAll("input[name='patro']:checked")].map(e => e.value))
    );

    fd.append(
        "invitados",
        JSON.stringify([...document.querySelectorAll("input[name='invitados']:checked")].map(e => e.value))
    );

    allFiles.forEach(f => fd.append("imagenes[]", f));

    try {
        const res = await fetch(`${API_BASE_URL}/api/events`, {
            method: "POST",
            headers: { Authorization: `Bearer ${token}` },
            body: fd
        });

        const data = await res.json();

        if (!data.success) {
            alert("Error: " + data.error);
            console.log(data);
            return;
        }

        alert("Evento creado correctamente");
        window.location.href = "/ong/eventos";

    } catch (e) {
        alert("Error de servidor");
        console.error(e);
    }
}

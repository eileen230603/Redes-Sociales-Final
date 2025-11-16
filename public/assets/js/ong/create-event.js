// ==========================================
// 🌟 create-event.js (VERSIÓN FINAL DEFINITIVA)
// ==========================================

// ===============================
// 🔐 VALIDACIÓN DE SESIÓN
// ===============================
const token = localStorage.getItem("token");
const tipoUsuario = localStorage.getItem("tipo_usuario");
// Para ONG, id_entidad es igual a id_usuario (ambos son el user_id)
const ongId = parseInt(localStorage.getItem("id_entidad") || localStorage.getItem("id_usuario"), 10);

if (!token || tipoUsuario !== "ONG" || isNaN(ongId) || ongId <= 0) {
    alert("Debes iniciar sesión como ONG.");
    window.location.href = "/login";
    throw new Error("Usuario no autorizado");
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
        const res = await fetch(`${API_BASE_URL}/api/eventos/empresas/disponibles`, {
            headers: { Authorization: `Bearer ${token}` }
        });
        
        if (!res.ok) {
            throw new Error('Error en la respuesta del servidor');
        }
        
        const data = await res.json();

        if (!data.success) {
            throw new Error(data.error || 'Error al cargar empresas');
        }

        box.innerHTML = "";

        if (data.empresas && data.empresas.length > 0) {
            data.empresas.forEach(e => {
                box.innerHTML += `
                    <label class="col-md-4 p-2 border rounded">
                        <input type="checkbox" name="patro" value="${e.id}">
                        ${e.nombre}
                    </label>`;
            });
        } else {
            box.innerHTML = "<p class='text-muted'>No hay empresas disponibles</p>";
        }

    } catch (error) {
        console.error('Error cargando empresas:', error);
        box.innerHTML = "<p class='text-danger'>Error cargando empresas: " + error.message + "</p>";
    }
}

async function loadInvitados() {
    const box = document.getElementById("invitadosBox");
    box.innerHTML = "Cargando...";

    try {
        const res = await fetch(`${API_BASE_URL}/api/eventos/invitados`, {
            headers: { Authorization: `Bearer ${token}` }
        });
        
        if (!res.ok) {
            throw new Error('Error en la respuesta del servidor');
        }
        
        const data = await res.json();

        if (!data.success) {
            throw new Error(data.error || 'Error al cargar invitados');
        }

        box.innerHTML = "";

        if (data.invitados && data.invitados.length > 0) {
            data.invitados.forEach(i => {
                box.innerHTML += `
                    <label class="col-md-4 p-2 border rounded">
                        <input type="checkbox" name="invitados" value="${i.id}">
                        ${i.nombre}
                    </label>`;
            });
        } else {
            box.innerHTML = "<p class='text-muted'>No hay invitados disponibles</p>";
        }

    } catch (error) {
        console.error('Error cargando invitados:', error);
        box.innerHTML = "<p class='text-danger'>Error cargando invitados: " + error.message + "</p>";
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
        const res = await fetch(`${API_BASE_URL}/api/eventos`, {
            method: "POST",
            headers: { Authorization: `Bearer ${token}` },
            body: fd
        });

        const data = await res.json();

        if (!data.success) {
            mostrarNotificacion("error", "Error al crear evento", data.error || "Ocurrió un error inesperado");
            console.log(data);
            return;
        }

        // Mostrar notificación de éxito
        mostrarNotificacion("success", "¡Éxito!", "Evento creado correctamente");
        
        // Redirigir después de 2 segundos
        setTimeout(() => {
            window.location.href = "/ong/eventos";
        }, 2000);

    } catch (e) {
        mostrarNotificacion("error", "Error de servidor", "No se pudo conectar con el servidor");
        console.error(e);
    }
}

// ===============================
// 🔔 FUNCIÓN DE NOTIFICACIONES MEJORADA
// ===============================
function mostrarNotificacion(tipo, titulo, mensaje) {
    // Crear contenedor de toasts si no existe
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'toast-container position-fixed';
        toastContainer.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 400px;';
        document.body.appendChild(toastContainer);
    }

    // Colores y estilos mejorados según el tipo
    const colores = {
        success: { 
            bg: 'linear-gradient(135deg, #28a745 0%, #20c997 100%)',
            icon: 'fa-check-circle', 
            iconBg: '#28a745',
            text: '#ffffff',
            border: '#28a745',
            shadow: '0 8px 20px rgba(40, 167, 69, 0.3)'
        },
        error: { 
            bg: 'linear-gradient(135deg, #dc3545 0%, #e83e8c 100%)',
            icon: 'fa-exclamation-circle', 
            iconBg: '#dc3545',
            text: '#ffffff',
            border: '#dc3545',
            shadow: '0 8px 20px rgba(220, 53, 69, 0.3)'
        },
        warning: { 
            bg: 'linear-gradient(135deg, #ffc107 0%, #fd7e14 100%)',
            icon: 'fa-exclamation-triangle', 
            iconBg: '#ffc107',
            text: '#212529',
            border: '#ffc107',
            shadow: '0 8px 20px rgba(255, 193, 7, 0.3)'
        },
        info: { 
            bg: 'linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%)',
            icon: 'fa-info-circle', 
            iconBg: '#17a2b8',
            text: '#ffffff',
            border: '#17a2b8',
            shadow: '0 8px 20px rgba(23, 162, 184, 0.3)'
        }
    };

    const color = colores[tipo] || colores.info;

    // Crear el toast con diseño mejorado
    const toastId = 'toast-' + Date.now();
    const toast = document.createElement('div');
    toast.id = toastId;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
    // Estilos personalizados para el toast
    toast.style.cssText = `
        min-width: 350px;
        max-width: 400px;
        background: white;
        border-radius: 12px;
        box-shadow: ${color.shadow};
        overflow: hidden;
        margin-bottom: 15px;
        animation: slideInRight 0.4s ease-out;
        border-left: 4px solid ${color.border};
        transition: all 0.3s ease;
    `;

    toast.innerHTML = `
        <div style="
            background: ${color.bg};
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        ">
            <div style="
                width: 40px;
                height: 40px;
                background: rgba(255, 255, 255, 0.2);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            ">
                <i class="fas ${color.icon}" style="
                    font-size: 20px;
                    color: ${color.text};
                "></i>
            </div>
            <div style="flex: 1; min-width: 0;">
                <strong style="
                    display: block;
                    color: ${color.text};
                    font-size: 16px;
                    font-weight: 600;
                    margin-bottom: 2px;
                    line-height: 1.3;
                ">${titulo}</strong>
                <p style="
                    margin: 0;
                    color: ${color.text};
                    font-size: 13px;
                    opacity: 0.95;
                    line-height: 1.4;
                ">${mensaje}</p>
            </div>
            <button type="button" onclick="this.closest('[role=alert]').remove()" style="
                background: rgba(255, 255, 255, 0.2);
                border: none;
                color: ${color.text};
                width: 28px;
                height: 28px;
                border-radius: 50%;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 14px;
                transition: all 0.2s;
                flex-shrink: 0;
            " onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;

    // Agregar animación CSS si no existe
    if (!document.getElementById('toast-animations')) {
        const style = document.createElement('style');
        style.id = 'toast-animations';
        style.textContent = `
            @keyframes slideInRight {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            @keyframes slideOutRight {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(100%);
                    opacity: 0;
                }
            }
            #toast-container [role=alert] {
                animation: slideInRight 0.4s ease-out;
            }
            #toast-container [role=alert].removing {
                animation: slideOutRight 0.3s ease-in forwards;
            }
        `;
        document.head.appendChild(style);
    }

    toastContainer.appendChild(toast);

    // Auto-remover después de 4 segundos con animación
    setTimeout(() => {
        const toastElement = document.getElementById(toastId);
        if (toastElement) {
            toastElement.classList.add('removing');
            setTimeout(() => {
                toastElement.remove();
            }, 300);
        }
    }, 4000);
}

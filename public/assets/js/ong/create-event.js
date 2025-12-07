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
let urlImages = []; // Array para almacenar URLs de imágenes
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
// 🏢 PATROCINADORES
// ===============================
// Cargar patrocinadores disponibles
async function loadPatrocinadores() {
    const box = document.getElementById("patrocinadoresBox");
    if (!box) return;
    
    box.innerHTML = '<div class="col-12 text-center py-3"><div class="spinner-border text-primary" role="status"><span class="sr-only">Cargando...</span></div><p class="mt-2 text-muted">Cargando empresas...</p></div>';

    try {
        const res = await fetch(`${API_BASE_URL}/api/eventos/empresas/disponibles`, {
            headers: { 
                Authorization: `Bearer ${token}`,
                Accept: 'application/json',
                'Content-Type': 'application/json'
            }
        });
        
        if (!res.ok) {
            const errorData = await res.json().catch(() => ({}));
            throw new Error(errorData.error || `Error HTTP ${res.status}: ${res.statusText}`);
        }
        
        const data = await res.json();

        if (!data.success) {
            throw new Error(data.error || 'Error al cargar empresas');
        }

        box.innerHTML = "";

        if (!data.empresas || data.empresas.length === 0) {
            box.innerHTML = '<div class="col-12"><p class="text-muted text-center">No hay empresas disponibles para patrocinar.</p></div>';
            return;
        }

        data.empresas.forEach(emp => {
            const col = document.createElement('div');
            col.className = 'col-md-4 col-sm-6 mb-3';
            const inicial = (emp.nombre || 'E').charAt(0).toUpperCase();
            
            // Determinar qué avatar mostrar
            let avatarHTML = '';
            if (emp.foto_perfil) {
                // Si hay foto_perfil, mostrar solo la imagen (el fallback solo aparece si la imagen falla)
                avatarHTML = `
                    <div class="position-relative d-inline-block mr-3" style="width: 50px; height: 50px;">
                        <img src="${emp.foto_perfil}" alt="${emp.nombre}" class="rounded-circle patrocinador-avatar-img" 
                             style="width: 50px; height: 50px; object-fit: cover; border: 2px solid #E9ECEF; transition: all 0.3s; position: absolute; top: 0; left: 0; z-index: 2;"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="rounded-circle bg-gradient-primary text-white d-flex align-items-center justify-content-center patrocinador-fallback" 
                             style="width: 50px; height: 50px; font-weight: 600; font-size: 1.1rem; display: none; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); position: absolute; top: 0; left: 0; z-index: 1;">
                            ${inicial}
                        </div>
                    </div>
                `;
            } else {
                // Si no hay foto_perfil, mostrar solo el círculo con inicial
                avatarHTML = `
                    <div class="rounded-circle bg-gradient-primary text-white d-flex align-items-center justify-content-center mr-3" 
                         style="width: 50px; height: 50px; font-weight: 600; font-size: 1.1rem; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%);">
                        ${inicial}
                    </div>
                `;
            }
            
            col.innerHTML = `
                <div class="card border patrocinador-card" style="border-radius: 12px; transition: all 0.3s ease; border: 2px solid #E9ECEF; cursor: pointer; overflow: hidden;" 
                     onmouseover="this.style.borderColor='#00A36C'; this.style.boxShadow='0 4px 12px rgba(0,163,108,0.15)';" 
                     onmouseout="this.style.borderColor='#E9ECEF'; this.style.boxShadow='none';"
                     onclick="document.getElementById('patrocinador_${emp.id}').click();">
                    <div class="card-body p-3">
                        <div class="form-check m-0">
                            <input class="form-check-input" type="checkbox" name="patrocinadores[]" value="${emp.id}" id="patrocinador_${emp.id}" 
                                   onchange="this.closest('.patrocinador-card').style.borderColor = this.checked ? '#00A36C' : '#E9ECEF'; this.closest('.patrocinador-card').style.background = this.checked ? '#F0FDF4' : 'white';">
                            <label class="form-check-label d-flex align-items-center m-0" for="patrocinador_${emp.id}" style="cursor: pointer; width: 100%;">
                                ${avatarHTML}
                                <div class="flex-grow-1">
                                    <strong style="font-size: 0.95rem; color: #0C2B44; font-weight: 600; display: block; margin-bottom: 2px;">${emp.nombre || 'Sin nombre'}</strong>
                                    ${emp.NIT ? `<small class="text-muted" style="font-size: 0.8rem;">NIT: ${emp.NIT}</small>` : ''}
                                    ${emp.descripcion ? `<small class="text-muted d-block mt-1" style="font-size: 0.75rem; line-height: 1.3;">${emp.descripcion.substring(0, 50)}${emp.descripcion.length > 50 ? '...' : ''}</small>` : ''}
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            `;
            
            // Asegurar que solo se muestre un avatar (ocultar el fallback si la imagen carga correctamente)
            if (emp.foto_perfil) {
                setTimeout(() => {
                    const avatarImg = col.querySelector('.patrocinador-avatar-img');
                    const fallbackDiv = col.querySelector('.patrocinador-fallback');
                    if (avatarImg && fallbackDiv) {
                        // Si la imagen ya está cargada, ocultar el fallback
                        if (avatarImg.complete && avatarImg.naturalHeight !== 0 && avatarImg.naturalWidth !== 0) {
                            fallbackDiv.style.display = 'none';
                        }
                        // Cuando la imagen cargue, ocultar el fallback
                        avatarImg.addEventListener('load', () => {
                            fallbackDiv.style.display = 'none';
                        });
                    }
                }, 50);
            }
            box.appendChild(col);
        });

    } catch (error) {
        console.error("Error cargando patrocinadores:", error);
        let mensajeError = "Error al cargar empresas";
        if (error.message.includes("Failed to fetch") || error.message.includes("ERR_ADDRESS_UNREACHABLE") || error.message.includes("ERR_CONNECTION_TIMED_OUT")) {
            mensajeError = "No se pudo conectar con el servidor. Verifica que el servidor esté corriendo y que tengas conexión a internet.";
        } else {
            mensajeError = error.message || "Error desconocido";
        }
        box.innerHTML = `<div class="col-12">
            <div class="alert alert-warning" style="border-radius: 8px; border-left: 4px solid #ffc107;">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <strong>Error de conexión</strong><br>
                <small>${mensajeError}</small><br>
                <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="loadPatrocinadores()">
                    <i class="fas fa-redo mr-1"></i> Reintentar
                </button>
            </div>
        </div>`;
    }
}

// ===============================
// 🏢 EMPRESAS COLABORADORAS (PARTICIPANTES)
// ===============================
// Cargar empresas colaboradoras (participantes)
async function loadEmpresasColaboradoras() {
    const box = document.getElementById("empresasColaboradorasBox");
    if (!box) return;
    
    box.innerHTML = '<div class="col-12 text-center py-3"><div class="spinner-border text-primary" role="status"><span class="sr-only">Cargando...</span></div><p class="mt-2 text-muted">Cargando empresas...</p></div>';

    try {
        const res = await fetch(`${API_BASE_URL}/api/eventos/empresas/disponibles`, {
            headers: { 
                Authorization: `Bearer ${token}`,
                Accept: 'application/json',
                'Content-Type': 'application/json'
            }
        });
        
        if (!res.ok) {
            const errorData = await res.json().catch(() => ({}));
            throw new Error(errorData.error || `Error HTTP ${res.status}: ${res.statusText}`);
        }
        
        const data = await res.json();

        if (!data.success) {
            throw new Error(data.error || 'Error al cargar empresas');
        }

        box.innerHTML = "";

        if (data.empresas && Array.isArray(data.empresas) && data.empresas.length > 0) {
            data.empresas.forEach(empresa => {
                const empresaCard = document.createElement('div');
                empresaCard.className = 'col-md-4 col-sm-6 mb-3';
                empresaCard.innerHTML = `
                    <div class="card border h-100" style="border-radius: 8px; transition: all 0.2s;">
                        <div class="card-body p-3">
                            <div class="form-check">
                                <input class="form-check-input empresa-colaboradora-checkbox" 
                                       type="checkbox" 
                                       name="empresas_colaboradoras[]" 
                                       value="${empresa.id}" 
                                       id="empresa_${empresa.id}">
                                <label class="form-check-label w-100" for="empresa_${empresa.id}" style="cursor: pointer;">
                                    <div class="d-flex align-items-center">
                                        ${empresa.descripcion ? `
                                            <div class="flex-grow-1">
                                                <strong>${empresa.nombre || 'Sin nombre'}</strong>
                                                <br>
                                                <small class="text-muted">${empresa.descripcion.substring(0, 50)}${empresa.descripcion.length > 50 ? '...' : ''}</small>
                                            </div>
                                        ` : `
                                            <strong>${empresa.nombre || 'Sin nombre'}</strong>
                                        `}
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                `;
                box.appendChild(empresaCard);
            });
        } else {
            box.innerHTML = "<div class='col-12'><p class='text-muted text-center'>No hay empresas disponibles para asignar como colaboradoras.</p></div>";
        }

    } catch (error) {
        console.error('Error cargando empresas colaboradoras:', error);
        box.innerHTML = `<div class='col-12'><p class='text-danger text-center'>Error cargando empresas: ${error.message}</p></div>`;
    }
}

async function loadInvitados() {
    const box = document.getElementById("invitadosBox");
    box.innerHTML = "Cargando...";

    try {
        const res = await fetch(`${API_BASE_URL}/api/eventos/invitados`, {
            headers: { 
                Authorization: `Bearer ${token}`,
                Accept: 'application/json',
                'Content-Type': 'application/json'
            }
        });
        
        if (!res.ok) {
            const errorData = await res.json().catch(() => ({}));
            throw new Error(errorData.error || `Error HTTP ${res.status}: ${res.statusText}`);
        }
        
        const data = await res.json();

        if (!data.success) {
            throw new Error(data.error || 'Error al cargar invitados');
        }

        box.innerHTML = "";

        if (data.invitados && Array.isArray(data.invitados) && data.invitados.length > 0) {
            data.invitados.forEach(i => {
                box.innerHTML += `
                    <label class="col-md-4 p-2 border rounded">
                        <input type="checkbox" name="invitados" value="${i.id}">
                        ${i.nombre || 'Sin nombre'}
                    </label>`;
            });
        } else {
            box.innerHTML = "<p class='text-muted'>No hay invitados disponibles</p>";
        }

    } catch (error) {
        console.error('Error cargando invitados:', error);
        let mensajeError = "Error al cargar invitados";
        if (error.message.includes("Failed to fetch") || error.message.includes("ERR_ADDRESS_UNREACHABLE") || error.message.includes("ERR_CONNECTION_TIMED_OUT")) {
            mensajeError = "No se pudo conectar con el servidor. Verifica que el servidor esté corriendo.";
        } else {
            mensajeError = error.message || "Error desconocido";
        }
        box.innerHTML = `<div class="col-12">
            <div class="alert alert-warning" style="border-radius: 8px; border-left: 4px solid #ffc107;">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <strong>Error de conexión</strong><br>
                <small>${mensajeError}</small><br>
                <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="loadInvitados()">
                    <i class="fas fa-redo mr-1"></i> Reintentar
                </button>
            </div>
        </div>`;
    }
}

document.addEventListener("DOMContentLoaded", () => {
    loadPatrocinadores();
    loadEmpresasColaboradoras();
    loadInvitados();
    
    // Control de capacidad máxima con botones +/-
    const capacidadInput = document.getElementById("capacidadMaxima");
    const btnIncrementar = document.getElementById("btnIncrementarCapacidad");
    const btnDecrementar = document.getElementById("btnDecrementarCapacidad");
    
    if (capacidadInput && btnIncrementar && btnDecrementar) {
        // Botón incrementar
        btnIncrementar.addEventListener("click", function() {
            let value = parseInt(capacidadInput.value) || 0;
            value++;
            capacidadInput.value = value;
            capacidadInput.dispatchEvent(new Event('input'));
        });
        
        // Botón decrementar
        btnDecrementar.addEventListener("click", function() {
            let value = parseInt(capacidadInput.value) || 0;
            if (value > 0) {
                value--;
                capacidadInput.value = value;
                capacidadInput.dispatchEvent(new Event('input'));
            }
        });
        
        // Validar entrada manual
        capacidadInput.addEventListener("input", function(e) {
            let value = parseInt(this.value) || 0;
            if (value < 0) value = 0;
            this.value = value;
        });
        
        // Prevenir valores negativos
        capacidadInput.addEventListener("keydown", function(e) {
            if (e.key === '-' || e.key === 'e' || e.key === 'E' || e.key === '+' || e.key === '.') {
                e.preventDefault();
            }
        });
    }
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
// 🖼️ IMÁGENES POR URL
// ===============================
function addUrlImage(url) {
    // Verificar si la URL ya existe
    if (urlImages.includes(url)) {
        mostrarNotificacion('warning', 'URL duplicada', 'Esta URL ya ha sido agregada');
        return;
    }

    urlImages.push(url);
    updateUrlImagesPreview();
}

function updateUrlImagesPreview() {
    const container = document.getElementById('urlImagesContainer');
    if (!container) return;
    
    container.innerHTML = '';

    if (urlImages.length === 0) {
        return;
    }

    urlImages.forEach((url, index) => {
        const wrapper = document.createElement('div');
        wrapper.className = 'image-preview-wrapper';
        wrapper.style.cssText = 'border-radius: 8px; overflow: hidden; border: 2px solid #28a745;';
        
        const img = document.createElement('img');
        img.src = url;
        img.alt = `Imagen URL ${index + 1}`;
        img.style.cssText = 'width: 100%; height: 150px; object-fit: cover; cursor: pointer;';
        img.onclick = () => window.open(url, '_blank');
        img.onerror = function() {
            this.onerror = null;
            this.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="150" height="150"%3E%3Crect fill="%23f8f9fa" width="150" height="150"/%3E%3Ctext x="50%25" y="50%25" text-anchor="middle" dy=".3em" fill="%23adb5bd" font-family="Arial" font-size="12"%3EError cargando%3C/text%3E%3C/svg%3E';
            this.style.objectFit = 'contain';
            this.style.padding = '10px';
        };
        
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'remove-image';
        removeBtn.innerHTML = '<i class="fas fa-times"></i>';
        removeBtn.onclick = () => removeUrlImage(index);
        
        wrapper.appendChild(img);
        wrapper.appendChild(removeBtn);
        container.appendChild(wrapper);
    });
}

function removeUrlImage(index) {
    urlImages.splice(index, 1);
    updateUrlImagesPreview();
}

// Event listeners para agregar URL
document.addEventListener('DOMContentLoaded', function() {
    const btnAgregarUrl = document.getElementById('btnAgregarUrl');
    const imagenUrlInput = document.getElementById('imagen_url');
    
    if (btnAgregarUrl) {
        btnAgregarUrl.addEventListener('click', function() {
            const url = imagenUrlInput.value.trim();
            
            if (!url) {
                mostrarNotificacion('warning', 'URL vacía', 'Por favor ingresa una URL válida');
                return;
            }

            // Validar que sea una URL válida
            try {
                new URL(url);
            } catch (e) {
                mostrarNotificacion('error', 'URL inválida', 'Por favor ingresa una URL válida (ej: https://ejemplo.com/imagen.jpg)');
                return;
            }

            // Verificar que sea una imagen (por extensión)
            const imageExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.webp'];
            const isImage = imageExtensions.some(ext => url.toLowerCase().includes(ext)) || 
                           url.match(/\.(jpg|jpeg|png|gif|webp)(\?|$)/i);

            if (!isImage) {
                Swal.fire({
                    icon: 'warning',
                    title: 'URL no parece ser una imagen',
                    text: 'La URL debe apuntar a una imagen (JPG, PNG, GIF, WEBP)',
                    showCancelButton: true,
                    confirmButtonText: 'Agregar de todos modos',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        addUrlImage(url);
                        imagenUrlInput.value = '';
                    }
                });
            } else {
                addUrlImage(url);
                imagenUrlInput.value = '';
            }
        });
    }

    // Permitir agregar URL con Enter
    if (imagenUrlInput) {
        imagenUrlInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (btnAgregarUrl) btnAgregarUrl.click();
            }
        });
    }
});

// ===============================
// 🚀 ENVÍO FINAL
// ===============================
document.getElementById("createEventJsonForm")
    .addEventListener("submit", submitEventForm);

async function submitEventForm(e) {
    e.preventDefault();

    // ===============================
    // VALIDACIÓN ESTRICTA DE TODOS LOS CAMPOS OBLIGATORIOS
    // ===============================
    const titulo = document.getElementById("titulo").value.trim();
    const descripcion = document.getElementById("descripcion").value.trim();
    const tipoEvento = document.getElementById("tipoEvento").value;
    const fechaInicio = document.getElementById("fechaInicio").value;
    const fechaFinal = document.getElementById("fechaFinal").value;
    const estado = document.getElementById("estado").value;
    const locacion = document.getElementById("locacion").value.trim();
    const lat = document.getElementById("lat").value;
    const lng = document.getElementById("lng").value;

    // Validar título
    if (!titulo) {
        mostrarNotificacion("error", "Completa este campo", "El título del evento es obligatorio");
        document.getElementById("titulo").focus();
        document.getElementById("titulo").classList.add("is-invalid");
        return;
    } else {
        document.getElementById("titulo").classList.remove("is-invalid");
    }

    // Validar descripción
    if (!descripcion) {
        mostrarNotificacion("error", "Completa este campo", "La descripción del evento es obligatoria");
        document.getElementById("descripcion").focus();
        document.getElementById("descripcion").classList.add("is-invalid");
        return;
    } else {
        document.getElementById("descripcion").classList.remove("is-invalid");
    }

    // Validar tipo de evento
    if (!tipoEvento || tipoEvento === "") {
        mostrarNotificacion("error", "Completa este campo", "Debes seleccionar un tipo de evento");
        document.getElementById("tipoEvento").focus();
        document.getElementById("tipoEvento").classList.add("is-invalid");
        return;
    } else {
        document.getElementById("tipoEvento").classList.remove("is-invalid");
    }

    // Validar fecha de inicio
    if (!fechaInicio) {
        mostrarNotificacion("error", "Completa este campo", "La fecha de inicio es obligatoria");
        document.getElementById("fechaInicio").focus();
        document.getElementById("fechaInicio").classList.add("is-invalid");
        return;
    } else {
        document.getElementById("fechaInicio").classList.remove("is-invalid");
    }

    // Validar fecha de fin (obligatoria)
    if (!fechaFinal) {
        mostrarNotificacion("error", "Completa este campo", "La fecha de finalización es obligatoria");
        document.getElementById("fechaFinal").focus();
        document.getElementById("fechaFinal").classList.add("is-invalid");
        return;
    } else {
        document.getElementById("fechaFinal").classList.remove("is-invalid");
    }

    // Validar locación/dirección (obligatoria)
    if (!locacion || locacion === "") {
        mostrarNotificacion("error", "Completa este campo", "Debes seleccionar una ubicación en el mapa");
        document.getElementById("locacion").focus();
        document.getElementById("locacion").classList.add("is-invalid");
        return;
    } else {
        document.getElementById("locacion").classList.remove("is-invalid");
    }

    // Validar coordenadas (obligatorias)
    if (!lat || !lng || lat === "" || lng === "") {
        mostrarNotificacion("error", "Completa este campo", "Debes seleccionar una ubicación en el mapa");
        document.getElementById("locacion").focus();
        document.getElementById("locacion").classList.add("is-invalid");
        return;
    } else {
        document.getElementById("locacion").classList.remove("is-invalid");
    }

    // Validar que haya al menos una imagen (archivo o URL)
    if (allFiles.length === 0 && urlImages.length === 0) {
        mostrarNotificacion("error", "Completa este campo", "Debes agregar al menos una imagen promocional");
        document.getElementById("imagenesPromocionales").focus();
        return;
    }

    // Validar estado (obligatorio)
    if (!estado || estado === "" || estado === null) {
        mostrarNotificacion("error", "Completa este campo", "Debes seleccionar un estado para el evento");
        document.getElementById("estado").focus();
        document.getElementById("estado").classList.add("is-invalid");
        return;
    } else {
        document.getElementById("estado").classList.remove("is-invalid");
    }

    // Validar fecha de inicio sea futura
    const fechaInicioDate = new Date(fechaInicio);
    const ahora = new Date();
    if (fechaInicioDate <= ahora) {
        mostrarNotificacion("error", "Fecha inválida", "La fecha de inicio debe ser una fecha futura");
        document.getElementById("fechaInicio").focus();
        document.getElementById("fechaInicio").classList.add("is-invalid");
        return;
    } else {
        document.getElementById("fechaInicio").classList.remove("is-invalid");
    }

    // Validar fecha de fin (obligatoria y debe ser posterior a fecha de inicio)
    const fechaFinDate = new Date(fechaFinal);
    if (fechaFinDate <= fechaInicioDate) {
        mostrarNotificacion("error", "Fecha inválida", "La fecha de finalización debe ser posterior a la fecha de inicio");
        document.getElementById("fechaFinal").focus();
        document.getElementById("fechaFinal").classList.add("is-invalid");
        return;
    } else {
        document.getElementById("fechaFinal").classList.remove("is-invalid");
    }

    // Validar fecha límite de inscripción si está presente
    const fechaLimiteInscripcion = document.getElementById("fechaLimiteInscripcion").value;
    if (fechaLimiteInscripcion) {
        const fechaLimiteDate = new Date(fechaLimiteInscripcion);
        if (fechaLimiteDate >= fechaInicioDate) {
            mostrarNotificacion("error", "Fecha inválida", "La fecha límite de inscripción debe ser anterior a la fecha de inicio");
            document.getElementById("fechaLimiteInscripcion").focus();
            return;
        }
    }

    // Validar capacidad máxima (opcional, pero si se ingresa debe ser válido)
    const capacidadMaximaInput = document.getElementById("capacidadMaxima");
    const capacidadMaxima = capacidadMaximaInput ? capacidadMaximaInput.value.trim() : "";
    if (capacidadMaxima && capacidadMaxima !== "0") {
        const capacidadNum = parseInt(capacidadMaxima, 10);
        // Verificar que sea un número válido y mayor a 0
        if (isNaN(capacidadNum) || capacidadNum < 1) {
            mostrarNotificacion("error", "Valor inválido", "La capacidad máxima debe ser un número mayor a 0");
            if (capacidadMaximaInput) {
                capacidadMaximaInput.focus();
                capacidadMaximaInput.value = "0";
            }
            return;
        }
    }

    const fd = new FormData();

    // ✔ ID REAL DE LA ONG
    fd.append("ong_id", ongId);

    fd.append("titulo", titulo);
    fd.append("descripcion", descripcion);
    fd.append("tipo_evento", tipoEvento);

    fd.append("fecha_inicio", fechaInicio);
    fd.append("fecha_fin", fechaFinal);
    fd.append("fecha_limite_inscripcion", fechaLimiteInscripcion || "");
    fd.append("lat", lat);
    fd.append("lng", lng);

    // Capacidad máxima (solo números válidos)
    const capacidadMaximaValue = document.getElementById("capacidadMaxima") ? document.getElementById("capacidadMaxima").value.trim() : "";
    if (capacidadMaximaValue) {
        const capacidadNum = parseInt(capacidadMaximaValue, 10);
        if (!isNaN(capacidadNum) && capacidadNum > 0) {
            fd.append("capacidad_maxima", capacidadNum);
        }
    }
    fd.append("estado", estado);
    fd.append("ciudad", ciudadDetectada || "");
    fd.append("direccion", locacion);

    // Patrocinadores como array
    const patrocinadoresIds = [...document.querySelectorAll("input[name='patrocinadores[]']:checked")].map(e => parseInt(e.value));
    
    // Invitados como array
    const invitadosIds = [...document.querySelectorAll("input[name='invitados']:checked")].map(e => parseInt(e.value));
    
    // Enviar patrocinadores
    if (patrocinadoresIds.length > 0) {
        patrocinadoresIds.forEach(id => {
            fd.append("patrocinadores[]", id);
        });
    } else {
        // Enviar array vacío para que Laravel lo reconozca como array
        fd.append("patrocinadores", "[]");
    }
    
    // Enviar invitados
    if (invitadosIds.length > 0) {
        invitadosIds.forEach(id => {
            fd.append("invitados[]", id);
        });
    } else {
        // Enviar array vacío para que Laravel lo reconozca como array
        fd.append("invitados", "[]");
    }

    // Agregar archivos de imagen
    allFiles.forEach(f => fd.append("imagenes[]", f));
    
    // Agregar URLs de imágenes como JSON string
    if (urlImages.length > 0) {
        fd.append("imagenes_urls", JSON.stringify(urlImages));
    }

    // Mostrar indicador de carga
    const submitBtn = document.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Creando evento...';

    try {
        const res = await fetch(`${API_BASE_URL}/api/eventos`, {
            method: "POST",
            headers: { Authorization: `Bearer ${token}` },
            body: fd
        });

        const data = await res.json();
        
        // Restaurar botón
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;

        if (!res.ok || !data.success) {
            // Si hay errores de validación, mostrarlos
            let mensajeError = data.error || "Ocurrió un error inesperado";
            
            if (data.errors && typeof data.errors === 'object') {
                const erroresArray = Object.entries(data.errors).map(([campo, mensajes]) => {
                    const mensaje = Array.isArray(mensajes) ? mensajes[0] : mensajes;
                    // Traducir nombres de campos al español
                    const camposTraducidos = {
                        'ong_id': 'ID de ONG',
                        'titulo': 'Título',
                        'tipo_evento': 'Tipo de evento',
                        'fecha_inicio': 'Fecha de inicio',
                        'fecha_fin': 'Fecha de finalización',
                        'fecha_limite_inscripcion': 'Fecha límite de inscripción',
                        'estado': 'Estado',
                        'patrocinadores': 'Patrocinadores',
                        'invitados': 'Invitados'
                    };
                    const campoTraducido = camposTraducidos[campo] || campo;
                    return `${campoTraducido}: ${mensaje}`;
                });
                mensajeError = erroresArray.join('\n');
            }
            
            mostrarNotificacion("error", "Error al crear evento", mensajeError);
            console.error("Error completo:", data);
            // Restaurar botón en caso de error
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
            return;
        }

        // Obtener empresas colaboradoras seleccionadas
        const empresasColaboradorasIds = [...document.querySelectorAll("input[name='empresas_colaboradoras[]']:checked")].map(e => parseInt(e.value));
        
        console.log('📋 Empresas colaboradoras seleccionadas:', empresasColaboradorasIds);
        console.log('📦 Evento creado:', data.evento);
        
        // Si hay empresas colaboradoras seleccionadas, asignarlas al evento
        if (empresasColaboradorasIds.length > 0) {
            // Verificar que el evento tenga ID
            const eventoId = data.evento?.id;
            
            if (!eventoId) {
                console.error('❌ No se pudo obtener el ID del evento creado');
                mostrarNotificacion("warning", "Advertencia", "El evento se creó pero no se pudo asignar las empresas. Por favor, asigna las empresas manualmente desde la edición del evento.");
            } else {
                try {
                    console.log(`🔄 Asignando ${empresasColaboradorasIds.length} empresa(s) al evento ${eventoId}`);
                    
                    // Esperar un momento para asegurar que el evento esté completamente guardado
                    await new Promise(resolve => setTimeout(resolve, 500));
                    
                    const asignarRes = await fetch(`${API_BASE_URL}/api/eventos/${eventoId}/empresas/asignar`, {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            empresas: empresasColaboradorasIds
                        })
                    });

                    console.log('📡 Respuesta de asignación:', asignarRes.status, asignarRes.statusText);

                    const asignarData = await asignarRes.json();
                    console.log('📊 Datos de asignación:', asignarData);
                    
                    if (asignarRes.ok && asignarData.success) {
                        console.log(`✅ ${asignarData.empresas_asignadas} empresa(s) asignada(s) correctamente`);
                        mostrarNotificacion("success", "¡Éxito!", `Evento creado correctamente y ${asignarData.empresas_asignadas} empresa(s) asignada(s) y notificadas`);
                    } else {
                        console.error('❌ Error al asignar empresas colaboradoras:', asignarData.error || 'Error desconocido');
                        mostrarNotificacion("warning", "Advertencia", `El evento se creó pero hubo un problema al asignar empresas: ${asignarData.error || 'Error desconocido'}`);
                    }
                } catch (error) {
                    console.error('❌ Error al asignar empresas colaboradoras:', error);
                    mostrarNotificacion("warning", "Advertencia", `El evento se creó pero hubo un error al asignar empresas. Por favor, asigna las empresas manualmente desde la edición del evento.`);
                }
            }
        } else {
            // Mostrar notificación de éxito solo si no hay empresas
        mostrarNotificacion("success", "¡Éxito!", "Evento creado correctamente");
        }
        
        // Redirigir inmediatamente (sin delay)
        window.location.href = "/ong/eventos";

    } catch (e) {
        mostrarNotificacion("error", "Error de servidor", "No se pudo conectar con el servidor");
        console.error(e);
        // Restaurar botón en caso de error
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        }
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

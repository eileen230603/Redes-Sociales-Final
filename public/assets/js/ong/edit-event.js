// =====================================================
// 🛠️ edit-event.js — COMPLETO Y FUNCIONAL
// =====================================================

const token = localStorage.getItem("token");
const sqlUserId = localStorage.getItem("id_usuario");

if (!token) {
    // La función mostrarNotificacion se definirá más adelante
    setTimeout(() => {
        if (typeof mostrarNotificacion === 'function') {
            mostrarNotificacion("error", "Acceso denegado", "Debe iniciar sesión");
        } else {
            alert("Debe iniciar sesión");
        }
        window.location.href = "/login";
    }, 100);
}

const eventoId = window.location.pathname.split("/")[3];

// Función helper para construir URL de imagen
function buildImageUrl(imgUrl) {
    if (!imgUrl || imgUrl.trim() === '') return null;
    
    if (imgUrl.startsWith('http://') || imgUrl.startsWith('https://')) {
        return imgUrl;
    }
    
    if (imgUrl.startsWith('/storage/')) {
        return `${window.location.origin}${imgUrl}`;
    }
    
    if (imgUrl.startsWith('storage/')) {
        return `${window.location.origin}/${imgUrl}`;
    }
    
    return `${window.location.origin}/storage/${imgUrl}`;
}

// Array para almacenar imágenes existentes que se mantendrán
let imagenesExistentes = [];
let imagenesAEliminar = [];
let urlImages = []; // Array para almacenar URLs de imágenes nuevas

// =====================================================
// 1. Cargar datos del evento
// =====================================================
document.addEventListener("DOMContentLoaded", async () => {
    try {
        const res = await fetch(`${API_BASE_URL}/api/eventos/detalle/${eventoId}`, {
            headers: {
                "Authorization": `Bearer ${token}`,
                "Accept": "application/json"
            }
        });

        const data = await res.json();

        if (!data.success) {
            mostrarNotificacion("error", "Error", "Error cargando datos del evento");
            return;
        }

        const e = data.evento;

        // rellenar
        document.getElementById("titulo").value = e.titulo;
        document.getElementById("descripcion").value = e.descripcion;
        document.getElementById("tipo_evento").value = e.tipo_evento;

        document.getElementById("fecha_inicio").value =
            e.fecha_inicio ? e.fecha_inicio.replace(" ", "T") : "";

        document.getElementById("fecha_fin").value =
            e.fecha_fin ? e.fecha_fin.replace(" ", "T") : "";

        document.getElementById("fecha_limite_inscripcion").value =
            e.fecha_limite_inscripcion ? e.fecha_limite_inscripcion.replace(" ", "T") : "";

        document.getElementById("capacidad_maxima").value = e.capacidad_maxima ?? "";
        document.getElementById("estado").value = e.estado ?? "borrador";

        document.getElementById("ciudad").value = e.ciudad ?? "";
        document.getElementById("direccion").value = e.direccion ?? "";

        // Cargar imágenes existentes
        if (e.imagenes && Array.isArray(e.imagenes) && e.imagenes.length > 0) {
            imagenesExistentes = e.imagenes.filter(img => img && img.trim() !== '');
            mostrarImagenesExistentes(imagenesExistentes);
        } else {
            document.getElementById("imagenesExistentes").innerHTML = '<p class="text-muted">No hay imágenes disponibles</p>';
        }

    } catch (e) {
        console.error(e);
        mostrarNotificacion("error", "Error", "Error obteniendo los datos del evento");
    }
});

// Función para mostrar imágenes existentes
function mostrarImagenesExistentes(imagenes) {
    const container = document.getElementById("imagenesExistentes");
    
    if (!imagenes || imagenes.length === 0) {
        container.innerHTML = '<p class="text-muted">No hay imágenes disponibles</p>';
        return;
    }

    container.innerHTML = '';
    imagenes.forEach((imgUrl, index) => {
        const fullUrl = buildImageUrl(imgUrl);
        if (!fullUrl) return;

        const div = document.createElement('div');
        div.className = 'imagen-item';
        div.innerHTML = `
            <img src="${fullUrl}" alt="Imagen ${index + 1}" 
                 onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\\'http://www.w3.org/2000/svg\\' width=\\'150\\' height=\\'150\\'%3E%3Crect fill=\\'%23f8f9fa\\' width=\\'150\\' height=\\'150\\'/%3E%3Ctext x=\\'50%25\\' y=\\'50%25\\' text-anchor=\\'middle\\' dy=\\'.3em\\' fill=\\'%23adb5bd\\' font-family=\\'Arial\\' font-size=\\'12\\'%3EError%3C/text%3E%3C/svg%3E';">
            <button type="button" class="btn-eliminar" onclick="eliminarImagenExistente('${imgUrl}', ${index})" title="Eliminar imagen">
                <i class="fas fa-times"></i>
            </button>
        `;
        container.appendChild(div);
    });
}

// Función para eliminar imagen existente
function eliminarImagenExistente(imgUrl, index) {
    if (confirm('¿Estás seguro de que deseas eliminar esta imagen?')) {
        imagenesExistentes = imagenesExistentes.filter((img, i) => i !== index);
        imagenesAEliminar.push(imgUrl);
        mostrarImagenesExistentes(imagenesExistentes);
    }
}

// Preview de nuevas imágenes
document.getElementById("nuevasImagenes").addEventListener("change", function(e) {
    const previewContainer = document.getElementById("previewNuevasImagenes");
    previewContainer.innerHTML = '';
    
    const files = Array.from(e.target.files);
    files.forEach((file, index) => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.alt = `Preview ${index + 1}`;
                previewContainer.appendChild(img);
            };
            reader.readAsDataURL(file);
        }
    });
});

// ===============================
// 🖼️ IMÁGENES POR URL
// ===============================
function addUrlImage(url) {
    // Verificar si la URL ya existe
    if (urlImages.includes(url)) {
        mostrarNotificacion("warning", "URL duplicada", "Esta URL ya ha sido agregada");
        return;
    }

    urlImages.push(url);
    updateUrlImagesPreview();
}

function updateUrlImagesPreview() {
    const container = document.getElementById('urlImagesContainerEdit');
    if (!container) return;
    
    container.innerHTML = '';

    if (urlImages.length === 0) {
        return;
    }

    urlImages.forEach((url, index) => {
        const wrapper = document.createElement('div');
        wrapper.className = 'image-preview-wrapper-url';
        
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
    const btnAgregarUrl = document.getElementById('btnAgregarUrlEdit');
    const imagenUrlInput = document.getElementById('imagen_url_edit');
    
    if (btnAgregarUrl) {
        btnAgregarUrl.addEventListener('click', function() {
            const url = imagenUrlInput.value.trim();
            
            if (!url) {
                mostrarNotificacion("warning", "URL vacía", "Por favor ingresa una URL válida");
                return;
            }

            // Validar que sea una URL válida
            try {
                new URL(url);
            } catch (e) {
                mostrarNotificacion("error", "URL inválida", "Por favor ingresa una URL válida (ej: https://ejemplo.com/imagen.jpg)");
                return;
            }

            // Verificar que sea una imagen (por extensión)
            const imageExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.webp'];
            const isImage = imageExtensions.some(ext => url.toLowerCase().includes(ext)) || 
                           url.match(/\.(jpg|jpeg|png|gif|webp)(\?|$)/i);

            if (!isImage) {
                if (confirm('La URL no parece ser una imagen. ¿Deseas agregarla de todos modos?')) {
                    addUrlImage(url);
                    imagenUrlInput.value = '';
                }
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
    
    // Validar capacidad máxima en tiempo real (solo números)
    const capacidadInput = document.getElementById("capacidad_maxima");
    if (capacidadInput) {
        capacidadInput.addEventListener("input", function(e) {
            // Remover cualquier carácter que no sea número
            let value = this.value.replace(/[^0-9]/g, '');
            if (this.value !== value) {
                this.value = value;
                mostrarNotificacion("warning", "Carácter inválido", "Solo se permiten números en este campo");
            }
        });
        
        capacidadInput.addEventListener("paste", function(e) {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData).getData('text');
            const numbersOnly = paste.replace(/[^0-9]/g, '');
            if (numbersOnly !== paste) {
                mostrarNotificacion("warning", "Contenido inválido", "Solo se permiten números. Se han eliminado los caracteres no numéricos");
            }
            this.value = numbersOnly;
        });
    }
});

// =====================================================
// 2. Enviar actualización
// =====================================================
document.getElementById("editEventForm").addEventListener("submit", async (e) => {
    e.preventDefault();

    // ===============================
    // VALIDACIÓN DE CAMPOS OBLIGATORIOS
    // ===============================
    const titulo = document.getElementById("titulo").value.trim();
    const tipoEvento = document.getElementById("tipo_evento").value;
    const fechaInicio = document.getElementById("fecha_inicio").value;
    const estado = document.getElementById("estado").value;

    // Validar título
    if (!titulo) {
        mostrarNotificacion("error", "Campo requerido", "El título del evento es obligatorio");
        document.getElementById("titulo").focus();
        return;
    }

    // Validar tipo de evento
    if (!tipoEvento || tipoEvento === "") {
        mostrarNotificacion("error", "Campo requerido", "Debes seleccionar un tipo de evento");
        document.getElementById("tipo_evento").focus();
        return;
    }

    // Validar fecha de inicio
    if (!fechaInicio) {
        mostrarNotificacion("error", "Campo requerido", "La fecha de inicio es obligatoria");
        document.getElementById("fecha_inicio").focus();
        return;
    }

    // Validar estado
    if (!estado || estado === "") {
        mostrarNotificacion("error", "Campo requerido", "Debes seleccionar un estado para el evento");
        document.getElementById("estado").focus();
        return;
    }

    // Validar fecha de inicio sea futura (solo si se cambió)
    const fechaInicioDate = new Date(fechaInicio);
    const ahora = new Date();
    if (fechaInicioDate <= ahora) {
        mostrarNotificacion("error", "Fecha inválida", "La fecha de inicio debe ser una fecha futura");
        document.getElementById("fecha_inicio").focus();
        return;
    }

    // Validar fecha de fin si está presente
    const fechaFin = document.getElementById("fecha_fin").value;
    if (fechaFin) {
        const fechaFinDate = new Date(fechaFin);
        if (fechaFinDate <= fechaInicioDate) {
            mostrarNotificacion("error", "Fecha inválida", "La fecha de finalización debe ser posterior a la fecha de inicio");
            document.getElementById("fecha_fin").focus();
            return;
        }
    }

    // Validar fecha límite de inscripción si está presente
    const fechaLimiteInscripcion = document.getElementById("fecha_limite_inscripcion").value;
    if (fechaLimiteInscripcion) {
        const fechaLimiteDate = new Date(fechaLimiteInscripcion);
        if (fechaLimiteDate >= fechaInicioDate) {
            mostrarNotificacion("error", "Fecha inválida", "La fecha límite de inscripción debe ser anterior a la fecha de inicio");
            document.getElementById("fecha_limite_inscripcion").focus();
            return;
        }
    }

    // Validar capacidad máxima (solo números)
    const capacidadMaxima = document.getElementById("capacidad_maxima").value.trim();
    if (capacidadMaxima) {
        // Verificar que solo contenga números
        if (!/^\d+$/.test(capacidadMaxima)) {
            mostrarNotificacion("error", "Valor inválido", "La capacidad máxima debe ser un número válido (solo números, sin letras, símbolos ni espacios)");
            document.getElementById("capacidad_maxima").focus();
            document.getElementById("capacidad_maxima").value = "";
            return;
        }
        // Verificar que sea mayor a 0
        const capacidadNum = parseInt(capacidadMaxima, 10);
        if (isNaN(capacidadNum) || capacidadNum < 1) {
            mostrarNotificacion("error", "Valor inválido", "La capacidad máxima debe ser un número mayor a 0");
            document.getElementById("capacidad_maxima").focus();
            document.getElementById("capacidad_maxima").value = "";
            return;
        }
    }

    // Preparar FormData para enviar archivos
    const formData = new FormData();
    
    // Datos básicos
    formData.append("titulo", titulo);
    formData.append("descripcion", document.getElementById("descripcion").value || "");
    formData.append("tipo_evento", tipoEvento);
    formData.append("fecha_inicio", fechaInicio);
    formData.append("fecha_fin", fechaFin || "");
    formData.append("fecha_limite_inscripcion", fechaLimiteInscripcion || "");
    
    // Capacidad máxima (solo números válidos)
    if (capacidadMaxima && /^\d+$/.test(capacidadMaxima)) {
        formData.append("capacidad_maxima", parseInt(capacidadMaxima, 10));
    }
    
    formData.append("estado", estado);
    formData.append("ciudad", document.getElementById("ciudad").value || "");
    formData.append("direccion", document.getElementById("direccion").value || "");

    // Agregar nuevas imágenes (archivos)
    const nuevasImagenesInput = document.getElementById("nuevasImagenes");
    if (nuevasImagenesInput.files.length > 0) {
        Array.from(nuevasImagenesInput.files).forEach((file) => {
            formData.append("imagenes[]", file);
        });
    }

    // Agregar imágenes existentes que se mantendrán (como JSON)
    formData.append("imagenes_json", JSON.stringify(imagenesExistentes));
    
    // Agregar URLs de imágenes nuevas como JSON string
    if (urlImages.length > 0) {
        formData.append("imagenes_urls", JSON.stringify(urlImages));
    }

    try {
        const res = await fetch(`${API_BASE_URL}/api/eventos/${eventoId}`, {
            method: "PUT",
            headers: {
                "Authorization": `Bearer ${token}`,
                "Accept": "application/json"
                // NO incluir Content-Type, el navegador lo establecerá automáticamente con el boundary para FormData
            },
            body: formData
        });

        const data = await res.json();

        if (!res.ok || !data.success) {
            // Si hay errores de validación, mostrarlos
            let mensajeError = data.error || "Ocurrió un error inesperado";
            
            if (data.errors && typeof data.errors === 'object') {
                const erroresArray = Object.entries(data.errors).map(([campo, mensajes]) => {
                    const mensaje = Array.isArray(mensajes) ? mensajes[0] : mensajes;
                    // Traducir nombres de campos al español
                    const camposTraducidos = {
                        'titulo': 'Título',
                        'tipo_evento': 'Tipo de evento',
                        'fecha_inicio': 'Fecha de inicio',
                        'fecha_fin': 'Fecha de finalización',
                        'fecha_limite_inscripcion': 'Fecha límite de inscripción',
                        'estado': 'Estado',
                        'capacidad_maxima': 'Capacidad máxima',
                        'patrocinadores': 'Patrocinadores',
                        'invitados': 'Invitados'
                    };
                    const campoTraducido = camposTraducidos[campo] || campo;
                    return `${campoTraducido}: ${mensaje}`;
                });
                mensajeError = erroresArray.join('\n');
            }
            
            mostrarNotificacion("error", "Error al actualizar evento", mensajeError);
            console.error("Error completo:", data);
            return;
        }

        // Mostrar notificación de éxito
        mostrarNotificacion("success", "¡Éxito!", "Evento actualizado correctamente");
        
        // Redirigir después de 2 segundos
        setTimeout(() => {
            window.location.href = "/ong/eventos";
        }, 2000);

    } catch (err) {
        console.error(err);
        mostrarNotificacion("error", "Error de servidor", "No se pudo conectar con el servidor");
    }
});

// ===============================
// 🔔 FUNCIÓN DE NOTIFICACIONES (MISMA QUE CREATE)
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
                    white-space: pre-line;
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

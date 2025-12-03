// Evitar redeclaración de API_BASE_URL
if (typeof window.API_BASE_URL === 'undefined') {
    window.API_BASE_URL = "http://10.114.190.52:8000";
}

// PUBLIC_BASE_URL se define desde las vistas Blade usando la variable de entorno
// Si no está definida, usar un valor por defecto
if (typeof window.PUBLIC_BASE_URL === 'undefined') {
    window.PUBLIC_BASE_URL = "http://10.114.190.52:8000"; // Valor por defecto
}

// Alias para compatibilidad con código existente
if (typeof API_BASE_URL === 'undefined') {
    const API_BASE_URL = window.API_BASE_URL;
}

console.log("🌐 API_BASE_URL:", window.API_BASE_URL);
console.log("🌐 PUBLIC_BASE_URL:", window.PUBLIC_BASE_URL);

// Función helper para obtener la URL pública (para QR y enlaces compartidos)
function getPublicUrl(path = '') {
    const baseUrl = window.PUBLIC_BASE_URL || "http://10.114.190.52:8000";
    return `${baseUrl}${path.startsWith('/') ? path : '/' + path}`;
}

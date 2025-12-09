// ===========================
// Alternar visibilidad
// ===========================
document.getElementById("togglePassword").addEventListener("click", () => {
  const input = document.getElementById("password");
  const eye = document.getElementById("eyeIcon");
  const show = input.type === "password";
  input.type = show ? "text" : "password";
  eye.className = show ? "fas fa-eye-slash" : "fas fa-eye";
});

// ===========================
// LOGIN
// ===========================
document.getElementById("loginForm").addEventListener("submit", async (e) => {
  e.preventDefault();

  const correo_electronico = document.getElementById("email").value.trim();
  const contrasena = document.getElementById("password").value.trim();
  const result = document.getElementById("result");

  result.innerHTML = "Verificando...";

  // Asegurar que API_BASE_URL esté definido
  const apiUrl = window.API_BASE_URL || API_BASE_URL || "http://10.26.0.215:8000";
  
  // Debug: Verificar qué URL se está usando
  console.log("🔍 Intentando login con URL:", apiUrl);
  console.log("🔍 window.API_BASE_URL:", window.API_BASE_URL);
  console.log("🔍 API_BASE_URL (global):", typeof API_BASE_URL !== 'undefined' ? API_BASE_URL : 'undefined');
  
  try {
    const res = await fetch(`${apiUrl}/api/auth/login`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ correo_electronico, contrasena }),
    });

    // Verificar si la respuesta es válida antes de parsear JSON
    if (!res.ok) {
      if (res.status === 0 || res.statusText === '') {
        throw new Error('No se pudo conectar con el servidor. Verifica que el servidor esté ejecutándose.');
      }
    }

    const data = await res.json();
    console.log("LOGIN RESPONSE:", data);

    if (!res.ok || !data.success) {
      result.innerHTML = `<span style="color: #ff6b6b;">${data.error || 'Error al iniciar sesión'}</span>`;
      return;
    }

    localStorage.setItem("token", data.token);
    localStorage.setItem("id_usuario", data.user.id_usuario);
    localStorage.setItem("id_entidad", data.user.id_entidad);
    localStorage.setItem("tipo_usuario", data.user.tipo_usuario);
    localStorage.setItem("nombre_usuario", data.user.nombre_usuario ?? "");
    localStorage.setItem("usuario", JSON.stringify(data.user));

    // REDIRECCIÓN
    let ruta = "/";
    if (data.user.tipo_usuario === "ONG") ruta = "/home-ong";
    if (data.user.tipo_usuario === "Empresa") ruta = "/home-empresa";
    if (data.user.tipo_usuario === "Integrante externo") ruta = "/home-externo";

    window.location.href = ruta;
  } catch (error) {
    console.error("Error en login:", error);
    result.innerHTML = `<span style="color: #ff6b6b;">${error.message || 'Error de conexión. Verifica que el servidor esté ejecutándose.'}</span>`;
  }
});

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

  const res = await fetch(`${API_BASE_URL}/api/auth/login`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ correo_electronico, contrasena }),
  });

  const data = await res.json();
  console.log("LOGIN RESPONSE:", data);

  if (!res.ok || !data.success) {
    result.innerHTML = data.error;
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
  if (data.user.tipo_usuario === "ONG") ruta = "/ong/eventos";
  if (data.user.tipo_usuario === "Empresa") ruta = "/home-empresa";
  if (data.user.tipo_usuario === "Integrante externo") ruta = "/home-externo";

  window.location.href = ruta;
});

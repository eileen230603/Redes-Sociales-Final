if (typeof API_BASE_URL === 'undefined') {
  alert('⚠️ config.js no está cargado.');
}

document.getElementById('formRegister').addEventListener('submit', async (e) => {
  e.preventDefault();
  const msg = document.getElementById('msg');
  msg.textContent = 'Registrando...';
  msg.className = 'text-center text-blue-700 font-medium';

  const data = {
    tipo_usuario: 'Integrante externo',
    nombre_usuario: e.target.nombre_usuario.value.trim(),
    correo: e.target.correo.value.trim(),
    contrasena: e.target.contrasena.value,
    nombres: e.target.nombres.value.trim(),
    apellidos: e.target.apellidos.value.trim(),
    fecha_nacimiento: e.target.fecha_nacimiento.value,
    telefono: e.target.telefono.value.trim(),
    descripcion: e.target.descripcion.value.trim()
  };

  try {
    const res = await fetch(`${API_BASE_URL}/api/auth/register`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    });

    const text = await res.text();
    const result = JSON.parse(text);

    if (res.ok && result.success) {
      msg.textContent = '✅ Registro exitoso. Redirigiendo...';
      msg.className = 'text-center text-green-600 font-semibold';
      setTimeout(() => window.location.href = '/login', 1500);
    } else {
      throw new Error(result.error || 'Error al registrar usuario');
    }

  } catch (err) {
    msg.textContent = `❌ ${err.message}`;
    msg.className = 'text-center text-red-600 font-semibold';
  }
});

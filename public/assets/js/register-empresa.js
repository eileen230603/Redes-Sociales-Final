if (typeof API_BASE_URL === 'undefined') {
  alert('⚠️ config.js no está cargado.');
}

document.getElementById('formEmpresa').addEventListener('submit', async (e) => {
  e.preventDefault();
  const msg = document.getElementById('msg');
  msg.textContent = 'Registrando empresa...';
  msg.className = 'text-center text-blue-700 font-medium';

  const data = {
    tipo_usuario: 'Empresa',
    nombre_usuario: e.target.nombre_usuario.value.trim(),
    correo: e.target.correo.value.trim(),
    contrasena: e.target.contrasena.value,
    nombre_empresa: e.target.nombre_empresa.value.trim(),
    NIT: e.target.NIT.value.trim(),
    telefono: e.target.telefono.value.trim(),
    direccion: e.target.direccion.value.trim(),
    sitio_web: e.target.sitio_web.value.trim(),
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
      msg.textContent = '✅ Empresa registrada con éxito. Redirigiendo...';
      msg.className = 'text-center text-green-600 font-semibold';
      setTimeout(() => window.location.href = '/login', 1500);
    } else {
      throw new Error(result.error || 'Error al registrar empresa');
    }

  } catch (err) {
    msg.textContent = `❌ ${err.message}`;
    msg.className = 'text-center text-red-600 font-semibold';
  }
});

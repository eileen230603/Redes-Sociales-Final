if (typeof API_BASE_URL === 'undefined') {
  alert('⚠️ config.js no cargado.');
}

document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('formRegister');
  const msg = document.getElementById('msg');

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    msg.textContent = '⏳ Registrando...';
    msg.className = '';
    msg.style.background = 'rgba(255, 255, 255, 0.1)';
    msg.style.color = 'rgba(255, 255, 255, 0.9)';

    const payload = {
      tipo_usuario: 'Integrante externo',
      nombre_usuario: form.nombre_usuario.value.trim(),
      correo_electronico: form.correo_electronico.value.trim(),
      contrasena: form.contrasena.value,
      nombres: form.nombres.value.trim(),
      apellidos: form.apellidos.value.trim(),
      fecha_nacimiento: form.fecha_nacimiento.value || null,
      telefono: form.telefono.value.trim() || null,
      direccion: form.direccion.value.trim() || null,
      descripcion: form.descripcion.value.trim() || null
    };

    try {
      const res = await fetch(`${API_BASE_URL}/api/auth/register`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });

      const text = await res.text();
      let data;
      try {
        data = JSON.parse(text);
      } catch {
        throw new Error('Error inesperado en el servidor (no devolvió JSON válido).');
      }

      if (!res.ok || !data.success) {
        throw new Error(data.error || 'Error al registrar usuario externo');
      }

      msg.textContent = '✅ Usuario registrado con éxito. Redirigiendo...';
      msg.className = 'success';
      setTimeout(() => (window.location.href = '/login'), 2000);

    } catch (err) {
      console.error(err);
      msg.textContent = `❌ ${err.message}`;
      msg.className = 'error';
    }
  });
});

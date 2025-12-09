if (typeof API_BASE_URL === 'undefined') {
  alert('⚠️ config.js no cargado.');
}

document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('formRegister');
  const msg = document.getElementById('msg');

  // Obtener eventoId o megaEventoId de la URL si existe
  const urlParams = new URLSearchParams(window.location.search);
  const eventoId = urlParams.get('eventoId');
  const megaEventoId = urlParams.get('megaEventoId');

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

      // Si hay eventoId o megaEventoId, inscribir automáticamente después del registro
      if ((eventoId || megaEventoId) && data.token) {
        // Guardar token primero
        localStorage.setItem('token', data.token);
        localStorage.setItem('id_usuario', data.user.id_usuario);
        localStorage.setItem('tipo_usuario', data.user.tipo_usuario);
        
        if (eventoId) {
          msg.textContent = '✅ Usuario registrado. Inscribiéndote al evento...';
          
          try {
            const resInscripcion = await fetch(`${API_BASE_URL}/api/participaciones/inscribir`, {
              method: 'POST',
              headers: {
                'Authorization': `Bearer ${data.token}`,
                'Content-Type': 'application/json'
              },
              body: JSON.stringify({ evento_id: parseInt(eventoId) })
            });

            const dataInscripcion = await resInscripcion.json();
            
            if (dataInscripcion.success) {
              msg.textContent = '✅ ¡Registro e inscripción exitosos! Redirigiendo...';
              msg.className = 'success';
              setTimeout(() => {
                window.location.href = `/externo/eventos/${eventoId}`;
              }, 2000);
            } else {
              msg.textContent = '✅ Usuario registrado. Error al inscribirte al evento. Redirigiendo...';
              msg.className = 'success';
              setTimeout(() => {
                window.location.href = '/login';
              }, 2000);
            }
          } catch (errInscripcion) {
            console.error('Error al inscribir:', errInscripcion);
            msg.textContent = '✅ Usuario registrado. Error al inscribirte al evento. Redirigiendo...';
            msg.className = 'success';
            setTimeout(() => {
              window.location.href = '/login';
            }, 2000);
          }
        } else if (megaEventoId) {
          msg.textContent = '✅ Usuario registrado. Inscribiéndote al mega evento...';
          
          try {
            const resInscripcion = await fetch(`${API_BASE_URL}/api/mega-eventos/${megaEventoId}/participar`, {
              method: 'POST',
              headers: {
                'Authorization': `Bearer ${data.token}`,
                'Content-Type': 'application/json'
              }
            });

            const dataInscripcion = await resInscripcion.json();
            
            if (dataInscripcion.success) {
              msg.textContent = '✅ ¡Registro e inscripción exitosos! Redirigiendo...';
              msg.className = 'success';
              setTimeout(() => {
                window.location.href = `/externo/mega-eventos/${megaEventoId}/detalle`;
              }, 2000);
            } else {
              msg.textContent = '✅ Usuario registrado. Error al inscribirte al mega evento. Redirigiendo...';
              msg.className = 'success';
              setTimeout(() => {
                window.location.href = '/login';
              }, 2000);
            }
          } catch (errInscripcion) {
            console.error('Error al inscribir:', errInscripcion);
            msg.textContent = '✅ Usuario registrado. Error al inscribirte al mega evento. Redirigiendo...';
            msg.className = 'success';
            setTimeout(() => {
              window.location.href = '/login';
            }, 2000);
          }
        }
      } else {
        // No hay eventoId, redirigir normalmente
      msg.textContent = '✅ Usuario registrado con éxito. Redirigiendo...';
      msg.className = 'success';
      setTimeout(() => (window.location.href = '/login'), 2000);
      }

    } catch (err) {
      console.error(err);
      msg.textContent = `❌ ${err.message}`;
      msg.className = 'error';
    }
  });
});

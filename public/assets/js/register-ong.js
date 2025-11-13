// public/assets/js/register-ong.js

if (typeof API_BASE_URL === 'undefined') {
  alert('⚠️ config.js no cargado.');
}

function initMap() {
  const defaultCenter = [-16.5, -68.13]; // Coordenadas iniciales
  const map = L.map('map').setView(defaultCenter, 13);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
  }).addTo(map);

  let marker;
  map.on('click', async (e) => {
    const { lat, lng } = e.latlng;
    if (marker) marker.setLatLng(e.latlng);
    else marker = L.marker(e.latlng, { draggable: true })
      .addTo(map)
      .on('dragend', ev => onMapClick(ev.target.getLatLng()));
    await onMapClick({ lat, lng });
  });
}

async function onMapClick({ lat, lng }) {
  try {
    const url = `https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`;
    const res = await fetch(url, { headers: { Accept: 'application/json' } });
    const j = await res.json();
    if (j.display_name) {
      document.getElementById('direccion').value = j.display_name;
    }
  } catch {
    console.warn('No se pudo obtener la dirección de OSM');
  }
}

document.addEventListener('DOMContentLoaded', () => {
  initMap();

  const form = document.getElementById('formOng');
  const msg = document.getElementById('msg');

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    msg.textContent = 'Registrando...';
    msg.className = 'text-sm text-blue-700';

    const payload = {
      tipo_usuario: 'ONG',
      nombre_usuario: form.nombre_usuario.value.trim(),
      correo: form.correo.value.trim(),
      contrasena: form.contrasena.value,
      nombre_ong: form.nombre_ong.value.trim(),
      NIT: form.NIT.value.trim(),
      telefono: form.telefono.value.trim(),
      direccion: form.direccion.value.trim(),
      sitio_web: form.sitio_web.value.trim() || null,
      descripcion: form.descripcion.value.trim() || null
    };

    try {
      const res = await fetch(`${API_BASE_URL}/api/auth/register`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });

      // 🧠 Importante: leer como texto y luego intentar JSON
      const text = await res.text();
      let data;
      try {
        data = JSON.parse(text);
      } catch {
        throw new Error('Error inesperado en el servidor (no devolvió JSON válido).');
      }

      if (!res.ok || !data.success) {
        throw new Error(data.error || 'Error al registrar la ONG');
      }

      msg.textContent = '✅ ONG registrada con éxito';
      msg.className = 'text-sm text-green-700 font-medium';
      setTimeout(() => (window.location.href = '/login'), 1500);
    } catch (err) {
      console.error(err);
      msg.textContent = `❌ ${err.message}`;
      msg.className = 'text-sm text-red-600 font-medium';
    }
  });
});

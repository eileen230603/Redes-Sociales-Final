<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>UNI2 • Registro ONG</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<body class="bg-gradient-to-br from-green-600 via-teal-500 to-cyan-400 min-h-screen flex items-center justify-center p-4">

  <main class="w-full max-w-3xl bg-white rounded-3xl shadow-2xl p-8">
    <h1 class="text-3xl font-bold text-center text-green-700 mb-6">
      Registro de ONG
    </h1>

    <form id="formOng" class="grid grid-cols-2 gap-4">
      <div class="col-span-2">
        <label class="block text-sm font-medium">Nombre de usuario</label>
        <input name="nombre_usuario" type="text" required class="w-full border rounded-lg p-2">
      </div>

      <div class="col-span-2">
        <label class="block text-sm font-medium">Correo</label>
        <input name="correo" type="email" required class="w-full border rounded-lg p-2">
      </div>

      <div class="col-span-2">
        <label class="block text-sm font-medium">Contraseña</label>
        <input name="contrasena" type="password" required class="w-full border rounded-lg p-2">
      </div>

      <div>
        <label class="block text-sm font-medium">Nombre de la ONG</label>
        <input name="nombre_ong" type="text" required class="w-full border rounded-lg p-2">
      </div>
      <div>
        <label class="block text-sm font-medium">NIT</label>
        <input name="NIT" type="text" class="w-full border rounded-lg p-2">
      </div>

      <div>
        <label class="block text-sm font-medium">Teléfono</label>
        <input name="telefono" type="text" class="w-full border rounded-lg p-2">
      </div>
      <div>
        <label class="block text-sm font-medium">Sitio web</label>
        <input name="sitio_web" type="text" class="w-full border rounded-lg p-2">
      </div>

      <div class="col-span-2">
        <label class="block text-sm font-medium">Dirección (clic en el mapa)</label>
        <input id="direccion" name="direccion" type="text" class="w-full border rounded-lg p-2">
        <div id="map" class="w-full h-60 rounded-lg mt-2 border"></div>
      </div>

      <div class="col-span-2">
        <label class="block text-sm font-medium">Descripción</label>
        <textarea name="descripcion" rows="2" class="w-full border rounded-lg p-2"></textarea>
      </div>

      <div id="msg" class="col-span-2 text-center font-medium mt-2"></div>

      <div class="col-span-2 text-center mt-4">
        <button type="submit" class="px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition">
          <i class="fa fa-hand-holding-heart mr-2"></i>Registrar ONG
        </button>
      </div>
    </form>

    <p class="text-center mt-4">
      <a href="/login" class="text-sm text-green-600 hover:underline">¿Ya tienes cuenta? Inicia sesión</a>
    </p>
  </main>

  <script src="/assets/js/config.js"></script>
  <script src="/assets/js/register-ong.js"></script>
</body>
</html>

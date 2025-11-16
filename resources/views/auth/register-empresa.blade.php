<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>UNI2 • Registro Empresa</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-600 via-cyan-500 to-green-400 min-h-screen flex items-center justify-center p-4">

  <main class="w-full max-w-3xl bg-white rounded-3xl shadow-2xl p-8">
    <h1 class="text-3xl font-bold text-center text-blue-700 mb-6">
      Registro de Empresa
    </h1>

    <form id="formEmpresa" class="grid grid-cols-2 gap-4">
      <div class="col-span-2">
        <label class="block text-sm font-medium">Nombre de usuario</label>
        <input name="nombre_usuario" type="text" required class="w-full border rounded-lg p-2">
      </div>

      <div class="col-span-2">
        <label class="block text-sm font-medium">Correo</label>
        <input name="correo_electronico" type="email" required class="w-full border rounded-lg p-2">
      </div>

      <div class="col-span-2">
        <label class="block text-sm font-medium">Contraseña</label>
        <input name="contrasena" type="password" required class="w-full border rounded-lg p-2">
      </div>

      <div>
        <label class="block text-sm font-medium">Nombre de la Empresa</label>
        <input name="nombre_empresa" type="text" required class="w-full border rounded-lg p-2">
      </div>
      <div>
        <label class="block text-sm font-medium">Razón Social</label>
        <input name="razon_social" type="text" class="w-full border rounded-lg p-2">
      </div>

      <div>
        <label class="block text-sm font-medium">NIT</label>
        <input name="NIT" type="text" required class="w-full border rounded-lg p-2">
      </div>
      <div>
        <label class="block text-sm font-medium">Teléfono</label>
        <input name="telefono" type="text" class="w-full border rounded-lg p-2">
      </div>

      <div class="col-span-2">
        <label class="block text-sm font-medium">Dirección</label>
        <input name="direccion" type="text" class="w-full border rounded-lg p-2">
      </div>

      <div>
        <label class="block text-sm font-medium">Sitio Web</label>
        <input name="sitio_web" type="text" class="w-full border rounded-lg p-2">
      </div>
      <div>
        <label class="block text-sm font-medium">Descripción</label>
        <textarea name="descripcion" rows="2" class="w-full border rounded-lg p-2"></textarea>
      </div>

      <div id="msg" class="col-span-2 text-center font-medium mt-2"></div>

      <div class="col-span-2 text-center mt-4">
        <button type="submit" class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
          <i class="fa fa-building mr-2"></i>Registrar Empresa
        </button>
      </div>
    </form>

    <p class="text-center mt-4">
      <a href="/login" class="text-sm text-blue-600 hover:underline">¿Ya tienes cuenta? Inicia sesión</a>
    </p>
  </main>

  <script src="/assets/js/config.js"></script>
  <script src="/assets/js/register-empresa.js"></script>
</body>
</html>

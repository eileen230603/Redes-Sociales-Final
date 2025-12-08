<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>UNI2 • Iniciar sesión</title>

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            'brand-profundo': '#3883D3',
            'brand-cyan': '#21BFC4',
            'brand-verde': '#36C974',
            'brand-claro': '#F9F9F9'
          }
        }
      }
    }
  </script>

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="font-sans min-h-screen flex items-center justify-center bg-gradient-to-br from-brand-profundo via-brand-cyan to-brand-verde p-4">
  
  <!-- Elementos de fondo -->
  <div class="fixed inset-0 overflow-hidden pointer-events-none">
    <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
    <div class="absolute bottom-1/4 right-1/4 w-80 h-80 bg-white/5 rounded-full blur-3xl"></div>
  </div>

  <!-- Contenedor principal -->
  <main class="relative w-full max-w-md mx-4 bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 overflow-hidden">
    
    <!-- Header -->
    <div class="bg-gradient-to-r from-brand-profundo to-brand-cyan p-8 text-center relative">
      <div class="absolute inset-0 bg-white/10"></div>
      <div class="inline-flex items-center justify-center w-28 h-28 bg-white rounded-2xl shadow-lg">
        <img src="{{ asset('assets/img/UNI2.png') }}" class="h-20 w-auto object-contain" />
      </div>
      <h1 class="relative z-10 text-3xl font-bold text-white mb-2">Bienvenido</h1>
      <p class="relative z-10 text-white text-lg font-semibold">Inicia sesión en tu cuenta</p>
    </div>

    <!-- Formulario -->
    <div class="p-8">
      <form id="loginForm" class="space-y-6" novalidate>
        
        <!-- Correo -->
        <div class="space-y-2">
          <label for="email" class="flex items-center text-sm font-medium text-gray-700">
            <i class="fas fa-envelope w-4 mr-2 text-brand-profundo"></i> Correo electrónico
          </label>
          <div class="relative">
            <input id="email" type="email" placeholder="tu@ejemplo.com" required
              class="w-full px-4 py-3 bg-brand-claro/50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-profundo pl-11" />
            <i class="fas fa-user absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
          </div>
        </div>

        <!-- Contraseña -->
        <div class="space-y-2">
          <label for="password" class="flex items-center text-sm font-medium text-gray-700">
            <i class="fas fa-lock w-4 mr-2 text-brand-profundo"></i> Contraseña
          </label>
          <div class="relative">
            <input id="password" type="password" minlength="6" placeholder="••••••••" required
              class="w-full px-4 py-3 bg-brand-claro/50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-profundo pl-11 pr-12" />
            <i class="fas fa-key absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            <button type="button" id="togglePassword" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-brand-profundo">
              <i class="fas fa-eye" id="eyeIcon"></i>
            </button>
          </div>
        </div>

        <!-- Botón -->
        <button type="submit"
          class="w-full py-4 rounded-xl bg-gradient-to-r from-brand-profundo via-brand-cyan to-brand-verde text-white font-semibold shadow-lg hover:scale-[1.02] transition-transform">
          <i class="fas fa-sign-in-alt mr-2"></i> Iniciar Sesión
        </button>
      </form>

      <!-- Resultado -->
      <div id="result" class="mt-4 text-center text-sm font-medium"></div>

      <!-- Separador -->
      <div class="relative my-8">
        <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
        <div class="relative flex justify-center">
          <span class="bg-white px-4 text-sm text-gray-500">¿No tienes una cuenta?</span>
        </div>
      </div>

      <!-- Botones de registro -->
      <div class="space-y-4">
        <p class="text-center text-sm text-gray-600 mb-4">Regístrate como:</p>
        <div class="grid grid-cols-1 gap-3">

          <!-- Usuario Individual -->
          <a href="{{ url('/register-externo') }}"
             class="flex items-center justify-center px-4 py-3 bg-gradient-to-r from-brand-profundo/10 to-brand-profundo/5 text-brand-profundo rounded-xl font-medium hover:from-brand-profundo hover:to-brand-profundo hover:text-white transition-all duration-300 transform hover:scale-[1.02] border border-brand-profundo/20">
            <i class="fas fa-user mr-2"></i> Usuario Individual
          </a>

          <!-- ONG -->
          <a href="{{ url('/register-ong') }}"
             class="flex items-center justify-center px-4 py-3 bg-gradient-to-r from-brand-cyan/10 to-brand-cyan/5 text-brand-cyan rounded-xl font-medium hover:from-brand-cyan hover:to-brand-cyan hover:text-white transition-all duration-300 transform hover:scale-[1.02] border border-brand-cyan/20">
            <i class="fas fa-hands-helping mr-2"></i> Organización (ONG)
          </a>

          <!-- Empresa -->
          <a href="{{ url('/register-empresa') }}"
             class="flex items-center justify-center px-4 py-3 bg-gradient-to-r from-brand-verde/10 to-brand-verde/5 text-brand-verde rounded-xl font-medium hover:from-brand-verde hover:to-brand-verde hover:text-white transition-all duration-300 transform hover:scale-[1.02] border border-brand-verde/20">
            <i class="fas fa-building mr-2"></i> Empresa
          </a>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <div class="bg-gradient-to-r from-brand-claro/50 to-white/30 px-8 py-4 text-center border-t border-gray-100">
      <p class="text-xs text-gray-500">© 2025 UNI2. Conectando comunidades, transformando vidas.</p>
    </div>
  </main>

  <!-- Scripts -->
  <script src="{{ asset('assets/js/config.js') }}"></script>
  <script src="{{ asset('assets/js/login.js') }}"></script>
</body>
</html>

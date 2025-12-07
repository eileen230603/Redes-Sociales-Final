{{-- resources/views/layouts/adminlte-externo.blade.php --}}
{{-- Layout exclusivo para usuarios externos (Integrantes Externos) --}}
@extends('adminlte::page')

@section('title', 'UNI2 • Panel del Integrante Externo')

@push('js')
    {{-- Lucide icons para panel Externo (opcional) --}}
    <script type="module">
        (async () => {
            try {
                const { createIcons, icons } = await import("https://unpkg.com/lucide@latest/dist/esm/lucide.js").catch(() => null);
                if (!icons || !createIcons) return;

                const faToLucide = {
                    'fa-home': 'home',
                    'fa-calendar-alt': 'calendar-range',
                    'fa-calendar-check': 'calendar-check',
                    'fa-star': 'star',
                    'fa-chart-bar': 'bar-chart-3',
                    'fa-user-circle': 'user-round',
                    'fa-globe': 'globe-2',
                    'fa-sign-out-alt': 'log-out',
                };

                window.addEventListener('DOMContentLoaded', () => {
                    try {
                        document.querySelectorAll('i[class*="fa-"]').forEach(el => {
                            const classes = el.className.split(/\s+/);
                            const faClass = classes.find(c => c.startsWith('fa-'));
                            if (!faClass) return;

                            const lucideName = faToLucide[faClass];
                            if (!lucideName || !icons[lucideName]) return;

                            el.setAttribute('data-lucide', lucideName);
                            el.className = classes.filter(c => !c.startsWith('fa')).join(' ').trim();
                        });

                        createIcons({ icons });
                    } catch (e) {
                        // Silenciar error, usar Font Awesome como fallback
                    }
                });
            } catch (e) {
                // Silenciar error, usar Font Awesome como fallback
            }
        })();
    </script>
@endpush

@php
    // Configuración específica para usuarios externos
    config(['adminlte.layout_topnav' => null]);
    config(['adminlte.layout_fixed_sidebar' => true]);
    config(['adminlte.layout_fixed_navbar' => true]);
    // Configurar el menú específico para usuarios externos
    config(['adminlte.menu' => [
        ['header' => 'NAVEGACIÓN PRINCIPAL'],
        [
            'text' => 'Inicio',
            'url'  => '/home-externo',
            'icon' => 'fas fa-home',
        ],
        [
            'text' => 'Eventos',
            'url'  => '/externo/eventos',
            'icon' => 'fas fa-calendar-alt',
        ],
        [
            'text' => 'Mega Eventos',
            'url'  => '/externo/mega-eventos',
            'icon' => 'fas fa-star',
        ],
        [
            'text' => 'Mis Participaciones',
            'url'  => '/externo/mis-participaciones',
            'icon' => 'fas fa-calendar-check',
        ],
        [
            'text' => 'Reportes',
            'url'  => '/externo/reportes',
            'icon' => 'fas fa-chart-bar',
        ],
        [
            'text' => 'Mi Perfil',
            'url'  => '/perfil/externo',
            'icon' => 'fas fa-user-circle',
        ],
        ['header' => 'OTRAS OPCIONES'],
        [
            'text' => 'Ir a página pública',
            'url'  => '/home-publica',
            'icon' => 'fas fa-globe',
        ],
        [
            'text' => 'Cerrar sesión',
            'url'  => '#',
            'icon' => 'far fa-sign-out-alt',
            'label_color' => 'danger',
            'attributes' => [
                'onclick' => 'event.preventDefault(); event.stopPropagation(); cerrarSesion(event); return false;',
                'class' => 'logout-link'
            ],
        ],
    ]]);
@endphp

@section('content_header')
<div class="d-flex align-items-center justify-content-between" style="margin-bottom: 0.5rem;">
    <h1 class="mb-0" style="color: #0C2B44; font-weight: 700;">
        <i class="far fa-user-friends mr-2" style="color: #00A36C;"></i>
        @yield('page_title', 'Panel del Integrante Externo')
    </h1>
</div>
@stop

@section('content')
<div class="container-fluid">
    @yield('content_body')
</div>
@stop

{{-- El sidebar se renderiza automáticamente desde la configuración del menú --}}

{{-- NAVBAR SUPERIOR --}}
@push('adminlte_topnav')
    {{-- Avatar y nombre del usuario externo - PRIMERO para que aparezca a la derecha --}}
    <li class="nav-item d-flex align-items-center ml-auto" style="order: 999; margin-left: auto !important; margin-right: 0 !important;">
        <div class="d-flex align-items-center pl-3 pr-3"
             style="background: #ffffff; border-radius: 999px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); padding: 4px 16px; margin-right: 0 !important; min-width: 220px;">
            @php
                $user = Auth::user();
                $integranteExterno = optional($user)->integranteExterno;
                
                // Nombre: nombres + apellidos del integrante externo > nombre_usuario > name > 'Usuario'
                $nombreUsuario = 'Usuario';
                if ($integranteExterno) {
                    $nombres = trim($integranteExterno->nombres ?? '');
                    $apellidos = trim($integranteExterno->apellidos ?? '');
                    $nombreUsuario = trim("{$nombres} {$apellidos}") ?: ($user->nombre_usuario ?? ($user->name ?? 'Usuario'));
                } else {
                    $nombreUsuario = $user->nombre_usuario ?? ($user->name ?? 'Usuario');
                }
                
                $inicial = mb_substr(trim($nombreUsuario), 0, 1, 'UTF-8');
                
                // Prioridad de avatar: foto del integrante externo > foto del usuario > null
                $foto = null;
                if ($integranteExterno) {
                    try {
                        $foto = $integranteExterno->foto_perfil_url ?? null;
                    } catch (\Exception $e) {
                        // Si falla, intentar con el usuario
                    }
                }
                if (!$foto && $user) {
                    try {
                        $foto = $user->foto_perfil_url ?? null;
                    } catch (\Exception $e) {
                        $foto = null;
                    }
                }
            @endphp
            
            {{-- Avatar redondo --}}
            <div class="mr-3" id="avatarContainerExterno"
                 style="width: 40px; height: 40px; border-radius: 50%; overflow: hidden; background: #e9f5ff; flex-shrink: 0; position: relative; display: flex; align-items: center; justify-content: center;">
                <img id="headerAvatarExterno"
                     src="{{ $foto ?? '' }}"
                     alt="Foto perfil"
                     style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%; display: {{ $foto ? 'block' : 'none' }}; margin: 0; padding: 0;">
                <span id="headerAvatarInicialExterno"
                      style="font-size: 1rem; display: {{ $foto ? 'none' : 'flex' }}; align-items: center; justify-content: center; width: 40px; height: 40px; margin: 0; padding: 0; border-radius: 50%;">{{ $inicial }}</span>
            </div>
            
            {{-- Nombre + etiqueta --}}
            <div class="d-flex flex-column mr-3" style="max-width: 200px; min-width: 120px; flex-grow: 1;">
                <span id="headerNombreExterno"
                      style="font-size: 0.9rem; color: #2c3e50; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 1.2;">
                    {{ $nombreUsuario }}
                </span>
                @if($integranteExterno)
                    <small style="font-size: 0.75rem; color: #8c9aa8; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 1.2;">
                        Integrante Externo
                    </small>
                @endif
            </div>
            
            {{-- Dropdown de opciones --}}
            <div class="dropdown" style="flex-shrink: 0;">
                <a class="text-muted" href="#" role="button" id="dropdownPerfilExterno"
                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                   style="font-size: 0.75rem; text-decoration: none; padding: 0.25rem;">
                    <i class="fas fa-chevron-down"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownPerfilExterno">
                    <a class="dropdown-item" href="/perfil/externo">
                        <i class="far fa-user mr-2"></i> Mi perfil
                    </a>
                    <a class="dropdown-item" href="/home-publica">
                        <i class="far fa-globe mr-2"></i> Ir a página pública
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="#" onclick="cerrarSesion(event)">
                        <i class="far fa-sign-out-alt mr-2"></i> Cerrar sesión
        </a>
                </div>
            </div>
        </div>
    </li>
@endpush

@push('css')
<link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
<style>
    /* ============================================
       NUEVA PALETA DE COLORES - AZUL MARINO Y VERDE ESMERALDA
       ============================================ */
    :root {
        --brand-primario: #0C2B44;   /* Azul Marino */
        --brand-acento: #00A36C;     /* Verde Esmeralda */
        --brand-blanco: #FFFFFF;     /* Blanco Puro */
        --brand-gris-oscuro: #333333;/* Gris Carbón */
        --brand-gris-suave: #F5F5F5; /* Gris Suave */
    }

    /* Sidebar - Azul Marino Oscuro con Nueva Paleta */
    .main-sidebar,
    .sidebar-dark-primary {
        background-color: #0C2B44 !important;
        border-right: 1px solid rgba(255, 255, 255, 0.1) !important;
    }
    
    /* Brand/Logo del Sidebar */
    .sidebar-dark-primary .brand-link,
    .main-sidebar .brand-link,
    .brand-link.bg-primary {
        background-color: #0C2B44 !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
        padding: 1rem 1.25rem !important;
    }
    
    .sidebar-dark-primary .brand-text,
    .main-sidebar .brand-text,
    .brand-text {
        color: white !important;
        font-weight: 700 !important;
        font-size: 1.5rem !important;
    }
    
    .sidebar-dark-primary .brand-link:hover,
    .main-sidebar .brand-link:hover,
    .brand-link.bg-primary:hover {
        background-color: #0a2338 !important;
    }
    
    /* Logo Image */
    .sidebar-dark-primary .brand-image,
    .main-sidebar .brand-image,
    .brand-image {
        opacity: 1 !important;
        max-width: 50px !important;
        max-height: 50px !important;
        object-fit: contain !important;
    }
    
    /* Enlaces del Sidebar */
    .sidebar-dark-primary .nav-sidebar .nav-link,
    .main-sidebar .nav-sidebar .nav-link {
        color: rgba(255, 255, 255, 0.85) !important;
        border-radius: 8px !important;
        margin: 0.25rem 0.5rem !important;
        padding: 0.75rem 1rem !important;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
        position: relative !important;
        overflow: hidden !important;
    }
    
    /* Enlaces Activos */
    .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link.active,
    .sidebar-dark-primary .nav-sidebar .nav-link.active,
    .main-sidebar .nav-sidebar > .nav-item > .nav-link.active,
    .main-sidebar .nav-sidebar .nav-link.active {
        background-color: #00A36C !important;
        color: white !important;
        font-weight: 600 !important;
        box-shadow: 0 2px 8px rgba(0, 163, 108, 0.3) !important;
        transform: translateX(4px) !important;
        border-right: 3px solid rgba(255, 255, 255, 0.3) !important;
    }
    
    /* Hover de Enlaces */
    .sidebar-dark-primary .nav-sidebar .nav-link:hover:not(.active),
    .main-sidebar .nav-sidebar .nav-link:hover:not(.active) {
        background-color: rgba(0, 163, 108, 0.2) !important;
        color: white !important;
        transform: translateX(4px) scale(1.02) !important;
        box-shadow: 0 2px 6px rgba(0, 163, 108, 0.2) !important;
    }
    
    /* Iconos del Sidebar */
    .sidebar-dark-primary .nav-sidebar .nav-link .nav-icon,
    .main-sidebar .nav-sidebar .nav-link .nav-icon {
        color: rgba(255, 255, 255, 0.7) !important;
        margin-right: 0.75rem !important;
        transition: all 0.3s ease !important;
    }
    
    .sidebar-dark-primary .nav-sidebar .nav-link.active .nav-icon,
    .main-sidebar .nav-sidebar .nav-link.active .nav-icon {
        color: white !important;
    }
    
    /* Header del Sidebar */
    .sidebar-dark-primary .nav-header,
    .main-sidebar .nav-header {
        color: rgba(255, 255, 255, 0.6) !important;
        font-weight: 600 !important;
        font-size: 0.75rem !important;
        text-transform: uppercase !important;
        letter-spacing: 1px !important;
        padding: 1rem 1.25rem 0.5rem !important;
        margin-top: 0.5rem !important;
    }
    
    /* Main Header */
    .main-header {
        background-color: #0C2B44 !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
    }
    
    .main-header .navbar-nav .nav-link {
        color: rgba(255, 255, 255, 0.85) !important;
    }
    
    .main-header .navbar-nav .nav-link:hover {
        color: #00A36C !important;
    }
    
    /* Content Header */
    .content-header {
        background-color: transparent !important;
        padding: 1.5rem 1.5rem 1rem !important;
    }
    
    .content-header h1 {
        color: #0C2B44 !important;
        font-weight: 700 !important;
    }
    
    /* Cards */
    .card {
        border-radius: 12px !important;
        border: 1px solid #F5F5F5 !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
    }
    
    .card-header {
        border-radius: 12px 12px 0 0 !important;
        border-bottom: 1px solid #F5F5F5 !important;
    }
    
    /* Buttons */
    .btn-primary {
        background-color: #0C2B44 !important;
        border-color: #0C2B44 !important;
    }
    
    .btn-primary:hover {
        background-color: #0a2338 !important;
        border-color: #0a2338 !important;
    }
    
    .btn-success {
        background-color: #00A36C !important;
        border-color: #00A36C !important;
    }
    
    .btn-success:hover {
        background-color: #008a5a !important;
        border-color: #008a5a !important;
    }
    
    /* Badges */
    .badge-primary {
        background-color: #0C2B44 !important;
    }
    
    .badge-success {
        background-color: #00A36C !important;
    }
    
    /* Estilos para el bloque de avatar y nombre del usuario externo */
    .main-header .navbar-nav .nav-item.ml-auto {
        margin-left: auto !important;
        margin-right: 0 !important;
        display: flex !important;
        align-items: center !important;
    }
    
    /* Asegurar que el bloque esté completamente a la derecha */
    #bloqueUsuarioExterno {
        margin-left: auto !important;
        margin-right: 0 !important;
    }
    
    #bloqueUsuarioExterno > div {
        margin-right: 0 !important;
    }
    
    .main-header .navbar-nav .nav-item .dropdown-toggle,
    .main-header .navbar-nav .nav-item #dropdownPerfilExterno {
        border: none;
        background: transparent;
        padding: 0.25rem 0.5rem;
        color: #6c757d;
    }
    
    .main-header .navbar-nav .nav-item .dropdown-toggle:hover,
    .main-header .navbar-nav .nav-item #dropdownPerfilExterno:hover {
        color: var(--brand-primario) !important;
    }
    
    /* Ocultar el pseudo-elemento ::after de Bootstrap que agrega una flecha automática */
    .main-header .navbar-nav .nav-item #dropdownPerfilExterno::after {
        display: none !important;
        content: none !important;
    }
    
    .main-header .navbar-nav .nav-item .dropdown-toggle::after {
        display: none !important;
        content: none !important;
    }
    
    /* Asegurar que el bloque de usuario sea visible */
    #headerAvatarExterno,
    #headerAvatarInicialExterno,
    #headerNombreExterno {
        display: block !important;
    }
    
    /* Asegurar visibilidad del contenedor del usuario */
    .main-header .navbar-nav .nav-item.ml-auto > div {
        display: flex !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
    
    /* Estilos específicos para el avatar circular perfecto */
    #avatarContainerExterno {
        border-radius: 50% !important;
        overflow: hidden !important;
        width: 40px !important;
        height: 40px !important;
        min-width: 40px !important;
        min-height: 40px !important;
        max-width: 40px !important;
        max-height: 40px !important;
        position: relative !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    
    /* Imagen del avatar - círculo perfecto */
    #headerAvatarExterno {
        width: 40px !important;
        height: 40px !important;
        min-width: 40px !important;
        min-height: 40px !important;
        max-width: 40px !important;
        max-height: 40px !important;
        object-fit: cover !important;
        border-radius: 50% !important;
        margin: 0 !important;
        padding: 0 !important;
        display: block !important;
    }
    
    /* Inicial dentro del círculo - círculo perfecto */
    #headerAvatarInicialExterno {
        width: 40px !important;
        height: 40px !important;
        min-width: 40px !important;
        min-height: 40px !important;
        max-width: 40px !important;
        max-height: 40px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        margin: 0 !important;
        padding: 0 !important;
        border-radius: 50% !important;
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
    }
</style>
@endpush

@push('js')
<script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
<script src="{{ asset('assets/js/config.js') }}"></script>
<script>
// Función para crear e insertar el bloque de avatar y nombre en el navbar
function crearBloqueUsuarioExterno() {
    // Buscar el navbar de diferentes formas
    let navbar = document.querySelector('.main-header .navbar-nav');
    if (!navbar) {
        navbar = document.querySelector('.navbar-nav');
    }
    if (!navbar) {
        navbar = document.querySelector('.main-header nav ul');
    }
    if (!navbar) {
        navbar = document.querySelector('nav.navbar ul');
    }
    
    if (!navbar) {
        console.warn('⚠️ Navbar no encontrado, reintentando...');
        setTimeout(crearBloqueUsuarioExterno, 300);
        return;
    }

    // Verificar si ya existe
    if (document.getElementById('bloqueUsuarioExterno')) {
        console.log('✅ Bloque de usuario externo ya existe');
        return;
    }
    
    console.log('🔍 Creando bloque de usuario externo...');

    // Crear el elemento
    const li = document.createElement('li');
    li.className = 'nav-item d-flex align-items-center ml-auto';
    li.id = 'bloqueUsuarioExterno';
    li.style.cssText = 'order: 999; display: flex !important; align-items: center !important; margin-left: auto !important; margin-right: 0 !important;';

    @php
        $user = Auth::user();
        $integranteExterno = optional($user)->integranteExterno;
        
        $nombreUsuario = 'Usuario';
        if ($integranteExterno) {
            $nombres = trim($integranteExterno->nombres ?? '');
            $apellidos = trim($integranteExterno->apellidos ?? '');
            $nombreUsuario = trim("{$nombres} {$apellidos}") ?: ($user->nombre_usuario ?? ($user->name ?? 'Usuario'));
        } else {
            $nombreUsuario = $user->nombre_usuario ?? ($user->name ?? 'Usuario');
        }
        
        $inicial = mb_substr(trim($nombreUsuario), 0, 1, 'UTF-8');
        
        $foto = null;
        if ($integranteExterno) {
            try {
                $foto = $integranteExterno->foto_perfil_url ?? null;
            } catch (\Exception $e) {}
        }
        if (!$foto && $user) {
            try {
                $foto = $user->foto_perfil_url ?? null;
            } catch (\Exception $e) {
                $foto = null;
            }
        }
    @endphp

    li.innerHTML = `
        <div class="d-flex align-items-center pl-3 pr-3"
             style="background: #ffffff; border-radius: 999px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); padding: 4px 16px; margin-right: 0 !important; min-width: 220px;">
            <div class="mr-3" id="avatarContainerExterno"
                 style="width: 40px; height: 40px; border-radius: 50%; overflow: hidden; background: #e9f5ff; flex-shrink: 0; position: relative; display: flex; align-items: center; justify-content: center;">
                <img id="headerAvatarExterno"
                     src="{{ $foto ?? '' }}"
                     alt="Foto perfil"
                     style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%; display: {{ $foto ? 'block' : 'none' }}; margin: 0; padding: 0;">
                <span id="headerAvatarInicialExterno"
                      style="font-size: 1rem; display: {{ $foto ? 'none' : 'flex' }}; align-items: center; justify-content: center; width: 40px; height: 40px; margin: 0; padding: 0; border-radius: 50%;">{{ $inicial }}</span>
            </div>
            <div class="d-flex flex-column mr-3" style="max-width: 200px; min-width: 120px; flex-grow: 1;">
                <span id="headerNombreExterno"
                      style="font-size: 0.9rem; color: #2c3e50; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 1.2;">
                    {{ $nombreUsuario }}
                </span>
                @if($integranteExterno)
                    <small style="font-size: 0.75rem; color: #8c9aa8; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 1.2;">
                        Integrante Externo
                    </small>
                @endif
            </div>
            <div class="dropdown" style="flex-shrink: 0;">
                <a class="text-muted" href="#" role="button" id="dropdownPerfilExterno"
                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                   style="font-size: 0.75rem; text-decoration: none; padding: 0.25rem;">
                    <i class="fas fa-chevron-down"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownPerfilExterno">
                    <a class="dropdown-item" href="/perfil/externo">
                        <i class="far fa-user mr-2"></i> Mi perfil
                    </a>
                    <a class="dropdown-item" href="/home-publica">
                        <i class="far fa-globe mr-2"></i> Ir a página pública
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="#" onclick="cerrarSesion(event)">
                        <i class="far fa-sign-out-alt mr-2"></i> Cerrar sesión
                    </a>
                </div>
            </div>
        </div>
    `;

    // Insertar al final del navbar
    navbar.appendChild(li);
    console.log('✅ Bloque de usuario externo creado e insertado en:', navbar);
    
    // Inicializar el dropdown de Bootstrap si está disponible
    if (typeof $ !== 'undefined' && $.fn.dropdown) {
        $('#dropdownPerfilExterno').dropdown();
    }
    
    // Llamar a actualizarHeaderExterno para cargar datos desde la API
    setTimeout(actualizarHeaderExterno, 500);
}

// Cargar nombre del usuario al iniciar
document.addEventListener('DOMContentLoaded', function() {
    // Crear el bloque de usuario
    crearBloqueUsuarioExterno();
    
    // También intentar después de múltiples delays para asegurar que se cree
    setTimeout(crearBloqueUsuarioExterno, 500);
    setTimeout(crearBloqueUsuarioExterno, 1000);
    setTimeout(crearBloqueUsuarioExterno, 2000);
    setTimeout(crearBloqueUsuarioExterno, 3000);
    // Cargar nombre del usuario
    const usuario = JSON.parse(localStorage.getItem('usuario') || '{}');
    const nombreUsuario = localStorage.getItem('nombre_usuario') || usuario.nombre || 'Usuario';
    
    // Actualizar nombre en el saludo si existe el elemento
    const nombreUsuarioEl = document.getElementById('nombreUsuario');
    if (nombreUsuarioEl) {
        nombreUsuarioEl.textContent = nombreUsuario;
    }
    
    // Guardar nombre en localStorage para uso futuro
    if (nombreUsuario && nombreUsuario !== 'Usuario') {
        localStorage.setItem('nombre_usuario', nombreUsuario);
    }
});

// Verificar que el usuario es externo y proteger rutas
document.addEventListener('DOMContentLoaded', function() {
    const tipoUsuario = localStorage.getItem('tipo_usuario');
    const currentPath = window.location.pathname;
    
    // Agregar listener para el botón de cerrar sesión en el sidebar
    setTimeout(() => {
        // Buscar todos los enlaces que tengan el atributo onclick con cerrarSesion
        const logoutLinks = document.querySelectorAll('a[onclick*="cerrarSesion"], a[href="#"]');
        logoutLinks.forEach(link => {
            const text = link.textContent.trim().toLowerCase();
            if (text.includes('cerrar sesión') || text.includes('cerrar sesion')) {
                link.removeAttribute('onclick');
            link.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                cerrarSesion(e);
                    return false;
            });
            }
        });
        
        // También buscar por texto en el sidebar y navbar, y por clase
        const allLinks = document.querySelectorAll('.sidebar a, .main-sidebar a, nav.sidebar a, .navbar-nav a, .logout-link');
        allLinks.forEach(link => {
            const text = link.textContent.trim().toLowerCase();
            const isLogoutLink = link.classList.contains('logout-link') || 
                                 text === 'cerrar sesión' || 
                                 text.includes('cerrar sesión') || 
                                 text.includes('cerrar sesion');
            
            if (isLogoutLink && !link.hasAttribute('data-logout-bound')) {
                link.setAttribute('data-logout-bound', 'true');
                // No remover onclick si ya tiene uno, solo agregar listener
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    cerrarSesion(e);
                    return false;
                }, true); // Usar capture phase para ejecutar antes
            }
        });
    }, 500);
    
    // Si el usuario es externo pero está en rutas de ONG, redirigir
    if (tipoUsuario === 'Integrante externo') {
        // Proteger contra acceso a rutas de ONG
        if (currentPath.startsWith('/ong/')) {
            console.warn('Usuario externo intentando acceder a ruta de ONG, redirigiendo...');
            if (currentPath.startsWith('/ong/eventos')) {
                window.location.href = '/externo/eventos';
                return;
            }
            if (currentPath.startsWith('/ong/voluntarios')) {
                window.location.href = '/externo/reportes';
                return;
            }
            if (currentPath.startsWith('/ong/reportes')) {
                window.location.href = '/externo/reportes';
                return;
            }
            window.location.href = '/home-externo';
            return;
        }
        
        // Asegurar que los enlaces del sidebar apunten a rutas correctas
        document.querySelectorAll('a[href^="/ong/"]').forEach(link => {
            const href = link.getAttribute('href');
            if (href.includes('/ong/eventos')) {
                link.setAttribute('href', '/externo/eventos');
            } else if (href.includes('/ong/voluntarios')) {
                link.setAttribute('href', '/externo/reportes');
            } else if (href.includes('/ong/reportes')) {
                link.setAttribute('href', '/externo/reportes');
            }
        });
    } else if (tipoUsuario && tipoUsuario !== 'Integrante externo') {
        console.warn('Usuario no es externo, pero está en panel de externo');
    }
});

// Función global para cerrar sesión
async function cerrarSesion(event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    // Confirmar antes de cerrar sesión (usar SweetAlert2 si está disponible, sino confirm nativo)
    let confirmar = false;
    
    if (typeof Swal !== 'undefined') {
        const result = await Swal.fire({
            title: '¿Cerrar sesión?',
            text: '¿Estás seguro de que deseas cerrar sesión?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0C2B44',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, cerrar sesión',
            cancelButtonText: 'Cancelar'
        });
        confirmar = result.isConfirmed;
    } else {
        confirmar = confirm('¿Estás seguro de que deseas cerrar sesión?');
    }
    
    if (!confirmar) {
        return false;
    }
    
    // Mostrar loading si SweetAlert2 está disponible
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Cerrando sesión...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }
    
    const token = localStorage.getItem('token');
    
    if (token) {
        try {
            const response = await fetch(`${API_BASE_URL}/api/auth/logout`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) {
                console.warn('Respuesta del servidor no exitosa:', response.status);
            }
        } catch (error) {
            console.error('Error al cerrar sesión en el servidor:', error);
            // Continuar con el cierre de sesión local aunque falle el servidor
        }
    }
    
    // Limpiar localStorage
    localStorage.clear();
    sessionStorage.clear();
    
    // Cerrar SweetAlert si está abierto
    if (typeof Swal !== 'undefined') {
        Swal.close();
    }
    
    // Redirigir al login
    window.location.href = '/login';
    
    return false;
}

// Asegurar que la función esté disponible globalmente
window.cerrarSesion = cerrarSesion;

// ================================
// Actualizar header con nombre/avatar desde la API de perfil
// ================================
async function actualizarHeaderExterno() {
    try {
        const nombreSpan = document.getElementById('headerNombreExterno');
        const avatarImg = document.getElementById('headerAvatarExterno');
        const inicialSpan = document.getElementById('headerAvatarInicialExterno');

        if (!nombreSpan) return;

        const token = localStorage.getItem('token');
        if (!token) return;

        let API_BASE_URL = window.location.origin;
        if (typeof window !== 'undefined' && window.API_BASE_URL) {
            API_BASE_URL = window.API_BASE_URL;
        }

        const res = await fetch(`${API_BASE_URL}/api/perfil`, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            },
            cache: 'no-store'
        });

        const data = await res.json();
        if (!data || data.success === false) {
            console.warn('⚠️ No se pudo obtener datos del perfil');
            return;
        }

        // Obtener nombre: prioridad integrante_externo (nombres + apellidos) > nombre_usuario
        let nombre = null;
        if (data.data && data.data.integrante_externo) {
            const nombres = data.data.integrante_externo.nombres || '';
            const apellidos = data.data.integrante_externo.apellidos || '';
            nombre = `${nombres} ${apellidos}`.trim();
        }
        if (!nombre && data.data && data.data.nombre_usuario) {
            nombre = data.data.nombre_usuario;
        }

        // Obtener foto: prioridad integrante_externo > usuario
        const foto = (data.data && data.data.integrante_externo && data.data.integrante_externo.foto_perfil) 
            || (data.data && data.data.foto_perfil) 
            || null;

        // Actualizar nombre
        if (nombre && nombreSpan) {
            nombreSpan.textContent = nombre;
        }

        // Actualizar avatar - asegurar círculo perfecto
        if (avatarImg && inicialSpan) {
            // Aplicar estilos para círculo perfecto
            const avatarContainer = document.getElementById('avatarContainerExterno');
            if (avatarContainer) {
                avatarContainer.style.cssText = 'width: 40px !important; height: 40px !important; border-radius: 50% !important; overflow: hidden !important; background: #e9f5ff; flex-shrink: 0; position: relative; display: flex; align-items: center; justify-content: center; margin-right: 0.75rem;';
            }
            
            // Estilos para la imagen - círculo perfecto
            avatarImg.style.cssText = 'width: 40px !important; height: 40px !important; border-radius: 50% !important; object-fit: cover !important; margin: 0 !important; padding: 0 !important; display: block;';
            
            // Estilos para la inicial - círculo perfecto
            inicialSpan.style.cssText = 'width: 40px !important; height: 40px !important; border-radius: 50% !important; margin: 0 !important; padding: 0 !important; display: flex; align-items: center; justify-content: center; font-size: 1rem; position: absolute; top: 0; left: 0;';
            
            if (foto && foto.trim() !== '') {
                // Hay foto: mostrar imagen, ocultar inicial
                avatarImg.src = foto;
                avatarImg.onerror = function() {
                    // Si la imagen falla al cargar, mostrar inicial
                    avatarImg.style.display = 'none';
                    inicialSpan.style.display = 'flex';
                    if (nombre) {
                        inicialSpan.textContent = nombre.charAt(0).toUpperCase();
                    }
                };
                avatarImg.onload = function() {
                    // Imagen cargada correctamente
                    avatarImg.style.display = 'block';
                    inicialSpan.style.display = 'none';
                };
                avatarImg.style.display = 'block';
                inicialSpan.style.display = 'none';
            } else {
                // No hay foto: mostrar inicial, ocultar imagen
                avatarImg.style.display = 'none';
                inicialSpan.style.display = 'flex';
                if (nombre) {
                    inicialSpan.textContent = nombre.charAt(0).toUpperCase();
                }
            }
        }
    } catch (e) {
        console.warn('⚠️ Error actualizando header Externo:', e);
    }
}

// Inicializar cuando el DOM esté listo
function inicializarHeaderExterno() {
    // Esperar a que el navbar esté renderizado
    const navbar = document.querySelector('.main-header .navbar-nav');
    if (navbar) {
        actualizarHeaderExterno();
    } else {
        // Reintentar si el navbar aún no está listo
        setTimeout(inicializarHeaderExterno, 200);
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(inicializarHeaderExterno, 300);
        setTimeout(actualizarHeaderExterno, 1000);
    });
} else {
    setTimeout(inicializarHeaderExterno, 300);
    setTimeout(actualizarHeaderExterno, 1000);
}

// También actualizar cuando la página recupera el foco
window.addEventListener('focus', () => {
    setTimeout(actualizarHeaderExterno, 500);
});

// Observer para detectar cuando el navbar se agregue al DOM
const observer = new MutationObserver((mutations) => {
    const navbar = document.querySelector('.main-header .navbar-nav') || 
                   document.querySelector('.navbar-nav') ||
                   document.querySelector('.main-header nav ul') ||
                   document.querySelector('nav.navbar ul');
    
    if (navbar && !document.getElementById('bloqueUsuarioExterno')) {
        console.log('🔍 Navbar detectado por observer, creando bloque...');
        crearBloqueUsuarioExterno();
    }
});

// Observar cambios en el DOM
if (document.body) {
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
}

// También intentar crear el bloque cuando la ventana se carga completamente
window.addEventListener('load', () => {
    setTimeout(crearBloqueUsuarioExterno, 1000);
    setTimeout(crearBloqueUsuarioExterno, 2000);
});
</script>
{{-- Script de alertas para eventos próximos --}}
<script src="{{ asset('assets/js/ong/alertas-eventos-proximos.js') }}"></script>
@endpush

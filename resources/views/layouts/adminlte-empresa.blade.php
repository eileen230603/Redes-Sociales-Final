{{-- resources/views/layouts/adminlte-empresa.blade.php --}}
{{-- Layout exclusivo para empresas --}}
@extends('adminlte::page')

@section('title', 'UNI2 • Panel de Empresa')

@push('js')
    {{-- Lucide icons para panel Empresa (opcional) --}}
    <script type="module">
        (async () => {
            try {
                const { createIcons, icons } = await import("https://unpkg.com/lucide@latest/dist/esm/lucide.js").catch(() => null);
                if (!icons || !createIcons) return;

                const faToLucide = {
                    'fa-home': 'home',
                    'fa-calendar-check': 'calendar-check',
                    'fa-hand-holding-heart': 'hand-heart',
                    'fa-bell': 'bell',
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
    // Configuración específica para empresas
    config(['adminlte.layout_topnav' => null]);
    config(['adminlte.layout_fixed_sidebar' => true]);
    config(['adminlte.layout_fixed_navbar' => true]);
    // Configurar el menú específico para empresas
    config(['adminlte.menu' => [
        ['header' => 'NAVEGACIÓN PRINCIPAL'],
        [
            'text' => 'Inicio',
            'url'  => '/home-empresa',
            'icon' => 'fas fa-home',
        ],
        [
            'text' => 'Eventos Patrocinados',
            'url'  => '/empresa/eventos',
            'icon' => 'fas fa-calendar-check',
        ],
        [
            'text' => 'Ayuda a Eventos',
            'url'  => '/empresa/eventos/disponibles',
            'icon' => 'fas fa-hand-holding-heart',
        ],
        [
            'text' => 'Notificaciones',
            'url'  => '/empresa/notificaciones',
            'icon' => 'fas fa-bell',
        ],
        [
            'text' => 'Reportes',
            'url'  => '/empresa/reportes',
            'icon' => 'fas fa-chart-bar',
        ],
        [
            'text' => 'Mi Perfil',
            'url'  => '/perfil/empresa',
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
        <i class="far fa-building mr-2" style="color: #00A36C;"></i>
        @yield('page_title', 'Panel de Empresa')
    </h1>
</div>
@stop

@section('content')
<div class="container-fluid">
    @yield('content_body')
</div>
@stop

{{-- NAVBAR SUPERIOR --}}
@push('adminlte_topnav')
    {{-- 🔔 Ícono de Notificaciones - POSICIONADO ANTES DEL MENÚ DE USUARIO (círculo gris) --}}
    <li class="nav-item" id="notificacionesNavItemEmpresa" style="display: flex !important; align-items: center; order: 998;">
        <a href="/empresa/notificaciones" class="nav-link position-relative" id="notificacionesIconoEmpresa" title="Notificaciones" style="display: flex !important; align-items: center; justify-content: center; padding: 0.5rem 0.75rem !important; min-width: 45px; color: #6c757d !important;">
            <i class="fas fa-bell" style="font-size: 1.25rem !important; display: block !important;"></i>
            <span class="badge badge-danger position-absolute" id="contadorNotificacionesEmpresa" style="top: 2px; right: 2px; display: none; font-size: 0.7rem; padding: 4px 7px; min-width: 20px; height: 20px; line-height: 12px; border-radius: 10px; font-weight: bold; z-index: 10; background-color: #dc3545 !important; color: white !important; box-shadow: 0 2px 4px rgba(0,0,0,0.2); align-items: center; justify-content: center;">0</span>
        </a>
    </li>
    
    {{-- Avatar y nombre del usuario empresa - COMPLETAMENTE A LA DERECHA --}}
    <li class="nav-item d-flex align-items-center ml-auto" style="order: 999; margin-left: auto !important; margin-right: 0 !important;">
        <div class="d-flex align-items-center pl-3 pr-3"
             style="background: #ffffff; border-radius: 999px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); padding: 4px 16px; margin-right: 0 !important; min-width: 220px;">
            @php
                $user = Auth::user();
                $empresa = optional($user)->empresa;
                
                // Nombre: nombre_empresa > nombre_usuario > name > 'Usuario'
                $nombreUsuario = 'Usuario';
                if ($empresa) {
                    $nombreUsuario = trim($empresa->nombre_empresa ?? '') ?: ($user->nombre_usuario ?? ($user->name ?? 'Usuario'));
                } else {
                    $nombreUsuario = $user->nombre_usuario ?? ($user->name ?? 'Usuario');
                }
                
                $inicial = mb_substr(trim($nombreUsuario), 0, 1, 'UTF-8');
                
                // Prioridad de avatar: foto de empresa > foto de usuario > null
                $foto = null;
                if ($empresa) {
                    try {
                        $foto = $empresa->foto_perfil_url ?? null;
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
            
            {{-- Avatar redondo --}}
            <div class="mr-3" id="avatarContainerEmpresa"
                 style="width: 40px; height: 40px; border-radius: 50%; overflow: hidden; background: #e9f5ff; flex-shrink: 0; position: relative; display: flex; align-items: center; justify-content: center;">
                <img id="headerAvatarEmpresa"
                     src="{{ $foto ?? '' }}"
                     alt="Foto perfil"
                     style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%; display: {{ $foto ? 'block' : 'none' }}; margin: 0; padding: 0;">
                <span id="headerAvatarInicialEmpresa"
                      style="font-size: 1rem; display: {{ $foto ? 'none' : 'flex' }}; align-items: center; justify-content: center; width: 40px; height: 40px; margin: 0; padding: 0; border-radius: 50%;">{{ $inicial }}</span>
            </div>
            
            {{-- Nombre + etiqueta --}}
            <div class="d-flex flex-column mr-3" style="max-width: 200px; min-width: 120px; flex-grow: 1;">
                <span id="headerNombreEmpresa"
                      style="font-size: 0.9rem; color: #2c3e50; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 1.2;">
                    {{ $nombreUsuario }}
                </span>
                @if($empresa)
                    <small style="font-size: 0.75rem; color: #8c9aa8; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 1.2;">
                        Empresa
                    </small>
                @endif
            </div>
            
            {{-- Dropdown de opciones --}}
            <div class="dropdown" style="flex-shrink: 0;">
                <a class="text-muted" href="#" role="button" id="dropdownPerfilEmpresa"
                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                   style="font-size: 0.75rem; text-decoration: none; padding: 0.25rem;">
                    <i class="fas fa-chevron-down"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownPerfilEmpresa">
                    <a class="dropdown-item" href="/perfil/empresa">
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
        transform: translateX(4px) !important;
    }
    
    /* Iconos del Sidebar */
    .sidebar-dark-primary .nav-sidebar .nav-link i,
    .main-sidebar .nav-sidebar .nav-link i {
        margin-right: 0.75rem !important;
        width: 20px !important;
        text-align: center !important;
        transition: transform 0.3s ease !important;
    }
    
    .sidebar-dark-primary .nav-sidebar .nav-link:hover:not(.active) i,
    .main-sidebar .nav-sidebar .nav-link:hover:not(.active) i {
        transform: scale(1.1) !important;
    }
    
    /* Headers del Sidebar */
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
    
    /* Content Header */
    .content-header h1 {
        color: #0C2B44 !important;
        font-weight: 700 !important;
    }
    
    /* Estilos específicos para panel de empresa */
    
    /* Animación para el contador de notificaciones */
    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.1);
        }
    }
    
    .pulse-animation {
        animation: pulse 1.5s ease-in-out infinite;
    }

    /* ============================================
       ESTILOS PARA ICONO DE NOTIFICACIONES EMPRESA
       ============================================ */
    
    /* Asegurar que el navbar muestre el ícono */
    .navbar-nav .nav-item {
        display: flex;
        align-items: center;
    }
    
    /* Forzar que el icono de notificaciones esté siempre visible - ANTES DEL MENÚ DE USUARIO */
    #notificacionesNavItemEmpresa {
        display: flex !important;
        visibility: visible !important;
        opacity: 1 !important;
        order: 998 !important;
    }
    
    /* Asegurar que el icono esté justo antes del menú de usuario */
    .navbar-nav > #notificacionesNavItemEmpresa {
        order: 998 !important;
    }
    
    /* El menú de usuario (círculo gris) debe estar después */
    .navbar-nav > .nav-item:has(.user-menu) {
        order: 999 !important;
    }
    
    #notificacionesIconoEmpresa {
        display: flex !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    /* Forzar visibilidad del ícono en todas las pantallas */
    @media (max-width: 576px) {
        #notificacionesIconoEmpresa {
            display: flex !important;
            padding: 0.4rem 0.6rem !important;
        }
        #notificacionesNavItemEmpresa {
            display: flex !important;
            visibility: visible !important;
        }
    }
    
    /* Asegurar que el navbar tenga espacio para el icono */
    .main-header .navbar-nav {
        display: flex;
        align-items: center;
        flex-wrap: nowrap;
    }
    
    /* Forzar que el icono esté justo antes del menú de usuario */
    .main-header .navbar-nav > #notificacionesNavItemEmpresa {
        order: 998 !important;
        margin-right: 0.5rem;
    }
    
    /* Asegurar visibilidad en todas las pantallas */
    body:has(.main-header) #notificacionesNavItemEmpresa {
        display: flex !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
    
    /* Estilo específico para el icono de notificaciones */
    #notificacionesIconoEmpresa {
        cursor: pointer;
        border-radius: 50%;
        width: 45px;
        height: 45px;
        display: flex !important;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    
    #notificacionesIconoEmpresa:hover {
        background-color: rgba(0, 0, 0, 0.05);
    }
    
    /* Badge más visible */
    #contadorNotificacionesEmpresa {
        position: absolute !important;
        top: 5px !important;
        right: 5px !important;
        z-index: 1000 !important;
        font-size: 0.65rem !important;
        padding: 3px 6px !important;
        min-width: 18px !important;
        height: 18px !important;
        border-radius: 9px !important;
        font-weight: bold !important;
        background-color: #dc3545 !important;
        color: white !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.3) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    /* Asegurar que el icono esté visible incluso sin notificaciones */
    #notificacionesNavItemEmpresa .nav-link {
        display: flex !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    /* Estilos para el bloque de avatar y nombre del usuario empresa */
    .main-header .navbar-nav .nav-item.ml-auto {
        margin-left: auto !important;
        margin-right: 0 !important;
        display: flex !important;
        align-items: center !important;
    }
    
    /* Asegurar que el bloque esté completamente a la derecha */
    #bloqueUsuarioEmpresa {
        margin-left: auto !important;
        margin-right: 0 !important;
    }
    
    #bloqueUsuarioEmpresa > div {
        margin-right: 0 !important;
    }
    
    .main-header .navbar-nav .nav-item .dropdown-toggle,
    .main-header .navbar-nav .nav-item #dropdownPerfilEmpresa {
        border: none;
        background: transparent;
        padding: 0.25rem 0.5rem;
        color: #6c757d;
    }
    
    .main-header .navbar-nav .nav-item .dropdown-toggle:hover,
    .main-header .navbar-nav .nav-item #dropdownPerfilEmpresa:hover {
        color: var(--brand-primario) !important;
    }
    
    /* Ocultar el pseudo-elemento ::after de Bootstrap que agrega una flecha automática */
    .main-header .navbar-nav .nav-item #dropdownPerfilEmpresa::after {
        display: none !important;
    }
    
    /* Asegurar que el avatar sea un círculo perfecto */
    #avatarContainerEmpresa {
        width: 40px !important;
        height: 40px !important;
        border-radius: 50% !important;
        overflow: hidden !important;
    }
    
    #headerAvatarEmpresa {
        width: 40px !important;
        height: 40px !important;
        object-fit: cover !important;
        border-radius: 50% !important;
    }
    
    #headerAvatarInicialEmpresa {
        width: 40px !important;
        height: 40px !important;
        border-radius: 50% !important;
    }
</style>
@endpush

@push('js')
<script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
<script src="{{ asset('assets/js/config.js') }}"></script>
<script>
// Verificar que el usuario es empresa y proteger rutas
document.addEventListener('DOMContentLoaded', function() {
    const tipoUsuario = localStorage.getItem('tipo_usuario');
    const currentPath = window.location.pathname;
    
    // Agregar listener para el botón de cerrar sesión en el sidebar
    setTimeout(() => {
        // Buscar todos los enlaces con clase logout-link o que tengan el atributo onclick con cerrarSesion
        const logoutLinks = document.querySelectorAll('a.logout-link, a[onclick*="cerrarSesion"]');
        logoutLinks.forEach(link => {
            // Remover listeners anteriores para evitar duplicados
            const newLink = link.cloneNode(true);
            link.parentNode.replaceChild(newLink, link);
            
            newLink.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                cerrarSesion(e);
            });
        });
        
        // También buscar por texto en el sidebar
        const sidebarLinks = document.querySelectorAll('.sidebar a, .main-sidebar a, nav.sidebar a');
        sidebarLinks.forEach(link => {
            const text = link.textContent.trim();
            if ((text === 'Cerrar sesión' || text.includes('Cerrar sesión')) && !link.classList.contains('logout-link')) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    cerrarSesion(e);
                });
            }
        });
    }, 500);
    
    // Si el usuario es empresa pero está en rutas de ONG o externo, redirigir
    if (tipoUsuario === 'Empresa') {
        // Proteger contra acceso a rutas de ONG
        if (currentPath.startsWith('/ong/')) {
            console.warn('Usuario empresa intentando acceder a ruta de ONG, redirigiendo...');
            window.location.href = '/home-empresa';
            return;
        }
        // Proteger contra acceso a rutas de externo
        if (currentPath.startsWith('/externo/')) {
            console.warn('Usuario empresa intentando acceder a ruta de externo, redirigiendo...');
            window.location.href = '/home-empresa';
            return;
        }
    } else if (tipoUsuario && tipoUsuario !== 'Empresa') {
        console.warn('Usuario no es empresa, pero está en panel de empresa');
    }
});

// Función global para cerrar sesión
async function cerrarSesion(event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    // Confirmar antes de cerrar sesión
    if (!confirm('¿Estás seguro de que deseas cerrar sesión?')) {
        return false;
    }
    
    const token = localStorage.getItem('token');
    
    if (token) {
        try {
            await fetch(`${API_BASE_URL}/api/auth/logout`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });
        } catch (error) {
            console.error('Error al cerrar sesión en el servidor:', error);
            // Continuar con el cierre de sesión local aunque falle el servidor
        }
    }
    
    // Limpiar localStorage
    localStorage.clear();
    
    // Redirigir al login
    window.location.href = '/login';
    
    return false;
}

// Asegurar que la función esté disponible globalmente
window.cerrarSesion = cerrarSesion;

// ================================
// Función para crear e insertar el bloque de avatar y nombre en el navbar
// ================================
function crearBloqueUsuarioEmpresa() {
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
        setTimeout(crearBloqueUsuarioEmpresa, 300);
        return;
    }

    // Verificar si ya existe
    if (document.getElementById('bloqueUsuarioEmpresa')) {
        console.log('✅ Bloque de usuario empresa ya existe');
        return;
    }
    
    console.log('🔍 Creando bloque de usuario empresa...');

    // Crear el elemento
    const li = document.createElement('li');
    li.className = 'nav-item d-flex align-items-center ml-auto';
    li.id = 'bloqueUsuarioEmpresa';
    li.style.cssText = 'order: 999; display: flex !important; align-items: center !important; margin-left: auto !important; margin-right: 0 !important;';

    @php
        $user = Auth::user();
        $empresa = optional($user)->empresa;
        
        $nombreUsuario = 'Usuario';
        if ($empresa) {
            $nombreUsuario = trim($empresa->nombre_empresa ?? '') ?: ($user->nombre_usuario ?? ($user->name ?? 'Usuario'));
        } else {
            $nombreUsuario = $user->nombre_usuario ?? ($user->name ?? 'Usuario');
        }
        
        $inicial = mb_substr(trim($nombreUsuario), 0, 1, 'UTF-8');
        
        $foto = null;
        if ($empresa) {
            try {
                $foto = $empresa->foto_perfil_url ?? null;
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
            <div class="mr-3" id="avatarContainerEmpresa"
                 style="width: 40px; height: 40px; border-radius: 50%; overflow: hidden; background: #e9f5ff; flex-shrink: 0; position: relative; display: flex; align-items: center; justify-content: center;">
                <img id="headerAvatarEmpresa"
                     src="{{ $foto ?? '' }}"
                     alt="Foto perfil"
                     style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%; display: {{ $foto ? 'block' : 'none' }}; margin: 0; padding: 0;">
                <span id="headerAvatarInicialEmpresa"
                      style="font-size: 1rem; display: {{ $foto ? 'none' : 'flex' }}; align-items: center; justify-content: center; width: 40px; height: 40px; margin: 0; padding: 0; border-radius: 50%;">{{ $inicial }}</span>
            </div>
            <div class="d-flex flex-column mr-3" style="max-width: 200px; min-width: 120px; flex-grow: 1;">
                <span id="headerNombreEmpresa"
                      style="font-size: 0.9rem; color: #2c3e50; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 1.2;">
                    {{ $nombreUsuario }}
                </span>
                @if($empresa)
                    <small style="font-size: 0.75rem; color: #8c9aa8; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 1.2;">
                        Empresa
                    </small>
                @endif
            </div>
            <div class="dropdown" style="flex-shrink: 0;">
                <a class="text-muted" href="#" role="button" id="dropdownPerfilEmpresa"
                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                   style="font-size: 0.75rem; text-decoration: none; padding: 0.25rem;">
                    <i class="fas fa-chevron-down"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownPerfilEmpresa">
                    <a class="dropdown-item" href="/perfil/empresa">
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
    console.log('✅ Bloque de usuario empresa creado e insertado en:', navbar);
    
    // Inicializar el dropdown de Bootstrap si está disponible
    if (typeof $ !== 'undefined' && $.fn.dropdown) {
        $('#dropdownPerfilEmpresa').dropdown();
    }
    
    // Llamar a actualizarHeaderEmpresa para cargar datos desde la API
    setTimeout(actualizarHeaderEmpresa, 500);
}

// ================================
// Actualizar header con nombre/avatar desde la API de perfil
// ================================
async function actualizarHeaderEmpresa() {
    try {
        const nombreSpan = document.getElementById('headerNombreEmpresa');
        const avatarImg = document.getElementById('headerAvatarEmpresa');
        const inicialSpan = document.getElementById('headerAvatarInicialEmpresa');

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

        // Obtener nombre: prioridad empresa > usuario
        let nombre = null;
        if (data.data && data.data.empresa) {
            nombre = data.data.empresa.nombre_empresa || null;
        }
        if (!nombre && data.data && data.data.nombre_usuario) {
            nombre = data.data.nombre_usuario;
        }

        // Obtener foto: prioridad empresa > usuario
        const foto = (data.data && data.data.empresa && data.data.empresa.foto_perfil) 
            || (data.data && data.data.foto_perfil) 
            || null;

        // Actualizar nombre
        if (nombre && nombreSpan) {
            nombreSpan.textContent = nombre;
        }

        // Actualizar avatar - asegurar círculo perfecto
        if (avatarImg && inicialSpan) {
            // Aplicar estilos para círculo perfecto
            const avatarContainer = document.getElementById('avatarContainerEmpresa');
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
        console.warn('⚠️ Error actualizando header Empresa:', e);
    }
}

// Inicializar cuando el DOM esté listo
function inicializarHeaderEmpresa() {
    // Esperar a que el navbar esté renderizado
    const navbar = document.querySelector('.main-header .navbar-nav');
    if (navbar) {
        crearBloqueUsuarioEmpresa();
    } else {
        // Reintentar si el navbar aún no está listo
        setTimeout(inicializarHeaderEmpresa, 200);
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(inicializarHeaderEmpresa, 300);
        setTimeout(crearBloqueUsuarioEmpresa, 1000);
        setTimeout(actualizarHeaderEmpresa, 2000);
    });
} else {
    setTimeout(inicializarHeaderEmpresa, 300);
    setTimeout(crearBloqueUsuarioEmpresa, 1000);
    setTimeout(actualizarHeaderEmpresa, 2000);
}

// También actualizar cuando la página recupera el foco
window.addEventListener('focus', () => {
    setTimeout(actualizarHeaderEmpresa, 500);
});

// Observer para detectar cuando el navbar se agregue al DOM
const observerEmpresa = new MutationObserver((mutations) => {
    const navbar = document.querySelector('.main-header .navbar-nav') || 
                   document.querySelector('.navbar-nav') ||
                   document.querySelector('.main-header nav ul') ||
                   document.querySelector('nav.navbar ul');
    
    if (navbar && !document.getElementById('bloqueUsuarioEmpresa')) {
        console.log('🔍 Navbar detectado por observer, creando bloque empresa...');
        crearBloqueUsuarioEmpresa();
    }
});

// Observar cambios en el DOM
if (document.body) {
    observerEmpresa.observe(document.body, {
        childList: true,
        subtree: true
    });
}

// También intentar crear el bloque cuando la ventana se carga completamente
window.addEventListener('load', () => {
    setTimeout(crearBloqueUsuarioEmpresa, 1000);
    setTimeout(crearBloqueUsuarioEmpresa, 2000);
});

// ===============================
// 🔔 SISTEMA DE NOTIFICACIONES PARA EMPRESA
// ===============================
async function actualizarContadorNotificacionesEmpresa() {
    try {
        const token = localStorage.getItem('token');
        if (!token) return;

        const tipoUsuario = localStorage.getItem('tipo_usuario');
        if (tipoUsuario !== 'Empresa') return;

        const res = await fetch(`${API_BASE_URL}/api/empresas/notificaciones/contador`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if (!res.ok) return;

        const data = await res.json();
        const contador = data.no_leidas || 0;
        
        // Actualizar contador en el navbar superior
        const contadorNavbar = document.getElementById('contadorNotificacionesEmpresa');
        const iconoNavbar = document.getElementById('notificacionesIconoEmpresa');

        if (contadorNavbar) {
            if (contador > 0) {
                contadorNavbar.textContent = contador > 99 ? '99+' : contador;
                contadorNavbar.style.display = 'flex';
                contadorNavbar.style.visibility = 'visible';
                contadorNavbar.style.opacity = '1';
                contadorNavbar.classList.add('pulse-animation');
            } else {
                contadorNavbar.style.display = 'none';
                contadorNavbar.style.visibility = 'hidden';
                contadorNavbar.style.opacity = '0';
                contadorNavbar.classList.remove('pulse-animation');
            }
        }

        if (iconoNavbar) {
            iconoNavbar.style.display = 'flex';
            iconoNavbar.style.visibility = 'visible';
        }

        // Actualizar contador en el menú del sidebar
        const sidebarNotificaciones = document.querySelector('.sidebar a[href="/empresa/notificaciones"]');
        if (sidebarNotificaciones) {
            // Buscar o crear el badge en el sidebar
            let sidebarBadge = sidebarNotificaciones.querySelector('.badge');
            if (!sidebarBadge && contador > 0) {
                sidebarBadge = document.createElement('span');
                sidebarBadge.className = 'badge badge-danger float-right';
                sidebarBadge.style.cssText = 'margin-top: 3px; font-weight: bold;';
                sidebarNotificaciones.appendChild(sidebarBadge);
            }
            
            if (sidebarBadge) {
                if (contador > 0) {
                    sidebarBadge.textContent = contador > 99 ? '99+' : contador;
                    sidebarBadge.style.display = 'inline-block';
                    sidebarNotificaciones.classList.add('text-warning');
                } else {
                    sidebarBadge.style.display = 'none';
                    sidebarNotificaciones.classList.remove('text-warning');
                }
            }
        }
    } catch (error) {
        console.warn('Error actualizando contador de notificaciones:', error);
    }
}

// Inicializar sistema de notificaciones para empresa
function inicializarNotificacionesEmpresa() {
    const token = localStorage.getItem('token');
    if (!token) return;

    const tipoUsuario = localStorage.getItem('tipo_usuario');
    
    if (tipoUsuario !== 'Empresa') {
        const navItem = document.getElementById('notificacionesNavItemEmpresa');
        if (navItem) navItem.style.display = 'none';
        return;
    }

    // Buscar el navbar
    const navbarNav = document.querySelector('.main-header .navbar-nav');
    if (!navbarNav) {
        // Si no existe el navbar, intentar de nuevo más tarde
        setTimeout(inicializarNotificacionesEmpresa, 200);
        return;
    }

    let navItem = document.getElementById('notificacionesNavItemEmpresa');
    
    // Si el item no existe o no está en el navbar, crearlo/agregarlo
    if (!navItem || !navbarNav.contains(navItem)) {
        // Crear el item si no existe
        if (!navItem) {
            navItem = document.createElement('li');
            navItem.id = 'notificacionesNavItemEmpresa';
            navItem.className = 'nav-item';
            navItem.style.cssText = 'display: flex !important; align-items: center; order: 998;';
            
            const link = document.createElement('a');
            link.href = '/empresa/notificaciones';
            link.id = 'notificacionesIconoEmpresa';
            link.className = 'nav-link position-relative';
            link.title = 'Notificaciones';
            link.style.cssText = 'display: flex !important; align-items: center; justify-content: center; padding: 0.5rem 0.75rem !important; min-width: 45px; color: #6c757d !important; cursor: pointer;';
            
            const icon = document.createElement('i');
            icon.className = 'fas fa-bell';
            icon.style.cssText = 'font-size: 1.25rem !important; display: block !important;';
            
            const badge = document.createElement('span');
            badge.id = 'contadorNotificacionesEmpresa';
            badge.className = 'badge badge-danger position-absolute';
            badge.style.cssText = 'top: 2px; right: 2px; display: none; font-size: 0.7rem; padding: 4px 7px; min-width: 20px; height: 20px; line-height: 12px; border-radius: 10px; font-weight: bold; z-index: 10; background-color: #dc3545 !important; color: white !important; box-shadow: 0 2px 4px rgba(0,0,0,0.2); align-items: center; justify-content: center;';
            badge.textContent = '0';
            
            link.appendChild(icon);
            link.appendChild(badge);
            navItem.appendChild(link);
        }
        
        // Insertar antes del menú de usuario
        const userMenu = navbarNav.querySelector('.user-menu, .nav-item:has(.user-menu)');
        if (userMenu && userMenu.parentElement) {
            userMenu.parentElement.insertBefore(navItem, userMenu);
        } else {
            navbarNav.appendChild(navItem);
        }
    }

    // Asegurar que el ícono esté visible - FORZAR VISIBILIDAD
    navItem.style.display = 'flex';
    navItem.style.visibility = 'visible';
    navItem.style.opacity = '1';
    navItem.style.order = '998';
    
    const iconoNavbar = document.getElementById('notificacionesIconoEmpresa');
    if (iconoNavbar) {
        iconoNavbar.style.display = 'flex';
        iconoNavbar.style.visibility = 'visible';
        iconoNavbar.style.opacity = '1';
    }

    // Cargar contador inicial
    actualizarContadorNotificacionesEmpresa();
    setTimeout(actualizarContadorNotificacionesEmpresa, 500);
    setTimeout(actualizarContadorNotificacionesEmpresa, 1500);

    // Actualizar contador cada 5 segundos
    if (!window.notificacionesEmpresaInterval) {
        window.notificacionesEmpresaInterval = setInterval(actualizarContadorNotificacionesEmpresa, 5000);
    }
    
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) {
            actualizarContadorNotificacionesEmpresa();
        }
    });
    
    window.addEventListener('focus', actualizarContadorNotificacionesEmpresa);
</script>
{{-- Script de alertas para eventos próximos --}}
<script src="{{ asset('assets/js/ong/alertas-eventos-proximos.js') }}"></script>
}

// Ejecutar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', inicializarNotificacionesEmpresa);

// También ejecutar después de delays para asegurar que AdminLTE haya renderizado
setTimeout(inicializarNotificacionesEmpresa, 100);
setTimeout(inicializarNotificacionesEmpresa, 500);
setTimeout(inicializarNotificacionesEmpresa, 1000);
setTimeout(inicializarNotificacionesEmpresa, 2000);

// Hacer la función disponible globalmente
window.actualizarContadorNotificacionesEmpresa = actualizarContadorNotificacionesEmpresa;
</script>
@endpush


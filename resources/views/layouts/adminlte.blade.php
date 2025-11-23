{{-- resources/views/layouts/adminlte.blade.php --}}
@extends('adminlte::page')

@section('title', 'UNI2 • Panel Principal')

{{-- HEADER --}}
@section('content_header')
<div class="d-flex align-items-center justify-content-between">
    <h1 class="mb-0 text-primary">
        <i class="fas fa-layer-group mr-2"></i>
        @yield('page_title', 'Panel UNI2')
    </h1>
</div>
@stop

{{-- CONTENIDO PRINCIPAL --}}
@section('content')
<div class="container-fluid">
    @yield('content_body')
</div>
@stop

{{-- NAVBAR SUPERIOR --}}
@push('adminlte_topnav')
    {{-- 🔔 Ícono de Notificaciones - POSICIONADO ANTES DEL MENÚ DE USUARIO (círculo gris) --}}
    <li class="nav-item" id="notificacionesNavItem" style="display: flex !important; align-items: center; order: 998;">
        <a href="{{ route('ong.notificaciones.index') }}" class="nav-link position-relative" id="notificacionesIcono" title="Notificaciones" style="display: flex !important; align-items: center; justify-content: center; padding: 0.5rem 0.75rem !important; min-width: 45px; color: #6c757d !important;">
            <i class="fas fa-bell" style="font-size: 1.25rem !important; display: block !important;"></i>
            <span class="badge badge-danger position-absolute" id="contadorNotificaciones" style="top: 2px; right: 2px; display: none; font-size: 0.7rem; padding: 4px 7px; min-width: 20px; height: 20px; line-height: 12px; border-radius: 10px; font-weight: bold; z-index: 10; background-color: #dc3545 !important; color: white !important; box-shadow: 0 2px 4px rgba(0,0,0,0.2); align-items: center; justify-content: center;">0</span>
        </a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
        <a href="/home-publica" class="nav-link">
            <i class="fas fa-globe-americas mr-1"></i> Ir a página pública
        </a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
        <a href="#" onclick="cerrarSesion(event)" class="nav-link text-danger">
            <i class="fas fa-sign-out-alt mr-1"></i> Cerrar sesión
        </a>
    </li>
@endpush

{{-- SIDEBAR --}}
@push('adminlte_sidebar')
<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">

        {{-- 🏠 Inicio --}}
        <li class="nav-item">
            <a href="/home-ong" class="nav-link {{ request()->is('home-ong') ? 'active' : '' }}">
                <i class="nav-icon fas fa-home"></i>
                <p>Inicio</p>
            </a>
        </li>

        {{-- 📅 Eventos --}}
        <li class="nav-item has-treeview {{ request()->is('ong/eventos*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->is('ong/eventos*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-calendar-alt"></i>
                <p>
                    Eventos
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">

                {{-- Ver eventos --}}
                <li class="nav-item">
                    <a href="{{ route('ong.eventos.index') }}" 
                       class="nav-link {{ request()->is('ong/eventos') ? 'active' : '' }}">
                        <i class="fas fa-list nav-icon text-primary"></i>
                        <p>Ver eventos</p>
                    </a>
                </li>

                {{-- Crear evento --}}
                <li class="nav-item">
                    <a href="{{ route('ong.eventos.create') }}" 
                       class="nav-link {{ request()->is('ong/eventos/crear') ? 'active' : '' }}">
                        <i class="fas fa-calendar-plus nav-icon text-success"></i>
                        <p>Crear evento</p>
                    </a>
                </li>
            </ul>
        </li>

        {{-- 👥 Voluntarios --}}
        <li class="nav-item">
            <a href="{{ route('ong.voluntarios.index') }}" class="nav-link {{ request()->is('ong/voluntarios*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-users"></i>
                <p>Voluntarios</p>
            </a>
        </li>

        {{-- 📊 Dashboard --}}
        <li class="nav-item">
            <a href="{{ route('ong.dashboard.index') }}" class="nav-link {{ request()->is('ong/dashboard*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
            </a>
        </li>

        {{-- 📊 Reportes --}}
        <li class="nav-item">
            <a href="{{ route('ong.reportes.index') }}" class="nav-link {{ request()->is('ong/reportes*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-chart-bar"></i>
                <p>Reportes</p>
            </a>
        </li>

        {{-- 🔔 Notificaciones --}}
        <li class="nav-item">
            <a href="{{ route('ong.notificaciones.index') }}" class="nav-link {{ request()->is('ong/notificaciones*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-bell"></i>
                <p>Notificaciones</p>
            </a>
        </li>

        {{-- 👤 Perfil --}}
        <li class="nav-item">
            <a href="{{ route('perfil.ong') }}" class="nav-link {{ request()->is('perfil/ong') ? 'active' : '' }}">
                <i class="nav-icon fas fa-user-circle"></i>
                <p>Mi Perfil</p>
            </a>
        </li>

        {{-- 🌐 OTRAS OPCIONES --}}
        <li class="nav-header">OTRAS OPCIONES</li>

        <li class="nav-item">
            <a href="/home-publica" class="nav-link">
                <i class="nav-icon fas fa-globe"></i>
                <p>Ir a página pública</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="#" onclick="cerrarSesion(event)" class="nav-link text-danger">
                <i class="nav-icon fas fa-sign-out-alt"></i>
                <p>Cerrar sesión</p>
            </a>
        </li>
    </ul>
</nav>
@endpush

{{-- CSS --}}
@section('css')
<link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/custom.css') }}">
<style>
    /* Animación de pulso para notificaciones nuevas */
    @keyframes pulse {
        0% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
        }
        50% {
            transform: scale(1.1);
            box-shadow: 0 0 0 10px rgba(220, 53, 69, 0);
        }
        100% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
        }
    }
    
    .pulse-animation {
        animation: pulse 2s infinite;
    }

    /* Estilo para el ícono de notificaciones - SIEMPRE VISIBLE */
    #notificacionesIcono {
        display: flex !important;
        align-items: center;
        justify-content: center;
        padding: 0.5rem 0.75rem !important;
        transition: all 0.3s ease;
        position: relative;
        min-width: 45px;
        height: 100%;
        color: #6c757d !important;
    }

    #notificacionesIcono:hover {
        transform: scale(1.1);
        color: #dc3545 !important;
        background-color: rgba(220, 53, 69, 0.1);
        border-radius: 4px;
    }

    #notificacionesIcono i {
        font-size: 1.25rem !important;
        display: block !important;
    }

    /* Contador de notificaciones en navbar - SIEMPRE VISIBLE CUANDO HAY NOTIFICACIONES */
    #contadorNotificaciones {
        position: absolute;
        top: 2px;
        right: 2px;
        border-radius: 10px;
        font-weight: bold;
        line-height: 12px;
        padding: 4px 7px;
        min-width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
        background-color: #dc3545 !important;
        color: white !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        font-size: 0.7rem;
    }

    /* Asegurar que el navbar muestre el ícono */
    .navbar-nav .nav-item {
        display: flex;
        align-items: center;
    }
    
    /* Forzar que el icono de notificaciones esté siempre visible - ANTES DEL MENÚ DE USUARIO */
    #notificacionesNavItem {
        display: flex !important;
        visibility: visible !important;
        opacity: 1 !important;
        order: 998 !important;
    }
    
    /* Asegurar que el icono esté justo antes del menú de usuario */
    .navbar-nav > #notificacionesNavItem {
        order: 998 !important;
    }
    
    /* El menú de usuario (círculo gris) debe estar después */
    .navbar-nav > .nav-item:has(.user-menu) {
        order: 999 !important;
    }
    
    #notificacionesIcono {
        display: flex !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    /* Forzar visibilidad del ícono en todas las pantallas */
    @media (max-width: 576px) {
        #notificacionesIcono {
            display: flex !important;
            padding: 0.4rem 0.6rem !important;
        }
        #notificacionesNavItem {
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
    .main-header .navbar-nav > #notificacionesNavItem {
        order: 998 !important;
        margin-right: 0.5rem;
    }
    
    /* Asegurar visibilidad en todas las pantallas */
    body:has(.main-header) #notificacionesNavItem {
        display: flex !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
    
    /* Estilo específico para el icono de notificaciones */
    #notificacionesIcono {
        cursor: pointer;
        border-radius: 50%;
        width: 45px;
        height: 45px;
        display: flex !important;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    
    #notificacionesIcono:hover {
        background-color: rgba(0, 0, 0, 0.05);
    }
    
    /* Badge más visible */
    #contadorNotificaciones {
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
</style>
@stop

{{-- JS --}}
@section('js')
<script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
<script src="{{ asset('js/custom.js') }}"></script>
<script src="{{ asset('assets/js/config.js') }}"></script>
{{-- Script global para icono de notificaciones en todas las pantallas ONG --}}
<script src="{{ asset('js/notificaciones-ong.js') }}"></script>
<script>
async function cerrarSesion(event) {
    event.preventDefault();
    
    const token = localStorage.getItem('token');
    
    if (token) {
        try {
            await fetch(`${API_BASE_URL}/api/auth/logout`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                }
            });
        } catch (error) {
            console.error('Error al cerrar sesión en el servidor:', error);
        }
    }
    
    localStorage.clear();
    window.location.href = '/login';
}

// =======================================================
// 🔔 SISTEMA DE NOTIFICACIONES EN TIEMPO REAL
// =======================================================

// Función para actualizar el contador de notificaciones
async function actualizarContadorNotificaciones() {
    const token = localStorage.getItem('token');
    if (!token) return;

    try {
        const res = await fetch(`${API_BASE_URL}/api/notificaciones/contador`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'Cache-Control': 'no-cache',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!res.ok) {
            console.warn('Error al obtener contador de notificaciones:', res.status);
            return;
        }

        const data = await res.json();
        
        if (data.success) {
            const contador = parseInt(data.no_leidas) || 0;
            
            // Actualizar contador en el navbar superior
            const contadorNavbar = document.getElementById('contadorNotificaciones');
            const iconoNavbar = document.getElementById('notificacionesIcono');
            
            if (contadorNavbar) {
                if (contador > 0) {
                    // Mostrar "10+" cuando hay 10 o más notificaciones, o el número exacto si es menor
                    contadorNavbar.textContent = contador >= 10 ? '10+' : contador.toString();
                    contadorNavbar.style.display = 'flex';
                    contadorNavbar.style.visibility = 'visible';
                    contadorNavbar.style.opacity = '1';
                    contadorNavbar.style.backgroundColor = '#dc3545';
                    contadorNavbar.style.color = 'white';
                    contadorNavbar.style.alignItems = 'center';
                    contadorNavbar.style.justifyContent = 'center';
                    
                    // Agregar animación de pulso si hay notificaciones nuevas
                    contadorNavbar.classList.add('pulse-animation');
                    
                    // Asegurar que el ícono sea visible
                    if (iconoNavbar) {
                        iconoNavbar.style.display = 'flex';
                        iconoNavbar.style.visibility = 'visible';
                    }
                } else {
                    contadorNavbar.style.display = 'none';
                    contadorNavbar.style.visibility = 'hidden';
                    contadorNavbar.style.opacity = '0';
                    contadorNavbar.classList.remove('pulse-animation');
                }
            }
            
            // Asegurar que el ícono siempre esté visible
            if (iconoNavbar) {
                iconoNavbar.style.display = 'flex';
                iconoNavbar.style.visibility = 'visible';
            }

            // Badge del sidebar removido según solicitud del usuario
        }
    } catch (error) {
        console.warn('Error actualizando contador de notificaciones:', error);
    }
}

// Inicializar sistema de notificaciones
document.addEventListener('DOMContentLoaded', () => {
    const token = localStorage.getItem('token');
    if (!token) return;

    // Verificar que el usuario es ONG antes de mostrar notificaciones
    const tipoUsuario = localStorage.getItem('tipo_usuario');
    const navItem = document.getElementById('notificacionesNavItem');
    const iconoNavbar = document.getElementById('notificacionesIcono');
    
    if (tipoUsuario !== 'ONG') {
        // Ocultar ícono si no es ONG
        if (navItem) {
            navItem.style.display = 'none';
        }
        if (iconoNavbar) {
            iconoNavbar.style.display = 'none';
        }
        return;
    }

    // Asegurar que el ícono esté visible para ONGs - SIEMPRE VISIBLE
    if (navItem) {
        navItem.style.display = 'flex';
        navItem.style.visibility = 'visible';
        navItem.style.opacity = '1';
    }
    if (iconoNavbar) {
        iconoNavbar.style.display = 'flex';
        iconoNavbar.style.visibility = 'visible';
        iconoNavbar.style.opacity = '1';
    }
    
    // Forzar visibilidad del icono en todas las pantallas
    const notificacionesNavItem = document.getElementById('notificacionesNavItem');
    if (notificacionesNavItem) {
        notificacionesNavItem.style.setProperty('display', 'flex', 'important');
        notificacionesNavItem.style.setProperty('visibility', 'visible', 'important');
        notificacionesNavItem.style.setProperty('opacity', '1', 'important');
    }

    // Cargar contador inicial inmediatamente
    actualizarContadorNotificaciones();

    // También cargar después de un pequeño delay para asegurar que todo esté listo
    setTimeout(actualizarContadorNotificaciones, 500);
    setTimeout(actualizarContadorNotificaciones, 1500);

    // Actualizar contador cada 5 segundos (tiempo real más frecuente)
    setInterval(actualizarContadorNotificaciones, 5000);
    
    // Actualizar cuando la página recupera el foco
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) {
            actualizarContadorNotificaciones();
        }
    });
    
    window.addEventListener('focus', actualizarContadorNotificaciones);
    
    // Forzar visibilidad del icono cada vez que se carga una nueva página
    setTimeout(() => {
        const navItem = document.getElementById('notificacionesNavItem');
        const icono = document.getElementById('notificacionesIcono');
        if (navItem) {
            navItem.style.setProperty('display', 'flex', 'important');
            navItem.style.setProperty('visibility', 'visible', 'important');
            navItem.style.setProperty('opacity', '1', 'important');
        }
        if (icono) {
            icono.style.setProperty('display', 'flex', 'important');
            icono.style.setProperty('visibility', 'visible', 'important');
            icono.style.setProperty('opacity', '1', 'important');
        }
    }, 100);
});

// Asegurar visibilidad del icono cuando se navega entre páginas
window.addEventListener('load', () => {
    const tipoUsuario = localStorage.getItem('tipo_usuario');
    if (tipoUsuario !== 'ONG') return;
    
    // Buscar el icono existente
    let navItem = document.getElementById('notificacionesNavItem');
    let icono = document.getElementById('notificacionesIcono');
    
    // Si no existe, crearlo dinámicamente (para vistas que usan adminlte::page directamente)
    if (!navItem) {
        const navbarNav = document.querySelector('.main-header .navbar-nav');
        if (navbarNav) {
            // Buscar el menú de usuario para insertar antes de él
            const userMenu = navbarNav.querySelector('.nav-item:has(.user-menu), .nav-item:has([data-toggle="dropdown"])');
            
            navItem = document.createElement('li');
            navItem.className = 'nav-item';
            navItem.id = 'notificacionesNavItem';
            navItem.style.cssText = 'display: flex !important; align-items: center; order: 998;';
            
            const link = document.createElement('a');
            link.href = '{{ route("ong.notificaciones.index") }}';
            link.className = 'nav-link position-relative';
            link.id = 'notificacionesIcono';
            link.title = 'Notificaciones';
            link.style.cssText = 'display: flex !important; align-items: center; justify-content: center; padding: 0.5rem 0.75rem !important; min-width: 45px; color: #6c757d !important;';
            
            const bellIcon = document.createElement('i');
            bellIcon.className = 'fas fa-bell';
            bellIcon.style.cssText = 'font-size: 1.25rem !important; display: block !important;';
            
            const badge = document.createElement('span');
            badge.className = 'badge badge-danger position-absolute';
            badge.id = 'contadorNotificaciones';
            badge.style.cssText = 'top: 2px; right: 2px; display: none; font-size: 0.7rem; padding: 4px 7px; min-width: 20px; height: 20px; line-height: 12px; border-radius: 10px; font-weight: bold; z-index: 10; background-color: #dc3545 !important; color: white !important; box-shadow: 0 2px 4px rgba(0,0,0,0.2);';
            badge.textContent = '0';
            
            link.appendChild(bellIcon);
            link.appendChild(badge);
            navItem.appendChild(link);
            
            // Insertar antes del menú de usuario si existe, sino al final
            if (userMenu) {
                navbarNav.insertBefore(navItem, userMenu);
            } else {
                navbarNav.appendChild(navItem);
            }
            
            icono = link;
        }
    }
    
    // Forzar visibilidad
    if (navItem) {
        navItem.style.setProperty('display', 'flex', 'important');
        navItem.style.setProperty('visibility', 'visible', 'important');
        navItem.style.setProperty('opacity', '1', 'important');
    }
    if (icono) {
        icono.style.setProperty('display', 'flex', 'important');
        icono.style.setProperty('visibility', 'visible', 'important');
        icono.style.setProperty('opacity', '1', 'important');
    }
    
    // Actualizar contador si el icono fue creado dinámicamente
    const contador = document.getElementById('contadorNotificaciones');
    if (navItem && contador && !contador.hasAttribute('data-initialized')) {
        setTimeout(() => {
            actualizarContadorNotificaciones();
            contador.setAttribute('data-initialized', 'true');
        }, 500);
    }
});

// Script adicional para asegurar visibilidad en todas las pantallas
(function() {
    const tipoUsuario = localStorage.getItem('tipo_usuario');
    if (tipoUsuario !== 'ONG') return;
    
    function asegurarIconoVisible() {
        let navItem = document.getElementById('notificacionesNavItem');
        let icono = document.getElementById('notificacionesIcono');
        
        if (!navItem) {
            const navbarNav = document.querySelector('.main-header .navbar-nav, .navbar-nav');
            if (navbarNav) {
                const userMenu = navbarNav.querySelector('.user-menu, [data-toggle="dropdown"]')?.closest('.nav-item');
                
                navItem = document.createElement('li');
                navItem.className = 'nav-item';
                navItem.id = 'notificacionesNavItem';
                navItem.style.cssText = 'display: flex !important; align-items: center; order: 998;';
                
                const link = document.createElement('a');
                link.href = '{{ route("ong.notificaciones.index") }}';
                link.className = 'nav-link position-relative';
                link.id = 'notificacionesIcono';
                link.title = 'Notificaciones';
                link.style.cssText = 'display: flex !important; align-items: center; justify-content: center; padding: 0.5rem 0.75rem !important; min-width: 45px; color: #6c757d !important;';
                
                const bellIcon = document.createElement('i');
                bellIcon.className = 'fas fa-bell';
                bellIcon.style.cssText = 'font-size: 1.25rem !important; display: block !important;';
                
                const badge = document.createElement('span');
                badge.className = 'badge badge-danger position-absolute';
                badge.id = 'contadorNotificaciones';
                badge.style.cssText = 'top: 2px; right: 2px; display: none; font-size: 0.7rem; padding: 4px 7px; min-width: 20px; height: 20px; line-height: 12px; border-radius: 10px; font-weight: bold; z-index: 10; background-color: #dc3545 !important; color: white !important; box-shadow: 0 2px 4px rgba(0,0,0,0.2);';
                badge.textContent = '0';
                
                link.appendChild(bellIcon);
                link.appendChild(badge);
                navItem.appendChild(link);
                
                if (userMenu) {
                    navbarNav.insertBefore(navItem, userMenu);
                } else {
                    navbarNav.appendChild(navItem);
                }
            }
        }
        
        if (navItem) {
            navItem.style.setProperty('display', 'flex', 'important');
            navItem.style.setProperty('visibility', 'visible', 'important');
            navItem.style.setProperty('opacity', '1', 'important');
        }
        if (icono || document.getElementById('notificacionesIcono')) {
            const iconoEl = icono || document.getElementById('notificacionesIcono');
            iconoEl.style.setProperty('display', 'flex', 'important');
            iconoEl.style.setProperty('visibility', 'visible', 'important');
            iconoEl.style.setProperty('opacity', '1', 'important');
        }
    }
    
    // Ejecutar inmediatamente y después de que cargue el DOM
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', asegurarIconoVisible);
    } else {
        asegurarIconoVisible();
    }
    
    // También ejecutar después de un pequeño delay para asegurar que AdminLTE haya cargado
    setTimeout(asegurarIconoVisible, 100);
    setTimeout(asegurarIconoVisible, 500);
})();
</script>
@stop

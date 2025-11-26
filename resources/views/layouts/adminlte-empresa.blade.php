{{-- resources/views/layouts/adminlte-empresa.blade.php --}}
{{-- Layout exclusivo para empresas --}}
@extends('adminlte::page')

@section('title', 'UNI2 • Panel de Empresa')

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
            'icon' => 'fas fa-sign-out-alt',
            'label_color' => 'danger',
            'attributes' => [
                'onclick' => 'cerrarSesion(event); return false;'
            ],
        ],
    ]]);
@endphp

@section('content_header')
<div class="d-flex align-items-center justify-content-between">
    <h1 class="mb-0 text-primary">
        <i class="fas fa-building mr-2"></i>
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

@section('css')
<link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
<style>
    /* Estilos específicos para panel de empresa */
    .main-sidebar {
        background-color: #343a40 !important;
    }
    
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
</style>
@stop

@section('js')
<script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
<script src="{{ asset('assets/js/config.js') }}"></script>
<script>
// Verificar que el usuario es empresa y proteger rutas
document.addEventListener('DOMContentLoaded', function() {
    const tipoUsuario = localStorage.getItem('tipo_usuario');
    const currentPath = window.location.pathname;
    
    // Agregar listener para el botón de cerrar sesión en el sidebar
    setTimeout(() => {
        // Buscar todos los enlaces que tengan el atributo onclick con cerrarSesion
        const logoutLinks = document.querySelectorAll('a[onclick*="cerrarSesion"]');
        logoutLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                cerrarSesion(e);
            });
        });
        
        // También buscar por texto en el sidebar
        const sidebarLinks = document.querySelectorAll('.sidebar a, .main-sidebar a, nav.sidebar a');
        sidebarLinks.forEach(link => {
            const text = link.textContent.trim();
            if (text === 'Cerrar sesión' || text.includes('Cerrar sesión')) {
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
@stop


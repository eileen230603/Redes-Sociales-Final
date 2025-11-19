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
    {{-- 🔔 Ícono de Notificaciones Permanente - SIEMPRE VISIBLE PARA ONGs --}}
    <li class="nav-item" id="notificacionesNavItem" style="display: flex !important; align-items: center;">
        <a href="{{ route('ong.notificaciones.index') }}" class="nav-link position-relative" id="notificacionesIcono" title="Notificaciones" style="display: flex !important; align-items: center; justify-content: center; padding: 0.5rem 0.75rem !important; min-width: 45px; color: #6c757d !important;">
            <i class="fas fa-bell" style="font-size: 1.25rem !important; display: block !important;"></i>
            <span class="badge badge-danger position-absolute" id="contadorNotificaciones" style="top: 2px; right: 2px; display: none; font-size: 0.65rem; padding: 3px 6px; min-width: 18px; height: 18px; line-height: 12px; border-radius: 9px; font-weight: bold; z-index: 10; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">0</span>
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
                <p>Notificaciones <span id="badgeNotificaciones" class="badge badge-danger ml-1" style="display: none; font-weight: bold; font-size: 0.75rem; padding: 3px 6px; border-radius: 10px;">0</span></p>
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
        border-radius: 9px;
        font-weight: bold;
        line-height: 12px;
        padding: 3px 6px;
        min-width: 18px;
        height: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    /* Asegurar que el navbar muestre el ícono */
    .navbar-nav .nav-item {
        display: flex;
        align-items: center;
    }

    /* Forzar visibilidad del ícono en todas las pantallas */
    @media (max-width: 576px) {
        #notificacionesIcono {
            display: flex !important;
            padding: 0.4rem 0.6rem !important;
        }
    }
</style>
@stop

{{-- JS --}}
@section('js')
<script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
<script src="{{ asset('js/custom.js') }}"></script>
<script src="{{ asset('assets/js/config.js') }}"></script>
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
                    contadorNavbar.textContent = contador > 99 ? '99+' : contador;
                    contadorNavbar.style.display = 'flex';
                    contadorNavbar.style.visibility = 'visible';
                    contadorNavbar.style.opacity = '1';
                    
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
                    contadorNavbar.classList.remove('pulse-animation');
                }
            }
            
            // Asegurar que el ícono siempre esté visible
            if (iconoNavbar) {
                iconoNavbar.style.display = 'flex';
                iconoNavbar.style.visibility = 'visible';
            }

            // Actualizar contador en el sidebar (badge personalizado)
            const badgeSidebar = document.getElementById('badgeNotificaciones');
            if (badgeSidebar) {
                if (contador > 0) {
                    badgeSidebar.textContent = contador > 99 ? '99+' : contador;
                    badgeSidebar.style.display = 'inline-block';
                    badgeSidebar.style.visibility = 'visible';
                } else {
                    badgeSidebar.style.display = 'none';
                    badgeSidebar.style.visibility = 'hidden';
                }
            }

            // Actualizar badge en el menú de AdminLTE (si existe)
            // Buscar el elemento del menú de notificaciones generado por AdminLTE
            const menuItems = document.querySelectorAll('.nav-sidebar .nav-item');
            menuItems.forEach(item => {
                const link = item.querySelector('a[href*="notificaciones"]');
                if (link) {
                    // Buscar o crear el badge en el menú de AdminLTE
                    let adminLteBadge = link.querySelector('.badge');
                    if (!adminLteBadge && contador > 0) {
                        // Crear badge si no existe
                        const pTag = link.querySelector('p');
                        if (pTag) {
                            adminLteBadge = document.createElement('span');
                            adminLteBadge.className = 'badge badge-danger badge-sm ml-1';
                            adminLteBadge.style.cssText = 'display: inline-block; font-weight: bold;';
                            pTag.appendChild(adminLteBadge);
                        }
                    }
                    if (adminLteBadge) {
                        if (contador > 0) {
                            adminLteBadge.textContent = contador > 99 ? '99+' : contador;
                            adminLteBadge.style.display = 'inline-block';
                            adminLteBadge.style.visibility = 'visible';
                        } else {
                            adminLteBadge.style.display = 'none';
                            adminLteBadge.style.visibility = 'hidden';
                        }
                    }
                }
            });
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

    // Asegurar que el ícono esté visible para ONGs
    if (navItem) {
        navItem.style.display = 'flex';
        navItem.style.visibility = 'visible';
    }
    if (iconoNavbar) {
        iconoNavbar.style.display = 'flex';
        iconoNavbar.style.visibility = 'visible';
    }

    // Cargar contador inicial
    actualizarContadorNotificaciones();

    // Actualizar contador cada 10 segundos (tiempo real)
    setInterval(actualizarContadorNotificaciones, 10000);
});
</script>
@stop

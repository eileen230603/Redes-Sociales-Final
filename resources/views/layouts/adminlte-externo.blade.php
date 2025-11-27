{{-- resources/views/layouts/adminlte-externo.blade.php --}}
{{-- Layout exclusivo para usuarios externos (Integrantes Externos) --}}
@extends('adminlte::page')

@section('title', 'UNI2 • Panel del Integrante Externo')

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
    <li class="nav-item d-none d-sm-inline-block">
        <a href="/home-publica" class="nav-link">
            <i class="fas fa-globe-americas mr-1"></i> Ir a página pública
        </a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
        <a href="#" class="nav-link text-danger logout-link" onclick="event.preventDefault(); event.stopPropagation(); cerrarSesion(event); return false;">
            <i class="far fa-sign-out-alt mr-1"></i> Cerrar sesión
        </a>
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
</style>
@endpush

@push('js')
<script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
<script src="{{ asset('assets/js/config.js') }}"></script>
<script>
// Cargar nombre del usuario al iniciar
document.addEventListener('DOMContentLoaded', function() {
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
</script>
@endpush

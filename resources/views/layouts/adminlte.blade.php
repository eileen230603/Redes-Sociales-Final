{{-- resources/views/layouts/adminlte.blade.php --}}
@extends('adminlte::page')

@section('title', 'UNI2 • Panel Principal')

@push('js')
    {{-- Lucide icons: reemplazar Font Awesome visualmente en todo el panel (opcional) --}}
    <script type="module">
        (async () => {
            try {
                const { createIcons, icons } = await import("https://unpkg.com/lucide@latest/dist/esm/lucide.js").catch(() => {
                    // Si falla la carga, retornar null para que se use Font Awesome
                    return null;
                });

                if (!icons || !createIcons) {
                    // Si no se pudo cargar, continuar con Font Awesome
                    return;
                }

                const faToLucide = {
                    // Generales
                    'fa-home': 'home',
                    'fa-layer-group': 'layout-dashboard',
                    'fa-bell': 'bell',
                    'fa-globe': 'globe-2',
                    'fa-sign-out-alt': 'log-out',
                    'fa-users': 'users',
                    'fa-user-circle': 'user-round',
                    'fa-tachometer-alt': 'gauge',
                    'fa-chart-bar': 'bar-chart-3',
                    'fa-info-circle': 'info',
                    'fa-exclamation-triangle': 'alert-triangle',
                    'fa-hand-holding-heart': 'hand-heart',

                    // Calendario / eventos
                    'fa-calendar': 'calendar',
                    'fa-calendar-alt': 'calendar-range',
                    'fa-calendar-check': 'calendar-check',
                    'fa-calendar-plus': 'calendar-plus',
                    'fa-list': 'list',
                    'fa-angle-left': 'chevron-left',
                    'fa-history': 'history',

                    // Iconos de UI varios
                    'fa-heart': 'heart',
                    'fa-star': 'star',
                    'fa-share-alt': 'share-2',
                    'fa-images': 'images',
                    'fa-map-marker-alt': 'map-pin',
                    'fa-user-plus': 'user-plus',
                    'fa-check-circle': 'check-circle-2',
                    'fa-times-circle': 'x-circle',
                    'fa-phone': 'phone',
                    'fa-envelope': 'mail',
                    'fa-clock': 'clock',
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
                            // Quitamos las clases de icono de FA, pero dejamos espaciados (mr-2, etc.)
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

{{-- El layout usa completamente la configuración de config/adminlte.php --}}

{{-- HEADER --}}
@section('content_header')
<div class="d-flex align-items-center justify-content-between" style="margin-bottom: 0.5rem;">
    <h1 class="mb-0 text-primary">
        <i class="far fa-layer-group mr-2"></i>
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

{{-- NAVBAR SUPERIOR (lado derecho) --}}
@section('content_top_nav_right')
    {{-- Solo avatar + nombre de usuario, alineado a la derecha --}}
    <li class="nav-item d-flex align-items-center ml-auto">
        {{-- Mini perfil del usuario ONG --}}
        <div class="d-flex align-items-center pl-3 pr-3"
             style="background: #ffffff; border-radius: 999px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); padding: 4px 12px;">
            @php
                $user = Auth::user();
                $ong  = optional($user)->ong;
                $nombreUsuario = $ong->nombre_ong ?? ($user->nombre_usuario ?? ($user->name ?? 'Usuario'));
                $inicial = mb_substr(trim($nombreUsuario), 0, 1, 'UTF-8');
                // Prioridad de avatar: foto de ONG > foto de usuario > null
                $foto = $ong->foto_perfil_url ?? ($user->foto_perfil_url ?? null);
            @endphp
            <div class="rounded-circle d-flex align-items-center justify-content-center mr-2"
                 style="width: 32px; height: 32px; overflow: hidden; background: #e9f5ff; font-weight: 600; color: #0C2B44;">
                <img id="headerAvatarOng"
                     src="{{ $foto ?? '' }}"
                     alt="Foto perfil"
                     style="width: 100%; height: 100%; object-fit: cover; {{ $foto ? '' : 'display:none;' }}">
                <span id="headerAvatarInicialOng"
                      style="font-size: 0.95rem; {{ $foto ? 'display:none;' : '' }}">{{ $inicial }}</span>
            </div>
            <div class="d-flex flex-column mr-2" style="max-width: 160px;">
                <span id="headerNombreOng"
                      style="font-size: 0.9rem; color: #2c3e50; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    {{ $nombreUsuario }}
                </span>
                @if($ong && $ong->nombre_ong)
                    <small style="font-size: 0.75rem; color: #8c9aa8; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        ONG
                    </small>
                @endif
            </div>
            <div class="dropdown">
                <a class="text-muted dropdown-toggle" href="#" role="button" id="dropdownPerfilOng"
                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                   style="font-size: 0.75rem;">
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownPerfilOng">
                    <a class="dropdown-item" href="/perfil/ong">
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
@endsection

{{-- SIDEBAR --}}
{{-- El menú se genera automáticamente desde config/adminlte.php --}}

{{-- CSS --}}
@section('css')
<link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
<style>
    /* ============================================
       NUEVA PALETA DE COLORES - AZUL MARINO Y VERDE ESMERALDA
       DEFINIDA SOLO EN ESTE LAYOUT
       ============================================ */
    :root {
        --brand-primario: #0C2B44;   /* Azul Marino */
        --brand-acento: #00A36C;     /* Verde Esmeralda */
        --brand-blanco: #FFFFFF;     /* Blanco Puro */
        --brand-gris-oscuro: #333333;/* Gris Carbón */
        --brand-gris-suave: #F5F5F5; /* Gris Suave */
    }

    /* Helper para clases bg-brand-primario (logo, usermenu, etc.) */
    .bg-brand-primario {
        background-color: var(--brand-primario) !important;
        color: #ffffff !important;
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
    
    /* Override de bg-primary en sidebar */
    .main-sidebar .bg-primary,
    .sidebar-dark-primary .bg-primary {
        background-color: #0C2B44 !important;
    }
    
    /* Animación de entrada del sidebar */
    @keyframes slideInLeft {
        from {
            transform: translateX(-100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    /* Animación de entrada de elementos del menú */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Animación de activación del elemento del menú */
    @keyframes menuItemActivate {
        0% {
            background-color: rgba(0, 163, 108, 0);
            transform: scale(1);
        }
        50% {
            transform: scale(1.02);
        }
        100% {
            background-color: #00A36C;
            transform: scale(1);
        }
    }
    
    /* Animación de pulso para elementos activos */
    @keyframes activePulse {
        0%, 100% {
            box-shadow: 0 2px 8px rgba(0, 163, 108, 0.3);
        }
        50% {
            box-shadow: 0 2px 12px rgba(0, 163, 108, 0.5);
        }
    }
    
    /* Aplicar animación de entrada al sidebar */
    .main-sidebar {
        animation: slideInLeft 0.5s ease-out !important;
    }
    
    /* Animación de entrada escalonada para elementos del menú */
    .nav-sidebar > .nav-item {
        animation: fadeInUp 0.4s ease-out backwards;
    }
    
    .nav-sidebar > .nav-item:nth-child(1) { animation-delay: 0.1s; }
    .nav-sidebar > .nav-item:nth-child(2) { animation-delay: 0.15s; }
    .nav-sidebar > .nav-item:nth-child(3) { animation-delay: 0.2s; }
    .nav-sidebar > .nav-item:nth-child(4) { animation-delay: 0.25s; }
    .nav-sidebar > .nav-item:nth-child(5) { animation-delay: 0.3s; }
    .nav-sidebar > .nav-item:nth-child(6) { animation-delay: 0.35s; }
    .nav-sidebar > .nav-item:nth-child(7) { animation-delay: 0.4s; }
    .nav-sidebar > .nav-item:nth-child(8) { animation-delay: 0.45s; }
    .nav-sidebar > .nav-item:nth-child(9) { animation-delay: 0.5s; }
    
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
    
    /* Efecto de brillo al pasar el mouse */
    .sidebar-dark-primary .nav-sidebar .nav-link::before,
    .main-sidebar .nav-sidebar .nav-link::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        transition: left 0.5s;
    }
    
    .sidebar-dark-primary .nav-sidebar .nav-link:hover::before,
    .main-sidebar .nav-sidebar .nav-link:hover::before {
        left: 100%;
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
        animation: menuItemActivate 0.5s ease-out, activePulse 2s ease-in-out infinite !important;
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
    
    /* Iconos del Sidebar con animación */
    .sidebar-dark-primary .nav-sidebar .nav-link .nav-icon,
    .main-sidebar .nav-sidebar .nav-link .nav-icon {
        color: rgba(255, 255, 255, 0.7) !important;
        margin-right: 0.75rem !important;
        transition: all 0.3s ease !important;
        display: inline-block !important;
    }
    
    .sidebar-dark-primary .nav-sidebar .nav-link.active .nav-icon,
    .sidebar-dark-primary .nav-sidebar .nav-link:hover .nav-icon,
    .main-sidebar .nav-sidebar .nav-link.active .nav-icon,
    .main-sidebar .nav-sidebar .nav-link:hover .nav-icon {
        color: white !important;
        transform: scale(1.1) rotate(5deg) !important;
    }
    
    /* Animación de rotación para iconos al activar */
    @keyframes iconBounce {
        0%, 100% {
            transform: scale(1) rotate(0deg);
        }
        25% {
            transform: scale(1.15) rotate(-5deg);
        }
        75% {
            transform: scale(1.15) rotate(5deg);
        }
    }
    
    .sidebar-dark-primary .nav-sidebar .nav-link.active .nav-icon,
    .main-sidebar .nav-sidebar .nav-link.active .nav-icon {
        animation: iconBounce 0.6s ease-out !important;
    }
    
    /* Iconos con clases de color (text-primary, text-success) */
    .sidebar-dark-primary .nav-sidebar .nav-link .text-primary,
    .main-sidebar .nav-sidebar .nav-link .text-primary {
        color: #00A36C !important;
    }
    
    .sidebar-dark-primary .nav-sidebar .nav-link.active .text-primary,
    .main-sidebar .nav-sidebar .nav-link.active .text-primary {
        color: white !important;
    }
    
    .sidebar-dark-primary .nav-sidebar .nav-link .text-success,
    .main-sidebar .nav-sidebar .nav-link .text-success {
        color: #00A36C !important;
    }
    
    .sidebar-dark-primary .nav-sidebar .nav-link.active .text-success,
    .main-sidebar .nav-sidebar .nav-link.active .text-success {
        color: white !important;
    }
    
    /* Animación de apertura de submenús */
    @keyframes slideDown {
        from {
            opacity: 0;
            max-height: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            max-height: 500px;
            transform: translateY(0);
        }
    }
    
    /* Submenús (Treeview) */
    .sidebar-dark-primary .nav-treeview,
    .main-sidebar .nav-treeview {
        background-color: rgba(0, 0, 0, 0.1) !important;
        border-radius: 8px !important;
        margin: 0.5rem 0 !important;
        padding: 0.5rem 0 !important;
        animation: slideDown 0.4s ease-out !important;
        overflow: hidden !important;
    }
    
    .sidebar-dark-primary .nav-treeview .nav-link,
    .main-sidebar .nav-treeview .nav-link {
        padding-left: 2.5rem !important;
        font-size: 0.9rem !important;
        transition: all 0.3s ease !important;
        position: relative !important;
    }
    
    .sidebar-dark-primary .nav-treeview .nav-link::before,
    .main-sidebar .nav-treeview .nav-link::before {
        content: '';
        position: absolute;
        left: 1.5rem;
        top: 50%;
        transform: translateY(-50%);
        width: 6px;
        height: 6px;
        background-color: rgba(255, 255, 255, 0.4);
        border-radius: 50%;
        transition: all 0.3s ease;
    }
    
    .sidebar-dark-primary .nav-treeview .nav-link:hover::before,
    .main-sidebar .nav-treeview .nav-link:hover::before {
        background-color: #00A36C;
        transform: translateY(-50%) scale(1.5);
    }
    
    .sidebar-dark-primary .nav-treeview .nav-link.active,
    .main-sidebar .nav-treeview .nav-link.active {
        background-color: rgba(0, 163, 108, 0.3) !important;
        border-left: 3px solid #00A36C !important;
        transform: translateX(4px) !important;
        font-weight: 600 !important;
    }
    
    .sidebar-dark-primary .nav-treeview .nav-link.active::before,
    .main-sidebar .nav-treeview .nav-link.active::before {
        background-color: #00A36C;
        transform: translateY(-50%) scale(1.5);
    }
    
    .sidebar-dark-primary .nav-treeview .nav-link:hover:not(.active),
    .main-sidebar .nav-treeview .nav-link:hover:not(.active) {
        background-color: rgba(0, 163, 108, 0.15) !important;
        transform: translateX(4px) !important;
    }
    
    /* Headers de Sección */
    .sidebar-dark-primary .nav-header,
    .main-sidebar .nav-header {
        color: rgba(255, 255, 255, 0.6) !important;
        font-size: 0.75rem !important;
        font-weight: 600 !important;
        text-transform: uppercase !important;
        letter-spacing: 1px !important;
        padding: 1rem 1rem 0.5rem 1rem !important;
        margin-top: 1rem !important;
    }
    
    /* Separador después de header */
    .sidebar-dark-primary .nav-header + .nav-item,
    .main-sidebar .nav-header + .nav-item {
        margin-top: 0.5rem !important;
    }
    
    /* Animación de rotación para flechas */
    @keyframes rotateArrow {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(-90deg);
        }
    }
    
    /* Flecha de Treeview */
    .sidebar-dark-primary .nav-link .right,
    .main-sidebar .nav-link .right {
        color: rgba(255, 255, 255, 0.6) !important;
        transition: all 0.3s ease !important;
        display: inline-block !important;
    }
    
    .sidebar-dark-primary .nav-item.menu-open > .nav-link .right,
    .main-sidebar .nav-item.menu-open > .nav-link .right {
        transform: rotate(-90deg) !important;
        color: white !important;
    }
    
    .sidebar-dark-primary .nav-link.active .right,
    .sidebar-dark-primary .nav-link:hover .right,
    .main-sidebar .nav-link.active .right,
    .main-sidebar .nav-link:hover .right {
        color: white !important;
    }
    
    .sidebar-dark-primary .nav-link:hover .right,
    .main-sidebar .nav-link:hover .right {
        transform: translateX(2px) !important;
    }
    
    /* Enlace de Cerrar Sesión */
    .sidebar-dark-primary .nav-link.text-danger,
    .main-sidebar .nav-link.text-danger {
        color: rgba(220, 53, 69, 0.8) !important;
    }
    
    .sidebar-dark-primary .nav-link.text-danger:hover,
    .main-sidebar .nav-link.text-danger:hover {
        background-color: rgba(220, 53, 69, 0.2) !important;
        color: #dc3545 !important;
    }
    
    /* Override de AdminLTE para asegurar colores */
    .sidebar-dark-primary.nav-sidebar .nav-item .nav-link {
        color: rgba(255, 255, 255, 0.85) !important;
    }
    
    /* Asegurar que el sidebar tenga el fondo correcto */
    body .main-sidebar.sidebar-dark-primary {
        background-color: #0C2B44 !important;
    }
    
    /* Menu abierto (has-treeview menu-open) */
    .sidebar-dark-primary .nav-item.menu-open > .nav-link,
    .main-sidebar .nav-item.menu-open > .nav-link {
        background-color: rgba(0, 163, 108, 0.1) !important;
        color: white !important;
    }
    
    /* Scrollbar del Sidebar */
    .main-sidebar::-webkit-scrollbar {
        width: 6px !important;
    }
    
    .main-sidebar::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.1) !important;
    }
    
    .main-sidebar::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.2) !important;
        border-radius: 3px !important;
    }
    
    .main-sidebar::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.3) !important;
    }

    /* Navbar Superior - Nueva Paleta */
    .main-header {
        background-color: white !important;
        border-bottom: 2px solid var(--brand-gris-suave) !important;
        box-shadow: 0 2px 4px rgba(12, 43, 68, 0.08) !important;
    }
    
    .navbar-primary,
    .main-header .navbar {
        background-color: white !important;
        border-bottom: 2px solid var(--brand-gris-suave) !important;
    }
    
    .navbar-primary .navbar-nav .nav-link,
    .main-header .navbar-nav .nav-link {
        color: var(--brand-gris-oscuro) !important;
        font-weight: 500 !important;
        padding: 0.75rem 1rem !important;
        border-radius: 8px !important;
        transition: all 0.3s ease !important;
    }
    
    .navbar-primary .navbar-nav .nav-link:hover,
    .main-header .navbar-nav .nav-link:hover {
        color: var(--brand-primario) !important;
        background-color: rgba(12, 43, 68, 0.05) !important;
    }
    
    /* Icono de Notificaciones en Navbar */
    .main-header #notificacionesNavItem .nav-link {
        color: var(--brand-gris-oscuro) !important;
    }
    
    .main-header #notificacionesNavItem .nav-link:hover {
        color: var(--brand-primario) !important;
        background-color: rgba(12, 43, 68, 0.05) !important;
    }
    
    .main-header #notificacionesNavItem .nav-link i {
        color: var(--brand-gris-oscuro) !important;
    }
    
    .main-header #notificacionesNavItem .nav-link:hover i {
        color: var(--brand-primario) !important;
    }
    
    /* Badge de Notificaciones */
    .main-header #contadorNotificaciones {
        background-color: #dc3545 !important;
        color: white !important;
    }

    /* Animación de entrada del contenido */
    @keyframes fadeInContent {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Aplicar animación al contenido principal */
    .content-wrapper > .content {
        animation: fadeInContent 0.5s ease-out !important;
    }
    
    /* Header de contenido fijo (sticky) para todas las pantallas ONG */
    .content-header {
        position: sticky;
        top: 56px; /* altura aproximada del navbar */
        z-index: 1029;
        background-color: #f5f5f5;
        padding-top: 0.75rem;
        padding-bottom: 0.5rem;
        animation: fadeInContent 0.4s ease-out !important;
    }

    .content-header h1 {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--brand-primario);
        margin: 0;
    }

    .content-header h1 i {
        color: var(--brand-acento);
        transition: transform 0.3s ease !important;
    }
    
    .content-header h1:hover i {
        transform: rotate(15deg) scale(1.1) !important;
    }
    
    /* User Menu en Navbar */
    .main-header .user-menu {
        color: var(--brand-gris-oscuro) !important;
    }
    
    .main-header .user-menu:hover {
        color: var(--brand-primario) !important;
        background-color: rgba(12, 43, 68, 0.05) !important;
    }

    /* Header de Contenido */
    .content-header h1 {
        color: var(--brand-primario) !important;
        font-weight: 700 !important;
    }
    
    .content-header h1 i {
        color: var(--brand-acento) !important;
    }

    /* Cards - Mejor Espaciado */
    .card {
        border-radius: 12px !important;
        border: none !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
        margin-bottom: 1.5rem !important;
        transition: all 0.3s ease !important;
    }
    
    .card:hover {
        box-shadow: 0 4px 16px rgba(12, 43, 68, 0.12) !important;
        transform: translateY(-2px) !important;
    }
    
    .card-body {
        padding: 1.5rem !important;
    }
    
    .card-header {
        padding: 1.25rem 1.5rem !important;
        background-color: white !important;
        border-bottom: 1px solid var(--brand-gris-suave) !important;
        border-radius: 12px 12px 0 0 !important;
    }
    
    .card-header h3, .card-header h5 {
        color: var(--brand-gris-oscuro) !important;
        font-weight: 600 !important;
        margin: 0 !important;
    }

    /* Botones */
    .btn-primary {
        background-color: var(--brand-primario) !important;
        border-color: var(--brand-primario) !important;
        border-radius: 8px !important;
        padding: 0.5rem 1.25rem !important;
        font-weight: 500 !important;
        transition: all 0.3s ease !important;
    }
    
    .btn-primary:hover {
        background-color: #0a2338 !important;
        border-color: #0a2338 !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 12px rgba(12, 43, 68, 0.3) !important;
    }
    
    .btn-success {
        background-color: var(--brand-acento) !important;
        border-color: var(--brand-acento) !important;
        border-radius: 8px !important;
    }
    
    .btn-success:hover {
        background-color: #008a5a !important;
        border-color: #008a5a !important;
    }
    
    .btn-outline-primary {
        color: var(--brand-primario) !important;
        border-color: var(--brand-primario) !important;
        border-radius: 8px !important;
    }
    
    .btn-outline-primary:hover {
        background-color: var(--brand-primario) !important;
        color: white !important;
    }

    /* Badges */
    .badge-primary {
        background-color: var(--brand-primario) !important;
    }
    
    .badge-success {
        background-color: var(--brand-acento) !important;
    }

    /* Tabs */
    .nav-tabs {
        border-bottom: 2px solid var(--brand-gris-suave) !important;
    }
    
    .nav-tabs .nav-link {
        border: none !important;
        border-bottom: 3px solid transparent !important;
        color: var(--brand-gris-oscuro) !important;
        font-weight: 500 !important;
        padding: 0.75rem 1.25rem !important;
        margin-right: 0.5rem !important;
    }
    
    .nav-tabs .nav-link:hover {
        border-bottom-color: var(--brand-gris-suave) !important;
        color: var(--brand-primario) !important;
    }
    
    .nav-tabs .nav-link.active {
        border-bottom-color: var(--brand-acento) !important;
        color: var(--brand-primario) !important;
        background: transparent !important;
        font-weight: 600 !important;
    }

    /* Tablas */
    .table {
        border-radius: 8px !important;
        overflow: hidden !important;
    }
    
    .table thead th {
        background-color: var(--brand-gris-suave) !important;
        color: var(--brand-gris-oscuro) !important;
        font-weight: 600 !important;
        text-transform: uppercase !important;
        font-size: 0.75rem !important;
        letter-spacing: 0.5px !important;
        padding: 1rem !important;
        border: none !important;
    }
    
    .table tbody td {
        padding: 1rem !important;
        vertical-align: middle !important;
        border-top: 1px solid var(--brand-gris-suave) !important;
    }
    
    .table tbody tr:hover {
        background-color: rgba(12, 43, 68, 0.02) !important;
    }

    /* Iconos - Más Visibles */
    .nav-icon, .fas, .far, .fab {
        font-size: 1.1rem !important;
        width: 20px !important;
        text-align: center !important;
    }
    
    .card-body i.fa-3x {
        opacity: 0.15 !important;
    }

    /* Contenedores con Mejor Espaciado */
    .container-fluid {
        padding: 1.5rem !important;
    }
    
    .row {
        margin-left: -0.75rem !important;
        margin-right: -0.75rem !important;
    }
    
    .row > [class*="col-"] {
        padding-left: 0.75rem !important;
        padding-right: 0.75rem !important;
    }

    /* Formularios */
    .form-control {
        border-radius: 8px !important;
        border: 1px solid #dee2e6 !important;
        padding: 0.625rem 1rem !important;
        transition: all 0.3s ease !important;
    }
    
    .form-control:focus {
        border-color: var(--brand-acento) !important;
        box-shadow: 0 0 0 0.2rem rgba(0, 163, 108, 0.15) !important;
    }

    /* Espaciado General Mejorado */
    .mb-4 {
        margin-bottom: 2rem !important;
    }
    
    .mt-4 {
        margin-top: 2rem !important;
    }
    
    .p-4 {
        padding: 2rem !important;
    }

    /* Gradientes con Nueva Paleta */
    .bg-gradient-primary {
        background: linear-gradient(135deg, var(--brand-primario) 0%, #0a2338 100%) !important;
    }
    
    .bg-gradient-success {
        background: linear-gradient(135deg, var(--brand-acento) 0%, #008a5a 100%) !important;
    }
    
    .bg-gradient-primary-accent {
        background: linear-gradient(135deg, var(--brand-primario) 0%, var(--brand-acento) 100%) !important;
    }

    /* Notificaciones */
    #notificacionesIcono:hover {
        background-color: rgba(12, 43, 68, 0.1) !important;
        color: var(--brand-primario) !important;
    }
    
    #contadorNotificaciones {
        background-color: var(--brand-acento) !important;
    }
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
@if(file_exists(public_path('js/custom.js')))
<script src="{{ asset('js/custom.js') }}?v={{ time() }}"></script>
@endif
<script src="{{ asset('assets/js/config.js') }}"></script>
{{-- Script global para icono de notificaciones en todas las pantallas ONG --}}
<script src="{{ asset('js/notificaciones-ong.js') }}"></script>
<script src="{{ asset('assets/js/ong/alertas-eventos-proximos.js') }}"></script>
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
                    // Formatear número estilo TikTok (mostrar "999+" si es mayor a 999)
                    const mostrarNumero = contador > 999 ? '999+' : contador.toString();
                    contadorNavbar.textContent = mostrarNumero;
                    contadorNavbar.style.display = 'flex';
                    contadorNavbar.style.visibility = 'visible';
                    contadorNavbar.style.opacity = '1';
                    // Aplicar estilo TikTok
                    contadorNavbar.style.background = 'linear-gradient(135deg, #ff0050 0%, #ff4081 100%)';
                    contadorNavbar.style.color = 'white';
                    contadorNavbar.style.fontWeight = '900';
                    contadorNavbar.style.fontSize = '0.85rem';
                    contadorNavbar.style.boxShadow = '0 3px 8px rgba(255, 0, 80, 0.5), 0 0 0 2px white';
                    contadorNavbar.style.border = '2px solid white';
                    contadorNavbar.style.top = '-8px';
                    contadorNavbar.style.right = '-8px';
                    contadorNavbar.style.minWidth = '22px';
                    contadorNavbar.style.height = '22px';
                    contadorNavbar.style.borderRadius = '11px';
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

// =======================================================
// 🎨 ANIMACIONES DE TRANSICIÓN DE PÁGINA
// =======================================================

// Agregar clase de transición al hacer clic en enlaces del sidebar
document.addEventListener('DOMContentLoaded', () => {
    const sidebarLinks = document.querySelectorAll('.nav-sidebar .nav-link[href]');
    
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Solo aplicar si no es un enlace que abre un submenú
            if (!this.closest('.has-treeview') || this.getAttribute('href') !== '#') {
                // Agregar clase de transición al contenido
                const contentWrapper = document.querySelector('.content-wrapper > .content');
                if (contentWrapper) {
                    contentWrapper.style.opacity = '0';
                    contentWrapper.style.transform = 'translateY(20px)';
                    contentWrapper.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                }
                
                // Restaurar después de un breve delay (para permitir la navegación)
                setTimeout(() => {
                    if (contentWrapper) {
                        contentWrapper.style.opacity = '1';
                        contentWrapper.style.transform = 'translateY(0)';
                    }
                }, 100);
            }
        });
    });
    
    // Animar elementos del menú al cargar
    const menuItems = document.querySelectorAll('.nav-sidebar > .nav-item');
    menuItems.forEach((item, index) => {
        item.style.animationDelay = `${index * 0.05}s`;
    });
    
    // Función para manejar el toggle de menús (Eventos y Mega Eventos)
    function toggleMenu(menuItem) {
        const treeview = menuItem.querySelector('.nav-treeview');
        if (treeview) {
            if (menuItem.classList.contains('menu-open')) {
                // Colapsar
                menuItem.classList.remove('menu-open');
                treeview.style.display = 'none';
            } else {
                // Expandir
                menuItem.classList.add('menu-open');
                treeview.style.display = 'block';
            }
        }
    }
    
    // Inicializar los menús con toggle después de que AdminLTE cargue
    function inicializarMenusToggle() {
        // Buscar todos los menús con submenús (Eventos y Mega Eventos)
        const menuItems = document.querySelectorAll('.nav-sidebar .nav-item.has-treeview');
        
        menuItems.forEach(item => {
            const link = item.querySelector('.nav-link');
            if (link && link.getAttribute('href') === '#') {
                // Remover listeners anteriores si existen
                const newLink = link.cloneNode(true);
                link.parentNode.replaceChild(newLink, link);
                
                // Agregar evento de clic para toggle
                newLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    toggleMenu(item);
                });
            }
        });
    }
    
    // Inicializar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(inicializarMenusToggle, 300);
        });
    } else {
        setTimeout(inicializarMenusToggle, 300);
    }
    
    // También inicializar después de que AdminLTE haya renderizado completamente
    window.addEventListener('load', function() {
        setTimeout(inicializarMenusToggle, 500);
    });
    
    // Expandir automáticamente el menú "Eventos" cuando se está en home-ong
    if (window.location.pathname === '/home-ong' || window.location.pathname === '/home-ong/') {
        setTimeout(() => {
            const eventosMenuItems = document.querySelectorAll('.nav-sidebar .nav-item.has-treeview');
            eventosMenuItems.forEach(item => {
                const link = item.querySelector('.nav-link');
                if (link) {
                    const text = link.textContent.trim();
                    // Buscar el menú que contiene "Eventos" y tiene submenú (pero no "Mega Eventos")
                    if (text.includes('Eventos') && !text.includes('Mega')) {
                        // Expandir automáticamente
                        item.classList.add('menu-open');
                        const treeview = item.querySelector('.nav-treeview');
                        if (treeview) {
                            treeview.style.display = 'block';
                        }
                    }
                }
            });
        }, 800);
    }
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
            bellIcon.className = 'far fa-bell';
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

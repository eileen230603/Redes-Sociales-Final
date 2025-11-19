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
                <p>Notificaciones <span id="badgeNotificaciones" class="badge badge-danger badge-sm ml-1" style="display: none;">0</span></p>
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

// Cargar contador de notificaciones no leídas
document.addEventListener('DOMContentLoaded', async () => {
    const token = localStorage.getItem('token');
    if (!token) return;

    try {
        const res = await fetch(`${API_BASE_URL}/api/notificaciones`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });
        const data = await res.json();
        if (data.success && data.no_leidas > 0) {
            const badge = document.getElementById('badgeNotificaciones');
            if (badge) {
                badge.textContent = data.no_leidas;
                badge.style.display = 'inline-block';
            }
        }
    } catch (error) {
        console.warn('Error cargando notificaciones:', error);
    }
});
</script>
@stop

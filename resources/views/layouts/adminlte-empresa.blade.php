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
            'text' => 'Reportes',
            'url'  => '/empresa/reportes',
            'icon' => 'fas fa-chart-bar',
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
</script>
@stop


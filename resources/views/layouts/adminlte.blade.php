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
        <a href="/login" onclick="localStorage.clear()" class="nav-link text-danger">
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
            <a href="#" class="nav-link">
                <i class="nav-icon fas fa-users"></i>
                <p>Voluntarios</p>
            </a>
        </li>

        {{-- 📊 Reportes --}}
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="nav-icon fas fa-chart-bar"></i>
                <p>Reportes</p>
            </a>
        </li>

        {{-- ⚙️ Configuraciones --}}
        <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
                <i class="nav-icon fas fa-cogs"></i>
                <p>
                    Configuraciones
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-user-cog nav-icon"></i>
                        <p>Perfil</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-sliders-h nav-icon"></i>
                        <p>Parámetros</p>
                    </a>
                </li>
            </ul>
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
            <a href="/login" onclick="localStorage.clear()" class="nav-link text-danger">
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
@stop

{{-- resources/views/layouts/adminlte-externo.blade.php --}}
@extends('adminlte::page')

@section('title', 'UNI2 • Panel del Integrante Externo')

@section('content_header')
<div class="d-flex align-items-center justify-content-between">
    <h1 class="mb-0 text-primary">
        <i class="fas fa-user-friends mr-2"></i>
        @yield('page_title', 'Panel del Integrante Externo')
    </h1>
</div>
@stop

@section('content')
<div class="container-fluid">
    @yield('content_body')
</div>
@stop

@push('adminlte_sidebar')
<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column">

        <li class="nav-item">
            <a href="/home-externo" class="nav-link {{ request()->is('home-externo') ? 'active' : '' }}">
                <i class="nav-icon fas fa-home"></i>
                <p>Inicio</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="/externo/eventos" class="nav-link {{ request()->is('externo/eventos') ? 'active' : '' }}">
                <i class="nav-icon fas fa-calendar-alt"></i>
                <p>Eventos</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="/externo/reportes" class="nav-link">
                <i class="nav-icon fas fa-chart-bar"></i>
                <p>Reportes</p>
            </a>
        </li>

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

@section('css')
<link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
@stop

@section('js')
<script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
@stop

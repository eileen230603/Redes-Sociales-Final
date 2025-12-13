<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class ApiRouteServiceProvider extends ServiceProvider
{
    /**
     * Register API routes manually (forcing Laravel to load routes/api.php).
     * NOTA: Las rutas API ahora se cargan desde bootstrap/app.php
     * Este método se mantiene vacío para evitar duplicación
     */
    public function boot(): void
    {
        // Las rutas API se cargan desde bootstrap/app.php con withRouting()
        // No cargar aquí para evitar duplicación
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    /**
     * Mostrar vista principal de configuración
     */
    public function index()
    {
        return view('ong.configuracion.index');
    }
}

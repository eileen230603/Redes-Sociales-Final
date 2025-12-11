<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use Illuminate\Http\Request;

class EventoPublicoController extends Controller
{
    /**
     * Mostrar evento público mediante QR (sin autenticación)
     */
    public function show($id)
    {
        try {
            $evento = Evento::with('ong')->find($id);

            if (!$evento) {
                return view('evento-publico.error', [
                    'mensaje' => 'Evento no encontrado'
                ]);
            }

            // Agregar información del creador (ONG)
            if ($evento->ong) {
                $evento->creador = [
                    'tipo' => 'ONG',
                    'nombre' => $evento->ong->nombre_ong ?? 'ONG',
                    'foto_perfil' => $evento->ong->foto_perfil_url ?? null,
                    'id' => $evento->ong->user_id ?? null
                ];
            } else {
                $evento->creador = null;
            }

            // Hacer visible las imágenes para que el accessor funcione
            $evento->makeVisible('imagenes');

            // Procesar imágenes para usar IP fija (accesible desde cualquier dispositivo)
            $baseUrl = 'http://10.26.5.12:8000';
            if ($evento->imagenes && is_array($evento->imagenes)) {
                $evento->imagenes = array_map(function($imagen) use ($baseUrl) {
                    if (empty($imagen) || !is_string($imagen)) {
                        return null;
                    }

                    // Si ya es una URL completa, retornarla
                    if (strpos($imagen, 'http://') === 0 || strpos($imagen, 'https://') === 0) {
                        return $imagen;
                    }

                    // Si empieza con /storage/, agregar la IP fija
                    if (strpos($imagen, '/storage/') === 0) {
                        return $baseUrl . $imagen;
                    }

                    // Si empieza con storage/, agregar /storage/
                    if (strpos($imagen, 'storage/') === 0) {
                        return $baseUrl . '/storage/' . $imagen;
                    }

                    // Por defecto, asumir que es relativa a storage
                    return $baseUrl . '/storage/' . ltrim($imagen, '/');
                }, array_filter($evento->imagenes, function($img) {
                    return !empty($img) && is_string($img);
                }));
            }

            return view('evento-publico.show', [
                'evento' => $evento,
                'eventoId' => $id
            ]);
        } catch (\Throwable $e) {
            \Log::error("Error al mostrar evento público: " . $e->getMessage());
            return view('evento-publico.error', [
                'mensaje' => 'Error al cargar el evento'
            ]);
        }
    }
}
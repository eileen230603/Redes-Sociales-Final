<?php

namespace App\Http\Controllers;

use App\Models\MegaEvento;
use App\Models\Ong;
use Illuminate\Http\Request;

class MegaEventoPublicoController extends Controller
{
    /**
     * Mostrar mega evento público mediante QR (sin autenticación)
     */
    public function show($id)
    {
        try {
            $megaEvento = MegaEvento::find($id);

            if (!$megaEvento) {
                return view('mega-evento-publico.error', [
                    'mensaje' => 'Mega evento no encontrado'
                ]);
            }

            // Agregar información del creador (ONG)
            if ($megaEvento->ong_organizadora_principal) {
                $ong = Ong::where('user_id', $megaEvento->ong_organizadora_principal)->first();
                if ($ong) {
                    // El accessor foto_perfil_url ya normaliza las URLs
                    $fotoPerfil = $ong->foto_perfil_url ?? null;
                    
                    $megaEvento->creador = [
                        'tipo' => 'ONG',
                        'nombre' => $ong->nombre_ong ?? 'ONG',
                        'foto_perfil' => $fotoPerfil,
                        'id' => $ong->user_id ?? null
                    ];
                } else {
                    $megaEvento->creador = null;
                }
            } else {
                $megaEvento->creador = null;
            }

            // Hacer visible las imágenes para que el accessor funcione
            $megaEvento->makeVisible('imagenes');

            // Procesar imágenes para usar IP fija (accesible desde cualquier dispositivo)
            $baseUrl = 'http://192.168.0.7:8000';
            if ($megaEvento->imagenes && is_array($megaEvento->imagenes)) {
                $megaEvento->imagenes = array_map(function($imagen) use ($baseUrl) {
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
                }, array_filter($megaEvento->imagenes, function($img) {
                    return !empty($img) && is_string($img);
                }));
            }

            return view('mega-evento-publico.show', [
                'megaEvento' => $megaEvento,
                'megaEventoId' => $id
            ]);
        } catch (\Throwable $e) {
            \Log::error("Error al mostrar mega evento público: " . $e->getMessage());
            return view('mega-evento-publico.error', [
                'mensaje' => 'Error al cargar el mega evento'
            ]);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ong;
use App\Models\Empresa;
use App\Models\IntegranteExterno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Obtener el perfil completo del usuario autenticado
     */
    public function show(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'Usuario no autenticado'
                ], 401);
            }

            // Cargar la información específica según el tipo de usuario
            $profileData = [
                'id_usuario' => $user->id_usuario,
                'nombre_usuario' => $user->nombre_usuario,
                'correo_electronico' => $user->correo_electronico,
                'tipo_usuario' => $user->tipo_usuario,
                'fecha_registro' => $user->fecha_registro,
                'activo' => $user->activo,
                'foto_perfil' => $user->foto_perfil_url ?? null,
            ];

            // Agregar información específica según el tipo
            if ($user->esOng() && $user->ong) {
                $profileData['ong'] = [
                    'nombre_ong' => $user->ong->nombre_ong,
                    'NIT' => $user->ong->NIT,
                    'telefono' => $user->ong->telefono,
                    'direccion' => $user->ong->direccion,
                    'sitio_web' => $user->ong->sitio_web,
                    'descripcion' => $user->ong->descripcion,
                    'foto_perfil' => $user->ong->foto_perfil_url ?? null,
                    'created_at' => $user->ong->created_at,
                    'updated_at' => $user->ong->updated_at,
                ];
            } elseif ($user->esEmpresa() && $user->empresa) {
                $profileData['empresa'] = [
                    'nombre_empresa' => $user->empresa->nombre_empresa,
                    'NIT' => $user->empresa->NIT,
                    'telefono' => $user->empresa->telefono,
                    'direccion' => $user->empresa->direccion,
                    'sitio_web' => $user->empresa->sitio_web,
                    'descripcion' => $user->empresa->descripcion,
                    'foto_perfil' => $user->empresa->foto_perfil_url ?? null,
                    'created_at' => $user->empresa->created_at,
                    'updated_at' => $user->empresa->updated_at,
                ];
            } elseif ($user->esIntegranteExterno() && $user->integranteExterno) {
                $profileData['integrante_externo'] = [
                    'nombres' => $user->integranteExterno->nombres,
                    'apellidos' => $user->integranteExterno->apellidos,
                    'fecha_nacimiento' => $user->integranteExterno->fecha_nacimiento,
                    'email' => $user->integranteExterno->email,
                    'phone_number' => $user->integranteExterno->phone_number,
                    'descripcion' => $user->integranteExterno->descripcion,
                    'foto_perfil' => $user->integranteExterno->foto_perfil_url ?? null,
                    'created_at' => $user->integranteExterno->created_at,
                    'updated_at' => $user->integranteExterno->updated_at,
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $profileData
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener el perfil: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar el perfil del usuario autenticado
     */
    public function update(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'Usuario no autenticado'
                ], 401);
            }

            // Validación según el tipo de usuario
            $rules = [
                'nombre_usuario' => 'sometimes|string|max:50|unique:usuarios,nombre_usuario,' . $user->id_usuario . ',id_usuario',
                'correo_electronico' => 'sometimes|email|max:100|unique:usuarios,correo_electronico,' . $user->id_usuario . ',id_usuario',
                'contrasena_actual' => 'sometimes|string',
                'nueva_contrasena' => 'sometimes|string|min:6|required_with:contrasena_actual',
                'foto_perfil' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
                'foto_perfil_url' => 'sometimes|nullable|url|max:500',
            ];

            // Reglas específicas según el tipo
            if ($user->esOng()) {
                $rules = array_merge($rules, [
                    'nombre_ong' => 'sometimes|string|max:100',
                    'NIT' => 'sometimes|nullable|string|max:20',
                    'telefono' => 'sometimes|nullable|string|max:20',
                    'direccion' => 'sometimes|nullable|string|max:150',
                    'sitio_web' => 'sometimes|nullable|url|max:150',
                    'descripcion' => 'sometimes|nullable|string',
                ]);
            } elseif ($user->esEmpresa()) {
                $rules = array_merge($rules, [
                    'nombre_empresa' => 'sometimes|string|max:100',
                    'NIT' => 'sometimes|nullable|string|max:20',
                    'telefono' => 'sometimes|nullable|string|max:20',
                    'direccion' => 'sometimes|nullable|string|max:150',
                    'sitio_web' => 'sometimes|nullable|url|max:150',
                    'descripcion' => 'sometimes|nullable|string',
                ]);
            } elseif ($user->esIntegranteExterno()) {
                $rules = array_merge($rules, [
                    'nombres' => 'sometimes|string|max:100',
                    'apellidos' => 'sometimes|nullable|string|max:100',
                    'fecha_nacimiento' => 'sometimes|nullable|date',
                    'email' => 'sometimes|nullable|email|max:100',
                    'phone_number' => 'sometimes|nullable|string|max:30',
                    'descripcion' => 'sometimes|nullable|string',
                ]);
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Actualizar datos del usuario
            if ($request->has('nombre_usuario')) {
                $user->nombre_usuario = $request->nombre_usuario;
            }

            if ($request->has('correo_electronico')) {
                $user->correo_electronico = $request->correo_electronico;
            }

            // Actualizar contraseña si se proporciona
            if ($request->has('contrasena_actual') && $request->has('nueva_contrasena')) {
                if (!Hash::check($request->contrasena_actual, $user->contrasena)) {
                    return response()->json([
                        'success' => false,
                        'error' => 'La contraseña actual es incorrecta'
                    ], 422);
                }
                $user->contrasena = Hash::make($request->nueva_contrasena);
            }

            // Procesar foto de perfil para usuario base
            $fotoPerfil = $this->processFotoPerfil($request, $user, 'usuario');
            if ($fotoPerfil !== null && !empty($fotoPerfil)) {
                $user->foto_perfil = $fotoPerfil;
                \Log::info("Guardando foto_perfil en usuario base: $fotoPerfil");
            }
            $user->save();

            // Actualizar información específica según el tipo
            if ($user->esOng() && $user->ong) {
                $ong = $user->ong;
                if ($request->has('nombre_ong')) $ong->nombre_ong = $request->nombre_ong;
                if ($request->has('NIT')) $ong->NIT = $request->NIT;
                if ($request->has('telefono')) $ong->telefono = $request->telefono;
                if ($request->has('direccion')) $ong->direccion = $request->direccion;
                if ($request->has('sitio_web')) $ong->sitio_web = $request->sitio_web;
                if ($request->has('descripcion')) $ong->descripcion = $request->descripcion;
                
                // Procesar foto de perfil para ONG
                $fotoPerfilOng = $this->processFotoPerfil($request, $user, 'ong');
                if ($fotoPerfilOng !== null && !empty($fotoPerfilOng)) {
                    $ong->foto_perfil = $fotoPerfilOng;
                    \Log::info("Guardando foto_perfil en ONG: $fotoPerfilOng");
                }
                
                $ong->save();
                
                // Recargar la relación para asegurar que los cambios se reflejen
                $user->load('ong');
            } elseif ($user->esEmpresa() && $user->empresa) {
                $empresa = $user->empresa;
                if ($request->has('nombre_empresa')) $empresa->nombre_empresa = $request->nombre_empresa;
                if ($request->has('NIT')) $empresa->NIT = $request->NIT;
                if ($request->has('telefono')) $empresa->telefono = $request->telefono;
                if ($request->has('direccion')) $empresa->direccion = $request->direccion;
                if ($request->has('sitio_web')) $empresa->sitio_web = $request->sitio_web;
                if ($request->has('descripcion')) $empresa->descripcion = $request->descripcion;
                
                // Procesar foto de perfil para Empresa
                $fotoPerfilEmpresa = $this->processFotoPerfil($request, $user, 'empresa');
                if ($fotoPerfilEmpresa !== null && !empty($fotoPerfilEmpresa)) {
                    $empresa->foto_perfil = $fotoPerfilEmpresa;
                    \Log::info("Guardando foto_perfil en Empresa: $fotoPerfilEmpresa");
                }
                
                $empresa->save();
                
                // Recargar la relación para asegurar que los cambios se reflejen
                $user->load('empresa');
            } elseif ($user->esIntegranteExterno() && $user->integranteExterno) {
                $externo = $user->integranteExterno;
                if ($request->has('nombres')) $externo->nombres = $request->nombres;
                if ($request->has('apellidos')) $externo->apellidos = $request->apellidos;
                if ($request->has('fecha_nacimiento')) $externo->fecha_nacimiento = $request->fecha_nacimiento;
                if ($request->has('email')) $externo->email = $request->email;
                if ($request->has('phone_number')) $externo->phone_number = $request->phone_number;
                if ($request->has('descripcion')) $externo->descripcion = $request->descripcion;
                
                // Procesar foto de perfil para Externo
                $fotoPerfilExterno = $this->processFotoPerfil($request, $user, 'externo');
                if ($fotoPerfilExterno !== null && !empty($fotoPerfilExterno)) {
                    $externo->foto_perfil = $fotoPerfilExterno;
                    \Log::info("Guardando foto_perfil en Externo: $fotoPerfilExterno");
                }
                
                $externo->save();
                
                // Recargar la relación para asegurar que los cambios se reflejen
                $user->load('integranteExterno');
            }

            // Recargar el usuario para obtener los datos actualizados
            $user->refresh();
            
            // Si es ONG, recargar también la relación
            if ($user->esOng()) {
                $user->load('ong');
            } elseif ($user->esEmpresa()) {
                $user->load('empresa');
            } elseif ($user->esIntegranteExterno()) {
                $user->load('integranteExterno');
            }
            
            // Obtener la foto de perfil actualizada según el tipo
            $fotoPerfilActualizada = null;
            if ($user->esOng() && $user->ong) {
                $fotoPerfilActualizada = $user->ong->foto_perfil_url ?? null;
            } elseif ($user->esEmpresa() && $user->empresa) {
                $fotoPerfilActualizada = $user->empresa->foto_perfil_url ?? null;
            } elseif ($user->esIntegranteExterno() && $user->integranteExterno) {
                $fotoPerfilActualizada = $user->integranteExterno->foto_perfil_url ?? null;
            } else {
                $fotoPerfilActualizada = $user->foto_perfil_url ?? null;
            }
            
            \Log::info("Perfil actualizado. Foto de perfil: " . ($fotoPerfilActualizada ?? 'null'));
            
            return response()->json([
                'success' => true,
                'message' => 'Perfil actualizado correctamente',
                'foto_perfil' => $fotoPerfilActualizada
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al actualizar el perfil: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Procesar foto de perfil: subida de archivo o URL
     * Similar a la lógica de processImages en MegaEventoController
     */
    private function processFotoPerfil(Request $request, User $user, $tipo = 'usuario')
    {
        \Log::info("processFotoPerfil iniciado para tipo: {$tipo}, usuario: {$user->id_usuario}");
        
        // Prioridad: archivo subido > URL proporcionada
        if ($request->hasFile('foto_perfil')) {
            $file = $request->file('foto_perfil');
            \Log::info("Archivo recibido: " . $file->getClientOriginalName() . ", tamaño: " . $file->getSize());
            
            // Verificar que el archivo sea válido
            if (!$file->isValid()) {
                \Log::warning("Archivo de foto_perfil inválido para tipo {$tipo}");
                return null;
            }
            
            // Validar tipo de archivo
            $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
            if (!in_array($file->getMimeType(), $allowedMimes)) {
                \Log::warning("Tipo de archivo no permitido para foto_perfil tipo {$tipo}: " . $file->getMimeType());
                return null;
            }

            // Validar tamaño (5MB máximo)
            if ($file->getSize() > 5 * 1024 * 1024) {
                \Log::warning("Archivo demasiado grande para foto_perfil tipo {$tipo}: " . $file->getSize());
                return null;
            }

            // Eliminar foto anterior si existe (solo si es archivo local, no URL externa)
            $fotoAnterior = null;
            if ($tipo === 'ong' && $user->ong && $user->ong->foto_perfil) {
                $fotoAnterior = $user->ong->foto_perfil;
            } elseif ($tipo === 'empresa' && $user->empresa && $user->empresa->foto_perfil) {
                $fotoAnterior = $user->empresa->foto_perfil;
            } elseif ($tipo === 'externo' && $user->integranteExterno && $user->integranteExterno->foto_perfil) {
                $fotoAnterior = $user->integranteExterno->foto_perfil;
            } elseif ($user->foto_perfil) {
                $fotoAnterior = $user->foto_perfil;
            }

            // Solo eliminar si es un archivo local (no URL externa)
            if ($fotoAnterior && !str_starts_with($fotoAnterior, 'http://') && !str_starts_with($fotoAnterior, 'https://')) {
                try {
                    // Limpiar la ruta para eliminar
                    $rutaAnterior = str_replace('storage/', '', $fotoAnterior);
                    $rutaAnterior = str_replace('/storage/', '', $rutaAnterior);
                    $rutaAnterior = ltrim($rutaAnterior, '/');
                    
                    // Eliminar de storage/app/public/
                    if (Storage::disk('public')->exists($rutaAnterior)) {
                        Storage::disk('public')->delete($rutaAnterior);
                        \Log::info("Eliminada foto anterior de storage para tipo {$tipo}: {$rutaAnterior}");
                    }
                    
                    // Eliminar también de public/storage/
                    $publicPathAnterior = public_path('storage/' . $rutaAnterior);
                    if (file_exists($publicPathAnterior) && is_file($publicPathAnterior)) {
                        unlink($publicPathAnterior);
                        \Log::info("Eliminada foto anterior de public/storage para tipo {$tipo}: {$publicPathAnterior}");
                    }
                } catch (\Exception $e) {
                    \Log::warning("Error al eliminar foto anterior para tipo {$tipo}: " . $e->getMessage());
                }
            }

            // Guardar nueva foto usando el mismo sistema mejorado que eventos y mega eventos
            try {
                // Generar nombre único para el archivo
                $extension = $file->getClientOriginalExtension();
                $nombreArchivo = 'perfil_' . $user->id_usuario . '_' . time() . '_' . \Illuminate\Support\Str::random(8) . '.' . $extension;
                $ruta = 'perfiles/' . $tipo . '/' . $user->id_usuario;
                $filename = $ruta . '/' . $nombreArchivo;
                
                // Guardar usando el disco 'public' explícitamente
                $path = Storage::disk('public')->putFileAs(
                    $ruta,
                    $file,
                    $nombreArchivo
                );
                
                // Verificar que el archivo se guardó correctamente
                $fullPath = storage_path('app/public/' . $path);
                if (!file_exists($fullPath) || !is_file($fullPath)) {
                    \Log::error("No se pudo guardar la foto de perfil: $fullPath");
                    return null;
                }
                
                // Verificar que el archivo tiene contenido
                if (filesize($fullPath) === 0) {
                    \Log::error("La foto de perfil se guardó vacía: $fullPath");
                    Storage::disk('public')->delete($path);
                    return null;
                }
                
                // Copiar también a public/storage/ para que el servidor de PHP pueda servirlo directamente
                $publicPath = public_path('storage/' . $path);
                $publicDir = dirname($publicPath);
                if (!file_exists($publicDir)) {
                    if (!mkdir($publicDir, 0755, true)) {
                        \Log::warning("No se pudo crear el directorio: $publicDir");
                    }
                }
                
                if (file_exists($fullPath)) {
                    if (!copy($fullPath, $publicPath)) {
                        \Log::warning("No se pudo copiar la foto a public/storage: $publicPath");
                    } else {
                        \Log::info("Foto copiada exitosamente a public/storage: $publicPath");
                    }
                }
                
                // Obtener la URL pública (ruta relativa)
                $url = Storage::disk('public')->url($path);
                
                // Verificar que la URL se generó correctamente
                if (empty($url)) {
                    \Log::error("No se pudo generar la URL para la foto de perfil: $path");
                    return null;
                }
                
                \Log::info("Foto de perfil guardada exitosamente para tipo {$tipo}: $url -> $fullPath (también copiada a $publicPath)");
                return $url; // Retorna /storage/perfiles/...
                
            } catch (\Exception $e) {
                \Log::error("Error al guardar foto de perfil tipo {$tipo}: " . $e->getMessage());
                \Log::error("Stack trace: " . $e->getTraceAsString());
                return null;
            }
        }

        // Si se proporciona una URL (similar a imagenes_urls en mega-eventos)
        $fotoPerfilUrl = null;
        
        // Intentar obtener de diferentes formas (FormData puede venir de diferentes maneras)
        if ($request->has('foto_perfil_url')) {
            $fotoPerfilUrl = $request->input('foto_perfil_url');
        }
        
        // Si viene como JSON string (similar a imagenes_urls)
        if (empty($fotoPerfilUrl) && $request->has('foto_perfil_url_json')) {
            $urlJson = $request->input('foto_perfil_url_json');
            if (is_string($urlJson)) {
                $decoded = json_decode($urlJson, true);
                if (is_array($decoded) && !empty($decoded)) {
                    $fotoPerfilUrl = $decoded[0] ?? null; // Tomar la primera URL si es array
                } elseif (is_string($decoded)) {
                    $fotoPerfilUrl = $decoded;
                }
            }
        }
        
        // Si aún está vacío, verificar en all() (útil para FormData)
        if (empty($fotoPerfilUrl)) {
            $all = $request->all();
            $fotoPerfilUrl = $all['foto_perfil_url'] ?? null;
        }
        
        if (!empty($fotoPerfilUrl)) {
            $url = trim($fotoPerfilUrl);
            
            // Validar que sea una URL válida (similar a mega-eventos)
            if (filter_var($url, FILTER_VALIDATE_URL) && 
                (str_starts_with($url, 'http://') || str_starts_with($url, 'https://'))) {
                // Guardar la URL directamente en la base de datos (igual que mega-eventos)
                \Log::info("Guardando foto_perfil_url para tipo {$tipo}: {$url}");
                return $url;
            } else {
                \Log::warning("URL de foto_perfil inválida para tipo {$tipo}: {$url}");
            }
        }

        return null; // No hay cambios en la foto
    }
}


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
use Illuminate\Support\Facades\DB;

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
            \Log::info("=== INICIO UPDATE PERFIL ===");
            \Log::info("Content-Type: " . ($request->header('Content-Type') ?? 'NO HEADER'));
            \Log::info("Request method: " . $request->method());
            \Log::info("Request tiene foto_perfil file: " . ($request->hasFile('foto_perfil') ? 'SÍ' : 'NO'));
            \Log::info("Request tiene foto_perfil_url: " . ($request->has('foto_perfil_url') ? 'SÍ' : 'NO'));
            \Log::info("Request all keys: " . implode(', ', array_keys($request->all())));
            \Log::info("Request allFiles keys: " . implode(', ', array_keys($request->allFiles())));
            \Log::info("Request input keys: " . implode(', ', array_keys($request->input())));
            \Log::info("Request files count: " . count($request->allFiles()));
            
            // Debug: verificar $_FILES directamente
            \Log::info("_FILES superglobal: " . json_encode($_FILES ?? []));
            
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'Usuario no autenticado'
                ], 401);
            }
            
            \Log::info("Usuario autenticado: {$user->id_usuario}, tipo: {$user->tipo_usuario}");

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

            // Validar contraseña antes de la transacción
            if ($request->has('contrasena_actual') && $request->has('nueva_contrasena')) {
                if (!Hash::check($request->contrasena_actual, $user->contrasena)) {
                    return response()->json([
                        'success' => false,
                        'error' => 'La contraseña actual es incorrecta'
                    ], 422);
                }
            }

            // TRANSACCIÓN: Actualizar usuario + entidad relacionada
            DB::transaction(function () use ($request, $user) {
                // 1. Actualizar datos del usuario
                if ($request->has('nombre_usuario')) {
                    $user->nombre_usuario = $request->nombre_usuario;
                }

                if ($request->has('correo_electronico')) {
                    $user->correo_electronico = $request->correo_electronico;
                }

                // Actualizar contraseña si se proporciona
                if ($request->has('contrasena_actual') && $request->has('nueva_contrasena')) {
                    $user->contrasena = Hash::make($request->nueva_contrasena);
                }
                
                // Procesar foto de perfil para usuario base (también guardar en tabla usuarios)
                $fotoPerfilUsuario = $this->processFotoPerfil($request, $user, 'usuario');
                if ($fotoPerfilUsuario !== null && !empty($fotoPerfilUsuario)) {
                    $user->foto_perfil = $fotoPerfilUsuario;
                    \Log::info("Guardando foto_perfil en tabla usuarios: $fotoPerfilUsuario");
                }
                
                $user->save();
                \Log::info("Usuario guardado. foto_perfil en tabla usuarios: " . ($user->foto_perfil ?? 'NULL'));

            // 2. Actualizar información específica según el tipo
            if ($user->esOng() && $user->ong) {
                $ong = $user->ong;
                if ($request->has('nombre_ong')) $ong->nombre_ong = $request->nombre_ong;
                if ($request->has('NIT')) $ong->NIT = $request->NIT;
                if ($request->has('telefono')) $ong->telefono = $request->telefono;
                if ($request->has('direccion')) $ong->direccion = $request->direccion;
                if ($request->has('sitio_web')) $ong->sitio_web = $request->sitio_web;
                if ($request->has('descripcion')) $ong->descripcion = $request->descripcion;
                
                // Procesar foto de perfil para ONG (solo una vez, no para usuario base)
                $fotoPerfilOng = $this->processFotoPerfil($request, $user, 'ong');
                \Log::info("Resultado processFotoPerfil para ONG: " . ($fotoPerfilOng ?? 'NULL'));
                if ($fotoPerfilOng !== null && !empty($fotoPerfilOng)) {
                    $ong->foto_perfil = $fotoPerfilOng;
                    \Log::info("Guardando foto_perfil en ONG: $fotoPerfilOng");
                } else {
                    \Log::warning("processFotoPerfil retornó null o vacío para ONG");
                }
                
                $ong->save();
                \Log::info("ONG guardada. foto_perfil en BD: " . ($ong->foto_perfil ?? 'NULL'));
            } elseif ($user->esEmpresa() && $user->empresa) {
                $empresa = $user->empresa;
                if ($request->has('nombre_empresa')) $empresa->nombre_empresa = $request->nombre_empresa;
                if ($request->has('NIT')) $empresa->NIT = $request->NIT;
                if ($request->has('telefono')) $empresa->telefono = $request->telefono;
                if ($request->has('direccion')) $empresa->direccion = $request->direccion;
                if ($request->has('sitio_web')) $empresa->sitio_web = $request->sitio_web;
                if ($request->has('descripcion')) $empresa->descripcion = $request->descripcion;
                
                // Procesar foto de perfil para Empresa (solo una vez, no para usuario base)
                $fotoPerfilEmpresa = $this->processFotoPerfil($request, $user, 'empresa');
                \Log::info("Resultado processFotoPerfil para Empresa: " . ($fotoPerfilEmpresa ?? 'NULL'));
                if ($fotoPerfilEmpresa !== null && !empty($fotoPerfilEmpresa)) {
                    $empresa->foto_perfil = $fotoPerfilEmpresa;
                    \Log::info("Guardando foto_perfil en Empresa: $fotoPerfilEmpresa");
                } else {
                    \Log::warning("processFotoPerfil retornó null o vacío para Empresa");
                }
                
                $empresa->save();
                \Log::info("Empresa guardada. foto_perfil en BD: " . ($empresa->foto_perfil ?? 'NULL'));
            } elseif ($user->esIntegranteExterno() && $user->integranteExterno) {
                $externo = $user->integranteExterno;
                if ($request->has('nombres')) $externo->nombres = $request->nombres;
                if ($request->has('apellidos')) $externo->apellidos = $request->apellidos;
                if ($request->has('fecha_nacimiento')) $externo->fecha_nacimiento = $request->fecha_nacimiento;
                if ($request->has('email')) $externo->email = $request->email;
                if ($request->has('phone_number')) $externo->phone_number = $request->phone_number;
                if ($request->has('descripcion')) $externo->descripcion = $request->descripcion;
                
                // Procesar foto de perfil para Externo (solo una vez, no para usuario base)
                $fotoPerfilExterno = $this->processFotoPerfil($request, $user, 'externo');
                \Log::info("Resultado processFotoPerfil para Externo: " . ($fotoPerfilExterno ?? 'NULL'));
                if ($fotoPerfilExterno !== null && !empty($fotoPerfilExterno)) {
                    $externo->foto_perfil = $fotoPerfilExterno;
                    \Log::info("Guardando foto_perfil en Externo: $fotoPerfilExterno");
                } else {
                    \Log::warning("processFotoPerfil retornó null o vacío para Externo");
                }
                
                $externo->save();
                \Log::info("Externo guardado. foto_perfil en BD: " . ($externo->foto_perfil ?? 'NULL'));
                }
            });
                
            // Recargar el usuario y relaciones para obtener los datos actualizados
            $user->refresh();
            
            // Recargar relaciones según el tipo de usuario
            if ($user->esOng()) {
                $user->load('ong');
                $entidad = $user->ong;
            } elseif ($user->esEmpresa()) {
                $user->load('empresa');
                $entidad = $user->empresa;
            } elseif ($user->esIntegranteExterno()) {
                $user->load('integranteExterno');
                $entidad = $user->integranteExterno;
            } else {
                $entidad = null;
            }
            
            // Obtener la foto de perfil actualizada según el tipo
            $fotoPerfilActualizada = null;
            if ($entidad) {
                // Recargar la entidad desde la base de datos para obtener el valor actualizado
                $entidad->refresh();
                $fotoPerfilActualizada = $entidad->foto_perfil_url ?? null;
                \Log::info("Foto de perfil desde entidad ({$user->tipo_usuario}): " . ($fotoPerfilActualizada ?? 'null'));
                \Log::info("Valor raw de foto_perfil en BD: " . ($entidad->foto_perfil ?? 'null'));
            } else {
                $user->refresh();
                $fotoPerfilActualizada = $user->foto_perfil_url ?? null;
                \Log::info("Foto de perfil desde usuario base: " . ($fotoPerfilActualizada ?? 'null'));
                \Log::info("Valor raw de foto_perfil en BD: " . ($user->foto_perfil ?? 'null'));
            }
            
            \Log::info("Perfil actualizado. Foto de perfil final: " . ($fotoPerfilActualizada ?? 'null'));
            
            return response()->json([
                'success' => true,
                'message' => 'Perfil actualizado correctamente',
                'foto_perfil' => $fotoPerfilActualizada,
                'data' => [
                    'foto_perfil' => $fotoPerfilActualizada,
                    'tipo_usuario' => $user->tipo_usuario
                ]
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
        \Log::info("hasFile('foto_perfil'): " . ($request->hasFile('foto_perfil') ? 'SÍ' : 'NO'));
        \Log::info("all() keys: " . implode(', ', array_keys($request->all())));
        \Log::info("files() keys: " . implode(', ', array_keys($request->allFiles())));
        
        // Prioridad: archivo subido > URL proporcionada
        if ($request->hasFile('foto_perfil')) {
            $file = $request->file('foto_perfil');
            \Log::info("✅ Archivo recibido: " . $file->getClientOriginalName() . ", tamaño: " . $file->getSize() . " bytes");
            
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
                    // Limpiar la ruta para eliminar (similar a eventos)
                    $rutaAnterior = str_replace('storage/', '', $fotoAnterior);
                    $rutaAnterior = str_replace('/storage/', '', $rutaAnterior);
                    $rutaAnterior = ltrim($rutaAnterior, '/');
                    
                    // Si la ruta contiene "perfil/", es la nueva estructura
                    if (strpos($rutaAnterior, 'perfil/') === 0) {
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
                    } else {
                        // Compatibilidad con rutas antiguas (perfiles/)
                        if (Storage::disk('public')->exists($rutaAnterior)) {
                            Storage::disk('public')->delete($rutaAnterior);
                            \Log::info("Eliminada foto anterior (ruta antigua) de storage para tipo {$tipo}: {$rutaAnterior}");
                        }
                        
                        $publicPathAnterior = public_path('storage/' . $rutaAnterior);
                        if (file_exists($publicPathAnterior) && is_file($publicPathAnterior)) {
                            unlink($publicPathAnterior);
                            \Log::info("Eliminada foto anterior (ruta antigua) de public/storage para tipo {$tipo}: {$publicPathAnterior}");
                        }
                    }
                } catch (\Exception $e) {
                    \Log::warning("Error al eliminar foto anterior para tipo {$tipo}: " . $e->getMessage());
                }
            }

            // Guardar nueva foto usando el mismo sistema mejorado que eventos y mega eventos
            try {
                // Generar nombre único para el archivo (similar a eventos: perfil/{tipo}/{id_usuario}/{uuid}.{ext})
                $extension = $file->getClientOriginalExtension();
                $uuid = \Illuminate\Support\Str::uuid();
                $rutaDirectorio = 'perfil/' . $tipo . '/' . $user->id_usuario;
                $nombreArchivo = $uuid . '.' . $extension;
                $filename = $rutaDirectorio . '/' . $nombreArchivo;
                
                // Asegurar que el directorio existe antes de guardar
                $directorioCompleto = storage_path('app/public/' . $rutaDirectorio);
                if (!file_exists($directorioCompleto)) {
                    if (!mkdir($directorioCompleto, 0755, true)) {
                        \Log::error("No se pudo crear el directorio: $directorioCompleto");
                        return null;
                    }
                    \Log::info("Directorio creado: $directorioCompleto");
                }
                
                // Guardar usando el disco 'public' explícitamente (igual que eventos)
                $path = Storage::disk('public')->putFileAs(
                    $rutaDirectorio,
                    $file,
                    $nombreArchivo
                );
                
                \Log::info("Archivo guardado con Storage::putFileAs. Path retornado: $path");
                
                // Verificar que el archivo se guardó correctamente
                $fullPath = storage_path('app/public/' . $path);
                if (!file_exists($fullPath) || !is_file($fullPath)) {
                    \Log::error("No se pudo guardar la foto de perfil: $fullPath");
                    \Log::error("Path retornado por Storage: $path");
                    \Log::error("Directorio completo esperado: $directorioCompleto");
                    return null;
                }
                
                // Verificar que el archivo tiene contenido
                $fileSize = filesize($fullPath);
                if ($fileSize === 0) {
                    \Log::error("La foto de perfil se guardó vacía: $fullPath");
                    Storage::disk('public')->delete($path);
                    return null;
                }
                
                \Log::info("Archivo guardado correctamente. Tamaño: $fileSize bytes. Ruta: $fullPath");
                
                // Copiar también a public/storage/ para que el servidor de PHP pueda servirlo directamente
                $publicPath = public_path('storage/' . $path);
                $publicDir = dirname($publicPath);
                if (!file_exists($publicDir)) {
                    if (!mkdir($publicDir, 0755, true)) {
                        \Log::warning("No se pudo crear el directorio público: $publicDir");
                    } else {
                        \Log::info("Directorio público creado: $publicDir");
                    }
                }
                
                if (file_exists($fullPath)) {
                    if (!copy($fullPath, $publicPath)) {
                        \Log::warning("No se pudo copiar la foto a public/storage: $publicPath");
                    } else {
                        \Log::info("Foto copiada exitosamente a public/storage: $publicPath");
                    }
                }
                
                // Retornar la ruta relativa (sin /storage/) para guardar en BD
                // El accessor del modelo se encargará de generar la URL completa
                \Log::info("Foto de perfil guardada exitosamente para tipo {$tipo}: $path -> $fullPath (también copiada a $publicPath)");
                return $path; // Retorna perfil/{tipo}/{id_usuario}/{uuid}.{ext}
                
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


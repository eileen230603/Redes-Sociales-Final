# Implementación de Transacciones de Base de Datos
## Sistema de Gestión de Eventos Sociales

---

## Introducción

Las transacciones de base de datos garantizan que múltiples operaciones se ejecuten de forma atómica: todas se completan exitosamente o ninguna se aplica. Esto es crítico para mantener la integridad de los datos cuando se realizan operaciones que involucran múltiples tablas relacionadas.

En Laravel, las transacciones se implementan usando `DB::transaction()` o métodos transaccionales de Eloquent.

---

## Lugares Críticos que Requieren Transacciones

### 1. Registro de Usuarios (AuthController::register)
**Problema actual**: Se crea el usuario y luego se crea el registro específico (ONG/Empresa/Externo). Si falla la segunda operación, queda un usuario huérfano.

**Solución**: Envolver ambas operaciones en una transacción.

### 2. Inscripción a Eventos (EventoParticipacionController::inscribir)
**Problema actual**: Se crea la participación y luego se crea la notificación. Si falla la notificación, la participación queda sin notificar.

**Solución**: Envolver ambas operaciones en una transacción.

### 3. Participación en Mega Eventos (MegaEventoController::participar)
**Problema actual**: Se inserta en la tabla pivot y luego se crea la notificación. Si falla la notificación, la participación queda sin notificar.

**Solución**: Envolver ambas operaciones en una transacción.

### 4. Creación de Eventos con Imágenes (EventController::store)
**Problema actual**: Se crea el evento y luego se procesan las imágenes. Si falla el procesamiento de imágenes, el evento queda sin imágenes.

**Solución**: Considerar transacción o manejo de errores mejorado.

### 5. Actualización de Perfil (ProfileController::update)
**Problema actual**: Se actualiza el usuario y luego la entidad relacionada. Si falla la segunda, quedan datos inconsistentes.

**Solución**: Envolver ambas operaciones en una transacción.

---

## Implementación de Transacciones

### Método 1: DB::transaction() (Recomendado)

```php
use Illuminate\Support\Facades\DB;

DB::transaction(function () {
    // Operaciones de base de datos
    // Si alguna falla, todas se revierten automáticamente
});
```

### Método 2: Transacciones Manuales

```php
use Illuminate\Support\Facades\DB;

DB::beginTransaction();
try {
    // Operaciones de base de datos
    DB::commit();
} catch (\Exception $e) {
    DB::rollBack();
    throw $e;
}
```

---

## Ejemplos de Implementación

### Ejemplo 1: AuthController::register() con Transacción

**Archivo**: `app/Http/Controllers/Auth/AuthController.php`

```php
use Illuminate\Support\Facades\DB;

public function register(Request $request)
{
    try {
        // VALIDACIÓN (fuera de la transacción)
        $validator = Validator::make($request->all(), [
            'tipo_usuario' => 'required|in:ONG,Empresa,Integrante externo',
            'nombre_usuario' => 'required|string|max:50|unique:usuarios,nombre_usuario',
            'correo_electronico' => 'required|email|max:100|unique:usuarios,correo_electronico',
            'contrasena' => 'required|string|min:6',
            // ... resto de validaciones
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first(),
            ], 422);
        }

        // TRANSACCIÓN: Crear usuario + entidad relacionada
        $user = DB::transaction(function () use ($request) {
            // 1. Crear usuario base
            $user = User::create([
                'nombre_usuario' => $request->nombre_usuario,
                'correo_electronico' => $request->correo_electronico,
                'contrasena' => Hash::make($request->contrasena),
                'tipo_usuario' => $request->tipo_usuario,
                'activo' => true,
            ]);

            // 2. Crear entidad específica según tipo
            if ($user->tipo_usuario === "ONG") {
                Ong::create([
                    'user_id' => $user->id_usuario,
                    'nombre_ong' => $request->nombre_ong,
                    'NIT' => $request->NIT,
                    'telefono' => $request->telefono,
                    'direccion' => $request->direccion,
                    'sitio_web' => $request->sitio_web,
                    'descripcion' => $request->descripcion,
                ]);
            } elseif ($user->tipo_usuario === "Empresa") {
                Empresa::create([
                    'user_id' => $user->id_usuario,
                    'nombre_empresa' => $request->nombre_empresa,
                    'NIT' => $request->NIT,
                    'telefono' => $request->telefono,
                    'direccion' => $request->direccion,
                    'sitio_web' => $request->sitio_web,
                    'descripcion' => $request->descripcion,
                ]);
            } elseif ($user->tipo_usuario === "Integrante externo") {
                IntegranteExterno::create([
                    'user_id' => $user->id_usuario,
                    'nombres' => $request->nombres,
                    'apellidos' => $request->apellidos,
                    'fecha_nacimiento' => $request->fecha_nacimiento,
                    'email' => $request->correo_electronico,
                    'phone_number' => $request->telefono,
                    'descripcion' => $request->descripcion,
                ]);
            }

            return $user;
        });

        // Generar token (fuera de la transacción)
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Usuario registrado correctamente',
            'token' => $token,
            'user' => [
                'id_usuario' => $user->id_usuario,
                'nombre_usuario' => $user->nombre_usuario,
                'tipo_usuario' => $user->tipo_usuario,
            ],
        ], 201);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
        ], 422);
    } catch (\Throwable $e) {
        \Log::error('Error en registro: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'error' => 'Error interno al registrar usuario',
        ], 500);
    }
}
```

**Beneficios**:
- Si falla la creación de la entidad relacionada, el usuario no se crea
- Si falla la creación del usuario, no se intenta crear la entidad
- Garantiza consistencia de datos

---

### Ejemplo 2: EventoParticipacionController::inscribir() con Transacción

**Archivo**: `app/Http/Controllers/Api/EventoParticipacionController.php`

```php
use Illuminate\Support\Facades\DB;

public function inscribir(Request $request)
{
    try {
        $externoId = $request->user()->id_usuario;
        $eventoId = $request->evento_id;

        // Validaciones (fuera de la transacción)
        $evento = Evento::find($eventoId);
        if (!$evento) {
            return response()->json(["success" => false, "error" => "Evento no encontrado"], 404);
        }

        if (!$evento->inscripcion_abierta) {
            return response()->json(["success" => false, "error" => "Inscripciones cerradas"], 400);
        }

        $inscritos = EventoParticipacion::where('evento_id', $eventoId)->count();
        if ($evento->capacidad_maxima && $inscritos >= $evento->capacidad_maxima) {
            return response()->json(["success" => false, "error" => "Cupo agotado"], 400);
        }

        if (EventoParticipacion::where('evento_id', $eventoId)->where('externo_id', $externoId)->exists()) {
            return response()->json(["success" => false, "error" => "Ya estás inscrito"], 400);
        }

        // TRANSACCIÓN: Crear participación + notificación
        $data = DB::transaction(function () use ($eventoId, $externoId, $evento) {
            // 1. Crear participación
            $participacion = EventoParticipacion::create([
                "evento_id" => $eventoId,
                "externo_id" => $externoId,
                "estado" => "aprobada",
                "asistio" => false,
                "puntos" => 0
            ]);

            // 2. Crear notificación para la ONG
            $this->crearNotificacionParticipacion($evento, $externoId);

            return $participacion;
        });

        return response()->json([
            "success" => true,
            "message" => "Inscripción exitosa y aprobada automáticamente",
            "data" => $data
        ]);

    } catch (\Throwable $e) {
        \Log::error('Error en inscripción: ' . $e->getMessage());
        return response()->json([
            "success" => false,
            "error" => "Error al procesar la inscripción"
        ], 500);
    }
}
```

**Beneficios**:
- Si falla la creación de la notificación, la participación se revierte
- Garantiza que la ONG siempre reciba notificación cuando hay una inscripción
- Evita estados inconsistentes

---

### Ejemplo 3: MegaEventoController::participar() con Transacción

**Archivo**: `app/Http/Controllers/MegaEventoController.php`

```php
use Illuminate\Support\Facades\DB;

public function participar(Request $request, $megaEventoId)
{
    try {
        $externoId = $request->user()->id_usuario;
        
        // Validaciones (fuera de la transacción)
        $user = \App\Models\User::find($externoId);
        if (!$user || ($user->tipo_usuario !== 'Integrante externo' && $user->tipo_usuario !== 'Voluntario')) {
            return response()->json([
                'success' => false,
                'error' => 'Solo usuarios externos y voluntarios pueden participar en mega eventos'
            ], 403);
        }

        $megaEvento = MegaEvento::find($megaEventoId);
        if (!$megaEvento) {
            return response()->json([
                'success' => false,
                'error' => 'Mega evento no encontrado'
            ], 404);
        }

        if (!$megaEvento->es_publico) {
            return response()->json([
                'success' => false,
                'error' => 'Este mega evento no es público'
            ], 403);
        }

        if (!$megaEvento->activo) {
            return response()->json([
                'success' => false,
                'error' => 'Este mega evento no está activo'
            ], 400);
        }

        // Verificar capacidad
        $participantes = DB::table('mega_evento_participantes_externos')
            ->where('mega_evento_id', $megaEventoId)
            ->where('activo', true)
            ->count();
        
        if ($megaEvento->capacidad_maxima && $participantes >= $megaEvento->capacidad_maxima) {
            return response()->json([
                'success' => false,
                'error' => 'Cupo agotado'
            ], 400);
        }

        $integranteExterno = \App\Models\IntegranteExterno::where('user_id', $externoId)->first();
        if (!$integranteExterno) {
            return response()->json([
                'success' => false,
                'error' => 'Usuario externo no encontrado'
            ], 404);
        }

        // Verificar si ya está participando
        $yaParticipa = DB::table('mega_evento_participantes_externos')
            ->where('mega_evento_id', $megaEventoId)
            ->where('integrante_externo_id', $integranteExterno->user_id)
            ->exists();

        if ($yaParticipa) {
            return response()->json([
                'success' => false,
                'error' => 'Ya estás participando en este mega evento'
            ], 400);
        }

        // TRANSACCIÓN: Insertar participación + crear notificación
        DB::transaction(function () use ($megaEventoId, $integranteExterno, $megaEvento, $externoId) {
            // 1. Crear participación
            DB::table('mega_evento_participantes_externos')->insert([
                'mega_evento_id' => $megaEventoId,
                'integrante_externo_id' => $integranteExterno->user_id,
                'estado_participacion' => 'aprobada',
                'fecha_registro' => now(),
                'activo' => true
            ]);

            // 2. Crear notificación para la ONG
            $this->crearNotificacionMegaEvento($megaEvento, $externoId);
        });

        return response()->json([
            'success' => true,
            'message' => 'Participación registrada y aprobada automáticamente'
        ]);

    } catch (\Throwable $e) {
        \Log::error('Error al participar en mega evento: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'error' => 'Error al procesar la participación'
        ], 500);
    }
}
```

**Beneficios**:
- Si falla la creación de la notificación, la participación se revierte
- Garantiza consistencia entre la tabla pivot y las notificaciones

---

### Ejemplo 4: ProfileController::update() con Transacción

**Archivo**: `app/Http/Controllers/ProfileController.php`

```php
use Illuminate\Support\Facades\DB;

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

        // Validación (fuera de la transacción)
        $rules = [
            'nombre_usuario' => 'sometimes|string|max:50|unique:usuarios,nombre_usuario,' . $user->id_usuario . ',id_usuario',
            'correo_electronico' => 'sometimes|email|max:100|unique:usuarios,correo_electronico,' . $user->id_usuario . ',id_usuario',
            // ... resto de reglas
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // TRANSACCIÓN: Actualizar usuario + entidad relacionada
        DB::transaction(function () use ($request, $user) {
            // 1. Actualizar usuario base
            if ($request->has('nombre_usuario')) {
                $user->nombre_usuario = $request->nombre_usuario;
            }
            if ($request->has('correo_electronico')) {
                $user->correo_electronico = $request->correo_electronico;
            }
            if ($request->has('contrasena_actual') && $request->has('nueva_contrasena')) {
                if (!Hash::check($request->contrasena_actual, $user->contrasena)) {
                    throw new \Exception('La contraseña actual es incorrecta');
                }
                $user->contrasena = Hash::make($request->nueva_contrasena);
            }

            // Procesar foto de perfil
            $fotoPerfil = $this->processFotoPerfil($request, $user, 'usuario');
            if ($fotoPerfil !== null && !empty($fotoPerfil)) {
                $user->foto_perfil = $fotoPerfil;
            }
            $user->save();

            // 2. Actualizar entidad específica
            if ($user->esOng() && $user->ong) {
                $ong = $user->ong;
                if ($request->has('nombre_ong')) $ong->nombre_ong = $request->nombre_ong;
                if ($request->has('NIT')) $ong->NIT = $request->NIT;
                // ... resto de campos
                
                $fotoPerfilOng = $this->processFotoPerfil($request, $user, 'ong');
                if ($fotoPerfilOng !== null && !empty($fotoPerfilOng)) {
                    $ong->foto_perfil = $fotoPerfilOng;
                }
                $ong->save();
            } elseif ($user->esEmpresa() && $user->empresa) {
                $empresa = $user->empresa;
                if ($request->has('nombre_empresa')) $empresa->nombre_empresa = $request->nombre_empresa;
                // ... resto de campos
                
                $fotoPerfilEmpresa = $this->processFotoPerfil($request, $user, 'empresa');
                if ($fotoPerfilEmpresa !== null && !empty($fotoPerfilEmpresa)) {
                    $empresa->foto_perfil = $fotoPerfilEmpresa;
                }
                $empresa->save();
            } elseif ($user->esIntegranteExterno() && $user->integranteExterno) {
                $externo = $user->integranteExterno;
                if ($request->has('nombres')) $externo->nombres = $request->nombres;
                // ... resto de campos
                
                $fotoPerfilExterno = $this->processFotoPerfil($request, $user, 'externo');
                if ($fotoPerfilExterno !== null && !empty($fotoPerfilExterno)) {
                    $externo->foto_perfil = $fotoPerfilExterno;
                }
                $externo->save();
            }
        });

        $user->refresh();
        // ... resto del código de respuesta

    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'error' => 'Error al actualizar el perfil: ' . $e->getMessage()
        ], 500);
    }
}
```

**Beneficios**:
- Si falla la actualización de la entidad relacionada, los cambios en el usuario se revierten
- Garantiza consistencia entre usuario y entidad relacionada

---

## Consideraciones Importantes

### 1. Operaciones que NO deben estar en transacciones
- **Validaciones**: Deben ejecutarse antes de la transacción
- **Generación de tokens**: Pueden ejecutarse después de la transacción
- **Operaciones de archivos**: Si fallan, pueden dejar archivos huérfanos
- **Envío de emails**: No deben bloquear la transacción

### 2. Manejo de Errores en Transacciones
- Las excepciones dentro de `DB::transaction()` automáticamente hacen rollback
- Capturar excepciones específicas para dar mensajes de error apropiados
- Registrar errores para debugging

### 3. Transacciones Anidadas
- Laravel soporta transacciones anidadas
- Las transacciones anidadas usan "savepoints"
- Útil para operaciones complejas con múltiples niveles

### 4. Timeouts y Deadlocks
- Las transacciones pueden causar deadlocks si hay múltiples usuarios
- Considerar timeouts para transacciones largas
- Implementar retry logic para deadlocks

---

## Resumen de Transacciones Implementadas

| Controlador | Método | Transacciones Necesarias |
|------------|--------|-------------------------|
| AuthController | register() | ✅ Usuario + ONG/Empresa/Externo |
| EventoParticipacionController | inscribir() | ✅ Participación + Notificación |
| EventoParticipacionController | cancelar() | ⚠️ Solo elimina (no requiere) |
| MegaEventoController | participar() | ✅ Participación + Notificación |
| ProfileController | update() | ✅ Usuario + Entidad relacionada |
| EventController | store() | ⚠️ Evento + Imágenes (considerar) |
| EventController | update() | ⚠️ Evento + Imágenes (considerar) |

---

## Próximos Pasos

1. **Implementar transacciones** en los métodos identificados
2. **Probar** cada transacción con casos de éxito y fallo
3. **Documentar** el comportamiento transaccional
4. **Monitorear** deadlocks y timeouts en producción
5. **Optimizar** consultas dentro de transacciones para evitar bloqueos largos

---

## Notas Finales

Las transacciones son fundamentales para mantener la integridad de los datos en operaciones que involucran múltiples tablas. Sin embargo, deben usarse con cuidado para evitar:
- Bloqueos innecesarios
- Deadlocks
- Timeouts
- Degradación del rendimiento

Siempre prueba las transacciones en un ambiente de desarrollo antes de desplegar a producción.


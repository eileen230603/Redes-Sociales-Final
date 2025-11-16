# 📊 Análisis Completo del Proyecto Laravel

## 📋 Descripción General

Este es un **sistema de gestión de eventos** desarrollado en **Laravel 12** que permite a diferentes tipos de usuarios (ONGs, Empresas e Integrantes Externos) crear, gestionar y participar en eventos. El sistema utiliza **Laravel Sanctum** para autenticación API y **AdminLTE** para la interfaz de usuario.

---

## 🏗️ Arquitectura y Tecnologías

### Stack Tecnológico
- **Backend**: Laravel 12 (PHP 8.2+)
- **Autenticación**: Laravel Sanctum (API Tokens)
- **Frontend**: Blade Templates + AdminLTE 3.15
- **Base de Datos**: SQLite (por defecto), soporta MySQL/MariaDB/PostgreSQL
- **Build Tools**: Vite 7.0.7
- **CSS Framework**: Tailwind CSS 4.0

### Estructura del Proyecto
```
uni2_proyecto_final/
├── app/
│   ├── Http/Controllers/     # Controladores (API y Web)
│   ├── Models/                # Modelos Eloquent
│   └── Providers/             # Service Providers
├── database/
│   ├── migrations/            # Migraciones de BD
│   └── seeders/               # Seeders
├── resources/
│   ├── views/                 # Vistas Blade
│   ├── js/                    # JavaScript
│   └── css/                   # Estilos
└── routes/
    ├── web.php                # Rutas web
    └── api.php                # Rutas API
```

---

## 🗄️ Estructura de Base de Datos

### Tablas Principales

#### 1. **usuarios** (Tabla central)
- `id_usuario` (PK)
- `nombre_usuario` (unique)
- `correo_electronico` (unique)
- `contrasena` (hasheada)
- `tipo_usuario` (CHECK: 'Super admin', 'Integrante externo', 'ONG', 'Empresa')
- `activo` (boolean)
- `fecha_registro`

#### 2. **ongs** (Relación 1:1 con usuarios)
- `user_id` (PK, FK → usuarios.id_usuario)
- `nombre_ong`
- `NIT`, `telefono`, `direccion`, `sitio_web`, `descripcion`

#### 3. **empresas** (Relación 1:1 con usuarios)
- `user_id` (PK, FK → usuarios.id_usuario)
- `nombre_empresa`
- `NIT`, `telefono`, `direccion`, `sitio_web`, `descripcion`

#### 4. **integrantes_externos** (Relación 1:1 con usuarios)
- `user_id` (PK, FK → usuarios.id_usuario)
- `nombres`, `apellidos`
- `fecha_nacimiento`, `email`, `phone_number`, `descripcion`

#### 5. **eventos** (Creados por ONGs)
- `id` (PK)
- `ong_id` (FK → ongs.user_id)
- `titulo`, `descripcion`, `tipo_evento`
- `fecha_inicio`, `fecha_fin`, `fecha_limite_inscripcion`
- `capacidad_maxima`, `inscripcion_abierta`
- `estado` (enum: 'borrador', 'publicado', 'cancelado')
- `lat`, `lng`, `direccion`, `ciudad`
- `imagenes` (JSON), `patrocinadores` (JSON), `auspiciadores` (JSON), `invitados` (JSON)
- `timestamps`

#### 6. **evento_participaciones** (Tabla pivot)
- `id` (PK)
- `evento_id` (FK → eventos.id)
- `externo_id` (FK → usuarios.id_usuario)
- `asistio` (boolean)
- `puntos` (integer)
- `unique(evento_id, externo_id)`

#### 7. **mega_eventos** (Eventos especiales)
- `mega_evento_id` (PK)
- `ong_organizadora_principal` (FK → ongs.user_id)
- `titulo`, `descripcion`
- `fecha_inicio`, `fecha_fin`
- `ubicacion`, `categoria`, `estado`
- `capacidad_maxima`, `es_publico`, `activo`

### Relaciones Clave
```
User (1) ──< (1) Ong
User (1) ──< (1) Empresa
User (1) ──< (1) IntegranteExterno
Ong (1) ──< (*) Evento
Evento (*) ──< (*) User (a través de evento_participaciones)
Ong (1) ──< (*) MegaEvento
```

---

## 📦 Modelos Eloquent

### User Model
- **Tabla**: `usuarios`
- **PK**: `id_usuario`
- **Relaciones**:
  - `hasOne(Ong)`
  - `hasOne(Empresa)`
  - `hasOne(IntegranteExterno)`
- **Métodos de rol**: `esOng()`, `esEmpresa()`, `esIntegranteExterno()`, `esSuperAdmin()`
- **Autenticación personalizada**: Usa `contrasena` en lugar de `password`

### Evento Model
- **Casts**: Fechas a `datetime`, arrays JSON (`imagenes`, `patrocinadores`, `invitados`)
- **Relaciones**:
  - `belongsTo(Ong)`
  - `hasMany(EventoParticipacion)`
  - `belongsToMany(User)` (a través de `evento_participaciones`)

### EventoParticipacion Model
- **Tabla pivot** entre `eventos` y `usuarios`
- Campos: `asistio`, `puntos`

---

## 🎮 Controladores y Endpoints

### API Routes (`routes/api.php`)

#### Autenticación (Público)
- `POST /api/auth/register` - Registro de usuarios
- `POST /api/auth/login` - Inicio de sesión

#### Rutas Protegidas (Sanctum)
- `POST /api/auth/logout` - Cerrar sesión

#### Eventos
- `GET /api/eventos` - Listar eventos publicados
- `GET /api/eventos/ong/{ongId}` - Eventos de una ONG
- `GET /api/eventos/detalle/{id}` - Detalle de evento
- `POST /api/eventos` - Crear evento
- `PUT /api/eventos/{id}` - Actualizar evento
- `DELETE /api/eventos/{id}` - Eliminar evento

#### Participaciones
- `POST /api/participaciones/inscribir` - Inscribirse a evento
- `POST /api/participaciones/cancelar` - Cancelar inscripción
- `GET /api/participaciones/mis-eventos` - Mis eventos inscritos

### Web Routes (`routes/web.php`)

#### Vistas Públicas
- `/` → `auth.login`
- `/login` → `auth.login`
- `/register-ong` → `auth.register-ong`
- `/register-empresa` → `auth.register-empresa`
- `/register-externo` → `auth.register-externo`

#### Vistas de Home
- `/home-publica` → `home-publica`
- `/home-ong` → `home-ong`
- `/home-empresa` → `home-empresa`
- `/home-externo` → `externo.home`

#### Módulo ONG
- `/ong/eventos` → `ong.eventos.index`
- `/ong/eventos/crear` → `ong.eventos.create`
- `/ong/eventos/{id}/editar` → `ong.eventos.edit`
- `/ong/eventos/{id}/detalle` → `ong.eventos.show`

#### Módulo Externo
- `/externo/eventos` → `externo.eventos.index`
- `/externo/eventos/{id}/detalle` → `externo.eventos.show`

---

## 🔐 Autenticación y Seguridad

### Laravel Sanctum
- Autenticación basada en tokens
- Tokens generados en login/registro
- Middleware `auth:sanctum` protege rutas API

### AuthController
- **register()**: Crea usuario base + registro específico según tipo
- **login()**: Valida credenciales y genera token
- Validaciones completas con mensajes de error

### Seguridad
- ✅ Contraseñas hasheadas con `Hash::make()`
- ✅ Validación de datos de entrada
- ✅ Verificación de usuario activo
- ⚠️ **Falta**: Método `logout()` en AuthController (ruta definida pero no implementada)

---

## 🎯 Funcionalidades Principales

### 1. Gestión de Usuarios
- Registro diferenciado por tipo (ONG, Empresa, Integrante Externo)
- Login con email y contraseña
- Sistema de roles basado en `tipo_usuario`

### 2. Gestión de Eventos (ONGs)
- Crear eventos con información completa
- Estados: borrador, publicado, cancelado
- Campos JSON para: imágenes, patrocinadores, auspiciadores, invitados
- Geolocalización (lat/lng)
- Control de capacidad máxima
- Fechas de inicio, fin y límite de inscripción

### 3. Participación en Eventos (Externos)
- Ver eventos publicados
- Inscribirse a eventos
- Cancelar inscripción
- Ver mis eventos inscritos
- Validación de cupos disponibles
- Validación de inscripciones abiertas

### 4. Mega Eventos
- Modelo definido pero funcionalidad no implementada completamente
- Relación con ONG organizadora principal

---

## ✅ Puntos Fuertes

1. **Arquitectura clara**: Separación entre API y Web
2. **Modelos bien estructurados**: Relaciones Eloquent correctas
3. **Validaciones**: Validación de datos en registro y creación
4. **Flexibilidad**: Soporte para múltiples tipos de usuarios
5. **Escalabilidad**: Estructura preparada para crecer
6. **Seguridad básica**: Sanctum implementado, contraseñas hasheadas

---

## ⚠️ Áreas de Mejora y Problemas Detectados

### 1. **Falta implementar logout**
```php
// En routes/api.php línea 16:
Route::post('/auth/logout', [AuthController::class, 'logout']);
// Pero el método logout() no existe en AuthController
```

### 2. **Métodos duplicados en EventController**
- `participar()`, `cancelar()`, `misEventos()` están en `EventController`
- Pero también existe `EventoParticipacionController` con los mismos métodos
- **Recomendación**: Eliminar duplicados, usar solo `EventoParticipacionController`

### 3. **Falta validación en EventController**
- `store()` y `update()` no tienen validación de datos
- **Riesgo**: Datos inválidos pueden causar errores

### 4. **Manejo de errores inconsistente**
- Algunos métodos usan try-catch, otros no
- Algunos retornan información detallada del error (archivo, línea), otros no

### 5. **Falta autorización**
- No hay verificación de que un usuario solo pueda editar/eliminar sus propios eventos
- Cualquier usuario autenticado puede modificar cualquier evento

### 6. **MegaEventos sin implementar**
- Modelo existe pero no hay controladores ni rutas

### 7. **Timestamps inconsistentes**
- `User`: `$timestamps = false`
- `IntegranteExterno`: `$timestamps = false`
- `MegaEvento`: `$timestamps = false`
- Otros modelos usan timestamps

### 8. **Falta paginación**
- `indexAll()` y `indexByOng()` retornan todos los registros
- Puede ser lento con muchos eventos

### 9. **Campos JSON sin validación**
- `patrocinadores`, `invitados`, `imagenes` se guardan como JSON sin validar estructura

### 10. **Falta relación en MegaEvento**
- No hay relaciones con otras tablas (participantes, patrocinadores, etc.)

---

## 🔧 Recomendaciones de Mejora

### Prioridad Alta

1. **Implementar logout()**
```php
public function logout(Request $request)
{
    $request->user()->currentAccessToken()->delete();
    return response()->json(['success' => true, 'message' => 'Sesión cerrada']);
}
```

2. **Agregar validación a EventController**
```php
$validator = Validator::make($request->all(), [
    'titulo' => 'required|string|max:255',
    'ong_id' => 'required|exists:ongs,user_id',
    'fecha_inicio' => 'required|date|after:now',
    // ... más validaciones
]);
```

3. **Implementar autorización (Policies)**
```php
// EventPolicy
public function update(User $user, Evento $evento)
{
    return $user->id_usuario === $evento->ong_id;
}
```

4. **Eliminar métodos duplicados**
- Remover `participar()`, `cancelar()`, `misEventos()` de `EventController`
- Usar solo `EventoParticipacionController`

### Prioridad Media

5. **Agregar paginación**
```php
$eventos = Evento::where('estado', 'publicado')
    ->orderBy('fecha_inicio', 'asc')
    ->paginate(15);
```

6. **Estandarizar manejo de errores**
- Crear un trait o helper para respuestas de error consistentes

7. **Validar estructura JSON**
- Crear reglas de validación para arrays JSON

8. **Completar funcionalidad MegaEventos**
- Crear controlador y rutas
- Implementar relaciones faltantes

### Prioridad Baja

9. **Agregar tests**
- Tests unitarios para modelos
- Tests de integración para API

10. **Documentación API**
- Considerar Swagger/OpenAPI

11. **Optimizaciones**
- Eager loading en relaciones
- Índices en BD para búsquedas frecuentes

---

## 📊 Resumen de Archivos Clave

### Controladores
- `AuthController.php` - Autenticación (register, login)
- `EventController.php` - CRUD de eventos
- `EventoParticipacionController.php` - Participaciones
- `MegaEventoController.php` - Existe pero sin uso aparente

### Modelos
- `User.php` - Usuario base
- `Ong.php`, `Empresa.php`, `IntegranteExterno.php` - Tipos de usuario
- `Evento.php` - Eventos
- `EventoParticipacion.php` - Participaciones
- `MegaEvento.php` - Mega eventos

### Migraciones
- 16 migraciones en total
- Estructura completa de BD definida
- Foreign keys y constraints implementadas

---

## 🎓 Conclusión

El proyecto muestra una **base sólida** para un sistema de gestión de eventos con:
- ✅ Arquitectura MVC bien estructurada
- ✅ Separación clara entre API y Web
- ✅ Modelos y relaciones bien definidas
- ✅ Autenticación implementada

Sin embargo, necesita **mejoras en seguridad, validación y completar funcionalidades** antes de ser considerado producción-ready.

**Estado actual**: ⚠️ **Beta/Desarrollo** - Funcional pero necesita refinamiento.

---

*Análisis generado el: $(date)*


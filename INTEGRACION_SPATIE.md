# 📋 INTEGRACIÓN DE SPATIE LARAVEL PERMISSION

## 📦 1. INSTALACIÓN DEL PAQUETE

**Archivo:** `composer.json`
- **Línea 18:** `"spatie/laravel-permission": "^6.23"`
- **Estado:** ✅ Instalado

---

## 🗄️ 2. BASE DE DATOS - MIGRACIONES

**Archivo:** `database/migrations/2025_12_11_191147_create_permission_tables.php`
- **Descripción:** Migración que crea las tablas necesarias para Spatie
- **Tablas creadas:**
  - `permissions` - Almacena los permisos
  - `roles` - Almacena los roles
  - `model_has_permissions` - Relación usuarios-permisos
  - `model_has_roles` - Relación usuarios-roles
  - `role_has_permissions` - Relación roles-permisos
- **Estado:** ✅ Ejecutada

---

## ⚙️ 3. CONFIGURACIÓN

**Archivo:** `config/permission.php`
- **Descripción:** Archivo de configuración de Spatie
- **Cómo se creó:** `php artisan vendor:publish --tag="permission-config"`
- **Estado:** ✅ Publicado y configurado

---

## 👤 4. MODELO USER

**Archivo:** `app/Models/User.php`

### Cambios realizados:

**Línea 8:** Import del trait
```php
use Spatie\Permission\Traits\HasRoles;
```

**Línea 12:** Uso del trait
```php
use HasApiTokens, Notifiable, HasRoles;
```

**Funcionalidad agregada:**
- El modelo User ahora puede usar métodos de Spatie:
  - `$user->assignRole('ONG')`
  - `$user->hasRole('ONG')`
  - `$user->can('eventos.crear')`
  - `$user->hasPermissionTo('eventos.crear')`
  - `$user->roles` - Obtener todos los roles
  - `$user->permissions` - Obtener todos los permisos

**Estado:** ✅ Integrado

---

## 🌱 5. SEEDERS - ROLES Y PERMISOS

**Archivo:** `database/seeders/RolesAndPermissionsSeeder.php`
- **Descripción:** Crea todos los roles y permisos del sistema
- **Permisos creados:** 47 permisos organizados en categorías:
  - Eventos (12 permisos)
  - Mega Eventos (11 permisos)
  - Participaciones (5 permisos)
  - Reportes (7 permisos)
  - Notificaciones (2 permisos)
  - Configuración (4 permisos)
  - Usuarios y Voluntarios (4 permisos)
  - Perfil (2 permisos)

- **Roles creados:** 4 roles
  - **Super Admin:** Todos los permisos
  - **ONG:** Permisos de gestión completos
  - **Empresa:** Permisos limitados
  - **Integrante Externo:** Permisos básicos

**Estado:** ✅ Creado

---

## 🔄 6. DATABASE SEEDER

**Archivo:** `database/seeders/DatabaseSeeder.php`

### Cambios realizados:

**Línea 14-16:** Incluye el seeder de roles
```php
$this->call([
    RolesAndPermissionsSeeder::class,
]);
```

**Línea 35-37:** Asigna rol al usuario demo
```php
$user = \App\Models\User::find($id);
if ($user) {
    $user->assignRole('ONG');
}
```

**Estado:** ✅ Actualizado

---

## 🔐 7. CONTROLADOR DE AUTENTICACIÓN

**Archivo:** `app/Http/Controllers/Auth/AuthController.php`

### Cambios realizados:

**Línea 14:** Import del modelo Role
```php
use Spatie\Permission\Models\Role;
```

**Líneas 96-109:** Asignación automática de roles al registrar usuarios
```php
// 3. Asignar rol de Spatie según tipo de usuario
$tipoUsuarioToRole = [
    'ONG' => 'ONG',
    'Empresa' => 'Empresa',
    'Integrante externo' => 'Integrante Externo',
];

$nombreRol = $tipoUsuarioToRole[$user->tipo_usuario] ?? null;
if ($nombreRol) {
    try {
        $rol = Role::findByName($nombreRol, 'web');
        $user->assignRole($rol);
    } catch (\Exception $e) {
        \Log::warning("Rol '{$nombreRol}' no encontrado para usuario {$user->id_usuario}");
    }
}
```

**Funcionalidad:** Cuando un usuario se registra, automáticamente se le asigna el rol correspondiente según su `tipo_usuario`

**Estado:** ✅ Integrado

---

## 🛣️ 8. RUTAS API - MIDDLEWARE DE PERMISOS

**Archivo:** `routes/api.php`

### Rutas protegidas con middleware de Spatie:

#### **EVENTOS** (Líneas 49-83)
- `permission:eventos.ver` - Ver eventos
- `permission:eventos.crear` - Crear eventos
- `permission:eventos.editar` - Editar eventos
- `permission:eventos.eliminar` - Eliminar eventos
- `permission:eventos.gestionar` - Gestionar eventos
- `permission:eventos.patrocinar` - Patrocinar eventos
- `permission:eventos.exportar-reportes` - Exportar reportes

#### **EMPRESAS PARTICIPANTES** (Líneas 86-95)
- `permission:eventos.gestionar` - Asignar/remover empresas
- `permission:eventos.ver-participantes` - Ver participantes

#### **PARTICIPACIONES** (Líneas 105-113)
- `permission:eventos.inscribirse` - Inscribirse en eventos
- `permission:participaciones.ver-mis-participaciones` - Ver mis participaciones
- `permission:participaciones.aprobar` - Aprobar participaciones
- `permission:participaciones.rechazar` - Rechazar participaciones

#### **CONTROL DE ASISTENCIA** (Líneas 130-135)
- `permission:eventos.control-asistencia` - Controlar asistencia
- `permission:eventos.exportar-reportes` - Exportar reportes de asistencia

#### **REACCIONES Y COMPARTIDOS** (Líneas 137-143)
- `permission:eventos.reaccionar` - Reaccionar a eventos
- `permission:eventos.compartir` - Compartir eventos

#### **MEGA EVENTOS** (Líneas 246-287)
- `permission:mega-eventos.ver` - Ver mega eventos
- `permission:mega-eventos.crear` - Crear mega eventos
- `permission:mega-eventos.editar` - Editar mega eventos
- `permission:mega-eventos.eliminar` - Eliminar mega eventos
- `permission:mega-eventos.gestionar` - Gestionar mega eventos
- `permission:mega-eventos.participar` - Participar en mega eventos
- `permission:mega-eventos.reaccionar` - Reaccionar a mega eventos
- `permission:mega-eventos.compartir` - Compartir mega eventos
- `permission:mega-eventos.control-asistencia` - Controlar asistencia
- `permission:mega-eventos.exportar-reportes` - Exportar reportes

#### **CONFIGURACIÓN** (Líneas 289-299)
- `permission:configuracion.ver` - Ver configuración
- `permission:configuracion.gestionar` - Gestionar configuración

#### **PARAMETRIZACIONES** (Líneas 302-351)
- `permission:parametrizaciones.ver` - Ver parametrizaciones
- `permission:parametrizaciones.gestionar` - Gestionar parametrizaciones

**Estado:** ✅ Integrado en múltiples rutas

---

## 🖥️ 9. COMANDO ARTISAN

**Archivo:** `app/Console/Commands/AssignRolesToUsers.php`
- **Comando:** `php artisan spatie:assign-roles`
- **Descripción:** Asigna roles de Spatie a usuarios existentes basándose en su `tipo_usuario`
- **Funcionalidad:**
  - Lee todos los usuarios
  - Mapea `tipo_usuario` a roles de Spatie
  - Asigna el rol correspondiente
  - Muestra estadísticas del proceso

**Estado:** ✅ Creado y funcional

---

## 📊 RESUMEN DE INTEGRACIÓN

### Archivos modificados/creados:

1. ✅ `composer.json` - Dependencia agregada
2. ✅ `database/migrations/2025_12_11_191147_create_permission_tables.php` - Migración creada
3. ✅ `config/permission.php` - Configuración publicada
4. ✅ `app/Models/User.php` - Trait HasRoles agregado
5. ✅ `database/seeders/RolesAndPermissionsSeeder.php` - Seeder creado
6. ✅ `database/seeders/DatabaseSeeder.php` - Actualizado
7. ✅ `app/Http/Controllers/Auth/AuthController.php` - Asignación automática de roles
8. ✅ `routes/api.php` - Middleware de permisos en múltiples rutas
9. ✅ `app/Console/Commands/AssignRolesToUsers.php` - Comando creado

### Funcionalidades implementadas:

- ✅ Sistema de roles (Super Admin, ONG, Empresa, Integrante Externo)
- ✅ Sistema de permisos (47 permisos granulares)
- ✅ Asignación automática de roles al registrar usuarios
- ✅ Protección de rutas con middleware de permisos
- ✅ Comando para migrar usuarios existentes
- ✅ Cache de permisos para mejor rendimiento

### Estado general: ✅ COMPLETAMENTE INTEGRADO

---

## 🚀 CÓMO USAR SPATIE EN EL PROYECTO

### En Controladores:
```php
if (auth()->user()->can('eventos.crear')) {
    // Permitir crear evento
}

if (auth()->user()->hasRole('ONG')) {
    // Usuario es ONG
}
```

### En Rutas:
```php
Route::post('/eventos', [EventController::class, 'store'])
    ->middleware('permission:eventos.crear');
```

### En Vistas Blade:
```blade
@can('eventos.crear')
    <a href="/eventos/crear">Crear Evento</a>
@endcan

@role('ONG')
    <div>Contenido solo para ONG</div>
@endrole
```

---

## 📝 NOTAS IMPORTANTES

1. **Compatibilidad:** El sistema antiguo basado en `tipo_usuario` sigue funcionando. Spatie se agregó como complemento.

2. **Reportes:** Las rutas de reportes NO usan middleware de Spatie porque el controlador ya valida que el usuario sea tipo ONG.

3. **Migración:** Para asignar roles a usuarios existentes, ejecuta:
   ```bash
   php artisan spatie:assign-roles
   ```

4. **Cache:** Spatie cachea los permisos automáticamente para mejor rendimiento. Si cambias permisos, ejecuta:
   ```bash
   php artisan permission:cache-reset
   ```


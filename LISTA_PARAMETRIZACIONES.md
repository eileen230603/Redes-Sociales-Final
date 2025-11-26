# Lista de Parametrizaciones del Sistema

Este documento lista todas las parametrizaciones (catálogos y configuraciones) disponibles en el sistema.

## 📊 RESUMEN GENERAL

- **Total de Catálogos de Parametrización:** 8
- **Modelo de Parámetros de Configuración:** 1 (Parametro)
- **Total de Endpoints API:** 32

---

## 📋 CATÁLOGOS DE PARAMETRIZACIÓN

### 1. 📅 **TIPOS DE EVENTO** (`tipos_evento`)

**Modelo:** `App\Models\TipoEvento`  
**Controlador:** `ParametrizacionController::tiposEvento()`  
**Rutas API:** `/api/parametrizaciones/tipos-evento`

**Valores por Defecto (Seeder):**
1. **Conferencia** (`conferencia`)
   - Icono: `fas fa-microphone`
   - Color: `primary`
   - Orden: 1

2. **Taller** (`taller`)
   - Icono: `fas fa-tools`
   - Color: `info`
   - Orden: 2

3. **Seminario** (`seminario`)
   - Icono: `fas fa-graduation-cap`
   - Color: `success`
   - Orden: 3

4. **Voluntariado** (`voluntariado`)
   - Icono: `fas fa-hands-helping`
   - Color: `warning`
   - Orden: 4

5. **Cultural** (`cultural`)
   - Icono: `fas fa-theater-masks`
   - Color: `purple`
   - Orden: 5

6. **Deportivo** (`deportivo`)
   - Icono: `fas fa-running`
   - Color: `danger`
   - Orden: 6

7. **Otro** (`otro`)
   - Icono: `fas fa-calendar`
   - Color: `secondary`
   - Orden: 7

**Operaciones CRUD:**
- ✅ GET `/api/parametrizaciones/tipos-evento` - Listar
- ✅ POST `/api/parametrizaciones/tipos-evento` - Crear
- ✅ PUT `/api/parametrizaciones/tipos-evento/{id}` - Actualizar
- ✅ DELETE `/api/parametrizaciones/tipos-evento/{id}` - Eliminar

---

### 2. 🎯 **CATEGORÍAS DE MEGA EVENTOS** (`categorias_mega_eventos`)

**Modelo:** `App\Models\CategoriaMegaEvento`  
**Controlador:** `ParametrizacionController::categoriasMegaEvento()`  
**Rutas API:** `/api/parametrizaciones/categorias-mega-evento`

**Valores por Defecto (Seeder):**
1. **Social** (`social`)
   - Icono: `fas fa-users`
   - Color: `primary`
   - Orden: 1

2. **Cultural** (`cultural`)
   - Icono: `fas fa-theater-masks`
   - Color: `purple`
   - Orden: 2

3. **Deportivo** (`deportivo`)
   - Icono: `fas fa-running`
   - Color: `danger`
   - Orden: 3

4. **Educativo** (`educativo`)
   - Icono: `fas fa-graduation-cap`
   - Color: `info`
   - Orden: 4

5. **Benéfico** (`benefico`)
   - Icono: `fas fa-heart`
   - Color: `danger`
   - Orden: 5

6. **Ambiental** (`ambiental`)
   - Icono: `fas fa-leaf`
   - Color: `success`
   - Orden: 6

7. **Otro** (`otro`)
   - Icono: `fas fa-calendar`
   - Color: `secondary`
   - Orden: 7

**Operaciones CRUD:**
- ✅ GET `/api/parametrizaciones/categorias-mega-evento` - Listar
- ✅ POST `/api/parametrizaciones/categorias-mega-evento` - Crear
- ✅ PUT `/api/parametrizaciones/categorias-mega-evento/{id}` - Actualizar
- ✅ DELETE `/api/parametrizaciones/categorias-mega-evento/{id}` - Eliminar

---

### 3. 🏙️ **CIUDADES** (`ciudades`)

**Modelo:** `App\Models\Ciudad`  
**Controlador:** `ParametrizacionController::ciudades()`  
**Rutas API:** `/api/parametrizaciones/ciudades`

**Valores por Defecto (Seeder) - Principales ciudades de Bolivia:**
1. **Santa Cruz de la Sierra** (Santa Cruz)
2. **La Paz** (La Paz)
3. **Cochabamba** (Cochabamba)
4. **Sucre** (Chuquisaca)
5. **Oruro** (Oruro)
6. **Potosí** (Potosí)
7. **Tarija** (Tarija)
8. **Trinidad** (Beni)
9. **Cobija** (Pando)

**Campos:**
- `nombre` - Nombre de la ciudad
- `departamento` - Departamento al que pertenece
- `pais` - País (por defecto: Bolivia)
- `lat` / `lng` - Coordenadas geográficas
- `codigo_postal` - Código postal (opcional)
- `activo` - Estado activo/inactivo

**Operaciones CRUD:**
- ✅ GET `/api/parametrizaciones/ciudades` - Listar (con filtros: buscar, departamento, pais)
- ✅ POST `/api/parametrizaciones/ciudades` - Crear
- ✅ PUT `/api/parametrizaciones/ciudades/{id}` - Actualizar
- ✅ DELETE `/api/parametrizaciones/ciudades/{id}` - Eliminar

---

### 4. 📍 **LUGARES** (`lugares`)

**Modelo:** `App\Models\Lugar`  
**Controlador:** `ParametrizacionController::lugares()`  
**Rutas API:** `/api/parametrizaciones/lugares`

**Campos:**
- `nombre` - Nombre del lugar
- `direccion` - Dirección completa
- `ciudad_id` - Relación con Ciudad
- `lat` / `lng` - Coordenadas geográficas
- `capacidad` - Capacidad máxima (opcional)
- `descripcion` - Descripción del lugar
- `telefono` - Teléfono de contacto
- `email` - Email de contacto
- `sitio_web` - Sitio web
- `activo` - Estado activo/inactivo

**Operaciones CRUD:**
- ✅ GET `/api/parametrizaciones/lugares` - Listar (con filtros: buscar, ciudad_id)
- ✅ POST `/api/parametrizaciones/lugares` - Crear
- ✅ PUT `/api/parametrizaciones/lugares/{id}` - Actualizar
- ✅ DELETE `/api/parametrizaciones/lugares/{id}` - Eliminar

---

### 5. ✅ **ESTADOS DE PARTICIPACIÓN** (`estados_participacion`)

**Modelo:** `App\Models\EstadoParticipacion`  
**Controlador:** `ParametrizacionController::estadosParticipacion()`  
**Rutas API:** `/api/parametrizaciones/estados-participacion`

**Valores por Defecto (Seeder):**
1. **Pendiente** (`pendiente`)
   - Color: `warning`
   - Icono: `fas fa-clock`
   - Orden: 1

2. **Aprobada** (`aprobada`)
   - Color: `success`
   - Icono: `fas fa-check-circle`
   - Orden: 2

3. **Rechazada** (`rechazada`)
   - Color: `danger`
   - Icono: `fas fa-times-circle`
   - Orden: 3

**Operaciones CRUD:**
- ✅ GET `/api/parametrizaciones/estados-participacion` - Listar
- ✅ POST `/api/parametrizaciones/estados-participacion` - Crear
- ✅ PUT `/api/parametrizaciones/estados-participacion/{id}` - Actualizar
- ✅ DELETE `/api/parametrizaciones/estados-participacion/{id}` - Eliminar

---

### 6. 🔔 **TIPOS DE NOTIFICACIÓN** (`tipos_notificacion`)

**Modelo:** `App\Models\TipoNotificacion`  
**Controlador:** `ParametrizacionController::tiposNotificacion()`  
**Rutas API:** `/api/parametrizaciones/tipos-notificacion`

**Valores por Defecto (Seeder):**
1. **Reacción a Evento** (`reaccion_evento`)
   - Plantilla: `{usuario} reaccionó a tu evento "{evento}"`
   - Icono: `fas fa-heart`
   - Color: `danger`

2. **Nueva Participación** (`nueva_participacion`)
   - Plantilla: `{usuario} se inscribió a tu evento "{evento}"`
   - Icono: `fas fa-user-plus`
   - Color: `info`

**Campos:**
- `codigo` - Código único
- `nombre` - Nombre descriptivo
- `descripcion` - Descripción
- `plantilla_mensaje` - Plantilla del mensaje con variables
- `icono` - Icono FontAwesome
- `color` - Color del badge
- `activo` - Estado activo/inactivo

**Operaciones CRUD:**
- ✅ GET `/api/parametrizaciones/tipos-notificacion` - Listar
- ✅ POST `/api/parametrizaciones/tipos-notificacion` - Crear
- ✅ PUT `/api/parametrizaciones/tipos-notificacion/{id}` - Actualizar
- ✅ DELETE `/api/parametrizaciones/tipos-notificacion/{id}` - Eliminar

---

### 7. 📊 **ESTADOS DE EVENTO** (`estados_evento`)

**Modelo:** `App\Models\EstadoEvento`  
**Controlador:** `ParametrizacionController::estadosEvento()`  
**Rutas API:** `/api/parametrizaciones/estados-evento`

**Valores por Defecto (Seeder):**

**Para Eventos Regulares:**
1. **Borrador** (`borrador`)
   - Tipo: `evento`
   - Color: `secondary`
   - Icono: `fas fa-edit`
   - Orden: 1

2. **Publicado** (`publicado`)
   - Tipo: `evento`
   - Color: `success`
   - Icono: `fas fa-check`
   - Orden: 2

3. **Cancelado** (`cancelado`)
   - Tipo: `evento`
   - Color: `danger`
   - Icono: `fas fa-times`
   - Orden: 3

**Para Mega Eventos:**
4. **En Planificación** (`planificacion`)
   - Tipo: `mega_evento`
   - Color: `info`
   - Icono: `fas fa-calendar-alt`
   - Orden: 1

5. **Activo** (`activo`)
   - Tipo: `mega_evento`
   - Color: `success`
   - Icono: `fas fa-play`
   - Orden: 2

6. **En Curso** (`en_curso`)
   - Tipo: `mega_evento`
   - Color: `warning`
   - Icono: `fas fa-spinner`
   - Orden: 3

7. **Finalizado** (`finalizado`)
   - Tipo: `mega_evento`
   - Color: `secondary`
   - Icono: `fas fa-check-circle`
   - Orden: 4

8. **Cancelado** (`cancelado_mega`)
   - Tipo: `mega_evento`
   - Color: `danger`
   - Icono: `fas fa-times-circle`
   - Orden: 5

**Campos:**
- `codigo` - Código único
- `nombre` - Nombre descriptivo
- `descripcion` - Descripción
- `tipo` - Tipo: `evento`, `mega_evento`, o `ambos`
- `color` - Color del badge
- `icono` - Icono FontAwesome
- `orden` - Orden de visualización
- `activo` - Estado activo/inactivo

**Operaciones CRUD:**
- ✅ GET `/api/parametrizaciones/estados-evento` - Listar (con filtro: tipo)
- ✅ POST `/api/parametrizaciones/estados-evento` - Crear
- ✅ PUT `/api/parametrizaciones/estados-evento/{id}` - Actualizar
- ✅ DELETE `/api/parametrizaciones/estados-evento/{id}` - Eliminar

---

### 8. 👥 **TIPOS DE USUARIO** (`tipos_usuario`)

**Modelo:** `App\Models\TipoUsuario`  
**Controlador:** `ParametrizacionController::tiposUsuario()`  
**Rutas API:** `/api/parametrizaciones/tipos-usuario`

**Valores por Defecto (Seeder):**
1. **Super Admin** (`super_admin`)
   - Descripción: Administrador del sistema
   - Permisos: `['*']` (todos)

2. **ONG** (`ong`)
   - Descripción: Organización No Gubernamental
   - Permisos: `['eventos.*', 'mega_eventos.*', 'participaciones.*', 'notificaciones.*']`

3. **Empresa** (`empresa`)
   - Descripción: Empresa patrocinadora
   - Permisos: `['eventos.ver', 'eventos.patrocinar']`

4. **Integrante Externo** (`externo`)
   - Descripción: Usuario externo o voluntario
   - Permisos: `['eventos.ver', 'eventos.inscribirse', 'eventos.reaccionar']`

**Campos:**
- `codigo` - Código único
- `nombre` - Nombre descriptivo
- `descripcion` - Descripción
- `permisos_default` - Array de permisos por defecto
- `activo` - Estado activo/inactivo

**Operaciones CRUD:**
- ✅ GET `/api/parametrizaciones/tipos-usuario` - Listar
- ✅ POST `/api/parametrizaciones/tipos-usuario` - Crear
- ✅ PUT `/api/parametrizaciones/tipos-usuario/{id}` - Actualizar
- ✅ DELETE `/api/parametrizaciones/tipos-usuario/{id}` - Eliminar

---

## ⚙️ PARÁMETROS DE CONFIGURACIÓN DEL SISTEMA

### 9. 🔧 **PARÁMETROS** (`parametros`)

**Modelo:** `App\Models\Parametro`  
**Controlador:** `ConfiguracionController`  
**Rutas API:** `/api/configuracion/parametros`

**Descripción:**
Sistema de parámetros de configuración del sistema que permite almacenar valores configurables como:
- Límites del sistema (ej: máximo de eventos por ONG)
- Configuraciones de notificaciones
- Configuraciones generales
- Valores por defecto del sistema

**Campos:**
- `codigo` - Código único del parámetro (ej: `max_eventos_por_ong`)
- `nombre` - Nombre descriptivo
- `descripcion` - Descripción detallada
- `categoria` - Categoría: `general`, `eventos`, `usuarios`, `notificaciones`, etc.
- `tipo` - Tipo: `texto`, `numero`, `booleano`, `json`, `fecha`
- `valor` - Valor actual del parámetro
- `valor_defecto` - Valor por defecto
- `opciones` - Opciones disponibles (JSON, para select, radio, etc.)
- `grupo` - Grupo al que pertenece (para agrupar en la UI)
- `orden` - Orden de visualización
- `editable` - Si el parámetro puede ser editado
- `visible` - Si el parámetro es visible en la UI
- `requerido` - Si el parámetro es requerido
- `validacion` - Reglas de validación adicionales
- `ayuda` - Texto de ayuda para el usuario

**Métodos Helper del Modelo:**
```php
// Obtener valor de parámetro
Parametro::obtener('max_eventos_por_ong', 10);

// Establecer valor de parámetro
Parametro::establecer('max_eventos_por_ong', 20);
```

**Operaciones CRUD:**
- ✅ GET `/api/configuracion/parametros` - Listar (con filtros: categoria, grupo, visible, editable, buscar)
- ✅ GET `/api/configuracion/parametros/{codigo}` - Obtener por código
- ✅ POST `/api/configuracion/parametros` - Crear
- ✅ PUT `/api/configuracion/parametros/{id}` - Actualizar
- ✅ DELETE `/api/configuracion/parametros/{id}` - Eliminar

---

## 📊 RESUMEN DE ENDPOINTS

### Endpoints de Parametrización (Catálogos)
- **Tipos de Evento:** 4 endpoints (GET, POST, PUT, DELETE)
- **Categorías Mega Eventos:** 4 endpoints
- **Ciudades:** 4 endpoints
- **Lugares:** 4 endpoints
- **Estados Participación:** 4 endpoints
- **Tipos Notificación:** 4 endpoints
- **Estados Evento:** 4 endpoints
- **Tipos Usuario:** 4 endpoints

**Total:** 32 endpoints de catálogos

### Endpoints de Configuración (Parámetros)
- **Parámetros:** 5 endpoints (GET listar, GET por código, POST, PUT, DELETE)

**Total:** 5 endpoints de configuración

---

## 🎯 CARACTERÍSTICAS COMUNES

### Filtros Comunes en Listados:
- `activo` - Filtrar por estado activo/inactivo
- `buscar` - Búsqueda por texto (nombre, descripción, etc.)

### Campos Comunes:
- `codigo` - Código único (en la mayoría)
- `nombre` - Nombre descriptivo
- `descripcion` - Descripción
- `activo` - Estado activo/inactivo
- `orden` - Orden de visualización (en la mayoría)
- `icono` - Icono FontAwesome (en algunos)
- `color` - Color del badge (en algunos)

### Soft Deletes:
- Todos los modelos usan `SoftDeletes` para eliminación suave

---

## 📝 NOTAS IMPORTANTES

1. **Seeder:** El archivo `ParametrizacionesSeeder.php` contiene los valores por defecto de todos los catálogos.

2. **Validaciones:** Todos los endpoints tienen validaciones completas antes de crear/actualizar.

3. **Relaciones:** 
   - `Lugar` tiene relación con `Ciudad`
   - `EstadoEvento` puede ser para `evento`, `mega_evento` o `ambos`

4. **Uso en el Sistema:**
   - Los catálogos se usan en formularios de creación/edición
   - Los parámetros se usan para configuraciones del sistema
   - Los valores por defecto se cargan automáticamente al ejecutar el seeder

---

## ✅ ESTADO DE IMPLEMENTACIÓN

- ✅ **8 Catálogos de Parametrización** - Completamente implementados
- ✅ **1 Sistema de Parámetros** - Completamente implementado
- ✅ **37 Endpoints API** - Todos funcionales
- ✅ **CRUD Completo** - Para todos los catálogos
- ✅ **Validaciones** - Implementadas en todos los endpoints
- ✅ **Soft Deletes** - Implementado en todos los modelos



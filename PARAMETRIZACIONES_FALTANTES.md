# 📋 Parametrizaciones Faltantes - Recomendaciones

**Fecha:** Diciembre 2024  
**Proyecto:** Sistema de Gestión de Eventos - Redes Sociales Final

---

## 🎯 Parametrizaciones Críticas Faltantes

### ❌ **1. Tipos de Evento** - **0% Implementado**
**Estado Actual:**
- Valores hardcodeados en vistas (`create.blade.php`, `edit.blade.php`)
- Opciones: conferencia, taller, seminario, voluntariado, cultural, deportivo, otro
- No hay tabla de catálogo
- No se puede agregar/editar tipos desde la interfaz

**Recomendación:**
- Crear tabla `tipos_evento` con campos:
  - `id`, `codigo`, `nombre`, `descripcion`, `icono`, `color`, `activo`, `orden`
- Crear CRUD para gestión de tipos
- Reemplazar selects hardcodeados por consulta dinámica
- Permitir activar/desactivar tipos sin eliminar eventos existentes

**Impacto:** Alto - Facilita personalización y mantenimiento

---

### ❌ **2. Categorías de Mega Eventos** - **0% Implementado**
**Estado Actual:**
- Valores hardcodeados en vistas
- Opciones: social, cultural, deportivo, educativo, benéfico, ambiental, otro
- Campo `categoria` es string libre en base de datos
- No hay validación ni catálogo

**Recomendación:**
- Crear tabla `categorias_mega_eventos` con campos:
  - `id`, `codigo`, `nombre`, `descripcion`, `icono`, `color`, `activo`, `orden`
- Crear CRUD para gestión de categorías
- Validar categoría en creación/edición de mega eventos
- Reemplazar selects hardcodeados

**Impacto:** Alto - Consistencia de datos y mejor filtrado

---

### ❌ **3. Ciudades / Ubicaciones** - **0% Implementado**
**Estado Actual:**
- Campo `ciudad` es texto libre en tabla `eventos`
- No hay catálogo de ciudades
- No hay validación ni normalización
- Dificulta reportes y filtros por ubicación

**Recomendación:**
- Crear tabla `ciudades` con campos:
  - `id`, `nombre`, `codigo_postal`, `departamento`, `pais`, `lat`, `lng`, `activo`
- Crear tabla `lugares` (lugares específicos) con campos:
  - `id`, `nombre`, `direccion`, `ciudad_id`, `lat`, `lng`, `capacidad`, `descripcion`, `activo`
- Crear CRUD para gestión de ciudades y lugares
- Reemplazar inputs de texto por selects con autocompletado
- Permitir reutilizar lugares frecuentes

**Impacto:** Muy Alto - Mejora reportes, filtros y experiencia de usuario

---

### ⚠️ **4. Estados de Participación** - **50% Implementado**
**Estado Actual:**
- Estados: pendiente, aprobada, rechazada (hardcodeados en código)
- Campo `estado` en `evento_participaciones` sin constraint
- No hay tabla de catálogo

**Recomendación:**
- Crear tabla `estados_participacion` con campos:
  - `id`, `codigo`, `nombre`, `descripcion`, `color`, `icono`, `orden`
- Agregar constraint FK en `evento_participaciones.estado`
- Permitir agregar nuevos estados (ej: "en espera", "confirmada")
- Validar transiciones de estado

**Impacto:** Medio - Mejora flexibilidad y reportes

---

### ❌ **5. Tipos de Notificaciones** - **0% Implementado**
**Estado Actual:**
- Campo `tipo` en tabla `notificaciones` es string libre
- Valores usados: 'reaccion_evento', 'nueva_participacion'
- No hay catálogo ni validación

**Recomendación:**
- Crear tabla `tipos_notificacion` con campos:
  - `id`, `codigo`, `nombre`, `descripcion`, `plantilla_mensaje`, `icono`, `color`, `activo`
- Agregar constraint FK en `notificaciones.tipo`
- Permitir personalizar plantillas de mensajes
- Facilitar agregar nuevos tipos de notificación

**Impacto:** Medio - Escalabilidad y personalización

---

### ❌ **6. Configuraciones del Sistema** - **Parcialmente Implementado**
**Estado Actual:**
- Existe tabla `parametros` y `ConfiguracionController`
- Pero no hay parámetros predefinidos para:
  - Límites de eventos por ONG
  - Tamaño máximo de imágenes
  - Días de anticipación para inscripciones
  - Configuración de notificaciones
  - Límites de capacidad
  - Tiempos de expiración de tokens

**Recomendación:**
- Crear seeder con parámetros estándar del sistema
- Categorías sugeridas:
  - `eventos`: max_eventos_por_ong, max_imagenes_por_evento, etc.
  - `usuarios`: max_intentos_login, tiempo_expiracion_token, etc.
  - `notificaciones`: activar_email, activar_push, etc.
  - `archivos`: max_tamano_imagen, formatos_permitidos, etc.
  - `inscripciones`: dias_anticipacion_min, dias_anticipacion_max, etc.

**Impacto:** Alto - Flexibilidad y configurabilidad del sistema

---

### ❌ **7. Estados de Evento (Expandido)** - **60% Implementado**
**Estado Actual:**
- Eventos: borrador, publicado, cancelado (ENUM)
- Mega Eventos: planificacion, activo, en_curso, finalizado, cancelado (string)
- No hay tabla de catálogo
- No se pueden agregar nuevos estados

**Recomendación:**
- Crear tabla `estados_evento` con campos:
  - `id`, `codigo`, `nombre`, `descripcion`, `tipo` (evento/mega_evento), `color`, `icono`, `orden`
- Permitir diferentes estados para eventos regulares y mega eventos
- Validar transiciones permitidas (ej: no se puede pasar de "finalizado" a "borrador")
- Reemplazar ENUMs por FKs

**Impacto:** Medio - Flexibilidad y consistencia

---

### ❌ **8. Tipos de Usuario (Expandido)** - **60% Implementado**
**Estado Actual:**
- Valores hardcodeados: 'Super admin', 'Integrante externo', 'ONG', 'Empresa'
- Constraint CHECK en base de datos
- No hay tabla de catálogo

**Recomendación:**
- Crear tabla `tipos_usuario` con campos:
  - `id`, `codigo`, `nombre`, `descripcion`, `permisos_default`, `activo`
- Reemplazar constraint CHECK por FK
- Permitir agregar nuevos tipos (ej: "Moderador", "Colaborador")
- Asociar permisos por tipo de usuario

**Impacto:** Alto - Escalabilidad y gestión de permisos

---

### ❌ **9. Categorías de Parámetros** - **Parcialmente Implementado**
**Estado Actual:**
- Campo `categoria` en tabla `parametros` es string libre
- No hay catálogo de categorías válidas
- No hay validación

**Recomendación:**
- Crear tabla `categorias_parametros` con campos:
  - `id`, `codigo`, `nombre`, `descripcion`, `icono`, `color`, `orden`
- Agregar constraint FK en `parametros.categoria`
- Facilitar agrupación y organización de parámetros

**Impacto:** Bajo - Mejora organización pero no crítico

---

### ❌ **10. Formatos de Archivo Permitidos** - **0% Implementado**
**Estado Actual:**
- Formatos hardcodeados en validaciones: jpeg, png, jpg, gif, webp
- Tamaño máximo hardcodeado: 5MB
- No se puede configurar desde interfaz

**Recomendación:**
- Crear tabla `formatos_archivo` con campos:
  - `id`, `extension`, `mime_type`, `tipo` (imagen/documento), `tamano_max_kb`, `activo`
- Usar parámetros del sistema para tamaños máximos
- Validar dinámicamente según configuración

**Impacto:** Medio - Flexibilidad para diferentes tipos de archivo

---

## 📊 Resumen de Prioridades

### 🔴 **Prioridad ALTA** (Implementar primero):
1. **Tipos de Evento** - Usado frecuentemente, hardcodeado
2. **Ciudades / Lugares** - Mejora significativa en UX y reportes
3. **Categorías de Mega Eventos** - Consistencia de datos
4. **Configuraciones del Sistema** - Ya existe infraestructura, falta contenido

### 🟡 **Prioridad MEDIA** (Implementar después):
5. **Estados de Participación** - Mejora flexibilidad
6. **Tipos de Notificaciones** - Escalabilidad
7. **Estados de Evento (Expandido)** - Consistencia
8. **Tipos de Usuario (Expandido)** - Escalabilidad

### 🟢 **Prioridad BAJA** (Opcional):
9. **Categorías de Parámetros** - Organización
10. **Formatos de Archivo** - Flexibilidad avanzada

---

## 🎯 Beneficios de Implementar Parametrizaciones

### ✅ **Ventajas:**
- **Mantenibilidad:** Cambios sin modificar código
- **Flexibilidad:** Agregar nuevas opciones dinámicamente
- **Consistencia:** Validación centralizada
- **Escalabilidad:** Fácil agregar nuevas funcionalidades
- **Reportes:** Mejor agrupación y análisis de datos
- **UX:** Mejor experiencia con autocompletado y validación

### ⚠️ **Consideraciones:**
- Migrar datos existentes de valores hardcodeados a tablas
- Actualizar validaciones en controladores
- Actualizar vistas para usar datos dinámicos
- Crear seeders con datos iniciales
- Documentar proceso de migración

---

## 📝 Plan de Implementación Sugerido

### Fase 1: Críticas (2-3 semanas)
1. Tipos de Evento
2. Categorías de Mega Eventos
3. Configuraciones del Sistema (seeders)

### Fase 2: Importantes (2 semanas)
4. Ciudades / Lugares
5. Estados de Participación

### Fase 3: Mejoras (1-2 semanas)
6. Tipos de Notificaciones
7. Estados de Evento (Expandido)
8. Tipos de Usuario (Expandido)

### Fase 4: Opcionales (1 semana)
9. Categorías de Parámetros
10. Formatos de Archivo

---

**Total Estimado:** 6-8 semanas para implementación completa

---

**Generado automáticamente por análisis del código fuente**


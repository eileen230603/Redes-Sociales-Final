# Funcionalidades de Participación en Eventos

## 📋 Índice
1. [Usuario Externo](#usuario-externo)
2. [ONG (Organizador)](#ong-organizador)

---

## 👤 Usuario Externo

### 🎯 Funcionalidades Principales

#### 1. **Inscribirse en un Evento**
- **Acción**: Buscar y seleccionar eventos disponibles
- **Proceso**:
  - Ver lista de eventos públicos
  - Ver detalles del evento (fecha, ubicación, descripción, capacidad)
  - Verificar si hay cupos disponibles
  - Confirmar inscripción
- **Resultado**:
  - ✅ Inscripción automática y aprobada instantáneamente
  - 🎫 Generación automática de ticket único (UUID)
  - 📧 Notificación automática a la ONG organizadora
- **Validaciones**:
  - El evento debe tener inscripciones abiertas
  - Debe haber cupos disponibles
  - No puede estar ya inscrito en el mismo evento

#### 2. **Ver Mis Participaciones**
- **Acción**: Acceder a "Mis Participaciones"
- **Información mostrada**:
  - Lista de todos los eventos en los que está inscrito
  - Estado de cada participación:
    - ✅ **Aprobada**: Participación confirmada
    - ⏳ **Pendiente**: Esperando aprobación de la ONG
    - ❌ **Rechazada**: Participación no aceptada
  - Estado de asistencia:
    - ✅ **Asistió**: Ya registró su asistencia
    - ⏳ **Pendiente**: Aún no ha asistido
  - Fecha y hora de inscripción
  - Código de ticket único

#### 3. **Ver Ticket de Acceso**
- **Acción**: Ver el ticket con código QR
- **Funcionalidad**:
  - Mostrar código QR del ticket
  - Mostrar código alfanumérico del ticket
  - Descargar o compartir el ticket
- **Uso**: Presentar el ticket al llegar al evento para escanear y registrar asistencia

#### 4. **Cancelar Inscripción**
- **Acción**: Cancelar participación en un evento
- **Proceso**:
  - Acceder a "Mis Participaciones"
  - Seleccionar el evento
  - Confirmar cancelación
- **Resultado**:
  - ✅ Eliminación de la participación
  - 📧 Notificación a la ONG (opcional)
  - Liberación del cupo para otros participantes

#### 5. **Ver Detalles del Evento**
- **Información disponible**:
  - Título y descripción completa
  - Fechas (inicio, fin, límite de inscripción)
  - Ubicación (ciudad, dirección, mapa)
  - Capacidad máxima y cupos disponibles
  - Tipo de evento
  - Estado del evento
  - Galería de imágenes
  - Patrocinadores
  - Invitados especiales

#### 6. **Reaccionar a Eventos (Favoritos)**
- **Acción**: Marcar evento como favorito
- **Funcionalidad**: Guardar eventos de interés para consultarlos después

#### 7. **Compartir Eventos**
- **Acción**: Compartir eventos en redes sociales
- **Métodos**:
  - Copiar enlace
  - Generar código QR para compartir
  - Compartir en redes sociales

---

## 🏢 ONG (Organizador)

### 🎯 Funcionalidades Principales

#### 1. **Gestionar Participantes**

##### a. **Ver Lista de Participantes**
- **Acción**: Acceder a la sección "Voluntarios y Participantes Inscritos"
- **Información mostrada**:
  - Lista completa de participantes (registrados y no registrados)
  - Datos de contacto (nombre, correo, teléfono)
  - Estado de participación (aprobada, pendiente, rechazada)
  - Fecha de inscripción
  - Estado de asistencia
  - Código de ticket

##### b. **Aprobar Participación**
- **Acción**: Aprobar solicitudes de participación
- **Proceso**:
  - Ver participantes pendientes
  - Revisar información del participante
  - Confirmar aprobación
- **Resultado**:
  - ✅ Cambio de estado a "aprobada"
  - 🎫 Generación automática de ticket (si no existe)
  - 📧 Notificación al participante

##### c. **Rechazar Participación**
- **Acción**: Rechazar solicitudes de participación
- **Proceso**:
  - Ver participantes pendientes
  - Confirmar rechazo
- **Resultado**:
  - ❌ Cambio de estado a "rechazada"
  - 📧 Notificación al participante
  - Liberación del cupo

#### 2. **Control de Asistencia**

##### a. **Registrar Asistencia**
- **Métodos disponibles**:
  
  **1. Escaneo QR:**
  - Activar escáner de cámara
  - Escanear código QR del ticket del participante
  - Registro automático de asistencia
  
  **2. Entrada Manual:**
  - Ingresar código del ticket manualmente
  - O seleccionar participante de la lista y marcar asistencia
  
  **3. Registro Directo:**
  - Marcar asistencia directamente desde la lista de participantes
  - Agregar observaciones opcionales

##### b. **Ver Estadísticas de Asistencia**
- **Información mostrada**:
  - 📊 Total de inscritos
  - ✅ Total de asistentes
  - ⏳ Total pendientes
  - 📈 Tasa de asistencia (porcentaje)

##### c. **Lista de Asistencia**
- **Tabla con información**:
  - Nombre del participante
  - Código de ticket
  - Estado (Asistió / Pendiente)
  - Hora exacta de check-in
  - Modo de registro (QR / Manual / Online)
  - Observaciones (si las hay)
  - Acciones disponibles

##### d. **Agregar Observaciones**
- **Funcionalidad**: Agregar notas al registrar asistencia
- **Ejemplos de observaciones**:
  - "Llegó tarde"
  - "Salió antes"
  - "Documento no válido"
  - "Participación parcial"

#### 3. **Validaciones de Seguridad**

##### a. **Control de QR**
- ✅ Los códigos QR expiran 15 minutos después del inicio del evento
- ✅ No se puede escanear el mismo QR más de una vez
- ✅ Validación de que el ticket pertenece al evento correcto

##### b. **Permisos**
- ✅ Solo la ONG propietaria del evento puede registrar asistencia
- ✅ Solo participantes aprobados pueden tener asistencia registrada

#### 4. **Gestionar Eventos**

##### a. **Crear Eventos**
- Configurar todos los detalles del evento
- Establecer fechas, ubicación, capacidad
- Subir imágenes y galería

##### b. **Editar Eventos**
- Modificar información del evento
- Actualizar fechas, ubicación, descripción
- Gestionar imágenes

##### c. **Ver Dashboard del Evento**
- Estadísticas generales del evento
- Métricas de participación
- Reportes de asistencia
- Exportar datos

#### 5. **Notificaciones**
- **Recibir notificaciones cuando**:
  - Un usuario se inscribe al evento
  - Un participante cancela su inscripción
  - Se alcanza la capacidad máxima

#### 6. **Reportes y Exportaciones**
- **Funcionalidades disponibles**:
  - Ver reportes de asistencia
  - Exportar listas de participantes
  - Generar reportes PDF
  - Exportar a Excel (próximamente)

---

## 🔄 Flujo Completo de Participación

### Para el Usuario Externo:

```
1. Buscar Evento
   ↓
2. Ver Detalles del Evento
   ↓
3. Inscribirse
   ↓
4. Recibir Ticket (automático)
   ↓
5. Ver Ticket con QR
   ↓
6. Asistir al Evento
   ↓
7. Presentar Ticket (QR o código)
   ↓
8. ONG registra asistencia
   ↓
9. Ver confirmación de asistencia en "Mis Participaciones"
```

### Para la ONG:

```
1. Crear Evento
   ↓
2. Publicar Evento
   ↓
3. Recibir Notificaciones de Inscripciones
   ↓
4. Revisar Participantes
   ↓
5. Aprobar/Rechazar Participaciones
   ↓
6. Día del Evento: Registrar Asistencias
   ↓
7. Ver Estadísticas en Tiempo Real
   ↓
8. Generar Reportes Finales
```

---

## 📊 Estados y Transiciones

### Estado de Participación:
- **Pendiente** → Esperando aprobación de la ONG
- **Aprobada** → Participación confirmada, puede asistir
- **Rechazada** → Participación no aceptada

### Estado de Asistencia:
- **No asistió** → Aún no ha registrado asistencia
- **Asistió** → Asistencia registrada con fecha y hora

### Modo de Asistencia:
- **QR** → Registrado mediante escaneo de código QR
- **Manual** → Registrado manualmente por la ONG
- **Online** → Para eventos virtuales (futuro)
- **Confirmación** → Validación post-evento (futuro)

---

## 🔐 Permisos y Restricciones

### Usuario Externo:
- ✅ Puede inscribirse en eventos públicos
- ✅ Puede ver sus propias participaciones
- ✅ Puede cancelar sus propias inscripciones
- ❌ No puede ver otros participantes
- ❌ No puede registrar asistencia
- ❌ No puede aprobar/rechazar participaciones

### ONG:
- ✅ Puede crear y gestionar sus eventos
- ✅ Puede ver todos los participantes de sus eventos
- ✅ Puede aprobar/rechazar participaciones
- ✅ Puede registrar asistencia
- ✅ Puede ver estadísticas y reportes
- ❌ No puede gestionar eventos de otras ONGs
- ❌ No puede modificar participaciones de otros eventos

---

## 📱 Interfaz de Usuario

### Usuario Externo:
- **Vista de Eventos**: Lista de eventos disponibles
- **Detalle de Evento**: Información completa del evento
- **Mis Participaciones**: Historial de eventos inscritos
- **Ticket**: Código QR y alfanumérico para acceso

### ONG:
- **Panel de Control**: Dashboard con estadísticas
- **Gestión de Eventos**: Crear, editar, ver eventos
- **Participantes**: Lista y gestión de participantes
- **Control de Asistencia**: Registro y seguimiento de asistencia
- **Reportes**: Estadísticas y exportaciones

---

## 🎯 Casos de Uso Específicos

### Caso 1: Inscripción Exitosa
**Actor**: Usuario Externo
1. Busca un evento de su interés
2. Revisa detalles y verifica disponibilidad
3. Se inscribe
4. Recibe confirmación inmediata
5. Obtiene ticket automáticamente
6. Puede ver su ticket con QR

### Caso 2: Registro de Asistencia con QR
**Actor**: ONG
1. Abre el panel de control de asistencia
2. Activa el escáner QR
3. El participante presenta su ticket
4. Se escanea el código QR
5. Sistema valida y registra asistencia automáticamente
6. Se actualiza la lista y estadísticas en tiempo real

### Caso 3: Registro Manual de Asistencia
**Actor**: ONG
1. Accede a la lista de participantes
2. Busca al participante
3. Hace clic en "Marcar" asistencia
4. Opcionalmente agrega observaciones
5. Confirma registro
6. Se actualiza el estado del participante

### Caso 4: Gestión de Participantes
**Actor**: ONG
1. Recibe notificación de nueva inscripción
2. Revisa información del participante
3. Decide aprobar o rechazar
4. Si aprueba, se genera ticket automáticamente
5. El participante recibe notificación

---

## 📝 Notas Importantes

1. **Tickets Únicos**: Cada participante recibe un ticket único (UUID) que no se puede duplicar
2. **Aprobación Automática**: Actualmente las inscripciones se aprueban automáticamente, pero la ONG puede rechazarlas después
3. **Expiración de QR**: Los códigos QR expiran 15 minutos después del inicio del evento para seguridad
4. **No Duplicación**: Un ticket solo puede usarse una vez para registrar asistencia
5. **Observaciones**: Las observaciones son opcionales pero útiles para el seguimiento

---

## 🚀 Funcionalidades Futuras

- [ ] Integración con Zoom/Meet para eventos virtuales
- [ ] Confirmación post-evento con evidencia
- [ ] Historial completo de auditoría
- [ ] Reportes PDF personalizados
- [ ] Exportación a Excel
- [ ] Notificaciones push
- [ ] Certificados de asistencia automáticos


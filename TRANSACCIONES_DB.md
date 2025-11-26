# Resumen de Transacciones de Base de Datos

Este documento lista todas las transacciones de base de datos (`DB::transaction`) implementadas en el proyecto.

## 📋 Total de Transacciones: **10**

---

## 1. **AuthController.php** - Registro de Usuario
**Ubicación:** `app/Http/Controllers/Auth/AuthController.php`  
**Método:** `register()`  
**Línea:** ~52

**Operaciones:**
- Crear usuario base en tabla `users`
- Crear entidad específica según tipo:
  - Si es ONG → Crear registro en tabla `ongs`
  - Si es Empresa → Crear registro en tabla `empresas`
  - Si es Externo → Crear registro en tabla `integrantes_externos`

**Propósito:** Garantizar que el usuario y su entidad relacionada se creen juntos o no se cree ninguno.

---

## 2. **ProfileController.php** - Actualización de Perfil
**Ubicación:** `app/Http/Controllers/ProfileController.php`  
**Método:** `update()`  
**Línea:** ~170

**Operaciones:**
- Actualizar datos del usuario en tabla `users`
- Actualizar foto de perfil del usuario
- Actualizar información específica según tipo:
  - Si es ONG → Actualizar tabla `ongs`
  - Si es Empresa → Actualizar tabla `empresas`
  - Si es Externo → Actualizar tabla `integrantes_externos`

**Propósito:** Mantener consistencia entre el usuario base y su entidad relacionada.

---

## 3. **EventoParticipacionController.php** - Inscripción a Evento
**Ubicación:** `app/Http/Controllers/Api/EventoParticipacionController.php`  
**Método:** `inscribir()`  
**Línea:** ~42

**Operaciones:**
- Crear participación en tabla `evento_participaciones`
- Crear notificación para la ONG organizadora

**Propósito:** Asegurar que la inscripción y la notificación se creen juntas.

---

## 4. **EventoReaccionController.php** - Reacción a Evento
**Ubicación:** `app/Http/Controllers/Api/EventoReaccionController.php`  
**Método:** `toggle()`  
**Línea:** ~57

**Operaciones:**
- Crear reacción en tabla `evento_reacciones`
- Crear notificación para la ONG organizadora

**Propósito:** Garantizar que la reacción y la notificación se creen juntas.

---

## 5. **MegaEventoController.php** - Participación en Mega Evento
**Ubicación:** `app/Http/Controllers/MegaEventoController.php`  
**Método:** `participar()`  
**Línea:** ~617

**Operaciones:**
- Insertar participación en tabla `mega_evento_participantes_externos`
- Crear notificación para la ONG organizadora

**Propósito:** Asegurar que la participación y la notificación se creen juntas.

---

## 6. **EventController.php** - Crear Evento (Patrocinadores)
**Ubicación:** `app/Http/Controllers/Api/EventController.php`  
**Método:** `store()`  
**Línea:** ~688

**Operaciones:**
- Crear registros en `evento_empresas_participantes` para cada patrocinador
- Crear notificaciones para cada empresa patrocinadora

**Propósito:** Garantizar que todos los patrocinadores se registren correctamente con sus notificaciones.

---

## 7. **EventController.php** - Actualizar Evento (Sincronizar Patrocinadores)
**Ubicación:** `app/Http/Controllers/Api/EventController.php`  
**Método:** `update()`  
**Línea:** ~830

**Operaciones:**
- Eliminar patrocinadores que ya no están en la lista
- Crear nuevos registros en `evento_empresas_participantes` para nuevos patrocinadores
- Crear notificaciones para nuevas empresas patrocinadoras

**Propósito:** Mantener sincronización entre el campo JSON `patrocinadores` y la tabla `evento_empresas_participantes`.

---

## 8. **EventController.php** - Eliminar Evento
**Ubicación:** `app/Http/Controllers/Api/EventController.php`  
**Método:** `destroy()`  
**Línea:** ~1026

**Operaciones:**
- Eliminar participaciones en `evento_participaciones`
- Eliminar empresas participantes en `evento_empresas_participantes`
- Eliminar reacciones en `evento_reacciones`
- Eliminar notificaciones relacionadas en `notificaciones`
- Eliminar el evento en `eventos`

**Propósito:** Garantizar eliminación completa y consistente de todos los datos relacionados con el evento.

---

## 9. **EventoEmpresaParticipacionController.php** - Asignar Empresas Colaboradoras
**Ubicación:** `app/Http/Controllers/Api/EventoEmpresaParticipacionController.php`  
**Método:** `asignarEmpresas()`  
**Línea:** ~56

**Operaciones:**
- Crear registros en `evento_empresas_participantes` para cada empresa asignada
- Crear notificaciones para cada empresa colaboradora
- Actualizar campo `patrocinadores` en tabla `eventos` (si aplica)

**Propósito:** Asegurar que las empresas se asignen correctamente y reciban notificaciones.

---

## 10. **EventoEmpresaParticipacionController.php** - Remover Empresas Colaboradoras
**Ubicación:** `app/Http/Controllers/Api/EventoEmpresaParticipacionController.php`  
**Método:** `removerEmpresas()`  
**Línea:** ~177

**Operaciones:**
- Eliminar registros de `evento_empresas_participantes`
- Actualizar campo `patrocinadores` en tabla `eventos` con las empresas restantes

**Propósito:** Mantener consistencia entre la tabla de participaciones y el campo JSON del evento.

---

## 📊 Resumen por Controlador

| Controlador | Cantidad de Transacciones |
|------------|---------------------------|
| `EventController.php` | 3 |
| `EventoEmpresaParticipacionController.php` | 2 |
| `AuthController.php` | 1 |
| `ProfileController.php` | 1 |
| `EventoParticipacionController.php` | 1 |
| `EventoReaccionController.php` | 1 |
| `MegaEventoController.php` | 1 |

---

## 🔍 Patrones Comunes

### Transacciones que incluyen Notificaciones:
- EventoParticipacionController (inscripción)
- EventoReaccionController (reacción)
- MegaEventoController (participación)
- EventController (crear/actualizar patrocinadores)
- EventoEmpresaParticipacionController (asignar empresas)

### Transacciones de Creación:
- AuthController (registro usuario)
- EventController (crear patrocinadores)
- EventoEmpresaParticipacionController (asignar empresas)

### Transacciones de Actualización:
- ProfileController (actualizar perfil)
- EventController (sincronizar patrocinadores)
- EventoEmpresaParticipacionController (remover empresas)

### Transacciones de Eliminación:
- EventController (eliminar evento y datos relacionados)

---

## ✅ Beneficios de las Transacciones

1. **Consistencia de Datos:** Garantiza que operaciones relacionadas se completen todas o ninguna.
2. **Integridad Referencial:** Previene estados inconsistentes entre tablas relacionadas.
3. **Rollback Automático:** Si algo falla, todas las operaciones se revierten automáticamente.
4. **Notificaciones Atómicas:** Las notificaciones se crean junto con las acciones que las generan.

---

## 📝 Notas Importantes

- Todas las transacciones usan `DB::transaction()` con closures.
- Las transacciones manejan errores automáticamente con rollback.
- Las notificaciones siempre se crean dentro de transacciones para garantizar consistencia.
- Las operaciones de eliminación en cascada están protegidas por transacciones.


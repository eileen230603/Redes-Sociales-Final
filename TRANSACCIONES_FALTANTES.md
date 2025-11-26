# Transacciones Faltantes para Completar al 100%

Este documento identifica las operaciones que **deberían estar dentro de transacciones** pero actualmente no lo están.

## 📋 Total de Transacciones Faltantes: **5**

---

## 1. ⚠️ **EventController::store()** - Crear Evento Completo
**Ubicación:** `app/Http/Controllers/Api/EventController.php`  
**Método:** `store()`  
**Línea:** ~659-684

**Problema Actual:**
```php
// Se crea el evento
$evento = Evento::create([...]);

// Luego se procesan las imágenes (fuera de transacción)
$imagenes = $this->processImages($request, $evento->id);
if (!empty($imagenes)) {
    $evento->update(['imagenes' => $imagenes]);
}

// Luego se crean patrocinadores (dentro de transacción, pero separada)
if (!empty($patrocinadores)) {
    DB::transaction(function () use ($evento, $patrocinadores) {
        // ...
    });
}
```

**Riesgo:**
- Si falla el procesamiento de imágenes, el evento queda creado sin imágenes
- Si falla la creación de patrocinadores, el evento queda sin patrocinadores
- No hay consistencia atómica entre todas las operaciones

**Solución Propuesta:**
```php
DB::transaction(function () use ($request, $data, $patrocinadores) {
    // 1. Crear evento
    $evento = Evento::create([...]);
    
    // 2. Procesar imágenes
    $imagenes = $this->processImages($request, $evento->id);
    if (!empty($imagenes)) {
        $evento->update(['imagenes' => $imagenes]);
    }
    
    // 3. Crear patrocinadores
    if (!empty($patrocinadores)) {
        foreach ($patrocinadores as $empresaId) {
            // ... crear participaciones y notificaciones
        }
    }
    
    return $evento;
});
```

**Prioridad:** 🔴 **ALTA** - Afecta la integridad de la creación de eventos

---

## 2. ⚠️ **EventController::update()** - Actualizar Evento Completo
**Ubicación:** `app/Http/Controllers/Api/EventController.php`  
**Método:** `update()`  
**Línea:** ~824-830

**Problema Actual:**
```php
// Se actualiza el evento
$evento->update($data);

// Luego se sincronizan patrocinadores (en transacción separada)
if (isset($data['patrocinadores'])) {
    DB::transaction(function () use ($evento, $nuevosPatrocinadores) {
        // ...
    });
}
```

**Riesgo:**
- Si falla la sincronización de patrocinadores, el evento queda actualizado pero con datos inconsistentes
- El procesamiento de imágenes también está fuera de la transacción principal

**Solución Propuesta:**
```php
DB::transaction(function () use ($evento, $data, $request) {
    // 1. Procesar imágenes
    $nuevasImagenes = $this->processImages($request, $evento->id);
    $imagenesActuales = array_merge($imagenesActuales, $nuevasImagenes);
    $data['imagenes'] = array_values(array_unique(array_filter($imagenesActuales)));
    
    // 2. Actualizar evento
    $evento->update($data);
    
    // 3. Sincronizar patrocinadores
    if (isset($data['patrocinadores'])) {
        // ... sincronizar patrocinadores
    }
});
```

**Prioridad:** 🔴 **ALTA** - Afecta la integridad de la actualización de eventos

---

## 3. ⚠️ **EventoEmpresaParticipacionController::confirmarParticipacion()** - Confirmar Participación
**Ubicación:** `app/Http/Controllers/Api/EventoEmpresaParticipacionController.php`  
**Método:** `confirmarParticipacion()`  
**Línea:** ~230-290

**Problema Actual:**
```php
// Se actualiza la participación
$participacion->estado = 'confirmada';
$participacion->save();

// Luego se crea la notificación (fuera de transacción)
$this->crearNotificacionConfirmacion($evento, $empresaId);
```

**Riesgo:**
- Si falla la creación de la notificación, la participación queda confirmada pero sin notificar a la ONG
- No hay garantía de consistencia entre el estado y la notificación

**Solución Propuesta:**
```php
DB::transaction(function () use ($participacion, $evento, $empresaId, $request) {
    // 1. Actualizar participación
    $participacion->estado = 'confirmada';
    if ($request->has('tipo_colaboracion')) {
        $participacion->tipo_colaboracion = $request->tipo_colaboracion;
    }
    if ($request->has('descripcion_colaboracion')) {
        $participacion->descripcion_colaboracion = $request->descripcion_colaboracion;
    }
    $participacion->save();
    
    // 2. Crear notificación
    $this->crearNotificacionConfirmacion($evento, $empresaId);
    
    return $participacion;
});
```

**Prioridad:** 🟡 **MEDIA** - Afecta la consistencia de notificaciones

---

## 4. ⚠️ **EventoParticipacionController::cancelar()** - Cancelar Inscripción
**Ubicación:** `app/Http/Controllers/Api/EventoParticipacionController.php`  
**Método:** `cancelar()`  
**Línea:** ~62-78

**Problema Actual:**
```php
// Solo elimina la participación
$registro->delete();
```

**Riesgo:**
- No elimina notificaciones relacionadas (si existen)
- No actualiza contadores o estadísticas relacionadas
- Podría dejar datos huérfanos

**Solución Propuesta:**
```php
DB::transaction(function () use ($registro, $eventoId, $externoId) {
    // 1. Eliminar participación
    $registro->delete();
    
    // 2. Eliminar notificaciones relacionadas (opcional, según requerimientos)
    // Notificacion::where('evento_id', $eventoId)
    //     ->where('externo_id', $externoId)
    //     ->where('tipo', 'participacion')
    //     ->delete();
    
    // 3. Actualizar estadísticas si es necesario
    // ...
});
```

**Prioridad:** 🟢 **BAJA** - Solo elimina un registro, pero podría mejorarse

---

## 5. ⚠️ **MegaEventoController::update()** - Actualizar Mega Evento
**Ubicación:** `app/Http/Controllers/MegaEventoController.php`  
**Método:** `update()`  
**Línea:** ~421

**Problema Actual:**
```php
// Se procesan imágenes
$nuevasImagenes = $this->processImages($request, $megaEvento->mega_evento_id);
$imagenesActuales = array_merge($imagenesActuales, $nuevasImagenes);
$data['imagenes'] = array_values(array_unique(array_filter($imagenesActuales)));

// Luego se actualiza (fuera de transacción)
$megaEvento->update($data);
```

**Riesgo:**
- Si falla el procesamiento de imágenes, el mega evento queda actualizado pero sin imágenes
- No hay consistencia atómica

**Solución Propuesta:**
```php
DB::transaction(function () use ($megaEvento, $data, $request) {
    // 1. Procesar imágenes
    $nuevasImagenes = $this->processImages($request, $megaEvento->mega_evento_id);
    $imagenesActuales = array_merge($imagenesActuales, $nuevasImagenes);
    $data['imagenes'] = array_values(array_unique(array_filter($imagenesActuales)));
    
    // 2. Actualizar mega evento
    $megaEvento->update($data);
    
    return $megaEvento;
});
```

**Prioridad:** 🟡 **MEDIA** - Similar a la actualización de eventos normales

---

## 📊 Resumen de Prioridades

| # | Controlador | Método | Prioridad | Impacto |
|---|------------|--------|-----------|---------|
| 1 | EventController | store() | 🔴 ALTA | Integridad de creación de eventos |
| 2 | EventController | update() | 🔴 ALTA | Integridad de actualización de eventos |
| 3 | EventoEmpresaParticipacionController | confirmarParticipacion() | 🟡 MEDIA | Consistencia de notificaciones |
| 4 | EventoParticipacionController | cancelar() | 🟢 BAJA | Limpieza de datos |
| 5 | MegaEventoController | update() | 🟡 MEDIA | Integridad de mega eventos |

---

## 🎯 Recomendaciones

### Implementación Inmediata (Prioridad ALTA):
1. **EventController::store()** - Envolver creación de evento, procesamiento de imágenes y patrocinadores en una sola transacción
2. **EventController::update()** - Envolver actualización de evento, imágenes y sincronización de patrocinadores en una sola transacción

### Implementación Recomendada (Prioridad MEDIA):
3. **EventoEmpresaParticipacionController::confirmarParticipacion()** - Envolver actualización y notificación
4. **MegaEventoController::update()** - Envolver procesamiento de imágenes y actualización

### Implementación Opcional (Prioridad BAJA):
5. **EventoParticipacionController::cancelar()** - Considerar limpieza de notificaciones relacionadas

---

## ⚠️ Consideraciones Importantes

### Operaciones que NO deben estar en transacciones:
- **Procesamiento de archivos**: Si el procesamiento de imágenes falla después de subir archivos, los archivos quedarían huérfanos. Considerar:
  - Procesar archivos primero y validar
  - Luego crear/actualizar en transacción
  - O implementar limpieza de archivos huérfanos

### Manejo de Errores:
- Todas las transacciones deben tener manejo de errores adecuado
- Registrar errores para debugging
- Retornar mensajes de error claros al usuario

### Performance:
- Las transacciones bloquean recursos de base de datos
- Evitar transacciones muy largas
- Considerar procesar archivos fuera de la transacción si es muy pesado

---

## ✅ Checklist de Implementación

- [ ] Implementar transacción en `EventController::store()`
- [ ] Implementar transacción en `EventController::update()`
- [ ] Implementar transacción en `EventoEmpresaParticipacionController::confirmarParticipacion()`
- [ ] Implementar transacción en `MegaEventoController::update()`
- [ ] Mejorar `EventoParticipacionController::cancelar()` (opcional)
- [ ] Probar cada transacción con casos de error
- [ ] Verificar que los rollbacks funcionan correctamente
- [ ] Actualizar documentación de transacciones

---

## 📝 Notas Finales

Una vez implementadas estas transacciones, el sistema tendrá **cobertura del 100%** en operaciones críticas que involucran múltiples tablas relacionadas, garantizando:

✅ **Integridad de datos**  
✅ **Consistencia transaccional**  
✅ **Rollback automático en caso de errores**  
✅ **Mejor manejo de errores y recuperación**


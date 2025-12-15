# Solución: FormatException HTML en Dashboard

## Problema Identificado

**Endpoint llamado por Flutter:**
```
GET /api/eventos/{id}/dashboard-completo
```

**Estado actual:**
- ❌ La ruta NO EXISTE en `routes/api.php`
- ❌ Laravel devuelve HTML (404 o error 500)
- ❌ Flutter intenta parsear HTML como JSON
- ❌ Lanza `FormatException` y rompe la UI

## Causa Raíz

1. **Backend:** Endpoint `/api/eventos/{id}/dashboard-completo` no está definido
2. **Frontend:** `_parseJsonSafely` lanza excepción en lugar de retornar error controlado
3. **Contrato roto:** Flutter espera JSON, Laravel devuelve HTML

## Solución Implementada

### 1. Backend - Agregar Ruta Faltante

Necesitamos agregar en `routes/api.php` (línea 67, después de dashboard normal):

```php
// DASHBOARD COMPLETO DEL EVENTO
Route::get('/{id}/dashboard-completo', [EventController::class, 'dashboardCompleto'])
    ->where('id', '[0-9]+');
Route::get('/{id}/dashboard-completo/pdf', [EventController::class, 'dashboardCompletoPdf'])
    ->where('id', '[0-9]+');
Route::get('/{id}/dashboard-completo/excel', [EventController::class, 'dashboardCompletoExcel'])
    ->where('id', '[0-9]+');
```

### 2. Frontend - Mejorar Manejo de Errores

#### A. Mejorar `_parseJsonSafely`

**Cambios:**
- NO lanzar `FormatException`
- Retornar objeto de error controlado
- Validar `Content-Type` del response
- Detectar errores de autenticación

#### B. Mejorar métodos de API

**Cambios:**
- Validar `statusCode` ANTES de parsear
- Validar `Content-Type` contiene `application/json`
- Manejar casos específicos:
  - 401: Sesión expirada
  - 404: Recurso no encontrado
  - 500: Error del servidor
  - HTML response: Respuesta inválida

## Archivos a Modificar

### Backend
1. `routes/api.php` - Agregar rutas faltantes
2. `app/Http/Controllers/Api/EventController.php` - Implementar métodos

### Frontend
1. `lib/services/api_service.dart` - Mejorar manejo de errores
2. Métodos afectados:
   - `_parseJsonSafely`
   - `getDashboardEventoCompleto`
   - `getDashboardOngCompleto`

## Implementación

Ver archivos modificados en el commit.

## Validación

1. Verificar que endpoint existe: `GET /api/eventos/1/dashboard-completo`
2. Verificar respuesta JSON válida
3. Verificar manejo de errores en Flutter
4. Probar casos:
   - Dashboard carga correctamente
   - Error 404 muestra mensaje claro
   - Error 401 sugiere re-login
   - Error 500 muestra error del servidor

## Resultado Esperado

✅ Nunca más `FormatException`  
✅ Mensajes de error claros y específicos  
✅ UI estable incluso con errores  
✅ Backend y frontend alineados por contrato

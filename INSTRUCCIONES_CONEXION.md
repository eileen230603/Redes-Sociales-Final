# 🔧 Instrucciones para Solucionar el Error de Conexión

## ✅ Cambios Realizados

1. **URL actualizada** a `http://127.0.0.1:8000/api` (para Chrome/Web)
2. **CORS configurado** en Laravel para permitir peticiones desde Flutter Web
3. **Middleware de CORS** habilitado

## 🚀 Pasos para Solucionar

### 1. Reinicia el servidor Laravel

**IMPORTANTE:** Debes reiniciar el servidor Laravel para que los cambios de CORS surtan efecto.

1. Detén el servidor actual (Ctrl+C en la terminal donde está corriendo)
2. Reinicia el servidor:

```bash
cd Redes-Sociales-Final
php artisan serve
```

### 2. Reinicia la app Flutter

1. Detén la app Flutter (Ctrl+C o detener desde el IDE)
2. Reinicia la app:

```bash
cd redes_sociales_mobile
flutter run -d chrome
```

### 3. Prueba la conexión

Ahora debería funcionar. La URL configurada es `http://127.0.0.1:8000/api`

## 🔍 Si Aún No Funciona

### Verifica que el servidor esté corriendo:

Abre tu navegador y visita:
```
http://127.0.0.1:8000
```

Deberías ver la página de Laravel.

### Prueba el endpoint directamente:

```
http://127.0.0.1:8000/api/auth/login
```

Deberías ver un error de método (porque es POST, no GET), pero confirma que el servidor responde.

### Cambia la URL si es necesario:

Si ejecutas en **emulador Android**, cambia en `lib/config/api_config.dart`:

```dart
static const String baseUrl = 'http://10.0.2.2:8000/api';
```

Si ejecutas en **dispositivo físico**, usa tu IP local:

```dart
static const String baseUrl = 'http://192.168.1.XXX:8000/api';
```

## 📝 Resumen

1. ✅ Reinicia servidor Laravel: `php artisan serve`
2. ✅ Reinicia app Flutter
3. ✅ URL configurada: `http://127.0.0.1:8000/api`
4. ✅ CORS habilitado

¡Debería funcionar ahora!


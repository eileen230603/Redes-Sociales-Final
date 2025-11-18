# 🔧 Solución de Problemas de Conexión

## Error: ERR_CONNECTION_TIMED_OUT

Este error significa que la app Flutter no puede conectarse al servidor Laravel.

### ✅ Pasos para Solucionar:

#### 1. Verificar que el servidor Laravel esté corriendo

Abre una terminal y ejecuta:

```bash
cd Redes-Sociales-Final
php artisan serve
```

Deberías ver algo como:
```
Laravel development server started: http://127.0.0.1:8000
```

**⚠️ IMPORTANTE:** El servidor debe estar corriendo mientras uses la app móvil.

#### 2. Configurar la URL correcta según tu entorno

Edita el archivo: `lib/config/api_config.dart`

**Para Emulador Android (por defecto):**
```dart
static const String baseUrl = 'http://10.0.2.2:8000/api';
```

**Para Dispositivo Físico:**
1. Encuentra tu IP local:
   - Windows: Abre CMD y ejecuta `ipconfig`
   - Busca "IPv4 Address" (ejemplo: 192.168.1.100)
   
2. Cambia la URL:
```dart
static const String baseUrl = 'http://192.168.1.100:8000/api';
```
(Reemplaza 192.168.1.100 con tu IP real)

**Para iOS Simulator:**
```dart
static const String baseUrl = 'http://localhost:8000/api';
```

#### 3. Verificar que Laravel acepte conexiones externas

Si usas dispositivo físico, asegúrate de que Laravel escuche en todas las interfaces:

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

#### 4. Verificar el firewall

- Windows: Asegúrate de que el firewall permita conexiones en el puerto 8000
- El dispositivo móvil y la computadora deben estar en la misma red WiFi

#### 5. Probar la conexión manualmente

Abre un navegador en tu dispositivo/emulador y visita:
- Emulador Android: `http://10.0.2.2:8000`
- Dispositivo físico: `http://TU_IP:8000`

Si ves la página de Laravel, la conexión funciona.

### 🔍 Debug

La app ahora muestra mensajes de debug en la consola:
- `🔗 Intentando conectar a: ...` - Muestra la URL que intenta usar
- `📡 Respuesta recibida: ...` - Muestra el código de estado HTTP
- `❌ Error: ...` - Muestra errores detallados

Revisa la consola de Flutter para ver estos mensajes.

### 📝 Resumen Rápido

1. ✅ Servidor Laravel corriendo: `php artisan serve`
2. ✅ URL correcta en `api_config.dart`
3. ✅ Misma red WiFi (si usas dispositivo físico)
4. ✅ Firewall permitiendo conexiones
5. ✅ Probar en navegador primero

### 🆘 Si aún no funciona

1. Verifica que el servidor Laravel esté realmente corriendo
2. Prueba cambiar el puerto en Laravel: `php artisan serve --port=8001`
3. Actualiza la URL en `api_config.dart` con el nuevo puerto
4. Reinicia la app Flutter completamente


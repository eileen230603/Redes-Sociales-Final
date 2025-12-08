# Redes Sociales - Aplicación Móvil Flutter

Aplicación móvil desarrollada en Flutter que se conecta a la API REST de Laravel para gestionar eventos, participaciones y patrocinios.

## 🚀 Características

- ✅ **Autenticación completa**: Login y Registro
- ✅ **Múltiples tipos de usuario**: ONG, Empresa, Integrante externo
- ✅ **Conexión con API Laravel**: Usa la misma base de datos
- ✅ **Almacenamiento local**: Guarda tokens y datos de sesión
- ✅ **Interfaz moderna**: Diseño Material Design 3

## 📋 Requisitos

- Flutter SDK (3.7.2 o superior)
- Dart SDK
- Servidor Laravel corriendo (puerto 8000 por defecto)

## 🔧 Configuración

### 1. Instalar dependencias

```bash
flutter pub get
```

### 2. Configurar URL de la API

Edita el archivo `lib/config/api_config.dart` y cambia la URL base según tu entorno:

```dart
// Para emulador Android
static const String baseUrl = 'http://10.0.2.2:8000/api';

// Para dispositivo físico (reemplaza con tu IP local)
static const String baseUrl = 'http://192.168.1.XXX:8000/api';

// Para producción
static const String baseUrl = 'https://tu-dominio.com/api';
```

### 3. Ejecutar la aplicación

```bash
flutter run
```

## 📱 Estructura del Proyecto

```
lib/
├── config/
│   └── api_config.dart          # Configuración de la API
├── models/
│   ├── user.dart                # Modelo de usuario
│   └── auth_response.dart       # Modelo de respuesta de autenticación
├── services/
│   ├── api_service.dart         # Servicio para llamadas a la API
│   └── storage_service.dart     # Servicio para almacenamiento local
├── screens/
│   ├── login_screen.dart        # Pantalla de login
│   ├── register_screen.dart     # Pantalla de registro
│   └── home_screen.dart         # Pantalla principal
└── main.dart                    # Punto de entrada
```

## 🔐 Autenticación

### Login
- Endpoint: `POST /api/auth/login`
- Campos: `correo_electronico`, `contrasena`
- Respuesta: Token y datos del usuario

### Registro
- Endpoint: `POST /api/auth/register`
- Campos según tipo de usuario:
  - **ONG**: nombre_ong, NIT, telefono, direccion, sitio_web, descripcion
  - **Empresa**: nombre_empresa, NIT, telefono, direccion, sitio_web, descripcion
  - **Integrante externo**: nombres, apellidos, fecha_nacimiento, telefono, descripcion

## 📦 Dependencias

- `http`: Para realizar peticiones HTTP a la API
- `shared_preferences`: Para almacenar tokens y datos de sesión localmente

## 🛠️ Próximas Funcionalidades

- [ ] Listado de eventos
- [ ] Detalle de eventos
- [ ] Inscripción a eventos
- [ ] Gestión de patrocinios
- [ ] Perfil de usuario
- [ ] Notificaciones

## 📝 Notas

- La aplicación usa Laravel Sanctum para autenticación basada en tokens
- El token se guarda automáticamente después del login/registro
- La sesión persiste entre reinicios de la aplicación

## 🔗 Conexión con Laravel

Esta aplicación se conecta a la API REST del proyecto Laravel ubicado en:
- `Redes-Sociales-Final/`

Asegúrate de que el servidor Laravel esté corriendo antes de usar la app móvil.

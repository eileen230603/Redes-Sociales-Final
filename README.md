<div align="center">

# 🌐 Redes Sociales - Plataforma de Impacto Social

### *Conectando ONGs, Empresas y Voluntarios para Transformar el Mundo*

[![Flutter](https://img.shields.io/badge/Flutter-3.38.5-02569B?logo=flutter)](https://flutter.dev)
[![Laravel](https://img.shields.io/badge/Laravel-10.x-FF2D20?logo=laravel)](https://laravel.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![PRs Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen.svg)](CONTRIBUTING.md)

[Características](#-características-principales) •
[Arquitectura](#-arquitectura-del-sistema) •
[Instalación](#-instalación-rápida) •
[Documentación](#-documentación) •
[Contribuir](#-contribuir)

</div>

---

## 📖 Descripción del Proyecto

**Redes Sociales** es una plataforma digital full-stack diseñada para revolucionar la forma en que las organizaciones sin fines de lucro (ONGs), empresas socialmente responsables y voluntarios colaboran en iniciativas de impacto social.

### 🎯 Misión

Facilitar la conexión entre actores clave del ecosistema social mediante una plataforma tecnológica robusta, escalable y fácil de usar que permita:

- 🏢 **ONGs**: Crear, gestionar y promocionar eventos sociales
- 💼 **Empresas**: Patrocinar eventos y demostrar responsabilidad social corporativa
- 🙋 **Voluntarios**: Descubrir oportunidades de participación y generar impacto

---

## ✨ Características Principales

### 🎨 **Interfaz de Usuario Premium**
- Diseño Material Design 3 con paleta de colores profesional
- Sistema de diseño unificado con componentes reutilizables
- Experiencia responsive optimizada para web y móvil
- Animaciones fluidas y micro-interacciones

### 📊 **Dashboards Analíticos Avanzados**
- **Dashboard ONG**: Métricas en tiempo real, KPIs visuales, gráficos interactivos
- **Dashboard Empresa**: Seguimiento de patrocinios, ROI social, impacto generado
- **Dashboard Voluntario**: Historial de participación, estadísticas personales, logros

### 🔐 **Autenticación y Seguridad**
- Sistema de autenticación basado en Laravel Sanctum
- Tokens JWT con expiración configurable
- Roles y permisos granulares (ONG, Empresa, Integrante Externo)
- Validación de datos en frontend y backend

### 📱 **Gestión de Eventos**
- Creación y edición de eventos con formularios intuitivos
- Sistema de inscripciones con confirmación automática
- Mega eventos con múltiples sub-eventos
- Geolocalización con mapas interactivos
- Galería de imágenes con carga múltiple

### 💰 **Sistema de Patrocinios**
- Asignación de empresas patrocinadoras a eventos
- Confirmación bidireccional (ONG ↔ Empresa)
- Seguimiento de colaboraciones y aportes
- Reportes de impacto para patrocinadores

### 📈 **Analytics y Reportes**
- Gráficos de participación mensual
- Distribución de eventos por tipo y categoría
- Tasas de asistencia y engagement
- Exportación de reportes en PDF y Excel
- Métricas de impacto social cuantificables

### 🔔 **Sistema de Notificaciones**
- Notificaciones en tiempo real
- Alertas de nuevos eventos y confirmaciones
- Recordatorios de eventos próximos
- Historial completo de notificaciones

---

## 🏗️ Arquitectura del Sistema

### **Stack Tecnológico**

#### **Frontend (Flutter)**
```
Flutter 3.38.5
├── Dart 3.x
├── Material Design 3
├── State Management: Provider/setState
├── HTTP Client: http package
├── Local Storage: shared_preferences
├── Charts: fl_chart
├── Maps: flutter_map
└── Image Handling: image_picker
```

#### **Backend (Laravel)**
```
Laravel 10.x
├── PHP 8.1+
├── MySQL 8.0+
├── Laravel Sanctum (Auth)
├── Eloquent ORM
├── RESTful API
├── File Storage: Laravel Storage
└── PDF Generation: DomPDF
```

### **Arquitectura de Capas**

```
┌─────────────────────────────────────────┐
│         CAPA DE PRESENTACIÓN            │
│  (Flutter - Material Design 3)          │
│  • Screens  • Widgets  • Themes         │
└─────────────────────────────────────────┘
                    ↕
┌─────────────────────────────────────────┐
│         CAPA DE SERVICIOS               │
│  • ApiService  • StorageService         │
│  • CacheService  • NotificationService  │
└─────────────────────────────────────────┘
                    ↕
┌─────────────────────────────────────────┐
│         CAPA DE API REST                │
│  (Laravel Controllers & Routes)         │
│  • Auth  • Events  • Users  • Reports   │
└─────────────────────────────────────────┘
                    ↕
┌─────────────────────────────────────────┐
│         CAPA DE NEGOCIO                 │
│  (Laravel Models & Business Logic)      │
│  • Eloquent Models  • Relationships     │
└─────────────────────────────────────────┘
                    ↕
┌─────────────────────────────────────────┐
│         CAPA DE DATOS                   │
│  (MySQL Database)                       │
│  • Migrations  • Seeders  • Indexes     │
└─────────────────────────────────────────┘
```

---

## 🚀 Instalación Rápida

### **Prerrequisitos**

- **Flutter SDK**: 3.7.2 o superior ([Instalar Flutter](https://flutter.dev/docs/get-started/install))
- **PHP**: 8.1 o superior
- **Composer**: Gestor de dependencias PHP
- **MySQL**: 8.0 o superior
- **Node.js**: 16.x o superior (opcional, para assets)

### **1️⃣ Clonar el Repositorio**

```bash
git clone https://github.com/eileen230603/Redes-Sociales-Final.git
cd Redes-Sociales-Final
```

### **2️⃣ Configurar Backend (Laravel)**

```bash
# Instalar dependencias PHP
composer install

# Copiar archivo de entorno
cp .env.example .env

# Generar clave de aplicación
php artisan key:generate

# Configurar base de datos en .env
# DB_DATABASE=redes_sociales
# DB_USERNAME=tu_usuario
# DB_PASSWORD=tu_contraseña

# Ejecutar migraciones
php artisan migrate

# (Opcional) Poblar base de datos con datos de prueba
php artisan db:seed

# Crear enlace simbólico para storage
php artisan storage:link

# Iniciar servidor de desarrollo
php artisan serve
```

El backend estará disponible en: `http://127.0.0.1:8000`

### **3️⃣ Configurar Frontend (Flutter)**

```bash
# Instalar dependencias Flutter
flutter pub get

# Configurar URL de API
# Editar: lib/config/api_config.dart
# static const String baseUrl = 'http://127.0.0.1:8000/api';

# Ejecutar en web
flutter run -d web-server --web-port=8080

# O ejecutar en Chrome
flutter run -d chrome
```

La aplicación web estará disponible en: `http://localhost:8080`

---

## 📂 Estructura del Proyecto

### **Frontend (Flutter)**

```
lib/
├── config/
│   ├── api_config.dart           # Configuración de endpoints
│   ├── design_tokens.dart        # Tokens de diseño (colores, espaciado)
│   └── typography_system.dart    # Sistema tipográfico
│
├── models/
│   ├── user.dart                 # Modelo de usuario
│   ├── evento.dart               # Modelo de evento
│   └── dashboard_data.dart       # Modelos de datos de dashboards
│
├── services/
│   ├── api_service.dart          # Cliente HTTP para API
│   ├── storage_service.dart      # Almacenamiento local
│   └── cache_service.dart        # Sistema de caché
│
├── screens/
│   ├── auth/
│   │   ├── login_screen.dart
│   │   └── register_screen.dart
│   ├── ong/
│   │   ├── dashboard_ong_completo_screen.dart
│   │   ├── eventos_ong_screen.dart
│   │   └── crear_evento_screen.dart
│   ├── empresa/
│   │   ├── dashboard_empresa_screen.dart
│   │   └── eventos_patrocinados_screen.dart
│   └── externo/
│       ├── dashboard_externo_mejorado_screen.dart
│       └── eventos_list_screen.dart
│
├── widgets/
│   ├── atoms/                    # Componentes básicos
│   ├── molecules/                # Componentes compuestos
│   ├── organisms/                # Componentes complejos
│   └── charts/                   # Gráficos y visualizaciones
│
└── main.dart                     # Punto de entrada
```

### **Backend (Laravel)**

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       ├── AuthController.php
│   │       ├── EventController.php
│   │       ├── DashboardOngController.php
│   │       ├── DashboardExternoController.php
│   │       └── EventoEmpresaParticipacionController.php
│   └── Middleware/
│       └── Authenticate.php
│
├── Models/
│   ├── User.dart
│   ├── Evento.dart
│   ├── Ong.dart
│   ├── Empresa.dart
│   ├── IntegranteExterno.dart
│   ├── EventoParticipacion.dart
│   └── EventoEmpresaParticipacion.dart
│
database/
├── migrations/
│   ├── 2024_create_users_table.php
│   ├── 2024_create_eventos_table.php
│   └── ...
└── seeders/
    └── DatabaseSeeder.php

routes/
└── api.php                       # Definición de rutas API
```

---

## 🔌 API Endpoints

### **Autenticación**

| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| POST | `/api/auth/register` | Registro de usuario | ❌ |
| POST | `/api/auth/login` | Inicio de sesión | ❌ |
| POST | `/api/auth/logout` | Cerrar sesión | ✅ |
| GET | `/api/auth/user` | Obtener usuario actual | ✅ |

### **Eventos**

| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| GET | `/api/eventos` | Listar eventos | ❌ |
| GET | `/api/eventos/{id}` | Detalle de evento | ❌ |
| POST | `/api/eventos` | Crear evento | ✅ ONG |
| PUT | `/api/eventos/{id}` | Actualizar evento | ✅ ONG |
| DELETE | `/api/eventos/{id}` | Eliminar evento | ✅ ONG |
| GET | `/api/eventos/{id}/dashboard` | Dashboard del evento | ✅ ONG |
| GET | `/api/eventos/{id}/dashboard-completo` | Dashboard completo | ✅ ONG |

### **Dashboards**

| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| GET | `/api/dashboard/ong` | Dashboard ONG | ✅ ONG |
| GET | `/api/eventos/empresa/patrocinados` | Dashboard Empresa | ✅ Empresa |
| GET | `/api/dashboard/externo` | Dashboard Voluntario | ✅ Externo |

### **Participaciones**

| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| POST | `/api/eventos/{id}/participar` | Inscribirse a evento | ✅ Externo |
| GET | `/api/mis-eventos` | Mis eventos | ✅ Externo |
| POST | `/api/eventos/{id}/empresas/asignar` | Asignar empresas | ✅ ONG |

---

## 🎨 Sistema de Diseño

### **Paleta de Colores**

```dart
// Colores Primarios
Primary: #6750A4
Secondary: #625B71
Tertiary: #7D5260

// Colores de Estado
Success: #4CAF50
Warning: #FF9800
Error: #F44336
Info: #2196F3

// Colores Neutros
Surface: #FFFFFF
Background: #FDFBFF
OnSurface: #1C1B1F
```

### **Tipografía**

```dart
// Headings
displayLarge: 57px / Bold
displayMedium: 45px / Bold
displaySmall: 36px / Bold

// Títulos
titleLarge: 22px / Medium
titleMedium: 16px / Medium
titleSmall: 14px / Medium

// Cuerpo
bodyLarge: 16px / Regular
bodyMedium: 14px / Regular
bodySmall: 12px / Regular
```

### **Espaciado**

```dart
xxs: 4px
xs: 8px
sm: 12px
md: 16px
lg: 24px
xl: 32px
xxl: 48px
```

---

## 📊 Características de los Dashboards

### **Dashboard ONG**

- 📈 **KPIs Visuales**: Total eventos, participantes, empresas colaboradoras
- 📊 **Gráficos Interactivos**: Participación mensual, eventos por tipo
- 🎯 **Estados Vacíos Profesionales**: Mensajes motivadores cuando no hay datos
- 📱 **Responsive**: Diseño adaptable a todos los tamaños de pantalla
- 📥 **Exportación**: PDF y Excel de reportes completos

### **Dashboard Empresa**

- 💼 **Eventos Patrocinados**: Lista completa con métricas
- 📊 **Impacto Medible**: Participantes alcanzados, reacciones, compartidos
- 🎯 **ROI Social**: Métricas de retorno de inversión social
- 📈 **Tendencias**: Evolución de participación en eventos patrocinados

### **Dashboard Voluntario**

- 🏆 **Estadísticas Personales**: Eventos inscritos, asistidos, horas acumuladas
- 📅 **Mes Más Activo**: Identificación del período con más participación
- ✅ **Tasa de Asistencia**: Gráfico circular con desglose detallado
- 📊 **Distribución**: Pie chart de asistencia vs no asistencia
- 🌍 **Ciudades**: Mapa de ubicaciones donde ha participado

---

## 🧪 Testing

### **Backend (Laravel)**

```bash
# Ejecutar tests unitarios
php artisan test

# Ejecutar tests con cobertura
php artisan test --coverage

# Tests específicos
php artisan test --filter EventControllerTest
```

### **Frontend (Flutter)**

```bash
# Ejecutar tests
flutter test

# Tests con cobertura
flutter test --coverage

# Tests de integración
flutter drive --target=test_driver/app.dart
```

---

## 🚢 Deployment

### **Backend (Laravel)**

```bash
# Optimizar para producción
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Configurar permisos
chmod -R 775 storage bootstrap/cache
```

### **Frontend (Flutter Web)**

```bash
# Build para producción
flutter build web --release

# Los archivos estarán en: build/web/
# Subir a hosting (Netlify, Vercel, Firebase, etc.)
```

---

## 🤝 Contribuir

¡Las contribuciones son bienvenidas! Por favor, sigue estos pasos:

1. **Fork** el repositorio
2. Crea una **rama** para tu feature (`git checkout -b feature/AmazingFeature`)
3. **Commit** tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. **Push** a la rama (`git push origin feature/AmazingFeature`)
5. Abre un **Pull Request**

### **Guías de Estilo**

- **Flutter**: Seguir [Effective Dart](https://dart.dev/guides/language/effective-dart)
- **Laravel**: Seguir [PSR-12](https://www.php-fig.org/psr/psr-12/)
- **Commits**: Usar [Conventional Commits](https://www.conventionalcommits.org/)

---

## 📝 Changelog

### **v2.0.0** (2025-01-15)
- ✨ Dashboard Externo Mejorado con métricas avanzadas
- ✨ Dashboard Empresa con seguimiento de patrocinios
- 🐛 Fix: Gráfico circular de asistencia sin sobreposición
- 🔧 Mejora: Manejo robusto de errores JSON/HTML en API
- 📚 Documentación: README profesional completo

### **v1.5.0** (2024-12-10)
- ✨ Dashboard ONG con KPIs visuales
- ✨ Sistema de diseño unificado
- 🐛 Fix: Overflow en cards de métricas

### **v1.0.0** (2024-11-01)
- 🎉 Lanzamiento inicial
- ✨ Autenticación completa
- ✨ Gestión de eventos
- ✨ Sistema de participaciones

---

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo [LICENSE](LICENSE) para más detalles.

---

## 👥 Equipo de Desarrollo

Desarrollado con ❤️ por estudiantes de Ingeniería de Sistemas - UNI2

- **Backend Lead**: Laravel & API REST
- **Frontend Lead**: Flutter & Material Design
- **UX/UI Designer**: Sistema de Diseño
- **QA Engineer**: Testing & Quality Assurance

---

## 📞 Soporte

¿Tienes preguntas o necesitas ayuda?

- 📧 Email: soporte@redessociales.com
- 💬 Issues: [GitHub Issues](https://github.com/eileen230603/Redes-Sociales-Final/issues)
- 📖 Docs: [Documentación Completa](https://docs.redessociales.com)

---

<div align="center">

**⭐ Si este proyecto te fue útil, considera darle una estrella en GitHub ⭐**

[⬆ Volver arriba](#-redes-sociales---plataforma-de-impacto-social)

</div>

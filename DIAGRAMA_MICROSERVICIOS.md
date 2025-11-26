# Diagrama de Integración de Microservicios
## Sistema de Gestión de Eventos Sociales

---

## 1. Arquitectura General de Microservicios

```mermaid
graph TB
    subgraph "Clientes"
        WEB[Web App<br/>Laravel Blade]
        MOBILE[Mobile App<br/>Flutter]
    end

    subgraph "API Gateway"
        GATEWAY[API Gateway<br/>Kong/Nginx]
    end

    subgraph "Microservicios de Aplicación"
        AUTH[Auth Service<br/>Laravel Sanctum]
        EVENTOS[Eventos Service]
        PARTICIPACIONES[Participaciones Service]
        NOTIFICACIONES[Notificaciones Service]
        DASHBOARD[Dashboard/Analytics Service]
        CONFIG[Configuración Service]
        STORAGE[Storage Service]
        MEGA[Mega Eventos Service]
    end

    subgraph "Servicios de Infraestructura"
        QUEUE[Message Queue<br/>Redis/RabbitMQ]
        CACHE[Cache Service<br/>Redis]
        DB_AUTH[(Auth DB)]
        DB_EVENTOS[(Eventos DB)]
        DB_PARTICIPACIONES[(Participaciones DB)]
        DB_NOTIFICACIONES[(Notificaciones DB)]
        DB_DASHBOARD[(Dashboard DB)]
        DB_CONFIG[(Config DB)]
        DB_STORAGE[(Storage Metadata DB)]
        DB_MEGA[(Mega Eventos DB)]
    end

    WEB --> GATEWAY
    MOBILE --> GATEWAY
    GATEWAY --> AUTH
    GATEWAY --> EVENTOS
    GATEWAY --> PARTICIPACIONES
    GATEWAY --> NOTIFICACIONES
    GATEWAY --> DASHBOARD
    GATEWAY --> CONFIG
    GATEWAY --> STORAGE
    GATEWAY --> MEGA

    AUTH --> DB_AUTH
    AUTH --> CACHE
    EVENTOS --> DB_EVENTOS
    EVENTOS --> CACHE
    PARTICIPACIONES --> DB_PARTICIPACIONES
    PARTICIPACIONES --> QUEUE
    NOTIFICACIONES --> DB_NOTIFICACIONES
    NOTIFICACIONES --> QUEUE
    DASHBOARD --> DB_DASHBOARD
    CONFIG --> DB_CONFIG
    STORAGE --> DB_STORAGE
    MEGA --> DB_MEGA

    EVENTOS -.->|Evento Creado| QUEUE
    PARTICIPACIONES -.->|Participación| QUEUE
    QUEUE -.->|Notificar| NOTIFICACIONES
    QUEUE -.->|Actualizar Stats| DASHBOARD
```

---

## 2. Flujo de Comunicación entre Microservicios

```mermaid
sequenceDiagram
    participant C as Cliente
    participant G as API Gateway
    participant A as Auth Service
    participant E as Eventos Service
    participant P as Participaciones Service
    participant Q as Message Queue
    participant N as Notificaciones Service
    participant D as Dashboard Service

    C->>G: POST /api/auth/login
    G->>A: Validar credenciales
    A-->>G: Token JWT
    G-->>C: Token + User Info

    C->>G: POST /api/eventos (con token)
    G->>A: Validar token
    A-->>G: Token válido
    G->>E: Crear evento
    E->>E: Guardar en DB
    E->>Q: Publicar "evento.creado"
    E-->>G: Evento creado
    G-->>C: Respuesta

    Q->>N: Consumir "evento.creado"
    N->>N: Crear notificaciones
    N->>Q: Publicar "notificacion.enviada"

    C->>G: POST /api/participaciones/inscribir
    G->>P: Inscribir usuario
    P->>P: Guardar participación
    P->>Q: Publicar "participacion.creada"
    P-->>G: Participación creada
    G-->>C: Respuesta

    Q->>N: Consumir "participacion.creada"
    N->>N: Notificar a ONG
    Q->>D: Consumir "participacion.creada"
    D->>D: Actualizar estadísticas
```

---

## 3. Desglose de Microservicios

### 3.1 Auth Service
```mermaid
graph LR
    AUTH[Auth Service]
    AUTH --> REG[Registro]
    AUTH --> LOGIN[Login]
    AUTH --> TOKEN[Token Management]
    AUTH --> PERM[Permisos]
    AUTH --> DB[(Users DB)]
    AUTH --> CACHE[(Redis Cache)]
```

**Responsabilidades:**
- Registro de usuarios (ONG, Empresa, Externo)
- Autenticación y generación de tokens JWT
- Validación de tokens
- Gestión de permisos y roles
- Sesiones y refresh tokens

---

### 3.2 Eventos Service
```mermaid
graph LR
    EVT[Eventos Service]
    EVT --> CRUD[CRUD Eventos]
    EVT --> FILTER[Filtros/Búsqueda]
    EVT --> STATES[Estados Evento]
    EVT --> PATROC[Patrocinadores]
    EVT --> DB[(Eventos DB)]
    EVT --> PUB[Publisher]
```

**Responsabilidades:**
- Crear, leer, actualizar, eliminar eventos
- Gestión de estados (Borrador, Publicado, Finalizado)
- Asociación de patrocinadores
- Búsqueda y filtrado de eventos
- Publicar eventos a Message Queue

---

### 3.3 Participaciones Service
```mermaid
graph LR
    PART[Participaciones Service]
    PART --> INSC[Inscripciones]
    PART --> APROB[Aprobaciones]
    PART --> CANCEL[Cancelaciones]
    PART --> LIST[Listados]
    PART --> DB[(Participaciones DB)]
    PART --> PUB[Publisher]
```

**Responsabilidades:**
- Inscripción de usuarios a eventos
- Aprobación/rechazo de participaciones
- Cancelación de participaciones
- Listado de participantes por evento
- Publicar eventos de participación

---

### 3.4 Notificaciones Service
```mermaid
graph LR
    NOT[Notificaciones Service]
    NOT --> CREATE[Crear Notificaciones]
    NOT --> SEND[Enviar Notificaciones]
    NOT --> READ[Marcar Leídas]
    NOT --> COUNT[Contador]
    NOT --> DB[(Notificaciones DB)]
    NOT --> SUB[Subscriber]
    NOT --> EMAIL[Email Service]
    NOT --> PUSH[Push Notifications]
```

**Responsabilidades:**
- Crear notificaciones desde eventos
- Enviar notificaciones (email, push, in-app)
- Marcar notificaciones como leídas
- Contador de notificaciones no leídas
- Consumir eventos de otros servicios

---

### 3.5 Dashboard/Analytics Service
```mermaid
graph LR
    DASH[Dashboard Service]
    DASH --> STATS[Estadísticas]
    DASH --> REPORTS[Reportes]
    DASH --> CHARTS[Gráficos]
    DASH --> DB[(Analytics DB)]
    DASH --> SUB[Subscriber]
    DASH --> CACHE[(Cache)]
```

**Responsabilidades:**
- Estadísticas generales de ONG
- Estadísticas de participantes
- Estadísticas de reacciones
- Generación de reportes
- Consumir eventos para actualizar métricas

---

### 3.6 Configuración Service
```mermaid
graph LR
    CONFIG[Configuración Service]
    CONFIG --> PARAM[Parametrizaciones]
    CONFIG --> TIPOS[Tipos de Datos]
    CONFIG --> CATEG[Categorías]
    CONFIG --> CIUDADES[Ciudades/Lugares]
    CONFIG --> DB[(Config DB)]
```

**Responsabilidades:**
- Gestión de tipos de evento
- Categorías de mega eventos
- Ciudades y lugares
- Estados de participación
- Tipos de notificación
- Configuraciones del sistema

---

### 3.7 Storage Service
```mermaid
graph LR
    STOR[Storage Service]
    STOR --> UPLOAD[Upload Files]
    STOR --> DOWNLOAD[Download Files]
    STOR --> DELETE[Delete Files]
    STOR --> CORS[CORS Handling]
    STOR --> FILES[(File Storage)]
    STOR --> DB[(Metadata DB)]
```

**Responsabilidades:**
- Subida de archivos (imágenes, documentos)
- Servir archivos con CORS
- Eliminación de archivos
- Gestión de metadatos
- Optimización de imágenes

---

### 3.8 Mega Eventos Service
```mermaid
graph LR
    MEGA[Mega Eventos Service]
    MEGA --> CRUD[CRUD Mega Eventos]
    MEGA --> PART[Mega Participaciones]
    MEGA --> IMG[Gestión Imágenes]
    MEGA --> DB[(Mega Eventos DB)]
    MEGA --> PUB[Publisher]
```

**Responsabilidades:**
- Crear, leer, actualizar, eliminar mega eventos
- Participación en mega eventos
- Gestión de imágenes
- Publicar eventos relacionados

---

## 4. Tecnologías y Protocolos

### Comunicación Síncrona
- **REST API**: Comunicación HTTP/REST entre cliente y servicios
- **API Gateway**: Kong, Nginx, o AWS API Gateway
- **Autenticación**: JWT tokens (Laravel Sanctum)

### Comunicación Asíncrona
- **Message Queue**: Redis Queue, RabbitMQ, o Apache Kafka
- **Eventos**: Event-driven architecture para desacoplamiento
- **Patrón**: Publisher/Subscriber

### Bases de Datos
- **Por Servicio**: Cada microservicio tiene su propia base de datos
- **Tipos**: MySQL/PostgreSQL para datos relacionales, Redis para cache
- **Replicación**: Read replicas para servicios de lectura intensiva

### Infraestructura
- **Contenedores**: Docker para cada microservicio
- **Orquestación**: Kubernetes o Docker Compose
- **Service Discovery**: Consul, Eureka, o Kubernetes DNS
- **Load Balancing**: Nginx, HAProxy, o Kubernetes Service

---

## 5. Patrones de Integración

### 5.1 API Gateway Pattern
- Punto único de entrada para todos los clientes
- Enrutamiento de solicitudes a microservicios
- Autenticación y autorización centralizada
- Rate limiting y throttling
- Logging y monitoreo

### 5.2 Event-Driven Architecture
- Desacoplamiento entre servicios
- Escalabilidad horizontal
- Resiliencia ante fallos
- Event sourcing para auditoría

### 5.3 Database per Service
- Cada microservicio tiene su propia base de datos
- Independencia de datos
- Escalabilidad independiente
- Tecnologías específicas por servicio

### 5.4 CQRS (Command Query Responsibility Segregation)
- Separación de comandos (escritura) y consultas (lectura)
- Optimización de lecturas con vistas materializadas
- Dashboard Service puede usar CQRS

---

## 6. Flujo de Datos Completo

```mermaid
graph TD
    START[Usuario crea evento] --> AUTH_CHECK{Validar Token}
    AUTH_CHECK -->|Válido| CREATE_EVENT[Eventos Service: Crear]
    AUTH_CHECK -->|Inválido| ERROR[Error 401]
    
    CREATE_EVENT --> SAVE_DB[Guardar en DB]
    SAVE_DB --> PUBLISH[Publicar evento.creado]
    
    PUBLISH --> QUEUE[Message Queue]
    
    QUEUE --> NOTIFY[Notificaciones: Crear notif]
    QUEUE --> DASH_UPDATE[Dashboard: Actualizar stats]
    QUEUE --> CACHE_UPDATE[Cache: Invalidar]
    
    NOTIFY --> SEND_NOTIF[Enviar notificación]
    DASH_UPDATE --> UPDATE_METRICS[Actualizar métricas]
    
    SAVE_DB --> RESPONSE[Respuesta al cliente]
```

---

## 7. Consideraciones de Seguridad

- **Autenticación**: JWT tokens con expiración
- **Autorización**: RBAC (Role-Based Access Control)
- **HTTPS**: Todas las comunicaciones encriptadas
- **Rate Limiting**: Protección contra abuso
- **CORS**: Configuración adecuada para clientes
- **Validación**: Validación de entrada en cada servicio
- **Sanitización**: Prevención de inyecciones SQL/XSS

---

## 8. Monitoreo y Observabilidad

- **Logging**: Logs centralizados (ELK Stack, Loki)
- **Métricas**: Prometheus + Grafana
- **Tracing**: Distributed tracing (Jaeger, Zipkin)
- **Health Checks**: Endpoints de salud en cada servicio
- **Alertas**: Notificaciones de errores y fallos

---

## 9. Escalabilidad

- **Horizontal Scaling**: Múltiples instancias de cada servicio
- **Load Balancing**: Distribución de carga
- **Caching**: Redis para datos frecuentemente accedidos
- **Database Sharding**: Particionamiento de bases de datos grandes
- **CDN**: Para archivos estáticos y storage

---

## 10. Despliegue

### Desarrollo
- Docker Compose para todos los servicios
- Hot reload para desarrollo local

### Producción
- Kubernetes para orquestación
- CI/CD con GitLab CI, GitHub Actions, o Jenkins
- Blue-Green o Canary deployments
- Rollback automático en caso de errores

---

## Notas Finales

Este diagrama representa una arquitectura de microservicios ideal para el sistema de gestión de eventos sociales. La migración desde la aplicación monolítica actual debería realizarse de forma gradual, comenzando con servicios menos acoplados como Notificaciones o Storage, y luego continuar con los servicios principales.


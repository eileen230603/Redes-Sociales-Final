# Guía de Integración de Mega Eventos

## 📍 Estado Actual de Integración

Los mega eventos ya están integrados en:
- ✅ Menú de navegación (ONG, Empresa, Externo)
- ✅ Rutas y controladores API
- ✅ Vistas de listado, creación, edición y detalle
- ✅ Dashboard principal (estadísticas)
- ✅ Reportes

## 🎯 Lugares Donde Puedes Integrar Mejor los Mega Eventos

### 1. **Página de Bienvenida (welcome.blade.php)**
**Ubicación:** `resources/views/welcome.blade.php`

**Qué hacer:**
- Agregar una sección de "Mega Eventos Destacados"
- Mostrar los últimos 3-6 mega eventos públicos
- Crear cards atractivos con imágenes y fechas

**Ejemplo de código:**
```php
<!-- Sección de Mega Eventos Destacados -->
<section id="mega-eventos" class="py-20 bg-gradient-to-br from-gray-50 to-white">
    <div class="max-w-7xl mx-auto px-4">
        <h2 class="text-4xl font-bold text-center mb-12">
            <i class="fas fa-star text-brand-acento mr-3"></i>
            Mega Eventos Destacados
        </h2>
        <div id="megaEventosContainer" class="grid md:grid-cols-3 gap-8">
            <!-- Se cargarán dinámicamente -->
        </div>
    </div>
</section>
```

**API a usar:** `GET /api/mega-eventos/publicos`

---

### 2. **Calendario del Dashboard (home-ong.blade.php)**
**Ubicación:** `resources/views/ong/dashboard/index.blade.php`

**Qué hacer:**
- Incluir mega eventos en el calendario junto con eventos regulares
- Diferenciar visualmente (icono de estrella para mega eventos)
- Mostrar mega eventos en el detalle de eventos del día

**Modificación necesaria:**
```javascript
// En la función cargarEventosCalendario(), agregar:
async function cargarMegaEventosCalendario() {
    // Cargar mega eventos y agregarlos a eventosCalendario
    const res = await fetch(`${API_BASE_URL}/api/mega-eventos`, {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        }
    });
    const data = await res.json();
    if (data.success && data.mega_eventos) {
        data.mega_eventos.forEach(mega => {
            eventosCalendario.push({
                id: `mega-${mega.mega_evento_id}`,
                titulo: mega.titulo,
                fecha_inicio: mega.fecha_inicio,
                fecha_fin: mega.fecha_fin,
                tipo_evento: 'Mega Evento',
                es_mega_evento: true, // Flag para diferenciar
                estado: mega.estado
            });
        });
    }
}
```

---

### 3. **Página de Inicio Pública (home-publica.blade.php)**
**Ubicación:** `resources/views/home-publica.blade.php`

**Qué hacer:**
- Agregar sección de "Próximos Mega Eventos"
- Mostrar mega eventos públicos con más detalle
- Agregar botón para ver todos los mega eventos

**Ejemplo:**
```blade
<div class="col-md-6">
    <div class="card border-success">
        <div class="card-header bg-success text-white">
            <i class="fas fa-star mr-2"></i>Próximos Mega Eventos
        </div>
        <div class="card-body" id="megaEventosPublicos">
            <!-- Cargar dinámicamente -->
        </div>
    </div>
</div>
```

---

### 4. **Home de Externo (home-externo.blade.php)**
**Ubicación:** `resources/views/home-externo.blade.php`

**Qué hacer:**
- Agregar widget de "Mega Eventos Recomendados"
- Mostrar mega eventos en los que puede participar
- Agregar contador de mega eventos disponibles

---

### 5. **Widget en Dashboard Principal**
**Ubicación:** `resources/views/ong/dashboard/index.blade.php`

**Qué hacer:**
- Agregar tarjeta de "Próximos Mega Eventos" (similar a eventos)
- Mostrar los 3 próximos mega eventos
- Agregar enlace rápido para crear nuevo mega evento

**Ejemplo de código:**
```html
<!-- Agregar después de las tarjetas de estadísticas -->
<div class="col-12 mb-4">
    <div class="card border-0 shadow-sm" style="border-radius: 16px;">
        <div class="card-header bg-white border-0 pt-4 pb-3 px-4 d-flex justify-content-between">
            <h3 class="card-title mb-0" style="font-size: 1.3rem; font-weight: 700; color: #0C2B44;">
                <i class="fas fa-star mr-2" style="color: #00A36C;"></i>Próximos Mega Eventos
            </h3>
            <a href="/ong/mega-eventos/crear" class="btn btn-sm btn-success">
                <i class="fas fa-plus mr-1"></i>Nuevo
            </a>
        </div>
        <div class="card-body px-4 pb-4" id="proximosMegaEventos">
            <!-- Cargar dinámicamente -->
        </div>
    </div>
</div>
```

---

### 6. **Integración en Reportes**
**Ubicación:** `app/Http/Controllers/ReportController.php`

**Qué hacer:**
- Ya está integrado, pero puedes mejorar:
  - Agregar comparativa entre eventos regulares y mega eventos
  - Gráficos de participación en mega eventos
  - Análisis de impacto de mega eventos vs eventos regulares

---

### 7. **Notificaciones Push**
**Ubicación:** Sistema de notificaciones

**Qué hacer:**
- Notificar cuando se crea un nuevo mega evento público
- Recordatorios de mega eventos próximos
- Notificaciones de cambios en mega eventos

---

## 🚀 Implementación Rápida Recomendada

### Prioridad Alta:
1. **Agregar mega eventos al calendario del dashboard** (home-ong.blade.php)
2. **Widget de próximos mega eventos en dashboard** (home-ong.blade.php)
3. **Sección en página de bienvenida** (welcome.blade.php)

### Prioridad Media:
4. **Home pública con mega eventos** (home-publica.blade.php)
5. **Home externo con recomendaciones** (home-externo.blade.php)

### Prioridad Baja:
6. **Mejoras en reportes** (ya está integrado)
7. **Notificaciones** (requiere sistema de notificaciones)

---

## 📝 Archivos a Modificar

1. `resources/views/welcome.blade.php` - Agregar sección de mega eventos
2. `resources/views/ong/dashboard/index.blade.php` - Calendario y widget
3. `resources/views/home-publica.blade.php` - Sección pública
4. `resources/views/home-externo.blade.php` - Recomendaciones
5. `public/assets/js/ong/dashboard.js` (si existe) - Lógica JavaScript

---

## 🔗 APIs Disponibles

- `GET /api/mega-eventos` - Listar mega eventos (requiere auth)
- `GET /api/mega-eventos/publicos` - Mega eventos públicos (sin auth)
- `GET /api/mega-eventos/{id}` - Detalle de mega evento
- `GET /api/mega-eventos/en-curso` - Mega eventos en curso

---

## 💡 Tips de Implementación

1. **Reutilizar componentes:** Usa las mismas tarjetas de eventos pero con estilo diferente para mega eventos
2. **Iconos distintivos:** Usa `fa-star` para mega eventos vs `fa-calendar` para eventos regulares
3. **Colores:** Usa el color `#00A36C` (verde) para destacar mega eventos
4. **Lazy loading:** Carga los mega eventos de forma asíncrona para no afectar el rendimiento

---

## ❓ ¿Necesitas ayuda con alguna implementación específica?

Indica cuál de estas integraciones quieres implementar primero y te ayudo con el código completo.

# Paleta de Colores del Proyecto UNI2

Este documento define la paleta de colores oficial del proyecto UNI2.

## 🎨 Paleta de Colores

### Colores Principales

| Color | Código HEX | Nombre Tailwind | Uso |
|-------|------------|----------------|-----|
| ![#0C2B44](https://via.placeholder.com/50x50/0C2B44/0C2B44.png) | `#0C2B44` | `brand-primario` | Color principal - Azul Marino, botones principales, enlaces, títulos |
| ![#00A36C](https://via.placeholder.com/50x50/00A36C/00A36C.png) | `#00A36C` | `brand-acento` | Color de acento - Verde Esmeralda, highlights, elementos destacados |
| ![#FFFFFF](https://via.placeholder.com/50x50/FFFFFF/FFFFFF.png) | `#FFFFFF` | `brand-blanco` | Base neutral - Blanco puro, fondos principales |
| ![#333333](https://via.placeholder.com/50x50/333333/333333.png) | `#333333` | `brand-gris-oscuro` | Neutral oscuro - Gris carbón, textos principales |
| ![#F5F5F5](https://via.placeholder.com/50x50/F5F5F5/F5F5F5.png) | `#F5F5F5` | `brand-gris-suave` | Soporte - Gris suave, fondos alternos, secciones secundarias |

## 📋 Configuración en Tailwind

```javascript
tailwind.config = {
    theme: {
        extend: {
            colors: {
                'brand-primario': '#0C2B44',
                'brand-acento': '#00A36C',
                'brand-blanco': '#FFFFFF',
                'brand-gris-oscuro': '#333333',
                'brand-gris-suave': '#F5F5F5'
            }
        }
    }
}
```

## 🎯 Uso Recomendado

### Gradientes Principales
- **Hero Sections / CTAs:** `from-brand-primario via-brand-primario to-brand-acento`
- **Cards / Elementos:** `from-brand-primario to-brand-acento`
- **Acentos Suaves:** `from-brand-acento to-brand-primario`
- **Fondos Alternos:** `bg-brand-gris-suave`

### Botones
- **Primarios:** `bg-gradient-to-r from-brand-primario to-brand-acento` con texto blanco
- **Secundarios:** `bg-brand-acento` con texto blanco
- **Texto sobre fondo oscuro:** `bg-white text-brand-primario`

### Enlaces y Hovers
- **Enlaces:** `text-brand-gris-oscuro hover:text-brand-primario`
- **Hover en botones:** Usar sombras con `rgba(12, 43, 68, 0.4)`

### Iconos y Badges
- **Iconos principales:** `text-brand-primario`
- **Iconos de acento:** `text-brand-acento`
- **Badges:** `bg-brand-primario/10 text-brand-primario` o `bg-brand-acento/10 text-brand-acento`

### Fondos
- **Fondos principales:** `bg-white` o `bg-brand-blanco`
- **Fondos alternos:** `bg-brand-gris-suave`
- **Fondos oscuros:** `bg-brand-primario`
- **Overlays:** `bg-brand-primario/10` o `bg-brand-acento/5`

### Textos
- **Títulos principales:** `text-brand-gris-oscuro`
- **Textos secundarios:** `text-brand-gris-oscuro/70`
- **Textos sobre fondo oscuro:** `text-white`
- **Acentos en texto:** `bg-gradient-to-r from-brand-primario to-brand-acento bg-clip-text text-transparent`

## 📝 Guía de Aplicación

### Jerarquía Visual
1. **Primario (Azul Marino):** Elementos principales, navegación, botones primarios
2. **Acento (Verde Esmeralda):** Elementos destacados, CTAs secundarios, highlights
3. **Gris Oscuro:** Textos y contenido principal
4. **Gris Suave:** Fondos alternos y secciones secundarias
5. **Blanco:** Fondos principales y contraste

### Contraste y Accesibilidad
- ✅ Azul Marino (#0C2B44) sobre Blanco: **Excelente contraste** (WCAG AAA)
- ✅ Verde Esmeralda (#00A36C) sobre Blanco: **Buen contraste** (WCAG AA)
- ✅ Gris Carbón (#333333) sobre Blanco: **Excelente contraste** (WCAG AAA)
- ✅ Texto sobre fondos con opacidad: Usar `/70` o `/80` para textos secundarios

## 🔄 Migración de Colores Anteriores

| Color Anterior | Color Nuevo | Notas |
|---------------|-------------|-------|
| `brand-oscuro` (#330000) | `brand-primario` (#0C2B44) | Color principal |
| `brand-burgundy` (#62152d) | `brand-primario` (#0C2B44) | Color principal |
| `brand-magenta` (#952f57) | `brand-primario` (#0C2B44) | Color principal |
| `brand-rosa` (#ca668b) | `brand-acento` (#00A36C) | Color de acento |
| `brand-pastel` (#ff9ec2) | `brand-acento` (#00A36C) | Color de acento |
| `gray-700`, `gray-800` | `brand-gris-oscuro` (#333333) | Textos |
| `gray-600` | `brand-gris-oscuro/70` | Textos secundarios |
| `gray-50`, `gray-100` | `brand-gris-suave` (#F5F5F5) | Fondos |

## ✅ Archivos Actualizados

- ✅ `resources/views/welcome.blade.php` - Página de bienvenida completa

## 📋 Archivos Pendientes de Actualizar

- ⏳ Layouts AdminLTE (ONG, Empresa, Externo)
- ⏳ Páginas de autenticación
- ⏳ Dashboards
- ⏳ Formularios
- ⏳ Componentes reutilizables

## 🎨 Ejemplos de Uso

### Botón Primario
```html
<a href="#" class="px-6 py-3 bg-gradient-to-r from-brand-primario to-brand-acento text-white rounded-xl font-semibold hover:shadow-lg transition-all">
    Acción Principal
</a>
```

### Badge
```html
<span class="px-4 py-2 bg-brand-primario/10 text-brand-primario rounded-full text-sm font-semibold">
    Etiqueta
</span>
```

### Card con Gradiente
```html
<div class="bg-gradient-to-br from-brand-primario/5 to-brand-acento/5 rounded-2xl p-8">
    <!-- Contenido -->
</div>
```

### Título con Gradiente
```html
<h2 class="text-4xl font-bold bg-gradient-to-r from-brand-primario to-brand-acento bg-clip-text text-transparent">
    Título Destacado
</h2>
```

---

**Última actualización:** 2025-01-20

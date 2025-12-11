# Sistema de Reportes Avanzados para ONGs

## Instalación

### 1. Instalar dependencias

```bash
composer require maatwebsite/excel
composer require barryvdh/laravel-dompdf
```

### 2. Publicar configuraciones

```bash
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider" --tag=config
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider" --tag=config
```

### 3. Configurar storage link

```bash
php artisan storage:link
```

## Estructura del Sistema

### Arquitectura

```
app/
├── Http/Controllers/
│   └── ReportController.php          # Controlador principal de reportes
├── Services/
│   └── ReportService.php             # Lógica de negocio y cálculos
└── Exports/
    ├── EventosExport.php             # Exportación Excel de eventos
    ├── MegaEventosExport.php         # Exportación Excel de mega eventos
    └── ConsolidadoExport.php         # Exportación Excel consolidada

resources/views/ong/reportes/
├── dashboard.blade.php               # Dashboard principal
├── eventos.blade.php                 # Reporte de eventos regulares
├── mega-eventos.blade.php            # Reporte de mega eventos
├── consolidado.blade.php             # Reporte consolidado
└── components/
    ├── filter-form.blade.php         # Formulario de filtros
    ├── export-buttons.blade.php      # Botones de exportación
    ├── metric-card.blade.php         # Card de métrica
    └── chart-container.blade.php    # Contenedor de gráficos

public/
├── css/
│   └── reportes-ong.css              # Estilos minimalistas
└── js/
    └── reportes-ong.js               # JavaScript para interactividad
```

## Uso

### Acceder a los Reportes

1. **Dashboard Principal**: `/ong/reportes`
2. **Eventos Regulares**: `/ong/reportes/eventos`
3. **Mega Eventos**: `/ong/reportes/mega-eventos`
4. **Consolidado**: `/ong/reportes/consolidado`

### Aplicar Filtros

Los filtros disponibles incluyen:
- Rango de fechas (Desde/Hasta)
- Categoría
- Estado (múltiples selección)
- Ubicación
- Rango de participantes

### Exportar Reportes

Cada reporte puede exportarse en:
- **PDF**: Formato profesional con gráficos y tablas
- **Excel**: Múltiples sheets con formato
- **CSV**: Datos en formato CSV
- **JSON**: Datos estructurados en JSON

## Métricas Disponibles

### Eventos Regulares
- Total de eventos creados
- Total de participantes
- Eventos activos
- Promedio de participantes por evento
- Tasa de ocupación promedio
- Distribución por categoría
- Distribución por estado
- Top 5 eventos por participantes
- Tendencias temporales
- Análisis geográfico
- Total de patrocinios
- Duración promedio
- Distribución público/privado

### Mega Eventos
- Total de mega eventos
- Total de participantes
- Mega eventos activos
- Promedio de participantes
- Tasa de ocupación promedio
- Distribución por categoría
- Distribución por estado

### Consolidado
- Total general de participantes
- Total general de eventos
- Comparativa lado a lado
- Mejor rendimiento (eventos vs mega eventos)
- Distribución porcentual

## Queries Optimizadas

El sistema utiliza:
- **Eager Loading** para evitar N+1 queries
- **Select específico** en lugar de SELECT *
- **DB::raw** para cálculos agregados
- **Cache** para resultados (1 hora)
- **Índices** en columnas frecuentemente filtradas

## Diseño Minimalista

El diseño sigue principios minimalistas:
- Paleta de colores limitada (verde #00A36C, azul #0C2B44, grises)
- Espaciado generoso (30px padding, 20px entre secciones)
- Tipografía sans-serif (Inter/Roboto)
- Cards con sombras sutiles
- Botones bien espaciados (15px gap)
- Hover effects sutiles

## Visualizaciones

Gráficos implementados con Chart.js:
- **Barras verticales**: Distribución por categoría
- **Dona (Doughnut)**: Distribución por estado (cutout 70%)
- **Líneas**: Tendencias temporales
- **Barras horizontales**: Top eventos

## Seguridad

- Validación de autenticación (solo ONGs)
- Sanitización de inputs
- Rate limiting en exportaciones (10/hora)
- Validación de filtros
- Límite de registros exportables (10,000)

## Notas Técnicas

- El sistema usa tokens de localStorage para autenticación
- Las rutas web renderizan vistas, los datos se cargan vía API
- Los gráficos se generan dinámicamente con Chart.js
- Los PDFs usan DomPDF con CSS inline
- Los Excel usan Laravel Excel con múltiples sheets


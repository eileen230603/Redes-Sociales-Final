# Sistema de Reportes Avanzados - Guía de Instalación

## Dependencias Requeridas

Este sistema de reportes requiere las siguientes dependencias de Composer:

### 1. Laravel Excel (Maatwebsite)
```bash
composer require maatwebsite/excel
```

### 2. DomPDF (ya instalado)
```bash
composer require barryvdh/laravel-dompdf
```

## Configuración

### 1. Publicar configuración de Excel
```bash
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider" --tag=config
```

### 2. Publicar configuración de DomPDF (si no está publicado)
```bash
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
```

### 3. Verificar configuración

Asegúrate de que en `config/excel.php` esté configurado correctamente.

## Estructura de Archivos Creados

```
app/
├── Http/Controllers/
│   └── ReportController.php          # Controlador principal de reportes
├── Services/
│   └── ReportService.php              # Lógica de negocio para reportes
└── Exports/
    ├── MegaEventosResumenExport.php
    ├── AnalisisTemporalExport.php
    ├── ParticipacionColaboracionExport.php
    ├── AnalisisGeograficoExport.php
    └── RendimientoOngExport.php

resources/views/ong/reportes/
├── dashboard.blade.php                # Dashboard principal
└── resumen-ejecutivo.blade.php        # Reporte 1 (ejemplo completo)
```

## Vistas Pendientes de Crear

Siguiendo el mismo patrón de `resumen-ejecutivo.blade.php`, crear:

1. `analisis-temporal.blade.php` - Reporte 2
2. `participacion-colaboracion.blade.php` - Reporte 3
3. `analisis-geografico.blade.php` - Reporte 4
4. `rendimiento-ong.blade.php` - Reporte 5

Y las vistas de exportación PDF en:
```
resources/views/ong/reportes/exports/
├── resumen-ejecutivo-pdf.blade.php
├── analisis-temporal-pdf.blade.php
├── participacion-colaboracion-pdf.blade.php
├── analisis-geografico-pdf.blade.php
└── rendimiento-ong-pdf.blade.php
```

## Rutas Configuradas

Todas las rutas están bajo el prefijo `/ong/reportes/` con middleware `auth`:

- `GET /ong/reportes` - Dashboard principal
- `GET /ong/reportes/resumen-ejecutivo` - Reporte 1
- `GET /ong/reportes/resumen-ejecutivo/exportar/pdf` - Exportar PDF
- `GET /ong/reportes/resumen-ejecutivo/exportar/excel` - Exportar Excel
- (Similar para los otros 4 reportes)

## Características Implementadas

✅ ReportController completo con todos los métodos
✅ ReportService con lógica de negocio optimizada
✅ Export classes para Excel (5 reportes)
✅ Validación y sanitización de filtros
✅ Cache para optimización (5 minutos)
✅ Logging para auditoría
✅ Dashboard principal con KPIs
✅ Vista completa del Reporte 1 con Chart.js

## Pendiente de Implementar

- Vistas Blade para reportes 2, 3, 4 y 5
- Vistas PDF para todos los reportes
- Funcionalidades extras (programación automática, compartir por email, etc.)

## Uso

1. Acceder a `/ong/reportes` como usuario tipo ONG
2. Seleccionar el reporte deseado
3. Aplicar filtros si es necesario
4. Exportar en el formato deseado (PDF, Excel, CSV, JSON según el reporte)

## Notas

- Las consultas están optimizadas con eager loading
- Se usa cache para reportes que no cambian frecuentemente
- Los filtros están validados y sanitizados para prevenir SQL injection
- El diseño es responsive y sigue la paleta de colores del sistema


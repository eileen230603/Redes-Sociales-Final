# 🎯 Sistema de Exportación PDF Profesional para Laravel

## 📋 Descripción

Sistema completo de exportación de reportes a PDF para aplicación Laravel que gestiona eventos de ONGs. Genera PDFs profesionales con diseño moderno tipo factura comercial, márgenes del 10% a cada lado, y funcionalidades avanzadas.

## 🚀 Instalación

### Paso 1: Instalar DomPDF

```bash
composer require barryvdh/laravel-dompdf
```

### Paso 2: Publicar Configuración (Opcional)

```bash
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
```

Esto creará el archivo `config/dompdf.php` con todas las opciones configurables.

### Paso 3: Configurar DomPDF

Editar `config/dompdf.php`:

```php
return [
    'options' => [
        'enable_remote' => true,           // Cargar imágenes externas
        'default_paper_size' => 'a4',
        'default_paper_orientation' => 'portrait',
        'enable_html5_parser' => true,
        'font_dir' => storage_path('fonts/'),
        'font_cache' => storage_path('fonts/'),
        'dpi' => 96,
        'enable_javascript' => true,
    ],
];
```

### Paso 4: Crear Estructura de Carpetas

```bash
mkdir -p resources/views/pdf
mkdir -p resources/views/ong/reportes/exports
```

### Paso 5: Verificar Modelo Ong

Asegurarse de que el modelo `Ong` tenga el accessor `getLogoUrlAttribute()` (ya implementado).

### Paso 6: Agregar Rutas

En `routes/web.php`, agregar:

```php
use App\Http\Controllers\ReportController;

Route::middleware(['auth', 'tipo.usuario:ONG'])->prefix('reportes/pdf')->group(function () {
    Route::get('/resumen-ejecutivo', [ReportController::class, 'exportarResumenEjecutivoPDF'])
        ->name('reportes.pdf.resumen-ejecutivo');
    
    Route::get('/resumen-ejecutivo/preview', [ReportController::class, 'exportarResumenEjecutivoPDF'])
        ->name('reportes.pdf.resumen-ejecutivo.preview');
    
    // Agregar más rutas para otros tipos de reportes
});
```

## 📖 Uso

### Desde JavaScript Vanilla

```javascript
function exportarPDF(tipoReporte, filtros) {
    const params = new URLSearchParams();
    
    // Agregar filtros
    if (filtros.fecha_inicio) params.append('fecha_inicio', filtros.fecha_inicio);
    if (filtros.fecha_fin) params.append('fecha_fin', filtros.fecha_fin);
    if (filtros.categoria) params.append('categoria', filtros.categoria);
    if (filtros.estado) params.append('estado', filtros.estado);
    
    // Construir URL
    const url = `/reportes/pdf/${tipoReporte}?${params.toString()}`;
    
    // Descargar PDF
    window.location.href = url;
}

// Ejemplo de uso
exportarPDF('resumen-ejecutivo', {
    fecha_inicio: '2024-01-01',
    fecha_fin: '2024-12-31',
    categoria: 'social'
});
```

### Desde Blade

```blade
<a href="{{ route('reportes.pdf.resumen-ejecutivo', [
    'fecha_inicio' => '2024-01-01',
    'fecha_fin' => '2024-12-31',
    'categoria' => 'social'
]) }}" 
   class="btn btn-primary" 
   download>
    <i class="fas fa-file-pdf"></i> Exportar PDF
</a>
```

### Desde React (Componente)

```jsx
import { Download, Eye } from 'lucide-react';

function ExportarPDFButton({ tipoReporte, filtros, showPreview = false, variant = 'primary' }) {
    const [isExporting, setIsExporting] = useState(false);
    
    const buildURL = () => {
        const params = new URLSearchParams();
        Object.entries(filtros || {}).forEach(([key, value]) => {
            if (value) params.append(key, value);
        });
        return `/reportes/pdf/${tipoReporte}?${params.toString()}`;
    };
    
    const handleExport = () => {
        setIsExporting(true);
        const url = buildURL();
        const a = document.createElement('a');
        a.href = url;
        a.download = `reporte-${tipoReporte}.pdf`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        setTimeout(() => setIsExporting(false), 1000);
    };
    
    const handlePreview = () => {
        const url = buildURL();
        window.open(url, '_blank');
    };
    
    const variantClasses = {
        primary: 'bg-blue-600 hover:bg-blue-700 text-white',
        secondary: 'bg-gray-600 hover:bg-gray-700 text-white',
        outline: 'border-2 border-blue-600 text-blue-600 hover:bg-blue-50'
    };
    
    return (
        <div className="inline-flex gap-2">
            <button
                onClick={handleExport}
                disabled={isExporting}
                className={`inline-flex items-center gap-2 px-4 py-2 rounded-lg font-medium transition-all shadow-md hover:shadow-lg disabled:opacity-50 ${variantClasses[variant]}`}
            >
                {isExporting ? (
                    <span className="spinner-border spinner-border-sm" />
                ) : (
                    <Download size={18} />
                )}
                Exportar PDF
            </button>
            
            {showPreview && (
                <button
                    onClick={handlePreview}
                    className="inline-flex items-center gap-2 px-4 py-2 rounded-lg border-2 border-gray-300 text-gray-700 hover:bg-gray-50"
                >
                    <Eye size={18} />
                    Vista Previa
                </button>
            )}
        </div>
    );
}
```

## 🎨 Características del Diseño

### Márgenes
- **10%** de margen en todos los lados (configurado en `@page { margin: 10%; }`)
- El contenido no ocupa toda la hoja, dejando espacio respirable

### Header
- Logo de la ONG (máximo 120px) alineado a la izquierda
- Datos de contacto (nombre, teléfono, email, dirección) a la derecha
- Borde inferior de 3px color #2c3e50

### Metadata
- Fondo gris claro (#ecf0f1)
- Borde izquierdo azul (#3498db) de 4px
- Muestra fecha y hora de generación capturadas con `Carbon::now()`
- Filtros aplicados

### KPIs
- Grid de 3 columnas usando `display: table`
- Bordes de 1px, fondo degradado sutil
- Valores en 20-28pt bold
- Etiquetas en 9pt uppercase gris

### Tablas
- Header oscuro (#34495e) con texto blanco
- Filas alternadas en gris claro (#f9f9f9)
- Badges de colores según estado
- Padding de 8-10px

### Gráficos
- Barras horizontales CSS simuladas
- Width porcentual calculado desde el máximo valor

### Insights
- Cajas con fondo verde claro (#e8f5e9)
- Borde izquierdo verde (#27ae60)
- Mensajes automáticos basados en análisis de datos

### Footer
- Borde superior gris
- Texto centrado en 8pt gris
- Copyright, nombre de ONG y fecha/hora de generación

## 🔧 Configuración Avanzada

### Cachear PDFs

```php
use Illuminate\Support\Facades\Cache;

public function exportarResumenEjecutivoPDF(Request $request)
{
    $cacheKey = 'pdf_resumen_' . $user->id_usuario . '_' . md5(json_encode($filtros));
    
    return Cache::remember($cacheKey, 3600, function () use ($datos, $pdfData) {
        $pdf = Pdf::loadView('ong.reportes.exports.resumen-ejecutivo-pdf', [
            'datos' => $datos,
            'pdfData' => $pdfData
        ]);
        return $pdf->output();
    });
}
```

### Procesar en Background con Jobs

```php
php artisan make:job GenerarPDFReporte

// En el Job
public function handle()
{
    $pdf = Pdf::loadView('pdf.reporte', $this->data);
    Storage::put("pdfs/{$this->filename}", $pdf->output());
}
```

## 🐛 Troubleshooting

### Imágenes no cargan

**Problema:** Las imágenes del logo no aparecen en el PDF.

**Solución:**
- Usar `public_path()` en lugar de `asset()` para rutas absolutas
- Verificar que `enable_remote` esté en `true` en `config/dompdf.php`
- Asegurarse de que las imágenes estén en `public/storage/`

```php
$logoUrl = public_path('storage/' . $ong->foto_perfil);
```

### CSS no funciona

**Problema:** Los estilos no se aplican correctamente.

**Solución:**
- DomPDF solo soporta CSS 2.1, NO flexbox ni grid
- Usar `display: table` y `display: table-cell` en lugar de flexbox
- Usar `position: absolute/relative` en lugar de transform
- Todos los estilos deben estar inline o en `<style>` dentro del `<head>`

### Caracteres especiales raros

**Problema:** Caracteres como ñ, á, é aparecen mal.

**Solución:**
- Verificar `<meta charset="utf-8">` en el HTML
- Usar fuentes que soporten UTF-8 (Helvetica, Arial)
- Configurar `mb_internal_encoding('UTF-8')` en el controlador

### PDF en blanco

**Problema:** El PDF se genera pero está vacío.

**Solución:**
- Revisar logs en `storage/logs/laravel.log`
- Verificar errores de DomPDF
- Asegurarse de que la vista Blade no tenga errores de sintaxis
- Verificar que los datos se estén pasando correctamente

### Memoria insuficiente

**Problema:** Error "Allowed memory size exhausted".

**Solución:**
- Aumentar `memory_limit` en `php.ini`
- O usar `ini_set('memory_limit', '256M')` al inicio del controlador

```php
public function exportarResumenEjecutivoPDF(Request $request)
{
    ini_set('memory_limit', '256M');
    // ... resto del código
}
```

### Timeout en reportes grandes

**Problema:** El PDF tarda mucho en generarse.

**Solución:**
- Aumentar `max_execution_time` en `php.ini`
- O procesar por chunks usando Jobs en background
- Limitar la cantidad de registros mostrados

## 📊 Tipos de Reportes Disponibles

### 1. Resumen Ejecutivo
- Totales consolidados de eventos regulares + mega eventos
- KPIs de finalización/cancelación
- Distribución por categoría con barras visuales
- Estadísticas de participantes y patrocinadores

### 2. Análisis Temporal
- Evolución mensual con tabla comparativa
- Año actual vs anterior
- Crecimiento porcentual
- Gráficos de barras CSS
- Promedio mensual
- Insights automáticos basados en tendencias

### 3. KPIs Destacados
- Métricas separadas por tipo de evento
- Cards grandes con totales consolidados
- Análisis comparativo últimos 6 meses vs anteriores
- Tasas de utilización y ocupación

### 4. Participación y Colaboración
- Top 10 empresas patrocinadoras
- Top 10 voluntarios activos
- Eventos con más colaboradores

## 🎯 Mejores Prácticas

1. **Siempre capturar fecha/hora real**: Usar `Carbon::now()` dentro del método del controlador
2. **Validar datos**: Verificar que los filtros sean válidos antes de generar el PDF
3. **Manejo de errores**: Usar try-catch y logging para debugging
4. **Optimización**: Cachear PDFs cuando sea posible
5. **Seguridad**: Validar permisos antes de generar reportes
6. **Formato consistente**: Usar métodos auxiliares de formateo del ReportService

## 📝 Notas Importantes

- DomPDF solo soporta CSS 2.1, NO flexbox, grid, ni transform complejos
- Las imágenes deben estar en rutas absolutas o URLs completas
- El charset debe ser UTF-8 para caracteres especiales
- Los márgenes del 10% están configurados en `@page { margin: 10%; }`
- La fecha y hora se capturan con `Carbon::now()` en el momento exacto de la exportación

## 🔗 Referencias

- [Documentación DomPDF](https://github.com/barryvdh/laravel-dompdf)
- [Laravel Documentation](https://laravel.com/docs)
- [Carbon Documentation](https://carbon.nesbot.com/docs/)


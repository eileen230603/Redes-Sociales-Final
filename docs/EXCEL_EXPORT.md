# Documentación: Exportación Excel Dashboard ONG

## Descripción General

El sistema de exportación Excel del Dashboard ONG genera un archivo `.xlsx` profesional con diseño Power BI que contiene 10 hojas con análisis completo de la gestión de eventos de una ONG.

## Estructura del Archivo

### Hoja 1: Portada
**Propósito:** Presentación profesional del reporte

**Contenido:**
- Título principal "DASHBOARD ANALÍTICO"
- Información de la organización
- Número de folio único (formato: DASH-000001)
- Período analizado
- Fecha de generación
- Aviso de confidencialidad
- Footer "Powered by UNI2 Analytics Platform"

**Diseño:**
- Fondo azul oscuro (#0C2B44)
- Título en blanco 32px
- Subtítulo en verde (#00A36C) 24px

---

### Hoja 2: 📊 Resumen Ejecutivo
**Propósito:** KPIs principales con comparativas y métricas de engagement

**Contenido:**
- Información general de la ONG y período
- KPIs principales con valores actuales, anteriores y variación
- Distribución de eventos por estado con porcentajes
- Métricas de engagement (tasas por evento)

**Fórmulas clave:**
- Variación: `=(actual - anterior) / anterior` con protección #DIV/0!
- Porcentajes: `=B14/(B14+B15+B16)` para distribución
- Tasas: `=B7/(B14+B15+B16)` para engagement por evento

**Colores:**
- Header KPIs: Verde (#00A36C)
- Header Distribución: Rojo (#DC3545)
- Header Engagement: Azul claro (#17A2B8)

---

### Hoja 3: 📈 Métricas Principales
**Propósito:** Análisis detallado de métricas con categorías

**Contenido:**
- Métricas generales (eventos por estado)
- Engagement (reacciones, compartidos, estimados)
- Participación (voluntarios, participantes, estimados)
- Ratios y promedios con fórmulas

**Fórmulas clave:**
- Estimados: `=B8*0.7` (Me Gusta), `=B8*0.3` (Comentarios)
- Ratios: `=B8/(B2+B3+B4+B5)` con validación de denominador
- Tasa Engagement: `=(B8+B9)/B15` con protección

**Colores por sección:**
- Métricas Generales: Azul oscuro (#0C2B44)
- Engagement: Verde (#00A36C)
- Participación: Azul claro (#17A2B8)
- Ratios: Naranja (#FFA500)

---

### Hoja 4: 🏆 Top Eventos
**Propósito:** Ranking de top 10 eventos por engagement

**Contenido:**
- Lista de eventos ordenados por engagement descendente
- Columnas: #, Título, Reacciones, Compartidos, Inscripciones, Engagement Total, Estado
- Fila de totales con fórmulas SUMA

**Fórmulas:**
- Engagement: `=C{row}+D{row}+E{row}`
- Totales: `=SUMA(C3:C{lastRow})`

**Características:**
- Columna ranking con fondo gris oscuro
- Formato condicional por estado
- Paneles congelados en fila 2

---

### Hoja 5: 👥 Top Voluntarios
**Propósito:** Hall of Fame de voluntarios más activos

**Contenido:**
- Ranking de voluntarios por eventos participados
- Columnas: #, Nombre, Email, Eventos Participados, Horas Contribuidas, Reconocimiento
- Sistema de badges: ⭐⭐⭐ Gold (>10), ⭐⭐ Silver (5-10), ⭐ Bronze (<5)

**Fórmulas:**
- Horas: `=D{row}*2` (2 horas promedio por evento)
- Totales: `=SUMA(D4:D{lastRow})`

**Características:**
- Título dorado destacado
- Formato condicional por badge
- Nota explicativa del cálculo de horas

---

### Hoja 6: 📊 Tendencias Temporales
**Propósito:** Análisis de series de tiempo con promedios móviles

**Contenido:**
- KPIs del período (crecimiento, mejor/peor mes, volatilidad)
- Tabla de tendencias mensuales con:
  - Variación % (primera fila: N/A)
  - Promedio móvil 3M (primeras 2 filas: N/A)
  - Tendencia calculada (↑ Creciendo, ↓ Decreciendo, → Estable)
- Estadísticas avanzadas (máximo, mínimo, rango, coeficiente variación)

**Fórmulas clave:**
- Crecimiento: `=SI(B{first}=0, 0, (B{last}-B{first})/B{first})`
- Variación: `=SI(B{prev}=0, 0, (B{row}-B{prev})/B{prev})`
- Promedio móvil: `=PROMEDIO(B{row-2}:B{row})`
- Tendencia: `=SI(C{row}>0,"↑ Creciendo",SI(C{row}<0,"↓ Decreciendo","→ Estable"))`

**Formatos:**
- Participantes: `#,##0`
- Variación: `0.0%`
- Promedio móvil: `#,##0.0`

---

### Hoja 7: 📊 Distribución Estados
**Propósito:** Análisis de distribución con semáforo visual

**Contenido:**
- Tabla de frecuencias por estado (Activo, Inactivo, Finalizado, Cancelado)
- Porcentajes y porcentaje acumulado
- Métricas derivadas (tasas de finalización, actividad, cancelación)
- Interpretación (estado predominante, salud del programa)

**Fórmulas:**
- Porcentaje: `=SI($B$2=0, 0, B5/$B$2)`
- Porcentaje acumulado: `=C6+D5` (progresivo)
- Estado predominante: `=SI(B5=MAX($B$5:$B$8),"Activo",...)`
- Salud: `=SI(C5>0.5,"Excelente",SI(C5>0.3,"Bueno",...))`

**Colores por estado:**
- Activo: Verde claro (#E8F5E9)
- Inactivo: Amarillo claro (#FFF9C4)
- Finalizado: Rojo claro (#FFCDD2)
- Cancelado: Gris (#E0E0E0)

---

### Hoja 8: 📋 Listado Completo
**Propósito:** Tabla filtrable completa de todos los eventos

**Contenido:**
- Instrucciones de uso de filtros
- Tabla completa con: ID, Título, Fechas, Duración, Ubicación, Estado, Participantes, Tipo
- Resumen estadístico (totales, promedios, máximos)

**Fórmulas:**
- Duración: `=SI(D{row}="N/A","N/A",D{row}-C{row})`
- Total eventos: `=CONTARA(A5:A{lastRow})`
- Eventos regulares: `=CONTAR.SI(I5:I{lastRow},"Evento")`
- Evento con más participantes: `=INDICE(B5:B{lastRow},COINCIDIR(MAX(H5:H{lastRow}),H5:H{lastRow},0))`

**Características:**
- Autofiltros habilitados
- Paneles congelados en fila 4 y columna B
- Formato condicional por estado y tipo

---

### Hoja 9: 🔍 Análisis Comparativo
**Propósito:** Comparación período actual vs anterior

**Contenido:**
- Tabla comparativa de métricas
- Diferencia y variación % calculadas
- Tendencia visual (👍 Crecimiento, 👎 Decrecimiento, ➡️ Estable)
- Insights clave (mayor crecimiento, mayor decrecimiento, métricas estables)
- Recomendaciones automáticas basadas en análisis

**Fórmulas:**
- Diferencia: `=B{row}-C{row}`
- Variación %: `=SI(C{row}=0, 0, (B{row}-C{row})/C{row})`
- Tendencia: `=SI(E{row}>0,"👍 Crecimiento",SI(E{row}<0,"👎 Decrecimiento","➡️ Estable"))`
- Mayor crecimiento: `=INDICE(A5:A{lastRow},COINCIDIR(MAX(E5:E{lastRow}),E5:E{lastRow},0))`

**Colores:**
- Período actual: Azul claro (#E3F2FD)
- Período anterior: Naranja claro (#FFF3E0)
- Crecimiento: Verde (#E8F5E9)
- Decrecimiento: Rojo (#FFCDD2)

---

### Hoja 10: ⚠️ Alertas
**Propósito:** Sistema de monitoreo y recomendaciones

**Contenido:**
- Dashboard de salud con tarjetas KPI
- Tabla de alertas ordenadas por severidad
- Resumen por tipo de alerta
- Acciones prioritarias inmediatas (top 5 críticas)

**Tipos de alerta:**
- `evento_proximo`: Evento próximo a iniciar
- `baja_participacion`: Evento con baja participación
- `sin_voluntarios`: Evento sin voluntarios suficientes
- `pendiente_evaluacion`: Evento pendiente de evaluación

**Severidades:**
- `danger`: Crítica (rojo)
- `warning`: Advertencia (amarillo)
- `info`: Informativa (azul)

**Fórmulas:**
- Contar críticas: `=CONTAR.SI(B10:B1000,"danger")`
- Prioridad: `=SI(A{row}="danger","ALTA",SI(A{row}="warning","MEDIA","BAJA"))`
- Salud general: `=SI(B4=0,SI(D4<3,"Excelente","Bueno"),SI(B4<3,"Regular","Crítico"))`

---

## Paleta de Colores Power BI

### Colores Principales
- **Azul oscuro:** `#0C2B44` - Headers principales, fondos destacados
- **Verde corporativo:** `#00A36C` - Métricas positivas, activos, éxito
- **Rojo:** `#DC3545` - Alertas, finalizados, errores
- **Azul claro:** `#17A2B8` - Información secundaria, datos neutros
- **Amarillo:** `#FFC107` - Advertencias, pendientes
- **Gris claro:** `#F8F9FA` - Filas alternas, fondos suaves
- **Gris medio:** `#CCCCCC` - Bordes, separadores
- **Verde menta:** `#E8F5E9` - Fondos informativos, estados positivos
- **Blanco:** `#FFFFFF` - Texto sobre oscuro, fondos limpios

### Colores Especiales
- **Dorado:** `#FFD700` - Hall of Fame, reconocimientos
- **Plateado:** `#E0E0E0` - Badges Silver
- **Bronce:** `#CD7F32` - Badges Bronze
- **Naranja:** `#FFA500` - Headers de secciones especiales

---

## Fórmulas y Validaciones

### Protección contra #DIV/0!
Todas las divisiones deben usar:
```excel
=SI(denominador=0, 0, numerador/denominador)
```
o
```excel
=SI(denominador=0, "N/A", numerador/denominador)
```

### Referencias Absolutas
Usar `$B$2` cuando la referencia debe mantenerse constante al copiar fórmulas.

### Referencias Dinámicas
Calcular rangos dinámicamente:
```excel
=SUMA(B5:B{$lastRow})
```
En lugar de rangos fijos como `B5:B100`.

---

## Formatos Numéricos

- **Enteros:** `#,##0` (con separador de miles)
- **Decimales:** `0.00` (2 decimales)
- **Porcentajes:** `0.0%` (1 decimal)
- **Fechas:** `dd/mm/yyyy` (formato español)

---

## Troubleshooting

### Error: "Class 'App\Exports\OngDashboardExport' not found"
**Solución:** Verificar que el archivo existe en `app/Exports/OngDashboardExport.php` y ejecutar:
```bash
composer dump-autoload
```

### Error: "#DIV/0!" en celdas
**Solución:** Verificar que todas las fórmulas de división tengan protección `SI(denominador=0, ...)`

### Error: "#REF!" en fórmulas
**Solución:** Verificar que las referencias de celdas sean correctas y que no se hayan eliminado filas referenciadas

### Archivo muy grande (>5MB)
**Solución:** 
- Reducir rango de fechas
- Implementar generación asíncrona con Jobs
- Considerar comprimir en ZIP

### Tiempo de generación >60 segundos
**Solución:**
- Verificar que cache esté funcionando
- Optimizar queries en `obtenerDatosDashboard`
- Considerar generación asíncrona

---

## Extensión del Sistema

### Agregar Nueva Hoja

1. Crear nueva clase en `OngDashboardExport.php`:
```php
class NuevaHojaSheet implements FromCollection, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    protected $datos;
    
    public function __construct($datos) {
        $this->datos = $datos;
    }
    
    // Implementar métodos requeridos
}
```

2. Agregar a método `sheets()`:
```php
new NuevaHojaSheet($this->datos),
```

3. Agregar datos en `obtenerDatosDashboard()` del controlador

---

## Versión
**v1.0** - Implementación completa con 10 hojas Power BI

**Última actualización:** {{ fecha_actual }}

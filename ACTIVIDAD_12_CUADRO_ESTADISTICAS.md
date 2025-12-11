# Actividad 12: Cuadro de Estadísticas - Guía Completa

## 📸 1. CAPTURAS DE PANTALLA REQUERIDAS

### Pantalla 1: Dashboard General de ONG
**Ruta:** `/ong/dashboard`
**Qué capturar:**
- Vista completa de la página mostrando:
  - Las 6 tarjetas de métricas principales (Eventos Activos, Eventos Inactivos, Eventos Finalizados, Total Reacciones, Total Compartidos, Total Voluntarios)
  - Los 6 gráficos principales:
    1. Tendencias Mensuales de Participantes
    2. Distribución de Estados de Eventos
    3. Comparativa de Rendimiento entre Eventos
    4. Actividad Semanal Agregada
    5. Reacciones vs Compartidos por Evento
    6. Métricas Generales (Gráfico Radar)
  - Tabla de Listado de Eventos
  - Tabla de Actividad de los Últimos 30 Días
  - Tabla de Top 10 Eventos
  - Tabla de Top 10 Voluntarios

### Pantalla 2: Dashboard Individual de Evento
**Ruta:** `/ong/eventos/{id}/dashboard`
**Qué capturar:**
- Vista completa mostrando:
  - Las 4 tarjetas de métricas (Reacciones, Compartidos, Voluntarios, Participantes)
  - Los 8 gráficos:
    1. Reacciones por Día
    2. Participantes por Estado
    3. Compartidos por Día
    4. Inscripciones por Día
    5. Comparativa de Métricas
    6. Actividad Semanal
    7. Tendencias Temporales
    8. Métricas Generales (Radar)
  - Tabla de Actividad Reciente
  - Tabla de Top Participantes
  - Tabla de Distribución por Estados

### Pantalla 3: Código del Controlador (Backend)
**Archivo:** `app/Http/Controllers/Ong/OngDashboardController.php`
**Líneas a capturar:**
- Método `dashboard()` (líneas ~140-250)
- Método `obtenerMetricasPrincipales()` (líneas ~280-350)
- Método `obtenerTendenciasMensuales()` (líneas ~360-420)

### Pantalla 4: Código de la Vista (Frontend)
**Archivo:** `resources/views/ong/dashboard.blade.php`
**Líneas a capturar:**
- Sección de tarjetas de métricas (líneas ~87-150)
- Sección de gráficos con Chart.js (líneas ~190-280)
- Función JavaScript `crearGraficas()` (líneas ~770-1100)

### Pantalla 5: Consultas SQL/Eloquent
**Archivo:** `app/Http/Controllers/Ong/OngDashboardController.php`
**Líneas a capturar:**
- Consultas agregadas con `DB::table()` y `groupBy()`
- Consultas con `join()` para relacionar tablas
- Uso de `selectRaw()` para cálculos

---

## 📊 2. DESCRIPCIÓN DEL PROPÓSITO DE CADA GRÁFICO O INDICADOR

### Dashboard General de ONG

#### **Tarjetas de Métricas Principales:**

1. **Eventos Activos**
   - **Propósito:** Mostrar el número total de eventos que están actualmente en curso
   - **Valor:** Permite a la ONG conocer rápidamente cuántos eventos requieren atención activa
   - **Fuente de datos:** Tabla `eventos` filtrada por `fecha_inicio <= now()` y `fecha_fin >= now()`

2. **Eventos Inactivos**
   - **Propósito:** Indicar eventos que están en estado borrador o no publicados
   - **Valor:** Ayuda a identificar eventos pendientes de publicación
   - **Fuente de datos:** Tabla `eventos` con `estado = 'borrador'` o `'inactivo'`

3. **Eventos Finalizados**
   - **Propósito:** Contar eventos que ya completaron su ciclo de vida
   - **Valor:** Permite evaluar el historial de eventos realizados
   - **Fuente de datos:** Tabla `eventos` con `fecha_fin < now()` o `estado = 'finalizado'`

4. **Total Reacciones**
   - **Propósito:** Sumar todas las reacciones (likes) recibidas en todos los eventos
   - **Valor:** Mide el engagement total de la audiencia con los eventos
   - **Fuente de datos:** Tabla `evento_reacciones` agregada por `evento_id`

5. **Total Compartidos**
   - **Propósito:** Contar todas las veces que los eventos fueron compartidos
   - **Valor:** Indica el alcance y viralidad de los eventos
   - **Fuente de datos:** Tabla `evento_compartidos` agregada por `evento_id`

6. **Total Voluntarios**
   - **Propósito:** Mostrar el número único de voluntarios que han participado
   - **Valor:** Permite conocer el tamaño de la base de voluntarios activos
   - **Fuente de datos:** Tabla `evento_participaciones` con `DISTINCT externo_id`

#### **Gráficos:**

1. **Tendencias Mensuales de Participantes (Gráfico de Líneas)**
   - **Propósito:** Visualizar la evolución del número de participantes mes a mes
   - **Valor:** Identifica tendencias de crecimiento o decrecimiento en la participación
   - **Tipo:** Line Chart con múltiples datasets
   - **Datos:** Agrupación mensual de `evento_participaciones` por `DATE_TRUNC('month', created_at)`

2. **Distribución de Estados de Eventos (Gráfico de Dona)**
   - **Propósito:** Mostrar la proporción de eventos por estado (activo, finalizado, inactivo)
   - **Valor:** Proporciona una vista rápida del estado general del portafolio de eventos
   - **Tipo:** Doughnut Chart
   - **Datos:** Conteo agrupado por `estado` en tabla `eventos`

3. **Comparativa de Rendimiento entre Eventos (Gráfico de Barras)**
   - **Propósito:** Comparar métricas clave (reacciones, compartidos, participantes) entre diferentes eventos
   - **Valor:** Identifica qué eventos tienen mejor rendimiento para replicar estrategias exitosas
   - **Tipo:** Bar Chart agrupado
   - **Datos:** Agregación por `evento_id` de múltiples tablas

4. **Actividad Semanal Agregada (Gráfico de Área)**
   - **Propósito:** Mostrar la actividad total (reacciones + compartidos + inscripciones) por semana
   - **Valor:** Identifica semanas de mayor actividad para planificación futura
   - **Tipo:** Area Chart (Line Chart con fill)
   - **Datos:** Agrupación semanal usando `DATE_TRUNC('week', created_at)`

5. **Reacciones vs Compartidos por Evento (Gráfico de Columnas Apiladas)**
   - **Propósito:** Comparar visualmente reacciones y compartidos para cada evento
   - **Valor:** Ayuda a entender qué eventos generan más engagement vs alcance
   - **Tipo:** Stacked Bar Chart
   - **Datos:** Join entre `evento_reacciones` y `evento_compartidos` agrupado por evento

6. **Métricas Generales (Gráfico Radar)**
   - **Propósito:** Visualizar múltiples métricas normalizadas en un solo gráfico
   - **Valor:** Proporciona una vista holística del rendimiento general de la ONG
   - **Tipo:** Radar Chart
   - **Datos:** Normalización de métricas (0-100) de engagement, participación, alcance, conversión

### Dashboard Individual de Evento

#### **Tarjetas de Métricas:**

1. **Total Reacciones**
   - **Propósito:** Contar reacciones específicas del evento
   - **Valor:** Mide el interés inmediato de la audiencia
   - **Fuente:** `SELECT COUNT(*) FROM evento_reacciones WHERE evento_id = ?`

2. **Total Compartidos**
   - **Propósito:** Contar compartidos del evento
   - **Valor:** Indica el alcance orgánico del evento
   - **Fuente:** `SELECT COUNT(*) FROM evento_compartidos WHERE evento_id = ?`

3. **Total Voluntarios**
   - **Propósito:** Contar voluntarios únicos inscritos
   - **Valor:** Muestra el nivel de compromiso de voluntarios
   - **Fuente:** `SELECT COUNT(DISTINCT externo_id) FROM evento_participaciones WHERE evento_id = ?`

4. **Total Participantes**
   - **Propósito:** Contar todos los participantes (registrados + no registrados)
   - **Valor:** Indica el tamaño total de la audiencia del evento
   - **Fuente:** Suma de `evento_participaciones` + `evento_participantes_no_registrados`

#### **Gráficos Específicos del Evento:**

1. **Reacciones por Día (Line Chart)**
   - **Propósito:** Ver la evolución diaria de reacciones
   - **Valor:** Identifica días de mayor engagement

2. **Participantes por Estado (Doughnut Chart)**
   - **Propósito:** Distribución de participantes (aprobados, pendientes, rechazados)
   - **Valor:** Muestra el estado del proceso de selección

3. **Compartidos por Día (Bar Chart)**
   - **Propósito:** Visualizar días con mayor viralidad
   - **Valor:** Identifica momentos de mayor alcance

4. **Inscripciones por Día (Line Chart)**
   - **Propósito:** Ver la curva de inscripciones a lo largo del tiempo
   - **Valor:** Ayuda a entender el momento óptimo de promoción

5. **Comparativa de Métricas (Bar Chart)**
   - **Propósito:** Comparar período actual vs período anterior
   - **Valor:** Evalúa el crecimiento o decrecimiento del evento

6. **Actividad Semanal (Area Chart)**
   - **Propósito:** Ver actividad agregada por día de la semana
   - **Valor:** Identifica días de la semana con mayor actividad

7. **Tendencias Temporales (Line Chart)**
   - **Propósito:** Visualizar múltiples métricas en el tiempo
   - **Valor:** Permite correlacionar diferentes métricas

8. **Métricas Generales (Radar Chart)**
   - **Propósito:** Vista 360° del rendimiento del evento
   - **Valor:** Identifica fortalezas y debilidades del evento

---

## 🔍 3. EXPLICACIÓN DE LAS CONSULTAS O FUENTES DE DATOS

### Estructura de Base de Datos

Las consultas utilizan las siguientes tablas principales:

1. **`eventos`** - Almacena información de eventos regulares
   - Campos clave: `id`, `ong_id`, `titulo`, `fecha_inicio`, `fecha_fin`, `estado`, `created_at`

2. **`mega_eventos`** - Almacena información de mega eventos
   - Campos clave: `mega_evento_id`, `ong_organizadora_principal`, `titulo`, `fecha_inicio`, `fecha_fin`, `estado`

3. **`evento_reacciones`** - Registra cada reacción (like) a un evento
   - Campos clave: `id`, `evento_id`, `externo_id`, `created_at`

4. **`evento_compartidos`** - Registra cada compartido de un evento
   - Campos clave: `id`, `evento_id`, `externo_id`, `metodo`, `created_at`

5. **`evento_participaciones`** - Registra participantes registrados
   - Campos clave: `id`, `evento_id`, `externo_id`, `estado`, `created_at`

6. **`evento_participantes_no_registrados`** - Participantes sin cuenta
   - Campos clave: `id`, `evento_id`, `nombres`, `apellidos`, `estado`, `created_at`

7. **`integrantes_externos`** - Información de voluntarios
   - Campos clave: `user_id`, `nombres`, `apellidos`, `email`

### Ejemplos de Consultas SQL

#### Consulta 1: Métricas Principales
```sql
-- Total de eventos activos
SELECT COUNT(*) 
FROM eventos 
WHERE ong_id = ? 
  AND fecha_inicio <= NOW() 
  AND fecha_fin >= NOW();

-- Total de reacciones
SELECT COUNT(*) 
FROM evento_reacciones er
INNER JOIN eventos e ON er.evento_id = e.id
WHERE e.ong_id = ? 
  AND er.created_at BETWEEN ? AND ?;
```

#### Consulta 2: Tendencias Mensuales
```sql
SELECT 
    DATE_TRUNC('month', ep.created_at) as mes,
    COUNT(DISTINCT ep.externo_id) as participantes_unicos,
    COUNT(*) as total_inscripciones
FROM evento_participaciones ep
INNER JOIN eventos e ON ep.evento_id = e.id
WHERE e.ong_id = ?
  AND ep.created_at BETWEEN ? AND ?
GROUP BY DATE_TRUNC('month', ep.created_at)
ORDER BY mes;
```

#### Consulta 3: Top 10 Voluntarios
```sql
SELECT 
    ie.user_id,
    ie.nombres,
    ie.apellidos,
    ie.email,
    COUNT(DISTINCT ep.evento_id) as eventos_participados,
    COUNT(ep.id) as total_participaciones
FROM evento_participaciones ep
INNER JOIN integrantes_externos ie ON ep.externo_id = ie.user_id
INNER JOIN eventos e ON ep.evento_id = e.id
WHERE e.ong_id = ?
GROUP BY ie.user_id, ie.nombres, ie.apellidos, ie.email
ORDER BY eventos_participados DESC, total_participaciones DESC
LIMIT 10;
```

#### Consulta 4: Distribución de Estados
```sql
SELECT 
    estado,
    COUNT(*) as total
FROM eventos
WHERE ong_id = ?
GROUP BY estado;
```

### Uso de Eloquent ORM

El código utiliza Eloquent para abstraer las consultas SQL:

```php
// Ejemplo: Obtener métricas principales
$totalEventosActivos = Evento::where('ong_id', $ongId)
    ->where('fecha_inicio', '<=', now())
    ->where('fecha_fin', '>=', now())
    ->count();

// Ejemplo: Tendencias mensuales con agregación
$tendencias = DB::table('evento_participaciones as ep')
    ->join('eventos as e', 'ep.evento_id', '=', 'e.id')
    ->where('e.ong_id', $ongId)
    ->whereBetween('ep.created_at', [$fechaInicio, $fechaFin])
    ->selectRaw("DATE_TRUNC('month', ep.created_at) as mes, COUNT(*) as total")
    ->groupBy(DB::raw("DATE_TRUNC('month', ep.created_at)"))
    ->orderBy('mes')
    ->get();
```

### Optimizaciones Implementadas

1. **Eager Loading:** Uso de `with()` para evitar N+1 queries
2. **Índices:** Consultas optimizadas con índices en `ong_id`, `evento_id`, `created_at`
3. **Cache:** Resultados cacheados por 15-30 minutos para reducir carga
4. **Agregaciones en BD:** Cálculos realizados en PostgreSQL, no en PHP
5. **Límites:** Uso de `limit()` para top N resultados

---

## 💻 4. CÓDIGO RELEVANTE A CAPTURAR

### Código 1: Método del Controlador - Obtención de Métricas
**Archivo:** `app/Http/Controllers/Ong/OngDashboardController.php`
**Líneas:** ~280-350

```php
private function obtenerMetricasPrincipales($eventos, $megaEventos, $fechaInicio, $fechaFin)
{
    $eventosIds = $eventos->pluck('id')->toArray();
    $megaEventosIds = $megaEventos->pluck('mega_evento_id')->toArray();
    
    // Total de reacciones
    $totalReacciones = DB::table('evento_reacciones')
        ->whereIn('evento_id', $eventosIds)
        ->whereBetween('created_at', [$fechaInicio, $fechaFin])
        ->count();
    
    // Total de compartidos
    $totalCompartidos = DB::table('evento_compartidos')
        ->whereIn('evento_id', $eventosIds)
        ->whereBetween('created_at', [$fechaInicio, $fechaFin])
        ->count();
    
    // Total de voluntarios únicos
    $totalVoluntarios = DB::table('evento_participaciones')
        ->whereIn('evento_id', $eventosIds)
        ->whereBetween('created_at', [$fechaInicio, $fechaFin])
        ->whereNotNull('externo_id')
        ->distinct('externo_id')
        ->count('externo_id');
    
    return [
        'eventos_activos' => $eventos->where('fecha_inicio', '<=', now())
            ->where('fecha_fin', '>=', now())
            ->count(),
        'eventos_inactivos' => $eventos->where('estado', 'borrador')
            ->orWhere('estado', 'inactivo')
            ->count(),
        'eventos_finalizados' => $eventos->where('fecha_fin', '<', now())
            ->orWhere('estado', 'finalizado')
            ->count(),
        'total_reacciones' => $totalReacciones,
        'total_compartidos' => $totalCompartidos,
        'total_voluntarios' => $totalVoluntarios
    ];
}
```

### Código 2: Función JavaScript - Creación de Gráficos
**Archivo:** `resources/views/ong/dashboard.blade.php`
**Líneas:** ~776-990

```javascript
function crearGraficas(datos) {
    // Gráfico de Tendencias Mensuales
    const ctxTendencias = document.getElementById('graficaTendenciasMensuales');
    if (ctxTendencias) {
        charts.tendenciasMensuales = new Chart(ctxTendencias, {
            type: 'line',
            data: {
                labels: datos.tendencias_mensuales.labels,
                datasets: [{
                    label: 'Participantes',
                    data: datos.tendencias_mensuales.participantes,
                    borderColor: '#00A36C',
                    backgroundColor: 'rgba(0, 163, 108, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Tendencias Mensuales de Participantes'
                    },
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        }
                    }
                }
            }
        });
    }
    
    // Gráfico de Distribución de Estados
    const ctxDistribucion = document.getElementById('graficaDistribucionEstados');
    if (ctxDistribucion) {
        charts.distribucionEstados = new Chart(ctxDistribucion, {
            type: 'doughnut',
            data: {
                labels: Object.keys(datos.distribucion_estados),
                datasets: [{
                    data: Object.values(datos.distribucion_estados),
                    backgroundColor: [
                        '#00A36C',
                        '#0C2B44',
                        '#dc3545',
                        '#ffc107',
                        '#17a2b8'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });
    }
}
```

### Código 3: Consulta de Tendencias Mensuales
**Archivo:** `app/Http/Controllers/Ong/OngDashboardController.php`
**Líneas:** ~360-420

```php
private function obtenerTendenciasMensuales($eventosIds, $megaEventosIds, $fechaInicio, $fechaFin)
{
    // Participantes de eventos regulares
    $participantesRegulares = DB::table('evento_participaciones')
        ->whereIn('evento_id', $eventosIds)
        ->whereBetween('created_at', [$fechaInicio, $fechaFin])
        ->selectRaw("DATE_TRUNC('month', created_at) as mes, COUNT(*) as total")
        ->groupBy(DB::raw("DATE_TRUNC('month', created_at)"))
        ->orderBy('mes')
        ->get()
        ->pluck('total', 'mes')
        ->toArray();
    
    // Participantes de mega eventos
    $participantesMega = DB::table('mega_evento_participantes_externos')
        ->whereIn('mega_evento_id', $megaEventosIds)
        ->whereBetween('fecha_registro', [$fechaInicio, $fechaFin])
        ->selectRaw("DATE_TRUNC('month', fecha_registro) as mes, COUNT(*) as total")
        ->groupBy(DB::raw("DATE_TRUNC('month', fecha_registro)"))
        ->orderBy('mes')
        ->get()
        ->pluck('total', 'mes')
        ->toArray();
    
    // Combinar y formatear
    $todosMeses = array_unique(array_merge(array_keys($participantesRegulares), array_keys($participantesMega)));
    sort($todosMeses);
    
    $labels = [];
    $datos = [];
    
    foreach ($todosMeses as $mes) {
        $labels[] = Carbon::parse($mes)->locale('es')->isoFormat('MMMM YYYY');
        $datos[] = ($participantesRegulares[$mes] ?? 0) + ($participantesMega[$mes] ?? 0);
    }
    
    return [
        'labels' => $labels,
        'participantes' => $datos
    ];
}
```

### Código 4: HTML de Tarjetas de Métricas
**Archivo:** `resources/views/ong/dashboard.blade.php`
**Líneas:** ~87-150

```html
<div class="col-lg-3 col-md-6 mb-3">
    <div class="card border-0 shadow-sm" style="border-radius: 8px; border-left: 4px solid #dc3545 !important;">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 id="totalEventosActivos" class="mb-0" style="font-size: 2.5rem; font-weight: 800; color: #0C2B44;">
                        0
                    </h3>
                    <p class="mb-0 mt-2" style="color: #0C2B44; font-size: 1rem; font-weight: 600;">
                        Eventos Activos
                    </p>
                </div>
                <div style="color: #dc3545; opacity: 0.2; font-size: 3rem;">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>
        </div>
    </div>
</div>
```

---

## 💡 5. JUSTIFICACIÓN DEL VALOR PARA EL CLIENTE

### Valor Estratégico

1. **Toma de Decisiones Basada en Datos**
   - El dashboard proporciona métricas en tiempo real que permiten a las ONGs tomar decisiones informadas sobre:
     - Qué eventos replicar o mejorar
     - Cuándo lanzar nuevos eventos
     - Cómo asignar recursos limitados
     - Dónde enfocar esfuerzos de marketing

2. **Identificación de Tendencias**
   - Los gráficos de tendencias mensuales y semanales permiten:
     - Identificar patrones estacionales
     - Predecir picos de participación
     - Planificar campañas futuras basadas en datos históricos

3. **Optimización de Recursos**
   - Al comparar el rendimiento entre eventos, las ONGs pueden:
     - Identificar eventos exitosos para replicar estrategias
     - Detectar eventos con bajo rendimiento para mejorarlos
     - Asignar presupuesto de marketing a eventos con mayor ROI

4. **Medición de Impacto**
   - Las métricas agregadas permiten:
     - Demostrar el impacto social a donantes e inversionistas
     - Generar reportes para stakeholders
     - Justificar financiamiento con datos concretos

5. **Gestión de Voluntarios**
   - El tracking de voluntarios permite:
     - Identificar voluntarios más activos para reconocimiento
     - Detectar necesidades de reclutamiento
     - Optimizar la asignación de voluntarios a eventos

6. **Mejora Continua**
   - Los insights automáticos y comparativas ayudan a:
     - Identificar áreas de mejora
     - Establecer benchmarks
     - Medir el progreso hacia objetivos

### Beneficios Específicos por Tipo de Usuario

**Para Directivos de ONG:**
- Visión ejecutiva del rendimiento general
- Métricas para presentaciones a junta directiva
- Datos para solicitudes de financiamiento

**Para Coordinadores de Eventos:**
- Detalles específicos de cada evento
- Identificación de problemas en tiempo real
- Métricas para ajustar estrategias durante el evento

**Para Equipo de Marketing:**
- Datos de engagement para optimizar campañas
- Identificación de canales más efectivos
- Métricas de alcance y viralidad

**Para Donantes/Inversionistas:**
- Transparencia en el uso de recursos
- Medición de impacto social
- ROI de inversiones en eventos

### ROI (Retorno de Inversión)

1. **Ahorro de Tiempo:** 
   - Antes: 2-3 horas manuales para generar reportes
   - Ahora: Reportes instantáneos con un clic
   - **Ahorro:** ~10 horas/mes por ONG

2. **Mejora en Toma de Decisiones:**
   - Decisiones basadas en datos vs intuición
   - Reducción de eventos fallidos
   - Aumento en tasa de éxito de eventos

3. **Transparencia y Credibilidad:**
   - Reportes profesionales para stakeholders
   - Mayor confianza de donantes
   - Mejor acceso a financiamiento

4. **Escalabilidad:**
   - Sistema soporta crecimiento sin aumentar costos
   - Automatización reduce necesidad de personal adicional

---

## 📋 CHECKLIST PARA COMPLETAR LA ACTIVIDAD

- [ ] Captura 1: Dashboard General de ONG (vista completa)
- [ ] Captura 2: Dashboard Individual de Evento (vista completa)
- [ ] Captura 3: Código del controlador (métodos principales)
- [ ] Captura 4: Código de la vista (HTML y JavaScript)
- [ ] Captura 5: Consultas SQL/Eloquent (métodos de obtención de datos)
- [ ] Documento con descripción de cada gráfico/indicador
- [ ] Explicación de fuentes de datos y consultas
- [ ] Justificación del valor para el cliente

---

## 📝 NOTAS ADICIONALES

- Todas las capturas deben ser claras y legibles
- Incluir anotaciones en las capturas si es necesario
- El código debe mostrar líneas relevantes (no todo el archivo)
- Las consultas deben mostrar la lógica de negocio, no solo SQL crudo
- La justificación debe ser específica y cuantificable cuando sea posible


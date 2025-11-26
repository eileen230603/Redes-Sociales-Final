# Parametrizaciones Recomendadas para Completar al 100%

Este documento lista las parametrizaciones adicionales recomendadas para que el sistema esté completo y profesional.

## 📊 RESUMEN

- **Parametrizaciones Actuales:** 8 catálogos + 1 sistema de parámetros
- **Parametrizaciones Recomendadas:** 12 catálogos adicionales
- **Total al 100%:** 20 catálogos + 1 sistema de parámetros

---

## 🎯 PARAMETRIZACIONES RECOMENDADAS (Prioridad ALTA)

### 1. 🔗 **TIPOS DE COLABORACIÓN** (`tipos_colaboracion`)

**Justificación:** Actualmente `tipo_colaboracion` es un campo de texto libre en `evento_empresas_participantes`. Debería ser un catálogo para estandarizar.

**Valores Sugeridos:**
1. **Recursos** (`recursos`)
   - Icono: `fas fa-box`
   - Color: `info`
   - Descripción: Provisión de recursos materiales o equipos

2. **Logística** (`logistica`)
   - Icono: `fas fa-truck`
   - Color: `warning`
   - Descripción: Apoyo en transporte, almacenamiento, distribución

3. **Financiera** (`financiera`)
   - Icono: `fas fa-dollar-sign`
   - Color: `success`
   - Descripción: Apoyo económico o patrocinio financiero

4. **Técnica** (`tecnica`)
   - Icono: `fas fa-laptop`
   - Color: `primary`
   - Descripción: Apoyo técnico, tecnología, sistemas

5. **Marketing/Comunicación** (`marketing`)
   - Icono: `fas fa-bullhorn`
   - Color: `purple`
   - Descripción: Apoyo en publicidad, marketing, comunicación

6. **Voluntariado** (`voluntariado`)
   - Icono: `fas fa-hands-helping`
   - Color: `danger`
   - Descripción: Apoyo mediante personal voluntario

7. **Patrocinador** (`patrocinador`)
   - Icono: `fas fa-handshake`
   - Color: `success`
   - Descripción: Patrocinio general del evento

**Campos:**
- `codigo` - Código único
- `nombre` - Nombre descriptivo
- `descripcion` - Descripción detallada
- `icono` - Icono FontAwesome
- `color` - Color del badge
- `orden` - Orden de visualización
- `activo` - Estado activo/inactivo

**Impacto:** Mejora la estandarización y reportes de colaboraciones.

---

### 2. 📍 **MODALIDADES DE EVENTO** (`modalidades_evento`)

**Justificación:** Los eventos pueden ser presenciales, virtuales o híbridos. Esto es importante para la organización.

**Valores Sugeridos:**
1. **Presencial** (`presencial`)
   - Icono: `fas fa-map-marker-alt`
   - Color: `primary`
   - Descripción: Evento realizado en un lugar físico

2. **Virtual** (`virtual`)
   - Icono: `fas fa-video`
   - Color: `info`
   - Descripción: Evento realizado completamente en línea

3. **Híbrido** (`hibrido`)
   - Icono: `fas fa-users`
   - Color: `success`
   - Descripción: Evento con participación presencial y virtual

**Campos:**
- `codigo` - Código único
- `nombre` - Nombre descriptivo
- `descripcion` - Descripción
- `icono` - Icono FontAwesome
- `color` - Color del badge
- `orden` - Orden de visualización
- `activo` - Estado activo/inactivo

**Impacto:** Permite filtrar y organizar eventos por modalidad.

---

### 3. 🎫 **FORMATOS DE EVENTO** (`formatos_evento`)

**Justificación:** Los eventos pueden tener diferentes formatos de ejecución.

**Valores Sugeridos:**
1. **Conferencia** (`conferencia`)
   - Icono: `fas fa-microphone-alt`
   - Descripción: Formato de conferencia o charla

2. **Taller Práctico** (`taller_practico`)
   - Icono: `fas fa-tools`
   - Descripción: Taller con actividades prácticas

3. **Seminario** (`seminario`)
   - Icono: `fas fa-chalkboard-teacher`
   - Descripción: Formato de seminario académico

4. **Mesa Redonda** (`mesa_redonda`)
   - Icono: `fas fa-comments`
   - Descripción: Mesa redonda o panel de discusión

5. **Networking** (`networking`)
   - Icono: `fas fa-network-wired`
   - Descripción: Evento de networking

6. **Exposición** (`exposicion`)
   - Icono: `fas fa-images`
   - Descripción: Exposición o muestra

7. **Festival** (`festival`)
   - Icono: `fas fa-music`
   - Descripción: Festival o celebración

**Campos:** Similar a tipos de evento

**Impacto:** Permite categorizar mejor los eventos por formato.

---

### 4. 🏷️ **ESTADOS DE EMPRESA COLABORADORA** (`estados_empresa_colaboradora`)

**Justificación:** Actualmente `estado` en `evento_empresas_participantes` tiene valores hardcodeados. Debería ser un catálogo.

**Valores Sugeridos:**
1. **Asignada** (`asignada`)
   - Color: `warning`
   - Icono: `fas fa-user-plus`
   - Descripción: Empresa asignada por la ONG, pendiente de confirmación

2. **Confirmada** (`confirmada`)
   - Color: `success`
   - Icono: `fas fa-check-circle`
   - Descripción: Empresa confirmó su participación

3. **Cancelada** (`cancelada`)
   - Color: `danger`
   - Icono: `fas fa-times-circle`
   - Descripción: Participación cancelada

4. **En Evaluación** (`en_evaluacion`)
   - Color: `info`
   - Icono: `fas fa-search`
   - Descripción: En proceso de evaluación

5. **Rechazada** (`rechazada`)
   - Color: `secondary`
   - Icono: `fas fa-ban`
   - Descripción: Participación rechazada

**Campos:** Similar a estados de participación

**Impacto:** Mejora el seguimiento del estado de las empresas colaboradoras.

---

### 5. 🌍 **IDIOMAS** (`idiomas`)

**Justificación:** Para eventos internacionales o multilingües.

**Valores Sugeridos:**
1. **Español** (`es`)
   - Código ISO: `es`
   - Nombre: Español
   - Activo: true

2. **Inglés** (`en`)
   - Código ISO: `en`
   - Nombre: English
   - Activo: true

3. **Quechua** (`qu`)
   - Código ISO: `qu`
   - Nombre: Quechua
   - Activo: true

4. **Aymara** (`ay`)
   - Código ISO: `ay`
   - Nombre: Aymara
   - Activo: true

**Campos:**
- `codigo` - Código ISO 639-1
- `nombre` - Nombre del idioma
- `nombre_nativo` - Nombre en el idioma nativo
- `activo` - Estado activo/inactivo

**Impacto:** Permite eventos multilingües y mejor internacionalización.

---

### 6. 💰 **MONEDAS** (`monedas`)

**Justificación:** Para eventos que manejen costos o presupuestos.

**Valores Sugeridos:**
1. **Boliviano** (`BOB`)
   - Símbolo: `Bs.`
   - Nombre: Boliviano
   - País: Bolivia

2. **Dólar Estadounidense** (`USD`)
   - Símbolo: `$`
   - Nombre: Dólar Estadounidense
   - País: Estados Unidos

3. **Euro** (`EUR`)
   - Símbolo: `€`
   - Nombre: Euro
   - País: Zona Euro

**Campos:**
- `codigo` - Código ISO 4217 (ej: BOB, USD)
- `nombre` - Nombre de la moneda
- `simbolo` - Símbolo (ej: Bs., $, €)
- `pais` - País o región
- `activo` - Estado activo/inactivo

**Impacto:** Permite manejar presupuestos y costos en diferentes monedas.

---

## 🎯 PARAMETRIZACIONES RECOMENDADAS (Prioridad MEDIA)

### 7. 👥 **RANGOS DE EDAD** (`rangos_edad`)

**Justificación:** Para eventos dirigidos a grupos de edad específicos.

**Valores Sugeridos:**
1. **Infantil** (`infantil`) - 0-12 años
2. **Adolescente** (`adolescente`) - 13-17 años
3. **Joven** (`joven`) - 18-25 años
4. **Adulto Joven** (`adulto_joven`) - 26-35 años
5. **Adulto** (`adulto`) - 36-50 años
6. **Adulto Mayor** (`adulto_mayor`) - 51-65 años
7. **Tercera Edad** (`tercera_edad`) - 66+ años
8. **Todos** (`todos`) - Sin restricción de edad

**Campos:**
- `codigo` - Código único
- `nombre` - Nombre del rango
- `edad_minima` - Edad mínima
- `edad_maxima` - Edad máxima (null para sin límite)
- `descripcion` - Descripción
- `activo` - Estado activo/inactivo

**Impacto:** Permite filtrar eventos por público objetivo.

---

### 8. 🎓 **NIVELES EDUCATIVOS** (`niveles_educativos`)

**Justificación:** Para eventos que requieren cierto nivel educativo.

**Valores Sugeridos:**
1. **Sin Requisito** (`sin_requisito`)
2. **Primaria** (`primaria`)
3. **Secundaria** (`secundaria`)
4. **Técnico** (`tecnico`)
5. **Universitario** (`universitario`)
6. **Postgrado** (`postgrado`)

**Campos:** Similar a otros catálogos

**Impacto:** Permite especificar requisitos educativos para eventos.

---

### 9. ⏰ **FRANJAS HORARIAS** (`franjas_horarias`)

**Justificación:** Para organizar eventos por horarios del día.

**Valores Sugeridos:**
1. **Madrugada** (`madrugada`) - 00:00 - 05:59
2. **Mañana** (`manana`) - 06:00 - 11:59
3. **Mediodía** (`mediodia`) - 12:00 - 13:59
4. **Tarde** (`tarde`) - 14:00 - 17:59
5. **Noche** (`noche`) - 18:00 - 23:59

**Campos:**
- `codigo` - Código único
- `nombre` - Nombre de la franja
- `hora_inicio` - Hora de inicio (formato HH:mm)
- `hora_fin` - Hora de fin (formato HH:mm)
- `activo` - Estado activo/inactivo

**Impacto:** Permite filtrar eventos por horario del día.

---

### 10. 🏆 **NIVELES DE PRIORIDAD** (`niveles_prioridad`)

**Justificación:** Para clasificar eventos, notificaciones o tareas por prioridad.

**Valores Sugeridos:**
1. **Baja** (`baja`)
   - Color: `secondary`
   - Icono: `fas fa-arrow-down`

2. **Normal** (`normal`)
   - Color: `info`
   - Icono: `fas fa-minus`

3. **Alta** (`alta`)
   - Color: `warning`
   - Icono: `fas fa-arrow-up`

4. **Urgente** (`urgente`)
   - Color: `danger`
   - Icono: `fas fa-exclamation-triangle`

**Campos:** Similar a otros catálogos con color e icono

**Impacto:** Permite priorizar eventos y tareas.

---

### 11. 📊 **TIPOS DE RECURSOS** (`tipos_recursos`)

**Justificación:** Para categorizar los recursos que las empresas pueden aportar.

**Valores Sugeridos:**
1. **Equipos** (`equipos`)
   - Icono: `fas fa-laptop`
   - Descripción: Equipos tecnológicos o de oficina

2. **Materiales** (`materiales`)
   - Icono: `fas fa-box`
   - Descripción: Materiales de consumo o construcción

3. **Espacios** (`espacios`)
   - Icono: `fas fa-building`
   - Descripción: Espacios físicos para eventos

4. **Transporte** (`transporte`)
   - Icono: `fas fa-bus`
   - Descripción: Servicios de transporte

5. **Alimentación** (`alimentacion`)
   - Icono: `fas fa-utensils`
   - Descripción: Servicios de catering o alimentación

6. **Tecnología** (`tecnologia`)
   - Icono: `fas fa-server`
   - Descripción: Servicios tecnológicos o de TI

7. **Personal** (`personal`)
   - Icono: `fas fa-users`
   - Descripción: Personal o voluntarios

**Campos:** Similar a tipos de colaboración

**Impacto:** Permite categorizar mejor los recursos disponibles.

---

### 12. 📱 **CANALES DE COMUNICACIÓN** (`canales_comunicacion`)

**Justificación:** Para notificaciones y comunicaciones del sistema.

**Valores Sugeridos:**
1. **Email** (`email`)
   - Icono: `fas fa-envelope`
   - Color: `primary`

2. **SMS** (`sms`)
   - Icono: `fas fa-sms`
   - Color: `info`

3. **Push Notification** (`push`)
   - Icono: `fas fa-bell`
   - Color: `warning`

4. **WhatsApp** (`whatsapp`)
   - Icono: `fab fa-whatsapp`
   - Color: `success`

5. **Sistema** (`sistema`)
   - Icono: `fas fa-bell`
   - Color: `secondary`

**Campos:** Similar a otros catálogos

**Impacto:** Permite configurar preferencias de notificación por canal.

---

## 🎯 PARAMETRIZACIONES RECOMENDADAS (Prioridad BAJA - Opcionales)

### 13. 🌐 **PAÍSES** (`paises`)

**Justificación:** Para eventos internacionales o expandir más allá de Bolivia.

**Valores:** Lista completa de países con código ISO 3166-1

**Campos:**
- `codigo` - Código ISO 3166-1 alpha-2 (ej: BO, US, ES)
- `nombre` - Nombre del país
- `codigo_telefono` - Código telefónico internacional
- `activo` - Estado activo/inactivo

---

### 14. 🏢 **SECTORES EMPRESARIALES** (`sectores_empresariales`)

**Justificación:** Para categorizar empresas por sector.

**Valores Sugeridos:**
1. **Tecnología** (`tecnologia`)
2. **Salud** (`salud`)
3. **Educación** (`educacion`)
4. **Alimentación** (`alimentacion`)
5. **Retail** (`retail`)
6. **Servicios** (`servicios`)
7. **Manufactura** (`manufactura`)
8. **Construcción** (`construccion`)
9. **Turismo** (`turismo`)
10. **Otro** (`otro`)

---

### 15. 📅 **FRECUENCIAS DE EVENTO** (`frecuencias_evento`)

**Justificación:** Para eventos recurrentes.

**Valores Sugeridos:**
1. **Único** (`unico`)
2. **Diario** (`diario`)
3. **Semanal** (`semanal`)
4. **Quincenal** (`quincenal`)
5. **Mensual** (`mensual`)
6. **Trimestral** (`trimestral`)
7. **Semestral** (`semestral`)
8. **Anual** (`anual`)

---

### 16. 🎯 **OBJETIVOS DE EVENTO** (`objetivos_evento`)

**Justificación:** Para clasificar eventos por su objetivo principal.

**Valores Sugeridos:**
1. **Educativo** (`educativo`)
2. **Social** (`social`)
3. **Recaudación** (`recaudacion`)
4. **Concienciación** (`concienciacion`)
5. **Networking** (`networking`)
6. **Celebración** (`celebracion`)
7. **Voluntariado** (`voluntariado`)

---

### 17. 📋 **TIPOS DE DOCUMENTO** (`tipos_documento`)

**Justificación:** Para documentos relacionados con eventos o usuarios.

**Valores Sugeridos:**
1. **Cédula de Identidad** (`ci`)
2. **Pasaporte** (`pasaporte`)
3. **Licencia de Conducir** (`licencia`)
4. **NIT** (`nit`)
5. **Otro** (`otro`)

---

### 18. 🔐 **NIVELES DE ACCESO** (`niveles_acceso`)

**Justificación:** Para controlar acceso a información o funcionalidades.

**Valores Sugeridos:**
1. **Público** (`publico`)
2. **Registrado** (`registrado`)
3. **Verificado** (`verificado`)
4. **Premium** (`premium`)
5. **VIP** (`vip`)

---

### 19. 📊 **MÉTRICAS DE IMPACTO** (`metricas_impacto`)

**Justificación:** Para medir el impacto de los eventos.

**Valores Sugeridos:**
1. **Participantes** (`participantes`)
2. **Recaudación** (`recaudacion`)
3. **Alcance** (`alcance`)
4. **Engagement** (`engagement`)
5. **Satisfacción** (`satisfaccion`)

---

### 20. 🏅 **TIPOS DE RECONOCIMIENTO** (`tipos_reconocimiento`)

**Justificación:** Para reconocer a participantes, empresas o voluntarios.

**Valores Sugeridos:**
1. **Certificado** (`certificado`)
2. **Medalla** (`medalla`)
3. **Trofeo** (`trofeo`)
4. **Diploma** (`diploma`)
5. **Reconocimiento Especial** (`reconocimiento_especial`)

---

## 📊 RESUMEN DE PRIORIDADES

### 🔴 ALTA PRIORIDAD (Implementar Primero)
1. ✅ Tipos de Colaboración
2. ✅ Modalidades de Evento
3. ✅ Formatos de Evento
4. ✅ Estados de Empresa Colaboradora
5. ✅ Idiomas
6. ✅ Monedas

**Total:** 6 catálogos

### 🟡 MEDIA PRIORIDAD (Implementar Después)
7. ✅ Rangos de Edad
8. ✅ Niveles Educativos
9. ✅ Franjas Horarias
10. ✅ Niveles de Prioridad
11. ✅ Tipos de Recursos
12. ✅ Canales de Comunicación

**Total:** 6 catálogos

### 🟢 BAJA PRIORIDAD (Opcional)
13. Países
14. Sectores Empresariales
15. Frecuencias de Evento
16. Objetivos de Evento
17. Tipos de Documento
18. Niveles de Acceso
19. Métricas de Impacto
20. Tipos de Reconocimiento

**Total:** 8 catálogos opcionales

---

## 🎯 PLAN DE IMPLEMENTACIÓN RECOMENDADO

### Fase 1: Prioridad ALTA (6 catálogos)
- Implementar los 6 catálogos de alta prioridad
- Actualizar `evento_empresas_participantes` para usar `tipo_colaboracion_id`
- Agregar campo `modalidad_id` a eventos
- Agregar campo `formato_id` a eventos

### Fase 2: Prioridad MEDIA (6 catálogos)
- Implementar los 6 catálogos de media prioridad
- Integrar con formularios y filtros existentes

### Fase 3: Prioridad BAJA (8 catálogos - Opcional)
- Implementar según necesidades específicas del proyecto

---

## ✅ BENEFICIOS AL IMPLEMENTAR

1. **Estandarización:** Valores consistentes en todo el sistema
2. **Reportes Mejores:** Filtros y agrupaciones más precisas
3. **Escalabilidad:** Fácil agregar nuevos valores sin cambiar código
4. **Mantenibilidad:** Cambios centralizados en catálogos
5. **UX Mejorada:** Selects y filtros más claros para usuarios
6. **Internacionalización:** Soporte para múltiples idiomas y monedas
7. **Flexibilidad:** Sistema adaptable a diferentes necesidades

---

## 📝 NOTAS IMPORTANTES

1. **Migración de Datos:** Al implementar catálogos para campos existentes (como `tipo_colaboracion`), se debe crear un script de migración de datos.

2. **Validaciones:** Todos los nuevos catálogos deben tener validaciones en los endpoints.

3. **Seeders:** Crear seeders con valores por defecto para cada catálogo.

4. **Relaciones:** Actualizar modelos para usar relaciones con los nuevos catálogos.

5. **Frontend:** Actualizar formularios para usar selects de los catálogos en lugar de campos de texto libre.



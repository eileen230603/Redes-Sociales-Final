# Historias de Usuario
## Sistema de Gestión de Eventos Sociales

---

## Índice

1. [Autenticación y Registro](#1-autenticación-y-registro)
2. [Gestión de Perfil](#2-gestión-de-perfil)
3. [Gestión de Eventos (ONG)](#3-gestión-de-eventos-ong)
4. [Participación en Eventos (Externos/Voluntarios)](#4-participación-en-eventos-externosvoluntarios)
5. [Reacciones y Favoritos](#5-reacciones-y-favoritos)
6. [Notificaciones](#6-notificaciones)
7. [Dashboard y Estadísticas (ONG)](#7-dashboard-y-estadísticas-ong)
8. [Gestión de Voluntarios (ONG)](#8-gestión-de-voluntarios-ong)
9. [Mega Eventos](#9-mega-eventos)
10. [Configuración y Parametrización](#10-configuración-y-parametrización)
11. [Gestión de Empresas](#11-gestión-de-empresas)

---

## 1. Autenticación y Registro

### HU-001: Registro de Usuario ONG
**Como** administrador de una ONG  
**Quiero** registrarme en el sistema  
**Para** poder crear y gestionar eventos sociales

**Criterios de Aceptación:**
- Debo poder proporcionar: nombre de usuario, correo electrónico, contraseña, nombre de la ONG
- El sistema debe validar que el correo y nombre de usuario sean únicos
- Debo poder proporcionar información adicional: NIT, teléfono, dirección, sitio web, descripción
- Al registrarme, debo recibir un token de autenticación
- El sistema debe crear automáticamente el registro en la tabla de ONGs

**Prioridad:** Alta

---

### HU-002: Registro de Usuario Empresa
**Como** representante de una empresa  
**Quiero** registrarme en el sistema  
**Para** poder patrocinar eventos y participar en mega eventos

**Criterios de Aceptación:**
- Debo poder proporcionar: nombre de usuario, correo electrónico, contraseña, nombre de la empresa
- El sistema debe validar que el correo y nombre de usuario sean únicos
- Debo poder proporcionar información adicional: NIT, teléfono, dirección, sitio web, descripción
- Al registrarme, debo recibir un token de autenticación
- El sistema debe crear automáticamente el registro en la tabla de Empresas

**Prioridad:** Alta

---

### HU-003: Registro de Usuario Externo/Voluntario
**Como** persona interesada en participar en eventos  
**Quiero** registrarme en el sistema  
**Para** poder inscribirme en eventos y mega eventos

**Criterios de Aceptación:**
- Debo poder proporcionar: nombre de usuario, correo electrónico, contraseña, nombres, apellidos
- El sistema debe validar que el correo y nombre de usuario sean únicos
- Debo poder proporcionar información adicional: fecha de nacimiento, teléfono, descripción
- Al registrarme, debo recibir un token de autenticación
- El sistema debe crear automáticamente el registro en la tabla de Integrantes Externos

**Prioridad:** Alta

---

### HU-004: Inicio de Sesión
**Como** usuario registrado  
**Quiero** iniciar sesión en el sistema  
**Para** acceder a las funcionalidades según mi tipo de usuario

**Criterios de Aceptación:**
- Debo poder iniciar sesión con correo electrónico y contraseña
- El sistema debe validar mis credenciales
- Si mis credenciales son correctas, debo recibir un token JWT
- El token debe incluir información de mi usuario: id_usuario, nombre_usuario, tipo_usuario, id_entidad
- Si mis credenciales son incorrectas, debo recibir un mensaje de error claro
- Si mi cuenta está inactiva, no debo poder iniciar sesión

**Prioridad:** Alta

---

### HU-005: Cerrar Sesión
**Como** usuario autenticado  
**Quiero** cerrar sesión  
**Para** proteger mi cuenta cuando termine de usar el sistema

**Criterios de Aceptación:**
- Debo poder cerrar sesión en cualquier momento
- Al cerrar sesión, el token actual debe ser invalidado
- Debo recibir confirmación de que la sesión se cerró correctamente

**Prioridad:** Media

---

## 2. Gestión de Perfil

### HU-006: Ver Mi Perfil
**Como** usuario autenticado  
**Quiero** ver mi información de perfil completa  
**Para** verificar y revisar mis datos personales o de mi organización

**Criterios de Aceptación:**
- Debo poder ver toda mi información de usuario base: nombre, correo, tipo de usuario, fecha de registro
- Si soy ONG, debo ver información específica: nombre ONG, NIT, teléfono, dirección, sitio web, descripción, foto de perfil
- Si soy Empresa, debo ver información específica: nombre empresa, NIT, teléfono, dirección, sitio web, descripción, foto de perfil
- Si soy Externo, debo ver información específica: nombres, apellidos, fecha de nacimiento, email, teléfono, descripción, foto de perfil
- La información debe mostrarse de forma clara y organizada

**Prioridad:** Media

---

### HU-007: Actualizar Mi Perfil
**Como** usuario autenticado  
**Quiero** actualizar mi información de perfil  
**Para** mantener mis datos actualizados

**Criterios de Aceptación:**
- Debo poder actualizar mi nombre de usuario (si no está en uso)
- Debo poder actualizar mi correo electrónico (si no está en uso)
- Debo poder cambiar mi contraseña proporcionando la contraseña actual
- Debo poder actualizar la información específica de mi tipo de usuario
- Debo poder subir o actualizar mi foto de perfil (archivo o URL)
- El sistema debe validar todos los campos antes de guardar
- Debo recibir confirmación cuando los cambios se guarden correctamente

**Prioridad:** Media

---

## 3. Gestión de Eventos (ONG)

### HU-008: Crear Evento
**Como** ONG autenticada  
**Quiero** crear un nuevo evento  
**Para** promocionar y organizar actividades sociales

**Criterios de Aceptación:**
- Debo poder proporcionar: título, descripción, tipo de evento, fecha de inicio, fecha de fin
- Debo poder establecer: fecha límite de inscripción, capacidad máxima, estado (borrador/publicado/finalizado/cancelado)
- Debo poder especificar la ubicación: ciudad, dirección, coordenadas (lat/lng)
- Debo poder agregar al menos una imagen promocional (archivo o URL)
- Debo poder seleccionar patrocinadores de la lista de empresas disponibles
- Debo poder seleccionar invitados de la lista de externos disponibles
- Debo poder habilitar/deshabilitar inscripciones
- El sistema debe validar que la fecha de inicio sea futura
- El sistema debe validar que la fecha de fin sea posterior a la fecha de inicio
- El sistema debe validar que haya al menos una imagen
- Al crear el evento, debo recibir confirmación con los datos del evento creado

**Prioridad:** Alta

---

### HU-009: Listar Mis Eventos
**Como** ONG autenticada  
**Quiero** ver todos mis eventos  
**Para** gestionarlos y hacer seguimiento

**Criterios de Aceptación:**
- Debo poder ver todos los eventos que he creado
- Debo poder filtrar por tipo de evento
- Debo poder filtrar por estado (borrador, activo, próximo, finalizado, cancelado)
- Debo poder buscar por título o descripción
- Los eventos deben mostrarse con: título, imágenes, fechas, estado dinámico, patrocinadores, invitados
- El estado dinámico debe calcularse automáticamente según las fechas (activo, próximo, finalizado)
- Los patrocinadores e invitados deben mostrarse con nombre y avatar
- Debo ver el conteo total de eventos

**Prioridad:** Alta

---

### HU-010: Ver Detalle de Evento
**Como** ONG autenticada  
**Quiero** ver los detalles completos de un evento  
**Para** revisar y editar la información

**Criterios de Aceptación:**
- Debo poder ver toda la información del evento: título, descripción, fechas, ubicación, imágenes, patrocinadores, invitados
- Debo ver el estado actual del evento (guardado y dinámico)
- Debo ver la capacidad máxima y el número de inscritos
- Debo ver si las inscripciones están abiertas o cerradas
- La información debe mostrarse de forma clara y organizada

**Prioridad:** Media

---

### HU-011: Editar Evento
**Como** ONG autenticada  
**Quiero** editar un evento existente  
**Para** actualizar la información cuando sea necesario

**Criterios de Aceptación:**
- Debo poder modificar: título, descripción, tipo, fechas, ubicación, estado
- Debo poder agregar, eliminar o reemplazar imágenes
- Debo poder agregar o quitar patrocinadores
- Debo poder agregar o quitar invitados
- Debo poder modificar la capacidad máxima y el estado de inscripciones
- El sistema debe validar todos los campos antes de guardar
- Debo recibir confirmación cuando los cambios se guarden correctamente

**Prioridad:** Alta

---

### HU-012: Eliminar Evento
**Como** ONG autenticada  
**Quiero** eliminar un evento  
**Para** remover eventos que ya no son necesarios

**Criterios de Aceptación:**
- Debo poder eliminar eventos que he creado
- Al eliminar, debo recibir confirmación
- El sistema debe eliminar también las imágenes asociadas del storage

**Prioridad:** Media

---

### HU-013: Dashboard de Eventos por Estado
**Como** ONG autenticada  
**Quiero** ver un dashboard con mis eventos organizados por estado  
**Para** tener una vista general del estado de todos mis eventos

**Criterios de Aceptación:**
- Debo poder ver estadísticas: total, finalizados, activos, próximos, cancelados, borradores
- Debo poder filtrar eventos por estado
- Debo poder buscar eventos por título o descripción
- Los eventos deben mostrarse con toda su información enriquecida
- Debo ver el conteo de eventos según el filtro aplicado

**Prioridad:** Media

---

### HU-014: Agregar Patrocinador a Evento
**Como** ONG autenticada  
**Quiero** agregar una empresa como patrocinador de un evento  
**Para** reconocer su apoyo y colaboración

**Criterios de Aceptación:**
- Debo poder ver la lista de empresas disponibles para patrocinar
- Debo poder agregar una empresa como patrocinador de un evento específico
- El sistema debe validar que la empresa no sea ya patrocinadora
- Al agregar, debo recibir confirmación
- La empresa debe aparecer en la lista de patrocinadores del evento

**Prioridad:** Baja

---

### HU-015: Ver Empresas Disponibles para Patrocinar
**Como** ONG autenticada  
**Quiero** ver la lista de empresas disponibles  
**Para** seleccionarlas como patrocinadoras de mis eventos

**Criterios de Aceptación:**
- Debo poder ver todas las empresas registradas en el sistema
- Cada empresa debe mostrar: ID, nombre, descripción
- La lista debe estar disponible cuando creo o edito un evento

**Prioridad:** Baja

---

### HU-016: Ver Invitados Disponibles
**Como** ONG autenticada  
**Quiero** ver la lista de usuarios externos disponibles  
**Para** seleccionarlos como invitados especiales de mis eventos

**Criterios de Aceptación:**
- Debo poder ver todos los usuarios externos registrados en el sistema
- Cada invitado debe mostrar: ID, nombre completo, email
- La lista debe estar disponible cuando creo o edito un evento

**Prioridad:** Baja

---

## 4. Participación en Eventos (Externos/Voluntarios)

### HU-017: Ver Eventos Disponibles
**Como** usuario externo o voluntario autenticado  
**Quiero** ver todos los eventos publicados  
**Para** encontrar eventos en los que pueda participar

**Criterios de Aceptación:**
- Debo poder ver todos los eventos con estado "publicado"
- Debo poder filtrar por tipo de evento
- Debo poder buscar eventos por título o descripción
- Los eventos deben mostrarse ordenados por fecha de inicio
- Cada evento debe mostrar: título, descripción, imágenes, fechas, ubicación, patrocinadores, invitados
- Debo ver el estado dinámico del evento (activo, próximo, finalizado)
- Debo ver si las inscripciones están abiertas

**Prioridad:** Alta

---

### HU-018: Ver Detalle de Evento Público
**Como** usuario externo o voluntario autenticado  
**Quiero** ver los detalles completos de un evento publicado  
**Para** decidir si quiero participar

**Criterios de Aceptación:**
- Debo poder ver toda la información del evento: título, descripción, fechas, ubicación, imágenes
- Debo ver los patrocinadores con nombre y avatar
- Debo ver los invitados especiales con nombre y avatar
- Debo ver la capacidad máxima y el número de inscritos actuales
- Debo ver si las inscripciones están abiertas
- Debo ver el estado del evento (activo, próximo, finalizado)

**Prioridad:** Alta

---

### HU-019: Inscribirme en un Evento
**Como** usuario externo o voluntario autenticado  
**Quiero** inscribirme en un evento  
**Para** participar en la actividad

**Criterios de Aceptación:**
- Debo poder inscribirme en eventos con inscripciones abiertas
- El sistema debe validar que el evento existe y está publicado
- El sistema debe validar que las inscripciones estén abiertas
- El sistema debe validar que no esté agotado el cupo (si hay capacidad máxima)
- El sistema debe validar que no esté ya inscrito
- Al inscribirme, debo ser aprobado automáticamente
- Debo recibir confirmación de mi inscripción
- La ONG debe recibir una notificación de mi inscripción

**Prioridad:** Alta

---

### HU-020: Cancelar Mi Inscripción
**Como** usuario externo o voluntario autenticado  
**Quiero** cancelar mi inscripción a un evento  
**Para** liberar mi cupo si no puedo asistir

**Criterios de Aceptación:**
- Debo poder cancelar mi inscripción en cualquier momento
- El sistema debe validar que estoy inscrito en el evento
- Al cancelar, debo recibir confirmación
- Mi participación debe ser eliminada del sistema

**Prioridad:** Media

---

### HU-021: Ver Mis Eventos Inscritos
**Como** usuario externo o voluntario autenticado  
**Quiero** ver todos los eventos en los que estoy inscrito  
**Para** hacer seguimiento de mis participaciones

**Criterios de Aceptación:**
- Debo poder ver todos los eventos en los que me he inscrito
- Cada evento debe mostrar: título, fechas, estado de mi participación, si asistí, puntos obtenidos
- Los eventos deben estar ordenados por fecha de inscripción (más recientes primero)
- Debo ver el estado de mi participación (aprobada, pendiente, rechazada)

**Prioridad:** Media

---

### HU-022: Ver Participantes de un Evento (ONG)
**Como** ONG autenticada  
**Quiero** ver la lista de participantes de mis eventos  
**Para** gestionar las inscripciones y hacer seguimiento

**Criterios de Aceptación:**
- Debo poder ver todos los participantes de un evento específico
- Cada participante debe mostrar: nombre completo, correo, teléfono, fecha de inscripción, estado, si asistió, puntos
- Debo ver la foto de perfil de cada participante
- La lista debe estar ordenada por fecha de inscripción
- Debo ver el conteo total de participantes

**Prioridad:** Alta

---

### HU-023: Aprobar Participación
**Como** ONG autenticada  
**Quiero** aprobar la participación de un usuario en mi evento  
**Para** confirmar su asistencia

**Criterios de Aceptación:**
- Debo poder aprobar participaciones pendientes
- El sistema debe validar que soy la ONG propietaria del evento
- Al aprobar, el estado debe cambiar a "aprobada"
- Debo recibir confirmación de la acción

**Prioridad:** Media

---

### HU-024: Rechazar Participación
**Como** ONG autenticada  
**Quiero** rechazar la participación de un usuario en mi evento  
**Para** gestionar el cupo del evento

**Criterios de Aceptación:**
- Debo poder rechazar participaciones pendientes
- El sistema debe validar que soy la ONG propietaria del evento
- Al rechazar, el estado debe cambiar a "rechazada"
- Debo recibir confirmación de la acción

**Prioridad:** Media

---

## 5. Reacciones y Favoritos

### HU-025: Reaccionar a un Evento
**Como** usuario externo o voluntario autenticado  
**Quiero** reaccionar (dar like/favorito) a un evento  
**Para** guardarlo en mis favoritos y mostrar interés

**Criterios de Aceptación:**
- Debo poder agregar una reacción (corazón/favorito) a un evento publicado
- El sistema debe validar que el evento existe
- Si ya reaccioné, al hacer clic debo quitar la reacción (toggle)
- Debo ver el total de reacciones del evento
- Debo recibir confirmación de mi acción
- La ONG debe recibir una notificación cuando reacciono

**Prioridad:** Media

---

### HU-026: Verificar si Reaccioné a un Evento
**Como** usuario externo o voluntario autenticado  
**Quiero** verificar si ya reaccioné a un evento  
**Para** saber si debo agregar o quitar la reacción

**Criterios de Aceptación:**
- Debo poder verificar mi estado de reacción en un evento específico
- El sistema debe indicar si reaccioné o no
- Debo ver el total de reacciones del evento

**Prioridad:** Baja

---

### HU-027: Ver Usuarios que Reaccionaron (ONG)
**Como** ONG autenticada  
**Quiero** ver la lista de usuarios que reaccionaron a mis eventos  
**Para** conocer el interés generado

**Criterios de Aceptación:**
- Debo poder ver todos los usuarios que reaccionaron a un evento específico
- Cada usuario debe mostrar: nombre completo, correo, fecha de reacción, foto de perfil
- La lista debe estar ordenada por fecha de reacción (más recientes primero)
- Debo ver el conteo total de reacciones
- Solo debo poder ver esta información de mis propios eventos

**Prioridad:** Baja

---

## 6. Notificaciones

### HU-028: Ver Mis Notificaciones
**Como** ONG autenticada  
**Quiero** ver todas mis notificaciones  
**Para** estar al tanto de las actividades en mis eventos

**Criterios de Aceptación:**
- Debo poder ver todas mis notificaciones ordenadas por fecha (más recientes primero)
- Cada notificación debe mostrar: tipo, título, mensaje, fecha, si está leída
- Debo ver información del evento relacionado (si aplica)
- Debo ver información del usuario que generó la notificación (si aplica)
- Debo ver el conteo de notificaciones no leídas

**Prioridad:** Alta

---

### HU-029: Ver Contador de Notificaciones No Leídas
**Como** ONG autenticada  
**Quiero** ver el número de notificaciones no leídas  
**Para** saber si tengo nuevas notificaciones sin revisar

**Criterios de Aceptación:**
- Debo poder obtener solo el contador de notificaciones no leídas
- El contador debe actualizarse en tiempo real
- Debe ser eficiente para consultas frecuentes

**Prioridad:** Media

---

### HU-030: Marcar Notificación como Leída
**Como** ONG autenticada  
**Quiero** marcar una notificación como leída  
**Para** organizar mis notificaciones y actualizar el contador

**Criterios de Aceptación:**
- Debo poder marcar una notificación específica como leída
- Al marcar, el estado debe cambiar a "leída"
- El contador de no leídas debe actualizarse
- Debo recibir confirmación de la acción

**Prioridad:** Media

---

### HU-031: Marcar Todas las Notificaciones como Leídas
**Como** ONG autenticada  
**Quiero** marcar todas mis notificaciones como leídas  
**Para** limpiar mi bandeja de notificaciones de una vez

**Criterios de Aceptación:**
- Debo poder marcar todas mis notificaciones no leídas como leídas
- Todas las notificaciones deben cambiar su estado a "leída"
- El contador debe quedar en cero
- Debo recibir confirmación de la acción

**Prioridad:** Baja

---

## 7. Dashboard y Estadísticas (ONG)

### HU-032: Ver Estadísticas Generales
**Como** ONG autenticada  
**Quiero** ver estadísticas generales de mi organización  
**Para** tener una visión general del desempeño

**Criterios de Aceptación:**
- Debo ver estadísticas de eventos: total, activos, próximos, finalizados
- Debo ver estadísticas de mega eventos: total, activos
- Debo ver estadísticas de voluntarios: total únicos, total inscripciones, aprobados
- Debo ver estadísticas de reacciones: total, eventos con reacciones
- Debo ver distribución de eventos por tipo
- Debo ver distribución de participantes por estado
- La información debe estar actualizada y ser precisa

**Prioridad:** Alta

---

### HU-033: Ver Estadísticas de Participantes
**Como** ONG autenticada  
**Quiero** ver estadísticas detalladas de participantes  
**Para** analizar la participación en mis eventos

**Criterios de Aceptación:**
- Debo ver estadísticas por evento: total, aprobados, pendientes, rechazados
- Debo ver totales generales: total participantes, aprobados, pendientes, rechazados
- Las estadísticas deben estar organizadas por evento
- Debo poder identificar qué eventos tienen más participación

**Prioridad:** Media

---

### HU-034: Ver Lista Detallada de Participantes
**Como** ONG autenticada  
**Quiero** ver una lista detallada de todos mis participantes  
**Para** gestionar y contactar a los voluntarios

**Criterios de Aceptación:**
- Debo ver todos los participantes de todos mis eventos
- Debo poder filtrar por evento específico
- Cada participante debe mostrar: nombre, correo, teléfono, evento, fecha de inscripción, estado, foto de perfil
- La lista debe estar ordenada por fecha de inscripción (más recientes primero)

**Prioridad:** Media

---

### HU-035: Ver Estadísticas de Reacciones
**Como** ONG autenticada  
**Quiero** ver estadísticas de reacciones a mis eventos  
**Para** medir el interés generado

**Criterios de Aceptación:**
- Debo ver el total de reacciones por evento
- Debo ver el total general de reacciones
- Las estadísticas deben estar organizadas por evento
- Debo poder identificar qué eventos generan más interés

**Prioridad:** Baja

---

### HU-036: Ver Lista Detallada de Reacciones
**Como** ONG autenticada  
**Quiero** ver una lista detallada de todas las reacciones  
**Para** conocer quiénes están interesados en mis eventos

**Criterios de Aceptación:**
- Debo ver todos los usuarios que reaccionaron a mis eventos
- Debo poder filtrar por evento específico
- Cada reacción debe mostrar: nombre del usuario, correo, evento, fecha de reacción, foto de perfil
- La lista debe estar ordenada por fecha de reacción (más recientes primero)

**Prioridad:** Baja

---

## 8. Gestión de Voluntarios (ONG)

### HU-037: Ver Lista de Voluntarios
**Como** ONG autenticada  
**Quiero** ver todos mis voluntarios  
**Para** gestionar y hacer seguimiento de las personas que participan en mis eventos

**Criterios de Aceptación:**
- Debo ver todos los voluntarios que han participado en mis eventos
- Cada voluntario debe mostrar: nombre, email, teléfono, tipo de usuario, evento en el que participa, estado, si asistió, puntos, fecha de inscripción, foto de perfil
- Solo debo ver voluntarios con participación aprobada
- La información debe estar organizada y clara

**Prioridad:** Alta

---

## 9. Mega Eventos

### HU-038: Crear Mega Evento
**Como** ONG autenticada  
**Quiero** crear un mega evento  
**Para** organizar eventos de mayor envergadura

**Criterios de Aceptación:**
- Debo poder proporcionar: título, descripción, fecha de inicio, fecha de fin, ubicación, coordenadas
- Debo poder seleccionar una categoría del mega evento
- Debo poder establecer: capacidad máxima, si es público, estado (planificación/activo/en_curso/finalizado/cancelado)
- Debo poder agregar imágenes promocionales (archivo o URL)
- El sistema debe validar que la fecha de fin sea posterior a la fecha de inicio
- Al crear, debo recibir confirmación con los datos del mega evento creado

**Prioridad:** Media

---

### HU-039: Listar Mega Eventos
**Como** usuario autenticado  
**Quiero** ver todos los mega eventos  
**Para** encontrar mega eventos en los que pueda participar

**Criterios de Aceptación:**
- Debo poder ver todos los mega eventos
- Debo poder filtrar por categoría
- Debo poder filtrar por estado
- Debo poder buscar por título o descripción
- Cada mega evento debe mostrar: título, descripción, imágenes, fechas, ubicación, categoría, estado, ONG organizadora
- Los mega eventos deben estar ordenados por fecha de creación (más recientes primero)

**Prioridad:** Media

---

### HU-040: Ver Detalle de Mega Evento
**Como** usuario autenticado  
**Quiero** ver los detalles completos de un mega evento  
**Para** decidir si quiero participar

**Criterios de Aceptación:**
- Debo poder ver toda la información del mega evento: título, descripción, fechas, ubicación, imágenes, categoría, estado
- Debo ver información de la ONG organizadora principal
- Debo ver la capacidad máxima y si es público
- La información debe mostrarse de forma clara y organizada

**Prioridad:** Media

---

### HU-041: Editar Mega Evento
**Como** ONG autenticada  
**Quiero** editar un mega evento que he creado  
**Para** actualizar la información cuando sea necesario

**Criterios de Aceptación:**
- Debo poder modificar: título, descripción, fechas, ubicación, categoría, estado, capacidad máxima
- Debo poder agregar, eliminar o reemplazar imágenes
- Debo poder cambiar si es público o no
- El sistema debe validar todos los campos antes de guardar
- Debo recibir confirmación cuando los cambios se guarden correctamente

**Prioridad:** Media

---

### HU-042: Eliminar Mega Evento
**Como** ONG autenticada  
**Quiero** eliminar un mega evento  
**Para** remover mega eventos que ya no son necesarios

**Criterios de Aceptación:**
- Debo poder eliminar mega eventos que he creado
- Al eliminar, debo recibir confirmación
- El sistema debe eliminar también las imágenes asociadas del storage

**Prioridad:** Baja

---

### HU-043: Eliminar Imagen de Mega Evento
**Como** ONG autenticada  
**Quiero** eliminar una imagen específica de un mega evento  
**Para** gestionar las imágenes promocionales

**Criterios de Aceptación:**
- Debo poder eliminar una imagen específica de un mega evento
- Al eliminar, la imagen debe ser removida del array y del storage
- Debo recibir confirmación de la acción
- El mega evento actualizado debe ser retornado

**Prioridad:** Baja

---

### HU-044: Participar en Mega Evento
**Como** usuario externo o voluntario autenticado  
**Quiero** participar en un mega evento público  
**Para** formar parte de la actividad

**Criterios de Aceptación:**
- Debo poder participar en mega eventos públicos y activos
- El sistema debe validar que el mega evento existe, es público y está activo
- El sistema debe validar que no esté agotado el cupo (si hay capacidad máxima)
- El sistema debe validar que no esté ya participando
- Al participar, debo ser aprobado automáticamente
- Debo recibir confirmación de mi participación
- La ONG debe recibir una notificación de mi participación

**Prioridad:** Media

---

### HU-045: Verificar Participación en Mega Evento
**Como** usuario externo o voluntario autenticado  
**Quiero** verificar si estoy participando en un mega evento  
**Para** saber mi estado de participación

**Criterios de Aceptación:**
- Debo poder verificar si estoy participando en un mega evento específico
- El sistema debe indicar si estoy participando o no
- La verificación debe ser rápida y eficiente

**Prioridad:** Baja

---

### HU-046: Ver Mega Eventos Públicos
**Como** usuario externo o voluntario autenticado  
**Quiero** ver solo los mega eventos públicos  
**Para** encontrar mega eventos en los que pueda participar

**Criterios de Aceptación:**
- Debo poder ver todos los mega eventos públicos y activos
- Debo poder filtrar por categoría
- Debo poder buscar por título o descripción
- Los mega eventos deben estar ordenados por fecha de inicio (más próximos primero)
- Cada mega evento debe mostrar toda su información relevante

**Prioridad:** Media

---

## 10. Configuración y Parametrización

### HU-047: Ver Parámetros del Sistema
**Como** administrador del sistema  
**Quiero** ver todos los parámetros de configuración  
**Para** gestionar la configuración del sistema

**Criterios de Aceptación:**
- Debo poder ver todos los parámetros del sistema
- Debo poder filtrar por categoría
- Debo poder filtrar por grupo
- Debo poder filtrar por visible/editable
- Debo poder buscar por código, nombre o descripción
- Los parámetros deben estar ordenados por categoría, grupo y orden

**Prioridad:** Baja

---

### HU-048: Ver Parámetro por Código
**Como** desarrollador o administrador  
**Quiero** obtener un parámetro específico por su código  
**Para** usar valores de configuración en el sistema

**Criterios de Aceptación:**
- Debo poder obtener un parámetro por su código único
- Debo recibir el valor formateado según el tipo del parámetro
- Si el parámetro no existe, debo recibir un error 404

**Prioridad:** Baja

---

### HU-049: Crear Parámetro
**Como** administrador del sistema  
**Quiero** crear nuevos parámetros de configuración  
**Para** personalizar el comportamiento del sistema

**Criterios de Aceptación:**
- Debo poder crear parámetros con: código, nombre, descripción, categoría, tipo, valor, valor por defecto
- Debo poder especificar: opciones (si es select), grupo, orden, editable, visible, requerido, validación, ayuda
- El código debe ser único
- El sistema debe validar todos los campos
- Debo recibir confirmación cuando el parámetro se cree correctamente

**Prioridad:** Baja

---

### HU-050: Actualizar Parámetro
**Como** administrador del sistema  
**Quiero** actualizar parámetros de configuración  
**Para** modificar la configuración del sistema

**Criterios de Aceptación:**
- Debo poder actualizar parámetros editables
- Debo poder modificar: código, nombre, descripción, categoría, tipo, valor, opciones, grupo, orden, etc.
- El sistema debe validar que el parámetro sea editable
- El sistema debe procesar el valor según el tipo (JSON, booleano, etc.)
- Debo recibir confirmación cuando el parámetro se actualice correctamente

**Prioridad:** Baja

---

### HU-051: Actualizar Solo el Valor de un Parámetro
**Como** administrador del sistema  
**Quiero** actualizar solo el valor de un parámetro  
**Para** cambiar rápidamente valores de configuración

**Criterios de Aceptación:**
- Debo poder actualizar solo el valor de un parámetro editable
- El sistema debe procesar el valor según el tipo del parámetro
- El sistema debe validar que el parámetro sea editable
- Debo recibir confirmación cuando el valor se actualice correctamente

**Prioridad:** Baja

---

### HU-052: Eliminar Parámetro
**Como** administrador del sistema  
**Quiero** eliminar parámetros de configuración  
**Para** limpiar parámetros que ya no son necesarios

**Criterios de Aceptación:**
- Debo poder eliminar parámetros
- Al eliminar, debo recibir confirmación
- El parámetro debe ser removido permanentemente

**Prioridad:** Baja

---

### HU-053: Ver Categorías de Parámetros
**Como** administrador del sistema  
**Quiero** ver todas las categorías de parámetros disponibles  
**Para** organizar y filtrar parámetros

**Criterios de Aceptación:**
- Debo poder obtener una lista de todas las categorías únicas
- Las categorías deben estar ordenadas alfabéticamente

**Prioridad:** Baja

---

### HU-054: Ver Grupos de Parámetros
**Como** administrador del sistema  
**Quiero** ver todos los grupos de parámetros disponibles  
**Para** organizar y filtrar parámetros

**Criterios de Aceptación:**
- Debo poder obtener una lista de todos los grupos únicos
- Los grupos deben estar ordenados alfabéticamente
- Solo deben mostrarse grupos que no sean nulos

**Prioridad:** Baja

---

### HU-055: Gestionar Tipos de Evento
**Como** administrador del sistema  
**Quiero** gestionar los tipos de evento disponibles  
**Para** que las ONGs puedan categorizar sus eventos

**Criterios de Aceptación:**
- Debo poder listar todos los tipos de evento
- Debo poder crear nuevos tipos de evento
- Debo poder actualizar tipos de evento existentes
- Debo poder eliminar tipos de evento
- Cada tipo debe tener: nombre, descripción, activo

**Prioridad:** Baja

---

### HU-056: Gestionar Categorías de Mega Eventos
**Como** administrador del sistema  
**Quiero** gestionar las categorías de mega eventos  
**Para** que las ONGs puedan categorizar sus mega eventos

**Criterios de Aceptación:**
- Debo poder listar todas las categorías de mega eventos
- Debo poder crear nuevas categorías
- Debo poder actualizar categorías existentes
- Debo poder eliminar categorías
- Cada categoría debe tener: nombre, descripción, activo

**Prioridad:** Baja

---

### HU-057: Gestionar Ciudades
**Como** administrador del sistema  
**Quiero** gestionar las ciudades disponibles  
**Para** que las ONGs puedan seleccionar ciudades al crear eventos

**Criterios de Aceptación:**
- Debo poder listar todas las ciudades
- Debo poder crear nuevas ciudades
- Debo poder actualizar ciudades existentes
- Debo poder eliminar ciudades
- Cada ciudad debe tener: nombre, código, departamento, activo

**Prioridad:** Baja

---

### HU-058: Gestionar Lugares
**Como** administrador del sistema  
**Quiero** gestionar los lugares disponibles  
**Para** que las ONGs puedan seleccionar lugares específicos al crear eventos

**Criterios de Aceptación:**
- Debo poder listar todos los lugares
- Debo poder crear nuevos lugares
- Debo poder actualizar lugares existentes
- Debo poder eliminar lugares
- Cada lugar debe tener: nombre, dirección, ciudad, coordenadas, activo

**Prioridad:** Baja

---

### HU-059: Gestionar Estados de Participación
**Como** administrador del sistema  
**Quiero** gestionar los estados de participación  
**Para** controlar el flujo de aprobación de participantes

**Criterios de Aceptación:**
- Debo poder listar todos los estados de participación
- Debo poder crear nuevos estados
- Debo poder actualizar estados existentes
- Debo poder eliminar estados
- Cada estado debe tener: nombre, descripción, orden, activo

**Prioridad:** Baja

---

### HU-060: Gestionar Tipos de Notificación
**Como** administrador del sistema  
**Quiero** gestionar los tipos de notificación  
**Para** categorizar las notificaciones del sistema

**Criterios de Aceptación:**
- Debo poder listar todos los tipos de notificación
- Debo poder crear nuevos tipos
- Debo poder actualizar tipos existentes
- Debo poder eliminar tipos
- Cada tipo debe tener: nombre, descripción, icono, activo

**Prioridad:** Baja

---

### HU-061: Gestionar Estados de Evento
**Como** administrador del sistema  
**Quiero** gestionar los estados de evento  
**Para** controlar el ciclo de vida de los eventos

**Criterios de Aceptación:**
- Debo poder listar todos los estados de evento
- Debo poder crear nuevos estados
- Debo poder actualizar estados existentes
- Debo poder eliminar estados
- Cada estado debe tener: nombre, descripción, orden, activo

**Prioridad:** Baja

---

### HU-062: Gestionar Tipos de Usuario
**Como** administrador del sistema  
**Quiero** gestionar los tipos de usuario  
**Para** controlar los roles y permisos del sistema

**Criterios de Aceptación:**
- Debo poder listar todos los tipos de usuario
- Debo poder crear nuevos tipos
- Debo poder actualizar tipos existentes
- Debo poder eliminar tipos
- Cada tipo debe tener: nombre, descripción, permisos, activo

**Prioridad:** Baja

---

## 11. Gestión de Empresas

### HU-063: Ver Eventos Disponibles para Patrocinar (Empresa)
**Como** empresa autenticada  
**Quiero** ver eventos disponibles para patrocinar  
**Para** encontrar oportunidades de patrocinio

**Criterios de Aceptación:**
- Debo poder ver eventos que están buscando patrocinadores
- Debo poder ver información relevante de cada evento
- Debo poder contactar a las ONGs para patrocinar

**Prioridad:** Baja

---

### HU-064: Ver Mega Eventos (Empresa)
**Como** empresa autenticada  
**Quiero** ver mega eventos disponibles  
**Para** encontrar oportunidades de participación o patrocinio

**Criterios de Aceptación:**
- Debo poder ver todos los mega eventos públicos
- Debo poder ver información relevante de cada mega evento
- Debo poder ver la ONG organizadora

**Prioridad:** Baja

---

## Resumen de Prioridades

### Prioridad Alta (Críticas para el funcionamiento básico)
- HU-001 a HU-004: Registro e inicio de sesión
- HU-008 a HU-012: Gestión básica de eventos (ONG)
- HU-017 a HU-020: Participación en eventos (Externos)
- HU-022: Ver participantes (ONG)
- HU-028: Ver notificaciones
- HU-032: Estadísticas generales
- HU-037: Ver voluntarios

### Prioridad Media (Importantes pero no críticas)
- HU-005 a HU-007: Gestión de perfil
- HU-013 a HU-016: Funcionalidades adicionales de eventos
- HU-021, HU-023, HU-024: Gestión de participaciones
- HU-025, HU-026: Reacciones
- HU-029, HU-030: Gestión de notificaciones
- HU-033 a HU-036: Estadísticas detalladas
- HU-038 a HU-046: Mega eventos

### Prioridad Baja (Mejoras y funcionalidades avanzadas)
- HU-027, HU-031: Funcionalidades adicionales de reacciones y notificaciones
- HU-047 a HU-062: Configuración y parametrización
- HU-063, HU-064: Funcionalidades para empresas

---

## Notas Finales

- **Total de Historias de Usuario:** 64
- **Formato:** Todas las historias siguen el formato estándar: Como [rol], Quiero [acción], Para [objetivo]
- **Criterios de Aceptación:** Cada historia incluye criterios específicos y medibles
- **Priorización:** Las historias están priorizadas según su importancia para el funcionamiento del sistema

Este documento puede ser utilizado como base para:
- Planificación de sprints
- Estimación de esfuerzo
- Definición de alcance del proyecto
- Documentación para stakeholders
- Guía para el equipo de desarrollo


@extends('layouts.adminlte')

@section('page_title', 'Detalle del Evento')

@section('content_body')
<input type="hidden" id="eventoId" value="{{ request()->id }}">
<div class="container-fluid px-0">
    <!-- Banner Superior con Imagen Principal - Mejorado -->
    <div id="eventBanner" class="position-relative" style="height: 450px; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); overflow: hidden; border-radius: 0 0 24px 24px;">
        <div id="bannerImage" class="w-100 h-100" style="background-size: cover; background-position: center; opacity: 0.25; transition: transform 0.5s ease;"></div>
        <div class="position-absolute" style="top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(to bottom, rgba(12, 43, 68, 0.4) 0%, rgba(0, 163, 108, 0.7) 100%);"></div>
        <div class="position-absolute" style="bottom: 0; left: 0; right: 0; padding: 3rem 2rem; color: white;">
            <div class="container">
                <div class="d-flex align-items-center mb-3">
                    <div class="mr-3" style="width: 64px; height: 64px; background: rgba(255,255,255,0.15); border-radius: 16px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px); box-shadow: 0 4px 16px rgba(0,0,0,0.2);">
                        <i class="fas fa-calendar-alt" style="font-size: 2rem;"></i>
                    </div>
                    <div style="flex: 1;">
                        <h1 id="titulo" class="mb-2" style="font-size: 2.75rem; font-weight: 700; text-shadow: 2px 2px 8px rgba(0,0,0,0.5); letter-spacing: -0.5px; line-height: 1.2;"></h1>
                        <div class="d-flex flex-wrap align-items-center" style="gap: 0.75rem;">
                            <span id="tipoEventoBadge" class="badge" style="font-size: 0.95rem; padding: 0.6em 1.2em; background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); border-radius: 50px; font-weight: 500;"></span>
                            <span id="estadoBadge" class="badge" style="font-size: 0.95rem; padding: 0.6em 1.2em; border-radius: 50px; font-weight: 500;"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Imagen de Galería -->
    <div id="modalImagenGaleria" class="modal fade" tabindex="-1" role="dialog" style="display: none;">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 8px 32px rgba(0,0,0,0.3); background: transparent;">
                <div class="modal-body p-0" style="position: relative;">
                    <button type="button" class="close" onclick="cerrarModalImagen()" aria-label="Close" style="position: absolute; top: 10px; right: 10px; z-index: 1050; border: none; background: rgba(255,255,255,0.9); border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: #333; opacity: 0.8; cursor: pointer; transition: all 0.3s ease;" onmouseover="this.style.opacity='1'; this.style.background='rgba(255,255,255,1)'" onmouseout="this.style.opacity='0.8'; this.style.background='rgba(255,255,255,0.9)'">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <img id="imagenModalGaleria" src="" alt="Imagen de galería" style="width: 100%; height: auto; border-radius: 16px; max-height: 80vh; object-fit: contain;">
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Compartir -->
    <div id="modalCompartir" class="modal fade" tabindex="-1" role="dialog" style="display: none;">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 400px;">
            <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 8px 32px rgba(0,0,0,0.2);">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold">Compartir</h5>
                    <button type="button" class="close" onclick="cerrarModalCompartir()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row text-center">
                        <!-- Copiar enlace -->
                        <div class="col-6 mb-4">
                            <button onclick="copiarEnlace()" class="btn btn-link p-0" style="text-decoration: none; border: none; background: none; width: 100%;">
                                <div class="bg-light rounded-lg d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; transition: all 0.3s ease;" onmouseover="this.style.transform='scale(1.1)'; this.style.background='#E9ECEF';" onmouseout="this.style.transform='scale(1)'; this.style.background='#F5F5F5';">
                                    <i class="fas fa-link fa-2x text-primary"></i>
                                </div>
                                <span class="font-weight-bold text-dark">Copiar enlace</span>
                            </button>
                        </div>
                        <!-- QR Code -->
                        <div class="col-6 mb-4">
                            <button onclick="mostrarQR()" class="btn btn-link p-0" style="text-decoration: none; border: none; background: none; width: 100%;">
                                <div class="bg-primary rounded-lg d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; transition: all 0.3s ease;" onmouseover="this.style.transform='scale(1.1)'; this.style.background='#0056b3';" onmouseout="this.style.transform='scale(1)'; this.style.background='#007bff';">
                                    <i class="fas fa-qrcode fa-2x text-white"></i>
                                </div>
                                <span class="font-weight-bold text-dark">Código QR</span>
                            </button>
                        </div>
                    </div>
                    <!-- Contenedor para el QR -->
                    <div id="qrContainer" style="display: none; margin-top: 1.5rem;">
                        <div class="text-center">
                            <div id="qrcode" class="d-inline-block p-3 bg-white rounded mb-3"></div>
                            <p class="text-muted mb-0">Escanea este código para acceder al evento</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Control de Asistencias (Consolidado) -->
    <div id="modalGestionarParticipantes" class="modal fade" tabindex="-1" role="dialog" style="display: none;">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
            <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 8px 32px rgba(0,0,0,0.3);">
                <div class="modal-header" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); color: white; border-radius: 16px 16px 0 0;">
                    <h5 class="modal-title font-weight-bold">
                        <i class="fas fa-clipboard-check mr-2"></i> Control de Asistencias
                    </h5>
                    <button type="button" class="close text-white" onclick="cerrarGestionParticipantes()" aria-label="Close" style="opacity: 0.9;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <!-- Tabs de navegación -->
                    <ul class="nav nav-tabs mb-4" id="tabsControlAsistencia" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="tab-participantes" data-toggle="tab" href="#contenido-participantes" role="tab">
                                <i class="fas fa-users mr-2"></i> Participantes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-comentarios" data-toggle="tab" href="#contenido-comentarios" role="tab">
                                <i class="fas fa-comments mr-2"></i> Comentarios
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-estadisticas" data-toggle="tab" href="#contenido-estadisticas" role="tab">
                                <i class="fas fa-chart-bar mr-2"></i> Estadísticas
                            </a>
                        </li>
                    </ul>

                    <!-- Contenido de las tabs -->
                    <div class="tab-content" id="contenidoControlAsistencia">
                        <!-- Tab de Participantes -->
                        <div class="tab-pane fade show active" id="contenido-participantes" role="tabpanel">
                            <div class="modal-body p-4">
                    <!-- Filtros y búsqueda -->
                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <label class="font-weight-semibold text-dark">Filtrar por estado:</label>
                            <select id="filtroEstadoAsistencia" class="form-control" onchange="filtrarParticipantes()">
                                <option value="todos">Todos</option>
                                <option value="asistio">Asistieron</option>
                                <option value="no_asistio">No asistieron</option>
                                <option value="pendiente">Pendiente</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="font-weight-semibold text-dark">Filtrar por inscripción:</label>
                            <select id="filtroEstadoParticipacion" class="form-control" onchange="filtrarParticipantes()">
                                <option value="todos">Todos</option>
                                <option value="aprobada">Aprobados</option>
                                <option value="pendiente">Pendientes</option>
                                <option value="rechazada">Rechazados</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="font-weight-semibold text-dark">Buscar:</label>
                            <input type="text" id="buscarParticipante" class="form-control" placeholder="Buscar por nombre..." oninput="filtrarParticipantes()">
                        </div>
                    </div>

                    <!-- Estadísticas rápidas -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-2">
                            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <div class="card-body text-white text-center">
                                    <h3 id="totalParticipantes" class="mb-0 font-weight-bold">0</h3>
                                    <small>Total Inscritos</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                                <div class="card-body text-white text-center">
                                    <h3 id="totalAsistieron" class="mb-0 font-weight-bold">0</h3>
                                    <small>Asistieron</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);">
                                <div class="card-body text-white text-center">
                                    <h3 id="totalNoAsistieron" class="mb-0 font-weight-bold">0</h3>
                                    <small>No Asistieron</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);">
                                <div class="card-body text-white text-center">
                                    <h3 id="totalPendientes" class="mb-0 font-weight-bold">0</h3>
                                    <small>Pendientes</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de participantes -->
                    <div id="loadingParticipantes" class="text-center py-5" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                        <p class="mt-3 text-muted">Cargando participantes...</p>
                    </div>

                    <div id="tablaParticipantes" class="table-responsive">
                        <!-- Se llenará dinámicamente -->
                    </div>

                    <div id="sinParticipantes" class="alert alert-info text-center" style="display: none;">
                        <i class="fas fa-info-circle mr-2"></i> No se encontraron participantes con los filtros aplicados.
                    </div>
                            </div>
                        </div>

                        <!-- Tab de Comentarios -->
                        <div class="tab-pane fade" id="contenido-comentarios" role="tabpanel">
                            <div class="p-3">
                                <h5 class="mb-4"><i class="fas fa-comments mr-2 text-primary"></i> Comentarios de Asistencia</h5>
                                
                                <div id="loadingComentarios" class="text-center py-5" style="display: none;">
                                    <div class="spinner-border text-primary" role="status"></div>
                                    <p class="mt-3 text-muted">Cargando comentarios...</p>
                                </div>
                                
                                <div id="listaComentarios">
                                    <!-- Se llenará dinámicamente -->
                                </div>
                                
                                <div id="sinComentarios" class="alert alert-info text-center" style="display: none;">
                                    <i class="fas fa-info-circle mr-2"></i> No hay comentarios de asistencia aún.
                                </div>
                            </div>
                        </div>

                        <!-- Tab de Estadísticas -->
                        <div class="tab-pane fade" id="contenido-estadisticas" role="tabpanel">
                            <div class="p-3">
                                <h5 class="mb-4"><i class="fas fa-chart-bar mr-2 text-primary"></i> Estadísticas del Evento</h5>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <div class="card border-0 shadow-sm">
                                            <div class="card-body">
                                                <h6 class="font-weight-bold mb-3">Tasa de Asistencia</h6>
                                                <div class="progress" style="height: 25px; border-radius: 10px;">
                                                    <div id="progressAsistencia" class="progress-bar bg-success" role="progressbar" style="width: 0%">
                                                        <span id="porcentajeAsistencia" class="font-weight-bold">0%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <div class="card border-0 shadow-sm">
                                            <div class="card-body">
                                                <h6 class="font-weight-bold mb-3">Estado de Inscripciones</h6>
                                                <canvas id="chartInscripciones" height="100"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card border-0 shadow-sm">
                                            <div class="card-body">
                                                <h6 class="font-weight-bold mb-3">Resumen General</h6>
                                                <div class="table-responsive">
                                                    <table class="table table-sm">
                                                        <tbody>
                                                            <tr>
                                                                <td class="font-weight-semibold">Total de Inscritos:</td>
                                                                <td id="resumenTotal" class="text-right">0</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="font-weight-semibold">Participantes que Asistieron:</td>
                                                                <td id="resumenAsistieron" class="text-right text-success">0</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="font-weight-semibold">Participantes que No Asistieron:</td>
                                                                <td id="resumenNoAsistieron" class="text-right text-danger">0</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="font-weight-semibold">Pendientes de Confirmar:</td>
                                                                <td id="resumenPendientes" class="text-right text-warning">0</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="font-weight-semibold">Con Comentarios:</td>
                                                                <td id="resumenConComentarios" class="text-right text-info">0</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="cerrarGestionParticipantes()">
                        <i class="fas fa-times mr-2"></i> Cerrar
                    </button>
                    <button type="button" class="btn btn-primary" onclick="exportarParticipantes()">
                        <i class="fas fa-file-excel mr-2"></i> Exportar a Excel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <!-- Mensaje de Evento Finalizado -->
        <div id="mensajeEventoFinalizado" class="alert alert-secondary mb-4" style="display: none; border-radius: 16px; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
            <div class="d-flex align-items-center">
                <div class="mr-3" style="width: 56px; height: 56px; background: linear-gradient(135deg, #6c757d 0%, #495057 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-info-circle fa-2x text-white"></i>
                </div>
                <div>
                    <h5 class="mb-1 font-weight-bold" style="color: #2c3e50;">Este evento fue finalizado</h5>
                    <p class="mb-0 text-muted">
                        Fecha de finalización: <span id="fechaFinalizacionMensaje" class="font-weight-bold text-dark"></span>
                    </p>
                    <p class="mb-0 mt-2 text-muted" style="font-size: 0.9rem;">
                        Ya no es posible participar, reaccionar o compartir este evento. Solo puedes ver los detalles.
                    </p>
                </div>
            </div>
        </div>

        <!-- Botones de Acción (ONG) - Mejorados -->
        <div class="d-flex justify-content-end mb-4 flex-wrap" style="gap: 0.75rem;">
            <a href="/ong/eventos" class="btn btn-outline-secondary" style="border-radius: 10px; padding: 0.6rem 1.5rem; font-weight: 500; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <i class="fas fa-arrow-left mr-2"></i> Volver
            </a>
            <a id="btnDashboard" href="#" class="btn btn-success" style="display: none; border-radius: 10px; padding: 0.6rem 1.5rem; font-weight: 600; box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);">
                <i class="fas fa-chart-bar mr-2"></i> Dashboard
            </a>
            <button id="btnControlAsistencia" class="btn btn-info" onclick="abrirGestionParticipantes()" style="display: none; border-radius: 10px; padding: 0.6rem 1.5rem; font-weight: 600; box-shadow: 0 4px 12px rgba(23, 162, 184, 0.3);">
                <i class="fas fa-clipboard-check mr-2"></i> Control Asistencias
            </button>
            <div class="btn btn-outline-danger d-flex align-items-center" id="btnReacciones" style="cursor: default; border-radius: 10px; padding: 0.6rem 1.5rem; font-weight: 500; box-shadow: 0 2px 8px rgba(220, 53, 69, 0.2);">
                <i class="fas fa-heart mr-2"></i>
                <span id="contadorReaccionesOng">0</span> reacciones
            </div>
            <button class="btn btn-primary d-flex align-items-center" id="btnCompartir" style="border-radius: 10px; padding: 0.6rem 1.5rem; font-weight: 600; box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);">
                <i class="fas fa-share-alt mr-2"></i> Compartir 
                <span id="contadorCompartidos" class="badge badge-light ml-2" style="background: rgba(255,255,255,0.3); color: white;">0</span>
            </button>
            <a id="btnEditar" href="#" class="btn btn-primary" style="border-radius: 10px; padding: 0.6rem 1.5rem; font-weight: 600; box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);">
                <i class="fas fa-edit mr-2"></i> Editar
            </a>
        </div>

        <div class="row">
            <!-- Columna Principal -->
            <div class="col-lg-8">
                <!-- Descripción -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="section-icon mr-3">
                                <i class="fas fa-align-left"></i>
                            </div>
                            <div>
                                <h5 class="mb-0" style="font-weight: 700; color: #0C2B44; font-size: 1.1rem;">
                                    Descripción
                        </h5>
                    </div>
                        </div>
                        <p id="descripcion" class="mb-0 text-muted" style="line-height: 1.8; font-size: 1rem; color: #495057;"></p>
                    </div>
                </div>

                <!-- Información del Evento -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="section-icon mr-3">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <div>
                                <h5 class="mb-0" style="font-weight: 700; color: #0C2B44; font-size: 1.1rem;">
                                    Información del Evento
                        </h5>
                                <p class="mb-0 text-muted" style="font-size: 0.85rem; margin-top: 0.25rem;">
                                    Detalles importantes del evento
                                </p>
                    </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                    <div class="info-content">
                                        <h6 class="info-label">Fecha de Inicio</h6>
                                        <p id="fecha_inicio" class="info-value"></p>
                                </div>
                            </div>
                                    </div>
                            <div class="col-md-6 mb-4">
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-calendar-times"></i>
                                </div>
                                    <div class="info-content">
                                        <h6 class="info-label">Fecha de Fin</h6>
                                        <p id="fecha_fin" class="info-value"></p>
                            </div>
                                    </div>
                                </div>
                            <div class="col-md-6 mb-4">
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-clock"></i>
                            </div>
                                    <div class="info-content">
                                        <h6 class="info-label">Límite de Inscripción</h6>
                                        <p id="fecha_limite_inscripcion" class="info-value"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div class="info-content">
                                        <h6 class="info-label">Capacidad Máxima</h6>
                                        <p id="capacidad_maxima" class="info-value"></p>
                                </div>
                            </div>
                            </div>
                            <div class="col-md-6 mb-4" id="fechaFinalizacionContainer" style="display: none;">
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-flag-checkered"></i>
                                    </div>
                                    <div class="info-content">
                                        <h6 class="info-label">Fecha de Finalización</h6>
                                        <p id="fecha_finalizacion" class="info-value"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4" id="creadorContainer">
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-user-circle"></i>
                                    </div>
                                    <div class="info-content">
                                        <h6 class="info-label">Creado por</h6>
                                        <div id="creadorInfo" class="d-flex align-items-center">
                                            <span id="creadorNombre" class="info-value"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ubicación -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="section-icon mr-3">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <h5 class="mb-0" style="font-weight: 700; color: #0C2B44; font-size: 1.1rem;">
                                    Ubicación
                        </h5>
                                <p class="mb-0 text-muted" style="font-size: 0.85rem; margin-top: 0.25rem;">
                                    Lugar donde se realizará el evento
                                </p>
                    </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-city"></i>
                                    </div>
                                    <div class="info-content">
                                        <h6 class="info-label">Ciudad</h6>
                                        <p id="ciudad" class="info-value"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-road"></i>
                            </div>
                                    <div class="info-content">
                                        <h6 class="info-label">Dirección</h6>
                                        <p id="direccion" class="info-value"></p>
                        </div>
                                </div>
                            </div>
                        </div>
                        <div id="mapContainer" class="mt-3 rounded" style="height: 350px; overflow: hidden; display: none; border: 2px solid #f0f0f0; box-shadow: 0 2px 12px rgba(0,0,0,0.05);">
                            <!-- Mapa se cargará aquí -->
                        </div>
                    </div>
                </div>

                <!-- Galería de Imágenes -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="section-icon mr-3">
                                <i class="fas fa-images"></i>
                            </div>
                            <div>
                                <h5 class="mb-0" style="font-weight: 700; color: #0C2B44; font-size: 1.1rem;">
                                    Galería de Imágenes
                        </h5>
                                <p class="mb-0 text-muted" style="font-size: 0.85rem; margin-top: 0.25rem;">
                                    Imágenes promocionales del evento
                                </p>
                    </div>
                        </div>
                        <div id="imagenes">
                            <!-- Carrusel de Bootstrap -->
                            <div id="carouselImagenes" class="carousel slide" data-ride="carousel" data-interval="3000" style="display: none; border-radius: 12px; overflow: hidden;">
                                <div class="carousel-inner" id="carouselInner"></div>
                                <a class="carousel-control-prev" href="#carouselImagenes" role="button" data-slide="prev" style="width: 5%;">
                                    <span class="carousel-control-prev-icon" aria-hidden="true" style="background-color: rgba(0,0,0,0.6); border-radius: 50%; width: 45px; height: 45px; backdrop-filter: blur(10px);"></span>
                                    <span class="sr-only">Anterior</span>
                                </a>
                                <a class="carousel-control-next" href="#carouselImagenes" role="button" data-slide="next" style="width: 5%;">
                                    <span class="carousel-control-next-icon" aria-hidden="true" style="background-color: rgba(0,0,0,0.6); border-radius: 50%; width: 45px; height: 45px; backdrop-filter: blur(10px);"></span>
                                    <span class="sr-only">Siguiente</span>
                                </a>
                                <!-- Indicadores -->
                                <ol class="carousel-indicators" id="carouselIndicators"></ol>
                            </div>
                            <p id="sinImagenes" class="text-muted text-center py-4" style="display: none;">
                                <i class="fas fa-image mr-2"></i> No hay imágenes disponibles
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Reacciones (Favoritos) -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div class="d-flex align-items-center">
                                <div class="section-icon mr-3">
                                    <i class="fas fa-heart"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0" style="font-weight: 700; color: #0C2B44; font-size: 1.1rem;">
                                        Reacciones y Favoritos
                        </h5>
                                    <p class="mb-0 text-muted" style="font-size: 0.85rem; margin-top: 0.25rem;">
                                        Usuarios que marcaron este evento como favorito
                                    </p>
                                </div>
                            </div>
                            <button class="btn btn-sm btn-outline-secondary btn-actualizar-reacciones" onclick="cargarReacciones()" style="border-radius: 8px; padding: 0.4rem 1rem;">
                            <i class="fas fa-sync mr-1"></i> Actualizar
                        </button>
                    </div>
                        <div id="reaccionesContainer">
                            <div class="text-center py-4">
                                <div class="spinner-border" role="status" style="color: #00A36C; width: 3rem; height: 3rem;">
                                    <span class="sr-only">Cargando...</span>
                                </div>
                                <p class="mt-3 text-muted">Cargando reacciones...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Voluntarios y Participantes Inscritos -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div class="d-flex align-items-center">
                                <div class="section-icon mr-3">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0" style="font-weight: 700; color: #0C2B44; font-size: 1.1rem;">
                                        Participantes Inscritos
                        </h5>
                                    <p class="mb-0 text-muted" style="font-size: 0.85rem; margin-top: 0.25rem;">
                                        Usuarios registrados y voluntarios
                                    </p>
                                </div>
                            </div>
                            <button class="btn btn-sm btn-outline-secondary btn-actualizar-participantes" onclick="cargarParticipantesCardONG()" style="border-radius: 8px; padding: 0.4rem 1rem;">
                            <i class="fas fa-sync mr-1"></i> Actualizar
                        </button>
                    </div>
                        <div id="participantesContainer">
                            <div class="text-center py-4">
                                <div class="spinner-border" role="status" style="color: #00A36C; width: 3rem; height: 3rem;">
                                    <span class="sr-only">Cargando...</span>
                                </div>
                                <p class="mt-3 text-muted">Cargando participantes...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Información Rápida -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; position: sticky; top: 20px;">
                    <div class="card-body p-4" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); border-radius: 16px 16px 0 0;">
                        <div class="d-flex align-items-center mb-3">
                            <div class="mr-3" style="width: 48px; height: 48px; background: rgba(255,255,255,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px);">
                                <i class="fas fa-info-circle text-white" style="font-size: 1.25rem;"></i>
                            </div>
                            <h5 class="mb-0 text-white" style="font-weight: 700; font-size: 1.1rem;">
                                Información Rápida
                        </h5>
                    </div>
                        </div>
                    <div class="card-body p-4">
                        <div class="info-sidebar-item mb-3 pb-3" style="border-bottom: 1px solid #f0f0f0;">
                            <small class="d-block mb-2" style="color: #6c757d; font-weight: 600; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                <i class="fas fa-flag mr-1"></i> Estado
                            </small>
                            <span id="estadoSidebar" class="badge" style="font-size: 0.9rem; padding: 0.5em 1em;"></span>
                        </div>
                        <div class="info-sidebar-item mb-3 pb-3" style="border-bottom: 1px solid #f0f0f0;">
                            <small class="d-block mb-2" style="color: #6c757d; font-weight: 600; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                <i class="fas fa-tag mr-1"></i> Tipo de Evento
                            </small>
                            <span id="tipoEventoSidebar" class="font-weight-bold text-dark" style="font-size: 1rem;"></span>
                        </div>
                        <div class="info-sidebar-item mb-3 pb-3" style="border-bottom: 1px solid #f0f0f0;">
                            <small class="d-block mb-2" style="color: #6c757d; font-weight: 600; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                <i class="fas fa-users mr-1"></i> Capacidad
                            </small>
                            <span id="capacidadSidebar" class="font-weight-bold text-dark" style="font-size: 1rem;"></span>
                        </div>
                        <div id="inscripcionAbiertaContainer" class="info-sidebar-item mb-0">
                            <small class="d-block mb-2" style="color: #6c757d; font-weight: 600; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                <i class="fas fa-user-check mr-1"></i> Inscripción
                            </small>
                            <span id="inscripcionAbierta" class="badge" style="font-size: 0.9rem; padding: 0.5em 1em;"></span>
                        </div>
                    </div>
                </div>

                <!-- Patrocinadores -->
                <div id="patrocinadoresCard" class="card border-0 shadow-sm mb-4" style="display: none; border-radius: 16px; position: sticky; top: 400px;">
                    <div class="card-body p-4" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border-radius: 16px 16px 0 0;">
                        <div class="d-flex align-items-center">
                            <div class="mr-3" style="width: 48px; height: 48px; background: rgba(255,255,255,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px);">
                                <i class="fas fa-handshake text-white" style="font-size: 1.25rem;"></i>
                            </div>
                            <h5 class="mb-0 text-white" style="font-weight: 700; font-size: 1.1rem;">
                                Patrocinadores
                        </h5>
                    </div>
                    </div>
                    <div class="card-body p-4">
                        <div id="patrocinadores" class="d-flex flex-wrap" style="gap: 0.75rem;"></div>
                    </div>
                </div>

                <!-- Invitados -->
                <div id="invitadosCard" class="card border-0 shadow-sm mb-4" style="display: none; border-radius: 16px; position: sticky; top: 600px;">
                    <div class="card-body p-4" style="background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%); border-radius: 16px 16px 0 0;">
                        <div class="d-flex align-items-center">
                            <div class="mr-3" style="width: 48px; height: 48px; background: rgba(255,255,255,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px);">
                                <i class="fas fa-user-friends text-white" style="font-size: 1.25rem;"></i>
                            </div>
                            <h5 class="mb-0 text-white" style="font-weight: 700; font-size: 1.1rem;">
                                Invitados Especiales
                        </h5>
                    </div>
                    </div>
                    <div class="card-body p-4">
                        <div id="invitados" class="d-flex flex-wrap" style="gap: 0.75rem;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    /* Variables de color */
    :root {
        --primary-color: #00A36C;
        --primary-dark: #008a5a;
        --dark-color: #0C2B44;
        --border-color: #e9ecef;
        --bg-light: #f8f9fa;
        --shadow-sm: 0 2px 8px rgba(12, 43, 68, 0.08);
        --shadow-md: 0 4px 16px rgba(12, 43, 68, 0.12);
    }

    body {
        background-color: #f5f7fa;
    }
    
    /* Banner mejorado */
    #eventBanner {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        border-radius: 0 0 24px 24px;
        overflow: hidden;
    }

    /* Iconos de sección - Con container y icono adentro */
    .section-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
        box-shadow: 0 4px 12px rgba(0, 163, 108, 0.2);
        flex-shrink: 0;
    }

    /* Items de información */
    .info-item {
        display: flex;
        align-items: flex-start;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 12px;
        border-left: 4px solid #00A36C;
        transition: all 0.3s ease;
    }

    .info-item:hover {
        background: #ffffff;
        box-shadow: 0 4px 12px rgba(0, 163, 108, 0.1);
        transform: translateX(4px);
    }

    .info-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0C2B44;
        font-size: 1.2rem;
        margin-right: 1rem;
        flex-shrink: 0;
    }

    .info-content {
        flex: 1;
    }

    .info-label {
        font-size: 0.85rem;
        color: #6c757d;
        font-weight: 600;
        margin-bottom: 0.25rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-value {
        font-size: 1rem;
        color: #2c3e50;
        font-weight: 500;
        margin: 0;
    }

    /* Cards mejoradas */
    .card {
        transition: all 0.3s ease;
        border: none !important;
    }
    
    .card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-md) !important;
    }
    
    /* Estilos para galería de imágenes mejorados */
    .gallery-item {
        position: relative;
        overflow: hidden;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .gallery-item:hover {
        transform: scale(1.05);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }
    
    .gallery-item img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    /* Carrusel mejorado */
    #carouselImagenes {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    }

    #carouselImagenes .carousel-item img {
        border-radius: 12px;
    }

    /* Badges mejorados */
    .badge {
        border-radius: 50px;
        padding: 0.5em 1em;
        font-weight: 500;
        letter-spacing: 0.3px;
    }

    /* Botones mejorados */
    .btn {
        border-radius: 10px;
        font-weight: 500;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .btn-primary {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        border: none;
    }

    .btn-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: none;
    }

    .btn-info {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        border: none;
    }

    .btn-secondary {
        background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
        border: none;
    }

    .btn-outline-danger {
        border: 2px solid #dc3545;
        color: #dc3545;
    }

    .btn-outline-danger:hover {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        border-color: #dc3545;
        color: white;
    }

    .btn-outline-secondary {
        border: 2px solid #6c757d;
        color: #6c757d;
    }

    .btn-outline-secondary:hover {
        background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
        border-color: #6c757d;
        color: white;
    }
    
    /* Estilos para participantes */
    .participante-item {
        padding: 0.75rem;
        border-radius: 8px;
        background: #F5F5F5;
        border-left: 3px solid #00A36C;
        margin-bottom: 0.5rem;
    }
    
    /* Estilos para avatares de participantes - círculos perfectos */
    .participante-card .card-body img[alt*="participante"],
    .participante-card .card-body img[alt*="Carmen"],
    .participante-card .card-body img[alt*="usuario"],
    .participante-card .card-body img[alt*="Sin nombre"] {
        width: 70px !important;
        height: 70px !important;
        border-radius: 50% !important;
        object-fit: cover !important;
        object-position: center !important;
        border: 4px solid #00A36C !important;
        box-shadow: 0 4px 12px rgba(0, 163, 108, 0.3) !important;
        display: block !important;
        background: #f8f9fa !important;
    }
    
    /* Ocultar placeholder cuando hay imagen visible */
    .participante-card .card-body div[id^="avatar-placeholder"] {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
    }
    
    /* Mostrar placeholder solo si la imagen falla (cuando la imagen tiene display:none) */
    .participante-card .card-body img[style*="display: none"] ~ div[id^="avatar-placeholder"],
    .participante-card .card-body img[style*="display:none"] ~ div[id^="avatar-placeholder"] {
        display: flex !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
    
    .participante-card .card-body > div > div:first-child > div {
        width: 70px !important;
        height: 70px !important;
        border-radius: 50% !important;
        overflow: hidden !important;
    }
    
    /* Mejoras para cards de reacciones y participantes */
    .participante-card {
        animation: fadeInUp 0.6s ease-out;
        border-radius: 16px !important;
        overflow: hidden;
    }

    .reaccion-card {
        animation: fadeInUp 0.6s ease-out;
        border-radius: 16px !important;
        overflow: hidden;
    }
    
    /* Animación suave para las tarjetas */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Patrocinadores e invitados mejorados - Ya manejado en JavaScript */

    /* Mapa mejorado */
    #mapContainer {
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    #mapContainer:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.1) !important;
    }

    /* Responsive */
    @media (max-width: 991px) {
        .section-icon {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }

        .info-icon {
            font-size: 1rem;
        }

        h5 {
            font-size: 1rem !important;
        }

        #eventBanner {
            height: 350px !important;
        }

        #eventBanner h1 {
            font-size: 2rem !important;
        }
    }

    /* Animaciones para reacciones */
    @keyframes heartBeat {
        0%, 100% {
            transform: scale(1);
        }
        25% {
            transform: scale(1.2);
        }
        50% {
            transform: scale(1.1);
        }
        75% {
            transform: scale(1.15);
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
            opacity: 1;
        }
        50% {
            transform: scale(1.1);
            opacity: 0.8;
        }
    }

    .reaccion-card {
        animation: fadeInUp 0.5s ease-out;
    }

    .reaccion-card .fa-heart {
        animation: heartBeat 0.6s ease-in-out;
    }

    .reaccion-card:hover .fa-heart {
        animation: pulse 1s ease-in-out infinite;
        color: #dc3545 !important;
    }

    /* Estilos para prevenir renderizado vertical del texto en cards de reacciones */
    #reaccionesContainer .reaccion-card .card-body {
        display: flex;
        flex-direction: column;
    }

    #reaccionesContainer .reaccion-card .d-flex {
        flex-wrap: nowrap !important;
        white-space: nowrap !important;
    }

    #reaccionesContainer .reaccion-card .flex-grow-1 {
        min-width: 0;
        overflow: hidden;
        flex: 1 1 auto;
    }

    #reaccionesContainer .reaccion-card h6 {
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        display: inline-block !important;
        max-width: 100% !important;
    }

    #reaccionesContainer .reaccion-card .badge {
        white-space: nowrap !important;
        flex-shrink: 0 !important;
    }

    #reaccionesContainer .reaccion-card .mb-2 {
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        display: flex !important;
        align-items: center !important;
    }

    #reaccionesContainer .reaccion-card .mb-2 span {
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        display: inline-block !important;
    }

    #reaccionesContainer .reaccion-card .rounded-circle {
        flex-shrink: 0 !important;
    }

    /* Asegurar que el avatar solo se muestre una vez */
    #reaccionesContainer .reaccion-card .position-relative img {
        display: block !important;
    }

    #reaccionesContainer .reaccion-card .position-relative > div[style*="display: none"] {
        display: none !important;
    }

    /* Animación para el contador de reacciones */
    #contadorReaccionesOng {
        transition: all 0.3s ease;
    }

    #contadorReaccionesOng.animate {
        animation: pulse 0.5s ease-in-out;
        color: #dc3545;
        font-weight: 700;
    }

    /* Animación para el botón de actualizar reacciones */
    .btn-actualizar-reacciones:active {
        transform: rotate(360deg);
        transition: transform 0.5s ease;
    }

    /* Estilos para el modal de compartir */
    .modal-backdrop {
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal.show {
        display: block !important;
    }
</style>
@parent
@stop

@section('js')
@parent
{{-- Script global para icono de notificaciones --}}
<script src="{{ asset('js/notificaciones-ong.js') }}"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<!-- Librería QRCode para generar códigos QR -->
<script src="https://cdn.jsdelivr.net/npm/qrcodejs2@0.0.2/qrcode.min.js"></script>
<script>
    // Definir PUBLIC_BASE_URL desde variable de entorno
    window.PUBLIC_BASE_URL = "{{ env('PUBLIC_APP_URL', 'http://10.26.5.12:8000') }}";
    console.log("🌐 PUBLIC_BASE_URL desde .env:", window.PUBLIC_BASE_URL);
</script>
<script src="{{ asset('assets/js/config.js') }}"></script>
<script src="{{ asset('assets/js/ong/show-event.js') }}"></script>
<script src="{{ asset('assets/js/ong/asistencia-functions.js') }}"></script>
<script>
    // Asegurar que cargarParticipantes se ejecute después de que todo esté cargado
    document.addEventListener('DOMContentLoaded', function() {
        console.log('✅ DOM completamente cargado');
        
        // Ejecutar la función específica para el card lateral
        setTimeout(function() {
            console.log('📋 Ejecutando cargarParticipantesCardONG desde DOMContentLoaded...');
            cargarParticipantesCardONG().catch(err => {
                console.error('❌ Error ejecutando cargarParticipantesCardONG:', err);
            });
        }, 500);
    });
</script>
<script>
    // Función para mostrar imagen en modal
    function mostrarImagenGaleria(url) {
        const modal = document.getElementById('modalImagenGaleria');
        const img = document.getElementById('imagenModalGaleria');
        if (modal && img) {
            img.src = url;
            $(modal).modal('show');
        }
    }

    // Función para cerrar modal de imagen
    function cerrarModalImagen() {
        const modal = document.getElementById('modalImagenGaleria');
        if (modal) {
            $(modal).modal('hide');
        }
    }

    // Cerrar modal al hacer clic fuera de la imagen
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('modalImagenGaleria');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal || e.target.classList.contains('modal-dialog')) {
                    cerrarModalImagen();
                }
            });
        }
    });
</script>

<!-- Modal de Lista de Participantes -->
<div id="modalListaParticipantes" class="modal fade" tabindex="-1" role="dialog" style="display: none;">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 8px 32px rgba(0,0,0,0.3);">
            <div class="modal-header" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); border-radius: 16px 16px 0 0; color: white;">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-users mr-2"></i>
                    Lista de Participantes que Asistieron
                </h5>
                <button type="button" class="close text-white" onclick="cerrarModalListaParticipantes()" aria-label="Close" style="opacity: 0.9;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <!-- Resumen -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h3 id="totalAsistieronLista" class="mb-0">0</h3>
                                <p class="mb-0">Total que Asistieron</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h3 id="totalRegistradosLista" class="mb-0">0</h3>
                                <p class="mb-0">Total Registrados</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de Exportación -->
                <div class="d-flex justify-content-end mb-3" style="gap: 0.5rem;">
                    <button class="btn btn-success" onclick="exportarListaParticipantesPDF()">
                        <i class="fas fa-file-pdf mr-2"></i> Exportar PDF
                    </button>
                    <button class="btn btn-primary" onclick="exportarListaParticipantesExcel()">
                        <i class="fas fa-file-excel mr-2"></i> Exportar Excel
                    </button>
                </div>

                <!-- Tabla de Participantes -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Participante</th>
                                <th>Email</th>
                                <th>Teléfono</th>
                                <th>Fecha de Inscripción</th>
                                <th>Fecha de Validación</th>
                                <th>Validado por</th>
                                <th>Modo de Validación</th>
                            </tr>
                        </thead>
                        <tbody id="tablaListaParticipantes">
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="sr-only">Cargando...</span>
                                    </div>
                                    <p class="mt-2 text-muted">Cargando participantes...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Control de Asistencia -->
<div id="modalControlAsistencia" class="modal fade" tabindex="-1" role="dialog" style="display: none;">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 8px 32px rgba(0,0,0,0.3);">
            <div class="modal-header" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); border-radius: 16px 16px 0 0; color: white;">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-clipboard-check mr-2"></i>
                    Control de Asistencia
                </h5>
                <button type="button" class="close text-white" onclick="cerrarModalControlAsistencia()" aria-label="Close" style="opacity: 0.9;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <!-- Resumen -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h3 id="totalParticipantes" class="mb-0">0</h3>
                                <p class="text-muted mb-0">Total Participantes</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h3 id="totalAsistieron" class="mb-0">0</h3>
                                <p class="mb-0">Asistieron</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center">
                                <h3 id="totalNoAsistieron" class="mb-0">0</h3>
                                <p class="mb-0">No Asistieron</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de Exportación -->
                <div class="d-flex justify-content-end mb-3" style="gap: 0.5rem;">
                    <button class="btn btn-success" onclick="exportarAsistenciaPDF()">
                        <i class="fas fa-file-pdf mr-2"></i> Exportar PDF
                    </button>
                    <button class="btn btn-primary" onclick="exportarAsistenciaExcel()">
                        <i class="fas fa-file-excel mr-2"></i> Exportar Excel
                    </button>
                </div>

                <!-- Tabla de Asistencia -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>Participante</th>
                                <th>Fecha de Inscripción</th>
                                <th>Estado de Asistencia</th>
                                <th>Validado por</th>
                                <th>Fecha de Validación</th>
                                <th>Observaciones</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaAsistencia">
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="sr-only">Cargando...</span>
                                    </div>
                                    <p class="mt-2 text-muted">Cargando datos de asistencia...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Usar la variable global del evento
function obtenerEventoId() {
    return window.eventoIdActual || document.getElementById('eventoId')?.value || window.location.pathname.split("/")[3];
}

// Función para abrir modal de control de asistencia
function abrirControlAsistencia() {
    const eventoId = obtenerEventoId();
    if (!eventoId) {
        alert('Error: No se pudo obtener el ID del evento');
        return;
    }
    
    $('#modalControlAsistencia').modal('show');
    cargarControlAsistencia();
}

// Función para cerrar modal
function cerrarModalControlAsistencia() {
    $('#modalControlAsistencia').modal('hide');
}

// Cargar datos de control de asistencia
async function cargarControlAsistencia() {
    const token = localStorage.getItem('token');
    if (!token) {
        alert('Debes iniciar sesión');
        return;
    }

    try {
        const url = `${API_BASE_URL}/api/eventos/${obtenerEventoId()}/control-asistencia`;
        const res = await fetch(url, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
            },
        });

        const data = await res.json();

        if (data.success) {
            console.log('✅ Control de asistencia cargado:', data);
            
            // Actualizar resumen en cards (si existen)
            const totalCard = document.getElementById('totalParticipantes');
            const asistieronCard = document.getElementById('totalAsistieron');
            const noAsistieronCard = document.getElementById('totalNoAsistieron');
            
            if (totalCard) totalCard.textContent = data.total || 0;
            if (asistieronCard) asistieronCard.textContent = data.asistieron || 0;
            if (noAsistieronCard) noAsistieronCard.textContent = data.no_asistieron || 0;

            // Actualizar tabla
            mostrarTablaAsistencia(data.participantes);
        } else {
            console.error('❌ Error cargando control de asistencia:', data.error);
            alert(data.error || 'Error al cargar control de asistencia');
        }
    } catch (error) {
        console.error('Error cargando control de asistencia:', error);
        alert('Error al cargar control de asistencia');
    }
}

// Función auxiliar para formatear fechas desde PostgreSQL
function formatearFechaInscripcion(fechaStr) {
    if (!fechaStr || fechaStr === '—' || fechaStr === '-') return '—';
    try {
        // Patrón para formato PostgreSQL: "2025-01-15 14:30:00"
        const match = fechaStr.match(/^(\d{4})-(\d{2})-(\d{2})[\sT](\d{2}):(\d{2}):(\d{2})(?:\.\d+)?(?:Z|[+-]\d{2}:\d{2})?$/);
        if (match) {
            const [, year, month, day, hour, minute, second] = match;
            const fechaLocal = new Date(
                parseInt(year, 10),
                parseInt(month, 10) - 1,
                parseInt(day, 10),
                parseInt(hour, 10),
                parseInt(minute, 10),
                parseInt(second || 0, 10)
            );
            if (!isNaN(fechaLocal.getTime())) {
                return fechaLocal.toLocaleString('es-ES', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }
        }
        // Si no coincide, intentar parsear directamente
        const fechaParseada = new Date(fechaStr);
        if (!isNaN(fechaParseada.getTime())) {
            return fechaParseada.toLocaleString('es-ES', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
        return fechaStr; // Devolver original si no se puede parsear
    } catch (error) {
        return fechaStr; // Devolver original en caso de error
    }
}

// Mostrar tabla de asistencia
function mostrarTablaAsistencia(participantes) {
    const tbody = document.getElementById('tablaAsistencia');
    tbody.innerHTML = '';

    if (!participantes || participantes.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-4 text-muted">
                    No hay participantes inscritos en este evento.
                </td>
            </tr>
        `;
        return;
    }

    participantes.forEach(participante => {
        const fechaInscripcionFormateada = formatearFechaInscripcion(participante.fecha_inscripcion);
        const fechaRegistroFormateada = formatearFechaInscripcion(participante.fecha_registro_asistencia);
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${participante.participante}</td>
            <td>${fechaInscripcionFormateada}</td>
            <td>${participante.estado_asistencia}</td>
            <td>${participante.validado_por || '—'}</td>
            <td>${fechaRegistroFormateada}</td>
            <td>${participante.observaciones || '—'}</td>
            <td>
                <button class="btn btn-sm btn-primary" onclick="editarAsistencia(${participante.id}, '${participante.tipo}', '${participante.estado_asistencia_raw}')">
                    <i class="fas fa-edit mr-1"></i> Editar
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

// Editar asistencia
async function editarAsistencia(participacionId, tipo, estadoActual) {
    const nuevoEstado = estadoActual === 'asistido' ? 'no_asistido' : 'asistido';
    const confirmacion = confirm(`¿Cambiar estado de asistencia a "${nuevoEstado === 'asistido' ? 'Asistió' : 'No asistió'}"?`);
    
    if (!confirmacion) return;

    const observaciones = prompt('Observaciones (opcional):') || '';

    const token = localStorage.getItem('token');
    if (!token) {
        alert('Debes iniciar sesión');
        return;
    }

    try {
        const url = `${API_BASE_URL}/api/participaciones/${participacionId}/modificar-asistencia`;
        const res = await fetch(url, {
            method: 'PUT',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                estado_asistencia: nuevoEstado,
                observaciones: observaciones,
            }),
        });

        const data = await res.json();

        if (data.success) {
            alert('Estado de asistencia actualizado correctamente');
            cargarControlAsistencia(); // Recargar tabla
        } else {
            alert(data.error || 'Error al modificar asistencia');
        }
    } catch (error) {
        console.error('Error modificando asistencia:', error);
        alert('Error al modificar asistencia');
    }
}

// Exportar a PDF
function exportarAsistenciaPDF() {
    const eventoId = obtenerEventoId();
    if (!eventoId) return;
    window.open(`${API_BASE_URL}/api/eventos/${obtenerEventoId()}/exportar-asistencia-pdf?token=${localStorage.getItem('token')}`, '_blank');
}

// Exportar a Excel
function exportarAsistenciaExcel() {
    const eventoId = obtenerEventoId();
    if (!eventoId) return;
    window.open(`${API_BASE_URL}/api/eventos/${obtenerEventoId()}/exportar-asistencia-excel?token=${localStorage.getItem('token')}`, '_blank');
}

// Mostrar botón de control de asistencia solo si el evento está en curso o finalizado
function verificarMostrarBotonControlAsistencia(estadoDinamico) {
    const btnControl = document.getElementById('btnControlAsistencia');
    
    // El botón de Control de Asistencias siempre está visible (consolida todos los controles)
    if (btnControl) btnControl.style.display = 'inline-block';
}

// Función para abrir modal de lista de participantes
function abrirListaParticipantes() {
    const eventoId = obtenerEventoId();
    if (!eventoId) {
        alert('Error: No se pudo obtener el ID del evento');
        return;
    }
    
    $('#modalListaParticipantes').modal('show');
    cargarListaParticipantes();
}

// Función para cerrar modal de lista de participantes
function cerrarModalListaParticipantes() {
    $('#modalListaParticipantes').modal('hide');
}

// Cargar lista de participantes que asistieron
async function cargarListaParticipantes() {
    const token = localStorage.getItem('token');
    if (!token) {
        alert('Debes iniciar sesión');
        return;
    }

    try {
        const url = `${API_BASE_URL}/api/eventos/${obtenerEventoId()}/control-asistencia`;
        const res = await fetch(url, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
            },
        });

        const data = await res.json();

        if (data.success) {
            // Filtrar solo los que asistieron
            const participantesAsistieron = data.participantes.filter(p => 
                p.estado_asistencia_raw === 'asistido' || 
                p.estado_asistencia.includes('✅') ||
                p.asistio === true
            );

            // Actualizar resumen
            document.getElementById('totalAsistieronLista').textContent = participantesAsistieron.length;
            document.getElementById('totalRegistradosLista').textContent = data.total || 0;

            // Actualizar tabla
            mostrarTablaListaParticipantes(participantesAsistieron);
        } else {
            alert(data.error || 'Error al cargar lista de participantes');
        }
    } catch (error) {
        console.error('Error cargando lista de participantes:', error);
        alert('Error al cargar lista de participantes');
    }
}

// Mostrar tabla de participantes que asistieron
function mostrarTablaListaParticipantes(participantes) {
    const tbody = document.getElementById('tablaListaParticipantes');
    tbody.innerHTML = '';

    if (!participantes || participantes.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-4 text-muted">
                    No hay participantes que hayan asistido a este evento.
                </td>
            </tr>
        `;
        return;
    }

    participantes.forEach((participante, index) => {
        const fechaInscripcionFormateada = formatearFechaInscripcion(participante.fecha_inscripcion);
        const fechaRegistroFormateada = formatearFechaInscripcion(participante.fecha_registro_asistencia);
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${index + 1}</td>
            <td>${participante.participante || '—'}</td>
            <td>${participante.email || '—'}</td>
            <td>${participante.telefono || '—'}</td>
            <td>${fechaInscripcionFormateada}</td>
            <td>${fechaRegistroFormateada}</td>
            <td>${participante.validado_por || '—'}</td>
            <td>${participante.modo_asistencia || '—'}</td>
        `;
        tbody.appendChild(row);
    });
}

// Exportar lista de participantes a PDF
function exportarListaParticipantesPDF() {
    const eventoId = obtenerEventoId();
    if (!eventoId) return;
    window.open(`${API_BASE_URL}/api/eventos/${obtenerEventoId()}/exportar-asistencia-pdf?token=${localStorage.getItem('token')}&solo_asistieron=true`, '_blank');
}

// Exportar lista de participantes a Excel
function exportarListaParticipantesExcel() {
    const eventoId = obtenerEventoId();
    if (!eventoId) return;
    window.open(`${API_BASE_URL}/api/eventos/${obtenerEventoId()}/exportar-asistencia-excel?token=${localStorage.getItem('token')}&solo_asistieron=true`, '_blank');
}

// ==========================================
// Gestión de Participantes
// ==========================================

let participantesData = [];
let participantesFiltrados = [];

// Abrir modal de gestión de participantes
async function abrirGestionParticipantes() {
    const modal = document.getElementById('modalGestionarParticipantes');
    if (modal && typeof $ !== 'undefined') {
        $(modal).modal('show');
    } else if (modal) {
        modal.style.display = 'block';
        modal.classList.add('show');
        document.body.classList.add('modal-open');
        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        backdrop.id = 'backdropGestionParticipantes';
        document.body.appendChild(backdrop);
    }
    
    // Cargar participantes
    await cargarParticipantes();
}

// Cerrar modal
function cerrarGestionParticipantes() {
    const modal = document.getElementById('modalGestionarParticipantes');
    if (modal && typeof $ !== 'undefined') {
        $(modal).modal('hide');
    } else if (modal) {
        modal.style.display = 'none';
        modal.classList.remove('show');
        document.body.classList.remove('modal-open');
        const backdrop = document.getElementById('backdropGestionParticipantes');
        if (backdrop) backdrop.remove();
    }
}

// Cargar participantes desde la API
async function cargarParticipantes() {
    const eventoId = obtenerEventoId();
    if (!eventoId) {
        alert('Error: No se pudo obtener el ID del evento');
        return;
    }
    
    const loading = document.getElementById('loadingParticipantes');
    const tabla = document.getElementById('tablaParticipantes');
    const sinDatos = document.getElementById('sinParticipantes');
    
    if (loading) loading.style.display = 'block';
    if (tabla) tabla.style.display = 'none';
    if (sinDatos) sinDatos.style.display = 'none';
    
    try {
        const token = localStorage.getItem('token');
        // Usar el endpoint de control-asistencia que incluye todos los tipos de participantes
        const response = await fetch(`${API_BASE_URL}/api/eventos/${eventoId}/control-asistencia`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        console.log('📊 Datos de control de asistencia:', data);
        
        if (data.success && data.participantes) {
            participantesData = data.participantes;
            participantesFiltrados = [...participantesData];
            
            console.log('✅ Participantes cargados:', {
                total: participantesData.length,
                registrados: participantesData.filter(p => p.tipo === 'registrado').length,
                voluntarios: participantesData.filter(p => p.tipo === 'voluntario').length,
                asistieron: participantesData.filter(p => p.estado_asistencia_raw === 'asistido' || p.asistio).length
            });
            
            actualizarEstadisticas();
            mostrarTablaParticipantes();
        } else {
            if (sinDatos) {
                sinDatos.style.display = 'block';
                sinDatos.innerHTML = `<i class="fas fa-info-circle mr-2"></i> ${data.message || 'No hay participantes inscritos en este evento.'}`;
            }
        }
    } catch (error) {
        console.error('❌ Error cargando participantes:', error);
        if (sinDatos) {
            sinDatos.style.display = 'block';
            sinDatos.className = 'alert alert-danger text-center';
            sinDatos.innerHTML = `<i class="fas fa-exclamation-circle mr-2"></i> Error al cargar los participantes. Por favor, intenta nuevamente.`;
        }
    } finally {
        if (loading) loading.style.display = 'none';
    }
}

// Actualizar estadísticas
function actualizarEstadisticas() {
    const total = participantesData.length;
    const asistieron = participantesData.filter(p => 
        p.estado_asistencia_raw === 'asistido' || 
        p.asistio === true ||
        p.estado_asistencia === '✅ Asistió'
    ).length;
    const noAsistieron = participantesData.filter(p => 
        p.estado_asistencia_raw === 'no_asistio' ||
        p.estado_asistencia === '❌ No asistió'
    ).length;
    const pendientes = total - asistieron - noAsistieron;
    const conComentarios = participantesData.filter(p => 
        p.comentario_asistencia && p.comentario_asistencia.trim() !== ''
    ).length;
    
    console.log('📊 Estadísticas calculadas:', { total, asistieron, noAsistieron, pendientes, conComentarios });
    
    // Actualizar cards de estadísticas
    const totalCard = document.getElementById('totalParticipantes');
    const asistieronCard = document.getElementById('totalAsistieron');
    const noAsistieronCard = document.getElementById('totalNoAsistieron');
    const pendientesCard = document.getElementById('totalPendientes');
    
    if (totalCard) totalCard.textContent = total;
    if (asistieronCard) asistieronCard.textContent = asistieron;
    if (noAsistieronCard) noAsistieronCard.textContent = noAsistieron;
    if (pendientesCard) pendientesCard.textContent = pendientes;
    
    // Actualizar resumen en tab de estadísticas (si existen)
    const resumenTotal = document.getElementById('resumenTotal');
    const resumenAsistieron = document.getElementById('resumenAsistieron');
    const resumenNoAsistieron = document.getElementById('resumenNoAsistieron');
    const resumenPendientes = document.getElementById('resumenPendientes');
    const resumenConComentarios = document.getElementById('resumenConComentarios');
    
    if (resumenTotal) resumenTotal.textContent = total;
    if (resumenAsistieron) resumenAsistieron.textContent = asistieron;
    if (resumenNoAsistieron) resumenNoAsistieron.textContent = noAsistieron;
    if (resumenPendientes) resumenPendientes.textContent = pendientes;
    if (resumenConComentarios) resumenConComentarios.textContent = conComentarios;
    
    // Actualizar barra de progreso (si existe)
    const progressBar = document.getElementById('progressAsistencia');
    const porcentajeSpan = document.getElementById('porcentajeAsistencia');
    
    if (progressBar && porcentajeSpan) {
        const porcentaje = total > 0 ? Math.round((asistieron / total) * 100) : 0;
        progressBar.style.width = porcentaje + '%';
        porcentajeSpan.textContent = porcentaje + '%';
    }
    
    // Cargar comentarios
    cargarComentarios();
}

// Filtrar participantes
function filtrarParticipantes() {
    const filtroAsistencia = document.getElementById('filtroEstadoAsistencia')?.value || 'todos';
    const filtroParticipacion = document.getElementById('filtroEstadoParticipacion')?.value || 'todos';
    const busqueda = document.getElementById('buscarParticipante')?.value.toLowerCase() || '';
    
    participantesFiltrados = participantesData.filter(p => {
        // Filtro de asistencia
        let cumpleAsistencia = true;
        if (filtroAsistencia !== 'todos') {
            if (filtroAsistencia === 'asistio') {
                cumpleAsistencia = p.estado_asistencia_raw === 'asistido' || 
                                   p.asistio === true ||
                                   p.estado_asistencia === '✅ Asistió';
            } else if (filtroAsistencia === 'no_asistio') {
                cumpleAsistencia = p.estado_asistencia_raw === 'no_asistio' ||
                                   p.estado_asistencia === '❌ No asistió';
            } else if (filtroAsistencia === 'pendiente') {
                cumpleAsistencia = p.estado_asistencia_raw === 'pendiente' || 
                                   (!p.asistio && p.estado_asistencia !== '✅ Asistió' && p.estado_asistencia !== '❌ No asistió');
            }
        }
        
        // Filtro de tipo (registrado/voluntario)
        let cumpleParticipacion = true;
        if (filtroParticipacion !== 'todos') {
            // El endpoint control-asistencia no tiene campo "estado" para voluntarios
            // Solo aplica filtro si es tipo registrado
            if (p.tipo === 'registrado') {
                cumpleParticipacion = filtroParticipacion === 'aprobada'; // La mayoría están aprobados
            } else if (p.tipo === 'voluntario') {
                cumpleParticipacion = filtroParticipacion === 'aprobada' || filtroParticipacion === 'todos';
            }
        }
        
        // Filtro de búsqueda
        let cumpleBusqueda = true;
        if (busqueda) {
            const nombreCompleto = (p.participante || '').toLowerCase();
            const emailTel = (p.email || p.telefono || '').toLowerCase();
            cumpleBusqueda = nombreCompleto.includes(busqueda) || emailTel.includes(busqueda);
        }
        
        return cumpleAsistencia && cumpleParticipacion && cumpleBusqueda;
    });
    
    console.log('🔍 Participantes filtrados:', participantesFiltrados.length);
    mostrarTablaParticipantes();
}

// Mostrar tabla de participantes
function mostrarTablaParticipantes() {
    const tabla = document.getElementById('tablaParticipantes');
    const sinDatos = document.getElementById('sinParticipantes');
    
    if (!tabla) return;
    
    if (participantesFiltrados.length === 0) {
        tabla.style.display = 'none';
        if (sinDatos) sinDatos.style.display = 'block';
        return;
    }
    
    if (sinDatos) sinDatos.style.display = 'none';
    tabla.style.display = 'block';
    
    let html = `
        <table class="table table-hover table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 20%;">Participante</th>
                    <th style="width: 10%;">Tipo</th>
                    <th style="width: 15%;">Email/Teléfono</th>
                    <th style="width: 10%;">Asistencia</th>
                    <th style="width: 12%;">Fecha Check-in</th>
                    <th style="width: 28%;">Comentarios</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    participantesFiltrados.forEach((p, index) => {
        const nombre = p.participante || 'N/A';
        const email = p.email || p.telefono || 'N/A';
        
        // Badge de tipo
        const tipoBadge = p.tipo === 'registrado' 
            ? '<span class="badge badge-primary"><i class="fas fa-user mr-1"></i>Registrado</span>'
            : '<span class="badge badge-info"><i class="fas fa-user-plus mr-1"></i>Voluntario</span>';
        
        // Badge de asistencia (usar el formato del endpoint)
        let asistenciaBadge = '<span class="badge badge-secondary">Pendiente</span>';
        if (p.estado_asistencia === '✅ Asistió' || p.estado_asistencia_raw === 'asistido' || p.asistio) {
            asistenciaBadge = '<span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i>Asistió</span>';
        } else if (p.estado_asistencia === '❌ No asistió' || p.estado_asistencia_raw === 'no_asistio') {
            asistenciaBadge = '<span class="badge badge-danger"><i class="fas fa-times-circle mr-1"></i>No asistió</span>';
        }
        
        // Fecha de check-in
        const fechaCheckin = p.fecha_registro_asistencia || '-';
        
        // Comentarios (mostrar ambos si existen)
        let comentarioHTML = '';
        const comentarioRegistro = p.comentario && p.comentario !== '-' ? p.comentario : null;
        const comentarioAsistencia = p.comentario_asistencia && p.comentario_asistencia !== '-' ? p.comentario_asistencia : null;
        
        if (comentarioRegistro || comentarioAsistencia) {
            if (comentarioRegistro) {
                const corto = comentarioRegistro.length > 30 ? comentarioRegistro.substring(0, 30) + '...' : comentarioRegistro;
                comentarioHTML += `<div class="mb-1"><small><strong>Registro:</strong> ${corto}</small></div>`;
            }
            if (comentarioAsistencia) {
                const corto = comentarioAsistencia.length > 30 ? comentarioAsistencia.substring(0, 30) + '...' : comentarioAsistencia;
                comentarioHTML += `<div><small><strong class="text-success">Asistencia:</strong> ${corto}</small></div>`;
            }
        } else {
            comentarioHTML = '<small class="text-muted">-</small>';
        }
        
        const tooltipText = [comentarioRegistro, comentarioAsistencia].filter(c => c).join(' | ');
        const comentarioTooltip = tooltipText ? `title="${tooltipText.replace(/"/g, '&quot;')}" data-toggle="tooltip"` : '';
        
        html += `
            <tr>
                <td class="text-center font-weight-bold">${index + 1}</td>
                <td>
                    <div class="d-flex align-items-center">
                        ${p.foto_perfil ? `
                            <img src="${p.foto_perfil}" alt="${nombre}" class="rounded-circle mr-2" style="width: 35px; height: 35px; object-fit: cover; border: 2px solid #00A36C;">
                        ` : `
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-2" style="width: 35px; height: 35px; font-weight: bold;">
                                ${nombre.charAt(0).toUpperCase()}
                            </div>
                        `}
                        <div>
                            <div class="font-weight-semibold">${nombre}</div>
                        </div>
                    </div>
                </td>
                <td class="text-center">${tipoBadge}</td>
                <td><small>${email}</small></td>
                <td class="text-center">${asistenciaBadge}</td>
                <td class="text-center"><small>${fechaCheckin}</small></td>
                <td ${comentarioTooltip}>
                    ${comentarioHTML}
                </td>
            </tr>
        `;
    });
    
    html += `
            </tbody>
        </table>
        <p class="text-muted text-center mt-3">
            <small>
                <i class="fas fa-info-circle mr-1"></i>
                Mostrando ${participantesFiltrados.length} de ${participantesData.length} participantes
                (${participantesData.filter(p => p.tipo === 'registrado').length} registrados, 
                ${participantesData.filter(p => p.tipo === 'voluntario').length} voluntarios)
            </small>
        </p>
    `;
    
    tabla.innerHTML = html;
    
    // Activar tooltips
    if (typeof $ !== 'undefined' && $.fn.tooltip) {
        $('[data-toggle="tooltip"]').tooltip();
    }
}

// Exportar participantes a Excel
function exportarParticipantes() {
    const eventoId = obtenerEventoId();
    if (!eventoId) return;
    window.open(`${API_BASE_URL}/api/eventos/${obtenerEventoId()}/exportar-participantes-completo?token=${localStorage.getItem('token')}`, '_blank');
}

// Función específica para cargar participantes en el card de la página principal
async function cargarParticipantesCardONG() {
    console.log('📋 Cargando participantes en card (desde botón Actualizar)...');
    
    const container = document.getElementById('participantesContainer');
    if (!container) {
        console.error('❌ Container participantesContainer no encontrado');
        return;
    }

    const token = localStorage.getItem('token');
    if (!token) {
        container.innerHTML = `
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Debes iniciar sesión para ver los participantes.
            </div>
        `;
        return;
    }

    // Obtener eventoId de la URL
    const eventoId = window.location.pathname.split("/")[3];
    if (!eventoId) {
        console.error('❌ No se pudo obtener el ID del evento');
        container.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle mr-2"></i>
                Error: No se pudo obtener el ID del evento.
            </div>
        `;
        return;
    }

    console.log('📋 Iniciando carga de participantes para evento:', eventoId);
    console.log('🌐 API_BASE_URL:', typeof API_BASE_URL !== 'undefined' ? API_BASE_URL : 'NO DEFINIDO');

    try {
        container.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border" role="status" style="color: #00A36C; width: 3rem; height: 3rem;">
                    <span class="sr-only">Cargando...</span>
                </div>
                <p class="mt-3" style="color: #333333; font-weight: 500;">Cargando participantes...</p>
            </div>
        `;

        const url = `${API_BASE_URL}/api/participaciones/evento/${eventoId}`;
        console.log('🌐 URL de petición:', url);

        const res = await fetch(url, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        console.log('📡 Status de respuesta:', res.status);

        if (!res.ok) {
            const errorText = await res.text();
            console.error('❌ Error HTTP:', res.status, errorText);
            throw new Error(`Error ${res.status}: ${errorText}`);
        }

        const data = await res.json();
        console.log('✅ Respuesta del servidor:', data);
        console.log('📊 Total participantes:', data.participantes?.length || 0);

        if (!data.success) {
            console.error('❌ Error en respuesta:', data.error);
            container.innerHTML = `
                <div class="alert alert-warning" style="border-radius: 8px; padding: 1rem;">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>Error:</strong> ${data.error || 'Error al cargar participantes'}
                </div>
            `;
            return;
        }

        if (!data.participantes || data.participantes.length === 0) {
            console.log('ℹ️ No hay participantes en este evento');
            container.innerHTML = `
                <div class="text-center py-5" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); border-radius: 12px; padding: 3rem 2rem;">
                    <div style="background: rgba(255, 255, 255, 0.15); width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; backdrop-filter: blur(10px);">
                        <i class="far fa-users" style="font-size: 2.5rem; color: white;"></i>
                    </div>
                    <h5 style="color: white; font-weight: 600; margin-bottom: 0.5rem;">No hay participantes inscritos aún</h5>
                    <p style="color: rgba(255, 255, 255, 0.9); margin: 0; font-size: 0.95rem;">Las solicitudes de participación aparecerán aquí cuando los usuarios se inscriban</p>
                </div>
            `;
            return;
        }
        
        console.log('📊 Total participantes recibidos:', data.participantes.length);
        console.log('📊 Participantes registrados:', data.participantes.filter(p => p.tipo === 'registrado').length);
        console.log('📊 Participantes no registrados:', data.participantes.filter(p => p.tipo === 'no_registrado').length);
        console.log('🎨 Iniciando renderizado de participantes...');

        // Función helper para parsear fechas
        function parsearFechaLocal(fechaStr) {
            if (!fechaStr) return null;
            try {
                if (typeof fechaStr === 'string') {
                    fechaStr = fechaStr.trim();
                    const match = fechaStr.match(/^(\d{4})-(\d{2})-(\d{2})[\sT](\d{2}):(\d{2}):(\d{2})/);
                    if (match) {
                        const [, year, month, day, hour, minute, second] = match;
                        return new Date(
                            parseInt(year, 10),
                            parseInt(month, 10) - 1,
                            parseInt(day, 10),
                            parseInt(hour, 10),
                            parseInt(minute, 10),
                            parseInt(second || 0, 10)
                        );
                    }
                }
                return new Date(fechaStr);
            } catch (error) {
                return new Date(fechaStr);
            }
        }
        
        // Crear grid de participantes
        let html = '<div class="row">';
        data.participantes.forEach((participante, index) => {
            console.log(`🎨 Renderizando participante ${index + 1}:`, {
                nombre: participante.nombre,
                tipo: participante.tipo,
                estado: participante.estado
            });
            
            const fechaObj = parsearFechaLocal(participante.fecha_inscripcion);
            const fechaInscripcion = fechaObj ? fechaObj.toLocaleDateString('es-ES', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            }) : 'N/A';

            let estadoBadge = '';
            if (participante.estado === 'aprobada') {
                const asistioBadge = participante.asistio 
                    ? '<span class="badge" style="background: #28a745; color: white; padding: 0.4em 0.8em; border-radius: 20px; font-weight: 600; font-size: 0.8rem; box-shadow: 0 2px 4px rgba(40, 167, 69, 0.2);"><i class="fas fa-check-circle mr-1"></i>Asistió</span>'
                    : '<span class="badge" style="background: #ffc107; color: #333; padding: 0.4em 0.8em; border-radius: 20px; font-weight: 600; font-size: 0.8rem; box-shadow: 0 2px 4px rgba(255, 193, 7, 0.2);"><i class="fas fa-clock mr-1"></i>Sin asistir</span>';
                estadoBadge = '<span class="badge" style="background: #00A36C; color: white; padding: 0.4em 0.8em; border-radius: 20px; font-weight: 600; font-size: 0.8rem; box-shadow: 0 2px 4px rgba(0, 163, 108, 0.2);"><i class="fas fa-check mr-1"></i>Aprobada</span>' + asistioBadge;
            } else if (participante.estado === 'rechazada') {
                estadoBadge = '<span class="badge" style="background: #dc3545; color: white; padding: 0.4em 0.8em; border-radius: 20px; font-weight: 600; font-size: 0.8rem; box-shadow: 0 2px 4px rgba(220, 53, 69, 0.2);"><i class="fas fa-times mr-1"></i>Rechazada</span>';
            } else {
                estadoBadge = '<span class="badge" style="background: #ffc107; color: #333333; padding: 0.4em 0.8em; border-radius: 20px; font-weight: 600; font-size: 0.8rem; box-shadow: 0 2px 4px rgba(255, 193, 7, 0.2);"><i class="fas fa-hourglass-half mr-1"></i>Pendiente</span>';
            }

            const nombreParticipante = participante.nombre || participante.nombres || 'Sin nombre';
            const inicial = nombreParticipante.charAt(0).toUpperCase();
            const fotoPerfil = participante.foto_perfil || null;
            const esNoRegistrado = participante.tipo === 'no_registrado' || participante.tipo_usuario === 'Voluntario';
            const tipoBadge = esNoRegistrado 
                ? '<span class="badge" style="background: #17a2b8; color: white; padding: 0.35em 0.7em; border-radius: 15px; font-size: 0.75rem; font-weight: 600; box-shadow: 0 2px 4px rgba(23, 162, 184, 0.2);"><i class="fas fa-user-clock mr-1"></i> Voluntario</span>'
                : '<span class="badge" style="background: #007bff; color: white; padding: 0.35em 0.7em; border-radius: 15px; font-size: 0.75rem; font-weight: 600; box-shadow: 0 2px 4px rgba(0, 123, 255, 0.2);"><i class="fas fa-user mr-1"></i> Externo</span>';

            html += `
                <div class="col-md-6 col-lg-4 mb-4 participante-card" style="animation-delay: ${index * 0.1}s;">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; border: 1px solid #E8E8E8; transition: all 0.3s ease; overflow: hidden;" onmouseover="this.style.transform='translateY(-6px)'; this.style.boxShadow='0 12px 24px rgba(12, 43, 68, 0.15)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.08)';">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start mb-3">
                                ${fotoPerfil ? `
                                    <div class="mr-3" style="flex-shrink: 0; width: 70px; height: 70px; position: relative;">
                                        <img src="${fotoPerfil}" alt="${nombreParticipante}" 
                                             id="avatar-img-${participante.id}"
                                             style="width: 70px; height: 70px; border-radius: 50%; object-fit: cover; object-position: center; border: 4px solid #00A36C; box-shadow: 0 4px 12px rgba(0, 163, 108, 0.3); display: block; background: #f8f9fa;" 
                                             onerror="this.style.display='none'; const ph = document.getElementById('avatar-placeholder-${participante.id}'); if(ph) ph.style.display='flex';">
                                        <div id="avatar-placeholder-${participante.id}" class="d-flex align-items-center justify-content-center" 
                                             style="width: 70px; height: 70px; border-radius: 50%; font-weight: 700; font-size: 1.8rem; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); color: white; box-shadow: 0 4px 12px rgba(0, 163, 108, 0.3); border: 4px solid #00A36C; display: none; position: absolute; top: 0; left: 0;">
                                            ${inicial}
                                        </div>
                                    </div>
                                ` : `
                                    <div class="mr-3" style="flex-shrink: 0;">
                                        <div class="d-flex align-items-center justify-content-center" 
                                             style="width: 70px; height: 70px; border-radius: 50%; font-weight: 700; font-size: 1.8rem; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); color: white; box-shadow: 0 4px 12px rgba(0, 163, 108, 0.3); border: 4px solid #00A36C;">
                                            ${inicial}
                                        </div>
                                    </div>
                                `}
                                <div class="flex-grow-1" style="min-width: 0;">
                                    <div class="d-flex align-items-start justify-content-between mb-2 flex-wrap" style="gap: 0.5rem;">
                                        <div style="flex: 1; min-width: 0;">
                                            <h6 class="mb-1" style="color: #0C2B44; font-weight: 700; font-size: 1.05rem; line-height: 1.3; word-wrap: break-word;">${nombreParticipante}</h6>
                                            <div class="mt-1">
                                                ${tipoBadge}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <div style="color: #555; font-size: 0.875rem; margin-bottom: 0.25rem;">
                                            <i class="far fa-envelope mr-1" style="color: #00A36C; width: 16px;"></i> 
                                            <span style="word-break: break-word;">${participante.correo || participante.email || 'No disponible'}</span>
                                        </div>
                                        ${(participante.telefono || participante.phone_number) ? `
                                            <div style="color: #555; font-size: 0.875rem;">
                                                <i class="far fa-phone mr-1" style="color: #00A36C; width: 16px;"></i> 
                                                ${participante.telefono || participante.phone_number}
                                            </div>
                                        ` : ''}
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3" style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                                ${estadoBadge}
                            </div>
                            <div class="pt-3" style="border-top: 1px solid #F0F0F0;">
                                <div style="color: #666; font-size: 0.8rem; margin-bottom: 0.5rem;">
                                    <i class="far fa-clock mr-1" style="color: #00A36C;"></i> 
                                    <span>${fechaInscripcion}</span>
                                </div>
                                ${participante.comentario ? `
                                    <div style="background: #f8f9fa; padding: 0.75rem; border-radius: 8px; border-left: 3px solid #00A36C; margin-top: 0.5rem;">
                                        <div style="color: #495057; font-size: 0.8rem; line-height: 1.4;">
                                            <i class="fas fa-comment mr-1" style="color: #00A36C;"></i> 
                                            <strong>Comentario:</strong> ${participante.comentario}
                                        </div>
                                    </div>
                                ` : ''}
                            </div>
                            ${(!esNoRegistrado && participante.estado === 'pendiente') ? `
                                <div class="d-flex mt-3" style="gap: 0.5rem;">
                                    <button class="btn btn-sm flex-fill" onclick="aprobarParticipacion(${participante.id})" title="Aprobar" style="background: #00A36C; color: white; border: none; border-radius: 8px; font-weight: 600; padding: 0.5rem; transition: all 0.2s;" onmouseover="this.style.background='#008a5a'; this.style.transform='scale(1.02)';" onmouseout="this.style.background='#00A36C'; this.style.transform='scale(1)';">
                                        <i class="far fa-check-circle mr-1"></i> Aprobar
                                    </button>
                                    <button class="btn btn-sm flex-fill" onclick="rechazarParticipacion(${participante.id})" title="Rechazar" style="background: #dc3545; color: white; border: none; border-radius: 8px; font-weight: 600; padding: 0.5rem; transition: all 0.2s;" onmouseover="this.style.background='#c82333'; this.style.transform='scale(1.02)';" onmouseout="this.style.background='#dc3545'; this.style.transform='scale(1)';">
                                        <i class="far fa-times-circle mr-1"></i> Rechazar
                                    </button>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        const totalRegistrados = data.participantes.filter(p => p.tipo === 'registrado').length;
        const totalVoluntarios = data.participantes.filter(p => p.tipo === 'no_registrado').length;
        html += `
            <div class="mt-4 text-center">
                <div class="d-inline-flex align-items-center" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); padding: 0.75rem 1.5rem; border-radius: 25px; box-shadow: 0 4px 12px rgba(12, 43, 68, 0.2);">
                    <i class="fas fa-users mr-2" style="color: white; font-size: 1.1rem;"></i>
                    <span style="color: white; font-weight: 600; font-size: 1rem;">
                        Total: ${data.count || data.participantes.length} participante(s)
                    </span>
                    ${totalRegistrados > 0 ? `
                        <span class="ml-3" style="color: rgba(255, 255, 255, 0.9); font-size: 0.9rem;">
                            <i class="fas fa-user mr-1"></i> ${totalRegistrados} Externo${totalRegistrados !== 1 ? 's' : ''}
                        </span>
                    ` : ''}
                    ${totalVoluntarios > 0 ? `
                        <span class="ml-3" style="color: rgba(255, 255, 255, 0.9); font-size: 0.9rem;">
                            <i class="fas fa-user-clock mr-1"></i> ${totalVoluntarios} Voluntario${totalVoluntarios !== 1 ? 's' : ''}
                        </span>
                    ` : ''}
                </div>
            </div>
        `;

        console.log('🎨 HTML generado, longitud:', html.length);
        console.log('🎨 Insertando HTML en container:', container.id);
        container.innerHTML = html;
        console.log('✅ HTML insertado correctamente');
        
        // Agregar animación de entrada
        setTimeout(() => {
            const cards = container.querySelectorAll('.participante-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease-out';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        }, 100);

    } catch (error) {
        console.error('❌ Error completo cargando participantes:', error);
        console.error('❌ Stack trace:', error.stack);
        console.error('❌ Mensaje:', error.message);
        
        if (container) {
            container.innerHTML = `
                <div class="alert alert-danger" style="border-radius: 8px; padding: 1rem;">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>Error al cargar participantes</strong>
                    <p class="mb-0 mt-2"><small>${error.message || 'Error de conexión. Por favor, verifica tu conexión e intenta nuevamente.'}</small></p>
                    <button class="btn btn-sm btn-secondary mt-2" onclick="cargarParticipantesCardONG()">
                        <i class="fas fa-redo mr-1"></i> Reintentar
                    </button>
                </div>
            `;
        }
    }
}

// Cargar comentarios de asistencia
function cargarComentarios() {
    const listaComentarios = document.getElementById('listaComentarios');
    const sinComentarios = document.getElementById('sinComentarios');
    
    if (!listaComentarios) {
        console.warn('⚠️ Elemento listaComentarios no encontrado');
        return;
    }
    
    console.log('📝 Cargando comentarios de participantes:', participantesData.length);
    
    const participantesConComentarios = participantesData.filter(p => {
        // Buscar cualquier tipo de comentario
        const tieneComentarioRegistro = p.comentario && p.comentario.trim() !== '' && p.comentario !== '-';
        const tieneComentarioAsistencia = p.comentario_asistencia && p.comentario_asistencia.trim() !== '' && p.comentario_asistencia !== '-';
        return tieneComentarioRegistro || tieneComentarioAsistencia;
    });
    
    console.log('💬 Participantes con comentarios:', {
        total: participantesConComentarios.length,
        conComentarioRegistro: participantesData.filter(p => p.comentario && p.comentario !== '-').length,
        conComentarioAsistencia: participantesData.filter(p => p.comentario_asistencia && p.comentario_asistencia !== '-').length
    });
    
    if (participantesConComentarios.length === 0) {
        listaComentarios.innerHTML = '';
        if (sinComentarios) sinComentarios.style.display = 'block';
        return;
    }
    
    if (sinComentarios) sinComentarios.style.display = 'none';
    
    let html = '';
    participantesConComentarios.forEach((p, index) => {
        const nombre = p.participante || 'Usuario desconocido';
        const fechaCheckinRaw = p.fecha_registro_asistencia || p.fecha_inscripcion;
        const fechaCheckin = fechaCheckinRaw ? formatearFechaInscripcion(fechaCheckinRaw) : 'Fecha no disponible';
        
        const asistio = p.estado_asistencia === '✅ Asistió' || 
                       p.estado_asistencia_raw === 'asistido' || 
                       p.asistio === true;
        const badgeColor = asistio ? 'success' : 'warning';
        const badgeText = asistio ? 'Asistió' : 'Pendiente';
        const badgeIcon = asistio ? 'fa-check-circle' : 'fa-clock';
        
        // Preparar comentarios
        const comentarioRegistro = p.comentario && p.comentario !== '-' ? p.comentario : null;
        const comentarioAsistencia = p.comentario_asistencia && p.comentario_asistencia !== '-' ? p.comentario_asistencia : null;
        
        html += `
            <div class="card mb-3 border-0 shadow-sm" style="border-left: 4px solid ${asistio ? '#28a745' : '#ffc107'} !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="d-flex align-items-center">
                            ${p.foto_perfil ? `
                                <img src="${p.foto_perfil}" alt="${nombre}" class="rounded-circle mr-3" style="width: 40px; height: 40px; object-fit: cover; border: 2px solid ${asistio ? '#28a745' : '#ffc107'};">
                            ` : `
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-3" style="width: 40px; height: 40px; font-weight: bold; font-size: 1.1rem;">
                                    ${nombre.charAt(0).toUpperCase()}
                                </div>
                            `}
                            <div>
                                <h6 class="mb-0 font-weight-bold">${nombre}</h6>
                                <small class="text-muted">
                                    <i class="far fa-clock mr-1"></i> ${fechaCheckin}
                                </small>
                                ${p.tipo ? `<br><span class="badge badge-${p.tipo === 'registrado' ? 'primary' : 'info'} badge-sm mt-1">${p.tipo === 'registrado' ? 'Registrado' : 'Voluntario'}</span>` : ''}
                            </div>
                        </div>
                        <span class="badge badge-${badgeColor}">
                            <i class="fas ${badgeIcon} mr-1"></i> ${badgeText}
                        </span>
                    </div>
                    <div class="pl-5">
                        ${comentarioRegistro ? `
                            <div class="mb-2 p-2" style="background: #f8f9fa; border-radius: 6px;">
                                <small class="text-muted"><i class="fas fa-user-edit mr-1"></i> <strong>Al inscribirse:</strong></small>
                                <p class="mb-0 text-dark mt-1" style="font-size: 0.9rem; line-height: 1.5;">
                                    ${comentarioRegistro}
                                </p>
                            </div>
                        ` : ''}
                        ${comentarioAsistencia ? `
                            <div class="p-2" style="background: #d4edda; border-radius: 6px;">
                                <small class="text-success"><i class="fas fa-clipboard-check mr-1"></i> <strong>Al registrar asistencia:</strong></small>
                                <p class="mb-0 text-dark mt-1" style="font-size: 0.9rem; line-height: 1.5;">
                                    ${comentarioAsistencia}
                                </p>
                            </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
    });
    
    listaComentarios.innerHTML = html;
}
</script>
@stop
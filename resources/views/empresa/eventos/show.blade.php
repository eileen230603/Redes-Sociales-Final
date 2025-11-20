@extends('layouts.adminlte-empresa')

@section('page_title', 'Detalle del Evento')

@section('content_body')
<input type="hidden" id="eventoId" value="{{ request()->id }}">
<div class="container-fluid px-0">
    <!-- Banner Superior con Imagen Principal -->
    <div id="eventBanner" class="position-relative" style="height: 400px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); overflow: hidden;">
        <div id="bannerImage" class="w-100 h-100" style="background-size: cover; background-position: center; opacity: 0.3;"></div>
        <div class="position-absolute" style="top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(to bottom, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0.6) 100%);"></div>
        <div class="position-absolute" style="bottom: 0; left: 0; right: 0; padding: 2rem; color: white;">
            <div class="container">
                <h1 id="titulo" class="mb-2" style="font-size: 2.5rem; font-weight: 700; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);"></h1>
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <span id="tipoEventoBadge" class="badge badge-light" style="font-size: 0.9rem; padding: 0.5em 1em;"></span>
                    <span id="estadoBadge" class="badge" style="font-size: 0.9rem; padding: 0.5em 1em;"></span>
                    <span class="badge badge-success" style="font-size: 0.9rem; padding: 0.5em 1em;">
                        <i class="fas fa-handshake mr-1"></i> Patrocinador
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <!-- Botones de Acción (Empresa) -->
        <div class="d-flex justify-content-end mb-4 gap-2 flex-wrap">
            <a href="/empresa/eventos" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left mr-2"></i> Volver
            </a>
            <div class="btn btn-outline-secondary" style="border-radius: 50px; cursor: default;">
                <i class="fas fa-heart text-danger mr-2"></i>
                <span id="contadorReaccionesEmpresa">0</span> reacciones
            </div>
        </div>

        <div class="row">
            <!-- Columna Principal -->
            <div class="col-lg-8">
                <!-- Descripción -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                    <div class="card-body p-4">
                        <h4 class="mb-3" style="color: #2c3e50; font-weight: 600;">
                            <i class="fas fa-align-left mr-2 text-primary"></i> Descripción
                        </h4>
                        <p id="descripcion" class="mb-0" style="color: #6c757d; line-height: 1.8; font-size: 1rem;"></p>
                    </div>
                </div>

                <!-- Información del Evento -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                    <div class="card-body p-4">
                        <h4 class="mb-4" style="color: #2c3e50; font-weight: 600;">
                            <i class="fas fa-info-circle mr-2 text-primary"></i> Información del Evento
                        </h4>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-calendar-alt text-primary mr-3 mt-1" style="font-size: 1.2rem;"></i>
                                    <div>
                                        <h6 class="mb-1" style="color: #495057; font-weight: 600;">Fecha de Inicio</h6>
                                        <p id="fecha_inicio" class="mb-0 text-muted"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-calendar-check text-primary mr-3 mt-1" style="font-size: 1.2rem;"></i>
                                    <div>
                                        <h6 class="mb-1" style="color: #495057; font-weight: 600;">Fecha de Fin</h6>
                                        <p id="fecha_fin" class="mb-0 text-muted"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-clock text-primary mr-3 mt-1" style="font-size: 1.2rem;"></i>
                                    <div>
                                        <h6 class="mb-1" style="color: #495057; font-weight: 600;">Límite de Inscripción</h6>
                                        <p id="fecha_limite_inscripcion" class="mb-0 text-muted"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-users text-primary mr-3 mt-1" style="font-size: 1.2rem;"></i>
                                    <div>
                                        <h6 class="mb-1" style="color: #495057; font-weight: 600;">Capacidad Máxima</h6>
                                        <p id="capacidad_maxima" class="mb-0 text-muted"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3" id="fechaFinalizacionContainer" style="display: none;">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-flag-checkered text-info mr-3 mt-1" style="font-size: 1.2rem;"></i>
                                    <div>
                                        <h6 class="mb-1" style="color: #495057; font-weight: 600;">Fecha de Finalización</h6>
                                        <p id="fecha_finalizacion" class="mb-0 text-muted"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ubicación -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                    <div class="card-body p-4">
                        <h4 class="mb-3" style="color: #2c3e50; font-weight: 600;">
                            <i class="fas fa-map-marker-alt mr-2 text-primary"></i> Ubicación
                        </h4>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h6 class="mb-1" style="color: #495057; font-weight: 600;">Ciudad</h6>
                                <p id="ciudad" class="mb-0 text-muted"></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="mb-1" style="color: #495057; font-weight: 600;">Dirección</h6>
                                <p id="direccion" class="mb-0 text-muted"></p>
                            </div>
                        </div>
                        <div id="mapContainer" class="mt-3" style="height: 300px; border-radius: 8px; overflow: hidden; display: none;">
                            <!-- Mapa se cargará aquí -->
                        </div>
                    </div>
                </div>

                <!-- Galería de Imágenes -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                    <div class="card-body p-4">
                        <h4 class="mb-4" style="color: #2c3e50; font-weight: 600;">
                            <i class="fas fa-images mr-2 text-primary"></i> Galería de Imágenes
                        </h4>
                        <div id="imagenes" class="row"></div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Información Rápida -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                    <div class="card-body p-4">
                        <h5 class="mb-3" style="color: #2c3e50; font-weight: 600;">
                            <i class="fas fa-info-circle mr-2 text-primary"></i> Información Rápida
                        </h5>
                        <div class="mb-3 pb-3 border-bottom">
                            <small class="text-muted d-block mb-1">Estado</small>
                            <span id="estadoSidebar" class="badge"></span>
                        </div>
                        <div class="mb-3 pb-3 border-bottom">
                            <small class="text-muted d-block mb-1">Tipo de Evento</small>
                            <span id="tipoEventoSidebar" class="text-dark font-weight-bold"></span>
                        </div>
                        <div class="mb-3 pb-3 border-bottom">
                            <small class="text-muted d-block mb-1">Capacidad</small>
                            <span id="capacidadSidebar" class="text-dark font-weight-bold"></span>
                        </div>
                        <div id="inscripcionAbiertaContainer" class="mb-3">
                            <small class="text-muted d-block mb-1">Inscripción</small>
                            <span id="inscripcionAbierta" class="badge"></span>
                        </div>
                    </div>
                </div>

                <!-- Patrocinadores -->
                <div id="patrocinadoresCard" class="card border-0 shadow-sm mb-4" style="border-radius: 12px; display: none;">
                    <div class="card-body p-4">
                        <h5 class="mb-3" style="color: #2c3e50; font-weight: 600;">
                            <i class="fas fa-handshake mr-2 text-primary"></i> Patrocinadores
                        </h5>
                        <div id="patrocinadores" class="d-flex flex-wrap gap-2"></div>
                    </div>
                </div>

                <!-- Invitados -->
                <div id="invitadosCard" class="card border-0 shadow-sm mb-4" style="border-radius: 12px; display: none;">
                    <div class="card-body p-4">
                        <h5 class="mb-3" style="color: #2c3e50; font-weight: 600;">
                            <i class="fas fa-user-friends mr-2 text-primary"></i> Invitados Especiales
                        </h5>
                        <div id="invitados" class="d-flex flex-wrap gap-2"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    body {
        background-color: #f8f9fa;
    }
    
    .card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
    }
    
    #eventBanner {
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    
    .badge {
        font-weight: 500;
        padding: 0.5em 0.8em;
    }
    
    /* Estilos para galería de imágenes */
    .gallery-item {
        position: relative;
        overflow: hidden;
        border-radius: 8px;
        cursor: pointer;
        transition: transform 0.3s;
    }
    
    .gallery-item:hover {
        transform: scale(1.05);
    }
    
    .gallery-item img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }
</style>
@endsection

@section('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="{{ asset('assets/js/config.js') }}"></script>
<script src="{{ asset('assets/js/empresa/show-event.js') }}"></script>
@endsection


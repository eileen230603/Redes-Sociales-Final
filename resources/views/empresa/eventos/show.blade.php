@extends('layouts.adminlte-empresa')

@section('page_title', 'Detalle del Evento')

@section('content_body')
<input type="hidden" id="eventoId" value="{{ request()->id }}">
<div class="container-fluid px-0">
    <!-- Banner Superior con Imagen Principal - Mejorado -->
    <div id="eventBanner" class="position-relative" style="height: 450px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); overflow: hidden; border-radius: 0 0 24px 24px;">
        <div id="bannerImage" class="w-100 h-100" style="background-size: cover; background-position: center; opacity: 0.25; transition: transform 0.5s ease;"></div>
        <div class="position-absolute" style="top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(to bottom, rgba(102, 126, 234, 0.4) 0%, rgba(118, 75, 162, 0.7) 100%);"></div>
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
                            <span class="badge" style="font-size: 0.95rem; padding: 0.6em 1.2em; background: rgba(40, 167, 69, 0.3); backdrop-filter: blur(10px); border-radius: 50px; font-weight: 500;">
                        <i class="fas fa-handshake mr-1"></i> Patrocinador
                    </span>
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
                <div class="modal-header" style="border-bottom: 1px solid #F5F5F5; padding: 1.5rem;">
                    <h5 class="modal-title" style="color: #2c3e50; font-weight: 700; font-size: 1.25rem;">Compartir</h5>
                    <button type="button" class="close" onclick="cerrarModalCompartir()" aria-label="Close" style="border: none; background: none; font-size: 1.5rem; color: #333; opacity: 0.5; cursor: pointer;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="padding: 2rem;">
                    <div class="row text-center">
                        <!-- Copiar enlace -->
                        <div class="col-6 mb-4">
                            <button onclick="copiarEnlace()" class="btn btn-link p-0" style="text-decoration: none; border: none; background: none; width: 100%;">
                                <div style="width: 80px; height: 80px; background: #F5F5F5; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(0,0,0,0.1);" onmouseover="this.style.background='#E9ECEF'; this.style.transform='scale(1.1)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)'" onmouseout="this.style.background='#F5F5F5'; this.style.transform='scale(1)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.1)'">
                                    <i class="fas fa-link" style="font-size: 2rem; color: #2c3e50;"></i>
                                </div>
                                <span style="color: #333; font-size: 0.9rem; font-weight: 600;">Copiar enlace</span>
                            </button>
                        </div>
                        <!-- QR Code -->
                        <div class="col-6 mb-4">
                            <button onclick="mostrarQR()" class="btn btn-link p-0" style="text-decoration: none; border: none; background: none; width: 100%;">
                                <div style="width: 80px; height: 80px; background: #667eea; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(102,126,234,0.3);" onmouseover="this.style.background='#764ba2'; this.style.transform='scale(1.1)'; this.style.boxShadow='0 4px 12px rgba(118,75,162,0.4)'" onmouseout="this.style.background='#667eea'; this.style.transform='scale(1)'; this.style.boxShadow='0 2px 8px rgba(102,126,234,0.3)'">
                                    <i class="fas fa-qrcode" style="font-size: 2rem; color: white;"></i>
                                </div>
                                <span style="color: #333; font-size: 0.9rem; font-weight: 600;">Código QR</span>
                            </button>
                        </div>
                    </div>
                    <!-- Contenedor para el QR -->
                    <div id="qrContainer" style="display: none; margin-top: 1.5rem;">
                        <div class="text-center">
                            <div id="qrcode" style="display: inline-block; padding: 1rem; background: white; border-radius: 12px; margin-bottom: 1rem;"></div>
                            <p style="color: #333; font-size: 0.9rem; margin: 0;">Escanea este código para acceder al evento</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <!-- Botones de Acción (Empresa) - Mejorados -->
        <div class="d-flex justify-content-end mb-4 flex-wrap" style="gap: 0.75rem;">
            <a href="/empresa/eventos" class="btn btn-outline-secondary" style="border-radius: 10px; padding: 0.6rem 1.5rem; font-weight: 500; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <i class="fas fa-arrow-left mr-2"></i> Volver
            </a>
            <div class="btn btn-outline-secondary d-flex align-items-center" style="border-radius: 10px; padding: 0.6rem 1.5rem; cursor: default; font-weight: 500; box-shadow: 0 2px 8px rgba(220, 53, 69, 0.2);">
                <i class="fas fa-heart text-danger mr-2"></i>
                <span id="contadorReaccionesEmpresa">0</span> reacciones
            </div>
            <button class="btn btn-outline-primary d-flex align-items-center" id="btnCompartir" style="border-radius: 10px; padding: 0.6rem 1.5rem; font-weight: 500; box-shadow: 0 2px 8px rgba(0, 123, 255, 0.2);">
                <i class="far fa-share-square mr-2"></i> Compartir
                <span class="badge badge-light ml-2" id="contadorCompartidosEmpresa" style="background: rgba(255,255,255,0.3); color: #007bff;">0</span>
            </button>
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
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Información Rápida -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; position: sticky; top: 20px;">
                    <div class="card-body p-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 16px 16px 0 0;">
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

                <!-- Empresas Colaboradoras -->
                <div id="colaboradorasCard" class="card border-0 shadow-sm mb-4" style="display: none; border-radius: 16px; position: sticky; top: 400px;">
                    <div class="card-body p-4" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border-radius: 16px 16px 0 0;">
                        <div class="d-flex align-items-center">
                            <div class="mr-3" style="width: 48px; height: 48px; background: rgba(255,255,255,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px);">
                                <i class="fas fa-handshake text-white" style="font-size: 1.25rem;"></i>
                            </div>
                            <h5 class="mb-0 text-white" style="font-weight: 700; font-size: 1.1rem;">
                                Empresas Colaboradoras
                            </h5>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div id="colaboradoras" class="d-flex flex-wrap" style="gap: 0.75rem;"></div>
                    </div>
                </div>

                <!-- Auspiciadores -->
                <div id="auspiciadoresCard" class="card border-0 shadow-sm mb-4" style="display: none; border-radius: 16px; position: sticky; top: 500px;">
                    <div class="card-body p-4" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%); border-radius: 16px 16px 0 0;">
                        <div class="d-flex align-items-center">
                            <div class="mr-3" style="width: 48px; height: 48px; background: rgba(255,255,255,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px);">
                                <i class="fas fa-star text-white" style="font-size: 1.25rem;"></i>
                            </div>
                            <h5 class="mb-0 text-white" style="font-weight: 700; font-size: 1.1rem;">
                                Auspiciadores
                            </h5>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div id="auspiciadores" class="d-flex flex-wrap" style="gap: 0.75rem;"></div>
                    </div>
                </div>

                <!-- Patrocinadores -->
                <div id="patrocinadoresCard" class="card border-0 shadow-sm mb-4" style="display: none; border-radius: 16px; position: sticky; top: 600px;">
                    <div class="card-body p-4" style="background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); border-radius: 16px 16px 0 0;">
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
                <div id="invitadosCard" class="card border-0 shadow-sm mb-4" style="display: none; border-radius: 16px; position: sticky; top: 700px;">
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
@endsection

@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    /* Variables de color */
    :root {
        --primary-color: #667eea;
        --primary-dark: #764ba2;
        --dark-color: #2c3e50;
        --border-color: #e9ecef;
        --bg-light: #f8f9fa;
        --shadow-sm: 0 2px 8px rgba(102, 126, 234, 0.08);
        --shadow-md: 0 4px 16px rgba(102, 126, 234, 0.12);
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
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
        flex-shrink: 0;
    }

    /* Items de información */
    .info-item {
        display: flex;
        align-items: flex-start;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 12px;
        border-left: 4px solid #667eea;
        transition: all 0.3s ease;
    }

    .info-item:hover {
        background: #ffffff;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
        transform: translateX(4px);
    }

    .info-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        color: #667eea;
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
    
    .badge {
        border-radius: 50px;
        padding: 0.5em 1em;
        font-weight: 500;
        letter-spacing: 0.3px;
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

    /* Estilos para el modal de compartir */
    .modal-backdrop {
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal.show {
        display: block !important;
    }

    /* Responsive */
    @media (max-width: 991px) {
        .section-icon {
            font-size: 1.25rem;
        }

        .info-icon {
            width: 36px;
            height: 36px;
            font-size: 0.9rem;
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
</style>
@endsection

@section('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js" onload="console.log('QRCode loaded')" onerror="console.error('Failed to load QRCode')"></script>
<script>
    // Esperar a que QRCode esté disponible
    window.addEventListener('load', function() {
        if (typeof QRCode === 'undefined') {
            console.warn('QRCode not loaded, retrying...');
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js';
            script.onload = function() {
                console.log('QRCode loaded on retry');
            };
            document.head.appendChild(script);
        }
    });
</script>
<script>
    // Definir PUBLIC_BASE_URL desde variable de entorno
    window.PUBLIC_BASE_URL = "{{ env('PUBLIC_APP_URL', 'http://192.168.0.7:8000') }}";
    console.log("🌐 PUBLIC_BASE_URL desde .env:", window.PUBLIC_BASE_URL);
</script>
<script src="{{ asset('assets/js/config.js') }}"></script>
<script src="{{ asset('assets/js/empresa/show-event.js') }}"></script>
<script>
    // Función para mostrar imagen en modal
    function mostrarImagenGaleria(url) {
        const modal = document.getElementById('modalImagenGaleria');
        const img = document.getElementById('imagenModalGaleria');
        if (modal && img) {
            img.src = url;
            if (typeof $ !== 'undefined') {
                $(modal).modal('show');
            } else {
                modal.style.display = 'block';
                modal.classList.add('show');
                document.body.classList.add('modal-open');
                const backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                backdrop.id = 'modalBackdropImagen';
                document.body.appendChild(backdrop);
            }
        }
    }

    // Función para cerrar modal de imagen
    function cerrarModalImagen() {
        const modal = document.getElementById('modalImagenGaleria');
        if (modal) {
            if (typeof $ !== 'undefined') {
                $(modal).modal('hide');
            } else {
                modal.style.display = 'none';
                modal.classList.remove('show');
                document.body.classList.remove('modal-open');
                const backdrop = document.getElementById('modalBackdropImagen');
                if (backdrop) {
                    backdrop.remove();
                }
            }
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
@endsection


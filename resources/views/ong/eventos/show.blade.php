@extends('layouts.adminlte')

@section('page_title', 'Detalle del Evento')

@section('content_body')
<div class="container-fluid px-0">
    <!-- Banner Superior con Imagen Principal -->
    <div id="eventBanner" class="position-relative" style="height: 400px; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); overflow: hidden;">
        <div id="bannerImage" class="w-100 h-100" style="background-size: cover; background-position: center; opacity: 0.3;"></div>
        <div class="position-absolute" style="top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(to bottom, rgba(12, 43, 68, 0.3) 0%, rgba(0, 163, 108, 0.6) 100%);"></div>
        <div class="position-absolute" style="bottom: 0; left: 0; right: 0; padding: 2rem; color: white;">
            <div class="container">
                <h1 id="titulo" class="mb-2" style="font-size: 2.5rem; font-weight: 700; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);"></h1>
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <span id="tipoEventoBadge" class="badge badge-light" style="font-size: 0.9rem; padding: 0.5em 1em;"></span>
                    <span id="estadoBadge" class="badge" style="font-size: 0.9rem; padding: 0.5em 1em;"></span>
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
                    <h5 class="modal-title" style="color: #0C2B44; font-weight: 700; font-size: 1.25rem;">Compartir</h5>
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
                                    <i class="fas fa-link" style="font-size: 2rem; color: #0C2B44;"></i>
                                </div>
                                <span style="color: #333; font-size: 0.9rem; font-weight: 600;">Copiar enlace</span>
                            </button>
                        </div>
                        <!-- QR Code -->
                        <div class="col-6 mb-4">
                            <button onclick="mostrarQR()" class="btn btn-link p-0" style="text-decoration: none; border: none; background: none; width: 100%;">
                                <div style="width: 80px; height: 80px; background: #0C2B44; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(12,43,68,0.3);" onmouseover="this.style.background='#00A36C'; this.style.transform='scale(1.1)'; this.style.boxShadow='0 4px 12px rgba(0,163,108,0.4)'" onmouseout="this.style.background='#0C2B44'; this.style.transform='scale(1)'; this.style.boxShadow='0 2px 8px rgba(12,43,68,0.3)'">
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
        <!-- Mensaje de Evento Finalizado -->
        <div id="mensajeEventoFinalizado" class="alert alert-info mb-4" style="display: none; border-radius: 12px; border: none; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); color: white; padding: 1.5rem;">
            <div class="d-flex align-items-center">
                <i class="far fa-info-circle mr-3" style="font-size: 2rem;"></i>
                <div>
                    <h5 class="mb-1" style="font-weight: 700; font-size: 1.1rem;">Este evento fue finalizado</h5>
                    <p class="mb-0" style="font-size: 0.95rem; opacity: 0.95;">
                        Fecha de finalización: <span id="fechaFinalizacionMensaje" style="font-weight: 600;"></span>
                    </p>
                    <p class="mb-0 mt-2" style="font-size: 0.9rem; opacity: 0.9;">
                        Ya no es posible participar, reaccionar o compartir este evento. Solo puedes ver los detalles.
                    </p>
                </div>
            </div>
        </div>

        <!-- Botones de Acción (ONG) -->
        <div class="d-flex justify-content-end mb-4 flex-wrap" style="gap: 0.5rem;">
            <a href="/ong/eventos" class="btn" style="background: #F5F5F5; color: #0C2B44; border: none; border-radius: 8px;">
                <i class="far fa-arrow-left mr-2"></i> Volver
            </a>
            <a id="btnDashboard" href="#" class="btn" style="background: #00A36C; color: white; border: none; border-radius: 8px; font-weight: 600; display: none;">
                <i class="far fa-chart-bar mr-2"></i> Dashboard del Evento
            </a>
            <div class="btn" id="btnReacciones" style="background: #F5F5F5; color: #dc3545; border: none; border-radius: 50px; cursor: default; transition: all 0.3s ease;">
                <i class="far fa-heart mr-2" style="transition: all 0.3s ease;"></i>
                <span id="contadorReaccionesOng" style="transition: all 0.3s ease;">0</span> reacciones
            </div>
            <button class="btn" id="btnCompartir" style="background: #0C2B44; color: white; border: none; border-radius: 50px;">
                <i class="far fa-share-square mr-2"></i> Compartir <span id="contadorCompartidos" style="margin-left: 0.5rem; font-weight: 600;">0</span>
            </button>
            <a id="btnEditar" href="#" class="btn" style="background: #0C2B44; color: white; border: none; border-radius: 8px;">
                <i class="far fa-edit mr-2"></i> Editar Evento
            </a>
        </div>

        <div class="row">
            <!-- Columna Principal -->
            <div class="col-lg-8">
                <!-- Descripción -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; border: 1px solid #F5F5F5;">
                    <div class="card-body p-4">
                        <h4 class="mb-3" style="color: #0C2B44; font-weight: 700;">
                            <i class="far fa-align-left mr-2" style="color: #00A36C;"></i> Descripción
                        </h4>
                        <p id="descripcion" class="mb-0" style="color: #333333; line-height: 1.8; font-size: 1rem;"></p>
                    </div>
                </div>

                <!-- Información del Evento -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; border: 1px solid #F5F5F5;">
                    <div class="card-body p-4">
                        <h4 class="mb-4" style="color: #0C2B44; font-weight: 700;">
                            <i class="far fa-info-circle mr-2" style="color: #00A36C;"></i> Información del Evento
                        </h4>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <i class="far fa-calendar mr-3 mt-1" style="font-size: 1.2rem; color: #00A36C;"></i>
                                    <div>
                                        <h6 class="mb-1" style="color: #0C2B44; font-weight: 600;">Fecha de Inicio</h6>
                                        <p id="fecha_inicio" class="mb-0" style="color: #333333;"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <i class="far fa-calendar-check mr-3 mt-1" style="font-size: 1.2rem; color: #00A36C;"></i>
                                    <div>
                                        <h6 class="mb-1" style="color: #0C2B44; font-weight: 600;">Fecha de Fin</h6>
                                        <p id="fecha_fin" class="mb-0" style="color: #333333;"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <i class="far fa-clock mr-3 mt-1" style="font-size: 1.2rem; color: #00A36C;"></i>
                                    <div>
                                        <h6 class="mb-1" style="color: #0C2B44; font-weight: 600;">Límite de Inscripción</h6>
                                        <p id="fecha_limite_inscripcion" class="mb-0" style="color: #333333;"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <i class="far fa-users mr-3 mt-1" style="font-size: 1.2rem; color: #00A36C;"></i>
                                    <div>
                                        <h6 class="mb-1" style="color: #0C2B44; font-weight: 600;">Capacidad Máxima</h6>
                                        <p id="capacidad_maxima" class="mb-0" style="color: #333333;"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3" id="fechaFinalizacionContainer" style="display: none;">
                                <div class="d-flex align-items-start">
                                    <i class="far fa-flag-checkered mr-3 mt-1" style="font-size: 1.2rem; color: #00A36C;"></i>
                                    <div>
                                        <h6 class="mb-1" style="color: #0C2B44; font-weight: 600;">Fecha de Finalización</h6>
                                        <p id="fecha_finalizacion" class="mb-0" style="color: #333333;"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3" id="creadorContainer">
                                <div class="d-flex align-items-start">
                                    <i class="far fa-user-circle mr-3 mt-1" style="font-size: 1.2rem; color: #00A36C;"></i>
                                    <div>
                                        <h6 class="mb-1" style="color: #0C2B44; font-weight: 600;">Creado por</h6>
                                        <div id="creadorInfo" class="d-flex align-items-center" style="gap: 0.5rem;">
                                            <span id="creadorNombre" class="mb-0" style="color: #333333;"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ubicación -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; border: 1px solid #F5F5F5;">
                    <div class="card-body p-4">
                        <h4 class="mb-3" style="color: #0C2B44; font-weight: 700;">
                            <i class="far fa-map-marker-alt mr-2" style="color: #00A36C;"></i> Ubicación
                        </h4>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h6 class="mb-1" style="color: #0C2B44; font-weight: 600;">Ciudad</h6>
                                <p id="ciudad" class="mb-0" style="color: #333333;"></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="mb-1" style="color: #0C2B44; font-weight: 600;">Dirección</h6>
                                <p id="direccion" class="mb-0" style="color: #333333;"></p>
                            </div>
                        </div>
                        <div id="mapContainer" class="mt-3" style="height: 300px; border-radius: 8px; overflow: hidden; display: none;">
                            <!-- Mapa se cargará aquí -->
                        </div>
                    </div>
                </div>

                <!-- Galería de Imágenes -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; border: 1px solid #F5F5F5;">
                    <div class="card-body p-4">
                        <h4 class="mb-4" style="color: #0C2B44; font-weight: 700;">
                            <i class="far fa-images mr-2" style="color: #00A36C;"></i> Galería de Imágenes
                        </h4>
                        <div id="imagenes">
                            <!-- Carrusel de Bootstrap -->
                            <div id="carouselImagenes" class="carousel slide" data-ride="carousel" data-interval="3000" style="display: none;">
                                <div class="carousel-inner" id="carouselInner"></div>
                                <a class="carousel-control-prev" href="#carouselImagenes" role="button" data-slide="prev" style="width: 5%;">
                                    <span class="carousel-control-prev-icon" aria-hidden="true" style="background-color: rgba(0,0,0,0.5); border-radius: 50%; width: 40px; height: 40px;"></span>
                                    <span class="sr-only">Anterior</span>
                                </a>
                                <a class="carousel-control-next" href="#carouselImagenes" role="button" data-slide="next" style="width: 5%;">
                                    <span class="carousel-control-next-icon" aria-hidden="true" style="background-color: rgba(0,0,0,0.5); border-radius: 50%; width: 40px; height: 40px;"></span>
                                    <span class="sr-only">Siguiente</span>
                                </a>
                                <!-- Indicadores -->
                                <ol class="carousel-indicators" id="carouselIndicators"></ol>
                            </div>
                            <p id="sinImagenes" class="text-muted text-center" style="display: none;">No hay imágenes disponibles</p>
                        </div>
                    </div>
                </div>

                <!-- Reacciones (Favoritos) -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; border: 1px solid #F5F5F5;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-0" style="color: #0C2B44; font-weight: 700;">
                                <i class="far fa-heart mr-2" style="color: #dc3545;"></i> Reacciones y Favoritos
                            </h4>
                            <button class="btn btn-sm btn-actualizar-reacciones" onclick="cargarReacciones()" style="background: #F5F5F5; color: #0C2B44; border: none; border-radius: 8px; transition: transform 0.5s ease;">
                                <i class="far fa-sync mr-1"></i> Actualizar
                            </button>
                        </div>
                        <p class="mb-3" style="font-size: 0.9rem; color: #333333;">
                            Usuarios que han marcado este evento como favorito con un corazón.
                        </p>
                        <div id="reaccionesContainer">
                            <div class="text-center py-3">
                                <div class="spinner-border" role="status" style="color: #00A36C;">
                                    <span class="sr-only">Cargando...</span>
                                </div>
                                <p class="mt-2" style="color: #333333;">Cargando reacciones...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Voluntarios y Participantes Inscritos -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; border: 1px solid #F5F5F5;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-0" style="color: #0C2B44; font-weight: 700;">
                                <i class="far fa-users mr-2" style="color: #00A36C;"></i> Voluntarios y Participantes Inscritos
                            </h4>
                            <button class="btn btn-sm" onclick="cargarParticipantes()" style="background: #F5F5F5; color: #0C2B44; border: none; border-radius: 8px;">
                                <i class="far fa-sync mr-1"></i> Actualizar
                            </button>
                        </div>
                        <p class="mb-3" style="font-size: 0.9rem; color: #333333;">
                            Gestiona las solicitudes de participación y aprueba o rechaza a los voluntarios que desean participar en este evento.
                        </p>
                        <div id="participantesContainer">
                            <div class="text-center py-3">
                                <div class="spinner-border" role="status" style="color: #00A36C;">
                                    <span class="sr-only">Cargando...</span>
                                </div>
                                <p class="mt-2" style="color: #333333;">Cargando participantes...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Información Rápida -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; border: 1px solid #F5F5F5;">
                    <div class="card-body p-4">
                        <h5 class="mb-3" style="color: #0C2B44; font-weight: 700;">
                            <i class="far fa-info-circle mr-2" style="color: #00A36C;"></i> Información Rápida
                        </h5>
                        <div class="mb-3 pb-3 border-bottom" style="border-color: #F5F5F5 !important;">
                            <small class="d-block mb-1" style="color: #333333; font-weight: 600;">Estado</small>
                            <span id="estadoSidebar" class="badge"></span>
                        </div>
                        <div class="mb-3 pb-3 border-bottom" style="border-color: #F5F5F5 !important;">
                            <small class="d-block mb-1" style="color: #333333; font-weight: 600;">Tipo de Evento</small>
                            <span id="tipoEventoSidebar" class="font-weight-bold" style="color: #0C2B44;"></span>
                        </div>
                        <div class="mb-3 pb-3 border-bottom" style="border-color: #F5F5F5 !important;">
                            <small class="d-block mb-1" style="color: #333333; font-weight: 600;">Capacidad</small>
                            <span id="capacidadSidebar" class="font-weight-bold" style="color: #0C2B44;"></span>
                        </div>
                        <div id="inscripcionAbiertaContainer" class="mb-3">
                            <small class="d-block mb-1" style="color: #333333; font-weight: 600;">Inscripción</small>
                            <span id="inscripcionAbierta" class="badge"></span>
                        </div>
                    </div>
                </div>

                <!-- Patrocinadores -->
                <div id="patrocinadoresCard" class="card border-0 shadow-sm mb-4" style="border-radius: 12px; border: 1px solid #F5F5F5; display: none;">
                    <div class="card-body p-4">
                        <h5 class="mb-3" style="color: #0C2B44; font-weight: 700;">
                            <i class="far fa-handshake mr-2" style="color: #00A36C;"></i> Patrocinadores
                        </h5>
                        <div id="patrocinadores" class="d-flex flex-wrap gap-2"></div>
                    </div>
                </div>

                <!-- Invitados -->
                <div id="invitadosCard" class="card border-0 shadow-sm mb-4" style="border-radius: 12px; border: 1px solid #F5F5F5; display: none;">
                    <div class="card-body p-4">
                        <h5 class="mb-3" style="color: #0C2B44; font-weight: 700;">
                            <i class="far fa-user-friends mr-2" style="color: #00A36C;"></i> Invitados Especiales
                        </h5>
                        <div id="invitados" class="d-flex flex-wrap gap-2"></div>
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
    body {
        background-color: #F5F5F5;
    }
    
    .card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(12, 43, 68, 0.15) !important;
    }
    
    #eventBanner {
        box-shadow: 0 4px 6px rgba(12, 43, 68, 0.2);
    }
    
    .badge {
        font-weight: 500;
        padding: 0.5em 0.8em;
        border-radius: 20px;
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
    
    /* Estilos para participantes */
    .participante-item {
        padding: 0.75rem;
        border-radius: 8px;
        background: #F5F5F5;
        border-left: 3px solid #00A36C;
        margin-bottom: 0.5rem;
    }
    
    /* Mejoras para cards de reacciones y participantes */
    .card {
        border: 1px solid #F5F5F5 !important;
    }
    
    .card:hover {
        border-color: #00A36C !important;
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
    window.PUBLIC_BASE_URL = "{{ env('PUBLIC_APP_URL', 'http://192.168.0.6:8000') }}";
    console.log("🌐 PUBLIC_BASE_URL desde .env:", window.PUBLIC_BASE_URL);
</script>
<script src="{{ asset('assets/js/config.js') }}"></script>
<script src="{{ asset('assets/js/ong/show-event.js') }}"></script>
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
@stop

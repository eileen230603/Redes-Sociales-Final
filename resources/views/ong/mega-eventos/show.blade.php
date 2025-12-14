@extends('layouts.adminlte')

@section('page_title', 'Detalle del Mega Evento')

@section('content_body')
<div class="container-fluid">
    <div id="loadingMessage" class="text-center py-5">
        <div class="spinner-border" role="status" style="color: #00A36C; width: 3rem; height: 3rem;">
            <span class="sr-only">Cargando...</span>
        </div>
        <p class="mt-3" style="color: #333333; font-weight: 500;">Cargando información del mega evento...</p>
    </div>

    <div id="megaEventoContent" style="display: none;">
        <!-- Banner Superior con Imagen Principal - Mejorado -->
        <div id="eventBanner" class="position-relative" style="height: 450px; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); overflow: hidden; border-radius: 0 0 24px 24px;">
            <div id="bannerImage" class="w-100 h-100" style="background-size: cover; background-position: center; opacity: 0.25; transition: transform 0.5s ease;"></div>
            <div class="position-absolute" style="top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(to bottom, rgba(12, 43, 68, 0.4) 0%, rgba(0, 163, 108, 0.7) 100%);"></div>
            <div class="position-absolute" style="bottom: 0; left: 0; right: 0; padding: 3rem 2rem; color: white;">
                <div class="container">
                    <div class="d-flex align-items-center mb-3">
                        <div class="mr-3" style="width: 64px; height: 64px; background: rgba(255,255,255,0.15); border-radius: 16px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px); box-shadow: 0 4px 16px rgba(0,0,0,0.2); animation: pulse 2s ease-in-out infinite;">
                            <i class="fas fa-star" style="font-size: 2rem;"></i>
                        </div>
                        <div style="flex: 1;">
                            <h1 id="titulo" class="mb-2" style="font-size: 2.75rem; font-weight: 700; text-shadow: 2px 2px 8px rgba(0,0,0,0.5); letter-spacing: -0.5px; line-height: 1.2; animation: fadeInUp 0.6s ease-out;">-</h1>
                            <div class="d-flex flex-wrap align-items-center" style="gap: 0.75rem;">
                                <span id="categoriaBadge" class="badge" style="font-size: 0.95rem; padding: 0.6em 1.2em; background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); border-radius: 50px; font-weight: 500; animation: fadeInUp 0.8s ease-out;">-</span>
                                <span id="estadoBadge" class="badge" style="font-size: 0.95rem; padding: 0.6em 1.2em; border-radius: 50px; font-weight: 500; animation: fadeInUp 0.9s ease-out;">-</span>
                                <span id="publicoBadge" class="badge" style="font-size: 0.95rem; padding: 0.6em 1.2em; border-radius: 50px; font-weight: 500; animation: fadeInUp 1s ease-out;">-</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mt-4">
            <!-- Botones de Acción - Mejorados -->
            <div class="d-flex justify-content-end mb-4 flex-wrap" style="gap: 0.75rem;">
                <a href="{{ route('ong.mega-eventos.index') }}" class="btn btn-outline-secondary" style="border-radius: 12px; padding: 0.6rem 1.5rem; font-weight: 500; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(0,0,0,0.08);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.12)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.08)'">
                    <i class="fas fa-arrow-left mr-2"></i> Volver
                </a>
                <button class="btn btn-outline-danger" id="btnReaccionar" style="border-radius: 12px; padding: 0.6rem 1.5rem; font-weight: 500; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(220,53,69,0.2);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(220,53,69,0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(220,53,69,0.2)'">
                    <i class="fas fa-heart mr-2" id="iconoCorazon"></i>
                    <span id="textoReaccion">Me gusta</span>
                    <span id="contadorReacciones" class="badge badge-light ml-2">0</span>
                </button>
                <button class="btn btn-primary" id="btnCompartir" style="border-radius: 12px; padding: 0.6rem 1.5rem; font-weight: 500; transition: all 0.3s ease; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); border: none; box-shadow: 0 2px 8px rgba(12,43,68,0.2);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(12,43,68,0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(12,43,68,0.2)'">
                    <i class="fas fa-share-alt mr-2"></i> Compartir <span id="contadorCompartidos" class="badge badge-light ml-2">0</span>
                </button>
                <a href="#" id="seguimientoLink" class="btn btn-info" style="border-radius: 12px; padding: 0.6rem 1.5rem; font-weight: 500; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(23,162,184,0.2);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(23,162,184,0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(23,162,184,0.2)'">
                    <i class="fas fa-chart-line mr-2"></i> Seguimiento
                </a>
                <a href="#" id="editLink" class="btn btn-success" style="border-radius: 12px; padding: 0.6rem 1.5rem; font-weight: 500; transition: all 0.3s ease; background: linear-gradient(135deg, #00A36C 0%, #008a5a 100%); border: none; box-shadow: 0 2px 8px rgba(0,163,108,0.2);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0,163,108,0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(0,163,108,0.2)'">
                    <i class="fas fa-edit mr-2"></i> Editar Mega Evento
                </a>
            </div>

            <div class="row">
                <!-- Columna Principal -->
                <div class="col-lg-8">
                <!-- Descripción -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; animation: fadeInUp 0.5s ease-out;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="section-icon mr-3">
                                <i class="fas fa-align-left"></i>
                            </div>
                            <div>
                                <h5 class="mb-0" style="font-weight: 700; color: #0C2B44; font-size: 1.1rem;">
                                    Descripción
                        </h5>
                                <p class="mb-0 text-muted" style="font-size: 0.85rem; margin-top: 0.25rem;">
                                    Información detallada del mega evento
                                </p>
                    </div>
                        </div>
                        <p id="descripcion" class="mb-0" style="color: #495057; line-height: 1.8; font-size: 1rem;">-</p>
                    </div>
                        </div>

                <!-- Información del Mega Evento -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; animation: fadeInUp 0.6s ease-out;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="section-icon mr-3">
                                <i class="fas fa-info-circle"></i>
                            </div>
                                    <div>
                                <h5 class="mb-0" style="font-weight: 700; color: #0C2B44; font-size: 1.1rem;">
                                    Información del Mega Evento
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
                                        <i class="fas fa-calendar-alt"></i>
                            </div>
                                    <div class="info-content">
                                        <h6 class="info-label">Fecha de Inicio</h6>
                                        <p id="fecha_inicio" class="info-value">-</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-calendar-check"></i>
                                                </div>
                                    <div class="info-content">
                                        <h6 class="info-label">Fecha de Fin</h6>
                                        <p id="fecha_fin" class="info-value">-</p>
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
                                        <p id="capacidad_maxima" class="info-value">-</p>
                            </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="section-icon mr-3">
                                        <i class="fas fa-map"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0" style="font-weight: 700; color: #0C2B44; font-size: 1.1rem;">
                                            Mapa de Ubicación
                                        </h5>
                                    </div>
                                </div>
                                <div id="map" class="rounded" style="height: 350px; border: 1px solid #e9ecef; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: all 0.3s ease;" onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.12)'" onmouseout="this.style.boxShadow='0 2px 8px rgba(0,0,0,0.08)'"></div>
                            </div>
                            <div class="col-md-12 mb-4">
                                <div class="info-item" style="background: linear-gradient(135deg, rgba(12, 43, 68, 0.05) 0%, rgba(0, 163, 108, 0.05) 100%); padding: 1.5rem; border-radius: 12px; border-left: 4px solid #00A36C;">
                                    <div class="info-icon" style="font-size: 1.5rem; color: #00A36C;">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div class="info-content">
                                        <h6 class="info-label" style="color: #0C2B44; font-weight: 600; margin-bottom: 0.5rem;">Ubicación del Evento</h6>
                                        <div id="ubicacionContainer">
                                            <p id="ubicacion" class="info-value" style="font-size: 1rem; font-weight: 500;">-</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Galería de Imágenes -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; animation: fadeInUp 0.7s ease-out;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div class="d-flex align-items-center">
                                <div class="section-icon mr-3">
                                    <i class="fas fa-images"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0" style="font-weight: 700; color: #0C2B44; font-size: 1.1rem;">
                                        Galería de Imágenes
                        </h5>
                                    <p class="mb-0 text-muted" style="font-size: 0.85rem; margin-top: 0.25rem;">
                                        Imágenes promocionales del mega evento
                                    </p>
                    </div>
                            </div>
                            <span id="imagenesCount" class="badge" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); color: white; font-size: 0.9rem; padding: 0.5em 1em; border-radius: 50px; font-weight: 600;">0</span>
                        </div>
                        <div id="imagenesContainer" class="row">
                            <div class="col-12 text-center py-4">
                                <div class="spinner-border" role="status" style="color: #00A36C; width: 3rem; height: 3rem;">
                                    <span class="sr-only">Cargando...</span>
                                </div>
                                <p class="mt-3 text-muted">Cargando imágenes...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reacciones y Favoritos -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; animation: fadeInUp 0.8s ease-out;">
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
                                        Usuarios que han marcado este mega evento como favorito
                                    </p>
                                </div>
                            </div>
                            <button class="btn btn-sm btn-outline-secondary btn-actualizar-reacciones" onclick="cargarReaccionesMegaEvento()" style="border-radius: 8px; padding: 0.4rem 1rem; transition: all 0.3s ease;" onmouseover="this.style.transform='rotate(180deg)'" onmouseout="this.style.transform='rotate(0deg)'">
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

                <!-- Participantes Inscritos -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; animation: fadeInUp 0.9s ease-out;">
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
                            <button class="btn btn-sm btn-outline-secondary btn-actualizar-participantes" onclick="cargarParticipantesCardMegaEvento()" style="border-radius: 8px; padding: 0.4rem 1rem;">
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

                <!-- Control de Asistencias -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; animation: fadeInUp 1s ease-out;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div class="d-flex align-items-center">
                                <div class="section-icon mr-3">
                                    <i class="fas fa-clipboard-check"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0" style="font-weight: 700; color: #0C2B44; font-size: 1.1rem;">
                                        Control de Asistencias
                                    </h5>
                                    <p class="mb-0 text-muted" style="font-size: 0.85rem; margin-top: 0.25rem;">
                                        Registra y gestiona las asistencias de los participantes
                                    </p>
                                </div>
                            </div>
                            <button class="btn btn-sm btn-outline-secondary" onclick="cargarListaAsistenciaMegaEvento()" style="border-radius: 8px; padding: 0.4rem 1rem;">
                                <i class="fas fa-sync mr-1"></i> Actualizar
                            </button>
                        </div>

                        <!-- Estadísticas -->
                        <div class="row mb-4">
                            <div class="col-md-4 mb-3">
                                <div class="card border-0" style="background: linear-gradient(135deg, #0C2B44 0%, #1a4a6b 100%); border-radius: 12px; padding: 1.25rem; box-shadow: 0 4px 12px rgba(12, 43, 68, 0.2);">
                                    <div class="d-flex align-items-center">
                                        <div class="mr-3" style="width: 50px; height: 50px; background: rgba(255,255,255,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-users text-white" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div>
                                            <p class="mb-0 text-white" style="font-size: 0.85rem; opacity: 0.9;">Total Inscritos</p>
                                            <h3 class="mb-0 text-white" id="totalInscritosMegaEvento" style="font-weight: 700;">0</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card border-0" style="background: linear-gradient(135deg, #00A36C 0%, #008a5a 100%); border-radius: 12px; padding: 1.25rem; box-shadow: 0 4px 12px rgba(0, 163, 108, 0.2);">
                                    <div class="d-flex align-items-center">
                                        <div class="mr-3" style="width: 50px; height: 50px; background: rgba(255,255,255,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-check-circle text-white" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div>
                                            <p class="mb-0 text-white" style="font-size: 0.85rem; opacity: 0.9;">Asistieron</p>
                                            <h3 class="mb-0 text-white" id="totalAsistieronMegaEvento" style="font-weight: 700;">0</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card border-0" style="background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%); border-radius: 12px; padding: 1.25rem; box-shadow: 0 4px 12px rgba(255, 193, 7, 0.2);">
                                    <div class="d-flex align-items-center">
                                        <div class="mr-3" style="width: 50px; height: 50px; background: rgba(255,255,255,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-clock text-white" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div>
                                            <p class="mb-0 text-white" style="font-size: 0.85rem; opacity: 0.9;">Pendientes</p>
                                            <h3 class="mb-0 text-white" id="totalPendientesMegaEvento" style="font-weight: 700;">0</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información sobre registro de asistencia -->
                        <div class="alert alert-info mb-4" style="background: linear-gradient(135deg, rgba(23, 162, 184, 0.1) 0%, rgba(23, 162, 184, 0.05) 100%); border-left: 4px solid #17a2b8; border-radius: 12px; padding: 1.25rem;">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-info-circle mr-3 mt-1" style="font-size: 1.5rem; color: #17a2b8;"></i>
                                <div>
                                    <h6 class="mb-2" style="font-weight: 700; color: #0C2B44;">
                                        <i class="fas fa-user-check mr-2" style="color: #17a2b8;"></i>Registro de Asistencia
                                    </h6>
                                    <p class="mb-0" style="color: #0C2B44; line-height: 1.6;">
                                        Los participantes deben registrar su propia asistencia desde su cuenta. Aquí puedes visualizar quién ha asistido y quién está pendiente.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Lista de asistencias -->
                        <div id="listaAsistenciaMegaEventoContainer">
                            <div class="text-center py-4">
                                <div class="spinner-border" role="status" style="color: #00A36C; width: 3rem; height: 3rem;">
                                    <span class="sr-only">Cargando...</span>
                                </div>
                                <p class="mt-3 text-muted">Cargando lista de asistencia...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar - Información Adicional -->
            <div class="col-lg-4">
                <!-- ONG Organizadora -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; position: sticky; top: 2rem; animation: fadeInUp 0.5s ease-out;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="section-icon mr-3">
                                <i class="fas fa-building"></i>
                    </div>
                            <div>
                                <h5 class="mb-0" style="font-weight: 700; color: #0C2B44; font-size: 1.1rem;">
                                    ONG Organizadora
                                </h5>
                    </div>
                </div>
                        <div class="info-item d-flex align-items-center">
                            <div id="ongAvatarContainer" class="mr-3" style="flex-shrink: 0;">
                                <!-- Avatar se cargará aquí dinámicamente -->
                            </div>
                            <div class="info-content flex-grow-1">
                                <p id="ong_organizadora" class="info-value mb-0" style="font-size: 1rem; font-weight: 500;">-</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fechas del Sistema -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; animation: fadeInUp 0.6s ease-out;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="section-icon mr-3">
                                <i class="fas fa-calendar-alt"></i>
                    </div>
                            <div>
                                <h5 class="mb-0" style="font-weight: 700; color: #0C2B44; font-size: 1.1rem;">
                                    Fechas del Sistema
                                </h5>
                        </div>
                        </div>
                        <div class="info-item mb-3 pb-3" style="border-bottom: 1px solid #e9ecef;">
                            <div class="info-icon">
                                <i class="fas fa-plus-circle"></i>
                            </div>
                            <div class="info-content">
                                <h6 class="info-label">Fecha de Creación</h6>
                                <p id="fecha_creacion" class="info-value">-</p>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-sync-alt"></i>
                            </div>
                            <div class="info-content">
                                <h6 class="info-label">Última Actualización</h6>
                                <p id="fecha_actualizacion" class="info-value">-</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información Adicional -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; animation: fadeInUp 0.7s ease-out;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="section-icon mr-3">
                                <i class="fas fa-info-circle"></i>
                        </div>
                        <div>
                                <h5 class="mb-0" style="font-weight: 700; color: #0C2B44; font-size: 1.1rem;">
                                    Información Adicional
                                </h5>
                        </div>
                    </div>
                        <div class="info-item mb-3 pb-3" style="border-bottom: 1px solid #e9ecef;">
                            <div class="info-icon">
                                <i class="fas fa-flag"></i>
                </div>
                            <div class="info-content" style="flex: 1;">
                                <h6 class="info-label" style="font-size: 0.85rem; font-weight: 600; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.5rem;">Estado del Evento</h6>
                                <div id="estado" class="info-value" style="font-size: 1rem; font-weight: 500;">-</div>
            </div>
        </div>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-eye"></i>
                            </div>
                            <div class="info-content" style="flex: 1;">
                                <h6 class="info-label" style="font-size: 0.85rem; font-weight: 600; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.5rem;">Visibilidad</h6>
                                <div id="es_publico" class="info-value" style="font-size: 1rem; font-weight: 500;">-</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Patrocinadores -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; animation: fadeInUp 0.8s ease-out; display: none;" id="patrocinadoresCard">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="section-icon mr-3">
                                <i class="fas fa-handshake"></i>
                            </div>
                            <div>
                                <h5 class="mb-0" style="font-weight: 700; color: #0C2B44; font-size: 1.1rem;">
                                    Patrocinadores
                                </h5>
                                <p class="mb-0 text-muted" style="font-size: 0.85rem; margin-top: 0.25rem;">
                                    Empresas que patrocinan este mega evento
                                </p>
                            </div>
                        </div>
                        <div id="patrocinadores">
                            <!-- Se llenará dinámicamente -->
                        </div>
                    </div>
                </div>
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
                <button type="button" class="close" onclick="cerrarModalCompartir()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row text-center">
                    <!-- Copiar enlace -->
                    <div class="col-6 mb-4">
                        <button onclick="copiarEnlaceMegaEvento()" class="btn btn-link p-0" style="text-decoration: none; border: none; background: none; width: 100%;">
                            <div class="bg-light rounded-lg d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; transition: all 0.3s ease;" onmouseover="this.style.transform='scale(1.1)'; this.style.background='#E9ECEF';" onmouseout="this.style.transform='scale(1)'; this.style.background='#F5F5F5';">
                                <i class="fas fa-link fa-2x text-primary"></i>
                            </div>
                            <span class="font-weight-bold text-dark">Copiar enlace</span>
                        </button>
                    </div>
                    <!-- QR Code -->
                    <div class="col-6 mb-4">
                        <button onclick="mostrarQRMegaEvento()" class="btn btn-link p-0" style="text-decoration: none; border: none; background: none; width: 100%;">
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
                        <p class="text-muted mb-0">Escanea este código para acceder al mega evento</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    /* Variables CSS */
    :root {
        --primary-color: #0C2B44;
        --secondary-color: #00A36C;
        --text-dark: #2c3e50;
        --text-muted: #6c757d;
        --border-radius: 16px;
        --shadow-sm: 0 2px 8px rgba(0,0,0,0.08);
        --shadow-md: 0 4px 12px rgba(0,0,0,0.12);
        --shadow-lg: 0 8px 24px rgba(0,0,0,0.15);
    }
    
    /* Animaciones */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes fadeInUp {
        from { 
            opacity: 0; 
            transform: translateY(30px); 
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
            transform: scale(1.05);
            opacity: 0.9;
        }
    }

    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* Contenido principal */
    #megaEventoContent {
        animation: fadeIn 0.5s ease-in;
    }

    /* Banner con efecto hover */
    #eventBanner:hover #bannerImage {
        transform: scale(1.05);
    }

    /* Section Icon (para títulos principales) */
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
        box-shadow: 0 4px 12px rgba(12, 43, 68, 0.2);
        flex-shrink: 0;
        transition: all 0.3s ease;
    }

    .section-icon:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(12, 43, 68, 0.3);
    }

    /* Info Icon (para items individuales) */
    .info-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0C2B44;
        font-size: 1.2rem;
        margin-right: 1rem;
        flex-shrink: 0;
        transition: all 0.3s ease;
    }

    .info-item {
        display: flex;
        align-items: flex-start;
        padding: 1rem;
        border-radius: 12px;
        transition: all 0.3s ease;
        margin-bottom: 0.5rem;
    }

    .info-item:hover {
        background: rgba(12, 43, 68, 0.03);
        transform: translateX(5px);
    }

    .info-item:hover .info-icon {
        color: #00A36C;
        transform: scale(1.1);
    }

    .info-content {
        flex: 1;
    }

    .info-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-muted);
        margin-bottom: 0.25rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-value {
        font-size: 1rem;
        color: var(--text-dark);
        margin: 0;
        font-weight: 500;
    }

    /* Cards mejoradas */
    .card {
        transition: all 0.3s ease;
        border: none;
    }
    
    .card:hover {
        box-shadow: var(--shadow-md) !important;
        transform: translateY(-2px);
    }
    
    /* Imágenes con efecto hover */
    #imagenesContainer img {
        transition: transform 0.3s ease;
    }
    
    #imagenesContainer .position-relative:hover img {
        transform: scale(1.05);
    }
    
    /* Badges mejorados */
    .badge {
        font-weight: 500;
        padding: 0.5em 0.8em;
        border-radius: 20px;
        transition: all 0.3s ease;
    }

    .badge:hover {
        transform: scale(1.05);
    }
    
    .badge-success {
        background: linear-gradient(135deg, #00A36C 0%, #008a5a 100%) !important;
        color: white !important;
    }
    
    .badge-info {
        background: linear-gradient(135deg, #0C2B44 0%, #1a4d6b 100%) !important;
        color: white !important;
    }
    
    .badge-secondary {
        background-color: #333333 !important;
        color: white !important;
    }
    
    .badge-primary {
        background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%) !important;
        color: white !important;
    }
    
    .badge-danger {
        background-color: #dc3545 !important;
        color: white !important;
    }
    
    /* Mapa mejorado */
    #map {
        transition: all 0.3s ease;
        border-radius: 12px;
    }
    
    #map:hover {
        box-shadow: var(--shadow-md);
    }

    /* Botones mejorados */
    .btn {
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-2px);
    }

    .btn:active {
        transform: translateY(0);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .section-icon {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }

        .info-icon {
            font-size: 1rem;
        }

        #eventBanner {
            height: 350px !important;
        }

        #titulo {
            font-size: 2rem !important;
        }
    }

    /* Animación para reacciones */
    .reaccion-card {
        animation: fadeInUp 0.5s ease-out;
    }

    .reaccion-card:nth-child(1) { animation-delay: 0.1s; }
    .reaccion-card:nth-child(2) { animation-delay: 0.2s; }
    .reaccion-card:nth-child(3) { animation-delay: 0.3s; }
    .reaccion-card:nth-child(4) { animation-delay: 0.4s; }
    .reaccion-card:nth-child(5) { animation-delay: 0.5s; }
</style>
@endpush

@push('js')
{{-- Script global para icono de notificaciones --}}
<script src="{{ asset('js/notificaciones-ong.js') }}"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="{{ asset('assets/js/config.js') }}"></script>
<script>
// Función helper para construir URL de imagen
function buildImageUrl(imgUrl) {
    // Validar que imgUrl sea un string válido
    if (!imgUrl) return null;
    
    // Si es un array u objeto, retornar null (no debería pasar)
    if (typeof imgUrl !== 'string') {
        console.warn('buildImageUrl recibió un valor no string:', typeof imgUrl, imgUrl);
        return null;
    }
    
    // Limpiar espacios en blanco
    imgUrl = imgUrl.trim();
    if (imgUrl === '') return null;
    
    // CASO ESPECIAL: Detectar URLs malformadas como /storage/["http://..."]
    // Este patrón ocurre cuando se concatena /storage/ con un array JSON
    const storageJsonPattern = /\/storage\/\[(.*?)\]$/;
    const storageJsonMatch = imgUrl.match(storageJsonPattern);
    if (storageJsonMatch) {
        console.warn('buildImageUrl: Detectada URL malformada con /storage/[...], extrayendo JSON:', imgUrl.substring(0, 150));
        try {
            const jsonStr = '[' + storageJsonMatch[1] + ']';
            const parsed = JSON.parse(jsonStr);
            if (Array.isArray(parsed) && parsed.length > 0 && typeof parsed[0] === 'string') {
                // Usar el primer elemento del array parseado
                imgUrl = parsed[0].trim();
                if (imgUrl === '') return null;
            } else {
                console.warn('buildImageUrl: JSON extraído no es un array válido');
                return null;
            }
        } catch (e) {
            console.warn('buildImageUrl: Error al parsear JSON de URL malformada:', e);
            return null;
        }
    }
    
    // Si parece ser un array JSON serializado, intentar parsearlo y tomar el primer elemento
    if (imgUrl.startsWith('[') && imgUrl.endsWith(']')) {
        try {
            const parsed = JSON.parse(imgUrl);
            if (Array.isArray(parsed) && parsed.length > 0 && typeof parsed[0] === 'string') {
                imgUrl = parsed[0].trim();
                if (imgUrl === '') return null;
                // Validar que el resultado parseado no sea otro array JSON
                if (imgUrl.startsWith('[') && imgUrl.endsWith(']')) {
                    console.warn('buildImageUrl: El elemento parseado es otro array JSON, rechazando');
                    return null;
                }
            } else {
                console.warn('buildImageUrl: JSON parseado no es un array válido:', parsed);
                return null;
            }
        } catch (e) {
            console.warn('buildImageUrl: Error al parsear JSON:', e);
            return null;
        }
    }
    
    // Validación adicional: asegurar que imgUrl no contenga arrays JSON después del procesamiento
    if (imgUrl.includes('[') || imgUrl.includes(']')) {
        console.warn('buildImageUrl: URL contiene caracteres de array JSON después del procesamiento:', imgUrl);
        return null;
    }
    
    // Filtrar rutas inválidas (como templates/yootheme, resizer, wp-content, etc.)
    // PERO solo si NO es una URL completa de internet
    const esUrlCompleta = imgUrl.startsWith('http://') || imgUrl.startsWith('https://');
    const rutasInvalidas = ['/templates/', '/cache/', '/yootheme/', '/resizer/', '/wp-content/', 
                            'templates/', 'cache/', 'yootheme/', 'resizer/', 'wp-content/'];
    
    // Solo filtrar si NO es una URL completa de internet
    if (!esUrlCompleta) {
        const esRutaInvalida = rutasInvalidas.some(ruta => imgUrl.toLowerCase().includes(ruta.toLowerCase()));
    if (esRutaInvalida) {
        console.warn('Ruta de imagen inválida filtrada:', imgUrl);
        return null;
        }
    }
    
    // Si ya es una URL completa, reemplazar IPs antiguas y retornarla
    if (imgUrl.startsWith('http://') || imgUrl.startsWith('https://')) {
        // Reemplazar IPs antiguas
        imgUrl = imgUrl.replace(/http:\/\/127\.0\.0\.1:8000/g, 'http://192.168.0.7:8000');
        imgUrl = imgUrl.replace(/https:\/\/127\.0\.0\.1:8000/g, 'https://192.168.0.7:8000');
        imgUrl = imgUrl.replace(/http:\/\/192\.168\.0\.6:8000/g, 'http://192.168.0.7:8000');
        imgUrl = imgUrl.replace(/https:\/\/192\.168\.0\.6:8000/g, 'https://192.168.0.7:8000');
        imgUrl = imgUrl.replace(/http:\/\/192.168.0.7:8000/g, 'http://192.168.0.7:8000');
        imgUrl = imgUrl.replace(/https:\/\/192.168.0.7:8000/g, 'https://192.168.0.7:8000');
        
        // Validar que la URL completa no contenga arrays JSON
        if (!imgUrl.includes('[') && !imgUrl.includes(']')) {
            // Si es una URL externa, usar el proxy
            try {
                const url = new URL(imgUrl);
                const currentHost = window.location.hostname;
                const currentPort = window.location.port || (window.location.protocol === 'https:' ? '443' : '80');
                const imageHost = url.hostname;
                const imagePort = url.port || (url.protocol === 'https:' ? '443' : '80');
                
                if (imageHost !== currentHost || imagePort !== currentPort) {
                    const baseUrl = (typeof API_BASE_URL !== 'undefined' && API_BASE_URL) 
                        ? API_BASE_URL 
                        : window.location.origin;
                    const correctedBaseUrl = baseUrl.replace(/127\.0\.0\.1:8000/g, '192.168.0.7:8000')
                                                     .replace(/192\.168\.0\.6:8000/g, '192.168.0.7:8000')
                                                     .replace(/192.168.0.7:8000/g, '192.168.0.7:8000');
                    return `${correctedBaseUrl}/api/image-proxy?url=${encodeURIComponent(imgUrl)}`;
                }
            } catch (e) {
                // Si no se puede parsear, retornar la URL original
            }
            
            return imgUrl;
        } else {
            console.warn('buildImageUrl: URL completa contiene arrays JSON:', imgUrl);
            return null;
        }
    }
    
    // Si empieza con /storage/, verificar si es una ruta externa mal formateada
    if (imgUrl.startsWith('/storage/')) {
        // Detectar si es una ruta externa que fue mal formateada (como /storage/resizer/ o /storage/wp-content/)
        const rutasExternas = ['/storage/resizer/', '/storage/wp-content/', '/storage/templates/', 
                               '/storage/cache/', '/storage/yootheme/'];
        const esRutaExterna = rutasExternas.some(ruta => imgUrl.toLowerCase().startsWith(ruta.toLowerCase()));
        
        if (esRutaExterna) {
            // Es una ruta externa mal formateada, extraer la parte después de /storage/
            const rutaExterna = imgUrl.replace(/^\/storage\//, '');
            // Intentar construir la URL externa original (esto es una aproximación)
            // En realidad, estas rutas deberían estar guardadas como URLs completas en la BD
            console.warn('buildImageUrl: Ruta externa mal formateada detectada:', imgUrl);
            return null; // Rechazar estas rutas ya que no podemos determinar la URL externa completa
        }
        
        const baseUrl = (typeof API_BASE_URL !== 'undefined' && API_BASE_URL) 
            ? API_BASE_URL 
            : window.location.origin;
        const correctedBaseUrl = baseUrl.replace(/127\.0\.0\.1:8000/g, '192.168.0.7:8000')
                                         .replace(/192\.168\.0\.6:8000/g, '192.168.0.7:8000')
                                         .replace(/192.168.0.7:8000/g, '192.168.0.7:8000');
        const finalUrl = `${correctedBaseUrl}${imgUrl}`;
        // Validar que la URL final no contenga arrays JSON
        if (!finalUrl.includes('[') && !finalUrl.includes(']')) {
            return finalUrl;
        } else {
            console.warn('buildImageUrl: URL construida contiene arrays JSON:', finalUrl);
            return null;
        }
    }
    
    // Si empieza con storage/, agregar /
    if (imgUrl.startsWith('storage/')) {
        const baseUrl = (typeof API_BASE_URL !== 'undefined' && API_BASE_URL) 
            ? API_BASE_URL 
            : window.location.origin;
        const correctedBaseUrl = baseUrl.replace(/127\.0\.0\.1:8000/g, '192.168.0.7:8000')
                                         .replace(/192\.168\.0\.6:8000/g, '192.168.0.7:8000')
                                         .replace(/192.168.0.7:8000/g, '192.168.0.7:8000');
        const finalUrl = `${correctedBaseUrl}/${imgUrl}`;
        // Validar que la URL final no contenga arrays JSON
        if (!finalUrl.includes('[') && !finalUrl.includes(']')) {
            return finalUrl;
        } else {
            console.warn('buildImageUrl: URL construida contiene arrays JSON:', finalUrl);
            return null;
        }
    }
    
    // Solo procesar si parece ser una ruta de imagen válida
    const extensionesValidas = ['.jpg', '.jpeg', '.png', '.gif', '.webp', '.svg'];
    const tieneExtensionValida = extensionesValidas.some(ext => imgUrl.toLowerCase().includes(ext.toLowerCase()));
    
    if (tieneExtensionValida) {
        const baseUrl = (typeof API_BASE_URL !== 'undefined' && API_BASE_URL) 
            ? API_BASE_URL 
            : window.location.origin;
        const correctedBaseUrl = baseUrl.replace(/127\.0\.0\.1:8000/g, '192.168.0.7:8000')
                                         .replace(/192\.168\.0\.6:8000/g, '192.168.0.7:8000')
                                         .replace(/192.168.0.7:8000/g, '192.168.0.7:8000');
        const finalUrl = `${correctedBaseUrl}/storage/${imgUrl.replace(/^\//, '')}`;
        // Validar que la URL final no contenga arrays JSON
        if (!finalUrl.includes('[') && !finalUrl.includes(']')) {
            return finalUrl;
        } else {
            console.warn('buildImageUrl: URL construida contiene arrays JSON:', finalUrl);
            return null;
        }
    }
    
    // Si no tiene extensión válida, retornar null
    console.warn('Ruta de imagen sin extensión válida:', imgUrl);
    return null;
}

let megaEventoId = null;

document.addEventListener('DOMContentLoaded', async () => {
    const token = localStorage.getItem('token');
    
    if (!token) {
        Swal.fire({
            icon: 'warning',
            title: 'Sesión Expirada',
            text: 'Tu sesión ha expirado. Por favor, inicia sesión nuevamente.',
            confirmButtonText: 'Ir al Login'
        }).then(() => {
            window.location.href = '/login';
        });
        return;
    }

    // Obtener ID de la URL
    // La URL es: /ong/mega-eventos/{id}/detalle
    const pathParts = window.location.pathname.split('/').filter(p => p !== '');
    console.log('Path parts:', pathParts);
    
    // Buscar el índice de 'mega-eventos' y tomar el siguiente elemento como ID
    const megaEventosIndex = pathParts.indexOf('mega-eventos');
    if (megaEventosIndex !== -1 && pathParts[megaEventosIndex + 1]) {
        megaEventoId = pathParts[megaEventosIndex + 1];
    } else {
        // Fallback: intentar obtener el penúltimo elemento
        megaEventoId = pathParts[pathParts.length - 2];
    }

    console.log('Mega Evento ID extraído:', megaEventoId);

    if (!megaEventoId || isNaN(megaEventoId)) {
        const loadingMessage = document.getElementById('loadingMessage');
        loadingMessage.innerHTML = `
            <div class="alert alert-danger" style="background: #f8d7da; border: 1px solid #dc3545; color: #721c24; border-radius: 8px; padding: 1rem;">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Error: ID de mega evento inválido en la URL. URL: ${window.location.pathname}
            </div>
        `;
        return;
    }

    await loadMegaEvento();
});

async function loadMegaEvento() {
    const token = localStorage.getItem('token');
    const loadingMessage = document.getElementById('loadingMessage');
    const content = document.getElementById('megaEventoContent');

    if (!loadingMessage || !content) {
        console.error('Elementos del DOM no encontrados');
        return;
    }

    try {
        console.log('Cargando mega evento ID:', megaEventoId);
        console.log('API URL:', `${API_BASE_URL}/api/mega-eventos/${megaEventoId}`);
        
        const res = await fetch(`${API_BASE_URL}/api/mega-eventos/${megaEventoId}`, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        console.log('Response status:', res.status);
        const data = await res.json();
        console.log('Response data:', data);

        if (!res.ok || !data.success) {
            loadingMessage.innerHTML = `
                <div class="alert alert-danger" style="background: #f8d7da; border: 1px solid #dc3545; color: #721c24; border-radius: 8px; padding: 1rem;">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Error: ${data.error || 'Error al cargar el mega evento'}
                </div>
            `;
            return;
        }

        if (!data.mega_evento) {
            loadingMessage.innerHTML = `
                <div class="alert alert-warning" style="background: #fff3cd; border: 1px solid #ffc107; color: #856404; border-radius: 8px; padding: 1rem;">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    No se encontró información del mega evento
                </div>
            `;
            return;
        }

        const mega = data.mega_evento;
        
        try {
            displayMegaEvento(mega);
            loadingMessage.style.display = 'none';
            content.style.display = 'block';
            
            // Configurar botón de compartir
            configurarBotonesCompartir(megaEventoId, mega);
            // Cargar contador de compartidos
            cargarContadorCompartidosMegaEvento(megaEventoId);
            // Cargar reacciones
            verificarReaccionMegaEvento();
            cargarReaccionesMegaEvento();
            // Cargar participantes
            cargarParticipantesCardMegaEvento();
            // Mostrar patrocinadores
            mostrarPatrocinadoresMegaEvento(mega);
            // Iniciar actualización en tiempo real
            iniciarActualizacionTiempoRealMegaEvento();
            console.log('Mega evento mostrado correctamente');
        } catch (displayError) {
            console.error('Error al mostrar el mega evento:', displayError);
            loadingMessage.innerHTML = `
                <div class="alert alert-danger" style="background: #f8d7da; border: 1px solid #dc3545; color: #721c24; border-radius: 8px; padding: 1rem;">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Error al mostrar el mega evento: ${displayError.message}
                </div>
            `;
        }

    } catch (error) {
        console.error('Error en loadMegaEvento:', error);
        loadingMessage.innerHTML = `
            <div class="alert alert-danger" style="background: #f8d7da; border: 1px solid #dc3545; color: #721c24; border-radius: 8px; padding: 1rem;">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Error de conexión al cargar el mega evento: ${error.message}
            </div>
        `;
    }
}

function displayMegaEvento(mega) {
    
    try {
        // Helper para construir URL de imagen (usar la función global buildImageUrl)
        // Ya no es necesario definir una función local, se usa la global

        // Banner con imagen principal
        const banner = document.getElementById('eventBanner');
        const bannerImage = document.getElementById('bannerImage');
        if (bannerImage && mega.imagenes) {
            // Usar la misma función de parseo que para las imágenes principales
            function parsearImagenesBanner(imagenesInput) {
                if (!imagenesInput) return [];
                if (Array.isArray(imagenesInput)) {
                    return imagenesInput;
                }
                if (typeof imagenesInput === 'string') {
                    const trimmed = imagenesInput.trim();
                    if (trimmed.startsWith('[') && trimmed.endsWith(']')) {
                        try {
                            const parsed = JSON.parse(trimmed);
                            return Array.isArray(parsed) ? parsed : [];
                        } catch (e) {
                            return [];
                        }
                    }
                    return [trimmed];
                }
                return [];
            }
            
            // Función recursiva para extraer URLs válidas (misma que la principal)
            function extraerUrlsValidasBanner(input) {
                const urls = [];
                if (!input) return urls;
                
                if (Array.isArray(input)) {
                    input.forEach(item => {
                        urls.push(...extraerUrlsValidasBanner(item));
                    });
                    return urls;
                }
                
                if (typeof input === 'string') {
                    const trimmed = input.trim();
                    if (trimmed === '') return urls;
                    
                    if (trimmed.startsWith('[') && trimmed.endsWith(']')) {
                        try {
                            const parsed = JSON.parse(trimmed);
                            return extraerUrlsValidasBanner(parsed);
                        } catch (e) {
                            return urls;
                        }
                    }
                    
                    // Caso especial: /storage/["http://..."]
                    if (trimmed.includes('[') || trimmed.includes(']')) {
                        const storageJsonMatch = trimmed.match(/\/storage\/\[(.*?)\]$/);
                        if (storageJsonMatch) {
                            try {
                                const jsonStr = '[' + storageJsonMatch[1] + ']';
                                const parsed = JSON.parse(jsonStr);
                                return extraerUrlsValidasBanner(parsed);
                            } catch (e) {
                                return urls;
                            }
                        }
                        
                        // Intentar extraer JSON de cualquier parte
                        try {
                            const jsonMatch = trimmed.match(/\[.*?\]/);
                            if (jsonMatch) {
                                const parsed = JSON.parse(jsonMatch[0]);
                                return extraerUrlsValidasBanner(parsed);
                            }
                        } catch (e) {
                            return urls;
                        }
                        return urls;
                    }
                    
                    // Filtrar rutas inválidas
                    const rutasInvalidas = ['/templates/', '/cache/', '/yootheme/', 'templates/', 'cache/', 'yootheme/'];
                    if (!rutasInvalidas.some(ruta => trimmed.toLowerCase().includes(ruta.toLowerCase()))) {
                        urls.push(trimmed);
                    }
                }
                
                return urls;
            }
            
            // Parsear y extraer URLs válidas
            const imagenesParseadasBanner = parsearImagenesBanner(mega.imagenes);
            const urlsValidasBanner = extraerUrlsValidasBanner(imagenesParseadasBanner);
            
            // Usar la primera URL válida para el banner
            if (urlsValidasBanner.length > 0) {
                const primeraImagen = buildImageUrl(urlsValidasBanner[0]);
                if (primeraImagen && typeof primeraImagen === 'string' && !primeraImagen.includes('[') && !primeraImagen.includes(']')) {
                    bannerImage.style.backgroundImage = `url(${primeraImagen})`;
                }
            }
        }

        // Título
        const tituloEl = document.getElementById('titulo');
        if (tituloEl) {
            tituloEl.textContent = mega.titulo || 'Sin título';
        }

    // Categoría badge (en el banner)
    const categoriaBadgeEl = document.getElementById('categoriaBadge');
    if (categoriaBadgeEl) {
        if (mega.categoria) {
            categoriaBadgeEl.innerHTML = '<span class="badge badge-success" style="background: #00A36C !important; color: white !important;">' + mega.categoria.charAt(0).toUpperCase() + mega.categoria.slice(1) + '</span>';
        } else {
            categoriaBadgeEl.style.display = 'none';
        }
    }

        // Estado badge (en el banner)
        const estadoBadges = {
            'planificacion': { class: 'badge-secondary', text: 'Planificación' },
            'activo': { class: 'badge-success', text: 'Activo' },
            'en_curso': { class: 'badge-info', text: 'En Curso' },
            'finalizado': { class: 'badge-primary', text: 'Finalizado' },
            'cancelado': { class: 'badge-danger', text: 'Cancelado' }
        };
        const estadoInfo = estadoBadges[mega.estado] || { class: 'badge-secondary', text: mega.estado || 'N/A' };
        const estadoBadgeEl = document.getElementById('estadoBadge');
        if (estadoBadgeEl) {
            estadoBadgeEl.className = `badge ${estadoInfo.class}`;
            estadoBadgeEl.textContent = estadoInfo.text;
        }

        // Público/Privado badge (en el banner)
        const publicoBadgeEl = document.getElementById('publicoBadge');
        if (publicoBadgeEl) {
            if (mega.es_publico) {
                publicoBadgeEl.className = 'badge badge-info';
                publicoBadgeEl.textContent = 'Público';
            } else {
                publicoBadgeEl.className = 'badge badge-secondary';
                publicoBadgeEl.textContent = 'Privado';
            }
        }

        // Descripción
        const descripcionEl = document.getElementById('descripcion');
        if (descripcionEl) {
            const descripcion = mega.descripcion || 'Sin descripción disponible.';
            descripcionEl.textContent = descripcion;
        }

        // Función para formatear fechas desde PostgreSQL sin conversión de zona horaria
        function formatearFechaPostgreSQL(fechaStr) {
            if (!fechaStr) return 'Fecha no especificada';
            try {
                let fechaObj;
                
                if (typeof fechaStr === 'string') {
                    fechaStr = fechaStr.trim();
                    
                    // Patrones para diferentes formatos de fecha
                    const mysqlPattern = /^(\d{4})-(\d{2})-(\d{2})[\sT](\d{2}):(\d{2}):(\d{2})(?:\.\d+)?(?:Z|[+-]\d{2}:\d{2})?$/;
                    const isoPattern = /^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})(?:\.\d+)?(?:Z|[+-]\d{2}:\d{2})?$/;
                    
                    let match = fechaStr.match(mysqlPattern) || fechaStr.match(isoPattern);
                    
                    if (match) {
                        // Parsear manualmente para evitar conversión UTC
                        const [, year, month, day, hour, minute, second] = match;
                        fechaObj = new Date(
                            parseInt(year, 10),
                            parseInt(month, 10) - 1,
                            parseInt(day, 10),
                            parseInt(hour, 10),
                            parseInt(minute, 10),
                            parseInt(second || 0, 10)
                        );
                    } else {
                        fechaObj = new Date(fechaStr);
                    }
                } else {
                    fechaObj = new Date(fechaStr);
                }
                
                if (isNaN(fechaObj.getTime())) return fechaStr;
                
                const año = fechaObj.getFullYear();
                const mes = fechaObj.getMonth();
                const dia = fechaObj.getDate();
                const horas = fechaObj.getHours();
                const minutos = fechaObj.getMinutes();
                
                const meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 
                              'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
                
                const horaFormateada = String(horas).padStart(2, '0');
                const minutoFormateado = String(minutos).padStart(2, '0');
                
                return `${dia} de ${meses[mes]} de ${año}, ${horaFormateada}:${minutoFormateado}`;
            } catch (error) {
                console.error('Error formateando fecha:', error);
                return fechaStr;
            }
        }

        // Fechas - Corregidas para obtener correctamente desde PostgreSQL
        const fechaInicioEl = document.getElementById('fecha_inicio');
        if (fechaInicioEl) {
            if (mega.fecha_inicio) {
                fechaInicioEl.textContent = formatearFechaPostgreSQL(mega.fecha_inicio);
            } else {
                fechaInicioEl.textContent = '-';
            }
        }

        const fechaFinEl = document.getElementById('fecha_fin');
        if (fechaFinEl) {
            if (mega.fecha_fin) {
                fechaFinEl.textContent = formatearFechaPostgreSQL(mega.fecha_fin);
            } else {
                fechaFinEl.textContent = '-';
            }
        }

        // Ubicación - Mostrar de forma simple y directa
        const ubicacionContainer = document.getElementById('ubicacionContainer');
        if (!ubicacionContainer) {
            console.error('No se encontró el elemento ubicacionContainer');
            throw new Error('Elemento ubicacionContainer no encontrado');
        }
        
        const ubicacion = mega.ubicacion || 'No especificada';
        
        // Mostrar la ubicación de forma simple y clara
        if (ubicacion && ubicacion !== 'No especificada' && ubicacion.trim() !== '') {
            ubicacionContainer.innerHTML = `
                <p class="mb-0 text-muted">
                    <i class="fas fa-map-marker-alt mr-2 text-danger"></i>${ubicacion}
                </p>
            `;
        } else {
            ubicacionContainer.innerHTML = `
                <p class="mb-0 text-muted">
                    <i class="fas fa-exclamation-circle mr-2 text-warning"></i>Ubicación no especificada
                </p>
            `;
        }

        // Capacidad máxima - mostrar correctamente
        const capacidadEl = document.getElementById('capacidad_maxima');
        if (capacidadEl) {
            // Obtener el valor crudo si está disponible, o usar el valor normalizado
            const capacidadValue = mega.capacidad_maxima;
            if (capacidadValue !== null && capacidadValue !== undefined && capacidadValue !== '' && !isNaN(capacidadValue) && parseInt(capacidadValue) > 0) {
                const capacidadNum = parseInt(capacidadValue);
                capacidadEl.textContent = `${capacidadNum} personas`;
                capacidadEl.style.fontWeight = '600';
                capacidadEl.style.color = '#0C2B44';
            } else {
                capacidadEl.textContent = 'Sin límite de capacidad';
                capacidadEl.style.fontStyle = 'italic';
                capacidadEl.style.color = '#6c757d';
            }
        }

        // ONG Organizadora - Con avatar
        const ongEl = document.getElementById('ong_organizadora');
        const ongAvatarContainer = document.getElementById('ongAvatarContainer');
        if (ongEl && ongAvatarContainer) {
            
            // Intentar obtener la información de la ONG de diferentes formas
            let ong = null;
            if (mega.ong_principal) {
                ong = mega.ong_principal;
            } else if (mega.ongPrincipal) {
                ong = mega.ongPrincipal;
            }
            
            if (ong) {
                console.log('ONG encontrada:', ong);
                console.log('Propiedades de ONG:', Object.keys(ong));
                const nombreOng = ong.nombre_ong || ong.nombre || '-';
                ongEl.textContent = nombreOng;
                
                // Obtener avatar de la ONG - intentar múltiples propiedades
                let fotoPerfil = ong.foto_perfil_url || ong.foto_perfil || ong.avatar || null;
                console.log('Foto perfil ONG (raw):', fotoPerfil);
                
                // Si la URL viene del backend, puede que ya esté normalizada, pero verificamos
                if (fotoPerfil && typeof fotoPerfil === 'string' && fotoPerfil.trim() !== '') {
                    // Normalizar URL de la imagen
                    const avatarUrl = buildImageUrl(fotoPerfil);
                    console.log('Avatar URL normalizada:', avatarUrl);
                    
                    // Crear contenedor limpio sin fallback verde
                    ongAvatarContainer.innerHTML = '';
                    
                    // Crear imagen sin fallback verde
                    const img = document.createElement('img');
                    img.src = avatarUrl;
                    img.alt = nombreOng;
                    img.className = 'rounded-circle';
                    img.style.cssText = 'width: 70px; height: 70px; object-fit: cover; border: 2px solid #e9ecef; box-shadow: 0 2px 8px rgba(0,0,0,0.1); display: block;';
                    
                    img.onerror = function() {
                        console.error('Error cargando avatar de ONG:', avatarUrl);
                        this.style.display = 'none';
                    };
                    
                    img.onload = function() {
                        console.log('Avatar de ONG cargado correctamente:', avatarUrl);
                    };
                    
                    ongAvatarContainer.appendChild(img);
                } else {
                    console.log('No hay foto_perfil disponible');
                    ongAvatarContainer.innerHTML = '';
                }
            } else {
                console.warn('No se encontró información de la ONG organizadora');
                ongEl.textContent = '-';
                ongAvatarContainer.innerHTML = '';
            }
        } else {
            console.error('Elementos del DOM no encontrados:', { ongEl, ongAvatarContainer });
        }

        // Fechas del sistema
        const fechaCreacionEl = document.getElementById('fecha_creacion');
        if (fechaCreacionEl) {
            if (mega.fecha_creacion) {
                const fechaCreacion = new Date(mega.fecha_creacion);
                fechaCreacionEl.textContent = fechaCreacion.toLocaleString('es-ES');
            } else {
                fechaCreacionEl.textContent = '-';
            }
        }

        const fechaActualizacionEl = document.getElementById('fecha_actualizacion');
        if (fechaActualizacionEl) {
            if (mega.fecha_actualizacion) {
                const fechaActualizacion = new Date(mega.fecha_actualizacion);
                fechaActualizacionEl.textContent = fechaActualizacion.toLocaleString('es-ES');
            } else {
                fechaActualizacionEl.textContent = '-';
            }
        }

    // Estados en el sidebar (usando innerHTML para badges con estilos)
    const estadoBadgesHTML = {
        'planificacion': '<span class="badge badge-secondary" style="background: #333333 !important; color: white !important;">Planificación</span>',
        'activo': '<span class="badge badge-success" style="background: #00A36C !important; color: white !important;">Activo</span>',
        'en_curso': '<span class="badge badge-info" style="background: #0C2B44 !important; color: white !important;">En Curso</span>',
        'finalizado': '<span class="badge badge-primary" style="background: #0C2B44 !important; color: white !important;">Finalizado</span>',
        'cancelado': '<span class="badge badge-danger" style="background: #dc3545 !important; color: white !important;">Cancelado</span>'
    };
    
    // Solo establecer el estado en el sidebar si el elemento existe
    const estadoElement = document.getElementById('estado');
    if (estadoElement) {
        const estadoBadgesHTMLStyled = {
            'planificacion': '<span class="badge badge-secondary" style="background: #6c757d !important; color: white !important; font-size: 0.9rem; padding: 0.5em 1em; border-radius: 50px; font-weight: 600;">Planificación</span>',
            'activo': '<span class="badge badge-success" style="background: #00A36C !important; color: white !important; font-size: 0.9rem; padding: 0.5em 1em; border-radius: 50px; font-weight: 600;">Activo</span>',
            'en_curso': '<span class="badge badge-info" style="background: #17a2b8 !important; color: white !important; font-size: 0.9rem; padding: 0.5em 1em; border-radius: 50px; font-weight: 600;">En Curso</span>',
            'finalizado': '<span class="badge badge-primary" style="background: #0C2B44 !important; color: white !important; font-size: 0.9rem; padding: 0.5em 1em; border-radius: 50px; font-weight: 600;">Finalizado</span>',
            'cancelado': '<span class="badge badge-danger" style="background: #dc3545 !important; color: white !important; font-size: 0.9rem; padding: 0.5em 1em; border-radius: 50px; font-weight: 600;">Cancelado</span>'
        };
        estadoElement.innerHTML = estadoBadgesHTMLStyled[mega.estado] || '<span class="badge badge-secondary" style="background: #6c757d !important; color: white !important; font-size: 0.9rem; padding: 0.5em 1em; border-radius: 50px; font-weight: 600;">' + (mega.estado || 'N/A') + '</span>';
    }

    const esPublicoElement = document.getElementById('es_publico');
    if (esPublicoElement) {
        esPublicoElement.innerHTML = mega.es_publico 
            ? '<span class="badge badge-info" style="background: #17a2b8 !important; color: white !important; font-size: 0.9rem; padding: 0.5em 1em; border-radius: 50px; font-weight: 600;">Público</span>' 
            : '<span class="badge badge-secondary" style="background: #6c757d !important; color: white !important; font-size: 0.9rem; padding: 0.5em 1em; border-radius: 50px; font-weight: 600;">Privado</span>';
    }

        // Imágenes (el modelo ya devuelve URLs completas)
        
        // Asegurar que las imágenes sean un array
        let imagenes = [];
        
        // Función helper para parsear imágenes
        function parsearImagenes(imagenesInput) {
            if (!imagenesInput) return [];
            
            // Si ya es un array, usarlo directamente
            if (Array.isArray(imagenesInput)) {
                return imagenesInput;
            }
            
            // Si es string, intentar parsearlo como JSON
            if (typeof imagenesInput === 'string') {
                // Verificar si parece ser un JSON array
                const trimmed = imagenesInput.trim();
                if (trimmed.startsWith('[') && trimmed.endsWith(']')) {
                    try {
                        const parsed = JSON.parse(trimmed);
                        if (Array.isArray(parsed)) {
                            return parsed;
                        }
                    } catch (e) {
                        console.error('Error parseando imágenes como JSON:', e);
                    }
                }
                // Si no es JSON válido, tratarlo como un solo string
                return [trimmed];
            }
            
            return [];
        }
        
        // Parsear las imágenes
        const imagenesParseadas = parsearImagenes(mega.imagenes);
        
        // Función recursiva para extraer URLs válidas de cualquier estructura
        function extraerUrlsValidas(input) {
            const urls = [];
            
            if (!input) return urls;
            
            // Si es un array, procesar cada elemento
            if (Array.isArray(input)) {
                input.forEach(item => {
                    urls.push(...extraerUrlsValidas(item));
                });
                return urls;
            }
            
            // Si es un string
            if (typeof input === 'string') {
                const trimmed = input.trim();
                if (trimmed === '') return urls;
                
                // Si parece ser un JSON array, parsearlo recursivamente
                if (trimmed.startsWith('[') && trimmed.endsWith(']')) {
                    try {
                        const parsed = JSON.parse(trimmed);
                        return extraerUrlsValidas(parsed);
                    } catch (e) {
                        console.warn('Error parseando JSON recursivo:', e);
                        return urls;
                    }
                }
                
                // Validar que no contenga arrays JSON
                if (trimmed.includes('[') || trimmed.includes(']')) {
                    // Caso especial: si la URL contiene /storage/["http://..."] extraer solo el JSON
                    const storageJsonMatch = trimmed.match(/\/storage\/\[(.*?)\]$/);
                    if (storageJsonMatch) {
                        try {
                            const jsonStr = '[' + storageJsonMatch[1] + ']';
                            const parsed = JSON.parse(jsonStr);
                            return extraerUrlsValidas(parsed);
                        } catch (e) {
                            // Silenciar el error, ya que se está manejando
                        }
                    }
                    
                    // Intentar extraer URLs del string si contiene JSON en cualquier parte
                    try {
                        // Buscar el primer array JSON en el string
                        const jsonMatch = trimmed.match(/\[.*?\]/);
                        if (jsonMatch) {
                            const parsed = JSON.parse(jsonMatch[0]);
                            return extraerUrlsValidas(parsed);
                        }
                    } catch (e) {
                        // Silenciar el error, ya que se está manejando
                    }
                    return urls; // Rechazar si contiene arrays JSON y no se pudo parsear
                }
                
                // Filtrar rutas inválidas
                const rutasInvalidas = ['/templates/', '/cache/', '/yootheme/', 'templates/', 'cache/', 'yootheme/'];
                if (!rutasInvalidas.some(ruta => trimmed.toLowerCase().includes(ruta.toLowerCase()))) {
                    urls.push(trimmed);
                }
            }
            
            return urls;
        }
        
        // Extraer todas las URLs válidas usando la función recursiva
        imagenes = extraerUrlsValidas(imagenesParseadas);
        
        // Eliminar duplicados
        imagenes = [...new Set(imagenes)];
        
        const imagenesContainer = document.getElementById('imagenesContainer');
        const imagenesCount = document.getElementById('imagenesCount');
        
        if (!imagenesContainer || !imagenesCount) {
            console.error('No se encontraron los elementos de imágenes');
            throw new Error('Elementos de imágenes no encontrados');
        }
    
    // Actualizar contador
    imagenesCount.textContent = imagenes.length;
    
    if (imagenes.length === 0) {
        imagenesContainer.innerHTML = `
            <div class="col-12">
                <div class="text-center py-5 bg-light rounded">
                    <div class="bg-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-images fa-3x text-white"></i>
                    </div>
                    <h5 class="font-weight-bold text-dark mb-2">No hay imágenes disponibles</h5>
                    <p class="text-muted mb-0">Las imágenes aparecerán aquí cuando se agreguen al mega evento.</p>
                </div>
            </div>
        `;
    } else {
        imagenesContainer.innerHTML = '';
        imagenes.forEach((imgUrlOriginal, index) => {
            if (!imgUrlOriginal || typeof imgUrlOriginal !== 'string' || imgUrlOriginal.trim() === '') {
                console.warn('Imagen inválida en índice', index, ':', imgUrlOriginal);
                return;
            }
            
            let imgUrl = imgUrlOriginal.trim();
            
            // Verificar que no sea un JSON string completo (seguridad adicional)
            if (imgUrl.startsWith('[') && imgUrl.endsWith(']')) {
                console.warn('Imagen parece ser un JSON array completo, intentando parsear:', imgUrl.substring(0, 100));
                try {
                    const parsed = JSON.parse(imgUrl);
                    if (Array.isArray(parsed) && parsed.length > 0 && typeof parsed[0] === 'string') {
                        // Usar el primer elemento del array
                        imgUrl = parsed[0].trim();
                        if (!imgUrl) {
                            console.warn('Array JSON parseado pero primer elemento vacío');
                            return;
                        }
                    } else {
                        console.warn('Array JSON parseado pero no válido');
                        return;
                    }
                } catch (e) {
                    console.warn('Error parseando JSON de imagen:', e);
                    return;
                }
            }
            
            // Validar que imgUrl sea un string válido después del procesamiento
            if (typeof imgUrl !== 'string' || imgUrl.trim() === '') {
                console.warn('Imagen procesada inválida:', imgUrl);
                return;
            }
            
            // Filtrar rutas inválidas (solo para rutas locales, no URLs completas)
            const esUrlCompleta = imgUrl.startsWith('http://') || imgUrl.startsWith('https://');
            const rutasInvalidas = ['/templates/', '/cache/', '/yootheme/', '/resizer/', '/wp-content/',
                                    'templates/', 'cache/', 'yootheme/', 'resizer/', 'wp-content/'];
            
            // Solo filtrar si NO es una URL completa de internet
            if (!esUrlCompleta) {
                const esRutaInvalida = rutasInvalidas.some(ruta => imgUrl.toLowerCase().includes(ruta.toLowerCase()));
            if (esRutaInvalida) {
                console.warn('Ruta de imagen inválida filtrada:', imgUrl);
                return; // Saltar esta imagen
                }
            }
            
            // Guardar el valor limpio para usar en el manejo de errores
            const imgUrlLimpio = imgUrl;
            
            // Usar buildImageUrl para procesar y validar la URL
            // buildImageUrl maneja arrays JSON, URLs completas, y rutas relativas
            const fullUrl = buildImageUrl(imgUrlLimpio);
            
            if (!fullUrl) {
                console.warn('No se pudo construir URL para imagen:', imgUrlLimpio);
                return; // Saltar esta imagen si no se puede construir la URL
            }
            
            // Validar que fullUrl sea una URL válida (no contenga arrays JSON)
            if (typeof fullUrl !== 'string' || fullUrl.includes('[') || fullUrl.includes(']')) {
                console.error('URL construida inválida (contiene JSON):', fullUrl);
                return;
            }

            // Crear elementos con diseño minimalista mejorado
            const colDiv = document.createElement('div');
            colDiv.className = 'col-md-4 col-sm-6 mb-4';
            
            const imgWrapper = document.createElement('div');
            imgWrapper.className = 'position-relative overflow-hidden';
            imgWrapper.style.cssText = 'height: 240px; background: #f8f9fa; border-radius: 16px; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(0,0,0,0.08); overflow: hidden;';
            imgWrapper.onmouseenter = function() { 
                this.style.transform = 'translateY(-6px)'; 
                this.style.boxShadow = '0 8px 24px rgba(12, 43, 68, 0.15)';
            };
            imgWrapper.onmouseleave = function() { 
                this.style.transform = 'translateY(0)'; 
                this.style.boxShadow = '0 2px 8px rgba(0,0,0,0.08)';
            };
            imgWrapper.onclick = () => abrirImagen(fullUrl, index + 1);
            
            // Overlay para efecto hover mejorado
            const overlay = document.createElement('div');
            overlay.className = 'position-absolute w-100 h-100 d-flex align-items-center justify-content-center';
            overlay.style.cssText = 'top: 0; left: 0; background: linear-gradient(135deg, rgba(12, 43, 68, 0) 0%, rgba(0, 163, 108, 0) 100%); transition: all 0.3s ease; pointer-events: none;';
            imgWrapper.onmouseenter = function() { 
                overlay.style.background = 'linear-gradient(135deg, rgba(12, 43, 68, 0.7) 0%, rgba(0, 163, 108, 0.7) 100%)';
                icon.style.opacity = '1';
                icon.style.transform = 'scale(1.2)';
            };
            imgWrapper.onmouseleave = function() { 
                overlay.style.background = 'linear-gradient(135deg, rgba(12, 43, 68, 0) 0%, rgba(0, 163, 108, 0) 100%)';
                icon.style.opacity = '0';
                icon.style.transform = 'scale(1)';
            };
            
            const icon = document.createElement('i');
            icon.className = 'fas fa-search-plus fa-2x text-white';
            icon.style.cssText = 'opacity: 0; transition: all 0.3s ease; transform: scale(1);';
            
            overlay.appendChild(icon);
            
            const img = document.createElement('img');
            img.src = fullUrl;
            img.className = 'w-100 h-100';
            img.style.cssText = 'object-fit: cover; display: block;';
            img.alt = `Imagen ${index + 1}`;
            img.loading = 'lazy'; // Lazy loading para mejor rendimiento
            
            // Manejo de errores mejorado con múltiples intentos
            img.onerror = function() {
                console.error('Error cargando imagen:', fullUrl);
                this.onerror = null;
                
                // Validar que imgUrlLimpio sea un string válido (no un array JSON)
                if (!imgUrlLimpio || typeof imgUrlLimpio !== 'string' || imgUrlLimpio.includes('[') || imgUrlLimpio.includes(']')) {
                    console.error('URL limpia inválida, usando placeholder:', imgUrlLimpio);
                    this.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="400" height="220"%3E%3Crect fill="%23f8f9fa" width="400" height="220"/%3E%3Ctext x="50%25" y="50%25" text-anchor="middle" dy=".3em" fill="%23adb5bd" font-family="Arial" font-size="14"%3EImagen no disponible%3C/text%3E%3C/svg%3E';
                    this.style.objectFit = 'contain';
                    this.style.padding = '20px';
                    return;
                }
                
                // Intentar con diferentes formatos de URL usando solo imgUrlLimpio
                // Primero, reemplazar IPs antiguas si es una URL completa
                let imgUrlProcessed = imgUrlLimpio;
                if (imgUrlProcessed.startsWith('http://') || imgUrlProcessed.startsWith('https://')) {
                    imgUrlProcessed = imgUrlProcessed.replace(/http:\/\/127\.0\.0\.1:8000/g, 'http://192.168.0.7:8000');
                    imgUrlProcessed = imgUrlProcessed.replace(/https:\/\/127\.0\.0\.1:8000/g, 'https://192.168.0.7:8000');
                    imgUrlProcessed = imgUrlProcessed.replace(/http:\/\/192\.168\.0\.6:8000/g, 'http://192.168.0.7:8000');
                    imgUrlProcessed = imgUrlProcessed.replace(/https:\/\/192\.168\.0\.6:8000/g, 'https://192.168.0.7:8000');
                    imgUrlProcessed = imgUrlProcessed.replace(/http:\/\/192.168.0.7:8000/g, 'http://192.168.0.7:8000');
                    imgUrlProcessed = imgUrlProcessed.replace(/https:\/\/192.168.0.7:8000/g, 'https://192.168.0.7:8000');
                }
                
                const baseUrl = (typeof API_BASE_URL !== 'undefined' && API_BASE_URL) 
                    ? API_BASE_URL 
                    : window.location.origin;
                const correctedBaseUrl = baseUrl.replace(/127\.0\.0\.1:8000/g, '192.168.0.7:8000')
                                                 .replace(/192\.168\.0\.6:8000/g, '192.168.0.7:8000')
                                                 .replace(/192.168.0.7:8000/g, '192.168.0.7:8000');
                
                const altUrls = [
                    // Si ya es URL completa, usarla directamente (después de reemplazar IPs)
                    (imgUrlProcessed.startsWith('http://') || imgUrlProcessed.startsWith('https://')) ? imgUrlProcessed : null,
                    // Si empieza con /storage/, construir URL completa
                    imgUrlProcessed.startsWith('/storage/') ? `${correctedBaseUrl}${imgUrlProcessed}` : null,
                    // Si empieza con storage/, construir URL completa
                    imgUrlProcessed.startsWith('storage/') ? `${correctedBaseUrl}/${imgUrlProcessed}` : null,
                    // Si es una ruta relativa sin prefijo, agregar /storage/
                    (!imgUrlProcessed.startsWith('http://') && !imgUrlProcessed.startsWith('https://') && 
                     !imgUrlProcessed.startsWith('/storage/') && !imgUrlProcessed.startsWith('storage/')) 
                        ? `${correctedBaseUrl}/storage/${imgUrlProcessed.replace(/^\//, '')}` : null
                ].filter(url => {
                    // Filtrar URLs inválidas que contengan arrays JSON o que sean iguales a la URL que falló
                    return url && 
                           typeof url === 'string' && 
                           url !== fullUrl && 
                           !url.includes('[') && 
                           !url.includes(']') &&
                           !url.includes('/storage/http://') && // Evitar URLs dobles
                           !url.includes('/storage/https://') && // Evitar URLs dobles
                           url.trim() !== '';
                });
                
                let attemptIndex = 0;
                const tryNextUrl = () => {
                    if (attemptIndex < altUrls.length) {
                        const nextUrl = altUrls[attemptIndex];
                        // Validar que la URL no contenga arrays JSON antes de intentar cargarla
                        if (nextUrl && typeof nextUrl === 'string' && !nextUrl.includes('[') && !nextUrl.includes(']')) {
                            this.src = nextUrl;
                            attemptIndex++;
                            this.onerror = tryNextUrl;
                        } else {
                            attemptIndex++;
                            tryNextUrl();
                        }
                    } else {
                        // Si todas fallan, usar placeholder SVG
                        this.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="400" height="220"%3E%3Crect fill="%23f8f9fa" width="400" height="220"/%3E%3Ctext x="50%25" y="50%25" text-anchor="middle" dy=".3em" fill="%23adb5bd" font-family="Arial" font-size="14"%3EImagen no disponible%3C/text%3E%3C/svg%3E';
                        this.style.objectFit = 'contain';
                        this.style.padding = '20px';
                    }
                };
                tryNextUrl();
            };
            
            imgWrapper.appendChild(img);
            imgWrapper.appendChild(overlay);
            colDiv.appendChild(imgWrapper);
            imagenesContainer.appendChild(colDiv);
        });
    }

        // Link de editar
        const editLinkEl = document.getElementById('editLink');
        if (editLinkEl) {
            editLinkEl.href = `/ong/mega-eventos/${megaEventoId}/editar`;
        }
        
        const seguimientoLinkEl = document.getElementById('seguimientoLink');
        if (seguimientoLinkEl) {
            seguimientoLinkEl.href = `/ong/mega-eventos/${megaEventoId}/seguimiento`;
        }

        // Mapa - Implementación mejorada igual a eventos/detalles
        const mapContainer = document.getElementById('map');
        if (mapContainer) {
            const inicializarMapa = async () => {
                let lat = mega.lat;
                let lng = mega.lng;
                let direccionCompleta = mega.ubicacion || '';

                // Si no hay coordenadas pero hay ubicación, hacer geocodificación
                if ((!lat || !lng) && direccionCompleta && direccionCompleta.trim() !== '') {
                    try {
                        // Usar Nominatim (OpenStreetMap) para geocodificación
                        const geocodeUrl = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(direccionCompleta)}&limit=1`;
                        const geocodeRes = await fetch(geocodeUrl, {
                            headers: {
                                'User-Agent': 'MegaEventoApp/1.0'
                            }
                        });
                        const geocodeData = await geocodeRes.json();
                        
                        if (geocodeData && geocodeData.length > 0) {
                            lat = parseFloat(geocodeData[0].lat);
                            lng = parseFloat(geocodeData[0].lon);
                        }
                    } catch (error) {
                        console.warn('Error en geocodificación:', error);
                    }
                }

                // Validar coordenadas antes de mostrar el mapa
                if (lat && lng && !isNaN(parseFloat(lat)) && !isNaN(parseFloat(lng))) {
                    // Asegurar que el contenedor esté visible y tenga tamaño
                    mapContainer.style.display = 'block';
                    mapContainer.style.height = '350px';
                    mapContainer.style.width = '100%';
                    
                    // Limpiar contenido previo
                    mapContainer.innerHTML = '';
                    
                    // Esperar a que el DOM esté completamente listo
                    setTimeout(() => {
                        try {
                            // Verificar que Leaflet esté disponible
                            if (typeof L === 'undefined') {
                                console.error('Leaflet no está cargado');
                                mapContainer.innerHTML = '<div class="alert alert-warning p-3 m-0">Error: La librería de mapas no está cargada. Por favor, recarga la página.</div>';
                                return;
                            }
                            
                            // Limpiar cualquier mapa anterior
                            if (window.megaEventoMapa) {
                                try {
                                    window.megaEventoMapa.remove();
                                } catch (e) {
                                    console.warn('Error al remover mapa anterior:', e);
                                }
                            }
                            
                            const latNum = parseFloat(lat);
                            const lngNum = parseFloat(lng);
                            
                            // Inicializar el mapa con opciones mejoradas
                            const map = L.map('map', {
                                zoomControl: true,
                                scrollWheelZoom: true,
                                doubleClickZoom: true,
                                boxZoom: true,
                                keyboard: true,
                                dragging: true,
                                touchZoom: true
                            }).setView([latNum, lngNum], 13);
                            
                            // Guardar referencia global
                            window.megaEventoMapa = map;
                            
                            // Agregar capa de tiles con opciones mejoradas
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '© OpenStreetMap contributors',
                                maxZoom: 19,
                                minZoom: 3
                    }).addTo(map);
                            
                            // Esperar a que los tiles se carguen
                            map.whenReady(() => {
                                // Agregar marcador con popup mejorado
                                const marker = L.marker([latNum, lngNum]).addTo(map);
                                const popupContent = `
                                    <div style="padding: 0.5rem; min-width: 200px;">
                                        <strong style="color: #0C2B44; font-size: 1rem; display: block; margin-bottom: 0.25rem;">${direccionCompleta || 'Ubicación del mega evento'}</strong>
                                        ${mega.ciudad ? `<small style="color: #6c757d; display: block;">${mega.ciudad}</small>` : ''}
                                    </div>
                                `;
                                marker.bindPopup(popupContent).openPopup();
                                
                                // Ajustar el mapa múltiples veces para asegurar renderizado completo
                                setTimeout(() => {
                                    map.invalidateSize();
                                }, 100);
                                
                                setTimeout(() => {
                                    map.invalidateSize();
                                }, 300);
                                
                                setTimeout(() => {
                                    map.invalidateSize();
                                }, 500);
                                
                                // Forzar actualización después de un segundo
                                setTimeout(() => {
                                    map.invalidateSize();
                                    map.setView([latNum, lngNum], map.getZoom());
                                }, 1000);
                            });
                            
                        } catch (error) {
                            console.error('Error inicializando mapa:', error);
                            mapContainer.innerHTML = `<div class="alert alert-danger p-3 m-0">Error al cargar el mapa: ${error.message}</div>`;
                        }
                    }, 500);
                } else {
                    mapContainer.style.display = 'none';
                }
            };

            // Inicializar el mapa cuando el DOM esté listo
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', inicializarMapa);
            } else {
            inicializarMapa();
            }
        }
        
        // Guardar información del mega evento para compartir
        if (window.megaEventoParaCompartir) {
            window.megaEventoParaCompartir.titulo = mega.titulo || 'Mega Evento';
            window.megaEventoParaCompartir.descripcion = mega.descripcion || '';
        }
        
        console.log('displayMegaEvento completado exitosamente');
    } catch (error) {
        console.error('Error en displayMegaEvento:', error);
        throw error; // Re-lanzar el error para que loadMegaEvento lo capture
    }
}

// Función para abrir imagen en modal o nueva ventana
function abrirImagen(url, index) {
    Swal.fire({
        imageUrl: url,
        imageAlt: 'Imagen ' + index + ' del mega evento',
        showCloseButton: true,
        showConfirmButton: false,
        width: '90%',
        padding: '1rem',
        background: 'rgba(0,0,0,0.9)',
        customClass: {
            popup: 'swal-image-popup'
        }
    });
}

// Configurar botones de compartir
function configurarBotonesCompartir(megaEventoId, mega) {
    const btnCompartir = document.getElementById('btnCompartir');
    
    if (btnCompartir) {
        btnCompartir.onclick = () => {
            mostrarModalCompartirMegaEvento();
        };
    }
    
    // Guardar información del mega evento para compartir
    window.megaEventoParaCompartir = {
        mega_evento_id: megaEventoId,
        titulo: mega.titulo || 'Mega Evento',
        descripcion: mega.descripcion || '',
        url: typeof getPublicUrl !== 'undefined' 
            ? getPublicUrl(`/mega-evento/${megaEventoId}/qr`)
            : `http://192.168.0.7:8000/mega-evento/${megaEventoId}/qr`
    };
}

// Mostrar modal de compartir
function mostrarModalCompartirMegaEvento() {
    const modal = document.getElementById('modalCompartir');
    if (modal) {
        if (typeof $ !== 'undefined') {
            $(modal).modal('show');
        } else {
            modal.style.display = 'block';
            modal.classList.add('show');
            document.body.classList.add('modal-open');
            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            backdrop.id = 'modalBackdropCompartir';
            backdrop.onclick = () => cerrarModalCompartir();
            document.body.appendChild(backdrop);
        }
    }
}

// Cerrar modal de compartir
function cerrarModalCompartir() {
    const modal = document.getElementById('modalCompartir');
    if (modal) {
        if (typeof $ !== 'undefined') {
            $(modal).modal('hide');
        } else {
            modal.style.display = 'none';
            modal.classList.remove('show');
            document.body.classList.remove('modal-open');
            const backdrop = document.getElementById('modalBackdropCompartir');
            if (backdrop) {
                backdrop.remove();
            }
        }
    }
    // Ocultar QR
    const qrContainer = document.getElementById('qrContainer');
    if (qrContainer) {
        qrContainer.style.display = 'none';
    }
}

// Registrar compartido
async function registrarCompartidoMegaEvento(megaEventoId, metodo) {
    try {
        const token = localStorage.getItem('token');
        // Usar la ruta pública que acepta tanto usuarios autenticados como no autenticados
        const url = `${API_BASE_URL}/api/mega-eventos/${megaEventoId}/compartir-publico`;
        
        const headers = {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        };
        
        // Si hay token, incluirlo (para usuarios autenticados: ONG, externos, empresas)
        if (token) {
            headers['Authorization'] = `Bearer ${token}`;
        }
        
        await fetch(url, {
            method: 'POST',
            headers: headers,
            body: JSON.stringify({ metodo: metodo })
        });
        
        // Actualizar contador de compartidos
        await cargarContadorCompartidosMegaEvento(megaEventoId);
    } catch (error) {
        console.warn('Error registrando compartido:', error);
    }
}

// Cargar contador de compartidos
async function cargarContadorCompartidosMegaEvento(megaEventoId) {
    try {
        const res = await fetch(`${API_BASE_URL}/api/mega-eventos/${megaEventoId}/compartidos/total`);
        const data = await res.json();
        
        if (data.success) {
            const contadorCompartidos = document.getElementById('contadorCompartidos');
            if (contadorCompartidos) {
                contadorCompartidos.textContent = data.total_compartidos || 0;
            }
        }
    } catch (error) {
        console.warn('Error cargando contador de compartidos:', error);
    }
}

// Copiar enlace
async function copiarEnlaceMegaEvento() {
    const megaEvento = window.megaEventoParaCompartir;
    if (!megaEvento) return;

    // Registrar compartido en backend
    await registrarCompartidoMegaEvento(megaEvento.mega_evento_id, 'link');

    const url = megaEvento.url;
    
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(url).then(() => {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: '¡Enlace copiado!',
                    text: 'El enlace se ha copiado al portapapeles',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                alert('Enlace copiado al portapapeles');
            }
            cerrarModalCompartir();
        }).catch(err => {
            console.error('Error al copiar:', err);
            fallbackCopiarEnlaceMegaEvento(url);
        });
    } else {
        fallbackCopiarEnlaceMegaEvento(url);
    }
}

function fallbackCopiarEnlaceMegaEvento(url) {
    const textarea = document.createElement('textarea');
    textarea.value = url;
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    textarea.select();
    try {
        document.execCommand('copy');
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: '¡Enlace copiado!',
                text: 'El enlace se ha copiado al portapapeles',
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            alert('Enlace copiado al portapapeles');
        }
        cerrarModalCompartir();
    } catch (err) {
        console.error('Error al copiar:', err);
        alert('Error al copiar el enlace. Por favor, cópialo manualmente: ' + url);
    }
    document.body.removeChild(textarea);
}

// Mostrar QR Code
async function mostrarQRMegaEvento() {
    const megaEvento = window.megaEventoParaCompartir;
    if (!megaEvento) return;

    // Registrar compartido en backend
    await registrarCompartidoMegaEvento(megaEvento.mega_evento_id, 'qr');

    const qrContainer = document.getElementById('qrContainer');
    const qrcodeDiv = document.getElementById('qrcode');
    
    if (!qrContainer || !qrcodeDiv) return;

    const qrUrl = megaEvento.url;
    
    // Limpiar contenido anterior
    qrcodeDiv.innerHTML = '';
    
    // Mostrar contenedor primero
    qrContainer.style.display = 'block';
    
    // Agregar indicador de carga
    qrcodeDiv.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x" style="color: #0C2B44;"></i><p class="mt-2" style="color: #333;">Generando QR...</p></div>';
    
    // Intentar cargar QRCode si no está disponible
    if (typeof QRCode === 'undefined') {
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js';
        script.onload = function() {
            generarQRCodeMegaEvento(qrUrl, qrcodeDiv);
        };
        script.onerror = function() {
            generarQRConAPIMegaEvento(qrUrl, qrcodeDiv);
        };
        document.head.appendChild(script);
    } else {
        generarQRCodeMegaEvento(qrUrl, qrcodeDiv);
    }
}

// Función auxiliar para generar QR con la librería
function generarQRCodeMegaEvento(qrUrl, qrcodeDiv) {
    try {
        QRCode.toCanvas(qrcodeDiv, qrUrl, {
            width: 250,
            margin: 2,
            color: {
                dark: '#0C2B44',
                light: '#FFFFFF'
            },
            errorCorrectionLevel: 'M'
        }, function (error) {
            if (error) {
                console.error('Error generando QR:', error);
                generarQRConAPIMegaEvento(qrUrl, qrcodeDiv);
            } else {
                const canvas = qrcodeDiv.querySelector('canvas');
                if (canvas) {
                    canvas.style.display = 'block';
                    canvas.style.margin = '0 auto';
                }
            }
        });
    } catch (error) {
        console.error('Error en generarQRCode:', error);
        generarQRConAPIMegaEvento(qrUrl, qrcodeDiv);
    }
}

// Función alternativa usando API de QR
function generarQRConAPIMegaEvento(qrUrl, qrcodeDiv) {
    try {
        const apiUrl = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodeURIComponent(qrUrl)}&bgcolor=FFFFFF&color=0C2B44`;
        const img = document.createElement('img');
        img.src = apiUrl;
        img.alt = 'QR Code';
        img.style.cssText = 'display: block; margin: 0 auto; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);';
        img.onerror = function() {
            qrcodeDiv.innerHTML = `
                <div class="text-center p-3">
                    <p class="text-danger mb-2" style="font-size: 0.9rem;">Error cargando generador de QR.</p>
                    <p class="text-muted mb-2" style="font-size: 0.85rem;">Por favor, usa el enlace directo:</p>
                    <a href="${qrUrl}" target="_blank" class="btn btn-sm" style="background: #0C2B44; color: white;">Abrir enlace</a>
                </div>
            `;
        };
        qrcodeDiv.innerHTML = '';
        qrcodeDiv.appendChild(img);
    } catch (error) {
        console.error('Error generando QR con API:', error);
        qrcodeDiv.innerHTML = `
            <div class="text-center p-3">
                <p class="text-danger mb-2" style="font-size: 0.9rem;">Error cargando generador de QR.</p>
                <p class="text-muted mb-2" style="font-size: 0.85rem;">Por favor, usa el enlace directo:</p>
                <a href="${qrUrl}" target="_blank" class="btn btn-sm" style="background: #0C2B44; color: white;">Abrir enlace</a>
            </div>
        `;
    }
}

// Funciones para reacciones de mega eventos
async function verificarReaccionMegaEvento() {
    const token = localStorage.getItem('token');
    const btnReaccionar = document.getElementById('btnReaccionar');
    const iconoCorazon = document.getElementById('iconoCorazon');
    const textoReaccion = document.getElementById('textoReaccion');
    const contadorReacciones = document.getElementById('contadorReacciones');

    if (!btnReaccionar) return;

    try {
        const res = await fetch(`${API_BASE_URL}/api/mega-eventos/reacciones/verificar/${megaEventoId}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        const data = await res.json();
        if (data.success) {
            if (data.reaccionado) {
                iconoCorazon.className = 'fas fa-heart mr-2';
                btnReaccionar.classList.remove('btn-outline-danger');
                btnReaccionar.classList.add('btn-danger');
                textoReaccion.textContent = 'Te gusta';
            } else {
                iconoCorazon.className = 'fas fa-heart mr-2';
                btnReaccionar.classList.remove('btn-danger');
                btnReaccionar.classList.add('btn-outline-danger');
                textoReaccion.textContent = 'Me gusta';
            }
            contadorReacciones.textContent = data.total_reacciones || 0;
        }

        // Agregar evento click al botón
        btnReaccionar.onclick = async () => {
            await toggleReaccionMegaEvento();
        };
    } catch (error) {
        console.warn('Error verificando reacción:', error);
    }
}

async function toggleReaccionMegaEvento() {
    const token = localStorage.getItem('token');
    const btnReaccionar = document.getElementById('btnReaccionar');
    const iconoCorazon = document.getElementById('iconoCorazon');
    const textoReaccion = document.getElementById('textoReaccion');
    const contadorReacciones = document.getElementById('contadorReacciones');

    try {
        const res = await fetch(`${API_BASE_URL}/api/mega-eventos/reacciones/toggle`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ mega_evento_id: megaEventoId })
        });

        const data = await res.json();
        if (data.success) {
            if (data.reaccionado) {
                iconoCorazon.className = 'fas fa-heart mr-2';
                btnReaccionar.classList.remove('btn-outline-danger');
                btnReaccionar.classList.add('btn-danger');
                textoReaccion.textContent = 'Te gusta';
            } else {
                iconoCorazon.className = 'fas fa-heart mr-2';
                btnReaccionar.classList.remove('btn-danger');
                btnReaccionar.classList.add('btn-outline-danger');
                textoReaccion.textContent = 'Me gusta';
            }
            contadorReacciones.textContent = data.total_reacciones || 0;
            // Recargar lista de reacciones
            cargarReaccionesMegaEvento();
        }
    } catch (error) {
        console.error('Error en toggle reacción:', error);
    }
}

// Cargar lista de usuarios que reaccionaron
async function cargarReaccionesMegaEvento() {
    const container = document.getElementById('reaccionesContainer');
    if (!container) return;

    const token = localStorage.getItem('token');

    try {
        container.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border" role="status" style="color: #00A36C; width: 3rem; height: 3rem;">
                    <span class="sr-only">Cargando...</span>
                </div>
                <p class="mt-3" style="color: #333333; font-weight: 500;">Cargando reacciones...</p>
            </div>
        `;

        const res = await fetch(`${API_BASE_URL}/api/mega-eventos/reacciones/${megaEventoId}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        const data = await res.json();

        if (!res.ok || !data.success) {
            container.innerHTML = `
                <div class="alert" style="background: #fff3cd; border: 1px solid #ffc107; color: #856404; border-radius: 8px; padding: 1rem;">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    ${data.error || 'Error al cargar reacciones'}
                </div>
            `;
            return;
        }

        if (!data.reacciones || data.reacciones.length === 0) {
            container.innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-heart fa-3x mb-3 text-danger" style="opacity: 0.3;"></i>
                    <p class="mb-0 text-muted">Aún no hay reacciones en este mega evento</p>
                </div>
            `;
            return;
        }

        let html = '<div class="row">';
        data.reacciones.forEach((reaccion, index) => {
            const fechaReaccion = new Date(reaccion.fecha_reaccion).toLocaleDateString('es-ES', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            const fotoPerfil = reaccion.foto_perfil || null;
            const inicialNombre = reaccion.nombre ? reaccion.nombre.charAt(0).toUpperCase() : '?';
            const tipoBadge = reaccion.tipo === 'registrado' 
                ? '<span class="badge badge-success">Registrado</span>'
                : '<span class="badge badge-warning">No registrado</span>';

            html += `
                <div class="col-md-6 col-lg-4 mb-3 reaccion-card" style="animation-delay: ${index * 0.1}s;">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; transition: all 0.3s ease; overflow: hidden;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 24px rgba(12, 43, 68, 0.15)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.08)'">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3" style="white-space: nowrap; overflow: hidden;">
                                <div class="position-relative mr-3" style="flex-shrink: 0;">
                                ${fotoPerfil ? `
                                        <img src="${fotoPerfil}" alt="${reaccion.nombre}" class="rounded-circle" style="width: 70px; height: 70px; object-fit: cover; border: 3px solid #00A36C; box-shadow: 0 4px 12px rgba(0, 163, 108, 0.2); display: block;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 70px; height: 70px; font-weight: 700; font-size: 1.5rem; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); color: white; border: 3px solid #00A36C; box-shadow: 0 4px 12px rgba(0, 163, 108, 0.2); position: absolute; top: 0; left: 0; display: none;">
                                            ${inicialNombre}
                                        </div>
                                ` : `
                                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 70px; height: 70px; font-weight: 700; font-size: 1.5rem; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); color: white; border: 3px solid #00A36C; box-shadow: 0 4px 12px rgba(0, 163, 108, 0.2);">
                                        ${inicialNombre}
                                    </div>
                                `}
                                </div>
                                <div class="flex-grow-1" style="min-width: 0; overflow: hidden;">
                                    <h6 class="mb-1" style="color: #2c3e50; font-weight: 600; font-size: 1rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${reaccion.nombre || 'N/A'}</h6>
                                    <div class="mb-2">
                                        ${tipoBadge}
                                    </div>
                                    <small class="text-muted d-block" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        <i class="fas fa-envelope mr-1" style="color: #0C2B44;"></i> ${reaccion.correo || 'N/A'}
                                    </small>
                                    ${reaccion.telefono ? `
                                        <small class="text-muted d-block" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            <i class="fas fa-phone mr-1" style="color: #0C2B44;"></i> ${reaccion.telefono}
                                        </small>
                                    ` : ''}
                                </div>
                                </div>
                            <div class="mt-3 pt-3 border-top d-flex align-items-center justify-content-between">
                                <small class="text-muted">
                                    <i class="fas fa-clock mr-1" style="color: #6c757d;"></i> 
                                    ${fechaReaccion}
                                </small>
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: linear-gradient(135deg, rgba(220, 53, 69, 0.1) 0%, rgba(220, 53, 69, 0.2) 100%) !important;">
                                    <i class="fas fa-heart" style="color: #dc3545; font-size: 1.1rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        container.innerHTML = html;
    } catch (error) {
        console.error('Error cargando reacciones:', error);
        container.innerHTML = `
            <div class="alert alert-danger" style="background: #f8d7da; border: 1px solid #dc3545; color: #721c24; border-radius: 8px; padding: 1rem;">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Error de conexión al cargar reacciones
            </div>
        `;
    }
}

// Mostrar patrocinadores del mega evento
function mostrarPatrocinadoresMegaEvento(mega) {
    const patrocinadoresCard = document.getElementById('patrocinadoresCard');
    const patrocinadoresDiv = document.getElementById('patrocinadores');
    
    if (!patrocinadoresCard || !patrocinadoresDiv) return;
    
    // Helper para construir URL de imagen (usar la función global)
    // Esta función local ya no es necesaria, se usa la global
    
    if (mega.patrocinadores && Array.isArray(mega.patrocinadores) && mega.patrocinadores.length > 0) {
        patrocinadoresCard.style.display = 'block';
        patrocinadoresDiv.innerHTML = '';
        
        mega.patrocinadores.forEach(pat => {
            const nombre = pat.nombre || 'Sin nombre';
            // El backend ya devuelve la URL completa, usar directamente
            const fotoPerfil = pat.foto_perfil || null;
            
            const patrocinadorDiv = document.createElement('div');
            patrocinadorDiv.className = 'mb-3 pb-3';
            patrocinadorDiv.style.cssText = 'border-bottom: 1px solid #e9ecef; transition: all 0.3s ease;';
            patrocinadorDiv.onmouseenter = function() {
                this.style.backgroundColor = '#f8f9fa';
                this.style.borderRadius = '8px';
                this.style.padding = '0.5rem';
                this.style.marginLeft = '-0.5rem';
                this.style.marginRight = '-0.5rem';
            };
            patrocinadorDiv.onmouseleave = function() {
                this.style.backgroundColor = 'transparent';
                this.style.borderRadius = '0';
                this.style.padding = '0';
                this.style.marginLeft = '0';
                this.style.marginRight = '0';
            };
            
            patrocinadorDiv.innerHTML = `
                <div class="d-flex align-items-center">
                    ${fotoPerfil ? `
                        <div class="mr-3" style="flex-shrink: 0;">
                            <img src="${fotoPerfil}" alt="${nombre}" class="rounded-circle" 
                                 style="width: 50px; height: 50px; object-fit: cover; border: 2px solid #e9ecef; box-shadow: 0 2px 8px rgba(0,0,0,0.1);"
                                 onerror="this.style.display='none';">
                        </div>
                    ` : ''}
                    <div class="flex-grow-1">
                        <h6 class="mb-1" style="font-weight: 700; color: #0C2B44; font-size: 0.95rem; margin-bottom: 0.25rem;">${nombre}</h6>
                        ${pat.NIT ? `<small class="text-muted d-block" style="font-size: 0.8rem; margin-bottom: 0.25rem;">NIT: ${pat.NIT}</small>` : ''}
                        ${pat.estado_compromiso ? `<span class="badge badge-success" style="font-size: 0.7rem; background: #00A36C; padding: 0.25em 0.5em;">${pat.estado_compromiso}</span>` : ''}
                    </div>
                </div>
            `;
            
            patrocinadoresDiv.appendChild(patrocinadorDiv);
        });
    } else {
        patrocinadoresCard.style.display = 'none';
    }
}

// Cargar lista de participantes inscritos
async function cargarParticipantesCardMegaEvento() {
    const container = document.getElementById('participantesContainer');
    if (!container) return;

    const token = localStorage.getItem('token');

    try {
        container.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border" role="status" style="color: #00A36C; width: 3rem; height: 3rem;">
                    <span class="sr-only">Cargando...</span>
                </div>
                <p class="mt-3" style="color: #333333; font-weight: 500;">Cargando participantes...</p>
            </div>
        `;

        const res = await fetch(`${API_BASE_URL}/api/mega-eventos/${megaEventoId}/participantes`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        const data = await res.json();

        if (!res.ok || !data.success) {
            container.innerHTML = `
                <div class="alert" style="background: #fff3cd; border: 1px solid #ffc107; color: #856404; border-radius: 8px; padding: 1rem;">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    ${data.error || 'Error al cargar participantes'}
                </div>
            `;
            return;
        }

        if (!data.participantes || data.participantes.length === 0) {
            container.innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x mb-3 text-muted" style="opacity: 0.3;"></i>
                    <p class="mb-0 text-muted">Aún no hay participantes inscritos en este mega evento</p>
                </div>
            `;
            return;
        }

        // Helper para construir URL de imagen (usar la función global buildImageUrl)
        // Ya no es necesario definir una función local, se usa la global

        let html = '<div class="row">';
        data.participantes.forEach((participante, index) => {
            const fechaRegistro = new Date(participante.fecha_registro).toLocaleDateString('es-ES', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            // Priorizar nombre_completo, luego construir desde nombres/apellidos
            let nombreCompleto = participante.nombre_completo || participante.participante;
            
            // Si no hay nombre_completo, intentar construir desde nombres y apellidos
            if (!nombreCompleto || nombreCompleto === 'N/A' || nombreCompleto.length <= 2) {
                const nombres = participante.nombres || '';
                const apellidos = participante.apellidos || '';
                const nombreConstruido = (nombres + ' ' + apellidos).trim();
                nombreCompleto = nombreConstruido || participante.nombre_usuario || 'Usuario';
            }
            
            // Si aún es muy corto o solo tiene una letra, usar nombre_usuario
            if (nombreCompleto.length <= 2 && participante.nombre_usuario && participante.nombre_usuario.length > 2) {
                nombreCompleto = participante.nombre_usuario;
            }
            
            const fotoPerfil = participante.foto_perfil || participante.avatar;
            const fotoPerfilUrl = fotoPerfil ? buildImageUrl(fotoPerfil) : null;
            const inicialNombre = nombreCompleto !== 'N/A' && nombreCompleto !== 'Usuario' ? nombreCompleto.charAt(0).toUpperCase() : '?';
            
            // Badge de estado
            let estadoBadge = '';
            const estado = participante.estado || 'pendiente';
            if (estado === 'aprobada' || estado === 'aprobado') {
                estadoBadge = '<span class="badge badge-success">Aprobado</span>';
            } else if (estado === 'rechazada' || estado === 'rechazado') {
                estadoBadge = '<span class="badge badge-danger">Rechazado</span>';
            } else {
                estadoBadge = '<span class="badge badge-warning">Pendiente</span>';
            }

            const tipoBadge = participante.tipo === 'registrado' 
                ? '<span class="badge badge-info">Registrado</span>'
                : '<span class="badge badge-secondary">No registrado</span>';

            html += `
                <div class="col-md-6 col-lg-4 mb-3 participante-card" style="animation-delay: ${index * 0.1}s;">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; transition: all 0.3s ease; overflow: hidden;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 24px rgba(12, 43, 68, 0.15)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.08)'">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3" style="white-space: nowrap; overflow: hidden;">
                                <div class="position-relative mr-3" style="flex-shrink: 0;">
                                ${fotoPerfil ? `
                                        <img src="${fotoPerfil}" alt="${nombreCompleto}" class="rounded-circle" style="width: 70px; height: 70px; object-fit: cover; border: 3px solid #00A36C; box-shadow: 0 4px 12px rgba(0, 163, 108, 0.2); display: block;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 70px; height: 70px; font-weight: 700; font-size: 1.5rem; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); color: white; border: 3px solid #00A36C; box-shadow: 0 4px 12px rgba(0, 163, 108, 0.2); position: absolute; top: 0; left: 0; display: none;">
                                            ${inicialNombre}
                                        </div>
                                ` : `
                                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 70px; height: 70px; font-weight: 700; font-size: 1.5rem; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); color: white; border: 3px solid #00A36C; box-shadow: 0 4px 12px rgba(0, 163, 108, 0.2);">
                                        ${inicialNombre}
                                    </div>
                                `}
                                </div>
                                <div class="flex-grow-1" style="min-width: 0; overflow: hidden;">
                                    <h6 class="mb-1" style="color: #2c3e50; font-weight: 600; font-size: 1rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${nombreCompleto}</h6>
                                    <div class="mb-2 d-flex flex-wrap" style="gap: 0.25rem;">
                                        ${tipoBadge}
                                        ${estadoBadge}
                                    </div>
                                    <small class="text-muted d-block" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        <i class="fas fa-envelope mr-1" style="color: #0C2B44;"></i> ${participante.email || 'N/A'}
                                    </small>
                                    ${participante.telefono ? `
                                        <small class="text-muted d-block" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            <i class="fas fa-phone mr-1" style="color: #0C2B44;"></i> ${participante.telefono}
                                        </small>
                                    ` : ''}
                                </div>
                                </div>
                            <div class="mt-3 pt-3 border-top d-flex align-items-center justify-content-between">
                                <small class="text-muted">
                                    <i class="fas fa-clock mr-1" style="color: #6c757d;"></i> 
                                    ${fechaRegistro}
                                </small>
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: linear-gradient(135deg, rgba(0, 163, 108, 0.1) 0%, rgba(0, 163, 108, 0.2) 100%) !important;">
                                    <i class="fas fa-user-check" style="color: #00A36C; font-size: 1.1rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        container.innerHTML = html;
    } catch (error) {
        console.error('Error cargando participantes:', error);
        container.innerHTML = `
            <div class="alert alert-danger" style="background: #f8d7da; border: 1px solid #dc3545; color: #721c24; border-radius: 8px; padding: 1rem;">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Error de conexión al cargar participantes
            </div>
        `;
    }
}

// Actualizar contadores en tiempo real
let intervaloContadoresMegaEvento = null;
function iniciarActualizacionTiempoRealMegaEvento() {
    // Actualizar cada 5 segundos
    intervaloContadoresMegaEvento = setInterval(() => {
        verificarReaccionMegaEvento();
        cargarContadorCompartidosMegaEvento(megaEventoId);
    }, 5000);
}

// Limpiar intervalo al salir de la página
window.addEventListener('beforeunload', function() {
    if (intervaloContadoresMegaEvento) {
        clearInterval(intervaloContadoresMegaEvento);
    }
});
</script>
<style>
.swal-image-popup {
    max-width: 90vw !important;
}
.swal-image-popup img {
    max-width: 100%;
    max-height: 80vh;
    object-fit: contain;
}

/* Animaciones para reacciones */
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

@keyframes heartBeat {
    0%, 100% {
        transform: scale(1);
    }
    25% {
        transform: scale(1.2);
    }
    50% {
        transform: scale(1);
    }
    75% {
        transform: scale(1.1);
    }
}

/* Animación para el contador de reacciones */
#contadorReacciones {
    transition: all 0.3s ease;
}

#contadorReacciones.animate {
    animation: pulse 0.5s ease-in-out;
    color: #dc3545;
    font-weight: 700;
}

/* Animación para el botón de actualizar reacciones */
.btn-actualizar-reacciones:active {
    transform: rotate(360deg);
    transition: transform 0.5s ease;
}
</style>
<script src="{{ asset('assets/js/ong/asistencia-mega-eventos-functions.js') }}"></script>
<script>
    // Cargar lista de asistencia al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            cargarListaAsistenciaMegaEvento();
        }, 1000);
    });
</script>
@endpush


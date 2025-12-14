@extends('layouts.adminlte-externo')

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
                                <div style="width: 80px; height: 80px; background: #00A36C; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(0, 163, 108, 0.3);" onmouseover="this.style.background='#008a5a'; this.style.transform='scale(1.1)'; this.style.boxShadow='0 4px 12px rgba(0, 163, 108, 0.4)'" onmouseout="this.style.background='#00A36C'; this.style.transform='scale(1)'; this.style.boxShadow='0 2px 8px rgba(0, 163, 108, 0.3)'">
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

    <!-- Modal de Registrar Asistencia -->
    <div id="modalRegistrarAsistencia" class="modal fade" tabindex="-1" role="dialog" style="display: none;">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 12px 48px rgba(0,0,0,0.25); overflow: hidden;">
                <div class="modal-header" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); border-radius: 0; color: white; padding: 1.5rem 2rem; border-bottom: none;">
                    <h5 class="modal-title font-weight-bold mb-0" style="font-size: 1.5rem;">
                        <i class="fas fa-clipboard-check mr-2"></i>
                        Registrar Asistencia
                    </h5>
                    <button type="button" class="close text-white" onclick="cerrarModalRegistrarAsistencia()" aria-label="Close" style="opacity: 0.9; font-size: 1.5rem; transition: all 0.3s ease; padding: 0.5rem;"
                        onmouseover="this.style.opacity='1'; this.style.transform='rotate(90deg)';"
                        onmouseout="this.style.opacity='0.9'; this.style.transform='rotate(0deg)';">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-5">
                    <!-- Mensaje informativo sobre tiempo límite -->
                    <div id="mensajeTiempoLimite" class="alert alert-info mb-4" style="display: none; border-radius: 12px; border-left: 4px solid #17a2b8; background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%); box-shadow: 0 2px 8px rgba(23, 162, 184, 0.15);">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-clock mr-3 mt-1" style="font-size: 1.5rem; color: #17a2b8;"></i>
                            <div>
                                <strong style="color: #0c5460;">Tiempo límite para registrar asistencia</strong>
                                <p class="mb-0 mt-1" id="textoTiempoLimite" style="color: #0c5460;"></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <i class="fas fa-ticket-alt" style="font-size: 3rem; color: #00A36C; opacity: 0.8;"></i>
                        </div>
                        <p class="text-muted mb-0" style="font-size: 1.05rem; line-height: 1.6;">
                            Ingresa tu código de ticket o escanea el código QR para registrar tu asistencia a este evento.
                        </p>
                    </div>

                    <!-- Formulario de Validación -->
                    <div class="mb-5">
                        <div class="row mb-4">
                            <div class="col-12 col-md-8 mb-3 mb-md-0">
                                <label for="ticketCodigoInputDetalle" class="form-label font-weight-semibold mb-2" style="color: #0C2B44;">
                                    <i class="fas fa-key mr-2 text-primary"></i>Código de Ticket
                                </label>
                                <input 
                                    type="text" 
                                    id="ticketCodigoInputDetalle" 
                                    placeholder="Ingresa tu código de ticket o escanea el QR"
                                    class="form-control form-control-lg"
                                    style="border-radius: 12px; padding: 1rem 1.25rem; border: 2px solid #e0e0e0; transition: all 0.3s ease; font-size: 1rem;"
                                    onkeypress="if(event.key === 'Enter') verificarTicketDetalle()"
                                    onfocus="this.style.borderColor='#00A36C'; this.style.boxShadow='0 0 0 0.2rem rgba(0, 163, 108, 0.25)';"
                                    onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none';"
                                >
                            </div>
                            <div class="col-12 col-md-4 d-flex align-items-end">
                                <button 
                                    onclick="verificarTicketDetalle()" 
                                    id="btnVerificarDetalle"
                                    class="btn btn-primary btn-lg w-100"
                                    style="border-radius: 12px; padding: 1rem 1.5rem; font-weight: 600; background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); border: none; box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3); transition: all 0.3s ease;"
                                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(0, 123, 255, 0.4)';"
                                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(0, 123, 255, 0.3)';"
                                >
                                    <i class="fas fa-search mr-2"></i> Verificar
                                </button>
                            </div>
                        </div>

                        <!-- Separador -->
                        <div class="text-center my-4 position-relative">
                            <hr style="border: none; border-top: 2px solid #e0e0e0; margin: 0;">
                            <span class="position-absolute" style="background: white; padding: 0 1rem; top: -12px; left: 50%; transform: translateX(-50%); color: #6c757d; font-weight: 500;">
                                <i class="fas fa-qrcode mr-2"></i>O escanea un código QR
                            </span>
                        </div>

                        <!-- Botones de QR - Mejorados con más espacio -->
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <button 
                                    onclick="activarEscannerQRDetalle()" 
                                    id="btnEscanearQRDetalle"
                                    class="btn btn-info w-100"
                                    style="border-radius: 12px; padding: 1rem 1.5rem; font-weight: 600; background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); border: none; box-shadow: 0 4px 12px rgba(23, 162, 184, 0.3); transition: all 0.3s ease; font-size: 1rem;"
                                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(23, 162, 184, 0.4)';"
                                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(23, 162, 184, 0.3)';"
                                >
                                    <i class="fas fa-camera mr-2"></i> Escanear Código QR
                                </button>
                            </div>
                            <div class="col-12 col-md-6">
                                <button 
                                    type="button"
                                    onclick="document.getElementById('inputQRImagenDetalle').click()"
                                    id="btnImportarQRDetalle"
                                    class="btn btn-primary w-100"
                                    style="border-radius: 12px; padding: 1rem 1.5rem; font-weight: 600; background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); border: none; box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3); transition: all 0.3s ease; font-size: 1rem;"
                                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(0, 123, 255, 0.4)';"
                                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(0, 123, 255, 0.3)';"
                                >
                                    <i class="fas fa-upload mr-2"></i> Importar QR
                                </button>
                            </div>
                        </div>
                        <input
                            type="file"
                            id="inputQRImagenDetalle"
                            accept="image/*"
                            style="display: none;"
                            onchange="procesarQRImagenDetalle(event)"
                        >

                        <!-- Contenedor del Escáner QR -->
                        <div id="qrScannerContainerDetalle" style="display: none; margin-top: 2rem; padding: 2rem; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 16px; border: 2px dashed #00A36C; box-shadow: 0 4px 16px rgba(0, 163, 108, 0.15);">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h6 class="font-weight-bold mb-0" style="color: #0C2B44; font-size: 1.1rem;">
                                    <i class="fas fa-camera mr-2" style="color: #00A36C;"></i> Escáner QR Activo
                                </h6>
                                <button onclick="detenerEscannerQRDetalle()" class="btn btn-sm btn-danger" style="border-radius: 8px; padding: 0.5rem 1rem; font-weight: 600; box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3); transition: all 0.3s ease;"
                                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(220, 53, 69, 0.4)';"
                                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(220, 53, 69, 0.3)';">
                                    <i class="fas fa-times mr-1"></i> Cerrar
                                </button>
                            </div>
                            <div style="position: relative; width: 100%; max-width: 450px; margin: 0 auto; border-radius: 16px; overflow: hidden; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2); border: 3px solid #00A36C;">
                                <video id="qrVideoDetalle" width="100%" style="display: block; background: #000; border-radius: 12px;"></video>
                                <canvas id="qrCanvasDetalle" style="display: none;"></canvas>
                            </div>
                            <p class="text-center mt-4 mb-0" style="color: #6c757d; font-size: 0.95rem;">
                                <i class="fas fa-info-circle mr-2" style="color: #00A36C;"></i> Apunta la cámara hacia el código QR de tu ticket
                            </p>
                        </div>
                    </div>

                    <!-- Información del Evento (se muestra después de verificar el ticket) -->
                    <div id="infoEventoContainerDetalle" class="hidden mb-4 p-4" style="background: linear-gradient(135deg, rgba(12, 43, 68, 0.05) 0%, rgba(0, 163, 108, 0.1) 100%); border-radius: 16px; border: 2px solid rgba(0, 163, 108, 0.2); box-shadow: 0 4px 16px rgba(0, 163, 108, 0.1);">
                        <h5 class="font-weight-bold mb-4" style="color: #0C2B44; font-size: 1.25rem;">
                            <i class="fas fa-calendar-check mr-2" style="color: #00A36C;"></i> Confirmar Asistencia
                        </h5>
                        <div id="infoEventoDetalleDetalle" class="mb-4">
                            <!-- Se llenará dinámicamente -->
                        </div>
                        
                        <!-- Campo de comentario -->
                        <div class="form-group mb-4">
                            <label for="comentarioAsistencia" class="font-weight-semibold mb-2" style="color: #0C2B44;">
                                <i class="fas fa-comment-dots mr-2" style="color: #17a2b8;"></i> Comentario (opcional)
                            </label>
                            <textarea 
                                id="comentarioAsistencia" 
                                class="form-control" 
                                rows="3" 
                                placeholder="Agrega un comentario sobre tu asistencia al evento..."
                                style="border-radius: 12px; resize: vertical; border: 2px solid #e0e0e0; padding: 0.75rem 1rem; transition: all 0.3s ease;"
                                maxlength="500"
                                onfocus="this.style.borderColor='#00A36C'; this.style.boxShadow='0 0 0 0.2rem rgba(0, 163, 108, 0.25)';"
                                onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none';"
                            ></textarea>
                            <small class="form-text text-muted mt-2">
                                <i class="fas fa-info-circle mr-1"></i> Máximo 500 caracteres
                            </small>
                        </div>
                        
                        <button 
                            onclick="confirmarAsistenciaDetalle()" 
                            id="btnConfirmarAsistenciaDetalle"
                            class="btn btn-success btn-block btn-lg"
                            style="border-radius: 12px; padding: 1rem 1.5rem; font-weight: 600; background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border: none; box-shadow: 0 4px 16px rgba(40, 167, 69, 0.3); transition: all 0.3s ease; font-size: 1.05rem;"
                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(40, 167, 69, 0.4)';"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 16px rgba(40, 167, 69, 0.3)';"
                        >
                            <i class="fas fa-check-circle mr-2"></i> Confirmar Asistencia
                        </button>
                    </div>

                    <!-- Mensajes de Resultado -->
                    <div id="mensajeResultadoDetalle" class="hidden mt-4" style="display: none;"></div>
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

        <!-- Botones de Acción (Externo) - Mejorados -->
        <div class="d-flex justify-content-end mb-4 flex-wrap" style="gap: 0.75rem;">
            <a href="/externo/eventos" class="btn btn-outline-secondary" style="border-radius: 10px; padding: 0.6rem 1.5rem; font-weight: 500; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <i class="fas fa-arrow-left mr-2"></i> Volver
            </a>
            <button class="btn btn-outline-danger d-flex align-items-center" id="btnReaccionar" style="border-radius: 10px; padding: 0.6rem 1.5rem; font-weight: 500; box-shadow: 0 2px 8px rgba(220, 53, 69, 0.2);">
                <i class="far fa-heart mr-2" id="iconoCorazon"></i>
                <span id="textoReaccion">Me gusta</span>
                <span class="badge badge-light ml-2" id="contadorReacciones" style="background: rgba(255,255,255,0.3); color: #dc3545;">0</span>
            </button>
            <button class="btn btn-outline-primary d-flex align-items-center" id="btnCompartir" style="border-radius: 10px; padding: 0.6rem 1.5rem; font-weight: 500; box-shadow: 0 2px 8px rgba(0, 123, 255, 0.2);">
                <i class="far fa-share-square mr-2"></i> Compartir
                <span class="badge badge-light ml-2" id="contadorCompartidos" style="background: rgba(255,255,255,0.3); color: #007bff;">0</span>
            </button>
            <button class="btn btn-success d-none align-items-center" id="btnParticipar" style="display: none !important; visibility: hidden !important; border-radius: 10px; padding: 0.6rem 1.5rem; font-weight: 600; box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3); background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border: none;">
                <i class="fas fa-check-circle mr-2"></i> Participar
        </button>
            <button class="btn btn-danger d-none align-items-center" id="btnCancelar" style="display: none !important; visibility: hidden !important; border-radius: 10px; padding: 0.6rem 1.5rem; font-weight: 600; box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3); background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); border: none;">
                <i class="fas fa-times-circle mr-2"></i> Cancelar Inscripción
        </button>
            <button class="btn btn-info d-none d-flex align-items-center" id="btnRegistrarAsistencia" onclick="abrirModalRegistrarAsistencia()" style="border-radius: 10px; padding: 0.6rem 1.5rem; font-weight: 600; box-shadow: 0 4px 12px rgba(23, 162, 184, 0.3); background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); border: none;">
                <i class="fas fa-clipboard-check mr-2"></i> Registrar Asistencia
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
@endsection

@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    /* Variables de color */
    :root {
        --primary-color: #00A36C;
        --primary-dark: #0C2B44;
        --dark-color: #2c3e50;
        --border-color: #e9ecef;
        --bg-light: #f8f9fa;
        --shadow-sm: 0 2px 8px rgba(12, 43, 68, 0.08);
        --shadow-md: 0 4px 16px rgba(0, 163, 108, 0.12);
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
        color: #00A36C;
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

    .btn-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
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

    .btn-outline-primary {
        border: 2px solid #007bff;
        color: #007bff;
    }

    .btn-outline-primary:hover {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        border-color: #007bff;
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

    /* Carrusel mejorado */
    #carouselImagenes {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    }

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

    /* Estilos para el modal de compartir */
    .modal-backdrop {
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal.show {
        display: block !important;
    }
    
    .hidden {
        display: none !important;
    }
    
    /* Estilos para mensajes de éxito y error */
    .alert {
        animation: slideInDown 0.3s ease-out;
    }
    
    @keyframes slideInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endsection

@section('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<!-- Librería jsQR para escanear códigos QR -->
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
<!-- Librería QRCode para generar códigos QR (solo para compartir) -->
<script src="https://cdn.jsdelivr.net/npm/qrcodejs2@0.0.2/qrcode.min.js"></script>
<script>
    // Definir PUBLIC_BASE_URL desde variable de entorno
    window.PUBLIC_BASE_URL = "{{ env('PUBLIC_APP_URL', 'http://192.168.0.7:8000') }}";
    console.log("🌐 PUBLIC_BASE_URL desde .env:", window.PUBLIC_BASE_URL);
</script>
<script src="{{ asset('assets/js/config.js') }}"></script>
<script src="{{ asset('assets/js/externo/show-event.js') }}"></script>
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

    // Variables globales para el modal de registrar asistencia
    let qrStreamDetalle = null;
    let qrScanningDetalle = false;
    let infoEventoDetalleActual = null;

    // Abrir modal de registrar asistencia
    function abrirModalRegistrarAsistencia() {
        const modal = document.getElementById('modalRegistrarAsistencia');
        
        // Verificar y mostrar mensaje de tiempo límite si aplica
        if (window.eventoActualGlobal) {
            const eventoActualGlobal = window.eventoActualGlobal;
            const ahora = new Date();
            let fechaFin = null;
            
            // Parsear fecha de fin correctamente
            if (eventoActualGlobal.fecha_fin) {
                if (typeof eventoActualGlobal.fecha_fin === 'string') {
                    const match = eventoActualGlobal.fecha_fin.trim().match(/^(\d{4})-(\d{2})-(\d{2})[\sT](\d{2}):(\d{2}):(\d{2})/);
                    if (match) {
                        const [, year, month, day, hour, minute, second] = match;
                        fechaFin = new Date(
                            parseInt(year, 10),
                            parseInt(month, 10) - 1,
                            parseInt(day, 10),
                            parseInt(hour, 10),
                            parseInt(minute, 10),
                            parseInt(second || 0, 10)
                        );
                    } else {
                        fechaFin = new Date(eventoActualGlobal.fecha_fin);
                    }
                } else {
                    fechaFin = new Date(eventoActualGlobal.fecha_fin);
                }
            }
            
            const eventoTerminado = fechaFin && ahora > fechaFin;
            
            if (eventoTerminado && fechaFin) {
                const diferenciaMs = ahora - fechaFin;
                const horasDesdeFinalizacion = diferenciaMs / (1000 * 60 * 60);
                const dentroDe24Horas = horasDesdeFinalizacion <= 24;
                
                if (dentroDe24Horas) {
                    const horasRestantes = 24 - horasDesdeFinalizacion;
                    const horas = Math.floor(horasRestantes);
                    const minutos = Math.floor((horasRestantes - horas) * 60);
                    
                    document.getElementById('mensajeTiempoLimite').style.display = 'block';
                    document.getElementById('textoTiempoLimite').innerHTML = 
                        `Este evento finalizó hace ${Math.floor(horasDesdeFinalizacion)} horas. Tienes <strong>${horas} horas y ${minutos} minutos</strong> restantes para registrar tu asistencia.`;
                } else {
                    document.getElementById('mensajeTiempoLimite').style.display = 'none';
                }
            } else {
                document.getElementById('mensajeTiempoLimite').style.display = 'none';
            }
        }
        
        if (modal && typeof $ !== 'undefined') {
            $(modal).modal('show');
        } else if (modal) {
            modal.style.display = 'block';
            modal.classList.add('show');
            document.body.classList.add('modal-open');
        }
    }

    // Cerrar modal
    function cerrarModalRegistrarAsistencia() {
        const modal = document.getElementById('modalRegistrarAsistencia');
        if (modal && typeof $ !== 'undefined') {
            $(modal).modal('hide');
        } else if (modal) {
            modal.style.display = 'none';
            modal.classList.remove('show');
            document.body.classList.remove('modal-open');
        }
        detenerEscannerQRDetalle();
        document.getElementById('ticketCodigoInputDetalle').value = '';
        document.getElementById('infoEventoContainerDetalle').classList.add('hidden');
        infoEventoDetalleActual = null;
    }

    // Verificar ticket desde detalle
    async function verificarTicketDetalle() {
        const token = localStorage.getItem('token');
        if (!token) {
            mostrarMensajeDetalle('Debes iniciar sesión', 'error');
            return;
        }

        const ticketCodigo = document.getElementById('ticketCodigoInputDetalle').value.trim();
        if (!ticketCodigo) {
            mostrarMensajeDetalle('Por favor, ingresa un código de ticket', 'error');
            return;
        }
        
        // Verificar si aún está dentro del período de 30 minutos
        if (window.eventoActualGlobal) {
            const ahora = new Date();
            let fechaFin = null;
            
            if (window.eventoActualGlobal.fecha_fin) {
                if (typeof window.eventoActualGlobal.fecha_fin === 'string') {
                    const match = window.eventoActualGlobal.fecha_fin.trim().match(/^(\d{4})-(\d{2})-(\d{2})[\sT](\d{2}):(\d{2}):(\d{2})/);
                    if (match) {
                        const [, year, month, day, hour, minute, second] = match;
                        fechaFin = new Date(
                            parseInt(year, 10),
                            parseInt(month, 10) - 1,
                            parseInt(day, 10),
                            parseInt(hour, 10),
                            parseInt(minute, 10),
                            parseInt(second || 0, 10)
                        );
                    } else {
                        fechaFin = new Date(window.eventoActualGlobal.fecha_fin);
                    }
                }
            }
            
            if (fechaFin && ahora > fechaFin) {
                const diferenciaMs = ahora - fechaFin;
                const minutosDesdeFinalizacion = diferenciaMs / (1000 * 60);
                
                if (minutosDesdeFinalizacion > 30) {
                    mostrarMensajeDetalle('El plazo de 30 minutos para registrar asistencia ha expirado. Ya no es posible registrar tu asistencia a este evento.', 'error');
                    return;
                }
            }
        }

        const btnVerificar = document.getElementById('btnVerificarDetalle');
        btnVerificar.disabled = true;
        btnVerificar.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Verificando...';

        try {
            const apiUrl = window.API_BASE_URL || 'http://192.168.0.7:8000';
            const res = await fetch(`${apiUrl}/api/verificar-ticket-welcome`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    ticket_codigo: ticketCodigo
                }),
            });

            const data = await res.json();

            if (data.success) {
                console.log('✅ Ticket verificado correctamente:', data.data);
                infoEventoDetalleActual = data.data;
                mostrarInfoEventoDetalle(data.data);
            } else {
                console.error('❌ Error al verificar ticket:', data.error);
                mostrarMensajeDetalle(data.error || 'Error al verificar ticket', 'error');
                document.getElementById('infoEventoContainerDetalle').classList.add('hidden');
            }
        } catch (error) {
            console.error('Error verificando ticket:', error);
            mostrarMensajeDetalle('Error al verificar ticket. Por favor, intenta nuevamente.', 'error');
            document.getElementById('infoEventoContainerDetalle').classList.add('hidden');
        } finally {
            btnVerificar.disabled = false;
            btnVerificar.innerHTML = '<i class="fas fa-search mr-2"></i> Verificar';
        }
    }

    // Mostrar información del evento en detalle
    function mostrarInfoEventoDetalle(evento) {
        const container = document.getElementById('infoEventoDetalleDetalle');
        
        if (evento.ya_validado) {
            container.innerHTML = `
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Este ticket ya fue utilizado el ${evento.fecha_validacion_anterior}
                </div>
                <div class="mb-3">
                    <p><strong>Evento:</strong> ${evento.evento_titulo}</p>
                    <p><strong>Fecha:</strong> ${evento.fecha_inicio}</p>
                    ${evento.ubicacion ? `<p><strong>Ubicación:</strong> ${evento.ubicacion}${evento.ciudad ? ', ' + evento.ciudad : ''}</p>` : ''}
                </div>
            `;
            document.getElementById('btnConfirmarAsistenciaDetalle').style.display = 'none';
        } else {
            container.innerHTML = `
                <div class="mb-3">
                    <p><strong>Evento:</strong> ${evento.evento_titulo}</p>
                    ${evento.evento_descripcion ? `<p><strong>Descripción:</strong> ${evento.evento_descripcion}</p>` : ''}
                    <p><strong>Fecha de inicio:</strong> ${evento.fecha_inicio}</p>
                    ${evento.ubicacion ? `<p><strong>Ubicación:</strong> ${evento.ubicacion}${evento.ciudad ? ', ' + evento.ciudad : ''}</p>` : ''}
                    <p><strong>Tipo de evento:</strong> ${evento.evento_tipo || 'No especificado'}</p>
                </div>
            `;
            document.getElementById('btnConfirmarAsistenciaDetalle').style.display = 'block';
        }

        document.getElementById('infoEventoContainerDetalle').classList.remove('hidden');
    }

    // Confirmar asistencia desde detalle
    async function confirmarAsistenciaDetalle() {
        if (!infoEventoDetalleActual) {
            mostrarMensajeDetalle('No hay información de evento para confirmar', 'error');
            return;
        }

        const token = localStorage.getItem('token');
        if (!token) {
            mostrarMensajeDetalle('Debes iniciar sesión', 'error');
            return;
        }

        const comentario = document.getElementById('comentarioAsistencia').value.trim();

        const btnConfirmar = document.getElementById('btnConfirmarAsistenciaDetalle');
        btnConfirmar.disabled = true;
        btnConfirmar.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Confirmando...';

        try {
            const apiUrl = window.API_BASE_URL || 'http://192.168.0.7:8000';
            const body = {
                ticket_codigo: infoEventoDetalleActual.ticket_codigo,
                modo_validacion: 'Manual'
            };
            
            // Agregar comentario solo si existe
            if (comentario) {
                body.comentario = comentario;
            }

            const res = await fetch(`${apiUrl}/api/validar-asistencia-welcome`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(body),
            });

            const data = await res.json();

            if (data.success) {
                console.log('✅ Asistencia confirmada exitosamente');
                mostrarMensajeDetalle(data.message || '¡Asistencia confirmada correctamente!', 'success');
                document.getElementById('ticketCodigoInputDetalle').value = '';
                document.getElementById('comentarioAsistencia').value = '';
                document.getElementById('infoEventoContainerDetalle').classList.add('hidden');
                infoEventoDetalleActual = null;
                
                // Cerrar modal después de 2 segundos
                setTimeout(() => {
                    cerrarModalRegistrarAsistencia();
                    // Recargar página para actualizar estado
                    location.reload();
                }, 2000);
            } else {
                console.error('❌ Error al confirmar asistencia:', data.error);
                mostrarMensajeDetalle(data.error || 'Error al confirmar asistencia', 'error');
            }
        } catch (error) {
            console.error('Error confirmando asistencia:', error);
            mostrarMensajeDetalle('Error al confirmar asistencia. Por favor, intenta nuevamente.', 'error');
        } finally {
            btnConfirmar.disabled = false;
            btnConfirmar.innerHTML = '<i class="fas fa-check-circle mr-2"></i> Confirmar Asistencia';
        }
    }

    // Mostrar mensaje en modal de detalle
    function mostrarMensajeDetalle(mensaje, tipo) {
        const mensajeDiv = document.getElementById('mensajeResultadoDetalle');
        
        if (tipo === 'success') {
            mensajeDiv.className = 'alert alert-success mt-4';
            mensajeDiv.style.background = 'linear-gradient(135deg, #28a745 0%, #20c997 100%)';
            mensajeDiv.style.color = 'white';
            mensajeDiv.style.border = 'none';
            mensajeDiv.style.borderRadius = '16px';
            mensajeDiv.style.padding = '1.5rem 1.75rem';
            mensajeDiv.style.fontWeight = '600';
            mensajeDiv.style.boxShadow = '0 4px 16px rgba(40, 167, 69, 0.3)';
            mensajeDiv.style.borderLeft = '4px solid rgba(255, 255, 255, 0.5)';
            mensajeDiv.innerHTML = `
                <div class="d-flex align-items-start">
                    <div style="background: rgba(255, 255, 255, 0.2); border-radius: 50%; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-right: 1rem;">
                        <i class="fas fa-check-circle" style="font-size: 1.75rem;"></i>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-size: 1.15rem; font-weight: 700; margin-bottom: 0.5rem; letter-spacing: 0.3px;">¡Éxito!</div>
                        <div style="font-size: 0.95rem; line-height: 1.5; opacity: 0.95;">${mensaje}</div>
                    </div>
                </div>
            `;
        } else {
            mensajeDiv.className = 'alert alert-danger mt-4';
            mensajeDiv.style.background = 'linear-gradient(135deg, #dc3545 0%, #c82333 100%)';
            mensajeDiv.style.color = 'white';
            mensajeDiv.style.border = 'none';
            mensajeDiv.style.borderRadius = '16px';
            mensajeDiv.style.padding = '1.5rem 1.75rem';
            mensajeDiv.style.fontWeight = '600';
            mensajeDiv.style.boxShadow = '0 4px 16px rgba(220, 53, 69, 0.3)';
            mensajeDiv.style.borderLeft = '4px solid rgba(255, 255, 255, 0.5)';
            mensajeDiv.innerHTML = `
                <div class="d-flex align-items-start">
                    <div style="background: rgba(255, 255, 255, 0.2); border-radius: 50%; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-right: 1rem;">
                        <i class="fas fa-exclamation-triangle" style="font-size: 1.75rem;"></i>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-size: 1.15rem; font-weight: 700; margin-bottom: 0.5rem; letter-spacing: 0.3px;">Error</div>
                        <div style="font-size: 0.95rem; line-height: 1.5; opacity: 0.95;">${mensaje}</div>
                    </div>
                </div>
            `;
        }
        
        mensajeDiv.classList.remove('hidden');
        mensajeDiv.style.display = 'block';

        // Auto-ocultar después de 5 segundos
        setTimeout(() => {
            mensajeDiv.classList.add('hidden');
            mensajeDiv.style.display = 'none';
        }, 5000);
    }

    // Activar escáner QR en detalle
    async function activarEscannerQRDetalle() {
        const container = document.getElementById('qrScannerContainerDetalle');
        const video = document.getElementById('qrVideoDetalle');
        const canvas = document.getElementById('qrCanvasDetalle');
        const context = canvas.getContext('2d');

        try {
            const stream = await navigator.mediaDevices.getUserMedia({ 
                video: { facingMode: 'environment' } 
            });
            
            qrStreamDetalle = stream;
            video.srcObject = stream;
            video.setAttribute('playsinline', true);
            video.play();
            container.style.display = 'block';
            qrScanningDetalle = true;

            video.addEventListener('loadedmetadata', () => {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
            });

            function scanQR() {
                if (!qrScanningDetalle) return;

                if (video.readyState === video.HAVE_ENOUGH_DATA) {
                    context.drawImage(video, 0, 0, canvas.width, canvas.height);
                    const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                    
                    // Usar jsQR si está disponible
                    if (typeof jsQR !== 'undefined') {
                        const code = jsQR(imageData.data, imageData.width, imageData.height);
                        if (code) {
                            detenerEscannerQRDetalle();
                            document.getElementById('ticketCodigoInputDetalle').value = code.data;
                            verificarTicketDetalle();
                        }
                    }
                }

                requestAnimationFrame(scanQR);
            }

            scanQR();

        } catch (error) {
            console.error('Error accediendo a la cámara:', error);
            mostrarMensajeDetalle('No se pudo acceder a la cámara. Por favor, verifica los permisos.', 'error');
        }
    }

    // Detener escáner QR en detalle
    function detenerEscannerQRDetalle() {
        qrScanningDetalle = false;
        if (qrStreamDetalle) {
            qrStreamDetalle.getTracks().forEach(track => track.stop());
            qrStreamDetalle = null;
        }
        const video = document.getElementById('qrVideoDetalle');
        if (video) {
            video.srcObject = null;
        }
        document.getElementById('qrScannerContainerDetalle').style.display = 'none';
    }

    // Procesar imagen QR importada
    function procesarQRImagenDetalle(event) {
        const file = event.target.files[0];
        if (!file) { return; }
        
        if (!file.type.startsWith('image/')) {
            mostrarMensajeDetalle('Por favor, selecciona un archivo de imagen válido', 'error');
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const img = new Image();
            img.onload = function() {
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                canvas.width = img.width;
                canvas.height = img.height;
                context.drawImage(img, 0, 0);
                const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                
                if (typeof jsQR !== 'undefined') {
                    const code = jsQR(imageData.data, imageData.width, imageData.height);
                    if (code) {
                        const ticketCodigo = code.data.trim();
                        document.getElementById('ticketCodigoInputDetalle').value = ticketCodigo;
                        mostrarMensajeDetalle('QR importado correctamente. Verificando...', 'success');
                        // Verificar automáticamente
                        setTimeout(() => {
                            verificarTicketDetalle();
                        }, 500);
                    } else {
                        mostrarMensajeDetalle('No se pudo leer el código QR de la imagen. Asegúrate de que la imagen sea clara y contenga un código QR válido.', 'error');
                    }
                } else {
                    mostrarMensajeDetalle('Error: La librería de QR no está cargada. Por favor, recarga la página.', 'error');
                }
            };
            img.onerror = function() {
                mostrarMensajeDetalle('Error al cargar la imagen. Por favor, intenta con otra imagen.', 'error');
            };
            img.src = e.target.result;
        };
        reader.onerror = function() {
            mostrarMensajeDetalle('Error al leer el archivo. Por favor, intenta nuevamente.', 'error');
        };
        reader.readAsDataURL(file);
        event.target.value = ''; // Clear input for re-selection
    }

    // ========== SISTEMA DE ALERTAS DE 5 MINUTOS ANTES DEL INICIO ==========
    let alertaEventoMostrada = false;
    
    // Verificar si el evento actual inicia en 5 minutos
    function verificarAlertaEventoActual() {
        const token = localStorage.getItem('token');
        if (!token || alertaEventoMostrada) return;

        const eventoIdInput = document.getElementById('eventoId');
        const eventoId = eventoIdInput ? eventoIdInput.value : null;
        if (!eventoId) return;

        // Usar el evento que ya está cargado en window.eventoActualGlobal si está disponible
        if (window.eventoActualGlobal && window.eventoActualGlobal.fecha_inicio) {
            const fechaInicio = new Date(window.eventoActualGlobal.fecha_inicio);
            const ahora = new Date();
            const diferenciaMs = fechaInicio - ahora;
            const minutosRestantes = Math.floor(diferenciaMs / (1000 * 60));

            // Si faltan entre 0 y 5 minutos para el inicio
            if (minutosRestantes >= 0 && minutosRestantes <= 5 && !alertaEventoMostrada) {
                mostrarAlertaEventoActual(window.eventoActualGlobal);
                alertaEventoMostrada = true;
            }
        } else {
            // Si no está cargado, obtenerlo de la API
            fetch(`${API_BASE_URL}/api/eventos/detalle/${eventoId}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.evento && data.evento.fecha_inicio) {
                    const fechaInicio = new Date(data.evento.fecha_inicio);
                    const ahora = new Date();
                    const diferenciaMs = fechaInicio - ahora;
                    const minutosRestantes = Math.floor(diferenciaMs / (1000 * 60));

                    // Si faltan entre 0 y 5 minutos para el inicio
                    if (minutosRestantes >= 0 && minutosRestantes <= 5 && !alertaEventoMostrada) {
                        mostrarAlertaEventoActual(data.evento);
                        alertaEventoMostrada = true;
                    }
                }
            })
            .catch(error => {
                console.error('Error verificando alerta del evento:', error);
            });
        }
    }

    // Mostrar alerta para el evento actual
    function mostrarAlertaEventoActual(evento) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'info',
                title: '¡Evento por comenzar!',
                html: `
                    <div class="text-left">
                        <p class="mb-3"><strong>${evento.titulo || 'Evento'}</strong></p>
                        <p class="mb-2">El evento está por comenzar. Recuerda que podrás registrar tu asistencia cuando se habilite la opción.</p>
                        <p class="text-sm text-gray-600">Faltan 5 minutos para el inicio del evento.</p>
                    </div>
                `,
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#00A36C',
                timer: 10000,
                timerProgressBar: true
            });
        }
    }

    // Verificar alerta cuando se carga la página (después de que cargue el evento)
    setTimeout(() => {
        verificarAlertaEventoActual();
    }, 3000); // Esperar 3 segundos para que cargue el evento

    // Verificar cada minuto
    setInterval(verificarAlertaEventoActual, 60000);
</script>
@endsection

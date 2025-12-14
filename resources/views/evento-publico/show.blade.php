<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $evento->titulo ?? 'Evento' }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .evento-banner {
            height: 450px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow: hidden;
            border-radius: 0 0 24px 24px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        }
        .banner-image {
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            opacity: 0.25;
            transition: transform 0.5s ease;
        }
        .banner-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, rgba(102, 126, 234, 0.4) 0%, rgba(118, 75, 162, 0.7) 100%);
        }
        .banner-content {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 3rem 2rem;
            color: white;
        }
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

        .card {
            border-radius: 16px;
            border: none !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.12) !important;
        }

        .btn-participar {
            background: linear-gradient(135deg, #00A36C 0%, #008a5a 100%);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 163, 108, 0.3);
        }
        .btn-participar:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 163, 108, 0.4);
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #E0E0E0;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            outline: none;
        }
        .btn-outline-danger:hover, .btn-danger {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        }
        .btn-outline-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
        }
        #contadorReaccionesPublico {
            background: rgba(255, 255, 255, 0.2);
            color: #dc3545;
            padding: 0.25em 0.5em;
            border-radius: 12px;
            font-weight: 600;
        }

        /* Badges mejorados */
        .badge {
            border-radius: 50px;
            padding: 0.5em 1em;
            font-weight: 500;
            letter-spacing: 0.3px;
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

            .evento-banner {
                height: 350px !important;
            }

            .evento-banner h1 {
                font-size: 2rem !important;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid px-0">
        <!-- Banner Superior -->
        <div class="evento-banner">
            <div class="banner-image" id="bannerImage"></div>
            <div class="banner-overlay"></div>
            <div class="banner-content">
                <div class="container">
                    <div class="d-flex align-items-center mb-3">
                        <div class="mr-3" style="width: 64px; height: 64px; background: rgba(255,255,255,0.15); border-radius: 16px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px); box-shadow: 0 4px 16px rgba(0,0,0,0.2);">
                            <i class="fas fa-calendar-alt" style="font-size: 2rem;"></i>
                        </div>
                        <div style="flex: 1;">
                            <h1 class="mb-2" style="font-size: 2.75rem; font-weight: 700; text-shadow: 2px 2px 8px rgba(0,0,0,0.5); letter-spacing: -0.5px; line-height: 1.2;">{{ $evento->titulo ?? 'Evento' }}</h1>
                            <div class="d-flex flex-wrap align-items-center" style="gap: 0.75rem;">
                                <span class="badge" style="font-size: 0.95rem; padding: 0.6em 1.2em; background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); border-radius: 50px; font-weight: 500;">{{ $evento->tipo_evento ?? 'Evento' }}</span>
                                <span class="badge badge-success" style="font-size: 0.95rem; padding: 0.6em 1.2em; border-radius: 50px; font-weight: 500;">{{ ucfirst($evento->estado ?? 'Publicado') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Compartir -->
        <div id="modalCompartirPublico" class="modal fade" tabindex="-1" role="dialog" style="display: none;">
            <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 400px;">
                <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 8px 32px rgba(0,0,0,0.2);">
                    <div class="modal-header" style="border-bottom: 1px solid #F5F5F5; padding: 1.5rem;">
                        <h5 class="modal-title" style="color: #2c3e50; font-weight: 700; font-size: 1.25rem;">Compartir</h5>
                        <button type="button" class="close" onclick="cerrarModalCompartirPublico()" aria-label="Close" style="border: none; background: none; font-size: 1.5rem; color: #333; opacity: 0.5; cursor: pointer;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="padding: 2rem;">
                        <div class="row text-center">
                            <!-- Copiar enlace -->
                            <div class="col-6 mb-4">
                                <button onclick="copiarEnlacePublico()" class="btn btn-link p-0" style="text-decoration: none; border: none; background: none; width: 100%;">
                                    <div style="width: 80px; height: 80px; background: #F5F5F5; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(0,0,0,0.1);" onmouseover="this.style.background='#E9ECEF'; this.style.transform='scale(1.1)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)'" onmouseout="this.style.background='#F5F5F5'; this.style.transform='scale(1)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.1)'">
                                        <i class="fas fa-link" style="font-size: 2rem; color: #2c3e50;"></i>
                                    </div>
                                    <span style="color: #333; font-size: 0.9rem; font-weight: 600;">Copiar enlace</span>
                                </button>
                            </div>
                            <!-- QR Code -->
                            <div class="col-6 mb-4">
                                <button onclick="mostrarQRPublico()" class="btn btn-link p-0" style="text-decoration: none; border: none; background: none; width: 100%;">
                                    <div style="width: 80px; height: 80px; background: #667eea; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(102,126,234,0.3);" onmouseover="this.style.background='#764ba2'; this.style.transform='scale(1.1)'; this.style.boxShadow='0 4px 12px rgba(118,75,162,0.4)'" onmouseout="this.style.background='#667eea'; this.style.transform='scale(1)'; this.style.boxShadow='0 2px 8px rgba(102,126,234,0.3)'">
                                        <i class="fas fa-qrcode" style="font-size: 2rem; color: white;"></i>
                                    </div>
                                    <span style="color: #333; font-size: 0.9rem; font-weight: 600;">Código QR</span>
                                </button>
                            </div>
                        </div>
                        <!-- Contenedor para el QR -->
                        <div id="qrContainerPublico" style="display: none; margin-top: 1.5rem;">
                            <div class="text-center">
                                <div id="qrcodePublico" style="display: inline-block; padding: 1rem; background: white; border-radius: 12px; margin-bottom: 1rem;"></div>
                                <p style="color: #333; font-size: 0.9rem; margin: 0;">Escanea este código para acceder al evento</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mt-4">
            <!-- Botones de Acción (Reacción y Compartir) - Mejorados -->
            <div class="d-flex justify-content-end mb-4 flex-wrap" style="gap: 0.75rem;">
                <button class="btn btn-outline-danger d-flex align-items-center" id="btnReaccionarPublico" style="border-radius: 10px; padding: 0.6rem 1.5rem; font-weight: 500; box-shadow: 0 2px 8px rgba(220, 53, 69, 0.2); transition: all 0.3s ease;">
                    <i class="far fa-heart mr-2" id="iconoCorazonPublico" style="transition: all 0.3s ease;"></i>
                    <span id="textoReaccionPublico">Me gusta</span>
                    <span class="badge badge-light ml-2" id="contadorReaccionesPublico" style="background: rgba(255,255,255,0.3); color: #dc3545;">0</span>
                </button>
                <button class="btn btn-outline-primary d-flex align-items-center" id="btnCompartirPublico" style="border-radius: 10px; padding: 0.6rem 1.5rem; font-weight: 500; box-shadow: 0 2px 8px rgba(0, 123, 255, 0.2); transition: all 0.3s ease;">
                    <i class="fas fa-share-alt mr-2"></i> Compartir 
                    <span id="contadorCompartidosPublico" class="badge badge-light ml-2" style="background: rgba(255,255,255,0.3); color: #007bff; font-weight: 600;">0</span>
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
                            <p class="mb-0 text-muted" style="line-height: 1.8; font-size: 1rem; color: #495057;">{{ $evento->descripcion ?? 'Sin descripción disponible.' }}</p>
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
                                            <p class="info-value">{{ $evento->fecha_inicio ? \Carbon\Carbon::parse($evento->fecha_inicio)->format('d/m/Y H:i') : 'No especificada' }}</p>
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
                                            <p class="info-value">{{ $evento->fecha_fin ? \Carbon\Carbon::parse($evento->fecha_fin)->format('d/m/Y H:i') : 'No especificada' }}</p>
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
                                            <p class="info-value">{{ $evento->capacidad_maxima ? $evento->capacidad_maxima . ' personas' : 'Sin límite' }}</p>
                                        </div>
                                    </div>
                                </div>
                                @if($evento->creador && $evento->creador['nombre'])
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-user-circle text-primary mr-3 mt-1" style="font-size: 1.2rem;"></i>
                                        <div>
                                            <h6 class="mb-1" style="color: #495057; font-weight: 600;">Creado por</h6>
                                            <div class="d-flex align-items-center" style="gap: 0.5rem;">
                                                @if($evento->creador['foto_perfil'])
                                                    <img src="{{ $evento->creador['foto_perfil'] }}" alt="{{ $evento->creador['nombre'] }}" 
                                                         class="rounded-circle" 
                                                         style="width: 32px; height: 32px; object-fit: cover; border: 2px solid #667eea;">
                                                @else
                                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                                                         style="width: 32px; height: 32px; font-weight: 600; font-size: 0.9rem;">
                                                        {{ strtoupper(substr($evento->creador['nombre'], 0, 1)) }}
                                                    </div>
                                                @endif
                                                <span style="color: #495057; font-weight: 500;">{{ $evento->creador['nombre'] }}</span>
                                                <span class="badge badge-info" style="font-size: 0.75rem; padding: 0.25em 0.5em;">{{ $evento->creador['tipo'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Ubicación -->
                    @if($evento->ciudad || $evento->direccion)
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
                                @if($evento->ciudad)
                                <div class="col-md-6 mb-3">
                                    <div class="info-item">
                                        <div class="info-icon">
                                            <i class="fas fa-city"></i>
                                        </div>
                                        <div class="info-content">
                                            <h6 class="info-label">Ciudad</h6>
                                            <p class="info-value">{{ $evento->ciudad }}</p>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @if($evento->direccion)
                                <div class="col-md-6 mb-3">
                                    <div class="info-item">
                                        <div class="info-icon">
                                            <i class="fas fa-road"></i>
                                        </div>
                                        <div class="info-content">
                                            <h6 class="info-label">Dirección</h6>
                                            <p class="info-value">{{ $evento->direccion }}</p>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                            @if($evento->lat && $evento->lng)
                            <div id="mapContainer" class="mt-3 rounded" style="height: 350px; overflow: hidden; border: 2px solid #f0f0f0; box-shadow: 0 2px 12px rgba(0,0,0,0.05);"></div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Galería de Imágenes -->
                    @if($evento->imagenes && is_array($evento->imagenes) && count($evento->imagenes) > 0)
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
                            <div class="row">
                                @foreach($evento->imagenes as $index => $imgUrl)
                                    @php
                                        $fullUrl = $imgUrl; // El accessor del modelo ya devuelve URLs completas
                                    @endphp
                                    @if($fullUrl)
                                    <div class="col-md-4 mb-3">
                                        <div class="gallery-item" style="border-radius: 12px; overflow: hidden; cursor: pointer; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: all 0.3s ease;" onclick="mostrarImagenGaleriaPublico('{{ $fullUrl }}')" onmouseover="this.style.transform='scale(1.05)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.15)'" onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.1)'">
                                            <img src="{{ $fullUrl }}"
                                                 alt="Imagen {{ $index + 1 }}"
                                                 style="width: 100%; height: 200px; object-fit: cover; display: block;"
                                                 onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%27400%27 height=%27200%27%3E%3Crect fill=%27%23f8f9fa%27 width=%27400%27 height=%27200%27/%3E%3Ctext x=%2750%25%27 y=%2750%25%27 text-anchor=%27middle%27 dy=%27.3em%27 fill=%27%23adb5bd%27 font-family=%27Arial%27 font-size=%2714%27%3EImagen no disponible%3C/text%3E%3C/svg%3E';">
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Sidebar - Botón de Participación -->
                <div class="col-lg-4">
                    <div class="card mb-4" style="position: sticky; top: 20px;">
                        <div class="card-body p-4">
                            <h5 class="mb-4" style="color: #2c3e50; font-weight: 600;">
                                <i class="fas fa-user-plus mr-2 text-primary"></i> Participar en este Evento
                            </h5>
                            <!-- Mensaje informativo -->
                            <div class="alert alert-info mb-3" style="border-radius: 10px;">
                                <i class="fas fa-info-circle mr-2"></i>
                                <strong>¿Quieres participar?</strong>
                                <p class="mb-0 mt-2" style="font-size: 0.9rem;">Debes tener una cuenta de Usuario Externo para inscribirte en este evento.</p>
                            </div>
                            <!-- Botón de Participar -->
                            <button type="button" class="btn btn-participar w-100" id="btnParticiparPublico">
                                    <i class="fas fa-check-circle mr-2"></i> Participar
                                </button>
                            <div id="mensajeParticipacion" class="mt-3" style="display: none;"></div>
                        </div>
                    </div>

                    <!-- Información Rápida -->
                    <div class="card">
                        <div class="card-body p-4">
                            <h5 class="mb-3" style="color: #2c3e50; font-weight: 600;">
                                <i class="fas fa-info-circle mr-2 text-primary"></i> Información Rápida
                            </h5>
                            <div class="mb-3 pb-3 border-bottom">
                                <small class="text-muted d-block mb-1">Estado</small>
                                <span class="badge badge-success">{{ ucfirst($evento->estado ?? 'Publicado') }}</span>
                            </div>
                            <div class="mb-3 pb-3 border-bottom">
                                <small class="text-muted d-block mb-1">Tipo de Evento</small>
                                <span class="text-dark font-weight-bold">{{ ucfirst($evento->tipo_evento ?? 'N/A') }}</span>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted d-block mb-1">Capacidad</small>
                                <span class="text-dark font-weight-bold">{{ $evento->capacidad_maxima ? $evento->capacidad_maxima . ' personas' : 'Sin límite' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
    {{-- Lucide icons para página pública de evento (opcional) --}}
    <script type="module">
        (async () => {
            try {
                const { createIcons, icons } = await import("https://unpkg.com/lucide@latest/dist/esm/lucide.js").catch(() => null);
                if (!icons || !createIcons) return;

                const faToLucidePublic = {
                    'fa-link': 'link-2',
                    'fa-qrcode': 'qr-code',
                    'fa-heart': 'heart',
                    'fa-share-alt': 'share-2',
                    'fa-align-left': 'align-left',
                    'fa-info-circle': 'info',
                    'fa-calendar-alt': 'calendar',
                    'fa-calendar-check': 'calendar-check',
                    'fa-users': 'users',
                    'fa-map-marker-alt': 'map-pin',
                    'fa-images': 'images',
                    'fa-user-plus': 'user-plus',
                    'fa-check-circle': 'check-circle-2',
                };

                window.addEventListener('DOMContentLoaded', () => {
                    try {
                        document.querySelectorAll('i[class*="fa-"]').forEach(el => {
                            const classes = el.className.split(/\s+/);
                            const faClass = classes.find(c => c.startsWith('fa-') || c.startsWith('fas') || c.startsWith('far'));
                            if (!faClass) return;

                            // Buscar la clase tipo fa-xxx
                            const faIconClass = classes.find(c => c.startsWith('fa-'));
                            if (!faIconClass) return;

                            const lucideName = faToLucidePublic[faIconClass];
                            if (!lucideName || !icons[lucideName]) return;

                            el.setAttribute('data-lucide', lucideName);
                            el.className = classes.filter(c => !c.startsWith('fa')).join(' ').trim();
                        });

                        createIcons({ icons });
                    } catch (e) {
                        // Silenciar error, usar Font Awesome como fallback
                    }
                });
            } catch (e) {
                // Silenciar error, usar Font Awesome como fallback
            }
        })();
    </script>
    <script>
        const eventoId = {{ $eventoId }};
        const API_BASE_URL = '{{ url("/") }}';
        const PUBLIC_BASE_URL = typeof getPublicUrl !== 'undefined' 
            ? (window.PUBLIC_BASE_URL || 'http://192.168.0.7:8000')
            : 'http://192.168.0.7:8000';
        
        // Almacenar datos del evento para compartir
        window.eventoParaCompartir = {
            id: eventoId,
            titulo: '{{ addslashes($evento->titulo ?? "Evento") }}',
            url: `${PUBLIC_BASE_URL}/evento/${eventoId}/qr`
        };
        
        // Modal para ver imagen de la galería
        function mostrarImagenGaleriaPublico(url) {
            let modal = document.getElementById('modalImagenPublica');
            if (!modal) {
                modal = document.createElement('div');
                modal.id = 'modalImagenPublica';
                modal.className = 'modal fade';
                modal.tabIndex = -1;
                modal.role = 'dialog';
                modal.innerHTML = `
                    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 800px;">
                        <div class="modal-content" style="border-radius: 12px; overflow: hidden;">
                            <div class="modal-body p-0" style="background: #000;">
                                <img id="imagenPublicaAmpliada" src="" alt="Imagen" style="width: 100%; height: auto; display: block;">
                            </div>
                        </div>
                    </div>
                `;
                document.body.appendChild(modal);
            }

            const img = document.getElementById('imagenPublicaAmpliada');
            if (img) {
                img.src = url;
            }

            if (typeof $ !== 'undefined') {
                $('#modalImagenPublica').modal('show');
            } else {
                modal.style.display = 'block';
                modal.classList.add('show');
                document.body.classList.add('modal-open');
                const backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                backdrop.onclick = () => {
                    modal.style.display = 'none';
                    modal.classList.remove('show');
                    document.body.classList.remove('modal-open');
                    backdrop.remove();
                };
                document.body.appendChild(backdrop);
            }
        }

        // Registrar compartido
        async function registrarCompartidoPublico(metodo) {
            try {
                const nombres = document.getElementById('nombres')?.value || '';
                const apellidos = document.getElementById('apellidos')?.value || '';
                const email = document.getElementById('email')?.value || '';
                
                await fetch(`${API_BASE_URL}/api/eventos/${eventoId}/compartir-publico`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        metodo: metodo,
                        nombres: nombres || null,
                        apellidos: apellidos || null,
                        email: email || null
                    })
                });
                
                // Actualizar contador
                await cargarContadorCompartidosPublico();
            } catch (error) {
                console.warn('Error registrando compartido:', error);
            }
        }
        
        // Cargar contador de compartidos
        async function cargarContadorCompartidosPublico() {
            try {
                const res = await fetch(`${API_BASE_URL}/api/eventos/${eventoId}/compartidos/total`);
                const data = await res.json();
                
                if (data.success) {
                    const contador = document.getElementById('contadorCompartidosPublico');
                    if (contador) {
                        contador.textContent = data.total_compartidos || 0;
                    }
                }
            } catch (error) {
                console.warn('Error cargando contador de compartidos:', error);
            }
        }
        
        // Cargar contador de reacciones al inicio (endpoint público)
        async function cargarReaccionesPublico() {
            try {
                const res = await fetch(`${API_BASE_URL}/api/reacciones/evento/${eventoId}/total`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    document.getElementById('contadorReaccionesPublico').textContent = data.total_reacciones || 0;
                }
            } catch (error) {
                console.error('Error cargando reacciones:', error);
            }
        }
        
        // Toggle reacción (público - solo si está autenticado)
        async function toggleReaccionPublico() {
            const token = localStorage.getItem('token');
            
            if (!token) {
                // No está autenticado - redirigir a login
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Inicia sesión',
                        text: 'Debes iniciar sesión para reaccionar a este evento.',
                        confirmButtonText: 'Ir a Login',
                        cancelButtonText: 'Cancelar',
                        showCancelButton: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '/login';
                        }
                    });
                } else {
                    alert('Debes iniciar sesión para reaccionar');
                    window.location.href = '/login';
                }
                return;
            }
            
            const btnReaccionar = document.getElementById('btnReaccionarPublico');
            const iconoCorazon = document.getElementById('iconoCorazonPublico');
            const textoReaccion = document.getElementById('textoReaccionPublico');
            const contadorReacciones = document.getElementById('contadorReaccionesPublico');
            const estaReaccionado = btnReaccionar.classList.contains('btn-danger');
            
            try {
                const res = await fetch(`${API_BASE_URL}/api/reacciones/evento/${eventoId}/toggle`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
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
                        iconoCorazon.className = 'far fa-heart mr-2';
                        btnReaccionar.classList.remove('btn-danger');
                        btnReaccionar.classList.add('btn-outline-danger');
                        textoReaccion.textContent = 'Me gusta';
                    }
                    contadorReacciones.textContent = data.total_reacciones || 0;
                } else {
                    alert(data.error || 'Error al procesar reacción');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error de conexión');
            }
        }
        
        // Modal de compartir
        function mostrarModalCompartirPublico() {
            const modal = document.getElementById('modalCompartirPublico');
            if (modal) {
                $(modal).modal('show');
            }
        }
        
        function cerrarModalCompartirPublico() {
            const modal = document.getElementById('modalCompartirPublico');
            if (modal) {
                $(modal).modal('hide');
            }
        }
        
        // Copiar enlace
        async function copiarEnlacePublico() {
            const evento = window.eventoParaCompartir;
            if (!evento) return;
            
            // Registrar compartido
            await registrarCompartidoPublico('link');
            
            const url = evento.url;
            
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(url).then(() => {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Enlace copiado!',
                        text: 'El enlace se ha copiado al portapapeles',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }).catch(() => {
                    // Fallback para navegadores antiguos
                    const textarea = document.createElement('textarea');
                    textarea.value = url;
                    document.body.appendChild(textarea);
                    textarea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textarea);
                    Swal.fire({
                        icon: 'success',
                        title: '¡Enlace copiado!',
                        text: 'El enlace se ha copiado al portapapeles',
                        timer: 2000,
                        showConfirmButton: false
                    });
                });
            } else {
                // Fallback para navegadores antiguos
                const textarea = document.createElement('textarea');
                textarea.value = url;
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
                Swal.fire({
                    icon: 'success',
                    title: '¡Enlace copiado!',
                    text: 'El enlace se ha copiado al portapapeles',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
            cerrarModalCompartirPublico();
        }
        
        // Mostrar QR
        async function mostrarQRPublico() {
            const evento = window.eventoParaCompartir;
            if (!evento) return;
            
            // Registrar compartido
            await registrarCompartidoPublico('qr');
            
            const qrContainer = document.getElementById('qrContainerPublico');
            const qrcodeDiv = document.getElementById('qrcodePublico');
            
            if (!qrContainer || !qrcodeDiv) return;
            
            const qrUrl = evento.url;
            qrcodeDiv.innerHTML = '';
            qrContainer.style.display = 'block';
            
            qrcodeDiv.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x" style="color: #667eea;"></i><p class="mt-2" style="color: #333;">Generando QR...</p></div>';
            
            if (typeof QRCode === 'undefined') {
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js';
                script.onload = function() {
                    generarQRPublico(qrUrl, qrcodeDiv);
                };
                script.onerror = function() {
                    generarQRConAPIPublico(qrUrl, qrcodeDiv);
                };
                document.head.appendChild(script);
            } else {
                generarQRPublico(qrUrl, qrcodeDiv);
            }
        }
        
        function generarQRPublico(qrUrl, qrcodeDiv) {
            try {
                QRCode.toCanvas(qrcodeDiv, qrUrl, {
                    width: 250,
                    margin: 2,
                    color: {
                        dark: '#667eea',
                        light: '#FFFFFF'
                    },
                    errorCorrectionLevel: 'M'
                }, function (error) {
                    if (error) {
                        generarQRConAPIPublico(qrUrl, qrcodeDiv);
                    } else {
                        const canvas = qrcodeDiv.querySelector('canvas');
                        if (canvas) {
                            canvas.style.display = 'block';
                            canvas.style.margin = '0 auto';
                        }
                    }
                });
            } catch (error) {
                generarQRConAPIPublico(qrUrl, qrcodeDiv);
            }
        }
        
        function generarQRConAPIPublico(qrUrl, qrcodeDiv) {
            const apiUrl = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodeURIComponent(qrUrl)}&bgcolor=FFFFFF&color=667eea`;
            qrcodeDiv.innerHTML = `<img src="${apiUrl}" alt="QR Code" style="display: block; margin: 0 auto; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">`;
        }
        
        // Inicializar
        document.addEventListener('DOMContentLoaded', function() {
            // Configurar botones
            document.getElementById('btnReaccionarPublico').addEventListener('click', toggleReaccionPublico);
            document.getElementById('btnCompartirPublico').addEventListener('click', mostrarModalCompartirPublico);
            
            // Cargar reacciones
            cargarReaccionesPublico();
            // Cargar contador de compartidos
            cargarContadorCompartidosPublico();
        });
        
        // Helper para construir URL de imagen
        function buildImageUrl(imgUrl) {
            if (!imgUrl || imgUrl.trim() === '') return null;
            if (imgUrl.startsWith('http://') || imgUrl.startsWith('https://')) return imgUrl;
            if (imgUrl.startsWith('/storage/')) return `${window.location.origin}${imgUrl}`;
            if (imgUrl.startsWith('storage/')) return `${window.location.origin}/${imgUrl}`;
            return `${window.location.origin}/storage/${imgUrl}`;
        }
        
        // Cargar imagen del banner
        @if($evento->imagenes && count($evento->imagenes) > 0)
        const primeraImagen = buildImageUrl('{{ $evento->imagenes[0] }}');
        if (primeraImagen) {
            document.getElementById('bannerImage').style.backgroundImage = `url(${primeraImagen})`;
        }
        @endif

        // Mapa
        @if($evento->lat && $evento->lng)
        document.addEventListener('DOMContentLoaded', function() {
            const map = L.map('mapContainer').setView([{{ $evento->lat }}, {{ $evento->lng }}], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);
            L.marker([{{ $evento->lat }}, {{ $evento->lng }}]).addTo(map).bindPopup('{{ $evento->direccion ?? $evento->ciudad ?? "Ubicación del evento" }}');
        });
        @endif

        // Función para verificar si ya participa
        // Función verificarParticipacion eliminada - ya no se necesita

        // Código de verificación de participación eliminado - ya no se necesita formulario público

        // Botón de Participar - Redirige a registro o login
        document.getElementById('btnParticiparPublico').addEventListener('click', function() {
            const token = localStorage.getItem('token');
            
            if (!token) {
                // No está autenticado - redirigir a registro con eventoId
                const eventoId = {{ $eventoId }};
                window.location.href = `/register-externo?eventoId=${eventoId}`;
                    } else {
                // Está autenticado - verificar si es usuario externo y si ya está inscrito
                verificarYParticipar();
            }
        });
        
        // Verificar si el usuario autenticado ya está participando o inscribirlo
        async function verificarYParticipar() {
            const token = localStorage.getItem('token');
            const eventoId = {{ $eventoId }};
            
            try {
                // Verificar si ya está inscrito
                const resVerificar = await fetch(`${API_BASE_URL}/api/participaciones/mis-eventos`, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });
                
                const dataVerificar = await resVerificar.json();
            
                if (dataVerificar.success && dataVerificar.eventos) {
                    const yaInscrito = dataVerificar.eventos.some(e => e.id === eventoId);
                    
                    if (yaInscrito) {
                Swal.fire({
                    icon: 'info',
                    title: 'Ya estás participando',
                            text: 'Ya estás inscrito en este evento.',
                    confirmButtonText: 'Aceptar'
                });
                return;
                    }
            }

                // Si no está inscrito, inscribirlo
                const resInscribir = await fetch(`${API_BASE_URL}/api/participaciones/inscribir`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ evento_id: eventoId })
                });

                const dataInscribir = await resInscribir.json();

                if (dataInscribir.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Inscripción exitosa!',
                        text: 'Te has inscrito correctamente en el evento.',
                        confirmButtonText: 'Aceptar'
                    }).then(() => {
                        location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                        text: dataInscribir.error || 'Error al inscribirte en el evento'
                        });
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo conectar con el servidor. Por favor, intenta nuevamente.'
                });
            }
        }
        
        // (Implementaciones duplicadas de cargarReaccionesPublico y toggleReaccionPublico eliminadas)
        
        // Modal de compartir
        function mostrarModalCompartirPublico() {
            const modal = document.getElementById('modalCompartirPublico');
            if (modal) {
                $(modal).modal('show');
            }
        }
        
        function cerrarModalCompartirPublico() {
            const modal = document.getElementById('modalCompartirPublico');
            if (modal) {
                $(modal).modal('hide');
            }
        }
        
        // Copiar enlace
        async function copiarEnlacePublico() {
            const evento = window.eventoParaCompartir;
            if (!evento) return;
            
            // Registrar compartido
            await registrarCompartidoPublico('link');
            
            const url = evento.url;
            
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(url).then(() => {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Enlace copiado!',
                        text: 'El enlace se ha copiado al portapapeles',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }).catch(() => {
                    const textarea = document.createElement('textarea');
                    textarea.value = url;
                    document.body.appendChild(textarea);
                    textarea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textarea);
                    Swal.fire({
                        icon: 'success',
                        title: '¡Enlace copiado!',
                        text: 'El enlace se ha copiado al portapapeles',
                        timer: 2000,
                        showConfirmButton: false
                    });
                });
            } else {
                const textarea = document.createElement('textarea');
                textarea.value = url;
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
                Swal.fire({
                    icon: 'success',
                    title: '¡Enlace copiado!',
                    text: 'El enlace se ha copiado al portapapeles',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
            cerrarModalCompartirPublico();
        }
        
        // Mostrar QR
        async function mostrarQRPublico() {
            const evento = window.eventoParaCompartir;
            if (!evento) return;
            
            // Registrar compartido
            await registrarCompartidoPublico('qr');
            
            const qrContainer = document.getElementById('qrContainerPublico');
            const qrcodeDiv = document.getElementById('qrcodePublico');
            
            if (!qrContainer || !qrcodeDiv) return;
            
            const qrUrl = evento.url;
            qrcodeDiv.innerHTML = '';
            qrContainer.style.display = 'block';
            
            qrcodeDiv.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x" style="color: #667eea;"></i><p class="mt-2" style="color: #333;">Generando QR...</p></div>';
            
            if (typeof QRCode === 'undefined') {
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js';
                script.onload = function() {
                    generarQRPublico(qrUrl, qrcodeDiv);
                };
                script.onerror = function() {
                    generarQRConAPIPublico(qrUrl, qrcodeDiv);
                };
                document.head.appendChild(script);
            } else {
                generarQRPublico(qrUrl, qrcodeDiv);
            }
        }
        
        function generarQRPublico(qrUrl, qrcodeDiv) {
            try {
                QRCode.toCanvas(qrcodeDiv, qrUrl, {
                    width: 250,
                    margin: 2,
                    color: {
                        dark: '#667eea',
                        light: '#FFFFFF'
                    },
                    errorCorrectionLevel: 'M'
                }, function (error) {
                    if (error) {
                        generarQRConAPIPublico(qrUrl, qrcodeDiv);
                    } else {
                        const canvas = qrcodeDiv.querySelector('canvas');
                        if (canvas) {
                            canvas.style.display = 'block';
                            canvas.style.margin = '0 auto';
                        }
                    }
                });
            } catch (error) {
                generarQRConAPIPublico(qrUrl, qrcodeDiv);
            }
        }
        
        function generarQRConAPIPublico(qrUrl, qrcodeDiv) {
            const apiUrl = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodeURIComponent(qrUrl)}&bgcolor=FFFFFF&color=667eea`;
            qrcodeDiv.innerHTML = `<img src="${apiUrl}" alt="QR Code" style="display: block; margin: 0 auto; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">`;
        }
        
        // Inicializar botones
        document.addEventListener('DOMContentLoaded', function() {
            const btnReaccionar = document.getElementById('btnReaccionarPublico');
            const btnCompartir = document.getElementById('btnCompartirPublico');
            
            if (btnReaccionar) {
                btnReaccionar.addEventListener('click', toggleReaccionPublico);
            }
            if (btnCompartir) {
                btnCompartir.addEventListener('click', mostrarModalCompartirPublico);
            }
            
            // Cargar reacciones
            cargarReaccionesPublico();
            // Cargar contador de compartidos
            cargarContadorCompartidosPublico();
            
            // Iniciar auto-refresco de reacciones para usuarios random
            iniciarAutoRefrescoReaccionesPublico();
        });
        
        // Auto-refresco de reacciones para usuarios random
        let refrescoReaccionesIntervalPublico = null;
        
        function iniciarAutoRefrescoReaccionesPublico() {
            try {
                if (refrescoReaccionesIntervalPublico) {
                    clearInterval(refrescoReaccionesIntervalPublico);
                }

                // Cada 10 segundos actualiza el contador de reacciones
                refrescoReaccionesIntervalPublico = setInterval(async () => {
                    try {
                        await cargarReaccionesPublico();
                    } catch (err) {
                        console.warn('Error en auto-refresco de reacciones:', err);
                    }
                }, 10000); // Cada 10 segundos
            } catch (error) {
                console.warn('Error iniciando auto-refresco de reacciones:', error);
            }
        }
    </script>
</body>
</html>


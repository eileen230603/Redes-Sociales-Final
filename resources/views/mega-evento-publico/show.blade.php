<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $megaEvento->titulo ?? 'Mega Evento' }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <style>
        /* Variables CSS */
        :root {
            --primary-color: #ffc107;
            --secondary-color: #ff9800;
            --text-dark: #2c3e50;
            --text-muted: #6c757d;
            --border-radius: 16px;
            --shadow-sm: 0 2px 8px rgba(0,0,0,0.08);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.12);
            --shadow-lg: 0 8px 24px rgba(0,0,0,0.15);
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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

        .evento-banner {
            height: 450px;
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
            position: relative;
            overflow: hidden;
        }

        .banner-image {
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            opacity: 0.25;
            transition: transform 0.5s ease;
        }

        .evento-banner:hover .banner-image {
            transform: scale(1.05);
        }

        .banner-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, rgba(255, 193, 7, 0.4) 0%, rgba(255, 152, 0, 0.7) 100%);
        }

        .banner-content {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 3rem 2rem;
            color: white;
        }

        /* Section Icon (para títulos principales) */
        .section-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.25rem;
            box-shadow: 0 4px 12px rgba(255, 193, 7, 0.2);
            flex-shrink: 0;
            transition: all 0.3s ease;
        }

        .section-icon:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(255, 193, 7, 0.3);
        }

        /* Info Icon (para items individuales) */
        .info-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffc107;
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
            background: rgba(255, 193, 7, 0.03);
            transform: translateX(5px);
        }

        .info-item:hover .info-icon {
            color: #ff9800;
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

        .card {
            border-radius: var(--border-radius);
            border: none;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: var(--shadow-md) !important;
            transform: translateY(-2px);
        }

        .btn-participar {
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
            color: #333;
            border: none;
            border-radius: 12px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(255, 193, 7, 0.2);
        }

        .btn-participar:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
        }

        .form-control {
            border-radius: 12px;
            border: 1px solid #E0E0E0;
            padding: 0.75rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #ffc107;
            box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
            transform: translateY(-1px);
        }

        .btn-outline-danger:hover, .btn-danger {
            transform: translateY(-2px);
        }

        .btn-outline-warning:hover {
            transform: translateY(-2px);
        }

        #contadorReaccionesPublico {
            background: rgba(255, 255, 255, 0.2);
            color: #333;
            padding: 0.25em 0.5em;
            border-radius: 12px;
            font-weight: 600;
        }

        /* Galería de imágenes mejorada */
        .gallery-item {
            transition: all 0.3s ease;
        }

        .gallery-item:hover img {
            transform: scale(1.05);
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

            .evento-banner {
                height: 350px !important;
            }

            h1 {
                font-size: 2rem !important;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid px-0">
        <!-- Banner Superior - Mejorado -->
        <div class="evento-banner" style="border-radius: 0 0 24px 24px;">
            <div class="banner-image" id="bannerImage" style="transition: transform 0.5s ease;"></div>
            <div class="banner-overlay"></div>
            <div class="banner-content">
                <div class="container">
                    <div class="d-flex align-items-center mb-3">
                        <div class="mr-3" style="width: 64px; height: 64px; background: rgba(255,255,255,0.15); border-radius: 16px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px); box-shadow: 0 4px 16px rgba(0,0,0,0.2); animation: pulse 2s ease-in-out infinite;">
                            <i class="fas fa-star" style="font-size: 2rem; color: white;"></i>
                        </div>
                        <div style="flex: 1;">
                            <h1 class="mb-2" style="font-size: 2.75rem; font-weight: 700; text-shadow: 2px 2px 8px rgba(0,0,0,0.5); letter-spacing: -0.5px; line-height: 1.2; animation: fadeInUp 0.6s ease-out;">{{ $megaEvento->titulo ?? 'Mega Evento' }}</h1>
                            <div class="d-flex flex-wrap align-items-center" style="gap: 0.75rem;">
                                <span class="badge" style="font-size: 0.95rem; padding: 0.6em 1.2em; background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); border-radius: 50px; font-weight: 500; animation: fadeInUp 0.8s ease-out;">{{ ucfirst($megaEvento->categoria ?? 'Mega Evento') }}</span>
                                <span class="badge badge-success" style="font-size: 0.95rem; padding: 0.6em 1.2em; border-radius: 50px; font-weight: 500; animation: fadeInUp 0.9s ease-out;">{{ ucfirst($megaEvento->estado ?? 'Publicado') }}</span>
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
                                    <div style="width: 80px; height: 80px; background: #ffc107; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(255,193,7,0.3);" onmouseover="this.style.background='#ff9800'; this.style.transform='scale(1.1)'; this.style.boxShadow='0 4px 12px rgba(255,152,0,0.4)'" onmouseout="this.style.background='#ffc107'; this.style.transform='scale(1)'; this.style.boxShadow='0 2px 8px rgba(255,193,7,0.3)'">
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
                                <p style="color: #333; font-size: 0.9rem; margin: 0;">Escanea este código para acceder al mega evento</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mt-4">
            <!-- Botones de Acción (Reacción y Compartir) - Mejorados -->
            <div class="d-flex justify-content-end mb-4 flex-wrap" style="gap: 0.75rem;">
                <button class="btn btn-outline-danger d-flex align-items-center" id="btnReaccionarPublico" style="border-radius: 12px; padding: 0.6rem 1.5rem; font-weight: 500; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(220,53,69,0.2);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(220,53,69,0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(220,53,69,0.2)'">
                    <i class="far fa-heart mr-2" id="iconoCorazonPublico" style="transition: all 0.3s ease;"></i>
                    <span id="textoReaccionPublico">Me gusta</span>
                    <span class="badge badge-light ml-2" id="contadorReaccionesPublico">0</span>
                </button>
                <button class="btn btn-outline-warning d-flex align-items-center" id="btnCompartirPublico" style="border-radius: 12px; padding: 0.6rem 1.5rem; font-weight: 500; transition: all 0.3s ease; border-color: #ffc107; color: #856404; box-shadow: 0 2px 8px rgba(255,193,7,0.2);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(255,193,7,0.3)'; this.style.background='#ffc107'; this.style.color='#333'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(255,193,7,0.2)'; this.style.background='transparent'; this.style.color='#856404'">
                    <i class="fas fa-share-alt mr-2"></i> Compartir <span id="contadorCompartidosPublico" class="badge badge-light ml-2" style="font-weight: 600;">0</span>
                </button>
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
                                    <h5 class="mb-0" style="font-weight: 700; color: #2c3e50; font-size: 1.1rem;">
                                        Descripción
                                    </h5>
                                    <p class="mb-0 text-muted" style="font-size: 0.85rem; margin-top: 0.25rem;">
                                        Información detallada del mega evento
                                    </p>
                                </div>
                            </div>
                            <p class="mb-0" style="color: #495057; line-height: 1.8; font-size: 1rem;">{{ $megaEvento->descripcion ?? 'Sin descripción disponible.' }}</p>
                        </div>
                    </div>

                    <!-- Información del Evento -->
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; animation: fadeInUp 0.6s ease-out;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-4">
                                <div class="section-icon mr-3">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                        <div>
                                    <h5 class="mb-0" style="font-weight: 700; color: #2c3e50; font-size: 1.1rem;">
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
                                            <p class="info-value">
                                                @if($megaEvento->fecha_inicio)
                                                    @php
                                                        $fecha = \Carbon\Carbon::parse($megaEvento->fecha_inicio);
                                                        $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
                                                    @endphp
                                                    {{ $fecha->format('d') }} de {{ $meses[$fecha->month - 1] }} de {{ $fecha->year }}, {{ $fecha->format('H:i') }}
                                                @else
                                                    No especificada
                                                @endif
                                            </p>
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
                                            <p class="info-value">
                                                @if($megaEvento->fecha_fin)
                                                    @php
                                                        $fecha = \Carbon\Carbon::parse($megaEvento->fecha_fin);
                                                        $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
                                                    @endphp
                                                    {{ $fecha->format('d') }} de {{ $meses[$fecha->month - 1] }} de {{ $fecha->year }}, {{ $fecha->format('H:i') }}
                                                @else
                                                    No especificada
                                                @endif
                                            </p>
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
                                            <p class="info-value">{{ $megaEvento->capacidad_maxima ? $megaEvento->capacidad_maxima . ' personas' : 'Sin límite' }}</p>
                                        </div>
                                    </div>
                                </div>
                                @if($megaEvento->creador && $megaEvento->creador['nombre'])
                                <div class="col-md-6 mb-4">
                                    <div class="info-item" style="position: relative;">
                                        <div class="info-icon">
                                            <i class="fas fa-building"></i>
                                        </div>
                                        <div class="info-content">
                                            <h6 class="info-label">ONG Organizadora</h6>
                                            <div class="d-flex align-items-center" style="gap: 0.75rem;">
                                                <div style="position: relative; flex-shrink: 0;">
                                                @if($megaEvento->creador['foto_perfil'])
                                                    <img src="{{ $megaEvento->creador['foto_perfil'] }}" alt="{{ $megaEvento->creador['nombre'] }}" 
                                                         class="rounded-circle" 
                                                             style="width: 70px; height: 70px; object-fit: cover; border: 3px solid #ffc107; box-shadow: 0 4px 12px rgba(255, 193, 7, 0.2); display: block;"
                                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                        <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                                             style="width: 70px; height: 70px; font-weight: 700; font-size: 1.5rem; background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); color: white; border: 3px solid #ffc107; box-shadow: 0 4px 12px rgba(255, 193, 7, 0.2); position: absolute; top: 0; left: 0; display: none;">
                                                            {{ strtoupper(substr($megaEvento->creador['nombre'], 0, 1)) }}
                                                        </div>
                                                @else
                                                        <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                                             style="width: 70px; height: 70px; font-weight: 700; font-size: 1.5rem; background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); color: white; border: 3px solid #ffc107; box-shadow: 0 4px 12px rgba(255, 193, 7, 0.2);">
                                                        {{ strtoupper(substr($megaEvento->creador['nombre'], 0, 1)) }}
                                                    </div>
                                                @endif
                                                </div>
                                                <div>
                                                    <div style="color: #495057; font-weight: 600; font-size: 1rem; margin-bottom: 0.25rem;">{{ $megaEvento->creador['nombre'] }}</div>
                                                <span class="badge badge-warning" style="font-size: 0.75rem; padding: 0.25em 0.5em;">{{ $megaEvento->creador['tipo'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Ubicación -->
                    @if($megaEvento->ubicacion || ($megaEvento->lat && $megaEvento->lng))
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; animation: fadeInUp 0.7s ease-out;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-4">
                                <div class="section-icon mr-3">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0" style="font-weight: 700; color: #2c3e50; font-size: 1.1rem;">
                                        Ubicación
                                    </h5>
                                </div>
                            </div>
                            @if($megaEvento->ubicacion)
                            <div class="info-item mb-3" style="background: linear-gradient(135deg, rgba(255, 193, 7, 0.05) 0%, rgba(255, 152, 0, 0.05) 100%); padding: 1.5rem; border-radius: 12px; border-left: 4px solid #ffc107;">
                                <div class="info-icon" style="font-size: 1.5rem; color: #ffc107;">
                                    <i class="fas fa-map-marked-alt"></i>
                                </div>
                                <div class="info-content">
                                    <p class="info-value" style="font-size: 1rem; font-weight: 500;">{{ $megaEvento->ubicacion }}</p>
                                </div>
                            </div>
                            @endif
                            @if($megaEvento->lat && $megaEvento->lng)
                            <div id="mapContainer" class="mt-3" style="height: 350px; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: all 0.3s ease;" onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.12)'" onmouseout="this.style.boxShadow='0 2px 8px rgba(0,0,0,0.08)'"></div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Galería de Imágenes -->
                    @if($megaEvento->imagenes && is_array($megaEvento->imagenes) && count($megaEvento->imagenes) > 0)
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; animation: fadeInUp 0.8s ease-out;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-4">
                                <div class="section-icon mr-3">
                                    <i class="fas fa-images"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0" style="font-weight: 700; color: #2c3e50; font-size: 1.1rem;">
                                        Galería de Imágenes
                                    </h5>
                                    <p class="mb-0 text-muted" style="font-size: 0.85rem; margin-top: 0.25rem;">
                                        Imágenes promocionales del mega evento
                                    </p>
                                </div>
                            </div>
                            <div class="row" id="galeriaImagenes">
                                @foreach($megaEvento->imagenes as $index => $imgUrl)
                                    @if($imgUrl)
                                    @php
                                        // Normalizar URL usando el origen actual
                                        $baseUrl = request()->getSchemeAndHttpHost();
                                        $fullUrl = $imgUrl;
                                        if (strpos($imgUrl, 'http://') !== 0 && strpos($imgUrl, 'https://') !== 0) {
                                            if (strpos($imgUrl, '/storage/') === 0) {
                                                $fullUrl = $baseUrl . $imgUrl;
                                            } elseif (strpos($imgUrl, 'storage/') === 0) {
                                                $fullUrl = $baseUrl . '/storage/' . $imgUrl;
                                            } else {
                                                $fullUrl = $baseUrl . '/storage/' . ltrim($imgUrl, '/');
                                            }
                                        }
                                    @endphp
                                    <div class="col-md-4 mb-3">
                                        <div class="gallery-item" style="border-radius: 16px; overflow: hidden; cursor: pointer; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: all 0.3s ease; height: 240px; background: #f8f9fa;" data-img-url="{{ $fullUrl }}" data-index="{{ $index }}" onclick="mostrarImagenGaleriaPublico('{{ $fullUrl }}')" onmouseover="this.style.transform='translateY(-6px)'; this.style.boxShadow='0 8px 24px rgba(0,0,0,0.15)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.08)'">
                                            <img src="{{ $fullUrl }}"
                                                 alt="Imagen {{ $index + 1 }}"
                                                 style="width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.3s ease;"
                                                 onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%27400%27 height=%27240%27%3E%3Crect fill=%27%23f8f9fa%27 width=%27400%27 height=%27240%27/%3E%3Ctext x=%2750%25%27 y=%2750%25%27 text-anchor=%27middle%27 dy=%27.3em%27 fill=%27%23adb5bd%27 font-family=%27Arial%27 font-size=%2714%27%3EImagen no disponible%3C/text%3E%3C/svg%3E';"
                                                 onmouseover="this.style.transform='scale(1.05)'"
                                                 onmouseout="this.style.transform='scale(1)'">
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Sidebar - Formulario de Participación -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; position: sticky; top: 2rem; animation: fadeInUp 0.5s ease-out;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-4">
                                <div class="section-icon mr-3">
                                    <i class="fas fa-user-plus"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0" style="font-weight: 700; color: #2c3e50; font-size: 1.1rem;">
                                        Participar en este Mega Evento
                            </h5>
                                </div>
                            </div>
                            <!-- Mensaje informativo -->
                            <div class="alert alert-info mb-3" style="border-radius: 10px;">
                                <i class="fas fa-info-circle mr-2"></i>
                                <strong>¿Quieres participar?</strong>
                                <p class="mb-0 mt-2" style="font-size: 0.9rem;">Debes tener una cuenta de Usuario Externo para inscribirte en este mega evento.</p>
                            </div>
                            <!-- Botón de Participar -->
                            <button type="button" class="btn btn-participar w-100" id="btnParticiparPublicoMega" style="background: #ffc107; color: #333;">
                                    <i class="fas fa-check-circle mr-2"></i> Participar
                                </button>
                            <div id="mensajeParticipacion" class="mt-3" style="display: none;"></div>
                        </div>
                    </div>

                    <!-- Información Rápida -->
                    <div class="card border-0 shadow-sm" style="border-radius: 16px; animation: fadeInUp 0.6s ease-out;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="section-icon mr-3">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0" style="font-weight: 700; color: #2c3e50; font-size: 1.1rem;">
                                        Información Rápida
                            </h5>
                                </div>
                            </div>
                            <div class="mb-3 pb-3 border-bottom">
                                <small class="text-muted d-block mb-1">Estado</small>
                                <span class="badge badge-success">{{ ucfirst($megaEvento->estado ?? 'Publicado') }}</span>
                            </div>
                            <div class="mb-3 pb-3 border-bottom">
                                <small class="text-muted d-block mb-1">Categoría</small>
                                <span class="text-dark font-weight-bold">{{ ucfirst($megaEvento->categoria ?? 'N/A') }}</span>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted d-block mb-1">Capacidad</small>
                                <span class="text-dark font-weight-bold">{{ $megaEvento->capacidad_maxima ? $megaEvento->capacidad_maxima . ' personas' : 'Sin límite' }}</span>
                            </div>
                            @if($megaEvento->creador && $megaEvento->creador['nombre'])
                            <div class="mb-3">
                                <small class="text-muted d-block mb-1">Organizador</small>
                                <span class="text-dark font-weight-bold">{{ $megaEvento->creador['nombre'] }}</span>
                            </div>
                            @endif
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
                // Si lucide no carga, se usará Font Awesome automáticamente
            }
        })();
    </script>
    <script>
        const megaEventoId = {{ $megaEventoId }};
        const API_BASE_URL = '{{ url("/") }}';
        const PUBLIC_BASE_URL = typeof getPublicUrl !== 'undefined' 
            ? (window.PUBLIC_BASE_URL || 'http://192.168.0.7:8000')
            : 'http://192.168.0.7:8000';
        
        // Almacenar datos del mega evento para compartir
        window.megaEventoParaCompartir = {
            id: megaEventoId,
            titulo: '{{ addslashes($megaEvento->titulo ?? "Mega Evento") }}',
            url: `${PUBLIC_BASE_URL}/mega-evento/${megaEventoId}/qr`
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
                
                await fetch(`${API_BASE_URL}/api/mega-eventos/${megaEventoId}/compartir-publico`, {
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
                const res = await fetch(`${API_BASE_URL}/api/mega-eventos/${megaEventoId}/compartidos/total`);
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
                const res = await fetch(`${API_BASE_URL}/api/reacciones/mega-evento/${megaEventoId}/total`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    const contador = document.getElementById('contadorReaccionesPublico');
                    if (contador) {
                        contador.textContent = data.total_reacciones || 0;
                    }
                }
            } catch (error) {
                console.error('Error cargando reacciones:', error);
            }
        }
        
        // Toggle reacción (público - con nombres y apellidos del formulario)
        async function toggleReaccionPublico() {
            const token = localStorage.getItem('token');
            
            if (!token) {
                // No está autenticado - redirigir a login
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Inicia sesión',
                        text: 'Debes iniciar sesión para reaccionar a este mega evento.',
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
                const res = await fetch(`${API_BASE_URL}/api/reacciones/mega-evento/${megaEventoId}/toggle`, {
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
                    if (contadorReacciones) {
                        contadorReacciones.textContent = data.total_reacciones || 0;
                    }
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
            const megaEvento = window.megaEventoParaCompartir;
            if (!megaEvento) return;
            
            // Registrar compartido
            await registrarCompartidoPublico('link');
            
            const url = megaEvento.url;
            
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
            const megaEvento = window.megaEventoParaCompartir;
            if (!megaEvento) return;
            
            // Registrar compartido
            await registrarCompartidoPublico('qr');
            
            const qrContainer = document.getElementById('qrContainerPublico');
            const qrcodeDiv = document.getElementById('qrcodePublico');
            
            if (!qrContainer || !qrcodeDiv) return;
            
            const qrUrl = megaEvento.url;
            qrcodeDiv.innerHTML = '';
            qrContainer.style.display = 'block';
            
            qrcodeDiv.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x" style="color: #ffc107;"></i><p class="mt-2" style="color: #333;">Generando QR...</p></div>';
            
            // Intentar cargar la librería QRCode si no está disponible
            if (typeof QRCode === 'undefined') {
                // Verificar si ya se está cargando
                if (document.querySelector('script[src*="qrcode"]')) {
                    // Esperar a que se cargue
                    const checkQRCode = setInterval(() => {
                        if (typeof QRCode !== 'undefined') {
                            clearInterval(checkQRCode);
                            generarQRPublico(qrUrl, qrcodeDiv);
                        }
                    }, 100);
                    // Timeout después de 5 segundos
                    setTimeout(() => {
                        clearInterval(checkQRCode);
                        if (typeof QRCode === 'undefined') {
                            generarQRConAPIPublico(qrUrl, qrcodeDiv);
                        }
                    }, 5000);
                } else {
                    const script = document.createElement('script');
                    script.src = 'https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js';
                    script.onload = function() {
                        if (typeof QRCode !== 'undefined') {
                            generarQRPublico(qrUrl, qrcodeDiv);
                        } else {
                            generarQRConAPIPublico(qrUrl, qrcodeDiv);
                        }
                    };
                    script.onerror = function() {
                        generarQRConAPIPublico(qrUrl, qrcodeDiv);
                    };
                    document.head.appendChild(script);
                }
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
                        dark: '#ffc107',
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
            try {
                const apiUrl = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodeURIComponent(qrUrl)}&bgcolor=FFFFFF&color=ffc107`;
                const img = document.createElement('img');
                img.src = apiUrl;
                img.alt = 'QR Code';
                img.style.cssText = 'display: block; margin: 0 auto; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);';
                img.onerror = function() {
                    qrcodeDiv.innerHTML = `
                        <div class="text-center p-3">
                            <p class="text-danger mb-2" style="font-size: 0.9rem;">Error cargando generador de QR.</p>
                            <p class="text-muted mb-2" style="font-size: 0.85rem;">Por favor, usa el enlace directo:</p>
                            <a href="${qrUrl}" target="_blank" class="btn btn-sm btn-warning">Abrir enlace</a>
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
                        <a href="${qrUrl}" target="_blank" class="btn btn-sm btn-warning">Abrir enlace</a>
                    </div>
                `;
            }
        }
        
        // Función para verificar si ya participa
        async function verificarParticipacion(nombres, apellidos) {
            try {
                const response = await fetch(`${API_BASE_URL}/api/mega-eventos/${megaEventoId}/verificar-participacion-publica`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ nombres, apellidos })
                });

                const data = await response.json();
                return data.success && data.ya_participa;
            } catch (error) {
                console.error('Error verificando participación:', error);
                return false;
            }
        }

        // Verificar participación cuando se ingresan datos
        let verificacionTimeout;
        document.getElementById('nombres').addEventListener('input', function() {
            clearTimeout(verificacionTimeout);
            const nombres = this.value.trim();
            const apellidos = document.getElementById('apellidos').value.trim();
            
            if (nombres && apellidos) {
                verificacionTimeout = setTimeout(async () => {
                    const yaParticipa = await verificarParticipacion(nombres, apellidos);
                    const mensajeYaParticipa = document.getElementById('mensajeYaParticipa');
                    const formParticipar = document.getElementById('formParticipar');
                    
                    if (yaParticipa) {
                        mensajeYaParticipa.style.display = 'block';
                        formParticipar.style.display = 'none';
                    } else {
                        mensajeYaParticipa.style.display = 'none';
                        formParticipar.style.display = 'block';
                    }
                }, 500);
            }
        });

        document.getElementById('apellidos').addEventListener('input', function() {
            clearTimeout(verificacionTimeout);
            const nombres = document.getElementById('nombres').value.trim();
            const apellidos = this.value.trim();
            
            if (nombres && apellidos) {
                verificacionTimeout = setTimeout(async () => {
                    const yaParticipa = await verificarParticipacion(nombres, apellidos);
                    const mensajeYaParticipa = document.getElementById('mensajeYaParticipa');
                    const formParticipar = document.getElementById('formParticipar');
                    
                    if (yaParticipa) {
                        mensajeYaParticipa.style.display = 'block';
                        formParticipar.style.display = 'none';
                    } else {
                        mensajeYaParticipa.style.display = 'none';
                        formParticipar.style.display = 'block';
                    }
                }, 500);
            }
        });

        // Botón de Participar - Redirige a registro o login
        document.getElementById('btnParticiparPublicoMega').addEventListener('click', function() {
            const token = localStorage.getItem('token');
            
            if (!token) {
                // No está autenticado - redirigir a registro con megaEventoId
                const megaEventoId = {{ $megaEventoId }};
                window.location.href = `/register-externo?megaEventoId=${megaEventoId}`;
            } else {
                // Está autenticado - verificar si es usuario externo y si ya está inscrito
                verificarYParticiparMega();
            }
        });
        
        // Verificar si el usuario autenticado ya está participando o inscribirlo
        async function verificarYParticiparMega() {
            const token = localStorage.getItem('token');
            const megaEventoId = {{ $megaEventoId }};
            
            try {
                // Verificar si ya está inscrito (necesitamos una ruta para esto)
                // Por ahora, intentar inscribir directamente
                const resInscribir = await fetch(`${API_BASE_URL}/api/mega-eventos/${megaEventoId}/participar`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                const dataInscribir = await resInscribir.json();

                if (dataInscribir.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Inscripción exitosa!',
                        text: 'Te has inscrito correctamente en el mega evento.',
                        confirmButtonText: 'Aceptar'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    if (dataInscribir.error && dataInscribir.error.includes('Ya estás inscrito')) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Ya estás participando',
                            text: 'Ya estás inscrito en este mega evento.',
                            confirmButtonText: 'Aceptar'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: dataInscribir.error || 'Error al inscribirte en el mega evento'
                        });
                    }
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

        // Helper para construir URL de imagen con IP fija
        const IMAGE_BASE_URL = 'http://192.168.0.7:8000';
        function buildImageUrl(imgUrl) {
            if (!imgUrl || imgUrl.trim() === '') return null;
            // Si ya es una URL completa, retornarla directamente
            if (imgUrl.startsWith('http://') || imgUrl.startsWith('https://')) {
                return imgUrl;
            }
            // Si empieza con /storage/, agregar la IP fija
            if (imgUrl.startsWith('/storage/')) {
                return `${IMAGE_BASE_URL}${imgUrl}`;
            }
            // Si empieza con storage/, agregar /storage/
            if (imgUrl.startsWith('storage/')) {
                return `${IMAGE_BASE_URL}/${imgUrl}`;
            }
            // Por defecto, asumir que es relativa a storage
            return `${IMAGE_BASE_URL}/storage/${imgUrl.replace(/^\//, '')}`;
        }

        // Cargar imágenes de la galería con URLs correctas
        function cargarImagenesGaleria() {
            const galeriaItems = document.querySelectorAll('.gallery-item[data-img-url]');
            console.log('Cargando imágenes de galería, encontradas:', galeriaItems.length);
            
            galeriaItems.forEach((item, index) => {
                const imgUrl = item.getAttribute('data-img-url');
                const img = item.querySelector('img');
                
                if (!img || !imgUrl) {
                    console.warn(`Imagen ${index + 1}: falta img o imgUrl`);
                    return;
                }
                
                console.log(`Procesando imagen ${index + 1}:`, imgUrl);
                const fullUrl = buildImageUrl(imgUrl);
                
                if (fullUrl) {
                    console.log(`URL completa para imagen ${index + 1}:`, fullUrl);
                    img.src = fullUrl;
                    
                    // Agregar evento click para mostrar imagen ampliada
                    item.onclick = () => mostrarImagenGaleriaPublico(fullUrl);
                    
                    // Manejo de errores mejorado
                    img.onerror = function() {
                        console.error(`Error cargando imagen ${index + 1}:`, fullUrl);
                        this.onerror = null;
                        // Intentar con diferentes formatos
                        const altUrls = [
                            imgUrl.startsWith('/storage/') ? `${IMAGE_BASE_URL}${imgUrl}` : null,
                            imgUrl.startsWith('storage/') ? `${IMAGE_BASE_URL}/${imgUrl}` : null,
                            `${IMAGE_BASE_URL}/storage/${imgUrl.replace(/^\//, '')}`,
                            imgUrl // Intentar con la URL original
                        ].filter(url => url && url !== fullUrl);
                        
                        let attemptIndex = 0;
                        const tryNextUrl = () => {
                            if (attemptIndex < altUrls.length) {
                                this.src = altUrls[attemptIndex];
                                attemptIndex++;
                                this.onerror = tryNextUrl;
                            } else {
                                // Si todas fallan, usar placeholder
                                this.src = 'data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%27400%27 height=%27200%27%3E%3Crect fill=%27%23f8f9fa%27 width=%27400%27 height=%27200%27/%3E%3Ctext x=%2750%25%27 y=%2750%25%27 text-anchor=%27middle%27 dy=%27.3em%27 fill=%27%23adb5bd%27 font-family=%27Arial%27 font-size=%2714%27%3EImagen no disponible%3C/text%3E%3C/svg%3E';
                                this.style.objectFit = 'contain';
                                this.style.padding = '20px';
                            }
                        };
                        tryNextUrl();
                    };
                } else {
                    console.warn(`No se pudo construir URL para imagen ${index + 1}`);
                }
            });
        }

        // Actualizar contadores en tiempo real
        let intervaloContadores = null;
        function iniciarActualizacionTiempoReal() {
            // Actualizar cada 5 segundos
            intervaloContadores = setInterval(() => {
                cargarReaccionesPublico();
                cargarContadorCompartidosPublico();
            }, 5000);
        }

        // Inicializar
        function inicializarPagina() {
            // Configurar botones
            const btnReaccionar = document.getElementById('btnReaccionarPublico');
            const btnCompartir = document.getElementById('btnCompartirPublico');
            
            if (btnReaccionar) {
                btnReaccionar.addEventListener('click', toggleReaccionPublico);
            }
            if (btnCompartir) {
                btnCompartir.addEventListener('click', mostrarModalCompartirPublico);
            }
            
            // Cargar imágenes de la galería (con retraso para asegurar que el DOM esté listo)
            setTimeout(() => {
                cargarImagenesGaleria();
            }, 100);
            
            // Cargar reacciones y compartidos
            cargarReaccionesPublico();
            cargarContadorCompartidosPublico();
            
            // Iniciar actualización en tiempo real
            iniciarActualizacionTiempoReal();
        }

        // Ejecutar cuando el DOM esté listo
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', inicializarPagina);
        } else {
            // El DOM ya está listo
            inicializarPagina();
        }

        // Limpiar intervalo al salir de la página
        window.addEventListener('beforeunload', function() {
            if (intervaloContadores) {
                clearInterval(intervaloContadores);
            }
        });
        
        // Cargar imagen del banner
        @if($megaEvento->imagenes && count($megaEvento->imagenes) > 0)
        @php
            $bannerImg = $megaEvento->imagenes[0];
            $bannerUrl = $bannerImg;
            if (strpos($bannerImg, 'http://') !== 0 && strpos($bannerImg, 'https://') !== 0) {
                if (strpos($bannerImg, '/storage/') === 0) {
                    $bannerUrl = 'http://192.168.0.7:8000' . $bannerImg;
                } elseif (strpos($bannerImg, 'storage/') === 0) {
                    $bannerUrl = 'http://192.168.0.7:8000/storage/' . $bannerImg;
                } else {
                    $bannerUrl = 'http://192.168.0.7:8000/storage/' . ltrim($bannerImg, '/');
                }
            }
        @endphp
        const primeraImagen = '{{ $bannerUrl }}';
        if (primeraImagen) {
            document.getElementById('bannerImage').style.backgroundImage = `url(${primeraImagen})`;
        }
        @endif

        // Mapa - Implementación mejorada igual a eventos/detalles
        const mapContainer = document.getElementById('mapContainer');
        if (mapContainer) {
            const inicializarMapa = async () => {
                let lat = {{ $megaEvento->lat ?? 'null' }};
                let lng = {{ $megaEvento->lng ?? 'null' }};
                let direccionCompleta = '{{ addslashes($megaEvento->ubicacion ?? "") }}';

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
                            const map = L.map('mapContainer', {
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
                                        <strong style="color: #2c3e50; font-size: 1rem; display: block; margin-bottom: 0.25rem;">${direccionCompleta || 'Ubicación del mega evento'}</strong>
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
    </script>
</body>
</html>


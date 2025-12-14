@extends('layouts.adminlte')

@section('page_title', 'Mega Eventos')

@section('content_body')
<div class="container-fluid">
    <!-- Botón normal (visible al inicio) - Diseño Minimalista -->
    <div class="d-flex justify-content-between align-items-center mb-4" id="btnNuevoMegaEventoNormal">
        <div>
            <h2 class="mb-0" style="font-weight: 600; color: #0C2B44; font-size: 1.75rem;">Mega Eventos</h2>
            <p class="text-muted mb-0" style="font-size: 0.9rem; margin-top: 0.25rem;">Gestiona tus eventos principales</p>
        </div>
        <a href="{{ route('ong.mega-eventos.create') }}" class="btn" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); color: white; border: none; border-radius: 12px; padding: 0.75rem 1.5rem; font-weight: 500; box-shadow: 0 2px 8px rgba(12, 43, 68, 0.15); transition: all 0.3s ease;">
                <i class="fas fa-plus mr-2"></i> Nuevo Mega Evento
            </a>
    </div>

    <!-- Botón FAB circular (oculto inicialmente) -->
    <a href="{{ route('ong.mega-eventos.create') }}" id="btnNuevoMegaEventoFAB" class="fab-button" style="display: none;">
        <i class="fas fa-plus"></i>
    </a>

    <!-- Panel de Estadísticas Agregadas - Minimalista -->
    <div id="panelEstadisticasAgregadas" class="card mb-4 shadow-sm" style="border: none; border-radius: 16px; display: none; background: white;">
        <div class="card-header bg-white" style="border-bottom: 1px solid #f0f0f0; padding: 1.5rem; border-radius: 16px 16px 0 0;">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0" style="font-weight: 600; color: #0C2B44; font-size: 1.25rem;">
                    <i class="fas fa-chart-bar mr-2" style="color: #00A36C;"></i>Estadísticas Agregadas
                </h5>
                <button class="btn btn-sm" onclick="ocultarEstadisticasAgregadas()" style="background: #f8f9fa; border: 1px solid #e0e0e0; color: #6c757d; border-radius: 8px; padding: 0.5rem 1rem;">
                    <i class="fas fa-times mr-1"></i> Cerrar
                </button>
            </div>
        </div>
        <div class="card-body p-4">
            <!-- Métricas Principales -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card bg-gradient-primary" style="border: none; border-radius: 12px;">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white text-uppercase mb-3" style="font-size: 0.8rem; opacity: .95; font-weight: 600;">Total Mega Eventos</h6>
                                    <h2 class="text-white mb-0" id="totalMegaEventos" style="font-size: 3rem; font-weight: 700;">0</h2>
                                </div>
                                <i class="fas fa-star fa-3x text-white" style="opacity: .2;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card bg-gradient-success" style="border: none; border-radius: 12px;">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white text-uppercase mb-3" style="font-size: 0.8rem; opacity: .95; font-weight: 600;">Total Participantes</h6>
                                    <h2 class="text-white mb-0" id="totalParticipantesAgregado" style="font-size: 3rem; font-weight: 700;">0</h2>
                                </div>
                                <i class="fas fa-users fa-3x text-white" style="opacity: .2;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card" style="border: none; border-radius: 12px; background: linear-gradient(135deg, #e91e63 0%, #c2185b 100%);">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white text-uppercase mb-3" style="font-size: 0.8rem; opacity: .95; font-weight: 600;">Total Reacciones</h6>
                                    <h2 class="text-white mb-0" id="totalReaccionesAgregado" style="font-size: 3rem; font-weight: 700;">0</h2>
                                </div>
                                <i class="fas fa-heart fa-3x text-white" style="opacity: .2;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card" style="border: none; border-radius: 12px; background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%);">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white text-uppercase mb-3" style="font-size: 0.8rem; opacity: .95; font-weight: 600;">Total Compartidos</h6>
                                    <h2 class="text-white mb-0" id="totalCompartidosAgregado" style="font-size: 3rem; font-weight: 700;">0</h2>
                                </div>
                                <i class="fas fa-share-alt fa-3x text-white" style="opacity: .2;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Métricas Secundarias -->
            <div class="row mb-4">
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="card shadow-sm" style="border: none; border-radius: 12px; border-left: 4px solid #00A36C;">
                        <div class="card-body p-4">
                            <h6 class="text-muted text-uppercase mb-2" style="font-size: 0.75rem; font-weight: 600;">Mega Eventos Activos</h6>
                            <h3 class="mb-0" id="megaEventosActivos" style="font-size: 2rem; font-weight: 700; color: #00A36C;">0</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="card shadow-sm" style="border: none; border-radius: 12px; border-left: 4px solid #6c757d;">
                        <div class="card-body p-4">
                            <h6 class="text-muted text-uppercase mb-2" style="font-size: 0.75rem; font-weight: 600;">Mega Eventos Finalizados</h6>
                            <h3 class="mb-0" id="megaEventosFinalizados" style="font-size: 2rem; font-weight: 700; color: #6c757d;">0</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="card shadow-sm" style="border: none; border-radius: 12px; border-left: 4px solid #17a2b8;">
                        <div class="card-body p-4">
                            <h6 class="text-muted text-uppercase mb-2" style="font-size: 0.75rem; font-weight: 600;">Promedio Participantes/Evento</h6>
                            <h3 class="mb-0" id="promedioParticipantes" style="font-size: 2rem; font-weight: 700; color: #17a2b8;">0</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de Detalle por Mega Evento -->
            <div class="card shadow-sm" style="border: none; border-radius: 12px;">
                <div class="card-header bg-white" style="border-bottom: 1px solid #f0f0f0; padding: 1.25rem;">
                    <h6 class="mb-0" style="font-weight: 600; color: #0C2B44;">
                        <i class="fas fa-list mr-2" style="color: #00A36C;"></i>Detalle por Mega Evento
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead style="background: #F5F5F5;">
                                <tr>
                                    <th style="padding: 1rem; font-weight: 600; color: #0C2B44;">Mega Evento</th>
                                    <th style="padding: 1rem; font-weight: 600; color: #0C2B44;">Estado</th>
                                    <th style="padding: 1rem; font-weight: 600; color: #0C2B44;">Participantes</th>
                                    <th style="padding: 1rem; font-weight: 600; color: #0C2B44;">Reacciones</th>
                                    <th style="padding: 1rem; font-weight: 600; color: #0C2B44;">Compartidos</th>
                                    <th style="padding: 1rem; font-weight: 600; color: #0C2B44;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tablaMegaEventosDetalle">
                                <tr>
                                    <td colspan="6" class="text-center py-3">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="sr-only">Cargando...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros y Búsqueda - Diseño Minimalista -->
    <div class="card mb-4 shadow-sm" style="border: none; border-radius: 16px; background: #ffffff;">
        <div class="card-body p-4">
            <div class="row align-items-end">
                <div class="col-md-3 mb-3 mb-md-0">
                    <label for="filtroCategoria" class="form-label text-muted mb-2" style="font-size: 0.875rem; font-weight: 500;">
                        Categoría
                    </label>
                    <select id="filtroCategoria" class="form-control" style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 0.625rem 1rem; font-size: 0.9rem;">
                        <option value="todos">Todas las categorías</option>
                        <option value="cultural">Cultural</option>
                        <option value="deportivo">Deportivo</option>
                        <option value="educativo">Educativo</option>
                        <option value="social">Social</option>
                        <option value="benefico">Benéfico</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3 mb-md-0">
                    <label for="filtroEstado" class="form-label text-muted mb-2" style="font-size: 0.875rem; font-weight: 500;">
                        Estado
                    </label>
                    <select id="filtroEstado" class="form-control" style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 0.625rem 1rem; font-size: 0.9rem;">
                        <option value="todos">Todos los estados</option>
                        <option value="planificacion">Planificación</option>
                        <option value="activo">Activo</option>
                        <option value="en_curso">En Curso</option>
                        <option value="finalizado">Finalizado</option>
                        <option value="cancelado">Cancelado</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <label for="buscador" class="form-label text-muted mb-2" style="font-size: 0.875rem; font-weight: 500;">
                        Buscar
                    </label>
                    <div class="input-group" style="border-radius: 8px; overflow: hidden; border: 1px solid #e0e0e0;">
                        <input type="text" id="buscador" class="form-control border-0" placeholder="Buscar por título o descripción..." style="padding: 0.625rem 1rem; font-size: 0.9rem;">
                        <button class="btn btn-link text-muted border-0" type="button" id="btnLimpiar" style="padding: 0.625rem 1rem;">
                            <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                <div class="col-md-2 mb-3 mb-md-0">
                    <button class="btn btn-primary w-100" type="button" onclick="mostrarEstadisticasAgregadas()" style="border-radius: 8px; padding: 0.625rem 1rem; font-size: 0.9rem; font-weight: 500;">
                        <i class="fas fa-chart-bar mr-2"></i>Estadísticas
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="megaEventosContainer" class="row">
        <div class="col-12 text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Cargando...</span>
            </div>
            <p class="mt-3 text-muted">Cargando mega eventos...</p>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    /* Estilo Minimalista General */
    body {
        background-color: #f8f9fa;
    }
    
    .container-fluid {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem 1.5rem;
    }
    
    /* Botón FAB (Floating Action Button) - Minimalista */
    .fab-button {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 16px rgba(12, 43, 68, 0.25);
        z-index: 1000;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        border: none;
    }

    .fab-button:hover {
        transform: scale(1.05) translateY(-2px);
        box-shadow: 0 6px 20px rgba(12, 43, 68, 0.35);
        color: white;
        text-decoration: none;
    }

    .fab-button i {
        font-size: 1.25rem;
    }

    .fab-button.show {
        opacity: 1;
        visibility: visible;
        transform: scale(1);
    }

    .fab-button.hide {
        opacity: 0;
        visibility: hidden;
        transform: scale(0.8);
    }
    
    /* Cards de Mega Eventos - Minimalista */
    .card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        background: white;
    }
    
    .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    }
    
    /* Inputs y Selects - Minimalista */
    .form-control:focus {
        border-color: #00A36C;
        box-shadow: 0 0 0 0.2rem rgba(0, 163, 108, 0.1);
    }
    
    /* Botones de acción en cards - Separados */
    .btn-group-minimal {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    
    .btn-group-minimal .btn {
        border-radius: 8px;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        font-weight: 500;
        border: 1px solid #e0e0e0;
        transition: all 0.2s ease;
    }
    
    .btn-group-minimal .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    /* Badges - Minimalista */
    .badge {
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        font-weight: 500;
        font-size: 0.75rem;
    }
    
    /* Placeholder de imagen */
    .image-placeholder {
        background: linear-gradient(135deg, #f5f5f5 0%, #e8e8e8 100%);
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .container-fluid {
            padding: 1rem;
        }
        
        .fab-button {
            width: 52px;
            height: 52px;
            bottom: 20px;
            right: 20px;
        }
    }
</style>
@endpush

@section('js')
{{-- Script global para icono de notificaciones --}}
<script src="{{ asset('js/notificaciones-ong.js') }}"></script>
<script src="{{ asset('assets/js/config.js') }}"></script>
<script>
// Función helper para construir URL de imagen (IGUAL QUE EN SHOW.BLADE.PHP)
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
    const storageJsonPattern = /\/storage\/\[(.*?)\]$/;
    const storageJsonMatch = imgUrl.match(storageJsonPattern);
    if (storageJsonMatch) {
        console.warn('buildImageUrl: Detectada URL malformada con /storage/[...], extrayendo JSON:', imgUrl.substring(0, 150));
        try {
            const jsonStr = '[' + storageJsonMatch[1] + ']';
            const parsed = JSON.parse(jsonStr);
            if (Array.isArray(parsed) && parsed.length > 0 && typeof parsed[0] === 'string') {
                imgUrl = parsed[0].trim();
                if (imgUrl === '') return null;
            } else {
                return null;
            }
        } catch (e) {
            return null;
        }
    }
    
    // Si parece ser un array JSON serializado, intentar parsearlo
    if (imgUrl.startsWith('[') && imgUrl.endsWith(']')) {
        try {
            const parsed = JSON.parse(imgUrl);
            if (Array.isArray(parsed) && parsed.length > 0 && typeof parsed[0] === 'string') {
                imgUrl = parsed[0].trim();
                if (imgUrl === '') return null;
            } else {
                return null;
            }
        } catch (e) {
            return null;
        }
    }
    
    // Validación adicional: asegurar que imgUrl no contenga arrays JSON
    if (imgUrl.includes('[') || imgUrl.includes(']')) {
        console.warn('buildImageUrl: URL contiene caracteres de array JSON:', imgUrl);
        return null;
    }
    
    // Filtrar rutas inválidas (solo si NO es URL completa)
    const esUrlCompleta = imgUrl.startsWith('http://') || imgUrl.startsWith('https://');
    const rutasInvalidas = ['/templates/', '/cache/', '/yootheme/', '/resizer/', '/wp-content/', 
                            'templates/', 'cache/', 'yootheme/', 'resizer/', 'wp-content/'];
    
    if (!esUrlCompleta) {
        const esRutaInvalida = rutasInvalidas.some(ruta => imgUrl.toLowerCase().includes(ruta.toLowerCase()));
        if (esRutaInvalida) {
            console.warn('Ruta de imagen inválida filtrada:', imgUrl);
            return null;
        }
    }
    
    // Si ya es una URL completa, reemplazar IPs antiguas y retornarla
    if (esUrlCompleta) {
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
            return null;
        }
    }
    
    // Si empieza con /storage/, verificar si es una ruta externa mal formateada
    if (imgUrl.startsWith('/storage/')) {
        const rutasExternas = ['/storage/resizer/', '/storage/wp-content/', '/storage/templates/', 
                               '/storage/cache/', '/storage/yootheme/'];
        const esRutaExterna = rutasExternas.some(ruta => imgUrl.toLowerCase().startsWith(ruta.toLowerCase()));
        
        if (esRutaExterna) {
            console.warn('buildImageUrl: Ruta externa mal formateada detectada:', imgUrl);
            return null;
        }
        
        const baseUrl = (typeof API_BASE_URL !== 'undefined' && API_BASE_URL) 
            ? API_BASE_URL 
            : window.location.origin;
        const correctedBaseUrl = baseUrl.replace(/127\.0\.0\.1:8000/g, '192.168.0.7:8000')
                                         .replace(/192\.168\.0\.6:8000/g, '192.168.0.7:8000')
                                         .replace(/192.168.0.7:8000/g, '192.168.0.7:8000');
        const finalUrl = `${correctedBaseUrl}${imgUrl}`;
        if (!finalUrl.includes('[') && !finalUrl.includes(']')) {
            return finalUrl;
        } else {
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
        if (!finalUrl.includes('[') && !finalUrl.includes(']')) {
            return finalUrl;
        } else {
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
        if (!finalUrl.includes('[') && !finalUrl.includes(']')) {
            return finalUrl;
        } else {
            return null;
        }
    }
    
    return null;
}

let filtrosMegaEventos = {
    categoria: 'todos',
    estado: 'todos',
    buscar: ''
};

async function cargarMegaEventos() {
    const container = document.getElementById('megaEventosContainer');
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

    container.innerHTML = '<div class="col-12 text-center py-5"><div class="spinner-border text-primary" role="status"><span class="sr-only">Cargando...</span></div><p class="mt-3 text-muted">Cargando mega eventos...</p></div>';

    try {
        // Construir URL con parámetros de filtro
        const params = new URLSearchParams();
        if (filtrosMegaEventos.categoria !== 'todos') {
            params.append('categoria', filtrosMegaEventos.categoria);
        }
        if (filtrosMegaEventos.estado !== 'todos') {
            params.append('estado', filtrosMegaEventos.estado);
        }
        if (filtrosMegaEventos.buscar.trim() !== '') {
            params.append('buscar', filtrosMegaEventos.buscar.trim());
        }

        const url = `${API_BASE_URL}/api/mega-eventos${params.toString() ? '?' + params.toString() : ''}`;

        const res = await fetch(url, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        const data = await res.json();

        if (!res.ok || !data.success) {
            container.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Error: ${data.error || 'Error al cargar mega eventos'}
                    </div>
                </div>
            `;
            return;
        }

        if (!data.mega_eventos || data.mega_eventos.length === 0) {
            container.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle fa-3x mb-3"></i>
                        <h4>No hay mega eventos registrados</h4>
                        <p class="mb-3">Comienza creando tu primer mega evento</p>
                        <a href="{{ route('ong.mega-eventos.create') }}" class="btn btn-success">
                            <i class="fas fa-plus mr-2"></i> Crear Mega Evento
                        </a>
                    </div>
                </div>
            `;
            return;
        }

        container.innerHTML = '';

        // Función helper para formatear fechas desde PostgreSQL sin conversión de zona horaria
        const formatearFechaPostgreSQL = (fechaStr) => {
            if (!fechaStr) return '-';
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
        };

        data.mega_eventos.forEach(mega => {
            const fechaInicio = formatearFechaPostgreSQL(mega.fecha_inicio);
            const fechaFin = formatearFechaPostgreSQL(mega.fecha_fin);

            // Función para procesar una URL de imagen individual
            function procesarImagenUrl(img) {
                // Validar que sea string
                if (typeof img !== 'string') {
                    return null;
                }
                
                // Hacer trim y validar que no esté vacío
                let trimmed = img.trim();
                if (trimmed === '') {
                    return null;
                }
                    
                // Intentar decodificar URI component en bloque try-catch
                    try {
                        const decoded = decodeURIComponent(trimmed);
                        if (decoded !== trimmed) {
                            trimmed = decoded;
                        }
                    } catch (e) {
                        // Si falla la decodificación, usar el original
                    }
                    
                // Normalizar dobles barras
                trimmed = trimmed.replace(/\/\//g, '/');
                
                // Corregir http:/ y https:/ sin doble barra
                trimmed = trimmed.replace(/http:\/(?!\/)/g, 'http://');
                trimmed = trimmed.replace(/https:\/(?!\/)/g, 'https://');
                    
                // Detectar si contiene caracteres de JSON como [ ] %5B %5D
                const tieneCaracteresJson = trimmed.includes('[') || trimmed.includes(']') || 
                                           trimmed.includes('%5B') || trimmed.includes('%5D') ||
                                           trimmed.includes('%22');
                
                if (tieneCaracteresJson) {
                    // Intentar extraer JSON primero buscando patrón /storage/[...]
                    const storageJsonMatch = trimmed.match(/\/storage\/\[(.*?)\]/);
                        if (storageJsonMatch) {
                            try {
                                let jsonStr = '[' + storageJsonMatch[1] + ']';
                            // Decodificar si es necesario
                                try {
                                    jsonStr = decodeURIComponent(jsonStr);
                                } catch (e) {
                                    // Si falla, usar el original
                                }
                                const parsed = JSON.parse(jsonStr);
                            // Si es array, procesar el primer elemento
                            if (Array.isArray(parsed) && parsed.length > 0) {
                                return procesarImagenUrl(parsed[0]);
                            }
                                } catch (e) {
                            // Si falla, continuar con el procesamiento normal
                            }
                        }
                        
                    // Intentar parsear como JSON directamente
                    if (trimmed.startsWith('[') || trimmed.startsWith('{')) {
                        try {
                            const parsed = JSON.parse(trimmed);
                            if (Array.isArray(parsed) && parsed.length > 0) {
                                return procesarImagenUrl(parsed[0]);
                            }
                        } catch (e) {
                            // Si falla, continuar con el procesamiento normal
                        }
                    }
                    }
                    
                // Filtrar rutas inválidas
                    const rutasInvalidas = ['/templates/', '/cache/', '/yootheme/', 'templates/', 'cache/', 'yootheme/', '/resizer/'];
                    const esRutaInvalida = rutasInvalidas.some(ruta => trimmed.toLowerCase().includes(ruta.toLowerCase()));
                    
                if (esRutaInvalida) {
                    return null;
            }
            
                return trimmed;
            }
            
            // Procesar imágenes del backend
            let imagenesValidas = [];
            
            if (mega.imagenes && Array.isArray(mega.imagenes)) {
                imagenesValidas = mega.imagenes
                    .map(img => procesarImagenUrl(img))
                    .filter(img => img !== null && img !== undefined);
            } else if (mega.imagenes && typeof mega.imagenes === 'string') {
                // Si es string, intentar procesarlo
                const procesada = procesarImagenUrl(mega.imagenes);
                if (procesada) {
                    imagenesValidas = [procesada];
                }
            }
            
            // Eliminar duplicados
            imagenesValidas = [...new Set(imagenesValidas)];
            
            const imagenPrincipal = imagenesValidas.length > 0 ? imagenesValidas[0] : null;
            
            // Debug: Log para ver qué imágenes se están procesando
            if (imagenPrincipal) {
                console.log(`[DEBUG] Imagen principal para "${mega.titulo}":`, imagenPrincipal);
            } else if (mega.imagenes && mega.imagenes.length > 0) {
                console.warn(`[DEBUG] No se pudo procesar imágenes para "${mega.titulo}". Imágenes recibidas:`, mega.imagenes);
            }

            const estadoBadge = {
                'planificacion': '<span class="badge badge-secondary">Planificación</span>',
                'activo': '<span class="badge badge-success">Activo</span>',
                'en_curso': '<span class="badge badge-info">En Curso</span>',
                'finalizado': '<span class="badge badge-primary">Finalizado</span>',
                'cancelado': '<span class="badge badge-danger">Cancelado</span>'
            }[mega.estado] || '<span class="badge badge-secondary">' + (mega.estado || 'N/A') + '</span>';

            // Crear el card usando DOM para evitar problemas con comillas
            const colDiv = document.createElement('div');
            colDiv.className = 'col-md-6 col-lg-4 mb-4';
            
            const cardDiv = document.createElement('div');
            cardDiv.className = 'card h-100 shadow-sm';
            cardDiv.style.cssText = 'border-radius: 12px; border: 1px solid #e9ecef; transition: transform 0.2s, box-shadow 0.2s; overflow: hidden;';
            cardDiv.onmouseenter = function() {
                this.style.transform = 'translateY(-4px)';
                this.style.boxShadow = '0 8px 16px rgba(0,0,0,0.15)';
            };
            cardDiv.onmouseleave = function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '0 2px 4px rgba(0,0,0,0.1)';
            };
            
            // Imagen o placeholder - clickable para ir a detalles
            const imgWrapper = document.createElement('div');
            imgWrapper.className = 'position-relative';
            imgWrapper.style.cssText = 'cursor: pointer; overflow: hidden; height: 220px; background: #f8f9fa;';
            imgWrapper.onclick = () => window.location.href = `/ong/mega-eventos/${mega.mega_evento_id}/detalle`;
            
            if (imagenPrincipal) {
                const img = document.createElement('img');
                img.className = 'card-img-top';
                img.style.cssText = 'width: 100%; height: 220px; object-fit: cover; transition: transform 0.3s ease; display: block;';
                img.alt = mega.titulo || 'Mega Evento';
                img.crossOrigin = 'anonymous'; // Permitir CORS
                
                // Crear placeholder que se mostrará si la imagen falla
                const placeholder = document.createElement('div');
                placeholder.className = 'image-placeholder';
                placeholder.style.cssText = 'display: none; width: 100%; height: 220px; align-items: center; justify-content: center; background: #f8f9fa; position: absolute; top: 0; left: 0;';
                placeholder.innerHTML = '<i class="fas fa-image fa-3x text-muted" style="opacity: 0.3;"></i>';
                imgWrapper.appendChild(placeholder);
                
                let errorHandled = false;
                let loadAttempted = false;
                
                // Función para intentar cargar la imagen
                const intentarCargarImagen = (url) => {
                    if (loadAttempted) return;
                    loadAttempted = true;
                    
                    // Verificar primero con fetch si la imagen existe
                    fetch(url, { method: 'HEAD', mode: 'no-cors' })
                        .then(() => {
                            // Si no hay error, establecer src
                            img.src = url;
                        })
                        .catch(() => {
                            // Si falla, intentar de todas formas (puede ser problema de CORS)
                            img.src = url;
                        });
                };
                
                // Configurar handlers antes de establecer src
                img.onerror = function() {
                    if (!errorHandled) {
                        errorHandled = true;
                        console.warn(`[DEBUG] Error cargando imagen para "${mega.titulo}":`, this.src);
                        this.style.display = 'none';
                        placeholder.style.display = 'flex';
                    }
                };
                
                img.onload = function() {
                    placeholder.style.display = 'none';
                    console.log(`[DEBUG] Imagen cargada exitosamente para "${mega.titulo}"`);
                };
                
                // Intentar cargar la imagen
                intentarCargarImagen(imagenPrincipal);
                
                // Agregar atributos adicionales para mejor carga
                img.loading = 'lazy';
                img.decoding = 'async';
                
                imgWrapper.onmouseenter = function() {
                    const imgEl = imgWrapper.querySelector('img');
                    if (imgEl && imgEl.style.display !== 'none') {
                        imgEl.style.transform = 'scale(1.05)';
                    }
                };
                imgWrapper.onmouseleave = function() {
                    const imgEl = imgWrapper.querySelector('img');
                    if (imgEl && imgEl.style.display !== 'none') {
                        imgEl.style.transform = 'scale(1)';
                    }
                };
                
                // Agregar imagen y placeholder al wrapper
                imgWrapper.appendChild(img);
                imgWrapper.appendChild(placeholder);
            } else {
                // Si no hay imagen, mostrar placeholder directamente
                imgWrapper.style.cssText = 'cursor: pointer; height: 220px; background: #f8f9fa; border-bottom: 1px solid #e9ecef; display: flex; align-items: center; justify-content: center;';
                imgWrapper.innerHTML = '<i class="fas fa-image fa-3x text-muted" style="opacity: 0.3;"></i>';
            }
            
            cardDiv.appendChild(imgWrapper);
            
            // Card body mejorado
            const cardBody = document.createElement('div');
            cardBody.className = 'card-body d-flex flex-column p-4';
            
            const titulo = document.createElement('h5');
            titulo.className = 'card-title mb-2';
            titulo.style.cssText = 'font-size: 1.2rem; font-weight: 700; color: #0C2B44;';
            titulo.textContent = mega.titulo || 'Sin título';
            
            const descripcion = document.createElement('p');
            descripcion.className = 'card-text flex-grow-1 mb-3';
            descripcion.style.cssText = 'font-size: 0.9rem; color: #6c757d; line-height: 1.6;';
            descripcion.textContent = mega.descripcion 
                ? (mega.descripcion.length > 100 ? mega.descripcion.substring(0, 100) + '...' : mega.descripcion)
                : 'Sin descripción';
            
            const infoDiv = document.createElement('div');
            infoDiv.className = 'mb-3';
            infoDiv.style.cssText = 'font-size: 0.85rem; color: #6c757d;';
            
            // Formatear fecha de finalización completa
            const fechaFinFormateada = mega.fecha_fin ? formatearFechaPostgreSQL(mega.fecha_fin) : 'No especificada';
            
            // Obtener información del usuario/ONG organizadora
            const ongPrincipal = mega.ong_principal || {};
            const nombreUsuario = ongPrincipal.nombre_usuario || ongPrincipal.nombre_ong || ongPrincipal.nombre || 'Usuario';
            const fotoPerfilOng = ongPrincipal.foto_perfil_url || ongPrincipal.avatar || null;
            const inicialNombre = nombreUsuario.charAt(0).toUpperCase();
            
            infoDiv.innerHTML = `
                <div class="mb-2">
                    <i class="fas fa-calendar-alt mr-2 text-info"></i> <strong>Inicio:</strong> ${fechaInicio}
                </div>
                <div class="mb-2">
                    <i class="fas fa-calendar-check mr-2 text-success"></i> <strong>Finalización:</strong> ${fechaFinFormateada}
                </div>
                ${mega.ubicacion ? `
                    <div class="mb-2">
                        <i class="fas fa-map-marker-alt mr-2 text-danger"></i> ${mega.ubicacion.length > 35 ? mega.ubicacion.substring(0, 35) + '...' : mega.ubicacion}
                    </div>
                ` : ''}
                <div class="mb-2 d-flex align-items-center">
                    <i class="fas fa-user mr-2" style="color: #17a2b8;"></i>
                    <div class="d-flex align-items-center">
                        ${fotoPerfilOng ? `
                            <img src="${fotoPerfilOng}" 
                                 alt="${nombreUsuario}" 
                                 class="rounded-circle mr-2" 
                                 style="width: 24px; height: 24px; object-fit: cover; border: 1px solid #e9ecef;"
                                 onerror="this.style.display='none';">
                        ` : ''}
                        <span style="font-weight: 500; color: #0C2B44;">${nombreUsuario}</span>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    ${estadoBadge}
                    ${mega.es_publico ? '<span class="badge badge-info">Público</span>' : '<span class="badge badge-secondary">Privado</span>'}
                </div>
            `;
            
            const btnGroup = document.createElement('div');
            btnGroup.className = 'mt-auto pt-3 border-top';
            btnGroup.style.cssText = 'border-top: 1px solid #f0f0f0 !important;';
            btnGroup.innerHTML = `
                <div class="btn-group-minimal">
                    <a href="/ong/mega-eventos/${mega.mega_evento_id}/detalle" 
                       class="btn btn-sm btn-primary" title="Ver detalles" style="background: #0C2B44; border-color: #0C2B44; color: white; border-radius: 8px; padding: 0.5rem 1rem; font-weight: 500;">
                        <i class="fas fa-eye mr-1"></i> Detalles
                    </a>
                    <a href="/ong/mega-eventos/${mega.mega_evento_id}/seguimiento" 
                       class="btn btn-sm btn-info" title="Seguimiento" style="background: #00A36C; border-color: #00A36C; color: white; border-radius: 8px; padding: 0.5rem 1rem; font-weight: 500;">
                        <i class="fas fa-chart-line mr-1"></i> Seguimiento
                    </a>
                    <a href="/ong/mega-eventos/${mega.mega_evento_id}/editar" 
                       class="btn btn-sm btn-warning" title="Editar" style="background: #ffc107; border-color: #ffc107; color: #0C2B44; border-radius: 8px; padding: 0.5rem 0.75rem; font-weight: 500;">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button onclick="eliminarMegaEvento(${mega.mega_evento_id})" 
                            class="btn btn-sm btn-danger" title="Eliminar" style="background: #dc3545; border-color: #dc3545; color: white; border-radius: 8px; padding: 0.5rem 0.75rem; font-weight: 500;">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
            
            cardBody.appendChild(titulo);
            cardBody.appendChild(descripcion);
            cardBody.appendChild(infoDiv);
            cardBody.appendChild(btnGroup);
            
            cardDiv.appendChild(cardBody);
            colDiv.appendChild(cardDiv);
            container.appendChild(colDiv);
        });

    } catch (error) {
        console.error('Error:', error);
        container.innerHTML = `
            <div class="col-12">
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Error de conexión al cargar mega eventos.
                </div>
            </div>
        `;
    }
}

document.addEventListener('DOMContentLoaded', async () => {
    // Cargar mega eventos iniciales
    await cargarMegaEventos();

    // Event listeners para filtros
    document.getElementById('filtroCategoria').addEventListener('change', function() {
        filtrosMegaEventos.categoria = this.value;
        cargarMegaEventos();
    });

    document.getElementById('filtroEstado').addEventListener('change', function() {
        filtrosMegaEventos.estado = this.value;
        cargarMegaEventos();
    });

    // Búsqueda con debounce
    let searchTimeout;
    document.getElementById('buscador').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            filtrosMegaEventos.buscar = this.value;
            cargarMegaEventos();
        }, 500);
    });

    // Botón limpiar
    document.getElementById('btnLimpiar').addEventListener('click', function() {
        document.getElementById('buscador').value = '';
        document.getElementById('filtroCategoria').value = 'todos';
        document.getElementById('filtroEstado').value = 'todos';
        filtrosMegaEventos = {
            categoria: 'todos',
            estado: 'todos',
            buscar: ''
        };
        cargarMegaEventos();
    });

    // Control del FAB (Floating Action Button)
    const btnNormal = document.getElementById('btnNuevoMegaEventoNormal');
    const btnFAB = document.getElementById('btnNuevoMegaEventoFAB');
    let lastScrollTop = 0;

    window.addEventListener('scroll', function() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        if (scrollTop > 200) {
            // Ocultar botón normal y mostrar FAB
            if (btnNormal) btnNormal.style.display = 'none';
            if (btnFAB) {
                btnFAB.style.display = 'flex';
                btnFAB.classList.add('show');
                btnFAB.classList.remove('hide');
            }
        } else {
            // Mostrar botón normal y ocultar FAB
            if (btnNormal) btnNormal.style.display = 'flex';
            if (btnFAB) {
                btnFAB.classList.add('hide');
                btnFAB.classList.remove('show');
                setTimeout(() => {
                    if (btnFAB.classList.contains('hide')) {
                        btnFAB.style.display = 'none';
                    }
                }, 300);
            }
        }
        
        lastScrollTop = scrollTop;
    });
});

async function eliminarMegaEvento(id) {
    const result = await Swal.fire({
        title: '¿Eliminar Mega Evento?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });

    if (!result.isConfirmed) return;

    const token = localStorage.getItem('token');

    try {
        const res = await fetch(`${API_BASE_URL}/api/mega-eventos/${id}`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        const data = await res.json();

        if (!res.ok || !data.success) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.error || 'Error al eliminar el mega evento'
            });
            return;
        }

        Swal.fire({
            icon: 'success',
            title: '¡Eliminado!',
            text: 'El mega evento ha sido eliminado correctamente',
            timer: 1500,
            timerProgressBar: true
        }).then(() => {
            location.reload();
        });

    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error de Conexión',
            text: 'No se pudo conectar con el servidor'
        });
    }
}
</script>
@endsection


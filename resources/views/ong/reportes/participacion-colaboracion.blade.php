@extends('layouts.adminlte')

@section('page_title', 'Reporte de Participación y Colaboración - ONG')

@section('content_body')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom" style="border-bottom: 1px solid #e9ecef; padding: 1.5rem;">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <h3 class="mb-0" style="color: #0C2B44; font-weight: 700; font-size: 1.75rem;">
                            <i class="fas fa-users mr-2" style="color: #00A36C;"></i>Participación y Colaboración
                        </h3>
                        <div class="d-flex mt-2 mt-md-0 flex-wrap">
                            <a href="{{ route('ong.reportes.participacion-colaboracion.exportar.pdf') }}" class="btn btn-sm mr-2 mb-2" style="background: #dc3545; color: white; border-radius: 8px; padding: 0.6rem 1.5rem; font-weight: 600;">
                                <i class="fas fa-file-pdf mr-2"></i>Exportar PDF
                            </a>
                            <a href="{{ route('ong.reportes.index') }}" class="btn btn-sm mb-2" style="background: #6c757d; color: white; border-radius: 8px; padding: 0.6rem 1.5rem; font-weight: 600;">
                                <i class="fas fa-arrow-left mr-2"></i>Volver a Reportes
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom" style="border-bottom: 1px solid #e9ecef; padding: 1.5rem;">
                    <h5 class="mb-0" style="color: #0C2B44; font-weight: 600;">
                        <i class="fas fa-filter mr-2" style="color: #00A36C;"></i>Filtros
                    </h5>
                </div>
                <div class="card-body" style="padding: 1.5rem;">
                    <form id="filtrosForm" method="GET" action="{{ route('ong.reportes.participacion-colaboracion') }}">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="fecha_inicio" style="font-weight: 600; color: #333; margin-bottom: 0.5rem;">Fecha Inicio</label>
                                @php
                                    $fechaInicioDefault = request('fecha_inicio') ?: \Carbon\Carbon::now()->subYear()->format('Y-m-d');
                                @endphp
                                <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" 
                                       value="{{ $filtros['fecha_inicio'] ?? $fechaInicioDefault }}" 
                                       style="border-radius: 8px; border: 1px solid #dee2e6;">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="fecha_fin" style="font-weight: 600; color: #333; margin-bottom: 0.5rem;">Fecha Fin</label>
                                @php
                                    $fechaFinDefault = request('fecha_fin') ?: \Carbon\Carbon::now()->format('Y-m-d');
                                @endphp
                                <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" 
                                       value="{{ $filtros['fecha_fin'] ?? $fechaFinDefault }}" 
                                       style="border-radius: 8px; border: 1px solid #dee2e6;">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="categoria" style="font-weight: 600; color: #333; margin-bottom: 0.5rem;">Categoría</label>
                                <select name="categoria" id="categoria" class="form-control" style="border-radius: 8px; border: 1px solid #dee2e6;">
                                    <option value="">Todas</option>
                                    <option value="social" {{ ($filtros['categoria'] ?? '') === 'social' ? 'selected' : '' }}>Social</option>
                                    <option value="educativo" {{ ($filtros['categoria'] ?? '') === 'educativo' ? 'selected' : '' }}>Educativo</option>
                                    <option value="ambiental" {{ ($filtros['categoria'] ?? '') === 'ambiental' ? 'selected' : '' }}>Ambiental</option>
                                    <option value="salud" {{ ($filtros['categoria'] ?? '') === 'salud' ? 'selected' : '' }}>Salud</option>
                                    <option value="cultural" {{ ($filtros['categoria'] ?? '') === 'cultural' ? 'selected' : '' }}>Cultural</option>
                                    <option value="deportivo" {{ ($filtros['categoria'] ?? '') === 'deportivo' ? 'selected' : '' }}>Deportivo</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3 d-flex align-items-end">
                                <div class="w-100 d-flex gap-2">
                                    <button type="submit" class="btn btn-block" style="background: #0C2B44; color: white; border-radius: 8px; padding: 0.6rem; font-weight: 600;">
                                        <i class="fas fa-search mr-2"></i>Filtrar
                                    </button>
                                    <a href="{{ route('ong.reportes.participacion-colaboracion') }}" class="btn btn-block" style="background: #6c757d; color: white; border-radius: 8px; padding: 0.6rem; font-weight: 600;">
                                        <i class="fas fa-redo mr-2"></i>Limpiar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido -->
    <div class="row">
        <!-- Top Empresas Patrocinadoras -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom" style="padding: 1rem 1.5rem;">
                    <h5 class="mb-0" style="color: #0C2B44; font-weight: 600;">
                        <i class="fas fa-building mr-2" style="color: #00A36C;"></i>Top Empresas Patrocinadoras
                    </h5>
                </div>
                <div class="card-body" style="padding: 1.5rem;">
                    <div id="contenidoTopEmpresas">
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                            <p>Cargando datos...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Voluntarios -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom" style="padding: 1rem 1.5rem;">
                    <h5 class="mb-0" style="color: #0C2B44; font-weight: 600;">
                        <i class="fas fa-users mr-2" style="color: #00A36C;"></i>Top Voluntarios Más Activos
                    </h5>
                </div>
                <div class="card-body" style="padding: 1.5rem;">
                    <div id="contenidoTopVoluntarios">
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                            <p>Cargando datos...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Eventos con Más Colaboradores -->
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom" style="padding: 1rem 1.5rem;">
                    <h5 class="mb-0" style="color: #0C2B44; font-weight: 600;">
                        <i class="fas fa-calendar-check mr-2" style="color: #00A36C;"></i>Eventos con Más Colaboradores
                    </h5>
                </div>
                <div class="card-body" style="padding: 1.5rem;">
                    <div id="contenidoEventosColaboracion">
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                            <p>Cargando datos...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    .card {
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.12);
    }

    .btn {
        border-radius: 8px;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
        margin-right: 0 !important;
        margin-left: 0 !important;
        margin-bottom: 0.5rem;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    .d-flex.gap-2 {
        gap: 12px !important;
    }
    
    /* Espaciado específico para botones del header */
    .card-header .d-flex > a,
    .card-header .d-flex > button {
        margin-right: 0.75rem;
        margin-left: 0;
    }
    
    .card-header .d-flex > a:last-child,
    .card-header .d-flex > button:last-child {
        margin-right: 0;
    }

    input.form-control, select.form-control {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        transition: all 0.3s ease;
    }

    input.form-control:focus, select.form-control:focus {
        border-color: #00A36C;
        box-shadow: 0 0 0 0.2rem rgba(0,163,108,0.25);
    }

    .table {
        border-radius: 8px;
        overflow: hidden;
    }

    .table thead th {
        background: #0C2B44;
        color: white;
        font-weight: 600;
        border: none;
        padding: 1rem;
    }

    .table tbody td {
        padding: 0.75rem 1rem;
        vertical-align: middle;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .badge {
        border-radius: 20px;
        padding: 0.4em 0.8em;
        font-size: 0.85rem;
        font-weight: 500;
    }
</style>
@endpush

@push('js')
<script src="{{ asset('assets/js/config.js') }}"></script>
<script>
// Definir API_BASE_URL si no está definido
if (typeof API_BASE_URL === 'undefined') {
    window.API_BASE_URL = "{{ env('APP_URL', 'http://localhost:8000') }}";
    var API_BASE_URL = window.API_BASE_URL;
    console.log("🌐 API_BASE_URL definido:", API_BASE_URL);
}

let token = localStorage.getItem('token');

document.addEventListener('DOMContentLoaded', async function() {
    await cargarDatos();
});

async function cargarDatos() {
    try {
        if (!token) {
            console.error('No hay token de autenticación');
            return;
        }

        // Construir URL con filtros - usar valores por defecto si no hay filtros
        const urlParams = new URLSearchParams(window.location.search);
        const params = new URLSearchParams();
        
        const fechaInicioInput = document.getElementById('fecha_inicio');
        const fechaFinInput = document.getElementById('fecha_fin');
        
        // Usar valor del formulario si existe, sino el de URL, sino valor por defecto (último año)
        const fechaInicio = fechaInicioInput?.value || urlParams.get('fecha_inicio') || new Date(Date.now() - 365*24*60*60*1000).toISOString().split('T')[0];
        const fechaFin = fechaFinInput?.value || urlParams.get('fecha_fin') || new Date().toISOString().split('T')[0];
        
        params.append('fecha_inicio', fechaInicio);
        params.append('fecha_fin', fechaFin);
        
        if (urlParams.get('categoria')) params.append('categoria', urlParams.get('categoria'));

        // La ruta API está en /api/reportes-ong/participacion-colaboracion
        const res = await fetch(`${API_BASE_URL}/api/reportes-ong/participacion-colaboracion?${params.toString()}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if (!res.ok) {
            const errorText = await res.text();
            console.error('Error en respuesta:', res.status, errorText);
            throw new Error(`Error ${res.status}: ${res.statusText}`);
        }

        const response = await res.json();
        
        if (!response.success) {
            throw new Error(response.error || 'Error al obtener datos');
        }

        const datos = response.datos || response || {
            top_empresas: [],
            top_voluntarios: [],
            eventos_colaboracion: []
        };

        console.log('📊 Datos recibidos:', {
            empresas: datos.top_empresas?.length || 0,
            voluntarios: datos.top_voluntarios?.length || 0,
            eventos: datos.eventos_colaboracion?.length || 0,
            datos_completos: datos
        });

        // Renderizar Top Empresas
        renderizarTopEmpresas(datos.top_empresas || []);
        
        // Renderizar Top Voluntarios
        renderizarTopVoluntarios(datos.top_voluntarios || []);
        
        // Renderizar Eventos con Más Colaboradores
        renderizarEventosColaboracion(datos.eventos_colaboracion || []);

    } catch (error) {
        console.error('Error cargando datos:', error);
        document.getElementById('contenidoTopEmpresas').innerHTML = 
            '<div class="alert alert-danger">Error al cargar los datos: ' + error.message + '</div>';
        document.getElementById('contenidoTopVoluntarios').innerHTML = 
            '<div class="alert alert-danger">Error al cargar los datos: ' + error.message + '</div>';
        document.getElementById('contenidoEventosColaboracion').innerHTML = 
            '<div class="alert alert-danger">Error al cargar los datos: ' + error.message + '</div>';
    }
}

function renderizarTopEmpresas(empresas) {
    const contenedor = document.getElementById('contenidoTopEmpresas');
    
    if (!empresas || empresas.length === 0) {
        contenedor.innerHTML = '<p class="text-muted text-center py-4">No hay empresas patrocinadoras registradas</p>';
        return;
    }

    let html = '<div class="table-responsive"><table class="table table-hover"><thead><tr><th style="width: 50px;">#</th><th>Empresa</th><th class="text-center">Eventos Patrocinados</th><th class="text-center">Monto Total</th></tr></thead><tbody>';
    
    empresas.forEach((empresa, index) => {
        html += `
            <tr>
                <td class="text-center">${index + 1}</td>
                <td><strong>${empresa.nombre || empresa.nombre_completo || 'N/A'}</strong></td>
                <td class="text-center"><span class="badge" style="background: #17a2b8; color: white; border-radius: 20px; padding: 0.4em 0.8em;">${empresa.eventos_count || empresa.total_eventos || 0}</span></td>
                <td class="text-center">${empresa.monto_total ? '$' + parseFloat(empresa.monto_total).toLocaleString() : 'N/A'}</td>
            </tr>
        `;
    });
    
    html += '</tbody></table></div>';
    contenedor.innerHTML = html;
}

function renderizarTopVoluntarios(voluntarios) {
    const contenedor = document.getElementById('contenidoTopVoluntarios');
    
    if (!voluntarios || voluntarios.length === 0) {
        contenedor.innerHTML = '<p class="text-muted text-center py-4">No hay voluntarios registrados</p>';
        return;
    }

    let html = '<div class="table-responsive"><table class="table table-hover"><thead><tr><th style="width: 50px;">#</th><th>Voluntario</th><th class="text-center">Eventos Participados</th><th class="text-center">Horas Contribuidas</th></tr></thead><tbody>';
    
    voluntarios.forEach((voluntario, index) => {
        html += `
            <tr>
                <td class="text-center">${index + 1}</td>
                <td><strong>${voluntario.nombre || voluntario.nombre_completo || 'N/A'}</strong></td>
                <td class="text-center"><span class="badge" style="background: #00A36C; color: white; border-radius: 20px; padding: 0.4em 0.8em;">${voluntario.eventos_count || voluntario.total_eventos || 0}</span></td>
                <td class="text-center">${voluntario.horas_contribuidas || (voluntario.participaciones_count ? voluntario.participaciones_count * 2 : 0)} hrs</td>
            </tr>
        `;
    });
    
    html += '</tbody></table></div>';
    contenedor.innerHTML = html;
}

function renderizarEventosColaboracion(eventos) {
    const contenedor = document.getElementById('contenidoEventosColaboracion');
    
    if (!eventos || eventos.length === 0) {
        contenedor.innerHTML = '<p class="text-muted text-center py-4">No hay eventos con colaboradores registrados</p>';
        return;
    }

    let html = '<div class="table-responsive"><table class="table table-hover"><thead><tr><th style="width: 50px;">#</th><th>Evento</th><th class="text-center">Fecha</th><th class="text-center">Voluntarios</th><th class="text-center">Empresas</th><th class="text-center">Total Colaboradores</th></tr></thead><tbody>';
    
    eventos.forEach((evento, index) => {
        const voluntarios = evento.voluntarios_count || evento.total_participantes || 0;
        const empresas = evento.empresas_count || evento.total_patrocinadores || 0;
        const total = evento.total_colaboradores || (voluntarios + empresas);
        
        html += `
            <tr>
                <td class="text-center">${index + 1}</td>
                <td><strong>${evento.titulo || 'N/A'}</strong></td>
                <td class="text-center">${evento.fecha_inicio || evento.fecha_creacion || 'N/A'}</td>
                <td class="text-center"><span class="badge" style="background: #00A36C; color: white; border-radius: 20px; padding: 0.4em 0.8em;">${voluntarios}</span></td>
                <td class="text-center"><span class="badge" style="background: #17a2b8; color: white; border-radius: 20px; padding: 0.4em 0.8em;">${empresas}</span></td>
                <td class="text-center"><span class="badge" style="background: #0C2B44; color: white; border-radius: 20px; padding: 0.4em 0.8em;">${total}</span></td>
            </tr>
        `;
    });
    
    html += '</tbody></table></div>';
    contenedor.innerHTML = html;
}
</script>
@endpush

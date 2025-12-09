@extends('layouts.adminlte-empresa')

@section('page_title', 'Reportes y Estadísticas')

@section('content_body')
<div class="container-fluid">
    <!-- Panel de Bienvenida -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); border: none; border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h2 class="text-white mb-0" style="font-weight: 700; font-size: 1.8rem;">
                                <i class="far fa-chart-bar mr-2"></i>
                                Reportes y Estadísticas Completas
                            </h2>
                            <p class="text-white mb-0 mt-2" style="opacity: 0.95; font-size: 1rem;">
                                Visualiza el impacto de tus patrocinios y colaboraciones
                            </p>
                        </div>
                        <div class="mt-3 mt-md-0">
                            <button class="btn btn-light mr-2" id="btnDescargarPDF" style="border-radius: 8px; background: #FFFFFF; color: #0C2B44; border: 1px solid #0C2B44;">
                                <i class="far fa-file-pdf mr-2"></i>Descargar PDF
                            </button>
                            <button class="btn btn-light" id="btnDescargarExcel" style="border-radius: 8px; background: #FFFFFF; color: #0C2B44; border: 1px solid #0C2B44;">
                                <i class="far fa-file-excel mr-2"></i>Descargar Excel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas principales -->
    <div class="row mb-4">
        <div class="col-lg-3 col-6 mb-3">
            <div class="card shadow-sm h-100" style="border-radius: 16px; border: 1px solid #e9ecef; background: linear-gradient(135deg, #0C2B44 0%, #1a4a6b 100%); overflow: hidden;">
                <div class="card-body p-4 position-relative" style="color: white;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-2" style="font-size: 0.875rem; font-weight: 500; opacity: 0.9; text-transform: uppercase; letter-spacing: 0.5px;">Total Eventos</h6>
                            <h2 class="mb-0" id="totalEventos" style="font-weight: 700; font-size: 2rem;">0</h2>
                        </div>
                        <div style="width: 56px; height: 56px; background: rgba(255, 255, 255, 0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px);">
                            <i class="fas fa-calendar-check" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6 mb-3">
            <div class="card shadow-sm h-100" style="border-radius: 16px; border: 1px solid #e9ecef; background: linear-gradient(135deg, #00A36C 0%, #059669 100%); overflow: hidden;">
                <div class="card-body p-4 position-relative" style="color: white;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-2" style="font-size: 0.875rem; font-weight: 500; opacity: 0.9; text-transform: uppercase; letter-spacing: 0.5px;">Eventos Activos</h6>
                            <h2 class="mb-0" id="eventosActivos" style="font-weight: 700; font-size: 2rem;">0</h2>
                        </div>
                        <div style="width: 56px; height: 56px; background: rgba(255, 255, 255, 0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px);">
                            <i class="fas fa-play-circle" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6 mb-3">
            <div class="card shadow-sm h-100" style="border-radius: 16px; border: 1px solid #e9ecef; background: linear-gradient(135deg, #1a4a6b 0%, #0C2B44 100%); overflow: hidden;">
                <div class="card-body p-4 position-relative" style="color: white;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-2" style="font-size: 0.875rem; font-weight: 500; opacity: 0.9; text-transform: uppercase; letter-spacing: 0.5px;">Eventos Finalizados</h6>
                            <h2 class="mb-0" id="eventosFinalizados" style="font-weight: 700; font-size: 2rem;">0</h2>
                        </div>
                        <div style="width: 56px; height: 56px; background: rgba(255, 255, 255, 0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px);">
                            <i class="fas fa-check-circle" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6 mb-3">
            <div class="card shadow-sm h-100" style="border-radius: 16px; border: 1px solid #e9ecef; background: linear-gradient(135deg, #059669 0%, #00A36C 100%); overflow: hidden;">
                <div class="card-body p-4 position-relative" style="color: white;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-2" style="font-size: 0.875rem; font-weight: 500; opacity: 0.9; text-transform: uppercase; letter-spacing: 0.5px;">ONGs Beneficiadas</h6>
                            <h2 class="mb-0" id="ongsBeneficiadas" style="font-weight: 700; font-size: 2rem;">0</h2>
                        </div>
                        <div style="width: 56px; height: 56px; background: rgba(255, 255, 255, 0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px);">
                            <i class="fas fa-hands-helping" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>

    <!-- Estadísticas secundarias -->
    <div class="row mb-4">
        <div class="col-lg-4 col-6 mb-3">
            <div class="card shadow-sm h-100" style="border-radius: 16px; border: 1px solid #e9ecef; background: #ffffff;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2" style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Patrocinios</h6>
                            <h3 class="mb-0" id="totalPatrocinios" style="color: #0C2B44; font-weight: 700; font-size: 1.75rem;">0</h3>
    </div>
                        <div style="width: 56px; height: 56px; background: linear-gradient(135deg, rgba(12, 43, 68, 0.1) 0%, rgba(12, 43, 68, 0.15) 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-hand-holding-heart" style="font-size: 1.5rem; color: #0C2B44;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-6 mb-3">
            <div class="card shadow-sm h-100" style="border-radius: 16px; border: 1px solid #e9ecef; background: #ffffff;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2" style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Colaboraciones</h6>
                            <h3 class="mb-0" id="totalColaboraciones" style="color: #00A36C; font-weight: 700; font-size: 1.75rem;">0</h3>
                        </div>
                        <div style="width: 56px; height: 56px; background: linear-gradient(135deg, rgba(0, 163, 108, 0.1) 0%, rgba(0, 163, 108, 0.15) 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-users" style="font-size: 1.5rem; color: #00A36C;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-6 mb-3">
            <div class="card shadow-sm h-100" style="border-radius: 16px; border: 1px solid #e9ecef; background: #ffffff;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2" style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Eventos Próximos</h6>
                            <h3 class="mb-0" id="eventosProximos" style="color: #0C2B44; font-weight: 700; font-size: 1.75rem;">0</h3>
                        </div>
                        <div style="width: 56px; height: 56px; background: linear-gradient(135deg, rgba(12, 43, 68, 0.1) 0%, rgba(12, 43, 68, 0.15) 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-calendar-alt" style="font-size: 1.5rem; color: #0C2B44;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
        
    <!-- Gráficos Minimalistas -->
    <div class="row mb-4" id="seccionGraficos">
        <!-- Gráfico de líneas con área (estilo imagen) -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm" style="border-radius: 16px; border: 1px solid #e9ecef; background: #ffffff;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                            <i class="fas fa-chart-line text-white" style="font-size: 1.1rem;"></i>
                    </div>
                        <div>
                            <h5 class="mb-0" style="font-weight: 700; color: #0C2B44; font-size: 1.1rem;">Eventos por Mes</h5>
                            <small class="text-muted">Tendencia de participación</small>
                </div>
            </div>
                    <canvas id="graficoMensual" width="378" height="378" style="width: 10cm; height: 10cm; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>

        <!-- Gráfico de barras verticales (estilo imagen) -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm" style="border-radius: 16px; border: 1px solid #e9ecef; background: #ffffff;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                            <i class="fas fa-chart-bar text-white" style="font-size: 1.1rem;"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="font-weight: 700; color: #0C2B44; font-size: 1.1rem;">Eventos por Tipo</h5>
                            <small class="text-muted">Distribución por categoría</small>
                        </div>
                    </div>
                    <canvas id="graficoTipos" width="378" height="378" style="width: 10cm; height: 10cm; max-width: 100%;"></canvas>
        </div>
    </div>
</div>

        <!-- Sección de Colaboraciones -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm" style="border-radius: 16px; border: 1px solid #e9ecef; background: #ffffff;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                            <i class="fas fa-users text-white" style="font-size: 1.1rem;"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="font-weight: 700; color: #0C2B44; font-size: 1.1rem;">Colaboraciones</h5>
                            <small class="text-muted">Análisis de participación</small>
                        </div>
                    </div>
                    <!-- Gráfico de barras de colaboraciones -->
                    <canvas id="graficoColaboraciones" width="378" height="378" style="width: 10cm; height: 10cm; max-width: 100%; margin-bottom: 20px;"></canvas>
                    <!-- Pie chart de colaboraciones por categoría -->
                    <canvas id="graficoColaboracionesPie" width="378" height="378" style="width: 10cm; height: 10cm; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfico de eventos por estado (pie chart) -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm" style="border-radius: 16px; border: 1px solid #e9ecef; background: #ffffff;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                            <i class="fas fa-chart-pie text-white" style="font-size: 1.1rem;"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="font-weight: 700; color: #0C2B44; font-size: 1.1rem;">Eventos por Estado</h5>
                            <small class="text-muted">Distribución actual</small>
                        </div>
                    </div>
                    <canvas id="graficoEstados" width="378" height="378" style="width: 10cm; height: 10cm; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Eventos Próximos -->
    <div class="row mb-4" id="seccionEventosProximos">
        <div class="col-12">
            <div class="card shadow-sm" style="border-radius: 16px; border: 1px solid #e9ecef; background: #ffffff;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                            <i class="fas fa-calendar-alt text-white" style="font-size: 1.1rem;"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="font-weight: 700; color: #0C2B44; font-size: 1.1rem;">Eventos Próximos</h5>
                            <small class="text-muted">Próximas actividades programadas</small>
                        </div>
                    </div>
                    <div id="listaEventosProximos" class="list-group">
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-spinner fa-spin"></i> Cargando eventos próximos...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de eventos -->
    <div class="card shadow-sm" style="border-radius: 16px; border: 1px solid #e9ecef; background: #ffffff;">
        <div class="card-body p-4">
            <div class="d-flex align-items-center mb-4">
                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                    <i class="fas fa-table text-white" style="font-size: 1.1rem;"></i>
                </div>
                <div>
                    <h5 class="mb-0" style="font-weight: 700; color: #0C2B44; font-size: 1.1rem;">Eventos Detallados</h5>
                    <small class="text-muted">Lista completa de participación</small>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="tablaEventos" style="border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr style="background: #f8f9fa; border-radius: 8px;">
                            <th style="font-weight: 700; color: #0C2B44; padding: 12px; border: none; border-top-left-radius: 8px;">
                                <i class="fas fa-calendar-check mr-2"></i>Evento
                            </th>
                            <th style="font-weight: 700; color: #0C2B44; padding: 12px; border: none;">
                                <i class="fas fa-hands-helping mr-2"></i>ONG Organizadora
                            </th>
                            <th style="font-weight: 700; color: #0C2B44; padding: 12px; border: none;">
                                <i class="fas fa-tag mr-2"></i>Tipo
                            </th>
                            <th style="font-weight: 700; color: #0C2B44; padding: 12px; border: none;">
                                <i class="fas fa-link mr-2"></i>Relación
                            </th>
                            <th style="font-weight: 700; color: #0C2B44; padding: 12px; border: none;">
                                <i class="fas fa-info-circle mr-2"></i>Estado
                            </th>
                            <th style="font-weight: 700; color: #0C2B44; padding: 12px; border: none; border-top-right-radius: 8px;">
                                <i class="fas fa-clock mr-2"></i>Fecha Inicio
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class="fas fa-spinner fa-spin mr-2"></i> Cargando eventos...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script src="{{ asset('assets/js/config.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
// Variables globales
let eventosData = [];
let graficoEstados, graficoTipos, graficoMensual, graficoONGs, graficoColaboraciones, graficoColaboracionesPie;
let datosCargados = false;
let graficosCreados = false;
let inicializacionCompleta = false;

// Prevenir múltiples ejecuciones del script completo
if (window.reportesInicializado) {
    console.warn('⚠️ Reportes ya fue inicializado, evitando duplicación');
    // Salir completamente del script si ya fue inicializado
    // No ejecutar nada más
} else {
    window.reportesInicializado = true;

// Función para verificar Chart.js - optimizada para evitar loops
function verificarChartJS() {
    return new Promise((resolve) => {
        // Si Chart.js ya está disponible, resolver inmediatamente
        if (typeof Chart !== 'undefined') {
            console.log('✅ Chart.js está disponible');
            resolve();
            return;
        }
        
        // Verificar máximo 50 veces (5 segundos) con intervalo de 100ms
        let intentos = 0;
        const maxIntentos = 50;
        
        let checkChart = setInterval(() => {
            intentos++;
            
            if (typeof Chart !== 'undefined') {
                console.log('✅ Chart.js está disponible');
                clearInterval(checkChart);
                checkChart = null;
                resolve();
                return;
            }
            
            // Si se alcanzó el máximo de intentos, resolver de todas formas
            if (intentos >= maxIntentos) {
                console.warn('⚠️ Chart.js no se cargó después de 5 segundos, continuando...');
                clearInterval(checkChart);
                checkChart = null;
                resolve();
            }
        }, 100);
    });
}

// Función principal para cargar datos
async function cargarDatos() {
    // Evitar múltiples cargas simultáneas
    if (datosCargados) {
        console.log('Los datos ya fueron cargados, omitiendo...');
        return;
    }
    
    datosCargados = true;
    
    const token = localStorage.getItem('token');
    const empresaId = parseInt(localStorage.getItem('id_entidad') || localStorage.getItem('id_usuario'), 10);
    const apiUrl = window.API_BASE_URL || window.location.origin;

    if (!token || isNaN(empresaId) || empresaId <= 0) {
        datosCargados = false; // Resetear en caso de error
        alert('Debe iniciar sesión correctamente');
        window.location.href = '/login';
        return;
    }

    try {
        console.log('Cargando eventos de la empresa...');
        console.log('API_BASE_URL:', apiUrl);
        console.log('Token disponible:', token ? 'Sí' : 'No');
        console.log('Empresa ID:', empresaId);
        
        // Cargar eventos de la empresa
        const url = `${apiUrl}/api/empresas/mis-eventos`;
        console.log('🔗 URL de petición:', url);
        
        const res = await fetch(url, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            credentials: 'include'
        });

        console.log('Respuesta HTTP:', res.status, res.statusText);

        if (!res.ok) {
            const errorText = await res.text();
            console.error('Error en respuesta:', errorText);
            throw new Error(`HTTP error! status: ${res.status} - ${errorText}`);
        }

        const data = await res.json();
        console.log('Respuesta de API completa:', data);
        
        if (!data.success) {
            throw new Error(data.error || 'Error al cargar eventos');
        }

        eventosData = data.eventos || [];
        console.log('✅ Eventos cargados:', eventosData.length, 'eventos');
        console.log('📊 Estructura de datos:', eventosData.length > 0 ? eventosData[0] : 'Sin eventos');
        console.log('📈 Debug info:', data.debug || 'No disponible');
        
        // Validar estructura de datos
        if (eventosData.length > 0) {
            const primerEvento = eventosData[0];
            console.log('🔍 Validando estructura:', {
                tieneEvento: !!primerEvento.evento,
                tieneTipoRelacion: !!primerEvento.tipo_relacion,
                tieneOng: !!primerEvento.evento?.ong
            });
        }
        
        if (eventosData.length === 0) {
            console.warn('⚠️ No se encontraron eventos para esta empresa');
            const tbody = document.getElementById('tablaEventos')?.querySelector('tbody');
            if (tbody) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="fas fa-info-circle fa-2x mb-3" style="color: #ccc;"></i>
                            <p class="mb-0">No tienes eventos patrocinados o colaborados aún.</p>
                            <small class="text-muted">Cuando patrocines o colabores en eventos, aparecerán aquí.</small>
                        </td>
                    </tr>
                `;
            }
            
            // Mostrar estadísticas en cero
            calcularEstadisticas([]);
            
            // Crear gráficos vacíos
            await verificarChartJS();
            crearGraficos([]);
            llenarEventosProximos([]);
        } else {
            // Calcular y mostrar estadísticas
            calcularEstadisticas(eventosData);
            
            // Verificar que Chart.js esté disponible antes de crear gráficos
            await verificarChartJS();
            
            // Crear gráficos con datos reales
            crearGraficos(eventosData);
            
            // Llenar tabla
            llenarTabla(eventosData);
            
            // Llenar eventos próximos
            llenarEventosProximos(eventosData);
            
            console.log('✅ Todos los datos procesados y mostrados correctamente');
        }

    } catch (error) {
        datosCargados = false; // Resetear en caso de error para permitir reintento
        console.error('Error cargando reportes:', error);
        const errorMsg = error.message || 'Error desconocido';
        
        const tbody = document.getElementById('tablaEventos')?.querySelector('tbody');
        if (tbody) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-danger">
                        <i class="fas fa-exclamation-circle"></i> Error al cargar datos: ${errorMsg}
                        <br><small>Por favor, verifica la consola para más detalles.</small>
                    </td>
                </tr>
            `;
        }
        
        // Intentar crear gráficos vacíos
        try {
            if (typeof Chart !== 'undefined') {
                crearGraficos([]);
            }
        } catch (e) {
            console.error('Error creando gráficos vacíos:', e);
        }
    }
}

// Usar una función inmediata para evitar múltiples ejecuciones
// Solo ejecutar si no está inicializado
(function() {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', inicializarReportes, { once: true });
    } else {
        // DOM ya está listo, esperar un poco más para asegurar que todo esté cargado
        setTimeout(() => {
            if (!inicializacionCompleta && !window.reportesInicializando) {
                inicializarReportes();
            }
        }, 200);
    }
})();

async function inicializarReportes() {
    // Evitar múltiples inicializaciones - protección doble
    if (inicializacionCompleta || window.reportesInicializando) {
        console.log('⚠️ Ya se está inicializando o ya se inicializó, omitiendo...');
        return;
    }
    
    window.reportesInicializando = true;
    inicializacionCompleta = true;
    
    console.log('🚀 DOM cargado, iniciando carga de datos...');
    console.log('API_BASE_URL disponible:', typeof window.API_BASE_URL !== 'undefined' ? window.API_BASE_URL : 'NO');
    
    // Asignar eventos a los botones primero - solo una vez
    const btnPDF = document.getElementById('btnDescargarPDF');
    const btnExcel = document.getElementById('btnDescargarExcel');
    
    if (btnPDF && !btnPDF.dataset.listenerAdded) {
        btnPDF.dataset.listenerAdded = 'true';
        btnPDF.addEventListener('click', async function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            if (!this.disabled && !generandoPDF) {
                await descargarPDF.call(this);
            }
        }, { once: false, passive: false });
    }
    
    if (btnExcel && !btnExcel.dataset.listenerAdded) {
        btnExcel.dataset.listenerAdded = 'true';
        btnExcel.addEventListener('click', async function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            if (!this.disabled && !generandoExcel) {
                await descargarExcel.call(this);
            }
        }, { once: false, passive: false });
    }
    
    // Esperar un momento para que config.js cargue
    await new Promise(resolve => setTimeout(resolve, 300));
    
    // Verificar que API_BASE_URL esté disponible
    if (typeof window.API_BASE_URL === 'undefined') {
        console.warn('⚠️ API_BASE_URL no está disponible, esperando...');
        await new Promise(resolve => setTimeout(resolve, 500));
    }
    
    // Verificar Chart.js
    await verificarChartJS();
    
    // Cargar datos una sola vez
    await cargarDatos();
    
    // Marcar como completado
    window.reportesInicializando = false;
}

// Función auxiliar para calcular estados (usada por crearGraficos)
function calcularEstadosParaGrafico(eventos) {
    const estados = {
        'Próximo': 0,
        'Activo': 0,
        'Finalizado': 0
    };
    
    const ahora = new Date();
    
    eventos.forEach(item => {
        const evento = item.evento;
        if (!evento || !evento.fecha_inicio) {
            estados['Activo']++;
            return;
        }
        
        try {
            const fechaInicio = new Date(evento.fecha_inicio);
            const fechaFin = evento.fecha_fin ? new Date(evento.fecha_fin) : null;
            
            if (fechaFin && !isNaN(fechaFin.getTime()) && fechaFin < ahora) {
                estados['Finalizado']++;
            } else if (!isNaN(fechaInicio.getTime()) && fechaInicio <= ahora && (!fechaFin || fechaFin >= ahora)) {
                estados['Activo']++;
            } else if (!isNaN(fechaInicio.getTime()) && fechaInicio > ahora) {
                estados['Próximo']++;
            } else {
                estados['Activo']++;
            }
        } catch (e) {
            estados['Activo']++;
        }
    });
    
    return estados;
}

function calcularEstadisticas(eventos) {
    console.log('📊 Calculando estadísticas para', eventos.length, 'eventos');
    
    const ahora = new Date();
    
    // Total eventos
    const totalEventos = eventos.length;
    
    // Eventos por estado dinámico
    let eventosActivos = 0;
    let eventosFinalizados = 0;
    let eventosProximos = 0;
    
    // Contadores por tipo de relación
    let totalPatrocinios = 0;
    let totalColaboraciones = 0;
    
    // ONGs únicas
    const ongsSet = new Set();
    
    eventos.forEach(item => {
        const evento = item.evento;
        if (!evento) {
            console.warn('⚠️ Item sin evento:', item);
            return;
        }
        
        // Contar por tipo de relación
        if (item.tipo_relacion === 'patrocinadora') {
            totalPatrocinios++;
        } else if (item.tipo_relacion === 'colaboradora') {
            totalColaboraciones++;
        }
        
        // Agregar ONG a set
        if (evento.ong && evento.ong.user_id) {
            ongsSet.add(evento.ong.user_id);
        }
        
        // Calcular estado dinámico
        if (evento.fecha_inicio && evento.fecha_fin) {
            try {
                const fechaInicio = new Date(evento.fecha_inicio);
                const fechaFin = new Date(evento.fecha_fin);
                
                if (!isNaN(fechaInicio.getTime()) && !isNaN(fechaFin.getTime())) {
                    if (fechaFin < ahora) {
                        eventosFinalizados++;
                    } else if (fechaInicio <= ahora && fechaFin >= ahora) {
                        eventosActivos++;
                    } else if (fechaInicio > ahora) {
                        eventosProximos++;
                    }
                } else {
                    eventosActivos++;
                }
            } catch (e) {
                console.warn('Error procesando fechas:', e, evento);
                eventosActivos++;
            }
        } else if (evento.fecha_inicio) {
            try {
                const fechaInicio = new Date(evento.fecha_inicio);
                if (!isNaN(fechaInicio.getTime())) {
                    if (fechaInicio > ahora) {
                        eventosProximos++;
                    } else {
                        eventosActivos++;
                    }
                } else {
                    eventosActivos++;
                }
            } catch (e) {
                eventosActivos++;
            }
        } else {
            eventosActivos++;
        }
    });
    
    console.log('📈 Estadísticas calculadas:', {
        total: totalEventos,
        activos: eventosActivos,
        finalizados: eventosFinalizados,
        proximos: eventosProximos,
        patrocinios: totalPatrocinios,
        colaboraciones: totalColaboraciones,
        ongs: ongsSet.size
    });
    
    // Actualizar estadísticas principales solo si los elementos existen
    const totalEventosEl = document.getElementById('totalEventos');
    const eventosActivosEl = document.getElementById('eventosActivos');
    const eventosFinalizadosEl = document.getElementById('eventosFinalizados');
    const ongsBeneficiadasEl = document.getElementById('ongsBeneficiadas');
    const totalPatrociniosEl = document.getElementById('totalPatrocinios');
    const totalColaboracionesEl = document.getElementById('totalColaboraciones');
    const eventosProximosEl = document.getElementById('eventosProximos');
    
    if (totalEventosEl) {
        totalEventosEl.textContent = totalEventos;
        console.log('✅ Total eventos actualizado:', totalEventos);
    }
    if (eventosActivosEl) eventosActivosEl.textContent = eventosActivos;
    if (eventosFinalizadosEl) eventosFinalizadosEl.textContent = eventosFinalizados;
    if (ongsBeneficiadasEl) ongsBeneficiadasEl.textContent = ongsSet.size;
    if (totalPatrociniosEl) totalPatrociniosEl.textContent = totalPatrocinios;
    if (totalColaboracionesEl) totalColaboracionesEl.textContent = totalColaboraciones;
    if (eventosProximosEl) eventosProximosEl.textContent = eventosProximos;
}

function crearGraficos(eventos) {
    console.log('📊 Creando gráficos con', eventos.length, 'eventos');
    
    // Validar que Chart.js esté disponible
    if (typeof Chart === 'undefined') {
        console.error('❌ Chart.js no está disponible');
        // Intentar esperar un poco más
        setTimeout(() => {
            if (typeof Chart !== 'undefined') {
                crearGraficos(eventos);
            } else {
                console.error('❌ Chart.js aún no está disponible después de esperar');
            }
        }, 500);
        return;
    }
    
    // Validar que hay eventos con estructura correcta
    const eventosValidos = eventos.filter(item => item && item.evento);
    console.log('✅ Eventos válidos para gráficos:', eventosValidos.length);
    
    if (eventosValidos.length === 0 && eventos.length > 0) {
        console.warn('⚠️ Hay eventos pero ninguno tiene estructura válida');
    }
    
    // Destruir gráficos existentes antes de crear nuevos
    if (graficoEstados) {
        graficoEstados.destroy();
        graficoEstados = null;
    }
    if (graficoTipos) {
        graficoTipos.destroy();
        graficoTipos = null;
    }
    if (graficoMensual) {
        graficoMensual.destroy();
        graficoMensual = null;
    }
    if (graficoONGs) {
        graficoONGs.destroy();
        graficoONGs = null;
    }
    if (graficoColaboraciones) {
        graficoColaboraciones.destroy();
        graficoColaboraciones = null;
    }
    if (graficoColaboracionesPie) {
        graficoColaboracionesPie.destroy();
        graficoColaboracionesPie = null;
    }
    
    // Gráfico de estados - usar función auxiliar
    const estados = calcularEstadosParaGrafico(eventosValidos);
    console.log('📊 Estados calculados:', estados);
    
    const ctxEstados = document.getElementById('graficoEstados');
    if (!ctxEstados) {
        console.error('❌ No se encontró el canvas graficoEstados');
        return;
    }
    
    try {
        // Asegurar que siempre haya datos para mostrar
        const estadosLabels = Object.keys(estados);
        const estadosValues = Object.values(estados);
        const totalEstados = estadosValues.reduce((a, b) => a + b, 0);
        
        // Si no hay datos, mostrar gráfico vacío con mensaje
        if (totalEstados === 0) {
            estadosLabels.push('Sin datos');
            estadosValues.push(0);
        }
        
        graficoEstados = new Chart(ctxEstados, {
            type: 'doughnut',
            data: {
                labels: estadosLabels,
                datasets: [{
                    data: estadosValues,
                    backgroundColor: [
                        '#0C2B44',
                        '#00A36C',
                        '#1a4a6b'
                    ],
                    borderWidth: 2.5,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 1, // 1:1 para gráficas cuadradas 10x10
                animation: {
                    duration: 0 // Deshabilitar animaciones para evitar actualizaciones constantes
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: {
                                size: 11,
                                weight: '600',
                                family: "'Segoe UI', sans-serif"
                            },
                            usePointStyle: true,
                            pointStyle: 'circle',
                            boxWidth: 8,
                            boxHeight: 8
                        }
                    },
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(12, 43, 68, 0.9)',
                        titleFont: { size: 11, weight: '600' },
                        bodyFont: { size: 10 },
                        padding: 10,
                        cornerRadius: 6,
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += context.parsed || 0;
                                return label;
                            }
                        }
                    }
                }
            }
        });
        console.log('✅ Gráfico de estados creado exitosamente');
    } catch (error) {
        console.error('❌ Error creando gráfico de estados:', error);
    }
    
    // Gráfico de tipos
    const tipos = {};
    eventosValidos.forEach(item => {
        const tipo = item.evento?.tipo_evento || 'Sin tipo';
        tipos[tipo] = (tipos[tipo] || 0) + 1;
    });
    
    console.log('📊 Tipos calculados:', tipos);
    
    const ctxTipos = document.getElementById('graficoTipos');
    if (!ctxTipos) {
        console.error('No se encontró el canvas graficoTipos');
        return;
    }
    
    if (graficoTipos) graficoTipos.destroy();
    
    try {
        const tiposKeys = Object.keys(tipos);
        const tiposValues = Object.values(tipos);
        
        // Asegurar que siempre haya datos para mostrar
        const tiposLabels = tiposKeys.length > 0 ? tiposKeys : ['Sin datos'];
        const tiposData = tiposValues.length > 0 ? tiposValues : [0];
        
        graficoTipos = new Chart(ctxTipos, {
            type: 'bar',
            data: {
                labels: tiposLabels,
                datasets: [{
                    label: 'Cantidad',
                    data: tiposData,
                    backgroundColor: tiposLabels.map((_, i) => i % 2 === 0 ? '#0C2B44' : '#00A36C'),
                    borderRadius: 6,
                    borderSkipped: false,
                    maxBarThickness: 50
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 1, // 1:1 para gráficas cuadradas 10x10
                animation: {
                    duration: 0 // Deshabilitar animaciones
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(12, 43, 68, 0.9)',
                        titleFont: { size: 12, weight: '600' },
                        bodyFont: { size: 11 },
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: true
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            font: { size: 10, family: "'Segoe UI', sans-serif" },
                            color: '#6c757d'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            stepSize: 1,
                            font: { size: 10, family: "'Segoe UI', sans-serif" },
                            color: '#6c757d'
                        }
                    }
                }
            }
        });
        console.log('✅ Gráfico de tipos creado exitosamente');
    } catch (error) {
        console.error('❌ Error creando gráfico de tipos:', error);
    }
    
    // Gráfico mensual - usar formato YYYY-MM para ordenamiento correcto
    const eventosPorMes = {};
    const eventosPorMesObj = {}; // Guardar objeto Date para ordenamiento
    
    eventosValidos.forEach(item => {
        if (item.evento?.fecha_inicio) {
            try {
                const fecha = new Date(item.evento.fecha_inicio);
                if (!isNaN(fecha.getTime())) {
                    // Usar formato YYYY-MM para ordenamiento
                    const mesKey = fecha.getFullYear() + '-' + String(fecha.getMonth() + 1).padStart(2, '0');
                    const mesLabel = fecha.toLocaleDateString('es-ES', { month: 'short', year: 'numeric' });
                    
                    if (!eventosPorMesObj[mesKey]) {
                        eventosPorMesObj[mesKey] = {
                            label: mesLabel,
                            fecha: fecha,
                            count: 0
                        };
                    }
                    eventosPorMesObj[mesKey].count++;
                }
            } catch (e) {
                console.warn('⚠️ Error procesando fecha para gráfico mensual:', e);
            }
        }
    });
    
    // Ordenar por fecha (mesKey) y extraer labels y valores
    const mesesOrdenados = Object.keys(eventosPorMesObj)
        .sort() // YYYY-MM se ordena correctamente como string
        .map(key => eventosPorMesObj[key]);
    
    const meses = mesesOrdenados.map(m => m.label);
    const valores = mesesOrdenados.map(m => m.count);
    
    console.log('Eventos por mes calculados:', mesesOrdenados.length, 'meses');
    
    const ctxMensual = document.getElementById('graficoMensual');
    if (!ctxMensual) {
        console.error('No se encontró el canvas graficoMensual');
        return;
    }
    
    if (graficoMensual) graficoMensual.destroy();
    
    try {
        // Si no hay datos, crear gráfico con mensaje
        let mesesFinal = meses;
        let valoresPatrocinios = [];
        let valoresColaboraciones = [];
        
        if (meses.length === 0) {
            mesesFinal = ['Sin datos'];
            valoresPatrocinios = [0];
            valoresColaboraciones = [0];
        } else {
            // Separar patrocinios y colaboraciones por mes
            const eventosPorMesPatrocinios = {};
            const eventosPorMesColaboraciones = {};
            
            eventosValidos.forEach(item => {
                if (item.evento?.fecha_inicio) {
                    try {
                        const fecha = new Date(item.evento.fecha_inicio);
                        if (!isNaN(fecha.getTime())) {
                            const mesKey = fecha.getFullYear() + '-' + String(fecha.getMonth() + 1).padStart(2, '0');
                            if (!eventosPorMesPatrocinios[mesKey]) eventosPorMesPatrocinios[mesKey] = 0;
                            if (!eventosPorMesColaboraciones[mesKey]) eventosPorMesColaboraciones[mesKey] = 0;
                            
                            if (item.tipo_relacion === 'patrocinadora') {
                                eventosPorMesPatrocinios[mesKey]++;
                            } else {
                                eventosPorMesColaboraciones[mesKey]++;
                            }
                        }
                    } catch (e) {
                        console.warn('⚠️ Error procesando fecha:', e);
                    }
                }
            });
            
            valoresPatrocinios = mesesOrdenados.map(m => {
                const mesKey = m.fecha.getFullYear() + '-' + String(m.fecha.getMonth() + 1).padStart(2, '0');
                return eventosPorMesPatrocinios[mesKey] || 0;
            });
            valoresColaboraciones = mesesOrdenados.map(m => {
                const mesKey = m.fecha.getFullYear() + '-' + String(m.fecha.getMonth() + 1).padStart(2, '0');
                return eventosPorMesColaboraciones[mesKey] || 0;
            });
        }
        
        graficoMensual = new Chart(ctxMensual, {
            type: 'line',
            data: {
                labels: mesesFinal,
                datasets: [
                    {
                        label: 'Patrocinios',
                        data: valoresPatrocinios,
                        borderColor: '#0C2B44',
                        backgroundColor: 'rgba(12, 43, 68, 0.05)',
                        tension: 0.4,
                        fill: false,
                        borderWidth: 2.5,
                        pointRadius: 4,
                        pointBackgroundColor: '#0C2B44',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: '#0C2B44',
                        pointHoverBorderColor: '#ffffff',
                        pointHoverBorderWidth: 2
                    },
                    {
                        label: 'Colaboraciones',
                        data: valoresColaboraciones,
                        borderColor: '#00A36C',
                        backgroundColor: 'rgba(0, 163, 108, 0.15)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 2.5,
                        pointRadius: 4,
                        pointBackgroundColor: '#00A36C',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: '#00A36C',
                        pointHoverBorderColor: '#ffffff',
                        pointHoverBorderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 1, // 1:1 para gráficas cuadradas 10x10
                animation: {
                    duration: 0 // Deshabilitar animaciones
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: { size: 11, weight: '600', family: "'Segoe UI', sans-serif" },
                            usePointStyle: true,
                            pointStyle: 'circle',
                            boxWidth: 8,
                            boxHeight: 8
                        }
                    },
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(12, 43, 68, 0.9)',
                        titleFont: { size: 12, weight: '600' },
                        bodyFont: { size: 11 },
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            font: { size: 10, family: "'Segoe UI', sans-serif" },
                            color: '#6c757d'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            stepSize: 1,
                            font: { size: 10, family: "'Segoe UI', sans-serif" },
                            color: '#6c757d'
                        }
                    }
                }
            }
        });
        console.log('✅ Gráfico mensual creado exitosamente');
    } catch (error) {
        console.error('❌ Error creando gráfico mensual:', error);
    }
    
    // Gráfico por ONG
    const ongs = {};
    eventosValidos.forEach(item => {
        const ongNombre = item.evento?.ong?.nombre_ong || 'Sin ONG';
        ongs[ongNombre] = (ongs[ongNombre] || 0) + 1;
    });
    
    // Ordenar por cantidad y tomar los top 5
    const ongsOrdenadas = Object.entries(ongs)
        .sort((a, b) => b[1] - a[1])
        .slice(0, 5);
    
    console.log('📊 ONGs calculadas:', ongs, 'Top 5:', ongsOrdenadas);
    
    const ctxONGs = document.getElementById('graficoONGs');
    if (!ctxONGs) {
        console.error('No se encontró el canvas graficoONGs');
        return;
    }
    
    if (graficoONGs) graficoONGs.destroy();
    
    try {
        const ongsLabels = ongsOrdenadas.length > 0 ? ongsOrdenadas.map(o => o[0]) : ['Sin datos'];
        const ongsData = ongsOrdenadas.length > 0 ? ongsOrdenadas.map(o => o[1]) : [0];
        
        graficoONGs = new Chart(ctxONGs, {
            type: 'bar',
            data: {
                labels: ongsLabels,
                datasets: [{
                    label: 'Eventos',
                    data: ongsData,
                    backgroundColor: '#00A36C',
                    borderRadius: 8
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 1, // 1:1 para gráficas cuadradas 10x10
                animation: {
                    duration: 0
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(12, 43, 68, 0.9)',
                        titleFont: { size: 11, weight: '600' },
                        bodyFont: { size: 10 },
                        padding: 10,
                        cornerRadius: 6
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            stepSize: 1,
                            font: { size: 10, family: "'Segoe UI', sans-serif" },
                            color: '#6c757d'
                        }
                    },
                    y: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            font: { size: 10, family: "'Segoe UI', sans-serif" },
                            color: '#6c757d'
                        }
                    }
                }
            }
        });
        console.log('✅ Gráfico de ONGs creado exitosamente');
    } catch (error) {
        console.error('❌ Error creando gráfico de ONGs:', error);
    }
    
    // Gráfico de colaboraciones (breakdown)
    const ctxColaboraciones = document.getElementById('graficoColaboraciones');
    if (ctxColaboraciones) {
        if (graficoColaboraciones) graficoColaboraciones.destroy();
        
        try {
            const patrocinios = eventosValidos.filter(e => e.tipo_relacion === 'patrocinadora').length;
            const activos = eventosValidos.filter(e => {
                const evento = e.evento;
                if (!evento || !evento.fecha_inicio) return false;
                try {
                    const fechaInicio = new Date(evento.fecha_inicio);
                    const fechaFin = evento.fecha_fin ? new Date(evento.fecha_fin) : null;
                    const ahora = new Date();
                    if (isNaN(fechaInicio.getTime())) return false;
                    return fechaInicio <= ahora && (!fechaFin || isNaN(fechaFin.getTime()) || fechaFin >= ahora);
                } catch (e) {
                    return false;
                }
            }).length;
            const colaboraciones = eventosValidos.filter(e => e.tipo_relacion === 'colaboradora').length;
            
            console.log('📊 Colaboraciones breakdown:', { patrocinios, activos, colaboraciones });
            
            graficoColaboraciones = new Chart(ctxColaboraciones, {
                type: 'bar',
                data: {
                    labels: ['Patrocinios', 'Activos', 'Colaboraciones'],
                    datasets: [{
                        label: 'Cantidad',
                        data: [patrocinios, activos, colaboraciones],
                        backgroundColor: ['#0C2B44', '#00A36C', '#00A36C'],
                        borderRadius: 6,
                        borderSkipped: false,
                        maxBarThickness: 40
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    aspectRatio: 1, // 1:1 para gráficas cuadradas 10x10
                    animation: {
                        duration: 0 // Deshabilitar animaciones
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            enabled: true,
                            backgroundColor: 'rgba(12, 43, 68, 0.9)',
                            titleFont: { size: 11, weight: '600' },
                            bodyFont: { size: 10 },
                            padding: 10,
                            cornerRadius: 6
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false, drawBorder: false },
                            ticks: {
                                font: { size: 9, family: "'Segoe UI', sans-serif" },
                                color: '#6c757d'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)',
                                drawBorder: false
                            },
                            ticks: {
                                stepSize: 1,
                                font: { size: 9, family: "'Segoe UI', sans-serif" },
                                color: '#6c757d'
                            }
                        }
                    }
                }
            });
            console.log('✅ Gráfico de colaboraciones creado exitosamente');
        } catch (error) {
            console.error('❌ Error creando gráfico de colaboraciones:', error);
        }
    }
    
    // Pie chart de colaboraciones por tipo de evento
    const ctxColaboracionesPie = document.getElementById('graficoColaboracionesPie');
    if (ctxColaboracionesPie) {
        if (graficoColaboracionesPie) graficoColaboracionesPie.destroy();
        
        try {
            const tiposColaboraciones = {};
            eventosValidos.forEach(item => {
                const tipo = item.evento?.tipo_evento || 'Otro';
                tiposColaboraciones[tipo] = (tiposColaboraciones[tipo] || 0) + 1;
            });
            
            const tiposKeys = Object.keys(tiposColaboraciones);
            const tiposValues = Object.values(tiposColaboraciones);
            const total = tiposValues.reduce((a, b) => a + b, 0);
            
            // Colores alternados de la paleta
            const colores = ['#0C2B44', '#00A36C', '#1a4a6b', '#059669'];
            const backgroundColor = tiposKeys.map((_, i) => colores[i % colores.length]);
            
            graficoColaboracionesPie = new Chart(ctxColaboracionesPie, {
                type: 'pie',
                data: {
                    labels: tiposKeys.map((k, i) => {
                        const porcentaje = total > 0 ? Math.round((tiposValues[i] / total) * 100) : 0;
                        return `${k} (${porcentaje}%)`;
                    }),
                    datasets: [{
                        data: tiposValues,
                        backgroundColor: backgroundColor,
                        borderWidth: 2.5,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    aspectRatio: 1, // 1:1 para gráficas cuadradas 10x10
                    animation: {
                        duration: 0 // Deshabilitar animaciones
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 12,
                                font: { size: 10, weight: '600', family: "'Segoe UI', sans-serif" },
                                usePointStyle: true,
                                pointStyle: 'circle',
                                boxWidth: 8,
                                boxHeight: 8
                            }
                        },
                        tooltip: {
                            enabled: true,
                            backgroundColor: 'rgba(12, 43, 68, 0.9)',
                            titleFont: { size: 11, weight: '600' },
                            bodyFont: { size: 10 },
                            padding: 10,
                            cornerRadius: 6
                        }
                    }
                }
            });
            console.log('✅ Pie chart de colaboraciones creado exitosamente');
        } catch (error) {
            console.error('❌ Error creando pie chart de colaboraciones:', error);
        }
    }
    
        console.log('✅ Todos los gráficos creados exitosamente');
        console.log('📊 Resumen:', {
            estados: Object.keys(estados).length,
            tipos: Object.keys(tipos).length,
            meses: meses.length,
            ongs: ongsOrdenadas.length
        });
}

function llenarTabla(eventos) {
    console.log('📋 Llenando tabla con', eventos.length, 'eventos');
    
    const tbody = document.querySelector('#tablaEventos tbody');
    if (!tbody) {
        console.error('❌ No se encontró el tbody de la tabla');
        return;
    }
    
    if (eventos.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">No hay eventos registrados</td></tr>';
        console.log('⚠️ Tabla vacía - sin eventos');
        return;
    }
    
    const filas = eventos.map((item, index) => {
        const evento = item.evento;
        if (!evento) {
            console.warn('⚠️ Item sin evento en índice', index, item);
            return '';
        }
        
        let fechaInicio = 'N/A';
        try {
            if (evento.fecha_inicio) {
                fechaInicio = new Date(evento.fecha_inicio).toLocaleDateString('es-ES', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
            }
        } catch (e) {
            console.warn('Error formateando fecha:', e);
        }
        
        // Determinar estado dinámico
        let estado = evento.estado || 'N/A';
        let estadoClass = 'bg-secondary';
        const ahora = new Date();
        
        try {
            if (evento.fecha_inicio && evento.fecha_fin) {
                const fechaInicio = new Date(evento.fecha_inicio);
                const fechaFin = new Date(evento.fecha_fin);
                
                if (!isNaN(fechaInicio.getTime()) && !isNaN(fechaFin.getTime())) {
                    if (fechaFin < ahora) {
                        estado = 'Finalizado';
                        estadoClass = 'bg-warning';
                    } else if (fechaInicio <= ahora && fechaFin >= ahora) {
                        estado = 'Activo';
                        estadoClass = 'bg-success';
                    } else if (fechaInicio > ahora) {
                        estado = 'Próximo';
                        estadoClass = 'bg-info';
                    }
                }
            } else if (evento.fecha_inicio) {
                const fechaInicio = new Date(evento.fecha_inicio);
                if (!isNaN(fechaInicio.getTime())) {
                    if (fechaInicio > ahora) {
                        estado = 'Próximo';
                        estadoClass = 'bg-info';
                    } else {
                        estado = 'Activo';
                        estadoClass = 'bg-success';
                    }
                }
            }
        } catch (e) {
            console.warn('Error calculando estado:', e);
        }
        
        // Tipo de relación
        const tipoRelacion = item.tipo_relacion === 'patrocinadora' ? 'Patrocinador' : 'Colaborador';
        const tipoRelacionColor = item.tipo_relacion === 'patrocinadora'
            ? 'background: linear-gradient(135deg, #0C2B44 0%, #1a4a6b 100%); color: white;'
            : 'background: linear-gradient(135deg, #00A36C 0%, #059669 100%); color: white;';
        
        return `
            <tr>
                <td><strong>${(evento.titulo || 'Sin título').substring(0, 50)}</strong></td>
                <td>${(evento.ong?.nombre_ong || 'N/A').substring(0, 30)}</td>
                <td><span class="badge bg-secondary">${evento.tipo_evento || 'N/A'}</span></td>
                <td><span class="badge" style="${tipoRelacionColor}">${tipoRelacion}</span></td>
                <td><span class="badge ${estadoClass}">${estado}</span></td>
                <td>${fechaInicio}</td>
            </tr>
        `;
    }).filter(fila => fila !== '').join('');
    
    tbody.innerHTML = filas || '<tr><td colspan="6" class="text-center text-muted">No hay eventos válidos</td></tr>';
    console.log('✅ Tabla llenada con', eventos.filter(item => item.evento).length, 'filas');
}

function llenarEventosProximos(eventos) {
    const contenedor = document.getElementById('listaEventosProximos');
    if (!contenedor) return;
    
    const ahora = new Date();
    const eventosProximos = eventos
        .filter(item => {
            const evento = item.evento;
            if (!evento || !evento.fecha_inicio) return false;
            const fechaInicio = new Date(evento.fecha_inicio);
            return fechaInicio > ahora;
        })
        .sort((a, b) => {
            const fechaA = new Date(a.evento.fecha_inicio);
            const fechaB = new Date(b.evento.fecha_inicio);
            return fechaA - fechaB;
        })
        .slice(0, 5); // Top 5 próximos
    
    if (eventosProximos.length === 0) {
        contenedor.innerHTML = `
            <div class="text-center text-muted py-4">
                <i class="far fa-calendar-times fa-3x mb-3" style="color: #ccc;"></i>
                <p class="mb-0">No hay eventos próximos</p>
            </div>
        `;
        return;
    }
    
    contenedor.innerHTML = eventosProximos.map((item, index) => {
        const evento = item.evento;
        const fechaInicio = new Date(evento.fecha_inicio);
        const fechaFormateada = fechaInicio.toLocaleDateString('es-ES', {
            day: '2-digit',
            month: '2-digit'
        });
        
        return `
            <div class="list-group-item border-0 mb-2" style="border-radius: 8px; background: #f8f9fa;">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <span class="badge mr-3" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); color: white; padding: 0.5rem 0.75rem; border-radius: 8px; font-size: 0.875rem; font-weight: 700;">${index + 1}</span>
                        <div>
                            <h6 class="mb-1" style="font-weight: 700; color: #0C2B44;">${evento.titulo || 'Sin título'}</h6>
                            <small class="text-muted">${evento.ong?.nombre_ong || 'N/A'}</small>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="badge" style="background: rgba(0, 163, 108, 0.1); color: #00A36C; padding: 0.4rem 0.8rem; border-radius: 8px;">
                            <i class="far fa-calendar mr-1"></i>${fechaFormateada}
                        </span>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

// Variables para prevenir ejecuciones simultáneas
let generandoPDF = false;
let generandoExcel = false;

async function descargarPDF() {
    const boton = this;
    
    // Prevenir ejecuciones simultáneas
    if (generandoPDF) {
        console.warn('Ya se está generando un PDF, espera...');
        return;
    }
    
    generandoPDF = true;
    
    try {
        const textoOriginal = boton.innerHTML;
        boton.disabled = true;
        boton.innerHTML = '<i class="far fa-spinner fa-spin mr-2"></i>Generando PDF...';
        
        // Verificar que jsPDF esté disponible
        if (typeof window.jspdf === 'undefined') {
            throw new Error('jsPDF no está disponible');
        }
        
        // Verificar que html2canvas esté disponible
        if (typeof html2canvas === 'undefined') {
            throw new Error('html2canvas no está disponible');
        }
        
        // Importar jsPDF
        const { jsPDF } = window.jspdf;
        
        const pdf = new jsPDF('p', 'mm', 'a4');
        const pageWidth = pdf.internal.pageSize.getWidth();
        const pageHeight = pdf.internal.pageSize.getHeight();
        const margin = 15;
        let yPos = margin;
        
        // Función auxiliar para agregar nueva página si es necesario
        function checkPageBreak(neededHeight) {
            if (yPos + neededHeight > pageHeight - margin) {
                pdf.addPage();
                yPos = margin;
                return true;
            }
            return false;
        }
        
        // ========== PORTADA ==========
        pdf.setFillColor(12, 43, 68); // #0C2B44
        pdf.rect(0, 0, pageWidth, 50, 'F');
        
        pdf.setTextColor(255, 255, 255);
        pdf.setFontSize(24);
        pdf.setFont('helvetica', 'bold');
        pdf.text('REPORTE DE EMPRESA', pageWidth / 2, 25, { align: 'center' });
        
        pdf.setFontSize(12);
        pdf.setFont('helvetica', 'normal');
        const fechaGeneracion = new Date().toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        pdf.text(`Generado el ${fechaGeneracion}`, pageWidth / 2, 35, { align: 'center' });
        
        yPos = 60;
        
        // ========== RESUMEN EJECUTIVO ==========
        pdf.setTextColor(12, 43, 68);
        pdf.setFontSize(18);
        pdf.setFont('helvetica', 'bold');
        pdf.text('RESUMEN EJECUTIVO', margin, yPos);
        yPos += 10;
        
        pdf.setFontSize(10);
        pdf.setFont('helvetica', 'normal');
        
        const estadisticas = {
            'Total Eventos': document.getElementById('totalEventos')?.textContent || '0',
            'Eventos Activos': document.getElementById('eventosActivos')?.textContent || '0',
            'Eventos Finalizados': document.getElementById('eventosFinalizados')?.textContent || '0',
            'ONGs Beneficiadas': document.getElementById('ongsBeneficiadas')?.textContent || '0',
            'Patrocinios': document.getElementById('totalPatrocinios')?.textContent || '0',
            'Colaboraciones': document.getElementById('totalColaboraciones')?.textContent || '0',
            'Eventos Próximos': document.getElementById('eventosProximos')?.textContent || '0'
        };
        
        let xPos = margin;
        let col = 0;
        Object.entries(estadisticas).forEach(([key, value]) => {
            if (col === 2) {
                col = 0;
                xPos = margin;
                yPos += 8;
                checkPageBreak(8);
            }
            
            pdf.setFont('helvetica', 'bold');
            pdf.text(key + ':', xPos, yPos);
            pdf.setFont('helvetica', 'normal');
            pdf.text(value, xPos + 50, yPos);
            
            xPos += 90;
            col++;
        });
        
        yPos += 15;
        checkPageBreak(15);
        
        // ========== GRÁFICOS ==========
        // Gráfico de Estados
        if (graficoEstados) {
            checkPageBreak(60);
            pdf.setFontSize(16);
            pdf.setFont('helvetica', 'bold');
            pdf.text('Eventos por Estado', margin, yPos);
            yPos += 8;
            
            try {
                const imgEstados = graficoEstados.toBase64Image('image/png', 1.0);
                pdf.addImage(imgEstados, 'PNG', margin, yPos, 80, 50);
                yPos += 55;
            } catch (e) {
                console.warn('Error capturando gráfico de estados:', e);
                yPos += 10;
            }
            checkPageBreak(10);
        }
        
        // Gráfico de Tipos
        if (graficoTipos) {
            checkPageBreak(60);
            pdf.setFontSize(16);
            pdf.setFont('helvetica', 'bold');
            pdf.text('Eventos por Tipo', margin, yPos);
            yPos += 8;
            
            try {
                const imgTipos = graficoTipos.toBase64Image('image/png', 1.0);
                pdf.addImage(imgTipos, 'PNG', margin, yPos, 80, 50);
                yPos += 55;
            } catch (e) {
                console.warn('Error capturando gráfico de tipos:', e);
                yPos += 10;
            }
            checkPageBreak(10);
        }
        
        // Gráfico Mensual
        if (graficoMensual) {
            checkPageBreak(60);
            pdf.setFontSize(16);
            pdf.setFont('helvetica', 'bold');
            pdf.text('Tendencia de Participación por Mes', margin, yPos);
            yPos += 8;
            
            try {
                const imgMensual = graficoMensual.toBase64Image('image/png', 1.0);
                pdf.addImage(imgMensual, 'PNG', margin, yPos, 80, 50);
                yPos += 55;
            } catch (e) {
                console.warn('Error capturando gráfico mensual:', e);
                yPos += 10;
            }
            checkPageBreak(10);
        }
        
        // ========== TABLA DE EVENTOS ==========
        pdf.addPage();
        yPos = margin;
        
        pdf.setFontSize(18);
        pdf.setFont('helvetica', 'bold');
        pdf.text('DETALLE DE EVENTOS', margin, yPos);
        yPos += 10;
        
        // Encabezados de tabla
        pdf.setFontSize(9);
        pdf.setFont('helvetica', 'bold');
        pdf.setFillColor(12, 43, 68);
        pdf.setTextColor(255, 255, 255);
        pdf.rect(margin, yPos, pageWidth - 2 * margin, 8, 'F');
        
        const headers = ['Evento', 'ONG', 'Tipo', 'Relación', 'Estado', 'Fecha'];
        const colWidths = [50, 40, 25, 25, 25, 30];
        let xStart = margin + 2;
        
        headers.forEach((header, i) => {
            pdf.text(header, xStart, yPos + 6);
            xStart += colWidths[i];
        });
        
        yPos += 10;
        pdf.setTextColor(0, 0, 0);
        pdf.setFont('helvetica', 'normal');
        
        // Filas de eventos
        eventosData.forEach((item, index) => {
            checkPageBreak(8);
            const evento = item.evento;
            if (!evento) return;
            
            // Alternar color de fondo
            if (index % 2 === 0) {
                pdf.setFillColor(245, 245, 245);
                pdf.rect(margin, yPos - 2, pageWidth - 2 * margin, 6, 'F');
            }
            
            const fechaInicio = evento.fecha_inicio ? 
                new Date(evento.fecha_inicio).toLocaleDateString('es-ES') : 'N/A';
            const tipoRelacion = item.tipo_relacion === 'patrocinadora' ? 'Patrocinador' : 'Colaborador';
            
            let estado = evento.estado || 'N/A';
            const ahora = new Date();
            if (evento.fecha_inicio && evento.fecha_fin) {
                const fechaInicio = new Date(evento.fecha_inicio);
                const fechaFin = new Date(evento.fecha_fin);
                if (fechaFin < ahora) estado = 'Finalizado';
                else if (fechaInicio <= ahora && fechaFin >= ahora) estado = 'Activo';
                else if (fechaInicio > ahora) estado = 'Próximo';
            }
            
            const rowData = [
                (evento.titulo || 'Sin título').substring(0, 30),
                (evento.ong?.nombre_ong || 'N/A').substring(0, 25),
                (evento.tipo_evento || 'N/A').substring(0, 20),
                tipoRelacion.substring(0, 15),
                estado.substring(0, 15),
                fechaInicio
            ];
            
            xStart = margin + 2;
            rowData.forEach((text, i) => {
                pdf.text(text, xStart, yPos + 4);
                xStart += colWidths[i];
            });
            
            yPos += 8;
        });
        
        // ========== PIE DE PÁGINA ==========
        const totalPages = pdf.internal.pages.length - 1;
        for (let i = 1; i <= totalPages; i++) {
            pdf.setPage(i);
            pdf.setFontSize(8);
            pdf.setTextColor(128, 128, 128);
            pdf.text(
                `Página ${i} de ${totalPages} - Generado el ${fechaGeneracion}`,
                pageWidth / 2,
                pageHeight - 10,
                { align: 'center' }
            );
        }
        
        // Descargar
        const fecha = new Date().toISOString().split('T')[0];
        pdf.save(`reporte-empresa-${fecha}.pdf`);
        
        // Restaurar botón
        boton.disabled = false;
        boton.innerHTML = textoOriginal;
        
    } catch (error) {
        console.error('Error generando PDF:', error);
        alert('Error al generar el PDF: ' + error.message);
        if (boton) {
            boton.disabled = false;
            boton.innerHTML = '<i class="far fa-file-pdf mr-2"></i>Descargar PDF';
        }
    } finally {
        generandoPDF = false;
    }
}

// Función auxiliar para convertir gráfico a imagen base64
function chartToBase64(chart) {
    if (!chart) return null;
    try {
        return chart.toBase64Image('image/png', 1.0);
    } catch (e) {
        console.error('Error convirtiendo gráfico a imagen:', e);
        return null;
    }
}

async function descargarExcel() {
    const boton = this;
    
    // Prevenir ejecuciones simultáneas
    if (generandoExcel) {
        console.warn('Ya se está generando un Excel, espera...');
        return;
    }
    
    generandoExcel = true;
    
    try {
        const textoOriginal = boton.innerHTML;
        boton.disabled = true;
        boton.innerHTML = '<i class="far fa-spinner fa-spin mr-2"></i>Generando Excel...';
        
        // Verificar que XLSX esté disponible
        if (typeof XLSX === 'undefined') {
            throw new Error('XLSX no está disponible');
        }
        
        // Crear workbook
        const wb = XLSX.utils.book_new();
        const fechaGeneracion = new Date().toLocaleDateString('es-ES');
        const ahora = new Date();
        
        // ========== HOJA 1: RESUMEN EJECUTIVO ==========
        const resumen = [
            ['REPORTE DE EMPRESA - RESUMEN EJECUTIVO'],
            ['Fecha de generación', fechaGeneracion],
            [''],
            ['ESTADÍSTICAS GENERALES'],
            [''],
            ['Métrica', 'Valor'],
            ['Total Eventos', parseInt(document.getElementById('totalEventos')?.textContent || '0')],
            ['Eventos Activos', parseInt(document.getElementById('eventosActivos')?.textContent || '0')],
            ['Eventos Finalizados', parseInt(document.getElementById('eventosFinalizados')?.textContent || '0')],
            ['ONGs Beneficiadas', parseInt(document.getElementById('ongsBeneficiadas')?.textContent || '0')],
            ['Total Patrocinios', parseInt(document.getElementById('totalPatrocinios')?.textContent || '0')],
            ['Total Colaboraciones', parseInt(document.getElementById('totalColaboraciones')?.textContent || '0')],
            ['Eventos Próximos', parseInt(document.getElementById('eventosProximos')?.textContent || '0')],
            [''],
            ['TOTALES', '=SUM(B7:B13)']
        ];
        
        const ws1 = XLSX.utils.aoa_to_sheet(resumen);
        ws1['!cols'] = [
            { wch: 30 },
            { wch: 15 }
        ];
        
        // Agregar formato a encabezados
        if (!ws1['!merges']) ws1['!merges'] = [];
        ws1['!merges'].push({ s: { r: 0, c: 0 }, e: { r: 0, c: 1 } });
        ws1['!merges'].push({ s: { r: 3, c: 0 }, e: { r: 3, c: 1 } });
        
        XLSX.utils.book_append_sheet(wb, ws1, 'Resumen Ejecutivo');
        
        // ========== HOJA 2: EVENTOS DETALLADOS (TABLA) ==========
        const eventosDetallados = [
            ['Evento', 'ONG Organizadora', 'Tipo', 'Relación', 'Estado', 'Fecha Inicio', 'Fecha Fin', 'Ciudad']
        ];
        
        eventosData.forEach(item => {
            const evento = item.evento;
            if (!evento) return;
            
            const fechaInicio = evento.fecha_inicio ? 
                new Date(evento.fecha_inicio).toLocaleDateString('es-ES') : 'N/A';
            const fechaFin = evento.fecha_fin ? 
                new Date(evento.fecha_fin).toLocaleDateString('es-ES') : 'N/A';
            const tipoRelacion = item.tipo_relacion === 'patrocinadora' ? 'Patrocinador' : 'Colaborador';
            
            let estado = evento.estado || 'N/A';
            if (evento.fecha_inicio && evento.fecha_fin) {
                const fechaInicio = new Date(evento.fecha_inicio);
                const fechaFin = new Date(evento.fecha_fin);
                if (fechaFin < ahora) {
                    estado = 'Finalizado';
                } else if (fechaInicio <= ahora && fechaFin >= ahora) {
                    estado = 'Activo';
                } else if (fechaInicio > ahora) {
                    estado = 'Próximo';
                }
            }
            
            eventosDetallados.push([
                evento.titulo || 'Sin título',
                evento.ong?.nombre_ong || 'N/A',
                evento.tipo_evento || 'N/A',
                tipoRelacion,
                estado,
                fechaInicio,
                fechaFin,
                evento.ciudad || 'N/A'
            ]);
        });
        
        // Agregar fila de totales
        eventosDetallados.push(['', '', '', '', '', '', '', '']);
        eventosDetallados.push(['TOTAL EVENTOS', `=COUNTA(A2:A${eventosDetallados.length - 1})`, '', '', '', '', '', '']);
        
        const ws2 = XLSX.utils.aoa_to_sheet(eventosDetallados);
        ws2['!cols'] = [
            { wch: 35 },
            { wch: 30 },
            { wch: 20 },
            { wch: 15 },
            { wch: 15 },
            { wch: 15 },
            { wch: 15 },
            { wch: 20 }
        ];
        
        XLSX.utils.book_append_sheet(wb, ws2, 'Eventos Detallados');
        
        // ========== HOJA 3: EVENTOS POR MES (TABLA) ==========
        const eventosPorMes = [
            ['Mes', 'Patrocinios', 'Colaboraciones', 'Total']
        ];
        
        if (graficoMensual && graficoMensual.data && graficoMensual.data.labels) {
            const meses = graficoMensual.data.labels;
            const patrocinios = graficoMensual.data.datasets[0]?.data || [];
            const colaboraciones = graficoMensual.data.datasets[1]?.data || [];
            
            meses.forEach((mes, i) => {
                const total = (patrocinios[i] || 0) + (colaboraciones[i] || 0);
                eventosPorMes.push([
                    mes,
                    patrocinios[i] || 0,
                    colaboraciones[i] || 0,
                    total
                ]);
            });
            
            // Agregar totales
            eventosPorMes.push(['', '', '', '']);
            eventosPorMes.push(['TOTALES', `=SUM(B2:B${eventosPorMes.length - 1})`, `=SUM(C2:C${eventosPorMes.length - 1})`, `=SUM(D2:D${eventosPorMes.length - 1})`]);
        }
        
        const ws3 = XLSX.utils.aoa_to_sheet(eventosPorMes);
        ws3['!cols'] = [
            { wch: 20 },
            { wch: 15 },
            { wch: 15 },
            { wch: 15 }
        ];
        
        XLSX.utils.book_append_sheet(wb, ws3, 'Eventos por Mes');
        
        // ========== HOJA 4: EVENTOS POR TIPO (TABLA) ==========
        const eventosPorTipo = [
            ['Tipo de Evento', 'Cantidad', 'Porcentaje']
        ];
        
        if (graficoTipos && graficoTipos.data && graficoTipos.data.labels) {
            const tipos = graficoTipos.data.labels;
            const cantidades = graficoTipos.data.datasets[0]?.data || [];
            const total = cantidades.reduce((a, b) => a + b, 0);
            
            tipos.forEach((tipo, i) => {
                const cantidad = cantidades[i] || 0;
                const porcentaje = total > 0 ? (cantidad / total * 100).toFixed(2) : 0;
                eventosPorTipo.push([
                    tipo,
                    cantidad,
                    porcentaje + '%'
                ]);
            });
            
            // Agregar totales
            eventosPorTipo.push(['', '', '']);
            eventosPorTipo.push(['TOTAL', `=SUM(B2:B${eventosPorTipo.length - 1})`, '100%']);
        }
        
        const ws4 = XLSX.utils.aoa_to_sheet(eventosPorTipo);
        ws4['!cols'] = [
            { wch: 30 },
            { wch: 15 },
            { wch: 15 }
        ];
        
        XLSX.utils.book_append_sheet(wb, ws4, 'Eventos por Tipo');
        
        // ========== HOJA 5: EVENTOS POR ESTADO (TABLA) ==========
        const eventosPorEstado = [
            ['Estado', 'Cantidad', 'Porcentaje']
        ];
        
        if (graficoEstados && graficoEstados.data && graficoEstados.data.labels) {
            const estados = graficoEstados.data.labels;
            const cantidades = graficoEstados.data.datasets[0]?.data || [];
            const total = cantidades.reduce((a, b) => a + b, 0);
            
            estados.forEach((estado, i) => {
                const cantidad = cantidades[i] || 0;
                const porcentaje = total > 0 ? (cantidad / total * 100).toFixed(2) : 0;
                eventosPorEstado.push([
                    estado,
                    cantidad,
                    porcentaje + '%'
                ]);
            });
            
            // Agregar totales
            eventosPorEstado.push(['', '', '']);
            eventosPorEstado.push(['TOTAL', `=SUM(B2:B${eventosPorEstado.length - 1})`, '100%']);
        }
        
        const ws5 = XLSX.utils.aoa_to_sheet(eventosPorEstado);
        ws5['!cols'] = [
            { wch: 20 },
            { wch: 15 },
            { wch: 15 }
        ];
        
        XLSX.utils.book_append_sheet(wb, ws5, 'Eventos por Estado');
        
        // ========== HOJA 6: EVENTOS PRÓXIMOS (TABLA) ==========
        const eventosProximos = [
            ['Evento', 'ONG', 'Fecha Inicio', 'Días Restantes']
        ];
        
        const proximos = eventosData
            .filter(item => {
                const evento = item.evento;
                if (!evento || !evento.fecha_inicio) return false;
                return new Date(evento.fecha_inicio) > ahora;
            })
            .sort((a, b) => new Date(a.evento.fecha_inicio) - new Date(b.evento.fecha_inicio))
            .slice(0, 20);
        
        proximos.forEach(item => {
            const evento = item.evento;
            const fechaInicio = new Date(evento.fecha_inicio);
            const fecha = fechaInicio.toLocaleDateString('es-ES');
            const diasRestantes = Math.ceil((fechaInicio - ahora) / (1000 * 60 * 60 * 24));
            
            eventosProximos.push([
                evento.titulo || 'Sin título',
                evento.ong?.nombre_ong || 'N/A',
                fecha,
                diasRestantes
            ]);
        });
        
        const ws6 = XLSX.utils.aoa_to_sheet(eventosProximos);
        ws6['!cols'] = [
            { wch: 35 },
            { wch: 30 },
            { wch: 15 },
            { wch: 15 }
        ];
        
        XLSX.utils.book_append_sheet(wb, ws6, 'Eventos Próximos');
        
        // ========== HOJA 7: ANÁLISIS DE COLABORACIONES ==========
        const analisisColaboraciones = [
            ['ANÁLISIS DE COLABORACIONES', '', '', ''],
            ['', '', '', ''],
            ['Tipo de Relación', 'Cantidad', 'Porcentaje', 'ONGs Únicas']
        ];
        
        const patrocinios = eventosData.filter(e => e.tipo_relacion === 'patrocinadora').length;
        const colaboraciones = eventosData.filter(e => e.tipo_relacion === 'colaboradora').length;
        const total = patrocinios + colaboraciones;
        
        const ongsPatrocinios = new Set();
        const ongsColaboraciones = new Set();
        
        eventosData.forEach(item => {
            const evento = item.evento;
            if (!evento || !evento.ong) return;
            
            if (item.tipo_relacion === 'patrocinadora') {
                ongsPatrocinios.add(evento.ong.user_id || evento.ong.nombre_ong);
            } else {
                ongsColaboraciones.add(evento.ong.user_id || evento.ong.nombre_ong);
            }
        });
        
        const porcentajePatrocinios = total > 0 ? ((patrocinios / total) * 100).toFixed(2) : 0;
        const porcentajeColaboraciones = total > 0 ? ((colaboraciones / total) * 100).toFixed(2) : 0;
        
        analisisColaboraciones.push([
            'Patrocinios',
            patrocinios,
            porcentajePatrocinios + '%',
            ongsPatrocinios.size
        ]);
        
        analisisColaboraciones.push([
            'Colaboraciones',
            colaboraciones,
            porcentajeColaboraciones + '%',
            ongsColaboraciones.size
        ]);
        
        analisisColaboraciones.push(['', '', '', '']);
        analisisColaboraciones.push(['TOTAL', total, '100%', ongsPatrocinios.size + ongsColaboraciones.size]);
        
        const ws7 = XLSX.utils.aoa_to_sheet(analisisColaboraciones);
        ws7['!cols'] = [
            { wch: 20 },
            { wch: 15 },
            { wch: 15 },
            { wch: 15 }
        ];
        
        if (!ws7['!merges']) ws7['!merges'] = [];
        ws7['!merges'].push({ s: { r: 0, c: 0 }, e: { r: 0, c: 3 } });
        
        XLSX.utils.book_append_sheet(wb, ws7, 'Análisis Colaboraciones');
        
        // Descargar
        const fecha = new Date().toISOString().split('T')[0];
        XLSX.writeFile(wb, `reporte-empresa-${fecha}.xlsx`);
        
        // Restaurar botón
        boton.disabled = false;
        boton.innerHTML = textoOriginal;
        
    } catch (error) {
        console.error('Error generando Excel:', error);
        alert('Error al generar el Excel: ' + error.message);
        if (boton) {
            boton.disabled = false;
            boton.innerHTML = '<i class="far fa-file-excel mr-2"></i>Descargar Excel';
        }
    } finally {
        generandoExcel = false;
    }
}
} // Cierre del bloque de prevención de duplicación
</script>
@stop

@extends('layouts.adminlte')

@section('page_title', 'Reportes Avanzados')

@section('content_body')
<div class="container-fluid" style="max-width: 1400px; margin: 0 auto; padding: 40px 20px;">
    <!-- Header Minimalista -->
    <div class="mb-5">
        <h1 style="font-size: 2rem; font-weight: 600; color: #0C2B44; margin-bottom: 8px; letter-spacing: -0.5px;">
            Reportes Avanzados
        </h1>
        <p style="color: #6C757D; font-size: 1rem; margin: 0;">Análisis operativo, táctico y estratégico para la toma de decisiones</p>
    </div>

    <!-- KPIs Principales - Diseño Minimalista -->
    <div class="row mb-5" style="gap: 20px 0;">
        <div class="col-lg-3 col-md-6">
            <div class="kpi-card" style="background: #FFFFFF; border: 1px solid #E9ECEF; border-radius: 16px; padding: 28px; transition: all 0.3s ease;">
                <div style="display: flex; align-items: center; margin-bottom: 16px;">
                    <div style="width: 48px; height: 48px; background: #F0F9F5; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 16px;">
                        <i class="fas fa-calendar-alt" style="color: #0C2B44; font-size: 1.25rem;"></i>
                    </div>
                    <div style="flex: 1;">
                        <p style="margin: 0; color: #6C757D; font-size: 0.875rem; font-weight: 500;">Total Eventos</p>
                    </div>
                </div>
                <h2 style="margin: 0; font-size: 2.5rem; font-weight: 700; color: #0C2B44; line-height: 1.2;" id="totalEventosGeneral">0</h2>
                <p style="margin: 8px 0 0 0; color: #6C757D; font-size: 0.813rem;">Eventos regulares + Mega eventos</p>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="kpi-card" style="background: #FFFFFF; border: 1px solid #E9ECEF; border-radius: 16px; padding: 28px; transition: all 0.3s ease;">
                <div style="display: flex; align-items: center; margin-bottom: 16px;">
                    <div style="width: 48px; height: 48px; background: #E8F5E9; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 16px;">
                        <i class="fas fa-check-circle" style="color: #00A36C; font-size: 1.25rem;"></i>
                    </div>
                    <div style="flex: 1;">
                        <p style="margin: 0; color: #6C757D; font-size: 0.875rem; font-weight: 500;">Finalizados</p>
                    </div>
                </div>
                <h2 style="margin: 0; font-size: 2.5rem; font-weight: 700; color: #00A36C; line-height: 1.2;" id="totalFinalizadosGeneral">0</h2>
                <p style="margin: 8px 0 0 0; color: #6C757D; font-size: 0.813rem;">Eventos completados</p>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="kpi-card" style="background: #FFFFFF; border: 1px solid #E9ECEF; border-radius: 16px; padding: 28px; transition: all 0.3s ease;">
                <div style="display: flex; align-items: center; margin-bottom: 16px;">
                    <div style="width: 48px; height: 48px; background: #F0F4F8; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 16px;">
                        <i class="fas fa-users" style="color: #0C2B44; font-size: 1.25rem;"></i>
                    </div>
                    <div style="flex: 1;">
                        <p style="margin: 0; color: #6C757D; font-size: 0.875rem; font-weight: 500;">Participantes</p>
                    </div>
                </div>
                <h2 style="margin: 0; font-size: 2.5rem; font-weight: 700; color: #0C2B44; line-height: 1.2;" id="totalParticipantesGeneral">0</h2>
                <p style="margin: 8px 0 0 0; color: #6C757D; font-size: 0.813rem;">Total de asistentes</p>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="kpi-card" style="background: #FFFFFF; border: 1px solid #E9ECEF; border-radius: 16px; padding: 28px; transition: all 0.3s ease;">
                <div style="display: flex; align-items: center; margin-bottom: 16px;">
                    <div style="width: 48px; height: 48px; background: #FFF4E6; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 16px;">
                        <i class="fas fa-handshake" style="color: #0C2B44; font-size: 1.25rem;"></i>
                    </div>
                    <div style="flex: 1;">
                        <p style="margin: 0; color: #6C757D; font-size: 0.875rem; font-weight: 500;">Patrocinadores</p>
                    </div>
                </div>
                <h2 style="margin: 0; font-size: 2.5rem; font-weight: 700; color: #0C2B44; line-height: 1.2;" id="totalPatrocinadoresGeneral">0</h2>
                <p style="margin: 8px 0 0 0; color: #6C757D; font-size: 0.813rem;">Empresas colaboradoras</p>
            </div>
        </div>
    </div>

    <!-- Resumen Detallado: Eventos Regulares vs Mega Eventos -->
    <div class="row mb-5" style="gap: 20px 0;">
        <div class="col-md-6">
            <div style="background: #FFFFFF; border: 1px solid #E9ECEF; border-radius: 16px; padding: 32px;">
                <div style="display: flex; align-items: center; margin-bottom: 24px;">
                    <div style="width: 40px; height: 40px; background: #F0F4F8; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                        <i class="fas fa-calendar-check" style="color: #0C2B44; font-size: 1.125rem;"></i>
                    </div>
                    <h3 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: #0C2B44;">Eventos Regulares</h3>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
                    <div>
                        <p style="margin: 0 0 4px 0; color: #6C757D; font-size: 0.813rem; font-weight: 500;">Total</p>
                        <h3 style="margin: 0; font-size: 2rem; font-weight: 700; color: #0C2B44;" id="totalEventosRegulares">0</h3>
                    </div>
                    <div>
                        <p style="margin: 0 0 4px 0; color: #6C757D; font-size: 0.813rem; font-weight: 500;">Finalizados</p>
                        <h3 style="margin: 0; font-size: 2rem; font-weight: 700; color: #00A36C;" id="eventosRegularesFinalizadosCount">0</h3>
                    </div>
                </div>

                <div style="border-top: 1px solid #E9ECEF; padding-top: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <span style="color: #495057; font-size: 0.938rem;">
                            <i class="fas fa-users" style="color: #6C757D; margin-right: 8px; font-size: 0.875rem;"></i>Participantes
                        </span>
                        <span style="font-weight: 600; color: #0C2B44; font-size: 1rem;" id="totalParticipantesEventos">0</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <span style="color: #495057; font-size: 0.938rem;">
                            <i class="fas fa-handshake" style="color: #6C757D; margin-right: 8px; font-size: 0.875rem;"></i>Patrocinadores
                        </span>
                        <span style="font-weight: 600; color: #0C2B44; font-size: 1rem;" id="totalPatrocinadoresEventos">0</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <span style="color: #495057; font-size: 0.938rem;">
                            <i class="fas fa-play-circle" style="color: #6C757D; margin-right: 8px; font-size: 0.875rem;"></i>Activos
                        </span>
                        <span style="font-weight: 600; color: #00A36C; font-size: 1rem;" id="eventosRegularesActivos">0</span>
                    </div>
                    <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid #F8F9FA;">
                        <p style="margin: 0; color: #6C757D; font-size: 0.875rem;">
                            Tasa de Finalización: <strong style="color: #0C2B44;" id="tasaFinalizacionEventosTexto">0%</strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div style="background: #FFFFFF; border: 1px solid #E9ECEF; border-radius: 16px; padding: 32px;">
                <div style="display: flex; align-items: center; margin-bottom: 24px;">
                    <div style="width: 40px; height: 40px; background: #F0F9F5; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                        <i class="fas fa-star" style="color: #00A36C; font-size: 1.125rem;"></i>
                    </div>
                    <h3 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: #0C2B44;">Mega Eventos</h3>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
                    <div>
                        <p style="margin: 0 0 4px 0; color: #6C757D; font-size: 0.813rem; font-weight: 500;">Total</p>
                        <h3 style="margin: 0; font-size: 2rem; font-weight: 700; color: #0C2B44;" id="totalMegaEventosCount">0</h3>
                    </div>
                    <div>
                        <p style="margin: 0 0 4px 0; color: #6C757D; font-size: 0.813rem; font-weight: 500;">Finalizados</p>
                        <h3 style="margin: 0; font-size: 2rem; font-weight: 700; color: #00A36C;" id="megaEventosFinalizadosCount">0</h3>
                    </div>
                </div>

                <div style="border-top: 1px solid #E9ECEF; padding-top: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <span style="color: #495057; font-size: 0.938rem;">
                            <i class="fas fa-users" style="color: #6C757D; margin-right: 8px; font-size: 0.875rem;"></i>Participantes
                        </span>
                        <span style="font-weight: 600; color: #0C2B44; font-size: 1rem;" id="totalParticipantesMega">0</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <span style="color: #495057; font-size: 0.938rem;">
                            <i class="fas fa-handshake" style="color: #6C757D; margin-right: 8px; font-size: 0.875rem;"></i>Patrocinadores
                        </span>
                        <span style="font-weight: 600; color: #0C2B44; font-size: 1rem;" id="totalPatrocinadoresMega">0</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <span style="color: #495057; font-size: 0.938rem;">
                            <i class="fas fa-play-circle" style="color: #6C757D; margin-right: 8px; font-size: 0.875rem;"></i>Activos
                        </span>
                        <span style="font-weight: 600; color: #00A36C; font-size: 1rem;" id="megaEventosActivos">0</span>
                    </div>
                    <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid #F8F9FA;">
                        <p style="margin: 0; color: #6C757D; font-size: 0.875rem;">
                            Tasa de Finalización: <strong style="color: #0C2B44;" id="tasaFinalizacionMegaTexto">0%</strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tasa de Finalización Consolidada -->
    <div class="row mb-5" style="gap: 20px 0;">
        <div class="col-md-8">
            <div style="background: #FFFFFF; border: 1px solid #E9ECEF; border-radius: 16px; padding: 32px;">
                <h3 style="margin: 0 0 24px 0; font-size: 1.25rem; font-weight: 600; color: #0C2B44;">
                    Tasa de Finalización (Consolidada)
                </h3>
                
                <div style="margin-bottom: 32px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <span style="color: #495057; font-size: 0.938rem; font-weight: 500;">Progreso General</span>
                        <span style="font-size: 1.5rem; font-weight: 700; color: #00A36C;" id="tasaFinalizacionValor">0%</span>
                    </div>
                    <div style="height: 12px; background: #F8F9FA; border-radius: 8px; overflow: hidden;">
                        <div id="progressBarFinalizacion" 
                             style="height: 100%; background: #00A36C; width: 0%; transition: width 0.5s ease; border-radius: 8px;"
                             role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                </div>
                
                <!-- Desglose por Tipo -->
                <div style="border-top: 1px solid #E9ECEF; padding-top: 24px;">
                    <h4 style="margin: 0 0 20px 0; font-size: 1rem; font-weight: 600; color: #0C2B44;">Desglose por Tipo</h4>
                    
                    <!-- Eventos Regulares -->
                    <div style="margin-bottom: 20px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <span style="color: #495057; font-size: 0.938rem; font-weight: 500;">
                                <i class="fas fa-calendar" style="color: #0C2B44; margin-right: 8px; font-size: 0.875rem;"></i>Eventos Regulares
                            </span>
                            <span style="font-weight: 600; color: #0C2B44; font-size: 1rem;" id="eventosRegularesFinalizados">0</span>
                        </div>
                        <div style="height: 8px; background: #F8F9FA; border-radius: 6px; overflow: hidden; margin-bottom: 6px;">
                            <div id="progressBarEventosRegulares" 
                                 style="height: 100%; background: #0C2B44; width: 0%; transition: width 0.5s ease; border-radius: 6px;"
                                 role="progressbar">
                            </div>
                        </div>
                        <p style="margin: 0; color: #6C757D; font-size: 0.813rem;" id="detalleEventosRegulares">Total: 0 | Finalizados: 0</p>
                    </div>
                    
                    <!-- Mega Eventos -->
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <span style="color: #495057; font-size: 0.938rem; font-weight: 500;">
                                <i class="fas fa-star" style="color: #00A36C; margin-right: 8px; font-size: 0.875rem;"></i>Mega Eventos
                            </span>
                            <span style="font-weight: 600; color: #00A36C; font-size: 1rem;" id="megaEventosFinalizados">0</span>
                        </div>
                        <div style="height: 8px; background: #F8F9FA; border-radius: 6px; overflow: hidden; margin-bottom: 6px;">
                            <div id="progressBarMegaEventos" 
                                 style="height: 100%; background: #00A36C; width: 0%; transition: width 0.5s ease; border-radius: 6px;"
                                 role="progressbar">
                            </div>
                        </div>
                        <p style="margin: 0; color: #6C757D; font-size: 0.813rem;" id="detalleMegaEventos">Total: 0 | Finalizados: 0</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div style="background: #FFFFFF; border: 1px solid #E9ECEF; border-radius: 16px; padding: 32px;">
                <h3 style="margin: 0 0 24px 0; font-size: 1.25rem; font-weight: 600; color: #0C2B44;">
                    Análisis Comparativo
                </h3>
                <div style="text-align: center; margin-bottom: 20px;">
                    <p style="margin: 0 0 4px 0; color: #6C757D; font-size: 0.813rem; font-weight: 500;">Últimos 6 meses</p>
                    <h3 style="margin: 0; font-size: 2rem; font-weight: 700; color: #00A36C;" id="eventosUltimos6Meses">0</h3>
                </div>
                <div style="text-align: center; margin-bottom: 20px;">
                    <p style="margin: 0 0 4px 0; color: #6C757D; font-size: 0.813rem; font-weight: 500;">6 meses anteriores</p>
                    <h3 style="margin: 0; font-size: 2rem; font-weight: 700; color: #6C757D;" id="eventos6MesesAnteriores">0</h3>
                </div>
                <div style="border-top: 1px solid #E9ECEF; padding-top: 20px; text-align: center;">
                    <p style="margin: 0 0 8px 0; color: #6C757D; font-size: 0.813rem; font-weight: 500;">Crecimiento</p>
                    <h4 style="margin: 0; font-size: 1.5rem; font-weight: 700;" id="crecimientoPorcentual">0%</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficas de Análisis -->
    <div class="row mb-5" style="gap: 20px 0;">
        <!-- Gráfico de Dona: Distribución por Categoría -->
        <div class="col-lg-6">
            <div style="background: #FFFFFF; border: 1px solid #E9ECEF; border-radius: 16px; padding: 32px;">
                <h3 style="margin: 0 0 24px 0; font-size: 1.25rem; font-weight: 600; color: #0C2B44;">
                    Distribución por Categoría
                </h3>
                <div style="position: relative; height: 300px;">
                    <canvas id="chartCategorias"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfico de Barras: Comparación Eventos Regulares vs Mega Eventos -->
        <div class="col-lg-6">
            <div style="background: #FFFFFF; border: 1px solid #E9ECEF; border-radius: 16px; padding: 32px;">
                <h3 style="margin: 0 0 24px 0; font-size: 1.25rem; font-weight: 600; color: #0C2B44;">
                    Comparación de Eventos
                </h3>
                <div style="position: relative; height: 300px;">
                    <canvas id="chartComparacionEventos"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de Líneas: Tendencias Temporales -->
    <div class="row mb-5">
        <div class="col-12">
            <div style="background: #FFFFFF; border: 1px solid #E9ECEF; border-radius: 16px; padding: 32px;">
                <h3 style="margin: 0 0 24px 0; font-size: 1.25rem; font-weight: 600; color: #0C2B44;">
                    Tendencias Temporales (Últimos 6 Meses)
                </h3>
                <div style="position: relative; height: 350px;">
                    <canvas id="chartTendencias"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de Barras Horizontales: Top Categorías -->
    <div class="row mb-5">
        <div class="col-12">
            <div style="background: #FFFFFF; border: 1px solid #E9ECEF; border-radius: 16px; padding: 32px;">
                <h3 style="margin: 0 0 24px 0; font-size: 1.25rem; font-weight: 600; color: #0C2B44;">
                    Top Categorías por Participantes
                </h3>
                <div style="position: relative; height: 300px;">
                    <canvas id="chartTopCategorias"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Navegación de Reportes -->
    <div class="mb-4">
        <h3 style="margin: 0 0 24px 0; font-size: 1.5rem; font-weight: 600; color: #0C2B44;">Reportes Disponibles</h3>
    </div>
    
    <div class="row" style="gap: 20px 0;">
        @php
            $reportes = [
                ['icon' => 'chart-pie', 'title' => 'Resumen Ejecutivo', 'desc' => 'Totales generales, KPIs principales y gráfico de torta por categorías', 'route' => 'ong.reportes.resumen-ejecutivo', 'badges' => ['PDF', 'Excel']],
                ['icon' => 'chart-line', 'title' => 'Análisis Temporal', 'desc' => 'Gráfico de líneas de eventos por mes con comparativa año anterior', 'route' => 'ong.reportes.analisis-temporal', 'badges' => ['PDF', 'Excel', 'CSV']],
                ['icon' => 'users', 'title' => 'Participación y Colaboración', 'desc' => 'Top empresas patrocinadoras, voluntarios más activos y eventos con más colaboradores', 'route' => 'ong.reportes.participacion-colaboracion', 'badges' => ['PDF', 'Excel']],
                ['icon' => 'map-marker-alt', 'title' => 'Análisis Geográfico', 'desc' => 'Tabla de ciudades con más eventos y distribución por departamentos', 'route' => 'ong.reportes.analisis-geografico', 'badges' => ['PDF', 'Excel']],
                ['icon' => 'trophy', 'title' => 'Rendimiento por ONG', 'desc' => 'Ranking de ONGs por eventos creados, tasas de finalización y promedio de asistentes', 'route' => 'ong.reportes.rendimiento-ong', 'badges' => ['PDF', 'Excel', 'JSON']],
            ];
        @endphp
        
        @foreach($reportes as $reporte)
        <div class="col-lg-4 col-md-6">
            <div class="reporte-card" 
                 onclick="window.location.href='{{ route($reporte['route']) }}'"
                 style="background: #FFFFFF; border: 1px solid #E9ECEF; border-radius: 16px; padding: 24px; cursor: pointer; transition: all 0.3s ease; height: 100%;">
                <div style="display: flex; align-items: center; margin-bottom: 16px;">
                    <div style="width: 48px; height: 48px; background: #F0F9F5; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 16px;">
                        <i class="fas fa-{{ $reporte['icon'] }}" style="color: #00A36C; font-size: 1.25rem;"></i>
                    </div>
                    <h4 style="margin: 0; font-size: 1.125rem; font-weight: 600; color: #0C2B44;">{{ $reporte['title'] }}</h4>
                </div>
                <p style="margin: 0 0 16px 0; color: #6C757D; font-size: 0.875rem; line-height: 1.5;">{{ $reporte['desc'] }}</p>
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                    @foreach($reporte['badges'] as $badge)
                    <span style="display: inline-block; padding: 4px 12px; background: #F8F9FA; color: #6C757D; border-radius: 6px; font-size: 0.75rem; font-weight: 500;">{{ $badge }}</span>
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

@push('js')
<script src="{{ asset('assets/js/config.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Variables globales para los gráficos
let chartCategorias = null;
let chartComparacionEventos = null;
let chartTendencias = null;
let chartTopCategorias = null;
document.addEventListener('DOMContentLoaded', async function() {
    const token = localStorage.getItem('token');
    const tipoUsuario = localStorage.getItem('tipo_usuario');
    const ongId = parseInt(localStorage.getItem('id_entidad') || localStorage.getItem('id_usuario'), 10);

    // Validar autenticación en el frontend
    if (!token || tipoUsuario !== 'ONG' || isNaN(ongId) || ongId <= 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Acceso denegado',
            text: 'Debes iniciar sesión como ONG para acceder a los reportes.',
            confirmButtonText: 'Ir al Login'
        }).then(() => {
            window.location.href = '/login';
        });
        return;
    }

    try {
        // Cargar KPIs destacados desde la API de reportes
        const response = await fetch(`${API_BASE_URL}/api/reportes-ong/kpis-destacados`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) {
            // Si falla, intentar con el endpoint de dashboard general
            const fallbackResponse = await fetch(`${API_BASE_URL}/api/dashboard-ong/estadisticas-generales`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (fallbackResponse.ok) {
                const fallbackData = await fallbackResponse.json();
                if (fallbackData.success && fallbackData.estadisticas) {
                    const stats = fallbackData.estadisticas;
                    updateKPIsFromDashboard(stats);
                }
            }
            return;
        }

        const data = await response.json();
        
        if (data.success && data.kpis) {
            const kpis = data.kpis;
            updateKPIs(kpis);
        }
        
    } catch (error) {
        console.error('Error cargando KPIs:', error);
    }
});

function updateKPIs(kpis) {
    console.log('Actualizando KPIs:', kpis);
    
    // ========== ACTUALIZAR KPIs PRINCIPALES (CONSOLIDADOS) ==========
    const totalEventosGeneral = kpis.total_eventos_general || 0;
    document.getElementById('totalEventosGeneral').textContent = totalEventosGeneral;

    const totalFinalizadosGeneral = kpis.total_finalizados_general || 0;
    document.getElementById('totalFinalizadosGeneral').textContent = totalFinalizadosGeneral;

    const totalParticipantesGeneral = kpis.total_participantes || 0;
    document.getElementById('totalParticipantesGeneral').textContent = totalParticipantesGeneral;

    const totalPatrocinadoresGeneral = kpis.total_patrocinadores || 0;
    document.getElementById('totalPatrocinadoresGeneral').textContent = totalPatrocinadoresGeneral;

    // ========== ACTUALIZAR DATOS DE EVENTOS REGULARES ==========
    const totalEventos = kpis.total_eventos || 0;
    const eventosFinalizados = kpis.eventos_finalizados || 0;
    const eventosActivos = kpis.eventos_activos || 0;
    const totalParticipantesEventos = kpis.total_participantes_eventos || 0;
    const totalPatrocinadoresEventos = kpis.total_patrocinadores_eventos || 0;
    const tasaFinalizacionEventos = kpis.tasa_finalizacion_eventos || 0;

    const totalEventosRegularesEl = document.getElementById('totalEventosRegulares');
    if (totalEventosRegularesEl) totalEventosRegularesEl.textContent = totalEventos;
    
    const eventosRegularesFinalizadosEl = document.getElementById('eventosRegularesFinalizadosCount');
    if (eventosRegularesFinalizadosEl) eventosRegularesFinalizadosEl.textContent = eventosFinalizados;
    
    const totalParticipantesEventosEl = document.getElementById('totalParticipantesEventos');
    if (totalParticipantesEventosEl) totalParticipantesEventosEl.textContent = totalParticipantesEventos;
    
    const totalPatrocinadoresEventosEl = document.getElementById('totalPatrocinadoresEventos');
    if (totalPatrocinadoresEventosEl) totalPatrocinadoresEventosEl.textContent = totalPatrocinadoresEventos;
    
    const eventosRegularesActivosEl = document.getElementById('eventosRegularesActivos');
    if (eventosRegularesActivosEl) eventosRegularesActivosEl.textContent = eventosActivos;
    
    const tasaFinalizacionEventosTextoEl = document.getElementById('tasaFinalizacionEventosTexto');
    if (tasaFinalizacionEventosTextoEl) tasaFinalizacionEventosTextoEl.textContent = tasaFinalizacionEventos.toFixed(2) + '%';

    // ========== ACTUALIZAR DATOS DE MEGA EVENTOS ==========
    const totalMegaEventos = kpis.total_mega_eventos || 0;
    const megaEventosFinalizados = kpis.mega_eventos_finalizados || 0;
    const megaEventosActivos = kpis.mega_eventos_activos || 0;
    const totalParticipantesMega = kpis.total_participantes_mega || 0;
    const totalPatrocinadoresMega = kpis.total_patrocinadores_mega || 0;
    const tasaFinalizacionMega = kpis.tasa_finalizacion_mega || 0;

    const totalMegaEventosCountEl = document.getElementById('totalMegaEventosCount');
    if (totalMegaEventosCountEl) totalMegaEventosCountEl.textContent = totalMegaEventos;
    
    const megaEventosFinalizadosCountEl = document.getElementById('megaEventosFinalizadosCount');
    if (megaEventosFinalizadosCountEl) megaEventosFinalizadosCountEl.textContent = megaEventosFinalizados;
    
    const totalParticipantesMegaEl = document.getElementById('totalParticipantesMega');
    if (totalParticipantesMegaEl) totalParticipantesMegaEl.textContent = totalParticipantesMega;
    
    const totalPatrocinadoresMegaEl = document.getElementById('totalPatrocinadoresMega');
    if (totalPatrocinadoresMegaEl) totalPatrocinadoresMegaEl.textContent = totalPatrocinadoresMega;
    
    const megaEventosActivosEl = document.getElementById('megaEventosActivos');
    if (megaEventosActivosEl) megaEventosActivosEl.textContent = megaEventosActivos;
    
    const tasaFinalizacionMegaTextoEl = document.getElementById('tasaFinalizacionMegaTexto');
    if (tasaFinalizacionMegaTextoEl) tasaFinalizacionMegaTextoEl.textContent = tasaFinalizacionMega.toFixed(2) + '%';

    // ========== ACTUALIZAR TASA DE FINALIZACIÓN CON DETALLES ==========
    const tasaFinalizacion = kpis.tasa_finalizacion || 0;
    const progressBarFinalizacion = document.getElementById('progressBarFinalizacion');
    const tasaFinalizacionValor = document.getElementById('tasaFinalizacionValor');
    
    if (progressBarFinalizacion) {
        progressBarFinalizacion.style.width = tasaFinalizacion + '%';
        progressBarFinalizacion.setAttribute('aria-valuenow', tasaFinalizacion);
    }
    
    if (tasaFinalizacionValor) {
        tasaFinalizacionValor.textContent = tasaFinalizacion.toFixed(2) + '%';
    }

    // Detalles de tasa de finalización
    if (kpis.detalle_tasa_finalizacion) {
        const detalle = kpis.detalle_tasa_finalizacion;
        
        // Eventos Regulares
        if (detalle.eventos_regulares) {
            const eventosReg = detalle.eventos_regulares;
            document.getElementById('eventosRegularesFinalizados').textContent = eventosReg.finalizados || 0;
            
            const progressBarEventosReg = document.getElementById('progressBarEventosRegulares');
            const detalleEventosReg = document.getElementById('detalleEventosRegulares');
            
            if (progressBarEventosReg) {
                progressBarEventosReg.style.width = eventosReg.tasa + '%';
            }
            if (detalleEventosReg) {
                detalleEventosReg.textContent = `Total: ${eventosReg.total} | Finalizados: ${eventosReg.finalizados} (${eventosReg.porcentaje.toFixed(1)}% del total general)`;
            }
        }
        
        // Mega Eventos
        if (detalle.mega_eventos) {
            const megaEventos = detalle.mega_eventos;
            document.getElementById('megaEventosFinalizados').textContent = megaEventos.finalizados || 0;
            
            const progressBarMega = document.getElementById('progressBarMegaEventos');
            const detalleMega = document.getElementById('detalleMegaEventos');
            
            if (progressBarMega) {
                progressBarMega.style.width = megaEventos.tasa + '%';
            }
            if (detalleMega) {
                detalleMega.textContent = `Total: ${megaEventos.total} | Finalizados: ${megaEventos.finalizados} (${megaEventos.porcentaje.toFixed(1)}% del total general)`;
            }
        }
    }

    // Actualizar análisis comparativo
    const eventosUltimos6Meses = kpis.eventos_ultimos_6_meses || 0;
    const eventos6MesesAnteriores = kpis.eventos_6_meses_anteriores || 0;
    const crecimientoPorcentual = kpis.crecimiento_porcentual || 0;

    const eventosUltimos6MesesEl = document.getElementById('eventosUltimos6Meses');
    if (eventosUltimos6MesesEl) eventosUltimos6MesesEl.textContent = eventosUltimos6Meses;

    const eventos6MesesAnterioresEl = document.getElementById('eventos6MesesAnteriores');
    if (eventos6MesesAnterioresEl) eventos6MesesAnterioresEl.textContent = eventos6MesesAnteriores;

    const crecimientoPorcentualEl = document.getElementById('crecimientoPorcentual');
    if (crecimientoPorcentualEl) {
        const isPositive = crecimientoPorcentual >= 0;
        crecimientoPorcentualEl.style.color = isPositive ? '#00A36C' : '#dc3545';
        crecimientoPorcentualEl.innerHTML = `<i class="fas fa-arrow-${isPositive ? 'up' : 'down'}" style="margin-right: 8px;"></i>${isPositive ? '+' : ''}${crecimientoPorcentual.toFixed(2)}%`;
    }

    // Actualizar gráficas
    actualizarGraficas(kpis);
}

function actualizarGraficas(kpis) {
    // Verificar que Chart.js esté disponible
    if (typeof Chart === 'undefined') {
        console.warn('Chart.js no está disponible');
        return;
    }

    // Configuración global de Chart.js
    Chart.defaults.font.family = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#6C757D';
    Chart.defaults.plugins.legend.labels.padding = 15;
    Chart.defaults.plugins.legend.labels.usePointStyle = true;

    // 1. Gráfico de Dona: Distribución por Categoría
    const ctxCategorias = document.getElementById('chartCategorias');
    if (ctxCategorias) {
        if (chartCategorias) chartCategorias.destroy();
        
        const distribucion = kpis.distribucion_categoria || {};
        const categorias = Object.keys(distribucion).map(c => c.charAt(0).toUpperCase() + c.slice(1));
        const valores = Object.values(distribucion);
        
        // Colores para las categorías
        const colores = [
            '#00A36C', '#0C2B44', '#667eea', '#f5576c', 
            '#f093fb', '#4facfe', '#43e97b', '#fa709a'
        ];
        
        chartCategorias = new Chart(ctxCategorias, {
            type: 'doughnut',
            data: {
                labels: categorias,
                datasets: [{
                    data: valores,
                    backgroundColor: colores.slice(0, categorias.length),
                    borderWidth: 0,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: { size: 12, weight: '500' },
                            color: '#0C2B44'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: { size: 13, weight: '600' },
                        bodyFont: { size: 12 },
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const porcentaje = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                return `${context.label}: ${context.parsed} (${porcentaje}%)`;
                            }
                        }
                    }
                },
                cutout: '70%'
            }
        });
    }

    // 2. Gráfico de Barras: Comparación Eventos Regulares vs Mega Eventos
    const ctxComparacion = document.getElementById('chartComparacionEventos');
    if (ctxComparacion) {
        if (chartComparacionEventos) chartComparacionEventos.destroy();
        
        chartComparacionEventos = new Chart(ctxComparacion, {
            type: 'bar',
            data: {
                labels: ['Total', 'Finalizados', 'Activos', 'Participantes', 'Patrocinadores'],
                datasets: [
                    {
                        label: 'Eventos Regulares',
                        data: [
                            kpis.total_eventos || 0,
                            kpis.eventos_finalizados || 0,
                            kpis.eventos_activos || 0,
                            kpis.total_participantes_eventos || 0,
                            kpis.total_patrocinadores_eventos || 0
                        ],
                        backgroundColor: '#0C2B44',
                        borderRadius: 8,
                        borderSkipped: false
                    },
                    {
                        label: 'Mega Eventos',
                        data: [
                            kpis.total_mega_eventos || 0,
                            kpis.mega_eventos_finalizados || 0,
                            kpis.mega_eventos_activos || 0,
                            kpis.total_participantes_mega || 0,
                            kpis.total_patrocinadores_mega || 0
                        ],
                        backgroundColor: '#00A36C',
                        borderRadius: 8,
                        borderSkipped: false
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            padding: 15,
                            font: { size: 12, weight: '500' },
                            color: '#0C2B44'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: { size: 13, weight: '600' },
                        bodyFont: { size: 12 },
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += context.parsed.y.toLocaleString();
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: '#6C757D',
                            font: { size: 11, weight: '500' }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#6C757D',
                            font: { size: 11 }
                        }
                    }
                }
            }
        });
    }

    // 3. Gráfico de Líneas: Tendencias Temporales
    const ctxTendencias = document.getElementById('chartTendencias');
    if (ctxTendencias) {
        if (chartTendencias) chartTendencias.destroy();
        
        // Generar labels de los últimos 6 meses
        const meses = [];
        const ahora = new Date();
        for (let i = 5; i >= 0; i--) {
            const fecha = new Date(ahora.getFullYear(), ahora.getMonth() - i, 1);
            meses.push(fecha.toLocaleDateString('es-ES', { month: 'short', year: 'numeric' }));
        }
        
        // Datos simulados (en producción, estos vendrían del backend)
        const datosEventos = [
            kpis.eventos_ultimos_6_meses || 0,
            Math.round((kpis.eventos_ultimos_6_meses || 0) * 0.9),
            Math.round((kpis.eventos_ultimos_6_meses || 0) * 0.85),
            Math.round((kpis.eventos_ultimos_6_meses || 0) * 0.8),
            Math.round((kpis.eventos_ultimos_6_meses || 0) * 0.75),
            kpis.eventos_6_meses_anteriores || 0
        ];
        
        chartTendencias = new Chart(ctxTendencias, {
            type: 'line',
            data: {
                labels: meses,
                datasets: [
                    {
                        label: 'Eventos Creados',
                        data: datosEventos,
                        borderColor: '#00A36C',
                        backgroundColor: 'rgba(0, 163, 108, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5,
                        pointBackgroundColor: '#00A36C',
                        pointBorderColor: '#FFFFFF',
                        pointBorderWidth: 2,
                        pointHoverRadius: 7,
                        pointHoverBackgroundColor: '#00A36C',
                        pointHoverBorderColor: '#FFFFFF',
                        pointHoverBorderWidth: 3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: { size: 13, weight: '600' },
                        bodyFont: { size: 12 },
                        intersect: false,
                        mode: 'index'
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: '#6C757D',
                            font: { size: 11, weight: '500' }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#6C757D',
                            font: { size: 11 },
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    // 4. Gráfico de Barras Horizontales: Top Categorías
    const ctxTopCategorias = document.getElementById('chartTopCategorias');
    if (ctxTopCategorias) {
        if (chartTopCategorias) chartTopCategorias.destroy();
        
        const distribucion = kpis.distribucion_categoria || {};
        const categorias = Object.keys(distribucion).map(c => c.charAt(0).toUpperCase() + c.slice(1));
        const valores = Object.values(distribucion);
        
        // Ordenar por valor descendente y tomar top 5
        const indices = valores.map((v, i) => ({ valor: v, indice: i }))
            .sort((a, b) => b.valor - a.valor)
            .slice(0, 5);
        
        const topCategorias = indices.map(item => categorias[item.indice]);
        const topValores = indices.map(item => item.valor);
        
        chartTopCategorias = new Chart(ctxTopCategorias, {
            type: 'bar',
            data: {
                labels: topCategorias,
                datasets: [{
                    label: 'Eventos',
                    data: topValores,
                    backgroundColor: '#00A36C',
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: { size: 13, weight: '600' },
                        bodyFont: { size: 12 }
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
                            color: '#6C757D',
                            font: { size: 11 },
                            stepSize: 1
                        }
                    },
                    y: {
                        grid: { display: false },
                        ticks: {
                            color: '#0C2B44',
                            font: { size: 12, weight: '500' }
                        }
                    }
                }
            }
        });
    }
}

function updateKPIsFromDashboard(stats) {
    if (stats.mega_eventos) {
        const totalMega = stats.mega_eventos.total || 0;
        const elements = document.querySelectorAll('.kpi-card h2');
        if (elements.length > 0) {
            elements[0].textContent = totalMega;
        }
    }
}
</script>
@endpush

@push('css')
<style>
    body {
        background-color: #F8F9FA;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    }
    
    .kpi-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        border-color: #00A36C;
    }
    
    .reporte-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        border-color: #00A36C;
    }
    
    @media (max-width: 768px) {
        .container-fluid {
            padding: 20px 16px !important;
        }
        
        h1 {
            font-size: 1.5rem !important;
        }
        
        .kpi-card,
        .reporte-card {
            padding: 20px !important;
        }
    }
</style>
@endpush

@extends('layouts.adminlte')

@section('page_title', 'Seguimiento de Mega Evento')

@section('content_body')
<div class="container-fluid">
    <!-- Loading State -->
    <div id="loadingMessage" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Cargando...</span>
        </div>
        <p class="mt-3 text-muted">Cargando información de seguimiento...</p>
    </div>

    <div id="seguimientoContent" style="display: none; animation: fadeIn 0.5s ease-in;">
        <!-- Header con información del mega evento -->
        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap" style="gap: 0.75rem;">
                <div class="flex-grow-1">
                    <a href="#" id="volverLink" class="btn btn-outline-secondary mb-3" style="border-radius: 12px; padding: 0.6rem 1.5rem; font-weight: 500; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(0,0,0,0.08);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.12)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.08)'">
                        <i class="fas fa-arrow-left mr-2"></i> Volver
                    </a>
                    <h2 class="mb-2" id="tituloMegaEvento" style="font-weight: 700; color: var(--primary-color); font-size: 2rem; animation: fadeInUp 0.5s ease-out;">
                        -
                    </h2>
                    <p class="mb-0" id="fechasMegaEvento" style="font-size: 0.95rem; color: var(--text-muted); animation: fadeInUp 0.6s ease-out;">-</p>
                </div>
                <div>
                    <button id="btnExportarReporte" class="btn btn-primary" style="border-radius: 12px; padding: 0.6rem 1.5rem; font-weight: 500; transition: all 0.3s ease; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); border: none; box-shadow: 0 2px 8px rgba(12,43,68,0.2);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(12,43,68,0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(12,43,68,0.2)'">
                        <i class="fas fa-download mr-2"></i> Exportar Reporte
                    </button>
                </div>
            </div>
        </div>

        <!-- Panel de Métricas Principales -->
        <div class="row mb-4">
            <!-- Total Participantes -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm stat-card-modern" style="border-radius: 16px; overflow: hidden; animation: fadeInUp 0.5s ease-out;">
                    <div class="card-body p-4" style="background: linear-gradient(135deg, #0C2B44 0%, #094166 100%);">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <p class="text-white mb-1" style="opacity: 0.9; font-size: 0.85rem; font-weight: 500;">Total Participantes</p>
                                <h3 id="totalParticipantes" class="text-white mb-0" style="font-size: 2.2rem; font-weight: 700;">0</h3>
                            </div>
                            <div class="bg-white rounded-circle p-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; opacity: 0.9;">
                                <i class="fas fa-users" style="font-size: 1.3rem; color: #0C2B44;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Participantes Aprobados -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm stat-card-modern" style="border-radius: 16px; overflow: hidden; animation: fadeInUp 0.6s ease-out;">
                    <div class="card-body p-4" style="background: linear-gradient(135deg, #00A36C 0%, #008557 100%);">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <p class="text-white mb-1" style="opacity: 0.9; font-size: 0.85rem; font-weight: 500;">Aprobados</p>
                                <h3 id="participantesAprobados" class="text-white mb-0" style="font-size: 2.2rem; font-weight: 700;">0</h3>
                            </div>
                            <div class="bg-white rounded-circle p-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; opacity: 0.9;">
                                <i class="fas fa-check-circle" style="font-size: 1.3rem; color: #00A36C;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tasa de Confirmación -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm stat-card-modern" style="border-radius: 16px; overflow: hidden; animation: fadeInUp 0.7s ease-out;">
                    <div class="card-body p-4" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%);">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <p class="text-white mb-1" style="opacity: 0.9; font-size: 0.85rem; font-weight: 500;">Tasa Confirmación</p>
                                <h3 id="tasaConfirmacion" class="text-white mb-0" style="font-size: 2.2rem; font-weight: 700;">0%</h3>
                            </div>
                            <div class="bg-white rounded-circle p-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; opacity: 0.9;">
                                <i class="fas fa-percentage" style="font-size: 1.3rem; color: #0C2B44;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Capacidad -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm stat-card-modern" style="border-radius: 16px; overflow: hidden; animation: fadeInUp 0.8s ease-out;">
                    <div class="card-body p-4" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%);">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <p class="text-white mb-1" style="opacity: 0.9; font-size: 0.85rem; font-weight: 500;">Capacidad</p>
                                <h3 id="porcentajeCapacidad" class="text-white mb-0" style="font-size: 2.2rem; font-weight: 700;">-</h3>
                            </div>
                            <div class="bg-white rounded-circle p-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; opacity: 0.9;">
                                <i class="fas fa-chart-pie" style="font-size: 1.3rem; color: #00A36C;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel de Alertas -->
        <div id="panelAlertas" class="card border-0 shadow-sm mb-4" style="border-radius: 16px; animation: fadeInUp 0.5s ease-out; display: none;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-4">
                    <div class="section-icon mr-3">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div>
                        <h5 class="mb-0" style="font-weight: 700; color: #0C2B44; font-size: 1.1rem;">
                            Alertas y Notificaciones
                        </h5>
                        <p class="mb-0 text-muted" style="font-size: 0.85rem; margin-top: 0.25rem;">
                            Información importante sobre el evento
                        </p>
                    </div>
                </div>
                <div id="alertasContainer">
                    <!-- Las alertas se cargarán aquí dinámicamente -->
                </div>
            </div>
        </div>

        <!-- Métricas de Interacción: Reacciones, Compartidos, Participaciones -->
        <div class="row mb-4">
            <!-- Total Reacciones -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card border-0 shadow-sm" style="border-radius: 16px; animation: fadeInUp 0.6s ease-out;">
                    <div class="card-body p-4">
                        <div class="info-item" style="padding: 0; margin: 0;">
                            <div class="info-icon" style="width: 48px; height: 48px; background: rgba(0, 163, 108, 0.1); border-radius: 12px; color: #00A36C;">
                                <i class="fas fa-heart"></i>
                            </div>
                            <div class="info-content">
                                <h6 class="info-label">Total Reacciones</h6>
                                <p class="info-value mb-0" id="totalReacciones" style="font-size: 1.5rem; color: #0C2B44;">0</p>
                                <small class="text-muted">Me gusta recibidos</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Compartidos -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card border-0 shadow-sm" style="border-radius: 16px; animation: fadeInUp 0.7s ease-out;">
                    <div class="card-body p-4">
                        <div class="info-item" style="padding: 0; margin: 0;">
                            <div class="info-icon" style="width: 48px; height: 48px; background: rgba(12, 43, 68, 0.1); border-radius: 12px; color: #0C2B44;">
                                <i class="fas fa-share-alt"></i>
                            </div>
                            <div class="info-content">
                                <h6 class="info-label">Total Compartidos</h6>
                                <p class="info-value mb-0" id="totalCompartidos" style="font-size: 1.5rem; color: #0C2B44;">0</p>
                                <small class="text-muted">Veces compartido</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Participaciones -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card border-0 shadow-sm" style="border-radius: 16px; animation: fadeInUp 0.8s ease-out;">
                    <div class="card-body p-4">
                        <div class="info-item" style="padding: 0; margin: 0;">
                            <div class="info-icon" style="width: 48px; height: 48px; background: rgba(0, 163, 108, 0.1); border-radius: 12px; color: #00A36C;">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <div class="info-content">
                                <h6 class="info-label">Total Participaciones</h6>
                                <p class="info-value mb-0" id="totalParticipaciones" style="font-size: 1.5rem; color: #0C2B44;">0</p>
                                <small class="text-muted" id="detalleParticipaciones">0 registrados, 0 no registrados</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Seguimiento por Tipo de Actor -->
        <div class="row mb-4">
            <!-- ONG Organizadora -->
            <div class="col-lg-12 col-md-12 mb-4">
                <div class="card border-0 shadow-sm" style="border-radius: 16px; animation: fadeInUp 0.6s ease-out;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="section-icon mr-3">
                                <i class="fas fa-flag"></i>
                            </div>
                            <div>
                                <h5 class="mb-0" style="font-weight: 700; color: #0C2B44; font-size: 1.1rem;">
                                    ONG Organizadora
                                </h5>
                                <p class="mb-0 text-muted" style="font-size: 0.85rem; margin-top: 0.25rem;">
                                    Cumplimiento de tareas y responsabilidades
                                </p>
                            </div>
                        </div>
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="info-label">Cumplimiento de tareas</span>
                                <strong id="porcentajeCumplimientoOng" class="info-value" style="color: #00A36C;">0%</strong>
                            </div>
                            <div class="progress" style="height: 8px; border-radius: 4px; background: #f0f0f0;">
                                <div id="barraCumplimientoOng" class="progress-bar" role="progressbar" style="width: 0%; background: linear-gradient(90deg, #0C2B44 0%, #00A36C 100%); transition: width 0.3s ease;"></div>
                            </div>
                        </div>
                        <div id="tareasOngContainer" class="row">
                            <!-- Tareas se cargarán dinámicamente -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficas -->
        <div class="row mb-5">
            <!-- Gráfica de Reacciones por Día -->
            <div class="col-lg-6 col-md-12 mb-4">
                <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                    <div class="card-header bg-white border-0 pt-4 pb-3 px-4">
                        <h3 class="card-title mb-0" style="font-size: 1.1rem; font-weight: 600; color: #333333;">
                            <i class="fas fa-heart mr-2" style="color: #00A36C;"></i>Reacciones por Día
                        </h3>
                        <small class="text-muted" style="font-size: 0.85rem;">Últimos 30 días</small>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div style="height: 300px; position: relative;">
                            <canvas id="graficaInscripciones"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráfica de Estado de Participantes -->
            <div class="col-lg-6 col-md-12 mb-4">
                <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                    <div class="card-header bg-white border-0 pt-4 pb-3 px-4">
                        <h3 class="card-title mb-0" style="font-size: 1.1rem; font-weight: 600; color: #333333;">
                            <i class="fas fa-chart-pie mr-2" style="color: #0C2B44;"></i>Estado de Participantes
                        </h3>
                        <small class="text-muted" style="font-size: 0.85rem;">Distribución actual</small>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div style="height: 300px; position: relative;">
                            <canvas id="graficaEstados"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Participantes -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; animation: fadeInUp 0.7s ease-out;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-4">
                    <div class="section-icon mr-3">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="mb-0" style="font-weight: 700; color: #0C2B44; font-size: 1.1rem;">
                            Seguimiento de Inscripciones
                        </h5>
                        <p class="mb-0 text-muted" style="font-size: 0.85rem; margin-top: 0.25rem;">
                            Lista de participantes registrados
                        </p>
                    </div>
                    <div class="d-flex align-items-center" style="gap: 0.5rem;">
                        <select id="filtroEstadoParticipante" class="form-control" style="border-radius: 8px; padding: 0.5rem 0.75rem; font-size: 0.9rem; border: 1px solid rgba(12, 43, 68, 0.2);">
                            <option value="todos">Todos</option>
                            <option value="aprobada">Aprobados</option>
                            <option value="pendiente">Pendientes</option>
                            <option value="rechazada">Rechazados</option>
                        </select>
                        <input type="text" id="buscadorParticipantes" class="form-control" placeholder="Buscar..." style="width: 200px; border-radius: 8px; padding: 0.5rem 0.75rem; font-size: 0.9rem; border: 1px solid rgba(12, 43, 68, 0.2);">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background: var(--brand-gris-suave);">
                            <tr>
                                <th style="padding: 1rem; font-weight: 600; color: var(--primary-color); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Participante</th>
                                <th style="padding: 1rem; font-weight: 600; color: var(--primary-color); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Email</th>
                                <th style="padding: 1rem; font-weight: 600; color: var(--primary-color); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Teléfono</th>
                                <th style="padding: 1rem; font-weight: 600; color: var(--primary-color); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Estado</th>
                                <th style="padding: 1rem; font-weight: 600; color: var(--primary-color); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Fecha Registro</th>
                                <th style="padding: 1rem; font-weight: 600; color: var(--primary-color); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaParticipantes">
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="spinner-border" role="status" style="color: #00A36C; width: 2rem; height: 2rem;">
                                        <span class="sr-only">Cargando...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Historial de Cambios -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; animation: fadeInUp 0.8s ease-out;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-4">
                    <div class="section-icon mr-3">
                        <i class="fas fa-history"></i>
                    </div>
                    <div>
                        <h5 class="mb-0" style="font-weight: 700; color: #0C2B44; font-size: 1.1rem;">
                            Bitácora de Cambios
                        </h5>
                        <p class="mb-0 text-muted" style="font-size: 0.85rem; margin-top: 0.25rem;">
                            Registro de actividades y modificaciones
                        </p>
                    </div>
                </div>
                <div id="historialContainer">
                    <div class="text-center py-4">
                        <div class="spinner-border" role="status" style="color: #00A36C; width: 2rem; height: 2rem;">
                            <span class="sr-only">Cargando...</span>
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
    /* Variables CSS (igual que show.blade.php) */
    :root {
        --primary-color: #0C2B44;
        --secondary-color: #00A36C;
        --text-dark: #2c3e50;
        --text-muted: #6c757d;
        --border-radius: 16px;
        --shadow-sm: 0 2px 8px rgba(0,0,0,0.08);
        --shadow-md: 0 4px 12px rgba(0,0,0,0.12);
        --shadow-lg: 0 8px 24px rgba(0,0,0,0.15);
        --brand-primario: #0C2B44;
        --brand-acento: #00A36C;
        --brand-blanco: #FFFFFF;
        --brand-gris-oscuro: #333333;
        --brand-gris-suave: #F5F5F5;
    }
    
    /* Animaciones (igual que show.blade.php) */
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

    /* Section Icon (igual que show.blade.php) */
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

    /* Cards mejoradas (igual que show.blade.php) */
    .card {
        transition: all 0.3s ease;
        border: none;
    }
    
    .card:hover {
        box-shadow: var(--shadow-md) !important;
        transform: translateY(-2px);
    }

    /* Badges mejorados (igual que show.blade.php) */
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
    
    .badge-primary {
        background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%) !important;
        color: white !important;
    }

    body {
        background-color: var(--brand-gris-suave);
    }

    .container-fluid {
        max-width: 1400px;
        padding: 2rem 1.5rem;
    }

    /* Métricas Principales */
    .metric-card-minimal {
        background: var(--brand-blanco);
        border: 1px solid rgba(12, 43, 68, 0.1);
        border-radius: 8px;
        padding: 2rem 1.5rem;
        text-align: center;
        transition: all 0.2s ease;
    }

    .metric-card-minimal:hover {
        border-color: var(--brand-acento);
        box-shadow: 0 2px 8px rgba(0, 163, 108, 0.1);
    }

    .metric-value {
        font-size: 2.5rem;
        font-weight: 300;
        color: var(--brand-primario);
        line-height: 1.2;
        margin-bottom: 0.5rem;
    }

    .metric-label {
        font-size: 0.9rem;
        color: var(--brand-gris-oscuro);
        font-weight: 400;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Tarjetas de Interacción */
    .interaction-card-minimal {
        background: var(--brand-blanco);
        border: 1px solid rgba(12, 43, 68, 0.1);
        border-radius: 8px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        transition: all 0.2s ease;
    }

    .interaction-card-minimal:hover {
        border-color: var(--brand-acento);
        box-shadow: 0 2px 8px rgba(0, 163, 108, 0.1);
    }

    .interaction-icon-minimal {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: rgba(0, 163, 108, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1.25rem;
        color: var(--brand-acento);
        font-size: 1.25rem;
    }

    .interaction-content-minimal {
        flex: 1;
    }

    .interaction-value {
        font-size: 2rem;
        font-weight: 300;
        color: var(--brand-primario);
        line-height: 1.2;
        margin-bottom: 0.25rem;
    }

    .interaction-label {
        font-size: 0.95rem;
        color: var(--brand-gris-oscuro);
        font-weight: 500;
        margin-bottom: 0.25rem;
    }

    .interaction-subtitle {
        font-size: 0.85rem;
        color: var(--brand-gris-oscuro);
        opacity: 0.7;
    }

    /* Secciones */
    .section-card-minimal {
        background: var(--brand-blanco);
        border: 1px solid rgba(12, 43, 68, 0.1);
        border-radius: 8px;
        overflow: hidden;
    }

    .section-header-minimal {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid rgba(12, 43, 68, 0.1);
        background: var(--brand-gris-suave);
    }

    .section-title-minimal {
        font-size: 1rem;
        font-weight: 500;
        color: var(--brand-primario);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .section-subtitle-minimal {
        font-size: 0.85rem;
        color: var(--brand-gris-oscuro);
        opacity: 0.7;
        margin-top: 0.25rem;
        font-weight: 400;
    }

    .section-body-minimal {
        padding: 1.5rem;
    }

    /* Progress Bar Minimalista */
    .progress-minimal {
        height: 6px;
        background: var(--brand-gris-suave);
        border-radius: 3px;
        overflow: hidden;
    }

    .progress-bar-minimal {
        height: 100%;
        background: linear-gradient(90deg, var(--brand-primario) 0%, var(--brand-acento) 100%);
        transition: width 0.3s ease;
    }

    .progress-label-minimal {
        font-size: 0.9rem;
        color: var(--brand-gris-oscuro);
        font-weight: 400;
    }

    .progress-value-minimal {
        font-size: 0.9rem;
        color: var(--brand-acento);
        font-weight: 500;
    }

    /* Tareas */
    .tareas-container-minimal {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .tarea-item-minimal {
        display: flex;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid rgba(12, 43, 68, 0.1);
    }

    .tarea-item-minimal:last-child {
        border-bottom: none;
    }

    .tarea-item-minimal i {
        margin-right: 0.75rem;
        width: 20px;
    }

    .tarea-item-minimal small {
        color: var(--brand-gris-oscuro);
        font-size: 0.9rem;
    }

    /* Alertas Minimalistas */
    .alertas-container-minimal {
        background: var(--brand-blanco);
        border: 1px solid rgba(12, 43, 68, 0.1);
        border-radius: 8px;
        overflow: hidden;
    }

    .alertas-header-minimal {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid rgba(12, 43, 68, 0.1);
        background: var(--brand-gris-suave);
    }

    .alertas-title {
        font-size: 1rem;
        font-weight: 500;
        color: var(--brand-primario);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .alertas-content-minimal {
        padding: 1.5rem;
    }

    .alert-item-minimal {
        border-radius: 4px;
    }

    /* Tabla Minimalista */
    .table-minimal {
        width: 100%;
        border-collapse: collapse;
    }

    .table-minimal thead {
        background: var(--brand-gris-suave);
    }

    .table-minimal thead th {
        padding: 1rem 1.5rem;
        text-align: left;
        font-size: 0.85rem;
        font-weight: 500;
        color: var(--brand-primario);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid rgba(12, 43, 68, 0.1);
    }

    .table-minimal tbody td {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid rgba(12, 43, 68, 0.05);
        color: var(--brand-gris-oscuro);
        font-size: 0.9rem;
    }

    .table-minimal tbody tr:hover {
        background: rgba(0, 163, 108, 0.03);
    }

    .table-minimal tbody tr:last-child td {
        border-bottom: none;
    }

    /* Form Controls Minimalistas */
    .form-control-minimal {
        border: 1px solid rgba(12, 43, 68, 0.2);
        border-radius: 6px;
        padding: 0.5rem 0.75rem;
        font-size: 0.9rem;
        color: var(--brand-gris-oscuro);
        background: var(--brand-blanco);
        transition: all 0.2s ease;
    }

    .form-control-minimal:focus {
        outline: none;
        border-color: var(--brand-acento);
        box-shadow: 0 0 0 3px rgba(0, 163, 108, 0.1);
    }

    /* Badges Minimalistas */
    .badge-estado {
        padding: 0.35em 0.75em;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 500;
        display: inline-block;
    }

    .badge-aprobada {
        background: rgba(0, 163, 108, 0.1);
        color: var(--brand-acento);
    }

    .badge-pendiente {
        background: rgba(12, 43, 68, 0.1);
        color: var(--brand-primario);
    }

    .badge-rechazada {
        background: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }

    /* Historial Minimalista */
    .historial-item-minimal {
        display: flex;
        align-items: flex-start;
        padding: 1rem 0;
        border-bottom: 1px solid rgba(12, 43, 68, 0.1);
    }

    .historial-item-minimal:last-child {
        border-bottom: none;
    }

    .historial-icon-minimal {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(0, 163, 108, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        color: var(--brand-acento);
        font-size: 1rem;
        flex-shrink: 0;
    }

    .historial-content-minimal {
        flex: 1;
    }

    .historial-title-minimal {
        font-size: 0.95rem;
        font-weight: 500;
        color: var(--brand-gris-oscuro);
        margin-bottom: 0.25rem;
    }

    .historial-detail-minimal {
        font-size: 0.85rem;
        color: var(--brand-gris-oscuro);
        opacity: 0.7;
        margin-bottom: 0.25rem;
    }

    .historial-date-minimal {
        font-size: 0.8rem;
        color: var(--brand-gris-oscuro);
        opacity: 0.6;
    }

    /* Canvas */
    canvas {
        max-width: 100%;
    }

    /* Estilo para tarjetas de gráficas (igual que home-ong) */
    .card.border-0.shadow-sm {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card.border-0.shadow-sm:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12) !important;
    }

    /* Botones mejorados (igual que show.blade.php) */
    .btn {
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-2px);
    }

    .btn:active {
        transform: translateY(0);
    }

    /* Loading State */
    #loadingMessage {
        padding: 4rem 2rem;
    }

    #loadingMessage .spinner-border {
        width: 3rem;
        height: 3rem;
        border-width: 0.3rem;
        color: var(--brand-acento);
    }

    #loadingMessage p {
        color: var(--brand-gris-oscuro);
        opacity: 0.7;
        margin-top: 1rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .container-fluid {
            padding: 1rem;
        }

        .metric-value {
            font-size: 2rem;
        }

        .interaction-value {
            font-size: 1.5rem;
        }

        .section-body-minimal {
            padding: 1rem;
        }

        .table-minimal thead th,
        .table-minimal tbody td {
            padding: 0.75rem 1rem;
        }
    }
</style>
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="{{ asset('assets/js/config.js') }}"></script>
<script>
let megaEventoId = null;
let graficaInscripciones = null;
let graficaEstados = null;

// Obtener ID del mega evento de la URL
// La URL es: /ong/mega-eventos/{id}/seguimiento
const pathParts = window.location.pathname.split('/').filter(p => p !== '');
// Buscar el índice de 'mega-eventos' y tomar el siguiente elemento como ID
const megaEventosIndex = pathParts.indexOf('mega-eventos');
if (megaEventosIndex !== -1 && pathParts[megaEventosIndex + 1]) {
    megaEventoId = pathParts[megaEventosIndex + 1];
} else {
    // Fallback: intentar obtener el penúltimo elemento
    megaEventoId = pathParts[pathParts.length - 2];
}

// Cargar datos de seguimiento
async function cargarSeguimiento() {
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

    try {
        // Cargar estadísticas
        const resSeguimiento = await fetch(`${API_BASE_URL}/api/mega-eventos/${megaEventoId}/seguimiento`, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        const dataSeguimiento = await resSeguimiento.json();

        if (!resSeguimiento.ok || !dataSeguimiento.success) {
            throw new Error(dataSeguimiento.error || 'Error al cargar seguimiento');
        }

        // Actualizar información del mega evento
        const megaEvento = dataSeguimiento.mega_evento;
        document.getElementById('tituloMegaEvento').textContent = megaEvento.titulo;
        document.getElementById('volverLink').href = `/ong/mega-eventos/${megaEventoId}/detalle`;
        
        const fechaInicio = new Date(megaEvento.fecha_inicio).toLocaleDateString('es-BO', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        const fechaFin = new Date(megaEvento.fecha_fin).toLocaleDateString('es-BO', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        document.getElementById('fechasMegaEvento').textContent = `${fechaInicio} - ${fechaFin}`;

        // Actualizar métricas principales
        const stats = dataSeguimiento.estadisticas;
        document.getElementById('totalParticipantes').textContent = stats.total_participantes;
        document.getElementById('participantesAprobados').textContent = stats.participantes_aprobados;
        document.getElementById('tasaConfirmacion').textContent = stats.tasa_confirmacion + '%';
        
        // Actualizar métricas de interacción
        if (stats.interaccion_social) {
            document.getElementById('totalReacciones').textContent = stats.interaccion_social.total_reacciones || 0;
            document.getElementById('totalCompartidos').textContent = stats.interaccion_social.total_compartidos || 0;
        }
        
        // Actualizar métricas de participación
        document.getElementById('totalParticipaciones').textContent = stats.total_participantes || 0;
        const participantesRegistrados = stats.participantes_registrados || 0;
        const participantesNoRegistrados = stats.participantes_no_registrados || 0;
        document.getElementById('detalleParticipaciones').textContent = `${participantesRegistrados} registrados, ${participantesNoRegistrados} no registrados`;
        
        if (stats.porcentaje_capacidad !== null) {
            document.getElementById('porcentajeCapacidad').textContent = stats.porcentaje_capacidad + '%';
        } else {
            document.getElementById('porcentajeCapacidad').textContent = 'Sin límite';
        }

        // Actualizar seguimiento por tipo de actor
        document.getElementById('porcentajeCumplimientoOng').textContent = stats.porcentaje_cumplimiento_ong + '%';
        const barraCumplimiento = document.getElementById('barraCumplimientoOng');
        if (barraCumplimiento) {
            barraCumplimiento.style.width = stats.porcentaje_cumplimiento_ong + '%';
        }

        // Mostrar tareas de ONG
        if (stats.tareas_cumplidas_ong) {
            const tareasContainer = document.getElementById('tareasOngContainer');
            const tareas = stats.tareas_cumplidas_ong;
            tareasContainer.innerHTML = `
                <div class="col-md-6 mb-3">
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas ${tareas.evento_publicado ? 'fa-check-circle' : 'fa-times-circle'}"></i>
                        </div>
                        <div class="info-content">
                            <h6 class="info-label">Evento publicado</h6>
                            <p class="info-value" style="color: ${tareas.evento_publicado ? '#00A36C' : '#dc3545'};">
                                ${tareas.evento_publicado ? 'Completado' : 'Pendiente'}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas ${tareas.imagenes_cargadas ? 'fa-check-circle' : 'fa-times-circle'}"></i>
                        </div>
                        <div class="info-content">
                            <h6 class="info-label">Imágenes cargadas</h6>
                            <p class="info-value" style="color: ${tareas.imagenes_cargadas ? '#00A36C' : '#dc3545'};">
                                ${tareas.imagenes_cargadas ? 'Completado' : 'Pendiente'}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas ${tareas.fechas_definidas ? 'fa-check-circle' : 'fa-times-circle'}"></i>
                        </div>
                        <div class="info-content">
                            <h6 class="info-label">Fechas definidas</h6>
                            <p class="info-value" style="color: ${tareas.fechas_definidas ? '#00A36C' : '#dc3545'};">
                                ${tareas.fechas_definidas ? 'Completado' : 'Pendiente'}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas ${tareas.ubicacion_definida ? 'fa-check-circle' : 'fa-times-circle'}"></i>
                        </div>
                        <div class="info-content">
                            <h6 class="info-label">Ubicación definida</h6>
                            <p class="info-value" style="color: ${tareas.ubicacion_definida ? '#00A36C' : '#dc3545'};">
                                ${tareas.ubicacion_definida ? 'Completado' : 'Pendiente'}
                            </p>
                        </div>
                    </div>
                </div>
            `;
        }

        // Mostrar alertas
        if (dataSeguimiento.alertas && dataSeguimiento.alertas.length > 0) {
            mostrarAlertas(dataSeguimiento.alertas);
        }

        // Crear gráficas
        crearGraficaReacciones(dataSeguimiento.reacciones_por_dia || []);
        crearGraficaEstados(stats);

        // Cargar participantes
        cargarParticipantes();

        // Cargar historial
        cargarHistorial();

        // Mostrar contenido
        document.getElementById('loadingMessage').style.display = 'none';
        document.getElementById('seguimientoContent').style.display = 'block';

    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message || 'Error al cargar el seguimiento del mega evento'
        });
    }
}

// Crear gráfica de reacciones por día
function crearGraficaReacciones(datos) {
    const ctx = document.getElementById('graficaInscripciones').getContext('2d');
    
    // Si no hay datos, crear un array vacío con los últimos 30 días
    if (!datos || datos.length === 0) {
        const ultimos30Dias = [];
        for (let i = 29; i >= 0; i--) {
            const fecha = new Date();
            fecha.setDate(fecha.getDate() - i);
            ultimos30Dias.push({
                fecha: fecha.toISOString().split('T')[0],
                cantidad: 0
            });
        }
        datos = ultimos30Dias;
    }
    
    const labels = datos.map(d => {
        const fecha = new Date(d.fecha);
        return fecha.toLocaleDateString('es-BO', { month: 'short', day: 'numeric' });
    });
    const valores = datos.map(d => d.cantidad || 0);

    if (graficaInscripciones) {
        graficaInscripciones.destroy();
    }

    // Crear gradiente para el área
    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(0, 163, 108, 0.6)');
    gradient.addColorStop(1, 'rgba(0, 163, 108, 0.0)');

    graficaInscripciones = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Reacciones',
                data: valores,
                borderColor: '#00A36C',
                backgroundColor: gradient,
                tension: 0.4,
                fill: true,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: '#00A36C',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 12,
                        font: { size: 11, weight: '500' },
                        color: '#666'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 163, 108, 0.9)',
                    padding: 12,
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: '#00A36C',
                    borderWidth: 1
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        color: '#666',
                        font: { size: 11 }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#666',
                        font: { size: 11, weight: '500' }
                    }
                }
            }
        }
    });
}

// Crear gráfica de estados
function crearGraficaEstados(stats) {
    const ctx = document.getElementById('graficaEstados').getContext('2d');

    if (graficaEstados) {
        graficaEstados.destroy();
    }

    const labels = ['Aprobados', 'Pendientes'];
    const data = [stats.participantes_aprobados || 0, stats.participantes_pendientes || 0];
    
    // Agregar cancelados si existen
    if (stats.participantes_cancelados > 0) {
        labels.push('Cancelados');
        data.push(stats.participantes_cancelados);
    }

    // Si no hay datos, mostrar valores mínimos para evitar error
    const total = data.reduce((a, b) => a + b, 0);
    if (total === 0) {
        data[0] = 1;
        data[1] = 1;
    }

    graficaEstados = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: ['#00A36C', '#0C2B44', '#dc3545'],
                borderWidth: 0,
                cutout: '70%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 15,
                        font: { size: 12, weight: '500' },
                        color: '#666'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(12, 43, 68, 0.9)',
                    padding: 12,
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}

// Mostrar alertas
function mostrarAlertas(alertas) {
    const panelAlertas = document.getElementById('panelAlertas');
    const container = document.getElementById('alertasContainer');
    
    if (!alertas || alertas.length === 0) {
        panelAlertas.style.display = 'none';
        return;
    }

    panelAlertas.style.display = 'block';
    
    const nivelColores = {
        'critica': { bg: '#f8d7da', border: '#dc3545', icon: 'fa-exclamation-triangle', text: '#721c24' },
        'advertencia': { bg: '#fff3cd', border: '#ffc107', icon: 'fa-exclamation-circle', text: '#856404' },
        'info': { bg: '#d1ecf1', border: '#17a2b8', icon: 'fa-info-circle', text: '#0c5460' }
    };

        container.innerHTML = alertas.map(alerta => {
        const color = nivelColores[alerta.nivel] || nivelColores.info;
        const fecha = new Date(alerta.fecha).toLocaleString('es-BO', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });

        return `
            <div class="info-item" style="border-left: 4px solid ${color.border}; background: ${color.bg}; margin-bottom: 0.75rem;">
                <div class="info-icon" style="color: ${color.text};">
                    <i class="fas ${color.icon}"></i>
                </div>
                <div class="info-content">
                    <h6 class="info-label" style="color: ${color.text}; margin-bottom: 0.5rem;">${alerta.mensaje}</h6>
                    <p class="info-value mb-0" style="color: ${color.text}; opacity: 0.7; font-size: 0.85rem;">${fecha}</p>
                </div>
            </div>
        `;
    }).join('');
}

// Cargar participantes
async function cargarParticipantes(estado = 'todos', buscar = '') {
    const token = localStorage.getItem('token');
    const tbody = document.getElementById('tablaParticipantes');

    tbody.innerHTML = '<tr><td colspan="6" class="text-center py-3"><div class="spinner-border text-primary" role="status"><span class="sr-only">Cargando...</span></div></td></tr>';

    try {
        const params = new URLSearchParams();
        if (estado !== 'todos') {
            params.append('estado', estado);
        }
        if (buscar.trim() !== '') {
            params.append('buscar', buscar.trim());
        }

        const url = `${API_BASE_URL}/api/mega-eventos/${megaEventoId}/participantes${params.toString() ? '?' + params.toString() : ''}`;
        
        const res = await fetch(url, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        const data = await res.json();

        if (!res.ok || !data.success) {
            throw new Error(data.error || 'Error al cargar participantes');
        }

        if (data.participantes.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-5 text-muted">No hay participantes registrados</td></tr>';
            return;
        }

        tbody.innerHTML = data.participantes.map(p => {
            const fechaRegistro = new Date(p.fecha_registro).toLocaleDateString('es-BO', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            // Priorizar nombre_completo, luego construir desde nombres/apellidos
            let nombreCompleto = p.nombre_completo || p.participante;
            
            // Si no hay nombre_completo, intentar construir desde nombres y apellidos
            if (!nombreCompleto || nombreCompleto === 'Sin nombre' || nombreCompleto.length <= 2) {
                const nombres = p.nombres || '';
                const apellidos = p.apellidos || '';
                const nombreConstruido = (nombres + ' ' + apellidos).trim();
                nombreCompleto = nombreConstruido || p.nombre_usuario || 'Usuario';
            }
            
            // Si aún es muy corto o solo tiene una letra, usar nombre_usuario
            if (nombreCompleto.length <= 2 && p.nombre_usuario && p.nombre_usuario.length > 2) {
                nombreCompleto = p.nombre_usuario;
            }
            
            const fotoPerfil = p.foto_perfil || p.avatar || null;
            // El estado puede venir como 'estado' o 'estado_participacion' dependiendo del tipo
            const estado = p.estado || p.estado_participacion || 'pendiente';
            const estadoClass = estado === 'aprobada' ? 'badge-aprobada' : 
                               estado === 'rechazada' ? 'badge-rechazada' : 'badge-pendiente';
            const estadoTexto = estado === 'aprobada' ? 'Aprobada' :
                               estado === 'rechazada' ? 'Rechazada' : 'Pendiente';
            
            // Badge de tipo de participante
            const tipoBadge = p.tipo === 'registrado' 
                ? '<span class="badge badge-info mr-2" style="font-size: 0.7rem;">Registrado</span>'
                : '<span class="badge badge-warning mr-2" style="font-size: 0.7rem;">No registrado</span>';

            return `
                <tr>
                    <td style="padding: 1rem;">
                        <div class="d-flex align-items-center">
                            <div class="mr-2 position-relative" style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                ${fotoPerfil ? `
                                    <img src="${fotoPerfil}" alt="${nombreCompleto}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div style="width: 100%; height: 100%; display: none; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1rem;">
                                        ${nombreCompleto.charAt(0).toUpperCase()}
                                    </div>
                                ` : `
                                    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1rem;">
                                        ${nombreCompleto.charAt(0).toUpperCase()}
                                    </div>
                                `}
                            </div>
                            <div>
                                <strong class="text-dark">${nombreCompleto}</strong>
                                <br>${tipoBadge}
                            </div>
                        </div>
                    </td>
                    <td>${p.email || '-'}</td>
                    <td>${p.telefono || '-'}</td>
                    <td>
                        <span class="badge badge-estado ${estadoClass}">${estadoTexto}</span>
                    </td>
                    <td>${fechaRegistro}</td>
                    <td>
                        ${p.integrante_externo_id ? `
                            <button class="btn btn-sm btn-outline-primary" onclick="verHistorialParticipante(${p.integrante_externo_id})" title="Ver historial">
                                <i class="fas fa-history"></i>
                            </button>
                        ` : '<span class="text-muted">-</span>'}
                    </td>
                </tr>
            `;
        }).join('');

    } catch (error) {
        console.error('Error:', error);
        tbody.innerHTML = `<tr><td colspan="6" class="text-center py-5 text-danger">Error al cargar participantes: ${error.message}</td></tr>`;
    }
}

// Cargar historial
async function cargarHistorial() {
    const token = localStorage.getItem('token');
    const container = document.getElementById('historialContainer');

    try {
        const res = await fetch(`${API_BASE_URL}/api/mega-eventos/${megaEventoId}/historial`, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        const data = await res.json();

        if (!res.ok || !data.success) {
            throw new Error(data.error || 'Error al cargar historial');
        }

        if (data.historial.length === 0) {
            container.innerHTML = '<p class="text-muted mb-0">No hay historial disponible</p>';
            return;
        }

        if (data.historial.length === 0) {
            container.innerHTML = '<p class="text-muted mb-0">No hay historial disponible</p>';
            return;
        }

        const iconosPorTipo = {
            'creacion': 'fa-check',
            'estado': 'fa-clock',
            'publicacion': 'fa-calendar',
            'imagenes': 'fa-image',
            'actualizacion': 'fa-edit',
            'participacion': 'fa-user-plus'
        };

        const coloresPorTipo = {
            'creacion': '#00A36C',
            'estado': '#17a2b8',
            'publicacion': '#6f42c1',
            'imagenes': '#ffc107',
            'actualizacion': '#0C2B44',
            'participacion': '#00A36C'
        };

        container.innerHTML = data.historial.map(h => {
            const fecha = new Date(h.fecha).toLocaleDateString('es-BO', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            const icono = h.icono || iconosPorTipo[h.tipo] || 'fa-clock';
            const color = coloresPorTipo[h.tipo] || '#00A36C';

            return `
                <div class="info-item">
                    <div class="info-icon" style="color: ${color};">
                        <i class="fas ${icono}"></i>
                    </div>
                    <div class="info-content">
                        <h6 class="info-label" style="margin-bottom: 0.25rem;">${h.accion}</h6>
                        <p class="info-value mb-1" style="font-size: 0.9rem;">${h.detalle}</p>
                        <small class="text-muted" style="font-size: 0.8rem;">${fecha} - ${h.usuario}</small>
                    </div>
                </div>
            `;
        }).join('');

    } catch (error) {
        console.error('Error:', error);
        container.innerHTML = `<p class="text-danger">Error al cargar historial: ${error.message}</p>`;
    }
}

// Ver historial de participante
function verHistorialParticipante(integranteId) {
    Swal.fire({
        icon: 'info',
        title: 'Historial de Participante',
        text: 'Esta funcionalidad se implementará próximamente',
        confirmButtonText: 'Cerrar'
    });
}

// Exportar reporte a Excel
document.getElementById('btnExportarReporte').addEventListener('click', function() {
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

    Swal.fire({
        title: 'Exportando reporte...',
        text: 'Por favor espera mientras se genera el archivo Excel',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Crear enlace de descarga
    const url = `${API_BASE_URL}/api/mega-eventos/${megaEventoId}/exportar-excel`;
    
    // Agregar token como header usando fetch
    fetch(url, {
        method: 'GET',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/vnd.ms-excel'
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(data.error || 'Error al exportar reporte');
            });
        }
        return response.blob();
    })
    .then(blob => {
        const urlBlob = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = urlBlob;
        link.download = `seguimiento-mega-evento-${megaEventoId}-${new Date().toISOString().split('T')[0]}.xls`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(urlBlob);
        
        Swal.fire({
            icon: 'success',
            title: '¡Exportación exitosa!',
            text: 'El reporte se ha descargado correctamente',
            confirmButtonText: 'Cerrar'
        });
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error al exportar',
            text: error.message || 'Ocurrió un error al generar el archivo Excel',
            confirmButtonText: 'Cerrar'
        });
    });
});

// Filtros
document.getElementById('filtroEstadoParticipante').addEventListener('change', function() {
    const estado = this.value;
    const buscar = document.getElementById('buscadorParticipantes').value;
    cargarParticipantes(estado, buscar);
});

document.getElementById('buscadorParticipantes').addEventListener('input', function() {
    const estado = document.getElementById('filtroEstadoParticipante').value;
    const buscar = this.value;
    cargarParticipantes(estado, buscar);
});

// Inicializar
document.addEventListener('DOMContentLoaded', function() {
    cargarSeguimiento();
});
</script>
@endpush


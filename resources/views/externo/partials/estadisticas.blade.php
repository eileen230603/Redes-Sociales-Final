<!-- Gráfica de Estadísticas -->
<div class="row mb-4">
    <div class="col-lg-6 col-md-6 mb-3">
        <div class="card shadow-sm" style="border: none; border-radius: 16px; background: #ffffff;">
            <div class="card-header bg-white d-flex justify-content-between align-items-center" style="border-bottom: 1px solid #f0f0f0; padding: 1rem 1.25rem; border-radius: 16px 16px 0 0;">
                <div>
                    <h6 class="mb-0" style="font-weight: 600; color: #333; font-size: 0.9rem;">
                        <i class="far fa-calendar-check mr-2" style="color: #00A36C; font-size: 0.85rem;"></i>Eventos Inscritos
                    </h6>
                    <small class="text-muted" style="font-size: 0.75rem;">Mensual</small>
                </div>
                <div class="text-right">
                    <div class="badge badge-success" style="background: #e8f8f2; color: #00A36C; font-size: 0.85rem; padding: 0.4rem 0.8rem; border-radius: 8px;">
                        <i class="fas fa-circle" style="font-size: 0.5rem; margin-right: 4px;"></i>
                        <span id="badgeEventosInscritos" style="font-weight: 700;">0</span>
                    </div>
                </div>
            </div>
            <div class="card-body" style="padding: 1.5rem;">
                <div style="height: 180px; position: relative;">
                    <canvas id="graficaEventosInscritos"></canvas>
                </div>
                <div class="text-center mt-3">
                    <h2 class="mb-0" id="totalEventosInscritosGrafica" style="color: #00A36C; font-weight: 700; font-size: 2.5rem;">0</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-md-6 mb-3">
        <div class="card shadow-sm" style="border: none; border-radius: 16px; background: #ffffff;">
            <div class="card-header bg-white d-flex justify-content-between align-items-center" style="border-bottom: 1px solid #f0f0f0; padding: 1rem 1.25rem; border-radius: 16px 16px 0 0;">
                <div>
                    <h6 class="mb-0" style="font-weight: 600; color: #333; font-size: 0.9rem;">
                        <i class="far fa-handshake mr-2" style="color: #00A36C; font-size: 0.85rem;"></i>Eventos Asistidos
                    </h6>
                    <small class="text-muted" style="font-size: 0.75rem;">Mensual</small>
                </div>
                <div class="text-right">
                    <div class="badge badge-success" style="background: #e8f8f2; color: #00A36C; font-size: 0.85rem; padding: 0.4rem 0.8rem; border-radius: 8px;">
                        <i class="fas fa-circle" style="font-size: 0.5rem; margin-right: 4px;"></i>
                        <span id="badgeEventosAsistidos" style="font-weight: 700;">0</span>
                    </div>
                </div>
            </div>
            <div class="card-body" style="padding: 1.5rem;">
                <div style="height: 180px; position: relative;">
                    <canvas id="graficaEventosAsistidos"></canvas>
                </div>
                <div class="text-center mt-3">
                    <h2 class="mb-0" id="totalEventosAsistidosGrafica" style="color: #00A36C; font-weight: 700; font-size: 2.5rem;">0</h2>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card metric-card mb-4">
    <div class="card-header" style="background: var(--color-bg); border-bottom: 1px solid var(--color-border); padding: 20px;">
        <h5 class="mb-0" style="color: var(--color-secondary); font-weight: 600;">
            <i data-feather="filter" style="width: 20px; height: 20px; margin-right: 10px;"></i>
            Filtros Avanzados
        </h5>
    </div>
    <div class="card-body" style="padding: 30px;">
        <form id="filtrosForm" method="GET" action="{{ $route }}">
            <!-- Tipo de Reporte (solo para consolidado) -->
            @if(isset($tipoReporte) && $tipoReporte === 'consolidado')
            <div class="filter-group">
                <label class="filter-label">Tipo de Reporte</label>
                <div class="radio-group" style="display: flex; flex-direction: column; gap: 20px; margin-top: 10px;">
                    <label class="radio-label">
                        <input type="radio" name="tipo_reporte" value="eventos" {{ request('tipo_reporte', 'ambos') === 'eventos' ? 'checked' : '' }}>
                        <span>Solo Eventos Regulares</span>
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="tipo_reporte" value="mega_eventos" {{ request('tipo_reporte') === 'mega_eventos' ? 'checked' : '' }}>
                        <span>Solo Mega Eventos</span>
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="tipo_reporte" value="ambos" {{ request('tipo_reporte', 'ambos') === 'ambos' ? 'checked' : '' }}>
                        <span>Consolidado (Ambos)</span>
                    </label>
                </div>
            </div>
            @endif

            <div class="row">
                <!-- Rango de Fechas -->
                <div class="col-md-3 mb-3">
                    <div class="filter-group">
                        <label for="fecha_inicio" class="filter-label">Fecha Desde</label>
                        <input type="date" class="filter-input" id="fecha_inicio" name="fecha_inicio" 
                               value="{{ request('fecha_inicio') }}">
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="filter-group">
                        <label for="fecha_fin" class="filter-label">Fecha Hasta</label>
                        <input type="date" class="filter-input" id="fecha_fin" name="fecha_fin" 
                               value="{{ request('fecha_fin') }}">
                    </div>
                </div>

                <!-- Categoría -->
                <div class="col-md-2 mb-3">
                    <div class="filter-group">
                        <label for="categoria" class="filter-label">Categoría</label>
                        <select class="filter-input" id="categoria" name="categoria">
                            <option value="">Todas</option>
                            <option value="social" {{ request('categoria') == 'social' ? 'selected' : '' }}>Social</option>
                            <option value="educativo" {{ request('categoria') == 'educativo' ? 'selected' : '' }}>Educativo</option>
                            <option value="ambiental" {{ request('categoria') == 'ambiental' ? 'selected' : '' }}>Ambiental</option>
                            <option value="salud" {{ request('categoria') == 'salud' ? 'selected' : '' }}>Salud</option>
                            <option value="cultural" {{ request('categoria') == 'cultural' ? 'selected' : '' }}>Cultural</option>
                            <option value="deportivo" {{ request('categoria') == 'deportivo' ? 'selected' : '' }}>Deportivo</option>
                            <option value="benefico" {{ request('categoria') == 'benefico' ? 'selected' : '' }}>Benéfico</option>
                            <option value="otro" {{ request('categoria') == 'otro' ? 'selected' : '' }}>Otro</option>
                        </select>
                    </div>
                </div>

                <!-- Estado -->
                <div class="col-md-2 mb-3">
                    <div class="filter-group">
                        <label class="filter-label">Estado</label>
                        <div class="checkbox-group" style="display: flex; flex-direction: column; gap: 20px; margin-top: 10px;">
                            <label class="checkbox-label">
                                <input type="checkbox" name="estados[]" value="planificacion" {{ in_array('planificacion', request('estados', [])) ? 'checked' : '' }}>
                                <span>Planificación</span>
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="estados[]" value="activo" {{ in_array('activo', request('estados', [])) ? 'checked' : '' }}>
                                <span>Activo</span>
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="estados[]" value="en_curso" {{ in_array('en_curso', request('estados', [])) ? 'checked' : '' }}>
                                <span>En Curso</span>
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="estados[]" value="finalizado" {{ in_array('finalizado', request('estados', [])) ? 'checked' : '' }}>
                                <span>Finalizado</span>
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="estados[]" value="cancelado" {{ in_array('cancelado', request('estados', [])) ? 'checked' : '' }}>
                                <span>Cancelado</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Ubicación -->
                <div class="col-md-2 mb-3">
                    <div class="filter-group">
                        <label for="ubicacion" class="filter-label">Ubicación</label>
                        <input type="text" class="filter-input" id="ubicacion" name="ubicacion" 
                               value="{{ request('ubicacion') }}" placeholder="Ciudad o dirección">
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Rango de Participantes -->
                <div class="col-md-3 mb-3">
                    <div class="filter-group">
                        <label for="participantes_min" class="filter-label">Participantes Mínimo</label>
                        <input type="number" class="filter-input" id="participantes_min" name="participantes_min" 
                               value="{{ request('participantes_min') }}" min="0">
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="filter-group">
                        <label for="participantes_max" class="filter-label">Participantes Máximo</label>
                        <input type="number" class="filter-input" id="participantes_max" name="participantes_max" 
                               value="{{ request('participantes_max') }}" min="0">
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="btn-group" style="margin-top: 30px;">
                <button type="submit" class="btn btn-primary">
                    <i data-feather="search" style="width: 18px; height: 18px; margin-right: 8px;"></i>
                    Aplicar Filtros
                </button>
                <button type="button" class="btn btn-outline" onclick="clearFilters()" style="margin-left: 20px;">
                    <i data-feather="x" style="width: 18px; height: 18px; margin-right: 8px;"></i>
                    Limpiar Filtros
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function clearFilters() {
    document.getElementById('filtrosForm').reset();
    window.location.href = '{{ $route }}';
}
</script>


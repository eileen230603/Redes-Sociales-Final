<div class="card metric-card">
    <div class="card-body" style="padding: 25px;">
        <div class="d-flex justify-content-between align-items-start">
            <div class="flex-grow-1">
                <p class="metric-label">{{ $titulo }}</p>
                <h3 class="metric-value">{!! $valor !!}</h3>
            </div>
            <div class="metric-icon" style="opacity: 0.2;">
                <i data-feather="{{ $icono }}" style="width: 48px; height: 48px; color: var(--color-{{ $color ?? 'primary' }});"></i>
            </div>
        </div>
    </div>
</div>

<style>
.metric-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    border: 1px solid var(--color-border);
}

.metric-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.12);
    border-color: var(--color-primary);
}

.metric-label {
    font-size: 14px;
    color: var(--color-text-muted);
    margin-bottom: 8px;
    font-weight: 500;
}

.metric-value {
    font-size: 36px;
    font-weight: 700;
    color: var(--color-secondary);
    margin: 0;
}
</style>


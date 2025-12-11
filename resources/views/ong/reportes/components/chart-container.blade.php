<div class="card metric-card">
    <div class="card-header" style="background: var(--color-bg); border-bottom: 1px solid var(--color-border); padding: 20px;">
        <h5 class="mb-0" style="color: var(--color-secondary); font-weight: 600;">
            <i data-feather="bar-chart-2" style="width: 20px; height: 20px; margin-right: 10px;"></i>
            {{ $titulo }}
        </h5>
    </div>
    <div class="card-body" style="padding: 25px;">
        <canvas id="{{ $id }}" height="80"></canvas>
    </div>
</div>


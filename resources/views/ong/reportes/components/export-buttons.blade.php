<div class="card metric-card mb-4">
    <div class="card-body" style="padding: 20px;">
        <div class="btn-group">
            <button type="button" class="btn-export btn-export-pdf" onclick="exportarPDF('{{ $tipoReporte }}')">
                <i data-feather="file-text" style="width: 18px; height: 18px; margin-right: 8px;"></i>
                Exportar PDF
            </button>
            <button type="button" class="btn-export btn-export-excel" onclick="exportarExcel('{{ $tipoReporte }}')">
                <i data-feather="file" style="width: 18px; height: 18px; margin-right: 8px;"></i>
                Exportar Excel
            </button>
            <button type="button" class="btn-export btn-export-csv" onclick="exportarCSV('{{ $tipoReporte }}')">
                <i data-feather="table" style="width: 18px; height: 18px; margin-right: 8px;"></i>
                Exportar CSV
            </button>
            <button type="button" class="btn-export btn-export-json" onclick="exportarJSON('{{ $tipoReporte }}')">
                <i data-feather="code" style="width: 18px; height: 18px; margin-right: 8px;"></i>
                Exportar JSON
            </button>
        </div>
    </div>
</div>

<style>
.btn-export {
    min-width: 150px;
    height: 45px;
    border-radius: 8px;
    border: none;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: white;
    text-decoration: none;
}

.btn-export-pdf {
    background: #0C2B44;
}

.btn-export-excel {
    background: #00A36C;
}

.btn-export-csv {
    background: #6C757D;
}

.btn-export-json {
    background: #FF6B35;
}

.btn-export:hover {
    opacity: 0.85;
    transform: translateY(-2px);
}

.btn-group {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}
</style>


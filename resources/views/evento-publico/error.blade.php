<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
    </style>
</head>
<body>
    <div class="container text-center">
        <div class="card" style="max-width: 500px; margin: 0 auto;">
            <div class="card-body p-5">
                <i class="fas fa-exclamation-triangle fa-4x text-warning mb-4"></i>
                <h3 class="mb-3">Error</h3>
                <p class="text-muted">{{ $mensaje ?? 'Ha ocurrido un error' }}</p>
                <a href="/" class="btn btn-primary mt-3">Volver al inicio</a>
            </div>
        </div>
    </div>
</body>
</html>


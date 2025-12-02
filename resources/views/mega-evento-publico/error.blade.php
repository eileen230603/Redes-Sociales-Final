<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - Mega Evento</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .error-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="text-center">
            <i class="fas fa-exclamation-triangle fa-5x text-warning mb-4"></i>
            <h2 class="mb-3">Error</h2>
            <p class="text-muted">{{ $mensaje ?? 'Error al cargar el mega evento' }}</p>
            <a href="/home-publica" class="btn btn-primary mt-3">
                <i class="fas fa-home mr-2"></i> Volver al inicio
            </a>
        </div>
    </div>
</body>
</html>


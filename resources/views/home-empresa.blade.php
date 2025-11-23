@extends('layouts.adminlte-empresa')

@section('page_title', 'Panel de Empresa')

@section('content_body')
<div class="alert alert-info">
    <h5>Bienvenido, <span id="nombreUsuario"></span></h5>
    <p>Administra tus eventos, publicaciones y alianzas empresariales desde este panel.</p>
</div>

    <!-- 🔹 Estadísticas -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3 id="eventosActivos">5</h3>
                    <p>Eventos activos</p>
                </div>
                <div class="icon"><i class="fas fa-calendar-check"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3 id="alianzas">8</h3>
                    <p>Alianzas ONG</p>
                </div>
                <div class="icon"><i class="fas fa-handshake"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3 id="voluntarios">36</h3>
                    <p>Voluntarios vinculados</p>
                </div>
                <div class="icon"><i class="fas fa-users"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3 id="proyectos">4</h3>
                    <p>Proyectos activos</p>
                </div>
                <div class="icon"><i class="fas fa-briefcase"></i></div>
            </div>
        </div>
    </div>

    <!-- 🔹 Gráfico de impacto -->
    <div class="card">
        <div class="card-header bg-gradient-primary text-white">
            <h3 class="card-title"><i class="fas fa-chart-bar mr-2"></i> Impacto por Categoría</h3>
        </div>
        <div class="card-body">
            <canvas id="graficoImpacto" height="120"></canvas>
        </div>
    </div>

@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="/assets/js/config.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const token = localStorage.getItem('token');
  const nombre = localStorage.getItem('nombre_usuario') ?? 'Empresa';
  document.getElementById('nombreUsuario').innerText = nombre;

  if (!token) {
    alert('⚠️ Tu sesión ha expirado.');
    window.location.href = '/login';
    return;
  }

  // 🔹 Gráfico de impacto
  const ctx = document.getElementById('graficoImpacto');
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Educación', 'Salud', 'Ambiente', 'Cultura'],
      datasets: [{
        label: 'Proyectos',
        data: [5, 3, 2, 4],
        backgroundColor: ['#007bff','#28a745','#ffc107','#dc3545']
      }]
    },
    options: {
      plugins: { legend: { display: false } }
    }
  });

  // El logout se maneja desde el layout
});
</script>
@stop

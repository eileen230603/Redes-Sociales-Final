@extends('adminlte::page')

@section('title', 'Panel ONG')

@section('content_header')
    <h1><i class="fas fa-hand-holding-heart"></i> Panel ONG</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="alert alert-info">
        <h5>Bienvenido, <span id="nombreUsuario"></span></h5>
        <p>Gestiona tus eventos y voluntarios desde este panel.</p>
    </div>

    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3 id="totalEventos">12</h3>
                    <p>Eventos activos</p>
                </div>
                <div class="icon"><i class="fas fa-calendar-alt"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3 id="totalVoluntarios">87</h3>
                    <p>Voluntarios</p>
                </div>
                <div class="icon"><i class="fas fa-users"></i></div>
            </div>
        </div>
    </div>

    <canvas id="graficoEventos" height="120"></canvas>

    <button id="logoutBtn" class="btn btn-danger mt-3">
        <i class="fas fa-sign-out-alt"></i> Cerrar sesión
    </button>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="/assets/js/config.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const token = localStorage.getItem('token');
  if (!token) {
    alert('⚠️ Tu sesión ha expirado. Inicia sesión nuevamente.');
    window.location.href = '/login';
    return;
  }

  const nombre = localStorage.getItem('nombre_usuario') ?? 'Usuario ONG';
  document.getElementById('nombreUsuario').innerText = nombre;

  const ctx = document.getElementById('graficoEventos');
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Cultura', 'Educación', 'Salud', 'Ambiente'],
      datasets: [{
        label: 'Eventos',
        data: [12, 8, 5, 6],
        backgroundColor: ['#007bff','#28a745','#ffc107','#dc3545']
      }]
    }
  });

  document.getElementById('logoutBtn').addEventListener('click', async () => {
    await fetch(`${API_BASE_URL}/api/auth/logout`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' }
    });
    localStorage.clear();
    window.location.href = '/login';
  });
});
</script>
@stop

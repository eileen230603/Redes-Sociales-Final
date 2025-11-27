@extends('layouts.adminlte-externo')

@section('page_title', 'Mis Participaciones')

@section('content_body')
<div class="container-fluid">
    <!-- Header con diseño mejorado - Paleta de colores -->
    <div class="card mb-4 shadow-sm" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); border: none; border-radius: 15px; overflow: hidden;">
        <div class="card-body py-4 px-4">
            <div class="row align-items-center">
                <div class="col-md-10">
                    <div class="d-flex align-items-center">
                        <div class="bg-white rounded-circle p-3 mr-3 shadow-sm" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                            <i class="far fa-calendar-check" style="font-size: 1.8rem; color: #00A36C;"></i>
                        </div>
                        <div>
                            <h3 class="text-white mb-1" style="font-weight: 700; font-size: 1.75rem;">
                                Mis Participaciones en Eventos
                            </h3>
                            <p class="text-white mb-0" style="opacity: 0.95; font-size: 1rem;">
                                Revisa todos los eventos en los que estás participando
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 text-right d-none d-md-block">
                    <i class="far fa-calendar-check" style="font-size: 4.5rem; color: rgba(255,255,255,0.15);"></i>
                </div>
            </div>
        </div>
    </div>

    <div id="participacionesContainer" class="row">
        <div class="col-12 text-center py-5">
            <div class="spinner-border" role="status" style="width: 3rem; height: 3rem; color: #00A36C;">
                <span class="sr-only">Cargando...</span>
            </div>
            <p class="mt-3 text-muted">Cargando tus participaciones...</p>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    .card {
        transition: all 0.3s ease;
        border-radius: 12px;
        border: 1px solid #F5F5F5;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(12, 43, 68, 0.15) !important;
    }

    .badge-success {
        background-color: #00A36C !important;
        color: white !important;
    }

    .badge-warning {
        background-color: #ffc107 !important;
        color: #212529 !important;
    }

    .badge-danger {
        background-color: #dc3545 !important;
        color: white !important;
    }
</style>
@endpush

@push('js')
<script src="{{ asset('assets/js/config.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', async () => {
    await cargarMisParticipaciones();
});

async function cargarMisParticipaciones() {
    const container = document.getElementById('participacionesContainer');
    const token = localStorage.getItem('token');

    if (!token) {
        container.innerHTML = `
            <div class="col-12">
                <div class="alert alert-warning">
                    <p>Debes iniciar sesión para ver tus participaciones.</p>
                    <a href="/login" class="btn btn-primary">Iniciar sesión</a>
                </div>
            </div>
        `;
        return;
    }

    try {
        container.innerHTML = `
            <div class="col-12 text-center py-5">
                <div class="spinner-border" role="status" style="width: 3rem; height: 3rem; color: #00A36C;">
                    <span class="sr-only">Cargando...</span>
                </div>
                <p class="mt-3 text-muted">Cargando tus participaciones...</p>
            </div>
        `;

        const res = await fetch(`${API_BASE_URL}/api/participaciones/mis-eventos`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        const data = await res.json();

        if (!res.ok || !data.success) {
            container.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        ${data.error || 'Error al cargar tus participaciones'}
                    </div>
                </div>
            `;
            return;
        }

        if (!data.eventos || data.eventos.length === 0) {
            container.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle fa-3x mb-3"></i>
                        <h4>No tienes participaciones registradas</h4>
                        <p class="mb-3">Explora eventos disponibles e inscríbete</p>
                        <a href="/externo/eventos" class="btn" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); color: white; border: none; border-radius: 8px; padding: 0.75rem 1.5rem; font-weight: 500;">
                            <i class="far fa-calendar-alt mr-2"></i> Ver Eventos Disponibles
                        </a>
                    </div>
                </div>
            `;
            return;
        }

        container.innerHTML = '';

        data.eventos.forEach(participacion => {
            const evento = participacion.evento;
            const fechaInicio = evento.fecha_inicio ? new Date(evento.fecha_inicio).toLocaleDateString('es-ES', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            }) : 'Fecha no especificada';

            const fechaInscripcion = participacion.created_at ? new Date(participacion.created_at).toLocaleDateString('es-ES', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            }) : 'N/A';

            let estadoBadge = '';
            let estadoColor = '';
            let estadoIcon = '';
            if (participacion.estado === 'aprobada') {
                estadoBadge = 'Aprobada';
                estadoColor = 'success';
                estadoIcon = 'fa-check-circle';
            } else if (participacion.estado === 'rechazada') {
                estadoBadge = 'Rechazada';
                estadoColor = 'danger';
                estadoIcon = 'fa-times-circle';
            } else {
                estadoBadge = 'Pendiente';
                estadoColor = 'warning';
                estadoIcon = 'fa-clock';
            }

            const colDiv = document.createElement('div');
            colDiv.className = 'col-md-6 col-lg-4 mb-4';
            
            colDiv.innerHTML = `
                <div class="card h-100 border-0 shadow-sm" style="border-radius: 12px; transition: transform 0.2s, box-shadow 0.2s;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="card-title mb-0" style="font-size: 1.1rem; font-weight: 600; color: #0C2B44;">
                                ${evento.titulo || 'Sin título'}
                            </h5>
                            <span class="badge badge-${estadoColor}" style="${estadoColor === 'success' ? 'background-color: #00A36C !important;' : estadoColor === 'warning' ? 'background-color: #ffc107 !important;' : 'background-color: #dc3545 !important;'} color: white; padding: 0.4em 0.8em; border-radius: 8px;">
                                <i class="far ${estadoIcon} mr-1"></i>${estadoBadge}
                            </span>
                        </div>
                        
                        <p class="text-muted mb-3" style="font-size: 0.9rem; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            ${evento.descripcion || 'Sin descripción'}
                        </p>
                        
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2" style="color: #6c757d; font-size: 0.85rem;">
                                <i class="far fa-calendar-alt mr-2" style="color: #00A36C;"></i>
                                <span><strong>Fecha:</strong> ${fechaInicio}</span>
                            </div>
                            <div class="d-flex align-items-center mb-2" style="color: #6c757d; font-size: 0.85rem;">
                                <i class="far fa-calendar-check mr-2" style="color: #00A36C;"></i>
                                <span><strong>Inscrito:</strong> ${fechaInscripcion}</span>
                            </div>
                            ${evento.ciudad ? `
                                <div class="d-flex align-items-center" style="color: #6c757d; font-size: 0.85rem;">
                                    <i class="far fa-map-marker-alt mr-2" style="color: #00A36C;"></i>
                                    <span>${evento.ciudad}</span>
                                </div>
                            ` : ''}
                        </div>
                        
                        <div class="mt-3 pt-3 border-top">
                            <a href="/externo/eventos/${evento.id}/detalle" class="btn btn-sm btn-block" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); color: white; border: none; border-radius: 8px; padding: 0.5em 1.2em; font-weight: 500; transition: all 0.3s;">
                                <i class="far fa-eye mr-2"></i> Ver Detalles
                            </a>
                        </div>
                    </div>
                </div>
            `;
            
            // Efecto hover
            const card = colDiv.querySelector('.card');
            const btn = colDiv.querySelector('.btn');
            card.onmouseenter = function() {
                this.style.transform = 'translateY(-4px)';
                this.style.boxShadow = '0 8px 16px rgba(12, 43, 68, 0.15)';
                if (btn) {
                    btn.style.transform = 'scale(1.02)';
                }
            };
            card.onmouseleave = function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '0 1px 3px rgba(0,0,0,0.1)';
                if (btn) {
                    btn.style.transform = 'scale(1)';
                }
            };
            
            container.appendChild(colDiv);
        });

    } catch (error) {
        console.error('Error cargando participaciones:', error);
        container.innerHTML = `
            <div class="col-12">
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Error de conexión al cargar tus participaciones
                </div>
            </div>
        `;
    }
}
</script>
@endpush


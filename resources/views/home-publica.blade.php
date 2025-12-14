@extends('adminlte::page')

@section('title', 'UNI2 • Página Pública')

@section('content_header')
    <h1><i class="fas fa-globe-americas"></i> Bienvenido a UNI2</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-4" style="color: #0C2B44; font-weight: 700;">
                <i class="fas fa-star mr-2" style="color: #FFD700;"></i>Mega Eventos Destacados
            </h2>
        </div>
    </div>
    
    <div class="row mb-4">
        <div id="megaEventosPublicosContainer" class="col-12">
            <div class="text-center py-5">
                <div class="spinner-border text-warning" role="status" style="width: 3rem; height: 3rem;">
                    <span class="sr-only">Cargando...</span>
                </div>
                <p class="text-muted mt-3">Cargando mega eventos...</p>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">Últimos Eventos</div>
                <div class="card-body">
                    <ul>
                        <li>Campaña Médica</li>
                        <li>Festival de Reciclaje</li>
                        <li>Charla de Inclusión</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-success">
                <div class="card-header bg-success text-white">Organizaciones Destacadas</div>
                <div class="card-body">
                    <ul>
                        <li>Fundación Esperanza Viva</li>
                        <li>Manos Solidarias</li>
                        <li>Guardianes del Bosque</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script src="{{ asset('assets/js/config.js') }}"></script>
<script>
console.log("✅ Página pública cargada correctamente");

// Cargar mega eventos públicos
async function cargarMegaEventosPublicos() {
    const container = document.getElementById('megaEventosPublicosContainer');
    if (!container) return;

    try {
        const apiUrl = window.API_BASE_URL || 'http://192.168.0.7:8000';
        const res = await fetch(`${apiUrl}/api/mega-eventos/publicos`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        const data = await res.json();
        
        if (data.success && data.mega_eventos && data.mega_eventos.length > 0) {
            // Filtrar solo los próximos y ordenar por fecha
            const hoy = new Date();
            hoy.setHours(0, 0, 0, 0);
            
            const proximos = data.mega_eventos
                .filter(mega => {
                    if (!mega.fecha_inicio) return false;
                    const fechaInicio = new Date(mega.fecha_inicio);
                    fechaInicio.setHours(0, 0, 0, 0);
                    return fechaInicio >= hoy;
                })
                .sort((a, b) => new Date(a.fecha_inicio) - new Date(b.fecha_inicio))
                .slice(0, 6);

            if (proximos.length > 0) {
                let html = '<div class="row">';
                proximos.forEach(mega => {
                    const fechaInicio = new Date(mega.fecha_inicio);
                    const fechaStr = fechaInicio.toLocaleDateString('es-ES', { 
                        day: 'numeric', 
                        month: 'long', 
                        year: 'numeric' 
                    });
                    
                    // Obtener primera imagen
                    let imagenUrl = null;
                    if (mega.imagenes && Array.isArray(mega.imagenes) && mega.imagenes.length > 0) {
                        imagenUrl = mega.imagenes[0];
                    } else if (typeof mega.imagenes === 'string' && mega.imagenes.trim()) {
                        try {
                            const parsed = JSON.parse(mega.imagenes);
                            if (Array.isArray(parsed) && parsed.length > 0) {
                                imagenUrl = parsed[0];
                            }
                        } catch (e) {
                            imagenUrl = mega.imagenes;
                        }
                    }
                    
                    if (imagenUrl && !imagenUrl.startsWith('http')) {
                        if (imagenUrl.startsWith('/storage/')) {
                            imagenUrl = `${apiUrl}${imagenUrl}`;
                        } else {
                            imagenUrl = `${apiUrl}/storage/${imagenUrl}`;
                        }
                    }
                    
                    html += `
                        <div class="col-md-4 mb-4">
                            <div class="card border-0 shadow-lg" style="border-radius: 16px; overflow: hidden; transition: transform 0.3s; cursor: pointer;" 
                                 onmouseover="this.style.transform='translateY(-5px)'" 
                                 onmouseout="this.style.transform='translateY(0)'"
                                 onclick="window.location.href='/mega-evento/${mega.mega_evento_id}/qr'">
                                ${imagenUrl ? `
                                    <div style="height: 200px; overflow: hidden; position: relative;">
                                        <img src="${imagenUrl}" alt="${mega.titulo}" class="w-100 h-100" style="object-fit: cover;"
                                             onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div style="display: none; height: 200px; background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%); align-items: center; justify-content: center;">
                                            <i class="fas fa-star text-white" style="font-size: 4rem; opacity: 0.5;"></i>
                                        </div>
                                        <div class="position-absolute" style="top: 10px; right: 10px;">
                                            <span class="badge" style="background: rgba(255, 215, 0, 0.9); color: #0C2B44; font-weight: 600; padding: 0.5rem 0.75rem;">
                                                <i class="fas fa-star mr-1"></i>Mega Evento
                                            </span>
                                        </div>
                                    </div>
                                ` : `
                                    <div style="height: 200px; background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%); display: flex; align-items: center; justify-content: center; position: relative;">
                                        <i class="fas fa-star text-white" style="font-size: 4rem; opacity: 0.5;"></i>
                                        <div class="position-absolute" style="top: 10px; right: 10px;">
                                            <span class="badge" style="background: rgba(255, 255, 255, 0.9); color: #0C2B44; font-weight: 600; padding: 0.5rem 0.75rem;">
                                                <i class="fas fa-star mr-1"></i>Mega Evento
                                            </span>
                                        </div>
                                    </div>
                                `}
                                <div class="card-body p-4">
                                    <h5 class="card-title mb-3" style="font-weight: 700; color: #0C2B44; font-size: 1.1rem; line-height: 1.4; min-height: 3rem;">
                                        ${mega.titulo || 'Mega Evento'}
                                    </h5>
                                    <p class="text-muted mb-3" style="font-size: 0.9rem; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 2.5rem;">
                                        ${mega.descripcion || 'Descubre este increíble mega evento que está transformando comunidades.'}
                                    </p>
                                    <div class="mb-3">
                                        <p class="mb-1" style="font-size: 0.85rem; color: #666;">
                                            <i class="far fa-calendar mr-2" style="color: #FFD700;"></i>${fechaStr}
                                        </p>
                                        ${mega.ubicacion ? `
                                            <p class="mb-0" style="font-size: 0.85rem; color: #666;">
                                                <i class="far fa-map-marker-alt mr-2" style="color: #FFD700;"></i>${mega.ubicacion}
                                            </p>
                                        ` : ''}
                                    </div>
                                    <a href="/mega-evento/${mega.mega_evento_id}/qr" 
                                       class="btn btn-block" 
                                       style="background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%); color: white; border: none; border-radius: 8px; padding: 0.75rem; font-weight: 600;">
                                        <i class="fas fa-star mr-2"></i>Ver Detalle
                                    </a>
                                </div>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
                container.innerHTML = html;
            } else {
                container.innerHTML = `
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            <i class="fas fa-star fa-3x mb-3" style="color: #FFD700; opacity: 0.3;"></i>
                            <h5>No hay mega eventos próximos</h5>
                            <p class="mb-0">Vuelve pronto para ver los próximos mega eventos destacados</p>
                        </div>
                    </div>
                `;
            }
        } else {
            container.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="fas fa-star fa-3x mb-3" style="color: #FFD700; opacity: 0.3;"></i>
                        <h5>No hay mega eventos disponibles</h5>
                    </div>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error cargando mega eventos:', error);
        container.innerHTML = `
            <div class="col-12">
                <div class="alert alert-warning text-center">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <p class="mb-0">Error al cargar mega eventos. Por favor, intenta más tarde.</p>
                </div>
            </div>
        `;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    cargarMegaEventosPublicos();
});
</script>
@stop

let chartEventosInscritos = null;
let chartEventosAsistidos = null;

document.addEventListener("DOMContentLoaded", async () => {
    const token = localStorage.getItem("token");
    if (!token) {
        window.location.href = '/login';
        return;
    }

    await cargarPerfilUsuario();
    await cargarEstadisticas();
    await cargarEventos();
});


async function cargarPerfilUsuario() {
    const nombreStorage = localStorage.getItem("nombre_usuario");
    if (nombreStorage) {
        actualizarSaludo(nombreStorage);
    }

    const token = localStorage.getItem("token");
    if (!token) return;

    try {
        const res = await fetch(`${API_BASE_URL}/api/perfil`, {
            headers: {
                "Authorization": `Bearer ${token}`,
                "Accept": "application/json"
            },
            cache: "no-cache"
        });

        const data = await res.json();
        if (!res.ok || !data.success) return;

        const nombrePerfil = data.data?.nombre_usuario
            || data.data?.integrante_externo?.nombres
            || data.data?.integrante_externo?.nombre_completo;

        if (nombrePerfil) {
            localStorage.setItem("nombre_usuario", nombrePerfil);
            localStorage.setItem("usuario", JSON.stringify(data.data));
            actualizarSaludo(nombrePerfil);
        }
    } catch (error) {
        console.error("Error al cargar el perfil del usuario:", error);
    }
}

function actualizarSaludo(nombre) {
    const nombreUsuarioEl = document.getElementById("nombreUsuario");
    if (nombreUsuarioEl) {
        nombreUsuarioEl.textContent = nombre;
    }
}

async function cargarEstadisticas() {
    const token = localStorage.getItem("token");
    if (!token) return;

    try {
        const resStats = await fetch(`${API_BASE_URL}/api/participaciones/mis-eventos`, {
            headers: { "Authorization": `Bearer ${token}`, "Accept": "application/json" },
            cache: "no-cache"
        });

        const dataStats = await resStats.json();
        if (!resStats.ok || !dataStats.success) throw new Error(dataStats.error || 'Error al obtener estadísticas');

        const eventos = dataStats.eventos ?? [];
        const totalInscritos = eventos.length;
        const asistidos = eventos.filter(e => e.asistio).length;
        const puntosAcumulados = eventos.reduce((acc, e) => acc + (e.puntos ?? 0), 0);

        actualizarTexto("eventosInscritos", totalInscritos);
        actualizarTexto("eventosAsistidos", asistidos);
        actualizarTexto("puntosAcumulados", puntosAcumulados);
        
        // Actualizar badges
        actualizarTexto("badgeEventosInscritos", totalInscritos);
        actualizarTexto("badgeEventosAsistidos", asistidos);

        // Crear gráficas con el mismo estilo que home-ong
        crearGraficas(totalInscritos, asistidos);
    } catch (error) {
        console.error("Error al cargar estadísticas del externo:", error);
    }
}

function actualizarTexto(elementId, value) {
    const element = document.getElementById(elementId);
    if (element) {
        element.textContent = value;
    }
}

// Crear gráficas con el mismo estilo que home-ong
function crearGraficas(totalInscritos, totalAsistidos) {
    // 1. Gráfica estilo Heart Rate (línea con ondas) - Eventos Inscritos
    const ctxEventosInscritos = document.getElementById('graficaEventosInscritos');
    if (ctxEventosInscritos) {
        if (chartEventosInscritos) chartEventosInscritos.destroy();
        
        // Simular datos de frecuencia con ondas
        const dataPoints = [];
        const baseValue = Math.max(totalInscritos / 2, 20);
        for (let i = 0; i < 50; i++) {
            const wave = Math.sin(i * 0.3) * 15 + baseValue;
            dataPoints.push(wave);
        }

        chartEventosInscritos = new Chart(ctxEventosInscritos, {
            type: 'line',
            data: {
                labels: Array(50).fill(''),
                datasets: [{
                    data: dataPoints,
                    borderColor: '#7FFF7F',
                    backgroundColor: 'rgba(127, 255, 127, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: false }
                },
                scales: {
                    x: { display: false },
                    y: { display: false }
                }
            }
        });

        document.getElementById('totalEventosInscritosGrafica').textContent = totalInscritos;
    }

    // 2. Gráfica estilo Sleeping Periods (barras con puntos) - Eventos Asistidos
    const ctxEventosAsistidos = document.getElementById('graficaEventosAsistidos');
    if (ctxEventosAsistidos) {
        if (chartEventosAsistidos) chartEventosAsistidos.destroy();
        
        const dataBars = [];
        const baseValue = Math.max(totalAsistidos / 2, 10);
        for (let i = 0; i < 7; i++) {
            dataBars.push(Math.random() * baseValue + baseValue);
        }

        chartEventosAsistidos = new Chart(ctxEventosAsistidos, {
            type: 'bar',
            data: {
                labels: ['L', 'M', 'M', 'J', 'V', 'S', 'D'],
                datasets: [{
                    data: dataBars,
                    backgroundColor: 'rgba(127, 255, 127, 0.6)',
                    borderRadius: 10,
                    barThickness: 30
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: true }
                },
                scales: {
                    x: { 
                        grid: { display: false },
                        ticks: { color: '#00A36C', font: { weight: 'bold' } }
                    },
                    y: { 
                        display: false,
                        beginAtZero: true
                    }
                }
            }
        });

        document.getElementById('totalEventosAsistidosGrafica').textContent = totalAsistidos;
    }
}

async function cargarEventos() {
    const token = localStorage.getItem("token");
    const cont = document.getElementById("contenedorEventos");

    try {
        const res = await fetch(`${API_BASE_URL}/api/eventos`, {
            headers: { "Authorization": `Bearer ${token}` }
        });

        const json = await res.json();
        if (!res.ok || !json.success) {
            throw new Error(json.error || 'Error al obtener eventos');
        }

        cont.innerHTML = "";

        (json.eventos || []).forEach(e => {
            const fechaInicio = e.fecha_inicio ? new Date(e.fecha_inicio).toLocaleDateString('es-ES', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            }) : 'Fecha no especificada';
            
            cont.innerHTML += `
            <div class="card mb-3 shadow-sm" style="border-radius: 12px; border: 1px solid #F5F5F5; transition: all 0.3s;">
                <div class="card-body p-4">
                    <h5 class="mb-3" style="color: #0C2B44; font-weight: 700; font-size: 1.15rem;">${e.titulo || 'Sin título'}</h5>
                    <p class="mb-3" style="color: #333; line-height: 1.6;">${e.descripcion || 'Sin descripción'}</p>
                    <div class="mb-3">
                        <p class="mb-2" style="color: #666; font-size: 0.9rem;">
                            <i class="far fa-calendar mr-2" style="color: #00A36C;"></i><strong>Fecha:</strong> ${fechaInicio}
                        </p>
                        <p class="mb-0" style="color: #666; font-size: 0.9rem;">
                            <i class="far fa-map-marker-alt mr-2" style="color: #00A36C;"></i><strong>Ubicación:</strong> ${e.ciudad || 'No especificada'}
                        </p>
                    </div>
                    <a class="btn" href="/externo/eventos/${e.id}/detalle" style="background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%); color: white; border: none; border-radius: 8px; padding: 0.5rem 1.5rem; font-weight: 500; transition: all 0.3s;">
                        <i class="far fa-eye mr-2"></i>Ver Detalles
                    </a>
                </div>
            </div>`;
        });
        
        // Agregar efecto hover a las tarjetas
        const cards = cont.querySelectorAll('.card');
        cards.forEach(card => {
            card.onmouseenter = function() {
                this.style.transform = 'translateY(-4px)';
                this.style.boxShadow = '0 8px 16px rgba(12, 43, 68, 0.15)';
                this.style.borderColor = '#00A36C';
            };
            card.onmouseleave = function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '0 2px 8px rgba(0, 0, 0, 0.08)';
                this.style.borderColor = '#F5F5F5';
            };
        });

    } catch (error) {
        console.error("Error al cargar eventos disponibles:", error);
        cont.innerHTML = `<p class="text-danger">Error al cargar eventos.</p>`;
    }
}

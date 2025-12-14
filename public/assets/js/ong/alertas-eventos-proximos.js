// ==========================================
// 🔔 Sistema de Alertas para Eventos Próximos
// ==========================================

(function() {
    'use strict';

    const token = localStorage.getItem('token');
    const tipoUsuario = localStorage.getItem('tipo_usuario');
    const userId = parseInt(localStorage.getItem("id_entidad") || localStorage.getItem("id_usuario"), 10);

    // Solo ejecutar si el usuario está autenticado
    if (!token || !userId || userId <= 0) {
        return;
    }

    // IDs de alertas ya mostradas (para evitar duplicados)
    const alertasMostradas = new Set();

    /**
     * Obtener alertas de eventos próximos
     */
    async function obtenerAlertasEventosProximos() {
        try {
            const API_BASE_URL = window.API_BASE_URL || 'http://192.168.0.7:8000';
            
            // Para ONG
            if (tipoUsuario === 'ONG') {
                const res = await fetch(`${API_BASE_URL}/api/notificaciones`, {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });

                if (!res.ok) return;

                const data = await res.json();
                if (data.success && data.alertas_eventos_proximos && data.alertas_eventos_proximos.length > 0) {
                    mostrarAlertas(data.alertas_eventos_proximos);
                }
            } 
            // Para usuarios externos
            else if (tipoUsuario === 'EXTERNO') {
                // Obtener notificaciones del usuario externo
                const res = await fetch(`${API_BASE_URL}/api/dashboard-externo/notificaciones`, {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });

                if (!res.ok) return;

                const data = await res.json();
                if (data.success && data.alertas_eventos_proximos && data.alertas_eventos_proximos.length > 0) {
                    mostrarAlertas(data.alertas_eventos_proximos);
                }
            }
            // Para empresas
            else if (tipoUsuario === 'EMPRESA') {
                const res = await fetch(`${API_BASE_URL}/api/empresas/notificaciones`, {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });

                if (!res.ok) return;

                const data = await res.json();
                if (data.success && data.alertas_eventos_proximos && data.alertas_eventos_proximos.length > 0) {
                    mostrarAlertas(data.alertas_eventos_proximos);
                }
            }
        } catch (error) {
            // Silenciar errores de conexión
            if (!error.message || (!error.message.includes('Failed to fetch') && !error.message.includes('ERR_'))) {
                console.warn('Error obteniendo alertas de eventos próximos:', error);
            }
        }
    }

    /**
     * Mostrar alertas en pantalla
     */
    function mostrarAlertas(alertas) {
        // Crear contenedor de alertas si no existe
        let alertasContainer = document.getElementById('alertas-eventos-proximos-container');
        if (!alertasContainer) {
            alertasContainer = document.createElement('div');
            alertasContainer.id = 'alertas-eventos-proximos-container';
            alertasContainer.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 10000;
                max-width: 400px;
                width: 100%;
            `;
            document.body.appendChild(alertasContainer);
        }

        alertas.forEach(alerta => {
            // Evitar mostrar la misma alerta dos veces
            if (alertasMostradas.has(alerta.id)) {
                return;
            }
            alertasMostradas.add(alerta.id);

            const alertaElement = document.createElement('div');
            alertaElement.className = 'alerta-evento-proximo';
            alertaElement.dataset.alertaId = alerta.id;
            alertaElement.style.cssText = `
                background: linear-gradient(135deg, #00A36C 0%, #0C2B44 100%);
                color: white;
                padding: 1.25rem 1.5rem;
                border-radius: 12px;
                box-shadow: 0 8px 24px rgba(0, 163, 108, 0.3);
                margin-bottom: 15px;
                animation: slideInRight 0.4s ease-out;
                border-left: 4px solid #00A36C;
                position: relative;
                cursor: pointer;
                transition: all 0.3s ease;
            `;

            alertaElement.onmouseover = function() {
                this.style.transform = 'translateX(-5px)';
                this.style.boxShadow = '0 12px 32px rgba(0, 163, 108, 0.4)';
            };
            alertaElement.onmouseout = function() {
                this.style.transform = 'translateX(0)';
                this.style.boxShadow = '0 8px 24px rgba(0, 163, 108, 0.3)';
            };

            const eventoId = alerta.evento_id || '';
            // Determinar URL según el tipo de usuario
            let eventoUrl = '#';
            if (eventoId) {
                if (tipoUsuario === 'ONG') {
                    eventoUrl = `/ong/eventos/${eventoId}/detalle`;
                } else if (tipoUsuario === 'EXTERNO') {
                    eventoUrl = `/externo/eventos/${eventoId}/detalle`;
                } else if (tipoUsuario === 'EMPRESA') {
                    eventoUrl = `/empresa/eventos/${eventoId}/detalle`;
                }
            }

            alertaElement.innerHTML = `
                <div style="display: flex; align-items: flex-start; gap: 12px;">
                    <div style="
                        width: 40px;
                        height: 40px;
                        background: rgba(255, 255, 255, 0.2);
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        flex-shrink: 0;
                    ">
                        <i class="fas fa-bell" style="font-size: 20px; color: white;"></i>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <strong style="
                            display: block;
                            font-size: 16px;
                            font-weight: 700;
                            margin-bottom: 4px;
                            line-height: 1.3;
                        ">${alerta.titulo || 'Evento por Iniciar'}</strong>
                        <p style="
                            margin: 0;
                            font-size: 13px;
                            opacity: 0.95;
                            line-height: 1.4;
                        ">${alerta.mensaje || alerta.evento_titulo || 'Un evento está por comenzar'}</p>
                        ${eventoId ? `
                            <a href="${eventoUrl}" style="
                                display: inline-block;
                                margin-top: 8px;
                                padding: 6px 12px;
                                background: rgba(255, 255, 255, 0.2);
                                border-radius: 6px;
                                color: white;
                                text-decoration: none;
                                font-size: 12px;
                                font-weight: 600;
                                transition: all 0.2s;
                            " onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                                Ver evento <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        ` : ''}
                    </div>
                    <button type="button" onclick="this.closest('.alerta-evento-proximo').remove(); marcarAlertaComoLeida(${alerta.id});" style="
                        background: rgba(255, 255, 255, 0.2);
                        border: none;
                        color: white;
                        width: 28px;
                        height: 28px;
                        border-radius: 50%;
                        cursor: pointer;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 14px;
                        transition: all 0.2s;
                        flex-shrink: 0;
                    " onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;

            // Agregar evento de clic en toda la alerta
            if (eventoId) {
                alertaElement.onclick = function(e) {
                    if (e.target.tagName !== 'BUTTON' && e.target.tagName !== 'I' && !e.target.closest('button')) {
                        window.location.href = eventoUrl;
                    }
                };
            }

            alertasContainer.appendChild(alertaElement);

            // Auto-remover después de 10 segundos
            setTimeout(() => {
                if (alertaElement.parentNode) {
                    alertaElement.style.animation = 'slideOutRight 0.3s ease-in forwards';
                    setTimeout(() => {
                        if (alertaElement.parentNode) {
                            alertaElement.remove();
                        }
                    }, 300);
                }
            }, 10000);
        });

        // Agregar animaciones CSS si no existen
        if (!document.getElementById('alertas-animations')) {
            const style = document.createElement('style');
            style.id = 'alertas-animations';
            style.textContent = `
                @keyframes slideInRight {
                    from {
                        transform: translateX(100%);
                        opacity: 0;
                    }
                    to {
                        transform: translateX(0);
                        opacity: 1;
                    }
                }
                @keyframes slideOutRight {
                    from {
                        transform: translateX(0);
                        opacity: 1;
                    }
                    to {
                        transform: translateX(100%);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);
        }
    }

    /**
     * Marcar alerta como leída
     */
    window.marcarAlertaComoLeida = async function(alertaId) {
        try {
            const API_BASE_URL = window.API_BASE_URL || 'http://192.168.0.7:8000';
            let endpoint = '';
            
            // Determinar endpoint según tipo de usuario
            if (tipoUsuario === 'ONG') {
                endpoint = `${API_BASE_URL}/api/notificaciones/${alertaId}/leida`;
            } else if (tipoUsuario === 'EXTERNO') {
                // Para usuarios externos, usar el mismo endpoint pero con validación diferente
                endpoint = `${API_BASE_URL}/api/notificaciones/${alertaId}/leida`;
            } else if (tipoUsuario === 'EMPRESA') {
                endpoint = `${API_BASE_URL}/api/empresas/notificaciones/${alertaId}/leida`;
            }
            
            if (endpoint) {
                await fetch(endpoint, {
                    method: 'PUT',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });
            }
        } catch (error) {
            // Silenciar errores
        }
    };

    // Cargar alertas al iniciar
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(obtenerAlertasEventosProximos, 1000);
        });
    } else {
        setTimeout(obtenerAlertasEventosProximos, 1000);
    }

    // Actualizar alertas cada 2 minutos
    setInterval(obtenerAlertasEventosProximos, 120000);
})();

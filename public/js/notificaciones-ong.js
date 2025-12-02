/**
 * Script global para mostrar el icono de notificaciones en todas las pantallas de ONG
 * VERSIÓN MEJORADA CON PRUEBAS Y DEBUGGING
 */

(function() {
    'use strict';
    
    console.log('🔔 Script de notificaciones iniciado');
    
    const tipoUsuario = localStorage.getItem('tipo_usuario');
    console.log('👤 Tipo de usuario:', tipoUsuario);
    
    if (tipoUsuario !== 'ONG') {
        console.log('⚠️ No es ONG, saliendo...');
        return;
    }
    
    let contadorGlobal = 0; // Contador global para pruebas
    
    function crearIconoNotificaciones() {
        console.log('🔍 Buscando icono existente...');
        
        // Verificar si ya existe
        const existente = document.getElementById('notificacionesNavItem');
        if (existente) {
            console.log('✅ Icono ya existe');
            return existente;
        }
        
        console.log('🔨 Creando nuevo icono...');
        
        // Buscar el navbar - intentar múltiples selectores
        let navbarNav = document.querySelector('.main-header .navbar-nav');
        if (!navbarNav) {
            navbarNav = document.querySelector('.navbar-nav');
        }
        if (!navbarNav) {
            navbarNav = document.querySelector('.main-header nav ul');
        }
        if (!navbarNav) {
            console.warn('⚠️ No se encontró navbar, reintentando...');
            setTimeout(crearIconoNotificaciones, 500);
            return null;
        }
        
        console.log('✅ Navbar encontrado:', navbarNav);
        
        // Buscar el menú de usuario (círculo gris) - múltiples formas
        let userMenu = null;
        
        // Buscar por imagen de usuario
        const userImage = navbarNav.querySelector('img.user-image, .user-image img, img[alt*="User"], img[alt*="Usuario"]');
        if (userImage) {
            userMenu = userImage.closest('.nav-item');
            console.log('✅ Menú de usuario encontrado por imagen');
        }
        
        // Buscar por dropdown
        if (!userMenu) {
            const dropdown = navbarNav.querySelector('a[data-toggle="dropdown"]');
            if (dropdown) {
                userMenu = dropdown.closest('.nav-item');
                console.log('✅ Menú de usuario encontrado por dropdown');
            }
        }
        
        // Buscar el último nav-item como fallback
        if (!userMenu) {
            const allItems = Array.from(navbarNav.querySelectorAll('.nav-item'));
            if (allItems.length > 0) {
                userMenu = allItems[allItems.length - 1];
                console.log('✅ Usando último nav-item como referencia');
            }
        }
        
        // Crear el elemento del icono
        const navItem = document.createElement('li');
        navItem.className = 'nav-item';
        navItem.id = 'notificacionesNavItem';
        navItem.style.cssText = 'display: flex !important; align-items: center; visibility: visible !important; opacity: 1 !important; margin-right: 10px !important;';
        
        const link = document.createElement('a');
        link.href = '/ong/notificaciones';
        link.className = 'nav-link position-relative';
        link.id = 'notificacionesIcono';
        link.title = 'Notificaciones';
        link.style.cssText = 'display: flex !important; align-items: center; justify-content: center; padding: 0.5rem 0.75rem !important; min-width: 45px; height: 45px; color: #6c757d !important; cursor: pointer; text-decoration: none !important; border-radius: 50%; background-color: transparent;';
        
        const bellIcon = document.createElement('i');
        bellIcon.className = 'fas fa-bell';
        bellIcon.style.cssText = 'font-size: 1.3rem !important; display: block !important; color: #6c757d !important;';
        
        const badge = document.createElement('span');
        badge.className = 'badge badge-danger position-absolute';
        badge.id = 'contadorNotificaciones';
        // Estilo TikTok: número grande y visible
        badge.style.cssText = 'top: -8px; right: -8px; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; font-weight: 900; padding: 4px 8px; min-width: 22px; height: 22px; border-radius: 11px; z-index: 1000; background: linear-gradient(135deg, #ff0050 0%, #ff4081 100%) !important; color: white !important; box-shadow: 0 3px 8px rgba(255, 0, 80, 0.5), 0 0 0 2px white; border: 2px solid white; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; letter-spacing: -0.5px;';
        badge.textContent = '0';
        
        link.appendChild(bellIcon);
        link.appendChild(badge);
        navItem.appendChild(link);
        
        // Insertar antes del menú de usuario si existe, sino al final
        if (userMenu && userMenu !== navItem) {
            navbarNav.insertBefore(navItem, userMenu);
            console.log('✅ Icono insertado antes del menú de usuario');
        } else {
            navbarNav.appendChild(navItem);
            console.log('✅ Icono insertado al final del navbar');
        }
        
        // Forzar visibilidad con múltiples métodos
        navItem.style.setProperty('display', 'flex', 'important');
        navItem.style.setProperty('visibility', 'visible', 'important');
        navItem.style.setProperty('opacity', '1', 'important');
        navItem.style.setProperty('position', 'relative', 'important');
        
        link.style.setProperty('display', 'flex', 'important');
        link.style.setProperty('visibility', 'visible', 'important');
        link.style.setProperty('opacity', '1', 'important');
        
        // Agregar hover effect
        link.addEventListener('mouseenter', () => {
            link.style.backgroundColor = 'rgba(0, 0, 0, 0.05)';
        });
        link.addEventListener('mouseleave', () => {
            link.style.backgroundColor = 'transparent';
        });
        
        console.log('✅ Icono de notificaciones creado y visible en:', window.location.pathname);
        console.log('📍 Posición del icono:', navItem.getBoundingClientRect());
        
        return navItem;
    }
    
    // ================================
    // Actualizar header con nombre/avatar desde la API de dashboard ONG
    // ================================
    async function actualizarHeaderOng() {
        try {
            const nombreSpan = document.getElementById('headerNombreOng');
            const avatarImg = document.getElementById('headerAvatarOng');
            const inicialSpan = document.getElementById('headerAvatarInicialOng');

            if (!nombreSpan) return;

            const token = localStorage.getItem('token');
            if (!token) return;

            let API_BASE_URL = window.location.origin;
            if (typeof window !== 'undefined' && window.API_BASE_URL) {
                API_BASE_URL = window.API_BASE_URL;
            }

            const res = await fetch(`${API_BASE_URL}/api/dashboard-ong/estadisticas-generales`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                },
                cache: 'no-store'
            });

            const data = await res.json();
            if (!data || data.success === false) {
                console.warn('⚠️ No se pudo obtener datos del dashboard');
                return;
            }

            // Obtener nombre: prioridad ONG > usuario
            const nombre = (data.ong && data.ong.nombre) || (data.usuario && data.usuario.nombre_usuario) || null;
            
            // Obtener foto: prioridad ONG > usuario
            const foto = (data.ong && data.ong.foto_perfil) || (data.usuario && data.usuario.foto_perfil) || null;

            // Actualizar nombre
            if (nombre && nombreSpan) {
                nombreSpan.textContent = nombre;
            }

            // Actualizar avatar
            if (avatarImg && inicialSpan) {
                if (foto && foto.trim() !== '') {
                    // Hay foto: mostrar imagen, ocultar inicial
                    avatarImg.src = foto;
                    avatarImg.onerror = function() {
                        // Si la imagen falla al cargar, mostrar inicial
                        avatarImg.style.display = 'none';
                        inicialSpan.style.display = 'block';
                        if (nombre) {
                            inicialSpan.textContent = nombre.charAt(0).toUpperCase();
                        }
                    };
                    avatarImg.onload = function() {
                        // Imagen cargada correctamente
                        avatarImg.style.display = 'block';
                        inicialSpan.style.display = 'none';
                    };
                    avatarImg.style.display = 'block';
                    inicialSpan.style.display = 'none';
                } else {
                    // No hay foto: mostrar inicial, ocultar imagen
                    avatarImg.style.display = 'none';
                    inicialSpan.style.display = 'block';
                    if (nombre) {
                        inicialSpan.textContent = nombre.charAt(0).toUpperCase();
                    }
                }
            }
        } catch (e) {
            console.warn('⚠️ Error actualizando header ONG:', e);
        }
    }
    
    // Función para actualizar el contador
    async function actualizarContador() {
        const token = localStorage.getItem('token');
        if (!token) {
            console.warn('⚠️ No hay token');
            return;
        }
        
        try {
            let API_BASE_URL = window.location.origin;
            if (typeof window !== 'undefined' && window.API_BASE_URL) {
                API_BASE_URL = window.API_BASE_URL;
            }
            
            console.log('🔄 Actualizando contador desde:', `${API_BASE_URL}/api/notificaciones/contador`);
            
            const res = await fetch(`${API_BASE_URL}/api/notificaciones/contador`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'Cache-Control': 'no-cache',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                cache: 'no-store'
            });
            
            if (!res.ok) {
                console.warn('⚠️ Error HTTP:', res.status, res.statusText);
                if (res.status === 401) {
                    console.warn('⚠️ Token inválido o expirado');
                }
                return;
            }
            
            const data = await res.json();
            console.log('📊 Respuesta del servidor:', data);
            
            if (data.success !== false && data.no_leidas !== undefined) {
                const contador = parseInt(data.no_leidas) || 0;
                contadorGlobal = contador;
                
                console.log('🔢 Contador recibido:', contador);
                
                // Buscar todos los badges
                const badges = document.querySelectorAll('#contadorNotificaciones');
                console.log('🏷️ Badges encontrados:', badges.length);
                
                // Formatear número estilo TikTok (mostrar "999+" si es mayor a 999)
                const mostrarNumero = contador > 999 ? '999+' : contador.toString();
                
                badges.forEach((badge, index) => {
                    console.log(`🏷️ Actualizando badge ${index + 1}:`, badge);
                    
                    if (contador > 0) {
                        badge.textContent = mostrarNumero;
                        badge.style.display = 'flex';
                        badge.style.visibility = 'visible';
                        badge.style.opacity = '1';
                        // Aplicar estilo TikTok
                        badge.style.background = 'linear-gradient(135deg, #ff0050 0%, #ff4081 100%)';
                        badge.style.color = 'white';
                        badge.style.fontWeight = '900';
                        badge.style.fontSize = '0.85rem';
                        badge.style.boxShadow = '0 3px 8px rgba(255, 0, 80, 0.5), 0 0 0 2px white';
                        badge.style.border = '2px solid white';
                        badge.style.top = '-8px';
                        badge.style.right = '-8px';
                        badge.style.minWidth = '22px';
                        badge.style.height = '22px';
                        badge.style.borderRadius = '11px';
                        console.log(`✅ Badge ${index + 1} actualizado a:`, badge.textContent);
                    } else {
                        badge.style.display = 'none';
                        badge.style.visibility = 'hidden';
                        badge.style.opacity = '0';
                        console.log(`❌ Badge ${index + 1} ocultado (sin notificaciones)`);
                    }
                });
                
                // Si no hay badges, crear uno de prueba
                if (badges.length === 0) {
                    console.warn('⚠️ No se encontraron badges, creando uno de prueba...');
                    const icono = document.getElementById('notificacionesIcono');
                    if (icono) {
                        const badgePrueba = document.createElement('span');
                        badgePrueba.className = 'badge badge-danger position-absolute';
                        badgePrueba.id = 'contadorNotificaciones';
                        const mostrarNumero = contador > 999 ? '999+' : contador.toString();
                        badgePrueba.style.cssText = 'top: -8px; right: -8px; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; font-weight: 900; padding: 4px 8px; min-width: 22px; height: 22px; border-radius: 11px; z-index: 1000; background: linear-gradient(135deg, #ff0050 0%, #ff4081 100%) !important; color: white !important; box-shadow: 0 3px 8px rgba(255, 0, 80, 0.5), 0 0 0 2px white; border: 2px solid white; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; letter-spacing: -0.5px;';
                        badgePrueba.textContent = mostrarNumero;
                        icono.appendChild(badgePrueba);
                        console.log('✅ Badge de prueba creado');
                    }
                }
            } else {
                console.warn('⚠️ Respuesta inválida del servidor:', data);
            }
        } catch (error) {
            console.error('❌ Error actualizando contador:', error);
        }
    }
    
    // Función de inicialización
    function inicializar() {
        console.log('🚀 Inicializando sistema de notificaciones...');
        
        // Solo mantenemos el sistema de contador, ya no insertamos un nuevo icono
            setTimeout(actualizarContador, 500);
            setTimeout(actualizarContador, 1500);

        // Actualizar header (avatar + nombre) una vez cargado
        actualizarHeaderOng();
    }
    
    // Ejecutar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', inicializar);
    } else {
        inicializar();
    }
    
    // Múltiples intentos para asegurar que se cree
    setTimeout(crearIconoNotificaciones, 100);
    setTimeout(crearIconoNotificaciones, 300);
    setTimeout(crearIconoNotificaciones, 500);
    setTimeout(crearIconoNotificaciones, 1000);
    setTimeout(crearIconoNotificaciones, 2000);
    
    // Actualizar contador múltiples veces
    setTimeout(actualizarContador, 800);
    setTimeout(actualizarContador, 1500);
    setTimeout(actualizarContador, 2500);
    
    // Actualizar contador cada 5 segundos
    setInterval(actualizarContador, 5000);
    
    // Actualizar cuando la página recupera el foco
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) {
            console.log('👁️ Página visible, actualizando contador...');
            actualizarContador();
        }
    });
    
    window.addEventListener('focus', () => {
        console.log('🎯 Ventana con foco, actualizando contador...');
        actualizarContador();
    });
    
    // Observar cambios en el DOM
    const observer = new MutationObserver((mutations) => {
        if (!document.getElementById('notificacionesNavItem')) {
            console.log('🔄 DOM cambió, recreando icono...');
            crearIconoNotificaciones();
        }
    });
    
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
    
    // Exponer función de prueba global
    window.probarNotificaciones = function(numero) {
        const badge = document.getElementById('contadorNotificaciones');
        if (badge) {
            badge.textContent = numero >= 10 ? '10+' : numero.toString();
            badge.style.display = 'flex';
            badge.style.visibility = 'visible';
            badge.style.opacity = '1';
            console.log('✅ Badge actualizado manualmente a:', numero);
        } else {
            console.error('❌ No se encontró el badge');
        }
    };
    
    console.log('✅ Sistema de notificaciones cargado. Usa window.probarNotificaciones(numero) para probar.');
})();

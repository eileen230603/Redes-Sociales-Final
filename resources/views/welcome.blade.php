<!DOCTYPE html>
<html lang="es">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UNI2 • Conectando Comunidades, Transformando Vidas</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'brand-primario': '#0C2B44',
                        'brand-acento': '#00A36C',
                        'brand-blanco': '#FFFFFF',
                        'brand-gris-oscuro': '#333333',
                        'brand-gris-suave': '#F5F5F5'
                    }
                }
            }
        }
    </script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
            <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        /* Animaciones */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-40px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(40px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-20px);
            }
        }
        
        @keyframes pulse-glow {
            0%, 100% {
                box-shadow: 0 0 20px rgba(12, 43, 68, 0.3);
            }
            50% {
                box-shadow: 0 0 40px rgba(12, 43, 68, 0.6);
            }
        }
        
        @keyframes gradient-shift {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }
        
        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
        }
        
        .animate-fade-in-left {
            animation: fadeInLeft 0.8s ease-out forwards;
        }
        
        .animate-fade-in-right {
            animation: fadeInRight 0.8s ease-out forwards;
        }
        
        .animate-scale-in {
            animation: scaleIn 0.6s ease-out forwards;
        }
        
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        
        .animate-pulse-glow {
            animation: pulse-glow 3s ease-in-out infinite;
        }
        
        .gradient-animated {
            background-size: 200% 200%;
            animation: gradient-shift 8s ease infinite;
        }
        
        /* Glassmorphism */
        .glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        /* Hover Effects */
        .hover-lift {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .hover-lift:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        
        .hover-glow:hover {
            box-shadow: 0 0 30px rgba(12, 43, 68, 0.4);
        }
        
        /* Scroll Animations */
        .scroll-animate {
            opacity: 0;
            transform: translateY(40px);
            transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .scroll-animate.animated {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Parallax Effect */
        .parallax {
            transition: transform 0.3s ease-out;
        }
            </style>
    </head>
<body class="font-sans antialiased bg-white overflow-x-hidden">
    
    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 bg-white/95 backdrop-blur-md shadow-sm z-50 transition-all duration-300" id="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('assets/img/UNI2.png') }}" alt="UNI2" class="h-12 w-auto transition-transform hover:scale-110">
                    <span class="text-2xl font-bold bg-gradient-to-r from-brand-primario to-brand-acento bg-clip-text text-transparent">UNI2</span>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#sobre" class="text-brand-gris-oscuro hover:text-brand-primario transition font-medium">Sobre el Proyecto</a>
                    <a href="#como-funciona" class="text-brand-gris-oscuro hover:text-brand-primario transition font-medium">Cómo Funciona</a>
                    <a href="#servicios" class="text-brand-gris-oscuro hover:text-brand-primario transition font-medium">Servicios</a>
                    <a href="#beneficios" class="text-brand-gris-oscuro hover:text-brand-primario transition font-medium">Beneficios</a>
                    <a href="{{ route('login') }}" class="text-brand-gris-oscuro hover:text-brand-primario transition font-medium">Iniciar Sesión</a>
                    <a href="{{ route('login') }}" class="px-6 py-2.5 bg-gradient-to-r from-brand-primario to-brand-acento text-white rounded-xl font-semibold hover:shadow-lg hover:scale-105 transition-all">
                        Registrarse
                    </a>
                </div>
                <div class="md:hidden">
                    <button id="mobileMenuBtn" class="text-brand-gris-oscuro hover:text-brand-primario transition p-2">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div id="mobileMenu" class="hidden md:hidden bg-white border-t shadow-lg">
            <div class="px-4 py-6 space-y-4">
                <a href="#sobre" class="block text-brand-gris-oscuro hover:text-brand-primario font-medium py-2">Sobre el Proyecto</a>
                <a href="#como-funciona" class="block text-brand-gris-oscuro hover:text-brand-primario font-medium py-2">Cómo Funciona</a>
                <a href="#servicios" class="block text-brand-gris-oscuro hover:text-brand-primario font-medium py-2">Servicios</a>
                <a href="#beneficios" class="block text-brand-gris-oscuro hover:text-brand-primario font-medium py-2">Beneficios</a>
                <a href="{{ route('login') }}" class="block text-brand-gris-oscuro hover:text-brand-primario font-medium py-2">Iniciar Sesión</a>
                <a href="{{ route('login') }}" class="block px-6 py-3 bg-gradient-to-r from-brand-primario to-brand-acento text-white rounded-xl text-center font-semibold">
                    Registrarse
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-24 md:pt-40 md:pb-32 bg-gradient-to-br from-brand-primario via-brand-primario to-brand-acento gradient-animated overflow-hidden">
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-white/10 rounded-full blur-3xl animate-float"></div>
            <div class="absolute bottom-1/4 right-1/4 w-80 h-80 bg-white/10 rounded-full blur-3xl animate-float" style="animation-delay: 2s;"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-brand-acento/20 rounded-full blur-3xl animate-pulse"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <!-- Imagen izquierda -->
                <div class="animate-fade-in-left flex justify-center md:justify-start">
                    <img src="{{ asset('assets/img/iniii.png') }}" alt="UNI2 Logo" class="w-full max-w-md h-auto animate-float drop-shadow-2xl">
                </div>
                
                <!-- Contenido derecho -->
                <div class="animate-fade-in-right text-center md:text-left">
                    <div class="inline-block mb-6">
                        <span class="px-4 py-2 bg-white/20 backdrop-blur-md rounded-full text-white text-sm font-semibold">
                            <i class="fas fa-rocket mr-2"></i> Plataforma de Impacto Social
                        </span>
                    </div>
                    <h1 class="text-5xl md:text-7xl font-extrabold text-white mb-6 leading-tight">
                        Conectando Comunidades,<br>
                        <span class="text-brand-acento drop-shadow-lg">Transformando Vidas</span>
                    </h1>
                    <p class="text-xl md:text-2xl text-white/95 mb-12 leading-relaxed font-light">
                        La plataforma que une ONGs, empresas y voluntarios para crear eventos que generan <strong>impacto real</strong> y <strong>medible</strong> en nuestras comunidades.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-5 justify-center md:justify-start items-center mb-16">
                        <a href="{{ route('login') }}" class="group px-10 py-5 bg-white text-brand-primario rounded-2xl font-bold text-lg hover:shadow-2xl transition-all transform hover:scale-105 animate-pulse-glow">
                            <i class="fas fa-sign-in-alt mr-2 group-hover:translate-x-1 transition-transform"></i> Iniciar Sesión
                        </a>
                        <a href="{{ route('login') }}" class="px-10 py-5 bg-brand-acento text-white rounded-2xl font-bold text-lg hover:shadow-2xl transition-all transform hover:scale-105">
                            <i class="fas fa-user-plus mr-2"></i> Crear Cuenta Gratis
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
            <a href="#sobre" class="text-white/70 hover:text-white transition">
                <i class="fas fa-chevron-down text-3xl"></i>
            </a>
        </div>
    </section>

    <!-- Estadísticas -->
    <section class="py-16 bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center scroll-animate">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-brand-primario to-brand-acento rounded-2xl mb-4 shadow-lg">
                        <i class="fas fa-calendar-check text-white text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-brand-gris-oscuro mb-2">Eventos Exitosos</h3>
                    <p class="text-brand-gris-oscuro/70">Más de 500 eventos comunitarios gestionados exitosamente</p>
                </div>
                <div class="text-center scroll-animate">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-brand-acento to-brand-primario rounded-2xl mb-4 shadow-lg">
                        <i class="fas fa-users text-white text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-brand-gris-oscuro mb-2">Comunidad Activa</h3>
                    <p class="text-brand-gris-oscuro/70">Más de 2,500 voluntarios comprometidos con el cambio</p>
                </div>
                <div class="text-center scroll-animate">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-brand-primario to-brand-acento rounded-2xl mb-4 shadow-lg">
                        <i class="fas fa-trophy text-white text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-brand-gris-oscuro mb-2">Impacto Medible</h3>
                    <p class="text-brand-gris-oscuro/70">Sistema de métricas para evaluar el impacto real</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Sobre el Proyecto -->
    <section id="sobre" class="py-24 bg-gradient-to-b from-white to-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-16 items-center">
                <div class="scroll-animate">
                    <div class="inline-block mb-4">
                        <span class="px-4 py-2 bg-brand-primario/10 text-brand-primario rounded-full text-sm font-semibold">
                            Sobre UNI2
                        </span>
                    </div>
                    <h2 class="text-4xl md:text-5xl font-bold text-brand-gris-oscuro mb-6 leading-tight">
                        ¿Qué es <span class="bg-gradient-to-r from-brand-primario to-brand-acento bg-clip-text text-transparent">UNI2</span>?
                    </h2>
                    <p class="text-lg text-brand-gris-oscuro/80 mb-6 leading-relaxed">
                        UNI2 es una <strong>plataforma innovadora</strong> diseñada para conectar organizaciones sin fines de lucro (ONGs), empresas comprometidas con la responsabilidad social y voluntarios apasionados por generar cambios positivos en sus comunidades.
                    </p>
                    <p class="text-lg text-brand-gris-oscuro/80 mb-8 leading-relaxed">
                        Facilitamos la creación, gestión y participación en eventos comunitarios y megaeventos, permitiendo que cada actor del ecosistema social pueda contribuir de manera efectiva y medible al bienestar de nuestras comunidades.
                    </p>
                    <div class="grid grid-cols-2 gap-4 mb-8">
                        <div class="flex items-center space-x-3 p-4 bg-white rounded-xl shadow-sm hover-lift">
                            <div class="w-12 h-12 bg-brand-primario/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-check-circle text-brand-primario text-xl"></i>
                            </div>
                            <span class="font-semibold text-brand-gris-oscuro">Gestión Simplificada</span>
                        </div>
                        <div class="flex items-center space-x-3 p-4 bg-white rounded-xl shadow-sm hover-lift">
                            <div class="w-12 h-12 bg-brand-acento/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-chart-line text-brand-acento text-xl"></i>
                            </div>
                            <span class="font-semibold text-brand-gris-oscuro">Impacto Medible</span>
                        </div>
                        <div class="flex items-center space-x-3 p-4 bg-white rounded-xl shadow-sm hover-lift">
                            <div class="w-12 h-12 bg-brand-primario/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-users text-brand-primario text-xl"></i>
                            </div>
                            <span class="font-semibold text-brand-gris-oscuro">Comunidad Activa</span>
                        </div>
                        <div class="flex items-center space-x-3 p-4 bg-white rounded-xl shadow-sm hover-lift">
                            <div class="w-12 h-12 bg-brand-acento/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-shield-alt text-brand-acento text-xl"></i>
                            </div>
                            <span class="font-semibold text-brand-gris-oscuro">Seguro y Confiable</span>
                        </div>
                    </div>
                    <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-brand-primario to-brand-acento text-white rounded-xl font-semibold hover:shadow-lg transition-all">
                        Comenzar Ahora <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
                <div class="scroll-animate">
                    <div class="relative">
                        <div class="absolute inset-0 bg-gradient-to-br from-brand-primario/20 to-brand-acento/20 rounded-3xl transform rotate-6"></div>
                        <div class="relative bg-white rounded-3xl p-8 shadow-2xl">
                            <div class="grid grid-cols-3 gap-4 mb-6">
                                <div class="text-center bg-gradient-to-br from-brand-primario to-brand-acento rounded-2xl p-6 text-white hover-lift">
                                    <i class="fas fa-hands-helping text-4xl mb-3"></i>
                                    <p class="font-bold text-lg">ONGs</p>
                                    <p class="text-sm opacity-90">Organizaciones</p>
                                </div>
                                <div class="text-center bg-gradient-to-br from-brand-acento to-brand-primario rounded-2xl p-6 text-white hover-lift">
                                    <i class="fas fa-building text-4xl mb-3"></i>
                                    <p class="font-bold text-lg">Empresas</p>
                                    <p class="text-sm opacity-90">RSE</p>
                                </div>
                                <div class="text-center bg-gradient-to-br from-brand-primario to-brand-acento rounded-2xl p-6 text-white hover-lift">
                                    <i class="fas fa-users text-4xl mb-3"></i>
                                    <p class="font-bold text-lg">Voluntarios</p>
                                    <p class="text-sm opacity-90">Comunidad</p>
                                </div>
                            </div>
                            <div class="text-center bg-gradient-to-br from-brand-primario to-brand-acento rounded-2xl p-8 text-white">
                                <i class="fas fa-heart text-5xl mb-4 animate-pulse"></i>
                                <p class="text-2xl font-bold mb-2">Impacto Real</p>
                                <p class="text-sm opacity-90">Transformando comunidades juntos</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Cómo Funciona -->
    <section id="como-funciona" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 scroll-animate">
                <div class="inline-block mb-4">
                    <span class="px-4 py-2 bg-brand-acento/10 text-brand-acento rounded-full text-sm font-semibold">
                        Proceso Simple
                    </span>
                </div>
                <h2 class="text-4xl md:text-5xl font-bold text-brand-gris-oscuro mb-4">
                    ¿Cómo <span class="bg-gradient-to-r from-brand-primario to-brand-acento bg-clip-text text-transparent">Funciona</span>?
                </h2>
                <p class="text-xl text-brand-gris-oscuro/70 max-w-2xl mx-auto">
                    En solo 4 pasos simples, puedes comenzar a generar impacto en tu comunidad
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="scroll-animate text-center">
                    <div class="relative mb-6">
                        <div class="absolute inset-0 bg-gradient-to-br from-brand-primario to-brand-acento rounded-2xl transform rotate-6 opacity-20"></div>
                        <div class="relative w-24 h-24 bg-gradient-to-br from-brand-primario to-brand-acento rounded-2xl flex items-center justify-center mx-auto shadow-xl">
                            <span class="text-4xl font-bold text-white">1</span>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-brand-gris-oscuro mb-3">Regístrate</h3>
                    <p class="text-brand-gris-oscuro/70 leading-relaxed">
                        Crea tu cuenta como ONG, Empresa o Voluntario en menos de 2 minutos
                    </p>
                </div>
                
                <div class="scroll-animate text-center">
                    <div class="relative mb-6">
                        <div class="absolute inset-0 bg-gradient-to-br from-brand-acento to-brand-primario rounded-2xl transform rotate-6 opacity-20"></div>
                        <div class="relative w-24 h-24 bg-gradient-to-br from-brand-acento to-brand-primario rounded-2xl flex items-center justify-center mx-auto shadow-xl">
                            <span class="text-4xl font-bold text-white">2</span>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-brand-gris-oscuro mb-3">Explora Eventos</h3>
                    <p class="text-brand-gris-oscuro/70 leading-relaxed">
                        Descubre eventos cercanos a ti o crea nuevos eventos para tu organización
                    </p>
                </div>
                
                <div class="scroll-animate text-center">
                    <div class="relative mb-6">
                        <div class="absolute inset-0 bg-gradient-to-br from-brand-primario to-brand-acento rounded-2xl transform rotate-6 opacity-20"></div>
                        <div class="relative w-24 h-24 bg-gradient-to-br from-brand-primario to-brand-acento rounded-2xl flex items-center justify-center mx-auto shadow-xl">
                            <span class="text-4xl font-bold text-white">3</span>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-brand-gris-oscuro mb-3">Participa</h3>
                    <p class="text-brand-gris-oscuro/70 leading-relaxed">
                        Inscríbete en eventos, gestiona participantes y genera impacto real
                    </p>
                </div>
                
                <div class="scroll-animate text-center">
                    <div class="relative mb-6">
                        <div class="absolute inset-0 bg-gradient-to-br from-brand-acento to-brand-primario rounded-2xl transform rotate-6 opacity-20"></div>
                        <div class="relative w-24 h-24 bg-gradient-to-br from-brand-acento to-brand-primario rounded-2xl flex items-center justify-center mx-auto shadow-xl">
                            <span class="text-4xl font-bold text-white">4</span>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-brand-gris-oscuro mb-3">Mide el Impacto</h3>
                    <p class="text-brand-gris-oscuro/70 leading-relaxed">
                        Accede a reportes detallados y certificados de participación
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Servicios / Funcionalidades -->
    <section id="servicios" class="py-24 bg-gradient-to-b from-white to-brand-gris-suave">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 scroll-animate">
                <div class="inline-block mb-4">
                    <span class="px-4 py-2 bg-brand-primario/10 text-brand-primario rounded-full text-sm font-semibold">
                        Nuestras Herramientas
                    </span>
                </div>
                <h2 class="text-4xl md:text-5xl font-bold text-brand-gris-oscuro mb-4">
                    Servicios <span class="bg-gradient-to-r from-brand-primario to-brand-acento bg-clip-text text-transparent">Completos</span>
                </h2>
                <p class="text-xl text-brand-gris-oscuro/70 max-w-2xl mx-auto">
                    Herramientas poderosas para gestionar eventos, conectar voluntarios y generar impacto social medible
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Gestión de Eventos -->
                <div class="bg-white rounded-2xl p-8 shadow-lg hover-lift scroll-animate">
                    <div class="w-20 h-20 bg-gradient-to-br from-brand-primario to-brand-acento rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                        <i class="fas fa-calendar-alt text-white text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-brand-gris-oscuro mb-4">Gestión de Eventos</h3>
                    <p class="text-brand-gris-oscuro/70 leading-relaxed mb-4">
                        Crea y gestiona eventos comunitarios y megaeventos de forma sencilla. Controla fechas, ubicaciones, capacidad y participantes en tiempo real.
                    </p>
                    <ul class="space-y-2 text-sm text-brand-gris-oscuro/70">
                        <li class="flex items-center"><i class="fas fa-check text-brand-primario mr-2"></i> Calendario integrado</li>
                        <li class="flex items-center"><i class="fas fa-check text-brand-primario mr-2"></i> Gestión de capacidad</li>
                        <li class="flex items-center"><i class="fas fa-check text-brand-primario mr-2"></i> Notificaciones automáticas</li>
                    </ul>
                </div>

                <!-- Participación Voluntaria -->
                <div class="bg-white rounded-2xl p-8 shadow-lg hover-lift scroll-animate">
                    <div class="w-20 h-20 bg-gradient-to-br from-brand-acento to-brand-primario rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                        <i class="fas fa-hand-holding-heart text-white text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-brand-gris-oscuro mb-4">Participación Voluntaria</h3>
                    <p class="text-brand-gris-oscuro/70 leading-relaxed mb-4">
                        Sistema de inscripción intuitivo que permite a los voluntarios encontrar eventos, inscribirse fácilmente y hacer seguimiento de su participación.
                    </p>
                    <ul class="space-y-2 text-sm text-brand-gris-oscuro/70">
                        <li class="flex items-center"><i class="fas fa-check text-brand-acento mr-2"></i> Búsqueda avanzada</li>
                        <li class="flex items-center"><i class="fas fa-check text-brand-acento mr-2"></i> Inscripción en un clic</li>
                        <li class="flex items-center"><i class="fas fa-check text-brand-acento mr-2"></i> Historial completo</li>
                    </ul>
                </div>

                <!-- Reacciones a Eventos -->
                <div class="bg-white rounded-2xl p-8 shadow-lg hover-lift scroll-animate">
                    <div class="w-20 h-20 bg-gradient-to-br from-brand-primario to-brand-acento rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                        <i class="fas fa-heart text-white text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-brand-gris-oscuro mb-4">Reacciones a Eventos</h3>
                    <p class="text-brand-gris-oscuro/70 leading-relaxed mb-4">
                        Los usuarios pueden marcar eventos como favoritos, permitiendo que las organizaciones conozcan qué eventos generan más interés en la comunidad.
                    </p>
                    <ul class="space-y-2 text-sm text-brand-gris-oscuro/70">
                        <li class="flex items-center"><i class="fas fa-check text-brand-primario mr-2"></i> Sistema de favoritos</li>
                        <li class="flex items-center"><i class="fas fa-check text-brand-primario mr-2"></i> Métricas de interés</li>
                        <li class="flex items-center"><i class="fas fa-check text-brand-primario mr-2"></i> Recomendaciones</li>
                    </ul>
                </div>

                <!-- Panel de ONG -->
                <div class="bg-white rounded-2xl p-8 shadow-lg hover-lift scroll-animate">
                    <div class="w-20 h-20 bg-gradient-to-br from-brand-primario to-brand-acento rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                        <i class="fas fa-chart-line text-white text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-brand-gris-oscuro mb-4">Panel de ONG</h3>
                    <p class="text-brand-gris-oscuro/70 leading-relaxed mb-4">
                        Dashboard completo para ONGs con estadísticas de eventos, gestión de voluntarios, reportes detallados y herramientas de análisis de impacto.
                    </p>
                    <ul class="space-y-2 text-sm text-brand-gris-oscuro/70">
                        <li class="flex items-center"><i class="fas fa-check text-brand-primario mr-2"></i> Dashboard interactivo</li>
                        <li class="flex items-center"><i class="fas fa-check text-brand-primario mr-2"></i> Reportes en tiempo real</li>
                        <li class="flex items-center"><i class="fas fa-check text-brand-primario mr-2"></i> Análisis de impacto</li>
                    </ul>
                </div>

                <!-- Certificados para Voluntarios -->
                <div class="bg-white rounded-2xl p-8 shadow-lg hover-lift scroll-animate">
                    <div class="w-20 h-20 bg-gradient-to-br from-brand-acento to-brand-primario rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                        <i class="fas fa-certificate text-white text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-brand-gris-oscuro mb-4">Certificados para Voluntarios</h3>
                    <p class="text-brand-gris-oscuro/70 leading-relaxed mb-4">
                        Sistema de certificación que reconoce y valida la participación de voluntarios en eventos, generando documentos oficiales de su contribución.
                    </p>
                    <ul class="space-y-2 text-sm text-brand-gris-oscuro/70">
                        <li class="flex items-center"><i class="fas fa-check text-brand-acento mr-2"></i> Certificados digitales</li>
                        <li class="flex items-center"><i class="fas fa-check text-brand-acento mr-2"></i> Validación automática</li>
                        <li class="flex items-center"><i class="fas fa-check text-brand-acento mr-2"></i> Descarga en PDF</li>
                    </ul>
                </div>

                <!-- Dashboard de Participación -->
                <div class="bg-white rounded-2xl p-8 shadow-lg hover-lift scroll-animate">
                    <div class="w-20 h-20 bg-gradient-to-br from-brand-primario to-brand-acento rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                        <i class="fas fa-chart-pie text-white text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-brand-gris-oscuro mb-4">Dashboard de Participación</h3>
                    <p class="text-brand-gris-oscuro/70 leading-relaxed mb-4">
                        Visualiza estadísticas completas de participación, reacciones y tendencias. Herramientas de análisis para optimizar tus eventos y maximizar el impacto.
                    </p>
                    <ul class="space-y-2 text-sm text-brand-gris-oscuro/70">
                        <li class="flex items-center"><i class="fas fa-check text-brand-primario mr-2"></i> Gráficos interactivos</li>
                        <li class="flex items-center"><i class="fas fa-check text-brand-primario mr-2"></i> Tendencias y patrones</li>
                        <li class="flex items-center"><i class="fas fa-check text-brand-primario mr-2"></i> Exportación de datos</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Beneficios -->
    <section id="beneficios" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 scroll-animate">
                <div class="inline-block mb-4">
                    <span class="px-4 py-2 bg-brand-primario/10 text-brand-primario rounded-full text-sm font-semibold">
                        Ventajas
                    </span>
                </div>
                <h2 class="text-4xl md:text-5xl font-bold text-brand-gris-oscuro mb-4">
                    ¿Por qué elegir <span class="bg-gradient-to-r from-brand-primario to-brand-acento bg-clip-text text-transparent">UNI2</span>?
                </h2>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-gradient-to-br from-brand-primario/5 to-brand-acento/5 rounded-2xl p-8 hover-lift scroll-animate">
                    <div class="w-16 h-16 bg-brand-primario rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-bolt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-brand-gris-oscuro mb-3">Rápido y Eficiente</h3>
                    <p class="text-brand-gris-oscuro/70">Gestiona eventos en minutos, no en horas. Interfaz intuitiva diseñada para ahorrar tiempo.</p>
                </div>
                
                <div class="bg-gradient-to-br from-brand-acento/5 to-brand-primario/5 rounded-2xl p-8 hover-lift scroll-animate">
                    <div class="w-16 h-16 bg-brand-acento rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-mobile-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-brand-gris-oscuro mb-3">Totalmente Responsivo</h3>
                    <p class="text-brand-gris-oscuro/70">Accede desde cualquier dispositivo. Funciona perfectamente en móviles, tablets y computadoras.</p>
                </div>
                
                <div class="bg-gradient-to-br from-brand-primario/5 to-brand-acento/5 rounded-2xl p-8 hover-lift scroll-animate">
                    <div class="w-16 h-16 bg-brand-primario rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-shield-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-brand-gris-oscuro mb-3">Seguro y Confiable</h3>
                    <p class="text-brand-gris-oscuro/70">Tus datos están protegidos con los más altos estándares de seguridad y privacidad.</p>
                </div>
                
                <div class="bg-gradient-to-br from-brand-acento/10 to-brand-primario/10 rounded-2xl p-8 hover-lift scroll-animate">
                    <div class="w-16 h-16 bg-brand-acento rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-headset text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-brand-gris-oscuro mb-3">Soporte Dedicado</h3>
                    <p class="text-brand-gris-oscuro/70">Equipo de soporte disponible para ayudarte en cada paso del proceso.</p>
                </div>
                
                <div class="bg-gradient-to-br from-brand-primario/10 to-brand-acento/10 rounded-2xl p-8 hover-lift scroll-animate">
                    <div class="w-16 h-16 bg-brand-primario rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-chart-bar text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-brand-gris-oscuro mb-3">Analytics Avanzados</h3>
                    <p class="text-brand-gris-oscuro/70">Métricas detalladas y reportes personalizados para medir el impacto real de tus eventos.</p>
                </div>
                
                <div class="bg-gradient-to-br from-brand-acento/10 to-brand-primario/10 rounded-2xl p-8 hover-lift scroll-animate">
                    <div class="w-16 h-16 bg-brand-acento rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-gift text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-brand-gris-oscuro mb-3">Gratis para Voluntarios</h3>
                    <p class="text-brand-gris-oscuro/70">Los voluntarios pueden usar todas las funcionalidades sin costo alguno.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Final -->
    <section class="py-24 bg-gradient-to-br from-brand-primario via-brand-primario to-brand-acento gradient-animated relative overflow-hidden">
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-white/10 rounded-full blur-3xl animate-float"></div>
            <div class="absolute bottom-1/4 right-1/4 w-80 h-80 bg-white/10 rounded-full blur-3xl animate-float" style="animation-delay: 2s;"></div>
        </div>
        
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <div class="scroll-animate">
                <div class="inline-block mb-6">
                    <span class="px-4 py-2 bg-white/20 backdrop-blur-md rounded-full text-white text-sm font-semibold">
                        <i class="fas fa-star mr-2"></i> Únete a la Comunidad
                    </span>
                </div>
                <h2 class="text-4xl md:text-6xl font-extrabold text-white mb-6 leading-tight">
                    Únete y empieza a<br>
                    <span class="text-brand-acento drop-shadow-lg">transformar comunidades</span>
                </h2>
                <p class="text-xl md:text-2xl text-white/95 mb-12 max-w-2xl mx-auto leading-relaxed">
                    Sé parte del cambio. Ya seas una ONG, una empresa o un voluntario, tu participación marca la diferencia en nuestras comunidades.
                </p>
                <div class="flex flex-col sm:flex-row gap-5 justify-center items-center">
                    <a href="{{ route('login') }}" class="group px-10 py-5 bg-white text-brand-primario rounded-2xl font-bold text-lg hover:shadow-2xl transition-all transform hover:scale-105">
                        <i class="fas fa-user-plus mr-2 group-hover:rotate-12 transition-transform"></i> Crear Cuenta Gratis
                    </a>
                    <a href="{{ route('login') }}" class="px-10 py-5 bg-brand-acento text-white rounded-2xl font-bold text-lg hover:shadow-2xl transition-all transform hover:scale-105">
                        <i class="fas fa-sign-in-alt mr-2"></i> Iniciar Sesión
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-brand-primario text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-3 gap-8 mb-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">UNI2</h3>
                    <p class="text-white/80">Conectando comunidades, transformando vidas.</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Enlaces Rápidos</h4>
                    <ul class="space-y-2 text-white/80">
                        <li><a href="#sobre" class="hover:text-white transition">Sobre el Proyecto</a></li>
                        <li><a href="#como-funciona" class="hover:text-white transition">Cómo Funciona</a></li>
                        <li><a href="#servicios" class="hover:text-white transition">Servicios</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Contacto</h4>
                    <p class="text-white/80">Soporte disponible para ayudarte</p>
                </div>
            </div>
            <div class="border-t border-white/20 pt-8 text-center text-white/60">
                <p>&copy; {{ date('Y') }} UNI2. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Sección de Validación de Asistencia para Usuarios No Registrados -->
    @guest
    <section id="validacionAsistenciaNoRegistrado" class="py-16 bg-gradient-to-br from-brand-primario via-brand-primario to-brand-acento">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl shadow-2xl p-8 md:p-12" style="animation: fadeInUp 0.8s ease-out;">
                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="inline-block p-4 bg-brand-acento/10 rounded-full mb-4">
                        <i class="fas fa-qrcode text-brand-acento text-4xl"></i>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-brand-primario mb-3">
                        Registrar Asistencia
                    </h2>
                    <p class="text-gray-600 text-lg">
                        Si participaste en un evento sin cuenta, ingresa tus datos y código de ticket para validar tu asistencia
                    </p>
                </div>

                <!-- Formulario de Validación -->
                <div class="mb-6">
                    <form id="formValidacionNoRegistrado" onsubmit="event.preventDefault(); verificarTicketNoRegistrado();">
                        <div class="space-y-4 mb-4">
                            <div>
                                <label class="block text-sm font-semibold text-brand-primario mb-2">
                                    <i class="fas fa-user mr-2"></i> Nombres
                                </label>
                                <input 
                                    type="text" 
                                    id="nombresNoRegistrado" 
                                    required
                                    placeholder="Ingresa tus nombres"
                                    class="w-full px-6 py-4 border-2 border-gray-300 rounded-xl focus:border-brand-acento focus:ring-2 focus:ring-brand-acento/20 outline-none transition text-lg"
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-brand-primario mb-2">
                                    <i class="fas fa-user mr-2"></i> Apellidos
                                </label>
                                <input 
                                    type="text" 
                                    id="apellidosNoRegistrado" 
                                    required
                                    placeholder="Ingresa tus apellidos"
                                    class="w-full px-6 py-4 border-2 border-gray-300 rounded-xl focus:border-brand-acento focus:ring-2 focus:ring-brand-acento/20 outline-none transition text-lg"
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-brand-primario mb-2">
                                    <i class="fas fa-ticket-alt mr-2"></i> Código de Ticket
                                </label>
                                <input 
                                    type="text" 
                                    id="ticketCodigoNoRegistrado" 
                                    required
                                    placeholder="Ingresa tu código de ticket o escanea el QR"
                                    class="w-full px-6 py-4 border-2 border-gray-300 rounded-xl focus:border-brand-acento focus:ring-2 focus:ring-brand-acento/20 outline-none transition text-lg"
                                    onkeypress="if(event.key === 'Enter') verificarTicketNoRegistrado()"
                                >
                            </div>
                        </div>

                        <!-- Botones de QR -->
                        <div class="flex flex-col sm:flex-row gap-3 justify-center mb-4">
                            <button 
                                type="button"
                                onclick="activarEscannerQRNoRegistrado()" 
                                id="btnEscanearQRNoRegistrado"
                                class="px-6 py-3 bg-brand-acento text-white rounded-xl font-semibold hover:bg-brand-acento/90 transition transform hover:scale-105"
                            >
                                <i class="fas fa-camera mr-2"></i> Escanear Código QR
                            </button>
                            <button 
                                type="button"
                                onclick="document.getElementById('inputQRImagenNoRegistrado').click()" 
                                id="btnImportarQRNoRegistrado"
                                class="px-6 py-3 bg-brand-primario text-white rounded-xl font-semibold hover:bg-brand-primario/90 transition transform hover:scale-105"
                            >
                                <i class="fas fa-upload mr-2"></i> Importar QR
                            </button>
                            <input 
                                type="file" 
                                id="inputQRImagenNoRegistrado" 
                                accept="image/*" 
                                style="display: none;"
                                onchange="procesarQRImagenNoRegistrado(event)"
                            >
                        </div>

                        <!-- Contenedor del Escáner QR -->
                        <div id="qrScannerContainerNoRegistrado" style="display: none; margin-top: 1.5rem; padding: 1.5rem; background: #f8f9fa; border-radius: 12px; border: 2px dashed #00A36C;">
                            <div class="flex justify-between items-center mb-3">
                                <h6 class="font-bold text-brand-primario">
                                    <i class="fas fa-camera mr-2 text-brand-acento"></i> Escáner QR Activo
                                </h6>
                                <button type="button" onclick="detenerEscannerQRNoRegistrado()" class="px-4 py-2 bg-red-500 text-white rounded-lg font-semibold hover:bg-red-600 transition">
                                    <i class="fas fa-times mr-1"></i> Cerrar
                                </button>
                            </div>
                            <div style="position: relative; width: 100%; max-width: 400px; margin: 0 auto; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);">
                                <video id="qrVideoNoRegistrado" width="100%" style="display: block; background: #000; border-radius: 12px;"></video>
                                <canvas id="qrCanvasNoRegistrado" style="display: none;"></canvas>
                            </div>
                            <p class="text-center mt-3 text-gray-600 text-sm">
                                <i class="fas fa-info-circle mr-2"></i> Apunta la cámara hacia el código QR de tu ticket
                            </p>
                        </div>

                        <button 
                            type="submit"
                            id="btnVerificarNoRegistrado"
                            class="w-full px-8 py-4 bg-brand-primario text-white rounded-xl font-bold text-lg hover:bg-brand-primario/90 transition transform hover:scale-105 shadow-lg"
                        >
                            <i class="fas fa-search mr-2"></i> Verificar Ticket
                        </button>
                        <div id="mensajeLoginNoRegistrado" class="mt-4 text-center"></div>
                    </form>
                </div>

                <!-- Información del Evento (se muestra después de verificar el ticket) -->
                <div id="infoEventoContainerNoRegistrado" class="hidden mb-6 p-6 bg-gradient-to-r from-brand-primario/10 to-brand-acento/10 rounded-xl border-2 border-brand-acento/30">
                    <h3 class="text-xl font-bold text-brand-primario mb-4">
                        <i class="fas fa-calendar-check mr-2 text-brand-acento"></i> Confirmar Asistencia
                    </h3>
                    <div id="infoEventoDetalleNoRegistrado" class="mb-4">
                        <!-- Se llenará dinámicamente -->
                    </div>
                    <button 
                        onclick="confirmarAsistenciaNoRegistrado()" 
                        id="btnConfirmarAsistenciaNoRegistrado"
                        class="w-full px-8 py-4 bg-brand-acento text-white rounded-xl font-bold text-lg hover:bg-brand-acento/90 transition transform hover:scale-105 shadow-lg"
                    >
                        <i class="fas fa-check-circle mr-2"></i> Confirmar Asistencia
                    </button>
                </div>

                <!-- Mensajes de Resultado -->
                <div id="mensajeResultadoNoRegistrado" class="hidden mt-4 p-4 rounded-xl"></div>
            </div>
        </div>
    </section>

    <!-- Scripts para Validación de Asistencia de Usuarios No Registrados -->
    <!-- Librería para escaneo QR (debe estar antes de los scripts) -->
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
    <script>
        let qrStreamNoRegistrado = null;
        let qrScanningNoRegistrado = false;
        let infoEventoNoRegistradoActual = null;

        // Verificar ticket de usuario no registrado
        async function verificarTicketNoRegistrado() {
            const nombres = document.getElementById('nombresNoRegistrado').value.trim();
            const apellidos = document.getElementById('apellidosNoRegistrado').value.trim();
            const ticketCodigo = document.getElementById('ticketCodigoNoRegistrado').value.trim();

            if (!nombres || !apellidos || !ticketCodigo) {
                mostrarMensajeNoRegistrado('Por favor, completa todos los campos', 'error');
                return;
            }

            const btnVerificar = document.getElementById('btnVerificarNoRegistrado');
            btnVerificar.disabled = true;
            btnVerificar.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Verificando...';

            try {
                const apiUrl = window.API_BASE_URL || 'http://192.168.0.6:8000';
                const res = await fetch(`${apiUrl}/api/verificar-ticket-no-registrado-welcome`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        nombres: nombres,
                        apellidos: apellidos,
                        ticket_codigo: ticketCodigo
                    }),
                });

                const data = await res.json();

                if (data.success) {
                    infoEventoNoRegistradoActual = {
                        ...data.data,
                        nombres: nombres,
                        apellidos: apellidos
                    };
                    mostrarInfoEventoNoRegistrado(data.data);
                } else {
                    mostrarMensajeNoRegistrado(data.error || 'Error al verificar ticket', 'error');
                    document.getElementById('infoEventoContainerNoRegistrado').classList.add('hidden');
                }
            } catch (error) {
                console.error('Error verificando ticket:', error);
                mostrarMensajeNoRegistrado('Error al verificar ticket. Por favor, intenta nuevamente.', 'error');
                document.getElementById('infoEventoContainerNoRegistrado').classList.add('hidden');
            } finally {
                btnVerificar.disabled = false;
                btnVerificar.innerHTML = '<i class="fas fa-search mr-2"></i> Verificar Ticket';
            }
        }

        // Mostrar información del evento para usuario no registrado
        function mostrarInfoEventoNoRegistrado(evento) {
            const container = document.getElementById('infoEventoDetalleNoRegistrado');
            
            if (evento.ya_validado) {
                container.innerHTML = `
                    <div class="alert alert-warning mb-3">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Ya registraste tu asistencia para este evento.
                    </div>
                    <div class="mb-3">
                        <p><strong>Evento:</strong> ${evento.evento_titulo}</p>
                        <p><strong>Fecha:</strong> ${evento.fecha_inicio}</p>
                        ${evento.ubicacion ? `<p><strong>Ubicación:</strong> ${evento.ubicacion}${evento.ciudad ? ', ' + evento.ciudad : ''}</p>` : ''}
                    </div>
                `;
                document.getElementById('btnConfirmarAsistenciaNoRegistrado').style.display = 'none';
            } else {
                container.innerHTML = `
                    <div class="mb-3">
                        <p><strong>Participante:</strong> ${evento.participante}</p>
                        <p><strong>Evento:</strong> ${evento.evento_titulo}</p>
                        ${evento.evento_descripcion ? `<p><strong>Descripción:</strong> ${evento.evento_descripcion}</p>` : ''}
                        <p><strong>Fecha de inicio:</strong> ${evento.fecha_inicio}</p>
                        ${evento.ubicacion ? `<p><strong>Ubicación:</strong> ${evento.ubicacion}${evento.ciudad ? ', ' + evento.ciudad : ''}</p>` : ''}
                        <p><strong>Tipo de evento:</strong> ${evento.evento_tipo || 'No especificado'}</p>
                    </div>
                `;
                document.getElementById('btnConfirmarAsistenciaNoRegistrado').style.display = 'block';
            }

            document.getElementById('infoEventoContainerNoRegistrado').classList.remove('hidden');
        }

        // Confirmar asistencia de usuario no registrado
        async function confirmarAsistenciaNoRegistrado() {
            if (!infoEventoNoRegistradoActual) {
                mostrarMensajeNoRegistrado('No hay información de evento para confirmar', 'error');
                return;
            }

            const btnConfirmar = document.getElementById('btnConfirmarAsistenciaNoRegistrado');
            btnConfirmar.disabled = true;
            btnConfirmar.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Confirmando...';

            try {
                const apiUrl = window.API_BASE_URL || 'http://192.168.0.6:8000';
                const res = await fetch(`${apiUrl}/api/validar-asistencia-no-registrado-welcome`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        nombres: infoEventoNoRegistradoActual.nombres,
                        apellidos: infoEventoNoRegistradoActual.apellidos,
                        ticket_codigo: infoEventoNoRegistradoActual.ticket_codigo
                    }),
                });

                const data = await res.json();

                if (data.success) {
                    mostrarMensajeNoRegistrado(data.message || '¡Asistencia confirmada correctamente!', 'success');
                    // Limpiar formulario
                    document.getElementById('nombresNoRegistrado').value = '';
                    document.getElementById('apellidosNoRegistrado').value = '';
                    document.getElementById('ticketCodigoNoRegistrado').value = '';
                    document.getElementById('infoEventoContainerNoRegistrado').classList.add('hidden');
                    infoEventoNoRegistradoActual = null;
                } else {
                    mostrarMensajeNoRegistrado(data.error || 'Error al confirmar asistencia', 'error');
                }
            } catch (error) {
                console.error('Error confirmando asistencia:', error);
                mostrarMensajeNoRegistrado('Error al confirmar asistencia. Por favor, intenta nuevamente.', 'error');
            } finally {
                btnConfirmar.disabled = false;
                btnConfirmar.innerHTML = '<i class="fas fa-check-circle mr-2"></i> Confirmar Asistencia';
            }
        }

        // Mostrar mensaje para usuario no registrado
        function mostrarMensajeNoRegistrado(mensaje, tipo) {
            const mensajeDiv = document.getElementById('mensajeResultadoNoRegistrado');
            const mensajeLogin = document.getElementById('mensajeLoginNoRegistrado');
            
            if (mensajeDiv) {
                mensajeDiv.className = tipo === 'success' 
                    ? 'mt-4 p-4 rounded-xl bg-green-100 border-2 border-green-500 text-green-800'
                    : 'mt-4 p-4 rounded-xl bg-red-100 border-2 border-red-500 text-red-800';
                mensajeDiv.innerHTML = `
                    <div class="flex items-center">
                        <i class="fas ${tipo === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-3 text-xl"></i>
                        <span class="font-semibold">${mensaje}</span>
                    </div>
                `;
                mensajeDiv.classList.remove('hidden');
                setTimeout(() => {
                    mensajeDiv.classList.add('hidden');
                }, 5000);
            }
            
            if (mensajeLogin) {
                mensajeLogin.innerHTML = tipo === 'error' 
                    ? `<span class="text-red-600 font-semibold">${mensaje}</span>`
                    : '';
            }
        }

        // Activar escáner QR para usuario no registrado
        async function activarEscannerQRNoRegistrado() {
            const container = document.getElementById('qrScannerContainerNoRegistrado');
            const video = document.getElementById('qrVideoNoRegistrado');
            const canvas = document.getElementById('qrCanvasNoRegistrado');
            const context = canvas.getContext('2d');

            try {
                const stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { facingMode: 'environment' } 
                });
                
                qrStreamNoRegistrado = stream;
                video.srcObject = stream;
                video.setAttribute('playsinline', true);
                video.play();
                container.style.display = 'block';
                qrScanningNoRegistrado = true;

                video.addEventListener('loadedmetadata', () => {
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                });

                function scanQR() {
                    if (!qrScanningNoRegistrado) return;

                    if (video.readyState === video.HAVE_ENOUGH_DATA) {
                        context.drawImage(video, 0, 0, canvas.width, canvas.height);
                        const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                        
                        if (typeof jsQR !== 'undefined') {
                            const code = jsQR(imageData.data, imageData.width, imageData.height);
                            if (code) {
                                detenerEscannerQRNoRegistrado();
                                document.getElementById('ticketCodigoNoRegistrado').value = code.data;
                                verificarTicketNoRegistrado();
                            }
                        }
                    }

                    requestAnimationFrame(scanQR);
                }

                scanQR();

            } catch (error) {
                console.error('Error accediendo a la cámara:', error);
                mostrarMensajeNoRegistrado('No se pudo acceder a la cámara. Por favor, verifica los permisos.', 'error');
            }
        }

        // Detener escáner QR para usuario no registrado
        function detenerEscannerQRNoRegistrado() {
            qrScanningNoRegistrado = false;
            if (qrStreamNoRegistrado) {
                qrStreamNoRegistrado.getTracks().forEach(track => track.stop());
                qrStreamNoRegistrado = null;
            }
            const video = document.getElementById('qrVideoNoRegistrado');
            if (video) {
                video.srcObject = null;
            }
            document.getElementById('qrScannerContainerNoRegistrado').style.display = 'none';
        }

        // Procesar imagen QR importada
        function procesarQRImagenNoRegistrado(event) {
            const file = event.target.files[0];
            if (!file) {
                return;
            }

            // Validar que sea una imagen
            if (!file.type.startsWith('image/')) {
                mostrarMensajeNoRegistrado('Por favor, selecciona un archivo de imagen válido', 'error');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const img = new Image();
                img.onload = function() {
                    // Crear canvas temporal para procesar la imagen
                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');
                    canvas.width = img.width;
                    canvas.height = img.height;
                    context.drawImage(img, 0, 0);

                    // Obtener datos de la imagen
                    const imageData = context.getImageData(0, 0, canvas.width, canvas.height);

                    // Procesar con jsQR
                    if (typeof jsQR !== 'undefined') {
                        const code = jsQR(imageData.data, imageData.width, imageData.height);
                        
                        if (code) {
                            // QR encontrado, extraer código
                            const ticketCodigo = code.data.trim();
                            document.getElementById('ticketCodigoNoRegistrado').value = ticketCodigo;
                            
                            // Verificar que los nombres y apellidos estén completos antes de validar
                            const nombres = document.getElementById('nombresNoRegistrado').value.trim();
                            const apellidos = document.getElementById('apellidosNoRegistrado').value.trim();
                            
                            if (!nombres || !apellidos) {
                                mostrarMensajeNoRegistrado('Por favor, completa los campos de nombres y apellidos antes de importar el QR', 'error');
                                return;
                            }
                            
                            // Validar con el backend (valida nombre, apellidos y ticket en PostgreSQL)
                            verificarTicketNoRegistrado();
                        } else {
                            mostrarMensajeNoRegistrado('No se pudo leer el código QR de la imagen. Asegúrate de que la imagen sea clara y contenga un código QR válido.', 'error');
                        }
                    } else {
                        mostrarMensajeNoRegistrado('Error: La librería de QR no está cargada. Por favor, recarga la página.', 'error');
                    }
                };
                img.onerror = function() {
                    mostrarMensajeNoRegistrado('Error al cargar la imagen. Por favor, intenta con otra imagen.', 'error');
                };
                img.src = e.target.result;
            };
            reader.onerror = function() {
                mostrarMensajeNoRegistrado('Error al leer el archivo. Por favor, intenta nuevamente.', 'error');
            };
            reader.readAsDataURL(file);
            
            // Limpiar el input para permitir seleccionar el mismo archivo nuevamente
            event.target.value = '';
        }
    </script>
    @endguest

    <!-- Sección de Validación de Asistencia para Usuarios Registrados -->
    @auth
    <section id="validacionAsistencia" class="py-16 bg-gradient-to-br from-brand-primario via-brand-primario to-brand-acento">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl shadow-2xl p-8 md:p-12" style="animation: fadeInUp 0.8s ease-out;">
                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="inline-block p-4 bg-brand-acento/10 rounded-full mb-4">
                        <i class="fas fa-qrcode text-brand-acento text-4xl"></i>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-brand-primario mb-3">
                        Valida tu Asistencia
                    </h2>
                    <p class="text-gray-600 text-lg">
                        Ingresa tus credenciales y luego tu código de ticket o escanea el código QR
                    </p>
                </div>

                <!-- Paso 1: Formulario de Login -->
                <div id="pasoLogin" class="mb-6">
                    <form id="formLoginAsistencia" onsubmit="event.preventDefault(); hacerLoginAsistencia();">
                        <div class="space-y-4 mb-6">
                            <div>
                                <label class="block text-sm font-semibold text-brand-primario mb-2">
                                    <i class="fas fa-envelope mr-2"></i> Correo Electrónico
                                </label>
                                <input 
                                    type="email" 
                                    id="emailAsistencia" 
                                    required
                                    placeholder="tu@correo.com"
                                    class="w-full px-6 py-4 border-2 border-gray-300 rounded-xl focus:border-brand-acento focus:ring-2 focus:ring-brand-acento/20 outline-none transition text-lg"
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-brand-primario mb-2">
                                    <i class="fas fa-lock mr-2"></i> Contraseña
                                </label>
                                <input 
                                    type="password" 
                                    id="passwordAsistencia" 
                                    required
                                    placeholder="Tu contraseña"
                                    class="w-full px-6 py-4 border-2 border-gray-300 rounded-xl focus:border-brand-acento focus:ring-2 focus:ring-brand-acento/20 outline-none transition text-lg"
                                >
                            </div>
                        </div>
                        <button 
                            type="submit"
                            id="btnLoginAsistencia"
                            class="w-full px-8 py-4 bg-brand-acento text-white rounded-xl font-bold text-lg hover:bg-brand-acento/90 transition transform hover:scale-105 shadow-lg"
                        >
                            <i class="fas fa-sign-in-alt mr-2"></i> Iniciar Sesión
                        </button>
                        <div id="mensajeLogin" class="mt-4 text-center"></div>
                    </form>
                </div>

                <!-- Paso 2: Formulario de Validación (oculto inicialmente) -->
                <div id="pasoValidacion" class="mb-6" style="display: none;">
                    <div class="mb-4 p-4 bg-green-50 border-2 border-green-200 rounded-xl">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-600 text-xl mr-3"></i>
                                <div>
                                    <p class="font-semibold text-green-800">Sesión iniciada</p>
                                    <p class="text-sm text-green-600" id="nombreUsuarioLogueado"></p>
                                </div>
                            </div>
                            <button onclick="cerrarSesionAsistencia()" class="text-sm text-red-600 hover:text-red-800 font-semibold">
                                <i class="fas fa-sign-out-alt mr-1"></i> Cerrar Sesión
                            </button>
                        </div>
                    </div>

                    <div class="mb-6">
                        <div class="flex flex-col md:flex-row gap-4 mb-4">
                            <div class="flex-1">
                                <input 
                                    type="text" 
                                    id="ticketCodigoInput" 
                                    placeholder="Ingresa tu código de ticket o escanea el QR"
                                    class="w-full px-6 py-4 border-2 border-gray-300 rounded-xl focus:border-brand-acento focus:ring-2 focus:ring-brand-acento/20 outline-none transition text-lg"
                                    onkeypress="if(event.key === 'Enter') verificarTicket()"
                                >
                            </div>
                            <button 
                                onclick="verificarTicket()" 
                                id="btnVerificar"
                                class="px-8 py-4 bg-brand-primario text-white rounded-xl font-bold text-lg hover:bg-brand-primario/90 transition transform hover:scale-105 shadow-lg"
                            >
                                <i class="fas fa-search mr-2"></i> Verificar
                            </button>
                        </div>

                        <!-- Botón Escanear QR -->
                        <div class="flex justify-center mb-4">
                            <button 
                                onclick="activarEscannerQRWelcome()" 
                                id="btnEscanearQR"
                                class="px-6 py-3 bg-brand-acento text-white rounded-xl font-semibold hover:bg-brand-acento/90 transition transform hover:scale-105"
                            >
                                <i class="fas fa-camera mr-2"></i> Escanear Código QR
                            </button>
                        </div>

                        <!-- Contenedor del Escáner QR -->
                        <div id="qrScannerContainerWelcome" style="display: none; margin-top: 1.5rem; padding: 1.5rem; background: #f8f9fa; border-radius: 12px; border: 2px dashed #00A36C;">
                            <div class="flex justify-between items-center mb-3">
                                <h6 class="font-bold text-brand-primario">
                                    <i class="fas fa-camera mr-2 text-brand-acento"></i> Escáner QR Activo
                                </h6>
                                <button onclick="detenerEscannerQRWelcome()" class="px-4 py-2 bg-red-500 text-white rounded-lg font-semibold hover:bg-red-600 transition">
                                    <i class="fas fa-times mr-1"></i> Cerrar
                                </button>
                            </div>
                            <div style="position: relative; width: 100%; max-width: 400px; margin: 0 auto; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);">
                                <video id="qrVideoWelcome" width="100%" style="display: block; background: #000; border-radius: 12px;"></video>
                                <canvas id="qrCanvasWelcome" style="display: none;"></canvas>
                            </div>
                            <p class="text-center mt-3 text-gray-600 text-sm">
                                <i class="fas fa-info-circle mr-2"></i> Apunta la cámara hacia el código QR de tu ticket
                            </p>
                        </div>
                    </div>

                    <!-- Información del Evento (se muestra después de verificar el ticket) -->
                    <div id="infoEventoContainer" class="hidden mb-6 p-6 bg-gradient-to-r from-brand-primario/10 to-brand-acento/10 rounded-xl border-2 border-brand-acento/30">
                        <h3 class="text-xl font-bold text-brand-primario mb-4">
                            <i class="fas fa-calendar-check mr-2 text-brand-acento"></i> Información del Evento
                        </h3>
                        <div id="infoEventoDetalle" class="space-y-2">
                            <!-- Se llenará dinámicamente -->
                        </div>
                        <button 
                            onclick="confirmarAsistencia()" 
                            id="btnConfirmarAsistencia"
                            class="mt-4 w-full px-8 py-4 bg-brand-acento text-white rounded-xl font-bold text-lg hover:bg-brand-acento/90 transition transform hover:scale-105 shadow-lg"
                        >
                            <i class="fas fa-check-circle mr-2"></i> Confirmar Asistencia
                        </button>
                    </div>

                    <!-- Mensajes de Resultado -->
                    <div id="mensajeResultado" class="hidden mt-4 p-4 rounded-xl"></div>
                </div>
            </div>
        </div>
    </section>
    @endauth

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-12 mb-12">
                <div>
                    <div class="flex items-center mb-6">
                        <img src="{{ asset('assets/img/UNI2.png') }}" alt="UNI2" class="h-10 w-auto">
                        <span class="ml-3 text-2xl font-bold bg-gradient-to-r from-brand-primario to-brand-acento bg-clip-text text-transparent">UNI2</span>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed mb-4">
                        Conectando comunidades, transformando vidas. La plataforma que une esfuerzos para generar impacto social real y medible.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-brand-primario transition">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-brand-acento transition">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-brand-primario transition">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-brand-acento transition">
                            <i class="fab fa-linkedin"></i>
                        </a>
                    </div>
                </div>
                <div>
                    <h4 class="font-bold text-lg mb-4">Plataforma</h4>
                    <ul class="space-y-3 text-sm text-gray-400">
                        <li><a href="#sobre" class="hover:text-white transition">Sobre el Proyecto</a></li>
                        <li><a href="#como-funciona" class="hover:text-white transition">Cómo Funciona</a></li>
                        <li><a href="#servicios" class="hover:text-white transition">Servicios</a></li>
                        <li><a href="#beneficios" class="hover:text-white transition">Beneficios</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-lg mb-4">Recursos</h4>
                    <ul class="space-y-3 text-sm text-gray-400">
                        <li><a href="{{ route('login') }}" class="hover:text-white transition">Iniciar Sesión</a></li>
                        <li><a href="{{ route('register.ong') }}" class="hover:text-white transition">Registro ONG</a></li>
                        <li><a href="{{ route('register.empresa') }}" class="hover:text-white transition">Registro Empresa</a></li>
                        <li><a href="{{ route('register.externo') }}" class="hover:text-white transition">Registro Voluntario</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-lg mb-4">Contacto</h4>
                    <ul class="space-y-3 text-sm text-gray-400">
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-3 text-brand-acento"></i>
                            <a href="mailto:contacto@uni2.com" class="hover:text-white transition">contacto@uni2.com</a>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone mr-3 text-brand-acento"></i>
                            <span>+591 12345678</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-map-marker-alt mr-3 text-brand-acento"></i>
                            <span>Santa Cruz de la Sierra, Bolivia</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center text-sm text-gray-400">
                    <p>&copy; {{ date('Y') }} UNI2. Todos los derechos reservados.</p>
                    <div class="flex space-x-6 mt-4 md:mt-0">
                        <a href="#" class="hover:text-white transition">Política de Privacidad</a>
                        <a href="#" class="hover:text-white transition">Términos de Servicio</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        // Mobile Menu Toggle
        document.getElementById('mobileMenuBtn')?.addEventListener('click', function() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        });

        // Smooth Scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    document.getElementById('mobileMenu')?.classList.add('hidden');
                }
            });
        });

        // Navbar Scroll Effect
        let lastScroll = 0;
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            const currentScroll = window.pageYOffset;
            if (currentScroll > 100) {
                navbar.classList.add('shadow-lg');
            } else {
                navbar.classList.remove('shadow-lg');
            }
            lastScroll = currentScroll;
        });

        // Scroll Animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -100px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animated');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.scroll-animate').forEach(el => {
            observer.observe(el);
        });

        // Parallax Effect
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const parallaxElements = document.querySelectorAll('.parallax');
            parallaxElements.forEach(element => {
                const speed = 0.5;
                element.style.transform = `translateY(${scrolled * speed}px)`;
            });
        });
    </script>

    <!-- Scripts para Validación de Asistencia de Usuarios Registrados -->
    @auth
    <script>
        let qrStream = null;
        let qrScanning = false;
        let infoEventoActual = null; // Para guardar la info del evento verificado

        // Verificar si ya hay sesión al cargar
        document.addEventListener('DOMContentLoaded', function() {
            const token = localStorage.getItem('token');
            if (token) {
                // Verificar que el token sea válido y el usuario sea externo
                verificarSesionYMostrarValidacion();
            }
        });

        // Hacer login desde el formulario
        async function hacerLoginAsistencia() {
            const email = document.getElementById('emailAsistencia').value.trim();
            const password = document.getElementById('passwordAsistencia').value.trim();
            const mensajeDiv = document.getElementById('mensajeLogin');
            const btnLogin = document.getElementById('btnLoginAsistencia');

            if (!email || !password) {
                mensajeDiv.innerHTML = '<span class="text-red-600 font-semibold">Por favor, completa todos los campos</span>';
                return;
            }

            btnLogin.disabled = true;
            btnLogin.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Iniciando sesión...';
            mensajeDiv.innerHTML = '';

            try {
                const apiUrl = window.API_BASE_URL || 'http://192.168.0.6:8000';
                const res = await fetch(`${apiUrl}/api/auth/login`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        correo_electronico: email,
                        contrasena: password
                    }),
                });

                const data = await res.json();

                if (data.success && data.user) {
                    // Verificar que sea usuario externo
                    if (data.user.tipo_usuario !== 'Integrante externo') {
                        mensajeDiv.innerHTML = '<span class="text-red-600 font-semibold">Esta funcionalidad es solo para usuarios externos (voluntarios)</span>';
                        btnLogin.disabled = false;
                        btnLogin.innerHTML = '<i class="fas fa-sign-in-alt mr-2"></i> Iniciar Sesión';
                        return;
                    }

                    // Guardar token y datos del usuario
                    localStorage.setItem('token', data.token);
                    localStorage.setItem('id_usuario', data.user.id_usuario);
                    localStorage.setItem('tipo_usuario', data.user.tipo_usuario);
                    localStorage.setItem('nombre_usuario', data.user.nombre_usuario || '');

                    // Mostrar paso de validación
                    mostrarPasoValidacion(data.user.nombre_usuario);
                } else {
                    mensajeDiv.innerHTML = `<span class="text-red-600 font-semibold">${data.error || 'Error al iniciar sesión'}</span>`;
                    btnLogin.disabled = false;
                    btnLogin.innerHTML = '<i class="fas fa-sign-in-alt mr-2"></i> Iniciar Sesión';
                }
            } catch (error) {
                console.error('Error en login:', error);
                mensajeDiv.innerHTML = '<span class="text-red-600 font-semibold">Error de conexión. Verifica que el servidor esté ejecutándose.</span>';
                btnLogin.disabled = false;
                btnLogin.innerHTML = '<i class="fas fa-sign-in-alt mr-2"></i> Iniciar Sesión';
            }
        }

        // Verificar sesión existente
        async function verificarSesionYMostrarValidacion() {
            const token = localStorage.getItem('token');
            const tipoUsuario = localStorage.getItem('tipo_usuario');
            const nombreUsuario = localStorage.getItem('nombre_usuario');

            if (token && tipoUsuario === 'Integrante externo') {
                mostrarPasoValidacion(nombreUsuario);
            }
        }

        // Mostrar paso de validación
        function mostrarPasoValidacion(nombreUsuario) {
            document.getElementById('pasoLogin').style.display = 'none';
            document.getElementById('pasoValidacion').style.display = 'block';
            document.getElementById('nombreUsuarioLogueado').textContent = nombreUsuario || 'Usuario';
        }

        // Cerrar sesión
        function cerrarSesionAsistencia() {
            localStorage.removeItem('token');
            localStorage.removeItem('id_usuario');
            localStorage.removeItem('tipo_usuario');
            localStorage.removeItem('nombre_usuario');
            
            document.getElementById('pasoLogin').style.display = 'block';
            document.getElementById('pasoValidacion').style.display = 'none';
            document.getElementById('infoEventoContainer').classList.add('hidden');
            document.getElementById('ticketCodigoInput').value = '';
            document.getElementById('emailAsistencia').value = '';
            document.getElementById('passwordAsistencia').value = '';
            infoEventoActual = null;
        }

        // Verificar ticket (mostrar info del evento)
        async function verificarTicket() {
            const token = localStorage.getItem('token');
            if (!token) {
                mostrarMensaje('Debes iniciar sesión primero', 'error');
                return;
            }

            const ticketCodigo = document.getElementById('ticketCodigoInput').value.trim();
            if (!ticketCodigo) {
                mostrarMensaje('Por favor, ingresa un código de ticket', 'error');
                return;
            }

            const btnVerificar = document.getElementById('btnVerificar');
            btnVerificar.disabled = true;
            btnVerificar.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Verificando...';

            try {
                const apiUrl = window.API_BASE_URL || 'http://192.168.0.6:8000';
                const res = await fetch(`${apiUrl}/api/verificar-ticket-welcome`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        ticket_codigo: ticketCodigo
                    }),
                });

                const data = await res.json();

                if (data.success) {
                    infoEventoActual = data.data;
                    mostrarInfoEvento(data.data);
                } else {
                    mostrarMensaje(data.error || 'Error al verificar ticket', 'error');
                    document.getElementById('infoEventoContainer').classList.add('hidden');
                }
            } catch (error) {
                console.error('Error verificando ticket:', error);
                mostrarMensaje('Error al verificar ticket. Por favor, intenta nuevamente.', 'error');
                document.getElementById('infoEventoContainer').classList.add('hidden');
            } finally {
                btnVerificar.disabled = false;
                btnVerificar.innerHTML = '<i class="fas fa-search mr-2"></i> Verificar';
            }
        }

        // Mostrar información del evento
        function mostrarInfoEvento(evento) {
            const container = document.getElementById('infoEventoDetalle');
            
            if (evento.ya_validado) {
                container.innerHTML = `
                    <div class="p-4 bg-yellow-50 border-2 border-yellow-300 rounded-xl mb-4">
                        <p class="text-yellow-800 font-semibold">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Este ticket ya fue utilizado el ${evento.fecha_validacion_anterior}
                        </p>
                    </div>
                    <div class="space-y-3">
                        <p><strong class="text-brand-primario">Evento:</strong> ${evento.evento_titulo}</p>
                        <p><strong class="text-brand-primario">Fecha:</strong> ${evento.fecha_inicio}</p>
                        ${evento.ubicacion ? `<p><strong class="text-brand-primario">Ubicación:</strong> ${evento.ubicacion}${evento.ciudad ? ', ' + evento.ciudad : ''}</p>` : ''}
                    </div>
                `;
                document.getElementById('btnConfirmarAsistencia').style.display = 'none';
            } else {
                container.innerHTML = `
                    <div class="space-y-3">
                        <p><strong class="text-brand-primario">Evento:</strong> ${evento.evento_titulo}</p>
                        ${evento.evento_descripcion ? `<p><strong class="text-brand-primario">Descripción:</strong> ${evento.evento_descripcion}</p>` : ''}
                        <p><strong class="text-brand-primario">Fecha de inicio:</strong> ${evento.fecha_inicio}</p>
                        ${evento.ubicacion ? `<p><strong class="text-brand-primario">Ubicación:</strong> ${evento.ubicacion}${evento.ciudad ? ', ' + evento.ciudad : ''}</p>` : ''}
                        <p><strong class="text-brand-primario">Tipo de evento:</strong> ${evento.evento_tipo || 'No especificado'}</p>
                    </div>
                `;
                document.getElementById('btnConfirmarAsistencia').style.display = 'block';
            }

            document.getElementById('infoEventoContainer').classList.remove('hidden');
        }

        // Confirmar asistencia
        async function confirmarAsistencia() {
            if (!infoEventoActual) {
                mostrarMensaje('No hay información de evento para confirmar', 'error');
                return;
            }

            const token = localStorage.getItem('token');
            if (!token) {
                mostrarMensaje('Debes iniciar sesión', 'error');
                return;
            }

            const btnConfirmar = document.getElementById('btnConfirmarAsistencia');
            btnConfirmar.disabled = true;
            btnConfirmar.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Confirmando...';

            try {
                const apiUrl = window.API_BASE_URL || 'http://192.168.0.6:8000';
                const res = await fetch(`${apiUrl}/api/validar-asistencia-welcome`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        ticket_codigo: infoEventoActual.ticket_codigo,
                        modo_validacion: 'Manual'
                    }),
                });

                const data = await res.json();

                if (data.success) {
                    mostrarMensaje(data.message || '¡Asistencia confirmada correctamente!', 'success');
                    document.getElementById('ticketCodigoInput').value = '';
                    document.getElementById('infoEventoContainer').classList.add('hidden');
                    infoEventoActual = null;
                } else {
                    mostrarMensaje(data.error || 'Error al confirmar asistencia', 'error');
                }
            } catch (error) {
                console.error('Error confirmando asistencia:', error);
                mostrarMensaje('Error al confirmar asistencia. Por favor, intenta nuevamente.', 'error');
            } finally {
                btnConfirmar.disabled = false;
                btnConfirmar.innerHTML = '<i class="fas fa-check-circle mr-2"></i> Confirmar Asistencia';
            }
        }

        // Mostrar mensaje de resultado
        function mostrarMensaje(mensaje, tipo) {
            const mensajeDiv = document.getElementById('mensajeResultado');
            mensajeDiv.className = tipo === 'success' 
                ? 'mt-4 p-4 rounded-xl bg-green-100 border-2 border-green-500 text-green-800'
                : 'mt-4 p-4 rounded-xl bg-red-100 border-2 border-red-500 text-red-800';
            mensajeDiv.innerHTML = `
                <div class="flex items-center">
                    <i class="fas ${tipo === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-3 text-xl"></i>
                    <span class="font-semibold">${mensaje}</span>
                </div>
            `;
            mensajeDiv.classList.remove('hidden');

            setTimeout(() => {
                mensajeDiv.classList.add('hidden');
            }, 5000);
        }

        // Activar escáner QR
        async function activarEscannerQRWelcome() {
            const container = document.getElementById('qrScannerContainerWelcome');
            const video = document.getElementById('qrVideoWelcome');
            const canvas = document.getElementById('qrCanvasWelcome');
            const context = canvas.getContext('2d');

            try {
                const stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { facingMode: 'environment' } 
                });
                
                qrStream = stream;
                video.srcObject = stream;
                video.setAttribute('playsinline', true);
                video.play();
                container.style.display = 'block';
                qrScanning = true;

                video.addEventListener('loadedmetadata', () => {
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                });

                function scanQR() {
                    if (!qrScanning) return;

                    if (video.readyState === video.HAVE_ENOUGH_DATA) {
                        context.drawImage(video, 0, 0, canvas.width, canvas.height);
                        const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                        const code = jsQR(imageData.data, imageData.width, imageData.height);

                        if (code) {
                            detenerEscannerQRWelcome();
                            document.getElementById('ticketCodigoInput').value = code.data;
                            verificarTicket();
                        }
                    }

                    requestAnimationFrame(scanQR);
                }

                scanQR();

            } catch (error) {
                console.error('Error accediendo a la cámara:', error);
                mostrarMensaje('No se pudo acceder a la cámara. Por favor, verifica los permisos.', 'error');
            }
        }

        // Detener escáner QR
        function detenerEscannerQRWelcome() {
            qrScanning = false;
            if (qrStream) {
                qrStream.getTracks().forEach(track => track.stop());
                qrStream = null;
            }
            const video = document.getElementById('qrVideoWelcome');
            if (video) {
                video.srcObject = null;
            }
            document.getElementById('qrScannerContainerWelcome').style.display = 'none';
        }
    </script>
    @endauth
    </body>
</html>

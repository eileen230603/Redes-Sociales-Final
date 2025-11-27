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
            <div class="text-center animate-fade-in-up">
                <div class="inline-block mb-6">
                    <span class="px-4 py-2 bg-white/20 backdrop-blur-md rounded-full text-white text-sm font-semibold">
                        <i class="fas fa-rocket mr-2"></i> Plataforma de Impacto Social
                    </span>
                </div>
                <h1 class="text-5xl md:text-7xl font-extrabold text-white mb-6 leading-tight">
                    Conectando Comunidades,<br>
                    <span class="text-brand-acento drop-shadow-lg">Transformando Vidas</span>
                </h1>
                <p class="text-xl md:text-2xl text-white/95 mb-12 max-w-3xl mx-auto leading-relaxed font-light">
                    La plataforma que une ONGs, empresas y voluntarios para crear eventos que generan <strong>impacto real</strong> y <strong>medible</strong> en nuestras comunidades.
                </p>
                <div class="flex flex-col sm:flex-row gap-5 justify-center items-center mb-16">
                    <a href="{{ route('login') }}" class="group px-10 py-5 bg-white text-brand-primario rounded-2xl font-bold text-lg hover:shadow-2xl transition-all transform hover:scale-105 animate-pulse-glow">
                        <i class="fas fa-sign-in-alt mr-2 group-hover:translate-x-1 transition-transform"></i> Iniciar Sesión
                    </a>
                    <a href="{{ route('login') }}" class="px-10 py-5 bg-brand-acento text-white rounded-2xl font-bold text-lg hover:shadow-2xl transition-all transform hover:scale-105">
                        <i class="fas fa-user-plus mr-2"></i> Crear Cuenta Gratis
                    </a>
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
    </body>
</html>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>UNI2 • Registro Usuario Externo</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0C2B44 0%, #154a6b 50%, #0C2B44 100%);
            min-height: 100vh;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
            overflow-y: auto;
            animation: gradientShift 15s ease infinite;
        }

        @keyframes gradientShift {
            0%, 100% {
                background: linear-gradient(135deg, #0C2B44 0%, #154a6b 50%, #0C2B44 100%);
            }
            50% {
                background: linear-gradient(135deg, #0a2338 0%, #0C2B44 50%, #00A36C 100%);
            }
        }

        body::before {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: rgba(12, 43, 68, 0.1);
            border-radius: 50%;
            top: -100px;
            left: -100px;
            animation: float 8s ease-in-out infinite;
        }

        body::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: rgba(0, 163, 108, 0.08);
            border-radius: 50%;
            bottom: -150px;
            right: -150px;
            animation: float 10s ease-in-out infinite reverse;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0) scale(1);
            }
            50% {
                transform: translateY(-20px) scale(1.1);
            }
        }

        .register-container {
            display: flex;
            max-width: 1200px;
            width: 100%;
            background: #FFFFFF;
            border-radius: 30px;
            overflow: visible;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 1;
            animation: slideInUp 0.8s ease-out;
            margin: 20px auto;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .left-panel {
            flex: 1;
            padding: 60px 50px;
            background: #FFFFFF;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            position: relative;
            overflow: visible;
            animation: fadeInLeft 1s ease-out 0.2s both;
            min-height: 100%;
        }

        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .left-panel::before {
            content: '';
            position: absolute;
            width: 40px;
            height: 40px;
            background: rgba(0, 163, 108, 0.15);
            border-radius: 50%;
            top: 80px;
            left: 50px;
        }

        .left-panel::after {
            content: '';
            position: absolute;
            width: 60px;
            height: 60px;
            background: rgba(0, 163, 108, 0.1);
            border-radius: 50%;
            top: 30%;
            right: 80px;
        }

        .decorative-circles {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            pointer-events: none;
            overflow: hidden;
        }

        .circle {
            position: absolute;
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .circle-1 {
            width: 100px;
            height: 100px;
            top: 15%;
            right: 10%;
            background: rgba(0, 163, 108, 0.08);
            animation-delay: 0s;
        }

        .circle-2 {
            width: 70px;
            height: 70px;
            top: 60%;
            left: 15%;
            background: rgba(12, 43, 68, 0.06);
            animation-delay: 1s;
        }

        .circle-3 {
            width: 50px;
            height: 50px;
            bottom: 20%;
            right: 25%;
            background: rgba(0, 163, 108, 0.1);
            animation-delay: 2s;
        }

        .circle-4 {
            width: 35px;
            height: 35px;
            top: 40%;
            left: 10%;
            background: rgba(0, 163, 108, 0.15);
            animation-delay: 0.5s;
        }

        .circle-5 {
            width: 80px;
            height: 80px;
            bottom: 10%;
            left: 30%;
            background: rgba(12, 43, 68, 0.05);
            animation-delay: 1.5s;
        }

        .circle-6 {
            width: 45px;
            height: 45px;
            top: 25%;
            left: 40%;
            background: rgba(0, 163, 108, 0.08);
            animation-delay: 2.5s;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 30px;
            animation: fadeInScale 1s ease-out 0.4s both;
        }

        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.8);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .logo-icon {
            width: 50px;
            height: 50px;
            border: 3px solid #00A36C;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            animation: pulse 2s ease-in-out infinite;
            transition: transform 0.3s ease;
        }

        .logo-icon:hover {
            transform: rotate(360deg) scale(1.1);
        }

        @keyframes pulse {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(0, 163, 108, 0.4);
            }
            50% {
                box-shadow: 0 0 0 10px rgba(0, 163, 108, 0);
            }
        }

        .logo-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 8px;
        }

        .logo-text {
            display: flex;
            flex-direction: column;
        }

        .logo-text span:first-child {
            color: #00A36C;
            font-size: 14px;
            font-weight: 600;
        }

        .logo-text span:last-child {
            color: #0C2B44;
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .welcome-message {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 0;
            animation: fadeInUp 1s ease-out 0.6s both;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .welcome-title {
            font-size: 48px;
            font-weight: 700;
            color: #0C2B44;
            margin-bottom: 20px;
            text-align: center;
            animation: slideDown 0.8s ease-out 0.8s both;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .welcome-subtitle {
            font-size: 20px;
            color: #00A36C;
            font-weight: 600;
            margin-bottom: 30px;
            text-align: center;
        }

        .welcome-description {
            font-size: 16px;
            color: #666;
            text-align: center;
            line-height: 1.6;
            max-width: 400px;
        }

        .illustration-content {
            width: 100%;
            max-width: 300px;
            aspect-ratio: 1;
            background: linear-gradient(135deg, rgba(0, 163, 108, 0.15) 0%, rgba(0, 163, 108, 0.25) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            animation: rotateIn 1.2s ease-out 1s both, gentleFloat 4s ease-in-out infinite;
        }

        @keyframes rotateIn {
            from {
                opacity: 0;
                transform: rotate(-180deg) scale(0.5);
            }
            to {
                opacity: 1;
                transform: rotate(0deg) scale(1);
            }
        }

        @keyframes gentleFloat {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-15px);
            }
        }

        .illustration-content img {
            max-width: 90%;
            max-height: 90%;
            object-fit: contain;
        }

        .footer-text {
            font-size: 11px;
            color: #333333;
            margin-top: 20px;
            text-align: center;
        }

        .right-panel {
            flex: 1.2;
            padding: 60px 50px;
            background: linear-gradient(135deg, #0C2B44 0%, #154a6b 50%, #0C2B44 100%);
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            position: relative;
            overflow-y: auto;
            overflow-x: hidden;
            animation: fadeInRight 1s ease-out 0.3s both;
            min-height: 100%;
        }

        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .right-panel::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('{{ asset("assets/img/fondo.png") }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0.3;
            z-index: 0;
            animation: gentleFloat 20s ease-in-out infinite;
        }

        .right-panel::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(12, 43, 68, 0.7) 0%, rgba(21, 74, 107, 0.65) 50%, rgba(12, 43, 68, 0.7) 100%);
            z-index: 0;
        }

        .right-panel > div {
            position: relative;
            z-index: 1;
        }

        h1 {
            color: #FFFFFF;
            font-size: 42px;
            margin-bottom: 10px;
            font-weight: 300;
            animation: slideDown 0.8s ease-out 0.5s both;
        }

        .subtitle {
            color: rgba(255, 255, 255, 0.8);
            font-size: 16px;
            margin-bottom: 40px;
            animation: fadeIn 0.6s ease-out 0.7s both;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
            animation: fadeInUp 0.6s ease-out both;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-group:nth-child(1) { animation-delay: 0.8s; }
        .form-group:nth-child(2) { animation-delay: 0.9s; }
        .form-group:nth-child(3) { animation-delay: 1s; }
        .form-group:nth-child(4) { animation-delay: 1.1s; }
        .form-group:nth-child(5) { animation-delay: 1.2s; }
        .form-group:nth-child(6) { animation-delay: 1.3s; }
        .form-group:nth-child(7) { animation-delay: 1.4s; }
        .form-group:nth-child(8) { animation-delay: 1.5s; }
        .form-group:nth-child(9) { animation-delay: 1.6s; }
        .form-group:nth-child(10) { animation-delay: 1.7s; }

        label {
            display: block;
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .input-wrapper {
            position: relative;
        }

        input, textarea {
            width: 100%;
            padding: 14px 18px;
            border: none;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.15);
            color: #FFFFFF;
            font-size: 15px;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            font-family: inherit;
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        input::placeholder,
        textarea::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        input:focus,
        textarea:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.2);
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }

        input:hover,
        textarea:hover {
            background: rgba(255, 255, 255, 0.18);
        }

        #msg {
            margin-top: 15px;
            text-align: center;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.9);
            min-height: 20px;
            padding: 10px;
            border-radius: 8px;
        }

        #msg.success {
            background: rgba(0, 163, 108, 0.2);
            color: #00A36C;
        }

        #msg.error {
            background: rgba(220, 53, 69, 0.2);
            color: #ff6b6b;
        }

        .register-btn {
            width: 100%;
            padding: 15px;
            background: #00A36C;
            border: none;
            border-radius: 10px;
            color: #FFFFFF;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 20px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.6s ease-out 1.8s both;
        }

        .register-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .register-btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .register-btn:hover {
            background: #008c5c;
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 5px 20px rgba(0, 163, 108, 0.5);
        }

        .register-btn:active {
            transform: translateY(0) scale(0.98);
        }

        .register-btn i {
            position: relative;
            z-index: 1;
            margin-right: 8px;
        }

        .login-link {
            text-align: center;
            margin-top: 25px;
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
            animation: fadeIn 0.6s ease-out 2s both;
        }

        .login-link a {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: underline;
            font-weight: 600;
            transition: color 0.3s;
        }

        .login-link a:hover {
            color: #FFFFFF;
        }

        @media (max-width: 768px) {
            .register-container {
                flex-direction: column;
            }

            .left-panel, .right-panel {
                padding: 40px 30px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .welcome-title {
                font-size: 36px;
            }

            h1 {
                font-size: 32px;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="left-panel">
            <div class="decorative-circles">
                <div class="circle circle-1"></div>
                <div class="circle circle-2"></div>
                <div class="circle circle-3"></div>
                <div class="circle circle-4"></div>
                <div class="circle circle-5"></div>
                <div class="circle circle-6"></div>
            </div>
            <div>
                <div class="logo">
                    <div class="logo-icon">
                        <img src="{{ asset('assets/img/UNI2 - copia.png') }}" alt="UNI2">
                    </div>
                    <div class="logo-text">
                        <span>UNI2</span>
                        <span>Conectando comunidades</span>
                    </div>
      </div>

                <div class="welcome-message">
                    <h2 class="welcome-title">¡Únete!</h2>
                    <p class="welcome-subtitle">Crea tu cuenta como Usuario Individual</p>
                    <p class="welcome-description">
                        Forma parte de nuestra comunidad y participa en eventos que transforman vidas. 
                        Únete a miles de personas que ya están haciendo la diferencia.
                    </p>
                    <div class="illustration-content" style="margin-top: 30px;">
                        <img src="{{ asset('assets/img/log 2.png') }}" alt="UNI2 Logo">
                    </div>
                </div>
      </div>

            <div class="footer-text">
                © 2025 UNI2<br>
                Conectando comunidades, transformando vidas.
            </div>
      </div>

        <div class="right-panel">
      <div>
                <h1>Registro</h1>
                <p class="subtitle">Completa tus datos para crear tu cuenta</p>
                
                <form id="formRegister" novalidate>
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label for="nombre_usuario">
                                <i class="far fa-user"></i> Nombre de usuario
                            </label>
                            <input type="text" id="nombre_usuario" name="nombre_usuario" placeholder="Ingresa tu nombre de usuario" required>
                        </div>

                        <div class="form-group full-width">
                            <label for="correo_electronico">
                                <i class="far fa-envelope"></i> Correo electrónico
                            </label>
                            <input type="email" id="correo_electronico" name="correo_electronico" placeholder="tu@ejemplo.com" required>
                        </div>

                        <div class="form-group full-width">
                            <label for="contrasena">
                                <i class="far fa-lock"></i> Contraseña
                            </label>
                            <input type="password" id="contrasena" name="contrasena" placeholder="••••••••" minlength="6" required>
                        </div>

                        <div class="form-group">
                            <label for="nombres">
                                <i class="far fa-id-card"></i> Nombres
                            </label>
                            <input type="text" id="nombres" name="nombres" placeholder="Tus nombres" required>
      </div>

                        <div class="form-group">
                            <label for="apellidos">
                                <i class="far fa-id-card"></i> Apellidos
                            </label>
                            <input type="text" id="apellidos" name="apellidos" placeholder="Tus apellidos" required>
      </div>

                        <div class="form-group">
                            <label for="documento_identidad">
                                <i class="far fa-id-badge"></i> Documento de identidad
                            </label>
                            <input type="text" id="documento_identidad" name="documento_identidad" placeholder="Número de documento" required>
      </div>

                        <div class="form-group">
                            <label for="telefono">
                                <i class="far fa-phone"></i> Teléfono
                            </label>
                            <input type="text" id="telefono" name="telefono" placeholder="Tu teléfono">
      </div>

                        <div class="form-group full-width">
                            <label for="direccion">
                                <i class="far fa-map-marker-alt"></i> Dirección
                            </label>
                            <input type="text" id="direccion" name="direccion" placeholder="Tu dirección">
      </div>

                        <div class="form-group">
                            <label for="fecha_nacimiento">
                                <i class="far fa-calendar"></i> Fecha de nacimiento
                            </label>
                            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento">
      </div>

                        <div class="form-group full-width">
                            <label for="descripcion">
                                <i class="far fa-align-left"></i> Descripción
                            </label>
                            <textarea id="descripcion" name="descripcion" rows="3" placeholder="Cuéntanos sobre ti..."></textarea>
                        </div>
      </div>

                    <div id="msg"></div>

                    <button type="submit" class="register-btn">
                        <i class="fas fa-user-plus"></i> Registrar Usuario
        </button>
    </form>

                <div class="login-link">
                    ¿Ya tienes cuenta? <a href="/login">Inicia sesión</a>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/config.js') }}"></script>
    <script src="{{ asset('assets/js/register-externo.js') }}"></script>
</body>
</html>

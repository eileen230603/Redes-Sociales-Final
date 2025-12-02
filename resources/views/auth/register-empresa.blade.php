<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>UNI2 • Registro Empresa</title>
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
            align-items: center;
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

        /* Elementos decorativos geométricos */
        body::before {
            content: '';
            position: fixed;
            width: 500px;
            height: 500px;
            background: rgba(0, 163, 108, 0.05);
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
            top: -200px;
            left: -200px;
            animation: morph 20s ease-in-out infinite;
            z-index: 0;
        }

        body::after {
            content: '';
            position: fixed;
            width: 400px;
            height: 400px;
            background: rgba(12, 43, 68, 0.08);
            border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%;
            bottom: -150px;
            right: -150px;
            animation: morph 25s ease-in-out infinite reverse;
            z-index: 0;
        }

        @keyframes morph {
            0%, 100% {
                border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
            }
            25% {
                border-radius: 58% 42% 75% 25% / 76% 46% 54% 24%;
            }
            50% {
                border-radius: 50% 50% 33% 67% / 55% 27% 73% 45%;
            }
            75% {
                border-radius: 33% 67% 58% 42% / 63% 68% 32% 37%;
            }
        }

        .register-container {
            max-width: 900px;
            width: 100%;
            background: #FFFFFF;
            border-radius: 24px;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.4);
            position: relative;
            z-index: 1;
            animation: slideInScale 0.8s ease-out;
            overflow: hidden;
        }

        @keyframes slideInScale {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .header-section {
            background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%);
            padding: 40px 50px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .header-section::before {
            content: '';
            position: absolute;
            width: 200%;
            height: 200%;
            background: url('{{ asset("assets/img/fondo.png") }}');
            background-size: cover;
            opacity: 0.15;
            top: -50%;
            left: -50%;
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% {
                transform: translate(0, 0) rotate(0deg);
            }
            33% {
                transform: translate(30px, -30px) rotate(120deg);
            }
            66% {
                transform: translate(-20px, 20px) rotate(240deg);
            }
        }

        .header-section > * {
            position: relative;
            z-index: 1;
        }

        .logo-container {
            display: inline-flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            animation: fadeInDown 0.8s ease-out;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo-circle {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.4);
            }
            50% {
                box-shadow: 0 0 0 15px rgba(255, 255, 255, 0);
            }
        }

        .logo-circle img {
            width: 80%;
            height: 80%;
            object-fit: contain;
        }

        .header-title {
            color: #FFFFFF;
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            animation: fadeInDown 0.8s ease-out 0.2s both;
        }

        .header-subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
            font-weight: 400;
            animation: fadeInDown 0.8s ease-out 0.4s both;
        }

        .form-section {
            padding: 50px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
            margin-bottom: 30px;
        }

        .form-group {
            animation: fadeInUp 0.6s ease-out both;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-group:nth-child(1) { animation-delay: 0.1s; }
        .form-group:nth-child(2) { animation-delay: 0.2s; }
        .form-group:nth-child(3) { animation-delay: 0.3s; }
        .form-group:nth-child(4) { animation-delay: 0.4s; }
        .form-group:nth-child(5) { animation-delay: 0.5s; }
        .form-group:nth-child(6) { animation-delay: 0.6s; }
        .form-group:nth-child(7) { animation-delay: 0.7s; }
        .form-group:nth-child(8) { animation-delay: 0.8s; }
        .form-group:nth-child(9) { animation-delay: 0.9s; }
        .form-group:nth-child(10) { animation-delay: 1s; }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        label {
            display: block;
            color: #0C2B44;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        label i {
            margin-right: 8px;
            color: #00A36C;
            width: 18px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        textarea {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #E0E0E0;
            border-radius: 10px;
            background: #FAFAFA;
            color: #333;
            font-size: 15px;
            transition: all 0.3s ease;
            font-family: inherit;
        }

        input::placeholder,
        textarea::placeholder {
            color: #999;
        }

        input:focus,
        textarea:focus {
            outline: none;
            border-color: #00A36C;
            background: #FFFFFF;
            box-shadow: 0 0 0 4px rgba(0, 163, 108, 0.1);
        }

        textarea {
            resize: vertical;
            min-height: 90px;
            font-family: inherit;
        }

        #msg {
            text-align: center;
            padding: 15px;
            margin: 20px 0;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            min-height: 20px;
            animation: fadeIn 0.4s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        #msg.text-center.text-blue-700 {
            color: #0C2B44;
            background: rgba(0, 163, 108, 0.1);
            border: 2px solid rgba(0, 163, 108, 0.2);
        }

        #msg.text-center.text-green-600 {
            color: #00A36C;
            background: rgba(0, 163, 108, 0.1);
            border: 2px solid rgba(0, 163, 108, 0.3);
        }

        #msg.text-center.text-red-600 {
            color: #DC3545;
            background: rgba(220, 53, 69, 0.1);
            border: 2px solid rgba(220, 53, 69, 0.2);
        }

        .submit-container {
            text-align: center;
            margin-top: 30px;
        }

        .register-btn {
            padding: 16px 50px;
            background: linear-gradient(135deg, #0C2B44 0%, #00A36C 100%);
            color: #FFFFFF;
            border: none;
            border-radius: 12px;
            font-size: 17px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(0, 163, 108, 0.3);
            animation: fadeInUp 0.6s ease-out 1.1s both;
        }

        .register-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }

        .register-btn:hover::before {
            left: 100%;
        }

        .register-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 163, 108, 0.4);
        }

        .register-btn:active {
            transform: translateY(-1px);
        }

        .register-btn i {
            margin-right: 10px;
        }

        .login-link {
            text-align: center;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid #E0E0E0;
        }

        .login-link a {
            color: #00A36C;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: color 0.3s;
        }

        .login-link a:hover {
            color: #0C2B44;
            text-decoration: underline;
        }

        /* Decoraciones geométricas en las esquinas */
        .register-container::before {
            content: '';
            position: absolute;
            width: 150px;
            height: 150px;
            background: linear-gradient(135deg, rgba(0, 163, 108, 0.1), transparent);
            border-radius: 50%;
            top: -75px;
            right: -75px;
            z-index: 0;
        }

        .register-container::after {
            content: '';
            position: absolute;
            width: 120px;
            height: 120px;
            background: linear-gradient(225deg, rgba(12, 43, 68, 0.1), transparent);
            border-radius: 50%;
            bottom: -60px;
            left: -60px;
            z-index: 0;
        }

        .form-section {
            position: relative;
            z-index: 1;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .header-section {
                padding: 30px 25px;
            }

            .form-section {
                padding: 35px 25px;
            }

            .header-title {
                font-size: 28px;
            }

            .register-container {
                border-radius: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="header-section">
            <div class="logo-container">
                <div class="logo-circle">
                    <img src="{{ asset('assets/img/UNI2 - copia.png') }}" alt="UNI2">
                </div>
            </div>
            <h1 class="header-title">Registro de Empresa</h1>
            <p class="header-subtitle">Únete a nuestra plataforma y conecta tu empresa con causas sociales</p>
        </div>

        <div class="form-section">
            <form id="formEmpresa" novalidate>
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
                        <input type="email" id="correo_electronico" name="correo_electronico" placeholder="empresa@ejemplo.com" required>
      </div>

                    <div class="form-group full-width">
                        <label for="contrasena">
                            <i class="far fa-lock"></i> Contraseña
                        </label>
                        <input type="password" id="contrasena" name="contrasena" placeholder="••••••••" minlength="6" required>
      </div>

                    <div class="form-group">
                        <label for="nombre_empresa">
                            <i class="far fa-building"></i> Nombre de la Empresa
                        </label>
                        <input type="text" id="nombre_empresa" name="nombre_empresa" placeholder="Nombre comercial" required>
      </div>

                    <div class="form-group">
                        <label for="razon_social">
                            <i class="far fa-file-alt"></i> Razón Social
                        </label>
                        <input type="text" id="razon_social" name="razon_social" placeholder="Razón social legal">
      </div>

                    <div class="form-group">
                        <label for="NIT">
                            <i class="far fa-id-card"></i> NIT
                        </label>
                        <input type="text" id="NIT" name="NIT" placeholder="Número de identificación tributaria" required>
      </div>

                    <div class="form-group">
                        <label for="telefono">
                            <i class="far fa-phone"></i> Teléfono
                        </label>
                        <input type="text" id="telefono" name="telefono" placeholder="Teléfono de contacto">
      </div>

                    <div class="form-group full-width">
                        <label for="direccion">
                            <i class="far fa-map-marker-alt"></i> Dirección
                        </label>
                        <input type="text" id="direccion" name="direccion" placeholder="Dirección de la empresa">
      </div>

                    <div class="form-group">
                        <label for="sitio_web">
                            <i class="far fa-globe"></i> Sitio Web
                        </label>
                        <input type="text" id="sitio_web" name="sitio_web" placeholder="https://tu-empresa.com">
      </div>

                    <div class="form-group">
                        <label for="descripcion">
                            <i class="far fa-align-left"></i> Descripción
                        </label>
                        <textarea id="descripcion" name="descripcion" rows="3" placeholder="Describe tu empresa y su compromiso social..."></textarea>
      </div>
      </div>

                <div id="msg"></div>

                <div class="submit-container">
                    <button type="submit" class="register-btn">
                        <i class="fas fa-building"></i> Registrar Empresa
        </button>
      </div>
    </form>

            <div class="login-link">
                ¿Ya tienes cuenta? <a href="/login">Inicia sesión</a>
            </div>
        </div>
    </div>

    <script>
        if (typeof API_BASE_URL === 'undefined') {
            var API_BASE_URL = '{{ url("/") }}';
        }
    </script>
    <script src="{{ asset('assets/js/register-empresa.js') }}"></script>
</body>
</html>

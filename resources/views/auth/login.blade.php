<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>UNI2 • Iniciar sesión</title>
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
            overflow: hidden;
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

        .login-container {
            display: flex;
            max-width: 1100px;
            width: 100%;
            background: #FFFFFF;
            border-radius: 30px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 1;
            animation: slideInUp 0.8s ease-out;
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
            justify-content: space-between;
            position: relative;
            overflow: visible;
            animation: fadeInLeft 1s ease-out 0.2s both;
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

        @keyframes float {
            0%, 100% {
                transform: translateY(0) scale(1);
            }
            50% {
                transform: translateY(-20px) scale(1.1);
            }
        }

        .circle-1 {
            animation-delay: 0s;
        }

        .circle-2 {
            animation-delay: 1s;
        }

        .circle-3 {
            animation-delay: 2s;
        }

        .circle-4 {
            animation-delay: 0.5s;
        }

        .circle-5 {
            animation-delay: 1.5s;
        }

        .circle-6 {
            animation-delay: 2.5s;
        }

        .circle-1 {
            width: 100px;
            height: 100px;
            top: 15%;
            right: 10%;
            background: rgba(0, 163, 108, 0.08);
        }

        .circle-2 {
            width: 70px;
            height: 70px;
            top: 60%;
            left: 15%;
            background: rgba(12, 43, 68, 0.06);
        }

        .circle-3 {
            width: 50px;
            height: 50px;
            bottom: 20%;
            right: 25%;
            background: rgba(0, 163, 108, 0.1);
        }

        .circle-4 {
            width: 35px;
            height: 35px;
            top: 40%;
            left: 10%;
            background: rgba(0, 163, 108, 0.15);
        }

        .circle-5 {
            width: 80px;
            height: 80px;
            bottom: 10%;
            left: 30%;
            background: rgba(12, 43, 68, 0.05);
        }

        .circle-6 {
            width: 45px;
            height: 45px;
            top: 25%;
            left: 40%;
            background: rgba(0, 163, 108, 0.08);
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

        .illustration {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 0;
        }

        .illustration-content {
            width: 100%;
            max-width: 350px;
            aspect-ratio: 1;
            background: linear-gradient(135deg, rgba(0, 163, 108, 0.15) 0%, rgba(0, 163, 108, 0.25) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            animation: rotateIn 1.2s ease-out 0.6s both, gentleFloat 4s ease-in-out infinite;
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
        }

        .right-panel {
            flex: 1;
            padding: 60px 50px;
            background: linear-gradient(135deg, #0C2B44 0%, #154a6b 50%, #0C2B44 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
            animation: fadeInRight 1s ease-out 0.3s both;
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

        .right-panel > div {
            position: relative;
            z-index: 1;
        }

        h1 {
            color: #FFFFFF;
            font-size: 42px;
            margin-bottom: 40px;
            font-weight: 300;
            animation: slideDown 0.8s ease-out 0.5s both;
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

        .form-group {
            margin-bottom: 25px;
            animation: fadeInUp 0.6s ease-out both;
        }

        .form-group:nth-child(1) {
            animation-delay: 0.7s;
        }

        .form-group:nth-child(2) {
            animation-delay: 0.9s;
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

        input {
            width: 100%;
            padding: 14px 18px 14px 45px;
            border: none;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.15);
            color: #FFFFFF;
            font-size: 15px;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        input:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(5px);
        }

        input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        input:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.2);
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.7);
            font-size: 16px;
            transition: all 0.3s ease;
        }

        input:focus + .input-icon,
        .input-wrapper:focus-within .input-icon {
            color: #00A36C;
            transform: translateY(-50%) scale(1.2);
        }

        .forgot-password {
            text-align: right;
            margin-top: 8px;
        }

        .forgot-password a {
            color: rgba(255, 255, 255, 0.6);
            font-size: 12px;
            text-decoration: none;
            transition: color 0.3s;
        }

        .forgot-password a:hover {
            color: rgba(255, 255, 255, 0.9);
        }

        .login-btn {
            width: 100%;
            padding: 15px;
            background: #00A36C;
            border: none;
            border-radius: 10px;
            color: #FFFFFF;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 30px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.6s ease-out 1.1s both;
        }

        .login-btn::before {
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

        .login-btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .login-btn:hover {
            background: #008c5c;
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 5px 20px rgba(0, 163, 108, 0.5);
        }

        .login-btn:active {
            transform: translateY(0) scale(0.98);
        }

        .login-btn i {
            position: relative;
            z-index: 1;
        }

        #result {
            margin-top: 15px;
            text-align: center;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.9);
            min-height: 20px;
        }

        .separator {
            position: relative;
            margin: 30px 0;
            text-align: center;
            animation: fadeIn 0.6s ease-out 1.2s both;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .separator::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: rgba(255, 255, 255, 0.2);
        }

        .separator span {
            position: relative;
            background: linear-gradient(135deg, #0C2B44 0%, #154a6b 50%, #0C2B44 100%);
            padding: 0 15px;
            color: rgba(255, 255, 255, 0.7);
            font-size: 12px;
        }

        .register-section {
            margin-top: 25px;
            animation: fadeIn 0.6s ease-out 1.2s both;
        }

        .register-title {
            text-align: center;
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
            margin-bottom: 15px;
            font-weight: 500;
        }

        .register-buttons {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .register-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px 18px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            animation: fadeInUp 0.6s ease-out both;
            position: relative;
            overflow: hidden;
        }

        .register-btn:nth-child(1) {
            animation-delay: 1.3s;
        }

        .register-btn:nth-child(2) {
            animation-delay: 1.5s;
        }

        .register-btn:nth-child(3) {
            animation-delay: 1.7s;
        }

        .register-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.5s, height 0.5s;
        }

        .register-btn:hover::before {
            width: 200px;
            height: 200px;
        }

        .register-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.4);
            transform: translateY(-3px) scale(1.02);
            color: #FFFFFF;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .register-btn i {
            position: relative;
            z-index: 1;
            transition: transform 0.3s ease;
        }

        .register-btn:hover i {
            transform: scale(1.2) rotate(5deg);
        }

        .register-btn i {
            margin-right: 10px;
            font-size: 16px;
        }

        .register-btn.user {
            border-color: rgba(0, 163, 108, 0.3);
        }

        .register-btn.user:hover {
            background: rgba(0, 163, 108, 0.2);
            border-color: #00A36C;
        }

        .register-btn.ong {
            border-color: rgba(12, 43, 68, 0.3);
        }

        .register-btn.ong:hover {
            background: rgba(12, 43, 68, 0.2);
            border-color: #0C2B44;
        }

        .register-btn.empresa {
            border-color: rgba(0, 163, 108, 0.3);
        }

        .register-btn.empresa:hover {
            background: rgba(0, 163, 108, 0.2);
            border-color: #00A36C;
        }

        .bottom-links {
            margin-top: 50px;
            text-align: center;
        }

        .bottom-links a {
            color: rgba(255, 255, 255, 0.5);
            font-size: 12px;
            text-decoration: none;
            display: block;
            margin-bottom: 5px;
            transition: color 0.3s;
        }

        .bottom-links a:hover {
            color: rgba(255, 255, 255, 0.8);
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.7);
            cursor: pointer;
            font-size: 16px;
            padding: 5px;
            transition: color 0.3s;
        }

        .toggle-password:hover {
            color: rgba(255, 255, 255, 0.9);
        }

        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
            }

            .left-panel, .right-panel {
                padding: 40px 30px;
            }

            .illustration-content {
                max-width: 250px;
            }

            h1 {
                font-size: 32px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
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

                <div class="illustration">
                    <div class="illustration-content">
                        <img src="{{ asset('assets/img/iniii.png') }}" alt="UNI2 Logo">
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
                <h1>Login</h1>
                
                <form id="loginForm" novalidate>
                    <div class="form-group">
                        <label for="email">
                            <i class="far fa-envelope"></i> Correo electrónico
                        </label>
                        <div class="input-wrapper">
                            <i class="fas fa-user input-icon"></i>
                            <input type="email" id="email" name="email" placeholder="tu@ejemplo.com" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">
                            <i class="far fa-lock"></i> Contraseña
          </label>
                        <div class="input-wrapper">
                            <i class="fas fa-key input-icon"></i>
                            <input type="password" id="password" name="password" placeholder="••••••••" minlength="6" required>
                            <button type="button" id="togglePassword" class="toggle-password">
              <i class="fas fa-eye" id="eyeIcon"></i>
            </button>
          </div>
        </div>

                    <button type="submit" class="login-btn">
                        <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
        </button>
      </form>

                <div id="result"></div>

                <div class="separator">
                    <span>¿No tienes una cuenta?</span>
      </div>

                <div class="register-section">
                    <p class="register-title">Regístrate como:</p>
                    <div class="register-buttons">
                        <a href="{{ url('/register-externo') }}" class="register-btn user">
                            <i class="fas fa-user"></i> Usuario Individual
                        </a>

                        <a href="{{ url('/register-ong') }}" class="register-btn ong">
                            <i class="fas fa-hands-helping"></i> Organización (ONG)
                        </a>

                        <a href="{{ url('/register-empresa') }}" class="register-btn empresa">
                            <i class="fas fa-building"></i> Empresa
                        </a>
                    </div>
                </div>
                
                <div class="bottom-links">
                    <a href="#">Términos y Servicios</a>
                    <a href="#">¿Tienes un problema? Contáctanos</a>
                </div>
        </div>
      </div>
    </div>

  <script src="{{ asset('assets/js/config.js') }}"></script>
  <script src="{{ asset('assets/js/login.js') }}"></script>
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        });
    </script>
</body>
</html>

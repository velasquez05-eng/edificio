<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | SEINT</title>
    <link href="../../includes/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../includes/css/login.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        /* --- Estilos adicionales --- */
        body {
            background-color: #f5f7fa;
        }

        .login-container {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            border-radius: 16px;
            overflow: hidden;
            background: #fff;
        }

        .login-right {
            padding: 2rem 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* Lado izquierdo con colores del sistema */
        .login-left {
            background: linear-gradient(135deg, #2a7595 0%, #0d3d47 100%);
            color: white;
            padding: 3rem 2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #afcfce;
        }

        .system-title {
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: white;
        }

        .system-subtitle {
            color: #afcfce;
            font-size: 1rem;
        }

        .features {
            margin-top: 1.5rem;
        }

        .feature-item h5 {
            color: #99b6a0;
        }

        .feature-item p {
            color: #afcfce;
            font-size: 0.9rem;
        }

        /* Ícono dinámico */
        .icon-container {
            text-align: center;
            margin-bottom: 1rem;
            position: relative;
            height: 80px;
        }

        .icon-dynamic {
            font-size: 3.5rem;
            color: #6c757d;
            transition: all 0.5s ease;
            opacity: 0;
            position: absolute;
            left: 50%;
            transform: translateX(-50%) scale(0.8);
        }

        .icon-dynamic.active {
            opacity: 1;
            transform: translateX(-50%) scale(1);
        }

        .icon-dynamic.user {
            color: #2a7595;
        }

        .icon-dynamic.robot-error {
            color: #dc3545;
        }

        h3 {
            text-align: center;
            font-weight: 700;
            color: #333;
            margin-bottom: 1.5rem;
            transition: color 0.3s ease;
        }

        .form-label {
            font-weight: 500;
            color: #0d3d47;
        }

        .password-container {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #888;
        }

        .toggle-password:hover {
            color: #2a7595;
        }

        .btn-login {
            background-color: #368979;
            border-color: #368979;
            transition: all 0.3s ease-in-out;
            border-radius: 10px;
            font-weight: 600;
        }

        .btn-login:hover {
            background-color: #2a7595;
            border-color: #2a7595;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(42, 117, 149, 0.3);
        }

        #recaptchaError {
            font-size: 0.9rem;
            color: #d9534f;
            display: none;
            text-align: center;
        }

        .login-links {
            text-align: center;
            margin-top: 10px;
        }

        .forgot-password-link {
            text-decoration: none;
            color: #2a7595;
            font-size: 0.9rem;
        }

        .forgot-password-link:hover {
            color: #0d3d47;
            text-decoration: underline;
        }

        .form-control:focus {
            border-color: #2a7595;
            box-shadow: 0 0 0 0.25rem rgba(42, 117, 149, 0.25);
        }
    </style>
</head>
<body>
<div class="container">
    <div class="login-container">
        <div class="row g-0">
            <!-- Lado izquierdo -->
            <div class="col-md-6 login-left">
                <div class="logo-container">
                    <div class="logo">
                        <i class="fas fa-building"></i>
                    </div>
                    <h2 class="system-title">SEINT</h2>
                    <p class="system-subtitle">Gestión integral de espacios y servicios</p>
                </div>
                <div class="features">
                    <div class="feature-item mb-4">
                        <h5><i class="fas fa-check-circle me-2"></i>Acceso personalizado</h5>
                        <p>Diferentes perfiles para usuarios y personal</p>
                    </div>
                    <div class="feature-item mb-4">
                        <h5><i class="fas fa-check-circle me-2"></i>Gestión centralizada</h5>
                        <p>Controla todos los servicios desde una plataforma</p>
                    </div>
                    <div class="feature-item">
                        <h5><i class="fas fa-check-circle me-2"></i>Interfaz intuitiva</h5>
                        <p>Diseño moderno y fácil de usar</p>
                    </div>
                </div>
            </div>

            <!-- Lado derecho -->
            <div class="col-md-6 login-right">
                <form method="POST" action="../controlador/PersonaControlador.php" id="loginForm">
                    <input type="hidden" name="action" value="login">



                    <h3 id="loginTitle">Login</h3>

                    <div class="mb-3">
                        <label for="username" class="form-label">Usuario o Email</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Ingresa tu usuario o correo electrónico" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <div class="password-container">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Ingresa tu contraseña" required>
                            <span class="toggle-password" id="togglePassword">
                                <i class="far fa-eye"></i>
                            </span>
                        </div>
                    </div>

                    <div class="mb-3 text-center">
                        <div class="g-recaptcha"
                             data-sitekey="6LdZwe0rAAAAABg8fRgVhiG1lAHj0jW1ippKCqg9"
                             data-callback="recaptchaSuccess"
                             data-expired-callback="recaptchaExpired">
                        </div>
                        <div id="recaptchaError">Por favor, marca "No soy un robot"</div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 btn-login">Iniciar Sesión</button>

                    <div class="login-links">
                        <a href="RecuperarVista.php" class="forgot-password-link">¿Olvidaste tu contraseña?</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="../../includes/js/bootstrap.bundle.min.js"></script>
<script>
    // Mostrar/ocultar contraseña
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const icon = this.querySelector('i');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    });







</script>
</body>
</html>
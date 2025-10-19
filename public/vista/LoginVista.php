<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | SEINT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        :root {
            --celeste: #afefce;
            --verde-suave: #99b6a0;
            --verde: #368979;
            --azul: #2a7595;
            --azul-oscuro: #0d3d47;
        }

        body {
            background: linear-gradient(135deg, var(--celeste) 0%, var(--azul) 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            max-width: 1000px;
            width: 100%;
            margin: auto;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(13, 61, 71, 0.25);
            background: white;
            transition: all 0.3s ease;
        }

        .login-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 45px rgba(13, 61, 71, 0.3);
        }

        .login-left {
            background: linear-gradient(135deg, var(--azul-oscuro) 0%, var(--azul) 100%);
            color: white;
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-right {
            background-color: white;
            padding: 50px 40px;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo {
            width: 120px;
            height: 120px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 50px;
            transition: transform 0.3s ease;
        }

        .logo:hover {
            transform: rotate(10deg) scale(1.05);
        }

        .system-title {
            font-size: 26px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .system-subtitle {
            font-size: 16px;
            opacity: 0.9;
        }

        .feature-item h5 {
            font-weight: 600;
        }

        .form-control {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 12px 15px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--azul);
            box-shadow: 0 0 10px rgba(42, 117, 149, 0.25);
            transform: scale(1.02);
        }

        .password-container {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 12px;
            cursor: pointer;
            color: var(--azul);
            transition: color 0.3s ease;
            z-index: 5;
        }

        .toggle-password:hover {
            color: var(--azul-oscuro);
        }

        /* Solución para reposicionar los iconos de validación */
        .was-validated .form-control:valid,
        .was-validated .form-control:invalid {
            padding-right: 45px;
            background-position: right 15px center;
        }

        .was-validated .password-container .form-control:valid,
        .was-validated .password-container .form-control:invalid {
            padding-right: 70px;
            background-position: right 40px center;
        }

        /* Centrar el reCAPTCHA */
        .g-recaptcha {
            display: flex;
            justify-content: center;
        }

        .g-recaptcha > div {
            margin: 0 auto;
        }

        .btn-login {
            background-color: var(--verde);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(54, 137, 121, 0.3);
        }

        .btn-login:hover {
            background-color: var(--azul);
            box-shadow: 0 6px 15px rgba(42, 117, 149, 0.4);
            transform: translateY(-2px);
        }

        .login-links {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .login-links a {
            color: var(--azul);
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .login-links a:hover {
            color: var(--azul-oscuro);
            text-decoration: underline;
        }

        .alert {
            border-radius: 10px;
            font-size: 14px;
        }

        #loginTitle {
            font-weight: 700;
            margin-bottom: 25px;
            text-align: center;
            color: var(--azul-oscuro);
        }

        /* Nuevos estilos para el contador de bloqueo */
        .bloqueo-container {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            border: 1px solid #ffc107;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            text-align: center;
        }

        .bloqueo-titulo {
            color: #856404;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .bloqueo-tiempo {
            font-size: 24px;
            font-weight: bold;
            color: #d63031;
            font-family: 'Courier New', monospace;
        }

        .bloqueo-mensaje {
            color: #856404;
            font-size: 14px;
            margin-top: 5px;
        }

        .btn-login:disabled {
            background-color: #6c757d;
            cursor: not-allowed;
        }

        .form-control:disabled {
            background-color: #e9ecef;
            opacity: 1;
        }

        .g-recaptcha-disabled {
            opacity: 0.6;
            pointer-events: none;
        }

        @media (max-width: 768px) {
            .login-left {
                display: none;
            }

            .login-right {
                padding: 30px 25px;
            }

            .login-container {
                box-shadow: 0 10px 30px rgba(13, 61, 71, 0.25);
            }

            /* Ajustes para reCAPTCHA en móviles */
            .g-recaptcha {
                transform: scale(0.9);
                transform-origin: center;
            }
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
                <form method="POST" action="../controlador/PersonaControlador.php" id="loginForm" novalidate>
                    <input type="hidden" name="action" value="login">

                    <h3 id="loginTitle">Iniciar Sesión</h3>

                    <!-- Contenedor para mostrar el bloqueo -->
                    <div id="bloqueoContainer" class="bloqueo-container d-none">
                        <div class="bloqueo-titulo">
                            <i class="fas fa-lock me-2"></i>Cuenta Temporalmente Bloqueada
                        </div>
                        <div class="bloqueo-tiempo" id="tiempoRestante">
                            00:00
                        </div>
                        <div class="bloqueo-mensaje">
                            Podrás intentar nuevamente cuando el tiempo finalice
                        </div>
                    </div>

                    <!-- Contenedor para mensajes de error (se oculta cuando hay bloqueo) -->
                    <div id="alertContainer">
                        <?php if (isset($_GET['error'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo htmlspecialchars($_GET['error']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_GET['success'])): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php echo htmlspecialchars($_GET['success']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="username" class="form-label fw-semibold">Usuario o Email</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Ingresa tu usuario o correo electrónico" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Contraseña</label>
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
                        <div id="recaptchaError" class="text-danger small mt-2 d-none">Por favor, marca "No soy un robot"</div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 btn-login" id="btnLogin">Iniciar Sesión</button>

                    <div class="login-links">
                        <a href="RecuperarVista.php" class="forgot-password-link">¿Olvidaste tu contraseña?</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Variables globales
    let tiempoRestante = 0;
    let contadorInterval = null;
    let recaptchaWidgetId = null;

    // Inicializar reCAPTCHA
    function onloadCallback() {
        recaptchaWidgetId = grecaptcha.render(document.querySelector('.g-recaptcha'), {
            'sitekey': '6LdZwe0rAAAAABg8fRgVhiG1lAHj0jW1ippKCqg9',
            'callback': recaptchaSuccess,
            'expired-callback': recaptchaExpired
        });
    }

    // Mostrar/ocultar contraseña
    document.getElementById('togglePassword').addEventListener('click', function () {
        const passwordInput = document.getElementById('password');
        const icon = this.querySelector('i');
        const isPassword = passwordInput.type === 'password';
        passwordInput.type = isPassword ? 'text' : 'password';
        icon.classList.toggle('fa-eye', !isPassword);
        icon.classList.toggle('fa-eye-slash', isPassword);
    });

    // reCAPTCHA
    let recaptchaValid = false;
    function recaptchaSuccess() {
        recaptchaValid = true;
        document.getElementById('recaptchaError').classList.add('d-none');
    }
    function recaptchaExpired() {
        recaptchaValid = false;
        document.getElementById('recaptchaError').classList.remove('d-none');
    }

    // Validación antes de enviar
    document.getElementById('loginForm').addEventListener('submit', function (e) {
        if (!this.checkValidity() || !recaptchaValid || tiempoRestante > 0) {
            e.preventDefault();
            this.classList.add('was-validated');
            if (!recaptchaValid) {
                document.getElementById('recaptchaError').classList.remove('d-none');
            }
        }
    });

    // Función para formatear el tiempo
    function formatearTiempo(segundos) {
        const minutos = Math.floor(segundos / 60);
        const segundosRestantes = segundos % 60;
        return `${minutos.toString().padStart(2, '0')}:${segundosRestantes.toString().padStart(2, '0')}`;
    }

    // Función para actualizar el contador
    function actualizarContador() {
        if (tiempoRestante > 0) {
            tiempoRestante--;
            document.getElementById('tiempoRestante').textContent = formatearTiempo(tiempoRestante);

            // Deshabilitar el formulario
            document.getElementById('username').disabled = true;
            document.getElementById('password').disabled = true;
            document.getElementById('btnLogin').disabled = true;
            document.querySelector('.g-recaptcha').classList.add('g-recaptcha-disabled');

        } else {
            // Tiempo terminado, habilitar formulario
            clearInterval(contadorInterval);
            contadorInterval = null;
            document.getElementById('bloqueoContainer').classList.add('d-none');
            document.getElementById('alertContainer').style.display = 'block';
            document.getElementById('username').disabled = false;
            document.getElementById('password').disabled = false;
            document.getElementById('btnLogin').disabled = false;
            document.querySelector('.g-recaptcha').classList.remove('g-recaptcha-disabled');

            // Resetear reCAPTCHA correctamente
            if (window.grecaptcha && recaptchaWidgetId !== null) {
                grecaptcha.reset(recaptchaWidgetId);
            }
            recaptchaValid = false;
        }
    }

    // Función para verificar estado del usuario via AJAX
    function verificarEstadoUsuario() {
        const username = document.getElementById('username').value.trim();
        if (!username) return;

        // Crear FormData para enviar la petición
        const formData = new FormData();
        formData.append('action', 'verificarBloqueo');
        formData.append('username', username);

        fetch('../controlador/PersonaControlador.php', {
            method: 'POST',
            body: formData
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(data => {
                if (data.bloqueado && !data.bloqueo_permanente && data.segundos_restantes > 0) {
                    tiempoRestante = data.segundos_restantes;
                    document.getElementById('bloqueoContainer').classList.remove('d-none');
                    document.getElementById('alertContainer').style.display = 'none'; // Ocultar alertas
                    document.getElementById('tiempoRestante').textContent = formatearTiempo(tiempoRestante);

                    // Iniciar contador si no está corriendo
                    if (!contadorInterval) {
                        contadorInterval = setInterval(actualizarContador, 1000);
                        actualizarContador(); // Ejecutar inmediatamente
                    }
                } else {
                    // Mostrar alertas si no hay bloqueo
                    document.getElementById('alertContainer').style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error al verificar estado:', error);
                document.getElementById('alertContainer').style.display = 'block';
            });
    }

    // Inicializar cuando la página carga
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert');
        if (alerts.length > 0) {
            setTimeout(() => {
                alerts.forEach(alert => {
                    alert.classList.remove('show');
                    alert.classList.add('fade');
                    setTimeout(() => alert.remove(), 500);
                });
            }, 15000);
        }

        // Verificar si hay parámetros de bloqueo en la URL
        const urlParams = new URLSearchParams(window.location.search);
        const tiempoParam = urlParams.get('tiempo');
        const bloqueadoParam = urlParams.get('bloqueado');

        if (bloqueadoParam === 'true' && tiempoParam) {
            tiempoRestante = parseInt(tiempoParam);
            if (tiempoRestante > 0) {
                document.getElementById('bloqueoContainer').classList.remove('d-none');
                document.getElementById('alertContainer').style.display = 'none'; // Ocultar alertas
                document.getElementById('tiempoRestante').textContent = formatearTiempo(tiempoRestante);

                // Iniciar contador
                contadorInterval = setInterval(actualizarContador, 1000);
                actualizarContador(); // Ejecutar inmediatamente
            }
        }

        // Verificar estado cuando el usuario pierde el foco del username
        document.getElementById('username').addEventListener('blur', verificarEstadoUsuario);

        // Limpiar contador si el usuario cambia el username
        document.getElementById('username').addEventListener('input', function() {
            if (contadorInterval) {
                clearInterval(contadorInterval);
                contadorInterval = null;
            }
            document.getElementById('bloqueoContainer').classList.add('d-none');
            document.getElementById('alertContainer').style.display = 'block'; // Mostrar alertas
            document.getElementById('username').disabled = false;
            document.getElementById('password').disabled = false;
            document.getElementById('btnLogin').disabled = false;
            document.querySelector('.g-recaptcha').classList.remove('g-recaptcha-disabled');
        });
    });
</script>
</body>
</html>
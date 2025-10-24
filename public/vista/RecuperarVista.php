<?php
session_start();
$etapa = $_GET['etapa'] ?? 'solicitud'; // solicitud, codigo, nueva_contrasena
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña | SEINT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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

        .btn-primary {
            background-color: var(--verde);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(54, 137, 121, 0.3);
        }

        .btn-primary:hover {
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

        .page-title {
            font-weight: 700;
            margin-bottom: 25px;
            text-align: center;
            color: var(--azul-oscuro);
        }

        .info-text {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 14px;
            color: #495057;
        }

        .password-requirements {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
            font-size: 0.9rem;
        }

        .requirement-met {
            color: #198754;
        }

        .requirement-not-met {
            color: #6c757d;
        }

        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }

        .step {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 15px;
            font-weight: bold;
            position: relative;
            color: #6c757d;
        }

        .step.active {
            background-color: var(--verde);
            color: white;
        }

        .step.completed {
            background-color: var(--azul);
            color: white;
        }

        .step-line {
            position: absolute;
            top: 50%;
            width: 30px;
            height: 2px;
            background-color: #e9ecef;
            z-index: -1;
        }

        .step-line.completed {
            background-color: var(--azul);
        }

        .step-line.left {
            left: -30px;
        }

        .step-line.right {
            right: -30px;
        }

        .step-label {
            position: absolute;
            top: 40px;
            left: 50%;
            transform: translateX(-50%);
            white-space: nowrap;
            font-size: 12px;
            color: #6c757d;
        }

        .step.active .step-label {
            color: var(--verde);
            font-weight: 600;
        }

        .step.completed .step-label {
            color: var(--azul);
        }

        .code-inputs {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .code-input {
            width: 50px;
            height: 60px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            border: 2px solid #ddd;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .code-input:focus {
            border-color: var(--azul);
            box-shadow: 0 0 5px rgba(42, 117, 149, 0.3);
            transform: scale(1.05);
        }

        .resend-code {
            text-align: center;
            margin-top: 15px;
        }

        .resend-code button {
            background: none;
            border: none;
            color: var(--azul);
            text-decoration: underline;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .resend-code button:hover {
            color: var(--azul-oscuro);
        }

        .resend-code button:disabled {
            color: #6c757d;
            cursor: not-allowed;
        }

        .resend-code form {
            display: inline;
        }

        .countdown {
            font-weight: bold;
            color: var(--azul);
        }

        .password-strength {
            margin-top: 5px;
            font-size: 0.85rem;
        }

        .strength-weak {
            color: #dc3545;
        }

        .strength-medium {
            color: #fd7e14;
        }

        .strength-strong {
            color: #198754;
        }

        .loading {
            display: none;
            text-align: center;
            margin: 10px 0;
        }

        .spinner-border {
            width: 1.5rem;
            height: 1.5rem;
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

            .step {
                margin: 0 10px;
            }

            .step-line {
                width: 20px;
            }

            .step-line.left {
                left: -20px;
            }

            .step-line.right {
                right: -20px;
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
                        <h5><i class="fas fa-check-circle me-2"></i>Proceso Seguro</h5>
                        <p>Validación por código temporal</p>
                    </div>
                    <div class="feature-item mb-4">
                        <h5><i class="fas fa-check-circle me-2"></i>Requisitos de Contraseña</h5>
                        <p class="mb-0">• Mínimo 8 caracteres<br>
                            • Al menos una letra mayúscula<br>
                            • Al menos una letra minúscula<br>
                            • Al menos un número<br>
                            • Al menos un símbolo (@$!%*?&)</p>
                    </div>
                    <div class="feature-item">
                        <h5><i class="fas fa-check-circle me-2"></i>Proceso de Recuperación</h5>
                        <p class="mb-0">• Ingresa tu email<br>
                            • Verifica el código<br>
                            • Crea nueva contraseña<br>
                            • Código válido por 30 minutos</p>
                    </div>
                </div>
            </div>

            <!-- Lado derecho -->
            <div class="col-md-6 login-right">
                <!-- Indicador de pasos -->
                <div class="step-indicator">
                    <div class="step <?php echo $etapa === 'solicitud' ? 'active' : ($etapa === 'codigo' || $etapa === 'nueva_contrasena' ? 'completed' : ''); ?>">
                        <span>1</span>
                        <div class="step-label">Solicitud</div>
                    </div>
                    <div class="step <?php echo $etapa === 'codigo' ? 'active' : ($etapa === 'nueva_contrasena' ? 'completed' : ''); ?>">
                        <span>2</span>
                        <div class="step-label">Código</div>
                    </div>
                    <div class="step <?php echo $etapa === 'nueva_contrasena' ? 'active' : ''; ?>">
                        <span>3</span>
                        <div class="step-label">Contraseña</div>
                    </div>
                </div>

                <?php if ($etapa === 'solicitud'): ?>
                    <h3 class="page-title">Recuperar Contraseña</h3>
                <?php elseif ($etapa === 'codigo'): ?>
                    <h3 class="page-title">Verificar Código</h3>
                <?php elseif ($etapa === 'nueva_contrasena'): ?>
                    <h3 class="page-title">Nueva Contraseña</h3>
                <?php endif; ?>

                <!-- Mostrar mensajes de error/success -->
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php
                        echo $_SESSION['error'];
                        unset($_SESSION['error']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php
                        echo $_SESSION['success'];
                        unset($_SESSION['success']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                    </div>
                <?php endif; ?>

                <?php if ($etapa === 'solicitud'): ?>
                    <form method="POST" action="../controlador/PersonaControlador.php" id="solicitudForm">
                        <input type="hidden" name="action" value="solicitarCodigo">

                        <div class="info-text">
                            <i class="fas fa-info-circle me-2"></i>
                            Ingresa tu email y te enviaremos un código de recuperación.
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Correo Electrónico</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Ingresa tu correo electrónico" required>
                        </div>

                        <div class="loading" id="loadingSolicitud">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Enviando código...</span>
                            </div>
                            <p class="mt-2">Enviando código de verificación...</p>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 btn-login" id="btnSolicitar">Enviar Código</button>

                        <div class="login-links">
                            <a href="LoginVista.php" class="register-link">Volver al Login</a>
                        </div>
                    </form>

                <?php elseif ($etapa === 'codigo'): ?>
                    <!-- Verificar que existe id_persona en sesión -->
                    <?php if (!isset($_SESSION['id_persona'])): ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Sesión expirada. Por favor, solicita un nuevo código.
                        </div>
                        <div class="text-center">
                            <a href="RecuperarVista.php?etapa=solicitud" class="btn btn-primary">Solicitar Nuevo Código</a>
                        </div>
                    <?php else: ?>
                        <form method="POST" action="../controlador/PersonaControlador.php" id="codigoForm">
                            <input type="hidden" name="action" value="verificarCodigo">

                            <div class="info-text">
                                <i class="fas fa-envelope me-2"></i>
                                Hemos enviado un código de 6 dígitos a tu email.
                                <br><small class="text-muted">Código asociado a: <?php echo $_SESSION['email_recuperacion'] ?? ''; ?></small>
                            </div>



                            <div class="mb-3">
                                <label for="codigo" class="form-label fw-semibold">Código de Verificación</label>
                                <div class="code-inputs">
                                    <input type="text" class="code-input" maxlength="1" pattern="[0-9]" required>
                                    <input type="text" class="code-input" maxlength="1" pattern="[0-9]" required>
                                    <input type="text" class="code-input" maxlength="1" pattern="[0-9]" required>
                                    <input type="text" class="code-input" maxlength="1" pattern="[0-9]" required>
                                    <input type="text" class="code-input" maxlength="1" pattern="[0-9]" required>
                                    <input type="text" class="code-input" maxlength="1" pattern="[0-9]" required>
                                </div>
                                <input type="hidden" id="codigoCompleto" name="codigo">
                                <small class="form-text text-muted">Ingresa exactamente 6 dígitos</small>
                            </div>



                            <button type="submit" class="btn btn-primary w-100 btn-login" id="btnVerificar">Verificar Código</button>


                        </form>
                        <div class="resend-code">
                            <form method="POST" action="../controlador/PersonaControlador.php" id="reenviarForm">
                                <input type="hidden" name="action" value="solicitarCodigo">
                                <input type="hidden" name="email" value="<?php echo $_SESSION['email_recuperacion'] ?? ''; ?>">
                                <button type="submit" id="btnReenviar" disabled>
                                    Reenviar código <span id="countdown" class="countdown">(60)</span>
                                </button>
                            </form>
                        </div>
                        <div class="login-links">
                            <a href="RecuperarVista.php?etapa=solicitud" class="register-link">Volver atrás</a>
                        </div>
                    <?php endif; ?>

                <?php elseif ($etapa === 'nueva_contrasena'): ?>
                    <!-- Verificar que el código fue validado y existe id_persona -->
                    <?php if (!isset($_SESSION['id_persona']) || !isset($_SESSION['codigo_valido'])): ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Acceso no autorizado. Por favor, completa el proceso de verificación.
                        </div>
                        <div class="text-center">
                            <a href="RecuperarVista.php?etapa=solicitud" class="btn btn-primary">Iniciar Proceso</a>
                        </div>
                    <?php else: ?>
                        <form method="POST" action="../controlador/PersonaControlador.php" id="contrasenaForm">
                            <input type="hidden" name="action" value="cambiarContraseña">
                            <input type="hidden" name="id_persona" value="<?php echo $_SESSION['id_persona']; ?>">

                            <div class="info-text">
                                <i class="fas fa-user me-2"></i>
                                Crea tu nueva contraseña para la cuenta: <?php echo $_SESSION['email_recuperacion'] ?? ''; ?>
                            </div>

                            <div class="mb-3">
                                <label for="new_password" class="form-label fw-semibold">Nueva Contraseña</label>
                                <div class="password-container">
                                    <input type="password" class="form-control" id="new_password" name="new_password"
                                           placeholder="Ingresa tu nueva contraseña" required>
                                    <span class="toggle-password" onclick="togglePassword('new_password')">
                                        <i class="far fa-eye"></i>
                                    </span>
                                </div>
                                <div class="password-strength" id="passwordStrength"></div>
                            </div>

                            <div class="mb-3">
                                <label for="confirmar_contrasena" class="form-label fw-semibold">Confirmar Contraseña</label>
                                <div class="password-container">
                                    <input type="password" class="form-control" id="confirmar_contrasena" name="confirmar_contrasena"
                                           placeholder="Confirma tu nueva contraseña" required>
                                    <span class="toggle-password" onclick="togglePassword('confirmar_contrasena')">
                                        <i class="far fa-eye"></i>
                                    </span>
                                </div>
                                <div class="text-danger small mt-1" id="passwordMatchError"></div>
                            </div>

                            <div class="password-requirements">
                                <strong>Requisitos de seguridad:</strong><br>
                                <span id="reqLength" class="requirement-not-met">• Mínimo 8 caracteres</span><br>
                                <span id="reqUpper" class="requirement-not-met">• Al menos una letra mayúscula</span><br>
                                <span id="reqLower" class="requirement-not-met">• Al menos una letra minúscula</span><br>
                                <span id="reqNumber" class="requirement-not-met">• Al menos un número</span><br>
                                <span id="reqSymbol" class="requirement-not-met">• Al menos un símbolo (@$!%*?&)</span>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 btn-login" id="btnCambiar">Cambiar Contraseña</button>

                            <div class="login-links">
                                <a href="RecuperarVista.php?etapa=codigo" class="register-link">Volver atrás</a>
                            </div>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Funcionalidad para mostrar/ocultar contraseña
    function togglePassword(inputId) {
        const passwordInput = document.getElementById(inputId);
        const icon = passwordInput.parentNode.querySelector('i');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // Validación de código (solo números)
    document.addEventListener('DOMContentLoaded', function() {
        // Manejo de inputs de código
        const codeInputs = document.querySelectorAll('.code-input');
        if (codeInputs.length > 0) {
            codeInputs.forEach((input, index) => {
                input.addEventListener('input', function() {
                    // Solo permitir números
                    this.value = this.value.replace(/[^0-9]/g, '');

                    // Mover al siguiente input si se ingresa un número
                    if (this.value.length === 1 && index < codeInputs.length - 1) {
                        codeInputs[index + 1].focus();
                    }

                    // Actualizar el campo oculto con el código completo
                    updateCodigoCompleto();
                });

                input.addEventListener('keydown', function(e) {
                    // Manejar tecla retroceso
                    if (e.key === 'Backspace' && this.value === '' && index > 0) {
                        codeInputs[index - 1].focus();
                    }
                });
            });

            function updateCodigoCompleto() {
                let codigo = '';
                codeInputs.forEach(input => {
                    codigo += input.value;
                });
                document.getElementById('codigoCompleto').value = codigo;
            }
        }

        // Validación de contraseña en tiempo real
        const passwordInput = document.getElementById('new_password');
        const confirmPasswordInput = document.getElementById('confirmar_contrasena');

        if (passwordInput && confirmPasswordInput) {
            passwordInput.addEventListener('input', validatePassword);
            confirmPasswordInput.addEventListener('input', validatePasswordMatch);
        }

        // Contador para reenviar código
        const btnReenviar = document.getElementById('btnReenviar');
        if (btnReenviar) {
            let tiempoRestante = 60;
            const countdownElement = document.getElementById('countdown');

            const countdownInterval = setInterval(() => {
                tiempoRestante--;
                countdownElement.textContent = `(${tiempoRestante})`;

                if (tiempoRestante <= 0) {
                    clearInterval(countdownInterval);
                    btnReenviar.disabled = false;
                    countdownElement.textContent = '';
                }
            }, 1000);

            // Manejar el envío del formulario de reenvío
            const reenviarForm = document.getElementById('reenviarForm');
            if (reenviarForm) {
                reenviarForm.addEventListener('submit', function(e) {
                    // Mostrar loading
                    btnReenviar.disabled = true;
                    btnReenviar.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enviando...';

                    // Reiniciar contador después de enviar
                    setTimeout(() => {
                        btnReenviar.disabled = true;
                        tiempoRestante = 60;
                        countdownElement.textContent = `(${tiempoRestante})`;

                        const newInterval = setInterval(() => {
                            tiempoRestante--;
                            countdownElement.textContent = `(${tiempoRestante})`;

                            if (tiempoRestante <= 0) {
                                clearInterval(newInterval);
                                btnReenviar.disabled = false;
                                btnReenviar.innerHTML = 'Reenviar código';
                                countdownElement.textContent = '';
                            }
                        }, 1000);
                    }, 2000);
                });
            }
        }

        // Manejo de envío de formularios con loading
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            if (form.id !== 'reenviarForm') { // Excluir el formulario de reenvío
                form.addEventListener('submit', function(e) {
                    const loadingElement = this.querySelector('.loading');
                    const submitButton = this.querySelector('button[type="submit"]');

                    if (loadingElement) {
                        loadingElement.style.display = 'block';
                    }

                    if (submitButton) {
                        submitButton.disabled = true;
                        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...';
                    }
                });
            }
        });

        // Auto-ocultar alertas después de 5 segundos
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                if (alert.classList.contains('show')) {
                    alert.classList.remove('show');
                    setTimeout(() => alert.remove(), 500);
                }
            }, 5000);
        });
    });

    // Validación de fortaleza de contraseña
    function validatePassword() {
        const password = document.getElementById('new_password').value;
        const strengthElement = document.getElementById('passwordStrength');
        const requirements = {
            length: document.getElementById('reqLength'),
            upper: document.getElementById('reqUpper'),
            lower: document.getElementById('reqLower'),
            number: document.getElementById('reqNumber'),
            symbol: document.getElementById('reqSymbol')
        };

        // Verificar requisitos
        const hasMinLength = password.length >= 8;
        const hasUpperCase = /[A-Z]/.test(password);
        const hasLowerCase = /[a-z]/.test(password);
        const hasNumber = /[0-9]/.test(password);
        const hasSymbol = /[@$!%*?&]/.test(password);

        // Actualizar indicadores visuales
        requirements.length.className = hasMinLength ? 'requirement-met' : 'requirement-not-met';
        requirements.upper.className = hasUpperCase ? 'requirement-met' : 'requirement-not-met';
        requirements.lower.className = hasLowerCase ? 'requirement-met' : 'requirement-not-met';
        requirements.number.className = hasNumber ? 'requirement-met' : 'requirement-not-met';
        requirements.symbol.className = hasSymbol ? 'requirement-met' : 'requirement-not-met';

        // Calcular fortaleza
        let strength = 0;
        if (hasMinLength) strength++;
        if (hasUpperCase) strength++;
        if (hasLowerCase) strength++;
        if (hasNumber) strength++;
        if (hasSymbol) strength++;

        // Mostrar indicador de fortaleza
        if (password.length === 0) {
            strengthElement.textContent = '';
            strengthElement.className = 'password-strength';
        } else {
            let strengthText, strengthClass;
            if (strength <= 2) {
                strengthText = 'Contraseña débil';
                strengthClass = 'strength-weak';
            } else if (strength <= 4) {
                strengthText = 'Contraseña media';
                strengthClass = 'strength-medium';
            } else {
                strengthText = 'Contraseña fuerte';
                strengthClass = 'strength-strong';
            }

            strengthElement.textContent = strengthText;
            strengthElement.className = 'password-strength ' + strengthClass;
        }

        // Validar coincidencia si ya hay texto en el campo de confirmación
        validatePasswordMatch();
    }

    function validatePasswordMatch() {
        const password = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirmar_contrasena').value;
        const matchError = document.getElementById('passwordMatchError');

        if (confirmPassword && password !== confirmPassword) {
            matchError.textContent = 'Las contraseñas no coinciden';
        } else {
            matchError.textContent = '';
        }
    }
</script>
</body>
</html>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambio de Contraseña Obligatorio</title>
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

        .form-control {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 12px 15px;
            margin-bottom: 10px;
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
        }

        .toggle-password:hover {
            color: var(--azul-oscuro);
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

        .security-alert {
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            font-size: 14px;
        }

        .password-strength {
            height: 5px;
            margin-top: 5px;
            border-radius: 2px;
            transition: all 0.3s ease;
        }
        .strength-weak { background-color: #dc3545; width: 25%; }
        .strength-medium { background-color: #ffc107; width: 50%; }
        .strength-good { background-color: #28a745; width: 75%; }
        .strength-strong { background-color: #20c997; width: 100%; }

        .requirement {
            font-size: 0.85rem;
            margin-bottom: 3px;
        }
        .requirement.met { color: #28a745; }
        .requirement.unmet { color: #6c757d; }
        .requirement i { margin-right: 5px; }

        @media (max-width: 768px) {
            .login-left { display: none; }
            .login-right { padding: 30px 25px; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="login-container">
        <div class="row g-0">
            <div class="col-md-6 login-right">
                <div class="security-alert" id="securityAlert">
                    <h5><i class="fas fa-exclamation-triangle me-2"></i>Cambio de Contraseña Obligatorio</h5>
                    <p class="mb-0">Es tu primer inicio de sesión. Por seguridad, debes cambiar tu contraseña antes de continuar.</p>
                </div>

                <h3 class="text-center mb-4">Establecer Nueva Contraseña</h3>

                <!-- Aquí podrían ir alertas dinámicas -->
                <div id="alertContainer"></div>

                <form method="POST" action="../controlador/PersonaControlador.php" id="formCambioContrasena">
                    <input type="hidden" name="action" value="cambiarContraseña">
                    <input type="hidden" name="id_persona" value="<?php echo $_SESSION['id_persona'];?>">

                    <div class="mb-3">
                        <label for="new_password" class="form-label">Nueva Contraseña</label>
                        <div class="password-container">
                            <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Ingresa tu nueva contraseña" required onkeyup="checkPasswordStrength(this.value)">
                            <span class="toggle-password" onclick="togglePassword('new_password')"><i class="far fa-eye"></i></span>
                        </div>
                        <div class="password-strength" id="passwordStrength"></div>
                    </div>

                    <div class="mb-3">
                        <label for="confirmar_contrasena" class="form-label">Confirmar Nueva Contraseña</label>
                        <div class="password-container">
                            <input type="password" class="form-control" id="confirmar_contrasena" name="confirmar_contrasena" placeholder="Confirma tu nueva contraseña" required onkeyup="checkPasswordMatch()">
                            <span class="toggle-password" onclick="togglePassword('confirmar_contrasena')"><i class="far fa-eye"></i></span>
                        </div>
                        <div id="passwordMatch" class="form-text"></div>
                    </div>

                    <div class="password-requirements mb-3">
                        <strong>Requisitos de seguridad:</strong>
                        <div class="requirement" id="reqLength"><i class="far fa-circle"></i> Mínimo 8 caracteres</div>
                        <div class="requirement" id="reqUppercase"><i class="far fa-circle"></i> Al menos una letra mayúscula</div>
                        <div class="requirement" id="reqLowercase"><i class="far fa-circle"></i> Al menos una letra minúscula</div>
                        <div class="requirement" id="reqNumber"><i class="far fa-circle"></i> Al menos un número</div>
                        <div class="requirement" id="reqSymbol"><i class="far fa-circle"></i> Al menos un símbolo (@$!%*?&)</div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 btn-login" id="submitBtn">Cambiar Contraseña</button>
                </form>
            </div>

            <div class="col-md-6 login-left">
                <div class="logo-container">
                    <div class="logo"><i class="fas fa-building"></i></div>
                    <h2 class="system-title">Sistema Edificio Inteligente</h2>
                    <p class="system-subtitle">Gestión integral de espacios y servicios</p>
                </div>
                <div class="features">
                    <div class="feature-item">
                        <h5><i class="fas fa-shield-alt me-2"></i>Seguridad de Cuenta</h5>
                        <p class="mb-0">El cambio de contraseña en el primer inicio es una medida de seguridad para proteger tu cuenta.</p>
                    </div>
                    <div class="feature-item">
                        <h5><i class="fas fa-check-circle me-2"></i>Requisitos de Contraseña</h5>
                        • Mínimo 8 caracteres<br>
                        • Al menos una letra mayúscula<br>
                        • Al menos una letra minúscula<br>
                        • Al menos un número<br>
                        • Al menos un símbolo (@$!%*?&)
                    </div>
                    <div class="feature-item">
                        <h5><i class="fas fa-lightbulb me-2"></i>Consejos de Seguridad</h5>
                        • No reutilices contraseñas antiguas<br>
                        • No uses información personal<br>
                        • Considera usar un gestor de contraseñas<br>
                        • Cambia tu contraseña periódicamente
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Mostrar/ocultar contraseña
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

    // Fortaleza de contraseña y requisitos
    function checkPasswordStrength(password) {
        const strengthBar = document.getElementById('passwordStrength');
        const requirements = {
            length: password.length >= 8,
            uppercase: /[A-Z]/.test(password),
            lowercase: /[a-z]/.test(password),
            number: /[0-9]/.test(password),
            symbol: /[@$!%*?&]/.test(password)
        };

        updateRequirementStatus('reqLength', requirements.length);
        updateRequirementStatus('reqUppercase', requirements.uppercase);
        updateRequirementStatus('reqLowercase', requirements.lowercase);
        updateRequirementStatus('reqNumber', requirements.number);
        updateRequirementStatus('reqSymbol', requirements.symbol);

        let score = Object.values(requirements).filter(m => m).length;

        strengthBar.className = 'password-strength';
        if (password.length === 0) {
            strengthBar.style.width = '0%';
            strengthBar.style.backgroundColor = 'transparent';
        } else if (score <= 2) strengthBar.classList.add('strength-weak');
        else if (score <= 3) strengthBar.classList.add('strength-medium');
        else if (score <= 4) strengthBar.classList.add('strength-good');
        else strengthBar.classList.add('strength-strong');

        const allMet = Object.values(requirements).every(v => v);
        checkFormValidity(allMet);
    }

    function updateRequirementStatus(id, met) {
        const el = document.getElementById(id);
        const icon = el.querySelector('i');
        if (met) {
            el.classList.add('met'); el.classList.remove('unmet');
            icon.className = 'fas fa-check-circle';
        } else {
            el.classList.add('unmet'); el.classList.remove('met');
            icon.className = 'far fa-circle';
        }
    }

    function checkPasswordMatch() {
        const password = document.getElementById('new_password').value;
        const confirm = document.getElementById('confirmar_contrasena').value;
        const matchEl = document.getElementById('passwordMatch');

        if (confirm.length === 0) matchEl.textContent = '';
        else if (password === confirm) {
            matchEl.textContent = 'Las contraseñas coinciden';
            matchEl.className = 'form-text text-success';
        } else {
            matchEl.textContent = 'Las contraseñas no coinciden';
            matchEl.className = 'form-text text-danger';
        }

        const allRequirementsMet = document.querySelectorAll('.requirement.met').length === 5;
        const passwordsMatch = password === confirm && password.length > 0;
        checkFormValidity(allRequirementsMet && passwordsMatch);
    }

    function checkFormValidity(isValid) {
        document.getElementById('submitBtn').disabled = !isValid;
    }

    document.getElementById('formCambioContrasena').addEventListener('submit', function(e) {
        const password = document.getElementById('new_password').value;
        const confirm = document.getElementById('confirmar_contrasena').value;

        const requirements = {
            length: password.length >= 8,
            uppercase: /[A-Z]/.test(password),
            lowercase: /[a-z]/.test(password),
            number: /[0-9]/.test(password),
            symbol: /[@$!%*?&]/.test(password)
        };

        if (password !== confirm || !Object.values(requirements).every(v => v)) {
            e.preventDefault();
            alert('Verifica que la contraseña cumpla todos los requisitos y coincida.');
        }
    });

    // Ocultar alertas automáticamente después de 15 segundos
    window.addEventListener('DOMContentLoaded', () => {
        const alerts = document.querySelectorAll('.security-alert, .alert');
        setTimeout(() => {
            alerts.forEach(alert => {
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 500);
            });
        }, 15000);
    });

    document.addEventListener('DOMContentLoaded', function() {
        checkPasswordStrength('');
        checkPasswordMatch();
    });
</script>
</body>
</html>

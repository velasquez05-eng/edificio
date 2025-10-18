<?php

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambio de Contraseña Obligatorio</title>
    <link href="../../includes/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../includes/css/login.css">
    <style>
        .security-alert {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
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
        .requirement.met {
            color: #28a745;
        }
        .requirement.unmet {
            color: #6c757d;
        }
        .requirement i {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container"> 
            <div class="row g-0">
                <div class="col-md-6 login-right">
                    <div class="security-alert">
                        <h5><i class="fas fa-exclamation-triangle me-2"></i>Cambio de Contraseña Obligatorio</h5>
                        <p class="mb-0">Es tu primer inicio de sesión. Por seguridad, debes cambiar tu contraseña antes de continuar.</p>
                    </div>
                    
                    <h3 class="text-center mb-4">Establecer Nueva Contraseña</h3>
                    
                    <!-- Mostrar mensajes de error/success -->
                 

                    <form method="POST" action="../controlador/PersonaControlador.php" id="formCambioContrasena">
                        <input type="hidden" name="action" value="cambiarContraseña">
                        <input type="hidden" name="id_persona" value="<?php echo $_SESSION['id_persona'];?>">
                        
                        <div class="info-text">
                            Por favor, establece una nueva contraseña segura para tu cuenta.
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label">Nueva Contraseña</label>
                            <div class="password-container">
                                <input type="password" class="form-control" id="new_password" name="new_password" 
                                       placeholder="Ingresa tu nueva contraseña" required
                                       onkeyup="checkPasswordStrength(this.value)">
                                <span class="toggle-password" onclick="togglePassword('new_password')">
                                    <i class="far fa-eye"></i>
                                </span>
                            </div>
                            <div class="password-strength" id="passwordStrength"></div>
                        </div>

                        <div class="mb-3">
                            <label for="confirmar_contrasena" class="form-label">Confirmar Nueva Contraseña</label>
                            <div class="password-container">
                                <input type="password" class="form-control" id="confirmar_contrasena" name="confirmar_contrasena" 
                                       placeholder="Confirma tu nueva contraseña" required
                                       onkeyup="checkPasswordMatch()">
                                <span class="toggle-password" onclick="togglePassword('confirmar_contrasena')">
                                    <i class="far fa-eye"></i>
                                </span>
                            </div>
                            <div id="passwordMatch" class="form-text"></div>
                        </div>

                        <div class="password-requirements mb-3">
                            <strong>Requisitos de seguridad:</strong>
                            <div class="requirement" id="reqLength">
                                <i class="far fa-circle"></i> Mínimo 8 caracteres
                            </div>
                            <div class="requirement" id="reqUppercase">
                                <i class="far fa-circle"></i> Al menos una letra mayúscula
                            </div>
                            <div class="requirement" id="reqLowercase">
                                <i class="far fa-circle"></i> Al menos una letra minúscula
                            </div>
                            <div class="requirement" id="reqNumber">
                                <i class="far fa-circle"></i> Al menos un número
                            </div>
                            <div class="requirement" id="reqSymbol">
                                <i class="far fa-circle"></i> Al menos un símbolo (@$!%*?&)
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 btn-login" id="submitBtn">Cambiar Contraseña</button>
                    </form>
                </div>
                
                <div class="col-md-6 login-left">
                    <div class="logo-container">
                        <div class="logo">
                            <i class="fas fa-building"></i>
                        </div>
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
    
    <script src="../../includes/js/bootstrap.bundle.min.js"></script>
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

        // Verificar fortaleza de la contraseña
        function checkPasswordStrength(password) {
            const strengthBar = document.getElementById('passwordStrength');
            const requirements = {
                length: password.length >= 8,
                uppercase: /[A-Z]/.test(password),
                lowercase: /[a-z]/.test(password),
                number: /[0-9]/.test(password),
                symbol: /[@$!%*?&]/.test(password)
            };
            
            // Actualizar indicadores visuales de requisitos
            updateRequirementStatus('reqLength', requirements.length);
            updateRequirementStatus('reqUppercase', requirements.uppercase);
            updateRequirementStatus('reqLowercase', requirements.lowercase);
            updateRequirementStatus('reqNumber', requirements.number);
            updateRequirementStatus('reqSymbol', requirements.symbol);
            
            // Calcular fortaleza
            let score = 0;
            Object.values(requirements).forEach(met => {
                if (met) score++;
            });
            
            // Actualizar barra de fortaleza
            strengthBar.className = 'password-strength';
            if (password.length === 0) {
                strengthBar.style.width = '0%';
                strengthBar.style.backgroundColor = 'transparent';
            } else if (score <= 2) {
                strengthBar.classList.add('strength-weak');
            } else if (score <= 3) {
                strengthBar.classList.add('strength-medium');
            } else if (score <= 4) {
                strengthBar.classList.add('strength-good');
            } else {
                strengthBar.classList.add('strength-strong');
            }
            
            // Verificar si todos los requisitos se cumplen
            const allRequirementsMet = Object.values(requirements).every(met => met);
            checkFormValidity(allRequirementsMet);
        }
        
        // Actualizar estado visual de los requisitos
        function updateRequirementStatus(elementId, isMet) {
            const element = document.getElementById(elementId);
            const icon = element.querySelector('i');
            
            if (isMet) {
                element.classList.add('met');
                element.classList.remove('unmet');
                icon.classList.remove('fa-circle', 'fa-times-circle');
                icon.classList.add('fa-check-circle');
            } else {
                element.classList.add('unmet');
                element.classList.remove('met');
                icon.classList.remove('fa-circle', 'fa-check-circle');
                icon.classList.add('fa-times-circle');
            }
        }
        
        // Verificar si las contraseñas coinciden
        function checkPasswordMatch() {
            const password = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirmar_contrasena').value;
            const matchElement = document.getElementById('passwordMatch');
            
            if (confirmPassword.length === 0) {
                matchElement.textContent = '';
                matchElement.className = 'form-text';
            } else if (password === confirmPassword) {
                matchElement.textContent = 'Las contraseñas coinciden';
                matchElement.className = 'form-text text-success';
            } else {
                matchElement.textContent = 'Las contraseñas no coinciden';
                matchElement.className = 'form-text text-danger';
            }
            
            // Verificar validez del formulario
            const allRequirementsMet = document.querySelectorAll('.requirement.met').length === 5;
            const passwordsMatch = password === confirmPassword && password.length > 0;
            checkFormValidity(allRequirementsMet && passwordsMatch);
        }
        
        // Habilitar/deshabilitar botón de envío
        function checkFormValidity(isValid) {
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = !isValid;
        }
        
        // Validación del formulario antes del envío
        document.getElementById('formCambioContrasena').addEventListener('submit', function(e) {
            const password = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirmar_contrasena').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Las contraseñas no coinciden. Por favor, verifica.');
                return false;
            }
            
            // Verificar requisitos de seguridad
            const requirements = {
                length: password.length >= 8,
                uppercase: /[A-Z]/.test(password),
                lowercase: /[a-z]/.test(password),
                number: /[0-9]/.test(password),
                symbol: /[@$!%*?&]/.test(password)
            };
            
            const allRequirementsMet = Object.values(requirements).every(met => met);
            
            if (!allRequirementsMet) {
                e.preventDefault();
                alert('La contraseña no cumple con todos los requisitos de seguridad.');
                return false;
            }
            
            return true;
        });

        // Inicializar validación al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            checkPasswordStrength('');
            checkPasswordMatch();
        });
    </script>
</body>
</html>
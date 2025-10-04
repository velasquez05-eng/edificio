<?php
session_start();
if (isset($_SESSION['id_usuario'])) {
    if ($_SESSION['tipo_usuario'] === 'personal') {
        header("Location: ../vista/personalVista/Dashboard.php");
    } else {
        header("Location: ../vista/usuarioVista/Dashboard.php");
    }
    exit();
}

$etapa = $_GET['etapa'] ?? 'solicitud'; // solicitud, codigo, nueva_contrasena
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar</title>
    <link href="../../includes/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
       <link rel="stylesheet" href="../../includes/css/login.css">
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="row g-0">
                <div class="col-md-6 login-right">
                    <?php if ($etapa === 'solicitud'): ?>
                        <h3 class="text-center mb-4">Recuperar Contraseña</h3>
                    <?php elseif ($etapa === 'codigo'): ?>
                        <h3 class="text-center mb-4">Verificar Código</h3>
                    <?php elseif ($etapa === 'nueva_contrasena'): ?>
                        <h3 class="text-center mb-4">Nueva Contraseña</h3>
                    <?php endif; ?>
                    
                    <!-- Mostrar mensajes de error/success -->
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <?php 
                                echo $_SESSION['error']; 
                                unset($_SESSION['error']);
                            ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success">
                            <?php 
                                echo $_SESSION['success']; 
                                unset($_SESSION['success']);
                            ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($etapa === 'solicitud'): ?>
                        <form method="POST" action="../controlador/RecuperarControlador.php">
                            <input type="hidden" name="action" value="solicitar_codigo">
                            
                            <div class="info-text">
                                Ingresa tu email y te enviaremos un código de recuperación.
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Ingresa tu correo electrónico" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 btn-login">Enviar Código</button>
                            
                            <div class="login-links">
                                <a href="LoginVista.php" class="register-link">Volver al Login</a>
                            </div>
                        </form>

                    <?php elseif ($etapa === 'codigo'): ?>
                        <form method="POST" action="../controlador/RecuperarControlador.php">
                            <input type="hidden" name="action" value="verificar_codigo">
                            
                            <div class="info-text">
                                Hemos enviado un código de 6 dígitos a tu email.
                            </div>

                            <?php if (isset($_SESSION['codigo_debug'])): ?>
                                <div class="alert alert-info">
                                    <strong>DEBUG:</strong> Código: <?php echo $_SESSION['codigo_debug']; ?> 
                                    (solo para testing)
                                </div>
                            <?php endif; ?>

                            <div class="mb-3">
                                <label for="codigo" class="form-label">Código de Verificación</label>
                                <input type="text" class="form-control" id="codigo" name="codigo" 
                                       placeholder="Ingresa el código de 6 dígitos" 
                                       maxlength="6" pattern="[0-9]{6}" required>
                                <small class="form-text text-muted">Ingresa exactamente 6 dígitos</small>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 btn-login">Verificar Código</button>
                            
                            <div class="login-links">
                                <a href="RecuperarVista.php?etapa=solicitud" class="register-link">Volver atrás</a>
                            </div>
                        </form>

                    <?php elseif ($etapa === 'nueva_contrasena'): ?>
                        <form method="POST" action="../controlador/RecuperarControlador.php">
                            <input type="hidden" name="action" value="cambiar_contrasena">
                            
                            <div class="info-text">
                                Crea tu nueva contraseña.
                            </div>

                            <div class="mb-3">
                                <label for="nueva_contrasena" class="form-label">Nueva Contraseña</label>
                                <div class="password-container">
                                    <input type="password" class="form-control" id="nueva_contrasena" name="nueva_contrasena" 
                                           placeholder="Ingresa tu nueva contraseña" required>
                                    <span class="toggle-password" onclick="togglePassword('nueva_contrasena')">
                                        <i class="far fa-eye"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="confirmar_contrasena" class="form-label">Confirmar Contraseña</label>
                                <div class="password-container">
                                    <input type="password" class="form-control" id="confirmar_contrasena" name="confirmar_contrasena" 
                                           placeholder="Confirma tu nueva contraseña" required>
                                    <span class="toggle-password" onclick="togglePassword('confirmar_contrasena')">
                                        <i class="far fa-eye"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="password-requirements">
                                <strong>Requisitos de seguridad:</strong><br>
                                • Mínimo 8 caracteres<br>
                                • Al menos una letra mayúscula<br>
                                • Al menos una letra minúscula<br>
                                • Al menos un número<br>
                                • Al menos un símbolo (@$!%*?&)
                            </div>

                            <button type="submit" class="btn btn-primary w-100 btn-login">Cambiar Contraseña</button>
                            
                            <div class="login-links">
                                <a href="RecuperarVista.php?etapa=codigo" class="register-link">Volver atrás</a>
                            </div>
                        </form>
                    <?php endif; ?>
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
                            <h5><i class="fas fa-check-circle me-2"></i>Correo Electrónico Admitido</h5>
                            <p class="mb-0">Solo Gmail o Hotmail</p>
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
                            <h5><i class="fas fa-check-circle me-2"></i>Proceso de Recuperación</h5>
                            • Ingresa tu email<br>
                            • Verifica el código<br>
                            • Crea nueva contraseña<br>
                            • Código válido por 30 minutos
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

        // Validación de código (solo números)
        document.addEventListener('DOMContentLoaded', function() {
            const codigoInput = document.getElementById('codigo');
            if (codigoInput) {
                codigoInput.addEventListener('input', function() {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
            }
        });
    </script>
</body>
</html>
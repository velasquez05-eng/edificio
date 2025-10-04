<?php
session_start();
if (isset($_SESSION['id_usuario'])) {
        header("Location: ../vista/DashboardVista.php");

}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="../../includes/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        }
        
        .login-container {
            max-width: 1000px;
            margin: 0 auto;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 30px rgba(13, 61, 71, 0.2);
        }
        
        .login-left {
            background: linear-gradient(135deg, var(--azul-oscuro) 0%, var(--azul) 100%);
            color: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .login-right {
            background-color: white;
            padding: 40px;
        }
        
        .logo-container {
            text-align: center;
            margin-bottom: 30px;
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
        }
        
        .system-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .system-subtitle {
            font-size: 16px;
            opacity: 0.8;
        }
        
        .form-control {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 12px 15px;
            margin-bottom: 20px;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--azul);
            box-shadow: 0 0 0 0.2rem rgba(42, 117, 149, 0.25);
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
        }
        
        .btn-login {
            background-color: var(--verde);
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-login:hover {
            background-color: var(--azul);
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
            transition: color 0.3s;
        }
        
        .login-links a:hover {
            color: var(--azul-oscuro);
            text-decoration: underline;
        }
        
        .alert {
            border-radius: 8px;
            padding: 12px 15px;
        }
        
        @media (max-width: 768px) {
            .login-left {
                display: none;
            }
        }
        .toggle {
            color: var(--azul-oscuro);
        }
        
        .toggle::before {
            content: 'Usuario';
        }
        .toggle.on::before {
            content: 'Personal';
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="row g-0">
                <div class="col-md-6 login-left">
                    <div class="logo-container">
                        <div class="logo">
                            <i class="fas fa-building"></i>
                        </div>
                        <h2 class="system-title">Sistema Edificio Inteligente</h2>
                        <p class="system-subtitle">Gestión integral de espacios y servicios</p>
                    </div>
                    <div class="features">
                        <div class="feature-item mb-4">
                            <h5><i class="fas fa-check-circle me-2"></i>Acceso personalizado</h5>
                            <p class="mb-0">Diferentes perfiles para usuarios y personal</p>
                        </div>
                        <div class="feature-item mb-4">
                            <h5><i class="fas fa-check-circle me-2"></i>Gestión centralizada</h5>
                            <p class="mb-0">Controla todos los servicios desde una plataforma</p>
                        </div>
                        <div class="feature-item">
                            <h5><i class="fas fa-check-circle me-2"></i>Interfaz intuitiva</h5>
                            <p class="mb-0">Diseño moderno y fácil de usar</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 login-right">
                  
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="../controlador/LoginControlador.php">
                        <input type="hidden" name="action" value="login">
                          <h3 class="text-center mb-4 fw-bold">
                        <div class="toggle" onclick="toggleSwitch()"></div>
                        <input type="hidden" id="tipo_usuario" name="tipo_usuario" value="usuario">
                    </h3>

                        <div class="mb-3">
                            <label for="username" class="form-label">Usuario</label>
                            <input type="text" class="form-control" id="username" name="username" placeholder="Ingresa tu usuario" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <div class="password-container">
                                <input type="password" class="form-control" id="password" name="password" value="Admin11@" placeholder="Ingresa tu contraseña" required>
                                <span class="toggle-password" id="togglePassword">
                                    <i class="far fa-eye"></i>
                                </span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 btn-login">Iniciar Sesión</button>
                        
                        <div class="login-links">
                            <a href="RegistroVista.php" class="register-link">Registrarse</a>
                            <a href="RecuperarVista.php" class="forgot-password-link">¿Olvidaste tu contraseña?</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="../../includes/js/bootstrap.bundle.min.js"></script>
    <script>
        // Funcionalidad para mostrar/ocultar contraseña
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        function toggleSwitch() {
            const toggle = document.querySelector('.toggle');
            const input = document.getElementById('tipo_usuario');
            
            toggle.classList.toggle('on');
            input.value = toggle.classList.contains('on') ? 'personal' : 'usuario';
        }
    </script>
</body>
</html>
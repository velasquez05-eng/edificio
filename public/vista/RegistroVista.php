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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
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
            color: white;
        }
        
        .btn-login:hover {
            background-color: var(--azul);
            color: white;
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
        
        .feature-item {
            background-color: rgba(255, 255, 255, 0.1);
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 15px;
        }
        
        .feature-item h5 {
            color: var(--celeste);
        }
        
        .password-requirements {
            font-size: 12px;
            color: #666;
            margin-top: -15px;
            margin-bottom: 15px;
        }
        
        .date-group {
            display: flex;
            gap: 10px;
        }
        
        .date-group .form-control {
            flex: 1;
            margin-bottom: 0;
        }
        
        .date-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
            display: block;
        }
        
        @media (max-width: 768px) {
            .login-left {
                display: none;
            }
            
            .date-group {
                flex-direction: column;
                gap: 0;
            }
            
            .date-group .form-control {
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="row g-0">
                <div class="col-md-6 login-right">
                    <h3 class="text-center mb-4">Registrar Usuario</h3>
        
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

                    <form method="POST" action="../controlador/RegistroControlador.php">
                        <input type="hidden" name="action" value="registro">
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <input type="text" class="form-control" id="appaterno" name="appaterno" placeholder="Apellido Paterno" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <input type="text" class="form-control" id="apmaterno" name="apmaterno" placeholder="Apellido Materno" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <span class="date-label">Fecha de Nacimiento</span>
                                    <div class="date-group">
                                        <select class="form-control" id="dia" name="dia" required>
                                            <option value="">Día</option>
                                            <?php for ($i = 1; $i <= 31; $i++): ?>
                                                <option value="<?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>">
                                                    <?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>
                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                        
                                        <select class="form-control" id="mes" name="mes" required>
                                            <option value="">Mes</option>
                                            <option value="01">Enero</option>
                                            <option value="02">Febrero</option>
                                            <option value="03">Marzo</option>
                                            <option value="04">Abril</option>
                                            <option value="05">Mayo</option>
                                            <option value="06">Junio</option>
                                            <option value="07">Julio</option>
                                            <option value="08">Agosto</option>
                                            <option value="09">Septiembre</option>
                                            <option value="10">Octubre</option>
                                            <option value="11">Noviembre</option>
                                            <option value="12">Diciembre</option>
                                        </select>
                                        
                                        <select class="form-control" id="anio" name="anio" required>
                                            <option value="">Año</option>
                                            <?php 
                                                $anio_actual = date('Y');
                                                for ($i = $anio_actual; $i >= 1900; $i--): 
                                            ?>
                                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <input type="number" class="form-control" id="ci" name="ci" placeholder="CI" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <input type="tel" class="form-control" id="telefono" name="telefono" placeholder="Teléfono">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <input type="email" class="form-control" id="email" name="email" placeholder="Correo Electrónico" required>
                        </div>

                        <div class="mb-3">
                            <input type="text" class="form-control" id="usuario" name="usuario" placeholder="Usuario" required>
                        </div>

                        <div class="mb-3">
                            <div class="password-container">
                                <input type="password" class="form-control" id="password" name="password" placeholder="Ingresa tu contraseña" required>
                                <span class="toggle-password" id="togglePassword">
                                    <i class="far fa-eye"></i>
                                </span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="password-container">
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirma tu contraseña" required>
                                <span class="toggle-password" id="toggleConfirmPassword">
                                    <i class="far fa-eye"></i>
                                </span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 btn-login">Registrar</button>
                        
                        <div class="login-links">
                            <a href="LoginVista.php" class="register-link">Volver al Login</a>
                        </div>
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
                            <h5><i class="fas fa-check-circle me-2"></i>Requisitos de Correo</h5>
                            <p class="mb-0">Solo se admiten cuentas Gmail o Hotmail</p>
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
                            <h5><i class="fas fa-check-circle me-2"></i>Datos Requeridos</h5>
                            • Nombre completo<br>
                            • Fecha de nacimiento<br>
                            • CI único<br>
                            • Correo válido<br>
                            • Usuario único
                        </div>
                    </div>
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

        // Funcionalidad para mostrar/ocultar confirmación de contraseña
        document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
            const confirmPasswordInput = document.getElementById('confirm_password');
            const icon = this.querySelector('i');
            
            if (confirmPasswordInput.type === 'password') {
                confirmPasswordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                confirmPasswordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // Validación de fecha
        function validarFecha() {
            const dia = document.getElementById('dia').value;
            const mes = document.getElementById('mes').value;
            const anio = document.getElementById('anio').value;
            
            if (dia && mes && anio) {
                // Validar días según el mes
                const fecha = new Date(anio, mes - 1, dia);
                if (fecha.getDate() != dia || fecha.getMonth() != mes - 1 || fecha.getFullYear() != anio) {
                    return false;
                }
                
                // Validar que no sea fecha futura
                const hoy = new Date();
                if (fecha > hoy) {
                    return false;
                }
                
                // Validar edad mínima (18 años)
                const edadMinima = new Date();
                edadMinima.setFullYear(edadMinima.getFullYear() - 18);
                if (fecha > edadMinima) {
                    return false;
                }
            }
            return true;
        }

        // Validación del formulario
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            // Validar contraseñas coincidentes
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Las contraseñas no coinciden. Por favor, verifica.');
                return false;
            }
            
            // Validación básica de formato de correo
            const email = document.getElementById('email').value;
            if (!email.endsWith('@gmail.com') && !email.endsWith('@hotmail.com')) {
                e.preventDefault();
                alert('Solo se admiten correos Gmail o Hotmail.');
                return false;
            }
            
            // Validar fecha
            if (!validarFecha()) {
                e.preventDefault();
                alert('La fecha de nacimiento no es válida. Debe ser una fecha real y el usuario debe ser mayor de 18 años.');
                return false;
            }
        });

        // Validación de días según el mes seleccionado
        document.getElementById('mes').addEventListener('change', function() {
            const mes = this.value;
            const diaSelect = document.getElementById('dia');
            const anio = document.getElementById('anio').value;
            
            if (mes && anio) {
                const diasEnMes = new Date(anio, mes, 0).getDate();
                const diaActual = diaSelect.value;
                
                // Actualizar opciones de días
                while (diaSelect.options.length > 1) {
                    diaSelect.remove(1);
                }
                
                for (let i = 1; i <= diasEnMes; i++) {
                    const option = document.createElement('option');
                    option.value = i.toString().padStart(2, '0');
                    option.textContent = i.toString().padStart(2, '0');
                    diaSelect.appendChild(option);
                }
                
                // Restaurar selección anterior si es válida
                if (diaActual && parseInt(diaActual) <= diasEnMes) {
                    diaSelect.value = diaActual.padStart(2, '0');
                }
            }
        });

        // Validación de días cuando cambia el año
        document.getElementById('anio').addEventListener('change', function() {
            const mesSelect = document.getElementById('mes');
            if (mesSelect.value) {
                mesSelect.dispatchEvent(new Event('change'));
            }
        });
    </script>
</body>
</html>
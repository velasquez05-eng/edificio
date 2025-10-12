<?php include("../../includes/header.php"); ?>

    <!-- Page Header -->

    <div class="page-header fade-in">
        <div class="page-title">
            <h1>Registrar Nueva Persona</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item active">Persona</li>
                    <li class="breadcrumb-item active" aria-current="page">Registrar Persona</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Alertas -->
<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success correcto alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <?php echo htmlspecialchars($_GET['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger error alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <?php echo htmlspecialchars($_GET['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
    <!-- Formulario de Registro -->
    <div class="row fade-in">
        <div class="col-lg-8">
            <div class="content-box">
                <div class="content-box-header">
                    <h5>Información Personal</h5>
                </div>
                <div class="content-box-body">
                    <form id="formRegistrarPersona" action="../controlador/PersonaControlador.php" method="POST">
                        <input type="hidden" name="action" value="registrar">

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="nombre" class="form-label">
                                        <i class="fas fa-user text-verde me-2"></i>Nombre *
                                    </label>
                                    <input type="text"
                                           class="form-control"
                                           id="nombre"
                                           name="nombre"
                                           required
                                           maxlength="50"
                                           placeholder="Ingrese el nombre">
                                    <div class="form-text">Solo letras y espacios</div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="apellido_paterno" class="form-label">
                                        <i class="fas fa-user text-azul me-2"></i>Apellido Paterno *
                                    </label>
                                    <input type="text"
                                           class="form-control"
                                           id="apellido_paterno"
                                           name="apellido_paterno"
                                           required
                                           maxlength="50"
                                           placeholder="Ingrese apellido paterno">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="apellido_materno" class="form-label">
                                        <i class="fas fa-user text-morado me-2"></i>Apellido Materno
                                    </label>
                                    <input type="text"
                                           class="form-control"
                                           id="apellido_materno"
                                           name="apellido_materno"
                                           maxlength="50"
                                           placeholder="Ingrese apellido materno">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="ci" class="form-label">
                                        <i class="fas fa-id-card text-warning me-2"></i>Cédula de Identidad *
                                    </label>
                                    <input type="text"
                                           class="form-control"
                                           id="ci"
                                           name="ci"
                                           required
                                           maxlength="15"
                                           placeholder="Ej: 12345678"
                                           pattern="[0-9]+"
                                           title="Solo se permiten números">
                                    <div class="form-text">Solo números, sin puntos ni guiones</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="telefono" class="form-label">
                                        <i class="fas fa-phone text-success me-2"></i>Teléfono *
                                    </label>
                                    <input type="tel"
                                           class="form-control"
                                           id="telefono"
                                           name="telefono"
                                           required
                                           maxlength="15"
                                           placeholder="Ej: 76543210"
                                           pattern="[0-9]+"
                                           title="Solo se permiten números">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope text-danger me-2"></i>Email *
                                    </label>
                                    <input type="email"
                                           class="form-control"
                                           id="email"
                                           name="email"
                                           required
                                           maxlength="100"
                                           placeholder="usuario@gmail.com o usuario@hotmail.com">
                                    <div class="form-text">Solo se permiten correos de Gmail o Hotmail</div>
                                    <div class="valid-feedback" id="email-valid-feedback" style="display: none;">
                                        <i class="fas fa-check-circle me-1"></i>Correo válido
                                    </div>
                                    <div class="invalid-feedback" id="email-invalid-feedback" style="display: none;">
                                        <i class="fas fa-times-circle me-1"></i>Solo se permiten correos Gmail (@gmail.com) o Hotmail (@hotmail.com)
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="username" class="form-label">
                                        <i class="fas fa-user-circle text-primary me-2"></i>Username *
                                    </label>
                                    <div class="input-group">
                                        <input type="text"
                                               class="form-control"
                                               id="username"
                                               name="username"
                                               required
                                               maxlength="50"
                                               placeholder="Se generará automáticamente"
                                               pattern="[a-zA-Z0-9_]+"
                                               title="Solo letras, números y guiones bajos">
                                        <button class="btn btn-outline-secondary" type="button" id="btn-generar-username" title="Generar username automáticamente">
                                            <i class="fas fa-magic"></i>
                                        </button>
                                    </div>
                                    <div class="form-text">
                                        <small>Formato: primera letra del nombre + apellido paterno + primera letra del apellido materno (todo en minúsculas)</small>
                                    </div>
                                    <div class="valid-feedback" id="username-valid-feedback" style="display: none;">
                                        <i class="fas fa-check-circle me-1"></i>Username válido
                                    </div>
                                    <div class="invalid-feedback" id="username-invalid-feedback" style="display: none;">
                                        <i class="fas fa-times-circle me-1"></i>Solo letras, números y _ (sin espacios)
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="password" class="form-label">
                                        <i class="fas fa-lock text-warning me-2"></i>Contraseña *
                                    </label>
                                    <div class="input-group">
                                        <input type="password"
                                               class="form-control"
                                               id="password"
                                               name="password"
                                               required
                                               minlength="8"
                                               maxlength="100"
                                               placeholder="Ingrese contraseña segura">
                                        <button class="btn btn-outline-secondary toggle-password"
                                                type="button"
                                                data-target="password"
                                                title="Mostrar/ocultar contraseña">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-info"
                                                type="button"
                                                id="btn-generar-password"
                                                title="Generar contraseña segura">
                                            <i class="fas fa-dice"></i>
                                        </button>
                                    </div>
                                    <div class="form-text mt-2">
                                        <small>Requisitos mínimos:</small>
                                        <ul class="mb-0 mt-1 ps-3">
                                            <li id="req-length" class="text-danger"><small>8 caracteres mínimo</small></li>
                                            <li id="req-uppercase" class="text-danger"><small>1 letra mayúscula</small></li>
                                            <li id="req-lowercase" class="text-danger"><small>1 letra minúscula</small></li>
                                            <li id="req-number" class="text-danger"><small>1 número</small></li>
                                            <li id="req-special" class="text-danger"><small>1 carácter especial</small></li>
                                        </ul>
                                    </div>
                                    <div class="valid-feedback" id="password-valid-feedback" style="display: none;">
                                        <i class="fas fa-check-circle me-1"></i>Contraseña segura
                                    </div>
                                    <div class="invalid-feedback" id="password-invalid-feedback" style="display: none;">
                                        <i class="fas fa-times-circle me-1"></i>La contraseña no cumple los requisitos
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="confirm_password" class="form-label">
                                        <i class="fas fa-lock text-success me-2"></i>Confirmar Contraseña *
                                    </label>
                                    <div class="input-group">
                                        <input type="password"
                                               class="form-control"
                                               id="confirm_password"
                                               name="confirm_password"
                                               required
                                               placeholder="Repita la contraseña">
                                        <button class="btn btn-outline-secondary toggle-password"
                                                type="button"
                                                data-target="confirm_password"
                                                title="Mostrar/ocultar contraseña">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="valid-feedback" id="confirm-valid-feedback" style="display: none;">
                                        <i class="fas fa-check-circle me-1"></i>Contraseñas coinciden
                                    </div>
                                    <div class="invalid-feedback" id="confirm-invalid-feedback" style="display: none;">
                                        <i class="fas fa-times-circle me-1"></i>Las contraseñas no coinciden
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="id_rol" class="form-label">
                                        <i class="fas fa-user-tag text-info me-2"></i>Rol *
                                    </label>
                                    <select class="form-control" id="id_rol" name="id_rol" required>
                                        <option value="">Seleccione un rol</option>
                                        <?php
                                        // Incluir y cargar los roles disponibles
                                        require_once '../../config/database.php';
                                        require_once '../modelo/RolModelo.php';
                                        try {
                                            $database = new Database();
                                            $db = $database->getConnection();
                                            $rolModelo = new RolModelo($db);
                                            $roles = $rolModelo->listarRoles();
                                            foreach ($roles as $rol) {
                                                echo '<option value="' . htmlspecialchars($rol['id_rol']) . '">'. htmlspecialchars($rol['rol']). '</option>';
                                            }
                                        } catch (Exception $e) {
                                            echo '<option value="">Error al cargar roles</option>';
                                        }
                                        ?>
                                    </select>
                                    <div class="form-text">Seleccione el rol asignado a la persona</div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <button type="submit" class="btn btn-primary" style="background: var(--verde); border: none;">
                                <i class="fas fa-save me-2"></i>Registrar Persona
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Información Adicional -->
        <div class="col-lg-4">
            <div class="content-box position-sticky" style="top: 100px;">
                <div class="content-box-header">
                    <h5>Información Importante</h5>
                </div>
                <div class="content-box-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Instrucciones:</h6>
                        <ul class="mb-0 mt-2">
                            <li>Todos los campos marcados con (*) son obligatorios</li>
                            <li>La cédula de identidad debe ser única</li>
                            <li>Verifique que la persona no esté registrada previamente</li>
                            <li><strong>Solo se aceptan correos Gmail o Hotmail</strong></li>
                            <li>Los datos sensibles se almacenan cifrados</li>
                            <li><strong>La contraseña debe cumplir todos los requisitos de seguridad</strong></li>
                            <li><strong>El username se genera automáticamente</strong></li>
                            <li><strong>Use el botón de dados para generar contraseñas seguras</strong></li>
                        </ul>
                    </div>

                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Notas:</h6>
                        <ul class="mb-0 mt-2">
                            <li>No se permiten cédulas duplicadas</li>
                            <li>Una vez registrado, la persona podrá acceder al sistema</li>
                            <li>Verifique la información antes de guardar</li>
                            <li><strong>Correos permitidos: @gmail.com y @hotmail.com</strong></li>
                            <li><strong>Username único: formato automático o personalizado</strong></li>
                            <li><strong>Guarde la contraseña generada en un lugar seguro</strong></li>
                        </ul>
                    </div>

                    <div class="alert alert-success">
                        <h6><i class="fas fa-shield-alt me-2"></i>Seguridad:</h6>
                        <ul class="mb-0 mt-2">
                            <li>Datos personales cifrados</li>
                            <li>Protección de información sensible</li>
                            <li>Acceso restringido por roles</li>
                            <li>Validación estricta de correos electrónicos</li>
                            <li><strong>Contraseñas seguras con hash</strong></li>
                            <li><strong>Generador de contraseñas seguras incluido</strong></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script para validaciones -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const formRegistrar = document.getElementById('formRegistrarPersona');
            const btnRegistrar = formRegistrar.querySelector('button[type="submit"]');
            const emailInput = document.getElementById('email');
            const emailValidFeedback = document.getElementById('email-valid-feedback');
            const emailInvalidFeedback = document.getElementById('email-invalid-feedback');
            const usernameInput = document.getElementById('username');
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('confirm_password');
            const nombreInput = document.getElementById('nombre');
            const apellidoPaternoInput = document.getElementById('apellido_paterno');
            const apellidoMaternoInput = document.getElementById('apellido_materno');
            const btnGenerarUsername = document.getElementById('btn-generar-username');
            const btnGenerarPassword = document.getElementById('btn-generar-password');

            // Función para generar contraseña segura
            function generarPasswordSegura(longitud = 12) {
                const caracteres = {
                    minusculas: 'abcdefghijklmnopqrstuvwxyz',
                    mayusculas: 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
                    numeros: '0123456789',
                    especiales: '!@#$%^&*()_+-=[]{}|;:,.<>?'
                };

                // Asegurar al menos un carácter de cada tipo
                let password = '';
                password += caracteres.minusculas.charAt(Math.floor(Math.random() * caracteres.minusculas.length));
                password += caracteres.mayusculas.charAt(Math.floor(Math.random() * caracteres.mayusculas.length));
                password += caracteres.numeros.charAt(Math.floor(Math.random() * caracteres.numeros.length));
                password += caracteres.especiales.charAt(Math.floor(Math.random() * caracteres.especiales.length));

                // Completar el resto de la longitud con caracteres aleatorios de todos los tipos
                const todosCaracteres = caracteres.minusculas + caracteres.mayusculas + caracteres.numeros + caracteres.especiales;
                for (let i = password.length; i < longitud; i++) {
                    password += todosCaracteres.charAt(Math.floor(Math.random() * todosCaracteres.length));
                }

                // Mezclar la contraseña para que no siempre empiece con el mismo patrón
                return password.split('').sort(() => Math.random() - 0.5).join('');
            }

            // Función para generar username automáticamente
            function generarUsername() {
                const nombre = nombreInput.value.trim();
                const apellidoPaterno = apellidoPaternoInput.value.trim();
                const apellidoMaterno = apellidoMaternoInput.value.trim();

                if (!nombre || !apellidoPaterno) {
                    return '';
                }

                // Obtener primera letra del nombre
                const primeraLetraNombre = nombre.charAt(0).toLowerCase();

                // Convertir apellido paterno a minúsculas y eliminar espacios
                const apellidoPaternoClean = apellidoPaterno.toLowerCase().replace(/\s+/g, '');

                // Obtener primera letra del apellido materno (si existe)
                const primeraLetraMaterno = apellidoMaterno ? apellidoMaterno.charAt(0).toLowerCase() : '';

                // Generar username: primeraLetraNombre + apellidoPaterno + primeraLetraMaterno
                let username = primeraLetraNombre + apellidoPaternoClean + primeraLetraMaterno;

                // Limpiar caracteres especiales y mantener solo letras y números
                username = username.replace(/[^a-z0-9_]/g, '');

                return username;
            }

            // Función para actualizar el campo username
            function actualizarUsername() {
                const username = generarUsername();
                if (username) {
                    usernameInput.value = username;
                    // Disparar evento input para activar validaciones
                    usernameInput.dispatchEvent(new Event('input'));
                }
            }

            // Función para generar y establecer contraseña
            function generarYEstablecerPassword() {
                const password = generarPasswordSegura(12);
                passwordInput.value = password;
                confirmPasswordInput.value = password;

                // Disparar eventos para activar validaciones
                passwordInput.dispatchEvent(new Event('input'));
                confirmPasswordInput.dispatchEvent(new Event('input'));

                // Mostrar contraseña temporalmente para que el usuario la vea
                passwordInput.type = 'text';
                confirmPasswordInput.type = 'text';

                // Efecto visual de éxito
                const icon = btnGenerarPassword.querySelector('i');
                icon.classList.remove('fa-dice');
                icon.classList.add('fa-check');
                btnGenerarPassword.classList.remove('btn-outline-info');
                btnGenerarPassword.classList.add('btn-success');

                // Restaurar después de 3 segundos
                setTimeout(() => {
                    passwordInput.type = 'password';
                    confirmPasswordInput.type = 'password';
                    icon.classList.remove('fa-check');
                    icon.classList.add('fa-dice');
                    btnGenerarPassword.classList.remove('btn-success');
                    btnGenerarPassword.classList.add('btn-outline-info');
                }, 3000);
            }

            // Evento para el botón de generar username
            btnGenerarUsername.addEventListener('click', function() {
                actualizarUsername();

                // Efecto visual de éxito
                const icon = this.querySelector('i');
                icon.classList.remove('fa-magic');
                icon.classList.add('fa-check');
                this.classList.remove('btn-outline-secondary');
                this.classList.add('btn-success');

                setTimeout(() => {
                    icon.classList.remove('fa-check');
                    icon.classList.add('fa-magic');
                    this.classList.remove('btn-success');
                    this.classList.add('btn-outline-secondary');
                }, 2000);
            });

            // Evento para el botón de generar password
            btnGenerarPassword.addEventListener('click', function() {
                generarYEstablecerPassword();
            });

            // Auto-generar username cuando se llenen los campos necesarios
            nombreInput.addEventListener('blur', function() {
                if (apellidoPaternoInput.value.trim() && !usernameInput.value) {
                    actualizarUsername();
                }
            });

            apellidoPaternoInput.addEventListener('blur', function() {
                if (nombreInput.value.trim() && !usernameInput.value) {
                    actualizarUsername();
                }
            });

            apellidoMaternoInput.addEventListener('blur', function() {
                if (nombreInput.value.trim() && apellidoPaternoInput.value.trim() && usernameInput.value) {
                    // Si ya hay un username y se modifica el apellido materno, actualizar
                    actualizarUsername();
                }
            });

            // Función para toggle de visibilidad de contraseña
            function setupPasswordToggle() {
                const toggleButtons = document.querySelectorAll('.toggle-password');
                toggleButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const targetId = this.getAttribute('data-target');
                        const passwordInput = document.getElementById(targetId);
                        const icon = this.querySelector('i');

                        if (passwordInput.type === 'password') {
                            passwordInput.type = 'text';
                            icon.classList.remove('fa-eye');
                            icon.classList.add('fa-eye-slash');
                            this.setAttribute('title', 'Ocultar contraseña');
                        } else {
                            passwordInput.type = 'password';
                            icon.classList.remove('fa-eye-slash');
                            icon.classList.add('fa-eye');
                            this.setAttribute('title', 'Mostrar contraseña');
                        }
                    });
                });
            }

            // Función para validar correo Gmail o Hotmail
            function validarEmailDominio(email) {
                if (!email) return false;

                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    return false;
                }

                // Validar dominios permitidos
                const dominio = email.toLowerCase().split('@')[1];
                return dominio === 'gmail.com' || dominio === 'hotmail.com';
            }

            // Función para validar fortaleza de contraseña
            function validarFortalezaPassword(password) {
                const requisitos = {
                    length: password.length >= 8,
                    uppercase: /[A-Z]/.test(password),
                    lowercase: /[a-z]/.test(password),
                    number: /[0-9]/.test(password),
                    special: /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)
                };

                // Actualizar indicadores visuales
                Object.keys(requisitos).forEach(key => {
                    const element = document.getElementById(`req-${key}`);
                    if (element) {
                        element.className = requisitos[key] ? 'text-success' : 'text-danger';
                    }
                });

                return Object.values(requisitos).every(Boolean);
            }

            // Función para validar username
            function validarUsername(username) {
                return /^[a-zA-Z0-9_]+$/.test(username);
            }

            // Validación del formulario
            formRegistrar.addEventListener('submit', function(e) {
                const nombre = document.getElementById('nombre').value.trim();
                const apellidoPaterno = document.getElementById('apellido_paterno').value.trim();
                const ci = document.getElementById('ci').value.trim();
                const telefono = document.getElementById('telefono').value.trim();
                const email = document.getElementById('email').value.trim();
                const username = document.getElementById('username').value.trim();
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('confirm_password').value;
                const idRol = document.getElementById('id_rol').value;

                // Validar nombre (solo letras y espacios)
                if (!nombre || !/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(nombre)) {
                    e.preventDefault();
                    alert('Por favor, ingrese un nombre válido (solo letras y espacios)');
                    document.getElementById('nombre').focus();
                    return;
                }

                if (!apellidoPaterno || !/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(apellidoPaterno)) {
                    e.preventDefault();
                    alert('Por favor, ingrese un apellido paterno válido (solo letras y espacios)');
                    document.getElementById('apellido_paterno').focus();
                    return;
                }

                // Validar CI (solo números)
                if (!ci || !/^\d+$/.test(ci)) {
                    e.preventDefault();
                    alert('Por favor, ingrese una cédula válida (solo números)');
                    document.getElementById('ci').focus();
                    return;
                }

                // Validar teléfono (solo números)
                if (!telefono || !/^\d+$/.test(telefono)) {
                    e.preventDefault();
                    alert('Por favor, ingrese un teléfono válido (solo números)');
                    document.getElementById('telefono').focus();
                    return;
                }

                // Validar email (solo Gmail o Hotmail)
                if (!email || !validarEmailDominio(email)) {
                    e.preventDefault();
                    alert('Por favor, ingrese un email válido de Gmail (@gmail.com) o Hotmail (@hotmail.com)');
                    document.getElementById('email').focus();
                    return;
                }

                // Validar username
                if (!username || !validarUsername(username)) {
                    e.preventDefault();
                    alert('Por favor, ingrese un username válido (solo letras, números y guiones bajos)');
                    document.getElementById('username').focus();
                    return;
                }

                // Validar contraseña
                if (!password || !validarFortalezaPassword(password)) {
                    e.preventDefault();
                    alert('Por favor, ingrese una contraseña que cumpla todos los requisitos de seguridad');
                    document.getElementById('password').focus();
                    return;
                }

                // Validar confirmación de contraseña
                if (password !== confirmPassword) {
                    e.preventDefault();
                    alert('Las contraseñas no coinciden');
                    document.getElementById('confirm_password').focus();
                    return;
                }

                if (!idRol) {
                    e.preventDefault();
                    alert('Por favor, seleccione un rol');
                    document.getElementById('id_rol').focus();
                    return;
                }

                // Mostrar loading en el botón
                btnRegistrar.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Registrando...';
                btnRegistrar.disabled = true;
            });

            // Validación en tiempo real para email con feedback visual
            emailInput.addEventListener('input', function() {
                const email = this.value.trim();

                if (email === '') {
                    this.classList.remove('is-valid', 'is-invalid');
                    emailValidFeedback.style.display = 'none';
                    emailInvalidFeedback.style.display = 'none';
                    return;
                }

                if (validarEmailDominio(email)) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                    emailInvalidFeedback.style.display = 'none';
                    emailValidFeedback.style.display = 'block';
                } else {
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                    emailValidFeedback.style.display = 'none';
                    emailInvalidFeedback.style.display = 'block';
                }
            });

            // Validación en tiempo real para username
            usernameInput.addEventListener('input', function() {
                const username = this.value.trim();

                if (username === '') {
                    this.classList.remove('is-valid', 'is-invalid');
                    return;
                }

                if (validarUsername(username)) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else {
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                }
            });

            // Validación en tiempo real para contraseña
            passwordInput.addEventListener('input', function() {
                const password = this.value;

                if (password === '') {
                    this.classList.remove('is-valid', 'is-invalid');
                    // Resetear indicadores
                    ['length', 'uppercase', 'lowercase', 'number', 'special'].forEach(key => {
                        const element = document.getElementById(`req-${key}`);
                        if (element) element.className = 'text-danger';
                    });
                    return;
                }

                if (validarFortalezaPassword(password)) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else {
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                }

                // Validar confirmación de contraseña en tiempo real
                const confirmPassword = confirmPasswordInput.value;
                if (confirmPassword) {
                    validarConfirmacionPassword(password, confirmPassword);
                }
            });

            // Función para validar confirmación de contraseña
            function validarConfirmacionPassword(password, confirmPassword) {
                if (confirmPassword === '') {
                    confirmPasswordInput.classList.remove('is-valid', 'is-invalid');
                    return;
                }

                if (password === confirmPassword) {
                    confirmPasswordInput.classList.remove('is-invalid');
                    confirmPasswordInput.classList.add('is-valid');
                } else {
                    confirmPasswordInput.classList.remove('is-valid');
                    confirmPasswordInput.classList.add('is-invalid');
                }
            }

            // Validación en tiempo real para confirmación de contraseña
            confirmPasswordInput.addEventListener('input', function() {
                const password = passwordInput.value;
                const confirmPassword = this.value;
                validarConfirmacionPassword(password, confirmPassword);
            });

            // Validación en tiempo real para nombres (solo letras)
            document.getElementById('nombre').addEventListener('input', function() {
                this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
                if (this.value.length < 2) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });

            document.getElementById('apellido_paterno').addEventListener('input', function() {
                this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
                if (this.value.length < 2) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });

            document.getElementById('apellido_materno').addEventListener('input', function() {
                this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
            });

            // Validación en tiempo real para números (CI y teléfono)
            document.getElementById('ci').addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '');
                if (this.value.length < 5) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });

            document.getElementById('telefono').addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '');
                if (this.value.length < 7) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });

            // Inicializar toggle de contraseñas
            setupPasswordToggle();

            // Auto-ocultar alertas después de 5 segundos
            setTimeout(function() {
                const alerts = document.querySelectorAll('.correcto,.error');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 6000);
        });
    </script>

    <!-- Estilos adicionales -->
    <style>
        .is-invalid {
            border-color: #dc3545 !important;
        }

        .is-valid {
            border-color: #198754 !important;
        }

        .form-text {
            font-size: 0.875rem;
            color: #6c757d;
        }

        .form-text ul {
            margin-bottom: 0;
        }

        .form-text li {
            font-size: 0.8rem;
        }

        .content-box {
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .content-box-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 1px solid #dee2e6;
            padding: 15px 20px;
            border-radius: 10px 10px 0 0;
        }

        .text-verde { color: var(--verde); }
        .text-azul { color: var(--azul); }
        .text-morado { color: #6f42c1; }
        .text-warning { color: #ffc107; }
        .text-success { color: #198754; }
        .text-danger { color: #dc3545; }
        .text-info { color: #0dcaf0; }
        .text-primary { color: #0d6efd; }

        .form-control:focus {
            border-color: var(--verde);
            box-shadow: 0 0 0 0.2rem rgba(54, 137, 121, 0.25);
        }

        .alert ul {
            padding-left: 20px;
        }

        .alert li {
            margin-bottom: 5px;
        }

        .valid-feedback,
        .invalid-feedback {
            display: block;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .valid-feedback i,
        .invalid-feedback i {
            font-size: 0.8rem;
        }

        #password-strength {
            margin-top: 5px;
        }

        .strength-weak { color: #dc3545; }
        .strength-medium { color: #fd7e14; }
        .strength-strong { color: #198754; }

        /* Estilos para los botones de contraseña */
        .toggle-password,
        #btn-generar-password {
            border: 1px solid #ced4da;
            border-left: none;
            background-color: white;
            transition: all 0.3s ease;
        }

        .toggle-password:hover,
        #btn-generar-password:hover {
            background-color: #f8f9fa;
            border-color: #86b7fe;
        }

        .toggle-password:focus,
        #btn-generar-password:focus {
            box-shadow: none;
            border-color: #86b7fe;
        }

        .input-group:focus-within .toggle-password,
        .input-group:focus-within #btn-generar-password {
            border-color: var(--verde);
        }

        .input-group:focus-within .form-control {
            border-right: none;
        }

        .input-group:focus-within .form-control:focus {
            border-right: 1px solid var(--verde);
        }

        /* Estilos para el botón de generar username */
        #btn-generar-username {
            border: 1px solid #ced4da;
            border-left: none;
            transition: all 0.3s ease;
        }

        #btn-generar-username:hover {
            background-color: #f8f9fa;
            border-color: #86b7fe;
        }

        #btn-generar-username:focus {
            box-shadow: none;
            border-color: #86b7fe;
        }

        /* Ajustes para el grupo de inputs con múltiples botones */
        .input-group > .form-control:not(:first-child) {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }
    </style>

<?php include("../../includes/footer.php"); ?>
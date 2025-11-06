<?php include("../../includes/header.php"); ?>

    <!-- Page Header -->
    <div class="page-header fade-in">
        <div class="page-title">
            <h1>Mi Perfil</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Mi Perfil</li>
                </ol>
            </nav>
        </div>
        <div class="page-actions">
            <a href="../controlador/DashboardControlador.php?action=mostrarDashboard<?php echo ($_SESSION['id_rol'] == '1') ? 'Administrador' : (($_SESSION['id_rol'] == '2') ? 'Residente' : 'Personal'); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
            </a>
        </div>
    </div>

    <!-- Alertas -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <?php echo htmlspecialchars($_GET['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?php echo htmlspecialchars($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Información del Perfil -->
    <div class="row fade-in mb-4">
        <div class="col-md-4">
            <div class="content-box">
                <div class="content-box-header text-center">
                    <h5>Mi Información</h5>
                </div>
                <div class="content-box-body text-center">
                    <div class="avatar-circle mb-3 mx-auto">
                        <span class="avatar-text"><?php echo $_SESSION['avatar'] ?? 'US'; ?></span>
                    </div>
                    <h5><?php echo htmlspecialchars($persona['nombre'] . ' ' . $persona['apellido_paterno'] . ' ' . ($persona['apellido_materno'] ?? '')); ?></h5>
                    <span class="badge bg-primary mb-3"><?php echo htmlspecialchars($persona['rol'] ?? 'Usuario'); ?></span>
                    <div class="text-muted small">
                        <p class="mb-1"><i class="fas fa-user me-2"></i><?php echo htmlspecialchars($persona['username'] ?? ''); ?></p>
                        <p class="mb-0"><i class="fas fa-id-card me-2"></i><?php echo htmlspecialchars($persona['ci'] ?? ''); ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="content-box">
                <div class="content-box-header">
                    <h5>Editar Perfil</h5>
                </div>
                <div class="content-box-body">
                    <form id="formActualizarPerfil" action="../controlador/PersonaControlador.php" method="POST">
                        <input type="hidden" name="action" value="actualizarMiPerfil">

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="nombre" class="form-label">
                                        <i class="fas fa-user text-primary me-2"></i>Nombre *
                                    </label>
                                    <input type="text"
                                           class="form-control"
                                           id="nombre"
                                           name="nombre"
                                           required
                                           maxlength="50"
                                           value="<?php echo htmlspecialchars($persona['nombre'] ?? ''); ?>"
                                           placeholder="Ingrese el nombre">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="apellido_paterno" class="form-label">
                                        <i class="fas fa-user text-primary me-2"></i>Apellido Paterno *
                                    </label>
                                    <input type="text"
                                           class="form-control"
                                           id="apellido_paterno"
                                           name="apellido_paterno"
                                           required
                                           maxlength="50"
                                           value="<?php echo htmlspecialchars($persona['apellido_paterno'] ?? ''); ?>"
                                           placeholder="Ingrese el apellido paterno">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="apellido_materno" class="form-label">
                                        <i class="fas fa-user text-primary me-2"></i>Apellido Materno
                                    </label>
                                    <input type="text"
                                           class="form-control"
                                           id="apellido_materno"
                                           name="apellido_materno"
                                           maxlength="50"
                                           value="<?php echo htmlspecialchars($persona['apellido_materno'] ?? ''); ?>"
                                           placeholder="Ingrese el apellido materno">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope text-primary me-2"></i>Correo Electrónico *
                                    </label>
                                    <input type="email"
                                           class="form-control"
                                           id="email"
                                           name="email"
                                           required
                                           value="<?php echo htmlspecialchars($persona['email'] ?? ''); ?>"
                                           placeholder="correo@ejemplo.com">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="telefono" class="form-label">
                                        <i class="fas fa-phone text-primary me-2"></i>Teléfono *
                                    </label>
                                    <input type="tel"
                                           class="form-control"
                                           id="telefono"
                                           name="telefono"
                                           required
                                           value="<?php echo htmlspecialchars($persona['telefono'] ?? ''); ?>"
                                           placeholder="Ingrese el teléfono">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="ci" class="form-label">
                                        <i class="fas fa-id-card text-muted me-2"></i>Cédula de Identidad
                                    </label>
                                    <input type="text"
                                           class="form-control"
                                           id="ci"
                                           name="ci"
                                           readonly
                                           value="<?php echo htmlspecialchars($persona['ci'] ?? ''); ?>"
                                           style="background-color: #e9ecef;">
                                    <div class="form-text text-muted">No se puede modificar</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="username" class="form-label">
                                        <i class="fas fa-user-tag text-muted me-2"></i>Usuario
                                    </label>
                                    <input type="text"
                                           class="form-control"
                                           id="username"
                                           name="username"
                                           readonly
                                           value="<?php echo htmlspecialchars($persona['username'] ?? ''); ?>"
                                           style="background-color: #e9ecef;">
                                    <div class="form-text text-muted">No se puede modificar</div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Checkbox para cambiar contraseña -->
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="cambiarPassword" name="cambiar_password">
                            <label class="form-check-label" for="cambiarPassword">
                                <i class="fas fa-key me-2"></i>
                                <strong>Cambiar Contraseña</strong>
                            </label>
                        </div>

                        <!-- Campos de contraseña (ocultos por defecto) -->
                        <div id="passwordFields" style="display: none;">
                            <div class="alert alert-info mb-3">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Cambiar Contraseña</strong>
                                <p class="mb-0 small">Complete los campos para cambiar su contraseña.</p>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="nueva_password" class="form-label">
                                            <i class="fas fa-lock text-primary me-2"></i>Nueva Contraseña *
                                        </label>
                                        <div class="password-container position-relative">
                                            <input type="password"
                                                   class="form-control"
                                                   id="nueva_password"
                                                   name="nueva_password"
                                                   minlength="8"
                                                   placeholder="Mínimo 8 caracteres">
                                            <span class="toggle-password position-absolute" style="right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;">
                                                <i class="far fa-eye"></i>
                                            </span>
                                        </div>
                                        <div class="form-text">Mínimo 8 caracteres</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="confirmar_password" class="form-label">
                                            <i class="fas fa-lock text-primary me-2"></i>Confirmar Contraseña *
                                        </label>
                                        <div class="password-container position-relative">
                                            <input type="password"
                                                   class="form-control"
                                                   id="confirmar_password"
                                                   name="confirmar_password"
                                                   minlength="8"
                                                   placeholder="Confirme la contraseña">
                                            <span class="toggle-password position-absolute" style="right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;">
                                                <i class="far fa-eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="../controlador/DashboardControlador.php?action=mostrarDashboard<?php echo ($_SESSION['id_rol'] == '1') ? 'Administrador' : (($_SESSION['id_rol'] == '2') ? 'Residente' : 'Personal'); ?>" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .avatar-circle {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1a5276 0%, #2a7595 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 2.5rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .password-container {
            position: relative;
        }

        .toggle-password {
            cursor: pointer;
            color: #6c757d;
            transition: color 0.3s;
        }

        .toggle-password:hover {
            color: #495057;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cambiarPasswordCheckbox = document.getElementById('cambiarPassword');
            const passwordFields = document.getElementById('passwordFields');
            const nuevaPasswordInput = document.getElementById('nueva_password');
            const confirmarPasswordInput = document.getElementById('confirmar_password');

            // Mostrar/ocultar campos de contraseña según el checkbox
            cambiarPasswordCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    passwordFields.style.display = 'block';
                    nuevaPasswordInput.setAttribute('required', 'required');
                    confirmarPasswordInput.setAttribute('required', 'required');
                } else {
                    passwordFields.style.display = 'none';
                    nuevaPasswordInput.removeAttribute('required');
                    confirmarPasswordInput.removeAttribute('required');
                    nuevaPasswordInput.value = '';
                    confirmarPasswordInput.value = '';
                }
            });

            // Toggle password visibility
            document.querySelectorAll('.toggle-password').forEach(toggle => {
                toggle.addEventListener('click', function() {
                    const input = this.previousElementSibling || this.parentElement.querySelector('input[type="password"], input[type="text"]');
                    const icon = this.querySelector('i');
                    
                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    } else {
                        input.type = 'password';
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                });
            });

            // Validar contraseñas al enviar
            document.getElementById('formActualizarPerfil').addEventListener('submit', function(e) {
                // Solo validar si el checkbox está marcado
                if (cambiarPasswordCheckbox.checked) {
                    const nuevaPassword = nuevaPasswordInput.value;
                    const confirmarPassword = confirmarPasswordInput.value;

                    if (!nuevaPassword || !confirmarPassword) {
                        e.preventDefault();
                        alert('Por favor complete ambos campos de contraseña');
                        return false;
                    }

                    if (nuevaPassword !== confirmarPassword) {
                        e.preventDefault();
                        alert('Las contraseñas no coinciden');
                        return false;
                    }

                    if (nuevaPassword.length < 8) {
                        e.preventDefault();
                        alert('La contraseña debe tener al menos 8 caracteres');
                        return false;
                    }
                } else {
                    // Si el checkbox no está marcado, limpiar los campos
                    nuevaPasswordInput.value = '';
                    confirmarPasswordInput.value = '';
                }
            });

            // Auto-ocultar alertas después de 5 segundos
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });
    </script>

<?php include("../../includes/footer.php"); ?>


<?php include("../../includes/header.php");?>


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
                                               placeholder="ejemplo@correo.com">
                                    </div>
                                </div>

                                <div class="col-md-6">
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
                                <li>El email debe tener un formato válido</li>
                                <li>Los datos sensibles se almacenan cifrados</li>
                            </ul>
                        </div>

                        <div class="alert alert-warning">
                            <h6><i class="fas fa-exclamation-triangle me-2"></i>Notas:</h6>
                            <ul class="mb-0 mt-2">
                                <li>No se permiten cédulas duplicadas</li>
                                <li>Una vez registrado, la persona podrá acceder al sistema</li>
                                <li>Verifique la información antes de guardar</li>
                                <li>Los residentes se registran en una sección diferente</li>
                            </ul>
                        </div>

                        <div class="alert alert-success">
                            <h6><i class="fas fa-shield-alt me-2"></i>Seguridad:</h6>
                            <ul class="mb-0 mt-2">
                                <li>Datos personales cifrados</li>
                                <li>Protección de información sensible</li>
                                <li>Acceso restringido por roles</li>
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

            // Validación del formulario
            formRegistrar.addEventListener('submit', function(e) {
                const nombre = document.getElementById('nombre').value.trim();
                const apellidoPaterno = document.getElementById('apellido_paterno').value.trim();
                const ci = document.getElementById('ci').value.trim();
                const telefono = document.getElementById('telefono').value.trim();
                const email = document.getElementById('email').value.trim();
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

                // Validar email
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!email || !emailRegex.test(email)) {
                    e.preventDefault();
                    alert('Por favor, ingrese un email válido');
                    document.getElementById('email').focus();
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

            // Validación en tiempo real para email
            document.getElementById('email').addEventListener('input', function() {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(this.value)) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });
        });
    </script>

    <!-- Estilos adicionales -->
    <style>
        .is-invalid {
            border-color: #dc3545 !important;
        }

        .form-text {
            font-size: 0.875rem;
            color: #6c757d;
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
    </style>

<?php include("../../includes/footer.php");?>
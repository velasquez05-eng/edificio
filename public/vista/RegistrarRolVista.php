<?php include("../../includes/header.php");?>


            <!-- Page Header -->
            <div class="page-header fade-in">
                <div class="page-title">
                    <h1>Registrar Nuevo Rol</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                            <li class="breadcrumb-item"><a href="../controlador/RolControlador.php?action=listar">Roles</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Registrar Rol</li>
                        </ol>
                    </nav>
                </div>

            </div>

            <!-- Formulario de Registro -->
            <div class="row fade-in">
                <div class="col-lg-8">
                    <div class="content-box">
                        <div class="content-box-header">
                            <h5>Información del Rol</h5>
                        </div>
                        <div class="content-box-body">
                            <form id="formRegistrarRol" action="../controlador/RolControlador.php" method="POST">
                                <input type="hidden" name="action" value="registrar">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="rol" class="form-label">
                                                <i class="fas fa-user-tag text-verde me-2"></i>Nombre del Rol *
                                            </label>
                                            <input type="text"
                                                   class="form-control"
                                                   id="rol"
                                                   name="rol"
                                                   required
                                                   maxlength="50"
                                                   placeholder="Ej: Administrador, Residente, etc.">
                                            <div class="form-text">Nombre único identificador del rol</div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="descripcion" class="form-label">
                                                <i class="fas fa-align-left text-azul me-2"></i>Descripción *
                                            </label>
                                            <input type="text"
                                                   class="form-control"
                                                   id="descripcion"
                                                   name="descripcion"
                                                   required
                                                   maxlength="200"
                                                   placeholder="Descripción de las funciones del rol">
                                            <div class="form-text">Descripción detallada del rol</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                    <a href="RolControlador.php?action=listar" class="btn btn-secondary me-md-2">
                                        <i class="fas fa-times me-2"></i>Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary" style="background: var(--verde); border: none;">
                                        <i class="fas fa-save me-2"></i>Registrar Rol
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
                                    <li>El nombre del rol debe ser único</li>
                                    <li>Verifique que el rol no exista previamente</li>
                                    <li>La descripción debe ser clara y concisa</li>
                                </ul>
                            </div>

                            <div class="alert alert-warning">
                                <h6><i class="fas fa-exclamation-triangle me-2"></i>Notas:</h6>
                                <ul class="mb-0 mt-2">
                                    <li>No se permiten roles duplicados</li>
                                    <li>Una vez registrado, el rol estará disponible inmediatamente</li>
                                    <li>Verifique la información antes de guardar</li>
                                    <li>Los roles definen los permisos de los usuarios</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


    <!-- Script para validaciones -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const formRegistrar = document.getElementById('formRegistrarRol');
            const btnRegistrar = formRegistrar.querySelector('button[type="submit"]');

            // Validación del formulario
            formRegistrar.addEventListener('submit', function(e) {
                const rol = document.getElementById('rol').value.trim();
                const descripcion = document.getElementById('descripcion').value.trim();

                if (!rol) {
                    e.preventDefault();
                    alert('Por favor, ingrese el nombre del rol');
                    document.getElementById('rol').focus();
                    return;
                }

                if (!descripcion) {
                    e.preventDefault();
                    alert('Por favor, ingrese la descripción del rol');
                    document.getElementById('descripcion').focus();
                    return;
                }

                if (rol.length < 3) {
                    e.preventDefault();
                    alert('El nombre del rol debe tener al menos 3 caracteres');
                    document.getElementById('rol').focus();
                    return;
                }

                if (descripcion.length < 10) {
                    e.preventDefault();
                    alert('La descripción debe tener al menos 10 caracteres');
                    document.getElementById('descripcion').focus();
                    return;
                }

                // Mostrar loading en el botón
                btnRegistrar.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Registrando...';
                btnRegistrar.disabled = true;
            });

            // Validación en tiempo real
            document.getElementById('rol').addEventListener('input', function() {
                if (this.value.length < 3) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });

            document.getElementById('descripcion').addEventListener('input', function() {
                if (this.value.length < 10) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });
        });


    </script>

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
    </style>

<?php include("../../includes/footer.php");?>
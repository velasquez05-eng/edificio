<?php include("../../includes/header.php");?>

    <!-- Main Content -->

            <!-- Page Header -->
            <div class="page-header fade-in">
                <div class="page-title">
                    <h1>Listado de Roles</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Roles</li>
                        </ol>
                    </nav>
                </div>
                <div class="page-actions">
                    <a href="../vista/RegistrarRolVista.php" class="btn btn-success">
                        Registrar <i class="fas fa-arrow-right me-2"></i>
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

            <!-- Roles -->
            <div class="row fade-in">
                <div class="col-12">
                    <div class="content-box">
                        <div class="content-box-header d-flex justify-content-between align-items-center">
                            <h5>Roles Registrados</h5>
                            <span class="badge bg-primary"><?php echo count($roles); ?> roles</span>
                        </div>
                        <div class="content-box-body">
                            <?php if (empty($roles)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-user-tag fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No hay roles registrados</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped">
                                        <thead>
                                        <tr>
                                            <th># ID</th>
                                            <th>Nombre del Rol</th>
                                            <th>Descripción</th>
                                            <th>Estado</th>
                                            <th>Opciones</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($roles as $rol): ?>
                                            <tr>
                                                <td><strong><?php echo htmlspecialchars($rol['id_rol']); ?></strong></td>
                                                <td>
                                                    <i class="fas fa-user-tag text-primary me-2"></i>
                                                    <?php echo htmlspecialchars($rol['rol']); ?>
                                                </td>
                                                <td>
                                                    <?php echo htmlspecialchars($rol['descripcion']); ?>
                                                </td>
                                                <td>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check me-1"></i>Activo
                                                </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-warning btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editarRolModal"
                                                            data-id="<?php echo htmlspecialchars($rol['id_rol']); ?>"
                                                            data-nombre="<?php echo htmlspecialchars($rol['rol']); ?>"
                                                            data-descripcion="<?php echo htmlspecialchars($rol['descripcion']); ?>"
                                                            title="Editar rol">
                                                        <i class="fas fa-edit"></i> Editar
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>


    <!-- Modal Editar Rol -->
    <div class="modal fade" id="editarRolModal" tabindex="-1" aria-labelledby="editarRolModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarRolModalLabel">
                        <i class="fas fa-edit me-2"></i>Editar Rol
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="RolControlador.php?action=editar" id="formEditarRol">
                    <input type="hidden" name="action" value="editar">
                    <input type="hidden" id="id_rol" name="id_rol">

                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label for="rol" class="form-label">
                                            <i class="fas fa-user-tag text-verde me-2"></i>Nombre del Rol
                                        </label>
                                        <input type="text" class="form-control" id="rol" name="rol" required
                                               maxlength="50" readonly>
                                        <div class="form-text">Nombre único del rol</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label for="descripcion" class="form-label">
                                            <i class="fas fa-align-left text-azul me-2"></i>Descripción
                                        </label>
                                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"
                                                  maxlength="200"></textarea>
                                        <div class="form-text">Descripción del rol (máximo 200 caracteres)</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-warning" id="btnGuardarCambios">
                            <i class="fas fa-save me-2"></i>Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script para cargar datos en el modal y validaciones -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editarModal = document.getElementById('editarRolModal');
            const formEditar = document.getElementById('formEditarRol');
            const btnGuardar = document.getElementById('btnGuardarCambios');

            // Cargar datos en el modal
            editarModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const nombre = button.getAttribute('data-nombre');
                const descripcion = button.getAttribute('data-descripcion');

                document.getElementById('id_rol').value = id;
                document.getElementById('rol').value = nombre;
                document.getElementById('descripcion').value = descripcion;
            });

            // Validación del formulario
            formEditar.addEventListener('submit', function(e) {
                const nombre = document.getElementById('rol').value.trim();
                const descripcion = document.getElementById('descripcion').value.trim();

                if (!nombre) {
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

                // Mostrar loading en el botón
                btnGuardar.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Guardando...';
                btnGuardar.disabled = true;
            });

            // Limpiar el modal cuando se cierre
            editarModal.addEventListener('hidden.bs.modal', function() {
                formEditar.reset();
                btnGuardar.innerHTML = '<i class="fas fa-save me-2"></i>Guardar Cambios';
                btnGuardar.disabled = false;
            });
        });

        // Auto-ocultar alertas después de 5 segundos
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>

    <!-- Estilos para el modal -->
    <style>
        .modal-content {
            border: none;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(13, 61, 71, 0.3);
            overflow: hidden;
        }

        .modal-header {
            background: linear-gradient(135deg, var(--azul-oscuro) 0%, var(--azul) 100%);
            color: white;
            border-bottom: 2px solid rgba(175, 239, 206, 0.3);
            padding: 20px 25px;
        }

        .modal-title {
            font-weight: 600;
            font-size: 1.3rem;
        }

        .btn-close {
            filter: invert(1);
            opacity: 0.8;
            transition: all 0.3s;
        }

        .btn-close:hover {
            opacity: 1;
            transform: rotate(90deg);
        }

        .modal-body {
            padding: 30px;
        }

        .modal-footer {
            border-top: 1px solid #f0f0f0;
            padding: 20px 25px;
            gap: 10px;
        }

        .form-label {
            font-weight: 600;
            color: var(--azul-oscuro);
            margin-bottom: 8px;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 10px 15px;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: var(--verde);
            box-shadow: 0 0 0 0.2rem rgba(54, 137, 121, 0.25);
        }

        .form-text {
            font-size: 0.85rem;
            color: var(--verde-suave);
        }

        .btn-warning {
            background: linear-gradient(135deg, var(--verde) 0%, var(--azul) 100%);
            border: none;
            color: white;
            font-weight: 600;
            padding: 10px 25px;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .btn-warning:hover {
            background: linear-gradient(135deg, var(--azul) 0%, var(--azul-oscuro) 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(42, 117, 149, 0.4);
        }

        .btn-secondary {
            background: #6c757d;
            border: none;
            font-weight: 600;
            padding: 10px 25px;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        .text-verde { color: var(--verde); }
        .text-azul { color: var(--azul); }

        .table th {
            border-top: none;
            font-weight: 600;
            color: var(--azul-oscuro);
        }

        .content-box-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 1px solid #dee2e6;
        }
    </style>

<?php include("../../includes/footer.php");?>
<?php include("../../includes/header.php");?>


        <!-- Page Header -->
        <div class="page-header fade-in">
            <div class="page-title">
                <h1>Listado de Personal</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Personal</li>
                    </ol>
                </nav>
            </div>
            <div class="page-actions">
                <a href="../vista/RegistrarPersonaVista.php" class="btn btn-success">
                    Registrar Personal <i class="fas fa-user-plus me-2"></i>
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

        <!-- Personal -->
        <div class="row fade-in">
            <div class="col-12">
                <div class="content-box">
                    <div class="content-box-header d-flex justify-content-between align-items-center">
                        <h5>Personal Registrado</h5>
                        <span class="badge bg-primary"><?php echo count($personal); ?> personas</span>
                    </div>
                    <div class="content-box-body">
                        <?php if (empty($personal)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No hay personal registrado</p>
                                <a href="../vista/RegistrarPersonaVista.php" class="btn btn-success">
                                    <i class="fas fa-user-plus me-2"></i>Registrar Primer Personal
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead>
                                    <tr>
                                        <th># ID</th>
                                        <th>Nombre Completo</th>
                                        <th>CI</th>
                                        <th>Teléfono</th>
                                        <th>Email</th>
                                        <th>Rol</th>
                                        <th>Estado</th>
                                        <th>Opciones</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($personal as $persona): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($persona['id_persona']); ?></strong></td>
                                            <td>
                                                <i class="fas fa-user text-primary me-2"></i>
                                                <?php echo htmlspecialchars($persona['nombre'] . ' ' . $persona['apellido_paterno'] . ' ' . $persona['apellido_materno']); ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($persona['ci']); ?>
                                            </td>
                                            <td>
                                                <i class="fas fa-phone text-success me-2"></i>
                                                <?php echo htmlspecialchars($persona['telefono']); ?>
                                            </td>
                                            <td>
                                                <i class="fas fa-envelope text-warning me-2"></i>
                                                <?php echo htmlspecialchars($persona['email']); ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    <i class="fas fa-user-tag me-1"></i>
                                                    <?php echo htmlspecialchars($persona['rol']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check me-1"></i>Activo
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-warning btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editarPersonaModal"
                                                            data-id="<?php echo htmlspecialchars($persona['id_persona']); ?>"
                                                            data-nombre="<?php echo htmlspecialchars($persona['nombre']); ?>"
                                                            data-apellido_paterno="<?php echo htmlspecialchars($persona['apellido_paterno']); ?>"
                                                            data-apellido_materno="<?php echo htmlspecialchars($persona['apellido_materno']); ?>"
                                                            data-ci="<?php echo htmlspecialchars($persona['ci']); ?>"
                                                            data-telefono="<?php echo htmlspecialchars($persona['telefono']); ?>"
                                                            data-email="<?php echo htmlspecialchars($persona['email']); ?>"
                                                            data-id_rol="<?php echo htmlspecialchars($persona['id_rol']); ?>"
                                                            title="Editar persona">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-danger btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#eliminarPersonaModal"
                                                            data-id="<?php echo htmlspecialchars($persona['id_persona']); ?>"
                                                            data-nombre="<?php echo htmlspecialchars($persona['nombre'] . ' ' . $persona['apellido_paterno']); ?>"
                                                            title="Eliminar persona">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
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


    <!-- Modal Editar Persona -->
    <div class="modal fade" id="editarPersonaModal" tabindex="-1" aria-labelledby="editarPersonaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarPersonaModalLabel">
                        <i class="fas fa-edit me-2"></i>Editar Persona
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="PersonaControlador.php?action=editar" id="formEditarPersona">
                    <input type="hidden" name="action" value="editar">
                    <input type="hidden" id="id_persona" name="id_persona">

                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="nombre" class="form-label">Nombre</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="apellido_paterno" class="form-label">Apellido Paterno</label>
                                        <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="apellido_materno" class="form-label">Apellido Materno</label>
                                        <input type="text" class="form-control" id="apellido_materno" name="apellido_materno">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="ci" class="form-label">CI</label>
                                        <input type="text" class="form-control" id="ci" name="ci" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="telefono" class="form-label">Teléfono</label>
                                        <input type="tel" class="form-control" id="telefono" name="telefono" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="id_rol" class="form-label">Rol</label>
                                        <select class="form-control" id="id_rol" name="id_rol" required>
                                            <!-- Las opciones se cargarán dinámicamente -->
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save me-2"></i>Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script para cargar datos en el modal -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editarModal = document.getElementById('editarPersonaModal');

            // Cargar datos en el modal de editar
            editarModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;

                document.getElementById('id_persona').value = button.getAttribute('data-id');
                document.getElementById('nombre').value = button.getAttribute('data-nombre');
                document.getElementById('apellido_paterno').value = button.getAttribute('data-apellido_paterno');
                document.getElementById('apellido_materno').value = button.getAttribute('data-apellido_materno');
                document.getElementById('ci').value = button.getAttribute('data-ci');
                document.getElementById('telefono').value = button.getAttribute('data-telefono');
                document.getElementById('email').value = button.getAttribute('data-email');

                // Aquí deberías cargar los roles disponibles en el select
                // Por ahora, simplemente establecemos el valor
                document.getElementById('id_rol').value = button.getAttribute('data-id_rol');
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

    <!-- Estilos adicionales -->
    <style>
        .content-box-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 1px solid #dee2e6;
        }

        .table th {
            border-top: none;
            font-weight: 600;
            color: var(--azul-oscuro);
            background-color: #f8f9fa;
        }

        .btn-group .btn {
            margin: 0 2px;
        }

        .badge {
            font-size: 0.75rem;
        }
    </style>

<?php include("../../includes/footer.php");?>
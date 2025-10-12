<?php include("../../includes/header.php"); ?>

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
                                    <th>Username</th>
                                    <th>Teléfono</th>
                                    <th>Email</th>
                                    <th>Rol</th>
                                    <th>Verificado</th>
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
                                            <i class="fas fa-user-circle text-info me-2"></i>
                                            <?php echo htmlspecialchars($persona['username']); ?>
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
                                            <?php if ($persona['verificado']): ?>
                                                <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i>Verificado
                                            </span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">
                                                <i class="fas fa-clock me-1"></i>Pendiente
                                            </span>
                                            <?php endif; ?>
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
                                                        data-username="<?php echo htmlspecialchars($persona['username']); ?>"
                                                        data-id_rol="<?php echo htmlspecialchars($persona['id_rol']); ?>"
                                                        title="Editar datos personales">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-info btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editarPasswordModal"
                                                        data-id="<?php echo htmlspecialchars($persona['id_persona']); ?>"
                                                        data-nombre="<?php echo htmlspecialchars($persona['nombre'] . ' ' . $persona['apellido_paterno']); ?>"
                                                        data-id-rol="<?php echo htmlspecialchars($persona['id_rol']); ?>"
                                                        data-ci="<?php echo htmlspecialchars($persona['ci']); ?>"
                                                        title="Cambiar contraseña">
                                                    <i class="fas fa-key"></i>
                                                </button>
                                                <?php if (!$persona['verificado']): ?>
                                                    <button class="btn btn-secondary btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#ampliarVerificacionModal"
                                                            data-id="<?php echo htmlspecialchars($persona['id_persona']); ?>"
                                                            data-nombre="<?php echo htmlspecialchars($persona['nombre'] . ' ' . $persona['apellido_paterno']); ?>"
                                                            title="Ampliar tiempo de verificación">
                                                        <i class="fas fa-clock"></i>
                                                    </button>
                                                <?php endif; ?>
                                                <button class="btn btn-primary btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#departamentosModal"
                                                        data-id="<?php echo htmlspecialchars($persona['id_persona']); ?>"
                                                        data-nombre="<?php echo htmlspecialchars($persona['nombre'] . ' ' . $persona['apellido_paterno']); ?>"
                                                        title="Ver departamentos">
                                                    <i class="fas fa-building"></i>
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
                        <i class="fas fa-edit me-2"></i>Editar Datos Personales
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="PersonaControlador.php?action=editarPersona" id="formEditarPersona">
                    <input type="hidden" name="action" value="editarPersona">
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
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
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
                                        <label for="ci" class="form-label">CI</label>
                                        <input type="text" class="form-control" id="ci" name="ci" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" class="form-control" id="username" name="username" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label for="id_rol" class="form-label">Rol</label>
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

    <!-- Modal Cambiar Contraseña -->
    <div class="modal fade" id="editarPasswordModal" tabindex="-1" aria-labelledby="editarPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarPasswordModalLabel">
                        <i class="fas fa-key me-2"></i>Cambiar Contraseña
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="PersonaControlador.php?action=actualizarPasswordOpcion" id="formEditarPassword">
                    <input type="hidden" name="action" value="actualizarPasswordOpcion">
                    <input type="hidden" id="id_persona_password" name="id_persona">
                    <input type="hidden" id="id_rol_password" name="id_rol">
                    <input type="hidden" class="form-control" id="ci_password" name="ci_password" required>

                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="mb-3">
                                <p>Cambiando contraseña para: <strong id="nombrePersonaPassword"></strong></p>
                                <p>La nueva contraseña sera su: <strong id="numeroci"> </strong></p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-info">
                            <i class="fas fa-save me-2"></i>Cambiar Contraseña
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Ampliar Verificación -->
    <div class="modal fade" id="ampliarVerificacionModal" tabindex="-1" aria-labelledby="ampliarVerificacionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ampliarVerificacionModalLabel">
                        <i class="fas fa-clock me-2"></i>Ampliar Tiempo de Verificación
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="PersonaControlador.php?action=ampliarVerificacion" id="formAmpliarVerificacion">
                    <input type="hidden" name="action" value="ampliarVerificacion">
                    <input type="hidden" id="id_persona_verificacion" name="id_persona">

                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="mb-3">
                                <p>Ampliando tiempo de verificación para: <strong id="nombrePersonaVerificacion"></strong></p>
                            </div>
                            <div class="form-group mb-3">
                                <label for="dias_ampliacion" class="form-label">Días de ampliación</label>
                                <select class="form-control" id="dias_ampliacion" name="dias_ampliacion" required>
                                    <option value="1">1 día</option>
                                    <option value="3">3 días</option>
                                    <option value="7">7 días</option>
                                    <option value="15">15 días</option>
                                    <option value="30">30 días</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-secondary">
                            <i class="fas fa-clock me-2"></i>Ampliar Verificación
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Ver Departamentos -->
    <div class="modal fade" id="departamentosModal" tabindex="-1" aria-labelledby="departamentosModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="departamentosModalLabel">
                        <i class="fas fa-building me-2"></i>Departamentos del Personal
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="mb-3">
                            <p>Departamentos asignados a: <strong id="nombrePersonaDepartamentos"></strong></p>
                        </div>
                        <div id="listaDepartamentos">
                            <!-- Los departamentos se cargarán dinámicamente -->
                            <div class="text-center py-3">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                                <p class="mt-2">Cargando departamentos...</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Eliminar Persona -->
    <div class="modal fade" id="eliminarPersonaModal" tabindex="-1" aria-labelledby="eliminarPersonaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eliminarPersonaModalLabel">
                        <i class="fas fa-trash me-2"></i>Eliminar Personal
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="PersonaControlador.php?action=eliminar" id="formEliminarPersona">
                    <input type="hidden" name="action" value="eliminar">
                    <input type="hidden" id="id_persona_eliminar" name="id_persona">

                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="text-center mb-3">
                                <i class="fas fa-exclamation-triangle fa-3x text-warning"></i>
                            </div>
                            <p class="text-center">¿Está seguro que desea eliminar al siguiente personal?</p>
                            <p class="text-center fw-bold" id="nombrePersonaEliminar"></p>
                            <p class="text-center text-danger">Esta acción no se puede deshacer.</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-2"></i>Eliminar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script para cargar datos en los modales -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Cargar datos en el modal de editar
            const editarModal = document.getElementById('editarPersonaModal');
            editarModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;

                document.getElementById('id_persona').value = button.getAttribute('data-id');
                document.getElementById('nombre').value = button.getAttribute('data-nombre');
                document.getElementById('apellido_paterno').value = button.getAttribute('data-apellido_paterno');
                document.getElementById('apellido_materno').value = button.getAttribute('data-apellido_materno');
                document.getElementById('ci').value = button.getAttribute('data-ci');
                document.getElementById('telefono').value = button.getAttribute('data-telefono');
                document.getElementById('email').value = button.getAttribute('data-email');
                document.getElementById('username').value = button.getAttribute('data-username');
                document.getElementById('id_rol').value = button.getAttribute('data-id_rol');
            });

            // Cargar datos en el modal de cambiar contraseña
            const passwordModal = document.getElementById('editarPasswordModal');
            passwordModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;

                document.getElementById('id_persona_password').value = button.getAttribute('data-id');
                document.getElementById('nombrePersonaPassword').textContent = button.getAttribute('data-nombre');
                document.getElementById('id_rol_password').value = button.getAttribute('data-id-rol');
                document.getElementById('numeroci').textContent = button.getAttribute('data-ci');
                document.getElementById('ci_password').value = button.getAttribute('data-ci');
            });

            // Cargar datos en el modal de ampliar verificación
            const verificacionModal = document.getElementById('ampliarVerificacionModal');
            verificacionModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;

                document.getElementById('id_persona_verificacion').value = button.getAttribute('data-id');
                document.getElementById('nombrePersonaVerificacion').textContent = button.getAttribute('data-nombre');
            });

            // Cargar datos en el modal de departamentos
            const departamentosModal = document.getElementById('departamentosModal');
            departamentosModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const idPersona = button.getAttribute('data-id');

                document.getElementById('nombrePersonaDepartamentos').textContent = button.getAttribute('data-nombre');

                // Aquí deberías cargar los departamentos del personal desde el servidor
                // Por ahora, simulamos una carga
                setTimeout(function() {
                    document.getElementById('listaDepartamentos').innerHTML = `
                    <div class="list-group">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            Departamento de Ventas
                            <span class="badge bg-primary">Principal</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            Departamento de Marketing
                            <span class="badge bg-secondary">Secundario</span>
                        </div>
                    </div>
                `;
                }, 1000);
            });

            // Cargar datos en el modal de eliminar
            const eliminarModal = document.getElementById('eliminarPersonaModal');
            eliminarModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;

                document.getElementById('id_persona_eliminar').value = button.getAttribute('data-id');
                document.getElementById('nombrePersonaEliminar').textContent = button.getAttribute('data-nombre');
            });

            // Validación de contraseñas coincidentes
            const formPassword = document.getElementById('formEditarPassword');
            formPassword.addEventListener('submit', function(event) {
                const nuevaPassword = document.getElementById('nueva_password').value;
                const confirmarPassword = document.getElementById('confirmar_password').value;

                if (nuevaPassword !== confirmarPassword) {
                    event.preventDefault();
                    alert('Las contraseñas no coinciden. Por favor, inténtelo de nuevo.');
                    return false;
                }

                if (nuevaPassword.length < 6) {
                    event.preventDefault();
                    alert('La contraseña debe tener al menos 6 caracteres.');
                    return false;
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

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
    </style>

<?php include("../../includes/footer.php"); ?>
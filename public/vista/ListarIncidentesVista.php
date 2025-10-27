<?php include("../../includes/header.php"); ?>

    <!-- Page Header -->
    <div class="page-header fade-in">
        <div class="page-title">
            <h1>Gestión de Incidentes</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item"><a href="#">Incidentes</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Lista de Incidentes</li>
                </ol>
            </nav>
        </div>
        <div class="page-actions">
            <a href="IncidenteControlador.php?action=formularioIncidente" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>Registrar Incidente
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

    <!-- Navegación entre secciones -->
    <div class="row fade-in mb-4">
        <div class="col-12">
            <div class="content-box">
                <div class="content-box-body">
                    <div class="row text-center">
                        <div class="col-md-3 mb-3 mb-md-0">
                            <a href="IncidenteControlador.php?action=verIncidentesPendientes"
                               class="btn btn-warning btn-lg w-100">
                                <i class="fas fa-clock me-2"></i>
                                Pendientes
                                <span class="badge bg-dark ms-2"><?php echo $numeroIncidentesPendientes; ?></span>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <a href="IncidenteControlador.php?action=verIncidentesProceso"
                               class="btn btn-info btn-lg w-100">
                                <i class="fas fa-tasks me-2"></i>
                                En Proceso
                                <span class="badge bg-dark ms-2"><?php echo $numeroIncidentesProceso; ?></span>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <a href="IncidenteControlador.php?action=verIncidentesResueltos"
                               class="btn btn-success btn-lg w-100">
                                <i class="fas fa-check-circle me-2"></i>
                                Resueltos
                                <span class="badge bg-dark ms-2"><?php echo $numeroIncidentesResueltos; ?></span>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="IncidenteControlador.php?action=verIncidentesReasignacion"
                               class="btn btn-secondary btn-lg w-100">
                                <i class="fas fa-sync-alt me-2"></i>
                                Por Reasignar
                                <span class="badge bg-dark ms-2"><?php echo $numeroIncidentesReasignacion; ?></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas de Incidentes -->
    <div class="row fade-in mb-4">
        <div class="col-md-2">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                    <h4 class="mb-1"><?php echo $numeroIncidentesPendientes; ?></h4>
                    <p class="text-muted mb-0">Pendientes</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-tasks fa-2x text-info mb-2"></i>
                    <h4 class="mb-1"><?php echo $numeroIncidentesProceso; ?></h4>
                    <p class="text-muted mb-0">En Proceso</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                    <h4 class="mb-1"><?php echo $numeroIncidentesResueltos; ?></h4>
                    <p class="text-muted mb-0">Resueltos</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-sync-alt fa-2x text-secondary mb-2"></i>
                    <h4 class="mb-1"><?php echo $numeroIncidentesReasignacion; ?></h4>
                    <p class="text-muted mb-0">Por Reasignar</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-times-circle fa-2x text-danger mb-2"></i>
                    <h4 class="mb-1"><?php echo $numeroIncidentesCancelados; ?></h4>
                    <p class="text-muted mb-0">Cancelados</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-exclamation-triangle fa-2x text-primary mb-2"></i>
                    <h4 class="mb-1"><?php echo $numeroTotalIncidentes; ?></h4>
                    <p class="text-muted mb-0">Total</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Incidentes -->
    <div class="row fade-in">
        <div class="col-12">
            <div class="content-box">
                <div class="content-box-header d-flex justify-content-between align-items-center">
                    <h5>Lista de Incidentes</h5>
                    <span class="badge bg-primary"><?php echo count($incidentes); ?> incidentes</span>
                </div>
                <div class="content-box-body">
                    <?php if (empty($incidentes)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-exclamation-triangle fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No hay incidentes registrados</p>
                        </div>
                    <?php else: ?>
                        <div class="table-container">
                            <table id="tablaIncidentes" class="table table-hover table-striped">
                                <thead>
                                <tr>
                                    <th># ID</th>
                                    <th>Departamento</th>
                                    <th>Residente</th>
                                    <th>Descripción</th>
                                    <th>Tipo</th>
                                    <th>Fecha Registro</th>
                                    <th>Estado</th>
                                    <th>Personal Asignado</th>
                                    <th>Opciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($incidentes as $incidente): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($incidente['id_incidente']); ?></strong></td>
                                        <td>
                                            <i class="fas fa-building text-primary me-2"></i>
                                            <?php echo htmlspecialchars($incidente['numero_departamento']); ?>
                                        </td>
                                        <td>
                                            <i class="fas fa-user text-info me-2"></i>
                                            <?php echo htmlspecialchars($incidente['nombre_residente']); ?>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($incidente['descripcion']); ?>
                                        </td>
                                        <td>
                                            <?php
                                            $badge_class = '';
                                            $icon = '';
                                            switch($incidente['tipo']) {
                                                case 'interno':
                                                    $badge_class = 'bg-primary';
                                                    $icon = 'fa-building';
                                                    break;
                                                case 'externo':
                                                    $badge_class = 'bg-secondary';
                                                    $icon = 'fa-external-link-alt';
                                                    break;
                                                default:
                                                    $badge_class = 'bg-secondary';
                                                    $icon = 'fa-question-circle';
                                            }
                                            ?>
                                            <span class="badge <?php echo $badge_class; ?>">
                                            <i class="fas <?php echo $icon; ?> me-1"></i>
                                            <?php echo ucfirst(htmlspecialchars($incidente['tipo'])); ?>
                                        </span>
                                        </td>
                                        <td>
                                            <i class="fas fa-calendar text-success me-2"></i>
                                            <?php echo date('d/m/Y H:i', strtotime($incidente['fecha_registro'])); ?>
                                        </td>
                                        <td>
                                            <?php
                                            $badge_class = '';
                                            $icon = '';
                                            switch($incidente['estado']) {
                                                case 'pendiente':
                                                    $badge_class = 'bg-warning';
                                                    $icon = 'fa-clock';
                                                    break;
                                                case 'en_proceso':
                                                    $badge_class = 'bg-info';
                                                    $icon = 'fa-tasks';
                                                    break;
                                                case 'resuelto':
                                                    $badge_class = 'bg-success';
                                                    $icon = 'fa-check-circle';
                                                    break;
                                                case 'cancelado':
                                                    $badge_class = 'bg-danger';
                                                    $icon = 'fa-times-circle';
                                                    break;
                                                default:
                                                    $badge_class = 'bg-secondary';
                                                    $icon = 'fa-question-circle';
                                            }
                                            ?>
                                            <span class="badge <?php echo $badge_class; ?>">
                                            <i class="fas <?php echo $icon; ?> me-1"></i>
                                            <?php echo ucfirst(str_replace('_', ' ', htmlspecialchars($incidente['estado']))); ?>
                                        </span>
                                        </td>
                                        <td>
                                            <?php if (!empty($incidente['personal_asignado'])): ?>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-user-tie text-primary me-2"></i>
                                                    <div>
                                                        <div><?php echo htmlspecialchars($incidente['personal_asignado']); ?></div>
                                                        <?php if ($incidente['requiere_reasignacion']): ?>
                                                            <small class="text-danger">
                                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                                                Requiere reasignación
                                                            </small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">
                                                <i class="fas fa-user-slash me-1"></i>Sin asignar
                                            </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <?php if ($incidente['estado'] == 'cancelado' || $incidente['estado'] == 'resuelto'): ?>
                                                    <!-- Solo mostrar historial para incidentes cancelados o resueltos -->
                                                    <button class="btn btn-primary btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#verHistorialModal"
                                                            data-id="<?php echo htmlspecialchars($incidente['id_incidente']); ?>"
                                                            data-descripcion="<?php echo htmlspecialchars($incidente['descripcion']); ?>"
                                                            title="Ver historial">
                                                        <i class="fas fa-history"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <!-- Botones para incidentes activos -->
                                                    <!-- Botón Editar Información Básica -->
                                                    <button class="btn btn-warning btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editarIncidenteModal"
                                                            data-id="<?php echo htmlspecialchars($incidente['id_incidente']); ?>"
                                                            data-departamento="<?php echo htmlspecialchars($incidente['id_departamento']); ?>"
                                                            data-residente="<?php echo htmlspecialchars($incidente['id_residente']); ?>"
                                                            data-descripcion="<?php echo htmlspecialchars($incidente['descripcion']); ?>"
                                                            data-descripcion-detallada="<?php echo htmlspecialchars($incidente['descripcion_detallada']); ?>"
                                                            data-costo="<?php echo htmlspecialchars($incidente['costo_externo']); ?>"
                                                            data-area="<?php echo htmlspecialchars($incidente['id_area']); ?>"
                                                            data-estado="<?php echo htmlspecialchars($incidente['estado']); ?>"
                                                            title="Editar información del incidente">
                                                        <i class="fas fa-edit"></i>
                                                    </button>

                                                    <!-- Botón Cambiar Tipo -->
                                                    <button class="btn btn-outline-primary btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#cambiarTipoModal"
                                                            data-id="<?php echo htmlspecialchars($incidente['id_incidente']); ?>"
                                                            data-tipo="<?php echo htmlspecialchars($incidente['tipo']); ?>"
                                                            data-descripcion="<?php echo htmlspecialchars($incidente['descripcion']); ?>"
                                                            title="Cambiar tipo de incidente">
                                                        <i class="fas fa-exchange-alt"></i>
                                                    </button>

                                                    <?php if ($incidente['estado'] == 'pendiente'): ?>
                                                        <!-- Botón Asignar Personal -->
                                                        <button class="btn btn-info btn-sm"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#asignarPersonalModal"
                                                                data-id="<?php echo htmlspecialchars($incidente['id_incidente']); ?>"
                                                                data-descripcion="<?php echo htmlspecialchars($incidente['descripcion']); ?>"
                                                                title="Asignar personal">
                                                            <i class="fas fa-user-tie"></i>
                                                        </button>
                                                    <?php elseif ($incidente['estado'] == 'en_proceso'): ?>
                                                        <!-- Botón Reasignar Personal -->
                                                        <?php if ($incidente['requiere_reasignacion'] || true): ?>
                                                            <button class="btn btn-secondary btn-sm"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#reasignarPersonalModal"
                                                                    data-id="<?php echo htmlspecialchars($incidente['id_incidente']); ?>"
                                                                    data-descripcion="<?php echo htmlspecialchars($incidente['descripcion']); ?>"
                                                                    data-personal-actual="<?php echo htmlspecialchars($incidente['id_personal_asignado'] ?? ''); ?>"
                                                                    title="Reasignar personal">
                                                                <i class="fas fa-sync-alt"></i>
                                                            </button>
                                                        <?php endif; ?>

                                                        <!-- Botón Resolver Incidente -->
                                                        <button class="btn btn-success btn-sm"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#resolverIncidenteModal"
                                                                data-id="<?php echo htmlspecialchars($incidente['id_incidente']); ?>"
                                                                data-descripcion="<?php echo htmlspecialchars($incidente['descripcion']); ?>"
                                                                title="Resolver incidente">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    <?php endif; ?>

                                                    <!-- Botón Ver Historial -->
                                                    <button class="btn btn-primary btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#verHistorialModal"
                                                            data-id="<?php echo htmlspecialchars($incidente['id_incidente']); ?>"
                                                            data-descripcion="<?php echo htmlspecialchars($incidente['descripcion']); ?>"
                                                            title="Ver historial">
                                                        <i class="fas fa-history"></i>
                                                    </button>

                                                    <?php if ($incidente['estado'] != 'resuelto' && $incidente['estado'] != 'cancelado'): ?>
                                                        <!-- Botón Cancelar Incidente -->
                                                        <button class="btn btn-danger btn-sm"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#cancelarIncidenteModal"
                                                                data-id="<?php echo htmlspecialchars($incidente['id_incidente']); ?>"
                                                                data-descripcion="<?php echo htmlspecialchars($incidente['descripcion']); ?>"
                                                                title="Cancelar incidente">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                <?php endif; ?>
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

    <!-- Modal Editar Incidente (Información Básica) -->
    <div class="modal fade" id="editarIncidenteModal" tabindex="-1" aria-labelledby="editarIncidenteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarIncidenteModalLabel">
                        <i class="fas fa-edit me-2"></i>Editar Información del Incidente
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="IncidenteControlador.php" id="formEditarIncidente">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="editarIncidente">
                        <input type="hidden" name="id_incidente" id="editIdIncidente">

                        <div class="mb-3">
                            <label for="editDescripcion" class="form-label">
                                <i class="fas fa-align-left text-info me-2"></i>Descripción
                            </label>
                            <textarea class="form-control" id="editDescripcion" name="descripcion" rows="3" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="editDescripcionDetallada" class="form-label">
                                <i class="fas fa-file-alt text-secondary me-2"></i>Descripción Detallada
                            </label>
                            <textarea class="form-control" id="editDescripcionDetallada" name="descripcion_detallada" rows="4"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="editCosto" class="form-label">
                                <i class="fas fa-dollar-sign text-success me-2"></i>Costo Externo (Opcional)
                            </label>
                            <input type="number" class="form-control" id="editCosto" name="costo_externo" step="0.01" min="0" placeholder="0.00">
                        </div>

                        <div class="mb-3">
                            <label for="editEstado" class="form-label">
                                <i class="fas fa-tag text-warning me-2"></i>Estado
                            </label>
                            <select class="form-select" id="editEstado" name="estado" required>
                                <option value="pendiente">Pendiente</option>
                                <option value="en_proceso">En Proceso</option>
                                <option value="resuelto">Resuelto</option>
                                <option value="cancelado">Cancelado</option>
                            </select>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Cambiar Tipo de Incidente -->
    <div class="modal fade" id="cambiarTipoModal" tabindex="-1" aria-labelledby="cambiarTipoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cambiarTipoModalLabel">
                        <i class="fas fa-exchange-alt me-2"></i>Cambiar Tipo de Incidente
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="IncidenteControlador.php" id="formCambiarTipo">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="cambiarTipoIncidente">
                        <input type="hidden" name="id_incidente" id="cambiarTipoId">

                        <div class="text-center mb-3">
                            <i class="fas fa-exchange-alt fa-3x text-primary mb-2"></i>
                            <p class="text-muted">Cambiar tipo del incidente: <strong id="descripcionIncidenteCambiarTipo"></strong></p>
                        </div>

                        <div class="mb-3">
                            <label for="nuevoTipo" class="form-label">
                                <i class="fas fa-tag text-success me-2"></i>Nuevo Tipo
                            </label>
                            <select class="form-select" id="nuevoTipo" name="tipo" required>
                                <option value="interno">Interno</option>
                                <option value="externo">Externo</option>
                            </select>
                        </div>

                        <div id="costoExternoContainer" style="display: none;">
                            <div class="mb-3">
                                <label for="costoExterno" class="form-label">
                                    <i class="fas fa-dollar-sign text-success me-2"></i>Costo Externo
                                </label>
                                <input type="number" class="form-control" id="costoExterno" name="costo_externo" step="0.01" min="0" placeholder="0.00">
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Interno:</strong> Incidente que puede ser resuelto con recursos internos.<br>
                            <strong>Externo:</strong> Incidente que requiere contratación de servicios externos (con costo asociado).
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-exchange-alt me-2"></i>Cambiar Tipo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Asignar Personal -->
    <div class="modal fade" id="asignarPersonalModal" tabindex="-1" aria-labelledby="asignarPersonalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="asignarPersonalModalLabel">
                        <i class="fas fa-user-tie me-2"></i>Asignar Personal
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="IncidenteControlador.php" id="formAsignarPersonal">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="asignarPersonal">
                        <input type="hidden" name="id_incidente" id="asignarIncidenteId">

                        <div class="text-center mb-3">
                            <i class="fas fa-user-tie fa-3x text-info mb-2"></i>
                            <p class="text-muted">Asignar personal para el incidente: <strong id="descripcionIncidenteAsignar"></strong></p>
                        </div>

                        <div class="mb-3">
                            <label for="personalAsignado" class="form-label">
                                <i class="fas fa-users text-primary me-2"></i>Personal Disponible
                            </label>
                            <select class="form-select" id="personalAsignado" name="id_personal" required>
                                <option value="">Seleccionar personal</option>
                                <?php foreach ($personalDisponible as $personal): ?>
                                    <option value="<?php echo htmlspecialchars($personal['id_persona']); ?>">
                                        <?php echo htmlspecialchars($personal['nombre_completo']); ?> - <?php echo htmlspecialchars($personal['especialidad']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="observacionesAsignacion" class="form-label">
                                <i class="fas fa-sticky-note text-secondary me-2"></i>Observaciones
                            </label>
                            <textarea class="form-control" id="observacionesAsignacion" name="observaciones" rows="3" placeholder="Observaciones para el personal asignado..."></textarea>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Al asignar personal, el estado del incidente cambiará a "En Proceso".
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-info">
                            <i class="fas fa-user-tie me-2"></i>Asignar Personal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Reasignar Personal -->
    <div class="modal fade" id="reasignarPersonalModal" tabindex="-1" aria-labelledby="reasignarPersonalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reasignarPersonalModalLabel">
                        <i class="fas fa-sync-alt me-2"></i>Reasignar Personal
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="IncidenteControlador.php" id="formReasignarPersonal">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="reasignarPersonal">
                        <input type="hidden" name="id_incidente" id="reasignarIncidenteId">
                        <input type="hidden" name="id_personal_actual" id="personalActualId">

                        <div class="text-center mb-3">
                            <i class="fas fa-sync-alt fa-3x text-secondary mb-2"></i>
                            <p class="text-muted">Reasignar personal para el incidente: <strong id="descripcionIncidenteReasignar"></strong></p>
                        </div>

                        <div class="mb-3">
                            <label for="nuevoPersonal" class="form-label">
                                <i class="fas fa-users text-primary me-2"></i>Nuevo Personal
                            </label>
                            <select class="form-select" id="nuevoPersonal" name="id_nuevo_personal" required>
                                <option value="">Seleccionar nuevo personal</option>
                                <?php foreach ($personalDisponible as $personal): ?>
                                    <option value="<?php echo htmlspecialchars($personal['id_persona']); ?>">
                                        <?php echo htmlspecialchars($personal['nombre_completo']); ?> - <?php echo htmlspecialchars($personal['especialidad']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="comentarioReasignacion" class="form-label">
                                <i class="fas fa-comment-dots text-warning me-2"></i>Comentario de Reasignación
                            </label>
                            <textarea class="form-control" id="comentarioReasignacion" name="comentario_reasignacion" rows="3" placeholder="Explique por qué se requiere la reasignación..." required></textarea>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            La reasignación quedará registrada en el historial del incidente.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-secondary">
                            <i class="fas fa-sync-alt me-2"></i>Reasignar Personal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Resolver Incidente -->
    <div class="modal fade" id="resolverIncidenteModal" tabindex="-1" aria-labelledby="resolverIncidenteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resolverIncidenteModalLabel">
                        <i class="fas fa-check me-2"></i>Resolver Incidente
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="IncidenteControlador.php" id="formResolverIncidente">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="resolverIncidente">
                        <input type="hidden" name="id_incidente" id="resolverIncidenteId">

                        <div class="text-center mb-3">
                            <i class="fas fa-question-circle fa-3x text-warning mb-2"></i>
                            <h6>¿Está seguro que desea marcar este incidente como resuelto?</h6>
                            <p class="text-muted mb-0">Incidente: <strong id="descripcionIncidenteResolver"></strong></p>
                        </div>

                        <div class="mb-3">
                            <label for="observacionesResolucion" class="form-label">
                                <i class="fas fa-sticky-note text-secondary me-2"></i>Observaciones de Resolución
                            </label>
                            <textarea class="form-control" id="observacionesResolucion" name="observaciones" rows="3" placeholder="Describa cómo se resolvió el incidente..." required></textarea>
                        </div>

                        <div class="alert alert-success">
                            <i class="fas fa-info-circle me-2"></i>
                            Al resolver el incidente, se cerrará el caso y se registrará en el historial.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check me-2"></i>Marcar como Resuelto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Ver Historial -->
    <div class="modal fade" id="verHistorialModal" tabindex="-1" aria-labelledby="verHistorialModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="verHistorialModalLabel">
                        <i class="fas fa-history me-2"></i>Ver Historial del Incidente
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="GET" action="IncidenteControlador.php" id="formVerHistorial">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="verHistorialIncidente">
                        <input type="hidden" name="id_incidente" id="verHistorialId">

                        <div class="text-center">
                            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                            <h6 class="text-warning">Redirección</h6>
                            <p class="mb-3">Está a punto de ver el historial completo de este incidente.</p>
                            <p class="text-muted mt-3">¿Desea continuar?</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-eye me-2"></i>Continuar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Cancelar Incidente -->
    <div class="modal fade" id="cancelarIncidenteModal" tabindex="-1" aria-labelledby="cancelarIncidenteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelarIncidenteModalLabel">
                        <i class="fas fa-times me-2"></i>Cancelar Incidente
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="IncidenteControlador.php" id="formCancelarIncidente">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="cancelarIncidente">
                        <input type="hidden" name="id_incidente" id="cancelarIncidenteId">

                        <div class="text-center mb-3">
                            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-2"></i>
                            <h6>¿Está seguro que desea cancelar este incidente?</h6>
                            <p class="text-muted mb-0">Incidente: <strong id="descripcionIncidenteCancelar"></strong></p>
                        </div>

                        <div class="mb-3">
                            <label for="motivoCancelacion" class="form-label">
                                <i class="fas fa-sticky-note text-secondary me-2"></i>Motivo de Cancelación
                            </label>
                            <textarea class="form-control" id="motivoCancelacion" name="motivo" rows="3" placeholder="Especifique el motivo de la cancelación..." required></textarea>
                        </div>

                        <div class="alert alert-danger">
                            <i class="fas fa-info-circle me-2"></i>
                            Esta acción no se puede deshacer. El incidente será marcado como cancelado.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-times me-2"></i>Cancelar Incidente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Incluir DataTables CSS y JS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

    <!-- Script para DataTable y funcionalidades -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar DataTable
            var tabla = $('#tablaIncidentes').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                responsive: false,
                scrollX: false,
                autoWidth: false,
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                pageLength: 10,
                lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
                order: [[0, 'desc']],
                columnDefs: [
                    {
                        orderable: false,
                        targets: [8] // Columna de opciones no ordenable
                    },
                    {
                        searchable: false,
                        targets: [8] // Columna de opciones no buscable
                    },
                    {
                        width: 'auto',
                        targets: '_all'
                    }
                ],
                initComplete: function() {
                    // Personalizar el buscador
                    $('.dataTables_filter input').addClass('form-control form-control-sm');
                    $('.dataTables_filter input').attr('placeholder', 'Buscar...');

                    // Personalizar el selector de cantidad de registros
                    $('.dataTables_length select').addClass('form-select form-select-sm');
                }
            });

            // Cargar datos en el modal de editar incidente
            const editarIncidenteModal = document.getElementById('editarIncidenteModal');
            editarIncidenteModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const idIncidente = button.getAttribute('data-id');
                const descripcion = button.getAttribute('data-descripcion');
                const descripcionDetallada = button.getAttribute('data-descripcion-detallada');
                const costo = button.getAttribute('data-costo');
                const estado = button.getAttribute('data-estado');

                // Llenar el formulario con los datos actuales
                document.getElementById('editIdIncidente').value = idIncidente;
                document.getElementById('editDescripcion').value = descripcion;
                document.getElementById('editDescripcionDetallada').value = descripcionDetallada;
                document.getElementById('editCosto').value = costo;
                document.getElementById('editEstado').value = estado;

                console.log('Datos cargados:', {
                    idIncidente,
                    descripcion,
                    descripcionDetallada,
                    costo,
                    estado
                });
            });

            // Cargar datos en el modal de cambiar tipo
            const cambiarTipoModal = document.getElementById('cambiarTipoModal');
            cambiarTipoModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const idIncidente = button.getAttribute('data-id');
                const tipoActual = button.getAttribute('data-tipo');
                const descripcion = button.getAttribute('data-descripcion');

                document.getElementById('cambiarTipoId').value = idIncidente;
                document.getElementById('descripcionIncidenteCambiarTipo').textContent = descripcion;
                document.getElementById('nuevoTipo').value = tipoActual;

                // Mostrar/ocultar campo de costo según el tipo
                toggleCostoExterno(tipoActual);
            });

            // Mostrar/ocultar campo de costo según tipo seleccionado
            document.getElementById('nuevoTipo').addEventListener('change', function() {
                toggleCostoExterno(this.value);
            });

            function toggleCostoExterno(tipo) {
                const costoContainer = document.getElementById('costoExternoContainer');
                if (tipo === 'externo') {
                    costoContainer.style.display = 'block';
                } else {
                    costoContainer.style.display = 'none';
                }
            }

            // Cargar datos en el modal de asignar personal
            const asignarPersonalModal = document.getElementById('asignarPersonalModal');
            asignarPersonalModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const idIncidente = button.getAttribute('data-id');
                const descripcion = button.getAttribute('data-descripcion');

                document.getElementById('asignarIncidenteId').value = idIncidente;
                document.getElementById('descripcionIncidenteAsignar').textContent = descripcion;
            });

            // Cargar datos en el modal de reasignar personal
            const reasignarPersonalModal = document.getElementById('reasignarPersonalModal');
            reasignarPersonalModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const idIncidente = button.getAttribute('data-id');
                const descripcion = button.getAttribute('data-descripcion');
                const personalActual = button.getAttribute('data-personal-actual');

                document.getElementById('reasignarIncidenteId').value = idIncidente;
                document.getElementById('descripcionIncidenteReasignar').textContent = descripcion;
                document.getElementById('personalActualId').value = personalActual;
            });

            // Cargar datos en el modal de resolver incidente
            const resolverIncidenteModal = document.getElementById('resolverIncidenteModal');
            resolverIncidenteModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const idIncidente = button.getAttribute('data-id');
                const descripcion = button.getAttribute('data-descripcion');

                document.getElementById('resolverIncidenteId').value = idIncidente;
                document.getElementById('descripcionIncidenteResolver').textContent = descripcion;
            });

            // Cargar datos en el modal de ver historial
            const verHistorialModal = document.getElementById('verHistorialModal');
            verHistorialModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const idIncidente = button.getAttribute('data-id');
                const descripcion = button.getAttribute('data-descripcion');

                document.getElementById('verHistorialId').value = idIncidente;
                document.getElementById('verHistorialModalLabel').innerHTML = `<i class="fas fa-history me-2"></i>Historial - ${descripcion.substring(0, 30)}...`;
            });

            // Cargar datos en el modal de cancelar incidente
            const cancelarIncidenteModal = document.getElementById('cancelarIncidenteModal');
            cancelarIncidenteModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const idIncidente = button.getAttribute('data-id');
                const descripcion = button.getAttribute('data-descripcion');

                document.getElementById('cancelarIncidenteId').value = idIncidente;
                document.getElementById('descripcionIncidenteCancelar').textContent = descripcion;
            });

            // Configurar envío de formularios para cerrar modales automáticamente
            const forms = document.querySelectorAll('#formEditarIncidente, #formCambiarTipo, #formAsignarPersonal, #formReasignarPersonal, #formResolverIncidente, #formVerHistorial, #formCancelarIncidente');
            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    const modal = bootstrap.Modal.getInstance(this.closest('.modal'));
                    if (modal) {
                        modal.hide();
                    }
                });
            });

            // Auto-ocultar alertas después de 5 segundos
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 10000);
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

        .table-container {
            width: 100%;
            overflow-x: hidden;
        }

        /* Estilos para DataTable */
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 0.375rem 0.75rem;
            margin-bottom: 1rem;
        }

        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            margin: 0 2px;
            padding: 0.375rem 0.75rem;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--azul-oscuro);
            border-color: var(--azul-oscuro);
            color: white !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #e9ecef;
            border-color: #dee2e6;
        }

        #tablaIncidentes {
            width: 100% !important;
            table-layout: auto;
        }

        .dataTables_scrollBody {
            overflow-x: hidden !important;
        }

        /* Estilos para las estadísticas */
        .content-box.text-center {
            transition: transform 0.2s;
        }

        .content-box.text-center:hover {
            transform: translateY(-5px);
        }

        .content-box.text-center .fa-2x {
            margin-bottom: 10px;
        }

        .content-box.text-center h4 {
            font-weight: bold;
            color: var(--azul-oscuro);
        }

        /* Indicador de reasignación */
        .text-danger small {
            font-size: 0.7rem;
        }
    </style>

<?php include("../../includes/footer.php"); ?>
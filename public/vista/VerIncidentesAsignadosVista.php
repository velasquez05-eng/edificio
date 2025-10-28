<?php include("../../includes/header.php"); ?>

    <!-- Page Header -->
    <div class="page-header fade-in">
        <div class="page-title">
            <h1>Incidentes Asignados</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Incidentes Asignados</li>
                </ol>
            </nav>
        </div>
        <div class="page-actions">
            <span class="badge bg-primary fs-6">
                <i class="fas fa-user-tie me-1"></i>
                <?php echo htmlspecialchars($_SESSION['nombre_completo'] ?? 'Personal'); ?>
            </span>
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

    <!-- Estadísticas -->
    <div class="row fade-in mb-4">
        <div class="col-md-4">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-tasks fa-2x text-warning mb-2"></i>
                    <h4 class="mb-1"><?php echo $numeroIncidentesAsignados; ?></h4>
                    <p class="text-muted mb-0">Pendientes</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                    <h4 class="mb-1"><?php echo $numeroIncidentesAtendidos; ?></h4>
                    <p class="text-muted mb-0">Atendidos</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-list-alt fa-2x text-primary mb-2"></i>
                    <h4 class="mb-1"><?php echo $numeroIncidentesAsignados + $numeroIncidentesAtendidos; ?></h4>
                    <p class="text-muted mb-0">Total</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Pestañas -->
    <div class="row fade-in">
        <div class="col-12">
            <div class="content-box">
                <div class="content-box-header">
                    <ul class="nav nav-tabs card-header-tabs" id="incidentesTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="asignados-tab" data-bs-toggle="tab" data-bs-target="#asignados" type="button" role="tab">
                                <i class="fas fa-clock me-2"></i>Pendientes
                                <span class="badge bg-warning ms-2"><?php echo count($incidentesAsignados); ?></span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="atendidos-tab" data-bs-toggle="tab" data-bs-target="#atendidos" type="button" role="tab">
                                <i class="fas fa-check-circle me-2"></i>Atendidos
                                <span class="badge bg-success ms-2"><?php echo count($incidentesAtendidos); ?></span>
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="content-box-body">
                    <div class="tab-content" id="incidentesTabsContent">

                        <!-- Pestaña de Incidentes Asignados -->
                        <div class="tab-pane fade show active" id="asignados" role="tabpanel">
                            <?php if (empty($incidentesAsignados)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                    <h5 class="text-success">¡Excelente trabajo!</h5>
                                    <p class="text-muted">No tienes incidentes pendientes.</p>
                                </div>
                            <?php else: ?>
                                <div class="table-container">
                                    <table id="tablaAsignados" class="table table-hover table-striped">
                                        <thead>
                                        <tr>
                                            <th># ID</th>
                                            <th>Descripción</th>
                                            <th>Ubicación</th>
                                            <th>Residente</th>
                                            <th>Fecha Asignación</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($incidentesAsignados as $incidente): ?>
                                            <tr>
                                                <td><strong><?php echo htmlspecialchars($incidente['id_incidente']); ?></strong></td>
                                                <td>
                                                    <div class="fw-bold"><?php echo htmlspecialchars($incidente['descripcion']); ?></div>
                                                    <?php if (!empty($incidente['descripcion_detallada'])): ?>
                                                        <small class="text-muted"><?php echo substr(htmlspecialchars($incidente['descripcion_detallada']), 0, 80); ?>...</small>
                                                    <?php endif; ?>
                                                    <?php if ($incidente['requiere_reasignacion']): ?>
                                                        <div class="mt-1">
                                                            <span class="badge bg-danger">
                                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                                                Solicita Reasignación
                                                            </span>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($incidente['id_area']) && !empty($incidente['nombre_area'])): ?>
                                                        <span class="badge bg-warning">
                                                            <i class="fas fa-map-marker-alt me-1"></i>
                                                            <?php echo htmlspecialchars($incidente['nombre_area']); ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-info">
                                                            <i class="fas fa-building me-1"></i>
                                                            Depto. <?php echo htmlspecialchars($incidente['numero_departamento']); ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <i class="fas fa-user text-primary me-2"></i>
                                                    <?php echo htmlspecialchars($incidente['nombre_residente']); ?>
                                                </td>
                                                <td>
                                                    <i class="fas fa-calendar text-success me-2"></i>
                                                    <?php echo date('d/m/Y H:i', strtotime($incidente['fecha_asignacion'])); ?>
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
                                                        default:
                                                            $badge_class = 'bg-secondary';
                                                            $icon = 'fa-question-circle';
                                                    }
                                                    ?>
                                                    <span class="badge <?php echo $badge_class; ?>">
                                                        <i class="fas <?php echo $icon; ?> me-1"></i>
                                                        <?php echo ucfirst(str_replace('_', ' ', $incidente['estado'])); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group-vertical" role="group">
                                                        <?php if (empty($incidente['fecha_atencion'])): ?>
                                                            <!-- Solo mostrar botón INICIAR cuando no se ha iniciado -->
                                                            <button class="btn btn-info btn-sm mb-1"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#iniciarAtencionModal"
                                                                    data-id="<?php echo $incidente['id_incidente']; ?>"
                                                                    data-descripcion="<?php echo htmlspecialchars($incidente['descripcion']); ?>"
                                                                    title="Iniciar atención del incidente">
                                                                <i class="fas fa-play me-1"></i>Iniciar
                                                            </button>
                                                        <?php else: ?>
                                                            <!-- Mostrar ACTUALIZAR, REASIGNAR y RESOLVER cuando ya se inició -->
                                                            <button class="btn btn-warning btn-sm mb-1"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#actualizarProgresoModal"
                                                                    data-id="<?php echo $incidente['id_incidente']; ?>"
                                                                    data-descripcion="<?php echo htmlspecialchars($incidente['descripcion']); ?>"
                                                                    data-observaciones="<?php echo htmlspecialchars($incidente['observaciones_asignacion'] ?? ''); ?>"
                                                                    title="Actualizar progreso">
                                                                <i class="fas fa-edit me-1"></i>Progreso
                                                            </button>

                                                            <!-- Botón Solicitar Reasignación -->
                                                            <button class="btn btn-secondary btn-sm mb-1"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#solicitarReasignacionModal"
                                                                    data-id="<?php echo $incidente['id_incidente']; ?>"
                                                                    data-descripcion="<?php echo htmlspecialchars($incidente['descripcion']); ?>"
                                                                    title="Solicitar reasignación">
                                                                <i class="fas fa-sync-alt me-1"></i>Reasignar
                                                            </button>

                                                            <button class="btn btn-success btn-sm mb-1"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#resolverIncidenteModal"
                                                                    data-id="<?php echo $incidente['id_incidente']; ?>"
                                                                    data-descripcion="<?php echo htmlspecialchars($incidente['descripcion']); ?>"
                                                                    data-tipo="<?php echo $incidente['tipo']; ?>"
                                                                    title="Marcar como resuelto">
                                                                <i class="fas fa-check me-1"></i>Resolver
                                                            </button>
                                                        <?php endif; ?>

                                                        <!-- Botón VER HISTORIAL - Siempre visible después de iniciar -->
                                                        <?php if (!empty($incidente['fecha_atencion'])): ?>
                                                            <a href="IncidenteControlador.php?action=verHistorialIncidente&id_incidente=<?php echo $incidente['id_incidente']; ?>"
                                                               class="btn btn-primary btn-sm"
                                                               title="Ver historial del incidente">
                                                                <i class="fas fa-history me-1"></i>Historial
                                                            </a>
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

                        <!-- Pestaña de Incidentes Atendidos -->
                        <div class="tab-pane fade" id="atendidos" role="tabpanel">
                            <?php if (empty($incidentesAtendidos)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No hay incidentes atendidos.</p>
                                </div>
                            <?php else: ?>
                                <div class="table-container">
                                    <table id="tablaAtendidos" class="table table-hover table-striped">
                                        <thead>
                                        <tr>
                                            <th># ID</th>
                                            <th>Descripción</th>
                                            <th>Ubicación</th>
                                            <th>Residente</th>
                                            <th>Fecha Atención</th>
                                            <th>Estado</th>
                                            <th>Acción</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($incidentesAtendidos as $incidente): ?>
                                            <tr>
                                                <td><strong><?php echo $incidente['id_incidente']; ?></strong></td>
                                                <td><?php echo htmlspecialchars($incidente['descripcion']); ?></td>
                                                <td>
                                                    <?php if (!empty($incidente['id_area']) && !empty($incidente['nombre_area'])): ?>
                                                        <span class="badge bg-warning">
                                                            <i class="fas fa-map-marker-alt me-1"></i>
                                                            <?php echo htmlspecialchars($incidente['nombre_area']); ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-info">
                                                            <i class="fas fa-building me-1"></i>
                                                            Depto. <?php echo htmlspecialchars($incidente['numero_departamento']); ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <i class="fas fa-user text-primary me-2"></i>
                                                    <?php echo htmlspecialchars($incidente['nombre_residente']); ?>
                                                </td>
                                                <td>
                                                    <i class="fas fa-calendar-check text-success me-2"></i>
                                                    <?php echo date('d/m/Y H:i', strtotime($incidente['fecha_atencion'])); ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check-circle me-1"></i>
                                                        <?php echo ucfirst(str_replace('_', ' ', $incidente['estado'])); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="IncidenteControlador.php?action=verHistorialIncidente&id_incidente=<?php echo $incidente['id_incidente']; ?>"
                                                       class="btn btn-primary btn-sm"
                                                       title="Ver historial">
                                                        <i class="fas fa-history"></i>
                                                    </a>
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
        </div>
    </div>

    <!-- Modal Iniciar Atención -->
    <div class="modal fade" id="iniciarAtencionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-play me-2"></i>Iniciar Atención
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="IncidenteControlador.php">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="iniciarAtencion">
                        <input type="hidden" name="id_incidente" id="iniciarAtencionId">

                        <div class="text-center mb-3">
                            <i class="fas fa-play-circle fa-3x text-info mb-2"></i>
                            <p>Iniciar atención del incidente: <strong id="descripcionIncidenteIniciar"></strong></p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Observaciones Iniciales</label>
                            <textarea class="form-control" name="observaciones" rows="4" placeholder="Describa el plan de trabajo, materiales necesarios, tiempo estimado..." required></textarea>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Al iniciar la atención, el estado cambiará a "En Proceso" y podrás actualizar el progreso.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-info">Iniciar Atención</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Actualizar Progreso -->
    <div class="modal fade" id="actualizarProgresoModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>Actualizar Progreso
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="IncidenteControlador.php">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="actualizarProgresoPersonal">
                        <input type="hidden" name="id_incidente" id="actualizarProgresoId">

                        <div class="text-center mb-3">
                            <i class="fas fa-tasks fa-3x text-warning mb-2"></i>
                            <p>Actualizar progreso del incidente: <strong id="descripcionIncidenteProgreso"></strong></p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Observaciones de Progreso</label>
                            <textarea class="form-control" name="observaciones" rows="4" placeholder="Describa el progreso realizado, materiales utilizados, tiempo empleado, próximos pasos..." required></textarea>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle me-2"></i>
                            Esta actualización quedará registrada en el historial del incidente.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning">Actualizar Progreso</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Solicitar Reasignación -->
    <div class="modal fade" id="solicitarReasignacionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-sync-alt me-2"></i>Solicitar Reasignación
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="IncidenteControlador.php">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="solicitarReasignacion">
                        <input type="hidden" name="id_incidente" id="solicitarReasignacionId">

                        <div class="text-center mb-3">
                            <i class="fas fa-sync-alt fa-3x text-secondary mb-2"></i>
                            <h6>Solicitar Reasignación de Incidente</h6>
                            <p>Incidente: <strong id="descripcionIncidenteReasignacion"></strong></p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Observaciones Técnicas</label>
                            <textarea class="form-control" name="observaciones" rows="3" placeholder="Describa el diagnóstico realizado, problemas identificados, trabajo completado hasta el momento..." required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Comentario de Reasignación</label>
                            <textarea class="form-control" name="comentario_reasignacion" rows="3" placeholder="Explique por qué requiere la reasignación, qué tipo de especialista se necesita, riesgos identificados..." required></textarea>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Importante:</strong> Al solicitar reasignación, el incidente será marcado para revisión del administrador y podrá ser reasignado a otro personal especializado.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-secondary">
                            <i class="fas fa-sync-alt me-1"></i>Solicitar Reasignación
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Resolver Incidente -->
    <div class="modal fade" id="resolverIncidenteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-check me-2"></i>Resolver Incidente
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="IncidenteControlador.php">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="resolverIncidentePersonal">
                        <input type="hidden" name="id_incidente" id="resolverIncidenteId">

                        <div class="text-center mb-3">
                            <i class="fas fa-question-circle fa-3x text-success mb-2"></i>
                            <h6>¿Está seguro que ha resuelto completamente este incidente?</h6>
                            <p>Incidente: <strong id="descripcionIncidenteResolver"></strong></p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Observaciones Finales</label>
                            <textarea class="form-control" name="observaciones_finales" rows="4" placeholder="Describa el trabajo realizado, solución aplicada, materiales utilizados, recomendaciones..." required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Costo (si aplica)</label>
                            <input type="number" class="form-control" name="costo_externo" min="0" step="0.01" placeholder="0.00">
                            <div class="form-text">Ingrese el costo solo si se requirió contratación externa</div>
                        </div>

                        <div class="alert alert-success">
                            <i class="fas fa-info-circle me-2"></i>
                            El incidente se marcará como resuelto y aparecerá en el historial de atendidos.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Resolver Incidente</button>
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
            // Inicializar DataTable para incidentes asignados
            if (document.getElementById('tablaAsignados')) {
                $('#tablaAsignados').DataTable({
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                    },
                    responsive: false,
                    scrollX: false,
                    autoWidth: false,
                    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                    pageLength: 10,
                    lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
                    order: [[4, 'desc']], // Ordenar por fecha de asignación descendente
                    columnDefs: [
                        {
                            orderable: false,
                            targets: [6] // Columna de acciones no ordenable
                        },
                        {
                            searchable: false,
                            targets: [6] // Columna de acciones no buscable
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
            }

            // Inicializar DataTable para incidentes atendidos
            if (document.getElementById('tablaAtendidos')) {
                $('#tablaAtendidos').DataTable({
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                    },
                    responsive: false,
                    scrollX: false,
                    autoWidth: false,
                    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                    pageLength: 10,
                    lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
                    order: [[4, 'desc']], // Ordenar por fecha de atención descendente
                    columnDefs: [
                        {
                            orderable: false,
                            targets: [6] // Columna de acciones no ordenable
                        },
                        {
                            searchable: false,
                            targets: [6] // Columna de acciones no buscable
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
            }

            // Modal Iniciar Atención
            const iniciarAtencionModal = document.getElementById('iniciarAtencionModal');
            if (iniciarAtencionModal) {
                iniciarAtencionModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    document.getElementById('iniciarAtencionId').value = button.getAttribute('data-id');
                    document.getElementById('descripcionIncidenteIniciar').textContent = button.getAttribute('data-descripcion');
                });
            }

            // Modal Actualizar Progreso
            const actualizarProgresoModal = document.getElementById('actualizarProgresoModal');
            if (actualizarProgresoModal) {
                actualizarProgresoModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    document.getElementById('actualizarProgresoId').value = button.getAttribute('data-id');
                    document.getElementById('descripcionIncidenteProgreso').textContent = button.getAttribute('data-descripcion');
                });
            }

            // Modal Solicitar Reasignación
            const solicitarReasignacionModal = document.getElementById('solicitarReasignacionModal');
            if (solicitarReasignacionModal) {
                solicitarReasignacionModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    document.getElementById('solicitarReasignacionId').value = button.getAttribute('data-id');
                    document.getElementById('descripcionIncidenteReasignacion').textContent = button.getAttribute('data-descripcion');
                });
            }

            // Modal Resolver Incidente
            const resolverIncidenteModal = document.getElementById('resolverIncidenteModal');
            if (resolverIncidenteModal) {
                resolverIncidenteModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    document.getElementById('resolverIncidenteId').value = button.getAttribute('data-id');
                    document.getElementById('descripcionIncidenteResolver').textContent = button.getAttribute('data-descripcion');
                });
            }

            // Auto-ocultar alertas
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
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        .btn-group-vertical .btn {
            margin-bottom: 2px;
        }
        .badge.bg-warning {
            background-color: #ffc107 !important;
            color: #212529;
        }
        .badge.bg-danger {
            background-color: #dc3545 !important;
            color: white;
        }
        .nav-tabs .nav-link.active {
            font-weight: 600;
            color: #0d6efd;
            border-bottom: 3px solid #0d6efd;
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

        .table-container {
            width: 100%;
            overflow-x: hidden;
        }

        #tablaAsignados, #tablaAtendidos {
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
        .badge.bg-danger {
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
        }
    </style>

<?php include("../../includes/footer.php"); ?>
<?php include("../../includes/header.php"); ?>

    <!-- Page Header -->
    <div class="page-header fade-in">
        <div class="page-title">
            <h1>Cargos Fijos</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item"><a href="#">Mantenimiento</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Cargos Fijos</li>
                </ol>
            </nav>
        </div>
        <div class="page-actions">
            <a href="CargosFijosControlador.php?action=formularioCrearCargo" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>Nuevo Cargo
            </a>
            <a href="CargosFijosControlador.php?action=vistaGenerarConceptos" class="btn btn-primary">
                <i class="fas fa-cog me-2"></i>Generar Conceptos
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

    <!-- Estadísticas -->
    <div class="row fade-in mb-4">
        <div class="col-md-3">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-list fa-2x text-primary mb-2"></i>
                    <h4 class="mb-1"><?php echo $estadisticas['total_cargos'] ?? 0; ?></h4>
                    <p class="text-muted mb-0">Total Cargos</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                    <h4 class="mb-1"><?php echo $estadisticas['cargos_activos'] ?? 0; ?></h4>
                    <p class="text-muted mb-0">Activos</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-times-circle fa-2x text-warning mb-2"></i>
                    <h4 class="mb-1"><?php echo $estadisticas['cargos_inactivos'] ?? 0; ?></h4>
                    <p class="text-muted mb-0">Inactivos</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-money-bill-wave fa-2x text-info mb-2"></i>
                    <h4 class="mb-1">Bs. <?php echo number_format($estadisticas['monto_mensual_total'] ?? 0, 2); ?></h4>
                    <p class="text-muted mb-0">Total Mensual</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Cargos Fijos -->
    <div class="row fade-in">
        <div class="col-12">
            <div class="content-box">
                <div class="content-box-header d-flex justify-content-between align-items-center">
                    <h5>Lista de Cargos Fijos</h5>
                    <span class="badge bg-primary"><?php echo count($cargos); ?> cargos</span>
                </div>
                <div class="content-box-body">
                    <?php if (empty($cargos)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-money-bill-wave fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No hay cargos fijos registrados</p>
                            <a href="CargosFijosControlador.php?action=formularioCrearCargo" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Crear Primer Cargo
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-container">
                            <table id="tablaCargosFijos" class="table table-hover table-striped">
                                <thead>
                                <tr>
                                    <th># ID</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Monto (Bs.)</th>
                                    <th>Estado</th>
                                    <th>Fecha Creación</th>
                                    <th>Opciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($cargos as $cargo): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($cargo['id_cargo']); ?></strong></td>
                                        <td>
                                            <i class="fas fa-tag text-primary me-2"></i>
                                            <?php echo htmlspecialchars($cargo['nombre_cargo']); ?>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($cargo['descripcion'] ?: 'Sin descripción'); ?>
                                        </td>
                                        <td>
                                            <i class="fas fa-money-bill-wave text-success me-2"></i>
                                            Bs. <?php echo number_format($cargo['monto'], 2); ?>
                                        </td>
                                        <td>
                                            <?php
                                            $badge_class = $cargo['estado'] == 'activo' ? 'bg-success' : 'bg-warning';
                                            $icon = $cargo['estado'] == 'activo' ? 'fa-check-circle' : 'fa-times-circle';
                                            ?>
                                            <span class="badge <?php echo $badge_class; ?>">
                                                <i class="fas <?php echo $icon; ?> me-1"></i>
                                                <?php echo ucfirst(htmlspecialchars($cargo['estado'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>
                                                <?php echo date('d/m/Y', strtotime($cargo['fecha_creacion'])); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-warning btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editarCargoModal"
                                                        data-id="<?php echo htmlspecialchars($cargo['id_cargo']); ?>"
                                                        data-nombre="<?php echo htmlspecialchars($cargo['nombre_cargo']); ?>"
                                                        data-descripcion="<?php echo htmlspecialchars($cargo['descripcion']); ?>"
                                                        data-monto="<?php echo htmlspecialchars($cargo['monto']); ?>"
                                                        data-estado="<?php echo htmlspecialchars($cargo['estado']); ?>"
                                                        title="Editar cargo">
                                                    <i class="fas fa-edit"></i>
                                                </button>

                                                <?php if ($cargo['estado'] == 'activo'): ?>
                                                    <button class="btn btn-secondary btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#cambiarEstadoModal"
                                                            data-id="<?php echo htmlspecialchars($cargo['id_cargo']); ?>"
                                                            data-nombre="<?php echo htmlspecialchars($cargo['nombre_cargo']); ?>"
                                                            data-estado="inactivo"
                                                            title="Desactivar cargo">
                                                        <i class="fas fa-pause"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <button class="btn btn-success btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#cambiarEstadoModal"
                                                            data-id="<?php echo htmlspecialchars($cargo['id_cargo']); ?>"
                                                            data-nombre="<?php echo htmlspecialchars($cargo['nombre_cargo']); ?>"
                                                            data-estado="activo"
                                                            title="Activar cargo">
                                                        <i class="fas fa-play"></i>
                                                    </button>
                                                <?php endif; ?>

                                                <button class="btn btn-danger btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#eliminarCargoModal"
                                                        data-id="<?php echo htmlspecialchars($cargo['id_cargo']); ?>"
                                                        data-nombre="<?php echo htmlspecialchars($cargo['nombre_cargo']); ?>"
                                                        title="Eliminar cargo">
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

    <!-- Modal Editar Cargo -->
    <div class="modal fade" id="editarCargoModal" tabindex="-1" aria-labelledby="editarCargoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarCargoModalLabel">
                        <i class="fas fa-edit me-2"></i>Editar Cargo Fijo
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="CargosFijosControlador.php" id="formEditarCargo">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="editarCargo">
                        <input type="hidden" name="id_cargo" id="editIdCargo">

                        <div class="mb-3">
                            <label for="editNombreCargo" class="form-label">
                                <i class="fas fa-tag text-primary me-2"></i>Nombre del Cargo
                            </label>
                            <input type="text" class="form-control" id="editNombreCargo" name="nombre_cargo" required>
                        </div>

                        <div class="mb-3">
                            <label for="editMonto" class="form-label">
                                <i class="fas fa-money-bill-wave text-success me-2"></i>Monto (Bs.)
                            </label>
                            <input type="number" class="form-control" id="editMonto" name="monto" step="0.01" min="0" required>
                        </div>

                        <div class="mb-3">
                            <label for="editDescripcion" class="form-label">
                                <i class="fas fa-align-left text-info me-2"></i>Descripción
                            </label>
                            <textarea class="form-control" id="editDescripcion" name="descripcion" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="editEstado" class="form-label">
                                <i class="fas fa-toggle-on text-success me-2"></i>Estado
                            </label>
                            <select class="form-select" id="editEstado" name="estado" required>
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
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

    <!-- Modal Cambiar Estado -->
    <div class="modal fade" id="cambiarEstadoModal" tabindex="-1" aria-labelledby="cambiarEstadoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cambiarEstadoModalLabel">
                        <i class="fas fa-exchange-alt me-2"></i>Cambiar Estado
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="CargosFijosControlador.php" id="formCambiarEstado">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="cambiarEstadoCargo">
                        <input type="hidden" name="id_cargo" id="cambiarEstadoIdCargo">
                        <input type="hidden" name="estado" id="cambiarEstadoNuevo">

                        <div class="text-center">
                            <i class="fas fa-question-circle fa-3x text-warning mb-3"></i>
                            <h6 id="cambiarEstadoTexto"></h6>
                            <p class="text-muted mb-0">Cargo: <strong id="nombreCargoCambiarEstado"></strong></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-sync-alt me-2"></i>Cambiar Estado
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Eliminar Cargo -->
    <div class="modal fade" id="eliminarCargoModal" tabindex="-1" aria-labelledby="eliminarCargoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eliminarCargoModalLabel">
                        <i class="fas fa-trash me-2"></i>Confirmar Eliminación
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="CargosFijosControlador.php" id="formEliminarCargo">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="eliminarCargo">
                        <input type="hidden" name="id_cargo" id="eliminarCargoId">

                        <div class="text-center">
                            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                            <h6>¿Está seguro que desea eliminar el cargo?</h6>
                            <p class="text-muted mb-0">Cargo: <strong id="nombreCargoEliminar"></strong></p>
                            <p class="text-muted">Esta acción no se puede deshacer.</p>
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

    <!-- Scripts -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar DataTable
            var tabla = $('#tablaCargosFijos').DataTable({
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
                        targets: [6]
                    },
                    {
                        searchable: false,
                        targets: [6]
                    },
                    {
                        width: 'auto',
                        targets: '_all'
                    }
                ],
                initComplete: function() {
                    $('.dataTables_filter input').addClass('form-control form-control-sm');
                    $('.dataTables_filter input').attr('placeholder', 'Buscar...');
                    $('.dataTables_length select').addClass('form-select form-select-sm');
                }
            });

            // Modal Editar Cargo
            const editarCargoModal = document.getElementById('editarCargoModal');
            editarCargoModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                document.getElementById('editIdCargo').value = button.getAttribute('data-id');
                document.getElementById('editNombreCargo').value = button.getAttribute('data-nombre');
                document.getElementById('editDescripcion').value = button.getAttribute('data-descripcion');
                document.getElementById('editMonto').value = button.getAttribute('data-monto');
                document.getElementById('editEstado').value = button.getAttribute('data-estado');
            });

            // Modal Cambiar Estado
            const cambiarEstadoModal = document.getElementById('cambiarEstadoModal');
            cambiarEstadoModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const idCargo = button.getAttribute('data-id');
                const nombre = button.getAttribute('data-nombre');
                const nuevoEstado = button.getAttribute('data-estado');
                const accion = nuevoEstado === 'activo' ? 'activar' : 'desactivar';

                document.getElementById('cambiarEstadoIdCargo').value = idCargo;
                document.getElementById('cambiarEstadoNuevo').value = nuevoEstado;
                document.getElementById('nombreCargoCambiarEstado').textContent = nombre;
                document.getElementById('cambiarEstadoTexto').textContent = `¿Está seguro que desea ${accion} este cargo?`;
            });

            // Modal Eliminar Cargo
            const eliminarCargoModal = document.getElementById('eliminarCargoModal');
            eliminarCargoModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                document.getElementById('eliminarCargoId').value = button.getAttribute('data-id');
                document.getElementById('nombreCargoEliminar').textContent = button.getAttribute('data-nombre');
            });

            // Auto-ocultar alertas
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 10000);
        });
    </script>

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

        .content-box.text-center {
            transition: transform 0.2s;
        }

        .content-box.text-center:hover {
            transform: translateY(-5px);
        }
    </style>

<?php include("../../includes/footer.php"); ?>
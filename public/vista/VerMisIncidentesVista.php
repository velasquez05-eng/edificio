<?php include("../../includes/header.php"); ?>

    <!-- Page Header -->
    <div class="page-header fade-in">
        <div class="page-title">
            <h1>Mis Incidentes Reportados</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Mis Incidentes</li>
                </ol>
            </nav>
        </div>
        <div class="page-actions">
            <a href="IncidenteControlador.php?action=formularioIncidente" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>Reportar Nuevo Incidente
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

    <!-- Estadísticas de Mis Incidentes -->
    <div class="row fade-in mb-4">
        <div class="col-md-3">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                    <h4 class="mb-1"><?php echo $numeroIncidentesPendientes; ?></h4>
                    <p class="text-muted mb-0">Pendientes</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-tasks fa-2x text-info mb-2"></i>
                    <h4 class="mb-1"><?php echo $numeroIncidentesProceso; ?></h4>
                    <p class="text-muted mb-0">En Proceso</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                    <h4 class="mb-1"><?php echo $numeroIncidentesResueltos; ?></h4>
                    <p class="text-muted mb-0">Resueltos</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-list-alt fa-2x text-primary mb-2"></i>
                    <h4 class="mb-1"><?php echo $numeroTotalIncidentes; ?></h4>
                    <p class="text-muted mb-0">Total Reportados</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Mis Incidentes -->
    <div class="row fade-in">
        <div class="col-12">
            <div class="content-box">
                <div class="content-box-header d-flex justify-content-between align-items-center">
                    <h5>Historial de Mis Incidentes</h5>
                    <span class="badge bg-primary"><?php echo count($incidentes); ?> incidentes</span>
                </div>
                <div class="content-box-body">
                    <?php if (empty($incidentes)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-exclamation-triangle fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No has reportado ningún incidente</h5>
                            <p class="text-muted mb-3">Cuando reportes incidentes, aparecerán en esta lista</p>
                            <a href="IncidenteControlador.php?action=formularioIncidente" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Reportar mi primer incidente
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive"> <!-- Cambiado de table-container a table-responsive -->
                            <table id="tablaMisIncidentes" class="table table-hover table-striped">
                                <thead>
                                <tr>
                                    <th># ID</th>
                                    <th>Descripción</th>
                                    <th>Tipo</th>
                                    <th>Área Común</th>
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
                                            <div class="fw-bold"><?php echo htmlspecialchars($incidente['descripcion']); ?></div>
                                            <?php if (!empty($incidente['descripcion_detallada'])): ?>
                                                <small class="text-muted"><?php echo substr(htmlspecialchars($incidente['descripcion_detallada']), 0, 100); ?>...</small>
                                            <?php endif; ?>
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
                                            <?php if (!empty($incidente['id_area']) && !empty($incidente['nombre_area'])): ?>
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-map-marker-alt me-1"></i>
                                                    <?php echo htmlspecialchars($incidente['nombre_area']); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-home me-1"></i>
                                                    Departamento
                                                </span>
                                            <?php endif; ?>
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
                                                <!-- Botón Ver Historial -->
                                                <a href="IncidenteControlador.php?action=verHistorialIncidente&id_incidente=<?php echo htmlspecialchars($incidente['id_incidente']); ?>"
                                                   class="btn btn-primary btn-sm"
                                                   title="Ver historial completo">
                                                    <i class="fas fa-history"></i>
                                                </a>

                                                <?php
                                                // Determinar si se puede cancelar
                                                $puedeCancelar = ($incidente['estado'] != 'resuelto' &&
                                                        $incidente['estado'] != 'cancelado' &&
                                                        empty($incidente['id_area']));
                                                ?>

                                                <?php if ($puedeCancelar): ?>
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
                                                <!-- Si involucra área común, NO se muestra ningún botón de cancelar -->
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
                        <input type="hidden" name="action" value="cancelarIncidenteResidente">
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
            var tabla = $('#tablaMisIncidentes').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                responsive: true,
                pageLength: 10,
                lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
                order: [[0, 'desc']],
                columnDefs: [
                    {
                        orderable: false,
                        targets: [7] // Columna de opciones no ordenable
                    },
                    {
                        searchable: false,
                        targets: [7] // Columna de opciones no buscable
                    }
                ],
                // Configuración para evitar scroll horizontal
                scrollX: false,
                autoWidth: false
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

    <style>
        .table th {
            border-top: none;
            font-weight: 600;
            background-color: #f8f9fa;
        }

        .btn-group .btn {
            margin: 0 2px;
        }

        /* Eliminar scroll horizontal */
        .table-responsive {
            overflow-x: hidden;
        }

        .content-box.text-center {
            transition: transform 0.2s;
        }

        .content-box.text-center:hover {
            transform: translateY(-5px);
        }

        /* Badge para áreas comunes */
        .badge.bg-warning {
            background-color: #ffc107 !important;
            color: #212529;
        }

        /* Asegurar que la tabla ocupe el 100% del ancho disponible */
        #tablaMisIncidentes {
            width: 100% !important;
        }
    </style>

<?php include("../../includes/footer.php"); ?>
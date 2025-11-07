<?php
// public/vista/ListarMedidoresVista.php
include("../../includes/header.php");
?>

    <!-- Page Header -->
    <div class="page-header fade-in">
        <div class="page-title">
            <h1>Historial de Consumo</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item"><a href="ServicioControlador.php">Servicios</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Medidores</li>
                </ol>
            </nav>
        </div>
        <div class="page-actions">
            <a href="ServicioControlador.php?action=listarServicios" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver a Servicios
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

    <!-- Lista de Medidores -->
    <div class="row fade-in">
        <div class="col-12">
            <div class="content-box">
                <div class="content-box-header d-flex justify-content-between align-items-center">
                    <h5>
                        <i class="fas fa-tachometer-alt me-2"></i>Lista de Medidores
                    </h5>
                    <span class="badge bg-primary"><?php echo count($medidores); ?> medidores</span>
                </div>
                <div class="content-box-body">
                    <?php if (empty($medidores)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-tachometer-alt fa-4x text-muted mb-3"></i>
                            <h4 class="text-muted mb-3">No hay medidores registrados</h4>
                            <p class="text-muted">No se encontraron medidores en el sistema.</p>
                            <a href="ServicioControlador.php?action=formularioAsignarServicio" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Asignar Servicio a Departamento
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-container">
                            <table id="tablaMedidores" class="table table-hover table-striped">
                                <thead>
                                <tr>
                                    <th># ID</th>
                                    <th>Código</th>
                                    <th>Servicio</th>
                                    <th>Departamento</th>
                                    <th>Piso</th>
                                    <th>Unidad de Medida</th>
                                    <th>Fecha Instalación</th>
                                    <th>Estado</th>
                                    <th>Opciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($medidores as $medidor): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($medidor['id_medidor']); ?></strong></td>
                                        <td>
                                            <i class="fas fa-barcode text-primary me-2"></i>
                                            <strong><?php echo htmlspecialchars($medidor['codigo']); ?></strong>
                                        </td>
                                        <td>
                                            <i class="fas fa-concierge-bell text-info me-2"></i>
                                            <?php echo htmlspecialchars(ucfirst($medidor['servicio'])); ?>
                                        </td>
                                        <td>
                                            <i class="fas fa-door-open text-warning me-2"></i>
                                            <?php echo htmlspecialchars($medidor['departamento']); ?>
                                        </td>
                                        <td>
                                            <i class="fas fa-layer-group text-secondary me-2"></i>
                                            <?php echo htmlspecialchars($medidor['piso']); ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                <?php echo htmlspecialchars($medidor['unidad_medida']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <i class="fas fa-calendar text-success me-2"></i>
                                            <?php echo date('d/m/Y', strtotime($medidor['fecha_instalacion'])); ?>
                                        </td>
                                        <td>
                                            <?php
                                            $badge_class = '';
                                            $icon = '';
                                            switch($medidor['estado']) {
                                                case 'activo':
                                                    $badge_class = 'bg-success';
                                                    $icon = 'fa-check-circle';
                                                    break;
                                                case 'mantenimiento':
                                                    $badge_class = 'bg-warning';
                                                    $icon = 'fa-tools';
                                                    break;
                                                case 'baja':
                                                    $badge_class = 'bg-secondary';
                                                    $icon = 'fa-ban';
                                                    break;
                                                case 'corte':
                                                    $badge_class = 'bg-danger';
                                                    $icon = 'fa-exclamation-triangle';
                                                    break;
                                                default:
                                                    $badge_class = 'bg-secondary';
                                                    $icon = 'fa-question-circle';
                                            }
                                            ?>
                                            <span class="badge <?php echo $badge_class; ?>">
                                                <i class="fas <?php echo $icon; ?> me-1"></i>
                                                <?php echo ucfirst(htmlspecialchars($medidor['estado'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="ServicioControlador.php?action=verHistorialConsumo&id_medidor=<?php echo $medidor['id_medidor']; ?>"
                                               class="btn btn-info btn-sm" title="Ver historial de consumo">
                                                <i class="fas fa-chart-line me-1"></i>Ver Historial
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

    <!-- Scripts para DataTable -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar DataTable
            var tabla = $('#tablaMedidores').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                responsive: true,
                pageLength: 10,
                order: [[0, 'desc']],
                columnDefs: [
                    {
                        orderable: false,
                        targets: [8] // Columna de opciones no ordenable
                    }
                ]
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

        .btn-sm {
            white-space: nowrap;
        }
    </style>

<?php include("../../includes/footer.php"); ?>









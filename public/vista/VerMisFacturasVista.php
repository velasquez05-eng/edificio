<?php include("../../includes/header.php"); ?>

    <!-- Page Header -->
    <div class="page-header fade-in">
        <div class="page-title">
            <h1>Mis Facturas</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Mis Facturas</li>
                </ol>
            </nav>
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

    <!-- Estadísticas de Facturas -->
<?php
$estadisticas = [
    'total_facturas' => count($facturas),
    'pendientes' => count(array_filter($facturas, function($f) { return $f['estado'] == 'pendiente'; })),
    'pagadas' => count(array_filter($facturas, function($f) { return $f['estado'] == 'pagada'; })),
    'vencidas' => count(array_filter($facturas, function($f) { return $f['estado'] == 'vencida'; }))
];
?>

    <div class="row fade-in mb-4">
        <div class="col-md-3">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-file-invoice fa-2x text-primary mb-2"></i>
                    <h4 class="mb-1"><?php echo $estadisticas['total_facturas']; ?></h4>
                    <p class="text-muted mb-0">Total Facturas</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                    <h4 class="mb-1"><?php echo $estadisticas['pendientes']; ?></h4>
                    <p class="text-muted mb-0">Pendientes</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                    <h4 class="mb-1"><?php echo $estadisticas['pagadas']; ?></h4>
                    <p class="text-muted mb-0">Pagadas</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-exclamation-triangle fa-2x text-danger mb-2"></i>
                    <h4 class="mb-1"><?php echo $estadisticas['vencidas']; ?></h4>
                    <p class="text-muted mb-0">Vencidas</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Mis Facturas -->
    <div class="row fade-in">
        <div class="col-12">
            <div class="content-box">
                <div class="content-box-header d-flex justify-content-between align-items-center">
                    <h5>Mis Facturas</h5>
                    <span class="badge bg-primary"><?php echo count($facturas); ?> facturas</span>
                </div>
                <div class="content-box-body">
                    <?php if (empty($facturas)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-file-invoice-dollar fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No tienes facturas registradas</p>
                        </div>
                    <?php else: ?>
                        <div class="table-container">
                            <table id="tablaFacturas" class="table table-hover table-striped">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th><i class="fas fa-building text-warning me-2"></i>Departamento</th>
                                    <th><i class="fas fa-calendar-day text-success me-2"></i>Emisión</th>
                                    <th><i class="fas fa-calendar-times text-danger me-2"></i>Vencimiento</th>
                                    <th><i class="fas fa-money-bill-wave text-primary me-2"></i>Monto</th>
                                    <th><i class="fas fa-cubes text-secondary me-2"></i>Conceptos</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($facturas as $index => $factura): ?>
                                    <tr>
                                        <td><strong><?php echo $factura['id_factura']; ?></strong></td>
                                        <td>
                                            <strong>D<?php echo htmlspecialchars($factura['departamento']); ?>-P<?php echo htmlspecialchars($factura['piso']); ?></strong>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($factura['fecha_emision'])); ?></td>
                                        <td>
                                            <?php
                                            $fecha_vencimiento = strtotime($factura['fecha_vencimiento']);
                                            $hoy = strtotime('today');
                                            $clase_fecha = ($fecha_vencimiento < $hoy && $factura['estado'] != 'pagada') ? 'text-danger fw-bold' : '';
                                            ?>
                                            <span class="<?php echo $clase_fecha; ?>">
                                                <?php echo date('d/m/Y', $fecha_vencimiento); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <strong>Bs. <?php echo number_format($factura['monto_total'], 2); ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                <i class="fas fa-cube me-1"></i>
                                                <?php echo $factura['cantidad_conceptos']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php
                                            $badge_class = '';
                                            $icon = '';
                                            switch($factura['estado']) {
                                                case 'pagada':
                                                    $badge_class = 'bg-success';
                                                    $icon = 'fa-check-circle';
                                                    break;
                                                case 'pendiente':
                                                    $badge_class = 'bg-warning';
                                                    $icon = 'fa-clock';
                                                    break;
                                                case 'vencida':
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
                                                <?php echo ucfirst(htmlspecialchars($factura['estado'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <!-- Solo botón Ver Factura -->
                                            <button class="btn btn-info btn-sm ver-factura"
                                                    data-id="<?php echo $factura['id_factura']; ?>"
                                                    data-departamento="D<?php echo $factura['departamento']; ?>-P<?php echo $factura['piso']; ?>"
                                                    data-residente="<?php echo htmlspecialchars($factura['residente']); ?>"
                                                    data-monto="Bs. <?php echo number_format($factura['monto_total'], 2); ?>"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#confirmarVerFacturaModal"
                                                    title="Ver factura">
                                                <i class="fas fa-eye"></i> Ver
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

    <!-- Modal Confirmar Ver Factura -->
    <div class="modal fade" id="confirmarVerFacturaModal" tabindex="-1" aria-labelledby="confirmarVerFacturaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmarVerFacturaModalLabel">
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>Confirmar Apertura
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>Advertencia:</strong> Está a punto de abrir la factura en una nueva vista.
                    </div>

                    <div class="factura-info p-3 border rounded">
                        <p><strong>Factura #:</strong> <span id="confirmFacturaNumero"></span></p>
                        <p><strong>Departamento:</strong> <span id="confirmDepartamento"></span></p>
                        <p><strong>Residente:</strong> <span id="confirmResidente"></span></p>
                        <p><strong>Monto Total:</strong> <span id="confirmMonto"></span></p>
                    </div>

                    <p class="mt-3 mb-0 text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Será redirigido a la vista detallada de la factura.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" id="btnConfirmarVerFactura">
                        <i class="fas fa-external-link-alt me-2"></i>Abrir Factura
                    </button>
                </div>
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
            var tabla = $('#tablaFacturas').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                responsive: true,
                dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                pageLength: 10,
                lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
                order: [[0, 'desc']],
                columnDefs: [
                    {
                        orderable: false,
                        targets: [7] // Columna de acciones
                    },
                    {
                        searchable: false,
                        targets: [7] // Columna de acciones
                    }
                ],
                initComplete: function() {
                    $('.dataTables_filter input').addClass('form-control form-control-sm');
                    $('.dataTables_filter input').attr('placeholder', 'Buscar...');
                    $('.dataTables_length select').addClass('form-select form-select-sm');
                }
            });

            // Variables globales
            let facturaParaVer = null;

            // Modal Confirmar Ver Factura
            const confirmarVerFacturaModal = document.getElementById('confirmarVerFacturaModal');
            confirmarVerFacturaModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                facturaParaVer = {
                    id: button.getAttribute('data-id'),
                    departamento: button.getAttribute('data-departamento'),
                    residente: button.getAttribute('data-residente'),
                    monto: button.getAttribute('data-monto')
                };

                // Actualizar información en el modal
                document.getElementById('confirmFacturaNumero').textContent = facturaParaVer.id;
                document.getElementById('confirmDepartamento').textContent = facturaParaVer.departamento;
                document.getElementById('confirmResidente').textContent = facturaParaVer.residente;
                document.getElementById('confirmMonto').textContent = facturaParaVer.monto;
            });

            // Botón confirmar ver factura
            document.getElementById('btnConfirmarVerFactura').addEventListener('click', function() {
                if (facturaParaVer && facturaParaVer.id) {
                    // Redirigir al controlador para ver la factura
                    window.location.href = `../controlador/FacturaControlador.php?action=verFactura&id_factura=${facturaParaVer.id}`;
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

        .btn {
            min-width: 70px;
        }

        .content-box.text-center {
            transition: transform 0.2s;
        }

        .content-box.text-center:hover {
            transform: translateY(-5px);
        }

        .factura-info {
            background-color: #f8f9fa;
            border-left: 4px solid #0d6efd;
        }

        @media (max-width: 768px) {
            .table-container {
                overflow-x: auto;
            }

            .btn {
                min-width: 60px;
                font-size: 0.8rem;
            }
        }
    </style>

<?php include("../../includes/footer.php"); ?>
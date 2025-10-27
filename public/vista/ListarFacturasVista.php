<?php include("../../includes/header.php"); ?>

    <!-- Page Header -->
    <div class="page-header fade-in">
        <div class="page-title">
            <h1>Gestión de Facturas</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Facturas</li>
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

    <!-- Panel de Generación de Facturas -->
    <div class="row fade-in mb-4">
        <div class="col-12">
            <div class="content-box">
                <div class="content-box-header d-flex justify-content-between align-items-center">
                    <h5>Generar Facturas</h5>
                    <i class="fas fa-file-invoice-dollar text-primary"></i>
                </div>
                <div class="content-box-body">
                    <form method="POST" action="../controlador/FacturaControlador.php" class="row g-3">
                        <input type="hidden" name="action" value="generarFacturas">

                        <div class="col-md-6">
                            <label for="mes_facturacion" class="form-label fw-bold">Mes de Facturación:</label>
                            <input type="month"
                                   class="form-control"
                                   id="mes_facturacion"
                                   name="mes_facturacion"
                                   required
                                   min="2024-01"
                                   max="2025-12">
                            <div class="form-text">Seleccione el mes para el cual generar las facturas (formato: YYYY-MM)</div>
                        </div>

                        <div class="col-md-6 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-cog me-2"></i>Generar Facturas
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

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

    <!-- Tabla de Facturas -->
    <div class="row fade-in">
        <div class="col-12">
            <div class="content-box">
                <div class="content-box-header d-flex justify-content-between align-items-center">
                    <h5>Lista de Facturas</h5>
                    <span class="badge bg-primary"><?php echo count($facturas); ?> facturas</span>
                </div>
                <div class="content-box-body">
                    <?php if (empty($facturas)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-file-invoice-dollar fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No hay facturas registradas en el sistema</p>
                        </div>
                    <?php else: ?>
                        <div class="table-container">
                            <table id="tablaFacturas" class="table table-hover table-striped">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th><i class="fas fa-building text-warning me-2"></i>Departamento</th>
                                    <th><i class="fas fa-user text-info me-2"></i>Residente</th>
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
                                        <td><?php echo htmlspecialchars($factura['residente']); ?></td>
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
                                            <div class="btn-group" role="group">
                                                <!-- Ver Detalles -->
                                                <button class="btn btn-info btn-sm ver-detalle"
                                                        data-id="<?php echo $factura['id_factura']; ?>"
                                                        title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </button>

                                                <!-- Exportar PDF -->
                                                <a href="../controlador/FacturaControlador.php?action=exportarPDF&id_factura=<?php echo $factura['id_factura']; ?>"
                                                   class="btn btn-danger btn-sm"
                                                   title="Exportar PDF">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
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

    <!-- Modal Ver Detalles -->
    <div class="modal fade" id="verDetalleModal" tabindex="-1" aria-labelledby="verDetalleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="verDetalleModalLabel">
                        <i class="fas fa-file-invoice me-2"></i>Detalle de Factura
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="detalleFacturaContent">
                    <!-- Contenido cargado por AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Incluir DataTables CSS y JS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">

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
                        targets: [8]
                    },
                    {
                        searchable: false,
                        targets: [8]
                    }
                ],
                initComplete: function() {
                    $('.dataTables_filter input').addClass('form-control form-control-sm');
                    $('.dataTables_filter input').attr('placeholder', 'Buscar...');
                    $('.dataTables_length select').addClass('form-select form-select-sm');
                }
            });

            // Modal Ver Detalle
            const verDetalleModal = document.getElementById('verDetalleModal');
            verDetalleModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const idFactura = button.getAttribute('data-id');

                // Cargar detalles via AJAX
                $.ajax({
                    url: '../controlador/FacturaControlador.php',
                    type: 'POST',
                    data: {
                        action: 'obtenerDetalleFactura',
                        id_factura: idFactura
                    },
                    success: function(response) {
                        const detalle = JSON.parse(response);
                        let contenido = '';

                        if (detalle) {
                            contenido = `
                            <div class="factura-info bg-light p-3 rounded mb-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-2"><strong>Factura #:</strong> ${detalle.factura.id_factura}</p>
                                        <p class="mb-2"><strong>Departamento:</strong> D${detalle.factura.departamento}-P${detalle.factura.piso}</p>
                                        <p class="mb-2"><strong>Residente:</strong> ${detalle.factura.residente}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-2"><strong>Emisión:</strong> ${new Date(detalle.factura.fecha_emision).toLocaleDateString('es-ES')}</p>
                                        <p class="mb-2"><strong>Vencimiento:</strong> ${new Date(detalle.factura.fecha_vencimiento).toLocaleDateString('es-ES')}</p>
                                        <p class="mb-2"><strong>Estado:</strong> <span class="badge ${getBadgeClass(detalle.factura.estado)}">${detalle.factura.estado}</span></p>
                                    </div>
                                </div>
                            </div>

                            <h6 class="mb-3">Conceptos:</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Concepto</th>
                                            <th>Descripción</th>
                                            <th>Cantidad</th>
                                            <th>Monto</th>
                                        </tr>
                                    </thead>
                                    <tbody>`;

                            detalle.conceptos.forEach(concepto => {
                                contenido += `
                                <tr>
                                    <td>${concepto.concepto}</td>
                                    <td>${concepto.descripcion}</td>
                                    <td>${concepto.cantidad}</td>
                                    <td>Bs. ${parseFloat(concepto.monto).toFixed(2)}</td>
                                </tr>`;
                            });

                            contenido += `
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                            <td><strong>Bs. ${parseFloat(detalle.factura.monto_total).toFixed(2)}</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>`;

                            if (detalle.pagos && detalle.pagos.length > 0) {
                                contenido += `
                                <h6 class="mb-3 mt-4">Historial de Pagos:</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Monto</th>
                                                <th>Persona</th>
                                                <th>Observación</th>
                                            </tr>
                                        </thead>
                                        <tbody>`;

                                detalle.pagos.forEach(pago => {
                                    contenido += `
                                    <tr>
                                        <td>${new Date(pago.fecha_pago).toLocaleDateString('es-ES')}</td>
                                        <td>Bs. ${parseFloat(pago.monto_pagado).toFixed(2)}</td>
                                        <td>${pago.persona_pago}</td>
                                        <td>${pago.observacion || '-'}</td>
                                    </tr>`;
                                });

                                contenido += `
                                        </tbody>
                                    </table>
                                </div>`;
                            }
                        } else {
                            contenido = '<div class="alert alert-danger">Error al cargar los detalles de la factura</div>';
                        }

                        document.getElementById('detalleFacturaContent').innerHTML = contenido;
                    },
                    error: function() {
                        document.getElementById('detalleFacturaContent').innerHTML =
                            '<div class="alert alert-danger">Error al cargar los detalles de la factura</div>';
                    }
                });
            });

            // Asignar evento a botones de ver detalle
            document.addEventListener('click', function(e) {
                if (e.target.closest('.ver-detalle')) {
                    const button = e.target.closest('.ver-detalle');
                    const idFactura = button.getAttribute('data-id');
                    button.setAttribute('data-id', idFactura);
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

            function getBadgeClass(estado) {
                switch(estado) {
                    case 'pagada': return 'bg-success';
                    case 'pendiente': return 'bg-warning';
                    case 'vencida': return 'bg-danger';
                    default: return 'bg-secondary';
                }
            }
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

        .content-box.text-center {
            transition: transform 0.2s;
        }

        .content-box.text-center:hover {
            transform: translateY(-5px);
        }

        @media (max-width: 768px) {
            .table-container {
                overflow-x: auto;
            }
        }
    </style>

<?php include("../../includes/footer.php"); ?>
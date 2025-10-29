<?php include("../../includes/header.php"); ?>

    <!-- Page Header -->
    <div class="page-header fade-in">
        <div class="page-title">
            <h1>Mi Historial de Pagos</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item"><a href="../controlador/FacturaControlador.php?action=misFacturas">Mis Facturas</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Historial de Pagos</li>
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

    <!-- Estadísticas de Pagos -->
<?php if (!empty($estadisticas) && $estadisticas['total_pagos'] > 0): ?>
    <div class="row fade-in mb-4">
        <div class="col-md-3">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-receipt fa-2x text-primary mb-2"></i>
                    <h4 class="mb-1"><?php echo $estadisticas['total_pagos']; ?></h4>
                    <p class="text-muted mb-0">Total Pagos</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-money-bill-wave fa-2x text-success mb-2"></i>
                    <h4 class="mb-1">Bs. <?php echo $estadisticas['total_pagado_formateado']; ?></h4>
                    <p class="text-muted mb-0">Total Pagado</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                    <h4 class="mb-1"><?php echo $estadisticas['porcentaje_puntual']; ?>%</h4>
                    <p class="text-muted mb-0">Pagos Puntuales</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-qrcode fa-2x text-info mb-2"></i>
                    <h4 class="mb-1"><?php echo $estadisticas['pagos_qr']; ?></h4>
                    <p class="text-muted mb-0">Pagos QR</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Detalles de Estadísticas -->
    <div class="row fade-in mb-4">
        <div class="col-12">
            <div class="content-box">
                <div class="content-box-header">
                    <h5><i class="fas fa-chart-bar me-2"></i>Resumen de Pagos</h5>
                </div>
                <div class="content-box-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-success rounded p-2 me-3">
                                    <i class="fas fa-check-circle text-white"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0"><?php echo $estadisticas['pagos_puntuales']; ?> Pagos</h6>
                                    <small class="text-muted">Puntuales (<?php echo $estadisticas['porcentaje_puntual']; ?>%)</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-danger rounded p-2 me-3">
                                    <i class="fas fa-exclamation-triangle text-white"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0"><?php echo $estadisticas['pagos_atrasados']; ?> Pagos</h6>
                                    <small class="text-muted">Atrasados (<?php echo $estadisticas['porcentaje_atrasado']; ?>%)</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-info rounded p-2 me-3">
                                    <i class="fas fa-qrcode text-white"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0"><?php echo $estadisticas['pagos_qr']; ?> Pagos</h6>
                                    <small class="text-muted">Con QR (<?php echo $estadisticas['porcentaje_qr']; ?>%)</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary rounded p-2 me-3">
                                    <i class="fas fa-credit-card text-white"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Bs. <?php echo $estadisticas['promedio_pago_formateado']; ?></h6>
                                    <small class="text-muted">Promedio por pago</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

    <!-- Tabla de Historial de Pagos -->
    <div class="row fade-in">
        <div class="col-12">
            <div class="content-box">
                <div class="content-box-header d-flex justify-content-between align-items-center">
                    <h5>Historial de Pagos</h5>
                    <span class="badge bg-primary"><?php echo count($pagos); ?> registros</span>
                </div>
                <div class="content-box-body">
                    <?php if (empty($pagos)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No tienes pagos registrados en el sistema</p>
                            <a href="../controlador/FacturaControlador.php?action=misFacturas" class="btn btn-primary">
                                <i class="fas fa-file-invoice me-2"></i>Ver Mis Facturas
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-container">
                            <table id="tablaPagos" class="table table-hover table-striped">
                                <thead>
                                <tr>
                                    <th># Pago</th>
                                    <th><i class="fas fa-file-invoice text-primary me-2"></i>Factura</th>
                                    <th><i class="fas fa-building text-warning me-2"></i>Departamento</th>
                                    <th><i class="fas fa-money-bill-wave text-success me-2"></i>Monto</th>
                                    <th><i class="fas fa-calendar-day text-info me-2"></i>Fecha Pago</th>
                                    <th><i class="fas fa-clock text-secondary me-2"></i>Puntualidad</th>
                                    <th><i class="fas fa-qrcode text-info me-2"></i>Método</th>
                                    <th>Acciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($pagos as $pago): ?>
                                    <tr>
                                        <td><strong>#<?php echo $pago['id_historial_pago']; ?></strong></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-file-invoice text-primary me-2"></i>
                                                <div>
                                                    <strong>Factura #<?php echo $pago['id_factura']; ?></strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        Emisión: <?php echo $pago['fecha_emision_formateada']; ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <strong>D<?php echo htmlspecialchars($pago['departamento']); ?>-P<?php echo htmlspecialchars($pago['piso']); ?></strong>
                                        </td>
                                        <td>
                                            <div class="text-success">
                                                <strong>Bs. <?php echo $pago['monto_pagado_formateado']; ?></strong>
                                                <br>
                                                <small class="text-muted">
                                                    Total: Bs. <?php echo $pago['monto_factura_formateado']; ?>
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <strong><?php echo $pago['fecha_pago_formateada']; ?></strong>
                                                <br>
                                                <small class="text-muted">
                                                    <?php echo $pago['fecha_pago_hora']; ?>
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($pago['puntual']): ?>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i>
                                                    Puntual
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                    Atrasado
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $pago['tipo_pago'] === 'qr' ? 'info' : 'primary'; ?>">
                                                <i class="fas <?php echo $pago['icono']; ?> me-1"></i>
                                                <?php echo $pago['metodo']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <!-- Ver Detalles del Pago -->
                                                <button class="btn btn-info btn-sm ver-pago"
                                                        data-id="<?php echo $pago['id_historial_pago']; ?>"
                                                        data-factura="<?php echo $pago['id_factura']; ?>"
                                                        data-monto="Bs. <?php echo $pago['monto_pagado_formateado']; ?>"
                                                        data-fecha="<?php echo $pago['fecha_pago_formateada']; ?>"
                                                        data-metodo="<?php echo $pago['metodo']; ?>"
                                                        data-puntualidad="<?php echo $pago['puntual'] ? 'Puntual' : 'Atrasado'; ?>"
                                                        data-observacion="<?php echo htmlspecialchars($pago['observacion']); ?>"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#detallesPagoModal"
                                                        title="Ver detalles del pago">
                                                    <i class="fas fa-eye"></i> Detalles
                                                </button>

                                                <!-- Ver Factura -->
                                                <a href="../controlador/FacturaControlador.php?action=verFactura&id_factura=<?php echo $pago['id_factura']; ?>"
                                                   class="btn btn-outline-primary btn-sm"
                                                   title="Ver factura">
                                                    <i class="fas fa-file-invoice"></i> Factura
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

    <!-- Modal Detalles del Pago -->
    <div class="modal fade" id="detallesPagoModal" tabindex="-1" aria-labelledby="detallesPagoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detallesPagoModalLabel">
                        <i class="fas fa-receipt me-2"></i>Detalles del Pago
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-primary"># Pago:</label>
                                <p class="mb-0" id="modalPagoId">-</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-primary">Factura:</label>
                                <p class="mb-0" id="modalFacturaId">-</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-primary">Monto Pagado:</label>
                                <p class="mb-0 text-success fw-bold" id="modalMonto">-</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-primary">Fecha de Pago:</label>
                                <p class="mb-0" id="modalFecha">-</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-primary">Método:</label>
                                <p class="mb-0" id="modalMetodo">-</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-primary">Puntualidad:</label>
                                <p class="mb-0" id="modalPuntualidad">-</p>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-primary">Observación:</label>
                        <div class="border rounded p-3 bg-light" id="modalObservacion">
                            -
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cerrar
                    </button>
                    <a href="#" class="btn btn-primary" id="btnVerFacturaCompleta">
                        <i class="fas fa-external-link-alt me-2"></i>Ver Factura Completa
                    </a>
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
            var tabla = $('#tablaPagos').DataTable({
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

            // Modal Detalles del Pago
            const detallesPagoModal = document.getElementById('detallesPagoModal');
            detallesPagoModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;

                // Obtener datos del botón
                const pagoData = {
                    id: button.getAttribute('data-id'),
                    factura: button.getAttribute('data-factura'),
                    monto: button.getAttribute('data-monto'),
                    fecha: button.getAttribute('data-fecha'),
                    metodo: button.getAttribute('data-metodo'),
                    puntualidad: button.getAttribute('data-puntualidad'),
                    observacion: button.getAttribute('data-observacion')
                };

                // Actualizar información en el modal
                document.getElementById('modalPagoId').textContent = '#' + pagoData.id;
                document.getElementById('modalFacturaId').textContent = 'Factura #' + pagoData.factura;
                document.getElementById('modalMonto').textContent = pagoData.monto;
                document.getElementById('modalFecha').textContent = pagoData.fecha;
                document.getElementById('modalMetodo').textContent = pagoData.metodo;

                // Aplicar estilo a la puntualidad
                const puntualidadElement = document.getElementById('modalPuntualidad');
                puntualidadElement.textContent = pagoData.puntualidad;
                if (pagoData.puntualidad === 'Puntual') {
                    puntualidadElement.className = 'mb-0 text-success fw-bold';
                } else {
                    puntualidadElement.className = 'mb-0 text-danger fw-bold';
                }

                // Actualizar observación
                document.getElementById('modalObservacion').textContent = pagoData.observacion;

                // Actualizar enlace a factura completa
                document.getElementById('btnVerFacturaCompleta').href =
                    '../controlador/FacturaControlador.php?action=verFactura&id_factura=' + pagoData.factura;
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
            min-width: 80px;
        }

        .content-box.text-center {
            transition: transform 0.2s;
        }

        .content-box.text-center:hover {
            transform: translateY(-5px);
        }

        .badge {
            font-size: 0.75rem;
        }

        @media (max-width: 768px) {
            .table-container {
                overflow-x: auto;
            }

            .btn-group .btn {
                min-width: 60px;
                font-size: 0.8rem;
                padding: 0.25rem 0.5rem;
            }

            .content-box-body .row > div {
                margin-bottom: 1rem;
            }
        }
    </style>

<?php include("../../includes/footer.php"); ?>
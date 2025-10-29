<?php include("../../includes/header.php"); ?>

    <!-- Page Header -->
    <div class="page-header fade-in">
        <div class="page-title">
            <h1>Conceptos Completos del Sistema</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item"><a href="../controlador/FacturaControlador.php?action=listarFacturas">Facturas</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Conceptos Completos</li>
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

    <!-- Estadísticas de Conceptos -->
<?php if (!empty($estadisticas) && $estadisticas['total_conceptos'] > 0): ?>
    <div class="row fade-in mb-4">
        <div class="col-md-3">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-cubes fa-2x text-primary mb-2"></i>
                    <h4 class="mb-1"><?php echo $estadisticas['total_conceptos']; ?></h4>
                    <p class="text-muted mb-0">Total Conceptos</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-money-bill-wave fa-2x text-success mb-2"></i>
                    <h4 class="mb-1">Bs. <?php echo $estadisticas['total_monto_formateado']; ?></h4>
                    <p class="text-muted mb-0">Monto Total</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-users fa-2x text-warning mb-2"></i>
                    <h4 class="mb-1"><?php echo $estadisticas['personas_con_conceptos']; ?></h4>
                    <p class="text-muted mb-0">Personas</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-building fa-2x text-info mb-2"></i>
                    <h4 class="mb-1"><?php echo $estadisticas['departamentos_con_conceptos']; ?></h4>
                    <p class="text-muted mb-0">Departamentos</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Detalles de Estadísticas -->
    <div class="row fade-in mb-4">
        <div class="col-12">
            <div class="content-box">
                <div class="content-box-header">
                    <h5><i class="fas fa-chart-bar me-2"></i>Resumen General de Conceptos</h5>
                </div>
                <div class="content-box-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-warning rounded p-2 me-3">
                                    <i class="fas fa-clock text-white"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0"><?php echo $estadisticas['conceptos_pendientes']; ?> Conceptos</h6>
                                    <small class="text-muted">Pendientes (<?php echo $estadisticas['porcentaje_pendientes']; ?>%)</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-info rounded p-2 me-3">
                                    <i class="fas fa-file-invoice text-white"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0"><?php echo $estadisticas['conceptos_facturados']; ?> Conceptos</h6>
                                    <small class="text-muted">Facturados (<?php echo $estadisticas['porcentaje_facturados']; ?>%)</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-secondary rounded p-2 me-3">
                                    <i class="fas fa-ban text-white"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0"><?php echo $estadisticas['conceptos_cancelados']; ?> Conceptos</h6>
                                    <small class="text-muted">Cancelados (<?php echo $estadisticas['porcentaje_cancelados']; ?>%)</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tipos de Conceptos -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="mb-3"><i class="fas fa-tags me-2"></i>Distribución por Tipo de Concepto</h6>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="bg-primary rounded p-1 me-2">
                                            <i class="fas fa-tint text-white"></i>
                                        </div>
                                        <div>
                                            <small class="fw-bold">Agua: <?php echo $estadisticas['conceptos_agua']; ?> (<?php echo $estadisticas['porcentaje_agua']; ?>%)</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="bg-warning rounded p-1 me-2">
                                            <i class="fas fa-bolt text-white"></i>
                                        </div>
                                        <div>
                                            <small class="fw-bold">Luz: <?php echo $estadisticas['conceptos_luz']; ?> (<?php echo $estadisticas['porcentaje_luz']; ?>%)</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="bg-danger rounded p-1 me-2">
                                            <i class="fas fa-fire text-white"></i>
                                        </div>
                                        <div>
                                            <small class="fw-bold">Gas: <?php echo $estadisticas['conceptos_gas']; ?> (<?php echo $estadisticas['porcentaje_gas']; ?>%)</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="bg-info rounded p-1 me-2">
                                            <i class="fas fa-calendar-alt text-white"></i>
                                        </div>
                                        <div>
                                            <small class="fw-bold">Reservas: <?php echo $estadisticas['conceptos_reserva']; ?> (<?php echo $estadisticas['porcentaje_reserva']; ?>%)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información adicional -->
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="bg-success rounded p-2 me-3">
                                    <i class="fas fa-chart-line text-white"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Bs. <?php echo $estadisticas['promedio_monto_formateado']; ?></h6>
                                    <small class="text-muted">Promedio por concepto</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary rounded p-2 me-3">
                                    <i class="fas fa-calendar text-white"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0"><?php echo $estadisticas['primer_concepto_formateado']; ?></h6>
                                    <small class="text-muted">Primer concepto registrado</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

    <!-- Tabla de Conceptos Completos -->
    <div class="row fade-in">
        <div class="col-12">
            <div class="content-box">
                <div class="content-box-header d-flex justify-content-between align-items-center">
                    <h5>Conceptos Completos del Sistema</h5>
                    <div>
                        <span class="badge bg-primary"><?php echo count($conceptos); ?> registros</span>
                        <button class="btn btn-outline-success btn-sm ms-2" onclick="exportarConceptos()">
                            <i class="fas fa-file-excel me-1"></i>Exportar
                        </button>
                    </div>
                </div>
                <div class="content-box-body">
                    <?php if (empty($conceptos)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-cubes fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No hay conceptos registrados en el sistema</p>
                        </div>
                    <?php else: ?>
                        <div class="table-container">
                            <table id="tablaConceptosCompletos" class="table table-hover table-striped">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th><i class="fas fa-tag me-2"></i>Concepto</th>
                                    <th><i class="fas fa-user me-2"></i>Persona</th>
                                    <th><i class="fas fa-building me-2"></i>Departamento</th>
                                    <th><i class="fas fa-info-circle me-2"></i>Descripción</th>
                                    <th><i class="fas fa-hashtag me-2"></i>Origen</th>
                                    <th><i class="fas fa-money-bill-wave me-2"></i>Monto</th>
                                    <th><i class="fas fa-calendar me-2"></i>Fecha</th>
                                    <th>Estado</th>
                                    <th>Factura</th>
                                    <th>Acciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($conceptos as $concepto): ?>
                                    <tr>
                                        <td><strong>#<?php echo $concepto['id_concepto']; ?></strong></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-<?php echo $concepto['color']; ?> rounded p-2 me-2 text-white">
                                                    <i class="fas <?php echo $concepto['icono']; ?>"></i>
                                                </div>
                                                <div>
                                                    <strong><?php echo $concepto['concepto_texto']; ?></strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        Cant: <?php echo $concepto['cantidad']; ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-user text-info me-2"></i>
                                                <div>
                                                    <strong><?php echo htmlspecialchars($concepto['persona_completa']); ?></strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        CI: <?php echo htmlspecialchars($concepto['ci']); ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($concepto['departamento']): ?>
                                                <span class="badge bg-light text-dark">
                                                    <i class="fas fa-building me-1"></i>
                                                    <?php echo $concepto['departamento_info']; ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Sin departamento</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="concepto-descripcion">
                                                <strong><?php echo htmlspecialchars(substr($concepto['descripcion'], 0, 50)); ?></strong>
                                                <?php if (strlen($concepto['descripcion']) > 50): ?>
                                                    <span class="text-muted">...</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">
                                                <?php echo htmlspecialchars($concepto['origen_info']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="text-success text-end">
                                                <strong>Bs. <?php echo $concepto['monto_formateado']; ?></strong>
                                                <br>
                                                <small class="text-muted">
                                                    Subtotal: Bs. <?php echo $concepto['subtotal_formateado']; ?>
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <strong><?php echo $concepto['fecha_creacion_formateada']; ?></strong>
                                                <br>
                                                <small class="text-muted">
                                                    <?php echo $concepto['fecha_creacion_hora']; ?>
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge <?php echo $concepto['badge_class']; ?>">
                                                <?php echo $concepto['estado_texto']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($concepto['id_factura']): ?>
                                                <span class="badge bg-info">
                                                    <i class="fas fa-file-invoice me-1"></i>
                                                    #<?php echo $concepto['id_factura']; ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-times me-1"></i>
                                                    Sin factura
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <!-- Ver Detalles del Concepto -->
                                                <button class="btn btn-info btn-sm ver-concepto"
                                                        data-id="<?php echo $concepto['id_concepto']; ?>"
                                                        data-concepto="<?php echo htmlspecialchars($concepto['concepto_texto']); ?>"
                                                        data-persona="<?php echo htmlspecialchars($concepto['persona_completa']); ?>"
                                                        data-departamento="<?php echo htmlspecialchars($concepto['departamento_info']); ?>"
                                                        data-descripcion="<?php echo htmlspecialchars($concepto['descripcion']); ?>"
                                                        data-monto="Bs. <?php echo $concepto['monto_formateado']; ?>"
                                                        data-cantidad="<?php echo $concepto['cantidad']; ?>"
                                                        data-subtotal="Bs. <?php echo $concepto['subtotal_formateado']; ?>"
                                                        data-fecha="<?php echo $concepto['fecha_creacion_formateada']; ?>"
                                                        data-estado="<?php echo $concepto['estado_texto']; ?>"
                                                        data-origen="<?php echo htmlspecialchars($concepto['origen_info']); ?>"
                                                        data-origen-detalle="<?php echo htmlspecialchars($concepto['origen_detalle']); ?>"
                                                        data-factura="<?php echo $concepto['id_factura'] ? 'Factura #' . $concepto['id_factura'] : 'Sin factura'; ?>"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#detallesConceptoModal"
                                                        title="Ver detalles del concepto">
                                                    <i class="fas fa-eye"></i>
                                                </button>

                                                <!-- Ver Factura si existe -->
                                                <?php if ($concepto['id_factura']): ?>
                                                    <a href="../controlador/FacturaControlador.php?action=verFactura&id_factura=<?php echo $concepto['id_factura']; ?>"
                                                       class="btn btn-outline-primary btn-sm"
                                                       title="Ver factura">
                                                        <i class="fas fa-file-invoice"></i>
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
            </div>
        </div>
    </div>

    <!-- Modal Detalles del Concepto -->
    <div class="modal fade" id="detallesConceptoModal" tabindex="-1" aria-labelledby="detallesConceptoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detallesConceptoModalLabel">
                        <i class="fas fa-cube me-2"></i>Detalles Completos del Concepto
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-primary"># Concepto:</label>
                                <p class="mb-0" id="modalConceptoId">-</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-primary">Tipo:</label>
                                <p class="mb-0" id="modalConceptoTipo">-</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-primary">Persona:</label>
                                <p class="mb-0" id="modalConceptoPersona">-</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-primary">Departamento:</label>
                                <p class="mb-0" id="modalConceptoDepartamento">-</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-primary">Cantidad:</label>
                                <p class="mb-0" id="modalConceptoCantidad">-</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-primary">Monto Unitario:</label>
                                <p class="mb-0 text-success fw-bold" id="modalConceptoMonto">-</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-primary">Subtotal:</label>
                                <p class="mb-0 text-success fw-bold fs-5" id="modalConceptoSubtotal">-</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-primary">Fecha de Creación:</label>
                                <p class="mb-0" id="modalConceptoFecha">-</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-primary">Estado:</label>
                                <p class="mb-0" id="modalConceptoEstado">-</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-primary">Origen:</label>
                                <p class="mb-0" id="modalConceptoOrigen">-</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-primary">Detalle del Origen:</label>
                        <div class="border rounded p-2 bg-light" id="modalConceptoOrigenDetalle">
                            -
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-primary">Factura:</label>
                        <p class="mb-0" id="modalConceptoFactura">-</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-primary">Descripción Completa:</label>
                        <div class="border rounded p-3 bg-light" id="modalConceptoDescripcion">
                            -
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cerrar
                    </button>
                    <a href="#" class="btn btn-primary" id="btnVerFacturaConcepto">
                        <i class="fas fa-external-link-alt me-2"></i>Ver Factura
                    </a>
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
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <!-- Script para DataTable y funcionalidades -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar DataTable con botones de exportación
            var tabla = $('#tablaConceptosCompletos').DataTable({
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
                buttons: [
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel me-2"></i>Excel',
                        className: 'btn btn-success btn-sm',
                        title: 'Conceptos_Completos_Sistema'
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf me-2"></i>PDF',
                        className: 'btn btn-danger btn-sm',
                        title: 'Conceptos_Completos_Sistema'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print me-2"></i>Imprimir',
                        className: 'btn btn-info btn-sm',
                        title: 'Conceptos_Completos_Sistema'
                    }
                ],
                columnDefs: [
                    {
                        orderable: false,
                        targets: [10] // Columna de acciones
                    },
                    {
                        searchable: false,
                        targets: [10] // Columna de acciones
                    }
                ],
                initComplete: function() {
                    $('.dataTables_filter input').addClass('form-control form-control-sm');
                    $('.dataTables_filter input').attr('placeholder', 'Buscar...');
                    $('.dataTables_length select').addClass('form-select form-select-sm');

                    // Agregar botones de exportación
                    this.api().buttons().container().appendTo('#tablaConceptosCompletos_wrapper .col-md-6:eq(0)');
                }
            });

            // Modal Detalles del Concepto
            const detallesConceptoModal = document.getElementById('detallesConceptoModal');
            detallesConceptoModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;

                // Obtener datos del botón
                const conceptoData = {
                    id: button.getAttribute('data-id'),
                    concepto: button.getAttribute('data-concepto'),
                    persona: button.getAttribute('data-persona'),
                    departamento: button.getAttribute('data-departamento'),
                    descripcion: button.getAttribute('data-descripcion'),
                    monto: button.getAttribute('data-monto'),
                    cantidad: button.getAttribute('data-cantidad'),
                    subtotal: button.getAttribute('data-subtotal'),
                    fecha: button.getAttribute('data-fecha'),
                    estado: button.getAttribute('data-estado'),
                    origen: button.getAttribute('data-origen'),
                    origenDetalle: button.getAttribute('data-origen-detalle'),
                    factura: button.getAttribute('data-factura')
                };

                // Actualizar información en el modal
                document.getElementById('modalConceptoId').textContent = '#' + conceptoData.id;
                document.getElementById('modalConceptoTipo').textContent = conceptoData.concepto;
                document.getElementById('modalConceptoPersona').textContent = conceptoData.persona;
                document.getElementById('modalConceptoDepartamento').textContent = conceptoData.departamento;
                document.getElementById('modalConceptoCantidad').textContent = conceptoData.cantidad;
                document.getElementById('modalConceptoMonto').textContent = conceptoData.monto;
                document.getElementById('modalConceptoSubtotal').textContent = conceptoData.subtotal;
                document.getElementById('modalConceptoFecha').textContent = conceptoData.fecha;
                document.getElementById('modalConceptoEstado').textContent = conceptoData.estado;
                document.getElementById('modalConceptoOrigen').textContent = conceptoData.origen;
                document.getElementById('modalConceptoOrigenDetalle').textContent = conceptoData.origenDetalle;
                document.getElementById('modalConceptoFactura').textContent = conceptoData.factura;
                document.getElementById('modalConceptoDescripcion').textContent = conceptoData.descripcion;

                // Actualizar enlace a factura (si existe)
                const btnVerFactura = document.getElementById('btnVerFacturaConcepto');
                if (conceptoData.factura !== 'Sin factura') {
                    const facturaId = conceptoData.factura.replace('Factura #', '');
                    btnVerFactura.href = '../controlador/FacturaControlador.php?action=verFactura&id_factura=' + facturaId;
                    btnVerFactura.style.display = 'inline-block';
                } else {
                    btnVerFactura.style.display = 'none';
                }
            });

            // Función para exportar conceptos
            window.exportarConceptos = function() {
                tabla.button('.buttons-excel').trigger();
            };

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
            min-width: 40px;
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

        .concepto-descripcion {
            max-width: 200px;
            word-wrap: break-word;
        }

        .dt-buttons .btn {
            margin-right: 5px;
        }

        @media (max-width: 768px) {
            .table-container {
                overflow-x: auto;
            }

            .btn-group .btn {
                min-width: 35px;
                font-size: 0.8rem;
                padding: 0.25rem 0.5rem;
            }

            .content-box-body .row > div {
                margin-bottom: 1rem;
            }

            .concepto-descripcion {
                max-width: 150px;
            }

            .dt-buttons {
                margin-bottom: 10px;
            }
        }
    </style>

<?php include("../../includes/footer.php"); ?>
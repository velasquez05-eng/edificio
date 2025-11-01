<?php include("../../includes/header.php"); ?>

    <!-- Page Header -->
    <div class="page-header fade-in">
        <div class="page-title">
            <h1>Gestión de Planillas</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item"><a href="#">Recursos Humanos</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Planillas de Pago</li>
                </ol>
            </nav>
        </div>
        <div class="page-actions">
            <a href="PlanillaControlador.php?action=formularioGenerarPlanilla" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>Generar Planilla
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
                        <div class="col-md-4 mb-3 mb-md-0">
                            <a href="PlanillaControlador.php?action=listarPlanillasCompleto"
                               class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-list me-2"></i>
                                Planillas Completas
                                <span class="badge bg-dark ms-2"><?php echo count($planillas); ?></span>
                            </a>
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <a href="PlanillaControlador.php?action=formularioGenerarPlanilla"
                               class="btn btn-warning btn-lg w-100">
                                <i class="fas fa-calculator me-2"></i>
                                Generar Planillas
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="PlanillaControlador.php?action=verMiPlanilla"
                               class="btn btn-info btn-lg w-100">
                                <i class="fas fa-user me-2"></i>
                                Mi Planilla
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros y Estadísticas -->
    <div class="row fade-in mb-4">
        <!-- Filtros -->
        <div class="col-md-6">
            <div class="content-box">
                <div class="content-box-header">
                    <h5><i class="fas fa-filter me-2"></i>Filtros</h5>
                </div>
                <div class="content-box-body">
                    <form method="GET" action="PlanillaControlador.php">
                        <input type="hidden" name="action" value="listarPlanillasCompleto">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="filtroMes" class="form-label">Mes</label>
                                <select id="filtroMes" name="mes" class="form-select">
                                    <option value="">Todos los meses</option>
                                    <?php
                                    $meses = [
                                            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                                            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                                            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                                    ];
                                    $mesActual = $_GET['mes'] ?? '';
                                    foreach($meses as $numero => $nombre): ?>
                                        <option value="<?php echo $numero; ?>" <?php echo $mesActual == $numero ? 'selected' : ''; ?>>
                                            <?php echo $nombre; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="filtroAnio" class="form-label">Año</label>
                                <select id="filtroAnio" name="anio" class="form-select">
                                    <option value="">Todos los años</option>
                                    <?php
                                    $anioActual = $_GET['anio'] ?? '';
                                    for($i = 2023; $i <= 2025; $i++): ?>
                                        <option value="<?php echo $i; ?>" <?php echo $anioActual == $i ? 'selected' : ''; ?>>
                                            <?php echo $i; ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="PlanillaControlador.php?action=listarPlanillasCompleto" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-times me-2"></i>Limpiar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-2"></i>Aplicar Filtros
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="col-md-6">
            <div class="content-box">
                <div class="content-box-header">
                    <h5><i class="fas fa-chart-bar me-2"></i>Estadísticas del Periodo</h5>
                </div>
                <div class="content-box-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="stat-card">
                                <i class="fas fa-users fa-2x text-primary mb-2"></i>
                                <h4 class="mb-1"><?php echo $estadisticas['total_empleados'] ?? 0; ?></h4>
                                <p class="text-muted mb-0">Empleados</p>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="stat-card">
                                <i class="fas fa-money-bill-wave fa-2x text-success mb-2"></i>
                                <h4 class="mb-1">Bs. <?php echo number_format($estadisticas['total_liquido'] ?? 0, 2); ?></h4>
                                <p class="text-muted mb-0">Total Líquido</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-card">
                                <i class="fas fa-chart-line fa-2x text-info mb-2"></i>
                                <h4 class="mb-1">Bs. <?php echo number_format($estadisticas['total_ganado'] ?? 0, 2); ?></h4>
                                <p class="text-muted mb-0">Total Ganado</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-card">
                                <i class="fas fa-percentage fa-2x text-warning mb-2"></i>
                                <h4 class="mb-1">Bs. <?php echo number_format($estadisticas['total_gestora'] ?? 0, 2); ?></h4>
                                <p class="text-muted mb-0">Gestora</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Planillas -->
    <div class="row fade-in">
        <div class="col-12">
            <div class="content-box">
                <div class="content-box-header d-flex justify-content-between align-items-center">
                    <h5>Lista de Planillas de Pago</h5>
                    <div>
                        <span class="badge bg-primary"><?php echo count($planillas); ?> planillas</span>
                    </div>
                </div>
                <div class="content-box-body">
                    <?php if (empty($planillas)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-file-invoice-dollar fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No hay planillas generadas para el periodo seleccionado</p>
                            <a href="PlanillaControlador.php?action=formularioGenerarPlanilla" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Generar Primera Planilla
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-container">
                            <table id="tablaPlanillas" class="table table-hover table-striped">
                                <thead>
                                <tr>
                                    <th>Empleado</th>
                                    <th>Rol</th>
                                    <th>Periodo</th>
                                    <th>Salario Base</th>
                                    <th>Días Trab.</th>
                                    <th>Total Ganado</th>
                                    <th>Descuento Gestora</th>
                                    <th>Líquido Pagable</th>
                                    <th>Método Pago</th>
                                    <th>Estado</th>
                                    <th>Fecha Pago</th>
                                    <th>Fecha Generación</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                // Array de meses en español
                                $meses_espanol = [
                                        'January' => 'Enero', 'February' => 'Febrero', 'March' => 'Marzo',
                                        'April' => 'Abril', 'May' => 'Mayo', 'June' => 'Junio',
                                        'July' => 'Julio', 'August' => 'Agosto', 'September' => 'Septiembre',
                                        'October' => 'Octubre', 'November' => 'Noviembre', 'December' => 'Diciembre'
                                ];

                                // Función para obtener clase CSS del estado
                                function getEstadoClass($estado) {
                                    switch($estado) {
                                        case 'pagada': return 'bg-success';
                                        case 'procesada': return 'bg-warning';
                                        case 'cancelada': return 'bg-danger';
                                        default: return 'bg-secondary';
                                    }
                                }

                                // Función para obtener texto del método de pago
                                function getMetodoPagoText($metodo) {
                                    switch($metodo) {
                                        case 'transferencia': return 'Transferencia';
                                        case 'qr': return 'QR';
                                        case 'efectivo': return 'Efectivo';
                                        case 'cheque': return 'Cheque';
                                        default: return $metodo;
                                    }
                                }

                                foreach ($planillas as $planilla):
                                    // Convertir el periodo a formato español
                                    $periodo_ingles = date('F Y', strtotime($planilla['periodo']));
                                    $periodo_espanol = strtr($periodo_ingles, $meses_espanol);
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($planilla['nombre_completo']); ?></td>
                                        <td>
                                            <span class="badge bg-info"><?php echo htmlspecialchars($planilla['rol']); ?></span>
                                        </td>
                                        <td><?php echo $periodo_espanol; ?></td>
                                        <td>Bs. <?php echo number_format($planilla['haber_basico'], 2); ?></td>
                                        <td>
                                            <span class="badge <?php echo $planilla['dias_trabajados'] < 30 ? 'bg-warning' : 'bg-success'; ?>">
                                                <?php echo $planilla['dias_trabajados']; ?> días
                                            </span>
                                        </td>
                                        <td>
                                            <strong class="text-success">Bs. <?php echo number_format($planilla['total_ganado'], 2); ?></strong>
                                        </td>
                                        <td>
                                            <span class="text-danger">Bs. <?php echo number_format($planilla['descuento_gestora'], 2); ?></span>
                                        </td>
                                        <td>
                                            <strong class="text-primary">Bs. <?php echo number_format($planilla['liquido_pagable'], 2); ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                <?php echo getMetodoPagoText($planilla['metodo_pago']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge <?php echo getEstadoClass($planilla['estado']); ?>">
                                                <?php echo ucfirst($planilla['estado']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($planilla['fecha_pago']): ?>
                                                <?php echo date('d/m/Y H:i', strtotime($planilla['fecha_pago'])); ?>
                                            <?php else: ?>
                                                <span class="badge bg-light text-dark">Pendiente</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php echo date('d/m/Y H:i', strtotime($planilla['fecha_creacion'])); ?>
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

    <!-- Incluir DataTables CSS y JS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">

    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

    <!-- Botones de Exportación (Solo CSV) -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

    <!-- Script para DataTable y funcionalidades -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar DataTable
            var tabla = $('#tablaPlanillas').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                responsive: true,
                scrollX: true,
                autoWidth: false,
                dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>" +
                    "<'row'<'col-sm-12 col-md-6'B>>",

                buttons: [
                    {
                        extend: 'csvHtml5',
                        text: '<i class="fas fa-file-csv me-2"></i>CSV',
                        className: 'btn btn-success btn-sm mb-2',
                        title: 'Planillas_de_Pago',
                        filename: 'planillas_pago_' + new Date().toISOString().slice(0, 10),
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                            format: {
                                body: function (data, row, column, node) {
                                    // Limpiar HTML de todas las columnas
                                    var textoSinHTML = data.replace(/<[^>]*>/g, '').trim();

                                    // Para columnas monetarias, mantener solo el número
                                    if (column === 3 || column === 5 || column === 6 || column === 7) {
                                        // Extraer solo el número de los montos
                                        var numero = textoSinHTML.replace('Bs.', '').replace(/[^\d.,]/g, '').trim();
                                        return numero || '0.00';
                                    }

                                    // Para columna de días trabajados
                                    if (column === 4) {
                                        return textoSinHTML.replace('días', '').trim();
                                    }

                                    // Para columna de rol (quitar badge)
                                    if (column === 1) {
                                        return textoSinHTML;
                                    }

                                    // Para columna de método de pago (limpiar badge)
                                    if (column === 8) {
                                        return textoSinHTML;
                                    }

                                    // Para columna de estado (limpiar badge)
                                    if (column === 9) {
                                        return textoSinHTML;
                                    }

                                    return textoSinHTML;
                                }
                            }
                        }
                    }
                ],
                pageLength: 10,
                lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
                order: [[11, 'desc']], // Ordenar por fecha de generación descendente
                columnDefs: [
                    {
                        targets: [3, 4, 5, 6, 7],
                        className: 'text-center'
                    },
                    {
                        targets: [8, 9],
                        className: 'text-center'
                    },
                    {
                        targets: [10, 11],
                        className: 'text-center'
                    }
                ],
                initComplete: function() {
                    $('.dataTables_filter input').addClass('form-control form-control-sm');
                    $('.dataTables_filter input').attr('placeholder', 'Buscar...');
                    $('.dataTables_length select').addClass('form-select form-select-sm');
                    this.api().buttons().container().appendTo('#tablaPlanillas_wrapper .col-md-6:eq(1)');
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
        .stat-card {
            padding: 15px;
            border-radius: 8px;
            background: #f8f9fa;
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            background: #e9ecef;
        }

        .stat-card h4 {
            font-weight: bold;
            color: var(--azul-oscuro);
        }

        .table th {
            border-top: none;
            font-weight: 600;
            color: var(--azul-oscuro);
            background-color: #f8f9fa;
            white-space: nowrap;
        }

        .content-box-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 1px solid #dee2e6;
        }

        .table-container {
            width: 100%;
            overflow-x: auto;
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

        #tablaPlanillas {
            width: 100% !important;
        }

        /* Estilos para botones de exportación */
        .dt-buttons {
            margin-bottom: 10px;
        }

        .dt-buttons .btn {
            margin-right: 5px;
        }

        /* Estilos para badges de estado */
        .bg-success { background-color: #198754 !important; }
        .bg-warning { background-color: #ffc107 !important; color: #000 !important; }
        .bg-danger { background-color: #dc3545 !important; }
        .bg-secondary { background-color: #6c757d !important; }

        @media (max-width: 768px) {
            .dt-buttons {
                text-align: center;
            }

            .dt-buttons .btn {
                margin-bottom: 5px;
                width: 100%;
            }

            .table th,
            .table td {
                font-size: 0.875rem;
                padding: 0.5rem;
            }
        }
    </style>

<?php include("../../includes/footer.php"); ?>
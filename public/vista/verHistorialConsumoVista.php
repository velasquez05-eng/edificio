<?php include("../../includes/header.php"); ?>

    <!-- Page Header -->
    <div class="page-header fade-in">
        <div class="page-title">
            <h1>Historial de Consumo</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item"><a href="../controlador/ServicioControlador.php?action=listarServicios">Servicios</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Historial de Consumo</li>
                </ol>
            </nav>
        </div>
        <div class="page-actions">
            <a href="ServicioControlador.php?action=formularioGenerarConsumo" class="btn btn-primary">
                <i class="fas fa-bolt me-2"></i>Generar Consumos
            </a>
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

    <div class="row fade-in">
        <!-- Información del Medidor -->
        <div class="col-lg-4">
            <div class="content-box">
                <div class="content-box-header">
                    <h5><i class="fas fa-info-circle me-2 text-info"></i>Información del Medidor</h5>
                </div>
                <div class="content-box-body">
                    <?php if ($medidor_actual): ?>
                        <div class="text-center mb-4">
                            <?php
                            $icon_class = '';
                            $badge_class = '';
                            switch($medidor_actual['servicio']) {
                                case 'agua':
                                    $icon_class = 'fas fa-tint fa-3x text-info';
                                    $badge_class = 'bg-info';
                                    break;
                                case 'luz':
                                    $icon_class = 'fas fa-bolt fa-3x text-warning';
                                    $badge_class = 'bg-warning';
                                    break;
                                case 'gas':
                                    $icon_class = 'fas fa-fire fa-3x text-danger';
                                    $badge_class = 'bg-danger';
                                    break;
                            }
                            ?>
                            <i class="<?php echo $icon_class; ?> mb-3"></i>
                            <h4 class="text-primary"><?php echo ucfirst(htmlspecialchars($medidor_actual['servicio'])); ?></h4>
                            <p class="text-muted"><?php echo htmlspecialchars($medidor_actual['unidad_medida']); ?></p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><strong>Código del Medidor:</strong></label>
                            <p class="form-control-static">
                                <code><?php echo htmlspecialchars($medidor_actual['codigo']); ?></code>
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><strong>Departamento:</strong></label>
                            <p class="form-control-static">
                                <i class="fas fa-building text-info me-2"></i>
                                Departamento <?php echo htmlspecialchars($medidor_actual['departamento']); ?>
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><strong>Estado:</strong></label>
                            <p>
                            <span class="badge <?php echo $badge_class; ?>">
                                <?php echo ucfirst(htmlspecialchars($medidor_actual['estado'])); ?>
                            </span>
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><strong>Fecha Instalación:</strong></label>
                            <p class="form-control-static">
                                <i class="fas fa-calendar-alt text-primary me-2"></i>
                                <?php echo date('d/m/Y', strtotime($medidor_actual['fecha_instalacion'])); ?>
                            </p>
                        </div>

                    <?php else: ?>
                        <div class="text-center text-muted">
                            <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                            <p>No se encontró información del medidor</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Filtros -->
            <div class="content-box mt-4">
                <div class="content-box-header">
                    <h5><i class="fas fa-filter me-2 text-primary"></i>Filtrar Historial</h5>
                </div>
                <div class="content-box-body">
                    <form method="GET" action="../controlador/ServicioControlador.php">
                        <input type="hidden" name="action" value="verHistorialConsumo">
                        <input type="hidden" name="id_medidor" value="<?php echo htmlspecialchars($_GET['id_medidor']); ?>">

                        <div class="mb-3">
                            <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                            <input type="date"
                                   class="form-control"
                                   id="fecha_inicio"
                                   name="fecha_inicio"
                                   value="<?php echo htmlspecialchars($_GET['fecha_inicio'] ?? ''); ?>">
                        </div>

                        <div class="mb-3">
                            <label for="fecha_fin" class="form-label">Fecha Fin</label>
                            <input type="date"
                                   class="form-control"
                                   id="fecha_fin"
                                   name="fecha_fin"
                                   value="<?php echo htmlspecialchars($_GET['fecha_fin'] ?? ''); ?>">
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Filtrar
                            </button>
                            <a href="../controlador/ServicioControlador.php?action=verHistorialConsumo&id_medidor=<?php echo htmlspecialchars($_GET['id_medidor']); ?>"
                               class="btn btn-outline-secondary">
                                <i class="fas fa-sync me-2"></i>Limpiar Filtros
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Estadísticas Rápidas -->
            <div class="content-box mt-4">
                <div class="content-box-header">
                    <h5><i class="fas fa-chart-bar me-2 text-success"></i>Estadísticas</h5>
                </div>
                <div class="content-box-body">
                    <?php
                    $total_consumo = 0;
                    $total_registros = count($historial);
                    $consumo_promedio = 0;
                    $consumo_maximo = 0;
                    $consumo_minimo = PHP_FLOAT_MAX;
                    $fecha_mas_reciente = '';
                    $fecha_mas_antigua = '';

                    if ($total_registros > 0) {
                        foreach ($historial as $lectura) {
                            $consumo = floatval($lectura['consumo']);
                            $total_consumo += $consumo;

                            if ($consumo > $consumo_maximo) {
                                $consumo_maximo = $consumo;
                            }

                            if ($consumo < $consumo_minimo) {
                                $consumo_minimo = $consumo;
                            }

                            if (empty($fecha_mas_reciente) || $lectura['fecha_hora'] > $fecha_mas_reciente) {
                                $fecha_mas_reciente = $lectura['fecha_hora'];
                            }

                            if (empty($fecha_mas_antigua) || $lectura['fecha_hora'] < $fecha_mas_antigua) {
                                $fecha_mas_antigua = $lectura['fecha_hora'];
                            }
                        }

                        $consumo_promedio = $total_consumo / $total_registros;
                        $consumo_minimo = $consumo_minimo === PHP_FLOAT_MAX ? 0 : $consumo_minimo;
                    }
                    ?>

                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="stat-card">
                                <i class="fas fa-database fa-2x text-primary mb-2"></i>
                                <h5 class="mb-1"><?php echo $total_registros; ?></h5>
                                <small class="text-muted">Registros</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="stat-card">
                                <i class="fas fa-chart-line fa-2x text-success mb-2"></i>
                                <h5 class="mb-1"><?php echo number_format($total_consumo, 2); ?></h5>
                                <small class="text-muted">Consumo Total</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="stat-card">
                                <i class="fas fa-calculator fa-2x text-info mb-2"></i>
                                <h5 class="mb-1"><?php echo number_format($consumo_promedio, 2); ?></h5>
                                <small class="text-muted">Promedio</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="stat-card">
                                <i class="fas fa-arrow-up fa-2x text-warning mb-2"></i>
                                <h5 class="mb-1"><?php echo number_format($consumo_maximo, 2); ?></h5>
                                <small class="text-muted">Máximo</small>
                            </div>
                        </div>
                    </div>

                    <?php if ($total_registros > 0): ?>
                        <div class="mt-3">
                            <small class="text-muted">
                                <strong>Período:</strong><br>
                                <?php echo date('d/m/Y', strtotime($fecha_mas_antigua)); ?> -
                                <?php echo date('d/m/Y', strtotime($fecha_mas_reciente)); ?>
                            </small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Historial de Consumos -->
        <div class="col-lg-8">
            <div class="content-box">
                <div class="content-box-header d-flex justify-content-between align-items-center">
                    <h5><i class="fas fa-history me-2 text-primary"></i>Historial de Consumos</h5>
                    <span class="badge bg-primary"><?php echo count($historial); ?> registros</span>
                </div>
                <div class="content-box-body">
                    <?php if (empty($historial)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No hay registros de consumo</h5>
                            <p class="text-muted">No se encontraron lecturas para el medidor seleccionado.</p>
                            <a href="ServicioControlador.php?action=formularioGenerarConsumo" class="btn btn-primary mt-2">
                                <i class="fas fa-bolt me-2"></i>Generar Consumos
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table id="tablaHistorial" class="table table-hover table-striped">
                                <thead>
                                <tr>
                                    <th>Fecha y Hora</th>
                                    <th>Consumo</th>
                                    <th>Unidad</th>
                                    <th>Acciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($historial as $lectura): ?>
                                    <tr>
                                        <td>
                                            <i class="fas fa-calendar-alt text-primary me-2"></i>
                                            <?php echo date('d/m/Y H:i', strtotime($lectura['fecha_hora'])); ?>
                                        </td>
                                        <td>
                                            <span class="consumo-valor <?php echo $lectura['consumo'] > $consumo_promedio ? 'text-danger' : 'text-success'; ?>">
                                                <i class="fas fa-bolt me-2"></i>
                                                <?php echo number_format($lectura['consumo'], 2); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                <?php echo htmlspecialchars($lectura['unidad_medida']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-info"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#detalleLecturaModal"
                                                        data-fecha="<?php echo htmlspecialchars($lectura['fecha_hora']); ?>"
                                                        data-consumo="<?php echo htmlspecialchars($lectura['consumo']); ?>"
                                                        data-unidad="<?php echo htmlspecialchars($lectura['unidad_medida']); ?>"
                                                        data-servicio="<?php echo htmlspecialchars($lectura['servicio']); ?>"
                                                        data-departamento="<?php echo htmlspecialchars($lectura['departamento']); ?>"
                                                        title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-outline-danger"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#eliminarLecturaModal"
                                                        data-id="<?php echo htmlspecialchars($lectura['id_lectura']); ?>"
                                                        data-fecha="<?php echo date('d/m/Y H:i', strtotime($lectura['fecha_hora'])); ?>"
                                                        data-consumo="<?php echo htmlspecialchars($lectura['consumo']); ?>"
                                                        data-unidad="<?php echo htmlspecialchars($lectura['unidad_medida']); ?>"
                                                        title="Eliminar lectura">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Resumen del Período -->
                        <div class="row mt-4">
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Consumo Total</h6>
                                        <h4 class="text-primary"><?php echo number_format($total_consumo, 2); ?></h4>
                                        <small class="text-muted"><?php echo htmlspecialchars($medidor_actual['unidad_medida'] ?? ''); ?></small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Promedio Diario</h6>
                                        <h4 class="text-success"><?php echo number_format($consumo_promedio, 2); ?></h4>
                                        <small class="text-muted"><?php echo htmlspecialchars($medidor_actual['unidad_medida'] ?? ''); ?></small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Días Registrados</h6>
                                        <h4 class="text-info"><?php echo $total_registros; ?></h4>
                                        <small class="text-muted">Total de lecturas</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>


        </div>
    </div>

    <!-- Modal Detalle Lectura -->
    <div class="modal fade" id="detalleLecturaModal" tabindex="-1" aria-labelledby="detalleLecturaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detalleLecturaModalLabel">
                        <i class="fas fa-info-circle me-2"></i>Detalle de Lectura
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <strong><i class="fas fa-calendar-alt text-primary me-2"></i>Fecha:</strong>
                            <p id="detalleFecha" class="mb-2"></p>
                        </div>
                        <div class="col-6">
                            <strong><i class="fas fa-bolt text-warning me-2"></i>Consumo:</strong>
                            <p id="detalleConsumo" class="mb-2"></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <strong><i class="fas fa-tachometer-alt text-info me-2"></i>Servicio:</strong>
                            <p id="detalleServicio" class="mb-2"></p>
                        </div>
                        <div class="col-6">
                            <strong><i class="fas fa-building text-success me-2"></i>Departamento:</strong>
                            <p id="detalleDepartamento" class="mb-2"></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <strong><i class="fas fa-ruler text-secondary me-2"></i>Unidad de Medida:</strong>
                            <p id="detalleUnidad" class="mb-0"></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Eliminar Lectura -->
    <div class="modal fade" id="eliminarLecturaModal" tabindex="-1" aria-labelledby="eliminarLecturaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eliminarLecturaModalLabel">
                        <i class="fas fa-trash me-2"></i>Eliminar Lectura
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="../controlador/ServicioControlador.php" id="formEliminarLectura">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="eliminarLectura">
                        <input type="hidden" name="id_lectura" id="eliminarIdLectura">

                        <div class="text-center">
                            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                            <h6>¿Está seguro que desea eliminar esta lectura?</h6>
                            <p class="text-muted mb-0">Fecha: <strong id="eliminarFecha"></strong></p>
                            <p class="text-muted">Consumo: <strong id="eliminarConsumo"></strong></p>
                            <div class="alert alert-danger mt-3">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <strong>Advertencia:</strong> Esta acción no se puede deshacer.
                            </div>
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

    <!-- Incluir DataTables CSS y JS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

    <!-- Script para funcionalidades -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar DataTable
            var tabla = $('#tablaHistorial').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                responsive: true,
                order: [[0, 'desc']],
                pageLength: 10,
                lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
                columnDefs: [
                    {
                        orderable: false,
                        targets: [3] // Columna de acciones no ordenable
                    },
                    {
                        searchable: false,
                        targets: [3] // Columna de acciones no buscable
                    }
                ]
            });

            // Cargar datos en el modal de detalle
            const detalleLecturaModal = document.getElementById('detalleLecturaModal');
            detalleLecturaModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const fecha = button.getAttribute('data-fecha');
                const consumo = button.getAttribute('data-consumo');
                const unidad = button.getAttribute('data-unidad');
                const servicio = button.getAttribute('data-servicio');
                const departamento = button.getAttribute('data-departamento');

                document.getElementById('detalleFecha').textContent = new Date(fecha).toLocaleString('es-ES');
                document.getElementById('detalleConsumo').textContent = consumo + ' ' + unidad;
                document.getElementById('detalleServicio').textContent = servicio.charAt(0).toUpperCase() + servicio.slice(1);
                document.getElementById('detalleDepartamento').textContent = 'Departamento ' + departamento;
                document.getElementById('detalleUnidad').textContent = unidad;
            });

            // Cargar datos en el modal de eliminar
            const eliminarLecturaModal = document.getElementById('eliminarLecturaModal');
            eliminarLecturaModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const idLectura = button.getAttribute('data-id');
                const fecha = button.getAttribute('data-fecha');
                const consumo = button.getAttribute('data-consumo');
                const unidad = button.getAttribute('data-unidad');

                document.getElementById('eliminarIdLectura').value = idLectura;
                document.getElementById('eliminarFecha').textContent = fecha;
                document.getElementById('eliminarConsumo').textContent = consumo + ' ' + unidad;
            });

            // Validar fechas en el filtro
            const fechaInicio = document.getElementById('fecha_inicio');
            const fechaFin = document.getElementById('fecha_fin');

            if (fechaInicio && fechaFin) {
                fechaInicio.addEventListener('change', function() {
                    if (this.value && fechaFin.value && this.value > fechaFin.value) {
                        fechaFin.value = this.value;
                    }
                });

                fechaFin.addEventListener('change', function() {
                    if (this.value && fechaInicio.value && this.value < fechaInicio.value) {
                        fechaInicio.value = this.value;
                    }
                });
            }

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

    <style>
        .content-box {
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .content-box-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 1px solid #dee2e6;
            padding: 15px 20px;
            border-radius: 10px 10px 0 0;
        }

        .stat-card {
            padding: 15px;
            border-radius: 8px;
            background: #f8f9fa;
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }

        .consumo-valor {
            font-weight: 600;
        }

        .consumo-valor.text-danger {
            color: #dc3545 !important;
        }

        .consumo-valor.text-success {
            color: #28a745 !important;
        }

        .btn-group-sm .btn {
            padding: 0.25rem 0.5rem;
        }

        .table th {
            border-top: none;
            font-weight: 600;
            background-color: #f8f9fa;
        }

        /* Iconos específicos */
        .fa-tint { color: #17a2b8 !important; }
        .fa-bolt { color: #ffc107 !important; }
        .fa-fire { color: #dc3545 !important; }

        /* Badges */
        .badge.bg-info { background-color: #17a2b8 !important; }
        .badge.bg-warning { background-color: #ffc107 !important; color: #000 !important; }
        .badge.bg-danger { background-color: #dc3545 !important; }
        .badge.bg-primary { background-color: #007bff !important; }
        .badge.bg-secondary { background-color: #6c757d !important; }

        /* Responsive */
        @media (max-width: 768px) {
            .btn-group {
                flex-direction: column;
            }

            .btn-group .btn {
                margin-bottom: 0.25rem;
            }

            .stat-card {
                margin-bottom: 1rem;
            }
        }
    </style>

<?php include("../../includes/footer.php"); ?>
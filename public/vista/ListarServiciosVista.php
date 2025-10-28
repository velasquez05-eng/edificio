<?php include("../../includes/header.php"); ?>

    <!-- Page Header -->
    <div class="page-header fade-in">
        <div class="page-title">
            <h1>Servicios</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../vista/DashboardVista.php"><i class="fas fa-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item"><a href="#">Servicios</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Lista de Servicios</li>
                </ol>
            </nav>
        </div>
        <div class="page-actions">
            <a href="ServicioControlador.php?action=formularioServicio" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>Registrar Servicio
            </a>
            <a href="ServicioControlador.php?action=formularioAsignarServicio" class="btn btn-info">
                <i class="fas fa-link me-2"></i>Asignar Servicio
            </a>
            <a href="ServicioControlador.php?action=formularioGenerarConsumo" class="btn btn-primary">
                <i class="fas fa-bolt me-2"></i>Generar Consumo
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

    <!-- Estadísticas de Servicios -->
<?php
// Calcular estadísticas
$numeroActivos = 0;
$numeroInactivos = 0;
$costoPromedio = 0;
$totalServicios = count($servicios);

foreach ($servicios as $servicio) {
    if ($servicio['estado'] == 'activo') {
        $numeroActivos++;
    } else {
        $numeroInactivos++;
    }
    $costoPromedio += $servicio['costo_unitario'];
}

$costoPromedio = $totalServicios > 0 ? $costoPromedio / $totalServicios : 0;
?>

    <div class="row fade-in mb-4">
        <div class="col-md-3">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                    <h4 class="mb-1"><?php echo $numeroActivos; ?></h4>
                    <p class="text-muted mb-0">Activos</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-times-circle fa-2x text-danger mb-2"></i>
                    <h4 class="mb-1"><?php echo $numeroInactivos; ?></h4>
                    <p class="text-muted mb-0">Inactivos</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-money-bill-wave fa-2x text-info mb-2"></i>
                    <h4 class="mb-1">Bs. <?php echo number_format($costoPromedio, 2); ?></h4>
                    <p class="text-muted mb-0">Costo Promedio</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-bolt fa-2x text-primary mb-2"></i>
                    <h4 class="mb-1"><?php echo $totalServicios; ?></h4>
                    <p class="text-muted mb-0">Total Servicios</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Servicios -->
    <div class="row fade-in">
        <div class="col-12">
            <div class="content-box">
                <div class="content-box-header d-flex justify-content-between align-items-center">
                    <h5>Lista de Servicios</h5>
                    <span class="badge bg-primary"><?php echo count($servicios); ?> servicios</span>
                </div>
                <div class="content-box-body">
                    <?php if (empty($servicios)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-bolt fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No hay servicios registrados</p>
                            <a href="ServicioControlador.php?action=formularioServicio" class="btn btn-success">
                                <i class="fas fa-plus me-2"></i>Registrar Primer Servicio
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-container">
                            <table id="tablaServicios" class="table table-hover table-striped">
                                <thead>
                                <tr>
                                    <th># ID</th>
                                    <th>Nombre</th>
                                    <th>Unidad de Medida</th>
                                    <th>Costo Unitario</th>
                                    <th>Estado</th>
                                    <th>Opciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($servicios as $servicio): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($servicio['id_servicio']); ?></strong></td>
                                        <td>
                                            <?php
                                            $icon_class = '';
                                            switch(strtolower($servicio['nombre'])) {
                                                case 'agua':
                                                case 'water':
                                                    $icon_class = 'fas fa-tint text-info';
                                                    break;
                                                case 'luz':
                                                case 'electricidad':
                                                case 'electricity':
                                                    $icon_class = 'fas fa-bolt text-warning';
                                                    break;
                                                case 'gas':
                                                    $icon_class = 'fas fa-fire text-danger';
                                                    break;
                                                case 'internet':
                                                    $icon_class = 'fas fa-wifi text-primary';
                                                    break;
                                                default:
                                                    $icon_class = 'fas fa-bolt text-secondary';
                                            }
                                            ?>
                                            <i class="<?php echo $icon_class; ?> me-2"></i>
                                            <?php echo ucfirst(htmlspecialchars($servicio['nombre'])); ?>
                                        </td>
                                        <td>
                                            <i class="fas fa-ruler me-2 text-secondary"></i>
                                            <?php echo htmlspecialchars($servicio['unidad_medida']); ?>
                                        </td>
                                        <td>
                                            <i class="fas fa-money-bill-wave text-success me-2"></i>
                                            Bs. <?php echo number_format($servicio['costo_unitario'], 2); ?>
                                        </td>
                                        <td>
                                            <?php
                                            $badge_class = $servicio['estado'] == 'activo' ? 'bg-success' : 'bg-danger';
                                            $icon = $servicio['estado'] == 'activo' ? 'fa-check-circle' : 'fa-times-circle';
                                            ?>
                                            <span class="badge <?php echo $badge_class; ?>">
                                            <i class="fas <?php echo $icon; ?> me-1"></i>
                                            <?php echo ucfirst(htmlspecialchars($servicio['estado'])); ?>
                                        </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-warning btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editarServicioModal"
                                                    data-id="<?php echo htmlspecialchars($servicio['id_servicio']); ?>"
                                                    data-nombre="<?php echo htmlspecialchars($servicio['nombre']); ?>"
                                                    data-unidad="<?php echo htmlspecialchars($servicio['unidad_medida']); ?>"
                                                    data-costo="<?php echo htmlspecialchars($servicio['costo_unitario']); ?>"
                                                    data-estado="<?php echo htmlspecialchars($servicio['estado']); ?>"
                                                    title="Editar servicio">
                                                <i class="fas fa-edit"></i> Editar
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

    <!-- Modal Editar Servicio -->
    <div class="modal fade" id="editarServicioModal" tabindex="-1" aria-labelledby="editarServicioModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarServicioModalLabel">
                        <i class="fas fa-edit me-2"></i>Editar Servicio
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="ServicioControlador.php" id="formEditarServicio">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="editarServicio">
                        <input type="hidden" name="id_servicio" id="editIdServicio">

                        <div class="mb-3">
                            <label for="editNombre" class="form-label">
                                <i class="fas fa-bolt text-warning me-2"></i>Nombre del Servicio
                            </label>
                            <input type="text" class="form-control" id="editNombre" name="nombre" readonly disabled>
                            <small class="form-text text-muted">El nombre del servicio no se puede modificar</small>
                        </div>

                        <div class="mb-3">
                            <label for="editUnidad" class="form-label">
                                <i class="fas fa-ruler text-info me-2"></i>Unidad de Medida
                            </label>
                            <input type="text" class="form-control" id="editUnidad" name="unidad_medida" required
                                   placeholder="Ej: m³, kWh, MB" pattern="[A-Za-z0-9³²°]{1,10}">
                            <small class="form-text text-muted">Ejemplo: m³, kWh, MB, etc. (máx. 10 caracteres)</small>
                        </div>

                        <div class="mb-3">
                            <label for="editCosto" class="form-label">
                                <i class="fas fa-money-bill-wave text-success me-2"></i>Costo Unitario (Bs.)
                            </label>
                            <input type="number" class="form-control" id="editCosto" name="costo_unitario"
                                   step="0.01" min="0.01" max="9999.99" required
                                   placeholder="0.00">
                            <small class="form-text text-muted">El costo debe ser mayor a 0.00 Bs.</small>
                        </div>

                        <div class="mb-3">
                            <label for="editEstado" class="form-label">
                                <i class="fas fa-toggle-on text-primary me-2"></i>Estado
                            </label>
                            <select class="form-select" id="editEstado" name="estado" required>
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                            <small class="form-text text-muted">Los servicios inactivos no podrán ser asignados</small>
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
            var tabla = $('#tablaServicios').DataTable({
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
                        targets: [5] // Columna de opciones no ordenable
                    },
                    {
                        searchable: false,
                        targets: [5] // Columna de opciones no buscable
                    },
                    {
                        targets: [0],
                        className: 'text-center'
                    },
                    {
                        targets: [3, 4],
                        className: 'text-end'
                    }
                ],
                initComplete: function() {
                    // Personalizar el buscador
                    $('.dataTables_filter input').addClass('form-control form-control-sm');
                    $('.dataTables_filter input').attr('placeholder', 'Buscar servicios...');

                    // Personalizar el selector de cantidad de registros
                    $('.dataTables_length select').addClass('form-select form-select-sm');
                }
            });

            // Cargar datos en el modal de editar servicio
            const editarServicioModal = document.getElementById('editarServicioModal');
            editarServicioModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const idServicio = button.getAttribute('data-id');
                const nombre = button.getAttribute('data-nombre');
                const unidad = button.getAttribute('data-unidad');
                const costo = button.getAttribute('data-costo');
                const estado = button.getAttribute('data-estado');

                // Llenar el formulario con los datos actuales
                document.getElementById('editIdServicio').value = idServicio;
                document.getElementById('editNombre').value = nombre;
                document.getElementById('editUnidad').value = unidad;
                document.getElementById('editCosto').value = parseFloat(costo).toFixed(2);
                document.getElementById('editEstado').value = estado;

                // Actualizar título del modal
                document.getElementById('editarServicioModalLabel').innerHTML =
                    `<i class="fas fa-edit me-2"></i>Editar Servicio - ${nombre}`;
            });

            // Validación del formulario de edición
            const formEditarServicio = document.getElementById('formEditarServicio');
            formEditarServicio.addEventListener('submit', function(e) {
                const costo = parseFloat(document.getElementById('editCosto').value);
                const unidad = document.getElementById('editUnidad').value.trim();

                if (costo <= 0) {
                    e.preventDefault();
                    alert('El costo unitario debe ser mayor a 0.00 Bs.');
                    document.getElementById('editCosto').focus();
                    return;
                }

                if (unidad.length === 0 || unidad.length > 10) {
                    e.preventDefault();
                    alert('La unidad de medida debe tener entre 1 y 10 caracteres.');
                    document.getElementById('editUnidad').focus();
                    return;
                }

                // Cerrar modal después de enviar
                const modal = bootstrap.Modal.getInstance(editarServicioModal);
                if (modal) {
                    modal.hide();
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

            // Efectos visuales para las filas de la tabla
            $('#tablaServicios tbody tr').hover(
                function() {
                    $(this).addClass('table-active');
                },
                function() {
                    $(this).removeClass('table-active');
                }
            );
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

        .badge {
            font-size: 0.75rem;
        }

        .table-container {
            width: 100%;
            overflow-x: hidden;
        }

        /* Estilos para DataTable */
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 0.375rem 0.75rem;
        }

        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
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

        /* Estilos para las estadísticas */
        .content-box.text-center {
            transition: transform 0.2s, box-shadow 0.2s;
            border: 1px solid #dee2e6;
        }

        .content-box.text-center:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .content-box.text-center .fa-2x {
            margin-bottom: 10px;
        }

        .content-box.text-center h4 {
            font-weight: bold;
            color: var(--azul-oscuro);
        }

        /* Iconos específicos para servicios */
        .fa-tint { color: #17a2b8 !important; }
        .fa-bolt { color: #ffc107 !important; }
        .fa-fire { color: #dc3545 !important; }
        .fa-wifi { color: #0d6efd !important; }

        /* Mejoras para la tabla */
        .table-hover tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.05);
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.02);
        }

        /* Botón de editar */
        .btn-warning {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #000;
        }

        .btn-warning:hover {
            background-color: #ffca2c;
            border-color: #ffca2c;
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
    </style>

<?php include("../../includes/footer.php"); ?>
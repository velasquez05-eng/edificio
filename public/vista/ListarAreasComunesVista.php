<?php include("../../includes/header.php");
?>


    <!-- Page Header -->
    <div class="page-header fade-in">
        <div class="page-title">
            <h1>Áreas Comunes</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item"><a href="#">Áreas Comunes</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Lista de Áreas Comunes</li>
                </ol>
            </nav>
        </div>
        <div class="page-actions">
            <a href="AreaComunControlador.php?action=formularioArea" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>Registrar Área
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
                            <a href="AreaComunControlador.php?action=verReservasPendientes"
                               class="btn btn-warning btn-lg w-100">
                                <i class="fas fa-clock me-2"></i>
                                Reservas Pendientes
                                <span class="badge bg-dark ms-2"><?php echo $numeroReservasPendientes; ?></span>
                            </a>
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <a href="AreaComunControlador.php?action=verReservasMes"
                               class="btn btn-info btn-lg w-100">
                                <i class="fas fa-calendar-alt me-2"></i>
                                Reservas del Mes
                                <span class="badge bg-dark ms-2"><?php echo $numeroReservasMes; ?></span>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="AreaComunControlador.php?action=listarAreas"
                               class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-swimming-pool me-2"></i>
                                Ver Áreas
                                <span class="badge bg-dark ms-2"><?php echo count($areascomunes); ?></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas de Áreas -->
    <div class="row fade-in mb-4">
        <div class="col-md-3">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                    <h4 class="mb-1"><?php echo $numeroDisponible; ?></h4>
                    <p class="text-muted mb-0">Disponibles</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-tools fa-2x text-warning mb-2"></i>
                    <h4 class="mb-1"><?php echo $numeroMantenimiento; ?></h4>
                    <p class="text-muted mb-0">En Mantenimiento</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-times-circle fa-2x text-danger mb-2"></i>
                    <h4 class="mb-1"><?php echo $numeroNoDisponible; ?></h4>
                    <p class="text-muted mb-0">No Disponibles</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-swimming-pool fa-2x text-primary mb-2"></i>
                    <h4 class="mb-1"><?php echo $numeroTotalArea; ?></h4>
                    <p class="text-muted mb-0">Total Áreas</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Áreas Comunes -->
    <div class="row fade-in">
        <div class="col-12">
            <div class="content-box">
                <div class="content-box-header d-flex justify-content-between align-items-center">
                    <h5>Lista de Áreas Comunes</h5>
                    <span class="badge bg-primary"><?php echo count($areascomunes); ?> áreas</span>
                </div>
                <div class="content-box-body">
                    <?php if (empty($areascomunes)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-swimming-pool fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No hay áreas comunes registradas</p>
                        </div>
                    <?php else: ?>
                        <div class="table-container">
                            <table id="tablaAreasComunes" class="table table-hover table-striped">
                                <thead>
                                <tr>
                                    <th># ID</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Capacidad</th>
                                    <th>Precio Reserva</th>
                                    <th>Estado</th>
                                    <th>Mantenimiento</th>
                                    <th>Opciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($areascomunes as $area): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($area['id_area']); ?></strong></td>
                                        <td>
                                            <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                            <?php echo htmlspecialchars($area['nombre']); ?>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($area['descripcion']); ?>
                                        </td>
                                        <td>
                                            <i class="fas fa-users text-info me-2"></i>
                                            <?php echo htmlspecialchars($area['capacidad']); ?> personas
                                        </td>
                                        <td>
                                            <i class="fas fa-money-bill-wave text-success me-2"></i>
                                            Bs. <?php echo number_format($area['costo_reserva'], 2); ?>
                                        </td>
                                        <td>
                                            <?php
                                            $badge_class = '';
                                            switch($area['estado']) {
                                                case 'disponible':
                                                    $badge_class = 'bg-success';
                                                    $icon = 'fa-check-circle';
                                                    break;
                                                case 'mantenimiento':
                                                    $badge_class = 'bg-warning';
                                                    $icon = 'fa-tools';
                                                    break;
                                                case 'no disponible':
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
                                                <?php echo ucfirst(htmlspecialchars($area['estado'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($area['fecha_inicio_mantenimiento'] && $area['fecha_fin_mantenimiento']): ?>
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar-alt me-1"></i>
                                                    <?php echo date('d/m/Y', strtotime($area['fecha_inicio_mantenimiento'])); ?>
                                                    -
                                                    <?php echo date('d/m/Y', strtotime($area['fecha_fin_mantenimiento'])); ?>
                                                </small>
                                            <?php else: ?>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check me-1"></i>Funcional
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-warning btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editarAreaModal"
                                                        data-id="<?php echo htmlspecialchars($area['id_area']); ?>"
                                                        data-nombre="<?php echo htmlspecialchars($area['nombre']); ?>"
                                                        data-descripcion="<?php echo htmlspecialchars($area['descripcion']); ?>"
                                                        data-capacidad="<?php echo htmlspecialchars($area['capacidad']); ?>"
                                                        data-costo="<?php echo htmlspecialchars($area['costo_reserva']); ?>"
                                                        data-estado="<?php echo htmlspecialchars($area['estado']); ?>"
                                                        title="Editar área">
                                                    <i class="fas fa-edit"></i>
                                                </button>

                                                <?php if ($area['estado'] == 'disponible'): ?>
                                                    <button class="btn btn-secondary btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#mantenimientoModal"
                                                            data-id="<?php echo htmlspecialchars($area['id_area']); ?>"
                                                            data-nombre="<?php echo htmlspecialchars($area['nombre']); ?>"
                                                            title="Programar mantenimiento">
                                                        <i class="fas fa-tools"></i>
                                                    </button>
                                                <?php elseif ($area['estado'] == 'mantenimiento'): ?>
                                                    <button class="btn btn-success btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#finalizarMantenimientoModal"
                                                            data-id="<?php echo htmlspecialchars($area['id_area']); ?>"
                                                            data-nombre="<?php echo htmlspecialchars($area['nombre']); ?>"
                                                            title="Finalizar mantenimiento">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                <?php endif; ?>

                                                <button class="btn btn-info btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#verReservasModal"
                                                        data-id="<?php echo htmlspecialchars($area['id_area']); ?>"
                                                        data-nombre="<?php echo htmlspecialchars($area['nombre']); ?>"
                                                        title="Ver reservas del área">
                                                    <i class="fas fa-list"></i>
                                                </button>

                                                <button class="btn btn-danger btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#eliminarAreaModal"
                                                        data-id="<?php echo htmlspecialchars($area['id_area']); ?>"
                                                        data-nombre="<?php echo htmlspecialchars($area['nombre']); ?>"
                                                        title="Eliminar área">
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

    <!-- Modal Editar Área -->
    <div class="modal fade" id="editarAreaModal" tabindex="-1" aria-labelledby="editarAreaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarAreaModalLabel">
                        <i class="fas fa-edit me-2"></i>Editar Área Común
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="AreaComunControlador.php" id="formEditarArea">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="editarArea">
                        <input type="hidden" name="id_area" id="editIdArea">

                        <div class="mb-3">
                            <label for="editNombre" class="form-label">
                                <i class="fas fa-map-marker-alt text-primary me-2"></i>Nombre
                            </label>
                            <input type="text" class="form-control" id="editNombre" name="nombre" required>
                        </div>

                        <div class="mb-3">
                            <label for="editDescripcion" class="form-label">
                                <i class="fas fa-align-left text-info me-2"></i>Descripción
                            </label>
                            <textarea class="form-control" id="editDescripcion" name="descripcion" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="editCapacidad" class="form-label">
                                <i class="fas fa-users text-info me-2"></i>Capacidad
                            </label>
                            <input type="number" class="form-control" id="editCapacidad" name="capacidad" min="1" required>
                        </div>

                        <div class="mb-3">
                            <label for="editCosto" class="form-label">
                                <i class="fas fa-money-bill-wave text-success me-2"></i>Precio de Reserva (Bs.)
                            </label>
                            <input type="number" class="form-control" id="editCosto" name="costo_reserva" step="0.01" min="0" required>
                        </div>

                        <div class="mb-3">
                            <label for="editEstado" class="form-label">
                                <i class="fas fa-toggle-on text-success me-2"></i>Estado
                            </label>
                            <select class="form-select" id="editEstado" name="estado" required>
                                <option value="disponible">Disponible</option>
                                <option value="no disponible">No Disponible</option>
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

    <!-- Modal Mantenimiento -->
    <div class="modal fade" id="mantenimientoModal" tabindex="-1" aria-labelledby="mantenimientoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mantenimientoModalLabel">
                        <i class="fas fa-tools me-2"></i>Programar Mantenimiento
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="AreaComunControlador.php" id="formMantenimiento">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="programarMantenimiento">
                        <input type="hidden" name="id_persona" id="id_persona" value="<?php echo $_SESSION['id_persona'] ?>">
                        <input type="hidden" name="id_area" id="mantenimientoIdArea">


                        <div class="text-center mb-3">
                            <i class="fas fa-tools fa-3x text-warning mb-2"></i>
                            <p class="text-muted">Programar mantenimiento para: <strong id="nombreAreaMantenimiento"></strong></p>
                        </div>

                        <div class="mb-3">
                            <label for="fechaInicioMantenimiento" class="form-label">
                                <i class="fas fa-calendar-plus text-primary me-2"></i>Fecha Inicio
                            </label>
                            <input type="date" class="form-control" id="fechaInicioMantenimiento" name="fecha_inicio" required
                                   min="<?php echo date('Y-m-d'); ?>">
                        </div>

                        <div class="mb-3">
                            <label for="fechaFinMantenimiento" class="form-label">
                                <i class="fas fa-calendar-minus text-danger me-2"></i>Fecha Fin
                            </label>
                            <input type="date" class="form-control" id="fechaFinMantenimiento" name="fecha_fin" required
                                   min="<?php echo date('Y-m-d'); ?>">
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Durante el período de mantenimiento, el área no estará disponible para reservas.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-tools me-2"></i>Programar Mantenimiento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Finalizar Mantenimiento -->
    <div class="modal fade" id="finalizarMantenimientoModal" tabindex="-1" aria-labelledby="finalizarMantenimientoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="finalizarMantenimientoModalLabel">
                        <i class="fas fa-check me-2"></i>Finalizar Mantenimiento
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="AreaComunControlador.php" id="formFinalizarMantenimiento">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="finalizarMantenimiento">
                        <input type="hidden" name="id_area" id="finalizarMantenimientoIdArea">

                        <div class="text-center">
                            <i class="fas fa-question-circle fa-3x text-warning mb-3"></i>
                            <h6>¿Está seguro que desea finalizar el mantenimiento?</h6>
                            <p class="text-muted mb-0">Área: <strong id="nombreAreaFinalizarMantenimiento"></strong></p>
                            <p class="text-muted">El área volverá a estar disponible para reservas.</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check me-2"></i>Finalizar Mantenimiento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Ver Reservas del Área -->
    <div class="modal fade" id="verReservasModal" tabindex="-1" aria-labelledby="verReservasModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="verReservasModalLabel">
                        <i class="fas fa-list me-2"></i>Ver Reservas del Área
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="GET" action="AreaComunControlador.php" id="formVerReservas">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="verReservasArea">
                        <input type="hidden" name="id_area" id="verReservasIdArea">

                        <div class="text-center">
                            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                            <h6 class="text-warning">Advertencia</h6>
                            <p class="mb-3">Está a punto de ver la información general de todas las reservas de esta área común.</p>
                            <p class="text-muted mt-3">¿Desea continuar?</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-eye me-2"></i>Continuar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Eliminar Área -->
    <div class="modal fade" id="eliminarAreaModal" tabindex="-1" aria-labelledby="eliminarAreaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eliminarAreaModalLabel">
                        <i class="fas fa-trash me-2"></i>Confirmar Eliminación
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="AreaComunControlador.php" id="formEliminarArea">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="eliminarArea">
                        <input type="hidden" name="id_area" id="eliminarAreaId">

                        <div class="text-center">
                            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                            <h6>¿Está seguro que desea eliminar el área?</h6>
                            <p class="text-muted mb-0">Área: <strong id="nombreAreaEliminar"></strong></p>
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
            var tabla = $('#tablaAreasComunes').DataTable({
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
                        targets: [7] // Columna de opciones no ordenable
                    },
                    {
                        searchable: false,
                        targets: [7] // Columna de opciones no buscable
                    },
                    {
                        width: 'auto',
                        targets: '_all'
                    }
                ],
                initComplete: function() {
                    // Personalizar el buscador
                    $('.dataTables_filter input').addClass('form-control form-control-sm');
                    $('.dataTables_filter input').attr('placeholder', 'Buscar...');

                    // Personalizar el selector de cantidad de registros
                    $('.dataTables_length select').addClass('form-select form-select-sm');
                }
            });

            // Cargar datos en el modal de editar area
            const editarAreaModal = document.getElementById('editarAreaModal');
            editarAreaModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const idArea = button.getAttribute('data-id');
                const nombre = button.getAttribute('data-nombre');
                const descripcion = button.getAttribute('data-descripcion');
                const capacidad = button.getAttribute('data-capacidad');
                const costo = button.getAttribute('data-costo');
                const estado = button.getAttribute('data-estado');

                // Llenar el formulario con los datos actuales
                document.getElementById('editIdArea').value = idArea;
                document.getElementById('editNombre').value = nombre;
                document.getElementById('editDescripcion').value = descripcion;
                document.getElementById('editCapacidad').value = capacidad;
                document.getElementById('editCosto').value = costo;
                document.getElementById('editEstado').value = estado;
            });

            // Cargar datos en el modal de mantenimiento
            const mantenimientoModal = document.getElementById('mantenimientoModal');
            mantenimientoModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const idArea = button.getAttribute('data-id');
                const nombre = button.getAttribute('data-nombre');

                document.getElementById('mantenimientoIdArea').value = idArea;
                document.getElementById('nombreAreaMantenimiento').textContent = nombre;

                // Establecer fecha mínima para fecha fin
                const fechaInicioInput = document.getElementById('fechaInicioMantenimiento');
                const fechaFinInput = document.getElementById('fechaFinMantenimiento');

                fechaInicioInput.addEventListener('change', function() {
                    fechaFinInput.min = this.value;
                    if (fechaFinInput.value && fechaFinInput.value < this.value) {
                        fechaFinInput.value = this.value;
                    }
                });
            });

            // Cargar datos en el modal de finalizar mantenimiento
            const finalizarMantenimientoModal = document.getElementById('finalizarMantenimientoModal');
            finalizarMantenimientoModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const idArea = button.getAttribute('data-id');
                const nombre = button.getAttribute('data-nombre');

                document.getElementById('finalizarMantenimientoIdArea').value = idArea;
                document.getElementById('nombreAreaFinalizarMantenimiento').textContent = nombre;
            });

            // Cargar datos en el modal de ver reservas
            const verReservasModal = document.getElementById('verReservasModal');
            verReservasModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const idArea = button.getAttribute('data-id');
                const nombre = button.getAttribute('data-nombre');

                document.getElementById('verReservasIdArea').value = idArea;
                document.getElementById('verReservasModalLabel').innerHTML = `<i class="fas fa-list me-2"></i>Ver Reservas - ${nombre}`;
            });

            // Cargar datos en el modal de eliminar area
            const eliminarAreaModal = document.getElementById('eliminarAreaModal');
            eliminarAreaModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const idArea = button.getAttribute('data-id');
                const nombre = button.getAttribute('data-nombre');

                document.getElementById('eliminarAreaId').value = idArea;
                document.getElementById('nombreAreaEliminar').textContent = nombre;
            });

            // Configurar envío de formularios para cerrar modales automáticamente
            const forms = document.querySelectorAll('#formEditarArea, #formMantenimiento, #formFinalizarMantenimiento, #formEliminarArea, #formVerReservas');
            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    const modal = bootstrap.Modal.getInstance(this.closest('.modal'));
                    if (modal) {
                        modal.hide();
                    }
                });
            });

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

        .badge {
            font-size: 0.75rem;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
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

        #tablaAreasComunes {
            width: 100% !important;
            table-layout: auto;
        }

        .dataTables_scrollBody {
            overflow-x: hidden !important;
        }

        /* Estilos para las estadísticas */
        .content-box.text-center {
            transition: transform 0.2s;
        }

        .content-box.text-center:hover {
            transform: translateY(-5px);
        }

        .content-box.text-center .fa-2x {
            margin-bottom: 10px;
        }

        .content-box.text-center h4 {
            font-weight: bold;
            color: var(--azul-oscuro);
        }
    </style>

<?php include("../../includes/footer.php"); ?>
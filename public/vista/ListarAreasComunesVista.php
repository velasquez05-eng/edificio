<?php include("../../includes/header.php"); ?>

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

    <!-- Reservas del Día -->
    <div class="row fade-in mb-4">
        <div class="col-12">
            <div class="content-box">
                <div class="content-box-header d-flex justify-content-between align-items-center">
                    <h5>
                        <i class="fas fa-calendar-day text-primary me-2"></i>
                        Reservas del Día - <?php  date_default_timezone_set('America/La_Paz'); echo date('d/m/Y'); ?>
                    </h5>
                    <div class="d-flex align-items-center">
                        <input type="date" id="fechaFiltro" class="form-control form-control-sm me-2"
                               value="<?php echo date('Y-m-d'); ?>" style="width: auto;">
                        <button class="btn btn-primary btn-sm" onclick="filtrarReservas()">
                            <i class="fas fa-filter me-1"></i>Filtrar
                        </button>
                    </div>
                </div>
                <div class="content-box-body">
                    <div id="reservasHoyContainer">
                        <!-- Las reservas se cargaran aqui via JavaScript -->
                        <div class="text-center py-4">
                            <i class="fas fa-spinner fa-spin fa-2x text-muted mb-3"></i>
                            <p class="text-muted">Cargando reservas del día...</p>
                        </div>
                    </div>
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
                                    <th>Estado</th>
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
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-warning btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editarAreaModal"
                                                        data-id="<?php echo htmlspecialchars($area['id_area']); ?>"
                                                        data-nombre="<?php echo htmlspecialchars($area['nombre']); ?>"
                                                        data-descripcion="<?php echo htmlspecialchars($area['descripcion']); ?>"
                                                        data-capacidad="<?php echo htmlspecialchars($area['capacidad']); ?>"
                                                        data-estado="<?php echo htmlspecialchars($area['estado']); ?>"
                                                        title="Editar área">
                                                    <i class="fas fa-edit"></i>
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
                <form method="POST" action="AreaComunControlador.php">
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
                            <label for="editEstado" class="form-label">
                                <i class="fas fa-toggle-on text-success me-2"></i>Estado
                            </label>
                            <select class="form-select" id="editEstado" name="estado" required>
                                <option value="disponible">Disponible</option>
                                <option value="mantenimiento">En Mantenimiento</option>
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
                <div class="modal-body">
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
                    <a href="#" id="btnConfirmarEliminar" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Eliminar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Confirmar Reserva -->
    <div class="modal fade" id="confirmarReservaModal" tabindex="-1" aria-labelledby="confirmarReservaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmarReservaModalLabel">
                        <i class="fas fa-check-circle me-2"></i>Confirmar Reserva
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="AreaComunControlador.php" id="formConfirmarReserva">
                    <input type="hidden" name="action" value="confirmarReserva">
                    <input type="hidden" name="id_area" id="confirmarIdArea">
                    <input type="hidden" name="fecha_reserva" id="confirmarFechaReserva">
                    <input type="hidden" name="hora_inicio" id="confirmarHoraInicio">
                    <div class="modal-body">
                        <div class="text-center">
                            <i class="fas fa-question-circle fa-3x text-primary mb-3"></i>
                            <h6>¿Confirmar esta reserva?</h6>
                            <div class="reserva-info bg-light p-3 rounded mt-3">
                                <p class="mb-1"><strong>Área:</strong> <span id="nombreAreaReserva"></span></p>
                                <p class="mb-1"><strong>Fecha:</strong> <span id="fechaReserva"></span></p>
                                <p class="mb-1"><strong>Horario:</strong> <span id="horarioReserva"></span></p>
                                <p class="mb-0"><strong>Persona:</strong> <span id="personaReserva"></span></p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check me-2"></i>Confirmar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Cancelar Reserva -->
    <div class="modal fade" id="cancelarReservaModal" tabindex="-1" aria-labelledby="cancelarReservaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelarReservaModalLabel">
                        <i class="fas fa-times-circle me-2"></i>Cancelar Reserva
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="AreaComunControlador.php" id="formCancelarReserva">
                    <input type="hidden" name="action" value="cancelarReserva">
                    <input type="hidden" name="id_area" id="cancelarIdArea">
                    <input type="hidden" name="fecha_reserva" id="cancelarFechaReserva">
                    <input type="hidden" name="hora_inicio" id="cancelarHoraInicio">
                    <div class="modal-body">
                        <div class="text-center">
                            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                            <h6>¿Cancelar esta reserva?</h6>
                            <div class="reserva-info bg-light p-3 rounded mt-3">
                                <p class="mb-1"><strong>Área:</strong> <span id="nombreAreaCancelar"></span></p>
                                <p class="mb-1"><strong>Fecha:</strong> <span id="fechaCancelar"></span></p>
                                <p class="mb-1"><strong>Horario:</strong> <span id="horarioCancelar"></span></p>
                                <p class="mb-0"><strong>Persona:</strong> <span id="personaCancelar"></span></p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cerrar
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-ban me-2"></i>Cancelar Reserva
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Marcar como Pendiente -->
    <div class="modal fade" id="pendienteReservaModal" tabindex="-1" aria-labelledby="pendienteReservaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pendienteReservaModalLabel">
                        <i class="fas fa-clock me-2"></i>Marcar como Pendiente
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="AreaComunControlador.php" id="formPendienteReserva">
                    <input type="hidden" name="action" value="pendienteReserva">
                    <input type="hidden" name="id_area" id="pendienteIdArea">
                    <input type="hidden" name="fecha_reserva" id="pendienteFechaReserva">
                    <input type="hidden" name="hora_inicio" id="pendienteHoraInicio">
                    <div class="modal-body">
                        <div class="text-center">
                            <i class="fas fa-question-circle fa-3x text-warning mb-3"></i>
                            <h6>¿Marcar esta reserva como pendiente?</h6>
                            <div class="reserva-info bg-light p-3 rounded mt-3">
                                <p class="mb-1"><strong>Área:</strong> <span id="nombreAreaPendiente"></span></p>
                                <p class="mb-1"><strong>Fecha:</strong> <span id="fechaPendiente"></span></p>
                                <p class="mb-1"><strong>Horario:</strong> <span id="horarioPendiente"></span></p>
                                <p class="mb-0"><strong>Persona:</strong> <span id="personaPendiente"></span></p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cerrar
                        </button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-clock me-2"></i>Marcar como Pendiente
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
                        targets: [5] // Columna de opciones no ordenable
                    },
                    {
                        searchable: false,
                        targets: [5] // Columna de opciones no buscable
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
                const estado = button.getAttribute('data-estado');

                // Llenar el formulario con los datos actuales
                document.getElementById('editIdArea').value = idArea;
                document.getElementById('editNombre').value = nombre;
                document.getElementById('editDescripcion').value = descripcion;
                document.getElementById('editCapacidad').value = capacidad;
                document.getElementById('editEstado').value = estado;
            });

            // Cargar datos en el modal de eliminar area
            const eliminarAreaModal = document.getElementById('eliminarAreaModal');
            eliminarAreaModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const idArea = button.getAttribute('data-id');
                const nombre = button.getAttribute('data-nombre');

                document.getElementById('nombreAreaEliminar').textContent = nombre;
                document.getElementById('btnConfirmarEliminar').href = `AreaComunControlador.php?action=eliminarArea&id_area=${idArea}`;
            });

            // Cargar reservas del dia al inicio
            cargarReservasDelDia();

            // Auto-ocultar alertas despues de 5 segundos
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });

        // Funcion para cargar reservas del dia
        function cargarReservasDelDia() {
            const fecha = document.getElementById('fechaFiltro').value;
            const container = document.getElementById('reservasHoyContainer');

            // Mostrar loading
            container.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-spinner fa-spin fa-2x text-muted mb-3"></i>
                    <p class="text-muted">Cargando reservas...</p>
                </div>
            `;

            // Obtener todas las reservas para la fecha seleccionada
            fetch(`AreaComunControlador.php?action=obtenerReservasPorFecha&fecha=${fecha}`)
                .then(response => response.json())
                .then(reservas => {
                    mostrarReservasEnContainer(reservas, fecha);
                })
                .catch(error => {
                    console.error('Error:', error);
                    container.innerHTML = `
                        <div class="text-center py-4">
                            <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                            <p class="text-danger">Error al cargar las reservas</p>
                        </div>
                    `;
                });
        }

        // Configuración de zona horaria
        const APP_TIMEZONE = 'America/La_Paz';

        // Función para formatear fechas
        function formatearFecha(fecha) {
            if (!fecha) return 'Fecha inválida';

            try {
                // Para formato YYYY-MM-DD (de input date)
                if (fecha.match(/^\d{4}-\d{2}-\d{2}$/)) {
                    const [year, month, day] = fecha.split('-');
                    return `${day}/${month}/${year}`;
                }

                // Para otros formatos usar Date con zona horaria
                const fechaObj = new Date(fecha);
                return fechaObj.toLocaleDateString('es-ES', {
                    timeZone: APP_TIMEZONE,
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                });
            } catch (error) {
                return fecha;
            }
        }
        // Funcion para mostrar reservas en el contenedor
        function mostrarReservasEnContainer(reservas, fecha) {
            const container = document.getElementById('reservasHoyContainer');

            if (!reservas || reservas.length === 0) {
                // Formatear la fecha con zona horaria
                const fechaFormateada = formatearFecha(fecha);

                container.innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <p class="text-muted">No hay reservas para el ${fechaFormateada}</p>
            </div>
        `;
                return;
            }

            let html = `
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Área</th>
                                <th>Horario</th>
                                <th>Persona</th>
                                <th>CI</th>
                                <th>Email</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            reservas.forEach(reserva => {
                const nombreCompleto = `${reserva.nombre_persona} ${reserva.apellido_persona}`;
                let badgeClass = '';
                switch(reserva.estado) {
                    case 'confirmada': badgeClass = 'bg-success'; break;
                    case 'pendiente': badgeClass = 'bg-warning'; break;
                    case 'cancelada': badgeClass = 'bg-danger'; break;
                    default: badgeClass = 'bg-secondary';
                }

                html += `
                    <tr>
                        <td>
                            <i class="fas fa-map-marker-alt text-primary me-1"></i>
                            ${reserva.area_nombre}
                        </td>
                        <td>
                            <i class="fas fa-clock text-info me-1"></i>
                            ${reserva.hora_inicio} - ${reserva.hora_fin}
                        </td>
                        <td>${nombreCompleto}</td>
                        <td>${reserva.ci || 'N/A'}</td>
                        <td>${reserva.email || 'N/A'}</td>
                        <td>
                            <span class="badge ${badgeClass}">
                                ${reserva.estado.charAt(0).toUpperCase() + reserva.estado.slice(1)}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                ${reserva.estado !== 'confirmada' ? `
                                    <button class="btn btn-success btn-sm"
                                            onclick="mostrarModalConfirmarReserva(
                                                '${reserva.id_area}',
                                                '${reserva.area_nombre}',
                                                '${reserva.fecha_reserva}',
                                                '${reserva.hora_inicio}',
                                                '${reserva.hora_fin}',
                                                '${nombreCompleto}'
                                            )" title="Confirmar reserva">
                                        <i class="fas fa-check"></i>
                                    </button>
                                ` : ''}
                                ${reserva.estado !== 'cancelada' ? `
                                    <button class="btn btn-danger btn-sm"
                                            onclick="mostrarModalCancelarReserva(
                                                '${reserva.id_area}',
                                                '${reserva.area_nombre}',
                                                '${reserva.fecha_reserva}',
                                                '${reserva.hora_inicio}',
                                                '${reserva.hora_fin}',
                                                '${nombreCompleto}'
                                            )" title="Cancelar reserva">
                                        <i class="fas fa-times"></i>
                                    </button>
                                ` : ''}
                                ${reserva.estado !== 'pendiente' ? `
                                    <button class="btn btn-warning btn-sm"
                                            onclick="mostrarModalPendienteReserva(
                                                '${reserva.id_area}',
                                                '${reserva.area_nombre}',
                                                '${reserva.fecha_reserva}',
                                                '${reserva.hora_inicio}',
                                                '${reserva.hora_fin}',
                                                '${nombreCompleto}'
                                            )" title="Marcar como pendiente">
                                        <i class="fas fa-clock"></i>
                                    </button>
                                ` : ''}
                            </div>
                        </td>
                    </tr>
                `;
            });

            html += `
                        </tbody>
                    </table>
                </div>
            `;

            container.innerHTML = html;
        }

        // Funcion para filtrar reservas por fecha
        function filtrarReservas() {
            cargarReservasDelDia();
        }

        // Funcion para mostrar modal de confirmar reserva
        function mostrarModalConfirmarReserva(idArea, nombreArea, fecha, horaInicio, horaFin, persona) {
            document.getElementById('nombreAreaReserva').textContent = nombreArea;
            document.getElementById('fechaReserva').textContent = new Date(fecha).toLocaleDateString('es-ES');
            document.getElementById('horarioReserva').textContent = `${horaInicio} - ${horaFin}`;
            document.getElementById('personaReserva').textContent = persona;

            // Llenar el formulario
            document.getElementById('confirmarIdArea').value = idArea;
            document.getElementById('confirmarFechaReserva').value = fecha;
            document.getElementById('confirmarHoraInicio').value = horaInicio;

            // Mostrar el modal
            const modal = new bootstrap.Modal(document.getElementById('confirmarReservaModal'));
            modal.show();
        }

        // Funcion para mostrar modal de cancelar reserva
        function mostrarModalCancelarReserva(idArea, nombreArea, fecha, horaInicio, horaFin, persona) {
            document.getElementById('nombreAreaCancelar').textContent = nombreArea;
            document.getElementById('fechaCancelar').textContent = new Date(fecha).toLocaleDateString('es-ES');
            document.getElementById('horarioCancelar').textContent = `${horaInicio} - ${horaFin}`;
            document.getElementById('personaCancelar').textContent = persona;

            // Llenar el formulario
            document.getElementById('cancelarIdArea').value = idArea;
            document.getElementById('cancelarFechaReserva').value = fecha;
            document.getElementById('cancelarHoraInicio').value = horaInicio;

            // Mostrar el modal
            const modal = new bootstrap.Modal(document.getElementById('cancelarReservaModal'));
            modal.show();
        }
        // Funcion para mostrar modal de marcar como pendiente
        function mostrarModalPendienteReserva(idArea, nombreArea, fecha, horaInicio, horaFin, persona) {
            document.getElementById('nombreAreaPendiente').textContent = nombreArea;
            document.getElementById('fechaPendiente').textContent = new Date(fecha).toLocaleDateString('es-ES');
            document.getElementById('horarioPendiente').textContent = `${horaInicio} - ${horaFin}`;
            document.getElementById('personaPendiente').textContent = persona;

            // Llenar el formulario
            document.getElementById('pendienteIdArea').value = idArea;
            document.getElementById('pendienteFechaReserva').value = fecha;
            document.getElementById('pendienteHoraInicio').value = horaInicio;

            // Mostrar el modal
            const modal = new bootstrap.Modal(document.getElementById('pendienteReservaModal'));
            modal.show();
        }
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

        .area-info {
            border-left: 4px solid var(--azul-oscuro);
        }

        .reserva-info {
            border-left: 4px solid var(--azul-oscuro);
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

        /* Estilos para el formulario de edicion */
        .form-label {
            font-weight: 600;
            color: var(--azul-oscuro);
        }

        .form-control, .form-select {
            border-radius: 0.5rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--azul-oscuro);
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        /* Estilos para la tabla de reservas del dia */
        #reservasHoyContainer table {
            font-size: 0.875rem;
        }

        #reservasHoyContainer .table th {
            background-color: #e9ecef;
        }
    </style>

<?php include("../../includes/footer.php"); ?>
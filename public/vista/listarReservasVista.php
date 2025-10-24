<?php include("../../includes/header.php"); ?>

    <!-- Page Header -->
    <div class="page-header fade-in">
        <div class="page-title">
            <h1>Mis Reservas</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item"><a href="AreaComunControlador.php?action=listarAreas">Áreas Comunes</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Mis Reservas</li>
                </ol>
            </nav>
        </div>
        <div class="page-actions">
            <a href="AreaComunControlador.php?action=listarAreas" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver a Áreas
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

    <!-- Estadísticas de Mis Reservas -->
    <div class="row fade-in mb-4">
        <div class="col-md-3">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                    <h4 class="mb-1">
                        <?php
                        $reservasPendientes = array_filter($reservas, function($reserva) {
                            return $reserva['estado'] == 'pendiente';
                        });
                        echo count($reservasPendientes);
                        ?>
                    </h4>
                    <p class="text-muted mb-0">Pendientes</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                    <h4 class="mb-1">
                        <?php
                        $reservasConfirmadas = array_filter($reservas, function($reserva) {
                            return $reserva['estado'] == 'confirmada';
                        });
                        echo count($reservasConfirmadas);
                        ?>
                    </h4>
                    <p class="text-muted mb-0">Confirmadas</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-times-circle fa-2x text-danger mb-2"></i>
                    <h4 class="mb-1">
                        <?php
                        $reservasCanceladas = array_filter($reservas, function($reserva) {
                            return $reserva['estado'] == 'cancelada';
                        });
                        echo count($reservasCanceladas);
                        ?>
                    </h4>
                    <p class="text-muted mb-0">Canceladas</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-calendar-alt fa-2x text-info mb-2"></i>
                    <h4 class="mb-1">
                        <?php echo count($reservas); ?>
                    </h4>
                    <p class="text-muted mb-0">Total Reservas</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Mis Reservas -->
    <div class="row fade-in">
        <div class="col-12">
            <div class="content-box">
                <div class="content-box-header d-flex justify-content-between align-items-center">
                    <h5>Lista de Mis Reservas</h5>
                    <div>
                        <span class="badge bg-warning"><?php echo count($reservasPendientes); ?> pendientes</span>
                        <span class="badge bg-success"><?php echo count($reservasConfirmadas); ?> confirmadas</span>
                        <span class="badge bg-danger"><?php echo count($reservasCanceladas); ?> canceladas</span>
                    </div>
                </div>
                <div class="content-box-body">
                    <?php if (empty($reservas)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No tienes reservas realizadas</p>
                            <a href="AreaComunControlador.php?action=formularioReservaArea" class="btn btn-primary mt-2">
                                <i class="fas fa-plus me-2"></i>Realizar una Reserva
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-container">
                            <table id="tablaMisReservas" class="table table-hover table-striped">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th><i class="fas fa-map-marker-alt text-primary me-2"></i>Área Común</th>
                                    <th><i class="fas fa-calendar-day text-success me-2"></i>Fecha Reserva</th>
                                    <th><i class="fas fa-clock text-warning me-2"></i>Horario</th>
                                    <th><i class="fas fa-money-bill-wave text-info me-2"></i>Costo por Hora</th>
                                    <th><i class="fas fa-calculator text-primary me-2"></i>Costo Total</th>
                                    <th><i class="fas fa-info-circle me-2"></i>Estado</th>
                                    <th>Motivo</th>
                                    <th>Acciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                // Ordenar reservas: primero pendientes, luego confirmadas, luego canceladas
                                usort($reservas, function($a, $b) {
                                    $ordenEstados = ['pendiente' => 1, 'confirmada' => 2, 'cancelada' => 3];
                                    return $ordenEstados[$a['estado']] - $ordenEstados[$b['estado']];
                                });

                                foreach ($reservas as $index => $reserva):
                                    // Calcular horas de diferencia
                                    $horaInicio = new DateTime($reserva['hora_inicio']);
                                    $horaFin = new DateTime($reserva['hora_fin']);
                                    $diferencia = $horaInicio->diff($horaFin);
                                    $horas = $diferencia->h + ($diferencia->i / 60);

                                    // Calcular costo total
                                    $costoPorHora = floatval($reserva['costo_reserva']);
                                    $costoTotal = $horas * $costoPorHora;

                                    // Determinar clase del badge según el estado
                                    $badgeClass = '';
                                    switch($reserva['estado']) {
                                        case 'pendiente': $badgeClass = 'bg-warning'; break;
                                        case 'confirmada': $badgeClass = 'bg-success'; break;
                                        case 'cancelada': $badgeClass = 'bg-danger'; break;
                                        default: $badgeClass = 'bg-secondary';
                                    }
                                    ?>
                                    <tr>
                                        <td><strong><?php echo $index + 1; ?></strong></td>
                                        <td>
                                            <?php echo htmlspecialchars($reserva['nombre_area']); ?>
                                        </td>
                                        <td>
                                            <?php echo date('d/m/Y', strtotime($reserva['fecha_reserva'])); ?>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($reserva['hora_inicio']." - ".$reserva['hora_fin']); ?>
                                        </td>
                                        <td>
                                            Bs. <?php echo number_format($costoPorHora, 2); ?>
                                        </td>
                                        <td>
                                            <strong>Bs. <?php echo number_format($costoTotal, 2); ?></strong>
                                            <small class="text-muted d-block"><?php echo number_format($horas, 1); ?> horas</small>
                                        </td>
                                        <td>
                                            <span class="badge <?php echo $badgeClass; ?>">
                                                <?php echo ucfirst($reserva['estado']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($reserva['motivo']): ?>
                                                <button class="btn btn-info btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#verMotivoModal"
                                                        data-motivo="<?php echo htmlspecialchars($reserva['motivo']); ?>"
                                                        data-area="<?php echo htmlspecialchars($reserva['nombre_area']); ?>"
                                                        data-fecha="<?php echo date('d/m/Y', strtotime($reserva['fecha_reserva'])); ?>"
                                                        data-horario="<?php echo htmlspecialchars($reserva['hora_inicio'] . ' - ' . $reserva['hora_fin']); ?>"
                                                        data-estado="<?php echo ucfirst($reserva['estado']); ?>"
                                                        data-costo="<?php echo 'Bs. ' . number_format($costoTotal, 2); ?>"
                                                        title="Ver motivo">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            <?php else: ?>
                                                <span class="text-muted">Sin motivo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <?php if ($reserva['estado'] == 'pendiente'): ?>
                                                    <!-- Modificar reserva (solo pendientes) - MODAL -->
                                                    <button class="btn btn-warning btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modificarReservaModal"
                                                            data-id-persona="<?php echo htmlspecialchars($reserva['id_persona']); ?>"
                                                            data-id-area="<?php echo htmlspecialchars($reserva['id_area']); ?>"
                                                            data-fecha-reserva="<?php echo htmlspecialchars($reserva['fecha_reserva']); ?>"
                                                            data-hora-inicio="<?php echo htmlspecialchars($reserva['hora_inicio']); ?>"
                                                            data-hora-fin="<?php echo htmlspecialchars($reserva['hora_fin']); ?>"
                                                            data-area-nombre="<?php echo htmlspecialchars($reserva['nombre_area']); ?>"
                                                            data-costo-hora="<?php echo htmlspecialchars($costoPorHora); ?>"
                                                            title="Modificar fecha y horario">
                                                        <i class="fas fa-edit"></i>
                                                    </button>

                                                    <!-- Cancelar reserva (solo pendientes) -->
                                                    <button class="btn btn-danger btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#cancelarReservaModal"
                                                            data-id-persona="<?php echo htmlspecialchars($reserva['id_persona']); ?>"
                                                            data-id-area="<?php echo htmlspecialchars($reserva['id_area']); ?>"
                                                            data-fecha-reserva="<?php echo htmlspecialchars($reserva['fecha_reserva']); ?>"
                                                            data-hora-inicio="<?php echo htmlspecialchars($reserva['hora_inicio']); ?>"
                                                            data-area-nombre="<?php echo htmlspecialchars($reserva['nombre_area']); ?>"
                                                            data-fecha="<?php echo date('d/m/Y', strtotime($reserva['fecha_reserva'])); ?>"
                                                            data-horario="<?php echo htmlspecialchars($reserva['hora_inicio'] . ' - ' . $reserva['hora_fin']); ?>"
                                                            title="Cancelar reserva">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <!-- Espacio para mantener consistencia cuando no hay acciones -->
                                                    <span class="text-muted small">No disponible</span>
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

    <!-- Modal Ver Motivo -->
    <div class="modal fade" id="verMotivoModal" tabindex="-1" aria-labelledby="verMotivoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="verMotivoModalLabel">
                        <i class="fas fa-eye me-2"></i>Detalles de la Reserva
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="reserva-info bg-light p-3 rounded mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Área:</strong> <span id="modalArea"></span></p>
                                <p class="mb-2"><strong>Fecha:</strong> <span id="modalFecha"></span></p>
                                <p class="mb-2"><strong>Horario:</strong> <span id="modalHorario"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Estado:</strong> <span id="modalEstado"></span></p>
                                <p class="mb-2"><strong>Costo Total:</strong> <span id="modalCosto"></span></p>
                            </div>
                        </div>
                    </div>
                    <div class="motivo-content">
                        <label class="form-label fw-bold">Motivo de la Reserva:</label>
                        <div class="p-3 bg-white rounded border motivo-text-container" style="max-height: 300px; overflow-y: auto;">
                            <p class="mb-0" id="modalMotivo"></p>
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

    <!-- Modal Modificar Reserva -->
    <!-- Modal Modificar Reserva -->
    <div class="modal fade" id="modificarReservaModal" tabindex="-1" aria-labelledby="modificarReservaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modificarReservaModalLabel">
                        <i class="fas fa-edit me-2"></i>Modificar Reserva
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="AreaComunControlador.php" id="formModificarReserva">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="modificarReservaUsuario">
                        <input type="hidden" name="id_persona" id="modificarIdPersona">
                        <input type="hidden" name="id_area" id="modificarIdArea">
                        <input type="hidden" name="fecha_reserva_original" id="modificarFechaOriginal">
                        <input type="hidden" name="hora_inicio_original" id="modificarHoraInicioOriginal">

                        <div class="reserva-info bg-light p-3 rounded mb-3">
                            <p class="mb-2"><strong>Área:</strong> <span id="modificarArea"></span></p>
                            <p class="mb-2"><strong>Costo por Hora:</strong> Bs. <span id="modificarCostoHora"></span></p>
                            <p class="mb-0 text-info"><small><i class="fas fa-info-circle me-1"></i>Solo puedes modificar la fecha y el horario</small></p>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="modificarFecha" class="form-label">Nueva Fecha <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="modificarFecha" name="fecha_reserva" required min="<?php echo date('Y-m-d'); ?>">
                                    <div class="form-text">Selecciona la nueva fecha para la reserva</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="modificarHoraInicio" class="form-label">Nueva Hora Inicio <span class="text-danger">*</span></label>
                                    <select class="form-select" id="modificarHoraInicio" name="hora_inicio" required>
                                        <option value="">Seleccione...</option>
                                        <!-- Las opciones se generarán dinámicamente con JavaScript -->
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="modificarHoraFin" class="form-label">Nueva Hora Fin <span class="text-danger">*</span></label>
                                    <select class="form-select" id="modificarHoraFin" name="hora_fin" required>
                                        <option value="">Seleccione...</option>
                                        <!-- Las opciones se generarán dinámicamente con JavaScript -->
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Duración</label>
                                    <div class="p-2 bg-light rounded">
                                        <span id="modificarDuracion">0 horas</span><br>
                                        <small id="modificarCostoTotal" class="text-muted">Costo total: Bs. 0.00</small>
                                    </div>
                                </div>
                            </div>
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
                    <div class="modal-body">
                        <input type="hidden" name="action" value="cancelarReservaUsuario">
                        <input type="hidden" name="id_persona" id="cancelarIdPersona">
                        <input type="hidden" name="id_area" id="cancelarIdArea">
                        <input type="hidden" name="fecha_reserva" id="cancelarFecha">
                        <input type="hidden" name="hora_inicio" id="cancelarHoraInicio">

                        <div class="text-center mb-3">
                            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-2"></i>
                            <h6>¿Está seguro que desea cancelar esta reserva?</h6>
                        </div>

                        <div class="reserva-info bg-light p-3 rounded">
                            <p class="mb-2"><strong>Área:</strong> <span id="cancelarArea"></span></p>
                            <p class="mb-2"><strong>Fecha:</strong> <span id="cancelarFecha"></span></p>
                            <p class="mb-2"><strong>Horario:</strong> <span id="cancelarHorario"></span></p>
                            <p class="mb-0 text-danger"><strong>Esta acción no se puede deshacer</strong></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>No Cancelar
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-times-circle me-2"></i>Sí, Cancelar Reserva
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

    <!-- Script para DataTable y funcionalidades -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar DataTable
            var tabla = $('#tablaMisReservas').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                responsive: false,
                scrollX: false,
                autoWidth: false,
                dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                pageLength: 10,
                lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
                order: [[2, 'asc']], // Ordenar por fecha
                columnDefs: [
                    { orderable: false, targets: [8] },
                    { searchable: false, targets: [8] }
                ],
                initComplete: function() {
                    $('.dataTables_filter input').addClass('form-control form-control-sm');
                    $('.dataTables_filter input').attr('placeholder', 'Buscar...');
                    $('.dataTables_length select').addClass('form-select form-select-sm');
                }
            });

            // Modal Ver Motivo
            const verMotivoModal = document.getElementById('verMotivoModal');
            verMotivoModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                document.getElementById('modalMotivo').textContent = button.getAttribute('data-motivo');
                document.getElementById('modalArea').textContent = button.getAttribute('data-area');
                document.getElementById('modalFecha').textContent = button.getAttribute('data-fecha');
                document.getElementById('modalHorario').textContent = button.getAttribute('data-horario');
                document.getElementById('modalEstado').textContent = button.getAttribute('data-estado');
                document.getElementById('modalCosto').textContent = button.getAttribute('data-costo');
            });

            // Modal Modificar Reserva
            const modificarReservaModal = document.getElementById('modificarReservaModal');
            modificarReservaModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const idPersona = button.getAttribute('data-id-persona');
                const idArea = button.getAttribute('data-id-area');
                const fechaReserva = button.getAttribute('data-fecha-reserva');
                const horaInicio = button.getAttribute('data-hora-inicio'); // Formato: 14:00:00
                const horaFin = button.getAttribute('data-hora-fin'); // Formato: 16:00:00
                const areaNombre = button.getAttribute('data-area-nombre');
                const costoHora = button.getAttribute('data-costo-hora');

                console.log('Hora inicio original:', horaInicio);
                console.log('Hora fin original:', horaFin);

                // Llenar campos hidden
                document.getElementById('modificarIdPersona').value = idPersona;
                document.getElementById('modificarIdArea').value = idArea;
                document.getElementById('modificarFechaOriginal').value = fechaReserva;
                document.getElementById('modificarHoraInicioOriginal').value = horaInicio;

                // Llenar información visible
                document.getElementById('modificarArea').textContent = areaNombre;
                document.getElementById('modificarCostoHora').textContent = parseFloat(costoHora).toFixed(2);

                // Llenar fecha
                document.getElementById('modificarFecha').value = fechaReserva;

                // Convertir horas a formato sin segundos (14:00:00 -> 14:00)
                const horaInicioSimple = horaInicio.substring(0, 5); // Toma solo HH:mm
                const horaFinSimple = horaFin.substring(0, 5); // Toma solo HH:mm

                console.log('Hora inicio simple:', horaInicioSimple);
                console.log('Hora fin simple:', horaFinSimple);

                // Generar opciones de hora inicio (8–21) y fin (9–22)
                const horaInicioSelect = document.getElementById('modificarHoraInicio');
                const horaFinSelect = document.getElementById('modificarHoraFin');

                // Limpiar selects
                horaInicioSelect.innerHTML = '<option value="">Seleccione...</option>';
                horaFinSelect.innerHTML = '<option value="">Seleccione...</option>';

                // Generar opciones para hora inicio (8:00 - 21:00)
                for (let h = 8; h <= 21; h++) {
                    const hora = `${String(h).padStart(2, '0')}:00`;
                    const option = document.createElement('option');
                    option.value = hora;
                    option.textContent = hora;
                    horaInicioSelect.appendChild(option);
                }

                // Generar opciones para hora fin (9:00 - 22:00)
                for (let h = 9; h <= 22; h++) {
                    const hora = `${String(h).padStart(2, '0')}:00`;
                    const option = document.createElement('option');
                    option.value = hora;
                    option.textContent = hora;
                    horaFinSelect.appendChild(option);
                }

                // ESTABLECER LOS VALORES POR DEFECTO
                horaInicioSelect.value = horaInicioSimple;
                horaFinSelect.value = horaFinSimple;

                console.log('Hora inicio seleccionada:', horaInicioSelect.value);
                console.log('Hora fin seleccionada:', horaFinSelect.value);

                // Filtrar horas fin válidas basadas en la hora inicio seleccionada
                filtrarHorasFin();

                // Calcular duración inicial
                calcularDuracionYCosto();
            });

            // Función para filtrar horas fin válidas
            function filtrarHorasFin() {
                const horaInicioSelect = document.getElementById('modificarHoraInicio');
                const horaFinSelect = document.getElementById('modificarHoraFin');
                const horaInicio = horaInicioSelect.value;

                if (horaInicio) {
                    const hInicio = parseInt(horaInicio.split(':')[0]);

                    Array.from(horaFinSelect.options).forEach(opt => {
                        if (!opt.value) return; // Saltar la opción "Seleccione..."
                        const hFin = parseInt(opt.value.split(':')[0]);
                        opt.disabled = hFin <= hInicio;
                    });

                    // Si la hora fin actual está deshabilitada, buscar la siguiente hora disponible
                    const horaFinActual = horaFinSelect.value;
                    if (horaFinActual) {
                        const hFinActual = parseInt(horaFinActual.split(':')[0]);
                        if (hFinActual <= hInicio) {
                            // Buscar la primera hora disponible después de la hora inicio
                            for (let h = hInicio + 1; h <= 22; h++) {
                                const horaDisponible = `${String(h).padStart(2, '0')}:00`;
                                const option = horaFinSelect.querySelector(`option[value="${horaDisponible}"]`);
                                if (option && !option.disabled) {
                                    horaFinSelect.value = horaDisponible;
                                    break;
                                }
                            }
                        }
                    }
                }
            }

            // Función para calcular duración y costo
            function calcularDuracionYCosto() {
                const horaInicio = document.getElementById('modificarHoraInicio').value;
                const horaFin = document.getElementById('modificarHoraFin').value;
                const costoHora = parseFloat(document.getElementById('modificarCostoHora').textContent);
                const duracionEl = document.getElementById('modificarDuracion');
                const costoEl = document.getElementById('modificarCostoTotal');

                if (horaInicio && horaFin) {
                    const hInicio = parseInt(horaInicio.split(':')[0]);
                    const hFin = parseInt(horaFin.split(':')[0]);

                    if (hFin <= hInicio) {
                        duracionEl.textContent = 'Hora fin debe ser mayor a inicio';
                        duracionEl.className = 'text-danger';
                        costoEl.textContent = 'Costo total: Bs. 0.00';
                        document.getElementById('modificarHoraFin').setCustomValidity('La hora fin debe ser mayor a la de inicio');
                    } else {
                        const horas = hFin - hInicio;
                        const costoTotal = horas * costoHora;
                        duracionEl.textContent = `${horas} hora${horas > 1 ? 's' : ''}`;
                        duracionEl.className = '';
                        costoEl.textContent = `Costo total: Bs. ${costoTotal.toFixed(2)}`;
                        document.getElementById('modificarHoraFin').setCustomValidity('');
                    }
                } else {
                    duracionEl.textContent = '0 horas';
                    duracionEl.className = '';
                    costoEl.textContent = 'Costo total: Bs. 0.00';
                }
            }

            // Listeners para actualizar duración y filtrar horas válidas
            document.getElementById('modificarHoraInicio').addEventListener('change', function() {
                filtrarHorasFin();
                calcularDuracionYCosto();
            });

            document.getElementById('modificarHoraFin').addEventListener('change', calcularDuracionYCosto);

            // Modal Cancelar Reserva
            const cancelarReservaModal = document.getElementById('cancelarReservaModal');
            cancelarReservaModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const idPersona = button.getAttribute('data-id-persona');
                const idArea = button.getAttribute('data-id-area');
                const fechaReserva = button.getAttribute('data-fecha-reserva');
                const horaInicio = button.getAttribute('data-hora-inicio');
                const areaNombre = button.getAttribute('data-area-nombre');
                const fecha = button.getAttribute('data-fecha');
                const horario = button.getAttribute('data-horario');

                // Llenar campos hidden
                document.getElementById('cancelarIdPersona').value = idPersona;
                document.getElementById('cancelarIdArea').value = idArea;
                document.getElementById('cancelarFecha').value = fechaReserva;
                document.getElementById('cancelarHoraInicio').value = horaInicio;

                // Llenar información visible
                document.getElementById('cancelarArea').textContent = areaNombre;
                document.getElementById('cancelarFecha').textContent = fecha;
                document.getElementById('cancelarHorario').textContent = horario;
            });

            // Auto-ocultar alertas después de 5 segundos
            setTimeout(function() {
                document.querySelectorAll('.alert').forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);

            // Validación del formulario de modificación
            document.getElementById('formModificarReserva').addEventListener('submit', function(e) {
                const horaInicio = document.getElementById('modificarHoraInicio').value;
                const horaFin = document.getElementById('modificarHoraFin').value;

                if (horaInicio && horaFin) {
                    const hInicio = parseInt(horaInicio.split(':')[0]);
                    const hFin = parseInt(horaFin.split(':')[0]);

                    if (hFin <= hInicio) {
                        e.preventDefault();
                        alert('La hora de fin debe ser mayor a la hora de inicio');
                        return false;
                    }
                }
            });
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
            background: #007bff;
            border-color: #007bff;
            color: white !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #e9ecef;
            border-color: #007bff;
        }

        #tablaMisReservas {
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

        .reserva-info {
            border-left: 4px solid #007bff;
        }

        .motivo-text-container {
            max-height: 300px;
            overflow-y: auto;
            word-wrap: break-word;
            white-space: pre-wrap;
        }

        @media (max-width: 768px) {
            .modal-lg {
                max-width: 95%;
            }
        }
    </style>

<?php include("../../includes/footer.php"); ?>
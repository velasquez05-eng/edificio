<?php include("../../includes/header.php"); ?>

    <!-- Page Header -->
    <div class="page-header fade-in">
        <div class="page-title">
            <h1>Reservas del Área Común</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item"><a href="AreaComunControlador.php?action=listarAreas">Áreas Comunes</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Reservas del Área</li>
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

    <!-- Información del Área -->
    <div class="row fade-in mb-4">
        <div class="col-12">
            <div class="content-box">
                <div class="content-box-header d-flex justify-content-between align-items-center">
                    <h5>Información del Área Común</h5>
                    <span class="badge
                <?php
                    switch($area['estado']) {
                        case 'disponible': echo 'bg-success'; break;
                        case 'mantenimiento': echo 'bg-warning'; break;
                        case 'no disponible': echo 'bg-danger'; break;
                        default: echo 'bg-secondary';
                    }
                    ?>">
                <?php echo ucfirst(htmlspecialchars($area['estado'])); ?>
            </span>
                </div>
                <div class="content-box-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-primary">Nombre del Área:</label>
                                <h4><?php echo htmlspecialchars($area['nombre']); ?></h4>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-primary">Descripción:</label>
                                <p class="mb-0"><?php echo htmlspecialchars($area['descripcion'] ?: 'Sin descripción'); ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-primary">Capacidad:</label>
                                <h4>
                                    <i class="fas fa-users text-info me-2"></i>
                                    <?php echo htmlspecialchars($area['capacidad']); ?> personas
                                </h4>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-primary">Precio de Reserva:</label>
                                <h4>
                                    <i class="fas fa-money-bill-wave text-success me-2"></i>
                                    Bs. <?php echo number_format($area['costo_reserva'], 2); ?>
                                </h4>
                            </div>
                        </div>
                    </div>
                    <?php if ($area['fecha_inicio_mantenimiento'] && $area['fecha_fin_mantenimiento']): ?>
                        <div class="alert alert-warning mt-3">
                            <i class="fas fa-tools me-2"></i>
                            <strong>En Mantenimiento:</strong>
                            Desde <?php echo date('d/m/Y', strtotime($area['fecha_inicio_mantenimiento'])); ?>
                            hasta <?php echo date('d/m/Y', strtotime($area['fecha_fin_mantenimiento'])); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas de Reservas -->
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
                    <i class="fas fa-calendar-alt fa-2x text-primary mb-2"></i>
                    <h4 class="mb-1"><?php echo count($reservas); ?></h4>
                    <p class="text-muted mb-0">Total Reservas</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Reservas -->
    <div class="row fade-in">
        <div class="col-12">
            <div class="content-box">
                <div class="content-box-header d-flex justify-content-between align-items-center">
                    <h5>Lista de Reservas</h5>
                    <span class="badge bg-primary"><?php echo count($reservas); ?> reservas</span>
                </div>
                <div class="content-box-body">
                    <?php if (empty($reservas)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No hay reservas registradas para esta área</p>
                        </div>
                    <?php else: ?>
                        <div class="table-container">
                            <table id="tablaReservas" class="table table-hover table-striped">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th><i class="fas fa-user text-info me-2"></i>Persona</th>
                                    <th><i class="fas fa-id-card text-secondary me-2"></i>CI</th>
                                    <th><i class="fas fa-envelope text-primary me-2"></i>Email</th>
                                    <th><i class="fas fa-building text-warning me-2"></i>Departamento</th>
                                    <th><i class="fas fa-calendar-day text-success me-2"></i>Fecha Reserva</th>
                                    <th><i class="fas fa-clock text-warning me-2"></i>Horario</th>
                                    <th>Estado</th>
                                    <th>Motivo</th>
                                    <th>Acciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($reservas as $index => $reserva): ?>
                                    <tr>
                                        <td><strong><?php echo $index + 1; ?></strong></td>
                                        <td>
                                            <?php
                                            $nombreCompleto = htmlspecialchars($reserva['nombre_persona']) . ' ' .
                                                    htmlspecialchars($reserva['apellido_paterno']);
                                            if (!empty($reserva['apellido_materno'])) {
                                                $nombreCompleto .= ' ' . htmlspecialchars($reserva['apellido_materno']);
                                            }
                                            echo $nombreCompleto;
                                            ?>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($reserva['ci']); ?>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($reserva['email'] ?: 'No especificado'); ?>
                                        </td>
                                        <td>
                                            <?php
                                            echo htmlspecialchars($reserva['departamentos_vinculados'] ?: 'No especificado');
                                            ?>
                                        </td>
                                        <td>
                                            <?php echo date('d/m/Y', strtotime($reserva['fecha_reserva'])); ?>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($reserva['hora_inicio']." - ".$reserva['hora_fin']); ?>
                                        </td>
                                        <td>
                                            <?php
                                            $badge_class = '';
                                            switch($reserva['estado']) {
                                                case 'confirmada':
                                                    $badge_class = 'bg-success';
                                                    $icon = 'fa-check-circle';
                                                    break;
                                                case 'pendiente':
                                                    $badge_class = 'bg-warning';
                                                    $icon = 'fa-clock';
                                                    break;
                                                case 'cancelada':
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
                                            <?php echo ucfirst(htmlspecialchars($reserva['estado'])); ?>
                                        </span>
                                        </td>
                                        <td>
                                            <?php if ($reserva['motivo']): ?>
                                                <button class="btn btn-info btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#verMotivoModal"
                                                        data-motivo="<?php echo htmlspecialchars($reserva['motivo']); ?>"
                                                        data-persona="<?php echo htmlspecialchars($nombreCompleto); ?>"
                                                        data-fecha="<?php echo date('d/m/Y', strtotime($reserva['fecha_reserva'])); ?>"
                                                        data-horario="<?php echo htmlspecialchars($reserva['hora_inicio'] . ' - ' . $reserva['hora_fin']); ?>"
                                                        data-departamento="<?php echo htmlspecialchars($reserva['departamentos_vinculados']); ?>"
                                                        title="Ver motivo">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            <?php else: ?>
                                                <span class="text-muted">Sin motivo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <!-- Cambiar a Confirmada -->
                                                <?php if ($reserva['estado'] != 'confirmada'): ?>
                                                    <button class="btn btn-success btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#cambiarEstadoModal"
                                                            data-id-persona="<?php echo htmlspecialchars($reserva['id_persona']); ?>"
                                                            data-id-area="<?php echo htmlspecialchars($reserva['id_area']); ?>"
                                                            data-fecha-reserva="<?php echo htmlspecialchars($reserva['fecha_reserva']); ?>"
                                                            data-hora-inicio="<?php echo htmlspecialchars($reserva['hora_inicio']); ?>"
                                                            data-persona="<?php echo htmlspecialchars($nombreCompleto); ?>"
                                                            data-estado-actual="<?php echo htmlspecialchars($reserva['estado']); ?>"
                                                            data-nuevo-estado="confirmada"
                                                            title="Confirmar reserva">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                <?php endif; ?>

                                                <!-- Cambiar a Pendiente -->
                                                <?php if ($reserva['estado'] != 'pendiente'): ?>
                                                    <button class="btn btn-warning btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#cambiarEstadoModal"
                                                            data-id-persona="<?php echo htmlspecialchars($reserva['id_persona']); ?>"
                                                            data-id-area="<?php echo htmlspecialchars($reserva['id_area']); ?>"
                                                            data-fecha-reserva="<?php echo htmlspecialchars($reserva['fecha_reserva']); ?>"
                                                            data-hora-inicio="<?php echo htmlspecialchars($reserva['hora_inicio']); ?>"
                                                            data-persona="<?php echo htmlspecialchars($nombreCompleto); ?>"
                                                            data-estado-actual="<?php echo htmlspecialchars($reserva['estado']); ?>"
                                                            data-nuevo-estado="pendiente"
                                                            title="Marcar como pendiente">
                                                        <i class="fas fa-clock"></i>
                                                    </button>
                                                <?php endif; ?>

                                                <!-- Cambiar a Cancelada -->
                                                <?php if ($reserva['estado'] != 'cancelada'): ?>
                                                    <button class="btn btn-danger btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#cambiarEstadoModal"
                                                            data-id-persona="<?php echo htmlspecialchars($reserva['id_persona']); ?>"
                                                            data-id-area="<?php echo htmlspecialchars($reserva['id_area']); ?>"
                                                            data-fecha-reserva="<?php echo htmlspecialchars($reserva['fecha_reserva']); ?>"
                                                            data-hora-inicio="<?php echo htmlspecialchars($reserva['hora_inicio']); ?>"
                                                            data-persona="<?php echo htmlspecialchars($nombreCompleto); ?>"
                                                            data-estado-actual="<?php echo htmlspecialchars($reserva['estado']); ?>"
                                                            data-nuevo-estado="cancelada"
                                                            title="Cancelar reserva">
                                                        <i class="fas fa-times"></i>
                                                    </button>
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

    <!-- Modal Ver Motivo - MEJORADO -->
    <div class="modal fade" id="verMotivoModal" tabindex="-1" aria-labelledby="verMotivoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="verMotivoModalLabel">
                        <i class="fas fa-eye me-2"></i>Motivo de la Reserva
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="reserva-info bg-light p-3 rounded mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Persona:</strong> <span id="modalPersona"></span></p>
                                <p class="mb-2"><strong>Fecha:</strong> <span id="modalFecha"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Horario:</strong> <span id="modalHorario"></span></p>
                                <p class="mb-2"><strong>Departamento:</strong> <span id="modalDepartamento"></span></p>
                            </div>
                        </div>
                    </div>
                    <div class="motivo-content">
                        <label class="form-label fw-bold">Motivo:</label>
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

    <!-- Modal Cambiar Estado -->
    <div class="modal fade" id="cambiarEstadoModal" tabindex="-1" aria-labelledby="cambiarEstadoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cambiarEstadoModalLabel">
                        <i class="fas fa-exchange-alt me-2"></i>Cambiar Estado de Reserva
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="AreaComunControlador.php" id="formCambiarEstado">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="cambiarEstadoReserva">
                        <input type="hidden" name="id_persona" id="cambiarEstadoIdPersona">
                        <input type="hidden" name="id_area" id="cambiarEstadoIdArea">
                        <input type="hidden" name="fecha_reserva" id="cambiarEstadoFecha">
                        <input type="hidden" name="hora_inicio" id="cambiarEstadoHoraInicio">
                        <input type="hidden" name="nuevo_estado" id="cambiarEstadoNuevoEstado">

                        <div class="text-center mb-3">
                            <i class="fas fa-question-circle fa-3x text-warning mb-2"></i>
                            <h6>¿Está seguro que desea cambiar el estado de esta reserva?</h6>
                        </div>

                        <div class="reserva-info bg-light p-3 rounded">
                            <p class="mb-2"><strong>Persona:</strong> <span id="cambiarEstadoPersona"></span></p>
                            <p class="mb-2"><strong>Fecha:</strong> <span id="cambiarEstadoFecha"></span></p>
                            <p class="mb-2"><strong>Horario:</strong> <span id="cambiarEstadoHorario"></span></p>
                            <p class="mb-2"><strong>Estado Actual:</strong> <span id="cambiarEstadoActual" class="badge"></span></p>
                            <p class="mb-0"><strong>Nuevo Estado:</strong> <span id="cambiarEstadoNuevo" class="badge"></span></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Confirmar Cambio
                        </button>
                    </div>
                </form>
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

    <!-- Botones de Exportación -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <!-- Script para DataTable y funcionalidades -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar DataTable
            var tabla = $('#tablaReservas').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                responsive: false,
                scrollX: false,
                autoWidth: false,
                dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>" +
                    "<'row'<'col-sm-12 col-md-6'B>>",

                buttons: [
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fas fa-file-pdf me-2"></i>PDF',
                        className: 'btn btn-danger btn-sm mb-2',
                        title: 'Reporte de Reservas - <?php echo htmlspecialchars($area["nombre"]); ?>',
                        filename: 'reservas_<?php echo htmlspecialchars($area["nombre"]); ?>_' + new Date().toISOString().slice(0, 10),
                        exportOptions: {
                            columns: [0, 1, 3, 4, 5, 6, 7], // Quitamos CI(2), Email(8) y Motivo(9)
                            stripHtml: true,
                            stripNewlines: true,
                            format: {
                                body: function (data, row, column, node) {
                                    // Limpiar HTML de todas las columnas
                                    var textoSinHTML = data.replace(/<[^>]*>/g, '').trim();

                                    // Para columna 6 (horario), limpiar espacios extras
                                    if (column === 6) {
                                        return textoSinHTML.replace(/\s+/g, ' ').trim();
                                    }

                                    return textoSinHTML;
                                }
                            }
                        },
                        customize: function (doc) {
                            // Fecha de generación
                            var fechaGeneracion = new Date().toLocaleDateString('es-ES', {
                                weekday: 'long',
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            });

                            // Encabezado profesional
                            doc.content.splice(0, 0,
                                {
                                    columns: [
                                        {
                                            text: 'SEINT',
                                            style: 'logoText',
                                            width: '20%'
                                        },
                                        {
                                            stack: [
                                                { text: 'SISTEMA DE EDIFICIO INTELIGENTE', style: 'systemName' },
                                                { text: 'Reporte de Reservas de Áreas Comunes', style: 'reportTitle' }
                                            ],
                                            width: '60%',
                                            alignment: 'center'
                                        },
                                        {
                                            text: `Generado:\n${fechaGeneracion}`,
                                            style: 'dateText',
                                            width: '20%',
                                            alignment: 'right'
                                        }
                                    ],
                                    margin: [0, 0, 0, 20]
                                }
                            );

                            // Línea separadora
                            doc.content.splice(1, 0, {
                                canvas: [{ type: 'line', x1: 0, y1: 0, x2: 515, y2: 0, lineWidth: 2, lineColor: '#2c3e50' }],
                                margin: [0, 10, 0, 20]
                            });

                            // Información del área con diseño de tarjeta
                            doc.content.splice(2, 0, {
                                layout: {
                                    fillColor: function(i, node) { return '#f8f9fa'; },
                                    hLineWidth: function(i, node) { return 0.5; },
                                    vLineWidth: function(i, node) { return 0.5; },
                                    hLineColor: function(i, node) { return '#dee2e6'; },
                                    vLineColor: function(i, node) { return '#dee2e6'; }
                                },
                                table: {
                                    widths: ['100%'],
                                    body: [
                                        [
                                            {
                                                text: 'INFORMACIÓN DEL ÁREA COMÚN',
                                                style: 'cardHeader',
                                                fillColor: '#2c3e50',
                                                color: '#ffffff'
                                            }
                                        ],
                                        [
                                            {
                                                columns: [
                                                    {
                                                        width: '50%',
                                                        stack: [
                                                            { text: `Nombre: <?php echo htmlspecialchars($area['nombre']); ?>`, style: 'fieldTitle' },
                                                            { text: `Descripción: <?php echo htmlspecialchars($area['descripcion'] ?: 'Sin descripción'); ?>`, style: 'fieldValue' },
                                                            { text: `Capacidad: <?php echo htmlspecialchars($area['capacidad']); ?> personas`, style: 'fieldValue' }
                                                        ]
                                                    },
                                                    {
                                                        width: '50%',
                                                        stack: [
                                                            {
                                                                text: `Estado: <?php echo ucfirst(htmlspecialchars($area['estado'])); ?>`,
                                                                style: 'fieldValue',
                                                                color: getEstadoColor('<?php echo $area["estado"]; ?>')
                                                            },
                                                            { text: `Precio: Bs. <?php echo number_format($area['costo_reserva'], 2); ?>`, style: 'fieldValue' },
                                                            {
                                                                text: `<?php echo ($area['fecha_inicio_mantenimiento'] && $area['fecha_fin_mantenimiento']) ? 'Mantenimiento: ' . date('d/m/Y', strtotime($area['fecha_inicio_mantenimiento'])) . ' - ' . date('d/m/Y', strtotime($area['fecha_fin_mantenimiento'])) : 'Sin mantenimiento programado'; ?>`,
                                                                style: 'fieldValue'
                                                            }
                                                        ]
                                                    }
                                                ],
                                                margin: [10, 10, 10, 10]
                                            }
                                        ]
                                    ]
                                },
                                margin: [0, 0, 0, 20]
                            });

                            // Estadísticas con diseño moderno
                            doc.content.splice(3, 0, {
                                layout: {
                                    fillColor: function(i, node) { return '#ffffff'; },
                                    hLineWidth: function(i, node) { return 0.5; },
                                    vLineWidth: function(i, node) { return 0.5; },
                                    hLineColor: function(i, node) { return '#e9ecef'; },
                                    vLineColor: function(i, node) { return '#e9ecef'; }
                                },
                                table: {
                                    widths: ['25%', '25%', '25%', '25%'],
                                    body: [
                                        [
                                            { text: 'ESTADÍSTICAS', style: 'statsHeader', colSpan: 4, alignment: 'center', fillColor: '#34495e', color: '#ffffff' },
                                            {}, {}, {}
                                        ],
                                        [
                                            {
                                                text: [
                                                    { text: 'Total\n', style: 'statsLabel' },
                                                    { text: '<?php echo count($reservas); ?>', style: 'statsNumber' }
                                                ],
                                                style: 'statBox',
                                                fillColor: '#3498db',
                                                color: '#ffffff',
                                                alignment: 'center'
                                            },
                                            {
                                                text: [
                                                    { text: 'Pendientes\n', style: 'statsLabel' },
                                                    { text: '<?php echo count(array_filter($reservas, function($r) { return $r["estado"] == "pendiente"; })); ?>', style: 'statsNumber' }
                                                ],
                                                style: 'statBox',
                                                fillColor: '#f39c12',
                                                color: '#ffffff',
                                                alignment: 'center'
                                            },
                                            {
                                                text: [
                                                    { text: 'Confirmadas\n', style: 'statsLabel' },
                                                    { text: '<?php echo count(array_filter($reservas, function($r) { return $r["estado"] == "confirmada"; })); ?>', style: 'statsNumber' }
                                                ],
                                                style: 'statBox',
                                                fillColor: '#27ae60',
                                                color: '#ffffff',
                                                alignment: 'center'
                                            },
                                            {
                                                text: [
                                                    { text: 'Canceladas\n', style: 'statsLabel' },
                                                    { text: '<?php echo count(array_filter($reservas, function($r) { return $r["estado"] == "cancelada"; })); ?>', style: 'statsNumber' }
                                                ],
                                                style: 'statBox',
                                                fillColor: '#e74c3c',
                                                color: '#ffffff',
                                                alignment: 'center'
                                            }
                                        ]
                                    ]
                                },
                                margin: [0, 0, 0, 25]
                            });

                            // Procesar la tabla de datos
                            if (doc.content[4].table) {
                                // Ajustar diseño de la tabla principal CON BORDES
                                doc.content[4].table.widths = ['5%', '15%', '15%', '15%', '10%', '15%', '25%'];

                                // AGREGAR BORDES A LA TABLA PRINCIPAL
                                doc.content[4].layout = {
                                    hLineWidth: function(i, node) {
                                        return 0.5;
                                    },
                                    vLineWidth: function(i, node) {
                                        return 0.5;
                                    },
                                    hLineColor: function(i, node) {
                                        return '#dee2e6';
                                    },
                                    vLineColor: function(i, node) {
                                        return '#dee2e6';
                                    }
                                };

                                // Encabezado de la tabla
                                doc.content[4].table.body[0].forEach(function(cell, index) {
                                    cell.fillColor = '#2c3e50';
                                    cell.color = '#ffffff';
                                    cell.bold = true;
                                    cell.alignment = 'center';
                                    cell.style = 'tableHeader';
                                    cell.border = [true, true, true, true];
                                });

                                // Procesar cada fila de datos
                                for (let i = 1; i < doc.content[4].table.body.length; i++) {
                                    let row = doc.content[4].table.body[i];

                                    // Alternar colores de fila
                                    if (i % 2 === 0) {
                                        row.forEach(function(cell) {
                                            cell.fillColor = '#f8f9fa';
                                        });
                                    }

                                    // Aplicar bordes a todas las celdas
                                    row.forEach(function(cell) {
                                        cell.border = [true, true, true, true];
                                    });

                                    // Formatear estado con colores (columna 6)
                                    if (row[6] && row[6].text) {
                                        let estadoConfig = getEstadoConfig(row[6].text);
                                        row[6] = {
                                            text: row[6].text,
                                            fillColor: estadoConfig.fillColor,
                                            color: estadoConfig.color,
                                            bold: true,
                                            alignment: 'center',
                                            style: 'estadoBadge',
                                            border: [true, true, true, true]
                                        };
                                    }

                                    // Centrar número y fecha
                                    row[0].alignment = 'center';
                                    row[4].alignment = 'center';
                                    row[5].alignment = 'center';
                                }
                            }

                            // Estilos personalizados
                            doc.styles = {
                                logoText: {
                                    fontSize: 16,
                                    bold: true,
                                    color: '#2c3e50'
                                },
                                systemName: {
                                    fontSize: 14,
                                    bold: true,
                                    color: '#2c3e50',
                                    margin: [0, 2, 0, 2]
                                },
                                reportTitle: {
                                    fontSize: 12,
                                    color: '#7f8c8d',
                                    margin: [0, 2, 0, 2]
                                },
                                dateText: {
                                    fontSize: 9,
                                    color: '#95a5a6'
                                },
                                cardHeader: {
                                    fontSize: 12,
                                    bold: true,
                                    alignment: 'center',
                                    margin: [5, 5, 5, 5]
                                },
                                fieldTitle: {
                                    fontSize: 10,
                                    bold: true,
                                    color: '#2c3e50',
                                    margin: [0, 2, 0, 2]
                                },
                                fieldValue: {
                                    fontSize: 9,
                                    color: '#34495e',
                                    margin: [0, 2, 0, 2]
                                },
                                statsHeader: {
                                    fontSize: 11,
                                    bold: true,
                                    margin: [5, 5, 5, 5]
                                },
                                statBox: {
                                    fontSize: 10,
                                    margin: [8, 8, 8, 8]
                                },
                                statsLabel: {
                                    fontSize: 9,
                                    bold: false
                                },
                                statsNumber: {
                                    fontSize: 16,
                                    bold: true
                                },
                                tableHeader: {
                                    fontSize: 8,
                                    bold: true,
                                    margin: [3, 3, 3, 3]
                                },
                                estadoBadge: {
                                    fontSize: 7,
                                    margin: [2, 2, 2, 2]
                                }
                            };

                            // Configuración general del documento
                            doc.defaultStyle = {
                                fontSize: 8,
                                color: '#2c3e50'
                            };

                            // Pie de página profesional
                            doc.footer = function(currentPage, pageCount) {
                                return {
                                    columns: [
                                        {
                                            text: 'SEINT - Sistema de Edificio Inteligente',
                                            alignment: 'left',
                                            fontSize: 7,
                                            color: '#7f8c8d',
                                            width: '50%'
                                        },
                                        {
                                            text: `Página ${currentPage} de ${pageCount}`,
                                            alignment: 'center',
                                            fontSize: 7,
                                            color: '#7f8c8d',
                                            width: '25%'
                                        },
                                        {
                                            text: 'Sistema de Gestión de Condominios',
                                            alignment: 'right',
                                            fontSize: 7,
                                            color: '#7f8c8d',
                                            width: '25%'
                                        }
                                    ],
                                    margin: [40, 10, 40, 0]
                                };
                            };

                            // Márgenes del documento
                            doc.pageMargins = [40, 80, 40, 60];

                            // Funciones auxiliares
                            function getEstadoColor(estado) {
                                switch(estado.toLowerCase()) {
                                    case 'disponible': return '#27ae60';
                                    case 'mantenimiento': return '#f39c12';
                                    case 'no disponible': return '#e74c3c';
                                    default: return '#7f8c8d';
                                }
                            }

                            function getEstadoConfig(estado) {
                                switch(estado.toLowerCase()) {
                                    case 'confirmada':
                                        return { fillColor: '#27ae60', color: '#ffffff' };
                                    case 'pendiente':
                                        return { fillColor: '#f39c12', color: '#ffffff' };
                                    case 'cancelada':
                                        return { fillColor: '#e74c3c', color: '#ffffff' };
                                    default:
                                        return { fillColor: '#95a5a6', color: '#ffffff' };
                                }
                            }
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel me-2"></i>Excel',
                        className: 'btn btn-success btn-sm mb-2',
                        title: 'Reservas_<?php echo htmlspecialchars($area["nombre"]); ?>',
                        filename: 'reservas_<?php echo htmlspecialchars($area["nombre"]); ?>_' + new Date().toISOString().slice(0, 10),
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8], // Incluir todas las columnas en Excel
                            format: {
                                body: function (data, row, column, node) {
                                    // Limpiar HTML de todas las columnas
                                    var textoSinHTML = data.replace(/<[^>]*>/g, '').trim();

                                    // Columna 8 es la columna de motivo
                                    if (column === 8) {
                                        var botonMotivo = $(node).find('button[data-motivo]');
                                        if (botonMotivo.length > 0) {
                                            var motivoCompleto = botonMotivo.data('motivo');
                                            return motivoCompleto || 'Sin motivo especificado';
                                        }
                                        return textoSinHTML || 'Sin motivo especificado';
                                    }

                                    // Para columna 6 (horario), limpiar espacios extras
                                    if (column === 6) {
                                        return textoSinHTML.replace(/\s+/g, ' ').trim();
                                    }

                                    return textoSinHTML;
                                }
                            }
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print me-2"></i>Imprimir',
                        className: 'btn btn-info btn-sm mb-2',
                        title: 'SEINT - Reporte de Reservas - <?php echo htmlspecialchars($area["nombre"]); ?>',
                        exportOptions: {
                            columns: [0, 1, 3, 4, 5, 6, 7], // Quitamos CI(2), Email(8) y Motivo(9)
                            stripHtml: false,
                            format: {
                                body: function (data, row, column, node) {
                                    // Limpiar HTML de todas las columnas
                                    var textoSinHTML = data.replace(/<[^>]*>/g, '').trim();

                                    // Para columna 6 (horario), limpiar espacios extras
                                    if (column === 6) {
                                        return textoSinHTML.replace(/\s+/g, ' ').trim();
                                    }

                                    return textoSinHTML;
                                }
                            }
                        },
                        customize: function (win) {
                            $(win.document.body).find('h1').css({
                                'text-align': 'center',
                                'color': '#2c3e50',
                                'border-bottom': '2px solid #3498db',
                                'padding-bottom': '10px'
                            });

                            $(win.document.body).find('table').addClass('table table-striped table-bordered');

                            // Agregar información del área con estilo
                            var areaInfo = `
                            <div style="margin-bottom: 25px; padding: 20px; border: 2px solid #3498db; border-radius: 10px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                                <h4 style="color: #2c3e50; text-align: center; margin-bottom: 15px;">
                                    <i class="fas fa-building"></i> Información del Área Común
                                </h4>
                                <div style="display: flex; justify-content: space-between;">
                                    <div style="width: 48%;">
                                        <p><strong>Nombre:</strong> <?php echo htmlspecialchars($area["nombre"]); ?></p>
                                        <p><strong>Descripción:</strong> <?php echo htmlspecialchars($area["descripcion"] ?: "Sin descripción"); ?></p>
                                        <p><strong>Capacidad:</strong> <?php echo htmlspecialchars($area["capacidad"]); ?> personas</p>
                                    </div>
                                    <div style="width: 48%;">
                                        <p><strong>Estado:</strong> <span style="color: ${getEstadoColorPrint('<?php echo $area["estado"]; ?>')}"><?php echo ucfirst(htmlspecialchars($area["estado"])); ?></span></p>
                                        <p><strong>Precio:</strong> Bs. <?php echo number_format($area["costo_reserva"], 2); ?></p>
                                        <?php if ($area['fecha_inicio_mantenimiento'] && $area['fecha_fin_mantenimiento']): ?>
                                            <p><strong>Mantenimiento:</strong> <?php echo date('d/m/Y', strtotime($area['fecha_inicio_mantenimiento'])); ?> - <?php echo date('d/m/Y', strtotime($area['fecha_fin_mantenimiento'])); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div style="margin-bottom: 25px; padding: 15px; border: 1px solid #bdc3c7; border-radius: 8px; background: white;">
                                <h5 style="color: #2c3e50; text-align: center; margin-bottom: 15px;">
                                    <i class="fas fa-chart-bar"></i> Estadísticas de Reservas
                                </h5>
                                <div style="display: flex; justify-content: space-around; text-align: center;">
                                    <div style="padding: 10px;">
                                        <h6 style="color: #3498db; margin: 0;">Total</h6>
                                        <h4 style="color: #2c3e50; margin: 5px 0;"><?php echo count($reservas); ?></h4>
                                    </div>
                                    <div style="padding: 10px;">
                                        <h6 style="color: #f39c12; margin: 0;">Pendientes</h6>
                                        <h4 style="color: #2c3e50; margin: 5px 0;"><?php echo count(array_filter($reservas, function($r) { return $r["estado"] == "pendiente"; })); ?></h4>
                                    </div>
                                    <div style="padding: 10px;">
                                        <h6 style="color: #27ae60; margin: 0;">Confirmadas</h6>
                                        <h4 style="color: #2c3e50; margin: 5px 0;"><?php echo count(array_filter($reservas, function($r) { return $r["estado"] == "confirmada"; })); ?></h4>
                                    </div>
                                    <div style="padding: 10px;">
                                        <h6 style="color: #e74c3c; margin: 0;">Canceladas</h6>
                                        <h4 style="color: #2c3e50; margin: 5px 0;"><?php echo count(array_filter($reservas, function($r) { return $r["estado"] == "cancelada"; })); ?></h4>
                                    </div>
                                </div>
                            </div>
                        `;

                            $(win.document.body).prepend(areaInfo);

                            // Agregar header
                            var header = `
                            <div style="text-align: center; margin-bottom: 20px; padding: 15px; border-bottom: 3px solid #3498db;">
                                <h1 style="color: #2c3e50; margin: 0;">SEINT</h1>
                                <h3 style="color: #7f8c8d; margin: 5px 0;">Sistema de Edificio Inteligente</h3>
                                <p style="color: #95a5a6; margin: 0;">Reporte de Reservas de Áreas Comunes</p>
                            </div>
                        `;
                            $(win.document.body).prepend(header);

                            function getEstadoColorPrint(estado) {
                                switch(estado.toLowerCase()) {
                                    case 'disponible': return '#27ae60';
                                    case 'mantenimiento': return '#f39c12';
                                    case 'no disponible': return '#e74c3c';
                                    default: return '#7f8c8d';
                                }
                            }
                        }
                    }
                ],
                pageLength: 10,
                lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
                order: [[5, 'desc']],
                columnDefs: [
                    {
                        orderable: false,
                        targets: [9]
                    },
                    {
                        searchable: false,
                        targets: [9]
                    },
                    {
                        width: 'auto',
                        targets: '_all'
                    }
                ],
                initComplete: function() {
                    $('.dataTables_filter input').addClass('form-control form-control-sm');
                    $('.dataTables_filter input').attr('placeholder', 'Buscar...');
                    $('.dataTables_length select').addClass('form-select form-select-sm');
                    this.api().buttons().container().appendTo('#tablaReservas_wrapper .col-md-6:eq(1)');
                }
            });

            // Modal Ver Motivo - MEJORADO
            const verMotivoModal = document.getElementById('verMotivoModal');
            verMotivoModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const motivo = button.getAttribute('data-motivo');
                const persona = button.getAttribute('data-persona');
                const fecha = button.getAttribute('data-fecha');
                const horario = button.getAttribute('data-horario');
                const departamento = button.getAttribute('data-departamento');

                document.getElementById('modalMotivo').textContent = motivo;
                document.getElementById('modalPersona').textContent = persona;
                document.getElementById('modalFecha').textContent = fecha;
                document.getElementById('modalHorario').textContent = horario;
                document.getElementById('modalDepartamento').textContent = departamento;
            });

            // Modal Cambiar Estado
            const cambiarEstadoModal = document.getElementById('cambiarEstadoModal');
            cambiarEstadoModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const idPersona = button.getAttribute('data-id-persona');
                const idArea = button.getAttribute('data-id-area');
                const fechaReserva = button.getAttribute('data-fecha-reserva');
                const horaInicio = button.getAttribute('data-hora-inicio');
                const persona = button.getAttribute('data-persona');
                const estadoActual = button.getAttribute('data-estado-actual');
                const nuevoEstado = button.getAttribute('data-nuevo-estado');

                // Llenar campos hidden
                document.getElementById('cambiarEstadoIdPersona').value = idPersona;
                document.getElementById('cambiarEstadoIdArea').value = idArea;
                document.getElementById('cambiarEstadoFecha').value = fechaReserva;
                document.getElementById('cambiarEstadoHoraInicio').value = horaInicio;
                document.getElementById('cambiarEstadoNuevoEstado').value = nuevoEstado;

                // Llenar información visible
                document.getElementById('cambiarEstadoPersona').textContent = persona;
                document.getElementById('cambiarEstadoFecha').textContent = new Date(fechaReserva).toLocaleDateString('es-ES');
                document.getElementById('cambiarEstadoHorario').textContent = horaInicio;

                // Configurar badges de estado
                const estadoActualBadge = document.getElementById('cambiarEstadoActual');
                const estadoNuevoBadge = document.getElementById('cambiarEstadoNuevo');

                // Estado actual
                let badgeClassActual = '';
                switch(estadoActual) {
                    case 'confirmada': badgeClassActual = 'bg-success'; break;
                    case 'pendiente': badgeClassActual = 'bg-warning'; break;
                    case 'cancelada': badgeClassActual = 'bg-danger'; break;
                    default: badgeClassActual = 'bg-secondary';
                }
                estadoActualBadge.className = `badge ${badgeClassActual}`;
                estadoActualBadge.textContent = estadoActual.charAt(0).toUpperCase() + estadoActual.slice(1);

                // Nuevo estado
                let badgeClassNuevo = '';
                switch(nuevoEstado) {
                    case 'confirmada': badgeClassNuevo = 'bg-success'; break;
                    case 'pendiente': badgeClassNuevo = 'bg-warning'; break;
                    case 'cancelada': badgeClassNuevo = 'bg-danger'; break;
                    default: badgeClassNuevo = 'bg-secondary';
                }
                estadoNuevoBadge.className = `badge ${badgeClassNuevo}`;
                estadoNuevoBadge.textContent = nuevoEstado.charAt(0).toUpperCase() + nuevoEstado.slice(1);

                // Configurar título del modal según el nuevo estado
                let titulo = '';
                switch(nuevoEstado) {
                    case 'confirmada': titulo = 'Confirmar Reserva'; break;
                    case 'pendiente': titulo = 'Marcar como Pendiente'; break;
                    case 'cancelada': titulo = 'Cancelar Reserva'; break;
                    default: titulo = 'Cambiar Estado';
                }
                document.getElementById('cambiarEstadoModalLabel').innerHTML = `<i class="fas fa-exchange-alt me-2"></i>${titulo}`;
            });

            // Configurar envío de formularios para cerrar modales automáticamente
            const forms = document.querySelectorAll('#formCambiarEstado');
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

        #tablaReservas {
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
            border-left: 4px solid var(--azul-oscuro);
        }

        /* Estilos para botones de exportación */
        .dt-buttons {
            margin-bottom: 10px;
        }

        .dt-buttons .btn {
            margin-right: 5px;
        }

        /* Estilos mejorados para el modal de motivo */
        .motivo-text-container {
            max-height: 300px;
            overflow-y: auto;
            word-wrap: break-word;
            white-space: pre-wrap;
        }

        @media (max-width: 768px) {
            .dt-buttons {
                text-align: center;
            }

            .dt-buttons .btn {
                margin-bottom: 5px;
                width: 100%;
            }

            .modal-lg {
                max-width: 95%;
            }
        }
    </style>

<?php include("../../includes/footer.php"); ?>
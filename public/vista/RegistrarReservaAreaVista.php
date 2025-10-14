<?php
include("../../includes/header.php");

// Configurar zona horaria
date_default_timezone_set('America/La_Paz');

// ID de persona por defecto (en un sistema real esto vendría de la sesión del usuario)
$id_persona = 1;
$rol = 'Administrador'; // En un sistema real esto vendría de la sesión

// Fecha actual o fecha seleccionada
$fecha_actual = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');
$fecha_obj = new DateTime($fecha_actual);
$inicio_semana = clone $fecha_obj;
$inicio_semana->modify('monday this week');
$fin_semana = clone $inicio_semana;
$fin_semana->modify('sunday this week');

// Obtener lista de áreas disponibles
require_once '../../config/database.php';
require_once '../modelo/AreaComunModelo.php';

$database = new Database();
$db = $database->getConnection();
$areaModelo = new AreaComunModelo($db);
$areas = $areaModelo->listarAreas();

// Reservas del área seleccionada
$reservas_area = [];
$area_seleccionada = isset($_GET['id_area']) ? intval($_GET['id_area']) : 0;

if ($area_seleccionada > 0) {
    $reservas_area = $areaModelo->obtenerReservasPorArea($area_seleccionada);
}

// Crear array de reservas por fecha y hora para fácil acceso
$reservas_por_fecha = [];
foreach ($reservas_area as $reserva) {
    // No mostrar reservas canceladas
    if ($reserva['estado'] === 'cancelada') {
        continue;
    }

    $fecha = $reserva['fecha_reserva'];
    $hora_inicio = $reserva['hora_inicio'];
    $hora_fin = $reserva['hora_fin'];

    if (!isset($reservas_por_fecha[$fecha])) {
        $reservas_por_fecha[$fecha] = [];
    }

    // Marcar todas las horas entre inicio y fin como ocupadas
    $hora_actual = intval(substr($hora_inicio, 0, 2));
    $hora_final = intval(substr($hora_fin, 0, 2));

    for ($h = $hora_actual; $h < $hora_final; $h++) {
        $hora_key = str_pad($h, 2, '0', STR_PAD_LEFT) . ':00';
        $reservas_por_fecha[$fecha][$hora_key] = $reserva;
    }
}
?>

    <!-- Page Header -->
    <div class="page-header fade-in">
        <div class="page-title">
            <h1>Registrar Nueva Reserva</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item"><a href="../controlador/AreaComunControlador.php?action=listarAreas">Áreas Comunes</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Registrar Reserva</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Agenda Semanal -->
    <div class="row fade-in mb-4">
        <div class="col-12">
            <div class="content-box">
                <div class="content-box-header d-flex justify-content-between align-items-center">
                    <h5>Agenda Semanal - Disponibilidad del Área</h5>
                    <div class="d-flex gap-2">
                        <a href="AreaComunControlador.php?action=formularioReservaArea&fecha=<?php echo date('Y-m-d', strtotime($fecha_actual . ' -1 week')); ?><?php echo $area_seleccionada ? '&id_area=' . $area_seleccionada : ''; ?>"
                           class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-chevron-left"></i> Semana Anterior
                        </a>
                        <span class="btn btn-sm btn-light">
                            <?php echo $inicio_semana->format('d/m/Y') . ' - ' . $fin_semana->format('d/m/Y'); ?>
                        </span>
                        <a href="AreaComunControlador.php?action=formularioReservaArea&fecha=<?php echo date('Y-m-d', strtotime($fecha_actual . ' +1 week')); ?><?php echo $area_seleccionada ? '&id_area=' . $area_seleccionada : ''; ?>"
                           class="btn btn-sm btn-outline-secondary">
                            Semana Siguiente <i class="fas fa-chevron-right"></i>
                        </a>
                        <a href="AreaComunControlador.php?action=formularioReservaArea&fecha=<?php echo date('Y-m-d'); ?><?php echo $area_seleccionada ? '&id_area=' . $area_seleccionada : ''; ?>"
                           class="btn btn-sm btn-primary">
                            Hoy
                        </a>
                    </div>
                </div>
                <div class="content-box-body">
                    <!-- Selector de Área para la Agenda -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="selector_area" class="form-label">Seleccionar Área para Ver Agenda:</label>
                            <select class="form-control" id="selector_area" name="selector_area">
                                <option value="">Seleccione un área para ver disponibilidad</option>
                                <?php
                                if ($areas) {
                                    foreach ($areas as $area) {
                                        if ($area['estado'] == 'disponible') {
                                            $selected = ($area_seleccionada == $area['id_area']) ? 'selected' : '';
                                            echo "<option value='{$area['id_area']}' $selected>{$area['nombre']}</option>";
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <?php if ($area_seleccionada > 0): ?>
                        <!-- Tabla de Agenda Compacta -->
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-bordered table-agenda table-sm mb-1">
                                <thead class="table-light sticky-top">
                                <tr>
                                    <th width="70" class="sticky-column">Hora</th>
                                    <?php
                                    $dias_semana = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
                                    $fecha_dia = clone $inicio_semana;
                                    for ($i = 0; $i < 7; $i++) {
                                        $clase_hoy = ($fecha_dia->format('Y-m-d') == date('Y-m-d')) ? 'bg-warning' : '';
                                        echo "<th class='text-center $clase_hoy' style='min-width: 80px; padding: 4px 2px;'>";
                                        echo "<div class='fw-bold' style='font-size: 0.8rem;'>{$dias_semana[$i]}</div>";
                                        echo "<div class='small' style='font-size: 0.75rem;'>{$fecha_dia->format('d/m')}</div>";
                                        echo "</th>";
                                        $fecha_dia->modify('+1 day');
                                    }
                                    ?>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                // Horarios de 8:00 AM a 9:00 PM (21:00)
                                for ($hora = 8; $hora <= 21; $hora++) {
                                    echo "<tr>";
                                    $hora_formateada = str_pad($hora, 2, '0', STR_PAD_LEFT) . ':00';
                                    echo "<td class='text-center fw-bold bg-light sticky-column' style='padding: 2px 4px; font-size: 0.8rem; background: #f8f9fa !important;'>{$hora_formateada}</td>";

                                    $fecha_dia = clone $inicio_semana;
                                    for ($dia = 0; $dia < 7; $dia++) {
                                        $fecha_actual_str = $fecha_dia->format('Y-m-d');
                                        $celda_id = "celda_{$fecha_actual_str}_{$hora_formateada}";

                                        // Verificar si hay reserva en esta fecha y hora
                                        $reservada = false;
                                        $reserva_info = null;
                                        $estado_reserva = '';

                                        if (isset($reservas_por_fecha[$fecha_actual_str][$hora_formateada])) {
                                            $reservada = true;
                                            $reserva_info = $reservas_por_fecha[$fecha_actual_str][$hora_formateada];
                                            $estado_reserva = $reserva_info['estado'];
                                        }

                                        $clase_celda = '';
                                        $texto_celda = '✓';
                                        $title_text = 'Disponible';

                                        if ($reservada && $reserva_info) {
                                            if ($estado_reserva === 'confirmada') {
                                                $clase_celda = 'bg-success text-white';
                                                $texto_celda = '✓';
                                                $title_text = "Confirmada: {$reserva_info['hora_inicio']} - {$reserva_info['hora_fin']}";
                                            } elseif ($estado_reserva === 'pendiente') {
                                                $clase_celda = 'bg-warning';
                                                $texto_celda = '!';
                                                $title_text = "Pendiente: {$reserva_info['hora_inicio']} - {$reserva_info['hora_fin']}";
                                            }
                                        }

                                        echo "<td id='{$celda_id}' class='text-center celda-hora {$clase_celda}' 
                                              style='cursor: pointer; height: 30px; vertical-align: middle; font-size: 0.8rem; padding: 2px;'
                                              title='{$title_text}'
                                              data-fecha='{$fecha_actual_str}'
                                              data-hora='{$hora_formateada}'
                                              data-reservada='".($reservada ? '1' : '0')."'";

                                        if ($reservada && $reserva_info) {
                                            echo " data-estado='{$reserva_info['estado']}'";
                                            echo " data-motivo='".htmlspecialchars($reserva_info['motivo'])."'";
                                            echo " data-hora-inicio='{$reserva_info['hora_inicio']}'";
                                            echo " data-hora-fin='{$reserva_info['hora_fin']}'";
                                        } else {
                                            echo " data-estado='' data-motivo='' data-hora-inicio='' data-hora-fin=''";
                                        }

                                        echo ">";
                                        echo "<span style='font-size: 0.75rem;'>{$texto_celda}</span>";
                                        echo "</td>";

                                        $fecha_dia->modify('+1 day');
                                    }
                                    echo "</tr>";
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Leyenda Compacta -->
                        <div class="row mt-2">
                            <div class="col-12">
                                <div class="d-flex flex-wrap gap-3 justify-content-center" style="font-size: 0.8rem;">
                                    <div class="d-flex align-items-center">
                                        <div class="color-box bg-success me-1" style="width: 12px; height: 12px;"></div>
                                        <small class="text-muted">Confirmada (✓)</small>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="color-box bg-warning me-1" style="width: 12px; height: 12px;"></div>
                                        <small class="text-muted">Pendiente (!)</small>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="color-box bg-light me-1 border" style="width: 12px; height: 12px;"></div>
                                        <small class="text-muted">Disponible (✓)</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info text-center py-2">
                            <i class="fas fa-info-circle me-2"></i>
                            Seleccione un área para ver su agenda de disponibilidad semanal
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario de Registro -->
    <div class="row fade-in">
        <div class="col-lg-8">
            <div class="content-box">
                <div class="content-box-header">
                    <h5>Información de la Reserva</h5>
                </div>
                <div class="content-box-body">
                    <form id="formRegistrarReserva" action="../controlador/AreaComunControlador.php" method="POST">
                        <input type="hidden" name="action" value="registrarReserva">
                        <input type="hidden" name="id_persona" value="<?php echo $id_persona; ?>">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="id_area" class="form-label">
                                        <i class="fas fa-map-marker-alt text-verde me-2"></i>Área Común *
                                    </label>
                                    <select class="form-control" id="id_area" name="id_area" required>
                                        <option value="">Seleccione un área</option>
                                        <?php
                                        if ($areas) {
                                            foreach ($areas as $area) {
                                                if ($area['estado'] == 'disponible') {
                                                    $selected = ($area_seleccionada == $area['id_area']) ? 'selected' : '';
                                                    echo "<option value='{$area['id_area']}' $selected>{$area['nombre']} (Capacidad: {$area['capacidad']})</option>";
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                    <div class="form-text">Seleccione el área común a reservar</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="fecha_reserva" class="form-label">
                                        <i class="fas fa-calendar-alt text-azul me-2"></i>Fecha de Reserva *
                                    </label>
                                    <input type="date"
                                           class="form-control"
                                           id="fecha_reserva"
                                           name="fecha_reserva"
                                           required
                                           min="<?php echo date('Y-m-d'); ?>"
                                           value="<?php echo $fecha_actual; ?>">
                                    <div class="form-text">Seleccione la fecha para la reserva</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="hora_inicio" class="form-label">
                                        <i class="fas fa-clock text-verde me-2"></i>Hora de Inicio *
                                    </label>
                                    <select class="form-control" id="hora_inicio" name="hora_inicio" required>
                                        <option value="">Seleccione hora de inicio</option>
                                        <?php
                                        // Generar opciones de hora de 8:00 a 21:00 (solo horas enteras)
                                        for ($hora = 8; $hora <= 21; $hora++) {
                                            $hora_formateada = str_pad($hora, 2, '0', STR_PAD_LEFT) . ':00';
                                            echo "<option value='{$hora_formateada}'>{$hora_formateada}</option>";
                                        }
                                        ?>
                                    </select>
                                    <div class="form-text">Hora exacta en que inicia la reserva (8:00 - 21:00)</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="hora_fin" class="form-label">
                                        <i class="fas fa-clock text-azul me-2"></i>Hora de Fin *
                                    </label>
                                    <select class="form-control" id="hora_fin" name="hora_fin" required>
                                        <option value="">Seleccione hora de fin</option>
                                        <?php
                                        // Generar opciones de hora de 9:00 a 22:00 (solo horas enteras)
                                        for ($hora = 9; $hora <= 22; $hora++) {
                                            $hora_formateada = str_pad($hora, 2, '0', STR_PAD_LEFT) . ':00';
                                            if ($hora == 22) {
                                                $hora_formateada = '21:59';
                                            }
                                            echo "<option value='{$hora_formateada}'>{$hora_formateada}</option>";
                                        }
                                        ?>
                                    </select>
                                    <div class="form-text">Hora exacta en que finaliza la reserva</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="motivo" class="form-label">
                                        <i class="fas fa-sticky-note text-verde me-2"></i>Motivo de la Reserva *
                                    </label>
                                    <textarea class="form-control"
                                              id="motivo"
                                              name="motivo"
                                              rows="3"
                                              required
                                              maxlength="500"
                                              placeholder="Describa el motivo de la reserva"></textarea>
                                    <div class="form-text">Descripción del propósito de la reserva</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <?php if ($rol === 'Administrador'): ?>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="estado" class="form-label">
                                            <i class="fas fa-tag text-azul me-2"></i>Estado de la Reserva
                                        </label>
                                        <select class="form-control" id="estado" name="estado">
                                            <option value="pendiente">Pendiente</option>
                                            <option value="confirmada">Confirmada</option>
                                            <option value="cancelada">Cancelada</option>
                                        </select>
                                        <div class="form-text">Estado inicial de la reserva</div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <input type="hidden" name="estado" value="pendiente">
                            <?php endif; ?>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="AreaComunControlador.php?action=listarAreas" class="btn btn-secondary me-md-2">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary" style="background: var(--verde); border: none;">
                                <i class="fas fa-save me-2"></i>Registrar Reserva
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Información Adicional -->
        <div class="col-lg-4">
            <div class="content-box position-sticky" style="top: 100px;">
                <div class="content-box-header">
                    <h5>Información Importante</h5>
                </div>
                <div class="content-box-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Instrucciones:</h6>
                        <ul class="mb-0 mt-2" style="font-size: 0.9rem;">
                            <li>Seleccione un área en la agenda para ver disponibilidad</li>
                            <li><span class="text-success">● Verde:</span> Reserva confirmada</li>
                            <li><span class="text-warning">● Amarillo:</span> Reserva pendiente</li>
                            <li><span class="text-muted">● Blanco:</span> Disponible</li>
                            <li>Pase el mouse sobre las celdas para ver detalles</li>
                            <li>Haga clic en una celda para ver información completa</li>
                            <?php if ($rol === 'Administrador'): ?>
                                <li><strong>ROL ADMINISTRADOR:</strong> Puede cambiar el estado de la reserva</li>
                            <?php else: ?>
                                <li><strong>ROL USUARIO:</strong> Las reservas se crean como "Pendiente"</li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <div id="info-reserva" class="alert alert-light d-none">
                        <h6><i class="fas fa-calendar me-2"></i>Información de Reserva:</h6>
                        <div id="detalle-reserva" style="font-size: 0.9rem;"></div>
                    </div>

                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Notas:</h6>
                        <ul class="mb-0 mt-2" style="font-size: 0.9rem;">
                            <li>Las reservas están sujetas a disponibilidad</li>
                            <li>Las reservas pendientes requieren confirmación</li>
                            <li>Verifique la capacidad máxima del área seleccionada</li>
                            <li>Las reservas canceladas no pueden ser recuperadas</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script para funcionalidades de la agenda -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const formRegistrar = document.getElementById('formRegistrarReserva');
            const btnRegistrar = formRegistrar.querySelector('button[type="submit"]');
            const fechaReserva = document.getElementById('fecha_reserva');
            const horaInicio = document.getElementById('hora_inicio');
            const horaFin = document.getElementById('hora_fin');
            const idArea = document.getElementById('id_area');
            const selectorArea = document.getElementById('selector_area');
            const infoReserva = document.getElementById('info-reserva');
            const detalleReserva = document.getElementById('detalle-reserva');

            // Cambiar área en la agenda
            selectorArea.addEventListener('change', function() {
                if (this.value) {
                    window.location.href = `AreaComunControlador.php?action=formularioReservaArea&id_area=${this.value}&fecha=<?php echo $fecha_actual; ?>`;
                }
            });

            // Cambiar área en el formulario también cambia la agenda
            idArea.addEventListener('change', function() {
                if (this.value) {
                    window.location.href = `AreaComunControlador.php?action=formularioReservaArea&id_area=${this.value}&fecha=<?php echo $fecha_actual; ?>`;
                }
            });

            // Cambiar fecha en el formulario actualiza la agenda
            fechaReserva.addEventListener('change', function() {
                const areaId = idArea.value || selectorArea.value;
                if (areaId) {
                    window.location.href = `AreaComunControlador.php?action=formularioReservaArea&id_area=${areaId}&fecha=${this.value}`;
                }
            });

            // Click en celdas de la agenda
            document.querySelectorAll('.celda-hora').forEach(celda => {
                celda.addEventListener('click', function() {
                    const fecha = this.getAttribute('data-fecha');
                    const hora = this.getAttribute('data-hora');
                    const reservada = this.getAttribute('data-reservada');
                    const estado = this.getAttribute('data-estado');
                    const motivo = this.getAttribute('data-motivo');
                    const horaInicioReserva = this.getAttribute('data-hora-inicio');
                    const horaFinReserva = this.getAttribute('data-hora-fin');

                    // Actualizar formulario si está disponible
                    if (reservada === '0') {
                        fechaReserva.value = fecha;
                        horaInicio.value = hora;

                        // Calcular hora fin (1 hora después)
                        const horaNum = parseInt(hora.split(':')[0]);
                        const horaFinNum = horaNum + 1;
                        const horaFinValor = horaFinNum.toString().padStart(2, '0') + ':00';
                        if (horaFinValor <= '22:00') {
                            horaFin.value = horaFinValor;
                        }
                    }

                    // Mostrar información de la reserva
                    if (reservada === '1') {
                        detalleReserva.innerHTML = `
                            <div class="mb-2"><strong>Fecha:</strong> ${fecha}</div>
                            <div class="mb-2"><strong>Horario:</strong> ${horaInicioReserva} - ${horaFinReserva}</div>
                            <div class="mb-2"><strong>Estado:</strong> <span class="text-capitalize badge ${estado === 'confirmada' ? 'bg-success' : 'bg-warning'}">${estado}</span></div>
                            <div class="mb-2"><strong>Motivo:</strong> ${motivo || 'No especificado'}</div>
                            <div class="alert alert-warning mt-2 p-2">
                                <small><i class="fas fa-info-circle me-1"></i>Este horario ya está reservado</small>
                            </div>
                        `;
                    } else {
                        detalleReserva.innerHTML = `
                            <div class="mb-2"><strong>Fecha:</strong> ${fecha}</div>
                            <div class="mb-2"><strong>Hora:</strong> ${hora}</div>
                            <div class="mb-2 text-success"><strong>Disponible</strong></div>
                            <div class="mb-2">Puede reservar este horario</div>
                        `;
                    }

                    infoReserva.classList.remove('d-none');
                });
            });

            // Validación del formulario
            formRegistrar.addEventListener('submit', function(e) {
                const motivo = document.getElementById('motivo').value.trim();

                if (!idArea.value) {
                    e.preventDefault();
                    alert('Por favor, seleccione un área común');
                    idArea.focus();
                    return;
                }

                if (!fechaReserva.value) {
                    e.preventDefault();
                    alert('Por favor, seleccione la fecha de reserva');
                    fechaReserva.focus();
                    return;
                }

                if (!horaInicio.value) {
                    e.preventDefault();
                    alert('Por favor, seleccione la hora de inicio');
                    horaInicio.focus();
                    return;
                }

                if (!horaFin.value) {
                    e.preventDefault();
                    alert('Por favor, seleccione la hora de fin');
                    horaFin.focus();
                    return;
                }

                // Convertir horas a números para comparación
                const horaInicioNum = parseInt(horaInicio.value.split(':')[0]);
                const horaFinNum = parseInt(horaFin.value.split(':')[0]);

                if (horaFinNum <= horaInicioNum) {
                    e.preventDefault();
                    alert('La hora de fin debe ser posterior a la hora de inicio');
                    horaFin.focus();
                    return;
                }

                if (!motivo) {
                    e.preventDefault();
                    alert('Por favor, ingrese el motivo de la reserva');
                    document.getElementById('motivo').focus();
                    return;
                }

                if (motivo.length < 10) {
                    e.preventDefault();
                    alert('El motivo debe tener al menos 10 caracteres');
                    document.getElementById('motivo').focus();
                    return;
                }

                // Mostrar loading en el botón
                btnRegistrar.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Registrando...';
                btnRegistrar.disabled = true;
            });

            // Validación en tiempo real
            document.getElementById('motivo').addEventListener('input', function() {
                if (this.value.length < 10) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });

            horaInicio.addEventListener('change', function() {
                if (horaFin.value) {
                    const horaInicioNum = parseInt(this.value.split(':')[0]);
                    const horaFinNum = parseInt(horaFin.value.split(':')[0]);

                    if (horaFinNum <= horaInicioNum) {
                        this.classList.add('is-invalid');
                        horaFin.classList.add('is-invalid');
                    } else {
                        this.classList.remove('is-invalid');
                        horaFin.classList.remove('is-invalid');
                    }
                }
            });

            horaFin.addEventListener('change', function() {
                if (horaInicio.value) {
                    const horaInicioNum = parseInt(horaInicio.value.split(':')[0]);
                    const horaFinNum = parseInt(this.value.split(':')[0]);

                    if (horaFinNum <= horaInicioNum) {
                        this.classList.add('is-invalid');
                        horaInicio.classList.add('is-invalid');
                    } else {
                        this.classList.remove('is-invalid');
                        horaInicio.classList.remove('is-invalid');
                    }
                }
            });
        });
    </script>

    <style>
        .is-invalid {
            border-color: #dc3545 !important;
        }

        .form-text {
            font-size: 0.875rem;
            color: #6c757d;
        }

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

        .text-verde { color: var(--verde); }
        .text-azul { color: var(--azul); }

        .table-agenda th, .table-agenda td {
            border: 1px solid #dee2e6;
            font-size: 0.8rem;
        }

        .table-agenda {
            font-size: 0.8rem;
        }

        .celda-hora:hover {
            opacity: 0.8;
            transform: scale(1.02);
            transition: all 0.2s ease;
        }

        .color-box {
            border-radius: 2px;
        }

        .bg-success {
            background-color: #28a745 !important;
        }

        .bg-warning {
            background-color: #ffc107 !important;
        }

        .sticky-column {
            position: sticky;
            left: 0;
            background: #f8f9fa;
            z-index: 1;
        }

        .sticky-top {
            position: sticky;
            top: 0;
            z-index: 2;
        }

        .table-sm td {
            padding: 2px 4px;
        }

        .table-sm th {
            padding: 4px 2px;
        }
    </style>

<?php include("../../includes/footer.php");?>
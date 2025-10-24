<?php
include("../../includes/header.php"); ?>

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

    <!-- Alertas de éxito y error -->
<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-notificacion alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <?php echo htmlspecialchars($_GET['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-notificacion alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <?php echo htmlspecialchars($_GET['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

    <!-- Cronograma Semanal de Todas las Áreas -->
    <div class="row fade-in mb-4">
        <div class="col-12">
            <div class="content-box">
                <div class="content-box-header d-flex justify-content-between align-items-center">
                    <h5><i class="fas fa-calendar-alt me-2"></i>Cronograma Semanal - Todas las Áreas</h5>
                    <div class="d-flex gap-2">
                        <a href="AreaComunControlador.php?action=formularioReservaArea&fecha=<?php echo date('Y-m-d', strtotime($fecha_actual . ' -1 week')); ?>"
                           class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-chevron-left"></i> Semana Anterior
                        </a>
                        <span class="btn btn-sm btn-light">
                            <?php echo $inicio_semana->format('d/m/Y') . ' - ' . $fin_semana->format('d/m/Y'); ?>
                        </span>
                        <a href="AreaComunControlador.php?action=formularioReservaArea&fecha=<?php echo date('Y-m-d', strtotime($fecha_actual . ' +1 week')); ?>"
                           class="btn btn-sm btn-outline-secondary">
                            Semana Siguiente <i class="fas fa-chevron-right"></i>
                        </a>
                        <a href="AreaComunControlador.php?action=formularioReservaArea&fecha=<?php echo date('Y-m-d'); ?>"
                           class="btn btn-sm btn-primary">
                            Hoy
                        </a>
                    </div>
                </div>
                <div class="content-box-body">
                    <!-- Tabla de Cronograma -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm table-agenda">
                            <thead class="table-light sticky-top">
                            <tr>
                                <th width="70" class="sticky-column">Hora</th>
                                <?php
                                $dias_semana = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
                                $fecha_dia = clone $inicio_semana;
                                for ($i = 0; $i < 7; $i++) {
                                    $clase_hoy = ($fecha_dia->format('Y-m-d') == date('Y-m-d')) ? 'bg-warning' : '';
                                    echo "<th class='text-center $clase_hoy' style='min-width: 100px;'>";
                                    echo "<div class='fw-bold small'>{$dias_semana[$i]}</div>";
                                    echo "<div class='small'>{$fecha_dia->format('d/m')}</div>";
                                    echo "</th>";
                                    $fecha_dia->modify('+1 day');
                                }
                                ?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php for ($hora = 8; $hora <= 21; $hora++): ?>
                                <tr>
                                    <?php
                                    $hora_formateada = str_pad($hora, 2, '0', STR_PAD_LEFT) . ':00';
                                    echo "<td class='text-center fw-bold bg-light sticky-column' style='font-size: 0.8rem; padding: 4px;'>{$hora_formateada}</td>";

                                    $fecha_dia = clone $inicio_semana;
                                    for ($dia = 0; $dia < 7; $dia++):
                                        $fecha_actual_str = $fecha_dia->format('Y-m-d');
                                        $celda_id = "celda_{$fecha_actual_str}_{$hora_formateada}";

                                        // Buscar reservas para esta fecha y hora
                                        $reservas_en_hora = [];
                                        if (isset($reservas_semana[$fecha_actual_str][$hora_formateada])) {
                                            $reservas_en_hora = $reservas_semana[$fecha_actual_str][$hora_formateada];
                                        }
                                        ?>
                                        <td id='<?php echo $celda_id; ?>'
                                            class='celda-hora p-1 <?php echo !empty($reservas_en_hora) ? 'con-reserva' : ''; ?>'
                                            style='height: 50px; vertical-align: top; font-size: 0.7rem; cursor: pointer; position: relative;'
                                            data-fecha='<?php echo $fecha_actual_str; ?>'
                                            data-hora='<?php echo $hora_formateada; ?>'
                                            data-hora-num='<?php echo $hora; ?>'
                                            data-reservas='<?php echo htmlspecialchars(json_encode($reservas_en_hora), ENT_QUOTES, 'UTF-8'); ?>'
                                            onclick="manejarSeleccionRango(this)">

                                            <!-- Checkbox invisible para selección -->
                                            <input type="checkbox"
                                                   class="checkbox-seleccion"
                                                   style="display: none;"
                                                   data-fecha='<?php echo $fecha_actual_str; ?>'
                                                   data-hora='<?php echo $hora_formateada; ?>'
                                                   data-hora-num='<?php echo $hora; ?>'>

                                            <?php if (empty($reservas_en_hora)): ?>
                                                <!-- Celda disponible -->
                                                <div class="h-100 d-flex align-items-center justify-content-center text-success">
                                                    <i class="fas fa-check"></i>
                                                </div>
                                            <?php else: ?>
                                                <!-- Mostrar áreas reservadas -->
                                                <?php foreach (array_slice($reservas_en_hora, 0, 2) as $reserva): ?>
                                                    <?php
                                                    $clase_badge = $reserva['estado'] == 'confirmada' ? 'bg-success' : 'bg-warning';
                                                    $nombre_corto = strlen($reserva['nombre_area']) > 10 ?
                                                            substr($reserva['nombre_area'], 0, 8) . '...' :
                                                            $reserva['nombre_area'];
                                                    ?>
                                                    <div class="mb-1">
                                                        <span class="badge <?php echo $clase_badge; ?> w-100"
                                                              title="<?php echo $reserva['nombre_area']; ?> (<?php echo $reserva['estado']; ?>): <?php echo $reserva['hora_inicio']; ?>-<?php echo $reserva['hora_fin']; ?>">
                                                            <?php echo $nombre_corto; ?>
                                                        </span>
                                                    </div>
                                                <?php endforeach; ?>
                                                <?php if (count($reservas_en_hora) > 2): ?>
                                                    <small class="text-muted d-block text-center">+<?php echo count($reservas_en_hora) - 2; ?> más</small>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                        <?php
                                        $fecha_dia->modify('+1 day');
                                    endfor;
                                    ?>
                                </tr>
                            <?php endfor; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Controles de selección en la parte inferior -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex gap-3 align-items-center">
                                    <!-- Leyenda de colores -->
                                    <div class="d-flex flex-wrap gap-2 align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="color-box bg-success me-1" style="width: 16px; height: 16px; border-radius: 3px;"></div>
                                            <small class="text-muted">Confirmada</small>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="color-box bg-warning me-1" style="width: 16px; height: 16px; border-radius: 3px;"></div>
                                            <small class="text-muted">Pendiente</small>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="color-box bg-light me-1 border" style="width: 16px; height: 16px; border-radius: 3px;"></div>
                                            <small class="text-muted">Disponible</small>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="color-box bg-info me-1" style="width: 16px; height: 16px; border-radius: 3px;"></div>
                                            <small class="text-muted">Seleccionada</small>
                                        </div>
                                    </div>


                                    <!-- Botones de acción -->
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-success btn-sm" onclick="aplicarAlFormulario()">
                                            <i class="fas fa-check me-1"></i>Aplicar
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="limpiarSeleccion()">
                                            <i class="fas fa-times me-1"></i>Limpiar
                                        </button>
                                    </div>
                                </div>

                                <!-- Contador de selección -->
                                <div>
                                    <span class="badge bg-info fs-6" id="contador-seleccion">0 horas seleccionadas</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resto del formulario se mantiene igual -->
    <!-- Formulario de Registro -->
    <div class="row fade-in">
        <div class="col-lg-8">
            <div class="content-box">
                <div class="content-box-header">
                    <h5><i class="fas fa-edit me-2"></i>Información de la Reserva</h5>
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
                                    <select class="form-control" id="id_area" name="id_area" required onchange="actualizarInfoArea()">
                                        <option value="">Seleccione un área</option>
                                        <?php
                                        if ($areas) {
                                            foreach ($areas as $area) {
                                                if ($area['estado'] == 'disponible') {
                                                    $capacidad = $area['capacidad'] ?? 0;
                                                    $costo_reserva = $area['costo_reserva'] ?? 0;
                                                    echo "<option value='{$area['id_area']}' 
                                                          data-capacidad='{$capacidad}' 
                                                          data-costo-hora='{$costo_reserva}'>";
                                                    echo "{$area['nombre']}";
                                                    echo "</option>";
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
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="hora_inicio" class="form-label">
                                        <i class="fas fa-clock text-verde me-2"></i>Hora de Inicio *
                                    </label>
                                    <select class="form-control" id="hora_inicio" name="hora_inicio" required onchange="calcularCosto(); validarDisponibilidad();">
                                        <option value="">Seleccione hora</option>
                                        <?php for ($hora = 8; $hora <= 21; $hora++): ?>
                                            <?php $hora_formateada = str_pad($hora, 2, '0', STR_PAD_LEFT) . ':00'; ?>
                                            <option value="<?php echo $hora_formateada; ?>"><?php echo $hora_formateada; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="hora_fin" class="form-label">
                                        <i class="fas fa-clock text-azul me-2"></i>Hora de Fin *
                                    </label>
                                    <select class="form-control" id="hora_fin" name="hora_fin" required onchange="calcularCosto(); validarDisponibilidad();">
                                        <option value="">Seleccione hora</option>
                                        <?php for ($hora = 9; $hora <= 22; $hora++): ?>
                                            <?php
                                            $hora_formateada = str_pad($hora, 2, '0', STR_PAD_LEFT) . ':00';
                                            if ($hora == 22) {
                                                $hora_formateada = '22:00';
                                            }
                                            ?>
                                            <option value="<?php echo $hora_formateada; ?>"><?php echo $hora_formateada; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-users text-verde me-2"></i>Capacidad
                                    </label>
                                    <input type="text"
                                           class="form-control bg-light"
                                           id="capacidad_display"
                                           readonly
                                           placeholder="Seleccione un área">
                                    <div class="form-text">Capacidad máxima</div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-money-bill-wave text-azul me-2"></i>Costo Total
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">Bs.</span>
                                        <input type="text"
                                               class="form-control bg-light"
                                               id="costo_display"
                                               readonly
                                               value="0.00">
                                    </div>
                                    <div class="form-text" id="info-costo">Costo por hora: Bs. 0.00</div>
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
                                              placeholder="Describa detalladamente el motivo de la reserva (mínimo 10 caracteres)"></textarea>
                                    <div class="form-text">
                                        <span id="contador-caracteres">0</span>/500 caracteres (mínimo 10)
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Alerta de validación de disponibilidad -->
                        <div id="alerta-disponibilidad" class="alert alert-warning d-none">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <span id="mensaje-disponibilidad"></span>
                        </div>

                        <?php if ($id_rol == 1): ?>
                            <div class="row">
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
                            </div>
                        <?php else: ?>
                            <input type="hidden" name="estado" value="pendiente">
                        <?php endif; ?>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="AreaComunControlador.php?action=listarAreas" class="btn btn-secondary me-md-2">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary" style="background: var(--verde); border: none;" id="btn-registrar">
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
                    <h5><i class="fas fa-info-circle me-2"></i>Información Importante</h5>
                </div>
                <div class="content-box-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-lightbulb me-2"></i>Instrucciones:</h6>
                        <ul class="mb-0 mt-2" style="font-size: 0.9rem;">
                            <li>Vea el cronograma semanal de todas las áreas</li>
                            <li>Las celdas verdes indican disponibilidad</li>
                            <li>Los badges muestran áreas reservadas</li>
                            <li><strong>Haga clic en la primera y última hora</strong> para seleccionar un rango completo</li>
                            <li><strong>Use los botones</strong> para aplicar la selección al formulario</li>
                            <li>Complete el formulario y envíe la reserva</li>
                        </ul>
                    </div>

                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Notas:</h6>
                        <ul class="mb-0 mt-2" style="font-size: 0.9rem;">
                            <li>Las reservas están sujetas a disponibilidad</li>
                            <li>Las reservas pendientes requieren confirmación del administrador</li>
                            <li>Verifique la capacidad máxima del área seleccionada</li>
                            <li>Las reservas canceladas no pueden ser recuperadas</li>
                            <?php if ($id_rol == 1): ?>
                                <li class="text-success"><strong>ROL ADMINISTRADOR:</strong> Puede confirmar reservas directamente</li>
                            <?php else: ?>
                                <li class="text-info"><strong>ROL RESIDENTE:</strong> Las reservas se crean como "Pendiente"</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para aceptar normas de reserva -->
    <div class="modal fade" id="modalNormas" tabindex="-1" aria-labelledby="modalNormasLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-white border-bottom">
                    <h5 class="modal-title text-dark" id="modalNormasLabel">
                        <i class="fas fa-file-contract text-primary me-2"></i>Términos y Condiciones
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <p class="mb-3"><strong>Al confirmar esta reserva, acepta los siguientes términos:</strong></p>

                        <div class="small">
                            <ul class="mb-3">
                                <li>Las reservas confirmadas <strong>NO</strong> pueden ser canceladas</li>
                                <li>El costo es <strong>obligatorio</strong> aunque no use el espacio</li>
                                <li>Es <strong>responsable</strong> por daños durante su reserva</li>
                                <li>Debe respetar el horario y capacidad establecidos</li>
                                <li>Cualquier modificación requiere autorización de administración</li>
                            </ul>
                        </div>

                        <div class="alert alert-warning p-2 small mb-0">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            Estas normas son de cumplimiento obligatorio según el Reglamento de Copropiedad.
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-success" id="btn-confirmar-reserva">
                        <i class="fas fa-check-circle me-2"></i>Acepto y Confirmo
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Script para funcionalidades -->
    <script>
        // Variables globales para selección por rango
        let seleccionRango = {
            fecha: null,
            primeraHora: null,
            ultimaHora: null,
            horas: []
        };

        // Función para manejar selección por rango
        function manejarSeleccionRango(celda) {
            const fecha = celda.getAttribute('data-fecha');
            const horaNum = parseInt(celda.getAttribute('data-hora-num'));

            // Si cambia de fecha, limpiar selección automáticamente
            if (seleccionRango.fecha && seleccionRango.fecha !== fecha) {
                limpiarSeleccion();
                seleccionRango.fecha = fecha;
                seleccionRango.primeraHora = horaNum;
                seleccionRango.ultimaHora = horaNum;
            } else if (!seleccionRango.fecha) {
                // Primera selección
                seleccionRango.fecha = fecha;
                seleccionRango.primeraHora = horaNum;
                seleccionRango.ultimaHora = horaNum;
            } else {
                // Segunda selección - definir el rango
                seleccionRango.ultimaHora = horaNum;
            }

            // Calcular rango completo entre primera y última hora
            const inicio = Math.min(seleccionRango.primeraHora, seleccionRango.ultimaHora);
            const fin = Math.max(seleccionRango.primeraHora, seleccionRango.ultimaHora);

            // Limpiar selección anterior
            limpiarVisualizacionSeleccion();

            // Marcar todas las horas en el rango
            for (let hora = inicio; hora <= fin; hora++) {
                const horaFormateada = hora.toString().padStart(2, '0') + ':00';
                const celda = document.querySelector(`[data-fecha="${seleccionRango.fecha}"][data-hora="${horaFormateada}"]`);
                if (celda) {
                    celda.classList.add('seleccionada');
                    const checkbox = celda.querySelector('.checkbox-seleccion');
                    checkbox.checked = true;
                }
            }

            // Actualizar array de horas seleccionadas
            seleccionRango.horas = [];
            for (let hora = inicio; hora <= fin; hora++) {
                seleccionRango.horas.push(hora);
            }

            actualizarContadorSeleccion();
        }

        // Aplicar selección al formulario
        function aplicarAlFormulario() {
            if (seleccionRango.horas.length === 0) {
                alert('Por favor, seleccione al menos una hora.');
                return;
            }

            if (!seleccionRango.fecha) {
                alert('Por favor, seleccione horas de un mismo día.');
                return;
            }

            // Establecer fecha
            document.getElementById('fecha_reserva').value = seleccionRango.fecha;

            // Establecer hora inicio (primera hora seleccionada)
            const horaInicioValor = seleccionRango.horas[0].toString().padStart(2, '0') + ':00';
            document.getElementById('hora_inicio').value = horaInicioValor;

            // Establecer hora fin (última hora seleccionada + 1)
            const horaFinValor = (seleccionRango.horas[seleccionRango.horas.length - 1] + 1).toString().padStart(2, '0') + ':00';
            if (horaFinValor <= '22:00') {
                document.getElementById('hora_fin').value = horaFinValor;
            }

            // Recalcular costo y validar disponibilidad
            if (document.getElementById('id_area').value) {
                const changeEvent = new Event('change');
                document.getElementById('hora_inicio').dispatchEvent(changeEvent);
                document.getElementById('hora_fin').dispatchEvent(changeEvent);
                document.getElementById('fecha_reserva').dispatchEvent(changeEvent);
            }

            // Hacer scroll suave al formulario
            document.getElementById('formRegistrarReserva').scrollIntoView({
                behavior: 'smooth'
            });

            // QUITÉ EL ALERT QUE MOSTRABA EL MENSAJE
        }

        // Limpiar selección visual (solo la visualización)
        function limpiarVisualizacionSeleccion() {
            document.querySelectorAll('.checkbox-seleccion').forEach(checkbox => {
                checkbox.checked = false;
            });
            document.querySelectorAll('.celda-hora').forEach(celda => {
                celda.classList.remove('seleccionada');
            });
        }

        // Limpiar selección completa
        function limpiarSeleccion() {
            limpiarVisualizacionSeleccion();
            seleccionRango.fecha = null;
            seleccionRango.primeraHora = null;
            seleccionRango.ultimaHora = null;
            seleccionRango.horas = [];
            actualizarContadorSeleccion();
        }

        // Actualizar contador de selección
        function actualizarContadorSeleccion() {
            const contador = document.getElementById('contador-seleccion');
            const count = seleccionRango.horas.length;
            contador.textContent = `${count} hora(s) seleccionada(s)`;

            if (count > 0) {
                contador.classList.remove('bg-secondary');
                contador.classList.add('bg-info');
            } else {
                contador.classList.remove('bg-info');
                contador.classList.add('bg-secondary');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const formRegistrar = document.getElementById('formRegistrarReserva');
            const btnRegistrar = document.getElementById('btn-registrar');
            const fechaReserva = document.getElementById('fecha_reserva');
            const horaInicio = document.getElementById('hora_inicio');
            const horaFin = document.getElementById('hora_fin');
            const idArea = document.getElementById('id_area');
            const motivo = document.getElementById('motivo');
            const contadorCaracteres = document.getElementById('contador-caracteres');
            const capacidadDisplay = document.getElementById('capacidad_display');
            const costoDisplay = document.getElementById('costo_display');
            const infoCosto = document.getElementById('info-costo');
            const alertaDisponibilidad = document.getElementById('alerta-disponibilidad');
            const mensajeDisponibilidad = document.getElementById('mensaje-disponibilidad');
            const modalNormas = new bootstrap.Modal(document.getElementById('modalNormas'));
            const btnConfirmarReserva = document.getElementById('btn-confirmar-reserva');

            // Variables globales para el cálculo de costo
            let costoPorHora = 0;

            // Actualizar información del área seleccionada
            function actualizarInfoArea() {
                const optionSeleccionada = idArea.options[idArea.selectedIndex];
                const capacidad = optionSeleccionada.getAttribute('data-capacidad');
                costoPorHora = parseFloat(optionSeleccionada.getAttribute('data-costo-hora')) || 0;

                if (capacidad && capacidad !== '0') {
                    capacidadDisplay.value = `${capacidad} personas`;
                } else {
                    capacidadDisplay.value = 'No especificada';
                }

                infoCosto.textContent = `Costo por hora: Bs. ${costoPorHora.toFixed(2)}`;
                calcularCosto();
                validarDisponibilidad();
            }

            // Calcular costo total
            function calcularCosto() {
                if (horaInicio.value && horaFin.value && costoPorHora > 0) {
                    const horaInicioNum = parseInt(horaInicio.value.split(':')[0]);
                    const horaFinNum = parseInt(horaFin.value.split(':')[0]);
                    let horasReserva = horaFinNum - horaInicioNum;

                    if (horaFin.value === '21:59') {
                        horasReserva = 22 - horaInicioNum;
                    }

                    const costoTotal = horasReserva * costoPorHora;
                    costoDisplay.value = costoTotal.toFixed(2);
                    infoCosto.textContent = `Costo por hora: Bs. ${costoPorHora.toFixed(2)} | ${horasReserva} hora(s) = Bs. ${costoTotal.toFixed(2)}`;
                } else {
                    costoDisplay.value = '0.00';
                    infoCosto.textContent = `Costo por hora: Bs. ${costoPorHora.toFixed(2)}`;
                }
            }

            // Validar disponibilidad
            function validarDisponibilidad() {
                alertaDisponibilidad.classList.add('d-none');

                if (!idArea.value || !fechaReserva.value || !horaInicio.value || !horaFin.value) {
                    return true;
                }

                const idAreaSeleccionada = parseInt(idArea.value);
                const fechaSeleccionada = fechaReserva.value;
                const horaInicioSeleccionada = horaInicio.value.includes(':') ?
                    horaInicio.value.substring(0, 5) : horaInicio.value;
                const horaFinSeleccionada = horaFin.value.includes(':') ?
                    horaFin.value.substring(0, 5) : horaFin.value;

                const celdasFecha = document.querySelectorAll(`[data-fecha="${fechaSeleccionada}"]`);
                let conflictoEncontrado = null;

                for (let celda of celdasFecha) {
                    const reservasData = celda.getAttribute('data-reservas');
                    if (reservasData && reservasData !== '[]') {
                        const reservas = JSON.parse(reservasData);
                        for (let reserva of reservas) {
                            if (reserva.id_area === idAreaSeleccionada) {
                                const reservaInicio = reserva.hora_inicio.includes(':') ?
                                    reserva.hora_inicio.substring(0, 5) : reserva.hora_inicio;
                                const reservaFin = reserva.hora_fin.includes(':') ?
                                    reserva.hora_fin.substring(0, 5) : reserva.hora_fin;

                                const haySolapamiento = (
                                    horaInicioSeleccionada < reservaFin &&
                                    horaFinSeleccionada > reservaInicio
                                );

                                if (haySolapamiento) {
                                    conflictoEncontrado = reserva;
                                    break;
                                }
                            }
                        }
                        if (conflictoEncontrado) break;
                    }
                }

                if (conflictoEncontrado) {
                    mensajeDisponibilidad.innerHTML = `
                        El área <strong>${conflictoEncontrado.nombre_area}</strong> ya está reservada de
                        <strong>${conflictoEncontrado.hora_inicio}</strong> a <strong>${conflictoEncontrado.hora_fin}</strong>.
                        Por favor, seleccione otro horario.
                    `;
                    alertaDisponibilidad.classList.remove('d-none');
                    return false;
                }

                return true;
            }

            // Contador de caracteres para el motivo
            motivo.addEventListener('input', function() {
                const longitud = this.value.length;
                contadorCaracteres.textContent = longitud;

                if (longitud < 10) {
                    this.classList.add('is-invalid');
                    contadorCaracteres.className = 'text-danger';
                } else {
                    this.classList.remove('is-invalid');
                    contadorCaracteres.className = longitud >= 490 ? 'text-warning' : 'text-success';
                }
            });

            // Validación del formulario
            formRegistrar.addEventListener('submit', function(e) {
                e.preventDefault();

                if (!idArea.value) {
                    idArea.focus();
                    return;
                }

                if (!fechaReserva.value) {
                    fechaReserva.focus();
                    return;
                }

                if (!horaInicio.value) {
                    horaInicio.focus();
                    return;
                }

                if (!horaFin.value) {
                    horaFin.focus();
                    return;
                }

                const horaInicioNum = parseInt(horaInicio.value.replace(':', ''));
                const horaFinNum = parseInt(horaFin.value.replace(':', ''));

                if (horaFinNum <= horaInicioNum) {
                    horaFin.focus();
                    return;
                }

                const motivoTexto = motivo.value.trim();
                if (!motivoTexto || motivoTexto.length < 10) {
                    motivo.focus();
                    return;
                }

                if (!validarDisponibilidad()) {
                    return;
                }

                modalNormas.show();
            });

            // Confirmar reserva después de aceptar normas
            btnConfirmarReserva.addEventListener('click', function() {
                btnRegistrar.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Registrando...';
                btnRegistrar.disabled = true;
                modalNormas.hide();

                setTimeout(() => {
                    formRegistrar.submit();
                }, 500);
            });

            // Event listeners
            horaInicio.addEventListener('change', calcularCosto);
            horaFin.addEventListener('change', calcularCosto);
            idArea.addEventListener('change', actualizarInfoArea);
            fechaReserva.addEventListener('change', validarDisponibilidad);

            // Inicializar
            if (idArea.value) {
                actualizarInfoArea();
            }
            actualizarContadorSeleccion();
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

        .celda-hora:hover {
            background-color: #f8f9fa !important;
            transform: scale(1.01);
            transition: all 0.2s ease;
            cursor: pointer;
        }

        /* Estilos para celdas seleccionadas */
        .celda-hora.seleccionada {
            background-color: #e3f2fd !important;
            border: 2px solid #2196f3 !important;
            position: relative;
        }

        .celda-hora.seleccionada::after {
            content: "✓";
            position: absolute;
            top: 2px;
            right: 2px;
            color: #2196f3;
            font-weight: bold;
            font-size: 0.8rem;
            background: white;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }

        /* Estilos para celdas con reservas */
        .celda-hora.con-reserva {
            opacity: 0.8;
        }

        .celda-hora.con-reserva.seleccionada {
            opacity: 1;
            background-color: #fff3cd !important;
            border: 2px solid #ffc107 !important;
        }

        .celda-hora.con-reserva.seleccionada::after {
            color: #ffc107;
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

        .bg-info {
            background-color: #17a2b8 !important;
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

        /* Mejorar visibilidad de badges */
        .badge {
            font-size: 0.65rem;
            padding: 0.2em 0.4em;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
            display: block;
            text-align: center;
            font-weight: 500;
        }

        .celda-hora .badge {
            margin-bottom: 2px;
        }

        .bg-light {
            background-color: #f8f9fa !important;
        }

        .table-agenda th,
        .table-agenda td {
            padding: 4px 6px;
            font-size: 0.8rem;
        }

        .table-agenda {
            font-size: 0.8rem;
        }

        .modal-body ul {
            padding-left: 1.2rem;
            margin-bottom: 1rem;
        }

        .modal-body li {
            margin-bottom: 0.5rem;
        }

        /* Estilos para botones */
        .btn-sm {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
        }

        /* Ajustes para la tabla sin scroll */
        .table-responsive {
            overflow: visible;
        }

        /* Separadores verticales */
        .vr {
            width: 1px;
            height: 24px;
            background-color: #dee2e6;
            margin: 0 0.5rem;
        }
    </style>

<?php include("../../includes/footer.php"); ?>
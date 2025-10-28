<?php include("../../includes/header.php"); ?>

    <!-- Page Header -->
    <div class="page-header fade-in">
        <div class="page-title">
            <h1>Asignar Servicios a Departamentos</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item"><a href="../controlador/ServicioControlador.php?action=listarServicios">Servicios</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Asignar Servicios</li>
                </ol>
            </nav>
        </div>
        <div class="page-actions">
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
        <!-- Formulario de Asignación -->
        <div class="col-lg-6">
            <div class="content-box">
                <div class="content-box-header">
                    <h5><i class="fas fa-link me-2 text-primary"></i>Asignar Servicio a Departamento</h5>
                </div>
                <div class="content-box-body">
                    <form id="formAsignarServicio" action="../controlador/ServicioControlador.php" method="POST">
                        <input type="hidden" name="action" value="asignarServicioDepartamento">

                        <div class="mb-3">
                            <label for="departamento" class="form-label">
                                <i class="fas fa-building text-info me-2"></i>Departamento *
                            </label>
                            <select class="form-control" id="departamento" name="id_departamento" required>
                                <option value="">Seleccionar Departamento</option>
                                <?php
                                foreach ($departamentos as $depto) {
                                    $selected = (isset($_GET['id_departamento']) && $_GET['id_departamento'] == $depto['id_departamento']) ? 'selected' : '';
                                    echo "<option value='{$depto['id_departamento']}' $selected>
                                        Depto {$depto['numero']} - Piso {$depto['piso']}
                                      </option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="servicio" class="form-label">
                                <i class="fas fa-bolt text-warning me-2"></i>Servicio *
                            </label>
                            <select class="form-control" id="servicio" name="id_servicio" required>
                                <option value="">Seleccionar Servicio</option>
                                <?php
                                foreach ($servicios as $servicio) {
                                    if ($servicio['estado'] == 'activo') {
                                        $icon_class = '';
                                        switch($servicio['nombre']) {
                                            case 'agua':
                                                $icon_class = 'fas fa-tint text-info';
                                                break;
                                            case 'luz':
                                                $icon_class = 'fas fa-bolt text-warning';
                                                break;
                                            case 'gas':
                                                $icon_class = 'fas fa-fire text-danger';
                                                break;
                                        }
                                        echo "<option value='{$servicio['id_servicio']}' data-unidad='{$servicio['unidad_medida']}'>
                                            <i class='$icon_class me-2'></i>" . ucfirst($servicio['nombre']) . " ({$servicio['unidad_medida']})
                                          </option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="codigo_medidor" class="form-label">
                                <i class="fas fa-tachometer-alt text-success me-2"></i>Código del Medidor *
                            </label>
                            <input type="text"
                                   class="form-control"
                                   id="codigo_medidor"
                                   name="codigo_medidor"
                                   required
                                   maxlength="50"
                                   placeholder="Ej: MED-AGUA-001, MED-LUZ-001, etc."
                                   value="<?php echo isset($_GET['codigo_medidor']) ? htmlspecialchars($_GET['codigo_medidor']) : ''; ?>">
                            <div class="form-text">Código único identificador del medidor</div>
                        </div>

                        <div class="mb-3">
                            <label for="fecha_instalacion" class="form-label">
                                <i class="fas fa-calendar-alt text-primary me-2"></i>Fecha de Instalación
                            </label>
                            <input type="date"
                                   class="form-control"
                                   id="fecha_instalacion"
                                   name="fecha_instalacion"
                                   value="<?php echo date('Y-m-d'); ?>">
                            <div class="form-text">Fecha en que se instaló el medidor</div>
                        </div>

                        <div class="mb-3">
                            <label for="estado_medidor" class="form-label">
                                <i class="fas fa-toggle-on text-info me-2"></i>Estado del Medidor
                            </label>
                            <select class="form-control" id="estado_medidor" name="estado_medidor">
                                <option value="activo" selected>Activo</option>
                                <option value="mantenimiento">En Mantenimiento</option>
                                <option value="baja">De Baja</option>
                                <option value="corte">Corte de Servicio</option>
                            </select>
                            <div class="form-text">Estado inicial del medidor</div>
                        </div>

                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle me-2"></i>Información</h6>
                            <p class="mb-0">Al asignar un servicio, se creará un medidor asociado al departamento para llevar el control de consumos.</p>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-link me-2"></i>Asignar Servicio
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Información y Medidores Existentes -->
        <div class="col-lg-6">
            <!-- Información del Departamento Seleccionado -->
            <div class="content-box mb-4">
                <div class="content-box-header">
                    <h5><i class="fas fa-info-circle me-2 text-info"></i>Información de Asignación</h5>
                </div>
                <div class="content-box-body">
                    <div id="infoDepartamento" class="text-center text-muted">
                        <i class="fas fa-building fa-3x mb-3"></i>
                        <p>Seleccione un departamento para ver información detallada</p>
                    </div>
                    <div id="detalleDepartamento" style="display: none;">
                        <h6 id="nombreDepartamento" class="text-primary"></h6>
                        <div id="serviciosAsignados" class="mt-3">
                            <small class="text-muted">Servicios asignados:</small>
                            <div id="listaServicios" class="mt-2"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Medidores del Departamento -->
            <div class="content-box">
                <div class="content-box-header">
                    <h5><i class="fas fa-list me-2 text-success"></i>Medidores del Departamento</h5>
                </div>
                <div class="content-box-body">
                    <div id="listaMedidores" class="text-center text-muted">
                        <i class="fas fa-tachometer-alt fa-2x mb-2"></i>
                        <p>No hay medidores asignados</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Todas las Asignaciones -->
    <div class="row fade-in mt-4">
        <div class="col-12">
            <div class="content-box">
                <div class="content-box-header d-flex justify-content-between align-items-center">
                    <h5><i class="fas fa-list-alt me-2 text-primary"></i>Todas las Asignaciones de Servicios</h5>
                    <span class="badge bg-primary" id="totalAsignaciones">0 asignaciones</span>
                </div>
                <div class="content-box-body">
                    <div class="table-responsive">
                        <table id="tablaAsignaciones" class="table table-hover table-striped">
                            <thead>
                            <tr>
                                <th>Departamento</th>
                                <th>Servicio</th>
                                <th>Código Medidor</th>
                                <th>Fecha Instalación</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (empty($medidores)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <i class="fas fa-tachometer-alt fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No hay asignaciones de servicios registradas</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($medidores as $medidor): ?>
                                    <tr>
                                        <td>
                                            <i class="fas fa-building text-info me-2"></i>
                                            <strong>Depto <?php echo htmlspecialchars($medidor['numero']); ?></strong>
                                            <br><small class="text-muted">Piso <?php echo htmlspecialchars($medidor['piso']); ?></small>
                                        </td>
                                        <td>
                                            <?php
                                            $icon_class = '';
                                            $badge_class = '';
                                            switch($medidor['servicio']) {
                                                case 'agua':
                                                    $icon_class = 'fas fa-tint text-info';
                                                    $badge_class = 'bg-info';
                                                    break;
                                                case 'luz':
                                                    $icon_class = 'fas fa-bolt text-warning';
                                                    $badge_class = 'bg-warning';
                                                    break;
                                                case 'gas':
                                                    $icon_class = 'fas fa-fire text-danger';
                                                    $badge_class = 'bg-danger';
                                                    break;
                                            }
                                            ?>
                                            <i class="<?php echo $icon_class; ?> me-2"></i>
                                            <?php echo ucfirst(htmlspecialchars($medidor['servicio'])); ?>
                                            <br><small class="text-muted"><?php echo htmlspecialchars($medidor['unidad_medida']); ?></small>
                                        </td>
                                        <td>
                                            <code><?php echo htmlspecialchars($medidor['codigo']); ?></code>
                                        </td>
                                        <td>
                                            <?php echo date('d/m/Y', strtotime($medidor['fecha_instalacion'])); ?>
                                        </td>
                                        <td>
                                            <?php
                                            $estado_badge = '';
                                            switch($medidor['estado']) {
                                                case 'activo':
                                                    $estado_badge = 'bg-success';
                                                    break;
                                                case 'mantenimiento':
                                                    $estado_badge = 'bg-warning';
                                                    break;
                                                case 'baja':
                                                    $estado_badge = 'bg-secondary';
                                                    break;
                                                case 'corte':
                                                    $estado_badge = 'bg-danger';
                                                    break;
                                            }
                                            ?>
                                            <span class="badge <?php echo $estado_badge; ?>">
                                                <?php echo ucfirst(htmlspecialchars($medidor['estado'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-warning"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editarMedidorModal"
                                                        data-id="<?php echo htmlspecialchars($medidor['id_medidor']); ?>"
                                                        data-codigo="<?php echo htmlspecialchars($medidor['codigo']); ?>"
                                                        data-estado="<?php echo htmlspecialchars($medidor['estado']); ?>"
                                                        data-fecha="<?php echo htmlspecialchars($medidor['fecha_instalacion']); ?>"
                                                        title="Editar medidor">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-outline-danger"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#eliminarMedidorModal"
                                                        data-id="<?php echo htmlspecialchars($medidor['id_medidor']); ?>"
                                                        data-codigo="<?php echo htmlspecialchars($medidor['codigo']); ?>"
                                                        data-servicio="<?php echo htmlspecialchars($medidor['servicio']); ?>"
                                                        data-departamento="<?php echo htmlspecialchars($medidor['numero']); ?>"
                                                        title="Eliminar asignación">
                                                    <i class="fas fa-unlink"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Editar Medidor -->
    <div class="modal fade" id="editarMedidorModal" tabindex="-1" aria-labelledby="editarMedidorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarMedidorModalLabel">
                        <i class="fas fa-edit me-2"></i>Editar Medidor
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="../controlador/ServicioControlador.php" id="formEditarMedidor">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="editarMedidor">
                        <input type="hidden" name="id_medidor" id="editIdMedidor">

                        <div class="mb-3">
                            <label for="editCodigo" class="form-label">Código del Medidor</label>
                            <input type="text" class="form-control" id="editCodigo" name="codigo_medidor" required>
                        </div>

                        <div class="mb-3">
                            <label for="editEstado" class="form-label">Estado</label>
                            <select class="form-control" id="editEstado" name="estado_medidor" required>
                                <option value="activo">Activo</option>
                                <option value="mantenimiento">En Mantenimiento</option>
                                <option value="baja">De Baja</option>
                                <option value="corte">Corte de Servicio</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="editFecha" class="form-label">Fecha de Instalación</label>
                            <input type="date" class="form-control" id="editFecha" name="fecha_instalacion" required>
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

    <!-- Modal Eliminar Asignación -->
    <div class="modal fade" id="eliminarMedidorModal" tabindex="-1" aria-labelledby="eliminarMedidorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eliminarMedidorModalLabel">
                        <i class="fas fa-unlink me-2"></i>Eliminar Asignación
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="../controlador/ServicioControlador.php" id="formEliminarMedidor">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="eliminarMedidor">
                        <input type="hidden" name="id_medidor" id="eliminarIdMedidor">

                        <div class="text-center">
                            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                            <h6>¿Está seguro que desea eliminar esta asignación?</h6>
                            <p class="text-muted mb-0">Medidor: <strong id="eliminarCodigoMedidor"></strong></p>
                            <p class="text-muted mb-0">Servicio: <strong id="eliminarServicio"></strong></p>
                            <p class="text-muted">Departamento: <strong id="eliminarDepartamento"></strong></p>
                            <div class="alert alert-danger mt-3">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <strong>Advertencia:</strong> Esta acción eliminará también el historial de consumos asociado.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-unlink me-2"></i>Eliminar Asignación
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script para funcionalidades -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const formAsignar = document.getElementById('formAsignarServicio');
            const selectDepto = document.getElementById('departamento');
            const selectServicio = document.getElementById('servicio');
            const infoDepartamento = document.getElementById('infoDepartamento');
            const detalleDepartamento = document.getElementById('detalleDepartamento');
            const listaMedidores = document.getElementById('listaMedidores');
            const totalAsignaciones = document.getElementById('totalAsignaciones');

            // Actualizar total de asignaciones
            if (totalAsignaciones) {
                const total = <?php echo count($medidores); ?>;
                totalAsignaciones.textContent = total + ' asignación' + (total !== 1 ? 'es' : '');
            }

            // Cargar información del departamento seleccionado
            selectDepto.addEventListener('change', function() {
                const idDepartamento = this.value;

                if (!idDepartamento) {
                    infoDepartamento.style.display = 'block';
                    detalleDepartamento.style.display = 'none';
                    listaMedidores.innerHTML = `
                <i class="fas fa-tachometer-alt fa-2x mb-2"></i>
                <p>No hay medidores asignados</p>
            `;
                    return;
                }

                // Simular carga de información (en un caso real, harías una petición AJAX)
                cargarInformacionDepartamento(idDepartamento);
            });

            function cargarInformacionDepartamento(idDepartamento) {
                // Simular delay de carga
                setTimeout(() => {
                    // En una implementación real, esto vendría de una petición AJAX al servidor
                    const departamentos = <?php echo json_encode($departamentos); ?>;
                    const medidores = <?php echo json_encode($medidores); ?>;

                    const depto = departamentos.find(d => d.id_departamento == idDepartamento);
                    if (!depto) return;

                    // Actualizar información del departamento
                    document.getElementById('nombreDepartamento').textContent =
                        `Departamento ${depto.numero} - Piso ${depto.piso}`;

                    // Mostrar servicios asignados
                    const serviciosAsignados = medidores.filter(m => m.id_departamento == idDepartamento);
                    let listaHTML = '';

                    if (serviciosAsignados.length > 0) {
                        serviciosAsignados.forEach(medidor => {
                            let iconClass = '';
                            let badgeClass = '';

                            switch(medidor.servicio) {
                                case 'agua':
                                    iconClass = 'fas fa-tint text-info';
                                    badgeClass = 'bg-info';
                                    break;
                                case 'luz':
                                    iconClass = 'fas fa-bolt text-warning';
                                    badgeClass = 'bg-warning';
                                    break;
                                case 'gas':
                                    iconClass = 'fas fa-fire text-danger';
                                    badgeClass = 'bg-danger';
                                    break;
                            }

                            listaHTML += `
                        <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                            <div>
                                <i class="${iconClass} me-2"></i>
                                <span>${medidor.servicio.charAt(0).toUpperCase() + medidor.servicio.slice(1)}</span>
                                <small class="text-muted d-block">${medidor.codigo}</small>
                            </div>
                            <span class="badge ${badgeClass}">${medidor.estado}</span>
                        </div>
                    `;
                        });
                    } else {
                        listaHTML = '<p class="text-muted">No hay servicios asignados</p>';
                    }

                    document.getElementById('listaServicios').innerHTML = listaHTML;

                    // Actualizar lista de medidores
                    if (serviciosAsignados.length > 0) {
                        listaMedidores.innerHTML = listaHTML;
                    } else {
                        listaMedidores.innerHTML = `
                    <i class="fas fa-tachometer-alt fa-2x mb-2"></i>
                    <p>No hay medidores asignados a este departamento</p>
                `;
                    }

                    infoDepartamento.style.display = 'none';
                    detalleDepartamento.style.display = 'block';
                }, 500);
            }

            // Validación del formulario de asignación
            formAsignar.addEventListener('submit', function(e) {
                const depto = document.getElementById('departamento').value;
                const servicio = document.getElementById('servicio').value;
                const codigo = document.getElementById('codigo_medidor').value.trim();

                if (!depto || !servicio || !codigo) {
                    e.preventDefault();
                    showAlert('Por favor, complete todos los campos obligatorios', 'error');
                    return;
                }

                if (codigo.length < 3) {
                    e.preventDefault();
                    showAlert('El código del medidor debe tener al menos 3 caracteres', 'error');
                    return;
                }

                // Mostrar loading
                const btn = this.querySelector('button[type="submit"]');
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Asignando...';
                btn.disabled = true;
            });

            // Cargar datos en el modal de editar medidor
            const editarMedidorModal = document.getElementById('editarMedidorModal');
            editarMedidorModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const idMedidor = button.getAttribute('data-id');
                const codigo = button.getAttribute('data-codigo');
                const estado = button.getAttribute('data-estado');
                const fecha = button.getAttribute('data-fecha');

                document.getElementById('editIdMedidor').value = idMedidor;
                document.getElementById('editCodigo').value = codigo;
                document.getElementById('editEstado').value = estado;
                document.getElementById('editFecha').value = fecha;
            });

            // Cargar datos en el modal de eliminar medidor
            const eliminarMedidorModal = document.getElementById('eliminarMedidorModal');
            eliminarMedidorModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const idMedidor = button.getAttribute('data-id');
                const codigo = button.getAttribute('data-codigo');
                const servicio = button.getAttribute('data-servicio');
                const departamento = button.getAttribute('data-departamento');

                document.getElementById('eliminarIdMedidor').value = idMedidor;
                document.getElementById('eliminarCodigoMedidor').textContent = codigo;
                document.getElementById('eliminarServicio').textContent = servicio;
                document.getElementById('eliminarDepartamento').textContent = 'Depto ' + departamento;
            });

            // Función para mostrar alertas temporales
            function showAlert(message, type) {
                const alertClass = type === 'error' ? 'alert-danger' : 'alert-success';
                const icon = type === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle';

                const alertDiv = document.createElement('div');
                alertDiv.className = `alert ${alertClass} alert-dismissible fade show`;
                alertDiv.innerHTML = `
            <i class="fas ${icon} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;

                const pageHeader = document.querySelector('.page-header');
                pageHeader.parentNode.insertBefore(alertDiv, pageHeader.nextSibling);

                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        const bsAlert = new bootstrap.Alert(alertDiv);
                        bsAlert.close();
                    }
                }, 5000);
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

        .btn-group-sm .btn {
            padding: 0.25rem 0.5rem;
        }

        /* Estilos para la tabla */
        .table th {
            border-top: none;
            font-weight: 600;
            background-color: #f8f9fa;
        }

        /* Iconos específicos */
        .fa-tint { color: #17a2b8 !important; }
        .fa-bolt { color: #ffc107 !important; }
        .fa-fire { color: #dc3545 !important; }
        .fa-link { color: #007bff !important; }
        .fa-unlink { color: #dc3545 !important; }

        /* Badges de estado */
        .badge.bg-success { background-color: #28a745 !important; }
        .badge.bg-warning { background-color: #ffc107 !important; color: #000 !important; }
        .badge.bg-danger { background-color: #dc3545 !important; }
        .badge.bg-info { background-color: #17a2b8 !important; }
        .badge.bg-secondary { background-color: #6c757d !important; }

        /* Responsive */
        @media (max-width: 768px) {
            .btn-group {
                flex-direction: column;
            }

            .btn-group .btn {
                margin-bottom: 0.25rem;
            }
        }
    </style>

<?php include("../../includes/footer.php"); ?>
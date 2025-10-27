<?php include("../../includes/header.php"); ?>

    <!-- Page Header -->
    <div class="page-header fade-in">
        <div class="page-title">
            <h1>Registrar Nuevo Incidente</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item"><a href="IncidenteControlador.php?action=listarIncidentes">Incidentes</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Registrar Incidente</li>
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

    <!-- Formulario de Registro -->
    <div class="row fade-in">
        <div class="col-lg-8">
            <div class="content-box">
                <div class="content-box-header">
                    <h5>Información del Incidente</h5>
                    <?php if ($_SESSION['id_rol'] == 2): ?>
                        <span class="badge bg-info">Vista Residente</span>
                    <?php endif; ?>
                </div>
                <div class="content-box-body">
                    <form id="formRegistrarIncidente" action="IncidenteControlador.php" method="POST">
                        <input type="hidden" name="action" value="registrarIncidente">

                        <!-- Campos para Administrador -->
                        <?php if ($_SESSION['id_rol'] == 1): ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="id_departamento" class="form-label">
                                            <i class="fas fa-building text-primary me-2"></i>Departamento *
                                        </label>
                                        <select class="form-control" id="id_departamento" name="id_departamento" required>
                                            <option value="">Seleccione un departamento</option>
                                            <?php foreach ($departamentos as $departamento): ?>
                                                <option value="<?php echo htmlspecialchars($departamento['id_departamento']); ?>"
                                                        <?php echo (isset($_GET['id_departamento']) && $_GET['id_departamento'] == $departamento['id_departamento']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($departamento['numero']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="form-text">Departamento donde ocurre el incidente</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="id_residente" class="form-label">
                                            <i class="fas fa-user text-info me-2"></i>Residente Reportante *
                                        </label>
                                        <select class="form-control" id="id_residente" name="id_residente" required disabled>
                                            <option value="">Primero seleccione un departamento</option>
                                        </select>
                                        <div class="form-text" id="residente-info">Persona que reporta el incidente</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="tipo" class="form-label">
                                            <i class="fas fa-tag text-success me-2"></i>Tipo de Incidente *
                                        </label>
                                        <select class="form-control" id="tipo" name="tipo" required>
                                            <option value="">Seleccione un tipo</option>
                                            <option value="interno" <?php echo (isset($_GET['tipo']) && $_GET['tipo'] == 'interno') ? 'selected' : ''; ?>>Interno</option>
                                            <option value="externo" <?php echo (isset($_GET['tipo']) && $_GET['tipo'] == 'externo') ? 'selected' : ''; ?>>Externo</option>
                                        </select>
                                        <div class="form-text">Interno: recursos propios / Externo: requiere contratación externa</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="id_area" class="form-label">
                                            <i class="fas fa-map-marker-alt text-warning me-2"></i>Área Común (Opcional)
                                        </label>
                                        <select class="form-control" id="id_area" name="id_area">
                                            <option value="">Seleccione un área común</option>
                                            <?php foreach ($areas as $area): ?>
                                                <option value="<?php echo htmlspecialchars($area['id_area']); ?>"
                                                        <?php echo (isset($_GET['id_area']) && $_GET['id_area'] == $area['id_area']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($area['nombre']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="form-text">Si el incidente ocurre en un área común</div>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- Campos para Residente (ocultos) -->
                            <?php if (!empty($departamentos)): ?>
                                <input type="hidden" name="id_departamento" value="<?php echo $departamentos[0]['id_departamento']; ?>">
                            <?php endif; ?>
                            <input type="hidden" name="id_residente" value="<?php echo $_SESSION['id_persona'] ?? ''; ?>">
                            <input type="hidden" name="tipo" value="interno">
                            <input type="hidden" name="id_area" value="">

                            <!-- Información del Residente -->
                            <div class="alert alert-info">
                                <h6><i class="fas fa-user me-2"></i>Información del Reportante</h6>
                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <strong>Departamento:</strong><br>
                                        <?php
                                        if (!empty($departamentos)) {
                                            echo htmlspecialchars($departamentos[0]['numero']);
                                        } else {
                                            echo '<span class="text-danger">No tiene departamento asignado</span>';
                                        }
                                        ?>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Residente:</strong><br>
                                        <?php echo htmlspecialchars( $_SESSION['nombre']." ".$_SESSION['apellido_paterno']." ".$_SESSION['apellido_materno'] ?? 'Usuario'); ?>
                                    </div>
                                </div>
                                <?php if (count($departamentos) > 1): ?>
                                    <div class="row mt-2">
                                        <div class="col-12">
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Tiene acceso a múltiples departamentos. El incidente se registrará para el departamento principal.
                                            </small>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Campos comunes para ambos roles -->
                        <div class="form-group mb-3">
                            <label for="descripcion" class="form-label">
                                <i class="fas fa-align-left text-primary me-2"></i>Descripción Breve *
                            </label>
                            <input type="text"
                                   class="form-control"
                                   id="descripcion"
                                   name="descripcion"
                                   required
                                   maxlength="200"
                                   placeholder="Descripción breve del incidente"
                                   value="<?php echo isset($_GET['descripcion']) ? htmlspecialchars($_GET['descripcion']) : ''; ?>">
                            <div class="form-text">Descripción corta del incidente (máximo 200 caracteres)</div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="descripcion_detallada" class="form-label">
                                <i class="fas fa-file-alt text-secondary me-2"></i>Descripción Detallada
                            </label>
                            <textarea class="form-control"
                                      id="descripcion_detallada"
                                      name="descripcion_detallada"
                                      rows="4"
                                      maxlength="1000"
                                      placeholder="Descripción detallada del incidente, incluyendo ubicación exacta, daños observados, etc."><?php echo isset($_GET['descripcion_detallada']) ? htmlspecialchars($_GET['descripcion_detallada']) : ''; ?></textarea>
                            <div class="form-text">Descripción completa del incidente (máximo 1000 caracteres)</div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="IncidenteControlador.php?action=listarIncidentes" class="btn btn-secondary me-md-2">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Registrar Incidente
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
                        <ul class="mb-0 mt-2">
                            <li>Los campos marcados con (*) son obligatorios</li>
                            <?php if ($_SESSION['id_rol'] == 1): ?>
                                <li>Seleccione el departamento y residente correctos</li>
                                <li>Describa claramente el problema</li>
                                <li>Especifique si es interno o externo</li>
                                <li>Incluya el área común si aplica</li>
                            <?php else: ?>
                                <li>Su departamento y información personal se usarán automáticamente</li>
                                <li>Describa claramente el problema que necesita atención</li>
                                <li>Sea específico sobre la ubicación y daños</li>
                                <li>Todos los incidentes se registrarán como tipo "interno" inicialmente</li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <?php if ($_SESSION['id_rol'] == 1): ?>
                        <div class="alert alert-warning">
                            <h6><i class="fas fa-exclamation-triangle me-2"></i>Tipos de Incidente:</h6>
                            <ul class="mb-0 mt-2">
                                <li><strong>Interno:</strong> Puede ser resuelto con personal interno</li>
                                <li><strong>Externo:</strong> Requiere contratación de servicios externos</li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <h6><i class="fas fa-exclamation-triangle me-2"></i>Para Residentes:</h6>
                            <ul class="mb-0 mt-2">
                                <li>Su incidente será revisado por el administrador</li>
                                <li>El tipo de incidente se determinará posteriormente</li>
                                <li>Recibirá actualizaciones sobre el progreso</li>
                                <li>Para emergencias, contacte directamente al administrador</li>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="alert alert-success">
                        <h6><i class="fas fa-lightbulb me-2"></i>Sugerencias:</h6>
                        <ul class="mb-0 mt-2">
                            <li>Sea específico en la descripción</li>
                            <li>Incluya ubicación exacta</li>
                            <li>Mencione daños observados</li>
                            <li>Especifique urgencia si aplica</li>
                            <li>Adjunte fotos si es posible</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script para validaciones y carga dinámica de residentes -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const formRegistrar = document.getElementById('formRegistrarIncidente');
            const btnRegistrar = formRegistrar.querySelector('button[type="submit"]');
            const selectDepartamento = document.getElementById('id_departamento');
            const selectResidente = document.getElementById('id_residente');
            const residenteInfo = document.getElementById('residente-info');

            <?php if ($_SESSION['id_rol'] == 1): ?>
            // Cargar residentes cuando se selecciona un departamento
            selectDepartamento.addEventListener('change', function() {
                const idDepartamento = this.value;

                if (idDepartamento) {
                    // Mostrar loading
                    selectResidente.innerHTML = '<option value="">Cargando residentes...</option>';
                    selectResidente.disabled = true;
                    residenteInfo.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Cargando residentes del departamento...';

                    // Hacer petición AJAX para obtener residentes del departamento
                    fetch(`IncidenteControlador.php?action=obtenerResidentesPorDepartamento&id_departamento=${idDepartamento}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                selectResidente.innerHTML = '<option value="">Seleccione un residente</option>';

                                if (data.residentes.length > 0) {
                                    data.residentes.forEach(residente => {
                                        const option = document.createElement('option');
                                        option.value = residente.id_persona;
                                        option.textContent = residente.nombre_completo;
                                        selectResidente.appendChild(option);
                                    });
                                    selectResidente.disabled = false;
                                    residenteInfo.innerHTML = `${data.residentes.length} residente(s) encontrado(s) en este departamento`;
                                } else {
                                    selectResidente.innerHTML = '<option value="">No hay residentes en este departamento</option>';
                                    residenteInfo.innerHTML = '<span class="text-warning">No hay residentes activos en este departamento</span>';
                                }
                            } else {
                                selectResidente.innerHTML = '<option value="">Error al cargar residentes</option>';
                                residenteInfo.innerHTML = '<span class="text-danger">Error al cargar residentes</span>';
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            selectResidente.innerHTML = '<option value="">Error al cargar residentes</option>';
                            residenteInfo.innerHTML = '<span class="text-danger">Error de conexión</span>';
                        });
                } else {
                    selectResidente.innerHTML = '<option value="">Primero seleccione un departamento</option>';
                    selectResidente.disabled = true;
                    residenteInfo.innerHTML = 'Persona que reporta el incidente';
                }
            });

            // Si ya hay un departamento seleccionado (por ejemplo, al recargar la página), cargar sus residentes
            <?php if (isset($_GET['id_departamento']) && !empty($_GET['id_departamento'])): ?>
            setTimeout(() => {
                selectDepartamento.dispatchEvent(new Event('change'));
            }, 500);
            <?php endif; ?>
            <?php endif; ?>

            // Validación del formulario
            formRegistrar.addEventListener('submit', function(e) {
                <?php if ($_SESSION['id_rol'] == 1): ?>
                const departamento = document.getElementById('id_departamento').value;
                const residente = document.getElementById('id_residente').value;
                const tipo = document.getElementById('tipo').value;
                <?php endif; ?>

                const descripcion = document.getElementById('descripcion').value.trim();

                <?php if ($_SESSION['id_rol'] == 1): ?>
                if (!departamento) {
                    e.preventDefault();
                    showAlert('Por favor, seleccione el departamento', 'error');
                    document.getElementById('id_departamento').focus();
                    return;
                }

                if (!residente) {
                    e.preventDefault();
                    showAlert('Por favor, seleccione el residente reportante', 'error');
                    document.getElementById('id_residente').focus();
                    return;
                }

                if (!tipo) {
                    e.preventDefault();
                    showAlert('Por favor, seleccione el tipo de incidente', 'error');
                    document.getElementById('tipo').focus();
                    return;
                }
                <?php endif; ?>

                if (!descripcion) {
                    e.preventDefault();
                    showAlert('Por favor, ingrese la descripción del incidente', 'error');
                    document.getElementById('descripcion').focus();
                    return;
                }

                if (descripcion.length < 10) {
                    e.preventDefault();
                    showAlert('La descripción debe tener al menos 10 caracteres', 'error');
                    document.getElementById('descripcion').focus();
                    return;
                }

                // Mostrar loading en el botón
                btnRegistrar.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Registrando...';
                btnRegistrar.disabled = true;
            });

            // Validación en tiempo real
            document.getElementById('descripcion').addEventListener('input', function() {
                if (this.value.length < 10) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });

            // Auto-ocultar alertas después de 5 segundos
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert-notificacion');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);

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

        /* Estilos para las alertas */
        .alert {
            border-radius: 8px;
            border: none;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .badge.bg-info {
            background-color: #17a2b8 !important;
        }

        /* Estilos para selects deshabilitados */
        select:disabled {
            background-color: #f8f9fa;
            opacity: 0.7;
        }
    </style>

<?php include("../../includes/footer.php"); ?>
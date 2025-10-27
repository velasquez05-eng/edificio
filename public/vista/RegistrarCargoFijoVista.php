<?php include("../../includes/header.php"); ?>

    <!-- Page Header -->
    <div class="page-header fade-in">
        <div class="page-title">
            <h1>Registrar Nuevo Cargo Fijo</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item"><a href="CargosFijosControlador.php?action=listarCargosVista">Cargos Fijos</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Registrar Cargo</li>
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
                    <h5>Información del Cargo Fijo</h5>
                </div>
                <div class="content-box-body">
                    <form id="formRegistrarCargo" action="CargosFijosControlador.php" method="POST">
                        <input type="hidden" name="action" value="crearCargo">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="nombre_cargo" class="form-label">
                                        <i class="fas fa-tag text-primary me-2"></i>Nombre del Cargo *
                                    </label>
                                    <input type="text"
                                           class="form-control"
                                           id="nombre_cargo"
                                           name="nombre_cargo"
                                           required
                                           maxlength="255"
                                           placeholder="Ej: Mantenimiento general, Agua, Luz, etc."
                                           value="<?php echo isset($_GET['nombre_cargo']) ? htmlspecialchars($_GET['nombre_cargo']) : ''; ?>">
                                    <div class="form-text">Nombre descriptivo del cargo fijo</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="monto" class="form-label">
                                        <i class="fas fa-money-bill-wave text-success me-2"></i>Monto (Bs.) *
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">Bs.</span>
                                        <input type="number"
                                               class="form-control"
                                               id="monto"
                                               name="monto"
                                               required
                                               min="0"
                                               max="10000"
                                               step="0.01"
                                               placeholder="0.00"
                                               value="<?php echo isset($_GET['monto']) ? htmlspecialchars($_GET['monto']) : ''; ?>">
                                        <span class="input-group-text">BOB</span>
                                    </div>
                                    <div class="form-text">Monto mensual en Bolivianos</div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="descripcion" class="form-label">
                                <i class="fas fa-align-left text-info me-2"></i>Descripción
                            </label>
                            <textarea class="form-control"
                                      id="descripcion"
                                      name="descripcion"
                                      rows="3"
                                      maxlength="500"
                                      placeholder="Descripción detallada del cargo fijo y su propósito"><?php echo isset($_GET['descripcion']) ? htmlspecialchars($_GET['descripcion']) : ''; ?></textarea>
                            <div class="form-text">Descripción opcional del cargo (máximo 500 caracteres)</div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="estado" class="form-label">
                                <i class="fas fa-toggle-on text-success me-2"></i>Estado *
                            </label>
                            <select class="form-control" id="estado" name="estado" required>
                                <option value="">Seleccione un estado</option>
                                <option value="activo" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'activo') ? 'selected' : ''; ?>>Activo</option>
                                <option value="inactivo" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                            </select>
                            <div class="form-text">Estado inicial del cargo</div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="CargosFijosControlador.php?action=listarCargosVista" class="btn btn-secondary me-md-2">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Registrar Cargo
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
                            <li>El nombre del cargo debe ser único y descriptivo</li>
                            <li>El monto debe ser el valor mensual por departamento</li>
                            <li>Los cargos activos se incluirán en la generación de conceptos</li>
                        </ul>
                    </div>

                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Estados:</h6>
                        <ul class="mb-0 mt-2">
                            <li><strong>Activo:</strong> Se incluye en la generación de conceptos</li>
                            <li><strong>Inactivo:</strong> No se incluye en conceptos</li>
                        </ul>
                    </div>

                    <div class="alert alert-success">
                        <h6><i class="fas fa-lightbulb me-2"></i>Sugerencias:</h6>
                        <ul class="mb-0 mt-2">
                            <li>Use nombres claros y descriptivos</li>
                            <li>Establezca montos realistas</li>
                            <li>Proporcione una descripción útil</li>
                            <li>Active solo los cargos que aplican actualmente</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script para validaciones -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const formRegistrar = document.getElementById('formRegistrarCargo');
            const btnRegistrar = formRegistrar.querySelector('button[type="submit"]');

            // Validación del formulario
            formRegistrar.addEventListener('submit', function(e) {
                const nombre = document.getElementById('nombre_cargo').value.trim();
                const monto = document.getElementById('monto').value;
                const estado = document.getElementById('estado').value;

                if (!nombre) {
                    e.preventDefault();
                    showAlert('Por favor, ingrese el nombre del cargo', 'error');
                    document.getElementById('nombre_cargo').focus();
                    return;
                }

                if (!monto || parseFloat(monto) <= 0) {
                    e.preventDefault();
                    showAlert('Por favor, ingrese un monto válido mayor a 0', 'error');
                    document.getElementById('monto').focus();
                    return;
                }

                if (!estado) {
                    e.preventDefault();
                    showAlert('Por favor, seleccione el estado del cargo', 'error');
                    document.getElementById('estado').focus();
                    return;
                }

                if (nombre.length < 2) {
                    e.preventDefault();
                    showAlert('El nombre del cargo debe tener al menos 2 caracteres', 'error');
                    document.getElementById('nombre_cargo').focus();
                    return;
                }

                // Mostrar loading en el botón
                btnRegistrar.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Registrando...';
                btnRegistrar.disabled = true;
            });

            // Validación en tiempo real
            document.getElementById('nombre_cargo').addEventListener('input', function() {
                if (this.value.length < 2) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });

            document.getElementById('monto').addEventListener('input', function() {
                const montoValue = parseFloat(this.value);
                if (!this.value || montoValue <= 0 || montoValue > 10000 || isNaN(montoValue)) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });

            document.getElementById('estado').addEventListener('change', function() {
                if (!this.value) {
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

        .alert {
            border-radius: 8px;
            border: none;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .input-group-text {
            background-color: #f8f9fa;
            border-color: #ced4da;
        }
    </style>

<?php include("../../includes/footer.php"); ?>
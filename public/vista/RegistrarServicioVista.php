<?php include("../../includes/header.php"); ?>

    <!-- Page Header -->
    <div class="page-header fade-in">
        <div class="page-title">
            <h1>Registrar Nuevo Servicio</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item"><a href="../controlador/ServicioControlador.php?action=listarServicios">Servicios</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Registrar Servicio</li>
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
                    <h5>Información del Servicio</h5>
                </div>
                <div class="content-box-body">
                    <form id="formRegistrarServicio" action="../controlador/ServicioControlador.php" method="POST">
                        <input type="hidden" name="action" value="registrarServicio">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="nombre" class="form-label">
                                        <i class="fas fa-bolt text-warning me-2"></i>Tipo de Servicio *
                                    </label>
                                    <select class="form-control" id="nombre" name="nombre" required>
                                        <option value="">Seleccione un servicio</option>
                                        <option value="agua" <?php echo (isset($_GET['nombre']) && $_GET['nombre'] == 'agua') ? 'selected' : ''; ?>>Agua</option>
                                        <option value="luz" <?php echo (isset($_GET['nombre']) && $_GET['nombre'] == 'luz') ? 'selected' : ''; ?>>Luz</option>
                                        <option value="gas" <?php echo (isset($_GET['nombre']) && $_GET['nombre'] == 'gas') ? 'selected' : ''; ?>>Gas</option>
                                    </select>
                                    <div class="form-text">Seleccione el tipo de servicio a registrar</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="unidad_medida" class="form-label">
                                        <i class="fas fa-ruler text-info me-2"></i>Unidad de Medida *
                                    </label>
                                    <input type="text"
                                           class="form-control"
                                           id="unidad_medida"
                                           name="unidad_medida"
                                           required
                                           maxlength="20"
                                           placeholder="Ej: m³, kWh, etc."
                                           value="<?php echo isset($_GET['unidad_medida']) ? htmlspecialchars($_GET['unidad_medida']) : ''; ?>">
                                    <div class="form-text">Unidad de medida del consumo</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="costo_unitario" class="form-label">
                                        <i class="fas fa-money-bill-wave text-success me-2"></i>Costo Unitario *
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">Bs.</span>
                                        <input type="number"
                                               class="form-control"
                                               id="costo_unitario"
                                               name="costo_unitario"
                                               required
                                               min="0.01"
                                               max="1000"
                                               step="0.01"
                                               placeholder="0.00"
                                               value="<?php echo isset($_GET['costo_unitario']) ? htmlspecialchars($_GET['costo_unitario']) : ''; ?>">
                                        <span class="input-group-text">BOB</span>
                                    </div>
                                    <div class="form-text">Costo por unidad de consumo en Bolivianos</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="estado" class="form-label">
                                        <i class="fas fa-toggle-on text-primary me-2"></i>Estado *
                                    </label>
                                    <select class="form-control" id="estado" name="estado" required>
                                        <option value="">Seleccione un estado</option>
                                        <option value="activo" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'activo') ? 'selected' : ''; ?>>Activo</option>
                                        <option value="inactivo" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                                    </select>
                                    <div class="form-text">Estado actual del servicio</div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="../controlador/ServicioControlador.php?action=listarServicios" class="btn btn-secondary me-md-2">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary" style="background: var(--verde); border: none;">
                                <i class="fas fa-save me-2"></i>Registrar Servicio
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
                            <li>El tipo de servicio debe ser único</li>
                            <li>Verifique que el servicio no exista previamente</li>
                            <li>Ingrese la unidad de medida correcta</li>
                            <li>El costo unitario debe ser mayor a 0</li>
                        </ul>
                    </div>

                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Tipos de Servicio:</h6>
                        <ul class="mb-0 mt-2">
                            <li><strong>Agua:</strong> Consumo de agua potable</li>
                            <li><strong>Luz:</strong> Consumo de energía eléctrica</li>
                            <li><strong>Gas:</strong> Consumo de gas natural</li>
                        </ul>
                    </div>

                    <div class="alert alert-success">
                        <h6><i class="fas fa-lightbulb me-2"></i>Unidades de Medida:</h6>
                        <ul class="mb-0 mt-2">
                            <li><strong>Agua:</strong> m³ (metros cúbicos)</li>
                            <li><strong>Luz:</strong> kWh (kilovatio-hora)</li>
                            <li><strong>Gas:</strong> m³ (metros cúbicos)</li>
                        </ul>
                    </div>

                    <div class="alert alert-primary">
                        <h6><i class="fas fa-calculator me-2"></i>Costo Unitario:</h6>
                        <ul class="mb-0 mt-2">
                            <li>Representa el costo por unidad de consumo</li>
                            <li>Se usa para calcular facturas</li>
                            <li>Debe ser actualizado periódicamente</li>
                            <li>Ejemplo: Bs. 0.85 por m³ de agua</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script para validaciones y auto-cierre de alertas -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const formRegistrar = document.getElementById('formRegistrarServicio');
            const btnRegistrar = formRegistrar.querySelector('button[type="submit"]');
            const selectServicio = document.getElementById('nombre');
            const inputUnidad = document.getElementById('unidad_medida');

            // Auto-completar unidad de medida según el servicio seleccionado
            selectServicio.addEventListener('change', function() {
                const servicio = this.value;
                let unidad = '';

                switch(servicio) {
                    case 'agua':
                        unidad = 'm³';
                        break;
                    case 'luz':
                        unidad = 'kWh';
                        break;
                    case 'gas':
                        unidad = 'm³';
                        break;
                    default:
                        unidad = '';
                }

                inputUnidad.value = unidad;
            });

            // Validación del formulario
            formRegistrar.addEventListener('submit', function(e) {
                const nombre = document.getElementById('nombre').value;
                const unidad = document.getElementById('unidad_medida').value.trim();
                const costo = document.getElementById('costo_unitario').value;
                const estado = document.getElementById('estado').value;

                if (!nombre) {
                    e.preventDefault();
                    showAlert('Por favor, seleccione el tipo de servicio', 'error');
                    document.getElementById('nombre').focus();
                    return;
                }

                if (!unidad) {
                    e.preventDefault();
                    showAlert('Por favor, ingrese la unidad de medida', 'error');
                    document.getElementById('unidad_medida').focus();
                    return;
                }

                if (!costo || parseFloat(costo) <= 0) {
                    e.preventDefault();
                    showAlert('Por favor, ingrese un costo unitario válido (mayor a 0)', 'error');
                    document.getElementById('costo_unitario').focus();
                    return;
                }

                if (!estado) {
                    e.preventDefault();
                    showAlert('Por favor, seleccione el estado del servicio', 'error');
                    document.getElementById('estado').focus();
                    return;
                }

                if (unidad.length < 1) {
                    e.preventDefault();
                    showAlert('La unidad de medida debe tener al menos 1 carácter', 'error');
                    document.getElementById('unidad_medida').focus();
                    return;
                }

                if (parseFloat(costo) > 1000) {
                    e.preventDefault();
                    showAlert('El costo unitario no puede ser mayor a 1,000 Bs.', 'error');
                    document.getElementById('costo_unitario').focus();
                    return;
                }

                // Mostrar loading en el botón
                btnRegistrar.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Registrando...';
                btnRegistrar.disabled = true;
            });

            // Validación en tiempo real
            document.getElementById('nombre').addEventListener('change', function() {
                if (!this.value) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });

            document.getElementById('unidad_medida').addEventListener('input', function() {
                if (!this.value.trim()) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });

            document.getElementById('costo_unitario').addEventListener('input', function() {
                const costoValue = parseFloat(this.value);
                if (!this.value || costoValue <= 0 || costoValue > 1000 || isNaN(costoValue)) {
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

                // Insertar después del page-header
                const pageHeader = document.querySelector('.page-header');
                pageHeader.parentNode.insertBefore(alertDiv, pageHeader.nextSibling);

                // Auto-remover después de 5 segundos
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

        /* Estilos para el select */
        .form-control:focus {
            border-color: var(--azul);
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        /* Estilos para las alertas */
        .alert {
            border-radius: 8px;
            border: none;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
        }

        .alert-danger {
            background: linear-gradient(135deg, #f8d7da 0%, #f1b0b7 100%);
            color: #721c24;
        }

        .alert-info {
            background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
            color: #0c5460;
        }

        .alert-warning {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            color: #856404;
        }

        .alert-primary {
            background: linear-gradient(135deg, #cce7ff 0%, #b3d9ff 100%);
            color: #004085;
        }

        /* Estilos para el input group */
        .input-group-text {
            background-color: #f8f9fa;
            border-color: #ced4da;
        }

        /* Iconos específicos para servicios */
        .fa-bolt { color: #ffc107 !important; }
        .fa-ruler { color: #17a2b8 !important; }
        .fa-money-bill-wave { color: #28a745 !important; }
        .fa-toggle-on { color: #007bff !important; }
    </style>

<?php include("../../includes/footer.php"); ?>
<?php include("../../includes/header.php"); ?>

    <!-- Page Header -->
    <div class="page-header fade-in">
        <div class="page-title">
            <h1>Registrar Nuevo Departamento</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item"><a href="../controlador/DepartamentoControlador.php?action=listarDepartamentos">Departamentos</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Registrar Departamento</li>
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
                    <h5>Información del Departamento</h5>
                </div>
                <div class="content-box-body">
                    <form id="formRegistrarDepartamento" action="../controlador/DepartamentoControlador.php" method="POST">
                        <input type="hidden" name="action" value="registrarDepartamento">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="numero" class="form-label">
                                        <i class="fas fa-hashtag text-verde me-2"></i>Número de Departamento *
                                    </label>
                                    <input type="text"
                                           class="form-control"
                                           id="numero"
                                           name="numero"
                                           required
                                           maxlength="10"
                                           placeholder="Ej: 101, A-201, etc."
                                           value="<?php echo isset($_GET['numero']) ? htmlspecialchars($_GET['numero']) : ''; ?>">
                                    <div class="form-text">Número único identificador del departamento</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="piso" class="form-label">
                                        <i class="fas fa-stairs text-azul me-2"></i>Piso *
                                    </label>
                                    <input type="number"
                                           class="form-control"
                                           id="piso"
                                           name="piso"
                                           required
                                           min="1"
                                           max="50"
                                           placeholder="Número de piso"
                                           value="<?php echo isset($_GET['piso']) ? htmlspecialchars($_GET['piso']) : ''; ?>">
                                    <div class="form-text">Piso donde se encuentra el departamento</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="../controlador/DepartamentoControlador.php?action=listarDepartamentos" class="btn btn-secondary me-md-2">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary" style="background: var(--verde); border: none;">
                                <i class="fas fa-save me-2"></i>Registrar Departamento
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
                            <li>Todos los campos marcados con (*) son obligatorios</li>
                            <li>El número de departamento debe ser único</li>
                            <li>Verifique que el departamento no exista previamente</li>
                            <li>Seleccione el estado apropiado del departamento</li>
                        </ul>
                    </div>

                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Estados:</h6>
                        <ul class="mb-0 mt-2">
                            <li><strong>Disponible:</strong> Departamento libre para asignar</li>
                            <li><strong>Ocupado:</strong> Departamento con residentes</li>
                            <li><strong>Mantenimiento:</strong> En reparación o limpieza</li>
                        </ul>
                    </div>

                    <div class="alert alert-success">
                        <h6><i class="fas fa-lightbulb me-2"></i>Sugerencias:</h6>
                        <ul class="mb-0 mt-2">
                            <li>Use números consecutivos para mejor organización</li>
                            <li>Registre primero los departamentos disponibles</li>
                            <li>Verifique la numeración con el plano del edificio</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script para validaciones y auto-cierre de alertas -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const formRegistrar = document.getElementById('formRegistrarDepartamento');
            const btnRegistrar = formRegistrar.querySelector('button[type="submit"]');

            // Validación del formulario
            formRegistrar.addEventListener('submit', function(e) {
                const numero = document.getElementById('numero').value.trim();
                const piso = document.getElementById('piso').value.trim();
                const estado = document.getElementById('estado').value;

                if (!numero) {
                    e.preventDefault();
                    showAlert('Por favor, ingrese el número del departamento', 'error');
                    document.getElementById('numero').focus();
                    return;
                }

                if (!piso) {
                    e.preventDefault();
                    showAlert('Por favor, ingrese el número de piso', 'error');
                    document.getElementById('piso').focus();
                    return;
                }

                if (!estado) {
                    e.preventDefault();
                    showAlert('Por favor, seleccione el estado del departamento', 'error');
                    document.getElementById('estado').focus();
                    return;
                }

                if (numero.length < 1) {
                    e.preventDefault();
                    showAlert('El número de departamento debe tener al menos 1 carácter', 'error');
                    document.getElementById('numero').focus();
                    return;
                }

                if (piso < 1 || piso > 50) {
                    e.preventDefault();
                    showAlert('El piso debe estar entre 1 y 50', 'error');
                    document.getElementById('piso').focus();
                    return;
                }

                // Mostrar loading en el botón
                btnRegistrar.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Registrando...';
                btnRegistrar.disabled = true;
            });

            // Validación en tiempo real
            document.getElementById('numero').addEventListener('input', function() {
                if (this.value.length < 1) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });

            document.getElementById('piso').addEventListener('input', function() {
                const pisoValue = parseInt(this.value);
                if (pisoValue < 1 || pisoValue > 50 || isNaN(pisoValue)) {
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

        .text-verde { color: var(--verde); }
        .text-azul { color: var(--azul); }

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
    </style>

<?php include("../../includes/footer.php"); ?>
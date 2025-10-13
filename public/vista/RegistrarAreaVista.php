<?php include("../../includes/header.php"); ?>

    <!-- Page Header -->
    <div class="page-header fade-in">
        <div class="page-title">
            <h1>Registrar Nueva Área Común</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item"><a href="../controlador/AreaComunControlador.php?action=listarAreasComunes">Áreas Comunes</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Registrar Área Común</li>
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
                    <h5>Información del Área Común</h5>
                </div>
                <div class="content-box-body">
                    <form id="formRegistrarAreaComun" action="../controlador/AreaComunControlador.php" method="POST">
                        <input type="hidden" name="action" value="registrarArea">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="nombre" class="form-label">
                                        <i class="fas fa-building text-verde me-2"></i>Nombre del Área *
                                    </label>
                                    <input type="text"
                                           class="form-control"
                                           id="nombre"
                                           name="nombre"
                                           required
                                           maxlength="100"
                                           placeholder="Ej: Piscina, Gimnasio, Salón de Eventos, etc."
                                           value="<?php echo isset($_GET['nombre']) ? htmlspecialchars($_GET['nombre']) : ''; ?>">
                                    <div class="form-text">Nombre descriptivo del área común</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="capacidad" class="form-label">
                                        <i class="fas fa-users text-azul me-2"></i>Capacidad
                                    </label>
                                    <input type="number"
                                           class="form-control"
                                           id="capacidad"
                                           name="capacidad"
                                           min="1"
                                           max="1000"
                                           placeholder="Número máximo de personas"
                                           value="<?php echo isset($_GET['capacidad']) ? htmlspecialchars($_GET['capacidad']) : ''; ?>">
                                    <div class="form-text">Capacidad máxima de personas (opcional)</div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="descripcion" class="form-label">
                                <i class="fas fa-align-left text-verde me-2"></i>Descripción
                            </label>
                            <textarea class="form-control"
                                      id="descripcion"
                                      name="descripcion"
                                      rows="3"
                                      maxlength="500"
                                      placeholder="Descripción detallada del área común y sus características"><?php echo isset($_GET['descripcion']) ? htmlspecialchars($_GET['descripcion']) : ''; ?></textarea>
                            <div class="form-text">Descripción opcional del área común (máximo 500 caracteres)</div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="estado" class="form-label">
                                <i class="fas fa-toggle-on text-azul me-2"></i>Estado *
                            </label>
                            <select class="form-control" id="estado" name="estado" required>
                                <option value="">Seleccione un estado</option>
                                <option value="disponible" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'disponible') ? 'selected' : ''; ?>>Disponible</option>
                                <option value="mantenimiento" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'mantenimiento') ? 'selected' : ''; ?>>En Mantenimiento</option>
                                <option value="no disponible" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'no disponible') ? 'selected' : ''; ?>>No Disponible</option>
                            </select>
                            <div class="form-text">Estado actual del área común</div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="../controlador/AreaComunControlador.php?action=listarAreasComunes" class="btn btn-secondary me-md-2">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary" style="background: var(--verde); border: none;">
                                <i class="fas fa-save me-2"></i>Registrar Área Común
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
                            <li>El nombre del área debe ser único y descriptivo</li>
                            <li>Verifique que el área no exista previamente</li>
                            <li>Seleccione el estado apropiado del área</li>
                        </ul>
                    </div>

                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Estados:</h6>
                        <ul class="mb-0 mt-2">
                            <li><strong>Disponible:</strong> Área libre para reservar</li>
                            <li><strong>Mantenimiento:</strong> En reparación o limpieza</li>
                            <li><strong>No Disponible:</strong> Temporalmente fuera de servicio</li>
                        </ul>
                    </div>

                    <div class="alert alert-success">
                        <h6><i class="fas fa-lightbulb me-2"></i>Sugerencias:</h6>
                        <ul class="mb-0 mt-2">
                            <li>Use nombres claros y descriptivos</li>
                            <li>Incluya la capacidad si es relevante</li>
                            <li>Proporcione una descripción útil para los residentes</li>
                            <li>Actualice el estado según la disponibilidad real</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script para validaciones y auto-cierre de alertas -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const formRegistrar = document.getElementById('formRegistrarAreaComun');
            const btnRegistrar = formRegistrar.querySelector('button[type="submit"]');

            // Validación del formulario
            formRegistrar.addEventListener('submit', function(e) {
                const nombre = document.getElementById('nombre').value.trim();
                const estado = document.getElementById('estado').value;
                const capacidad = document.getElementById('capacidad').value;

                if (!nombre) {
                    e.preventDefault();
                    showAlert('Por favor, ingrese el nombre del área común', 'error');
                    document.getElementById('nombre').focus();
                    return;
                }

                if (!estado) {
                    e.preventDefault();
                    showAlert('Por favor, seleccione el estado del área común', 'error');
                    document.getElementById('estado').focus();
                    return;
                }

                if (nombre.length < 2) {
                    e.preventDefault();
                    showAlert('El nombre del área común debe tener al menos 2 caracteres', 'error');
                    document.getElementById('nombre').focus();
                    return;
                }

                if (capacidad && (capacidad < 1 || capacidad > 1000)) {
                    e.preventDefault();
                    showAlert('La capacidad debe estar entre 1 y 1000 personas', 'error');
                    document.getElementById('capacidad').focus();
                    return;
                }

                // Mostrar loading en el botón
                btnRegistrar.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Registrando...';
                btnRegistrar.disabled = true;
            });

            // Validación en tiempo real
            document.getElementById('nombre').addEventListener('input', function() {
                if (this.value.length < 2) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });

            document.getElementById('capacidad').addEventListener('input', function() {
                const capacidadValue = parseInt(this.value);
                if (this.value && (capacidadValue < 1 || capacidadValue > 1000 || isNaN(capacidadValue))) {
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
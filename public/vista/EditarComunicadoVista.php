<?php
// public/vista/EditarComunicadoVista.php
include("../../includes/header.php");
?>

    <!-- Page Header -->
    <div class="page-header fade-in">
        <div class="page-title">
            <h1>Editar Comunicado</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item"><a href="ComunicadoControlador.php">Comunicados</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Editar</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Alertas -->
<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <?php echo htmlspecialchars($_GET['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

    <!-- Formulario -->
    <div class="row fade-in">
        <div class="col-lg-8">
            <div class="content-box">
                <div class="content-box-header">
                    <h5>Información del Comunicado</h5>
                </div>
                <div class="content-box-body">
                    <form id="formComunicado" action="ComunicadoControlador.php?action=actualizar" method="POST">
                        <input type="hidden" name="id_comunicado" value="<?php echo $comunicado['id_comunicado']; ?>">

                        <div class="mb-3">
                            <label for="titulo" class="form-label">
                                <i class="fas fa-heading text-primary me-2"></i>Título *
                            </label>
                            <input type="text"
                                   class="form-control"
                                   id="titulo"
                                   name="titulo"
                                   required
                                   maxlength="255"
                                   value="<?php echo htmlspecialchars($comunicado['titulo']); ?>"
                                   placeholder="Ingrese el título del comunicado">
                        </div>

                        <div class="mb-3">
                            <label for="contenido" class="form-label">
                                <i class="fas fa-align-left text-info me-2"></i>Contenido *
                            </label>
                            <textarea class="form-control"
                                      id="contenido"
                                      name="contenido"
                                      rows="8"
                                      required
                                      placeholder="Escriba el contenido del comunicado..."><?php echo htmlspecialchars($comunicado['contenido']); ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="fecha_expiracion" class="form-label">
                                        <i class="fas fa-calendar-times text-warning me-2"></i>Fecha de Expiración
                                    </label>
                                    <input type="date"
                                           class="form-control"
                                           id="fecha_expiracion"
                                           name="fecha_expiracion"
                                           value="<?php echo $comunicado['fecha_expiracion'] ? date('Y-m-d', strtotime($comunicado['fecha_expiracion'])) : ''; ?>"
                                           min="<?php echo date('Y-m-d'); ?>">
                                    <div class="form-text">Opcional. Si no se establece, el comunicado no expirará.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="prioridad" class="form-label">
                                        <i class="fas fa-exclamation-circle text-danger me-2"></i>Prioridad *
                                    </label>
                                    <select class="form-select" id="prioridad" name="prioridad" required>
                                        <option value="">Seleccione prioridad</option>
                                        <option value="baja" <?php echo ($comunicado['prioridad'] == 'baja') ? 'selected' : ''; ?>>Baja</option>
                                        <option value="media" <?php echo ($comunicado['prioridad'] == 'media') ? 'selected' : ''; ?>>Media</option>
                                        <option value="alta" <?php echo ($comunicado['prioridad'] == 'alta') ? 'selected' : ''; ?>>Alta</option>
                                        <option value="urgente" <?php echo ($comunicado['prioridad'] == 'urgente') ? 'selected' : ''; ?>>Urgente</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tipo_audiencia" class="form-label">
                                        <i class="fas fa-users text-success me-2"></i>Audiencia *
                                    </label>
                                    <select class="form-select" id="tipo_audiencia" name="tipo_audiencia" required>
                                        <option value="">Seleccione audiencia</option>
                                        <option value="Todos" <?php echo ($comunicado['tipo_audiencia'] == 'Todos') ? 'selected' : ''; ?>>Todos</option>
                                        <option value="Residente" <?php echo ($comunicado['tipo_audiencia'] == 'Residente') ? 'selected' : ''; ?>>Residente</option>
                                        <option value="Personal" <?php echo ($comunicado['tipo_audiencia'] == 'Personal') ? 'selected' : ''; ?>>Personal</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="estado" class="form-label">
                                        <i class="fas fa-toggle-on text-info me-2"></i>Estado *
                                    </label>
                                    <select class="form-select" id="estado" name="estado" required>
                                        <option value="">Seleccione estado</option>
                                        <option value="borrador" <?php echo ($comunicado['estado'] == 'borrador') ? 'selected' : ''; ?>>Borrador</option>
                                        <option value="publicado" <?php echo ($comunicado['estado'] == 'publicado') ? 'selected' : ''; ?>>Publicado</option>
                                        <option value="archivado" <?php echo ($comunicado['estado'] == 'archivado') ? 'selected' : ''; ?>>Archivado</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="ComunicadoControlador.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Volver al Listado
                            </a>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Actualizar Comunicado
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Panel de Ayuda -->
        <div class="col-lg-4">
            <div class="content-box">
                <div class="content-box-header">
                    <h5><i class="fas fa-info-circle me-2"></i>Información Importante</h5>
                </div>
                <div class="content-box-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-lightbulb me-2"></i>Recomendaciones:</h6>
                        <ul class="mb-0 ps-3">
                            <li>Use títulos claros y descriptivos</li>
                            <li>Sea específico en el contenido</li>
                            <li>Establezca fechas de expiración cuando sea necesario</li>
                            <li>Seleccione la audiencia correcta</li>
                        </ul>
                    </div>

                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Niveles de Prioridad:</h6>
                        <ul class="mb-0 ps-3">
                            <li><strong>Baja:</strong> Información general</li>
                            <li><strong>Media:</strong> Avisos importantes</li>
                            <li><strong>Alta:</strong> Situaciones críticas</li>
                            <li><strong>Urgente:</strong> Emergencias</li>
                        </ul>
                    </div>

                    <div class="alert alert-success">
                        <h6><i class="fas fa-users me-2"></i>Tipos de Audiencia:</h6>
                        <ul class="mb-0 ps-3">
                            <li><strong>Todos:</strong> Todos los usuarios</li>
                            <li><strong>Residente:</strong> Solo residentes</li>
                            <li><strong>Personal:</strong> Solo personal administrativo</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script para validación del formulario -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('formComunicado');
            const titulo = document.getElementById('titulo');
            const contenido = document.getElementById('contenido');

            // Validación en tiempo real
            titulo.addEventListener('input', function() {
                if (this.value.length > 0) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else {
                    this.classList.remove('is-valid');
                }
            });

            contenido.addEventListener('input', function() {
                if (this.value.length > 0) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else {
                    this.classList.remove('is-valid');
                }
            });

            // Validación al enviar
            form.addEventListener('submit', function(e) {
                let valid = true;

                if (titulo.value.trim() === '') {
                    titulo.classList.add('is-invalid');
                    valid = false;
                }

                if (contenido.value.trim() === '') {
                    contenido.classList.add('is-invalid');
                    valid = false;
                }

                if (!valid) {
                    e.preventDefault();
                    // Mostrar alerta de error
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-danger alert-dismissible fade show mt-3';
                    alertDiv.innerHTML = `
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Por favor complete todos los campos requeridos.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                `;
                    form.insertBefore(alertDiv, form.firstChild);
                }
            });

            // Auto-ocultar alertas
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });
    </script>

    <style>
        .form-label {
            font-weight: 600;
            color: var(--azul-oscuro);
        }

        .content-box .alert ul {
            margin-bottom: 0;
        }

        .is-valid {
            border-color: #198754 !important;
        }

        .is-invalid {
            border-color: #dc3545 !important;
        }

        .form-text {
            font-size: 0.875rem;
            color: #6c757d;
        }
    </style>

<?php include("../../includes/footer.php"); ?>





<?php include("../../includes/header.php"); ?>

    <!-- Page Header -->
    <div class="page-header fade-in">
        <div class="page-title">
            <h1>Generar Conceptos de Mantenimiento</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item"><a href="CargosFijosControlador.php?action=listarCargosVista">Cargos Fijos</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Generar Conceptos</li>
                </ol>
            </nav>
        </div>
        <div class="page-actions">
            <a href="CargosFijosControlador.php?action=listarCargosVista" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver a Cargos
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

    <!-- Resumen de Generación -->
    <div class="row fade-in mb-4">
        <div class="col-md-4">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-tags fa-2x text-primary mb-2"></i>
                    <h4 class="mb-1"><?php echo count($cargos_activos); ?></h4>
                    <p class="text-muted mb-0">Cargos Activos</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-building fa-2x text-success mb-2"></i>
                    <h4 class="mb-1"><?php echo $estadisticas['total_departamentos'] ?? 0; ?></h4>
                    <p class="text-muted mb-0">Departamentos Ocupados</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-calculator fa-2x text-info mb-2"></i>
                    <h4 class="mb-1">Bs. <?php echo number_format($estadisticas['monto_mensual_total'] ?? 0, 2); ?></h4>
                    <p class="text-muted mb-0">Total a Generar</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Información de Última Generación -->
<?php if ($ultima_generacion && $ultima_generacion['ultima_generacion']): ?>
    <div class="row fade-in mb-4">
        <div class="col-12">
            <div class="content-box">
                <div class="content-box-header">
                    <h5><i class="fas fa-history me-2"></i>Última Generación</h5>
                </div>
                <div class="content-box-body">
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        La última generación de conceptos fue el:
                        <strong><?php echo date('d/m/Y H:i:s', strtotime($ultima_generacion['ultima_generacion'])); ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

    <!-- Formulario de Generación -->
    <div class="row fade-in">
        <div class="col-lg-8">
            <div class="content-box">
                <div class="content-box-header">
                    <h5>Generar Nuevos Conceptos</h5>
                </div>
                <div class="content-box-body">
                    <form id="formGenerarConceptos" action="CargosFijosControlador.php" method="POST">
                        <input type="hidden" name="action" value="generarConceptosMantenimiento">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="month" class="form-label">
                                        <i class="fas fa-calendar-alt text-primary me-2"></i>Mes *
                                    </label>
                                    <select class="form-control" id="month" name="month" required>
                                        <option value="">Seleccione el mes</option>
                                        <option value="1" <?php echo date('n') == 1 ? 'selected' : ''; ?>>Enero</option>
                                        <option value="2" <?php echo date('n') == 2 ? 'selected' : ''; ?>>Febrero</option>
                                        <option value="3" <?php echo date('n') == 3 ? 'selected' : ''; ?>>Marzo</option>
                                        <option value="4" <?php echo date('n') == 4 ? 'selected' : ''; ?>>Abril</option>
                                        <option value="5" <?php echo date('n') == 5 ? 'selected' : ''; ?>>Mayo</option>
                                        <option value="6" <?php echo date('n') == 6 ? 'selected' : ''; ?>>Junio</option>
                                        <option value="7" <?php echo date('n') == 7 ? 'selected' : ''; ?>>Julio</option>
                                        <option value="8" <?php echo date('n') == 8 ? 'selected' : ''; ?>>Agosto</option>
                                        <option value="9" <?php echo date('n') == 9 ? 'selected' : ''; ?>>Septiembre</option>
                                        <option value="10" <?php echo date('n') == 10 ? 'selected' : ''; ?>>Octubre</option>
                                        <option value="11" <?php echo date('n') == 11 ? 'selected' : ''; ?>>Noviembre</option>
                                        <option value="12" <?php echo date('n') == 12 ? 'selected' : ''; ?>>Diciembre</option>
                                    </select>
                                    <div class="form-text">Mes para el cual generar los conceptos</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="year" class="form-label">
                                        <i class="fas fa-calendar text-success me-2"></i>Año *
                                    </label>
                                    <select class="form-control" id="year" name="year" required>
                                        <option value="">Seleccione el año</option>
                                        <?php
                                        $currentYear = date('Y');
                                        for ($i = $currentYear - 1; $i <= $currentYear + 1; $i++): ?>
                                            <option value="<?php echo $i; ?>" <?php echo $i == $currentYear ? 'selected' : ''; ?>>
                                                <?php echo $i; ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                    <div class="form-text">Año para el cual generar los conceptos</div>
                                </div>
                            </div>
                        </div>

                        <!-- Resumen de lo que se generará -->
                        <div class="alert alert-warning" id="resumenGeneracion">
                            <h6><i class="fas fa-calculator me-2"></i>Resumen de Generación</h6>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <small><strong>Cargos Activos:</strong> <span id="resumenCargos"><?php echo count($cargos_activos); ?></span></small><br>
                                    <small><strong>Departamentos:</strong> <span id="resumenDepartamentos"><?php echo $estadisticas['total_departamentos'] ?? 0; ?></span></small>
                                </div>
                                <div class="col-md-6">
                                    <small><strong>Total Conceptos:</strong> <span id="resumenTotalConceptos"><?php echo count($cargos_activos) * ($estadisticas['total_departamentos'] ?? 0); ?></span></small><br>
                                    <small><strong>Monto Total:</strong> Bs. <span id="resumenMontoTotal"><?php echo number_format($estadisticas['monto_mensual_total'] ?? 0, 2); ?></span></small>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-danger d-none" id="alertaYaGenerado">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Advertencia:</strong> Si ya se  generaron conceptos para el mes seleccionado.
                            Si continúa, se crearán conceptos duplicados. Verificar los conceptos
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="CargosFijosControlador.php?action=listarCargosVista" class="btn btn-secondary me-md-2">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary" id="btnGenerar">
                                <i class="fas fa-cog me-2"></i>Generar Conceptos
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Precauciones -->
        <div class="col-lg-4">
            <div class="content-box position-sticky" style="top: 100px;">
                <div class="content-box-header">
                    <h5>Precauciones</h5>
                </div>
                <div class="content-box-body">
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Antes de generar:</h6>
                        <ul class="mb-0 mt-2">
                            <li>Verifique que no existan conceptos ya generados para el mes seleccionado</li>
                            <li>Revise que todos los cargos estén correctamente configurados</li>
                            <li>Confirme los departamentos ocupados</li>
                            <li>Este proceso no se puede deshacer</li>
                            <li>Los conceptos generados estarán en estado "pendiente"</li>
                            <li>Se crearán conceptos para todos los departamentos ocupados</li>
                            <li>Cada departamento recibirá todos los cargos activos</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Cargos Activos -->
    <div class="row fade-in mt-4">
        <div class="col-12">
            <div class="content-box">
                <div class="content-box-header">
                    <h5><i class="fas fa-list me-2"></i>Cargos Fijos Activos</h5>
                </div>
                <div class="content-box-body">
                    <?php if (empty($cargos_activos)): ?>
                        <div class="text-center py-3">
                            <i class="fas fa-exclamation-triangle fa-2x text-warning mb-2"></i>
                            <p class="text-muted">No hay cargos fijos activos. No se pueden generar conceptos.</p>
                            <a href="CargosFijosControlador.php?action=formularioCrearCargo" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-2"></i>Crear Cargo
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Monto</th>
                                    <th>Descripción</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($cargos_activos as $cargo): ?>
                                    <tr>
                                        <td>
                                            <i class="fas fa-tag text-primary me-2"></i>
                                            <?php echo htmlspecialchars($cargo['nombre_cargo']); ?>
                                        </td>
                                        <td>
                                                <span class="badge bg-success">
                                                    Bs. <?php echo number_format($cargo['monto'], 2); ?>
                                                </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?php echo htmlspecialchars($cargo['descripcion'] ?: 'Sin descripción'); ?>
                                            </small>
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

    <!-- Script para validaciones y cálculos -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const formGenerar = document.getElementById('formGenerarConceptos');
            const btnGenerar = document.getElementById('btnGenerar');
            const monthSelect = document.getElementById('month');
            const yearSelect = document.getElementById('year');
            const alertaYaGenerado = document.getElementById('alertaYaGenerado');

            // Verificar si ya se generaron conceptos para el mes seleccionado
            async function verificarConceptosGenerados() {
                const month = monthSelect.value;
                const year = yearSelect.value;

                if (!month || !year) {
                    alertaYaGenerado.classList.add('d-none');
                    return;
                }

                try {
                    // Aquí puedes implementar la verificación real contra tu base de datos
                    // Por ahora es un ejemplo básico
                    if (month === '<?php echo date("n"); ?>' && year === '<?php echo date("Y"); ?>') {
                        alertaYaGenerado.classList.remove('d-none');
                    } else {
                        alertaYaGenerado.classList.add('d-none');
                    }
                } catch (error) {
                    console.error('Error al verificar conceptos:', error);
                }
            }

            // Event listeners
            monthSelect.addEventListener('change', verificarConceptosGenerados);
            yearSelect.addEventListener('change', verificarConceptosGenerados);

            // Validación del formulario
            formGenerar.addEventListener('submit', function(e) {
                const month = monthSelect.value;
                const year = yearSelect.value;

                if (!month || !year) {
                    e.preventDefault();
                    showAlert('Por favor, seleccione mes y año', 'error');
                    return;
                }

                if (<?php echo empty($cargos_activos) ? 'true' : 'false'; ?>) {
                    e.preventDefault();
                    showAlert('No hay cargos fijos activos para generar conceptos', 'error');
                    return;
                }

                if (<?php echo ($estadisticas['total_departamentos'] ?? 0) == 0 ? 'true' : 'false'; ?>) {
                    e.preventDefault();
                    showAlert('No hay departamentos ocupados para generar conceptos', 'error');
                    return;
                }

                // Confirmación adicional
                if (!confirm('¿Está seguro que desea generar los conceptos de mantenimiento? Esta acción no se puede deshacer.')) {
                    e.preventDefault();
                    return;
                }

                // Mostrar loading
                btnGenerar.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generando...';
                btnGenerar.disabled = true;
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
            }

            // Inicializar
            verificarConceptosGenerados();
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

        .content-box.text-center {
            transition: transform 0.2s;
        }

        .content-box.text-center:hover {
            transform: translateY(-5px);
        }

        .alert {
            border-radius: 8px;
            border: none;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .table th {
            border-top: none;
            font-weight: 600;
            background-color: #f8f9fa;
        }
    </style>

<?php include("../../includes/footer.php"); ?>
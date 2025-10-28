<?php include("../../includes/header.php"); ?>

    <!-- Page Header -->
    <div class="page-header fade-in">
        <div class="page-title">
            <h1>Generar Consumos de Servicios</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item"><a href="../controlador/ServicioControlador.php?action=listarServicios">Servicios</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Generar Consumos</li>
                </ol>
            </nav>
        </div>
        <div class="page-actions">
            <a href="ServicioControlador.php?action=verReporteConsumos" class="btn btn-info">
                <i class="fas fa-chart-bar me-2"></i>Ver Reportes
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
        <!-- Formulario para Consumos Masivos -->
        <div class="col-lg-6">
            <div class="content-box">
                <div class="content-box-header">
                    <h5><i class="fas fa-bolt me-2 text-warning"></i>Generar Consumos Masivos</h5>
                </div>
                <div class="content-box-body">
                    <form id="formConsumosMasivos" action="../controlador/ServicioControlador.php" method="POST">
                        <input type="hidden" name="action" value="generarConsumosMasivos">

                        <div class="mb-3">
                            <label for="month" class="form-label">
                                <i class="fas fa-calendar-alt text-primary me-2"></i>Mes *
                            </label>
                            <select class="form-control" id="month" name="month" required>
                                <option value="">Seleccionar Mes</option>
                                <?php
                                $meses = [
                                    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                                    5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                                    9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                                ];
                                $mesActual = date('n');
                                foreach ($meses as $num => $nombre) {
                                    $selected = ($num == $mesActual) ? 'selected' : '';
                                    echo "<option value='$num' $selected>$nombre</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="year" class="form-label">
                                <i class="fas fa-calendar text-primary me-2"></i>Año *
                            </label>
                            <input type="number"
                                   class="form-control"
                                   id="year"
                                   name="year"
                                   min="2020"
                                   max="2030"
                                   value="<?php echo date('Y'); ?>"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="departamento" class="form-label">
                                <i class="fas fa-building text-info me-2"></i>Departamento
                            </label>
                            <select class="form-control" id="departamento" name="departamento">
                                <option value="">Todos los Departamentos</option>
                                <?php
                                foreach ($departamentos as $depto) {
                                    echo "<option value='{$depto['id_departamento']}'>
                                        Depto {$depto['numero']} (Piso {$depto['piso']})
                                      </option>";
                                }
                                ?>
                            </select>
                            <div class="form-text">Dejar en blanco para generar consumos para todos los departamentos</div>
                        </div>

                        <div class="alert alert-warning">
                            <h6><i class="fas fa-exclamation-triangle me-2"></i>Advertencia</h6>
                            <p class="mb-0">Esta acción generará consumos automáticos para todo el mes seleccionado. Los datos existentes serán sobrescritos.</p>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-bolt me-2"></i>Generar Consumos Masivos
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Formulario para Consumo Individual -->
        <div class="col-lg-6">
            <div class="content-box">
                <div class="content-box-header">
                    <h5><i class="fas fa-plus-circle me-2 text-success"></i>Registrar Consumo Individual</h5>
                </div>
                <div class="content-box-body">
                    <form id="formConsumoIndividual" action="../controlador/ServicioControlador.php" method="POST">
                        <input type="hidden" name="action" value="generarConsumoIndividual">

                        <div class="mb-3">
                            <label for="departamento_individual" class="form-label">
                                <i class="fas fa-building text-info me-2"></i>Departamento *
                            </label>
                            <select class="form-control" id="departamento_individual" name="departamento_individual" required>
                                <option value="">Seleccionar Departamento</option>
                                <?php
                                foreach ($departamentos as $depto) {
                                    echo "<option value='{$depto['id_departamento']}'>
                                        Depto {$depto['numero']} (Piso {$depto['piso']})
                                      </option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="medidor" class="form-label">
                                <i class="fas fa-tachometer-alt text-primary me-2"></i>Medidor *
                            </label>
                            <select class="form-control" id="medidor" name="id_medidor" required disabled>
                                <option value="">Primero seleccione un departamento</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="consumo" class="form-label">
                                <i class="fas fa-chart-line text-success me-2"></i>Consumo *
                            </label>
                            <div class="input-group">
                                <input type="number"
                                       class="form-control"
                                       id="consumo"
                                       name="consumo"
                                       step="0.01"
                                       min="0.01"
                                       max="100"
                                       placeholder="0.00"
                                       required>
                                <span class="input-group-text" id="unidad_medida">-</span>
                            </div>
                            <div class="form-text" id="rango_consumo"></div>
                        </div>

                        <div class="mb-3">
                            <label for="fecha_hora" class="form-label">
                                <i class="fas fa-clock text-info me-2"></i>Fecha y Hora
                            </label>
                            <input type="datetime-local"
                                   class="form-control"
                                   id="fecha_hora"
                                   name="fecha_hora"
                                   value="<?php echo date('Y-m-d\TH:i'); ?>">
                            <div class="form-text">Dejar en blanco para usar la fecha y hora actual</div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>Registrar Consumo Individual
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Información de Rangos -->
    <div class="row fade-in mt-4">
        <div class="col-12">
            <div class="content-box">
                <div class="content-box-header">
                    <h5><i class="fas fa-info-circle me-2 text-info"></i>Información de Rangos de Consumo</h5>
                </div>
                <div class="content-box-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="card border-primary mb-3">
                                <div class="card-body">
                                    <i class="fas fa-tint fa-2x text-info mb-2"></i>
                                    <h5 class="card-title">Agua</h5>
                                    <p class="card-text">
                                        <strong>Rango diario:</strong> 0.1 - 1.5 m³<br>
                                        <strong>Unidad:</strong> m³ (metros cúbicos)
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-warning mb-3">
                                <div class="card-body">
                                    <i class="fas fa-bolt fa-2x text-warning mb-2"></i>
                                    <h5 class="card-title">Luz</h5>
                                    <p class="card-text">
                                        <strong>Rango diario:</strong> 0.1 - 2.0 kWh<br>
                                        <strong>Unidad:</strong> kWh (kilovatio-hora)
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-danger mb-3">
                                <div class="card-body">
                                    <i class="fas fa-fire fa-2x text-danger mb-2"></i>
                                    <h5 class="card-title">Gas</h5>
                                    <p class="card-text">
                                        <strong>Rango diario:</strong> 0.01 - 0.05 m³<br>
                                        <strong>Unidad:</strong> m³ (metros cúbicos)
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script para funcionalidades -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const formMasivos = document.getElementById('formConsumosMasivos');
            const formIndividual = document.getElementById('formConsumoIndividual');
            const selectDeptoIndividual = document.getElementById('departamento_individual');
            const selectMedidor = document.getElementById('medidor');
            const inputConsumo = document.getElementById('consumo');
            const spanUnidad = document.getElementById('unidad_medida');
            const divRango = document.getElementById('rango_consumo');

            // Cargar medidores cuando se selecciona un departamento
            selectDeptoIndividual.addEventListener('change', function() {
                const idDepartamento = this.value;

                if (!idDepartamento) {
                    selectMedidor.innerHTML = '<option value="">Primero seleccione un departamento</option>';
                    selectMedidor.disabled = true;
                    resetConsumoFields();
                    return;
                }

                // Simular carga de medidores (en un caso real, harías una petición AJAX)
                selectMedidor.disabled = true;
                selectMedidor.innerHTML = '<option value="">Cargando medidores...</option>';

                // Simular delay de carga
                setTimeout(() => {
                    // En una implementación real, esto vendría de una petición AJAX al servidor
                    const medidores = [
                        {id: 1, servicio: 'agua', unidad: 'm³', rango: '0.1 - 1.5'},
                        {id: 2, servicio: 'luz', unidad: 'kWh', rango: '0.1 - 2.0'},
                        {id: 3, servicio: 'gas', unidad: 'm³', rango: '0.01 - 0.05'}
                    ];

                    let options = '<option value="">Seleccione un medidor</option>';
                    medidores.forEach(medidor => {
                        let servicioNombre = '';
                        let iconClass = '';

                        switch(medidor.servicio) {
                            case 'agua':
                                servicioNombre = 'Agua';
                                iconClass = 'fas fa-tint text-info';
                                break;
                            case 'luz':
                                servicioNombre = 'Luz';
                                iconClass = 'fas fa-bolt text-warning';
                                break;
                            case 'gas':
                                servicioNombre = 'Gas';
                                iconClass = 'fas fa-fire text-danger';
                                break;
                        }

                        options += `<option value="${medidor.id}" data-servicio="${medidor.servicio}" data-unidad="${medidor.unidad}" data-rango="${medidor.rango}">
                              <i class="${iconClass} me-2"></i>${servicioNombre} (${medidor.unidad})
                            </option>`;
                    });

                    selectMedidor.innerHTML = options;
                    selectMedidor.disabled = false;
                }, 500);
            });

            // Actualizar información de unidad y rango cuando se selecciona un medidor
            selectMedidor.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];

                if (!selectedOption.value) {
                    resetConsumoFields();
                    return;
                }

                const unidad = selectedOption.getAttribute('data-unidad');
                const rango = selectedOption.getAttribute('data-rango');
                const servicio = selectedOption.getAttribute('data-servicio');

                spanUnidad.textContent = unidad;
                divRango.innerHTML = `<i class="fas fa-info-circle me-1"></i>Rango recomendado: ${rango} ${unidad} por día`;

                // Establecer placeholder según el servicio
                switch(servicio) {
                    case 'agua':
                        inputConsumo.placeholder = '0.50';
                        break;
                    case 'luz':
                        inputConsumo.placeholder = '1.20';
                        break;
                    case 'gas':
                        inputConsumo.placeholder = '0.03';
                        break;
                }
            });

            function resetConsumoFields() {
                spanUnidad.textContent = '-';
                divRango.innerHTML = '';
                inputConsumo.placeholder = '0.00';
            }

            // Validación del formulario de consumos masivos
            formMasivos.addEventListener('submit', function(e) {
                const month = document.getElementById('month').value;
                const year = document.getElementById('year').value;

                if (!month || !year) {
                    e.preventDefault();
                    showAlert('Por favor, complete todos los campos obligatorios', 'error');
                    return;
                }

                if (confirm('¿Está seguro que desea generar consumos masivos para el mes seleccionado? Esta acción no se puede deshacer.')) {
                    // Mostrar loading
                    const btn = this.querySelector('button[type="submit"]');
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generando...';
                    btn.disabled = true;
                } else {
                    e.preventDefault();
                }
            });

            // Validación del formulario de consumo individual
            formIndividual.addEventListener('submit', function(e) {
                const depto = document.getElementById('departamento_individual').value;
                const medidor = document.getElementById('medidor').value;
                const consumo = document.getElementById('consumo').value;

                if (!depto || !medidor || !consumo) {
                    e.preventDefault();
                    showAlert('Por favor, complete todos los campos obligatorios', 'error');
                    return;
                }

                const consumoValue = parseFloat(consumo);
                if (consumoValue <= 0 || consumoValue > 100) {
                    e.preventDefault();
                    showAlert('El consumo debe ser mayor a 0 y menor a 100', 'error');
                    return;
                }

                // Mostrar loading
                const btn = this.querySelector('button[type="submit"]');
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Registrando...';
                btn.disabled = true;
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

        .form-text {
            font-size: 0.875rem;
            color: #6c757d;
        }

        .input-group-text {
            background-color: #f8f9fa;
            border-color: #ced4da;
            min-width: 60px;
        }

        .card {
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);|
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* Estilos para los iconos de servicios */
        .fa-tint { color: #17a2b8 !important; }
        .fa-bolt { color: #ffc107 !important; }
        .fa-fire { color: #dc3545 !important; }
        .fa-tachometer-alt { color: #6f42c1 !important; }

        /* Estilos para las cards de información */
        .card-border-primary { border-left: 4px solid #007bff !important; }
        .card-border-warning { border-left: 4px solid #ffc107 !important; }
        .card-border-danger { border-left: 4px solid #dc3545 !important; }
    </style>

<?php include("../../includes/footer.php"); ?>
<?php include("../../includes/header.php"); ?>

    <!-- Page Header -->
    <div class="page-header fade-in">
        <div class="page-title">
            <h1>Generar Planillas de Pago</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item"><a href="PlanillaControlador.php?action=listarPlanillasCompleto">Planillas</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Generar Planillas</li>
                </ol>
            </nav>
        </div>
        <div class="page-actions">
            <a href="PlanillaControlador.php?action=listarPlanillasCompleto" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver a Planillas
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

    <!-- Pestañas de Generación -->
    <div class="row fade-in">
        <div class="col-12">
            <div class="content-box">
                <div class="content-box-header">
                    <h5><i class="fas fa-cogs me-2"></i>Opciones de Generación de Planillas</h5>
                </div>
                <div class="content-box-body">
                    <ul class="nav nav-tabs nav-fill" id="planillaTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="completa-tab" data-bs-toggle="tab" data-bs-target="#completa" type="button" role="tab">
                                <i class="fas fa-users me-2"></i>Planilla Completa
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="personalizada-tab" data-bs-toggle="tab" data-bs-target="#personalizada" type="button" role="tab">
                                <i class="fas fa-user me-2"></i>Planilla Personalizada
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="multiple-tab" data-bs-toggle="tab" data-bs-target="#multiple" type="button" role="tab">
                                <i class="fas fa-list me-2"></i>Planillas Múltiples
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content p-4" id="planillaTabsContent">

                        <!-- Pestaña Planilla Completa -->
                        <div class="tab-pane fade show active" id="completa" role="tabpanel">
                            <div class="row">
                                <div class="col-md-8">
                                    <form id="formPlanillaCompleta" method="POST" action="PlanillaControlador.php">
                                        <input type="hidden" name="action" value="generarPlanillaCompleta">

                                        <h5 class="mb-4"><i class="fas fa-users text-primary me-2"></i>Generar Planilla para Todos los Empleados</h5>

                                        <div class="row mb-4">
                                            <div class="col-md-6">
                                                <label for="completaMes" class="form-label">Mes</label>
                                                <select class="form-select" id="completaMes" name="mes" required>
                                                    <option value="">Seleccionar mes...</option>
                                                    <option value="1">Enero</option>
                                                    <option value="2">Febrero</option>
                                                    <option value="3">Marzo</option>
                                                    <option value="4">Abril</option>
                                                    <option value="5">Mayo</option>
                                                    <option value="6">Junio</option>
                                                    <option value="7">Julio</option>
                                                    <option value="8">Agosto</option>
                                                    <option value="9">Septiembre</option>
                                                    <option value="10">Octubre</option>
                                                    <option value="11">Noviembre</option>
                                                    <option value="12">Diciembre</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="completaAnio" class="form-label">Año</label>
                                                <select class="form-select" id="completaAnio" name="anio" required>
                                                    <option value="">Seleccionar año...</option>
                                                    <option value="2023">2023</option>
                                                    <option value="2024">2024</option>
                                                    <option value="2025">2025</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- AÑADIR MÉTODO DE PAGO -->
                                        <div class="row mb-4">
                                            <div class="col-md-6">
                                                <label for="completaMetodoPago" class="form-label">Método de Pago</label>
                                                <select class="form-select" id="completaMetodoPago" name="metodo_pago" required>
                                                    <option value="">Seleccionar método...</option>
                                                    <option value="transferencia">Transferencia Bancaria</option>
                                                    <option value="qr">Pago QR</option>
                                                    <option value="efectivo">Efectivo</option>
                                                    <option value="cheque">Cheque</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="alert alert-warning">
                                            <h6><i class="fas fa-exclamation-triangle me-2"></i>Información Importante</h6>
                                            <ul class="mb-0">
                                                <li>Se generarán planillas para <strong id="totalEmpleados">0</strong> empleados activos</li>
                                                <li>Los cálculos incluyen descuento de Gestora (12.71%)</li>
                                                <li>Se asumen 30 días trabajados por defecto</li>
                                                <li>Estado: <span id="estadoPlanilla">procesada</span> (según método de pago)</li>
                                            </ul>
                                        </div>

                                        <div class="form-check mb-4">
                                            <input class="form-check-input" type="checkbox" id="forzarCompleta" name="forzar">
                                            <label class="form-check-label" for="forzarCompleta">
                                                <strong>Forzar regeneración</strong> (Elimina planillas existentes para este periodo)
                                            </label>
                                        </div>

                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <button type="button" class="btn btn-secondary" onclick="previsualizarCompleta()">
                                                <i class="fas fa-eye me-2"></i>Previsualizar
                                            </button>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-play me-2"></i>Generar Planilla Completa
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-4">
                                    <div class="content-box">
                                        <div class="content-box-header">
                                            <h6><i class="fas fa-info-circle me-2"></i>Resumen</h6>
                                        </div>
                                        <div class="content-box-body">
                                            <div id="resumenCompleta">
                                                <p class="text-muted text-center">Seleccione un periodo para ver el resumen</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pestaña Planilla Personalizada -->
                        <div class="tab-pane fade" id="personalizada" role="tabpanel">
                            <div class="row">
                                <div class="col-md-8">
                                    <form id="formPlanillaPersonalizada" method="POST" action="PlanillaControlador.php">
                                        <input type="hidden" name="action" value="generarPlanillaPersonalizada">

                                        <h5 class="mb-4"><i class="fas fa-user text-warning me-2"></i>Generar Planilla para Empleado Específico</h5>

                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="personalizadaEmpleado" class="form-label">Seleccionar Empleado</label>
                                                <select class="form-select" id="personalizadaEmpleado" name="id_persona" required>
                                                    <option value="">Seleccionar empleado...</option>
                                                    <!-- Se carga dinámicamente -->
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <div id="infoEmpleado" class="empleado-info" style="display: none;">
                                                    <label class="form-label">Información del Empleado</label>
                                                    <div class="card bg-light">
                                                        <div class="card-body py-2">
                                                            <p class="mb-1" id="empleadoNombre"><strong>Nombre:</strong> -</p>
                                                            <p class="mb-1" id="empleadoRol"><strong>Rol:</strong> -</p>
                                                            <p class="mb-0" id="empleadoSalario"><strong>Salario Base:</strong> -</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="personalizadaMes" class="form-label">Mes</label>
                                                <select class="form-select" id="personalizadaMes" name="mes" required>
                                                    <option value="">Seleccionar mes...</option>
                                                    <option value="1">Enero</option>
                                                    <option value="2">Febrero</option>
                                                    <option value="3">Marzo</option>
                                                    <option value="4">Abril</option>
                                                    <option value="5">Mayo</option>
                                                    <option value="6">Junio</option>
                                                    <option value="7">Julio</option>
                                                    <option value="8">Agosto</option>
                                                    <option value="9">Septiembre</option>
                                                    <option value="10">Octubre</option>
                                                    <option value="11">Noviembre</option>
                                                    <option value="12">Diciembre</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="personalizadaAnio" class="form-label">Año</label>
                                                <select class="form-select" id="personalizadaAnio" name="anio" required>
                                                    <option value="">Seleccionar año...</option>
                                                    <option value="2023">2023</option>
                                                    <option value="2024">2024</option>
                                                    <option value="2025">2025</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- AÑADIR MÉTODO DE PAGO -->
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="personalizadaMetodoPago" class="form-label">Método de Pago</label>
                                                <select class="form-select" id="personalizadaMetodoPago" name="metodo_pago" required>
                                                    <option value="">Seleccionar método...</option>
                                                    <option value="transferencia">Transferencia Bancaria</option>
                                                    <option value="qr">Pago QR</option>
                                                    <option value="efectivo">Efectivo</option>
                                                    <option value="cheque">Cheque</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="diasDescuento" class="form-label">Días de Descuento</label>
                                                <input type="number" class="form-control" id="diasDescuento" name="dias_descuento"
                                                       min="0" max="30" step="0.5" value="0" required>
                                                <div class="form-text">
                                                    Soporta medios días (0.5, 1.5, etc.)
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mb-4">
                                            <div class="col-md-6">
                                                <label class="form-label">Días Trabajados</label>
                                                <div class="card bg-light">
                                                    <div class="card-body text-center">
                                                        <h4 id="diasTrabajados" class="text-success mb-0">30</h4>
                                                        <small class="text-muted">días trabajados</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Estado Planilla</label>
                                                <div class="card bg-light">
                                                    <div class="card-body text-center">
                                                        <h6 id="estadoPersonalizada" class="text-info mb-0">procesada</h6>
                                                        <small class="text-muted">según método de pago</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-check mb-4">
                                            <input class="form-check-input" type="checkbox" id="forzarPersonalizada" name="forzar">
                                            <label class="form-check-label" for="forzarPersonalizada">
                                                <strong>Forzar regeneración</strong> (Reemplaza planilla existente para este empleado)
                                            </label>
                                        </div>

                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <button type="button" class="btn btn-secondary" onclick="previsualizarPersonalizada()">
                                                <i class="fas fa-eye me-2"></i>Previsualizar
                                            </button>
                                            <button type="submit" class="btn btn-warning">
                                                <i class="fas fa-user-check me-2"></i>Generar Planilla Personalizada
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-4">
                                    <div class="content-box">
                                        <div class="content-box-header">
                                            <h6><i class="fas fa-calculator me-2"></i>Cálculo Estimado</h6>
                                        </div>
                                        <div class="content-box-body">
                                            <div id="calculoPersonalizada">
                                                <p class="text-muted text-center">Seleccione un empleado y periodo</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pestaña Planillas Múltiples -->
                        <div class="tab-pane fade" id="multiple" role="tabpanel">
                            <div class="row">
                                <div class="col-md-8">
                                    <form id="formPlanillaMultiple" method="POST" action="PlanillaControlador.php">
                                        <input type="hidden" name="action" value="generarPlanillaMultiple">

                                        <h5 class="mb-4"><i class="fas fa-list text-info me-2"></i>Generar Planillas Múltiples</h5>

                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="multipleMes" class="form-label">Mes</label>
                                                <select class="form-select" id="multipleMes" name="mes" required>
                                                    <option value="">Seleccionar mes...</option>
                                                    <option value="1">Enero</option>
                                                    <option value="2">Febrero</option>
                                                    <option value="3">Marzo</option>
                                                    <option value="4">Abril</option>
                                                    <option value="5">Mayo</option>
                                                    <option value="6">Junio</option>
                                                    <option value="7">Julio</option>
                                                    <option value="8">Agosto</option>
                                                    <option value="9">Septiembre</option>
                                                    <option value="10">Octubre</option>
                                                    <option value="11">Noviembre</option>
                                                    <option value="12">Diciembre</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="multipleAnio" class="form-label">Año</label>
                                                <select class="form-select" id="multipleAnio" name="anio" required>
                                                    <option value="">Seleccionar año...</option>
                                                    <option value="2023">2023</option>
                                                    <option value="2024">2024</option>
                                                    <option value="2025">2025</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- AÑADIR MÉTODO DE PAGO -->
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="multipleMetodoPago" class="form-label">Método de Pago</label>
                                                <select class="form-select" id="multipleMetodoPago" name="metodo_pago" required>
                                                    <option value="">Seleccionar método...</option>
                                                    <option value="transferencia">Transferencia Bancaria</option>
                                                    <option value="qr">Pago QR</option>
                                                    <option value="efectivo">Efectivo</option>
                                                    <option value="cheque">Cheque</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="jsonDescuentos" class="form-label">Descuentos por Empleado (JSON)</label>
                                            <textarea class="form-control" id="jsonDescuentos" name="json_descuentos" rows="8"
                                                      placeholder='{"1": 2.5, "2": 1.0, "3": 0.5}' required></textarea>
                                            <div class="form-text">
                                                <strong>Formato:</strong> {"id_persona": dias_descuento, ...}<br>
                                                <strong>Ejemplo:</strong> {"1": 2.5, "2": 1.0} - Persona 1 con 2.5 días de descuento, Persona 2 con 1 día
                                            </div>
                                        </div>

                                        <div class="alert alert-info">
                                            <h6><i class="fas fa-lightbulb me-2"></i>Ayuda Rápida</h6>
                                            <p class="mb-2">Puede usar el generador automático para crear el JSON:</p>
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="mostrarGeneradorJSON()">
                                                <i class="fas fa-magic me-1"></i>Generador Automático
                                            </button>
                                        </div>

                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <button type="button" class="btn btn-secondary" onclick="validarJSON()">
                                                <i class="fas fa-check me-2"></i>Validar JSON
                                            </button>
                                            <button type="submit" class="btn btn-info">
                                                <i class="fas fa-play-circle me-2"></i>Generar Planillas Múltiples
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-4">
                                    <div class="content-box">
                                        <div class="content-box-header">
                                            <h6><i class="fas fa-users me-2"></i>Lista de Empleados</h6>
                                        </div>
                                        <div class="content-box-body">
                                            <div id="listaEmpleadosMultiple">
                                                <p class="text-muted text-center">Cargando empleados...</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Generador JSON -->
    <div class="modal fade" id="generadorJSONModal" tabindex="-1" aria-labelledby="generadorJSONModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="generadorJSONModalLabel">
                        <i class="fas fa-magic me-2"></i>Generador Automático de JSON
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">Seleccione los empleados y establezca los días de descuento para cada uno:</p>

                    <div id="formularioGeneradorJSON">
                        <!-- Se carga dinámicamente -->
                    </div>

                    <div class="mt-3">
                        <label class="form-label">JSON Generado:</label>
                        <textarea class="form-control" id="jsonGenerado" rows="4" readonly></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" onclick="usarJSONGenerado()">
                        <i class="fas fa-check me-2"></i>Usar este JSON
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        let empleadosData = [];

        // Inicializar al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            cargarEmpleados();
            inicializarEventos();
        });

        function inicializarEventos() {
            // Planilla Completa - Cambio de periodo
            document.getElementById('completaMes').addEventListener('change', actualizarResumenCompleta);
            document.getElementById('completaAnio').addEventListener('change', actualizarResumenCompleta);
            document.getElementById('completaMetodoPago').addEventListener('change', actualizarEstadoPlanilla);

            // Planilla Personalizada - Cambio de empleado
            document.getElementById('personalizadaEmpleado').addEventListener('change', function() {
                const empleadoId = this.value;
                mostrarInfoEmpleado(empleadoId);
                actualizarCalculoPersonalizada();
            });

            // Planilla Personalizada - Cambio de días de descuento
            document.getElementById('diasDescuento').addEventListener('input', function() {
                const diasDescuento = parseFloat(this.value) || 0;
                const diasTrabajados = 30 - diasDescuento;
                document.getElementById('diasTrabajados').textContent = diasTrabajados;
                actualizarCalculoPersonalizada();
            });

            // Planilla Personalizada - Cambio de periodo
            document.getElementById('personalizadaMes').addEventListener('change', actualizarCalculoPersonalizada);
            document.getElementById('personalizadaAnio').addEventListener('change', actualizarCalculoPersonalizada);
            document.getElementById('personalizadaMetodoPago').addEventListener('change', actualizarEstadoPersonalizada);

            // Planillas Múltiples - Cambio de periodo
            document.getElementById('multipleMes').addEventListener('change', actualizarListaEmpleadosMultiple);
            document.getElementById('multipleAnio').addEventListener('change', actualizarListaEmpleadosMultiple);
            document.getElementById('multipleMetodoPago').addEventListener('change', actualizarEstadoMultiple);
        }

        function cargarEmpleados() {
            fetch('PlanillaControlador.php?action=obtenerEmpleadosActivos')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        empleadosData = data.data;
                        llenarSelectEmpleados();
                        actualizarResumenCompleta();
                        actualizarListaEmpleadosMultiple();
                    }
                })
                .catch(error => {
                    console.error('Error al cargar empleados:', error);
                });
        }

        function llenarSelectEmpleados() {
            const select = document.getElementById('personalizadaEmpleado');
            select.innerHTML = '<option value="">Seleccionar empleado...</option>';

            empleadosData.forEach(empleado => {
                const option = document.createElement('option');
                option.value = empleado.id_persona;
                option.textContent = `${empleado.nombre_completo} - ${empleado.rol} (Bs. ${formatCurrency(empleado.salario_base)})`;
                option.setAttribute('data-salario', empleado.salario_base);
                option.setAttribute('data-rol', empleado.rol);
                select.appendChild(option);
            });
        }

        function mostrarInfoEmpleado(empleadoId) {
            const infoDiv = document.getElementById('infoEmpleado');
            const empleado = empleadosData.find(e => e.id_persona == empleadoId);

            if (empleado) {
                document.getElementById('empleadoNombre').innerHTML = `<strong>Nombre:</strong> ${empleado.nombre_completo}`;
                document.getElementById('empleadoRol').innerHTML = `<strong>Rol:</strong> ${empleado.rol}`;
                document.getElementById('empleadoSalario').innerHTML = `<strong>Salario Base:</strong> Bs. ${formatCurrency(empleado.salario_base)}`;
                infoDiv.style.display = 'block';
            } else {
                infoDiv.style.display = 'none';
            }
        }

        function actualizarEstadoPlanilla() {
            const metodoPago = document.getElementById('completaMetodoPago').value;
            const estado = metodoPago === 'qr' ? 'procesada' : 'pagada';
            document.getElementById('estadoPlanilla').textContent = estado;
        }

        function actualizarEstadoPersonalizada() {
            const metodoPago = document.getElementById('personalizadaMetodoPago').value;
            const estado = metodoPago === 'qr' ? 'procesada' : 'pagada';
            document.getElementById('estadoPersonalizada').textContent = estado;
        }

        function actualizarEstadoMultiple() {
            // Para múltiples, el estado se determina igual
            const metodoPago = document.getElementById('multipleMetodoPago').value;
            const estado = metodoPago === 'qr' ? 'procesada' : 'pagada';
            // Puedes mostrar esto en algún lugar si es necesario
        }

        function actualizarResumenCompleta() {
            const mes = document.getElementById('completaMes').value;
            const anio = document.getElementById('completaAnio').value;
            const resumenDiv = document.getElementById('resumenCompleta');

            if (mes && anio) {
                const totalEmpleados = empleadosData.length;
                const totalSalarios = empleadosData.reduce((sum, emp) => sum + parseFloat(emp.salario_base), 0);
                const totalGestora = totalSalarios * 0.1271;
                const totalLiquido = totalSalarios - totalGestora;

                document.getElementById('totalEmpleados').textContent = totalEmpleados;

                resumenDiv.innerHTML = `
                    <div class="text-center mb-3">
                        <h6 class="text-primary">${getMonthName(mes)} ${anio}</h6>
                    </div>
                    <table class="table table-sm">
                        <tr><td><strong>Empleados:</strong></td><td>${totalEmpleados}</td></tr>
                        <tr><td><strong>Total Salarios:</strong></td><td>Bs. ${formatCurrency(totalSalarios)}</td></tr>
                        <tr><td><strong>Total Gestora:</strong></td><td>Bs. ${formatCurrency(totalGestora)}</td></tr>
                        <tr class="table-success"><td><strong>Total Líquido:</strong></td><td><strong>Bs. ${formatCurrency(totalLiquido)}</strong></td></tr>
                    </table>
                `;
            } else {
                resumenDiv.innerHTML = '<p class="text-muted text-center">Seleccione un periodo para ver el resumen</p>';
            }
        }

        function actualizarCalculoPersonalizada() {
            const empleadoId = document.getElementById('personalizadaEmpleado').value;
            const diasDescuento = parseFloat(document.getElementById('diasDescuento').value) || 0;
            const calculoDiv = document.getElementById('calculoPersonalizada');
            const empleado = empleadosData.find(e => e.id_persona == empleadoId);

            if (empleado && diasDescuento >= 0) {
                const salarioBase = parseFloat(empleado.salario_base);
                const diasTrabajados = 30 - diasDescuento;
                const salarioDiario = salarioBase / 30;
                const totalGanado = salarioDiario * diasTrabajados;
                const descuentoGestora = salarioBase * 0.1271;
                const liquidoPagable = totalGanado - descuentoGestora;

                calculoDiv.innerHTML = `
                    <div class="calculation-preview">
                        <table class="table table-sm table-bordered">
                            <tr><td><strong>Salario Base:</strong></td><td>Bs. ${formatCurrency(salarioBase)}</td></tr>
                            <tr><td><strong>Días Trabajados:</strong></td><td>${diasTrabajados} días</td></tr>
                            <tr><td><strong>Total Ganado:</strong></td><td>Bs. ${formatCurrency(totalGanado)}</td></tr>
                            <tr><td><strong>Descuento Gestora:</strong></td><td>Bs. ${formatCurrency(descuentoGestora)}</td></tr>
                            <tr class="table-success"><td><strong>Líquido Pagable:</strong></td><td><strong>Bs. ${formatCurrency(liquidoPagable)}</strong></td></tr>
                        </table>
                    </div>
                `;
            } else {
                calculoDiv.innerHTML = '<p class="text-muted text-center">Seleccione un empleado y periodo</p>';
            }
        }

        function actualizarListaEmpleadosMultiple() {
            const listaDiv = document.getElementById('listaEmpleadosMultiple');

            if (empleadosData.length > 0) {
                let html = '<div class="empleados-list">';
                empleadosData.forEach(empleado => {
                    html += `
                        <div class="empleado-item mb-2 p-2 border rounded">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>${empleado.nombre_completo}</strong>
                                    <br>
                                    <small class="text-muted">${empleado.rol} - Bs. ${formatCurrency(empleado.salario_base)}</small>
                                </div>
                                <div>
                                    <small class="text-info">ID: ${empleado.id_persona}</small>
                                </div>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
                listaDiv.innerHTML = html;
            } else {
                listaDiv.innerHTML = '<p class="text-muted text-center">No hay empleados activos</p>';
            }
        }

        function mostrarGeneradorJSON() {
            const modal = new bootstrap.Modal(document.getElementById('generadorJSONModal'));
            const formularioDiv = document.getElementById('formularioGeneradorJSON');

            let html = '<div class="row">';
            empleadosData.forEach(empleado => {
                html += `
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">${empleado.nombre_completo}</h6>
                                <p class="card-text text-muted small">${empleado.rol} - ID: ${empleado.id_persona}</p>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Días descuento</span>
                                    <input type="number" class="form-control empleado-descuento"
                                           data-id="${empleado.id_persona}"
                                           min="0" max="30" step="0.5" value="0"
                                           onchange="actualizarJSONGenerado()">
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';

            formularioDiv.innerHTML = html;
            actualizarJSONGenerado();
            modal.show();
        }

        function actualizarJSONGenerado() {
            const descuentos = {};
            const inputs = document.querySelectorAll('.empleado-descuento');

            inputs.forEach(input => {
                const id = input.getAttribute('data-id');
                const valor = parseFloat(input.value) || 0;
                if (valor > 0) {
                    descuentos[id] = valor;
                }
            });

            document.getElementById('jsonGenerado').value = JSON.stringify(descuentos, null, 2);
        }

        function usarJSONGenerado() {
            const json = document.getElementById('jsonGenerado').value;
            document.getElementById('jsonDescuentos').value = json;

            const modal = bootstrap.Modal.getInstance(document.getElementById('generadorJSONModal'));
            modal.hide();

            mostrarMensaje('JSON copiado al formulario principal', 'success');
        }

        function validarJSON() {
            const jsonInput = document.getElementById('jsonDescuentos').value;

            try {
                const json = JSON.parse(jsonInput);
                const totalEmpleados = Object.keys(json).length;

                mostrarMensaje(`JSON válido. Se procesarán ${totalEmpleados} empleados.`, 'success');
            } catch (error) {
                mostrarMensaje('Error: JSON inválido. Por favor verifique el formato.', 'danger');
            }
        }

        function previsualizarCompleta() {
            const mes = document.getElementById('completaMes').value;
            const anio = document.getElementById('completaAnio').value;
            const metodoPago = document.getElementById('completaMetodoPago').value;

            if (!mes || !anio || !metodoPago) {
                mostrarMensaje('Por favor complete todos los campos', 'warning');
                return;
            }

            mostrarMensaje(`Previsualizando planilla completa para ${getMonthName(mes)} ${anio} (${metodoPago})...`, 'info');
        }

        function previsualizarPersonalizada() {
            const empleadoId = document.getElementById('personalizadaEmpleado').value;
            const mes = document.getElementById('personalizadaMes').value;
            const anio = document.getElementById('personalizadaAnio').value;
            const metodoPago = document.getElementById('personalizadaMetodoPago').value;

            if (!empleadoId || !mes || !anio || !metodoPago) {
                mostrarMensaje('Por favor complete todos los campos', 'warning');
                return;
            }

            const empleado = empleadosData.find(e => e.id_persona == empleadoId);
            mostrarMensaje(`Previsualizando planilla para ${empleado.nombre_completo} - ${getMonthName(mes)} ${anio} (${metodoPago})...`, 'info');
        }

        // Funciones auxiliares
        function formatCurrency(amount) {
            return parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }

        function getMonthName(monthNumber) {
            const months = [
                'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
            ];
            return months[parseInt(monthNumber) - 1];
        }

        function mostrarMensaje(mensaje, tipo) {
            const alertClass = tipo === 'success' ? 'alert-success' :
                tipo === 'warning' ? 'alert-warning' :
                    tipo === 'danger' ? 'alert-danger' : 'alert-info';

            const alertHTML = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    <i class="fas fa-${tipo === 'success' ? 'check' :
                tipo === 'warning' ? 'exclamation-triangle' :
                    tipo === 'danger' ? 'times' : 'info'}-circle me-2"></i>
                    ${mensaje}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;

            document.querySelector('.page-header').insertAdjacentHTML('afterend', alertHTML);

            // Auto-eliminar después de 5 segundos
            setTimeout(() => {
                const alert = document.querySelector('.alert');
                if (alert) {
                    alert.remove();
                }
            }, 5000);
        }
    </script>

    <!-- Estilos adicionales -->
    <style>
        .nav-tabs .nav-link {
            color: #6c757d;
            font-weight: 500;
        }

        .nav-tabs .nav-link.active {
            color: var(--azul-oscuro);
            font-weight: 600;
        }

        .empleado-info .card {
            border-left: 4px solid var(--azul-oscuro);
        }

        .calculation-preview table td {
            padding: 0.5rem;
        }

        .empleados-list .empleado-item {
            transition: all 0.2s;
        }

        .empleados-list .empleado-item:hover {
            background-color: #f8f9fa;
            border-color: var(--azul-oscuro);
        }

        .content-box {
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
        }

        .content-box-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 1px solid #dee2e6;
            padding: 1rem 1.25rem;
        }

        .content-box-body {
            padding: 1.25rem;
        }

        .form-text {
            font-size: 0.875rem;
        }

        .tab-content {
            border: 1px solid #dee2e6;
            border-top: none;
            border-radius: 0 0 0.5rem 0.5rem;
        }

        .nav-tabs {
            border-bottom: 1px solid #dee2e6;
        }

        /* Colores específicos para cada pestaña */
        #completa-tab.active { border-bottom-color: var(--azul-oscuro); }
        #personalizada-tab.active { border-bottom-color: #ffc107; }
        #multiple-tab.active { border-bottom-color: #0dcaf0; }

        .btn-primary { background-color: var(--azul-oscuro); border-color: var(--azul-oscuro); }
        .btn-warning { background-color: #ffc107; border-color: #ffc107; color: #000; }
        .btn-info { background-color: #0dcaf0; border-color: #0dcaf0; }

        .alert {
            border: none;
            border-radius: 0.5rem;
        }
    </style>

<?php include("../../includes/footer.php"); ?>
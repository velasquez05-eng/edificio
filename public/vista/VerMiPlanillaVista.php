<?php include("../../includes/header.php"); ?>

    <!-- Page Header -->
    <div class="page-header fade-in">
        <div class="page-title">
            <h1>Mi Planilla de Pagos</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item"><a href="#">Recursos Humanos</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Mi Planilla</li>
                </ol>
            </nav>
        </div>
        <div class="page-actions">
            <button class="btn btn-success" onclick="exportarMiPlanillaCSV()">
                <i class="fas fa-file-csv me-2"></i>Descargar CSV
            </button>
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

    <!-- Información del Empleado -->
    <div class="row fade-in mb-4">
        <div class="col-md-12">
            <div class="content-box">
                <div class="content-box-header">
                    <h5><i class="fas fa-user me-2"></i>Mi Información</h5>
                </div>
                <div class="content-box-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <div class="avatar-circle mb-3">
                                <span class="avatar-text"><?php echo $_SESSION['avatar'] ?? 'US'; ?></span>
                            </div>
                            <h5><?php echo htmlspecialchars($_SESSION['nombre'] . ' ' . $_SESSION['apellido_paterno'] . ' ' . ($_SESSION['apellido_materno'] ?? '')); ?></h5>
                            <span class="badge bg-primary"><?php echo htmlspecialchars($_SESSION['rol_nombre'] ?? 'Empleado'); ?></span>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td><strong><i class="fas fa-id-card me-2 text-muted"></i>CI:</strong></td>
                                            <td><?php echo htmlspecialchars($_SESSION['ci'] ?? 'N/A'); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong><i class="fas fa-envelope me-2 text-muted"></i>Email:</strong></td>
                                            <td><?php echo htmlspecialchars($_SESSION['email'] ?? 'N/A'); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong><i class="fas fa-phone me-2 text-muted"></i>Teléfono:</strong></td>
                                            <td><?php echo htmlspecialchars($_SESSION['telefono'] ?? 'N/A'); ?></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td><strong><i class="fas fa-user-tag me-2 text-muted"></i>Usuario:</strong></td>
                                            <td><?php echo htmlspecialchars($_SESSION['username'] ?? 'N/A'); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong><i class="fas fa-calendar me-2 text-muted"></i>Último Acceso:</strong></td>
                                            <td><?php echo date('d/m/Y H:i'); ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros y Resumen -->
    <div class="row fade-in mb-4">
        <!-- Filtros -->
        <div class="col-md-4">
            <div class="content-box">
                <div class="content-box-header">
                    <h5><i class="fas fa-filter me-2"></i>Filtrar Planillas</h5>
                </div>
                <div class="content-box-body">
                    <form method="GET" action="PlanillaControlador.php">
                        <input type="hidden" name="action" value="verMiPlanilla">
                        <div class="mb-3">
                            <label for="filtroMes" class="form-label">Mes</label>
                            <select id="filtroMes" name="mes" class="form-select">
                                <option value="">Todos los meses</option>
                                <?php
                                $meses = [
                                        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                                        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                                        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                                ];
                                $mesActual = $_GET['mes'] ?? '';
                                foreach($meses as $numero => $nombre): ?>
                                    <option value="<?php echo $numero; ?>" <?php echo $mesActual == $numero ? 'selected' : ''; ?>>
                                        <?php echo $nombre; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="filtroAnio" class="form-label">Año</label>
                            <select id="filtroAnio" name="anio" class="form-select">
                                <option value="">Todos los años</option>
                                <?php
                                $anioActual = $_GET['anio'] ?? '';
                                for($i = 2023; $i <= 2025; $i++): ?>
                                    <option value="<?php echo $i; ?>" <?php echo $anioActual == $i ? 'selected' : ''; ?>>
                                        <?php echo $i; ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="PlanillaControlador.php?action=verMiPlanilla" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-times me-2"></i>Limpiar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-2"></i>Aplicar Filtros
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Resumen -->
        <div class="col-md-8">
            <div class="content-box">
                <div class="content-box-header">
                    <h5><i class="fas fa-chart-bar me-2"></i>Resumen <?php
                        if (isset($_GET['mes']) && $_GET['mes'] != '') {
                            $mesNumero = $_GET['mes'];
                            $mesNombre = $meses[$mesNumero] ?? '';
                            echo "de " . $mesNombre;
                        }
                        if (isset($_GET['anio']) && $_GET['anio'] != '') {
                            echo " " . $_GET['anio'];
                        }
                        if (!isset($_GET['mes']) && !isset($_GET['anio'])) {
                            echo "General";
                        }
                        ?></h5>
                </div>
                <div class="content-box-body">
                    <div class="row text-center">
                        <?php
                        // Calcular resumen desde PHP
                        $totalGanado = 0;
                        $totalGestora = 0;
                        $totalLiquido = 0;
                        $totalPlanillas = count($planillas);

                        if (!empty($planillas)) {
                            foreach($planillas as $planilla) {
                                $totalGanado += $planilla['total_ganado'];
                                $totalGestora += $planilla['descuento_gestora'];
                                $totalLiquido += $planilla['liquido_pagable'];
                            }
                        }
                        ?>
                        <div class="col-4 mb-3">
                            <div class="stat-card">
                                <i class="fas fa-file-invoice-dollar fa-2x text-info mb-2"></i>
                                <h4 class="mb-1"><?php echo $totalPlanillas; ?></h4>
                                <p class="text-muted mb-0">Planillas</p>
                            </div>
                        </div>
                        <div class="col-4 mb-3">
                            <div class="stat-card">
                                <i class="fas fa-money-bill-wave fa-2x text-success mb-2"></i>
                                <h4 class="mb-1">Bs. <?php echo number_format($totalGanado, 2); ?></h4>
                                <p class="text-muted mb-0">Total Ganado</p>
                            </div>
                        </div>
                        <div class="col-4 mb-3">
                            <div class="stat-card">
                                <i class="fas fa-wallet fa-2x text-primary mb-2"></i>
                                <h4 class="mb-1">Bs. <?php echo number_format($totalLiquido, 2); ?></h4>
                                <p class="text-muted mb-0">Total Líquido</p>
                            </div>
                        </div>
                        <div class="col-12">
                            <small class="text-muted">
                                <?php
                                if ($totalPlanillas > 0) {
                                    echo "Mostrando " . $totalPlanillas . " planilla(s)";
                                } else {
                                    echo "No hay planillas para el filtro seleccionado";
                                }
                                ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mis Planillas -->
    <div class="row fade-in">
        <div class="col-12">
            <div class="content-box">
                <div class="content-box-header d-flex justify-content-between align-items-center">
                    <h5>Historial de Mis Planillas</h5>
                    <span class="badge bg-primary"><?php echo count($planillas); ?> planillas</span>
                </div>
                <div class="content-box-body">
                    <?php if (empty($planillas)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-file-invoice-dollar fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No hay planillas registradas</h5>
                            <p class="text-muted">No se encontraron planillas de pago para el periodo seleccionado.</p>
                            <div class="mt-3">
                                <i class="fas fa-info-circle text-info me-2"></i>
                                <small class="text-muted">
                                    <?php
                                    if (isset($_GET['mes']) || isset($_GET['anio'])) {
                                        echo "Prueba con otros filtros o limpia los filtros actuales.";
                                    } else {
                                        echo "Las planillas se generan automáticamente cada mes.";
                                    }
                                    ?>
                                </small>
                            </div>
                            <?php if (isset($_GET['mes']) || isset($_GET['anio'])): ?>
                                <a href="PlanillaControlador.php?action=verMiPlanilla" class="btn btn-primary mt-3">
                                    <i class="fas fa-times me-2"></i>Limpiar Filtros
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="table-container">
                            <table id="tablaMiPlanilla" class="table table-hover table-striped">
                                <thead>
                                <tr>
                                    <th>Periodo</th>
                                    <th>Salario Base</th>
                                    <th>Días Trabajados</th>
                                    <th>Total Ganado</th>
                                    <th>Descuento Gestora</th>
                                    <th>Líquido Pagable</th>
                                    <th>Estado</th>
                                    <th>Observaciones</th>
                                    <th>Acciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                // Array de meses en español para conversión
                                $meses_espanol = [
                                        'January' => 'Enero', 'February' => 'Febrero', 'March' => 'Marzo',
                                        'April' => 'Abril', 'May' => 'Mayo', 'June' => 'Junio',
                                        'July' => 'Julio', 'August' => 'Agosto', 'September' => 'Septiembre',
                                        'October' => 'Octubre', 'November' => 'Noviembre', 'December' => 'Diciembre'
                                ];

                                foreach ($planillas as $planilla):
                                    // Convertir el periodo a español
                                    $periodo_ingles = date('F Y', strtotime($planilla['periodo']));
                                    $periodo_espanol = strtr($periodo_ingles, $meses_espanol);
                                    ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo $periodo_espanol; ?></strong>
                                            <br>
                                            <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($planilla['fecha_creacion'])); ?></small>
                                        </td>
                                        <td>
                                            Bs. <?php echo number_format($planilla['haber_basico'], 2); ?>
                                        </td>
                                        <td>
                                            <span class="badge <?php echo $planilla['dias_trabajados'] < 30 ? 'bg-warning' : 'bg-success'; ?>">
                                                <?php echo $planilla['dias_trabajados']; ?> días
                                            </span>
                                        </td>
                                        <td>
                                            <strong class="text-success">
                                                Bs. <?php echo number_format($planilla['total_ganado'], 2); ?>
                                            </strong>
                                        </td>
                                        <td>
                                            <span class="text-danger">
                                                Bs. <?php echo number_format($planilla['descuento_gestora'], 2); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <strong class="text-primary">
                                                Bs. <?php echo number_format($planilla['liquido_pagable'], 2); ?>
                                            </strong>
                                        </td>
                                        <td>
                                            <span class="badge <?php echo $planilla['estado'] == 'pagada' ? 'bg-success' : ($planilla['estado'] == 'procesada' ? 'bg-warning' : 'bg-secondary'); ?>">
                                                <?php echo ucfirst($planilla['estado']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small class="<?php echo strpos($planilla['observacion'], 'Descuento') !== false ? 'text-warning' : 'text-success'; ?>">
                                                <?php echo htmlspecialchars($planilla['observacion']); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-warning btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#desgloseCalculoModal"
                                                        onclick="mostrarDesgloseCalculos(<?php echo htmlspecialchars(json_encode($planilla)); ?>)"
                                                        title="Ver desglose de cálculos">
                                                    <i class="fas fa-calculator"></i>
                                                </button>

                                                <?php if ($planilla['estado'] == 'procesada'): ?>
                                                    <!-- Estado Procesada: Solo botón QR -->
                                                    <button class="btn btn-info btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#pagoQRModal"
                                                            onclick="mostrarQRPago(<?php echo htmlspecialchars(json_encode($planilla)); ?>)"
                                                            title="Pagar con QR">
                                                        <i class="fas fa-qrcode"></i>
                                                    </button>
                                                <?php elseif ($planilla['estado'] == 'pagada'): ?>
                                                    <!-- Estado Pagada: Mostrar PDF -->
                                                    <a href="PlanillaControlador.php?action=descargarReciboPDF&id=<?php echo $planilla['id_planilla_emp']; ?>"
                                                       class="btn btn-danger btn-sm"
                                                       title="Descargar Recibo PDF">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <!-- Otros estados -->
                                                    <button class="btn btn-secondary btn-sm" disabled>
                                                        <i class="fas fa-ban"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
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

    <!-- Modal Desglose de Cálculos -->
    <div class="modal fade" id="desgloseCalculoModal" tabindex="-1" aria-labelledby="desgloseCalculoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="desgloseCalculoModalLabel">
                        <i class="fas fa-calculator me-2"></i>Desglose de Cálculos
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="contenido-desglose">
                        <!-- Se carga dinámicamente -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Pago QR -->
    <div class="modal fade" id="pagoQRModal" tabindex="-1" aria-labelledby="pagoQRModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pagoQRModalLabel">
                        <i class="fas fa-qrcode me-2"></i>Pago con QR
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div id="contenido-qr">
                        <!-- Se carga dinámicamente -->
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" class="btn btn-success" id="btnVerificarPago" onclick="verificarPago()">
                        <i class="fas fa-sync-alt me-2"></i>Verificar Pago
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Incluir QRCodeJS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <!-- Scripts -->
    <script>
        let planillaActual = null;
        let currentQRCode = null;

        // Función para mostrar desglose de cálculos
        function mostrarDesgloseCalculos(planilla) {
            const diasDescuento = 30 - planilla.dias_trabajados;
            const salarioDiario = planilla.haber_basico / 30;
            const descuentoPorDias = salarioDiario * diasDescuento;
            const porcentajeGestora = 12.71;

            document.getElementById('contenido-desglose').innerHTML = `
                <h6 class="mb-3">Desglose de Cálculos - ${formatDate(planilla.periodo)}</h6>

                <div class="calculation-steps">
                    <div class="step">
                        <h6>1. Cálculo de Días Trabajados</h6>
                        <p class="mb-1">Días del mes: <strong>30 días</strong></p>
                        <p class="mb-1">Días de descuento: <strong>${diasDescuento} días</strong></p>
                        <p class="mb-0">Días trabajados: <strong>30 - ${diasDescuento} = ${planilla.dias_trabajados} días</strong></p>
                    </div>

                    <div class="step">
                        <h6>2. Cálculo de Salario Diario</h6>
                        <p class="mb-0">Salario base / 30 días = <strong>Bs. ${formatCurrency(planilla.haber_basico)} ÷ 30 = Bs. ${formatCurrency(salarioDiario)} por día</strong></p>
                    </div>

                    <div class="step">
                        <h6>3. Cálculo de Total Ganado</h6>
                        <p class="mb-0">Salario diario × días trabajados = <strong>Bs. ${formatCurrency(salarioDiario)} × ${planilla.dias_trabajados} = Bs. ${formatCurrency(planilla.total_ganado)}</strong></p>
                    </div>

                    ${diasDescuento > 0 ? `
                    <div class="step">
                        <h6>4. Descuento por Días No Trabajados</h6>
                        <p class="mb-0">Salario diario × días de descuento = <strong>Bs. ${formatCurrency(salarioDiario)} × ${diasDescuento} = Bs. ${formatCurrency(descuentoPorDias)}</strong></p>
                    </div>
                    ` : ''}

                    <div class="step">
                        <h6>${diasDescuento > 0 ? '5.' : '4.'} Descuento Gestora</h6>
                        <p class="mb-1">Salario base × ${porcentajeGestora}% = <strong>Bs. ${formatCurrency(planilla.haber_basico)} × 0.1271</strong></p>
                        <p class="mb-0">Total descuento gestora: <strong>Bs. ${formatCurrency(planilla.descuento_gestora)}</strong></p>
                    </div>

                    <div class="step">
                        <h6>${diasDescuento > 0 ? '6.' : '5.'} Cálculo Final</h6>
                        <p class="mb-1">Total ganado - Descuento gestora = <strong>Bs. ${formatCurrency(planilla.total_ganado)} - Bs. ${formatCurrency(planilla.descuento_gestora)}</strong></p>
                        <p class="mb-0 text-primary"><strong>Líquido pagable: Bs. ${formatCurrency(planilla.liquido_pagable)}</strong></p>
                    </div>
                </div>
            `;
        }

        // Función para mostrar QR de pago
        function mostrarQRPago(planilla) {
            planillaActual = planilla;

            // Solo enviar el monto en el QR
            const monto = planilla.liquido_pagable;
            const datosQR = `${monto}`;

            document.getElementById('contenido-qr').innerHTML = `
                <div class="mb-3">
                    <h6 class="text-primary">${formatDate(planilla.periodo)}</h6>
                    <h4 class="text-success">Bs. ${formatCurrency(planilla.liquido_pagable)}</h4>
                </div>

                <div class="qr-placeholder mb-3">
                    <div class="qr-code bg-light p-4 rounded d-inline-block" id="qr-container">
                        <!-- QR se generará aquí -->
                    </div>
                </div>

                <div class="payment-info text-start">
                    <p class="mb-1"><strong>Concepto:</strong> Pago de Planilla</p>
                    <p class="mb-1"><strong>Periodo:</strong> ${formatDate(planilla.periodo)}</p>
                    <p class="mb-1"><strong>Beneficiario:</strong> ${planilla.nombre_completo}</p>
                    <p class="mb-0"><strong>Estado:</strong> <span class="badge bg-warning">Pendiente</span></p>
                </div>

                <div class="mt-3">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Escanee el código QR con su app de banca móvil para realizar el pago
                    </small>
                </div>
            `;

            // Generar QR con solo el monto
            generarQR(datosQR);
        }

        // Función para generar QR
        function generarQR(datos) {
            const qrContainer = document.getElementById("qr-container");

            // Limpiar QR anterior
            if (currentQRCode) {
                currentQRCode.clear();
            }
            qrContainer.innerHTML = '';

            // Generar nuevo QR
            currentQRCode = new QRCode(qrContainer, {
                text: datos,
                width: 200,
                height: 200,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });
        }

        // Función para verificar pago
        function verificarPago() {
            if (!planillaActual) {
                alert('No hay planilla seleccionada');
                return;
            }

            const idPlanilla = planillaActual.id_planilla_emp;
            const mes = <?php echo json_encode($_GET['mes'] ?? ''); ?>;
            const anio = <?php echo json_encode($_GET['anio'] ?? ''); ?>;

            // Construir URL con parámetros
            let url = `PlanillaControlador.php?action=verificarPago&id_planilla=${idPlanilla}`;

            if (mes) url += `&mes=${mes}`;
            if (anio) url += `&anio=${anio}`;

            // Redirigir para verificar pago
            window.location.href = url;
        }

        function exportarMiPlanillaCSV() {
            // Crear contenido CSV
            const headers = [
                'Periodo',
                'Salario Base',
                'Días Trabajados',
                'Total Ganado',
                'Descuento Gestora',
                'Líquido Pagable',
                'Estado',
                'Observaciones',
                'Fecha Generación'
            ];

            let csvContent = headers.join(',') + '\n';

            // Usar datos de PHP directamente
            <?php if (!empty($planillas)): ?>
            <?php foreach($planillas as $planilla):
            // Convertir periodo a español para CSV también
            $periodo_ingles = date('F Y', strtotime($planilla['periodo']));
            $periodo_espanol = strtr($periodo_ingles, $meses_espanol);
            ?>
            csvContent += `"<?php echo $periodo_espanol; ?>",<?php echo $planilla['haber_basico']; ?>,<?php echo $planilla['dias_trabajados']; ?>,<?php echo $planilla['total_ganado']; ?>,<?php echo $planilla['descuento_gestora']; ?>,<?php echo $planilla['liquido_pagable']; ?>,"<?php echo $planilla['estado']; ?>","<?php echo $planilla['observacion']; ?>","<?php echo date('d/m/Y H:i', strtotime($planilla['fecha_creacion'])); ?>"\n`;
            <?php endforeach; ?>
            <?php endif; ?>

            // Crear y descargar archivo
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);

            const fecha = new Date().toISOString().split('T')[0];
            link.setAttribute('href', url);
            link.setAttribute('download', `mi_planilla_${fecha}.csv`);
            link.style.visibility = 'hidden';

            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            mostrarMensaje('CSV descargado exitosamente', 'success');
        }

        // Funciones auxiliares
        function formatCurrency(amount) {
            return parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            const meses = [
                'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
            ];
            const mes = meses[date.getMonth()];
            const año = date.getFullYear();
            return `${mes} ${año}`;
        }

        function mostrarMensaje(mensaje, tipo) {
            const alertClass = tipo === 'success' ? 'alert-success' :
                tipo === 'warning' ? 'alert-warning' : 'alert-info';
            const icon = tipo === 'success' ? 'check' :
                tipo === 'warning' ? 'exclamation-triangle' : 'info';

            const alertHTML = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    <i class="fas fa-${icon}-circle me-2"></i>
                    ${mensaje}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            document.querySelector('.page-header').insertAdjacentHTML('afterend', alertHTML);
        }

        // Inicializar DataTable al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('tablaMiPlanilla')) {
                $('#tablaMiPlanilla').DataTable({
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                    },
                    pageLength: 10,
                    order: [[0, 'desc']],
                    columnDefs: [
                        {
                            orderable: false,
                            targets: [8] // Columna de acciones no ordenable
                        }
                    ]
                });
            }

            // Auto-ocultar alertas después de 5 segundos
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });
    </script>

    <!-- Estilos adicionales -->
    <style>
        .avatar-circle {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--azul-oscuro), var(--azul-claro));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }

        .avatar-text {
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .stat-card {
            padding: 15px;
            border-radius: 8px;
            background: #f8f9fa;
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            background: #e9ecef;
        }

        .stat-card h4 {
            font-weight: bold;
        }

        .calculation-steps .step {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            border-left: 4px solid var(--azul-oscuro);
        }

        .calculation-steps .step h6 {
            color: var(--azul-oscuro);
            margin-bottom: 10px;
        }

        .calculation-steps .step p {
            margin-bottom: 5px;
        }

        .table th {
            border-top: none;
            font-weight: 600;
            color: var(--azul-oscuro);
            background-color: #f8f9fa;
        }

        .btn-group .btn {
            margin: 0 2px;
        }

        .content-box-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 1px solid #dee2e6;
        }

        .table-container {
            width: 100%;
            overflow-x: auto;
        }

        .qr-placeholder .qr-code {
            border: 2px dashed #dee2e6;
        }

        .payment-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #28a745;
        }

        /* Estilos para badges de estado */
        .bg-success { background-color: #198754 !important; }
        .bg-warning { background-color: #ffc107 !important; color: #000 !important; }
        .bg-secondary { background-color: #6c757d !important; }
    </style>

<?php include("../../includes/footer.php"); ?>
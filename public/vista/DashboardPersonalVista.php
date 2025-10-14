<?php
include("../../includes/header.php");

// Verificar que las variables estén definidas
$metricasFinancieras = $metricasFinancieras ?? [];
$departamentosProblema = $departamentosProblema ?? ['riesgo' => [], 'corte' => []];
$consumosPromedio = $consumosPromedio ?? [];
$metricasSeguridad = $metricasSeguridad ?? ['no_verificados' => 0, 'login_fallidos' => []];
$incidentesRecientes = $incidentesRecientes ?? [];
$historialIncidentes = $historialIncidentes ?? [];
$promedioPagos = $promedioPagos ?? [];
$reservasProximas = $reservasProximas ?? [];
$facturasVencidas = $facturasVencidas ?? [];
$estadisticasGenerales = $estadisticasGenerales ?? [];

// Parámetros de filtro
$mes_filtro = $_GET['mes'] ?? date('m');
$anio_filtro = $_GET['anio'] ?? date('Y');
?>

    <!-- Page Header -->
    <div class="page-header fade-in">
        <div class="page-title">
            <h1>Dashboard Administrador - Condominio Inteligente</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Dashboard Administrador</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Mostrar mensajes de éxito o error -->
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

    <div class="container-fluid">
        <!-- Filtros -->
        <div class="filter-section">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="mb-0 text-dark">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard de Administración
                    </h4>
                    <p class="text-muted mb-0">Monitoreo integral del condominio</p>
                </div>
                <div class="col-md-4">
                    <form method="GET" class="row g-2">
                        <input type="hidden" name="action" value="dashboardAdministrador">
                        <div class="col-4">
                            <select name="mes" class="form-select form-select-sm">
                                <?php for($i=1; $i<=12; $i++): ?>
                                    <option value="<?= sprintf('%02d', $i) ?>" <?= $i == $mes_filtro ? 'selected' : '' ?>>
                                        <?= DateTime::createFromFormat('!m', $i)->format('F') ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-4">
                            <select name="anio" class="form-select form-select-sm">
                                <?php for($i=2022; $i<=2025; $i++): ?>
                                    <option value="<?= $i ?>" <?= $i == $anio_filtro ? 'selected' : '' ?>>
                                        <?= $i ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i class="fas fa-filter"></i> Filtrar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Métricas Principales -->
        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card metric-card primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="stat-label">INGRESOS DEL MES</p>
                                <h3 class="stat-number">Bs. <?= number_format($metricasFinancieras['ingresos_mes'] ?? 0, 2) ?></h3>
                            </div>
                            <div class="icon">
                                <i class="fas fa-money-bill-wave fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card metric-card danger">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="stat-label">DEUDA TOTAL</p>
                                <h3 class="stat-number">Bs. <?= number_format($metricasFinancieras['deuda_total'] ?? 0, 2) ?></h3>
                                <small><?= $metricasFinancieras['total_facturas_vencidas'] ?? 0 ?> facturas pendientes</small>
                            </div>
                            <div class="icon">
                                <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card metric-card warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="stat-label">MOROSIDAD</p>
                                <h3 class="stat-number">Bs. <?= number_format($metricasFinancieras['morosidad'] ?? 0, 2) ?></h3>
                                <small><?= $metricasFinancieras['facturas_vencidas'] ?? 0 ?> facturas vencidas</small>
                            </div>
                            <div class="icon">
                                <i class="fas fa-clock fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card metric-card success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="stat-label">DEPTOS. CON PROBLEMAS</p>
                                <h3 class="stat-number"><?= count($departamentosProblema['riesgo']) + count($departamentosProblema['corte']) ?></h3>
                                <small><?= count($departamentosProblema['corte']) ?> en corte</small>
                            </div>
                            <div class="icon">
                                <i class="fas fa-home fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alertas de Corte y Riesgo -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <i class="fas fa-ban me-2"></i>Departamentos en Corte de Servicio
                        <span class="badge bg-light text-dark ms-2"><?= count($departamentosProblema['corte']) ?></span>
                    </div>
                    <div class="card-body">
                        <?php if (count($departamentosProblema['corte']) > 0): ?>
                            <?php foreach($departamentosProblema['corte'] as $depto): ?>
                                <div class="alert alert-danger-custom alert-custom d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <strong>Dpto. <?= $depto['numero'] ?></strong> (Piso <?= $depto['piso'] ?>)
                                        <br>
                                        <small><?= $depto['facturas_vencidas'] ?> facturas vencidas</small>
                                    </div>
                                    <div class="text-end">
                                        <strong>Bs. <?= number_format($depto['deuda_departamento'], 2) ?></strong>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted text-center py-3">
                                <i class="fas fa-check-circle me-2 text-success"></i>
                                No hay departamentos en corte de servicio
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <i class="fas fa-exclamation-triangle me-2"></i>Departamentos en Riesgo de Corte
                        <span class="badge bg-light text-dark ms-2"><?= count($departamentosProblema['riesgo']) ?></span>
                    </div>
                    <div class="card-body">
                        <?php if (count($departamentosProblema['riesgo']) > 0): ?>
                            <?php foreach($departamentosProblema['riesgo'] as $depto): ?>
                                <div class="alert alert-warning-custom alert-custom d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <strong>Dpto. <?= $depto['numero'] ?></strong> (Piso <?= $depto['piso'] ?>)
                                        <br>
                                        <small><?= $depto['facturas_vencidas'] ?> facturas vencidas</small>
                                    </div>
                                    <div class="text-end">
                                        <strong>Bs. <?= number_format($depto['deuda_departamento'], 2) ?></strong>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted text-center py-3">
                                <i class="fas fa-check-circle me-2 text-success"></i>
                                No hay departamentos en riesgo de corte
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Segunda Fila: Consumos y Seguridad -->
        <div class="row mt-4">
            <!-- Consumo por Servicio -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-chart-bar me-2"></i>Consumo Promedio - <?= DateTime::createFromFormat('!m', $mes_filtro)->format('F') ?> <?= $anio_filtro ?>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                <tr>
                                    <th>Servicio</th>
                                    <th>Consumo Promedio</th>
                                    <th>Costo Promedio</th>
                                    <th>Unidad</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach($consumosPromedio as $consumo): ?>
                                    <tr>
                                        <td>
                                            <i class="fas fa-<?= $consumo['servicio'] == 'agua' ? 'tint' : ($consumo['servicio'] == 'luz' ? 'bolt' : 'fire') ?> me-2 text-<?= $consumo['servicio'] == 'agua' ? 'primary' : ($consumo['servicio'] == 'luz' ? 'warning' : 'danger') ?>"></i>
                                            <?= ucfirst($consumo['servicio']) ?>
                                        </td>
                                        <td><strong><?= number_format($consumo['consumo_promedio'], 2) ?></strong></td>
                                        <td><strong class="text-success">Bs. <?= number_format($consumo['costo_promedio'], 2) ?></strong></td>
                                        <td><span class="badge bg-secondary"><?= $consumo['unidad_medida'] ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Métricas de Seguridad -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <i class="fas fa-shield-alt me-2"></i>Métricas de Seguridad
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-user-clock me-2"></i>
                                    <strong>Usuarios no verificados:</strong>
                                </div>
                                <span class="badge bg-warning"><?= $metricasSeguridad['no_verificados'] ?></span>
                            </div>
                        </div>

                        <h6 class="border-bottom pb-2">
                            <i class="fas fa-sign-in-alt me-2"></i>Intentos fallidos de login (30 días):
                        </h6>
                        <?php if (count($metricasSeguridad['login_fallidos']) > 0): ?>
                            <?php foreach($metricasSeguridad['login_fallidos'] as $intento): ?>
                                <div class="alert alert-danger py-2 mb-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?= $intento['username'] ?></strong>
                                            <br>
                                            <small class="text-muted"><?= $intento['rol'] ?></small>
                                        </div>
                                        <span class="badge bg-danger"><?= $intento['intentos_fallidos'] ?> intentos</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted text-center py-3">
                                <i class="fas fa-check-circle me-2 text-success"></i>
                                No hay intentos fallidos recientes
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tercera Fila: Incidentes y Pagos -->
        <div class="row mt-4">
            <!-- Incidentes Recientes -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <i class="fas fa-tools me-2"></i>Incidentes Recientes
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                <tr>
                                    <th>Departamento</th>
                                    <th>Descripción</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach($incidentesRecientes as $incidente): ?>
                                    <tr>
                                        <td><strong><?= $incidente['departamento'] ?></strong></td>
                                        <td>
                                            <small title="<?= $incidente['descripcion'] ?>">
                                                <?= strlen($incidente['descripcion']) > 30 ? substr($incidente['descripcion'], 0, 30) . '...' : $incidente['descripcion'] ?>
                                            </small>
                                        </td>
                                        <td>
                                            <?php
                                            $badge_class = [
                                                    'pendiente' => 'bg-secondary',
                                                    'en_proceso' => 'bg-warning',
                                                    'resuelto' => 'bg-success',
                                                    'cancelado' => 'bg-danger'
                                            ][$incidente['estado']] ?? 'bg-secondary';
                                            ?>
                                            <span class="badge badge-estado <?= $badge_class ?>">
                                                <?= str_replace('_', ' ', $incidente['estado']) ?>
                                            </span>
                                        </td>
                                        <td><small><?= date('d/m/Y', strtotime($incidente['fecha_registro'])) ?></small></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Promedio de Pagos -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <i class="fas fa-chart-line me-2"></i>Top 10 - Promedio de Pagos por Departamento
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                <tr>
                                    <th>Departamento</th>
                                    <th>Promedio de Pago</th>
                                    <th>Facturas Pagadas</th>
                                    <th>Total Pagado</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach($promedioPagos as $pago): ?>
                                    <tr>
                                        <td><strong><?= $pago['departamento'] ?></strong></td>
                                        <td><strong class="text-success">Bs. <?= number_format($pago['promedio_pago'], 2) ?></strong></td>
                                        <td><span class="badge bg-primary"><?= $pago['facturas_pagadas'] ?></span></td>
                                        <td><small>Bs. <?= number_format($pago['total_pagado'], 2) ?></small></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cuarta Fila: Historial y Facturas -->
        <div class="row mt-4">
            <!-- Historial de Incidentes -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <i class="fas fa-history me-2"></i>Historial de Incidentes
                    </div>
                    <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead class="table-light">
                                <tr>
                                    <th>Incidente</th>
                                    <th>Persona</th>
                                    <th>Acción</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach($historialIncidentes as $historial): ?>
                                    <tr>
                                        <td><small>#<?= $historial['id_incidente'] ?></small></td>
                                        <td><small><?= $historial['persona'] ?></small></td>
                                        <td>
                                            <span class="badge bg-info"><?= $historial['accion'] ?></span>
                                        </td>
                                        <td>
                                            <?php if ($historial['estado_anterior'] && $historial['estado_nuevo']): ?>
                                                <small>
                                                    <i class="fas fa-arrow-right text-muted"></i>
                                                    <?= $historial['estado_anterior'] ?> → <?= $historial['estado_nuevo'] ?>
                                                </small>
                                            <?php endif; ?>
                                        </td>
                                        <td><small><?= date('H:i d/m', strtotime($historial['fecha_accion'])) ?></small></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Facturas Vencidas -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <i class="fas fa-exclamation-circle me-2"></i>Facturas Vencidas Recientes
                    </div>
                    <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead class="table-light">
                                <tr>
                                    <th>Departamento</th>
                                    <th>Servicio</th>
                                    <th>Monto</th>
                                    <th>Días Vencida</th>
                                    <th>Vencimiento</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach($facturasVencidas as $factura): ?>
                                    <tr>
                                        <td><strong><?= $factura['departamento'] ?></strong></td>
                                        <td>
                                            <span class="badge bg-<?= $factura['servicio'] == 'agua' ? 'primary' : ($factura['servicio'] == 'luz' ? 'warning' : 'danger') ?>">
                                                <?= ucfirst($factura['servicio']) ?>
                                            </span>
                                        </td>
                                        <td><strong>Bs. <?= number_format($factura['monto_total'], 2) ?></strong></td>
                                        <td>
                                            <span class="badge bg-danger"><?= $factura['dias_vencida'] ?> días</span>
                                        </td>
                                        <td><small><?= date('d/m/Y', strtotime($factura['fecha_vencimiento'])) ?></small></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include("../../includes/footer.php"); ?>
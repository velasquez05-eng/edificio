<?php
include("../../includes/header.php");

// Verificar que las variables estén definidas
$comunicados = $comunicados ?? [];
$consumoDiario = $consumoDiario ?? [];
$consumoMensual = $consumoMensual ?? [];
$facturas = $facturas ?? [];
$incidentes = $incidentes ?? [];
$reservas = $reservas ?? [];
$estadisticas = $estadisticas ?? [];
$departamento = $departamento ?? [];
$infoResidente = $infoResidente ?? [];

// Preparar datos para gráficos
function prepararDatosConsumoDiarioChart($consumoDiario) {
    $datos = ['labels' => [], 'datasets' => []];
    $servicios = [];
    $consumoPorServicio = [];

    foreach ($consumoDiario as $consumo) {
        $servicio = $consumo['servicio'];
        $fecha = date('d/m', strtotime($consumo['fecha']));

        if (!in_array($fecha, $datos['labels'])) {
            $datos['labels'][] = $fecha;
        }

        if (!in_array($servicio, $servicios)) {
            $servicios[] = $servicio;
        }

        $consumoPorServicio[$servicio][$fecha] = $consumo['consumo_diario'];
    }

    $colores = [
            'agua' => 'rgba(54, 162, 235, 0.8)',
            'luz' => 'rgba(255, 206, 86, 0.8)',
            'gas' => 'rgba(255, 99, 132, 0.8)'
    ];

    foreach ($servicios as $servicio) {
        $dataset = [
                'label' => ucfirst($servicio),
                'data' => [],
                'backgroundColor' => $colores[$servicio] ?? 'rgba(201, 203, 207, 0.8)',
                'borderColor' => $colores[$servicio] ?? 'rgba(201, 203, 207, 1)',
                'borderWidth' => 2,
                'fill' => false
        ];

        foreach ($datos['labels'] as $fecha) {
            $dataset['data'][] = $consumoPorServicio[$servicio][$fecha] ?? 0;
        }

        $datos['datasets'][] = $dataset;
    }

    return $datos;
}

function prepararDatosConsumoMensualChart($consumoMensual) {
    $datos = ['labels' => [], 'datasets' => []];
    $servicios = [];
    $consumoPorServicio = [];

    foreach ($consumoMensual as $consumo) {
        $servicio = $consumo['servicio'];
        $mes = $consumo['mes'] . '/' . substr($consumo['año'], 2, 2);

        if (!in_array($mes, $datos['labels'])) {
            $datos['labels'][] = $mes;
        }

        if (!in_array($servicio, $servicios)) {
            $servicios[] = $servicio;
        }

        $consumoPorServicio[$servicio][$mes] = $consumo['consumo_mensual'];
    }

    $colores = [
            'agua' => 'rgba(54, 162, 235, 0.8)',
            'luz' => 'rgba(255, 206, 86, 0.8)',
            'gas' => 'rgba(255, 99, 132, 0.8)'
    ];

    foreach ($servicios as $servicio) {
        $dataset = [
                'label' => ucfirst($servicio),
                'data' => [],
                'backgroundColor' => $colores[$servicio] ?? 'rgba(201, 203, 207, 0.8)',
                'borderColor' => $colores[$servicio] ?? 'rgba(201, 203, 207, 1)',
                'borderWidth' => 1
        ];

        foreach ($datos['labels'] as $mes) {
            $dataset['data'][] = $consumoPorServicio[$servicio][$mes] ?? 0;
        }

        $datos['datasets'][] = $dataset;
    }

    return $datos;
}

$consumoDiarioChart = prepararDatosConsumoDiarioChart($consumoDiario);
$consumoMensualChart = prepararDatosConsumoMensualChart($consumoMensual);

// Obtener datos del usuario desde la sesión
$nombre_usuario = $_SESSION['nombre'] ?? 'Residente';
$apellido_usuario = $_SESSION['apellido_paterno'] ?? '';
$nombre_completo = trim($nombre_usuario . ' ' . $apellido_usuario);
?>

    <!-- Page Header -->
    <div class="page-header fade-in">
        <div class="page-title">
            <h1>Dashboard del Residente</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Dashboard Residente</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Contenido Principal del Dashboard Residente -->
    <div class="container-fluid">

        <!-- Mensajes de alerta -->
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php echo htmlspecialchars($_GET['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo htmlspecialchars($_GET['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Información del Departamento -->
        <?php if ($departamento): ?>
            <div class="row mb-4">
                <div class="col-12">
                    <div class="alert alert-info">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-building me-2"></i>
                                <strong>Departamento:</strong> Número <?php echo htmlspecialchars($departamento['numero']); ?> -
                                Piso <?php echo htmlspecialchars($departamento['piso']); ?>
                            </div>
                            <div>
                                <strong>Bienvenido:</strong> <?php echo htmlspecialchars($nombre_completo); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Tarjetas de Resumen -->
        <div class="row mb-4">
            <!-- Facturas Pendientes -->
            <div class="col-xl-3 col-md-6">
                <div class="info-card bg-gradient-warning">
                    <div>
                        <h3><?php echo $estadisticas['total_facturas_pendientes'] ?? 0; ?></h3>
                        <p>Facturas Pendientes</p>
                    </div>
                    <div class="card-progress">
                        <div class="card-progress-bar" style="width: 100%"></div>
                    </div>
                    <i class="fas fa-file-invoice-dollar icon"></i>
                </div>
            </div>

            <!-- Reservas Activas -->
            <div class="col-xl-3 col-md-6">
                <div class="info-card bg-gradient-success">
                    <div>
                        <h3><?php echo $estadisticas['total_reservas_activas'] ?? 0; ?></h3>
                        <p>Reservas Activas</p>
                    </div>
                    <div class="card-progress">
                        <div class="card-progress-bar" style="width: 100%"></div>
                    </div>
                    <i class="fas fa-calendar-check icon"></i>
                </div>
            </div>

            <!-- Incidentes Reportados -->
            <div class="col-xl-3 col-md-6">
                <div class="info-card bg-gradient-info">
                    <div>
                        <h3><?php echo $estadisticas['total_incidentes_activos'] ?? 0; ?></h3>
                        <p>Incidentes Activos</p>
                    </div>
                    <div class="card-progress">
                        <div class="card-progress-bar" style="width: 100%"></div>
                    </div>
                    <i class="fas fa-exclamation-triangle icon"></i>
                </div>
            </div>

            <!-- Consumo Agua Actual -->
            <div class="col-xl-3 col-md-6">
                <div class="info-card bg-gradient-primary">
                    <div>
                        <h3><?php echo number_format($estadisticas['consumo_agua_actual'] ?? 0, 1); ?></h3>
                        <p>Consumo Agua (m³/día)</p>
                    </div>
                    <div class="card-progress">
                        <div class="card-progress-bar" style="width: 100%"></div>
                    </div>
                    <i class="fas fa-tint icon"></i>
                </div>
            </div>
        </div>

        <!-- Comunicados Recientes -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="content-box">
                    <div class="content-box-header d-flex justify-content-between align-items-center">
                        <h5>
                            <i class="fas fa-bullhorn me-2"></i>Comunicados Recientes
                        </h5>
                        <span class="badge bg-primary"><?php echo count($comunicados); ?> comunicados</span>
                    </div>
                    <div class="content-box-body">
                        <?php if (empty($comunicados)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-bullhorn fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No hay comunicados recientes</p>
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($comunicados as $comunicado): ?>
                                    <?php
                                    $prioridad_class = '';
                                    $badge_class = '';
                                    switch($comunicado['prioridad']) {
                                        case 'urgente':
                                            $prioridad_class = 'border-left-danger';
                                            $badge_class = 'bg-danger';
                                            break;
                                        case 'alta':
                                            $prioridad_class = 'border-left-warning';
                                            $badge_class = 'bg-warning';
                                            break;
                                        case 'media':
                                            $prioridad_class = 'border-left-info';
                                            $badge_class = 'bg-info';
                                            break;
                                        case 'baja':
                                            $prioridad_class = 'border-left-success';
                                            $badge_class = 'bg-success';
                                            break;
                                        default:
                                            $prioridad_class = 'border-left-secondary';
                                            $badge_class = 'bg-secondary';
                                    }
                                    ?>
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="card h-100 <?php echo $prioridad_class; ?>">
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0 text-truncate">
                                                    <?php echo htmlspecialchars($comunicado['titulo']); ?>
                                                </h6>
                                                <span class="badge <?php echo $badge_class; ?>">
                                                    <?php echo ucfirst($comunicado['prioridad']); ?>
                                                </span>
                                            </div>
                                            <div class="card-body">
                                                <p class="card-text small">
                                                    <?php
                                                    $contenido = htmlspecialchars($comunicado['contenido']);
                                                    echo strlen($contenido) > 100 ? substr($contenido, 0, 100) . '...' : $contenido;
                                                    ?>
                                                </p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted">
                                                        <i class="fas fa-user me-1"></i>
                                                        <?php echo htmlspecialchars($comunicado['autor']); ?>
                                                    </small>
                                                    <small class="text-muted">
                                                        <i class="fas fa-calendar me-1"></i>
                                                        <?php echo date('d/m/Y', strtotime($comunicado['fecha_publicacion'])); ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos de Consumo -->
        <div class="row mb-4">
            <!-- Consumo Diario -->
            <div class="col-lg-6 mb-4">
                <div class="content-box h-100">
                    <div class="content-box-header">
                        <h5><i class="fas fa-chart-line me-2"></i>Consumo Diario (Últimos 7 días)</h5>
                    </div>
                    <div class="content-box-body">
                        <canvas id="consumoDiarioChart" height="250"></canvas>
                    </div>
                </div>
            </div>

            <!-- Consumo Mensual -->
            <div class="col-lg-6 mb-4">
                <div class="content-box h-100">
                    <div class="content-box-header">
                        <h5><i class="fas fa-chart-bar me-2"></i>Consumo Mensual (Últimos 6 meses)</h5>
                    </div>
                    <div class="content-box-body">
                        <canvas id="consumoMensualChart" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Facturas y Reservas -->
        <div class="row mb-4">
            <!-- Facturas Recientes -->
            <div class="col-lg-6 mb-4">
                <div class="content-box h-100">
                    <div class="content-box-header d-flex justify-content-between align-items-center">
                        <h5><i class="fas fa-file-invoice-dollar me-2"></i>Mis Facturas</h5>
                        <span class="badge bg-primary"><?php echo count($facturas); ?> facturas</span>
                    </div>
                    <div class="content-box-body">
                        <?php if (empty($facturas)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-file-invoice-dollar fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No hay facturas registradas</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>Servicio</th>
                                        <th>Monto</th>
                                        <th>Vencimiento</th>
                                        <th>Estado</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach($facturas as $factura): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars(ucfirst($factura['servicio'])); ?></td>
                                            <td>S/ <?php echo number_format($factura['monto_total'], 2); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($factura['fecha_vencimiento'])); ?></td>
                                            <td>
                                                <?php
                                                $badge_class = '';
                                                switch($factura['estado_real']) {
                                                    case 'pagada':
                                                        $badge_class = 'bg-success';
                                                        break;
                                                    case 'vencida':
                                                        $badge_class = 'bg-danger';
                                                        break;
                                                    case 'pendiente':
                                                        $badge_class = 'bg-warning';
                                                        break;
                                                    default:
                                                        $badge_class = 'bg-secondary';
                                                }
                                                ?>
                                                <span class="badge <?php echo $badge_class; ?>">
                                                    <?php echo ucfirst($factura['estado_real']); ?>
                                                </span>
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

            <!-- Reservas Activas -->
            <div class="col-lg-6 mb-4">
                <div class="content-box h-100">
                    <div class="content-box-header d-flex justify-content-between align-items-center">
                        <h5><i class="fas fa-calendar-check me-2"></i>Mis Reservas</h5>
                        <span class="badge bg-primary"><?php echo count($reservas); ?> reservas</span>
                    </div>
                    <div class="content-box-body">
                        <?php if (empty($reservas)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-calendar-check fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No tienes reservas activas</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>Área</th>
                                        <th>Fecha</th>
                                        <th>Horario</th>
                                        <th>Estado</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach($reservas as $reserva): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($reserva['area_comun']); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($reserva['fecha_reserva'])); ?></td>
                                            <td>
                                                <?php echo date('H:i', strtotime($reserva['hora_inicio'])); ?> -
                                                <?php echo date('H:i', strtotime($reserva['hora_fin'])); ?>
                                            </td>
                                            <td>
                                                <?php
                                                $badge_class = '';
                                                switch($reserva['estado']) {
                                                    case 'confirmada':
                                                        $badge_class = 'bg-success';
                                                        break;
                                                    case 'pendiente':
                                                        $badge_class = 'bg-warning';
                                                        break;
                                                    case 'cancelada':
                                                        $badge_class = 'bg-danger';
                                                        break;
                                                    default:
                                                        $badge_class = 'bg-secondary';
                                                }
                                                ?>
                                                <span class="badge <?php echo $badge_class; ?>">
                                                    <?php echo ucfirst($reserva['estado']); ?>
                                                </span>
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

        <!-- Incidentes Reportados -->
        <div class="row">
            <div class="col-12">
                <div class="content-box">
                    <div class="content-box-header d-flex justify-content-between align-items-center">
                        <h5><i class="fas fa-exclamation-triangle me-2"></i>Mis Incidentes Reportados</h5>
                        <span class="badge bg-primary"><?php echo count($incidentes); ?> incidentes</span>
                    </div>
                    <div class="content-box-body">
                        <?php if (empty($incidentes)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-exclamation-triangle fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No has reportado incidentes</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>Descripción</th>
                                        <th>Fecha Reporte</th>
                                        <th>Personal Asignado</th>
                                        <th>Estado</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach($incidentes as $incidente): ?>
                                        <tr>
                                            <td>
                                                <?php
                                                $descripcion = htmlspecialchars($incidente['descripcion']);
                                                echo strlen($descripcion) > 50 ? substr($descripcion, 0, 50) . '...' : $descripcion;
                                                ?>
                                            </td>
                                            <td><?php echo date('d/m/Y', strtotime($incidente['fecha_registro'])); ?></td>
                                            <td>
                                                <?php echo $incidente['personal_asignado'] ? htmlspecialchars($incidente['personal_asignado']) : '<span class="text-muted">Sin asignar</span>'; ?>
                                            </td>
                                            <td>
                                                <?php
                                                $badge_class = '';
                                                $icon = '';
                                                switch($incidente['estado']) {
                                                    case 'pendiente':
                                                        $badge_class = 'bg-warning';
                                                        $icon = 'fa-clock';
                                                        break;
                                                    case 'en_proceso':
                                                        $badge_class = 'bg-info';
                                                        $icon = 'fa-cog';
                                                        break;
                                                    case 'resuelto':
                                                        $badge_class = 'bg-success';
                                                        $icon = 'fa-check';
                                                        break;
                                                    case 'cancelado':
                                                        $badge_class = 'bg-danger';
                                                        $icon = 'fa-times';
                                                        break;
                                                    default:
                                                        $badge_class = 'bg-secondary';
                                                        $icon = 'fa-question';
                                                }
                                                ?>
                                                <span class="badge <?php echo $badge_class; ?>">
                                                    <i class="fas <?php echo $icon; ?> me-1"></i>
                                                    <?php echo ucfirst(str_replace('_', ' ', $incidente['estado'])); ?>
                                                </span>
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
    </div>

    <!-- Scripts para Gráficos -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Datos para gráficos
        const consumoDiarioData = <?php echo json_encode($consumoDiarioChart); ?>;
        const consumoMensualData = <?php echo json_encode($consumoMensualChart); ?>;

        // Gráfico de Consumo Diario
        if (document.getElementById('consumoDiarioChart')) {
            const consumoDiarioCtx = document.getElementById('consumoDiarioChart').getContext('2d');
            new Chart(consumoDiarioCtx, {
                type: 'line',
                data: {
                    labels: consumoDiarioData.labels,
                    datasets: consumoDiarioData.datasets
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Consumo Diario por Servicio'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Consumo'
                            }
                        }
                    }
                }
            });
        }

        // Gráfico de Consumo Mensual
        if (document.getElementById('consumoMensualChart')) {
            const consumoMensualCtx = document.getElementById('consumoMensualChart').getContext('2d');
            new Chart(consumoMensualCtx, {
                type: 'bar',
                data: {
                    labels: consumoMensualData.labels,
                    datasets: consumoMensualData.datasets
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Consumo Mensual por Servicio'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Consumo'
                            }
                        }
                    }
                }
            });
        }
    </script>

    <!-- Estilos adicionales -->
    <style>
        .info-card.bg-gradient-warning {
            background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
            color: white;
        }

        .info-card.bg-gradient-success {
            background: linear-gradient(135deg, #67B26F 0%, #4ca2cd 100%);
            color: white;
        }

        .info-card.bg-gradient-info {
            background: linear-gradient(135deg, #4ca2cd 0%, #67B26F 100%);
            color: white;
        }

        .info-card.bg-gradient-primary {
            background: linear-gradient(135deg, #4A90E2 0%, #67B26F 100%);
            color: white;
        }

        .border-left-danger { border-left: 4px solid #dc3545 !important; }
        .border-left-warning { border-left: 4px solid #ffc107 !important; }
        .border-left-info { border-left: 4px solid #0dcaf0 !important; }
        .border-left-success { border-left: 4px solid #198754 !important; }
        .border-left-secondary { border-left: 4px solid #6c757d !important; }
    </style>

<?php include("../../includes/footer.php"); ?>
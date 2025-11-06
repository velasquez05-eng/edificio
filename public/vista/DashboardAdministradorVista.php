<?php
include("../../includes/header.php");

// Verificar que las variables estén definidas
$estadisticasGenerales = $estadisticasGenerales ?? [];
$metricasFinancieras = $metricasFinancieras ?? [];
$consumoMensualGeneral = $consumoMensualGeneral ?? [];
$estadisticasConsumoGeneral = $estadisticasConsumoGeneral ?? [];
$todosResidentes = $todosResidentes ?? [];
$todosIncidentes = $todosIncidentes ?? [];
$todasAreas = $todasAreas ?? [];
$todasReservas = $todasReservas ?? [];
$todosServicios = $todosServicios ?? [];
$departamentosSelector = $departamentosSelector ?? [];
$consumoDiarioDepartamento = $consumoDiarioDepartamento ?? [];

// Parámetros de filtro (si no están definidos desde el controlador, obtenerlos de GET)
$mes_filtro = $mes_filtro ?? ($_GET['mes'] ?? date('m'));
$anio_filtro = $anio_filtro ?? ($_GET['anio'] ?? date('Y'));
$departamento_filtro = $departamento_filtro ?? ($_GET['departamento'] ?? '');

// Preparar datos para gráficos
function prepararDatosConsumoMensualChart($consumoMensualGeneral) {
    $datos = ['labels' => [], 'datasets' => []];
    
    // Si no hay datos, retornar estructura vacía
    if (empty($consumoMensualGeneral)) {
        return $datos;
    }
    
    $servicios = []; // Cambiado de $departamentos a $servicios para claridad
    $consumoPorServicio = []; // Cambiado de $consumoPorDepartamento a $consumoPorServicio

    foreach ($consumoMensualGeneral as $consumo) {
        $departamento = 'Dpto ' . $consumo['departamento'] . ' - P' . $consumo['piso'];
        $servicio = $consumo['servicio'];

        if (!in_array($departamento, $datos['labels'])) {
            $datos['labels'][] = $departamento;
        }

        if (!in_array($servicio, $servicios)) {
            $servicios[] = $servicio;
        }

        $consumoPorServicio[$servicio][$departamento] = $consumo['consumo_mensual'] ?? 0;
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

        foreach ($datos['labels'] as $departamento) {
            $dataset['data'][] = $consumoPorServicio[$servicio][$departamento] ?? 0;
        }

        $datos['datasets'][] = $dataset;
    }

    return $datos;
}

function prepararDatosConsumoDiarioChart($consumoDiarioDepartamento) {
    $datos = ['labels' => [], 'datasets' => []];
    
    // Si no hay datos, retornar estructura vacía
    if (empty($consumoDiarioDepartamento)) {
        return $datos;
    }
    
    $servicios = [];
    $consumoPorServicio = [];

    foreach ($consumoDiarioDepartamento as $consumo) {
        $servicio = $consumo['servicio'] ?? '';
        $fecha_str = $consumo['fecha'] ?? '';
        
        if (empty($servicio) || empty($fecha_str)) {
            continue;
        }
        
        $fecha = date('d/m', strtotime($fecha_str));
        
        // Validar que la fecha sea válida
        if ($fecha === '01/01' || $fecha === false) {
            continue;
        }

        if (!in_array($fecha, $datos['labels'])) {
            $datos['labels'][] = $fecha;
        }

        if (!in_array($servicio, $servicios)) {
            $servicios[] = $servicio;
        }

        $consumoPorServicio[$servicio][$fecha] = $consumo['consumo_diario'] ?? 0;
    }
    
    // Ordenar las fechas
    usort($datos['labels'], function($a, $b) {
        $dateA = DateTime::createFromFormat('d/m', $a);
        $dateB = DateTime::createFromFormat('d/m', $b);
        if ($dateA === false || $dateB === false) {
            return 0;
        }
        return $dateA->getTimestamp() - $dateB->getTimestamp();
    });

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

$consumoMensualChart = prepararDatosConsumoMensualChart($consumoMensualGeneral);
$consumoDiarioChart = prepararDatosConsumoDiarioChart($consumoDiarioDepartamento);
?>

    <!-- Page Header -->
    <div class="page-header fade-in">
        <div class="page-title">
            <h1>Dashboard Administrador - SEINT</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Dashboard Administrador</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Mostrar mensajes -->
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
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Mes</label>
                                <select name="mes" class="form-select">
                                    <?php for ($i = 1; $i <= 12; $i++): ?>
                                        <option value="<?php echo $i; ?>" <?php echo $i == $mes_filtro ? 'selected' : ''; ?>>
                                            <?php echo DateTime::createFromFormat('!m', $i)->format('F'); ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Año</label>
                                <select name="anio" class="form-select">
                                    <?php for ($i = date('Y') - 1; $i <= date('Y') + 1; $i++): ?>
                                        <option value="<?php echo $i; ?>" <?php echo $i == $anio_filtro ? 'selected' : ''; ?>>
                                            <?php echo $i; ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Departamento (Consumo Diario)</label>
                                <select name="departamento" class="form-select">
                                    <option value="">Seleccionar departamento</option>
                                    <?php foreach ($departamentosSelector as $depto): ?>
                                        <option value="<?php echo $depto['id_departamento']; ?>"
                                                <?php echo (string)$depto['id_departamento'] === (string)$departamento_filtro ? 'selected' : ''; ?>>
                                            Dpto <?php echo $depto['numero']; ?> - Piso <?php echo $depto['piso']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas Generales -->
        <div class="row mb-4">
            <div class="col-xl-2 col-md-4">
                <div class="info-card bg-gradient-primary">
                    <div>
                        <h3><?php echo $estadisticasGenerales['total_residentes'] ?? 0; ?></h3>
                        <p>Residentes</p>
                    </div>
                    <i class="fas fa-users icon"></i>
                </div>
            </div>
            <div class="col-xl-2 col-md-4">
                <div class="info-card bg-gradient-success">
                    <div>
                        <h3><?php echo $estadisticasGenerales['total_departamentos'] ?? 0; ?></h3>
                        <p>Departamentos</p>
                    </div>
                    <i class="fas fa-building icon"></i>
                </div>
            </div>
            <div class="col-xl-2 col-md-4">
                <div class="info-card bg-gradient-info">
                    <div>
                        <h3><?php echo $estadisticasGenerales['total_personal'] ?? 0; ?></h3>
                        <p>Personal</p>
                    </div>
                    <i class="fas fa-user-tie icon"></i>
                </div>
            </div>
            <div class="col-xl-2 col-md-4">
                <div class="info-card bg-gradient-warning">
                    <div>
                        <h3><?php echo $estadisticasGenerales['total_incidentes_activos'] ?? 0; ?></h3>
                        <p>Incidentes Activos</p>
                    </div>
                    <i class="fas fa-exclamation-triangle icon"></i>
                </div>
            </div>
            <div class="col-xl-2 col-md-4">
                <div class="info-card bg-gradient-danger">
                    <div>
                        <h3>S/ <?php echo number_format($metricasFinancieras['deuda_total'] ?? 0, 2); ?></h3>
                        <p>Deuda Total</p>
                    </div>
                    <i class="fas fa-money-bill-wave icon"></i>
                </div>
            </div>
            <div class="col-xl-2 col-md-4">
                <div class="info-card bg-gradient-secondary">
                    <div>
                        <h3><?php echo $estadisticasGenerales['total_reservas_mes'] ?? 0; ?></h3>
                        <p>Reservas Mes</p>
                    </div>
                    <i class="fas fa-calendar-check icon"></i>
                </div>
            </div>
        </div>

        <!-- Gráficos de Consumo -->
        <div class="row mb-4">
            <!-- Consumo Mensual General -->
            <div class="col-lg-8 mb-4">
                <div class="content-box h-100">
                    <div class="content-box-header">
                        <h5><i class="fas fa-chart-bar me-2"></i>Consumo Mensual por Departamento</h5>
                    </div>
                    <div class="content-box-body">
                        <canvas id="consumoMensualChart" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- Estadísticas de Consumo -->
            <div class="col-lg-4 mb-4">
                <div class="content-box h-100">
                    <div class="content-box-header">
                        <h5><i class="fas fa-chart-pie me-2"></i>Estadísticas de Consumo</h5>
                    </div>
                    <div class="content-box-body">
                        <?php if (empty($estadisticasConsumoGeneral)): ?>
                            <div class="text-center py-4">
                                <p class="text-muted">No hay datos de consumo</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($estadisticasConsumoGeneral as $servicio): ?>
                                <div class="mb-3 p-3 border rounded">
                                    <h6 class="text-primary"><?php echo ucfirst($servicio['servicio']); ?></h6>
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <small>Promedio</small>
                                            <p class="fw-bold"><?php echo number_format($servicio['consumo_promedio'], 2); ?></p>
                                        </div>
                                        <div class="col-6">
                                            <small>Total</small>
                                            <p class="fw-bold"><?php echo number_format($servicio['consumo_total'], 2); ?></p>
                                        </div>
                                    </div>
                                    <small class="text-muted">
                                        <?php echo $servicio['departamentos_con_consumo']; ?> departamentos
                                    </small>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Consumo Diario por Departamento -->
        <?php if (!empty($departamento_filtro) && !empty($consumoDiarioDepartamento)): ?>
            <div class="row mb-4">
                <div class="col-12">
                    <div class="content-box">
                        <div class="content-box-header">
                            <h5><i class="fas fa-chart-line me-2"></i>Consumo Diario - Departamento Seleccionado</h5>
                        </div>
                        <div class="content-box-body">
                            <canvas id="consumoDiarioChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Información de Residentes -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="content-box">
                    <div class="content-box-header d-flex justify-content-between align-items-center">
                        <h5><i class="fas fa-users me-2"></i>Residentes</h5>
                        <span class="badge bg-primary"><?php echo count($todosResidentes); ?> residentes</span>
                    </div>
                    <div class="content-box-body">
                        <?php if (empty($todosResidentes)): ?>
                            <div class="text-center py-4">
                                <p class="text-muted">No hay residentes registrados</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>CI</th>
                                        <th>Email</th>
                                        <th>Teléfono</th>
                                        <th>Departamento</th>
                                        <th>Estado</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach($todosResidentes as $residente): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($residente['nombre'] . ' ' . $residente['apellido_paterno'] . ' ' . $residente['apellido_materno']); ?></td>
                                            <td><?php echo htmlspecialchars($residente['ci']); ?></td>
                                            <td><?php echo htmlspecialchars($residente['email']); ?></td>
                                            <td><?php echo htmlspecialchars($residente['telefono'] ?? 'No registrado'); ?></td>
                                            <td>Dpto <?php echo htmlspecialchars($residente['departamento']); ?> - Piso <?php echo htmlspecialchars($residente['piso']); ?></td>
                                            <td><span class="badge bg-success">Activo</span></td>
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

        <!-- Incidentes y Reservas -->
        <div class="row mb-4">
            <!-- Incidentes -->
            <div class="col-lg-6 mb-4">
                <div class="content-box h-100">
                    <div class="content-box-header d-flex justify-content-between align-items-center">
                        <h5><i class="fas fa-exclamation-triangle me-2"></i>Incidentes Recientes</h5>
                        <span class="badge bg-primary"><?php echo count($todosIncidentes); ?> incidentes</span>
                    </div>
                    <div class="content-box-body">
                        <?php if (empty($todosIncidentes)): ?>
                            <div class="text-center py-4">
                                <p class="text-muted">No hay incidentes registrados</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>Descripción</th>
                                        <th>Departamento</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach($todosIncidentes as $incidente): ?>
                                        <tr>
                                            <td>
                                                <?php
                                                $descripcion = htmlspecialchars($incidente['descripcion']);
                                                echo strlen($descripcion) > 30 ? substr($descripcion, 0, 30) . '...' : $descripcion;
                                                ?>
                                            </td>
                                            <td>Dpto <?php echo htmlspecialchars($incidente['departamento']); ?></td>
                                            <td>
                                                <?php
                                                $badge_class = '';
                                                switch($incidente['estado']) {
                                                    case 'pendiente': $badge_class = 'bg-warning'; break;
                                                    case 'en_proceso': $badge_class = 'bg-info'; break;
                                                    case 'resuelto': $badge_class = 'bg-success'; break;
                                                    default: $badge_class = 'bg-secondary';
                                                }
                                                ?>
                                                <span class="badge <?php echo $badge_class; ?>">
                                                    <?php echo ucfirst(str_replace('_', ' ', $incidente['estado'])); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('d/m/Y', strtotime($incidente['fecha_registro'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Reservas -->
            <div class="col-lg-6 mb-4">
                <div class="content-box h-100">
                    <div class="content-box-header d-flex justify-content-between align-items-center">
                        <h5><i class="fas fa-calendar-check me-2"></i>Reservas Próximas</h5>
                        <span class="badge bg-primary"><?php echo count($todasReservas); ?> reservas</span>
                    </div>
                    <div class="content-box-body">
                        <?php if (empty($todasReservas)): ?>
                            <div class="text-center py-4">
                                <p class="text-muted">No hay reservas próximas</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>Área</th>
                                        <th>Residente</th>
                                        <th>Fecha</th>
                                        <th>Horario</th>
                                        <th>Estado</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach($todasReservas as $reserva): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($reserva['area_comun']); ?></td>
                                            <td><?php echo htmlspecialchars($reserva['residente']); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($reserva['fecha_reserva'])); ?></td>
                                            <td>
                                                <?php echo date('H:i', strtotime($reserva['hora_inicio'])); ?> -
                                                <?php echo date('H:i', strtotime($reserva['hora_fin'])); ?>
                                            </td>
                                            <td>
                                                <?php
                                                $badge_class = '';
                                                switch($reserva['estado']) {
                                                    case 'confirmada': $badge_class = 'bg-success'; break;
                                                    case 'pendiente': $badge_class = 'bg-warning'; break;
                                                    default: $badge_class = 'bg-secondary';
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

        <!-- Áreas y Servicios -->
        <div class="row">
            <!-- Áreas Comunes -->
            <div class="col-lg-6 mb-4">
                <div class="content-box h-100">
                    <div class="content-box-header d-flex justify-content-between align-items-center">
                        <h5><i class="fas fa-map-marker-alt me-2"></i>Áreas Comunes</h5>
                        <span class="badge bg-primary"><?php echo count($todasAreas); ?> áreas</span>
                    </div>
                    <div class="content-box-body">
                        <?php if (empty($todasAreas)): ?>
                            <div class="text-center py-4">
                                <p class="text-muted">No hay áreas comunes registradas</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Capacidad</th>
                                        <th>Costo</th>
                                        <th>Estado</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach($todasAreas as $area): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($area['nombre']); ?></td>
                                            <td><?php echo $area['capacidad'] ?? 'N/A'; ?></td>
                                            <td>S/ <?php echo number_format($area['costo_reserva'], 2); ?></td>
                                            <td>
                                                <?php
                                                $badge_class = '';
                                                switch($area['estado']) {
                                                    case 'disponible': $badge_class = 'bg-success'; break;
                                                    case 'mantenimiento': $badge_class = 'bg-warning'; break;
                                                    default: $badge_class = 'bg-secondary';
                                                }
                                                ?>
                                                <span class="badge <?php echo $badge_class; ?>">
                                                    <?php echo ucfirst($area['estado']); ?>
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

            <!-- Servicios -->
            <div class="col-lg-6 mb-4">
                <div class="content-box h-100">
                    <div class="content-box-header d-flex justify-content-between align-items-center">
                        <h5><i class="fas fa-bolt me-2"></i>Servicios</h5>
                        <span class="badge bg-primary"><?php echo count($todosServicios); ?> servicios</span>
                    </div>
                    <div class="content-box-body">
                        <?php if (empty($todosServicios)): ?>
                            <div class="text-center py-4">
                                <p class="text-muted">No hay servicios registrados</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>Servicio</th>
                                        <th>Unidad</th>
                                        <th>Costo Unitario</th>
                                        <th>Estado</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach($todosServicios as $servicio): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars(ucfirst($servicio['nombre'])); ?></td>
                                            <td><?php echo htmlspecialchars($servicio['unidad_medida']); ?></td>
                                            <td>S/ <?php echo number_format($servicio['costo_unitario'], 2); ?></td>
                                            <td>
                                                <span class="badge bg-success">Activo</span>
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
        // Esperar a que Chart.js esté cargado
        document.addEventListener('DOMContentLoaded', function() {
            // Verificar que Chart esté disponible
            if (typeof Chart === 'undefined') {
                console.error('Chart.js no está cargado correctamente');
                return;
            }

            // Gráfico de Consumo Mensual
            const consumoMensualData = <?php echo json_encode($consumoMensualChart); ?>;
            const consumoMensualCanvas = document.getElementById('consumoMensualChart');
            
            if (consumoMensualCanvas) {
                try {
                    const ctx = consumoMensualCanvas.getContext('2d');
                    
                    // Verificar que hay datos para mostrar
                    if (consumoMensualData && consumoMensualData.labels && consumoMensualData.labels.length > 0 && 
                        consumoMensualData.datasets && consumoMensualData.datasets.length > 0) {
                        new Chart(ctx, {
                            type: 'bar',
                            data: consumoMensualData,
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    title: {
                                        display: true,
                                        text: 'Consumo Mensual por Departamento'
                                    },
                                    legend: {
                                        display: true,
                                        position: 'top'
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        title: {
                                            display: true,
                                            text: 'Consumo'
                                        }
                                    },
                                    x: {
                                        ticks: {
                                            maxRotation: 45,
                                            minRotation: 45
                                        }
                                    }
                                }
                            }
                        });
                    } else {
                        // Mostrar mensaje si no hay datos
                        consumoMensualCanvas.parentElement.innerHTML = '<div class="text-center py-4"><p class="text-muted">No hay datos de consumo mensual para mostrar</p></div>';
                    }
                } catch (error) {
                    console.error('Error al crear el gráfico de consumo mensual:', error);
                }
            }

            // Gráfico de Consumo Diario
            const consumoDiarioData = <?php echo json_encode($consumoDiarioChart); ?>;
            const consumoDiarioCanvas = document.getElementById('consumoDiarioChart');
            
            if (consumoDiarioCanvas && consumoDiarioData && consumoDiarioData.labels && consumoDiarioData.labels.length > 0) {
                try {
                    const ctx2 = consumoDiarioCanvas.getContext('2d');
                    
                    if (consumoDiarioData.datasets && consumoDiarioData.datasets.length > 0) {
                        new Chart(ctx2, {
                            type: 'line',
                            data: consumoDiarioData,
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    title: {
                                        display: true,
                                        text: 'Consumo Diario - Últimos 7 días'
                                    },
                                    legend: {
                                        display: true,
                                        position: 'top'
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
                    } else {
                        // Mostrar mensaje si no hay datos
                        consumoDiarioCanvas.parentElement.innerHTML = '<div class="text-center py-4"><p class="text-muted">No hay datos de consumo diario para mostrar</p></div>';
                    }
                } catch (error) {
                    console.error('Error al crear el gráfico de consumo diario:', error);
                }
            }
        });
    </script>

<?php include("../../includes/footer.php"); ?>
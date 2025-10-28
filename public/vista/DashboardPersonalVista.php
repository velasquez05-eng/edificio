<?php
include("../../includes/header.php");

// Verificar que las variables estén definidas
$comunicados = $comunicados ?? [];
$incidentesAsignados = $incidentesAsignados ?? [];
$estadisticasIncidentes = $estadisticasIncidentes ?? ['pendientes' => 0, 'atendidos' => 0, 'total' => 0];
$reservasConfirmadas = $reservasConfirmadas ?? [];
$residentes = $residentes ?? [];
$consumoServicios = $consumoServicios ?? [];
$consumoPorDepartamento = $consumoPorDepartamento ?? [];
$estadisticasConsumo = $estadisticasConsumo ?? [];
$infoPersonal = $infoPersonal ?? [];
$resumenActividades = $resumenActividades ?? [];

// Calcular porcentajes para las métricas
$porcentajeAtendidos = $estadisticasIncidentes['total'] > 0 ?
        round(($estadisticasIncidentes['atendidos'] / $estadisticasIncidentes['total']) * 100) : 0;
$porcentajePendientes = $estadisticasIncidentes['total'] > 0 ?
        round(($estadisticasIncidentes['pendientes'] / $estadisticasIncidentes['total']) * 100) : 0;
?>

    <!-- Page Header -->
    <div class="page-header fade-in">
        <div class="page-title">
            <h1>Dashboard Personal - SEINT</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Dashboard Personal</li>
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
        <!-- Resumen de Actividades -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="info-card bg-gradient-azul">
                    <div>
                        <h3><?php echo $resumenActividades['incidentes_asignados'] ?? 0; ?></h3>
                        <p>Incidentes Asignados</p>
                    </div>
                    <div class="card-progress">
                        <div class="card-progress-bar" style="width: 100%"></div>
                    </div>
                    <i class="fas fa-tasks icon"></i>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="info-card bg-gradient-verde">
                    <div>
                        <h3><?php echo $resumenActividades['incidentes_resueltos'] ?? 0; ?></h3>
                        <p>Incidentes Resueltos</p>
                    </div>
                    <div class="card-progress">
                        <div class="card-progress-bar" style="width: 100%"></div>
                    </div>
                    <i class="fas fa-check-circle icon"></i>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="info-card bg-gradient-naranja">
                    <div>
                        <h3><?php echo $resumenActividades['reservas_supervisadas'] ?? 0; ?></h3>
                        <p>Reservas del Mes</p>
                    </div>
                    <div class="card-progress">
                        <div class="card-progress-bar" style="width: 100%"></div>
                    </div>
                    <i class="fas fa-calendar-check icon"></i>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="info-card bg-gradient-celeste">
                    <div>
                        <h3><?php echo count($residentes); ?></h3>
                        <p>Residentes Activos</p>
                    </div>
                    <div class="card-progress">
                        <div class="card-progress-bar" style="width: 100%"></div>
                    </div>
                    <i class="fas fa-users icon"></i>
                </div>
            </div>
        </div>

        <!-- Comunicados y Incidentes Asignados -->
        <div class="row mb-4">
            <!-- Comunicados -->
            <div class="col-lg-6 mb-4">
                <div class="content-box h-100">
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
                            <div class="comunicados-list">
                                <?php foreach ($comunicados as $comunicado): ?>
                                    <?php
                                    // Determinar clase de color según prioridad
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
                                    <div class="comunicado-item <?php echo $prioridad_class; ?>">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="mb-0 text-truncate flex-grow-1" title="<?php echo htmlspecialchars($comunicado['titulo']); ?>">
                                                <?php echo htmlspecialchars($comunicado['titulo']); ?>
                                            </h6>
                                            <span class="badge <?php echo $badge_class; ?> ms-2">
                                                <?php echo ucfirst($comunicado['prioridad']); ?>
                                            </span>
                                        </div>
                                        <p class="text-muted small mb-2">
                                            <?php
                                            $contenido = htmlspecialchars($comunicado['contenido']);
                                            echo strlen($contenido) > 120 ? substr($contenido, 0, 120) . '...' : $contenido;
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
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Incidentes Asignados -->
            <div class="col-lg-6 mb-4">
                <div class="content-box h-100">
                    <div class="content-box-header d-flex justify-content-between align-items-center">
                        <h5>
                            <i class="fas fa-tools me-2"></i>Mis Incidentes Asignados
                        </h5>
                        <span class="badge bg-primary"><?php echo count($incidentesAsignados); ?> incidentes</span>
                    </div>
                    <div class="content-box-body">
                        <?php if (empty($incidentesAsignados)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-tools fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No tienes incidentes asignados</p>
                            </div>
                        <?php else: ?>
                            <div class="incidentes-list">
                                <?php foreach($incidentesAsignados as $incidente): ?>
                                    <div class="incidente-item">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">Departamento <?php echo htmlspecialchars($incidente['departamento']); ?> - Piso <?php echo htmlspecialchars($incidente['piso']); ?></h6>
                                                <p class="text-muted small mb-2">
                                                    <?php
                                                    $descripcion = htmlspecialchars($incidente['descripcion']);
                                                    echo strlen($descripcion) > 100 ? substr($descripcion, 0, 100) . '...' : $descripcion;
                                                    ?>
                                                </p>
                                            </div>
                                            <div class="text-end">
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
                                                    default:
                                                        $badge_class = 'bg-secondary';
                                                        $icon = 'fa-question';
                                                }
                                                ?>
                                                <span class="badge <?php echo $badge_class; ?> mb-2">
                                                    <i class="fas <?php echo $icon; ?> me-1"></i>
                                                    <?php echo ucfirst(str_replace('_', ' ', $incidente['estado'])); ?>
                                                </span>
                                                <br>
                                                <?php
                                                $prioridad_class = '';
                                                switch($incidente['prioridad']) {
                                                    case 'alta':
                                                        $prioridad_class = 'bg-danger';
                                                        break;
                                                    case 'media':
                                                        $prioridad_class = 'bg-warning';
                                                        break;
                                                    case 'baja':
                                                        $prioridad_class = 'bg-success';
                                                        break;
                                                    default:
                                                        $prioridad_class = 'bg-secondary';
                                                }
                                                ?>
                                                <span class="badge <?php echo $prioridad_class; ?>">
                                                    <?php echo ucfirst($incidente['prioridad']); ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="fas fa-user me-1"></i>
                                                <?php echo htmlspecialchars($incidente['residente']); ?>
                                            </small>
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>
                                                <?php echo date('d/m/Y', strtotime($incidente['fecha_registro'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Métricas de Incidentes -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="content-box">
                    <div class="content-box-header">
                        <h5><i class="fas fa-chart-pie me-2"></i>Estadísticas de Incidentes</h5>
                    </div>
                    <div class="content-box-body">
                        <div class="row text-center">
                            <div class="col-md-3 mb-3">
                                <div class="metric-card">
                                    <h3 class="text-primary"><?php echo $estadisticasIncidentes['total']; ?></h3>
                                    <p class="text-muted">Total Incidentes</p>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="metric-card">
                                    <h3 class="text-success"><?php echo $estadisticasIncidentes['atendidos']; ?></h3>
                                    <p class="text-muted">Atendidos (<?php echo $porcentajeAtendidos; ?>%)</p>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-success" style="width: <?php echo $porcentajeAtendidos; ?>%"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="metric-card">
                                    <h3 class="text-warning"><?php echo $estadisticasIncidentes['pendientes']; ?></h3>
                                    <p class="text-muted">Pendientes (<?php echo $porcentajePendientes; ?>%)</p>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-warning" style="width: <?php echo $porcentajePendientes; ?>%"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="metric-card">
                                    <h3 class="text-info"><?php echo $estadisticasIncidentes['en_proceso'] ?? 0; ?></h3>
                                    <p class="text-muted">En Proceso</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reservas Confirmadas -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="content-box">
                    <div class="content-box-header d-flex justify-content-between align-items-center">
                        <h5><i class="fas fa-calendar-check me-2"></i>Reservas Confirmadas del Mes</h5>
                        <span class="badge bg-primary"><?php echo count($reservasConfirmadas); ?> reservas</span>
                    </div>
                    <div class="content-box-body">
                        <?php if (empty($reservasConfirmadas)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-calendar-check fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No hay reservas confirmadas para este mes</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="reservasTable" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Área Común</th>
                                        <th>Residente</th>
                                        <th>Departamento</th>
                                        <th>Fecha</th>
                                        <th>Horario</th>
                                        <th>Estado</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach($reservasConfirmadas as $reserva): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($reserva['id_reserva']); ?></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($reserva['nombre_area']); ?></strong>
                                                <?php if (!empty($reserva['descripcion_area'])): ?>
                                                    <br><small class="text-muted"><?php echo htmlspecialchars($reserva['descripcion_area']); ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($reserva['nombre_residente']); ?></td>
                                            <td>
                                                <span class="badge bg-azul">
                                                    <?php echo htmlspecialchars($reserva['departamento']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <i class="fas fa-calendar me-2 text-muted"></i>
                                                <?php echo date('d/m/Y', strtotime($reserva['fecha_reserva'])); ?>
                                            </td>
                                            <td>
                                                <i class="fas fa-clock me-2 text-muted"></i>
                                                <?php echo date('H:i', strtotime($reserva['hora_inicio'])); ?> -
                                                <?php echo date('H:i', strtotime($reserva['hora_fin'])); ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i>Confirmada
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

        <!-- Información de Residentes -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="content-box">
                    <div class="content-box-header d-flex justify-content-between align-items-center">
                        <h5><i class="fas fa-users me-2"></i>Información General de Residentes</h5>
                        <span class="badge bg-primary"><?php echo count($residentes); ?> residentes</span>
                    </div>
                    <div class="content-box-body">
                        <?php if (empty($residentes)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No hay residentes registrados</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="residentesTable" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre Completo</th>
                                        <th>CI</th>
                                        <th>Email</th>
                                        <th>Teléfono</th>
                                        <th>Departamento</th>
                                        <th>Estado</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach($residentes as $residente): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($residente['id_persona']); ?></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($residente['nombre'] . ' ' . $residente['apellido_paterno'] . ' ' . $residente['apellido_materno']); ?></strong>
                                            </td>
                                            <td><?php echo htmlspecialchars($residente['ci']); ?></td>
                                            <td>
                                                <i class="fas fa-envelope me-2 text-muted"></i>
                                                <?php echo htmlspecialchars($residente['email']); ?>
                                            </td>
                                            <td>
                                                <i class="fas fa-phone me-2 text-muted"></i>
                                                <?php echo htmlspecialchars($residente['telefono'] ?? 'No registrado'); ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-azul">
                                                    <i class="fas fa-door-closed me-1"></i>
                                                    Dpto. <?php echo htmlspecialchars($residente['departamento']); ?> - Piso <?php echo htmlspecialchars($residente['piso']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i>Activo
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

        <!-- Consumo de Servicios por Departamento -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="content-box">
                    <div class="content-box-header d-flex justify-content-between align-items-center">
                        <h5><i class="fas fa-chart-bar me-2"></i>Consumo de Servicios por Departamento</h5>
                        <span class="badge bg-primary"><?php echo count($consumoPorDepartamento); ?> departamentos</span>
                    </div>
                    <div class="content-box-body">
                        <?php if (empty($consumoPorDepartamento)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No hay datos de consumo disponibles</p>
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach($consumoPorDepartamento as $departamento): ?>
                                    <div class="col-md-6 col-lg-4 mb-4">
                                        <div class="card h-100">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0">
                                                    <i class="fas fa-building me-2"></i>
                                                    Departamento <?php echo htmlspecialchars($departamento['departamento']); ?> - Piso <?php echo htmlspecialchars($departamento['piso']); ?>
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <?php foreach($departamento['servicios'] as $servicio): ?>
                                                    <div class="servicio-consumo mb-3">
                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                            <span class="fw-bold">
                                                                <?php echo ucfirst($servicio['servicio']); ?>
                                                            </span>
                                                            <span class="text-primary fw-bold">
                                                                <?php echo number_format($servicio['consumo_mensual'], 2); ?>
                                                                <?php echo htmlspecialchars($servicio['unidad_medida']); ?>
                                                            </span>
                                                        </div>
                                                        <div class="progress" style="height: 8px;">
                                                            <?php
                                                            $porcentaje = min(100, ($servicio['consumo_mensual'] / max(1, $servicio['consumo_mensual'])) * 100);
                                                            $bg_class = '';
                                                            switch($servicio['servicio']) {
                                                                case 'agua': $bg_class = 'bg-primary'; break;
                                                                case 'luz': $bg_class = 'bg-warning'; break;
                                                                case 'gas': $bg_class = 'bg-danger'; break;
                                                                default: $bg_class = 'bg-secondary';
                                                            }
                                                            ?>
                                                            <div class="progress-bar <?php echo $bg_class; ?>"
                                                                 style="width: <?php echo $porcentaje; ?>%">
                                                            </div>
                                                        </div>
                                                        <small class="text-muted">
                                                            Costo: $<?php echo number_format($servicio['costo_mensual'], 2); ?>
                                                            <?php if ($servicio['estado_medidor'] != 'activo'): ?>
                                                                <span class="badge bg-warning float-end">
                                                                    <?php echo ucfirst($servicio['estado_medidor']); ?>
                                                                </span>
                                                            <?php endif; ?>
                                                        </small>
                                                    </div>
                                                <?php endforeach; ?>
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

        <!-- Estadísticas de Consumo General -->
        <div class="row">
            <div class="col-12">
                <div class="content-box">
                    <div class="content-box-header d-flex justify-content-between align-items-center">
                        <h5><i class="fas fa-chart-line me-2"></i>Estadísticas de Consumo Mensual</h5>
                        <span class="badge bg-primary"><?php echo date('F Y'); ?></span>
                    </div>
                    <div class="content-box-body">
                        <?php if (empty($estadisticasConsumo)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No hay estadísticas de consumo disponibles</p>
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach($estadisticasConsumo as $servicio): ?>
                                    <div class="col-md-4 mb-3">
                                        <div class="card h-100">
                                            <div class="card-body text-center">
                                                <?php
                                                $icon_class = '';
                                                $bg_class = '';
                                                $text_class = '';
                                                switch($servicio['servicio']) {
                                                    case 'agua':
                                                        $icon_class = 'fa-tint';
                                                        $bg_class = 'bg-primary';
                                                        $text_class = 'text-primary';
                                                        break;
                                                    case 'luz':
                                                        $icon_class = 'fa-bolt';
                                                        $bg_class = 'bg-warning';
                                                        $text_class = 'text-warning';
                                                        break;
                                                    case 'gas':
                                                        $icon_class = 'fa-fire';
                                                        $bg_class = 'bg-danger';
                                                        $text_class = 'text-danger';
                                                        break;
                                                    default:
                                                        $icon_class = 'fa-chart-bar';
                                                        $bg_class = 'bg-secondary';
                                                        $text_class = 'text-secondary';
                                                }
                                                ?>
                                                <div class="mb-3">
                                                    <span class="badge <?php echo $bg_class; ?> p-3">
                                                        <i class="fas <?php echo $icon_class; ?> fa-2x"></i>
                                                    </span>
                                                </div>
                                                <h5 class="card-title"><?php echo ucfirst($servicio['servicio']); ?></h5>
                                                <h3 class="<?php echo $text_class; ?>"><?php echo number_format($servicio['consumo_promedio'], 2); ?></h3>
                                                <p class="text-muted"><?php echo htmlspecialchars($servicio['unidad_medida']); ?> promedio</p>

                                                <div class="row text-center mt-3">
                                                    <div class="col-6">
                                                        <small class="text-muted">Máximo</small>
                                                        <p class="fw-bold <?php echo $text_class; ?>"><?php echo number_format($servicio['consumo_maximo'], 2); ?></p>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="text-muted">Mínimo</small>
                                                        <p class="fw-bold <?php echo $text_class; ?>"><?php echo number_format($servicio['consumo_minimo'], 2); ?></p>
                                                    </div>
                                                </div>

                                                <div class="mt-2">
                                                    <small class="text-muted">
                                                        Total: <?php echo number_format($servicio['consumo_total'], 2); ?>
                                                        <?php echo htmlspecialchars($servicio['unidad_medida']); ?>
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
    </div>

    <!-- Scripts para DataTables y Gráficos -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        // Inicializar DataTables
        $(document).ready(function() {
            // Tabla de Reservas
            $('#reservasTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
                },
                "pageLength": 10,
                "responsive": true,
                "order": [[4, 'asc']] // Ordenar por fecha
            });

            // Tabla de Residentes
            $('#residentesTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
                },
                "pageLength": 10,
                "responsive": true,
                "order": [[1, 'asc']] // Ordenar por nombre
            });
        });

        // Animaciones de entrada
        document.addEventListener('DOMContentLoaded', function() {
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('fade-in');
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.content-box, .info-card, .card').forEach(el => {
                observer.observe(el);
            });
        });
    </script>

    <!-- Estilos adicionales -->
    <style>
        .info-card.bg-gradient-naranja {
            background: linear-gradient(135deg, #ff7e5f 0%, #feb47b 100%);
            color: white;
        }

        .info-card.bg-gradient-azul {
            background: linear-gradient(135deg, #4A90E2 0%, #67B26F 100%);
            color: white;
        }

        .info-card.bg-gradient-verde {
            background: linear-gradient(135deg, #67B26F 0%, #4ca2cd 100%);
            color: white;
        }

        .info-card.bg-gradient-celeste {
            background: linear-gradient(135deg, #4ca2cd 0%, #67B26F 100%);
            color: white;
        }

        /* Estilos para las listas de comunicados e incidentes */
        .comunicados-list .comunicado-item,
        .incidentes-list .incidente-item {
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
            transition: background-color 0.3s ease;
        }

        .comunicados-list .comunicado-item:hover,
        .incidentes-list .incidente-item:hover {
            background-color: #f8f9fa;
        }

        .comunicados-list .comunicado-item:last-child,
        .incidentes-list .incidente-item:last-child {
            border-bottom: none;
        }

        /* Bordes izquierdos para prioridad */
        .border-left-danger { border-left: 4px solid #dc3545 !important; }
        .border-left-warning { border-left: 4px solid #ffc107 !important; }
        .border-left-info { border-left: 4px solid #0dcaf0 !important; }
        .border-left-success { border-left: 4px solid #198754 !important; }
        .border-left-secondary { border-left: 4px solid #6c757d !important; }

        /* Tarjetas de métricas */
        .metric-card {
            padding: 20px;
            border-radius: 10px;
            background: #f8f9fa;
            transition: transform 0.3s ease;
        }

        .metric-card:hover {
            transform: translateY(-5px);
        }

        .servicio-consumo {
            padding: 10px;
            border-radius: 8px;
            background: #f8f9fa;
            margin-bottom: 10px;
        }

        .dataTables_wrapper .dataTables_filter input {
            border: 2px solid #e9ecef;
            border-radius: 5px;
            padding: 5px 10px;
            margin-left: 10px;
        }

        .dataTables_wrapper .dataTables_length select {
            border: 2px solid #e9ecef;
            border-radius: 5px;
            padding: 5px;
        }

        .bg-azul {
            background-color: var(--azul) !important;
        }

        .text-verde {
            color: var(--verde) !important;
        }

        .text-celeste {
            color: var(--celeste) !important;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .content-box-header {
                flex-direction: column;
                align-items: flex-start !important;
            }

            .content-box-header .badge {
                margin-top: 10px;
            }
        }
    </style>

<?php include("../../includes/footer.php"); ?>
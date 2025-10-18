<?php
include("../../includes/header.php");

// Verificar que las variables estén definidas
$comunicados = $comunicados ?? [];
$incidentesAsignados = $incidentesAsignados ?? [];
$estadisticasIncidentes = $estadisticasIncidentes ?? ['pendientes' => 0, 'atendidos' => 0, 'total' => 0];
$reservasConfirmadas = $reservasConfirmadas ?? [];
$residentes = $residentes ?? [];
$consumoServicios = $consumoServicios ?? [];
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
        <!-- Comunicados -->
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
                                    // Determinar clase de color según prioridad
                                    $prioridad_class = '';
                                    switch($comunicado['prioridad']) {
                                        case 'urgente':
                                            $prioridad_class = 'border-danger';
                                            $icon_class = 'text-danger';
                                            $badge_class = 'bg-danger';
                                            break;
                                        case 'alta':
                                            $prioridad_class = 'border-warning';
                                            $icon_class = 'text-warning';
                                            $badge_class = 'bg-warning';
                                            break;
                                        case 'media':
                                            $prioridad_class = 'border-info';
                                            $icon_class = 'text-info';
                                            $badge_class = 'bg-info';
                                            break;
                                        case 'baja':
                                            $prioridad_class = 'border-success';
                                            $icon_class = 'text-success';
                                            $badge_class = 'bg-success';
                                            break;
                                        default:
                                            $prioridad_class = 'border-secondary';
                                            $icon_class = 'text-secondary';
                                            $badge_class = 'bg-secondary';
                                    }
                                    ?>
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="card h-100 <?php echo $prioridad_class; ?>">
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0 text-truncate" title="<?php echo htmlspecialchars($comunicado['titulo']); ?>">
                                                    <?php echo htmlspecialchars($comunicado['titulo']); ?>
                                                </h6>
                                                <span class="badge <?php echo $badge_class; ?>">
                                                    <?php echo ucfirst($comunicado['prioridad']); ?>
                                                </span>
                                            </div>
                                            <div class="card-body">
                                                <p class="card-text">
                                                    <?php
                                                    $contenido = htmlspecialchars($comunicado['contenido']);
                                                    echo strlen($contenido) > 100 ? substr($contenido, 0, 100) . '...' : $contenido;
                                                    ?>
                                                </p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted">
                                                        <i class="fas fa-calendar me-1"></i>
                                                        <?php echo date('d/m/Y', strtotime($comunicado['fecha_publicacion'])); ?>
                                                    </small>
                                                    <small class="text-muted">
                                                        <i class="fas fa-users me-1"></i>
                                                        <?php echo htmlspecialchars($comunicado['tipo_audiencia']); ?>
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

        <!-- Métricas de Incidentes -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="info-card bg-gradient-celeste">
                    <div>
                        <h3><?php echo $estadisticasIncidentes['total']; ?></h3>
                        <p>Total Incidentes</p>
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
                        <h3><?php echo $estadisticasIncidentes['atendidos']; ?></h3>
                        <p>Atendidos</p>
                    </div>
                    <div class="card-progress">
                        <div class="card-progress-bar" style="width: <?php echo $estadisticasIncidentes['total'] > 0 ? ($estadisticasIncidentes['atendidos'] / $estadisticasIncidentes['total']) * 100 : 0; ?>%"></div>
                    </div>
                    <i class="fas fa-check-circle icon"></i>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="info-card bg-gradient-naranja">
                    <div>
                        <h3><?php echo $estadisticasIncidentes['pendientes']; ?></h3>
                        <p>Pendientes</p>
                    </div>
                    <div class="card-progress">
                        <div class="card-progress-bar" style="width: <?php echo $estadisticasIncidentes['total'] > 0 ? ($estadisticasIncidentes['pendientes'] / $estadisticasIncidentes['total']) * 100 : 0; ?>%"></div>
                    </div>
                    <i class="fas fa-clock icon"></i>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="info-card bg-gradient-azul">
                    <div>
                        <h3><?php echo count($reservasConfirmadas); ?></h3>
                        <p>Reservas Confirmadas</p>
                    </div>
                    <div class="card-progress">
                        <div class="card-progress-bar" style="width: 100%"></div>
                    </div>
                    <i class="fas fa-calendar-check icon"></i>
                </div>
            </div>
        </div>

        <!-- Incidentes Asignados -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="content-box">
                    <div class="content-box-header d-flex justify-content-between align-items-center">
                        <h5>Mis Incidentes Asignados</h5>
                        <span class="badge bg-primary"><?php echo count($incidentesAsignados); ?> incidentes</span>
                    </div>
                    <div class="content-box-body">
                        <?php if (empty($incidentesAsignados)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-tools fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No tienes incidentes asignados</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="incidentesTable" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Departamento</th>
                                        <th>Tipo</th>
                                        <th>Descripción</th>
                                        <th>Fecha Reporte</th>
                                        <th>Estado</th>
                                        <th>Prioridad</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach($incidentesAsignados as $incidente): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($incidente['id_incidente']); ?></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($incidente['departamento']); ?></strong>
                                            </td>
                                            <td>
                                                    <span class="badge bg-secondary">
                                                        <?php echo htmlspecialchars($incidente['tipo_incidente']); ?>
                                                    </span>
                                            </td>
                                            <td>
                                                <?php
                                                $descripcion = htmlspecialchars($incidente['descripcion']);
                                                echo strlen($descripcion) > 50 ? substr($descripcion, 0, 50) . '...' : $descripcion;
                                                ?>
                                            </td>
                                            <td>
                                                <i class="fas fa-calendar me-2 text-muted"></i>
                                                <?php echo date('d/m/Y', strtotime($incidente['fecha_registro'])); ?>
                                            </td>
                                            <td>
                                                <?php
                                                $badge_class = '';
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
                                            <td>
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

        <!-- Reservas Confirmadas -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="content-box">
                    <div class="content-box-header d-flex justify-content-between align-items-center">
                        <h5>Reservas Confirmadas</h5>
                        <span class="badge bg-primary"><?php echo count($reservasConfirmadas); ?> reservas</span>
                    </div>
                    <div class="content-box-body">
                        <?php if (empty($reservasConfirmadas)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-calendar-check fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No hay reservas confirmadas</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="reservasTable" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Área Común</th>
                                        <th>Residente</th>
                                        <th>Fecha</th>
                                        <th>Hora Inicio</th>
                                        <th>Hora Fin</th>
                                        <th>Estado</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach($reservasConfirmadas as $reserva): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($reserva['id_reserva']); ?></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($reserva['nombre_area']); ?></strong>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($reserva['nombre_residente'] . ' ' . $reserva['apellido_residente']); ?>
                                            </td>
                                            <td>
                                                <i class="fas fa-calendar me-2 text-muted"></i>
                                                <?php echo date('d/m/Y', strtotime($reserva['fecha_reserva'])); ?>
                                            </td>
                                            <td>
                                                <i class="fas fa-clock me-2 text-muted"></i>
                                                <?php echo date('H:i', strtotime($reserva['hora_inicio'])); ?>
                                            </td>
                                            <td>
                                                <i class="fas fa-clock me-2 text-muted"></i>
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
                        <h5>Información General de Residentes</h5>
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
                                        <th>Email</th>
                                        <th>Teléfono</th>
                                        <th>Departamento</th>
                                        <th>Estado</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach($residentes as $residente): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($residente['id_residente']); ?></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($residente['nombre'] . ' ' . $residente['apellido']); ?></strong>
                                            </td>
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
                                                        <?php echo htmlspecialchars($residente['departamento']); ?>
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

        <!-- Estadísticas de Consumo -->
        <div class="row">
            <div class="col-12">
                <div class="content-box">
                    <div class="content-box-header d-flex justify-content-between align-items-center">
                        <h5>Estadísticas de Consumo Mensual</h5>
                        <span class="badge bg-primary"><?php echo date('F Y'); ?></span>
                    </div>
                    <div class="content-box-body">
                        <?php if (empty($consumoServicios)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No hay datos de consumo disponibles</p>
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach($consumoServicios as $servicio): ?>
                                    <div class="col-md-4 mb-3">
                                        <div class="card h-100">
                                            <div class="card-body text-center">
                                                <?php
                                                $icon_class = '';
                                                $bg_class = '';
                                                switch($servicio['servicio']) {
                                                    case 'agua':
                                                        $icon_class = 'fa-tint text-primary';
                                                        $bg_class = 'bg-primary';
                                                        break;
                                                    case 'luz':
                                                        $icon_class = 'fa-bolt text-warning';
                                                        $bg_class = 'bg-warning';
                                                        break;
                                                    case 'gas':
                                                        $icon_class = 'fa-fire text-danger';
                                                        $bg_class = 'bg-danger';
                                                        break;
                                                    default:
                                                        $icon_class = 'fa-chart-bar text-secondary';
                                                        $bg_class = 'bg-secondary';
                                                }
                                                ?>
                                                <div class="mb-3">
                                                    <span class="badge <?php echo $bg_class; ?> p-2">
                                                        <i class="fas <?php echo $icon_class; ?> fa-2x"></i>
                                                    </span>
                                                </div>
                                                <h5 class="card-title"><?php echo ucfirst($servicio['servicio']); ?></h5>
                                                <h3 class="text-primary"><?php echo number_format($servicio['consumo'], 2); ?></h3>
                                                <p class="text-muted"><?php echo htmlspecialchars($servicio['unidad_medida']); ?></p>
                                                <div class="progress mb-2">
                                                    <div class="progress-bar <?php echo $bg_class; ?>"
                                                         role="progressbar"
                                                         style="width: <?php echo min(100, ($servicio['consumo'] / max(1, $servicio['consumo_max'])) * 100); ?>%"
                                                         aria-valuenow="<?php echo $servicio['consumo']; ?>"
                                                         aria-valuemin="0"
                                                         aria-valuemax="<?php echo $servicio['consumo_max']; ?>">
                                                    </div>
                                                </div>
                                                <small class="text-muted">
                                                    Consumo promedio: <?php echo number_format($servicio['consumo_promedio'], 2); ?>
                                                    <?php echo htmlspecialchars($servicio['unidad_medida']); ?>
                                                </small>
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

    <!-- Scripts para DataTables -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        // Inicializar DataTables
        $(document).ready(function() {
            // Tabla de Incidentes
            $('#incidentesTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
                },
                "pageLength": 10,
                "responsive": true,
                "order": [[0, 'desc']]
            });

            // Tabla de Reservas
            $('#reservasTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
                },
                "pageLength": 10,
                "responsive": true,
                "order": [[0, 'desc']]
            });

            // Tabla de Residentes
            $('#residentesTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
                },
                "pageLength": 10,
                "responsive": true,
                "order": [[0, 'desc']]
            });
        });

        // Asegurar que las animaciones funcionen
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

            document.querySelectorAll('.content-box, .info-card').forEach(el => {
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

        .dataTables_info {
            color: #6c757d;
            padding-top: 10px !important;
        }

        .dataTables_paginate .paginate_button {
            border-radius: 5px !important;
            margin: 0 2px;
            padding: 5px 10px !important;
        }

        .dataTables_paginate .paginate_button.current {
            background: var(--verde) !important;
            border-color: var(--verde) !important;
            color: white !important;
        }

        .dataTables_paginate .paginate_button:hover {
            background: var(--azul) !important;
            border-color: var(--azul) !important;
            color: white !important;
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

        /* Estilos para las tarjetas de comunicados */
        .card.border-danger {
            border-left: 4px solid #dc3545 !important;
        }

        .card.border-warning {
            border-left: 4px solid #ffc107 !important;
        }

        .card.border-info {
            border-left: 4px solid #0dcaf0 !important;
        }

        .card.border-success {
            border-left: 4px solid #198754 !important;
        }

        .card.border-secondary {
            border-left: 4px solid #6c757d !important;
        }
    </style>

<?php include("../../includes/footer.php"); ?>
<?php include("../../includes/header.php"); ?>

    <!-- Page Header -->
    <div class="page-header fade-in">
        <div class="page-title">
            <h1>Historial - Incidente #<?php echo htmlspecialchars($incidente['id_incidente']); ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                    <?php if ($_SESSION['id_rol'] == '1'): ?>
                        <li class="breadcrumb-item"><a href="IncidenteControlador.php?action=listarIncidentes">Incidentes</a></li>
                    <?php else: ?>
                        <li class="breadcrumb-item"><a href="IncidenteControlador.php?action=verMisIncidentes">Mis Incidentes</a></li>
                    <?php endif; ?>
                    <li class="breadcrumb-item active">Historial</li>
                </ol>
            </nav>
        </div>
        <div class="page-actions">
            <?php if ($_SESSION['id_rol'] == '1'): ?>
                <a href="IncidenteControlador.php?action=listarIncidentes" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Volver
                </a>
            <?php else: ?>
                <a href="IncidenteControlador.php?action=verMisIncidentes" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Volver
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="row fade-in">
        <!-- Información General -->
        <div class="col-lg-4 mb-4">
            <div class="content-box h-100">
                <div class="content-box-header">
                    <h6><i class="fas fa-info-circle me-2"></i>Información General</h6>
                </div>
                <div class="content-box-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <i class="fas fa-hashtag text-primary"></i>
                            <div>
                                <small class="text-muted">ID</small>
                                <div class="fw-bold">#<?php echo htmlspecialchars($incidente['id_incidente']); ?></div>
                            </div>
                        </div>

                        <div class="info-item">
                            <i class="fas fa-building text-primary"></i>
                            <div>
                                <small class="text-muted">Departamento</small>
                                <div class="fw-bold"><?php echo htmlspecialchars($incidente['numero_departamento']); ?></div>
                            </div>
                        </div>

                        <div class="info-item">
                            <i class="fas fa-user text-info"></i>
                            <div>
                                <small class="text-muted">Residente</small>
                                <div class="fw-bold"><?php echo htmlspecialchars($incidente['residente_nombre']); ?></div>
                            </div>
                        </div>

                        <div class="info-item">
                            <i class="fas fa-tag text-success"></i>
                            <div>
                                <small class="text-muted">Tipo</small>
                                <div>
                                    <span class="badge bg-<?php echo $incidente['tipo'] == 'interno' ? 'primary' : 'secondary'; ?>">
                                        <?php echo ucfirst($incidente['tipo']); ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="info-item">
                            <i class="fas fa-toggle-on text-warning"></i>
                            <div>
                                <small class="text-muted">Estado</small>
                                <div>
                                    <?php
                                    $badge_class = '';
                                    switch($incidente['estado']) {
                                        case 'pendiente': $badge_class = 'bg-warning'; break;
                                        case 'en_proceso': $badge_class = 'bg-info'; break;
                                        case 'resuelto': $badge_class = 'bg-success'; break;
                                        case 'cancelado': $badge_class = 'bg-danger'; break;
                                        default: $badge_class = 'bg-secondary';
                                    }
                                    ?>
                                    <span class="badge <?php echo $badge_class; ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $incidente['estado'])); ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="info-item">
                            <i class="fas fa-map-marker-alt text-warning"></i>
                            <div>
                                <small class="text-muted">Ubicación</small>
                                <div>
                                    <?php if (!empty($incidente['nombre_area'])): ?>
                                        <span class="badge bg-warning">
                                            <?php echo htmlspecialchars($incidente['nombre_area']); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Departamento</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="info-item">
                            <i class="fas fa-calendar text-success"></i>
                            <div>
                                <small class="text-muted">Fecha Registro</small>
                                <div class="fw-bold"><?php echo date('d/m/Y H:i', strtotime($incidente['fecha_registro'])); ?></div>
                            </div>
                        </div>

                        <?php if ($incidente['tipo'] == 'externo' && !empty($incidente['costo_externo'])): ?>
                            <div class="info-item">
                                <i class="fas fa-money-bill-wave text-success"></i>
                                <div>
                                    <small class="text-muted">Costo</small>
                                    <div class="fw-bold text-success">Bs. <?php echo number_format($incidente['costo_externo'], 2); ?></div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mt-4">
                        <h6 class="text-muted mb-3"><i class="fas fa-align-left me-2"></i>Descripción</h6>
                        <div class="p-3 bg-light rounded">
                            <p class="mb-0 small"><?php echo htmlspecialchars($incidente['descripcion']); ?></p>
                        </div>
                    </div>

                    <?php if (!empty($incidente['descripcion_detallada'])): ?>
                        <div class="mt-3">
                            <h6 class="text-muted mb-3"><i class="fas fa-file-alt me-2"></i>Detalles</h6>
                            <div class="p-3 bg-light rounded scrollable-details">
                                <small><?php echo nl2br(htmlspecialchars($incidente['descripcion_detallada'])); ?></small>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Historial -->
        <div class="col-lg-8">
            <div class="content-box h-100">
                <div class="content-box-header d-flex justify-content-between align-items-center">
                    <h6><i class="fas fa-history me-2"></i>Historial de Actividades</h6>
                    <span class="badge bg-primary"><?php echo count($historial); ?></span>
                </div>
                <div class="content-box-body p-0">
                    <?php if (empty($historial)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-history fa-2x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No hay actividades registradas</p>
                        </div>
                    <?php else: ?>
                        <div class="history-scroll">
                            <?php foreach ($historial as $registro): ?>
                                <div class="history-item">
                                    <div class="history-icon">
                                        <?php
                                        $icon = '';
                                        $color = '';
                                        switch($registro['accion']) {
                                            case 'creacion': $icon = 'fa-plus'; $color = 'success'; break;
                                            case 'asignacion': $icon = 'fa-user-tie'; $color = 'info'; break;
                                            case 'inicio_atencion': $icon = 'fa-play'; $color = 'primary'; break;
                                            case 'actualizacion': $icon = 'fa-edit'; $color = 'warning'; break;
                                            case 'resolucion': $icon = 'fa-check'; $color = 'success'; break;
                                            case 'cancelacion': $icon = 'fa-times'; $color = 'danger'; break;
                                            case 'reasignacion': $icon = 'fa-sync-alt'; $color = 'secondary'; break;
                                            default: $icon = 'fa-circle'; $color = 'secondary';
                                        }
                                        ?>
                                        <div class="icon-wrapper bg-<?php echo $color; ?>">
                                            <i class="fas <?php echo $icon; ?>"></i>
                                        </div>
                                    </div>
                                    <div class="history-content">
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <h6 class="mb-0 text-<?php echo $color; ?> fw-semibold">
                                                <?php echo ucfirst($registro['accion']); ?>
                                            </h6>
                                            <small class="text-muted">
                                                <?php echo date('H:i', strtotime($registro['fecha_accion'])); ?>
                                            </small>
                                        </div>

                                        <div class="history-meta mb-1">
                                            <small class="text-muted">
                                                <i class="fas fa-user me-1"></i>
                                                <?php echo htmlspecialchars($registro['nombre_completo']); ?>
                                                • <?php echo date('d/m/Y', strtotime($registro['fecha_accion'])); ?>
                                            </small>
                                        </div>

                                        <?php if ($registro['estado_anterior'] || $registro['estado_nuevo']): ?>
                                            <div class="state-change mb-1">
                                                <div class="d-flex align-items-center gap-1">
                                                    <?php if ($registro['estado_anterior']): ?>
                                                        <span class="badge bg-secondary">
                                                            <?php echo ucfirst(str_replace('_', ' ', $registro['estado_anterior'])); ?>
                                                        </span>
                                                        <i class="fas fa-arrow-right text-muted" style="font-size: 0.7rem;"></i>
                                                    <?php endif; ?>
                                                    <?php if ($registro['estado_nuevo']): ?>
                                                        <span class="badge bg-primary">
                                                            <?php echo ucfirst(str_replace('_', ' ', $registro['estado_nuevo'])); ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($registro['observacion'])): ?>
                                            <div class="observations">
                                                <div class="p-2 bg-light rounded mt-1">
                                                    <small class="text-muted"><?php echo nl2br(htmlspecialchars($registro['observacion'])); ?></small>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <style>
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 0;
        }

        .info-item i {
            width: 16px;
            text-align: center;
            font-size: 0.9rem;
        }

        .history-scroll {
            max-height: 600px;
            overflow-y: auto;
            padding: 0.75rem;
        }

        /* Scroll personalizado más delgado */
        .history-scroll::-webkit-scrollbar {
            width: 4px;
        }

        .history-scroll::-webkit-scrollbar-track {
            background: #f8f9fa;
            border-radius: 4px;
        }

        .history-scroll::-webkit-scrollbar-thumb {
            background: #dee2e6;
            border-radius: 4px;
        }

        .history-scroll::-webkit-scrollbar-thumb:hover {
            background: #adb5bd;
        }

        .scrollable-details {
            max-height: 120px;
            overflow-y: auto;
            font-size: 0.8rem;
        }

        .scrollable-details::-webkit-scrollbar {
            width: 3px;
        }

        /* Filas más delgadas */
        .history-item {
            display: flex;
            gap: 0.75rem;
            padding: 0.75rem;
            border-bottom: 1px solid #f1f3f4;
            transition: background-color 0.15s;
        }

        .history-item:last-child {
            border-bottom: none;
        }

        .history-item:hover {
            background-color: #fafbfc;
        }

        .history-icon {
            flex-shrink: 0;
            margin-top: 2px;
        }

        .icon-wrapper {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.8rem;
        }

        .history-content {
            flex: 1;
            min-width: 0;
        }

        .history-content h6 {
            font-size: 0.9rem;
            font-weight: 600;
        }

        .history-meta {
            font-size: 0.75rem;
        }

        .state-change {
            font-size: 0.75rem;
        }

        .observations {
            font-size: 0.75rem;
        }

        .observations .bg-light {
            background-color: #f8f9fa !important;
            border: 1px solid #e9ecef;
        }

        /* Badges más pequeños */
        .badge {
            font-size: 0.7rem;
            padding: 0.25em 0.5em;
            font-weight: 500;
        }

        /* Colores mantenidos */
        .bg-success { background-color: #28a745 !important; }
        .bg-info { background-color: #17a2b8 !important; }
        .bg-primary { background-color: #007bff !important; }
        .bg-warning { background-color: #ffc107 !important; }
        .bg-danger { background-color: #dc3545 !important; }
        .bg-secondary { background-color: #6c757d !important; }

        .text-success { color: #28a745 !important; }
        .text-info { color: #17a2b8 !important; }
        .text-primary { color: #007bff !important; }
        .text-warning { color: #ffc107 !important; }
        .text-danger { color: #dc3545 !important; }

        .badge.bg-warning {
            background-color: #ffc107 !important;
            color: #212529;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .info-grid {
                grid-template-columns: 1fr;
            }

            .history-item {
                padding: 0.6rem 0.5rem;
                gap: 0.6rem;
            }

            .icon-wrapper {
                width: 28px;
                height: 28px;
                font-size: 0.7rem;
            }
        }

        /* Mejor espaciado general */
        .content-box-body {
            padding: 1rem;
        }

        .fw-semibold {
            font-weight: 600;
        }
    </style>

<?php include("../../includes/footer.php"); ?>
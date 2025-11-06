<?php
// public/vista/ListarComunicadosPublicadosVista.php
include("../../includes/header.php");
?>

    <!-- Page Header -->
    <div class="page-header fade-in">
        <div class="page-title">
            <h1>Comunicados Publicados</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Comunicados Publicados</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Lista de Comunicados Publicados -->
    <div class="row fade-in">
        <div class="col-12">
            <?php if (empty($comunicados)): ?>
                <div class="content-box">
                    <div class="content-box-body text-center py-5">
                        <i class="fas fa-bullhorn fa-4x text-muted mb-4"></i>
                        <h4 class="text-muted mb-3">No hay comunicados publicados</h4>
                        <p class="text-muted">Actualmente no hay comunicados disponibles para mostrar.</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($comunicados as $comunicado): ?>
                        <?php
                        // Determinar clases según prioridad
                        $prioridad_class = '';
                        $badge_class = '';
                        $icon = '';
                        switch($comunicado['prioridad']) {
                            case 'urgente':
                                $prioridad_class = 'border-left-danger';
                                $badge_class = 'bg-danger';
                                $icon = 'fa-exclamation-triangle';
                                break;
                            case 'alta':
                                $prioridad_class = 'border-left-warning';
                                $badge_class = 'bg-warning';
                                $icon = 'fa-exclamation-circle';
                                break;
                            case 'media':
                                $prioridad_class = 'border-left-info';
                                $badge_class = 'bg-info';
                                $icon = 'fa-info-circle';
                                break;
                            case 'baja':
                                $prioridad_class = 'border-left-success';
                                $badge_class = 'bg-success';
                                $icon = 'fa-check-circle';
                                break;
                            default:
                                $prioridad_class = 'border-left-secondary';
                                $badge_class = 'bg-secondary';
                                $icon = 'fa-question-circle';
                        }

                        // Truncar contenido para vista previa
                        $contenido_preview = strlen($comunicado['contenido']) > 200 
                            ? substr($comunicado['contenido'], 0, 200) . '...' 
                            : $comunicado['contenido'];
                        ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="content-box h-100 comunicado-card <?php echo $prioridad_class; ?>" 
                                 style="border-left: 4px solid; transition: transform 0.2s, box-shadow 0.2s; cursor: pointer;"
                                 onclick="window.location.href='ComunicadoControlador.php?action=verComunicado&id=<?php echo $comunicado['id_comunicado']; ?>'">
                                <div class="content-box-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">
                                        <i class="fas fa-bullhorn me-2"></i>
                                        <?php echo htmlspecialchars(mb_substr($comunicado['titulo'], 0, 40)); ?>
                                        <?php if (strlen($comunicado['titulo']) > 40): ?>...<?php endif; ?>
                                    </h6>
                                    <span class="badge <?php echo $badge_class; ?>">
                                        <i class="fas <?php echo $icon; ?> me-1"></i>
                                        <?php echo ucfirst($comunicado['prioridad']); ?>
                                    </span>
                                </div>
                                <div class="content-box-body">
                                    <!-- Contenido -->
                                    <p class="text-muted mb-3" style="min-height: 60px;">
                                        <?php echo nl2br(htmlspecialchars($contenido_preview)); ?>
                                    </p>

                                    <!-- Información del autor y fechas -->
                                    <div class="border-top pt-2">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <small class="text-muted">
                                                <i class="fas fa-user me-1"></i>
                                                <?php echo htmlspecialchars($comunicado['autor_nombre'] . ' ' . $comunicado['autor_apellido']); ?>
                                            </small>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>
                                                <?php echo date('d/m/Y', strtotime($comunicado['fecha_publicacion'])); ?>
                                            </small>
                                            <?php if ($comunicado['fecha_expiracion']): ?>
                                                <small class="text-warning">
                                                    <i class="fas fa-clock me-1"></i>
                                                    Expira: <?php echo date('d/m/Y', strtotime($comunicado['fecha_expiracion'])); ?>
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- Audiencia -->
                                    <div class="mt-2">
                                        <span class="badge bg-dark">
                                            <i class="fas fa-users me-1"></i>
                                            <?php echo htmlspecialchars($comunicado['tipo_audiencia']); ?>
                                        </span>
                                    </div>

                                    <!-- Botón ver más -->
                                    <div class="mt-3">
                                        <a href="ComunicadoControlador.php?action=verComunicado&id=<?php echo $comunicado['id_comunicado']; ?>" 
                                           class="btn btn-sm btn-primary w-100">
                                            <i class="fas fa-eye me-2"></i>Ver Detalles
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Información de cantidad -->
                <div class="content-box mt-4">
                    <div class="content-box-body text-center">
                        <p class="text-muted mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Mostrando <strong><?php echo count($comunicados); ?></strong> comunicado(s) publicado(s)
                        </p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <style>
        .comunicado-card {
            border-radius: 8px;
            overflow: hidden;
        }

        .comunicado-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .border-left-danger {
            border-left-color: #dc3545 !important;
        }

        .border-left-warning {
            border-left-color: #ffc107 !important;
        }

        .border-left-info {
            border-left-color: #0dcaf0 !important;
        }

        .border-left-success {
            border-left-color: #198754 !important;
        }

        .content-box-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 1px solid #dee2e6;
        }

        @media (max-width: 768px) {
            .col-md-6 {
                margin-bottom: 1rem;
            }
        }
    </style>

<?php include("../../includes/footer.php"); ?>




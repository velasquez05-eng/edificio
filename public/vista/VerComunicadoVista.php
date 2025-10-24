<?php
// public/vista/VerComunicadoVista.php
include("../../includes/header.php");

if (!isset($comunicado)) {
    header("Location: ComunicadoControlador.php?error=Comunicado no encontrado");
    exit();
}
?>

    <!-- Page Header -->
    <div class="page-header fade-in">
        <div class="page-title">
            <h1>Ver Comunicado</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item"><a href="ComunicadoControlador.php">Comunicados</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Ver Comunicado</li>
                </ol>
            </nav>
        </div>
        <div class="page-actions">
            <a href="ComunicadoControlador.php?action=editar&id=<?php echo $comunicado['id_comunicado']; ?>"
               class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Editar
            </a>
            <a href="ComunicadoControlador.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <div class="row fade-in">
        <div class="col-lg-8">
            <!-- Tarjeta del Comunicado -->
            <div class="content-box">
                <div class="content-box-header d-flex justify-content-between align-items-center">
                    <h5>Detalles del Comunicado</h5>
                    <div>
                        <?php
                        $badge_class = '';
                        switch($comunicado['prioridad']) {
                            case 'urgente':
                                $badge_class = 'bg-danger';
                                break;
                            case 'alta':
                                $badge_class = 'bg-warning';
                                break;
                            case 'media':
                                $badge_class = 'bg-info';
                                break;
                            case 'baja':
                                $badge_class = 'bg-secondary';
                                break;
                        }
                        ?>
                        <span class="badge <?php echo $badge_class; ?> me-2">
                        <i class="fas fa-exclamation-circle me-1"></i>
                        <?php echo ucfirst($comunicado['prioridad']); ?>
                    </span>
                        <span class="badge bg-dark">
                        <i class="fas fa-users me-1"></i>
                        <?php echo $comunicado['tipo_audiencia']; ?>
                    </span>
                    </div>
                </div>
                <div class="content-box-body">
                    <!-- Encabezado -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h2 class="text-primary mb-3"><?php echo htmlspecialchars($comunicado['titulo']); ?></h2>
                        <div class="row text-muted">
                            <div class="col-md-6">
                                <p class="mb-1">
                                    <i class="fas fa-user me-2"></i>
                                    <strong>Autor:</strong> <?php echo htmlspecialchars($comunicado['autor_nombre'] . ' ' . $comunicado['autor_apellido']); ?>
                                </p>
                                <p class="mb-1">
                                    <i class="fas fa-calendar me-2"></i>
                                    <strong>Publicado:</strong> <?php echo date('d/m/Y H:i', strtotime($comunicado['fecha_publicacion'])); ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1">
                                    <i class="fas fa-clock me-2"></i>
                                    <strong>Expira:</strong>
                                    <?php echo $comunicado['fecha_expiracion'] ? date('d/m/Y', strtotime($comunicado['fecha_expiracion'])) : 'No expira'; ?>
                                </p>
                                <p class="mb-1">
                                    <i class="fas fa-toggle-on me-2"></i>
                                    <strong>Estado:</strong>
                                    <span class="badge bg-success"><?php echo ucfirst($comunicado['estado']); ?></span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Contenido -->
                    <div class="comunicado-contenido">
                        <h4 class="mb-3 text-dark">Contenido:</h4>
                        <div class="p-3 bg-white border rounded">
                            <?php echo nl2br(htmlspecialchars($comunicado['contenido'])); ?>
                        </div>
                    </div>

                    <!-- Información Adicional -->
                    <div class="mt-4 p-3 bg-light rounded">
                        <h5 class="mb-3"><i class="fas fa-info-circle me-2"></i>Información Adicional</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <i class="fas fa-id-badge me-2"></i>
                                    <strong>ID Comunicado:</strong> #<?php echo $comunicado['id_comunicado']; ?>
                                </p>
                                <p class="mb-2">
                                    <i class="fas fa-eye me-2"></i>
                                    <strong>Estado Actual:</strong>
                                    <span class="badge
                                    <?php
                                    switch($comunicado['estado']) {
                                        case 'publicado': echo 'bg-success'; break;
                                        case 'borrador': echo 'bg-warning'; break;
                                        case 'archivado': echo 'bg-info'; break;
                                        default: echo 'bg-secondary';
                                    }
                                    ?>">
                                    <?php echo ucfirst($comunicado['estado']); ?>
                                </span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <i class="fas fa-bullhorn me-2"></i>
                                    <strong>Audiencia:</strong>
                                    <span class="badge bg-dark"><?php echo $comunicado['tipo_audiencia']; ?></span>
                                </p>
                                <p class="mb-2">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    <strong>Prioridad:</strong>
                                    <span class="badge <?php echo $badge_class; ?>">
                                    <?php echo ucfirst($comunicado['prioridad']); ?>
                                </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel Lateral -->
        <div class="col-lg-4">
            <!-- Acciones Rápidas -->
            <div class="content-box mb-4">
                <div class="content-box-header">
                    <h5><i class="fas fa-bolt me-2"></i>Acciones Rápidas</h5>
                </div>
                <div class="content-box-body">
                    <div class="d-grid gap-2">
                        <a href="ComunicadoControlador.php?action=editar&id=<?php echo $comunicado['id_comunicado']; ?>"
                           class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Editar Comunicado
                        </a>

                        <?php if ($comunicado['estado'] != 'publicado'): ?>
                            <a href="ComunicadoControlador.php?action=cambiarEstado&id=<?php echo $comunicado['id_comunicado']; ?>&estado=publicado"
                               class="btn btn-success">
                                <i class="fas fa-eye me-2"></i>Publicar
                            </a>
                        <?php endif; ?>

                        <?php if ($comunicado['estado'] != 'archivado'): ?>
                            <a href="ComunicadoControlador.php?action=cambiarEstado&id=<?php echo $comunicado['id_comunicado']; ?>&estado=archivado"
                               class="btn btn-info">
                                <i class="fas fa-archive me-2"></i>Archivar
                            </a>
                        <?php endif; ?>

                        <button type="button"
                                class="btn btn-danger"
                                data-bs-toggle="modal"
                                data-bs-target="#eliminarComunicadoModal">
                            <i class="fas fa-trash me-2"></i>Eliminar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Información de Estado -->
            <div class="content-box">
                <div class="content-box-header">
                    <h5><i class="fas fa-chart-bar me-2"></i>Estadísticas</h5>
                </div>
                <div class="content-box-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-calendar text-primary me-2"></i>Días Publicado</span>
                            <span class="badge bg-primary">
                            <?php
                            $dias = floor((time() - strtotime($comunicado['fecha_publicacion'])) / (60 * 60 * 24));
                            echo $dias . ' día' . ($dias != 1 ? 's' : '');
                            ?>
                        </span>
                        </div>

                        <?php if ($comunicado['fecha_expiracion']): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-clock text-warning me-2"></i>Días para Expirar</span>
                                <span class="badge bg-warning">
                                <?php
                                $dias_expira = floor((strtotime($comunicado['fecha_expiracion']) - time()) / (60 * 60 * 24));
                                echo $dias_expira >= 0 ? $dias_expira . ' día' . ($dias_expira != 1 ? 's' : '') : 'Expirado';
                                ?>
                            </span>
                            </div>
                        <?php endif; ?>

                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-tag text-info me-2"></i>Prioridad</span>
                            <span class="badge <?php echo $badge_class; ?>">
                            <?php echo ucfirst($comunicado['prioridad']); ?>
                        </span>
                        </div>

                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-users text-success me-2"></i>Alcance</span>
                            <span class="badge bg-dark"><?php echo $comunicado['tipo_audiencia']; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Eliminar -->
    <div class="modal fade" id="eliminarComunicadoModal" tabindex="-1" aria-labelledby="eliminarComunicadoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eliminarComunicadoModalLabel">
                        <i class="fas fa-trash me-2"></i>Confirmar Eliminación
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                        <h6>¿Está seguro que desea eliminar este comunicado?</h6>
                        <p class="text-muted mb-0">"<?php echo htmlspecialchars($comunicado['titulo']); ?>"</p>
                        <p class="text-muted">Esta acción no se puede deshacer.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <a href="ComunicadoControlador.php?action=eliminar&id=<?php echo $comunicado['id_comunicado']; ?>"
                       class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Eliminar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <style>
        .comunicado-contenido {
            line-height: 1.6;
        }

        .comunicado-contenido .bg-white {
            min-height: 200px;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .list-group-item {
            border: none;
            padding: 0.75rem 0;
        }

        .content-box-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .bg-light {
            background-color: #f8f9fa !important;
        }
    </style>

<?php include("../../includes/footer.php"); ?>
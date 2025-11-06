<?php
// public/vista/ListarComunicadosEliminadosVista.php
include("../../includes/header.php");
?>

    <!-- Page Header -->
    <div class="page-header fade-in">
        <div class="page-title">
            <h1>Comunicados Eliminados</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item"><a href="ComunicadoControlador.php">Comunicados</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Comunicados Eliminados</li>
                </ol>
            </nav>
        </div>
        <div class="page-actions">
            <a href="ComunicadoControlador.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver a Comunicados
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

    <!-- Lista de Comunicados Eliminados -->
    <div class="row fade-in">
        <div class="col-12">
            <div class="content-box">
                <div class="content-box-header d-flex justify-content-between align-items-center bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-trash me-2"></i>Comunicados Eliminados
                    </h5>
                    <span class="badge bg-light text-dark"><?php echo count($comunicados); ?> eliminados</span>
                </div>
                <div class="content-box-body">
                    <?php if (empty($comunicados)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-trash-alt fa-4x text-muted mb-3"></i>
                            <h4 class="text-muted mb-3">No hay comunicados eliminados</h4>
                            <p class="text-muted">No se encontraron comunicados en la papelera de reciclaje.</p>
                            <a href="ComunicadoControlador.php" class="btn btn-primary">
                                <i class="fas fa-arrow-left me-2"></i>Volver a Comunicados
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Nota:</strong> Estos comunicados han sido eliminados. Puedes restaurarlos haciendo clic en el botón "Restaurar".
                        </div>
                        <div class="table-container">
                            <table id="tablaComunicadosEliminados" class="table table-hover table-striped">
                                <thead>
                                <tr>
                                    <th># ID</th>
                                    <th>Título</th>
                                    <th>Autor</th>
                                    <th>Fecha Publicación</th>
                                    <th>Fecha Expiración</th>
                                    <th>Prioridad</th>
                                    <th>Audiencia</th>
                                    <th>Opciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($comunicados as $comunicado): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($comunicado['id_comunicado']); ?></strong></td>
                                        <td>
                                            <i class="fas fa-file-alt text-danger me-2"></i>
                                            <?php echo htmlspecialchars($comunicado['titulo']); ?>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($comunicado['autor_nombre'] . ' ' . $comunicado['autor_apellido']); ?>
                                        </td>
                                        <td>
                                            <i class="fas fa-calendar text-info me-2"></i>
                                            <?php echo date('d/m/Y H:i', strtotime($comunicado['fecha_publicacion'])); ?>
                                        </td>
                                        <td>
                                            <?php if ($comunicado['fecha_expiracion']): ?>
                                                <i class="fas fa-clock text-warning me-2"></i>
                                                <?php echo date('d/m/Y', strtotime($comunicado['fecha_expiracion'])); ?>
                                            <?php else: ?>
                                                <span class="text-muted">Sin expiración</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $badge_class = '';
                                            $icon = '';
                                            switch($comunicado['prioridad']) {
                                                case 'urgente':
                                                    $badge_class = 'bg-danger';
                                                    $icon = 'fa-exclamation-triangle';
                                                    break;
                                                case 'alta':
                                                    $badge_class = 'bg-warning';
                                                    $icon = 'fa-exclamation-circle';
                                                    break;
                                                case 'media':
                                                    $badge_class = 'bg-info';
                                                    $icon = 'fa-info-circle';
                                                    break;
                                                case 'baja':
                                                    $badge_class = 'bg-secondary';
                                                    $icon = 'fa-info';
                                                    break;
                                                default:
                                                    $badge_class = 'bg-secondary';
                                                    $icon = 'fa-question-circle';
                                            }
                                            ?>
                                            <span class="badge <?php echo $badge_class; ?>">
                                                <i class="fas <?php echo $icon; ?> me-1"></i>
                                                <?php echo ucfirst(htmlspecialchars($comunicado['prioridad'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-dark">
                                                <i class="fas fa-users me-1"></i>
                                                <?php echo htmlspecialchars($comunicado['tipo_audiencia']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1 align-items-center">
                                                <a href="ComunicadoControlador.php?action=verComunicado&id=<?php echo $comunicado['id_comunicado']; ?>"
                                                   class="btn btn-info btn-sm" title="Ver comunicado">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                <a href="ComunicadoControlador.php?action=restaurar&id=<?php echo $comunicado['id_comunicado']; ?>"
                                                   class="btn btn-success btn-sm" 
                                                   title="Restaurar comunicado"
                                                   onclick="return confirm('¿Está seguro que desea restaurar este comunicado? Se restaurará como borrador.');">
                                                    <i class="fas fa-undo"></i>
                                                </a>
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

    <!-- Scripts para DataTable y funcionalidades -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar DataTable
            var tabla = $('#tablaComunicadosEliminados').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                responsive: true,
                pageLength: 10,
                order: [[0, 'desc']],
                columnDefs: [
                    {
                        orderable: false,
                        targets: [7] // Columna de opciones no ordenable
                    }
                ]
            });

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

    <style>
        .content-box-header {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            border-bottom: 1px solid #dee2e6;
        }

        .table th {
            border-top: none;
            font-weight: 600;
            color: var(--azul-oscuro);
            background-color: #f8f9fa;
        }

        .d-flex.gap-1 .btn {
            margin: 0 2px;
        }

        .table tbody tr {
            opacity: 0.9;
        }

        .table tbody tr:hover {
            opacity: 1;
        }
    </style>

<?php include("../../includes/footer.php"); ?>




<?php
// public/vista/ListarComunicadosVista.php
include("../../includes/header.php");
?>

    <!-- Page Header -->
    <div class="page-header fade-in">
        <div class="page-title">
            <h1>Gestión de Comunicados</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Comunicados</li>
                </ol>
            </nav>
        </div>
        <div class="page-actions">
            <a href="ComunicadoControlador.php?action=formularioComunicado" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>Nuevo Comunicado
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

    <!-- Estadísticas -->
    <div class="row fade-in mb-4">
        <div class="col-md-2 col-6 mb-3">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-bullhorn fa-2x text-primary mb-2"></i>
                    <h4 class="mb-1"><?php echo $estadisticas['total'] ?? 0; ?></h4>
                    <p class="text-muted mb-0">Total</p>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-3">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-eye fa-2x text-success mb-2"></i>
                    <h4 class="mb-1"><?php echo $estadisticas['publicados'] ?? 0; ?></h4>
                    <p class="text-muted mb-0">Publicados</p>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-3">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-edit fa-2x text-warning mb-2"></i>
                    <h4 class="mb-1"><?php echo $estadisticas['borradores'] ?? 0; ?></h4>
                    <p class="text-muted mb-0">Borradores</p>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-3">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-archive fa-2x text-info mb-2"></i>
                    <h4 class="mb-1"><?php echo $estadisticas['archivados'] ?? 0; ?></h4>
                    <p class="text-muted mb-0">Archivados</p>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-3">
            <?php if (isset($_SESSION['id_rol']) && $_SESSION['id_rol'] == '1'): ?>
                <a href="ComunicadoControlador.php?action=listarEliminados" class="text-decoration-none">
            <?php endif; ?>
            <div class="content-box text-center <?php echo (isset($_SESSION['id_rol']) && $_SESSION['id_rol'] == '1') ? 'hover-card' : ''; ?>">
                <div class="content-box-body">
                    <i class="fas fa-trash fa-2x text-danger mb-2"></i>
                    <h4 class="mb-1"><?php echo $estadisticas['eliminados'] ?? 0; ?></h4>
                    <p class="text-muted mb-0">Eliminados</p>
                </div>
            </div>
            <?php if (isset($_SESSION['id_rol']) && $_SESSION['id_rol'] == '1'): ?>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Lista de Comunicados -->
    <div class="row fade-in">
        <div class="col-12">
            <div class="content-box">
                <div class="content-box-header d-flex justify-content-between align-items-center">
                    <h5>Lista de Comunicados</h5>
                    <span class="badge bg-primary"><?php echo count($comunicados); ?> comunicados</span>
                </div>
                <div class="content-box-body">
                    <?php if (empty($comunicados)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-bullhorn fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No hay comunicados registrados</p>
                            <a href="ComunicadoControlador.php?action=formularioComunicado" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Crear Primer Comunicado
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-container">
                            <table id="tablaComunicados" class="table table-hover table-striped">
                                <thead>
                                <tr>
                                    <th># ID</th>
                                    <th>Título</th>
                                    <th>Autor</th>
                                    <th>Fecha Publicación</th>
                                    <th>Fecha Expiración</th>
                                    <th>Prioridad</th>
                                    <th>Audiencia</th>
                                    <th>Estado</th>
                                    <th>Opciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($comunicados as $comunicado): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($comunicado['id_comunicado']); ?></strong></td>
                                        <td>
                                            <i class="fas fa-file-alt text-primary me-2"></i>
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
                                            <?php
                                            $badge_class = '';
                                            switch($comunicado['estado']) {
                                                case 'publicado':
                                                    $badge_class = 'bg-success';
                                                    $icon = 'fa-eye';
                                                    break;
                                                case 'borrador':
                                                    $badge_class = 'bg-warning';
                                                    $icon = 'fa-edit';
                                                    break;
                                                case 'archivado':
                                                    $badge_class = 'bg-info';
                                                    $icon = 'fa-archive';
                                                    break;
                                                default:
                                                    $badge_class = 'bg-secondary';
                                                    $icon = 'fa-question-circle';
                                            }
                                            ?>
                                            <span class="badge <?php echo $badge_class; ?>">
                                                <i class="fas <?php echo $icon; ?> me-1"></i>
                                                <?php echo ucfirst(htmlspecialchars($comunicado['estado'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1 align-items-center">
                                                <a href="ComunicadoControlador.php?action=verComunicado&id=<?php echo $comunicado['id_comunicado']; ?>"
                                                   class="btn btn-info btn-sm" title="Ver comunicado">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                <a href="ComunicadoControlador.php?action=editar&id=<?php echo $comunicado['id_comunicado']; ?>"
                                                   class="btn btn-warning btn-sm" title="Editar comunicado">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <!-- Dropdown para cambiar estado y eliminar -->
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-secondary btn-sm dropdown-toggle"
                                                            data-bs-toggle="dropdown" aria-expanded="false" title="Más opciones">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li><h6 class="dropdown-header">Cambiar Estado</h6></li>
                                                        <?php if ($comunicado['estado'] != 'publicado'): ?>
                                                            <li>
                                                                <a class="dropdown-item" href="ComunicadoControlador.php?action=cambiarEstado&id=<?php echo $comunicado['id_comunicado']; ?>&estado=publicado">
                                                                    <i class="fas fa-eye text-success me-2"></i>Publicar
                                                                </a>
                                                            </li>
                                                        <?php endif; ?>
                                                        <?php if ($comunicado['estado'] != 'borrador'): ?>
                                                            <li>
                                                                <a class="dropdown-item" href="ComunicadoControlador.php?action=cambiarEstado&id=<?php echo $comunicado['id_comunicado']; ?>&estado=borrador">
                                                                    <i class="fas fa-edit text-warning me-2"></i>Marcar como Borrador
                                                                </a>
                                                            </li>
                                                        <?php endif; ?>
                                                        <?php if ($comunicado['estado'] != 'archivado'): ?>
                                                            <li>
                                                                <a class="dropdown-item" href="ComunicadoControlador.php?action=cambiarEstado&id=<?php echo $comunicado['id_comunicado']; ?>&estado=archivado">
                                                                    <i class="fas fa-archive text-info me-2"></i>Archivar
                                                                </a>
                                                            </li>
                                                        <?php endif; ?>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <a class="dropdown-item text-danger" href="#"
                                                               data-bs-toggle="modal"
                                                               data-bs-target="#eliminarComunicadoModal"
                                                               data-id="<?php echo htmlspecialchars($comunicado['id_comunicado']); ?>"
                                                               data-titulo="<?php echo htmlspecialchars($comunicado['titulo']); ?>">
                                                                <i class="fas fa-trash me-2"></i>Eliminar
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
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

    <!-- Modal Eliminar Comunicado -->
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
                        <h6>¿Está seguro que desea eliminar el comunicado?</h6>
                        <p class="text-muted mb-0">Comunicado: <strong id="tituloComunicadoEliminar"></strong></p>
                        <p class="text-muted">Esta acción no se puede deshacer.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <a href="#" class="btn btn-danger" id="btnEliminarComunicado">
                        <i class="fas fa-trash me-2"></i>Eliminar
                    </a>
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
            var tabla = $('#tablaComunicados').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                responsive: true,
                pageLength: 10,
                order: [[0, 'desc']],
                columnDefs: [
                    {
                        orderable: false,
                        targets: [8] // Columna de opciones no ordenable
                    }
                ]
            });

            // Configurar modal de eliminación
            const eliminarModal = document.getElementById('eliminarComunicadoModal');
            eliminarModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const idComunicado = button.getAttribute('data-id');
                const titulo = button.getAttribute('data-titulo');

                document.getElementById('tituloComunicadoEliminar').textContent = titulo;
                document.getElementById('btnEliminarComunicado').href =
                    'ComunicadoControlador.php?action=eliminar&id=' + idComunicado;
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
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
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

        .btn-group .btn {
            margin: 0;
        }

        .content-box.text-center {
            transition: transform 0.2s;
        }

        .content-box.text-center:hover {
            transform: translateY(-5px);
        }

        .hover-card {
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
        }

        .hover-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        a.text-decoration-none {
            color: inherit;
        }

        a.text-decoration-none:hover {
            color: inherit;
        }
    </style>

<?php include("../../includes/footer.php"); ?>
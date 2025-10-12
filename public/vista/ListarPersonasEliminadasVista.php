<?php include("../../includes/header.php"); ?>

    <!-- Page Header -->
    <div class="page-header fade-in">
        <div class="page-title">
            <h1>Personas Eliminadas</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item"><a href="#">Persona</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Personas Eliminadas</li>
                </ol>
            </nav>
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

    <!-- Personas Eliminadas -->
    <div class="row fade-in">
        <div class="col-12">
            <div class="content-box">
                <div class="content-box-header d-flex justify-content-between align-items-center">
                    <h5>Lista de Personas Eliminadas</h5>
                    <span class="badge bg-danger"><?php echo count($personas); ?> personas</span>
                </div>
                <div class="content-box-body">
                    <?php if (empty($personas)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No hay personas eliminadas</p>

                        </div>
                    <?php else: ?>
                        <div class="table-container">
                            <table id="tablaPersonasEliminadas" class="table table-hover table-striped">
                                <thead>
                                <tr>
                                    <th># ID</th>
                                    <th>Nombre Completo</th>
                                    <th>CI</th>
                                    <th>Username</th>
                                    <th>Teléfono</th>
                                    <th>Email</th>
                                    <th>Rol</th>
                                    <th>Fecha de Eliminación</th>
                                    <th>Opciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($personas as $persona): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($persona['id_persona']); ?></strong></td>
                                        <td>
                                            <i class="fas fa-user text-primary me-2"></i>
                                            <?php echo htmlspecialchars($persona['nombre'] . ' ' . $persona['apellido_paterno'] . ' ' . $persona['apellido_materno']); ?>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($persona['ci']); ?>
                                        </td>
                                        <td>
                                            <i class="fas fa-user-circle text-info me-2"></i>
                                            <?php echo htmlspecialchars($persona['username']); ?>
                                        </td>
                                        <td>
                                            <i class="fas fa-phone text-success me-2"></i>
                                            <?php echo htmlspecialchars($persona['telefono']); ?>
                                        </td>
                                        <td>
                                            <i class="fas fa-envelope text-warning me-2"></i>
                                            <?php echo htmlspecialchars($persona['email']); ?>
                                        </td>
                                        <td>
                                        <span class="badge bg-info">
                                            <i class="fas fa-user-tag me-1"></i>
                                            <?php echo htmlspecialchars($persona['rol']); ?>
                                        </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-calendar-times me-1"></i>
                                                <?php echo htmlspecialchars($persona['fecha_eliminado']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-success btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#restaurarPersonaModal"
                                                        data-id="<?php echo htmlspecialchars($persona['id_persona']); ?>"
                                                        data-nombre="<?php echo htmlspecialchars($persona['nombre'] . ' ' . $persona['apellido_paterno']); ?>"
                                                        data-id-rol="<?php echo htmlspecialchars($persona['id_rol']); ?>"
                                                        title="Restaurar persona">
                                                    <i class="fas fa-undo"></i>
                                                </button>
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

    <!-- Modal Restaurar Persona -->
    <div class="modal fade" id="restaurarPersonaModal" tabindex="-1" aria-labelledby="restaurarPersonaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="restaurarPersonaModalLabel">
                        <i class="fas fa-undo me-2"></i>Restaurar Persona
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="PersonaControlador.php?action=restaurarPersona" id="formRestaurarPersona">
                    <input type="hidden" name="action" value="restaurarPersona">
                    <input type="hidden" id="id_persona_restaurar" name="id_persona">
                    <input type="hidden" id="id_rol_restaurar" name="id_rol">

                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="text-center mb-3">
                                <i class="fas fa-undo fa-3x text-success"></i>
                            </div>
                            <p class="text-center">¿Está seguro que desea restaurar a la siguiente persona?</p>
                            <p class="text-center fw-bold" id="nombrePersonaRestaurar"></p>
                            <p class="text-center">La persona volverá a estar disponible en el sistema.</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-undo me-2"></i>Restaurar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Incluir DataTables CSS y JS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

    <!-- Script para DataTable y funcionalidades -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar DataTable
            var tabla = $('#tablaPersonasEliminadas').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                responsive: false, // Desactivar responsive para eliminar scroll horizontal
                scrollX: false, // Desactivar scroll horizontal
                autoWidth: false, // Desactivar auto width
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                pageLength: 10,
                lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
                order: [[0, 'desc']],
                columnDefs: [
                    {
                        orderable: false,
                        targets: [8] // Columna de opciones no ordenable
                    },
                    {
                        searchable: false,
                        targets: [8] // Columna de opciones no buscable
                    },
                    {
                        width: 'auto', // Ancho automático para todas las columnas
                        targets: '_all'
                    }
                ],
                initComplete: function() {
                    // Personalizar el buscador
                    $('.dataTables_filter input').addClass('form-control form-control-sm');
                    $('.dataTables_filter input').attr('placeholder', 'Buscar...');

                    // Personalizar el selector de cantidad de registros
                    $('.dataTables_length select').addClass('form-select form-select-sm');
                }
            });

            // Cargar datos en el modal de restaurar
            const restaurarModal = document.getElementById('restaurarPersonaModal');
            restaurarModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;

                document.getElementById('id_persona_restaurar').value = button.getAttribute('data-id');
                document.getElementById('nombrePersonaRestaurar').textContent = button.getAttribute('data-nombre');
                document.getElementById('id_rol_restaurar').value = button.getAttribute('data-id-rol');
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

    <!-- Estilos adicionales -->
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

        .btn-group .btn {
            margin: 0 2px;
        }

        .badge {
            font-size: 0.75rem;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }

        /* Contenedor de tabla sin scroll horizontal */
        .table-container {
            width: 100%;
            overflow-x: hidden; /* Eliminar scroll horizontal */
        }

        /* Estilos para DataTable */
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 0.375rem 0.75rem;
            margin-bottom: 1rem;
        }

        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            margin: 0 2px;
            padding: 0.375rem 0.75rem;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--azul-oscuro);
            border-color: var(--azul-oscuro);
            color: white !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #e9ecef;
            border-color: #dee2e6;
        }

        /* Asegurar que la tabla ocupe el 100% del ancho disponible */
        #tablaPersonasEliminadas {
            width: 100% !important;
            table-layout: auto;
        }

        /* Eliminar cualquier scroll horizontal */
        .dataTables_scrollBody {
            overflow-x: hidden !important;
        }
    </style>

<?php include("../../includes/footer.php"); ?>
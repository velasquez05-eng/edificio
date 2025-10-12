<?php include("../../includes/header.php"); ?>

    <!-- Page Header -->
    <div class="page-header fade-in">
        <div class="page-title">
            <h1>Departamentos</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item"><a href="#">Departamento</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Lista de Departamentos</li>
                </ol>
            </nav>
        </div>
        <div class="page-actions">
            <a href="DepartamentoControlador.php?action=formularioDepartamento" class="btn btn-success">
                Registrar Departamento
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

    <!-- Departamentos -->
    <div class="row fade-in">
        <div class="col-12">
            <div class="content-box">
                <div class="content-box-header d-flex justify-content-between align-items-center">
                    <h5>Lista de Departamentos</h5>
                    <span class="badge bg-primary"><?php echo count($departamentos); ?> departamentos</span>
                </div>
                <div class="content-box-body">
                    <?php if (empty($departamentos)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-building fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No hay departamentos registrados</p>
                        </div>
                    <?php else: ?>
                        <div class="table-container">
                            <table id="tablaDepartamentos" class="table table-hover table-striped">
                                <thead>
                                <tr>
                                    <th># ID</th>
                                    <th>Número</th>
                                    <th>Piso</th>
                                    <th>Estado</th>
                                    <th>Opciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($departamentos as $departamento): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($departamento['id_departamento']); ?></strong></td>
                                        <td>
                                            <i class="fas fa-hashtag text-primary me-2"></i>
                                            <?php echo htmlspecialchars($departamento['numero']); ?>
                                        </td>
                                        <td>
                                            <i class="fas fa-stairs text-info me-2"></i>
                                            Piso <?php echo htmlspecialchars($departamento['piso']); ?>
                                        </td>
                                        <td>
                                            <?php
                                            $badge_class = '';
                                            switch($departamento['estado']) {
                                                case 'disponible':
                                                    $badge_class = 'bg-success';
                                                    $icon = 'fa-check-circle';
                                                    break;
                                                case 'ocupado':
                                                    $badge_class = 'bg-warning';
                                                    $icon = 'fa-user';
                                                    break;
                                                default:
                                                    $badge_class = 'bg-secondary';
                                                    $icon = 'fa-question-circle';
                                            }
                                            ?>
                                            <span class="badge <?php echo $badge_class; ?>">
                                                <i class="fas <?php echo $icon; ?> me-1"></i>
                                                <?php echo ucfirst(htmlspecialchars($departamento['estado'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-info btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#verPersonasModal"
                                                        data-id="<?php echo htmlspecialchars($departamento['id_departamento']); ?>"
                                                        data-numero="<?php echo htmlspecialchars($departamento['numero']); ?>"
                                                        data-piso="<?php echo htmlspecialchars($departamento['piso']); ?>"
                                                        title="Ver personas del departamento">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-warning btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editarDepartamentoModal"
                                                        data-id="<?php echo htmlspecialchars($departamento['id_departamento']); ?>"
                                                        data-numero="<?php echo htmlspecialchars($departamento['numero']); ?>"
                                                        data-piso="<?php echo htmlspecialchars($departamento['piso']); ?>"
                                                        data-estado="<?php echo htmlspecialchars($departamento['estado']); ?>"
                                                        title="Editar departamento">
                                                    <i class="fas fa-edit"></i>
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

    <!-- Modal Ver Personas del Departamento -->
    <div class="modal fade" id="verPersonasModal" tabindex="-1" aria-labelledby="verPersonasModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="verPersonasModalLabel">
                        <i class="fas fa-users me-2"></i>Personas del Departamento
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="departamento-info bg-light p-3 rounded">
                                    <h6 class="mb-0">
                                        <i class="fas fa-building text-primary me-2"></i>
                                        Departamento: <span id="numeroDepartamento" class="fw-bold"></span> -
                                        Piso: <span id="pisoDepartamento" class="fw-bold"></span>
                                    </h6>
                                </div>
                            </div>
                        </div>

                        <div id="listaPersonas">
                            <!-- Aquí se cargarán las personas dinámicamente -->
                            <div class="text-center py-4">
                                <i class="fas fa-spinner fa-spin fa-2x text-muted mb-3"></i>
                                <p class="text-muted">Cargando información...</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Editar Departamento -->
    <div class="modal fade" id="editarDepartamentoModal" tabindex="-1" aria-labelledby="editarDepartamentoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarDepartamentoModalLabel">
                        <i class="fas fa-edit me-2"></i>Editar Departamento
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditarDepartamento" method="POST" action="DepartamentoControlador.php">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="editarDepartamento">
                        <input type="hidden" name="id_departamento" id="editIdDepartamento">

                        <div class="mb-3">
                            <label for="editNumero" class="form-label">
                                <i class="fas fa-hashtag text-primary me-2"></i>Número
                            </label>
                            <input type="text" class="form-control" id="editNumero" name="numero" required>
                            <div class="form-text">Número identificador del departamento</div>
                        </div>

                        <div class="mb-3">
                            <label for="editPiso" class="form-label">
                                <i class="fas fa-stairs text-info me-2"></i>Piso
                            </label>
                            <input type="number" class="form-control" id="editPiso" name="piso" min="1" max="50" required>
                            <div class="form-text">Número de piso donde se encuentra</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Guardar Cambios
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
            var tabla = $('#tablaDepartamentos').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                responsive: false,
                scrollX: false,
                autoWidth: false,
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                pageLength: 10,
                lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
                order: [[0, 'desc']],
                columnDefs: [
                    {
                        orderable: false,
                        targets: [4] // Columna de opciones no ordenable
                    },
                    {
                        searchable: false,
                        targets: [4] // Columna de opciones no buscable
                    },
                    {
                        width: 'auto',
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

            // Cargar datos en el modal de ver personas
            const verPersonasModal = document.getElementById('verPersonasModal');
            verPersonasModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const idDepartamento = button.getAttribute('data-id');
                const numero = button.getAttribute('data-numero');
                const piso = button.getAttribute('data-piso');

                // Actualizar información del departamento
                document.getElementById('numeroDepartamento').textContent = numero;
                document.getElementById('pisoDepartamento').textContent = piso;

                // Cargar personas del departamento
                cargarPersonasDepartamento(idDepartamento);
            });

            // Cargar datos en el modal de editar departamento
            const editarDepartamentoModal = document.getElementById('editarDepartamentoModal');
            editarDepartamentoModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const idDepartamento = button.getAttribute('data-id');
                const numero = button.getAttribute('data-numero');
                const piso = button.getAttribute('data-piso');
                const estado = button.getAttribute('data-estado');

                // Llenar el formulario con los datos actuales
                document.getElementById('editIdDepartamento').value = idDepartamento;
                document.getElementById('editNumero').value = numero;
                document.getElementById('editPiso').value = piso;
                document.getElementById('editEstado').value = estado;
            });

            // Función para cargar personas del departamento
            function cargarPersonasDepartamento(idDepartamento) {
                const listaPersonas = document.getElementById('listaPersonas');

                // Mostrar loading
                listaPersonas.innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-spinner fa-spin fa-2x text-muted mb-3"></i>
                        <p class="text-muted">Cargando información...</p>
                    </div>
                `;

                // Simular carga de datos (aquí deberías hacer una petición AJAX real)
                setTimeout(() => {
                    // Esto es un ejemplo - reemplaza con tu lógica real
                    const personasEjemplo = [
                        { nombre: 'Juan Pérez', rol: 'Propietario', telefono: '123456789' },
                        { nombre: 'María García', rol: 'Residente', telefono: '987654321' }
                    ];

                    if (personasEjemplo.length > 0) {
                        let html = '<div class="row">';
                        personasEjemplo.forEach(persona => {
                            html += `
                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <i class="fas fa-user text-primary me-2"></i>
                                                ${persona.nombre}
                                            </h6>
                                            <p class="card-text mb-1">
                                                <small class="text-muted">
                                                    <i class="fas fa-user-tag me-1"></i>
                                                    ${persona.rol}
                                                </small>
                                            </p>
                                            <p class="card-text mb-0">
                                                <small class="text-muted">
                                                    <i class="fas fa-phone me-1"></i>
                                                    ${persona.telefono}
                                                </small>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        html += '</div>';
                        listaPersonas.innerHTML = html;
                    } else {
                        listaPersonas.innerHTML = `
                            <div class="text-center py-4">
                                <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No hay personas asignadas a este departamento</p>
                            </div>
                        `;
                    }
                }, 1000);
            }

            // Manejar el envío del formulario de edición
            document.getElementById('formEditarDepartamento').addEventListener('submit', function(e) {
                e.preventDefault();

                // Aquí puedes agregar validaciones adicionales si es necesario
                const formData = new FormData(this);

                // Simular envío del formulario (reemplaza con tu lógica real)
                console.log('Datos a enviar:', Object.fromEntries(formData));

                // En un caso real, aquí harías una petición AJAX o dejarías que el formulario se envíe normalmente
                this.submit();
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

        .table-container {
            width: 100%;
            overflow-x: hidden;
        }

        .departamento-info {
            border-left: 4px solid var(--azul-oscuro);
        }

        .card {
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
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

        #tablaDepartamentos {
            width: 100% !important;
            table-layout: auto;
        }

        .dataTables_scrollBody {
            overflow-x: hidden !important;
        }

        /* Estilos para el formulario de edición */
        .form-label {
            font-weight: 600;
            color: var(--azul-oscuro);
        }

        .form-control, .form-select {
            border-radius: 0.5rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--azul-oscuro);
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
    </style>

<?php include("../../includes/footer.php"); ?>
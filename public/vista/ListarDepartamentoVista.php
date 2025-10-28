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
                                    <th>Residentes</th>
                                    <th>Medidores</th>
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
                                        <span class="badge bg-info">
                                            <i class="fas fa-users me-1"></i>
                                            <?php echo $departamento['total_residentes'] ?? 0; ?>
                                        </span>
                                        </td>
                                        <td>
                                        <span class="badge bg-purple">
                                            <i class="fas fa-tachometer-alt me-1"></i>
                                            <?php echo $departamento['total_medidores'] ?? 0; ?>
                                        </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-info btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#detalleDepartamentoModal"
                                                        data-id="<?php echo htmlspecialchars($departamento['id_departamento']); ?>"
                                                        data-numero="<?php echo htmlspecialchars($departamento['numero']); ?>"
                                                        data-piso="<?php echo htmlspecialchars($departamento['piso']); ?>"
                                                        data-estado="<?php echo htmlspecialchars($departamento['estado']); ?>"
                                                        title="Ver detalles del departamento">
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

    <!-- Modal Detalles del Departamento -->
    <div class="modal fade" id="detalleDepartamentoModal" tabindex="-1" aria-labelledby="detalleDepartamentoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detalleDepartamentoModalLabel">
                        <i class="fas fa-building me-2"></i>Detalles del Departamento
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <!-- Información del Departamento -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="departamento-info bg-light p-3 rounded">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <h6 class="mb-1">
                                                <i class="fas fa-hashtag text-primary me-2"></i>
                                                Departamento: <span id="numeroDepartamento" class="fw-bold"></span>
                                            </h6>
                                        </div>
                                        <div class="col-md-4">
                                            <h6 class="mb-1">
                                                <i class="fas fa-stairs text-info me-2"></i>
                                                Piso: <span id="pisoDepartamento" class="fw-bold"></span>
                                            </h6>
                                        </div>
                                        <div class="col-md-4">
                                            <h6 class="mb-1">
                                                <i class="fas fa-circle me-2"></i>
                                                Estado: <span id="estadoDepartamento" class="fw-bold"></span>
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Sección Residentes -->
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-users me-2"></i>Residentes
                                            <span id="contadorResidentes" class="badge bg-light text-dark ms-2">0</span>
                                        </h6>
                                    </div>
                                    <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                                        <div id="listaPersonas">
                                            <div class="text-center py-4">
                                                <i class="fas fa-spinner fa-spin fa-2x text-muted mb-3"></i>
                                                <p class="text-muted">Cargando residentes...</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sección Medidores -->
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header bg-purple text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-tachometer-alt me-2"></i>Medidores
                                            <span id="contadorMedidores" class="badge bg-light text-dark ms-2">0</span>
                                        </h6>
                                    </div>
                                    <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                                        <div id="listaMedidores">
                                            <div class="text-center py-4">
                                                <i class="fas fa-spinner fa-spin fa-2x text-muted mb-3"></i>
                                                <p class="text-muted">Cargando medidores...</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
                        targets: [4, 5, 6] // Columnas no ordenables
                    },
                    {
                        searchable: false,
                        targets: [4, 5, 6] // Columnas no buscables
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

            // Cargar datos en el modal de detalles
            const detalleModal = document.getElementById('detalleDepartamentoModal');
            detalleModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const idDepartamento = button.getAttribute('data-id');
                const numero = button.getAttribute('data-numero');
                const piso = button.getAttribute('data-piso');
                const estado = button.getAttribute('data-estado');

                // Actualizar información del departamento
                document.getElementById('numeroDepartamento').textContent = numero;
                document.getElementById('pisoDepartamento').textContent = piso;
                document.getElementById('estadoDepartamento').textContent = estado.charAt(0).toUpperCase() + estado.slice(1);

                // Cargar residentes y medidores
                cargarResidentesDepartamento(idDepartamento);
                cargarMedidoresDepartamento(idDepartamento);
            });

            // Cargar datos en el modal de editar departamento
            const editarDepartamentoModal = document.getElementById('editarDepartamentoModal');
            editarDepartamentoModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const idDepartamento = button.getAttribute('data-id');
                const numero = button.getAttribute('data-numero');
                const piso = button.getAttribute('data-piso');

                // Llenar el formulario con los datos actuales
                document.getElementById('editIdDepartamento').value = idDepartamento;
                document.getElementById('editNumero').value = numero;
                document.getElementById('editPiso').value = piso;
            });

            // Función para cargar residentes del departamento
            function cargarResidentesDepartamento(idDepartamento) {
                const listaPersonas = document.getElementById('listaPersonas');

                // Mostrar loading
                listaPersonas.innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-spinner fa-spin fa-2x text-muted mb-3"></i>
                        <p class="text-muted">Cargando residentes...</p>
                    </div>
                `;

                // Hacer petición AJAX real al controlador
                fetch(`DepartamentoControlador.php?action=obtenerDetallesDepartamento&id_departamento=${idDepartamento}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const residentes = data.departamento.residentes || [];

                            if (residentes.length > 0) {
                                let html = '';
                                residentes.forEach(residente => {
                                    html += `
                                        <div class="card mb-3">
                                            <div class="card-body">
                                                <h6 class="card-title mb-2">
                                                    <i class="fas fa-user text-primary me-2"></i>
                                                    ${residente.nombre_completo || residente.nombre + ' ' + residente.apellido_paterno}
                                                </h6>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <small class="text-muted">
                                                            <i class="fas fa-user-tag me-1"></i>
                                                            ${residente.rol || 'Residente'}
                                                        </small>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="text-muted">
                                                            <i class="fas fa-phone me-1"></i>
                                                            ${residente.telefono || 'No disponible'}
                                                        </small>
                                                    </div>
                                                </div>
                                                ${residente.email ? `
                                                <div class="mt-2">
                                                    <small class="text-muted">
                                                        <i class="fas fa-envelope me-1"></i>
                                                        ${residente.email}
                                                    </small>
                                                </div>
                                                ` : ''}
                                            </div>
                                        </div>
                                    `;
                                });
                                listaPersonas.innerHTML = html;
                                document.getElementById('contadorResidentes').textContent = residentes.length;
                            } else {
                                listaPersonas.innerHTML = `
                                    <div class="text-center py-4">
                                        <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No hay residentes asignados a este departamento</p>
                                    </div>
                                `;
                                document.getElementById('contadorResidentes').textContent = '0';
                            }
                        } else {
                            listaPersonas.innerHTML = `
                                <div class="text-center py-4">
                                    <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                                    <p class="text-danger">Error: ${data.error}</p>
                                </div>
                            `;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        listaPersonas.innerHTML = `
                            <div class="text-center py-4">
                                <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                                <p class="text-danger">Error al cargar los residentes</p>
                            </div>
                        `;
                    });
            }

            // Función para cargar medidores del departamento
            function cargarMedidoresDepartamento(idDepartamento) {
                const listaMedidores = document.getElementById('listaMedidores');

                // Mostrar loading
                listaMedidores.innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-spinner fa-spin fa-2x text-muted mb-3"></i>
                        <p class="text-muted">Cargando medidores...</p>
                    </div>
                `;

                // Hacer petición AJAX real al controlador
                fetch(`DepartamentoControlador.php?action=obtenerDetallesDepartamento&id_departamento=${idDepartamento}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const medidores = data.departamento.medidores || [];

                            if (medidores.length > 0) {
                                let html = '';
                                medidores.forEach(medidor => {
                                    const estadoClass = medidor.estado_medidor === 'activo' ? 'bg-success' :
                                        medidor.estado_medidor === 'mantenimiento' ? 'bg-warning' : 'bg-danger';

                                    html += `
                                        <div class="card mb-3">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h6 class="card-title mb-0">
                                                        <i class="fas fa-tachometer-alt text-purple me-2"></i>
                                                        ${medidor.servicio}
                                                    </h6>
                                                    <span class="badge ${estadoClass}">
                                                        ${medidor.estado_medidor}
                                                    </span>
                                                </div>
                                                <p class="card-text mb-1">
                                                    <small class="text-muted">
                                                        <i class="fas fa-barcode me-1"></i>
                                                        Código: ${medidor.codigo}
                                                    </small>
                                                </p>
                                                <p class="card-text mb-1">
                                                    <small class="text-muted">
                                                        <i class="fas fa-ruler me-1"></i>
                                                        Unidad: ${medidor.unidad_medida}
                                                    </small>
                                                </p>
                                                ${medidor.costo_unitario ? `
                                                <p class="card-text mb-2">
                                                    <small class="text-muted">
                                                        <i class="fas fa-dollar-sign me-1"></i>
                                                        Costo: $${medidor.costo_unitario}
                                                    </small>
                                                </p>
                                                ` : ''}
                                                <a href="DepartamentoControlador.php?action=verHistorialConsumo&id_medidor=${medidor.id_medidor}"
                                                   class="btn btn-outline-info btn-sm w-100" >
                                                    <i class="fas fa-chart-line me-1"></i>Ver Historial
                                                </a>
                                            </div>
                                        </div>
                                    `;
                                });
                                listaMedidores.innerHTML = html;
                                document.getElementById('contadorMedidores').textContent = medidores.length;
                            } else {
                                listaMedidores.innerHTML = `
                                    <div class="text-center py-4">
                                        <i class="fas fa-tachometer-alt fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No hay medidores asignados a este departamento</p>
                                    </div>
                                `;
                                document.getElementById('contadorMedidores').textContent = '0';
                            }
                        } else {
                            listaMedidores.innerHTML = `
                                <div class="text-center py-4">
                                    <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                                    <p class="text-danger">Error: ${data.error}</p>
                                </div>
                            `;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        listaMedidores.innerHTML = `
                            <div class="text-center py-4">
                                <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                                <p class="text-danger">Error al cargar los medidores</p>
                            </div>
                        `;
                    });
            }

            // Manejar el envío del formulario de edición
            document.getElementById('formEditarDepartamento').addEventListener('submit', function(e) {
                e.preventDefault();
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

        .bg-purple {
            background-color: #6f42c1 !important;
        }

        .text-purple {
            color: #6f42c1 !important;
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

        .card-header {
            border-bottom: 1px solid rgba(0,0,0,0.125);
        }

        .btn-outline-info:hover {
            background-color: #0dcaf0;
            border-color: #0dcaf0;
            color: white;
        }
    </style>

<?php include("../../includes/footer.php"); ?>
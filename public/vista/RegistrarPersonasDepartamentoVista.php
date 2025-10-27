<?php include("../../includes/header.php"); ?>

    <!-- Page Header -->
    <div class="page-header fade-in">
        <div class="page-title">
            <h1>Asignar Personas a Departamentos</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item"><a href="../controlador/DepartamentoControlador.php?action=listarDepartamentos">Departamentos</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Asignar Personas</li>
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

    <!-- Contenido Principal -->
    <div class="row fade-in">
        <div class="col-12">
            <div class="content-box">
                <div class="content-box-header d-flex justify-content-between align-items-center">
                    <h5>Asignación Masiva de Personas</h5>
                    <span class="badge bg-primary" id="contadorAsignados">0 personas asignadas</span>
                </div>
                <div class="content-box-body">
                    <!-- Formulario de Departamento Seleccionado -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-building me-2"></i>Departamento Seleccionado</h6>
                        </div>
                        <div class="card-body">
                            <form id="frmDepartamento" class="row g-3 align-items-center">
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Número</label>
                                    <input type="text" class="form-control" id="depto_numero" readonly placeholder="Seleccione un departamento">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold">Piso</label>
                                    <input type="text" class="form-control" id="depto_piso" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Descripción</label>
                                    <input type="text" class="form-control" id="depto_desc" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Estado</label>
                                    <input type="text" class="form-control" id="depto_estado" readonly>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Panel de Personas Asignadas -->
                        <div class="col-lg-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <span>Personas Asignadas</span>
                                    <span class="badge bg-success" id="contadorLista">0</span>
                                </div>
                                <div class="card-body p-0">
                                    <table id="tablaAsignados" class="table table-sm table-hover mb-0">
                                        <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>CI</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <!-- Datos cargados dinámicamente -->
                                        </tbody>
                                    </table>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <button id="btnAgregarSeleccion" class="btn btn-primary btn-sm" disabled>
                                        <i class="fas fa-plus me-1"></i>Agregar seleccionados
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Panel de Departamentos -->
                        <div class="col-lg-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-list me-2"></i>Seleccione un Departamento</h6>
                                </div>
                                <div class="card-body p-0">
                                    <table id="tablaDeptos" class="table table-sm table-hover mb-0">
                                        <thead>
                                        <tr>
                                            <th>Número</th>
                                            <th>Piso</th>
                                            <th>Estado</th>
                                            <th class="text-center">Acción</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($departamentos as $depto): ?>
                                            <tr>
                                                <td>
                                                    <i class="fas fa-hashtag text-primary me-2"></i>
                                                    <?php echo htmlspecialchars($depto['numero']); ?>
                                                </td>
                                                <td>
                                                    <i class="fas fa-stairs text-info me-2"></i>
                                                    <?php echo htmlspecialchars($depto['piso']); ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $badge_class = $depto['estado'] == 'disponible' ? 'bg-success' : 'bg-warning';
                                                    $icon = $depto['estado'] == 'disponible' ? 'fa-check-circle' : 'fa-user';
                                                    ?>
                                                    <span class="badge <?php echo $badge_class; ?>">
                                                        <i class="fas <?php echo $icon; ?> me-1"></i>
                                                        <?php echo ucfirst(htmlspecialchars($depto['estado'])); ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <button class="btn btn-sm btn-outline-primary seleccionar-depto"
                                                            data-id="<?php echo $depto['id_departamento']; ?>"
                                                            data-numero="<?php echo htmlspecialchars($depto['numero']); ?>"
                                                            data-piso="<?php echo htmlspecialchars($depto['piso']); ?>"
                                                            data-estado="<?php echo htmlspecialchars($depto['estado']); ?>">
                                                        <i class="fas fa-check me-1"></i>Seleccionar
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Panel de Personas Disponibles -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0"><i class="fas fa-users me-2"></i>Seleccione Personas para Asignar</h6>
                            <div class="form-check">
                                <input type="checkbox" id="selAll" class="form-check-input">
                                <label for="selAll" class="form-check-label">Seleccionar todo</label>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <table id="tablaPersonas" class="table table-sm table-hover mb-0">
                                <thead>
                                <tr>
                                    <th class="text-center" style="width: 40px;">
                                        <input type="checkbox" id="selAllHeader">
                                    </th>
                                    <th>ID</th>
                                    <th>Nombre Completo</th>
                                    <th>CI</th>
                                    <th>Teléfono</th>
                                    <th>Email</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($personas as $persona): ?>
                                    <tr>
                                        <td class="text-center">
                                            <input type="checkbox" class="sel-persona" value="<?php echo $persona['id_persona']; ?>">
                                        </td>
                                        <td><?php echo htmlspecialchars($persona['id_persona']); ?></td>
                                        <td>
                                            <?php
                                            $nombreCompleto = '';
                                            if (isset($persona['nombre_completo'])) {
                                                $nombreCompleto = $persona['nombre_completo'];
                                            } else {
                                                $nombreCompleto = $persona['nombre'] . " " . $persona['apellido_paterno'] . " " . $persona['apellido_materno'];
                                            }
                                            echo htmlspecialchars($nombreCompleto);
                                            ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($persona['ci']); ?></td>
                                        <td><?php echo htmlspecialchars($persona['telefono']); ?></td>
                                        <td><?php echo htmlspecialchars($persona['email']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div id="resumenAsignacion" class="text-muted"></div>
                        <div>
                            <a href="../controlador/DepartamentoControlador.php?action=listarDepartamentos" class="btn btn-secondary me-2">
                                <i class="fas fa-arrow-left me-2"></i>Volver
                            </a>
                            <button id="btnGuardar" class="btn btn-success" disabled>
                                <i class="fas fa-save me-2"></i>Guardar Asignación
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmación -->
    <div class="modal fade" id="modalConfirmacion" tabindex="-1" aria-labelledby="modalConfirmacionLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalConfirmacionLabel">
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                        Confirmar Asignación
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="modalMensaje">
                        <!-- El mensaje se insertará aquí dinámicamente -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-success" id="btnConfirmarAsignacion">
                        <i class="fas fa-check me-2"></i>Confirmar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            let deptoSeleccionado = null;
            let asignados = [];

            // Inicializar DataTables - CONFIGURACIÓN CORRECTA
            const tablaDeptos = $('#tablaDeptos').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                pageLength: 5,
                lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                order: [[0, 'asc']],
                columnDefs: [
                    {
                        orderable: false,
                        targets: [3] // Columna de acción no ordenable
                    },
                    {
                        searchable: false,
                        targets: [3] // Columna de acción no buscable
                    }
                ],
                initComplete: function() {
                    $('.dataTables_filter input').addClass('form-control form-control-sm');
                    $('.dataTables_length select').addClass('form-select form-select-sm');
                }
            });

            const tablaPersonas = $('#tablaPersonas').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                pageLength: 5,
                lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                order: [[2, 'asc']], // Ordenar por nombre
                columnDefs: [
                    {
                        orderable: false,
                        targets: [0] // Checkbox no ordenable
                    },
                    {
                        searchable: false,
                        targets: [0] // Checkbox no buscable
                    }
                ],
                initComplete: function() {
                    $('.dataTables_filter input').addClass('form-control form-control-sm');
                    $('.dataTables_length select').addClass('form-select form-select-sm');
                }
            });

            const tablaAsignados = $('#tablaAsignados').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json',
                    emptyTable: "Sin personas asignadas",
                    zeroRecords: "No se encontraron registros"
                },
                pageLength: 5,
                lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                ordering: false, // No ordenable
                searching: false, // Sin búsqueda
                info: true, // Con información de registros
                paging: true, // Con paginación
                columns: [
                    { data: 'id' },
                    { data: 'nombre' },
                    { data: 'ci' },
                    {
                        data: null,
                        className: 'text-center',
                        render: function(data) {
                            return `<button class="btn btn-sm btn-outline-danger quitar-persona" data-id="${data.id}">
                                <i class="fas fa-times"></i>
                            </button>`;
                        }
                    }
                ],
                data: [] // Inicialmente vacío
            });

            // Seleccionar departamento
            $('#tablaDeptos').on('click', '.seleccionar-depto', function() {
                const id = $(this).data('id');
                const numero = $(this).data('numero');
                const piso = $(this).data('piso');
                const estado = $(this).data('estado');

                seleccionarDepto(id, numero, piso, estado);
            });

            function seleccionarDepto(id, numero, piso, estado) {
                deptoSeleccionado = { id, numero, piso, estado };

                // Actualizar formulario
                $('#depto_numero').val(numero);
                $('#depto_piso').val(piso);
                $('#depto_estado').val(estado);
                $('#depto_desc').val(`Departamento ${numero} - Piso ${piso}`);

                // Resaltar fila seleccionada
                $('#tablaDeptos tbody tr').removeClass('table-primary');
                $(this).closest('tr').addClass('table-primary');

                actualizarEstadoBotonGuardar();
                actualizarResumen();

                mostrarMensaje(`Departamento ${numero} seleccionado correctamente`, 'success');
            }

            // Seleccionar/deseleccionar todos
            $('#selAll, #selAllHeader').on('change', function() {
                const isChecked = this.checked;
                $('.sel-persona').prop('checked', isChecked);
                actualizarEstadoBotonAgregar();
            });

            // Actualizar estado del botón agregar cuando cambian los checkboxes
            $(document).on('change', '.sel-persona', function() {
                const totalCheckboxes = $('.sel-persona').length;
                const checkedCheckboxes = $('.sel-persona:checked').length;
                const allChecked = totalCheckboxes > 0 && totalCheckboxes === checkedCheckboxes;

                $('#selAll, #selAllHeader').prop('checked', allChecked);
                actualizarEstadoBotonAgregar();
            });

            function actualizarEstadoBotonAgregar() {
                const seleccionados = $('.sel-persona:checked').length;
                $('#btnAgregarSeleccion').prop('disabled', seleccionados === 0);
            }

            // Agregar personas seleccionadas
            $('#btnAgregarSeleccion').on('click', function() {
                if (!deptoSeleccionado) {
                    mostrarMensaje('Primero debe seleccionar un departamento', 'error');
                    return;
                }

                const ids = [];
                $('.sel-persona:checked').each(function() {
                    ids.push(parseInt($(this).val()));
                });

                if (ids.length === 0) {
                    mostrarMensaje('Seleccione al menos una persona', 'error');
                    return;
                }

                // Obtener datos de las personas seleccionadas
                const nuevasPersonas = [];
                $('.sel-persona:checked').each(function() {
                    const row = $(this).closest('tr');
                    const id = parseInt($(this).val());
                    const idPersona = row.find('td:eq(1)').text();
                    const nombre = row.find('td:eq(2)').text();
                    const ci = row.find('td:eq(3)').text();

                    if (!asignados.find(p => p.id === id)) {
                        nuevasPersonas.push({ id, idPersona, nombre, ci });
                    }
                });

                if (nuevasPersonas.length === 0) {
                    mostrarMensaje('Las personas seleccionadas ya están en la lista', 'info');
                    return;
                }

                asignados.push(...nuevasPersonas);
                renderAsignados();

                // Desmarcar checkboxes
                $('.sel-persona').prop('checked', false);
                $('#selAll, #selAllHeader').prop('checked', false);
                actualizarEstadoBotonAgregar();

                mostrarMensaje(`${nuevasPersonas.length} persona(s) agregada(s) correctamente`, 'success');
            });

            // Quitar persona asignada
            $('#tablaAsignados').on('click', '.quitar-persona', function() {
                const id = parseInt($(this).data('id'));
                const persona = asignados.find(p => p.id === id);
                asignados = asignados.filter(p => p.id !== id);
                renderAsignados();
                mostrarMensaje(`"${persona.nombre}" removido de la lista`, 'info');
            });

            // Renderizar lista de asignados
            function renderAsignados() {
                // Actualizar DataTable de asignados
                tablaAsignados.clear().rows.add(asignados).draw();

                // Actualizar contadores
                $('#contadorLista').text(asignados.length);
                $('#contadorAsignados').text(`${asignados.length} personas asignadas`);
                actualizarEstadoBotonGuardar();
                actualizarResumen();
            }

            function actualizarEstadoBotonGuardar() {
                $('#btnGuardar').prop('disabled', !deptoSeleccionado || asignados.length === 0);
            }

            function actualizarResumen() {
                const resumen = $('#resumenAsignacion');
                if (deptoSeleccionado && asignados.length > 0) {
                    resumen.html(`
                <span class="fw-semibold">Resumen:</span>
                Asignando ${asignados.length} persona(s) al departamento ${deptoSeleccionado.numero}
            `);
                } else {
                    resumen.html('');
                }
            }

            // Guardar asignación - MODIFICADO PARA USAR MODAL
            $('#btnGuardar').on('click', function() {
                if (!deptoSeleccionado) {
                    mostrarMensaje('Seleccione un departamento', 'error');
                    return;
                }

                if (asignados.length === 0) {
                    mostrarMensaje('Asigne al menos una persona', 'error');
                    return;
                }

                // Preparar mensaje del modal
                const mensaje = deptoSeleccionado.estado === 'ocupado' ?
                    `<div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>ADVERTENCIA:</strong> El departamento ${deptoSeleccionado.numero} ya está ocupado.
                    </div>
                    <p class="mb-0">¿Desea reemplazar a los ocupantes actuales?</p>` :
                    `<p class="mb-0">¿Confirmar asignación de ${asignados.length} persona(s) al departamento ${deptoSeleccionado.numero}?</p>`;

                // Mostrar modal
                $('#modalMensaje').html(mensaje);
                $('#modalConfirmacion').modal('show');
            });

            // Confirmar asignación desde el modal
            $('#btnConfirmarAsignacion').on('click', function() {
                // Cerrar modal
                $('#modalConfirmacion').modal('hide');

                // Mostrar loading
                const btn = $('#btnGuardar');
                const originalText = btn.html();
                btn.html('<i class="fas fa-spinner fa-spin me-2"></i>Guardando...');
                btn.prop('disabled', true);

                // Enviar datos mediante formulario tradicional
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '../controlador/DepartamentoControlador.php';

                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'asignarPersonasDepartamento';
                form.appendChild(actionInput);

                const deptoInput = document.createElement('input');
                deptoInput.type = 'hidden';
                deptoInput.name = 'id_departamento';
                deptoInput.value = deptoSeleccionado.id;
                form.appendChild(deptoInput);

                const personasInput = document.createElement('input');
                personasInput.type = 'hidden';
                personasInput.name = 'personas';
                personasInput.value = JSON.stringify(asignados.map(p => p.id));
                form.appendChild(personasInput);

                document.body.appendChild(form);
                form.submit();
            });

            function mostrarMensaje(mensaje, tipo) {
                // Crear alerta temporal
                const alertClass = tipo === 'error' ? 'alert-danger' : 'alert-success';
                const icon = tipo === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle';

                const alertDiv = $(`
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                <i class="fas ${icon} me-2"></i>
                ${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);

                $('.content-box-body').prepend(alertDiv);

                // Auto-remover después de 5 segundos
                setTimeout(() => {
                    alertDiv.alert('close');
                }, 5000);
            }

            // Auto-ocultar alertas del servidor
            setTimeout(() => {
                $('.alert-dismissible').alert('close');
            }, 5000);
        });
    </script>

    <style>
        .table-primary {
            background-color: #e3f2fd !important;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }

        .content-box-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 1px solid #dee2e6;
        }

        .badge {
            font-size: 0.75rem;
        }

        .form-control[readonly] {
            background-color: #f8f9fa;
        }

        /* DataTables custom styles */
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 0.375rem 0.75rem;
        }

        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
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

        /* Asegurar que DataTables funcione correctamente */
        .dataTables_wrapper {
            padding: 15px;
        }

        table.dataTable {
            margin-bottom: 0 !important;
        }

    </style>

<?php include("../../includes/footer.php"); ?>
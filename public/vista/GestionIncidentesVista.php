<?php include("../../includes/header.php");?>

<main class="main-content">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="page-header fade-in">
            <div class="page-title">
                <h1>Gestión de Incidentes</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Incidentes</li>
                    </ol>
                </nav>
            </div>
            <div class="page-actions">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoIncidente">
                    <i class="fas fa-plus me-2"></i> Nuevo Incidente
                </button>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="row fade-in">
            <div class="col-xl-3 col-md-6">
                <div class="info-card bg-gradient-celeste">
                    <div>
                        <h3>8</h3>
                        <p>Pendientes</p>
                    </div>
                    <i class="fas fa-clock icon"></i>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="info-card bg-gradient-verde">
                    <div>
                        <h3>12</h3>
                        <p>En Proceso</p>
                    </div>
                    <i class="fas fa-tools icon"></i>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="info-card bg-gradient-azul">
                    <div>
                        <h3>25</h3>
                        <p>Resueltos</p>
                    </div>
                    <i class="fas fa-check-circle icon"></i>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="info-card bg-gradient-oscuro">
                    <div>
                        <h3>45</h3>
                        <p>Total Incidentes</p>
                    </div>
                    <i class="fas fa-list icon"></i>
                </div>
            </div>
        </div>

        <!-- Tabla de Incidentes -->
        <div class="row fade-in">
            <div class="col-12">
                <div class="content-box">
                    <div class="content-box-header">
                        <h5>Lista de Incidentes</h5>
                        <div class="box-actions">
                            <select class="form-select form-select-sm">
                                <option>Todos los estados</option>
                                <option>Pendientes</option>
                                <option>En proceso</option>
                                <option>Resueltos</option>
                            </select>
                        </div>
                    </div>
                    <div class="content-box-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th># Incidente</th>
                                        <th>Título</th>
                                        <th>Departamento</th>
                                        <th>Fecha Reporte</th>
                                        <th>Estado</th>
                                        <th>Asignado a</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>#INC-7842</td>
                                        <td>Fuga de agua en baño</td>
                                        <td>Departamento 302</td>
                                        <td>12/05/2023</td>
                                        <td><span class="status-badge badge-warning">En Proceso</span></td>
                                        <td>Juan Pérez</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalDetalleIncidente">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>#INC-7841</td>
                                        <td>Problema eléctrico</td>
                                        <td>Departamento 105</td>
                                        <td>11/05/2023</td>
                                        <td><span class="status-badge badge-success">Resuelto</span></td>
                                        <td>María García</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalDetalleIncidente">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modal Nuevo Incidente -->
<div class="modal fade" id="modalNuevoIncidente" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reportar Nuevo Incidente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Título del Incidente</label>
                                <input type="text" class="form-control" placeholder="Ej: Fuga de agua en baño">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Departamento</label>
                                <select class="form-select">
                                    <option>Seleccionar departamento</option>
                                    <option>Departamento 101</option>
                                    <option>Departamento 102</option>
                                    <option>Departamento 201</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción Detallada</label>
                        <textarea class="form-control" rows="4" placeholder="Describa el problema en detalle..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Prioridad</label>
                        <select class="form-select">
                            <option>Baja</option>
                            <option>Media</option>
                            <option>Alta</option>
                            <option>Urgente</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Reportar Incidente</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detalle Incidente -->
<div class="modal fade" id="modalDetalleIncidente" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle del Incidente #INC-7842</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Información General</h6>
                        <p><strong>Título:</strong> Fuga de agua en baño</p>
                        <p><strong>Departamento:</strong> 302</p>
                        <p><strong>Fecha Reporte:</strong> 12/05/2023</p>
                        <p><strong>Estado:</strong> <span class="status-badge badge-warning">En Proceso</span></p>
                    </div>
                    <div class="col-md-6">
                        <h6>Asignación</h6>
                        <p><strong>Asignado a:</strong> Juan Pérez</p>
                        <p><strong>Especialidad:</strong> Plomería</p>
                        <p><strong>Fecha Asignación:</strong> 12/05/2023</p>
                    </div>
                </div>
                <div class="mt-3">
                    <h6>Descripción</h6>
                    <p>Se reporta fuga constante de agua en el grifo principal del baño. El agua gotea aproximadamente 1 litro por hora.</p>
                </div>
                <div class="mt-3">
                    <h6>Progreso</h6>
                    <div class="progress mb-2">
                        <div class="progress-bar bg-warning" style="width: 65%">65%</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary">Actualizar Estado</button>
            </div>
        </div>
    </div>
</div>

<?php include("../../includes/footer.php");?>

<?php include("../../includes/header.php");?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container-fluid">
            <!-- Page Header -->
            <div class="page-header fade-in">
                <div class="page-title">
                    <h1>Dashboard del Sistema</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Dashboard Principal</li>
                        </ol>
                    </nav>
                </div>
                <div class="page-actions">
                    <button class="btn btn-primary" style="background: var(--verde); border: none;">
                        <i class="fas fa-plus me-2"></i> Nuevo Reporte
                    </button>
                </div>
            </div>

            <!-- Info Cards -->
            <div class="row fade-in">
                <div class="col-xl-3 col-md-6">
                    <div class="info-card bg-gradient-celeste">
                        <div>
                            <h3>15</h3>
                            <p>Incidentes Activos</p>
                        </div>
                        <div class="card-progress">
                            <div class="card-progress-bar" style="width: 65%"></div>
                        </div>
                        <i class="fas fa-exclamation-triangle icon"></i>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="info-card bg-gradient-verde">
                        <div>
                            <h3>42</h3>
                            <p>Departamentos</p>
                        </div>
                        <div class="card-progress">
                            <div class="card-progress-bar" style="width: 78%"></div>
                        </div>
                        <i class="fas fa-building icon"></i>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="info-card bg-gradient-azul">
                        <div>
                            <h3>8</h3>
                            <p>Personal Activo</p>
                        </div>
                        <div class="card-progress">
                            <div class="card-progress-bar" style="width: 45%"></div>
                        </div>
                        <i class="fas fa-users icon"></i>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="info-card bg-gradient-oscuro pulse">
                        <div>
                            <h3>5</h3>
                            <p>Facturas Pendientes</p>
                        </div>
                        <div class="card-progress">
                            <div class="card-progress-bar" style="width: 30%"></div>
                        </div>
                        <i class="fas fa-file-invoice-dollar icon"></i>
                    </div>
                </div>
            </div>

            <!-- Charts and Activity Row -->
            <div class="row fade-in">
                <div class="col-lg-8">
                    <div class="content-box">
                        <div class="content-box-header">
                            <h5>Incidentes por Departamento</h5>
                            <div class="box-actions">
                                <select class="form-select form-select-sm">
                                    <option>Última semana</option>
                                    <option>Último mes</option>
                                    <option>Último trimestre</option>
                                </select>
                            </div>
                        </div>
                        <div class="content-box-body">
                            <div class="chart-container">
                                <canvas id="incidentsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="content-box">
                        <div class="content-box-header">
                            <h5>Actividad Reciente</h5>
                        </div>
                        <div class="content-box-body">
                            <div class="activity-timeline">
                                <div class="timeline-item">
                                    <div class="timeline-time">Hace 5 minutos</div>
                                    <div class="timeline-content">
                                        <strong>Incidente reportado</strong>
                                        <p>Fuga de agua en departamento 302</p>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-time">Hace 15 minutos</div>
                                    <div class="timeline-content">
                                        <strong>Factura generada</strong>
                                        <p>Factura mensual de mantenimiento</p>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-time">Hace 1 hora</div>
                                    <div class="timeline-content">
                                        <strong>Personal asignado</strong>
                                        <p>Juan Pérez asignado a reparación eléctrica</p>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-time">Hace 2 horas</div>
                                    <div class="timeline-content">
                                        <strong>Comunicado publicado</strong>
                                        <p>Corte programado de agua este viernes</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Incidents -->
            <div class="row fade-in">
                <div class="col-12">
                    <div class="content-box">
                        <div class="content-box-header">
                            <h5>Incidentes Recientes</h5>
                            <div class="box-actions">
                                <button class="btn btn-sm btn-outline-primary">Ver Todos</button>
                            </div>
                        </div>
                        <div class="content-box-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th># Incidente</th>
                                            <th>Departamento</th>
                                            <th>Tipo</th>
                                            <th>Fecha Reporte</th>
                                            <th>Estado</th>
                                            <th>Asignado a</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>#INC-7842</td>
                                            <td>Departamento 302</td>
                                            <td>Fuga de agua</td>
                                            <td>12/05/2023</td>
                                            <td><span class="status-badge badge-warning">En Proceso</span></td>
                                            <td>Juan Pérez</td>
                                        </tr>
                                        <tr>
                                            <td>#INC-7841</td>
                                            <td>Departamento 105</td>
                                            <td>Problema eléctrico</td>
                                            <td>11/05/2023</td>
                                            <td><span class="status-badge badge-success">Resuelto</span></td>
                                            <td>María García</td>
                                        </tr>
                                        <tr>
                                            <td>#INC-7840</td>
                                            <td>Área común</td>
                                            <td>Ascensor atascado</td>
                                            <td>10/05/2023</td>
                                            <td><span class="status-badge badge-success">Resuelto</span></td>
                                            <td>Carlos López</td>
                                        </tr>
                                        <tr>
                                            <td>#INC-7839</td>
                                            <td>Departamento 408</td>
                                            <td>Cerradura dañada</td>
                                            <td>09/05/2023</td>
                                            <td><span class="status-badge badge-danger">Pendiente</span></td>
                                            <td>-</td>
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

<?php include("../../includes/footer.php");?>
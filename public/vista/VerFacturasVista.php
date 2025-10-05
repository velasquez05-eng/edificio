<?php include("../../includes/header.php");?>

<main class="main-content">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="page-header fade-in">
            <div class="page-title">
                <h1>Gestión de Facturas</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Facturas</li>
                    </ol>
                </nav>
            </div>
            <div class="page-actions">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalGenerarFactura">
                    <i class="fas fa-file-invoice me-2"></i> Generar Factura
                </button>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="row fade-in">
            <div class="col-xl-3 col-md-6">
                <div class="info-card bg-gradient-celeste">
                    <div>
                        <h3>5</h3>
                        <p>Pendientes de Pago</p>
                    </div>
                    <i class="fas fa-clock icon"></i>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="info-card bg-gradient-verde">
                    <div>
                        <h3>18</h3>
                        <p>Pagadas Este Mes</p>
                    </div>
                    <i class="fas fa-check-circle icon"></i>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="info-card bg-gradient-azul">
                    <div>
                        <h3>₡ 125,400</h3>
                        <p>Total Recaudado</p>
                    </div>
                    <i class="fas fa-money-bill-wave icon"></i>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="info-card bg-gradient-oscuro">
                    <div>
                        <h3>3</h3>
                        <p>Vencidas</p>
                    </div>
                    <i class="fas fa-exclamation-triangle icon"></i>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="row fade-in">
            <div class="col-12">
                <div class="content-box">
                    <div class="content-box-header">
                        <h5>Filtros de Búsqueda</h5>
                    </div>
                    <div class="content-box-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Estado</label>
                                <select class="form-select">
                                    <option>Todos</option>
                                    <option>Pendiente</option>
                                    <option>Pagada</option>
                                    <option>Vencida</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Mes</label>
                                <select class="form-select">
                                    <option>Todos los meses</option>
                                    <option>Enero 2024</option>
                                    <option>Febrero 2024</option>
                                    <option>Marzo 2024</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Departamento</label>
                                <select class="form-select">
                                    <option>Todos los departamentos</option>
                                    <option>Departamento 101</option>
                                    <option>Departamento 102</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <button class="btn btn-primary w-100">Aplicar Filtros</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Facturas -->
        <div class="row fade-in">
            <div class="col-12">
                <div class="content-box">
                    <div class="content-box-header">
                        <h5>Lista de Facturas</h5>
                        <div class="box-actions">
                            <button class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-download me-1"></i> Exportar
                            </button>
                        </div>
                    </div>
                    <div class="content-box-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th># Factura</th>
                                        <th>Departamento</th>
                                        <th>Fecha Emisión</th>
                                        <th>Fecha Vencimiento</th>
                                        <th>Monto</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>#FAC-2024-001</td>
                                        <td>Departamento 302</td>
                                        <td>01/03/2024</td>
                                        <td>15/03/2024</td>
                                        <td>₡ 45,200</td>
                                        <td><span class="status-badge badge-warning">Pendiente</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalDetalleFactura">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-print"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>#FAC-2024-002</td>
                                        <td>Departamento 105</td>
                                        <td>01/03/2024</td>
                                        <td>15/03/2024</td>
                                        <td>₡ 38,750</td>
                                        <td><span class="status-badge badge-success">Pagada</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalDetalleFactura">
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

<!-- Modal Generar Factura -->
<div class="modal fade" id="modalGenerarFactura" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Generar Nueva Factura</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Departamento</label>
                                <select class="form-select" required>
                                    <option value="">Seleccionar departamento</option>
                                    <option>Departamento 101</option>
                                    <option>Departamento 102</option>
                                    <option>Departamento 201</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Período Facturado</label>
                                <select class="form-select" required>
                                    <option value="">Seleccionar período</option>
                                    <option>Marzo 2024</option>
                                    <option>Febrero 2024</option>
                                    <option>Enero 2024</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Fecha Emisión</label>
                                <input type="date" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Fecha Vencimiento</label>
                                <input type="date" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Conceptos</label>
                        <div class="border rounded p-3">
                            <div class="row mb-2">
                                <div class="col-6"><strong>Mantenimiento</strong></div>
                                <div class="col-6 text-end">₡ 15,000</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6"><strong>Agua</strong></div>
                                <div class="col-6 text-end">₡ 8,500</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6"><strong>Luz</strong></div>
                                <div class="col-6 text-end">₡ 12,300</div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-6"><strong>Total</strong></div>
                                <div class="col-6 text-end"><strong>₡ 35,800</strong></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Generar Factura</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detalle Factura -->
<div class="modal fade" id="modalDetalleFactura" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Factura #FAC-2024-001</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Información de Factura</h6>
                        <p><strong>Departamento:</strong> 302</p>
                        <p><strong>Propietario:</strong> María Rodríguez</p>
                        <p><strong>Fecha Emisión:</strong> 01/03/2024</p>
                        <p><strong>Fecha Vencimiento:</strong> 15/03/2024</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Estado de Pago</h6>
                        <p><strong>Estado:</strong> <span class="status-badge badge-warning">Pendiente</span></p>
                        <p><strong>Monto Total:</strong> ₡ 45,200</p>
                        <p><strong>Días Restantes:</strong> 5 días</p>
                    </div>
                </div>
                <div class="mt-4">
                    <h6>Desglose de Conceptos</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Concepto</th>
                                    <th>Descripción</th>
                                    <th class="text-end">Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Mantenimiento</td>
                                    <td>Mantenimiento mensual edificio</td>
                                    <td class="text-end">₡ 20,000</td>
                                </tr>
                                <tr>
                                    <td>Agua</td>
                                    <td>Consumo mes de febrero</td>
                                    <td class="text-end">₡ 12,500</td>
                                </tr>
                                <tr>
                                    <td>Luz áreas comunes</td>
                                    <td>Iluminación áreas comunes</td>
                                    <td class="text-end">₡ 8,200</td>
                                </tr>
                                <tr>
                                    <td>Fondo reserva</td>
                                    <td>Aporte fondo de reserva</td>
                                    <td class="text-end">₡ 4,500</td>
                                </tr>
                                <tr class="table-active">
                                    <td colspan="2"><strong>TOTAL</strong></td>
                                    <td class="text-end"><strong>₡ 45,200</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary">Registrar Pago</button>
                <button type="button" class="btn btn-outline-primary">
                    <i class="fas fa-print me-1"></i> Imprimir
                </button>
            </div>
        </div>
    </div>
</div>

<?php include("../../includes/footer.php");?>
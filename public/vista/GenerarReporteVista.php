<?php include("../../includes/header.php");?>

<main class="main-content">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="page-header fade-in">
            <div class="page-title">
                <h1>Generar Reportes</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Reportes</li>
                    </ol>
                </nav>
            </div>
            <div class="page-actions">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoReporte">
                    <i class="fas fa-plus me-2"></i> Nuevo Reporte
                </button>
            </div>
        </div>

        <!-- Tipos de Reporte -->
        <div class="row fade-in">
            <div class="col-xl-3 col-md-6">
                <div class="info-card bg-gradient-celeste clickable-card" data-bs-toggle="modal" data-bs-target="#modalReporteFinanciero">
                    <div>
                        <h3>Financiero</h3>
                        <p>Estado de cuentas y pagos</p>
                    </div>
                    <i class="fas fa-chart-line icon"></i>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="info-card bg-gradient-verde clickable-card" data-bs-toggle="modal" data-bs-target="#modalReporteIncidentes">
                    <div>
                        <h3>Incidentes</h3>
                        <p>Reportes de mantenimiento</p>
                    </div>
                    <i class="fas fa-tools icon"></i>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="info-card bg-gradient-azul clickable-card" data-bs-toggle="modal" data-bs-target="#modalReporteConsumo">
                    <div>
                        <h3>Consumo</h3>
                        <p>Servicios y recursos</p>
                    </div>
                    <i class="fas fa-tint icon"></i>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="info-card bg-gradient-oscuro clickable-card" data-bs-toggle="modal" data-bs-target="#modalReporteGeneral">
                    <div>
                        <h3>General</h3>
                        <p>Reporte completo</p>
                    </div>
                    <i class="fas fa-file-alt icon"></i>
                </div>
            </div>
        </div>

        <!-- Reportes Recientes -->
        <div class="row fade-in">
            <div class="col-12">
                <div class="content-box">
                    <div class="content-box-header">
                        <h5>Reportes Generados Recientemente</h5>
                        <div class="box-actions">
                            <button class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-sync me-1"></i> Actualizar
                            </button>
                        </div>
                    </div>
                    <div class="content-box-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th># Reporte</th>
                                        <th>Tipo</th>
                                        <th>Período</th>
                                        <th>Generado por</th>
                                        <th>Fecha Generación</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>#REP-2024-015</td>
                                        <td>Financiero Mensual</td>
                                        <td>Febrero 2024</td>
                                        <td>Admin User</td>
                                        <td>01/03/2024</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-download"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>#REP-2024-014</td>
                                        <td>Incidentes Trimestral</td>
                                        <td>Q4 2023</td>
                                        <td>Admin User</td>
                                        <td>15/01/2024</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-download"></i>
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

<!-- Modal Nuevo Reporte -->
<div class="modal fade" id="modalNuevoReporte" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Configurar Nuevo Reporte</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tipo de Reporte</label>
                                <select class="form-select" required>
                                    <option value="">Seleccionar tipo</option>
                                    <option>Financiero</option>
                                    <option>Incidentes</option>
                                    <option>Consumo de Servicios</option>
                                    <option>Reporte General</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Formato</label>
                                <select class="form-select" required>
                                    <option value="">Seleccionar formato</option>
                                    <option>PDF</option>
                                    <option>Excel</option>
                                    <option>HTML</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Fecha Inicio</label>
                                <input type="date" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Fecha Fin</label>
                                <input type="date" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Filtros Adicionales</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="incluirGraficos">
                            <label class="form-check-label" for="incluirGraficos">
                                Incluir gráficos y estadísticas
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="incluirDetalles">
                            <label class="form-check-label" for="incluirDetalles">
                                Incluir detalles completos
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="soloActivos">
                            <label class="form-check-label" for="soloActivos">
                                Solo departamentos activos
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Generar Reporte</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Reporte Financiero -->
<div class="modal fade" id="modalReporteFinanciero" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Vista Previa - Reporte Financiero</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <h4>Reporte Financiero - Marzo 2024</h4>
                    <p class="text-muted">Generado el 15/03/2024</p>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h5>₡ 450,200</h5>
                                <p>Total Facturado</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h5>₡ 380,150</h5>
                                <p>Total Recaudado</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h5>₡ 70,050</h5>
                                <p>Pendiente</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h5>84%</h5>
                                <p>Tasa de Cobro</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="chart-container mb-4">
                    <canvas id="chartFinancieroPreview" height="100"></canvas>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary">
                    <i class="fas fa-download me-1"></i> Descargar PDF
                </button>
                <button type="button" class="btn btn-success">
                    <i class="fas fa-file-excel me-1"></i> Descargar Excel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Hacer las tarjetas clickeables
    document.querySelectorAll('.clickable-card').forEach(card => {
        card.style.cursor = 'pointer';
    });

    // Gráfico de preview financiero
    const ctx = document.getElementById('chartFinancieroPreview').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
            datasets: [{
                label: 'Facturado',
                data: [320000, 380000, 450000, 420000, 480000, 520000],
                backgroundColor: '#4e73df'
            }, {
                label: 'Recaudado',
                data: [280000, 320000, 380000, 350000, 420000, 480000],
                backgroundColor: '#1cc88a'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top'
                }
            }
        }
    });
});
</script>

<?php include("../../includes/footer.php");?>
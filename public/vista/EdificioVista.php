<?php include("../../includes/header.php");?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container-fluid">
            <!-- Page Header -->
            <div class="page-header fade-in">
                <div class="page-title">
                    <h1>Lista de Edificios</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edificios</li>
                        </ol>
                    </nav>
                </div>
                <div class="page-actions">
                    <span class="text-muted">Vista General</span>
                </div>
            </div>

            <!-- Info Card -->
            <div class="row fade-in">
                <div class="col-12">
                    <div class="info-card bg-gradient-verde">
                        <div>
                            <h3>5</h3>
                            <p>Edificios Registrados en el Sistema</p>
                        </div>
                        <div class="card-progress">
                            <div class="card-progress-bar" style="width: 100%"></div>
                        </div>
                        <i class="fas fa-building icon"></i>
                    </div>
                </div>
            </div>

            <!-- Tabla de Edificios -->
            <div class="row fade-in">
                <div class="col-12">
                    <div class="content-box">
                        <div class="content-box-header">
                            <h5>Información de Edificios</h5>
                            <div class="box-actions">
                                <input type="text" class="form-control form-control-sm" placeholder="Buscar edificio..." id="buscarEdificio">
                            </div>
                        </div>
                        <div class="content-box-body">
                            <div class="table-responsive">
                                <table class="table table-hover" id="tablaEdificios">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre del Edificio</th>
                                            <th>Dirección</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>1</strong></td>
                                            <td>
                                                <i class="fas fa-building text-primary me-2"></i>
                                                Edificio Principal
                                            </td>
                                            <td>
                                                <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                                Av. Principal #123, Ciudad
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>2</strong></td>
                                            <td>
                                                <i class="fas fa-building text-primary me-2"></i>
                                                Torre Norte
                                            </td>
                                            <td>
                                                <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                                Calle Secundaria #456, Ciudad
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>3</strong></td>
                                            <td>
                                                <i class="fas fa-building text-primary me-2"></i>
                                                Complejo Residencial Sur
                                            </td>
                                            <td>
                                                <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                                Av. Los Álamos #789, Ciudad
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>4</strong></td>
                                            <td>
                                                <i class="fas fa-building text-primary me-2"></i>
                                                Edificio Corporativo
                                            </td>
                                            <td>
                                                <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                                Zona Comercial #321, Ciudad
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>5</strong></td>
                                            <td>
                                                <i class="fas fa-building text-primary me-2"></i>
                                                Residencial Las Flores
                                            </td>
                                            <td>
                                                <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                                Urbanización Jardín #654, Ciudad
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estadísticas Adicionales -->
            <div class="row fade-in">
                <div class="col-md-4">
                    <div class="content-box">
                        <div class="content-box-header">
                            <h6>Resumen</h6>
                        </div>
                        <div class="content-box-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Total de Edificios:</span>
                                <strong>5</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Registrados este mes:</span>
                                <strong>1</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Activos:</span>
                                <strong>5</strong>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="content-box">
                        <div class="content-box-header">
                            <h6>Distribución por Zonas</h6>
                        </div>
                        <div class="content-box-body">
                            <div class="chart-container">
                                <canvas id="zonasChart" height="100"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Script para búsqueda -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Buscar edificios
        document.getElementById('buscarEdificio').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#tablaEdificios tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // Gráfico simple de distribución
        const ctx = document.getElementById('zonasChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Zona Norte', 'Zona Sur', 'Zona Centro', 'Zona Este'],
                datasets: [{
                    data: [2, 1, 1, 1],
                    backgroundColor: [
                        '#4e73df',
                        '#1cc88a',
                        '#36b9cc',
                        '#f6c23e'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    });
    </script>

<?php include("../../includes/footer.php");?>
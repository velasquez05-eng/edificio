
<?php

session_start();
if (!isset($_SESSION['id_persona'])) {
    echo "REDIRIGIENDO - id_persona no está en sesión";
    header('Location: ../vista/LoginVista.php?error=Debe iniciar sesión');
    exit;
}
// Verificación de sesión y rol - SIN session_start()
// Obtener datos del usuario desde la sesión - DEFINIR LAS VARIABLES PRIMERO
$nombre_usuario = $_SESSION['nombre'] ?? 'Residente';
$apellido_usuario = $_SESSION['apellido_paterno'] ?? '';
$rol_usuario = $_SESSION['rol_nombre'] ?? 'Residente';
$nombre_completo = trim($nombre_usuario . ' ' . $apellido_usuario);
?>

<?php include("../../includes/header.php"); ?>

    <!-- Page Header -->
    <div class="page-header fade-in">
        <div class="page-title">
            <h1>Dashboard del Residente</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Dashboard Residente</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Contenido Principal del Dashboard Residente -->
    <div class="container-fluid">

        <!-- Mensajes de alerta -->
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php echo htmlspecialchars($_GET['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo htmlspecialchars($_GET['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Tarjetas de Resumen -->
        <div class="row">
            <!-- Facturas Pendientes -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Facturas Pendientes
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">3</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-file-invoice-dollar fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reservas Activas -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Reservas Activas
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">2</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Incidentes Reportados -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Incidentes Activos
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">1</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Consumo Mensual -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Consumo Agua (m³)
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">15.2</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-tint fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección de Acciones Rápidas -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Acciones Rápidas</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3 mb-3">
                                <a href="#" class="btn btn-outline-primary btn-lg w-100">
                                    <i class="fas fa-file-invoice-dollar fa-2x mb-2"></i><br>
                                    Ver Facturas
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="#" class="btn btn-outline-success btn-lg w-100">
                                    <i class="fas fa-calendar-plus fa-2x mb-2"></i><br>
                                    Reservar Área
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="#" class="btn btn-outline-warning btn-lg w-100">
                                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i><br>
                                    Reportar Incidente
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="#" class="btn btn-outline-info btn-lg w-100">
                                    <i class="fas fa-chart-line fa-2x mb-2"></i><br>
                                    Ver Consumos
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información Reciente -->
        <div class="row">
            <!-- Facturas Recientes -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Facturas Recientes</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                <tr>
                                    <th>Servicio</th>
                                    <th>Monto</th>
                                    <th>Vencimiento</th>
                                    <th>Estado</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>Mantenimiento</td>
                                    <td>S/ 150.00</td>
                                    <td>15/10/2024</td>
                                    <td><span class="badge bg-warning">Pendiente</span></td>
                                </tr>
                                <tr>
                                    <td>Agua</td>
                                    <td>S/ 45.50</td>
                                    <td>10/10/2024</td>
                                    <td><span class="badge bg-success">Pagado</span></td>
                                </tr>
                                <tr>
                                    <td>Luz</td>
                                    <td>S/ 89.30</td>
                                    <td>12/10/2024</td>
                                    <td><span class="badge bg-success">Pagado</span></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reservas Próximas -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-success">Mis Reservas</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                <tr>
                                    <th>Área</th>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Estado</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>Sala de Eventos</td>
                                    <td>20/10/2024</td>
                                    <td>15:00 - 17:00</td>
                                    <td><span class="badge bg-success">Confirmada</span></td>
                                </tr>
                                <tr>
                                    <td>Gimnasio</td>
                                    <td>18/10/2024</td>
                                    <td>08:00 - 09:00</td>
                                    <td><span class="badge bg-warning">Pendiente</span></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información Adicional -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Bienvenido, <?php echo htmlspecialchars($nombre_usuario); ?></h6>
                    </div>
                    <div class="card-body">
                        <p>Desde este dashboard puedes:</p>
                        <ul>
                            <li>Revisar y pagar tus facturas pendientes</li>
                            <li>Gestionar reservas de áreas comunes</li>
                            <li>Reportar incidentes o problemas</li>
                            <li>Consultar tu consumo de servicios</li>
                            <li>Ver notificaciones y comunicados</li>
                        </ul>
                        <p class="mb-0">Utiliza el menú lateral para acceder a todas las funcionalidades disponibles.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include("../../includes/footer.php"); ?>
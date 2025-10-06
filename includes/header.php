
<?php
session_start();

// Verificar si está logueado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../public/vista/LoginVista.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clarity Systems</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Google Fonts -->
       <link rel="stylesheet" href="../../includes/css/dashboard.css">
    <!-- Estilos CSS dashboard -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Chart.js -->
     
  
    
     <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
</head>
<body>
    <!-- Header -->
<header class="header">
    <div class="d-flex align-items-center">
        <button class="mobile-menu-toggle me-3">
            <i class="fas fa-bars"></i>
        </button>
        <a href="../vista/DashboardVista.php" class="navbar-brand">
            <i class="fas fa-building"></i>
            <span class="d-none d-sm-inline">Clarity Systems</span>
        </a>
    </div>
    <div class="header-controls">
        <button class="btn-notification">
            <i class="fas fa-bell"></i>
            <span class="notification-badge">5</span>
        </button>
        
        <div class="user-profile">
            <div class="user-avatar"><?php echo $_SESSION['avatar']; ?></div>
            <div class="user-info">
                <div class="user-name"><?php echo $_SESSION['nombre']." ".$_SESSION['appaterno']." ".$_SESSION['apmaterno'];?></div>
                <?php if($_SESSION['tipo_usuario'] == 'Personal'): ?>
                <div class="user-role"><?php echo $_SESSION['rol']; ?></div>
                <?php endif; ?>
            </div>
            <i class="fas fa-chevron-down ms-2" style="font-size: 0.9rem;"></i>
            <div class="user-dropdown">
                <a href="../vista/PerfilVista.php" class="user-dropdown-item">
                    <i class="fas fa-user"></i>
                    <span>Mi Perfil</span>
                </a>
                <div class="user-dropdown-divider"></div>
                <!-- Enlace para cerrar sesión con modal - CORREGIDO -->
                <a href="#" class="user-dropdown-item" data-bs-toggle="modal" data-bs-target="#logoutModal">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Cerrar Sesión</span>
                </a>
            </div>
        </div>
    </div>
</header>

<!-- Modal de confirmación para cerrar sesión - DEBE ESTAR FUERA DEL HEADER -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logoutModalLabel">
                    <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center py-3">
                    <div class="mb-4">
                        <i class="fas fa-sign-out-alt fa-4x text-warning"></i>
                    </div>
                    <h4 class="mb-3">¿Estás seguro de que deseas cerrar sesión?</h4>
                    <p class="text-muted">Serás redirigido a la página de inicio de sesión.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <form method="POST" action="../controlador/LoginControlador.php" class="d-inline">
                    <input type="hidden" name="action" value="logout">
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-sign-out-alt me-2"></i>Sí, Cerrar Sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

    <!-- Sidebar -->
    <aside class="sidebar">
        <button class="sidebar-toggle">
            <i class="fas fa-chevron-right"></i>
        </button>
        <div class="sidebar-brand">
            <span class="fw-light">Edificio Bilbao</span>
        </div>
        
        <div class="sidebar-wrapper">
            <nav class="mt-2">
                <ul class="nav sidebar-menu flex-column">
                    <li class="nav-item">
                        <a href="#" class="nav-link active">
                            <i class="fas fa-tachometer-alt"></i>
                            <p>
                                Gestión del sistema
                            </p>
                        </a>
                        <ul class="nav nav-treeview show">
                            <li class="nav-item">
                                <a href="../vista/GestionIncidentesVista.php" class="nav-link">
                                    <i class="fas fa-tools"></i>
                                    <p>Gestionar Incidentes</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../vista/VerFacturasVista.php" class="nav-link">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                    <p>Ver Facturas</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../vista/GenerarReporteVista.php" class="nav-link">
                                    <i class="fas fa-chart-bar"></i>
                                    <p>Generar Reporte</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../vista/NotificacionesVista.php" class="nav-link">
                                    <i class="fas fa-bell"></i>
                                    <p>Notificaciones</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                    <li class="nav-header">OPCIONES</li>
                    
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-building"></i>
                            <p>
                                Edificio
                                <i class="nav-arrow fas fa-chevron-right float-end"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="../controlador/EdificioControlador.php?action=mostrar&id_edificio=1" class="nav-link">
                                    <i class="fas fa-eye"></i>
                                    <p>Mostrar Edificio</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-door-open"></i>
                            <p>
                                Departamento
                                <i class="nav-arrow fas fa-chevron-right float-end"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="fas fa-list"></i>
                                    <p>Listar Departamentos</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="fas fa-plus-circle"></i>
                                    <p>Registrar Departamento</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-user-tie"></i>
                            <p>
                                Personal
                                <i class="nav-arrow fas fa-chevron-right float-end"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="fas fa-list"></i>
                                    <p>Listar Personal</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="fas fa-user-plus"></i>
                                    <p>Registrar Personal</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="fas fa-user-tag"></i>
                                    <p>Listar Roles</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="fas fa-plus-circle"></i>
                                    <p>Crear Rol</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-users"></i>
                            <p>
                                Usuarios
                                <i class="nav-arrow fas fa-chevron-right float-end"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="fas fa-list"></i>
                                    <p>Listar Usuarios</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="fas fa-user-plus"></i>
                                    <p>Registrar Usuario</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-exclamation-triangle"></i>
                            <p>
                                Incidentes
                                <i class="nav-arrow fas fa-chevron-right float-end"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="fas fa-list"></i>
                                    <p>Listar Incidentes</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="fas fa-plus-circle"></i>
                                    <p>Registrar Incidente</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="fas fa-user-check"></i>
                                    <p>Asignar Incidente</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="fas fa-tools"></i>
                                    <p>Atender Incidente</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="fas fa-history"></i>
                                    <p>Historial Incidentes</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-bullhorn"></i>
                            <p>
                                Comunicado
                                <i class="nav-arrow fas fa-chevron-right float-end"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="fas fa-list"></i>
                                    <p>Listar Comunicados</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="fas fa-plus-circle"></i>
                                    <p>Registrar Comunicado</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-concierge-bell"></i>
                            <p>
                                Servicios
                                <i class="nav-arrow fas fa-chevron-right float-end"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="fas fa-list"></i>
                                    <p>Listar Servicios</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="fas fa-plus-circle"></i>
                                    <p>Registrar Servicio</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="fas fa-chart-line"></i>
                                    <p>Historial Consumo</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-file-invoice-dollar"></i>
                            <p>
                                Facturas
                                <i class="nav-arrow fas fa-chevron-right float-end"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="fas fa-list"></i>
                                    <p>Listar Facturas</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="fas fa-plus-circle"></i>
                                    <p>Registrar Factura</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="fas fa-money-check"></i>
                                    <p>Pagar Factura</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="fas fa-history"></i>
                                    <p>Historial Pagos</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>



<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['id_persona'])) {
    header("Location: ../controlador/PersonaControlador.php?action=logout");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEInt</title>
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
        <a href="../controlador/DashboardControlador.php?action=mostrarDashboard<?php echo ($_SESSION['id_rol'] == '1') ? 'Administrador' : (($_SESSION['id_rol'] == '2') ? 'Residente' : 'Personal'); ?>" class="navbar-brand">
            <i class="fas fa-building"></i>
            <span class="d-none d-sm-inline">SEInt</span>
        </a>
    </div>
    <div class="header-controls">
        <button class="btn-notification">
            <i class="fas fa-bell"></i>
            <span class="notification-badge">5</span>
        </button>
        
        <div class="user-profile">
            <div class="user-avatar"><?php echo $_SESSION['avatar']?></div>
            <div class="user-info">
                <div class="user-name"><?php echo $_SESSION['nombre']." ".$_SESSION['apellido_paterno']." ".$_SESSION['apellido_materno']?></div>

                <div class="user-role"><?php echo htmlspecialchars($_SESSION['rol_nombre']);?></div>
            </div>
            <i class="fas fa-chevron-down ms-2" style="font-size: 0.9rem;"></i>
            <div class="user-dropdown">
                <a href="#" class="user-dropdown-item">
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
                <form method="POST" action="../controlador/PersonaControlador.php" class="d-inline">
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
                    </li>
                    
                    <li class="nav-header">OPCIONES</li>
                    <?php if ($_SESSION['id_rol'] == '1') { ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-user-tie"></i>
                            <p>
                                Persona
                                <i class="nav-arrow fas fa-chevron-right float-end"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="../controlador/PersonaControlador.php?action=listarPersonal" class="nav-link">
                                    <i class="fas fa-list"></i>
                                    <p>Listar Personal</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../controlador/PersonaControlador.php?action=listarResidente" class="nav-link">
                                    <i class="fas fa-list"></i>
                                    <p>Listar Residente</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../controlador/PersonaControlador.php?action=listarEliminados" class="nav-link">
                                    <i class="fas fa-list"></i>
                                    <p>Listar Eliminados</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../controlador/RolControlador.php?action=listar" class="nav-link">
                                    <i class="fas fa-list"></i>
                                    <p>Listar Roles</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../controlador/PersonaControlador.php?action=formularioPersona" class="nav-link">
                                    <i class="fas fa-user-tag"></i>
                                    <p>Registrar Persona</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../controlador/RolControlador.php?action=formularioRol" class="nav-link">
                                    <i class="fas fa-plus-circle"></i>
                                    <p>Crear Rol</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php }?>
                    <?php if ($_SESSION['id_rol'] == '1') { ?>
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
                                <a href="../controlador/DepartamentoControlador.php?action=listarDepartamentos" class="nav-link">
                                    <i class="fas fa-list"></i>
                                    <p>Listar Departamentos</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../controlador/DepartamentoControlador.php?action=formularioAsignarPersonasDepartamento" class="nav-link">
                                    <i class="fas fa-plus-circle"></i>
                                    <p>Asignar Departamento</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../controlador/DepartamentoControlador.php?action=formularioDepartamento" class="nav-link">
                                    <i class="fas fa-plus-circle"></i>
                                    <p>Registrar Departamento</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php }?>

                    
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-users"></i>
                            <p>
                                Area Común
                                <i class="nav-arrow fas fa-chevron-right float-end"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php if ($_SESSION['id_rol'] == '1') { ?>
                            <li class="nav-item">
                                <a href="../controlador/AreaComunControlador.php?action=listarAreas" class="nav-link">
                                    <i class="fas fa-list"></i>
                                    <p>Listar Areas</p>
                                </a>
                            </li>
                            <?php }?>
                            <li class="nav-item">
                                <a href="../controlador/AreaComunControlador.php?action=listarReservas" class="nav-link">
                                    <i class="fas fa-list"></i>
                                    <p>Listar Reservas</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../controlador/AreaComunControlador.php?action=formularioReservaArea" class="nav-link">
                                    <i class="fas fa-plus-circle"></i>
                                    <p>Reservar Area</p>
                                </a>
                            </li>
                            <?php if ($_SESSION['id_rol'] == '1') { ?>
                            <li class="nav-item">
                                <a href="../controlador/AreaComunControlador.php?action=formularioArea" class="nav-link">
                                    <i class="fas fa-plus-circle"></i>
                                    <p>Registrar Area</p>
                                </a>
                            </li>
                            <?php }?>
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
                            <?php if ($_SESSION['id_rol'] == '2') { ?>

                            <li class="nav-item">
                                <a href="../controlador/IncidenteControlador.php?action=verMisIncidentes" class="nav-link">
                                    <i class="fas fa-list"></i>
                                    <p>Mis Incidentes</p>
                                </a>
                            </li>
                            <?php } ?>
                            <?php if ($_SESSION['id_rol'] == '1' || $_SESSION['id_rol'] == '2') { ?>

                            <li class="nav-item">
                                <a href="../controlador/IncidenteControlador.php?action=formularioIncidente" class="nav-link">
                                    <i class="fas fa-plus-circle"></i>
                                    <p>Registrar Incidente</p>
                                </a>
                            </li>
                            <?php } ?>

                            <?php if ($_SESSION['id_rol'] == '1') { ?>
                                <li class="nav-item">
                                    <a href="../controlador/IncidenteControlador.php?action=listarIncidentes" class="nav-link">
                                        <i class="fas fa-list"></i>
                                        <p>Listar Incidentes</p>
                                    </a>
                                </li>


                            <?php } ?>
                            <?php if ($_SESSION['id_rol'] != '1'&&$_SESSION['id_rol'] != '2') { ?>

                            <li class="nav-item">
                                <a href="../controlador/IncidenteControlador.php?action=verIncidentesAsignados" class="nav-link">
                                    <i class="fas fa-tools"></i>
                                    <p>Atender Incidente</p>
                                </a>
                            </li>
                            <?php } ?>

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
                                <a href="../controlador/ComunicadoControlador.php" class="nav-link">
                                    <i class="fas fa-list"></i>
                                    <p>Listar Comunicados</p>
                                </a>
                            </li>
                            <?php if ($_SESSION['id_rol'] == '1') { ?>
                            <li class="nav-item">
                                <a href="../controlador/ComunicadoControlador.php?action=formularioComunicado" class="nav-link">
                                    <i class="fas fa-plus-circle"></i>
                                    <p>Registrar Comunicado</p>
                                </a>
                            </li>
                            <?php }?>
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
                            <?php if ($_SESSION['id_rol'] == '1') { ?>
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
                            <?php }?>
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
                                <a href="../controlador/FacturaControlador.php?action=listarFacturas" class="nav-link">
                                    <i class="fas fa-list"></i>
                                    <p>Listar Facturas</p>
                                </a>
                            </li>
                            <?php if ($_SESSION['id_rol'] == '1') { ?>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="fas fa-plus-circle"></i>
                                    <p>Registrar Factura</p>
                                </a>
                            </li>
                            <?php }?>
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
    <!-- Main Content -->
    <main class="main-content">
        <div class="container-fluid">

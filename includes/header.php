
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['id_persona'])) {
    header("Location: ../controlador/PersonaControlador.php?action=logout");
    exit();
}

// Función para detectar si la página actual coincide con un enlace
function isCurrentPage($linkHref) {
    if (empty($linkHref) || $linkHref === '#') {
        return false;
    }
    
    // Obtener el archivo actual
    $currentFile = basename($_SERVER['PHP_SELF']);
    
    // Obtener parámetros actuales
    $currentParams = $_GET;
    
    // Parsear el enlace
    $linkParts = parse_url($linkHref);
    $linkFile = isset($linkParts['path']) ? basename($linkParts['path']) : '';
    $linkQuery = isset($linkParts['query']) ? $linkParts['query'] : '';
    
    // Comparar nombres de archivo
    if ($currentFile === $linkFile) {
        // Si el enlace tiene parámetros, compararlos
        if (!empty($linkQuery)) {
            parse_str($linkQuery, $linkParams);
            
            // Verificar que todos los parámetros del enlace estén en la URL actual
            foreach ($linkParams as $key => $value) {
                if (!isset($currentParams[$key]) || $currentParams[$key] != $value) {
                    return false;
                }
            }
        }
        return true;
    }
    
    return false;
}

// Función para obtener la clase del menú padre si algún hijo está activo
function getParentMenuClass($menuItems) {
    foreach ($menuItems as $item) {
        if (isset($item['href']) && isCurrentPage($item['href'])) {
            return 'show';
        }
    }
    return '';
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
    
    <!-- Estilos adicionales para notificaciones -->
    <style>
        .notification-container {
            position: relative;
        }
        
        .notification-dropdown {
            position: absolute;
            top: calc(100% + 15px);
            right: 0;
            width: 420px;
            max-height: 600px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2), 0 0 0 1px rgba(0, 0, 0, 0.05);
            z-index: 1050;
            overflow: hidden;
            animation: slideDown 0.3s ease-out;
            border: 1px solid rgba(26, 82, 118, 0.1);
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .notification-header {
            padding: 20px 24px;
            border-bottom: 2px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, #1a5276 0%, #2a7595 100%);
            color: white;
        }
        
        .notification-header h6 {
            margin: 0;
            font-weight: 700;
            font-size: 1.1rem;
            color: white;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .notification-header h6::before {
            content: '\f0f3';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
        }
        
        .btn-close-notifications {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            font-size: 1rem;
            cursor: pointer;
            padding: 6px;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }
        
        .btn-close-notifications:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: rotate(90deg);
        }
        
        .notification-body {
            max-height: 480px;
            overflow-y: auto;
            background: #f8f9fa;
        }
        
        .notification-body::-webkit-scrollbar {
            width: 8px;
        }
        
        .notification-body::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        .notification-body::-webkit-scrollbar-thumb {
            background: #1a5276;
            border-radius: 4px;
        }
        
        .notification-body::-webkit-scrollbar-thumb:hover {
            background: #2a7595;
        }
        
        .notification-item {
            padding: 18px 24px;
            border-bottom: 1px solid #e9ecef;
            cursor: pointer;
            transition: all 0.3s;
            background: white;
            margin: 8px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border-left: 4px solid transparent;
        }
        
        .notification-item:hover {
            background: #f0f7ff;
            transform: translateX(-4px);
            box-shadow: 0 4px 12px rgba(26, 82, 118, 0.15);
        }
        
        .notification-item:last-child {
            border-bottom: none;
            margin-bottom: 8px;
        }
        
        .notification-item.unread {
            background: #f0f7ff;
            border-left-color: #1a5276;
        }
        
        .notification-item.urgente {
            border-left-color: #dc3545;
        }
        
        .notification-item.alta {
            border-left-color: #ffc107;
        }
        
        .notification-item.media {
            border-left-color: #17a2b8;
        }
        
        .notification-item.baja {
            border-left-color: #6c757d;
        }
        
        .notification-title {
            font-weight: 700;
            color: #1a5276;
            margin-bottom: 10px;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .notification-content {
            color: #495057;
            font-size: 0.9rem;
            margin-bottom: 12px;
            line-height: 1.6;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .notification-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.8rem;
            color: #6c757d;
            padding-top: 8px;
            border-top: 1px solid #e9ecef;
        }
        
        .notification-meta span {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .notification-meta i {
            font-size: 0.75rem;
        }
        
        .notification-priority {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .notification-priority.danger {
            background: linear-gradient(135deg, #fee 0%, #fcc 100%);
            color: #dc3545;
            box-shadow: 0 2px 4px rgba(220, 53, 69, 0.2);
        }
        
        .notification-priority.warning {
            background: linear-gradient(135deg, #fff3cd 0%, #ffe69c 100%);
            color: #856404;
            box-shadow: 0 2px 4px rgba(255, 193, 7, 0.2);
        }
        
        .notification-priority.info {
            background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
            color: #0c5460;
            box-shadow: 0 2px 4px rgba(23, 162, 184, 0.2);
        }
        
        .notification-priority.secondary {
            background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
            color: #495057;
            box-shadow: 0 2px 4px rgba(108, 117, 125, 0.2);
        }
        
        .notification-empty {
            padding: 60px 30px;
            text-align: center;
            color: #6c757d;
        }
        
        .notification-empty i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.3;
            color: #1a5276;
        }
        
        .notification-empty p {
            font-size: 1rem;
            margin: 0;
            color: #6c757d;
        }
        
        .notification-dropdown::before {
            content: '';
            position: absolute;
            top: -8px;
            right: 20px;
            width: 0;
            height: 0;
            border-left: 8px solid transparent;
            border-right: 8px solid transparent;
            border-bottom: 8px solid white;
        }
    </style>
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
        <div class="notification-container position-relative">
            <button class="btn-notification" id="btnNotifications" type="button">
                <i class="fas fa-bell"></i>
                <span class="notification-badge" id="notificationBadge">0</span>
            </button>
            <div class="notification-dropdown" id="notificationDropdown" style="display: none;">
                <div class="notification-header">
                    <h6>Comunicados</h6>
                    <button type="button" class="btn-close-notifications" id="btnCloseNotifications">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="notification-body" id="notificationBody">
                    <div class="text-center py-3">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="text-muted mt-2 mb-0">Cargando comunicados...</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="user-profile">
            <div class="user-avatar"><?php echo $_SESSION['avatar']?></div>
            <div class="user-info">
                <div class="user-name"><?php echo $_SESSION['nombre']." ".$_SESSION['apellido_paterno']." ".$_SESSION['apellido_materno']?></div>

                <div class="user-role"><?php echo htmlspecialchars($_SESSION['rol_nombre']);?></div>
            </div>
            <i class="fas fa-chevron-down ms-2" style="font-size: 0.9rem;"></i>
            <div class="user-dropdown">
                <a href="../controlador/PersonaControlador.php?action=verMiPerfil" class="user-dropdown-item">
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
                    <?php if ($_SESSION['id_rol'] == '1') { 
                        // Verificar si alguna opción del menú Persona está activa
                        $personaMenuItems = [
                            ['href' => '../controlador/PersonaControlador.php?action=listarPersonal'],
                            ['href' => '../controlador/PersonaControlador.php?action=listarResidente'],
                            ['href' => '../controlador/PersonaControlador.php?action=listarEliminados'],
                            ['href' => '../controlador/RolControlador.php?action=listar'],
                            ['href' => '../controlador/PersonaControlador.php?action=formularioPersona'],
                            ['href' => '../controlador/RolControlador.php?action=formularioRol']
                        ];
                        $personaMenuActive = getParentMenuClass($personaMenuItems);
                        $personaParentActive = $personaMenuActive ? 'active' : '';
                        $personaArrowStyle = $personaMenuActive ? 'style="transform: rotate(90deg);"' : '';
                    ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link <?php echo $personaParentActive; ?>">
                            <i class="fas fa-user-tie"></i>
                            <p>
                                Persona
                                <i class="nav-arrow fas fa-chevron-right float-end" <?php echo $personaArrowStyle; ?>></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview <?php echo $personaMenuActive; ?>">
                            <li class="nav-item">
                                <a href="../controlador/PersonaControlador.php?action=listarPersonal" class="nav-link <?php echo isCurrentPage('../controlador/PersonaControlador.php?action=listarPersonal') ? 'active' : ''; ?>">
                                    <i class="fas fa-user-tie"></i>
                                    <p>Listar Personal</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../controlador/PersonaControlador.php?action=listarResidente" class="nav-link <?php echo isCurrentPage('../controlador/PersonaControlador.php?action=listarResidente') ? 'active' : ''; ?>">
                                    <i class="fas fa-users"></i>
                                    <p>Listar Residente</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../controlador/PersonaControlador.php?action=listarEliminados" class="nav-link <?php echo isCurrentPage('../controlador/PersonaControlador.php?action=listarEliminados') ? 'active' : ''; ?>">
                                    <i class="fas fa-user-slash"></i>
                                    <p>Listar Eliminados</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../controlador/RolControlador.php?action=listar" class="nav-link <?php echo isCurrentPage('../controlador/RolControlador.php?action=listar') ? 'active' : ''; ?>">
                                    <i class="fas fa-user-shield"></i>
                                    <p>Listar Roles</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../controlador/PersonaControlador.php?action=formularioPersona" class="nav-link <?php echo isCurrentPage('../controlador/PersonaControlador.php?action=formularioPersona') ? 'active' : ''; ?>">
                                    <i class="fas fa-user-plus"></i>
                                    <p>Registrar Persona</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../controlador/RolControlador.php?action=formularioRol" class="nav-link <?php echo isCurrentPage('../controlador/RolControlador.php?action=formularioRol') ? 'active' : ''; ?>">
                                    <i class="fas fa-user-cog"></i>
                                    <p>Crear Rol</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php }?>
                    <?php if ($_SESSION['id_rol'] == '1') { 
                        $departamentoMenuItems = [
                            ['href' => '../controlador/DepartamentoControlador.php?action=listarDepartamentos'],
                            ['href' => '../controlador/DepartamentoControlador.php?action=formularioAsignarPersonasDepartamento'],
                            ['href' => '../controlador/DepartamentoControlador.php?action=formularioDepartamento']
                        ];
                        $departamentoMenuActive = getParentMenuClass($departamentoMenuItems);
                        $departamentoParentActive = $departamentoMenuActive ? 'active' : '';
                        $departamentoArrowStyle = $departamentoMenuActive ? 'style="transform: rotate(90deg);"' : '';
                    ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link <?php echo $departamentoParentActive; ?>">
                            <i class="fas fa-door-open"></i>
                            <p>
                                Departamento
                                <i class="nav-arrow fas fa-chevron-right float-end" <?php echo $departamentoArrowStyle; ?>></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview <?php echo $departamentoMenuActive; ?>">
                            <li class="nav-item">
                                <a href="../controlador/DepartamentoControlador.php?action=listarDepartamentos" class="nav-link <?php echo isCurrentPage('../controlador/DepartamentoControlador.php?action=listarDepartamentos') ? 'active' : ''; ?>">
                                    <i class="fas fa-building"></i>
                                    <p>Listar Departamentos</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../controlador/DepartamentoControlador.php?action=formularioAsignarPersonasDepartamento" class="nav-link <?php echo isCurrentPage('../controlador/DepartamentoControlador.php?action=formularioAsignarPersonasDepartamento') ? 'active' : ''; ?>">
                                    <i class="fas fa-user-check"></i>
                                    <p>Asignar Departamento</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../controlador/DepartamentoControlador.php?action=formularioDepartamento" class="nav-link <?php echo isCurrentPage('../controlador/DepartamentoControlador.php?action=formularioDepartamento') ? 'active' : ''; ?>">
                                    <i class="fas fa-door-open"></i>
                                    <p>Registrar Departamento</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php }?>

                    
                    <?php 
                        $areaComunMenuItems = [];
                        if ($_SESSION['id_rol'] == '1') {
                            $areaComunMenuItems[] = ['href' => '../controlador/AreaComunControlador.php?action=listarAreas'];
                        }
                        $areaComunMenuItems[] = ['href' => '../controlador/AreaComunControlador.php?action=listarReservas'];
                        $areaComunMenuItems[] = ['href' => '../controlador/AreaComunControlador.php?action=formularioReservaArea'];
                        if ($_SESSION['id_rol'] == '1') {
                            $areaComunMenuItems[] = ['href' => '../controlador/AreaComunControlador.php?action=formularioArea'];
                        }
                        $areaComunMenuActive = getParentMenuClass($areaComunMenuItems);
                        $areaComunParentActive = $areaComunMenuActive ? 'active' : '';
                        $areaComunArrowStyle = $areaComunMenuActive ? 'style="transform: rotate(90deg);"' : '';
                    ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link <?php echo $areaComunParentActive; ?>">
                            <i class="fas fa-users"></i>
                            <p>
                                Area Común
                                <i class="nav-arrow fas fa-chevron-right float-end" <?php echo $areaComunArrowStyle; ?>></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview <?php echo $areaComunMenuActive; ?>">
                            <?php if ($_SESSION['id_rol'] == '1') { ?>
                            <li class="nav-item">
                                <a href="../controlador/AreaComunControlador.php?action=listarAreas" class="nav-link <?php echo isCurrentPage('../controlador/AreaComunControlador.php?action=listarAreas') ? 'active' : ''; ?>">
                                    <i class="fas fa-map-marked-alt"></i>
                                    <p>Listar Areas</p>
                                </a>
                            </li>
                            <?php }?>
                            <li class="nav-item">
                                <a href="../controlador/AreaComunControlador.php?action=listarReservas" class="nav-link <?php echo isCurrentPage('../controlador/AreaComunControlador.php?action=listarReservas') ? 'active' : ''; ?>">
                                    <i class="fas fa-calendar-check"></i>
                                    <p>Listar Reservas</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../controlador/AreaComunControlador.php?action=formularioReservaArea" class="nav-link <?php echo isCurrentPage('../controlador/AreaComunControlador.php?action=formularioReservaArea') ? 'active' : ''; ?>">
                                    <i class="fas fa-calendar-plus"></i>
                                    <p>Reservar Area</p>
                                </a>
                            </li>
                            <?php if ($_SESSION['id_rol'] == '1') { ?>
                            <li class="nav-item">
                                <a href="../controlador/AreaComunControlador.php?action=formularioArea" class="nav-link <?php echo isCurrentPage('../controlador/AreaComunControlador.php?action=formularioArea') ? 'active' : ''; ?>">
                                    <i class="fas fa-plus-square"></i>
                                    <p>Registrar Area</p>
                                </a>
                            </li>
                            <?php }?>
                        </ul>
                    </li>
                    
                    <?php 
                        $incidentesMenuItems = [];
                        if ($_SESSION['id_rol'] == '2') {
                            $incidentesMenuItems[] = ['href' => '../controlador/IncidenteControlador.php?action=verMisIncidentes'];
                        }
                        if ($_SESSION['id_rol'] == '1' || $_SESSION['id_rol'] == '2') {
                            $incidentesMenuItems[] = ['href' => '../controlador/IncidenteControlador.php?action=formularioIncidente'];
                        }
                        if ($_SESSION['id_rol'] == '1') {
                            $incidentesMenuItems[] = ['href' => '../controlador/IncidenteControlador.php?action=listarIncidentes'];
                        }
                        if ($_SESSION['id_rol'] != '1' && $_SESSION['id_rol'] != '2') {
                            $incidentesMenuItems[] = ['href' => '../controlador/IncidenteControlador.php?action=verIncidentesAsignados'];
                        }
                        $incidentesMenuActive = getParentMenuClass($incidentesMenuItems);
                        $incidentesParentActive = $incidentesMenuActive ? 'active' : '';
                        $incidentesArrowStyle = $incidentesMenuActive ? 'style="transform: rotate(90deg);"' : '';
                    ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link <?php echo $incidentesParentActive; ?>">
                            <i class="fas fa-exclamation-triangle"></i>
                            <p>
                                Incidentes
                                <i class="nav-arrow fas fa-chevron-right float-end" <?php echo $incidentesArrowStyle; ?>></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview <?php echo $incidentesMenuActive; ?>">
                            <?php if ($_SESSION['id_rol'] == '2') { ?>

                            <li class="nav-item">
                                <a href="../controlador/IncidenteControlador.php?action=verMisIncidentes" class="nav-link <?php echo isCurrentPage('../controlador/IncidenteControlador.php?action=verMisIncidentes') ? 'active' : ''; ?>">
                                    <i class="fas fa-clipboard-list"></i>
                                    <p>Mis Incidentes</p>
                                </a>
                            </li>
                            <?php } ?>
                            <?php if ($_SESSION['id_rol'] == '1' || $_SESSION['id_rol'] == '2') { ?>

                            <li class="nav-item">
                                <a href="../controlador/IncidenteControlador.php?action=formularioIncidente" class="nav-link <?php echo isCurrentPage('../controlador/IncidenteControlador.php?action=formularioIncidente') ? 'active' : ''; ?>">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <p>Registrar Incidente</p>
                                </a>
                            </li>
                            <?php } ?>

                            <?php if ($_SESSION['id_rol'] == '1') { ?>
                                <li class="nav-item">
                                    <a href="../controlador/IncidenteControlador.php?action=listarIncidentes" class="nav-link <?php echo isCurrentPage('../controlador/IncidenteControlador.php?action=listarIncidentes') ? 'active' : ''; ?>">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <p>Listar Incidentes</p>
                                    </a>
                                </li>


                            <?php } ?>
                            <?php if ($_SESSION['id_rol'] != '1'&&$_SESSION['id_rol'] != '2') { ?>

                            <li class="nav-item">
                                <a href="../controlador/IncidenteControlador.php?action=verIncidentesAsignados" class="nav-link <?php echo isCurrentPage('../controlador/IncidenteControlador.php?action=verIncidentesAsignados') ? 'active' : ''; ?>">
                                    <i class="fas fa-wrench"></i>
                                    <p>Atender Incidente</p>
                                </a>
                            </li>
                            <?php } ?>

                        </ul>
                    </li>
                    
                    <?php 
                        $comunicadoMenuItems = [
                            ['href' => '../controlador/ComunicadoControlador.php?action=listarPublicados'],
                            ['href' => '../controlador/ComunicadoControlador.php']
                        ];
                        if ($_SESSION['id_rol'] == '1') {
                            $comunicadoMenuItems[] = ['href' => '../controlador/ComunicadoControlador.php?action=formularioComunicado'];
                            $comunicadoMenuItems[] = ['href' => '../controlador/ComunicadoControlador.php?action=listarEliminados'];
                        }
                        $comunicadoMenuActive = getParentMenuClass($comunicadoMenuItems);
                        $comunicadoParentActive = $comunicadoMenuActive ? 'active' : '';
                        $comunicadoArrowStyle = $comunicadoMenuActive ? 'style="transform: rotate(90deg);"' : '';
                    ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link <?php echo $comunicadoParentActive; ?>">
                            <i class="fas fa-bullhorn"></i>
                            <p>
                                Comunicado
                                <i class="nav-arrow fas fa-chevron-right float-end" <?php echo $comunicadoArrowStyle; ?>></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview <?php echo $comunicadoMenuActive; ?>">
                            <li class="nav-item">
                                <a href="../controlador/ComunicadoControlador.php?action=listarPublicados" class="nav-link <?php echo isCurrentPage('../controlador/ComunicadoControlador.php?action=listarPublicados') ? 'active' : ''; ?>">
                                    <i class="fas fa-eye"></i>
                                    <p>Comunicados Publicados</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../controlador/ComunicadoControlador.php" class="nav-link <?php echo isCurrentPage('../controlador/ComunicadoControlador.php') ? 'active' : ''; ?>">
                                    <i class="fas fa-bullhorn"></i>
                                    <p>Listar Comunicados</p>
                                </a>
                            </li>
                            <?php if ($_SESSION['id_rol'] == '1') { ?>
                            <li class="nav-item">
                                <a href="../controlador/ComunicadoControlador.php?action=formularioComunicado" class="nav-link <?php echo isCurrentPage('../controlador/ComunicadoControlador.php?action=formularioComunicado') ? 'active' : ''; ?>">
                                    <i class="fas fa-edit"></i>
                                    <p>Registrar Comunicado</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../controlador/ComunicadoControlador.php?action=listarEliminados" class="nav-link <?php echo isCurrentPage('../controlador/ComunicadoControlador.php?action=listarEliminados') ? 'active' : ''; ?>">
                                    <i class="fas fa-trash-alt"></i>
                                    <p>Comunicados Eliminados</p>
                                </a>
                            </li>
                            <?php }?>
                        </ul>
                    </li>
                    <?php if ($_SESSION['id_rol'] == '1') { 
                        $cargosFijosMenuItems = [
                            ['href' => '../controlador/CargosFijosControlador.php?action=listarCargosVista'],
                            ['href' => '../controlador/CargosFijosControlador.php?action=formularioCrearCargo']
                        ];
                        $cargosFijosMenuActive = getParentMenuClass($cargosFijosMenuItems);
                        $cargosFijosParentActive = $cargosFijosMenuActive ? 'active' : '';
                        $cargosFijosArrowStyle = $cargosFijosMenuActive ? 'style="transform: rotate(90deg);"' : '';
                    ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link <?php echo $cargosFijosParentActive; ?>">
                            <i class="fas fa-money-check-alt"></i>
                            <p>
                                Cargos Fijos
                                <i class="nav-arrow fas fa-chevron-right float-end" <?php echo $cargosFijosArrowStyle; ?>></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview <?php echo $cargosFijosMenuActive; ?>">
                            <li class="nav-item">
                                <a href="../controlador/CargosFijosControlador.php?action=listarCargosVista" class="nav-link <?php echo isCurrentPage('../controlador/CargosFijosControlador.php?action=listarCargosVista') ? 'active' : ''; ?>">
                                    <i class="fas fa-dollar-sign"></i>
                                    <p>Listar Cargos Fijos</p>
                                </a>
                            </li>
                            <?php if ($_SESSION['id_rol'] == '1') { ?>
                                <li class="nav-item">
                                    <a href="../controlador/CargosFijosControlador.php?action=formularioCrearCargo" class="nav-link <?php echo isCurrentPage('../controlador/CargosFijosControlador.php?action=formularioCrearCargo') ? 'active' : ''; ?>">
                                        <i class="fas fa-money-bill-wave"></i>
                                        <p>Registrar Cargos Fijos</p>
                                    </a>
                                </li>
                            <?php }?>
                        </ul>
                    </li>

                    <?php }?>
                    <?php if ($_SESSION['id_rol'] == '1') { 
                        $serviciosMenuItems = [
                            ['href' => '../controlador/ServicioControlador.php?action=listarServicios'],
                            ['href' => '../controlador/ServicioControlador.php?action=listarMedidores']
                        ];
                        $serviciosMenuActive = getParentMenuClass($serviciosMenuItems);
                        $serviciosParentActive = $serviciosMenuActive ? 'active' : '';
                        $serviciosArrowStyle = $serviciosMenuActive ? 'style="transform: rotate(90deg);"' : '';
                    ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link <?php echo $serviciosParentActive; ?>">
                            <i class="fas fa-concierge-bell"></i>
                            <p>
                                Servicios
                                <i class="nav-arrow fas fa-chevron-right float-end" <?php echo $serviciosArrowStyle; ?>></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview <?php echo $serviciosMenuActive; ?>">

                            <li class="nav-item">
                                <a href="../controlador/ServicioControlador.php?action=listarServicios" class="nav-link <?php echo isCurrentPage('../controlador/ServicioControlador.php?action=listarServicios') ? 'active' : ''; ?>">
                                    <i class="fas fa-concierge-bell"></i>
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
                                <a href="../controlador/ServicioControlador.php?action=listarMedidores" class="nav-link <?php echo isCurrentPage('../controlador/ServicioControlador.php?action=listarMedidores') ? 'active' : ''; ?>">
                                    <i class="fas fa-tachometer-alt"></i>
                                    <p>Historial Consumo</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <?php }?>
                    <?php 
                        $facturasMenuItems = [];
                        if ($_SESSION['id_rol'] == '1') {
                            $facturasMenuItems[] = ['href' => '../controlador/FacturaControlador.php?action=listarFacturas'];
                            $facturasMenuItems[] = ['href' => '../controlador/FacturaControlador.php?action=historialPagosCompleto'];
                            $facturasMenuItems[] = ['href' => '../controlador/FacturaControlador.php?action=conceptosCompletos'];
                            $facturasMenuItems[] = ['href' => '../controlador/PlanillaControlador.php?action=listarPlanillasCompleto'];
                            $facturasMenuItems[] = ['href' => '../controlador/PlanillaControlador.php?action=formularioGenerarPlanilla'];
                        }
                        if ($_SESSION['id_rol'] == '2') {
                            $facturasMenuItems[] = ['href' => '../controlador/FacturaControlador.php?action=verMisFacturas'];
                            $facturasMenuItems[] = ['href' => '../controlador/FacturaControlador.php?action=misConceptos'];
                            $facturasMenuItems[] = ['href' => '../controlador/FacturaControlador.php?action=verMiHistorialPagos'];
                        }
                        if ($_SESSION['id_rol'] != '2' && $_SESSION['id_rol'] != '1') {
                            $facturasMenuItems[] = ['href' => '../controlador/PlanillaControlador.php?action=verMiPlanilla'];
                        }
                        $facturasMenuActive = getParentMenuClass($facturasMenuItems);
                        $facturasParentActive = $facturasMenuActive ? 'active' : '';
                        $facturasArrowStyle = $facturasMenuActive ? 'style="transform: rotate(90deg);"' : '';
                    ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link <?php echo $facturasParentActive; ?>">
                            <i class="fas fa-file-invoice-dollar"></i>
                            <p>
                                Facturas
                                <i class="nav-arrow fas fa-chevron-right float-end" <?php echo $facturasArrowStyle; ?>></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview <?php echo $facturasMenuActive; ?>">
                            <?php if ($_SESSION['id_rol'] == '1') { ?>

                                <li class="nav-item">
                                <a href="../controlador/FacturaControlador.php?action=listarFacturas" class="nav-link <?php echo isCurrentPage('../controlador/FacturaControlador.php?action=listarFacturas') ? 'active' : ''; ?>">
                                    <i class="fas fa-file-invoice"></i>
                                    <p>Listar Facturas</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../controlador/FacturaControlador.php?action=historialPagosCompleto" class="nav-link <?php echo isCurrentPage('../controlador/FacturaControlador.php?action=historialPagosCompleto') ? 'active' : ''; ?>">
                                    <i class="fas fa-clock"></i>
                                    <p>Historial Pagos</p>
                                </a>
                            </li>
                                <li class="nav-item">
                                    <a href="../controlador/FacturaControlador.php?action=conceptosCompletos" class="nav-link <?php echo isCurrentPage('../controlador/FacturaControlador.php?action=conceptosCompletos') ? 'active' : ''; ?>">
                                        <i class="fas fa-tags"></i>
                                        <p>Conceptos</p>
                                    </a>
                                </li>
                            <?php }?>
                            <?php if ($_SESSION['id_rol'] == '2') { ?>

                            <li class="nav-item">
                                <a href="../controlador/FacturaControlador.php?action=verMisFacturas" class="nav-link <?php echo isCurrentPage('../controlador/FacturaControlador.php?action=verMisFacturas') ? 'active' : ''; ?>">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                    <p>Mis Facturas</p>
                                </a>
                            </li>
                                <li class="nav-item">
                                    <a href="../controlador/FacturaControlador.php?action=misConceptos" class="nav-link <?php echo isCurrentPage('../controlador/FacturaControlador.php?action=misConceptos') ? 'active' : ''; ?>">
                                        <i class="fas fa-tag"></i>
                                        <p>Mis Conceptos</p>
                                    </a>
                                </li>
                            <li class="nav-item">
                                <a href="../controlador/FacturaControlador.php?action=verMiHistorialPagos" class="nav-link <?php echo isCurrentPage('../controlador/FacturaControlador.php?action=verMiHistorialPagos') ? 'active' : ''; ?>">
                                    <i class="fas fa-history"></i>
                                    <p>Mi Historial de Pagos</p>
                                </a>
                            </li>
                            <?php }?>
                            <?php if ($_SESSION['id_rol'] == '1') { ?>

                                <li class="nav-item">
                                    <a href="../controlador/PlanillaControlador.php?action=listarPlanillasCompleto" class="nav-link <?php echo isCurrentPage('../controlador/PlanillaControlador.php?action=listarPlanillasCompleto') ? 'active' : ''; ?>">
                                        <i class="fas fa-file-alt"></i>
                                        <p>Planillas</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="../controlador/PlanillaControlador.php?action=formularioGenerarPlanilla" class="nav-link <?php echo isCurrentPage('../controlador/PlanillaControlador.php?action=formularioGenerarPlanilla') ? 'active' : ''; ?>">
                                        <i class="fas fa-file-export"></i>
                                        <p>Generar planillas</p>
                                    </a>
                                </li>
                            <?php }?>
                            <?php if ($_SESSION['id_rol'] != '2' && $_SESSION['id_rol'] != '1') { ?>
                                <li class="nav-item">
                                    <a href="../controlador/PlanillaControlador.php?action=verMiPlanilla" class="nav-link <?php echo isCurrentPage('../controlador/PlanillaControlador.php?action=verMiPlanilla') ? 'active' : ''; ?>">
                                        <i class="fas fa-file-invoice"></i>
                                        <p>Mis planillas</p>
                                    </a>
                                </li>
                            <?php }?>
                        </ul>


                    </li>
                </ul>
            </nav>
        </div>
    </aside>
    <!-- Main Content -->
    <main class="main-content">
        <div class="container-fluid">

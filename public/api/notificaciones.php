<?php
// notificaciones.php - API para obtener comunicados como notificaciones
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

session_start();

if (empty($_SESSION['id_persona']) || empty($_SESSION['id_rol'])) {
    http_response_code(401);
    echo json_encode([
        'status' => 401,
        'message' => 'No autorizado',
        'comunicados' => [],
        'total' => 0
    ]);
    exit();
}

try {
    require_once '../../config/database.php';
    require_once '../modelo/DashboardModelo.php';

    $database = new Database();
    $db = $database->getConnection();
    $dashboardModelo = new DashboardModelo($db);

    $id_rol = $_SESSION['id_rol'];
    $comunicados = [];

    // Obtener comunicados según el rol y tipo de audiencia
    // La lógica es la misma que en los dashboards:
    // - Personal: muestra comunicados con tipo_audiencia = 'Todos' OR 'Personal'
    // - Residente: muestra comunicados con tipo_audiencia = 'Todos' OR 'Residente'
    // - Administrador: puede ver todos los comunicados publicados
    if ($id_rol == '1') {
        // Administrador - ver todos los comunicados publicados (sin filtro de audiencia)
        require_once '../modelo/ComunicadoModelo.php';
        $comunicadoModelo = new ComunicadoModelo($db);
        $comunicados = $comunicadoModelo->obtenerComunicadosPublicos();
        
        // Formatear autor para administradores
        foreach ($comunicados as &$comunicado) {
            if (isset($comunicado['autor_nombre']) && isset($comunicado['autor_apellido'])) {
                $comunicado['autor'] = trim($comunicado['autor_nombre'] . ' ' . $comunicado['autor_apellido']);
            } else {
                $comunicado['autor'] = 'Sistema';
            }
        }
    } elseif ($id_rol == '2') {
        // Residente - muestra comunicados con tipo_audiencia = 'Todos' OR 'Residente'
        $comunicados = $dashboardModelo->obtenerComunicadosResidente();
    } elseif ($id_rol != '1' && $id_rol != '2') {
        // Personal - muestra comunicados con tipo_audiencia = 'Todos' OR 'Personal'
        $comunicados = $dashboardModelo->obtenerComunicadosPersonal();
    }

    // Formatear fechas para mostrar
    foreach ($comunicados as &$comunicado) {
        if (isset($comunicado['fecha_publicacion'])) {
            $fecha = new DateTime($comunicado['fecha_publicacion']);
            $comunicado['fecha_formateada'] = $fecha->format('d/m/Y H:i');
            $comunicado['fecha_relativa'] = obtenerFechaRelativa($fecha);
        }
        
        // Determinar color de prioridad
        $comunicado['prioridad_color'] = obtenerColorPrioridad($comunicado['prioridad'] ?? 'media');
    }

    echo json_encode([
        'status' => 200,
        'message' => 'Comunicados obtenidos exitosamente',
        'comunicados' => $comunicados,
        'total' => count($comunicados)
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 500,
        'message' => 'Error: ' . $e->getMessage(),
        'comunicados' => [],
        'total' => 0
    ]);
}

function obtenerFechaRelativa($fecha) {
    $ahora = new DateTime();
    $diferencia = $ahora->diff($fecha);
    
    if ($diferencia->days == 0) {
        if ($diferencia->h == 0) {
            return 'Hace ' . $diferencia->i . ' minutos';
        }
        return 'Hace ' . $diferencia->h . ' horas';
    } elseif ($diferencia->days == 1) {
        return 'Ayer';
    } elseif ($diferencia->days < 7) {
        return 'Hace ' . $diferencia->days . ' días';
    } else {
        return $fecha->format('d/m/Y');
    }
}

function obtenerColorPrioridad($prioridad) {
    switch (strtolower($prioridad)) {
        case 'urgente':
            return 'danger';
        case 'alta':
            return 'warning';
        case 'media':
            return 'info';
        case 'baja':
            return 'secondary';
        default:
            return 'secondary';
    }
}
?>


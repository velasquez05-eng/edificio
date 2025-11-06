<?php
// comunicado.php - API completa para gestión de comunicados
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Manejar preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

error_log("DEBUG: comunicado.php accedido - Método: " . $_SERVER['REQUEST_METHOD']);

try {
    require_once '../../config/database.php';
    require_once '../modelo/ComunicadoModelo.php';

    $database = new Database();
    $db = $database->getConnection();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 500,
        'message' => 'Error cargando dependencias: ' . $e->getMessage()
    ]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    error_log("DEBUG: Input recibido: " . print_r($input, true));

    if ($input && isset($input['action'])) {
        $action = $input['action'];
        error_log("DEBUG: Acción solicitada: " . $action);

        switch($action) {
            // Gestión de comunicados
            case 'listarComunicados':
                handleListarComunicados($db, $input);
                break;
            case 'obtenerComunicadoPorId':
                handleObtenerComunicadoPorId($db, $input);
                break;
            case 'crearComunicado':
                handleCrearComunicado($db, $input);
                break;
            case 'actualizarComunicado':
                handleActualizarComunicado($db, $input);
                break;
            case 'cambiarEstado':
                handleCambiarEstado($db, $input);
                break;
            case 'eliminarComunicado':
                handleEliminarComunicado($db, $input);
                break;
            
            // Consultas especiales
            case 'obtenerComunicadosPublicos':
                handleObtenerComunicadosPublicos($db, $input);
                break;
            case 'listarComunicadosPublicados':
                handleListarComunicadosPublicados($db, $input);
                break;
            case 'listarComunicadosEliminados':
                handleListarComunicadosEliminados($db, $input);
                break;
            case 'restaurarComunicado':
                handleRestaurarComunicado($db, $input);
                break;
            
            // Estadísticas
            case 'obtenerEstadisticas':
                handleObtenerEstadisticas($db, $input);
                break;
            
            default:
                sendResponse(400, ['status' => 400, 'message' => 'Acción no válida: ' . $action]);
        }
    } else {
        sendResponse(400, ['status' => 400, 'message' => 'Datos de entrada inválidos o acción no especificada']);
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';

    switch($action) {
        case 'listarComunicados':
            handleListarComunicados($db, $_GET);
            break;
        case 'obtenerComunicadoPorId':
            handleObtenerComunicadoPorId($db, $_GET);
            break;
        case 'obtenerComunicadosPublicos':
            handleObtenerComunicadosPublicos($db, $_GET);
            break;
        case 'listarComunicadosPublicados':
            handleListarComunicadosPublicados($db, $_GET);
            break;
        case 'listarComunicadosEliminados':
            handleListarComunicadosEliminados($db, $_GET);
            break;
        case 'obtenerEstadisticas':
            handleObtenerEstadisticas($db, $_GET);
            break;
        default:
            sendResponse(400, ['status' => 400, 'message' => 'Acción GET no válida: ' . $action]);
    }
} else {
    sendResponse(405, ['status' => 405, 'message' => 'Método no permitido']);
}

// =============================================
// ENDPOINTS PARA GESTIÓN DE COMUNICADOS
// =============================================

function handleListarComunicados($db, $data) {
    try {
        $comunicadoModelo = new ComunicadoModelo($db);
        $comunicados = $comunicadoModelo->listarComunicados();

        if ($comunicados === false) {
            sendResponse(500, [
                'status' => 500,
                'message' => 'Error al listar comunicados'
            ]);
            return;
        }

        sendResponse(200, [
            'status' => 200,
            'message' => 'Comunicados listados exitosamente',
            'data' => $comunicados,
            'total' => count($comunicados)
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al listar comunicados: ' . $e->getMessage()
        ]);
    }
}

function handleObtenerComunicadoPorId($db, $data) {
    try {
        $comunicadoModelo = new ComunicadoModelo($db);

        $id_comunicado = intval($data['id_comunicado'] ?? 0);

        if ($id_comunicado <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de comunicado inválido']);
            return;
        }

        $comunicado = $comunicadoModelo->obtenerPorId($id_comunicado);

        if (!$comunicado) {
            sendResponse(404, ['status' => 404, 'message' => 'Comunicado no encontrado']);
            return;
        }

        sendResponse(200, [
            'status' => 200,
            'message' => 'Comunicado obtenido exitosamente',
            'data' => $comunicado
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al obtener comunicado: ' . $e->getMessage()
        ]);
    }
}

function handleCrearComunicado($db, $data) {
    try {
        $comunicadoModelo = new ComunicadoModelo($db);

        $camposRequeridos = ['id_persona', 'titulo', 'contenido', 'prioridad', 'estado', 'tipo_audiencia'];
        foreach($camposRequeridos as $campo) {
            if(!isset($data[$campo]) || empty(trim($data[$campo]))) {
                sendResponse(400, ['status' => 400, 'message' => "El campo $campo es obligatorio"]);
                return;
            }
        }

        // Validar prioridad
        $prioridadesValidas = ['urgente', 'alta', 'media', 'baja'];
        if (!in_array($data['prioridad'], $prioridadesValidas)) {
            sendResponse(400, ['status' => 400, 'message' => 'Prioridad inválida. Debe ser: urgente, alta, media o baja']);
            return;
        }

        // Validar estado
        $estadosValidos = ['publicado', 'borrador', 'archivado'];
        if (!in_array($data['estado'], $estadosValidos)) {
            sendResponse(400, ['status' => 400, 'message' => 'Estado inválido. Debe ser: publicado, borrador o archivado']);
            return;
        }

        // Validar tipo_audiencia
        $tiposAudienciaValidos = ['Todos', 'Residentes', 'Personal'];
        if (!in_array($data['tipo_audiencia'], $tiposAudienciaValidos)) {
            sendResponse(400, ['status' => 400, 'message' => 'Tipo de audiencia inválido. Debe ser: Todos, Residentes o Personal']);
            return;
        }

        $datos = [
            'id_persona' => intval($data['id_persona']),
            'titulo' => trim($data['titulo']),
            'contenido' => trim($data['contenido']),
            'fecha_expiracion' => !empty($data['fecha_expiracion']) ? trim($data['fecha_expiracion']) : null,
            'prioridad' => trim($data['prioridad']),
            'estado' => trim($data['estado']),
            'tipo_audiencia' => trim($data['tipo_audiencia'])
        ];

        // Validar formato de fecha_expiracion si se proporciona
        if ($datos['fecha_expiracion'] !== null) {
            $fecha_expiracion_obj = DateTime::createFromFormat('Y-m-d', $datos['fecha_expiracion']);
            if (!$fecha_expiracion_obj || $fecha_expiracion_obj->format('Y-m-d') !== $datos['fecha_expiracion']) {
                sendResponse(400, ['status' => 400, 'message' => 'Formato de fecha de expiración inválido. Debe ser YYYY-MM-DD']);
                return;
            }
        }

        $resultado = $comunicadoModelo->crear($datos);

        if($resultado !== false && $resultado > 0){
            sendResponse(201, [
                'status' => 201,
                'message' => 'Comunicado registrado exitosamente',
                'data' => ['id_comunicado' => $resultado]
            ]);
        } else {
            sendResponse(500, [
                'status' => 500,
                'message' => 'Error al registrar comunicado'
            ]);
        }
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error en base de datos: ' . $e->getMessage()
        ]);
    }
}

function handleActualizarComunicado($db, $data) {
    try {
        $comunicadoModelo = new ComunicadoModelo($db);

        $camposRequeridos = ['id_comunicado', 'titulo', 'contenido', 'prioridad', 'estado', 'tipo_audiencia'];
        foreach($camposRequeridos as $campo) {
            if(!isset($data[$campo]) || empty(trim($data[$campo]))) {
                sendResponse(400, ['status' => 400, 'message' => "El campo $campo es obligatorio"]);
                return;
            }
        }

        // Validar prioridad
        $prioridadesValidas = ['urgente', 'alta', 'media', 'baja'];
        if (!in_array($data['prioridad'], $prioridadesValidas)) {
            sendResponse(400, ['status' => 400, 'message' => 'Prioridad inválida. Debe ser: urgente, alta, media o baja']);
            return;
        }

        // Validar estado
        $estadosValidos = ['publicado', 'borrador', 'archivado'];
        if (!in_array($data['estado'], $estadosValidos)) {
            sendResponse(400, ['status' => 400, 'message' => 'Estado inválido. Debe ser: publicado, borrador o archivado']);
            return;
        }

        // Validar tipo_audiencia
        $tiposAudienciaValidos = ['Todos', 'Residente', 'Personal'];
        if (!in_array($data['tipo_audiencia'], $tiposAudienciaValidos)) {
            sendResponse(400, ['status' => 400, 'message' => 'Tipo de audiencia inválido. Debe ser: Todos, Residentes o Personal']);
            return;
        }

        $id_comunicado = intval($data['id_comunicado']);
        
        $datos = [
            'titulo' => trim($data['titulo']),
            'contenido' => trim($data['contenido']),
            'fecha_expiracion' => !empty($data['fecha_expiracion']) ? trim($data['fecha_expiracion']) : null,
            'prioridad' => trim($data['prioridad']),
            'estado' => trim($data['estado']),
            'tipo_audiencia' => trim($data['tipo_audiencia'])
        ];

        // Validar formato de fecha_expiracion si se proporciona
        if ($datos['fecha_expiracion'] !== null) {
            $fecha_expiracion_obj = DateTime::createFromFormat('Y-m-d', $datos['fecha_expiracion']);
            if (!$fecha_expiracion_obj || $fecha_expiracion_obj->format('Y-m-d') !== $datos['fecha_expiracion']) {
                sendResponse(400, ['status' => 400, 'message' => 'Formato de fecha de expiración inválido. Debe ser YYYY-MM-DD']);
                return;
            }
        }

        $resultado = $comunicadoModelo->actualizar($id_comunicado, $datos);

        if($resultado === true){
            sendResponse(200, [
                'status' => 200,
                'message' => 'Comunicado actualizado exitosamente'
            ]);
        } else {
            sendResponse(500, [
                'status' => 500,
                'message' => 'Error al actualizar comunicado'
            ]);
        }
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error en base de datos: ' . $e->getMessage()
        ]);
    }
}

function handleCambiarEstado($db, $data) {
    try {
        $comunicadoModelo = new ComunicadoModelo($db);

        $id_comunicado = intval($data['id_comunicado'] ?? 0);
        $estado = $data['estado'] ?? '';

        if ($id_comunicado <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de comunicado inválido']);
            return;
        }

        if (empty($estado)) {
            sendResponse(400, ['status' => 400, 'message' => 'Estado requerido']);
            return;
        }

        // Validar estado
        $estadosValidos = ['publicado', 'borrador', 'archivado', 'eliminado'];
        if (!in_array($estado, $estadosValidos)) {
            sendResponse(400, ['status' => 400, 'message' => 'Estado inválido. Debe ser: publicado, borrador, archivado o eliminado']);
            return;
        }

        $resultado = $comunicadoModelo->cambiarEstado($id_comunicado, $estado);

        if($resultado === true){
            sendResponse(200, [
                'status' => 200,
                'message' => 'Estado del comunicado actualizado exitosamente'
            ]);
        } else {
            sendResponse(500, [
                'status' => 500,
                'message' => 'Error al cambiar estado del comunicado'
            ]);
        }
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error en base de datos: ' . $e->getMessage()
        ]);
    }
}

function handleEliminarComunicado($db, $data) {
    try {
        $comunicadoModelo = new ComunicadoModelo($db);

        $id_comunicado = intval($data['id_comunicado'] ?? 0);

        if ($id_comunicado <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de comunicado inválido']);
            return;
        }

        $resultado = $comunicadoModelo->eliminar($id_comunicado);

        if($resultado === true){
            sendResponse(200, [
                'status' => 200,
                'message' => 'Comunicado eliminado exitosamente'
            ]);
        } else {
            sendResponse(500, [
                'status' => 500,
                'message' => 'Error al eliminar comunicado'
            ]);
        }
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error en base de datos: ' . $e->getMessage()
        ]);
    }
}

// =============================================
// ENDPOINTS PARA CONSULTAS ESPECIALES
// =============================================

function handleObtenerComunicadosPublicos($db, $data) {
    try {
        $comunicadoModelo = new ComunicadoModelo($db);
        $comunicados = $comunicadoModelo->obtenerComunicadosPublicos();

        if ($comunicados === false) {
            $comunicados = [];
        }

        sendResponse(200, [
            'status' => 200,
            'message' => 'Comunicados públicos listados exitosamente',
            'data' => $comunicados,
            'total' => count($comunicados)
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al listar comunicados públicos: ' . $e->getMessage()
        ]);
    }
}

function handleListarComunicadosPublicados($db, $data) {
    try {
        $comunicadoModelo = new ComunicadoModelo($db);
        $comunicados = $comunicadoModelo->listarComunicadosPublicados();

        if ($comunicados === false) {
            $comunicados = [];
        }

        sendResponse(200, [
            'status' => 200,
            'message' => 'Comunicados publicados listados exitosamente',
            'data' => $comunicados,
            'total' => count($comunicados)
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al listar comunicados publicados: ' . $e->getMessage()
        ]);
    }
}

function handleListarComunicadosEliminados($db, $data) {
    try {
        $comunicadoModelo = new ComunicadoModelo($db);
        $comunicados = $comunicadoModelo->listarComunicadosEliminados();

        if ($comunicados === false) {
            $comunicados = [];
        }

        sendResponse(200, [
            'status' => 200,
            'message' => 'Comunicados eliminados listados exitosamente',
            'data' => $comunicados,
            'total' => count($comunicados)
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al listar comunicados eliminados: ' . $e->getMessage()
        ]);
    }
}

function handleRestaurarComunicado($db, $data) {
    try {
        $comunicadoModelo = new ComunicadoModelo($db);

        $id_comunicado = intval($data['id_comunicado'] ?? 0);

        if ($id_comunicado <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de comunicado inválido']);
            return;
        }

        // Verificar que el comunicado existe y está eliminado
        $comunicado = $comunicadoModelo->obtenerPorId($id_comunicado);
        if (!$comunicado) {
            sendResponse(404, ['status' => 404, 'message' => 'Comunicado no encontrado']);
            return;
        }

        if ($comunicado['estado'] != 'eliminado') {
            sendResponse(400, ['status' => 400, 'message' => 'El comunicado no está eliminado. No se puede restaurar.']);
            return;
        }

        $resultado = $comunicadoModelo->restaurar($id_comunicado);

        if($resultado === true){
            sendResponse(200, [
                'status' => 200,
                'message' => 'Comunicado restaurado exitosamente. El estado fue cambiado a borrador.',
                'data' => ['id_comunicado' => $id_comunicado, 'estado_anterior' => 'eliminado', 'estado_nuevo' => 'borrador']
            ]);
        } else {
            sendResponse(500, [
                'status' => 500,
                'message' => 'Error al restaurar comunicado'
            ]);
        }
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error en base de datos: ' . $e->getMessage()
        ]);
    }
}

// =============================================
// ENDPOINTS PARA ESTADÍSTICAS
// =============================================

function handleObtenerEstadisticas($db, $data) {
    try {
        $comunicadoModelo = new ComunicadoModelo($db);
        $estadisticas = $comunicadoModelo->obtenerEstadisticas();

        if ($estadisticas === false) {
            $estadisticas = [
                'total' => 0,
                'publicados' => 0,
                'borradores' => 0,
                'archivados' => 0,
                'eliminados' => 0
            ];
        }

        sendResponse(200, [
            'status' => 200,
            'message' => 'Estadísticas obtenidas exitosamente',
            'data' => $estadisticas
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
        ]);
    }
}

// =============================================
// FUNCIONES AUXILIARES
// =============================================

function sendResponse($statusCode, $data) {
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}
?>


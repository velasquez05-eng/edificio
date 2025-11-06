<?php
// cargofijo.php - API completa para gestión de cargos fijos
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Manejar preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

error_log("DEBUG: cargofijo.php accedido - Método: " . $_SERVER['REQUEST_METHOD']);

try {
    require_once '../../config/database.php';
    require_once '../modelo/CargosFijosModelo.php';

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
            // Gestión de cargos fijos
            case 'listarCargosFijos':
                handleListarCargosFijos($db, $input);
                break;
            case 'obtenerCargosActivos':
                handleObtenerCargosActivos($db, $input);
                break;
            case 'obtenerCargoPorId':
                handleObtenerCargoPorId($db, $input);
                break;
            case 'crearCargo':
                handleCrearCargo($db, $input);
                break;
            case 'actualizarCargo':
                handleActualizarCargo($db, $input);
                break;
            case 'cambiarEstadoCargo':
                handleCambiarEstadoCargo($db, $input);
                break;
            case 'eliminarCargo':
                handleEliminarCargo($db, $input);
                break;
            
            // Funciones auxiliares
            case 'verificarCargoEnUso':
                handleVerificarCargoEnUso($db, $input);
                break;
            case 'obtenerTotalCargosActivos':
                handleObtenerTotalCargosActivos($db, $input);
                break;
            case 'obtenerDepartamentosOcupados':
                handleObtenerDepartamentosOcupados($db, $input);
                break;
            case 'generarConceptosMantenimiento':
                handleGenerarConceptosMantenimiento($db, $input);
                break;
            case 'verificarConceptosGenerados':
                handleVerificarConceptosGenerados($db, $input);
                break;
            case 'obtenerEstadisticasCargos':
                handleObtenerEstadisticasCargos($db, $input);
                break;
            case 'obtenerUltimaGeneracionConceptos':
                handleObtenerUltimaGeneracionConceptos($db, $input);
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
        case 'listarCargosFijos':
            handleListarCargosFijos($db, $_GET);
            break;
        case 'obtenerCargosActivos':
            handleObtenerCargosActivos($db, $_GET);
            break;
        case 'obtenerCargoPorId':
            handleObtenerCargoPorId($db, $_GET);
            break;
        case 'verificarCargoEnUso':
            handleVerificarCargoEnUso($db, $_GET);
            break;
        case 'obtenerTotalCargosActivos':
            handleObtenerTotalCargosActivos($db, $_GET);
            break;
        case 'obtenerDepartamentosOcupados':
            handleObtenerDepartamentosOcupados($db, $_GET);
            break;
        case 'verificarConceptosGenerados':
            handleVerificarConceptosGenerados($db, $_GET);
            break;
        case 'obtenerEstadisticasCargos':
            handleObtenerEstadisticasCargos($db, $_GET);
            break;
        case 'obtenerUltimaGeneracionConceptos':
            handleObtenerUltimaGeneracionConceptos($db, $_GET);
            break;
        default:
            sendResponse(400, ['status' => 400, 'message' => 'Acción GET no válida: ' . $action]);
    }
} else {
    sendResponse(405, ['status' => 405, 'message' => 'Método no permitido']);
}

// =============================================
// ENDPOINTS PARA GESTIÓN DE CARGOS FIJOS
// =============================================

function handleListarCargosFijos($db, $data) {
    try {
        $cargosModelo = new CargosFijosModelo($db);
        $cargos = $cargosModelo->obtenerCargosFijos();

        if ($cargos === false) {
            sendResponse(500, [
                'status' => 500,
                'message' => 'Error al listar cargos fijos'
            ]);
            return;
        }

        sendResponse(200, [
            'status' => 200,
            'message' => 'Cargos fijos listados exitosamente',
            'data' => $cargos,
            'total' => count($cargos)
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al listar cargos fijos: ' . $e->getMessage()
        ]);
    }
}

function handleObtenerCargosActivos($db, $data) {
    try {
        $cargosModelo = new CargosFijosModelo($db);
        $cargos = $cargosModelo->obtenerCargosActivos();

        if ($cargos === false) {
            sendResponse(500, [
                'status' => 500,
                'message' => 'Error al obtener cargos activos'
            ]);
            return;
        }

        sendResponse(200, [
            'status' => 200,
            'message' => 'Cargos activos obtenidos exitosamente',
            'data' => $cargos,
            'total' => count($cargos)
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al obtener cargos activos: ' . $e->getMessage()
        ]);
    }
}

function handleObtenerCargoPorId($db, $data) {
    try {
        $cargosModelo = new CargosFijosModelo($db);

        $id_cargo = intval($data['id_cargo'] ?? 0);

        if ($id_cargo <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de cargo inválido']);
            return;
        }

        $cargo = $cargosModelo->obtenerCargoPorId($id_cargo);

        if (!$cargo) {
            sendResponse(404, ['status' => 404, 'message' => 'Cargo no encontrado']);
            return;
        }

        sendResponse(200, [
            'status' => 200,
            'message' => 'Cargo obtenido exitosamente',
            'data' => $cargo
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al obtener cargo: ' . $e->getMessage()
        ]);
    }
}

function handleCrearCargo($db, $data) {
    try {
        $cargosModelo = new CargosFijosModelo($db);

        $camposRequeridos = ['nombre_cargo', 'monto'];
        foreach($camposRequeridos as $campo) {
            if(!isset($data[$campo]) || empty(trim($data[$campo]))) {
                sendResponse(400, ['status' => 400, 'message' => "El campo $campo es obligatorio"]);
                return;
            }
        }

        $nombre_cargo = trim($data['nombre_cargo']);
        $monto = floatval($data['monto']);
        $descripcion = !empty($data['descripcion']) ? trim($data['descripcion']) : null;
        $estado = !empty($data['estado']) ? trim($data['estado']) : 'activo';

        // Validar monto
        if ($monto <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'El monto debe ser mayor a cero']);
            return;
        }

        // Validar estado
        if (!in_array($estado, ['activo', 'inactivo'])) {
            sendResponse(400, ['status' => 400, 'message' => 'Estado inválido. Debe ser: activo o inactivo']);
            return;
        }

        $resultado = $cargosModelo->crearCargo($nombre_cargo, $monto, $descripcion, $estado);

        if($resultado === true){
            sendResponse(201, [
                'status' => 201,
                'message' => 'Cargo fijo creado exitosamente'
            ]);
        } else {
            sendResponse(500, [
                'status' => 500,
                'message' => 'Error al crear cargo fijo'
            ]);
        }
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error en base de datos: ' . $e->getMessage()
        ]);
    }
}

function handleActualizarCargo($db, $data) {
    try {
        $cargosModelo = new CargosFijosModelo($db);

        $camposRequeridos = ['id_cargo', 'nombre_cargo', 'monto', 'estado'];
        foreach($camposRequeridos as $campo) {
            if(!isset($data[$campo]) || empty(trim($data[$campo]))) {
                sendResponse(400, ['status' => 400, 'message' => "El campo $campo es obligatorio"]);
                return;
            }
        }

        $id_cargo = intval($data['id_cargo']);
        if ($id_cargo <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de cargo inválido']);
            return;
        }

        // Verificar que el cargo exista
        $cargo_existente = $cargosModelo->obtenerCargoPorId($id_cargo);
        if (!$cargo_existente) {
            sendResponse(404, ['status' => 404, 'message' => 'Cargo no encontrado']);
            return;
        }

        $nombre_cargo = trim($data['nombre_cargo']);
        $monto = floatval($data['monto']);
        $descripcion = !empty($data['descripcion']) ? trim($data['descripcion']) : null;
        $estado = trim($data['estado']);

        // Validar monto
        if ($monto <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'El monto debe ser mayor a cero']);
            return;
        }

        // Validar estado
        if (!in_array($estado, ['activo', 'inactivo'])) {
            sendResponse(400, ['status' => 400, 'message' => 'Estado inválido. Debe ser: activo o inactivo']);
            return;
        }

        $resultado = $cargosModelo->actualizarCargo($id_cargo, $nombre_cargo, $monto, $descripcion, $estado);

        if($resultado === true){
            sendResponse(200, [
                'status' => 200,
                'message' => 'Cargo fijo actualizado exitosamente'
            ]);
        } else {
            sendResponse(500, [
                'status' => 500,
                'message' => 'Error al actualizar cargo fijo'
            ]);
        }
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error en base de datos: ' . $e->getMessage()
        ]);
    }
}

function handleCambiarEstadoCargo($db, $data) {
    try {
        $cargosModelo = new CargosFijosModelo($db);

        $camposRequeridos = ['id_cargo', 'estado'];
        foreach($camposRequeridos as $campo) {
            if(!isset($data[$campo]) || empty(trim($data[$campo]))) {
                sendResponse(400, ['status' => 400, 'message' => "El campo $campo es obligatorio"]);
                return;
            }
        }

        $id_cargo = intval($data['id_cargo']);
        if ($id_cargo <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de cargo inválido']);
            return;
        }

        $estado = trim($data['estado']);

        // Validar estado
        if (!in_array($estado, ['activo', 'inactivo'])) {
            sendResponse(400, ['status' => 400, 'message' => 'Estado inválido. Debe ser: activo o inactivo']);
            return;
        }

        // Verificar que el cargo exista
        $cargo_existente = $cargosModelo->obtenerCargoPorId($id_cargo);
        if (!$cargo_existente) {
            sendResponse(404, ['status' => 404, 'message' => 'Cargo no encontrado']);
            return;
        }

        $resultado = $cargosModelo->cambiarEstadoCargo($id_cargo, $estado);

        if($resultado === true){
            sendResponse(200, [
                'status' => 200,
                'message' => 'Estado del cargo actualizado exitosamente'
            ]);
        } else {
            sendResponse(500, [
                'status' => 500,
                'message' => 'Error al cambiar estado del cargo'
            ]);
        }
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error en base de datos: ' . $e->getMessage()
        ]);
    }
}

function handleEliminarCargo($db, $data) {
    try {
        $cargosModelo = new CargosFijosModelo($db);

        $id_cargo = intval($data['id_cargo'] ?? 0);

        if ($id_cargo <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de cargo inválido']);
            return;
        }

        // Verificar que el cargo exista
        $cargo_existente = $cargosModelo->obtenerCargoPorId($id_cargo);
        if (!$cargo_existente) {
            sendResponse(404, ['status' => 404, 'message' => 'Cargo no encontrado']);
            return;
        }

        // Verificar si el cargo está en uso
        $en_uso = $cargosModelo->verificarCargoEnUso($id_cargo);
        if ($en_uso) {
            sendResponse(400, [
                'status' => 400,
                'message' => 'No se puede eliminar el cargo porque está siendo usado en conceptos de mantenimiento'
            ]);
            return;
        }

        $resultado = $cargosModelo->eliminarCargo($id_cargo);

        if($resultado === true){
            sendResponse(200, [
                'status' => 200,
                'message' => 'Cargo fijo eliminado exitosamente'
            ]);
        } else {
            sendResponse(500, [
                'status' => 500,
                'message' => 'Error al eliminar cargo fijo'
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
// ENDPOINTS AUXILIARES
// =============================================

function handleVerificarCargoEnUso($db, $data) {
    try {
        $cargosModelo = new CargosFijosModelo($db);

        $id_cargo = intval($data['id_cargo'] ?? 0);

        if ($id_cargo <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de cargo inválido']);
            return;
        }

        $en_uso = $cargosModelo->verificarCargoEnUso($id_cargo);

        sendResponse(200, [
            'status' => 200,
            'message' => 'Verificación completada',
            'data' => [
                'id_cargo' => $id_cargo,
                'en_uso' => $en_uso
            ]
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al verificar cargo: ' . $e->getMessage()
        ]);
    }
}

function handleObtenerTotalCargosActivos($db, $data) {
    try {
        $cargosModelo = new CargosFijosModelo($db);
        $total = $cargosModelo->obtenerTotalCargosActivos();

        sendResponse(200, [
            'status' => 200,
            'message' => 'Total de cargos activos obtenido exitosamente',
            'data' => [
                'total' => floatval($total)
            ]
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al obtener total de cargos activos: ' . $e->getMessage()
        ]);
    }
}

function handleObtenerDepartamentosOcupados($db, $data) {
    try {
        $cargosModelo = new CargosFijosModelo($db);
        $departamentos = $cargosModelo->obtenerDepartamentosOcupados();

        sendResponse(200, [
            'status' => 200,
            'message' => 'Departamentos ocupados listados exitosamente',
            'data' => $departamentos,
            'total' => count($departamentos)
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al listar departamentos ocupados: ' . $e->getMessage()
        ]);
    }
}

function handleGenerarConceptosMantenimiento($db, $data) {
    try {
        $cargosModelo = new CargosFijosModelo($db);

        $camposRequeridos = ['year', 'month'];
        foreach($camposRequeridos as $campo) {
            if(!isset($data[$campo])) {
                sendResponse(400, ['status' => 400, 'message' => "El campo $campo es obligatorio"]);
                return;
            }
        }

        $year = intval($data['year']);
        $month = intval($data['month']);

        // Validar año
        if ($year < 2020 || $year > 2100) {
            sendResponse(400, ['status' => 400, 'message' => 'Año inválido']);
            return;
        }

        // Validar mes
        if ($month < 1 || $month > 12) {
            sendResponse(400, ['status' => 400, 'message' => 'Mes inválido. Debe ser entre 1 y 12']);
            return;
        }

        // Verificar si ya se generaron conceptos para este mes
        $ya_generados = $cargosModelo->verificarConceptosGenerados($year, $month);
        if ($ya_generados) {
            sendResponse(400, [
                'status' => 400,
                'message' => 'Ya se generaron conceptos de mantenimiento para este mes'
            ]);
            return;
        }

        $resultado = $cargosModelo->generarConceptosMantenimiento($year, $month);

        if($resultado['success'] === true){
            sendResponse(200, [
                'status' => 200,
                'message' => 'Conceptos de mantenimiento generados exitosamente',
                'data' => $resultado
            ]);
        } else {
            sendResponse(500, [
                'status' => 500,
                'message' => $resultado['message'] ?? 'Error al generar conceptos de mantenimiento',
                'data' => $resultado
            ]);
        }
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error en base de datos: ' . $e->getMessage()
        ]);
    }
}

function handleVerificarConceptosGenerados($db, $data) {
    try {
        $cargosModelo = new CargosFijosModelo($db);

        $year = intval($data['year'] ?? 0);
        $month = intval($data['month'] ?? 0);

        if ($year <= 0 || $month < 1 || $month > 12) {
            sendResponse(400, ['status' => 400, 'message' => 'Año y mes son requeridos y deben ser válidos']);
            return;
        }

        $ya_generados = $cargosModelo->verificarConceptosGenerados($year, $month);

        sendResponse(200, [
            'status' => 200,
            'message' => 'Verificación completada',
            'data' => [
                'year' => $year,
                'month' => $month,
                'ya_generados' => $ya_generados
            ]
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al verificar conceptos generados: ' . $e->getMessage()
        ]);
    }
}

function handleObtenerEstadisticasCargos($db, $data) {
    try {
        $cargosModelo = new CargosFijosModelo($db);
        $estadisticas = $cargosModelo->obtenerEstadisticasCargos();

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

function handleObtenerUltimaGeneracionConceptos($db, $data) {
    try {
        $cargosModelo = new CargosFijosModelo($db);
        $resultado = $cargosModelo->obtenerUltimaGeneracionConceptos();

        sendResponse(200, [
            'status' => 200,
            'message' => 'Última generación obtenida exitosamente',
            'data' => $resultado
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al obtener última generación: ' . $e->getMessage()
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




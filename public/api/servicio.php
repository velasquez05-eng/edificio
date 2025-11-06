<?php
// servicio.php - API completa para gestión de servicios
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Manejar preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

error_log("DEBUG: servicio.php accedido - Método: " . $_SERVER['REQUEST_METHOD']);

try {
    require_once '../../config/database.php';
    require_once '../modelo/ServicioModelo.php';

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
            case 'listarServicios':
                handleListarServicios($db, $input);
                break;
            case 'crearServicio':
                handleCrearServicio($db, $input);
                break;
            case 'obtenerServicioPorId':
                handleObtenerServicioPorId($db, $input);
                break;
            case 'actualizarServicio':
                handleActualizarServicio($db, $input);
                break;
            case 'asignarServicioDepartamento':
                handleAsignarServicioDepartamento($db, $input);
                break;
            case 'editarMedidor':
                handleEditarMedidor($db, $input);
                break;
            case 'eliminarMedidor':
                handleEliminarMedidor($db, $input);
                break;
            case 'verificarMedidor':
                handleVerificarMedidor($db, $input);
                break;
            case 'verificarAsignacion':
                handleVerificarAsignacion($db, $input);
                break;
            case 'generarConsumo':
                handleGenerarConsumo($db, $input);
                break;
            case 'generarConsumosMasivos':
                handleGenerarConsumosMasivos($db, $input);
                break;
            case 'obtenerMedidoresDepartamento':
                handleObtenerMedidoresDepartamento($db, $input);
                break;
            case 'verHistorialConsumo':
                handleVerHistorialConsumo($db, $input);
                break;
            case 'generarResumenConsumo':
                handleGenerarResumenConsumo($db, $input);
                break;
            case 'verificarServicio':
                handleVerificarServicio($db, $input);
                break;
            case 'obtenerMedidoresPorServicio':
                handleObtenerMedidoresPorServicio($db, $input);
                break;
            case 'obtenerTodosMedidores':
                handleObtenerTodosMedidores($db, $input);
                break;
            case 'obtenerMedidorPorId':
                handleObtenerMedidorPorId($db, $input);
                break;
            case 'eliminarLectura':
                handleEliminarLectura($db, $input);
                break;
            case 'obtenerLecturaPorId':
                handleObtenerLecturaPorId($db, $input);
                break;
            default:
                sendResponse(400, ['status' => 400, 'message' => 'Acción no válida: ' . $action]);
        }
    } else {
        sendResponse(400, ['status' => 400, 'message' => 'Datos de entrada inválidos o acción no especificada']);
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Manejar GET requests para casos específicos
    $action = $_GET['action'] ?? '';

    switch($action) {
        case 'listarServicios':
            handleListarServicios($db, $_GET);
            break;
        case 'obtenerServicioPorId':
            handleObtenerServicioPorId($db, $_GET);
            break;
        case 'obtenerMedidoresDepartamento':
            handleObtenerMedidoresDepartamento($db, $_GET);
            break;
        case 'verHistorialConsumo':
            handleVerHistorialConsumo($db, $_GET);
            break;
        case 'obtenerMedidoresPorServicio':
            handleObtenerMedidoresPorServicio($db, $_GET);
            break;
        case 'obtenerTodosMedidores':
            handleObtenerTodosMedidores($db, $_GET);
            break;
        case 'obtenerMedidorPorId':
            handleObtenerMedidorPorId($db, $_GET);
            break;
        case 'obtenerLecturaPorId':
            handleObtenerLecturaPorId($db, $_GET);
            break;
        default:
            sendResponse(400, ['status' => 400, 'message' => 'Acción GET no válida: ' . $action]);
    }
} else {
    sendResponse(405, ['status' => 405, 'message' => 'Método no permitido']);
}

// =============================================
// ENDPOINTS PARA SERVICIOS
// =============================================

function handleListarServicios($db, $data) {
    try {
        $servicioModelo = new ServicioModelo($db);
        $servicios = $servicioModelo->listarServicios();

        sendResponse(200, [
            'status' => 200,
            'message' => 'Servicios listados exitosamente',
            'data' => $servicios,
            'total' => count($servicios)
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al listar servicios: ' . $e->getMessage()
        ]);
    }
}

function handleCrearServicio($db, $data) {
    try {
        $servicioModelo = new ServicioModelo($db);

        $camposRequeridos = ['nombre', 'unidad_medida', 'costo_unitario'];
        foreach($camposRequeridos as $campo) {
            if(!isset($data[$campo]) || empty(trim($data[$campo]))) {
                sendResponse(400, ['status' => 400, 'message' => "El campo $campo es obligatorio"]);
                return;
            }
        }

        $nombre = htmlspecialchars(trim($data['nombre']));
        $unidad_medida = htmlspecialchars(trim($data['unidad_medida']));
        $costo_unitario = floatval($data['costo_unitario']);
        $estado = isset($data['estado']) ? htmlspecialchars(trim($data['estado'])) : 'activo';

        // Validaciones adicionales
        if ($costo_unitario < 0) {
            sendResponse(400, ['status' => 400, 'message' => "El costo unitario debe ser un número mayor o igual a 0"]);
            return;
        }

        // Verificar si el servicio ya existe
        if ($servicioModelo->existeServicio($nombre)) {
            sendResponse(400, ['status' => 400, 'message' => "Ya existe un servicio con este nombre"]);
            return;
        }

        $resultado = $servicioModelo->registrarServicio($nombre, $unidad_medida, $costo_unitario, $estado);

        if($resultado){
            sendResponse(201, [
                'status' => 201,
                'message' => "Servicio registrado exitosamente"
            ]);
        } else {
            sendResponse(500, ['status' => 500, 'message' => "Error al registrar servicio"]);
        }

    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error en base de datos: ' . $e->getMessage()
        ]);
    }
}

function handleObtenerServicioPorId($db, $data) {
    try {
        $servicioModelo = new ServicioModelo($db);

        $id_servicio = intval($data['id_servicio'] ?? 0);

        if ($id_servicio <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de servicio inválido']);
            return;
        }

        $servicio = $servicioModelo->obtenerServicioPorId($id_servicio);

        if (!$servicio) {
            sendResponse(404, ['status' => 404, 'message' => 'Servicio no encontrado']);
            return;
        }

        sendResponse(200, [
            'status' => 200,
            'message' => 'Servicio obtenido exitosamente',
            'data' => $servicio
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al obtener servicio: ' . $e->getMessage()
        ]);
    }
}

function handleActualizarServicio($db, $data) {
    try {
        $servicioModelo = new ServicioModelo($db);

        $camposRequeridos = ['id_servicio', 'unidad_medida', 'costo_unitario', 'estado'];
        foreach($camposRequeridos as $campo) {
            if(!isset($data[$campo]) || empty(trim($data[$campo]))) {
                sendResponse(400, ['status' => 400, 'message' => "El campo $campo es obligatorio"]);
                return;
            }
        }

        $id_servicio = intval($data['id_servicio']);
        $unidad_medida = htmlspecialchars(trim($data['unidad_medida']));
        $costo_unitario = floatval($data['costo_unitario']);
        $estado = htmlspecialchars(trim($data['estado']));

        // Validaciones adicionales
        if ($costo_unitario < 0) {
            sendResponse(400, ['status' => 400, 'message' => "El costo unitario debe ser un número mayor o igual a 0"]);
            return;
        }

        if (!in_array($estado, ['activo', 'inactivo'])) {
            sendResponse(400, ['status' => 400, 'message' => "El estado debe ser 'activo' o 'inactivo'"]);
            return;
        }

        $resultado = $servicioModelo->actualizarServicio($id_servicio, $unidad_medida, $costo_unitario, $estado);

        if($resultado){
            sendResponse(200, [
                'status' => 200,
                'message' => "Servicio actualizado exitosamente"
            ]);
        } else {
            sendResponse(500, ['status' => 500, 'message' => "Error al actualizar servicio"]);
        }

    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error en base de datos: ' . $e->getMessage()
        ]);
    }
}

function handleAsignarServicioDepartamento($db, $data) {
    try {
        $servicioModelo = new ServicioModelo($db);

        $camposRequeridos = ['id_departamento', 'id_servicio', 'codigo_medidor', 'fecha_instalacion'];
        foreach($camposRequeridos as $campo) {
            if(!isset($data[$campo]) || empty(trim($data[$campo]))) {
                sendResponse(400, ['status' => 400, 'message' => "El campo $campo es obligatorio"]);
                return;
            }
        }

        $id_departamento = intval($data['id_departamento']);
        $id_servicio = intval($data['id_servicio']);
        $codigo_medidor = htmlspecialchars(trim($data['codigo_medidor']));
        $fecha_instalacion = htmlspecialchars(trim($data['fecha_instalacion']));
        $estado_medidor = isset($data['estado_medidor']) ? htmlspecialchars(trim($data['estado_medidor'])) : 'activo';

        // Verificar si ya existe una asignación
        if ($servicioModelo->existeAsignacion($id_departamento, $id_servicio)) {
            sendResponse(400, ['status' => 400, 'message' => "Este departamento ya tiene asignado este servicio"]);
            return;
        }

        // Verificar si el código de medidor ya existe
        if ($servicioModelo->existeMedidor($codigo_medidor)) {
            sendResponse(400, ['status' => 400, 'message' => "Ya existe un medidor con este código"]);
            return;
        }

        $resultado = $servicioModelo->asignarServicioDepartamento($id_departamento, $id_servicio, $codigo_medidor, $fecha_instalacion, $estado_medidor);

        if($resultado){
            sendResponse(201, [
                'status' => 201,
                'message' => "Servicio asignado al departamento exitosamente"
            ]);
        } else {
            sendResponse(500, ['status' => 500, 'message' => "Error al asignar servicio al departamento"]);
        }

    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error en base de datos: ' . $e->getMessage()
        ]);
    }
}

function handleEditarMedidor($db, $data) {
    try {
        $servicioModelo = new ServicioModelo($db);

        $camposRequeridos = ['id_medidor', 'codigo_medidor', 'fecha_instalacion', 'estado_medidor'];
        foreach($camposRequeridos as $campo) {
            if(!isset($data[$campo]) || empty(trim($data[$campo]))) {
                sendResponse(400, ['status' => 400, 'message' => "El campo $campo es obligatorio"]);
                return;
            }
        }

        $id_medidor = intval($data['id_medidor']);
        $codigo_medidor = htmlspecialchars(trim($data['codigo_medidor']));
        $fecha_instalacion = htmlspecialchars(trim($data['fecha_instalacion']));
        $estado_medidor = htmlspecialchars(trim($data['estado_medidor']));

        // Verificar si el código de medidor ya existe (excluyendo el actual)
        if ($servicioModelo->existeMedidor($codigo_medidor, $id_medidor)) {
            sendResponse(400, ['status' => 400, 'message' => "Ya existe otro medidor con este código"]);
            return;
        }

        $resultado = $servicioModelo->editarMedidor($id_medidor, $codigo_medidor, $fecha_instalacion, $estado_medidor);

        if($resultado){
            sendResponse(200, [
                'status' => 200,
                'message' => "Medidor actualizado exitosamente"
            ]);
        } else {
            sendResponse(500, ['status' => 500, 'message' => "Error al actualizar medidor"]);
        }

    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error en base de datos: ' . $e->getMessage()
        ]);
    }
}

function handleEliminarMedidor($db, $data) {
    try {
        $servicioModelo = new ServicioModelo($db);

        $id_medidor = intval($data['id_medidor'] ?? 0);

        if ($id_medidor <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de medidor inválido']);
            return;
        }

        $resultado = $servicioModelo->eliminarMedidor($id_medidor);

        if($resultado){
            sendResponse(200, [
                'status' => 200,
                'message' => "Medidor eliminado exitosamente"
            ]);
        } else {
            sendResponse(500, ['status' => 500, 'message' => "Error al eliminar medidor"]);
        }

    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error en base de datos: ' . $e->getMessage()
        ]);
    }
}

function handleVerificarMedidor($db, $data) {
    try {
        $servicioModelo = new ServicioModelo($db);

        $codigo_medidor = $data['codigo_medidor'] ?? '';
        $excluir_id = isset($data['excluir_id']) ? intval($data['excluir_id']) : null;

        if (empty($codigo_medidor)) {
            sendResponse(400, ['status' => 400, 'message' => 'Código de medidor requerido']);
            return;
        }

        $existe = $servicioModelo->existeMedidor($codigo_medidor, $excluir_id);

        sendResponse(200, [
            'status' => 200,
            'message' => 'Verificación completada',
            'data' => [
                'existe' => $existe,
                'codigo_medidor' => $codigo_medidor,
                'disponible' => !$existe
            ]
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al verificar medidor: ' . $e->getMessage()
        ]);
    }
}

function handleVerificarAsignacion($db, $data) {
    try {
        $servicioModelo = new ServicioModelo($db);

        $id_departamento = intval($data['id_departamento'] ?? 0);
        $id_servicio = intval($data['id_servicio'] ?? 0);

        if ($id_departamento <= 0 || $id_servicio <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de departamento e ID de servicio son requeridos']);
            return;
        }

        $existe = $servicioModelo->existeAsignacion($id_departamento, $id_servicio);

        sendResponse(200, [
            'status' => 200,
            'message' => 'Verificación completada',
            'data' => [
                'existe' => $existe,
                'id_departamento' => $id_departamento,
                'id_servicio' => $id_servicio
            ]
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al verificar asignación: ' . $e->getMessage()
        ]);
    }
}

function handleGenerarConsumo($db, $data) {
    try {
        $servicioModelo = new ServicioModelo($db);

        $camposRequeridos = ['id_medidor', 'consumo'];
        foreach($camposRequeridos as $campo) {
            if(!isset($data[$campo])) {
                sendResponse(400, ['status' => 400, 'message' => "El campo $campo es obligatorio"]);
                return;
            }
        }

        $id_medidor = intval($data['id_medidor']);
        $consumo = floatval($data['consumo']);
        $fecha_hora = isset($data['fecha_hora']) ? htmlspecialchars(trim($data['fecha_hora'])) : null;

        if ($id_medidor <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de medidor inválido']);
            return;
        }

        if ($consumo < 0) {
            sendResponse(400, ['status' => 400, 'message' => 'El consumo debe ser mayor o igual a 0']);
            return;
        }

        $resultado = $servicioModelo->generarConsumo($id_medidor, $consumo, $fecha_hora);

        if($resultado){
            sendResponse(201, [
                'status' => 201,
                'message' => "Consumo registrado exitosamente"
            ]);
        } else {
            sendResponse(500, ['status' => 500, 'message' => "Error al registrar consumo"]);
        }

    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error en base de datos: ' . $e->getMessage()
        ]);
    }
}

function handleGenerarConsumosMasivos($db, $data) {
    try {
        $servicioModelo = new ServicioModelo($db);

        $camposRequeridos = ['year', 'month'];
        foreach($camposRequeridos as $campo) {
            if(!isset($data[$campo])) {
                sendResponse(400, ['status' => 400, 'message' => "El campo $campo es obligatorio"]);
                return;
            }
        }

        $year = intval($data['year']);
        $month = intval($data['month']);
        $id_departamento = isset($data['id_departamento']) ? intval($data['id_departamento']) : null;

        // Validaciones
        if ($year < 2000 || $year > 2100) {
            sendResponse(400, ['status' => 400, 'message' => 'Año inválido']);
            return;
        }

        if ($month < 1 || $month > 12) {
            sendResponse(400, ['status' => 400, 'message' => 'Mes inválido']);
            return;
        }

        $resultado = $servicioModelo->generarConsumosMasivos($year, $month, $id_departamento);

        if($resultado !== false){
            sendResponse(201, [
                'status' => 201,
                'message' => "Consumos masivos generados exitosamente",
                'data' => [
                    'total_registros' => $resultado,
                    'year' => $year,
                    'month' => $month
                ]
            ]);
        } else {
            sendResponse(500, ['status' => 500, 'message' => "Error al generar consumos masivos"]);
        }

    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error en base de datos: ' . $e->getMessage()
        ]);
    }
}

function handleObtenerMedidoresDepartamento($db, $data) {
    try {
        $servicioModelo = new ServicioModelo($db);

        $id_departamento = intval($data['id_departamento'] ?? 0);

        if ($id_departamento <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de departamento inválido']);
            return;
        }

        $medidores = $servicioModelo->obtenerMedidoresDepartamento($id_departamento);

        sendResponse(200, [
            'status' => 200,
            'message' => 'Medidores obtenidos exitosamente',
            'data' => $medidores,
            'total' => count($medidores)
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al obtener medidores: ' . $e->getMessage()
        ]);
    }
}

function handleVerHistorialConsumo($db, $data) {
    try {
        $servicioModelo = new ServicioModelo($db);

        $id_medidor = intval($data['id_medidor'] ?? 0);

        if ($id_medidor <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de medidor inválido']);
            return;
        }

        $fecha_inicio = isset($data['fecha_inicio']) ? htmlspecialchars(trim($data['fecha_inicio'])) : null;
        $fecha_fin = isset($data['fecha_fin']) ? htmlspecialchars(trim($data['fecha_fin'])) : null;

        $historial = $servicioModelo->verHistorialConsumo($id_medidor, $fecha_inicio, $fecha_fin);

        sendResponse(200, [
            'status' => 200,
            'message' => 'Historial de consumo obtenido exitosamente',
            'data' => $historial,
            'total' => count($historial)
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al obtener historial de consumo: ' . $e->getMessage()
        ]);
    }
}

function handleGenerarResumenConsumo($db, $data) {
    try {
        $servicioModelo = new ServicioModelo($db);

        $camposRequeridos = ['id_medidor', 'fecha_inicio', 'fecha_fin'];
        foreach($camposRequeridos as $campo) {
            if(!isset($data[$campo]) || empty(trim($data[$campo]))) {
                sendResponse(400, ['status' => 400, 'message' => "El campo $campo es obligatorio"]);
                return;
            }
        }

        $id_medidor = intval($data['id_medidor']);
        $fecha_inicio = htmlspecialchars(trim($data['fecha_inicio']));
        $fecha_fin = htmlspecialchars(trim($data['fecha_fin']));

        if ($id_medidor <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de medidor inválido']);
            return;
        }

        $resultado = $servicioModelo->generarResumenConsumo($id_medidor, $fecha_inicio, $fecha_fin);

        if($resultado){
            sendResponse(201, [
                'status' => 201,
                'message' => "Resumen de consumo generado exitosamente"
            ]);
        } else {
            sendResponse(500, ['status' => 500, 'message' => "Error al generar resumen de consumo"]);
        }

    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error en base de datos: ' . $e->getMessage()
        ]);
    }
}

function handleVerificarServicio($db, $data) {
    try {
        $servicioModelo = new ServicioModelo($db);

        $nombre = $data['nombre'] ?? '';

        if (empty($nombre)) {
            sendResponse(400, ['status' => 400, 'message' => 'Nombre de servicio requerido']);
            return;
        }

        $existe = $servicioModelo->existeServicio($nombre);

        sendResponse(200, [
            'status' => 200,
            'message' => 'Verificación completada',
            'data' => [
                'existe' => $existe,
                'nombre' => $nombre,
                'disponible' => !$existe
            ]
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al verificar servicio: ' . $e->getMessage()
        ]);
    }
}

function handleObtenerMedidoresPorServicio($db, $data) {
    try {
        $servicioModelo = new ServicioModelo($db);

        $id_servicio = intval($data['id_servicio'] ?? 0);

        if ($id_servicio <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de servicio inválido']);
            return;
        }

        $medidores = $servicioModelo->obtenerMedidoresPorServicio($id_servicio);

        sendResponse(200, [
            'status' => 200,
            'message' => 'Medidores obtenidos exitosamente',
            'data' => $medidores,
            'total' => count($medidores)
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al obtener medidores: ' . $e->getMessage()
        ]);
    }
}

function handleObtenerTodosMedidores($db, $data) {
    try {
        $servicioModelo = new ServicioModelo($db);
        $medidores = $servicioModelo->obtenerTodosMedidores();

        sendResponse(200, [
            'status' => 200,
            'message' => 'Medidores obtenidos exitosamente',
            'data' => $medidores,
            'total' => count($medidores)
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al obtener medidores: ' . $e->getMessage()
        ]);
    }
}

function handleObtenerMedidorPorId($db, $data) {
    try {
        $servicioModelo = new ServicioModelo($db);

        $id_medidor = intval($data['id_medidor'] ?? 0);

        if ($id_medidor <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de medidor inválido']);
            return;
        }

        $medidor = $servicioModelo->obtenerMedidorPorId($id_medidor);

        if (!$medidor) {
            sendResponse(404, ['status' => 404, 'message' => 'Medidor no encontrado']);
            return;
        }

        sendResponse(200, [
            'status' => 200,
            'message' => 'Medidor obtenido exitosamente',
            'data' => $medidor
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al obtener medidor: ' . $e->getMessage()
        ]);
    }
}

function handleEliminarLectura($db, $data) {
    try {
        $servicioModelo = new ServicioModelo($db);

        $id_lectura = intval($data['id_lectura'] ?? 0);

        if ($id_lectura <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de lectura inválido']);
            return;
        }

        $resultado = $servicioModelo->eliminarLectura($id_lectura);

        if($resultado){
            sendResponse(200, [
                'status' => 200,
                'message' => "Lectura eliminada exitosamente"
            ]);
        } else {
            sendResponse(500, ['status' => 500, 'message' => "Error al eliminar lectura"]);
        }

    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error en base de datos: ' . $e->getMessage()
        ]);
    }
}

function handleObtenerLecturaPorId($db, $data) {
    try {
        $servicioModelo = new ServicioModelo($db);

        $id_lectura = intval($data['id_lectura'] ?? 0);

        if ($id_lectura <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de lectura inválido']);
            return;
        }

        $lectura = $servicioModelo->obtenerLecturaPorId($id_lectura);

        if (!$lectura) {
            sendResponse(404, ['status' => 404, 'message' => 'Lectura no encontrada']);
            return;
        }

        sendResponse(200, [
            'status' => 200,
            'message' => 'Lectura obtenida exitosamente',
            'data' => $lectura
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al obtener lectura: ' . $e->getMessage()
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
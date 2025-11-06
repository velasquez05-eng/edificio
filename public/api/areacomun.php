<?php
// areacomun.php - API completa para gestión de áreas comunes y reservas
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Manejar preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

error_log("DEBUG: areacomun.php accedido - Método: " . $_SERVER['REQUEST_METHOD']);

try {
    require_once '../../config/database.php';
    require_once '../modelo/AreaComunModelo.php';

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
            // Áreas comunes
            case 'listarAreas':
                handleListarAreas($db, $input);
                break;
            case 'obtenerAreaPorId':
                handleObtenerAreaPorId($db, $input);
                break;
            case 'crearArea':
                handleCrearArea($db, $input);
                break;
            case 'editarArea':
                handleEditarArea($db, $input);
                break;
            case 'eliminarArea':
                handleEliminarArea($db, $input);
                break;
            
            // Mantenimiento
            case 'programarMantenimiento':
                handleProgramarMantenimiento($db, $input);
                break;
            case 'finalizarMantenimiento':
                handleFinalizarMantenimiento($db, $input);
                break;
            
            // Reservas
            case 'listarReservasPorPersona':
                handleListarReservasPorPersona($db, $input);
                break;
            case 'listarReservasPorArea':
                handleListarReservasPorArea($db, $input);
                break;
            case 'listarReservasPendientes':
                handleListarReservasPendientes($db, $input);
                break;
            case 'listarReservasDelMes':
                handleListarReservasDelMes($db, $input);
                break;
            case 'obtenerReservaEspecifica':
                handleObtenerReservaEspecifica($db, $input);
                break;
            case 'crearReserva':
                handleCrearReserva($db, $input);
                break;
            case 'modificarReserva':
                handleModificarReserva($db, $input);
                break;
            case 'cancelarReserva':
                handleCancelarReserva($db, $input);
                break;
            case 'cambiarEstadoReserva':
                handleCambiarEstadoReserva($db, $input);
                break;
            
            // Verificaciones
            case 'verificarDisponibilidad':
                handleVerificarDisponibilidad($db, $input);
                break;
            
            // Estadísticas
            case 'contarReservasPendientes':
                handleContarReservasPendientes($db, $input);
                break;
            case 'contarReservasEsteMes':
                handleContarReservasEsteMes($db, $input);
                break;
            case 'contarAreasPorEstado':
                handleContarAreasPorEstado($db, $input);
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
        case 'listarAreas':
            handleListarAreas($db, $_GET);
            break;
        case 'obtenerAreaPorId':
            handleObtenerAreaPorId($db, $_GET);
            break;
        case 'listarReservasPorPersona':
            handleListarReservasPorPersona($db, $_GET);
            break;
        case 'listarReservasPorArea':
            handleListarReservasPorArea($db, $_GET);
            break;
        case 'listarReservasPendientes':
            handleListarReservasPendientes($db, $_GET);
            break;
        case 'listarReservasDelMes':
            handleListarReservasDelMes($db, $_GET);
            break;
        case 'obtenerReservaEspecifica':
            handleObtenerReservaEspecifica($db, $_GET);
            break;
        case 'contarReservasPendientes':
            handleContarReservasPendientes($db, $_GET);
            break;
        case 'contarReservasEsteMes':
            handleContarReservasEsteMes($db, $_GET);
            break;
        case 'contarAreasPorEstado':
            handleContarAreasPorEstado($db, $_GET);
            break;
        default:
            sendResponse(400, ['status' => 400, 'message' => 'Acción GET no válida: ' . $action]);
    }
} else {
    sendResponse(405, ['status' => 405, 'message' => 'Método no permitido']);
}

// =============================================
// ENDPOINTS PARA GESTIÓN DE ÁREAS COMUNES
// =============================================

function handleListarAreas($db, $data) {
    try {
        $areaModelo = new AreaComunModelo($db);
        $areas = $areaModelo->listarAreas();

        if ($areas === false) {
            sendResponse(500, [
                'status' => 500,
                'message' => 'Error al listar áreas comunes'
            ]);
            return;
        }

        sendResponse(200, [
            'status' => 200,
            'message' => 'Áreas comunes listadas exitosamente',
            'data' => $areas,
            'total' => count($areas)
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al listar áreas comunes: ' . $e->getMessage()
        ]);
    }
}

function handleObtenerAreaPorId($db, $data) {
    try {
        $areaModelo = new AreaComunModelo($db);

        $id_area = intval($data['id_area'] ?? 0);

        if ($id_area <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de área inválido']);
            return;
        }

        $area = $areaModelo->obtenerAreaPorId($id_area);

        if (!$area) {
            sendResponse(404, ['status' => 404, 'message' => 'Área común no encontrada']);
            return;
        }

        sendResponse(200, [
            'status' => 200,
            'message' => 'Área común obtenida exitosamente',
            'data' => $area
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al obtener área común: ' . $e->getMessage()
        ]);
    }
}

function handleCrearArea($db, $data) {
    try {
        $areaModelo = new AreaComunModelo($db);

        $camposRequeridos = ['nombre', 'capacidad'];
        foreach($camposRequeridos as $campo) {
            if(!isset($data[$campo]) || empty(trim($data[$campo]))) {
                sendResponse(400, ['status' => 400, 'message' => "El campo $campo es obligatorio"]);
                return;
            }
        }

        $nombre = trim($data['nombre']);
        $descripcion = trim($data['descripcion'] ?? '');
        $capacidad = intval($data['capacidad']);
        $estado = $data['estado'] ?? 'disponible';
        $costo_reserva = floatval($data['costo_reserva'] ?? 0);

        if ($capacidad < 1) {
            sendResponse(400, ['status' => 400, 'message' => 'La capacidad debe ser mayor a 0']);
            return;
        }

        $resultado = $areaModelo->registrarArea($nombre, $descripcion, $capacidad, $estado, $costo_reserva);

        if($resultado !== false && $resultado > 0){
            sendResponse(201, [
                'status' => 201,
                'message' => 'Área común registrada exitosamente',
                'data' => ['id_area' => $resultado]
            ]);
        } else {
            sendResponse(500, [
                'status' => 500,
                'message' => 'Error al registrar área común'
            ]);
        }
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error en base de datos: ' . $e->getMessage()
        ]);
    }
}

function handleEditarArea($db, $data) {
    try {
        $areaModelo = new AreaComunModelo($db);

        $camposRequeridos = ['id_area', 'nombre', 'capacidad'];
        foreach($camposRequeridos as $campo) {
            if(!isset($data[$campo]) || empty(trim($data[$campo]))) {
                sendResponse(400, ['status' => 400, 'message' => "El campo $campo es obligatorio"]);
                return;
            }
        }

        $id_area = intval($data['id_area']);
        $nombre = trim($data['nombre']);
        $descripcion = trim($data['descripcion'] ?? '');
        $capacidad = intval($data['capacidad']);
        $costo_reserva = floatval($data['costo_reserva'] ?? 0);
        $estado = $data['estado'] ?? 'disponible';

        if ($capacidad < 1) {
            sendResponse(400, ['status' => 400, 'message' => 'La capacidad debe ser mayor a 0']);
            return;
        }

        $resultado = $areaModelo->editarArea($id_area, $nombre, $descripcion, $capacidad, $costo_reserva, $estado);

        if($resultado === true){
            sendResponse(200, [
                'status' => 200,
                'message' => 'Área común actualizada exitosamente'
            ]);
        } else {
            sendResponse(500, [
                'status' => 500,
                'message' => 'Error al actualizar área común'
            ]);
        }
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error en base de datos: ' . $e->getMessage()
        ]);
    }
}

function handleEliminarArea($db, $data) {
    try {
        $areaModelo = new AreaComunModelo($db);

        $id_area = intval($data['id_area'] ?? 0);

        if ($id_area <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de área inválido']);
            return;
        }

        $resultado = $areaModelo->eliminarArea($id_area);

        if($resultado === true){
            sendResponse(200, [
                'status' => 200,
                'message' => 'Área común eliminada exitosamente'
            ]);
        } else {
            sendResponse(500, [
                'status' => 500,
                'message' => 'Error al eliminar área común'
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
// ENDPOINTS PARA MANTENIMIENTO
// =============================================

function handleProgramarMantenimiento($db, $data) {
    try {
        $areaModelo = new AreaComunModelo($db);

        $camposRequeridos = ['id_persona', 'id_area', 'fecha_inicio', 'fecha_fin'];
        foreach($camposRequeridos as $campo) {
            if(!isset($data[$campo]) || empty(trim($data[$campo]))) {
                sendResponse(400, ['status' => 400, 'message' => "El campo $campo es obligatorio"]);
                return;
            }
        }

        $id_persona = intval($data['id_persona']);
        $id_area = intval($data['id_area']);
        $fecha_inicio = trim($data['fecha_inicio']);
        $fecha_fin = trim($data['fecha_fin']);

        $resultado = $areaModelo->programarMantenimiento($id_persona, $id_area, $fecha_inicio, $fecha_fin);

        if($resultado === true){
            sendResponse(200, [
                'status' => 200,
                'message' => 'Mantenimiento programado exitosamente'
            ]);
        } else {
            sendResponse(500, [
                'status' => 500,
                'message' => 'Error al programar mantenimiento'
            ]);
        }
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error en base de datos: ' . $e->getMessage()
        ]);
    }
}

function handleFinalizarMantenimiento($db, $data) {
    try {
        $areaModelo = new AreaComunModelo($db);

        $id_area = intval($data['id_area'] ?? 0);

        if ($id_area <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de área inválido']);
            return;
        }

        $resultado = $areaModelo->finalizarMantenimiento($id_area);

        if($resultado === true){
            sendResponse(200, [
                'status' => 200,
                'message' => 'Mantenimiento finalizado exitosamente'
            ]);
        } else {
            sendResponse(500, [
                'status' => 500,
                'message' => 'Error al finalizar mantenimiento'
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
// ENDPOINTS PARA RESERVAS
// =============================================

function handleListarReservasPorPersona($db, $data) {
    try {
        $areaModelo = new AreaComunModelo($db);

        $id_persona = intval($data['id_persona'] ?? 0);

        if ($id_persona <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de persona inválido']);
            return;
        }

        $reservas = $areaModelo->obtenerReservasPorPersona($id_persona);

        if ($reservas === false) {
            $reservas = [];
        }

        sendResponse(200, [
            'status' => 200,
            'message' => 'Reservas por persona listadas exitosamente',
            'data' => $reservas,
            'total' => count($reservas)
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al listar reservas por persona: ' . $e->getMessage()
        ]);
    }
}

function handleListarReservasPorArea($db, $data) {
    try {
        $areaModelo = new AreaComunModelo($db);

        $id_area = intval($data['id_area'] ?? 0);

        if ($id_area <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de área inválido']);
            return;
        }

        $reservas = $areaModelo->obtenerReservasPorArea($id_area);

        if ($reservas === false) {
            $reservas = [];
        }

        sendResponse(200, [
            'status' => 200,
            'message' => 'Reservas por área listadas exitosamente',
            'data' => $reservas,
            'total' => count($reservas)
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al listar reservas por área: ' . $e->getMessage()
        ]);
    }
}

function handleListarReservasPendientes($db, $data) {
    try {
        $areaModelo = new AreaComunModelo($db);
        $reservas = $areaModelo->obtenerReservasPendientes();

        if ($reservas === false) {
            $reservas = [];
        }

        sendResponse(200, [
            'status' => 200,
            'message' => 'Reservas pendientes listadas exitosamente',
            'data' => $reservas,
            'total' => count($reservas)
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al listar reservas pendientes: ' . $e->getMessage()
        ]);
    }
}

function handleListarReservasDelMes($db, $data) {
    try {
        $areaModelo = new AreaComunModelo($db);

        $mes = $data['mes'] ?? date('Y-m');

        if (!preg_match('/^\d{4}-\d{2}$/', $mes)) {
            $mes = date('Y-m');
        }

        $reservas = $areaModelo->obtenerReservasDelMes($mes);

        if ($reservas === false) {
            $reservas = [];
        }

        sendResponse(200, [
            'status' => 200,
            'message' => 'Reservas del mes listadas exitosamente',
            'data' => $reservas,
            'mes' => $mes,
            'total' => count($reservas)
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al listar reservas del mes: ' . $e->getMessage()
        ]);
    }
}

function handleObtenerReservaEspecifica($db, $data) {
    try {
        $areaModelo = new AreaComunModelo($db);

        $camposRequeridos = ['id_persona', 'id_area', 'fecha_reserva', 'hora_inicio'];
        foreach($camposRequeridos as $campo) {
            if(!isset($data[$campo]) || empty(trim($data[$campo]))) {
                sendResponse(400, ['status' => 400, 'message' => "El campo $campo es obligatorio"]);
                return;
            }
        }

        $id_persona = intval($data['id_persona']);
        $id_area = intval($data['id_area']);
        $fecha_reserva = trim($data['fecha_reserva']);
        $hora_inicio = trim($data['hora_inicio']);

        $reserva = $areaModelo->obtenerReservaEspecifica($id_persona, $id_area, $fecha_reserva, $hora_inicio);

        if (!$reserva) {
            sendResponse(404, ['status' => 404, 'message' => 'Reserva no encontrada']);
            return;
        }

        sendResponse(200, [
            'status' => 200,
            'message' => 'Reserva obtenida exitosamente',
            'data' => $reserva
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al obtener reserva: ' . $e->getMessage()
        ]);
    }
}

function handleCrearReserva($db, $data) {
    try {
        $areaModelo = new AreaComunModelo($db);

        $camposRequeridos = ['id_persona', 'id_area', 'fecha_reserva', 'hora_inicio', 'hora_fin', 'motivo'];
        foreach($camposRequeridos as $campo) {
            if(!isset($data[$campo]) || empty(trim($data[$campo]))) {
                sendResponse(400, ['status' => 400, 'message' => "El campo $campo es obligatorio"]);
                return;
            }
        }

        $id_persona = intval($data['id_persona']);
        $id_area = intval($data['id_area']);
        $fecha_reserva = trim($data['fecha_reserva']);
        $hora_inicio = trim($data['hora_inicio']);
        $hora_fin = trim($data['hora_fin']);
        $motivo = trim($data['motivo']);
        $estado = $data['estado'] ?? 'pendiente';

        // Validaciones
        if (strlen($motivo) < 10) {
            sendResponse(400, ['status' => 400, 'message' => 'El motivo debe tener al menos 10 caracteres']);
            return;
        }

        $fecha_hoy = date('Y-m-d');
        if ($fecha_reserva < $fecha_hoy) {
            sendResponse(400, ['status' => 400, 'message' => 'No se pueden hacer reservas para fechas pasadas']);
            return;
        }

        if ($hora_fin <= $hora_inicio) {
            sendResponse(400, ['status' => 400, 'message' => 'La hora de fin debe ser mayor a la hora de inicio']);
            return;
        }

        // Verificar disponibilidad
        $disponible = $areaModelo->verificarDisponibilidad($id_area, $fecha_reserva, $hora_inicio, $hora_fin);
        if (!$disponible) {
            sendResponse(400, ['status' => 400, 'message' => 'El horario no está disponible. Ya existe una reserva en ese horario']);
            return;
        }

        $resultado = $areaModelo->registrarReserva($id_persona, $id_area, $fecha_reserva, $hora_inicio, $hora_fin, $motivo, $estado);

        if($resultado === true){
            sendResponse(201, [
                'status' => 201,
                'message' => 'Reserva registrada exitosamente'
            ]);
        } else {
            sendResponse(500, [
                'status' => 500,
                'message' => 'Error al registrar la reserva'
            ]);
        }
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error en base de datos: ' . $e->getMessage()
        ]);
    }
}

function handleModificarReserva($db, $data) {
    try {
        $areaModelo = new AreaComunModelo($db);

        $camposRequeridos = ['id_persona', 'id_area', 'fecha_reserva_original', 'hora_inicio_original', 'fecha_reserva', 'hora_inicio', 'hora_fin'];
        foreach($camposRequeridos as $campo) {
            if(!isset($data[$campo]) || empty(trim($data[$campo]))) {
                sendResponse(400, ['status' => 400, 'message' => "El campo $campo es obligatorio"]);
                return;
            }
        }

        $id_persona = intval($data['id_persona']);
        $id_area = intval($data['id_area']);
        $fecha_reserva_original = trim($data['fecha_reserva_original']);
        $hora_inicio_original = trim($data['hora_inicio_original']);
        $fecha_reserva = trim($data['fecha_reserva']);
        $hora_inicio = trim($data['hora_inicio']);
        $hora_fin = trim($data['hora_fin']);

        // Validaciones
        $fecha_hoy = date('Y-m-d');
        if ($fecha_reserva < $fecha_hoy) {
            sendResponse(400, ['status' => 400, 'message' => 'No se pueden modificar reservas para fechas pasadas']);
            return;
        }

        if ($hora_fin <= $hora_inicio) {
            sendResponse(400, ['status' => 400, 'message' => 'La hora de fin debe ser mayor a la hora de inicio']);
            return;
        }

        // Verificar que la reserva existe
        $reservaExistente = $areaModelo->verificarReservaUsuario($id_persona, $id_area, $fecha_reserva_original, $hora_inicio_original);
        if (!$reservaExistente) {
            sendResponse(404, ['status' => 404, 'message' => 'Reserva no encontrada o no pertenece al usuario']);
            return;
        }

        // Verificar disponibilidad del nuevo horario
        $disponible = $areaModelo->verificarDisponibilidadModificacion($id_area, $fecha_reserva, $hora_inicio, $hora_fin, $fecha_reserva_original, $hora_inicio_original);
        if (!$disponible) {
            sendResponse(400, ['status' => 400, 'message' => 'El nuevo horario no está disponible. Ya existe una reserva en ese horario']);
            return;
        }

        // Verificar que el usuario no tenga otra reserva en el mismo horario
        $reservaMismoHorario = $areaModelo->verificarReservaUsuarioMismoHorario(
            $id_persona,
            $id_area,
            $fecha_reserva,
            $hora_inicio,
            $hora_fin,
            $fecha_reserva_original,
            $hora_inicio_original
        );

        if ($reservaMismoHorario) {
            sendResponse(400, ['status' => 400, 'message' => 'Ya tienes otra reserva en ese horario para la misma área']);
            return;
        }

        $resultado = $areaModelo->modificarReserva($id_persona, $id_area, $fecha_reserva_original, $hora_inicio_original, $fecha_reserva, $hora_inicio, $hora_fin);

        if($resultado === true){
            sendResponse(200, [
                'status' => 200,
                'message' => 'Reserva modificada exitosamente'
            ]);
        } else {
            sendResponse(500, [
                'status' => 500,
                'message' => 'Error al modificar la reserva'
            ]);
        }
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error en base de datos: ' . $e->getMessage()
        ]);
    }
}

function handleCancelarReserva($db, $data) {
    try {
        $areaModelo = new AreaComunModelo($db);

        $camposRequeridos = ['id_persona', 'id_area', 'fecha_reserva', 'hora_inicio'];
        foreach($camposRequeridos as $campo) {
            if(!isset($data[$campo]) || empty(trim($data[$campo]))) {
                sendResponse(400, ['status' => 400, 'message' => "El campo $campo es obligatorio"]);
                return;
            }
        }

        $id_persona = intval($data['id_persona']);
        $id_area = intval($data['id_area']);
        $fecha_reserva = trim($data['fecha_reserva']);
        $hora_inicio = trim($data['hora_inicio']);

        // Verificar que la reserva existe
        $reservaExistente = $areaModelo->verificarReservaUsuario($id_persona, $id_area, $fecha_reserva, $hora_inicio);
        if (!$reservaExistente) {
            sendResponse(404, ['status' => 404, 'message' => 'Reserva no encontrada o no pertenece al usuario']);
            return;
        }

        $resultado = $areaModelo->cancelarReservaUsuario($id_persona, $id_area, $fecha_reserva, $hora_inicio);

        if($resultado === true){
            sendResponse(200, [
                'status' => 200,
                'message' => 'Reserva cancelada exitosamente'
            ]);
        } else {
            sendResponse(500, [
                'status' => 500,
                'message' => 'Error al cancelar la reserva'
            ]);
        }
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error en base de datos: ' . $e->getMessage()
        ]);
    }
}

function handleCambiarEstadoReserva($db, $data) {
    try {
        $areaModelo = new AreaComunModelo($db);

        $camposRequeridos = ['id_persona', 'id_area', 'fecha_reserva', 'hora_inicio', 'nuevo_estado'];
        foreach($camposRequeridos as $campo) {
            if(!isset($data[$campo]) || empty(trim($data[$campo]))) {
                sendResponse(400, ['status' => 400, 'message' => "El campo $campo es obligatorio"]);
                return;
            }
        }

        $id_persona = intval($data['id_persona']);
        $id_area = intval($data['id_area']);
        $fecha_reserva = trim($data['fecha_reserva']);
        $hora_inicio = trim($data['hora_inicio']);
        $nuevo_estado = trim($data['nuevo_estado']);

        // Validar estado
        $estadosValidos = ['pendiente', 'confirmada', 'cancelada'];
        if (!in_array($nuevo_estado, $estadosValidos)) {
            sendResponse(400, ['status' => 400, 'message' => 'Estado inválido. Debe ser: pendiente, confirmada o cancelada']);
            return;
        }

        $resultado = $areaModelo->actualizarEstadoReserva($id_persona, $id_area, $fecha_reserva, $hora_inicio, $nuevo_estado);

        if($resultado === true){
            sendResponse(200, [
                'status' => 200,
                'message' => 'Estado de reserva actualizado exitosamente'
            ]);
        } else {
            sendResponse(500, [
                'status' => 500,
                'message' => 'Error al actualizar el estado de la reserva'
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
// ENDPOINTS PARA VERIFICACIONES
// =============================================

function handleVerificarDisponibilidad($db, $data) {
    try {
        $areaModelo = new AreaComunModelo($db);

        $camposRequeridos = ['id_area', 'fecha', 'hora_inicio', 'hora_fin'];
        foreach($camposRequeridos as $campo) {
            if(!isset($data[$campo]) || empty(trim($data[$campo]))) {
                sendResponse(400, ['status' => 400, 'message' => "El campo $campo es obligatorio"]);
                return;
            }
        }

        $id_area = intval($data['id_area']);
        $fecha = trim($data['fecha']);
        $hora_inicio = trim($data['hora_inicio']);
        $hora_fin = trim($data['hora_fin']);

        $disponible = $areaModelo->verificarDisponibilidad($id_area, $fecha, $hora_inicio, $hora_fin);

        sendResponse(200, [
            'status' => 200,
            'message' => 'Verificación de disponibilidad completada',
            'data' => [
                'disponible' => $disponible,
                'id_area' => $id_area,
                'fecha' => $fecha,
                'hora_inicio' => $hora_inicio,
                'hora_fin' => $hora_fin
            ]
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al verificar disponibilidad: ' . $e->getMessage()
        ]);
    }
}

// =============================================
// ENDPOINTS PARA ESTADÍSTICAS
// =============================================

function handleContarReservasPendientes($db, $data) {
    try {
        $areaModelo = new AreaComunModelo($db);
        $total = $areaModelo->contarReservasPendientes();

        sendResponse(200, [
            'status' => 200,
            'message' => 'Conteo de reservas pendientes completado',
            'data' => [
                'total_pendientes' => $total
            ]
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al contar reservas pendientes: ' . $e->getMessage()
        ]);
    }
}

function handleContarReservasEsteMes($db, $data) {
    try {
        $areaModelo = new AreaComunModelo($db);
        $total = $areaModelo->contarReservasEsteMes();

        sendResponse(200, [
            'status' => 200,
            'message' => 'Conteo de reservas del mes completado',
            'data' => [
                'total_reservas' => $total,
                'mes' => date('Y-m')
            ]
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al contar reservas del mes: ' . $e->getMessage()
        ]);
    }
}

function handleContarAreasPorEstado($db, $data) {
    try {
        $areaModelo = new AreaComunModelo($db);
        $conteo = $areaModelo->contarAreasPorEstado();

        sendResponse(200, [
            'status' => 200,
            'message' => 'Conteo de áreas por estado completado',
            'data' => $conteo
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al contar áreas por estado: ' . $e->getMessage()
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


<?php
// incidente.php - API completa para gestión de incidentes
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Manejar preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

error_log("DEBUG: incidente.php accedido - Método: " . $_SERVER['REQUEST_METHOD']);

try {
    require_once '../../config/database.php';
    require_once '../modelo/IncidenteModelo.php';

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
            // Listado y consulta
            case 'listarIncidentes':
                handleListarIncidentes($db, $input);
                break;
            case 'listarIncidentesPorEstado':
                handleListarIncidentesPorEstado($db, $input);
                break;
            case 'listarIncidentesPorReasignacion':
                handleListarIncidentesPorReasignacion($db, $input);
                break;
            case 'listarIncidentesPorResidente':
                handleListarIncidentesPorResidente($db, $input);
                break;
            case 'listarIncidentesAsignados':
                handleListarIncidentesAsignados($db, $input);
                break;
            case 'listarIncidentesAtendidos':
                handleListarIncidentesAtendidos($db, $input);
                break;
            case 'obtenerIncidentePorId':
                handleObtenerIncidentePorId($db, $input);
                break;
            case 'obtenerHistorialIncidente':
                handleObtenerHistorialIncidente($db, $input);
                break;
            
            // Gestión de incidentes
            case 'crearIncidente':
                handleCrearIncidente($db, $input);
                break;
            case 'editarIncidente':
                handleEditarIncidente($db, $input);
                break;
            case 'cambiarTipoIncidente':
                handleCambiarTipoIncidente($db, $input);
                break;
            case 'cancelarIncidente':
                handleCancelarIncidente($db, $input);
                break;
            
            // Asignación y reasignación
            case 'asignarPersonal':
                handleAsignarPersonal($db, $input);
                break;
            case 'reasignarPersonal':
                handleReasignarPersonal($db, $input);
                break;
            case 'solicitarReasignacion':
                handleSolicitarReasignacion($db, $input);
                break;
            
            // Proceso del personal
            case 'iniciarAtencion':
                handleIniciarAtencion($db, $input);
                break;
            case 'actualizarProgresoPersonal':
                handleActualizarProgresoPersonal($db, $input);
                break;
            case 'resolverIncidentePersonal':
                handleResolverIncidentePersonal($db, $input);
                break;
            case 'resolverIncidente':
                handleResolverIncidente($db, $input);
                break;
            
            // Auxiliares
            case 'obtenerPersonalDisponible':
                handleObtenerPersonalDisponible($db, $input);
                break;
            case 'obtenerDepartamentos':
                handleObtenerDepartamentos($db, $input);
                break;
            case 'obtenerResidentes':
                handleObtenerResidentes($db, $input);
                break;
            case 'obtenerAreas':
                handleObtenerAreas($db, $input);
                break;
            case 'obtenerResidentesPorDepartamento':
                handleObtenerResidentesPorDepartamento($db, $input);
                break;
            case 'obtenerDepartamentosPorID':
                handleObtenerDepartamentosPorID($db, $input);
                break;
            
            // Estadísticas
            case 'obtenerEstadisticas':
                handleObtenerEstadisticas($db, $input);
                break;
            case 'contarIncidentesPorEstado':
                handleContarIncidentesPorEstado($db, $input);
                break;
            case 'contarIncidentesPorReasignacion':
                handleContarIncidentesPorReasignacion($db, $input);
                break;
            case 'contarTotalIncidentes':
                handleContarTotalIncidentes($db, $input);
                break;
            case 'contarIncidentesResidentePorEstado':
                handleContarIncidentesResidentePorEstado($db, $input);
                break;
            case 'contarTotalIncidentesResidente':
                handleContarTotalIncidentesResidente($db, $input);
                break;
            case 'contarIncidentesAsignados':
                handleContarIncidentesAsignados($db, $input);
                break;
            case 'contarIncidentesAtendidos':
                handleContarIncidentesAtendidos($db, $input);
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
        case 'listarIncidentes':
            handleListarIncidentes($db, $_GET);
            break;
        case 'listarIncidentesPorEstado':
            handleListarIncidentesPorEstado($db, $_GET);
            break;
        case 'listarIncidentesPorReasignacion':
            handleListarIncidentesPorReasignacion($db, $_GET);
            break;
        case 'listarIncidentesPorResidente':
            handleListarIncidentesPorResidente($db, $_GET);
            break;
        case 'listarIncidentesAsignados':
            handleListarIncidentesAsignados($db, $_GET);
            break;
        case 'listarIncidentesAtendidos':
            handleListarIncidentesAtendidos($db, $_GET);
            break;
        case 'obtenerIncidentePorId':
            handleObtenerIncidentePorId($db, $_GET);
            break;
        case 'obtenerHistorialIncidente':
            handleObtenerHistorialIncidente($db, $_GET);
            break;
        case 'obtenerPersonalDisponible':
            handleObtenerPersonalDisponible($db, $_GET);
            break;
        case 'obtenerDepartamentos':
            handleObtenerDepartamentos($db, $_GET);
            break;
        case 'obtenerResidentes':
            handleObtenerResidentes($db, $_GET);
            break;
        case 'obtenerAreas':
            handleObtenerAreas($db, $_GET);
            break;
        case 'obtenerResidentesPorDepartamento':
            handleObtenerResidentesPorDepartamento($db, $_GET);
            break;
        case 'obtenerDepartamentosPorID':
            handleObtenerDepartamentosPorID($db, $_GET);
            break;
        case 'obtenerEstadisticas':
            handleObtenerEstadisticas($db, $_GET);
            break;
        case 'contarIncidentesPorEstado':
            handleContarIncidentesPorEstado($db, $_GET);
            break;
        case 'contarIncidentesPorReasignacion':
            handleContarIncidentesPorReasignacion($db, $_GET);
            break;
        case 'contarTotalIncidentes':
            handleContarTotalIncidentes($db, $_GET);
            break;
        case 'contarIncidentesResidentePorEstado':
            handleContarIncidentesResidentePorEstado($db, $_GET);
            break;
        case 'contarTotalIncidentesResidente':
            handleContarTotalIncidentesResidente($db, $_GET);
            break;
        case 'contarIncidentesAsignados':
            handleContarIncidentesAsignados($db, $_GET);
            break;
        case 'contarIncidentesAtendidos':
            handleContarIncidentesAtendidos($db, $_GET);
            break;
        default:
            sendResponse(400, ['status' => 400, 'message' => 'Acción GET no válida: ' . $action]);
    }
} else {
    sendResponse(405, ['status' => 405, 'message' => 'Método no permitido']);
}

// =============================================
// ENDPOINTS PARA LISTADO Y CONSULTA
// =============================================

function handleListarIncidentes($db, $data) {
    try {
        $incidenteModelo = new IncidenteModelo($db);
        $incidentes = $incidenteModelo->listarIncidentes();

        sendResponse(200, [
            'status' => 200,
            'message' => 'Incidentes listados exitosamente',
            'data' => $incidentes,
            'total' => count($incidentes)
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al listar incidentes: ' . $e->getMessage()
        ]);
    }
}

function handleListarIncidentesPorEstado($db, $data) {
    try {
        $incidenteModelo = new IncidenteModelo($db);

        $estado = $data['estado'] ?? '';

        if (empty($estado)) {
            sendResponse(400, ['status' => 400, 'message' => 'Estado requerido']);
            return;
        }

        $estados_permitidos = ['pendiente', 'en_proceso', 'resuelto', 'cancelado'];
        if (!in_array($estado, $estados_permitidos)) {
            sendResponse(400, ['status' => 400, 'message' => 'Estado no válido. Debe ser: pendiente, en_proceso, resuelto o cancelado']);
            return;
        }

        $incidentes = $incidenteModelo->listarIncidentesPorEstado($estado);

        sendResponse(200, [
            'status' => 200,
            'message' => 'Incidentes por estado listados exitosamente',
            'data' => $incidentes,
            'estado' => $estado,
            'total' => count($incidentes)
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al listar incidentes por estado: ' . $e->getMessage()
        ]);
    }
}

function handleListarIncidentesPorReasignacion($db, $data) {
    try {
        $incidenteModelo = new IncidenteModelo($db);
        $incidentes = $incidenteModelo->listarIncidentesPorReasignacion();

        sendResponse(200, [
            'status' => 200,
            'message' => 'Incidentes por reasignación listados exitosamente',
            'data' => $incidentes,
            'total' => count($incidentes)
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al listar incidentes por reasignación: ' . $e->getMessage()
        ]);
    }
}

function handleListarIncidentesPorResidente($db, $data) {
    try {
        $incidenteModelo = new IncidenteModelo($db);

        $id_residente = intval($data['id_residente'] ?? 0);

        if ($id_residente <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de residente inválido']);
            return;
        }

        $incidentes = $incidenteModelo->listarIncidentesPorResidente($id_residente);

        sendResponse(200, [
            'status' => 200,
            'message' => 'Incidentes por residente listados exitosamente',
            'data' => $incidentes,
            'id_residente' => $id_residente,
            'total' => count($incidentes)
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al listar incidentes por residente: ' . $e->getMessage()
        ]);
    }
}

function handleListarIncidentesAsignados($db, $data) {
    try {
        $incidenteModelo = new IncidenteModelo($db);

        $id_personal = intval($data['id_personal'] ?? 0);

        if ($id_personal <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de personal inválido']);
            return;
        }

        $incidentes = $incidenteModelo->listarIncidentesAsignados($id_personal);

        sendResponse(200, [
            'status' => 200,
            'message' => 'Incidentes asignados listados exitosamente',
            'data' => $incidentes,
            'id_personal' => $id_personal,
            'total' => count($incidentes)
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al listar incidentes asignados: ' . $e->getMessage()
        ]);
    }
}

function handleListarIncidentesAtendidos($db, $data) {
    try {
        $incidenteModelo = new IncidenteModelo($db);

        $id_personal = intval($data['id_personal'] ?? 0);

        if ($id_personal <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de personal inválido']);
            return;
        }

        $incidentes = $incidenteModelo->listarIncidentesAtendidos($id_personal);

        sendResponse(200, [
            'status' => 200,
            'message' => 'Incidentes atendidos listados exitosamente',
            'data' => $incidentes,
            'id_personal' => $id_personal,
            'total' => count($incidentes)
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al listar incidentes atendidos: ' . $e->getMessage()
        ]);
    }
}

function handleObtenerIncidentePorId($db, $data) {
    try {
        $incidenteModelo = new IncidenteModelo($db);

        $id_incidente = intval($data['id_incidente'] ?? 0);

        if ($id_incidente <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de incidente inválido']);
            return;
        }

        $incidente = $incidenteModelo->obtenerIncidentePorId($id_incidente);

        if (!$incidente) {
            sendResponse(404, ['status' => 404, 'message' => 'Incidente no encontrado']);
            return;
        }

        sendResponse(200, [
            'status' => 200,
            'message' => 'Incidente obtenido exitosamente',
            'data' => $incidente
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al obtener incidente: ' . $e->getMessage()
        ]);
    }
}

function handleObtenerHistorialIncidente($db, $data) {
    try {
        $incidenteModelo = new IncidenteModelo($db);

        $id_incidente = intval($data['id_incidente'] ?? 0);

        if ($id_incidente <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de incidente inválido']);
            return;
        }

        $historial = $incidenteModelo->obtenerHistorialIncidente($id_incidente);

        sendResponse(200, [
            'status' => 200,
            'message' => 'Historial de incidente obtenido exitosamente',
            'data' => $historial,
            'id_incidente' => $id_incidente,
            'total' => count($historial)
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al obtener historial de incidente: ' . $e->getMessage()
        ]);
    }
}

// =============================================
// ENDPOINTS PARA GESTIÓN DE INCIDENTES
// =============================================

function handleCrearIncidente($db, $data) {
    try {
        $incidenteModelo = new IncidenteModelo($db);

        $camposRequeridos = ['id_departamento', 'id_residente', 'descripcion'];
        foreach($camposRequeridos as $campo) {
            if(!isset($data[$campo]) || empty(trim($data[$campo]))) {
                sendResponse(400, ['status' => 400, 'message' => "El campo $campo es obligatorio"]);
                return;
            }
        }

        $id_departamento = intval($data['id_departamento']);
        if ($id_departamento <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de departamento inválido']);
            return;
        }

        $datos = [
            'id_departamento' => $id_departamento,
            'id_residente' => intval($data['id_residente']),
            'tipo' => $data['tipo'] ?? 'interno',
            'descripcion' => trim($data['descripcion']),
            'descripcion_detallada' => !empty($data['descripcion_detallada']) ? trim($data['descripcion_detallada']) : null,
            'id_area' => !empty($data['id_area']) ? intval($data['id_area']) : null
        ];

        // Validar tipo
        if (!in_array($datos['tipo'], ['interno', 'externo'])) {
            sendResponse(400, ['status' => 400, 'message' => 'Tipo inválido. Debe ser: interno o externo']);
            return;
        }

        $id_incidente = $incidenteModelo->registrarIncidente($datos);

        if($id_incidente !== false && $id_incidente > 0){
            sendResponse(201, [
                'status' => 201,
                'message' => 'Incidente registrado exitosamente',
                'data' => ['id_incidente' => $id_incidente]
            ]);
        } else {
            sendResponse(500, [
                'status' => 500,
                'message' => 'Error al registrar incidente'
            ]);
        }
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error en base de datos: ' . $e->getMessage()
        ]);
    }
}

function handleEditarIncidente($db, $data) {
    try {
        $incidenteModelo = new IncidenteModelo($db);

        $camposRequeridos = ['id_incidente', 'descripcion'];
        foreach($camposRequeridos as $campo) {
            if(!isset($data[$campo]) || empty(trim($data[$campo]))) {
                sendResponse(400, ['status' => 400, 'message' => "El campo $campo es obligatorio"]);
                return;
            }
        }

        $datos = [
            'id_incidente' => intval($data['id_incidente']),
            'descripcion' => trim($data['descripcion']),
            'descripcion_detallada' => !empty($data['descripcion_detallada']) ? trim($data['descripcion_detallada']) : null
        ];

        $resultado = $incidenteModelo->editarIncidente($datos);

        if($resultado === true){
            sendResponse(200, [
                'status' => 200,
                'message' => 'Incidente actualizado exitosamente'
            ]);
        } else {
            sendResponse(500, [
                'status' => 500,
                'message' => 'Error al actualizar incidente'
            ]);
        }
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error en base de datos: ' . $e->getMessage()
        ]);
    }
}

function handleCambiarTipoIncidente($db, $data) {
    try {
        $incidenteModelo = new IncidenteModelo($db);

        $camposRequeridos = ['id_incidente', 'tipo'];
        foreach($camposRequeridos as $campo) {
            if(!isset($data[$campo]) || empty(trim($data[$campo]))) {
                sendResponse(400, ['status' => 400, 'message' => "El campo $campo es obligatorio"]);
                return;
            }
        }

        $tipo = trim($data['tipo']);
        if (!in_array($tipo, ['interno', 'externo'])) {
            sendResponse(400, ['status' => 400, 'message' => 'Tipo inválido. Debe ser: interno o externo']);
            return;
        }

        $datos = [
            'id_incidente' => intval($data['id_incidente']),
            'tipo' => $tipo
        ];

        $resultado = $incidenteModelo->cambiarTipoIncidente($datos);

        if($resultado === true){
            sendResponse(200, [
                'status' => 200,
                'message' => 'Tipo de incidente actualizado exitosamente'
            ]);
        } else {
            sendResponse(500, [
                'status' => 500,
                'message' => 'Error al cambiar tipo de incidente'
            ]);
        }
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error en base de datos: ' . $e->getMessage()
        ]);
    }
}

function handleCancelarIncidente($db, $data) {
    try {
        $incidenteModelo = new IncidenteModelo($db);

        $camposRequeridos = ['id_incidente', 'motivo', 'id_persona'];
        foreach($camposRequeridos as $campo) {
            if(!isset($data[$campo]) || empty(trim($data[$campo]))) {
                sendResponse(400, ['status' => 400, 'message' => "El campo $campo es obligatorio"]);
                return;
            }
        }

        $datos = [
            'id_persona' => intval($data['id_persona']),
            'id_incidente' => intval($data['id_incidente']),
            'motivo' => trim($data['motivo'])
        ];

        $resultado = $incidenteModelo->cancelarIncidente($datos);

        if($resultado === true){
            sendResponse(200, [
                'status' => 200,
                'message' => 'Incidente cancelado exitosamente'
            ]);
        } else {
            sendResponse(500, [
                'status' => 500,
                'message' => 'Error al cancelar incidente'
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
// ENDPOINTS PARA ASIGNACIÓN Y REASIGNACIÓN
// =============================================

function handleAsignarPersonal($db, $data) {
    try {
        $incidenteModelo = new IncidenteModelo($db);

        $camposRequeridos = ['id_incidente', 'id_personal'];
        foreach($camposRequeridos as $campo) {
            if(!isset($data[$campo]) || empty(trim($data[$campo]))) {
                sendResponse(400, ['status' => 400, 'message' => "El campo $campo es obligatorio"]);
                return;
            }
        }

        $datos = [
            'id_incidente' => intval($data['id_incidente']),
            'id_personal' => intval($data['id_personal']),
            'observaciones' => !empty($data['observaciones']) ? trim($data['observaciones']) : null
        ];

        $resultado = $incidenteModelo->asignarPersonal($datos);

        if($resultado === true){
            sendResponse(200, [
                'status' => 200,
                'message' => 'Personal asignado exitosamente'
            ]);
        } else {
            sendResponse(500, [
                'status' => 500,
                'message' => 'Error al asignar personal'
            ]);
        }
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error en base de datos: ' . $e->getMessage()
        ]);
    }
}

function handleReasignarPersonal($db, $data) {
    try {
        $incidenteModelo = new IncidenteModelo($db);

        $camposRequeridos = ['id_incidente', 'id_nuevo_personal'];
        foreach($camposRequeridos as $campo) {
            if(!isset($data[$campo]) || empty(trim($data[$campo]))) {
                sendResponse(400, ['status' => 400, 'message' => "El campo $campo es obligatorio"]);
                return;
            }
        }

        $datos = [
            'id_incidente' => intval($data['id_incidente']),
            'id_nuevo_personal' => intval($data['id_nuevo_personal']),
            'observaciones' => !empty($data['observaciones']) ? trim($data['observaciones']) : null
        ];

        $resultado = $incidenteModelo->reasignarPersonal($datos);

        if($resultado === true){
            sendResponse(200, [
                'status' => 200,
                'message' => 'Personal reasignado exitosamente'
            ]);
        } else {
            sendResponse(500, [
                'status' => 500,
                'message' => 'Error al reasignar personal'
            ]);
        }
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error en base de datos: ' . $e->getMessage()
        ]);
    }
}

function handleSolicitarReasignacion($db, $data) {
    try {
        $incidenteModelo = new IncidenteModelo($db);

        $camposRequeridos = ['id_incidente', 'id_personal', 'comentario_reasignacion'];
        foreach($camposRequeridos as $campo) {
            if(!isset($data[$campo]) || empty(trim($data[$campo]))) {
                sendResponse(400, ['status' => 400, 'message' => "El campo $campo es obligatorio"]);
                return;
            }
        }

        $id_incidente = intval($data['id_incidente']);
        $id_personal = intval($data['id_personal']);
        $observaciones = !empty($data['observaciones']) ? trim($data['observaciones']) : '';
        $comentario_reasignacion = trim($data['comentario_reasignacion']);

        $resultado = $incidenteModelo->solicitarReasignacion($id_incidente, $id_personal, $observaciones, $comentario_reasignacion);

        if($resultado === true){
            sendResponse(200, [
                'status' => 200,
                'message' => 'Solicitud de reasignación enviada exitosamente'
            ]);
        } else {
            sendResponse(500, [
                'status' => 500,
                'message' => 'Error al solicitar reasignación'
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
// ENDPOINTS PARA PROCESO DEL PERSONAL
// =============================================

function handleIniciarAtencion($db, $data) {
    try {
        $incidenteModelo = new IncidenteModelo($db);

        $camposRequeridos = ['id_incidente', 'id_personal', 'observaciones'];
        foreach($camposRequeridos as $campo) {
            if(!isset($data[$campo]) || empty(trim($data[$campo]))) {
                sendResponse(400, ['status' => 400, 'message' => "El campo $campo es obligatorio"]);
                return;
            }
        }

        $datos = [
            'id_incidente' => intval($data['id_incidente']),
            'id_personal' => intval($data['id_personal']),
            'observaciones' => trim($data['observaciones'])
        ];

        $resultado = $incidenteModelo->iniciarAtencionIncidente($datos);

        if($resultado === true){
            sendResponse(200, [
                'status' => 200,
                'message' => 'Atención iniciada exitosamente'
            ]);
        } else {
            sendResponse(500, [
                'status' => 500,
                'message' => 'Error al iniciar atención'
            ]);
        }
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error en base de datos: ' . $e->getMessage()
        ]);
    }
}

function handleActualizarProgresoPersonal($db, $data) {
    try {
        $incidenteModelo = new IncidenteModelo($db);

        $camposRequeridos = ['id_incidente', 'id_personal', 'observaciones'];
        foreach($camposRequeridos as $campo) {
            if(!isset($data[$campo]) || empty(trim($data[$campo]))) {
                sendResponse(400, ['status' => 400, 'message' => "El campo $campo es obligatorio"]);
                return;
            }
        }

        $datos = [
            'id_incidente' => intval($data['id_incidente']),
            'id_personal' => intval($data['id_personal']),
            'observaciones' => trim($data['observaciones'])
        ];

        $resultado = $incidenteModelo->actualizarProgresoIncidente($datos);

        if($resultado === true){
            sendResponse(200, [
                'status' => 200,
                'message' => 'Progreso actualizado exitosamente'
            ]);
        } else {
            sendResponse(500, [
                'status' => 500,
                'message' => 'Error al actualizar progreso'
            ]);
        }
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error en base de datos: ' . $e->getMessage()
        ]);
    }
}

function handleResolverIncidentePersonal($db, $data) {
    try {
        $incidenteModelo = new IncidenteModelo($db);

        $camposRequeridos = ['id_incidente', 'id_personal', 'observaciones_finales'];
        foreach($camposRequeridos as $campo) {
            if(!isset($data[$campo]) || empty(trim($data[$campo]))) {
                sendResponse(400, ['status' => 400, 'message' => "El campo $campo es obligatorio"]);
                return;
            }
        }

        $datos = [
            'id_incidente' => intval($data['id_incidente']),
            'id_personal' => intval($data['id_personal']),
            'observaciones_finales' => trim($data['observaciones_finales']),
            'costo_externo' => !empty($data['costo_externo']) ? floatval($data['costo_externo']) : 0
        ];

        $resultado = $incidenteModelo->resolverIncidentePersonal($datos);

        if($resultado === true){
            sendResponse(200, [
                'status' => 200,
                'message' => 'Incidente resuelto exitosamente'
            ]);
        } else {
            sendResponse(500, [
                'status' => 500,
                'message' => 'Error al resolver incidente'
            ]);
        }
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error en base de datos: ' . $e->getMessage()
        ]);
    }
}

function handleResolverIncidente($db, $data) {
    try {
        $incidenteModelo = new IncidenteModelo($db);

        $camposRequeridos = ['id_incidente', 'observaciones'];
        foreach($camposRequeridos as $campo) {
            if(!isset($data[$campo]) || empty(trim($data[$campo]))) {
                sendResponse(400, ['status' => 400, 'message' => "El campo $campo es obligatorio"]);
                return;
            }
        }

        $datos = [
            'id_incidente' => intval($data['id_incidente']),
            'observaciones' => trim($data['observaciones']),
            'costo_externo' => !empty($data['costo_externo']) ? floatval($data['costo_externo']) : 0
        ];

        $resultado = $incidenteModelo->resolverIncidente($datos);

        if($resultado === true){
            sendResponse(200, [
                'status' => 200,
                'message' => 'Incidente resuelto exitosamente'
            ]);
        } else {
            sendResponse(500, [
                'status' => 500,
                'message' => 'Error al resolver incidente'
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

function handleObtenerPersonalDisponible($db, $data) {
    try {
        $incidenteModelo = new IncidenteModelo($db);
        $personal = $incidenteModelo->obtenerPersonalDisponible();

        sendResponse(200, [
            'status' => 200,
            'message' => 'Personal disponible listado exitosamente',
            'data' => $personal,
            'total' => count($personal)
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al listar personal disponible: ' . $e->getMessage()
        ]);
    }
}

function handleObtenerDepartamentos($db, $data) {
    try {
        $incidenteModelo = new IncidenteModelo($db);
        $departamentos = $incidenteModelo->obtenerDepartamentos();

        sendResponse(200, [
            'status' => 200,
            'message' => 'Departamentos listados exitosamente',
            'data' => $departamentos,
            'total' => count($departamentos)
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al listar departamentos: ' . $e->getMessage()
        ]);
    }
}

function handleObtenerResidentes($db, $data) {
    try {
        $incidenteModelo = new IncidenteModelo($db);
        $residentes = $incidenteModelo->obtenerResidentes();

        sendResponse(200, [
            'status' => 200,
            'message' => 'Residentes listados exitosamente',
            'data' => $residentes,
            'total' => count($residentes)
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al listar residentes: ' . $e->getMessage()
        ]);
    }
}

function handleObtenerAreas($db, $data) {
    try {
        $incidenteModelo = new IncidenteModelo($db);
        $areas = $incidenteModelo->obtenerAreas();

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

function handleObtenerResidentesPorDepartamento($db, $data) {
    try {
        $incidenteModelo = new IncidenteModelo($db);

        $id_departamento = intval($data['id_departamento'] ?? 0);

        if ($id_departamento <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de departamento inválido']);
            return;
        }

        $residentes = $incidenteModelo->obtenerResidentesPorDepartamento($id_departamento);

        sendResponse(200, [
            'status' => 200,
            'message' => 'Residentes por departamento listados exitosamente',
            'data' => $residentes,
            'id_departamento' => $id_departamento,
            'total' => count($residentes)
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al listar residentes por departamento: ' . $e->getMessage()
        ]);
    }
}

function handleObtenerDepartamentosPorID($db, $data) {
    try {
        $incidenteModelo = new IncidenteModelo($db);

        $id_persona = intval($data['id_persona'] ?? 0);

        if ($id_persona <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de persona inválido']);
            return;
        }

        $departamentos = $incidenteModelo->obtenerDepartamentosPorID($id_persona);

        sendResponse(200, [
            'status' => 200,
            'message' => 'Departamentos por persona listados exitosamente',
            'data' => $departamentos,
            'id_persona' => $id_persona,
            'total' => count($departamentos)
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al listar departamentos por persona: ' . $e->getMessage()
        ]);
    }
}

// =============================================
// ENDPOINTS PARA ESTADÍSTICAS
// =============================================

function handleObtenerEstadisticas($db, $data) {
    try {
        $incidenteModelo = new IncidenteModelo($db);

        // Obtener todas las estadísticas generales
        $estadisticas = [
            'pendientes' => $incidenteModelo->contarIncidentesPorEstado('pendiente'),
            'en_proceso' => $incidenteModelo->contarIncidentesPorEstado('en_proceso'),
            'resueltos' => $incidenteModelo->contarIncidentesPorEstado('resuelto'),
            'cancelados' => $incidenteModelo->contarIncidentesPorEstado('cancelado'),
            'por_reasignacion' => $incidenteModelo->contarIncidentesPorReasignacion(),
            'total' => $incidenteModelo->contarTotalIncidentes()
        ];

        // Obtener personal disponible si se solicita
        $incluir_personal = isset($data['incluir_personal']) && $data['incluir_personal'] === true;
        $personalDisponible = null;
        if ($incluir_personal) {
            $personalDisponible = $incidenteModelo->obtenerPersonalDisponible();
        }

        sendResponse(200, [
            'status' => 200,
            'message' => 'Estadísticas obtenidas exitosamente',
            'data' => $estadisticas,
            'personal_disponible' => $personalDisponible
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
        ]);
    }
}

function handleContarIncidentesPorEstado($db, $data) {
    try {
        $incidenteModelo = new IncidenteModelo($db);

        $estado = $data['estado'] ?? '';

        if (empty($estado)) {
            sendResponse(400, ['status' => 400, 'message' => 'Estado requerido']);
            return;
        }

        $estados_permitidos = ['pendiente', 'en_proceso', 'resuelto', 'cancelado'];
        if (!in_array($estado, $estados_permitidos)) {
            sendResponse(400, ['status' => 400, 'message' => 'Estado no válido']);
            return;
        }

        $total = $incidenteModelo->contarIncidentesPorEstado($estado);

        sendResponse(200, [
            'status' => 200,
            'message' => 'Conteo de incidentes por estado completado',
            'data' => [
                'estado' => $estado,
                'total' => $total
            ]
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al contar incidentes por estado: ' . $e->getMessage()
        ]);
    }
}

function handleContarIncidentesPorReasignacion($db, $data) {
    try {
        $incidenteModelo = new IncidenteModelo($db);
        $total = $incidenteModelo->contarIncidentesPorReasignacion();

        sendResponse(200, [
            'status' => 200,
            'message' => 'Conteo de incidentes por reasignación completado',
            'data' => [
                'total' => $total
            ]
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al contar incidentes por reasignación: ' . $e->getMessage()
        ]);
    }
}

function handleContarTotalIncidentes($db, $data) {
    try {
        $incidenteModelo = new IncidenteModelo($db);
        $total = $incidenteModelo->contarTotalIncidentes();

        sendResponse(200, [
            'status' => 200,
            'message' => 'Conteo total de incidentes completado',
            'data' => [
                'total' => $total
            ]
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al contar total de incidentes: ' . $e->getMessage()
        ]);
    }
}

function handleContarIncidentesResidentePorEstado($db, $data) {
    try {
        $incidenteModelo = new IncidenteModelo($db);

        $id_residente = intval($data['id_residente'] ?? 0);
        $estado = $data['estado'] ?? '';

        if ($id_residente <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de residente inválido']);
            return;
        }

        if (empty($estado)) {
            sendResponse(400, ['status' => 400, 'message' => 'Estado requerido']);
            return;
        }

        $estados_permitidos = ['pendiente', 'en_proceso', 'resuelto', 'cancelado'];
        if (!in_array($estado, $estados_permitidos)) {
            sendResponse(400, ['status' => 400, 'message' => 'Estado no válido']);
            return;
        }

        $total = $incidenteModelo->contarIncidentesResidentePorEstado($id_residente, $estado);

        sendResponse(200, [
            'status' => 200,
            'message' => 'Conteo de incidentes por residente y estado completado',
            'data' => [
                'id_residente' => $id_residente,
                'estado' => $estado,
                'total' => $total
            ]
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al contar incidentes por residente y estado: ' . $e->getMessage()
        ]);
    }
}

function handleContarTotalIncidentesResidente($db, $data) {
    try {
        $incidenteModelo = new IncidenteModelo($db);

        $id_residente = intval($data['id_residente'] ?? 0);

        if ($id_residente <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de residente inválido']);
            return;
        }

        $total = $incidenteModelo->contarTotalIncidentesResidente($id_residente);

        sendResponse(200, [
            'status' => 200,
            'message' => 'Conteo total de incidentes por residente completado',
            'data' => [
                'id_residente' => $id_residente,
                'total' => $total
            ]
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al contar total de incidentes por residente: ' . $e->getMessage()
        ]);
    }
}

function handleContarIncidentesAsignados($db, $data) {
    try {
        $incidenteModelo = new IncidenteModelo($db);

        $id_personal = intval($data['id_personal'] ?? 0);

        if ($id_personal <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de personal inválido']);
            return;
        }

        $total = $incidenteModelo->contarIncidentesAsignados($id_personal);

        sendResponse(200, [
            'status' => 200,
            'message' => 'Conteo de incidentes asignados completado',
            'data' => [
                'id_personal' => $id_personal,
                'total' => $total
            ]
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al contar incidentes asignados: ' . $e->getMessage()
        ]);
    }
}

function handleContarIncidentesAtendidos($db, $data) {
    try {
        $incidenteModelo = new IncidenteModelo($db);

        $id_personal = intval($data['id_personal'] ?? 0);

        if ($id_personal <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de personal inválido']);
            return;
        }

        $total = $incidenteModelo->contarIncidentesAtendidos($id_personal);

        sendResponse(200, [
            'status' => 200,
            'message' => 'Conteo de incidentes atendidos completado',
            'data' => [
                'id_personal' => $id_personal,
                'total' => $total
            ]
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al contar incidentes atendidos: ' . $e->getMessage()
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


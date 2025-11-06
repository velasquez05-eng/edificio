<?php
// factura.php - API completa para gestión de facturas y conceptos
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Manejar preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

error_log("DEBUG: factura.php accedido - Método: " . $_SERVER['REQUEST_METHOD']);

try {
    require_once '../../config/database.php';
    require_once '../modelo/FacturaModelo.php';

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
            // Gestión de facturas
            case 'listarFacturas':
                handleListarFacturas($db, $input);
                break;
            case 'obtenerFacturaCompleta':
                handleObtenerFacturaCompleta($db, $input);
                break;
            case 'generarFacturas':
                handleGenerarFacturas($db, $input);
                break;
            case 'pagarFactura':
                handlePagarFactura($db, $input);
                break;
            
            // Facturas del usuario
            case 'obtenerMisFacturas':
                handleObtenerMisFacturas($db, $input);
                break;
            
            // Historial de pagos
            case 'obtenerMiHistorialPagos':
                handleObtenerMiHistorialPagos($db, $input);
                break;
            case 'obtenerHistorialPagosCompleto':
                handleObtenerHistorialPagosCompleto($db, $input);
                break;
            case 'obtenerEstadisticasMisPagos':
                handleObtenerEstadisticasMisPagos($db, $input);
                break;
            case 'obtenerEstadisticasPagosCompletas':
                handleObtenerEstadisticasPagosCompletas($db, $input);
                break;
            
            // Conceptos
            case 'obtenerMisConceptos':
                handleObtenerMisConceptos($db, $input);
                break;
            case 'obtenerConceptosCompletos':
                handleObtenerConceptosCompletos($db, $input);
                break;
            case 'obtenerEstadisticasMisConceptos':
                handleObtenerEstadisticasMisConceptos($db, $input);
                break;
            case 'obtenerEstadisticasConceptosCompletos':
                handleObtenerEstadisticasConceptosCompletos($db, $input);
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
        case 'listarFacturas':
            handleListarFacturas($db, $_GET);
            break;
        case 'obtenerFacturaCompleta':
            handleObtenerFacturaCompleta($db, $_GET);
            break;
        case 'obtenerMisFacturas':
            handleObtenerMisFacturas($db, $_GET);
            break;
        case 'obtenerMiHistorialPagos':
            handleObtenerMiHistorialPagos($db, $_GET);
            break;
        case 'obtenerHistorialPagosCompleto':
            handleObtenerHistorialPagosCompleto($db, $_GET);
            break;
        case 'obtenerEstadisticasMisPagos':
            handleObtenerEstadisticasMisPagos($db, $_GET);
            break;
        case 'obtenerEstadisticasPagosCompletas':
            handleObtenerEstadisticasPagosCompletas($db, $_GET);
            break;
        case 'obtenerMisConceptos':
            handleObtenerMisConceptos($db, $_GET);
            break;
        case 'obtenerConceptosCompletos':
            handleObtenerConceptosCompletos($db, $_GET);
            break;
        case 'obtenerEstadisticasMisConceptos':
            handleObtenerEstadisticasMisConceptos($db, $_GET);
            break;
        case 'obtenerEstadisticasConceptosCompletos':
            handleObtenerEstadisticasConceptosCompletos($db, $_GET);
            break;
        default:
            sendResponse(400, ['status' => 400, 'message' => 'Acción GET no válida: ' . $action]);
    }
} else {
    sendResponse(405, ['status' => 405, 'message' => 'Método no permitido']);
}

// =============================================
// ENDPOINTS PARA GESTIÓN DE FACTURAS
// =============================================

function handleListarFacturas($db, $data) {
    try {
        $facturaModelo = new FacturaModelo($db);
        $facturas = $facturaModelo->obtenerTodasLasFacturas();

        if ($facturas === false) {
            $facturas = [];
        }

        sendResponse(200, [
            'status' => 200,
            'message' => 'Facturas listadas exitosamente',
            'data' => $facturas,
            'total' => count($facturas)
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al listar facturas: ' . $e->getMessage()
        ]);
    }
}

function handleObtenerFacturaCompleta($db, $data) {
    try {
        $facturaModelo = new FacturaModelo($db);

        $id_factura = intval($data['id_factura'] ?? 0);

        if ($id_factura <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de factura inválido']);
            return;
        }

        $facturaCompleta = $facturaModelo->obtenerFacturaCompleta($id_factura);

        if (!$facturaCompleta) {
            sendResponse(404, ['status' => 404, 'message' => 'Factura no encontrada']);
            return;
        }

        sendResponse(200, [
            'status' => 200,
            'message' => 'Factura completa obtenida exitosamente',
            'data' => $facturaCompleta
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al obtener factura completa: ' . $e->getMessage()
        ]);
    }
}

function handleGenerarFacturas($db, $data) {
    try {
        $facturaModelo = new FacturaModelo($db);

        $mes = $data['mes_facturacion'] ?? '';

        if (empty($mes)) {
            sendResponse(400, ['status' => 400, 'message' => 'El campo mes_facturacion es obligatorio']);
            return;
        }

        if (!preg_match('/^\d{4}-\d{2}$/', $mes)) {
            sendResponse(400, ['status' => 400, 'message' => 'Formato de mes inválido. Use YYYY-MM']);
            return;
        }

        $resultado = $facturaModelo->generarFacturasMes($mes);

        if($resultado === true){
            sendResponse(201, [
                'status' => 201,
                'message' => 'Facturas generadas exitosamente para el mes ' . $mes,
                'mes' => $mes
            ]);
        } else {
            sendResponse(500, [
                'status' => 500,
                'message' => 'Error al generar las facturas'
            ]);
        }
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error en base de datos: ' . $e->getMessage()
        ]);
    }
}

function handlePagarFactura($db, $data) {
    try {
        $facturaModelo = new FacturaModelo($db);

        $id_factura = intval($data['id_factura'] ?? 0);
        $id_persona = !empty($data['id_persona']) ? intval($data['id_persona']) : null;

        if ($id_factura <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de factura inválido']);
            return;
        }

        // Obtener monto de la factura
        $facturaCompleta = $facturaModelo->obtenerFacturaCompleta($id_factura);
        if (!$facturaCompleta) {
            sendResponse(404, ['status' => 404, 'message' => 'Factura no encontrada']);
            return;
        }

        $monto_total = $facturaCompleta['factura']['monto_total'];

        // Procesar el pago
        $resultado = $facturaModelo->procesarPago($id_factura, $monto_total, $id_persona);

        if($resultado['success'] === true){
            sendResponse(200, [
                'status' => 200,
                'message' => $resultado['message'],
                'data' => [
                    'id_factura' => $resultado['id_factura'],
                    'id_persona' => $resultado['id_persona'],
                    'monto_pagado' => $resultado['monto_pagado']
                ]
            ]);
        } else {
            sendResponse(400, [
                'status' => 400,
                'message' => $resultado['message']
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
// ENDPOINTS PARA FACTURAS DEL USUARIO
// =============================================

function handleObtenerMisFacturas($db, $data) {
    try {
        $facturaModelo = new FacturaModelo($db);

        $id_persona = intval($data['id_persona'] ?? 0);

        if ($id_persona <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de persona inválido']);
            return;
        }

        $facturas = $facturaModelo->obtenerMisFacturas($id_persona);

        if ($facturas === false) {
            $facturas = [];
        }

        sendResponse(200, [
            'status' => 200,
            'message' => 'Facturas del usuario listadas exitosamente',
            'data' => $facturas,
            'id_persona' => $id_persona,
            'total' => count($facturas)
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al listar facturas del usuario: ' . $e->getMessage()
        ]);
    }
}

// =============================================
// ENDPOINTS PARA HISTORIAL DE PAGOS
// =============================================

function handleObtenerMiHistorialPagos($db, $data) {
    try {
        $facturaModelo = new FacturaModelo($db);

        $id_persona = intval($data['id_persona'] ?? 0);

        if ($id_persona <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de persona inválido']);
            return;
        }

        $pagos = $facturaModelo->obtenerMiHistorialPagos($id_persona);

        if ($pagos === false) {
            $pagos = [];
        }

        sendResponse(200, [
            'status' => 200,
            'message' => 'Historial de pagos del usuario obtenido exitosamente',
            'data' => $pagos,
            'id_persona' => $id_persona,
            'total' => count($pagos)
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al obtener historial de pagos del usuario: ' . $e->getMessage()
        ]);
    }
}

function handleObtenerHistorialPagosCompleto($db, $data) {
    try {
        $facturaModelo = new FacturaModelo($db);
        $pagos = $facturaModelo->obtenerHistorialPagosCompleto();

        if ($pagos === false) {
            $pagos = [];
        }

        sendResponse(200, [
            'status' => 200,
            'message' => 'Historial completo de pagos obtenido exitosamente',
            'data' => $pagos,
            'total' => count($pagos)
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al obtener historial completo de pagos: ' . $e->getMessage()
        ]);
    }
}

function handleObtenerEstadisticasMisPagos($db, $data) {
    try {
        $facturaModelo = new FacturaModelo($db);

        $id_persona = intval($data['id_persona'] ?? 0);

        if ($id_persona <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de persona inválido']);
            return;
        }

        $estadisticas = $facturaModelo->obtenerEstadisticasMisPagos($id_persona);

        sendResponse(200, [
            'status' => 200,
            'message' => 'Estadísticas de pagos del usuario obtenidas exitosamente',
            'data' => $estadisticas,
            'id_persona' => $id_persona
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al obtener estadísticas de pagos del usuario: ' . $e->getMessage()
        ]);
    }
}

function handleObtenerEstadisticasPagosCompletas($db, $data) {
    try {
        $facturaModelo = new FacturaModelo($db);
        $estadisticas = $facturaModelo->obtenerEstadisticasPagosCompletas();

        sendResponse(200, [
            'status' => 200,
            'message' => 'Estadísticas completas de pagos obtenidas exitosamente',
            'data' => $estadisticas
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al obtener estadísticas completas de pagos: ' . $e->getMessage()
        ]);
    }
}

// =============================================
// ENDPOINTS PARA CONCEPTOS
// =============================================

function handleObtenerMisConceptos($db, $data) {
    try {
        $facturaModelo = new FacturaModelo($db);

        $id_persona = intval($data['id_persona'] ?? 0);

        if ($id_persona <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de persona inválido']);
            return;
        }

        $conceptos = $facturaModelo->obtenerMisConceptos($id_persona);

        if ($conceptos === false) {
            $conceptos = [];
        }

        sendResponse(200, [
            'status' => 200,
            'message' => 'Conceptos del usuario listados exitosamente',
            'data' => $conceptos,
            'id_persona' => $id_persona,
            'total' => count($conceptos)
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al listar conceptos del usuario: ' . $e->getMessage()
        ]);
    }
}

function handleObtenerConceptosCompletos($db, $data) {
    try {
        $facturaModelo = new FacturaModelo($db);
        $conceptos = $facturaModelo->obtenerConceptosCompletos();

        if ($conceptos === false) {
            $conceptos = [];
        }

        sendResponse(200, [
            'status' => 200,
            'message' => 'Conceptos completos listados exitosamente',
            'data' => $conceptos,
            'total' => count($conceptos)
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al listar conceptos completos: ' . $e->getMessage()
        ]);
    }
}

function handleObtenerEstadisticasMisConceptos($db, $data) {
    try {
        $facturaModelo = new FacturaModelo($db);

        $id_persona = intval($data['id_persona'] ?? 0);

        if ($id_persona <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de persona inválido']);
            return;
        }

        $estadisticas = $facturaModelo->obtenerEstadisticasMisConceptos($id_persona);

        sendResponse(200, [
            'status' => 200,
            'message' => 'Estadísticas de conceptos del usuario obtenidas exitosamente',
            'data' => $estadisticas,
            'id_persona' => $id_persona
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al obtener estadísticas de conceptos del usuario: ' . $e->getMessage()
        ]);
    }
}

function handleObtenerEstadisticasConceptosCompletos($db, $data) {
    try {
        $facturaModelo = new FacturaModelo($db);
        $estadisticas = $facturaModelo->obtenerEstadisticasConceptosCompletos();

        sendResponse(200, [
            'status' => 200,
            'message' => 'Estadísticas completas de conceptos obtenidas exitosamente',
            'data' => $estadisticas
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al obtener estadísticas completas de conceptos: ' . $e->getMessage()
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


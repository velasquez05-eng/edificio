    <?php
    // planilla.php - API completa para gestión de planillas de pago
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');

    // Manejar preflight requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        http_response_code(200);
        exit();
    }

    error_log("DEBUG: planilla.php accedido - Método: " . $_SERVER['REQUEST_METHOD']);

    try {
        require_once '../../config/database.php';
        require_once '../modelo/PlanillaModelo.php';
        require_once '../modelo/PersonaModelo.php';

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
                // Generación de planillas
                case 'generarPlanillaCompleta':
                    handleGenerarPlanillaCompleta($db, $input);
                    break;
                case 'generarPlanillaPersonalizada':
                    handleGenerarPlanillaPersonalizada($db, $input);
                    break;
                case 'generarPlanillaMultiple':
                    handleGenerarPlanillaMultiple($db, $input);
                    break;
                
                // Consultas
                case 'listarPlanillasCompleto':
                    handleListarPlanillasCompleto($db, $input);
                    break;
                case 'listarMiPlanilla':
                    handleListarMiPlanilla($db, $input);
                    break;
                case 'obtenerPlanillaPorId':
                    handleObtenerPlanillaPorId($db, $input);
                    break;
                case 'obtenerDetallePlanilla':
                    handleObtenerDetallePlanilla($db, $input);
                    break;
                
                // Estadísticas y reportes
                case 'obtenerEstadisticas':
                    handleObtenerEstadisticas($db, $input);
                    break;
                case 'obtenerResumenAnual':
                    handleObtenerResumenAnual($db, $input);
                    break;
                
                // Auxiliares
                case 'obtenerEmpleadosActivos':
                    handleObtenerEmpleadosActivos($db, $input);
                    break;
                case 'verificarPlanillaExistente':
                    handleVerificarPlanillaExistente($db, $input);
                    break;
                case 'obtenerMetodosPago':
                    handleObtenerMetodosPago($db, $input);
                    break;
                
                // Gestión
                case 'actualizarEstadoPago':
                    handleActualizarEstadoPago($db, $input);
                    break;
                case 'eliminarPlanillaPeriodo':
                    handleEliminarPlanillaPeriodo($db, $input);
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
            case 'listarPlanillasCompleto':
                handleListarPlanillasCompleto($db, $_GET);
                break;
            case 'listarMiPlanilla':
                handleListarMiPlanilla($db, $_GET);
                break;
            case 'obtenerPlanillaPorId':
                handleObtenerPlanillaPorId($db, $_GET);
                break;
            case 'obtenerDetallePlanilla':
                handleObtenerDetallePlanilla($db, $_GET);
                break;
            case 'obtenerEstadisticas':
                handleObtenerEstadisticas($db, $_GET);
                break;
            case 'obtenerResumenAnual':
                handleObtenerResumenAnual($db, $_GET);
                break;
            case 'obtenerEmpleadosActivos':
                handleObtenerEmpleadosActivos($db, $_GET);
                break;
            case 'verificarPlanillaExistente':
                handleVerificarPlanillaExistente($db, $_GET);
                break;
            case 'obtenerMetodosPago':
                handleObtenerMetodosPago($db, $_GET);
                break;
            default:
                sendResponse(400, ['status' => 400, 'message' => 'Acción GET no válida: ' . $action]);
        }
    } else {
        sendResponse(405, ['status' => 405, 'message' => 'Método no permitido']);
    }

    // =============================================
    // ENDPOINTS PARA GENERACIÓN DE PLANILLAS
    // =============================================

    function handleGenerarPlanillaCompleta($db, $data) {
        try {
            $planillaModelo = new PlanillaModelo($db);

            $camposRequeridos = ['mes', 'anio', 'metodo_pago'];
            foreach($camposRequeridos as $campo) {
                if(!isset($data[$campo]) || empty(trim($data[$campo]))) {
                    sendResponse(400, ['status' => 400, 'message' => "El campo $campo es obligatorio"]);
                    return;
                }
            }

            $mes = intval($data['mes']);
            $anio = intval($data['anio']);
            $metodo_pago = trim($data['metodo_pago']);
            $forzar = isset($data['forzar']) ? (bool)$data['forzar'] : false;

            // Validar rangos
            if ($mes < 1 || $mes > 12) {
                sendResponse(400, ['status' => 400, 'message' => 'El mes debe estar entre 1 y 12']);
                return;
            }

            if ($anio < 2020 || $anio > 2030) {
                sendResponse(400, ['status' => 400, 'message' => 'El año debe estar entre 2020 y 2030']);
                return;
            }

            // Validar método de pago
            $metodos_validos = ['transferencia', 'qr', 'efectivo', 'cheque'];
            if (!in_array($metodo_pago, $metodos_validos)) {
                sendResponse(400, ['status' => 400, 'message' => 'Método de pago no válido']);
                return;
            }

            $resultado = $planillaModelo->generarPlanillaCompleta($mes, $anio, $metodo_pago, $forzar);

            if($resultado !== false){
                sendResponse(201, [
                    'status' => 201,
                    'message' => 'Planilla completa generada exitosamente',
                    'data' => $resultado,
                    'periodo' => sprintf("%04d-%02d", $anio, $mes)
                ]);
            } else {
                sendResponse(500, [
                    'status' => 500,
                    'message' => 'Error al generar la planilla completa'
                ]);
            }
        } catch (Exception $e) {
            sendResponse(500, [
                'status' => 500,
                'message' => 'Error en base de datos: ' . $e->getMessage()
            ]);
        }
    }

    function handleGenerarPlanillaPersonalizada($db, $data) {
        try {
            $planillaModelo = new PlanillaModelo($db);
            $personaModelo = new PersonaModelo($db);

            $camposRequeridos = ['id_persona', 'mes', 'anio', 'dias_descuento', 'metodo_pago'];
            foreach($camposRequeridos as $campo) {
                if(!isset($data[$campo]) || empty(trim($data[$campo]))) {
                    sendResponse(400, ['status' => 400, 'message' => "El campo $campo es obligatorio"]);
                    return;
                }
            }

            $id_persona = intval($data['id_persona']);
            $mes = intval($data['mes']);
            $anio = intval($data['anio']);
            $dias_descuento = floatval($data['dias_descuento']);
            $metodo_pago = trim($data['metodo_pago']);
            $forzar = isset($data['forzar']) ? (bool)$data['forzar'] : false;

            // Validar rangos
            if ($mes < 1 || $mes > 12) {
                sendResponse(400, ['status' => 400, 'message' => 'El mes debe estar entre 1 y 12']);
                return;
            }

            if ($anio < 2020 || $anio > 2030) {
                sendResponse(400, ['status' => 400, 'message' => 'El año debe estar entre 2020 y 2030']);
                return;
            }

            if ($dias_descuento < 0 || $dias_descuento > 30) {
                sendResponse(400, ['status' => 400, 'message' => 'Los días de descuento deben estar entre 0 y 30']);
                return;
            }

            // Validar método de pago
            $metodos_validos = ['transferencia', 'qr', 'efectivo', 'cheque'];
            if (!in_array($metodo_pago, $metodos_validos)) {
                sendResponse(400, ['status' => 400, 'message' => 'Método de pago no válido']);
                return;
            }

            // Verificar que la persona existe
            $persona = $personaModelo->obtenerPersonaPorId($id_persona);
            if (!$persona) {
                sendResponse(404, ['status' => 404, 'message' => 'La persona especificada no existe']);
                return;
            }

            $resultado = $planillaModelo->generarPlanillaPersonalizada($id_persona, $mes, $anio, $dias_descuento, $metodo_pago, $forzar);

            if($resultado !== false){
                sendResponse(201, [
                    'status' => 201,
                    'message' => 'Planilla personalizada generada exitosamente',
                    'data' => $resultado,
                    'id_persona' => $id_persona,
                    'periodo' => sprintf("%04d-%02d", $anio, $mes)
                ]);
            } else {
                sendResponse(500, [
                    'status' => 500,
                    'message' => 'Error al generar la planilla personalizada'
                ]);
            }
        } catch (Exception $e) {
            sendResponse(500, [
                'status' => 500,
                'message' => 'Error en base de datos: ' . $e->getMessage()
            ]);
        }
    }

    function handleGenerarPlanillaMultiple($db, $data) {
        try {
            $planillaModelo = new PlanillaModelo($db);

            $camposRequeridos = ['mes', 'anio', 'json_descuentos', 'metodo_pago'];
            foreach($camposRequeridos as $campo) {
                if(!isset($data[$campo]) || empty(trim($data[$campo]))) {
                    sendResponse(400, ['status' => 400, 'message' => "El campo $campo es obligatorio"]);
                    return;
                }
            }

            $mes = intval($data['mes']);
            $anio = intval($data['anio']);
            $json_descuentos = $data['json_descuentos'];
            $metodo_pago = trim($data['metodo_pago']);

            // Validar rangos
            if ($mes < 1 || $mes > 12) {
                sendResponse(400, ['status' => 400, 'message' => 'El mes debe estar entre 1 y 12']);
                return;
            }

            if ($anio < 2020 || $anio > 2030) {
                sendResponse(400, ['status' => 400, 'message' => 'El año debe estar entre 2020 y 2030']);
                return;
            }

            // Validar método de pago
            $metodos_validos = ['transferencia', 'qr', 'efectivo', 'cheque'];
            if (!in_array($metodo_pago, $metodos_validos)) {
                sendResponse(400, ['status' => 400, 'message' => 'Método de pago no válido']);
                return;
            }

            // Validar JSON
            if (is_string($json_descuentos)) {
                $descuentos = json_decode($json_descuentos, true);
            } else {
                $descuentos = $json_descuentos;
            }

            if (json_last_error() !== JSON_ERROR_NONE && is_string($json_descuentos)) {
                sendResponse(400, ['status' => 400, 'message' => 'Formato JSON inválido']);
                return;
            }

            if (empty($descuentos) || !is_array($descuentos)) {
                sendResponse(400, ['status' => 400, 'message' => 'El JSON de descuentos no puede estar vacío y debe ser un array']);
                return;
            }

            $resultado = $planillaModelo->generarPlanillaMultiple($mes, $anio, $descuentos, $metodo_pago);

            if($resultado !== false){
                sendResponse(201, [
                    'status' => 201,
                    'message' => 'Planillas múltiples generadas exitosamente',
                    'data' => $resultado,
                    'total_empleados' => count($descuentos),
                    'periodo' => sprintf("%04d-%02d", $anio, $mes)
                ]);
            } else {
                sendResponse(500, [
                    'status' => 500,
                    'message' => 'Error al generar las planillas múltiples'
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
    // ENDPOINTS PARA CONSULTAS
    // =============================================

    function handleListarPlanillasCompleto($db, $data) {
        try {
            $planillaModelo = new PlanillaModelo($db);

            $mes = !empty($data['mes']) ? intval($data['mes']) : null;
            $anio = !empty($data['anio']) ? intval($data['anio']) : null;

            // Validar si se proporcionan ambos
            if (($mes && !$anio) || (!$mes && $anio)) {
                sendResponse(400, ['status' => 400, 'message' => 'Si se especifica mes o año, ambos deben proporcionarse']);
                return;
            }

            if ($mes && ($mes < 1 || $mes > 12)) {
                sendResponse(400, ['status' => 400, 'message' => 'El mes debe estar entre 1 y 12']);
                return;
            }

            $planillas = $planillaModelo->listarPlanillasCompleto($mes, $anio);

            if ($planillas === false) {
                $planillas = [];
            }

            sendResponse(200, [
                'status' => 200,
                'message' => 'Planillas listadas exitosamente',
                'data' => $planillas,
                'total' => count($planillas),
                'filtros' => [
                    'mes' => $mes,
                    'anio' => $anio
                ]
            ]);
        } catch (Exception $e) {
            sendResponse(500, [
                'status' => 500,
                'message' => 'Error al listar planillas: ' . $e->getMessage()
            ]);
        }
    }

    function handleListarMiPlanilla($db, $data) {
        try {
            $planillaModelo = new PlanillaModelo($db);

            $id_persona = intval($data['id_persona'] ?? 0);

            if ($id_persona <= 0) {
                sendResponse(400, ['status' => 400, 'message' => 'ID de persona inválido']);
                return;
            }

            $mes = !empty($data['mes']) ? intval($data['mes']) : null;
            $anio = !empty($data['anio']) ? intval($data['anio']) : null;

            // Validar si se proporcionan ambos
            if (($mes && !$anio) || (!$mes && $anio)) {
                sendResponse(400, ['status' => 400, 'message' => 'Si se especifica mes o año, ambos deben proporcionarse']);
                return;
            }

            if ($mes && ($mes < 1 || $mes > 12)) {
                sendResponse(400, ['status' => 400, 'message' => 'El mes debe estar entre 1 y 12']);
                return;
            }

            $planillas = $planillaModelo->listarMiPlanilla($id_persona, $mes, $anio);

            if ($planillas === false) {
                $planillas = [];
            }

            sendResponse(200, [
                'status' => 200,
                'message' => 'Planillas del empleado listadas exitosamente',
                'data' => $planillas,
                'id_persona' => $id_persona,
                'total' => count($planillas),
                'filtros' => [
                    'mes' => $mes,
                    'anio' => $anio
                ]
            ]);
        } catch (Exception $e) {
            sendResponse(500, [
                'status' => 500,
                'message' => 'Error al listar planillas del empleado: ' . $e->getMessage()
            ]);
        }
    }

    function handleObtenerPlanillaPorId($db, $data) {
        try {
            $planillaModelo = new PlanillaModelo($db);

            $id_planilla_emp = intval($data['id_planilla_emp'] ?? 0);
            $id_persona = intval($data['id_persona'] ?? 0);

            if ($id_planilla_emp <= 0) {
                sendResponse(400, ['status' => 400, 'message' => 'ID de planilla inválido']);
                return;
            }

            if ($id_persona <= 0) {
                sendResponse(400, ['status' => 400, 'message' => 'ID de persona inválido']);
                return;
            }

            $planilla = $planillaModelo->obtenerPlanillaPorId($id_planilla_emp, $id_persona);

            if (!$planilla) {
                sendResponse(404, ['status' => 404, 'message' => 'Planilla no encontrada']);
                return;
            }

            sendResponse(200, [
                'status' => 200,
                'message' => 'Planilla obtenida exitosamente',
                'data' => $planilla
            ]);
        } catch (Exception $e) {
            sendResponse(500, [
                'status' => 500,
                'message' => 'Error al obtener planilla: ' . $e->getMessage()
            ]);
        }
    }

    function handleObtenerDetallePlanilla($db, $data) {
        try {
            $planillaModelo = new PlanillaModelo($db);

            $id_planilla_emp = intval($data['id_planilla_emp'] ?? 0);

            if ($id_planilla_emp <= 0) {
                sendResponse(400, ['status' => 400, 'message' => 'ID de planilla inválido']);
                return;
            }

            $planilla = $planillaModelo->obtenerDetallePlanilla($id_planilla_emp);

            if (!$planilla) {
                sendResponse(404, ['status' => 404, 'message' => 'Planilla no encontrada']);
                return;
            }

            sendResponse(200, [
                'status' => 200,
                'message' => 'Detalle de planilla obtenido exitosamente',
                'data' => $planilla
            ]);
        } catch (Exception $e) {
            sendResponse(500, [
                'status' => 500,
                'message' => 'Error al obtener detalle de planilla: ' . $e->getMessage()
            ]);
        }
    }

    // =============================================
    // ENDPOINTS PARA ESTADÍSTICAS Y REPORTES
    // =============================================

    function handleObtenerEstadisticas($db, $data) {
        try {
            $planillaModelo = new PlanillaModelo($db);

            $mes = intval($data['mes'] ?? date('n'));
            $anio = intval($data['anio'] ?? date('Y'));

            if ($mes < 1 || $mes > 12) {
                sendResponse(400, ['status' => 400, 'message' => 'El mes debe estar entre 1 y 12']);
                return;
            }

            if ($anio < 2020 || $anio > 2030) {
                sendResponse(400, ['status' => 400, 'message' => 'El año debe estar entre 2020 y 2030']);
                return;
            }

            $estadisticas = $planillaModelo->obtenerEstadisticasPlanillas($mes, $anio);
            $empleados = $planillaModelo->obtenerEmpleadosActivos();
            $existePlanilla = $planillaModelo->verificarPlanillaExistente($mes, $anio);

            sendResponse(200, [
                'status' => 200,
                'message' => 'Estadísticas obtenidas exitosamente',
                'data' => [
                    'estadisticas' => $estadisticas,
                    'empleados' => $empleados,
                    'existe_planilla' => $existePlanilla,
                    'periodo' => sprintf("%04d-%02d", $anio, $mes)
                ]
            ]);
        } catch (Exception $e) {
            sendResponse(500, [
                'status' => 500,
                'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
            ]);
        }
    }

    function handleObtenerResumenAnual($db, $data) {
        try {
            $planillaModelo = new PlanillaModelo($db);

            $anio = intval($data['anio'] ?? date('Y'));
            $id_persona = !empty($data['id_persona']) ? intval($data['id_persona']) : null;

            if ($anio < 2020 || $anio > 2030) {
                sendResponse(400, ['status' => 400, 'message' => 'El año debe estar entre 2020 y 2030']);
                return;
            }

            $resumen = $planillaModelo->obtenerResumenAnual($anio, $id_persona);

            if ($resumen === false) {
                $resumen = [];
            }

            sendResponse(200, [
                'status' => 200,
                'message' => 'Resumen anual obtenido exitosamente',
                'data' => $resumen,
                'anio' => $anio,
                'id_persona' => $id_persona
            ]);
        } catch (Exception $e) {
            sendResponse(500, [
                'status' => 500,
                'message' => 'Error al obtener resumen anual: ' . $e->getMessage()
            ]);
        }
    }

    // =============================================
    // ENDPOINTS AUXILIARES
    // =============================================

    function handleObtenerEmpleadosActivos($db, $data) {
        try {
            $planillaModelo = new PlanillaModelo($db);
            $empleados = $planillaModelo->obtenerEmpleadosActivos();

            if ($empleados === false) {
                $empleados = [];
            }

            sendResponse(200, [
                'status' => 200,
                'message' => 'Empleados activos listados exitosamente',
                'data' => $empleados,
                'total' => count($empleados)
            ]);
        } catch (Exception $e) {
            sendResponse(500, [
                'status' => 500,
                'message' => 'Error al listar empleados activos: ' . $e->getMessage()
            ]);
        }
    }

    function handleVerificarPlanillaExistente($db, $data) {
        try {
            $planillaModelo = new PlanillaModelo($db);

            $mes = intval($data['mes'] ?? 0);
            $anio = intval($data['anio'] ?? 0);

            if ($mes < 1 || $mes > 12) {
                sendResponse(400, ['status' => 400, 'message' => 'El mes debe estar entre 1 y 12']);
                return;
            }

            if ($anio < 2020 || $anio > 2030) {
                sendResponse(400, ['status' => 400, 'message' => 'El año debe estar entre 2020 y 2030']);
                return;
            }

            $existe = $planillaModelo->verificarPlanillaExistente($mes, $anio);

            sendResponse(200, [
                'status' => 200,
                'message' => 'Verificación completada',
                'data' => [
                    'existe' => $existe,
                    'mes' => $mes,
                    'anio' => $anio,
                    'periodo' => sprintf("%04d-%02d", $anio, $mes)
                ]
            ]);
        } catch (Exception $e) {
            sendResponse(500, [
                'status' => 500,
                'message' => 'Error al verificar planilla: ' . $e->getMessage()
            ]);
        }
    }

    function handleObtenerMetodosPago($db, $data) {
        try {
            $planillaModelo = new PlanillaModelo($db);
            $metodos = $planillaModelo->obtenerMetodosPago();

            sendResponse(200, [
                'status' => 200,
                'message' => 'Métodos de pago obtenidos exitosamente',
                'data' => $metodos
            ]);
        } catch (Exception $e) {
            sendResponse(500, [
                'status' => 500,
                'message' => 'Error al obtener métodos de pago: ' . $e->getMessage()
            ]);
        }
    }

    // =============================================
    // ENDPOINTS PARA GESTIÓN
    // =============================================

    function handleActualizarEstadoPago($db, $data) {
        try {
            $planillaModelo = new PlanillaModelo($db);

            $id_planilla_emp = intval($data['id_planilla_emp'] ?? 0);
            $id_persona = intval($data['id_persona'] ?? 0);

            if ($id_planilla_emp <= 0) {
                sendResponse(400, ['status' => 400, 'message' => 'ID de planilla inválido']);
                return;
            }

            if ($id_persona <= 0) {
                sendResponse(400, ['status' => 400, 'message' => 'ID de persona inválido']);
                return;
            }

            $resultado = $planillaModelo->actualizarEstadoPago($id_planilla_emp, $id_persona);

            if($resultado === true){
                sendResponse(200, [
                    'status' => 200,
                    'message' => 'Estado de pago actualizado exitosamente'
                ]);
            } else {
                sendResponse(500, [
                    'status' => 500,
                    'message' => 'Error al actualizar estado de pago'
                ]);
            }
        } catch (Exception $e) {
            sendResponse(500, [
                'status' => 500,
                'message' => 'Error en base de datos: ' . $e->getMessage()
            ]);
        }
    }

    function handleEliminarPlanillaPeriodo($db, $data) {
        try {
            $planillaModelo = new PlanillaModelo($db);

            $mes = intval($data['mes'] ?? 0);
            $anio = intval($data['anio'] ?? 0);

            if ($mes < 1 || $mes > 12) {
                sendResponse(400, ['status' => 400, 'message' => 'El mes debe estar entre 1 y 12']);
                return;
            }

            if ($anio < 2020 || $anio > 2030) {
                sendResponse(400, ['status' => 400, 'message' => 'El año debe estar entre 2020 y 2030']);
                return;
            }

            $resultado = $planillaModelo->eliminarPlanillaPeriodo($mes, $anio);

            if($resultado === true){
                sendResponse(200, [
                    'status' => 200,
                    'message' => 'Planillas del período eliminadas exitosamente',
                    'periodo' => sprintf("%04d-%02d", $anio, $mes)
                ]);
            } else {
                sendResponse(500, [
                    'status' => 500,
                    'message' => 'Error al eliminar planillas del período'
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
    // FUNCIONES AUXILIARES
    // =============================================

    function sendResponse($statusCode, $data) {
        http_response_code($statusCode);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit();
    }
    ?>


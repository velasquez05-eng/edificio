<?php
// departamento.php - API completa para gestión de departamentos
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Manejar preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

error_log("DEBUG: departamento.php accedido - Método: " . $_SERVER['REQUEST_METHOD']);

try {
    require_once '../../config/database.php';
    require_once '../modelo/DepartamentoModelo.php';
    require_once '../modelo/PersonaModelo.php';
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
            case 'listarDepartamentos':
                handleListarDepartamentos($db, $input);
                break;
            case 'listarDepartamentosCompletos': // NUEVO ENDPOINT
                handleListarDepartamentosCompletos($db, $input);
                break;
            case 'obtenerDepartamentoCompleto':
                handleObtenerDepartamentoCompleto($db, $input);
                break;
            case 'crearDepartamento':
                handleCrearDepartamento($db, $input);
                break;
            case 'editarDepartamento':
                handleEditarDepartamento($db, $input);
                break;
            case 'asignarPersonasDepartamento':
                handleAsignarPersonasDepartamento($db, $input);
                break;
            case 'desvincularPersonaDepartamento':
                handleDesvincularPersonaDepartamento($db, $input);
                break;
            case 'listarPersonasParaAsignar':
                handleListarPersonasParaAsignar($db, $input);
                break;
            case 'obtenerDepartamentosPersona':
                handleObtenerDepartamentosPersona($db, $input);
                break;
            case 'verificarNumeroDepartamento':
                handleVerificarNumeroDepartamento($db, $input);
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
        case 'listarDepartamentos':
            handleListarDepartamentos($db, $_GET);
            break;
        case 'listarDepartamentosCompletos': // NUEVO ENDPOINT
            handleListarDepartamentosCompletos($db, $_GET);
            break;
        case 'obtenerDepartamentoCompleto':
            handleObtenerDepartamentoCompleto($db, $_GET);
            break;
        case 'listarPersonasParaAsignar':
            handleListarPersonasParaAsignar($db, $_GET);
            break;
        case 'obtenerDepartamentosPersona':
            handleObtenerDepartamentosPersona($db, $_GET);
            break;
        default:
            sendResponse(400, ['status' => 400, 'message' => 'Acción GET no válida: ' . $action]);
    }
} else {
    sendResponse(405, ['status' => 405, 'message' => 'Método no permitido']);
}

// =============================================
// ENDPOINTS PARA GESTIÓN DE DEPARTAMENTOS
// =============================================

function handleListarDepartamentos($db, $data) {
    try {
        $departamentoModelo = new DepartamentoModelo($db);
        $servicioModelo = new ServicioModelo($db);

        $departamentos = $departamentoModelo->listarDepartamento();

        // Para cada departamento, obtener sus medidores y servicios
        foreach ($departamentos as &$departamento) {
            $medidores = $servicioModelo->obtenerMedidoresDepartamento($departamento['id_departamento']);

            // Para cada medidor, obtener la información completa del servicio
            foreach ($medidores as &$medidor) {
                if (isset($medidor['id_servicio'])) {
                    $servicio = $servicioModelo->obtenerServicioPorId($medidor['id_servicio']);
                    $medidor['servicio'] = $servicio ? $servicio : null;
                }
            }

            $departamento['medidores'] = $medidores;
        }

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

// NUEVA FUNCIÓN: Listar departamentos con información completa de residentes
function handleListarDepartamentosCompletos($db, $data) {
    try {
        $departamentoModelo = new DepartamentoModelo($db);
        $servicioModelo = new ServicioModelo($db);
        $personaModelo = new PersonaModelo($db);

        // Obtener departamentos básicos
        $departamentos = $departamentoModelo->listarDepartamento();

        // Para cada departamento, obtener información completa
        foreach ($departamentos as &$departamento) {
            $id_departamento = $departamento['id_departamento'];

            // 1. Obtener residentes del departamento
            $residentes = $personaModelo->obtenerResidentesPorDepartamento($id_departamento);
            $departamento['residentes'] = $residentes ? $residentes : [];

            // 2. Obtener medidores del departamento
            $medidores = $servicioModelo->obtenerMedidoresDepartamento($id_departamento);

            // 3. Para cada medidor, obtener información completa del servicio
            foreach ($medidores as &$medidor) {
                if (isset($medidor['id_servicio'])) {
                    $servicio = $servicioModelo->obtenerServicioPorId($medidor['id_servicio']);
                    $medidor['servicio'] = $servicio ? $servicio : null;

                    // Obtener último consumo si está disponible
                    if ($servicio) {
                        $ultimoConsumo = $servicioModelo->obtenerUltimoConsumo($medidor['id_medidor']);
                        $medidor['ultimo_consumo'] = $ultimoConsumo ? $ultimoConsumo : null;
                    }
                }
            }

            $departamento['medidores'] = $medidores;

            // 4. Calcular estadísticas
            $departamento['total_residentes'] = count($departamento['residentes']);
            $departamento['total_medidores'] = count($departamento['medidores']);

            // 5. Determinar estado basado en residentes
            $departamento['estado_ocupacion'] = ($departamento['total_residentes'] > 0) ? 'Ocupado' : 'Desocupado';
        }

        sendResponse(200, [
            'status' => 200,
            'message' => 'Departamentos completos listados exitosamente',
            'data' => $departamentos,
            'total' => count($departamentos),
            'metadata' => [
                'total_ocupados' => count(array_filter($departamentos, function($dep) {
                    return $dep['total_residentes'] > 0;
                })),
                'total_desocupados' => count(array_filter($departamentos, function($dep) {
                    return $dep['total_residentes'] == 0;
                }))
            ]
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al listar departamentos completos: ' . $e->getMessage()
        ]);
    }
}

function handleObtenerDepartamentoCompleto($db, $data) {
    try {
        $departamentoModelo = new DepartamentoModelo($db);
        $personaModelo = new PersonaModelo($db);
        $servicioModelo = new ServicioModelo($db);

        $id_departamento = intval($data['id_departamento'] ?? 0);

        if ($id_departamento <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de departamento inválido']);
            return;
        }

        // Obtener información básica del departamento
        $departamento = $departamentoModelo->obtenerDepartamentoPorId($id_departamento);

        if (!$departamento) {
            sendResponse(404, ['status' => 404, 'message' => 'Departamento no encontrado']);
            return;
        }

        // Obtener residentes del departamento
        $residentes = $personaModelo->obtenerResidentesPorDepartamento($id_departamento);
        $departamento['residentes'] = $residentes ? $residentes : [];

        // Obtener medidores del departamento
        $medidores = $servicioModelo->obtenerMedidoresDepartamento($id_departamento);

        // Para cada medidor, obtener información completa
        foreach ($medidores as &$medidor) {
            if (isset($medidor['id_servicio'])) {
                $servicio = $servicioModelo->obtenerServicioPorId($medidor['id_servicio']);
                $medidor['servicio'] = $servicio ? $servicio : null;

                // Obtener historial de consumo reciente
                if ($servicio) {
                    $historialReciente = $servicioModelo->obtenerHistorialConsumoReciente($medidor['id_medidor'], 5);
                    $medidor['historial_reciente'] = $historialReciente ? $historialReciente : [];
                }
            }
        }

        $departamento['medidores'] = $medidores;

        // Calcular estadísticas
        $departamento['total_residentes'] = count($departamento['residentes']);
        $departamento['total_medidores'] = count($departamento['medidores']);

        sendResponse(200, [
            'status' => 200,
            'message' => 'Departamento obtenido exitosamente',
            'data' => $departamento
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al obtener departamento: ' . $e->getMessage()
        ]);
    }
}

// Las demás funciones permanecen igual...
function handleCrearDepartamento($db, $data) {
    try {
        $departamentoModelo = new DepartamentoModelo($db);

        // Validar campos requeridos
        $camposRequeridos = ['numero', 'piso'];
        foreach($camposRequeridos as $campo) {
            if(!isset($data[$campo]) || empty(trim($data[$campo]))) {
                sendResponse(400, ['status' => 400, 'message' => "El campo $campo es obligatorio"]);
                return;
            }
        }

        $numero = htmlspecialchars(trim($data['numero']));
        $piso = intval($data['piso']);

        // Validaciones adicionales
        if ($piso <= 0) {
            sendResponse(400, ['status' => 400, 'message' => "El piso debe ser un número mayor a 0"]);
            return;
        }

        // Verificar si el número de departamento ya existe
        if ($departamentoModelo->verificarDepartamento($numero)) {
            sendResponse(400, ['status' => 400, 'message' => "El número de departamento ya está registrado en el sistema"]);
            return;
        }

        $resultado = $departamentoModelo->registrarDepartamento($numero, $piso);

        if($resultado === true){
            sendResponse(201, [
                'status' => 201,
                'message' => "Departamento registrado exitosamente"
            ]);
        } else {
            sendResponse(500, [
                'status' => 500,
                'message' => "Error al registrar departamento: " . $resultado
            ]);
        }

    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error en base de datos: ' . $e->getMessage()
        ]);
    }
}

function handleEditarDepartamento($db, $data) {
    try {
        $departamentoModelo = new DepartamentoModelo($db);

        $camposRequeridos = ['id_departamento', 'numero', 'piso'];
        foreach($camposRequeridos as $campo) {
            if(!isset($data[$campo]) || empty(trim($data[$campo]))) {
                sendResponse(400, ['status' => 400, 'message' => "El campo $campo es obligatorio"]);
                return;
            }
        }

        $id_departamento = intval($data['id_departamento']);
        $numero = htmlspecialchars(trim($data['numero']));
        $piso = intval($data['piso']);

        // Validaciones adicionales
        if ($piso <= 0) {
            sendResponse(400, ['status' => 400, 'message' => "El piso debe ser un número mayor a 0"]);
            return;
        }

        // Verificar si el número ya existe en OTRO departamento (excluyendo el actual)
        if ($departamentoModelo->verificarDepartamentoExcluyendo($numero, $id_departamento)) {
            sendResponse(400, ['status' => 400, 'message' => "El número de departamento ya está registrado en otro departamento"]);
            return;
        }

        $resultado = $departamentoModelo->editarDepartamento($id_departamento, $numero, $piso);

        if($resultado === true){
            sendResponse(200, [
                'status' => 200,
                'message' => "Departamento actualizado exitosamente"
            ]);
        } else {
            sendResponse(500, [
                'status' => 500,
                'message' => "Error al actualizar departamento: " . $resultado
            ]);
        }

    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error en base de datos: ' . $e->getMessage()
        ]);
    }
}

function handleAsignarPersonasDepartamento($db, $data) {
    try {
        $departamentoModelo = new DepartamentoModelo($db);

        $camposRequeridos = ['id_departamento', 'personas_ids'];
        foreach($camposRequeridos as $campo) {
            if(!isset($data[$campo])) {
                sendResponse(400, ['status' => 400, 'message' => "El campo $campo es obligatorio"]);
                return;
            }
        }

        $id_departamento = intval($data['id_departamento']);
        $personas_ids = $data['personas_ids'];

        // Validar que personas_ids sea un array
        if (!is_array($personas_ids) || empty($personas_ids)) {
            sendResponse(400, ['status' => 400, 'message' => "Debe seleccionar al menos una persona para asignar"]);
            return;
        }

        // Validar que todos los IDs sean números válidos
        foreach ($personas_ids as $id_persona) {
            if (!is_numeric($id_persona) || $id_persona <= 0) {
                sendResponse(400, ['status' => 400, 'message' => "ID de persona inválido: " . $id_persona]);
                return;
            }
        }

        $resultado = $departamentoModelo->asignarPersonasDepartamento($id_departamento, $personas_ids);

        if($resultado === true){
            sendResponse(200, [
                'status' => 200,
                'message' => "Personas asignadas al departamento exitosamente"
            ]);
        } else {
            sendResponse(500, [
                'status' => 500,
                'message' => "Error al asignar personas: " . $resultado
            ]);
        }

    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error en base de datos: ' . $e->getMessage()
        ]);
    }
}

function handleDesvincularPersonaDepartamento($db, $data) {
    try {
        $departamentoModelo = new DepartamentoModelo($db);

        $camposRequeridos = ['id_persona', 'id_departamento'];
        foreach($camposRequeridos as $campo) {
            if(!isset($data[$campo]) || empty(trim($data[$campo]))) {
                sendResponse(400, ['status' => 400, 'message' => "El campo $campo es obligatorio"]);
                return;
            }
        }

        $id_persona = intval($data['id_persona']);
        $id_departamento = intval($data['id_departamento']);

        $resultado = $departamentoModelo->desvincularDepartamentoPersona($id_persona, $id_departamento);

        if($resultado === true){
            sendResponse(200, [
                'status' => 200,
                'message' => "Persona desvinculada del departamento exitosamente"
            ]);
        } else {
            sendResponse(500, [
                'status' => 500,
                'message' => "Error al desvincular persona: " . $resultado
            ]);
        }

    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error en base de datos: ' . $e->getMessage()
        ]);
    }
}

function handleListarPersonasParaAsignar($db, $data) {
    try {
        $personaModelo = new PersonaModelo($db);
        $residentes = $personaModelo->listarResidentesSinDepartamento();

        sendResponse(200, [
            'status' => 200,
            'message' => 'Personas para asignar listadas exitosamente',
            'data' => $residentes,
            'total' => count($residentes)
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al listar personas para asignar: ' . $e->getMessage()
        ]);
    }
}

function handleObtenerDepartamentosPersona($db, $data) {
    try {
        $departamentoModelo = new DepartamentoModelo($db);

        $id_persona = intval($data['id_persona'] ?? 0);

        if ($id_persona <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de persona inválido']);
            return;
        }

        $departamentos = $departamentoModelo->listarDepartamentoPersona($id_persona);

        sendResponse(200, [
            'status' => 200,
            'message' => 'Departamentos de la persona obtenidos exitosamente',
            'data' => $departamentos,
            'total' => count($departamentos)
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al obtener departamentos de la persona: ' . $e->getMessage()
        ]);
    }
}

function handleVerificarNumeroDepartamento($db, $data) {
    try {
        $departamentoModelo = new DepartamentoModelo($db);

        $numero = $data['numero'] ?? '';
        $id_excluir = intval($data['id_excluir'] ?? 0);

        if (empty($numero)) {
            sendResponse(400, ['status' => 400, 'message' => 'Número de departamento requerido']);
            return;
        }

        $existe = false;

        if ($id_excluir > 0) {
            // Verificar excluyendo un ID (para edición)
            $existe = $departamentoModelo->verificarDepartamentoExcluyendo($numero, $id_excluir);
        } else {
            // Verificar sin excluir (para creación)
            $existe = $departamentoModelo->verificarDepartamento($numero);
        }

        sendResponse(200, [
            'status' => 200,
            'message' => 'Verificación completada',
            'data' => [
                'existe' => $existe,
                'numero' => $numero,
                'disponible' => !$existe
            ]
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al verificar número de departamento: ' . $e->getMessage()
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
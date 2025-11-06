<?php
// persona.php - API completa para gestión de personas
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Manejar preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

error_log("DEBUG: persona.php accedido - Método: " . $_SERVER['REQUEST_METHOD']);

try {
    require_once '../../config/database.php';
    require_once '../modelo/PersonaModelo.php';
    require_once '../modelo/RolModelo.php';
    require_once '../modelo/CorreoModelo.php';

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
            case 'login':
                handleLogin($db, $input);
                break;
            case 'cambiarContraseña':
                handleChangePassword($db, $input);
                break;
            case 'solicitarCodigo':
                handleSolicitarCodigo($db, $input);
                break;
            case 'verificarCodigo':
                handleVerificarCodigo($db, $input);
                break;
            // NUEVOS ENDPOINTS PARA GESTIÓN DE PERSONAS
            case 'listarPersonal':
                handleListarPersonal($db, $input);
                break;
            case 'listarResidentes':
                handleListarResidentes($db, $input);
                break;
            case 'listarEliminados':
                handleListarEliminados($db, $input);
                break;
            case 'listarRoles':
                handleListarRoles($db, $input);
                break;
            case 'crearPersona':
                handleCrearPersona($db, $input);
                break;
            case 'crearRol':
                handleCrearRol($db, $input);
                break;
            case 'editarPersona':
                handleEditarPersona($db, $input);
                break;
            case 'editarRol':
                handleEditarRol($db, $input);
                break;
            case 'eliminarPersona':
                handleEliminarPersona($db, $input);
                break;
            case 'restaurarPersona':
                handleRestaurarPersona($db, $input);
                break;
            case 'restablecerPassword':
                handleRestablecerPassword($db, $input);
                break;
            case 'ampliarTiempoVerificacion':
                handleAmpliarTiempoVerificacion($db, $input);
                break;
            default:
                sendResponse(400, ['status' => 400, 'message' => 'Acción no válida: ' . $action]);
        }
    } else {
        sendResponse(400, ['status' => 400, 'message' => 'Datos de entrada inválidos o acción no especificada']);
    }
} else {
    sendResponse(405, ['status' => 405, 'message' => 'Método no permitido']);
}

// =============================================
// ENDPOINTS EXISTENTES (MANTENER)
// =============================================

function handleLogin($db, $data) {
    try {
        $personaModelo = new PersonaModelo($db);
        $rolModelo = new RolModelo($db);

        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';

        if (empty($username) || empty($password)) {
            sendResponse(400, ['status' => 400, 'message' => 'Todos los campos son obligatorios']);
            return;
        }

        $user = $personaModelo->login($username, $password);

        if ($user) {
            $personaModelo->successLogin($user['id_persona'], $username);
            $estaVerificado = (bool)$user['verificado'];

            if (!$estaVerificado) {
                sendResponse(426, [
                    'status' => 426,
                    'message' => 'Debe verificar su cuenta primero',
                    'data' => formatUserData($user, $rolModelo)
                ]);
            } else {
                sendResponse(200, [
                    'status' => 200,
                    'message' => 'Login exitoso',
                    'data' => formatUserData($user, $rolModelo)
                ]);
            }
        } else {
            $personaModelo->errorLogin($username);
            sendResponse(401, [
                'status' => 401,
                'message' => 'Usuario o contraseña incorrectos'
            ]);
        }

    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error interno del servidor en login: ' . $e->getMessage()
        ]);
    }
}

function handleChangePassword($db, $data) {
    try {
        $personaModelo = new PersonaModelo($db);
        $rolModelo = new RolModelo($db);

        $id_persona = intval($data['id_persona'] ?? 0);
        $new_password = $data['new_password'] ?? '';

        if ($id_persona <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de persona inválido']);
            return;
        }

        if (empty($new_password)) {
            sendResponse(400, ['status' => 400, 'message' => 'La contraseña no puede estar vacía']);
            return;
        }

        $resultado = $personaModelo->cambiarPassword($id_persona, $new_password);

        if ($resultado) {
            $userData = $personaModelo->obtenerPersonaPorId($id_persona);
            sendResponse(200, [
                'status' => 200,
                'message' => 'Contraseña cambiada exitosamente',
                'data' => formatUserData($userData, $rolModelo)
            ]);
        } else {
            sendResponse(500, ['status' => 500, 'message' => 'Error al cambiar la contraseña']);
        }

    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error interno del servidor en cambio de contraseña: ' . $e->getMessage()
        ]);
    }
}

function handleSolicitarCodigo($db, $data) {
    try {
        $personaModelo = new PersonaModelo($db);
        $correo = new CorreoModelo($db);
        $email = $data['email'] ?? '';

        if (empty($email)) {
            sendResponse(400, ['status' => 400, 'message' => 'El email es obligatorio']);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            sendResponse(400, ['status' => 400, 'message' => 'El formato del email no es válido']);
            return;
        }

        $persona = $personaModelo->obtenerPersonaPorEmail($email);

        if (!$persona) {
            sendResponse(404, ['status' => 404, 'message' => 'No existe una cuenta con este email']);
            return;
        }

        $id_persona = $persona['id_persona'];
        $codigo_recuperacion = sprintf("%06d", mt_rand(1, 999999));
        $expiracion = date('Y-m-d H:i:s', strtotime('+30 minutes'));

        $resultado = $personaModelo->guardarCodigoRecuperacion($id_persona, $codigo_recuperacion, $expiracion);

        if ($resultado) {
            $correo->notificarCodigoRecuperacion($email,$persona['nombre'],$codigo_recuperacion);
            sendResponse(200, [
                'status' => 200,
                'message' => 'Código de recuperación enviado',
                'data' => [
                    'id_persona' => $id_persona,
                    'codigo_debug' => $codigo_recuperacion,
                    'email' => $email
                ]
            ]);
        } else {
            sendResponse(500, ['status' => 500, 'message' => 'Error al generar el código de recuperación']);
        }

    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error interno del servidor: ' . $e->getMessage()
        ]);
    }
}

function handleVerificarCodigo($db, $data) {
    try {
        $personaModelo = new PersonaModelo($db);

        $id_persona = intval($data['id_persona'] ?? 0);
        $codigo = $data['codigo'] ?? '';

        if ($id_persona <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de persona inválido']);
            return;
        }

        if (empty($codigo) || strlen($codigo) !== 6) {
            sendResponse(400, ['status' => 400, 'message' => 'El código debe tener 6 dígitos']);
            return;
        }

        $codigoValido = $personaModelo->verificarCodigoRecuperacion($id_persona, $codigo);

        if ($codigoValido) {
            sendResponse(200, [
                'status' => 200,
                'message' => 'Código verificado correctamente',
                'data' => [
                    'id_persona' => $id_persona,
                    'codigo_valido' => true
                ]
            ]);
        } else {
            sendResponse(400, ['status' => 400, 'message' => 'Código inválido o expirado']);
        }

    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error interno del servidor: ' . $e->getMessage()
        ]);
    }
}

// =============================================
// NUEVOS ENDPOINTS PARA GESTIÓN DE PERSONAS
// =============================================

function handleListarPersonal($db, $data) {
    try {
        $personaModelo = new PersonaModelo($db);
        $personal = $personaModelo->listarPersonal();

        sendResponse(200, [
            'status' => 200,
            'message' => 'Personal listado exitosamente',
            'data' => $personal,
            'total' => count($personal)
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al listar personal: ' . $e->getMessage()
        ]);
    }
}

function handleListarResidentes($db, $data) {
    try {
        $personaModelo = new PersonaModelo($db);
        $residentes = $personaModelo->listarResidente();

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

function handleListarEliminados($db, $data) {
    try {
        $personaModelo = new PersonaModelo($db);
        $eliminados = $personaModelo->listarEliminados();

        sendResponse(200, [
            'status' => 200,
            'message' => 'Personas eliminadas listadas exitosamente',
            'data' => $eliminados,
            'total' => count($eliminados)
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al listar personas eliminadas: ' . $e->getMessage()
        ]);
    }
}

function handleListarRoles($db, $data) {
    try {
        $rolModelo = new RolModelo($db);
        $roles = $rolModelo->listarRoles();

        sendResponse(200, [
            'status' => 200,
            'message' => 'Roles listados exitosamente',
            'data' => $roles,
            'total' => count($roles)
        ]);
    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error al listar roles: ' . $e->getMessage()
        ]);
    }
}

function handleCrearPersona($db, $data) {
    try {
        $personaModelo = new PersonaModelo($db);
        $rolModelo = new RolModelo($db);

        // Validar campos requeridos
        $camposRequeridos = ['nombre', 'apellido_paterno', 'ci', 'telefono', 'email', 'username', 'password', 'id_rol'];
        foreach($camposRequeridos as $campo) {
            if(!isset($data[$campo]) || empty(trim($data[$campo]))) {
                sendResponse(400, ['status' => 400, 'message' => "El campo $campo es obligatorio"]);
                return;
            }
        }

        $nombre = htmlspecialchars(trim($data['nombre']));
        $apellido_paterno = htmlspecialchars(trim($data['apellido_paterno']));
        $apellido_materno = isset($data['apellido_materno']) ? htmlspecialchars(trim($data['apellido_materno'])) : '';
        $ci = htmlspecialchars(trim($data['ci']));
        $telefono = htmlspecialchars(trim($data['telefono']));
        $email = filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL);
        $username = htmlspecialchars(trim($data['username']));
        $password = $data['password'];
        $id_rol = intval($data['id_rol']);

        // Validaciones adicionales
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            sendResponse(400, ['status' => 400, 'message' => "El formato del email no es válido"]);
            return;
        }

        if (strlen($password) < 8) {
            sendResponse(400, ['status' => 400, 'message' => "La contraseña debe tener al menos 8 caracteres"]);
            return;
        }

        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            sendResponse(400, ['status' => 400, 'message' => "El username solo puede contener letras, números y guiones bajos"]);
            return;
        }

        // Verificar si el CI ya existe
        if ($personaModelo->verificarCIExistente($ci)) {
            sendResponse(400, ['status' => 400, 'message' => "El número de CI ya está registrado en el sistema"]);
            return;
        }

        // Verificar si el username ya existe
        if ($personaModelo->verificarUsuarioExistente($username)) {
            sendResponse(400, ['status' => 400, 'message' => "El username ya está registrado en el sistema"]);
            return;
        }

        $resultado = $personaModelo->registrarPersona($nombre, $apellido_paterno, $apellido_materno, $ci, $telefono, $email, $username, $password, $id_rol);

        if($resultado){
            $rol = $rolModelo->obtenerRol($id_rol);
            sendResponse(201, [
                'status' => 201,
                'message' => "Persona registrada exitosamente como " . $rol['rol'] . ". Tiene 3 días para verificar su cuenta."
            ]);
        } else {
            sendResponse(500, ['status' => 500, 'message' => "Error al registrar persona"]);
        }

    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error en base de datos: ' . $e->getMessage()
        ]);
    }
}

function handleCrearRol($db, $data) {
    try {
        $rolModelo = new RolModelo($db);

        $rol = $data['rol'] ?? '';
        $descripcion = $data['descripcion'] ?? '';
        $salario_base = $data['salario_base'] ?? '';

        if(empty($rol) || empty($descripcion) || empty($salario_base)){
            sendResponse(400, ['status' => 400, 'message' => "Todos los campos son obligatorios"]);
            return;
        }

        if(!is_numeric($salario_base) || $salario_base < 0){
            sendResponse(400, ['status' => 400, 'message' => "El salario base debe ser un número válido mayor o igual a 0"]);
            return;
        }

        $resultado = $rolModelo->registrarRol($rol, $descripcion, $salario_base);

        if ($resultado) {
            sendResponse(201, [
                'status' => 201,
                'message' => "Rol creado exitosamente"
            ]);
        } else {
            sendResponse(500, ['status' => 500, 'message' => "Error al crear el rol"]);
        }

    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error en base de datos: ' . $e->getMessage()
        ]);
    }
}

function handleEditarPersona($db, $data) {
    try {
        $personaModelo = new PersonaModelo($db);
        $rolModelo = new RolModelo($db);

        $camposRequeridos = ['id_persona', 'nombre', 'apellido_paterno', 'telefono', 'email', 'id_rol'];
        foreach($camposRequeridos as $campo) {
            if(!isset($data[$campo]) || empty(trim($data[$campo]))) {
                sendResponse(400, ['status' => 400, 'message' => "El campo $campo es obligatorio"]);
                return;
            }
        }

        $id_persona = intval($data['id_persona']);
        $nombre = htmlspecialchars(trim($data['nombre']));
        $apellido_paterno = htmlspecialchars(trim($data['apellido_paterno']));
        $apellido_materno = isset($data['apellido_materno']) ? htmlspecialchars(trim($data['apellido_materno'])) : '';
        $telefono = htmlspecialchars(trim($data['telefono']));
        $email = filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL);
        $id_rol = intval($data['id_rol']);

        // Verificar si el email ya existe (excluyendo la persona actual)
        if ($personaModelo->verificarEmail($id_persona, $email)) {
            sendResponse(400, ['status' => 400, 'message' => "El email ya está registrado en el sistema"]);
            return;
        }

        $resultado = $personaModelo->editarPersona($id_persona, $nombre, $apellido_paterno, $apellido_materno, $telefono, $email, $id_rol);

        if($resultado){
            $rol = $rolModelo->obtenerRol($id_rol);
            sendResponse(200, [
                'status' => 200,
                'message' => "Persona editada exitosamente con el rol de " . $rol['rol']
            ]);
        } else {
            sendResponse(500, ['status' => 500, 'message' => "Error al editar persona"]);
        }

    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error en base de datos: ' . $e->getMessage()
        ]);
    }
}

function handleEditarRol($db, $data) {
    try {
        $rolModelo = new RolModelo($db);

        $camposRequeridos = ['id_rol', 'rol', 'descripcion', 'salario_base'];
        foreach($camposRequeridos as $campo) {
            if(!isset($data[$campo]) || empty(trim($data[$campo]))) {
                sendResponse(400, ['status' => 400, 'message' => "El campo $campo es obligatorio"]);
                return;
            }
        }

        $id_rol = intval($data['id_rol']);
        $rol = htmlspecialchars(trim($data['rol']));
        $descripcion = htmlspecialchars(trim($data['descripcion']));
        $salario_base = floatval($data['salario_base']);

        // Validaciones adicionales
        if ($salario_base < 0) {
            sendResponse(400, ['status' => 400, 'message' => "El salario base debe ser un número válido mayor o igual a 0"]);
            return;
        }

        $resultado = $rolModelo->editarRol($id_rol, $rol, $descripcion, $salario_base);

        if($resultado){
            sendResponse(200, [
                'status' => 200,
                'message' => "Rol editado exitosamente"
            ]);
        } else {
            sendResponse(500, ['status' => 500, 'message' => "Error al editar rol"]);
        }

    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error en base de datos: ' . $e->getMessage()
        ]);
    }
}

function handleEliminarPersona($db, $data) {
    try {
        $personaModelo = new PersonaModelo($db);

        $id_persona = intval($data['id_persona'] ?? 0);

        if ($id_persona <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de persona inválido']);
            return;
        }

        $resultado = $personaModelo->eliminarPersona($id_persona);

        if($resultado){
            sendResponse(200, [
                'status' => 200,
                'message' => "Persona eliminada exitosamente"
            ]);
        } else {
            sendResponse(500, ['status' => 500, 'message' => "Error al eliminar persona"]);
        }

    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error en base de datos: ' . $e->getMessage()
        ]);
    }
}

function handleRestaurarPersona($db, $data) {
    try {
        $personaModelo = new PersonaModelo($db);

        $id_persona = intval($data['id_persona'] ?? 0);

        if ($id_persona <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de persona inválido']);
            return;
        }

        $resultado = $personaModelo->restaurarPersona($id_persona);

        if ($resultado) {
            sendResponse(200, [
                'status' => 200,
                'message' => "Persona restaurada exitosamente"
            ]);
        } else {
            sendResponse(500, ['status' => 500, 'message' => "Error al restaurar persona"]);
        }

    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error en base de datos: ' . $e->getMessage()
        ]);
    }
}

function handleRestablecerPassword($db, $data) {
    try {
        $personaModelo = new PersonaModelo($db);

        $id_persona = intval($data['id_persona'] ?? 0);
        $password = $data['password'] ?? '';

        if ($id_persona <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de persona inválido']);
            return;
        }

        if (empty($password)) {
            sendResponse(400, ['status' => 400, 'message' => 'La contraseña es obligatoria']);
            return;
        }

        $resultado = $personaModelo->restablecerPassword($id_persona, $password);

        if($resultado){
            sendResponse(200, [
                'status' => 200,
                'message' => "Contraseña restablecida exitosamente"
            ]);
        } else {
            sendResponse(500, ['status' => 500, 'message' => "Error al restablecer contraseña"]);
        }

    } catch (Exception $e) {
        sendResponse(500, [
            'status' => 500,
            'message' => 'Error en base de datos: ' . $e->getMessage()
        ]);
    }
}

function handleAmpliarTiempoVerificacion($db, $data) {
    try {
        $personaModelo = new PersonaModelo($db);

        $id_persona = intval($data['id_persona'] ?? 0);
        $tiempo = intval($data['tiempo'] ?? 0);

        if ($id_persona <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'ID de persona inválido']);
            return;
        }

        if ($tiempo <= 0) {
            sendResponse(400, ['status' => 400, 'message' => 'El tiempo debe ser mayor a 0']);
            return;
        }

        $resultado = $personaModelo->ampliarTiempoVerificacion($id_persona, $tiempo);

        if($resultado){
            sendResponse(200, [
                'status' => 200,
                'message' => "Tiempo de verificación ampliado exitosamente"
            ]);
        } else {
            sendResponse(500, ['status' => 500, 'message' => "Error al ampliar tiempo de verificación"]);
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

function formatUserData($userData, $rolModelo) {
    $rol = $rolModelo->obtenerRol($userData['id_rol']);

    return [
        'id_persona' => $userData['id_persona'],
        'nombre' => $userData['nombre'],
        'apellido_paterno' => $userData['apellido_paterno'],
        'apellido_materno' => $userData['apellido_materno'] ?? '',
        'email' => $userData['email'],
        'username' => $userData['username'],
        'telefono' => $userData['telefono'],
        'ci' => $userData['ci'],
        'rol' => $userData['id_rol'],
        'rol_nombre' => $rol['rol'] ?? 'Sin rol',
        'verificado' => (bool)$userData['verificado']
    ];
}

function sendResponse($statusCode, $data) {
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}
?>
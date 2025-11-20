<?php

class AgenteControlador
{
    private $modelo;

    public function __construct($db)
    {
        require_once '../modelo/AgenteModelo.php';
        $this->modelo = new AgenteModelo($db);
    }

    /**
     * Enviar mensaje al agente
     */
    public function enviarMensaje()
    {
        // Verificar sesión
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['id_persona']) || !isset($_SESSION['id_rol'])) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'error' => 'No autorizado. Debes iniciar sesión.'
            ]);
            return;
        }

        // Obtener datos del POST
        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['mensaje']) || empty(trim($input['mensaje']))) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'El mensaje es requerido'
            ]);
            return;
        }

        $mensaje = trim($input['mensaje']);
        $historial = $input['historial'] ?? [];
        $id_rol = $_SESSION['id_rol'];
        $id_persona = $_SESSION['id_persona'];

        // Formatear historial si existe
        $historialFormateado = $this->modelo->formatearHistorial($historial);

        // Enviar mensaje al modelo
        $resultado = $this->modelo->enviarMensaje($mensaje, $id_rol, $id_persona, $historialFormateado);

        // Responder con JSON
        header('Content-Type: application/json');
        echo json_encode($resultado);
    }

    /**
     * Obtener información del sistema (para ayudar al agente)
     */
    public function obtenerInfoSistema()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['id_persona']) || !isset($_SESSION['id_rol'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'No autorizado']);
            return;
        }

        $id_rol = $_SESSION['id_rol'];
        $rol_nombre = $_SESSION['rol_nombre'] ?? 'Usuario';

        $info = [
            'success' => true,
            'rol' => [
                'id' => $id_rol,
                'nombre' => $rol_nombre
            ],
            'mensaje_bienvenida' => $this->obtenerMensajeBienvenida($id_rol),
            'preguntas_sugeridas' => $this->obtenerPreguntasSugeridas($id_rol)
        ];

        header('Content-Type: application/json');
        echo json_encode($info);
    }

    /**
     * Obtener preguntas sugeridas según el rol
     */
    private function obtenerPreguntasSugeridas($id_rol)
    {
        switch ($id_rol) {
            case 1: // Administrador
                return [
                    '¿Cómo asigno personal a un incidente?',
                    '¿Cómo genero facturas para un mes?',
                    '¿Cómo apruebo o rechazo reservas?',
                    '¿Cómo registro una nueva persona?',
                    '¿Cómo creo un departamento?',
                    '¿Cómo veo el historial de pagos?',
                    '¿Cómo registro un área común?',
                    '¿Cómo creo un comunicado?'
                ];
            case 2: // Residente
                return [
                    '¿Cómo hago una reserva de área común?',
                    '¿Cómo veo mis facturas?',
                    '¿Cómo reporto un incidente?',
                    '¿Cómo cancelo una reserva?',
                    '¿Cómo veo mi historial de pagos?',
                    '¿Cómo veo mis conceptos?',
                    '¿Cómo modifico mi reserva?',
                    '¿Cómo veo los comunicados?'
                ];
            case 3: // Personal
                return [
                    '¿Cómo atiendo un incidente asignado?',
                    '¿Cómo inicio la atención de un incidente?',
                    '¿Cómo actualizo el progreso de un incidente?',
                    '¿Cómo resuelvo un incidente?',
                    '¿Cómo solicito reasignación de un incidente?',
                    '¿Cómo veo mi planilla?',
                    '¿Cómo veo los comunicados?'
                ];
            default:
                return [
                    '¿Cómo uso el sistema?',
                    '¿Qué puedo hacer aquí?'
                ];
        }
    }

    /**
     * Obtener mensaje de bienvenida según el rol
     */
    private function obtenerMensajeBienvenida($id_rol)
    {
        switch ($id_rol) {
            case 1:
                return "¡Hola! Soy tu asistente virtual del Sistema de Gestión de Edificio. Como administrador, puedo ayudarte con la gestión de personas, departamentos, áreas comunes, incidentes, facturas, servicios y más. ¿En qué puedo ayudarte?";
            case 2:
                return "¡Hola! Soy tu asistente virtual. Como residente, puedo ayudarte con tus incidentes, reservas de áreas comunes, facturas, comunicados y más. ¿En qué puedo asistirte?";
            case 3:
                return "¡Hola! Soy tu asistente virtual. Como personal de mantenimiento, puedo ayudarte con los incidentes asignados, cómo actualizar su progreso, resolverlos y otras tareas. ¿Qué necesitas saber?";
            default:
                return "¡Hola! Soy tu asistente virtual del sistema. ¿En qué puedo ayudarte?";
        }
    }
}

// Manejo de rutas
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../../config/database.php';
    
    $database = new Database();
    $db = $database->getConnection();
    $controlador = new AgenteControlador($db);

    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? $_POST['action'] ?? 'enviarMensaje';

    switch ($action) {
        case 'enviarMensaje':
            $controlador->enviarMensaje();
            break;
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Acción no válida']);
            break;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    require_once '../../config/database.php';
    
    $database = new Database();
    $db = $database->getConnection();
    $controlador = new AgenteControlador($db);

    $action = $_GET['action'] ?? 'obtenerInfoSistema';

    switch ($action) {
        case 'obtenerInfoSistema':
            $controlador->obtenerInfoSistema();
            break;
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Acción no válida']);
            break;
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
}


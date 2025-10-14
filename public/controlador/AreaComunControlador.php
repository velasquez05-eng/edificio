<?php
class AreaComunControlador
{
    private $areamodelo;
    public function __construct($db){
        $this->areamodelo = new AreaComunModelo($db);
    }

    // Mostrar lista de areas comunes
    public function listarAreas(){
        $areascomunes = $this->areamodelo->listarAreas();
        include_once "../vista/ListarAreasComunesVista.php";
    }

    // Mostrar formulario para registrar nueva area
    public function formularioArea(){
        include_once "../vista/RegistrarAreaVista.php";
    }
    public function formularioReservaArea(){
        include_once "../vista/RegistrarReservaAreaVista.php";
    }
    // Registrar nueva area comun
    public function registrarArea(){
        try {
            if (empty($_POST['nombre']) || empty($_POST['capacidad'])) {
                header('Location: AreaComunControlador.php?action=formularioArea&error=Nombre+y+capacidad+son+obligatorios');
                exit;
            }

            $nombre = trim($_POST['nombre']);
            $descripcion = trim($_POST['descripcion'] ?? '');
            $capacidad = intval($_POST['capacidad']);
            $estado = $_POST['estado'] ?? 'disponible';

            if ($capacidad < 1) {
                header('Location: AreaComunControlador.php?action=formularioArea&error=La+capacidad+debe+ser+mayor+a+0');
                exit;
            }

            $resultado = $this->areamodelo->registrarArea($nombre, $descripcion, $capacidad, $estado);

            if ($resultado) {
                header('Location: AreaComunControlador.php?action=formularioArea&success=Area+comun+registrada+correctamente');
            } else {
                header('Location: AreaComunControlador.php?action=formularioArea&error=Error+al+registrar+el+area+comun');
            }
            exit;

        } catch (Exception $e) {
            error_log("Error en registrarArea controlador: " . $e->getMessage());
            header('Location: AreaComunControlador.php?action=formularioArea&error=Error+al+registrar+el+area+comun');
            exit;
        }
    }

    // Editar area comun existente
    public function editarArea(){
        try {
            if (empty($_POST['id_area']) || empty($_POST['nombre']) || empty($_POST['capacidad'])) {
                header('Location: AreaComunControlador.php?action=listarAreas&error=Todos+los+campos+son+obligatorios');
                exit;
            }

            $id_area = intval($_POST['id_area']);
            $nombre = trim($_POST['nombre']);
            $descripcion = trim($_POST['descripcion'] ?? '');
            $capacidad = intval($_POST['capacidad']);
            $estado = $_POST['estado'] ?? 'disponible';

            if ($capacidad < 1) {
                header('Location: AreaComunControlador.php?action=listarAreas&error=La+capacidad+debe+ser+mayor+a+0');
                exit;
            }

            $resultado = $this->areamodelo->editarArea($id_area, $nombre, $descripcion, $capacidad, $estado);

            if ($resultado) {
                header('Location: AreaComunControlador.php?action=listarAreas&success=Area+comun+actualizada+correctamente');
            } else {
                header('Location: AreaComunControlador.php?action=listarAreas&error=Error+al+actualizar+el+area+comun');
            }
            exit;

        } catch (Exception $e) {
            error_log("Error en editarArea controlador: " . $e->getMessage());
            header('Location: AreaComunControlador.php?action=listarAreas&error=Error+al+actualizar+el+area+comun');
            exit;
        }
    }

    // Eliminar area comun (eliminacion logica)
    public function eliminarArea(){
        try {
            if (!isset($_GET['id_area'])) {
                header('Location: AreaComunControlador.php?action=listarAreas&error=ID+de+area+no+especificado');
                exit;
            }

            $id_area = intval($_GET['id_area']);
            $resultado = $this->areamodelo->eliminarArea($id_area);

            if ($resultado) {
                header('Location: AreaComunControlador.php?action=listarAreas&success=Area+comun+eliminada+correctamente');
            } else {
                header('Location: AreaComunControlador.php?action=listarAreas&error=Error+al+eliminar+el+area+comun');
            }
            exit;

        } catch (Exception $e) {
            error_log("Error en eliminarArea controlador: " . $e->getMessage());
            header('Location: AreaComunControlador.php?action=listarAreas&error=Error+al+eliminar+el+area+comun');
            exit;
        }
    }

    // Obtener reservas por fecha (para AJAX)
    public function obtenerReservasPorFecha() {
        try {
            if (!isset($_GET['fecha'])) {
                header('Content-Type: application/json');
                echo json_encode([]);
                exit;
            }

            $fecha = $_GET['fecha'];

            // Obtener todas las areas
            $areas = $this->areamodelo->listarAreas();
            $todasLasReservas = [];

            // Para cada area, obtener sus reservas de la fecha
            foreach ($areas as $area) {
                $reservas = $this->areamodelo->obtenerReservasPorArea($area['id_area']);
                foreach ($reservas as $reserva) {
                    if ($reserva['fecha_reserva'] === $fecha) {
                        $reserva['area_nombre'] = $area['nombre'];
                        $todasLasReservas[] = $reserva;
                    }
                }
            }

            header('Content-Type: application/json');
            echo json_encode($todasLasReservas);
            exit;

        } catch (Exception $e) {
            error_log("Error en obtenerReservasPorFecha controlador: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode([]);
            exit;
        }
    }

    // Listar reservas de un area especifica (para AJAX)
    public function listarReservasArea() {
        try {
            if (!isset($_GET['id_area'])) {
                header('Content-Type: application/json');
                echo json_encode([]);
                exit;
            }

            $id_area = intval($_GET['id_area']);
            $reservas = $this->areamodelo->obtenerReservasPorArea($id_area);

            header('Content-Type: application/json');
            echo json_encode($reservas);
            exit;

        } catch (Exception $e) {
            error_log("Error en listarReservasArea controlador: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode([]);
            exit;
        }
    }

    // Confirmar una reserva pendiente
    public function confirmarReserva() {
        try {
            if (!isset($_POST['id_area']) || !isset($_POST['fecha_reserva']) || !isset($_POST['hora_inicio'])) {
                header('Location: AreaComunControlador.php?action=listarAreas&error=Datos+incompletos+para+confirmar+reserva');
                exit;
            }

            $id_area = intval($_POST['id_area']);
            $fecha_reserva = $_POST['fecha_reserva'];
            $hora_inicio = $_POST['hora_inicio'];

            $resultado = $this->areamodelo->actualizarEstadoReserva($id_area, $fecha_reserva, $hora_inicio, 'confirmada');

            if ($resultado) {
                header('Location: AreaComunControlador.php?action=listarAreas&success=Reserva+confirmada+correctamente');
            } else {
                header('Location: AreaComunControlador.php?action=listarAreas&error=Error+al+confirmar+la+reserva');
            }
            exit;

        } catch (Exception $e) {
            error_log("Error en confirmarReserva controlador: " . $e->getMessage());
            header('Location: AreaComunControlador.php?action=listarAreas&error=Error+al+confirmar+la+reserva');
            exit;
        }
    }

    // Cancelar una reserva
    public function cancelarReserva() {
        try {
            if (!isset($_POST['id_area']) || !isset($_POST['fecha_reserva']) || !isset($_POST['hora_inicio'])) {
                header('Location: AreaComunControlador.php?action=listarAreas&error=Datos+incompletos+para+cancelar+reserva');
                exit;
            }

            $id_area = intval($_POST['id_area']);
            $fecha_reserva = $_POST['fecha_reserva'];
            $hora_inicio = $_POST['hora_inicio'];

            $resultado = $this->areamodelo->actualizarEstadoReserva($id_area, $fecha_reserva, $hora_inicio, 'cancelada');

            if ($resultado) {
                header('Location: AreaComunControlador.php?action=listarAreas&success=Reserva+cancelada+correctamente');
            } else {
                header('Location: AreaComunControlador.php?action=listarAreas&error=Error+al+cancelar+la+reserva');
            }
            exit;

        } catch (Exception $e) {
            error_log("Error en cancelarReserva controlador: " . $e->getMessage());
            header('Location: AreaComunControlador.php?action=listarAreas&error=Error+al+cancelar+la+reserva');
            exit;
        }
    }

    // Marcar reserva como pendiente
    public function pendienteReserva() {
        try {
            if (!isset($_POST['id_area']) || !isset($_POST['fecha_reserva']) || !isset($_POST['hora_inicio'])) {
                header('Location: AreaComunControlador.php?action=listarAreas&error=Datos+incompletos+para+marcar+reserva+como+pendiente');
                exit;
            }

            $id_area = intval($_POST['id_area']);
            $fecha_reserva = $_POST['fecha_reserva'];
            $hora_inicio = $_POST['hora_inicio'];

            $resultado = $this->areamodelo->actualizarEstadoReserva($id_area, $fecha_reserva, $hora_inicio, 'pendiente');

            if ($resultado) {
                header('Location: AreaComunControlador.php?action=listarAreas&success=Reserva+marcada+como+pendiente+correctamente');
            } else {
                header('Location: AreaComunControlador.php?action=listarAreas&error=Error+al+marcar+la+reserva+como+pendiente');
            }
            exit;

        } catch (Exception $e) {
            error_log("Error en pendienteReserva controlador: " . $e->getMessage());
            header('Location: AreaComunControlador.php?action=listarAreas&error=Error+al+marcar+la+reserva+como+pendiente');
            exit;
        }
    }


}

// Manejo de rutas GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    require_once '../../config/database.php';
    require_once '../modelo/AreaComunModelo.php';

    $database = new Database();
    $db = $database->getConnection();
    $controlador = new AreaComunControlador($db);

    if(isset($_GET['action'])) {
        switch($_GET['action']) {
            case 'listarAreas':
                $controlador->listarAreas();
                break;
            case 'formularioArea':
                $controlador->formularioArea();
                break;
            case 'formularioReservaArea':
                $controlador->formularioReservaArea();
                break;
            case 'eliminarArea':
                $controlador->eliminarArea();
                break;
            case 'obtenerReservasPorFecha':
                $controlador->obtenerReservasPorFecha();
                break;
            case 'listarReservasArea':
                $controlador->listarReservasArea();
                break;
            default:
                header('Location: ../vista/DashboardVista.php?error=Accion+no+valida');
                exit;
        }
    } else {
        // Accion por defecto
        $controlador->listarAreas();
    }
}

// Manejo de rutas POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    require_once '../../config/database.php';
    require_once '../modelo/AreaComunModelo.php';

    $database = new Database();
    $db = $database->getConnection();
    $controlador = new AreaComunControlador($db);

    switch($_POST['action']) {
        case 'registrarArea':
            $controlador->registrarArea();
            break;
        case 'editarArea':
            $controlador->editarArea();
            break;
        case 'confirmarReserva':
            $controlador->confirmarReserva();
            break;
        case 'cancelarReserva':
            $controlador->cancelarReserva();
            break;
        case 'pendienteReserva':
            $controlador->pendienteReserva();
            break;
        default:
            header('Location: ../vista/DashboardVista.php?error=Accion+no+valida');
            exit;
    }
}
?>
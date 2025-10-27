<?php

class CargosFijosControlador {
    private $modelo;

    public function __construct($db) {
        $this->modelo = new CargosFijosModelo($db);
    }

    // Métodos para vistas
    public function listarCargosVista() {
        try {
            $cargos = $this->modelo->obtenerCargosFijos();
            $estadisticas = $this->modelo->obtenerEstadisticasCargos();

            include_once "../vista/ListarCargosFijosVista.php";
        } catch (Exception $e) {
            error_log("Error en listarCargosVista: " . $e->getMessage());
            header('Location: ../vista/DashboardVista.php?error=Error+al+cargar+los+cargos+fijos');
            exit;
        }
    }

    public function formularioCrearCargo() {
        include_once "../vista/RegistrarCargoFijoVista.php";
    }

    public function formularioEditarCargo() {
        try {
            if (!isset($_GET['id_cargo']) || empty($_GET['id_cargo'])) {
                header('Location: CargosFijosControlador.php?action=listarCargosVista&error=ID+de+cargo+no+especificado');
                exit;
            }

            $id_cargo = intval($_GET['id_cargo']);
            $cargo = $this->modelo->obtenerCargoPorId($id_cargo);

            if (!$cargo) {
                header('Location: CargosFijosControlador.php?action=listarCargosVista&error=Cargo+no+encontrado');
                exit;
            }

            include_once "../vista/EditarCargoFijoVista.php";
        } catch (Exception $e) {
            error_log("Error en formularioEditarCargo: " . $e->getMessage());
            header('Location: CargosFijosControlador.php?action=listarCargosVista&error=Error+al+cargar+el+formulario+de+edición');
            exit;
        }
    }

    public function vistaGenerarConceptos() {
        try {
            $cargos_activos = $this->modelo->obtenerCargosActivos();
            $ultima_generacion = $this->modelo->obtenerUltimaGeneracionConceptos();
            $estadisticas = $this->modelo->obtenerEstadisticasCargos();

            include_once "../vista/RegistrarConceptosMantenimientoVista.php";
        } catch (Exception $e) {
            error_log("Error en vistaGenerarConceptos: " . $e->getMessage());
            header('Location: CargosFijosControlador.php?action=listarCargosVista&error=Error+al+cargar+la+vista+de+generación+de+conceptos');
            exit;
        }
    }

    // Métodos para gestión de cargos
    public function crearCargo() {
        try {
            if (empty($_POST['nombre_cargo']) || empty($_POST['monto'])) {
                header('Location: CargosFijosControlador.php?action=formularioCrearCargo&error=Nombre+y+monto+son+obligatorios');
                exit;
            }

            $nombre_cargo = trim($_POST['nombre_cargo']);
            $monto = floatval($_POST['monto']);
            $descripcion = trim($_POST['descripcion'] ?? '');
            $estado = $_POST['estado'] ?? 'activo';

            if ($monto <= 0) {
                header('Location: CargosFijosControlador.php?action=formularioCrearCargo&error=El+monto+debe+ser+mayor+a+0');
                exit;
            }

            $resultado = $this->modelo->crearCargo($nombre_cargo, $monto, $descripcion, $estado);

            if ($resultado) {
                header('Location: CargosFijosControlador.php?action=listarCargosVista&success=Cargo+creado+correctamente');
            } else {
                header('Location: CargosFijosControlador.php?action=formularioCrearCargo&error=Error+al+crear+el+cargo');
            }
            exit;
        } catch (Exception $e) {
            error_log("Error en crearCargo controlador: " . $e->getMessage());
            header('Location: CargosFijosControlador.php?action=formularioCrearCargo&error=Error+al+crear+el+cargo');
            exit;
        }
    }

    public function editarCargo() {
        try {
            if (empty($_POST['id_cargo']) || empty($_POST['nombre_cargo']) || empty($_POST['monto'])) {
                header('Location: CargosFijosControlador.php?action=listarCargosVista&error=Todos+los+campos+son+obligatorios');
                exit;
            }

            $id_cargo = intval($_POST['id_cargo']);
            $nombre_cargo = trim($_POST['nombre_cargo']);
            $monto = floatval($_POST['monto']);
            $descripcion = trim($_POST['descripcion'] ?? '');
            $estado = $_POST['estado'] ?? 'activo';

            if ($monto <= 0) {
                header('Location: CargosFijosControlador.php?action=listarCargosVista&error=El+monto+debe+ser+mayor+a+0');
                exit;
            }

            $resultado = $this->modelo->actualizarCargo($id_cargo, $nombre_cargo, $monto, $descripcion, $estado);

            if ($resultado) {
                header('Location: CargosFijosControlador.php?action=listarCargosVista&success=Cargo+actualizado+correctamente');
            } else {
                header('Location: CargosFijosControlador.php?action=listarCargosVista&error=Error+al+actualizar+el+cargo');
            }
            exit;
        } catch (Exception $e) {
            error_log("Error en editarCargo controlador: " . $e->getMessage());
            header('Location: CargosFijosControlador.php?action=listarCargosVista&error=Error+al+actualizar+el+cargo');
            exit;
        }
    }

    public function eliminarCargo() {
        try {
            if (!isset($_POST['id_cargo'])) {
                header('Location: CargosFijosControlador.php?action=listarCargosVista&error=ID+de+cargo+no+especificado');
                exit;
            }

            $id_cargo = intval($_POST['id_cargo']);

            // Verificar si el cargo está siendo usado en conceptos
            $enUso = $this->modelo->verificarCargoEnUso($id_cargo);
            if ($enUso) {
                header('Location: CargosFijosControlador.php?action=listarCargosVista&error=No+se+puede+eliminar+el+cargo+porque+está+siendo+usado+en+conceptos+existentes');
                exit;
            }

            $resultado = $this->modelo->eliminarCargo($id_cargo);

            if ($resultado) {
                header('Location: CargosFijosControlador.php?action=listarCargosVista&success=Cargo+eliminado+correctamente');
            } else {
                header('Location: CargosFijosControlador.php?action=listarCargosVista&error=Error+al+eliminar+el+cargo');
            }
            exit;
        } catch (Exception $e) {
            error_log("Error en eliminarCargo controlador: " . $e->getMessage());
            header('Location: CargosFijosControlador.php?action=listarCargosVista&error=Error+al+eliminar+el+cargo');
            exit;
        }
    }

    public function cambiarEstadoCargo() {
        try {
            if (!isset($_POST['id_cargo']) || !isset($_POST['estado'])) {
                header('Location: CargosFijosControlador.php?action=listarCargosVista&error=ID+y+estado+son+obligatorios');
                exit;
            }

            $id_cargo = intval($_POST['id_cargo']);
            $estado = $_POST['estado'];
            $estados_validos = ['activo', 'inactivo'];

            if (!in_array($estado, $estados_validos)) {
                header('Location: CargosFijosControlador.php?action=listarCargosVista&error=Estado+no+válido');
                exit;
            }

            $resultado = $this->modelo->cambiarEstadoCargo($id_cargo, $estado);

            if ($resultado) {
                header('Location: CargosFijosControlador.php?action=listarCargosVista&success=Estado+del+cargo+actualizado+correctamente');
            } else {
                header('Location: CargosFijosControlador.php?action=listarCargosVista&error=Error+al+cambiar+el+estado+del+cargo');
            }
            exit;
        } catch (Exception $e) {
            error_log("Error en cambiarEstadoCargo controlador: " . $e->getMessage());
            header('Location: CargosFijosControlador.php?action=listarCargosVista&error=Error+al+cambiar+el+estado+del+cargo');
            exit;
        }
    }

    // Métodos para generación de conceptos
    public function generarConceptosMantenimiento() {
        try {
            $mes = $_POST['mes'] ?? date('Y-m');
            $year = $_POST['year'] ?? date('Y');

            if (!preg_match('/^\d{4}-\d{2}$/', $mes)) {
                header('Location: CargosFijosControlador.php?action=vistaGenerarConceptos&error=Formato+de+mes+no+válido');
                exit;
            }

            // Verificar si ya se generaron conceptos para este mes
            if ($this->modelo->verificarConceptosGenerados($year, $mes)) {
                header('Location: CargosFijosControlador.php?action=vistaGenerarConceptos&error=Ya+se+generaron+conceptos+de+mantenimiento+para+este+mes');
                exit;
            }

            $resultado = $this->modelo->generarConceptosMantenimiento($year, $mes);

            if ($resultado['success']) {
                $mensaje = "Se generaron {$resultado['total_conceptos']} conceptos de mantenimiento para {$resultado['departamentos']} departamentos. Total: Bs. " . number_format($resultado['total_monto'], 2);
                header('Location: CargosFijosControlador.php?action=vistaGenerarConceptos&success=' . urlencode($mensaje));
            } else {
                header('Location: CargosFijosControlador.php?action=vistaGenerarConceptos&error=Error+al+generar+los+conceptos+de+mantenimiento');
            }
            exit;
        } catch (Exception $e) {
            error_log("Error en generarConceptosMantenimiento controlador: " . $e->getMessage());
            header('Location: CargosFijosControlador.php?action=vistaGenerarConceptos&error=Error+al+generar+los+conceptos+de+mantenimiento');
            exit;
        }
    }

    // Método para API/AJAX (manteniendo compatibilidad)
    public function obtenerCargosAjax() {
        try {
            $cargos = $this->modelo->obtenerCargosFijos();
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $cargos,
                'message' => 'Cargos obtenidos correctamente'
            ]);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener cargos: ' . $e->getMessage()
            ]);
        }
    }
}

// Manejo de rutas (similar a AreaComunControlador)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    require_once '../../config/database.php';
    require_once '../modelo/CargosFijosModelo.php';

    $database = new Database();
    $db = $database->getConnection();
    $controlador = new CargosFijosControlador($db);

    if (isset($_GET['action'])) {
        switch ($_GET['action']) {
            case 'listarCargosVista':
                $controlador->listarCargosVista();
                break;
            case 'formularioCrearCargo':
                $controlador->formularioCrearCargo();
                break;
            case 'formularioEditarCargo':
                $controlador->formularioEditarCargo();
                break;
            case 'vistaGenerarConceptos':
                $controlador->vistaGenerarConceptos();
                break;
            case 'obtenerCargosAjax':
                $controlador->obtenerCargosAjax();
                break;
            default:
                header('Location: ../vista/DashboardVista.php?error=Accion+no+valida+get');
                exit;
        }
    } else {
        $controlador->listarCargosVista();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../../config/database.php';
    require_once '../modelo/CargosFijosModelo.php';

    $database = new Database();
    $db = $database->getConnection();
    $controlador = new CargosFijosControlador($db);

    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'crearCargo':
                $controlador->crearCargo();
                break;
            case 'editarCargo':
                $controlador->editarCargo();
                break;
            case 'eliminarCargo':
                $controlador->eliminarCargo();
                break;
            case 'cambiarEstadoCargo':
                $controlador->cambiarEstadoCargo();
                break;
            case 'generarConceptosMantenimiento':
                $controlador->generarConceptosMantenimiento();
                break;
            default:
                header('Location: ../vista/DashboardVista.php?error=Accion+no+valida+post');
                exit;
        }
    }
}
?>
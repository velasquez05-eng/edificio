<?php
// Activar errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

class DepartamentoControlador {
    private $departamentoModelo;

    public function __construct($db) {
        $this->departamentoModelo = new DepartamentoModelo($db);
    }

    public function mostrarVista() {
        $departamentos = $this->departamentoModelo->obtenerDepartamentoModelo();
        include_once '../vista/ListarDepartamentoVista.php';
    }

    // NUEVO MÉTODO: Mostrar vista de registro
    public function mostrarVistaRegistro() {
        include_once '../vista/RegistrarDepartamentoVista.php';
    }

    public function actualizarDepartamento() {
        if ($_POST['action'] === 'actualizar') {
            $id_departamento = $_POST['id_departamento'] ?? '';
            $numero = $_POST['numero'] ?? '';
            $piso = $_POST['piso'] ?? '';

            // Validaciones
            if (empty($id_departamento) || empty($numero) || empty($piso)) {
                $this->redirigirConError("Todos los campos son obligatorios");
                return;
            }

            if (!is_numeric($piso) || $piso < 0 || $piso > 50) {
                $this->redirigirConError("El piso debe ser un número entre 0 y 50");
                return;
            }

            // Actualizar departamento
            try {
                $resultado = $this->departamentoModelo->actualizarDepartamentoModelo($id_departamento, $numero, $piso);
                
                if ($resultado) {
                    $this->redirigirConExito("Departamento actualizado correctamente");
                } else {
                    $this->redirigirConError("Error al actualizar el departamento - No se pudo ejecutar la consulta");
                }
            } catch (Exception $e) {
                $this->redirigirConError("Error en base de datos: " . $e->getMessage());
            }
        }
    }

    public function registrarDepartamento() {
        if ($_POST['action'] === 'registrar') {
            $numero = $_POST['numero'] ?? '';
            $piso = $_POST['piso'] ?? '';
            $id_edificio = $_POST['id_edificio'] ?? '';

            // Validaciones
            if (empty($numero) || empty($piso)) {
                $this->redirigirConError("Todos los campos son obligatorios");
                return;
            }

            if (!is_numeric($piso) || $piso < 0 || $piso > 50) {
                $this->redirigirConError("El piso debe ser un número entre 0 y 50");
                return;
            }

            // Registrar departamento
            try {
                $resultado = $this->departamentoModelo->registrarDepartamentoModelo($numero, $piso,$id_edificio);
                
                if ($resultado) {
                    $this->redirigirConExito("Departamento registrado correctamente");
                } else {
                    $this->redirigirConError("Error al registrar el departamento - Puede que ya exista un departamento con ese número");
                }
            } catch (Exception $e) {
                $this->redirigirConError("Error en base de datos: " . $e->getMessage());
            }
        }
    }

    private function redirigirConExito($mensaje) {
        header('Location: DepartamentoControlador.php?accion=listar&success=' . urlencode($mensaje));
        exit;
    }

    private function redirigirConError($mensaje) {
        header('Location: DepartamentoControlador.php?accion=listar&error=' . urlencode($mensaje));
        exit;
    }
}

// PROCESAR SOLICITUD
try {
    // Solicitud GET para mostrar la vista principal
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'listar') {
        
        require_once '../../config/database.php';
        require_once '../modelo/DepartamentoModelo.php';
        
        $database = new Database();
        $db = $database->getConnection();
        $controlador = new DepartamentoControlador($db);
        $controlador->mostrarVista();
        exit;
    }

    // NUEVA SOLICITUD GET: Mostrar formulario de registro
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'registrar') {
        
        require_once '../../config/database.php';
        require_once '../modelo/DepartamentoModelo.php';
        
        $database = new Database();
        $db = $database->getConnection();
        $controlador = new DepartamentoControlador($db);
        $controlador->mostrarVistaRegistro();
        exit;
    }

    // Solicitud POST para actualizar
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'actualizar') {
        
        require_once '../../config/database.php';
        require_once '../modelo/DepartamentoModelo.php';
        
        $database = new Database();
        $db = $database->getConnection();
        $controlador = new DepartamentoControlador($db);
        $controlador->actualizarDepartamento();
        exit;
    }

    // Solicitud POST para registrar
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'registrar') {
        
        require_once '../../config/database.php';
        require_once '../modelo/DepartamentoModelo.php';
        
        $database = new Database();
        $db = $database->getConnection();
        $controlador = new DepartamentoControlador($db);
        $controlador->registrarDepartamento();
        exit;
    }

} catch (Exception $e) {
    die("Error en controlador: " . $e->getMessage());
}
?>
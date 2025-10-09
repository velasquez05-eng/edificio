<?php
class PersonaControlador{
    private $personamodelo;
    private $rolmodelo;

    public function __construct($db){
        $this->personamodelo = new PersonaModelo($db);
        $this->rolmodelo = new RolModelo($db);
    }

    public function listarPersonal(){
        $personal = $this->personamodelo->listarPersonal();
        include '../vista/ListarPersonalVista.php';
    }

    public function listarResidente(){
        $residentes = $this->personamodelo->listarResidente();
        include '../vista/ListarResidenteVista.php';
    }

    public function registrarPersona(){
        if ($_POST['action']=="registrar") {
            // Validar campos requeridos
            $camposRequeridos = ['nombre', 'apellido_paterno', 'ci', 'telefono', 'email', 'id_rol'];
            foreach($camposRequeridos as $campo) {
                if(!isset($_POST[$campo]) || empty(trim($_POST[$campo]))) {
                    $this->redirigirConError("El campo $campo es obligatorio");
                }
            }

            // Sanitizar datos
            $nombre = htmlspecialchars(trim($_POST['nombre']));
            $apellido_paterno = htmlspecialchars(trim($_POST['apellido_paterno']));
            $apellido_materno = isset($_POST['apellido_materno']) ? htmlspecialchars(trim($_POST['apellido_materno'])) : '';
            $ci = htmlspecialchars(trim($_POST['ci']));
            $telefono = htmlspecialchars(trim($_POST['telefono']));
            $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
            $id_rol = intval($_POST['id_rol']);

            // Validaciones adicionales
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->redirigirConError("El formato del email no es válido");
            }

            // VERIFICAR SI EL CI YA EXISTE
            $ciExistente = $this->personamodelo->verificarCIExistente($ci);
            if ($ciExistente === true) {
                $this->redirigirConError("El número de CI ya está registrado en el sistema");
            } elseif ($ciExistente === false) {
                // CI no existe, continuar
            } else {
                // Error en la verificación
                $this->redirigirConError("Error al verificar el CI");
            }

            // VERIFICAR SI EL EMAIL YA EXISTE
            if ($this->personamodelo->verificarEmailExistente($email)) {
                $this->redirigirConError("El email ya está registrado en el sistema");
            }

            try {
                $rol = $this->rolmodelo->obtenerRol($id_rol);
                $resultado = $this->personamodelo->registrarPersona($nombre, $apellido_paterno,$apellido_materno,$ci,$telefono,$email,$id_rol);
                if($resultado){
                    $this->redirigirConExito("Persona registrada exitosamente como ".$rol['rol']);
                }else{
                    $this->redirigirConError("Error al registrar persona - No se pudo ejecutar la consulta");
                }
            } catch (Exception $e) {
                $this->redirigirConError("Error en base de datos: ".$e->getMessage());
            }
        }
    }

    private function redirigirConExito($mensaje) {
        header('Location: ../vista/RegistrarPersonaVista.php?success=' . urlencode($mensaje));
        exit;
    }

    private function redirigirConError($mensaje) {
        header('Location: ../vista/RegistrarPersonaVista.php?error=' . urlencode($mensaje));
        exit;
    }
}

// Manejo de rutas
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    include_once "../../config/database.php";
    include_once "../modelo/PersonaModelo.php";
    include_once "../modelo/RolModelo.php";

    $database = new Database();
    $db = $database->getConnection();
    $controlador = new PersonaControlador($db);

    if(isset($_GET['action'])) {
        switch($_GET['action']) {
            case 'listarPersonal':
                $controlador->listarPersonal();
                break;
            case 'listarResidente':
                $controlador->listarResidente();
                break;
            default:
                header('Location: ../vista/RegistrarPersonaVista.php?error=Acción no válida');
                exit;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'registrar') {
    require_once '../../config/database.php';
    require_once '../modelo/PersonaModelo.php';
    require_once '../modelo/RolModelo.php';

    $database = new Database();
    $db = $database->getConnection();
    $controlador = new PersonaControlador($db);
    $controlador->registrarPersona();
    exit;
}
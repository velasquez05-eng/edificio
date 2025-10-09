<?php
class PersonaControlador{
    private $personamodelo;
    public function __construct($db){
        $this->personamodelo = new PersonaModelo($db);
    }
    public function listarPersonal(){
        $personal = $this->personamodelo->listarPersonal();
        include '../vista/ListarPersonalVista.php';
    }
    public function registrarPersona(){
        if ($_POST['action']=="registrar") {
            $nombre = $_POST['nombre'];
            $apellido_paterno = $_POST['apellido_paterno'];
            $apellido_materno = $_POST['apellido_materno'];
            $ci = $_POST['ci'];
            $telefono = $_POST['telefono'];
            $email = $_POST['email'];
            $id_rol = $_POST['id_rol'];
            if (empty($nombre) || empty($apellido_paterno) || empty($ci) || empty($telefono) || empty($email) || empty($id_rol)) {
                $this->redirigirConErrorPersona("Todos los campos son obligatorios");
            }

            try{
                $resultado=$this->personamodelo->registrarPersona($nombre, $apellido_paterno, $apellido_materno, $ci, $telefono, $email,$id_rol);
                if($resultado){
                    $this->redirigirConExitoPersona("Persona registrada exitosamente");
                }else{
                    $this->redirigirConErrorPersona("Error al registrar la personal - No se pudo ejecutar la consulta");
                }
            }catch (Exception $e){
                $this->redirigirConErrorPersona("Error en base de datos ".$e->getMessage());
            }

        }
    }
    private function redirigirConExitoPersona($mensaje) {
        header('Location: PersonaControlador.php?action=listarPersonal&success=' . urlencode($mensaje));
        exit;
    }

    private function redirigirConErrorPersona($mensaje) {
        header('Location: PersonaControlador.php?action=listarPersonal&error=' . urlencode($mensaje));
        exit;
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'listarPersonal') {
    include_once "../../config/database.php";
    include_once "../modelo/PersonaModelo.php";
    $database = new Database();
    $db = $database->getConnection();
    $controlador = new PersonaControlador($db);
    $controlador->listarPersonal();
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'registrar') {

    require_once '../../config/database.php';
    require_once '../modelo/PersonaModelo.php';

    $database = new Database();
    $db = $database->getConnection();
    $controlador = new PersonaControlador($db);
    $controlador->registrarPersona();
    exit;
}
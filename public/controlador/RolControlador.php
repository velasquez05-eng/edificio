<?php

    class RolControlador{
        private $rolmodelo;

        public function __construct($db){
            $this->rolmodelo = new RolModelo($db);
        }
        public function listarRol(){
            $roles=$this->rolmodelo->listarRoles();
            include_once "../vista/ListarRolVista.php";
        }

        public function registrarRol(){
            if($_POST['action']=="registrar"){
                $rol=$_POST['rol'];
                $descripcion=$_POST['descripcion'];

                if(empty($_POST['rol']) || empty($_POST['descripcion'])){
                    $this->redirigirConError("Todos los campos son obligatorios");
                    return;
                }

                try{
                    $resultado=$this->rolmodelo->registrarRol($rol,$descripcion);
                    if ($resultado) {
                        $this->redirigirConExito("Rol creado Exitosamente");
                    } else {
                        $this->redirigirConError("Error al crear el Rol - No se pudo ejecutar la consulta");
                    }
                }catch (Exception $e){
                    $this->redirigirConError("Error en base de datos: " . $e->getMessage());
                }
            }
        }

        public function editarRol(){
            if($_POST['action']=="editar"){
                $rol=$_POST['rol'];
                $descripcion=$_POST['descripcion'];
                $id_rol=$_POST['id_rol'];
                if(empty($_POST['rol']) || empty($_POST['descripcion'])){
                    $this->redirigirConError("Todos los datos son obligatorios");
                    return;
                }

                try {
                    $resultado=$this->rolmodelo->editarRol($id_rol,$rol,$descripcion);
                    if ($resultado) {
                        $this->redirigirConExito("Rol Editado Exitosamente");
                    }else{
                        $this->redirigirConError("Error al editar el rol - No se pudo ejecutar la consulta");
                    }
                }catch (Exception $e){
                    $this->redirigirConError("Error en base de datos: " . $e->getMessage());
                }
            }
        }
        private function redirigirConExito($mensaje) {
            header('Location: RolControlador.php?action=listar&success=' . urlencode($mensaje));
            exit;
        }

        private function redirigirConError($mensaje) {
            header('Location: RolControlador.php?action=listar&error=' . urlencode($mensaje));
            exit;
        }
    }

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'listar') {
    include_once "../../config/database.php";
    include_once "../modelo/RolModelo.php";
    $database = new Database();
    $db = $database->getConnection();
    $controlador = new RolControlador($db);
    $controlador->listarRol();
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'registrar') {

    require_once '../../config/database.php';
    require_once '../modelo/RolModelo.php';

    $database = new Database();
    $db = $database->getConnection();
    $controlador = new RolControlador($db);
    $controlador->registrarRol();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'editar') {
    require_once '../../config/database.php';
    require_once '../modelo/RolModelo.php';
    $database = new Database();
    $db = $database->getConnection();
    $controlador = new RolControlador($db);
    $controlador->editarRol();
    exit;
}
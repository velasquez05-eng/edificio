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
    public function formularioRol(){
        include_once "../vista/RegistrarRolVista.php";
    }

    public function registrarRol(){
        if($_POST['action']=="registrar"){
            $rol=$_POST['rol'];
            $descripcion=$_POST['descripcion'];
            $salario_base=$_POST['salario_base'];

            if(empty($_POST['rol']) || empty($_POST['descripcion']) || empty($_POST['salario_base'])){
                $this->redirigirConError("Todos los campos son obligatorios");
                return;
            }

            // Validar que salario_base sea un número válido
            if(!is_numeric($salario_base) || $salario_base < 0){
                $this->redirigirConError("El salario base debe ser un número válido mayor o igual a 0");
                return;
            }

            try{
                $resultado=$this->rolmodelo->registrarRol($rol,$descripcion,$salario_base);
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
            $salario_base=$_POST['salario_base'];
            $id_rol=$_POST['id_rol'];

            if(empty($_POST['rol']) || empty($_POST['descripcion']) || empty($_POST['salario_base'])){
                $this->redirigirConError("Todos los datos son obligatorios");
                return;
            }

            // Validar que salario_base sea un número válido
            if(!is_numeric($salario_base) || $salario_base < 0){
                $this->redirigirConError("El salario base debe ser un número válido mayor o igual a 0");
                return;
            }

            try {
                $resultado=$this->rolmodelo->editarRol($id_rol,$rol,$descripcion,$salario_base);
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


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    include_once "../../config/database.php";
    include_once "../modelo/RolModelo.php";
    $database = new Database();
    $db = $database->getConnection();
    $controlador = new RolControlador($db);

    switch ($_GET['action'] ?? '') {
        case 'listar':
            $controlador->listarRol();
            break;
        case 'formularioRol':
            $controlador->formularioRol();
            break;

        default:
            header('Location: ../vista/DashboardVista.php?error=Acción no válida');
            exit;
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../../config/database.php';
    require_once '../modelo/RolModelo.php';

    $database = new Database();
    $db = $database->getConnection();
    $controlador = new RolControlador($db);

    switch ($_POST['action'] ?? '') {
        case 'registrar':
            $controlador->registrarRol();
            break;

        case 'editar':
            $controlador->editarRol();
            break;

        default:
            header('Location: ../vista/DashboardVista.php?error=Acción no válida');
            exit;
    }
    exit;
}
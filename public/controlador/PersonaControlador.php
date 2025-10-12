<?php
class PersonaControlador{
    private $personamodelo;
    private $rolmodelo;
    private $correomodelo;

    public function __construct($db){
        $this->personamodelo = new PersonaModelo($db);
        $this->rolmodelo = new RolModelo($db);
        $this->correomodelo = new CorreoModelo();
    }

    public function listarPersonal(){
        $personal = $this->personamodelo->listarPersonal();
        include '../vista/ListarPersonalVista.php';
    }

    public function listarResidente(){
        $residentes = $this->personamodelo->listarResidente();
        include '../vista/ListarResidenteVista.php';
    }
    public function formularioPersona(){
        include '../vista/RegistrarPersonaVista.php';
    }
    public function registrarPersona(){
        if ($_POST['action']=="registrar") {
            // Validar campos requeridos
            $camposRequeridos = ['nombre', 'apellido_paterno', 'ci', 'telefono', 'email', 'username', 'password', 'id_rol'];
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
            $username = htmlspecialchars(trim($_POST['username']));
            $password = $_POST['password'];
            $id_rol = intval($_POST['id_rol']);

            // Validaciones adicionales
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->redirigirConError("El formato del email no es válido");
            }

            // Validar fortaleza de la contraseña
            if (strlen($password) < 8) {
                $this->redirigirConError("La contraseña debe tener al menos 8 caracteres");
            }

            // Validar formato de username (solo letras, números y guiones bajos)
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
                $this->redirigirConError("El username solo puede contener letras, números y guiones bajos");
            }

            // VERIFICAR SI EL CI YA EXISTE
            if ($this->personamodelo->verificarCIExistente($ci)) {
                $this->redirigirConError("El número de CI ya está registrado en el sistema");
            }

            // VERIFICAR SI EL EMAIL YA EXISTE
            //if ($this->personamodelo->verificarEmailExistente($email)) {
             //   $this->redirigirConError("El email ya está registrado en el sistema");
            //}

            // VERIFICAR SI EL USERNAME YA EXISTE
            if ($this->personamodelo->verificarUsuarioExistente($username)) {
                $this->redirigirConError("El username ya está registrado en el sistema");
            }

            try {
                $rol = $this->rolmodelo->obtenerRol($id_rol);
                $resultado = $this->personamodelo->registrarPersona($nombre,$apellido_paterno,$apellido_materno,$ci,$telefono,$email,$username,$password,$id_rol);
                if($resultado){
                    $this->correomodelo->notificarCredenciales($email,$nombre." ".$apellido_paterno." ".$apellido_materno,$username,$password);
                    $this->redirigirConExito("Persona registrada exitosamente como ".$rol['rol'] . ". Tiene 3 días para verificar su cuenta.");
                }else{
                    $this->redirigirConError("Error al registrar persona - No se pudo ejecutar la consulta");
                }
            } catch (Exception $e) {
                $this->redirigirConError("Error en base de datos: ".$e->getMessage());
            }
        }
    }
    private function redirigirConExito($mensaje) {
        header('Location: ../controlador/PersonaControlador.php?action=formularioPersona&&success=' . urlencode($mensaje));
        exit;
    }

    private function redirigirConError($mensaje) {
        header('Location: ../vista/RegistrarPersonaVista.php?error=' . urlencode($mensaje));
        exit;
    }
    public function editarPersona(){
        if ($_POST['action']=="editarPersona") {
            // Validar campos requeridos
            $camposRequeridos = ['id_persona','nombre', 'apellido_paterno', 'telefono', 'email', 'id_rol'];
            $id_rol = intval($_POST['id_rol']);
            $rol = $this->rolmodelo->obtenerRol($id_rol);

            foreach($camposRequeridos as $campo) {
                if(!isset($_POST[$campo]) || empty(trim($_POST[$campo]))) {
                    $this->redirigirEdicionConError("El campo $campo es obligatorio", $id_rol);
                }
            }

            // Sanitizar datos
            $id_persona = intval($_POST['id_persona']);
            $nombre = htmlspecialchars(trim($_POST['nombre']));
            $apellido_paterno = htmlspecialchars(trim($_POST['apellido_paterno']));
            $apellido_materno = isset($_POST['apellido_materno']) ? htmlspecialchars(trim($_POST['apellido_materno'])) : '';
            $telefono = htmlspecialchars(trim($_POST['telefono']));
            $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);


            // VERIFICAR SI EL EMAIL YA EXISTE (excluyendo la persona actual)
            if ($this->personamodelo->verificarEmail( $id_persona,$email)) {
                $this->redirigirEdicionConError("El email ya está registrado en el sistema", $id_rol);
            }

            try {
                $resultado = $this->personamodelo->editarPersona($id_persona, $nombre, $apellido_paterno, $apellido_materno, $telefono, $email, $id_rol);

                if($resultado){
                    $this->redirigirEdicionConExito("Persona editada exitosamente con el rol de ".$rol['rol'], $id_rol);
                } else {
                    $this->redirigirEdicionConError("Error al editar persona - No se pudo ejecutar la consulta", $id_rol);
                }
            } catch (Exception $e) {
                $this->redirigirEdicionConError("Error en base de datos: ".$e->getMessage(), $id_rol);
            }
        }
    }

// Métodos auxiliares para redirección
    private function redirigirEdicionConExito($mensaje, $id_rol) {
        $rol = $this->rolmodelo->obtenerRol($id_rol);
        if ($rol['rol'] == "Residente") {
            header('Location: ../controlador/PersonaControlador.php?action=listarResidente&&success=' . urlencode($mensaje));
        } else {
            header('Location: ../controlador/PersonaControlador.php?action=listarPersonal&&success=' . urlencode($mensaje));
        }
        exit;
    }

    private function redirigirEdicionConError($mensaje, $id_rol) {
        $rol = $this->rolmodelo->obtenerRol($id_rol);
        if ($rol['rol'] == "Residente") {
            header('Location: ../controlador/PersonaControlador.php?action=listarResidente&&error=' . urlencode($mensaje));
        } else {
            header('Location: ../controlador/PersonaControlador.php?action=listarPersonal&&error=' . urlencode($mensaje));
        }
        exit;
    }

    //actualizar contraseña
    public function restablecerPassword(){
        if ($_POST['action']=="restablecerPassword") {

            $camposRequeridos = ['id_persona',"nombrepassword","email_password",'ci_password','id_rol'];
            $id_rol = intval($_POST['id_rol']);

            foreach($camposRequeridos as $campo) {
                if(!isset($_POST[$campo]) || empty(trim($_POST[$campo]))) {
                    $this->redirigirEdicionConError("El campo $campo es obligatorio", $id_rol);
                }
            }

            // Sanitizar datos
            $id_persona = intval($_POST['id_persona']);
            $nombre= htmlspecialchars(trim($_POST['nombrepassword']));
            $email = htmlspecialchars(trim($_POST['email_password']));
            $password = $_POST['ci_password'];

            try {
                $resultado = $this->personamodelo->restablecerPassword($id_persona, $password);
                if($resultado){
                    $this->correomodelo->notificarRestablecimientoPassword($email, $nombre);
                    $rol = $this->rolmodelo->obtenerRol($id_rol);
                    $this->redirigirEdicionConExito("Contraseña restablecida exitosamente", $id_rol);
                } else {
                    $this->redirigirEdicionConError("Error al restablecer contraseña - No se pudo ejecutar la consulta", $id_rol);
                }
            } catch (Exception $e) {
                $this->redirigirEdicionConError("Error en base de datos: ".$e->getMessage(), $id_rol);
            }
        }
    }
    public function ampliarTiempoVerificacion()
    {
        if ($_POST['action']=="ampliarTiempoVerificacion") {

            $camposRequeridos = ['id_persona','tiempo_verificacion','id_rol_tiempo'];
            $id_rol = intval($_POST['id_rol_tiempo']);
            foreach($camposRequeridos as $campo) {
                if(!isset($_POST[$campo]) || empty(trim($_POST[$campo]))) {
                    $this->redirigirEdicionConError("El campo $campo es obligatorio", $id_rol);
                }
            }
            $id_persona = intval($_POST['id_persona']);
            $tiempo_verificacion = intval($_POST['tiempo_verificacion']);

            try {
                $resultado = $this->personamodelo->ampliarTiempoVerificacion($id_persona, $tiempo_verificacion);
                if($resultado){
                    $rol = $this->rolmodelo->obtenerRol($id_rol);
                    $this->redirigirEdicionConExito("Ampliacion de tiempo de verificacion realizada exitosamente", $id_rol);
                } else {
                    $this->redirigirEdicionConError("Error ampliacion de tiempo de verificacion - No se pudo ejecutar la consulta", $id_rol);
                }
            } catch (Exception $e) {
                $this->redirigirEdicionConError("Error en base de datos: ".$e->getMessage(), $id_rol);
            }
        }
    }

    public function eliminarPersona()
    {
        if ($_POST['action']=="eliminarPersona") {

            $camposRequeridos = ['id_persona','id_rol'];
            $id_rol = intval($_POST['id_rol']);
            foreach($camposRequeridos as $campo) {
                if(!isset($_POST[$campo]) || empty(trim($_POST[$campo]))) {
                    $this->redirigirEdicionConError("El campo $campo es obligatorio", $id_rol);
                }
            }
            $id_persona = intval($_POST['id_persona']);
            try {
                $resultado = $this->personamodelo->eliminarPersona($id_persona);
                if($resultado){
                    $rol = $this->rolmodelo->obtenerRol($id_rol);
                    $this->redirigirEdicionConExito("Eliminacion realizada exitosamente", $id_rol);
                } else {
                    $this->redirigirEdicionConError("Error al eliminar - No se pudo ejecutar la consulta", $id_rol);
                }
            } catch (Exception $e) {
                $this->redirigirEdicionConError("Error en base de datos: ".$e->getMessage(), $id_rol);
            }
        }
    }
}

// Manejo de rutas
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    include_once "../../config/database.php";
    include_once "../modelo/PersonaModelo.php";
    include_once "../modelo/RolModelo.php";
    require_once '../modelo/CorreoModelo.php';

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
            case 'formularioPersona':
                $controlador->formularioPersona();
                break;
            default:
                header('Location: ../vista/RegistrarPersonaVista.php?error=Acción no válida');
                exit;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    require_once '../../config/database.php';
    require_once '../modelo/PersonaModelo.php';
    require_once '../modelo/RolModelo.php';
    require_once '../modelo/CorreoModelo.php';

    $database = new Database();
    $db = $database->getConnection();
    $controlador = new PersonaControlador($db);

    switch($_POST['action']) {
        case 'registrar':
            $controlador->registrarPersona();
            break;
        case 'editarPersona':
            $controlador->editarPersona();
            break;
        case 'restablecerPassword':
            $controlador->restablecerPassword();
            break;
        case 'ampliarTiempoVerificacion':
            $controlador->ampliarTiempoVerificacion();
            break;
        case 'eliminarPersona':
            $controlador->eliminarPersona();
            break;
        default:
            header('Location: ../vista/DashboardVista.php?error=Acción no válida');
            exit;
    }
}
<?php
/**
 * PersonaControlador - Controlador para gestionar operaciones de personas
 * Incluye registro, edicion, eliminacion, login y gestion de roles
 */
class PersonaControlador{
    private $personamodelo;
    private $rolmodelo;
    private $correomodelo;

    /**
     * Constructor - Inicializa los modelos necesarios
     */
    public function __construct($db){
        $this->personamodelo = new PersonaModelo($db);
        $this->rolmodelo = new RolModelo($db);
        $this->correomodelo = new CorreoModelo();
    }

    // =============================================
    // METODOS DE VISUALIZACION
    // =============================================

    /**
     * Listar personal del sistema
     */
    public function listarPersonal(){
        $personal = $this->personamodelo->listarPersonal();
        include '../vista/ListarPersonalVista.php';
    }

    /**
     * Listar residentes del sistema
     */
    public function listarResidente(){
        $residentes = $this->personamodelo->listarResidente();
        include '../vista/ListarResidenteVista.php';
    }

    /**
     * Listar personas eliminadas
     */
    public function listarEliminados(){
        $personas = $this->personamodelo->listarEliminados();
        include '../vista/ListarPersonasEliminadasVista.php';
    }

    /**
     * Mostrar formulario de registro de persona
     */
    public function formularioPersona(){
        include '../vista/RegistrarPersonaVista.php';
    }

    /**
     * Mostrar formulario de login
     */
    public function verLogin(){
        include '../vista/LoginVista.php';
    }

    // =============================================
    // METODOS DE GESTION DE PERSONAS
    // =============================================

    /**
     * Registrar nueva persona en el sistema
     */
    public function registrarPersona(){
        if ($_POST['action']=="registrar") {
            // Validar campos requeridos
            $camposRequeridos = ['nombre', 'apellido_paterno', 'ci', 'telefono', 'email', 'username', 'password', 'id_rol'];
            foreach($camposRequeridos as $campo) {
                if(!isset($_POST[$campo]) || empty(trim($_POST[$campo]))) {
                    $this->redirigirConError("El campo $campo es obligatorio");
                }
            }

            // Sanitizar datos de entrada
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
                $this->redirigirConError("El formato del email no es valido");
            }

            // Validar fortaleza de la contrasena
            if (strlen($password) < 8) {
                $this->redirigirConError("La contrasena debe tener al menos 8 caracteres");
            }

            // Validar formato de username
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
                $this->redirigirConError("El username solo puede contener letras, numeros y guiones bajos");
            }

            // Verificar si el CI ya existe
            if ($this->personamodelo->verificarCIExistente($ci)) {
                $this->redirigirConError("El numero de CI ya esta registrado en el sistema");
            }

            // Verificar si el username ya existe
            if ($this->personamodelo->verificarUsuarioExistente($username)) {
                $this->redirigirConError("El username ya esta registrado en el sistema");
            }

            try {
                $rol = $this->rolmodelo->obtenerRol($id_rol);
                $resultado = $this->personamodelo->registrarPersona($nombre,$apellido_paterno,$apellido_materno,$ci,$telefono,$email,$username,$password,$id_rol);

                if($resultado){
                    $this->correomodelo->notificarCredenciales($email,$nombre." ".$apellido_paterno." ".$apellido_materno,$username,$password);
                    $this->redirigirConExito("Persona registrada exitosamente como ".$rol['rol'] . ". Tiene 3 dias para verificar su cuenta.");
                }else{
                    $this->redirigirConError("Error al registrar persona - No se pudo ejecutar la consulta");
                }
            } catch (Exception $e) {
                $this->redirigirConError("Error en base de datos: ".$e->getMessage());
            }
        }
    }

    /**
     * Editar informacion de persona existente
     */
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

            // Verificar si el email ya existe (excluyendo la persona actual)
            if ($this->personamodelo->verificarEmail($id_persona,$email)) {
                $this->redirigirEdicionConError("El email ya esta registrado en el sistema", $id_rol);
            }

            try {
                $resultado = $this->personamodelo->editarPersona($id_persona, $nombre, $apellido_paterno, $apellido_materno, $telefono, $email, $id_rol);

                if($resultado){
                    session_start();
                    // Actualizar datos de sesion si es el usuario logueado
                    if ($_SESSION['id_persona']==$id_persona) {
                        $_SESSION['nombre']=$nombre;
                        $_SESSION['apellido_paterno']=$apellido_paterno;
                        $_SESSION['apellido_materno']=$apellido_materno;
                        $_SESSION['telefono']=$telefono;
                        $_SESSION['email']=$email;
                        $_SESSION['id_rol']=$id_rol;
                        $_SESSION['rol_nombre'] = $rol['rol'];
                    }
                    $this->redirigirEdicionConExito("Persona editada exitosamente con el rol de ".$rol['rol'], $id_rol);
                } else {
                    $this->redirigirEdicionConError("Error al editar persona - No se pudo ejecutar la consulta", $id_rol);
                }
            } catch (Exception $e) {
                $this->redirigirEdicionConError("Error en base de datos: ".$e->getMessage(), $id_rol);
            }
        }
    }

    /**
     * Restablecer contrasena de usuario
     */
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
                    $rol = $this->rolmodelo->obtenerRol($id_rol);
                    $this->redirigirEdicionConExito("Contrasena restablecida exitosamente", $id_rol);
                } else {
                    $this->redirigirEdicionConError("Error al restablecer contrasena - No se pudo ejecutar la consulta", $id_rol);
                }
            } catch (Exception $e) {
                $this->redirigirEdicionConError("Error en base de datos: ".$e->getMessage(), $id_rol);
            }
        }
    }

        /**
     * Ampliar tiempo de verificacion de cuenta
     */

    public function cambiarContraseña(){
        if ($_POST['action']=="cambiarContraseña") {

            // Sanitizar datos
            $id_persona = intval($_POST['id_persona']);
            $password = $_POST['new_password'];

            try {
                $resultado = $this->personamodelo->cambiarPassword($id_persona, $password);
                if($resultado){
                        session_start();
                        $personaCompleta=$this->personamodelo->obtenerPersonaPorId($id_persona);
                        $rol = $this->rolmodelo->obtenerRol($personaCompleta['id_rol']);

                        // Guardar datos en sesion
                        $_SESSION['id_persona']=$personaCompleta['id_persona'];
                        $_SESSION['id_rol'] = $personaCompleta['id_rol'];
                        $_SESSION['rol_nombre'] = $rol['rol'];

                        // Datos personales
                        $_SESSION['nombre'] = $personaCompleta['nombre'];
                        $_SESSION['apellido_paterno'] = $personaCompleta['apellido_paterno'];
                        $_SESSION['apellido_materno'] = $personaCompleta['apellido_materno'] ?? '';
                        $_SESSION['username'] = $personaCompleta['username'];
                        $_SESSION['email'] = $personaCompleta['email'];
                        $_SESSION['telefono'] = $personaCompleta['telefono'];
                        $_SESSION['ci'] = $personaCompleta['ci'];

                        // Crear avatar para el header
                        $inicialNombre = strtoupper(substr($personaCompleta['nombre'], 0, 1));
                        $inicialApellido = strtoupper(substr($personaCompleta['apellido_paterno'], 0, 1));
                        $_SESSION['avatar'] = $inicialNombre . $inicialApellido;

                        // Redirigir segun el rol
                        switch ($_SESSION['id_rol']) {
                            case '1':
                                header("Location: ../controlador/DashboardControlador.php?action=mostrarDashboardAdministrador");
                                break;
                            case '2':
                                header("Location: ../controlador/DashboardControlador.php?action=mostrarDashboardResidente");
                                break;
                            default:
                                header("Location: ../controlador/DashboardControlador.php?action=mostrarDashboardPersonal");
                                break;
                        }
                        exit();
                } else {
                //    $this->redirigirEdicionConError("Error al restablecer contrasena - No se pudo ejecutar la consulta", $id_rol);
                }
            } catch (Exception $e) {
              //  $this->redirigirEdicionConError("Error en base de datos: ".$e->getMessage(), $id_rol);
            }
        }
    }
    /**
     * Ampliar tiempo de verificacion de cuenta
     */
    public function ampliarTiempoVerificacion(){
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

    /**
     * Eliminar persona (eliminacion logica)
     */
    public function eliminarPersona(){
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

    /**
     * Restaurar persona eliminada
     */
    public function restaurarPersona(){
        if ($_POST['action'] == "restaurarPersona") {
            $camposRequeridos = ['id_persona', 'id_rol'];
            $id_rol = intval($_POST['id_rol']);
            $id_persona = intval($_POST['id_persona']);

            try {
                $resultado = $this->personamodelo->restaurarPersona($id_persona);
                if ($resultado) {
                    $this->redirigirEdicionConExito("Restauracion realizada exitosamente", $id_rol);
                } else {
                    $this->redirigirEdicionConError("Error al restaurar - No se pudo ejecutar la consulta", $id_rol);
                }
            } catch (Exception $e) {
                $this->redirigirEdicionConError("Error en base de datos: " . $e->getMessage(), $id_rol);
            }
        }
    }

    // =============================================
    // METODOS DE AUTENTICACION
    // =============================================
    /**
     * Procesar inicio de sesión
     */
    public function login() {
        if ($_POST) {
            session_start();
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';

            // 1. Validar reCAPTCHA primero
            if (!$this->validarRecaptcha($recaptcha_response)) {
                header("Location: ../vista/LoginVista.php?error=Por favor, verifica que no eres un robot");
                exit();
            }

            // 2. Validar campos vacíos
            if (empty($username) || empty($password)) {
                header("Location: ../vista/LoginVista.php?error=Todos los campos son obligatorios");
                exit();
            }

            // 3. Verificar si el usuario está bloqueado temporalmente (ANTES del login)
            $bloqueoInfo = $this->personamodelo->verificarTiempoBloqueo($username);
            if ($bloqueoInfo['bloqueado']) {
                if ($bloqueoInfo['bloqueo_permanente']) {
                    $mensajeError = "Cuenta bloqueada permanentemente por seguridad. Contacte al administrador.";
                    header("Location: ../vista/LoginVista.php?error=" . urlencode($mensajeError));
                } else {
                    $mensajeError = "Cuenta temporalmente bloqueada. Tiempo restante: " . $bloqueoInfo['tiempo_restante'];
                    // Pasar el tiempo en segundos para el JavaScript
                    $tiempo_segundos = $bloqueoInfo['segundos_restantes'];
                    // Para debug
                    error_log("DEBUG BLOQUEO INICIAL - Tiempo: " . $tiempo_segundos . "s, Usuario: " . $username);
                    header("Location: ../vista/LoginVista.php?error=" . urlencode($mensajeError) . "&bloqueado=true&tiempo=" . $tiempo_segundos);
                }
                exit();
            }

            // 4. Resto de la lógica de login
            $user = $this->personamodelo->login($username, $password);

            if ($user) {
                // Login exitoso - registrar en historial y resetear bloqueo
                $this->personamodelo->successLogin($user['id_persona'], $username);

                $_SESSION['id_persona'] = $user['id_persona'];

                // PRIMERO: Verificar si el tiempo de verificación ha vencido
                if ($this->personamodelo->tiempoVerificacionVencido($user['id_persona'])) {
                    session_destroy();
                    header("Location: ../vista/LoginVista.php?error=Su tiempo para verificar su cuenta ha vencido");
                    exit();
                }

                // SEGUNDO: Verificar si la persona está verificada
                if (!$this->personamodelo->verificacionPersona($user['id_persona'])) {
                    // VERIFICAR SI ESTÁ BLOQUEADO ANTES DE MOSTRAR PANTALLA DE VERIFICACIÓN
                    $bloqueoInfo = $this->personamodelo->verificarTiempoBloqueoPorId($user['id_persona']);
                    if ($bloqueoInfo['bloqueado']) {
                        session_destroy();
                        if ($bloqueoInfo['bloqueo_permanente']) {
                            $mensajeError = "Cuenta bloqueada permanentemente. No puede verificar su cuenta.";
                        } else {
                            $mensajeError = "No puede verificar su cuenta mientras esté bloqueado. Tiempo restante: " . $bloqueoInfo['tiempo_restante'];
                        }
                        header("Location: ../vista/LoginVista.php?error=" . urlencode($mensajeError));
                        exit();
                    }

                    include_once "../vista/VerificacionVista.php";
                    exit();
                } else {
                    // Persona verificada y tiempo no vencido - proceder con el login
                    $rol = $this->rolmodelo->obtenerRol($user['id_rol']);

                    // Guardar datos en sesión
                    $_SESSION['id_rol'] = $user['id_rol'];
                    $_SESSION['rol_nombre'] = $rol['rol'];

                    // Datos personales
                    $_SESSION['nombre'] = $user['nombre'];
                    $_SESSION['apellido_paterno'] = $user['apellido_paterno'];
                    $_SESSION['apellido_materno'] = $user['apellido_materno'] ?? '';
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['telefono'] = $user['telefono'];
                    $_SESSION['ci'] = $user['ci'];

                    // Crear avatar para el header
                    $inicialNombre = strtoupper(substr($user['nombre'], 0, 1));
                    $inicialApellido = strtoupper(substr($user['apellido_paterno'], 0, 1));
                    $_SESSION['avatar'] = $inicialNombre . $inicialApellido;

                    // Redirigir según el rol
                    switch ($_SESSION['id_rol']) {
                        case '1':
                            header("Location: ../controlador/DashboardControlador.php?action=mostrarDashboardAdministrador");
                            break;
                        case '2':
                            header("Location: ../controlador/DashboardControlador.php?action=mostrarDashboardResidente");
                            break;
                        default:
                            header("Location: ../controlador/DashboardControlador.php?action=mostrarDashboardPersonal");
                            break;
                    }
                    exit();
                }
            } else {
                // Login fallido - registrar en historial y verificar bloqueo
                $this->personamodelo->errorLogin($username);

                // Verificar si después de este intento fallido debe bloquearse
                $bloqueoInfo = $this->personamodelo->verificarYBloquearUsuario($username);
                if ($bloqueoInfo['bloqueado']) {
                    if ($bloqueoInfo['bloqueo_permanente']) {
                        $mensajeError = "Cuenta bloqueada permanentemente por seguridad. Contacte al administrador.";
                        header("Location: ../vista/LoginVista.php?error=" . urlencode($mensajeError));
                    } else {
                        $mensajeError = "Demasiados intentos fallidos. Cuenta bloqueada por: " . $bloqueoInfo['tiempo_restante'];
                        // Pasar el tiempo en segundos para el JavaScript
                        $tiempo_segundos = $bloqueoInfo['segundos_restantes'] ?? 30;
                        // Para debug
                        error_log("DEBUG BLOQUEO FALLIDO - Tiempo: " . $tiempo_segundos . "s, Nivel: " . ($bloqueoInfo['nivel_bloqueo'] ?? 'N/A') . ", Usuario: " . $username);
                        header("Location: ../vista/LoginVista.php?error=" . urlencode($mensajeError) . "&bloqueado=true&tiempo=" . $tiempo_segundos);
                    }
                } else {
                    $mensajeError = "Usuario o contraseña incorrectos";
                    header("Location: ../vista/LoginVista.php?error=" . urlencode($mensajeError));
                }
                exit();
            }
        }
    }
    /**
     * Validar reCAPTCHA v2
     */
    private function validarRecaptcha($recaptcha_response) {
        $secret_key = "6LdZwe0rAAAAABfHgehPznrTwz-M6SydOiaHAfrU"; // Tu SECRET_KEY aquí

        // Verificar si está vacío
        if (empty($recaptcha_response)) {
            return false;
        }

        // Validar con Google
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = [
            'secret' => $secret_key,
            'response' => $recaptcha_response,
            'remoteip' => $_SERVER['REMOTE_ADDR']
        ];

        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            ]
        ];

        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);

        if ($response === false) {
            error_log("Error de conexión con reCAPTCHA");
            return false;
        }

        $result = json_decode($response, true);

        return $result['success'] ?? false;
    }
    /**
     * Cerrar sesion y destruir datos de sesion
     */
    public function logout() {
        // Verificar si la sesion esta activa antes de destruirla
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        // Limpiar todas las variables de sesion
        $_SESSION = array();

        // Eliminar cookie de sesion
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Finalmente destruir la sesion si existe
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        header('Location: ../vista/LoginVista.php');
        exit;
    }

    // =============================================
    // METODOS AUXILIARES DE REDIRECCION
    // =============================================

    /**
     * Redirigir con mensaje de exito
     */
    private function redirigirConExito($mensaje) {
        header('Location: ../controlador/PersonaControlador.php?action=formularioPersona&&success=' . urlencode($mensaje));
        exit;
    }

    /**
     * Redirigir con mensaje de error
     */
    private function redirigirConError($mensaje) {
        header('Location: ../vista/RegistrarPersonaVista.php?error=' . urlencode($mensaje));
        exit;
    }

    /**
     * Redirigir edicion con mensaje de exito
     */
    private function redirigirEdicionConExito($mensaje, $id_rol) {
        $rol = $this->rolmodelo->obtenerRol($id_rol);
        if ($rol['rol'] == "Residente") {
            header('Location: ../controlador/PersonaControlador.php?action=listarResidente&&success=' . urlencode($mensaje));
        } else {
            header('Location: ../controlador/PersonaControlador.php?action=listarPersonal&&success=' . urlencode($mensaje));
        }
        exit;
    }

    /**
     * Redirigir edicion con mensaje de error
     */
    private function redirigirEdicionConError($mensaje, $id_rol) {
        $rol = $this->rolmodelo->obtenerRol($id_rol);
        if ($rol['rol'] == "Residente") {
            header('Location: ../controlador/PersonaControlador.php?action=listarResidente&&error=' . urlencode($mensaje));
        } else {
            header('Location: ../controlador/PersonaControlador.php?action=listarPersonal&&error=' . urlencode($mensaje));
        }
        exit;
    }

    /**
     * Verificar estado de bloqueo para AJAX
     */
    public function verificarBloqueo() {
        if ($_POST && isset($_POST['username'])) {
            $username = $_POST['username'] ?? '';

            if (!empty($username)) {
                $bloqueoInfo = $this->personamodelo->verificarTiempoBloqueo($username);
                header('Content-Type: application/json');
                echo json_encode($bloqueoInfo);
                exit();
            }
        }

        // Si no hay username, retornar error
        header('Content-Type: application/json');
        echo json_encode([
            'bloqueado' => false,
            'tiempo_restante' => '0 segundos',
            'segundos_restantes' => 0,
            'bloqueo_permanente' => false
        ]);
        exit();
    }
}

// =============================================
// MANEJO DE RUTAS - PETICIONES GET
// =============================================

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
            case 'verLogin':
                $controlador->verLogin();
                break;
            case 'listarPersonal':
                $controlador->listarPersonal();
                break;
            case 'listarResidente':
                $controlador->listarResidente();
                break;
            case 'listarEliminados':
                $controlador->listarEliminados();
                break;
            case 'formularioPersona':
                $controlador->formularioPersona();
                break;
            default:
                header('Location: ../vista/DashboardVista.php?error=Accion no valida');
                exit;
        }
    }
}

// =============================================
// MANEJO DE RUTAS - PETICIONES POST
// =============================================

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    require_once '../../config/database.php';
    require_once '../modelo/PersonaModelo.php';
    require_once '../modelo/RolModelo.php';
    require_once '../modelo/CorreoModelo.php';

    $database = new Database();
    $db = $database->getConnection();
    $controlador = new PersonaControlador($db);

    switch($_POST['action']) {
        case 'login':
            $controlador->login();
            break;
        case 'logout':
            $controlador->logout();
            break;
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
        case 'restaurarPersona':
            $controlador->restaurarPersona();
            break;
        case 'cambiarContraseña':
            $controlador->cambiarContraseña();
            break;
        case 'verificarBloqueo': // ← NUEVA ACCIÓN PARA AJAX
            $controlador->verificarBloqueo();
            break;
        default:
            header('Location: ../vista/DashboardVista.php?error=Accion no valida');
            exit;
    }
}
?>
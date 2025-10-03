<?php
session_start();
require_once '../../config/database.php';
require_once '../modelo/RegistroModelo.php';

class RegistroControlador {
    private $modelo;
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->modelo = new RegistroModelo($this->conn);
    }

    public function registrarUsuario() {
        // Verificar que todos los campos requeridos estén presentes
        $campos_requeridos = ['nombre', 'appaterno', 'apmaterno', 'dia', 'mes', 'anio', 'ci', 'email', 'usuario', 'password', 'confirm_password'];
        
        foreach ($campos_requeridos as $campo) {
            if (empty($_POST[$campo])) {
                $_SESSION['error'] = "Todos los campos son obligatorios.";
                header("Location: ../vista/RegistroVista.php");
                exit();
            }
        }

        // Obtener y limpiar datos
        $nombre = trim($_POST['nombre']);
        $appaterno = trim($_POST['appaterno']);
        $apmaterno = trim($_POST['apmaterno']);
        $dia = $_POST['dia'];
        $mes = $_POST['mes'];
        $anio = $_POST['anio'];
        $ci = trim($_POST['ci']);
        $telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
        $email = trim($_POST['email']);
        $usuario = trim($_POST['usuario']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Validar formato de fecha
        $fecha_naci = $this->validarFechaNacimiento($dia, $mes, $anio);
        if (!$fecha_naci) {
            $_SESSION['error'] = "La fecha de nacimiento no es válida o el usuario debe ser mayor de 18 años.";
            header("Location: ../vista/RegistroVista.php");
            exit();
        }

        // Validar que las contraseñas coincidan
        if ($password !== $confirm_password) {
            $_SESSION['error'] = "Las contraseñas no coinciden.";
            header("Location: ../vista/RegistroVista.php");
            exit();
        }

        // Validar formato de email
        if (!$this->modelo->validarFormatoEmail($email)) {
            $_SESSION['error'] = "Solo se admiten correos Gmail o Hotmail.";
            header("Location: ../vista/RegistroVista.php");
            exit();
        }

        // Validar fortaleza de contraseña
        if (!$this->modelo->validarFortalezaPassword($password)) {
            $_SESSION['error'] = "La contraseña no cumple con los requisitos de seguridad: mínimo 8 caracteres, una mayúscula, una minúscula, un número y un símbolo (@$!%*?&).";
            header("Location: ../vista/RegistroVista.php");
            exit();
        }

        // Verificar si el CI ya existe
        if ($this->modelo->verificarCIExistente($ci)) {
            $_SESSION['error'] = "El número de CI ya está registrado.";
            header("Location: ../vista/RegistroVista.php");
            exit();
        }

        // Verificar si el email ya existe
        if ($this->modelo->verificarEmailExistente($email)) {
            $_SESSION['error'] = "El correo electrónico ya está registrado.";
            header("Location: ../vista/RegistroVista.php");
            exit();
        }

        // Verificar si el usuario ya existe
        if ($this->modelo->verificarUsuarioExistente($usuario)) {
            $_SESSION['error'] = "El nombre de usuario ya está en uso.";
            header("Location: ../vista/RegistroVista.php");
            exit();
        }

        // Hash de la contraseña
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Iniciar transacción
        $this->conn->beginTransaction();

        try {
            // Crear persona
            $personaCreada = $this->modelo->crearPersona(
                $nombre, 
                $appaterno, 
                $apmaterno, 
                $fecha_naci, 
                $ci, 
                $telefono, 
                $email
            );

            if (!$personaCreada) {
                throw new Exception("Error al crear el registro de persona.");
            }

            // Obtener ID de la persona creada
            $id_persona = $this->modelo->obtenerUltimoIdPersona();

            // Crear usuario
            $usuarioCreado = $this->modelo->crearUsuario($usuario, $password_hash, $id_persona);

            if (!$usuarioCreado) {
                throw new Exception("Error al crear el usuario.");
            }

            // Confirmar transacción
            $this->conn->commit();

            // Registro exitoso
            $_SESSION['success'] = "Registro exitoso. Ahora puedes iniciar sesión.";
            $_SESSION['usuario_registrado'] = $usuario;
            header("Location: ../vista/LoginVista.php");
            exit();

        } catch (Exception $e) {
            // Revertir transacción en caso de error
            $this->conn->rollBack();
            $_SESSION['error'] = "Error en el registro: " . $e->getMessage();
            header("Location: ../vista/RegistroVista.php");
            exit();
        }
    }

    // Validar fecha de nacimiento
    private function validarFechaNacimiento($dia, $mes, $anio) {
        // Verificar que los valores sean numéricos
        if (!is_numeric($dia) || !is_numeric($mes) || !is_numeric($anio)) {
            return false;
        }

        // Verificar que la fecha sea válida
        if (!checkdate($mes, $dia, $anio)) {
            return false;
        }

        // Crear fecha en formato YYYY-MM-DD
        $fecha_naci = sprintf("%04d-%02d-%02d", $anio, $mes, $dia);

        // Verificar que no sea una fecha futura
        $hoy = new DateTime();
        $fecha_nacimiento = new DateTime($fecha_naci);
        if ($fecha_nacimiento > $hoy) {
            return false;
        }

        // Verificar que sea mayor de 18 años
        $edad_minima = new DateTime();
        $edad_minima->modify('-18 years');
        if ($fecha_nacimiento > $edad_minima) {
            return false;
        }

        return $fecha_naci;
    }

    public function __destruct() {
        $this->conn = null;
    }
}

// Procesar la acción
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $controlador = new RegistroControlador();
    
    switch ($_POST['action']) {
        case 'registro':
            $controlador->registrarUsuario();
            break;
        default:
            $_SESSION['error'] = "Acción no válida.";
            header("Location: ../vista/RegistroVista.php");
            exit();
    }
} else {
    $_SESSION['error'] = "Acceso no autorizado.";
    header("Location: ../vista/RegistroVista.php");
    exit();
}
?>
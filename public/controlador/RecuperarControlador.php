<?php
session_start();
require_once '../../config/database.php';
require_once '../modelo/RecuperarModelo.php';

class RecuperarControlador {
    private $modelo;
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->modelo = new RecuperarModelo($this->conn);
    }

    public function solicitarCodigo() {
        $email = trim($_POST['email'] ?? '');

        if (empty($email)) {
            $_SESSION['error'] = "Por favor ingresa un email.";
            header("Location: ../vista/RecuperarVista.php?etapa=solicitud");
            exit();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "Por favor ingresa un email válido.";
            header("Location: ../vista/RecuperarVista.php?etapa=solicitud");
            exit();
        }

        // Validar formato de email (solo Gmail o Hotmail)
        if (!$this->modelo->validarFormatoEmail($email)) {
            $_SESSION['error'] = "Solo se admiten correos Gmail o Hotmail.";
            header("Location: ../vista/RecuperarVista.php?etapa=solicitud");
            exit();
        }

        // Verificar si el email existe y obtener datos
        $usuarioData = $this->modelo->obtenerUsuarioPorEmail($email);
        
        if (!$usuarioData) {
            $_SESSION['error'] = "No existe una cuenta con ese email.";
            header("Location: ../vista/RecuperarVista.php?etapa=solicitud");
            exit();
        }

        // Generar código de recuperación (6 dígitos)
        $codigo_recuperacion = sprintf("%06d", mt_rand(1, 999999));
        $expiracion = date('Y-m-d H:i:s', strtotime('+30 minutes'));

        // Guardar código en la base de datos
        $resultado = $this->modelo->guardarCodigoRecuperacion(
            $usuarioData['id_usuario'], 
            $codigo_recuperacion, 
            $expiracion,
            $usuarioData['tipo']
        );

        if ($resultado) {
            // Guardar datos en sesión
            $_SESSION['email_recuperacion'] = $email;
            $_SESSION['id_usuario_recuperacion'] = $usuarioData['id_usuario'];
            $_SESSION['tipo_usuario_recuperacion'] = $usuarioData['tipo'];
            
            // Para testing - mostrar código en pantalla
            $_SESSION['codigo_debug'] = $codigo_recuperacion;
            
            // En un entorno real, aquí enviarías el código por email
            header("Location: ../vista/RecuperarVista.php?etapa=codigo");
            exit();
        } else {
            $_SESSION['error'] = "Error al generar el código de recuperación.";
            header("Location: ../vista/RecuperarVista.php?etapa=solicitud");
            exit();
        }
    }

    public function verificarCodigo() {
        $codigo_ingresado = trim($_POST['codigo'] ?? '');
        $id_usuario = $_SESSION['id_usuario_recuperacion'] ?? '';
        $tipo_usuario = $_SESSION['tipo_usuario_recuperacion'] ?? '';

        if (empty($codigo_ingresado) || strlen($codigo_ingresado) !== 6) {
            $_SESSION['error'] = "El código debe tener 6 dígitos.";
            header("Location: ../vista/RecuperarVista.php?etapa=codigo");
            exit();
        }

        if (empty($id_usuario) || empty($tipo_usuario)) {
            $_SESSION['error'] = "Sesión inválida. Por favor, solicita un nuevo código.";
            header("Location: ../vista/RecuperarVista.php?etapa=solicitud");
            exit();
        }

        // Verificar código
        $codigoValido = $this->modelo->verificarCodigoRecuperacion(
            $id_usuario, 
            $codigo_ingresado, 
            $tipo_usuario
        );
        
        if ($codigoValido) {
            $_SESSION['codigo_valido'] = true;
            header("Location: ../vista/RecuperarVista.php?etapa=nueva_contrasena");
            exit();
        } else {
            $_SESSION['error'] = "Código inválido o expirado.";
            header("Location: ../vista/RecuperarVista.php?etapa=codigo");
            exit();
        }
    }

    public function cambiarContrasena() {
        $nueva_contrasena = $_POST['nueva_contrasena'] ?? '';
        $confirmar_contrasena = $_POST['confirmar_contrasena'] ?? '';
        $id_usuario = $_SESSION['id_usuario_recuperacion'] ?? '';
        $tipo_usuario = $_SESSION['tipo_usuario_recuperacion'] ?? '';

        // Validar sesión
        if ($_SESSION['codigo_valido'] !== true || empty($id_usuario) || empty($tipo_usuario)) {
            $_SESSION['error'] = "Sesión inválida. Por favor, inicia el proceso nuevamente.";
            header("Location: ../vista/RecuperarVista.php?etapa=solicitud");
            exit();
        }

        // Validar que las contraseñas coincidan
        if ($nueva_contrasena !== $confirmar_contrasena) {
            $_SESSION['error'] = "Las contraseñas no coinciden.";
            header("Location: ../vista/RecuperarVista.php?etapa=nueva_contrasena");
            exit();
        }

        // Validar fortaleza de contraseña
        if (!$this->modelo->validarFortalezaPassword($nueva_contrasena)) {
            $_SESSION['error'] = "La contraseña no cumple con los requisitos de seguridad.";
            header("Location: ../vista/RecuperarVista.php?etapa=nueva_contrasena");
            exit();
        }

        // Actualizar contraseña
        $resultado = $this->modelo->actualizarContrasena(
            $id_usuario, 
            $nueva_contrasena, 
            $tipo_usuario
        );
        
        if ($resultado) {
            // Limpiar sesión
            unset($_SESSION['email_recuperacion']);
            unset($_SESSION['id_usuario_recuperacion']);
            unset($_SESSION['tipo_usuario_recuperacion']);
            unset($_SESSION['codigo_valido']);
            unset($_SESSION['codigo_debug']);

            $_SESSION['success'] = "Contraseña actualizada correctamente. Ahora puedes iniciar sesión.";
            
            // Redirigir al login correspondiente
            header("Location: ../vista/LoginVista.php");
            exit();
        } else {
            $_SESSION['error'] = "Error al actualizar la contraseña.";
            header("Location: ../vista/RecuperarVista.php?etapa=nueva_contrasena");
            exit();
        }
    }

    public function __destruct() {
        $this->conn = null;
    }
}

// Procesar la acción
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $controlador = new RecuperarControlador();
    
    switch ($_POST['action']) {
        case 'solicitar_codigo':
            $controlador->solicitarCodigo();
            break;
        case 'verificar_codigo':
            $controlador->verificarCodigo();
            break;
        case 'cambiar_contrasena':
            $controlador->cambiarContrasena();
            break;
        default:
            $_SESSION['error'] = "Acción no válida.";
            header("Location: ../vista/RecuperarVista.php");
            exit();
    }
} else {
    $_SESSION['error'] = "Acceso no autorizado.";
    header("Location: ../vista/RecuperarVista.php");
    exit();
}
?>
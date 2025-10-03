<?php
class RegistroModelo {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Verificar si el CI ya existe
    public function verificarCIExistente($ci) {
        $sql = "SELECT id_persona FROM persona WHERE ci = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$ci]);
        return $stmt->rowCount() > 0;
    }

    // Verificar si el email ya existe
    public function verificarEmailExistente($email) {
        $sql = "SELECT id_persona FROM persona WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->rowCount() > 0;
    }

    // Verificar si el usuario ya existe
    public function verificarUsuarioExistente($usuario) {
        $sql = "SELECT id_usuario FROM usuario WHERE username = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$usuario]);
        return $stmt->rowCount() > 0;
    }

    // Validar formato de email (Gmail o Hotmail)
    public function validarFormatoEmail($email) {
        return preg_match('/@(gmail|hotmail)\.com$/', $email);
    }

    // Validar fortaleza de contraseña
    public function validarFortalezaPassword($password) {
        // Mínimo 8 caracteres, al menos una mayúscula, una minúscula, un número y un símbolo
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password);
    }

    // Crear nueva persona
    public function crearPersona($nombre, $appaterno, $apmaterno, $fecha_naci, $ci, $telefono, $email) {
        $sql = "INSERT INTO persona (nombre, appaterno, apmaterno, fecha_naci, ci, telefono, email) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$nombre, $appaterno, $apmaterno, $fecha_naci, $ci, $telefono, $email]);
    }

    // Obtener ID de la última persona insertada
    public function obtenerUltimoIdPersona() {
        return $this->conn->lastInsertId();
    }

    // Crear nuevo usuario
    public function crearUsuario($username, $password_hash, $id_persona) {
        $sql = "INSERT INTO usuario (username, password_hash, id_persona) 
                VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$username, $password_hash, $id_persona]);
    }

    // Obtener información del usuario por ID
    public function obtenerUsuarioPorId($id_usuario) {
        $sql = "SELECT u.*, p.nombre, p.appaterno, p.apmaterno, p.ci, p.telefono, p.email 
                FROM usuario u 
                INNER JOIN persona p ON u.id_persona = p.id_persona 
                WHERE u.id_usuario = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id_usuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
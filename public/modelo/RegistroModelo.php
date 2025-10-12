<?php
class RegistroModelo {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }
    // Verificar si el usuario ya existe
    public function verificarUsuarioExistente($usuario) {
        $sql = "SELECT id_usuario FROM usuario WHERE username = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$usuario]);
        return $stmt->rowCount() > 0;
    }

    // Crear nueva persona
    public function crearPersona($nombre, $appaterno, $apmaterno, $fecha_naci, $ci, $telefono, $email) {
        $sql = "INSERT INTO persona (nombre, appaterno, apmaterno, fecha_naci, ci, telefono, email) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$nombre, $appaterno, $apmaterno, $fecha_naci, $ci, $telefono, $email]);
    }

    // Obtener ID de la última persona insertada
    public function obtenerUltimoIdPersona() {
        return $this->db->lastInsertId();
    }

    // Crear nuevo usuario
    public function crearUsuario($username, $password_hash, $id_persona) {
        $sql = "INSERT INTO usuario (username, password_hash, id_persona) 
                VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$username, $password_hash, $id_persona]);
    }

    // Obtener información del usuario por ID
    public function obtenerUsuarioPorId($id_usuario) {
        $sql = "SELECT u.*, p.nombre, p.appaterno, p.apmaterno, p.ci, p.telefono, p.email 
                FROM usuario u 
                INNER JOIN persona p ON u.id_persona = p.id_persona 
                WHERE u.id_usuario = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_usuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
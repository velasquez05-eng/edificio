<?php
class LoginModelo {
    private $conn;
    private $table_name = "login";

    public function __construct($db) {
        $this->conn = $db;
    }

    //se debe colocar el insert en el historial_login si logro iniciar sesion
    public function login($username, $password) {
        $query = "SELECT * FROM " . $this->table_name . " u INNER JOIN persona p ON u.id_persona = p.id_persona WHERE u.username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            // Verificar contraseña
            if (password_verify($password, $row['password_hash'])) {
                return $row;
            }
        }
        return false;
    }

    //verifica si el username ya fue registrado
    public function verificarUsuarioExistente($username) {
        $sql = "SELECT * FROM login WHERE username = :username";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        if ($stmt->rowCount() == 1) {
            return true;
        }
        return false;
    }

    // Crear nuevo username y password
    public function registrarLogin($username, $password_hash, $id_persona) {
        $sql = "INSERT INTO login (username, password_hash, tiempo_verificacion, id_persona) 
            VALUES (:username, :password_hash, DATE_ADD(NOW(), INTERVAL 3 DAY), :id_persona)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password_hash', $password_hash);
        $stmt->bindParam(':id_persona', $id_persona);
        return $stmt->execute();
    }
}
?>
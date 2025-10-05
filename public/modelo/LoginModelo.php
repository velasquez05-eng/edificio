<?php
class LoginModelo {
    private $conn;
    private $table_name = "usuario";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login($username, $password) {
        $query = "SELECT *
                  FROM " . $this->table_name . " u
                  INNER JOIN persona p ON u.id_persona = p.id_persona
                  WHERE u.username = :username";

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

    public function loginPersonal($username, $password) {
        $query = "SELECT *
                    FROM personal p
                    INNER JOIN persona per ON p.id_persona = per.id_persona
                    INNER JOIN roles r ON p.id_rol = r.id_rol
                  WHERE p.username = :username";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // CORREGIDO: Cambiar $row['p.password_hash'] por $row['password_hash']
            if (password_verify($password, $row['password_hash'])) {
                return $row;
            }
        }
        return false;
    }
}
?>
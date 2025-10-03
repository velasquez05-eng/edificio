<?php
class RecuperarModelo {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Validar formato de email (Gmail o Hotmail)
    public function validarFormatoEmail($email) {
        return preg_match('/@(gmail|hotmail)\.com$/', $email);
    }

    // Validar fortaleza de contraseña
    public function validarFortalezaPassword($password) {
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password);
    }

    // Obtener usuario por email
    public function obtenerUsuarioPorEmail($email) {
        $sql = "SELECT u.id_usuario, u.username, p.nombre, p.email, 'usuario' as tipo
                FROM usuario u 
                INNER JOIN persona p ON u.id_persona = p.id_persona 
                WHERE p.email = ? 
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$email]);
        
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Si no encuentra usuario, buscar en personal
        if (!$usuario) {
            $sql = "SELECT p.id_personal as id_usuario, p.username, per.nombre, per.email, 'personal' as tipo
                    FROM personal p 
                    INNER JOIN persona per ON p.id_persona = per.id_persona 
                    WHERE per.email = ? 
                    LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        return $usuario;
    }

    // Guardar código de recuperación
    public function guardarCodigoRecuperacion($id_usuario, $codigo, $expiracion, $tipo) {
        if ($tipo === 'usuario') {
            $sql = "UPDATE usuario SET codigo_recuperacion = ?, expiracion_codigo = ? WHERE id_usuario = ?";
        } else {
            $sql = "UPDATE personal SET codigo_recuperacion = ?, expiracion_codigo = ? WHERE id_personal = ?";
        }
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$codigo, $expiracion, $id_usuario]);
    }

    // Verificar código de recuperación
    public function verificarCodigoRecuperacion($id_usuario, $codigo, $tipo) {
        if ($tipo === 'usuario') {
            $sql = "SELECT id_usuario FROM usuario 
                    WHERE id_usuario = ? 
                    AND codigo_recuperacion = ? 
                    AND expiracion_codigo > NOW()";
        } else {
            $sql = "SELECT id_personal FROM personal 
                    WHERE id_personal = ? 
                    AND codigo_recuperacion = ? 
                    AND expiracion_codigo > NOW()";
        }
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id_usuario, $codigo]);
        return $stmt->rowCount() > 0;
    }

    // Actualizar contraseña
    public function actualizarContrasena($id_usuario, $nueva_contrasena, $tipo) {
        $hash = password_hash($nueva_contrasena, PASSWORD_DEFAULT);
        
        if ($tipo === 'usuario') {
            $sql = "UPDATE usuario 
                    SET password_hash = ?, 
                        codigo_recuperacion = NULL, 
                        expiracion_codigo = NULL 
                    WHERE id_usuario = ?";
        } else {
            $sql = "UPDATE personal 
                    SET password_hash = ?, 
                        codigo_recuperacion = NULL, 
                        expiracion_codigo = NULL 
                    WHERE id_personal = ?";
        }
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$hash, $id_usuario]);
    }
}
?>
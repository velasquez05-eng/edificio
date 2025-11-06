<?php
// public/modelo/ComunicadoModelo.php

require_once '../../config/database.php';

class ComunicadoModelo {
    private $conn;
    private $table_name = "comunicado";
    private $encryption_key;

    public function __construct($db) {
        $this->conn = $db;
        $this->encryption_key = '1A3F6C9E2B5D8A0C7E4F1A2B3C8D5E6F7A1B2C3D4E5F6A7B8C9D0E1F2A3B4C5D6';

        // Asegurar que la clave tenga exactamente 32 bytes
        if (strlen($this->encryption_key) < 32) {
            $this->encryption_key = str_pad($this->encryption_key, 32, "\0");
        } elseif (strlen($this->encryption_key) > 32) {
            $this->encryption_key = substr($this->encryption_key, 0, 32);
        }
    }

    // Método para descifrar datos
    private function decrypt($encrypted_data) {
        if (empty($encrypted_data)) return $encrypted_data;
        try {
            $data = base64_decode($encrypted_data);
            if ($data === false) {
                throw new Exception('Error decodificando base64');
            }
            $iv = substr($data, 0, 16);
            $encrypted = substr($data, 16);
            $decrypted = openssl_decrypt($encrypted, 'AES-256-CBC', $this->encryption_key, OPENSSL_RAW_DATA, $iv);
            if ($decrypted === false) {
                throw new Exception('Error en descifrado: ' . openssl_error_string());
            }
            return $decrypted;
        } catch (Exception $e) {
            error_log("Error descifrando datos: " . $e->getMessage());
            return false;
        }
    }

    // Crear nuevo comunicado
    public function crear($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                 (id_persona, titulo, contenido, fecha_expiracion, prioridad, estado, tipo_audiencia) 
                 VALUES (:id_persona, :titulo, :contenido, :fecha_expiracion, :prioridad, :estado, :tipo_audiencia)";

        $stmt = $this->conn->prepare($query);

        // Sanitizar datos
        $id_persona = htmlspecialchars(strip_tags($data['id_persona']));
        $titulo = htmlspecialchars(strip_tags($data['titulo']));
        $contenido = htmlspecialchars(strip_tags($data['contenido']));
        $fecha_expiracion = htmlspecialchars(strip_tags($data['fecha_expiracion']));
        $prioridad = htmlspecialchars(strip_tags($data['prioridad']));
        $estado = htmlspecialchars(strip_tags($data['estado']));
        $tipo_audiencia = htmlspecialchars(strip_tags($data['tipo_audiencia']));

        // Bind parameters
        $stmt->bindParam(":id_persona", $id_persona);
        $stmt->bindParam(":titulo", $titulo);
        $stmt->bindParam(":contenido", $contenido);
        $stmt->bindParam(":fecha_expiracion", $fecha_expiracion);
        $stmt->bindParam(":prioridad", $prioridad);
        $stmt->bindParam(":estado", $estado);
        $stmt->bindParam(":tipo_audiencia", $tipo_audiencia);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Listar todos los comunicados
    public function listarComunicados() {
        $query = "SELECT c.*, p.nombre as autor_nombre, p.apellido_paterno as autor_apellido 
                  FROM " . $this->table_name . " c 
                  LEFT JOIN persona p ON c.id_persona = p.id_persona 
                  WHERE c.estado != 'eliminado'
                  ORDER BY c.fecha_publicacion DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Desencriptar datos sensibles del autor
        foreach ($resultados as &$comunicado) {
            if (!empty($comunicado['autor_nombre'])) {
                $comunicado['autor_nombre'] = $this->decrypt($comunicado['autor_nombre']);
            }
            if (!empty($comunicado['autor_apellido'])) {
                $comunicado['autor_apellido'] = $this->decrypt($comunicado['autor_apellido']);
            }
        }

        return $resultados;
    }

    // Obtener comunicado por ID
    public function obtenerPorId($id_comunicado) {
        $query = "SELECT c.*, p.nombre as autor_nombre, p.apellido_paterno as autor_apellido 
                  FROM " . $this->table_name . " c 
                  LEFT JOIN persona p ON c.id_persona = p.id_persona 
                  WHERE c.id_comunicado = :id_comunicado";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_comunicado", $id_comunicado);
        $stmt->execute();
        $comunicado = $stmt->fetch(PDO::FETCH_ASSOC);

        // Desencriptar datos sensibles del autor
        if ($comunicado) {
            if (!empty($comunicado['autor_nombre'])) {
                $comunicado['autor_nombre'] = $this->decrypt($comunicado['autor_nombre']);
            }
            if (!empty($comunicado['autor_apellido'])) {
                $comunicado['autor_apellido'] = $this->decrypt($comunicado['autor_apellido']);
            }
        }

        return $comunicado;
    }

    // Actualizar comunicado
    public function actualizar($id_comunicado, $data) {
        $query = "UPDATE " . $this->table_name . " 
                 SET titulo = :titulo, contenido = :contenido, fecha_expiracion = :fecha_expiracion, 
                     prioridad = :prioridad, estado = :estado, tipo_audiencia = :tipo_audiencia 
                 WHERE id_comunicado = :id_comunicado";

        $stmt = $this->conn->prepare($query);

        // Sanitizar datos
        $titulo = htmlspecialchars(strip_tags($data['titulo']));
        $contenido = htmlspecialchars(strip_tags($data['contenido']));
        $fecha_expiracion = htmlspecialchars(strip_tags($data['fecha_expiracion']));
        $prioridad = htmlspecialchars(strip_tags($data['prioridad']));
        $estado = htmlspecialchars(strip_tags($data['estado']));
        $tipo_audiencia = htmlspecialchars(strip_tags($data['tipo_audiencia']));

        // Bind parameters
        $stmt->bindParam(":titulo", $titulo);
        $stmt->bindParam(":contenido", $contenido);
        $stmt->bindParam(":fecha_expiracion", $fecha_expiracion);
        $stmt->bindParam(":prioridad", $prioridad);
        $stmt->bindParam(":estado", $estado);
        $stmt->bindParam(":tipo_audiencia", $tipo_audiencia);
        $stmt->bindParam(":id_comunicado", $id_comunicado);

        return $stmt->execute();
    }

    // Cambiar estado del comunicado
    public function cambiarEstado($id_comunicado, $estado) {
        $query = "UPDATE " . $this->table_name . " 
                 SET estado = :estado 
                 WHERE id_comunicado = :id_comunicado";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":estado", $estado);
        $stmt->bindParam(":id_comunicado", $id_comunicado);

        return $stmt->execute();
    }

    // Eliminar comunicado (soft delete)
    public function eliminar($id_comunicado) {
        $query = "UPDATE " . $this->table_name . " 
                 SET estado = 'eliminado' 
                 WHERE id_comunicado = :id_comunicado";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_comunicado", $id_comunicado);

        return $stmt->execute();
    }

    // Obtener estadísticas de comunicados
    public function obtenerEstadisticas() {
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN estado = 'publicado' THEN 1 ELSE 0 END) as publicados,
                    SUM(CASE WHEN estado = 'borrador' THEN 1 ELSE 0 END) as borradores,
                    SUM(CASE WHEN estado = 'archivado' THEN 1 ELSE 0 END) as archivados,
                    SUM(CASE WHEN estado = 'eliminado' THEN 1 ELSE 0 END) as eliminados
                  FROM " . $this->table_name;

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener comunicados públicos para dashboard
    public function obtenerComunicadosPublicos() {
        $query = "SELECT c.*, p.nombre as autor_nombre, p.apellido_paterno as autor_apellido 
                  FROM " . $this->table_name . " c 
                  LEFT JOIN persona p ON c.id_persona = p.id_persona 
                  WHERE c.estado = 'publicado' 
                  AND (c.fecha_expiracion IS NULL OR c.fecha_expiracion >= CURDATE())
                  ORDER BY 
                    CASE c.prioridad
                        WHEN 'urgente' THEN 1
                        WHEN 'alta' THEN 2
                        WHEN 'media' THEN 3
                        WHEN 'baja' THEN 4
                    END,
                    c.fecha_publicacion DESC
                  LIMIT 10";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Desencriptar datos sensibles del autor
        foreach ($resultados as &$comunicado) {
            if (!empty($comunicado['autor_nombre'])) {
                $comunicado['autor_nombre'] = $this->decrypt($comunicado['autor_nombre']);
            }
            if (!empty($comunicado['autor_apellido'])) {
                $comunicado['autor_apellido'] = $this->decrypt($comunicado['autor_apellido']);
            }
        }

        return $resultados;
    }

    // Obtener todos los comunicados publicados (sin límite)
    public function listarComunicadosPublicados() {
        $query = "SELECT c.*, p.nombre as autor_nombre, p.apellido_paterno as autor_apellido 
                  FROM " . $this->table_name . " c 
                  LEFT JOIN persona p ON c.id_persona = p.id_persona 
                  WHERE c.estado = 'publicado' 
                  AND (c.fecha_expiracion IS NULL OR c.fecha_expiracion >= CURDATE())
                  ORDER BY 
                    CASE c.prioridad
                        WHEN 'urgente' THEN 1
                        WHEN 'alta' THEN 2
                        WHEN 'media' THEN 3
                        WHEN 'baja' THEN 4
                    END,
                    c.fecha_publicacion DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Desencriptar datos sensibles del autor
        foreach ($resultados as &$comunicado) {
            if (!empty($comunicado['autor_nombre'])) {
                $comunicado['autor_nombre'] = $this->decrypt($comunicado['autor_nombre']);
            }
            if (!empty($comunicado['autor_apellido'])) {
                $comunicado['autor_apellido'] = $this->decrypt($comunicado['autor_apellido']);
            }
        }

        return $resultados;
    }

    // Listar comunicados eliminados
    public function listarComunicadosEliminados() {
        $query = "SELECT c.*, p.nombre as autor_nombre, p.apellido_paterno as autor_apellido 
                  FROM " . $this->table_name . " c 
                  LEFT JOIN persona p ON c.id_persona = p.id_persona 
                  WHERE c.estado = 'eliminado'
                  ORDER BY c.fecha_publicacion DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Desencriptar datos sensibles del autor
        foreach ($resultados as &$comunicado) {
            if (!empty($comunicado['autor_nombre'])) {
                $comunicado['autor_nombre'] = $this->decrypt($comunicado['autor_nombre']);
            }
            if (!empty($comunicado['autor_apellido'])) {
                $comunicado['autor_apellido'] = $this->decrypt($comunicado['autor_apellido']);
            }
        }

        return $resultados;
    }

    // Restaurar comunicado eliminado
    public function restaurar($id_comunicado) {
        $query = "UPDATE " . $this->table_name . " 
                 SET estado = 'borrador' 
                 WHERE id_comunicado = :id_comunicado";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_comunicado", $id_comunicado);

        return $stmt->execute();
    }
}
?>
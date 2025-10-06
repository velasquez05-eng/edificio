<?php
class EdificioModelo {
    private $conn;
    private $table_name = "edificio";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function obtenerEdificio($id_edificio) {
        $query = "SELECT * 
                  FROM " . $this->table_name . " 
                  WHERE id_edificio = :id_edificio"; // ← Corregido

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_edificio', $id_edificio);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        return null;
    }

    public function actualizarEdificio($id_edificio, $nombre, $direccion) {
        $query = "UPDATE " . $this->table_name . " 
                  SET nombre = :nombre, direccion = :direccion 
                  WHERE id_edificio = :id_edificio"; // ← Corregido

        $stmt = $this->conn->prepare($query);

        $nombre = htmlspecialchars(strip_tags($nombre));
        $direccion = htmlspecialchars(strip_tags($direccion));

        $stmt->bindParam(':id_edificio', $id_edificio);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':direccion', $direccion);

        return $stmt->execute(); // ← Simplificado
    }
}
?>
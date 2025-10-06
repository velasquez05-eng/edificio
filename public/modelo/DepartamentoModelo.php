<?php
class DepartamentoModelo {
    private $conn;
    private $table_name = "departamento";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function obtenerDepartamentoModelo() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        return [];
    }

    public function actualizarDepartamentoModelo($id_departamento, $numero, $piso) {
        
        $query = "UPDATE " . $this->table_name . " 
                  SET numero = :numero, piso = :piso 
                  WHERE id_departamento = :id_departamento";
        
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        $stmt->bindParam(':numero', $numero);
        $stmt->bindParam(':piso', $piso);
        $stmt->bindParam(':id_departamento', $id_departamento);
        
        // Ejecutar y verificar
        $resultado = $stmt->execute();
        
        
        if ($resultado && $stmt->rowCount() > 0) {
            return true;
        }
        
        return false;
    }

    public function obtenerDepartamentoPorId($id_departamento) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_departamento = :id_departamento";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_departamento', $id_departamento);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        return null;
    }
  
    public function registrarDepartamentoModelo($numero, $piso,$id_edificio) {
    // Validar que los datos no estén vacíos
    if (empty($numero) || empty($piso)||empty($id_edificio)) {
        error_log("ERROR: Número, piso e id son obligatorios");
        return false;
    }
    
    // Verificar si ya existe un departamento con el mismo número (opcional)
    $queryCheck = "SELECT id_departamento FROM " . $this->table_name . " WHERE numero = :numero";
    $stmtCheck = $this->conn->prepare($queryCheck);
    $stmtCheck->bindParam(':numero', $numero);
    $stmtCheck->execute();
    
    if ($stmtCheck->rowCount() > 0) {
        return false;
    }
    
    // Query para insertar el nuevo departamento
    $query = "INSERT INTO " . $this->table_name . " (numero, piso,id_edificio) 
              VALUES (:numero, :piso,:id_edificio)";
    
    $stmt = $this->conn->prepare($query);
    
    // Bind parameters
    $stmt->bindParam(':numero', $numero);
    $stmt->bindParam(':piso', $piso);
    $stmt->bindParam(':id_edificio', $id_edificio);

    // Ejecutar y verificar
    $resultado = $stmt->execute();
    
    if ($resultado) {
        return $this->conn->lastInsertId(); // Retorna el ID del nuevo departamento
    }
    
    return false;
}
}
?>
<?php

class DepartamentoModelo{
    private $db;
    private $table_departamento = 'departamento';
    private $table_tiene_departamento = 'tiene_departamento';

    public function __construct($db){
        $this->db = $db;
    }

    public function listarDepartamento(){
        $sql = "SELECT * FROM " . $this->table_departamento;
        $resultado = $this->db->prepare($sql);
        $resultado->execute();
        return $resultado->fetchAll(PDO::FETCH_ASSOC);
    }

    public function verificarDepartamento($numero){
        $sql = "SELECT * FROM " . $this->table_departamento . " WHERE numero = :numero";
        $resultado = $this->db->prepare($sql);
        $resultado->bindParam(':numero', $numero);
        $resultado->execute();
        return $resultado->rowCount() > 0;
    }

    // Método corregido para PDO
    public function verificarDepartamentoExcluyendo($numero, $id_excluir) {
        $sql = "SELECT id_departamento FROM " . $this->table_departamento . " WHERE numero = :numero AND id_departamento != :id_excluir";
        $resultado = $this->db->prepare($sql);
        $resultado->bindParam(':numero', $numero);
        $resultado->bindParam(':id_excluir', $id_excluir, PDO::PARAM_INT);
        $resultado->execute();
        return $resultado->rowCount() > 0;
    }

    //listar departamentos por el id_persona osea que le pertenecen a la persona
    public function listarDepartamentoPersona($id_persona){
        $sql = "SELECT * FROM " . $this->table_departamento . " dep, " . $this->table_tiene_departamento . " tdep 
                WHERE tdep.id_persona = :id_persona 
                AND dep.id_departamento = tdep.id_departamento 
                AND estado = 'activo'";
        $resultado = $this->db->prepare($sql);
        $resultado->bindParam(":id_persona", $id_persona);
        $resultado->execute();
        return $resultado->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registrarDepartamento($numero, $piso){
        try {
            $sql = "INSERT INTO " . $this->table_departamento . " (numero, piso) VALUES (:numero, :piso)";
            $resultado = $this->db->prepare($sql);
            $resultado->bindParam(':numero', $numero);
            $resultado->bindParam(':piso', $piso, PDO::PARAM_INT);
            if($resultado->execute()){
                return true;
            }
            return false;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function registrarDepartamentoPersona($id_persona, $id_departamento){
        try {
            $sql = "INSERT INTO " . $this->table_tiene_departamento . " (id_persona, id_departamento) 
                    VALUES (:id_persona, :id_departamento)";
            $resultado = $this->db->prepare($sql);
            $resultado->bindParam(':id_persona', $id_persona, PDO::PARAM_INT);
            $resultado->bindParam(':id_departamento', $id_departamento, PDO::PARAM_INT);
            if($resultado->execute()){
                return true;
            }
            return false;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function desvincularDepartamentoPersona($id_persona, $id_departamento){
        try {
            $sql = "UPDATE " . $this->table_tiene_departamento . " 
                    SET estado = 'inactivo'
                    WHERE id_persona = :id_persona 
                    AND id_departamento = :id_departamento";
            $resultado = $this->db->prepare($sql);
            $resultado->bindParam(':id_persona', $id_persona, PDO::PARAM_INT);
            $resultado->bindParam(':id_departamento', $id_departamento, PDO::PARAM_INT);
            if($resultado->execute()){
                return true;
            }
            return false;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function editarDepartamento($id_departamento, $numero, $piso){
        try {
            $sql = "UPDATE " . $this->table_departamento . "
                    SET numero = :numero, piso = :piso
                    WHERE id_departamento = :id_departamento";
            $resultado = $this->db->prepare($sql);
            $resultado->bindParam(':id_departamento', $id_departamento, PDO::PARAM_INT);
            $resultado->bindParam(':numero', $numero);
            $resultado->bindParam(':piso', $piso, PDO::PARAM_INT);

            if($resultado->execute()){
                return true;
            }
            return false;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    //aisgnar persoan a departamento
    public function asignarPersonasDepartamento($id_departamento, $personas_ids){
        try {
            $this->db->beginTransaction();

            // 2. Insertar las nuevas asignaciones
            $sql_insertar = "INSERT INTO " . $this->table_tiene_departamento . " 
                        (id_departamento, id_persona, estado) 
                        VALUES (:id_departamento, :id_persona, 'activo')";
            $stmt_insertar = $this->db->prepare($sql_insertar);

            foreach ($personas_ids as $id_persona) {
                $stmt_insertar->bindParam(':id_departamento', $id_departamento, PDO::PARAM_INT);
                $stmt_insertar->bindParam(':id_persona', $id_persona, PDO::PARAM_INT);

                if (!$stmt_insertar->execute()) {
                    throw new Exception("Error al asignar persona con ID: $id_persona");
                }
            }

            // 3. Actualizar estado del departamento a "ocupado"
            $sql_actualizar = "UPDATE " . $this->table_departamento . " 
                          SET estado = 'ocupado' 
                          WHERE id_departamento = :id_departamento";
            $stmt_actualizar = $this->db->prepare($sql_actualizar);
            $stmt_actualizar->bindParam(':id_departamento', $id_departamento, PDO::PARAM_INT);

            if (!$stmt_actualizar->execute()) {
                throw new Exception('Error al actualizar estado del departamento');
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error en asignarPersonasMasivo: " . $e->getMessage());
            return $e->getMessage();
        }
    }

    // Método adicional para verificar si existe un departamento
    public function existeDepartamento($id_departamento){
        $sql = "SELECT COUNT(*) FROM " . $this->table_departamento . " WHERE id_departamento = :id_departamento";
        $resultado = $this->db->prepare($sql);
        $resultado->bindParam(':id_departamento', $id_departamento, PDO::PARAM_INT);
        $resultado->execute();
        return $resultado->fetchColumn() > 0;
    }

    // Método adicional para obtener departamento por ID
    public function obtenerDepartamentoPorId($id_departamento){
        $sql = "SELECT * FROM " . $this->table_departamento . " WHERE id_departamento = :id_departamento";
        $resultado = $this->db->prepare($sql);
        $resultado->bindParam(':id_departamento', $id_departamento, PDO::PARAM_INT);
        $resultado->execute();
        return $resultado->fetch(PDO::FETCH_ASSOC);
    }

}
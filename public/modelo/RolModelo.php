<?php
class RolModelo
{
    private $db;
    private $table_name = "rol";
    public function __construct($db)
    {
        $this->db = $db;
    }

    public function listarRoles(){
        $query = "SELECT * FROM ".$this->table_name;
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return [];
    }
    public function obtenerRol($id_rol)
    {
        $query = "SELECT * FROM ".$this->table_name." WHERE id_rol = :id_rol";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id_rol", $id_rol);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }else{
            return [];
        }
    }

    public function registrarRol($rol, $descripcion, $salario_base){
        // Validar que los datos no estén vacíos
        if(empty($rol) || empty($descripcion) || empty($salario_base)){
            error_log("ERROR: El rol, descripción y salario base son obligatorios");
            return false;
        }
        $query = "insert into " . $this->table_name . " (rol, descripcion, salario_base) values (:rol, :descripcion, :salario_base)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":rol", $rol);
        $stmt->bindParam(":descripcion", $descripcion);
        $stmt->bindParam(":salario_base", $salario_base);
        if($stmt->execute()){
            return true;
        }else{
            return false;
        }
    }
    public function editarRol($id_rol,$rol,$descripcion,$salario_base){
        $query=" update ".$this->table_name." set rol=:rol, descripcion=:descripcion, salario_base=:salario_base where id_rol=:id_rol";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":rol",$rol);
        $stmt->bindParam(":descripcion",$descripcion);
        $stmt->bindParam(":salario_base",$salario_base);
        $stmt->bindParam(":id_rol",$id_rol);
        if($stmt->execute()){
            return true;
        }else{
            return false;
        }
    }


}
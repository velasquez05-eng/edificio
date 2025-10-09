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

    public function registrarRol($rol, $descripcion){
        // Validar que los datos no estén vacíos
        if(empty($rol) || empty($descripcion)){
            error_log("ERROR: El rol y descripción son obligatorios");
            return false;
        }
        $query = "insert into " . $this->table_name . " (rol, descripcion) values (:rol, :descripcion)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":rol", $rol);
        $stmt->bindParam(":descripcion", $descripcion);
        if($stmt->execute()){
            return true;
        }else{
            return false;
        }
    }
    public function editarRol($id_rol,$rol,$descripcion){
        $query=" update ".$this->table_name." set rol=:rol,descripcion=:descripcion where id_rol=:id_rol";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":rol",$rol);
        $stmt->bindParam(":descripcion",$descripcion);
        $stmt->bindParam(":id_rol",$id_rol);
        if($stmt->execute()){
            return true;
        }else{
            return false;
        }
    }


}
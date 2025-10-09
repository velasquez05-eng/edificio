<?php

class PersonaModelo
{
    private $db;
    private $table_name = "persona";
    private $encryption_key; // Clave de cifrado

    public function __construct($db, $encryption_key = null){
        $this->db = $db;
        $this->encryption_key = $encryption_key ?: '1A3F6C9E2B5D8A0C7E4F1A2B3C8D5E6F7A1B2C3D4E5F6A7B8C9D0E1F2A3B4C5D6';

        if (strlen($this->encryption_key) < 32) {
            $this->encryption_key = str_pad($this->encryption_key, 32, "\0");
        } elseif (strlen($this->encryption_key) > 32) {
            $this->encryption_key = substr($this->encryption_key, 0, 32);
        }
    }

    // Método para cifrar datos
    private function encrypt($data) {
        if (empty($data)) return $data;

        $iv = openssl_random_pseudo_bytes(16);
        $encrypted = openssl_encrypt($data, 'AES-256-CBC', $this->encryption_key, 0, $iv);
        return base64_encode($iv . $encrypted);
    }

    // Método para descifrar datos
    private function decrypt($encrypted_data) {
        if (empty($encrypted_data)) return $encrypted_data;

        $data = base64_decode($encrypted_data);
        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);
        return openssl_decrypt($encrypted, 'AES-256-CBC', $this->encryption_key, 0, $iv);
    }

    public function listarPersonal(){
        $query = "SELECT * FROM " . $this->table_name . " p, rol r 
                 WHERE p.id_rol = r.id_rol AND r.rol != 'Residente'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        if ($stmt->rowCount() > 0){
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Descifrar los datos sensibles
            foreach ($resultados as &$fila) {
                $fila['nombre'] = $this->decrypt($fila['nombre']);
                $fila['apellido_paterno'] = $this->decrypt($fila['apellido_paterno']);
                $fila['apellido_materno'] = $this->decrypt($fila['apellido_materno']);
                $fila['ci'] = $this->decrypt($fila['ci']);
            }

            return $resultados;
        }
        return [];
    }

    public function listarResidente(){
        $query = "SELECT * FROM " . $this->table_name . " p, rol r 
                 WHERE p.id_rol = r.id_rol AND r.rol = 'Residente'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        if ($stmt->rowCount() > 0){
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Descifrar los datos sensibles
            foreach ($resultados as &$fila) {
                $fila['nombre'] = $this->decrypt($fila['nombre']);
                $fila['apellido_paterno'] = $this->decrypt($fila['apellido_paterno']);
                $fila['apellido_materno'] = $this->decrypt($fila['apellido_materno']);
                $fila['ci'] = $this->decrypt($fila['ci']);
            }

            return $resultados;
        }
        return [];
    }

    public function registrarPersona($nombre, $apellido_paterno, $apellido_materno, $ci, $telefono, $email, $id_rol){

        $nombre_encrypted = $this->encrypt($nombre);
        $apellido_paterno_encrypted = $this->encrypt($apellido_paterno);
        $apellido_materno_encrypted = $this->encrypt($apellido_materno);
        $ci_encrypted = $this->encrypt($ci);

        $query = "INSERT INTO " . $this->table_name . " 
                 (nombre, apellido_paterno, apellido_materno, ci, telefono, email, id_rol) 
                 VALUES (:nombre, :apellido_paterno, :apellido_materno, :ci, :telefono, :email, :id_rol)";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":nombre", $nombre_encrypted);
        $stmt->bindParam(":apellido_paterno", $apellido_paterno_encrypted);
        $stmt->bindParam(":apellido_materno", $apellido_materno_encrypted);
        $stmt->bindParam(":ci", $ci_encrypted);
        $stmt->bindParam(":telefono", $telefono);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":id_rol", $id_rol);

        if($stmt->execute()){
            return true;
        }else{
            return false;
        }
    }




}
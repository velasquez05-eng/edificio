<?php

class PersonaModelo
{
    private $db;
    private $table_name = "persona";
    private $encryption_key; // Clave de cifrado

    public function __construct($db, $encryption_key = null){
        $this->db = $db;
        $this->encryption_key = $encryption_key ?: '1A3F6C9E2B5D8A0C7E4F1A2B3C8D5E6F7A1B2C3D4E5F6A7B8C9D0E1F2A3B4C5D6';

        // Asegurar que la clave tenga exactamente 32 bytes
        if (strlen($this->encryption_key) < 32) {
            $this->encryption_key = str_pad($this->encryption_key, 32, "\0");
        } elseif (strlen($this->encryption_key) > 32) {
            $this->encryption_key = substr($this->encryption_key, 0, 32);
        }
    }

    // Metodo para cifrar datos
    private function encrypt($data) {
        if (empty($data)) return $data;
        try {
            $iv = openssl_random_pseudo_bytes(16);
            $encrypted = openssl_encrypt($data, 'AES-256-CBC', $this->encryption_key, OPENSSL_RAW_DATA, $iv);
            if ($encrypted === false) {
                throw new Exception('Error en cifrado: ' . openssl_error_string());
            }
            return base64_encode($iv . $encrypted);
        } catch (Exception $e) {
            error_log("Error cifrando datos: " . $e->getMessage());
            return false;
        }
    }

    // Metodo para descifrar datos
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

    // Verificar si el CI ya existe
    public function verificarCIExistente($ci) {
        $sql = "SELECT id_persona, ci FROM ".$this->table_name;
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $personas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($personas as $persona) {
                $ci_decrypted = $this->decrypt($persona['ci']);
                if ($ci_decrypted === $ci) {
                    return true;
                }
            }
        }
        return false;
    }

    // Verificar si el usuario ya existe
    public function verificarUsuarioExistente($username) {
        $sql = "SELECT id_persona FROM ".$this->table_name." WHERE username = :username";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // Verificar si el email ya existe
    public function verificarEmailExistente($email) {
        $sql = "SELECT id_persona FROM ".$this->table_name." WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function verificarEmail($id_persona, $email) {
        $sql = "SELECT id_persona FROM ".$this->table_name." WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($resultado) {
            // Email existe en la base de datos
            if ($resultado['id_persona'] == $id_persona) {
                return false; // El email le pertenece a esta persona
            } else {
                return true; // El email existe pero es de otra persona
            }
        } else {
            return false; // El email no existe en la base de datos
        }
    }
    public function listarPersonal(){
        $query = "SELECT * FROM " . $this->table_name . " p, rol r 
                 WHERE p.id_rol = r.id_rol AND r.rol != 'Residente' and estado = 'activo'";
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

    public function registrarPersona($nombre, $apellido_paterno, $apellido_materno, $ci, $telefono, $email, $username, $password, $id_rol){
        try {
            // Cifrar los datos sensibles
            $nombre_encrypted = $this->encrypt($nombre);
            $apellido_paterno_encrypted = $this->encrypt($apellido_paterno);
            $apellido_materno_encrypted = $this->encrypt($apellido_materno);
            $ci_encrypted = $this->encrypt($ci);

            // Verificar que el cifrado fue exitoso
            if (!$nombre_encrypted || !$apellido_paterno_encrypted || !$ci_encrypted) {
                throw new Exception("Error al cifrar los datos");
            }

            // Hash de la contraseña
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            if (!$password_hash) {
                throw new Exception("Error al generar el hash de la contraseña");
            }

            $query = "INSERT INTO " . $this->table_name . " 
                     (nombre, apellido_paterno, apellido_materno, ci, telefono, email, username, password_hash, tiempo_verificacion, id_rol) 
                     VALUES (:nombre, :apellido_paterno, :apellido_materno, :ci, :telefono, :email, :username, :password_hash,  DATE_ADD(NOW(), INTERVAL 3 DAY), :id_rol)";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":nombre", $nombre_encrypted);
            $stmt->bindParam(":apellido_paterno", $apellido_paterno_encrypted);
            $stmt->bindParam(":apellido_materno", $apellido_materno_encrypted);
            $stmt->bindParam(":ci", $ci_encrypted);
            $stmt->bindParam(":telefono", $telefono);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":username", $username);
            $stmt->bindParam(":password_hash", $password_hash);
            $stmt->bindParam(":id_rol", $id_rol);

            if($stmt->execute()){
                return true;
            }else{
                error_log("Error en execute: " . implode(", ", $stmt->errorInfo()));
                return false;
            }
        } catch (Exception $e) {
            error_log("Error en registrarPersona: " . $e->getMessage());
            return false;
        }
    }


    public function editarPersona($id_persona, $nombre, $apellido_paterno, $apellido_materno, $telefono, $email, $id_rol){
        try {
            // Cifrar los datos sensibles
            $nombre_encrypted = $this->encrypt($nombre);
            $apellido_paterno_encrypted = $this->encrypt($apellido_paterno);
            $apellido_materno_encrypted = $this->encrypt($apellido_materno);

            $query = "UPDATE " . $this->table_name . " 
                 SET nombre = :nombre, 
                     apellido_paterno = :apellido_paterno, 
                     apellido_materno = :apellido_materno, 
                     telefono = :telefono, 
                     email = :email, 
                     id_rol = :id_rol 
                 WHERE id_persona = :id_persona";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":nombre", $nombre_encrypted);
            $stmt->bindParam(":apellido_paterno", $apellido_paterno_encrypted);
            $stmt->bindParam(":apellido_materno", $apellido_materno_encrypted);
            $stmt->bindParam(":telefono", $telefono);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":id_rol", $id_rol);
            $stmt->bindParam(":id_persona", $id_persona);

            if($stmt->execute()){
                return true;
            }else{
                error_log("Error en execute: " . implode(", ", $stmt->errorInfo()));
                return false;
            }
        } catch (Exception $e) {
            error_log("Error en editarPersona: " . $e->getMessage());
            return false;
        }
    }

    // sirve para actualizar la contraseña
    public function restablecerPassword($id_persona, $password){
        try {
            // Validar parámetros de entrada
            if (empty($id_persona) || empty($password)) {
                throw new InvalidArgumentException("ID de persona o contraseña vacíos");
            }

            // Validar que id_persona sea numérico
            if (!is_numeric($id_persona)) {
                throw new InvalidArgumentException("ID de persona no válido");
            }

            // Hash de la contraseña
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            if (!$password_hash) {
                throw new Exception("Error al generar el hash de la contraseña");
            }

            // CORRECCIÓN: Cambiar el punto por coma después de :password_hash
            $query = "UPDATE " . $this->table_name . " 
             SET password_hash = :password_hash,
             tiempo_verificacion = DATE_ADD(NOW(), INTERVAL 1 DAY),
             verificado = false
             WHERE id_persona = :id_persona";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":password_hash", $password_hash);
            $stmt->bindParam(":id_persona", $id_persona);

            if($stmt->execute()){
                // Verificar si realmente se actualizó algún registro
                if ($stmt->rowCount() > 0) {
                    return true;
                } else {
                    error_log("Advertencia: No se encontró el usuario con ID: " . $id_persona);
                    return false;
                }
            } else {
                error_log("Error en execute: " . implode(", ", $stmt->errorInfo()));
                return false;
            }

        } catch (InvalidArgumentException $e) {
            error_log("Error de validación en restablecerPassword: " . $e->getMessage()); // Corregido el nombre del método
            return false;
        } catch (Exception $e) {
            error_log("Error en restablecerPassword: " . $e->getMessage()); // Corregido el nombre del método
            return false;
        }
    }

    public function ampliarTiempoVerificacion($id_persona, $tiempo)
    {
        try {
            if (empty($id_persona) || empty($tiempo)) {
                throw new InvalidArgumentException("ID de persona o tiempo vacíos");
            }
            $query = "UPDATE {$this->table_name}
                  SET tiempo_verificacion = DATE_ADD(NOW(), INTERVAL $tiempo DAY)
                  WHERE id_persona = :id_persona";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id_persona", $id_persona);
            return $stmt->execute();

        } catch (Exception $e) {
            error_log("Error en ampliarTiempoVerificacion: " . $e->getMessage());
            return false;
        }
    }
    public function eliminarPersona($id_persona){
        try {
            if (empty($id_persona)) {
                throw new InvalidArgumentException("ID de persona o tiempo vacíos");
            }
            $query = "UPDATE {$this->table_name}
                  SET estado = 'inactivo'
                  WHERE id_persona = :id_persona";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id_persona", $id_persona);
            return $stmt->execute();

        } catch (Exception $e) {
            error_log("Error en ampliarTiempoVerificacion: " . $e->getMessage());
            return false;
        }
    }

}
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
        $query = "SELECT 
                p.*, 
                r.rol,
                GROUP_CONCAT(DISTINCT CONCAT('D', d.numero, '-P', d.piso) SEPARATOR ' / ') as departamentos_vinculados,
                GROUP_CONCAT(DISTINCT d.id_departamento) as ids_departamentos
              FROM " . $this->table_name . " p 
              JOIN rol r ON p.id_rol = r.id_rol 
              LEFT JOIN tiene_departamento td ON p.id_persona = td.id_persona AND td.estado = 'activo'
              LEFT JOIN departamento d ON td.id_departamento = d.id_departamento
              WHERE r.rol = 'Residente' AND p.estado = 'activo'
              GROUP BY p.id_persona
              ORDER BY p.nombre ASC";

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

                // Si no hay departamentos vinculados, establecer valores por defecto
                if (empty($fila['departamentos_vinculados'])) {
                    $fila['departamentos_vinculados'] = 'Sin departamento asignado';
                    $fila['ids_departamentos'] = '';
                }
            }

            return $resultados;
        }
        return [];
    }

    public function listarEliminados(){
        $query = "SELECT * FROM " . $this->table_name . " p, rol r 
                 WHERE p.id_rol = r.id_rol and estado = 'inactivo'";
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
                  SET estado = 'inactivo',
                      fecha_eliminado = NOW()
                  WHERE id_persona = :id_persona";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id_persona", $id_persona);
            return $stmt->execute();

        } catch (Exception $e) {
            error_log("Error en ampliarTiempoVerificacion: " . $e->getMessage());
            return false;
        }
    }

    public function restaurarPersona($id_persona)
    {
        try {
            if (empty($id_persona)) {
                throw new InvalidArgumentException("ID de persona o tiempo vacíos");
            }
            $query = "UPDATE {$this->table_name}
                  SET estado = 'activo',
                      fecha_eliminado = null
                  WHERE id_persona = :id_persona";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id_persona", $id_persona);
            return $stmt->execute();

        } catch (Exception $e) {
            error_log("Error en ampliarTiempoVerificacion: " . $e->getMessage());
            return false;
        }
    }

    // para el inicio de session
    public function login($username, $password) {
        // Determinar si el parámetro es un email o username
        $isEmail = filter_var($username, FILTER_VALIDATE_EMAIL);

        if ($isEmail) {
            // Buscar por email
            $query = "SELECT * FROM " . $this->table_name . " WHERE email = :username AND estado = 'activo'";
        } else {
            // Buscar por username
            $query = "SELECT * FROM " . $this->table_name . " WHERE username = :username AND estado = 'activo'";
        }

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            // Verificar contraseña
            if (password_verify($password, $row['password_hash'])) {
                // Descifrar los datos sensibles
                $row['nombre'] = $this->decrypt($row['nombre']);
                $row['apellido_paterno'] = $this->decrypt($row['apellido_paterno']);
                $row['apellido_materno'] = $this->decrypt($row['apellido_materno']);
                $row['ci'] = $this->decrypt($row['ci']);
                return $row;
            }
        }
        return false;
    }
    public function obtenerPersonaPorId($id_persona) {
        $query = "SELECT p.*, r.rol, r.descripcion as rol_descripcion 
          FROM " . $this->table_name . " p 
          INNER JOIN rol r ON p.id_rol = r.id_rol 
          WHERE p.id_persona = :id_persona";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_persona', $id_persona);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $persona = $stmt->fetch(PDO::FETCH_ASSOC);
            // Descifrar los datos sensibles
            $persona['nombre'] = $this->decrypt($persona['nombre']);
            $persona['apellido_paterno'] = $this->decrypt($persona['apellido_paterno']);
            $persona['apellido_materno'] = $this->decrypt($persona['apellido_materno']);
            $persona['ci'] = $this->decrypt($persona['ci']);
            return $persona;
        }
        return false;
    }
    public function verificacionPersona($id_persona) {
    // FALTA UN ESPACIO después de table_name
    $sql = "SELECT * FROM ".$this->table_name." WHERE id_persona = :id_persona and verificado = 1";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':id_persona', $id_persona);
    $stmt->execute();  
    if ($stmt->rowCount() > 0) {
        return true; 
    }
    return false;
    }
    public function tiempoVerificacionVencido($id_persona) {
        $sql = "SELECT * FROM ".$this->table_name." 
            WHERE id_persona = :id_persona 
            AND NOW() > tiempo_verificacion 
            AND verificado = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_persona', $id_persona);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
    public function successLogin($id_persona, $username) {
        try {
            // Registrar en historial_login como exitoso
            $sql_historial = "INSERT INTO historial_login (id_persona, fecha, estado) 
                         VALUES (:id_persona, NOW(), 'exitoso')";
            $stmt_historial = $this->db->prepare($sql_historial);
            $stmt_historial->bindParam(':id_persona', $id_persona);
            $stmt_historial->execute();

            // Resetear completamente el sistema de bloqueo
            $sql_reset = "UPDATE " . $this->table_name . " 
                     SET tiempo_bloqueo = NULL,
                         intentos_fallidos = 0
                     WHERE id_persona = :id_persona";
            $stmt_reset = $this->db->prepare($sql_reset);
            $stmt_reset->bindParam(':id_persona', $id_persona);
            $stmt_reset->execute();

            return true;
        } catch (Exception $e) {
            error_log("Error en successLogin: " . $e->getMessage());
            return false;
        }
    }
    public function errorLogin($username) {
        try {
            // Buscar usuario por username o email
            $sql_user = "SELECT id_persona FROM " . $this->table_name . " 
                    WHERE (username = :username OR email = :username) 
                    AND estado = 'activo'";
            $stmt_user = $this->db->prepare($sql_user);
            $stmt_user->bindParam(':username', $username);
            $stmt_user->execute();

            if ($stmt_user->rowCount() == 1) {
                $user = $stmt_user->fetch(PDO::FETCH_ASSOC);
                $id_persona = $user['id_persona'];

                // Registrar en historial_login como fallido
                $sql_historial = "INSERT INTO historial_login (id_persona, fecha, estado) 
                             VALUES (:id_persona, NOW(), 'fallido')";
                $stmt_historial = $this->db->prepare($sql_historial);
                $stmt_historial->bindParam(':id_persona', $id_persona);
                $stmt_historial->execute();

                // Incrementar contador de intentos fallidos
                $sql_update = "UPDATE " . $this->table_name . " 
                          SET intentos_fallidos = intentos_fallidos + 1
                          WHERE id_persona = :id_persona";
                $stmt_update = $this->db->prepare($sql_update);
                $stmt_update->bindParam(':id_persona', $id_persona);
                $stmt_update->execute();

                return true;
            }
            return false;
        } catch (Exception $e) {
            error_log("Error en errorLogin: " . $e->getMessage());
            return false;
        }
    }
    public function verificarTiempoBloqueo($username) {
        try {
            $sql = "SELECT id_persona, tiempo_bloqueo, estado,
                       TIMESTAMPDIFF(SECOND, NOW(), tiempo_bloqueo) as segundos_restantes
                FROM " . $this->table_name . " 
                WHERE (username = :username OR email = :username)";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            if ($stmt->rowCount() == 1) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                // Verificar si la cuenta está inactiva (bloqueo permanente)
                if ($user['estado'] == 'inactivo') {
                    return [
                        'bloqueado' => true,
                        'tiempo_restante' => 'PERMANENTE',
                        'segundos_restantes' => 0,
                        'bloqueo_permanente' => true
                    ];
                }

                // Verificar bloqueo temporal
                if ($user['tiempo_bloqueo'] !== null && $user['segundos_restantes'] > 0) {
                    $segundos_restantes = $user['segundos_restantes'];

                    return [
                        'bloqueado' => true,
                        'tiempo_restante' => $this->formatearTiempo($segundos_restantes),
                        'segundos_restantes' => $segundos_restantes,
                        'bloqueo_permanente' => false
                    ];
                }
            }

            return [
                'bloqueado' => false,
                'tiempo_restante' => '0 segundos',
                'segundos_restantes' => 0,
                'bloqueo_permanente' => false
            ];

        } catch (Exception $e) {
            error_log("Error en verificarTiempoBloqueo: " . $e->getMessage());
            return [
                'bloqueado' => false,
                'tiempo_restante' => '0 segundos',
                'segundos_restantes' => 0,
                'bloqueo_permanente' => false
            ];
        }
    }
    public function verificarYBloquearUsuario($username) {
        try {
            $sql = "SELECT id_persona, intentos_fallidos, tiempo_bloqueo 
                FROM " . $this->table_name . " 
                WHERE (username = :username OR email = :username) 
                AND estado = 'activo'";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            if ($stmt->rowCount() == 1) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                $intentos_fallidos = $user['intentos_fallidos'];

                // Si tiene 3 o más intentos fallidos, aplicar bloqueo
                if ($intentos_fallidos >= 3) {
                    $nivel_bloqueo = min(($intentos_fallidos - 2), 4); // 1, 2, 3 o 4

                    // Niveles de bloqueo progresivo
                    if ($nivel_bloqueo <= 3) {
                        // Bloqueos temporales
                        $tiempo_bloqueo_segundos = 30 * pow(2, $nivel_bloqueo - 1); // 30, 60, 120 segundos

                        $sql_bloqueo = "UPDATE " . $this->table_name . " 
                                   SET tiempo_bloqueo = DATE_ADD(NOW(), INTERVAL :tiempo SECOND)
                                   WHERE id_persona = :id_persona";

                        $stmt_bloqueo = $this->db->prepare($sql_bloqueo);
                        $stmt_bloqueo->bindParam(':tiempo', $tiempo_bloqueo_segundos);
                        $stmt_bloqueo->bindParam(':id_persona', $user['id_persona']);
                        $stmt_bloqueo->execute();

                        return [
                            'bloqueado' => true,
                            'tiempo_restante' => $this->formatearTiempo($tiempo_bloqueo_segundos),
                            'segundos_restantes' => $tiempo_bloqueo_segundos, // ← Asegurar que se envíe esto
                            'nivel_bloqueo' => $nivel_bloqueo,
                            'bloqueo_permanente' => false
                        ];
                    } else {
                        // CUARTO NIVEL: Bloqueo permanente
                        $sql_bloqueo_permanente = "UPDATE " . $this->table_name . " 
                                              SET estado = 'inactivo',
                                                  tiempo_bloqueo = NULL,
                                                  fecha_eliminado = NOW()
                                              WHERE id_persona = :id_persona";

                        $stmt_bloqueo = $this->db->prepare($sql_bloqueo_permanente);
                        $stmt_bloqueo->bindParam(':id_persona', $user['id_persona']);
                        $stmt_bloqueo->execute();

                        return [
                            'bloqueado' => true,
                            'tiempo_restante' => 'PERMANENTE',
                            'segundos_restantes' => 0,
                            'nivel_bloqueo' => $nivel_bloqueo,
                            'bloqueo_permanente' => true
                        ];
                    }
                }
            }

            return [
                'bloqueado' => false,
                'tiempo_restante' => '0 segundos',
                'segundos_restantes' => 0,
                'nivel_bloqueo' => 0,
                'bloqueo_permanente' => false
            ];

        } catch (Exception $e) {
            error_log("Error en verificarYBloquearUsuario: " . $e->getMessage());
            return [
                'bloqueado' => false,
                'tiempo_restante' => '0 segundos',
                'segundos_restantes' => 0,
                'nivel_bloqueo' => 0,
                'bloqueo_permanente' => false
            ];
        }
    }
    public function verificarTiempoBloqueoPorId($id_persona) {
        try {
            $sql = "SELECT tiempo_bloqueo, estado,
                       TIMESTAMPDIFF(SECOND, NOW(), tiempo_bloqueo) as segundos_restantes
                FROM " . $this->table_name . " 
                WHERE id_persona = :id_persona";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_persona', $id_persona);
            $stmt->execute();

            if ($stmt->rowCount() == 1) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                // Verificar si la cuenta está inactiva (bloqueo permanente)
                if ($user['estado'] == 'inactivo') {
                    return [
                        'bloqueado' => true,
                        'tiempo_restante' => 'PERMANENTE',
                        'segundos_restantes' => 0,
                        'bloqueo_permanente' => true
                    ];
                }

                // Verificar bloqueo temporal
                if ($user['tiempo_bloqueo'] > NOW()) {
                    $segundos_restantes = $user['segundos_restantes'];

                    return [
                        'bloqueado' => true,
                        'tiempo_restante' => $this->formatearTiempo($segundos_restantes),
                        'segundos_restantes' => $segundos_restantes,
                        'bloqueo_permanente' => false
                    ];
                }
            }

            return [
                'bloqueado' => false,
                'tiempo_restante' => '0 segundos',
                'segundos_restantes' => 0,
                'bloqueo_permanente' => false
            ];

        } catch (Exception $e) {
            error_log("Error en verificarTiempoBloqueoPorId: " . $e->getMessage());
            return [
                'bloqueado' => false,
                'tiempo_restante' => '0 segundos',
                'segundos_restantes' => 0,
                'bloqueo_permanente' => false
            ];
        }
    }
    // Método auxiliar para formatear el tiempo
    private function formatearTiempo($segundos) {
        if ($segundos < 60) {
            return $segundos . ' segundos';
        } else {
            $minutos = floor($segundos / 60);
            $segundos_restantes = $segundos % 60;
            if ($segundos_restantes > 0) {
                return $minutos . ' minuto' . ($minutos > 1 ? 's' : '') .
                    ' y ' . $segundos_restantes . ' segundo' . ($segundos_restantes > 1 ? 's' : '');
            } else {
                return $minutos . ' minuto' . ($minutos > 1 ? 's' : '');
            }
        }
    }
public function cambiarPassword($id_persona, $password){
    try {
        // Validar parámetros de entrada
        if (empty($id_persona) || empty($password)) {
            throw new InvalidArgumentException("ID de persona y contraseña son obligatorios");
        }

        // Hash de la contraseña
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        if (!$password_hash) {
            throw new Exception("Error al generar el hash de la contraseña");
        }

        // VERIFICA EL NOMBRE REAL DE TU COLUMNA EN LA BASE DE DATOS
        
        // Si tu columna realmente se llama 'password_hash', déjalo así:
        
        $query = "UPDATE " . $this->table_name . " 
                 SET password_hash = :password_hash,
                 verificado = true
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
            $errorInfo = $stmt->errorInfo();
            error_log("Error en execute: " . implode(", ", $errorInfo));
            return false;
        }

    } catch (InvalidArgumentException $e) {
        error_log("Error de validación en cambiarPassword: " . $e->getMessage());
        return false;
    } catch (Exception $e) {
        error_log("Error en cambiarPassword: " . $e->getMessage());
        return false;
    }
}
}
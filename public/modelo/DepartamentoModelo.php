<?php

class DepartamentoModelo{
    private $db;
    private $table_departamento = 'departamento';
    private $table_tiene_departamento = 'tiene_departamento';
    private $table_persona = 'persona';
    private $table_medidor = 'medidor';
    private $table_servicio = 'servicio';
    private $table_rol = 'rol';
    private $encryption_key;

    public function __construct($db){
        $this->db = $db;
        $this->encryption_key = '1A3F6C9E2B5D8A0C7E4F1A2B3C8D5E6F7A1B2C3D4E5F6A7B8C9D0E1F2A3B4C5D6';

        // Asegurar que la clave tenga exactamente 32 bytes
        if (strlen($this->encryption_key) < 32) {
            $this->encryption_key = str_pad($this->encryption_key, 32, "\0");
        } elseif (strlen($this->encryption_key) > 32) {
            $this->encryption_key = substr($this->encryption_key, 0, 32);
        }
    }

    // Metodo para descifrar datos (igual que en PersonaModelo)
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

    public function listarDepartamento(){
        $sql = "SELECT 
                    d.*,
                    COUNT(DISTINCT td.id_persona) as total_residentes,
                    COUNT(DISTINCT m.id_medidor) as total_medidores
                FROM " . $this->table_departamento . " d
                LEFT JOIN " . $this->table_tiene_departamento . " td 
                    ON d.id_departamento = td.id_departamento AND td.estado = 'activo'
                LEFT JOIN " . $this->table_persona . " p 
                    ON td.id_persona = p.id_persona AND p.estado = 'activo'
                LEFT JOIN " . $this->table_rol . " r 
                    ON p.id_rol = r.id_rol AND r.rol = 'Residente'
                LEFT JOIN " . $this->table_medidor . " m 
                    ON d.id_departamento = m.id_departamento AND m.estado = 'activo'
                LEFT JOIN " . $this->table_servicio . " s 
                    ON m.id_servicio = s.id_servicio
                GROUP BY d.id_departamento
                ORDER BY d.piso, d.numero";

        $resultado = $this->db->prepare($sql);
        $resultado->execute();
        return $resultado->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para obtener información específica de un departamento
    public function obtenerDepartamentoCompleto($id_departamento){
        try {
            // Primero obtener los IDs de residentes y datos básicos
            $sql = "SELECT 
                    d.*,
                    GROUP_CONCAT(DISTINCT p.id_persona) as residentes_ids,
                    GROUP_CONCAT(DISTINCT CONCAT(m.id_medidor, '|', s.nombre, '|', m.codigo, '|', s.unidad_medida, '|', s.costo_unitario, '|', m.estado, '|', m.fecha_instalacion) SEPARATOR '; ') as medidores_info
                FROM " . $this->table_departamento . " d
                LEFT JOIN " . $this->table_tiene_departamento . " td 
                    ON d.id_departamento = td.id_departamento AND td.estado = 'activo'
                LEFT JOIN " . $this->table_persona . " p 
                    ON td.id_persona = p.id_persona AND p.estado = 'activo'
                LEFT JOIN " . $this->table_rol . " r 
                    ON p.id_rol = r.id_rol
                LEFT JOIN " . $this->table_medidor . " m 
                    ON d.id_departamento = m.id_departamento
                LEFT JOIN " . $this->table_servicio . " s 
                    ON m.id_servicio = s.id_servicio
                WHERE d.id_departamento = :id_departamento
                GROUP BY d.id_departamento";

            $resultado = $this->db->prepare($sql);
            $resultado->bindParam(':id_departamento', $id_departamento);
            $resultado->execute();

            $departamento = $resultado->fetch(PDO::FETCH_ASSOC);

            if (!$departamento) {
                return null;
            }

            // Obtener y procesar residentes
            $departamento['residentes'] = [];
            if (!empty($departamento['residentes_ids'])) {
                $residentes_ids = explode(',', $departamento['residentes_ids']);
                $departamento['residentes'] = $this->obtenerDatosResidentes($residentes_ids);
            }

            // Procesar medidores
            $departamento['medidores'] = [];
            if (!empty($departamento['medidores_info'])) {
                $medidores = explode('; ', $departamento['medidores_info']);
                foreach ($medidores as $medidor) {
                    if (!empty($medidor)) {
                        $datos = explode('|', $medidor);
                        if (count($datos) >= 7) {
                            $departamento['medidores'][] = [
                                'id_medidor' => $datos[0],
                                'servicio' => $datos[1],
                                'codigo' => $datos[2],
                                'unidad_medida' => $datos[3],
                                'costo_unitario' => $datos[4],
                                'estado_medidor' => $datos[5],
                                'fecha_instalacion' => $datos[6]
                            ];
                        }
                    }
                }
            }

            // Eliminar campos temporales
            unset($departamento['residentes_ids'], $departamento['medidores_info']);

            return $departamento;

        } catch (Exception $e) {
            error_log("Error en obtenerDepartamentoCompleto: " . $e->getMessage());
            return null;
        }
    }

    // Método para obtener datos de residentes con decrypt
    private function obtenerDatosResidentes($residentes_ids) {
        if (empty($residentes_ids)) {
            return [];
        }

        $placeholders = str_repeat('?,', count($residentes_ids) - 1) . '?';
        $sql = "SELECT p.id_persona, p.nombre, p.apellido_paterno, p.apellido_materno, p.email, p.telefono, r.rol
                FROM " . $this->table_persona . " p
                LEFT JOIN " . $this->table_rol . " r ON p.id_rol = r.id_rol
                WHERE p.id_persona IN ($placeholders) AND p.estado = 'activo'";

        $resultado = $this->db->prepare($sql);
        $resultado->execute($residentes_ids);
        $residentes = $resultado->fetchAll(PDO::FETCH_ASSOC);

        // Aplicar decrypt a los datos cifrados
        foreach ($residentes as &$residente) {
            $residente['nombre'] = $this->decrypt($residente['nombre']);
            $residente['apellido_paterno'] = $this->decrypt($residente['apellido_paterno']);
            $residente['apellido_materno'] = $this->decrypt($residente['apellido_materno']);
            $residente['nombre_completo'] = trim(
                $residente['nombre'] . ' ' .
                $residente['apellido_paterno'] . ' ' .
                ($residente['apellido_materno'] ?? '')
            );
        }

        return $residentes;
    }

    // Métodos existentes se mantienen igual...
    public function verificarDepartamento($numero){
        $sql = "SELECT * FROM " . $this->table_departamento . " WHERE numero = :numero";
        $resultado = $this->db->prepare($sql);
        $resultado->bindParam(':numero', $numero);
        $resultado->execute();
        return $resultado->rowCount() > 0;
    }

    public function verificarDepartamentoExcluyendo($numero, $id_excluir) {
        $sql = "SELECT id_departamento FROM " . $this->table_departamento . " WHERE numero = :numero AND id_departamento != :id_excluir";
        $resultado = $this->db->prepare($sql);
        $resultado->bindParam(':numero', $numero);
        $resultado->bindParam(':id_excluir', $id_excluir, PDO::PARAM_INT);
        $resultado->execute();
        return $resultado->rowCount() > 0;
    }

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

    public function asignarPersonasDepartamento($id_departamento, $personas_ids){
        try {
            $this->db->beginTransaction();

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
}
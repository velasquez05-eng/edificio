<?php

class AreaComunModelo
{
    private $db;
    private $table_name = "area_comun";
    private $table_reservas = "reserva_area_comun";
    private $table_persona = "persona";
    private $encryption_key;

    public function __construct($db, $encryption_key = null){
        $this->db = $db;
        $this->encryption_key = $encryption_key ?: '1A3F6C9E2B5D8A0C7E4F1A2B3C8D5E6F7A1B2C3D4E5F6A7B8C9D0E1F2A3B4C5D6';

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

    // Obtener lista de todas las areas comunes
    public function listarAreas(){
        try {
            $sql = "SELECT * FROM $this->table_name WHERE estado != 'eliminado' ORDER BY nombre";
            $stmt = $this->db->prepare($sql);

            if (!$stmt->execute()) {
                throw new Exception("Error al ejecutar consulta");
            }

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en listarAreas: " . $e->getMessage());
            return false;
        }
    }

    // Registrar nueva area comun
    public function registrarArea($nombre, $descripcion, $capacidad, $estado = 'disponible'){
        try {
            if (empty($nombre) || empty($capacidad)) {
                throw new InvalidArgumentException("Nombre y capacidad son obligatorios");
            }

            if (!is_numeric($capacidad) || $capacidad < 1) {
                throw new InvalidArgumentException("Capacidad debe ser un numero mayor a 0");
            }

            $sql = "INSERT INTO $this->table_name (nombre, descripcion, capacidad, estado) 
                    VALUES (:nombre, :descripcion, :capacidad, :estado)";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':capacidad', $capacidad, PDO::PARAM_INT);
            $stmt->bindParam(':estado', $estado);

            if ($stmt->execute()) {
                return $this->db->lastInsertId();
            } else {
                throw new Exception("Error al ejecutar insercion");
            }

        } catch (Exception $e) {
            error_log("Error en registrarArea: " . $e->getMessage());
            return false;
        }
    }

    // Editar area comun existente
    public function editarArea($id_area, $nombre, $descripcion, $capacidad, $estado){
        try {
            if (empty($id_area) || !is_numeric($id_area)) {
                throw new InvalidArgumentException("ID de area no valido");
            }

            if (empty($nombre) || empty($capacidad)) {
                throw new InvalidArgumentException("Nombre y capacidad son obligatorios");
            }

            if (!is_numeric($capacidad) || $capacidad < 1) {
                throw new InvalidArgumentException("Capacidad debe ser un numero mayor a 0");
            }

            $sql = "UPDATE $this->table_name 
                    SET nombre = :nombre, 
                        descripcion = :descripcion, 
                        capacidad = :capacidad, 
                        estado = :estado 
                    WHERE id_area = :id_area AND estado != 'eliminado'";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':capacidad', $capacidad, PDO::PARAM_INT);
            $stmt->bindParam(':estado', $estado);
            $stmt->bindParam(':id_area', $id_area, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return $stmt->rowCount() > 0;
            } else {
                throw new Exception("Error al ejecutar actualizacion");
            }

        } catch (Exception $e) {
            error_log("Error en editarArea: " . $e->getMessage());
            return false;
        }
    }

    // Eliminar area comun (eliminacion logica)
    public function eliminarArea($id_area){
        try {
            if (empty($id_area) || !is_numeric($id_area)) {
                throw new InvalidArgumentException("ID de area no valido");
            }

            $sql = "UPDATE $this->table_name 
                    SET estado = 'eliminado' 
                    WHERE id_area = :id_area";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_area', $id_area, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return $stmt->rowCount() > 0;
            } else {
                throw new Exception("Error al ejecutar eliminacion");
            }

        } catch (Exception $e) {
            error_log("Error en eliminarArea: " . $e->getMessage());
            return false;
        }
    }

    // Obtener todas las reservas de un area especifica
    public function obtenerReservasPorArea($id_area) {
        try {
            if (empty($id_area) || !is_numeric($id_area)) {
                throw new InvalidArgumentException("ID de area no valido");
            }

            $sql = "SELECT 
                        rac.*,
                        p.nombre as nombre_persona_encrypted,
                        p.apellido_paterno as apellido_persona_encrypted,
                        p.apellido_materno,
                        p.ci as ci_encrypted,
                        p.email
                    FROM $this->table_reservas rac
                    JOIN $this->table_persona p ON rac.id_persona = p.id_persona
                    WHERE rac.id_area = :id_area
                    ORDER BY rac.fecha_reserva DESC, rac.hora_inicio";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_area', $id_area, PDO::PARAM_INT);

            if (!$stmt->execute()) {
                throw new Exception("Error al ejecutar consulta");
            }

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Descifrar los datos sensibles de personas
            foreach ($resultados as &$fila) {
                $fila['nombre_persona'] = $this->decrypt($fila['nombre_persona_encrypted']);
                $fila['apellido_persona'] = $this->decrypt($fila['apellido_persona_encrypted']);
                $fila['ci'] = $this->decrypt($fila['ci_encrypted']);

                // Limpiar campos encriptados
                unset($fila['nombre_persona_encrypted']);
                unset($fila['apellido_persona_encrypted']);
                unset($fila['ci_encrypted']);
            }

            return $resultados;

        } catch (Exception $e) {
            error_log("Error en obtenerReservasPorArea: " . $e->getMessage());
            return false;
        }
    }

    // Actualizar el estado de una reserva (confirmar, cancelar, etc.)
    public function actualizarEstadoReserva($id_area, $fecha_reserva, $hora_inicio, $estado) {
        try {
            if (empty($id_area) || !is_numeric($id_area)) {
                throw new InvalidArgumentException("ID de area no valido");
            }

            if (empty($fecha_reserva) || empty($hora_inicio)) {
                throw new InvalidArgumentException("Fecha y hora de inicio son obligatorios");
            }

            $sql = "UPDATE $this->table_reservas 
                    SET estado = :estado 
                    WHERE id_area = :id_area 
                    AND fecha_reserva = :fecha_reserva 
                    AND hora_inicio = :hora_inicio";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':estado', $estado);
            $stmt->bindParam(':id_area', $id_area, PDO::PARAM_INT);
            $stmt->bindParam(':fecha_reserva', $fecha_reserva);
            $stmt->bindParam(':hora_inicio', $hora_inicio);

            if ($stmt->execute()) {
                return $stmt->rowCount() > 0;
            } else {
                throw new Exception("Error al ejecutar actualizacion");
            }

        } catch (Exception $e) {
            error_log("Error en actualizarEstadoReserva: " . $e->getMessage());
            return false;
        }
    }
}
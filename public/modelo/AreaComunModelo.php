<?php

class AreaComunModelo
{
    private $db;
    private $table_name = "area_comun";
    private $table_reservas = "reserva_area_comun";
    private $table_persona = "persona";
    private $table_comunicado = "comunicado";
    private $table_departamento = "departamento";
    private $table_tiene_departamento = "tiene_departamento";
    private $encryption_key;

    public function __construct($db, $encryption_key = null)
    {
        $this->db = $db;
        $this->encryption_key = $encryption_key ?: '1A3F6C9E2B5D8A0C7E4F1A2B3C8D5E6F7A1B2C3D4E5F6A7B8C9D0E1F2A3B4C5D6';

        if (strlen($this->encryption_key) < 32) {
            $this->encryption_key = str_pad($this->encryption_key, 32, "\0");
        } elseif (strlen($this->encryption_key) > 32) {
            $this->encryption_key = substr($this->encryption_key, 0, 32);
        }
    }

    // Métodos de cifrado/descifrado
    private function encrypt($data)
    {
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

    private function decrypt($encrypted_data)
    {
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

    // Métodos para áreas comunes
    public function listarAreas()
    {
        try {
            $sql = "SELECT * FROM $this->table_name WHERE estado != 'eliminado' ORDER BY nombre";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en listarAreas: " . $e->getMessage());
            return false;
        }
    }

    public function registrarArea($nombre, $descripcion, $capacidad, $estado = 'disponible', $costo_reserva)
    {
        try {
            if (empty($nombre) || empty($capacidad)) {
                throw new InvalidArgumentException("Nombre y capacidad son obligatorios");
            }

            if (!is_numeric($capacidad) || $capacidad < 1) {
                throw new InvalidArgumentException("Capacidad debe ser un número mayor a 0");
            }

            $sql = "INSERT INTO $this->table_name (nombre, descripcion, capacidad, estado, costo_reserva) 
                    VALUES (:nombre, :descripcion, :capacidad, :estado, :costo_reserva)";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':capacidad', $capacidad, PDO::PARAM_INT);
            $stmt->bindParam(':estado', $estado);
            $stmt->bindParam(':costo_reserva', $costo_reserva);

            if ($stmt->execute()) {
                return $this->db->lastInsertId();
            } else {
                throw new Exception("Error al ejecutar inserción");
            }
        } catch (Exception $e) {
            error_log("Error en registrarArea: " . $e->getMessage());
            return false;
        }
    }

    public function editarArea($id_area, $nombre, $descripcion, $capacidad, $costo_reserva, $estado)
    {
        try {
            if (empty($id_area) || !is_numeric($id_area)) {
                throw new InvalidArgumentException("ID de área no válido");
            }

            if (empty($nombre) || empty($capacidad)) {
                throw new InvalidArgumentException("Nombre y capacidad son obligatorios");
            }

            if (!is_numeric($capacidad) || $capacidad < 1) {
                throw new InvalidArgumentException("Capacidad debe ser un número mayor a 0");
            }

            $sql = "UPDATE $this->table_name 
                    SET nombre = :nombre, 
                        descripcion = :descripcion, 
                        capacidad = :capacidad,
                        costo_reserva = :costo_reserva,
                        estado = :estado 
                    WHERE id_area = :id_area AND estado != 'eliminado'";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':capacidad', $capacidad, PDO::PARAM_INT);
            $stmt->bindParam(':costo_reserva', $costo_reserva);
            $stmt->bindParam(':estado', $estado);
            $stmt->bindParam(':id_area', $id_area, PDO::PARAM_INT);

            return $stmt->execute() && $stmt->rowCount() > 0;
        } catch (Exception $e) {
            error_log("Error en editarArea: " . $e->getMessage());
            return false;
        }
    }

    public function eliminarArea($id_area)
    {
        try {
            if (empty($id_area) || !is_numeric($id_area)) {
                throw new InvalidArgumentException("ID de área no válido");
            }

            $sql = "UPDATE $this->table_name 
                    SET estado = 'eliminado' 
                    WHERE id_area = :id_area";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_area', $id_area, PDO::PARAM_INT);

            return $stmt->execute() && $stmt->rowCount() > 0;
        } catch (Exception $e) {
            error_log("Error en eliminarArea: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerAreaPorId($id_area)
    {
        try {
            if (empty($id_area) || !is_numeric($id_area)) {
                throw new InvalidArgumentException("ID de área no válido");
            }

            $sql = "SELECT * FROM $this->table_name WHERE id_area = :id_area AND estado != 'eliminado'";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_area', $id_area, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerAreaPorId: " . $e->getMessage());
            return false;
        }
    }

    // Métodos para mantenimiento
    public function programarMantenimiento($id_persona, $id_area, $fecha_inicio, $fecha_fin)
    {
        try {
            if (empty($id_area) || !is_numeric($id_area)) {
                throw new InvalidArgumentException("ID de área no válido");
            }

            $sql_nombre = "SELECT nombre FROM $this->table_name WHERE id_area = :id_area";
            $stmt_nombre = $this->db->prepare($sql_nombre);
            $stmt_nombre->bindParam(':id_area', $id_area, PDO::PARAM_INT);
            $stmt_nombre->execute();
            $area = $stmt_nombre->fetch(PDO::FETCH_ASSOC);

            if (!$area) {
                throw new Exception("Área no encontrada");
            }

            $nombre_area = $area['nombre'];
            $this->db->beginTransaction();

            $sql_update = "UPDATE $this->table_name 
                          SET fecha_inicio_mantenimiento = :fecha_inicio,
                              fecha_fin_mantenimiento = :fecha_fin,
                              estado = 'mantenimiento'
                          WHERE id_area = :id_area AND estado != 'eliminado'";

            $stmt_update = $this->db->prepare($sql_update);
            $stmt_update->bindParam(':fecha_inicio', $fecha_inicio);
            $stmt_update->bindParam(':fecha_fin', $fecha_fin);
            $stmt_update->bindParam(':id_area', $id_area, PDO::PARAM_INT);

            if (!$stmt_update->execute()) {
                throw new Exception("Error al ejecutar actualización de mantenimiento");
            }

            $titulo = "Mantenimiento del Area: " . $nombre_area;
            $contenido = "El área " . $nombre_area . " entró en mantenimiento desde la fecha " . $fecha_inicio . " hasta la fecha " . $fecha_fin . ", conforme a lo establecido en el reglamento del edificio";

            $sql_comunicado = "INSERT INTO $this->table_comunicado 
                              (id_persona, titulo, contenido, fecha_publicacion, fecha_expiracion, prioridad, estado, tipo_audiencia) 
                              VALUES (:id_persona, :titulo, :contenido, NOW(), :fecha_fin, 'alta', 'publicado', 'Todos')";

            $stmt_comunicado = $this->db->prepare($sql_comunicado);
            $stmt_comunicado->bindParam(':id_persona', $id_persona, PDO::PARAM_INT);
            $stmt_comunicado->bindParam(':titulo', $titulo);
            $stmt_comunicado->bindParam(':contenido', $contenido);
            $stmt_comunicado->bindParam(':fecha_fin', $fecha_fin);

            if (!$stmt_comunicado->execute()) {
                throw new Exception("Error al insertar comunicado");
            }

            $this->db->commit();
            return $stmt_update->rowCount() > 0;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Error en programarMantenimiento: " . $e->getMessage());
            return false;
        }
    }

    public function finalizarMantenimiento($id_area)
    {
        try {
            if (empty($id_area) || !is_numeric($id_area)) {
                throw new InvalidArgumentException("ID de área no válido");
            }

            $sql = "UPDATE $this->table_name 
                    SET fecha_inicio_mantenimiento = null,
                        fecha_fin_mantenimiento = null,
                        estado = 'disponible'
                    WHERE id_area = :id_area AND estado != 'eliminado'";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_area', $id_area, PDO::PARAM_INT);
            return $stmt->execute() && $stmt->rowCount() > 0;
        } catch (Exception $e) {
            error_log("Error en finalizarMantenimiento: " . $e->getMessage());
            return false;
        }
    }

    // Métodos para reservas
    public function registrarReserva($id_persona, $id_area, $fecha_reserva, $hora_inicio, $hora_fin, $motivo, $estado)
    {
        if (!$this->db) {
            error_log("Error: Conexión a BD no disponible en registrarReserva");
            return false;
        }

        $sql = "INSERT INTO $this->table_reservas (id_persona, id_area, fecha_reserva, hora_inicio, hora_fin, motivo, estado) 
                VALUES (:id_persona, :id_area, :fecha_reserva, :hora_inicio, :hora_fin, :motivo, :estado)";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_persona', $id_persona, PDO::PARAM_INT);
            $stmt->bindParam(':id_area', $id_area, PDO::PARAM_INT);
            $stmt->bindParam(':fecha_reserva', $fecha_reserva, PDO::PARAM_STR);
            $stmt->bindParam(':hora_inicio', $hora_inicio, PDO::PARAM_STR);
            $stmt->bindParam(':hora_fin', $hora_fin, PDO::PARAM_STR);
            $stmt->bindParam(':motivo', $motivo, PDO::PARAM_STR);
            $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en registrarReserva: " . $e->getMessage());
            return false;
        }
    }

    public function actualizarEstadoReserva($id_persona, $id_area, $fecha_reserva, $hora_inicio, $nuevo_estado)
    {
        try {
            if (empty($id_area) || !is_numeric($id_area)) {
                throw new InvalidArgumentException("ID de área no válido");
            }

            if (empty($fecha_reserva) || empty($hora_inicio)) {
                throw new InvalidArgumentException("Fecha y hora de inicio son obligatorios");
            }

            $sql = "UPDATE $this->table_reservas 
                    SET estado = :estado 
                    WHERE id_persona = :id_persona
                    AND id_area = :id_area 
                    AND fecha_reserva = :fecha_reserva 
                    AND hora_inicio = :hora_inicio";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':estado', $nuevo_estado);
            $stmt->bindParam(':id_persona', $id_persona, PDO::PARAM_INT);
            $stmt->bindParam(':id_area', $id_area, PDO::PARAM_INT);
            $stmt->bindParam(':fecha_reserva', $fecha_reserva);
            $stmt->bindParam(':hora_inicio', $hora_inicio);

            return $stmt->execute() && $stmt->rowCount() > 0;
        } catch (Exception $e) {
            error_log("Error en actualizarEstadoReserva: " . $e->getMessage());
            return false;
        }
    }

    public function modificarReserva($id_persona, $id_area, $fecha_reserva_original, $hora_inicio_original, $fecha_reserva, $hora_inicio, $hora_fin)
    {
        try {
            if (!$this->db) {
                throw new Exception("Conexión a la base de datos no disponible");
            }

            $hora_inicio_mysql = (strpos($hora_inicio, ':') === false) ? $hora_inicio . ':00' : $hora_inicio;
            $hora_fin_mysql = (strpos($hora_fin, ':') === false) ? $hora_fin . ':00' : $hora_fin;
            $hora_inicio_original_mysql = $hora_inicio_original;

            $sql = "UPDATE reserva_area_comun 
                    SET fecha_reserva = :fecha_reserva, 
                        hora_inicio = :hora_inicio, 
                        hora_fin = :hora_fin
                    WHERE id_persona = :id_persona 
                    AND id_area = :id_area 
                    AND fecha_reserva = :fecha_reserva_original 
                    AND hora_inicio = :hora_inicio_original 
                    AND estado = 'pendiente'";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':fecha_reserva', $fecha_reserva);
            $stmt->bindParam(':hora_inicio', $hora_inicio_mysql);
            $stmt->bindParam(':hora_fin', $hora_fin_mysql);
            $stmt->bindParam(':id_persona', $id_persona, PDO::PARAM_INT);
            $stmt->bindParam(':id_area', $id_area, PDO::PARAM_INT);
            $stmt->bindParam(':fecha_reserva_original', $fecha_reserva_original);
            $stmt->bindParam(':hora_inicio_original', $hora_inicio_original_mysql);

            $resultado = $stmt->execute();
            return $resultado && $stmt->rowCount() > 0;
        } catch (Exception $e) {
            error_log("Error en modificarReserva: " . $e->getMessage());
            return false;
        }
    }

    public function cancelarReservaUsuario($id_persona, $id_area, $fecha_reserva, $hora_inicio)
    {
        try {
            $hora_inicio_mysql = $hora_inicio;

            $sql = "UPDATE reserva_area_comun 
                    SET estado = 'cancelada'
                    WHERE id_persona = :id_persona 
                    AND id_area = :id_area 
                    AND fecha_reserva = :fecha_reserva 
                    AND hora_inicio = :hora_inicio 
                    AND estado = 'pendiente'";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_persona', $id_persona, PDO::PARAM_INT);
            $stmt->bindParam(':id_area', $id_area, PDO::PARAM_INT);
            $stmt->bindParam(':fecha_reserva', $fecha_reserva);
            $stmt->bindParam(':hora_inicio', $hora_inicio_mysql);

            $resultado = $stmt->execute();
            return $resultado && $stmt->rowCount() > 0;
        } catch (Exception $e) {
            error_log("Error en cancelarReservaUsuario: " . $e->getMessage());
            return false;
        }
    }

    // Métodos de verificación
    public function verificarDisponibilidad($id_area, $fecha, $hora_inicio, $hora_fin)
    {
        if (!$this->db) {
            error_log("Error: Conexión a BD no disponible en verificarDisponibilidad");
            return false;
        }

        $sql = "SELECT COUNT(*) as count FROM $this->table_reservas 
                WHERE id_area = :id_area AND fecha_reserva = :fecha 
                AND estado != 'cancelada'
                AND (
                    (hora_inicio < :hora_fin AND hora_fin > :hora_inicio) OR
                    (hora_inicio >= :hora_inicio2 AND hora_inicio < :hora_fin2) OR
                    (hora_fin > :hora_inicio3 AND hora_fin <= :hora_fin3)
                )";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_area', $id_area, PDO::PARAM_INT);
            $stmt->bindParam(':fecha', $fecha, PDO::PARAM_STR);
            $stmt->bindParam(':hora_inicio', $hora_inicio, PDO::PARAM_STR);
            $stmt->bindParam(':hora_fin', $hora_fin, PDO::PARAM_STR);
            $stmt->bindParam(':hora_inicio2', $hora_inicio, PDO::PARAM_STR);
            $stmt->bindParam(':hora_fin2', $hora_fin, PDO::PARAM_STR);
            $stmt->bindParam(':hora_inicio3', $hora_inicio, PDO::PARAM_STR);
            $stmt->bindParam(':hora_fin3', $hora_fin, PDO::PARAM_STR);

            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['count'] == 0;
        } catch (PDOException $e) {
            error_log("Error en verificarDisponibilidad: " . $e->getMessage());
            return false;
        }
    }

    public function verificarDisponibilidadModificacion($id_area, $fecha, $hora_inicio, $hora_fin, $fecha_excluir, $hora_inicio_excluir)
    {
        try {
            $hora_inicio_mysql = (strlen($hora_inicio) == 5) ? $hora_inicio : $hora_inicio . ':00';
            $hora_fin_mysql = (strlen($hora_fin) == 5) ? $hora_fin : $hora_fin . ':00';
            $hora_inicio_excluir_mysql = (strlen($hora_inicio_excluir) == 5) ? $hora_inicio_excluir : $hora_inicio_excluir . ':00';

            $sql = "SELECT COUNT(*) as count
                    FROM $this->table_reservas 
                    WHERE id_area = :id_area 
                    AND fecha_reserva = :fecha 
                    AND estado IN ('pendiente', 'confirmada')
                    AND NOT (fecha_reserva = :fecha_excluir AND hora_inicio = :hora_inicio_excluir)
                    AND (:hora_inicio < hora_fin AND :hora_fin > hora_inicio)";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_area', $id_area, PDO::PARAM_INT);
            $stmt->bindParam(':fecha', $fecha);
            $stmt->bindParam(':hora_inicio', $hora_inicio_mysql);
            $stmt->bindParam(':hora_fin', $hora_fin_mysql);
            $stmt->bindParam(':fecha_excluir', $fecha_excluir);
            $stmt->bindParam(':hora_inicio_excluir', $hora_inicio_excluir_mysql);

            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['count'] == 0;
        } catch (Exception $e) {
            error_log("Error en verificarDisponibilidadModificacion: " . $e->getMessage());
            return false;
        }
    }

    public function verificarReservaUsuario($id_persona, $id_area, $fecha_reserva, $hora_inicio)
    {
        try {
            $hora_inicio_mysql = $hora_inicio;

            $sql = "SELECT COUNT(*) as count 
                    FROM $this->table_reservas 
                    WHERE id_persona = :id_persona 
                    AND id_area = :id_area 
                    AND fecha_reserva = :fecha_reserva 
                    AND hora_inicio = :hora_inicio 
                    AND estado = 'pendiente'";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_persona', $id_persona, PDO::PARAM_INT);
            $stmt->bindParam(':id_area', $id_area, PDO::PARAM_INT);
            $stmt->bindParam(':fecha_reserva', $fecha_reserva);
            $stmt->bindParam(':hora_inicio', $hora_inicio_mysql);

            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado && $resultado['count'] > 0;
        } catch (Exception $e) {
            error_log("Error en verificarReservaUsuario: " . $e->getMessage());
            return false;
        }
    }

    public function verificarReservaUsuarioMismoHorario($id_persona, $id_area, $fecha, $hora_inicio, $hora_fin, $fecha_excluir, $hora_inicio_excluir)
    {
        try {
            $hora_inicio_mysql = (strlen($hora_inicio) == 5) ? $hora_inicio : $hora_inicio . ':00';
            $hora_fin_mysql = (strlen($hora_fin) == 5) ? $hora_fin : $hora_fin . ':00';
            $hora_inicio_excluir_mysql = (strlen($hora_inicio_excluir) == 5) ? $hora_inicio_excluir : $hora_inicio_excluir . ':00';

            $sql = "SELECT COUNT(*) as count 
                    FROM $this->table_reservas 
                    WHERE id_persona = :id_persona 
                    AND id_area = :id_area 
                    AND fecha_reserva = :fecha 
                    AND estado IN ('pendiente', 'confirmada')
                    AND NOT (fecha_reserva = :fecha_excluir AND hora_inicio = :hora_inicio_excluir)
                    AND (:hora_inicio < hora_fin AND :hora_fin > hora_inicio)";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_persona', $id_persona, PDO::PARAM_INT);
            $stmt->bindParam(':id_area', $id_area, PDO::PARAM_INT);
            $stmt->bindParam(':fecha', $fecha);
            $stmt->bindParam(':hora_inicio', $hora_inicio_mysql);
            $stmt->bindParam(':hora_fin', $hora_fin_mysql);
            $stmt->bindParam(':fecha_excluir', $fecha_excluir);
            $stmt->bindParam(':hora_inicio_excluir', $hora_inicio_excluir_mysql);

            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['count'] > 0;
        } catch (Exception $e) {
            error_log("Error en verificarReservaUsuarioMismoHorario: " . $e->getMessage());
            return false;
        }
    }

    // Métodos de consulta
    public function obtenerReservasPorPersona($idPersona)
    {
        try {
            $sql = "SELECT 
                    r.id_persona,
                    r.id_area,
                    r.fecha_reserva,
                    r.hora_inicio,
                    r.hora_fin,
                    r.motivo,
                    r.estado,
                    a.nombre as nombre_area,
                    a.costo_reserva
                FROM $this->table_reservas r
                INNER JOIN $this->table_name a ON r.id_area = a.id_area
                WHERE r.id_persona = :id_persona
                ORDER BY 
                    CASE 
                        WHEN r.estado = 'pendiente' THEN 1
                        WHEN r.estado = 'confirmada' THEN 2
                        WHEN r.estado = 'cancelada' THEN 3
                        ELSE 4
                    END,
                    r.fecha_reserva ASC,
                    r.hora_inicio ASC";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_persona', $idPersona, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener reservas por persona: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerReservasPorArea($id_area)
    {
        try {
            if (empty($id_area) || !is_numeric($id_area)) {
                throw new InvalidArgumentException("ID de área no válido");
            }

            $sql = "SELECT 
                    rac.*,
                    p.nombre as nombre_persona_encrypted,
                    p.apellido_paterno as apellido_paterno_encrypted,
                    p.apellido_materno as apellido_materno_encrypted,
                    p.ci as ci_encrypted,
                    p.email,
                    r.rol,
                    GROUP_CONCAT(DISTINCT CONCAT('D', d.numero, '-P', d.piso) SEPARATOR ' / ') as departamentos_vinculados,
                    GROUP_CONCAT(DISTINCT d.id_departamento) as ids_departamentos
                FROM $this->table_reservas rac
                JOIN $this->table_persona p ON rac.id_persona = p.id_persona
                JOIN rol r ON p.id_rol = r.id_rol
                LEFT JOIN $this->table_tiene_departamento td ON p.id_persona = td.id_persona AND td.estado = 'activo'
                LEFT JOIN $this->table_departamento d ON td.id_departamento = d.id_departamento
                WHERE rac.id_area = :id_area
                GROUP BY rac.id_reserva, p.id_persona
                ORDER BY rac.fecha_reserva DESC, rac.hora_inicio";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_area', $id_area, PDO::PARAM_INT);
            $stmt->execute();
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($resultados as &$fila) {
                $fila['nombre_persona'] = $this->decrypt($fila['nombre_persona_encrypted']);
                $fila['apellido_paterno'] = $this->decrypt($fila['apellido_paterno_encrypted']);
                $fila['apellido_materno'] = $this->decrypt($fila['apellido_materno_encrypted']);
                $fila['ci'] = $this->decrypt($fila['ci_encrypted']);

                if (empty($fila['departamentos_vinculados'])) {
                    $fila['departamentos_vinculados'] = 'Sin departamento asignado';
                    $fila['ids_departamentos'] = '';
                }

                unset($fila['nombre_persona_encrypted']);
                unset($fila['apellido_paterno_encrypted']);
                unset($fila['apellido_materno_encrypted']);
                unset($fila['ci_encrypted']);
            }

            return $resultados;
        } catch (Exception $e) {
            error_log("Error en obtenerReservasPorArea: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerReservasPendientes()
    {
        try {
            $sql = "SELECT 
                    rac.*,
                    ac.nombre as nombre_area,
                    p.nombre as nombre_persona_encrypted,
                    p.apellido_paterno as apellido_paterno_encrypted,
                    p.apellido_materno as apellido_materno_encrypted,
                    p.ci as ci_encrypted,
                    p.email,
                    r.rol,
                    GROUP_CONCAT(DISTINCT CONCAT('D', d.numero, '-P', d.piso) SEPARATOR ' / ') as departamentos_vinculados,
                    GROUP_CONCAT(DISTINCT d.id_departamento) as ids_departamentos
                FROM $this->table_reservas rac
                JOIN $this->table_name ac ON rac.id_area = ac.id_area
                JOIN $this->table_persona p ON rac.id_persona = p.id_persona
                JOIN rol r ON p.id_rol = r.id_rol
                LEFT JOIN $this->table_tiene_departamento td ON p.id_persona = td.id_persona AND td.estado = 'activo'
                LEFT JOIN $this->table_departamento d ON td.id_departamento = d.id_departamento
                WHERE rac.estado = 'pendiente'
                AND ac.estado != 'eliminado'
                GROUP BY rac.id_reserva, p.id_persona
                ORDER BY rac.fecha_reserva ASC, rac.hora_inicio ASC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($resultados as &$fila) {
                $fila['nombre_persona'] = $this->decrypt($fila['nombre_persona_encrypted']);
                $fila['apellido_paterno'] = $this->decrypt($fila['apellido_paterno_encrypted']);
                $fila['apellido_materno'] = $this->decrypt($fila['apellido_materno_encrypted']);
                $fila['ci'] = $this->decrypt($fila['ci_encrypted']);

                if (empty($fila['departamentos_vinculados'])) {
                    $fila['departamentos_vinculados'] = 'Sin departamento asignado';
                    $fila['ids_departamentos'] = '';
                }

                unset($fila['nombre_persona_encrypted']);
                unset($fila['apellido_paterno_encrypted']);
                unset($fila['apellido_materno_encrypted']);
                unset($fila['ci_encrypted']);
            }

            return $resultados;
        } catch (Exception $e) {
            error_log("Error en obtenerReservasPendientes: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerReservasDelMes($mes = null)
    {
        try {
            if ($mes === null) {
                $fecha_inicio = date('Y-m-01');
                $fecha_fin = date('Y-m-t');
            } else {
                if (!preg_match('/^\d{4}-\d{2}$/', $mes)) {
                    $mes = date('Y-m');
                }
                $fecha_inicio = $mes . '-01';
                $fecha_fin = date('Y-m-t', strtotime($fecha_inicio));
            }

            $sql = "SELECT 
                    rac.*,
                    ac.nombre as area_nombre,
                    p.nombre as nombre_persona_encrypted,
                    p.apellido_paterno as apellido_paterno_encrypted,
                    p.apellido_materno as apellido_materno_encrypted,
                    p.ci as ci_encrypted,
                    p.email,
                    r.rol,
                    GROUP_CONCAT(DISTINCT CONCAT('D', d.numero, '-P', d.piso) SEPARATOR ' / ') as departamentos_vinculados,
                    GROUP_CONCAT(DISTINCT d.id_departamento) as ids_departamentos
                FROM $this->table_reservas rac
                JOIN $this->table_name ac ON rac.id_area = ac.id_area
                JOIN $this->table_persona p ON rac.id_persona = p.id_persona
                JOIN rol r ON p.id_rol = r.id_rol
                LEFT JOIN $this->table_tiene_departamento td ON p.id_persona = td.id_persona AND td.estado = 'activo'
                LEFT JOIN $this->table_departamento d ON td.id_departamento = d.id_departamento
                WHERE rac.fecha_reserva BETWEEN :fecha_inicio AND :fecha_fin
                AND ac.estado != 'eliminado'
                GROUP BY rac.id_reserva, p.id_persona
                ORDER BY rac.fecha_reserva DESC, rac.hora_inicio DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':fecha_inicio', $fecha_inicio);
            $stmt->bindParam(':fecha_fin', $fecha_fin);
            $stmt->execute();
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($resultados as &$fila) {
                $fila['nombre_persona'] = $this->decrypt($fila['nombre_persona_encrypted']);
                $fila['apellido_paterno'] = $this->decrypt($fila['apellido_paterno_encrypted']);
                $fila['apellido_materno'] = $this->decrypt($fila['apellido_materno_encrypted']);
                $fila['ci'] = $this->decrypt($fila['ci_encrypted']);

                if (empty($fila['departamentos_vinculados'])) {
                    $fila['departamentos_vinculados'] = 'Sin departamento asignado';
                    $fila['ids_departamentos'] = '';
                }

                unset($fila['nombre_persona_encrypted']);
                unset($fila['apellido_paterno_encrypted']);
                unset($fila['apellido_materno_encrypted']);
                unset($fila['ci_encrypted']);
            }

            return $resultados;
        } catch (Exception $e) {
            error_log("Error en obtenerReservasDelMes: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerReservaEspecifica($id_persona, $id_area, $fecha_reserva, $hora_inicio)
    {
        try {
            $sql = "SELECT 
                    r.*,
                    a.nombre as nombre_area,
                    a.costo_reserva
                FROM $this->table_reservas r
                INNER JOIN $this->table_name a ON r.id_area = a.id_area
                WHERE r.id_persona = :id_persona 
                AND r.id_area = :id_area 
                AND r.fecha_reserva = :fecha_reserva 
                AND r.hora_inicio = :hora_inicio";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_persona', $id_persona, PDO::PARAM_INT);
            $stmt->bindParam(':id_area', $id_area, PDO::PARAM_INT);
            $stmt->bindParam(':fecha_reserva', $fecha_reserva);
            $stmt->bindParam(':hora_inicio', $hora_inicio);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerReservaEspecifica: " . $e->getMessage());
            return false;
        }
    }

    // Métodos de estadísticas
    public function contarReservasPendientes()
    {
        try {
            $sql = "SELECT COUNT(*) AS total_pendientes
                    FROM $this->table_reservas rac
                    JOIN $this->table_name ac ON rac.id_area = ac.id_area
                    WHERE rac.estado = 'pendiente'
                    AND ac.estado != 'eliminado'";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado ? (int)$resultado['total_pendientes'] : 0;
        } catch (Exception $e) {
            error_log("Error en contarReservasPendientes: " . $e->getMessage());
            return 0;
        }
    }

    public function contarReservasEsteMes()
    {
        try {
            $fecha_inicio = date('Y-m-01');
            $fecha_fin = date('Y-m-t');

            $sql = "SELECT COUNT(*) AS total_reservas
                    FROM $this->table_reservas rac
                    JOIN $this->table_name ac ON rac.id_area = ac.id_area
                    WHERE rac.fecha_reserva BETWEEN :fecha_inicio AND :fecha_fin
                    AND ac.estado != 'eliminado'";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':fecha_inicio', $fecha_inicio);
            $stmt->bindParam(':fecha_fin', $fecha_fin);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado ? (int)$resultado['total_reservas'] : 0;
        } catch (Exception $e) {
            error_log("Error en contarReservasEsteMes: " . $e->getMessage());
            return 0;
        }
    }

    public function contarAreasPorEstado()
    {
        try {
            $sql = "SELECT estado, COUNT(*) AS total
                    FROM $this->table_name
                    GROUP BY estado";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $conteo = [
                'disponible' => 0,
                'mantenimiento' => 0,
                'no disponible' => 0,
                'eliminado' => 0,
                'total' => 0
            ];

            foreach ($resultados as $fila) {
                $estado = $fila['estado'];
                $conteo[$estado] = (int)$fila['total'];
            }

            $conteo['total'] = $conteo['disponible']
                + $conteo['mantenimiento']
                + $conteo['no disponible']
                + $conteo['eliminado'];

            return $conteo;
        } catch (Exception $e) {
            error_log("Error en contarAreasPorEstado: " . $e->getMessage());
            return [
                'disponible' => 0,
                'mantenimiento' => 0,
                'no disponible' => 0,
                'eliminado' => 0,
                'total' => 0
            ];
        }
    }
}
?>
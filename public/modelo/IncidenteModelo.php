<?php
class IncidenteModelo {
    private $db;
    private $encryption_key;
    private $table_incidente = "incidente";
    private $table_persona = "persona";
    private $table_departamento = "departamento";
    private $table_area_comun = "area_comun";
    private $table_incidente_asignado = "incidente_asignado";
    private $table_historial_incidente = "historial_incidente";

    public function __construct($db, $encryption_key = null) {
        $this->db = $db;
        $this->encryption_key = $encryption_key ?: '1A3F6C9E2B5D8A0C7E4F1A2B3C8D5E6F7A1B2C3D4E5F6A7B8C9D0E1F2A3B4C5D6';

        if (strlen($this->encryption_key) < 32) {
            $this->encryption_key = str_pad($this->encryption_key, 32, "\0");
        } elseif (strlen($this->encryption_key) > 32) {
            $this->encryption_key = substr($this->encryption_key, 0, 32);
        }
    }

    private function decrypt($encrypted_data) {
        if (empty($encrypted_data)) return $encrypted_data;
        try {
            $data = base64_decode($encrypted_data);
            if ($data === false) return $encrypted_data;
            if (strlen($data) < 16) return $encrypted_data;

            $iv = substr($data, 0, 16);
            $encrypted = substr($data, 16);
            if (empty($encrypted)) return '';

            $decrypted = openssl_decrypt($encrypted, 'AES-256-CBC', $this->encryption_key, OPENSSL_RAW_DATA, $iv);
            return $decrypted === false ? $encrypted_data : $decrypted;
        } catch (Exception $e) {
            error_log("Error descifrando datos: " . $e->getMessage());
            return $encrypted_data;
        }
    }

    // Método principal para listar incidentes con información completa
    public function listarIncidentes() {
        $sql = "SELECT 
                    i.*,
                    d.numero as numero_departamento,
                    r.nombre as nombre_residente,
                    r.apellido_paterno as apellido_paterno_residente,
                    r.apellido_materno as apellido_materno_residente,
                    a.nombre as nombre_area,
                    ia.id_personal_asignado,
                    p.nombre as nombre_personal,
                    p.apellido_paterno as apellido_personal,
                    ia.requiere_reasignacion
                FROM {$this->table_incidente} i
                LEFT JOIN {$this->table_departamento} d ON i.id_departamento = d.id_departamento
                LEFT JOIN {$this->table_persona} r ON i.id_residente = r.id_persona
                LEFT JOIN {$this->table_area_comun} a ON i.id_area = a.id_area
                LEFT JOIN (
                    SELECT ia1.id_incidente, ia1.id_personal as id_personal_asignado, 
                           ia1.requiere_reasignacion,
                           ROW_NUMBER() OVER (PARTITION BY ia1.id_incidente ORDER BY ia1.fecha_asignacion DESC) as rn
                    FROM {$this->table_incidente_asignado} ia1
                ) ia ON i.id_incidente = ia.id_incidente AND ia.rn = 1
                LEFT JOIN {$this->table_persona} p ON ia.id_personal_asignado = p.id_persona
                ORDER BY i.fecha_registro DESC";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error preparando la consulta");
        }

        $stmt->execute();
        $incidentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Descifrar datos sensibles y formatear
        foreach ($incidentes as &$incidente) {
            // Descifrar datos del residente
            $incidente['nombre_residente'] = $this->decrypt($incidente['nombre_residente']);
            $incidente['apellido_paterno_residente'] = $this->decrypt($incidente['apellido_paterno_residente']);
            $incidente['apellido_materno_residente'] = $this->decrypt($incidente['apellido_materno_residente']);

            // Construir nombre completo del residente
            $incidente['nombre_residente'] = trim(
                $incidente['nombre_residente'] . ' ' .
                $incidente['apellido_paterno_residente'] . ' ' .
                ($incidente['apellido_materno_residente'] ?? '')
            );

            // Descifrar datos del personal asignado si existe
            if (!empty($incidente['nombre_personal'])) {
                $incidente['nombre_personal'] = $this->decrypt($incidente['nombre_personal']);
                $incidente['apellido_personal'] = $this->decrypt($incidente['apellido_personal']);
                $incidente['personal_asignado'] = trim(
                    $incidente['nombre_personal'] . ' ' .
                    $incidente['apellido_personal']
                );
            } else {
                $incidente['personal_asignado'] = null;
            }
        }

        return $incidentes;
    }

    // Listar incidentes por estado
    public function listarIncidentesPorEstado($estado) {
        $estados_permitidos = ['pendiente', 'en_proceso', 'resuelto', 'cancelado'];
        if (!in_array($estado, $estados_permitidos)) {
            throw new Exception("Estado no válido");
        }

        $sql = "SELECT 
                    i.*,
                    d.numero as numero_departamento,
                    r.nombre as nombre_residente,
                    r.apellido_paterno as apellido_paterno_residente,
                    r.apellido_materno as apellido_materno_residente,
                    a.nombre as nombre_area,
                    ia.id_personal_asignado,
                    p.nombre as nombre_personal,
                    p.apellido_paterno as apellido_personal,
                    ia.requiere_reasignacion
                FROM {$this->table_incidente} i
                LEFT JOIN {$this->table_departamento} d ON i.id_departamento = d.id_departamento
                LEFT JOIN {$this->table_persona} r ON i.id_residente = r.id_persona
                LEFT JOIN {$this->table_area_comun} a ON i.id_area = a.id_area
                LEFT JOIN (
                    SELECT ia1.id_incidente, ia1.id_personal as id_personal_asignado, 
                           ia1.requiere_reasignacion,
                           ROW_NUMBER() OVER (PARTITION BY ia1.id_incidente ORDER BY ia1.fecha_asignacion DESC) as rn
                    FROM {$this->table_incidente_asignado} ia1
                ) ia ON i.id_incidente = ia.id_incidente AND ia.rn = 1
                LEFT JOIN {$this->table_persona} p ON ia.id_personal_asignado = p.id_persona
                WHERE i.estado = ?
                ORDER BY i.fecha_registro DESC";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error preparando la consulta");
        }

        $stmt->bindParam(1, $estado, PDO::PARAM_STR);
        $stmt->execute();
        $incidentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Descifrar y formatear como en listarIncidentes
        foreach ($incidentes as &$incidente) {
            $incidente['nombre_residente'] = $this->decrypt($incidente['nombre_residente']);
            $incidente['apellido_paterno_residente'] = $this->decrypt($incidente['apellido_paterno_residente']);
            $incidente['apellido_materno_residente'] = $this->decrypt($incidente['apellido_materno_residente']);

            $incidente['nombre_residente'] = trim(
                $incidente['nombre_residente'] . ' ' .
                $incidente['apellido_paterno_residente'] . ' ' .
                ($incidente['apellido_materno_residente'] ?? '')
            );

            if (!empty($incidente['nombre_personal'])) {
                $incidente['nombre_personal'] = $this->decrypt($incidente['nombre_personal']);
                $incidente['apellido_personal'] = $this->decrypt($incidente['apellido_personal']);
                $incidente['personal_asignado'] = trim(
                    $incidente['nombre_personal'] . ' ' .
                    $incidente['apellido_personal']
                );
            } else {
                $incidente['personal_asignado'] = null;
            }
        }

        return $incidentes;
    }

    // Listar incidentes que requieren reasignación
    public function listarIncidentesPorReasignacion() {
        $sql = "SELECT 
                    i.*,
                    d.numero as numero_departamento,
                    r.nombre as nombre_residente,
                    r.apellido_paterno as apellido_paterno_residente,
                    r.apellido_materno as apellido_materno_residente,
                    a.nombre as nombre_area,
                    ia.id_personal_asignado,
                    p.nombre as nombre_personal,
                    p.apellido_paterno as apellido_personal,
                    ia.requiere_reasignacion,
                    ia.comentario_reasignacion,
                    ia.observaciones as observaciones_asignacion,
                    ia.fecha_asignacion
                FROM {$this->table_incidente} i
                LEFT JOIN {$this->table_departamento} d ON i.id_departamento = d.id_departamento
                LEFT JOIN {$this->table_persona} r ON i.id_residente = r.id_persona
                LEFT JOIN {$this->table_area_comun} a ON i.id_area = a.id_area
                LEFT JOIN (
                    SELECT ia1.id_incidente, ia1.id_personal as id_personal_asignado, 
                           ia1.requiere_reasignacion,
                           ia1.comentario_reasignacion,
                           ia1.observaciones,
                           ia1.fecha_asignacion,
                           ROW_NUMBER() OVER (PARTITION BY ia1.id_incidente ORDER BY ia1.fecha_asignacion DESC) as rn
                    FROM {$this->table_incidente_asignado} ia1
                ) ia ON i.id_incidente = ia.id_incidente AND ia.rn = 1
                LEFT JOIN {$this->table_persona} p ON ia.id_personal_asignado = p.id_persona
                WHERE ia.requiere_reasignacion = TRUE
                ORDER BY i.fecha_registro DESC";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error preparando la consulta");
        }

        $stmt->execute();
        $incidentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Descifrar y formatear
        foreach ($incidentes as &$incidente) {
            $incidente['nombre_residente'] = $this->decrypt($incidente['nombre_residente']);
            $incidente['apellido_paterno_residente'] = $this->decrypt($incidente['apellido_paterno_residente']);
            $incidente['apellido_materno_residente'] = $this->decrypt($incidente['apellido_materno_residente']);

            $incidente['nombre_residente'] = trim(
                $incidente['nombre_residente'] . ' ' .
                $incidente['apellido_paterno_residente'] . ' ' .
                ($incidente['apellido_materno_residente'] ?? '')
            );

            if (!empty($incidente['nombre_personal'])) {
                $incidente['nombre_personal'] = $this->decrypt($incidente['nombre_personal']);
                $incidente['apellido_personal'] = $this->decrypt($incidente['apellido_personal']);
                $incidente['personal_asignado'] = trim(
                    $incidente['nombre_personal'] . ' ' .
                    $incidente['apellido_personal']
                );
            } else {
                $incidente['personal_asignado'] = null;
            }
        }

        return $incidentes;
    }

    // Métodos para contar incidentes
    public function contarIncidentesPorEstado($estado) {
        $estados_permitidos = ['pendiente', 'en_proceso', 'resuelto', 'cancelado'];
        if (!in_array($estado, $estados_permitidos)) {
            throw new Exception("Estado no válido");
        }

        $sql = "SELECT COUNT(*) as total FROM {$this->table_incidente} WHERE estado = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(1, $estado, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function contarIncidentesPorReasignacion() {
        $sql = "SELECT COUNT(DISTINCT i.id_incidente) as total
                FROM {$this->table_incidente} i
                JOIN {$this->table_incidente_asignado} ia ON i.id_incidente = ia.id_incidente
                WHERE ia.requiere_reasignacion = TRUE";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function contarTotalIncidentes() {
        $sql = "SELECT COUNT(*) as total FROM {$this->table_incidente}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // Obtener personal disponible (personal con rol de mantenimiento o similar)
    public function obtenerPersonalDisponible() {
        $sql = "SELECT 
                    p.id_persona,
                    p.nombre,
                    p.apellido_paterno,
                    p.apellido_materno,
                    r.rol as especialidad
                FROM {$this->table_persona} p
                INNER JOIN rol r ON p.id_rol = r.id_rol
                WHERE p.estado = 'activo' 
                AND r.id_rol != 1 
                AND r.id_rol != 2
                ORDER BY p.nombre, p.apellido_paterno";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error preparando la consulta");
        }

        $stmt->execute();
        $personal = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Descifrar datos
        foreach ($personal as &$persona) {
            $persona['nombre'] = $this->decrypt($persona['nombre']);
            $persona['apellido_paterno'] = $this->decrypt($persona['apellido_paterno']);
            $persona['apellido_materno'] = $this->decrypt($persona['apellido_materno']);
            $persona['nombre_completo'] = trim(
                $persona['nombre'] . ' ' .
                $persona['apellido_paterno'] . ' ' .
                ($persona['apellido_materno'] ?? '')
            );
        }

        return $personal;
    }

    // Obtener departamentos
    public function obtenerDepartamentos() {
        $sql = "SELECT id_departamento, numero FROM {$this->table_departamento} ORDER BY numero";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener residentes
    public function obtenerResidentes() {
        $sql = "SELECT 
                    p.id_persona,
                    p.nombre,
                    p.apellido_paterno,
                    p.apellido_materno
                FROM {$this->table_persona} p
                INNER JOIN rol r ON p.id_rol = r.id_rol
                WHERE p.estado = 'activo' 
                AND r.rol = 'residente'
                ORDER BY p.nombre, p.apellido_paterno";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $residentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Descifrar y formatear nombres
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

    // Obtener áreas comunes
    public function obtenerAreas() {
        $sql = "SELECT id_area, nombre FROM {$this->table_area_comun} WHERE estado = 'disponible' ORDER BY nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener historial de incidente
    public function obtenerHistorialIncidente($id_incidente) {
        $sql = "SELECT 
                    hi.*,
                    p.nombre,
                    p.apellido_paterno,
                    p.apellido_materno
                FROM {$this->table_historial_incidente} hi
                LEFT JOIN {$this->table_persona} p ON hi.id_persona = p.id_persona
                WHERE hi.id_incidente = ?
                ORDER BY hi.fecha_accion DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(1, $id_incidente, PDO::PARAM_INT);
        $stmt->execute();
        $historial = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Descifrar nombres
        foreach ($historial as &$registro) {
            $registro['nombre'] = $this->decrypt($registro['nombre']);
            $registro['apellido_paterno'] = $this->decrypt($registro['apellido_paterno']);
            $registro['apellido_materno'] = $this->decrypt($registro['apellido_materno']);
            $registro['nombre_completo'] = trim(
                $registro['nombre'] . ' ' .
                $registro['apellido_paterno'] . ' ' .
                ($registro['apellido_materno'] ?? '')
            );
        }

        return $historial;
    }

    // Obtener incidente por ID
    public function obtenerIncidentePorId($id_incidente) {
        if (empty($id_incidente)) {
            throw new Exception("ID de incidente es requerido");
        }

        $sql = "SELECT 
            i.*,
            d.numero as numero_departamento,
            r.nombre as nombre_residente,
            r.apellido_paterno as apellido_paterno_residente,
            r.apellido_materno as apellido_materno_residente,
            a.nombre as nombre_area,
            a.id_area
        FROM {$this->table_incidente} i
        LEFT JOIN {$this->table_departamento} d ON i.id_departamento = d.id_departamento
        LEFT JOIN {$this->table_persona} r ON i.id_residente = r.id_persona
        LEFT JOIN {$this->table_area_comun} a ON i.id_area = a.id_area
        WHERE i.id_incidente = ?";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error preparando la consulta");
        }

        $stmt->bindParam(1, $id_incidente, PDO::PARAM_INT);
        $stmt->execute();

        $incidente = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($incidente) {
            // Descifrar los datos sensibles
            $incidente['nombre_residente'] = $this->decrypt($incidente['nombre_residente']);
            $incidente['apellido_paterno_residente'] = $this->decrypt($incidente['apellido_paterno_residente']);
            $incidente['apellido_materno_residente'] = $this->decrypt($incidente['apellido_materno_residente']);

            // Construir nombre completo
            $incidente['residente_nombre'] = trim(
                $incidente['nombre_residente'] . ' ' .
                $incidente['apellido_paterno_residente'] . ' ' .
                ($incidente['apellido_materno_residente'] ?? '')
            );
        }

        return $incidente;
    }

    // Métodos POST optimizados para trabajar con triggers
    public function registrarIncidente($datos) {
        try {
            // Validar que id_departamento esté presente y sea válido
            if (empty($datos['id_departamento']) || !is_numeric($datos['id_departamento']) || intval($datos['id_departamento']) <= 0) {
                throw new Exception("El campo id_departamento es obligatorio y debe ser un número válido");
            }

            $this->db->beginTransaction();

            $sql = "INSERT INTO {$this->table_incidente} 
                (id_departamento, id_residente, id_creador, tipo, descripcion, descripcion_detallada, id_area) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->db->prepare($sql);

            $id_departamento = intval($datos['id_departamento']);
            $id_area = !empty($datos['id_area']) ? intval($datos['id_area']) : null;

            $stmt->bindParam(1, $id_departamento, PDO::PARAM_INT);
            $stmt->bindParam(2, $datos['id_residente'], PDO::PARAM_INT);
            $stmt->bindParam(3, $datos['id_residente'], PDO::PARAM_INT);
            $stmt->bindParam(4, $datos['tipo'], PDO::PARAM_STR);
            $stmt->bindParam(5, $datos['descripcion'], PDO::PARAM_STR);
            $stmt->bindParam(6, $datos['descripcion_detallada'], PDO::PARAM_STR);
            $stmt->bindParam(7, $id_area, PDO::PARAM_INT);

            $result = $stmt->execute();

            if ($result) {
                $id_incidente = $this->db->lastInsertId();
                $this->db->commit();
                return $id_incidente;
            }

            $this->db->rollBack();
            return false;

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error al registrar incidente: " . $e->getMessage());
            return false;
        }
    }

    public function editarIncidente($datos) {
        try {
            // Configurar usuario actual para el trigger
            $id_usuario = $_SESSION['id_usuario'] ?? 1;
            $this->db->exec("SET @usuario_actual = $id_usuario");

            $sql = "UPDATE {$this->table_incidente} 
                SET descripcion = ?, descripcion_detallada = ?
                WHERE id_incidente = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(1, $datos['descripcion'], PDO::PARAM_STR);
            $stmt->bindParam(2, $datos['descripcion_detallada'], PDO::PARAM_STR);
            $stmt->bindParam(3, $datos['id_incidente'], PDO::PARAM_INT);

            $result = $stmt->execute();

            // Limpiar variable de usuario
            $this->db->exec("SET @usuario_actual = NULL");

            return $result;

        } catch (Exception $e) {
            error_log("Error al editar incidente: " . $e->getMessage());
            return false;
        }
    }

    public function cambiarTipoIncidente($datos) {
        try {
            $id_usuario = $_SESSION['id_usuario'] ?? 1;
            $this->db->exec("SET @usuario_actual = $id_usuario");

            $sql = "UPDATE {$this->table_incidente} 
                SET tipo = ?
                WHERE id_incidente = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(1, $datos['tipo'], PDO::PARAM_STR);
            $stmt->bindParam(2, $datos['id_incidente'], PDO::PARAM_INT);

            $result = $stmt->execute();

            $this->db->exec("SET @usuario_actual = NULL");

            return $result;

        } catch (Exception $e) {
            error_log("Error al cambiar tipo de incidente: " . $e->getMessage());
            return false;
        }
    }

    public function asignarPersonal($datos) {
        try {
            $this->db->beginTransaction();

            $sql = "INSERT INTO {$this->table_incidente_asignado} 
                (id_incidente, id_personal, observaciones) 
                VALUES (?, ?, ?)";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(1, $datos['id_incidente'], PDO::PARAM_INT);
            $stmt->bindParam(2, $datos['id_personal'], PDO::PARAM_INT);
            $stmt->bindParam(3, $datos['observaciones'], PDO::PARAM_STR);

            $result = $stmt->execute();

            if ($result) {
                $this->db->commit();
                return true;
            }

            $this->db->rollBack();
            return false;

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error al asignar personal: " . $e->getMessage());
            return false;
        }
    }

    public function reasignarPersonal($datos) {
        try {
            $this->db->beginTransaction();

            $sql = "UPDATE incidente_asignado 
                SET id_personal = :id_nuevo_personal,
                    observaciones = :observaciones,
                    requiere_reasignacion = FALSE,
                    comentario_reasignacion = NULL
                WHERE id_incidente = :id_incidente";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_nuevo_personal', $datos['id_nuevo_personal'], PDO::PARAM_INT);
            $stmt->bindParam(':observaciones', $datos['observaciones'], PDO::PARAM_STR);
            $stmt->bindParam(':id_incidente', $datos['id_incidente'], PDO::PARAM_INT);

            $result = $stmt->execute();

            if ($result) {
                $this->db->commit();
                return true;
            }

            $this->db->rollBack();
            return false;

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error al reasignar personal: " . $e->getMessage());
            return false;
        }
    }


    public function resolverIncidente($datos) {
        try {
            $id_usuario = $_SESSION['id_usuario'] ?? 1;
            $this->db->exec("SET @usuario_actual = $id_usuario");

            // ✅ Incluir costo_externo para incidentes externos
            $sql = "UPDATE {$this->table_incidente} 
                SET estado = 'resuelto', 
                    costo_externo = ?,
                    descripcion_detallada = CONCAT(COALESCE(descripcion_detallada, ''), '\n--- RESUELTO ---\n', ?)
                WHERE id_incidente = ?";

            $stmt = $this->db->prepare($sql);
            $costo = ($datos['costo_externo'] && $datos['costo_externo'] > 0) ? $datos['costo_externo'] : 0;
            $stmt->bindParam(1, $costo, PDO::PARAM_STR);
            $stmt->bindParam(2, $datos['observaciones'], PDO::PARAM_STR);
            $stmt->bindParam(3, $datos['id_incidente'], PDO::PARAM_INT);

            $result = $stmt->execute();

            $this->db->exec("SET @usuario_actual = NULL");

            return $result;

        } catch (Exception $e) {
            error_log("Error al resolver incidente: " . $e->getMessage());
            return false;
        }
    }

    public function cancelarIncidente($datos) {
        try {
            $id_usuario = $datos['id_persona'] ?? 1;
            $this->db->exec("SET @usuario_actual = $id_usuario");

            $sql = "UPDATE {$this->table_incidente} 
                SET estado = 'cancelado',
                    descripcion_detallada = CONCAT(COALESCE(descripcion_detallada, ''), '\n--- CANCELADO ---\n', ?)
                WHERE id_incidente = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(1, $datos['motivo'], PDO::PARAM_STR);
            $stmt->bindParam(2, $datos['id_incidente'], PDO::PARAM_INT);

            $result = $stmt->execute();

            $this->db->exec("SET @usuario_actual = NULL");

            return $result;

        } catch (Exception $e) {
            error_log("Error al cancelar incidente: " . $e->getMessage());
            return false;
        }
    }

    // Obtener departamentos por ID de persona (para residentes)
    public function obtenerDepartamentosPorID($id_persona) {
        $sql = "SELECT 
                d.id_departamento, 
                d.numero
            FROM {$this->table_departamento} d
            INNER JOIN tiene_departamento td ON d.id_departamento = td.id_departamento
            WHERE td.id_persona = ? 
            AND td.estado = 'activo'
            ORDER BY d.numero";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error preparando la consulta");
        }

        $stmt->bindParam(1, $id_persona, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener incidentes por residente (para vista de mis incidentes)
    public function listarIncidentesPorResidente($id_residente) {
        $sql = "SELECT 
                i.*,
                d.numero as numero_departamento,
                r.nombre as nombre_residente,
                r.apellido_paterno as apellido_paterno_residente,
                r.apellido_materno as apellido_materno_residente,
                a.nombre as nombre_area,
                a.id_area,
                ia.id_personal_asignado,
                p.nombre as nombre_personal,
                p.apellido_paterno as apellido_personal,
                ia.requiere_reasignacion
            FROM {$this->table_incidente} i
            LEFT JOIN {$this->table_departamento} d ON i.id_departamento = d.id_departamento
            LEFT JOIN {$this->table_persona} r ON i.id_residente = r.id_persona
            LEFT JOIN {$this->table_area_comun} a ON i.id_area = a.id_area
            LEFT JOIN (
                SELECT ia1.id_incidente, ia1.id_personal as id_personal_asignado, 
                       ia1.requiere_reasignacion,
                       ROW_NUMBER() OVER (PARTITION BY ia1.id_incidente ORDER BY ia1.fecha_asignacion DESC) as rn
                FROM {$this->table_incidente_asignado} ia1
            ) ia ON i.id_incidente = ia.id_incidente AND ia.rn = 1
            LEFT JOIN {$this->table_persona} p ON ia.id_personal_asignado = p.id_persona
            WHERE i.id_residente = ?
            ORDER BY i.fecha_registro DESC";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error preparando la consulta");
        }

        $stmt->bindParam(1, $id_residente, PDO::PARAM_INT);
        $stmt->execute();
        $incidentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Descifrar datos sensibles y formatear
        foreach ($incidentes as &$incidente) {
            // Descifrar datos del residente
            $incidente['nombre_residente'] = $this->decrypt($incidente['nombre_residente']);
            $incidente['apellido_paterno_residente'] = $this->decrypt($incidente['apellido_paterno_residente']);
            $incidente['apellido_materno_residente'] = $this->decrypt($incidente['apellido_materno_residente']);

            // Construir nombre completo del residente
            $incidente['nombre_residente'] = trim(
                $incidente['nombre_residente'] . ' ' .
                $incidente['apellido_paterno_residente'] . ' ' .
                ($incidente['apellido_materno_residente'] ?? '')
            );

            // Descifrar datos del personal asignado si existe
            if (!empty($incidente['nombre_personal'])) {
                $incidente['nombre_personal'] = $this->decrypt($incidente['nombre_personal']);
                $incidente['apellido_personal'] = $this->decrypt($incidente['apellido_personal']);
                $incidente['personal_asignado'] = trim(
                    $incidente['nombre_personal'] . ' ' .
                    $incidente['apellido_personal']
                );
            } else {
                $incidente['personal_asignado'] = null;
            }
        }

        return $incidentes;
    }

    // Contar incidentes por residente y estado
    public function contarIncidentesResidentePorEstado($id_residente, $estado) {
        $sql = "SELECT COUNT(*) as total 
            FROM {$this->table_incidente} 
            WHERE id_residente = ? AND estado = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(1, $id_residente, PDO::PARAM_INT);
        $stmt->bindParam(2, $estado, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // Contar total de incidentes del residente
    public function contarTotalIncidentesResidente($id_residente) {
        $sql = "SELECT COUNT(*) as total 
            FROM {$this->table_incidente} 
            WHERE id_residente = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(1, $id_residente, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // Obtener residentes por departamento
    public function obtenerResidentesPorDepartamento($id_departamento) {
        $sql = "SELECT 
                p.id_persona,
                p.nombre,
                p.apellido_paterno,
                p.apellido_materno
            FROM {$this->table_persona} p
            INNER JOIN tiene_departamento td ON p.id_persona = td.id_persona
            INNER JOIN rol r ON p.id_rol = r.id_rol
            WHERE td.id_departamento = ? 
            AND td.estado = 'activo'
            AND p.estado = 'activo'
            AND r.rol = 'residente'
            ORDER BY p.nombre, p.apellido_paterno";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error preparando la consulta");
        }

        $stmt->bindParam(1, $id_departamento, PDO::PARAM_INT);
        $stmt->execute();

        $residentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Descifrar y formatear nombres
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

    // En IncidenteModelo.php - Métodos optimizados para personal

    /**
     * Listar incidentes asignados al personal
     */
    public function listarIncidentesAsignados($id_personal) {
        $sql = "SELECT 
                i.*,
                d.numero as numero_departamento,
                r.nombre as nombre_residente,
                r.apellido_paterno as apellido_paterno_residente,
                r.apellido_materno as apellido_materno_residente,
                a.nombre as nombre_area,
                ia.id_asignacion,
                ia.fecha_asignacion,
                ia.fecha_atencion,
                ia.observaciones as observaciones_asignacion,
                ia.requiere_reasignacion
            FROM {$this->table_incidente} i
            LEFT JOIN {$this->table_departamento} d ON i.id_departamento = d.id_departamento
            LEFT JOIN {$this->table_persona} r ON i.id_residente = r.id_persona
            LEFT JOIN {$this->table_area_comun} a ON i.id_area = a.id_area
            INNER JOIN {$this->table_incidente_asignado} ia ON i.id_incidente = ia.id_incidente
            WHERE ia.id_personal = ? 
            AND i.estado IN ('pendiente','en_proceso')
            ORDER BY 
                CASE WHEN ia.fecha_atencion IS NULL THEN 0 ELSE 1 END,
                i.fecha_registro DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(1, $id_personal, PDO::PARAM_INT);
        $stmt->execute();
        $incidentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Descifrar datos
        foreach ($incidentes as &$incidente) {
            $incidente['nombre_residente'] = $this->decrypt($incidente['nombre_residente']);
            $incidente['apellido_paterno_residente'] = $this->decrypt($incidente['apellido_paterno_residente']);
            $incidente['apellido_materno_residente'] = $this->decrypt($incidente['apellido_materno_residente']);

            $incidente['nombre_residente'] = trim(
                $incidente['nombre_residente'] . ' ' .
                $incidente['apellido_paterno_residente'] . ' ' .
                ($incidente['apellido_materno_residente'] ?? '')
            );
        }

        return $incidentes;
    }

    /**
     * Listar incidentes atendidos por el personal
     */
    public function listarIncidentesAtendidos($id_personal) {
        $sql = "SELECT 
            i.*,
            d.numero as numero_departamento,
            r.nombre as nombre_residente,
            r.apellido_paterno as apellido_paterno_residente,
            r.apellido_materno as apellido_materno_residente,
            a.nombre as nombre_area,
            ia.fecha_asignacion,
            ia.fecha_atencion,
            ia.observaciones as observaciones_asignacion
        FROM {$this->table_incidente} i
        LEFT JOIN {$this->table_departamento} d ON i.id_departamento = d.id_departamento
        LEFT JOIN {$this->table_persona} r ON i.id_residente = r.id_persona
        LEFT JOIN {$this->table_area_comun} a ON i.id_area = a.id_area
        INNER JOIN {$this->table_incidente_asignado} ia ON i.id_incidente = ia.id_incidente
        WHERE ia.id_personal = ? 
        AND ia.fecha_atencion IS NOT NULL
        AND i.estado IN ('cancelado','resuelto')  -- FILTRO POR ESTADO
        ORDER BY 
            CASE 
                WHEN i.estado = 'cancelado' THEN 1  -- Primero los en proceso
                WHEN i.estado = 'resuelto' THEN 2    -- Luego los resueltos
                ELSE 3
            END,
            ia.fecha_atencion DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(1, $id_personal, PDO::PARAM_INT);
        $stmt->execute();
        $incidentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Descifrar datos
        foreach ($incidentes as &$incidente) {
            $incidente['nombre_residente'] = $this->decrypt($incidente['nombre_residente']);
            $incidente['apellido_paterno_residente'] = $this->decrypt($incidente['apellido_paterno_residente']);
            $incidente['apellido_materno_residente'] = $this->decrypt($incidente['apellido_materno_residente']);

            $incidente['nombre_residente'] = trim(
                $incidente['nombre_residente'] . ' ' .
                $incidente['apellido_paterno_residente'] . ' ' .
                ($incidente['apellido_materno_residente'] ?? '')
            );
        }

        return $incidentes;
    }

    /**
     * Iniciar atención del incidente (PRIMERA VEZ)
     * Activa trigger: after_incidente_asignado_update → 'inicio_atencion'
     */
    public function iniciarAtencionIncidente($datos) {
        try {
            $sql = "UPDATE {$this->table_incidente_asignado} 
                SET fecha_atencion = NOW(),
                    observaciones = ?
                WHERE id_incidente = ? 
                AND id_personal = ?
                AND fecha_atencion IS NULL"; // Solo si no se ha iniciado

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(1, $datos['observaciones'], PDO::PARAM_STR);
            $stmt->bindParam(2, $datos['id_incidente'], PDO::PARAM_INT);
            $stmt->bindParam(3, $datos['id_personal'], PDO::PARAM_INT);

            $result = $stmt->execute();

            if ($result) {
                // Cambiar estado del incidente a "en_proceso" si está pendiente
                $sql_estado = "UPDATE {$this->table_incidente} 
                          SET estado = 'en_proceso' 
                          WHERE id_incidente = ? 
                          AND estado = 'pendiente'";

                $stmt_estado = $this->db->prepare($sql_estado);
                $stmt_estado->bindParam(1, $datos['id_incidente'], PDO::PARAM_INT);
                $stmt_estado->execute();
            }

            return $result;
            // ✅ Trigger se encarga de registrar 'inicio_atencion' en historial

        } catch (Exception $e) {
            error_log("Error al iniciar atención: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualizar progreso del incidente (ACTUALIZACIONES)
     * Activa trigger: after_incidente_asignado_update → 'actualizacion'
     */
    public function actualizarProgresoIncidente($datos) {
        try {
            $sql = "UPDATE {$this->table_incidente_asignado} 
                SET observaciones = ?
                WHERE id_incidente = ? 
                AND id_personal = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(1, $datos['observaciones'], PDO::PARAM_STR);
            $stmt->bindParam(2, $datos['id_incidente'], PDO::PARAM_INT);
            $stmt->bindParam(3, $datos['id_personal'], PDO::PARAM_INT);

            return $stmt->execute();
            // ✅ Trigger se encarga de registrar 'actualizacion' en historial

        } catch (Exception $e) {
            error_log("Error al actualizar progreso: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Resolver incidente (FINALIZAR)
     * Activa trigger: after_incidente_update → 'resolucion'
     */
    public function resolverIncidentePersonal($datos) {
        try {
            $id_usuario = $_SESSION['id_usuario'] ?? $datos['id_personal'];
            $this->db->exec("SET @usuario_actual = $id_usuario");

            $sql = "UPDATE {$this->table_incidente} 
                SET estado = 'resuelto',
                    costo_externo = ?,
                    descripcion_detallada = CONCAT(COALESCE(descripcion_detallada, ''), '\n--- RESUELTO POR PERSONAL ---\n', ?)
                WHERE id_incidente = ?";

            $stmt = $this->db->prepare($sql);
            $costo = ($datos['costo_externo'] && $datos['costo_externo'] > 0) ? $datos['costo_externo'] : 0;
            $stmt->bindParam(1, $costo, PDO::PARAM_STR);
            $stmt->bindParam(2, $datos['observaciones_finales'], PDO::PARAM_STR);
            $stmt->bindParam(3, $datos['id_incidente'], PDO::PARAM_INT);

            $result = $stmt->execute();

            $this->db->exec("SET @usuario_actual = NULL");

            return $result;
            // ✅ Trigger se encarga de registrar 'resolucion' y generar concepto si es externo

        } catch (Exception $e) {
            error_log("Error al resolver incidente: " . $e->getMessage());
            return false;
        }
    }

    // Métodos de conteo (sin cambios)
    public function contarIncidentesAsignados($id_personal) {
        $sql = "SELECT COUNT(*) as total 
            FROM {$this->table_incidente_asignado} ia
            INNER JOIN {$this->table_incidente} i ON ia.id_incidente = i.id_incidente
            WHERE ia.id_personal = ? 
            AND i.estado IN ('pendiente', 'en_proceso')";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(1, $id_personal, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function contarIncidentesAtendidos($id_personal) {
        $sql = "SELECT COUNT(*) as total 
        FROM {$this->table_incidente_asignado} ia
        INNER JOIN {$this->table_incidente} i ON ia.id_incidente = i.id_incidente
        WHERE ia.id_personal = ? 
        AND ia.fecha_atencion IS NOT NULL
        AND i.estado IN ('resuelto', 'cancelado')";  // FILTRO POR ESTADO

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(1, $id_personal, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }


    public function solicitarReasignacion($id_incidente, $id_personal, $observaciones, $comentario_reasignacion) {
        $sql = "UPDATE incidente_asignado 
            SET observaciones = :observaciones,
                requiere_reasignacion = TRUE,
                comentario_reasignacion = :comentario_reasignacion
            WHERE id_incidente = :id_incidente AND id_personal = :id_personal";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':observaciones' => $observaciones,
            ':comentario_reasignacion' => $comentario_reasignacion,
            ':id_incidente' => $id_incidente,
            ':id_personal' => $id_personal
        ]);
    }


}
?>
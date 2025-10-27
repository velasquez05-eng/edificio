<?php
class CargosFijosModelo {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Obtener todos los cargos fijos
    public function obtenerCargosFijos() {
        try {
            $sql = "SELECT * FROM cargos_fijos ORDER BY nombre_cargo";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerCargosFijos: " . $e->getMessage());
            return false;
        }
    }

    // Obtener cargos activos
    public function obtenerCargosActivos() {
        try {
            $sql = "SELECT * FROM cargos_fijos WHERE estado = 'activo' ORDER BY nombre_cargo";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerCargosActivos: " . $e->getMessage());
            return false;
        }
    }

    // Obtener cargo por ID
    public function obtenerCargoPorId($id_cargo) {
        try {
            $sql = "SELECT * FROM cargos_fijos WHERE id_cargo = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_cargo]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerCargoPorId: " . $e->getMessage());
            return false;
        }
    }

    // Crear nuevo cargo fijo
    public function crearCargo($nombre_cargo, $monto, $descripcion, $estado = 'activo') {
        try {
            $sql = "INSERT INTO cargos_fijos (nombre_cargo, monto, descripcion, estado) VALUES (?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$nombre_cargo, $monto, $descripcion, $estado]);
        } catch (PDOException $e) {
            error_log("Error en crearCargo: " . $e->getMessage());
            return false;
        }
    }

    // Actualizar cargo fijo
    public function actualizarCargo($id_cargo, $nombre_cargo, $monto, $descripcion, $estado) {
        try {
            $sql = "UPDATE cargos_fijos SET nombre_cargo = ?, monto = ?, descripcion = ?, estado = ? WHERE id_cargo = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$nombre_cargo, $monto, $descripcion, $estado, $id_cargo]);
        } catch (PDOException $e) {
            error_log("Error en actualizarCargo: " . $e->getMessage());
            return false;
        }
    }

    // Cambiar estado del cargo
    public function cambiarEstadoCargo($id_cargo, $estado) {
        try {
            $sql = "UPDATE cargos_fijos SET estado = ? WHERE id_cargo = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$estado, $id_cargo]);
        } catch (PDOException $e) {
            error_log("Error en cambiarEstadoCargo: " . $e->getMessage());
            return false;
        }
    }

    // Eliminar cargo fijo
    public function eliminarCargo($id_cargo) {
        try {
            $sql = "DELETE FROM cargos_fijos WHERE id_cargo = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id_cargo]);
        } catch (PDOException $e) {
            error_log("Error en eliminarCargo: " . $e->getMessage());
            return false;
        }
    }

    // Verificar si el cargo está siendo usado en conceptos
    public function verificarCargoEnUso($id_cargo) {
        try {
            $sql = "SELECT COUNT(*) as total FROM conceptos WHERE id_origen = ? AND tipo_origen = 'mantenimiento'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_cargo]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error en verificarCargoEnUso: " . $e->getMessage());
            return true; // Por seguridad, asumir que está en uso si hay error
        }
    }

    // Obtener total de cargos activos
    public function obtenerTotalCargosActivos() {
        try {
            $sql = "SELECT SUM(monto) as total FROM cargos_fijos WHERE estado = 'activo'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Error en obtenerTotalCargosActivos: " . $e->getMessage());
            return 0;
        }
    }

    // Obtener departamentos ocupados
    public function obtenerDepartamentosOcupados() {
        try {
            $sql = "
                SELECT DISTINCT d.id_departamento, d.numero, d.piso 
                FROM departamento d 
                JOIN tiene_departamento td ON d.id_departamento = td.id_departamento 
                WHERE td.estado = 'activo'
                ORDER BY d.piso, d.numero
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerDepartamentosOcupados: " . $e->getMessage());
            return [];
        }
    }

    // Generar conceptos de mantenimiento para todos los departamentos
    public function generarConceptosMantenimiento($year, $month) {
        try {
            $this->db->beginTransaction();

            $cargos_fijos = $this->obtenerCargosActivos();
            $departamentos = $this->obtenerDepartamentosOcupados();

            if (empty($cargos_fijos)) {
                throw new Exception("No hay cargos fijos activos para generar conceptos");
            }

            if (empty($departamentos)) {
                throw new Exception("No hay departamentos ocupados para generar conceptos");
            }

            $fecha_creacion = date('Y-m-d H:i:s');
            $descripcion_mes = date('F Y', mktime(0, 0, 0, $month, 1, $year));

            $total_conceptos = 0;
            $total_monto = 0;

            foreach ($departamentos as $departamento) {
                // Obtener persona del departamento
                $sql_persona = "
                SELECT td.id_persona 
                FROM tiene_departamento td 
                WHERE td.id_departamento = ? AND td.estado = 'activo' 
                LIMIT 1
            ";
                $stmt_persona = $this->db->prepare($sql_persona);
                $stmt_persona->execute([$departamento['id_departamento']]);
                $persona = $stmt_persona->fetch(PDO::FETCH_ASSOC);

                if (!$persona) continue;

                foreach ($cargos_fijos as $cargo) {
                    $descripcion = $cargo['nombre_cargo'] . " - " . $descripcion_mes;

                    // Insertar concepto (MANTENIENDO tu estructura original)
                    $sql_insert = "
                    INSERT INTO conceptos (
                        id_persona, id_factura, concepto, monto, id_origen, 
                        tipo_origen, cantidad, descripcion, fecha_creacion, estado
                    ) VALUES (?, NULL, 'mantenimiento', ?, ?, 'mantenimiento', 1, ?, ?, 'pendiente')
                ";

                    $stmt_insert = $this->db->prepare($sql_insert);
                    $stmt_insert->execute([
                        $persona['id_persona'],
                        $cargo['monto'],
                        $cargo['id_cargo'],
                        $descripcion,
                        $fecha_creacion
                    ]);

                    $total_conceptos++;
                    $total_monto += $cargo['monto'];
                }
            }

            $this->db->commit();

            return [
                'success' => true,
                'total_conceptos' => $total_conceptos,
                'total_monto' => $total_monto,
                'departamentos' => count($departamentos),
                'cargos' => count($cargos_fijos)
            ];

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error en generarConceptosMantenimiento: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    // Verificar si ya se generaron conceptos para un mes específico
    public function verificarConceptosGenerados($year, $month) {
        try {
            $fecha_inicio = sprintf("%04d-%02d-01 00:00:00", $year, $month);
            $fecha_fin = sprintf("%04d-%02d-31 23:59:59", $year, $month);

            $sql = "
                SELECT COUNT(*) as total 
                FROM conceptos 
                WHERE tipo_origen = 'mantenimiento' 
                AND fecha_creacion BETWEEN ? AND ?
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$fecha_inicio, $fecha_fin]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error en verificarConceptosGenerados: " . $e->getMessage());
            return true; // Por seguridad, asumir que ya se generaron si hay error
        }
    }

    // Obtener estadísticas de cargos
    public function obtenerEstadisticasCargos() {
        try {
            $sql_cargos = "SELECT COUNT(*) as total_cargos, 
                                  SUM(CASE WHEN estado = 'activo' THEN 1 ELSE 0 END) as cargos_activos,
                                  SUM(CASE WHEN estado = 'inactivo' THEN 1 ELSE 0 END) as cargos_inactivos,
                                  SUM(monto) as monto_total,
                                  SUM(CASE WHEN estado = 'activo' THEN monto ELSE 0 END) as monto_activos
                           FROM cargos_fijos";
            $stmt = $this->db->prepare($sql_cargos);
            $stmt->execute();
            $estadisticas = $stmt->fetch(PDO::FETCH_ASSOC);

            // Obtener departamentos ocupados
            $departamentos = $this->obtenerDepartamentosOcupados();
            $estadisticas['total_departamentos'] = count($departamentos);
            $estadisticas['monto_mensual_total'] = $estadisticas['monto_activos'] * $estadisticas['total_departamentos'];

            return $estadisticas;
        } catch (PDOException $e) {
            error_log("Error en obtenerEstadisticasCargos: " . $e->getMessage());
            return [
                'total_cargos' => 0,
                'cargos_activos' => 0,
                'cargos_inactivos' => 0,
                'monto_total' => 0,
                'monto_activos' => 0,
                'total_departamentos' => 0,
                'monto_mensual_total' => 0
            ];
        }
    }

    // Obtener última generación de conceptos
    public function obtenerUltimaGeneracionConceptos() {
        try {
            $sql = "
                SELECT MAX(fecha_creacion) as ultima_generacion 
                FROM conceptos 
                WHERE tipo_origen = 'mantenimiento'
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerUltimaGeneracionConceptos: " . $e->getMessage());
            return ['ultima_generacion' => null];
        }
    }

    // Método auxiliar para obtener nombre del mes
    private function obtenerNombreMes($month) {
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        return $meses[$month] ?? 'Mes';
    }
}
?>
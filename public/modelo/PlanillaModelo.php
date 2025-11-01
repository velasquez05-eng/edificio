<?php
class PlanillaModelo {
    private $db;
    private $table_name = "planilla_empleado";
    private $encryption_key;

    public function __construct($db, $encryption_key = null) {
        $this->db = $db;
        $this->encryption_key = $encryption_key ?: '1A3F6C9E2B5D8A0C7E4F1A2B3C8D5E6F7A1B2C3D4E5F6A7B8C9D0E1F2A3B4C5D6';

        // Asegurar que la clave tenga exactamente 32 bytes
        if (strlen($this->encryption_key) < 32) {
            $this->encryption_key = str_pad($this->encryption_key, 32, "\0");
        } elseif (strlen($this->encryption_key) > 32) {
            $this->encryption_key = substr($this->encryption_key, 0, 32);
        }
    }

    // Método para cifrar datos
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

    // Método para descifrar datos
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

    /**
     * Generar planilla para TODOS los empleados activos (ACTUALIZADO)
     */
    public function generarPlanillaCompleta($mes, $anio, $metodo_pago = 'transferencia', $forzar = false) {
        try {
            $query = "CALL GenerarPlanillaCompleta(?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $mes, PDO::PARAM_INT);
            $stmt->bindParam(2, $anio, PDO::PARAM_INT);
            $stmt->bindParam(3, $metodo_pago, PDO::PARAM_STR);
            $stmt->bindParam(4, $forzar, PDO::PARAM_BOOL);
            $stmt->execute();

            // Obtener todos los resultados (pueden ser múltiples conjuntos)
            $resultados = [];
            do {
                $resultados[] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } while ($stmt->nextRowset());

            // El primer conjunto contiene el resumen, el segundo los detalles
            $resumen = isset($resultados[0][0]) ? $resultados[0][0] : [];
            $detalles = isset($resultados[1]) ? $resultados[1] : [];

            return [
                'resumen' => $resumen,
                'detalles' => $detalles
            ];
        } catch (PDOException $e) {
            throw new Exception("Error al generar planilla completa: " . $e->getMessage());
        }
    }

    /**
     * Generar planilla para UN empleado específico (ACTUALIZADO)
     */
    public function generarPlanillaPersonalizada($id_persona, $mes, $anio, $dias_descuento, $metodo_pago = 'transferencia', $forzar = false) {
        try {
            $query = "CALL GenerarPlanillaPersonalizada(?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $id_persona, PDO::PARAM_INT);
            $stmt->bindParam(2, $mes, PDO::PARAM_INT);
            $stmt->bindParam(3, $anio, PDO::PARAM_INT);
            $stmt->bindParam(4, $dias_descuento, PDO::PARAM_STR);
            $stmt->bindParam(5, $metodo_pago, PDO::PARAM_STR);
            $stmt->bindParam(6, $forzar, PDO::PARAM_BOOL);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $resultados;
        } catch (PDOException $e) {
            throw new Exception("Error al generar planilla personalizada: " . $e->getMessage());
        }
    }

    /**
     * Generar planillas múltiples usando JSON (ACTUALIZADO)
     */
    public function generarPlanillaMultiple($mes, $anio, $json_descuentos, $metodo_pago = 'transferencia') {
        try {
            // Validar que el JSON sea válido
            if (!is_array($json_descuentos)) {
                throw new Exception("El parámetro debe ser un array asociativo");
            }

            // Convertir array a JSON
            $json_string = json_encode($json_descuentos);
            if ($json_string === false) {
                throw new Exception("Error al codificar JSON");
            }

            $query = "CALL GenerarPlanillaMultipleAvanzada(?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $mes, PDO::PARAM_INT);
            $stmt->bindParam(2, $anio, PDO::PARAM_INT);
            $stmt->bindParam(3, $json_string, PDO::PARAM_STR);
            $stmt->bindParam(4, $metodo_pago, PDO::PARAM_STR);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $resultados;
        } catch (PDOException $e) {
            throw new Exception("Error al generar planillas múltiples: " . $e->getMessage());
        }
    }

    /**
     * Listar TODAS las planillas (completo - para administración)
     */
    public function listarPlanillasCompleto($mes = null, $anio = null) {
        try {
            $where = "";
            $params = [];

            if ($mes && $anio) {
                $where = "WHERE DATE_FORMAT(pe.periodo, '%Y-%m') = ?";
                $params[] = sprintf("%04d-%02d", $anio, $mes);
            }

            $query = "
                SELECT 
                    pe.id_planilla_emp,
                    pe.periodo,
                    p.id_persona,
                    p.nombre,
                    p.apellido_paterno, 
                    p.apellido_materno,
                    r.rol,
                    pe.haber_basico,
                    pe.dias_trabajados,
                    pe.total_ganado,
                    pe.descuento_gestora,
                    pe.total_descuentos,
                    pe.liquido_pagable,
                    pe.estado,
                    pe.metodo_pago,
                    pe.fecha_pago,
                    pe.fecha_creacion
                FROM planilla_empleado pe
                JOIN persona p ON pe.id_persona = p.id_persona
                JOIN rol r ON pe.id_rol = r.id_rol
                $where
                ORDER BY pe.periodo DESC, p.nombre ASC
            ";

            $stmt = $this->db->prepare($query);
            if ($mes && $anio) {
                $stmt->execute($params);
            } else {
                $stmt->execute();
            }

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Descifrar datos sensibles de las personas
            foreach ($resultados as &$fila) {
                $fila['nombre'] = $this->decrypt($fila['nombre']);
                $fila['apellido_paterno'] = $this->decrypt($fila['apellido_paterno']);
                $fila['apellido_materno'] = $this->decrypt($fila['apellido_materno']);

                // Crear nombre completo descifrado
                $fila['nombre_completo'] = trim($fila['nombre'] . ' ' . $fila['apellido_paterno'] . ' ' . ($fila['apellido_materno'] ?? ''));
            }

            return $resultados;
        } catch (PDOException $e) {
            throw new Exception("Error al listar planillas: " . $e->getMessage());
        }
    }

    /**
     * Listar MI planilla (para empleado específico)
     */
    public function listarMiPlanilla($id_persona, $mes = null, $anio = null) {
        try {
            $where = "WHERE pe.id_persona = ?";
            $params = [$id_persona];

            if ($mes && $anio) {
                $where .= " AND DATE_FORMAT(pe.periodo, '%Y-%m') = ?";
                $params[] = sprintf("%04d-%02d", $anio, $mes);
            }

            $query = "
                SELECT 
                    pe.id_planilla_emp,
                    pe.periodo,
                    p.nombre,
                    p.apellido_paterno,
                    p.apellido_materno,
                    r.rol,
                    pe.haber_basico,
                    pe.dias_trabajados,
                    pe.total_ganado,
                    pe.descuento_gestora,
                    pe.total_descuentos,
                    pe.liquido_pagable,
                    pe.estado,
                    pe.metodo_pago,
                    pe.fecha_pago,
                    pe.fecha_creacion,
                    CASE 
                        WHEN pe.dias_trabajados < 30 THEN CONCAT('Descuento por ', (30 - pe.dias_trabajados), ' días')
                        ELSE 'Tiempo completo'
                    END as observacion
                FROM planilla_empleado pe
                JOIN persona p ON pe.id_persona = p.id_persona
                JOIN rol r ON pe.id_rol = r.id_rol
                $where
                ORDER BY pe.periodo DESC
                LIMIT 12
            ";

            $stmt = $this->db->prepare($query);
            $stmt->execute($params);

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Descifrar datos sensibles
            foreach ($resultados as &$fila) {
                $fila['nombre'] = $this->decrypt($fila['nombre']);
                $fila['apellido_paterno'] = $this->decrypt($fila['apellido_paterno']);
                $fila['apellido_materno'] = $this->decrypt($fila['apellido_materno']);

                // Crear nombre completo descifrado
                $fila['nombre_completo'] = trim($fila['nombre'] . ' ' . $fila['apellido_paterno'] . ' ' . ($fila['apellido_materno'] ?? ''));
            }

            return $resultados;
        } catch (PDOException $e) {
            throw new Exception("Error al listar mi planilla: " . $e->getMessage());
        }
    }

    /**
     * Obtener estadísticas de planillas
     */
    public function obtenerEstadisticasPlanillas($mes, $anio) {
        try {
            $periodo = sprintf("%04d-%02d-01", $anio, $mes);

            $query = "
                SELECT 
                    COUNT(*) as total_empleados,
                    SUM(pe.haber_basico) as total_salarios_base,
                    SUM(pe.total_ganado) as total_ganado,
                    SUM(pe.descuento_gestora) as total_gestora,
                    SUM(pe.total_descuentos) as total_descuentos,
                    SUM(pe.liquido_pagable) as total_liquido,
                    AVG(pe.dias_trabajados) as promedio_dias_trabajados,
                    MIN(pe.liquido_pagable) as minimo_liquido,
                    MAX(pe.liquido_pagable) as maximo_liquido
                FROM planilla_empleado pe
                WHERE pe.periodo = ?
            ";

            $stmt = $this->db->prepare($query);
            $stmt->execute([$periodo]);

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener estadísticas: " . $e->getMessage());
        }
    }

    /**
     * Verificar si ya existe planilla para un periodo
     */
    public function verificarPlanillaExistente($mes, $anio) {
        try {
            $periodo = sprintf("%04d-%02d-01", $anio, $mes);

            $query = "SELECT COUNT(*) as total FROM planilla_empleado WHERE periodo = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$periodo]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] > 0;
        } catch (PDOException $e) {
            throw new Exception("Error al verificar planilla: " . $e->getMessage());
        }
    }

    /**
     * Obtener empleados activos para planilla
     */
    public function obtenerEmpleadosActivos() {
        try {
            $query = "
                SELECT 
                    p.id_persona,
                    p.nombre,
                    p.apellido_paterno,
                    p.apellido_materno,
                    r.rol,
                    r.salario_base,
                    p.estado
                FROM persona p
                JOIN rol r ON p.id_rol = r.id_rol
                WHERE p.estado = 'activo' 
                AND r.salario_base > 0
                ORDER BY p.nombre ASC
            ";

            $stmt = $this->db->prepare($query);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Descifrar datos sensibles
            foreach ($resultados as &$fila) {
                $fila['nombre'] = $this->decrypt($fila['nombre']);
                $fila['apellido_paterno'] = $this->decrypt($fila['apellido_paterno']);
                $fila['apellido_materno'] = $this->decrypt($fila['apellido_materno']);

                // Crear nombre completo descifrado
                $fila['nombre_completo'] = trim($fila['nombre'] . ' ' . $fila['apellido_paterno'] . ' ' . ($fila['apellido_materno'] ?? ''));
            }

            return $resultados;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener empleados: " . $e->getMessage());
        }
    }

    /**
     * Obtener detalle de una planilla específica
     */
    public function obtenerDetallePlanilla($id_planilla_emp) {
        try {
            $query = "
                SELECT 
                    pe.*,
                    p.nombre,
                    p.apellido_paterno,
                    p.apellido_materno,
                    r.rol,
                    r.descripcion as rol_descripcion
                FROM planilla_empleado pe
                JOIN persona p ON pe.id_persona = p.id_persona
                JOIN rol r ON pe.id_rol = r.id_rol
                WHERE pe.id_planilla_emp = ?
            ";

            $stmt = $this->db->prepare($query);
            $stmt->execute([$id_planilla_emp]);

            if ($stmt->rowCount() == 1) {
                $planilla = $stmt->fetch(PDO::FETCH_ASSOC);

                // Descifrar datos sensibles
                $planilla['nombre'] = $this->decrypt($planilla['nombre']);
                $planilla['apellido_paterno'] = $this->decrypt($planilla['apellido_paterno']);
                $planilla['apellido_materno'] = $this->decrypt($planilla['apellido_materno']);

                // Crear nombre completo descifrado
                $planilla['nombre_completo'] = trim($planilla['nombre'] . ' ' . $planilla['apellido_paterno'] . ' ' . ($planilla['apellido_materno'] ?? ''));

                return $planilla;
            }

            return false;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener detalle de planilla: " . $e->getMessage());
        }
    }

    /**
     * Eliminar planilla por periodo
     */
    public function eliminarPlanillaPeriodo($mes, $anio) {
        try {
            $periodo = sprintf("%04d-%02d-01", $anio, $mes);

            $query = "DELETE FROM planilla_empleado WHERE periodo = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$periodo]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar planilla: " . $e->getMessage());
        }
    }

    /**
     * Obtener resumen anual de planillas
     */
    public function obtenerResumenAnual($anio, $id_persona = null) {
        try {
            $where = "WHERE YEAR(pe.periodo) = ?";
            $params = [$anio];

            if ($id_persona) {
                $where .= " AND pe.id_persona = ?";
                $params[] = $id_persona;
            }

            $query = "
                SELECT 
                    MONTH(pe.periodo) as mes,
                    COUNT(*) as total_planillas,
                    SUM(pe.total_ganado) as total_ganado,
                    SUM(pe.descuento_gestora) as total_gestora,
                    SUM(pe.liquido_pagable) as total_liquido
                FROM planilla_empleado pe
                $where
                GROUP BY MONTH(pe.periodo)
                ORDER BY mes ASC
            ";

            $stmt = $this->db->prepare($query);
            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener resumen anual: " . $e->getMessage());
        }
    }

    /**
     * Obtener una planilla específica por ID para un empleado
     */
    public function obtenerPlanillaPorId($id_planilla_emp, $id_persona) {
        try {
            $query = "
            SELECT 
                pe.id_planilla_emp,
                pe.periodo,
                pe.id_persona,
                p.nombre,
                p.apellido_paterno,
                p.apellido_materno,
                r.rol,
                pe.haber_basico,
                pe.dias_trabajados,
                pe.total_ganado,
                pe.descuento_gestora,
                pe.total_descuentos,
                pe.liquido_pagable,
                pe.estado,
                pe.metodo_pago,
                pe.fecha_pago,
                pe.fecha_creacion,
                CASE 
                    WHEN pe.dias_trabajados < 30 THEN CONCAT('Descuento por ', (30 - pe.dias_trabajados), ' días')
                    ELSE 'Tiempo completo'
                END as observacion
            FROM planilla_empleado pe
            JOIN persona p ON pe.id_persona = p.id_persona
            JOIN rol r ON pe.id_rol = r.id_rol
            WHERE pe.id_planilla_emp = ? AND pe.id_persona = ?
        ";

            $stmt = $this->db->prepare($query);
            $stmt->execute([$id_planilla_emp, $id_persona]);

            if ($stmt->rowCount() == 1) {
                $planilla = $stmt->fetch(PDO::FETCH_ASSOC);

                // Descifrar datos sensibles
                $planilla['nombre'] = $this->decrypt($planilla['nombre']);
                $planilla['apellido_paterno'] = $this->decrypt($planilla['apellido_paterno']);
                $planilla['apellido_materno'] = $this->decrypt($planilla['apellido_materno']);

                // Crear nombre completo descifrado
                $planilla['nombre_completo'] = trim($planilla['nombre'] . ' ' . $planilla['apellido_paterno'] . ' ' . ($planilla['apellido_materno'] ?? ''));

                return $planilla;
            }

            return false;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener planilla por ID: " . $e->getMessage());
        }
    }

    /**
     * Actualizar estado de pago de una planilla
     */
    public function actualizarEstadoPago($id_planilla_emp, $id_persona) {
        try {
            $query = "UPDATE planilla_empleado SET estado = 'pagada', fecha_pago = NOW() WHERE id_planilla_emp = ? AND id_persona = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$id_planilla_emp, $id_persona]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar estado de pago: " . $e->getMessage());
        }
    }

    /**
     * Obtener métodos de pago disponibles
     */
    public function obtenerMetodosPago() {
        return [
            'transferencia' => 'Transferencia Bancaria',
            'qr' => 'Pago QR',
            'efectivo' => 'Efectivo',
            'cheque' => 'Cheque'
        ];
    }
}
?>
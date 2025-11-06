<?php
class DashboardModelo
{
    private $db;
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

    // Método para descifrar datos
    private function decrypt($encrypted_data) {
        if (empty($encrypted_data)) return $encrypted_data;
        
        // Si el dato no parece estar cifrado (no es base64 válido), retornarlo tal cual
        if (!preg_match('/^[A-Za-z0-9+\/]+=*$/', $encrypted_data)) {
            return $encrypted_data;
        }
        
        try {
            $data = base64_decode($encrypted_data, true);
            if ($data === false || strlen($data) < 16) {
                // No es base64 válido o muy corto para contener IV + datos
                return $encrypted_data;
            }
            
            $iv = substr($data, 0, 16);
            $encrypted = substr($data, 16);
            
            if (empty($encrypted)) {
                return $encrypted_data;
            }
            
            $decrypted = openssl_decrypt($encrypted, 'AES-256-CBC', $this->encryption_key, OPENSSL_RAW_DATA, $iv);
            if ($decrypted === false) {
                // Si falla el descifrado, retornar el valor original
                return $encrypted_data;
            }
            return $decrypted;
        } catch (Exception $e) {
            // En caso de error, retornar el valor original en lugar de false
            return $encrypted_data;
        }
    }

    // MÉTODOS PARA DASHBOARD PERSONAL
    public function obtenerComunicadosPersonal() {
        try {
            $sql = "SELECT 
                        c.id_comunicado,
                        c.titulo,
                        c.contenido,
                        c.fecha_publicacion,
                        c.prioridad,
                        c.tipo_audiencia,
                        p.nombre,
                        p.apellido_paterno,
                        p.apellido_materno,
                        c.fecha_expiracion
                    FROM comunicado c
                    INNER JOIN persona p ON c.id_persona = p.id_persona
                    WHERE c.estado = 'publicado' 
                    AND (c.tipo_audiencia = 'Todos' OR c.tipo_audiencia = 'Personal')
                    AND (c.fecha_expiracion IS NULL OR c.fecha_expiracion >= CURDATE())
                    ORDER BY 
                        CASE c.prioridad
                            WHEN 'urgente' THEN 1
                            WHEN 'alta' THEN 2
                            WHEN 'media' THEN 3
                            WHEN 'baja' THEN 4
                        END,
                        c.fecha_publicacion DESC
                    LIMIT 6";

            $comunicados = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

            foreach ($comunicados as &$comunicado) {
                // Descifrar campos individuales
                $nombre = $this->decrypt($comunicado['nombre']) ?: ($comunicado['nombre'] ?? '');
                $apellido_paterno = $this->decrypt($comunicado['apellido_paterno']) ?: ($comunicado['apellido_paterno'] ?? '');
                $apellido_materno = $this->decrypt($comunicado['apellido_materno']) ?: ($comunicado['apellido_materno'] ?? '');
                
                // Concatenar nombre completo
                $comunicado['autor'] = trim($nombre . ' ' . $apellido_paterno . ' ' . $apellido_materno);
                
                // Eliminar campos individuales que ya no se necesitan
                unset($comunicado['nombre'], $comunicado['apellido_paterno'], $comunicado['apellido_materno']);
            }

            return $comunicados;
        } catch (Exception $e) {
            error_log("Error en obtenerComunicadosPersonal: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerIncidentesAsignados($id_personal) {
        try {
            $sql = "SELECT 
                        i.id_incidente,
                        i.descripcion,
                        i.descripcion_detallada,
                        i.fecha_registro,
                        i.estado,
                        ia.fecha_asignacion,
                        ia.fecha_atencion,
                        d.numero as departamento,
                        d.piso,
                        CONCAT(p.nombre, ' ', p.apellido_paterno) as residente,
                        p.telefono,
                        p.email
                    FROM incidente i
                    INNER JOIN incidente_asignado ia ON i.id_incidente = ia.id_incidente
                    INNER JOIN departamento d ON i.id_departamento = d.id_departamento
                    INNER JOIN persona p ON i.id_residente = p.id_persona
                    WHERE ia.id_personal = ?
                    AND i.estado IN ('pendiente', 'en_proceso')
                    ORDER BY i.fecha_registro DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_personal]);
            $incidentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($incidentes as &$incidente) {
                $incidente['residente'] = $this->decrypt($incidente['residente']) ?: ($incidente['residente'] ?? '');
            }

            return $incidentes;
        } catch (Exception $e) {
            error_log("Error en obtenerIncidentesAsignados: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerEstadisticasIncidentesPersonal($id_personal) {
        try {
            $sql = "SELECT 
                        i.estado,
                        COUNT(*) as cantidad
                    FROM incidente i
                    INNER JOIN incidente_asignado ia ON i.id_incidente = ia.id_incidente
                    WHERE ia.id_personal = ?
                    GROUP BY i.estado";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_personal]);
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $estadisticas = [
                'pendientes' => 0,
                'en_proceso' => 0,
                'atendidos' => 0,
                'total' => 0
            ];

            foreach ($resultados as $fila) {
                $estadisticas['total'] += $fila['cantidad'];
                if ($fila['estado'] == 'pendiente') {
                    $estadisticas['pendientes'] += $fila['cantidad'];
                } elseif ($fila['estado'] == 'en_proceso') {
                    $estadisticas['en_proceso'] += $fila['cantidad'];
                } elseif ($fila['estado'] == 'resuelto') {
                    $estadisticas['atendidos'] += $fila['cantidad'];
                }
            }

            return $estadisticas;
        } catch (Exception $e) {
            error_log("Error en obtenerEstadisticasIncidentesPersonal: " . $e->getMessage());
            return [
                'pendientes' => 0,
                'en_proceso' => 0,
                'atendidos' => 0,
                'total' => 0
            ];
        }
    }

    public function obtenerReservasConfirmadas() {
        try {
            $sql = "SELECT 
                        r.id_reserva,
                        a.nombre as nombre_area,
                        a.descripcion as descripcion_area,
                        CONCAT(p.nombre, ' ', p.apellido_paterno) as nombre_residente,
                        p.telefono,
                        p.email,
                        r.fecha_reserva,
                        r.hora_inicio,
                        r.hora_fin,
                        r.motivo,
                        r.estado,
                        d.numero as departamento
                    FROM reserva_area_comun r
                    INNER JOIN area_comun a ON r.id_area = a.id_area
                    INNER JOIN persona p ON r.id_persona = p.id_persona
                    INNER JOIN tiene_departamento td ON p.id_persona = td.id_persona
                    INNER JOIN departamento d ON td.id_departamento = d.id_departamento
                    WHERE r.estado = 'confirmada'
                    AND r.fecha_reserva >= CURDATE()
                    AND MONTH(r.fecha_reserva) = MONTH(CURDATE())
                    AND YEAR(r.fecha_reserva) = YEAR(CURDATE())
                    ORDER BY r.fecha_reserva, r.hora_inicio
                    LIMIT 15";

            $reservas = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

            foreach ($reservas as &$reserva) {
                $reserva['nombre_residente'] = $this->decrypt($reserva['nombre_residente']) ?: ($reserva['nombre_residente'] ?? '');
            }

            return $reservas;
        } catch (Exception $e) {
            error_log("Error en obtenerReservasConfirmadas: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerResidentesActivos() {
        try {
            $sql = "SELECT 
                        p.id_persona,
                        p.nombre,
                        p.apellido_paterno,
                        p.apellido_materno,
                        p.email,
                        p.telefono,
                        p.ci,
                        d.numero as departamento,
                        d.piso,
                        p.estado
                    FROM persona p
                    INNER JOIN tiene_departamento td ON p.id_persona = td.id_persona
                    INNER JOIN departamento d ON td.id_departamento = d.id_departamento
                    INNER JOIN rol r ON p.id_rol = r.id_rol
                    WHERE p.estado = 'activo'
                    AND td.estado = 'activo'
                    AND r.rol = 'Residente'
                    ORDER BY d.piso, d.numero, p.nombre, p.apellido_paterno
                    LIMIT 25";

            $residentes = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

            foreach ($residentes as &$residente) {
                $residente['nombre'] = $this->decrypt($residente['nombre']) ?: ($residente['nombre'] ?? '');
                $residente['apellido_paterno'] = $this->decrypt($residente['apellido_paterno']) ?: ($residente['apellido_paterno'] ?? '');
                $residente['apellido_materno'] = $this->decrypt($residente['apellido_materno']) ?: ($residente['apellido_materno'] ?? '');
                $residente['ci'] = $this->decrypt($residente['ci']) ?: ($residente['ci'] ?? '');
            }

            return $residentes;
        } catch (Exception $e) {
            error_log("Error en obtenerResidentesActivos: " . $e->getMessage());
            return [];
        }
    }

    // MÉTODOS PARA DASHBOARD RESIDENTE
    public function obtenerDepartamentoResidente($id_residente) {
        try {
            $sql = "SELECT 
                    d.id_departamento,
                    d.numero,
                    d.piso
                FROM departamento d
                INNER JOIN tiene_departamento td ON d.id_departamento = td.id_departamento
                WHERE td.id_persona = ?
                AND td.estado = 'activo'
                AND d.estado = 'ocupado'";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_residente]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerDepartamentoResidente: " . $e->getMessage());
            return null;
        }
    }

    public function obtenerComunicadosResidente() {
        try {
            $sql = "SELECT 
                    c.id_comunicado,
                    c.titulo,
                    c.contenido,
                    c.fecha_publicacion,
                    c.prioridad,
                    c.tipo_audiencia,
                    p.nombre,
                    p.apellido_paterno,
                    p.apellido_materno,
                    c.fecha_expiracion
                FROM comunicado c
                INNER JOIN persona p ON c.id_persona = p.id_persona
                WHERE c.estado = 'publicado' 
                AND (c.tipo_audiencia = 'Todos' OR c.tipo_audiencia = 'Residente')
                AND (c.fecha_expiracion IS NULL OR c.fecha_expiracion >= CURDATE())
                ORDER BY 
                    CASE c.prioridad
                        WHEN 'urgente' THEN 1
                        WHEN 'alta' THEN 2
                        WHEN 'media' THEN 3
                        WHEN 'baja' THEN 4
                    END,
                    c.fecha_publicacion DESC
                LIMIT 5";

            $comunicados = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

            foreach ($comunicados as &$comunicado) {
                // Descifrar campos individuales
                $nombre = $this->decrypt($comunicado['nombre']) ?: ($comunicado['nombre'] ?? '');
                $apellido_paterno = $this->decrypt($comunicado['apellido_paterno']) ?: ($comunicado['apellido_paterno'] ?? '');
                $apellido_materno = $this->decrypt($comunicado['apellido_materno']) ?: ($comunicado['apellido_materno'] ?? '');
                
                // Concatenar nombre completo
                $comunicado['autor'] = trim($nombre . ' ' . $apellido_paterno . ' ' . $apellido_materno);
                
                // Eliminar campos individuales que ya no se necesitan
                unset($comunicado['nombre'], $comunicado['apellido_paterno'], $comunicado['apellido_materno']);
            }

            return $comunicados;
        } catch (Exception $e) {
            error_log("Error en obtenerComunicadosResidente: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerConsumoDiarioResidente($id_departamento, $dias = 7) {
        try {
            $sql = "SELECT 
                    s.nombre as servicio,
                    s.unidad_medida,
                    DATE(ls.fecha_hora) as fecha,
                    SUM(ls.consumo) as consumo_diario
                FROM lector_sensor_consumo ls
                INNER JOIN medidor m ON ls.id_medidor = m.id_medidor
                INNER JOIN servicio s ON m.id_servicio = s.id_servicio
                WHERE m.id_departamento = ?
                AND ls.fecha_hora >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                GROUP BY s.nombre, s.unidad_medida, DATE(ls.fecha_hora)
                ORDER BY fecha DESC, s.nombre";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_departamento, $dias]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerConsumoDiarioResidente: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerConsumoMensualResidente($id_departamento, $meses = 6) {
        try {
            $sql = "SELECT 
                    s.nombre as servicio,
                    s.unidad_medida,
                    YEAR(hc.fecha_inicio) as año,
                    MONTH(hc.fecha_inicio) as mes,
                    hc.consumo_total as consumo_mensual
                FROM historial_consumo hc
                INNER JOIN medidor m ON hc.id_medidor = m.id_medidor
                INNER JOIN servicio s ON m.id_servicio = s.id_servicio
                WHERE m.id_departamento = ?
                AND hc.fecha_inicio >= DATE_SUB(CURDATE(), INTERVAL ? MONTH)
                ORDER BY año DESC, mes DESC, s.nombre";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_departamento, $meses]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerConsumoMensualResidente: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerFacturasResidente($id_residente) {
        try {
            $sql = "SELECT 
                    f.id_factura,
                    f.fecha_emision,
                    f.fecha_vencimiento,
                    f.monto_total,
                    f.estado,
                    s.nombre as servicio,
                    d.numero as departamento,
                    CASE 
                        WHEN f.fecha_vencimiento < CURDATE() AND f.estado = 'pendiente' THEN 'vencida'
                        ELSE f.estado
                    END as estado_real
                FROM factura f
                INNER JOIN servicio s ON f.id_servicio = s.id_servicio
                INNER JOIN departamento d ON f.id_departamento = d.id_departamento
                INNER JOIN tiene_departamento td ON d.id_departamento = td.id_departamento
                WHERE td.id_persona = ?
                AND td.estado = 'activo'
                ORDER BY f.fecha_vencimiento DESC, f.estado";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_residente]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerFacturasResidente: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerIncidentesResidente($id_residente) {
        try {
            $sql = "SELECT 
                    i.id_incidente,
                    i.descripcion,
                    i.descripcion_detallada,
                    i.fecha_registro,
                    i.estado,
                    d.numero as departamento,
                    CONCAT(p.nombre, ' ', p.apellido_paterno) as personal_asignado
                FROM incidente i
                LEFT JOIN incidente_asignado ia ON i.id_incidente = ia.id_incidente
                LEFT JOIN persona p ON ia.id_personal = p.id_persona
                INNER JOIN departamento d ON i.id_departamento = d.id_departamento
                INNER JOIN tiene_departamento td ON d.id_departamento = td.id_departamento
                WHERE td.id_persona = ?
                AND i.id_residente = ?
                ORDER BY i.fecha_registro DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_residente, $id_residente]);
            $incidentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($incidentes as &$incidente) {
                $incidente['personal_asignado'] = $this->decrypt($incidente['personal_asignado']) ?: ($incidente['personal_asignado'] ?? '');
            }

            return $incidentes;
        } catch (Exception $e) {
            error_log("Error en obtenerIncidentesResidente: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerReservasResidente($id_residente) {
        try {
            $sql = "SELECT 
                    r.id_reserva,
                    a.nombre as area_comun,
                    r.fecha_reserva,
                    r.hora_inicio,
                    r.hora_fin,
                    r.motivo,
                    r.estado,
                    a.costo_reserva
                FROM reserva_area_comun r
                INNER JOIN area_comun a ON r.id_area = a.id_area
                WHERE r.id_persona = ?
                AND r.fecha_reserva >= CURDATE()
                ORDER BY r.fecha_reserva, r.hora_inicio";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_residente]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerReservasResidente: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerEstadisticasResidente($id_residente) {
        try {
            $departamento = $this->obtenerDepartamentoResidente($id_residente);
            $id_departamento = $departamento ? $departamento['id_departamento'] : null;

            $estadisticas = [
                'total_facturas_pendientes' => 0,
                'total_reservas_activas' => 0,
                'total_incidentes_activos' => 0,
                'consumo_agua_actual' => 0,
                'departamento' => $departamento
            ];

            // Facturas pendientes
            $sql_facturas = "SELECT COUNT(*) as total 
                        FROM factura f
                        INNER JOIN tiene_departamento td ON f.id_departamento = td.id_departamento
                        WHERE td.id_persona = ?
                        AND f.estado = 'pendiente'";
            $stmt = $this->db->prepare($sql_facturas);
            $stmt->execute([$id_residente]);
            $estadisticas['total_facturas_pendientes'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Reservas activas
            $sql_reservas = "SELECT COUNT(*) as total 
                        FROM reserva_area_comun 
                        WHERE id_persona = ? 
                        AND estado = 'confirmada'
                        AND fecha_reserva >= CURDATE()";
            $stmt = $this->db->prepare($sql_reservas);
            $stmt->execute([$id_residente]);
            $estadisticas['total_reservas_activas'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Incidentes activos
            $sql_incidentes = "SELECT COUNT(*) as total 
                          FROM incidente i
                          INNER JOIN tiene_departamento td ON i.id_departamento = td.id_departamento
                          WHERE td.id_persona = ?
                          AND i.estado IN ('pendiente', 'en_proceso')";
            $stmt = $this->db->prepare($sql_incidentes);
            $stmt->execute([$id_residente]);
            $estadisticas['total_incidentes_activos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Consumo actual de agua
            if ($id_departamento) {
                $sql_consumo = "SELECT AVG(ls.consumo) as consumo_actual
                           FROM lector_sensor_consumo ls
                           INNER JOIN medidor m ON ls.id_medidor = m.id_medidor
                           WHERE m.id_departamento = ?
                           AND m.id_servicio = (SELECT id_servicio FROM servicio WHERE nombre = 'agua')
                           AND ls.fecha_hora >= DATE_SUB(NOW(), INTERVAL 1 DAY)";
                $stmt = $this->db->prepare($sql_consumo);
                $stmt->execute([$id_departamento]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $estadisticas['consumo_agua_actual'] = $result['consumo_actual'] ?? 0;
            }

            return $estadisticas;
        } catch (Exception $e) {
            error_log("Error en obtenerEstadisticasResidente: " . $e->getMessage());
            return [
                'total_facturas_pendientes' => 0,
                'total_reservas_activas' => 0,
                'total_incidentes_activos' => 0,
                'consumo_agua_actual' => 0,
                'departamento' => null
            ];
        }
    }

    public function obtenerInfoPersonal($id_personal) {
        try {
            $sql = "SELECT 
                        p.id_persona,
                        p.nombre,
                        p.apellido_paterno,
                        p.apellido_materno,
                        p.email,
                        p.telefono,
                        r.rol
                    FROM persona p
                    INNER JOIN rol r ON p.id_rol = r.id_rol
                    WHERE p.id_persona = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_personal]);
            $personal = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($personal) {
                $personal['nombre'] = $this->decrypt($personal['nombre']) ?: ($personal['nombre'] ?? '');
                $personal['apellido_paterno'] = $this->decrypt($personal['apellido_paterno']) ?: ($personal['apellido_paterno'] ?? '');
                $personal['apellido_materno'] = $this->decrypt($personal['apellido_materno']) ?: ($personal['apellido_materno'] ?? '');
            }

            return $personal;
        } catch (Exception $e) {
            error_log("Error en obtenerInfoPersonal: " . $e->getMessage());
            return null;
        }
    }



    // MÉTODOS PARA DASHBOARD ADMINISTRADOR

    /**
     * Obtener estadísticas generales del sistema
     */
    public function obtenerEstadisticasGenerales() {
        try {
            $estadisticas = [];

            // Total de residentes
            $sql_residentes = "SELECT COUNT(*) as total FROM persona p 
                          INNER JOIN rol r ON p.id_rol = r.id_rol 
                          WHERE r.rol = 'Residente' AND p.estado = 'activo'";
            $estadisticas['total_residentes'] = $this->db->query($sql_residentes)->fetch(PDO::FETCH_ASSOC)['total'];

            // Total de departamentos
            $sql_departamentos = "SELECT COUNT(*) as total FROM departamento WHERE estado = 'activo'";
            $estadisticas['total_departamentos'] = $this->db->query($sql_departamentos)->fetch(PDO::FETCH_ASSOC)['total'];

            // Total de personal
            $sql_personal = "SELECT COUNT(*) as total FROM persona p 
                        INNER JOIN rol r ON p.id_rol = r.id_rol 
                        WHERE r.rol != 'Residente' AND p.estado = 'activo'";
            $estadisticas['total_personal'] = $this->db->query($sql_personal)->fetch(PDO::FETCH_ASSOC)['total'];

            // Total de áreas comunes
            $sql_areas = "SELECT COUNT(*) as total FROM area_comun WHERE estado = 'activo'";
            $estadisticas['total_areas'] = $this->db->query($sql_areas)->fetch(PDO::FETCH_ASSOC)['total'];

            // Total de incidentes activos
            $sql_incidentes = "SELECT COUNT(*) as total FROM incidente WHERE estado IN ('pendiente', 'en_proceso')";
            $estadisticas['total_incidentes_activos'] = $this->db->query($sql_incidentes)->fetch(PDO::FETCH_ASSOC)['total'];

            // Total de reservas este mes
            $sql_reservas = "SELECT COUNT(*) as total FROM reserva_area_comun 
                        WHERE MONTH(fecha_reserva) = MONTH(CURDATE()) 
                        AND YEAR(fecha_reserva) = YEAR(CURDATE())";
            $estadisticas['total_reservas_mes'] = $this->db->query($sql_reservas)->fetch(PDO::FETCH_ASSOC)['total'];

            return $estadisticas;
        } catch (Exception $e) {
            error_log("Error en obtenerEstadisticasGenerales: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener consumo mensual general por departamento
     */
    public function obtenerConsumoMensualGeneral($mes = null, $anio = null) {
        try {
            $mes = $mes ?? date('m');
            $anio = $anio ?? date('Y');

            $sql = "SELECT 
                    d.id_departamento,
                    d.numero as departamento,
                    d.piso,
                    s.nombre as servicio,
                    s.unidad_medida,
                    COALESCE(hc.consumo_total, 0) as consumo_mensual,
                    (COALESCE(hc.consumo_total, 0) * s.costo_unitario) as costo_mensual,
                    hc.fecha_inicio,
                    hc.fecha_fin
                FROM departamento d
                CROSS JOIN servicio s
                LEFT JOIN medidor m ON d.id_departamento = m.id_departamento AND m.id_servicio = s.id_servicio
                LEFT JOIN historial_consumo hc ON m.id_medidor = hc.id_medidor 
                    AND MONTH(hc.fecha_inicio) = ? 
                    AND YEAR(hc.fecha_inicio) = ?
                WHERE d.estado = 'ocupado'
                AND s.estado = 'activo'
                ORDER BY d.piso, d.numero, s.nombre";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$mes, $anio]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerConsumoMensualGeneral: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener estadísticas de consumo general
     */
    public function obtenerEstadisticasConsumoGeneral($mes = null, $anio = null) {
        try {
            $mes = $mes ?? date('m');
            $anio = $anio ?? date('Y');

            $sql = "SELECT 
                    s.nombre as servicio,
                    s.unidad_medida,
                    COUNT(DISTINCT d.id_departamento) as departamentos_con_consumo,
                    AVG(hc.consumo_total) as consumo_promedio,
                    MAX(hc.consumo_total) as consumo_maximo,
                    MIN(hc.consumo_total) as consumo_minimo,
                    SUM(hc.consumo_total) as consumo_total,
                    SUM(hc.consumo_total * s.costo_unitario) as costo_total
                FROM servicio s
                LEFT JOIN medidor m ON s.id_servicio = m.id_servicio
                LEFT JOIN departamento d ON m.id_departamento = d.id_departamento
                LEFT JOIN historial_consumo hc ON m.id_medidor = hc.id_medidor 
                    AND MONTH(hc.fecha_inicio) = ? 
                    AND YEAR(hc.fecha_inicio) = ?
                WHERE s.estado = 'activo'
                GROUP BY s.id_servicio, s.nombre, s.unidad_medida
                ORDER BY s.nombre";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$mes, $anio]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerEstadisticasConsumoGeneral: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener todos los residentes
     */
    public function obtenerTodosResidentes() {
        try {
            $sql = "SELECT 
                    p.id_persona,
                    p.nombre,
                    p.apellido_paterno,
                    p.apellido_materno,
                    p.email,
                    p.telefono,
                    p.ci,
                    d.numero as departamento,
                    d.piso,
                    p.estado
                FROM persona p
                INNER JOIN tiene_departamento td ON p.id_persona = td.id_persona
                INNER JOIN departamento d ON td.id_departamento = d.id_departamento
                INNER JOIN rol r ON p.id_rol = r.id_rol
                WHERE p.estado = 'activo'
                AND td.estado = 'activo'
                AND r.rol = 'Residente'
                ORDER BY d.piso, d.numero, p.nombre, p.apellido_paterno";

            $residentes = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

            foreach ($residentes as &$residente) {
                $residente['nombre'] = $this->decrypt($residente['nombre']) ?: $residente['nombre'];
                $residente['apellido_paterno'] = $this->decrypt($residente['apellido_paterno']) ?: $residente['apellido_paterno'];
                $residente['apellido_materno'] = $this->decrypt($residente['apellido_materno']) ?: ($residente['apellido_materno'] ?? '');
                $residente['ci'] = $this->decrypt($residente['ci']) ?: $residente['ci'];
            }

            return $residentes;
        } catch (Exception $e) {
            error_log("Error en obtenerTodosResidentes: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener todos los incidentes
     */
    public function obtenerTodosIncidentes() {
        try {
            $sql = "SELECT 
                    i.id_incidente,
                    i.descripcion,
                    i.descripcion_detallada,
                    i.fecha_registro,
                    i.estado,
                    d.numero as departamento,
                    d.piso,
                    CONCAT(pr.nombre, ' ', pr.apellido_paterno) as residente,
                    CONCAT(pp.nombre, ' ', pp.apellido_paterno) as personal_asignado
                FROM incidente i
                INNER JOIN departamento d ON i.id_departamento = d.id_departamento
                INNER JOIN persona pr ON i.id_residente = pr.id_persona
                LEFT JOIN incidente_asignado ia ON i.id_incidente = ia.id_incidente
                LEFT JOIN persona pp ON ia.id_personal = pp.id_persona
                ORDER BY i.fecha_registro DESC
                LIMIT 50";

            $incidentes = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

            foreach ($incidentes as &$incidente) {
                $incidente['residente'] = $this->decrypt($incidente['residente']) ?: ($incidente['residente'] ?? '');
                $incidente['personal_asignado'] = $this->decrypt($incidente['personal_asignado']) ?: ($incidente['personal_asignado'] ?? '');
            }

            return $incidentes;
        } catch (Exception $e) {
            error_log("Error en obtenerTodosIncidentes: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener todas las áreas comunes
     */
    public function obtenerTodasAreas() {
        try {
            $sql = "SELECT 
                    id_area,
                    nombre,
                    descripcion,
                    capacidad,
                    costo_reserva,
                    estado,
                    fecha_inicio_mantenimiento,
                    fecha_fin_mantenimiento
                FROM area_comun
                WHERE estado != 'eliminado'
                ORDER BY nombre";

            return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerTodasAreas: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener todas las reservas
     */
    public function obtenerTodasReservas() {
        try {
            $sql = "SELECT 
                    r.id_reserva,
                    a.nombre as area_comun,
                    p.nombre,
                    p.apellido_paterno,
                    p.apellido_materno,
                    d.numero as departamento,
                    r.fecha_reserva,
                    r.hora_inicio,
                    r.hora_fin,
                    r.motivo,
                    r.estado,
                    a.costo_reserva
                FROM reserva_area_comun r
                INNER JOIN area_comun a ON r.id_area = a.id_area
                INNER JOIN persona p ON r.id_persona = p.id_persona
                INNER JOIN tiene_departamento td ON p.id_persona = td.id_persona
                INNER JOIN departamento d ON td.id_departamento = d.id_departamento
                WHERE r.fecha_reserva >= CURDATE()
                ORDER BY r.fecha_reserva, r.hora_inicio
                LIMIT 50";

            $reservas = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

            foreach ($reservas as &$reserva) {
                // Descifrar los campos individuales
                $nombre = $this->decrypt($reserva['nombre']) ?: ($reserva['nombre'] ?? '');
                $apellido_paterno = $this->decrypt($reserva['apellido_paterno']) ?: ($reserva['apellido_paterno'] ?? '');
                $apellido_materno = $this->decrypt($reserva['apellido_materno']) ?: ($reserva['apellido_materno'] ?? '');
                
                // Concatenar el nombre completo
                $reserva['residente'] = trim($nombre . ' ' . $apellido_paterno . ' ' . $apellido_materno);
                
                // Eliminar los campos individuales que ya no se necesitan
                unset($reserva['nombre'], $reserva['apellido_paterno'], $reserva['apellido_materno']);
            }

            return $reservas;
        } catch (Exception $e) {
            error_log("Error en obtenerTodasReservas: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener todos los servicios
     */
    public function obtenerTodosServicios() {
        try {
            $sql = "SELECT 
                    id_servicio,
                    nombre,
                    unidad_medida,
                    costo_unitario,
                    estado
                FROM servicio
                ORDER BY nombre";

            return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerTodosServicios: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener consumo diario por departamento (para el selector)
     */
    public function obtenerConsumoDiarioPorDepartamento($id_departamento, $dias = 7) {
        try {
            $sql = "SELECT 
                    s.nombre as servicio,
                    s.unidad_medida,
                    DATE(ls.fecha_hora) as fecha,
                    SUM(ls.consumo) as consumo_diario
                FROM lector_sensor_consumo ls
                INNER JOIN medidor m ON ls.id_medidor = m.id_medidor
                INNER JOIN servicio s ON m.id_servicio = s.id_servicio
                WHERE m.id_departamento = ?
                AND ls.fecha_hora >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                GROUP BY s.nombre, s.unidad_medida, DATE(ls.fecha_hora)
                ORDER BY fecha DESC, s.nombre";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_departamento, $dias]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerConsumoDiarioPorDepartamento: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener lista de departamentos para selector
     */
    public function obtenerDepartamentosParaSelector() {
        try {
            $sql = "SELECT 
                    id_departamento,
                    numero,
                    piso
                FROM departamento
                WHERE estado = 'ocupado'
                ORDER BY piso, numero";

            return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerDepartamentosParaSelector: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener métricas financieras
     */
    public function obtenerMetricasFinancieras($mes = null, $anio = null) {
        try {
            $mes = $mes ?? date('m');
            $anio = $anio ?? date('Y');

            $metricas = [];

            // Ingresos del mes
            $sql_ingresos = "SELECT COALESCE(SUM(ppf.monto_pagado), 0) as ingresos_mes
                       FROM persona_paga_factura ppf 
                       WHERE MONTH(ppf.fecha_pago) = ? AND YEAR(ppf.fecha_pago) = ?";
            $stmt = $this->db->prepare($sql_ingresos);
            $stmt->execute([$mes, $anio]);
            $metricas['ingresos_mes'] = $stmt->fetch(PDO::FETCH_ASSOC)['ingresos_mes'];

            // Deuda total
            $sql_deuda = "SELECT COALESCE(SUM(f.monto_total), 0) as deuda_total,
                             COUNT(f.id_factura) as total_facturas_vencidas
                      FROM factura f 
                      LEFT JOIN persona_paga_factura ppf ON f.id_factura = ppf.id_factura 
                      WHERE ppf.id_factura IS NULL";
            $deuda = $this->db->query($sql_deuda)->fetch(PDO::FETCH_ASSOC);
            $metricas['deuda_total'] = $deuda['deuda_total'];
            $metricas['total_facturas_vencidas'] = $deuda['total_facturas_vencidas'];

            return $metricas;
        } catch (Exception $e) {
            error_log("Error en obtenerMetricasFinancieras: " . $e->getMessage());
            return [];
        }
    }



}
?>
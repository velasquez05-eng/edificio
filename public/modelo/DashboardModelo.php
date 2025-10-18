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

    // MÉTRICAS FINANCIERAS PRINCIPALES
    public function obtenerMetricasFinancieras($mes, $anio) {
        try {
            $metricas = [];

            // Ingresos totales del mes
            $sql_ingresos = "SELECT COALESCE(SUM(ppf.monto_pagado), 0) as ingresos_mes
                           FROM persona_paga_factura ppf 
                           JOIN factura f ON ppf.id_factura = f.id_factura 
                           WHERE MONTH(ppf.fecha_pago) = ? AND YEAR(ppf.fecha_pago) = ?";
            $stmt = $this->db->prepare($sql_ingresos);
            $stmt->execute([$mes, $anio]);
            $metricas['ingresos_mes'] = $stmt->fetch(PDO::FETCH_ASSOC)['ingresos_mes'];

            // Deuda total (facturas no pagadas)
            $sql_deuda = "SELECT COALESCE(SUM(f.monto_total), 0) as deuda_total,
                                 COUNT(f.id_factura) as total_facturas_vencidas
                          FROM factura f 
                          LEFT JOIN persona_paga_factura ppf ON f.id_factura = ppf.id_factura 
                          WHERE ppf.id_factura IS NULL";
            $deuda = $this->db->query($sql_deuda)->fetch(PDO::FETCH_ASSOC);
            $metricas['deuda_total'] = $deuda['deuda_total'];
            $metricas['total_facturas_vencidas'] = $deuda['total_facturas_vencidas'];

            // Morosidad (facturas vencidas no pagadas)
            $sql_morosidad = "SELECT COALESCE(SUM(f.monto_total), 0) as morosidad,
                                     COUNT(*) as facturas_vencidas,
                                     AVG(DATEDIFF(CURDATE(), f.fecha_vencimiento)) as promedio_dias_retraso
                              FROM factura f 
                              LEFT JOIN persona_paga_factura ppf ON f.id_factura = ppf.id_factura 
                              WHERE ppf.id_factura IS NULL AND f.fecha_vencimiento < CURDATE()";
            $morosidad = $this->db->query($sql_morosidad)->fetch(PDO::FETCH_ASSOC);
            $metricas['morosidad'] = $morosidad['morosidad'];
            $metricas['facturas_vencidas'] = $morosidad['facturas_vencidas'];
            $metricas['promedio_dias_retraso'] = $morosidad['promedio_dias_retraso'];

            return $metricas;
        } catch (Exception $e) {
            error_log("Error en obtenerMetricasFinancieras: " . $e->getMessage());
            return [];
        }
    }

    // DEPARTAMENTOS EN RIESGO Y CORTE
    public function obtenerDepartamentosProblema() {
        try {
            $departamentos = [];

            // Departamentos en riesgo (2 facturas vencidas)
            $sql_riesgo = "SELECT d.id_departamento, d.numero, d.piso,
                          COUNT(f.id_factura) as facturas_vencidas,
                          SUM(f.monto_total) as deuda_departamento
                          FROM departamento d
                          JOIN factura f ON d.id_departamento = f.id_departamento
                          LEFT JOIN persona_paga_factura ppf ON f.id_factura = ppf.id_factura
                          WHERE ppf.id_factura IS NULL AND f.fecha_vencimiento < CURDATE()
                          GROUP BY d.id_departamento
                          HAVING facturas_vencidas = 2";
            $departamentos['riesgo'] = $this->db->query($sql_riesgo)->fetchAll(PDO::FETCH_ASSOC);

            // Departamentos en corte (3+ facturas vencidas)
            $sql_corte = "SELECT d.id_departamento, d.numero, d.piso,
                         COUNT(f.id_factura) as facturas_vencidas,
                         SUM(f.monto_total) as deuda_departamento
                         FROM departamento d
                         JOIN factura f ON d.id_departamento = f.id_departamento
                         LEFT JOIN persona_paga_factura ppf ON f.id_factura = ppf.id_factura
                         WHERE ppf.id_factura IS NULL AND f.fecha_vencimiento < CURDATE()
                         GROUP BY d.id_departamento
                         HAVING facturas_vencidas >= 3";
            $departamentos['corte'] = $this->db->query($sql_corte)->fetchAll(PDO::FETCH_ASSOC);

            return $departamentos;
        } catch (Exception $e) {
            error_log("Error en obtenerDepartamentosProblema: " . $e->getMessage());
            return ['riesgo' => [], 'corte' => []];
        }
    }

    // CONSUMO PROMEDIO POR SERVICIO
    public function obtenerConsumosPromedio($mes, $anio) {
        try {
            $sql_consumo = "SELECT s.nombre as servicio,
                           AVG(hc.consumo_total) as consumo_promedio,
                           AVG(hc.consumo_total * s.costo_unitario) as costo_promedio,
                           s.unidad_medida
                           FROM historial_consumo hc
                           JOIN medidor m ON hc.id_medidor = m.id_medidor
                           JOIN servicio s ON m.id_servicio = s.id_servicio
                           WHERE MONTH(hc.fecha_inicio) = ? AND YEAR(hc.fecha_inicio) = ?
                           GROUP BY s.id_servicio";

            $stmt = $this->db->prepare($sql_consumo);
            $stmt->execute([$mes, $anio]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerConsumosPromedio: " . $e->getMessage());
            return [];
        }
    }

    // MÉTRICAS DE SEGURIDAD
    public function obtenerMetricasSeguridad() {
        try {
            $metricas = [];

            // Usuarios no verificados
            $sql_no_verificados = "SELECT COUNT(*) as no_verificados 
                                 FROM persona 
                                 WHERE verificado = FALSE AND fecha_eliminado IS NULL";
            $metricas['no_verificados'] = $this->db->query($sql_no_verificados)->fetch(PDO::FETCH_ASSOC)['no_verificados'];

            // Intentos fallidos de login
            $sql_login_fallidos = "SELECT p.username, p.email, r.rol,
                                 COUNT(hl.id_historial_login) as intentos_fallidos,
                                 MAX(hl.fecha) as ultimo_intento
                                 FROM persona p
                                 JOIN rol r ON p.id_rol = r.id_rol
                                 JOIN historial_login hl ON p.id_persona = hl.id_persona
                                 WHERE hl.estado = 'fallido' AND hl.fecha >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                                 GROUP BY p.id_persona
                                 HAVING intentos_fallidos > 0
                                 ORDER BY intentos_fallidos DESC
                                 LIMIT 10";
            $metricas['login_fallidos'] = $this->db->query($sql_login_fallidos)->fetchAll(PDO::FETCH_ASSOC);

            return $metricas;
        } catch (Exception $e) {
            error_log("Error en obtenerMetricasSeguridad: " . $e->getMessage());
            return ['no_verificados' => 0, 'login_fallidos' => []];
        }
    }

    // INCIDENTES Y MANTENIMIENTO
    public function obtenerIncidentesRecientes() {
        try {
            $sql_incidentes = "SELECT i.id_incidente, d.numero as departamento,
                              p.nombre as residente, i.descripcion, i.estado, i.fecha_registro,
                              COUNT(hi.id_historial_incidente) as actualizaciones
                              FROM incidente i
                              JOIN departamento d ON i.id_departamento = d.id_departamento
                              JOIN persona p ON i.id_residente = p.id_persona
                              LEFT JOIN historial_incidente hi ON i.id_incidente = hi.id_incidente
                              GROUP BY i.id_incidente
                              ORDER BY i.fecha_registro DESC
                              LIMIT 10";

            return $this->db->query($sql_incidentes)->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerIncidentesRecientes: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerHistorialIncidentes() {
        try {
            $sql_historial = "SELECT hi.id_historial_incidente, hi.id_incidente,
                             CONCAT(p.nombre, ' (', r.rol, ')') as persona, 
                             hi.accion, hi.observacion,
                             hi.estado_anterior, hi.estado_nuevo, hi.fecha_accion
                             FROM historial_incidente hi
                             JOIN persona p ON hi.id_persona = p.id_persona
                             JOIN rol r ON p.id_rol = r.id_rol
                             ORDER BY hi.fecha_accion DESC
                             LIMIT 15";

            return $this->db->query($sql_historial)->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerHistorialIncidentes: " . $e->getMessage());
            return [];
        }
    }

    // PROMEDIO DE PAGOS POR DEPARTAMENTO
    public function obtenerPromedioPagos() {
        try {
            $sql_promedio = "SELECT d.numero as departamento,
                            AVG(f.monto_total) as promedio_pago,
                            COUNT(ppf.id_factura) as facturas_pagadas,
                            SUM(f.monto_total) as total_pagado
                            FROM departamento d
                            JOIN factura f ON d.id_departamento = f.id_departamento
                            JOIN persona_paga_factura ppf ON f.id_factura = ppf.id_factura
                            GROUP BY d.id_departamento
                            ORDER BY promedio_pago DESC
                            LIMIT 10";

            return $this->db->query($sql_promedio)->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerPromedioPagos: " . $e->getMessage());
            return [];
        }
    }

    // RESERVAS RECIENTES
    public function obtenerReservasProximas() {
        try {
            $sql_reservas = "SELECT r.fecha_reserva, r.hora_inicio, r.hora_fin, 
                            r.motivo, r.estado, a.nombre as area,
                            p.nombre as persona
                     FROM reserva_area_comun r
                     JOIN area_comun a ON r.id_area = a.id_area
                     JOIN persona p ON r.id_persona = p.id_persona
                     WHERE r.fecha_reserva >= CURDATE()
                     ORDER BY r.fecha_reserva, r.hora_inicio
                     LIMIT 10";

            return $this->db->query($sql_reservas)->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerReservasProximas: " . $e->getMessage());
            return [];
        }
    }

    // FACTURAS VENCIDAS DETALLADAS
    public function obtenerFacturasVencidas() {
        try {
            $sql_facturas = "SELECT f.id_factura, d.numero as departamento,
                            s.nombre as servicio, f.monto_total,
                            f.fecha_vencimiento,
                            DATEDIFF(CURDATE(), f.fecha_vencimiento) as dias_vencida
                     FROM factura f
                     JOIN departamento d ON f.id_departamento = d.id_departamento
                     JOIN servicio s ON f.id_servicio = s.id_servicio
                     LEFT JOIN persona_paga_factura ppf ON f.id_factura = ppf.id_factura
                     WHERE ppf.id_factura IS NULL AND f.fecha_vencimiento < CURDATE()
                     ORDER BY f.fecha_vencimiento DESC
                     LIMIT 10";

            return $this->db->query($sql_facturas)->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerFacturasVencidas: " . $e->getMessage());
            return [];
        }
    }

    // ESTADÍSTICAS GENERALES DEL SISTEMA
    public function obtenerEstadisticasGenerales() {
        try {
            $estadisticas = [];

            // Total de residentes
            $sql_residentes = "SELECT COUNT(*) as total FROM persona p 
                              JOIN rol r ON p.id_rol = r.id_rol 
                              WHERE r.rol = 'Residente' AND p.estado = 'activo'";
            $estadisticas['total_residentes'] = $this->db->query($sql_residentes)->fetch(PDO::FETCH_ASSOC)['total'];

            // Total de departamentos
            $sql_departamentos = "SELECT COUNT(*) as total FROM departamento WHERE estado = 'activo'";
            $estadisticas['total_departamentos'] = $this->db->query($sql_departamentos)->fetch(PDO::FETCH_ASSOC)['total'];

            // Total de personal
            $sql_personal = "SELECT COUNT(*) as total FROM persona p 
                            JOIN rol r ON p.id_rol = r.id_rol 
                            WHERE r.rol != 'Residente' AND p.estado = 'activo'";
            $estadisticas['total_personal'] = $this->db->query($sql_personal)->fetch(PDO::FETCH_ASSOC)['total'];

            // Total de áreas comunes
            $sql_areas = "SELECT COUNT(*) as total FROM area_comun WHERE estado = 'activo'";
            $estadisticas['total_areas'] = $this->db->query($sql_areas)->fetch(PDO::FETCH_ASSOC)['total'];

            return $estadisticas;
        } catch (Exception $e) {
            error_log("Error en obtenerEstadisticasGenerales: " . $e->getMessage());
            return [];
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
                        CONCAT(p.nombre, ' ', p.apellido_paterno) as autor
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
                    LIMIT 5";

            $comunicados = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

            // Descifrar los nombres de los autores
            foreach ($comunicados as &$comunicado) {
                $comunicado['autor'] = $this->decrypt($comunicado['autor']);
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
                        i.fecha_registro,
                        i.estado,
                        ia.fecha_asignacion,
                        d.numero as departamento,
                        CONCAT(p.nombre, ' ', p.apellido_paterno) as residente
                    FROM incidente i
                    INNER JOIN incidente_asignado ia ON i.id_incidente = ia.id_incidente
                    INNER JOIN departamento d ON i.id_departamento = d.id_departamento
                    INNER JOIN persona p ON i.id_residente = p.id_persona
                    WHERE ia.id_personal = ?
                    AND i.estado IN ('pendiente', 'en_proceso')
                    ORDER BY 
                        i.fecha_registro DESC,
                        ia.fecha_asignacion DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_personal]);
            $incidentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Descifrar nombres de residentes
            foreach ($incidentes as &$incidente) {
                $incidente['residente'] = $this->decrypt($incidente['residente']);
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
                'atendidos' => 0,
                'total' => 0
            ];

            foreach ($resultados as $fila) {
                $estadisticas['total'] += $fila['cantidad'];

                if ($fila['estado'] == 'pendiente') {
                    $estadisticas['pendientes'] = $fila['cantidad'];
                } elseif ($fila['estado'] == 'resuelto') {
                    $estadisticas['atendidos'] = $fila['cantidad'];
                } elseif ($fila['estado'] == 'en_proceso') {
                    $estadisticas['pendientes'] += $fila['cantidad'];
                }
            }

            return $estadisticas;
        } catch (Exception $e) {
            error_log("Error en obtenerEstadisticasIncidentesPersonal: " . $e->getMessage());
            return ['pendientes' => 0, 'atendidos' => 0, 'total' => 0];
        }
    }

    public function obtenerReservasConfirmadas() {
        try {
            $sql = "SELECT 
                        r.id_reserva,
                        a.nombre as nombre_area,
                        CONCAT(p.nombre, ' ', p.apellido_paterno) as nombre_residente,
                        r.fecha_reserva,
                        r.hora_inicio,
                        r.hora_fin,
                        r.estado
                    FROM reserva_area_comun r
                    INNER JOIN area_comun a ON r.id_area = a.id_area
                    INNER JOIN persona p ON r.id_persona = p.id_persona
                    WHERE r.estado = 'confirmada'
                    AND r.fecha_reserva >= CURDATE()
                    ORDER BY r.fecha_reserva, r.hora_inicio
                    LIMIT 10";

            $reservas = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

            // Descifrar nombres de residentes
            foreach ($reservas as &$reserva) {
                $reserva['nombre_residente'] = $this->decrypt($reserva['nombre_residente']);
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
                        p.estado
                    FROM persona p
                    INNER JOIN tiene_departamento td ON p.id_persona = td.id_persona
                    INNER JOIN departamento d ON td.id_departamento = d.id_departamento
                    INNER JOIN rol r ON p.id_rol = r.id_rol
                    WHERE p.estado = 'activo'
                    AND td.estado = 'activo'
                    AND r.rol = 'Residente'
                    ORDER BY p.nombre, p.apellido_paterno
                    LIMIT 20";

            $residentes = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

            // Descifrar datos sensibles
            foreach ($residentes as &$residente) {
                $residente['nombre'] = $this->decrypt($residente['nombre']);
                $residente['apellido_paterno'] = $this->decrypt($residente['apellido_paterno']);
                $residente['apellido_materno'] = $this->decrypt($residente['apellido_materno']);
                $residente['ci'] = $this->decrypt($residente['ci']);
            }

            return $residentes;
        } catch (Exception $e) {
            error_log("Error en obtenerResidentesActivos: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerConsumoServiciosMensual() {
        try {
            $sql = "SELECT 
                        s.nombre as servicio,
                        AVG(hc.consumo_total) as consumo,
                        s.unidad_medida,
                        MAX(hc.consumo_total) as consumo_max,
                        AVG(hc.consumo_total) as consumo_promedio
                    FROM historial_consumo hc
                    INNER JOIN medidor m ON hc.id_medidor = m.id_medidor
                    INNER JOIN servicio s ON m.id_servicio = s.id_servicio
                    WHERE YEAR(hc.fecha_inicio) = YEAR(CURDATE())
                    AND MONTH(hc.fecha_inicio) = MONTH(CURDATE())
                    AND s.nombre IN ('agua', 'luz', 'gas')
                    GROUP BY s.id_servicio
                    ORDER BY s.nombre";

            return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerConsumoServiciosMensual: " . $e->getMessage());
            return [];
        }
    }

    // Método adicional para obtener información del personal logueado
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
                // Descifrar datos
                $personal['nombre'] = $this->decrypt($personal['nombre']);
                $personal['apellido_paterno'] = $this->decrypt($personal['apellido_paterno']);
                $personal['apellido_materno'] = $this->decrypt($personal['apellido_materno']);
            }

            return $personal;
        } catch (Exception $e) {
            error_log("Error en obtenerInfoPersonal: " . $e->getMessage());
            return null;
        }
    }
}
?>
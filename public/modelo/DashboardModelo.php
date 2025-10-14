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
}
?>
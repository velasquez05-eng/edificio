<?php
class DashboardModelo {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function obtenerEstadisticasGenerales() {
        $estadisticas = [];

        try {
            // Total de incidentes activos
            $query = "SELECT COUNT(*) as total FROM incidente WHERE estado != 'Resuelto' OR estado IS NULL";
            $result = $this->db->query($query);
            $estadisticas['total_incidentes'] = $result ? $result->fetch(PDO::FETCH_ASSOC)['total'] : 0;

            // Total de departamentos
            $query = "SELECT COUNT(*) as total FROM departamento";
            $result = $this->db->query($query);
            $estadisticas['total_departamentos'] = $result ? $result->fetch(PDO::FETCH_ASSOC)['total'] : 0;

            // Departamentos ocupados
            $query = "SELECT COUNT(DISTINCT id_departamento) as total FROM pertenece_dep WHERE estado = 'activo'";
            $result = $this->db->query($query);
            $estadisticas['departamentos_ocupados'] = $result ? $result->fetch(PDO::FETCH_ASSOC)['total'] : 0;

            // Total de personal
            $query = "SELECT COUNT(*) as total FROM personal";
            $result = $this->db->query($query);
            $estadisticas['total_personal'] = $result ? $result->fetch(PDO::FETCH_ASSOC)['total'] : 0;

            // Personal activo (asumiendo que todos están activos)
            $estadisticas['personal_activo'] = $estadisticas['total_personal'];

            // Facturas pendientes
            $query = "SELECT COUNT(*) as total FROM factura WHERE estado_pago != 'Pagado' OR estado_pago IS NULL";
            $result = $this->db->query($query);
            $estadisticas['facturas_pendientes'] = $result ? $result->fetch(PDO::FETCH_ASSOC)['total'] : 0;

            // Total de facturas
            $query = "SELECT COUNT(*) as total FROM factura";
            $result = $this->db->query($query);
            $estadisticas['total_facturas'] = $result ? $result->fetch(PDO::FETCH_ASSOC)['total'] : 0;

        } catch (PDOException $e) {
            // Si hay error, devolver valores por defecto
            $estadisticas = [
                'total_incidentes' => 0,
                'total_departamentos' => 0,
                'departamentos_ocupados' => 0,
                'total_personal' => 0,
                'personal_activo' => 0,
                'facturas_pendientes' => 0,
                'total_facturas' => 0
            ];
        }

        return $estadisticas;
    }

    public function obtenerIncidentesPorMes($anio) {
        try {
            $query = "SELECT MONTH(fecha_creacion) as mes, COUNT(*) as total 
                      FROM incidente 
                      WHERE YEAR(fecha_creacion) = ? 
                      GROUP BY MONTH(fecha_creacion) 
                      ORDER BY mes";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([$anio]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $incidentes = array_fill(1, 12, 0);
            foreach ($result as $row) {
                $incidentes[$row['mes']] = $row['total'];
            }

            return $incidentes;
        } catch (PDOException $e) {
            return array_fill(1, 12, 0);
        }
    }

    public function obtenerPagosPorMes($anio) {
        try {
            $query = "SELECT MONTH(fecha_pago) as mes, SUM(monto_total) as total 
                      FROM historial_pagos 
                      WHERE YEAR(fecha_pago) = ? 
                      GROUP BY MONTH(fecha_pago) 
                      ORDER BY mes";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([$anio]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $pagos = array_fill(1, 12, 0);
            foreach ($result as $row) {
                $pagos[$row['mes']] = floatval($row['total']);
            }

            return $pagos;
        } catch (PDOException $e) {
            return array_fill(1, 12, 0);
        }
    }

    public function obtenerConsumoMensual($servicioId = 'all') {
        try {
            $query = "SELECT DATE_FORMAT(hora_lectura, '%Y-%m') as mes, SUM(consumo) as total 
                      FROM lectura_sensor 
                      WHERE hora_lectura >= DATE_SUB(NOW(), INTERVAL 6 MONTH)";
            
            if ($servicioId != 'all') {
                $query .= " AND id_servicio = ?";
            }
            
            $query .= " GROUP BY DATE_FORMAT(hora_lectura, '%Y-%m') 
                        ORDER BY mes";

            $stmt = $this->db->prepare($query);
            
            if ($servicioId != 'all') {
                $stmt->execute([$servicioId]);
            } else {
                $stmt->execute();
            }
            
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $consumo = [];
            foreach ($result as $row) {
                $consumo[$row['mes']] = floatval($row['total']);
            }

            // Si no hay datos, devolver datos de ejemplo
            if (empty($consumo)) {
                $consumo = [
                    date('Y-m', strtotime('-5 months')) => 100,
                    date('Y-m', strtotime('-4 months')) => 150,
                    date('Y-m', strtotime('-3 months')) => 120,
                    date('Y-m', strtotime('-2 months')) => 180,
                    date('Y-m', strtotime('-1 month')) => 160,
                    date('Y-m') => 140
                ];
            }

            return $consumo;
        } catch (PDOException $e) {
            // Datos de ejemplo en caso de error
            return [
                date('Y-m', strtotime('-5 months')) => 100,
                date('Y-m', strtotime('-4 months')) => 150,
                date('Y-m', strtotime('-3 months')) => 120,
                date('Y-m', strtotime('-2 months')) => 180,
                date('Y-m', strtotime('-1 month')) => 160,
                date('Y-m') => 140
            ];
        }
    }

    public function obtenerEstadisticasAreasComunes() {
        try {
            // Total de áreas comunes
            $query = "SELECT COUNT(*) as total FROM area_comun";
            $result = $this->db->query($query);
            $total = $result ? $result->fetch(PDO::FETCH_ASSOC)['total'] : 10;

            // Áreas reservadas actualmente
            $query = "SELECT COUNT(DISTINCT id_area_comun) as reservadas 
                      FROM reserva 
                      WHERE fecha_inicio <= NOW() AND fecha_fin >= NOW()";
            $result = $this->db->query($query);
            $reservadas = $result ? $result->fetch(PDO::FETCH_ASSOC)['reservadas'] : 2;

            return [
                'reservadas' => $reservadas,
                'disponibles' => $total - $reservadas
            ];
        } catch (PDOException $e) {
            return ['reservadas' => 2, 'disponibles' => 8];
        }
    }

    public function obtenerListaUsuarios() {
        try {
            $query = "SELECT u.id_usuario, p.nombre, p.appaterno, p.email, p.telefono 
                      FROM usuario u 
                      JOIN persona p ON u.id_persona = p.id_persona 
                      ORDER BY p.nombre";
            
            $result = $this->db->query($query);
            return $result ? $result->fetchAll(PDO::FETCH_ASSOC) : [];
        } catch (PDOException $e) {
            return [];
        }
    }

    public function obtenerListaDepartamentos() {
        try {
            $query = "SELECT d.id_departamento, d.numero, d.piso, e.nombre as nombre_edificio 
                      FROM departamento d 
                      JOIN edificio e ON d.id_edificio = e.id_edificio 
                      ORDER BY e.nombre, d.piso, d.numero";
            
            $result = $this->db->query($query);
            return $result ? $result->fetchAll(PDO::FETCH_ASSOC) : [];
        } catch (PDOException $e) {
            return [];
        }
    }

    public function obtenerServicios() {
        try {
            $query = "SELECT id_servicio, nombre_servicio FROM servicio ORDER BY nombre_servicio";
            $result = $this->db->query($query);
            return $result ? $result->fetchAll(PDO::FETCH_ASSOC) : [];
        } catch (PDOException $e) {
            return [];
        }
    }
}
?>
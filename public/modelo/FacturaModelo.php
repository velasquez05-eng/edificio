<?php
class FacturaModelo {
    private $db;
    private $table_name = "factura";
    private $encryption_key;

    public function __construct($db, $encryption_key = null) {
        $this->db = $db;
        $this->encryption_key = $encryption_key ?: '1A3F6C9E2B5D8A0C7E4F1A2B3C8D5E6F7A1B2C3D4E5F6A7B8C9D0E1F2A3B4C5D6';

        if (strlen($this->encryption_key) < 32) {
            $this->encryption_key = str_pad($this->encryption_key, 32, "\0");
        } elseif (strlen($this->encryption_key) > 32) {
            $this->encryption_key = substr($this->encryption_key, 0, 32);
        }
    }

    // Descifrar datos
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

    // Obtener todas las facturas
    public function obtenerTodasLasFacturas() {
        try {
            $query = "
                SELECT 
                    f.id_factura,
                    f.id_departamento,
                    d.numero as departamento,
                    d.piso,
                    p.nombre,
                    p.apellido_paterno,
                    p.apellido_materno,
                    p.ci,
                    f.fecha_emision,
                    f.fecha_vencimiento,
                    f.monto_total,
                    f.estado,
                    COUNT(DISTINCT c.id_concepto) as cantidad_conceptos,
                    (SELECT COUNT(*) FROM historial_pago hp WHERE hp.id_factura = f.id_factura) as pagos_realizados
                FROM factura f
                INNER JOIN departamento d ON f.id_departamento = d.id_departamento
                INNER JOIN tiene_departamento td ON d.id_departamento = td.id_departamento AND td.estado = 'activo'
                INNER JOIN persona p ON td.id_persona = p.id_persona AND p.estado = 'activo'
                LEFT JOIN conceptos c ON f.id_factura = c.id_factura
                GROUP BY f.id_factura
                ORDER BY f.fecha_emision DESC, f.id_factura DESC
            ";

            $stmt = $this->db->prepare($query);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $facturas = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($facturas as &$factura) {
                    $factura['nombre'] = $this->decrypt($factura['nombre']);
                    $factura['apellido_paterno'] = $this->decrypt($factura['apellido_paterno']);
                    $factura['apellido_materno'] = $this->decrypt($factura['apellido_materno']);
                    $factura['ci'] = $this->decrypt($factura['ci']);

                    $factura['residente'] = trim(
                        $factura['nombre'] . ' ' .
                        $factura['apellido_paterno'] . ' ' .
                        ($factura['apellido_materno'] ?: '')
                    );
                }

                return $facturas;
            }
            return [];

        } catch (PDOException $e) {
            error_log("Error al obtener facturas: " . $e->getMessage());
            return [];
        }
    }

    // Generar facturas para un mes específico
    public function generarFacturasMes($mes) {
        try {
            $fecha_mes = $mes . '-01';
            $query = "CALL generar_facturas_todos_departamentos(:fecha_mes)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":fecha_mes", $fecha_mes);
            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error al generar facturas: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener información completa de una factura para vista detallada
     * Incluye: factura, conceptos, departamento y residente
     */
    public function obtenerFacturaCompleta($id_factura) {
        try {
            // Información principal de la factura y departamento
            $query_factura = "
            SELECT 
                f.*,
                d.numero as departamento,
                d.piso,
                p.nombre,
                p.apellido_paterno,
                p.apellido_materno,
                p.ci,
                p.email,
                p.telefono
            FROM factura f
            INNER JOIN departamento d ON f.id_departamento = d.id_departamento
            INNER JOIN tiene_departamento td ON d.id_departamento = td.id_departamento AND td.estado = 'activo'
            INNER JOIN persona p ON td.id_persona = p.id_persona AND p.estado = 'activo'
            WHERE f.id_factura = ?
        ";

            $stmt_factura = $this->db->prepare($query_factura);
            $stmt_factura->execute([$id_factura]);
            $factura = $stmt_factura->fetch(PDO::FETCH_ASSOC);

            if (!$factura) {
                return null;
            }

            // Descifrar datos sensibles
            $factura['nombre'] = $this->decrypt($factura['nombre']);
            $factura['apellido_paterno'] = $this->decrypt($factura['apellido_paterno']);
            $factura['apellido_materno'] = $this->decrypt($factura['apellido_materno']);
            $factura['ci'] = $this->decrypt($factura['ci']);

            // Construir nombre completo del residente
            $factura['residente'] = trim(
                $factura['nombre'] . ' ' .
                $factura['apellido_paterno'] . ' ' .
                ($factura['apellido_materno'] ?: '')
            );

            // Conceptos de la factura
            $query_conceptos = "
            SELECT 
                concepto,
                descripcion,
                monto,
                cantidad,
                fecha_creacion
            FROM conceptos 
            WHERE id_factura = ?
            ORDER BY fecha_creacion
        ";

            $stmt_conceptos = $this->db->prepare($query_conceptos);
            $stmt_conceptos->execute([$id_factura]);
            $conceptos = $stmt_conceptos->fetchAll(PDO::FETCH_ASSOC);

            return [
                'factura' => $factura,
                'conceptos' => $conceptos
            ];

        } catch (PDOException $e) {
            error_log("Error al obtener factura completa: " . $e->getMessage());
            return null;
        }
    }


    /**
     * Procesar pago de factura
     */
    public function procesarPago($id_factura, $monto_pagado, $id_persona = null) {
        try {
            // Iniciar transacción
            $this->db->beginTransaction();

            // 1. Verificar que la factura existe y está pendiente/vencida
            $query_verificar = "SELECT estado, id_departamento FROM factura WHERE id_factura = ?";
            $stmt_verificar = $this->db->prepare($query_verificar);
            $stmt_verificar->execute([$id_factura]);
            $factura = $stmt_verificar->fetch(PDO::FETCH_ASSOC);

            if (!$factura) {
                throw new Exception("Factura no encontrada");
            }

            if ($factura['estado'] === 'pagada') {
                throw new Exception("La factura ya está pagada");
            }

            // 2. Si no se proporciona id_persona, obtenerlo del departamento
            if (!$id_persona) {
                $query_persona = "
                SELECT td.id_persona 
                FROM tiene_departamento td 
                WHERE td.id_departamento = ? AND td.estado = 'activo'
                LIMIT 1
            ";
                $stmt_persona = $this->db->prepare($query_persona);
                $stmt_persona->execute([$factura['id_departamento']]);
                $persona = $stmt_persona->fetch(PDO::FETCH_ASSOC);

                if ($persona) {
                    $id_persona = $persona['id_persona'];
                } else {
                    throw new Exception("No se encontró residente activo para este departamento");
                }
            }

            // 3. Registrar el pago en persona_paga_factura (los triggers se encargan del resto)
            $query_pago = "
            INSERT INTO persona_paga_factura (
                id_factura, 
                id_persona, 
                monto_pagado
            ) VALUES (?, ?, ?)
        ";
            $stmt_pago = $this->db->prepare($query_pago);
            $stmt_pago->execute([
                $id_factura,
                $id_persona,
                $monto_pagado
            ]);

            // 4. Confirmar transacción
            $this->db->commit();

            return [
                'success' => true,
                'message' => 'Pago procesado exitosamente',
                'id_factura' => $id_factura,
                'id_persona' => $id_persona,
                'monto_pagado' => $monto_pagado
            ];

        } catch (Exception $e) {
            // Revertir transacción en caso de error
            $this->db->rollBack();
            error_log("Error al procesar pago: " . $e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtener las facturas de una persona específica
     */
    public function obtenerMisFacturas($id_persona) {
        try {
            $query = "
            SELECT 
                f.id_factura,
                f.id_departamento,
                d.numero as departamento,
                d.piso,
                p.nombre,
                p.apellido_paterno,
                p.apellido_materno,
                p.ci,
                f.fecha_emision,
                f.fecha_vencimiento,
                f.monto_total,
                f.estado,
                COUNT(DISTINCT c.id_concepto) as cantidad_conceptos,
                (SELECT COUNT(*) FROM historial_pago hp WHERE hp.id_factura = f.id_factura) as pagos_realizados
            FROM factura f
            INNER JOIN departamento d ON f.id_departamento = d.id_departamento
            INNER JOIN tiene_departamento td ON d.id_departamento = td.id_departamento 
                AND td.estado = 'activo' 
                AND td.id_persona = :id_persona
            INNER JOIN persona p ON td.id_persona = p.id_persona AND p.estado = 'activo'
            LEFT JOIN conceptos c ON f.id_factura = c.id_factura
            WHERE p.id_persona = :id_persona
            GROUP BY f.id_factura
            ORDER BY f.fecha_emision DESC, f.id_factura DESC
        ";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id_persona", $id_persona, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $facturas = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($facturas as &$factura) {
                    $factura['nombre'] = $this->decrypt($factura['nombre']);
                    $factura['apellido_paterno'] = $this->decrypt($factura['apellido_paterno']);
                    $factura['apellido_materno'] = $this->decrypt($factura['apellido_materno']);
                    $factura['ci'] = $this->decrypt($factura['ci']);

                    $factura['residente'] = trim(
                        $factura['nombre'] . ' ' .
                        $factura['apellido_paterno'] . ' ' .
                        ($factura['apellido_materno'] ?: '')
                    );
                }

                return $facturas;
            }
            return [];

        } catch (PDOException $e) {
            error_log("Error al obtener mis facturas: " . $e->getMessage());
            return [];
        }
    }
    /**
     * Obtener el historial de pagos de una persona específica
     */
    public function obtenerMiHistorialPagos($id_persona) {
        try {
            $query = "
            SELECT 
                hp.id_historial_pago,
                hp.id_factura,
                f.id_departamento,
                d.numero as departamento,
                d.piso,
                hp.monto_pagado,
                hp.fecha_pago,
                hp.observacion,
                f.monto_total as monto_factura,
                f.estado as estado_factura,
                f.fecha_emision,
                f.fecha_vencimiento,
                COUNT(DISTINCT c.id_concepto) as cantidad_conceptos
            FROM historial_pago hp
            INNER JOIN factura f ON hp.id_factura = f.id_factura
            INNER JOIN departamento d ON f.id_departamento = d.id_departamento
            INNER JOIN tiene_departamento td ON d.id_departamento = td.id_departamento 
                AND td.estado = 'activo' 
                AND td.id_persona = :id_persona
            LEFT JOIN conceptos c ON f.id_factura = c.id_factura
            WHERE hp.id_persona = :id_persona
            GROUP BY hp.id_historial_pago
            ORDER BY hp.fecha_pago DESC, hp.id_historial_pago DESC
        ";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id_persona", $id_persona, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Procesar cada pago para agregar información adicional
                foreach ($pagos as &$pago) {
                    // Determinar tipo de pago basado en la observación
                    $observacion = strtolower($pago['observacion']);
                    if (strpos($observacion, 'qr') !== false) {
                        $pago['tipo_pago'] = 'qr';
                        $pago['metodo'] = 'QR';
                        $pago['icono'] = 'fa-qrcode';
                    } else {
                        $pago['tipo_pago'] = 'normal';
                        $pago['metodo'] = 'Normal';
                        $pago['icono'] = 'fa-credit-card';
                    }

                    // Determinar si fue puntual
                    $fecha_pago = strtotime($pago['fecha_pago']);
                    $fecha_vencimiento = strtotime($pago['fecha_vencimiento']);
                    $pago['puntual'] = $fecha_pago <= $fecha_vencimiento;

                    // Formatear montos
                    $pago['monto_pagado_formateado'] = number_format($pago['monto_pagado'], 2);
                    $pago['monto_factura_formateado'] = number_format($pago['monto_factura'], 2);

                    // Formatear fechas
                    $pago['fecha_pago_formateada'] = date('d/m/Y', strtotime($pago['fecha_pago']));
                    $pago['fecha_pago_hora'] = date('H:i', strtotime($pago['fecha_pago']));
                    $pago['fecha_emision_formateada'] = date('d/m/Y', strtotime($pago['fecha_emision']));
                    $pago['fecha_vencimiento_formateada'] = date('d/m/Y', strtotime($pago['fecha_vencimiento']));
                }

                return $pagos;
            }
            return [];

        } catch (PDOException $e) {
            error_log("Error al obtener historial de pagos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener estadísticas de pagos para una persona
     */
    public function obtenerEstadisticasMisPagos($id_persona) {
        try {
            $query = "
            SELECT 
                COUNT(*) as total_pagos,
                SUM(hp.monto_pagado) as total_pagado,
                AVG(hp.monto_pagado) as promedio_pago,
                MIN(hp.fecha_pago) as primer_pago,
                MAX(hp.fecha_pago) as ultimo_pago,
                COUNT(CASE WHEN hp.fecha_pago <= f.fecha_vencimiento THEN 1 END) as pagos_puntuales,
                COUNT(CASE WHEN hp.fecha_pago > f.fecha_vencimiento THEN 1 END) as pagos_atrasados,
                COUNT(CASE WHEN LOWER(hp.observacion) LIKE '%qr%' THEN 1 END) as pagos_qr,
                COUNT(CASE WHEN LOWER(hp.observacion) NOT LIKE '%qr%' THEN 1 END) as pagos_normales
            FROM historial_pago hp
            INNER JOIN factura f ON hp.id_factura = f.id_factura
            INNER JOIN tiene_departamento td ON f.id_departamento = td.id_departamento 
                AND td.estado = 'activo' 
                AND td.id_persona = :id_persona
            WHERE hp.id_persona = :id_persona
        ";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id_persona", $id_persona, PDO::PARAM_INT);
            $stmt->execute();

            $estadisticas = $stmt->fetch(PDO::FETCH_ASSOC);

            // Formatear los datos
            if ($estadisticas) {
                $estadisticas['total_pagado_formateado'] = number_format($estadisticas['total_pagado'], 2);
                $estadisticas['promedio_pago_formateado'] = number_format($estadisticas['promedio_pago'], 2);
                $estadisticas['primer_pago_formateado'] = $estadisticas['primer_pago'] ?
                    date('d/m/Y', strtotime($estadisticas['primer_pago'])) : 'N/A';
                $estadisticas['ultimo_pago_formateado'] = $estadisticas['ultimo_pago'] ?
                    date('d/m/Y', strtotime($estadisticas['ultimo_pago'])) : 'N/A';

                // Calcular porcentajes
                $total_pagos = $estadisticas['total_pagos'];
                if ($total_pagos > 0) {
                    $estadisticas['porcentaje_puntual'] = round(($estadisticas['pagos_puntuales'] / $total_pagos) * 100, 1);
                    $estadisticas['porcentaje_atrasado'] = round(($estadisticas['pagos_atrasados'] / $total_pagos) * 100, 1);
                    $estadisticas['porcentaje_qr'] = round(($estadisticas['pagos_qr'] / $total_pagos) * 100, 1);
                    $estadisticas['porcentaje_normal'] = round(($estadisticas['pagos_normales'] / $total_pagos) * 100, 1);
                } else {
                    $estadisticas['porcentaje_puntual'] = 0;
                    $estadisticas['porcentaje_atrasado'] = 0;
                    $estadisticas['porcentaje_qr'] = 0;
                    $estadisticas['porcentaje_normal'] = 0;
                }
            }

            return $estadisticas ?: [
                'total_pagos' => 0,
                'total_pagado' => 0,
                'total_pagado_formateado' => '0.00',
                'pagos_puntuales' => 0,
                'pagos_atrasados' => 0,
                'pagos_qr' => 0,
                'pagos_normales' => 0,
                'porcentaje_puntual' => 0,
                'porcentaje_atrasado' => 0,
                'porcentaje_qr' => 0,
                'porcentaje_normal' => 0
            ];

        } catch (PDOException $e) {
            error_log("Error al obtener estadísticas de pagos: " . $e->getMessage());
            return [
                'total_pagos' => 0,
                'total_pagado' => 0,
                'total_pagado_formateado' => '0.00',
                'pagos_puntuales' => 0,
                'pagos_atrasados' => 0,
                'pagos_qr' => 0,
                'pagos_normales' => 0,
                'porcentaje_puntual' => 0,
                'porcentaje_atrasado' => 0,
                'porcentaje_qr' => 0,
                'porcentaje_normal' => 0
            ];
        }
    }



    /**
     * Obtener el historial completo de pagos con información básica
     */
    public function obtenerHistorialPagosCompleto() {
        try {
            $query = "
            SELECT 
                hp.id_historial_pago,
                hp.id_factura,
                hp.monto_pagado,
                hp.fecha_pago,
                hp.observacion,
                
                -- Información de la factura
                f.id_departamento,
                f.monto_total as monto_factura,
                f.estado as estado_factura,
                f.fecha_emision,
                f.fecha_vencimiento,
                
                -- Información del departamento
                d.numero as departamento,
                d.piso,
                
                -- Información del residente
                p.nombre,
                p.apellido_paterno,
                p.apellido_materno,
                p.ci,
                
                -- Contador de conceptos
                COUNT(DISTINCT c.id_concepto) as cantidad_conceptos
                
            FROM historial_pago hp
            INNER JOIN factura f ON hp.id_factura = f.id_factura
            INNER JOIN departamento d ON f.id_departamento = d.id_departamento
            INNER JOIN tiene_departamento td ON d.id_departamento = td.id_departamento 
                AND td.estado = 'activo'
            INNER JOIN persona p ON td.id_persona = p.id_persona
            LEFT JOIN conceptos c ON f.id_factura = c.id_factura
            GROUP BY hp.id_historial_pago
            ORDER BY hp.fecha_pago DESC, hp.id_historial_pago DESC
        ";

            $stmt = $this->db->prepare($query);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Procesar cada pago para agregar información adicional
                foreach ($pagos as &$pago) {
                    // Descifrar datos sensibles del residente
                    $pago['nombre'] = $this->decrypt($pago['nombre']);
                    $pago['apellido_paterno'] = $this->decrypt($pago['apellido_paterno']);
                    $pago['apellido_materno'] = $this->decrypt($pago['apellido_materno']);
                    $pago['ci'] = $this->decrypt($pago['ci']);

                    // Construir nombre completo del residente
                    $pago['residente'] = trim(
                        $pago['nombre'] . ' ' .
                        $pago['apellido_paterno'] . ' ' .
                        ($pago['apellido_materno'] ?: '')
                    );

                    // Determinar tipo de pago basado en la observación
                    $observacion = strtolower($pago['observacion']);
                    if (strpos($observacion, 'qr') !== false) {
                        $pago['tipo_pago'] = 'qr';
                        $pago['metodo'] = 'QR';
                        $pago['icono'] = 'fa-qrcode';
                    } else {
                        $pago['tipo_pago'] = 'normal';
                        $pago['metodo'] = 'Normal';
                        $pago['icono'] = 'fa-credit-card';
                    }

                    // Determinar si fue puntual
                    $fecha_pago = strtotime($pago['fecha_pago']);
                    $fecha_vencimiento = strtotime($pago['fecha_vencimiento']);
                    $pago['puntual'] = $fecha_pago <= $fecha_vencimiento;

                    // Formatear montos
                    $pago['monto_pagado_formateado'] = number_format($pago['monto_pagado'], 2);
                    $pago['monto_factura_formateado'] = number_format($pago['monto_factura'], 2);

                    // Formatear fechas
                    $pago['fecha_pago_formateada'] = date('d/m/Y', strtotime($pago['fecha_pago']));
                    $pago['fecha_pago_hora'] = date('H:i', strtotime($pago['fecha_pago']));
                    $pago['fecha_emision_formateada'] = date('d/m/Y', strtotime($pago['fecha_emision']));
                    $pago['fecha_vencimiento_formateada'] = date('d/m/Y', strtotime($pago['fecha_vencimiento']));
                }

                return $pagos;
            }
            return [];

        } catch (PDOException $e) {
            error_log("Error al obtener historial completo de pagos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener estadísticas generales de pagos
     */
    public function obtenerEstadisticasPagosCompletas() {
        try {
            $query = "
            SELECT 
                COUNT(*) as total_pagos,
                SUM(hp.monto_pagado) as total_pagado,
                AVG(hp.monto_pagado) as promedio_pago,
                MIN(hp.fecha_pago) as primer_pago,
                MAX(hp.fecha_pago) as ultimo_pago,
                COUNT(CASE WHEN hp.fecha_pago <= f.fecha_vencimiento THEN 1 END) as pagos_puntuales,
                COUNT(CASE WHEN hp.fecha_pago > f.fecha_vencimiento THEN 1 END) as pagos_atrasados,
                COUNT(CASE WHEN LOWER(hp.observacion) LIKE '%qr%' THEN 1 END) as pagos_qr,
                COUNT(CASE WHEN LOWER(hp.observacion) NOT LIKE '%qr%' THEN 1 END) as pagos_normales,
                COUNT(DISTINCT f.id_departamento) as departamentos_con_pagos,
                COUNT(DISTINCT hp.id_persona) as personas_que_pagaron
            FROM historial_pago hp
            INNER JOIN factura f ON hp.id_factura = f.id_factura
        ";

            $stmt = $this->db->prepare($query);
            $stmt->execute();

            $estadisticas = $stmt->fetch(PDO::FETCH_ASSOC);

            // Formatear los datos
            if ($estadisticas) {
                $estadisticas['total_pagado_formateado'] = number_format($estadisticas['total_pagado'], 2);
                $estadisticas['promedio_pago_formateado'] = number_format($estadisticas['promedio_pago'], 2);
                $estadisticas['primer_pago_formateado'] = $estadisticas['primer_pago'] ?
                    date('d/m/Y', strtotime($estadisticas['primer_pago'])) : 'N/A';
                $estadisticas['ultimo_pago_formateado'] = $estadisticas['ultimo_pago'] ?
                    date('d/m/Y', strtotime($estadisticas['ultimo_pago'])) : 'N/A';

                // Calcular porcentajes
                $total_pagos = $estadisticas['total_pagos'];
                if ($total_pagos > 0) {
                    $estadisticas['porcentaje_puntual'] = round(($estadisticas['pagos_puntuales'] / $total_pagos) * 100, 1);
                    $estadisticas['porcentaje_atrasado'] = round(($estadisticas['pagos_atrasados'] / $total_pagos) * 100, 1);
                    $estadisticas['porcentaje_qr'] = round(($estadisticas['pagos_qr'] / $total_pagos) * 100, 1);
                    $estadisticas['porcentaje_normal'] = round(($estadisticas['pagos_normales'] / $total_pagos) * 100, 1);
                } else {
                    $estadisticas['porcentaje_puntual'] = 0;
                    $estadisticas['porcentaje_atrasado'] = 0;
                    $estadisticas['porcentaje_qr'] = 0;
                    $estadisticas['porcentaje_normal'] = 0;
                }
            }

            return $estadisticas ?: [
                'total_pagos' => 0,
                'total_pagado' => 0,
                'total_pagado_formateado' => '0.00',
                'pagos_puntuales' => 0,
                'pagos_atrasados' => 0,
                'pagos_qr' => 0,
                'pagos_normales' => 0,
                'departamentos_con_pagos' => 0,
                'personas_que_pagaron' => 0,
                'porcentaje_puntual' => 0,
                'porcentaje_atrasado' => 0,
                'porcentaje_qr' => 0,
                'porcentaje_normal' => 0
            ];

        } catch (PDOException $e) {
            error_log("Error al obtener estadísticas completas de pagos: " . $e->getMessage());
            return [
                'total_pagos' => 0,
                'total_pagado' => 0,
                'total_pagado_formateado' => '0.00',
                'pagos_puntuales' => 0,
                'pagos_atrasados' => 0,
                'pagos_qr' => 0,
                'pagos_normales' => 0,
                'departamentos_con_pagos' => 0,
                'personas_que_pagaron' => 0,
                'porcentaje_puntual' => 0,
                'porcentaje_atrasado' => 0,
                'porcentaje_qr' => 0,
                'porcentaje_normal' => 0
            ];
        }
    }




    /**
     * Obtener todos los conceptos de una persona específica
     */
    public function obtenerMisConceptos($id_persona) {
        try {
            $query = "
            SELECT 
                c.id_concepto,
                c.id_factura,
                c.concepto,
                c.monto,
                c.id_origen,
                c.tipo_origen,
                c.cantidad,
                c.descripcion,
                c.fecha_creacion,
                c.estado,
                
                -- Información de la factura (si está asignada)
                f.fecha_emision,
                f.fecha_vencimiento,
                f.estado as estado_factura,
                
                -- Información del departamento
                d.numero as departamento,
                d.piso,
                
                -- Información adicional según el tipo de origen
                CASE 
                    WHEN c.tipo_origen = 'reserva' THEN ac.nombre
                    WHEN c.tipo_origen = 'consumo' THEN s.nombre
                    WHEN c.tipo_origen = 'incidente' THEN i.descripcion
                    ELSE NULL
                END as origen_nombre,
                
                CASE 
                    WHEN c.tipo_origen = 'reserva' THEN rac.fecha_reserva
                    WHEN c.tipo_origen = 'consumo' THEN hc.fecha_inicio
                    WHEN c.tipo_origen = 'incidente' THEN i.fecha_registro
                    ELSE NULL
                END as origen_fecha

            FROM conceptos c
            LEFT JOIN factura f ON c.id_factura = f.id_factura
            LEFT JOIN departamento d ON f.id_departamento = d.id_departamento
            LEFT JOIN tiene_departamento td ON d.id_departamento = td.id_departamento 
                AND td.estado = 'activo'
            
            -- Joins para reservas
            LEFT JOIN reserva_area_comun rac ON c.tipo_origen = 'reserva' AND c.id_origen = rac.id_reserva
            LEFT JOIN area_comun ac ON rac.id_area = ac.id_area
            
            -- Joins para consumos
            LEFT JOIN historial_consumo hc ON c.tipo_origen = 'consumo' AND c.id_origen = hc.id_historial_consumo
            LEFT JOIN medidor m ON hc.id_medidor = m.id_medidor
            LEFT JOIN servicio s ON m.id_servicio = s.id_servicio
            
            -- Joins para incidentes
            LEFT JOIN incidente i ON c.tipo_origen = 'incidente' AND c.id_origen = i.id_incidente
            
            WHERE c.id_persona = :id_persona
            ORDER BY c.fecha_creacion DESC, c.id_concepto DESC
        ";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id_persona", $id_persona, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $conceptos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Procesar cada concepto para agregar información adicional
                foreach ($conceptos as &$concepto) {
                    // Formatear montos
                    $concepto['monto_formateado'] = number_format($concepto['monto'], 2);
                    $concepto['subtotal_formateado'] = number_format($concepto['monto'] * $concepto['cantidad'], 2);

                    // Formatear fechas
                    $concepto['fecha_creacion_formateada'] = date('d/m/Y', strtotime($concepto['fecha_creacion']));
                    $concepto['fecha_creacion_hora'] = date('H:i', strtotime($concepto['fecha_creacion']));

                    if ($concepto['fecha_emision']) {
                        $concepto['fecha_emision_formateada'] = date('d/m/Y', strtotime($concepto['fecha_emision']));
                    }

                    if ($concepto['fecha_vencimiento']) {
                        $concepto['fecha_vencimiento_formateada'] = date('d/m/Y', strtotime($concepto['fecha_vencimiento']));
                    }

                    // Determinar icono y color según el concepto
                    switch ($concepto['concepto']) {
                        case 'agua':
                            $concepto['icono'] = 'fa-tint';
                            $concepto['color'] = 'primary';
                            break;
                        case 'luz':
                            $concepto['icono'] = 'fa-bolt';
                            $concepto['color'] = 'warning';
                            break;
                        case 'gas':
                            $concepto['icono'] = 'fa-fire';
                            $concepto['color'] = 'danger';
                            break;
                        case 'mantenimiento':
                            $concepto['icono'] = 'fa-tools';
                            $concepto['color'] = 'secondary';
                            break;
                        case 'reserva_area':
                            $concepto['icono'] = 'fa-calendar-alt';
                            $concepto['color'] = 'info';
                            break;
                        case 'incidente':
                            $concepto['icono'] = 'fa-exclamation-triangle';
                            $concepto['color'] = 'danger';
                            break;
                        case 'multa':
                            $concepto['icono'] = 'fa-gavel';
                            $concepto['color'] = 'dark';
                            break;
                        default:
                            $concepto['icono'] = 'fa-cube';
                            $concepto['color'] = 'success';
                    }

                    // Determinar badge class según el estado
                    switch ($concepto['estado']) {
                        case 'pendiente':
                            $concepto['badge_class'] = 'bg-warning';
                            $concepto['estado_texto'] = 'Pendiente';
                            break;
                        case 'facturado':
                            $concepto['badge_class'] = 'bg-info';
                            $concepto['estado_texto'] = 'Facturado';
                            break;
                        case 'cancelado':
                            $concepto['badge_class'] = 'bg-secondary';
                            $concepto['estado_texto'] = 'Cancelado';
                            break;
                        default:
                            $concepto['badge_class'] = 'bg-secondary';
                            $concepto['estado_texto'] = $concepto['estado'];
                    }

                    // Información del origen
                    if ($concepto['tipo_origen'] === 'reserva' && $concepto['origen_nombre']) {
                        $concepto['origen_info'] = "Reserva: " . $concepto['origen_nombre'];
                    } elseif ($concepto['tipo_origen'] === 'consumo' && $concepto['origen_nombre']) {
                        $concepto['origen_info'] = "Consumo: " . ucfirst($concepto['origen_nombre']);
                    } elseif ($concepto['tipo_origen'] === 'incidente' && $concepto['origen_nombre']) {
                        $concepto['origen_info'] = "Incidente: " . substr($concepto['origen_nombre'], 0, 50) . "...";
                    } else {
                        $concepto['origen_info'] = "Directo";
                    }
                }

                return $conceptos;
            }
            return [];

        } catch (PDOException $e) {
            error_log("Error al obtener conceptos de persona: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener estadísticas de conceptos para una persona
     */
    public function obtenerEstadisticasMisConceptos($id_persona) {
        try {
            $query = "
            SELECT 
                COUNT(*) as total_conceptos,
                SUM(c.monto * c.cantidad) as total_monto,
                AVG(c.monto * c.cantidad) as promedio_monto,
                MIN(c.fecha_creacion) as primer_concepto,
                MAX(c.fecha_creacion) as ultimo_concepto,
                COUNT(CASE WHEN c.estado = 'pendiente' THEN 1 END) as conceptos_pendientes,
                COUNT(CASE WHEN c.estado = 'facturado' THEN 1 END) as conceptos_facturados,
                COUNT(CASE WHEN c.estado = 'cancelado' THEN 1 END) as conceptos_cancelados,
                COUNT(CASE WHEN c.id_factura IS NULL THEN 1 END) as conceptos_sin_factura,
                COUNT(CASE WHEN c.id_factura IS NOT NULL THEN 1 END) as conceptos_con_factura
            FROM conceptos c
            WHERE c.id_persona = :id_persona
        ";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id_persona", $id_persona, PDO::PARAM_INT);
            $stmt->execute();

            $estadisticas = $stmt->fetch(PDO::FETCH_ASSOC);

            // Formatear los datos
            if ($estadisticas) {
                $estadisticas['total_monto_formateado'] = number_format($estadisticas['total_monto'], 2);
                $estadisticas['promedio_monto_formateado'] = number_format($estadisticas['promedio_monto'], 2);
                $estadisticas['primer_concepto_formateado'] = $estadisticas['primer_concepto'] ?
                    date('d/m/Y', strtotime($estadisticas['primer_concepto'])) : 'N/A';
                $estadisticas['ultimo_concepto_formateado'] = $estadisticas['ultimo_concepto'] ?
                    date('d/m/Y', strtotime($estadisticas['ultimo_concepto'])) : 'N/A';

                // Calcular porcentajes
                $total_conceptos = $estadisticas['total_conceptos'];
                if ($total_conceptos > 0) {
                    $estadisticas['porcentaje_pendientes'] = round(($estadisticas['conceptos_pendientes'] / $total_conceptos) * 100, 1);
                    $estadisticas['porcentaje_facturados'] = round(($estadisticas['conceptos_facturados'] / $total_conceptos) * 100, 1);
                    $estadisticas['porcentaje_cancelados'] = round(($estadisticas['conceptos_cancelados'] / $total_conceptos) * 100, 1);
                    $estadisticas['porcentaje_sin_factura'] = round(($estadisticas['conceptos_sin_factura'] / $total_conceptos) * 100, 1);
                    $estadisticas['porcentaje_con_factura'] = round(($estadisticas['conceptos_con_factura'] / $total_conceptos) * 100, 1);
                } else {
                    $estadisticas['porcentaje_pendientes'] = 0;
                    $estadisticas['porcentaje_facturados'] = 0;
                    $estadisticas['porcentaje_cancelados'] = 0;
                    $estadisticas['porcentaje_sin_factura'] = 0;
                    $estadisticas['porcentaje_con_factura'] = 0;
                }
            }

            return $estadisticas ?: [
                'total_conceptos' => 0,
                'total_monto' => 0,
                'total_monto_formateado' => '0.00',
                'conceptos_pendientes' => 0,
                'conceptos_facturados' => 0,
                'conceptos_cancelados' => 0,
                'conceptos_sin_factura' => 0,
                'conceptos_con_factura' => 0,
                'porcentaje_pendientes' => 0,
                'porcentaje_facturados' => 0,
                'porcentaje_cancelados' => 0,
                'porcentaje_sin_factura' => 0,
                'porcentaje_con_factura' => 0
            ];

        } catch (PDOException $e) {
            error_log("Error al obtener estadísticas de conceptos: " . $e->getMessage());
            return [
                'total_conceptos' => 0,
                'total_monto' => 0,
                'total_monto_formateado' => '0.00',
                'conceptos_pendientes' => 0,
                'conceptos_facturados' => 0,
                'conceptos_cancelados' => 0,
                'conceptos_sin_factura' => 0,
                'conceptos_con_factura' => 0,
                'porcentaje_pendientes' => 0,
                'porcentaje_facturados' => 0,
                'porcentaje_cancelados' => 0,
                'porcentaje_sin_factura' => 0,
                'porcentaje_con_factura' => 0
            ];
        }
    }


    /**
     * Obtener todos los conceptos del sistema con información completa
     */
    public function obtenerConceptosCompletos() {
        try {
            $query = "
            SELECT 
                c.id_concepto,
                c.id_factura,
                c.id_persona,
                c.concepto,
                c.monto,
                c.id_origen,
                c.tipo_origen,
                c.cantidad,
                c.descripcion,
                c.fecha_creacion,
                c.estado,
                
                -- Información de la persona
                p.nombre,
                p.apellido_paterno,
                p.apellido_materno,
                p.ci,
                
                -- Información de la factura (si está asignada)
                f.fecha_emision,
                f.fecha_vencimiento,
                f.estado as estado_factura,
                f.monto_total as monto_factura,
                
                -- Información del departamento
                d.numero as departamento,
                d.piso,
                
                -- Información adicional según el tipo de origen
                CASE 
                    WHEN c.tipo_origen = 'reserva' THEN ac.nombre
                    WHEN c.tipo_origen = 'consumo' THEN s.nombre
                    WHEN c.tipo_origen = 'incidente' THEN i.descripcion
                    ELSE NULL
                END as origen_nombre,
                
                CASE 
                    WHEN c.tipo_origen = 'reserva' THEN rac.fecha_reserva
                    WHEN c.tipo_origen = 'consumo' THEN hc.fecha_inicio
                    WHEN c.tipo_origen = 'incidente' THEN i.fecha_registro
                    ELSE NULL
                END as origen_fecha,

                -- Información específica de reservas
                CASE 
                    WHEN c.tipo_origen = 'reserva' THEN CONCAT(rac.fecha_reserva, ' ', rac.hora_inicio, ' - ', rac.hora_fin)
                    ELSE NULL
                END as reserva_horario,

                -- Información específica de consumos
                CASE 
                    WHEN c.tipo_origen = 'consumo' THEN CONCAT('Consumo: ', hc.consumo_total, ' ', s.unidad_medida)
                    ELSE NULL
                END as consumo_info,

                -- Información específica de incidentes
                CASE 
                    WHEN c.tipo_origen = 'incidente' THEN i.tipo
                    ELSE NULL
                END as incidente_tipo

            FROM conceptos c
            INNER JOIN persona p ON c.id_persona = p.id_persona
            LEFT JOIN factura f ON c.id_factura = f.id_factura
            LEFT JOIN departamento d ON f.id_departamento = d.id_departamento
            LEFT JOIN tiene_departamento td ON d.id_departamento = td.id_departamento 
                AND td.estado = 'activo'
            
            -- Joins para reservas
            LEFT JOIN reserva_area_comun rac ON c.tipo_origen = 'reserva' AND c.id_origen = rac.id_reserva
            LEFT JOIN area_comun ac ON rac.id_area = ac.id_area
            
            -- Joins para consumos
            LEFT JOIN historial_consumo hc ON c.tipo_origen = 'consumo' AND c.id_origen = hc.id_historial_consumo
            LEFT JOIN medidor m ON hc.id_medidor = m.id_medidor
            LEFT JOIN servicio s ON m.id_servicio = s.id_servicio
            
            -- Joins para incidentes
            LEFT JOIN incidente i ON c.tipo_origen = 'incidente' AND c.id_origen = i.id_incidente
            
            ORDER BY c.fecha_creacion DESC, c.id_concepto DESC
        ";

            $stmt = $this->db->prepare($query);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $conceptos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Procesar cada concepto para agregar información adicional
                foreach ($conceptos as &$concepto) {
                    // Descifrar datos sensibles de la persona
                    $concepto['nombre'] = $this->decrypt($concepto['nombre']);
                    $concepto['apellido_paterno'] = $this->decrypt($concepto['apellido_paterno']);
                    $concepto['apellido_materno'] = $this->decrypt($concepto['apellido_materno']);
                    $concepto['ci'] = $this->decrypt($concepto['ci']);

                    // Construir nombre completo de la persona
                    $concepto['persona_completa'] = trim(
                        $concepto['nombre'] . ' ' .
                        $concepto['apellido_paterno'] . ' ' .
                        ($concepto['apellido_materno'] ?: '')
                    );

                    // Formatear montos
                    $concepto['monto_formateado'] = number_format($concepto['monto'], 2);
                    $concepto['subtotal_formateado'] = number_format($concepto['monto'] * $concepto['cantidad'], 2);
                    if ($concepto['monto_factura']) {
                        $concepto['monto_factura_formateado'] = number_format($concepto['monto_factura'], 2);
                    }

                    // Formatear fechas
                    $concepto['fecha_creacion_formateada'] = date('d/m/Y', strtotime($concepto['fecha_creacion']));
                    $concepto['fecha_creacion_hora'] = date('H:i', strtotime($concepto['fecha_creacion']));

                    if ($concepto['fecha_emision']) {
                        $concepto['fecha_emision_formateada'] = date('d/m/Y', strtotime($concepto['fecha_emision']));
                    }

                    if ($concepto['fecha_vencimiento']) {
                        $concepto['fecha_vencimiento_formateada'] = date('d/m/Y', strtotime($concepto['fecha_vencimiento']));
                    }

                    if ($concepto['origen_fecha']) {
                        $concepto['origen_fecha_formateada'] = date('d/m/Y', strtotime($concepto['origen_fecha']));
                    }

                    // Determinar icono y color según el concepto
                    switch ($concepto['concepto']) {
                        case 'agua':
                            $concepto['icono'] = 'fa-tint';
                            $concepto['color'] = 'primary';
                            $concepto['concepto_texto'] = 'Agua';
                            break;
                        case 'luz':
                            $concepto['icono'] = 'fa-bolt';
                            $concepto['color'] = 'warning';
                            $concepto['concepto_texto'] = 'Luz';
                            break;
                        case 'gas':
                            $concepto['icono'] = 'fa-fire';
                            $concepto['color'] = 'danger';
                            $concepto['concepto_texto'] = 'Gas';
                            break;
                        case 'mantenimiento':
                            $concepto['icono'] = 'fa-tools';
                            $concepto['color'] = 'secondary';
                            $concepto['concepto_texto'] = 'Mantenimiento';
                            break;
                        case 'reserva_area':
                            $concepto['icono'] = 'fa-calendar-alt';
                            $concepto['color'] = 'info';
                            $concepto['concepto_texto'] = 'Reserva Área';
                            break;
                        case 'incidente':
                            $concepto['icono'] = 'fa-exclamation-triangle';
                            $concepto['color'] = 'danger';
                            $concepto['concepto_texto'] = 'Incidente';
                            break;
                        case 'multa':
                            $concepto['icono'] = 'fa-gavel';
                            $concepto['color'] = 'dark';
                            $concepto['concepto_texto'] = 'Multa';
                            break;
                        default:
                            $concepto['icono'] = 'fa-cube';
                            $concepto['color'] = 'success';
                            $concepto['concepto_texto'] = ucfirst($concepto['concepto']);
                    }

                    // Determinar badge class según el estado
                    switch ($concepto['estado']) {
                        case 'pendiente':
                            $concepto['badge_class'] = 'bg-warning';
                            $concepto['estado_texto'] = 'Pendiente';
                            break;
                        case 'facturado':
                            $concepto['badge_class'] = 'bg-info';
                            $concepto['estado_texto'] = 'Facturado';
                            break;
                        case 'cancelado':
                            $concepto['badge_class'] = 'bg-secondary';
                            $concepto['estado_texto'] = 'Cancelado';
                            break;
                        default:
                            $concepto['badge_class'] = 'bg-secondary';
                            $concepto['estado_texto'] = $concepto['estado'];
                    }

                    // Información detallada del origen
                    if ($concepto['tipo_origen'] === 'reserva' && $concepto['origen_nombre']) {
                        $concepto['origen_info'] = "Reserva: " . $concepto['origen_nombre'];
                        $concepto['origen_detalle'] = $concepto['reserva_horario'];
                    } elseif ($concepto['tipo_origen'] === 'consumo' && $concepto['origen_nombre']) {
                        $concepto['origen_info'] = "Consumo: " . ucfirst($concepto['origen_nombre']);
                        $concepto['origen_detalle'] = $concepto['consumo_info'];
                    } elseif ($concepto['tipo_origen'] === 'incidente' && $concepto['origen_nombre']) {
                        $concepto['origen_info'] = "Incidente: " . ($concepto['incidente_tipo'] === 'externo' ? 'Externo' : 'Interno');
                        $concepto['origen_detalle'] = substr($concepto['origen_nombre'], 0, 80) . "...";
                    } else {
                        $concepto['origen_info'] = "Directo";
                        $concepto['origen_detalle'] = "Sin origen específico";
                    }

                    // Información del departamento
                    if ($concepto['departamento']) {
                        $concepto['departamento_info'] = "D" . $concepto['departamento'] . "-P" . $concepto['piso'];
                    } else {
                        $concepto['departamento_info'] = "Sin departamento";
                    }
                }

                return $conceptos;
            }
            return [];

        } catch (PDOException $e) {
            error_log("Error al obtener conceptos completos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener estadísticas generales de todos los conceptos
     */
    public function obtenerEstadisticasConceptosCompletos() {
        try {
            $query = "
            SELECT 
                COUNT(*) as total_conceptos,
                SUM(c.monto * c.cantidad) as total_monto,
                AVG(c.monto * c.cantidad) as promedio_monto,
                MIN(c.fecha_creacion) as primer_concepto,
                MAX(c.fecha_creacion) as ultimo_concepto,
                COUNT(CASE WHEN c.estado = 'pendiente' THEN 1 END) as conceptos_pendientes,
                COUNT(CASE WHEN c.estado = 'facturado' THEN 1 END) as conceptos_facturados,
                COUNT(CASE WHEN c.estado = 'cancelado' THEN 1 END) as conceptos_cancelados,
                COUNT(CASE WHEN c.id_factura IS NULL THEN 1 END) as conceptos_sin_factura,
                COUNT(CASE WHEN c.id_factura IS NOT NULL THEN 1 END) as conceptos_con_factura,
                COUNT(DISTINCT c.id_persona) as personas_con_conceptos,
                COUNT(DISTINCT f.id_departamento) as departamentos_con_conceptos,
                
                -- Estadísticas por tipo de concepto
                COUNT(CASE WHEN c.concepto = 'agua' THEN 1 END) as conceptos_agua,
                COUNT(CASE WHEN c.concepto = 'luz' THEN 1 END) as conceptos_luz,
                COUNT(CASE WHEN c.concepto = 'gas' THEN 1 END) as conceptos_gas,
                COUNT(CASE WHEN c.concepto = 'mantenimiento' THEN 1 END) as conceptos_mantenimiento,
                COUNT(CASE WHEN c.concepto = 'reserva_area' THEN 1 END) as conceptos_reserva,
                COUNT(CASE WHEN c.concepto = 'incidente' THEN 1 END) as conceptos_incidente,
                COUNT(CASE WHEN c.concepto = 'multa' THEN 1 END) as conceptos_multa,
                COUNT(CASE WHEN c.concepto = 'otros' THEN 1 END) as conceptos_otros,
                
                -- Estadísticas por tipo de origen
                COUNT(CASE WHEN c.tipo_origen = 'reserva' THEN 1 END) as origenes_reserva,
                COUNT(CASE WHEN c.tipo_origen = 'consumo' THEN 1 END) as origenes_consumo,
                COUNT(CASE WHEN c.tipo_origen = 'incidente' THEN 1 END) as origenes_incidente,
                COUNT(CASE WHEN c.tipo_origen = 'otros' THEN 1 END) as origenes_otros,
                COUNT(CASE WHEN c.tipo_origen IS NULL THEN 1 END) as origenes_directos
                
            FROM conceptos c
            LEFT JOIN factura f ON c.id_factura = f.id_factura
        ";

            $stmt = $this->db->prepare($query);
            $stmt->execute();

            $estadisticas = $stmt->fetch(PDO::FETCH_ASSOC);

            // Formatear los datos
            if ($estadisticas) {
                $estadisticas['total_monto_formateado'] = number_format($estadisticas['total_monto'], 2);
                $estadisticas['promedio_monto_formateado'] = number_format($estadisticas['promedio_monto'], 2);
                $estadisticas['primer_concepto_formateado'] = $estadisticas['primer_concepto'] ?
                    date('d/m/Y', strtotime($estadisticas['primer_concepto'])) : 'N/A';
                $estadisticas['ultimo_concepto_formateado'] = $estadisticas['ultimo_concepto'] ?
                    date('d/m/Y', strtotime($estadisticas['ultimo_concepto'])) : 'N/A';

                // Calcular porcentajes
                $total_conceptos = $estadisticas['total_conceptos'];
                if ($total_conceptos > 0) {
                    $estadisticas['porcentaje_pendientes'] = round(($estadisticas['conceptos_pendientes'] / $total_conceptos) * 100, 1);
                    $estadisticas['porcentaje_facturados'] = round(($estadisticas['conceptos_facturados'] / $total_conceptos) * 100, 1);
                    $estadisticas['porcentaje_cancelados'] = round(($estadisticas['conceptos_cancelados'] / $total_conceptos) * 100, 1);
                    $estadisticas['porcentaje_sin_factura'] = round(($estadisticas['conceptos_sin_factura'] / $total_conceptos) * 100, 1);
                    $estadisticas['porcentaje_con_factura'] = round(($estadisticas['conceptos_con_factura'] / $total_conceptos) * 100, 1);

                    // Porcentajes por tipo de concepto
                    $estadisticas['porcentaje_agua'] = round(($estadisticas['conceptos_agua'] / $total_conceptos) * 100, 1);
                    $estadisticas['porcentaje_luz'] = round(($estadisticas['conceptos_luz'] / $total_conceptos) * 100, 1);
                    $estadisticas['porcentaje_gas'] = round(($estadisticas['conceptos_gas'] / $total_conceptos) * 100, 1);
                    $estadisticas['porcentaje_mantenimiento'] = round(($estadisticas['conceptos_mantenimiento'] / $total_conceptos) * 100, 1);
                    $estadisticas['porcentaje_reserva'] = round(($estadisticas['conceptos_reserva'] / $total_conceptos) * 100, 1);
                    $estadisticas['porcentaje_incidente'] = round(($estadisticas['conceptos_incidente'] / $total_conceptos) * 100, 1);
                    $estadisticas['porcentaje_multa'] = round(($estadisticas['conceptos_multa'] / $total_conceptos) * 100, 1);
                    $estadisticas['porcentaje_otros'] = round(($estadisticas['conceptos_otros'] / $total_conceptos) * 100, 1);
                } else {
                    $estadisticas['porcentaje_pendientes'] = 0;
                    $estadisticas['porcentaje_facturados'] = 0;
                    $estadisticas['porcentaje_cancelados'] = 0;
                    $estadisticas['porcentaje_sin_factura'] = 0;
                    $estadisticas['porcentaje_con_factura'] = 0;
                    $estadisticas['porcentaje_agua'] = 0;
                    $estadisticas['porcentaje_luz'] = 0;
                    $estadisticas['porcentaje_gas'] = 0;
                    $estadisticas['porcentaje_mantenimiento'] = 0;
                    $estadisticas['porcentaje_reserva'] = 0;
                    $estadisticas['porcentaje_incidente'] = 0;
                    $estadisticas['porcentaje_multa'] = 0;
                    $estadisticas['porcentaje_otros'] = 0;
                }
            }

            return $estadisticas ?: [
                'total_conceptos' => 0,
                'total_monto' => 0,
                'total_monto_formateado' => '0.00',
                'conceptos_pendientes' => 0,
                'conceptos_facturados' => 0,
                'conceptos_cancelados' => 0,
                'conceptos_sin_factura' => 0,
                'conceptos_con_factura' => 0,
                'personas_con_conceptos' => 0,
                'departamentos_con_conceptos' => 0,
                'porcentaje_pendientes' => 0,
                'porcentaje_facturados' => 0,
                'porcentaje_cancelados' => 0,
                'porcentaje_sin_factura' => 0,
                'porcentaje_con_factura' => 0
            ];

        } catch (PDOException $e) {
            error_log("Error al obtener estadísticas completas de conceptos: " . $e->getMessage());
            return [
                'total_conceptos' => 0,
                'total_monto' => 0,
                'total_monto_formateado' => '0.00',
                'conceptos_pendientes' => 0,
                'conceptos_facturados' => 0,
                'conceptos_cancelados' => 0,
                'conceptos_sin_factura' => 0,
                'conceptos_con_factura' => 0,
                'personas_con_conceptos' => 0,
                'departamentos_con_conceptos' => 0,
                'porcentaje_pendientes' => 0,
                'porcentaje_facturados' => 0,
                'porcentaje_cancelados' => 0,
                'porcentaje_sin_factura' => 0,
                'porcentaje_con_factura' => 0
            ];
        }
    }


}
?>
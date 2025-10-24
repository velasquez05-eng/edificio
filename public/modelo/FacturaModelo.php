<?php

class FacturaModelo
{
    private $db;
    private $table_name = "factura";
    private $encryption_key; // Clave de cifrado

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

    // Metodo para descifrar datos (igual que en PersonaModelo)
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
     * Obtener todas las facturas del sistema
     */
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

                // Descifrar los datos sensibles de las personas
                foreach ($facturas as &$factura) {
                    $factura['nombre'] = $this->decrypt($factura['nombre']);
                    $factura['apellido_paterno'] = $this->decrypt($factura['apellido_paterno']);
                    $factura['apellido_materno'] = $this->decrypt($factura['apellido_materno']);
                    $factura['ci'] = $this->decrypt($factura['ci']);

                    // Construir nombre completo
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

    /**
     * Generar facturas para un mes específico usando procedimiento almacenado
     */
    public function generarFacturasMes($mes) {
        try {
            // Formatear la fecha para el procedimiento (primer día del mes)
            $fecha_mes = $mes . '-01';

            // Llamar al procedimiento almacenado
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
     * Obtener detalle completo de una factura
     */
    public function obtenerDetalleFactura($id_factura) {
        try {
            // Información de la factura
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
                    p.telefono,
                    (SELECT SUM(monto_pagado) FROM historial_pago WHERE id_factura = f.id_factura) as total_pagado
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

            // Descifrar datos sensibles de la persona
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

            // Historial de pagos
            $query_pagos = "
                SELECT 
                    hp.fecha_pago,
                    hp.monto_pagado,
                    hp.observacion,
                    p_pago.nombre as nombre_pago,
                    p_pago.apellido_paterno as apellido_pago
                FROM historial_pago hp
                INNER JOIN persona p_pago ON hp.id_persona = p_pago.id_persona
                WHERE hp.id_factura = ?
                ORDER BY hp.fecha_pago DESC
            ";

            $stmt_pagos = $this->db->prepare($query_pagos);
            $stmt_pagos->execute([$id_factura]);
            $pagos = $stmt_pagos->fetchAll(PDO::FETCH_ASSOC);

            // Descifrar datos de las personas en los pagos
            foreach ($pagos as &$pago) {
                $pago['nombre_pago'] = $this->decrypt($pago['nombre_pago']);
                $pago['apellido_pago'] = $this->decrypt($pago['apellido_pago']);
                $pago['persona_pago'] = trim($pago['nombre_pago'] . ' ' . $pago['apellido_pago']);
            }

            return [
                'factura' => $factura,
                'conceptos' => $conceptos,
                'pagos' => $pagos
            ];

        } catch (PDOException $e) {
            error_log("Error al obtener detalle de factura: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Generar PDF de una factura
     */
    public function generarPDF($id_factura) {
        try {
            $detalle = $this->obtenerDetalleFactura($id_factura);

            if (!$detalle) {
                return false;
            }

            // Aquí iría la lógica para generar el PDF usando TCPDF, FPDF, etc.
            // Por ahora, crearemos un PDF básico como ejemplo
            return $this->crearPDFBasico($detalle);

        } catch (Exception $e) {
            error_log("Error al generar PDF: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Crear PDF básico de la factura (ejemplo)
     */
    private function crearPDFBasico($detalle) {
        $factura = $detalle['factura'];
        $conceptos = $detalle['conceptos'];

        // Configurar headers para PDF
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="factura_' . $factura['id_factura'] . '_' . $factura['departamento'] . '.pdf"');

        // Crear contenido básico del PDF
        $html = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
                    .company { font-size: 24px; font-weight: bold; color: #2c3e50; }
                    .system { font-size: 18px; color: #7f8c8d; }
                    .factura-info { margin: 20px 0; }
                    .table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                    .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    .table th { background-color: #f2f2f2; }
                    .total { font-size: 18px; font-weight: bold; text-align: right; margin-top: 20px; }
                    .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #7f8c8d; }
                </style>
            </head>
            <body>
                <div class='header'>
                    <div class='company'>SEINT</div>
                    <div class='system'>Sistema de Edificio Inteligente</div>
                </div>

                <div class='factura-info'>
                    <h2>FACTURA #" . $factura['id_factura'] . "</h2>
                    <p><strong>Departamento:</strong> " . $factura['departamento'] . " - Piso " . $factura['piso'] . "</p>
                    <p><strong>Residente:</strong> " . $factura['residente'] . "</p>
                    <p><strong>CI:</strong> " . $factura['ci'] . "</p>
                    <p><strong>Email:</strong> " . $factura['email'] . "</p>
                    <p><strong>Teléfono:</strong> " . $factura['telefono'] . "</p>
                    <p><strong>Fecha Emisión:</strong> " . date('d/m/Y', strtotime($factura['fecha_emision'])) . "</p>
                    <p><strong>Fecha Vencimiento:</strong> " . date('d/m/Y', strtotime($factura['fecha_vencimiento'])) . "</p>
                    <p><strong>Estado:</strong> " . ucfirst($factura['estado']) . "</p>
                </div>

                <table class='table'>
                    <thead>
                        <tr>
                            <th>Concepto</th>
                            <th>Descripción</th>
                            <th>Cantidad</th>
                            <th>Monto</th>
                        </tr>
                    </thead>
                    <tbody>";

        foreach ($conceptos as $concepto) {
            $html .= "
                    <tr>
                        <td>" . ucfirst($concepto['concepto']) . "</td>
                        <td>" . $concepto['descripcion'] . "</td>
                        <td>" . $concepto['cantidad'] . "</td>
                        <td>Bs. " . number_format($concepto['monto'], 2) . "</td>
                    </tr>";
        }

        $html .= "
                    </tbody>
                </table>

                <div class='total'>
                    <strong>TOTAL: Bs. " . number_format($factura['monto_total'], 2) . "</strong>
                </div>";

        // Mostrar información de pagos si existe
        if (isset($detalle['pagos']) && !empty($detalle['pagos'])) {
            $html .= "
                <div class='pagos-info' style='margin-top: 30px;'>
                    <h3>Historial de Pagos</h3>
                    <table class='table'>
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Monto Pagado</th>
                                <th>Persona</th>
                                <th>Observación</th>
                            </tr>
                        </thead>
                        <tbody>";

            foreach ($detalle['pagos'] as $pago) {
                $html .= "
                            <tr>
                                <td>" . date('d/m/Y H:i', strtotime($pago['fecha_pago'])) . "</td>
                                <td>Bs. " . number_format($pago['monto_pagado'], 2) . "</td>
                                <td>" . $pago['persona_pago'] . "</td>
                                <td>" . ($pago['observacion'] ?: '-') . "</td>
                            </tr>";
            }

            $html .= "
                        </tbody>
                    </table>
                    <p><strong>Total Pagado: Bs. " . number_format($factura['total_pagado'], 2) . "</strong></p>
                </div>";
        }

        $html .= "
                <div class='footer'>
                    <p>SEINT - Sistema de Edificio Inteligente</p>
                    <p>Fecha de generación: " . date('d/m/Y H:i:s') . "</p>
                </div>
            </body>
            </html>";

        // Para un sistema real, usarías una librería como TCPDF
        // Por ahora, simplemente mostramos el HTML
        echo $html;
        return true;
    }

    /**
     * Obtener estadísticas de facturas
     */
    public function obtenerEstadisticas() {
        try {
            $query = "
                SELECT 
                    COUNT(*) as total_facturas,
                    SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN estado = 'pagada' THEN 1 ELSE 0 END) as pagadas,
                    SUM(CASE WHEN estado = 'vencida' THEN 1 ELSE 0 END) as vencidas,
                    SUM(monto_total) as total_monto,
                    AVG(monto_total) as promedio_monto
                FROM factura
                WHERE YEAR(fecha_emision) = YEAR(CURDATE())
            ";

            $stmt = $this->db->prepare($query);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error al obtener estadísticas: " . $e->getMessage());
            return [];
        }
    }
}
?>
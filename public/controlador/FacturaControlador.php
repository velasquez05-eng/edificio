<?php
class FacturaControlador {
    private $facturamodelo;

    public function __construct($db) {
        $this->facturamodelo = new FacturaModelo($db);
    }

    // Listar todas las facturas
    public function listarFacturas() {
        $facturas = $this->facturamodelo->obtenerTodasLasFacturas();
        include '../vista/ListarFacturasVista.php';
    }
    /**
     * Mostrar vista detallada de una factura específica
     */
    public function verFactura() {
        $id_factura = $_GET['id_factura'] ?? null;

        if ($id_factura) {
            $facturaCompleta = $this->facturamodelo->obtenerFacturaCompleta($id_factura);

            if ($facturaCompleta) {
                // Incluir la vista de detalle de factura
                include '../vista/VerFacturaVista.php';
            } else {
                $this->redirigirConError("Factura no encontrada");
            }
        } else {
            $this->redirigirConError("ID de factura no válido");
        }
    }

    // Generar facturas para un mes específico
    public function generarFacturas() {
        if ($_POST['action'] == "generarFacturas") {
            if(!isset($_POST['mes_facturacion']) || empty(trim($_POST['mes_facturacion']))) {
                $this->redirigirConError("El campo mes es obligatorio");
            }

            $mes = htmlspecialchars(trim($_POST['mes_facturacion']));

            if (!preg_match('/^\d{4}-\d{2}$/', $mes)) {
                $this->redirigirConError("Formato de mes inválido. Use YYYY-MM");
            }

            try {
                $resultado = $this->facturamodelo->generarFacturasMes($mes);

                if($resultado) {
                    $this->redirigirConExito("Facturas generadas exitosamente para el mes " . $mes);
                } else {
                    $this->redirigirConError("Error al generar las facturas");
                }
            } catch (Exception $e) {
                $this->redirigirConError("Error en base de datos: " . $e->getMessage());
            }
        }
    }

    // Redirecciones
    private function redirigirConExito($mensaje) {
        header('Location: ../controlador/FacturaControlador.php?action=listarFacturas&success=' . urlencode($mensaje));
        exit;
    }

    private function redirigirConError($mensaje) {
        header('Location: ../controlador/FacturaControlador.php?action=listarFacturas&error=' . urlencode($mensaje));
        exit;
    }


// En FacturaControlador.php - método descargarFactura

    public function descargarFactura($id_factura)
    {
        try {
            $facturaCompleta = $this->facturamodelo->obtenerFacturaCompleta($id_factura);
            if (!$facturaCompleta) {
                throw new Exception("Factura no encontrada");
            }

            $factura = $facturaCompleta['factura'];
            $conceptos = $facturaCompleta['conceptos'];

            $total = 0;
            foreach ($conceptos as $concepto) {
                $total += $concepto['monto'] * $concepto['cantidad'];
            }

            require_once '../../includes/tcpdf/tcpdf.php';

            $pdf = new TCPDF('P', 'mm', 'LETTER', true, 'UTF-8', false);
            $pdf->SetCreator('SEINT');
            $pdf->SetAuthor('SEINT');
            $pdf->SetTitle('Factura #' . $factura['id_factura']);
            $pdf->SetMargins(15, 18, 15);
            $pdf->SetAutoPageBreak(true, 15);
            $pdf->AddPage();

            $html = '
        <style>
            body {
                font-family: Helvetica, Arial, sans-serif;
                color: #2c3e50;
                font-size: 9px;
                line-height: 1.4;
            }

            .header {
                border-bottom: 1.5px solid #1a5276;
                padding-bottom: 10px;
                margin-bottom: 8px; /* más espacio visual */
            }

            .company-name {
                font-size: 18px;
                font-weight: bold;
                color: #1a5276;
                letter-spacing: 0.5px;
            }
            .company-subtitle {
                font-size: 8.5px;
                color: #7f8c8d;
                text-transform: uppercase;
                letter-spacing: 0.8px;
            }
            .invoice-title {
                font-size: 20px;
                font-weight: bold;
                color: #1a5276;
                text-align: right;
                letter-spacing: 0.5px;
            }
            .invoice-number {
                font-size: 8.5px;
                color: #7f8c8d;
                text-align: right;
            }

            .client-box {
                background: #f8f9fa;
                border: 1px solid #dee2e6;
                border-radius: 6px;
                padding: 12px;
                margin-bottom: 18px;
            }
            .client-header {
                font-size: 10px;
                font-weight: bold;
                color: #1a5276;
                margin-bottom: 6px;
                text-transform: uppercase;
            }
            .client-name {
                font-size: 11px;
                font-weight: bold;
                color: #2c3e50;
                margin-bottom: 4px;
            }
            .client-info {
                font-size: 8.5px;
                color: #34495e;
            }

            .due-date {
                background: #fdf2e9;
                border-left: 3px solid #f39c12;
                padding: 7px 10px;
                border-radius: 4px;
                font-size: 8.5px;
                font-weight: bold;
                color: #7d6608;
                margin-bottom: 18px;
            }
            .due-date.overdue {
                background: #fdecea;
                border-left-color: #c0392b;
                color: #922b21;
            }

            /* TABLA DE CONCEPTOS */
            .concepts-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 5px;
                font-size: 8px;
            }
            .concepts-table thead {
                background-color: #1a5276;
                color: #ffffff;
            }
            .concepts-table th {
                padding: 7px 4px;
                font-weight: bold;
                text-transform: uppercase;
                border-bottom: 1px solid #154360;
                text-align: center;
                font-size: 8.3px;
                letter-spacing: 0.3px;
                
            }
            .concepts-table th:first-child {
                text-align: left;
                padding-left: 6px;
            }
            .concepts-table td {
                padding: 5px 4px;
                border-bottom: 0.4px solid #e5e8e8;
                vertical-align: top;
                color: #1c2833;
            }
            .concepts-table td:nth-child(2),
            .concepts-table td:nth-child(3),
            .concepts-table td:nth-child(4) {
                text-align: center;
            }

            .concept-name {
                font-weight: bold;
                color: #17202a;
                font-size: 8.5px;
            }
            .concept-desc {
                color: #7f8c8d;
                font-size: 7.2px;
                margin-top: 1px;
                line-height: 1.2;
            }

            .total-box {
                width: 250px;
                margin-left: auto;
                
                padding: 10px;
                border-top: 2px solid #1a5276;
                text-align: right;
            }
            .total-label {
                font-size: 9px;
                font-weight: bold;
                color: #1a5276;
                text-transform: uppercase;
                letter-spacing: 0.4px;
            }
            .total-amount {
                font-size: 11px;
                font-weight: bold;
                color: #145a32;
            }

            .footer {
                text-align: center;
                color: #7f8c8d;
                font-size: 7px;
                margin-top: 25px;
                border-top: 1px solid #ecf0f1;
                padding-top: 8px;
            }
        </style>

        <!-- Encabezado -->
        <div class="header">
            <table width="100%">
                <tr>
                    <td width="60%">
                        <div class="company-name">SEINT</div>
                        <div class="company-subtitle">Sistema de Edificio Inteligente</div>
                    </td>
                    <td width="40%" style="text-align: right;">
                        <div class="invoice-title">FACTURA</div>
                        <div class="invoice-number">
                            N° ' . str_pad($factura['id_factura'], 6, '0', STR_PAD_LEFT) . '<br>
                            ' . date('d/m/Y', strtotime($factura['fecha_emision'])) . '
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Cliente -->
        <div class="client-box">
            <div class="client-header">Datos del Cliente</div>
            <table width="100%">
                <tr>
                    <td width="60%">
                        <div class="client-name">' . htmlspecialchars($factura['residente']) . '</div>
                        <div class="client-info">
                            <strong>Departamento:</strong> Piso ' . $factura['piso'] . ' - N°' . $factura['departamento'] . '<br>
                            <strong>Documento:</strong> ' . htmlspecialchars($factura['ci']) . '
                        </div>
                    </td>
                    <td width="40%">
                        <div class="client-info">
                            <strong>Emitido por:</strong><br>
                            Administración SEINT<br>
                            Sistema de Edificio Inteligente
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Vencimiento -->
        <div class="due-date ' . ($factura['estado'] === 'vencida' ? 'overdue' : '') . '">
            <strong>Fecha de Vencimiento:</strong> ' . date('d/m/Y', strtotime($factura['fecha_vencimiento'])) . '
        </div>

        <!-- Conceptos -->
        <table class="concepts-table">
            <thead>
                <tr>
                    <th width="30%">Concepto</th>
                    <th width="10%">Cant.</th>
                    <th width="35%">Precio</th>
                    <th width="25%">Total</th>
                </tr>
            </thead>
            <tbody>';

            if (!empty($conceptos)) {
                foreach ($conceptos as $concepto) {
                    $conceptoSubtotal = $concepto['monto'] * $concepto['cantidad'];
                    $html .= '
                <tr>
                    <td>
                        <div class="concept-name">' . htmlspecialchars($concepto['concepto']) . '</div>';
                    if (!empty($concepto['descripcion'])) {
                        $html .= '<div class="concept-desc">' . htmlspecialchars($concepto['descripcion']) . '</div> <br>';
                    }
                    $html .= '
                    </td>
                    
                    <td>' . $concepto['cantidad'] . '</td>
                    <td>Bs ' . number_format($concepto['monto'], 2, '.', ',') . '</td>
                    <td><strong>Bs ' . number_format($conceptoSubtotal, 2, '.', ',') . '</strong></td>
                
                </tr>';
                }

            } else {
                $html .= '

                <tr>
                    <td colspan="4" style="text-align: center; padding: 20px; color: #7f8c8d; font-style: italic;">
                        No se encontraron conceptos para esta factura
                    </td>
                </tr>';
            }

            $html .= '
            </tbody>
        </table>

        <!-- Total -->
        <div class="total-box">
            <div class="total-label">' . ($factura['estado'] === 'pagada' ? 'Total Pagado' : 'Total a Pagar') . ':</div>
            <div class="total-amount">Bs ' . number_format($total, 2, '.', ',') . '</div>
        </div>

        <!-- Pie -->
        <div class="footer">
            SEINT - Sistema de Edificio Inteligente<br>
            Factura generada el ' . date('d/m/Y \a \l\a\s H:i') . ' | Documento válido como comprobante
        </div>';

            $pdf->writeHTML($html, true, false, true, false, '');
            $pdf->Output('factura_' . str_pad($factura['id_factura'], 6, '0', STR_PAD_LEFT) . '.pdf', 'D');

        } catch (Exception $e) {
            header('Location: FacturaControlador.php?action=verFactura&id_factura=' . $id_factura . '&error=' . urlencode('Error al generar PDF: ' . $e->getMessage()));
            exit();
        }
    }



    public function pagarFactura() {
        if ($_POST['action'] == "pagarFactura") {
            try {
                $id_factura = $_POST['id_factura'] ?? null;
                $origen = $_POST['origen'] ?? 'pago_tarjeta';

                if (!$id_factura) {
                    throw new Exception("ID de factura no válido");
                }

                // Obtener monto de la factura
                $facturaCompleta = $this->facturamodelo->obtenerFacturaCompleta($id_factura);
                if (!$facturaCompleta) {
                    throw new Exception("Factura no encontrada");
                }

                $monto_total = $facturaCompleta['factura']['monto_total'];

                // Procesar el pago (solo hace el INSERT en persona_paga_factura)
                $resultado = $this->facturamodelo->procesarPago($id_factura, $monto_total);

                if ($resultado['success']) {
                    header('Location: FacturaControlador.php?action=verFactura&id_factura=' . $id_factura . '&origen=' . $origen . '&success=' . urlencode($resultado['message']));
                    exit;
                } else {
                    throw new Exception($resultado['message']);
                }

            } catch (Exception $e) {
                $id_factura = $_POST['id_factura'] ?? '';
                header('Location: FacturaControlador.php?action=verFactura&id_factura=' . $id_factura . '&error=' . urlencode('Error al procesar pago: ' . $e->getMessage()));
                exit;
            }
        }
    }



    public function misFacturas() {
        session_start();
        $id_persona = $_SESSION['id_persona'] ?? null; // Obtener de la sesión
        if ($id_persona) {
            $facturas = $this->facturamodelo->obtenerMisFacturas($id_persona);
            include '../vista/VerMisFacturasVista.php';
        } else {
            $this->redirigirConError("Usuario no autenticado");
        }
    }

    public function miHistorialPagos() {
        session_start();
        $id_persona = $_SESSION['id_persona'] ?? null;
        if ($id_persona) {
            $pagos = $this->facturamodelo->obtenerMiHistorialPagos($id_persona);
            $estadisticas = $this->facturamodelo->obtenerEstadisticasMisPagos($id_persona);
            include '../vista/VerMiHistorialPagosVista.php';
        } else {
            $this->redirigirConError("Usuario no autenticado");
        }
    }


    public function misConceptos() {
        session_start();
        $id_persona = $_SESSION['id_persona'] ?? null;
        if ($id_persona) {
            $conceptos = $this->facturamodelo->obtenerMisConceptos($id_persona);
            $estadisticas = $this->facturamodelo->obtenerEstadisticasMisConceptos($id_persona);
            include '../vista/MisConceptosVista.php';
        } else {
            $this->redirigirConError("Usuario no autenticado");
        }
    }


    public function historialPagosCompleto() {
        $pagos = $this->facturamodelo->obtenerHistorialPagosCompleto();
        $estadisticas = $this->facturamodelo->obtenerEstadisticasPagosCompletas();
        include '../vista/VerHistorialPagosVista.php';
    }

    public function conceptosCompletos() {
        $conceptos = $this->facturamodelo->obtenerConceptosCompletos();
        $estadisticas = $this->facturamodelo->obtenerEstadisticasConceptosCompletos();
        include '../vista/ConceptosCompletosVista.php';
    }

}

// Manejo de rutas GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    include_once "../../config/database.php";
    include_once "../modelo/FacturaModelo.php";

    $database = new Database();
    $db = $database->getConnection();
    $controlador = new FacturaControlador($db);

    if(isset($_GET['action'])) {
        switch($_GET['action']) {
            case 'listarFacturas':
                $controlador->listarFacturas();
                break;
            case 'verFactura':
                $controlador->verFactura();
                break;
            case 'verMisFacturas':
                $controlador->misFacturas();
                break;
            case 'verMiHistorialPagos':
                $controlador->miHistorialPagos();
                break;
                case 'misConceptos':
                    $controlador->misConceptos();
                    break;
           case 'historialPagosCompleto':
                    $controlador->historialPagosCompleto();
                    break;
                    case 'conceptosCompletos':
                        $controlador->conceptosCompletos();
                        break;
            case 'descargarFactura':
                $controlador->descargarFactura($_GET['id_factura']);
                break;
            default:
                header('Location: ../vista/DashboardVista.php?error=Accion no valida');
                exit;
        }
    }
}

// Manejo de rutas POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    require_once '../../config/database.php';
    require_once '../modelo/FacturaModelo.php';

    $database = new Database();
    $db = $database->getConnection();
    $controlador = new FacturaControlador($db);

    switch($_POST['action']) {
        case 'generarFacturas':
            $controlador->generarFacturas();
            break;
        case 'pagarFactura':
            $controlador->pagarFactura();
            break;
        default:
            header('Location: ../vista/DashboardVista.php?error=Accion no valida');
            exit;
    }
}
?>
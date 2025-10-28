<?php
/**
 * FacturaControlador - Controlador para gestionar operaciones de facturas
 * Incluye generacion, listado y exportacion de facturas
 */
class FacturaControlador {
    private $facturamodelo;

    /**
     * Constructor - Inicializa el modelo necesario
     */
    public function __construct($db) {
        $this->facturamodelo = new FacturaModelo($db);
    }

    // =============================================
    // METODOS DE VISUALIZACION
    // =============================================

    /**
     * Listar facturas del sistema
     */
    public function listarFacturas() {
        $facturas = $this->facturamodelo->obtenerTodasLasFacturas();
        include '../vista/ListarFacturasVista.php';
    }

    // =============================================
    // METODOS DE GESTION DE FACTURAS
    // =============================================

    /**
     * Generar facturas para un mes específico
     */
    public function generarFacturas() {
        if ($_POST['action'] == "generarFacturas") {
            $camposRequeridos = ['mes_facturacion'];

            foreach($camposRequeridos as $campo) {
                if(!isset($_POST[$campo]) || empty(trim($_POST[$campo]))) {
                    $this->redirigirConError("El campo mes es obligatorio");
                }
            }

            // Sanitizar datos
            $mes = htmlspecialchars(trim($_POST['mes_facturacion']));

            // Validar formato del mes (YYYY-MM)
            if (!preg_match('/^\d{4}-\d{2}$/', $mes)) {
                $this->redirigirConError("Formato de mes inválido. Use YYYY-MM");
            }

            try {
                // Llamar al procedimiento almacenado
                $resultado = $this->facturamodelo->generarFacturasMes($mes);

                if($resultado) {
                    $this->redirigirConExito("Facturas generadas exitosamente para el mes " . $mes);
                } else {
                    $this->redirigirConError("Error al generar las facturas - No se pudo ejecutar el procedimiento");
                }
            } catch (Exception $e) {
                $this->redirigirConError("Error en base de datos: " . $e->getMessage());
            }
        }
    }

    /**
     * Exportar factura en formato PDF
     */
    public function exportarFacturaPDF() {
        if ($_GET['action'] == "exportarPDF" && isset($_GET['id_factura'])) {
            $id_factura = intval($_GET['id_factura']);

            if ($id_factura > 0) {
                $resultado = $this->facturamodelo->generarPDF($id_factura);

                if ($resultado) {
                    // La descarga del PDF se maneja en el modelo
                    exit;
                } else {
                    $this->redirigirConError("Error al generar el PDF de la factura");
                }
            } else {
                $this->redirigirConError("Factura no válida");
            }
        }
    }

    /**
     * Obtener detalle de factura para AJAX
     */
    public function obtenerDetalleFactura() {
        if ($_POST['action'] == "obtenerDetalleFactura" && isset($_POST['id_factura'])) {
            $id_factura = intval($_POST['id_factura']);

            $detalle = $this->facturamodelo->obtenerDetalleFactura($id_factura);

            header('Content-Type: application/json');
            echo json_encode($detalle);
            exit();
        }
    }

    // =============================================
    // METODOS AUXILIARES DE REDIRECCION
    // =============================================

    /**
     * Redirigir con mensaje de exito
     */
    private function redirigirConExito($mensaje) {
        header('Location: ../controlador/FacturaControlador.php?action=listarFacturas&success=' . urlencode($mensaje));
        exit;
    }

    /**
     * Redirigir con mensaje de error
     */
    private function redirigirConError($mensaje) {
        header('Location: ../controlador/FacturaControlador.php?action=listarFacturas&error=' . urlencode($mensaje));
        exit;
    }


}

// =============================================
// MANEJO DE RUTAS - PETICIONES GET
// =============================================

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
            case 'exportarPDF':
                $controlador->exportarFacturaPDF();
                break;
            default:
                header('Location: ../vista/DashboardVista.php?error=Accion no valida');
                exit;
        }
    }
}

// =============================================
// MANEJO DE RUTAS - PETICIONES POST
// =============================================

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
        case 'obtenerDetalleFactura':
            $controlador->obtenerDetalleFactura();
            break;
        default:
            header('Location: ../vista/DashboardVista.php?error=Accion no valida');
            exit;
    }
}
?>
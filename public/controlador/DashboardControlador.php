<?php
require_once '../modelo/DashboardModelo.php';

class DashboardControlador {
    private $modelo;
    private $db;

    public function __construct($db) {
        $this->db = $db;
        $this->modelo = new DashboardModelo($db);
    }

    public function mostrarDashboard() {
        $anio_actual = date('Y');

        try {
            // Obtener datos para el dashboard
            $estadisticas = $this->modelo->obtenerEstadisticasGenerales();
            $incidentes_por_mes = $this->modelo->obtenerIncidentesPorMes($anio_actual);
            $pagos_por_mes = $this->modelo->obtenerPagosPorMes($anio_actual);
            $consumo_mensual = $this->modelo->obtenerConsumoMensual();
            $estadisticas_areas = $this->modelo->obtenerEstadisticasAreasComunes();
            $usuarios = $this->modelo->obtenerListaUsuarios();
            $departamentos = $this->modelo->obtenerListaDepartamentos();
            $servicios = $this->modelo->obtenerServicios();

            // Incluir la vista
            require_once '../vista/DashboardVista.php';
            
        } catch (Exception $e) {
            echo "Error al cargar el dashboard: " . $e->getMessage();
        }
    }

    public function obtenerDatosAjax() {
        if (isset($_GET['action'])) {
            try {
                switch ($_GET['action']) {
                    case 'obtenerIncidentes':
                        $anio = isset($_GET['anio']) ? intval($_GET['anio']) : date('Y');
                        $incidentes = $this->modelo->obtenerIncidentesPorMes($anio);
                        header('Content-Type: application/json');
                        echo json_encode(array_values($incidentes));
                        break;

                    case 'obtenerPagos':
                        $anio = isset($_GET['anio']) ? intval($_GET['anio']) : date('Y');
                        $pagos = $this->modelo->obtenerPagosPorMes($anio);
                        header('Content-Type: application/json');
                        echo json_encode(array_values($pagos));
                        break;

                    case 'obtenerConsumo':
                        $servicioId = isset($_GET['servicio']) ? $_GET['servicio'] : 'all';
                        $consumo = $this->modelo->obtenerConsumoMensual($servicioId);
                        header('Content-Type: application/json');
                        echo json_encode($consumo);
                        break;
                }
            } catch (Exception $e) {
                header('Content-Type: application/json');
                echo json_encode(['error' => $e->getMessage()]);
            }
        }
        exit;
    }
}

// Obtener conexión a la base de datos
require_once '../../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Ejecutar controlador
if (isset($_GET['action']) && $_GET['action'] == 'ajax') {
    $controlador = new DashboardControlador($db);
    $controlador->obtenerDatosAjax();
} else {
    $controlador = new DashboardControlador($db);
    $controlador->mostrarDashboard();
}
?>
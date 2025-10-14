<?php
class DashboardControlador{
    private $dashboardmodelo;

    public function __construct($db){
        $this->dashboardmodelo = new DashboardModelo($db);
    }

    public function mostrarDashboardAdministrador(){
        // Verificar que el usuario sea administrador
        if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 1) {
            header('Location: ../vista/LoginVista.php?error=Acceso no autorizado');
            exit;
        }

        // Obtener parámetros de filtro
        $mes_filtro = $_GET['mes'] ?? date('m');
        $anio_filtro = $_GET['anio'] ?? date('Y');

        try {
            // Obtener todos los datos para el dashboard de administrador
            $metricasFinancieras = $this->dashboardmodelo->obtenerMetricasFinancieras($mes_filtro, $anio_filtro);
            $departamentosProblema = $this->dashboardmodelo->obtenerDepartamentosProblema();
            $consumosPromedio = $this->dashboardmodelo->obtenerConsumosPromedio($mes_filtro, $anio_filtro);
            $metricasSeguridad = $this->dashboardmodelo->obtenerMetricasSeguridad();
            $incidentesRecientes = $this->dashboardmodelo->obtenerIncidentesRecientes();
            $historialIncidentes = $this->dashboardmodelo->obtenerHistorialIncidentes();
            $promedioPagos = $this->dashboardmodelo->obtenerPromedioPagos();
            $reservasProximas = $this->dashboardmodelo->obtenerReservasProximas();
            $facturasVencidas = $this->dashboardmodelo->obtenerFacturasVencidas();
            $estadisticasGenerales = $this->dashboardmodelo->obtenerEstadisticasGenerales();

            // Incluir la vista del dashboard de administrador
            include '../vista/DashboardPersonalVista.php';

        } catch (Exception $e) {
            error_log("Error en mostrarDashboardAdministrador: " . $e->getMessage());
            header('Location: ../vista/DashboardPersonalVista.php?error=Error al cargar el dashboard');
            exit;
        }
    }

    private function redirigirConError($mensaje) {
        header('Location: ../vista/DashboardPersonalVista.php?error=' . urlencode($mensaje));
        exit;
    }
}

// Manejo de rutas
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    session_start();

    include_once "../../config/database.php";
    include_once "../modelo/DashboardModelo.php";

    $database = new Database();
    $db = $database->getConnection();
    $controlador = new DashboardControlador($db);

    if(isset($_GET['action'])) {
        switch($_GET['action']) {
            case 'mostraDashboardPersonal':
                $controlador->mostrarDashboardAdministrador();
                break;
            default:
                // Por defecto mostrar dashboard de administrador
                $controlador->mostrarDashboardAdministrador();
                break;
        }
    } else {
        // Si no hay acción específica, mostrar dashboard de administrador
        $controlador->mostrarDashboardAdministrador();
    }
}
?>
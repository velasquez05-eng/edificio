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
            include '../vista/DashboardAdministradorVista.php';

        } catch (Exception $e) {
            error_log("Error en mostrarDashboardAdministrador: " . $e->getMessage());
            header('Location: ../vista/DashboardAdministradorVista.php?error=Error al cargar el dashboard');
            exit;
        }
    }

    public function mostrarDashboardPersonal() {
        // Verificar que el usuario sea personal (rol 3 según tu estructura)


        $id_personal = $_SESSION['id_persona'];

        try {
            // Obtener datos para el dashboard del personal
            $comunicados = $this->dashboardmodelo->obtenerComunicadosPersonal();
            $incidentesAsignados = $this->dashboardmodelo->obtenerIncidentesAsignados($id_personal);
            $estadisticasIncidentes = $this->dashboardmodelo->obtenerEstadisticasIncidentesPersonal($id_personal);
            $reservasConfirmadas = $this->dashboardmodelo->obtenerReservasConfirmadas();
            $residentes = $this->dashboardmodelo->obtenerResidentesActivos();
            $consumoServicios = $this->dashboardmodelo->obtenerConsumoServiciosMensual();

            // Incluir la vista del dashboard personal
            include '../vista/DashboardPersonalVista.php';

        } catch (Exception $e) {
            error_log("Error en mostrarDashboardPersonal: " . $e->getMessage());
            header('Location: ../vista/DashboardPersonalVista.php?error=Error al cargar el dashboard');
            exit;
        }
    }

    public function mostrarDashboardResidente() {
        // Verificar que el usuario sea residente (rol 2)
        if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 2) {
            header('Location: ../vista/LoginVista.php?error=Acceso no autorizado');
            exit;
        }

        try {
            // Obtener datos específicos del residente
            $id_usuario = $_SESSION['id_persona'];
            // Aquí llamarías a métodos específicos para residentes
            // $consumoPersonal = $this->dashboardmodelo->obtenerConsumoPersonal($id_usuario);
            // $facturasPendientes = $this->dashboardmodelo->obtenerFacturasPendientes($id_usuario);

            include '../vista/DashboardResidenteVista.php';

        } catch (Exception $e) {
            error_log("Error en mostrarDashboardResidente: " . $e->getMessage());
            header('Location: ../vista/DashboardResidenteVista.php?error=Error al cargar el dashboard');
            exit;
        }
    }
}

// Manejo de rutas
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['id_persona'])) {
    header('Location: ../vista/LoginVista.php?error=Debe iniciar sesión');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    include_once "../../config/database.php";
    include_once "../modelo/DashboardModelo.php";

    $database = new Database();
    $db = $database->getConnection();
    $controlador = new DashboardControlador($db);

    if (isset($_GET['action'])) {
        switch ($_GET['action']) {
            case 'mostrarDashboardAdministrador':
                $controlador->mostrarDashboardAdministrador();
                break;
            case 'mostrarDashboardPersonal':
                $controlador->mostrarDashboardPersonal();
                break;
            case 'mostrarDashboardResidente':
                $controlador->mostrarDashboardResidente();
                break;
            default:
                // Redirigir según el rol del usuario
                if ($_SESSION['id_rol'] == 1) {
                    $controlador->mostrarDashboardAdministrador();
                } elseif ($_SESSION['id_rol'] == 3) {
                    $controlador->mostrarDashboardPersonal();
                } else {
                    $controlador->mostrarDashboardResidente();
                }
                break;
        }
    }
}
?>
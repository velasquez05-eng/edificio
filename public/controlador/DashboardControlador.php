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
        $departamento_filtro = $_GET['departamento'] ?? null;

        try {
            // Obtener todos los datos para el dashboard de administrador
            $estadisticasGenerales = $this->dashboardmodelo->obtenerEstadisticasGenerales();
            $metricasFinancieras = $this->dashboardmodelo->obtenerMetricasFinancieras($mes_filtro, $anio_filtro);
            $consumoMensualGeneral = $this->dashboardmodelo->obtenerConsumoMensualGeneral($mes_filtro, $anio_filtro);
            $estadisticasConsumoGeneral = $this->dashboardmodelo->obtenerEstadisticasConsumoGeneral($mes_filtro, $anio_filtro);
            $todosResidentes = $this->dashboardmodelo->obtenerTodosResidentes();
            $todosIncidentes = $this->dashboardmodelo->obtenerTodosIncidentes();
            $todasAreas = $this->dashboardmodelo->obtenerTodasAreas();
            $todasReservas = $this->dashboardmodelo->obtenerTodasReservas();
            $todosServicios = $this->dashboardmodelo->obtenerTodosServicios();
            $departamentosSelector = $this->dashboardmodelo->obtenerDepartamentosParaSelector();

            // Consumo diario por departamento seleccionado
            $consumoDiarioDepartamento = [];
            if ($departamento_filtro) {
                $consumoDiarioDepartamento = $this->dashboardmodelo->obtenerConsumoDiarioPorDepartamento($departamento_filtro);
            }

            // Incluir la vista del dashboard de administrador
            include '../vista/DashboardAdministradorVista.php';

        } catch (Exception $e) {
            error_log("Error en mostrarDashboardAdministrador: " . $e->getMessage());
            header('Location: ../vista/DashboardAdministradorVista.php?error=Error al cargar el dashboard');
            exit;
        }
    }

    public function mostrarDashboardPersonal() {


        $id_personal = $_SESSION['id_persona'];

        try {
            // Obtener datos específicos para el dashboard del personal
            $comunicados = $this->dashboardmodelo->obtenerComunicadosPersonal();
            $incidentesAsignados = $this->dashboardmodelo->obtenerIncidentesAsignados($id_personal);
            $estadisticasIncidentes = $this->dashboardmodelo->obtenerEstadisticasIncidentesPersonal($id_personal);
            $reservasConfirmadas = $this->dashboardmodelo->obtenerReservasConfirmadas();
            $residentes = $this->dashboardmodelo->obtenerResidentesActivos();
      //      $consumoServicios = $this->dashboardmodelo->obtenerConsumoServiciosMensual();

            // Nuevos datos para el personal
  //          $consumoPorDepartamento = $this->dashboardmodelo->obtenerConsumoServiciosPorDepartamento();
    //        $estadisticasConsumo = $this->dashboardmodelo->obtenerEstadisticasConsumoMensual();
            $infoPersonal = $this->dashboardmodelo->obtenerInfoPersonal($id_personal);
//            $resumenActividades = $this->dashboardmodelo->obtenerResumenActividadesPersonal($id_personal);

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

        $id_residente = $_SESSION['id_persona'];

        try {
            // Obtener todos los datos específicos del residente
            $departamento = $this->dashboardmodelo->obtenerDepartamentoResidente($id_residente);
            $comunicados = $this->dashboardmodelo->obtenerComunicadosResidente();
            $consumoDiario = $this->dashboardmodelo->obtenerConsumoDiarioResidente($departamento['id_departamento'] ?? 0);
            $consumoMensual = $this->dashboardmodelo->obtenerConsumoMensualResidente($departamento['id_departamento'] ?? 0);
            $facturas = $this->dashboardmodelo->obtenerFacturasResidente($id_residente);
            $incidentes = $this->dashboardmodelo->obtenerIncidentesResidente($id_residente);
            $reservas = $this->dashboardmodelo->obtenerReservasResidente($id_residente);
            $estadisticas = $this->dashboardmodelo->obtenerEstadisticasResidente($id_residente);
            $infoResidente = $this->dashboardmodelo->obtenerInfoPersonal($id_residente);

            // Incluir la vista del dashboard residente
            include '../vista/DashboardResidenteVista.php';

        } catch (Exception $e) {
            error_log("Error en mostrarDashboardResidente: " . $e->getMessage());
            header('Location: ../vista/DashboardResidenteVista.php?error=Error al cargar el dashboard');
            exit;
        }
    }

    /**
     * Método para obtener datos específicos via AJAX
     */
    public function obtenerDatosFiltrados() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        if (!isset($_SESSION['id_persona'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            return;
        }

        $tipo = $_POST['tipo'] ?? '';
        $mes = $_POST['mes'] ?? date('m');
        $anio = $_POST['anio'] ?? date('Y');
        $id_persona = $_SESSION['id_persona'];

        try {
            switch ($tipo) {
                case 'consumo_departamento':
                    $datos = $this->dashboardmodelo->obtenerConsumoServiciosPorDepartamento($mes, $anio);
                    break;

                case 'estadisticas_consumo':
                    $datos = $this->dashboardmodelo->obtenerEstadisticasConsumoMensual($mes, $anio);
                    break;

                case 'incidentes_personal':
                    $datos = $this->dashboardmodelo->obtenerIncidentesAsignados($id_persona);
                    break;

                case 'reservas_mes':
                    $datos = $this->dashboardmodelo->obtenerReservasConfirmadas();
                    break;

                default:
                    http_response_code(400);
                    echo json_encode(['error' => 'Tipo de datos no válido']);
                    return;
            }

            echo json_encode([
                'success' => true,
                'datos' => $datos
            ]);

        } catch (Exception $e) {
            error_log("Error en obtenerDatosFiltrados: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Error interno del servidor']);
        }
    }

    /**
     * Método para obtener resumen rápido del dashboard personal
     */
    public function obtenerResumenPersonal() {
        if (!isset($_SESSION['id_persona']) || $_SESSION['id_rol'] != 3) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            return;
        }

        $id_personal = $_SESSION['id_persona'];

        try {
            $resumenActividades = $this->dashboardmodelo->obtenerResumenActividadesPersonal($id_personal);
            $estadisticasIncidentes = $this->dashboardmodelo->obtenerEstadisticasIncidentesPersonal($id_personal);
            $comunicados = $this->dashboardmodelo->obtenerComunicadosPersonal();

            echo json_encode([
                'success' => true,
                'resumen' => [
                    'actividades' => $resumenActividades,
                    'incidentes' => $estadisticasIncidentes,
                    'comunicados' => count($comunicados)
                ]
            ]);

        } catch (Exception $e) {
            error_log("Error en obtenerResumenPersonal: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Error interno del servidor']);
        }
    }
}

// Manejo de rutas y solicitudes
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['id_persona'])) {
    header('Location: ../vista/LoginVista.php?error=Debe iniciar sesión');
    exit;
}

// Configuración de base de datos
include_once "../../config/database.php";
include_once "../modelo/DashboardModelo.php";

$database = new Database();
$db = $database->getConnection();
$controlador = new DashboardControlador($db);

// Manejo de solicitudes
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
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
            case 'obtenerResumenPersonal':
                $controlador->obtenerResumenPersonal();
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
    } else {
        // Redirigir según el rol por defecto
        if ($_SESSION['id_rol'] == 1) {
            $controlador->mostrarDashboardAdministrador();
        } elseif ($_SESSION['id_rol'] == 3) {
            $controlador->mostrarDashboardPersonal();
        } else {
            $controlador->mostrarDashboardResidente();
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'obtenerDatosFiltrados':
                $controlador->obtenerDatosFiltrados();
                break;
            case 'obtenerResumenPersonal':
                $controlador->obtenerResumenPersonal();
                break;
            default:
                http_response_code(400);
                echo json_encode(['error' => 'Acción no válida']);
                break;
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Acción no especificada']);
    }
}
?>
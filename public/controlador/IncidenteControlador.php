<?php
class IncidenteControlador
{
    private $modelo;

    public function __construct($db)
    {
        $this->modelo = new IncidenteModelo($db);
    }

    // Método principal para listar todos los incidentes
    public function listarIncidentes()
    {
        try {
            session_start(); // Agregado para manejo de sesión
            $incidentes = $this->modelo->listarIncidentes();
            $numeroIncidentesPendientes = $this->modelo->contarIncidentesPorEstado('pendiente');
            $numeroIncidentesProceso = $this->modelo->contarIncidentesPorEstado('en_proceso');
            $numeroIncidentesResueltos = $this->modelo->contarIncidentesPorEstado('resuelto');
            $numeroIncidentesReasignacion = $this->modelo->contarIncidentesPorReasignacion();
            $numeroIncidentesCancelados = $this->modelo->contarIncidentesPorEstado('cancelado');
            $numeroTotalIncidentes = $this->modelo->contarTotalIncidentes();
            $personalDisponible = $this->modelo->obtenerPersonalDisponible();

            include_once "../vista/ListarIncidentesVista.php";
        } catch (Exception $e) {
            error_log("Error en listarIncidentes: " . $e->getMessage());
            header('Location: ../vista/DashboardAdministradorVista.php?error=Error+al+cargar+incidentes');
            exit;
        }
    }

    // Ver incidentes pendientes
    public function verIncidentesPendientes()
    {
        try {
            session_start();
            $incidentes = $this->modelo->listarIncidentesPorEstado('pendiente');
            $numeroIncidentesPendientes = $this->modelo->contarIncidentesPorEstado('pendiente');
            $numeroIncidentesProceso = $this->modelo->contarIncidentesPorEstado('en_proceso');
            $numeroIncidentesResueltos = $this->modelo->contarIncidentesPorEstado('resuelto');
            $numeroIncidentesReasignacion = $this->modelo->contarIncidentesPorReasignacion();
            $numeroIncidentesCancelados = $this->modelo->contarIncidentesPorEstado('cancelado');
            $numeroTotalIncidentes = $this->modelo->contarTotalIncidentes();
            $personalDisponible = $this->modelo->obtenerPersonalDisponible();

            include_once "../vista/ListarIncidentesVista.php";
        } catch (Exception $e) {
            error_log("Error en verIncidentesPendientes: " . $e->getMessage());
            header('Location: IncidenteControlador.php?action=listarIncidentes&error=Error+al+cargar+incidentes+pendientes');
            exit;
        }
    }

    // Ver incidentes en proceso
    public function verIncidentesProceso()
    {
        try {
            session_start();
            $incidentes = $this->modelo->listarIncidentesPorEstado('en_proceso');
            $numeroIncidentesPendientes = $this->modelo->contarIncidentesPorEstado('pendiente');
            $numeroIncidentesProceso = $this->modelo->contarIncidentesPorEstado('en_proceso');
            $numeroIncidentesResueltos = $this->modelo->contarIncidentesPorEstado('resuelto');
            $numeroIncidentesReasignacion = $this->modelo->contarIncidentesPorReasignacion();
            $numeroIncidentesCancelados = $this->modelo->contarIncidentesPorEstado('cancelado');
            $numeroTotalIncidentes = $this->modelo->contarTotalIncidentes();
            $personalDisponible = $this->modelo->obtenerPersonalDisponible();

            include_once "../vista/ListarIncidentesVista.php";
        } catch (Exception $e) {
            error_log("Error en verIncidentesProceso: " . $e->getMessage());
            header('Location: IncidenteControlador.php?action=listarIncidentes&error=Error+al+cargar+incidentes+en+proceso');
            exit;
        }
    }

    // Ver incidentes resueltos
    public function verIncidentesResueltos()
    {
        try {
            session_start();
            $incidentes = $this->modelo->listarIncidentesPorEstado('resuelto');
            $numeroIncidentesPendientes = $this->modelo->contarIncidentesPorEstado('pendiente');
            $numeroIncidentesProceso = $this->modelo->contarIncidentesPorEstado('en_proceso');
            $numeroIncidentesResueltos = $this->modelo->contarIncidentesPorEstado('resuelto');
            $numeroIncidentesReasignacion = $this->modelo->contarIncidentesPorReasignacion();
            $numeroIncidentesCancelados = $this->modelo->contarIncidentesPorEstado('cancelado');
            $numeroTotalIncidentes = $this->modelo->contarTotalIncidentes();
            $personalDisponible = $this->modelo->obtenerPersonalDisponible();

            include_once "../vista/ListarIncidentesVista.php";
        } catch (Exception $e) {
            error_log("Error en verIncidentesResueltos: " . $e->getMessage());
            header('Location: IncidenteControlador.php?action=listarIncidentes&error=Error+al+cargar+incidentes+resueltos');
            exit;
        }
    }

    // Ver incidentes por reasignar
    public function verIncidentesReasignacion()
    {
        try {
            session_start();
            $incidentes = $this->modelo->listarIncidentesPorReasignacion();
            $numeroIncidentesPendientes = $this->modelo->contarIncidentesPorEstado('pendiente');
            $numeroIncidentesProceso = $this->modelo->contarIncidentesPorEstado('en_proceso');
            $numeroIncidentesResueltos = $this->modelo->contarIncidentesPorEstado('resuelto');
            $numeroIncidentesReasignacion = $this->modelo->contarIncidentesPorReasignacion();
            $numeroIncidentesCancelados = $this->modelo->contarIncidentesPorEstado('cancelado');
            $numeroTotalIncidentes = $this->modelo->contarTotalIncidentes();
            $personalDisponible = $this->modelo->obtenerPersonalDisponible();

            include_once "../vista/ListarIncidentesVista.php";
        } catch (Exception $e) {
            error_log("Error en verIncidentesReasignacion: " . $e->getMessage());
            header('Location: IncidenteControlador.php?action=listarIncidentes&error=Error+al+cargar+incidentes+por+reasignar');
            exit;
        }
    }

    // Mostrar formulario para nuevo incidente
    public function formularioIncidente()
    {
        try {
            session_start();
            // Cargar datos necesarios para el formulario
            $areas = $this->modelo->obtenerAreas();

            // Si es administrador, cargar todos los residentes
            if ($_SESSION['id_rol'] == 1) {
                $departamentos = $this->modelo->obtenerDepartamentos();
                $residentes = $this->modelo->obtenerResidentes();
            } else {
                // Si es residente, cargar solo su información
                $residentes = [];
                $departamentos = $this->modelo->obtenerDepartamentosPorID($_SESSION['id_persona']);
            }

            include_once "../vista/RegistrarIncidenteVista.php";
        } catch (Exception $e) {
            error_log("Error en formularioIncidente: " . $e->getMessage());
            header('Location: IncidenteControlador.php?action=listarIncidentes&error=Error+al+cargar+formulario');
            exit;
        }
    }

    // Ver historial de incidente específico
    public function verHistorialIncidente()
    {
        try {
            if (!isset($_GET['id_incidente'])) {
                header('Location: IncidenteControlador.php?action=listarIncidentes&error=ID+de+incidente+no+especificado');
                exit;
            }

            $idIncidente = $_GET['id_incidente'];
            $historial = $this->modelo->obtenerHistorialIncidente($idIncidente);
            $incidente = $this->modelo->obtenerIncidentePorId($idIncidente);

            if (!$incidente) {
                header('Location: IncidenteControlador.php?action=listarIncidentes&error=Incidente+no+encontrado');
                exit;
            }

            include_once "../vista/VerHistorialIncidenteVista.php";
        } catch (Exception $e) {
            error_log("Error en verHistorialIncidente: " . $e->getMessage());
            header('Location: IncidenteControlador.php?action=listarIncidentes&error=Error+al+cargar+historial');
            exit;
        }
    }

    // Métodos POST simplificados - confían en los triggers
    public function registrarIncidente() {
        try {

            // Validar datos básicos
            if (empty($_POST['descripcion'])) {
                header('Location: IncidenteControlador.php?action=formularioIncidente&error=Descripción+requerida');
                exit;
            }

            // Si es residente, usar datos de sesión
            if ($_SESSION['id_rol'] == 2) {
                $datos = [
                    'id_departamento' => $_SESSION['id_departamento'] ?? null,
                    'id_residente' => $_SESSION['id_persona'],
                    'tipo' => 'interno', // Por defecto para residentes
                    'descripcion' => $_POST['descripcion'],
                    'descripcion_detallada' => $_POST['descripcion_detallada'] ?? null,
                    'id_area' => null // Residentes no pueden seleccionar área común
                ];
            } else {
                // Si es administrador, usar datos del formulario
                if (empty($_POST['id_departamento']) || empty($_POST['id_residente'])) {
                    header('Location: IncidenteControlador.php?action=formularioIncidente&error=Datos+incompletos');
                    exit;
                }

                $datos = [
                    'id_departamento' => $_POST['id_departamento'],
                    'id_residente' => $_POST['id_residente'],
                    'tipo' => $_POST['tipo'] ?? 'interno',
                    'descripcion' => $_POST['descripcion'],
                    'descripcion_detallada' => $_POST['descripcion_detallada'] ?? null,
                    'id_area' => !empty($_POST['id_area']) ? $_POST['id_area'] : null
                ];
            }

            $id_incidente = $this->modelo->registrarIncidente($datos);

            if ($id_incidente) {
                header('Location: IncidenteControlador.php?action=formularioIncidente&success=Incidente+registrado+exitosamente');
            } else {
                header('Location: IncidenteControlador.php?action=formularioIncidente&error=Error+al+registrar+incidente');
            }
            exit;

        } catch (Exception $e) {
            error_log("Error en registrarIncidente: " . $e->getMessage());
            header('Location: IncidenteControlador.php?action=formularioIncidente&error=Error+al+registrar+incidente');
            exit;
        }
    }

    public function editarIncidente() {
        try {
            session_start();
            if (empty($_POST['id_incidente']) || empty($_POST['descripcion'])) {
                header('Location: IncidenteControlador.php?action=listarIncidentes&error=Datos+incompletos');
                exit;
            }

            $datos = [
                'id_incidente' => $_POST['id_incidente'],
                'descripcion' => $_POST['descripcion'],
                'descripcion_detallada' => $_POST['descripcion_detallada'] ?? null
            ];

            if ($this->modelo->editarIncidente($datos)) {
                header('Location: IncidenteControlador.php?action=listarIncidentes&success=Incidente+actualizado+exitosamente');
            } else {
                header('Location: IncidenteControlador.php?action=listarIncidentes&error=Error+al+actualizar+incidente');
            }
            exit;

        } catch (Exception $e) {
            error_log("Error en editarIncidente: " . $e->getMessage());
            header('Location: IncidenteControlador.php?action=listarIncidentes&error=Error+al+actualizar+incidente');
            exit;
        }
    }

    public function cambiarTipoIncidente() {
        try {
            session_start();
            if (empty($_POST['id_incidente']) || empty($_POST['tipo'])) {
                header('Location: IncidenteControlador.php?action=listarIncidentes&error=Datos+incompletos');
                exit;
            }

            $datos = [
                'id_incidente' => $_POST['id_incidente'],
                'tipo' => $_POST['tipo']
            ];

            if ($this->modelo->cambiarTipoIncidente($datos)) {
                header('Location: IncidenteControlador.php?action=listarIncidentes&success=Tipo+de+incidente+actualizado');
            } else {
                header('Location: IncidenteControlador.php?action=listarIncidentes&error=Error+al+cambiar+tipo');
            }
            exit;

        } catch (Exception $e) {
            error_log("Error en cambiarTipoIncidente: " . $e->getMessage());
            header('Location: IncidenteControlador.php?action=listarIncidentes&error=Error+al+cambiar+tipo');
            exit;
        }
    }

    public function asignarPersonal() {
        try {
            session_start();
            if (empty($_POST['id_incidente']) || empty($_POST['id_personal'])) {
                header('Location: IncidenteControlador.php?action=listarIncidentes&error=Datos+incompletos');
                exit;
            }

            $datos = [
                'id_incidente' => $_POST['id_incidente'],
                'id_personal' => $_POST['id_personal'],
                'observaciones' => $_POST['observaciones'] ?? null
            ];

            if ($this->modelo->asignarPersonal($datos)) {
                header('Location: IncidenteControlador.php?action=listarIncidentes&success=Personal+asignado+exitosamente');
            } else {
                header('Location: IncidenteControlador.php?action=listarIncidentes&error=Error+al+asignar+personal');
            }
            exit;

        } catch (Exception $e) {
            error_log("Error en asignarPersonal: " . $e->getMessage());
            header('Location: IncidenteControlador.php?action=listarIncidentes&error=Error+al+asignar+personal');
            exit;
        }
    }

    public function reasignarPersonal() {
        try {

            // Construir arreglo de datos para el modelo
            $datos = [
                'id_incidente' => $_POST['id_incidente'],
                'id_nuevo_personal' => $_POST['id_nuevo_personal'],
                'observaciones' => $_POST['comentario_reasignacion']
            ];

            // Llamar al modelo
            if ($this->modelo->reasignarPersonal($datos)) {
                header('Location: IncidenteControlador.php?action=listarIncidentes&success=Personal+reasignado+exitosamente');
            } else {
                header('Location: IncidenteControlador.php?action=listarIncidentes&error=Error+al+reasignar+personal');
            }

            exit;

        } catch (Exception $e) {
            error_log("Error en reasignarPersonal: " . $e->getMessage());
            header('Location: IncidenteControlador.php?action=listarIncidentes&error=Error+al+reasignar+personal');
            exit;
        }
    }



    public function resolverIncidente() {
        try {
            session_start();
            if (empty($_POST['id_incidente']) || empty($_POST['observaciones'])) {
                header('Location: IncidenteControlador.php?action=listarIncidentes&error=Datos+incompletos');
                exit;
            }

            $datos = [
                'id_incidente' => $_POST['id_incidente'],
                'observaciones' => $_POST['observaciones'],
                'costo_externo' => $_POST['costo_externo'] ?? 0
            ];

            if ($this->modelo->resolverIncidente($datos)) {
                header('Location: IncidenteControlador.php?action=listarIncidentes&success=Incidente+resuelto+exitosamente');
            } else {
                header('Location: IncidenteControlador.php?action=listarIncidentes&error=Error+al+resolver+incidente');
            }
            exit;

        } catch (Exception $e) {
            error_log("Error en resolverIncidente: " . $e->getMessage());
            header('Location: IncidenteControlador.php?action=listarIncidentes&error=Error+al+resolver+incidente');
            exit;
        }
    }

    public function cancelarIncidente() {
        try {
            session_start();
            if (empty($_POST['id_incidente']) || empty($_POST['motivo'])) {
                header('Location: IncidenteControlador.php?action=listarIncidentes&error=Datos+incompletos');
                exit;
            }

            $datos = [
                'id_persona'=>$_SESSION['id_persona'],
                'id_incidente' => $_POST['id_incidente'],
                'motivo' => $_POST['motivo']
            ];

            if ($this->modelo->cancelarIncidente($datos)) {
                header('Location: IncidenteControlador.php?action=listarIncidentes&success=Incidente+cancelado+exitosamente');
            } else {
                header('Location: IncidenteControlador.php?action=listarIncidentes&error=Error+al+cancelar+incidente');
            }
            exit;

        } catch (Exception $e) {
            error_log("Error en cancelarIncidente: " . $e->getMessage());
            header('Location: IncidenteControlador.php?action=listarIncidentes&error=Error+al+cancelar+incidente');
            exit;
        }
    }

    public function cancelarIncidenteResidente() {
        try {
            session_start();
            if (empty($_POST['id_incidente']) || empty($_POST['motivo'])) {
                header('Location: IncidenteControlador.php?action=listarIncidentes&error=Datos+incompletos');
                exit;
            }

            $datos = [
                'id_persona'=>$_SESSION['id_persona'],
                'id_incidente' => $_POST['id_incidente'],
                'motivo' => $_POST['motivo']
            ];

            if ($this->modelo->cancelarIncidente($datos)) {
                header('Location: IncidenteControlador.php?action=verMisIncidentes&success=Incidente+cancelado+exitosamente');
            } else {
                header('Location: IncidenteControlador.php?action=verMisIncidentes&error=Error+al+cancelar+incidente');
            }
            exit;

        } catch (Exception $e) {
            error_log("Error en cancelarIncidente: " . $e->getMessage());
            header('Location: IncidenteControlador.php?action=listarIncidentes&error=Error+al+cancelar+incidente');
            exit;
        }
    }

    // Ver mis incidentes (para residentes)
    public function verMisIncidentes() {
        try {
            session_start();
            if ($_SESSION['id_rol'] != 2) {
                header('Location: IncidenteControlador.php?action=listarIncidentes&error=Acceso+no+autorizado');
                exit;
            }

            $id_residente = $_SESSION['id_persona'];

            $incidentes = $this->modelo->listarIncidentesPorResidente($id_residente);

            // Obtener estadísticas para el residente
            $numeroIncidentesPendientes = $this->modelo->contarIncidentesResidentePorEstado($id_residente, 'pendiente');
            $numeroIncidentesProceso = $this->modelo->contarIncidentesResidentePorEstado($id_residente, 'en_proceso');
            $numeroIncidentesResueltos = $this->modelo->contarIncidentesResidentePorEstado($id_residente, 'resuelto');
            $numeroIncidentesCancelados = $this->modelo->contarIncidentesResidentePorEstado($id_residente, 'cancelado');
            $numeroTotalIncidentes = $this->modelo->contarTotalIncidentesResidente($id_residente);

            include_once "../vista/VerMisIncidentesVista.php";
        } catch (Exception $e) {
            error_log("Error en verMisIncidentes: " . $e->getMessage());
            header('Location: ../vista/DashboardVista.php?error=Error+al+cargar+mis+incidentes');
            exit;
        }
    }

    // Obtener residentes por departamento (AJAX)
    public function obtenerResidentesPorDepartamento() {
        try {
            if (!isset($_GET['id_departamento']) || empty($_GET['id_departamento'])) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'ID de departamento no especificado']);
                exit;
            }

            $id_departamento = $_GET['id_departamento'];
            $residentes = $this->modelo->obtenerResidentesPorDepartamento($id_departamento);

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'residentes' => $residentes
            ]);
            exit;

        } catch (Exception $e) {
            error_log("Error en obtenerResidentesPorDepartamento: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Error al cargar residentes']);
            exit;
        }
    }

    // Métodos optimizados para personal
    public function verIncidentesAsignados() {
        try {
            session_start();


            $id_personal = $_SESSION['id_persona'];

            $incidentesAsignados = $this->modelo->listarIncidentesAsignados($id_personal);
            $incidentesAtendidos = $this->modelo->listarIncidentesAtendidos($id_personal);

            // Obtener estadísticas
            $numeroIncidentesAsignados = $this->modelo->contarIncidentesAsignados($id_personal);
            $numeroIncidentesAtendidos = $this->modelo->contarIncidentesAtendidos($id_personal);

            include_once "../vista/VerIncidentesAsignadosVista.php";
        } catch (Exception $e) {
            error_log("Error en verIncidentesAsignados: " . $e->getMessage());
            header('Location: ../vista/DashboardVista.php?error=Error+al+cargar+incidentes+asignados');
            exit;
        }
    }

    /**
     * Iniciar atención de incidente (PRIMERA VEZ)
     */
    public function iniciarAtencion() {
        try {
            session_start();


            if (empty($_POST['id_incidente']) || empty($_POST['observaciones'])) {
                header('Location: IncidenteControlador.php?action=verIncidentesAsignados&error=Datos+incompletos');
                exit;
            }

            $datos = [
                'id_incidente' => $_POST['id_incidente'],
                'id_personal' => $_SESSION['id_persona'],
                'observaciones' => $_POST['observaciones']
            ];

            if ($this->modelo->iniciarAtencionIncidente($datos)) {
                header('Location: IncidenteControlador.php?action=verIncidentesAsignados&success=Atención+iniciada+exitosamente');
            } else {
                header('Location: IncidenteControlador.php?action=verIncidentesAsignados&error=Error+al+iniciar+atención');
            }
            exit;

        } catch (Exception $e) {
            error_log("Error en iniciarAtencion: " . $e->getMessage());
            header('Location: IncidenteControlador.php?action=verIncidentesAsignados&error=Error+al+iniciar+atención');
            exit;
        }
    }

    /**
     * Actualizar progreso del incidente (ACTUALIZACIONES)
     */
    public function actualizarProgresoPersonal() {
        try {
            session_start();


            if (empty($_POST['id_incidente']) || empty($_POST['observaciones'])) {
                header('Location: IncidenteControlador.php?action=verIncidentesAsignados&error=Datos+incompletos');
                exit;
            }

            $datos = [
                'id_incidente' => $_POST['id_incidente'],
                'id_personal' => $_SESSION['id_persona'],
                'observaciones' => $_POST['observaciones']
            ];

            if ($this->modelo->actualizarProgresoIncidente($datos)) {
                header('Location: IncidenteControlador.php?action=verIncidentesAsignados&success=Progreso+actualizado+exitosamente');
            } else {
                header('Location: IncidenteControlador.php?action=verIncidentesAsignados&error=Error+al+actualizar+progreso');
            }
            exit;

        } catch (Exception $e) {
            error_log("Error en actualizarProgreso: " . $e->getMessage());
            header('Location: IncidenteControlador.php?action=verIncidentesAsignados&error=Error+al+actualizar+progreso');
            exit;
        }
    }

    /**
     * Resolver incidente (FINALIZAR)
     */
    public function resolverIncidentePersonal() {
        try {
            session_start();

            if (empty($_POST['id_incidente']) || empty($_POST['observaciones_finales'])) {
                header('Location: IncidenteControlador.php?action=verIncidentesAsignados&error=Datos+incompletos');
                exit;
            }

            $datos = [
                'id_incidente' => $_POST['id_incidente'],
                'id_personal' => $_SESSION['id_persona'],
                'observaciones_finales' => $_POST['observaciones_finales'],
                'costo_externo' => $_POST['costo_externo'] ?? 0
            ];

            if ($this->modelo->resolverIncidentePersonal($datos)) {
                header('Location: IncidenteControlador.php?action=verIncidentesAsignados&success=Incidente+resuelto+exitosamente');
            } else {
                header('Location: IncidenteControlador.php?action=verIncidentesAsignados&error=Error+al+resolver+incidente');
            }
            exit;

        } catch (Exception $e) {
            error_log("Error en resolverIncidentePersonal: " . $e->getMessage());
            header('Location: IncidenteControlador.php?action=verIncidentesAsignados&error=Error+al+resolver+incidente');
            exit;
        }
    }

    public function solicitarReasignacion() {
        // Verificar si es una solicitud POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                session_start();
                // Obtener datos del formulario
                $id_incidente = $_POST['id_incidente'] ?? null;
                $observaciones = $_POST['observaciones'] ?? '';
                $comentario_reasignacion = $_POST['comentario_reasignacion'] ?? '';



                // Obtener ID del personal actual desde la sesión
                $id_personal = $_SESSION['id_persona'] ?? null;
                if (empty($id_personal)) {
                    throw new Exception('No se pudo identificar al personal');
                }



                // Solicitar reasignación
                $resultado = $this->modelo->solicitarReasignacion(
                    $id_incidente,
                    $id_personal,
                    $observaciones,
                    $comentario_reasignacion
                );

                if ($resultado) {
                    // Redirigir con mensaje de éxito
                    header('Location: IncidenteControlador.php?action=verIncidentesAsignados&success=Solicitud+de+reasignación+enviada+correctamente');
                    exit();
                } else {
                    throw new Exception('Error al procesar la solicitud de reasignación');
                }

            } catch (Exception $e) {
                // Redirigir con mensaje de error
                header('Location: IncidenteControlador.php?action=verIncidentesAsignados&error=' . urlencode($e->getMessage()));
                exit();
            }
        } else {
            // Si no es POST, redirigir
            header('Location: IncidenteControlador.php?action=verIncidentesAsignados');
            exit();
        }
    }

}

// Manejo de rutas GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    require_once '../../config/database.php';
    require_once '../modelo/IncidenteModelo.php';

    $database = new Database();
    $db = $database->getConnection();
    $controlador = new IncidenteControlador($db);

    if (isset($_GET['action'])) {
        switch ($_GET['action']) {
            case 'listarIncidentes':
                $controlador->listarIncidentes();
                break;
            case 'verIncidentesPendientes':
                $controlador->verIncidentesPendientes();
                break;
            case 'verIncidentesProceso':
                $controlador->verIncidentesProceso();
                break;
            case 'verIncidentesResueltos':
                $controlador->verIncidentesResueltos();
                break;
            case 'verIncidentesReasignacion':
                $controlador->verIncidentesReasignacion();
                break;
            case 'formularioIncidente':
                $controlador->formularioIncidente();
                break;
            case 'verHistorialIncidente':
                $controlador->verHistorialIncidente();
                break;
            case 'verMisIncidentes':
                $controlador->verMisIncidentes();
                break;
            case 'obtenerResidentesPorDepartamento':
                $controlador->obtenerResidentesPorDepartamento();
                break;
            case 'verIncidentesAsignados':
                $controlador->verIncidentesAsignados();
                break;
            default:
                header('Location: ../vista/DashboardAdministradorVista.php?error=Accion+no+valida');
                exit;
        }
    } else {
        // Acción por defecto
        $controlador->listarIncidentes();
    }
}

// Manejo de rutas POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../../config/database.php';
    require_once '../modelo/IncidenteModelo.php';

    $database = new Database();
    $db = $database->getConnection();
    $controlador = new IncidenteControlador($db);

    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'registrarIncidente':
                $controlador->registrarIncidente();
                break;
            case 'editarIncidente':
                $controlador->editarIncidente();
                break;
            case 'cambiarTipoIncidente':
                $controlador->cambiarTipoIncidente();
                break;
            case 'asignarPersonal':
                $controlador->asignarPersonal();
                break;
            case 'reasignarPersonal':
                $controlador->reasignarPersonal();
                break;
            case 'resolverIncidente':
                $controlador->resolverIncidente();
                break;
            case 'cancelarIncidente':
                $controlador->cancelarIncidente();
                break;
            case 'cancelarIncidenteResidente':
                $controlador->cancelarIncidenteResidente();
            case 'iniciarAtencion':
                $controlador->iniciarAtencion();
                break;
            case 'actualizarProgresoPersonal':
                $controlador->actualizarProgresoPersonal();
                break;
            case 'resolverIncidentePersonal':
                $controlador->resolverIncidentePersonal();
                break;
            case 'solicitarReasignacion':
                $controlador->solicitarReasignacion();
                break;
            default:
                header('Location: IncidenteControlador.php?error=Accion+no+valida+post');
                exit;
        }
    } else {
        header('Location: IncidenteControlador.php?error=Accion+no+especificada');
        exit;
    }
}
?>
<?php
class AreaComunControlador
{
    private $areamodelo;
    public function __construct($db){
        $this->areamodelo = new AreaComunModelo($db);
    }

    // Mostrar lista de areas comunes
    public function listarAreas(){
        $areascomunes = $this->areamodelo->listarAreas();
        $numeroReservasPendientes = $this->areamodelo->contarReservasPendientes();
        $numeroReservasMes = $this->areamodelo->contarReservasEsteMes();
        $conteoArea =$this->areamodelo->contarAreasPorEstado();
        $numeroDisponible =$conteoArea['disponible'];
        $numeroMantenimiento =$conteoArea['mantenimiento'];
        $numeroNoDisponible =$conteoArea['no disponible'];
        $numeroTotalArea =$conteoArea['total'];
        include_once "../vista/ListarAreasComunesVista.php";
    }

    // Mostrar formulario para registrar nueva area
    public function formularioArea(){
        include_once "../vista/RegistrarAreaVista.php";
    }
    public function formularioReservaArea(){
        include_once "../vista/RegistrarReservaAreaVista.php";
    }

    //validaciones de fechas
    private function getListaFeriadosBolivia(int $year): array
    {
        $feriados = [];

        // Feriados Fijos (clave: 'MM-DD')
        $fijos = [
            '01-01', // Año Nuevo
            '01-22', // Día del Estado Plurinacional
            '05-01', // Día del Trabajo
            '06-21', // Año Nuevo Aymara
            '08-06', // Día de la Independencia
            '11-02', // Día de Todos los Difuntos
            '12-25', // Navidad
        ];
        foreach ($fijos as $fecha_mm_dd) {
            $feriados[] = $year . '-' . $fecha_mm_dd;
        }

        // Feriados Móviles (dependientes de Pascua)
        $pascuaTimestamp = easter_date($year);
        $pascuaDate = new DateTime('@' . $pascuaTimestamp);
        // Ajuste de la zona horaria para asegurar la fecha correcta de Pascua
        $pascuaDate->setTimezone(new DateTimeZone('America/La_Paz'));

        // Lunes y Martes de Carnaval (48 y 47 días ANTES de Pascua)
        $feriados[] = (clone $pascuaDate)->modify('-48 days')->format('Y-m-d');
        $feriados[] = (clone $pascuaDate)->modify('-47 days')->format('Y-m-d');

        // Viernes Santo (2 días ANTES de Pascua)
        $feriados[] = (clone $pascuaDate)->modify('-2 days')->format('Y-m-d');

        // Corpus Christi (60 días DESPUÉS de Pascua)
        $feriados[] = (clone $pascuaDate)->modify('+60 days')->format('Y-m-d');

        return $feriados;
    }


    private function esDiaFeriado(string $fecha): bool
    {
        try {
            $fechaObj = new DateTime($fecha);
            $year = (int)$fechaObj->format('Y');

            $feriados = $this->getListaFeriadosBolivia($year);
            $fechaFormateada = $fechaObj->format('Y-m-d');

            // Retorna FALSE si la fecha está en la lista de feriados
            if (in_array($fechaFormateada, $feriados)) {
                return false;
            }

            // Retorna TRUE si no es feriado
            return true;

        } catch (Exception $e) {
            return false;
        }
    }

    public function esIntervaloLibreDeFeriados(string $fecha_inicio, string $fecha_fin): bool
    {
        try {
            $inicio = new DateTime($fecha_inicio);
            $fin = new DateTime($fecha_fin);

            // Si la fecha de inicio es posterior a la de fin, devuelve error
            if ($inicio > $fin) {
                // Puedes manejar este error de forma más específica si es necesario
                return false;
            }

            $fin->modify('+1 day'); // Para incluir la fecha de fin en la iteración

            // Crea un período para iterar día a día
            $periodo = new DatePeriod(
                $inicio,
                new DateInterval('P1D'),
                $fin
            );

            foreach ($periodo as $fecha) {
                $fecha_string = $fecha->format('Y-m-d');

                // Si la función devuelve FALSE, significa que el día ES feriado.
                if (!$this->esDiaFeriado($fecha_string)) {
                    return false; // Se encontró un feriado, el intervalo NO es libre de feriados.
                }
            }

            // Si el bucle termina, ningún día fue feriado.
            return true;
        } catch (Exception $e) {
            // Error en el formato de las fechas
            return false;
        }
    }
    // Registrar nueva area comun
    public function registrarArea(){
        try {
            if (empty($_POST['nombre']) || empty($_POST['capacidad'])) {
                header('Location: AreaComunControlador.php?action=formularioArea&error=Nombre+y+capacidad+son+obligatorios');
                exit;
            }

            $nombre = trim($_POST['nombre']);
            $descripcion = trim($_POST['descripcion'] ?? '');
            $capacidad = intval($_POST['capacidad']);
            $estado = $_POST['estado'] ?? 'disponible';

            if ($capacidad < 1) {
                header('Location: AreaComunControlador.php?action=formularioArea&error=La+capacidad+debe+ser+mayor+a+0');
                exit;
            }

            $resultado = $this->areamodelo->registrarArea($nombre, $descripcion, $capacidad, $estado);

            if ($resultado) {
                header('Location: AreaComunControlador.php?action=formularioArea&success=Area+comun+registrada+correctamente');
            } else {
                header('Location: AreaComunControlador.php?action=formularioArea&error=Error+al+registrar+el+area+comun');
            }
            exit;

        } catch (Exception $e) {
            error_log("Error en registrarArea controlador: " . $e->getMessage());
            header('Location: AreaComunControlador.php?action=formularioArea&error=Error+al+registrar+el+area+comun');
            exit;
        }
    }

    // Editar area comun existente
    public function editarArea(){
        try {
            if (empty($_POST['id_area']) || empty($_POST['nombre']) || empty($_POST['capacidad'])) {
                header('Location: AreaComunControlador.php?action=listarAreas&error=Todos+los+campos+son+obligatorios');
                exit;
            }

            $id_area = intval($_POST['id_area']);
            $nombre = trim($_POST['nombre']);
            $descripcion = trim($_POST['descripcion'] ?? '');
            $capacidad = intval($_POST['capacidad']);
            $costo_reserva = floatval($_POST['costo_reserva']);
            $estado = $_POST['estado'] ?? 'disponible';

            if ($capacidad < 1) {
                header('Location: AreaComunControlador.php?action=listarAreas&error=La+capacidad+debe+ser+mayor+a+0');
                exit;
            }

            $resultado = $this->areamodelo->editarArea($id_area, $nombre, $descripcion, $capacidad,$costo_reserva, $estado);

            if ($resultado) {
                header('Location: AreaComunControlador.php?action=listarAreas&success=Area+comun+actualizada+correctamente');
            } else {
                header('Location: AreaComunControlador.php?action=listarAreas&error=Error+al+actualizar+el+area+comun');
            }
            exit;

        } catch (Exception $e) {
            error_log("Error en editarArea controlador: " . $e->getMessage());
            header('Location: AreaComunControlador.php?action=listarAreas&error=Error+al+actualizar+el+area+comun');
            exit;
        }
    }
    // programar area en mantenimiento
    public function programarMantenimiento(){
        if (isset($_POST['action'])=="programarMantenimiento") {
            $camposRequeridos = ['id_area','fecha_inicio','fecha_fin'];
            foreach($camposRequeridos as $campo){
                if (empty($_POST[$campo])||empty(trim($_POST[$campo]))) {
                    header('Location: AreaComunControlador.php?action=listarAreas&error='.urldecode("Error ".$campo." es obligatorio"));
                    exit;
                }
            }

            $id_persona=intval($_POST['id_persona']);
            $id_area = intval($_POST['id_area']);
            $fecha_inicio = trim($_POST['fecha_inicio']);
            $fecha_fin = trim($_POST['fecha_fin']);

            if (!$this->esIntervaloLibreDeFeriados($fecha_inicio, $fecha_fin)) {
                $mensaje = "Error: El intervalo de fechas del {$fecha_inicio} al {$fecha_fin} contiene días feriados";
                header('Location: AreaComunControlador.php?action=listarAreas&error=' . urlencode($mensaje));
                exit;
            }
            try{

                $resultado=$this->areamodelo->programarMantenimiento($id_persona,$id_area, $fecha_inicio, $fecha_fin);
                if ($resultado) {
                    header('Location: AreaComunControlador.php?action=listarAreas&success='.urldecode("La programacion del mantenimiento del area fue exitosa"));
                    exit;
                }else{
                    header('Location: AreaComunControlador.php?action=listarAreas&error='.urldecode("Error al programar la fecha de mantenimiento"));
                    exit;
                }
            }catch(Exception $e){
                header('Location: AreaComunControlador.php?action=listarAreas&error='.$e->getMessage());
                exit;
            }
        }

    }

    public function finalizarMantenimiento()
    {
        if (isset($_POST['action'])=="finalizarMantenimiento") {
            $camposRequeridos = ['id_area'];
            foreach($camposRequeridos as $campo){
                if (empty($_POST[$campo])||empty(trim($_POST[$campo]))) {
                    header('Location: AreaComunControlador.php?action=listarAreas&error='.urldecode("Error ".$campo." es obligatorio"));
                    exit;
                }
            }
            $id_area = intval($_POST['id_area']);

            try {
                $resultado = $this->areamodelo->finalizarMantenimiento($id_area);
                if ($resultado) {
                    header('Location: AreaComunControlador.php?action=listarAreas&success='.urldecode("La programacion del mantenimiento del area fue exitosa"));
                    exit;
                }else{
                    header('Location: AreaComunControlador.php?action=listarAreas&error='.urldecode("Error al programar la fecha de mantenimiento"));
                    exit;
                }

            }catch(Exception $e) {
                header('Location: AreaComunControlador.php?action=listarAreas&error='.$e->getMessage());
                exit;
            }

        }
    }
    // ver las reservas que tiene un area
    // En AreaComunControlador.php - CORREGIR esta función
    public function verReservasArea(){
        try {
            if (!isset($_GET['id_area']) || empty($_GET['id_area'])) {
                header('Location: AreaComunControlador.php?action=listarAreas&error=ID+de+area+no+especificado');
                exit;
            }

            $id_area = intval($_GET['id_area']);

            // Obtener información del área
            $area = $this->areamodelo->obtenerAreaPorId($id_area);
            if (!$area) {
                header('Location: AreaComunControlador.php?action=listarAreas&error=Area+no+encontrada');
                exit;
            }

            // Obtener reservas específicas de esta área
            $reservas = $this->areamodelo->obtenerReservasPorArea($id_area);
            if ($reservas === false) {
                $reservas = []; // Si hay error, mostrar lista vacía
            }

            include_once "../vista/verReservasAreaVista.php";

        } catch (Exception $e) {
            error_log("Error en verReservasArea: " . $e->getMessage());
            header('Location: AreaComunControlador.php?action=listarAreas&error=Error+al+cargar+las+reservas');
            exit;
        }
    }



    // Eliminar area comun
    public function eliminarArea(){
        try {
            if (!isset($_POST['id_area'])) {
                header('Location: AreaComunControlador.php?action=listarAreas&error=ID+de+area+no+especificado');
                exit;
            }

            $id_area = intval($_POST['id_area']);
            $resultado = $this->areamodelo->eliminarArea($id_area);

            if ($resultado) {
                header('Location: AreaComunControlador.php?action=listarAreas&success=Area+comun+eliminada+correctamente');
            } else {
                header('Location: AreaComunControlador.php?action=listarAreas&error=Error+al+eliminar+el+area+comun');
            }
            exit;

        } catch (Exception $e) {
            error_log("Error en eliminarArea controlador: " . $e->getMessage());
            header('Location: AreaComunControlador.php?action=listarAreas&error=Error+al+eliminar+el+area+comun');
            exit;
        }
    }

    // cambiar el estado de la reserva
    public function cambiarEstadoReserva() {
        try {
            $camposRequeridos = ['id_persona', 'id_area', 'fecha_reserva', 'hora_inicio', 'nuevo_estado'];
            foreach($camposRequeridos as $campo) {
                if (empty($_POST[$campo])) {
                    header('Location: AreaComunControlador.php?action=verReservasArea&id_area=' . $_POST['id_area'] . '&error=' . urlencode("Campo {$campo} es obligatorio"));
                    exit;
                }
            }

            $id_persona = intval($_POST['id_persona']);
            $id_area = intval($_POST['id_area']);
            $fecha_reserva = trim($_POST['fecha_reserva']);
            $hora_inicio = trim($_POST['hora_inicio']);
            $nuevo_estado = trim($_POST['nuevo_estado']);

            $resultado = $this->areamodelo->actualizarEstadoReserva($id_persona, $id_area, $fecha_reserva, $hora_inicio, $nuevo_estado);

            if ($resultado) {
                header('Location: AreaComunControlador.php?action=verReservasArea&id_area=' . $id_area . '&success=' . urlencode("Estado de reserva actualizado correctamente"));
            } else {
                header('Location: AreaComunControlador.php?action=verReservasArea&id_area=' . $id_area . '&error=' . urlencode("Error al actualizar el estado de la reserva"));
            }
            exit;

        } catch (Exception $e) {
            error_log("Error en cambiarEstadoReserva: " . $e->getMessage());
            header('Location: AreaComunControlador.php?action=verReservasArea&id_area=' . $_POST['id_area'] . '&error=' . urlencode("Error al cambiar el estado de la reserva"));
            exit;
        }
    }

    //informacion de las resrvas del mes
    public function verReservasMes() {
        try {
            // Obtener el mes solicitado (por defecto mes actual)
            $mes = $_GET['mes'] ?? date('Y-m');

            // Validar formato del mes (YYYY-MM)
            if (!preg_match('/^\d{4}-\d{2}$/', $mes)) {
                $mes = date('Y-m');
            }

            // Obtener reservas del mes
            $reservas = $this->areamodelo->obtenerReservasDelMes($mes);
            if ($reservas === false) {
                $reservas = [];
            }

            // Calcular mes anterior y siguiente
            $fecha = DateTime::createFromFormat('Y-m', $mes);
            $mesAnterior = $fecha->modify('-1 month')->format('Y-m');
            $mesSiguiente = $fecha->modify('+2 month')->format('Y-m'); // +2 porque ya restamos 1

            // Obtener nombre del mes en español
            $nombresMeses = [
                '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
                '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
                '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
            ];

            $partesMes = explode('-', $mes);
            $nombreMes = $nombresMeses[$partesMes[1]] ?? 'Mes Desconocido';
            $anio = $partesMes[0];

            include_once "../vista/verReservasMesVista.php";

        } catch (Exception $e) {
            error_log("Error en listarReservasMes: " . $e->getMessage());
            header('Location: AreaComunControlador.php?action=listarAreas&error=Error+al+cargar+las+reservas+del+mes');
            exit;
        }
    }

    public function verReservasPendientes(){
        // Verificar que la acción sea correcta
        if (isset($_GET['action']) && $_GET['action'] == 'verReservasPendientes') {

            $reservas = $this->areamodelo->obtenerReservasPendientes();

            // Verificar si hubo error en la consulta
            if ($reservas === false) {
                header("Location: AreaComunControlador.php?action=listarAreas&error=Error al cargar reservas pendientes");
                exit();
            }

            include_once "../vista/verReservasPendientesVista.php";
        }
    }




}

// Manejo de rutas GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    require_once '../../config/database.php';
    require_once '../modelo/AreaComunModelo.php';

    $database = new Database();
    $db = $database->getConnection();
    $controlador = new AreaComunControlador($db);

    if(isset($_GET['action'])) {
        switch($_GET['action']) {
            case 'listarAreas':
                $controlador->listarAreas();
                break;
            case 'formularioArea':
                $controlador->formularioArea();
                break;
            case 'formularioReservaArea':
                $controlador->formularioReservaArea();
                break;
            case 'verReservasArea':
                $controlador->verReservasArea();
                break;
            case 'verReservasMes':
                $controlador->verReservasMes();
                break;
            case 'verReservasPendientes':
                $controlador->verReservasPendientes();
                break;
            default:
                header('Location: ../vista/DashboardVista.php?error=Accion+no+valida+get');
                exit;
        }
    } else {
        // Accion por defecto
        $controlador->listarAreas();
    }
}

// Manejo de rutas POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../../config/database.php';
    require_once '../modelo/AreaComunModelo.php';

    $database = new Database();
    $db = $database->getConnection();
    $controlador = new AreaComunControlador($db);

    if (isset($_POST['action'])) {

    switch($_POST['action']) {
        case 'registrarArea':
            $controlador->registrarArea();
            break;
        case 'editarArea':
            $controlador->editarArea();
            break;
        case 'eliminarArea':
            $controlador->eliminarArea();
            break;
        case 'programarMantenimiento':
            $controlador->programarMantenimiento();
            break;
        case 'finalizarMantenimiento':
            $controlador->finalizarMantenimiento();
            break;
        case 'cambiarEstadoReserva':
            $controlador->cambiarEstadoReserva();
            break;
        default:
            header('Location: ../vista/DashboardVista.php?error=Accion+no+valida+post');
            exit;
    }

    }
}
?>
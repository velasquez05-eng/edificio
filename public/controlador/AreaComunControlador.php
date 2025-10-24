<?php
class AreaComunControlador
{
    private $areamodelo;

    public function __construct($db)
    {
        $this->areamodelo = new AreaComunModelo($db);
    }

    // Métodos para vistas
    public function listarAreas()
    {
        $areascomunes = $this->areamodelo->listarAreas();
        $numeroReservasPendientes = $this->areamodelo->contarReservasPendientes();
        $numeroReservasMes = $this->areamodelo->contarReservasEsteMes();
        $conteoArea = $this->areamodelo->contarAreasPorEstado();
        $numeroDisponible = $conteoArea['disponible'];
        $numeroMantenimiento = $conteoArea['mantenimiento'];
        $numeroNoDisponible = $conteoArea['no disponible'];
        $numeroTotalArea = $conteoArea['total'];

        include_once "../vista/ListarAreasComunesVista.php";
    }

    public function listarReservas()
    {
        try {
            session_start();
            $idPersona = $_SESSION['id_persona'] ?? null;

            if (!$idPersona) {
                header('Location: AreaComunControlador.php?action=listarAreas&error=No+se+pudo+identificar+al+usuario');
                exit;
            }

            $reservas = $this->areamodelo->obtenerReservasPorPersona($idPersona);
            $reservas = ($reservas === false || $reservas === null) ? [] : $reservas;

            include_once "../vista/listarReservasVista.php";
        } catch (Exception $e) {
            error_log("Error en listarReservas: " . $e->getMessage());
            $reservas = [];
            include_once "../vista/listarReservasVista.php";
        }
    }

    public function formularioArea()
    {
        include_once "../vista/RegistrarAreaVista.php";
    }

    public function formularioReservaArea()
    {
        try {
            date_default_timezone_set('America/La_Paz');
            session_start();

            $id_persona = $_SESSION['id_persona'] ?? 0;
            $id_rol = $_SESSION['id_rol'] ?? null;
            $rol = ($id_rol == 1) ? 'Administrador' : 'Residente';
            $fecha_actual = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');

            $fecha_obj = new DateTime($fecha_actual);
            $inicio_semana = clone $fecha_obj;
            $inicio_semana->modify('monday this week');
            $fin_semana = clone $inicio_semana;
            $fin_semana->modify('sunday this week');

            $areas = $this->areamodelo->listarAreas();
            $reservas_semana = [];
            $areas_info = [];

            foreach ($areas as $area) {
                if ($area['estado'] == 'disponible') {
                    $areas_info[$area['id_area']] = $area;
                    $reservas_area = $this->areamodelo->obtenerReservasPorArea($area['id_area']);

                    foreach ($reservas_area as $reserva) {
                        $fecha_reserva = $reserva['fecha_reserva'];

                        if ($fecha_reserva >= $inicio_semana->format('Y-m-d') &&
                            $fecha_reserva <= $fin_semana->format('Y-m-d') &&
                            $reserva['estado'] !== 'cancelada') {

                            $area_info = $areas_info[$reserva['id_area']];
                            $hora_inicio = intval(substr($reserva['hora_inicio'], 0, 2));
                            $hora_fin = intval(substr($reserva['hora_fin'], 0, 2));

                            for ($h = $hora_inicio; $h < $hora_fin; $h++) {
                                $hora_key = str_pad($h, 2, '0', STR_PAD_LEFT) . ':00';

                                if (!isset($reservas_semana[$fecha_reserva])) {
                                    $reservas_semana[$fecha_reserva] = [];
                                }
                                if (!isset($reservas_semana[$fecha_reserva][$hora_key])) {
                                    $reservas_semana[$fecha_reserva][$hora_key] = [];
                                }

                                $reservas_semana[$fecha_reserva][$hora_key][] = [
                                    'id_area' => $area_info['id_area'],
                                    'nombre_area' => $area_info['nombre'],
                                    'hora_inicio' => $reserva['hora_inicio'],
                                    'hora_fin' => $reserva['hora_fin'],
                                    'estado' => $reserva['estado'],
                                    'motivo' => $reserva['motivo']
                                ];
                            }
                        }
                    }
                }
            }

            $datos_vista = [
                'id_persona' => $id_persona,
                'id_rol' => $id_rol,
                'rol' => $rol,
                'fecha_actual' => $fecha_actual,
                'inicio_semana' => $inicio_semana,
                'fin_semana' => $fin_semana,
                'areas' => $areas,
                'reservas_semana' => $reservas_semana,
                'areas_info' => $areas_info
            ];

            include_once "../vista/RegistrarReservaAreaVista.php";
        } catch (Exception $e) {
            error_log("Error en formularioReservaArea: " . $e->getMessage());
            header('Location: AreaComunControlador.php?action=listarAreas&error=Error+al+cargar+el+formulario+de+reserva');
            exit;
        }
    }

    public function verReservasArea()
    {
        try {
            if (!isset($_GET['id_area']) || empty($_GET['id_area'])) {
                header('Location: AreaComunControlador.php?action=listarAreas&error=ID+de+area+no+especificado');
                exit;
            }

            $id_area = intval($_GET['id_area']);
            $area = $this->areamodelo->obtenerAreaPorId($id_area);

            if (!$area) {
                header('Location: AreaComunControlador.php?action=listarAreas&error=Area+no+encontrada');
                exit;
            }

            $reservas = $this->areamodelo->obtenerReservasPorArea($id_area);
            $reservas = ($reservas === false) ? [] : $reservas;

            include_once "../vista/verReservasAreaVista.php";
        } catch (Exception $e) {
            error_log("Error en verReservasArea: " . $e->getMessage());
            header('Location: AreaComunControlador.php?action=listarAreas&error=Error+al+cargar+las+reservas');
            exit;
        }
    }

    public function verReservasMes()
    {
        try {
            $mes = $_GET['mes'] ?? date('Y-m');

            if (!preg_match('/^\d{4}-\d{2}$/', $mes)) {
                $mes = date('Y-m');
            }

            $reservas = $this->areamodelo->obtenerReservasDelMes($mes);
            $reservas = ($reservas === false) ? [] : $reservas;

            $fecha = DateTime::createFromFormat('Y-m', $mes);
            $mesAnterior = $fecha->modify('-1 month')->format('Y-m');
            $mesSiguiente = $fecha->modify('+2 month')->format('Y-m');

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

    public function verReservasPendientes()
    {
        if (isset($_GET['action']) && $_GET['action'] == 'verReservasPendientes') {
            $reservas = $this->areamodelo->obtenerReservasPendientes();

            if ($reservas === false) {
                header("Location: AreaComunControlador.php?action=listarAreas&error=Error al cargar reservas pendientes");
                exit();
            }

            include_once "../vista/verReservasPendientesVista.php";
        }
    }

    // Métodos para gestión de áreas
    public function registrarArea()
    {
        try {
            if (empty($_POST['nombre']) || empty($_POST['capacidad'])) {
                header('Location: AreaComunControlador.php?action=formularioArea&error=Nombre+y+capacidad+son+obligatorios');
                exit;
            }

            $nombre = trim($_POST['nombre']);
            $descripcion = trim($_POST['descripcion'] ?? '');
            $capacidad = intval($_POST['capacidad']);
            $estado = $_POST['estado'] ?? 'disponible';
            $costo_reserva = $_POST['costo_reserva'] ?? '0';

            if ($capacidad < 1) {
                header('Location: AreaComunControlador.php?action=formularioArea&error=La+capacidad+debe+ser+mayor+a+0');
                exit;
            }

            $resultado = $this->areamodelo->registrarArea($nombre, $descripcion, $capacidad, $estado, $costo_reserva);

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

    public function editarArea()
    {
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

            $resultado = $this->areamodelo->editarArea($id_area, $nombre, $descripcion, $capacidad, $costo_reserva, $estado);

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

    public function eliminarArea()
    {
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

    // Métodos para mantenimiento
    public function programarMantenimiento()
    {
        if (isset($_POST['action']) && $_POST['action'] == "programarMantenimiento") {
            $camposRequeridos = ['id_area', 'fecha_inicio', 'fecha_fin'];

            foreach ($camposRequeridos as $campo) {
                if (empty($_POST[$campo]) || empty(trim($_POST[$campo]))) {
                    header('Location: AreaComunControlador.php?action=listarAreas&error=' . urlencode("Error " . $campo . " es obligatorio"));
                    exit;
                }
            }

            $id_persona = intval($_POST['id_persona']);
            $id_area = intval($_POST['id_area']);
            $fecha_inicio = trim($_POST['fecha_inicio']);
            $fecha_fin = trim($_POST['fecha_fin']);

            if (!$this->esIntervaloLibreDeFeriados($fecha_inicio, $fecha_fin)) {
                $mensaje = "Error: El intervalo de fechas del {$fecha_inicio} al {$fecha_fin} contiene días feriados";
                header('Location: AreaComunControlador.php?action=listarAreas&error=' . urlencode($mensaje));
                exit;
            }

            try {
                $resultado = $this->areamodelo->programarMantenimiento($id_persona, $id_area, $fecha_inicio, $fecha_fin);

                if ($resultado) {
                    header('Location: AreaComunControlador.php?action=listarAreas&success=' . urlencode("La programacion del mantenimiento del area fue exitosa"));
                } else {
                    header('Location: AreaComunControlador.php?action=listarAreas&error=' . urlencode("Error al programar la fecha de mantenimiento"));
                }
                exit;
            } catch (Exception $e) {
                header('Location: AreaComunControlador.php?action=listarAreas&error=' . $e->getMessage());
                exit;
            }
        }
    }

    public function finalizarMantenimiento()
    {
        if (isset($_POST['action']) && $_POST['action'] == "finalizarMantenimiento") {
            $camposRequeridos = ['id_area'];

            foreach ($camposRequeridos as $campo) {
                if (empty($_POST[$campo]) || empty(trim($_POST[$campo]))) {
                    header('Location: AreaComunControlador.php?action=listarAreas&error=' . urlencode("Error " . $campo . " es obligatorio"));
                    exit;
                }
            }

            $id_area = intval($_POST['id_area']);

            try {
                $resultado = $this->areamodelo->finalizarMantenimiento($id_area);

                if ($resultado) {
                    header('Location: AreaComunControlador.php?action=listarAreas&success=' . urlencode("El mantenimiento del área ha finalizado correctamente"));
                } else {
                    header('Location: AreaComunControlador.php?action=listarAreas&error=' . urlencode("Error al finalizar el mantenimiento"));
                }
                exit;
            } catch (Exception $e) {
                header('Location: AreaComunControlador.php?action=listarAreas&error=' . $e->getMessage());
                exit;
            }
        }
    }

    // Métodos para reservas
    public function registrarReserva()
    {
        try {
            $camposRequeridos = ['id_persona', 'id_area', 'fecha_reserva', 'hora_inicio', 'hora_fin', 'motivo'];

            foreach ($camposRequeridos as $campo) {
                if (empty($_POST[$campo])) {
                    header('Location: AreaComunControlador.php?action=formularioReservaArea&error=' . urlencode("El campo {$campo} es obligatorio"));
                    exit;
                }
            }

            $id_persona = intval($_POST['id_persona']);
            $id_area = intval($_POST['id_area']);
            $fecha_reserva = trim($_POST['fecha_reserva']);
            $hora_inicio = trim($_POST['hora_inicio']);
            $hora_fin = trim($_POST['hora_fin']);
            $motivo = trim($_POST['motivo']);
            $estado = $_POST['estado'] ?? 'pendiente';

            if (strlen($motivo) < 10) {
                header('Location: AreaComunControlador.php?action=formularioReservaArea&error=' . urlencode("El motivo debe tener al menos 10 caracteres"));
                exit;
            }

            $fecha_hoy = date('Y-m-d');
            if ($fecha_reserva < $fecha_hoy) {
                header('Location: AreaComunControlador.php?action=formularioReservaArea&error=' . urlencode("No se pueden hacer reservas para fechas pasadas"));
                exit;
            }

            if ($hora_fin <= $hora_inicio) {
                header('Location: AreaComunControlador.php?action=formularioReservaArea&error=' . urlencode("La hora de fin debe ser mayor a la hora de inicio"));
                exit;
            }

            $resultado = $this->areamodelo->registrarReserva($id_persona, $id_area, $fecha_reserva, $hora_inicio, $hora_fin, $motivo, $estado);

            if ($resultado) {
                header('Location: AreaComunControlador.php?action=formularioReservaArea&success=' . urlencode("Reserva registrada correctamente"));
            } else {
                header('Location: AreaComunControlador.php?action=formularioReservaArea&error=' . urlencode("Error al registrar la reserva"));
            }
            exit;
        } catch (Exception $e) {
            error_log("Error en registrarReserva: " . $e->getMessage());
            header('Location: AreaComunControlador.php?action=formularioReservaArea&error=' . urlencode("Error al procesar la reserva"));
            exit;
        }
    }

    public function modificarReservaUsuario()
    {
        try {
            $camposRequeridos = ['id_persona', 'id_area', 'fecha_reserva_original', 'hora_inicio_original', 'fecha_reserva', 'hora_inicio', 'hora_fin'];

            foreach ($camposRequeridos as $campo) {
                if (empty($_POST[$campo])) {
                    header('Location: AreaComunControlador.php?action=listarReservas&error=' . urlencode("Campo {$campo} es obligatorio"));
                    exit;
                }
            }

            $id_persona = intval($_POST['id_persona']);
            $id_area = intval($_POST['id_area']);
            $fecha_reserva_original = trim($_POST['fecha_reserva_original']);
            $hora_inicio_original = trim($_POST['hora_inicio_original']);
            $fecha_reserva = trim($_POST['fecha_reserva']);
            $hora_inicio = trim($_POST['hora_inicio']);
            $hora_fin = trim($_POST['hora_fin']);

            $fecha_hoy = date('Y-m-d');
            if ($fecha_reserva < $fecha_hoy) {
                header('Location: AreaComunControlador.php?action=listarReservas&error=' . urlencode("No se pueden modificar reservas para fechas pasadas"));
                exit;
            }

            if ($hora_fin <= $hora_inicio) {
                header('Location: AreaComunControlador.php?action=listarReservas&error=' . urlencode("La hora de fin debe ser mayor a la hora de inicio"));
                exit;
            }

            $reservaExistente = $this->areamodelo->verificarReservaUsuario($id_persona, $id_area, $fecha_reserva_original, $hora_inicio_original);
            if (!$reservaExistente) {
                header('Location: AreaComunControlador.php?action=listarReservas&error=' . urlencode("Reserva no encontrada o no pertenece al usuario"));
                exit;
            }

            $disponible = $this->areamodelo->verificarDisponibilidadModificacion($id_area, $fecha_reserva, $hora_inicio, $hora_fin, $fecha_reserva_original, $hora_inicio_original);

            if (!$disponible) {
                header('Location: AreaComunControlador.php?action=listarReservas&error=' . urlencode("El nuevo horario no está disponible. Ya existe una reserva en ese horario."));
                exit;
            }

            $reservaMismoHorario = $this->areamodelo->verificarReservaUsuarioMismoHorario(
                $id_persona,
                $id_area,
                $fecha_reserva,
                $hora_inicio,
                $hora_fin,
                $fecha_reserva_original,
                $hora_inicio_original
            );

            if ($reservaMismoHorario) {
                header('Location: AreaComunControlador.php?action=listarReservas&error=' . urlencode("Ya tienes otra reserva en ese horario para la misma área"));
                exit;
            }

            $resultado = $this->areamodelo->modificarReserva($id_persona, $id_area, $fecha_reserva_original, $hora_inicio_original, $fecha_reserva, $hora_inicio, $hora_fin);

            if ($resultado) {
                header('Location: AreaComunControlador.php?action=listarReservas&success=' . urlencode("Reserva modificada correctamente"));
            } else {
                header('Location: AreaComunControlador.php?action=listarReservas&error=' . urlencode("Error al modificar la reserva en la base de datos"));
            }
            exit;
        } catch (Exception $e) {
            error_log("Error en modificarReservaUsuario: " . $e->getMessage());
            header('Location: AreaComunControlador.php?action=listarReservas&error=' . urlencode("Error al procesar la modificación de la reserva"));
            exit;
        }
    }

    public function cancelarReservaUsuario()
    {
        try {
            $camposRequeridos = ['id_persona', 'id_area', 'fecha_reserva', 'hora_inicio'];

            foreach ($camposRequeridos as $campo) {
                if (empty($_POST[$campo])) {
                    header('Location: AreaComunControlador.php?action=listarReservas&error=' . urlencode("Campo {$campo} es obligatorio"));
                    exit;
                }
            }

            $id_persona = intval($_POST['id_persona']);
            $id_area = intval($_POST['id_area']);
            $fecha_reserva = trim($_POST['fecha_reserva']);
            $hora_inicio = trim($_POST['hora_inicio']);

            $reservaExistente = $this->areamodelo->verificarReservaUsuario($id_persona, $id_area, $fecha_reserva, $hora_inicio);
            if (!$reservaExistente) {
                header('Location: AreaComunControlador.php?action=listarReservas&error=' . urlencode("Reserva no encontrada o no pertenece al usuario"));
                exit;
            }

            $resultado = $this->areamodelo->cancelarReservaUsuario($id_persona, $id_area, $fecha_reserva, $hora_inicio);

            if ($resultado) {
                header('Location: AreaComunControlador.php?action=listarReservas&success=' . urlencode("Reserva cancelada correctamente"));
            } else {
                header('Location: AreaComunControlador.php?action=listarReservas&error=' . urlencode("Error al cancelar la reserva"));
            }
            exit;
        } catch (Exception $e) {
            error_log("Error en cancelarReservaUsuario: " . $e->getMessage());
            header('Location: AreaComunControlador.php?action=listarReservas&error=' . urlencode("Error al procesar la cancelación de la reserva"));
            exit;
        }
    }

    public function cambiarEstadoReserva()
    {
        try {
            $camposRequeridos = ['id_persona', 'id_area', 'fecha_reserva', 'hora_inicio', 'nuevo_estado'];

            foreach ($camposRequeridos as $campo) {
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

    // Métodos auxiliares para validación de feriados
    private function getListaFeriadosBolivia(int $year): array
    {
        $feriados = [];

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

        $pascuaTimestamp = easter_date($year);
        $pascuaDate = new DateTime('@' . $pascuaTimestamp);
        $pascuaDate->setTimezone(new DateTimeZone('America/La_Paz'));

        $feriados[] = (clone $pascuaDate)->modify('-48 days')->format('Y-m-d');
        $feriados[] = (clone $pascuaDate)->modify('-47 days')->format('Y-m-d');
        $feriados[] = (clone $pascuaDate)->modify('-2 days')->format('Y-m-d');
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

            return !in_array($fechaFormateada, $feriados);
        } catch (Exception $e) {
            return false;
        }
    }

    private function esIntervaloLibreDeFeriados(string $fecha_inicio, string $fecha_fin): bool
    {
        try {
            $inicio = new DateTime($fecha_inicio);
            $fin = new DateTime($fecha_fin);

            if ($inicio > $fin) {
                return false;
            }

            $fin->modify('+1 day');

            $periodo = new DatePeriod(
                $inicio,
                new DateInterval('P1D'),
                $fin
            );

            foreach ($periodo as $fecha) {
                $fecha_string = $fecha->format('Y-m-d');
                if (!$this->esDiaFeriado($fecha_string)) {
                    return false;
                }
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}

// Manejo de rutas
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    require_once '../../config/database.php';
    require_once '../modelo/AreaComunModelo.php';

    $database = new Database();
    $db = $database->getConnection();
    $controlador = new AreaComunControlador($db);

    if (isset($_GET['action'])) {
        switch ($_GET['action']) {
            case 'listarAreas':
                $controlador->listarAreas();
                break;
            case 'listarReservas':
                $controlador->listarReservas();
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
        $controlador->listarAreas();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../../config/database.php';
    require_once '../modelo/AreaComunModelo.php';

    $database = new Database();
    $db = $database->getConnection();
    $controlador = new AreaComunControlador($db);

    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
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
            case 'registrarReserva':
                $controlador->registrarReserva();
                break;
            case 'modificarReservaUsuario':
                $controlador->modificarReservaUsuario();
                break;
            case 'cancelarReservaUsuario':
                $controlador->cancelarReservaUsuario();
                break;
            default:
                header('Location: ../vista/DashboardVista.php?error=Accion+no+valida+post');
                exit;
        }
    }
}
?>
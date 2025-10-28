<?php
class ServicioControlador
{
    private $serviciomodelo;

    public function __construct($db)
    {
        $this->serviciomodelo = new ServicioModelo($db);
    }

    // Métodos para vistas
    public function listarServicios()
    {
        try {
            $servicios = $this->serviciomodelo->listarServicios();
            $servicios = ($servicios === false || $servicios === null) ? [] : $servicios;

            include_once "../vista/ListarServiciosVista.php";
        } catch (Exception $e) {
            error_log("Error en listarServicios: " . $e->getMessage());
            $servicios = [];
            include_once "../vista/ListarServiciosVista.php";
        }
    }

    public function formularioServicio()
    {
        include_once "../vista/RegistrarServicioVista.php";
    }

    public function formularioGenerarConsumo()
    {
        try {
            $departamentos = $this->serviciomodelo->obtenerDepartamentos();
            $departamentos = ($departamentos === false || $departamentos === null) ? [] : $departamentos;

            include_once "../vista/GenerarConsumoServicioVista.php";
        } catch (Exception $e) {
            error_log("Error en formularioGenerarConsumo: " . $e->getMessage());
            $departamentos = [];
            include_once "../vista/GenerarConsumoServicioVista.php";
        }
    }

    public function formularioAsignarServicio()
    {
        try {
            $departamentos = $this->serviciomodelo->obtenerDepartamentos();
            $servicios = $this->serviciomodelo->listarServicios();
            $medidores = $this->serviciomodelo->obtenerTodosMedidores();

            $departamentos = ($departamentos === false || $departamentos === null) ? [] : $departamentos;
            $servicios = ($servicios === false || $servicios === null) ? [] : $servicios;
            $medidores = ($medidores === false || $medidores === null) ? [] : $medidores;

            include_once "../vista/AsignarServicioDepartamentoVista.php";
        } catch (Exception $e) {
            error_log("Error en formularioAsignarServicio: " . $e->getMessage());
            $departamentos = [];
            $servicios = [];
            $medidores = [];
            include_once "../vista/AsignarServicioDepartamentoVista.php";
        }
    }


    // Método original para ver historial de consumo (redirige a vista separada)
    public function verHistorialConsumo()
    {
        try {
            $id_medidor = $_GET['id_medidor'] ?? null;
            $fecha_inicio = $_GET['fecha_inicio'] ?? null;
            $fecha_fin = $_GET['fecha_fin'] ?? null;

            if (!$id_medidor) {
                header('Location: ServicioControlador.php?action=listarServicios&error=ID+de+medidor+no+especificado');
                exit;
            }

            $historial = $this->serviciomodelo->verHistorialConsumo($id_medidor, $fecha_inicio, $fecha_fin);
            $historial = ($historial === false || $historial === null) ? [] : $historial;

            // Obtener información del medidor para mostrar en la vista
            $medidores = $this->serviciomodelo->obtenerTodosMedidores();
            $medidor_actual = null;
            foreach ($medidores as $medidor) {
                if ($medidor['id_medidor'] == $id_medidor) {
                    $medidor_actual = $medidor;
                    break;
                }
            }

            include_once "../vista/verHistorialConsumoVista.php";
        } catch (Exception $e) {
            error_log("Error en verHistorialConsumo: " . $e->getMessage());
            header('Location: ServicioControlador.php?action=listarServicios&error=Error+al+cargar+el+historial+de+consumo');
            exit;
        }
    }


    // Métodos para gestión de servicios
    public function registrarServicio()
    {
        try {
            if (empty($_POST['nombre']) || empty($_POST['unidad_medida']) || empty($_POST['costo_unitario'])) {
                header('Location: ServicioControlador.php?action=formularioServicio&error=Nombre,+unidad+de+medida+y+costo+unitario+son+obligatorios');
                exit;
            }

            $nombre = trim($_POST['nombre']);
            $unidad_medida = trim($_POST['unidad_medida']);
            $costo_unitario = floatval($_POST['costo_unitario']);
            $estado = $_POST['estado'] ?? 'activo';

            if ($costo_unitario <= 0) {
                header('Location: ServicioControlador.php?action=formularioServicio&error=El+costo+unitario+debe+ser+mayor+a+0');
                exit;
            }

            // Verificar si ya existe un servicio con el mismo nombre
            if ($this->serviciomodelo->existeServicio($nombre)) {
                header('Location: ServicioControlador.php?action=formularioServicio&error=Ya+existe+un+servicio+con+ese+nombre');
                exit;
            }

            $resultado = $this->serviciomodelo->registrarServicio($nombre, $unidad_medida, $costo_unitario, $estado);

            if ($resultado) {
                header('Location: ServicioControlador.php?action=formularioServicio&success=Servicio+registrado+correctamente');
            } else {
                header('Location: ServicioControlador.php?action=formularioServicio&error=Error+al+registrar+el+servicio');
            }
            exit;
        } catch (Exception $e) {
            error_log("Error en registrarServicio controlador: " . $e->getMessage());
            header('Location: ServicioControlador.php?action=formularioServicio&error=Error+al+registrar+el+servicio');
            exit;
        }
    }

    public function editarServicio()
    {
        try {
            if (empty($_POST['id_servicio']) || empty($_POST['unidad_medida']) || empty($_POST['costo_unitario'])) {
                header('Location: ServicioControlador.php?action=listarServicios&error=Todos+los+campos+son+obligatorios');
                exit;
            }

            $id_servicio = intval($_POST['id_servicio']);
            $unidad_medida = trim($_POST['unidad_medida']);
            $costo_unitario = floatval($_POST['costo_unitario']);
            $estado = $_POST['estado'] ?? 'activo';

            if ($costo_unitario <= 0) {
                header('Location: ServicioControlador.php?action=listarServicios&error=El+costo+unitario+debe+ser+mayor+a+0');
                exit;
            }

            $resultado = $this->serviciomodelo->actualizarServicio($id_servicio, $unidad_medida, $costo_unitario, $estado);

            if ($resultado) {
                header('Location: ServicioControlador.php?action=listarServicios&success=Servicio+actualizado+correctamente');
            } else {
                header('Location: ServicioControlador.php?action=listarServicios&error=Error+al+actualizar+el+servicio');
            }
            exit;
        } catch (Exception $e) {
            error_log("Error en editarServicio controlador: " . $e->getMessage());
            header('Location: ServicioControlador.php?action=listarServicios&error=Error+al+actualizar+el+servicio');
            exit;
        }
    }

    // Métodos para asignación de servicios a departamentos
    public function asignarServicioDepartamento()
    {
        try {
            $camposRequeridos = ['id_departamento', 'id_servicio', 'codigo_medidor'];

            foreach ($camposRequeridos as $campo) {
                if (empty($_POST[$campo])) {
                    header('Location: ServicioControlador.php?action=formularioAsignarServicio&error=' . urlencode("El campo {$campo} es obligatorio"));
                    exit;
                }
            }

            $id_departamento = intval($_POST['id_departamento']);
            $id_servicio = intval($_POST['id_servicio']);
            $codigo_medidor = trim($_POST['codigo_medidor']);
            $fecha_instalacion = $_POST['fecha_instalacion'] ?? date('Y-m-d');
            $estado_medidor = $_POST['estado_medidor'] ?? 'activo';

            // Validar código único
            if (strlen($codigo_medidor) < 3) {
                header('Location: ServicioControlador.php?action=formularioAsignarServicio&error=' . urlencode("El código del medidor debe tener al menos 3 caracteres"));
                exit;
            }

            // Verificar si ya existe un medidor con el mismo código
            if ($this->serviciomodelo->existeMedidor($codigo_medidor)) {
                header('Location: ServicioControlador.php?action=formularioAsignarServicio&error=' . urlencode("Ya existe un medidor con ese código"));
                exit;
            }

            // Verificar si el departamento ya tiene asignado este servicio
            if ($this->serviciomodelo->existeAsignacion($id_departamento, $id_servicio)) {
                header('Location: ServicioControlador.php?action=formularioAsignarServicio&error=' . urlencode("Este departamento ya tiene asignado este servicio"));
                exit;
            }

            $resultado = $this->serviciomodelo->asignarServicioDepartamento(
                $id_departamento,
                $id_servicio,
                $codigo_medidor,
                $fecha_instalacion,
                $estado_medidor
            );

            if ($resultado) {
                header('Location: ServicioControlador.php?action=formularioAsignarServicio&success=' . urlencode("Servicio asignado correctamente al departamento"));
            } else {
                header('Location: ServicioControlador.php?action=formularioAsignarServicio&error=' . urlencode("Error al asignar el servicio al departamento"));
            }
            exit;
        } catch (Exception $e) {
            error_log("Error en asignarServicioDepartamento: " . $e->getMessage());
            header('Location: ServicioControlador.php?action=formularioAsignarServicio&error=' . urlencode("Error al procesar la asignación"));
            exit;
        }
    }

    public function editarMedidor()
    {
        try {
            $camposRequeridos = ['id_medidor', 'codigo_medidor', 'estado_medidor', 'fecha_instalacion'];

            foreach ($camposRequeridos as $campo) {
                if (empty($_POST[$campo])) {
                    header('Location: ServicioControlador.php?action=formularioAsignarServicio&error=' . urlencode("El campo {$campo} es obligatorio"));
                    exit;
                }
            }

            $id_medidor = intval($_POST['id_medidor']);
            $codigo_medidor = trim($_POST['codigo_medidor']);
            $estado_medidor = trim($_POST['estado_medidor']);
            $fecha_instalacion = trim($_POST['fecha_instalacion']);

            // Validar código único (excluyendo el medidor actual)
            if ($this->serviciomodelo->existeMedidor($codigo_medidor, $id_medidor)) {
                header('Location: ServicioControlador.php?action=formularioAsignarServicio&error=' . urlencode("Ya existe otro medidor con ese código"));
                exit;
            }

            $resultado = $this->serviciomodelo->editarMedidor(
                $id_medidor,
                $codigo_medidor,
                $fecha_instalacion,
                $estado_medidor
            );

            if ($resultado) {
                header('Location: ServicioControlador.php?action=formularioAsignarServicio&success=' . urlencode("Medidor actualizado correctamente"));
            } else {
                header('Location: ServicioControlador.php?action=formularioAsignarServicio&error=' . urlencode("Error al actualizar el medidor"));
            }
            exit;
        } catch (Exception $e) {
            error_log("Error en editarMedidor: " . $e->getMessage());
            header('Location: ServicioControlador.php?action=formularioAsignarServicio&error=' . urlencode("Error al procesar la actualización"));
            exit;
        }
    }

    public function eliminarMedidor()
    {
        try {
            if (empty($_POST['id_medidor'])) {
                header('Location: ServicioControlador.php?action=formularioAsignarServicio&error=' . urlencode("ID de medidor no especificado"));
                exit;
            }

            $id_medidor = intval($_POST['id_medidor']);

            $resultado = $this->serviciomodelo->eliminarMedidor($id_medidor);

            if ($resultado) {
                header('Location: ServicioControlador.php?action=formularioAsignarServicio&success=' . urlencode("Asignación eliminada correctamente"));
            } else {
                header('Location: ServicioControlador.php?action=formularioAsignarServicio&error=' . urlencode("Error al eliminar la asignación"));
            }
            exit;
        } catch (Exception $e) {
            error_log("Error en eliminarMedidor: " . $e->getMessage());
            header('Location: ServicioControlador.php?action=formularioAsignarServicio&error=' . urlencode("Error al procesar la eliminación"));
            exit;
        }
    }

    // Métodos para generación de consumos
    public function generarConsumoIndividual()
    {
        try {
            $camposRequeridos = ['id_medidor', 'consumo'];

            foreach ($camposRequeridos as $campo) {
                if (empty($_POST[$campo])) {
                    header('Location: ServicioControlador.php?action=formularioGenerarConsumo&error=' . urlencode("El campo {$campo} es obligatorio"));
                    exit;
                }
            }

            $id_medidor = intval($_POST['id_medidor']);
            $consumo = floatval($_POST['consumo']);
            $fecha_hora = $_POST['fecha_hora'] ?? null;

            if ($consumo <= 0) {
                header('Location: ServicioControlador.php?action=formularioGenerarConsumo&error=' . urlencode("El consumo debe ser mayor a 0"));
                exit;
            }

            $resultado = $this->serviciomodelo->generarConsumo($id_medidor, $consumo, $fecha_hora);

            if ($resultado) {
                header('Location: ServicioControlador.php?action=formularioGenerarConsumo&success=' . urlencode("Consumo registrado correctamente"));
            } else {
                header('Location: ServicioControlador.php?action=formularioGenerarConsumo&error=' . urlencode("Error al registrar el consumo"));
            }
            exit;
        } catch (Exception $e) {
            error_log("Error en generarConsumoIndividual: " . $e->getMessage());
            header('Location: ServicioControlador.php?action=formularioGenerarConsumo&error=' . urlencode("Error al procesar el consumo"));
            exit;
        }
    }

    public function generarConsumosMasivos()
    {
        try {
            $camposRequeridos = ['year', 'month'];

            foreach ($camposRequeridos as $campo) {
                if (empty($_POST[$campo])) {
                    header('Location: ServicioControlador.php?action=formularioGenerarConsumo&error=' . urlencode("El campo {$campo} es obligatorio"));
                    exit;
                }
            }

            $year = intval($_POST['year']);
            $month = intval($_POST['month']);
            $id_departamento = $_POST['departamento'] ?? null;

            // Validar mes y año
            if ($month < 1 || $month > 12) {
                header('Location: ServicioControlador.php?action=formularioGenerarConsumo&error=' . urlencode("Mes inválido"));
                exit;
            }

            if ($year < 2020 || $year > 2030) {
                header('Location: ServicioControlador.php?action=formularioGenerarConsumo&error=' . urlencode("Año inválido"));
                exit;
            }

            $resultado = $this->serviciomodelo->generarConsumosMasivos($year, $month, $id_departamento);

            if ($resultado !== false) {
                // Generar también el resumen en historial_consumo
                $this->generarResumenConsumoMes($year, $month, $id_departamento);

                $mensaje = "Consumos masivos generados correctamente. Total de registros: " . $resultado;
                header('Location: ServicioControlador.php?action=formularioGenerarConsumo&success=' . urlencode($mensaje));
            } else {
                header('Location: ServicioControlador.php?action=formularioGenerarConsumo&error=' . urlencode("Error al generar los consumos masivos"));
            }
            exit;
        } catch (Exception $e) {
            error_log("Error en generarConsumosMasivos: " . $e->getMessage());
            header('Location: ServicioControlador.php?action=formularioGenerarConsumo&error=' . urlencode("Error al procesar los consumos masivos"));
            exit;
        }
    }

    private function generarResumenConsumoMes($year, $month, $id_departamento = null)
    {
        try {
            // Calcular fechas de inicio y fin del mes
            $fecha_inicio = sprintf("%04d-%02d-01 00:00:00", $year, $month);
            $last_day = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $fecha_fin = sprintf("%04d-%02d-%02d 23:59:59", $year, $month, $last_day);

            // Obtener todos los medidores (o solo los del departamento específico)
            if ($id_departamento) {
                $medidores = $this->serviciomodelo->obtenerMedidoresDepartamento($id_departamento);
            } else {
                $medidores = $this->serviciomodelo->obtenerTodosMedidores();
            }

            foreach ($medidores as $medidor) {
                $this->serviciomodelo->generarResumenConsumo($medidor['id_medidor'], $fecha_inicio, $fecha_fin);
            }

            return true;
        } catch (Exception $e) {
            error_log("Error en generarResumenConsumoMes: " . $e->getMessage());
            return false;
        }
    }

    public function verReporteConsumos()
    {
        try {
            $year = $_GET['year'] ?? date('Y');
            $month = $_GET['month'] ?? date('n');
            $id_departamento = $_GET['departamento'] ?? null;

            // Validar parámetros
            if (!is_numeric($year) || !is_numeric($month)) {
                header('Location: ServicioControlador.php?action=formularioGenerarConsumo&error=Parámetros+inválidos');
                exit;
            }

            $year = intval($year);
            $month = intval($month);

            if ($month < 1 || $month > 12) {
                header('Location: ServicioControlador.php?action=formularioGenerarConsumo&error=Mes+inválido');
                exit;
            }

            // Obtener datos para el reporte
            $fecha_inicio = sprintf("%04d-%02d-01", $year, $month);
            $last_day = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $fecha_fin = sprintf("%04d-%02d-%02d", $year, $month, $last_day);

            // Obtener todos los medidores para el reporte
            if ($id_departamento) {
                $medidores = $this->serviciomodelo->obtenerMedidoresDepartamento($id_departamento);
            } else {
                $medidores = $this->serviciomodelo->obtenerTodosMedidores();
            }

            // Calcular consumos y costos para cada medidor
            $reporte_data = [];
            $total_general = 0;

            foreach ($medidores as $medidor) {
                $historial = $this->serviciomodelo->verHistorialConsumo($medidor['id_medidor'], $fecha_inicio, $fecha_fin);

                $consumo_total = 0;
                $dias_medidos = count($historial);

                foreach ($historial as $lectura) {
                    $consumo_total += $lectura['consumo'];
                }

                $costo_total = $consumo_total * $medidor['costo_unitario'];
                $consumo_promedio = $dias_medidos > 0 ? $consumo_total / $dias_medidos : 0;

                $reporte_data[] = [
                    'medidor' => $medidor,
                    'consumo_total' => $consumo_total,
                    'dias_medidos' => $dias_medidos,
                    'consumo_promedio' => $consumo_promedio,
                    'costo_total' => $costo_total
                ];

                $total_general += $costo_total;
            }

            $departamentos = $this->serviciomodelo->obtenerDepartamentos();

            include_once "../vista/verReporteConsumosVista.php";
        } catch (Exception $e) {
            error_log("Error en verReporteConsumos: " . $e->getMessage());
            header('Location: ServicioControlador.php?action=formularioGenerarConsumo&error=Error+al+generar+el+reporte');
            exit;
        }
    }

    /**
     * Eliminar una lectura de consumo individual
     */
    public function eliminarLectura()
    {
        try {
            if (empty($_POST['id_lectura'])) {
                header('Location: ServicioControlador.php?action=listarServicios&error=ID+de+lectura+no+especificado');
                exit;
            }

            $id_lectura = intval($_POST['id_lectura']);
            $id_medidor = $_POST['id_medidor'] ?? null; // Para redireccionar de vuelta al historial

            // Obtener el id_medidor antes de eliminar para poder redireccionar
            if (!$id_medidor) {
                // Si no viene en el POST, obtenerlo de la lectura
                $lectura = $this->serviciomodelo->obtenerLecturaPorId($id_lectura);
                $id_medidor = $lectura ? $lectura['id_medidor'] : null;
            }

            $resultado = $this->serviciomodelo->eliminarLectura($id_lectura);

            if ($resultado) {
                $mensaje = "Lectura eliminada correctamente";
                if ($id_medidor) {
                    header('Location: ServicioControlador.php?action=verHistorialConsumo&id_medidor=' . $id_medidor . '&success=' . urlencode($mensaje));
                } else {
                    header('Location: ServicioControlador.php?action=listarServicios&success=' . urlencode($mensaje));
                }
            } else {
                $mensaje = "Error al eliminar la lectura";
                if ($id_medidor) {
                    header('Location: ServicioControlador.php?action=verHistorialConsumo&id_medidor=' . $id_medidor . '&error=' . urlencode($mensaje));
                } else {
                    header('Location: ServicioControlador.php?action=listarServicios&error=' . urlencode($mensaje));
                }
            }
            exit;
        } catch (Exception $e) {
            error_log("Error en eliminarLectura: " . $e->getMessage());
            $mensaje = "Error al procesar la eliminación";
            if ($id_medidor ?? null) {
                header('Location: ServicioControlador.php?action=verHistorialConsumo&id_medidor=' . $id_medidor . '&error=' . urlencode($mensaje));
            } else {
                header('Location: ServicioControlador.php?action=listarServicios&error=' . urlencode($mensaje));
            }
            exit;
        }
    }
}

// Manejo de rutas
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    require_once '../../config/database.php';
    require_once '../modelo/ServicioModelo.php';

    $database = new Database();
    $db = $database->getConnection();
    $controlador = new ServicioControlador($db);

    if (isset($_GET['action'])) {
        switch ($_GET['action']) {
            case 'listarServicios':
                $controlador->listarServicios();
                break;
            case 'formularioServicio':
                $controlador->formularioServicio();
                break;
            case 'formularioGenerarConsumo':
                $controlador->formularioGenerarConsumo();
                break;
            case 'formularioAsignarServicio':
                $controlador->formularioAsignarServicio();
                break;
            case 'verHistorialConsumo':
                $controlador->verHistorialConsumo();
                break;
            case 'verReporteConsumos':
                $controlador->verReporteConsumos();
                break;
            default:
                header('Location: ../vista/DashboardVista.php?error=Accion+no+valida+get');
                exit;
        }
    } else {
        $controlador->listarServicios();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../../config/database.php';
    require_once '../modelo/ServicioModelo.php';

    $database = new Database();
    $db = $database->getConnection();
    $controlador = new ServicioControlador($db);

    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'registrarServicio':
                $controlador->registrarServicio();
                break;
            case 'editarServicio':
                $controlador->editarServicio();
                break;
            case 'asignarServicioDepartamento':
                $controlador->asignarServicioDepartamento();
                break;
            case 'editarMedidor':
                $controlador->editarMedidor();
                break;
            case 'eliminarMedidor':
                $controlador->eliminarMedidor();
                break;
            case 'generarConsumoIndividual':
                $controlador->generarConsumoIndividual();
                break;
            case 'generarConsumosMasivos':
                $controlador->generarConsumosMasivos();
                break;
            case 'eliminarLectura':
                $controlador->eliminarLectura();
                break;
            default:
                header('Location: ../vista/DashboardVista.php?error=Accion+no+valida+post');
                exit;
        }
    }
}
?>
<?php

class DepartamentoControlador
{
    private $departamentomodelo;
    private $personamodelo;
    private $serviciomodelo;

    public function __construct($db){
        $this->departamentomodelo = new DepartamentoModelo($db);
        $this->personamodelo = new PersonaModelo($db);
        $this->serviciomodelo = new ServicioModelo($db);
    }

    public function listarDepartamentos(){
        // Usar el método original para el listado básico
        $departamentos = $this->departamentomodelo->listarDepartamento();
        include_once "../vista/ListarDepartamentoVista.php";
    }

    public function formularioDepartamento(){
        include_once "../vista/RegistrarDepartamentoVista.php";
    }

    public function formularioAsignarPersonasDepartamento(){
        $departamentos = $this->departamentomodelo->listarDepartamento();
        $personas = $this->personamodelo->listarResidente();
        include_once "../vista/RegistrarPersonasDepartamentoVista.php";
    }

    public function registrarDepartamento(){
        if($_POST['action'] == "registrarDepartamento"){
            $camposRequeridos = ['numero', 'piso'];
            foreach($camposRequeridos as $campo) {
                if(!isset($_POST[$campo]) || empty(trim($_POST[$campo]))) {
                    $this->redirigirConError("El campo $campo es obligatorio");
                }
            }

            $numero = htmlspecialchars($_POST['numero']);
            $piso = htmlspecialchars($_POST['piso']);

            // Verificar el número de departamento (sin excluir ningún ID)
            if($this->departamentomodelo->verificarDepartamento($numero)){
                $this->redirigirConError("El número de departamento ya está registrado en el sistema.");
            }

            try {
                $resultado = $this->departamentomodelo->registrarDepartamento($numero, $piso);
                if($resultado){
                    $this->redirigirConExito("Departamento registrado exitosamente");
                } else {
                    $this->redirigirConError("Error al registrar departamento - No se pudo ejecutar la consulta");
                }
            } catch (Exception $e) {
                $this->redirigirConError("Error en la base de datos: ".$e->getMessage());
            }
        }
    }

    public function editarDepartamento(){
        if($_POST['action'] == "editarDepartamento"){
            $camposRequeridos = ['id_departamento', 'numero', 'piso'];
            foreach($camposRequeridos as $campo) {
                if(!isset($_POST[$campo]) || empty(trim($_POST[$campo]))) {
                    $this->redirigirEditarConError("El campo $campo es obligatorio");
                }
            }

            $id_departamento = htmlspecialchars($_POST['id_departamento']);
            $numero = htmlspecialchars($_POST['numero']);
            $piso = htmlspecialchars($_POST['piso']);

            // Verificar si el número ya existe en OTRO departamento (excluyendo el actual)
            if($this->departamentomodelo->verificarDepartamentoExcluyendo($numero, $id_departamento)){
                $this->redirigirEditarConError("El número de departamento ya está registrado en otro departamento.");
            }

            try {
                $resultado = $this->departamentomodelo->editarDepartamento($id_departamento, $numero, $piso);
                if($resultado){
                    $this->redirigirEditarConExito("Departamento actualizado exitosamente");
                } else {
                    $this->redirigirEditarConError("Error al actualizar departamento - No se pudo actualizar el departamento");
                }
            } catch (Exception $e) {
                $this->redirigirEditarConError("Error en la base de datos: ".$e->getMessage());
            }
        }
    }

    public function asignarPersonasDepartamento()
    {
        if($_POST['action'] == "asignarPersonasDepartamento"){

            $id_departamento = $_POST['id_departamento'] ?? null;
            $personas_ids = json_decode($_POST['personas'] ?? '[]', true);
            try {
                $resultado = $this->departamentomodelo->asignarPersonasDepartamento($id_departamento, $personas_ids);
                if ($resultado === true) {
                    header('Location: DepartamentoControlador.php?action=formularioAsignarPersonasDepartamento&success=' . urlencode('Personas asignadas correctamente al departamento'));
                } else {
                    header('Location: DepartamentoControlador.php?action=formularioAsignarPersonasDepartamento&error= ' . urlencode('Error al asignar - No se pudo ejecutar la consulta'));
                }
            } catch (Exception $e) {
                header('Location: DepartamentoControlador.php?action=formularioAsignarPersonasDepartamento&error= ' . $e->getMessage());
            }
        }
    }

    // Método para ver detalles del departamento (residentes y medidores)
    public function verDetallesDepartamento() {
        try {
            $id_departamento = $_GET['id_departamento'] ?? null;

            if (!$id_departamento) {
                header('Location: DepartamentoControlador.php?action=listarDepartamentos&error=ID+de+departamento+no+especificado');
                exit;
            }

            // Obtener información completa del departamento
            $departamento = $this->departamentomodelo->obtenerDepartamentoCompleto($id_departamento);

            if (!$departamento) {
                header('Location: DepartamentoControlador.php?action=listarDepartamentos&error=Departamento+no+encontrado');
                exit;
            }

            include_once "../vista/DetallesDepartamentoVista.php";

        } catch (Exception $e) {
            error_log("Error en verDetallesDepartamento: " . $e->getMessage());
            header('Location: DepartamentoControlador.php?action=listarDepartamentos&error=Error+al+cargar+los+detalles+del+departamento');
            exit;
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

    private function redirigirEditarConExito($mensaje) {
        header('Location: ../controlador/DepartamentoControlador.php?action=listarDepartamentos&success=' . urlencode($mensaje));
        exit;
    }

    private function redirigirEditarConError($mensaje) {
        header('Location: ../controlador/DepartamentoControlador.php?action=listarDepartamentos&error=' . urlencode($mensaje));
        exit;
    }

    private function redirigirConExito($mensaje) {
        header('Location: ../controlador/DepartamentoControlador.php?action=formularioDepartamento&success=' . urlencode($mensaje));
        exit;
    }

    private function redirigirConError($mensaje) {
        header('Location: ../controlador/DepartamentoControlador.php?action=formularioDepartamento&error=' . urlencode($mensaje));
        exit;
    }
    // Agrega este método a tu DepartamentoControlador
    public function obtenerDetallesDepartamento() {
        try {
            $id_departamento = $_GET['id_departamento'] ?? null;

            if (!$id_departamento) {
                echo json_encode(['success' => false, 'error' => 'ID de departamento no especificado']);
                exit;
            }

            // Obtener información completa del departamento usando el método mejorado
            $departamento = $this->departamentomodelo->obtenerDepartamentoCompleto($id_departamento);

            if (!$departamento) {
                echo json_encode(['success' => false, 'error' => 'Departamento no encontrado']);
                exit;
            }

            // Preparar la respuesta JSON
            $response = [
                'success' => true,
                'departamento' => [
                    'id_departamento' => $departamento['id_departamento'],
                    'numero' => $departamento['numero'],
                    'piso' => $departamento['piso'],
                    'estado' => $departamento['estado'],
                    'residentes' => $departamento['residentes'] ?? [],
                    'medidores' => $departamento['medidores'] ?? []
                ]
            ];

            // Asegurarse de que los arrays estén presentes
            if (!isset($response['departamento']['residentes'])) {
                $response['departamento']['residentes'] = [];
            }
            if (!isset($response['departamento']['medidores'])) {
                $response['departamento']['medidores'] = [];
            }

            // Convertir a JSON y enviar respuesta
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;

        } catch (Exception $e) {
            error_log("Error en obtenerDetallesDepartamento: " . $e->getMessage());

            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'Error al cargar los detalles del departamento: ' . $e->getMessage()
            ]);
            exit;
        }
    }
}

// Manejo de rutas
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    include_once "../../config/database.php";
    include_once "../modelo/DepartamentoModelo.php";
    require_once "../modelo/PersonaModelo.php";
    require_once "../modelo/ServicioModelo.php";

    $database = new Database();
    $db = $database->getConnection();
    $controlador = new DepartamentoControlador($db);

    if(isset($_GET['action'])) {
        switch($_GET['action']) {
            case 'listarDepartamentos':
                $controlador->listarDepartamentos();
                break;
            case 'formularioDepartamento':
                $controlador->formularioDepartamento();
                break;
            case 'formularioAsignarPersonasDepartamento':
                $controlador->formularioAsignarPersonasDepartamento();
                break;
            case 'verDetallesDepartamento':
                $controlador->verDetallesDepartamento();
                break;
            case 'verHistorialConsumo':
                $controlador->verHistorialConsumo();
                break;
            case 'obtenerDetallesDepartamento':
                $controlador->obtenerDetallesDepartamento();
                break;
            default:
                header('Location: ../vista/DashboardVista.php?error=Acción no válida');
                exit;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    require_once '../../config/database.php';
    require_once '../modelo/DepartamentoModelo.php';
    require_once "../modelo/PersonaModelo.php";
    require_once "../modelo/ServicioModelo.php";

    $database = new Database();
    $db = $database->getConnection();
    $controlador = new DepartamentoControlador($db);

    switch($_POST['action']) {
        case 'registrarDepartamento':
            $controlador->registrarDepartamento();
            break;
        case 'editarDepartamento':
            $controlador->editarDepartamento();
            break;
        case 'asignarPersonasDepartamento':
            $controlador->asignarPersonasDepartamento();
            break;
        default:
            header('Location: ../vista/DashboardVista.php?error=Acción no válida');
            exit;
    }
}
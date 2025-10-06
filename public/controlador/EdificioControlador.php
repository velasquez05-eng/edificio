<?php
class EdificioControlador {
    private $edificioModelo;

    public function __construct($db) {
        $this->edificioModelo = new EdificioModelo($db);
    }

    public function mostrarVista($id_edificio) {
        $edificio = $this->edificioModelo->obtenerEdificio($id_edificio);
        
        if (!$edificio) {
            $edificio = [
                'nombre' => 'Torres del Parque',
                'direccion' => 'Avenida Principal 123, Ciudad Central'
            ];
        }
        
        include_once '../vista/EdificioVista.php';
    }

    public function obtenerDatos($id_edificio) {
        $edificio = $this->edificioModelo->obtenerEdificio($id_edificio);
        return $edificio ? true : false;
    }

    public function actualizar($id_edificio) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'] ?? '';
            $direccion = $_POST['direccion'] ?? '';
            
            if (!empty($nombre) && !empty($direccion)) {
                if ($this->edificioModelo->actualizarEdificio($id_edificio, $nombre, $direccion)) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Edificio actualizado exitosamente'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Error al actualizar el edificio'
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Nombre y dirección son requeridos'
                ]);
            }
        }
    }
}

// Procesar la solicitud
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    require_once '../../config/database.php';
    require_once '../modelo/EdificioModelo.php';
    
    $database = new Database();
    $db = $database->getConnection();
    $controlador = new EdificioControlador($db);
    
    if ($_GET['action'] === "mostrar" && isset($_GET['id_edificio'])) {
        $controlador->mostrarVista($_GET['id_edificio']);
    }
    exit;
}
?>
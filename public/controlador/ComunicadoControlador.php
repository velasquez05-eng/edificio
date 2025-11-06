<?php
// public/controlador/ComunicadoControlador.php

require_once '../modelo/ComunicadoModelo.php';

class ComunicadoControlador {
    private $comunicadoModelo;

    public function __construct($db) {
        $this->comunicadoModelo = new ComunicadoModelo($db);
    }

    public function handleRequest() {
        $action = isset($_GET['action']) ? $_GET['action'] : 'listarComunicados';

        switch ($action) {
            case 'formularioComunicado':
                $this->mostrarFormulario();
                break;
            case 'registrar':
                $this->registrarComunicado();
                break;
            case 'editar':
                $this->editarComunicado();
                break;
            case 'actualizar':
                $this->actualizarComunicado();
                break;
            case 'cambiarEstado':
                $this->cambiarEstado();
                break;
            case 'eliminar':
                $this->eliminarComunicado();
                break;
            case 'verComunicado':
                $this->verComunicado();
                break;
            case 'listarPublicados':
                $this->listarComunicadosPublicados();
                break;
            case 'listarEliminados':
                $this->listarComunicadosEliminados();
                break;
            case 'restaurar':
                $this->restaurarComunicado();
                break;
            default:
                $this->listarComunicados();
                break;
        }
    }

    private function mostrarFormulario() {
        $comunicado = null;
        if (isset($_GET['id'])) {
            $comunicado = $this->comunicadoModelo->obtenerPorId($_GET['id']);
        }
        include '../vista/RegistrarComunicadoVista.php';
    }

    private function registrarComunicado() {
        if ($_POST) {
            $data = [
                'id_persona' => $_SESSION['id_persona'],
                'titulo' => $_POST['titulo'],
                'contenido' => $_POST['contenido'],
                'fecha_expiracion' => $_POST['fecha_expiracion'],
                'prioridad' => $_POST['prioridad'],
                'estado' => $_POST['estado'],
                'tipo_audiencia' => $_POST['tipo_audiencia']
            ];

            $result = $this->comunicadoModelo->crear($data);

            if ($result) {
                header("Location: ComunicadoControlador.php?success=Comunicado registrado exitosamente");
            } else {
                header("Location: ComunicadoControlador.php?action=formularioComunicado&error=Error al registrar el comunicado");
            }
            exit();
        }
    }

    private function editarComunicado() {
        if (isset($_GET['id'])) {
            $comunicado = $this->comunicadoModelo->obtenerPorId($_GET['id']);
            if ($comunicado) {
                include '../vista/EditarComunicadoVista.php';
            } else {
                header("Location: ComunicadoControlador.php?error=Comunicado no encontrado");
            }
        }
    }

    private function actualizarComunicado() {
        if ($_POST && isset($_POST['id_comunicado'])) {
            $data = [
                'titulo' => $_POST['titulo'],
                'contenido' => $_POST['contenido'],
                'fecha_expiracion' => $_POST['fecha_expiracion'],
                'prioridad' => $_POST['prioridad'],
                'estado' => $_POST['estado'],
                'tipo_audiencia' => $_POST['tipo_audiencia']
            ];

            $result = $this->comunicadoModelo->actualizar($_POST['id_comunicado'], $data);

            if ($result) {
                header("Location: ComunicadoControlador.php?success=Comunicado actualizado exitosamente");
            } else {
                header("Location: ComunicadoControlador.php?action=editar&id=" . $_POST['id_comunicado'] . "&error=Error al actualizar el comunicado");
            }
            exit();
        }
    }

    private function cambiarEstado() {
        if (isset($_GET['id']) && isset($_GET['estado'])) {
            $result = $this->comunicadoModelo->cambiarEstado($_GET['id'], $_GET['estado']);

            if ($result) {
                header("Location: ComunicadoControlador.php?success=Estado del comunicado actualizado exitosamente");
            } else {
                header("Location: ComunicadoControlador.php?error=Error al cambiar el estado del comunicado");
            }
            exit();
        }
    }

    private function eliminarComunicado() {
        if (isset($_GET['id'])) {
            $result = $this->comunicadoModelo->eliminar($_GET['id']);

            if ($result) {
                header("Location: ComunicadoControlador.php?success=Comunicado eliminado exitosamente");
            } else {
                header("Location: ComunicadoControlador.php?error=Error al eliminar el comunicado");
            }
            exit();
        }
    }

    private function verComunicado() {
        if (isset($_GET['id'])) {
            $comunicado = $this->comunicadoModelo->obtenerPorId($_GET['id']);
            if ($comunicado) {
                include '../vista/VerComunicadoVista.php';
            } else {
                header("Location: ComunicadoControlador.php?error=Comunicado no encontrado");
            }
        }
    }

    private function listarComunicados() {
        $comunicados = $this->comunicadoModelo->listarComunicados();
        $estadisticas = $this->comunicadoModelo->obtenerEstadisticas();
        include '../vista/ListarComunicadosVista.php';
    }

    private function listarComunicadosPublicados() {
        $comunicados = $this->comunicadoModelo->listarComunicadosPublicados();
        include '../vista/ListarComunicadosPublicadosVista.php';
    }

    private function listarComunicadosEliminados() {
        $comunicados = $this->comunicadoModelo->listarComunicadosEliminados();
        include '../vista/ListarComunicadosEliminadosVista.php';
    }

    private function restaurarComunicado() {
        if (isset($_GET['id'])) {
            $result = $this->comunicadoModelo->restaurar($_GET['id']);

            if ($result) {
                header("Location: ComunicadoControlador.php?action=listarEliminados&success=Comunicado restaurado exitosamente");
            } else {
                header("Location: ComunicadoControlador.php?action=listarEliminados&error=Error al restaurar el comunicado");
            }
            exit();
        }
    }
}

// Ejecutar el controlador
session_start();
require_once '../../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    $controller = new ComunicadoControlador($db);
    $controller->handleRequest();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
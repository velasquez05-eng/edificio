<?php
require_once '../../config/database.php';
require_once '../modelo/DashboardModelo.php';

class DashboardControlador
{
    private $modelo;
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
        $this->modelo = new DashboardModelo($db);
    }
    public function mostrarDashboard()
    {
        include '../vista/DashboardVista.php';
    }

}


$database = new Database();
$db = $database->getConnection();

// Ejecutar controlador
if (isset($_GET['action']) && $_GET['action'] == 'mostrarDashboard') {
    $controlador = new DashboardControlador($db);
    $controlador->mostrarDashboard();
}
?>
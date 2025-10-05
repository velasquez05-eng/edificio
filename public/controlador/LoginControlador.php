<?php
session_start();

class LoginControlador {
    private $LoginModelo;

    public function __construct($db) {
        $this->LoginModelo = new LoginModelo($db);
    }

    public function login() {
        if ($_POST) {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $tipo_usuario = $_POST['tipo_usuario'] ?? 'Usuario'; 
            if (!empty($username) && !empty($password) && !empty($tipo_usuario)) {
                if ($tipo_usuario === 'Personal') {
                    $user = $this->LoginModelo->loginPersonal($username, $password);
                } else {
                    $user = $this->LoginModelo->login($username, $password);
                }
                    $redirect = '../vista/DashboardVista.php';
                if ($user) {
                    $_SESSION['id_usuario'] = $user['id_usuario'] ?? $user['id_personal'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['nombre'] = $user['nombre'] . ' ' . $user['appaterno']. ' ' . $user['apmaterno'];
                    $_SESSION['tipo_usuario'] = $tipo_usuario;

                    if ($tipo_usuario === 'Personal') {
                        $_SESSION['cargo'] = $user['cargo'];
                        $_SESSION['rol'] = $user['nombre_rol'];
                    }

                    header("Location: " . $redirect);
                    exit();
                } else {
                    $_SESSION['error'] = "Usuario o contraseña incorrectos";
                    header("Location: ../vista/LoginVista.php");
                    exit();
                }
            } else {
                $_SESSION['error'] = "Todos los campos son obligatorios";
                header("Location: ../vista/LoginVista.php");
                exit();
            }
        }
    }

    public function logout() {
        session_destroy();
        header("Location: ../vista/LoginVista.php");
        exit();
    }
}

// Procesar solicitud
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    require_once '../../config/database.php';
    require_once '../modelo/LoginModelo.php';
    
    $database = new Database();
    $db = $database->getConnection();
    
    $LoginControlador = new LoginControlador($db);
    
    if ($_POST['action'] === 'login') {
        $LoginControlador->login();
    } elseif ($_POST['action'] === 'logout') {
        $LoginControlador->logout();
    }
}
?>
<?php
session_start();
$_SESSION['id_persona']=1;
$_SESSION['id_rol']=1;
header("Location: public/controlador/DashboardControlador.php?action=mostrarDashboardPersonal");
exit();
?>
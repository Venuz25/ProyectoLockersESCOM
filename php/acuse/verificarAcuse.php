<?php
    session_start();
    include('../conexion.php');
    
    if (!isset($_SESSION['usuario'])) {
        header("Location: /ProyectoWeb/acuse.html");
        exit();
    }
?>


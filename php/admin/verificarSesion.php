<?php
    session_start();
    if (isset($_SESSION['usuario'])) {
        header("Location: /ProyectoWeb/asignacion.html");
        exit();
    } else {
        header("Location: /ProyectoWeb/admin.html");
        exit();
    }
?>
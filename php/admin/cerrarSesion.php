<?php
    //Archivo para cerrar la sesion del admin
    session_start();
    session_unset();
    session_destroy();
    header("Location: /ProyectoWeb/admin.html");
    exit();
?>
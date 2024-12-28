<?php
session_start();
include('../conexion.php');

// Verificar si el usuario ha iniciado sesión
if (isset($_SESSION['usuario'])) {
    // Verificar el tipo de solicitud
    if (isset($_SESSION['solicitud'])) {
        if ($_SESSION['solicitud'] === 'Primera vez') {
            header("Location: /ProyectoWeb/seguimiento.html");
            exit();
        } elseif ($_SESSION['solicitud'] === 'Renovación') {
            header("Location: /ProyectoWeb/pendientes.html");
            exit();
        } else {
            echo "Tipo de solicitud desconocido.";
        }
    } else {
        echo "No se encontró información sobre la solicitud.";
    }
} else {
    // Si no hay sesión iniciada, redirigir a la página de inicio de sesión
    header("Location: /ProyectoWeb/acuse.html");
    exit();
}
?>


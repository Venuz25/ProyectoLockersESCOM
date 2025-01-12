<?php
session_start();

header('Content-Type: application/json');

if (isset($_SESSION['usuario'])) {
    $nombreUsuario = $_SESSION['usuario'];
    $solicitud = isset($_SESSION['solicitud']) ? $_SESSION['solicitud'] : null;

    echo json_encode([
        'usuario' => $nombreUsuario,
        'solicitud' => $solicitud
    ]);
} else {
    echo json_encode([
        'error' => 'Usuario no autenticado'
    ]);
}
?>

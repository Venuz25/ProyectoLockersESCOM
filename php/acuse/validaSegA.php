<?php
    session_start();

    header('Content-Type: application/json');

    if (isset($_SESSION['usuario'])) {
        $nombreUsuario = $_SESSION['usuario'];
        $solicitud = isset($_SESSION['solicitud']) ? $_SESSION['solicitud'] : null;
        $boleta = isset($_SESSION['boleta']) ? $_SESSION['boleta'] : null;

        echo json_encode([
            'usuario' => $nombreUsuario,
            'solicitud' => $solicitud,
            'boleta' => $boleta
        ]);
    } else {
        echo json_encode([
            'error' => 'Usuario no autenticado'
        ]);
    }
?>

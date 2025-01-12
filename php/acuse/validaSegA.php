<?php
    session_start();

    header('Content-Type: application/json');

    if (isset($_SESSION['solicitud'])) {
        echo json_encode(['solicitud' => $_SESSION['solicitud']]);
    } else {
        echo json_encode(['error' => 'Estado de solicitud no encontrado']);
    }
?>

<?php
    session_start();
    if (isset($_SESSION['usuario']) && !empty($_SESSION['usuario'])) {
        http_response_code(200);
    } else {
        http_response_code(403);
        exit();
    }
?>
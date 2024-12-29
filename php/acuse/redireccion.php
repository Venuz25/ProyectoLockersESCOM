<?php
    session_start();
    include('verificarAcuse.php'); // Valida la sesión

    if (isset($_SESSION['solicitud']) && isset($_SESSION['estadosolicitud'])) {
        $solicitud = $_SESSION['solicitud'];
        $estadoSolicitud = $_SESSION['estadosolicitud'];
        
        $htmlFile = '';

        if ($solicitud === 'Primera vez') {
            switch ($estadoSolicitud) {
                case 'Aprobada':
                    $htmlFile = 'seguimiento.html';
                    break;
                case 'Pendiente':
                    $htmlFile = 'pendientes.html';
                    break;
                case 'Lista de espera':
                    $htmlFile = 'listaespera.html';
                    break;
                default:
                    echo "<script>alert('Estado de solicitud desconocido para Primera vez');</script>";
                    echo "<script>window.location.href = '/ProyectoWeb/acuse.html';</script>";
                    exit();
            }
        } elseif ($solicitud === 'Renovación') {
            switch ($estadoSolicitud) {
                case 'Aprobada':
                    include 'revisiontiempo.php';
                    if (verificarPlazo24Horas()) {
                        $htmlFile = 'seguimiento.html';
                    } else {
                        $htmlFile = 'pendientes.html';
                    }
                    break;
                case 'Pendiente':
                    $htmlFile = 'pendientes.html';
                    break;
                case 'Lista de espera':
                    $htmlFile = 'listaespera.html';
                    break;
                default:
                    echo "<script>alert('Estado de solicitud desconocido para Renovación');</script>";
                    echo "<script>window.location.href = '/ProyectoWeb/acuse.html';</script>";
                    exit();
            }
        } else {
            echo "<script>alert('Tipo de solicitud desconocido');</script>";
            echo "<script>window.location.href = '/ProyectoWeb/acuse.html';</script>";
            exit();
        }

        // Verificar si el archivo HTML existe y cargar su contenido
        if (file_exists($htmlFile)) {
            echo file_get_contents($htmlFile);
        } else {
            echo "<script>alert('El archivo HTML no existe');</script>";
            echo "<script>window.location.href = '/ProyectoWeb/acuse.html';</script>";
            exit();
        }
    } else {
        echo "No se encontró información sobre la solicitud.";
        exit();
    }
?>

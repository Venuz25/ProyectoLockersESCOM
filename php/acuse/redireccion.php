<?php
    session_start();
    include('verificarAcuse.php'); // Valida la sesión

    if (isset($_SESSION['solicitud']) && isset($_SESSION['estadosolicitud'])) {
        $solicitud = $_SESSION['solicitud'];
        $estadoSolicitud = $_SESSION['estadosolicitud'];
        $comprobante = $_SESSION['comprobante'];
        
        $htmlFile = '';

        if ($solicitud === 'Primera vez') {
            switch ($estadoSolicitud) {
                case 'Aprobada':
                    if($comprobante !== null){
                        $htmlFile = '/ProyectoWeb/exito.html';
                    }else{
                        $htmlFile = '/ProyectoWeb/seguimiento.html';
                    }
                    break;
                case 'Pendiente':
                    $htmlFile = '/ProyectoWeb/pendientes.html';
                    break;
                case 'Lista de espera':
                    $htmlFile = '/ProyectoWeb/listaespera.html';
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
                        if($comprobante !== null){
                            $htmlFile = '/ProyectoWeb/exito.html';
                        }else{
                            $htmlFile = '/ProyectoWeb/seguimiento.html';
                        }
                    } else {
                        if($comprobante !== null){
                            $htmlFile = '/ProyectoWeb/exito.html';
                        }else{
                            echo "<script>alert('Lo sentimos, tu tiempo de subida de documentos ha expirado. Se revisará tu solicitud conforme a los casilleros disponibles.');</script>";
                            echo "<script>window.location.href = 'cambioEstado.php';</script>";
                        }
                    }
                    break;
                case 'Pendiente':
                    $htmlFile = '/ProyectoWeb/pendientes.html';
                    break;
                case 'Lista de espera':
                    $htmlFile = '/ProyectoWeb/listaespera.html';
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

        // Redirigir directamente al archivo correspondiente
        header("Location: $htmlFile");
        exit();
    } else {
        echo "<script>alert('No se encontró información sobre la solicitud o no has iniciado sesión correctamente.');</script>";
        header("Location: /ProyectoWeb/acuse.html");
        exit();
    }
?>

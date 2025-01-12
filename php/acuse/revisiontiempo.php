<?php
    session_start();

    function verificarPlazo24Horas() {
        //$fechaRegistro = $_SESSION['fecharegistro'];
        $fechaRegistro = $_SESSION['faprobación'];
            

        // Convertir a marca de tiempo
        $timestampRegistro = strtotime($fechaRegistro);
        $timestampActual = time();
    
        // Calcular la diferencia
        return ($timestampActual - $timestampRegistro) <= 86400; // 24 horas en segundos
    }
?>


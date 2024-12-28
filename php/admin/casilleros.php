<?php
    include('../conexion.php');
    
    // Disponibilidad Casilleros
    $casilleros = "SELECT noCasillero, estado, altura FROM casilleros";
    $resultCasillero = $conn->query($casilleros);

    $lockers = [];
    if ($resultCasillero->num_rows > 0) {
        while ($rowCas = $resultCasillero->fetch_assoc()) {
            $lockers[] = $rowCas;
        }
    }
    echo json_encode($lockers);

    $conn->close();
?>

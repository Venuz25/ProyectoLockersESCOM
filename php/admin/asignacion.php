<?php
    $conn = new mysqli("localhost", "root", "", "lockers_db");

    if ($conn -> connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    //Disponibilidad Casilleros
    $casilleros = "SELECT noCasillero, estado FROM casilleros";
    $resultCasillero = $conn->query($casilleros);
    
    $lockers = [];
    if ($resultCasillero->num_rows > 0) {
        while($rowCas = $resultCasillero->fetch_assoc()) {
            $lockers[] = $rowCas;
        }
    }
    echo json_encode($lockers);
?>
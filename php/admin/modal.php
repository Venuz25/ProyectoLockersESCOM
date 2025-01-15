<?php
    //Archivo para recuperar los datos mostrados en el modal
    include('../conexion.php');
    
    $noCasillero = $_GET['noCasillero'];
    
    $stmt = $conn->prepare("SELECT c.noCasillero, c.altura, c.estado, a.boleta, a.nombre, a.primerAp, a.segundoAp 
                        FROM casilleros c
                        LEFT JOIN alumnos a ON c.boletaAsignada = a.boleta
                        WHERE c.noCasillero = ?");
    $stmt->bind_param("i", $noCasillero);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        echo json_encode($data);
    } else {
        echo json_encode(['error' => 'No se encontró información para este casillero.']);
    }
?>

<?php
    // Conexión a la base de datos
    include_once '../../conexion.php';

    $query = "
        SELECT 
            s.noBoleta, 
            CONCAT(a.nombre, ' ', a.primerAp, ' ', a.segundoAp) AS nombreCompleto 
        FROM solicitudes s
        JOIN alumnos a ON s.noBoleta = a.boleta
        WHERE s.estadoSolicitud != 'Aprobada'
        ORDER BY s.fechaRegistro
    ";

    $result = $conn->query($query);

    $boletas = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $boletas[] = $row;
        }
        echo json_encode(['success' => true, 'boletas' => $boletas]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se encontraron boletas disponibles']);
    }

    $conn->close();
?>

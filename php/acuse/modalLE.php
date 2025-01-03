<?php
    include('../conexion.php');

    $sql = "SELECT 
                a.boleta,
                a.nombre,
                a.primerAp,
                a.segundoAp,
                s.estadoSolicitud,
                s.fechaRegistro
            FROM 
                alumnos a
            INNER JOIN 
                solicitudes s ON a.boleta = s.noBoleta
            WHERE 
                s.estadoSolicitud = 'Lista de espera'
            ORDER BY 
                s.fechaRegistro ASC";

    $result = $conn->query($sql);

    $alumnos = [];
    while ($row = $result->fetch_assoc()) {
        $alumnos[] = $row;
    }

    echo json_encode($alumnos);

    $conn->close();
?>

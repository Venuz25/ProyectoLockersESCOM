<?php
    include('../conexion.php');

    $sql = "SELECT 
            a.boleta,
            CONCAT(a.nombre, ' ', a.primerAp, ' ', a.segundoAp) AS nombre,
            a.estatura,
            s.fechaRegistro,
            s.estadoSolicitud
        FROM 
            alumnos a
        INNER JOIN 
            solicitudes s ON a.boleta = s.noBoleta
        LEFT JOIN 
            casilleros c ON a.boleta = c.boletaAsignada
        WHERE 
            (s.estadoSolicitud = 'Pendiente' OR s.estadoSolicitud = 'Lista de espera') 
            AND c.boletaAsignada IS NULL
        ORDER BY 
            s.fechaRegistro ASC;
        ";

    $result = $conn->query($sql);
    $alumnos = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $alumnos[] = $row;
        }
    }

    echo json_encode($alumnos);
    $conn->close();
?>

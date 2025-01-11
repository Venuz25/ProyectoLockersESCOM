<?php
    //Archivo para recuperar los datos mostrados en la lista de alumnos al asignar un casillero
    include('../conexion.php');

    $noCasillero = isset($_GET['noCasillero']) ? (int)$_GET['noCasillero'] : 0;

    $sql = "SELECT 
                a.boleta,
                CONCAT(a.nombre, ' ', a.primerAp, ' ', a.segundoAp) AS nombre,
                a.estatura,
                a.solicitud,
                a.casilleroAnt,
                s.fechaRegistro,
                s.estadoSolicitud,
                CASE 
                    WHEN a.solicitud = 'Renovación' AND a.casilleroAnt = ? THEN 1
                    ELSE 2
                END AS prioridad
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
                prioridad ASC, 
                s.fechaRegistro ASC;";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $noCasillero);
    $stmt->execute();
    $result = $stmt->get_result();

    $alumnos = [];
    while ($row = $result->fetch_assoc()) {
        $alumnos[] = $row;
    }

    echo json_encode($alumnos);
    $stmt->close();
    $conn->close();
?>

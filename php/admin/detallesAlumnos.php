<?php
    include('../conexion.php');
    $boleta = $_GET['boleta'];

    $sql = "SELECT 
                a.boleta, 
                CONCAT(a.nombre, ' ', a.primerAp, ' ', a.segundoAp) AS nombreCompleto,
                a.estatura,
                a.solicitud,
                a.casilleroAnt,
                a.telefono,
                a.correo,
                a.credencial,
                a.horario,
                s.comprobantePago
            FROM alumnos a
            INNER JOIN solicitudes s ON a.boleta = s.noBoleta
            WHERE a.boleta = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $boleta);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $alumno = $result->fetch_assoc();
        echo json_encode($alumno);
    } else {
        echo json_encode(['error' => 'Alumno no encontrado.']);
    }

    $stmt->close();
    $conn->close();
?>

<?php
    //Archivo para recuperar datos del alumno para el modal de detalles
    include('../conexion.php');
    $boleta = $_GET['boleta'];

    $sql = "SELECT 
                a.boleta, 
                CONCAT(a.nombre, ' ', a.primerAp, ' ', a.segundoAp) AS nombre,
                a.estatura,
                a.solicitud,
                a.casilleroAnt,
                a.telefono,
                a.correo,
                a.curp,
                a.credencial,
                a.horario,
                s.fechaRegistro,
                s.estadoSolicitud,
                s.comprobantePago
            FROM alumnos a
            INNER JOIN solicitudes s ON a.boleta = s.noBoleta
            WHERE a.boleta = ?";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode(['error' => 'Error en la preparación de la consulta.']);
        exit();
    }

    $stmt->bind_param("s", $boleta);
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

<?php
    //Archivo para asignar el casillero
    include('../conexion.php');
    
    $data = json_decode(file_get_contents('php://input'), true);
    $boleta = $data['boleta'];
    $noCasillero = $data['noCasillero'];

    // Verificar si el casillero ya está asignado
    $sql = "SELECT estado FROM casilleros WHERE noCasillero = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $noCasillero);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['estado'] !== 'Disponible') {
            echo json_encode(['success' => false, 'message' => 'El casillero ya está asignado']);
            $stmt->close();
            $conn->close();
            exit();
        }
    }

    $sql = "UPDATE casilleros SET estado = 'Asignado', boletaAsignada = ? WHERE noCasillero = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $boleta, $noCasillero);

    if ($stmt->execute()) {
        $fechaAprobacion = date("Y-m-d H:i:s");
        $sql = "UPDATE solicitudes SET estadoSolicitud = 'Aprobada',fechaAprobacion = ? WHERE noBoleta = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $fechaAprobacion, $boleta);
        $stmt->execute();

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }

    $stmt->close();
    $conn->close();
?>
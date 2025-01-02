<?php
    include('../conexion.php');

    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['noCasillero'])) {
        $noCasillero = $data['noCasillero'];

        // Obtener el número de boleta asignado al casillero
        $sqlBoleta = "SELECT boletaAsignada FROM casilleros WHERE noCasillero = ?";
        $stmtBoleta = $conn->prepare($sqlBoleta);
        $stmtBoleta->bind_param("i", $noCasillero);
        $stmtBoleta->execute();
        $stmtBoleta->bind_result($boletaAsignada);
        $stmtBoleta->fetch();
        $stmtBoleta->close();

        // Si hay un boleta asignada, actualizar la solicitud
        if ($boletaAsignada) {
            // Actualizar estado de la solicitud a 'Pendiente'
            $sqlSolicitud = "UPDATE solicitudes SET estadoSolicitud = 'Pendiente' WHERE noBoleta = ?";
            $stmtSolicitud = $conn->prepare($sqlSolicitud);
            $stmtSolicitud->bind_param("i", $boletaAsignada);

            if (!$stmtSolicitud->execute()) {
                echo json_encode(['success' => false, 'error' => 'No se pudo actualizar la solicitud.']);
                $stmtSolicitud->close();
                $conn->close();
                exit();
            }
            $stmtSolicitud->close();
        }

        // Revocar casillero: actualizar casillero a 'Disponible'
        $sqlCasillero = "UPDATE casilleros SET boletaAsignada = NULL, estado = 'Disponible' WHERE noCasillero = ?";
        $stmtCasillero = $conn->prepare($sqlCasillero);
        $stmtCasillero->bind_param("i", $noCasillero);

        if ($stmtCasillero->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'No se pudo revocar el casillero.']);
        }

        $stmtCasillero->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'Datos inválidos.']);
    }

    $conn->close();
?>

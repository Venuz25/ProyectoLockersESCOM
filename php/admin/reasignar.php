<?php
    header('Content-Type: application/json');

    $conn = new mysqli("localhost", "root", "", "lockers_db");

    if ($conn->connect_error) {
        echo json_encode(['success' => false, 'error' => 'Error en la conexión a la base de datos.']);
        exit();
    }

    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['noCasillero'])) {
        $noCasillero = $data['noCasillero'];

        $sql = "UPDATE casilleros SET boletaAsignada = NULL, estado = 'Disponible' WHERE noCasillero = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $noCasillero);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'No se pudo actualizar el casillero.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'Datos inválidos.']);
    }

    $conn->close();
?>

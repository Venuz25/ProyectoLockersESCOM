<?php
    include_once '../../conexion.php';

    $table = isset($_GET['table']) ? $_GET['table'] : '';
    $data = [];

    if (!$table) {
        echo json_encode(['success' => false, 'message' => 'No se ha proporcionado una tabla']);
        exit;
    }

    // Validación estricta de las tablas permitidas
    $allowedTables = ['administradores', 'alumnos', 'casilleros', 'solicitudes'];
    if (!in_array($table, $allowedTables)) {
        echo json_encode(['success' => false, 'message' => 'Tabla no válida']);
        exit;
    }

    switch ($table) {
        case 'administradores':
            $sql = "SELECT id, usuario, contrasena FROM administradores";
            break;
        case 'alumnos':
            $sql = "SELECT boleta, solicitud, nombre, primerAp, segundoAp, telefono, correo, curp, estatura, credencial, horario, usuario, contrasena FROM alumnos";
            break;
        case 'casilleros':
            $sql = "SELECT noCasillero, altura, estado, boletaAsignada FROM casilleros";
            break;
        case 'solicitudes':
            $sql = "SELECT id, noBoleta, fechaRegistro, estadoSolicitud, comprobantePago FROM solicitudes";
            break;
    }

    try {
        $result = $conn->query($sql);
        if ($result === false) {
            throw new Exception("Error al ejecutar la consulta SQL");
        }

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            echo json_encode($data);
        } else {
            echo json_encode([]);
        }

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }

    $conn->close();
?>

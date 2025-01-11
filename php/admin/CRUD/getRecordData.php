<?php
    include_once '../../conexion.php';

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $recordId = isset($_GET['recordId']) ? intval($_GET['recordId']) : null;
        $table = isset($_GET['table']) ? $_GET['table'] : null;

        if (!$recordId || !$table) {
            echo json_encode(['success' => false, 'message' => 'Faltan parámetros necesarios.']);
            exit;
        }

        try {
            $query = '';
            switch ($table) {
                case 'administradores':
                    $query = "SELECT * FROM administradores WHERE id = ?";
                    break;
                case 'alumnos':
                    $query = "SELECT * FROM alumnos WHERE boleta = ?";
                    break;
                case 'casilleros':
                    $query = "SELECT * FROM casilleros WHERE noCasillero = ?";
                    break;
                case 'solicitudes':
                    $query = "SELECT * FROM solicitudes WHERE id = ?";
                    break;
                default:
                    echo json_encode(['success' => false, 'message' => 'Tabla no reconocida.']);
                    exit;
            }

            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $recordId);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();

            if ($data) {
                echo json_encode(['success' => true, 'obtData' => $data]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Registro no encontrado.']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    }
?>

<?php
    include_once '../../conexion.php';

    $data = json_decode(file_get_contents('php://input'), true);
    $query = $data['query'];

    $response = [];

    if (!$query) {
        echo json_encode(['success' => false, 'message' => 'No query provided']);
        exit;
    }

    try {
        if ($conn->query($query) === TRUE) {
            $response['success'] = true;
            $response['message'] = 'Consulta ejecutada correctamente';
        } else {
            $response['success'] = false;
            $response['message'] = 'Error al ejecutar la consulta: ' . $conn->error;
        }
    } catch (Exception $e) {
        $response['success'] = false;
        $response['message'] = 'Error: ' . $e->getMessage();
    }

    $conn->close();
    echo json_encode($response);
?>

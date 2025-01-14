<?php
session_start();

// Incluir conexión a la base de datos
include_once '../conexion.php';

$enTransaccion = false;

try {
    // Validar que se haya enviado un archivo y que sea un POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido.');
    }

    if (!isset($_FILES['comprobante']) || $_FILES['comprobante']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No se subió un archivo válido.');
    }

    // Obtener la boleta desde la sesión
    if (!isset($_SESSION['boleta'])) {
        throw new Exception('No se encontró la boleta en la sesión.');
    }

    $boleta = $_SESSION['boleta'];
    $archivo = $_FILES['comprobante'];

    // Ruta para guardar el archivo en el servidor
    $nombreArchivo = $archivo['name']; // Conservar el nombre original del archivo
    $rutaDestino = $_SERVER['DOCUMENT_ROOT'] . '/ProyectoWeb/Docs/Comprobantes/';

    // Mover el archivo al servidor
    if (!move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
        throw new Exception('No se pudo guardar el archivo en el servidor.');
    }

    // Generar ruta relativa para guardar en la base de datos
    $rutaRelativa = '/ProyectoWeb/Docs/Comprobantes/'.$nombreArchivo;

    // Actualizar BD
    $conn->begin_transaction();
    $enTransaccion = true;

    $sql = "UPDATE solicitudes SET comprobantePago = ? WHERE boleta = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $rutaRelativa, $boleta);

    if (!$stmt->execute()) {
        throw new Exception('Error al actualizar los datos en la base de datos.');
    }

    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'El comprobante fue actualizado exitosamente.',
    ]);
} catch (Exception $e) {
    // Deshacer cambios en caso de error
    if ($enTransaccion) {
        $conn->rollback();
    }

    // Responder con el error
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    // Cerrar la conexión
    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
}
?>

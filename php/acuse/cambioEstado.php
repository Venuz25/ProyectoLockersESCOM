<?php
    session_start();
    include('../conexion.php');

    $enTransaccion = false;
    try{

        //Verificaciones
        if (!$conn) {
            die('Error: No se pudo conectar a la base de datos.');
        }
        if (!isset($_SESSION['boleta'])) {
            throw new Exception('No se encontró la boleta en la sesión.');
        }
        
        $boleta = $_SESSION['boleta'];
        $estatus = "Pendiente";
        
        $conn->begin_transaction();
        $enTransaccion = true;
        
        $sql = "UPDATE solicitudes SET estadoSolicitud = ? WHERE noBoleta = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $estatus, $boleta);
        
        if (!$stmt->execute()) {
            throw new Exception('Error al actualizar los datos en la base de datos.');
        }
        
        $conn->commit();
        
        // Redirección al completar exitosamente
        header("Location: /ProyectoWeb/pendientes.html");
        exit();
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
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
        
        $sql = "UPDATE solicitudes SET estadoSolicitud = ?, fechaAprobacion = null WHERE noBoleta = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $estatus, $boleta);
        if (!$stmt->execute()) {
            throw new Exception('Error al actualizar los datos en la base de datos.');
        }

        // Revocar el casillero anterior
        $stmtLiberarCasillero = $conn->prepare("UPDATE casilleros SET estado = 'Disponible', boletaAsignada = NULL WHERE boletaAsignada = ?");
        $stmtLiberarCasillero->bind_param("s", $boleta);
        if (!$stmtLiberarCasillero->execute()) {
            echo json_encode(['success' => false, 'message' => 'Error al liberar el casillero actual.']);
            exit;
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
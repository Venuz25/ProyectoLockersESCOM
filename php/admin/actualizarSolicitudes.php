<?php
    //Archivo para cambiar el estado de las solicitudes en caso de que todos los casilleros este llenos
    include_once '../conexion.php';

    try {
        // Verificar si todos los casilleros están asignados
        $sqlCheckCasilleros = "SELECT COUNT(*) AS totalCasilleros, 
                                    SUM(CASE WHEN estado = 'Asignado' THEN 1 ELSE 0 END) AS asignados 
                            FROM casilleros";
        $result = $conn->query($sqlCheckCasilleros);
        $row = $result->fetch_assoc();

        if ($row['totalCasilleros'] == $row['asignados']) {
            $sqlUpdateSolicitudes = "UPDATE solicitudes SET estadoSolicitud = 'Lista de espera' WHERE estadoSolicitud = 'Pendiente'";
            if ($conn->query($sqlUpdateSolicitudes) === FALSE) {
                throw new Exception("Error al actualizar solicitudes: " . $conn->error);
            }
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }

    $conn->close();
?>

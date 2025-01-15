<?php
    //Archivo para revocar todos los casilleros
    include_once '../conexion.php';

    $conn->begin_transaction();
    try {
        // Actualizar las solicitudes asociadas a esos casilleros a 'Pendiente'
        $sqlSolicitudes = "UPDATE solicitudes 
                        SET estadoSolicitud = 'Pendiente', fechaAprobacion = NULL, comprobantePago = null
                        WHERE noBoleta IN (
                            SELECT boletaAsignada 
                            FROM casilleros
                            WHERE estado = 'Asignado'
                        )";
        if ($conn->query($sqlSolicitudes) === FALSE) {
            throw new Exception("Error al actualizar las solicitudes: " . $conn->error);
        }

        // Actualizar todos los casilleros a 'Disponible'
        $sqlCasilleros = "UPDATE casilleros SET estado = 'Disponible', boletaAsignada = NULL";
        if ($conn->query($sqlCasilleros) === FALSE) {
            throw new Exception("Error al actualizar los casilleros: " . $conn->error);
        }

        $conn->commit();
        // Mensaje de éxito
        echo "<script>
                alert('Los casilleros se restablecieron correctamente.');
                window.location.href = '/ProyectoWeb/asignacion.html';
              </script>";
    } catch (Exception $e) {
        $conn->rollback();
        // Mensaje de error
        echo "<script>
                alert('Error al restablecer: " . $e->getMessage() . "');
                window.history.back();
              </script>";
    }
    $conn->close();
?>

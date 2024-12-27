<?php
include 'conexion.php';

$query = "SELECT boleta, nombre, tipoSolicitud, altura, telefono, correo, curp, estado, fecha FROM Solicitudes WHERE estado IN ('Pendiente', 'Lista de espera') ORDER BY altura, fecha, estado";
$result = $conn->query($query);

$solicitudes = [];
while ($row = $result->fetch_assoc()) {
    $solicitudes[] = $row;
}

echo json_encode($solicitudes);
?>
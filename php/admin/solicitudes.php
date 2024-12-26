<?php
$conn = new mysqli("localhost", "root", "", "lockers_db");

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$sql = "SELECT 
            alumnos.boleta, 
            CONCAT(alumnos.nombre, ' ', alumnos.primerAp, ' ', alumnos.segundoAp) AS nombre, 
            alumnos.solicitud,
            alumnos.estatura, 
            solicitudes.estadoSolicitud 
        FROM solicitudes 
        JOIN alumnos ON solicitudes.noBoleta = alumnos.boleta 
        WHERE solicitudes.estadoSolicitud IN ('Pendiente', 'Lista de Espera')";

$result = $conn->query($sql);
$solicitudes = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $solicitudes[] = $row;
    }
}

echo json_encode($solicitudes);

$conn->close();
?>

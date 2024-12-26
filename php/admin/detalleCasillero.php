<?php
include 'conexion.php';

$noCasillero = $_GET['noCasillero'];
$query = "SELECT a.nombre, a.boleta FROM Alumnos a JOIN Casilleros c ON a.boleta = c.boletaAsignada WHERE c.noCasillero = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $noCasillero);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

echo json_encode($data);
?>
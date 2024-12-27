<?php
    $conn = new mysqli("localhost:3309", "root", "", "lockers_db");

    if ($conn -> connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }
    
    $usuario = $_POST['Usuario'];
    $contrasena = $_POST['Contraseña'];
    
    $sql = "SELECT * FROM administradores WHERE usuario = ? AND contrasena = ?";
    $stmt = $conn -> prepare($sql);
    $stmt -> bind_param("ss", $usuario, $contrasena);
    $stmt -> execute();
    $result = $stmt -> get_result();
    
    if ($result->num_rows > 0) {
        header("Location: /ProyectoWeb/asignacion.html");
        exit();
    } else {
        echo "<script>alert('Usuario o contraseña incorrectos');</script>";
        echo "<script>window.location.href = '/ProyectoWeb/admin.html';</script>";
    }
    
    $stmt -> close();
    $conn -> close();
?>
<?php
session_start();

$conn = new mysqli("localhost", "root", "", "lockers_db");

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Validar si se recibieron los datos del formulario
if (!empty($_POST['Usuario']) && !empty($_POST['Correo']) && !empty($_POST['Contraseña'])) {
    $usuario = $_POST['Usuario'];
    $correo = $_POST['Correo'];
    $contrasena = $_POST['Contraseña'];

    // Consulta SQL preparada
    $sql = "SELECT boleta, solicitud FROM alumnos WHERE usuario = ? AND correo = ? AND contrasena = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $usuario, $correo, $contrasena);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar si existe el usuario con las credenciales proporcionadas
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Guardar en la sesión
        $_SESSION['usuario'] = $usuario;
        $_SESSION['boleta'] = $row['boleta'];
        $_SESSION['solicitud'] = $row['solicitud'];

        // Redirigir según el tipo de solicitud
        if ($row['solicitud'] === 'Primera vez') {
            header("Location: /ProyectoWeb/seguimiento.php");
        } else if ($row['solicitud'] === 'Renovación') {
            header("Location: /ProyectoWeb/pendientes.php");
        } else {
            echo "<script>alert('Tipo de solicitud desconocido');</script>";
            echo "<script>window.location.href = '/ProyectoWeb/acuse.html';</script>";
        }
        exit();
    } else {
        echo "<script>alert('Usuario, correo o contraseña incorrectos');</script>";
        echo "<script>window.location.href = '/ProyectoWeb/acuse.html';</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('Por favor, complete todos los campos');</script>";
    echo "<script>window.location.href = '/ProyectoWeb/acuse.html';</script>";
}

$conn->close();
?>

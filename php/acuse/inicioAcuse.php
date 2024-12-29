<?php
    session_start();
    include('../conexion.php');

    // Validar si se recibieron los datos del formulario
    if (!empty($_POST['Usuario']) && !empty($_POST['Correo']) && !empty($_POST['Contraseña'])) {
        $usuario = $_POST['Usuario'];
        $correo = $_POST['Correo'];
        $contrasena = $_POST['Contraseña'];

        // Consulta SQL
        $sql = "SELECT a.boleta, a.solicitud, s.estadoSolicitud, s.fechaRegistro, s.comprobantePago FROM alumnos a
                INNER JOIN solicitudes s ON a.boleta = s.noBoleta WHERE a.usuario = ? AND a.correo = ? AND a.contrasena = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $usuario, $correo, $contrasena);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Guardar en la sesión
            $_SESSION['usuario'] = $usuario;
            $_SESSION['boleta'] = $row['boleta'];
            $_SESSION['solicitud'] = $row['solicitud'];
            $_SESSION['estadosolicitud'] = $row['estadoSolicitud'];
            $_SESSION['fecharegistro'] = $row['fechaRegistro'];
            $_SESSION['comprobante'] = $row['comprobantePago'];

            // Redirigir al script de manejo de redirecciones
            header("Location: redireccion.php");
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

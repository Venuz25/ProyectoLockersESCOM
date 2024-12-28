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

        if ($row['solicitud'] === 'Primera vez') {
            switch ($row['estadoSolicitud']) {
                case 'Aprobada':
                    header("Location: /ProyectoWeb/seguimiento.php");
                    break;
                case 'Pendiente':
                    header("Location: /ProyectoWeb/pendientes.php");
                    break;
                case 'Lista de espera':
                    header("Location: /ProyectoWeb/listaespera.php");
                    break;
                default:
                    echo "<script>alert('Estado de solicitud desconocido para Primera vez');</script>";
                    echo "<script>window.location.href = '/ProyectoWeb/acuse.html';</script>";
            }
        } elseif ($row['solicitud'] === 'Renovación') {
            switch ($row['estadoSolicitud']) {
                case 'Aprobada':
                    // Verificar si el plazo de 24 horas sigue vigente
                    include 'revisiontiempo.php'; // Asegúrate de que esta ruta es correcta
                    if (verificarPlazo24Horas()) { // Se llama la función de revisiontiempo.php
                        header("Location: /ProyectoWeb/seguimiento.php");
                        exit(); // Detener ejecución después de la redirección
                    } else {
                        echo "<script>alert('El plazo de 24 horas ha expirado. No puedes acceder a seguimiento.');</script>";
                        header("Location: /ProyectoWeb/pendientes.php");
                        exit();
                    }
                    break;
                case 'Pendiente':
                    header("Location: /ProyectoWeb/pendientes.php");
                    break;
                case 'Lista de espera':
                    header("Location: /ProyectoWeb/listaespera.php");
                    break;
                default:
                    echo "<script>alert('Estado de solicitud desconocido para Renovación');</script>";
                    echo "<script>window.location.href = '/ProyectoWeb/acuse.html';</script>";
            }
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

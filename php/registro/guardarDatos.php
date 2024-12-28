<?php
    include_once '../conexion.php';

    // Validar entradas del formulario
    $solicitud = $_POST['tipo_solicitud'];
    $casilleroAnt = $solicitud == 'Renovación' ? $conn->real_escape_string($_POST['casilleroAnt']) : null;
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $primerAp = $conn->real_escape_string($_POST['primerAp']);
    $segundoAp = $conn->real_escape_string($_POST['segundoAp']);
    $telefono = $conn->real_escape_string($_POST['telefono']);
    $correo = filter_var($_POST['correo'], FILTER_VALIDATE_EMAIL);
    $curp = $conn->real_escape_string($_POST['curp']);
    $estatura = (float)$_POST['estatura'];
    $usuario = $conn->real_escape_string($_POST['usuario']);
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
    $boleta = $conn->real_escape_string($_POST['boleta']);

    // Validar y subir archivos
    $credencial = "/ProyectoWeb/Docs/Credenciales/" . uniqid() . "-" . basename($_FILES["credencial"]["name"]);
    $horario = "/ProyectoWeb/Docs/Horarios/" . uniqid() . "-" . basename($_FILES["horario"]["name"]);

    if (!move_uploaded_file($_FILES["credencial"]["tmp_name"], $_SERVER['DOCUMENT_ROOT'] . $credencial) ||
        !move_uploaded_file($_FILES["horario"]["tmp_name"], $_SERVER['DOCUMENT_ROOT'] . $horario)) {
        die("Error al subir los archivos.");
    }

    $fechaRegistro = date("Y-m-d H:i:s");

    // Procesar solicitud
    if ($solicitud === "Renovación" && $casilleroAnt) {
        $sql_casillero = "SELECT estado FROM Casilleros WHERE noCasillero = '$casilleroAnt'";
        $result = $conn->query($sql_casillero);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($row['estado'] === 'Disponible') {
                $conn->query("INSERT INTO Solicitudes (noBoleta, fechaRegistro, estadoSolicitud) VALUES ('$boleta', '$fechaRegistro', 'Aprobada')");
                $conn->query("INSERT INTO Alumnos (...) VALUES (...)");
                $conn->query("UPDATE Casilleros SET boletaAsignada = '$boleta', estado = 'Asignado' WHERE noCasillero = '$casilleroAnt'");
                echo "Solicitud registrada con éxito.";
            } else {
                echo "El casillero solicitado ya está en uso.";
            }
        } else {
            echo "El casillero no existe.";
        }
    } elseif ($solicitud === "Primera vez") {
        $conn->query("INSERT INTO Solicitudes (noBoleta, fechaRegistro, estadoSolicitud) VALUES ('$boleta', '$fechaRegistro', 'Pendiente')");
        $conn->query("INSERT INTO Alumnos (...) VALUES (...)");
        echo "Solicitud registrada con éxito.";
    } else {
        echo "Error al registrar la solicitud.";
    }

    $conn->close();
?>

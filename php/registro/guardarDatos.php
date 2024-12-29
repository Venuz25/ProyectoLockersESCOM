<?php
    include_once '../conexion.php';

    //Datos del formulario
    $tipoSolicitud = $_POST['tipo_solicitud'];
    $casilleroAnt = $_POST['numero-casillero'];
    $nombre = $_POST['nombre'];
    $primerApellido = $_POST['p_apellido'];
    $segundoApellido = $_POST['s_apellido'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];
    $curp = $_POST['curp'];
    $estatura = $_POST['estatura'];
    $credencial = $_FILES['credencial']['name'];
    $horario = $_FILES['horario']['name'];
    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contraseña'];
    $boleta = $_POST['boleta'];

    $credencialPath = '/ProyectoWeb/Docs/Credenciales/' . $credencial;
    $horarioPath = '/ProyectoWeb/Docs/Horarios/' . $horario;

    // Verificar si la solicitud es de tipo "Renovación"
    if ($tipoSolicitud == "Renovación") {
        $sqlCasillero = "SELECT estado FROM casilleros WHERE noCasillero = '$casilleroAnt'";
        $result = $conn->query($sqlCasillero);
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($row['estado'] == 'Disponible') {
                // Subir los archivos
                move_uploaded_file($_FILES['credencial']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $credencialPath);
                move_uploaded_file($_FILES['horario']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $horarioPath);

                // Insertar datos en la tabla Alumnos
                $sqlAlumno = "INSERT INTO alumnos (boleta, solicitud, casilleroAnt, nombre, primerAp, segundoAp, telefono, correo, curp, estatura, credencial, horario, usuario, contrasena)
                    VALUES ('$boleta', '$tipoSolicitud', '$casilleroAnt', '$nombre', '$primerApellido', '$segundoApellido', '$telefono', '$correo', '$curp', '$estatura', '$credencialPath', '$horarioPath', '$usuario', '$contrasena')";

                if ($conn->query($sqlAlumno) === TRUE) { echo "Nuevo registro de alumno creado exitosamente."; } 
                else { echo "Error: " . $sqlAlumno . "<br>" . $conn->error; }

                // Insertar datos en la tabla Solicitudes
                $fechaRegistro = date("Y-m-d H:i:s");
                $sqlSolicitud = "INSERT INTO solicitudes (noBoleta, fechaRegistro, estadoSolicitud, comprobantePago)
                    VALUES ('$boleta', '$fechaRegistro', 'Aprobada', NULL)";
                    
                if ($conn->query($sqlSolicitud) === TRUE) { echo "La solicitud se registro correctamente."; } 
                else { echo "Error: " . $sqlSolicitud . "<br>" . $conn->error; }

                // Actualizar el casillero
                $sqlActualizarCasillero = "UPDATE casilleros SET estado = 'Asignado', boletaAsignada = '$boleta' WHERE noCasillero = '$casilleroAnt'";

                if ($conn->query($sqlActualizarCasillero) === TRUE) { echo "Casillero actualizado exitosamente.";} 
                else { echo "Error al actualizar el casillero: " . $conn->error;}

            } else { echo "<script>alert('El casillero solicitado no está disponible.'); window.history.back();</script>";
                exit(); }

        } else { echo "<script>alert('El casillero solicitado no existe.'); window.history.back();</script>"; exit();
        }
    } 
    // Caso de solicitud de tipo "Primera vez"
    else if ($tipoSolicitud == "Primera vez") {
        // Subir los archivos
        move_uploaded_file($_FILES['credencial']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $credencialPath);
        move_uploaded_file($_FILES['horario']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $horarioPath);

        // Insertar datos en la tabla Alumnos
        $sqlAlumno = "INSERT INTO alumnos (boleta, solicitud, casilleroAnt, nombre, primerAp, segundoAp, telefono, correo, curp, estatura, credencial, horario, usuario, contrasena)
            VALUES ('$boleta', '$tipoSolicitud', NUll , '$nombre', '$primerApellido', '$segundoApellido', '$telefono', '$correo', '$curp', '$estatura', '$credencialPath', '$horarioPath', '$usuario', '$contrasena')";


        if ($conn->query($sqlAlumno) === TRUE) { echo "Nuevo registro de alumno creado exitosamente."; } 
        else { echo "Error: " . $sqlAlumno . "<br>" . $conn->error; }

        // Insertar datos en la tabla Solicitudes
        $fechaRegistro = date("Y-m-d H:i:s");
        $sqlSolicitud = "INSERT INTO Solicitudes (noBoleta, fechaRegistro, estadoSolicitud, comprobantePago)
            VALUES ('$boleta', '$fechaRegistro', 'Pendiente', NULL)";

        if ($conn->query($sqlSolicitud) === TRUE) { echo "Solicitud registrada exitosamente."; } 
        else { echo "Error: " . $sqlSolicitud . "<br>" . $conn->error; }
    } else {
        echo "<script>alert('Error al registrar la solicitud.'); window.history.back();</script>";
        exit();
    }

    echo "<script>window.location.href = '/ProyectoWeb/confirmacion.html';</script>";

    $conn->close();
?>
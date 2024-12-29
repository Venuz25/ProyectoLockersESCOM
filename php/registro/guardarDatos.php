<?php
    include_once '../conexion.php';

    //Datos del formulario
    $tipoSolicitud = $_POST['tipo_solicitud'];
    $casilleroAnt = isset($_POST['numero-casillero']) ? $_POST['numero-casillero'] : NULL;
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
    if ($tipoSolicitud == "Renovación" && $casilleroAnt) {
        $sqlCasillero = "SELECT estado FROM Casilleros WHERE numero = '$casilleroAnt'";
        $result = $conn->query($sqlCasillero);
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($row['estado'] == 'Disponible') {
                // Subir los archivos
                move_uploaded_file($_FILES['credencial']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $credencialPath);
                move_uploaded_file($_FILES['horario']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $horarioPath);

                // Insertar datos en la tabla Alumnos
                $sqlAlumno = "INSERT INTO Alumnos (tipo_solicitud, casilleroAnt, nombre, primerAp, segundoAp, telefono, correo, curp, estatura, credencial, horario, usuario, contrasena)
                            VALUES ('$tipoSolicitud', '$casilleroAnt', '$nombre', '$primerApellido', '$segundoApellido', '$telefono', '$correo', '$curp', '$estatura', '$credencialPath', '$horarioPath', '$usuario', '$contrasena')";

                if ($conn->query($sqlAlumno) === TRUE) {
                    echo "Nuevo registro de alumno creado exitosamente.";
                } else {
                    echo "Error: " . $sqlAlumno . "<br>" . $conn->error;
                }

                // Insertar datos en la tabla Solicitudes
                $fechaRegistro = date("Y-m-d H:i:s");
                $sqlSolicitud = "INSERT INTO Solicitudes (noBoleta, fechaRegistro, estadoSolicitud, comprobantePago)
                                VALUES ('$boleta', '$fechaRegistro', 'Aprobado', NULL)";
                if ($conn->query($sqlSolicitud) === TRUE) {
                    echo "<script>alert('La solicitud se registro correctamente.'); window.history.back();</script>";
                } else {
                    echo "Error: " . $sqlSolicitud . "<br>" . $conn->error;
                }

                // Actualizar el casillero
                $sqlActualizarCasillero = "UPDATE Casilleros SET estado = 'Asignado', boletaAsignada = '$boleta' WHERE noCasillero = '$casilleroAnt'";
                if ($conn->query($sqlActualizarCasillero) === TRUE) {
                    echo "Casillero actualizado exitosamente.";
                } else {
                    echo "Error al actualizar el casillero: " . $conn->error;
                }
            } else {
                echo "<script>alert('El casillero solicitado ya está en uso.'); window.history.back();</script>";
                exit();
            }
        } else {
            echo "<script>alert('El casillero no existe.'); window.history.back();</script>";
            exit();
        }
    } 
    // Caso de solicitud de tipo "Primera vez"
    else if ($tipoSolicitud == "Primera vez") {
        // Subir los archivos
        move_uploaded_file($_FILES['credencial']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $credencialPath);
        move_uploaded_file($_FILES['horario']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $horarioPath);

        // Insertar datos en la tabla Alumnos
        $sqlAlumno = "INSERT INTO Alumnos (tipo_solicitud, casilleroAnt, nombre, primerAp, segundoAp, telefono, correo, curp, estatura, credencial, horario, usuario, contrasena)
                    VALUES ('$tipoSolicitud', NULL, '$nombre', '$primerApellido', '$segundoApellido', '$telefono', '$correo', '$curp', '$estatura', '$credencialPath', '$horarioPath', '$usuario', '$contrasena')";

        if ($conn->query($sqlAlumno) === TRUE) {
            echo "Nuevo registro de alumno creado exitosamente.";
        } else {
            echo "Error: " . $sqlAlumno . "<br>" . $conn->error;
        }

        // Insertar datos en la tabla Solicitudes
        $fechaRegistro = date("Y-m-d H:i:s");
        $sqlSolicitud = "INSERT INTO Solicitudes (noBoleta, fechaRegistro, estadoSolicitud, comprobantePago)
                        VALUES ('$boleta', '$fechaRegistro', 'Pendiente', NULL)";
        if ($conn->query($sqlSolicitud) === TRUE) {
            echo "Solicitud registrada exitosamente.";
        } else {
            echo "Error: " . $sqlSolicitud . "<br>" . $conn->error;
        }
    }

    $conn->close();
?>
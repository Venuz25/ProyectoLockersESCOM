<?php
    include_once '../conexion.php';

    // Datos del formulario
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

    // Verificar si todos los casilleros están ocupados
    $sqlTotalCasilleros = "SELECT COUNT(*) AS total FROM casilleros";
    $sqlCasillerosOcupados = "SELECT COUNT(*) AS ocupados FROM casilleros WHERE estado = 'Asignado'";

    $resultTotalCasilleros = $conn->query($sqlTotalCasilleros);
    $resultCasillerosOcupados = $conn->query($sqlCasillerosOcupados);

    $totalCasilleros = $resultTotalCasilleros->fetch_assoc()['total'];
    $ocupados = $resultCasillerosOcupados->fetch_assoc()['ocupados'];

    if ($totalCasilleros == $ocupados) {
        $estadoSolicitud = 'Lista de espera';
    } else {
        $estadoSolicitud = ($tipoSolicitud == 'Renovación') ? 'Aprobada' : 'Pendiente';
    }
   
    // Verificar si la boleta ya está registrada
    $sqlVerificarBoleta = "SELECT * FROM alumnos WHERE boleta = '$boleta'";
    $resultBoleta = $conn->query($sqlVerificarBoleta);

    if ($resultBoleta->num_rows > 0) {
        echo "<script>alert('La boleta ya está registrada. Verifique los datos.'); window.history.back();</script>";
        exit();
    }

    // Verificar si el usuario ya está registrado
    $sqlVerificarUsuario = "SELECT * FROM alumnos WHERE usuario = '$usuario'";
    $resultUsuario = $conn->query($sqlVerificarUsuario);

    if ($resultUsuario->num_rows > 0) {
        echo "<script>alert('El usuario ya está registrado. Verifique los datos.'); window.history.back();</script>";
        exit();
    }

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

                // Insertar datos en la tabla Solicitudes
                $fechaRegistro = date("Y-m-d H:i:s");
                $sqlSolicitud = "INSERT INTO solicitudes (noBoleta, fechaRegistro, estadoSolicitud, comprobantePago)
                    VALUES ('$boleta', '$fechaRegistro', '$estadoSolicitud', NULL)";

                // Actualizar el casillero
                $sqlActualizarCasillero = "UPDATE casilleros SET estado = 'Asignado', boletaAsignada = '$boleta' WHERE noCasillero = '$casilleroAnt'";

                if ($conn->query($sqlSolicitud) === FALSE || $conn->query($sqlAlumno) === FALSE || $conn->query($sqlActualizarCasillero) === FALSE) {
                    echo "Error al registrar la solicitud de $usuario..";
                    exit();
                }
            } else {
                echo "<script>alert('El casillero solicitado no está disponible.'); window.history.back();</script>";
                exit();
            }
        } else {
            echo "<script>alert('El casillero solicitado no existe.'); window.history.back();</script>";
            exit();
        }
    } 
    // Caso de solicitud de tipo "Primera vez"
    else if ($tipoSolicitud == "Primera vez") {
        // Subir los archivos
        move_uploaded_file($_FILES['credencial']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $credencialPath);
        move_uploaded_file($_FILES['horario']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $horarioPath);

        // Insertar datos en la tabla Alumnos
        $sqlAlumno = "INSERT INTO alumnos (boleta, solicitud, casilleroAnt, nombre, primerAp, segundoAp, telefono, correo, curp, estatura, credencial, horario, usuario, contrasena)
            VALUES ('$boleta', '$tipoSolicitud', NULL, '$nombre', '$primerApellido', '$segundoApellido', '$telefono', '$correo', '$curp', '$estatura', '$credencialPath', '$horarioPath', '$usuario', '$contrasena')";

        // Insertar datos en la tabla Solicitudes
        $fechaRegistro = date("Y-m-d H:i:s");
        $sqlSolicitud = "INSERT INTO solicitudes (noBoleta, fechaRegistro, estadoSolicitud, comprobantePago)
            VALUES ('$boleta', '$fechaRegistro', '$estadoSolicitud', NULL)";

        if ($conn->query($sqlSolicitud) === FALSE || $conn->query($sqlAlumno) === FALSE) {
            echo "Error al registrar la solicitud de $usuario.";
            exit();
        }
    } else {
        echo "<script>alert('Error al registrar la solicitud.'); /* window.history.back(); */</script>";
        exit();
    }

    echo "<script>window.location.href = '/ProyectoWeb/confirmacion.html';</script>";

    $conn->close();
?>

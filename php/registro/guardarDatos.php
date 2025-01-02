<?php
    include_once '../conexion.php';

    try {
        $conn->begin_transaction();

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
        $usuario = $_POST['usuario'];
        $contrasena = $_POST['contraseña'];
        $boleta = $_POST['boleta'];

        // Subir archivos
        $credencial = $_FILES['credencial']['name'];
        $horario = $_FILES['horario']['name'];
        $credencialPath = '/ProyectoWeb/Docs/Credenciales/' . $credencial;
        $horarioPath = '/ProyectoWeb/Docs/Horarios/' . $horario;

        if (!move_uploaded_file($_FILES['credencial']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $credencialPath) ||
            !move_uploaded_file($_FILES['horario']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $horarioPath)) {
            throw new Exception("Error al subir los archivos.");
        }

        // Verificar si todos los casilleros están ocupados
        $resultTotalCasilleros = $conn->query("SELECT COUNT(*) AS total FROM casilleros")->fetch_assoc();
        $resultCasillerosOcupados = $conn->query("SELECT COUNT(*) AS ocupados FROM casilleros WHERE estado = 'Asignado'")->fetch_assoc();

        $totalCasilleros = $resultTotalCasilleros['total'];
        $ocupados = $resultCasillerosOcupados['ocupados'];
        $estadoSolicitud = ($totalCasilleros == $ocupados) ? 'Lista de espera' : (($tipoSolicitud == 'Renovación') ? 'Aprobada' : 'Pendiente');

        // Verificar si la boleta o el usuario ya están registrados
        $stmtVerificar = $conn->prepare("SELECT COUNT(*) AS total FROM alumnos WHERE boleta = ? OR usuario = ?");
        $stmtVerificar->bind_param("ss", $boleta, $usuario);
        $stmtVerificar->execute();
        $resultVerificar = $stmtVerificar->get_result()->fetch_assoc();
        if ($resultVerificar['total'] > 0) {
            throw new Exception("La boleta o el usuario ya están registrados. Verifique los datos.");
        }

        // Insertar datos en la tabla Alumnos
        $stmtAlumno = $conn->prepare("INSERT INTO alumnos (boleta, solicitud, casilleroAnt, nombre, primerAp, segundoAp, telefono, correo, curp, estatura, credencial, horario, usuario, contrasena) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmtAlumno->bind_param("ssssssssssssss", $boleta, $tipoSolicitud, $casilleroAnt, $nombre, $primerApellido, $segundoApellido, $telefono, $correo, $curp, $estatura, $credencialPath, $horarioPath, $usuario, $contrasena);
        if (!$stmtAlumno->execute()) {
            throw new Exception("Error al insertar los datos en la tabla Alumnos.");
        }

        // Insertar datos en la tabla Solicitudes
        $fechaRegistro = date("Y-m-d H:i:s");
        $stmtSolicitud = $conn->prepare("INSERT INTO solicitudes (noBoleta, fechaRegistro, estadoSolicitud, comprobantePago) VALUES (?, ?, ?, NULL)");
        $stmtSolicitud->bind_param("sss", $boleta, $fechaRegistro, $estadoSolicitud);
        if (!$stmtSolicitud->execute()) {
            throw new Exception("Error al insertar los datos en la tabla Solicitudes.");
        }

        // Si la solicitud es de renovación, actualizar el estado del casillero
        if ($tipoSolicitud == "Renovación") {
            $stmtCasillero = $conn->prepare("UPDATE casilleros SET estado = 'Asignado', boletaAsignada = ? WHERE noCasillero = ? AND estado = 'Disponible'");
            $stmtCasillero->bind_param("ss", $boleta, $casilleroAnt);
            if (!$stmtCasillero->execute() || $stmtCasillero->affected_rows == 0) {
                throw new Exception("El casillero solicitado no está disponible o no existe.");
            }
        }

        // Confirmar la transacción
        $conn->commit();

        echo "<script>window.location.href = '/ProyectoWeb/confirmacion.html';</script>";

    } catch (Exception $e) {
        $conn->rollback(); // Revertir cambios en caso de error
        echo "<script>alert('" . $e->getMessage() . "'); window.history.back();</script>";
    } finally {
        $conn->close();
    }
?>

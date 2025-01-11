<?php
        include_once '../../conexion.php';

        $table = $_POST['table'];
        $recordId = $_POST['recordId'];

        switch ($table) {
            case 'administradores':
                $usuario = $_POST['usuario'];
                $contrasena = $_POST['contrasena'];
            
                // Verificar si el usuario ha cambiado
                $stmtVerificar = $conn->prepare("SELECT COUNT(*) AS total FROM administradores WHERE usuario = ? AND id != ?");
                $stmtVerificar->bind_param("si", $usuario, $recordId);
                $stmtVerificar->execute();
                $resultVerificar = $stmtVerificar->get_result()->fetch_assoc();
            
                if ($resultVerificar['total'] > 0) {
                    echo json_encode(['success' => false, 'message' => 'El usuario ya esta registrado. Verifique los datos.']);
                    exit;
                }
            
                // Actualizar los datos en la tabla
                $stmtActualizar = $conn->prepare("UPDATE administradores SET id = ?, usuario = ?, contrasena = ? WHERE id = ?");
                $stmtActualizar->bind_param("issi", $id, $usuario, $contrasena, $recordId);
            
                if ($stmtActualizar->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Administrador actualizado exitosamente']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al actualizar el administrador']);
                }
                break;
            

            case 'alumnos':
                $tipoSolicitud = $_POST['tipo_solicitud'];
                $casilleroAnt = ($tipoSolicitud == 'Renovación') ? $_POST['numero-casillero'] : NULL;
                $nombre = $_POST['nombre'];
                $primerApellido = $_POST['p_apellido'];
                $segundoApellido = $_POST['s_apellido'];
                $telefono = $_POST['telefono'];
                $correo = $_POST['correo'];
                $curp = $_POST['curp'];
                $estatura = $_POST['estatura'];
                $usuario = $_POST['usuario'];
                $contrasena = $_POST['contraseña'];
            
                // Actualizar archivos si se han enviado nuevos
                $credencialPath = null;
                $horarioPath = null;
            
                if (isset($_FILES['credencial']) && $_FILES['credencial']['error'] === UPLOAD_ERR_OK) {
                    $credencial = $_FILES['credencial']['name'];
                    $credencialPath = '/ProyectoWeb/Docs/Credenciales/' . $credencial;
                    move_uploaded_file($_FILES['credencial']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $credencialPath);
                }
                if (isset($_FILES['horario']) && $_FILES['horario']['error'] === UPLOAD_ERR_OK) {
                    $horario = $_FILES['horario']['name'];
                    $horarioPath = '/ProyectoWeb/Docs/Horarios/' . $horario;
                    move_uploaded_file($_FILES['horario']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $horarioPath);
                }
            
                // Actualizar los datos en la tabla Alumnos
                $stmtActualizarAlumno = $conn->prepare("UPDATE alumnos SET solicitud = ?, casilleroAnt = ?, nombre = ?, primerAp = ?, segundoAp = ?, telefono = ?, correo = ?, curp = ?, estatura = ?, credencial = ?, horario = ?, usuario = ?, contrasena = ? WHERE boleta = ?");
                $stmtActualizarAlumno->bind_param("ssssssssssssss", $tipoSolicitud, $casilleroAnt, $nombre, $primerApellido, $segundoApellido, $telefono, $correo, $curp, $estatura, $credencialPath, $horarioPath, $usuario, $contrasena, $recordId);

                if ($stmtActualizarAlumno->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Alumno actualizado exitosamente']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al actualizar los datos del alumno']);
                }
                break;
                
            case 'solicitudes':
                $estadoSolicitud = $_POST['estado_solicitud'];
                $noBoleta = $_POST['no_boleta'];

                // Actualizar los datos en la tabla Solicitudes
                $stmtActualizar = $conn->prepare("UPDATE solicitudes SET estadoSolicitud = ? WHERE noBoleta = ?");
                $stmtActualizar->bind_param("ss", $estadoSolicitud, $noBoleta);

                if ($stmtActualizar->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Solicitud actualizada exitosamente']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al actualizar la solicitud']);
                }
                break;

            default:
                echo json_encode(['success' => false, 'message' => 'Tabla no válida']);
                exit;
        }

        mysqli_close($conn);
?>

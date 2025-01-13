<?php
    //Archivo para actualizar un registro
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
            $stmtActualizar = $conn->prepare("UPDATE administradores SET usuario = ?, contrasena = ? WHERE id = ?");
            $stmtActualizar->bind_param("ssi", $usuario, $contrasena, $recordId);
        
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
        
            // Consulta previa para obtener los valores actuales de credencial y horario
            $stmtConsulta = $conn->prepare("SELECT credencial, horario FROM alumnos WHERE boleta = ?");
            $stmtConsulta->bind_param("s", $recordId);
            $stmtConsulta->execute();
            $resultConsulta = $stmtConsulta->get_result();
        
            if ($resultConsulta->num_rows > 0) {
                $row = $resultConsulta->fetch_assoc();
                $credencialPath = $row['credencial'];
                $horarioPath = $row['horario'];
            } else {
                echo json_encode(['success' => false, 'message' => 'Registro no encontrado']);
                exit;
            }
        
            // Actualizar archivos si se han enviado nuevos
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
        
            // Si la solicitud es "Renovación"
            if ($tipoSolicitud == 'Renovación' && $casilleroAnt) {
                // Verificar si el casillero ya está asignado
                $stmtVerificarCasillero = $conn->prepare("SELECT boletaAsignada FROM casilleros WHERE noCasillero = ?");
                $stmtVerificarCasillero->bind_param("i", $casilleroAnt);
                $stmtVerificarCasillero->execute();
                $resultCasillero = $stmtVerificarCasillero->get_result();

                if ($resultCasillero->num_rows > 0) {
                    $rowCasillero = $resultCasillero->fetch_assoc();
                    
                    // Verificar si el casillero ya tiene una boleta asignada y que sea diferente a la actual
                    if (!empty($rowCasillero['boletaAsignada']) && $rowCasillero['boletaAsignada'] != $recordId) {
                        echo json_encode(['success' => false, 'message' => 'El casillero ya está asignado a otro alumno. Verifique los datos.']);
                        exit;
                    }
                }

                // Asignar el casillero y actualizar su estado
                $stmtAsignarCasillero = $conn->prepare("UPDATE casilleros SET boletaAsignada = ?, estado = 'Asignado' WHERE noCasillero = ?");
                $stmtAsignarCasillero->bind_param("si", $recordId, $casilleroAnt);
                if (!$stmtAsignarCasillero->execute()) {
                    echo json_encode(['success' => false, 'message' => 'Error al asignar el casillero.']);
                    exit;
                }
        
                // Actualizar el estado de la solicitud a "Aprobada"
                $fechaAprobacion = date("Y-m-d H:i:s");
                $stmtActualizarSolicitud = $conn->prepare("UPDATE solicitudes SET estadoSolicitud = 'Aprobada', fechaAprobacion = ? WHERE noBoleta = ?");
                $stmtActualizarSolicitud->bind_param("ss", $fechaAprobacion, $recordId);
                if (!$stmtActualizarSolicitud->execute()) {
                    echo json_encode(['success' => false, 'message' => 'Error al actualizar el estado de la solicitud.']);
                    exit;
                }
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
            
        case 'casilleros':
            $altura = $_POST['altura'];
            $estado = $_POST['estado'];
            $boletaAsignada = ($estado == 'Asignado') ? $_POST['boletaAsignada'] : NULL;
        
            if ($estado == 'Asignado') {
                // Verificar si la boleta existe
                $stmtVerificarBoleta = $conn->prepare("SELECT COUNT(*) AS total FROM alumnos WHERE boleta = ?");
                $stmtVerificarBoleta->bind_param("s", $boletaAsignada);
                $stmtVerificarBoleta->execute();
                $resultVerificarBoleta = $stmtVerificarBoleta->get_result();
                $rowVerificarBoleta = $resultVerificarBoleta->fetch_assoc();
                if ($rowVerificarBoleta['total'] == 0) {
                    echo json_encode(['success' => false, 'message' => 'La boleta no existe. Verifique los datos.']);
                    exit;
                }
                
                // Verificar si la boletaAsignada ya está asignada a otro casillero
                $stmtVerificarBoleta = $conn->prepare("SELECT boletaAsignada FROM casilleros WHERE boletaAsignada = ? AND noCasillero != ?");
                $stmtVerificarBoleta->bind_param("si", $boletaAsignada, $recordId);
                $stmtVerificarBoleta->execute();
                $resultVerificarBoleta = $stmtVerificarBoleta->get_result();
                if ($resultVerificarBoleta->num_rows > 0) {
                    echo json_encode(['success' => false, 'message' => 'La boleta ya está asignada a otro casillero. Verifique los datos.']);
                    exit;
                }
        
                // Actualizar el casillero
                $stmtActualizarCasillero = $conn->prepare("UPDATE casilleros SET altura = ?, estado = 'Asignado', boletaAsignada = ? WHERE noCasillero = ?");
                $stmtActualizarCasillero->bind_param("dsi", $altura, $boletaAsignada, $recordId);
                if (!$stmtActualizarCasillero->execute()) {
                    echo json_encode(['success' => false, 'message' => 'Error al asignar el casillero.']);
                    exit;
                }

                // Actualizar el solicitud
                $fechaAprobacion = date("Y-m-d H:i:s");
                $stmtActualizarSolicitud = $conn->prepare("UPDATE solicitudes SET estadoSolicitud = 'Aprobada', fechaAprobacion = ? WHERE noBoleta = ?");
                $stmtActualizarSolicitud->bind_param("ss", $fechaAprobacion, $boletaAsignada);
                if (!$stmtActualizarSolicitud->execute()) {
                    echo json_encode(['success' => false, 'message' => 'Error al actualizar el estado de la solicitud.']);
                    exit;
                }

                echo json_encode(['success' => true, 'message' => 'Casillero actualizado exitosamente']);

            } else { // Si el estado es "Disponible"
                // Obtener la boletaAsignada del casillero
                $stmtObtenerBoleta = $conn->prepare("SELECT boletaAsignada FROM casilleros WHERE noCasillero = ?");
                $stmtObtenerBoleta->bind_param("i", $recordId);
                $stmtObtenerBoleta->execute();
                $resultObtenerBoleta = $stmtObtenerBoleta->get_result();
            
                if ($resultObtenerBoleta->num_rows > 0) {
                    $rowBoleta = $resultObtenerBoleta->fetch_assoc();
                    $boletaAsignada = $rowBoleta['boletaAsignada'];
            
                    if (!empty($boletaAsignada)) {
                        // Cambiar el estado de la solicitud asociada a la boleta
                        $stmtActualizarSolicitud = $conn->prepare("UPDATE solicitudes SET estadoSolicitud = 'Pendiente', fechaAprobacion = NULL WHERE noBoleta = ?");
                        $stmtActualizarSolicitud->bind_param("s", $boletaAsignada);
                        if (!$stmtActualizarSolicitud->execute()) {
                            echo json_encode(['success' => false, 'message' => 'Error al actualizar la solicitud.']);
                            exit;
                        }
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'No se encontró el casillero especificado.']);
                    exit;
                }
            
                // Actualizar el casillero
                $stmtActualizarCasillero = $conn->prepare("UPDATE casilleros SET altura = ?, estado = 'Disponible', boletaAsignada = NULL WHERE noCasillero = ?");
                $stmtActualizarCasillero->bind_param("di", $altura, $recordId);
                if (!$stmtActualizarCasillero->execute()) {
                    echo json_encode(['success' => false, 'message' => 'Error al actualizar el casillero.']);
                    exit;
                }
            
                echo json_encode(['success' => true, 'message' => 'Casillero actualizado exitosamente']);
            }
        
            break;
            
        case 'solicitudes':
            $noBoleta = $_POST['noBoleta'];
            $fechaRegistro = $_POST['fechaRegistro'];
            $estadoSolicitud = $_POST['estadoSolicitud'];
            $noCasillero = $estadoSolicitud == 'Aprobada'? $_POST['noCasillero'] : null;
            $fechaAprobacion = $estadoSolicitud == 'Aprobada'? $_POST['fechaAprobacion'] : null;

            // Consulta previa para obtener los valores actuales del comprobante de pago
            $stmtConsulta = $conn->prepare("SELECT comprobantePago FROM solicitudes WHERE id = ?");
            $stmtConsulta->bind_param("i", $recordId);
            $stmtConsulta->execute();
            $resultConsulta = $stmtConsulta->get_result();
        
            if ($resultConsulta->num_rows > 0) {
                $row = $resultConsulta->fetch_assoc();
                $comprobantePath = $row['comprobantePago'];
            } else {
                echo json_encode(['success' => false, 'message' => 'Registro no encontrado']);
                exit;
            }
        
            // Actualizar archivos si se han enviado nuevos
            if (isset($_FILES['comprobantePago']) && $_FILES['comprobantePago']['error'] === UPLOAD_ERR_OK) {
                $comprobante = $_FILES['comprobantePago']['name'];
                $comprobantePath = '/ProyectoWeb/Docs/Comprobantes/' . $comprobante;
                move_uploaded_file($_FILES['comprobantePago']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $comprobantePath);
            }

            //Si la solicitud esta aprobada
            if ($estadoSolicitud === 'Aprobada') {      
                // Verificar que el casillero exista
                $stmtVerificarCasillero = $conn->prepare("SELECT estado FROM casilleros WHERE noCasillero = ?");
                $stmtVerificarCasillero->bind_param("i", $noCasillero);
                $stmtVerificarCasillero->execute();
                $resultVerificarCasillero = $stmtVerificarCasillero->get_result();
                if ($resultVerificarCasillero->num_rows === 0) {
                    echo json_encode(['success' => false, 'message' => 'El casillero especificado no existe. Verifique los datos.']);
                    exit;
                }
                
                // Verificar si el casillero ya está asignado
                $stmtVerificarCasillero = $conn->prepare("SELECT estado FROM casilleros WHERE noCasillero = ? AND boletaAsignada != ?");
                $stmtVerificarCasillero->bind_param("is", $noCasillero, $noBoleta);
                $stmtVerificarCasillero->execute();
                $resultCasillero = $stmtVerificarCasillero->get_result();
                if ($resultCasillero->num_rows > 0) {
                    $rowCasillero = $resultCasillero->fetch_assoc();
                    if ($rowCasillero['estado'] === 'Asignado') {
                        echo json_encode(['success' => false, 'message' => 'El casillero ya está asignado. Por favor, seleccione otro.']);
                        exit;
                    }
                }
        
                // Verificar si el alumno ya tiene un casillero asignado
                $stmtVerificarBoleta = $conn->prepare("SELECT noCasillero FROM casilleros WHERE boletaAsignada = ?");
                $stmtVerificarBoleta->bind_param("s", $noBoleta);
                $stmtVerificarBoleta->execute();
                $resultBoleta = $stmtVerificarBoleta->get_result();

                if ($resultBoleta->num_rows > 0) {
                    $rowBoleta = $resultBoleta->fetch_assoc();
                    $casilleroActual = $rowBoleta['noCasillero'];

                    // Revocar el casillero anterior
                    $stmtLiberarCasillero = $conn->prepare("UPDATE casilleros SET estado = 'Disponible', boletaAsignada = NULL WHERE noCasillero = ?");
                    $stmtLiberarCasillero->bind_param("i", $casilleroActual);

                    if (!$stmtLiberarCasillero->execute()) {
                        echo json_encode(['success' => false, 'message' => 'Error al liberar el casillero actual.']);
                        exit;
                    }
                }

                // Asignar el nuevo casillero
                $stmtAsignarCasillero = $conn->prepare("UPDATE casilleros SET estado = 'Asignado', boletaAsignada = ? WHERE noCasillero = ?");
                $stmtAsignarCasillero->bind_param("si", $noBoleta, $noCasillero);

                if (!$stmtAsignarCasillero->execute()) {
                    echo json_encode(['success' => false, 'message' => 'Error al asignar el nuevo casillero.']);
                    exit;
                }
        
                // Actualizar la solicitud a "Aprobada"
                $stmtActualizarSolicitud = $conn->prepare("UPDATE solicitudes SET fechaRegistro = ?, estadoSolicitud = ?, fechaAprobacion = ?, comprobantePago = ? WHERE id = ?");
                $stmtActualizarSolicitud->bind_param("ssssi", $fechaRegistro, $estadoSolicitud, $fechaAprobacion, $comprobantePath, $recordId);
                if ($stmtActualizarSolicitud->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Solicitud aprobada exitosamente.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al aprobar la solicitud.']);
                }
        
            } else { // Si la solicitud es "Pendiente" o "Lista de espera"
                // Verificar si la boleta ya está asignada en un casillero
                $stmtVerificarBoleta = $conn->prepare("SELECT noCasillero FROM casilleros WHERE boletaAsignada = ?");
                $stmtVerificarBoleta->bind_param("s", $noBoleta);
                $stmtVerificarBoleta->execute();
                $resultBoleta = $stmtVerificarBoleta->get_result();
        
                if ($resultBoleta->num_rows > 0) {
                    $rowBoleta = $resultBoleta->fetch_assoc();
                    $casilleroActual = $rowBoleta['noCasillero'];
        
                    // Liberar el casillero actual
                    $stmtLiberarCasillero = $conn->prepare("UPDATE casilleros SET estado = 'Disponible', boletaAsignada = NULL WHERE noCasillero = ?");
                    $stmtLiberarCasillero->bind_param("i", $casilleroActual);
        
                    if (!$stmtLiberarCasillero->execute()) {
                        echo json_encode(['success' => false, 'message' => 'Error al liberar el casillero actual.']);
                        exit;
                    }
                }
        
                // Actualizar la solicitud a "Pendiente" o "Lista de espera"
                $stmtActualizarSolicitud = $conn->prepare("UPDATE solicitudes SET fechaRegistro = ?, estadoSolicitud = ?, fechaAprobacion = ?, comprobantePago = ? WHERE id = ?");
                $stmtActualizarSolicitud->bind_param("ssssi", $fechaRegistro, $estadoSolicitud, $fechaAprobacion, $comprobantePath, $recordId);
                if ($stmtActualizarSolicitud->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Solicitud aprobada exitosamente.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al aprobar la solicitud.']);
                }
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Tabla no válida']);
            exit;
    }

    mysqli_close($conn);
?>

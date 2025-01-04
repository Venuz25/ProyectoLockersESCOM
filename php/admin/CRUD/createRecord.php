<?php
    include_once '../../conexion.php';

    $table = $_POST['table'];

    switch ($table) {
        case 'administradores':
            $id = $_POST['id'];
            $usuario = $_POST['usuario'];
            $contrasena = $_POST['contrasena'];

            // Verificación de si la ID o usuario ya están registrados
            $stmtVerificar = $conn->prepare("SELECT COUNT(*) AS total FROM administradores WHERE id = ? OR usuario = ?");
            $stmtVerificar->bind_param("is", $id, $usuario);
            $stmtVerificar->execute();
            $resultVerificar = $stmtVerificar->get_result()->fetch_assoc();

            if ($resultVerificar['total'] > 0) {
                echo json_encode(['success' => false, 'message' => 'La id o el usuario ya están registrados. Verifique los datos.']);
                exit;
            }

            // Usar consulta preparada para insertar los datos
            $stmtInsertar = $conn->prepare("INSERT INTO administradores (id, usuario, contrasena) VALUES (?, ?, ?)");
            $stmtInsertar->bind_param("iss", $id, $usuario, $contrasena);

            // Ejecutar la consulta de inserción
            if ($stmtInsertar->execute()) {
                echo json_encode(['success' => true, 'message' => 'Administrador creado exitosamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al crear el administrador']);
            }
            break;
        case 'alumnos':
            // Datos del formulario
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
            $boleta = $_POST['boleta'];

            // Subir archivos
            $credencial = $_FILES['credencial']['name'];
            $horario = $_FILES['horario']['name'];
            $credencialPath = '/ProyectoWeb/Docs/Credenciales/' . $credencial;
            $horarioPath = '/ProyectoWeb/Docs/Horarios/' . $horario;

            if (!move_uploaded_file($_FILES['credencial']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $credencialPath) ||
                !move_uploaded_file($_FILES['horario']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $horarioPath)) {
                    echo json_encode(['success' => false, 'message' => 'Error al subir los archivos.']);
                    exit;
            }

            // Verificar si la boleta o el usuario ya están registrados
            $stmtVerificar = $conn->prepare("SELECT COUNT(*) AS total FROM alumnos WHERE boleta = ? OR usuario = ?");
            $stmtVerificar->bind_param("ss", $boleta, $usuario);
            $stmtVerificar->execute();
            $resultVerificar = $stmtVerificar->get_result()->fetch_assoc();
            if ($resultVerificar['total'] > 0) {
                echo json_encode(['success' => false, 'message' => 'La boleta o el usuario ya están registrados. Verifique los datos.']);
                exit;
            }

            // Verificar si todos los casilleros están ocupados
            $resultTotalCasilleros = $conn->query("SELECT COUNT(*) AS total FROM casilleros")->fetch_assoc();
            $resultCasillerosOcupados = $conn->query("SELECT COUNT(*) AS ocupados FROM casilleros WHERE estado = 'Asignado'")->fetch_assoc();

            $totalCasilleros = $resultTotalCasilleros['total'];
            $ocupados = $resultCasillerosOcupados['ocupados'];
            $estadoSolicitud = ($totalCasilleros == $ocupados) ? 'Lista de espera' : (($tipoSolicitud == 'Renovación') ? 'Aprobada' : 'Pendiente');

            // Insertar datos en la tabla Alumnos
            $stmtAlumno = $conn->prepare("INSERT INTO alumnos (boleta, solicitud, casilleroAnt, nombre, primerAp, segundoAp, telefono, correo, curp, estatura, credencial, horario, usuario, contrasena) 
                                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmtAlumno->bind_param("ssssssssssssss", $boleta, $tipoSolicitud, $casilleroAnt, $nombre, $primerApellido, $segundoApellido, $telefono, $correo, $curp, $estatura, $credencialPath, $horarioPath, $usuario, $contrasena);
            if (!$stmtAlumno->execute()) {
                echo json_encode(['success' => false, 'message' => 'Error al insertar los datos en la tabla Alumnos.']);
                exit;
            }

            // Insertar datos en la tabla Solicitudes
            $fechaRegistro = date("Y-m-d H:i:s");
            $stmtSolicitud = $conn->prepare("INSERT INTO solicitudes (noBoleta, fechaRegistro, estadoSolicitud, comprobantePago) VALUES (?, ?, ?, NULL)");
            $stmtSolicitud->bind_param("sss", $boleta, $fechaRegistro, $estadoSolicitud);
            if (!$stmtSolicitud->execute()) {
                echo json_encode(['success' => false, 'message' => 'Error al insertar los datos en la tabla Solicitudes.']);
                exit;
            }

            // Si la solicitud es de renovación, actualizar el estado del casillero
            if ($tipoSolicitud == "Renovación") {
                $stmtCasillero = $conn->prepare("UPDATE casilleros SET estado = 'Asignado', boletaAsignada = ? WHERE noCasillero = ? AND estado = 'Disponible'");
                $stmtCasillero->bind_param("ss", $boleta, $casilleroAnt);
                if (!$stmtCasillero->execute() || $stmtCasillero->affected_rows == 0) {
                    echo json_encode(['success' => false, 'message' => 'El casillero solicitado no está disponible o no existe.']);
                    exit;
                }
            }

            echo json_encode(['success' => true, 'message' => 'Alumno registrado exitosamente']);
            break;
        case 'casilleros':
            // Similar lógica para casilleros
            break;
        case 'solicitudes':
            // Similar lógica para solicitudes
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Tabla no válida']);
            exit;
    }

    mysqli_close($conn);
?>

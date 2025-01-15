<?php
    //Archivo para crear un registro
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
                echo json_encode(['success' => false, 'message' => 'El id o el usuario ya están registrados. Verifique los datos.']);
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
            
            // Ruta del directorio donde se guardarán los archivos
            $targetDirCredencial = $_SERVER['DOCUMENT_ROOT'] . '/ProyectoWeb/Docs/Credenciales/';
            $targetDirHorario = $_SERVER['DOCUMENT_ROOT'] . '/ProyectoWeb/Docs/Horarios/';

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
                echo json_encode(['success' => false, 'message' => 'La boleta o el usuario ya están registrados. Verifique los datos.']);
                exit;
            }

            // Verificar si el casillero existe
            $stmtVerificarExistenciaCasillero = $conn->prepare("SELECT COUNT(*) AS total FROM casilleros WHERE noCasillero = ?");
            $stmtVerificarExistenciaCasillero->bind_param("s", $casilleroAnt);
            $stmtVerificarExistenciaCasillero->execute();
            $resultVerificarExistenciaCasillero = $stmtVerificarExistenciaCasillero->get_result()->fetch_assoc();
            if ($resultVerificarExistenciaCasillero['total'] == 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'El casillero solicitado no existe en el sistema.'
                ]);
                exit;
            }

            // Verificar si el casillero está asignado a otro alumno
            $stmtVerificarCasillero = $conn->prepare("SELECT boletaAsignada FROM casilleros WHERE noCasillero = ? AND boletaAsignada != ?");
            $stmtVerificarCasillero->bind_param("ss", $casilleroAnt, $boleta);
            $stmtVerificarCasillero->execute();
            $resultVerificarCasillero = $stmtVerificarCasillero->get_result();
            if ($resultVerificarCasillero->num_rows > 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'El casillero solicitado ya está asignado a otro alumno.'
                ]);
                exit;
            }

            // Subida y renombrado de la credencial
            if (isset($_FILES['credencial']) && $_FILES['credencial']['error'] === UPLOAD_ERR_OK) {
                $credencialTmpName = $_FILES['credencial']['tmp_name'];
                $credencialExt = pathinfo($_FILES['credencial']['name'], PATHINFO_EXTENSION);
                $credencialNewName = $boleta . "_credencial." . $credencialExt;
                $credencialTargetFile = $targetDirCredencial . $credencialNewName;

                if (move_uploaded_file($credencialTmpName, $credencialTargetFile)) {
                    $credencialPath = '/ProyectoWeb/Docs/Credenciales/' . $credencialNewName;
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al subir la credencial']);
                    exit;
                }
            }

            // Subida y renombrado del horario
            if (isset($_FILES['horario']) && $_FILES['horario']['error'] === UPLOAD_ERR_OK) {
                $horarioTmpName = $_FILES['horario']['tmp_name'];
                $horarioExt = pathinfo($_FILES['horario']['name'], PATHINFO_EXTENSION);
                $horarioNewName = $boleta . "_horario." . $horarioExt; 
                $horarioTargetFile = $targetDirHorario . $horarioNewName;

                if (move_uploaded_file($horarioTmpName, $horarioTargetFile)) {
                    $horarioPath = '/ProyectoWeb/Docs/Horarios/' . $horarioNewName;
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al subir el horario']);
                    exit;
                }
            }
            
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
            $fechaAprobacion = ($tipoSolicitud == 'Renovación') ? date("Y-m-d H:i:s") : null;
            $stmtSolicitud = $conn->prepare("INSERT INTO solicitudes (noBoleta, fechaRegistro, estadoSolicitud, fechaAprobacion, comprobantePago) VALUES (?, ?, ?, ?, NULL)");
            $stmtSolicitud->bind_param("ssss", $boleta, $fechaRegistro, $estadoSolicitud, $fechaAprobacion);
            if (!$stmtSolicitud->execute()) {
                echo json_encode(['success' => false, 'message' => 'Error al insertar los datos en la tabla Solicitudes.']);
                exit;
            }

            // Si la solicitud es de renovación, actualizar el estado del casillero
            if ($tipoSolicitud == "Renovación") {
                $stmtCasillero = $conn->prepare("UPDATE casilleros SET estado = 'Asignado', boletaAsignada = ? WHERE noCasillero = ? AND estado = 'Disponible'");
                $stmtCasillero->bind_param("ss", $boleta, $casilleroAnt);
                if (!$stmtCasillero->execute() || $stmtCasillero->affected_rows == 0) {
                    echo json_encode(['success' => false, 'message' => 'Error al actualizar el estado del casillero.']);
                    exit;
                }
            }

            echo json_encode(['success' => true, 'message' => 'Alumno registrado exitosamente']);
            break;
        case 'casilleros':
            $noCasillero = $_POST['noCasillero'];
            $altura = $_POST['altura'];
            $estado = $_POST['estado'];
            $boletaAsignada = ($estado == 'Asignado')? $_POST['boletaAsignada'] : NULL;

            // Verificación de si el casillero ya están registrado
            $stmtVerificar = $conn->prepare("SELECT COUNT(*) AS total FROM casilleros WHERE noCasillero = ?");
            $stmtVerificar->bind_param("i", $noCasillero);
            $stmtVerificar->execute();
            $resultVerificar = $stmtVerificar->get_result()->fetch_assoc();

            if ($resultVerificar['total'] > 0) {
                echo json_encode(['success' => false, 'message' => 'El número de casillero ya existe. Verifique los datos.']);
                exit;
            }

            if($estado == 'Asignado'){
                // Verificación de si la boleta ya tiene un casillero asignado
                $stmtBoleta = $conn->prepare("SELECT COUNT(*) AS total FROM casilleros WHERE boletaAsignada = ?");
                $stmtBoleta->bind_param("s", $boletaAsignada);
                $stmtBoleta->execute();
                $resultBoleta = $stmtBoleta->get_result()->fetch_assoc();

                if ($resultBoleta['total'] > 0) {
                    echo json_encode(['success' => false, 'message' => 'La boleta ya tiene un casillero asignado. Verifique los datos.']);
                    exit;
                }

                // Verificación de si la boleta existe
                $stmtSolicitud = $conn->prepare("SELECT COUNT(*) AS total FROM solicitudes WHERE noBoleta = ?");
                $stmtSolicitud->bind_param("s", $boletaAsignada);
                $stmtSolicitud->execute();
                $resultSolicitud = $stmtSolicitud->get_result()->fetch_assoc();

                if ($resultSolicitud['total'] == 0) {
                    echo json_encode(['success' => false, 'message' => 'La boleta que ingreso no existe. Verifique los datos.']);
                    exit;
                }
            }

            $stmtCasillero = $conn->prepare("INSERT INTO casilleros (noCasillero, altura, estado, boletaAsignada) VALUES (?, ?, ?, ?)");
            $stmtCasillero->bind_param("isss", $noCasillero, $altura, $estado, $boletaAsignada);
            if (!$stmtCasillero->execute()) {
                echo json_encode(['success' => false, 'message' => 'Error al insertar los datos en la tabla Casilleros.']);
                exit;
            }
            
            if($estado == 'Asignado'){
                $fechaAprobacion = date("Y-m-d H:i:s");
                $stmtESolicitud = $conn->prepare("UPDATE solicitudes SET estadoSolicitud = 'Aprobada', fechaAprobacion = ? WHERE noBoleta = ?");
                $stmtESolicitud->bind_param("ss", $fechaAprobacion, $boletaAsignada);
                if (!$stmtESolicitud->execute()) {
                    echo json_encode(['success' => false, 'message' => 'Error al actualizar los datos en la Solicitud.']);
                    exit;
                }
            }

            echo json_encode(['success' => true, 'message' => 'Casillero registrado exitosamente']);
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Tabla no válida']);
            exit;
    }

    mysqli_close($conn);
?>

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

            $query = "INSERT INTO administradores (id, usuario, contrasena) VALUES ('$id','$usuario', '$contrasena')";
            break;
        case 'alumnos':
            


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

    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Registro creado exitosamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al crear el registro']);
    }

    mysqli_close($conn);
?>

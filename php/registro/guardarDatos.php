<?php
include_once '../conexion.php';

// Verificar si se han enviado datos a través del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir y sanitizar los datos del formulario
    $tipo_solicitud = $_POST['tipo_solicitud'];
    $numero_casillero = ($tipo_solicitud == "Renovación") ? $_POST['numero-casillero'] : null;
    $nombre = $_POST['nombre'];
    $p_apellido = $_POST['p_apellido'];
    $s_apellido = $_POST['s_apellido'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];
    $boleta = $_POST['boleta'];
    $curp = $_POST['curp'];
    $estatura = $_POST['estatura'];
    $usuario = $_POST['usuario'];
    $contraseña = $_POST['contraseña'];

    // Manejo de archivos subidos
    $credencial = $_FILES['credencial'];
    $horario = $_FILES['horario'];

    // Ruta de destino para guardar los archivos
    $ruta_docs = '../Docs/';

    // Mover archivos a la carpeta Docs
    if (move_uploaded_file($credencial['tmp_name'], $ruta_docs . basename($credencial['name'])) &&
        move_uploaded_file($horario['tmp_name'], $ruta_docs . basename($horario['name']))) {
        
        // Aquí se puede agregar la lógica para guardar los datos en la base de datos
        // Ejemplo de inserción en la base de datos
        $query = "INSERT INTO solicitudes (tipo_solicitud, numero_casillero, nombre, p_apellido, s_apellido, telefono, correo, boleta, curp, estatura, usuario, contraseña) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("ssssssssssss", $tipo_solicitud, $numero_casillero, $nombre, $p_apellido, $s_apellido, $telefono, $correo, $boleta, $curp, $estatura, $usuario, $contraseña);
        
        if ($stmt->execute()) {
            echo "Solicitud guardada exitosamente.";
        } else {
            echo "Error al guardar la solicitud: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error al subir los archivos.";
    }

    $conexion->close();
} else {
    echo "No se han enviado datos.";
}
?>
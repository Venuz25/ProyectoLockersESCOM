<?php
    date_default_timezone_set('America/Mexico_City');

    //Archivo conexión a la base de datos
    $servername = "localhost:3309";//3309
    $username = "root";
    $password = "";
    $dbname = "lockers_db";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }
?>
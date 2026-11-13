<?php

require_once "credenciales.php";

$conn = mysqli_connect(
    $DB_HOST,
    $DB_USER,
    $DB_PASSWORD,
    $DB_NAME
);

if (!$conn) {

    error_log(
        "Error de conexión a la base de datos: " .
        mysqli_connect_error()
    );

    die("No fue posible conectar con la base de datos.");
}

?>
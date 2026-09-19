<?php

session_start();

if(!isset($_SESSION['usuario'])){
    header("Location: login.php");
}

include("config/conexion.php");

$id = $_GET['id'];

$resultado = mysqli_query(
    $conn,
    "SELECT * FROM clientes WHERE id='$id'"
);

$row = mysqli_fetch_assoc($resultado);

if(isset($_POST['actualizar'])){

    $nombre = $_POST['nombre'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];

    mysqli_query(
        $conn,
        "UPDATE clientes SET

        nombre='$nombre',
        telefono='$telefono',
        direccion='$direccion'

        WHERE id='$id'"
    );

    header("Location: clientes.php");

}

?>

<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">

<title>Editar Cliente</title>

<link rel="stylesheet" href="css/styles.css">

</head>
<body>

<?php include("includes/sidebar.php"); ?>

<div class="main">

    <h1>Editar Cliente</h1>

    <div class="form-box">

        <form method="POST">

            <input
                type="text"
                name="nombre"
                class="form-control"
                value="<?= $row['nombre'] ?>"
                required
            >

            <input
                type="text"
                name="telefono"
                class="form-control"
                value="<?= $row['telefono'] ?>"
                required
            >

            <input
                type="text"
                name="direccion"
                class="form-control"
                value="<?= $row['direccion'] ?>"
                required
            >

            <button
                type="submit"
                name="actualizar"
                class="btn-login"
            >
                Actualizar Cliente
            </button>

        </form>

    </div>

</div>

</body>
</html>
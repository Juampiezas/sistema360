<?php

session_start();

if(!isset($_SESSION['usuario'])){
    header("Location: login.php");
}

include("config/conexion.php");

$id = $_GET['id'];

$resultado = mysqli_query(
    $conn,
    "SELECT * FROM proveedores WHERE id='$id'"
);

$row = mysqli_fetch_assoc($resultado);

if(isset($_POST['actualizar'])){

    $nombre = $_POST['nombre'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];

    mysqli_query(
        $conn,
        "UPDATE proveedores SET

        nombre='$nombre',
        telefono='$telefono',
        empresa='$empresa'

        WHERE id='$id'"
    );

    header("Location: proveedores.php");

}

?>

<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">

<title>Editar proveedor</title>

<link rel="stylesheet" href="css/styles.css">

</head>
<body>

<?php include("includes/sidebar.php"); ?>

<div class="main">

    <h1>Editar proveedor</h1>

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
                name="empresa"
                class="form-control"
                value="<?= $row['empresa'] ?>"
                required
            >

            <button
                type="submit"
                name="actualizar"
                class="btn-login"
            >
                Actualizar proveedor
            </button>

        </form>

    </div>

</div>

</body>
</html>
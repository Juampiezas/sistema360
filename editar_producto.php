<?php

session_start();

if(!isset($_SESSION['usuario'])){
    header("Location: login.php");
}

include("config/conexion.php");

$id = $_GET['id'];

$sql = "SELECT * FROM productos
        WHERE id='$id'";

$resultado = mysqli_query($conn, $sql);

$row = mysqli_fetch_assoc($resultado);

if(isset($_POST['actualizar'])){

    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $categoria = $_POST['categoria'];

    $update = "UPDATE productos SET

                nombre='$nombre',
                precio='$precio',
                stock='$stock',
                categoria='$categoria'

                WHERE id='$id'";

    mysqli_query($conn, $update);

    header("Location: productos.php");

}

?>

<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">

<title>Editar Producto</title>

<link rel="stylesheet" href="css/styles.css">

</head>
<body>

<?php include("includes/sidebar.php"); ?>

<div class="main">

    <h1>Editar Producto</h1>

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
                type="number"
                step="0.01"
                name="precio"
                class="form-control"
                value="<?= $row['precio'] ?>"
                required
            >

            <input
                type="number"
                name="stock"
                class="form-control"
                value="<?= $row['stock'] ?>"
                required
            >

            <input
                type="text"
                name="categoria"
                class="form-control"
                value="<?= $row['categoria'] ?>"
                required
            >

            <button
                type="submit"
                name="actualizar"
                class="btn-login"
            >
                Actualizar Producto
            </button>

        </form>

    </div>

</div>

</body>
</html>
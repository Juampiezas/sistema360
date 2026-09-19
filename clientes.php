<?php

session_start();

if(!isset($_SESSION['usuario'])){
    header("Location: login.php");
}

include("config/conexion.php");

/* AGREGAR */

if(isset($_POST['guardar'])){

    $nombre = $_POST['nombre'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];

    $sql = "INSERT INTO clientes(
                nombre,
                telefono,
                direccion
            )
            VALUES(
                '$nombre',
                '$telefono',
                '$direccion'
            )";

    mysqli_query($conn, $sql);

}

/* ELIMINAR */

if(isset($_GET['eliminar'])){

    $id = $_GET['eliminar'];

    mysqli_query(
        $conn,
        "DELETE FROM clientes WHERE id='$id'"
    );

}

/* LISTAR */

$clientes = mysqli_query(
    $conn,
    "SELECT * FROM clientes"
);

?>

<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">

<title>Clientes</title>

<link rel="stylesheet" href="css/styles.css">

</head>
<body>

<?php include("includes/sidebar.php"); ?>

<div class="main">

    <h1>Clientes</h1>

    <div class="form-box">

        <form method="POST">

            <input
                type="text"
                name="nombre"
                class="form-control"
                placeholder="Nombre"
                required
            >

            <input
                type="text"
                name="telefono"
                class="form-control"
                placeholder="Teléfono"
                required
            >

            <input
                type="text"
                name="direccion"
                class="form-control"
                placeholder="Dirección"
                required
            >

            <button
                type="submit"
                name="guardar"
                class="btn-login"
            >
                Guardar Cliente
            </button>

        </form>

    </div>

    <br>
    <input
    type="text"
    id="buscador"
    class="form-control"
    placeholder="Buscar..."
>
    <table class="tabla">

        <tr>

            <th>ID</th>
            <th>Nombre</th>
            <th>Teléfono</th>
            <th>Dirección</th>
            <th>Acciones</th>

        </tr>

        <?php while($row = mysqli_fetch_assoc($clientes)){ ?>

        <tr>

            <td><?= $row['id'] ?></td>
            <td><?= $row['nombre'] ?></td>
            <td><?= $row['telefono'] ?></td>
            <td><?= $row['direccion'] ?></td>

            <td>

                <a
                    href="editar_cliente.php?id=<?= $row['id'] ?>"
                    class="btn-edit"
                >
                    Editar
                </a>

                <a
                    href="clientes.php?eliminar=<?= $row['id'] ?>"
                    class="btn-delete"
                >
                    Eliminar
                </a>

            </td>

        </tr>

        <?php } ?>

    </table>

</div>

</body>
</html>
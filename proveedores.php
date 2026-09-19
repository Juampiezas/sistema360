<?php

session_start();

if(!isset($_SESSION['usuario'])){
    header("Location: login.php");
}

include("config/conexion.php");

if(isset($_POST['guardar'])){

    $nombre = $_POST['nombre'];
    $empresa = $_POST['empresa'];
    $telefono = $_POST['telefono'];

    mysqli_query(
        $conn,
        "INSERT INTO proveedores(
            nombre,
            empresa,
            telefono
        )
        VALUES(
            '$nombre',
            '$empresa',
            '$telefono'
        )"
    );

}

if(isset($_GET['eliminar'])){

    $id = $_GET['eliminar'];

    mysqli_query(
        $conn,
        "DELETE FROM proveedores WHERE id='$id'"
    );

}

$proveedores = mysqli_query(
    $conn,
    "SELECT * FROM proveedores"
);

?>

<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">

<title>Proveedores</title>

<link rel="stylesheet" href="css/styles.css">

</head>
<body>

<?php include("includes/sidebar.php"); ?>

<div class="main">

    <h1>Proveedores</h1>

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
                name="empresa"
                class="form-control"
                placeholder="Empresa"
                required
            >

            <input
                type="text"
                name="telefono"
                class="form-control"
                placeholder="Teléfono"
                required
            >

            <button
                type="submit"
                name="guardar"
                class="btn-login"
            >
                Guardar Proveedor
            </button>

        </form>

    </div>

    <br>

    <table class="tabla">

        <tr>

            <th>ID</th>
            <th>Nombre</th>
            <th>Empresa</th>
            <th>Teléfono</th>
            <th>Acciones</th>

        </tr>

        <?php while($row = mysqli_fetch_assoc($proveedores)){ ?>

        <tr>

            <td><?= $row['id'] ?></td>
            <td><?= $row['nombre'] ?></td>
            <td><?= $row['empresa'] ?></td>
            <td><?= $row['telefono'] ?></td>

            <td>

                <a
                    href="editar_proveedor.php?id=<?= $row['id'] ?>"
                    class="btn-edit"
                >
                    Editar
                </a>

                <a
                    href="proveedores.php?eliminar=<?= $row['id'] ?>"
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
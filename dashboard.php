<?php

session_start();

if(!isset($_SESSION['usuario'])){
    header("Location: login.php");
}

include("config/conexion.php");

/* CONTADORES */

$productos = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total FROM productos"
    )
);

$clientes = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total FROM clientes"
    )
);

$ventas = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT SUM(total) AS total FROM ventas"
    )
);

$compras = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT SUM(costo) AS total FROM compras"
    )
);

/* STOCK BAJO */

$stock_bajo = mysqli_query(
    $conn,
    "SELECT * FROM productos
    WHERE stock <= 5"
);

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<title>Dashboard</title>

<link rel="stylesheet" href="css/styles.css">

</head>

<body>

<?php include("includes/sidebar.php"); ?>

<div class="main">

    <h1>Dashboard</h1>

    <div class="row">

        <div class="col">

            <div class="card-dashboard bg1">

                <h2>
                    <?= $productos['total'] ?>
                </h2>

                <p>Productos</p>

            </div>

        </div>

        <div class="col">

            <div class="card-dashboard bg2">

                <h2>
                    <?= $clientes['total'] ?>
                </h2>

                <p>Clientes</p>

            </div>

        </div>

        <div class="col">

            <div class="card-dashboard bg3">

                <h2>
                    $<?= number_format($ventas['total'] ?? 0,2) ?>
                </h2>

                <p>Ventas Totales</p>

            </div>

        </div>

        <div class="col">

            <div class="card-dashboard bg4">

                <h2>
                    $<?= number_format($compras['total'] ?? 0,2) ?>
                </h2>

                <p>Compras Totales</p>

            </div>

        </div>

    </div>

    <br><br>

    <h2>Productos con Stock Bajo</h2>

    <table class="tabla">

        <tr>

            <th>ID</th>
            <th>Producto</th>
            <th>Stock</th>

        </tr>

        <?php while($row = mysqli_fetch_assoc($stock_bajo)){ ?>

        <tr>

            <td><?= $row['id'] ?></td>

            <td><?= $row['nombre'] ?></td>

            <td>

                <span style="
                    color:red;
                    font-weight:bold;
                ">

                    <?= $row['stock'] ?>

                </span>

            </td>

        </tr>

        <?php } ?>

    </table>

</div>

</body>
</html>
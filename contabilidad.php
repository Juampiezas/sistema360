<?php

session_start();

if(!isset($_SESSION['usuario'])){
    header("Location: login.php");
}

include("config/conexion.php");

/* TOTALES */

/* INGRESOS */

$ventas_total = mysqli_fetch_assoc(

    mysqli_query(
        $conn,
        "SELECT SUM(total) AS total
        FROM ventas"
    )

);

/* EGRESOS */

$compras_total = mysqli_fetch_assoc(

    mysqli_query(
        $conn,
        "SELECT SUM(costo) AS total
        FROM compras"
    )

);

$total_ingresos = $ventas_total['total'] ?? 0;

$total_egresos = $compras_total['total'] ?? 0;

$ganancia = $total_ingresos - $total_egresos;

/* MOVIMIENTOS */

/* VENTAS */

$ventas = mysqli_query(

    $conn,

    "SELECT

    ventas.id,
    ventas.total,
    ventas.fecha,

    clientes.nombre AS cliente,
    productos.nombre AS producto

    FROM ventas

    INNER JOIN clientes
    ON ventas.cliente_id = clientes.id

    INNER JOIN productos
    ON ventas.producto_id = productos.id"

);

/* COMPRAS */

$compras = mysqli_query(

    $conn,

    "SELECT

    compras.id,
    compras.costo,
    compras.fecha,

    proveedores.nombre AS proveedor,
    productos.nombre AS producto

    FROM compras

    INNER JOIN proveedores
    ON compras.proveedor_id = proveedores.id

    INNER JOIN productos
    ON compras.producto_id = productos.id"

);

?>

<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">

<title>Contabilidad</title>

<link rel="stylesheet" href="css/styles.css">

</head>
<body>

<?php include("includes/sidebar.php"); ?>

<div class="main">

    <h1>Contabilidad General</h1>

    <div class="row">

        <div class="col">

            <div class="card-dashboard bg3">

                <h2>
                    $<?= number_format($total_ingresos,2) ?>
                </h2>

                <p>Ingresos</p>

            </div>

        </div>

        <div class="col">

            <div class="card-dashboard bg2">

                <h2>
                    $<?= number_format($total_egresos,2) ?>
                </h2>

                <p>Egresos</p>

            </div>

        </div>

        <div class="col">

            <div class="card-dashboard bg4">

                <h2>
                    $<?= number_format($ganancia,2) ?>
                </h2>

                <p>Ganancia Neta</p>

            </div>

        </div>

    </div>

    <br><br>

    <h2>Ingresos por Ventas</h2>

    <table class="tabla">

        <tr>

            <th>ID</th>
            <th>Cliente</th>
            <th>Producto</th>
            <th>Total</th>
            <th>Fecha</th>

        </tr>

        <?php while($v = mysqli_fetch_assoc($ventas)){ ?>

        <tr>

            <td><?= $v['id'] ?></td>

            <td><?= $v['cliente'] ?></td>

            <td><?= $v['producto'] ?></td>

            <td>$<?= $v['total'] ?></td>

            <td><?= $v['fecha'] ?></td>

        </tr>

        <?php } ?>

    </table>

    <br><br>

    <h2>Egresos por Compras</h2>

    <table class="tabla">

        <tr>

            <th>ID</th>
            <th>Proveedor</th>
            <th>Producto</th>
            <th>Total</th>
            <th>Fecha</th>

        </tr>

        <?php while($c = mysqli_fetch_assoc($compras)){ ?>

        <tr>

            <td><?= $c['id'] ?></td>

            <td><?= $c['proveedor'] ?></td>

            <td><?= $c['producto'] ?></td>

            <td>$<?= $c['costo'] ?></td>

            <td><?= $c['fecha'] ?></td>

        </tr>

        <?php } ?>

    </table>

</div>

</body>
</html>
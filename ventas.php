<?php

session_start();

if(!isset($_SESSION['usuario'])){
    header("Location: login.php");
}

include("config/conexion.php");

/* REGISTRAR VENTA */

if(isset($_POST['guardar'])){

    $cliente_id = $_POST['cliente_id'];
    $producto_id = $_POST['producto_id'];
    $cantidad = $_POST['cantidad'];

    /* OBTENER PRODUCTO */

    $producto = mysqli_query(
        $conn,
        "SELECT * FROM productos
        WHERE id='$producto_id'"
    );

    $rowProducto = mysqli_fetch_assoc($producto);

    $precio = $rowProducto['precio'];

    $stock_actual = $rowProducto['stock'];

    /* VALIDAR STOCK */

    if($cantidad <= $stock_actual){

        $total = $precio * $cantidad;

        /* GUARDAR VENTA */

        mysqli_query(
            $conn,
            "INSERT INTO ventas(
                cliente_id,
                producto_id,
                cantidad,
                total
            )
            VALUES(
                '$cliente_id',
                '$producto_id',
                '$cantidad',
                '$total'
            )"
        );

        /* ACTUALIZAR STOCK */

        $nuevo_stock = $stock_actual - $cantidad;

        mysqli_query(
            $conn,
            "UPDATE productos SET
            stock='$nuevo_stock'
            WHERE id='$producto_id'"
        );

    }

}

/* LISTAR CLIENTES */

$clientes = mysqli_query(
    $conn,
    "SELECT * FROM clientes"
);

/* LISTAR PRODUCTOS */

$productos = mysqli_query(
    $conn,
    "SELECT * FROM productos"
);

/* HISTORIAL */

$ventas = mysqli_query(
    $conn,
    "SELECT ventas.id,
            clientes.nombre AS cliente,
            productos.nombre AS producto,
            ventas.cantidad,
            ventas.total,
            ventas.fecha

    FROM ventas

    INNER JOIN clientes
    ON ventas.cliente_id = clientes.id

    INNER JOIN productos
    ON ventas.producto_id = productos.id

    ORDER BY ventas.id DESC"
);

?>

<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">

<title>Ventas</title>

<link rel="stylesheet" href="css/styles.css">

</head>
<body>

<?php include("includes/sidebar.php"); ?>

<div class="main">

    <h1>Ventas</h1>

    <div class="form-box">

        <form method="POST">

            <select
                name="cliente_id"
                class="form-control"
                required
            >

                <option value="">
                    Seleccionar Cliente
                </option>

                <?php while($cliente = mysqli_fetch_assoc($clientes)){ ?>

                    <option value="<?= $cliente['id'] ?>">

                        <?= $cliente['nombre'] ?>

                    </option>

                <?php } ?>

            </select>

            <select
                name="producto_id"
                class="form-control"
                required
            >

                <option value="">
                    Seleccionar Producto
                </option>

                <?php while($producto = mysqli_fetch_assoc($productos)){ ?>

                    <option value="<?= $producto['id'] ?>">

                        <?= $producto['nombre'] ?>

                    </option>

                <?php } ?>

            </select>

            <input
                type="number"
                name="cantidad"
                class="form-control"
                placeholder="Cantidad"
                required
            >

            <button
                type="submit"
                name="guardar"
                class="btn-login"
            >
                Registrar Venta
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
            <th>Cliente</th>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Total</th>
            <th>Fecha</th>
            <th>Factura</th>

        </tr>

        <?php while($venta = mysqli_fetch_assoc($ventas)){ ?>

        <tr>

            <td><?= $venta['id'] ?></td>

            <td><?= $venta['cliente'] ?></td>

            <td><?= $venta['producto'] ?></td>

            <td><?= $venta['cantidad'] ?></td>

            <td>$<?= $venta['total'] ?></td>

            <td><?= $venta['fecha'] ?></td>
            <td>

    <a
        href="factura.php?id=<?= $venta['id'] ?>"
        class="btn-edit"
    >
        Ver Factura
    </a>

</td>

        </tr>

        <?php } ?>

    </table>

</div>

</body>
</html>
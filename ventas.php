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

    /* VALIDAR CANTIDAD Y STOCK */

if($cantidad <= 0){

    $mensaje = "Introduce una cantidad válida.";
    $tipo_mensaje = "error";

}elseif($cantidad > $stock_actual){

    $mensaje = "Stock insuficiente. Solo hay $stock_actual unidades disponibles.";
    $tipo_mensaje = "error";

}else{

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

    $mensaje = "Venta registrada correctamente.";
    $tipo_mensaje = "exito";
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
<?php if(isset($mensaje)){ ?>

    <div style="
        padding: 12px;
        margin-bottom: 15px;
        border-radius: 6px;
        background:
            <?= $tipo_mensaje === 'exito'
                ? '#d4edda'
                : '#f8d7da' ?>;
        color:
            <?= $tipo_mensaje === 'exito'
                ? '#155724'
                : '#721c24' ?>;
    ">

        <?= htmlspecialchars($mensaje) ?>

    </div>

<?php } ?>
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
    id="producto"
    class="form-control"
    onchange="mostrarStock()"
    required
>

    <option value="">
        Seleccionar Producto
    </option>

    <?php while($producto = mysqli_fetch_assoc($productos)){ ?>

        <option
            value="<?= $producto['id'] ?>"
            data-stock="<?= $producto['stock'] ?>"
        >
            <?= htmlspecialchars($producto['nombre']) ?>
        </option>

    <?php } ?>

</select>

<input
    type="text"
    id="stock_disponible"
    class="form-control"
    placeholder="Stock disponible"
    readonly
>

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
<script>

function mostrarStock() {

    const select =
        document.getElementById("producto");

    const campoStock =
        document.getElementById("stock_disponible");

    const opcion =
        select.options[select.selectedIndex];

    if (select.value === "") {

        campoStock.value = "";
        return;

    }

    const stock =
        opcion.getAttribute("data-stock");

    campoStock.value =
        "Stock disponible: " + stock;
}

</script>
</body>
</html>
<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if(!isset($_SESSION['usuario'])){
    header("Location: login.php");
}

include("config/conexion.php");

/* REGISTRAR COMPRA */

if(isset($_POST['guardar'])){

    $proveedor = $_POST['proveedor'];

    $nombre_producto = $_POST['producto'];

    $categoria = $_POST['categoria'];

    $cantidad = $_POST['cantidad'];

    $costo_unitario = $_POST['costo_unitario'];

    $precio_venta = $_POST['precio_venta'];

    /* TOTAL */

    $total = $cantidad * $costo_unitario;

    /* VERIFICAR SI PRODUCTO EXISTE */

    $buscar = mysqli_query(
        $conn,
        "SELECT * FROM productos
        WHERE nombre='$nombre_producto'"
    );

    if(mysqli_num_rows($buscar) > 0){

        /* EXISTE */

        $producto = mysqli_fetch_assoc($buscar);

        $producto_id = $producto['id'];

        /* AUMENTAR STOCK */

        mysqli_query(
            $conn,
            "UPDATE productos
            SET stock = stock + '$cantidad'
            WHERE id='$producto_id'"
        );

    }else{

        /* CREAR PRODUCTO */

        mysqli_query(
            $conn,
            "INSERT INTO productos(
                nombre,
                categoria,
                precio,
                stock
            )
            VALUES(
                '$nombre_producto',
                '$categoria',
                '$precio_venta',
                '$cantidad'
            )"
        );

        $producto_id = mysqli_insert_id($conn);

    }

    /* GUARDAR COMPRA */

    mysqli_query(
        $conn,
        "INSERT INTO compras(
            proveedor_id,
            producto_id,
            cantidad,
            costo,
            costo_unitario
        )
        VALUES(
            '$proveedor',
            '$producto_id',
            '$cantidad',
            '$total',
            '$costo_unitario'
        )"
    );

}

/* PROVEEDORES */

$proveedores = mysqli_query(
    $conn,
    "SELECT * FROM proveedores"
);

/* HISTORIAL */

$compras = mysqli_query(
    $conn,
    "SELECT compras.*,

    proveedores.nombre AS proveedor,
    productos.nombre AS producto

    FROM compras

    INNER JOIN proveedores
    ON compras.proveedor_id = proveedores.id

    INNER JOIN productos
    ON compras.producto_id = productos.id

    ORDER BY compras.id DESC"
);

?>

<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">

<title>Compras</title>

<link rel="stylesheet" href="css/styles.css">

</head>
<body>

<?php include("includes/sidebar.php"); ?>

<div class="main">

    <h1>Compras a Proveedores</h1>

    <div class="form-box">

        <form method="POST">

            <select
                name="proveedor"
                class="form-control"
                required
            >

                <option value="">
                    Seleccionar proveedor
                </option>

                <?php while($p = mysqli_fetch_assoc($proveedores)){ ?>

                    <option value="<?= $p['id'] ?>">
                        <?= $p['nombre'] ?>
                    </option>

                <?php } ?>

            </select>

            <input
                type="text"
                name="producto"
                class="form-control"
                placeholder="Nombre del producto"
                required
            >

            <input
                type="text"
                name="categoria"
                class="form-control"
                placeholder="Categoría"
                required
            >

            <input
                type="number"
                name="cantidad"
                class="form-control"
                placeholder="Cantidad"
                required
            >

            <input
                type="number"
                step="0.01"
                name="costo_unitario"
                class="form-control"
                placeholder="Costo unitario"
                required
            >

            <input
                type="number"
                step="0.01"
                name="precio_venta"
                class="form-control"
                placeholder="Precio de venta"
                required
            >

            <button
                type="submit"
                name="guardar"
                class="btn-login"
            >
                Registrar Compra
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
            <th>Proveedor</th>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Costo Unitario</th>
            <th>Total</th>
            <th>Fecha</th>

        </tr>

        <?php while($row = mysqli_fetch_assoc($compras)){ ?>

        <tr>

            <td><?= $row['id'] ?></td>

            <td><?= $row['proveedor'] ?></td>

            <td><?= $row['producto'] ?></td>

            <td><?= $row['cantidad'] ?></td>

            <td>$<?= $row['costo_unitario'] ?></td>

            <td>$<?= $row['costo'] ?></td>

            <td><?= $row['fecha'] ?></td>

        </tr>

        <?php } ?>

    </table>

</div>

</body>
</html>
<?php

session_start();

if(!isset($_SESSION['usuario'])){
    header("Location: login.php");
}

include("config/conexion.php");



/* ELIMINAR PRODUCTO */

if(isset($_GET['eliminar'])){

    $id = intval($_GET['eliminar']);

    /* COMPROBAR SI EL PRODUCTO TIENE COMPRAS */

    $consultaCompras = mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total
         FROM compras
         WHERE producto_id='$id'"
    );

    $resultadoCompras =
        mysqli_fetch_assoc($consultaCompras);

    /* COMPROBAR SI EL PRODUCTO TIENE VENTAS */

    $consultaVentas = mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total
         FROM ventas
         WHERE producto_id='$id'"
    );

    $resultadoVentas =
        mysqli_fetch_assoc($consultaVentas);

    $tieneCompras =
        $resultadoCompras['total'] > 0;

    $tieneVentas =
        $resultadoVentas['total'] > 0;

    if($tieneCompras || $tieneVentas){

        $mensaje =
            "No se puede eliminar este producto porque tiene compras o ventas registradas.";

    }else{

        $delete = mysqli_query(
            $conn,
            "DELETE FROM productos
             WHERE id='$id'"
        );

        if($delete){

            $mensaje =
                "Producto eliminado correctamente.";

        }else{

            $mensaje =
                "No se pudo eliminar el producto.";

        }
    }
}

/* LISTAR PRODUCTOS */

/* BUSCADOR */

$busqueda = "";

if(isset($_GET['buscar'])){
    $busqueda = $_GET['buscar'];
}

/* CONSULTA */

$sql = "SELECT * FROM productos
        WHERE nombre LIKE '%$busqueda%'
        OR categoria LIKE '%$busqueda%'";

$productos = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">

<title>Productos</title>

<link rel="stylesheet" href="css/styles.css">

</head>
<body>

<?php include("includes/sidebar.php"); ?>

<div class="main">

    <h1>Productos</h1>
    <?php if(isset($_GET['agregado'])){ ?>

    <div class="alerta">
        Producto agregado correctamente.
    </div>

<?php } ?>
<a
    href="agregar_producto.php"
    class="btn-login"
>
    + Agregar Producto
</a>

<br>
<br>
    <?php if(isset($mensaje)){ ?>

<div class="alerta">

    <?= $mensaje ?>

</div>

<?php } ?>

    <!-- <div class="form-box">

        <form method="POST">

            <input
                type="text"
                name="nombre"
                class="form-control"
                placeholder="Nombre del producto"
                required
            >

            <input
                type="number"
                step="0.01"
                name="precio"
                class="form-control"
                placeholder="Precio"
                required
            >

            <input
                type="number"
                name="stock"
                class="form-control"
                placeholder="Stock"
                required
            >

            <input
                type="text"
                name="categoria"
                class="form-control"
                placeholder="Categoría"
                required
            >

            <button
                type="submit"
                name="guardar"
                class="btn-login"
            >
                Guardar Producto
            </button>

        </form>

    </div> -->

    <br>
<div class="search-box">

    <form method="GET">

        <input
            type="text"
            name="buscar"
            class="form-control"
            placeholder="Buscar producto o categoría..."
            value="<?= $busqueda ?>"
        >

        <button
            type="submit"
            class="btn-login"
        >
            Buscar
        </button>

    </form>

</div>

    <table class="tabla">

        <tr>

            <th>ID</th>
            <th>Nombre</th>
            <th>Precio</th>
            <th>Stock</th>
            <th>Categoría</th>
            <th>Acciones</th>

        </tr>

        <?php while($row = mysqli_fetch_assoc($productos)){ ?>

        <tr>

            <td><?= $row['id'] ?></td>

            <td><?= $row['nombre'] ?></td>

            <td>$<?= $row['precio'] ?></td>

           <td>

    <?php if($row['stock'] <= 0){ ?>

        <span class="stock-bajo">
            <?= $row['stock'] ?> — Sin stock
        </span>

    <?php }elseif($row['stock'] <= 5){ ?>

        <span class="stock-bajo">
            <?= $row['stock'] ?> — Stock bajo
        </span>

    <?php }else{ ?>

        <?= $row['stock'] ?> — Disponible

    <?php } ?>

</td>

            <td><?= $row['categoria'] ?></td>

            <td>

                <a
                    href="editar_producto.php?id=<?= $row['id'] ?>"
                    class="btn-edit"
                >
                    Editar
                </a>

               <a
    href="productos.php?eliminar=<?= $row['id'] ?>"
    class="btn-delete"
    onclick="return confirm('¿Seguro que deseas eliminar este producto?');"
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
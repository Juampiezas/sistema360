<?php

session_start();

if(!isset($_SESSION['usuario'])){
    header("Location: login.php");
    exit;
}

include("config/conexion.php");

$mensaje = "";

/* AGREGAR PRODUCTO */

if(isset($_POST['guardar'])){

    $nombre = trim($_POST['nombre']);
    $precio = floatval($_POST['precio']);
    $stock = intval($_POST['stock']);
    $categoria = trim($_POST['categoria']);

    /* VALIDACIONES */

    if($nombre == ""){

        $mensaje = "El nombre del producto es obligatorio.";

    }elseif($categoria == ""){

        $mensaje = "La categoría es obligatoria.";

    }elseif($precio < 0){

        $mensaje = "El precio no puede ser negativo.";

    }elseif($stock < 0){

        $mensaje = "El stock no puede ser negativo.";

    }else{

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO productos
            (nombre, precio, stock, categoria)
            VALUES (?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "sdis",
            $nombre,
            $precio,
            $stock,
            $categoria
        );

        if(mysqli_stmt_execute($stmt)){

            mysqli_stmt_close($stmt);

            header("Location: productos.php?agregado=1");
            exit;

        }else{

            $mensaje = "No se pudo agregar el producto.";

        }

        mysqli_stmt_close($stmt);
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>Agregar Producto</title>

    <link
        rel="stylesheet"
        href="css/styles.css"
    >

</head>

<body>

<?php include("includes/sidebar.php"); ?>

<div class="main">

    <h1>Agregar Producto</h1>

    <?php if($mensaje != ""){ ?>

        <div class="alerta">
            <?= htmlspecialchars($mensaje) ?>
        </div>

    <?php } ?>

    <div class="form-box">

        <form method="POST">

            <input
                type="text"
                name="nombre"
                class="form-control"
                placeholder="Nombre del producto"
                value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>"
                required
            >

            <input
                type="number"
                name="precio"
                class="form-control"
                placeholder="Precio"
                step="0.01"
                min="0"
                value="<?= htmlspecialchars($_POST['precio'] ?? '') ?>"
                required
            >

            <input
                type="number"
                name="stock"
                class="form-control"
                placeholder="Stock inicial"
                min="0"
                value="<?= htmlspecialchars($_POST['stock'] ?? '0') ?>"
                required
            >

            <input
                type="text"
                name="categoria"
                class="form-control"
                placeholder="Categoría"
                value="<?= htmlspecialchars($_POST['categoria'] ?? '') ?>"
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

        <br>

        <a
            href="productos.php"
            class="btn-edit"
        >
            Volver a Productos
        </a>

    </div>

</div>

</body>
</html>
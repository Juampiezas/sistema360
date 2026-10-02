<?php

session_start();

if(!isset($_SESSION['usuario'])){
    header("Location: login.php");
    exit;
}

include("config/conexion.php");

$id = $_GET['id'];

$sql = "SELECT * FROM productos
        WHERE id='$id'";

$resultado = mysqli_query($conn, $sql);

$row = mysqli_fetch_assoc($resultado);

if(isset($_POST['actualizar'])){

    $nombre = trim($_POST['nombre']);
    $precio = floatval($_POST['precio']);
    $stock = intval($_POST['stock']);
    $categoria = trim($_POST['categoria']);

    if($nombre == ""){

        $mensaje = "El nombre del producto es obligatorio.";

    }elseif($categoria == ""){

        $mensaje = "La categoría es obligatoria.";

    }elseif($precio < 0){

        $mensaje = "El precio no puede ser negativo.";

    }elseif($stock < 0){

        $mensaje = "El stock no puede ser negativo.";

    }else{

        $update = "UPDATE productos SET
                    nombre='$nombre',
                    precio='$precio',
                    stock='$stock',
                    categoria='$categoria'
                    WHERE id='$id'";

        mysqli_query($conn, $update);

        header("Location: productos.php");
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">

<title>Editar Producto</title>
<?php if(isset($mensaje)){ ?>

    <div class="alerta">
        <?= htmlspecialchars($mensaje) ?>
    </div>

<?php } ?>
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
                min="0"
                name="precio"
                placeholder="Precio"
                class="form-control"
                value="<?= $row['precio'] ?>"
                required
            >

            <input
                type="number"
                min="0"
                name="stock"
                class="form-control"
                placeholder="Stock"
                value="<?= $row['stock'] ?>"
                required
            >

            <input
                type="text"
                name="categoria"
                class="form-control"
                placeholder="Categoría"
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
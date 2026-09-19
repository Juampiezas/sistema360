<?php

session_start();

if(!isset($_SESSION['usuario'])){
    header("Location: login.php");
}

include("config/conexion.php");

/* AGREGAR PRODUCTO */

if(isset($_POST['guardar'])){

    $nombre = $_POST['nombre'];

    mysqli_query(
        $conn,
        "INSERT INTO productos(nombre)
        VALUES('$nombre')"
    );

    $mensaje =
    "Producto agregado correctamente";

}

/* ELIMINAR PRODUCTO */

if(isset($_GET['eliminar'])){

    $id = $_GET['eliminar'];

    $delete = "DELETE FROM productos
               WHERE id='$id'";

    mysqli_query($conn, $delete);

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

    <?php if(isset($mensaje)){ ?>

<div class="alerta">

    <?= $mensaje ?>

</div>

<?php } ?>

    <div class="form-box">

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

    </div>

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

    <?php if($row['stock'] <= 5){ ?>

        <span class="stock-bajo">
            <?= $row['stock'] ?>
        </span>

    <?php }else{ ?>

        <?= $row['stock'] ?>

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
                >
                    Eliminar
                </a>

            </td>

        </tr>

        <?php } ?>

    </table>

</div>
<script>

const buscador =
document.getElementById("buscador");

if(buscador){

    buscador.addEventListener(
        "keyup",
        function(){

            let filtro =
            buscador.value.toLowerCase();

            let filas =
            document.querySelectorAll(".tabla tr");

            filas.forEach((fila,index)=>{

                if(index === 0) return;

                let texto =
                fila.innerText.toLowerCase();

                fila.style.display =
                texto.includes(filtro)
                ? ""
                : "none";

            });

        }
    );

}

</script>

</body>
</html>
</body>
</html>
<?php

session_start();

if(!isset($_SESSION['usuario'])){
    header("Location: login.php");
}

include("config/conexion.php");

$id = $_GET['id'];

$sql = mysqli_query(
    $conn,
    "SELECT ventas.id,
            ventas.cantidad,
            ventas.total,
            ventas.fecha,

            clientes.nombre AS cliente,
            clientes.telefono,
            clientes.direccion,

            productos.nombre AS producto,
            productos.precio

    FROM ventas

    INNER JOIN clientes
    ON ventas.cliente_id = clientes.id

    INNER JOIN productos
    ON ventas.producto_id = productos.id

    WHERE ventas.id='$id'"
);

$factura = mysqli_fetch_assoc($sql);

?>

<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">

<title>Factura</title>

<link rel="stylesheet" href="css/styles.css">

<style>

.factura{
    background:white;
    padding:40px;
    border-radius:15px;
    box-shadow:0 5px 15px rgba(0,0,0,.1);
}

.factura h1{
    color:#0f172a;
}

.linea{
    margin-bottom:15px;
}

.total{
    font-size:25px;
    font-weight:bold;
    color:#10b981;
}

</style>

</head>
<body>

<?php include("includes/sidebar.php"); ?>

<div class="main">

    <div class="factura">

        <h1>
            Factura #<?= $factura['id'] ?>
        </h1>

        <hr>

        <div class="linea">
            <strong>Cliente:</strong>
            <?= $factura['cliente'] ?>
        </div>

        <div class="linea">
            <strong>Teléfono:</strong>
            <?= $factura['telefono'] ?>
        </div>

        <div class="linea">
            <strong>Dirección:</strong>
            <?= $factura['direccion'] ?>
        </div>

        <hr>

        <div class="linea">
            <strong>Producto:</strong>
            <?= $factura['producto'] ?>
        </div>

        <div class="linea">
            <strong>Precio:</strong>
            $<?= $factura['precio'] ?>
        </div>

        <div class="linea">
            <strong>Cantidad:</strong>
            <?= $factura['cantidad'] ?>
        </div>

        <div class="linea total">
            Total: $<?= $factura['total'] ?>
        </div>

        <hr>

        <div class="linea">
            <strong>Fecha:</strong>
            <?= $factura['fecha'] ?>
        </div>

        <br>

        <a
            href="pdf_factura.php?id=<?= $factura['id'] ?>"
            class="btn-login"
            style="text-decoration:none; display:inline-block;"
        >
            Descargar PDF
        </a>

    </div>

</div>

</body>
</html>
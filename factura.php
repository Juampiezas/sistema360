<?php
 
session_start();
 
if(!isset($_SESSION['usuario'])){
    header("Location: login.php");
    exit;
}
 
include("config/conexion.php");
 
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
 
/* DATOS DE LA VENTA */
 
$stmt = mysqli_prepare($conn,
    "SELECT ventas.id,
            ventas.total,
            ventas.fecha,
 
            clientes.nombre AS cliente,
            clientes.telefono,
            clientes.direccion
 
    FROM ventas
 
    INNER JOIN clientes
    ON ventas.cliente_id = clientes.id
 
    WHERE ventas.id = ?"
);
 
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
 
$factura = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
 
if(!$factura){
    die("Factura no encontrada.");
}
 
/* PRODUCTOS DE LA VENTA */
 
$stmt = mysqli_prepare($conn,
    "SELECT productos.nombre AS producto,
            productos.precio,
            detalle_ventas.cantidad,
            detalle_ventas.subtotal
 
    FROM detalle_ventas
 
    INNER JOIN productos
    ON detalle_ventas.producto_id = productos.id
 
    WHERE detalle_ventas.venta_id = ?"
);
 
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
 
$detalles = mysqli_stmt_get_result($stmt);
 
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
            <?= htmlspecialchars($factura['cliente']) ?>
        </div>
 
        <div class="linea">
            <strong>Teléfono:</strong>
            <?= htmlspecialchars($factura['telefono'] ?? '') ?>
        </div>
 
        <div class="linea">
            <strong>Dirección:</strong>
            <?= htmlspecialchars($factura['direccion'] ?? '') ?>
        </div>
 
        <hr>
 
        <?php while($d = mysqli_fetch_assoc($detalles)){ ?>
 
        <div class="linea">
            <strong>Producto:</strong>
            <?= htmlspecialchars($d['producto']) ?>
        </div>
 
        <div class="linea">
            <strong>Precio:</strong>
            $<?= number_format($d['precio'], 2) ?>
        </div>
 
        <div class="linea">
            <strong>Cantidad:</strong>
            <?= $d['cantidad'] ?>
        </div>
 
        <div class="linea">
            <strong>Subtotal:</strong>
            $<?= number_format($d['subtotal'], 2) ?>
        </div>
 
        <hr>
 
        <?php } ?>
 
        <div class="linea total">
            Total: $<?= number_format($factura['total'], 2) ?>
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
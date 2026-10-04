<?php
 
session_start();
 
if(!isset($_SESSION['usuario'])){
    header("Location: login.php");
    exit;
}
 
error_reporting(E_ALL);
ini_set('display_errors', 1);
 
require_once 'dompdf/vendor/autoload.php';
 
use Dompdf\Dompdf;
 
include("config/conexion.php");
 
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
 
/* DATOS DE LA VENTA */
 
$stmt = mysqli_prepare($conn,
    "SELECT ventas.id,
            ventas.total,
            ventas.fecha,
 
            clientes.nombre AS cliente
 
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
 
$html = '
 
<h1>Sistema 360 - Factura #'.$factura['id'].'</h1>
 
<hr>
 
<h3>Cliente: '.htmlspecialchars($factura['cliente']).'</h3>
 
<hr>
';
 
while($d = mysqli_fetch_assoc($detalles)){
 
    $html .= '
 
<h3>Producto: '.htmlspecialchars($d['producto']).'</h3>
 
<h3>Precio: $'.number_format($d['precio'], 2).'</h3>
 
<h3>Cantidad: '.$d['cantidad'].'</h3>
 
<h3>Subtotal: $'.number_format($d['subtotal'], 2).'</h3>
 
<hr>
';
}
 
$html .= '
 
<h2>Total: $'.number_format($factura['total'], 2).'</h2>
 
<p>Fecha: '.$factura['fecha'].'</p>
 
';
 
$dompdf = new Dompdf();
 
$dompdf->loadHtml($html);
 
$dompdf->setPaper('A4', 'portrait');
 
$dompdf->render();
 
$dompdf->stream("factura_".$factura['id'].".pdf");
 
?>